<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Field.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Field extends Engine_Form {

  public function init() {
    
    $this->setMethod('POST')
            ->setAttrib('class', 'global_form_smoothbox')
            ->setTitle('Edit Profile Question');

    $categories = Engine_Api::_()->fields()->getFieldInfo('categories');
    $types = Engine_Api::_()->fields()->getFieldInfo('fields');
    $fieldByCat = array();
    $availableTypes = array();
    foreach ($types as $fieldType => $info) {
//        if($fieldType == 'select' || $fieldType == 'multiselect' || $fieldType == 'radio' || $fieldType == 'multi_checkbox')
        if($fieldType == 'select' || $fieldType == 'radio' || $fieldType == 'multi_checkbox' || $fieldType == 'multiselect' || $fieldType == 'checkbox' || $fieldType == 'text' || $fieldType == 'textarea')
            $fieldByCat[$info['category']][$fieldType] = $info['label'];
    }

    if(isset($categories['specific'])) {
      unset($categories['specific']);
    }

    $categories['generic']['label'] = '';
    foreach ($categories as $catType => $categoryInfo) {
      $label = $categoryInfo['label'];
      $availableTypes[$label] = $fieldByCat[$catType];
    }
  
    if(isset($availableTypes['Generic']['date'])) {
      unset($availableTypes['Generic']['date']);
    }
    
    if(isset($availableTypes['Generic']['heading'])) {
      unset($availableTypes['Generic']['heading']);
    }    

    $this->addElement('Select', 'type', array(
        'label' => 'Attribute Type',
        'required' => true,
        'allowEmpty' => false,
        'multiOptions' => $availableTypes,
        'onchange' => 'var form = this.getParent("form"); form.method = "get"; form.submit();',
    ));

    $this->addElement('Text', 'label', array(
        'label' => 'Attribute Label',
        'required' => true,
        'allowEmpty' => false,
        'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
    ));

    $this->addElement('Textarea', 'description', array(
        'label' => 'Description',
        'rows' => 6,
        'filters' => array(
             'StripTags',
            new Engine_Filter_Censor(),
        )
    ));

    $this->addElement('Hidden', 'required', array(
        'label' => 'Required?',
        'multiOptions' => array(
            0 => 'Not Required',
            1 => 'Required'
        ),
        'value' => 1,
    ));

    $this->addElement('Button', 'execute', array(
        'label' => 'Save Attribute',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
        'order' => 10000,
        'ignore' => true,
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'onclick' => 'parent.Smoothbox.close();',
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper',
        ),
        'order' => 10001,
        'ignore' => true,
    ));

    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array(
        'order' => 10002,
    ));
  }

}
