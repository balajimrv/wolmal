<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Otherinfo.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Otherinfo extends Engine_Db_Table {

  protected $_rowClass = "Sitestoreproduct_Model_Otherinfo";

  /**
   * Return otheninfo object of a product
   *
   * @param $product_id
   * @return object
   */
  public function getOtherinfo($product_id) {

    $select = $this->select()->where('product_id = ?', $product_id);

    return $this->fetchRow($select);
  }

  /**
   * Return particular column value of a product
   *
   * @param $product_id
   * @return string
   */
  public function getColumnValue($product_id, $column_name) {

    $select = $this->select()
            ->from($this->info('name'), array("$column_name"));

    $select->where('product_id = ?', $product_id);

    return $select->limit(1)->query()->fetchColumn();
  }
}