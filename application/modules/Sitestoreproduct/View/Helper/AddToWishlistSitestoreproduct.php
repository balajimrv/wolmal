<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddToWishlistSitestoreproduct.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_View_Helper_AddToWishlistSitestoreproduct extends Zend_View_Helper_Abstract {

  /**
   * Assembles action string
   * 
   * @return string
   */
  public function addToWishlistSitestoreproduct($item, $params = null) {
   
    //GET VIEWER ID
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();    
    
    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    //GET LEVEL SETTING
    $can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_wishlist', "create");
    
    if(empty($can_create)) {
      return;
    }    
    
    //GET VIEWER ID
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $data['item'] = $item;
    $data['classIcon'] = $params['classIcon'];
    $data['classLink'] = $params['classLink'];
    $data['text'] = isset($params['text']) ? $params['text'] : "Add to Wishlist";
    
    return $this->view->partial('_addToWishlist.tpl', 'sitestoreproduct', $data);
  }

}