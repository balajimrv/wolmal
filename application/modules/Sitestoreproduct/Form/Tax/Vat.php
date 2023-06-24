<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Vat.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Tax_Vat extends Engine_Form {

  public function init() {
    $this->setAttrib('id', 'store_vat');

    $this->addElement('Text', 'title', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Title'),
//        'value' => 'VAT',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', false, array(1, 255)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    
    $this->addElement('Select', 'handling_type', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('VAT Type'),
        'multiOptions' => array("1" => "Percent", "0" => "Fixed"),
        'onchange' => 'showPriceType();'
    ));

    $localeObject = Zend_Registry::get('Locale');
    $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
    $this->addElement('Text', 'tax_price', array(
        'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Price (%s)'), $currencyName),
        'allowEmpty' => false,
        'value' => '0',
        'validators' => array(
            array('StringLength', true, array(0, 13)),
            array('Float', true),
            array('GreaterThan', false, array('min' => '0')),
            array('Regex', true, array('pattern' => '/^(?:\d+|\d*\.\d+)$/', 'messages' => array('regexNotMatch' => 'Please enter a valid positive number.')))
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Text', 'tax_rate', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Rate (%)'),
        'maxlength' => 6,
        'allowEmpty' => false,
        'value' => '0',
        'validators' => array(
            array('Float', true),
            array('GreaterThan', false, array('min' => '0')),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.checkbox.vat.inclusive', 1)){
      $this->addElement('Checkbox', 'save_price_with_vat', array(
          'label' => Zend_Registry::get('Zend_Translate')->_("Do you want to enter product prices as their Basic Price (excluding VAT) on the product create and edit pages?"),
          'value' => 0
      ));
    }
    
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.checkbox.net.prices', 1)){
      $this->addElement('Checkbox', 'show_price_with_vat', array(
          'label' => Zend_Registry::get('Zend_Translate')->_("Do you want Basic Price (excluding VAT) to be displayed for your products at various places?"),
          'value' => 0
      ));
    }
    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Save'),
        'type' => 'submit',
        'ignore' => true,
        'class' => 'fleft',
        'decorators' => array(
            'ViewHelper',
        ),
    ));
    
    $this->addElement('Dummy', 'vat_loading_image', array(
        'decorators' => array(
            array('ViewScript', array(
                    'viewScript' => '_vatLoadingImage.tpl',
                    'class' => 'form element'
                ),
            )
        ),
    ));
  }
}