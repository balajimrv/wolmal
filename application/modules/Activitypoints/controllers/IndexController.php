<?php

class Activitypoints_IndexController extends Core_Controller_Action_Standard
{

  public function init()
  {
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
      ->addActionContext('sendpoints', 'json')
      ->addActionContext('friendsuggest', 'json')
      ->initContext();
  }


  public function indexAction()
  {

    $this->_helper->requireUser()->isValid();

	// per level
    if( !$this->_helper->requireAuth()->setAuthParams('activitypoints', null, 'use')->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();

    $api = Engine_Api::_()->getApi('core', 'activitypoints');

    $this->setupNavigation('activitypoints_vault');

	$is_error = 0;
	$result = 0;
	
    $to = $this->_getParam('to', null);

    if( $to !== null ) {
      $toUser = Engine_Api::_()->user()->getUser($to);
      if($toUser && $toUser->getIdentity() && !$viewer->isBlockedBy($toUser)) {
        $this->view->toUser = $toUser;
        $this->view->toValues = $toUser->getIdentity();//$to;
      }
    }
	
	$points_all = $api->getPoints($viewer->getIdentity());
	if($points_all) {
	  $user_points = $points_all['userpoints_count'];
	  $user_points_totalearned = $points_all['userpoints_totalearned'];
	} else {
	  $user_points = 0;
	  $user_points_totalearned = 0;
	}
	
	$user_rank = $api->getRank($viewer->getIdentity());
	
	$this->view->userpoints_enable_topusers = Semods_Utils::getSetting('activitypoints.enable_topusers');

	$this->view->user_rank = $user_rank;
	$this->view->user_points = $user_points;
	$this->view->user_points_totalearned = $user_points_totalearned;
	
	$this->view->allow_transfer = (bool)$this->_helper->requireAuth()->setAuthParams('activitypoints', null, 'allow_transfer')->checkRequire();

  }





  public function transactionsAction()
  {

    $this->_helper->requireUser()->isValid();

	// per level
    if( !$this->_helper->requireAuth()->setAuthParams('activitypoints', null, 'use')->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();

    $api = Engine_Api::_()->getApi('core', 'activitypoints');

    $this->setupNavigation('activitypoints_transactions');






    $this->view->formFilter = $formFilter = new Activitypoints_Form_TransactionsFilter();
    
    $page = $this->_getParam('page',1);

    $table = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $table->info('name');

    $rTable = Engine_Api::_()->getDbtable('transactions', 'activitypoints');
    $rName = $rTable->info('name');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($rName)
      ->join($userTableName, "`{$userTableName}`.`user_id` = `{$rName}`.`uptransaction_user_id`", '*');

    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'uptransaction_id',
      'order_direction' => 'DESC',
    ), $values);
    
    $this->view->assign($values);

    // Set up select info
    $select->order(( !empty($values['order']) ? $values['order'] : 'user_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    if( !empty($values['f_state']) && ($values['f_state'] != -1) )
    {
      $select->where('uptransaction_state = ?', $values['f_state'] );
    }
    
    if( !empty($values['f_title']) )
    {
      $select->where('uptransaction_text LIKE ?', '%' . $values['f_title'] . '%');
    }

    $select->where('uptransaction_user_id = ?', $viewer->getIdentity());
    

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );

	$success_message = '';
	$error_message = '';
	
	$success = $this->getRequest()->get('success','0');
	
	if($success == 1) {

	  $session = new Zend_Session_Namespace('Activitypoints_Flash_Message');
	  $success_message = $session->success_message;
	  $session->success_message = '';
	  
	}
	
	$this->view->success_message = $success_message;
	$this->view->error_message = $error_message;

  }





  public function helpAction()
  {

    $this->_helper->requireUser()->isValid();

	// per level
    if( !$this->_helper->requireAuth()->setAuthParams('activitypoints', null, 'use')->isValid() ) return;

    $this->setupNavigation('activitypoints_help');

    $action_group_types = Activitypoints_Api_Core::$action_group_types;

    $table_actiontypes = Engine_Api::_()->getDbTable('actionTypes','activity');
    $table_actionpoints = Engine_Api::_()->getDbTable('actionpoints','activitypoints');

    $sql =  "SELECT A.type,
                    P.action_id, P.action_type, IFNULL(P.action_name,A.type) AS action_name, P.action_points, P.action_requiredplugin, P.action_group, P.action_pointsmax, P.action_rolloverperiod, P.action_module, P.action_custom  
             FROM `{$table_actiontypes->info('name')}` A
             LEFT JOIN `{$table_actionpoints->info('name')}` P ON A.type = P.action_type
             UNION SELECT A.type, P.action_id, P.action_type, IFNULL(P.action_name,A.type) AS action_name, P.action_points, P.action_requiredplugin, P.action_group, P.action_pointsmax, P.action_rolloverperiod, P.action_module, P.action_custom  
             FROM `{$table_actiontypes->info('name')}` A
             RIGHT JOIN `{$table_actionpoints->info('name')}` P ON A.type = P.action_type
             WHERE P.action_group >= 0
             ORDER BY action_group DESC, action_id";

    $db = $table_actiontypes->getAdapter();
    
    $statement = $db->query($sql);

    $actions = array();
    $action_types = array();
    $action_group_previd = -1;
    $action_group_id = -1;
	
	$hide_actions = array('signup');
    
    while($row = $statement->fetch()) {
    
	  // skip zero value (disabled) actions
	  if(empty($row['action_points'])) {
		continue;
	  }

	  // skip uninstalled plugins
	  if(empty($row['type']) && (($row['action_custom'] == 0) || (($row['action_custom'] == 1) && !empty($row['action_module']) && !Engine_Api::_()->getDbTable('modules','core')->isModuleEnabled($row['action_module'])))) {
		continue;
	  }

	  // hide special actions
	  if(in_array($row['type'], $hide_actions)) {
		continue;
	  }
	
	
      $action_group_id = $row['action_group'];
      if($action_group_id != $action_group_previd) {
        if($action_group_previd != -1) {
          $actions[] = $action_group;
          $action_types[] = $action_group_types[intval($action_group_previd)];
          $action_group = array();
        } else {
          
        }
        $action_group_previd = $action_group_id;
      }
    
      // seconds -> days
      $row['action_rolloverperiod'] = $row['action_rolloverperiod'] / 86400; 
    
      $action_group[] = $row;
    
    }
    
    if(!empty($action_group)) {
      $actions[] = $action_group;
      $action_types[] = $action_group_types[intval($action_group_previd)];
    }
    
    $this->view->actions = $actions;
    $this->view->action_types = $action_types;

  }


  public function businessAction()
  {

    $this->_helper->requireUser()->isValid();

	// per level
    if( !$this->_helper->requireAuth()->setAuthParams('activitypoints', null, 'use')->isValid() ) return;

    $this->setupNavigation('activitypoints_help');
    
    $viewer = Engine_Api::_()->user()->getViewer();

    $bridgesTable = Engine_Api::_()->getDbtable('bridges', 'sesbasic');
    $this->view->businessPoint = $bridgesTable->select()
    			   ->from($bridgesTable->info('name'), "SUM(buyer_bb)")
    			   ->where('buyer_user_id =?',$viewer->getIdentity())
    			   ->query()->fetchColumn();

  }
  
    public function collectionAction()
  {

    $this->_helper->requireUser()->isValid();

	// per level
    if( !$this->_helper->requireAuth()->setAuthParams('activitypoints', null, 'use')->isValid() ) return;

    $this->setupNavigation('activitypoints_help');
    
    $viewer = Engine_Api::_()->user()->getViewer();

    $bridgesTable = Engine_Api::_()->getDbtable('bridges', 'sesbasic');
    $this->view->collectionPoint = $bridgesTable->select()
    			   ->from($bridgesTable->info('name'), "SUM(buyer_cb)")
    			   ->where('buyer_user_id =?',$viewer->getIdentity())
    			   ->query()->fetchColumn();

  }
  
    public function directAction()
  {

    $this->_helper->requireUser()->isValid();

	// per level
    if( !$this->_helper->requireAuth()->setAuthParams('activitypoints', null, 'use')->isValid() ) return;

    $this->setupNavigation('activitypoints_help');
    
    $viewer = Engine_Api::_()->user()->getViewer();

    $bridgesTable = Engine_Api::_()->getDbtable('bridges', 'sesbasic');
    $this->view->directPoint = $bridgesTable->select()
    			   ->from($bridgesTable->info('name'), "SUM(buyer_db)")
    			   ->where('buyer_user_id =?',$viewer->getIdentity())
    			   ->query()->fetchColumn();

  }

  public function sendpointsAction()
  {

    $this->_helper->requireUser()->isValid();

    $viewer = Engine_Api::_()->user()->getViewer();

    $api = Engine_Api::_()->getApi('core', 'activitypoints');
	
	$points_recipient_id = $this->_getParam('points_recipient_id');
	$points_amount = intval($this->_getParam('points_amount'));
	
	$result = $api->transferPoints( $viewer, $points_recipient_id, $points_amount );
  
	$response = array();
  
	if($result['is_error'] == 1) {
	  $response['status'] = 1;
	  $response['msg'] = $result['message'];
	}
	else {
	  $response['status'] = 0;
	  $response['msg'] = $result['message'];
	  $response['balancee'] = $api->getPointsBalance( $viewer->getIdentity() );
	}
	  
	$response['msg'] = Zend_Registry::get('Zend_Translate')->_( $result['message'] );
		

    foreach($response as $key => $val) {
      $this->view->$key = $val;
    }

  }







  public function suggestAction()
  {
    $data = array();
    if( $this->_helper->requireUser()->checkRequire() )
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $table = Engine_Api::_()->getItemTable('user');
      $select = Engine_Api::_()->user()->getViewer()->membership()->getMembersObjectSelect();

      if( $this->_getParam('includeSelf', false) ) {
        $data[] = array(
          'type' => 'user',
          'id' => $viewer->getIdentity(),
          'guid' => $viewer->getGuid(),
          'label' => $viewer->getTitle() . ' (you)',
          'photo' => $this->view->itemPhoto($viewer, 'thumb.icon'),
          'url' => $viewer->getHref(),
        );
      }

      if( 0 < ($limit = (int) $this->_getParam('limit', 10)) )
      {
        $select->limit($limit);
      }

      if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))))
      {
        $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
      }
      $ids = array();
      foreach( $select->getTable()->fetchAll($select) as $friend )
      {
        $data[] = array(
          'type'  => 'user',
          'id'    => $friend->getIdentity(),
          'guid'  => $friend->getGuid(),
          'label' => $friend->getTitle(),
          'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
          'url'   => $friend->getHref(),
        );
        $ids[] = $friend->getIdentity();
        $friend_data[$friend->getIdentity()] = $friend->getTitle();
      }
      
    }

    if( $this->_getParam('sendNow', true) )
    {
      return $this->_helper->json($data);
    }
    else
    {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }




  public function setupNavigation($active_menu) {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_main', array(), $active_menu);

  }
  
  public function bridgesAction() {
      
    $this->_helper->requireUser()->isValid();
    $this->setupNavigation('activitypoints_help');
    $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();

	$bridgeTable = Engine_Api::_()->getDbTable('bridges', 'sesbasic');
	$selectTable = $bridgeTable->select()
	               ->from($bridgeTable->info('name'), array("SUM(buyer_bb) as total_bb","SUM(buyer_cb) as total_cb","SUM(buyer_db) as total_db", "creation_date"))
	               ->where("DATE_FORMAT(creation_date,'%Y')=?", 2017)
	               ->where('buyer_user_id =?', $viewerId)
	               ->group("YEAR(creation_date)")
	               ->group("MONTH(creation_date)");
	$this->view->bridges = $bridgeTable->fetchAll($selectTable);
	$bArray = array();
	foreach($this->view->bridges as $bridge) {
	  $bArray[date('n',strtotime($bridge['creation_date']))]['total_bb'] = $bridge['total_bb'];  
	  $bArray[date('n',strtotime($bridge['creation_date']))]['total_cb'] = $bridge['total_cb']; 
	  $bArray[date('n',strtotime($bridge['creation_date']))]['total_db'] = $bridge['total_db']; 
	}
	$this->view->bridges = $bArray;  
  
  }


}