<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Paymentrequests.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Paymentrequests extends Engine_Db_Table
{
  protected $_name = 'sitestoreproduct_payment_requests';
  protected $_rowClass = 'Sitestoreproduct_Model_Paymentrequest';
  
  /**
   * Return store payment request object
   *
   * @param array $params
   * @return object
   */
  public function getStorePaymentRequestPaginator($params = array()) {
      
    $paginator = Zend_Paginator::factory($this->getStorePaymentRequestSelect($params));
    
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }    
    
    return $paginator;
  }
  
  public function getStorePaymentRequestSelect($params)
  {
    $select = $this->select()->where('store_id =?', $params['store_id'])->group('request_id');
   
    if (isset($params['search'])) {
      if (!empty($params['request_date']))
         $select->where("CAST(request_date AS DATE) =?", trim($params['request_date']));

      if (!empty($params['response_date']))
        $select->where("CAST(response_date AS DATE) =?", trim($params['response_date']));      

     $api = Engine_Api::_()->sitestoreproduct();

      if (!empty($params['request_min_amount']))
        $select->where("request_amount >=?", $api->getPriceWithCurrency(trim($params['request_min_amount']), 1, 1)); 

      if (!empty($params['request_max_amount']))
        $select->where("request_amount <=?", $api->getPriceWithCurrency(trim($params['request_max_amount']), 1, 1));  

      if (!empty($params['response_min_amount']))
        $select->where("response_amount >=?", $api->getPriceWithCurrency(trim($params['response_min_amount']), 1, 1)); 

      if (!empty($params['response_max_amount']))
        $select->where("response_amount <=?", $api->getPriceWithCurrency(trim($params['response_max_amount']), 1, 1)); 

      if (!empty($params['request_status'])) {
        $params['request_status']--;
        $select->where('request_status =? ', $params['request_status']);
      }
    }
    
    $select->order('request_id DESC');
    return $select;
  }

  /**
   * Return response detail for a payment request id
   *
   * @param $request_id
   * @return object
   */
  public function getResponseDetail($request_id)
  {
    return $this->select()->from($this->info('name'),array('response_amount', 'store_id', 'response_message'))->where('request_id =?', $request_id)->query()->fetchAll();
  }

  /**
   * Return sum of requested amount of a store
   *
   * @param $request_id
   * @return float
   */
  public function getRequestedAmount($store_id)
  {
    return $this->select()
                ->from($this->info('name'),array('SUM(request_amount)'))
                ->where('store_id =? AND request_status = 0', $store_id)
                ->query()
                ->fetchColumn();
  }
  
  /**
   * Return all admin transactions detail for a store
   *
   * @param array $params
   * @return object
   */
  public function getAllAdminTransactionsPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getAllAdminTransactionsSelect($params));
    
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }    
    
    return $paginator;
  }
  
  public function getAllAdminTransactionsSelect($params)
  {
    $transactionTableName = Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct')->info('name');
    $paymentRequestTableName = $this->info('name');

    $select = $this->select()
                   ->setIntegrityCheck(false)
                   ->from($paymentRequestTableName, array("$paymentRequestTableName.request_id", "$paymentRequestTableName.store_id", "$paymentRequestTableName.response_amount", "$paymentRequestTableName.gateway_id", "$paymentRequestTableName.gateway_profile_id", "$paymentRequestTableName.response_date"))
                   ->join($transactionTableName, "($transactionTableName.parent_order_id = $paymentRequestTableName.request_id)", array("$transactionTableName.transaction_id", "$transactionTableName.type", "$transactionTableName.state"))            
                   ->where("$transactionTableName.sender_type = 1")
                   ->where("$paymentRequestTableName.store_id =?", $params['store_id'])
                   ->group($paymentRequestTableName . '.request_id');

    if (isset($params['search'])) {
      if (!empty($params['date']))
        $select->where("CAST($transactionTableName.date AS DATE) =?", trim($params['date']));

      $api = Engine_Api::_()->sitestoreproduct();

       if (!empty($params['response_min_amount']))
        $select->where("$paymentRequestTableName.response_amount >=?", $api->getPriceWithCurrency(trim($params['response_min_amount']), 1, 1));  
      
       if (!empty($params['response_max_amount']))
        $select->where("$paymentRequestTableName.response_amount <=?", $api->getPriceWithCurrency(trim($params['response_max_amount']), 1, 1));
      
      if (!empty($params['state'])) {
        switch ($params['state']) {
          case 1:
            $state = 'processing';
            break;

          case 2:
            $state = 'pending';
            break;
        }

        $params['state']--;

        $select->where($transactionTableName . '.state LIKE ? ', '%' . $state . '%');
      }
    }
    
    $select->order('transaction_id DESC');

    return $select;
  }
}