<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photo.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Photo_Cover extends Engine_Form {

  public function init() {


    $this
            ->setTitle("Upload Store's Cover Photo")
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAttrib('id', 'cover_photo_form')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setAttrib('name', 'Upload A Cover Photo');



    $this->addElement('File', 'Filedata', array(
        'label' => 'Cover photo makes your store look attractive. Choose and upload a cover photo.',
        'destination' => APPLICATION_PATH . '/public/temporary/',
        'description' => 'The recommended height for the photo is 300px to enable "Drag to Reposition Cover Photo" feature.',
        'validators' => array(
            array('Extension', false, 'jpg,jpeg,png,gif'),
        ),
      'onchange' => 'javascript:uploadPhoto();'
    ));
    $this->Filedata->addDecorator('Description', array('placement' => 'APPEND', 'class' => 'description', 'escape' => false));

//    $this->addElement('Button', 'submit', array(
//        'label' => 'Save Photos',
//        'type' => 'submit',
//    ));

//    if (!Engine_Api::_()->getApi('settings', 'core')->sitestore_requried_photo) {
//      if ($sitestore->photo_id != 0) {
//        $this->addElement('Cancel', 'remove', array(
//            'label' => 'Remove Photo',
//            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
//                'action' => 'remove-photo',
//            )),
//            'onclick' => null,
//            'decorators' => array(
//                'ViewHelper'
//            ),
//        ));
//        $this->addDisplayGroup(array('done', 'remove'), 'buttons');
//      }
//    }
  }

}

?>