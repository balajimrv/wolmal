<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesadvancedactivity_Widget_FeedController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() ) {
      // Get subject
      $subject = Engine_Api::_()->core()->getSubject();
      if( !$subject->authorization()->isAllowed($viewer, 'view') )
        return $this->setNoRender();
    }
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $requestParams = $request->getParams();
    
    $this->view->isMemberHomePage = isset($_REQUEST['isMemberHomePage']) ? $_REQUEST['isMemberHomePage'] : $requestParams['action'] == 'home' && $requestParams['module'] == 'user' && $requestParams['controller'] == 'index';
    $this->view->isOnThisDayPage = isset($_REQUEST['isOnThisDayPage']) ? $_REQUEST['isOnThisDayPage'] :  $requestParams['action'] == 'onthisday' && $requestParams['module'] == 'sesadvancedactivity' && $requestParams['controller'] == 'index';
    if($this->view->isOnThisDayPage)
      $subject = Engine_Api::_()->user()->getViewer();
    $this->view->advcomment = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedcomment');
    $actionTable = Engine_Api::_()->getDbtable('actions', 'sesadvancedactivity');
    $this->view->usersettings =  Engine_Api::_()->getApi('settings', 'core')->getSetting($viewer->getIdentity().'.activity.user.setting','everyone');
    // Get some options
    $this->view->design = $this->_getParam('design',2);
    $this->view->userphotoalign = $this->_getParam('userphotoalign','left');
    
    //tab icon and text setting
    $this->view->welcometabtext = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.welcometabtext','Welcome');
    $this->view->welcomeicon = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.welcomeicon','icon');
    $this->view->whatsnewtext = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.whatsnewtext','What\'s New');
    $this->view->whatsnewicon = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.whatsnewicon','icon');
    
    $this->view->instagramtext = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.instagramtext','Instagram');
    $this->view->instagramicon = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.instagramicon','icon');
    
    //Image widget and height
    $this->view->enablewidthsetting = $this->_getParam('enablewidthsetting', 0);
    $this->view->sesact_image1_width = $this->_getParam("sesact_image1_width", 500);
    $this->view->sesact_image1_height = $this->_getParam("sesact_image1_height", 450);
    
    $this->view->sesact_image2_width = $this->_getParam("sesact_image2_width", 289);
    $this->view->sesact_image2_height = $this->_getParam("sesact_image2_height", 200);
    
    $this->view->sesact_image3_bigwidth = $this->_getParam("sesact_image3_bigwidth", 328);
    $this->view->sesact_image3_bigheight = $this->_getParam("sesact_image3_bigheight", 300);
    $this->view->sesact_image3_smallwidth = $this->_getParam("sesact_image3_smallwidth", 250);
    $this->view->sesact_image3_smallheight = $this->_getParam("sesact_image3_smallheight", 150);
    
    $this->view->sesact_image4_bigwidth = $this->_getParam("sesact_image4_bigwidth", 578);
    $this->view->sesact_image4_bigheight = $this->_getParam("sesact_image4_bigheight", 300);
    $this->view->sesact_image4_smallwidth = $this->_getParam("sesact_image4_smallwidth", 192);
    $this->view->sesact_image4_smallheight = $this->_getParam("sesact_image4_smallheight", 100);

    $this->view->sesact_image5_bigwidth = $this->_getParam("sesact_image5_bigwidth", 289);
    $this->view->sesact_image5_bigheight = $this->_getParam("sesact_image5_bigheight", 260);
    $this->view->sesact_image5_smallwidth = $this->_getParam("sesact_image5_smallwidth", 289);
    $this->view->sesact_image5_smallheight = $this->_getParam("sesact_image5_smallheight", 130);
    
    $this->view->sesact_image6_width = $this->_getParam("sesact_image6_width", 289);
    $this->view->sesact_image6_height = $this->_getParam("sesact_image6_height", 150);
    
    $this->view->sesact_image7_bigwidth = $this->_getParam("sesact_image7_bigwidth", 192);
    $this->view->sesact_image7_bigheight = $this->_getParam("sesact_image7_bigheight", 150);
    $this->view->sesact_image7_smallwidth = $this->_getParam("sesact_image7_smallwidth", 144);
    $this->view->sesact_image7_smallheight = $this->_getParam("sesact_image7_smallheight", 150);

    $this->view->sesact_image8_width = $this->_getParam("sesact_image8_width", 144);
    $this->view->sesact_image8_height = $this->_getParam("sesact_image8_height", 150);
    
    $this->view->sesact_image9_width = $this->_getParam("sesact_image9_width", 192);
    $this->view->sesact_image9_height = $this->_getParam("sesact_image9_height", 150);
    
    
    $this->view->allowprivacysetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.allowprivacysetting',1);
    if(! $this->view->allowprivacysetting)
       $this->view->usersettings =  'everyone';
    $this->view->contentCount = $this->_getParam('contentCount',0);
    $this->view->autoloadfeed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.autoloadfeed',1);
    $this->view->submitWithAjax = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.submitWithAjax', 1);
    $this->view->filterFeed  = $filterFeed = $this->_getParam('filterFeed','all');
    $this->view->scrollfeed = $this->_getParam('scrollfeed', 1);
    $this->view->autoloadTimes    = $this->_getParam('autoloadTimes', 3);
    $this->view->enableStatusBoxHighlight = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.highlightstatusbox', 0);
    if(!$this->view->autoloadTimes)
      $this->view->autoloadTimes = 100000000;
    $this->view->feedOnly         = $feedOnly = $request->getParam('feedOnly', false);
    $this->view->length           = $length = $request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
    $this->view->itemActionLimit  = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);
    $this->view->updateSettings   = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
    $this->view->viewAllLikes     = $request->getParam('viewAllLikes',    $request->getParam('show_likes',    false));
    $this->view->viewAllComments  = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
    $this->view->getUpdate        = $request->getParam('getUpdate');
    $this->view->checkUpdate      = $request->getParam('checkUpdate');
    $this->view->action_id        = (int) $request->getParam('action_id');
    $this->view->post_failed      = (int) $request->getParam('pf');
        
    if( $feedOnly ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }else{
      $this->view->allownetworkprivacy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.allownetworkprivacy',1);
      $allownetworkprivacytype = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.allownetworkprivacytype',0);
      $this->view->allowlistprivacy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.allowlistprivacy',1);
      if($allownetworkprivacytype == 1){
        $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($viewer)->order('engine4_network_networks.title ASC');
      }else{
        $select = Engine_Api::_()->getDbtable('networks', 'network')->select()->order('engine4_network_networks.title ASC');
      }
      $this->view->usernetworks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);
      $this->view->userlists = Engine_Api::_()->getDbtable('lists', 'user')->fetchAll(Engine_Api::_()->getDbtable('lists', 'user')->select()->order('engine4_user_lists.title ASC')->where('owner_id =?',$viewer->getIdentity()));
      $this->view->networkbasedfilter = false;
      //network based filtering
      $networkbasedfiltering = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.networkbasedfiltering',1);
     if($networkbasedfiltering != 2){
      if($networkbasedfiltering == 1){
        $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($viewer)->order('engine4_network_networks.title ASC');
      }else{
        $select = Engine_Api::_()->getDbtable('networks', 'network')->select()->order('engine4_network_networks.title ASC');
      }
      $this->view->networkbasedfilter = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);
     }       
     // Assign the composing values
      $composePartials = array();
      $this->view->composerOptions = $composerOptions = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesadvancedactivity.composeroptions',array());
       $notArray = array();
       if(!in_array('buysell',$composerOptions)){
        $notArray[] = 'post_self_buysell';
       }
       if(!in_array('fileupload',$composerOptions)){
        $notArray[] = 'post_self_file';
       }
      $networkbasedfilter = $this->view->networkbasedfilter;
      $activeLists = Engine_Api::_()->getDbTable('filterlists','sesadvancedactivity')->getLists($notArray);
        $lists = $activeLists->toArray();
        //check module enable
        $listsArray = array();
        foreach($lists as $list){
           if($list['filtertype'] != 'all' && $list['filtertype'] != 'scheduled_post' && $list['filtertype'] != 'my_networks' && $list['filtertype'] != 'my_friends' && $list['filtertype'] != 'posts' && $list['filtertype'] != 'saved_feeds' && $list['filtertype'] != 'post_self_buysell'  && $list['filtertype'] != 'post_self_file' && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($list['filtertype']))
            continue;
           $listsArray[] = $list;
        }
      if($networkbasedfilter){
        
        $networkbasedfilter = $networkbasedfilter->toArray();
        foreach($networkbasedfilter as $networkbased){
         $listsArray[] = $networkbased;
        }
       }
        $this->view->filterFeed  = $filterFeed = $listsArray[0]['filtertype'];
       $this->view->lists = $listsArray;
    }
      if( $length > 50 ) {
        $this->view->length = $length = 50;
      }
     // Get all activity feed types for custom view?
     $actionTypesTable = Engine_Api::_()->getDbtable('actionTypes', 'sesadvancedactivity');
     $this->view->groupedActionTypes = $groupedActionTypes = $actionTypesTable->getEnabledGroupedActionTypes();
     $actionTypeGroup = $filterFeed; 
     $actionTypeFilters = array();
     
    //SES advanced member plugin followig work
    $isSesmember = $actionTypeGroup == 'sesmember' && Engine_Api::_()->sesbasic()->isModuleEnable('sesmember') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmember.follow.active', 1);
    if(!$isSesmember){
      if( $actionTypeGroup && isset($groupedActionTypes[$actionTypeGroup]) ) {
        $actionTypeFilters = $groupedActionTypes[$actionTypeGroup]; 
        if($actionTypeGroup == 'sesalbum' || $actionTypeGroup == 'album')
          $actionTypeFilters = array_merge($actionTypeFilters,$groupedActionTypes['photo']);
        else if($actionTypeGroup == 'sesvideo')
          $actionTypeFilters = array_merge($actionTypeFilters,$groupedActionTypes['video']);
      }
    }
     
      if($actionTypeGroup == 'post_self_buysell')
        $actionTypeFilters = array('post_self_buysell');
      else if($actionTypeGroup == 'post_self_file')
        $actionTypeFilters = array('post_self_file');  
      //else if(strpos($actionTypeGroup , 'network_filter_' ) !== false)
       // $actionTypeFilters = array('network');
  
    // Get config options for activity
    $hashTag = isset($_GET['hashtag']) ? $_GET['hashtag'] : '';
    $config = array(
      'action_id' => (int) $request->getParam('action_id'),
      'max_id'    => (int) $request->getParam('maxid'),
      'min_id'    => (int) $request->getParam('minid'),     
      'limit'     => (int) $length,
      'showTypes' => $actionTypeFilters,
      'filterFeed'=>$filterFeed,
      'hashTag' => $hashTag,
      'targetPost'=>true,
      'isOnThisDayPage'=>$this->view->isOnThisDayPage,
    );
    // Pre-process feed items
    $selectCount = 0;
    $nextid = null;
    $firstid = null;
    $tmpConfig = $config;
    $activity = array();
    $endOfFeed = false;
    $friendRequests = array();
    $itemActionCounts = array();
    $enabledModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    do {
      // Get current batch
      $actions = null;
      // Where the Activity Feed is Fetched
      if( !empty($subject) ) {
        $actions = $actionTable->getActivityAbout($subject, $viewer, $tmpConfig);
      } else {
        $actions = $actionTable->getActivity($viewer, $tmpConfig);
      }
      $selectCount++;
      // Are we at the end?
      if( count($actions) < $length || count($actions) <= 0 ) {
        $endOfFeed = true;
      }
      // Pre-process
      if( count($actions) > 0 ) {
        foreach( $actions as $action ) {
          // get next id
          if( null === $nextid || $action->action_id <= $nextid ) {
            $nextid = $action->action_id - 1;
          }
          // get first id
          if( null === $firstid || $action->action_id > $firstid ) {
            $firstid = $action->action_id;
          }
          // skip disabled actions
          if( !$action->getTypeInfo() || !$action->getTypeInfo()->enabled ) continue;
          // skip items with missing items
          if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
          if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;
          // track/remove users who do too much (but only in the main feed)
          if( empty($subject) ) {
            $actionSubject = $action->getSubject();
            $actionObject = $action->getObject();
            if( !isset($itemActionCounts[$actionSubject->getGuid()]) ) {
              $itemActionCounts[$actionSubject->getGuid()] = 1;
            } else if( $itemActionCounts[$actionSubject->getGuid()] >= $itemActionLimit ) {
              continue;
            } else {
              $itemActionCounts[$actionSubject->getGuid()]++;
            }
          }
          // remove duplicate friend requests
          if( $action->type == 'friends' ) {
            $id = $action->subject_id . '_' . $action->object_id;
            $rev_id = $action->object_id . '_' . $action->subject_id;
            if( in_array($id, $friendRequests) || in_array($rev_id, $friendRequests) ) {
              continue;
            } else {
              $friendRequests[] = $id;
              $friendRequests[] = $rev_id;
            }
          }
          // remove items with disabled module attachments
          try {
            $attachments = $action->getAttachments();
          } catch (Exception $e) {
            // if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
            continue;
          }
          // add to list
          if( count($activity) < $length ) {
            $activity[] = $action;
            if( count($activity) == $length ) {
              break;
            }
          }
        }
      }
      // Set next tmp max_id
      if( $nextid ) {
        $tmpConfig['max_id'] = $nextid;
      }
      if( !empty($tmpConfig['action_id']) ) {
        $actions = array();
      }
    } 
    while( count($activity) < $length && $selectCount <= 3 && !$endOfFeed );
    $this->view->activity = $activity;
    $this->view->activityCount = count($activity);
    $this->view->nextid = $nextid;
    $this->view->firstid = $firstid;
    $this->view->endOfFeed = $endOfFeed;
    // Get some other info
    if( !empty($subject) ) {
      $this->view->subjectGuid = $subject->getGuid(false);
    }
    $this->view->enableComposer = false;
    if( $viewer->getIdentity() && !$this->_getParam('action_id') ) {
      if( !$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer)) ) {
        if( Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status') ) {
          $this->view->enableComposer = true;
        }
      } else if( $subject ) {
        if( Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment') ) {
          $this->view->enableComposer = true;
        }
      }
    }
    foreach( Zend_Registry::get('Engine_Manifest') as $key=>$data ) {
      if( empty($data['composer']) ) {
        continue;
      }
      foreach( $data['composer'] as $type => $config ) {
        if((!in_array($type,$composerOptions) && $type != 'sesadvancedactivityfacebook' && $type != 'sesadvancedactivitytwitter' && $type != 'sesadvancedactivitylinkedin') )
          continue;
       ;
        if( !empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1]) ) {
          continue;
        }
        if($type == 'photo' && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesalbum') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')){
          $config['script'][0] = '_composeadvancedactivityphoto.tpl';
          $config['script'][1] = 'sesadvancedactivity';
        }
        $composePartials[$type] = $config['script'];
      }
    }
    $composePartialsArray = array();
    //get diff
      foreach($composerOptions as $composerSetting){
         if(isset($composePartials[$composerSetting]))
           $composePartialsArray[$composerSetting] = $composePartials[$composerSetting];
       }
      foreach($composePartials as $key =>$composePartial){
        if(!array_key_exists($key,$composerArray))  {
          $composePartialsArray[$key] = $composePartial;  
        }
      }
    //remove Key from array
    $arrayRemove = array("album","buysell","sesadvancedactivitytargetpost","fileupload","sesevent");
    if((!$this->view->isMemberHomePage && ($subject && $subject->getType() != 'user'))){
      foreach($arrayRemove as $key){
        unset($composePartialsArray[$key]);
      }
      $this->view->composerOptions = array_diff($composerOptions, array("tagUseses","locationses","shedulepost"));
    }  
      
    
    $this->view->composePartials = $composePartialsArray;
    // Form token
    $session = new Zend_Session_Namespace('ActivityFormToken');
    //$session->setExpirationHops(10);
    if( empty($session->token) ) {
      $this->view->formToken = $session->token = md5(time() . $viewer->getIdentity() . get_class($this));
    } else {
      $this->view->formToken = $session->token;
    }
  }
}