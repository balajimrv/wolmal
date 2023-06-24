<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Cartproducts.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Cartproducts extends Engine_Db_Table {

  protected $_rowClass = 'Sitestoreproduct_Model_Cartproduct';
  protected $_name = 'sitestoreproduct_cartproducts';

  /**
   * Get viewer carts
   *
   * @param $cart_id = cart id of the viewer
   * @param $flag = flag variable
   * @return array
   */
  public function getCart($cart_id, $flag = false) {
    $cartProductTableName = $this->info('name');

    $select = $this->select();

    if (empty($flag)) {
      $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
      $productTableName = $productTable->info('name');

      $select = $select->from($cartProductTableName)
              ->setIntegrityCheck(false)
              ->join($productTableName, "($cartProductTableName.product_id = $productTableName.product_id)", array("$productTableName.store_id", "$productTableName.title", "$productTableName.price", "$productTableName.photo_id", "$productTableName.product_type"));
    }

    $select = $select->where("cart_id = ?", $cart_id);

    if (empty($flag)) {
      $select = $select->order("$productTableName.store_id DESC")->order("$cartProductTableName.product_id DESC");
    }

    $viewerCart = $select->query()->fetchAll();

    return $viewerCart;
  }

  /**
   * Return the viewer cart products
   *
   * @param $cart_id
   * @return object
   */
  public function getCheckoutViewerCart($cart_id, $params = array()) {
    $cartProductTableName = $this->info('name');
    $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    $productTableName = $productTable->info('name');
    $otherinfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');

    $select = $this->select()
            ->from($cartProductTableName, array('quantity', 'cart_id'))
            ->setIntegrityCheck(false)
            ->join($productTableName, "($cartProductTableName.product_id = $productTableName.product_id)", array("$productTableName.store_id", "$productTableName.product_id", "$productTableName.title", "$productTableName.product_type", "$productTableName.price", "$productTableName.in_stock", "$productTableName.user_tax", "$productTableName.weight", "$productTableName.stock_unlimited", "$productTableName.min_order_quantity", "$productTableName.max_order_quantity", "$productTableName.closed", "$productTableName.draft", "$productTableName.end_date", "$productTableName.end_date_enable", "$productTableName.approved", "$productTableName.start_date", "$productTableName.search", "allow_purchase"))
            ->join($otherinfoTableName, "($productTableName.product_id = $otherinfoTableName.product_id)", array("$otherinfoTableName.discount", "$otherinfoTableName.discount_start_date", "$otherinfoTableName.discount_end_date", "$otherinfoTableName.handling_type", "$otherinfoTableName.discount_value", "$otherinfoTableName.discount_amount","$otherinfoTableName.discount_permanant", "$otherinfoTableName.user_type", "$otherinfoTableName.product_info"))
            ->where("$cartProductTableName.cart_id = ?", $cart_id)
            ->order("$productTableName.store_id DESC")
            ->order("$cartProductTableName.cartproduct_id DESC")
            ->group("$cartProductTableName.product_id");
    
    if( isset($params['store_id']) && !empty($params['store_id']) )
      $select->where("$productTableName.store_id =?", $params['store_id']);

    return $select->query()->fetchAll();
  }

  /**
   * Return configuration of the product
   *
   * @param array $fetch_column
   * @param $product_id
   * @param $cart_id
   * @return object
   */
  public function getConfiguration($fetch_column, $product_id, $cart_id)
  {
    return $this->select()->from($this->info('name'), $fetch_column)
                    ->where('cart_id = ?', $cart_id)
                    ->where('product_id = ?', $product_id)
                    ->query()
                    ->fetchAll();
  }
  
  public function getConfigurationId($product_id, $cart_id)
  {
    return $this->select()->from($this->info('name'), 'cartproduct_id')
                    ->where('cart_id = ?', $cart_id)
                    ->where('product_id = ?', $product_id)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
  }
  
  /**
   * delete the cart, if last product of the cart is going to delete
   *
   * @param $cart_id
   * 
   */
  public function deleteCart($cartId)
  {
    $isCartProductExist = $this->select()
                               ->from($this->info('name'), array("cartproduct_id"))
                               ->where("cart_id =?", $cartId)
                               ->limit(1)->query()->fetchColumn();

    if( empty($isCartProductExist) )
      Engine_Api::_()->getItem('sitestoreproduct_cart', $cartId)->delete();
  }
  
  /**
   * delete the cart, if last product of the cart is going to delete
   *
   * @param $cart_id
   * 
   */
  public function getStoreCartProducts($cartId, $storeId)
  {
    $cartProductTableName = $this->info('name');
    $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    $productTableName = $productTable->info('name');
    
    $select = $this->select()
                   ->from($cartProductTableName, array("cartproduct_id"))
                   ->setIntegrityCheck(false)
                   ->join($productTableName, "($cartProductTableName.product_id = $productTableName.product_id)", '')
                   ->where("$cartProductTableName.cart_id =?", $cartId)
                   ->where("$productTableName.store_id = ?", $storeId);

    return $this->fetchAll($select);
  }
  
  public function getCartProductIds($cartId)
  {
    $select = $this->select()
                   ->from($this->info('name'), "product_id")
                   ->where("cart_id =?", $cartId);

    return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
  }
}