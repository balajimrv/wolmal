<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Form_Create extends Engine_Form {

  public function init() {

    //GET STORE ID
    $store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', null);

    //GET TAB ID
    $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

    //GET VIEW
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    //GET URL
    $url = $view->item('sitestore_store', $store_id)->getHref(array('tab' => $tab_id));

    $this->setTitle('Add a New Coupon')
            ->setAttrib('id', 'submit_form')
            ->setDescription("Enter your coupon's details below and click 'Add' to create your coupon.");
    $this->addElement('text', 'title', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Coupon Title'),
//        'required' => true,
    ));

    $this->addElement('textarea', 'description', array(
        'label' => 'Description',
        'description' => 'To make this coupon more relevant for users and to mention the terms and conditions of its use, enter its description below.',
//        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Text', 'url', array(
        'label' => 'Coupon URL', 'style' => 'width:200px;',
        'description' => Zend_Registry::get('Zend_Translate')->_('Please enter your coupon URL here.'),
        'filters' => array(
            array('PregReplace', array('/\s*[a-zA-Z0-9]{2,5}:\/\//', '')),
        )
    ));

    $this->addElement('Text', 'coupon_code', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Coupon Code'),
//        'description' => Zend_Registry::get('Zend_Translate')->_('Please enter coupon code for your coupon here.'),
        //'required' => true,
        'allowedEmpty' => false
    ));
    
    $this->addElement('Text', 'product_name', array(
        'label' => Zend_Registry::get('Zend_Translate')->_("Select Products"),
        'description' => Zend_Registry::get('Zend_Translate')->_("Enter the name of products in the auto-suggest box below."),
        'autocomplete' => 'off'));

    $this->addElement('Hidden', 'product_ids', array(
        'label' => '',
        'order' => 5,
        'filters' => array(
            'HtmlEntities'
        ),
    ));
    Engine_Form::addDefaultDecorators($this->product_ids);

    $this->addElement('Select', 'discount_type', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Discount Type'),
        'multiOptions' => array(
            1 => Zend_Registry::get('Zend_Translate')->_('Fixed'),
            0 => Zend_Registry::get('Zend_Translate')->_('Percentage')
        ),
        'value' => 0,
    ));

    $this->addElement('Text', 'rate', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Discount Rate (%)'),
        //'maxlength' => 6,
       // 'required' => true,
        'allowEmpty' => false,
        'value' => '0',
//        'validators' => array(
//            array('Float', true),
//            array('Between', false, array('min' => '0', 'max' => '100', 'inclusive' => true)),
//        ),
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
        //'required' => true,
//        'value' => '0',
//        'validators' => array(
//            array('NotEmpty', true),
//            array('StringLength', true, array(1, 13)),
//            array('Float', true),
//            array('Regex', true, array('pattern' => '/^(?:\d+|\d*\.\d+)$/', 'messages' => array('regexNotMatch' => 'Please enter a valid positive number.')))
//        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Text', 'minimum_purchase', array(
                'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Minimum Purchase Amount (%s)'), $currencyName),
        'description' => 'This coupon will only be applicable if total amount of order will be equal to or more than the entered amount. Leave this empty or set to zero to apply coupon on any amount.',
        //'value' => 0,
//        'validators' => array(
//            array('Float', true),
//            array('Regex', true, array('pattern' => '/^(?:\d+|\d*\.\d+)$/', 'messages' => array('regexNotMatch' => 'Please enter a valid positive number.')))
//        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->minimum_purchase->getDecorator('Description')->setOptions(array('placement' => 'PREPEND'));
    
    $this->addElement('Text', 'min_product_quantity', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Minimum Product Quantity'),
        'description' => 'Minimum number of tickets that should be added to order, to avail this coupon. Leave this empty or set to zero to apply coupon on any numbers of tickets.',
//        'validators' => array(
//            array('Regex', true, array('pattern' => '/(?:^|[^\.])(\d+)(?:\s+|$)/', 'messages' => array('regexNotMatch' => 'Please enter a valid positive number.')))
//        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->min_product_quantity->getDecorator('Description')->setOptions(array('placement' => 'PREPEND'));


    $this->addElement('File', 'photo', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Coupon Picture'),
        'description' => "<span id='loading_image' style='display:none;'></span> ",
        'onchange' => 'imageupload()',
    ));
    $this->photo->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    $this->photo->addValidator('Extension', false, 'jpg,png,gif');

    $date = (string) date('Y-m-d');
    $this->addElement('CalendarDateTime', 'start_time', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Start Date'),
        'description' => Zend_Registry::get('Zend_Translate')->_('When will this coupon start?'),
        'value' => $date . ' 00:00:00',
    ));
    $this->addElement('Radio', 'end_settings', array(
        'id' => 'end_settings',
        'label' => Zend_Registry::get('Zend_Translate')->_('End Date'),
        'description' => Zend_Registry::get('Zend_Translate')->_('When will this coupon end?'),
        'onclick' => "updateTextFields(this.value)",
        'multiOptions' => array(
            "0" => Zend_Registry::get('Zend_Translate')->_("Never. This coupon does not have an end date."),
            "1" => Zend_Registry::get('Zend_Translate')->_("This coupon ends on a specific date. (Select the date by clicking on the calendar icon below.)"),
        ),
        'value' => 0
    ));
    $date = (string) date('Y-m-d');
    $this->addElement('CalendarDateTime', 'end_time', array(
        'value' => $date . ' 00:00:00',
    ));
    
    $this->addElement('text', 'claim_count', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Uses Per Coupon'),
        'description' => Zend_Registry::get('Zend_Translate')->_('Enter the maximum number of times this coupon can be used by members. (Leave empty or Enter 0 for unlimited. Note: You will not be able to edit this filed once this coupon expires.)'),
        //'required' => true,
    ));

    $this->addElement('text', 'claim_user_count', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Uses Per Buyer'),
        'description' => Zend_Registry::get('Zend_Translate')->_('Enter the maximum number of times the coupon can be used by a single user. (Leave empty or Enter 0 for unlimited. Note: You will not be able to edit this field once this coupon expires.)'),
        //'required' => true,
    ));
    
    $this->addElement('Checkbox', 'status', array(
        'label' => Zend_Registry::get('Zend_Translate')->_("Enable this coupon"),
        'value' => 1
    ));
    
    if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0))
      $this->addElement('Checkbox', 'public', array(
          'label' => Zend_Registry::get('Zend_Translate')->_('Make this coupon public so that others can see it on my store.'),
      ));

    $this->addElement('Button', 'execute', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Preview'),
        'onclick' => "PageUrlBlur(1)",
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('cancel'),
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

    $this->addElement('Hidden', 'photo_id_filepath', array(
        'value' => 0,
        'order' => 854
    ));

    $this->addElement('Hidden', 'imageName', array(
        'order' => 992
    ));

    $this->addElement('Hidden', 'imageenable', array(
        'value' => 0,
        'order' => 991
    ));
  }

}

?>