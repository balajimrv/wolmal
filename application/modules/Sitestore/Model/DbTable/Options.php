<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Options.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Options extends Engine_Db_Table {

  protected $_name = 'sitestore_store_fields_options';
  protected $_rowClass = 'Sitestore_Model_Option';

  public function getAllProfileTypes() {
    $select = $this->select()
            ->where('field_id = ?', 1);
    $result = $this->fetchAll($select);
    return $result;
  }

}

?>