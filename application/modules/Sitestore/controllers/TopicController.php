<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TopicController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_TopicController extends Seaocore_Controller_Action_Standard {

  public function init() {

    //GET STORE ID
    $store_id = $this->_getParam('store_id');

    //PACKAGE BASE PRIYACY START
    if (!empty($store_id)) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorediscussion")) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sdicreate');
        if (empty($isStoreOwnerAllow)) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if (0 !== ($topic_id = (int) $this->_getParam('topic_id'))) {
        $topic = Engine_Api::_()->getItem('sitestore_topic', $topic_id);
        $store_id = $topic->store_id;
      }
      if (Engine_Api::_()->core()->hasSubject('sitestore_store') != null) {
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
        if (!empty($sitestore))
          $store_id = $sitestore->store_id;
      }
    }

    if (Engine_Api::_()->core()->hasSubject())
      return;

    if (0 !== ($topic_id = (int) $this->_getParam('topic_id')) &&
            null !== ($topic = Engine_Api::_()->getItem('sitestore_topic', $topic_id))) {
      Engine_Api::_()->core()->setSubject($topic);
    } else if (0 !== ($store_id = (int) $this->_getParam('store_id')) &&
            null !== ($sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id))) {
      Engine_Api::_()->core()->setSubject($sitestore);
    }
  }

  //ACTION FOR SHOWING THE TOPIC
  public function indexAction() {

    //CHECK SITESTORE SUBJECT IS VALID OR NOT
    if (!$this->_helper->requireSubject('sitestore_store')->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    //GET SITESTORE SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject();

    //SEND THE TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('topics', 'sitestore')->getStoreTopics($sitestore->getIdentity());
    $paginator->setCurrentPageNumber($this->_getParam('page'));
  }

  //ACTION FOR VIEW THE TOPIC
  public function viewAction() {

    //CHECK SITESTORE SUBJECT IS VALID OR NOT
    if (!$this->_helper->requireSubject('sitestore_topic')->isValid())
      return;
    
    //SEND THE TOPIC SUBJECT TO THE TPL
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

    //GET THE SITESTORE ITEM 
    $this->view->sitestore = $sitestore = $topic->getParentSitestore();    

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    }    

  }

  //ACTION FOR CREATING THE TOPIC, POST 
  public function createAction() {

    //USER VALIDATION REQURIED
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK SITESTORE SUBJECT IS VALID OR NOT
    if (!$this->_helper->requireSubject('sitestore_store')->isValid())
      return;

    //GET SITESTORE SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdicreate');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //GET LOGGED USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    //MAKE FORM
    $this->view->form = $form = new Sitestore_Form_Topic_Create();

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');
    $this->view->resource_type = $this->_getParam('resource_type', null);
    $this->view->resource_id = $this->_getParam('resource_id', 0);
    //CHECK METHOD / DATA
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['store_id'] = $store_id = $sitestore->getIdentity();

    $topicTable = Engine_Api::_()->getDbtable('topics', 'sitestore');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitestore');
    $postTable = Engine_Api::_()->getDbtable('posts', 'sitestore');

    //GET DB
    $db = $sitestore->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //CREATE TOPIC
      $values['resource_type'] = $this->_getParam('resource_type', null);
      $values['resource_id'] = $this->_getParam('resource_id', 0);
      $topic = $topicTable->createRow();
      $topic->setFromArray($values);
      $topic->view_count = 1;
      $topic->save();
      $values['topic_id'] = $topic->topic_id;

      //CREATE POST
      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      $topicWatchesTable->insert(array(
          'resource_id' => $sitestore->getIdentity(),
          'topic_id' => $topic->getIdentity(),
          'user_id' => $viewer->getIdentity(),
          'watch' => (bool) $values['watch'],
          'store_id' => $store_id,
      ));

      //ADD ACTIVITY
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $activityFeedType = null;
      if (Engine_Api::_()->sitestore()->isStoreOwner($sitestore) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
        $activityFeedType = 'sitestore_admin_topic_create';
      elseif ($sitestore->all_post || Engine_Api::_()->sitestore()->isStoreOwner($sitestore))
        $activityFeedType = 'sitestore_topic_create';


      if ($activityFeedType) {
        $action = $activityApi->addActivity($viewer, $sitestore, $activityFeedType);
        Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
      }
      if ($action) {
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $topic);
        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {
          $topiccreate_array = array();
          $topiccreate_array['type'] = 'sitestore_topic_create';
          $topiccreate_array['object'] = $topic;
          $topiccreate_array['description'] = $values['body'];
          Engine_Api::_()->facebooksefeed()->sendFacebookFeed($topiccreate_array);
        }
      }

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECT TO THE TOPIC VIEW STORE
    return $this->_redirectCustom($topic->getHref(array('tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab'))), array('prependBase' => false));
  }

  //ACTION FOR SENDING THE POST 
  public function postAction() {

    //USER VALIDATION REQURIED
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitestore_topic')->isValid())
      return;

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GET TOPIC SUBJECT
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

    //GET SITESTORE SUBJECT ADN STORE ID
    $this->view->sitestore = $sitestore = $topic->getParentSitestore();
    $store_id = $sitestore->store_id;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdicreate');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    if ($topic->closed) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('This topic is closed for posting.');
      return;
    }
    //MAKE FORM
    $this->view->form = $form = new Sitestore_Form_Post_Create();

    $quote_id = $this->getRequest()->getParam('quote_id');
    if( !empty($quote_id) ) {
      $quote = Engine_Api::_()->getItem('sitestore_post', $quote_id);
      if($quote->user_id == 0) {
          $owner_name = Zend_Registry::get('Zend_Translate')->_('Deleted Member');
      } else {
          $owner_name = $quote->getOwner()->__toString();
      }

			$form->body->setValue("<blockquote><strong>" . $this->view->translate('%1$s said:', $owner_name) . "</strong><br />" . $quote->body . "</blockquote><br />");

    }

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    //PROCESS
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET TOPIC OWNER
    $topicOwner = $topic->getOwner();

    //SELF CREATED TOPIC OR NOT
    $isOwnTopic = $viewer->isSelf($topicOwner);

    //GET POST TABLE
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitestore');

    //GET FORM VALUES
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['store_id'] = $sitestore->getIdentity();
    $values['topic_id'] = $topic->getIdentity();

    $watch = (bool) $values['watch'];
    $isWatching = $topicWatchesTable->isWatching($sitestore->getIdentity(), $topic->getIdentity(), $viewer->getIdentity());

    //GET DB
    $db = $sitestore->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //CREATE POST
      $post = Engine_Api::_()->getDbtable('posts', 'sitestore')->createRow();
      $post->setFromArray($values);
      $post->save();

      //WATCH
      if (false === $isWatching) {
        $topicWatchesTable->insert(array(
            'resource_id' => $sitestore->getIdentity(),
            'topic_id' => $topic->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'watch' => (bool) $watch,
            'store_id' => $values['store_id'],
        ));
      } else if ($watch != $isWatching) {
        $topicWatchesTable->update(array(
            'watch' => (bool) $watch,
            'store_id' => $values['store_id'],
                ), array(
            'resource_id = ?' => $sitestore->getIdentity(),
            'topic_id = ?' => $topic->getIdentity(),
            'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      //ADD ACTIVITY
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $activityFeedType = null;
      if (Engine_Api::_()->sitestore()->isStoreOwner($sitestore) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
        $activityFeedType = 'sitestore_admin_topic_reply';
      elseif ($sitestore->all_post || Engine_Api::_()->sitestore()->isStoreOwner($sitestore))
        $activityFeedType = 'sitestore_topic_reply';


//      if ($activityFeedType) {
//        $action = $activityApi->addActivity($viewer, $sitestore, $activityFeedType);
//        Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
//      }
      //ACTIVITY      
      if ($activityFeedType) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitestore, $activityFeedType);
        Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
        if (!empty($action))
          $action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);

        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {
          $topicreply_array = array();
          $topicreply_array['type'] = 'sitestore_topic_reply';
          $topicreply_array['object'] = $topic;
          $topicreply_array['description'] = $values['body'];
          Engine_Api::_()->facebooksefeed()->sendFacebookFeed($topicreply_array);
        }
      }

      //NOTIFICATIONS
      $notifyUserIds = $topicWatchesTable->getNotifyUserIds($values);

      foreach (Engine_Api::_()->getItemTable('user')->find($notifyUserIds) as $notifyUser) {
        //DON'T NOTIFY SELF
        if ($notifyUser->isSelf($viewer)) {
          continue;
        }

        if ($notifyUser->isSelf($topicOwner)) {
          $type = 'sitestore_discussion_response';
        } else {
          $type = 'sitestore_discussion_reply';
        }

        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($notifyUser, $viewer, $topic, $type, array(
            'message' => $this->view->BBCode($post->body),
        ));
      }

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECT TO THE POST STORE
    $this->_redirectCustom($post);
  }

  //ACTION FOR STICKY THE TOPIC 
  public function stickyAction() {

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitestore_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject('sitestore_topic');

    //START MANAGE-ADMIN CHECK
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $topic->store_id);
    $can_edit = $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

    //CHECKING WHETHER THE USER HAVE THE PERMISSION OR NOT.
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET DB
    $db = $topic->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE STICKY
      $topic->sticky = ( null === $this->_getParam('sticky') ? !$topic->sticky : (bool) $this->_getParam('sticky') );
      $topic->save();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    //REDIRECT TO THE TOPIC VIEW STORE
    $this->_redirectCustom($topic);
  }

  //ACTION FOR CLOSE THE TOPIC 
  public function closeAction() {

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitestore_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $topic->store_id);

    //START MANAGE-ADMIN CHECK
    $can_edit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET DB
    $db = $topic->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE TOPIC CLOSED
      $topic->closed = ( null === $this->_getParam('closed') ? !$topic->closed : (bool) $this->_getParam('closed') );
      $topic->save();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    //REDIRECT TO THE TOPIC VIEW STORE
    $this->_redirectCustom($topic);
  }

  //ACTION FOR RENAME THE TOPIC 
  public function renameAction() {

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitestore_topic')->isValid())
      return;

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $topic->store_id);

    //START MANAGE-ADMIN CHECK
    $can_edit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //MAKE FORM
    $this->view->form = $form = new Sitestore_Form_Topic_Rename();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $form->title->setValue(htmlspecialchars_decode($topic->title));
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET DB
    $db = $topic->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE TOPIC TITLE
      $topic->title = htmlspecialchars($form->getValue('title'));
      ;
      $topic->save();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECTING
    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected topic has been successfully renamed.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
  }

  //ACTION FOR DELETE THE TOPIC 
  public function deleteAction() {

    //CHECK TOPIC SUBJECT IS SET OR NOT  
    if (!$this->_helper->requireSubject('sitestore_topic')->isValid())
      return;

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //START MANAGE-ADMIN CHECK
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $topic->store_id);
    $can_edit = $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if ($can_edit != 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != $topic->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //MAKE FORM
    $this->view->form = $form = new Sitestore_Form_Topic_Delete();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET DB
    $db = $topic->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //GET STORE ID
      $store_id = $topic->store_id;

      //DELETE TOPIC
      $topic->delete();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECTING
    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The selected topic has been deleted.')),
                'layout' => 'default-simple',
                'parentRedirect' => $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id), 'tab' => $this->view->tab_selected_id), 'sitestore_entry_view'),
            ));
  }

  //ACTION FOR WATCH THE TOPIC 
  public function watchAction() {

    //GET TOPIC SUBJECT
    $topic = Engine_Api::_()->core()->getSubject();

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $topic->store_id);

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET WATCH PARAM
    $watch = $this->_getParam('watch', true);

    //GET TOPIC WATCH TABLE
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitestore');

    //GET DB
    $db = $topicWatchesTable->getAdapter();
    $db->beginTransaction();
    try {
      $isWatching = $topicWatchesTable->isWatching($sitestore->getIdentity(), $topic->getIdentity(), $viewer->getIdentity());
      if (false === $isWatching) {
        $topicWatchesTable->insert(array(
            'resource_id' => $sitestore->getIdentity(),
            'topic_id' => $topic->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'watch' => (bool) $watch,
            'store_id' => $topic->store_id,
        ));
      } else {
        $topicWatchesTable->update(array(
            'watch' => (bool) $watch,
            'store_id' => $topic->store_id,
                ), array(
            'resource_id = ?' => $sitestore->getIdentity(),
            'topic_id = ?' => $topic->getIdentity(),
            'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirectCustom($topic);
  }

}

?>