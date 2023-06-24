<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EditRate.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Tax_EditRate extends Engine_Form {

  public function init() {

    parent::init();

    $this->setTitle('Edit Tax Location');
    
    $this->addElement('Select', 'handling_type', array(
        'label' => 'Tax Type',
        'multiOptions' => array("0" => "Fixed", "1" => "Percent"),
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
        'label' => 'Rate (%)',
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

    $this->addElement('Checkbox', 'status', array(
        'label' => "Enable tax in this location",
        'value' => 1,
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
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
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
        ),
    ));
  }
}