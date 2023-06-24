<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Orderdownpayments.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Orderdownpayments extends Engine_Db_Table
{
  protected $_name = 'sitestoreproduct_order_downpayments';
  protected $_rowClass = 'Sitestoreproduct_Model_Orderdownpayment';
  
  /**
   * If remaining amount request exist, then return gateway_id
   *
   * @param $order_id int
   * @return gateway_id
   */
  public function isRemainingAmountRequestExist($order_id)
  {
    return $this->select()->from($this->info('name'), 'gateway_id')->where('order_id = ?', $order_id)->query()->fetchColumn();
  }
  
  public function getRemainingAmountPaymentDetail($params) {
    $select = $this->select()
                   ->where("order_id = ?", $params['order_id'])
                   ->where("payment_status = 'active' OR gateway_id = 3")
                   ->limit(1);
    return $this->fetchRow($select);
  }

}