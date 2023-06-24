<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_Widget_FeedController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $this->view->settingsApi = $settings = Engine_Api::_()->getApi('settings', 'core');
        $subject = null;
        if (Engine_Api::_()->core()->hasSubject()) {
            // Get subject
            $parentSubject = $subject = Engine_Api::_()->core()->getSubject();
            if ($subject->getType() == 'siteevent_event') {
                $parentSubject = Engine_Api::_()->getItem($subject->getParent()->getType(), $subject->getParent()->getIdentity());
            }
            if (!in_array($subject->getType(), array('sitepage_page', 'sitepageevent_event', 'sitegroup_group', 'sitegroupevent_event', 'sitestore_store', 'sitestoreevent_event', 'sitebusiness_business', 'sitebusinessevent_event')) && !in_array($parentSubject->getType(), array('sitepage_page', 'sitegroup_group', 'sitestore_store', 'sitebusiness_business'))) {
                if (!$subject->authorization()->isAllowed($viewer, 'view') && !$parentSubject->authorization()->isAllowed($viewer, 'view')) {
                    return $this->setNoRender();
                }
            } else if (in_array($subject->getType(), array('sitepage_page', 'sitepageevent_event')) || ($subject->getType() == 'sitepage_page')) {
                $pageSubject = $parentSubject;
                if ($subject->getType() == 'sitepageevent_event')
                    $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
                $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'view');
                if (empty($isManageAdmin)) {
                    return $this->setNoRender();
                }
            } else if (in_array($subject->getType(), array('sitebusiness_business', 'sitebusinessevent_event')) || ($subject->getType() == 'sitebusiness_business')) {
                $businessSubject = $parentSubject;
                if ($subject->getType() == 'sitebusinessevent_event')
                    $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
                $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'view');
                if (empty($isManageAdmin)) {
                    return $this->setNoRender();
                }
            } else if (in_array($subject->getType(), array('sitegroup_group', 'sitegroupevent_event')) || ($subject->getType() == 'sitegroup_group')) {
                $groupSubject = $parentSubject;
                if ($subject->getType() == 'sitegroupevent_event')
                    $groupSubject = Engine_Api::_()->getItem('sitegroup_group', $subject->group_id);
                $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($groupSubject, 'view');
                if (empty($isManageAdmin)) {
                    return $this->setNoRender();
                }
            } else if (in_array($subject->getType(), array('sitestore_store', 'sitestoreevent_event')) || ($subject->getType() == 'sitestore_store')) {
                $storeSubject = $parentSubject;
                if ($subject->getType() == 'sitestoreevent_event')
                    $storeSubject = Engine_Api::_()->getItem('sitestore_store', $subject->store_id);
                $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'view');
                if (empty($isManageAdmin)) {
                    return $this->setNoRender();
                }
            }
        }
        $isForCategoryPage = $this->_getParam('isForCategoryPage', false);

        $listLimit = 0;
        $composerLimit = 1;
        $request = Zend_Controller_Front::getInstance()->getRequest();

        // Get some options
        $this->view->homefeed = $homefeed = $request->getParam('homefeed', false);
        $this->view->feedOnly = $feedOnly = $request->getParam('feedOnly', false);
        if (!$this->view->feedOnly)
            $this->view->feedOnly = $feedOnly = $this->_getParam('feedOnly');
        $this->view->onViewPage = $this->_getParam('onViewPage');
        if ($this->view->onViewPage) {
            $feedOnly = 1;
        }
        $this->view->length = $length = $request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
        $this->view->itemActionLimit = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);
        $getComposerValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.composer.value', $listLimit);

        $this->view->curr_url = $curr_url = $request->getRequestUri(); // Return the current URL.
        $actionTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');

        $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
        $this->view->showHashtags = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitehashtag.showHashtags', 1);
        $this->view->hide = $this->_getParam('hide');
        if(Engine_Api::_()->hasModuleBootstrap('sitehashtag')) {
            if(!empty($this->view->hide)) {
                $this->view->updateSettings = 0;
            }
        }
        
              
        $this->view->viewAllLikes = $request->getParam('viewAllLikes', $request->getParam('show_likes', false));
        if (!$this->view->viewAllLikes)
            $this->view->viewAllLikes = $this->_getParam('viewAllLikes');
        $this->view->viewAllComments = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
        if (!$this->view->viewAllComments)
            $this->view->viewAllComments = $this->_getParam('viewAllComments');
        $getListViewValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.list.view.value', $composerLimit);
        $getPublishValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.publish.str.value', $composerLimit);
        $this->view->getUpdate = $request->getParam('getUpdate');
        $this->view->checkUpdate = $request->getParam('checkUpdate');
        $this->view->action_id = (int) $request->getParam('action_id');
        if (!$this->view->action_id)
            $this->view->action_id = $this->_getParam('action_id');
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
        if ($isForCategoryPage) {
            $this->view->actionFilter = $actionTypeGroup = $this->_getParam('actionFilter', 'all');
        }

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
        if ($viewer_id && (empty($subject) || $viewer->isSelf($subject))) {
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
        if (!$feedOnly && empty($subject) && !$isForCategoryPage) {

            if (!empty($viewer_id)) {
                $this->view->enableFriendListFilter = $enableFriendListFilter = $userFriendListEnable && $settings->getSetting('advancedactivity.friendlist.filtering', 1);
            } else {
                $this->view->enableFriendListFilter = $enableFriendListFilter = 0;
            }
            $enableContentTabs = 0;
            //  if (empty($subject)) {
            $this->view->contentTabs = $contentTabs = Engine_Api::_()->getDbtable('contents', 'advancedactivity')->getContentList(array('content_tab' => 1));
            $this->view->contentTabMax = $settings->getSetting('advancedactivity.defaultvisible', 7);
            $countContentTabs = @count($this->view->contentTabs);
            if ($countContentTabs)
                $enableContentTabs = 1;
            //  }
            $this->view->enableContentTabs = $enableContentTabs;
            $filterTabs = array();
            $i = 0;
            $defaultcontentTab = $request->getParam('actionFilter');
            $defaultUsercontentTab = $viewer_id && $settings->getSetting('advancedactivity.save.filter', 0) ? Engine_Api::_()->getDbtable('settings', 'user')->getSetting($viewer, "aaf_filter") : '';
            foreach ($contentTabs as $value) {
                if (empty($viewer_id) && in_array($value->filter_type, array('membership', 'only_network')))
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
            $this->view->canCreateCategroyList = 0;
            $categoryFilter = $settings->getSetting('aaf.category.filtering', 1);
            if ($categoryFilter && Engine_Api::_()->hasModuleBootstrap('advancedactivitypost')) {
                $tableCategories = Engine_Api::_()->getDbtable('categories', 'advancedactivitypost');
                $categoriesList = $tableCategories->getCategories();
                if (count($categoriesList)) {
                    if (count($filterTabs) > $this->view->contentTabMax) {
                        $filterTabs[$i]['filter_type'] = "separator";
                        $i++;
                    }
                    foreach ($categoriesList as $value) {
                        $filterTabs[$i]['filter_type'] = "activity_category";
                        $filterTabs[$i]['tab_title'] = $value->getTitle();
                        $filterTabs[$i]['list_id'] = $value->category_id;
                        $i++;
                    }
                }
            }
            if ($viewer_id) {
                $this->view->canCreateCustomList = $settings->getSetting('advancedactivity.customlist.filtering', 1);
                $customTypeLists = Engine_Api::_()->getDbtable('customtypes', 'advancedactivity')->getCustomTypeList(array('enabled' => 1));
                $count = count($customTypeLists);
                if (empty($count))
                    $this->view->canCreateCustomList = 0;
                if ($this->view->canCreateCustomList) {
                    $customLists = Engine_Api::_()->getDbtable('lists', 'advancedactivity')->getMemberOfList($viewer, 'default');
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
                if (Engine_Api::_()->hasModuleBootstrap('advancedactivitypost')) {
                    $tableCategories = Engine_Api::_()->getDbtable('categories', 'advancedactivitypost');
                    $categoriesList = $tableCategories->getCategories();
                    if (count($categoriesList)) {
                        $this->view->canCreateCategroyList = $settings->getSetting('aaf.categorylist.filtering', 1);
                        if ($this->view->canCreateCategroyList) {
                            $customLists = Engine_Api::_()->getDbtable('lists', 'advancedactivity')->getMemberOfList($viewer, 'category');
                            $countCustomLists = count($customLists);
                            if ($countCustomLists) {
                                if (count($filterTabs) > $this->view->contentTabMax) {
                                    $filterTabs[$i]['filter_type'] = "separator";
                                    $i++;
                                }
                                foreach ($customLists as $value) {
                                    $filterTabs[$i]['filter_type'] = "category_list";
                                    $filterTabs[$i]['tab_title'] = $value->title;
                                    $filterTabs[$i]['list_id'] = $value->list_id;
                                    $i++;
                                }
                            }
                        }
                    }
                }
            }
            $this->view->filterTabs = $filterTabs;
        }

        if ($actionTypeGroup && !in_array($actionTypeGroup, array('membership', 'owner', 'all', 'network_list', 'member_list', 'custom_list', 'category_list', 'activity_category'))) {
            $actionTypesTable = Engine_Api::_()->getDbtable('actionTypes', 'advancedactivity');
            $this->view->groupedActionTypes = $groupedActionTypes = $actionTypesTable->getEnabledGroupedActionTypes();
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
        } else if (in_array($actionTypeGroup, array('member_list', 'custom_list', 'category_list')) && ($list_id = $this->_getParam('list_id')) != null) {
            $listTypeFilter = Engine_Api::_()->advancedactivity()->getListBaseContent($actionTypeGroup, array('list_id' => $list_id));
        } else if ($actionTypeGroup == 'activity_category' && ($list_id = $this->_getParam('list_id')) != null) {
            $listTypeFilter['categories_id'][] = $list_id;
            $actionTypeGroup = 'category_list';
            $this->view->list_id = $list_id = $this->_getParam('list_id');
        } else if ($actionTypeGroup == 'network_list' && ($list_id = $this->_getParam('list_id') != null)) {
            $this->view->list_id = $list_id = $this->_getParam('list_id');
            $listTypeFilter = array($list_id);
        }
        $this->view->showPosts = $this->_getParam('showPosts', 1);
        $this->view->hide = $this->_getParam('hide');
        if (!$this->view->search)
            $this->view->search = $this->_getParam('search');
        // Get config options for activity
        $config = array(
            'action_id' => (int) $this->view->action_id,
            'max_id' => (int) $request->getParam('maxid'),
            'min_id' => (int) $request->getParam('minid'),
            'limit' => (int) $length,
            'showTypes' => $actionTypeFilters,
            'membership' => $actionTypeGroup == 'membership' ? true : false,
            'listTypeFilter' => $listTypeFilter,
            'actionTypeGroup' => $actionTypeGroup
        );

        if (Engine_Api::_()->hasModuleBootstrap('sitehashtag') && $this->_getParam('search')) {
            $config['hashtag'] = $this->_getParam('search');
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
        $this->view->hashtag = $hashtagNames = array();
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
                try {
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
                                $itemActionCounts[$itemAction->getGuid()] ++;
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
                        $total_guid = '';
                        /* Start Working group feed. */
                        if (!empty($action->getTypeInfo()->is_grouped) && isset($action->getTypeInfo()->is_grouped)) {
                            if ($action->type == 'friends') {
                                $object_guid = $action->getSubject()->getGuid();
                                $total_guid = $action->type . '_' . $object_guid;

                                if (!isset($grouped_actions[$total_guid])) {
                                    $grouped_actions[$total_guid] = array();
                                }
                                $grouped_actions[$total_guid][] = $action->getObject();
                            } elseif ($action && $action->type == 'tagged') {
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
                                break;
                            }
                        }
                    }
                } catch (Exception $ex) {
                    continue;
                }
            }

            // Set next tmp max_id
            if ($nextid) {
                $tmpConfig['max_id'] = $nextid;
            }
            if (!empty($tmpConfig['action_id'])) {
                $actions = array();
            }
        } while (count($activity) < $length && $selectCount <= 5 && !$endOfFeed);

        if (count($activity) < $length || count($activity) <= 0) {
            $endOfFeed = true;
        }

        $this->view->groupedFeeds = $grouped_actions;
        $this->view->activity = $activity;
        $this->view->activityCount = count($activity);
        $this->view->nextid = $nextid;
        $this->view->firstid = $firstid;
        $this->view->endOfFeed = $endOfFeed;

        if (Engine_Api::_()->hasModuleBootstrap('sitehashtag')) {
            $this->view->hashtag = $this->getHashtagNames($activity);
        }


        // Get some other info
        if (!empty($subject)) {
            $this->view->subjectGuid = $subject->getGuid(false);
        }


        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Advancedactivity/View/Helper', 'Advancedactivity_View_Helper');
        if (($getListViewValue + $getPublishValue) != $getComposerValue)
            Engine_Api::_()->getApi('settings', 'core')->setSetting('advancedactivity.post.active', $composerLimit);

        $front = Zend_Controller_Front::getInstance();
        $this->view->module_name = $front->getRequest()->getModuleName();
        $this->view->action_name = $front->getRequest()->getActionName();
        //  }
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
