<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreurl
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Addurl.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreurl_Form_Admin_Settings_Addurl extends Engine_Form
{

  public function init()
  {
		$this
		->setMethod('post')
		->setAttrib('class', 'global_form_box')
		;
    $modules = array();
    $moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
		$select = $moduleTable->select()->where('enabled = ?', 1);
		$enablemodules = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
    foreach ($enablemodules as $module) {
			$modules[$module] = $module.' ';
    }
    $this->addElement('Select', 'module_name', array(
              'label' => 'Module name',
              'description' => 'Select the module for which the standard URLs should be banned from being assigned to Stores, and which has not been selected previously over here.',
              'multiOptions' => $modules,
              'value' => ($modules),
          ));
    // Element: execute
    $this->addElement('Button', 'submit', array(
        'label' => 'Save',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

   $this->addElement('Cancel', 'cancel', array(
				'label' => 'cancel',
				'link' => true,
				'prependText' => ' or ',
				'href' => '',
				'onClick'=> 'javascript:parent.Smoothbox.close();',
				'decorators' => array(
					'ViewHelper'
				)
			));
	}
 
}