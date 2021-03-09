<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class wsmeshseo extends Module
{
    public function __construct()
    {
        $this->name = 'ns_monmodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'New Slang';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Module New Slang');
        $this->description = $this->l('Mon premier module super cool');

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');

        if (!Configuration::get('NS_MONMODULE_PAGENAME')) {
            $this->warning = $this->l('Aucun nom fourni');
        }
    }
}