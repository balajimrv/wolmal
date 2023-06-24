<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_DashboardController extends Core_Controller_Action_Standard {

  //SET THE VALUE FOR ALL ACTION DEFAULT
  public function init() {

    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->isValid())
      return;

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();

    $store_url = $this->_getParam('store_url', $this->_getParam('store_url', null));
    $store_id = $this->_getParam('store_id', $this->_getParam('store_id', null));

    if ($store_url) {
      $store_id = Engine_Api::_()->sitestore()->getStoreId($store_url);
    }

    if ($store_id) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      if ($sitestore) {
        Engine_Api::_()->core()->setSubject($sitestore);
      }
    }

    //FOR UPDATE EXPIRATION
    if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.task.updateexpiredstores') + 900) <= time()) {
      Engine_Api::_()->sitestore()->updateExpiredStores();
    }
  }

  //ACTION FOR SHOWING THE APPS AT DASHBOARD
  public function appAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //GET THE LOGGEDIN USER INFORMATION
    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET THE SITESTORE ID FROM THE URL
    $this->view->store_id = $store_id = $this->_getParam('store_id');

    //SET THE SUBJECT OF SITESTORE
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
       
    //VERSION CHECK APPLIED FOR - PACKAGE WORK
    $this->view->siteeventVersion = false;
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
 
      if (Engine_Api::_()->sitestore()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent')->version, '4.8.8')) {
        $this->view->siteeventVersion = true;
      }
    }
    
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorealbum")) {
          $this->view->allowed_upload_photo = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'spcreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->allowed_upload_photo = 1;
        }
      }
      //START THE STORE ALBUM WORK
      $this->view->default_album_id = Engine_Api::_()->getItemTable('sitestore_album')->getDefaultAlbum($store_id)->album_id;
      //END THE STORE ALBUM WORK

      $this->view->albumtab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $store_id, $layout);
    }

    //PASS THE STORE ID IN THE CORRESPONDING TPL FILE
    $this->view->sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $this->view->sitestores_view_menu = 16;

    //START THE STORE POLL WORK
    $sitestorePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll');
    if ($sitestorePollEnabled) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorepoll")) {
          $this->view->can_create_poll = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'splcreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_poll = 1;
        }
      }
      //PACKAGE BASE PRIYACY END

      $this->view->polltab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorepoll.profile-sitestorepolls', $store_id, $layout);
    }
    //END THE STORE POLL WORK
    //START THE STORE DOCUMENT WORK
    $sitestoreDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument');
    if ($sitestoreDocumentEnabled|| (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestoredocument")) {
          $this->view->can_create_doc = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'sdcreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_doc = 1;
        }
      }
      //PACKAGE BASE PRIYACY END

      if ($sitestoreDocumentEnabled) {
            $this->view->documenttab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoredocument.profile-sitestoredocuments', $store_id, $layout);
        } else {
            $this->view->documenttab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('document.contenttype-documents', $store_id, $layout);
        }
    }
    //END THE STORE DOCUMENT WORK
    //START THE STORE INVITE WORK
    $this->view->can_invite = 0;
    $sitestoreInviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreinvite');
    if ($sitestoreInviteEnabled) {

      //START MANAGE-ADMIN CHECK
      $this->view->can_invite = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'invite');
      //END MANAGE-ADMIN CHECK
    }
    //END THE STORE INVITE WORK
    //START THE STORE VIDEO WORK
    $sitestoreVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo');
    if ($sitestoreVideoEnabled|| (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorevideo")) {
          $this->view->can_create_video = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'svcreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_video = 1;
        }
      }
      //PACKAGE BASE PRIYACY END

      if($sitestoreVideoEnabled) {
				$this->view->videotab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorevideo.profile-sitestorevideos', $store_id, $layout);
      } else {
				$this->view->videotab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitevideo.contenttype-videos', $store_id, $layout);
      }
    }
    //END THE STORE VIDEO WORK
    //START THE STORE EVENT WORK
        $sitestoreeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent');
	
		if ($sitestoreeventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestoreevent")) {
          $this->view->can_create_event = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'secreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_event = 1;
        }
      }
      //PACKAGE BASE PRIYACY END

      if($sitestoreeventEnabled) {
				$this->view->eventtab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreevent.profile-sitestoreevents', $store_id, $layout);
      } else {
				$this->view->eventtab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('siteevent.contenttype-events', $store_id, $layout);
      }
    }
    //END THE STORE EVENT WORK
    //START THE STORE NOTE WORK
    $sitestoreNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote');
    if ($sitestoreNoteEnabled) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorenote")) {
          $this->view->can_create_notes = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'sncreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_notes = 1;
        }
      }
      //PACKAGE BASE PRIYACY END

      $this->view->notetab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorenote.profile-sitestorenotes', $store_id, $layout);
    }
    //END THE STORE NOTE WORK
    //START THE STORE REVEIW WORK
    $sitestoreReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
    if ($sitestoreReviewEnabled) {
      $hasPosted = Engine_Api::_()->getDbTable('reviews', 'sitestorereview')->canPostReview($subject->store_id, $viewer_id);
      if (empty($hasPosted) && !empty($viewer_id)) {
        $this->view->can_create_review = 1;
      } else {
        $this->view->can_create_review = 0;
      }

      $this->view->reviewtab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorereview.profile-sitestorereviews', $store_id, $layout);
    }
    //END THE STORE REVEIW WORK
    //START THE STORE DISCUSSION WORK
    $sitestoreDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion');
    if ($sitestoreDiscussionEnabled) {

      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'sdicreate');
      if (!empty($isManageAdmin)) {
        $this->view->can_create_discussion = 1;
      }
      //END MANAGE-ADMIN CHECK
      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorediscussion")) {
          $this->view->can_create_discussion = 0;
        }
      }
      //PACKAGE BASE PRIYACY END

      $this->view->discussiontab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.discussion-sitestore', $store_id, $layout);
    }
    //END THE STORE DISCUSSION WORK
    //START THE STORE FORM WORK
    $sitestoreFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform');
    if ($sitestoreFormEnabled) {

      //START MANAGE-ADMIN CHECK
      $this->view->can_form = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'form');
      //END MANAGE-ADMIN CHECK

      $store_id = $this->_getParam('store_id');
      $quetion = Engine_Api::_()->getDbtable('storequetions', 'sitestoreform');
      $select_quetion = $quetion->select()->where('store_id = ?', $store_id);
      $result_quetion = $quetion->fetchRow($select_quetion);
      $this->view->option_id = $result_quetion->option_id;

      $this->view->formtab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreform.sitestore-viewform', $store_id, $layout);
    }
    //END THE STORE FORM WORK
    //START THE STORE OFFER WORK
    $this->view->moduleEnable = $sitestoreOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer');
    if ($sitestoreOfferEnabled) {

      //START MANAGE-ADMIN CHECK
      $this->view->can_offer = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'offer');
      //END MANAGE-ADMIN CHECK

      $this->view->offertab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $store_id, $layout);
    }
    //END THE STORE OFFER WORK
    //START THE STORE MUSIC WORK
    $sitestoreMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic');
    if ($sitestoreMusicEnabled) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestoremusic")) {
          $this->view->can_create_musics = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'smcreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_musics = 1;
        }
      }
      //PACKAGE BASE PRIYACY END

      $this->view->musictab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoremusic.profile-sitestoremusic', $store_id, $layout);
    }
    //END THE STORE MUSIC WORK
    
    //START THE STORE NOTE WORK
    //PACKAGE BASE PRIYACY START
    if (!empty($subject->approved) && empty($subject->closed) && !empty($subject->search) && !empty($subject->draft) && empty($subject->declined)) {
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        $this->view->can_create_sitestoreproduct_product = true;
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'sncreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_sitestoreproduct_product = true;
        }
      }
    }

    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

    //IS USER IS PAGE ADMIN OR NOT
    if ($authValue > 1)
      $this->view->sitestoreproduct_store_admin = true;      

    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

    //IS USER IS PAGE ADMIN OR NOT
    if ($authValue > 1)
      $this->view->sitestoreproduct_store_admin = true;

    //PACKAGE BASE PRIYACY END

    $this->view->sitestoreproducttab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreproduct.profile-products', $store_id, $layout);
    //END THE STORE NOTE WORK

    $this->view->is_ajax = $this->_getParam('is_ajax', '');
  }

  //ACTION FOR CONTACT INFORMATION
  public function announcementsAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    //GET STORE ID
    $this->view->store_id = $store_id = $this->_getParam('store_id');

    //GET SITESTORE ITEM
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'contact');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET REQUEST IS AJAX OR NOT
    $this->view->is_ajax = $this->_getParam('is_ajax', '');

    //SHOW SELECTED TAB
    $this->view->sitestores_view_menu = 30;

    $this->view->announcements = Engine_Api::_()->getDbtable('announcements', 'sitestore')->announcements($store_id);
  }

  //ACTION FOR CONTACT INFORMATION
  public function notificationSettingsAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    //GET THE LOGGEDIN USER INFORMATION
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET STORE ID
    $this->view->store_id = $store_id = $this->_getParam('store_id');

    //GET SITESTORE ITEM
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    
    if( $viewer_id == $sitestore->owner_id )
    {
      $show_sitestoreproduct_form_element = true;
    }
    else
    {
      $show_sitestoreproduct_form_element = false;
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'contact');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET REQUEST IS AJAX OR NOT
    $this->view->is_ajax = $this->_getParam('is_ajax', '');

    //SHOW SELECTED TAB
    $this->view->sitestores_view_menu = 31;

    //SET FORM
    $this->view->form = $form = new Sitestore_Form_NotificationSettings(array('show_sitestoreproduct_form_element' => $show_sitestoreproduct_form_element));

    $ManageAdminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
    $ManageAdminsTableName = $ManageAdminsTable->info('name');

    $select = $ManageAdminsTable->select()
            ->from($ManageAdminsTableName)
            ->where($ManageAdminsTableName . '.store_id = ?', $store_id)
            ->where($ManageAdminsTableName . '.user_id = ?', $viewer_id);
    $results = $ManageAdminsTable->fetchRow($select);


    //POPULATE FORM
    $value['email'] = $results["email"];
    $this->view->notification = $value['notification'] = $results["notification"];
    $value['action_notification'] = unserialize($results['action_notification']);
    $form->populate($value);

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();
      if (isset($values['email'])) {
        $ManageAdminsTable->update(array('email' => $values['email']), array('store_id =?' => $store_id, 'user_id =?' => $viewer_id));
      }

      if (isset($values['notification'])) {
        $ManageAdminsTable->update(array('notification' => $values['notification']), array('store_id =?' => $store_id, 'user_id =?' => $viewer_id));
        if (!empty($values['notification'])) {
          $ManageAdminsTable->update(array('action_notification' => serialize($values['action_notification'])), array('store_id =?' => $store_id, 'user_id =?' => $viewer_id));
        } else {
          $ManageAdminsTable->update(array('action_notification' => ''), array('store_id =?' => $store_id, 'user_id =?' => $viewer_id));
        }
      }
      
      // STORE ADMIN IDS FOR WHICH NOTIFICATION WILL NOT SEND
      if( isset($values['toValues']) && !empty($values['toValues']) )
      {
        $store_admin_ids = $values['toValues'];
        $ManageAdminsTable->update(array('sitestoreproduct_notification' => 1), array('store_id =?' => $store_id, "user_id IN ($store_admin_ids)"));
      }

      //SHOW SUCCESS MESSAGE
      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
    } else {
      $this->view->is_ajax = $this->_getParam('is_ajax', '');
    }
  }
  
  // TO DISPLAY STORE ADMIN NAMES IN AUTOSUGGEST BOX
  public function suggestStoreAdminNamesAction() {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $text = $this->_getParam('value');
    $store_admin_ids = $this->_getParam('store_admin_ids', null);
    $store_id = $this->_getParam('store_id', null);
    $limit = $this->_getParam('limit', 40);
    
    if( empty($store_id) )
    {
      return;
    }
    
    $userTable = Engine_Api::_()->getItemTable('user');
    $userTableName = $userTable->info('name');
    
    $manageAdminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
    $manageAdminsTableName = $manageAdminsTable->info('name');
    
    $select = $userTable->select()
            ->from($userTableName)
            ->setIntegrityCheck(false)
            ->joinLeft($manageAdminsTableName, "$manageAdminsTableName.user_id = $userTableName.user_id", array("")) 
            ->where("$userTableName.displayname LIKE ?", "%" . $text . "%")
            ->where("$manageAdminsTableName.user_id !=?", $viewer_id)
            ->where("$manageAdminsTableName.store_id =?", $store_id);
    
    if( !empty($store_admin_ids) )
    {
      $select->where("$manageAdminsTableName.user_id NOT IN ($store_admin_ids)");
    } 
  
    $select->order("$userTableName.displayname ASC")->limit($limit);
    $users = $userTable->fetchAll($select);

    $data = array();
    $mode = $this->_getParam('struct');
    if ($mode == 'text') {
      foreach ($users as $user) {
        $data[] = $user->displayname;
      }
    } else {
      foreach ($users as $user) {
        $data[] = array(
                'id' => $user->user_id,
                'label' => $user->displayname,
                'photo' => $this->view->itemPhoto($user, 'thumb.icon'),
        );
      }
    }

    if ($this->_getParam('sendNow', true)) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  //ACTION FOR CONTACT INFORMATION
  public function contactAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    //GET STORE ID
    $this->view->store_id = $store_id = $this->_getParam('store_id');

    //GET SITESTORE ITEM
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'contact');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET REQUEST IS AJAX OR NOT
    $this->view->is_ajax = $this->_getParam('is_ajax', '');

    //SHOW SELECTED TAB
    $this->view->sitestores_view_menu = 20;

    //SET FORM
    $this->view->form = $form = new Sitestore_Form_Contactinfo(array('storeowner' => Engine_Api::_()->user()->getUser($sitestore->owner_id)));

    //POPULATE FORM
    $value['email'] = $sitestore->email;
    $value['phone'] = $sitestore->phone;
    $value['website'] = $sitestore->website;
    $form->populate($value);

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
      $values = $form->getValues();
      if (isset($values['email'])) {
        $email_id = $values['email'];

        //CHECK EMAIL VALIDATION
        $validator = new Zend_Validate_EmailAddress();
        $validator->getHostnameValidator()->setValidateTld(false);
        if (!empty($email_id)) {
          if (!$validator->isValid($email_id)) {
            $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter a valid email address.'));
            return;
          } else {
            $sitestore->email = $email_id;
          }
        } else {
          $sitestore->email = $email_id;
        }
      }

      //CHECK PHONE OPTION IS THERE OR NOT
      if (isset($values['phone'])) {
        $sitestore->phone = $values['phone'];
      }

      //CHECK WEBSITE OPTION IS THERE OR NOT
      if (isset($values['website'])) {
        $sitestore->website = $values['website'];
      }

      //SAVE VALUES
      $sitestore->save();

      //SHOW SUCCESS MESSAGE
      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
    } else {
      $this->view->is_ajax = $this->_getParam('is_ajax', '');
    }
  }

  //ACTION FOR EDIT STYLE
  public function editStyleAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //GET STORE ID AND STORE OBJECT
    $this->view->store_id = $store_id = $this->_getParam('store_id');
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    if (empty($sitestore)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $this->view->sitestores_view_menu = 3;
    $this->view->is_ajax = $this->_getParam('is_ajax', '');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET FORM
    $this->view->form = $form = new Sitestore_Form_Style();

    //GET CURRENT ROW
    $tableStyle = Engine_Api::_()->getDbtable('styles', 'core');

    $row = $tableStyle->fetchRow(array('type = ?' => 'sitestore_store', 'id = ? ' => $store_id));
    $style = $sitestore->getStoreStyle();

    //POPULATE
    if (!$this->getRequest()->isPost()) {
      $form->populate(array(
          'style' => ( null == $row ? '' : $row->style )
      ));
      return;
    }

    //Whoops, form was not valid
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET STYLE
    $style = $form->getValue('style');
    $style = strip_tags($style);

    $forbiddenStuff = array(
        '-moz-binding',
        'expression',
        'javascript:',
        'behaviour:',
        'vbscript:',
        'mocha:',
        'livescript:',
    );

    $style = str_replace($forbiddenStuff, '', $style);

    //SAVE IN DATABASE
    if (null == $row) {
      $row = $tableStyle->createRow();
      $row->type = 'sitestore_store';
      $row->id = $store_id;
    }
    $row->style = $style;
    $row->save();
    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
  }

  //ACTION FOR ALL LOCATION
  public function allLocationAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //LOCAITON ENABLE OR NOT
    if (!Engine_Api::_()->sitestore()->enableLocation()) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    $this->view->sitestores_view_menu = 4;

    //GET STORE ID, STORE OBJECT AND THEN CHECK STORE VALIDATION
    $this->view->store_id = $store_id = $this->_getParam('store_id');

    //$location_id = $this->_getParam('location_id');
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (empty($sitestore)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'map');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    if (!empty($sitestore->location)) {
      $mainLocationId = Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocationId($sitestore->store_id, $sitestore->location);
      $this->view->mainLocationObject = Engine_Api::_()->getItem('sitestore_location', $mainLocationId);
      $value['mainlocationId'] = $mainLocationId;
    }
    $value['id'] = $sitestore->getIdentity();
    $value['mapshow'] = 'Map Tab';
    $store = $this->_getParam('store');

    $this->view->location = $paginator = Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocation($value);

    $paginator->setItemCountPerPage(10);
    $this->view->paginator = $paginator->setCurrentPageNumber($store);
  }

  //ACTION FOR EDIT LOCATION
  public function editLocationAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //LOCAITON ENABLE OR NOT
    if (!Engine_Api::_()->sitestore()->enableLocation()) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    $this->view->sitestores_view_menu = 4;

    //GET STORE ID, STORE OBJECT AND THEN CHECK STORE VALIDATION
    $this->view->store_id = $store_id = $this->_getParam('store_id');
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (empty($sitestore)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'map');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $location_id = $this->_getParam('location_id');

    $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestore');

