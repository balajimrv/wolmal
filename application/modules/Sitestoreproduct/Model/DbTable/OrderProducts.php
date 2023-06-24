<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: OrderProducts.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_OrderProducts extends Engine_Db_Table
{
  protected $_name = 'sitestoreproduct_order_products';

  /**
   * Return products of an order
   *
   * @param $order_id
   * @param $stock_unlimited
   * @return object
   */
  public function getOrderProducts($order_id, $stock_unlimited = false) {

    $orderProductName = $this->info('name');
    $productName = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->info('name');

    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($orderProductName)
            ->joinLeft($productName, "$productName.product_id = $orderProductName.product_id", array("$productName.title", "$productName.product_code", "$productName.in_stock", "$productName.stock_unlimited", "$productName.product_type"))
            ->where($orderProductName . '.order_id =?', $order_id);
    
    if( !empty($stock_unlimited) )
      $select->where($productName . '.stock_unlimited =?', 0);
   
    return $this->fetchAll($select);
  }
  
  /**
   * Return product details purchased in an order
   *
   * @param $order_id
   * @return object
   */
  public function getOrderProductsDetail($order_id) {

    $select = $this->select()
                   ->where('order_id =?', $order_id);
   
    return $this->fetchAll($select);
  }
  
  /**
   * Return products of an order, which viewer can reorder
   *
   * @param $order_id
   * @return object
   */
  public function getReorderProducts($order_id, $params = array())
  {
    $orderProductName = $this->info('name');
    $productsTableName = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->info('name');
    $otherinfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');
    
    $select = $this->select()
                   ->setIntegrityCheck(false)
                   ->from($orderProductName, array("product_id", "quantity"))
                   ->join($productsTableName, "$productsTableName.product_id = $orderProductName.product_id", array("product_type"));
    if( isset($params['fetchDownpaymentValue']) && !empty($params['fetchDownpaymentValue']) ) {
      $select->join($otherinfoTableName, "$productsTableName.product_id = $otherinfoTableName.product_id", array("downpayment_value"));
    }
                   $select->where($productsTableName . '.approved = ?', '1')
                   ->where($productsTableName . '.draft = ?', '0')
                   ->where($productsTableName . ".search = ?", 1)
                   ->where("$productsTableName .start_date <= NOW()")
                   ->where("$productsTableName.end_date_enable = 0 OR $productsTableName.end_date > NOW()")
                   ->where("$productsTableName.stock_unlimited = 1 OR $productsTableName.min_order_quantity <= $productsTableName.in_stock")
                   ->where("$orderProductName.order_id =?",$order_id)
                   ->where("$orderProductName.configuration IS NULL");
    
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
      $select->where($productsTableName . '.closed = ?', '0');
    } 

    return $select->query()->fetchAll();
  }
  
  /**
   * Return product ids of an order
   *
   * @param $order_id
   * @return object
   */
  public function getOrderProductIds($order_id)
  {
    $select = $this->select()->from($this->info('name'), array('product_id'))->where('order_id =?', $order_id);
    return $select->query()->fetchAll();
  }
  
  /**
   * Return products name of an order
   *
   * @param $order_id
   * @return object
   */
  public function getOrderProductsName($order_id){
    $orderProductName = $this->info('name');
    $productName = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->info('name');
    $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($orderProductName,array("$orderProductName.quantity"))
            ->joinInner($productName, "$productName.product_id = $orderProductName.product_id", array("$productName.$title_column"))
            ->where($orderProductName . '.order_id =?', $order_id)
            ->query()
            ->fetchAll();
    return $select;
  }
  
  /**
   * Return product id of an order depends on product types 
   *
   * @param array $params
   * @return int
   */
  public function checkProductType($params = array())
  {
    $orderProductName = $this->info('name');
    $productName = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->info('name');
    
    $select = $this->select()
                   ->setIntegrityCheck(false)
                   ->from($orderProductName, array("$orderProductName.product_id"))
                   ->joinInner($productName, "$productName.product_id = $orderProductName.product_id", array(""))
                   ->where("$orderProductName.order_id =?", $params['order_id']);
    
    if( !empty($params['virtual']) )
      $select->where("product_type LIKE 'simple' OR product_type LIKE 'configurable' OR product_type LIKE 'grouped' OR product_type LIKE 'bundled' ");
    else
      empty($params['all_downloadable_products']) ? $select->where("product_type LIKE 'downloadable'") : $select->where("product_type LIKE 'simple' OR product_type LIKE 'configurable' OR product_type LIKE 'virtual' OR product_type LIKE 'grouped' OR product_type LIKE 'bundled' ");
    
    return $select->limit(1)->query()->fetchColumn();
  }
  
  /**
   * Check bundle product shipping is enable or not
   *
   * @param array $params
   * @return bool
   */
  public function checkBundleProductShipping($params = array())
  {
    $orderProductName = $this->info('name');
    $otherinfoTableName = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->info('name');
    
    $select = $this->select()
                   ->setIntegrityCheck(false)
                   ->from($orderProductName, array(""))
                   ->joinInner($otherinfoTableName, "$otherinfoTableName.product_id = $orderProductName.product_id", array("product_info"))
                   ->where("$orderProductName.order_id =?", $params['order_id'])
                   ->query()->fetchAll();
    
    if( !empty($select) )
    {
      $enableShipping = 0;
      foreach( $select as $optn )
      {
        $bundleProductInfo = @unserialize($optn['product_info']);
        if( !empty($bundleProductInfo) )
        {
          if( !empty($bundleProductInfo['enable_shipping']) )
            return false;
          else
            $enableShipping = 1;
        }
        else
          return false;
      }
      
      if( !empty($enableShipping) )
        return true;
    }
    else
      return false;
  }
  
  /**
   * Return all order products for which buyer has to pay
   *
   * @param $params array
   * @return array
   */
  public function getRemainingAmountOrderProducts($params = array()) {
    $order_product_table_name = $this->info('name');
    $order_table_name = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->info('name');
    
    $select = $this->select()
                    ->from($this->info('name'), array("product_title", "(price - downpayment ) as product_price", "quantity"));
                    $select->where("downpayment != 0");
                    
    if( isset($params['getStoreId']) && !empty($params['getStoreId']) ) {
      $select->setIntegrityCheck(false)
             ->join($order_table_name, "($order_product_table_name.order_id = $order_table_name.order_id)", array("store_id"));
    }
    
    if( isset($params['order_id']) && !empty($params['order_id']) )
      $select->where($order_product_table_name.'.order_id =?', $params['order_id']);
    
    return $select->query()->fetchAll();
  }
  
  public function getBillingOrderProducts($product_id) {
    $select = $this->select()
                ->from($this->info('name'))
                ->where("product_id =?", $product_id)
                ->where("order_product_info IS NOT NULL");

    return $this->fetchAll($select);
  }
}