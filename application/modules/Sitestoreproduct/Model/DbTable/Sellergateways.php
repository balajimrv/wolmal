<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: StoreGateways.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Sellergateways extends Engine_Db_Table {
//  protected $_rowClass = 'Sitestoreproduct_Model_Cartproduct';
  protected $_name = 'sitestoreproduct_store_gateways';
  
  public function getStoreChequeDetail($params = array())
  {
    $select = $this->select()
                   ->from($this->info('name'), 'details')
                   ->where("title =?", "ByCheque")
                   ->where("details IS NOT NULL")
                   ->limit(1);
    if( isset($params['store_id']) && !empty($params['store_id']) )
      $select->where("store_id =?", $params['store_id']);
    
    if( isset($params['storegateway_id']) && !empty($params['storegateway_id']) )
      $select->where("storegateway_id =?", $params['storegateway_id']);
    return $select->query()->fetchColumn();
  }
  
  public function isGatewayEnable($params = array()) {
    $select = $this->select()
            ->from($this->info('name'), 'storegateway_id')
            ->where('title =?', $params['title'])
            ->where('store_id =?', $params['store_id']);
    
    if( isset( $params['gateway_type'] ) && !empty( $params['gateway_type'] ) ) 
      $select->where('gateway_type =?', $params['gateway_type']);
    
    if( isset( $params['detailNotNull'] ) && !empty( $params['detailNotNull'] ) ) 
      $select->where("details IS NOT NULL");
    return $select->limit(1)->query()->fetchColumn();
  }
  
  public function getStoreEnabledGateway($params = array()) {
    $select = $this->select()
            ->from($this->info('name'), 'title')
            ->where('enabled =?', 1)
            ->where('store_id =?', $params['store_id'])
            ->where('gateway_type =?', $params['gateway_type']);
    return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
  }
}