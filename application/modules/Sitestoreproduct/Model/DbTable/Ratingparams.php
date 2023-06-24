<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ratingparams.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Ratingparams extends Engine_Db_Table {

  protected $_rowClass = 'Sitestoreproduct_Model_Ratingparam';

  /**
   * Review parameters
   *
   * @param Array $categoryIdsArray
   * @param Varchar $resource_type
   * @return Review parameters
   */
  public function reviewParams($categoryIdsArray = array(), $resource_type = null) {

    if (empty($categoryIdsArray)) {
      return null;
    }

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('ratingparam_id', 'ratingparam_name'))
            ->where("category_id IN (?)", (array) $categoryIdsArray)
            ->order("category_id");

    if (!empty($resource_type)) {
      $select->where("resource_type =?", $resource_type);
    }

    //RETURN RESULTS
    return $this->fetchAll($select);
  }

}