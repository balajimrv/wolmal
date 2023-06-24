<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Transactions.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Transactions extends Engine_Db_Table {

  protected $_rowClass = 'Sitestoreproduct_Model_Transaction';

  /**
   * Return benefit status of the current viewer during payment via Paypal or 2Checkout
   *
   * @param array $params
   * @return object
   */
  public function getBenefitStatus(User_Model_User $user = null) {
    // Get benefit setting
    $benefitSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.benefit', 'all');
    if (!in_array($benefitSetting, array('all', 'some', 'none'))) {
      $benefitSetting = 'all';
    }

    switch ($benefitSetting) {
      default:
      case 'all':
        return true;
        break;

      case 'some':
        if (!$user) {
          return false;
        }
        return (bool) $this->select()
                ->from($this, new Zend_Db_Expr('TRUE'))
                ->where('user_id = ?', $user->getIdentity())
                ->where('type = ?', 'payment')
                ->where('status = ?', 'okay')
                ->limit(1);
        break;

      case 'none':
        return false;
        break;
    }

    return false;
  }

  /**
   * Return all user transactions
   *
   * @param array $params
   * @return object
   */
  public function getAllUserTransactionsPaginator($params)
  {
    $paginator = Zend_Paginator::factory($this->getAllUserTransactions());
    if (!empty($params['page']))
    {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if (!empty($params['limit'])) 
    {
      $paginator->setItemCountPerPage($params['limit']);
    }

    return $paginator;
  }
  
  public function getAllUserTransactions()
  {
    $select = $this->select()
                   ->from($this->info('name'))
                   ->where("sender_type = 0")
                   ->order('transaction_id DESC');
   return $select;
  }

  /**
   * Return transactions state
   *
   * @param $sender_type
   * @param #store_id
   * @return string
   */
  public function getTransactionState($sender_type = false, $store_id = null)
  {
    $paymentRequestTableName = Engine_Api::_()->getDbtable('paymentrequests','sitestoreproduct')->info('name');
    $transactionTableName = $this->info('name');
    
    $select = $this->select()
                   ->setIntegrityCheck(false)
                   ->from($transactionTableName, array('state'))
                   ->distinct(true)
                   ->join($paymentRequestTableName, "($paymentRequestTableName.request_id = $transactionTableName.parent_order_id)", '');
    
    empty($sender_type) ? $select->where("sender_type = 0") : $select->where("sender_type = 1");
    
    if( !empty($store_id) )
      $select->where("$paymentRequestTableName.store_id =?", $store_id);
    
    return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
  }
  
  public function getOrderTransactionsPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getOrderTransactionsSelect($params));
    
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }    
    
    return $paginator;
  }
  
  public function getOrderTransactionsSelect($params = array())
  {
    $userTableName = Engine_Api::_()->getItemTable('user')->info('name');
    $transactionTableName = $this->info('name');
    $orderTableName = Engine_Api::_()->getDbtable('orders','sitestoreproduct')->info('name');
    
    $select = $this->select()
                   ->setIntegrityCheck(false)
                   ->from($transactionTableName, array('transaction_id', 'gateway_transaction_id', 'type', 'state', 'date'))
                   ->join($orderTableName, "($orderTableName.order_id = $transactionTableName.parent_order_id)", array('order_id', 'buyer_id', 'gateway_id', 'payment_status', 'grand_total'))
                   ->joinLeft($userTableName, "$orderTableName.buyer_id = $userTableName.user_id", array("$userTableName.username"))
                   ->where('sender_type = 0')
                   ->where("$orderTableName.direct_payment = 1");
    
    if( isset($params['store_id']) && !empty($params['store_id']) ) {
      $select->where("$orderTableName.store_id =?", $params['store_id']);
    }
    
    if( isset($params['username']) && !empty($params['username']) ) {
      $select->where($userTableName . '.username  LIKE ?', '%' . trim($params['username']) . '%');
    }
    
    if( isset($params['gateway']) && !empty($params['gateway']) ) {
      $select->where("$transactionTableName.gateway_id = ?", trim($params['gateway']));
    }

    $api = Engine_Api::_()->sitestoreproduct();
    
    if( isset($params['order_min_amount']) && !empty($params['order_min_amount']) && is_numeric($params['order_min_amount']) ) {
      $select->where("$orderTableName.grand_total >= ". $api->getPriceWithCurrency(trim($params['order_min_amount']), 1, 1));
    }
    
    if( isset($params['order_max_amount']) && !empty($params['order_max_amount']) && is_numeric($params['order_max_amount']) ) {
      $select->where("$orderTableName.grand_total <= ". $api->getPriceWithCurrency(trim($params['order_max_amount']), 1, 1)); 
    }
    
    if( isset($params['from']) && !empty($params['from']) ) {
      $select->where("CAST($transactionTableName.date AS DATE) >=?", trim($params['from']));
    }
    
    if( isset($params['to']) && !empty($params['to']) ) {
      $select->where("CAST($transactionTableName.date AS DATE) <=?", trim($params['to']));
    }
    
    $select->order("$orderTableName.order_id DESC");

    return $select;
  }

}