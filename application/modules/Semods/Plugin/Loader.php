<?php
class Semods_Plugin_Loader extends Zend_Controller_Plugin_Abstract
{

  public function preDispatch(Zend_Controller_Request_Abstract $request) {
    
    Semods_Loader::hook();
    
  }

}
?>
