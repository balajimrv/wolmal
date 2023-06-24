<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Documents.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Documents extends Engine_Db_Table {
  protected $_rowClass = "Sitestoreproduct_Model_Document";
  
   public function getDocumentsPaginator($params) {
    $paginator = Zend_Paginator::factory($this->getProductDocumentSelect($params));

    if (!empty($params['page']))
      $paginator->setCurrentPageNumber($params['page']);

    if (empty($params['limit']))
      $paginator->setItemCountPerPage(20);
    else
      $paginator->setItemCountPerPage($params['limit']);

    return $paginator;
  }
  
  public function getProductDocumentSelect($params) {
    $select = $this->select();
    if(isset($params['product_id']) && !empty ($params['product_id']))
         $select->where('product_id = ?', $params['product_id']);
    if(isset($params['status']) && !empty ($params['status']))
         $select->where('status = ?', $params['status']);
      if(isset($params['approve']) && !empty ($params['approve']))
         $select->where('approve = ?', $params['approve']);
     $select ->order('document_id DESC');
     return $select;
  }
  
  public function getwidgetProductDocumentSelect($params) {
    $select = $this->select();
    if(isset($params['product_id']) && !empty ($params['product_id']))
         $select   ->where('product_id = ?', $params['product_id']);
    if(isset($params['status']) && !empty ($params['status']))
         $select   ->where('status = ?', $params['status']);
         $select   ->where('approve = ?', 1);
     $select ->order('document_id DESC');
    return $select;
  }
  
}