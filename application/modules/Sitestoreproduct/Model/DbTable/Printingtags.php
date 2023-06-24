<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Printingtags.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Printingtags extends Engine_Db_Table {

  protected $_rowClass = "Sitestoreproduct_Model_Printingtag";

  public function getPrintingTagPaginator($params = array()) {
    $paginator = Zend_Paginator::factory($this->getPrintingTagSelect($params));

    if (!empty($params['page']))
      $paginator->setCurrentPageNumber($params['page']);

    if (empty($params['limit']))
      $paginator->setItemCountPerPage(8);
    else
      $paginator->setItemCountPerPage($params['limit']);

    return $paginator;
  }



  public function getPrintingTagSelect($params) {
    $printTagTable = $this->info('name');
//    $tagMapTableName = Engine_Api::_()->getDbtable('tagmappings', 'sitestoreproduct')->info('name');
//    $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
//    $productTableName = $productTable->info('name');

    $select = $this->select()
              ->from($printTagTable)
              ->where("store_id =?", $params['store_id'])
              ->order("printingtag_id DESC");
    
    
//    $select = $productTable->select()
//            ->setIntegrityCheck(false)
//            ->from($productTableName, null)
////            ->join($tagMapTableName, "$productTableName.`product_id` = $tagMapTableName.`product_id`", array("COUNT(".$tagMapTableName.".product_id) as products"))
////            
////            ->joinRight($printTagTable, "$printTagTable.printingtag_id = $tagMapTableName.printingtag_id")
//            ->where($printTagTable . '.store_id = ?', $params['store_id']);
////            ->group($printTagTable . '.printingtag_id');

    return $select;
  }

  public function getPrintingTags($store_id) {

    $select = $this->select();
    $select->where("store_id = ?", $store_id)
            ->where("status = ?", 1);

    return $this->fetchAll($select);
  }

  public function getPrintingTagsByProduct($product_id, $store_id) {
    $printTagTable = $this->info('name');
//    $tagMapTableName = Engine_Api::_()->getDbtable('tagmappings', 'sitestoreproduct')->info('name');

    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($printTagTable);
//            ->joinLeft($tagMapTableName, "$printTagTable.printingtag_id = $tagMapTableName.printingtag_id", array('product_id'));
//    $select->where($tagMapTableName . '.product_id = ?', $product_id);
    $select->where($printTagTable . '.store_id = ?', $store_id);
    $select->where($printTagTable . '.status = ?', 1);
    return $this->fetchAll($select);
  }

}