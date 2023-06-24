<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Form_Admin_Ratingparameter_Edit extends Engine_Form {

  protected $_field;

  public function init() {
    $this
            ->setTitle('Edit Review Parameters')
            ->setMethod('post')
            ->setAttrib('class', 'global_form_box');

    $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);

    $tmTable = Engine_Api::_()->getDbtable('reviewcats', 'sitestorereview');
    $tmName = $tmTable->info('name');

    $select = $tmTable->select()
                    ->from($tmName, array('reviewcat_id'))
                    ->where('category_id = ?', $category_id);
    $reviewCategories = $tmTable->fetchAll($select)->toArray();

    foreach ($reviewCategories as $key => $reviewcat_id) {
      $this->addElement('Text', 'reviewcat_name_' . $reviewcat_id['reviewcat_id'], array(
          'label' => '',
          'required' => true,
      ));
    }

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

  public function setField($reviewCategories) {
    $this->_field = $reviewCategories;

    foreach ($reviewCategories as $key => $reviewcat_id) {
      $reviewcat_field = 'reviewcat_name_' . $reviewcat_id['reviewcat_id'];
      $this->$reviewcat_field->setValue($reviewcat_id['reviewcat_name']);
    }
  }

}
?>