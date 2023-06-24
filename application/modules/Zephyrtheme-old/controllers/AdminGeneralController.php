<?php

class Zephyrtheme_AdminGeneralController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->form = $form = new Zephyrtheme_Form_Admin_General();

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
    ->getNavigation('zephyr_admin_main', array(), 'zephyr_admin_main_general');

    $settings = Engine_Api::_()->getApi('settings', 'core');

    // Populate form
    $form->populate($settings->getFlatSetting('zephyr', array()));

    if (!$this->getRequest()->isPost()) return false;
    if (!$form->isValid($this->getRequest()->getPost())) return false;

    // Process form
    $values = $form->getValues();
    $settings->zephyr = $values;
	$v_constants = array(
	'content_style' => $values['content_style'],
	'responsive_layout' => $values['responsive_layout'],
	'newsfeed_style' => $values['newsfeed_style'] );
	
    // Save as constants
    $zephyr_api = Engine_Api::_()->getApi('theme', 'zephyrtheme');
    $zephyr_api->setOptions($v_constants);

    $form->addNotice('Your changes have been saved.');
  }
}