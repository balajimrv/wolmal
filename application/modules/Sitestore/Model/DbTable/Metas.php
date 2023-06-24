<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Metas.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Metas extends Engine_Db_Table {

  protected $_name = 'sitestore_store_fields_meta';
  protected $_rowClass = 'Sitestore_Model_Meta';

  public function getFields($mp_id) {
    //Pickup the dynamic values in the fields_meta table according to the profile type
    $rmetaName = $this->info('name');
    $maptable = Engine_Api::_()->getDBTable('maps', 'sitestore');
    $rmapName = $maptable->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($this, array($rmetaName . '.field_id', $rmetaName . '.label', $rmetaName . '.type'))
            ->join($rmapName, $rmapName . '.child_id = ' . $rmetaName . '.field_id', array())
            ->where($rmapName . '.option_id = ?', $mp_id)
            ->order($rmapName . ".order");
    //->where($rmetaName . '.type <> ?', 'heading');
    $checkval = $this->fetchAll($select);

    //Dynamic select_option created here
    $storeIndex;
    $selectOption = array();
    foreach ($checkval->toarray() as $key => $value) {

      foreach ($value as $k => $v) {

        if ($k == 'field_id')
          $storeIndex = $v;
        if ($k == 'label')
          $selectOption[$storeIndex]['lable'] = $v;
        if ($k == 'type')
          $selectOption[$storeIndex]['type'] = $v;
      }
    }
    return $selectOption;
  }

}

?>