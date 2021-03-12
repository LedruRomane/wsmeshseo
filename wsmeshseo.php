<?php

require_once _PS_MODULE_DIR_.'wsmeshseo/classes/Config.php';
require_once _PS_MODULE_DIR_.'wsmeshseo/services/CdcTools.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class wsmeshseo extends Module
{
    /***
     * wsmeshseo constructor.
     */
    public function __construct()
    {
        $this->name = 'wsmeshseo';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Wess-Soft';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Module Mesh SEO');
        $this->description = $this->l('Systeme de maillage de catégorie sur prestashop');

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function init()
    {

        parent::init();

    }

    /***
     * install module
     * @return bool
     */
    public function install()
    {
        if (Shop::isFeatureActive())
        {
            Shop::setContext(Shop::CONTEXT_ALL);
        }


        return parent::install()
            && $this->registerHook('DisplayHeaderCategory')
            && $this->installDB() ;
    }

    /***
     * uninstall module
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallDB();
    }

    /***
     * install DataBase
     * @return bool
     */
    public function installDB()
    {
        $return = true;

        $return &= Db::getInstance()->execute('
                CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ws_seo_configuration` (
                `id_category` varchar(11) NOT NULL,
                `configuration` varchar(11) DEFAULT NULL,
                PRIMARY KEY (`id_category`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );

        return $return;
    }

    /***
     * Uninstall DataBase
     * @param bool $drop_table
     * @return bool
     */
    public function uninstallDB($drop_table = true)
    {
        $ret = true;
        if ($drop_table) {
            $ret &= Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ws_seo_configuration`');
        }

        return $ret;
    }

    /***
     * hook left column on product pages
     * @param $params
     * @return mixed
     */
    public function hookDisplayHeaderCategory($params)
    {
        $this->context->smarty->assign([
            'test' => Config::get('config')
        ]);

        return $this->display(__FILE__, 'wsmeshseo.tpl');
    }


    public function getContent()
    {
        $output = null;
        if (Tools::isSubmit('submitConfig')) {
            $values = array();

            $values['config'] = array('value' => strval(Tools::getValue('config')),
                'label' => $this->l("Test d'enregistrement d'une valeur en config"));

            foreach ($values as $key => $value) {
                if (!$value['value']
                    || empty($value['value'])
                    || !Validate::isGenericName($value['value']))
                    $output .= $this->displayError($this->l('Valeur config invalide') . ' ' . $value['label']);
                else {
                    Config::updateValue($key, $value['value']);
                }
            }

            if($output == null)
            {
                $output .= $this->displayConfirmation($this->l('Configurations mises à jour.'));
            }
        }

        if(Config::get('HOOK') == null) {
            $hook =$this->installCustomHooks();
            if ($hook) {
                $output .= $this->displayConfirmation($this->l('Hooks are correctly installed!'));
                $hookDb = Config::updateHook('displayHeaderCategory', 'Top of page category', 'This hook is placed at the top of product list on page category');
                $hookCat = Config::updateValue('HOOK', 'hookInstalled');
                if(!$hookDb || !$hookCat){
                    $output .= $this->displayError($this->l('Hooks are not correctly installed :-('));
                }
            } else {
                $output .= $this->displayError($this->l('Hooks are not correctly installed :-('));
                $this->smarty->assign(array(
                    'troubleshooting' => true
                ));
            }
        }

        return $output.$this->displayForm();
    }

    public function displayForm()
    {

        // Initialise les champs du formulaire dans un tableau
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Test d\'enregistrement d\'une valeur en config'),
                    'name' => 'config',
                    'size' => 20,
                    'required' => true
                ),
            ),
            'submit' => array(
                'title' => $this->l('save'),
                'name'  => 'btnSubmit'
            )
        );


        $helper = new HelperForm();

        // Module, token et currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name;

        $helper->submit_action = 'submitConfig';


        $helper->fields_value['config'] = Config::get('config');

        return $helper->generateForm($fields_form);
    }

    public function installCustomHooks() {
        $success = true;
        if(version_compare(_PS_VERSION_, '1.7', '>=')) {
            $filename = _PS_THEME_DIR_.'templates/catalog/listing/product-list.tpl';
            if(!CdcTools::stringInFile('{hook h="displayHeaderCategory"}', $filename)) {
                $file_content = Tools::file_get_contents($filename);
                $strg = "(<section id=\"main\">)";
                if(!empty($file_content)) {
                    $matches = preg_split($strg, $file_content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                    if(count($matches) == 2) {
                        $new_content = $matches[0] ."<section id=\"main\"> \n {hook h=\"displayHeaderCategory\"}" .$matches[1];
                        if(!file_put_contents($filename, $new_content)) {
                            $success = false;
                        }
                    } else {
                        $success = false;
                    }
                } else {
                    $success = false;
                }
            }
        }
        return $success;
    }


}