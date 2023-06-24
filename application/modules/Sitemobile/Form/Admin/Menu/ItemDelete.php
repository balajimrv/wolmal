<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ItemDelete.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Form_Admin_Menu_ItemDelete extends Engine_Form {

  protected $_addType;

  public function getAddType() {
    return $this->_addType;
  }

  public function setAddType($addType) {
    $this->_addType = $addType;
    return $this;
  }

  public function init() {
    $this
            ->setTitle('Delete ' . $this->_addType)
            ->setDescription('Are you sure that you want to delete this ' . $this->_addType . '?')
            ->setAttrib('class', 'global_form_popup')
    ;

    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Delete ' . $this->_addType,
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