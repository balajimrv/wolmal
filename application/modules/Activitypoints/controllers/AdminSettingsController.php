<?php

class Activitypoints_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_admin_main', array(), 'activitypoints_admin_main_settings');
    
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->view->form = $form = new Activitypoints_Form_Admin_Settings();
    
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $form->saveAdminSettings();
    }
  }


}