<?php
class FrontController extends FrontControllerCore

{

public function initContent()

{

$this->process();

if (!isset($this->context->cart))

$this->context->cart = new Cart();

$this->context->smarty->assign(array(

'HOOK_HEADER_CATEGORY' => Hook::exec('DisplayHeaderCategory'),
));

}

}

?>
