<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WishlistController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_WishlistController extends Seaocore_Controller_Action_Standard {

  //COMMON FUNCTION WHICH CALL AUTOMATICALLY BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {
    //AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
      return;

    if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
      return;

    $product_id = $this->_getParam('product_id');
    if (!empty($product_id)) {

      //AUTHORIZATION CHECK
      if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
        return;

      if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
        return;
    }

    $sitestoreproductWishlistView = Zend_Registry::isRegistered('sitestoreproductWishlistView') ? Zend_Registry::get('sitestoreproductWishlistView') : null;
    if (empty($sitestoreproductWishlistView))
      $this->_setParam('product_id', 0);

    //AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_wishlist', null, "view")->isValid())
      return;
  }

  //NONE USER SPECIFIC METHODS
  public function browseAction() {

    //GET PAGE OBJECT
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pageSelect = $pageTable->select()->where('name = ?', "sitestoreproduct_wishlist_browse");
    $pageObject = $pageTable->fetchRow($pageSelect);

    //GET SEARCH TEXT
    if ($this->_getParam('search', null)) {
      $metaParams['search'] = $this->_getParam('search', null);

      //SET META KEYWORDS
      Engine_Api::_()->sitestoreproduct()->setMetaKeywords($metaParams);
    }

    if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $this->_helper->content
              ->setContentName($pageObject->page_id)
              ->setNoRender()
              ->setEnabled();
    }else{
         $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    } 
  }

  public function myWishlistsAction() {

    //GET CONTENT TABLE
    $tableContent = Engine_Api::_()->getDbtable('content', 'core');
    $tableContentName = $tableContent->info('name');

    //GET PAGE TABLE
    $tablePage = Engine_Api::_()->getDbtable('pages', 'core');
    $tablePageName = $tablePage->info('name');

    $page_id = $tablePage->select()
            ->from($tablePageName, array('page_id'))
            ->where('name = ?', "sitestoreproduct_wishlist_browse")
            ->query()
            ->fetchColumn();

    if (!empty($page_id)) {
      $params = $tableContent->select()
              ->from($tableContent->info('name'), array('params'))
              ->where('page_id = ?', $page_id)
              ->where('name = ?', 'sitestoreproduct.wishlist-browse')
              ->query()
              ->fetchColumn();

      $content_id = $tableContent->select()
              ->from($tableContent->info('name'), array('content_id'))
              ->where('page_id = ?', $page_id)
              ->where('name = ?', 'sitestoreproduct.wishlist-browse')
              ->query()
              ->fetchColumn();
      $this->view->params = array();
      if (isset($params) && !empty($params)) {
        $this->view->params = Zend_Json_Decoder::decode($params);
      }
      $this->view->params['content_id'] = $content_id;
      $this->view->params['hide_follow'] = $this->_getParam('hide_follow', 0);
      $this->view->params['from_my_store_account'] = 1;
    }
    
    if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
         $this->_helper->content
              //->setNoRender()
              ->setEnabled();
    } 
  }

  //NONE USER SPECIFIC METHODS
  public function profileAction() {

    //GET WISHLIST ID AND OBJECT
    $wishlist_id = $this->_getParam('wishlist_id');
    $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);

    //SET SITESTOREPRODUCT SUBJECT
    Engine_Api::_()->core()->setSubject($wishlist);

    //GET PAGE OBJECT
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pageSelect = $pageTable->select()->where('name = ?', "sitestoreproduct_wishlist_profile");
    $pageObject = $pageTable->fetchRow($pageSelect);

    $params['wishlist'] = 'Wishlists';
    Engine_Api::_()->sitestoreproduct()->setMetaTitles($params);

    $params['wishlist_creator_name'] = $wishlist->getOwner()->getTitle();
    Engine_Api::_()->sitestoreproduct()->setMetaKeywords($params);

    //CHECK AUTHENTICATION
    if (!Engine_Api::_()->authorization()->isAllowed($wishlist, null, "view")) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //INCREASE VIEW COUNT IF VIEWER IS NOT OWNER
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$wishlist->getOwner()->isSelf($viewer)) {
      $wishlist->view_count++;
      $wishlist->save();
    }

    if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $this->_helper->content
              ->setContentName($pageObject->page_id)
              ->setNoRender()
              ->setEnabled();
    }else{
         $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    } 
  }

  //ACTION FOR ADDING THE ITEM IN WISHLIST
  public function addAction() {

    $param = $this->_getParam('param');
    $request_url = $this->_getParam('request_url');
    $return_url = $this->_getParam('return_url');
    $front = Zend_Controller_Front::getInstance();
    $base_url = $front->getBaseUrl();

    // CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid()) {
      $host = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
      if ($base_url == '') {
        $URL_Home = $host . $_SERVER['HTTP_HOST'] . '/login';
      } else {
        $URL_Home = $host . $_SERVER['HTTP_HOST'] . '/' . $request_url . '/login';
      }
      if (empty($param)) {
        return $this->_helper->redirector->gotoUrl($URL_Home, array('prependBase' => false));
      } else {
        return $this->_helper->redirector->gotoUrl($URL_Home . '?return_url=' . urlencode($return_url), array('prependBase' => false));
      }
    }

    //SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //ONLY LOGGED IN USER CAN CREATE
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CREATION PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_wishlist', null, "create")->isValid())
      return;

    //GET PAGE ID AND CHECK PAGE ID VALIDATION
    $product_id = $this->_getParam('product_id');
    if (empty($product_id)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET USER WISHLISTS
    $wishlistTable = Engine_Api::_()->getDbtable('wishlists', 'sitestoreproduct');
    $wishlistDatas = $wishlistTable->getUserWishlists($viewer_id);
    $this->view->wishlistDatasCount = $wishlistDataCount = Count($wishlistDatas);

    //LISING WILL ADD IF YOU CAN VIEW THIS
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $this->_getParam('product_id'));

    $this->view->can_add = 1;
    if (!$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, null, "view")->isValid()) {
      $this->view->can_add = 0;
    }

    //AUTHORIZATION CHECK
    if (!empty($sitestoreproduct->draft) || empty($sitestoreproduct->search) || empty($sitestoreproduct->approved)) {
      $this->view->can_add = 0;
    }

    //FORM GENERATION
    $this->view->form = $form = new Sitestoreproduct_Form_Wishlist_Add();

    $this->view->success = 0;

    //FORM VALIDATION
    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET FORM VALUES
    $values = $form->getValues();

    //CHECK FOR NEW ADDED WISHLIST TITLE
    if (!empty($values['body']) && empty($values['title'])) {

      $error = $this->view->translate('Please enter the wishlist title otherwise remove the wishlist note.');
      $this->view->status = false;
      $error = Zend_Registry::get('Zend_Translate')->_($error);
      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error);
      return;
    }

    //GET WISHLIST PAGE TABLE
    $wishlistProductTable = Engine_Api::_()->getDbtable('wishlistmaps', 'sitestoreproduct');

    $wishlistOldIds = array();

    //GET FOLLOW TABLE
    $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');

    //GET NOTIFY API
    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

    //WORK ON PREVIOUSLY CREATED WISHLIST
    foreach ($wishlistDatas as $wishlistData) {
      $key_name = 'wishlist_' . $wishlistData->wishlist_id;
      if (isset($values[$key_name]) && !empty($values[$key_name])) {

        $wishlistProductTable->insert(array(
            'wishlist_id' => $wishlistData->wishlist_id,
            'product_id' => $product_id,
        ));

        //WISHLIST COVER PHOTO
        $wishlistTable->update(
                array(
            'product_id' => $product_id,
                ), array(
            'wishlist_id = ?' => $wishlistData->wishlist_id,
            'product_id = ?' => 0
                )
        );

        //GET FOLLOWERS
        $followers = $followTable->getFollowers('sitestoreproduct_wishlist', $wishlistData->wishlist_id, $viewer_id);
        foreach ($followers as $follower) {
          $followerObject = Engine_Api::_()->getItem('user', $follower->poster_id);
          $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlistData->wishlist_id);
          $http = _ENGINE_SSL ? 'https://' : 'http://';
          $wishlist_link = '<a href="' . $http . $_SERVER['HTTP_HOST'] . '/' . $wishlist->getHref() . '">' . $wishlist->getTitle() . '</a>';
          $notifyApi->addNotification($followerObject, $viewer, $sitestoreproduct, 'sitestoreproduct_wishlist_followers', array("wishlist" => $wishlist_link));
        }

        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($viewer, $wishlistData, "sitestoreproduct_wishlist_add_product", '', array('product' => array($sitestoreproduct->getType(), $sitestoreproduct->getIdentity()),
        ));
        if ($action)
          $activityApi->attachActivity($action, $sitestoreproduct);
      }

      $in_key_name = 'inWishlist_' . $wishlistData->wishlist_id;
      if (isset($values[$in_key_name]) && empty($values[$in_key_name])) {
        $wishlistOldIds[$wishlistData->wishlist_id] = $wishlistData;
        $wishlistProductTable->delete(array('wishlist_id = ?' => $wishlistData->wishlist_id, 'product_id= ?' => $product_id));

        //DELETE ACTIVITY FEED
        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $actionTableName = $actionTable->info('name');

        $action_id = $actionTable->select()
                ->setIntegrityCheck(false)
                ->from($actionTableName, 'action_id')
                ->joinInner('engine4_activity_attachments', "engine4_activity_attachments.action_id = $actionTableName.action_id", array())
                ->where('engine4_activity_attachments.id = ?', $product_id)
                ->where($actionTableName . '.type = ?', "sitestoreproduct_wishlist_add_listing")
                ->where($actionTableName . '.subject_type = ?', 'user')
                ->where($actionTableName . '.object_type = ?', 'sitestoreproduct_wishlist')
                ->where($actionTableName . '.object_id = ?', $wishlistData->wishlist_id)
                ->query()
                ->fetchColumn();

        if (!empty($action_id)) {
          $activity = Engine_Api::_()->getItem('activity_action', $action_id);
          if (!empty($activity)) {
            $activity->delete();
          }
        }
      }
    }


    if (!empty($values['title'])) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

        //CREATE WISHLIST
        $wishlist = $wishlistTable->createRow();
        $wishlist->setFromArray($values);
        $wishlist->owner_id = $viewer_id;
        $wishlist->product_id = $product_id; //WISHLIST COVER PHOTO
        $wishlist->save();

        //PRIVACY WORK
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        if (empty($values['auth_view'])) {
          $values['auth_view'] = array('everyone');
        }

        $viewMax = array_search($values['auth_view'], $roles);
        foreach ($roles as $i => $role) {
          $auth->setAllowed($wishlist, $role, 'view', ($i <= $viewMax));
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }

      $wishlistProductTable->insert(array(
          'wishlist_id' => $wishlist->wishlist_id,
          'product_id' => $product_id,
          'date' => new Zend_Db_Expr('NOW()')
      ));

      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $wishlist, "sitestoreproduct_wishlist_add_product", '', array('product' => array($sitestoreproduct->getType(), $sitestoreproduct->getIdentity()),
      ));
      if ($action)
        $activityApi->attachActivity($action, $sitestoreproduct);
    }

    $this->view->wishlistOldDatas = $wishlistOldIds;
    $this->view->wishlistNewDatas = $wishlistProductTable->pageWishlists($product_id, $viewer_id);
    $this->view->success = 1;
    
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      //$this->view->notSuccessMessage=true;
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Add successfully.'))
              ));
    }
  }

  //ACTION FOR MESSAGING THE PRODUCT OWNER
  public function messageOwnerAction() {

    //LOGGED IN USER CAN SEND THE MESSAGE
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET PRODUCT ID AND OBJECT
    $wishlist_id = $this->_getParam("wishlist_id");
    $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);

    //OWNER CANT SEND A MESSAGE TO HIMSELF
    if ($viewer_id == $wishlist->owner_id) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //MAKE FORM
    $this->view->form = $form = new Messages_Form_Compose();
    $form->setDescription('Create your message with the form given below. (This message will be sent to the owner of this Wishlist.)');
    $form->removeElement('to');
    $form->toValues->setValue("$wishlist->owner_id");
    
    
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
    $form->removeElement('toValues');
    }
    //CHECK METHOD/DATA
    if (!$this->getRequest()->isPost()) {
      return;
    }

    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      $values = $this->getRequest()->getPost();

      $form->populate($values);

      $is_error = 0;
      if (empty($values['title'])) {
        $is_error = 1;
      }

      //SENDING MESSAGE
      if ($is_error == 1) {
        $error = $this->view->translate('Subject is required field !');
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      $recipients = preg_split('/[,. ]+/', $values['toValues']);

      //LIMIT RECIPIENTS
      $recipients = array_slice($recipients, 0, 1000);

      //CLEAN THE RECIPIENTS FOR REPEATING IDS
      $recipients = array_unique($recipients);

      //GET USER
      $user = Engine_Api::_()->getItem('user', $wishlist->owner_id);

      $wishlist_title = $wishlist->getTitle();
      $wishlist_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('wishlist_id' => $wishlist_id, 'slug' => $wishlist->getSlug()), "sitestoreproduct_wishlist_view") . ">$wishlist_title</a>";

      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->view->translate('This message corresponds to the Wishlist: ') . $wishlist_title_with_link);

      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');

      //INCREMENT MESSAGE COUNTER
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      $db->commit();

      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR REMOVE PRODUCT FROM THIS WISHLIST
  public function removeAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //GET WISHLIST AND PAGE ID 
    $this->view->wishlist_id = $wishlist_id = $this->_getParam('wishlist_id');
    $product_id = $this->_getParam('product_id');

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //DELETE FROM DATABASE
        Engine_Api::_()->getDbtable('wishlistmaps', 'sitestoreproduct')->delete(array('wishlist_id = ?' => $wishlist_id, 'product_id = ?' => $product_id));

        //DELETE ACTIVITY FEED
        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $actionTableName = $actionTable->info('name');

        $action_id = $actionTable->select()
                ->setIntegrityCheck(false)
                ->from($actionTableName, 'action_id')
                ->joinInner('engine4_activity_attachments', "engine4_activity_attachments.action_id = $actionTableName.action_id", array())
                ->where('engine4_activity_attachments.id = ?', $product_id)
                ->where($actionTableName . '.type = ?', "sitestoreproduct_wishlist_add_listing")
                ->where($actionTableName . '.subject_type = ?', 'user')
                ->where($actionTableName . '.object_type = ?', 'sitestoreproduct_wishlist')
                ->where($actionTableName . '.object_id = ?', $wishlist_id)
                ->query()
                ->fetchColumn();

        if (!empty($action_id)) {
          $activity = Engine_Api::_()->getItem('activity_action', $action_id);
          if (!empty($activity)) {
            $activity->delete();
          }
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('This product has been removed successfully from this wishlist!'))
      ));
    }
    $this->renderScript('wishlist/remove.tpl');
  }

  //ACTION FOR TELL TO THE FRIEND FOR THIS WISHLIST
  public function tellAFriendAction() {

    //DEFAULT LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewr_id = $viewer->getIdentity();

    //GET WISHLIST ID AND WISHLIST OBJECT
    $wishlist_id = $this->_getParam('wishlist_id', $this->_getParam('wishlist_id', null));
    $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);

    //FORM GENERATION
    $this->view->form = $form = new Sitestoreproduct_Form_Wishlist_TellAFriend();

    if (!empty($viewr_id)) {
      $value['wishlist_sender_email'] = $viewer->email;
      $value['wishlist_sender_name'] = $viewer->displayname;
      $form->populate($value);
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //EMAIL IDS
      $reciver_ids = explode(',', $values['wishlist_reciver_emails']);

      if (!empty($values['wishlist_send_me'])) {
        $reciver_ids[] = $values['wishlist_sender_email'];
      }

      $reciver_ids = array_unique($reciver_ids);

      $sender_email = $values['wishlist_sender_email'];

      //CHECK VALID EMAIL ID FORMITE
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);

      if (!$validator->isValid($sender_email)) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
        return;
      }
      foreach ($reciver_ids as $reciver_id) {
        $reciver_id = trim($reciver_id, ' ');
        if (!$validator->isValid($reciver_id)) {
          $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
          return;
        }
      }

      //GET EMAIL DETAILS
      $sender = $values['wishlist_sender_name'];
      $message = $values['wishlist_message'];
      $params['wishlist_id'] = $wishlist_id;
      $params['slug'] = $wishlist->getSlug();
      $heading = ucfirst($wishlist->getTitle());

      Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITESTOREPRODUCT_WISHLIST_TELLAFRIEND_EMAIL', array(
          'host' => $_SERVER['HTTP_HOST'],
          'sender_name' => $sender,
          'wishlist_title' => $heading,
          'message' => '<div>' . $message . '</div>',
          'object_link' => $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble($params, 'sitestoreproduct_wishlist_view', true),
          'sender_email' => $sender_email,
          'queue' => true
      ));

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => false,
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.'))
      ));
    }
  }

  //ACTION FOR CREATING THE WISHLIST
  public function createAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //ONLY LOGGED IN USER CAN CREATE
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CREATION PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_wishlist', null, "create")->isValid())
      return;

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //FORM GENERATION
    $this->view->form = $form = new Sitestoreproduct_Form_Wishlist_Create();

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET WISHLIST TABLE
    $wishlistTable = Engine_Api::_()->getItemTable('sitestoreproduct_wishlist');
    $db = $wishlistTable->getAdapter();
    $db->beginTransaction();

    try {

      //GET FORM VALUES
      $values = $form->getValues();
      $values['owner_id'] = $viewer->getIdentity();

      //CREATE WISHLIST
      $wishlist = $wishlistTable->createRow();
      $wishlist->setFromArray($values);
      $wishlist->save();

      //PRIVACY WORK
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if (empty($values['auth_view'])) {
        $values['auth_view'] = array('everyone');
      }
      $viewMax = array_search($values['auth_view'], $roles);

      $values['auth_comment'] = array('everyone');
      $commentMax = array_search($values['auth_comment'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($wishlist, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($wishlist, $role, 'comment', ($i <= $commentMax));
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      throw $e;
    }

    //GET URL
    $url = $this->_helper->url->url(array('wishlist_id' => $wishlist->wishlist_id, 'slug' => $wishlist->getSlug()), "sitestoreproduct_wishlist_view", true);

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'smoothboxClose' => 10,
        'parentRedirect' => $url,
        'parentRedirectTime' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your wishlist has been created successfully.'))
    ));
  }

  //ACTION FOR EDIT WISHLIST
  public function editAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //ONLY LOGGED IN USER CAN CREATE
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET WISHLIST ID AND CHECK VALIDATION
    $wishlist_id = $this->_getParam('wishlist_id');
    if (empty($wishlist_id)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //GET WISHLIST OBJECT
    $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $level_id = $viewer->level_id;

    if ($level_id != 1 && $wishlist->owner_id != $viewer_id) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //FORM GENERATION
    $this->view->form = $form = new Sitestoreproduct_Form_Wishlist_Edit();

    if (!$this->getRequest()->isPost()) {

      //PRIVACY WORK
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      $perms = array();
      foreach ($roles as $roleString) {
        $role = $roleString;
        if ($auth->isAllowed($wishlist, $role, 'view')) {
          $perms['auth_view'] = $roleString;
        }
      }

      $form->populate($wishlist->toArray());
      $form->populate($perms);
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $db = Engine_Api::_()->getItemTable('sitestoreproduct_wishlist')->getAdapter();
    $db->beginTransaction();

    try {

      //GET FORM VALUES
      $values = $form->getValues();

      //SAVE DATA
      $wishlist->setFromArray($values);
      $wishlist->save();

      //PRIVACTY WORK
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if (empty($values['auth_view'])) {
        $values['auth_view'] = array('everyone');
      }

      $viewMax = array_search($values['auth_view'], $roles);
      foreach ($roles as $i => $role) {
        $auth->setAllowed($wishlist, $role, 'view', ($i <= $viewMax));
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //GET URL
    $url = $this->_helper->url->url(array('wishlist_id' => $wishlist->wishlist_id, 'slug' => $wishlist->getSlug()), "sitestoreproduct_wishlist_view", true);

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'smoothboxClose' => 10,
        'parentRedirect' => $url,
        'parentRedirectTime' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your wishlist has been edited successfully.'))
    ));
  }

  //ACTION FOR PRINT WISHLIST
  public function printAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //GET WISHLIST ID AND OBJECT
    $wishlist_id = $this->_getParam('wishlist_id');
    $this->view->wishlist = $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);

    $content_id = $this->_getParam('content_id', 0);
    $params = Engine_Api::_()->sitestoreproduct()->getWidgetInfo('sitestoreproduct.wishlist-profile-items', $content_id)->params;
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
    $this->view->statisticsWishlist = array("productCount", "likeCount", "viewCount", "followCount");
    if (isset($params['statisticsWishlist'])) {
      $this->view->statisticsWishlist = $params['statisticsWishlist'];
    }

    //FETCH RESULTS
    $this->view->paginator = Engine_Api::_()->getDbTable('wishlistmaps', 'sitestoreproduct')->wishlistProducts($wishlist->wishlist_id);
    $this->view->paginator->setItemCountPerPage(500);
    $this->view->total_item = $this->view->paginator->getTotalItemCount();
  }

  public function coverPhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //SMOOTHBOX
    if (null == $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {
      //NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    //GET PRODUCT ID
    $product_id = $this->view->product_id = $this->_getParam('product_id');
    $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

    if (empty($sitestoreproduct)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //GET PRODUCT ID
    $wishlist_id = $this->view->wishlist_id = $this->_getParam('wishlist_id');
    $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //AUTHORIZATION CHECK
    if ($viewer->level_id != 1 && $wishlist->owner_id != $viewer_id) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //DELETE WISHLIST CONTENT
        $wishlist->product_id = $product_id;
        $wishlist->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRedirect' => $this->_helper->url->url(array('action' => 'browse'), "sitestoreproduct_wishlist_general", true),
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'))
      ));
    } else {
      $this->renderScript('wishlist/cover-photo.tpl');
    }
  }

  //ACTION FOR DELETE WISHLIST
  public function deleteAction() {

    //DEFAULT LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //ONLY LOGGED IN USER CAN CREATE
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET WISHLIST ID
    $this->view->wishlist_id = $wishlist_id = $this->_getParam('wishlist_id');

    $wishlist = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $level_id = $viewer->level_id;

    if ($level_id != 1 && $wishlist->owner_id != $viewer_id) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //DELETE WISHLIST CONTENT
        $wishlist->delete();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRedirect' => $this->_helper->url->url(array('action' => 'browse'), "sitestoreproduct_wishlist_general", true),
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your wishlist has been deleted successfully.'))
      ));
    } else {
      $this->renderScript('wishlist/delete.tpl');
    }
  }

}
