<?php
class Activitypoints_Model_DbTable_Points extends Engine_Db_Table
{
  protected $_name = 'semods_userpoints';
  
  public function getBalance($user_id) {


    $select = $this->getTable()->select()
              ->where("userpoints_user_id = ?", $user_id);
    
    $row = $this->getTable()->fetchRow($select);

    return $row ? $row->userpoints_count : 0;
    
  }

  public function get($user_id) {


    $select = $this->getTable()->select()
              ->where("userpoints_user_id = ?", $user_id);
    
    $row = $this->getTable()->fetchRow($select);

    // @tbd
    return $row ? $row : array('userpoints_user_id'     => $user_id,
                               'userpoints_count'       => 0,
                               'userpoints_totalearned' => 0,
                               'userpoints_totalspent'  => 0
                               );
    
  }

  public function tryDeduct($user_id, $amount) {

    $table_name = $this->getTable()->info('name');
    $select = $this->getTable()->select()
              ->from($table_name, 'COUNT(*) as `total`')
              ->where("userpoints_user_id = ?", $user_id)
              ->where("(userpoints_count - ?) >= 0", $amount);
    
    $row = $this->getTable()->fetchRow($select);

    return $row->total == 1;
    
  }



  public function deduct($user_id, $amount, $allowNegativeCredit = false) {
    
    if($allowNegativeCredit) {
      
      $updateCount = $this->update(array(
        'userpoints_count' => new Zend_Db_Expr('userpoints_count - ' . $this->getAdapter()->quote($amount)),
        'userpoints_totalspent' => new Zend_Db_Expr('userpoints_totalspent + ' . $this->getAdapter()->quote($amount)),
      ), array(
        'userpoints_user_id = ?' => $user_id,
      ));
      
      $success = ($updateCount == 1);
      
    } else {

      $updateCount = $this->update(array(
        'userpoints_count' => new Zend_Db_Expr('userpoints_count - ' . $this->getAdapter()->quote($amount)),
        'userpoints_totalspent' => new Zend_Db_Expr('userpoints_totalspent + ' . $this->getAdapter()->quote($amount)),
      ), array(
        'userpoints_user_id = ?' => $user_id,
        'userpoints_count >= ?' => $amount,
      ));
      
      $success = ($updateCount == 1);
      
    }
  
    return $success;
    
  }



  public function add($user_id, $amount, $update_totalearned = true) {
    
    $db = $this->getTable()->getAdapter();
    $tableName = $this->getTable()->info("name");

    if($update_totalearned) {

      $sql = "INSERT INTO `{$tableName}` (userpoints_user_id, userpoints_count, userpoints_totalearned)
                VALUES ( ?, ?, ? )
                ON DUPLICATE KEY UPDATE
                userpoints_count = userpoints_count + ?,
                userpoints_totalearned = userpoints_totalearned + ?";
      
      $values = array('userpoints_user_id'      => $user_id,
                      'userpoints_count'        => $amount, 
                      'userpoints_totalearned'  => $amount,
                      );

      $values2 = array('userpoints_count'        => $amount, 
                      'userpoints_totalearned'  => $amount,
                      );
      

    } else {

      $sql = "INSERT INTO $tableName (userpoints_user_id, userpoints_count, userpoints_totalearned)
                VALUES ( ?, ?, ? )
                ON DUPLICATE KEY UPDATE
                userpoints_count = userpoints_count + ?,
                userpoints_totalearned = userpoints_totalearned";
      
      $values = array('userpoints_user_id'      => $user_id,
                      'userpoints_count'        => $amount, 
                      'userpoints_totalearned'  => $amount,
                      );

      $values2 = array(
                      'userpoints_count'        => $amount, 
                      );

    }

    $db->query($sql, array_merge(array_values($values), array_values($values2)));
    
  }




  public function set($user_id, $amount) {

    $db = $this->getTable()->getAdapter();
    $tableName = $this->getTable()->info("name");

    $sql = "INSERT INTO $tableName (userpoints_user_id, userpoints_count)
              VALUES ( ?, ? )
              ON DUPLICATE KEY UPDATE
              userpoints_count = ?";
    
    $values = array('userpoints_user_id'      => $user_id,
                    'userpoints_count'        => $amount, 
                    );

    $values2 = array(
                    'userpoints_count'        => $amount, 
                    );

    $db->query($sql, array_merge(array_values($values), array_values($values2)));

  }



  public function tryDeductByType($user_id, $type) {

    $table_name = $this->getTable()->info('name');
    $table_spender = Engine_Api::_()->getDbTable('spender', 'activitypoints');

    $subselect = $table_spender->select()
                  ->from($table_spender->info('name'), 'userpointspender_cost')
                  ->where('userpointspender_type = ?', $type)
                  ->limit(1);

    $select = $this->getTable()->select()
              ->from($table_name, 'COUNT(*) as `total`')
              ->where("userpoints_user_id = ?", $user_id)
              ->where("(userpoints_count - (?)) >= 0", $subselect->__toString());
    
    $row = $this->getTable()->fetchRow($select);

    return $row->total == 1;

    
  }




  public function tryDeductById($user_id, $id) {

    $table_name = $this->getTable()->info('name');
    $table_spender = Engine_Api::_()->getDbTable('spender', 'activitypoints');

    $subselect = $table_spender->select()
                  ->from($table_spender->info('name'), 'userpointspender_cost')
                  ->where('userpointspender_id = ?', $id)
                  ->limit(1);

    $select = $this->getTable()->select()
              ->from($table_name, 'COUNT(*) as `total`')
              ->where("userpoints_user_id = ?", $user_id)
              ->where("(userpoints_count - (?)) >= 0", $subselect->__toString());
    
    $row = $this->getTable()->fetchRow($select);

    return $row->total == 1;
    
  }
  
  public function deductByType($user_id, $type, $allowNegativeCredit = false) {

    $table_name = $this->getTable()->info('name');
    $table_spender = Engine_Api::_()->getDbTable('spender', 'activitypoints');

    $amount = Engine_Api::_()->getDbTable('spender', 'activitypoints')->getCostByType($type);

    return $this->deduct( $user_id, $amount, $allowNegativeCredit );
    
  }


  public function getRank($user_id) {

    $table = $this->getTable();
    $table_name = $this->getTable()->info('name');

	$ranking_base = Semods_Utils::getSetting('activitypoints.topusers_rankby',0);
	$ranking_base = ($ranking_base == 0) ? 'userpoints_totalearned' : 'userpoints_count';

    $subselect = $table->select()
                  ->from($table_name, $ranking_base)
                  ->where('userpoints_user_id= ?', $user_id);

    $select = $this->getTable()->select()
    //SES Custm work
              ->from($table_name, 'count(*) as rank')
    //SES Cutom work
//              ->from($table_name, 'COUNT(*)+1 as rank')
              ->where("$ranking_base > ({$subselect->__toString()})", $subselect->__toString());
    
    $row = $this->getTable()->fetchRow($select);

    return $row ? $row->rank : 0;



    

    // FAST. This query shares the place for equal score and makes "ceiling" for shared rank positions
    //       ( "SELECT COUNT(*)+1 AS rank
    //          FROM se_semods_userpoints
    //          WHERE userpoints_totalearned > (SELECT userpoints_totalearned FROM se_semods_userpoints WHERE userpoints_user_id=$user_id)" )
    
  }





  

  public function getTable()
  {
    return $this;
  }

}