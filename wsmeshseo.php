<?php

require_once _PS_MODULE_DIR_.'wsmeshseo/classes/Config.php';

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
            && $this->registerHook('header')
            &&  $this->registerHook('leftColumn')
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
                `id_category` INT UNSIGNED NOT NULL,
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
    public function hookDisplayLeftColumn($params)
    {
        $this->context->smarty->assign([
            'test' => Config::get('WSMESHSEO_PAGENAME')
        ]);

        return $this->display(__FILE__, 'wsmeshseo.tpl');
    }

    /***
     * hook header on page wsmeshseo from the controller
     * @return mixed
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->registerStylesheet(
            'wsmeshseo',
            $this->_path.'views/css/wsmeshseo.css',
            ['server' => 'remote', 'position' => 'head', 'priority' => 150]
        );
    }


    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {

            $values = array();

            $values['WSMESHSEO_PAGENAME'] = array('value' => strval(Tools::getValue('WSMESHSEO_PAGENAME')),
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
        return $output.$this->displayForm();
    }

    public function displayForm()
    {

        // Initialise les champs du formulaire dans un tableau
        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Test d\'enregistrement d\'une valeur en config'),
                        'name' => 'WSMESHSEO_PAGENAME',
                        'size' => 20,
                        'required' => true
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Enregistrer'),
                    'name'  => 'btnSubmit'
                )
            ),
        );

        $helper = new HelperForm();

        // Module, token et currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&amp;configure='.$this->name;


        // Charge la valeur de WSMESHSEO_PAGENAME depuis la base
        $helper->fields_value['WSMESHSEO_PAGENAME'] = Config::get('WSMESHSEO_PAGENAME');

        return $helper->generateForm(array($form));
    }


}