<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

 class Siteverify_AdminManageController extends Core_Controller_Action_Admin {

  // VIEW ALL VERIFY ENTRIES TO ADMIN AND DELETE  MULTIPLE VERIFY ENTRIES
  public function indexAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteverify_admin_main', array(), 'siteverify_admin_main_manage');

    // CHECK POST
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $value) {
        $verify = Engine_Api::_()->getItem('siteverify_verify', $value);
        $verify->delete();
      }
    }
    
    include_once APPLICATION_PATH . '/application/modules/Siteverify/controllers/license/license2.php';
  }

  // DELETE SINGLE VERIFY ENTRY
  public function deleteAction() {

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
        // DELETE THE VERIFY ENTRY FROM DATABASE
        $verifyObj->delete();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      // AFTER DELETE FORWARD TO THE SAME PAGE
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 300,
          'parentRefresh' => 300,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('This verify entry has been deleted successfully.'))
      ));
    }

    // OUTPUT
    $this->renderScript('admin-manage/delete.tpl');
  }

  //EDIT VERIFY ENTRY
  public function editAction() {

    $this->_helper->layout->setLayout('admin-simple');

    $verifyObj = Engine_Api::_()->getItem('siteverify_verify', $this->_getParam('id'));
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
  }

  // GET DETAIL OF USER VERIFY ENTRY.
  public function detailAction() {

    //GET RESOURCE ID AND VERIFY LIMIT OF USER WHOM DETAIL WE WANT TO SEE.
    //GET DETAILS OF RESOURCE AND POSTER
    $verifyObj = Engine_Api::_()->getItem('siteverify_verify', $this->_getParam('id'));
    $this->view->comments = $verifyObj->comments;
    $this->view->verify_date = $verifyObj->creation_date;
    $this->view->resourceObj = $resource = Engine_Api::_()->getItem('user', $verifyObj->resource_id);
    $this->view->posterObj = $poster = Engine_Api::_()->getItem('user', $verifyObj->poster_id);
    $this->view->resource_title = $resource->getTitle();
    $this->view->poster_title = $poster->getTitle();
    $this->view->verify_count = Engine_Api::_()->getDbtable('verifies', 'siteverify')->getVerifyCount($verifyObj->resource_id);

    // GET VERIFY LIMIT OF USER
    $this->view->verify_limit = Engine_Api::_()->authorization()->getPermission($resource->level_id, 'siteverify_verify', 'verify_limit');
  }

  //CHANGE SATAUS OF USER VERIFY ENTRY BY ADMIN
  public function statusAction() {

    try {
      $verifyObj = Engine_Api::_()->getItem('siteverify_verify', $this->_getParam('id'));
      $verifyObj->status = !empty($verifyObj->status) ? 0 : 1;
      $verifyObj->save();
    } catch (Exception $e) {
      throw $e;
    }
    $this->_redirect('admin/siteverify/manage');
  }
}