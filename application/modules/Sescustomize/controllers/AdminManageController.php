<?php

class Sescustomize_AdminManageController extends Core_Controller_Action_Admin {
  public function fbvalueAction(){
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescustomize_admin_main', array(), 'sescustomize_admin_main_ebbb');
      
      $table = Engine_Api::_()->getDbTable('fbvalues','sescustomize');
      $selectFb = $table->select()->from($table->info('name'),array('fbcount'=>new Zend_db_Expr('SUM(total)')))->where('type =?','insert');
      $result = $table->fetchRow($selectFb);
      $this->view->fbCount = $result->fbcount;
      
      $selectFb1 = $table->select()->from($table->info('name'),array('redeemcount'=>new Zend_db_Expr('SUM(total)')))
		  ->where('type =?','redeem');
      $result1 = $table->fetchRow($selectFb1);
      $this->view->redeemCount = $result1->redeemcount;
      
      
      $this->view->year = $year  = date('Y');
	  $bridgeTable = Engine_Api::_()->getDbTable('bridges', 'sesbasic');
	  $selectTable = $bridgeTable->select()
    	               ->from($bridgeTable->info('name'), array("SUM(buyer_bb) as total_full_bb","SUM(buyer_cb) as total_full_cb","SUM(buyer_db) as total_full_db", "creation_date"))
    	               ->where("DATE_FORMAT(creation_date,'%Y') >= ?", 2017) 
    	               ->where("DATE_FORMAT(creation_date,'%Y') <= ?", ($year)) 
    	               ->group("YEAR(creation_date)")
    	               ->group("MONTH(creation_date)");
    	$this->view->full_bridges = $bridgeTable->fetchAll($selectTable);
    	
    	$bArray = array();
    	foreach($this->view->full_bridges as $bridge) {
	        $bArray[] = array("total_full_bb"=>$bridge['total_full_bb'],"total_full_cb"=>$bridge['total_full_cb'],"total_full_db"=>$bridge['total_full_db'],"creation_date"=>$bridge['creation_date']);
    	}
    	$this->view->full_bridges = $bArray;
      
      $table = Engine_Api::_()->getDbTable('bridges','sesbasic');
      $selectBB = $table->select()->from($table->info('name'),array('bbcount'=>new Zend_db_Expr('SUM(buyer_bb)')));
      $result = $table->fetchRow($selectBB);
      $this->view->Cobbunt = $result->bbcount;
      
  }
  public function indexAction() {
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescustomize_admin_main', array(), 'sescustomize_admin_main_manage');
    
    $endDate = strtotime("- 5 year");
    $endDate = strtotime("+ 2 months",$endDate);
    
    $this->view->formFilter = $formFilter = new Sescustomize_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page', 1);

    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select()
              ->where('DATE_FORMAT(creation_date,"%Y-%m-%d") <= ?', date("Y-m-d",$endDate));
    // Process form
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    
    if(isset($_GET['action']))
    $formFilter->getElement('action')->setValue($_GET['action']);

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }
    $values = array_merge(array(
        'order' => 'user_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    // Set up select info
    $select->order((!empty($values['order']) ? $values['order'] : 'user_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    if (!empty($values['displayname'])) {
      $select->where('displayname LIKE ?', '%' . $values['displayname'] . '%');
    }
    if (!empty($values['action_status'])) {
      $select->where('extend = ?', $values['action_status']);
    }
    if (!empty($values['username'])) {
      $select->where('username LIKE ?', '%' . $values['username'] . '%');
    }
    if (!empty($values['email'])) {
      $select->where('email LIKE ?', '%' . $values['email'] . '%');
    }
    
    if (!empty($values['level_id'])) {
      $select->where('level_id = ?', $values['level_id']);
    }

    if (!empty($values['user_id'])) {
      $select->where('user_id = ?', (int) $values['user_id']);
    }

    // Filter out junk
    $valuesCopy = array_filter($values);

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
    $this->view->formValues = $valuesCopy;

    $this->view->superAdminCount = count(Engine_Api::_()->user()->getSuperAdmins());
    $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;
    $this->view->openUser = (bool) ( $this->_getParam('open') && $paginator->getTotalItemCount() == 1 );
  }
  
  public function handshakeAction() {
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescustomize_admin_main', array(), 'sescustomize_admin_main_handshake');
    
    $this->view->formFilter = $formFilter = new Sescustomize_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page', 1);

    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select()->where('level_id != ?',1)->where('level_id != ?',2);
    // Process form
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    
    if(isset($_GET['action']))
    $formFilter->getElement('action')->setValue($_GET['action']);

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }
    $values = array_merge(array(
        'order' => 'user_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    // Set up select info
    $select->order((!empty($values['order']) ? $values['order'] : 'user_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    if (!empty($values['displayname'])) {
      $select->where('displayname LIKE ?', '%' . $values['displayname'] . '%');
    }
    if (!empty($values['username'])) {
      $select->where('username LIKE ?', '%' . $values['username'] . '%');
    }
    if (!empty($values['email'])) {
      $select->where('email LIKE ?', '%' . $values['email'] . '%');
    }
    
    if (!empty($values['level_id'])) {
      $select->where('level_id = ?', $values['level_id']);
    }

    if (!empty($values['user_id'])) {
      $select->where('user_id = ?', (int) $values['user_id']);
    }

    // Filter out junk
    $valuesCopy = array_filter($values);

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
    $this->view->formValues = $valuesCopy;
    
    $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;
    $this->view->openUser = (bool) ( $this->_getParam('open') && $paginator->getTotalItemCount() == 1 );
  }
  
  public function statsAction() {
      
    $id = $this->_getParam('id', null);
    $this->view->user = $user = Engine_Api::_()->getItem('user', $id);

    // posts
    $table = Engine_Api::_()->getDbTable('actions', 'activity');
    $this->view->post_count = $table->select()
      ->from($table, array(
        'COUNT(*) AS count',
      ))
      ->where('subject_id =?',$id)
      ->query()
      ->fetchColumn();
      
    $this->view->share_count = $table->select()
      ->from($table, array(
        'COUNT(*) AS count',
      ))
      ->where('type =?','share')
      ->where('subject_id =?',$id)
      ->query()
      ->fetchColumn();
      
    $like_count = 0;
    $table = Engine_Api::_()->getDbTable('likes', 'activity');
    $like_count += (int) $table->select()
      ->from($table, array(
        'COUNT(*) AS count',
      ))
      ->where('poster_id =?',$id)
      ->query()
      ->fetchColumn();

    $table = Engine_Api::_()->getDbTable('likes', 'core');
    $like_count += (int) $table->select()
      ->from($table, array(
        'COUNT(*) AS count',
      ))
      ->where('poster_id =?',$id)
      ->query()
      ->fetchColumn();

    $this->view->like_count = $like_count;
    // comments
    $comment_count = 0;
    
    $table = Engine_Api::_()->getDbTable('comments', 'activity');
    $comment_count += (int) $table->select()
      ->from($table, array(
        'COUNT(*) AS count',
      ))
      ->where('poster_id =?',$id)
      ->query()
      ->fetchColumn();

    $table = Engine_Api::_()->getDbTable('comments', 'core');
    $comment_count += (int) $table->select()
      ->from($table, array(
        'COUNT(*) AS count',
      ))
      ->where('poster_id =?',$id)
      ->query()
      ->fetchColumn();

    $this->view->comment_count = $comment_count;

    $table = Engine_Api::_()->getDbTable('photos', 'album');
    $this->view->photo_count = $table->select()
      ->from($table, array(
        'COUNT(*) AS count',
      ))
      ->where('owner_id =?',$id)
      ->query()
      ->fetchColumn();
      
    $table = Engine_Api::_()->getDbTable('albums', 'album');
    $this->view->album_count = $table->select()
      ->from($table, array(
        'COUNT(*) AS count',
      ))
      ->where('owner_id =?',$id)
      ->query()
      ->fetchColumn();
    $table = Engine_Api::_()->getDbTable('videos', 'video');
    $this->view->video_count = $table->select()
      ->from($table, array(
        'COUNT(*) AS count',
      ))
      ->where('owner_id =?',$id)
      ->query()
      ->fetchColumn();
    $table = Engine_Api::_()->getDbTable('orders', 'sitestoreproduct');
    $this->view->product_purchased = $table->select()
      ->from($table, array(
        'COUNT(*) AS count',
      ))
      ->where('buyer_id =?',$id)
      ->where('payment_status =?','active')
      ->query()
      ->fetchColumn();

  }
  
  public function extendUserAction() {
    $id = $this->_getParam('id', null);
    $this->view->user = $user = Engine_Api::_()->getItem('user', $id);
    $this->view->form = $form = new Sescustomize_Form_Admin_Manage_Extend();
    $date = strtotime("+ 5 years", strtotime($user->creation_date));
    if ($this->getRequest()->isPost()) {
      $db = Engine_Api::_()->getDbtable('users', 'user')->getAdapter();
      $db->beginTransaction();
      try {
        $user->extend = 0;
        $user->expiry_date = date("Y-m-d", $date);
        $user->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format' => 'smoothbox',
                  'messages' => array('This member will deactive automatically after 5 Years Completion of his/her account.')
      ));
    }
  }
  
  public function invitationAction() {
    $id = $this->_getParam('id', null);
    $user = Engine_Api::_()->getItem('user', $id);
    $this->view->form = $form = new Sescustomize_Form_Admin_Manage_Invitation();
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $inviteTable = Engine_Api::_()->getDbTable('invites','inviter');
    $isExist = $inviteTable->select()
                ->from($inviteTable->info('name'), 'new_user_id')
                ->where('new_user_id =?', $_POST['member_id'])
                ->query()
                ->fetchColumn();

	$newUser = Engine_Api::_()->getItem('user', $_POST['member_id']);
	$db = Engine_Db_Table::getDefaultAdapter();
	
	$result = $db->query('SELECT invite_id,user_id FROM `engine4_inviter_invites` WHERE new_user_id = "'.$_POST['member_id'].'"')->fetch();
	$userRes = $db->query('SELECT displayname FROM `engine4_users` WHERE user_id = "'.$result['user_id'].'"')->fetch();
	
    if($isExist && $_POST['user_exist'] !="yes") {
		echo "<script>confirm('This member id is already associated with (".$result['user_id']."-".$userRes['displayname'].")... Do you want to Add?');</script>";
       // return $form->getElement('member_id')->addError($isExist->new_user_id."This member id is already associated with someone..");
	   return $form->addElement('Hidden', 'user_exist', array(
      'value' => "yes",
    ));
    }else if($isExist && $_POST['user_exist'] =="yes") {
		//Update Existing User as 0
    	//$result = $db->query('SELECT invite_id FROM `engine4_inviter_invites` WHERE new_user_id = "'.$_POST['member_id'].'"')->fetch();
		
		$update = $db->query('UPDATE `engine4_inviter_invites` SET `new_user_id` = 0, `previous_user_id` = "'.$_POST['member_id'].'", `previous_user_update_date` = "'.date('Y-m-d H:i:s').'" WHERE `invite_id` = '.$result['invite_id']);		
		
		$result = $db->query('INSERT INTO `engine4_inviter_invites` (
		`invite_id`, 
		`user_id`, 
		`sender`, 
		`recipient`, 
		`code`, 
		`sent_date`, 
		`message`, 
		`new_user_id`, 
		`previous_user_id`, 
		`previous_user_update_date`, 
		`provider`, 
		`recipient_name`, 
		`referred_date`) VALUES 
		(NULL, "'.$user->user_id.'",
		"'.$user->email.'",
		"'.$newUser->email.'",
		"",
		"'.date('Y-m-d H:i:s').'",
		"Admin added manually!",
		"'.$_POST['member_id'].'",
		"0",
		"0000-00-00 00:00:00.000000",
		"",
		"'.$newUser->displayname.'",
		"'.date('Y-m-d H:i:s').'")');
		
		return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format' => 'smoothbox',
                  'messages' => array('This member has been invited successfully.')
      ));
	}else{
	
      //$newUser = Engine_Api::_()->getItem('user', $_POST['member_id']);
      $db = $inviteTable->getAdapter();
      $db->beginTransaction();
      try {
        $inviter = $inviteTable->createRow();
        $inviter->user_id = $user->user_id;
        $inviter->sender = $user->email;
        $inviter->recipient = $newUser->email;
        $inviter->sent_date = date('Y-m-d H:i:s');
        $inviter->message = 'You are being invited to join our social network.';
        $inviter->new_user_id = $_POST['member_id'];
        $inviter->recipient_name = $newUser->displayname;
        $inviter->referred_date = date('Y-m-d H:i:s');
        $inviter->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format' => 'smoothbox',
                  'messages' => array('This member has been invited successfully.')
      ));
	}
  }

