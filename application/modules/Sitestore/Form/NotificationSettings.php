<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contactinfo.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_NotificationSettings extends Engine_Form {

  protected $_show_sitestoreproduct_form_element;
  
  public function setShow_sitestoreproduct_form_element($value) {
    $this->_show_sitestoreproduct_form_element = $value;
    return $this;
  }
  
  public function init() {

//       $this->setTitle('Contact Details')
//               ->setDescription('Contact information will be displayed in the Info section of your store profile.')
//               ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
//               ->setAttrib('name', 'contactinfo');

		$this->addElement('Checkbox', 'email', array(
				'description' => 'Email Notifications',
				'label' => "Send email notifications to me when people post an update, or create various content on this Store.",
				'value' => 1,
		));
		
	  $this->addElement('Checkbox', 'notification', array(
				'description' => 'Site Notifications',
				'label' => "Send notification updates to me when people perform various actions on this store (Below you can individually activate notifications for the actions).",
				'onclick' => 'showNotificationAction()',
				'value' => 0,
		));

		$this->addElement( 'MultiCheckbox' , 'action_notification' , array (
			//'label' => 'Select the options that you want to be recive notification when any member post, comment, like, follow and create content.',
			'multiOptions' => array("posted" => "People post updates on this store", "created" => "People create various contents on this store (photos, reviews, etc.)", "comment" => "People post comments on this store", "like" => "People like this store", "follow" => "People follow this store"),
			'value' => array("posted", "created", "comment", "like", "follow")
		)) ;
    
    if(!empty($this->_show_sitestoreproduct_form_element) )
    {
      $this->addElement('Text', 'to', array(
            'label' => 'Order Notifications to Store Admins',
            'description' => 'Notifications for orders placed in your store (order creation, order status changing, etc.) are sent to all store admins by default. If you do not want selected store admins to receive these notifications, then start typing their names below and select them.',
            'autocomplete' => 'off',
'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
            ));
      Engine_Form::addDefaultDecorators($this->to);
      $this->to->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

      $this->addElement('Hidden', 'toValues', array(
              'order' => 4,
              'filters' => array(
                      'HtmlEntities'
              ),
      ));
      Engine_Form::addDefaultDecorators($this->toValues);
    }
		
		$this->addElement('Button', 'submit', array(
				'label' => 'Save Changes',
				'type' => 'submit',
				'ignore' => true,
		));
    
  }
}