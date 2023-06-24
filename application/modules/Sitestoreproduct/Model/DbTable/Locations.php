<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locations.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Locations extends Engine_Db_Table {

  protected $_rowClass = "Sitestoreproduct_Model_Location";

  /**
   * Get location
   *
   * @param array $params
   * @return object
   */
  public function getLocation($params = array()) {

    $select = $this->select();
    if (isset($params['id'])) {
      $select->where('product_id = ?', $params['id']);
      return $this->fetchRow($select);
    }

    if (isset($params['product_ids'])) {
      $select->where('product_id IN (?)', (array) $params['product_ids']);
      return $this->fetchAll($select);
    }
  }

  public function getLocationId($product_id, $location) {

    $locationName = $this->info('name');
    $select = $this->select()->from($locationName, 'location_id');
    $location_id = $select->where('product_id = ?', $product_id)->where('location = ?', $location)->query()
            ->fetchColumn();
    return $location_id;
  }

}

?>