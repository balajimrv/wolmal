<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ShippingSettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Shipping_ShippingSettings extends Engine_Form {

  public function init() {
    $user = Engine_Api::_()->user()->getViewer();

    $this->setTitle('Shipping Settings');

    // ACCESSING COUNTRIES ARRAY
    $locale = Zend_Registry::get('Zend_Translate')->getLocale();
    $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
    $country[0] = "";
    foreach ($countries as $keys => $countrie) {
      $country[$keys] = $countrie;
    }
    asort($country);

    $this->addElement('Select', 'country', array(
        'label' => 'Country',
        'multiOptions' => $country,
        'value' => key($country),
    ));

    $this->addElement('Text', 'state', array(
        'label' => 'Region / State',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', false, array(1, 64)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Text', 'city', array(
        'label' => 'City',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', false, array(1, 128)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
    ));
    
    $this->addElement('Text', 'zip', array(
      'label' => 'Zip/Pin Code',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
          array('NotEmpty', true),
          array('StringLength', false, array(3, 16)),
      ),
      'filters' => array(
          'StripTags',
          new Engine_Filter_Censor(),
      ),
    ));

    $this->addElement('Textarea', 'address', array(
        'label' => 'Address',
        'validators' => array(
            array('NotEmpty', true),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_EnableLinks()
        ),
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Settings',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit'), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
        ),
    ));
  }

}