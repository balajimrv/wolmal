<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Addresses.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Addresses extends Engine_Db_Table {

  protected $_name = 'sitestoreproduct_addresses';
  protected $_rowClass = 'Sitestoreproduct_Model_Address';
  
  /**
   * Return billing or shipping address detail
   *
   * @param array $param
   * @return object
   */
  public function getAddress($params) {
    $select = $this->select();

    if (!empty($params['owner_id'])) {
      $select->where('owner_id = ?', $params['owner_id']);
    }
    
    if (!empty($params['type'])) {
      $select->where('type = ?', $params['type']);
    }

    if (!empty($params['single_row'])) {
      return $this->fetchRow($select);
    }

    return $this->fetchAll($select);
  }
}