//    $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.multiple.location', 1);

    $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1);

    if (empty($location_id)) {
      $location_id = Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocationId($store_id, $sitestore->location);
    }
    if ($locationFieldEnable && $location_id) {
      $params['location_id'] = $location_id;
      $params['id'] = $store_id;
      $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocation($params);
    }

    //Get form
    if (!empty($location)) {
      $this->view->form = $form = new Sitestore_Form_Location(array(
                  'item' => $sitestore,
                  'location' => $location->location
              ));

      if (!$this->getRequest()->isPost()) {
        $form->populate($location->toarray());
        return;
      }

      //FORM VALIDAITON
      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

      //FORM VALIDAITON
      if ($form->isValid($this->getRequest()->getPost())) {
        $values = $form->getValues();
        unset($values['submit']);
        unset($values['location']);
        unset($values['locationParams']);
        $locationTable->update($values, array('store_id =?' => $store_id, 'location_id =?' => $location_id));
      }
      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }

    $this->view->location = $location = Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocation($params);
  }

  //ACTION FOR EDIT ADDRESS
  public function addLocationAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET STORE ID, STORE OBJECT AND THEN CHECK STORE VALIDATION
    $tab_selected_id = $this->_getParam('tab');
    $this->view->store_id = $store_id = $this->_getParam('store_id');
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (empty($sitestore)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'map');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $this->view->form = $form = new Sitestore_Form_Address(array('item' => $sitestore));
    $form->setTitle('Add Location');
    $form->setDescription('Add your location below, then click "Save Location" to save your location.');

    //POPULATE FORM
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();
    if (empty($values['location'])) {
      $itemError = Zend_Registry::get('Zend_Translate')->_("Please enter the location.");
      $form->addError($itemError);
      return;
    }
    
    if (empty($values['locationParams'])) {
      $itemError = Zend_Registry::get('Zend_Translate')->_("Please select location from the auto-suggest.");
      $form->addError($itemError);
      return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      if (!empty($values['main_location'])) {
        $sitestore->location = $values['location'];
        $sitestore->save();
      }

      $location = array();
      unset($values['submit']);
      $location = $values['location'];
      $locationName = $values['locationname'];
      if (!empty($location)) {
        $sitestore->setLocation($location, $locationName);
      }
      
      if(isset($values['product_location']) && !empty($values['product_location']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0)) {
          $storeProductTable = Engine_Api::_()->getItemTable('sitestoreproduct_product');
          $storeProductTableName = $storeProductTable->info('name');
          $select = $storeProductTable->select()->from($storeProductTableName, array('product_id', 'location'))->where('store_id = ?', $store_id);
          foreach($storeProductTable->fetchAll($select) as $storeProduct) {
              $storeProductTable->update(array('location' => $location), array('product_id = ?' => $storeProduct->product_id));
              $storeProduct->setLocation($location, $locationName);
          }
      }
      
      $db->commit();
      if (!empty($tab_selected_id)) {
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRedirect' => $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', true),
            'parentRedirectTime' => '2',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your store location has been added successfully.'))
        ));
      } else {
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRefresh' => 100,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your store location has been added successfully.'))
        ));
      }
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR LEAVE THE JOIN MEMBER.
  public function deleteLocationAction() {

    $store_id = $this->_getParam('store_id');
    $tab_selected_id = $this->_getParam('tab');
    $location_id = $this->_getParam('location_id');
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $location = Engine_Api::_()->getItem('sitestore_location', $location_id);
    if ($this->getRequest()->isPost()) {
      if ($location->location == $sitestore->location) {
        $sitestore->location = '';
        $sitestore->save();
      }

      if (!empty($store_id)) {
        Engine_Api::_()->getDbtable('locations', 'sitestore')->delete(array('location_id =?' => $location_id, 'store_id =?' => $store_id));
      }

      if (!empty($tab_selected_id)) {
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRedirect' => $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', true),
            'parentRedirectTime' => '2',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully delete location for this store.'))
        ));
      } else {
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRefresh' => 100,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully delete location for this store.'))
        ));
      }
    }
  }

  //ACTION FOR EDIT ADDRESS
  public function editAddressAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET STORE ID, STORE OBJECT AND THEN CHECK STORE VALIDATION
    $tab_selected_id = $this->_getParam('tab');
    $this->view->store_id = $store_id = $this->_getParam('store_id');
    $location_id = $this->_getParam('location_id');
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.multiple.location', 1);

    $location = Engine_Api::_()->getItem('sitestore_location', $location_id);

    if (empty($sitestore)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'map');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $this->view->form = $form = new Sitestore_Form_Address(array('item' => $sitestore));
    $form->setTitle('Edit Location');
    $form->setDescription('Edit your location below, then click "Save Location" to save your location.');

    if (!empty($multipleLocation) && $location->location == $sitestore->location) {
      $form->main_location->setValue(1);
    }

    //POPULATE FORM
    if (!$this->getRequest()->isPost()) {
      if (!empty($multipleLocation)) {
        $form->populate($location->toArray());
      } else {
        $form->populate($sitestore->toArray());
      }
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $values = $form->getValues();

    if (empty($values['location']) && !empty($multipleLocation)) {
      $itemError = Zend_Registry::get('Zend_Translate')->_("Please enter the location.");
      $form->addError($itemError);
      return;
    }
    
    if (empty($values['locationParams'])) {
      $itemError = Zend_Registry::get('Zend_Translate')->_("Please select location from the auto-suggest.");
      $form->addError($itemError);
      return;
    }
    
    $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestore');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      if (!empty($multipleLocation)) {
        if (!empty($values['main_location'])) {
          $sitestore->location = $values['location'];
          $sitestore->save();
        } elseif ($sitestore->location == $location->location) {
          $sitestore->location = '';
          $sitestore->save();
        }
        if ($location->location != $values['location']) {
          $locationTable->delete(array('location_id =?' => $location_id));
          $sitestore->setLocation($values['location']);
        }
      } else {
				if(!empty($values['location']) && $values['location'] != $sitestore->location) {
					$locationTable->delete(array('location_id =?' => $location_id));
					$sitestore->location = $values['location'];
					$sitestore->setLocation($values['location']);
					$sitestore->save();
				}
      }
      
      $location = '';
      $locationName = '';
      unset($values['submit']);
      $location = $values['location'];

      if (isset($values['locationname']))
        $locationName = $values['locationname'];
      
      if(isset($values['product_location']) && !empty($values['product_location']) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0)) {
          $storeProductTable = Engine_Api::_()->getItemTable('sitestoreproduct_product');
          $storeProductTableName = $storeProductTable->info('name');
          $select = $storeProductTable->select()->from($storeProductTableName, array('product_id', 'location'))->where('store_id = ?', $store_id);
          foreach($storeProductTable->fetchAll($select) as $storeProduct) {
              $storeProductTable->update(array('location' => $location), array('product_id = ?' => $storeProduct->product_id));
              $storeProduct->setLocation($location, $locationName);
          }
      }


      if (!empty($location)) {
        // $sitestore->setLocation();

        if (!empty($multipleLocation)) {
          $locationTable->update(array('location' => $location, 'locationname' => $locationName), array('store_id =?' => $store_id, 'location_id =?' => $location_id));
        } else {
          $locationTable->update(array('location' => $location), array('store_id =?' => $store_id, 'location_id =?' => $location_id));
        }
      } else {
        $locationTable->delete(array('store_id =?' => $store_id, 'location_id =?' => $location_id));
      }

      $db->commit();
      if (!empty($tab_selected_id)) {
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRedirect' => $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', true),
            'parentRedirectTime' => '2',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your store location has been modified successfully.'))
        ));
      }  elseif(!empty($multipleLocation)) {
				$this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRedirect' => $this->_helper->url->url(array('action' => 'all-location', 'store_id' => $store_id), 'sitestore_dashboard', true),
            'parentRedirectTime' => '2',
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your store location has been modified successfully.'))
        ));
      }
      else {
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 500,
            'parentRefresh' => 100,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your store location has been modified successfully.'))
        ));
      }
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR FEATURED OWNER
  public function featuredOwnersAction() {

    //USER VALIDAITON
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    $this->view->sitestores_view_menu = 17;

    //GET STORE ID AND STORE OBJECT
    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //EDIT PRIVACY
    $editPrivacy = 0;
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (!empty($isManageAdmin)) {
      $editPrivacy = 1;
    }

    $manageAdminAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1);
    if (empty($editPrivacy) || empty($manageAdminAllowed)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //GET FEATURED ADMINS
    $this->view->featuredhistories = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->featuredAdmins($store_id);

    $this->view->is_ajax = $this->_getParam('is_ajax', '');
  }

  //ACTION FOR MAKING FAVOURITE
  public function favouriteAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GETTING VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET THE SUBJECT
    $getStoreId = $this->_getParam('store_id', $this->_getParam('id', null));
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject();
    $store_id = $sitestore->store_id;

    //CALLING THE FUNCTION AND PASS THE VALUES OF STORE ID AND USER ID.
    $this->view->userListings = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStores($store_id, $viewer_id);

    //CHECK POST.
    if ($this->getRequest()->isPost()) {

      //GET VALUE FROM THE FORM.
      $values = $this->getRequest()->getPost();
      $selected_store_id = $values['store_id'];
      if (!empty($selected_store_id)) {

        $favouritesTable = Engine_Api::_()->getDbtable('favourites', 'sitestore');
        $row = $favouritesTable->createRow();
        $row->store_id = $selected_store_id;
        $row->store_id_for = $getStoreId;
        $row->owner_id = $viewer_id;
        $row->save();

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 100,
            'parentRefresh' => 100,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Store has been successfully linked.'))
        ));
      }
    }
    //RENDER THE SCRIPT.
    $this->renderScript('dashboard/favourite.tpl');
  }

  //ACTION FOR DELETING THE FAVOURITE
  public function favouriteDeleteAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GETTING THE VIEWER ID.
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET THE SUBJECT AND CHECK AUTH.
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject();
    $store_id = $sitestore->store_id;

    //CALLING THE FUNCTION.
    $this->view->userListings = Engine_Api::_()->getDbtable('favourites', 'sitestore')->deleteLink($store_id, $viewer_id);

    //CHECK POST.
    if ($this->getRequest()->isPost()) {

      $values = $this->getRequest()->getPost();
      $store_id_for = $store_id;
      $store_id = $values['store_id'];
      if (!empty($store_id)) {

        //DELETE THE RESULT FORM THE TABLE.
        $sitestoreTable = Engine_Api::_()->getDbtable('favourites', 'sitestore');
        $sitestoreTable->delete(array('store_id =?' => $store_id, 'store_id_for =?' => $store_id_for));

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 100,
            'parentRefresh' => 100,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Store has been successfully unlinked.'))
        ));
      }
    }
    //RENDER THE SCRIPT.
    $this->renderScript('dashboard/favourite-delete.tpl');
  }

  //ACTION FOR FOURSQUARE CODE
  public function foursquareAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    //SMOOTHBOX
    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {
      //NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    //GET STORE ID AND SITESTORE OBJECT
    $siteastore = Engine_Api::_()->getItem('sitestore_store', $this->_getParam('store_id'));

    //GENERATE FORM
    $this->view->form = $form = new Sitestore_Form_Foursquare();

    //POPULATE THE FORM
    $form->populate($siteastore->toArray());

    if (!$this->getRequest()->isPost())
      return;

    //SAVE THE FOURSQUARE CODE IN DATABASE
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $db = Engine_Api::_()->getDbtable('stores', 'sitestore')->getAdapter();
      $db->beginTransaction();
      try {
        $siteastore->foursquare_text = $_POST['foursquare_text'];
        $siteastore->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => false,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Text saved successfully.'))
      ));
    }
  }

  //ACTION: GET-STARTED
  public function getStartedAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //VIEWER INFORMATION
    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET STORE ID AND STORE SUBJECT
    $this->view->store_id = $store_id = $this->_getParam('store_id');
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
    $this->view->allowSellingProducts = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($store_id, false);

        
    //VERSION CHECK APPLIED FOR - PACKAGE WORK
    $this->view->siteeventVersion = false;
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
      if (Engine_Api::_()->sitestore()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('siteevent')->version, '4.8.8')) {
        $this->view->siteeventVersion = true;
      }
    }

    //WORK FOR SHOWING THE TIP MESSAGE IF NOT CONFIGURED ANY VAT
    $this->view->isConfiguredVat = true;
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0)){
    $storeVatDetail = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $store_id, 'is_vat = ?' => 1));    
    if(empty($storeVatDetail))
      $this->view->isConfiguredVat = false;
    }
    
    //GET PHOTO ID
    $this->view->photo_id = $subject->photo_id;
    
    // CHECK PAYMENT FOR ORDERS
    $directPaymentEnable = false;
    $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
    if( empty($isAdminDrivenStore) ) {
      $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
      if( empty($isPaymentToSiteEnable) ) {
        $directPaymentEnable = true;
      }
    }
    $this->view->isPaymentToSellerEnable = $directPaymentEnable;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $this->view->is_ajax = $this->_getParam('is_ajax', '');

    //OVERVIEW PRIVACY
    $this->view->overviewPrivacy = 0;
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'overview');
    if (!empty($isManageAdmin)) {
      $this->view->overviewPrivacy = 1;
    }

    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);

    //START STORE ALBUM WORK
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorealbum")) {
          $this->view->allowed_upload_photo = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'spcreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->allowed_upload_photo = 1;
        }
      }
      //START THE STORE ALBUM WORK
      $this->view->default_album_id = Engine_Api::_()->getItemTable('sitestore_album')->getDefaultAlbum($store_id)->album_id;
      //END THE STORE ALBUM WORK

      $this->view->albumtab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $store_id, $layout);
    }
    //END STORE ALBUM WORK
    //GET STORE OBJECT
    $this->view->sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $this->view->sitestores_view_menu = 12;

    //START THE STORE POLL WORK
    $sitestorePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll');
    if ($sitestorePollEnabled) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorepoll")) {
          $this->view->can_create_poll = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'splcreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_poll = 1;
        }
      }
      //PACKAGE BASE PRIYACY END

      $this->view->polltab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorepoll.profile-sitestorepolls', $store_id, $layout);
    }
    //END THE STORE POLL WORK
    
    //START THE SITESTORE WORK
    //PACKAGE BASE PRIYACY START
    if (!empty($subject->approved) && empty($subject->closed) && !empty($subject->search) && !empty($subject->draft) && empty($subject->declined)) {
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        $this->view->can_create_sitestoreproduct_product = true;
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'splcreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_sitestoreproduct_product = 1;
        }
      }
    }
    

    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

    //IS USER IS PAGE ADMIN OR NOT
    if ($authValue > 1)
      $this->view->sitestoreproduct_store_admin = true;
    //END THE SITESTORE WORK
     

      
    //START THE STORE DOCUMENT WORK
    $sitestoreDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument');
    if ($sitestoreDocumentEnabled|| (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestoredocument")) {
          $this->view->can_create_doc = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'sdcreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_doc = 1;
        }
      }
      //PACKAGE BASE PRIYACY END

      if ($sitestoreDocumentEnabled) {
            $this->view->documenttab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoredocument.profile-sitestoredocuments', $store_id, $layout);
        } else {
            $this->view->documenttab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('document.contenttype-documents', $store_id, $layout);
        }
    }
    //END THE STORE DOCUMENT WORK
    //START THE STORE INVITE WORK
    $this->view->can_invite = 0;
    $sitestoreInviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreinvite');
    if ($sitestoreInviteEnabled) {

      //START MANAGE-ADMIN CHECK
      $this->view->can_invite = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'invite');
      //END MANAGE-ADMIN CHECK
    }
    //END THE STORE INVITE WORK
    //START THE STORE VIDEO WORK
    $sitestoreVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo');
    if ($sitestoreVideoEnabled|| (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorevideo")) {
          $this->view->can_create_video = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'svcreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_video = 1;
        }
      }
      //PACKAGE BASE PRIYACY END
      if($sitestoreVideoEnabled) {
				$this->view->videotab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorevideo.profile-sitestorevideos', $store_id, $layout);
      } else {
				$this->view->videotab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitevideo.contenttype-videos', $store_id, $layout);
      }
    
    }
    //END THE STORE VIDEO WORK
    //START THE STORE EVENT WORK
    $sitestoreeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent');
		if ($sitestoreeventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestoreevent")) {
          $this->view->can_create_event = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'secreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_event = 1;
        }
      }
      //PACKAGE BASE PRIYACY END

			
      if($sitestoreeventEnabled) {
				$this->view->eventtab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreevent.profile-sitestoreevents', $store_id, $layout);
      } else {
				$this->view->eventtab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('siteevent.contenttype-events', $store_id, $layout);
      }
    }
    //END THE STORE EVENT WORK
    //START THE STORE NOTE WORK
    $sitestoreNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote');
    if ($sitestoreNoteEnabled) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorenote")) {
          $this->view->can_create_notes = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'sncreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_notes = 1;
        }
      }
      //PACKAGE BASE PRIYACY END

      $this->view->notetab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorenote.profile-sitestorenotes', $store_id, $layout);
    }
    //END THE STORE NOTE WORK
    //START THE STORE REVEIW WORK
    $sitestoreReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
    if ($sitestoreReviewEnabled) {
      $hasPosted = Engine_Api::_()->getDbTable('reviews', 'sitestorereview')->canPostReview($subject->store_id, $viewer_id);
      if (empty($hasPosted) && !empty($viewer_id)) {
        $this->view->can_create_review = 1;
      } else {
        $this->view->can_create_review = 0;
      }

      $this->view->reviewtab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorereview.profile-sitestorereviews', $store_id, $layout);
    }
    //END THE STORE REVEIW WORK
    //START THE STORE DISCUSSION WORK
    $sitestoreDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion');
    if ($sitestoreDiscussionEnabled) {

      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'sdicreate');
      if (!empty($isManageAdmin)) {
        $this->view->can_create_discussion = 1;
      }
      //END MANAGE-ADMIN CHECK
      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorediscussion")) {
          $this->view->can_create_discussion = 0;
        }
      }
      //PACKAGE BASE PRIYACY END

      $this->view->discussiontab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.discussion-sitestore', $store_id, $layout);
    }
    //END THE STORE DISCUSSION WORK
    //START THE STORE OFFER WORK
    $this->view->moduleEnable = $sitestoreOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer');
    if ($sitestoreOfferEnabled) {

      //START MANAGE-ADMIN CHECK
      $this->view->can_offer = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'offer');
      //END MANAGE-ADMIN CHECK

      $this->view->offertab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $store_id, $layout);
    }
    //END THE STORE OFFER WORK
    //START THE STORE FORM WORK
    $sitestoreFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform');
    if ($sitestoreFormEnabled) {

      //START MANAGE-ADMIN CHECK
      $this->view->can_form = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'form');
      //END MANAGE-ADMIN CHECK

      $store_id = $this->_getParam('store_id');
      $quetion = Engine_Api::_()->getDbtable('storequetions', 'sitestoreform');
      $select_quetion = $quetion->select()->where('store_id = ?', $store_id);
      $result_quetion = $quetion->fetchRow($select_quetion);
      $this->view->option_id = $result_quetion->option_id;

      $this->view->formtab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreform.sitestore-viewform', $store_id, $layout);
    }
    //END THE STORE FORM WORK
    //START THE STORE MUSIC WORK
    $sitestoreMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic');
    if ($sitestoreMusicEnabled) {

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestoremusic")) {
          $this->view->can_create_musics = 1;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'smcreate');
        if (!empty($isStoreOwnerAllow)) {
          $this->view->can_create_musics = 1;
        }
      }
      //PACKAGE BASE PRIYACY END

      $this->view->musictab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoremusic.profile-sitestoremusic', $store_id, $layout);
    }
    //END THE STORE MUSIC WORK

    $this->view->updatestab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('activity.feed', $store_id, $layout);
    
    // START WORK OF SHOWING THE TIP REGARDING SHIPPING METHOD AND PAYMENT INFO
    
    //GET PRODUCT USING LEVEL OR PACKAGE BASED SETTINGS
    $packageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1);
    if (!empty($packageEnable)) {
      $packageObj = Engine_Api::_()->getItem('sitestore_package', $subject->package_id);      
      if( empty($packageObj->store_settings) ) {
        $product_types = array('simple', 'configurable', 'virtual', 'grouped', 'bundled', 'downloadable');
      }else {
        $storeSettings = @unserialize($packageObj->store_settings);
        $product_types = $storeSettings['product_type'];
      }      
    } else {
      $user = $subject->getOwner();
      $product_types = Engine_Api::_()->authorization()->getPermission($user->level_id, "sitestore_store", "product_type");
      $product_types = Zend_Json_Decoder::decode($product_types);
    }
    $this->view->product_types = $product_types;
    $this->view->countProductTypes = $countProductTypes = count($product_types);
    
    $this->view->isAnyShippingMethodExist = Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct')->isAnyShippingMethodExist($store_id);
    
    if( empty($directPaymentEnable) ) {
      $this->view->store_gateway = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->getStoreGateway($store_id);
    } else {
      if( !empty($subject->store_gateway) ) {
        $storeEnableGateway = Zend_Json_Decoder::decode($subject->store_gateway);
        if( !empty($storeEnableGateway) ) {
          $this->view->store_gateway = 1;
        } else {
          $this->view->store_gateway = 0;
        }
      } else {
        $this->view->store_gateway = 0;
      }
    }
    // END WORK OF SHOWING THE TIP REGARDING SHIPPING METHOD AND PAYMENT INFO
  }

  //ACTION FOR HIDE THE PHOTO
  public function hidePhotoAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //GET AJAX VALUE
    $is_ajax = $this->_getParam('isajax', '');

    //IF REQUEST IS NOT AJAX THEN ONLY SHOW FORM
    if (empty($is_ajax)) {
      $this->view->form = $form = new Sitestore_Form_Hidephoto();
    } else {
      Engine_Api::_()->getDbtable('photos', 'sitestore')->update(array('photo_hide' => 1), array('photo_id = ?' => $this->_getParam('hide_photo_id', null)));
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
  }

  //ACTION FOR SHOW MARKETING STORE
  public function marketingAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //GET VIEWER IDENTITY
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET STORE ID AND SITESTORE OBJECT
    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

