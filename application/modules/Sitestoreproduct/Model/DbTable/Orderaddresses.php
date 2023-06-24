<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Orderaddresses.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Orderaddresses extends Engine_Db_Table {

  protected $_name = 'sitestoreproduct_order_addresses';

  /**
   * Return billing or shipping address for an order
   *
   * @param $order_id
   * @param $flag
   * @param $params
   * @return object
   */
  public function getAddress($order_id, $flag = false, $params = array())
  {
    $select = $this->select()->where('order_id = ?', $order_id)->limit(1);
    if( !empty($flag) )
    {
      $select = $select->order('orderaddress_id DESC');
    }
    
    if( isset($params['address_type']) && !empty($params['address_type']) ) {
      $select = $select->where('type = ?', $params['address_type']);
    }

    return $this->fetchRow($select);
  }
  
  /**
   * Return billing name for an order
   *
   * @param $order_id
   * @return object
   */
  public function getBillingName($order_id)
  {
    $select = $this->select()->from($this->info('name'), array('f_name', 'l_name'))->where('type = 0 AND order_id = ?', $order_id);
    return $this->fetchRow($select);
  }
  
  /**
   * Return billing email id
   *
   * @param $order_id
   * @return string
   */
  public function getBillingEmailId($order_id)
  {
    $select = $this->select()->from($this->info('name'), array('email'))->where('type = 0 AND order_id = ?', $order_id);
    return $select->query()->fetchColumn();
  }
}