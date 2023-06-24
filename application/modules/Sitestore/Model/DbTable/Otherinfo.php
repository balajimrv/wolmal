<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Otherinfo.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Otherinfo extends Engine_Db_Table {

//  protected $_rowClass = "Sitestoreproduct_Model_Otherinfo";

  /**
   * Return an attribute value of a store
   *
   * @param $store_id
   * @param $attrib_name
   * @return attrib value
   */
  public function getStoreAttribs($store_id, $attrib_name) {

    $select = $this->select()
            ->from($this->info('name'), $attrib_name)
            ->where('store_id = ?', $store_id);

    return $select->query()->fetchColumn();
  }
}