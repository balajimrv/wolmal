<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Widget_VideoContentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

     //GET VIDEO ID AND OBJECT
    $video_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id', $this->_getParam('video_id', null));
    $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);

    if (empty($sitestorevideo)) {
      return $this->setNoRender();
    }

    //GET TAB ID
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    $getPackagevideoView = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestorevideo');

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //IF THIS IS SENDING A MESSAGE ID, THE USER IS BEING DIRECTED FROM A CONVERSATION
    //CHECK IF MEMBER IS PART OF THE CONVERSATION
    $message_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer()))
        $message_view = true;
    }
    $this->view->message_view = $message_view;

    //SET SITESTORE SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestorevideo->store_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo")) {
        return $this->setNoRender();
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
      if (empty($isStoreOwnerAllow)) {
        return $this->setNoRender();
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
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

     //MAKE HIGHLIGHTED OR NOT
    $this->view->canMakeHighlighted = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.featured', 1);

    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($sitestore, 'everyone', 'view') === 1 ? true : false ||$auth->isAllowed($sitestore, 'registered', 'view') === 1 ? true : false;
    } 

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'comment');
    if (empty($isManageAdmin)) {
      $this->view->can_comment = 0;
    } else {
      $this->view->can_comment = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = $this->view->can_edit = 0;
    } else {
      $can_edit = $this->view->can_edit = 1;
    }

    if ($viewer_id != $sitestorevideo->owner_id && $can_edit != 1 && ($sitestorevideo->status != 1 || $sitestorevideo->search != 1) || empty($getPackagevideoView)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    //GET VIDEO TAGS
    $this->view->videoTags = $sitestorevideo->tags()->getTagMaps();

    //CHECK IF EMBEDDING IS ALLOWED
    $can_embed = true;
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.embeds', 1)) {
      $can_embed = false;
    } else if (isset($sitestorevideo->allow_embed) && !$sitestorevideo->allow_embed) {
      $can_embed = false;
    }
    $this->view->can_embed = $can_embed;

    $this->view->videoEmbedded = $embedded = "";

      //INCREMENT IN NUMBER OF VIEWS
      $owner = $sitestorevideo->getOwner();
      if (!$owner->isSelf($viewer)) {
        $sitestorevideo->view_count++;
      }

      $sitestorevideo->save();
      if ($sitestorevideo->type != 3) {
				$this->view->videoEmbedded = $embedded = $sitestorevideo->getRichContent(true);
      }

    //SET STORE-VIDEO SUBJECT
    if (Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->clearSubject();
    }
    Engine_Api::_()->core()->setSubject($sitestorevideo);

    //VIDEO FROM MY COMPUTER WORK
    if ($sitestorevideo->type == 3 && $sitestorevideo->status != 0) {
      $sitestorevideo->save();

      if (!empty($sitestorevideo->file_id)) {
        $storage_file = Engine_Api::_()->getItem('storage_file', $sitestorevideo->file_id);
        if ($storage_file) {
          $this->view->video_location = $storage_file->map();
          $this->view->video_extension = $storage_file->extension;
        }
      }
    }

    $this->view->rating_count = Engine_Api::_()->getDbTable('ratings', 'sitestorevideo')->ratingCount($sitestorevideo->getIdentity());
    $this->view->video = empty($getPackagevideoView) ? null : $sitestorevideo;
    $this->view->rated = Engine_Api::_()->getDbTable('ratings', 'sitestorevideo')->checkRated($sitestorevideo->getIdentity(), $viewer->getIdentity());

    //TAG WORK
    $this->view->limit_sitestorevideo = $total_sitestorevideos = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.tag.limit', 3);
    
    //VIDEO TABLE
    $videoTable = Engine_Api::_()->getDbtable('videos', 'sitestorevideo');

    //TOTAL VIDEO COUNT FOR THIS STORE
    $this->view->count_video = $videoTable->getStoreVideoCount($sitestorevideo->store_id);

    // Start: "Suggest to Friends" link work.
    $store_flag = 0;
    $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
    $isSupport = Engine_Api::_()->getApi('suggestion', 'sitestore')->isSupport();
    if (!empty($is_suggestion_enabled)) {
      // Here we are delete this video suggestion if viewer have.
      if (!empty($is_moduleEnabled)) {
        Engine_Api::_()->getApi('suggestion', 'sitestore')->deleteSuggestion($viewer_id, 'store_video', Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id'), 'store_video_suggestion');
      }

      $SuggVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion')->version;
      $versionStatus = strcasecmp($SuggVersion, '4.1.7p1');
      if ($versionStatus >= 0) {
        $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitestorevideo', Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id'), 1, null);
        if (!empty($modContentObj)) {
          $contentCreatePopup = @COUNT($modContentObj);
        }
      }

      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1)) {
        if ($sitestore->expiration_date <= date("Y-m-d H:i:s")) {
          $store_flag = 1;
        }
      }
      if (!empty($contentCreatePopup) && !empty($isSupport) && empty($sitestore->closed) && !empty($sitestore->approved) && empty($sitestore->declined) && !empty($sitestore->draft) && empty($store_flag) && !empty($viewer_id) && !empty($is_suggestion_enabled)) {
        $this->view->videoSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitestore', 'video_sugg_link');
      }
      // End: "Suggest to Friends" link work.
    }
  }

}
?>