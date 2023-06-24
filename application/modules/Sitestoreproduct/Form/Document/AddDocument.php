<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddDocument.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Form_Document_AddDocument extends Engine_Form {

  public function init() {
     $viewer = Engine_Api::_()->user()->getViewer();
    $level_id = $viewer->level_id;
    $filter = new Engine_Filter_Html();
    $this->setTitle('Add New Document');
    $this->setName('create_product_document');
    $this->setDescription("Add new document by filling the information below, then click 'Submit'.");


    $this->addElement('Text', 'title', array(
        'label' => 'Document Title',
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
    
    $this->addElement('textarea', 'body', array(
          'label' => 'Document Description',
        'attribs' => array('rows'=>24, 'cols'=>80, 'style'=>'width:300px; max-width:553px;height:120px;'),
          'filters' => array(
            'StripTags',
              $filter,
              new Engine_Filter_Censor(),
          ),
      ));
    
      $filesize = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'filesize');
    $description = Zend_Registry::get('Zend_Translate')->_('Browse and choose a file for your document. Maximum permissible size: %s KB and allowed file types: pdf, txt, ps, rtf, epub, odt, odp, ods, odg, odf, sxw, sxc, sxi, sxd, doc, ppt, pps, xls, docx, pptx, ppsx, xlsx, tif, tiff');
    $description = sprintf($description, $filesize);
    $this->addElement('File', 'filename', array(
        'label' => 'Document File',
        'required' => true,
        'description' => $description
    ));
 
     if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.document.privacy')){
       $this->addElement('Select', 'privacy', array(
     
        'label' => 'Allow Document Download',
        'multiOptions' => array("1" => "Yes, allow all users", "0" => "Show only to the buyers of this product"),
    ));
     }
    $this->addElement('Checkbox', 'status', array(
        'label' => "Enable this Document",
        'value' => 1,
    ));
    
     $this->addElement('Button', 'submit_doc', array(
        'label' => 'Submit',
        'type' => 'submit',
        'ignore' => true,
    ));
    
    
  }

}