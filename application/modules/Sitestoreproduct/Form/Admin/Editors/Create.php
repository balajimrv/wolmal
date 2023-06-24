<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Editors_Create extends Engine_Form {

  public function init() {

    $this->setMethod('post');
    $this->setTitle("Add New Editor")
            ->setDescription('Below, you can use the auto-suggest box to add a member as editor who will be allowed to write editor reviews for products on your site.')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Hidden', 'user_id', array());
    $this->addElement('Text', 'title', array(
        'label' => 'User name',
        'description' => 'Start typing the name of the member.',
        'allowEmpty' => false,
        'required' => true,
    ));

    $this->addElement('Textarea', 'details', array(
        'label' => 'About Editor',
        'description' => "Enter description about the editor. (Note: This description will be displayed in 'Product Profile: About Editor' and 'Editor / Member Profile: About Editor' widgets. Editors will also be able to write about themselves in the 'Editor / Member Profile: About Editor' widget.)",
    ));

    $this->addElement('Text', 'designation', array(
        'label' => 'Designation',
        'description' => "Enter the designation of the editor. (Note: This designation will be displayed in 'Editor / Member Profile: About Editor' widget.)",
        'maxlength' => 64,
    ));
    
    $this->addElement('Checkbox', 'email_notify', array(
        'description' => 'Email Notification',
        'label' => 'Send email notification when a new product is created.',
        'value' => 1,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Add Member',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));
  }

}