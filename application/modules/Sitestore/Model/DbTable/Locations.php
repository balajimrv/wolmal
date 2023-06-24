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
class Sitestore_Model_DbTable_Locations extends Engine_Db_Table {

  protected $_rowClass = "Sitestore_Model_Location";

  /**
   * Get location
   *
   * @param array $params
   * @return object
   */
  public function getLocation($params=array()) {

    $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1);
    if ($locationFieldEnable) {
   
      $locationName = $this->info('name');

      $select = $this->select();
      if (isset($params['id']) && isset($params['mapshow']) && $params['mapshow'] == 'Map Tab') {
        $select->where('store_id = ?', $params['id']);
        if (!empty($params['mainlocationId'])) {
					$select->where('location_id <> ?', $params['mainlocationId']);
        }
        
        $select->order('location_id DESC');
        return Zend_Paginator::factory($select);
      }
      elseif (isset($params['id'])) {
        $select->where('store_id = ?', $params['id']);
        if (isset($params['location_id']) && !empty($params['location_id'])) {
					$select->where('location_id = ?', $params['location_id']);
        }
        return $this->fetchRow($select);
      }

      if (isset($params['store_ids'])) {

        $idsStr = (string) ( is_array($params['store_ids']) ? "'" . join("', '", $params['store_ids']) . "'" : $params['store_ids'] );

        $select->where('store_id IN (?)', new Zend_Db_Expr($idsStr));
        return $this->fetchAll($select);
      }
    }
  }
  public function getLocationId ($store_id, $location) {

		$locationName = $this->info('name');
		$select = $this->select()->from($locationName, 'location_id');
		$location_id = $select->where('store_id = ?', $store_id)->where('location = ?', $location)->query()
												->fetchColumn();
		return $location_id;

  }

}
?>