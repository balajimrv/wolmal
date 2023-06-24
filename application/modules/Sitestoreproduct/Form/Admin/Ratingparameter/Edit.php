<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Ratingparameter_Edit extends Engine_Form {

  protected $_field;

  public function init() {
    $this
            ->setTitle('Edit Review Parameters')
            ->setMethod('post')
            ->setAttrib('class', 'global_form_box');

    $categoryIdsArray = array();
    $categoryIdsArray[] = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    $ratingParams = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct')->reviewParams($categoryIdsArray, 'sitestoreproduct_product');

    foreach ($ratingParams as $ratingparam_id) {
      $this->addElement('Text', 'ratingparam_name_' . $ratingparam_id->ratingparam_id, array(
          'label' => '',
          'required' => true,
      ));
    }

    //$this->addElement('dummy', 'dummy_text', array('description' => 'You can also add some new rating parameters from below.'));    

    $this->addElement('textarea', 'options', array(
        'style' => 'display:none;',
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

  public function setField($ratingParams) {
    $this->_field = $ratingParams;

    foreach ($ratingParams as $ratingparam_id) {
      $ratingparam_field = 'ratingparam_name_' . $ratingparam_id->ratingparam_id;
      $this->$ratingparam_field->setValue($ratingparam_id->ratingparam_name);
    }
  }

}