<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Addresses.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Addresses extends Engine_Form {
  
  protected $_viewerId;
  protected $_showShipping;
  
    public function getViewerId() {
    return $this->_viewerId;
  }

  public function setViewerId($viewerId) {
    $this->_viewerId = $viewerId;
    return $this;
  }
  
  public function getShowShipping() {
    return $this->_showShipping;
  }

  public function setShowShipping($showShipping) {
    $this->_showShipping = $showShipping;
    return $this;
  }
  
  public function init() {
    
    $this->setName('store_address');

    $this->addElement('Dummy', 'dummy_billing_address_title', array('label' => 'Billing Address'));
    
    $description = '<span id="f_name_billing_error" class="seaocore_txt_red"></span>';
    $this->addElement('Text', 'f_name_billing', array(
        'label' => 'First Name',
        'description' => $description,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', true, array(1, 128)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->f_name_billing->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    $description = '<span id="l_name_billing_error" class="seaocore_txt_red"></span>';
    $this->addElement('Text', 'l_name_billing', array(
        'label' => 'Last Name',
        'description' => $description,
        'allowEmpty' => true,
        'required' => false,
        'validators' => array(
            array('StringLength', true, array(0, 128)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->l_name_billing->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    $viewer_id = $this->getViewerId();
    if(empty($viewer_id)){
      $description = '<span id="email_billing_error" class="seaocore_txt_red"></span>';
      $this->addElement('Text', 'email_billing', array(
          'label' => 'Email',
          'description' => $description,
          'allowEmpty' => false,
          'required' => true,
          'validators' => array(
              array('NotEmpty', true),
              array('EmailAddress', true)
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
          ),
      ));
      $this->email_billing->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));
      $this->email_billing->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);
    }

    $description = '<span id="phone_billing_error" class="seaocore_txt_red"></span>';
    $this->addElement('Text', 'phone_billing', array(
        'label' => 'Phone Number',
        'description' => $description,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', true, array(5, 32)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->phone_billing->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    //TAKING COUNTRY ARRAY
    $locale = Zend_Registry::get('Zend_Translate')->getLocale();
    $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
    $countryArray[''] = "Select Country";
    $shippingCountries = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getRegionsByName($params = array());

    foreach ($shippingCountries as $keys => $shippingCountry) {
      foreach ($countries as $keys => $country) {
        if ($shippingCountry['country'] == $keys) {
          $localeCountryArray[$keys] = $country;
          break;
        }
      }
    }
    
    asort($localeCountryArray);
    $countryArray = array_merge($countryArray, $localeCountryArray);

    $description = '<span id="country_billing_error" class="seaocore_txt_red"></span>';
    $this->addElement('Select', 'country_billing', array(
        'label' => 'Country',
        'description' => $description,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
        ),
        'multiOptions' => $countryArray,
        'value' => key($countryArray),
        'onchange' => "showRegions(0);"
    ));
    $this->country_billing->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    $this->addElement('Select', 'state_billing', array(
        'RegisterInArrayValidator' => false,
        'allowEmpty' => true,
        'required' => false,
        'label' => 'Region / State',
        'description' => '',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formSetBillingRegion.tpl',
                    'class' => 'form element'))),
    ));

    $description = '<span id="city_billing_error" class="seaocore_txt_red"></span>';
    $this->addElement('Text', 'city_billing', array(
        'label' => 'City',
        'description' => $description,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', true, array(1, 128)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->city_billing->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    $description = '<span id="locality_billing_error" class="seaocore_txt_red"></span>';
    $this->addElement('Text', 'locality_billing', array(
        'label' => 'Locality',
        'description' => $description,
        'allowEmpty' => true,
        'required' => false,
        'validators' => array(
            array('StringLength', true, array(0, 128)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->locality_billing->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));
    
    $description = '<span id="zip_billing_error" class="seaocore_txt_red"></span>';
    $this->addElement('Text', 'zip_billing', array(
        'label' => 'Zip/Pin Code',
        'description' => $description,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', true, array(3, 16)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->zip_billing->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    $description = '<span id="address_billing_error" class="seaocore_txt_red"></span>';
    $this->addElement('Textarea', 'address_billing', array(
        'label' => 'Address',
        'description' => $description,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_EnableLinks()
        ),
    ));
    $this->address_billing->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));
    
    $showShipping = $this->getShowShipping();
    if(!empty($showShipping)){

    $this->addElement('Checkbox', 'common', array(
        'label' => "Same Shipping Address",
        'value' => 1,
        'onclick' => 'onSameAddress();'
    ));

    $this->addElement('Dummy', 'dummy_shipping_address_title', array('label' => 'Shipping Address'));

    $description = '<span id="f_name_shipping_error" class="seaocore_txt_red"></span>';
    $this->addElement('Text', 'f_name_shipping', array(
        'label' => 'First Name',
        'description' => $description,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', true, array(1, 128)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->f_name_shipping->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    $description = '<span id="l_name_shipping_error" class="seaocore_txt_red"></span>';
    $this->addElement('Text', 'l_name_shipping', array(
        'label' => 'Last Name',
        'description' => $description,
        'allowEmpty' => true,
        'required' => false,
        'validators' => array(
            array('StringLength', true, array(1, 128)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->l_name_shipping->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));
 
    $description = '<span id="phone_shipping_error" class="seaocore_txt_red"></span>';
    $this->addElement('Text', 'phone_shipping', array(
        'label' => 'Phone Number',
        'description' => $description,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', true, array(5, 32)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->phone_shipping->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    $description = '<span id="country_shipping_error" class="seaocore_txt_red"></span>';
    $this->addElement('Select', 'country_shipping', array(
        'label' => 'Country',
        'description' => $description,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
        ),
        'multiOptions' => $countryArray,
        'value' => key($countryArray),
        'onchange' => 'showRegions(1);'
    ));
    $this->country_shipping->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    //ELEMENT TITLE
    $this->addElement('Select', 'state_shipping', array(
        'RegisterInArrayValidator' => false,
        'allowEmpty' => true,
        'required' => false,
        'label' => 'Region / State',
        'description' => '',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formSetShippingRegion.tpl',
                    'class' => 'form element'))),
    ));

    $description = '<span id="city_shipping_error" class="seaocore_txt_red"></span>';
    $this->addElement('Text', 'city_shipping', array(
        'label' => 'City',
        'description' => $description,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', true, array(1, 128)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->city_shipping->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    $description = '<span id="locality_shipping_error" class="seaocore_txt_red"></span>';
    $this->addElement('Text', 'locality_shipping', array(
        'label' => 'Locality',
        'description' => $description,
        'allowEmpty' => true,
        'required' => false,
        'validators' => array(
            array('StringLength', true, array(0, 128)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->locality_shipping->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));
    
    $description = '<span id="zip_shipping_error" class="seaocore_txt_red"></span>';
    $this->addElement('Text', 'zip_shipping', array(
        'label' => 'Zip/Pin Code',
        'description' => $description,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', true, array(3, 16)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    $this->zip_shipping->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    $description = '<span id="address_shipping_error" class="seaocore_txt_red"></span>';
    $this->addElement('Textarea', 'address_shipping', array(
        'label' => 'Address',
        'description' => $description,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_EnableLinks()
        ),
    ));
    $this->address_shipping->getDecorator('Description')->setOptions(array('placement' => 'POSTPEND', 'escape' => false));

    }
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