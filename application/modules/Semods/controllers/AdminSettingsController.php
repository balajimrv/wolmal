<?php

class Semods_AdminSettingsController extends Core_Controller_Action_Admin
{
  
  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('semods_admin_main', array(), 'semods_admin_main_settings');

    $this->view->form = $form = new Semods_Form_Admin_Settings();
    
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $form->saveAdminSettings();
    }
    
  }

}