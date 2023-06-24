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
class Sitestoreproduct_Widget_EditorProfileReviewsSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //CHECK SUBJECT
    if (!Engine_Api::_()->core()->hasSubject('user')) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->user = $user = Engine_Api::_()->core()->getSubject();
    $type = $this->_getParam('type', 'user');
    if ($type == 'editor' && !Engine_Api::_()->getDbTable('editors', 'sitestoreproduct')->isEditor($user->user_id, 0)) {
      return $this->setNoRender();
    }

    //GET SETTINGS 
    $this->view->isAjax = $this->_getParam('isAjax', 0);
    $this->view->page = $page = $this->_getParam('page', 1);
    $this->view->itemCount = $itemCount = $this->_getParam('itemCount', 10);
    $sitestoreproductEditorProfileReview = Zend_Registry::isRegistered('sitestoreproductEditorProfileReview') ?  Zend_Registry::get('sitestoreproductEditorProfileReview') : null;

    //GET CATEGORY TABLE
    $this->view->tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');

    $params = array();
    $params['owner_id'] = $user->getIdentity();
    $this->view->type = $params['type'] = $type;
    $this->view->truncation = $this->_getParam('truncation', 60);
    $params['limit'] = $itemCount;
    $params['popularity'] = 'review_id';
    $params['pagination'] = 1;
    $params['interval'] = $interval = $this->_getParam('interval', 'overall');
    $params['resource_type'] = 'sitestoreproduct_product';

    $this->view->paginator = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct')->getReviews($params);
    $this->view->paginator->setItemCountPerPage($itemCount);
    $this->view->paginator->setCurrentPageNumber($page);
    $this->view->count = $count = $this->view->paginator->count();
    if (empty($count) && $this->view->type != 'editor') {
      return $this->setNoRender();
    }

    if ($this->view->type == 'editor') {
      $req = Zend_Controller_Front::getInstance()->getRequest();
      $this->view->showEditorLink = ($req->getModuleName() == 'sitestoreproduct' && $req->getControllerName() == 'editor') ? 0 : 1;

      if ((empty($count) && $this->view->showEditorLink) || empty($sitestoreproductEditorProfileReview)) {
        return $this->setNoRender();
      }
    }
  }

}