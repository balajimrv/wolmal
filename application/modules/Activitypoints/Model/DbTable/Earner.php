<?php
class Activitypoints_Model_DbTable_Earner extends Engine_Db_Table
{
  protected $_name = 'semods_userpointearner';

  public function getType($item_id) {
  
    $select = $this->getTable()->select()
              ->columns('userpointearnertype_name')
              ->where("userpointearnertype_id = ?", $item_id);
    
    $row = $this->getTable()->fetchRow($select);
  
    return $row ? $row->userpointearnertype_name : null;
    
  }

}