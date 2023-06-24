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
class Sitemobile_Widget_SitemobileNavigationController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        // Don't render this if not logged in
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->isajax = $this->_getParam('isajax', 0);
        $zendInstance = Zend_Controller_Front::getInstance();
        $request = $zendInstance->getRequest();
        $this->view->params = $p = $request->getParams();
        $menusSitemobile = Engine_Api::_()->getApi('menus', 'sitemobile');
        $sitemobileNavigation = Zend_Registry::isRegistered('sitemobileNavigation') ? Zend_Registry::get('sitemobileNavigation') : null;
        $sitemobileNavigationName = Zend_Registry::isRegistered('sitemobileNavigationName') ? Zend_Registry::get('sitemobileNavigationName') : null;
        $module = $p['module'];
        $moduleControllerAction = $p['module'] . '_' . $p['controller'] . '_' . $p['action'];

        if (empty($sitemobileNavigationName)) {
            switch ($module) {
                //      case 'blog':
                //				//GET NAVIGATION
                //				$this->view->navigation = $menusSitemobile->getNavigation('blog_main');
                //        break;
                case 'core':
                case 'activity':
                    break;
                case 'album':
                    $this->view->navigation = $navigation = $menusSitemobile->getNavigation('album_main');

                    foreach ($navigation->getPages() as $page) {
                        $class = explode(" ", $page->class);
                        if (($p['action'] == 'browse' && $class[1] == 'album_main_browse') || ($p['action'] == 'manage' && $class[1] == 'album_main_manage') || ($p['action'] == 'photos' && $class[1] == 'album_main_photo')) {
                            $page->active = true;
                            break;
                        }
                    }
                    break;
                case 'event':

                    //GET NAVIGATION
                    $this->view->navigation = $navigation = $menusSitemobile->getNavigation('event_main');
                    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
                    $active = $navigation->findOneBy('active', true);
                    if (empty($active) || $active->getRoute() !== 'event_general') {
                        $filter = !empty($p['filter']) ? $p['filter'] : 'future';
                        if ($filter != 'past' && $filter != 'future')
                            $filter = 'future';
                        foreach ($navigation->getPages() as $page) {
                            if (($page->label == "Upcoming Events" && $filter == "future") ||
                                    ($page->route == "event_past" && $filter == "past")) {
                                if ($p['module'] == 'event' && $p['controller'] == 'index' && ($p['action'] == 'create' || $p['action'] == 'edit')) {
                                    $page->active = false;
                                } else {
                                    $page->active = true;
                                }
                            }
                        }
                    }
                    break;
                case 'birthday':
                    $this->view->navigation = $menusSitemobile->getNavigation('event_main');
                    break;
                case 'user':
                    if ($p['controller'] !== 'settings')
                        break;
                    $id = $this->_getParam('id', null);
                    //SET UP NAVIGATION
                    $this->view->navigation = $menusSitemobile->getNavigation('user_settings', ( $id ? array('params' => array('id' => $id)) : array()));
                    break;
                default :
                    //PAGE PLUGIN MAIN NAVIGATION WORK  
                    $pagemodules = array('sitepagenote', 'sitepagepoll', 'sitepageoffer', 'sitepagedocument', 'sitepagereview', 'sitepageevent', 'sitepagebadge', 'sitepagediscussion', 'sitepagevideo', 'sitepagemember', 'sitepagemusic');
                    if (in_array($module, $pagemodules)) {
                        $module = 'sitepage';
                    }

                    $businessmodules = array('sitebusinessnote', 'sitebusinesspoll', 'sitebusinessoffer', 'sitebusinessdocument', 'sitebusinessreview', 'sitebusinessevent', 'sitebusinessbadge', 'sitebusinessdiscussion', 'sitebusinessvideo', 'sitebusinessmember', 'sitebusinessmusic');
                    if (in_array($module, $businessmodules)) {
                        $module = 'sitebusiness';
                    }

                    $groupmodules = array('sitegroupnote', 'sitegrouppoll', 'sitegroupoffer', 'sitegroupdocument', 'sitegroupreview', 'sitegroupevent', 'sitegroupbadge', 'sitegroupdiscussion', 'sitegroupvideo', 'sitegroupmember', 'sitegroupmusic');
                    if (in_array($module, $groupmodules)) {
                        $module = 'sitegroup';
                    }

                    $storemodules = array('sitestore', 'sitestoreoffer', 'sitestoreproduct', 'sitestorereview', 'sitestorevideo');
                    if (in_array($module, $storemodules)) {
                        $module = 'sitestore';

                        $accountPages = array("sitestoreproduct_wishlist_my-wishlists", "sitestoreproduct_product_my-order", "sitestore_like_mylikes", "sitestoreproduct_index_manage-address");
                        if (in_array($moduleControllerAction, $accountPages))
                            $module = 'sitestore_account';

                        $managePages = array("sitestoreproduct_product_manage", "sitestoreproduct_index_manage-order");
                        if (in_array($moduleControllerAction, $managePages) || ($moduleControllerAction == "sitestoreproduct_index_order-view" && !empty($p['store_id']))) {
                            $module = 'sitestore_manage_mobile';


                            if ($request->getParam("responseFlag") == 1) {
                                return $this->setNoRender();
                            }
                        }
                    }
                    $this->view->navigation = $menusSitemobile->getNavigation($module . '_main');
                    break;
            }
        } else {
            $this->view->navigation = $menusSitemobile->getNavigation($sitemobileNavigationName);
        }
        $countNavigation = count($this->view->navigation);

        if ((!$viewer->getIdentity() && $countNavigation < 2) || empty($sitemobileNavigation)) {
            return $this->setNoRender();
        }

        if (($countNavigation <= 0) || empty($sitemobileNavigation)) {
            return $this->setNoRender();
        }
    }

}

?>