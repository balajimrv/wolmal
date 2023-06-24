<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Delete.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Form_Admin_Ratingparameter_Delete extends Engine_Form {

  public function init() {

    $this->setTitle('Delete Review Parameters?')
        ->setDescription('Please click on the checkbox to select a parameter from below and then click "Delete" to delete them. Note that these review parameters will not be recoverable after being deleted.')
        ->setMethod('post')
        ->setAttrib('class', 'global_form_box');

    $categoryIdsArray = array();
    $categoryIdsArray[] = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    $ratingParams = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct')->reviewParams($categoryIdsArray, 'sitestoreproduct_product');

    foreach ($ratingParams as $ratingparam_id) {
      $this->addElement('Checkbox', 'ratingparam_name_' . $ratingparam_id->ratingparam_id, array(
          'label' => $ratingparam_id->ratingparam_name,
          'value' => 0,
      ));
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Delete',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => 'or ',
        'href' => '',
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}