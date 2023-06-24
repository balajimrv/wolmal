<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: EditFile.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_File_EditFile extends Engine_Form {

  protected $_type;
  
  public function getType() {
    return $this->_type;
  }

  public function setType($type) {
    $this->_type = $type;
    return $this;
  }
  
  public function init() {
    
    $type =  $this->getType();
    
    $this->setTitle('Edit File Information');

    $this->addElement('Text', 'title', array(
        'label' => "File Title",
        'allowEmpty' => false,
        'required' => true,
        'filters' => array(
            'StripTags',
             new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '255')),
            )));
    
    if($type == 'main'){
     $this->addElement('Text', 'download_limit', array(
        'label' => 'Max Downloads',
        'description' => 'Please enter 0 or leave this field empty for unlimited download',
        'allowEmpty' => false,
          'required' => true,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', false, array(-1))
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            )));
     $this->download_limit->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
    }

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
			 'decorators' => array(
            'ViewHelper',
        ),
    ));
    
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'order' => '999',
        'onclick' => "javascript:parent.Smoothbox.close();",
        'href' => "javascript:void(0);",
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array(
        'submit',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->setOrder('999');
  }

}