<?php

class wsmeshseodisplayModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('module:wsmeshseo/views/templates/front/display.tpl');
    }
}