//    $this->view->enableFoursquare = 1;
//    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'foursquare');
//    if (empty($isManageAdmin)) {
//      $this->view->enableFoursquare = 0;
//    }

    $this->view->enabletwitter = $sitestoretwitterEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter');
    if ($sitestoretwitterEnabled) {
      $this->view->enabletwitter = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'twitter');
    }


    $this->view->enableInvite = 1;
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'invite');
    if (empty($isManageAdmin)) {
      $this->view->enableInvite = 0;
    }

    $this->view->enableSendUpdate = 1;
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sendupdate');
    if (empty($isManageAdmin)) {
      $this->view->enableSendUpdate = 0;
    }

    $sitestoreLikeboxEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorelikebox');
    if (!empty($sitestoreLikeboxEnabled))
      $this->view->enableLikeBox = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'likebox');
    //END MANAGE-ADMIN CHECK

    $this->view->sitestores_view_menu = 20;
    $this->view->is_ajax = $this->_getParam('is_ajax', '');

    //CHECKING IF FACEBOOK STORE FEED WIDGET IS PLACED ON SITE PROFILE STORE OR NOT. IF YES ONLY THEN WE WILL SHOW FACEBOOK INTEGRATION FEATURE THERE.



    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    $this->view->fblikebox_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.fblikebox-sitestore', $store_id, $layout);
  }

  //ACTION FOR CREATING OVERVIEW
  public function overviewAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //GET STORE ID, STORE OBJECT AND STORE VALIDAITON
    $this->view->store_id = $store_id = $this->_getParam('store_id');
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (empty($sitestore)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'overview');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $overview = '';
    if (!empty($sitestore->overview)) {
      $overview = $sitestore->overview;
    }

    //FORM GENERATION
    $this->view->form = $form = new Sitestore_Form_Overview();

    if (!$this->getRequest()->isPost()) {

      $saved = $this->_getParam('saved');
      if (!empty($saved))
        $this->view->success = Zend_Registry::get('Zend_Translate')->_('Your store has been successfully created. You can enhance your store from this dashboard by creating other components.');
    }

    if ($this->getRequest()->isPost()) {

      $overview = $_POST['body'];
      $sitestore->overview = $overview;
      $sitestore->save();
      $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }
    $values['body'] = $overview;
    $form->populate($values);
    $this->view->sitestores_view_menu = 2;
  }

  //ACTION FOR CHANGING THE STORE PROFILE PICTURE
  public function profilePictureAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    //GET STORE ID
    $this->view->store_id = $store_id = $this->_getParam('store_id');

    //GET SITESTORE ITEM
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //GET SELECTED TAB
    $this->view->sitestores_view_menu = 22;

    //GET REQUEST IS ISAJAX OR NOT
    $this->view->is_ajax = $this->_getParam('is_ajax', '');

    //GET FORM
    $this->view->form = $form = new Sitestore_Form_Photo();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //UPLOAD PHOTO
    if ($form->Filedata->getValue() !== null) {
      //GET DB
      $db = $sitestore->getTable()->getAdapter();
      $db->beginTransaction();
      //PROCESS
      try {

        //SET PHOTO
        $sitestore->setPhoto($form->Filedata);

        //SET ALBUMS PARAMS
        $paramsAlbum = array();
        $paramsAlbum['store_id'] = $store_id;
        $paramsAlbum['default_value'] = 1;
        $paramsAlbum['limit'] = 1;

        //FETCH PHOTO ID
        $photo_id = Engine_Api::_()->getItemTable('sitestore_album')->getDefaultAlbum($sitestore->store_id)->photo_id;
        if ($photo_id == 0) {
          Engine_Api::_()->getItemTable('sitestore_album')->update(array('photo_id' => $sitestore->photo_id, 'owner_id' => $sitestore->owner_id), array('store_id = ?' => $sitestore->store_id, 'default_value = ?' => 1));
        }
        $db->commit();
      } catch (Engine_Image_Adapter_Exception $e) {
        $db->rollBack();
        $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    } else if ($form->getValue('coordinates') !== '') {
      $storage = Engine_Api::_()->storage();
      $iProfile = $storage->get($sitestore->photo_id, 'thumb.profile');
      $iSquare = $storage->get($sitestore->photo_id, 'thumb.icon');
      $pName = $iProfile->getStorageService()->temporary($iProfile);
      $iName = dirname($pName) . '/nis_' . basename($pName);
      list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));
      $image = Engine_Image::factory();
      $image->open($pName)
              ->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
              ->write($iName)
              ->destroy();
      $iSquare->store($iName);
      @unlink($iName);
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'profile-picture', 'store_id' => $store_id), 'sitestore_dashboard', true);
  }

  //ACTION FOR FILL THE DATA OF PROFILE TYPE
  public function profileTypeAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //GET STORE ID AND SITESTORE OBJECT
    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject();
    ;

    //GET PROFILE TYPE
    $profile_type_exist = $sitestore->profile_type;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    $this->view->sitestores_view_menu = 10;

    //PROFILE FIELDS FORM DATA
    $aliasedFields = $sitestore->fields()->getFieldsObjectsByAlias();
    $this->view->topLevelId = $topLevelId = 0;
    $this->view->topLevelValue = $topLevelValue = null;
    if (isset($aliasedFields['profile_type'])) {
      $aliasedFieldValue = $aliasedFields['profile_type']->getValue($sitestore);
      $topLevelId = $aliasedFields['profile_type']->field_id;
      $topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
      if (!$topLevelId || !$topLevelValue) {
        $topLevelId = null;
        $topLevelValue = null;
      }
      $this->view->topLevelId = $topLevelId;
      $this->view->topLevelValue = $topLevelValue;
    }

    //GET FORM
    $form = $this->view->form = new Fields_Form_Standard(array(
                'item' => Engine_Api::_()->core()->getSubject(),
                'topLevelId' => $topLevelId,
                'topLevelValue' => $topLevelValue,
            ));
    $form->submit->setLabel('Save Info');
    $form->setTitle('Edit Store Profile Info');
    if (empty($profile_type_exist)) {
      $form->setDescription('Profile information enables you to add additional information about your store depending on its category. This non-generic additional information will help others know more specific details about your store. First select a relevant Profile Type for your store, and then fill the corresponding profile information fields.');
    } else {
      $form->setDescription('Profile information enables you to add additional information about your store depending on its category. This non-generic additional information will help others know more specific details about your store.');
    }

    //SAVE DATA IF POSTED
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $form->saveValues();
      $values = $this->getRequest()->getPost();

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));

      $store_id = $this->_getParam('store_id', null);

      if (isset($values['0_0_1']) && !empty($values['0_0_1'])) {
        $profile_type = $values['0_0_1'];
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $sitestore->profile_type = $profile_type;
        $sitestore->save();
      }
    }

    //IF PACKAGE INABLE
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {

      $profileField_level = Engine_Api::_()->sitestore()->getPackageProfileLevel($store_id);
      if (empty($profileField_level)) {
        return $this->_forward('requireauth', 'error', 'core');
      }
      if ($profileField_level == 2) {
        $fieldsProfile = array("0_0_1", "submit");

        //PROFILE SELECT WORK
        if (empty($sitestore->profile_type)) {
          $profileType = $form->getElement('0_0_1')
                  ->getMultiOptions();

          $profileTypePackage = Engine_Api::_()->sitestore()->getSelectedProfilePackage($store_id);

          $profileTypeFinal = array_intersect_key($profileType, $profileTypePackage);

          //ONLY SET SELECTED PROFILE TYPE
          $profileType = $form->getElement('0_0_1')
                  ->setMultiOptions($profileTypeFinal);
          if (count($profileTypeFinal) <= 1) {
            $form->removeElement("0_0_1");
            $form->removeElement("submit");
            $error = Zend_Registry::get('Zend_Translate')->_("There are no profile fields available.");
            $form->addError($error);
          }
        }

        $field_id = array();
        $fieldsProfile_2 = Engine_Api::_()->sitestore()->getProfileFields($store_id);
        $fieldsProfile = array_merge($fieldsProfile, $fieldsProfile_2);

        //PROFILE FIELD IS SELECTED BUT THERE ARE NOT ANY PROFILE FIELDS
        if (!empty($sitestore->profile_type)) {
          $profile_field_flage = true;
          foreach ($fieldsProfile_2 as $k => $v) {
            $explodeField = explode("_", $v);
            if ($explodeField['1'] == $sitestore->profile_type) {
              $profile_field_flage = false;
              break;
            }
          }

          if ($profile_field_flage) {
            $form->removeElement("submit");
            $error = Zend_Registry::get('Zend_Translate')->_("There are no profile fields available.");
            $form->addError($error);
          }
        }

        foreach ($fieldsProfile_2 as $k => $v) {
          $explodeField = explode("_", $v);
          $field_id[] = $explodeField['2'];
        }

        $elements = $form->getElements();
        foreach ($elements as $key => $value) {
          $explode = explode("_", $key);
          if ($explode['0'] != "1" && $explode['0'] != "submit") {
            if (in_array($explode['0'], $field_id)) {
              $field_id[] = $explode['2'];
              $fieldsProfile[] = $key;
              continue;
            }
          }

          if (!in_array($key, $fieldsProfile)) {
            $form->removeElement($key);
            $form->addElement('Hidden', $key, array(
                "value" => "",
            ));
          }
        }
      }
    }//END PACKAGE WORK
    else {
      //START LEVEL CHECKS
      $store_owner = Engine_Api::_()->getItem('user', $sitestore->owner_id);
      $can_profile = Engine_Api::_()->authorization()->getPermission($store_owner->level_id, "sitestore_store", "profile");
      if (empty($can_profile)) {
        return $this->_forward('requireauth', 'error', 'core');
      }

      if ($can_profile == 2) {
        $fieldsProfile = array("0_0_1", "submit");

        //PROFILE SELECT WORK
        if (empty($sitestore->profile_type)) {
          $profileType = $form->getElement('0_0_1')
                  ->getMultiOptions();

          $profileTypePackage = Engine_Api::_()->sitestore()->getSelectedProfileLevel($store_owner->level_id);

          $profileTypeFinal = array_intersect_key($profileType, $profileTypePackage);

          //ONLY SET SELECTED PROFILE TYPE
          $profileType = $form->getElement('0_0_1')
                  ->setMultiOptions($profileTypeFinal);
          if (count($profileTypeFinal) <= 1) {
            $form->removeElement("0_0_1");
            $form->removeElement("submit");
            $error = Zend_Registry::get('Zend_Translate')->_("There are no profile fields available.");
            $form->addError($error);
          }
        }
        $fieldsProfile_2 = Engine_Api::_()->sitestore()->getLevelProfileFields($store_owner->level_id);
        $fieldsProfile = array_merge($fieldsProfile, $fieldsProfile_2);


        //PROFILE FIELD IS SELECTED BUT THERE ARE NOT ANY PROFILE FIELDS
        if (!empty($sitestore->profile_type)) {
          $profile_field_flage = true;
          foreach ($fieldsProfile_2 as $k => $v) {
            $explodeField = explode("_", $v);
            if ($explodeField['1'] == $sitestore->profile_type) {
              $profile_field_flage = false;
              break;
            }
          }

          if ($profile_field_flage) {
            $form->removeElement("submit");
            $error = Zend_Registry::get('Zend_Translate')->_("There are no profile fields available.");
            $form->addError($error);
          }
        }

        foreach ($fieldsProfile_2 as $k => $v) {
          $explodeField = explode("_", $v);
          $field_id[] = $explodeField['2'];
        }
        $elements = $form->getElements();
        foreach ($elements as $key => $value) {

          $explode = explode("_", $key);
          if ($explode['0'] != "1" && $explode['0'] != "submit") {
            if (in_array($explode['0'], $field_id)) {
              $field_id[] = $explode['2'];
              $fieldsProfile[] = $key;
              continue;
            }
          }
          if (!in_array($key, $fieldsProfile)) {
            $form->removeElement($key);
            $form->addElement('Hidden', $key, array(
                "value" => "",
            ));
          }
        }
      }//END LEVEL WORK
    }
  }

  //ACTION FOR REMOVE THE PHOTO
  public function removePhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    //GET SITESTORE ITEM
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $this->_getParam('store_id'));

    //CHECK FORM SUBMIT
    if (isset($_POST['submit']) && $_POST['submit'] == 'submit') {
      $sitestore->photo_id = 0;
      $sitestore->save();
      return $this->_helper->redirector->gotoRoute(array('action' => 'profile-picture', 'store_id' => $sitestore->store_id), 'sitestore_dashboard', true);
    }
  }

  //ACTION FOR UNHIDE THE PHOTO
  public function unhidePhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //UNHIDE PHOTO FORM
    $this->view->form = $form = new Sitestore_Form_Unhidephoto();

    //CHECK FORM VALIDAITON
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      Engine_Api::_()->getDbtable('photos', 'sitestore')->update(array('photo_hide' => 0), array('store_id = ?' => $this->_getParam('store_id', null), 'photo_hide = ?' => 1));
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your photos have been restored.'))
      ));
    }
  }

  //ACTION FOR UPLOADING THE OVERVIEWS PHOTOS FROM THE EDITOR
  public function uploadPhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //LAYOUT
    $this->_helper->layout->disableLayout();
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    //STORE ID
    $store_id = $this->_getParam('store_id');

    $special = $this->_getParam('special', 'overview');
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $can_edit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

    if ($special == 'overview') {
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'overview');
			if (empty($can_edit)) {
        return $this->_forward('requireauth', 'error', 'core');
      }

      if (empty($isManageAdmin)) {
        return $this->_forward('requireauth', 'error', 'core');
      }
     
    } else {
      $photoCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'spcreate');
			if (empty($can_edit) && empty($photoCreate)) {
				return $this->_forward('requireauth', 'error', 'core');
			}
    }
		//END MANAGE-ADMIN CHECK

    //END MANAGE-ADMIN CHECK
    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
     $fileName = Engine_Api::_()->seaocore()->tinymceEditorPhotoUploadedFileName();
    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!isset($_FILES[$fileName]) || !is_uploaded_file($_FILES[$fileName]['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    //PROCESS
    $db = Engine_Api::_()->getDbtable('photos', 'sitestore')->getAdapter();
    $db->beginTransaction();
    try {
      //CREATE PHOTO
      $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitestore');
      $photo = $tablePhoto->createRow();
      $photo->setFromArray(array(
          'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
          'store_id' => $store_id
      ));
      $photo->save();
      $photo->setPhoto($_FILES[$fileName]);

      $this->view->status = true;
      $this->view->name = $_FILES[$fileName]['name'];
      $this->view->photo_id = $photo->photo_id;
      $this->view->photo_url = $photo->getPhotoUrl();

      $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitestore');
      $album = $tableAlbum->getSpecialAlbum($sitestore, $special);
      $tablePhotoName = $tablePhoto->info('name');
      $photoSelect = $tablePhoto->select()->from($tablePhotoName, 'order')->where('album_id = ?', $album->album_id)->order('order DESC')->limit(1);
      $photo_rowinfo = $tablePhoto->fetchRow($photoSelect);
      $photo->collection_id = $album->album_id;
      $photo->album_id = $album->album_id;
      $order = 0;
      if (!empty($photo_rowinfo)) {
        $order = $photo_rowinfo->order + 1;
      }
      $photo->order = $order;
      $photo->save();

      if (!$album->photo_id) {
        $album->photo_id = $photo->file_id;
        $album->save();
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      return;
    }
  }

  //ACTION FOR Twitter CODE
  public function twitterAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    //SMOOTHBOX
    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {
      //NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    //GET STORE ID AND SITESTORE OBJECT
    $siteastore = Engine_Api::_()->getItem('sitestore_store', $this->_getParam('store_id'));

    //GENERATE FORM
    $this->view->form = $form = new Sitestore_Form_Twitter();

    //POPULATE THE FORM
    $form->populate($siteastore->toArray());

    if (!$this->getRequest()->isPost())
      return;

    //SAVE THE Twitter CODE IN DATABASE
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $db = Engine_Api::_()->getDbtable('stores', 'sitestore')->getAdapter();
      $db->beginTransaction();
      try {
        $siteastore->twitter_user_name = $_POST['twitter_user_name'];
        $siteastore->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    }

    $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your account has been added successfully! You can view your recent tweets on profile of your store.')),
        'parentRefresh' => false,
        'format' => 'smoothbox',
        'smoothboxClose' => 1500,
    ));
  }

  //ACTION FOR Twitter CODE
  public function facebookAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    //SMOOTHBOX
    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {
      //NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    //GET STORE ID AND SITESTORE OBJECT
    $siteastore = Engine_Api::_()->getItem('sitestore_store', $this->_getParam('store_id'));

    //GENERATE FORM
    $this->view->form = $form = new Sitestore_Form_Facebook();

    //POPULATE THE FORM
    $form->populate($siteastore->toArray());

    if (!$this->getRequest()->isPost())
      return;

    //SAVE THE Twitter CODE IN DATABASE
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $db = Engine_Api::_()->getDbtable('stores', 'sitestore')->getAdapter();
      $db->beginTransaction();
      try {
        $siteastore->fbpage_url = $_POST['fbpage_url'];
        $siteastore->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    }

    $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your Facebook Page has been successfully linked.')),
        'parentRefresh' => false,
        'format' => 'smoothbox',
        'smoothboxClose' => 1500,
    ));
  }

  public function manageMemberCategoryAction() {

    //CHECK PERMISSION FOR VIEW.
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION.
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    $this->view->sitestores_view_menu = 40;

    //GETTING THE OBJECT AND STORE ID.
    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $this->view->is_ajax = $this->_getParam('is_ajax', '');

    //EDIT PRIVACY
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

    $manageAdminAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1);
    $manageMemberSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoremember.category.settings', 1);

    if (empty($isManageAdmin) || empty($manageAdminAllowed)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if ($manageMemberSettings == 3) {
      $is_admincreated = array("0" => 0, "1" => 1);
      $store_id = array("0" => 0, "1" => $store_id);
    } elseif ($manageMemberSettings == 2) {
      $is_admincreated = array("0" => 0);
      $store_id = array("1" => $store_id);
    }

    $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitestoremember');
    $rolesTableName = $rolesTable->info('name');
    $select = $rolesTable->select()
            ->from($rolesTableName)
            ->where($rolesTableName . '.is_admincreated IN (?)', (array) $is_admincreated)
            ->where($rolesTableName . '.store_id IN (?)', (array) $store_id)
            ->where($rolesTableName . '.store_category_id = ? ', $sitestore->category_id)
            ->order('role_id DESC');
    $this->view->manageRolesHistories = $rolesTable->fetchALL($select);

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      $row = $rolesTable->createRow();
      $row->is_admincreated = 0;
      $row->role_name = $values['category_name'];
      $row->store_category_id = $sitestore->category_id;
      $row->store_id = $sitestore->store_id;
      $row->save();
    }
  }
  
  public function editRoleAction() {

    $role_id = (int) $this->_getParam('role_id');
    $store_id = (int) $this->_getParam('store_id');

    $role = Engine_Api::_()->getItem('sitestoremember_roles', $role_id);
    
    $this->view->form = $form = new Sitestore_Form_EditRole();
    $form->populate($role->toArray());

    $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitestoremember');
    
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    // Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try {
			$values = $form->getValues();
			$role->setFromArray($values);
			$role->save();
			$db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
		$this->_forward('success', 'utility', 'core', array(
			'smoothboxClose' => 500,
			'parentRedirect' => $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id), 'action' => 'manage-member-category',  'store_id' => $store_id ), 'sitestore_dashboard', true),
			'parentRedirectTime' => '2',
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('Roles has been edited successfully.'))
		));
  }
  
  //THIS ACTION FOR DELETE MANAGE ADMIN AND CALLING FROM THE CORE.JS FILE.
  public function deleteMemberCategoryAction() {

    $role_id = (int) $this->_getParam('category_id');
    $store_id = (int) $this->_getParam('store_id');
    $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitestoremember');
    $rolesTable->delete(array('role_id = ?' => $role_id, 'store_id = ?' => $store_id));
  }

  public function resetPositionCoverPhotoAction() {
    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;
    //GET STORE ID
    $store_id = $this->_getParam("store_id");
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    ////START MANAGE-ADMIN CHECK
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($can_edit))
      return;
    $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitestore');
    $album = $tableAlbum->getSpecialAlbum($sitestore, 'cover');
    $album->cover_params = $this->_getParam('position', array('top' => '0', 'left' => 0));
    $album->save();
  }

  public function getAlbumsPhotosAction() {
    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;
    $sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
    if (!$sitestorealbumEnabled) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //GET STORE ID
    $store_id = $this->_getParam("store_id");
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    ////START MANAGE-ADMIN CHECK
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($can_edit))
      return;
    //FETCH ALBUMS
    $this->view->recentAdded = $recentAdded = $this->_getParam("recent", false);
    $this->view->album_id = $album_id = $this->_getParam("album_id");
    if ($album_id) {
      $this->view->album = $album = Engine_Api::_()->getItem('sitestore_album', $album_id);
      $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
      $paginator->setItemCountPerPage(10000);
    } elseif ($recentAdded) {
      $paginator = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotos(array('store_id' => $store_id, 'orderby' => 'photo_id DESC', 'start' => 0, 'end' => 100));
    } else {
      $paramsAlbum['store_id'] = $store_id;
      $paginator = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbums($paramsAlbum);
    }
    $this->view->paginator = $paginator;
  }

  public function uploadCoverPhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //LAYOUT
    $this->_helper->layout->setLayout('default-simple');
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    //STORE ID
    $store_id = $this->_getParam('store_id');

    $special = $this->_getParam('special', 'cover');
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //GET FORM
    $this->view->form = $form = new Sitestore_Form_Photo_Cover();

    //CHECK FORM VALIDATION
    $file='';
    $notNeedToCreate=false;
    $photo_id = $this->_getParam('photo_id');
    if ($photo_id) {
      $photo = Engine_Api::_()->getItem('sitestore_photo', $photo_id);
      $album = Engine_Api::_()->getItem('sitestore_album', $photo->album_id);
      if ($album && $album->type == 'cover') {
        $notNeedToCreate = true;
      }
      if ($photo->file_id && !$notNeedToCreate)
        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo->file_id);
    }

    if (empty($photo_id) || empty($photo)) {
      if (!$this->getRequest()->isPost()) {
        return;
      }

      //CHECK FORM VALIDATION
      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }
    }
    //UPLOAD PHOTO
    if ($form->Filedata->getValue() !== null || $photo || ($notNeedToCreate && $file)) {
      //PROCESS
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //CREATE PHOTO
        $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitestore');
        if (!$notNeedToCreate) {
          $photo = $tablePhoto->createRow();
          $photo->setFromArray(array(
              'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
              'store_id' => $store_id
          ));
          $photo->save();
          if ($file) {
            $photo->setPhoto($file);
          } else {
            $photo->setPhoto($form->Filedata,true);
          }


          $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitestore');
          $album = $tableAlbum->getSpecialAlbum($sitestore, $special);

          $tablePhotoName = $tablePhoto->info('name');
          $photoSelect = $tablePhoto->select()->from($tablePhotoName, 'order')->where('album_id = ?', $album->album_id)->order('order DESC')->limit(1);
          $photo_rowinfo = $tablePhoto->fetchRow($photoSelect);
          $photo->collection_id = $album->album_id;
          $photo->album_id = $album->album_id;
          $order = 0;
          if (!empty($photo_rowinfo)) {
            $order = $photo_rowinfo->order + 1;
          }
          $photo->order = $order;
          $photo->save();
        }

        $album->cover_params = $this->_getParam('position', array('top' => '0', 'left' => 0));
        $album->save();
        if (!$album->photo_id) {
          $album->photo_id = $photo->file_id;
          $album->save();
        }
        $sitestore->store_cover = $photo->photo_id;
        $sitestore->save();
        //ADD ACTIVITY
        $viewer = Engine_Api::_()->user()->getViewer();
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $activityFeedType = null;
        if (Engine_Api::_()->sitestore()->isStoreOwner($sitestore) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
          $activityFeedType = 'sitestore_admin_cover_update';
        elseif ($sitestore->all_post || Engine_Api::_()->sitestore()->isStoreOwner($sitestore))
          $activityFeedType = 'sitestore_cover_update';


        if ($activityFeedType) {
          $action = $activityApi->addActivity($viewer, $sitestore, $activityFeedType);
        }
        if ($action) {
          Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
          if ($photo)
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
        }

        $this->view->status = true;
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
        return;
      }
    }
  }

  public function removeCoverPhotoAction() {
    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    $store_id = $this->_getParam('store_id');
    if ($this->getRequest()->isPost()) {
      $special = $this->_getParam('special', 'cover');
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      $sitestore->store_cover = 0;
      $sitestore->save();
      $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitestore');
      $album = $tableAlbum->getSpecialAlbum($sitestore, $special);
      $album->cover_params = array('top' => '0', 'left' => 0);
      $album->save();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
  }
  
  public function storeAction() {
    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;
    
    if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
      return;
    
    if (!Engine_Api::_()->core()->hasSubject()) 
      return $this->_forward('notfound', 'error', 'core');

    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
//    $authValue =  Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS STORE ADMIN OR NOT
//    if(empty($authValue))
//       return $this->_forward('requireauth', 'error', 'core');
//    else if($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');
    
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $this->view->showMethod = $methodName =  $this->_getParam('method', 'manage');
    
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if(empty($isManageAdmin))
      return $this->_forward('requireauth', 'error', 'core');
    
    //PRIVATE STORE FOR STORE ADMIN NOT STORE OWNER IN PAYMENT INFO CASE
    if($methodName == 'payment-info' && $sitestore->owner_id != $viewer_id && $viewer->level_id != 1)
      return $this->_forward('requireauth', 'error', 'core');
    
    //GET STORE ID AND STORE SUBJECT
    $this->view->is_ajax = $this->_getParam('is_ajax', false);
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');    
    $this->view->sitestores_view_menu = $this->_getParam('menuId', 62);

    //GET PHOTO ID
    $this->view->photo_id = $subject->photo_id;    

    $this->view->showMenu = $this->_getParam('menuId', 62);
    
    $this->view->tax_id = $tax_id = $this->_getParam('tax_id', 0);
    //AUTHENTICATE TAXID FOR VALID ACCESS
    if($methodName == 'manage-rate'){
      if(empty($tax_id))
        return $this->_forward('notfound', 'error', 'core');
      else{
        $taxObj = Engine_Api::_()->getItem('sitestoreproduct_taxes', $tax_id);
        if(empty($taxObj))
          return $this->_forward('notfound', 'error', 'core');
        else if($viewer->level_id != 1){
          $authValue =  Engine_Api::_()->sitestoreproduct()->isStoreAdmin($taxObj->store_id);

          //IS USER IS STORE ADMIN OR NOT
          if(empty($authValue))
             return $this->_forward('requireauth', 'error', 'core');
        }
      }
    }
    
    $this->view->storeNo = $this->_getParam('storeno', 0);
    $this->view->admin_calling = $this->_getParam('admin_calling', 0);
    $this->view->store = $this->_getParam('store', 1);
    $this->view->order_id = $this->_getParam('order_id', 0);
    $this->view->showType = $this->_getParam('type', 'product');
    $this->view->showId = $this->_getParam('id', 0);
    $this->view->notice = $this->_getParam('notice', 0);
    $this->view->method_id = $this->_getParam('method_id', 0);
    $this->view->month = $this->_getParam('month', 0);
    $this->view->year = $this->_getParam('year', 0);
    $this->view->tab = $this->_getParam('tab', 0);
    $this->view->sections = $this->_getParam('sections', 0);
    $this->view->task = $this->_getParam('task', 0);
    $this->view->tag_id = $this->_getParam('tag_id', 0);
  }

public function uploadKycAction() {
        if (!Engine_Api::_()->core()->hasSubject('sitestore_store')) {
            return;
        } else {
            $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject();
            $this->view->store_id = $store_id = $sitestore->store_id;
        }
        $this->view->is_ajax = $this->_getParam('is_ajax', '');
        $this->view->form = $form = new Sitegateway_Form_Order_Kyc();
        $sitegatewayApi = Engine_Api::_()->sitegateway();
        $this->view->adminGateway =$adminAPGateway= $sitegatewayApi->getAdminPaymentGateway('Sitegateway_Plugin_Gateway_MangoPay');
        $store_gateway_obj = Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $sitestore->store_id, 'plugin = \'Sitegateway_Plugin_Gateway_MangoPay\''));

      $mode = 'live';
        if ($adminAPGateway->config['test_mode']) {
            $mode = 'sandbox';
        }
        
        $this->view->mangoPayUserId = $store_gateway_obj->config[$mode]['mangopay_user_id'];
           $this->view->mangopayuser=true;
        if (!(isset($store_gateway_obj->config[$mode]['mangopay_user_id']) && !empty($store_gateway_obj->config[$mode]['mangopay_user_id']))) {
          $this->view->mangopayuser=false;
            $form->addError($this->view->translate('Please configure your MangoPay payment method'));
            return;
        }
        if (!$this->getRequest()->isPost()) {
            return;
        }
        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        //CREATE DOCUMENT
        if (!empty($values['page'])) {
            $store_gateway_obj->uploadKYC($form->page, $values['document_type'], $values['tag']);
        }
        $form->reset();
    }

}
