<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photo.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_modules_User_Form_Edit_Photo extends Engine_Form {

  public function init() {
    $this
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAttrib('name', 'EditPhoto');

    $this->addElement('Image', 'current', array(
        'label' => 'Current Photo',
        'ignore' => true,
    ));
    //  Engine_Form::addDefaultDecorators($this->current);

    $this->addElement('File', 'Filedata', array(
        'label' => 'Choose New Photo',
        'destination' => APPLICATION_PATH . '/public/temporary/',
        'multiFile' => 1,
        'accept' => 'image/*',
        'validators' => array(
            array('Count', false, 1),
            // array('Size', false, 612000),
            array('Extension', false, 'jpg,jpeg,png,gif'),
        ),
    ));


    $this->addElement('Button', 'done', array(
        'label' => 'Save Photo',
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper'
        ),
    ));

    $this->addElement('Cancel', 'remove', array(
        'label' => 'remove photo',
        'link' => true,
        'prependText' => ' or ',
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
            'action' => 'remove-photo',
        )),
        'onclick' => null,
        'class' => 'smoothbox',
        'decorators' => array(
            'ViewHelper'
        ),
    ));

    $this->addDisplayGroup(array('done', 'remove'), 'buttons');
  }

}