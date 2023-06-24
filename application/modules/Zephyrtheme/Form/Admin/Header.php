<?php

class Zephyrtheme_Form_Admin_Header extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Header Settings')
      ->setDescription('Settings that affect only the Header.');
	  
    // Add submit button
    $this->addElement('Button', 'submit_1', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));

    $this->addElement('Radio', 'header_layout', array(
      'label' => 'Header Layout',
      'description' => '',
      'multiOptions' => array(
	  	'6' => '<img src="./application/modules/Zephyrtheme/externals/images/headers/header_6.png" alt="Header Six"/>',
        '1' => '<img src="./application/modules/Zephyrtheme/externals/images/headers/header_1.png" alt="Header One"/>',
        '2' => '<img src="./application/modules/Zephyrtheme/externals/images/headers/header_2.png" alt="Header Two"/>',
		'3' => '<img src="./application/modules/Zephyrtheme/externals/images/headers/header_3.png" alt="Header Three"/>',
		'4' => '<img src="./application/modules/Zephyrtheme/externals/images/headers/header_4.png" alt="Header Four"/>',
		'5' => '<img src="./application/modules/Zephyrtheme/externals/images/headers/header_5.png" alt="Header Five"/>',
		
      ),
      'value' => '1',
	  'escape' => false
    ));

	$this->addElement('Radio', 'header_style', array(
      'label' => 'Header Style',
      'description' => '',
      'multiOptions' => array(
        '1' => '<span style="font-weight: bold;">Wide</span> - Header\'s content takes the full width of the browser',
        '2' => '<span style="font-weight: bold;">Compact</span> - Header\'s content takes the width of the content',
		'3' => '<span style="font-weight: bold;">Boxed</span> - the entire Header takes the content\'s width',
      ),
      'value' => '2',
	  'escape' => false
    ));

    $this->addElement('Select', 'header_logo', array(
      'label' => 'Header Logo',
      'description' => '<span style="font-weight: bold;">Logo images are uploaded via <a href="' .
          Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/files" target="_blank">
          <span style="font-weight: bold;">File & Media Manager</span></a></span>.<br />
          After uploading a background image refresh this page to see it in the select box below.',
    ));
	$this->getElement('header_logo')->getDecorator('Description')->setOption('escape',false);

    $this->addElement('Select', 'header_fixed', array(
      'label' => 'Fixed Header',
      'description' => 'Do you want the Header to be fixed on top when you scroll down?',
      'multiOptions' => array(
        0 => 'No',
        1 => 'Yes',
      ),
      'value' => 1
    ));
	
	$this->addElement('Textarea', 'header_banner', array(
      'label' => 'Banner for 4th & 5th Header Layout',
      'description' => 'Add banner code(*can be a Google AD). When this is empty the banner will not be displayed.
                        Recommended banner size: 468 x 60px.'
    ));

	$this->addElement('Select', 'header_mainmenu', array(
      'label' => 'Show Main-Menu for non-loggedin users?',
      'description' => '',
      'multiOptions' => array(
        0 => 'No',
        1 => 'Yes'
      ),
      'value' => 1
    ));
		
    $this->addElement('Text', 'mainmenu_dropdown', array(
      'label' => 'Main-Menu Dropdown',
      'description' => 'Main-Menu items before dropdown menu occurs.',
	  'value' => '5'
    ));
	
	$this->addElement('Dummy', 'minimenushow_dummy', array(
		'label' => 'Mini-Menu Items',
		'description' => 'Check which of the following items to show on the mini-menu.',
		'ignore' => true,
	));
	$this->addElement('Checkbox', 'minimenushow_profileimg', array(
	  'label' => 'Profile Image-Link', 'description' => '', 'value' => true ));
	$this->addElement('Checkbox', 'minimenushow_logout', array(
	  'label' => 'Logout Link - Switch Icon', 'description' => '', 'value' => true ));

	$this->addElement('Radio', 'header_loginbutton', array(
      'label' => 'Login Button',
      'description' => 'What the "Login" button should do when clicked?',
      'multiOptions' => array(
        0 => 'Open Login Box on the same page',
        1 => 'Redirect to Login Page',
      ),
      'value' => 0
    ));
	
	$this->addElement('Radio', 'header_signupbutton', array(
      'label' => 'Signup Button',
      'description' => 'What the "Signup" button should do when clicked?',
      'multiOptions' => array(
        0 => 'Open Signup Box on the same page',
        1 => 'Redirect to Signup Page',
      ),
      'value' => 1
    ));

	$this->addElement('Dummy', 'header_iconstitle', array(
        'content' => '<h2 style="margin-top: 15pt">Main-Menu Icons</h2>',
        'decorators' => array('ViewHelper'),
        'ignore' => true
    ));
	
	$this->addElement('Select', 'header_iconssize', array(
      'label' => 'Main-Menu Icons Size',
      'description' => '',
      'multiOptions' => array(
        '16' => '16px X 16px',
        '24' => '24px X 24px',
		'32' => '32px X 32px',
      ),
      'value' => '16'
    ));

    $this->addElement('Dummy', 'header_iconsdesc', array(
        'content' => '<p class="form-description" style="font-weight: bold;">Use only icons with the exact size specified above, otherwise the icons will be forced to resize to that size.</p><p class="form-description">You can generate icons with <a href="https://icomoon.io/app/#/select" target="_blank"><span style="font-weight: bold;">Icomoon App</span></a></p><p><span style="font-weight: bold;">Icon images are uploaded via <a href="' . Zend_Controller_Front::getInstance()->getBaseUrl() . '/admin/files" target="_blank"><span style="font-weight: bold;">File & Media Manager</span></a></span>.<br />After uploading a image refresh this page to see it in the select boxes below.</p>',
        'decorators' => array('ViewHelper'),
		'order' => 12,
        'ignore' => true
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'order' => 1000 // this has to be last!
    ));
  }
}