<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_IndexController extends Seaocore_Controller_Action_Standard {


  public function init() {

    //GET STORE ID
    $store_id = $this->_getParam('store_id');

    //PACKAGE BASE PRIYACY START
    if (!empty($store_id)) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo")) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
        if (empty($isStoreOwnerAllow)) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END    
    else {
      if ($this->_getParam('video_id') != null) {
        $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $this->_getParam('video_id'));
        $store_id = $sitestorevideo->store_id;
      }
    }

    //GET VIDEO ID
    $video_id = $this->_getParam('video_id');
    if ($video_id) {
      $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);
      if ($sitestorevideo) {
        Engine_Api::_()->core()->setSubject($sitestorevideo);
      }
    }
  }
  
  //ACTION FOR CREATE VIDEO
  public function createAction() {

    if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      //UPLOAD VIDEO
      if( isset($_GET['ul'])){
       return $this->_forward('upload-video', null, null, array('format' => 'json'));
      }
      if( isset($_FILES['Filedata']) && !empty($_FILES['Filedata']['name'])){ 
        $_POST['id'] = $this->uploadVideoAction();
      }
    }

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //GET STORE ID
    $store_id = $this->_getParam('store_id');

    //GET TAB ID
    $this->view->tab_selected_id = $tab_selected_id = $this->_getParam('tab');

    $getPackagevideoCreate = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestorevideo');
    $sitestoreModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

    //GET SITESTORE OBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
   
    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
      if (empty($isStoreOwnerAllow) ) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    // PACKAGE BASE PRIYACY END
    //VIDEO UPLOAD PROCESS
    $this->view->imageUpload = Engine_Api::_()->sitestorevideo()->isUpload();

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

     $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
    if (empty($isManageAdmin) && empty($can_edit)) {

      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //FORM GENERATON

    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$this->view->form = $form = new Sitestorevideo_Form_Video();
    } else {
      $this->view->form = $form = new Sitestorevideo_Form_SitemobileVideo();
    }

    if ($this->_getParam('type', false))
      $form->getElement('type')->setValue( $this->_getParam('type') );
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues('url');
      return;
    }

    if (empty($getPackagevideoCreate)) {
      return;
    }

    $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.set.type', 0);
    if (empty($isModType)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestorevideo.utility.type', convert_uuencode($sitestoreModHostName));
    }

    //GET FORM VALUES
    $values = $form->getValues();

    $values['owner_id'] = $viewer->getIdentity();

    //VIDEO CREATION PROCESS
    $videoTable = Engine_Api::_()->getDbtable('videos', 'sitestorevideo');

    $insert_action = false;
    
    $db = $videoTable->getAdapter();
    $db->beginTransaction();
    try {

      if ($values['type'] == 3) {
        $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $this->_getParam('id'));
        if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
          if (!$this->_helper->requireUser()->checkRequire()) {
              $this->view->status = false;
              $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
              return;
            }

            if (!$this->getRequest()->isPost()) {
              $this->view->status = false;
              $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
              return;
            }

            $values = $this->getRequest()->getPost();

            $this->view->clear_cache = true;
            if( empty($_FILES['Filedata']) ){
              $this->view->status = false;
              $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
              return;
            }

            if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
              $this->view->status = false;
              $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . print_r($_FILES, true);
              return;
            }

            $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
            if (in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions)) {
              $this->view->status = false;
              $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
              return;
            }

              $viewer = Engine_Api::_()->user()->getViewer();
              $values['owner_id'] = $viewer->getIdentity();

              $params = array(
                  'owner_id' => $viewer->getIdentity()
              );
              $sitestorevideo = Engine_Api::_()->sitestorevideo()->createSitestorevideo($params, $_FILES['Filedata'], $values);
              $sitestorevideo->title = $_FILES['Filedata']['name'];
              $sitestorevideo->owner_id = $viewer->getIdentity();
