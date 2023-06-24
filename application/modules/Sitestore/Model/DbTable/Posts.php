<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Posts.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Posts extends Engine_Db_Table {

  protected $_rowClass = 'Sitestore_Model_Post';

	/**
   * Gets all post for topic
   *
   * @param int $store_id
   * @param int $topic_id 
   * @return Zend_Db_Table_Select
   */		  
  public function getPost($store_id, $topic_id) {
    $select = $this->select()
            ->where('store_id = ?', $store_id)
            ->where('topic_id = ?', $topic_id)
            ->order('creation_date ASC');
            
    return Zend_Paginator::factory($select);        
  }
  
}

?>