<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Orderdownloads.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Orderdownloads extends Engine_Db_Table
{
  protected $_name = 'sitestoreproduct_order_downloads';
  protected $_rowClass = 'Sitestoreproduct_Model_Orderdownload';

  /**
   * Return downloadable files for an buyer
   *
   * @param array $params
   * @return object
   */
  public function getOrderDownloadsPaginator($params=array()) {

    $paginator = Zend_Paginator::factory($this->getOrderDownloadsSelect($params));
    
    if (!empty($params['page']))
      $paginator->setCurrentPageNumber($params['page']);

    if (empty($params['limit']))
      $paginator->setItemCountPerPage(20);
    else
      $paginator->setItemCountPerPage($params['limit']);

    return $paginator;
  }
  
  public function getOrderDownloadsSelect($params) {    
    
    $ordersTablename = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->info('name');
    $downloadableFilesTablename = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->info('name');
    $orderDownloadsTablename = $this->info('name');
    
    $select = $this->select()
                   ->setIntegrityCheck(false)
                   ->from($orderDownloadsTablename)
                   ->join($ordersTablename, "($orderDownloadsTablename.order_id = $ordersTablename.order_id)", array("$ordersTablename.order_id", "$ordersTablename.order_status"))
                   ->join($downloadableFilesTablename, "($orderDownloadsTablename.downloadablefile_id = $downloadableFilesTablename.downloadablefile_id)");
    
    if(!empty($params['buyer_id'])){
      $select->where('buyer_id = ?', $params['buyer_id']);
    }
    $select->order("$orderDownloadsTablename.orderdownload_id DESC");

    return $select;
  }
}