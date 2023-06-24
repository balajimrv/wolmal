<?php
class Sescustomize_Model_DbTable_Ebvalues extends Engine_Db_Table
{
  function earning($params = array()){
    $sum = $this->select()
      ->from($this->info('name'), new Zend_Db_Expr('SUM(total)'))
      ->group('user_id')
      ->where('type =?','insert');
    if(empty($params['user_id']))
      $sum->where('user_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity());
    else
      $sum->where('user_id = ?', $params['user_id']);
    if(!empty($params['month'])) {
      $sum->where('DATE_Format(creation_date,"%Y-%m") <=?',$params['month']); 
    }
    return (int)$sum->query()
      ->fetchColumn(0)
      ;      
  } 
  function earningGroupBy($params = array()){
    $sum = $this->select()
      ->from($this->info('name'), array('totalEarn'=> new Zend_Db_Expr('SUM(CASE WHEN `type` = "insert" THEN  total END)')
                                , 'totalRedeem'=> new Zend_Db_Expr('SUM(CASE WHEN `type` = "redeem" THEN  total END)')
                                , 'totalBank'=> new Zend_Db_Expr('SUM(CASE WHEN `type` = "bank" THEN  total END)')
      ))
      ->group('user_id');
      //->where('type =?','insert');
    if(empty($params['user_id']))
      $sum->where('user_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity());
    else
      $sum->where('user_id = ?', $params['user_id']);
    if(!empty($params['month'])) {
      $sum->where('DATE_Format(creation_date,"%Y-%m") <=?',$params['month']); 
    }
    $row = $this
      ->fetchRow($sum);
      
    if($row){
      return $row->totalEarn - $row->totalRedeem - $row->totalBank;
    }
    return 0;      
  }
  function expend($params = array()){
    $sum = $this->select()
      ->from($this->info('name'), new Zend_Db_Expr('SUM(total)'))
      ->group('user_id')
      ;
   if(empty($params['user_id']))
      $sum->where('user_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity());
    else
      $sum->where('user_id = ?', $params['user_id']);
   if(!empty($params['type']))
    $sum->where('type =?',$params['type']);
   else
    $sum->where('type !=?','insert');
   if(!empty($params['month'])) 
      $sum->where('DATE_Format(creation_date,"%Y-%m") =?',$params['month']); 
   return  (int)$sum->query()
      ->fetchColumn(0)
      ;
  }
  
  function currentEb($params = array()){
    $e = $this->earning($params);
    $ex = $this->expend($params);
      return  $e - $ex;
  }
  
}