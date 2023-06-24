<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_VideoContentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //GET VIDEO ID AND OBJECT
    $video_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id', $this->_getParam('video_id', null));
    $sitestoreproduct_video = Engine_Api::_()->getItem('sitestoreproduct_video', $video_id);

    if (empty($sitestoreproduct_video)) {
      return $this->setNoRender();
    }

    //GET TAB ID
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id');

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

    //SET SITESTOREPRODUCT SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $sitestoreproduct_video->product_id);

    $this->view->can_create = Engine_Api::_()->sitestoreproduct()->allowVideo($sitestoreproduct, $viewer);

    $this->view->allowView = $sitestoreproduct_video->authorization()->isAllowed($viewer, "view");

    $can_edit = $this->view->can_edit = $sitestoreproduct->authorization()->isAllowed($viewer, "edit");
    if (empty($can_edit) && $viewer_id == $sitestoreproduct_video->owner_id) {
      $this->view->can_edit = $can_edit = 1;
    }

    if ($viewer_id != $sitestoreproduct_video->owner_id && $can_edit != 1 && ($sitestoreproduct_video->status != 1 || $sitestoreproduct_video->search != 1)) {
      return $this->setNoRender();
    }

    //GET VIDEO TAGS
    $this->view->videoTags = $sitestoreproduct_video->tags()->getTagMaps();

    //CHECK IF EMBEDDING IS ALLOWED
    $can_embed = true;
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.video.embeds', 1)) {
      $can_embed = false;
    } else if (isset($sitestoreproduct_video->allow_embed) && !$sitestoreproduct_video->allow_embed) {
      $can_embed = false;
    }
    $this->view->can_embed = $can_embed;

    $this->view->videoEmbedded = $embedded = "";

    //INCREMENT IN NUMBER OF VIEWS
    $owner = $sitestoreproduct_video->getOwner();
    if (!$owner->isSelf($viewer)) {
      $sitestoreproduct_video->view_count++;
    }
    $sitestoreproduct_video->save();

    if ($sitestoreproduct_video->type != 3) {
      $this->view->videoEmbedded = $embedded = $sitestoreproduct_video->getRichContent(true);
    }

    //SET PRODUCT-VIDEO SUBJECT
    if (Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->clearSubject();
    }
    Engine_Api::_()->core()->setSubject($sitestoreproduct_video);

    //VIDEO FROM MY COMPUTER WORK
    if ($sitestoreproduct_video->type == 3 && $sitestoreproduct_video->status != 0) {
      $sitestoreproduct_video->save();

      if (!empty($sitestoreproduct_video->file_id)) {
        $storage_file = Engine_Api::_()->getItem('storage_file', $sitestoreproduct_video->file_id);
        if ($storage_file) {
          $this->view->video_location = $storage_file->map();
          $this->view->video_extension = $storage_file->extension;
        }
      }
    }

    $this->view->rating_count = Engine_Api::_()->getDbTable('videoratings', 'sitestoreproduct')->ratingCount($sitestoreproduct_video->getIdentity());
    $this->view->video = $sitestoreproduct_video;
    $this->view->rated = Engine_Api::_()->getDbTable('videoratings', 'sitestoreproduct')->checkRated($sitestoreproduct_video->getIdentity(), $viewer->getIdentity());

    //TAG WORK
    $this->view->limit_sitestoreproduct_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproductvideo.tag.limit', 3);

    //VIDEO TABLE
    $videoTable = Engine_Api::_()->getDbtable('videos', 'sitestoreproduct');

    //TOTAL VIDEO COUNT FOR THIS PRODUCT
    $this->view->count_video = $videoTable->getProductVideoCount($sitestoreproduct_video->product_id);
  }

}