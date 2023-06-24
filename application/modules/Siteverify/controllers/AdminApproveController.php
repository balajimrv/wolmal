<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminApproveController.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteverify_AdminApproveController extends Core_Controller_Action_Admin {

  // VIEW ALL VERIFY ENTRIES TO ADMIN AND DELETE MULTIPLE VERIFY ENTRIES
  public function indexAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteverify_admin_main', array(), 'siteverify_admin_main_approve');

    // CHECK POST
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $value) {
        $verifyObj = Engine_Api::_()->getItem('siteverify_verify', $value);
        $verifyObj->delete();
      }
    }

    include_once APPLICATION_PATH . '/application/modules/Siteverify/controllers/license/license2.php';
  }

  // DELETE SINGLE VERIFY ENTRY
  public function deleteAction() {

    // IN SMOOTHBOX
    $this->_helper->layout->setLayout('admin-simple');

    // CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $verifyObj = Engine_Api::_()->getItem('siteverify_verify', $this->_getParam('id'));

        //DELETE ACTIVITY FEED
        $action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?' => 'siteverify_new', 'subject_id = ?' => $verifyObj->poster_id, 'object_id = ?' => $verifyObj->resource_id));
        if (!empty($action_id)) {
          $action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
          $action->delete();
        }

        //DELETE VERIFY ENTRY
        $verifyObj->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      // AFTER DELETE FORWARD TO THE SAME PAGE
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('This verify entry has been deleted successfully.'))
      ));
    }
    $this->renderScript('admin-manage/delete.tpl');
  }

  // TO APPROVE USER VERIFY ENTRY BY ADMIN
  public function approveAction() {

    $this->_helper->layout->setLayout('admin-simple');

    // CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        //  IF ADMIN APPROVES THEN ADMIN APPROVE WILL BE FROM 0 TO 1.
        $verifyObj = Engine_Api::_()->getItem('siteverify_verify', $this->_getParam('id'));
        $verifyObj->admin_approve = 1;

        //UPDATE THE VERIFY ENTRY FROM DATABASE
        $verifyObj->save();

        // NOTIFICATION AND ACTIVITY FEED WORK
        $resource = Engine_Api::_()->getItem('user', $verifyObj->resource_id);
        $poster = Engine_Api::_()->getItem('user', $verifyObj->poster_id);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($resource, $poster, $verifyObj, 'siteverify_new');
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($poster, $resource, $verifyObj, 'siteverify_admin_approve');
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($poster, $resource, 'siteverify_new');

        //COMMIT
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      // AFTER APPROVE FORWARD TO THE SAME PAGE
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 400,
          'parentRefresh' => 400,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('This verification request has been approved successfully.'))
      ));
    }
    // OUTPUT
    $this->renderScript('admin-approve/approve.tpl');
  }

  public function editAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $verifyObj = Engine_Api::_()->getItem('siteverify_verify', $this->_getParam('id'));
    
    //MAKE EDIT FORM
    $this->view->form = $form = new Siteverify_Form_Admin_Edit();

    //POPULATE EDIT FORM
    $form->populate($verifyObj->toarray());

    // CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $verifyObj->comments = $_POST['comments'];
        // UPDATE THE VERIFY ENTRY FROM DATABASE
        $verifyObj->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      // AFTER EDIT FORWARD TO THE SAME PAGE
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 300,
          'parentRefresh' => 300,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have changed comments successfully.'))
      ));
    }

    // OUTPUT
    $this->renderScript('admin-approve/edit.tpl');
  }
}