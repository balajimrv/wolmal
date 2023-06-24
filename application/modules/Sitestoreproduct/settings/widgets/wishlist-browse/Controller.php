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
class Sitestoreproduct_Widget_WishlistBrowseController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //GET ZEND REQUEST OBJECT
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $requestParams = $request->getParams();
    $this->view->titleLink = $this->_getParam('titleLink', '');
    $this->view->wishlistBlockWidth = $this->_getParam('wishlistBlockWidth', 198);
    $this->view->isBottomTitle = $this->_getParam('is_bottom_title', 0);
    $this->view->hideFollow = empty($requestParams['hide_follow'])? true: false;
    $this->view->viewTypes = $viewTypes = $this->_getParam('viewTypes', array("list", "grid"));
    $this->view->viewTypeDefault = $this->_getParam('viewTypeDefault', 'grid');
    $this->view->wishlistCount = $viewTypes = $this->_getParam('wishlistCount', 1);
    $this->view->showPagination = $viewTypes = $this->_getParam('showPagination', 1);
    $this->view->followLike = $this->_getParam('followLike', array("follow", "like"));
    $this->view->statisticsWishlist = $this->_getParam('statisticsWishlist', array("productCount", "likeCount", "viewCount", "followCount"));
    $viewTypeDefault = $this->_getParam('viewTypeDefault', 'grid');
    if (is_array($viewTypes) && !in_array($viewTypeDefault, $viewTypes)) {
      $viewTypeDefault = $viewTypes[0];
    }
    if (!isset($requestParams['viewType'])) {
      $this->view->setAlsoInForm = true;
      $requestParams['viewType'] = $viewTypeDefault;
    }
    //GENERATE SEARCH FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Wishlist_Search();
    $form->populate($requestParams);
    $this->view->formValues = $form->getValues();
    $page = $request->getParam('page', 1);

    //GET PAGINATOR
    $params = array();
    $params['pagination'] = 1;
    $params['displayBy'] = $this->_getParam('displayBy', 'all');
    $params['is_only_featured'] = $this->_getParam('is_only_featured', 0);
    
    $from_my_store_account = $this->_getParam('from_my_store_account', 0);
    if(!empty($from_my_store_account)) {
      $this->view->from_my_store_account = $params['search_wishlist'] = 'my_wishlists';
    }
    
    $params = array_merge($requestParams, $params);
    $itemCount = $this->_getParam('itemCount', 20);
    $this->view->isSearched = Count($params);

    $this->view->paginator = Engine_Api::_()->getDbtable('wishlists', 'sitestoreproduct')->getBrowseWishlists($params);
    $sitestoreproductBrowseWishlist = Zend_Registry::isRegistered('sitestoreproductBrowseWishlist') ?  Zend_Registry::get('sitestoreproductBrowseWishlist') : null;
    $this->view->paginator->setItemCountPerPage($itemCount);
    $this->view->paginator->setCurrentPageNumber($page);
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->listThumbsCount = $this->_getParam('listThumbsCount', 4);
    $this->view->isAjax = $this->_getParam('isAjax', false);
    if ($this->view->isAjax) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    $this->view->params = $params = $this->_getAllParams();

    if ($viewer_id)
      $this->view->allowFollow = 1;
    
    if(empty($sitestoreproductBrowseWishlist) ) {
      return $this->setNoRender();
    }
  }

}
