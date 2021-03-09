<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class wsmeshseo extends Module
{
    public function __construct()
    {
        $this->name = 'wsmeshseo';
        $this->tab = 'front_office_features';
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

        if (!Configuration::get('WSMESHSEO_PAGENAME')) {
            $this->warning = $this->l('Aucun nom fourni');
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive())
        {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->registerHook('leftColumn') ||
            !$this->registerHook('header') ||
            !Configuration::updateValue('WSMESHSEO_PAGENAME', 'Mentions légales')
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('WSMESHSEO_PAGENAME')
        ) {
            return false;
        }

        return true;
    }

    public function hookDisplayLeftColumn($params)
    {
        $this->context->smarty->assign([
            'ns_page_name' => Configuration::get('WSMESHSEO_PAGENAME'),
            'ns_page_link' => $this->context->link->getModuleLink('wsmeshseo', 'display')
        ]);

        return $this->display(__FILE__, 'wsmeshseo.tpl');
    }

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

        if (Tools::isSubmit('btnSubmit')) {
            $pageName = strval(Tools::getValue('WSMESHSEO_PAGENAME'));

            if (
                !$pageName||
                empty($pageName)
            ) {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            } else {
                Configuration::updateValue('WSMESHSEO_PAGENAME', $pageName);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        // Récupère la langue par défaut
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Initialise les champs du formulaire dans un tableau
        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Configuration value'),
                        'name' => 'WSMESHSEO_PAGENAME',
                        'size' => 20,
                        'required' => true
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
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

        // Langue
        $helper->default_form_language = $defaultLang;

        // Charge la valeur de WSMESHSEO_PAGENAME depuis la base
        $helper->fields_value['WSMESHSEO_PAGENAME'] = Configuration::get('WSMESHSEO_PAGENAME');

        return $helper->generateForm(array($form));
    }
}