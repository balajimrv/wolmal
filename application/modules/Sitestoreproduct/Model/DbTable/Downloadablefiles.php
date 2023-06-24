<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Downloadablefiles.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Downloadablefiles extends Engine_Db_Table
{
  protected $_name = 'sitestoreproduct_downloadable_files';
  protected $_rowClass = 'Sitestoreproduct_Model_Downloadablefile';

  /**
   * Return downloadable files when seller view
   *
   * @param array $params
   * @return object
   */
  public function getDownloadableFilesPaginator($params=array()) {

    $paginator = Zend_Paginator::factory($this->getDownloadableFilesSelect($params));

    if (!empty($params['page']))
      $paginator->setCurrentPageNumber($params['page']);

    if (empty($params['limit']))
      $paginator->setItemCountPerPage(20);
    else
      $paginator->setItemCountPerPage($params['limit']);

    return $paginator;
  }
  
  public function getDownloadableFilesSelect($params) {    
    
    $select = $this->select();
    if(!empty($params['product_id'])){
      $select->where('product_id = ?', $params['product_id']);
    }
    
    if(!empty($params['type'])){
      $select->where('type LIKE ?', $params['type']);
    }
    
    if (empty($params['orderby'])) {
      $select->order('downloadablefile_id DESC');
    }else{
      $select->order('creation_date DESC');
    }

    return $select;
  }
  
  /**
   * Return downloadable files when an order has been placed
   *
   * @param array $params
   * @return object
   */
  public function getDownloadableFiles($params) { 
    $downloadableFileTableName = $this->info('name');
    $productsTableName = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($downloadableFileTableName)
            ->join($productsTableName, "($downloadableFileTableName.product_id = $productsTableName.product_id)", 'store_id');
            
    if(!empty($params['product_id'])){
      $select->where("$downloadableFileTableName.product_id = ?", $params['product_id']);
    }
    
    if(!empty($params['type'])){
      $select->where("$downloadableFileTableName.type LIKE ?", $params['type']);
    }
    
    if(!empty($params['status'])){
      $select->where("$downloadableFileTableName.status = ?", $params['status']);
    }
    
    if (!empty($params['orderby'])) {
      $select->order("$downloadableFileTableName.creation_date DESC");
    }else{
      $select->order("$downloadableFileTableName.downloadablefile_id DESC");
    }

    return $select->query()->fetchAll();
  }
  
  /**
   * Return sample files
   *
   * @param array $params
   * @return object
   */
  public function getSampleFiles($params) { 
    $select = $this->select()
            ->where("type LIKE 'sample'")
            ->where("status = 1");
    
    if(!empty($params['product_id'])){
      $select->where('product_id = ?', $params['product_id']);
    }
      $select->order('downloadablefile_id DESC');

    return $this->fetchAll($select);
  }
  
  /**
   * Return file status
   *
   * @param array $params
   * @return object
   */
  public function getUploadStatus($params){
    
    $select = $this->select()
            ->from($this->info('name'), array("SUM(size) as size", "COUNT(downloadablefile_id) as files"))
            ->where('product_id = ?', $params['product_id'])
            ->where('type LIKE ?', $params['type'])
            ->group('product_id');
    
    return $select->query()->fetch();
  }
  
  /**
   * Return count of main files
   *
   * @param productId
   * @return int
   */
  public function isAnyMainFileExist($productId){
    
    $select = $this->select()
            ->from($this->info('name'), array("downloadablefile_id"))
            ->where('product_id = ?', $productId)
            ->where("type LIKE 'main'")
            ->where("status = 1")
            ->limit(1)
            ->query()->fetchAll();
    
    return @count($select);
  }
  
  /**
   * Return the file id
   *
   * @param array $params
   * @return object
   */
  public function getFileId($params){
    
    $select = $this->select()
            ->from($this->info('name'), array("downloadablefile_id"))
            ->where('product_id = ?', $params['product_id'])
            ->where('type LIKE ?', $params['type'])
            ->where('filename LIKE ?', $params['filename']);
    return $select->query()->fetchColumn();
  }
  
}