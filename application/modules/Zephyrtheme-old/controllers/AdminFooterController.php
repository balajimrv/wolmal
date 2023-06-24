<?php

class Zephyrtheme_AdminFooterController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->form = $form = new Zephyrtheme_Form_Admin_Footer();

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
    ->getNavigation('zephyr_admin_main', array(), 'zephyr_admin_main_footer');

    $settings = Engine_Api::_()->getApi('settings', 'core');

    // Populate form
    $form->populate($settings->getFlatSetting('zephyr', array()));

    if (!$this->getRequest()->isPost()) return false;
    if (!$form->isValid($this->getRequest()->getPost())) return false;

    // Process form
    $values = $form->getValues();
    $settings->zephyr = $values;
	$v_constants = array( 'footer_style' => $values['footer_style'] );
	
    // Save as constants
    $zephyr_api = Engine_Api::_()->getApi('theme', 'zephyrtheme');
    $zephyr_api->setOptions($v_constants);

    $form->addNotice('Your changes have been saved.');
  }
}