<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddMethod.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Shipping_AddMethod extends Engine_Form {

  public function init() {



    $request = Zend_Controller_Front::getInstance()->getRequest();
    $store_id = $request->getParam('store_id', null);

    $this->setTitle('Create Shipping Method');
    $this->setName('create_shipping_methods');
    $this->setDescription("Below, you can create a shipping method to fulfill delivery to your buyers of the products that they purchase from your store.");

    $tempCountryArray['ALL'] = "All Countries";
    $shippingCountries = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getRegionsByName($params = array());

    foreach ($shippingCountries as $keys => $shippingCountry) {
      $localeCountryArray[$shippingCountry['country']] = Zend_Locale::getTranslation($shippingCountry['country'], 'country');
    }

    asort($localeCountryArray);
    
    $countryArray = array_merge($tempCountryArray, $localeCountryArray);
    
    $this->addElement('Text', 'title', array(
        'label' => 'Title',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', false, array(1, 128)),
        ),
         'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        )
    ));
    
    $this->addElement('Text', 'delivery_time', array(
        'label' => 'Delivery Time',
        'description' => 'Eg: 2-5 Days, 3-9 Working Days, 2-6 Business Days',
        'maxlength' => 30,
        'allowEmpty' => false,
        'validators' => array(
            array('NotEmpty', true),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->delivery_time->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    $this->addElement('Select', 'country', array(
        'label' => 'Country',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
        ),
        'multiOptions' => $countryArray,
        'value' => key($countryArray),
        'onchange' => 'showRegions(' . $store_id . ', 0, null, 0, null);'
    ));
      
    $this->addElement('Radio', 'all_regions', array(
        'label' => 'Enable shipping method for all Regions / States',
        'multiOptions' => array("yes" => "Yes", "no" => "No"),
        'value' => 'no',
        'onchange' => "return showAllRegions()"
    ));

    $this->addElement('Multiselect', 'state', array(
        'RegisterInArrayValidator' => false,
        'allowEmpty' => true,
        'required' => false,
        'label' => 'Regions / States',
        'description' => '',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formSetTableRateRegion.tpl',
                    'class' => 'form element'))),
    ));
    
    $temp_shipping_fee_type_array = array("0" => "Fixed", "1" => "Percentage");
if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore_shipping_extra_content', 1)){
    $this->addElement('Select', 'dependency', array(
        'label' => 'Method Dependency',
        'description' => "Shipping methods can depend on Cost, Quantity and Weight. Buyers will be shown those methods for which their order's properties will be collectively matched. This shipping method will be available on checkout process for an order from your store if:<br /><strong>Cost:</strong> If the order cost of your products will be between the configured range.<br /><strong>Weight:</strong> If the order weight of your products will be between the configured range.<br /><strong>Quantity:</strong> If the quantity of your products in the order will be between the configured range.",
        'multiOptions' => array("0" => "Cost & Weight", "1" => "Weight only", "2" => "Quantity & Weight"),
        'onchange' => 'showDependency();'
    ));
    $this->dependency->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));

    $this->addElement('Text', 'ship_limit', array(
        'label' => '',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formShipRange.tpl',
                    'class' => 'form element'))),

        'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
    ));
    
    $weightUnit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.weight.unit', 'lbs');
    $this->addElement('Text', 'weight_limit', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Total Order Weight (In %s)', $weightUnit),
        'description' => 'If the total order weight will be between the configured range, then this shipping method will be available on checkout process.',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formWeightLimit.tpl',
                    'class' => 'form element'))),
    ));
    $temp_shipping_fee_type_array = array("0" => "Fixed", "1" => "Percentage", "2" => "Per Unit Weight");
}
    $this->addElement('Select', 'ship_type', array(
        'label' => 'Shipping Type',
        'multiOptions' => array("0" => "Per Order", "1" => "Per Item"),
        'onchange' => 'showShipType();'
    ));

    $this->addElement('Select', 'handling_type', array(
        'label' => 'Shipping Fee Type',
        'description' => 'You can have a fixed shipping fee for this method, or a percentage of order total, or a fee that is calculated based on a fee per unit weight.',
        'RegisterInArrayValidator' => false,
        'multiOptions' => $temp_shipping_fee_type_array,
        'onchange' => 'showHandlingType();'
    ));

    $localeObject = Zend_Registry::get('Locale');
    $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
    $this->addElement('Text', 'price', array(
        'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Shipping Fee (%s)'), $currencyName),
        'allowEmpty' => false,
        'value' => '0',
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

    $this->addElement('Text', 'rate', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Rate (%)'),
        'maxlength' => 6,
        'allowEmpty' => false,
        'value' => '0',
        'validators' => array(
            array('Float', true),
            array('Between', false, array('min' => '0', 'max' => '100', 'inclusive' => true)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Checkbox', 'status', array(
        'label' => "Activate this shipping method for your store. (If enabled, then this method will be available to users for selection in the Shipping Methods section during the checkout process of an order from your store.)",
        'value' => 1,
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Settings',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formSetDivAddress.tpl',
                    'class' => 'form element'))),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
        ),
    ));
  }
}
