<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Storebills.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Storebills extends Engine_Db_Table
{
  protected $_name = 'sitestoreproduct_storebills';
  
  protected $_rowClass = 'Sitestoreproduct_Model_Storebill';

  protected $_serializedColumns = array('config');

  protected $_cryptedColumns = array('config');
  
  static private $_cryptKey;
  
  /**
   * Return store bill object
   *
   * @param array $params
   * @return object
   */
  public function getStoreBillPaginator($params = array()) {
      
    $paginator = Zend_Paginator::factory($this->getStoreBillSelect($params));
    
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }    
    
    return $paginator;
  }
  
  public function getStoreBillSelect($params)
  {
    $select = $this->select()->where('store_id =?', $params['store_id']);
   
    if (isset($params['search'])) {
      if (!empty($params['from']))
         $select->where("CAST(creation_date AS DATE) >=?", trim($params['from']));
      
      if (!empty($params['to']))
         $select->where("CAST(creation_date AS DATE) <=?", trim($params['to']));

       $api = Engine_Api::_()->sitestoreproduct();

      if (!empty($params['bill_min_amount']) && is_numeric($params['bill_min_amount']))
        $select->where("amount >=?", $api->getPriceWithCurrency(trim($params['bill_min_amount']), 1, 1)); 

      if (!empty($params['bill_max_amount']) && is_numeric($params['bill_max_amount']))
        $select->where("amount <=?", $api->getPriceWithCurrency(trim($params['bill_max_amount']), 1, 1)); 

      if (!empty($params['payment']) && $params['payment'] == 1) {
        $select->where("status = 'active'");
      }
      
      if (!empty($params['payment']) && $params['payment'] == 2) {
        $select->where("status != 'active'");
      }
    }

    $select->order('storebill_id DESC');
    return $select;
  }
  
  /**
   * Return total paid bill amount
   *
   * @param int $store_id
   * @return float
   */
  public function totalPaidBillAmount($store_id)
  {
    $storeBillTableName = $this->info('name');
    
    $select = $this->select()
                   ->from($storeBillTableName, array("SUM(amount)"))
                   ->where("store_id =?", $store_id)
                   ->where("status = 'active'")
                   ->query()->fetchColumn();
    return empty($select) ? 0 : $select;
  }
  
  /**
   * Return total failed bill payment amount
   *
   * @param int $store_id
   * @return float
   */
  public function paymentFailedBillAmount($store_id)
  {
    $select = $this->select()
                   ->from($this->info('name'), array("SUM(amount)"))
                   ->where("store_id =?", $store_id)
                   ->where("status != 'active'")
                   ->where("status != 'not_paid'") 
                   ->query()->fetchColumn();
    return $select;
  }
  
  public function getPaidCommissionDetail()
  {
    $select = $this->select()
                   ->from($this->info('name'), array("SUM(amount) as paid_commission", "store_id"))
                   ->where("status = 'active'")
                   ->group("store_id");

    return $select->query()->fetchAll();
  }
  
  public function getEnabledGatewayCount()
  {
    return $this->select()
      ->from($this, new Zend_Db_Expr('COUNT(*)'))
      ->where('enabled = ?', 1)
      ->query()
      ->fetchColumn()
      ;
  }

  public function getEnabledGateways()
  {
    return $this->fetchAll($this->select()->where('enabled = ?', true));
  }


  // Inline encryption/decryption
  public function insert(array $data)
  {
    // Serialize
    $data = $this->_serializeColumns($data);
    
    // Encrypt each column
    foreach( $this->_cryptedColumns as $col ) {
      if( !empty($data[$col]) ) {
        $data[$col] = self::_encrypt($data[$col]);
      }
    }
    
    return parent::insert($data);
  }

  public function update(array $data, $where)
  {
    // Serialize
    $data = $this->_serializeColumns($data);

    // Encrypt each column
    foreach( $this->_cryptedColumns as $col ) {
      if( !empty($data[$col]) ) {
        $data[$col] = self::_encrypt($data[$col]);
      }
    }

    return parent::update($data, $where);
  }

  protected function _fetch(Zend_Db_Table_Select $select)
  {
    $rows = parent::_fetch($select);

    foreach( $rows as $index => $data ) {
      // Decrypt each column
      foreach( $this->_cryptedColumns as $col ) {
        if( !empty($rows[$index][$col]) ) {
          $rows[$index][$col] = self::_decrypt($rows[$index][$col]);
        }
      }
      // Unserialize
      $rows[$index] = $this->_unserializeColumns($rows[$index]);
    }

    return $rows;
  }


  // Crypt Utility
  
  static private function _encrypt($data)
  {
    if( !extension_loaded('mcrypt') ) {
      return $data;
    }

    $key = self::_getCryptKey();
    $cryptData = mcrypt_encrypt(MCRYPT_DES, $key, $data, MCRYPT_MODE_ECB);

    return $cryptData;
  }

  static private function _decrypt($data)
  {
    if( !extension_loaded('mcrypt') ) {
      return $data;
    }

    $key = self::_getCryptKey();
    $cryptData = mcrypt_decrypt(MCRYPT_DES, $key, $data, MCRYPT_MODE_ECB);
    $cryptData = rtrim($cryptData, "\0");

    return $cryptData;
  }

  static private function _getCryptKey()
  {
    if( null === self::$_cryptKey ) {
      $key = Engine_Api::_()->getApi('settings', 'core')->core_secret
        . '^'
        . Engine_Api::_()->getApi('settings', 'core')->payment_secret;
      self::$_cryptKey  = substr(md5($key, true), 0, 8);
    }
    
    return self::$_cryptKey;
  }

}