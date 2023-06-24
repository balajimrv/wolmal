<?php
class Zephyrtheme_Form_Admin_Footer extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Footer Settings')
      ->setDescription('Settings that affect only the Footer.');
	  
    // Add submit button
    $this->addElement('Button', 'submit_1', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));

    $this->addElement('Radio', 'footer_logged', array(
      'label' => 'Footer Layout',
      'description' => 'Footer Layout for Logged-In users',
      'multiOptions' => array(
        0 => '<img src="./application/modules/Zephyrtheme/externals/images/footers/footer_1.png" alt="Footer Simple"/>',
        1 => '<img src="./application/modules/Zephyrtheme/externals/images/footers/footer_2.png" alt="Footer Complex"/>',
      ),
      'value' => 0,
	  'escape' => false,
    ));
	
    $this->addElement('Radio', 'footer_notlogged', array(
      'label' => '',
      'description' => 'Footer Layout for NOT Logged-In users',
      'multiOptions' => array(
        0 => '<img src="./application/modules/Zephyrtheme/externals/images/footers/footer_1.png" alt="Footer Simple"/>',
        1 => '<img src="./application/modules/Zephyrtheme/externals/images/footers/footer_2.png" alt="Footer Complex"/>',
      ),
      'value' => 1,
	  'escape' => false,
    ));

	$this->addElement('Radio', 'footer_style', array(
      'label' => 'Footer Style',
      'description' => '',
      'multiOptions' => array(
        '1' => '<span style="font-weight: bold;">Wide</span> - Footer\'s content takes the full width of the browser',
        '2' => '<span style="font-weight: bold;">Compact</span> - Footer\'s content takes the width of the content',
		'3' => '<span style="font-weight: bold;">Boxed</span> - the entire Footer takes the content\'s width',
      ),
      'value' => '2',
	  'escape' => false,
    ));

    $this->addElement('Select', 'footer_logo', array(
      'label' => 'Footer Logo',
      'description' => '<span style="font-weight: bold;">Logo images are uploaded via <a href="'.Zend_Controller_Front::getInstance()->getBaseUrl().'/admin/files" target="_blank"><span style="font-weight: bold;">File & Media Manager</span></a></span>.<br />After uploading a logo image refresh this page to see it in the select box below.',
    ));
	$this->getElement('footer_logo')->getDecorator('Description')->setOption('escape',false);
	
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}