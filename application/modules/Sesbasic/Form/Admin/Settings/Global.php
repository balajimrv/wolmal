<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesbasic
 * @package    Sesbasic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Global.php 2015-07-25 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesbasic_Form_Admin_Settings_Global extends Engine_Form {

  public function init() {

    $this->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

    $coreSetting = Engine_Api::_()->getApi('settings', 'core');
    $this->addElement('Text', "ses_mapApiKey", array(
        'label' => 'Google Map API Key',
        'description' => 'Enter the Google map API key for displaying Google map on your website.<a href="https://console.developers.google.com/project" target="_blank">Click Here</a> to generate the API key.',
        'allowEmpty' => true,
        'required' => false,
        'value' => $coreSetting->getSetting('ses.mapApiKey', ''),
    ));
    $this->ses_mapApiKey->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

    $this->addElement('Text', "ses_addthis", array(
        'label' => 'Add This Publisher Id',
        'description' => 'Enter the Add This Publisher Id for displaying Add This Widget on your website.<a href="https://www.addthis.com/dashboard" target="_blank">Click Here</a> to generate the Publisher Id.',
        'allowEmpty' => true,
        'required' => false,
        'value' => $coreSetting->getSetting('ses.addthis', ''),
    ));
    $this->ses_addthis->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
		
		//check ses video and album
		$sesalbum_enable_module = Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('sesalbum'));
		$sesvideo_enable_module = Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('sesvideo'));
		if($sesalbum_enable_module || $sesvideo_enable_module){
			$this->addElement('Select', "ses_allow_adult_filtering", array(
					'label' => 'Allow Adult Filtering',
					'description' => 'Do you want member on your website mark content adult in "Advanced Photos & Albums Plugin" & "Advanced Videos & Channels Plugin"?',
					'allowEmpty' => true,
					'required' => false,
					'multiOptions'=> array('1'=>'Yes, allow adult filtering','0'=>'No, do not allow adult filtering'),
					'value' => $coreSetting->getSetting('ses.allow.adult.filtering', '1'),
			));
			$this->ses_allow_adult_filtering->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
		}
		
    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
