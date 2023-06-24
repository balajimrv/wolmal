<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    FeedController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_FeedController extends Siteapi_Controller_Action_Standard {

    /**
     * Init model
     *
     */
    public function init() {
        $subject_type = $this->getRequestParam('subject_type');
        if (0 !== ($subject_id = (int) $this->getRequestParam('subject_id')) &&
                null !== ($subject = Engine_Api::_()->getItem($subject_type, $subject_id)))
            Engine_Api::_()->core()->setSubject($subject);

        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();
    }

    /**
     * Throw the init constructor errors.
     *
     * @return array
     */
    public function throwErrorAction() {
        $message = $this->getRequestParam("message", null);
        if (($error_code = $this->getRequestParam("error_code")) && !empty($error_code)) {
            if (!empty($message))
                $this->respondWithValidationError($error_code, $message);
            else
                $this->respondWithError($error_code);
        }
        return;
    }

    /**
     * Get ativity feeds
     *
     */
    public function indexAction() {

        // Get params
        $minid = $this->getRequestParam('minid', 0);
        $maxid = $this->getRequestParam('maxid', 0);
//    $homefeed = $this->getRequestParam('homefeed', false);
//    $feedOnly = $this->getRequestParam('feedOnly', false);
        $action_id = (int) $this->getRequestParam('action_id');
        $feed_filter = $this->getRequestParam('feed_filter', false);
        $feedCountOnly = $this->getRequestParam('feed_count_only', false);

        //     Hashtag search
        if (array_key_exists('hashtag', $_REQUEST) && !( $searchText = $_REQUEST['hashtag'] ))
            $this->respondWithValidationError('parameter_missing', 'hashtag empty');

        $searchText = urldecode($searchText);

        $feedCountWithContent = $this->getRequestParam('feed_count_with_content', false);
//    $isForCategoryPage = $this->getRequestParam('isForCategoryPage', false);
        $actionTypeGroup = (empty($action_id)) ? $this->getRequestParam('filter_type', 'all') : '';
        $siteapiActivityFeeds = Zend_Registry::isRegistered('siteapiActivityFeeds') ? Zend_Registry::get('siteapiActivityFeeds') : null;
        $length = $this->getRequestParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));

        // Set params
        $endOfFeed = $isAllowedToView = false;
        $firstid = $nextid = $default_firstid = $subject = null;
        $grouped_actions = $hideItems = $itemActionCounts = $friendRequests = $activity = $listTypeFilter = $actionTypeFilters = $getBodyResponse = array();
        $selectCount = $listLimit = 0;
        $composerLimit = 1;
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $itemActionLimit = $settings->getSetting('activity.userlength', 5);
        $actionTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');