  public function manualbbAction() {
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sescustomize_admin_main', array(), 'sescustomize_admin_main_manualbb');
    
    $this->view->formFilter = $formFilter = new Sescustomize_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page', 1);

    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select()->where('level_id != ?',1)->where('level_id != ?',2);
    // Process form
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    
    if(isset($_GET['action']))
    $formFilter->getElement('action')->setValue($_GET['action']);

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }
    $values = array_merge(array(
        'order' => 'user_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    // Set up select info
    $select->order((!empty($values['order']) ? $values['order'] : 'user_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    if (!empty($values['displayname'])) {
      $select->where('displayname LIKE ?', '%' . $values['displayname'] . '%');
    }
    if (!empty($values['username'])) {
      $select->where('username LIKE ?', '%' . $values['username'] . '%');
    }
    if (!empty($values['email'])) {
      $select->where('email LIKE ?', '%' . $values['email'] . '%');
    }
    
    if (!empty($values['level_id'])) {
      $select->where('level_id = ?', $values['level_id']);
    }

    if (!empty($values['user_id'])) {
      $select->where('user_id = ?', (int) $values['user_id']);
    }

    // Filter out junk
    $valuesCopy = array_filter($values);

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
    $this->view->formValues = $valuesCopy;
    
    $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;
    $this->view->openUser = (bool) ( $this->_getParam('open') && $paginator->getTotalItemCount() == 1 );
  }
  
  public function addbbAction() {
    $id = $this->_getParam('id', null);
    $user = Engine_Api::_()->getItem('user', $id);
    $this->view->form = $form = new Sescustomize_Form_Admin_Manage_Addbb();
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
	
    $bridgesTable = Engine_Api::_()->getDbTable('bridges', 'sesbasic');
	$db = Zend_Db_Table_Abstract::getDefaultAdapter();
	$total_BB = $_POST['buyer_bb'];
	$order_id = 0;
    $bridgesTable->insert(array(
					'buyer_user_id' => $user->user_id,
					'buyer_bb' => $total_BB,
					'order_id' =>$order_id,
					'type' => 'business',
					'creation_date' => date('Y-m-d H:i:s'),
				  ));
				  $parentId = $db->lastInsertId();
				  
	$viewer_id = $user->user_id;
	$level_id = $user->level_id;	  
		
		if($parentId !=""){
                $inviterTable = Engine_Api::_()->getDbtable('invites', 'inviter');
                $firstParentId = $inviterTable->select()->from($inviterTable->info('name'), 'user_id')->where('new_user_id = ?', $viewer_id)->query()->fetchColumn();
                if($firstParentId) {
                     $objUserIdParent = Engine_Api::_()->getItem('user',$firstParentId);
                     if($objUserIdParent && $objUserIdParent->level_id != 4){
                        $total_CB = ($total_BB / 100) * 50; // 50% of BB Point is equal to CB
                        $bridgesTable->insert(array(
                        'buyer_user_id' => $firstParentId,
                        'user_id' => $viewer_id,
                        'buyer_cb' => $total_CB,
                        'order_id' =>$order_id,
                        'parent_id' => $parentId,
                        'type' => 'collection',
                        'creation_date' => date('Y-m-d H:i:s'),
                      ));
                     }
                    //if($level_id == 10 || $level_id == 11 || $level_id == 12 || $level_id == 13 || $level_id == 14){
                      $parentId = $db->lastInsertId();
                      $secondParentId = $inviterTable->select()->from($inviterTable->info('name'), 'user_id')->where('new_user_id = ?', $firstParentId)->query()->fetchColumn();
                    if($secondParentId && !empty($total_CB)) {
                        $objUserIdParent = Engine_Api::_()->getItem('user',$secondParentId);
                        if($objUserIdParent && $objUserIdParent->level_id == 10 || $objUserIdParent->level_id == 11 || $objUserIdParent->level_id == 12 || $objUserIdParent->level_id == 13 || $objUserIdParent->level_id == 14){
                          $total_DB = ($total_CB / 100) * 10; // 10% of CB Point is equal to DB
                          $bridgesTable->insert(array(
                            'buyer_user_id' => $secondParentId,
                            'user_id' => $firstParentId,
                            'order_id' =>$order_id,
                            'buyer_db' => $total_DB,
                            'parent_id' => $parentId,
                            'type' => 'direct',
                            'creation_date' => date('Y-m-d H:i:s'),
                          ));
                        }
                    }
                    //}
                }  
               } 
		
	
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format' => 'smoothbox',
                  'messages' => array('Bridges has been added successfully!.')
      ));
        
  }
  
}
