<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Widget_SitemobileAdvfeedController extends Engine_Content_Widget_Abstract {

  public function indexAction() {



    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
      return $this->setNoRender();
    }

    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemobile/modules/Advancedactivity/View/Helper', 'Advancedactivity_View_Helper');

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    $photoUploadUrl = 'album/album/compose-upload/type/wall/';
    $videoUploadUrl = 'video/index/compose-upload/format/json/c_type/wall';
    $videoDeleteUrl = 'video/index/delete';
    $musicUploadUrl = 'music/playlist/add-song/format/json?ul=1&type=wall';
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->module_name = $module = $request->getModuleName();
    $this->view->action_name = $action = $request->getActionName();
    $this->view->controller = $controller = $request->getControllerName();
    $this->view->isHashTagPage = $isHashTagPage = $module == 'sitehashtag' && $controller == 'index' && $action == 'index';

    $this->view->resource_type = '';
    if (Engine_Api::_()->core()->hasSubject()) {
      // Get subject
      $subject = Engine_Api::_()->core()->getSubject();
      $this->view->resource_type = $subject->getType();
      if (!in_array($subject->getType(), array('sitepage_page', 'sitepageevent_event', 'sitebusiness_business', 'sitebusinessevent_event', 'sitegroup_group', 'sitegroupevent_event'))) {
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
          return $this->setNoRender();
        }
      } else if (in_array($subject->getType(), array('sitepage_page', 'sitepageevent_event'))) {
        $pageSubject = $subject;
        if ($subject->getType() == 'sitepageevent_event')
          $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);

        $photoUploadUrl = 'sitepage/album/compose-upload/type/wall/';

        $videoUploadUrl = 'sitepagevideo/index/compose-upload/format/json/c_type/wall';
        $videoDeleteUrl = 'sitepagevideo/index/delete';
        $musicUploadUrl = 'sitepagemusic/playlist/add-song/format/json?ul=1&type=wall';
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'view');
        if (empty($isManageAdmin)) {
          return $this->setNoRender();
        }
      } else if (in_array($subject->getType(), array('sitebusiness_business', 'sitebusinessevent_event'))) {
        $businessSubject = $subject;
        if ($subject->getType() == 'sitebusinessevent_event')
          $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
        $photoUploadUrl = 'sitebusiness/album/compose-upload/type/wall/';
        $videoUploadUrl = 'sitebusiness/index/compose-upload/format/json/c_type/wall';
        $videoDeleteUrl = 'sitebusiness/index/delete';
        $musicUploadUrl = 'sitebusinessmusic/playlist/add-song/format/json?ul=1&type=wall';
        $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'view');
        if (empty($isManageAdmin)) {
          return $this->setNoRender();
        }
      } else if (in_array($subject->getType(), array('sitegroup_group', 'sitegroupevent_event'))) {
        $groupSubject = $subject;
        if ($subject->getType() == 'sitegroupevent_event')
          $groupSubject = Engine_Api::_()->getItem('sitegroup_group', $subject->group_id);
        $photoUploadUrl = 'sitegroup/album/compose-upload/type/wall/';
        $videoUploadUrl = 'sitegroup/index/compose-upload/format/json/c_type/wall';
        $videoDeleteUrl = 'sitegroup/index/delete';
        $musicUploadUrl = 'sitegroupmusic/playlist/add-song/format/json?ul=1&type=wall';
        $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($groupSubject, 'view');
        if (empty($isManageAdmin)) {
          return $this->setNoRender();
        }
      }
    }


    //HOMEFEED WORK:
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $sitemobileAdvfeed = Zend_Registry::isRegistered('sitemobileAdvfeed') ? Zend_Registry::get('sitemobileAdvfeed') : null;
    $this->view->enableComposer = false;
    $this->view->action_id = $request->getParam('action_id');
    $this->view->viewAllLikes = $request->getParam('viewAllLikes', $request->getParam('show_likes', false));
    $this->view->viewAllComments = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
    if ($viewer->getIdentity() && !$request->getParam('action_id')) {
      if (Engine_Api::_()->seaocore()->isLessThan420ActivityModule()) {
        if (!$subject || $subject->authorization()->isAllowed($viewer, 'comment')) {
          $this->view->enableComposer = true;
        }
      } else {
        if (!$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer))) {
          if (Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status')) {
            $this->view->enableComposer = true;
          }
        } else if ($subject) {
          if (Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment')) {
            $this->view->enableComposer = true;
          }
        }
      }



      if (!empty($subject)) {
        // Get subject

        if ($subject->getType() == 'sitepage_page' || $subject->getType() == 'sitepageevent_event') {
          $pageSubject = $subject;
          if ($subject->getType() == 'sitepageevent_event')
            $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
          if ($this->view->enableComposer) {
            $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
            $activityFeedType = null;
            if (Engine_Api::_()->sitepage()->isPageOwner($pageSubject) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
              $activityFeedType = 'sitepage_post_self';
            else
              $activityFeedType = 'sitepage_post';
            if (!$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
              $this->view->enableComposer = false;
            }
          }
        } else if ($subject->getType() == 'sitebusiness_business' || $subject->getType() == 'sitebusinessevent_event') {
          $businessSubject = $subject;
          if ($subject->getType() == 'sitebusinessevent_event')
            $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
          $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
          if ($this->view->enableComposer) {
            $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
            $activityFeedType = null;

            if (Engine_Api::_()->sitebusiness()->isBusinessOwner($businessSubject) && Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable())
              $activityFeedType = 'sitebusiness_post_self';
            elseif ($businessSubject->all_post || Engine_Api::_()->sitebusiness()->isBusinessOwner($businessSubject))
              $activityFeedType = 'sitebusiness_post';
            if (!empty($activityFeedType) && !$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
              $this->view->enableComposer = false;
            }
          }
        } else if ($subject->getType() == 'sitebusiness_business' || $subject->getType() == 'sitebusinessevent_event') {
          $businessSubject = $subject;
          if ($subject->getType() == 'sitebusinessevent_event')
            $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
          $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
          if ($this->view->enableComposer) {
            $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
            $activityFeedType = null;

            if (Engine_Api::_()->sitebusiness()->isBusinessOwner($businessSubject) && Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable())
              $activityFeedType = 'sitebusiness_post_self';
            elseif ($businessSubject->all_post || Engine_Api::_()->sitebusiness()->isBusinessOwner($businessSubject))
              $activityFeedType = 'sitebusiness_post';
            if (!empty($activityFeedType) && !$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
              $this->view->enableComposer = false;
            }
          }
        } else if ($subject->getType() == 'sitegroup_group' || $subject->getType() == 'sitegroupevent_event') {
          $groupSubject = $subject;
          if ($subject->getType() == 'sitegroupevent_event')
            $groupSubject = Engine_Api::_()->getItem('sitegroup_group', $subject->group_id);
          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($groupSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
          if ($this->view->enableComposer) {
            $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
            $activityFeedType = null;

            if (Engine_Api::_()->sitegroup()->isGroupOwner($groupSubject) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
              $activityFeedType = 'sitegroup_post_self';
            elseif ($groupSubject->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($groupSubject))
              $activityFeedType = 'sitegroup_post';
            if (!empty($activityFeedType) && !$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
              $this->view->enableComposer = false;
            }
          }
        }
      }
    }

    //PRIVACY WORK;
    $this->view->settingsApi = $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->tabtype = $settings->getSetting('advancedactivity.tabtype', 3);
    $this->view->composerType = $settings->getSetting('advancedactivity.composer.type', 0);
    if (!$isHashTagPage && (empty($subject) || $viewer->isSelf($subject))) {
      $this->view->showPrivacyDropdown = in_array('userprivacy', $settings->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy")));
      if ($this->view->showPrivacyDropdown)
        $this->view->showDefaultInPrivacyDropdown = $userPrivacy = Engine_Api::_()->getDbtable('settings', 'user')->getSetting($viewer, "aaf_post_privacy");
      if (empty($userPrivacy))
        $this->view->showDefaultInPrivacyDropdown = $userPrivacy = $settings->getSetting('activity.content', 'everyone');

      $this->view->availableLabels = $availableLabels = array('everyone' => 'Everyone', 'networks' => 'Friends &amp; Networks', 'friends' => 'Friends Only', 'onlyme' => 'Only Me');
      $enableNetworkList = $settings->getSetting('advancedactivity.networklist.privacy', 0);
      if ($enableNetworkList) {
        $this->view->network_lists = $networkLists = Engine_Api::_()->advancedactivity()->getNetworks($enableNetworkList, $viewer);
        $this->view->enableNetworkList = count($networkLists);

        if (Engine_Api::_()->advancedactivity()->isNetworkBasePrivacy($userPrivacy)) {
          $ids = Engine_Api::_()->advancedactivity()->isNetworkBasePrivacyIds($userPrivacy);
          $privacyNetwork = array();
          $privacyNetworkIds = array();
          foreach ($networkLists as $network) {
            if (in_array($network->getIdentity(), $ids)) {
              $privacyNetwork["network_" . $network->getIdentity()] = $network->getTitle();
              $privacyNetworkIds[] = "network_" . $network->getIdentity();
            }
          }
          if (count($privacyNetwork) > 0) {
            $this->view->privacylists = $privacyNetwork;
            $this->view->showDefaultInPrivacyDropdown = $userPrivacy = join(",", $privacyNetworkIds);
          } else {
            $this->view->showDefaultInPrivacyDropdown = $userPrivacy = "networks";
          }
        }
      }
      $this->view->enableList = $userFriendListEnable = $settings->getSetting('user.friends.lists');

      $viewer_id = $viewer->getIdentity();
      if ($userFriendListEnable && !empty($viewer_id)) {
        $listTable = Engine_Api::_()->getItemTable('user_list');
        $this->view->lists = $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));
        $this->view->countList = $countList = @count($lists);
        if (!empty($countList) && !empty($userPrivacy) && !in_array($userPrivacy, array('everyone', 'networks', 'friends', 'onlyme')) && !Engine_Api::_()->advancedactivity()->isNetworkBasePrivacy($userPrivacy)) {
          $privacylists = $listTable->fetchAll($listTable->select()->where('list_id IN(?)', array(explode(",", $userPrivacy))));
          $temp_list = array();
          foreach ($privacylists as $plist) {
            $temp_list[$plist->list_id] = $plist->title;
          }
          if (count($temp_list) > 0) {
            $this->view->privacylists = $temp_list;
          } else {
            $this->view->showDefaultInPrivacyDropdown = $userPrivacy = "friends";
          }
        }
      } else {
        $userFriendListEnable = 0;
      }
      $this->view->enableList = $userFriendListEnable;
    }

    $this->view->composePartials = '';
    if (empty($sitemobileAdvfeed)) {
      return $this->setNoRender();
    }


    $listLimit = 0;
    $composerLimit = 1;
    // Get some options
    $this->view->homefeed = $homefeed = $request->getParam('homefeed', false);
    $this->view->feedOnly = $feedOnly = $request->getParam('feedOnly', false);
    $this->view->length = $length = $this->_getParam('sitemobileadvfeed_length', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
    $this->view->itemActionLimit = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);
    $getComposerValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.composer.value', $listLimit);

    $this->view->curr_url = $curr_url = $request->getRequestUri(); // Return the current URL.
    $actionTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');

    $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
    $this->view->viewAllLikes = $request->getParam('viewAllLikes', $request->getParam('show_likes', false));
    $this->view->viewAllComments = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
    $getListViewValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.list.view.value', $composerLimit);
    $getPublishValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.publish.str.value', $composerLimit);
    $this->view->getUpdate = $request->getParam('getUpdate');
    $this->view->checkUpdate = $request->getParam('checkUpdate');
    $this->view->action_id = (int) $request->getParam('action_id');
    $this->view->post_failed = (int) $request->getParam('pf');


    if ($feedOnly || $homefeed) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    if ($length > 50) {
      $this->view->length = $length = 50;
    }

    // Get all activity feed types for custom view?
    $actionTypeFilters = array();
    $listTypeFilter = array();
    $default_firstid = null;
    $this->view->isFromTab = $request->getParam('isFromTab', false);
    $this->view->actionFilter = $actionTypeGroup = $request->getParam('actionFilter', 'all');
    $viewer_id = $viewer->getIdentity();
    if (empty($subject) && $request->getParam('actionFilter') && $this->view->isFromTab && $viewer_id) {
      if ($settings->getSetting('advancedactivity.save.filter', 0)) {
        $contentTabs = Engine_Api::_()->getDbtable('contents', 'advancedactivity')->getContentList(array('content_tab' => 1));
        foreach ($contentTabs as $v) {
          if ($actionTypeGroup == $v->filter_type) {
            Engine_Api::_()->getDbtable('userSettings', 'seaocore')->setSetting($viewer, "aaf_filter", $actionTypeGroup);
            break;
          }
        }
      }
    }
    $this->view->tabtype = $settings->getSetting('advancedactivity.tabtype', 3);
    if ((empty($subject) || $viewer->isSelf($subject)) && !$isHashTagPage) {
      $this->view->enableList = $userFriendListEnable = $settings->getSetting('user.friends.lists');
      $viewer_id = $viewer->getIdentity();
      if ($userFriendListEnable && !empty($viewer_id)) {
        $listTable = Engine_Api::_()->getItemTable('user_list');
        $this->view->lists = $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));
        $this->view->countList = $countList = @count($lists);
      } else {
        $userFriendListEnable = 0;
      }
      $this->view->enableList = $userFriendListEnable;
    }

    if (!$feedOnly && empty($subject) && !$isHashTagPage) {
      if (!empty($viewer_id)) {
        $this->view->enableFriendListFilter = $enableFriendListFilter = $userFriendListEnable && $settings->getSetting('advancedactivity.friendlist.filtering', 1);
      } else {
        $this->view->enableFriendListFilter = $enableFriendListFilter = 0;
      }
      $enableContentTabs = 0;

      $this->view->contentTabs = $contentTabs = Engine_Api::_()->getDbtable('contents', 'advancedactivity')->getContentList(array('content_tab' => 1));
      $this->view->contentTabMax = $settings->getSetting('advancedactivity.defaultvisible', 7);
      $countContentTabs = @count($this->view->contentTabs);
      if ($countContentTabs)
        $enableContentTabs = 1;

      $this->view->enableContentTabs = $enableContentTabs;
      $filterTabs = array();
      $i = 0;
      $defaultcontentTab = $request->getParam('actionFilter');
      $defaultUsercontentTab = $viewer_id && $settings->getSetting('advancedactivity.save.filter', 0) ? Engine_Api::_()->getDbtable('settings', 'user')->getSetting($viewer, "aaf_filter") : '';
      foreach ($contentTabs as $value) {
        if ((empty($viewer_id) && in_array($value->filter_type, array('membership'))) || !Engine_Api::_()->sitemobile()->isSupportedModule($value->module_name))
          continue;
        $filterTabs[$i]['filter_type'] = $value->filter_type;
        $filterTabs[$i]['tab_title'] = $value->resource_title;
        $filterTabs[$i]['list_id'] = $value->content_id;
        $i++;
        if (empty($defaultcontentTab)) {
          $defaultcontentTab = $value->filter_type;
        }
        if ($defaultUsercontentTab == $value->filter_type) {
          $defaultcontentTab = $value->filter_type;
        }
      }
      if ($defaultcontentTab) {
        $this->view->actionFilter = $actionTypeGroup = $defaultcontentTab;
        if ($defaultcontentTab != 'all')
          $default_firstid = $actionTable->select()->from($actionTable, 'action_id')->order('action_id DESC')->limit(1)->query()->fetchColumn();
      }
      $enableNetworkListFilter = $settings->getSetting('advancedactivity.networklist.filtering', 0);
      if ($viewer_id && $enableNetworkListFilter) {
        $networkLists = Engine_Api::_()->advancedactivity()->getNetworks($enableNetworkListFilter, $viewer);
        $countNetworkLists = count($networkLists);
        if ($countNetworkLists) {
          if (count($filterTabs) > $this->view->contentTabMax)
            $filterTabs[$i]['filter_type'] = "separator";
          $i++;
          foreach ($networkLists as $value) {
            $filterTabs[$i]['filter_type'] = "network_list";
            $filterTabs[$i]['tab_title'] = $value->getTitle();
            $filterTabs[$i]['list_id'] = $value->getIdentity();
            $i++;
          }
        }
      }

      if ($enableFriendListFilter) {
        $countlistsLists = count($lists);
        if ($countlistsLists) {
          if (count($filterTabs) > $this->view->contentTabMax)
            $filterTabs[$i]['filter_type'] = "separator";
          $i++;
          foreach ($lists as $value) {
            $filterTabs[$i]['filter_type'] = "member_list";
            $filterTabs[$i]['tab_title'] = $value->title;
            $filterTabs[$i]['list_id'] = $value->list_id;
            $i++;
          }
        }
      }


      $this->view->canCreateCustomList = 0;
      if ($viewer_id) {
        $this->view->canCreateCustomList = $settings->getSetting('advancedactivity.customlist.filtering', 1);
        $customTypeLists = Engine_Api::_()->getDbtable('customtypes', 'advancedactivity')->getCustomTypeList(array('enabled' => 1));
        $count = count($customTypeLists);
        if (empty($count))
          $this->view->canCreateCustomList = 0;
        if ($this->view->canCreateCustomList) {
          $customLists = Engine_Api::_()->getDbtable('lists', 'advancedactivity')->getMemberOfList($viewer);
          $countCustomLists = count($customLists);
          if ($countCustomLists) {
            if (count($filterTabs) > $this->view->contentTabMax) {
              $filterTabs[$i]['filter_type'] = "separator";
              $i++;
            }
            foreach ($customLists as $value) {
              $filterTabs[$i]['filter_type'] = "custom_list";
              $filterTabs[$i]['tab_title'] = $value->title;
              $filterTabs[$i]['list_id'] = $value->list_id;
              $i++;
            }
          }
        }
      }
      $this->view->filterTabs = $filterTabs;
    }




    if ($actionTypeGroup && !in_array($actionTypeGroup, array('membership', 'owner', 'all', 'network_list', 'member_list', 'custom_list'))) {
      $actionTypesTable = Engine_Api::_()->getDbtable('actionTypes', 'advancedactivity');
      $this->view->groupedActionTypes = $groupedActionTypes = $actionTypesTable->getEnabledGroupedActionTypes();
      if (isset($groupedActionTypes[$actionTypeGroup])) {
        $actionTypeFilters = $groupedActionTypes[$actionTypeGroup];
        if ($actionTypeGroup == 'sitepage') {
          $actionTypeGroupSubModules = Engine_Api::_()->advancedactivity()->getSubModules($actionTypeGroup);
          foreach ($actionTypeGroupSubModules as $actionTypeGroupSubModule) {
            if (isset($groupedActionTypes[$actionTypeGroupSubModule])) {
              $actionTypeFilters = array_merge($actionTypeFilters, $groupedActionTypes[$actionTypeGroupSubModule]);
            }
          }
        } else if ($actionTypeGroup == 'sitebusiness') {
          $actionTypeGroupSubModules = Engine_Api::_()->advancedactivity()->getSubModules($actionTypeGroup);
          foreach ($actionTypeGroupSubModules as $actionTypeGroupSubModule) {
            if (isset($groupedActionTypes[$actionTypeGroupSubModule])) {
              $actionTypeFilters = array_merge($actionTypeFilters, $groupedActionTypes[$actionTypeGroupSubModule]);
            }
          }
        }
      }
    } else if (in_array($actionTypeGroup, array('member_list', 'custom_list')) && ($list_id = $this->_getParam('list_id')) != null) {
      $listTypeFilter = Engine_Api::_()->advancedactivity()->getListBaseContent($actionTypeGroup, array('list_id' => $list_id));
    } else if ($actionTypeGroup == 'network_list' && ($list_id = $this->_getParam('list_id') != null)) {
      $list_id = $this->_getParam('list_id');
      $listTypeFilter = array($list_id);
    }
    // Get config options for activity
    $config = array(
        'action_id' => (int) $request->getParam('action_id'),
        'max_id' => (int) $request->getParam('maxid'),
        'min_id' => (int) $request->getParam('minid'),
        'limit' => (int) $length,
        'showTypes' => $actionTypeFilters,
        'membership' => $actionTypeGroup == 'membership' ? true : false,
        'listTypeFilter' => $listTypeFilter,
        'actionTypeGroup' => $actionTypeGroup
    );


    $isHashTagEnabled = Engine_Api::_()->getDbtable('modules', 'sitemobile')->isModuleEnabled('sitehashtag');
    $search = $request->getParam('search');
    if ($isHashTagEnabled && $search) {
      $config['hashtag'] = $search;
      $this->view->search = $search;
      $this->view->updateSettings = false;
    }
    // Pre-process feed items
    $selectCount = 0;
    $nextid = null;
    $firstid = null;
    $tmpConfig = $config;
    $activity = array();
    $endOfFeed = false;

    $friendRequests = array();
    $itemActionCounts = array();
    // $enabledModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

    $hideItems = array();
    if (empty($subject)) {
      if ($viewer->getIdentity())
        $hideItems = Engine_Api::_()->getDbtable('hide', 'advancedactivity')->getHideItemByMember($viewer);
      if ($default_firstid) {
        $firstid = $default_firstid;
      }
    }

    $grouped_actions = array();

    do {
      // Get current batch
      $actions = null;
      if (!empty($subject)) {
        $actions = $actionTable->getActivityAbout($subject, $viewer, $tmpConfig);
      } else {
        $actions = $actionTable->getActivity($viewer, $tmpConfig);
      }
      $selectCount++;

      // Are we at the end?
      if (count($actions) < $length || count($actions) <= 0) {
        $endOfFeed = true;
      }

      // Pre-process
      if (count($actions) > 0) { 
         // try{
        foreach ($actions as $action) {
          // get next id
          if (null === $nextid || $action->action_id <= $nextid) {
            $nextid = $action->action_id - 1;
          }
          // get first id
          if (null === $firstid || $action->action_id > $firstid) {
            $firstid = $action->action_id;
          }
          // skip the hide actions and content        
          // skip disabled actions
          if (!$action->getTypeInfo() || !$action->getTypeInfo()->enabled)
            continue;
          // skip items with missing items
          if (!$action->getSubject() || !$action->getSubject()->getIdentity())
            continue;
          if (!$action->getObject() || !$action->getObject()->getIdentity())
            continue;

          // skip the hide actions and content 
          if (!empty($hideItems)) {
            if (isset($hideItems[$action->getType()]) && in_array($action->getIdentity(), $hideItems[$action->getType()])) {
              continue;
            }
            if (!$action->getTypeInfo()->is_object_thumb && isset($hideItems[$action->getSubject()->getType()]) && in_array($action->getSubject()->getIdentity(), $hideItems[$action->getSubject()->getType()])) {
              continue;
            }
            if (($action->getTypeInfo()->is_object_thumb || $action->getObject()->getType() == 'user' ) && isset($hideItems[$action->getObject()->getType()]) && in_array($action->getObject()->getIdentity(), $hideItems[$action->getObject()->getType()])) {
              continue;
            }
          }
          // track/remove users who do too much (but only in the main feed)
          if (empty($subject)) {
            $actionSubject = $action->getSubject();
            $actionObject = $action->getObject();
            if (isset($action->getTypeInfo()->is_object_thumb) && $action->getTypeInfo()->is_object_thumb) {
              $itemAction = $action->getObject();
            } else {
              $itemAction = $action->getSubject();
            }
            if (!isset($itemActionCounts[$itemAction->getGuid()])) {
              $itemActionCounts[$itemAction->getGuid()] = 1;
            } else if ($itemActionCounts[$itemAction->getGuid()] >= $itemActionLimit) {
              continue;
            } else {
              $itemActionCounts[$itemAction->getGuid()]++;
            }
          }
          // remove duplicate friend requests
          if ($action->type == 'friends') {
            $id = $action->subject_id . '_' . $action->object_id;
            $rev_id = $action->object_id . '_' . $action->subject_id;
            if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests)) {
              continue;
            } else {
              $friendRequests[] = $id;
              $friendRequests[] = $rev_id;
            }
          }

          /* Start Working group feed. */
          if (!empty($action->getTypeInfo()->is_grouped) && isset($action->getTypeInfo()->is_grouped)) {
            if ($action->type == 'friends') {
              $object_guid = $action->getSubject()->getGuid();
              $total_guid = $action->type . '_' . $object_guid;

              if (!isset($grouped_actions[$total_guid])) {
                $grouped_actions[$total_guid] = array();
              }
              $grouped_actions[$total_guid][] = $action->getObject();
            } elseif ($action->type == 'tagged') {
              foreach ($action->getAttachments() as $attachment) {
                $object_guid = $attachment->item->getGuid();
                $Subject_guid = $action->getSubject()->getGuid();
                $total_guid = $action->type . '_' . $object_guid . '_' . $Subject_guid;
              }
              if (!isset($grouped_actions[$total_guid])) {
                $grouped_actions[$total_guid] = array();
              }
              $grouped_actions[$total_guid][$action->getObject()->getGuid()] = $action->getObject();
            } else {
              $object_guid = $action->getObject()->getGuid();
              $total_guid = $action->type . '_' . $object_guid;

              if (!isset($grouped_actions[$total_guid])) {
                $grouped_actions[$total_guid] = array();
              }
              $grouped_actions[$total_guid][] = $action->getSubject();
            }

            if (count($grouped_actions[$total_guid]) > 1) {
              continue;
            }
          }
          /* End Working group feed. */

          // remove items with disabled module attachments
          try {
            $attachments = $action->getAttachments();
          } catch (Exception $e) {
            // if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
            continue;
          }

          // add to list
          if (count($activity) < $length) {
            $activity[] = $action;
            if (count($activity) == $length) {
              $actions = array();
            }
          }
        }
