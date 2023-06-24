<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Addicon.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Form_Admin_Manage_AddIcon extends Engine_Form {

  protected $_key;

  public function getItem() {
    return $this->_key;
  }

  public function setItem($key) {
    $this->_key = $key;
    return $this;
  }

  public function init() {

    if ($this->_key) {
      $this
              ->setTitle('Edit Home Screen Icon')
              ->setDescription('Upload an image for the mobile home screen icon and view its preview. The recommended dimension of this icon is ' . $this->_key . 'px.')
              ->setAttrib('class', 'global_form_popup')
      ;

      $this->addElement('File', 'photo', array(
//        'label' => 'Upload an image ('.$this->_key.'px)',
          'required' => true
      ));
    } else {
      $this
              ->setTitle('Add Home Screen Icon')
              ->setDescription('Upload an image for the mobile home screen icon and view its preview.')
              ->setAttrib('class', 'global_form_popup')
      ;

      $this->addElement('File', 'photo', array(
//        'label' => 'Upload an image',
          'required' => true
      ));
    }


    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Icon',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}
