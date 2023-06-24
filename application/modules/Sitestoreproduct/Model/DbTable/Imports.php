<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Imports.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Imports extends Engine_Db_Table {

  protected $_rowClass = "Sitestoreproduct_Model_Import";

  public function latestImportedFiles($firstImportId, $lastImportId, $store_id) {
    $importTableName = $this->info('name');
    $select = $this->select()
            ->where('store_id =?', $store_id)
            ->where('import_id BETWEEN ' . $firstImportId . ' AND ' . $lastImportId);
    return $this->fetchAll($select);
  }

}