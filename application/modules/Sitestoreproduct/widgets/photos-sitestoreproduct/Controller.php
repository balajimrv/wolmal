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
class Sitestoreproduct_Widget_PhotosSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET PRODUCT SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //GET LEVEL SETTING
    $this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($sitestoreproduct, $viewer, "photo");

    //GET PAGINATOR
    $this->view->album = $album = $sitestoreproduct->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $this->view->total_images = $total_images = $paginator->getTotalItemCount();
    if (empty($total_images) && !$this->view->allowed_upload_photo && !(Engine_Api::_()->sitestoreproduct()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }

    //ADD COUNT TO TITLE
    if ($this->_getParam('titleCount', false) && $total_images > 0) {
      $this->_childCount = $total_images;
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

    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage($this->_getParam('itemCount', 24));
    $this->view->can_edit = $canEdit = $sitestoreproduct->authorization()->isAllowed($viewer, "edit");
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}