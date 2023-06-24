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
class Sitestore_Model_DbTable_Writes extends Engine_Db_Table {

  protected $_rowClass = "Sitestore_Model_Write";

  public function writeContent($store_id) {
    $select = $this->select()->where('store_id = ?', $store_id);
    return $this->fetchRow($select);
  }

  public function setWriteContent($store_id, $text) {

    $this->delete(array('store_id = ?' => $store_id));
    $row = $this->createRow();
    $row->text = $text;
    $row->store_id = $store_id;
    $row->save();
  }
}
?>