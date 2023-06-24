<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Writes.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Announcements extends Engine_Db_Table {

  protected $_name = 'sitestore_announcements';
  protected $_rowClass = 'Sitestore_Model_Announcement';

  public function announcements($store_id, $limit = null) {
  
    $announcementTableName = $this->info('name');
    
    $select = $this->select()->where('store_id = ?', $store_id);
    if (!empty($limit)) {
			$select->where($announcementTableName . '.status = ?', 1)
			      ->where($announcementTableName . '. startdate <= ?', date('y-m-d'))
            ->where($announcementTableName . '. expirydate >= ?', date('y-m-d'))
			      ->limit($limit);
    }
    $select->order($announcementTableName . '.creation_date DESC');
    
    return $this->fetchAll($select);
  }
}