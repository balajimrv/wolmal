<?php

class Zephyrtheme_AdminTypographyController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->form = $form = new Zephyrtheme_Form_Admin_Typography();

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('zephyr_admin_main', array(), 'zephyr_admin_main_typography');
	  
	$settings = Engine_Api::_()->getApi('settings', 'core');  

	// Populate form
    $form->populate($settings->getFlatSetting('zephyr', array()));

    if(!$this->getRequest()->isPost()) return false;
    if(!$form->isValid($this->getRequest()->getPost())) return false;

    // Process form
    $values = $form->getValues();
    $settings->zephyr = $values;
	
	if(!empty($values['google_font'])) { $google_fontfix = '"'.trim(ucwords(strtolower($values['google_font']))).'"'; }
	else { $google_fontfix = $values['main_font']; }
	
	if(!empty($values['google_headingsfont'])) { $google_headingsfontfix = '"'.trim(ucwords(strtolower($values['google_headingsfont']))).'"'; }
	else { $google_headingsfontfix = $values['headings_font']; }
	
	$v_constants = array( 
		'google_fontfix' => $google_fontfix,
		'google_headingsfontfix' => $google_headingsfontfix,
		);
	
    // Save as constants
    $zephyr_api = Engine_Api::_()->getApi('theme', 'zephyrtheme');
	
	$zephyr_api->setOptions($values);
    $zephyr_api->setOptions($v_constants);
	//$zephyr_api->removeOption('zephyr_');

    $form->addNotice('Your changes have been saved.');
  }
}