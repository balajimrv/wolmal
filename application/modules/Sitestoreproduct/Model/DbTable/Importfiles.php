<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Importfiles.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Importfiles extends Engine_Db_Table {

  protected $_rowClass = "Sitestoreproduct_Model_Importfile";

  public function lastInsertedRow($store_id) {
    $importFileTable = $this->info('name');
    $select = $this->select()
            ->from($importFileTable, array(new Zend_Db_Expr('max(importfile_id)as maximum
')))
            ->where('store_id =?', $store_id);
    return $this->fetchAll($select);
  }

}