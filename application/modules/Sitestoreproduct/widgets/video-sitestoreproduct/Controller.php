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
class Sitestoreproduct_Widget_VideoSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->type_video = $type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.video');

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = Engine_Api::_()->user()->getViewer()->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    //GET SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //VIDEO IS ENABLED OR NOT
    $allowed_upload_videoEnable = Engine_Api::_()->sitestoreproduct()->enableVideoPlugin();
    if (!$allowed_upload_videoEnable) {
      return $this->setNoRender();
    }

    $this->view->title_truncation = $this->_getParam('truncation', 35);
    $itemCount = $this->_getParam('itemCount', 10);

    //AUTHORIZATION CHECK
    $this->view->allowed_upload_video = Engine_Api::_()->sitestoreproduct()->allowVideo($sitestoreproduct, $viewer);

    //FETCH RESULTS
    $this->view->paginator = Engine_Api::_()->getDbTable('clasfvideos', 'sitestoreproduct')->getProductVideos($sitestoreproduct->product_id, 1, $type_video);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page'));
    $this->view->paginator->setItemCountPerPage($itemCount);

    $counter = $this->view->paginator->getTotalItemCount();

    if (empty($counter) && empty($this->view->allowed_upload_video) && !(Engine_Api::_()->sitestoreproduct()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }

    //ADD VIDEO COUNT
    if ($this->_getParam('titleCount', false)) {
      $this->_childCount = $counter;
    }

    $params = $this->_getAllParams();
    $this->view->params = $params;
    if ($this->_getParam('loaded_by_ajax', false)) {
      $this->view->loaded_by_ajax = true;
      if ($this->_getParam('is_ajax_load', false)) {
        $this->view->is_ajax_load = true;
        $this->view->loaded_by_ajax = false;
        if (!$this->_getParam('onloadAdd', false))
          $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      } else {
        return;
      }
    }
    $this->view->showContent = true;

    $this->view->can_edit = $sitestoreproduct->authorization()->isAllowed($viewer, "edit");

    //IS SITEVIDEOVIEW MODULE ENABLED
    $this->view->sitevideoviewEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideoview');
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}
