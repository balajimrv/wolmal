<?php

class Semods_AdminHelpController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('semods_admin_main', array(), 'semods_admin_main_help');

  }

  
}