//    if ( empty($feedOnly) && Engine_Api::_()->core()->hasSubject() )
        if (Engine_Api::_()->core()->hasSubject())
            $feedOnly = 1;
        if (empty($siteapiActivityFeeds))
            $this->respondWithError('unauthorized');
        // Is subject set then check the authentication.
        if (Engine_Api::_()->core()->hasSubject()) {
            // Get subject
            $parentSubject = $subject = Engine_Api::_()->core()->getSubject();
            if ($subject->getType() == 'siteevent_event') {
                $parentSubject = Engine_Api::_()->getItem($subject->getParent()->getType(), $subject->getParent()->getIdentity());
            }
            if (!in_array($subject->getType(), array('sitepage_page', 'sitepageevent_event', 'sitegroup_group', 'sitegroupevent_event', 'sitestore_store', 'sitestoreevent_event', 'sitebusiness_business', 'sitebusinessevent_event')) && !in_array($parentSubject->getType(), array('sitepage_page', 'sitegroup_group', 'sitestore_store', 'sitebusiness_business'))) {
                if (!$subject->authorization()->isAllowed($viewer, 'view') && !$parentSubject->authorization()->isAllowed($viewer, 'view'))
                    $isAllowedToView = true;
            } else if (in_array($subject->getType(), array('sitepage_page', 'sitepageevent_event')) || ($subject->getType() == 'sitepage_page')) {
                $pageSubject = $parentSubject;
                if ($subject->getType() == 'sitepageevent_event')
                    $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
                $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'view');
                if (empty($isManageAdmin))
                    $isAllowedToView = true;
            } else if (in_array($subject->getType(), array('sitebusiness_business', 'sitebusinessevent_event')) || ($subject->getType() == 'sitebusiness_business')) {
                $businessSubject = $parentSubject;
                if ($subject->getType() == 'sitebusinessevent_event')
                    $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
                $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'view');
                if (empty($isManageAdmin))
                    $isAllowedToView = true;
            } else if (in_array($subject->getType(), array('sitegroup_group', 'sitegroupevent_event')) || ($subject->getType() == 'sitegroup_group')) {
                $groupSubject = $parentSubject;
                if ($subject->getType() == 'sitegroupevent_event')
                    $groupSubject = Engine_Api::_()->getItem('sitegroup_group', $subject->group_id);
                $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($groupSubject, 'view');
                if (empty($isManageAdmin))
                    $isAllowedToView = true;
            } else if (in_array($subject->getType(), array('sitestore_store', 'sitestoreevent_event')) || ($subject->getType() == 'sitestore_store')) {
                $storeSubject = $parentSubject;
                if ($subject->getType() == 'sitestoreevent_event')
                    $storeSubject = Engine_Api::_()->getItem('sitestore_store', $subject->store_id);
                $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'view');
                if (empty($isManageAdmin))
                    $isAllowedToView = true;
            }
            // Not authorized to view feeds.
            if (!empty($isAllowedToView))
                $this->respondWithError('unauthorized');
        }
        // Activity feed not be more then from 50
        $length = ($length > 50) ? 50 : $length;
        // Set the Content List
        if (!empty($feed_filter) && empty($feedCountOnly) && empty($subject) && $this->getRequestParam('filter_type') && $viewer_id) {
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
        // Start to make Filters array
//    if ( !empty($feed_filter) && empty($feedCountOnly) && empty($feedOnly) && empty($subject) && !$isForCategoryPage ) {
        if (!empty($feed_filter) && empty($feedCountOnly) && empty($feedOnly) && empty($subject)) {
            if (!empty($viewer_id)) {
                $enableFriendListFilter = $settings->getSetting('advancedactivity.friendlist.filtering', 1);
            } else {
                $enableFriendListFilter = 0;
            }
            $enableContentTabs = 0;
            $contentTabs = Engine_Api::_()->getDbtable('contents', 'advancedactivity')->getContentList(array('content_tab' => 1));
            $countContentTabs = @count($contentTabs);
            if ($countContentTabs)
                $enableContentTabs = 1;
            $filterTabs = array();
            $i = 0;
            $defaultcontentTab = $this->getRequestParam('filter_type');
            $defaultUsercontentTab = $viewer_id && $settings->getSetting('advancedactivity.save.filter', 0) ? Engine_Api::_()->getDbtable('settings', 'user')->getSetting($viewer, "aaf_filter") : '';
            foreach ($contentTabs as $value) {

                if (empty($viewer_id) && in_array($value->filter_type, array('membership', 'only_network', 'user_saved')))
                    continue;


                if ((_CLIENT_TYPE && (_CLIENT_TYPE == 'ios') && _IOS_VERSION && _IOS_VERSION < '1.8') || (_CLIENT_TYPE == 'both')) {
                    if (strstr($value->filter_type, 'sitegroup'))
                        continue;
                }

                if ((_CLIENT_TYPE && (_CLIENT_TYPE == 'android') && _ANDROID_VERSION && _ANDROID_VERSION < '1.7.1') || (_CLIENT_TYPE == 'both')) {
                    if (strstr($value->filter_type, 'sitegroup'))
                        continue;
                }

                if ((_CLIENT_TYPE && (_CLIENT_TYPE == 'ios') && _IOS_VERSION && _IOS_VERSION < '1.5.2') || (_CLIENT_TYPE == 'both')) {
                    if (strstr($value->filter_type, 'sitereview_listtype'))
                        continue;
                }

                if ((_CLIENT_TYPE && (_CLIENT_TYPE == 'android') && _ANDROID_VERSION && _ANDROID_VERSION < '1.6.2') || (_CLIENT_TYPE == 'both')) {
                    if (strstr($value->filter_type, 'sitereview_listtype'))
                        continue;
                }

                $filterTabs[$i]['tab_title'] = $this->translate($value->resource_title);
                $filterTabs[$i]['urlParams']['filter_type'] = $value->filter_type;
                $filterTabs[$i]['urlParams']['list_id'] = $value->content_id;
                $filterTabs[$i]['urlParams']['feedOnly'] = $filterTabs[$i]['urlParams']['isFromTab'] = 1;
                $i++;
                if (empty($defaultcontentTab)) {
                    $defaultcontentTab = $value->filter_type;
                }
                if ($defaultUsercontentTab == $value->filter_type) {
                    $defaultcontentTab = $value->filter_type;
                }
            }
            if ($defaultcontentTab) {
                $actionFilter = $actionTypeGroup = $defaultcontentTab;
                if ($defaultcontentTab != 'all')
                    $default_firstid = $actionTable->select()->from($actionTable, 'action_id')->order('action_id DESC')->limit(1)->query()->fetchColumn();
            }
            $enableNetworkListFilter = $settings->getSetting('advancedactivity.networklist.filtering', 0);
            if ($viewer_id && $enableNetworkListFilter) {
                $networkLists = Engine_Api::_()->advancedactivity()->getNetworks($enableNetworkListFilter, $viewer);
                $countNetworkLists = count($networkLists);
                if ($countNetworkLists) {
                    foreach ($networkLists as $value) {
                        $filterTabs[$i]['urlParams']['filter_type'] = "network_list";
                        $filterTabs[$i]['tab_title'] = $value->getTitle();
                        $filterTabs[$i]['urlParams']['list_id'] = $value->getIdentity();
                        $filterTabs[$i]['urlParams']['feedOnly'] = $filterTabs[$i]['urlParams']['isFromTab'] = 1;
                        $i++;
                    }
                }
            }
            if ($enableFriendListFilter) {
                $countlistsLists = count($lists);
                if ($countlistsLists) {
                    foreach ($lists as $value) {
                        $filterTabs[$i]['urlParams']['filter_type'] = "member_list";
                        $filterTabs[$i]['tab_title'] = $value->title;
                        $filterTabs[$i]['urlParams']['list_id'] = $value->list_id;
                        $filterTabs[$i]['urlParams']['feedOnly'] = $filterTabs[$i]['urlParams']['isFromTab'] = 1;
                        $i++;
                    }
                }
            }
            $canCreateCustomList = 0;
            $canCreateCategroyList = 0;
            $categoryFilter = $settings->getSetting('aaf.category.filtering', 1);
            if ($categoryFilter && Engine_Api::_()->hasModuleBootstrap('advancedactivitypost')) {
                $tableCategories = Engine_Api::_()->getDbtable('categories', 'advancedactivitypost');
                $categoriesList = $tableCategories->getCategories();
                if (count($categoriesList)) {
                    foreach ($categoriesList as $value) {
                        $filterTabs[$i]['urlParams']['filter_type'] = "activity_category";
                        $filterTabs[$i]['tab_title'] = $value->getTitle();
                        $filterTabs[$i]['urlParams']['list_id'] = $value->category_id;
                        $filterTabs[$i]['urlParams']['feedOnly'] = $filterTabs[$i]['urlParams']['isFromTab'] = 1;
                        $i++;
                    }
                }
            }
            if ($viewer_id) {
                $canCreateCustomList = $settings->getSetting('advancedactivity.customlist.filtering', 1);
                $customTypeLists = Engine_Api::_()->getDbtable('customtypes', 'advancedactivity')->getCustomTypeList(array('enabled' => 1));
                $count = count($customTypeLists);
                if (empty($count))
                    $canCreateCustomList = 0;
                if ($canCreateCustomList) {
                    $customLists = Engine_Api::_()->getDbtable('lists', 'advancedactivity')->getMemberOfList($viewer, 'default');
                    $countCustomLists = count($customLists);
                    if ($countCustomLists) {
                        foreach ($customLists as $value) {
                            $filterTabs[$i]['urlParams']['filter_type'] = "custom_list";
                            $filterTabs[$i]['tab_title'] = $value->title;
                            $filterTabs[$i]['urlParams']['list_id'] = $value->list_id;
                            $filterTabs[$i]['urlParams']['feedOnly'] = $filterTabs[$i]['urlParams']['isFromTab'] = 1;
                            $i++;
                        }
                    }
                }
                if (Engine_Api::_()->hasModuleBootstrap('advancedactivitypost')) {
                    $tableCategories = Engine_Api::_()->getDbtable('categories', 'advancedactivitypost');
                    $categoriesList = $tableCategories->getCategories();
                    if (count($categoriesList)) {
                        $canCreateCategroyList = $settings->getSetting('aaf.categorylist.filtering', 1);
                        if ($canCreateCategroyList) {
                            $customLists = Engine_Api::_()->getDbtable('lists', 'advancedactivity')->getMemberOfList($viewer, 'category');
                            $countCustomLists = count($customLists);
                            if ($countCustomLists) {
                                foreach ($customLists as $value) {
                                    $filterTabs[$i]['urlParams']['filter_type'] = "category_list";
                                    $filterTabs[$i]['tab_title'] = $value->title;
                                    $filterTabs[$i]['urlParams']['list_id'] = $value->list_id;
                                    $filterTabs[$i]['urlParams']['feedOnly'] = $filterTabs[$i]['urlParams']['isFromTab'] = 1;
                                    $i++;
                                }
                            }
                        }
                    }
                }
            }
        } // End to make Filters array
        if ($actionTypeGroup && !in_array($actionTypeGroup, array('membership', 'owner', 'all', 'network_list', 'member_list', 'custom_list', 'category_list', 'activity_category'))) {
            $actionTypesTable = Engine_Api::_()->getDbtable('actionTypes', 'advancedactivity');
            $groupedActionTypes = $actionTypesTable->getEnabledGroupedActionTypes();
            if (isset($groupedActionTypes[$actionTypeGroup])) {
                $actionTypeFilters = $groupedActionTypes[$actionTypeGroup];
                if (in_array($actionTypeGroup, array('sitepage', 'sitebusiness', 'sitegroup', 'sitestore'))) {
                    $actionTypeGroupSubModules = Engine_Api::_()->advancedactivity()->getSubModules($actionTypeGroup);
                    foreach ($actionTypeGroupSubModules as $actionTypeGroupSubModule) {
                        if (isset($groupedActionTypes[$actionTypeGroupSubModule])) {
                            $actionTypeFilters = array_merge($actionTypeFilters, $groupedActionTypes[$actionTypeGroupSubModule]);
                        }
                    }
                }
            }
        } else if (in_array($actionTypeGroup, array('member_list', 'custom_list', 'category_list')) && ($list_id = $this->getRequestParam('list_id')) != null) {
            $listTypeFilter = Engine_Api::_()->advancedactivity()->getListBaseContent($actionTypeGroup, array('list_id' => $list_id));
        } else if ($actionTypeGroup == 'activity_category' && ($list_id = $this->getRequestParam('list_id')) != null) {
            $listTypeFilter['categories_id'][] = $list_id;
            $actionTypeGroup = 'category_list';
            $list_id = $this->getRequestParam('list_id');
        } else if ($actionTypeGroup == 'network_list' && ($list_id = $this->getRequestParam('list_id') != null)) {
            $list_id = $this->getRequestParam('list_id');
            $listTypeFilter = array($list_id);
        }
        // Get config options for activity
        $tmpConfig = $config = array(
            'action_id' => (int) $action_id,
            'max_id' => (int) $maxid,
            'min_id' => (int) $minid,
            'limit' => (int) $length,
            'showTypes' => !empty($actionTypeFilters) ? $actionTypeFilters : false,
            'membership' => ($actionTypeGroup == 'membership') ? true : false,
            'listTypeFilter' => !empty($listTypeFilter) ? $listTypeFilter : false,
            'actionTypeGroup' => !empty($actionTypeGroup) ? $actionTypeGroup : false
        );

        if (!empty($searchText))
            $config['hashtag'] = $tmpConfig['hashtag'] = $searchText;

        // Get hide items by member. So that we can remove that feed from the response.
        if (empty($subject)) {
            if ($viewer->getIdentity())
                $hideItems = Engine_Api::_()->getDbtable('hide', 'advancedactivity')->getHideItemByMember($viewer);
            if ($default_firstid)
                $firstid = $default_firstid;
        }

        try {
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
                if (count($actions) < $length || count($actions) <= 0)
                    $endOfFeed = true;
                // Pre-process
                if (count($actions) > 0) {
                    foreach ($actions as $action) {
                        // get next id
                        if (null === $nextid || $action->action_id <= $nextid) {
                            $nextid = $action->action_id - 1;
                        }
                        // get first id
                        if (null === $firstid || $action->action_id > $firstid) {
                            $firstid = $action->action_id;
                        }
                        // skip disabled actions
                        if (!$action->getTypeInfo() || !$action->getTypeInfo()->enabled)
                            continue;
                        // skip items with missing items
                        if (!$action->getSubject() || !$action->getSubject()->getIdentity())
                            continue;
                        if (!$action->getObject() || !$action->getObject()->getIdentity())
                            continue;
                        // skip the hide actions and content        
                        if (!empty($hideItems) && empty($action_id)) {
                            if (isset($hideItems[$action->getType()]) && in_array($action->getIdentity(), $hideItems[$action->getType()])) {
                                continue;
                            }
                            if (!$action->getTypeInfo()->is_object_thumb && isset($hideItems[$action->getSubject()->getType()]) && in_array($action->getSubject()->getIdentity(), $hideItems[$action->getSubject()->getType()])) {
                                if (($action->getSubject()->getType() == 'user') && ($action->getSubject()->getIdentity() != $viewer_id))
                                    continue;
                            }
                            if (($action->getTypeInfo()->is_object_thumb || $action->getObject()->getType() == 'user' ) && isset($hideItems[$action->getObject()->getType()]) && in_array($action->getObject()->getIdentity(), $hideItems[$action->getObject()->getType()])) {
                                if (($action->getSubject()->getType() == 'user') && ($action->getSubject()->getIdentity() != $viewer_id))
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
                            } else if (isset($itemActionCounts[$itemAction->getGuid()])) {
                                $itemActionCounts[$itemAction->getGuid()] = $itemActionCounts[$itemAction->getGuid()] ++;
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
                        // add to list
                        if (count($activity) < $length) {
                            $activity[] = $action;
                            if (count($activity) == $length) {
                                $actions = array();
                            }
                        }
                    }
                }
                // Set next tmp max_id
                if ($nextid)
                    $tmpConfig['max_id'] = $nextid;
                if (!empty($tmpConfig['action_id']))
                    $actions = array();
            } while (count($activity) < $length && $selectCount <= 5 && !$endOfFeed);
        } catch (Exception $ex) {
            // Blank Exception
        }

        // Return feedcount only
        if (!empty($feedCountOnly) && empty($feedCountWithContent)) {
            // Delete landing page caching
            Engine_Api::_()->getApi('cache', 'siteapi')->deleteCache('feed_index_homefeed');
            $this->respondWithSuccess(COUNT($activity));
        }

        if (empty($maxid) && count($activity) <= 0) {
//            $this->respondWithSuccess();
        }
        // Check composer enabled or not.
        if ($viewer->getIdentity() && !$this->getRequestParam('action_id')) {
            if (!$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer))) {
                if (Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status')) {
                    $enableComposer = true;
                }
            } else if ($subject) {
                if (Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment')) {
                    $enableComposer = true;
                }
            }
            // Do have image upload permission
            $allow_photo_uploade = (($enableComposer) && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("album")) && (Engine_Api::_()->authorization()->isAllowed("album", null, "create"))) ? true : false;
        }
        // Prepare the activity feed response
        $getBodyResponse['data'] = $getFeeds = Engine_Api::_()->getApi('Siteapi_Feed', 'advancedactivity')->getFeeds($activity, array(
            "subject_info" => $this->getRequestParam('subject_info', false),
            "object_info" => $this->getRequestParam('object_info', false),
        ));



        if (array_key_exists('hashtag', $_REQUEST) && !count($getBodyResponse['data']))
            $this->respondWithError('no_record');

        $getBodyResponse['defaultFeedCount'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 20);
        $getBodyResponse['activityCount'] = COUNT($getFeeds);
        $getBodyResponse['enable_composer'] = !empty($enableComposer) ? $enableComposer : false;
        $getBodyResponse['enable_composer_photo'] = !empty($allow_photo_uploade) ? $allow_photo_uploade : false;
        // Set activity feed max id. It will use in pagination.
        $getBodyResponse['maxid'] = !empty($nextid) ? $nextid : 0;
        // Set min id of activity feed. It will return only first time not for pagination. It will use in New Feed functionality.
        if (empty($maxid))
            $getBodyResponse['minid'] = ++$firstid;
        // Add activity feed filters in response array.
        if (!empty($feed_filter) && !empty($filterTabs))
            $getBodyResponse['filterTabs'] = $filterTabs;
        if (!empty($subject) && !empty($feed_filter)) {
            $getBodyResponse['filterTabs'] = $this->_getFilterForContentProfile($subject);
        }
        // Add feed menu in response array.
        if (!isset($_REQUEST['hashtag']) && $this->getRequestParam('post_menus', true) && $this->getRequestParam('post_elements', true)) {
            $getPostMenus = $this->_getPostFeedOptions();
            if (!empty($getPostMenus))
                $getBodyResponse['feed_post_menu'] = $getPostMenus;
        }

        $getBodyResponse['reactionsEnabled'] = 0;
        $getBodyResponse['stickersEnabled'] = 0;
        $getBodyResponse['emojiEnabled'] = 0;

        try {
            if (Engine_Api::_()->getApi('Siteapi_Feed', 'advancedactivity')->isSitereactionPluginLive()) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereaction') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereaction.reaction.active', 1)) {
                    $getBodyResponse['reactionsEnabled'] = 1;
                    $getBodyResponse['reactions'] = Engine_Api::_()->getApi('Siteapi_Core', 'sitereaction')->getAllReactionIcons();
                }
            }
            if (Engine_Api::_()->getApi('Siteapi_Feed', 'advancedactivity')->isSitestickerPluginLive()) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('nestedcomment') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereaction.collection.active', 1)) {
                    $getBodyResponse['stickersEnabled'] = 1;
                    $getBodyResponse['emojiEnabled'] = 1;
                }
            }
        } catch (Exception $ex) {
            //Blank Exception
        }

        if (!empty($feedCountOnly) && !empty($feedCountWithContent))
            $getBodyResponse['count'] = COUNT($activity);

        // Success
        if (!empty($viewer_id))
            $this->respondWithSuccess($getBodyResponse);
        else
            $this->respondWithSuccess($getBodyResponse, 'homefeed');
    }

    /**
     * Call on activity feed post.
     *
     * @return array
     */
    public function postAction() {
        // Validate request methods
        $this->validateRequestMethod('POST');
        // Throw error for logged-out user
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');
        // Check authorization permission
        $viewer = Engine_Api::_()->user()->getViewer();
        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject();
        else
            $subject = $viewer;
        // Check authorization permission
        if (!$subject->authorization()->isAllowed($viewer, 'comment'))
            $this->respondWithError('unauthorized');
        // Check authorization permission for dependent modules
        if (Engine_Api::_()->core()->hasSubject()) {
            // Get subject
            $parentSubject = $subject = Engine_Api::_()->core()->getSubject();
            if ($subject->getType() == 'siteevent_event') {
                $parentSubject = Engine_Api::_()->getItem($subject->getParent()->getType(), $subject->getParent()->getIdentity());
                if (!Engine_Api::_()->authorization()->isAllowed($subject, $viewer, "post"))
                    $isAllowedToView = true;
            } elseif ($subject->getType() == 'sitepage_page' || $subject->getType() == 'sitepageevent_event' || $parentSubject->getType() == 'sitepage_page') {
                $pageSubject = $parentSubject;
                if ($subject->getType() == 'sitepageevent_event')
                    $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
                $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'comment');
                if (empty($isManageAdmin))
                    $isAllowedToView = true;
            } else if ($subject->getType() == 'sitebusiness_business' || $subject->getType() == 'sitebusinessevent_event' || $parentSubject->getType() == 'sitebusiness_business') {
                $businessSubject = $parentSubject;
                if ($subject->getType() == 'sitebusinessevent_event')
                    $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
                $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'comment');
                if (empty($isManageAdmin))
                    $isAllowedToView = true;
            } elseif ($subject->getType() == 'sitegroup_group' || $subject->getType() == 'sitegroupevent_event' || $parentSubject->getType() == 'sitegroup_group') {
                $groupSubject = $parentSubject;
                if ($subject->getType() == 'sitegroupevent_event')
                    $groupSubject = Engine_Api::_()->getItem('sitegroup_group', $subject->group_id);
                $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($groupSubject, 'comment');
                if (empty($isManageAdmin))
                    $isAllowedToView = true;
            } elseif ($subject->getType() == 'sitestore_store' || $subject->getType() == 'sitestoreevent_event' || $parentSubject->getType() == 'sitestore_store') {
                $storeSubject = $parentSubject;
                if ($subject->getType() == 'sitestoreevent_event')
                    $storeSubject = Engine_Api::_()->getItem('sitestore_store', $subject->store_id);
                $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'comment');
                if (empty($isManageAdmin))
                    $isAllowedToView = true;
            } else if (!$subject->authorization()->isAllowed($viewer, 'comment')) {
                $isAllowedToView = true;
            }
            // Not authorized to view feeds.
            if (!empty($isAllowedToView))
                $this->respondWithError('unauthorized');
        }
        // Start posting stories in activity feed.
        $postData = $_REQUEST;
        $post_attach = $this->getRequestParam('post_attach', 0);
        if (!isset($postData['body']) && empty($postData['body']) && ($post_attach != 1))
            $this->respondWithError('validation_fail');
        $body = @$postData['body'];
        $postData['body'] = $body = str_replace("%23", '#', $body);

        $privacy = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.content', 'everyone');
        $elementView = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.get.element.view', 0);
        if (isset($postData['auth_view']))
            $privacy = @$postData['auth_view'];
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');

        $body = htmlentities($body, ENT_QUOTES, 'UTF-8');

        $category_id = 0;
        if (isset($postData['category_id']))
            $category_id = @$postData['category_id'];
        $postData['body'] = $body;
        // set up action variable
        $action = null;
        // Process
        $db = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getAdapter();
        $db->beginTransaction();
        try {
            //work for attaching contant with status starts
            if (isset($post_attach) && ($post_attach == 1)) {
                $type = $_POST['type'];
                //to attach link
                if ($type == 'link') {
                    // clean URL for html code
                    $uri = trim(strip_tags($_POST['uri']));
                    if (empty($uri))
                        $this->respondWithValidationError('validation_fail', "URI is required");
                    $info = parse_url($uri);
                    // Process
                    $viewer = Engine_Api::_()->user()->getViewer();
                    // Use viewer as subject if no subject
                    if (null === $subject) {
                        $subject = $viewer;
                    }

                    try {
                        $client = new Zend_Http_Client($uri, array(
                            'maxredirects' => 2,
                            'timeout' => 10,
                        ));
                    } catch (Exception $e) {
                        $this->respondWithError('invalid_url');
                    }
                    // Try to mimic the requesting user's UA
                    $client->setHeaders(array(
                        'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                        'X-Powered-By' => 'Zend Framework'
                    ));
                    $response = $client->request();
                    $link = $this->_getUrlInfo($uri, $response);

                    if (isset($link) && !empty($link)) {
                        $table = Engine_Api::_()->getDbtable('links', 'core');
                        $db = $table->getAdapter();
                        $db->beginTransaction();
                        try {
                            //link creation
                            $attachment = Engine_Api::_()->getApi('links', 'core')->createLink($viewer, $link);
                            $attachment->uri = $link['url'];
                            $attachment->save();
                            $db->commit();
                        } catch (Exception $e) {
                            throw $e;
                            $this->respondWithValidationError('internal_server_error', $e->getMessage());
                        }
                    } else
                        $this->respondWithValidationError('internal_server_error');
                } else if ($type == 'video' &&
                        isset($_POST['video_id']) &&
                        !empty($_POST['video_id'])) {
                    $attachmentData['video_id'] = $_POST['video_id'];
                    $video = Engine_Api::_()->getItem('video', $_POST['video_id']);
                    if (isset($video) && !empty($video)) {
                        $attachmentData['title'] = $video->title;
                        $attachmentData['description'] = $video->description;
                        $plugin = Engine_Api::_()->loadClass('Video_Plugin_Composer');
                        $method = 'onAttachVideo';
                        $attachment = $plugin->$method($attachmentData);
                    }
                } else if ($type == 'music' &&
                        isset($_POST['song_id']) &&
                        !empty($_POST['song_id'])) {
                    $plugin = Engine_Api::_()->loadClass('Music_Plugin_Composer');
                    $method = 'onAttachMusic';
                    $attachmentData['song_id'] = $_POST['song_id'];
                    $song = Engine_Api::_()->getItem('music_playlist_song', $_POST['song_id']);
                    if (isset($song) && !empty($song)) {
                        $attachmentData['title'] = $song->title;
                        $attachment = $plugin->$method($attachmentData);
                    }
                }
            }

            $photoCount = count($_FILES);
// Try attachment getting stuff
            if (!empty($_FILES['photo']) && $photoCount == 1) {
                $table = Engine_Api::_()->getDbtable('albums', 'album');
                $type = $this->getRequestParam('image_type', 'wall');
                $album = $table->getSpecialAlbum($viewer, $type);
                $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
                $photo = $photoTable->createRow();
                $photo->owner_type = 'user';
                $photo->owner_id = $viewer->getIdentity();
                $photo->save();
// Set the photo
                $photo = $this->_setPhoto($_FILES['photo'], $photo);
                $photo->order = $photo->photo_id;
                $photo->album_id = $album->album_id;
                $photo->save();
                if (!$album->photo_id) {
                    $album->photo_id = $photo->getIdentity();
                    $album->save();
                }
                if ($type != 'message') {
// Authorizations
                    $auth = Engine_Api::_()->authorization()->context;
                    $auth->setAllowed($photo, 'everyone', 'view', true);
                    $auth->setAllowed($photo, 'everyone', 'comment', true);
                }
                $attachment = $photo;
            } else if (!empty($_FILES) && $photoCount > 1) {
                $photo_ids = array();
                $table = Engine_Api::_()->getDbtable('albums', 'album');
                $type = $this->getRequestParam('image_type', 'wall');
                $album = $table->getSpecialAlbum($viewer, $type);
                $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
                foreach ($_FILES as $wallPhoto) {
                    $photo = $photoTable->createRow();
                    $photo->owner_type = 'user';
                    $photo->owner_id = $viewer->getIdentity();
                    $photo->save();
// Set the photo
                    $photo = $this->_setPhoto($wallPhoto, $photo);
                    $photo->order = $photo->photo_id;
                    $photo->album_id = $album->album_id;
                    $photo->save();
                    if (!$album->photo_id) {
                        $album->photo_id = $photo->getIdentity();
                        $album->save();
                    }
                    if ($type != 'message') {
// Authorizations
                        $auth = Engine_Api::_()->authorization()->context;
                        $auth->setAllowed($photo, 'everyone', 'view', true);
                        $auth->setAllowed($photo, 'everyone', 'comment', true);
                    }
                    $plugin = Engine_Api::_()->loadClass('Album_Plugin_Composer');
                    $method = 'onAttachPhoto';
                    $attachmentData['photo_id'] = $photo->photo_id;
                    $photo_ids[] = $photo->photo_id;
                    $attachmentData['actionBody'] = '';
                    $attachment = $plugin->$method($attachmentData);
                }
                $attachmentData['type'] = $this->getRequestParam('type');
            }
            $body = preg_replace('/<br[^<>]*>/', "\n", $body);
//      $web_values = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.fb.twitter', 0);
            // Currently we are not working for Facebook and Twitter.
            $currentcontent_type = 1;
//      if ( isset($postData['activity_type']) )
//        $currentcontent_type = $postData['activity_type'];
            if (($currentcontent_type == 1)) {
                $showPrivacyDropdown = in_array('userprivacy', Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy")));
                if ($viewer->isSelf($subject) && $showPrivacyDropdown) {
                    Engine_Api::_()->getDbtable('userSettings', 'seaocore')->setSetting($viewer, "aaf_post_privacy", $privacy);
                } elseif (!$viewer->isSelf($subject)) {
                    $privacy = null;
                }
                $activityTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');
                if (!$attachment && $viewer->isSelf($subject)) {
                    $type = 'status';
                    if ($body != '') {
                        $viewer->status = $body;
                        $viewer->status_date = date('Y-m-d H:i:s');
                        $viewer->save();
                        $viewer->status()->setStatus($body);
                    }
                    if (isset($postData['composer']) && !empty($postData['composer'])) {
                        if ($body != '')
                            $type = 'sitetagcheckin_status';
                        else
                            $type = 'sitetagcheckin_checkin';
                    }
                    $action = $activityTable->addActivity($viewer, $subject, $type, $body, $privacy, array('aaf_post_category_id' => $category_id));
                } else { // General post
                    $type = 'post';
                    if (isset($postData['composer']['checkin']) && !empty($postData['composer']['checkin'])) {
                        $type = 'sitetagcheckin_post';
                    }
                    if ($viewer->isSelf($subject)) {
                        $type = 'post_self';
                        if (isset($postData['composer']['checkin']) && !empty($postData['composer']['checkin'])) {
                            $type = 'sitetagcheckin_post_self';
                        }
                        if ($type == 'post_self') {
                            $attachment_media_type = $attachment->getMediaType();
                            if ($attachment_media_type == 'image') {
                                $attachment_media_type = 'photo';
                            } else if ($attachment_media_type == 'item') {
                                $attachment_type = $attachment->getType();
                                if (strpos($attachment_type, 'music') !== false || strpos($attachment_type, 'song') !== false) {
                                    $attachment_media_type = 'music';
                                }
                            }
                            $tempType = $type . "_" . $attachment_media_type;
                            $typeInfo = Engine_Api::_()->getDbtable('actions', 'activity')->getActionType($tempType);
                            if ($typeInfo && $typeInfo->enabled) {
                                $type = $tempType;
                            }
                        }
                    } else {
                        $birthDayPluginEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('birthday');
                        if ($subject->getType() == 'user' && $birthDayPluginEnable) {
                            $typeInfo = $activityTable->getActionType("birthday_post");
                            if ($typeInfo && $typeInfo->enabled) {
                                $birthdayMemberIds = Engine_Api::_()->getApi('birthday', 'advancedactivity')->getMembersBirthdaysInRange(array('range' => 0));
                                if (!empty($birthdayMemberIds) && in_array($subject->getIdentity(), $birthdayMemberIds)) {
                                    $type = 'birthday_post';
                                }
                            }
                        }
                    }
                    // Add notification for <del>owner</del> user
                    $subjectOwner = $subject->getOwner();
                    if (!$viewer->isSelf($subject) &&
                            $subject instanceof User_Model_User) {
                        $notificationType = 'post_' . $subject->getType();
                        Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
                            'url' => $subject->getHref(),
                        ));
                    }
                    // Add activity
                    if ($subject->getType() == "sitepage_page") {
                        $activityFeedType = null;
                        if (Engine_Api::_()->sitepage()->isPageOwner($subject) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
                            $activityFeedType = 'sitepage_post_self';
                        elseif ($subject->all_post || Engine_Api::_()->sitepage()->isPageOwner($subject))
                            $activityFeedType = 'sitepage_post';
                        if ($activityFeedType) {
                            $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null);
                            Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
                        }
                    } else if ($subject->getType() == "sitebusiness_business") {
                        $activityFeedType = null;
                        if (Engine_Api::_()->sitebusiness()->isBusinessOwner($subject) && Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable())
                            $activityFeedType = 'sitebusiness_post_self';
                        elseif ($subject->all_post || Engine_Api::_()->sitebusiness()->isBusinessOwner($subject))
                            $activityFeedType = 'sitebusiness_post';
                        if ($activityFeedType) {
                            $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null);
                            Engine_Api::_()->getApi('subCore', 'sitebusiness')->deleteFeedStream($action);
                        }
                    } elseif ($subject->getType() == "sitegroup_group") {
                        $activityFeedType = null;
                        if (Engine_Api::_()->sitegroup()->isGroupOwner($subject) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
                            $activityFeedType = 'sitegroup_post_self';
                        elseif ($subject->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($subject))
                            $activityFeedType = 'sitegroup_post';
                        if ($activityFeedType) {
                            $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null);
                            Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
                        }
                    } elseif ($subject->getType() == "sitestore_store") {
                        $activityFeedType = null;
                        if (Engine_Api::_()->sitestore()->isStoreOwner($subject) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
                            $activityFeedType = 'sitestore_post_self';
                        elseif ($subject->all_post || Engine_Api::_()->sitestore()->isStoreOwner($subject))
                            $activityFeedType = 'sitestore_post';
                        if ($activityFeedType) {
                            $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null);
                            Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
                        }
                    } elseif ($subject->getType() == "siteevent_event") {
                        $activityFeedType = Engine_Api::_()->siteevent()->getActivtyFeedType($subject, 'siteevent_post');
//             $activityFeedType = null;
//             if (Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($subject))
//               $activityFeedType = 'sitestore_post_self';
//             elseif ($subject->all_post || Engine_Api::_()->sitestore()->isStoreOwner($subject))
//               $activityFeedType = 'sitestore_post';
// 
                        $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null);
//             if ($activityFeedType) {
//               $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null);
//              // Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
//             }
                    } else {
                        $action = $activityTable->addActivity($viewer, $subject, $type, $body, $privacy, array('aaf_post_category_id' => $category_id));
                    }
                    // Try to attach if necessary
                    if ($action && $attachment) {
                        // Item Privacy Work Start
                        if (!empty($privacy)) {
                            if (!in_array($privacy, array('everyone', 'networks', 'friends', 'onlyme'))) {
                                if (Engine_Api::_()->advancedactivity()->isNetworkBasePrivacy($privacy)) {
                                    $privacy = 'networks';
                                } else {
                                    $privacy = 'onlyme';
                                }
                            }
                            Engine_Api::_()->advancedactivity()->editContentPrivacy($attachment, $viewer, $privacy);
                        }
                        $count = 0;
                        if (($attachmentData['type'] == 'photo') && $photoCount > 1) {
                            foreach ($photo_ids as $photo_id) {
                                $photo = Engine_Api::_()->getItem("album_photo", $photo_id);
                                if ($action instanceof Activity_Model_Action) {
                                    $activityTable->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                                }
                                $count++;
                            }
                        } else {
                            $activityTable->attachActivity($action, $attachment);
                        }
                    }
                }

                // Decode the checkin string to array
                if (isset($postData['composer']) && !is_array($postData['composer']['checkin']))
                    $postData['composer'] = Zend_Json::decode($postData['composer']);


                if (isset($postData['composer']['checkin']) && !empty($postData['composer']['checkin'])) {
                    // In case: If using the Google Location Api at client side then set the params following.
                    $locationLibrary = $this->getRequestParam('locationLibrary', 'server');
                    if ($locationLibrary == 'client') {
                        if (!isset($postData['composer']['checkin']['resource_guid']))
                            $postData['composer']['checkin']['resource_guid'] = 0;
                        if (!isset($postData['composer']['checkin']['id']))
                            $postData['composer']['checkin']['id'] = 'sitetagcheckin_0';
                        if (!isset($postData['composer']['checkin']['type']))
                            $postData['composer']['checkin']['type'] = 'place';
                        if (!isset($postData['composer']['checkin']['prefixadd']))
                            $postData['composer']['checkin']['prefixadd'] = 'in';
                        if (!isset($postData['composer']['checkin']['photo']))
                            $postData['composer']['checkin']['photo'] = '<img class="thumb_icon item_photo_user" alt="" src="application/modules/Sitetagcheckin/externals/images/map_icon.png">';
                    }

                    $str = '';
                    foreach ($postData['composer']['checkin'] as $key => $value) {
                        $str .= $key . '=' . $value . '&';
                    }
                    $postData['composer']['checkin'] = rtrim($str, '&');
                    $composerDatas = $postData['composer'];
                    if ($action && !empty($composerDatas)) {
                        foreach ($composerDatas as $composerDataType => $composerDataValue) {
                            if (empty($composerDataValue))
                                continue;
                            $data['composer'][$composerDataType]['plugin'] = array(
                                'script' => array('_composeCheckin.tpl', 'sitetagcheckin'),
                                'plugin' => 'Sitetagcheckin_Plugin_Composer'
                            );
                            // @Todo: NEED TO CHECK
                            if (isset($data['composer'][$composerDataType]['plugin']) && !empty($data['composer'][$composerDataType]['plugin'])) {
                                $pluginClass = $data['composer'][$composerDataType]['plugin'];
                                Engine_Api::_()->getApi('Siteapi_Core', 'sitetagcheckin')->onAAFComposerCheckin(array($composerDataType => $composerDataValue), array('action' => $action));
                            }
                        }
                        //START SITETAGCHECKIN CODE
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin') && isset($postData['toValues']) && !empty($postData['toValues'])) {
                            $apiSitetagCheckin = Engine_Api::_()->sitetagcheckin();
                            $users = array_values(array_unique(explode(",", $postData['toValues'])));
                            $actionParams = (array) $action->params;
                            if (isset($actionParams['checkin'])) {
                                foreach (Engine_Api::_()->getItemMulti('user', $users) as $tag) {
                                    $apiSitetagCheckin->saveCheckin($actionParams['checkin'], $action, $actionParams, $tag->user_id);
                                }
                            }
                        }
                        //END SITETAGCHECKIN CODE
                    }
                }
            }
            // Start the work for tagging
            if ($action && isset($postData['toValues']) && !empty($postData['toValues'])) {
                $actionTag = new Engine_ProxyObject($action, Engine_Api::_()->getDbtable('tags', 'core'));
                $users = array_values(array_unique(explode(",", $postData['toValues'])));
                $params = (array) $action->params;
                foreach (Engine_Api::_()->getItemMulti('user', $users) as $tag) {
                    $actionTag->addTagMap($viewer, $tag, null);
                    // Add notification
                    $type_name = str_replace('_', ' ', 'post');
                    if (is_array($type_name)) {
                        $type_name = $type_name[0];
                    } else {
                        $type_name = 'post';
                    }
                    if (!(is_array($params) && isset($params['checkin']))) {
                        Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification(
                                $tag, $viewer, $action, 'tagged', array(
                            'object_type_name' => $type_name,
                            'label' => $type_name,
                                )
                        );
                    } else {
                        //GET LABEL
                        $label = $params['checkin']['label'];
                        $checkin_resource_guid = $params['checkin']['resource_guid'];
                        //MAKE LOCATION LINK
                        if (isset($checkin_resource_guid) && empty($checkin_resource_guid)) {
                            $locationLink = '<a href="https://maps.google.com/?q=' . urlencode($label) . '" target="_blank">' . $label . '</a>';
                        } else {
                            $pageItem = Engine_Api::_()->getItemByGuid($checkin_resource_guid);
                            $pageLink = $pageItem->getHref();
                            $pageTitle = $pageItem->getTitle();
                            $locationLink = "<a href='$pageLink'>$pageTitle</a>";
                        }
                        //SEND NOTIFICATION
                        Engine_Api::_()->getApi('Siteapi_Core', 'activity')->addNotification($tag, $viewer, $action, "sitetagcheckin_tagged", array("location" => $locationLink, "label" => $type_name));
                    }
                }
            }
            $publishMessage = html_entity_decode($body);
            $publishUrl = null;
            $publishName = null;
            $publishDesc = null;
            $publishPicUrl = null;
            // Add attachment
            if ($attachment) {
                try {
                    $publishUrl = $attachment->getHref();
                } catch (Exception $e) {
                    //Blank Exception
                }
                $publishName = $attachment->getTitle();
                $publishDesc = $attachment->getDescription();
                if (empty($publishName)) {
                    $publishName = ucwords($attachment->getShortType());
                }
                if (($tmpPicUrl = $attachment->getPhotoUrl())) {
                    $publishPicUrl = $tmpPicUrl;
                }
                // prevents OAuthException: (#100) FBCDN image is not allowed in stream
                if ($publishPicUrl &&
                        preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
                    $publishPicUrl = null;
                }
            } else {
                $publishUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
            }
            // Check to ensure proto/host
            if ($publishUrl &&
                    false === stripos($publishUrl, 'http://') &&
                    false === stripos($publishUrl, 'https://')) {
                $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
            }
            if ($publishPicUrl &&
                    false === stripos($publishPicUrl, 'http://') &&
                    false === stripos($publishPicUrl, 'https://')) {
                $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
            }
            // Add site title
            if ($publishName) {
                $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                        . ": " . $publishName;
            } else {
                $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
            }
            if (isset($postData['composer']['checkin']) && !empty($postData['composer']['checkin'])) {
                $checkinArray = array();
                parse_str($postData['composer']['checkin'], $checkinArray);
                if (!empty($publishMessage))
                    $publishMessage = $publishMessage . ' - ' . $this->translate('at') . ' ' . $checkinArray['label'];
                else {
                    $publishMessage = '- ' . $this->translate('was at') . ' ' . $checkinArray['label'];
                }
            }
            // Publish to facebook, if checked & enabled
