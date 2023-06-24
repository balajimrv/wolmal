<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreform_Form_Admin_Global extends Engine_Form {

  public function init() {

    $this
            ->setTitle('General Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Radio', 'sitestoreform_formtabseeting', array(
        'label' => 'Non-logged-in Visitors',
        'description' => 'Do you want the Form tab in Stores to be available to non-logged-in visitors? (If yes, then non-logged-in visitors will be able to see the Form tab on the Store and fill the form created by the Store Admin.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform.formtabseeting', 1),
    ));

    $this->addElement('Radio', 'sitestoreform_captcha', array(
        'label' => 'CAPTCHA',
        'description' => 'Do you want CAPTCHA in forms of Form App in Stores for non-logged-in visitors?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform.captcha', 1),
    ));
	
	  $this->addElement('Radio', 'sitestoreform_add_question', array(
				'label' => 'Allow Adding Custom Fields / Questions',
				'description' => 'Do you want to allow Store owners to add custom fields / questions for their Store Forms?',
				'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
				),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform.add.question', 1),
		));
	
	  $this->addElement('Radio', 'sitestoreform_edit_name', array(
				'label' => 'Allow Editing Form Tab’s Name',
				'description' => 'Do you want to allow Store owners to edit the name of their Store’s Form tab?',
				'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
				),
				'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform.edit.name', 1),
		));
	
	 $this->addElement('Text', 'sitestoreform_manifestUrl', array(
        'label' => 'Stores Form URL alternate text for "store-form"',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Please enter the text below which you want to display in place of "storeform" in the URLs of this plugin.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform.manifestUrl', "store-form"),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
?>