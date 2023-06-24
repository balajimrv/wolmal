<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminFieldsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_AdminFieldsController extends Fields_Controller_AdminAbstract {

  protected $_fieldType = 'sitestore_store';
  protected $_requireProfileType = true;

	//ACTION FOR SHOWING THE PROFILE FIELDS
  public function indexAction() {

		//GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_fields');

    include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
  }

	//ACTION FOR PROFILE FIELD CREATION
  public function fieldCreateAction() {
    parent::fieldCreateAction();

    //GENERATE FORM
    $form = $this->view->form;

    if ($form) {

      //$form->setTitle('Add Profile Question');
      $form->removeElement('show');
      $form->addElement('hidden', 'show', array('value' => 0));

      $display = $form->getElement('display');
      $display->setLabel('Show on profile store?');

      $display->setOptions(array('multiOptions' => array(
              1 => 'Show on profile store',
              0 => 'Hide on profile store'
      )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');

      $search->setOptions(array('multiOptions' => array(
              0 => 'Hide on the search options',
              1 => 'Show on the search options'
      )));
      $form->addElement('Select', 'display_bill', array(
          'label' => 'Show on Bills',
          'multiOptions' => array(
              0 => 'Hide on the bills',
              1 => 'Show on the bills'
          ),
      ));
    }
    if ($this->getRequest()->isPost()) {
      $fieldmapsTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'meta');
      $field_id = $fieldmapsTable->select()->from($fieldmapsTable->info('name'), array('field_id'))
                      ->order('field_id DESC')->limit(1)
                      ->query()->fetchColumn();
      $fieldmapsTable->update(array('display_bill' => $_POST['display_bill']), array('field_id = ?' => $field_id));
    }
  }

	//ACTION FOR PROFILE FIELD EDITION
  public function fieldEditAction() {
    parent::fieldEditAction();

    //GENERATE FORM
    $form = $this->view->form;

    if ($form) {

      $form->setTitle('Edit Profile Question');
      $form->removeElement('show');
      $form->addElement('hidden', 'show', array('value' => 0));
      $display = $form->getElement('display');
      $display->setLabel('Show on profile store?');

      $display->setOptions(array('multiOptions' => array(
              1 => 'Show on profile store',
              0 => 'Hide on profile store'
      )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');

      $search->setOptions(array('multiOptions' => array(
              0 => 'Hide on the search options',
              1 => 'Show on the search options'
      )));
      $form->addElement('Select', 'display_bill', array(
          'label' => 'Show on Bills',
          'multiOptions' => array(
              0 => 'Hide on the bills',
              1 => 'Show on the bills'
          ),
      ));
      $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);
      $form->display_bill->setValue($field->display_bill);
    }
    if ($this->getRequest()->isPost()) {
      $fieldmapsTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'meta');
      $field_id = $this->_getParam('field_id');
      $fieldmapsTable->update(array('display_bill' => $_POST['display_bill']), array('field_id = ?' => $field_id));
    }
  }

	//ACTION FOR HEADING CREATION
  public function headingCreateAction() {
    parent::headingCreateAction();

    //GENERATE FORM
    $form = $this->view->form;

    if ($form) {
      $form->removeElement('show');
      $form->addElement('hidden', 'show', array('value' => 0));

      $form->removeElement('display');
      $form->addElement('hidden', 'display', array('value' => 1));
    }
    if ($this->getRequest()->isPost()) {
      $fieldmapsTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'meta');
      $field_id = $fieldmapsTable->select()->from($fieldmapsTable->info('name'), array('field_id'))
                      ->order('field_id DESC')->limit(1)
                      ->query()->fetchColumn();
      $fieldmapsTable->update(array('display_bill' => 1), array('field_id = ?' => $field_id));
    }
  }

	//ACTION FOR HEADING EDITION
  public function headingEditAction() {
    parent::headingEditAction();

    //GENERATE FORM
    $form = $this->view->form;

    if ($form) {
      $form->removeElement('show');
      $form->addElement('hidden', 'show', array('value' => 0));

      $form->removeElement('display');
      $form->addElement('hidden', 'display', array('value' => 1));
    }
  }

	//ACTION FOR PROFILE DELETION
  public function typeDeleteAction() {
    $option_id = $this->_getParam('option_id');

    if (!empty($option_id)) {

      //DELETE FIELD ENTRIES IF EXISTS
      $fieldmapsTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'maps');
      $select = $fieldmapsTable->select()->where('option_id =?', $option_id);
      $metaData = $fieldmapsTable->fetchAll($select)->toArray();
      if (!empty($metaData)) {
        foreach ($metaData as $key => $child_ids) {
          $child_id = $child_ids['child_id'];

          //DELETE FIELD ENTRIES IF EXISTS
          $fieldmetaTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'meta');
          $fieldmetaTable->delete(array(
              'field_id = ?' => $child_id,
          ));
        }
      }

      $fieldmapsTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'maps');
      $fieldmapsTable->delete(array(
          'option_id = ?' => $option_id,
      ));

      $sitestoretable = Engine_Api::_()->getDbtable('stores', 'sitestore');
      $select = $sitestoretable->select()
              ->from($sitestoretable->info('name'), array('store_id'))
              ->where('profile_type = ?', $option_id);
      $rows = $sitestoretable->fetchAll($select)->toArray();
      if (!empty($rows)) {
        foreach ($rows as $key => $sitestore_ids) {
          $sitestore_id = $sitestore_ids['store_id'];

          $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestore_id);
          $sitestore->profile_type = 0;
          $sitestore->save();

          //DELETE FIELD ENTRIES IF EXISTS
          $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'values');
          $fieldvalueTable->delete(array(
              'item_id = ?' => $sitestore_id,
          ));

          $fieldsearchTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'search');
          $fieldsearchTable->delete(array(
              'item_id = ?' => $sitestore_id,
          ));
        }
      }
			//DELETE MAPPING
			Engine_Api::_()->getDbtable('profilemaps', 'sitestore')->delete(array('profile_type = ?' => $option_id));
    }
    parent::typeDeleteAction();
  }

}

?>