//          } catch(Exception $ex) {
//              continue;
//          }
      }

      // Set next tmp max_id
      if ($nextid) {
        $tmpConfig['max_id'] = $nextid;
      }
      if (!empty($tmpConfig['action_id'])) {
        $actions = array();
      }
    } while (count($activity) < $length && $selectCount <= 5 && !$endOfFeed);

    if (count($activity) < $length || count($activity) <= 0 || $nextid < 1) {
      $endOfFeed = true;
    }

    $this->view->groupedFeeds = $grouped_actions;
    $this->view->activity = $activity;
    $this->view->activityCount = count($activity);
    $this->view->nextid = $nextid;
    $this->view->firstid = $firstid;
    $this->view->endOfFeed = $endOfFeed;

    // Get lists
    $showHashtags = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitehashtag.showHashtags', 1);
    if ($showHashtags && $isHashTagEnabled) {
      $this->view->hashtag = $this->getHashtagNames($activity);
    }

    // Get some other info
    if (!empty($subject)) {
      $this->view->subjectGuid = $subject->getGuid(false);
    }

    $this->view->enableComposer = false;
    if ($viewer->getIdentity() && !$this->_getParam('action_id')) {
      if (!$subject || $subject->authorization()->isAllowed($viewer, 'comment')) {
        $this->view->enableComposer = true;
      }
      if (!empty($subject)) {
        // Get subject

        if ($subject->getType() == 'sitepage_page' || $subject->getType() == 'sitepageevent_event') {
          $pageSubject = $subject;
          if ($subject->getType() == 'sitepageevent_event')
            $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
        } else if ($subject->getType() == 'sitebusiness_business' || $subject->getType() == 'sitebusinessevent_event') {
          $businessSubject = $subject;
          if ($subject->getType() == 'sitebusinessevent_event')
            $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
          $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
        } else if ($subject->getType() == 'sitegroup_group' || $subject->getType() == 'sitegroupevent_event') {
          $groupSubject = $subject;
          if ($subject->getType() == 'sitegroupevent_event')
            $groupSubject = Engine_Api::_()->getItem('sitegroup_group', $subject->group_id);
          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($groupSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $this->view->enableComposer = true;
          }
        }
      }
    }

    if($isHashTagPage) {
      $this->view->enableComposer = false;
    }

    if (($getListViewValue + $getPublishValue) != $getComposerValue)
      Engine_Api::_()->getApi('settings', 'core')->setSetting('advancedactivity.post.active', $composerLimit);
    // Get lists if viewing own profile
    // if( $viewer->isSelf($subject) ) {

    // Form token
    $session = new Zend_Session_Namespace('ActivityFormToken');
    //$session->setExpirationHops(10);
    if (empty($session->token)) {
      $this->view->formToken = $session->token = md5(time() . $viewer->getIdentity() . get_class($this));
    } else {
      $this->view->formToken = $session->token;
    }
    $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
    $autoScrollFeedEnable = $this->_getParam('sitemobileadvfeed_scroll_autoload', 1);
    $maxAutoScrollFeed = $coreSettingsApi->getSetting('advancedactivity.maxautoload', 0);

    $allParmas = array();
    $allParmas['next_id'] = $nextid;
    $allParmas['list_id'] = @$list_id;
    $allParmas['endOfFeed'] = $endOfFeed;
    $allParmas['autoScrollFeedAAFEnable'] = $autoScrollFeedEnable;
    $allParmas['sitemobileadvfeed_scroll_autoload'] = $autoScrollFeedEnable;
    $allParmas['maxAutoScrollAAF'] = $maxAutoScrollFeed;
    $allParmas['actionFilter'] = $actionTypeGroup;
    $allParmas['sitemobileadvfeed_length'] = $length;
    $allParmas['countScrollAAFSocial'] = 0;
    $allParmas['search'] = $this->view->search;
    $allParmas['postURL'] = $this->view->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'post'), 'default', true);

    if (Engine_Api::_()->core()->hasSubject()) {
      // Get subject
      $subject = Engine_Api::_()->core()->getSubject();
      $allParmas['subject_guid'] = $subject->getGuid();
    }
    $allParmas['enableHashTagComposer'] = $this->view->enableComposer && $isHashTagEnabled && Engine_Api::_()->getDbtable('contents', 'sitehashtag')->getEnable('activity');
    
    $this->view->allParams = $allParmas;
    //MAKE AN ARRAY OF ATTACHMENT URLS
    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl() . '/';
    $attachmentsURL = array('photourl' => $baseUrl . $photoUploadUrl, 'videourl' => $baseUrl . $videoUploadUrl, 'videodeleteurl' => $baseUrl . $videoDeleteUrl,
        'musicurl' => $baseUrl . $musicUploadUrl);
    $this->view->attachmentsURL = $attachmentsURL;
  }

  protected function getHashtagNames($activity) {
    $hashTagMapTable = Engine_Api::_()->getDbtable('tagmaps', 'sitehashtag');
    $hashtagNames = array();
    foreach ($activity as $action) {
      preg_match_all("/\B#\w*[a-zA-Z]+\w*/", $action->body, $hashtags);
      $hashtagName = array();
      $hashtagmaps = $hashTagMapTable->getActionTagMaps($action->action_id);
      foreach ($hashtagmaps as $hashtagmap) {
        $tag = Engine_Api::_()->getItem('sitehashtag_tag', $hashtagmap->tag_id);
        if ($tag && !in_array($tag->text, $hashtags[0])) {
          $hashtagName[] = $tag->text;
        }
      }
      $hashtagNames[$action['action_id']] = $hashtagName;
    }
    return $hashtagNames;
  }
}
