<?php
class Activitypoints_Model_DbTable_Spender extends Engine_Db_Table
{
  protected $_name = 'semods_userpointspender';
  
  public function getCostByType($type) {

    $select = $this->getTable()->select()
              ->columns('userpointspender_cost')
              ->where("userpointspender_type = ?", $type);
    
    $row = $this->getTable()->fetchRow($select);

    return $row ? $row->userpointspender_cost : 0;
    
  }

  public function getTable()
  {
    return $this;
  }

}