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
class Sitemobile_Widget_SitemobileNotificationRequestMessagesController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        // Don't render this if not logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }

        $this->view->locationSpecific = $locationSpecific = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);

        $this->view->location = $this->_getParam('location', 0);
        $defaultNotification = 2; //SHOW ON ALL PAGES
        //FOR APP DEFAULT WILL BE 1
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $moduleName = $request->getModuleName();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        $allParams = $request->getParams();
        $pagename = $moduleName . '_' . $controllerName . '_' . $actionName;
        if (Engine_Api::_()->sitemobile()->isApp())
            $defaultNotification = 1;
        //CHECK EITHER TO SHOW ON ONLY MEMBER HOME PAGE OR EVERY PAGE OF SITE DEFAULT WILL BE ONLY ON MEMBERHOME PAGE WHICH HAS VALUE 1.
        $this->view->showOnlyCountLeft = ($this->_getParam('activePage', $defaultNotification) == 1) && ($moduleName != 'user' || $actionName != 'home');
        if ($this->view->showOnlyCountLeft && $pagename === 'sitemobile_browse_browse')
            return $this->setNoRender();
        //CHECK IT SHOULD BE MEMBER HOME PAGE.
        $this->view->showContent = $this->_getParam('showContent', array('request', 'updates', 'message'));
        $this->view->browseLayoutTypes = $this->_getParam('browseLayoutTypes', 'dashboard');
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
        $this->view->showCartIcon = Engine_Api::_()->getDbtable('modules', 'sitemobile')->isModuleEnabled('sitestoreproduct');
        if (!$require_check) {
            if ($viewer->getIdentity()) {
                $this->view->search_check = true;
            } else {
                $this->view->search_check = false;
            }
        }
        else
            $this->view->search_check = true;

        $this->view->messagePermission = false;
        $this->view->loadingViaAjax = $this->_getParam('loadingViaAjax', 1);

        if ($viewer->getIdentity()) {
            $this->view->notificationCount = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->hasNotifications($viewer);
            $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.notificationupdate');

            // Get permission setting
            $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
            if (Authorization_Api_Core::LEVEL_DISALLOW !== $permission) {
                $this->view->messagePermission = true;
            }

            $this->view->messageCount = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);

            $this->view->requestsCount = $requests = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->hasRequests($viewer);
            if ($this->view->showCartIcon) {
                $getCartId = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getCartId($viewer->getIdentity());
                if (!empty($getCartId)) {
                    $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
                    $getCart = $productTable->getCart($getCartId, false);
                    $this->view->cartProductCounts = $cartProductCounts = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getProductCounts($getCartId);
                }
            }

            $this->view->totalCount = $this->view->notificationCount + $this->view->messageCount + $this->view->requestsCount + $this->view->cartProductCounts;

