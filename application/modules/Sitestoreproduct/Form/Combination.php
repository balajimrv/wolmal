<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Combination.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Combination extends Engine_Form {

  protected $_category_drop_downs;
  protected $_product_id;
  
  public function getCategoryDropdowns(){
    return $this->_category_drop_downs;
  }

  public function setCategoryDropdowns($category_drop_downs) {
    $this->_category_drop_downs = $category_drop_downs;
    return $this;
  }
  
  public function getProductId(){
    return $this->_product_id;
  }
  
  public function setProductId($product_id){
    $this->_product_id = $product_id;
    return $this;
  }


    public function init() {
      
    $this->setMethod('POST')
            ->setAttrib('class', 'global_form_smoothbox')
            ->setTitle(Zend_Registry::get('Zend_Translate')->_('Make Variations'))
            ->setDescription(Zend_Registry::get('Zend_Translate')->_("Create a new product variation based on your product's select-box type attributes."));
    $hidden_order = 700;
    foreach($this->_category_drop_downs as $field_id => $dropDown){
      $select_box_id = 'select_'. $field_id;
      $this->addElement('Select', $select_box_id, array(
        'label' => Zend_Registry::get('Zend_Translate')->_($dropDown['lable']) . '<span style="color:red;"> * </span>',
        'multiOptions' => $dropDown['multioptions'],
        'required' => true,
        'allowEmpty' => false,
        'onchange' => "showPrice(this ,'$field_id', '$this->_product_id')",
    ));
    $this->$select_box_id->getDecorator('Label')->setOptions(array('escape' => false));
    
    $hidden_field_id = 'select_response_'. $field_id;
    $this->addElement('Hidden', $hidden_field_id, array(
            'order' => $hidden_order++,
        ));
    $exist_field_id = 'existed_' . $field_id;
      $this->addElement('Hidden', $exist_field_id, array(
          'order' => $hidden_order++,
      ));
    
    $this->addElement('Radio', 'price_increment_' . $field_id, array(
        'label' => Zend_Registry::get('Zend_Translate')->_("Increment / Decrement"),
        'multiOptions' => array(
            "1" => "Increment", "0" => "Decrement"
        ),
        'value' => 1,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
    )));
    
    $this->addElement('Text', 'price_' . $field_id, array(
         'label' => Zend_Registry::get('Zend_Translate')->_('Price'),
         'allowEmpty' => true,
         'maxlength' => 12,
         'value' => 0.00,
         'validators' => array(
            array('GreaterThan', false, array(-1))
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
    )));
    }
    
    $this->addElement('Select', 'status', array(
        'label' => Zend_Registry::get('Zend_Translate')->_("Status"),
        'multiOptions' => array(
            "1" => "Enabled", "0" => "Disabled"
        ),
        'value' => 1,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
    )));
    
    $this->addElement('Text', 'quantity', array(
          'label' => Zend_Registry::get('Zend_Translate')->_('Quantity'),
          'maxlength' => 5,
          'required' => true,
          'allowedEmpty' => false,
          'validators' => array(
              array('Int', false),
              array('GreaterThan', true, array(-1))
          ),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
      )));
    $this->addElement('Button', 'submit', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Save Variation'),
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
        'order' => 10000,
        'ignore' => true,
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('cancel'),
        'link' => true,
        'onclick' => 'parent.Smoothbox.close();',
        'prependText' => ' or ',
        'decorators' => array(
            'ViewHelper',
        ),
        'order' => 10001,
        'ignore' => true,
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
        'order' => 10002,
    ));
  }

}
