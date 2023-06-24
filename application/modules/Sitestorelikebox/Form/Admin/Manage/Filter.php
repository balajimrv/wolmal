<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Filter.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorelikebox_Form_Admin_Manage_Filter extends Engine_Form {

  public function init() {

    $this->setAttribs(array(
			'id' => 'filter_form',
			'class' => 'global_form_box',
		));

		//ADD FOR BORDER COLOR.
    $this->addElement('Text', 'sitestorelikebox_color', array(
			'decorators' => array(array('ViewScript', array(
				'viewScript' => '_formColorCode.tpl',
				'class' => 'form element'
			)))
    ));

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}
?>