<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TopicController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_TopicController extends Seaocore_Controller_Action_Standard {

  //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {

    //AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
      return;
    
    if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
      return;  
    
    //RETURN IF SUBJECT IS ALREADY SET
    if (Engine_Api::_()->core()->hasSubject())
      return;

    //SET TOPIC OR PRODUCT SUBJECT
    if (0 != ($topic_id = (int) $this->_getParam('topic_id')) &&
            null != ($topic = Engine_Api::_()->getItem('sitestoreproduct_topic', $topic_id))) {
      Engine_Api::_()->core()->setSubject($topic);
    } else if (0 != ($product_id = (int) $this->_getParam('product_id')) &&
            null != ($sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id))) {
      Engine_Api::_()->core()->setSubject($sitestoreproduct);
    }
  }

  //ACTION TO BROWSE ALL TOPICS
  public function indexAction() {

    //RETURN IF PRODUCT SUBJECT IS NOT SET
    if (!$this->_helper->requireSubject('sitestoreproduct_product')->isValid())
      return;

    //GET PRODUCT SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject();

    //SEND THE TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GET PAGINATOR
    $this->view->paginator = Engine_Api::_()->getDbtable('topics', 'sitestoreproduct')->getProductTopices($sitestoreproduct->getIdentity());
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page'));

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();    
    
    //CAN POST DISCUSSION IF COMMENTING IS ALLOWED    
    $this->view->canPost = 0;
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);
    if ($isStoreAdmins || $this->_helper->requireAuth->setAuthParams('sitestoreproduct_product', null, "comment")->checkRequire()) {
      $this->view->canPost = $canPost = 1;
    }            
  }

  //ACTION TO VIEW TOPIC
  public function viewAction() {

    //RETURN IF TOPIC SUBJECT IS NOT SET
    if (!$this->_helper->requireSubject('sitestoreproduct_topic')->isValid())
      return;

    //GET VIEWER
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');
    //GET TOPIC  SUBJECT
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

    //GET SITESTOREPRODUCT OBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $topic->product_id);
    
    $this->view->canPost = $canPost = 0;
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);
    if ($isStoreAdmins || $sitestoreproduct->authorization()->isAllowed($viewer, "comment")) {
      $this->view->canPost = $canPost = 1;
    }        

    //INCREASE THE VIEW COUNT
    if (!$viewer || !$viewer_id || $viewer_id != $topic->user_id) {
      $topic->view_count = new Zend_Db_Expr('view_count + 1');
      $topic->save();
    }

    //CHECK WATHCHING
    $isWatching = null;
    if ($viewer->getIdentity()) {
      $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'sitestoreproduct');
      $isWatching = $topicWatchesTable->isWatching($sitestoreproduct->getIdentity(), $topic->getIdentity(), $viewer_id);
      if (false == $isWatching) {
        $isWatching = null;
      } else {
        $isWatching = (bool) $isWatching;
      }
    }
    $this->view->isWatching = $isWatching;

    //GET POST ID
    $this->view->post_id = $post_id = (int) $this->_getParam('post');

    $table = Engine_Api::_()->getDbtable('posts', 'sitestoreproduct');
    $select = $table->select()
            ->where('product_id = ?', $sitestoreproduct->getIdentity())
            ->where('topic_id = ?', $topic->getIdentity())
            ->order('creation_date ASC');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    //SKIP TO PAGE OF SPECIFIED POST
    if (0 != ($post_id = (int) $this->_getParam('post_id')) &&
            null != ($post = Engine_Api::_()->getItem('sitestoreproduct_post', $post_id))) {
      $icpp = $paginator->getItemCountPerPage();
      $page = ceil(($post->getPostIndex() + 1) / $icpp);
      $paginator->setCurrentPageNumber($page);
    }
    //USE SPECIFIED PAGE
    else if (0 != ($page = (int) $this->_getParam('page'))) {
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }

    if ($canPost && !$topic->closed) {
      $this->view->form = $form = new Sitestoreproduct_Form_Post_Create();
      $form->populate(array(
          'topic_id' => $topic->getIdentity(),
          'ref' => $topic->getHref(),
          'watch' => ( false == $isWatching ? '0' : '1' ),
      ));
    }
  }

  public function createAction() {

    //ONLY LOGGED IN USER CAN CREATE TOPIC
    if (!$this->_helper->requireUser()->isValid())
      return;

    //PRODUCT SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('sitestoreproduct_product')->isValid())
      return;
    $this->view->tab_selected_id = $this->_getParam('tab');
    
    //GET PRODUCT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');
    
    //GET VIEWER
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();    
    
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);
    if (!$isStoreAdmins && !$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "comment")->isValid()) {
      return;
    }    

    //MAKE FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Topic_Create();

    //CHECK METHOD/DATA
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['product_id'] = $sitestoreproduct->getIdentity();

    //GET TABLES
    $topicTable = Engine_Api::_()->getDbtable('topics', 'sitestoreproduct');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'sitestoreproduct');
    $postTable = Engine_Api::_()->getDbtable('posts', 'sitestoreproduct');

    $db = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getAdapter();
    $db->beginTransaction();

    try {
      //CREATE TOPIC
      $topic = $topicTable->createRow();
      $topic->setFromArray($values);
      $topic->save();

      //CREATE POST
      $values['topic_id'] = $topic->topic_id;

      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      //CREATE TOPIC WATCH
      $topicWatchesTable->insert(array(
          'resource_id' => $sitestoreproduct->getIdentity(),
          'topic_id' => $topic->getIdentity(),
          'user_id' => $viewer->getIdentity(),
          'watch' => (bool) $values['watch'],
      ));

      //ADD ACTIVITY      
      $store = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $isStoreAdmin = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer_id, $store->getIdentity());   
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');

        if($isStoreAdmin) {
          $activityType = 'sitestoreproduct_admin_topic_create';                
        }
        else {
          $activityType = 'sitestoreproduct_topic_create';
        }

        $action = $actionTable->addActivity(Engine_Api::_()->user()->getViewer(), $store, $activityType, null, array('topic_id' => $topic->getIdentity(), 'child_id' => $sitestoreproduct->getIdentity()));

        if ($action != null) {
          $actionTable->attachActivity($action, $topic);
        }
      }      

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECT TO THE TOPIC VIEW PAGE
    return $this->_helper->redirector->gotoUrl($topic->getHref(), array('prependBase' => false));
  }

  //ACTION FOR TOPIC POST
  public function postAction() {

    //LOGGED IN USER CAN POST
    if (!$this->_helper->requireUser()->isValid())
      return;

    //TOPIC SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('sitestoreproduct_topic')->isValid())
      return;

    //SEND THE TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');
    
    //GET TOPIC SUBJECT
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

    //GET SITESTOREPRODUCT OBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $topic->product_id);
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);
    if (!$isStoreAdmins && !$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "comment")->isValid()) {
      return;
    }        

    if ($topic->closed) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('This has been closed for posting.');
      return;
    }

    //MAKE FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Post_Create();

    //CHECK METHOD
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $topicOwner = $topic->getOwner();
    $isOwnTopic = $viewer->isSelf($topicOwner);

    $postTable = Engine_Api::_()->getDbtable('posts', 'sitestoreproduct');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'sitestoreproduct');
    $userTable = Engine_Api::_()->getItemTable('user');
    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['product_id'] = $sitestoreproduct->getIdentity();
    $values['topic_id'] = $topic->getIdentity();

    $watch = (bool) $values['watch'];
    $isWatching = $topicWatchesTable->isWatching($sitestoreproduct->getIdentity(), $topic->getIdentity(), $viewer->getIdentity());

    $db = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getAdapter();
    $db->beginTransaction();

    try {

      //CREATE POST
      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      //WATCH
      if (false == $isWatching) {
        $topicWatchesTable->insert(array(
            'resource_id' => $sitestoreproduct->getIdentity(),
            'topic_id' => $topic->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'watch' => (bool) $watch,
        ));
      } else if ($watch != $isWatching) {
        $topicWatchesTable->update(array(
            'watch' => (bool) $watch,
                ), array(
            'resource_id = ?' => $sitestoreproduct->getIdentity(),
            'topic_id = ?' => $topic->getIdentity(),
            'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      $store = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
      $viewer_id = $viewer->getIdentity();
      $isStoreAdmin = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer_id, $store->getIdentity());      
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');

        if($isStoreAdmin) {
          $activityType = 'sitestoreproduct_admin_topic_reply';                
        }
        else {
          $activityType = 'sitestoreproduct_topic_reply';
        }

        $action = $actionTable->addActivity(Engine_Api::_()->user()->getViewer(), $store, $activityType, null, array('topic_id' => $topic->getIdentity(), 'child_id' => $sitestoreproduct->getIdentity()));

        if ($action != null) {
          $actionTable->attachActivity($action, $post);
        }
      }    

      //NOTIFICATIONS
      $notifyUserIds = $topicWatchesTable->getNotifyUserIds($values);

      foreach ($userTable->find($notifyUserIds) as $notifyUser) {

        //DONT NOTIFY SELF
        if ($notifyUser->isSelf($viewer)) {
          continue;
        }

        if ($notifyUser->isSelf($topicOwner)) {
          $type = 'sitestoreproduct_discussion_response';
        } else {
          $type = 'sitestoreproduct_discussion_reply';
        }

        $notifyApi->addNotification($notifyUser, $viewer, $topic, $type, array(
            'message' => $this->view->BBCode($post->body),
        ));
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECT TO THE TOPIC VIEW PAGE
    return $this->_helper->redirector->gotoUrl($topic->getHref(), array('prependBase' => false));
  }

  //ACTION FOR MAKE STICKY
  public function stickyAction() {

    //TOPIC SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('sitestoreproduct_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();
    
    $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $topic->product_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);
    if (!$isStoreAdmins && !$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "edit")->isValid()) {
      return;
    }          

    //GET TOPIC TABLE
    $table = Engine_Api::_()->getDbTable('topics', 'sitestoreproduct');
    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
      $topic = Engine_Api::_()->core()->getSubject();
      $topic->sticky = ( null == $this->_getParam('sticky') ? !$topic->sticky : (bool) $this->_getParam('sticky') );
      $topic->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirectCustom($topic);
  }

  //ACTINO FOR CLOSING THE TOPIC
  public function closeAction() {

    //TOPIC SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('sitestoreproduct_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();
    
    $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $topic->product_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);
    if (!$isStoreAdmins && !$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "edit")->isValid()) {
      return;
    }      

    //GET TOPIC TABLE
    $table = Engine_Api::_()->getDbTable('topics', 'sitestoreproduct');
    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
      $topic = Engine_Api::_()->core()->getSubject();
      $topic->closed = ( null == $this->_getParam('closed') ? !$topic->closed : (bool) $this->_getParam('closed') );
      $topic->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirectCustom($topic);
  }

  //ACTION FOR RENAME THE TOPIC
  public function renameAction() {

    //TOPIC SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('sitestoreproduct_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();
    
    $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $topic->product_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);
    if (!$isStoreAdmins && !$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "edit")->isValid()) {
      return;
    }      

    //GET FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Topic_Rename();

    //CHECK METHOD
    if (!$this->getRequest()->isPost()) {
      $form->title->setValue(htmlspecialchars_decode($topic->title));
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET TOPIC TABLE
    $table = Engine_Api::_()->getDbTable('topics', 'sitestoreproduct');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $title = htmlspecialchars($form->getValue('title'));
      $topic = Engine_Api::_()->core()->getSubject();
      $topic->title = $title;
      $topic->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Topic renamed.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
  }

  //ACTION FOR DELETING THE TOPIC
  public function deleteAction() {

    //TOPIC SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject('sitestoreproduct_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();
    
    $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $topic->product_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);
    if (!$isStoreAdmins && !$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "edit")->isValid()) {
      return;
    }      

    //MAKE FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Topic_Delete();

    //CHECK POST
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET TOPIC TABLE
    $table = Engine_Api::_()->getDbTable('topics', 'sitestoreproduct');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $topic = Engine_Api::_()->core()->getSubject();
      $sitestoreproduct = $topic->getParent('sitestoreproduct_product');
      $topic->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Topic deleted.')),
                'layout' => 'default-simple',
                'parentRedirect' => $sitestoreproduct->getHref(),
            ));
  }

  //ACTION FOR TOPIC WATCH
  public function watchAction() {

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //GET SITESTOREPRODUCT OBJECT
    $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $topic->product_id);

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    $watch = $this->_getParam('watch', true);
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'sitestoreproduct');
    $db = $topicWatchesTable->getAdapter();
    $db->beginTransaction();
    try {
      $resultWatch = $topicWatchesTable
              ->select()
              ->from($topicWatchesTable->info('name'), 'watch')
              ->where('resource_id = ?', $sitestoreproduct->getIdentity())
              ->where('topic_id = ?', $topic->getIdentity())
              ->where('user_id = ?', $viewer->getIdentity())
              ->limit(1)
              ->query()
              ->fetchAll();
      if (empty($resultWatch))
        $isWatching = 0;
      else
        $isWatching = 1;

      if (false == $isWatching) {
        $topicWatchesTable->insert(array(
            'resource_id' => $sitestoreproduct->getIdentity(),
            'topic_id' => $topic->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'watch' => (bool) $watch,
        ));
      } else {
        $topicWatchesTable->update(array(
            'watch' => (bool) $watch,
                ), array(
            'resource_id = ?' => $sitestoreproduct->getIdentity(),
            'topic_id = ?' => $topic->getIdentity(),
            'user_id = ?' => $viewer->getIdentity(),
        ));
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirectCustom($topic);
  }

}