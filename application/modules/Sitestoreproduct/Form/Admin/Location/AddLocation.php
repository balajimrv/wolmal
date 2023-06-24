<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddLocation.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Location_AddLocation extends Engine_Form {

  public function init() {

    $this->setTitle('Add Location')
            ->setMethod('post')
            ->setAttrib('class', 'global_form_box');

    //TAKING COUNTRIES OBJECT
    $locale = Zend_Registry::get('Zend_Translate')->getLocale();
    $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
    foreach ($countries as $keys => $tempCountry) {
      $country[$keys] = $tempCountry;
    }
   
    //GETTING DISABLED COUNTRIES
    $disabledCountryArray =  Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getDisabledCountries();
    
    //UNSETTING DISABLED COUNTRIES IN ARRAY
    foreach($disabledCountryArray as $countryName)
    {
      unset ($country[$countryName]);
    }
    @asort($country);
    
    $getAllEmptyRegionsArray = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getAllRegionsCountryArray();
    foreach($getAllEmptyRegionsArray as $obj) {
      if(array_key_exists($obj['country'], $country)) {
        unset($country[$obj['country']]);
      }
    }
    

    $this->addElement('Select', 'country', array(
        'label' => 'Country',
        'multiOptions' => $country,
        'value' => key($country),
    ));
    
    $this->addElement('Radio', 'all_regions', array(
        'label' => 'Enable all Regions / States',
        'multiOptions' => array(
             1 => 'Yes',
             0 => 'No'
         ),
        'onclick' => 'allRegion()',
        'value' => 1
    ));

    $this->addElement('Text', 'regions', array(
        'label' => 'Regions / States',
        'style' => 'display:none;',
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Add Location',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}