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
class Sitestoreproduct_Widget_WishlistProductsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params = array();
    $params['orderby'] = $this->_getParam('orderby', 'RAND()');
    $params['limit'] = $this->_getParam('limit', 3);
    $type = $this->_getParam('type', 'none');
    $this->view->title_truncation = $this->_getParam('truncation', 16);
    $this->view->statisticsWishlist = $this->_getParam('statisticsWishlist', array("productCount", "followCount"));
    $sitestoreproductWishlistProduct = Zend_Registry::isRegistered('sitestoreproductWishlistProduct') ?  Zend_Registry::get('sitestoreproductWishlistProduct') : null;
    
    //GET RECENT WISHLIST ID OF THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    if ($type != 'none' && empty($viewer_id)) {
      return $this->setNoRender();
    }

    //FETCH FRIENDS WISHLIST
    $this->view->friendsWishlists = array();
    if ($type == 'friends') {
      $params['owner_ids'] = $viewer->membership()->getMembershipsOfIds();
      if (empty($params['owner_ids'])) {
        return $this->setNoRender();
      }
    } elseif ($type == 'viewer') {
      $params['owner_ids'] = array("$viewer_id");
    }

    //FETCH WISHLISTS
    $this->view->wishlists = Engine_Api::_()->getDbtable('wishlists', 'sitestoreproduct')->getBrowseWishlists($params);

    if ((Count($this->view->wishlists) <= 0) || empty($sitestoreproductWishlistProduct)) {
      return $this->setNoRender();
    }
  }

}
