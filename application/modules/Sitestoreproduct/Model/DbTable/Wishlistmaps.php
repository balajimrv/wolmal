<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Wishlistmaps.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Wishlistmaps extends Engine_Db_Table {

  protected $_rowClass = 'Sitestoreproduct_Model_Wishlistmap';

  public function wishlistProducts($wishlist_id, $params = null) {
    //RETURN IF WISHLIST ID IS EMPTY
    if (empty($wishlist_id)) {
      return;
    }

    //GET WISHLIST PAGE TABLE NAME
    $wishlistProductTableName = $this->info('name');

    //GET PRODUCT TABLE
    $sitestoreproductTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
    $sitestoreproductTableName = $sitestoreproductTable->info('name');
    $otherInfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');

    //MAKE QUERY
    $select = $sitestoreproductTable->select()
            ->setIntegrityCheck(false)
            ->from($sitestoreproductTableName, array("store_id", "owner_id", "product_id", "title", "photo_id", "price", "in_stock", "user_tax", "weight", "product_type", "min_order_quantity", "max_order_quantity", "stock_unlimited", "closed", "draft", "end_date", "end_date_enable", "approved", "rating_avg", "rating_users", "rating_editor", "body", "category_id", "featured", "newlabel", "sponsored", "like_count", "view_count", "comment_count", "review_count", "creation_date", "start_date", "search", "allow_purchase"))
            ->join($otherInfoTableName, "($sitestoreproductTableName.product_id = $otherInfoTableName.product_id)", array("discount", "discount_start_date", "discount_end_date", "handling_type", "discount_value", "discount_amount"));
            
            
            
            if (isset($params['wishlists_array']) && !empty($params['wishlists_array'])) {       
                $select->join($wishlistProductTableName, "$wishlistProductTableName.product_id = $sitestoreproductTableName.product_id", array('date', 'product_id', 'wishlist_id'))
                ->where($wishlistProductTableName . '.wishlist_id IN (' . implode(",", $params['wishlists_array']) . ')');
            }else {
                $select->join($wishlistProductTableName, "$wishlistProductTableName.product_id = $sitestoreproductTableName.product_id", array('date', 'product_id'))
                ->where($wishlistProductTableName . '.wishlist_id = ?', $wishlist_id);
            }
            
            
            $select->where("$sitestoreproductTableName .start_date <= NOW()")
            ->where("$sitestoreproductTableName.end_date_enable = 0 OR $sitestoreproductTableName.end_date > NOW()")
            ->where("$sitestoreproductTableName.stock_unlimited = 1 OR $otherInfoTableName.out_of_stock = 1 OR $sitestoreproductTableName.min_order_quantity <= $sitestoreproductTableName.in_stock");


    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select->where($sitestoreproductTableName . '.category_id = ?', $params['category_id']);
    }

    if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
      $select->where($sitestoreproductTableName . '.subcategory_id = ?', $params['subcategory_id']);
    }

    if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
      $select->where($sitestoreproductTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
    }

    if (isset($params['category']) && !empty($params['category'])) {
      $select->where($sitestoreproductTableName . '.category_id = ?', $params['category']);
    }

    if (isset($params['subcategory']) && !empty($params['subcategory'])) {
      $select->where($sitestoreproductTableName . '.subcategory_id = ?', $params['subcategory']);
    }

    if (isset($params['subsubcategory']) && !empty($params['subsubcategory'])) {
      $select->where($sitestoreproductTableName . '.subsubcategory_id = ?', $params['subsubcategory']);
    }

    if (isset($params['search']) && !empty($params['search'])) {
      $select->where($sitestoreproductTableName . ".title LIKE ? OR " . $sitestoreproductTableName . ".body LIKE ? ", '%' . $params['search'] . '%');
    }
    if (isset($params['orderby']) && $params['orderby'] == 'random') {
      $select->order('RAND()');
    } else if (isset($params['orderby']) && !empty($params['orderby'])) {
      if($params['orderby'] == 'date') {
        $select->order("$wishlistProductTableName." . $params['orderby'] . " DESC");
      }
      else {
        $select->order("$sitestoreproductTableName." . $params['orderby'] . " DESC");
      }
    } else {
      $select->order($sitestoreproductTableName . '.product_id' . " DESC");
    }

    //RETURN RESULTS
    return Zend_Paginator::factory($select);
  }

  public function pageWishlists($product_id, $owner_id = 0) {

    //RETURN IF PAGE ID IS EMPTY
    if (empty($product_id)) {
      return;
    }

    //GET WISHLIST PAGE TABLE NAME
    $wishlistTable = Engine_Api::_()->getDbTable('wishlists', 'sitestoreproduct');
    $wishlistTableName = $wishlistTable->info('name');

    //GET WISHLIST PAGE TABLE NAME
    $wishlistProductTableName = $this->info('name');

    //MAKE QUERY
    $select = $wishlistTable->select()
            ->setIntegrityCheck(false)
            ->from($wishlistTableName)
            ->join($wishlistProductTableName, "$wishlistProductTableName.wishlist_id = $wishlistTableName.wishlist_id")
            ->where($wishlistTableName . '.owner_id = ?', $owner_id)
            ->where($wishlistProductTableName.'.product_id = ?', $product_id);
    
    //RETURN RESULTS
    return $wishlistTable->fetchAll($select);
  }

  /**
   * Return wishlists count
   *
   * @param int $wishlist_id 
   * @return wishlists count
   * */
  public function itemCount($wishlist_id) {

    $wishlistCount = $this->select()
            ->from($this->info('name'), array('COUNT(*) AS count'))
            ->where('wishlist_id = ?', $wishlist_id)
            ->query()
            ->fetchColumn();

    //RETURN WISHLIST COUNT
    return $wishlistCount;
  }

  /**
   * Return wishlists count
   *
   * @param int $wishlist_id 
   * @return wishlists count
   * */
  public function getWishlistsProductCount($product_id) {

    $wishlistCount = $this->select()
            ->from($this->info('name'), array('COUNT(*) AS count'))
            ->where('product_id = ?', $product_id)
            ->query()
            ->fetchColumn();

    //RETURN WISHLIST COUNT
    return $wishlistCount;
  }

}