//      if ((($currentcontent_type == 3) || isset($_REQUEST['post_to_facebook']))) {
//        try {
//
//          $session = new Zend_Session_Namespace();
//          $facebookApi = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
//          if ($facebookApi && Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebookApi)) {
//            //ADD CHECKIN LOCATION TEXT ALSO.IF CHECKED IN.
//            
//            $fb_data = array(
//                'message' => strip_tags($publishMessage),
//            );
//            if ($publishUrl) {
//              if (isset($_REQUEST['attachment'])) {
//                $fb_data['link'] = $publishUrl;
//              }
////              if ($attachment && $currentcontent_type == 3) {
////                $fb_data['link'] = $attachment->uri;
////              }
//            }
//            if ($publishName) {
//              $fb_data['name'] = $publishName;
//            }
//            if ($publishDesc) {
//              $fb_data['description'] = $publishDesc;
//            }
//            if ($publishPicUrl) {
//              $fb_data['picture'] = $publishPicUrl;
//            }
//            if (isset($_REQUEST['attachment']) && $_REQUEST['attachment']['type'] == 'music') {
//
//              $file = Engine_Api::_()->getItem('storage_file', $attachment->file_id);
////              $fb_data['source'] = $this->getHost . $this->view->seaddonsBaseUrl() . '/' . $file->storage_path;
//              $fb_data['type'] = 'mp3';
//              $fb_data['picture'] = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/application/modules/Advancedactivity/externals/images/music-button.png';
//              ;
//            }
//
//
//            if (isset($fb_data['link']) && !empty($fb_data['link'])) {
//              $appkey = Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.apikey');
//              $appsecret = Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.secretkey');
//              if (!empty($appkey) && !empty($appsecret)) {
//                $shortURL = Engine_Api::_()->getApi('Bitly', 'seaocore')->get_bitly_short_url($fb_data['link'], $appkey, $appsecret, $format = 'txt');
//                $fb_data['link'] = $shortURL;
//              }
//            }
//
//            $subjectPostFBArray = array('sitepage_page', 'sitebusiness_business', 'sitegroup_group', 'sitestore_store');
//            // IF SUBJECT IS AVAILABLE AS WELL AS IS ONE OF THE ABOVE
//            if ($subject && in_array($subject->getType(), $subjectPostFBArray)) {
//              $publish_fb_array = array('0' => 1, '1' => 2);
//              $fb_publish = Engine_Api::_()->getApi('settings', 'core')->getSetting(strtolower($subject->getModuleName()) . '.publish.facebook', serialize($publish_fb_array));
//              if (!empty($fb_publish) && !is_array($fb_publish))
//                $fb_publish = unserialize($fb_publish);
//              if (((isset($_REQUEST['post_to_facebook_profile']) && $_REQUEST['post_to_facebook_profile'] == 'true') || (!isset($_REQUEST['post_to_facebook_profile']) && !empty($fb_publish) && $fb_publish[(count($fb_publish) - 1)] == 2))) {
//                $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
//              }
//            }
//            else
//              $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
//            
//            if ($subject && isset($subject->fbpage_url) && !empty($subject->fbpage_url)) {
//              //explode the subject type
//              $subject_explode = explode("_", $subject->getType());
//              $subjectFbPostSettingVar = $subject_explode[0] . '.post' . $subject_explode[1];
//              //EXTRACTING THE PAGE ID FROM THE PAGE URL.
//              $url_expload = explode("?", $subject->fbpage_url);
//              $url_expload = explode("/", $url_expload[0]);
//              $count = count($url_expload);
//              $page_id_string = '';
//              for ($i = $count - 1; $i >= 0; $i--) {
//
//                if (!empty($url_expload[$i]) && empty($page_id_string))
//                  $page_id_string = $url_expload[$i];
//                if (is_numeric($url_expload[$i])) {
//                  $page_id = $url_expload[$i];
//                  break;
//                }
//              }
//              if (empty($page_id))
//                $page_id = $page_id_string;
//
//              //$manages_pages = $facebookApi->api('/me/accounts', 'GET');
//              //NOW IF THE USER WHO IS COMENTING IS OWNER OF THIS FACEBOOK PAGE THEN GETTING THE PAGE ACCESS TOKEN TO WITH THIS SITE PAGE IS INTEGRATED.
//
//              if (in_array($subject->getType(), $subjectPostFBArray) && (isset($_REQUEST['post_to_facebook_page']) && $_REQUEST['post_to_facebook_page'] == 'true') && Engine_Api::_()->getApi('settings', 'core')->getSetting($subjectFbPostSettingVar, 1) && !empty($fb_publish) && $fb_publish[0] == 1) {
//                if ($subject->getType() != 'sitegroup_group') {
//                  $pageinfo = $facebookApi->api('/' . $page_id . '?fields=access_token', 'GET');
//                  if (isset($pageinfo['access_token']))
//                    $fb_data['access_token'] = $pageinfo['access_token'];                  
//                } else {
//									if(!is_numeric($page_id)) {
//									 
//                   if(isset($subject->fbgroup_id) && !empty($subject->fbgroup_id))
//                     $page_id = $subject->fbgroup_id;
//                   else if(isset($subject->fbpage_title) && !empty($subject->fbpage_title)) {
//                      //GET THE NUMERIC ID OF GROUP.
//                     
//                      $page_id = trim($subject->fbpage_title);
//                      $group_info = $facebookApi->api('/search?q='.urlencode($page_id).'&type=group', 'GET');
//                      if(!empty($group_info) && isset($group_info['data']) && isset($group_info['data']['0'])) {
//                       $page_id = $group_info['data']['0']['id'];
//                      }
//                    }
//									}
//                }
//                $fb_data['message'] = $fb_data['message'] . '
//                  ' . $this->view->translate('Posted via') . ' ' . (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
//                $res = $facebookApi->api('/' . $page_id . '/feed', 'POST', $fb_data);
//              }
//            }
//
//            if ($currentcontent_type == 3) {
//              $last_fbfeedid = $_REQUEST['fbmin_id'];
//
//              $feed_stream = $this->view->content()->renderWidget("advancedactivity.advancedactivityfacebook-userfeed", array('getUpdate' => true, 'is_ajax' => 1, 'minid' => $last_fbfeedid, 'currentaction' => 'post_new'));
//              echo Zend_Json::encode(array('status' => true, 'post_fail' => 0, 'feed_stream' => $feed_stream));
//              exit();
//            }
//          }
//        } catch (Exception $e) {
//          // Silence
//        }
//      } // end Facebook
//      // Publish to twitter, if checked & enabled
//      if ((($currentcontent_type == 2) || isset($_REQUEST['post_to_twitter']))) {
//        try {
//          $Api_twitter = Engine_Api::_()->getApi('twitter_Api', 'seaocore');
//          if ($Api_twitter->isConnected()) {
//            // @todo truncation?
//            // @todo attachment
//            $twitterOauth = $twitter = $Api_twitter->getApi();
//            $login = Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.apikey');
//            $appkey = Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.secretkey');
//
//
//            //TWITTER ONLY ACCEPT 140 CHARACTERS MAX..
//            //IF BITLY IS CONFIGURED ON THE SITE..
//            if (!empty($login) && !empty($appkey)) {
//              if (strlen(html_entity_decode($_REQUEST['body'])) > 140 || isset($_REQUEST['attachment'])) {
//                if (isset($_REQUEST['attachment'])) {
//                  $shortURL = Engine_Api::_()->getApi('Bitly', 'seaocore')->get_bitly_short_url((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $attachment->getHref(), $login, $appkey, $format = 'txt');
//                  $BitlayLength = strlen($shortURL);
//                } else {
//                  $BitlayLength = 0;
//                  $shortURL = '';
//                }
//                $twitterFeed = substr(html_entity_decode($_REQUEST['body']), 0, (140 - ($BitlayLength + 1))) . ' ' . $shortURL;
//              }
//              else
//                $twitterFeed = html_entity_decode($_REQUEST['body']);
//            }
//
//           else {
//              $twitterFeed = substr(html_entity_decode($_REQUEST['body']), 0, 136) . ' ...';
//
//            }
//            if((empty($twitterFeed) || !isset($_REQUEST['attachment'])) && !empty($publishMessage))
//              $twitterFeed = substr($publishMessage, 0, 136) . ' ...';
//             
//            $lastfeedobject = $twitterOauth->post(
//                    'statuses/update', array('status' => strip_tags($twitterFeed))
//            );
//
//            if(isset($lastfeedobject->errors) && $lastfeedobject->errors[0]->code == 186) {
//                 $twitterFeed = substr(html_entity_decode($_REQUEST['body']), 0, 127) . ' ...';
//                 $lastfeedobject = $twitterOauth->post(
//                    'statuses/update', array('status' => strip_tags($twitterFeed))
//            );
//              }
//
//
//            if ($currentcontent_type == 2) {
//
//              $feed_stream = $this->view->content()->renderWidget("advancedactivity.advancedactivitytwitter-userfeed", array('getUpdate' => true, 'currentaction' => 'post_new', 'feedobj' => $lastfeedobject));
//              echo Zend_Json::encode(array('status' => true, 'post_fail' => 0, 'feed_stream' => $feed_stream));
//              exit();
//            }
//          }
//        } catch (Exception $e) { 
//          // Silence
//        }
//      }
//
//      // Publish to linkedin, if checked & enabled
//      if ((($currentcontent_type == 5) || isset($_REQUEST['post_to_linkedin']))) {
//
//        try {
//          $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
//          $OBJ_linkedin = $Api_linkedin->getApi();
//
//
//
//          // $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
//          if ($OBJ_linkedin) {
//            if ($attachment):
//              if ($publishUrl) {
//                $content['submitted-url'] = $publishUrl;
//              }
////              if ($currentcontent_type == 5) {
////                $content['submitted-url'] = $attachment->getHref();
////              }
//              if ($publishName && $publishUrl) {
//                $content['title'] = $publishName;
//              }
//              if ($publishDesc) {
//                $content['description'] = $publishDesc;
//              }
//              if ($publishPicUrl) {
//                $content['submitted-image-url'] = $publishPicUrl;
//              }
//            endif;
//            $content['comment'] = strip_tags($publishMessage);
//
//            $lastfeedobject = $OBJ_linkedin->share('new', $content);
//
//            if ($currentcontent_type == 5) {
//              $last_linkedinfeedid = $_REQUEST['linkedinmin_id'];
//
//              $feed_stream = $this->view->content()->renderWidget("advancedactivity.advancedactivitylinkedin-userfeed", array('getUpdate' => true, 'currentaction' => 'post_new', 'minid' => $last_linkedinfeedid, 'is_ajax' => 1));
//              echo Zend_Json::encode(array('status' => true, 'post_fail' => 0, 'feed_stream' => $feed_stream));
//              exit();
//            }
//          }
//        } catch (Exception $e) {
//          // Silence
//        }
//      }
//      if (empty($is_ajax) && !Engine_Api::_()->seaocore()->isLessThan420ActivityModule()) {
//        // Publish to janrain
//        if (//$this->getRequestParam('post_to_janrain', false) &&
//                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
//          try {
//            $session = new Zend_Session_Namespace('JanrainActivity');
//            $session->unsetAll();
//
//            $session->message = $publishMessage;
//            $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
//            $session->name = $publishName;
//            $session->desc = $publishDesc;
//            $session->picture = $publishPicUrl;
//          } catch (Exception $e) {
//            // Silence
//          }
//        }
//      }
            $db->commit();
            $this->successResponseNoContent('created', 'feed_index_homefeed');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /**
     * Attach Link - return informtion about the link being attached.
     *
     * @return array
     */
    public function attachLinkAction() {
        $this->validateRequestMethod('POST');
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams('core_link', null, 'create')->isValid())
            return;
        // clean URL for html code
        $uri = trim(strip_tags($_POST['uri']));

        if (empty($uri))
            $this->respondWithValidationError('validation_fail', "URI is required");
        $info = parse_url($uri);

        // Process
        $viewer = Engine_Api::_()->user()->getViewer();
        // Use viewer as subject if no subject

        if (null === $subject) {
            $subject = $viewer;
        }

        try {
            $client = new Zend_Http_Client($uri, array(
                'maxredirects' => 2,
                'timeout' => 10,
            ));
        } catch (Exception $ex) {
            $this->respondWithError('invalid_url');
        }



        // Try to mimic the requesting user's UA
        $client->setHeaders(array(
            'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'X-Powered-By' => 'Zend Framework'
        ));
        $response = $client->request();

        $link = $this->_getUrlInfo($uri, $response);

        $this->respondWithSuccess($link);
    }

    /**
     * Feed Menus - Hide OR Report Feed OR Hide all by ... (Hide the story from activity feed)
     *
     * @return array
     */
    public function hideItemAction() {
// Validate request methods
        $this->validateRequestMethod('POST');
// Throw error for logged-out user.
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');
        if (null == ($type = $this->getRequestParam('type', null)) ||
                null == ($id = $this->getRequestParam('id', null))) {
            $this->respondWithValidationError('parameter_missing', 'type OR id');
        }
        try {
            Engine_Api::_()->getDbtable('hide', 'advancedactivity')->insert(array(
                'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                'hide_resource_type' => $type,
                'hide_resource_id' => $id
            ));
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
// Start to make an array for further requests.
        $hideMenu["undo"]["name"] = "undo";
        $hideMenu["undo"]["label"] = $this->translate('This story is now hidden from your Activity Feed.');
        $hideMenu["undo"]["url"] = "advancedactivity/feeds/un-hide-item";
        $hideMenu["undo"]['urlParams'] = array(
            "type" => $type,
            "id" => $id
        );
        if (!Engine_Api::_()->core()->hasSubject() && ($type == "activity_action")) {
            $action = Engine_Api::_()->getItem('activity_action', $id);
            $isHide = $this->getRequestParam('hide_report', null);
            if (!($isHide)) {
                $item = (isset($action->getTypeInfo()->is_object_thumb) && !empty($action->getTypeInfo()->is_object_thumb)) ? $action->getObject() : $action->getSubject();
                if (!empty($item)) {
//                    $hideMenu["hide_all"]["name"] = "hide_all";
//                    $hideMenu["hide_all"]["label"] = $this->translate('Hide all by') . ' ' . $item->getTitle();
//                    $hideMenu["hide_all"]["url"] = "advancedactivity/feeds/hide-item";
//                    $hideMenu["hide_all"]['urlParams'] = array(
//                        "type" => $item->getType(),
//                        "id" => $item->getIdentity()
//                    );
                } else if (!Engine_Api::_()->core()->hasSubject() && ($type == "user")) {
                    $user = Engine_Api::_()->getItem('user', $id);
                    $hideMenu["undo"]["label"] = $this->translate('Stories from ' . $user->getTitle() . ' are hidden now and will not appear in your Activity Feed anymore.');
                }
            } else {
                $hideMenu["hide_all"]["name"] = 'report';
                $hideMenu["hide_all"]["label"] = $this->translate('file a report');
                $hideMenu["hide_all"]["url"] = 'report/create/subject/' . $action->getGuid();
                $hideMenu["hide_all"]['urlParams'] = array(
                    "type" => $action->getType(),
                    "id" => $action->getIdentity()
                );
            }
        }
        $this->respondWithSuccess($hideMenu);
    }

    /**
     * Feed Menus - Undo (Unhide the story from activity feed)
     *
     * @return array
     */
    public function unHideItemAction() {
// Validate request methods
        $this->validateRequestMethod('POST');
// Throw error for logged-out user.
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');
        if (null == ($type = $this->getRequestParam('type', null)) ||
                null == ($id = $this->getRequestParam('id', null))) {
            $this->respondWithValidationError('parameter_missing', 'type OR id');
        }
        try {
            Engine_Api::_()->getDbtable('hide', 'advancedactivity')->delete(array('user_id = ?' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                'hide_resource_type =? ' => $type,
                'hide_resource_id =?' => $id));
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
        $this->successResponseNoContent('no_content', 'feed_index_homefeed');
    }

    /**
     * Handles HTTP request to get an activity feed item's comment information.
     *
     * @return array
     */
    public function likesCommentsAction() {
// Validate request methods
        $this->validateRequestMethod();
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();

        $action_id = $this->getRequestParam('action_id');
        if (empty($action_id)) {
            $this->respondWithValidationError('parameter_missing', 'action_id');
        }
        $comment_id = $this->getRequestParam('comment_id', null);
        $page = $this->getRequestParam('page', null);
        $limit = $this->getRequestParam('limit', null);
        $viewer = Engine_Api::_()->user()->getViewer();
        $bodyParams = $likeUsersArray = array();
        $action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($action_id);
        if (!empty($comment_id)) {
            $comment = $action->comments()->getComment($comment_id);
            $getAllLikesUsers = $comment->likes()->getAllLikesUsers();
            $likes = $comment->likes()->getLikePaginator();
        } else {
            $getAllLikesUsers = $action->likes()->getAllLikesUsers();
            $likes = $action->likes()->getLikePaginator();
        }
// Likes
        $isLike = Engine_Api::_()->getDbtable('likes', 'activity')->isLike($action, $viewer);
        $viewAllLikes = $this->getRequestParam('viewAllLikes', $this->getRequestParam('view_all_likes', 0));
        if (!empty($viewAllLikes)) {
            foreach ($getAllLikesUsers as $user) {
                $tempUserArray = Engine_Api::_()->getApi('Core', 'siteapi')->validateUserArray($user);
                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($user);
                $tempUserArray = array_merge($tempUserArray, $getContentImages);
                $likeUsersArray[] = $tempUserArray;
            }
            $bodyParams['viewAllLikesBy'] = $likeUsersArray;
        }
        $canComment = $action->authorization()->isAllowed($viewer, 'comment');
        $canDelete = $action->authorization()->isAllowed($viewer, 'edit');
// If has a page, display oldest to newest
        if (null !== $page) {
            $commentSelect = $action->comments()->getCommentSelect();
            $commentSelect->order('comment_id ' . $this->getRequestParam('order', 'ASC'));
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber($page);
            $comments->setItemCountPerPage($limit);
        } else {
// If not has a page, show the
            $commentSelect = $action->comments()->getCommentSelect();
            $commentSelect->order('comment_id DESC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber(1);
            $comments->setItemCountPerPage(4);
        }
// Hide if can't post and no comments
        if (!$canComment && !$canDelete && count($comments) <= 0 && count($likes) <= 0)
            $this->respondWithError('unauthorized');
        $getTotalCommentCount = $comments->getTotalItemCount();
        $viewAllComments = $this->getRequestParam('viewAllComments', $this->getRequestParam('view_all_comments', 0));
        if (!empty($viewAllComments)) {
// Iterate over the comments backwards (or forwards!)
            $comments = $comments->getIterator();
            if ($page) {
                $i = 0;
                $l = count($comments) - 1;
                $d = 1;
                $e = $l + 1;
            } else {
                $i = count($comments) - 1;
                $l = count($comments);
                $d = -1;
                $e = -1;
            }
            for (; $i != $e; $i += $d) {
                $comment = $comments[$i];
                $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
                $commentInfo["action_id"] = $action_id;
                $commentInfo["comment_id"] = $comment->comment_id;
                // Add images
                $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($poster, false, 'author');
                $commentInfo = array_merge($commentInfo, $getContentImages);
                $commentInfo["author_title"] = $poster->getTitle();
                $commentInfo["user_id"] = $poster->getIdentity();
                $commentInfo["comment_body"] = $comment->body;
                $commentInfo["comment_date"] = $comment->creation_date;
                if (Engine_Api::_()->getApi('Siteapi_Feed', 'advancedactivity')->isSitestickerPluginLive()) {
                    if (isset($comment->attachment_type) && !empty($comment->attachment_type) && isset($comment->attachment_id) && !empty($comment->attachment_id) && ($comment->attachment_type == 'sitereaction_sticker' || $comment->attachment_type == 'album_photo')) {
                        $attachment = Engine_Api::_()->getItem($comment->attachment_type, $comment->attachment_id);
                        $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($attachment, false);
                        $commentInfo['attachment'] = $getContentImages;
                        $commentInfo['attachment_type'] = $comment->attachment_type;
                        $commentInfo['attachment_id'] = $comment->attachment_id;
                    }
                }
                if (!empty($canDelete) || $poster->isSelf($viewer)) {
                    $commentInfo["delete"] = array(
                        "name" => "delete",
                        "label" => $this->translate('Delete'),
                        "url" => "comment-delete",
                        'urlParams' => array(
                            "action_id" => $action_id,
                            "subject_type" => $action->getObject()->getType(),
                            "subject_id" => $action->getObject()->getIdentity(),
                            "comment_id" => $comment->comment_id
                        )
                    );
                } else {
                    $commentInfo["delete"] = null;
                }
                if (!empty($canComment)) {
                    $isLiked = $comment->likes()->isLike($viewer);
                    if (empty($isLiked)) {
                        $likeInfo["name"] = "like";
                        $likeInfo["label"] = $this->translate('Like');
                        $likeInfo["url"] = "like";
                        $likeInfo['urlParams'] = array(
                            "action_id" => $action_id,
                            "subject_type" => $action->getObject()->getType(),
                            "subject_id" => $action->getObject()->getIdentity(),
                            "comment_id" => $comment->getIdentity()
                        );
                        $likeInfo["isLike"] = 0;
                    } else {
                        $likeInfo["name"] = "unlike";
                        $likeInfo["label"] = $this->translate('Unlike');
                        $likeInfo["url"] = "unlike";
                        $likeInfo['urlParams'] = array(
                            "action_id" => $action_id,
                            "subject_type" => $action->getObject()->getType(),
                            "subject_id" => $action->getObject()->getIdentity(),
                            "comment_id" => $comment->getIdentity()
                        );
                        $likeInfo["isLike"] = 1;
                    }
                    $commentInfo["like_count"] = $comment->likes()->getLikeCount();
                    $commentInfo["like"] = $likeInfo;
                } else {
                    $commentInfo["like"] = null;
                }
                $allComments[] = $commentInfo;
            }
            $bodyParams['viewAllComments'] = $allComments;
        }
// FOLLOWING ARE THE GENRAL INFORMATION OF THE PLUGIN, WHICH WILL RETURN IN EVERY CALLING.
        $bodyParams['isLike'] = !empty($isLike) ? 1 : 0;
        $bodyParams['canComment'] = $canComment;
        $bodyParams['canDelete'] = $canDelete;
        $bodyParams['getTotalComments'] = $getTotalCommentCount;
        $bodyParams['getTotalLikes'] = $likes->getTotalItemCount();
        $this->respondWithSuccess($bodyParams);
    }

    /**
     * Image url - returns information for image type url.
     *
     * @return array
     */
    protected function _previewImage($uri, Zend_Http_Response $response) {
        $imageCount = 1;
        $image = array();
        $image['images'] = array($uri);
        return $images;
    }

    /**
     * Text url - returns information for text type url.
     *
     * @return array
     */
    protected function _previewText($uri, Zend_Http_Response $response) {
        $body = $response->getBody();
        $text = array();
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
                preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches)) {
            $charset = trim($matches[1]);
        } else {
            $charset = 'UTF-8';
        }
//    if( function_exists('mb_convert_encoding') ) {
//      $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
//    }
        // Reduce whitespace
        $text['body'] = $body = preg_replace('/[\n\r\t\v ]+/', ' ', $body);
        $text['title'] = $title = substr($body, 0, 63);
        $text['desciption'] = $description = substr($body, 0, 255);
        return $text;
    }

    /**
     * Text/html url - returns information for html/text type url.
     *
     * @return array
     */
    protected function _previewHtml($uri, Zend_Http_Response $response) {
        $body = $response->getBody();
        $html = array();
        $body = trim($body);
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
                preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches)) {
            $charset = $charset = trim($matches[1]);
        } else {
            $charset = $charset = 'UTF-8';
        }
        if (function_exists('mb_convert_encoding')) {
            $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
        }
        // Get DOM
        if (class_exists('DOMDocument')) {
            $dom = new Zend_Dom_Query($body);
        } else {
            $dom = null; // Maybe add b/c later
        }
        $title = null;
        if ($dom) {
            $titleList = $dom->query('title');
            if (count($titleList) > 0) {
                $title = trim($titleList->current()->textContent);
                $title = substr($title, 0, 255);
            }
        }
        $html['title'] = $title;
        $description = null;
        if ($dom) {
            $descriptionList = $dom->queryXpath("//meta[@name='description']");
            // Why are they using caps? -_-
            if (count($descriptionList) == 0) {
                $descriptionList = $dom->queryXpath("//meta[@name='Description']");
            }
            if (count($descriptionList) > 0) {
                $description = trim($descriptionList->current()->getAttribute('content'));
                $description = substr($description, 0, 255);
            }
        }

        if (empty($description) && !isset($description) && isset($title) && !empty($title))
            $description = $title;

        $html['description'] = $description;
        $medium = null;
        if ($dom) {
            $mediumList = $dom->queryXpath("//meta[@name='medium']");
            if (count($mediumList) > 0) {
                $medium = $mediumList->current()->getAttribute('content');
            }
        }
        $medium = $medium;
        // Get baseUrl and baseHref to parse . paths
        $baseUrlInfo = parse_url($uri);
        $baseUrl = null;
        $baseHostUrl = null;
        $baseUrlScheme = $baseUrlInfo['scheme'];
        $baseUrlHost = $baseUrlInfo['host'];
        if ($dom) {
            $baseUrlList = $dom->query('base');
            if ($baseUrlList && count($baseUrlList) > 0 && $baseUrlList->current()->getAttribute('href')) {
                $baseUrl = $baseUrlList->current()->getAttribute('href');
                $baseUrlInfo = parse_url($baseUrl);
                if (!isset($baseUrlInfo['scheme']) || empty($baseUrlInfo['scheme'])) {
                    $baseUrlInfo['scheme'] = $baseUrlScheme;
                }
                if (!isset($baseUrlInfo['host']) || empty($baseUrlInfo['host'])) {
                    $baseUrlInfo['host'] = $baseUrlHost;
                }
                $baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
            }
        }
        if (!$baseUrl) {
            $baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
            if (empty($baseUrlInfo['path'])) {
                $baseUrl = $baseHostUrl;
            } else {
                $baseUrl = explode('/', $baseUrlInfo['path']);
                array_pop($baseUrl);
                $baseUrl = join('/', $baseUrl);
                $baseUrl = trim($baseUrl, '/');
                $baseUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/' . $baseUrl . '/';
            }
        }
        $images = array();
        $thumb = null;
        if ($dom) {
            $thumbList = $dom->queryXpath("//link[@rel='image_src']");
            if (count($thumbList) > 0) {
                $thumb = $thumbList->current()->getAttribute('href');
            }
        }

        if ($thumb) {
            $images[] = $thumb;
        }
        if ($dom) {
            $imageQuery = $dom->query('img');
            foreach ($imageQuery as $image) {
                $src = $image->getAttribute('src');
                // Ignore images that don't have a src
                if (!$src || false === ($srcInfo = @parse_url($src))) {
                    continue;
                }
                $ext = ltrim(strrchr($src, '.'), '.');
                // Detect absolute url
                if (strpos($src, '/') === 0) {
                    // If relative to root, add host
                    $src = $baseHostUrl . ltrim($src, '/');
                } else if (strpos($src, './') === 0) {
                    // If relative to current path, add baseUrl
                    $src = $baseUrl . substr($src, 2);
                } else if (!empty($srcInfo['scheme']) && !empty($srcInfo['host'])) {
                    // Contians host and scheme, do nothing
                } else if (empty($srcInfo['scheme']) && empty($srcInfo['host'])) {
                    // if not contains scheme or host, add base
                    $src = $baseUrl . ltrim($src, '/');
                } else if (empty($srcInfo['scheme']) && !empty($srcInfo['host'])) {
                    // if contains host, but not scheme, add scheme?
                    $src = $baseUrlInfo['scheme'] . ltrim($src, '/');
                } else {
                    // Just add base
                    $src = $baseUrl . ltrim($src, '/');
                }
                // Ignore images that don't come from the same domain
                //if( strpos($src, $srcInfo['host']) === false ) {
                // @todo should we do this? disabled for now
                //continue;
                //}
                // Ignore images that don't end in an image extension
                if (!in_array($ext, array('jpg', 'jpeg', 'png'))) {
                    // @todo should we do this? disabled for now
                    continue;
                }
                if (!in_array($src, $images)) {
                    $images[] = $src;

                    if (empty($thumb)) {
                        $thumb = $images[0];
                    }

                    if (@getimagesize($thumb) == false || empty($thumb)) {
                        if (@getimagesize($src) != false)
                            $thumb = $src;
                    }
                }
            }
        }
        // Unique
        $images = array_values(array_unique($images));
        // Truncate if greater than 20
        if (count($images) > 30) {
            array_splice($images, 30, count($images));
        }
        $html['imageCount'] = count($images);
        $html['images'] = $images;
        $html['thumb'] = $thumb;
        return $html;
    }

    /**
     * Info of URL - Common function called to get information about url.
     *
     * @return array
     */
    private function _getUrlInfo($uri, Zend_Http_Response $response) {
        // Get content-type
        list($contentType) = explode(';', $response->getHeader('content-type'));
        $link['contentType'] = $contentType;
        $link['url'] = $uri;
        // Prepare
        $title = null;
        $description = null;
        $thumb = null;
        $imageCount = 0;
        $images = array();

        // Handling based on content-type
        switch (strtolower($contentType)) {
            // Images
            case 'image/gif':
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/tif': // Might not work
            case 'image/xbm':
            case 'image/xpm':
            case 'image/png':
            case 'image/bmp': // Might not work
                $link = array_merge($link, $this->_previewImage($uri, $response));
                break;
            // HTML
            case '':
            case 'text/html':
                $link = array_merge($link, $this->_previewHtml($uri, $response));
                break;
            // Plain text
            case 'text/plain':
                $link = array_merge($link, $this->_previewText($uri, $response));
                break;
            // Unknown
            default:
                break;
        }
        return $link;
    }

    /**
     * Feed Post Menus - Return an array of menus to post activity feed. Which help to findout that which post-menu should be display to not.
     *
     * @return array
     */
    private function _getPostFeedOptions() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
