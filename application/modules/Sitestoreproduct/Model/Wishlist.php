<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Wishlist.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class sitestoreproduct_Model_Wishlist extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;
  protected $_parent_type = 'user';
  protected $_parent_is_owner = true;

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {

    $params = array_merge(array(
        'route' => "sitestoreproduct_wishlist_view",
        'reset' => true,
        //'owner_id' => $this->owner_id,
        'wishlist_id' => $this->wishlist_id,
        'slug' => $this->getSlug(),
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  public function getDescription() {
    return $this->body;
  }

  /**
   * Return slug
   * */
  public function getSlug($str = null, $maxstrlen = 255) {
    
    if( null === $str ) {
      $str = $this->title;
    }
    
    return Engine_Api::_()->seaocore()->getSlug($str, 63);
  }

  public function getWishlistMap($params=array()) {
    $paginator = Engine_Api::_()->getDbTable('wishlistmaps', 'sitestoreproduct')->wishlistProducts($this->wishlist_id, $params);
    if (isset($params['limit']) && $params['limit'] > 0)
      $paginator->setItemCountPerPage($params['limit']);
    return $paginator;
  }

  public function getCoverItem() {
    if(!empty($this->product_id)) {
      return Engine_Api::_()->getItem('sitestoreproduct_product', $this->product_id);
    }
    else {
      return $this;
    }
  }

	public function getPhotoUrl($type = null) {
    
    if(!empty($this->product_id)) {
      
      if($type == null) $type = 'thumb.main';
      
      return Engine_Api::_()->getItem('sitestoreproduct_product', $this->product_id)->getPhotoUrl($type);
    }
  }
  
  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }
  
   /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }
  
  /**
   * Gets a proxy object for the follow handler
   *
   * @return Engine_ProxyObject
   * */
  public function follows() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('follows', 'seaocore'));
  }  
  
  /**
   * Delete the wishlist and belongings
   * 
   */
  public function _delete() {

    //DELETE ALL MAPPING VALUES FROM WISHLISTMAPS TABLES
    Engine_Api::_()->getDbtable('wishlistmaps', 'sitestoreproduct')->delete(array('wishlist_id = ?' => $this->wishlist_id));

    //DELETE WISHLIST
    parent::_delete();
  }

}
