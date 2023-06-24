<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: VideoeditController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_VideoeditController extends Core_Controller_Action_Standard {

  //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {

    //LOGGED IN USER CAN EDIT OR DELETE VIDEO
    if (!$this->_helper->requireUser()->isValid())
      return;

    //AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
      return;
  
    if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
      return;

    //SET SUBJECT
    $product_id = $this->_getParam('product_id', $this->_getParam('product_id', null));
    if ($product_id) {
      $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
      if ($sitestoreproduct) {
        Engine_Api::_()->core()->setSubject($sitestoreproduct);
      }
    }

    //SITESTOREPRODUCT SUBJECT SHOULD BE SET
    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }
  }

  //ACTION FOR EDIT THE VIDEO
  public function editAction() {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $this->view->sitestores_view_menu = 8;
    
    //GET SITESTOREPRODUCT SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject();
     $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

    $this->view->slideShowEnanle = $this->slideShowEnable();

    //AUTHORIZATION CHECK
    if (!$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, $viewer, "edit")->isValid()) {
      return;
    }

    //AUTHORIZATION CHECK
    $allowed_upload_video = Engine_Api::_()->sitestoreproduct()->allowVideo($sitestoreproduct, $viewer);
    if (empty($allowed_upload_video)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $this->view->content_id = Engine_Api::_()->sitestoreproduct()->getTabId('sitestoreproduct.video-sitestoreproduct');

    //SELECTED TAB
    $this->view->TabActive = "Videos";

    //GET VIDEOS
    $this->view->type_video = $type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.video');

    if ($type_video) {
      $this->view->main_video_id = $sitestoreproduct->main_video['corevideo_id'];
    } else {
      if(isset($sitestoreproduct->main_video['reviewvideo_id']))
      $this->view->main_video_id = $sitestoreproduct->main_video['reviewvideo_id'];
    }

    $this->view->videos = $videos = array();
    if (Engine_Api::_()->sitestoreproduct()->enableVideoPlugin() && !empty($type_video)) {
      $this->view->videos = $videos = Engine_Api::_()->getItemTable('sitestoreproduct_clasfvideo', 'sitestoreproduct')->getProductVideos($sitestoreproduct->product_id, 0, 1);
    } elseif (empty($type_video)) {
      $this->view->videos = $videos = Engine_Api::_()->getItemTable('sitestoreproduct_clasfvideo', 'sitestoreproduct')->getProductVideos($sitestoreproduct->product_id, 0, 0);
    }

    $this->view->count = count($videos);

    //MAKE FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Video_Editvideo();

    foreach ($videos as $video) {

      $subform = new Sitestoreproduct_Form_Video_Edit(array('elementsBelongTo' => $video->getGuid()));

      if ($video->status != 1) {
        if ($video->status == 0 || $video->status == 2):
          $msg = $this->view->translate("Your video is currently being processed - you will be notified when it is ready to be viewed.");
        elseif ($video->status == 3):
          $msg = $this->view->translate("Video conversion failed. Please try again.");
        elseif ($video->status == 4):
          $msg = $this->view->translate("Video conversion failed. Video format is not supported by FFMPEG. Please try again.");
        elseif ($video->status == 5):
          $msg = $this->view->translate("Video conversion failed. Audio files are not supported. Please try again.");
        elseif ($video->status == 7):
          $msg = $this->view->translate("Video conversion failed. You may be over the site upload limit.  Try  a smaller file, or delete some files to free up space.");
        endif;

        $subform->addElement('dummy', 'mssg' . $video->video_id, array(
            'description' => $msg,
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div', 'class' => 'tip')),
                array('Description', array('tag' => 'span', 'placement' => 'APPEND')),
                array('Description', array('placement' => 'APPEND')),
            ),
        ));
        $t = 'mssg' . $video->video_id;
        $subform->$t->getDecorator("Description")->setOption("placement", "append");
      }
      $subform->populate($video->toArray());
      $form->addSubForm($subform, $video->getGuid());
    }

    //CHECK METHOD
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET FORM VALUES
    $values = $form->getValues();

    if (isset($_POST['corevideo_cover']) && !empty($_POST['corevideo_cover'])) {
      if (isset($sitestoreproduct->main_video)  && !empty($sitestoreproduct->main_video)) {
        $sitestoreproduct->main_video = array_merge((array) $sitestoreproduct->main_video, array('corevideo_id' => $_POST['corevideo_cover']));
      } else {
        $sitestoreproduct->main_video = array('corevideo_id' => $_POST['corevideo_cover']);
      }
    } elseif (isset($_POST['reviewvideo_cover']) && $_POST['reviewvideo_cover']) {
      if (isset($sitestoreproduct->main_video)  && !empty($sitestoreproduct->main_video)) {
        $sitestoreproduct->main_video = array_merge((array) $sitestoreproduct->main_video, array('reviewvideo_id' => $_POST['reviewvideo_cover']));
      } else {
        $sitestoreproduct->main_video = array('reviewvideo_id' => $_POST['reviewvideo_cover']);
      }
    }

    $sitestoreproduct->save();

    //VIDEO SUBFORM PROCESS IN EDITING
    foreach ($videos as $video) {
      $subform = $form->getSubForm($video->getGuid());

      $values = $subform->getValues();
      $values = $values[$video->getGuid()];
      if (isset($values['delete']) && $values['delete'] == '1') {
        Engine_Api::_()->getDbtable('videos', 'sitestoreproduct')->delete(array('video_id = ?' => $video->video_id, 'product_id = ?' => $sitestoreproduct->product_id));
        Engine_Api::_()->getDbtable('actions', 'activity')->delete(array('type = ?' => 'video_sitestoreproduct', 'object_id = ?' => $sitestoreproduct->product_id));
      } else {
        $video->setFromArray($values);
        $video->save();
      }
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'edit', 'product_id' => $sitestoreproduct->product_id), "sitestoreproduct_videospecific", true);
  }

  public function deleteAction() {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET VIDEO ID
    $video_id = $this->_getParam('video_id');
    $viewer_id = $viewer->getIdentity();

    //GET SITESTOREPRODUCT SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject();

    $can_edit = $sitestoreproduct->authorization()->isAllowed($viewer, 'edit');

    $sitestoreproduct_video = $video = Engine_Api::_()->getItem('video', $this->_getParam('video_id'));

    //VIDEO OWNER AND PRODUCT OWNER CAN DELETE VIDEO
    if ($viewer_id != $sitestoreproduct_video->owner_id && $can_edit != 1) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        Engine_Api::_()->getDbtable('clasfvideos', 'sitestoreproduct')->delete(array('product_id = ?' => $sitestoreproduct->product_id, 'video_id = ?' => $video_id));
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => '500',
          'parentRefreshTime' => '500',
          'format' => 'smoothbox',
          'messages' => Zend_Registry::get('Zend_Translate')->_('You have successfully deleted this video.')
      ));
    }
  }

  public function slideShowEnable() {
    //GET CONTENT TABLE
    $tableContent = Engine_Api::_()->getDbtable('content', 'core');
    $tableContentName = $tableContent->info('name');

    //GET PAGE TABLE
    $tablePage = Engine_Api::_()->getDbtable('pages', 'core');
    $tablePageName = $tablePage->info('name');
    //GET PAGE ID
    $page_id = $tablePage->select()
            ->from($tablePageName, array('page_id'))
            ->where('name = ?', "sitestoreproduct_index_view")
            ->query()
            ->fetchColumn();

    if (empty($page_id)) {
      return false;
    }

    $content_id = $tableContent->select()
            ->from($tableContent->info('name'), array('content_id'))
            ->where('page_id = ?', $page_id)
            ->where('name = ?', 'sitestoreproduct.slideshow-list-photo')
            ->query()
            ->fetchColumn();

    if ($content_id)
      return true;

    $params = $tableContent->select()
            ->from($tableContent->info('name'), array('params'))
            ->where('page_id = ?', $page_id)
            ->where('name = ?', 'sitestoreproduct.editor-reviews-sitestoreproduct')
            ->query()
            ->fetchColumn();
    if ($params) {
      $params = Zend_Json::decode($params);
      if (!isset($params['show_slideshow']) || $params['show_slideshow']) {
        return true;
      }
      return false;
    }
  }

}