// Throw error for logged-out user.
        if (empty($viewer_id)) {
//      $this->_forward('throw-error', 'feed', 'advancedactivity', array(
//          "error_code" => "unauthorized"
//      ));
            return;
        }
// Get the subject
        if (Engine_Api::_()->core()->hasSubject())
            $subject = Engine_Api::_()->core()->getSubject();
        else
            $subject = Engine_Api::_()->user()->getViewer();
// Check authorization permission
        if (!$subject->authorization()->isAllowed($viewer, 'comment')) {
//      $this->_forward('throw-error', 'feed', 'advancedactivity', array(
//          "error_code" => "unauthorized"
//      ));
            return;
        }


// Make an array for feed post menus.
        $activityPost = array();
        $activityPost['status'] = 1;
        $activityPost['emotions'] = 1;
        $activityPost['withtags'] = 1;
        $composerList = @implode(",", Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.menuoptions', array('photoXXXalbum', 'linkXXXcore', 'musicXXXmusic', 'checkinXXXsitetagcheckin', 'videoXXXvideo')));
        $moduleEnabledByUs = array('album' => 'photo', 'sitetagcheckin' => 'checkin', 'video' => 'video', 'music' => 'music', 'core' => 'link');
        foreach ($moduleEnabledByUs as $modName => $key) {
            $activityPost[$key] = (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($modName) && strstr($composerList, $modName)) ? 1 : 0;
        }
        $activityPost['userprivacy'] = array(
            'everyone' => $this->translate('Everyone'),
            'networks' => $this->translate('Friends & Networks'),
            'friends' => $this->translate('Friends Only'),
            'onlyme' => $this->translate('Only Me')
        );
        return $activityPost;
    }

    private function _getFilterForContentProfile($subject) {
        $finalTabArray = $FilterForContentProfile = array();
        if (($subject->getType() === 'user') || ($subject->getType() === 'sitepage_page' && Engine_Api::_()->sitepage()->isFeedTypePageEnable()) || ($subject->getType() === 'sitebusiness_business' && Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable() || ($subject->getType() === 'sitegroup_group' && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable()) || ($subject->getType() === 'sitestore_store' && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable()))
        ) {
            $setLabelFlag = $FilterForContentProfile["tab_title"] = $subject->getTitle();
        } else {
            if ($subject->getType() === 'siteevent_event') {
                $FilterForContentProfile["tab_title"] = $this->translate('Leaders') . " (" . Engine_Api::_()->seaocore()->seaocoreTruncateText($subject->getTitle(), 15) . ")";
            } else {
                $FilterForContentProfile["tab_title"] = $this->translate('Owner') . " (" . Engine_Api::_()->seaocore()->seaocoreTruncateText($subject->getTitle(), 15) . ")";
            }
        }
        $FilterForContentProfile["tab_title"] = $this->translate('Everyone');
        $FilterForContentProfile["urlParams"] = array(
            "filter_type" => "all",
            "list_id" => 0,
            "isFromTab" => 1,
            "feedOnly" => 1
        );
        $finalTabArray[] = $FilterForContentProfile;

        $FilterForContentProfile["tab_title"] = !empty($setLabelFlag) ? $setLabelFlag : Engine_Api::_()->user()->getViewer()->getTitle();
        $FilterForContentProfile["urlParams"] = array(
            "filter_type" => "owner",
            "list_id" => 0,
            "isFromTab" => 1,
            "feedOnly" => 1
        );
        $finalTabArray[] = $FilterForContentProfile;



        if ($subject->getType() === 'siteevent_event') {
            $FilterForContentProfile["tab_title"] = $this->translate('Guests');
            $FilterForContentProfile["urlParams"] = array(
                "filter_type" => "membership",
                "list_id" => 0,
                "isFromTab" => 1,
                "feedOnly" => 1
            );
            $finalTabArray[] = $FilterForContentProfile;
        } elseif (Engine_Api::_()->user()->getViewer()->getIdentity() && ($subject->getType() != 'user')) {
            $FilterForContentProfile["tab_title"] = $this->translate('Friends');
            $FilterForContentProfile["urlParams"] = array(
                "filter_type" => "membership",
                "list_id" => 0,
                "isFromTab" => 1,
                "feedOnly" => 1
            );
            $finalTabArray[] = $FilterForContentProfile;
        }
        return $finalTabArray;
    }

    /**
     * Set the uploaded photo from activity post.
     *
     * @return object
     */
    private function _setPhoto($photo, $subject) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Group_Model_Exception('invalid argument passed to setPhoto');
        }
        $fileName = $photo['name'];
        $name = basename($file);
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $params = array(
            'parent_type' => $subject->getType(),
            'parent_id' => $subject->getIdentity(),
            'user_id' => $subject->owner_id,
            'name' => $fileName,
        );
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
// Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($mainPath)
                ->destroy();
// Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(140, 160)
                ->write($normalPath)
                ->destroy();
// Store
        try {
            $iMain = $filesTable->createFile($mainPath, $params);
            $iIconNormal = $filesTable->createFile($normalPath, $params);
            $iMain->bridge($iIconNormal, 'thumb.normal');
        } catch (Exception $e) {
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
// Remove temp files
        @unlink($mainPath);
        @unlink($normalPath);
// Update row
        $subject->modified_date = date('Y-m-d H:i:s');
        $subject->file_id = $iMain->file_id;
        $subject->save();
        return $subject;
    }

}
