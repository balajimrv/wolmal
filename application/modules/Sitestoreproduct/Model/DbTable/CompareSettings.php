<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: CompareSettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_CompareSettings extends Engine_Db_Table {

  public function getCompareList($params = array()) {
    $categoriesTable = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct');

    $tableName = $this->info('name');
    $categoriesTableName = $categoriesTable->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($tableName)
            ->join($categoriesTableName, "$categoriesTableName.category_id = $tableName.category_id   ", array($categoriesTableName . '.category_name'));
    
    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select->where($categoriesTableName . '.category_id =? ', $params['category_id']);
    }

    if (isset($params['fetchRow']) && !empty($params['fetchRow'])) {
      return $this->fetchRow($select);
    }

    return $result = $this->fetchAll($select);
  }

}