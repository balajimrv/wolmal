<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Sections.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Sections extends Engine_Db_Table {

  /**
   * Return categories
   *
   * @param int $home_store_display
   * @return categories
   */
  public function getSections($home_store_display) {
    $productTableName = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->info('name');
    $sectionTableName = $this->info('name');

    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($sectionTableName)
            ->joinLeft($productTableName, "$sectionTableName.section_id = $productTableName.section_id", array("COUNT(product_id) as count"))
            ->where("$sectionTableName.store_id =?", $home_store_display)
            ->group("$sectionTableName.section_id")
            ->order('sec_order ASC');
            

    return $this->fetchAll($select);
  }

  public function getStoreSections($store_id) {
    $select = $this->select();
    $select->where('store_id = ?', $store_id);
    $select->order('sec_order ASC');
    return $this->fetchAll($select);
  }

  public function getStoreSectionList($store_id, $limit, $order, $product) {
    $productTableName = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->info('name');
    $sectionTableName = $this->info('name');
    $select = $this->select();
    $select
            ->setIntegrityCheck(false)
            ->from($sectionTableName)
            ->joinLeft($productTableName, "$sectionTableName.section_id = $productTableName.section_id", array("COUNT(product_id) as count"))
            ->group("$sectionTableName.section_id")
            ->where($sectionTableName . '.store_id = ?', $store_id);

    if (isset($product) && !empty($product)) {
      if (isset($order) && !empty($order))
        $select->order('count DESC');else
        $select->order('count ASC');
    }

    if (!isset($product) && empty($product)) {
      if (isset($order) && !empty($order))
        $select->order('sec_order DESC');
      else
        $select->order('sec_order ASC');
    }

    if (isset($limit) && !empty($limit)) {
      $select->limit($limit);
    }

    return $this->fetchAll($select);
  }
  public function isSectionExist($section_name, $store_id) {
    $select = $this->select();
    $select->where('store_id = ?', $store_id);
    $select->where('section_name = ?', $section_name);
    return $this->fetchAll($select);
  }
 public function getSectionName($section_id) {
    $getSectionName = $this->select()
                    ->from($this->info('name'), array("section_name"))
                    ->where('section_id =?', $section_id)
                    ->limit(1)
                    ->query()->fetchColumn();
    return $getSectionName;
  }
}