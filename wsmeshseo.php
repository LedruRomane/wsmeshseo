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
                `id_category` varchar(25) NOT NULL,
                `configuration` varchar(25) DEFAULT NULL,
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
        $data = array();
        $category_grandparent = array();
        $category_grandchildren = array();

        $category=  new Category(Tools::getValue('id_category'));

        $category_children = $category->getChildren(Tools::getValue('id_category'),$this->context->language->id);

        $category_parent = $category->getParentsCategories();
        //Supprimer la catégorie "Accueil" des catégories parentes et la catégorie courante.
        //Trier la catégorie parente directe et les catégories grands-parentes
        foreach($category_parent as $key => $value){
            if($value['id_category'] == 2 || $value['id_category'] == $category->id ){
                unset($category_parent[$key]);
            }
            elseif($value['id_category'] == $category->id_parent){
                $category_grandparent[] = $category_parent[$key];
                unset($category_parent[$key]);
            }
        }

        //Récupération des petits-enfants
        if ($subCategories = $category->getSubCategories($this->context->language->id))
        {
            foreach ($subCategories as $key => $subcat) {
                $subcatObj = new Category($subcat['id_category']);
                $category_grandchildren =array_merge($category_grandchildren , $subcatObj->getSubCategories($this->context->language->id));
            }
        };

        $oncle =array();
        $cousin=array();
        $neveux=array();


        //Récupération des configurations cochées
        $configChecked = explode(',', Config::get('options'));

        //Récupérations des catégories en fonction des configs cochées
        foreach($configChecked as $value)
        {
            switch ($value)
            {
                case '1':
                    $data = array_merge($data,$category_grandparent);
                    break;
                case '2':
                    $data = array_merge($data,$category_parent);
                    break;
                case '3':
                    $data = array_merge($data,$category_children);
                    break;
                case '4':
                    $data = array_merge($data,$category_grandchildren);
                    break;
                case '5':
                    $data += $oncle;
                    break;
                case '6':
                    $data += $cousin;
                    break;
                case '7':
                    $data += $neveux;
                    break;
            }
        }
        $this->context->smarty->assign([
            'data' => $data
        ]);
        return $this->display(__FILE__, 'wsmeshseo.tpl');
    }

    protected function getConfigFormValues()
    {

        $config_fields = array (

            'checkbox' => Config::get('checkbox')

        );

        $opts = $this->getOptions();

        $id_checkbox_options = array();

        foreach ($opts as $options)
        {
            $id_checkbox_options[] = $options['id_checkbox_options'];
        }

        $id_checkbox_options_post = array();

        foreach ($id_checkbox_options as $opt_id)
        {
            if (Tools::getValue('options_'.(int)$opt_id))
            {
                $id_checkbox_options_post['options_'.(int)$opt_id] = true;
            }
        }

        $id_checkbox_options_config = array();

        if ($confs = Config::get('options'))
        {
            $confs = explode(',', Config::get('options'));
        }
        else{
            $confs = array();
        }

        foreach ($confs as $conf)
        {
            $id_checkbox_options_config['options_'.(int)$conf] = true;
        }

        if (Tools::isSubmit('btnSubmit'))
        {
            $config_fields = array_merge($config_fields, array_intersect($id_checkbox_options_post, $id_checkbox_options_config));
        }
        else{
            $config_fields = array_merge($config_fields, $id_checkbox_options_config);
        }


        return $config_fields;

    }


    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submitConfig')) {


            $all_opts = $this->getOptions();
            $checkbox_options = array();
            foreach ($all_opts as $chbx_options)
            {
                if (Tools::getValue('options_'.(int)$chbx_options['id_checkbox_options']))
                {
                    $checkbox_options[] = $chbx_options['id_checkbox_options'];
                }
            }

            Config::updateValue('options', implode(',', $checkbox_options));

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
        return $output.$this->displayForm($this->getConfigFormValues());
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
                    'type' => 'checkbox',
                    'label' => $this->l('Selectionnez l\'arborescence de la catégorie courante voulue : '),
                    'desc' => $this->l('Faites vos choix.'),
                    'name' => 'options',
                    'values' => array(
                        'query' => $this->getOptions(),
                        'id' => 'id_checkbox_options',
                        'name' => 'checkbox_options_name',
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('save'),
                'name'  => 'btnSubmit'
            ),
        );


        $helper = new HelperForm();

        // Module, token et currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name;

        $helper->submit_action = 'submitConfig';

        $existedValues = $this->getConfigFormValues();
        foreach($existedValues as $key => $value)
        {
            $helper->fields_value[$key] = true;
        }
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

    public function  getOptions(){
        $options =array(
            array(
                'id_checkbox_options' => '1',
                'checkbox_options_name' => 'Parents',
            ),
            array(
                'id_checkbox_options' => '2',
                'checkbox_options_name' => 'Grand-parents',
            ),
            array(
                'id_checkbox_options' => '3',
                'checkbox_options_name' => 'Enfants',
            ),
            array(
                'id_checkbox_options' => '4',
                'checkbox_options_name' => 'Petits-enfants',
            ),
            array(
                'id_checkbox_options' => '5',
                'checkbox_options_name' => 'Oncles',
            ),
            array(
                'id_checkbox_options' => '6',
                'checkbox_options_name' => 'Cousins',
            ),
            array(
                'id_checkbox_options' => '7',
                'checkbox_options_name' => 'Neveux',
            )
        );
        return $options;
    }
}