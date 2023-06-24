<?php
class Sescustomize_Plugin_Task_Jobs extends Core_Plugin_Task_Abstract {
    public function execute() { 
        
        if(date('d') == 25) {
        $db = Engine_Db_Table::getDefaultAdapter(); 
        $dateBack  = date('Y-m-d',strtotime('-1 Month',time()));
        $dateYM = date('Y-m',strtotime($dateBack));
        $bridgeTable = Engine_Api::_()->getDbTable('bridges', 'sesbasic');
        $table2 = $bridgeTable->info('name'); 
        $tableName = $bridgeTable->info('name');
        $date = date("Y-m-d");
        $selectTable = $bridgeTable->select()
                       ->from($tableName, array(new Zend_Db_Expr("CASE WHEN level_id = 4 then 0 
                                      WHEN level_id = 6 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  > 10000 then 10000 
                                      WHEN level_id = 6 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  <= 10000 then (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value 
                                      WHEN level_id = 7 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  > 20000 then 20000 
                                      WHEN level_id = 7 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  <= 20000 then (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value 
                                      WHEN level_id = 8 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  > 30000 then 30000 
                                      WHEN level_id = 8 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  <= 30000 then (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value                                       
                                      WHEN level_id = 9 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  > 50000 then 50000 
                                      WHEN level_id = 9 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  <= 50000 then (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value 
                                      WHEN level_id = 10 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  > 100000 then 100000 
                                      WHEN level_id = 10 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  <= 100000 then (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value                                         
                                       WHEN level_id = 11 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  > 200000 then 200000 
                                      WHEN level_id = 11 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  <= 200000 then (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value 
                                      WHEN level_id = 12 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  > 500000 then 500000 
                                      WHEN level_id = 12 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  <= 500000 then (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value 
                                      WHEN level_id = 13 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  > 2000000 then 2000000
                                      WHEN level_id = 13 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  <= 2000000 then (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value 
                                      WHEN level_id = 14 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  > 5000000 then 5000000 
                                      WHEN level_id = 14 AND (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value  <= 5000000 then (SUM($tableName.buyer_bb) + SUM($tableName.buyer_cb) + SUM($tableName.buyer_db)) * engine4_sescustomize_bbvalues.value 
                                      ELSE 0 END as eb_count                                      
                                       ") ,$tableName.".buyer_user_id",new Zend_Db_Expr("'insert'"),new Zend_Db_Expr("'".date('Y-m-d',strtotime($dateBack))."'")))
                       ->where('DATE_FORMAT('.$tableName.'.creation_date,"%Y-%m") =?', $dateYM)
                       ->setIntegrityCheck(false)
                       ->joinLeft('engine4_sescustomize_bbvalues','engine4_sescustomize_bbvalues.date = DATE_FORMAT('.$tableName.'.creation_date,"%m-%Y")',null)
                       //->join($table2,'engine4_sesbasic_bridges_2.buyer_user_id = '.$tableName.'.buyer_user_id AND DATE_FORMAT(engine4_sesbasic_bridges_2.creation_date,"%Y-%m") = "'.$dateYM.'" AND engine4_sesbasic_bridges_2.buyer_bb != 0',null)
                       ->joinLeft('engine4_users','engine4_users.user_id = '.$tableName.'.buyer_user_id',null)
                       ->joinLeft('engine4_sescustomize_ebvalues','engine4_sescustomize_ebvalues.user_id = '.$tableName.'.buyer_user_id AND engine4_sescustomize_ebvalues.creation_date = "'.date('Y-m-d',strtotime($dateBack)).'" AND engine4_sescustomize_ebvalues.type = "insert"',null)
                       ->where('engine4_sescustomize_ebvalues.ebvalue_id IS NULL')
                       ->where('engine4_users.user_id IS NOT NULL')
                       //->having("eb_count > 0 AND COUNT(engine4_sesbasic_bridges_2.bridge_id) > 0")
                       ->having("eb_count > 0")
                       ->where("(SELECT COUNT(bridge_id) FROM engine4_sesbasic_bridges as m WHERE buyer_bb != 0 AND buyer_user_id =  engine4_sesbasic_bridges.buyer_user_id AND (DATE_FORMAT(m.creation_date,'%Y-%m') ='".$dateYM."')) > 0")
                       ->group("$tableName.buyer_user_id")
                       ->group("YEAR($tableName.creation_date)")
                       ->group("MONTH($tableName.creation_date)");
        //$querySub = "UPDATE engine4_users u1 JOIN (".$selectTable.") b1 ON (u1.user_id = b1.buyer_user_id AND bb_update_date != '".$dateYM."') SET u1.eb_count = b1.eb_count,bb_update_date = '".$dateYM."'" ;  
        $querySub = "INSERT INTO `engine4_sescustomize_ebvalues`(`total`, `user_id`, `type`, `creation_date`) ".$selectTable ;
        $db->query($querySub);
        }
        
    
         $userTable = Engine_Api::_()->getItemTable('user');
        $selectUser = $userTable->select()
                      ->from($userTable->info('name'),array('*'))
                      ->where("date('Y-m-d H:i:s') >= ?","DATE_ADD(creation_date, INTERVAL 5 YEAR)")
                      ->where('level_id !=?',1)
                      ->where('level_id !=?',2)
                      ->where('process =?',0);
        $users = $userTable->fetchAll($selectUser);
        foreach($users as $user) {
            $date = strtotime("+ 7 years", strtotime($user->creation_date));
            if(!$user->extend) {
                if(strtotime($user->expiry_date) < time()) {
                $userTable->update(array('process' => 1,'enabled' => 0,'approved' => 0), array('user_id =?' => $user->user_id));
                }
            }
            else {
                $date = strtotime("+ 7 years", strtotime($user->creation_date));
                if($date < time()) {
                $userTable->update(array('process' => 1,'enabled' => 0,'approved' => 0), array('user_id =?' => $user->user_id));
                }
            }
        }
        
    }

}