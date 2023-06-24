<?php

class Activitypoints_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_admin_main', array(), 'activitypoints_admin_main_manage');

    $this->view->formFilter = $formFilter = new Activitypoints_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page',1);

    $table = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $table->info('name');

    $rTable = Engine_Api::_()->getDbtable('points', 'activitypoints');
    $rName = $rTable->info('name');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($userTableName)
      ->joinLeft($rName, "`{$userTableName}`.`user_id` = `{$rName}`.`userpoints_user_id`", '*');

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
      'order' => 'user_id',
      'order_direction' => 'DESC',
    ), $values);
    
    $this->view->assign($values);

    // Set up select info
    $select->order(( !empty($values['order']) ? $values['order'] : 'user_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    if( !empty($values['displayname']) )
    {
      $select->where('displayname LIKE ?', '%' . $values['displayname'] . '%');
    }
    
    if( !empty($values['username']) )
    {
      $select->where('username LIKE ?', '%' . $values['username'] . '%');
    }

    if( !empty($values['email']) )
    {
      $select->where('email LIKE ?', '%' . $values['email'] . '%');
    }

    if( !empty($values['level_id']) )
    {
      $select->where('level_id = ?', $values['level_id'] );
    }
    
    if( isset($values['enabled']) && $values['enabled'] != -1 )
    {
      $select->where('enabled = ?', $values['enabled'] );
    }

    $valuesCopy = array_filter($values);
    
    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );

//    $paginator->setItemCountPerPage(1);

    $this->view->superAdminCount = count(Engine_Api::_()->user()->getSuperAdmins());
    $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;
    $this->view->formValues = $valuesCopy;
  }

  public function multiModifyAction()
  {
    if ($this->getRequest()->isPost()) 
    {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key=>$value) {
        if ($key == 'modify_' . $value)
        {
          $user = Engine_Api::_()->getItem('user', (int) $value);
          if ($values['submit_button'] == 'delete')
    {
            if ($user->level_id != 1) 
            {
              $user->delete();
            }
          }
          else if ($values['submit_button'] == 'approve')
          {
            $user->enabled = 1;
            $user->save();           
    }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));

    $this->_forward('index', 'admin-manage', 'user');
  }



  public function editAction()
  {

    $id = $this->_getParam('id', null);
    $this->view->user = $user = Engine_Api::_()->user()->getUser($id);
    $this->view->form = $form = new Activitypoints_Form_Admin_Manage_Edit();
    
    if(!$user->getIdentity()) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    $this->setupNavigation('activitypoints_admin_edit_user', array('id' => $id) );
    
    $points =  Engine_Api::_()->getApi('core', 'activitypoints')->getPoints($id);
    $form->getElement('userpoints_count')->setValue($points['userpoints_count']);
    $form->getElement('userpoints_totalearned')->setValue($points['userpoints_totalearned']);
    $form->getElement('userpoints_totalspent')->setValue($points['userpoints_totalspent']);
    $form->getElement('id')->setValue($id);


    // Posting form
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $form->save();
    }

  }




  public function transactionsAction()
  {
    $id = $this->_getParam('id', null);    
    $this->view->user = $user = Engine_Api::_()->user()->getUser($id);
    
    if(!$user->getIdentity()) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }


    $task = $this->_getParam('task', '');    

    if($task == "confirm") {
      $transaction_id = intval($this->_getParam('transaction_id', 0));
    
      $uptransaction = Engine_Api::_()->getDbtable('transactions', 'activitypoints')->complete($transaction_id);
    
    }
    
    
    if($task == "cancel") {
      $transaction_id = intval($this->_getParam('transaction_id',0));
    
      $uptransaction = Engine_Api::_()->getDbtable('transactions', 'activitypoints')->cancel($transaction_id);
    }


    $this->setupNavigation('activitypoints_admin_edit_transactions', array('id' => $id) );

    $this->view->formFilter = $formFilter = new Activitypoints_Form_Admin_Manage_TransactionsFilter();
    $formFilter->getElement('id')->setValue($id);
    
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
    $select->order(( !empty($values['order']) ? $values['order'] : 'uptransaction_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    if( !empty($values['f_state']) && ($values['f_state'] != -1) )
    {
      $select->where('uptransaction_state = ?', $values['f_state'] );
    }
    
    if( !empty($values['f_title']) )
    {
      $select->where('uptransaction_text LIKE ?', '%' . $values['f_title'] . '%');
    }

    $select->where('uptransaction_user_id = ?', $values['id']);

    if( isset($values['f_type']) && ($values['f_type'] != -1) )
    {
      if(strpos($values['f_type'],'_') !== false) {
        $f_type = explode('_', $values['f_type']);
        $select->where('uptransaction_type = ?', $f_type[1] )
               ->where('uptransaction_cat = ?', $f_type[0] );
      } else {
        $select->where('uptransaction_type = ?', $values['f_type'] );
      }
    }
    
    
    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );


  }


  public function transactionsmodifyAction()
  {
    $this->id = $user_id = $id = $this->_getParam('id', null);

    if ($this->getRequest()->isPost()) {
      $transactions = $this->_getParam('transactions');

      if(is_array($transactions) && !empty($transactions)) {
        $transaction = implode(',',$transaction);
      }
      Engine_Api::_()->getDbTable('transactions','activitypoints')->delete(array("uptransaction_id IN (?)"=> $transactions));
    }
      
    return $this->_helper->redirector->gotoRoute(array('module' => 'activitypoints', 'controller' => 'manage', 'action' => 'transactions', 'id' => $this->id ), 'admin_default', true);

  }


  public function quotasAction()
  {

    $this->id = $user_id = $id = $this->_getParam('id', null);
    $this->view->user = $user = Engine_Api::_()->user()->getUser($id);
    $this->view->form = $form = new Activitypoints_Form_Admin_Manage_Edit();
    
    if(!$user->getIdentity()) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    $task = $this->_getParam('task','');
    
    if($task == 'reset') {
      $action_type = $this->_getParam('action_type','');
      Engine_Api::_()->getDbTable('counters','activitypoints')->reset($user_id, $action_type);
      
      return $this->_helper->redirector->gotoRoute(array('module' => 'activitypoints', 'controller' => 'manage', 'action' => 'quotas', 'id' => $this->id ), 'admin_default', true);
    }

    $this->setupNavigation('activitypoints_admin_edit_quotas', array('id' => $id) );



    $action_group_types = Activitypoints_Api_Core::$action_group_types;

    $table_actiontypes = Engine_Api::_()->getDbTable('actionTypes','activity');
    $table_actionpoints = Engine_Api::_()->getDbTable('actionpoints','activitypoints');

    $sql =  "SELECT A.type,
                    P.action_id, P.action_type, IFNULL(P.action_name,A.type) AS action_name, P.action_points, P.action_requiredplugin, P.action_group, P.action_pointsmax, P.action_rolloverperiod 
             FROM `{$table_actiontypes->info('name')}` A
             JOIN `{$table_actionpoints->info('name')}` P ON A.type = P.action_type
             UNION SELECT A.type, P.action_id, P.action_type, IFNULL(P.action_name,A.type) AS action_name, P.action_points, P.action_requiredplugin, P.action_group, P.action_pointsmax, P.action_rolloverperiod 
             FROM `{$table_actiontypes->info('name')}` A
             RIGHT JOIN `{$table_actionpoints->info('name')}` P ON A.type = P.action_type
             WHERE P.action_group >= 0 AND NOT( NOT ISNULL(P.action_requiredplugin) AND ISNULL(A.type) AND P.action_custom != 1 )
             ORDER BY action_group DESC, action_id";

    $db = $table_actiontypes->getAdapter();
    
    $user_quota = array();
    $user_quota_dbr = Engine_Api::_()->getDbTable('counters','activitypoints')->get($user_id);

    foreach($user_quota_dbr as $row) {
      $user_quota[$row['userpointcounters_action_id']] = $row;
    }

    $actions_statement = $db->query($sql);
    
    $actions = array();
    $action_types = array();
    $action_group_previd = -1;
    $action_group_id = -1;
    
    while($row = $actions_statement->fetch()) {
    
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
    
      // merge user quotas
      if(isset($user_quota[$row['action_id']])) {
        $row['userpointcounters_amount'] = $user_quota[$row['action_id']]['userpointcounters_amount']; 
        $row['userpointcounters_cumulative'] = $user_quota[$row['action_id']]['userpointcounters_cumulative']; 
        $row['userpointcounters_lastrollover'] = $user_quota[$row['action_id']]['userpointcounters_lastrollover']; 
      } else {
        $row['userpointcounters_amount'] = 0;
        $row['userpointcounters_cumulative'] = 0;
        $row['userpointcounters_lastrollover'] = 0;
      }
      
      $action_group[] = $row;
      
    
    }
    
    if(!empty($action_group)) {
      $actions[] = $action_group;
      $action_types[] = $action_group_types[intval($action_group_previd)];
    }
    
    
    $this->view->actions = $actions;
    $this->view->action_types = $action_types;

  }





















  protected $_periods = array(
    Zend_Date::DAY, //dd
    Zend_Date::WEEK, //ww
    Zend_Date::MONTH, //MM
    Zend_Date::YEAR, //y
  );

  protected $_allPeriods = array(
    Zend_Date::SECOND,
    Zend_Date::MINUTE,
    Zend_Date::HOUR,
    Zend_Date::DAY,
    Zend_Date::WEEK,
    Zend_Date::MONTH,
    Zend_Date::YEAR,
  );

  protected $_periodMap = array(
    Zend_Date::DAY => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR => 0,
    ),
    Zend_Date::WEEK => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR => 0,
      Zend_Date::WEEKDAY_8601 => 1,
    ),
    Zend_Date::MONTH => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR => 0,
      Zend_Date::DAY => 1,
    ),
    Zend_Date::YEAR => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR => 0,
      Zend_Date::DAY => 1,
      Zend_Date::MONTH => 1,
    ),
  );
  

  public function statsAction()
  {

    $id = $this->_getParam('id', null);
    $this->view->user = $user = Engine_Api::_()->user()->getUser($id);
    
    if(!$user->getIdentity()) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    $this->setupNavigation('activitypoints_admin_edit_stats', array('id' => $id) );
    
    $types['earned_vs_spent'] = 'Points Earned vs Points Spent';
    
    $this->view->filterForm = $filterForm = new Activitypoints_Form_Admin_Statistics_Filter();
    $filterForm->user_id->setValue($user->getIdentity());
    $filterForm->type->setMultiOptions($types);
    
  }


  public function chartDataAction()
  {
    // Disable layout and viewrenderer
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $translate = Zend_Registry::get('Zend_Translate');

    // Get params
    $user_id = $this->_getParam('user_id',0);
    $type = $this->_getParam('type');
    $start = $this->_getParam('start');
    $offset = $this->_getParam('offset', 0);
    $mode = $this->_getParam('mode');
    $chunk = $this->_getParam('chunk');
    $period = $this->_getParam('period');
    $periodCount = $this->_getParam('periodCount', 1);

    // Validate chunk/period
    if( !$chunk || !in_array($chunk, $this->_periods) ) {
      $chunk = Zend_Date::DAY;
    }
    if( !$period || !in_array($period, $this->_periods) ) {
      $period = Zend_Date::MONTH;
    }

    if( array_search($chunk, $this->_periods) >= array_search($period, $this->_periods) ) {

      $response = <<<EOC
{
  "title": {
    "text": "{$translate->translate('Please choose valid sub-period')}",
    "style": "{font-size: 14px;font-weight: bold;margin-bottom: 10px; color: #777777;}"
  },
  "elements": [
    {
      "type": "line",
      "values": [
        0
      ],
      "colour": "#5ba1cd"
    }
  ],
  "bg_colour": "#ffffff",
  "x_axis": {
    "labels": {
      "steps": 1,
      "labels": [
      ]
    },
    "colour": "#416b86",
    "grid-colour": "#dddddd",
    "steps": 1
  },
  "y_axis": {
    "min": 0,
    "max": 1,
    "steps": 1,
    "colour": "#416b86",
    "grid-colour": "#dddddd"
  }
}
EOC;
      $this->getResponse()->setBody( $response );
      return;
    }

    // Validate start
    if( $start && !is_numeric($start) ) {
      $start = strtotime($start);
    }
    if( !$start ) {
      $start = time();
    }
    
    // Make start fit to period?
    $startObject = new Zend_Date($start);
    
    $partMaps = $this->_periodMap[$period];
    foreach( $partMaps as $partType => $partValue ) {
      $startObject->set($partValue, $partType);
    }

    // Do offset
    if( $offset != 0 ) {
      $startObject->add($offset, $period);
    }
    
    // Get end time
    $endObject = new Zend_Date($startObject->getTimestamp());
    $endObject->add($periodCount, $period);

    $multiline = false;
    
    switch($type) {

      case 'earned_vs_spent':

        $multiline = true;
        $title_var = '100016067';
        $key1 = '100016079';
        $key2 = '100016080';

        
        // Get data
        $statsTable = Engine_Api::_()->getDbtable('stats', 'activitypoints');
        $statsSelect = $statsTable->select()
          ->from($statsTable->info('name'), array("userpointstat_date as date", "userpointstat_earn as value"))
          ->where('userpointstat_user_id = ?', $user_id)
          ->where('userpointstat_date >= ?', $startObject->getTimestamp())
          ->where('userpointstat_date < ?', $endObject->getTimestamp())
          ->order('userpointstat_date ASC')
          ;

        $rawData = $statsTable->fetchAll($statsSelect);

        $statsTable = Engine_Api::_()->getDbtable('stats', 'activitypoints');
        $statsSelect = $statsTable->select()
          ->from($statsTable->info('name'), array("userpointstat_date as date", "userpointstat_spend as value"))
          ->where('userpointstat_user_id = ?', $user_id)
          ->where('userpointstat_date >= ?', $startObject->getTimestamp())
          ->where('userpointstat_date < ?', $endObject->getTimestamp())
          ->order('userpointstat_date ASC')
          ;

        $rawData2 = $statsTable->fetchAll($statsSelect);
        
        break;
        
    
    }
    
    // Now create data structure
    $currentObject = clone $startObject;
    $nextObject = clone $startObject;
    $data = array();
    $dataLabels = array();
    $cumulative = 0;
    $previous = 0;

    $data2 = array();
    $cumulative2 = 0;

    do {
      $nextObject->add(1, $chunk);
      
      $currentObjectTimestamp = $currentObject->getTimestamp();
      $nextObjectTimestamp = $nextObject->getTimestamp();

      $data[$currentObjectTimestamp] = $cumulative;
      
      if($multiline) {
        $data2[$currentObjectTimestamp] = $cumulative2;
      }

      // Get everything that matches
      $currentPeriodCount = 0;
      foreach( $rawData as $rawDatum ) {
        $rawDatumDate = $rawDatum->date;
        if( $rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp ) {
          $currentPeriodCount += $rawDatum->value;
        }
      }

      if($multiline) {
        $currentPeriodCount2 = 0;
        foreach( $rawData2 as $rawDatum ) {
          $rawDatumDate = $rawDatum->date;
          if( $rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp ) {
            $currentPeriodCount2 += $rawDatum->value;
          }
        }
      }

      // Now do stuff with it
      switch( $mode ) {
        default:
        case 'normal':
          $data[$currentObjectTimestamp] = $currentPeriodCount;
          if($multiline) {
            $data2[$currentObjectTimestamp] = $currentPeriodCount2;
          }
          break;
        case 'cumulative':
          $cumulative += $currentPeriodCount;
          $data[$currentObjectTimestamp] = $cumulative;
          break;
        case 'delta':
          $data[$currentObjectTimestamp] = $currentPeriodCount - $previous;
          $previous = $currentPeriodCount;
          break;
      }
      
      $currentObject->add(1, $chunk);
    } while( $currentObject->getTimestamp() < $endObject->getTimestamp() );

    // Reprocess label
    $labelStrings = array();
    $labelDate = new Zend_Date();
    foreach( $data as $key => $value ) {
      $labelDate->set($key);
      $labelStrings[] = $this->view->locale()->toDate($labelDate, array('size' => 'short')); //date('D M d Y', $key);
    }

    // Let's expand them by 1.1 just for some nice spacing
    $minVal = min($data);
    $maxVal = max($data);
    $minVal = floor($minVal * ($minVal < 0 ? 1.1 : (1 / 1.1)) / 10) * 10;
    $maxVal = ceil($maxVal * ($maxVal > 0 ? 1.1 : (1 / 1.1)) / 10) * 10;

    // Remove some labels if there are too many
    $xlabelsteps = 1;
    if( count($data) > 10 ) {
      $xlabelsteps = ceil(count($data) / 10);
    }

    // Remove some grid lines if there are too many
    $xsteps = 1;
    if( count($data) > 100 ) {
      $xsteps = ceil(count($data) / 100);
    }

    // Create base chart
    require_once 'OFC/OFC_Chart.php';

    // Make x axis labels
    $x_axis_labels = new OFC_Elements_Axis_X_Label_Set();
    $x_axis_labels->set_steps( $xlabelsteps );
    $x_axis_labels->set_labels( $labelStrings );

    // Make x axis
    $labels = new OFC_Elements_Axis_X();
    $labels->set_labels( $x_axis_labels );
    $labels->set_colour("#416b86");
    $labels->set_grid_colour("#dddddd");
    $labels->set_steps($xsteps);

    // Make y axis
    $yaxis = new OFC_Elements_Axis_Y();
    $yaxis->set_range($minVal, $maxVal/*, $steps*/);
    $yaxis->set_colour("#416b86");
    $yaxis->set_grid_colour("#dddddd");
    
    // Make data
    $graph = new OFC_Charts_Line();
    $graph->set_values( array_values($data) );
    $graph->set_colour("#5ba1cd");
    $graph->set_key($translate->translate($key1), "12");
    
    if($multiline) {

      $graph2 = new OFC_Charts_Line();
      $graph2->set_values( array_values($data2) );
      $graph2->set_colour("#C89341");
      $graph2->set_key($translate->translate($key2), "12");
      
    }

    // Make title
    $titleStr = $translate->_($title_var);
    $title = new OFC_Elements_Title( $titleStr . ': '. $startObject->toString() . ' to ' . $endObject->toString() );
    $title->set_style( "{font-size: 14px;font-weight: bold;margin-bottom: 10px; color: #777777;}" );

    // Make full chart
    $chart = new OFC_Chart();
    $chart->set_bg_colour('#ffffff');

    $chart->set_x_axis($labels);
    $chart->add_y_axis($yaxis);
    $chart->add_element($graph);
    if($multiline) {
      $chart->add_element($graph2);
    }
    $chart->set_title( $title );
    
    // Send
    $this->getResponse()->setBody( $chart->toPrettyString() );
  }


  
  
 
 
 
 
 
 
 
   public function getNavigation($activeItem, $params = array()) {

    $links = array(
                array(
                      'label'      => 'Edit Member',
                      'route'      => 'admin_default',
                      'action'     => 'edit',
                      'controller' => 'manage',
                      'module'     => 'activitypoints',
                      'active'    => $activeItem == 'activitypoints_admin_edit_user' ? true : false,
                      'params'    => $params,
                    ),
                array(
                      'label'      => 'Points Activity Statistics',
                      'route'      => 'admin_default',
                      'action'     => 'stats',
                      'controller' => 'manage',
                      'module'     => 'activitypoints',
                      'active'    => $activeItem == 'activitypoints_admin_edit_stats' ? true : false,
                      'params'    => $params,
                    ),
                array(
                      'label'      => 'Points Transactions',
                      'route'      => 'admin_default',
                      'action'     => 'transactions',
                      'controller' => 'manage',
                      'module'     => 'activitypoints',
                      'active'    => $activeItem == 'activitypoints_admin_edit_transactions' ? true : false,
                      'params'    => $params,
                    ),
                array(
                      'label'      => 'Points Quotas & Usage',
                      'route'      => 'admin_default',
                      'action'     => 'quotas',
                      'controller' => 'manage',
                      'module'     => 'activitypoints',
                      'active'    => $activeItem == 'activitypoints_admin_edit_quotas' ? true : false,
                      'params'    => $params,
                    ),
                );

    return $links;

  }


  public function setupNavigation($activeItem, $params = array()) {

    $links = $this->getNavigation($activeItem, $params);

    $this->view->navigation = new Zend_Navigation();
    $this->view->navigation->addPages($links);


  }
  
  

  
}