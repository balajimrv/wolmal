<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProductUpload.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_File_ProductUpload extends Engine_Form
{  
  protected $_type;
  
  public function getType() {
    return $this->_type;
  }

  public function setType($type) {
    $this->_type = $type;
    return $this;
  }
  
  public function init()
  {
    
    $type =  $this->getType();

    /* 
     * main: Upload main files for downloadable products.
     * sample: Upload sample files for downloadable products.
    */
    if($type == 'main'){
      $tempLabel = 'Choose Main File';
      $this
      ->setTitle('Upload Main File')
      ->setDescription('Choose a file for your product below.');
    }else{
      $tempLabel = 'Choose Sample File';
      $this
      ->setTitle('Upload Sample File')
      ->setDescription('Choose a sample file for your product below.');
    }
    
    $this
      ->setAttrib('id', 'product_upload')
      ->setAttrib('name', 'product_upload')
      ->setAttrib('enctype','multipart/form-data');
    
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
          'description' => 'Please enter 0 or leave this field empty for unlimited downloads',
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
        
    $this->addElement('File', 'upload_product', array(
        'label' => $tempLabel,
        'required' => true,
    ));

//    $this->addElement('Checkbox', 'status', array(
//        'label' => "Show to Buyer",
//        'value' => 1,
//    ));
      
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Upload File',
      'type' => 'submit',
    ));
  }
}
