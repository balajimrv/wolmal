<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Form_Edit extends Engine_Form {

  public function init() {

    //GET STORE ID
    $offer_store = Zend_Controller_Front::getInstance()->getRequest()->getParam('offer_store', null);

    //GET STORE ID
    $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);

    //GET TAB ID
    $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

    //GET VIEW
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    //GET URL
    $url = $view->item('sitestore_store', $store_id)->getHref(array('tab' => $tab_id));
    $this->setTitle('Edit Coupon')
            ->setDescription("Edit your coupon's details below, then click 'Save Changes' to publish it on your Page.");

    $this->addElement('text', 'title', array(
        'label' => 'Coupon Title',
        'required' => true,
    ));

    $this->addElement('textarea', 'description', array(
        'label' => 'Description',
        'description' => 'To make this coupon more relevant for users and to mention its terms and conditions, enter its description. Terms and conditions can contain information such as coupon code used by staff at the store etc.',
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Text', 'url', array(
        'label' => 'Coupon URL', 'style' => 'width:200px;',
        'description' => 'Please enter your coupon URL here.',
        'filters' => array(
            array('PregReplace', array('/\s*[a-zA-Z0-9]{2,5}:\/\//', '')),
        )
    ));

    $this->addElement('Select', 'discount_type', array(
        'label' => 'Discount Type',
        'multiOptions' => array(
            1 => 'Fixed',
            0 => 'Percentage'
        ),
        'value' => 0,
    ));

    $this->addElement('Text', 'rate', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Discount Rate (%)'),
        'maxlength' => 6,
        'allowEmpty' => false,
        //'value' => '0',
        'validators' => array(
            array('Float', true),
            array('Between', false, array('min' => '0', 'max' => '100', 'inclusive' => true)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));

    $localeObject = Zend_Registry::get('Locale');
    $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
    $this->addElement('Text', 'price', array(
        'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Discount (%s)'), $currencyName),
        'allowEmpty' => false,
       // 'value' => '0',
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', true, array(1, 13)),
            array('Float', true),
            array('Regex', true, array('pattern' => '/^(?:\d+|\d*\.\d+)$/', 'messages' => array('regexNotMatch' => 'Please enter a valid positive number.')))
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Text', 'minimum_purchase', array(
         'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Minimum Purchase Amount (%s)'), $currencyName),
        'description' => 'The total amount that must reached before the coupon is valid. (Enter 0 or Leave Empty in case you want coupon to be applied on all amounts.)',
        //'value' => 0,
        'validators' => array(
            array('Float', true),
            array('Regex', true, array('pattern' => '/^(?:\d+|\d*\.\d+)$/', 'messages' => array('regexNotMatch' => 'Please enter a valid positive number.')))
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->minimum_purchase->getDecorator('Description')->setOptions(array('placement' => 'PREPEND'));
    
    $this->addElement('Text', 'min_product_quantity', array(
        'label' => 'Minimum Product Quantity',
        'description' => 'Enter the minimum quantity of the products that must be available in the cart for coupon to be applied. (Enter 0 or Leave Empty in case you want coupon to be applied on any quantity of products.)',
        'validators' => array(
            array('Regex', true, array('pattern' => '/(?:^|[^\.])(\d+)(?:\s+|$)/', 'messages' => array('regexNotMatch' => 'Please enter a valid positive number.')))
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->min_product_quantity->getDecorator('Description')->setOptions(array('placement' => 'PREPEND'));

    $this->addElement('Text', 'product_name', array(
        'label' => "Select Products",
        'description' => "Enter the name of products in the auto-suggest box below.",
        'autocomplete' => 'off'));

    $this->addElement('Hidden', 'product_ids', array(
        'label' => '',
        'order' => 9,
        'filters' => array(
            'HtmlEntities'
        ),
    ));
    Engine_Form::addDefaultDecorators($this->product_ids);

//    $this->addElement('File', 'photo', array(
//        'label' => 'Main Photo'
//    ));

    $date = (string) date('Y-m-d');

    $this->addElement('CalendarDateTime', 'start_time', array(
        'label' => 'Start Date',
        'description' => 'When will this coupon start?',
        'value' => $date . ' 00:00:00',
    ));
    $this->addElement('Radio', 'end_settings', array(
        'id' => 'end_settings',
        'label' => 'End Date',
        'description' => 'When will this coupon end?',
        'onclick' => "updateTextFields(this.value)",
        'multiOptions' => array(
            "0" => "Never. This coupon does not have an end date.",
            "1" => "End coupon on a specific date.",
        ),
        'value' => 0
    ));

    $this->addElement('CalendarDateTime', 'end_time', array(
        'description' => 'Select a date by clicking on the calendar icon below.',
        'value' => $date . ' 00:00:00',
    ));

    $this->addElement('text', 'claim_count', array(
        'label' => 'Uses Per Coupon',
        'description' => 'Enter the maximum number of times this coupon can be used by members. (Leave empty or Enter 0 for unlimited. Note: You will not be able to edit this field once this coupon expires.)',
        //'required' => true,
    ));

    $this->addElement('text', 'claim_user_count', array(
        'label' => 'Uses Per Buyer',
        'description' => 'Enter the maximum number of times the coupon can be used by a single user. (Leave empty or Enter 0 for unlimited. Note: You will not be able to edit this field once this coupon expires.)',
        //'required' => true,
    ));

    $this->addElement('Checkbox', 'status', array(
        'label' => "Enable this Coupon",
    ));
    
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0))
      $this->addElement('Checkbox', 'public', array(
          'label' => 'Want to make it as Public',
      ));

    if (empty($offer_store)) {
      $this->addElement('Button', 'execute', array(
          'label' => 'Save Changes',
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
          'href' => $url,
          'decorators' => array(
              'ViewHelper',
          ),
      ));

      $this->addDisplayGroup(array(
          'execute',
          'cancel',
              ), 'buttons', array(
          'decorators' => array(
              'FormElements',
              'DivDivDivWrapper'
          ),
      ));
    } else {
      $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => '_formButtonCancel.tpl',
                      'class' => 'form element')))
      ));
      $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
          'decorators' => array(
              'FormElements',
              'DivDivDivWrapper',
          ),
      ));
    }
  }

}

?>