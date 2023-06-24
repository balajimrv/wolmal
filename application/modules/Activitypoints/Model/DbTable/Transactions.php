<?php
class Activitypoints_Model_DbTable_Transactions extends Engine_Db_Table
{
  protected $_name = 'semods_uptransactions';
  //protected $_primary = array('uptransaction_id');  
  
  
  function is_completed($transaction_id) {
    $transaction = $this->fetchRow( array("uptransaction_id = ?" => $transaction_id) );
    if(!$transaction) {
      return false;
    }
    return $transaction->uptransaction_state == 0;
  }
  
  function complete($transaction_id) {

    $transaction = $this->fetchRow( array("uptransaction_id = ?" => $transaction_id) );
    if(!$transaction || ($transaction->uptransaction_state == 0)) {
      return false;
    }
      
    
    $this->update(array('uptransaction_state' => 0), array('uptransaction_id = ?' => $transaction_id));
      
    // FINISH TRANSACTION - REWARD USER IF "EARNER", DO NOTHING IF "SPENDER"
    if($transaction->uptransaction_cat == 1)  {
      Engine_Api::_()->getApi('core', 'activitypoints')->addPoints($transaction->uptransaction_user_id, $transaction->uptransaction_amount);
    }
    
    return true;
  }
  
  function cancel($transaction_id) {

    $transaction = $this->fetchRow( array("uptransaction_id = ?" => $transaction_id) );
    if(!$transaction || ($transaction->uptransaction_state == 2)) {
      return false;
    }

    $this->update(array('uptransaction_state' => 2), array('uptransaction_id = ?' => $transaction_id));

    // REFUND POINTS IF "SPENDER", DO NOTHING IF "EARNER"
    if($transaction->uptransaction_cat == 2)  {
      Engine_Api::_()->getApi('core', 'activitypoints')->addPoints($transaction->uptransaction_user_id,
                                                                   abs($transaction->uptransaction_amount),
                                                                   false // do not update "total earned"
                                                                   );
    }

    return true;
  }
    



  function add( $user_id, $type, $cat, $state, $text, $amount, $item_id = 0 ) {

    $time = time();
    $time = gmdate('Y-m-d G:i:s', $time);
    
    $values = array( 'uptransaction_user_id'  => $user_id,
                    'uptransaction_type'  => $type,
                    'uptransaction_cat' => $cat,
                    'uptransaction_state' => $state,
                    'uptransaction_text'  => $text,
                    //'uptransaction_date'  => 'UNIX_TIMESTAMP( NOW() )',
                    'uptransaction_date'  => $time,
                    'uptransaction_amount'  => $amount,
                    'uptransaction_item_id'  => $item_id
                    
                    );
    
    $transaction_id = $this->insert( $values );
  
    return $transaction_id;
  }

}