// Location related work
            if ($this->view->location) {
                $locationContentTable = Engine_Api::_()->getDbTable('locationcontents', 'seaocore');
                //DELETE COOKIES IF EXISTING SPECIFIC LOCATIONS IS NOT MATCHED WITH SAVED COOKIES VALUE
                $currentLocationCookies = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie(1);
                $setCookies = 1;
                if ($locationSpecific && !empty($currentLocationCookies) && !empty($currentLocationCookies['location'])) {
                    $locationcontent_id = $locationContentTable->getSpecificLocationColumn(array('location' => $currentLocationCookies['location'], 'columnName' => 'locationcontent_id', 'status' => 1));
                    if (empty($locationcontent_id)) {
                        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
                        setcookie('seaocore_myLocationDetails', '', time() - 3600, $view->url(array(), 'default', true));

                        if (($locationDefault = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefault'))) {
                            $seaocore_myLocationDetails['latitude'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultlatitude');
                            $seaocore_myLocationDetails['longitude'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultlongitude');
                            $seaocore_myLocationDetails['location'] = $locationDefault;
                            $seaocore_myLocationDetails['changeLocationWidget'] = 1;
                            $seaocore_myLocationDetails['locationmiles'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles');
                            Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($seaocore_myLocationDetails);
                            $setCookies = 0;
                        }
                    }
                }

                //SET DEFAULT COOKIES VALUE IF NOT SET 
                $locationCookies = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie(1);
                if ($setCookies && ((empty($locationCookies) || empty($locationCookies['location'])) && ($locationDefault = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefault')))) {
                    $seaocore_myLocationDetails['latitude'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultlatitude');
                    $seaocore_myLocationDetails['longitude'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultlongitude');
                    $seaocore_myLocationDetails['location'] = $locationDefault;
                    $seaocore_myLocationDetails['changeLocationWidget'] = 1;
                    $seaocore_myLocationDetails['locationmiles'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles');
                    Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($seaocore_myLocationDetails);
                }

                //GET VIEWER ID
                $viewer = Engine_Api::_()->user()->getViewer();
                $user = '';
                if ($viewer->getIdentity())
                    $user = Engine_Api::_()->getItem('user', $viewer->getIdentity());
                $this->view->getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
                $this->view->params = array();
                $this->view->detactLocation = $this->view->params['detactLocation'] = $this->_getParam('detactLocation', 0);

                if ($locationSpecific) {

                    $this->view->locationValue = '';
                    $this->view->locationValueTitle = '';
                    $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
                    if (isset($getMyLocationDetailsCookie['location']) && !empty($getMyLocationDetailsCookie['location'])) {
                        $this->view->locationValue = $getMyLocationDetailsCookie['location'];
                    }

                    $locations = $locationContentTable->getLocations(array('status' => 1));
                    $locationsArray = array();
                    foreach ($locations as $location) {
                        $locationsArray[$location->location] = $location->title;
                        if ($this->view->locationValue == $location->location) {
                            $this->view->locationValueTitle = $location->title;
                        }
                    }

                    $this->view->locationsArray = $locationsArray;
                } else {
                    $this->view->showSeperateLink = $this->_getParam('showSeperateLink', 1);
                }

                if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                    if (Engine_Api::_()->hasModuleBootstrap('sitemember')) {
                        $this->view->showLocationPrivacy = $this->_getParam('showLocationPrivacy', 0);
                        $this->view->updateUserLocation = $this->_getParam('updateUserLocation', 0);
                        $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie(1);

                        if ($user && $user->location && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.change.user.location', 0)) {
                            $locationRow = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocation(array('location' => $user->location));
                            $getMyLocationDetailsCookie['location'] = $user->location;
                            $getMyLocationDetailsCookie['latitude'] = $locationRow->latitude;
                            $getMyLocationDetailsCookie['longitude'] = $locationRow->longitude;
                            $getMyLocationDetailsCookie['changeLocationWidget'] = 1;
                            Engine_Api::_()->seaocore()->setMyLocationDetailsCookie($getMyLocationDetailsCookie);
                            $this->view->getMyLocationDetailsCookie = $getMyLocationDetailsCookie;
                        } elseif ($user && !$user->location && $this->view->updateUserLocation) {
                            if (!empty($getMyLocationDetailsCookie)) {
                                Engine_Api::_()->seaocore()->setUserLocation($getMyLocationDetailsCookie['location']);
                            }
                        }
                    }

                    if ($user && $user->getIdentity() && Engine_Api::_()->hasModuleBootstrap('sitemember')) {
                        $this->view->privacyOptions = $privacyOptions = Fields_Api_Core::getFieldPrivacyOptions();
                        $fields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);
                        $field_id = '';

                        $aliasedFields = $user->fields()->getFieldsObjectsByAlias();
                        $topLevelId = $aliasedFields['profile_type']->field_id;
                        $profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitemember');
                        $profilemapsTablename = $profilemapsTable->info('name');
                        $select = $profilemapsTable->select()->from($profilemapsTablename, array('profile_type'));
                        $select->where($profilemapsTablename . '.option_id = ?', $topLevelId);

                        $profile_type = $select->query()->fetchColumn();

                        foreach ($fields as $value) {
                            if (isset($value['type']) && $value['type'] == 'location' && $profile_type == $value['field_id']) {
                                $field_id = $value['field_id'];
                            } elseif (isset($value['type']) && $value['type'] == 'city' && $profile_type == $value['field_id']) {
                                $field_id = $value['field_id'];
                            }
                        }
                        if ($field_id) {
                            $values = Engine_Api::_()->fields()->getFieldsValues($user);
                            $valueRows = $values->getRowsMatching(array(
                                'field_id' => $field_id,
                                'item_id' => $user->getIdentity()
                            ));
                            foreach ($valueRows as $valueRow) {
                                $this->view->prevPrivacy = $valueRow->privacy;
                            }
                        }
                    }
                }
            }
        }
    }

}