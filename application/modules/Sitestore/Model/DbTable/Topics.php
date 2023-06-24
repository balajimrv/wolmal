<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topics.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Topics extends Engine_Db_Table {

  protected $_rowClass = 'Sitestore_Model_Topic';

  /**
   * Return store topics
   *
   * @param int $store_id
   * @return Zend_Db_Table_Select
   */  
  public function getStoreTopics($store_id) {

    $select = $this->select()
            ->where('store_id = ?', $store_id)
            ->order('sticky DESC')
            ->order('modified_date DESC');
    if (isset($params['resource_type']) && $params['resource_type'])
      $select->where('resource_type = ?', $params['resource_type']);
    if (isset($params['resource_id']) && $params['resource_id'])
      $select->where('resource_id = ?', $params['resource_id']);

    return Zend_Paginator::factory($select);
  }

}
?>