//              $sitestorevideo->save();
              $this->view->status = true;
              $this->view->name = $_FILES['Filedata']['name'];
              $this->view->code = $sitestorevideo->code;
              $this->view->video_id = $sitestorevideo->video_id;
            
          } 
      } else {
        $sitestorevideo = $videoTable->createRow();
      }

      $sitestorevideo->setFromArray($values);
      $sitestorevideo->store_id = $this->_getParam('store_id');
      $sitestorevideo->save();

      //THUMBNAIL CREATION
      $thumbnail = $this->handleThumbnail($sitestorevideo->type, $sitestorevideo->code);
      $ext = ltrim(strrchr($thumbnail, '.'), '.');
      $thumbnail_parsed = @parse_url($thumbnail);

      if (@GetImageSize($thumbnail)) {
        $valid_thumb = true;
      } else {
        $valid_thumb = false;
      }

      if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
        $src_fh = fopen($thumbnail, 'r');
        $tmp_fh = fopen($tmp_file, 'w');
        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
        $image = Engine_Image::factory();
        $image->open($tmp_file)
                ->resize(120, 240)
                ->write($thumb_file)
                ->destroy();

        try {
          $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
              'parent_type' => $sitestorevideo->getType(),
              'parent_id' => $sitestorevideo->getIdentity()
                  ));

          //REMOVE TEMP FILES
          @unlink($thumb_file);
          @unlink($tmp_file);
        } catch (Exception $e) {
          
        }
        $information = $this->handleInformation($sitestorevideo->type, $sitestorevideo->code);
        $sitestorevideo->duration = $information['duration'];
        $sitestorevideo->photo_id = $thumbFileRow->file_id;
        $sitestorevideo->status = 1;
        $sitestorevideo->featured = 0;
        $sitestorevideo->save();

        //INSERT NEW ACTION ITEM
        $insert_action = true;
      }

      if ($values['ignore'] == true) {
        $sitestorevideo->status = 1;
        $sitestorevideo->save();
        $insert_action = true;
      }

      //COMMENT PRIVACY
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
      $auth_comment = "everyone";
      $commentMax = array_search($auth_comment, $roles);
      foreach ($roles as $i => $role) {
        $auth->setAllowed($sitestorevideo, $role, 'comment', ($i <= $commentMax));
      }

      //TAG WORK
      if (!empty($values['tags'])) {
        $tags = preg_split('/[,]+/', $values['tags']);
        $sitestorevideo->tags()->addTagMaps($viewer, $tags);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    try {
      if ($insert_action && $sitestorevideo->search == 1) {
        $owner = $sitestorevideo->getOwner();
        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $activityFeedType = null;
        if (Engine_Api::_()->sitestore()->isStoreOwner($sitestore) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
          $activityFeedType = 'sitestorevideo_admin_new';
        elseif ($sitestore->all_post || Engine_Api::_()->sitestore()->isStoreOwner($sitestore))
          $activityFeedType = 'sitestorevideo_new';

        if ($activityFeedType) {
          $action = $actionTable->addActivity($owner, $sitestore, $activityFeedType);
          Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
        }
        if ($action != null) {
          $actionTable->attachActivity($action, $sitestorevideo);
        }

        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {

          $video_array = array();
          $video_array['type'] = 'sitestorevideo_new';
          $video_array['object'] = $sitestorevideo;

          Engine_Api::_()->facebooksefeed()->sendFacebookFeed($video_array);
        }

				//STORE VIDEO CREATE NOTIFICATION AND EMAIL WORK
				$sitestoreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitestore')->version;
				if (!empty($action)) {
					Engine_Api::_()->sitestore()->sendNotificationEmail($sitestorevideo, $action, 'sitestorevideo_create', 'SITESTOREVIDEO_CREATENOTIFICATION_EMAIL');
					$isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $store_id);
						if (!empty($isStoreAdmins)) {
							//NOTIFICATION FOR ALL FOLLWERS.
							Engine_Api::_()->sitestore()->sendNotificationToFollowers($sitestorevideo, $action, 'sitestorevideo_create');
						}
				}
      }

      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($sitestorevideo) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_helper->redirector->gotoRoute(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', true);
  }

  //ACTION FOR EDIT VIDEO
  public function editAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    //GET STORE ID AND SUBJECT
    $store_id = $this->_getParam('store_id', 0);
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //GET VIDEO OBJECT
    $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $this->_getParam('video_id'));

    //GET TAB ID
    $this->view->tab_selected_id = $this->_getParam('tab');

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
      if (empty($isStoreOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //SUPERADMIN, VIDEO OWNER AND STORE OWNER CAN EDIT VIDEO
    if ($viewer_id != $sitestorevideo->owner_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //FORM GENERATION
    $this->view->form = $form = new Sitestorevideo_Form_Edit();

    //PREPARE TAGS
    $sitestoreTags = $sitestorevideo->tags()->getTagMaps();
    $tagString = '';
    foreach ($sitestoreTags as $tagmap) {
      if ($tagString !== '') {
        $tagString .= ', ';
      }
      $tagString .= $tagmap->getTag()->getTitle();
    }
    $this->view->tagNamePrepared = $tagString;
    $form->tags->setValue($tagString);

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$this->getRequest()->isPost()) {
      $form->populate($sitestorevideo->toArray());
      return;
    }

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    //GET FORM VALUES
    $values = $form->getValues();

    //PROCESS
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitestorevideo->setFromArray($values);

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $sitestorevideo->tags()->setTagMaps($viewer, $tags);
      $sitestorevideo->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $store_id = $this->_getParam('store_id');

    //REDIRECTING TO THE EVENT VIEW STORE
    return $this->_redirectCustom($sitestorevideo->getHref(), array('prependBase' => false));
  }

  //ACTION FOR DELETE VIDEO
  public function deleteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET TAB ID
    $this->view->tab_selected_id = $tab_selected_id = $this->_getParam('tab');

    //GET VIDEO OBJECT
    $this->view->sitestorevideo = $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $this->getRequest()->getParam('video_id'));

    //GET VIDEO TITLE
    $this->view->title = $sitestorevideo->title;

    //GET STORE ID
    $store_id = $sitestorevideo->store_id;

    //GET NAVIGATION 
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //GET SITESTORE SUBJECT
    if (!empty($store_id)) {
      $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo")) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
        if (empty($isStoreOwnerAllow)) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      }
      //PACKAGE BASE PRIYACY END
      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
      if (empty($isManageAdmin)) {
        $can_edit = 0;
      } else {
        $can_edit = 1;
      }
      //END MANAGE-ADMIN CHECK
    } else {
      $can_edit = 1;
    }
    if (!$sitestorevideo) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
      return;
    }

    //VIDEO OWNER AND STORE OWNER CAN DELETE VIDEO
    if ($viewer_id != $sitestorevideo->owner_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $sitestorevideo->getTable()->getAdapter();
    $db->beginTransaction();

    try {

      Engine_Api::_()->getDbtable('ratings', 'sitestorevideo')->delete(array('video_id =?' => $this->getRequest()->getParam('video_id')));

      $sitestorevideo->delete();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    return $this->_gotoRouteCustom(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', true);
  }

  //ACTION FOR VIEW VIDEO
  public function viewAction() {

    //IF SITESTOREVIDEO SUBJECT IS NOT THEN RETURN
    if (!$this->_helper->requireSubject('sitestorevideo_video')->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        if(!Zend_Registry::isRegistered('sitemobileNavigationName')){
        Zend_Registry::set('sitemobileNavigationName','setNoRender');
        }
    }
    
    //GET VIDEO ITEM
    $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $this->getRequest()->getParam('video_id'));

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestorevideo->store_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
      if (empty($isStoreOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
    if (empty($isManageAdmin)) {
      $this->view->can_create = 0;
    } else {
      $this->view->can_create = 1;
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //CHECKING THE USER HAVE THE PERMISSION TO VIEW THE VIDEO OR NOT
    if ($viewer_id != $sitestorevideo->owner_id && $can_edit != 1 && ($sitestorevideo->search != 1 || $sitestorevideo->status != 1)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

   //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  //ACTION FOR DO RATING
  public function rateAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();

    $rating = $this->_getParam('rating');
    $video_id = $this->_getParam('video_id');

    $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitestorevideo');
    $db = $ratingTable->getAdapter();
    $db->beginTransaction();

    try {

      $ratingTable->setRating($video_id, $user_id, $rating);

      $total = $ratingTable->ratingCount($video_id);

      $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);

      //UPDATE CURRENT AVERAGE RATING IN VIDEO TABLE
      $rating = $ratingTable->rateVideo($video_id);

      $sitestorevideo->rating = $rating;
      $sitestorevideo->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $data = array();
    $data[] = array(
        'total' => $total,
        'rating' => $rating,
    );
    return $this->_helper->json($data);
    $data = Zend_Json::encode($data);
    $this->getResponse()->setBody($data);
  }

  //ACTION FOR HANDLES THUMBNAIL
  public function handleThumbnail($type, $code = null) {
    switch ($type) {
      //youtube
      case "1":
        //https://i.ytimg.com/vi/Y75eFjjgAEc/default.jpg
        return "https://i.ytimg.com/vi/$code/default.jpg";
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        return $thumbnail;
    }
  }

  //ACTION FOR VIDEO COMPOSE UPLOAD
  public function composeUploadAction() {

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->_redirect('login');
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    $video_title = $this->_getParam('title');
    $video_url = $this->_getParam('uri');
    $video_type = $this->_getParam('type');
    $composer_type = $this->_getParam('c_type', 'wall');

    // extract code
    //$code = $this->extractCode("http://www.youtube.com/watch?v=5osJ8-NttnU&feature=popt00us08", $video_type);
    //$code = parse_url("http://vimeo.com/3945157/asd243", PHP_URL_PATH);

    $code = $this->extractCode($video_url, $video_type);
    // check if code is valid
    // check which API should be used
    if ($video_type == 1) {
      $valid = $this->checkYouTube($code);
    }
    if ($video_type == 2) {
      $valid = $this->checkVimeo($code);
    }


    // check to make sure the user has not met their quota of # of allowed video uploads
    // set up data needed to check quota
    //$values['user_id'] = $viewer->getIdentity();
    //  $paginator = Engine_Api::_()->getItemTable('sitestorevideo_video')->getSitestorevideosPaginator($values);

    if ($valid) {
      $db = Engine_Api::_()->getDbtable('videos', 'sitestorevideo')->getAdapter();
      $db->beginTransaction();

      try {
        $information = $this->handleInformation($video_type, $code);
        // create video
        $table = Engine_Api::_()->getDbtable('videos', 'sitestorevideo');
        $video = $table->createRow();
        $video->title = $information['title'];
        $video->description = $information['description'];
        $video->duration = $information['duration'];
        $video->owner_id = $viewer->getIdentity();
        $video->code = $code;
        $video->type = $video_type;
        $video->save();

        // Now try to create thumbnail
        $thumbnail = $this->handleThumbnail($video->type, $video->code);
        $ext = ltrim(strrchr($thumbnail, '.'), '.');
        $thumbnail_parsed = @parse_url($thumbnail);

        $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

        $src_fh = fopen($thumbnail, 'r');
        $tmp_fh = fopen($tmp_file, 'w');
        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

        $image = Engine_Image::factory();
        $image->open($tmp_file)
                ->resize(120, 240)
                ->write($thumb_file)
                ->destroy();

        $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
                ));

        // If video is from the composer, keep it hidden until the post is complete
        if ($composer_type)
          $video->search = 0;

        $video->photo_id = $thumbFileRow->file_id;
        $video->status = 1;
        $video->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }


      // make the video public
      if ($composer_type === 'wall') {
        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ($roles as $i => $role) {
          $auth->setAllowed($video, $role, 'view', ($i <= $roles));
          $auth->setAllowed($video, $role, 'comment', ($i <= $roles));
        }
      }

      $this->view->status = true;
      $this->view->video_id = $video->video_id;
      $this->view->photo_id = $video->photo_id;
      $this->view->title = $video->title;
      $this->view->description = $video->description;
      $this->view->src = $video->getPhotoUrl();
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video posted successfully');
    } else {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('We could not find a video there - please check the URL and try again.');
    }
  }

  //ACTION FOR VIDEO VALIDATON
  public function validationAction() {
    global $sitestorevideo_validation;
    $video_type = $this->_getParam('type');
    $code = $this->_getParam('code');
    $ajax = $this->_getParam('ajax', false);
    $valid = false;

    if (!empty($sitestorevideo_validation)) {
      // check which API should be used
      if ($video_type == "youtube") {
        $valid = $this->checkYouTube($code);
      }
      if ($video_type == "vimeo") {
        $valid = $this->checkVimeo($code);
      }

      $this->view->code = $code;
      $this->view->ajax = $ajax;
      $this->view->valid = $valid;
    }
  }

  //HELPER FUNCTIONS
  public function extractCode($url, $type) {
    switch ($type) {
      //youtube
      case "1":
        // change new youtube URL to old one
        $new_code = @pathinfo($url);
        $url = preg_replace("/#!/", "?", $url);

        // get v variable from the url
        $arr = array();
        $arr = @parse_url($url);
        if ($arr['host'] === 'youtu.be') {
          $data = explode("?", $new_code['basename']);
          $code = $data[0];
        } else {
          $parameters = $arr["query"];
          parse_str($parameters, $data);
          $code = $data['v'];
          if ($code == "") {
            $code = $new_code['basename'];
          }
        }
        return $code;
      //vimeo
      case "2":
        // get the first variable after slash
        $code = @pathinfo($url);
        return $code['basename'];
    }
  }

  // YouTube Functions
  public function checkYouTube($code){
    $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
    if (!$data = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $code . '&key=' . $key)) return false;

    $data = Zend_Json::decode($data);
    if (empty($data['items'])) return false;
    return true;
  }

  //VIMEO FUNCTIONS
  public function checkVimeo($code) {
    //http://www.vimeo.com/api/docs/simple-api
    //http://vimeo.com/api/v2/video
    $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
    $video_id = count($data->video->id);
    if ($video_id == 0)
      return false;
    return true;
  }

  //ACTION FOR UPLOAD VIDEO
  public function uploadVideoAction() {
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();

    if( empty($_FILES['Filedata']) ){
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . print_r($_FILES, true);
      return;
    }

    $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
    if (in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('videos', 'sitestorevideo')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $values['owner_id'] = $viewer->getIdentity();

      $params = array(
          'owner_id' => $viewer->getIdentity()
      );
      $video = Engine_Api::_()->sitestorevideo()->createSitestorevideo($params, $_FILES['Filedata'], $values);
      $video->title = $_FILES['Filedata']['name'];
      $video->owner_id = $viewer->getIdentity();
      $video->save();
      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->code = $video->code;
      $this->view->video_id = $video->video_id;
      $db->commit();
      return $video->video_id;
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.') . $e;
      // throw $e;
      return;
    }
  }

  //ACTION FOR UPLOAD VIDEO
  public function uploadAction() {
    if (isset($_GET['ul']) || isset($_FILES['Filedata']))
      return $this->_forwardCustom('upload-video', null, null, array('format' => 'json'));

    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->form = $form = new Sitestorevideo_Form_Video();
    $this->view->navigation = $this->getNavigation();

    if (!$this->getRequest()->isPost()) {
      if (null !== ($video_id = $this->_getParam('video_id'))) {
        $form->populate(array(
            'video' => $video_id
        ));
      }
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $video = $form->saveValues();
  }

  //ACTION FOR HANDLE INFORMATION
  public function handleInformation($type, $code) {
    switch ($type) {
      //youtube
      case "1":
        $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
        $data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $code . '&key=' . $key);
        if (empty($data)) {
          return;
        }
        $data = Zend_Json::decode($data);
        $information = array();
        $youtube_video = $data['items'][0];
        $information['title'] = $youtube_video['snippet']['title'];
        $information['description'] = $youtube_video['snippet']['description'];
        $information['duration'] = Engine_Date::convertISO8601IntoSeconds($youtube_video['contentDetails']['duration']);
        return $information;
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        $information = array();
        $information['title'] = $data->video->title;
        $information['description'] = $data->video->description;
        $information['duration'] = $data->video->duration;
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return $information;
    }
  }

  //ACTION FOR MAKE THE SITESTOREVIDEO FEATURED/UNFEATURED
  public function featuredAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIDEO ID AND OBJECT
    $video_id = $this->view->video_id = $this->_getParam('video_id');
    $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);

    $this->view->featured = $sitestorevideo->featured;

    //GET STORE OBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestorevideo->store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    $this->view->canEdit = 0;
    if (!empty($isManageAdmin)) {
      $this->view->canEdit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //SMOOTHBOX
    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {//NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    if (!$this->getRequest()->isPost())
      return;

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $tab_selected_id = $this->_getParam('tab');

    //CHECK THAT FEATURED ACTION IS ALLOWED BY ADMIN OR NOT
    //CHECK CAN MAKE FEATURED OR NOT(ONLY SITESTORE VIDEO CAN MAKE FEATURED/UN-FEATURED)
    if ($viewer_id == $sitestorevideo->owner_id || !empty($this->view->canEdit)) {
      $this->view->permission = true;
      $this->view->success = false;
      $db = Engine_Api::_()->getDbtable('videos', 'sitestorevideo')->getAdapter();
      $db->beginTransaction();
      try {
        if ($sitestorevideo->featured == 0) {
          $sitestorevideo->featured = 1;
        } else {
          $sitestorevideo->featured = 0;
        }

        $sitestorevideo->save();
        $db->commit();
        $this->view->success = true;
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    } else {
      $this->view->permission = false;
    }

    if ($sitestorevideo->featured) {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Video successfully made featured.'));
    } else {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Video successfully made un-featured.'));
    }

    $this->_forwardCustom('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($sitestorevideo->store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', true),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => $suc_msg
    ));
  }

   //ACTION FOR MAKE THE SITESTOREVIDEO FEATURED/UNFEATURED
  public function highlightedAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIDEO ID AND OBJECT
    $video_id = $this->view->video_id = $this->_getParam('video_id');
    $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);

    $this->view->highlighted = $sitestorevideo->highlighted;

    //GET STORE OBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestorevideo->store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    $this->view->canEdit = 0;
    if (!empty($isManageAdmin)) {
      $this->view->canEdit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //SMOOTHBOX
    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {//NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    if (!$this->getRequest()->isPost())
      return;

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $tab_selected_id = $this->_getParam('tab');

    //CHECK THAT FEATURED ACTION IS ALLOWED BY ADMIN OR NOT
    //CHECK CAN MAKE FEATURED OR NOT(ONLY SITESTORE VIDEO CAN MAKE FEATURED/UN-FEATURED)
    if ($viewer_id == $sitestorevideo->owner_id || !empty($this->view->canEdit)) {
      $this->view->permission = true;
      $this->view->success = false;
      $db = Engine_Api::_()->getDbtable('videos', 'sitestorevideo')->getAdapter();
      $db->beginTransaction();
      try {
        if ($sitestorevideo->highlighted == 0) {
          $sitestorevideo->highlighted = 1;
        } else {
          $sitestorevideo->highlighted = 0;
        }

        $sitestorevideo->save();
        $db->commit();
        $this->view->success = true;
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    } else {
      $this->view->permission = false;
    }

    if ($sitestorevideo->highlighted) {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Video successfully made highlighted.'));
    } else {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Video successfully made un-highlighted.'));
    }

    $this->_forwardCustom('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($sitestorevideo->store_id), 'tab' => $tab_selected_id), 'sitestore_entry_view', true),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => $suc_msg
    ));
  }

  public function browseAction() {

    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->isValid())
      return;

   //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  // ACTION FOR FEATURED VIDEOS CAROUSEL AFTER CLICK ON BUTTON 
  public function featuredVideosCarouselAction() {
    //RETRIVE THE VALUE OF ITEM VISIBLE
    $this->view->itemsVisible = $limit = (int) $_GET['itemsVisible'];

    //RETRIVE THE VALUE OF NUMBER OF ROW
    $this->view->noOfRow = (int) $_GET['noOfRow'];

    //RETRIVE THE VALUE OF ITEM VISIBLE IN ONE ROW
    $this->view->inOneRow = (int) $_GET['inOneRow'];

    // Total Count Featured Photos
    $totalCount = (int) $_GET['totalItem'];

    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'] * $limit;

    if ($startindex > $totalCount) {
      $startindex = $totalCount - $limit;
    }
    if ($startindex < 0)
      $startindex = 0;

    $params = array();
    $params['category_id'] = $_GET['category_id'];
    $params['zero_count'] = 'featured';

    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $direction = $_GET['direction'];
    $this->view->offset = $params['start_index'] = $startindex;

    //GET Featured Photos with limit * 2
    $this->view->totalItemsInSlide = $params['limit'] = $limit * 2;
    $this->view->featuredVideos = $this->view->featuredVideos = $featuredVideos = Engine_Api::_()->getDbTable('videos', 'sitestorevideo')->widgetVideosData($params);

    //Pass the total number of result in tpl file
    $this->view->count = count($featuredVideos);

    //Pass the direction of button in tpl file
    $this->view->direction = $direction;
  }

  public function homeAction() {
 
    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->isValid())
      return;

     //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else { 
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }

  }

  //ACTION FOR ADDING VIDEO OF THE DAY
  public function addVideoOfDayAction() {
    //FORM GENERATION
    $form = $this->view->form = new Sitestorevideo_Form_ItemOfDayday();
    $video_id = $this->_getParam('video_id');
   // $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore');

				//FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $video_id)->where('resource_type = ?', 'sitestorevideo_video');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $video_id;
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitestorevideo_video';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Video of the Day has been added successfully.'))
              ));
    }
  }

 //ACTION FOR CONSTRUCT TAG CLOUD
  public function tagsCloudAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //GENERATE TAG-CLOULD HIDDEN FROM
    $this->view->form = $form = new Sitestore_Form_Searchtagcloud();

    //CONSTRUCTING TAG CLOUD
    $tag_array = array();
    $tag_cloud_array = Engine_Api::_()->getDbtable('videos', 'sitestorevideo')->getTagCloud();
    $tag_id_array = array();
    foreach ($tag_cloud_array as $vales) {
      $tag_array[$vales['text']] = $vales['Frequency'];
      $tag_id_array[$vales['text']] = $vales['tag_id'];
    }

    if (!empty($tag_array)) {
      $max_font_size = 18;
      $min_font_size = 12;
      $max_frequency = max(array_values($tag_array));
      $min_frequency = min(array_values($tag_array));
      $spread = $max_frequency - $min_frequency;

      if ($spread == 0) {
        $spread = 1;
      }

      $step = ($max_font_size - $min_font_size) / ($spread);

      $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);

      $this->view->tag_data = $tag_data;
      $this->view->tag_id_array = $tag_id_array;
    }
    $this->view->tag_array = $tag_array;
  }

}

?>