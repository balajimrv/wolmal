<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Delete.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Form_Admin_Ratingparameter_Delete extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Delete Review Parameters?')
            ->setDescription('Please click on the checkbox to select a parameter from below and then click "Delete" to delete them. Note that these review parameters will not be recoverable after being deleted.')
            ->setMethod('post')
            ->setAttrib('class', 'global_form_box');

    $category_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);

    $tmTable = Engine_Api::_()->getDbtable('reviewcats', 'sitestorereview');
    $tmName = $tmTable->info('name');

    $select = $tmTable->select()
                    ->from($tmName, array('reviewcat_id', 'reviewcat_name'))
                    ->where('category_id = ?', $category_id);
    $reviewCategories = $tmTable->fetchAll($select)->toArray();

    foreach ($reviewCategories as $key => $reviewcat_id) {
      $this->addElement('Checkbox', 'reviewcat_name_' . $reviewcat_id['reviewcat_id'], array(
          'label' => $reviewcat_id['reviewcat_name'],
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
?>