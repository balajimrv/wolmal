<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteverify_IndexController extends Seaocore_Controller_Action_Standard {

  protected $_viewer;

  public function init() {
    $this->_viewer = Engine_Api::_()->user()->getViewer();
  }

  //HERE CURRENT VIEWING USER IS VERIFIED AND A SUCCESSFULL MESSEGE IS DISPLAYED
  public function proceedToVerifyAction() {

    //TO CHECK USER IS LOGGED IN OR NOT
    $viewer_id = $this->_viewer->getIdentity();
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET THE VALUE OF RESOURCE ID ,RESOURCE TYPE AND RESOURCR TITLE
    $this->view->resource_id = $resource_id = $this->_getParam('resource_id');
    $resource = Engine_Api::_()->getItem('user', $resource_id);

    //TO CHECK ADMIN HAS ALLOWED TO VERIFY OR NOT
    $siteverify_allow_verify = Zend_Registry::isRegistered('siteverify_allow_verify') ? Zend_Registry::get('siteverify_allow_verify') : null;
    $allow_verify = Engine_Api::_()->authorization()->getPermission($resource->level_id, 'siteverify_verify', 'allow_verify');
    $verifyOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteverify_verify', $resource, 'auth_verify');
    $siteverifyManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.manage.type', 1);
    $siteverifyInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.info.type', 1);
    $hostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
    $tempHostType = $tempSitemenuLtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.global.view', 0);
    $siteverifyLtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.lsettings', null);
    $siteverifyGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.global.type', 0);

    if (empty($siteverify_allow_verify) || empty($allow_verify) || !in_array($this->_viewer->level_id, $verifyOptions)) {
      return;
    }

    $this->view->resource_title = $resource->getTitle();
    //DUMP ALL THE VALUE IN AN ARRAY
    $values = array();
    $values['resource_type'] = 'user';
    $values['resource_id'] = $resource_id;
    $values['poster_type'] = "user";
    $values['poster_id'] = $viewer_id;
    $values['comments'] = $this->_getParam('comments');

    //GET THE VERIFY COUNT ,VERIFY LIMIT, ALLOW UNVERIFY AND IS COMMENT .
    $verifyTableObj = Engine_Api::_()->getDbtable('verifies', 'siteverify');
    $authorizationApi = Engine_Api::_()->authorization();

    $verify_count = $verifyTableObj->getVerifyCount($resource_id);
    $this->view->verify_limit = $authorizationApi->getPermission($resource->level_id, 'siteverify_verify', 'verify_limit');
    $this->view->allow_unverify = $authorizationApi->getPermission($resource->level_id, 'siteverify_verify', 'allow_unverify');
    $this->view->is_comment = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.comment.verifypopup', 1);

    //IF ADMIN APPROVE IS ENABLED THEN ADMIN APPROVE WILL BE 0.
    $this->view->admin_approve = $admin_approve = $authorizationApi->getPermission($resource->level_id, 'siteverify_verify', 'admin_approve');
    if (!empty($admin_approve))
      $values['admin_approve'] = 0;
    else
      $verify_count = ++$verify_count; //OTHERWISE VERIFYCOUNT WILL BE INCREASED.
    $this->view->verify_count = $verify_count;
    
    if(empty($siteverifyGlobalType)) {
      for ($check = 0; $check < strlen($hostType); $check++) {
        $tempHostType += @ord($hostType[$check]);
      }

      for ($check = 0; $check < strlen($siteverifyLtype); $check++) {
        $tempSitemenuLtype += @ord($siteverifyLtype[$check]);
      }
    }
    
    if((empty($siteverifyGlobalType)) && (($siteverifyManageType != $tempHostType) || ($siteverifyInfoType != $tempSitemenuLtype)))
      return;

    //TO INSERT DATA IN VERIFY TABLE
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $verify = $verifyTableObj->createRow();
      $verify->setFromArray($values);
      $verify->save();
      $this->view->verify_id = $verify->verify_id; //$db->lastInsertId($verifyTableName);
      // NOTIFICATION AND ACTIVITY FEED WORK
      if (empty($admin_approve)) {
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($resource, $this->_viewer, $verify, 'siteverify_new');
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($this->_viewer, $resource, 'siteverify_new');
      } else {
        $userObj = Engine_Api::_()->getDbtable('users', 'user');
        $select = $userObj->select()->where('level_id = ?', 1);
        $adminObj = $userObj->fetchAll($select);
        foreach ($adminObj as $adminRow) {
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($adminRow, $this->_viewer, $verify, 'siteverify_user_request');
        }
      }
            
      if((empty($siteverifyGlobalType)) && (($siteverifyManageType != $tempHostType) || ($siteverifyInfoType != $tempSitemenuLtype))) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteverify.viewtypeinfo.type', 0);
      }
      
      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  // SHOW THE VERIFY PAGE IN THE POP-UP
  public function verifyAction() {

    //TO CHECK USER IS LOGGED IN OR NOT
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET THE VALUE OF RESOURCE ID ,RESOURCE TYPE AND RESOURCR TITLE
    $this->view->resource_id = $this->_getParam('resource_id');
    $this->view->resource = $resource = Engine_Api::_()->getItem('user', $this->view->resource_id);

    //TO CHECK ADMIN HAS ALLOWED TO VERIFY OR NOT
    $siteverify_allow_verify_popup = Zend_Registry::isRegistered('siteverify_allow_verify_popup') ? Zend_Registry::get('siteverify_allow_verify_popup') : null;
    $allow_verify = Engine_Api::_()->authorization()->getPermission($resource->level_id, 'siteverify_verify', 'allow_verify');
    $verifyOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteverify_verify', $resource, 'auth_verify');

    if (empty($siteverify_allow_verify_popup) || empty($allow_verify) || !in_array($this->_viewer->level_id, $verifyOptions))
      return;

    $this->view->is_comment = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.comment.verifypopup', 1);
    $this->view->resource_title = $resource->getTitle();
  }

  // GET LIST OF USERS WHO HAS VERIFIED CURRENT VIEWING USERS
  public function contentVerifyMemberListAction() {

    //TO CHECK USER IS LOGGED IN OR NOT
    if (!$this->_helper->requireUser()->isValid())
      return;
    
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) 
      Zend_Registry::set('setFixedCreationFormBack', 'Back');


    $this->view->resource_id = $resource_id = $this->_getParam('resource_id');
    $resource = Engine_Api::_()->getItem('user', $resource_id);
    $this->view->resource_title = $resource->getTitle();

    $this->view->current_page = $page = $this->_getParam('page', 1);
    $this->view->current_total_verify = $page * 10;

    $verifyTable = Engine_Api::_()->getDbtable('verifies', 'siteverify');
    $this->view->verify_count = $verifyTable->getVerifyCount($resource_id);

    // GET LIST OF USERS WHO HAS VERIFIED CURRENT VIEWING USERS
    $params = array('admin_approve' => 1, 'resource_id' => $resource_id, 'status' => 1);
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('verifies', 'siteverify')->getVerifyPaginator($params);
    $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(10);
  }

  //SHOW THE EDIT PAGE IN THE POP-UP
  public function editVerifyAction() {

    //TO CHECK USER IS LOGGED IN OR NOT
    if (!$this->_helper->requireUser()->isValid())
      return;

    //TO CHECK ADMIN HAS ALLOWED TO VERIFY OR NOT
    $is_comment = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.comment.verifypopup', 1);

    if (empty($is_comment))
      return $this->_forward('requireauth', 'error', 'core');

    //GET THE VALUE OF RESOURCE ID ,RESOURCE TYPE AND RESOURCR TITLE
    $this->view->verify_id = $verify_id = $this->_getParam('verify_id');
    $verifyObj = Engine_Api::_()->getItem('siteverify_verify', $verify_id);
    $this->view->resource = $resource = Engine_Api::_()->getItem('user', $verifyObj->resource_id);
    $this->view->resource_title = $resource->getTitle();
    $this->view->comments = $verifyObj->comments;
  }

  //UPDATE DATA BASE AFTER EDIT REQUEST
  public function afterEditRequestAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET  VERIFY ID,COMMENTS
    $this->view->verify_id = $verify_id = $this->_getParam('verify_id');
    $comments = $this->_getParam('comments');
    $verifyObj = Engine_Api::_()->getItem('siteverify_verify', $verify_id);
    $this->view->resource_id = $resource_id = $verifyObj->resource_id;
    $resource = Engine_Api::_()->getItem('user', $resource_id);
    $this->view->resource_title = $resource->getTitle();

    //GET THE VERIFY COUNT  VERIFY LIMIT AND ALLOW UNVERIFY.
    $siteverify_afteredit_request = Zend_Registry::isRegistered('siteverify_afteredit_request') ? Zend_Registry::get('siteverify_afteredit_request') : null;
    $verifyTableObj = Engine_Api::_()->getDbtable('verifies', 'siteverify');
    $authorizationApi = Engine_Api::_()->authorization();
    $siteverifyManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.manage.type', 1);
    $siteverifyInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.info.type', 1);
    $hostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
    $tempHostType = $tempSitemenuLtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.global.view', 0);
    $siteverifyLtype = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.lsettings', null);
    $siteverifyGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.global.type', 0);
    
    if(empty($siteverify_afteredit_request))
      return;

    $this->view->verify_count = $verifyTableObj->getVerifyCount($resource_id);

    $this->view->verify_limit = $authorizationApi->getPermission($resource->level_id, 'siteverify_verify', 'verify_limit');

    $this->view->allow_unverify = $authorizationApi->getPermission($resource->level_id, 'siteverify_verify', 'allow_unverify');

    $this->view->is_comment = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteverify.comment.verifypopup', 1);

    $this->view->admin_approve = $verifyObj->admin_approve;

    //UPDATE VERIFY DATA BASE
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $verifyObj->comments = $comments;
      // UPDATE THE VERIFY ENTRY FROM DATABASE
      $verifyObj->save();
      
      if(empty($siteverifyGlobalType)) {
        for ($check = 0; $check < strlen($hostType); $check++) {
          $tempHostType += @ord($hostType[$check]);
        }

        for ($check = 0; $check < strlen($siteverifyLtype); $check++) {
          $tempSitemenuLtype += @ord($siteverifyLtype[$check]);
        }

        if(($siteverifyManageType != $tempHostType) || ($siteverifyInfoType != $tempSitemenuLtype)) {
          Engine_Api::_()->getApi('settings', 'core')->setSetting('siteverify.viewtypeinfo.type', 0);
        }
      }      
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function deleteVerifyAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    // IN SMOOTHBOX
    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) 
    $this->_helper->layout->setLayout('admin-simple');

    //GET VERIFY ID
    $this->view->verify_id = $this->_getParam('verify_id');
    $siteverify_delete_verify = Zend_Registry::isRegistered('siteverify_delete_verify') ? Zend_Registry::get('siteverify_delete_verify') : null;
    if(empty($siteverify_delete_verify))
      return $this->_forward('requireauth', 'error', 'core');

    //TO CHECK IF ADMIN HAS ENABLE ALLOW UNVERIFY OR NOT
    $verifyObj = Engine_Api::_()->getItem('siteverify_verify', $this->view->verify_id);
    $resource_id = $verifyObj->resource_id;
    $resource = Engine_Api::_()->getItem('user', $resource_id);
    $allow_unverify = Engine_Api::_()->authorization()->getPermission($resource->level_id, 'siteverify_verify', 'allow_unverify');
    if (empty($allow_unverify)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
  }

  // DELETE ENTRY FROM DATA BASE AFETR DELETR REQUEST
  public function afterDeleteRequestAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VERIFY ID ,RESOURCE ID AND RESOURCE TYPE
    $this->view->viewer = $this->_viewer;
    $verify_id = $this->_getParam('verify_id');
    $verifyObj = Engine_Api::_()->getItem('siteverify_verify', $verify_id);
    $this->view->resource_id = $resource_id = $verifyObj->resource_id;
    $resource = Engine_Api::_()->getItem('user', $resource_id);
    $this->view->resource_title = $resource->getTitle();

    //GET THE VERIFY COUNT ,VERIFY LIMIT AND ALLOW VERIFY
    $verifyTableObj = Engine_Api::_()->getDbtable('verifies', 'siteverify');
    $authorizationApi = Engine_Api::_()->authorization();

    $verify_count = $verifyTableObj->getVerifyCount($resource_id);

    $this->view->verify_limit = $authorizationApi->getPermission($resource->level_id, 'siteverify_verify', 'verify_limit');

    $admin_approve = $verifyObj->admin_approve;

    if (!empty($admin_approve))
      $this->view->verify_count = $verify_count - 1;
    else
      $this->view->verify_count = $verify_count;

    //TO DELETE  DATA IN VERIFY TABLE
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

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
  }

}