<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Badges.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Badges extends Engine_Db_Table {

  protected $_rowClass = "Sitestoreproduct_Model_Badge";

  /**
   * Return badge data
   *
   * @param Array $params
   * @return Zend_Db_Table_Select
   */
  public function getBadgesData($params = array()) {

    //MAKE QUERY
    $select = $this->select()->order('badge_id DESC');

    //FETCH RESULTS
    return $this->fetchAll($select);
  }

  public function getBadgeColumn($badge_id = 0, $column = '') {

    $column = $this->select()
            ->from($this->info('name'), "$column")
            ->where('badge_id = ?', $badge_id)
            ->limit(1)
            ->query()
            ->fetchColumn();

    return $column;
  }

}