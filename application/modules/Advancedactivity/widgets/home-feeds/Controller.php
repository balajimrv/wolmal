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
class Advancedactivity_Widget_HomeFeedsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->title = $this->_getParam('title', null);
        $enableComposer = $this->_getParam('enableComposer', true);
        $loadByAjax = Engine_Api::_()->core()->hasSubject();
        $this->view->loadByAjax = $this->_getParam('loadByAjax', $loadByAjax);

        $this->view->showTabs = $this->_getParam('showTabs', 0);
        $this->view->showPosts = $this->_getParam('showPosts', 1);
        
        if(Engine_Api::_()->hasModuleBootstrap('sitehashtag')) {
            $front = Zend_Controller_Front::getInstance();
            $module = $front->getRequest()->getModuleName();
            $controller = $front->getRequest()->getControllerName();
            $action = $front->getRequest()->getActionName();
            if($module == 'sitehashtag' && $controller == 'index' && $action == 'index') {
                $this->view->hide = 1;
            }
            else{
                $this->view->hide = 0;
            }
        }
        
        if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') && (Engine_API::_()->seaocore()->isMobile() || Engine_API::_()->seaocore()->isTabletDevice())) {
           $this->view->loadByAjax = 0;
        }
        $this->view->videoWidth = $this->_getParam("videowidth", 0);
        $this->view->width1 = $this->_getParam("width1", 410);
        $this->view->width2 = $this->_getParam("width2", 410);
        $this->view->height2 = $this->_getParam("height2", 250);
        $this->view->width3big = $this->_getParam("width3big", 410);
        $this->view->height3big = $this->_getParam("height3big", 250);
        $this->view->width3small = $this->_getParam("width3small", 200);
        $this->view->height3small = $this->_getParam("height3small", 150);
        $this->view->width4big = $this->_getParam("width4big", 410);
        $this->view->height4big = $this->_getParam("height4big", 250);
        $this->view->width4small = $this->_getParam("width4small", 130);
        $this->view->height4small = $this->_getParam("height4small", 100);
        $this->view->width5big = $this->_getParam("width5big", 200);
        $this->view->height5big = $this->_getParam("height5big", 150);
        $this->view->width5small = $this->_getParam("width5small", 130);
        $this->view->height5small = $this->_getParam("height5small", 100);
        $this->view->width6 = $this->_getParam("width6", 410);
        $this->view->height6 = $this->_getParam("height6", 150);
        $this->view->width78 = $this->_getParam("width78", 95);
        $this->view->height78 = $this->_getParam("height78", 90);
        $this->view->widthphotoattachment = $this->_getParam("widthphotoattachment", 440);

        $this->view->showScrollTopButton = $this->_getParam("showScrollTopButton", 1);
        
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        $aafInfoTooltips = null;
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
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

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $this->view->isMobile = Engine_Api::_()->advancedactivity()->isMobile();
        // Get some other info
        if (!empty($subject)) {
            $this->view->subjectGuid = $subject->getGuid(false);
        }

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

            $this->view->parentType = null ;
            $this->view->parentId = null ;

            if (!empty($subject)) {
                // Get subject

                $parentSubject = $subject = Engine_Api::_()->core()->getSubject();
                $this->view->parentType = $subject->getType();
                $this->view->parentId = $subject->getIdentity();
                
                if ($subject->getType() == 'siteevent_event') {
                    $parentSubject = Engine_Api::_()->getItem($subject->parent_type, $subject->parent_id);
                    if (!Engine_Api::_()->authorization()->isAllowed($subject, $viewer, "post"))
                        $this->view->enableComposer = false;
                }
                else if ($subject->getType() == 'sitepage_page' || $subject->getType() == 'sitepageevent_event' || $parentSubject->getType() == 'sitepage_page') {
                    $pageSubject = $parentSubject;
                    if ($subject->getType() == 'sitepageevent_event')
                        $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
                    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'comment');
                    if (!empty($isManageAdmin)) {
                        $this->view->enableComposer = true;
                        if (!$pageSubject->all_post && !Engine_Api::_()->sitepage()->isPageOwner($pageSubject)) {
                            $this->view->enableComposer = false;
                        }
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
                } else if ($subject->getType() == 'sitebusiness_business' || $subject->getType() == 'sitebusinessevent_event' || $parentSubject->getType() == 'sitebusiness_business') {
                    $businessSubject = $parentSubject;
                    if ($subject->getType() == 'sitebusinessevent_event')
                        $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
                    $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'comment');
                    if (!empty($isManageAdmin)) {
                        $this->view->enableComposer = true;
                        if (!$businessSubject->all_post && !Engine_Api::_()->sitebusiness()->isBusinessOwner($businessSubject)) {
                            $this->view->enableComposer = false;
                        }
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
                } elseif ($subject->getType() == 'sitegroup_group' || $subject->getType() == 'sitegroupevent_event' || $parentSubject->getType() == 'sitebusiness_business') {
                    $groupSubject = $parentSubject;
                    if ($subject->getType() == 'sitegroupevent_event')
                        $groupSubject = Engine_Api::_()->getItem('sitegroup_group', $subject->group_id);
                    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($groupSubject, 'comment');
                    if (!empty($isManageAdmin)) {
                        $this->view->enableComposer = true;
                        if (!$groupSubject->all_post && !Engine_Api::_()->sitegroup()->isGroupOwner($groupSubject)) {
                            $this->view->enableComposer = false;
                        }
                    }
                    if ($this->view->enableComposer) {
                        $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
                        $activityFeedType = null;
                        if (Engine_Api::_()->sitegroup()->isGroupOwner($groupSubject) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
                            $activityFeedType = 'sitegroup_post_self';
                        else
                            $activityFeedType = 'sitegroup_post';
                        if (!$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
                            $this->view->enableComposer = false;
                        }
                    }
                } elseif ($subject->getType() == 'sitestore_store' || $subject->getType() == 'sitestoreevent_event' || $parentSubject->getType() == 'sitestore_store') {
                    $storeSubject = $parentSubject;
                    if ($subject->getType() == 'sitestoreevent_event')
                        $storeSubject = Engine_Api::_()->getItem('sitestore_store', $subject->store_id);
                    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'comment');
                    if (!empty($isManageAdmin)) {
                        $this->view->enableComposer = true;
                        if (!$storeSubject->all_post && !Engine_Api::_()->sitestore()->isStoreOwner($storeSubject)) {
                            $this->view->enableComposer = false;
                        }
                    }
                    if ($this->view->enableComposer) {
                        $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
                        $activityFeedType = null;
                        if (Engine_Api::_()->sitestore()->isStoreOwner($storeSubject) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
                            $activityFeedType = 'sitestore_post_self';
                        else
                            $activityFeedType = 'sitestore_post';
                        if (!$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
                            $this->view->enableComposer = false;
                        }
                    }
                }
            }
        }
        if ($this->view->enableComposer) {
            $this->view->enableComposer = $enableComposer;
        }
        if ($this->view->enableComposer) {
            // Assign the composing values
            $composerList = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.menuoptions', Engine_Api::_()->advancedactivity()->getComposerMenuList());
            $composePartials = array();
           
            foreach (Zend_Registry::get('Engine_Manifest') as $data) {
                if (empty($data['composer']) || !empty($data['composer']['facebook']) || !empty($data['composer']['twitter'])) {
                    continue;
                }
                foreach ($data['composer'] as $type => $config) {
                    $key = $type . 'XXX' . $config['script'][1];
                    if (!in_array($type, array('advanced_facebook', 'advanced_twitter', 'advanced_linkedin', 'tag', 'hashtag')) && !in_array($key, $composerList)) {
                        continue;
                    }
                    if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
                        continue;
                    }
                    if ($type == "tag" && $config['script'][1] == 'core')
                        continue;

                    if ($type == "link" && $config['script'][1] == 'core') {
                        $config['script'][1] = 'advancedactivity';
                    }
                    $composePartials[$key] = $config['script'];
                }
            }
            
            $p = array();
            foreach ($composePartials as $key => $partials) {
                if(isset($partials[1]) && $partials[1] == 'album' && Engine_Api::_()->hasModuleBootstrap('sitealbum')) {
                    $partials[1] = 'sitealbum';
                }
                if(($key === 'videoXXXvideo' || $key === 'videoXXXsitevideo') && Engine_Api::_()->hasModuleBootstrap('sitevideo')) {
                    $partials[1] = 'sitevideo';
                    $key = 'videoXXXsitevideo';
                }
                $p[$key] = $partials;
            }
            $this->view->composePartials = $p;
        }
        
        /*  Customization Start */
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Advancedactivity/View/Helper', 'Advancedactivity_View_Helper');
        $aafInfotooltipPost = Zend_Registry::isRegistered('advancedactivity_infotooltip_post') ? Zend_Registry::get('advancedactivity_infotooltip_post') : null;
        // Get lists if viewing own profile
        // if( $viewer->isSelf($subject) ) {
        // Get lists
        $this->view->settingsApi = $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->tabtype = $settings->getSetting('advancedactivity.tabtype', 3);
        $this->view->composerType = $settings->getSetting('advancedactivity.composer.type', 0);

        if (empty($subject) || $viewer->isSelf($subject)) {
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
            if (Engine_Api::_()->hasModuleBootstrap('advancedactivitypost')) {
                $this->view->canCreateCategroyList = 1;
                $tableCategories = Engine_Api::_()->getDbtable('categories', 'advancedactivitypost');
                $this->view->categoriesList = $tableCategories->getCategories();
            }
        }
        $web_values = $this->_getParam('advancedactivity_tabs', array());
        $isFeedEnabled = Zend_Registry::isRegistered('advancedactivity_feedEnabled') ? Zend_Registry::get('advancedactivity_feedEnabled') : null;
        if (!empty($subject) || $this->view->isMobile || empty($viewer_id)) {
            $web_values = array("aaffeed");
        }
        //CHECK IF THE WELCOME TAB WILL SHOW OR NOT.
        // Geting Welcome Tab Info
        if (!empty($web_values)) {
            $welcome_key = array_search('welcome', $web_values);
        }
        if ($welcome_key !== FALSE) {
            $session = new Zend_Session_Namespace();
            // Include JS files of "Suggestion Plugin" or "People You May Know Plugin" for Welcome Tab.
            $this->view->is_suggestionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
            $this->view->is_pymkEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('peopleyoumayknow');
            $this->view->is_welcomeTabEnabled = true;

            $getCustomBlockSettings = Engine_Api::_()->advancedactivity()->getCustomBlockSettings(array());
            if (empty($getCustomBlockSettings)) {
                if (isset($web_values[$welcome_key])) {
                    unset($web_values[$welcome_key]);
                }
            } else {
                $is_welcomeTabDefault = Engine_Api::_()->getApi('settings', 'core')->getSetting('welcomeTab.is.default', 0);
                if (!empty($session->isUserSignup)) {
                    $is_welcomeTabDefault = true;
                    unset($session->isUserSignup);
                }
                if (empty($is_welcomeTabDefault) && array_search('aaffeed', $web_values)) {
                    $this->view->activeTab = 1;
                }
            }
        }
        if (empty($isFeedEnabled)) {
            return $this->setNoRender();
        }


        $front = Zend_Controller_Front::getInstance();
        $this->view->module_name = $front->getRequest()->getModuleName();
        $this->view->action_name = $front->getRequest()->getActionName();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->search=  urldecode($request->getParam('search'));

        if ($this->view->enableComposer) {

            //LINKEDIN WORK..................................................//

            $linkedin_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey');
            $linkedin_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey');
            $linkedin_key = array_search('linkedin', $web_values);
            $linkedin_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.enable', 0);
            if (!empty($linkedin_apikey) && !empty($linkedin_secret) && ($linkedin_enable || $linkedin_key != FALSE)) {


                $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
                //$linkedinTable = Engine_Api::_()->getDbtable('linkedin', 'advancedactivity');
                $OBJ_linkedin = $Api_linkedin->getApi();
                $this->view->LinkedinloginURL = '';
                $this->view->LinkedinloginURL_temp = $LinkedinloginURL = Zend_Controller_Front::getInstance()->getRouter()
                                ->assemble(array('module' => 'seaocore', 'controller' => 'auth', 'action' => 'linkedin'), 'default', true) . '?' . http_build_query(array('redirect_urimain' => urlencode(( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_linkedin=1')));
                if ($OBJ_linkedin && $Api_linkedin->isConnected()) {
                    $OBJ_linkedin->setToken(array('oauth_token' => $_SESSION['linkedin_token2'], 'oauth_token_secret' => $_SESSION['linkedin_secret2']));
                    $OBJ_linkedin->setCallbackUrl(( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_linkedin=1');


                    $this->view->LinkedinloginURL_temp = $LinkedinloginURL;
                    $this->view->LinkedinloginURL = '';

                    try {
                        $options = '?count=1';
                        $LinkedinUserFeed = $OBJ_linkedin->profile();

                        if ($LinkedinUserFeed['success'] != TRUE) {

                            $this->view->LinkedinloginURL = $LinkedinloginURL;
                        }
                    } catch (Exception $e) {
                        $this->view->LinkedinloginURL = $LinkedinloginURL;
                    }
                } else {
                    $this->view->LinkedinloginURL = $LinkedinloginURL;
                }
            } else if ($linkedin_key !== FALSE && isset($web_values[$linkedin_key])) {
                unset($web_values[$linkedin_key]);
            }

            //FIRST CHECKING IF ADMIN HAS ENABLED THE THIRD PARTY SERVICES OR NOT....
            if ($subject && ($subject->getType() == 'sitepage_page' || $subject->getType() == 'sitebusiness_business' || $subject->getType() == 'sitestore_store'))
                $managepage = true;
            else
                $managepage = false;
            
            if($subject && $subject->getType() == 'sitegroup_group')
              $user_managed_groups = true;
            else
              $user_managed_groups = false;

            //Instagram WORK..................................................//

            $instagram_apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('instagram.apikey');
            $instagram_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('instagram.secretkey');
            $instagram_key = array_search('instagram', $web_values);
            $instagram_enable = in_array('instagram', $web_values) ? 1 : 0;

            if ( FALSE &&!empty($instagram_apikey) && !empty($instagram_secret) && ($instagram_key != FALSE || $instagram_enable)) {

                $Api_instagram = Engine_Api::_()->getApi('instagram_Api', 'seaocore');
                //$instagramTable = Engine_Api::_()->getDbtable('instagram', 'advancedactivity');
                $OBJ_instagram = $Api_instagram->getApi();
                $this->view->instagramloginURL = '';
                $this->view->instagramloginURL_temp = $instagramloginURL = Zend_Controller_Front::getInstance()->getRouter()
                                ->assemble(array('module' => 'seaocore', 'controller' => 'auth', 'action' => 'instagram-check'), 'default', true) . '?' . http_build_query(array('redirect_urimain' => urlencode(( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_instagram=1')));

                if ($OBJ_instagram && $Api_instagram->isConnected()) {
                    $OBJ_instagram->setAccessToken($_SESSION['instagram_token2']);

                    $this->view->instagramloginURL_temp = $instagramloginURL;
                    $this->view->instagramloginURL = '';

                    try {
                        $options = '?count=1';
                        $instagramUserFeed = $OBJ_instagram->getUser();

                        if (empty($instagramUserFeed)) {
                            $this->view->instagramloginURL = $instagramloginURL;
                        }
                    } catch (Exception $e) {
                        $this->view->instagramloginURL = $instagramloginURL;
                    }
                } else {
                    $this->view->instagramloginURL = $instagramloginURL;
                }
            } else if ($instagram_key !== FALSE && isset($web_values[$instagram_key])) {
                unset($web_values[$instagram_key]);
            }

            //FACEBOOK WORK..................................................//

            $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook');
            $fb_key = array_search('facebook', $web_values);
            $Api_facebook = Engine_Api::_()->getApi('facebook_Facebookinvite', 'seaocore');
            //THIS IS A SPACIAL CONDITION TILL 30TH APRIL 2016 IF THE APP IS CREATED AFTER 30 APRIL THEN WE WILL NOT SHOW FACEBOOK TAB HERE.      
            if (method_exists($Api_facebook, 'checkAppReadPermission') && !$Api_facebook->checkAppReadPermission() && $fb_key !== FALSE && $web_values[$fb_key]) {
                unset($web_values[$fb_key]);
            }

            $facebook_userfeed = $Api_facebook->getFBInstance();
            $fb_key = array_search('facebook', $web_values);
            if (!empty($settings['appid']) && !empty($settings['secret'])) {
                $FBloginURL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()
                                ->assemble(array('module' => 'seaocore', 'controller' => 'auth', 'action' => 'facebook'), 'default', true) . '?' . http_build_query(array('redirect_urimain' => urlencode(( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_fb=1'), 'manage_pages' => $managepage, 'user_managed_groups' => $user_managed_groups));

                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('facebook.enable', Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable == 'publish' ? 1 : 0) || $fb_key !== FALSE) {
                    $session = new Zend_Session_Namespace();


                    $session_userfeed = $facebook_userfeed;
                    $this->view->FBloginURL = '';
                    if (!empty($facebook_userfeed)) {

                        $this->view->FBloginURL_temp = $FBloginURL = $FBloginURL;

                        $this->view->FBloginURL = '';
                        $checksiteIntegrate = true;
                        $facebookCheck = new Seaocore_Api_Facebook_Facebookinvite();
                        $fb_checkconnection = $facebookCheck->checkConnection(null, $facebook_userfeed);

                        if ($session_userfeed && $fb_checkconnection) {
                            //$session->fb_checkconnection = true;
                            $core_fbenable = Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable;
                            $enable_socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
                            if (('publish' == $core_fbenable || 'login' == $core_fbenable || $enable_socialdnamodule) && (!$fb_checkconnection)) {
                                $checksiteIntegrate = false;
                            } else {
                                try {
                                    if (!isset($session->fb_canread)) {
                                        $permissions = $facebook_userfeed->api("/me/permissions");
                                        if (!$facebookCheck->checkPermission('read_stream', $permissions)) {
                                            $checksiteIntegrate = false;
                                        } else {
                                            $session->fb_canread = true;
                                        }
                                        
                                        if (!$facebookCheck->checkPermission('manage_pages', $permissions)) {
                                            $session->fb_can_managepages = false;
                                        } else {
                                            $session->fb_can_managepages = true;
                                        }
                                        
                                        if (!$facebookCheck->checkPermission('user_managed_groups', $permissions)) {
                                            $session->fb_can_managegroups = false;
                                        } else {
                                            $session->fb_can_managegroups = true;
                                        }
                                    }
                                    
                                    if ($subject && ((($subject->getType() == 'sitepage_page' || $subject->getType() == 'sitebusiness_business' || $subject->getType() == 'sitestore_store') && !$session->fb_can_managepages) || (($subject->getType() == 'sitegroup_group') && !$session->fb_can_managegroups ))) {
                                      $checksiteIntegrate = false;
                                    }
                                } catch (Exception $e) {
                                    $checksiteIntegrate = false;
                                }
                            }
                        }
                        if (!$session_userfeed || !$fb_checkconnection || !$checksiteIntegrate) {
                            $this->view->FBloginURL = $FBloginURL;
                        }
                    }
                }
            } else if ($fb_key !== FALSE && isset($web_values[$fb_key])) {
                unset($web_values[$fb_key]);
                $fb_key = false;
            }


            //TWITTER WORK............................................................//
            $tweet_key = array_search('twitter', $web_values);
            $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.twitter');
            if (function_exists('mb_strlen') && !empty($settings['key']) && !empty($settings['secret'])) {

                $this->view->TwitterLoginURL_temp = $TwitterloginURL = Zend_Controller_Front::getInstance()->getRouter()
                                ->assemble(array('module' => 'seaocore', 'controller' => 'auth',
                                    'action' => 'twitter'), 'default', true) . '?return_url=' . ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->url();
                $this->view->TwitterLoginURL = '';
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('twitter.enable', Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable == 'publish' ? 1 : 0) || $tweet_key !== FALSE) {
                    try {
                        $Api_twitter = Engine_Api::_()->getApi('twitter_Api', 'seaocore');
                        $twitterOauth = $twitter = $Api_twitter->getApi();
                        if ($twitter && $Api_twitter->isConnected()) {
                            // @todo truncation?
                            // @todo attachment
                            //$twitter = $twitterTable->getApi();
                            //$twitter->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);
                            $twitterData = (array) $twitterOauth->get(
                                            'statuses/home_timeline', array('count' => 1)
                            );
                            if (isset($twitterData['errors']))
                                $this->view->TwitterLoginURL = $TwitterloginURL;

                            //$logged_TwitterUserfeed = $twitter->statuses_homeTimeline(array('count' => 1));
                        } else {

                            $this->view->TwitterLoginURL = $TwitterloginURL;
                        }
                    } catch (Exception $e) {
                        $this->view->TwitterLoginURL = $TwitterloginURL;
                        // Silence
                    }
                }
            } else if ($tweet_key !== FALSE && isset($web_values[$tweet_key])) {
                unset($web_values[$tweet_key]);
            }
        }
        $this->view->web_values = $web_values;
        $count = 0;
        if (!empty($web_values))
            $count = count($web_values);

        if (empty($count)) {
            return $this->setNoRender();
        }

        foreach ($web_values as $value) {
            if (empty($this->view->activeTab)) {
                $this->view->activeTab = array_search($value, array("1" => "aaffeed", "3" => "facebook", "2" => "twitter", "4" => "welcome", "5" => "linkedin", "6" => "instagram"));
            }
            $tab = "is" . ucfirst($value) . "Enable";
            $this->view->$tab = 1;
        }

        if (isset($_GET['activityfeedtype'])) {
            switch ($_GET['activityfeedtype']) {
                case 'site':
                    if ($this->view->isAaffeedEnable) {
                        $this->view->activeTab = 1;
                    }
                    break;
                case 'facebook':
                    if ($this->view->isFacebookEnable) {
                        $this->view->activeTab = 3;
                    }
                    break;
                case 'twitter':
                    if ($this->view->isTwitterEnable) {
                        $this->view->activeTab = 2;
                    }
                    break;
                case 'linkedin':
                    if ($this->view->isLinkedinEnable) {
                        $this->view->activeTab = 5;
                    }
                    break;
                case 'instagram':
                    if ($this->view->isInstagramEnable) {
                        $this->view->activeTab = 6;
                    }
                    break;
                case 'welcome':
                    if ($this->view->isWelcomeEnable) {
                        $this->view->activeTab = 4;
                    }
                    break;
            }
        }

        $this->view->count_tabs = $count;
        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
        $this->view->maxAutoScrollFeed = 0;

        if ($this->view->isMobile) {
            $this->view->autoScrollFeedEnable = 0;
            $this->view->feedToolTipEnable = 0;
        } else {
            $this->view->autoScrollFeedEnable = $coreSettingsApi->getSetting('advancedactivity.scroll.autoload', 1);
            $this->view->aafShowImmediately = $coreSettingsApi->getSetting('advancedactivity.feed.autoload', 0);
            $this->view->maxAutoScrollFeed = $coreSettingsApi->getSetting('advancedactivity.maxautoload', 0);
            if (!empty($aafInfotooltipPost)) {
                $aafInfoTooltips = $coreSettingsApi->getSetting('advancedactivity.info.tooltips', 1);
            }
            $this->view->feedToolTipEnable = $aafInfoTooltips;
        }

        if (!Engine_Api::_()->seaocore()->isLessThan420ActivityModule()) {
            // Form token
            $session = new Zend_Session_Namespace('ActivityFormToken');
            //$session->setExpirationHops(10);
            if (empty($session->token)) {
                $this->view->formToken = $session->token = md5(time() . $viewer->getIdentity() . get_class($this));
            } else {
                $this->view->formToken = $session->token;
            }
        }
    }

}
