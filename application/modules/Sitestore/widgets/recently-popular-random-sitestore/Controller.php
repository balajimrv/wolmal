<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_RecentlyPopularRandomSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->titleLink = $this->_getParam('titleLink', '');
    $this->view->titleLinkPosition = $this->_getParam('titleLinkPosition', 'bottom');
    $this->view->photoHeight = $this->_getParam('photoHeight', 0);
    $this->view->photoWidth = $this->_getParam('photoWidth', 0);
    
    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if (!$this->_getParam('detactLocation', 0) || $this->_getParam('contentpage', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if ($this->_getParam('detactLocation', 0))
        $this->getElement()->removeDecorator('Title');

      $this->view->is_ajax_load = 1;//!$this->_getParam('loaded_by_ajax', false);
    }    
    
    $this->view->category_id = $category_id = $this->_getParam('category_id',0);
    $storemember = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
    if (!empty($storemember)) {
			$showTabArray = $this->_getParam('layouts_tabs', array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5', "5" => '6'));
    } else {
			$showTabArray = $this->_getParam('layouts_tabs', array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5'));
    }

    $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));

    $defaultOrder = $this->_getParam('layouts_oder', 1);
    $this->view->columnWidth = $this->_getParam('columnWidth', 188);
    $this->view->columnHeight = $this->_getParam('columnHeight', 350);
    $this->view->titlePosition = $this->_getParam('titlePosition', 1);
    $this->view->showlikebutton = $this->_getParam('showlikebutton', 1);
    $this->view->showfeaturedLable = $this->_getParam('showfeaturedLable', 1);
    $this->view->showsponsoredLable = $this->_getParam('showsponsoredLable', 1);
    $this->view->showlocation = $this->_getParam('showlocation', 1);
    $this->view->showprice = $this->_getParam('showprice', 1);
    $this->view->showpostedBy = $this->_getParam('showpostedBy', 1);
    
    $this->view->showdate = $this->_getParam('showdate', 1);
    $this->view->turncation = $this->_getParam('turncation', 15);
    
    $statisticsElement = array("likeCount" , "followCount", "viewCount" , "commentCount");
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
			$statisticsElement['']="reviewCount";
		}
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
			$statisticsElement['']="memberCount";
		}
    $this->view->statistics = $this->_getParam('statistics', $statisticsElement);

    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0);
    $params = array();
    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if($this->view->detactLocation) {
      $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $params['latitude'] = $this->_getParam('latitude', 0);
      $params['longitude'] = $this->_getParam('longitude', 0);
    }    

    $sitestore_most_viewed = Zend_Registry::isRegistered('sitestore_most_viewed') ? Zend_Registry::get('sitestore_most_viewed') : null;
    $this->view->list_view = 0;
    $this->view->grid_view = 0;
    $this->view->map_view = 0;
    $this->view->defaultView = -1;
    $list_limit = 0;
    $grid_limit = 0;
    if (in_array("1", $ShowViewArray)) {
      $this->view->list_view = 1;
      $list_limit = $this->_getParam('list_limit', 10);
      if ($this->view->defaultView == -1 || $defaultOrder == 1)
        $this->view->defaultView = 0;
    }
    if (in_array("2", $ShowViewArray)) {
      $this->view->grid_view = 1;
      $grid_limit = $this->_getParam('grid_limit', 15);
      if ($this->view->defaultView == -1 || $defaultOrder == 2)
        $this->view->defaultView = 1;
    }
    if (in_array("3", $ShowViewArray)) {
      $this->view->map_view = 1;
      $list_limit = $this->_getParam('list_limit', 10);
      if ($this->view->defaultView == -1 || $defaultOrder == 3)
        $this->view->defaultView = 2;
    }
    if (empty($sitestore_most_viewed)) {
      return $this->setNoRender();
    }

    $sitestoreRecently = array();
    $sitestoreViewed = array();
    $sitestoreRandom = array();
    $sitestoreFeatured = array();
    $sitestoreSponosred = array();
    $sitestoreJoined = array();

    $params = array_merge(array('limit' => "1",'category_id' => $category_id), $params);
    if (in_array("1", $showTabArray)) {
      // GET SITESTORE SITESTORE FOR RECENTLY POSTED
      $sitestoreRecently = Engine_Api::_()->sitestore()->getLising('Recently Posted', $params);
    }
    if (in_array("2", $showTabArray)) {
      // GET SITESTORE SITESTORE FOR MOST VIEWES
      $sitestoreViewed = Engine_Api::_()->sitestore()->getLising('Most Viewed', $params);
    }
    if (in_array("3", $showTabArray)) {
      $sitestoreRandom = Engine_Api::_()->sitestore()->getLising('Random', $params);
    }

    if (in_array("4", $showTabArray)) {
      $sitestoreFeatured = Engine_Api::_()->sitestore()->getLising('Featured', $params);
    }

    if (in_array("5", $showTabArray)) {
      $sitestoreSponosred = Engine_Api::_()->sitestore()->getLising('Sponosred', $params);
    }
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
			if (in_array("6", $showTabArray)) {
				$sitestoreJoined = Engine_Api::_()->sitestore()->getLising('Most Joined', $params);
			}
    }
    
    if ((!(count($sitestoreRecently) > 0) && !(count($sitestoreViewed) > 0) && !(count($sitestoreRandom) > 0 ) && !(count($sitestoreFeatured) > 0 ) && !(count($sitestoreSponosred) > 0 )) || ($this->view->defaultView == -1)) {
      return $this->setNoRender();
    }
    
    $this->view->paramsLocation = $this->_getAllParams();

    $tabsOrder = array();
    $tabs = array();
    $menuTabs = array();
    if (count($sitestoreRecently) > 0) {
      $tabs['recent'] = array('title' => 'Recent', 'tabShow' => 'Recently Posted');
      $tabsOrder['recent'] = $this->_getParam('recent_order', 1);
    }
    if (count($sitestoreViewed) > 0) {
      $tabs['popular'] = array('title' => 'Most Popular', 'tabShow' => 'Most Viewed');
      $tabsOrder['popular'] = $this->_getParam('popular_order', 2);
    }
    if (count($sitestoreRandom) > 0) {
      $tabs['random'] = array('title' => 'Random', 'tabShow' => 'Random');
      $tabsOrder['random'] = $this->_getParam('random_order', 3);
    }

    if (count($sitestoreFeatured) > 0) {
      $tabs['featured'] = array('title' => 'Featured', 'tabShow' => 'Featured');
      $tabsOrder['featured'] = $this->_getParam('featured_order', 4);
    }
    if (count($sitestoreSponosred) > 0) {
      $tabs['sponosred'] = array('title' => 'Sponsored', 'tabShow' => 'Sponosred');
      $tabsOrder['sponosred'] = $this->_getParam('sponosred_order', 5);
    }
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
			if (count($sitestoreJoined) > 0) {
				$tabs['mostjoined'] = array('title' => 'Most Joined', 'tabShow' => 'Most Joined');
				$tabsOrder['mostjoined'] = $this->_getParam('joined_order', 6);
			}
    }
    
    @asort($tabsOrder);
    $firstIndex = key($tabsOrder);
    foreach ($tabsOrder as $key => $value) {
      $menuTabs[$key] = $tabs[$key];
    }

    $params['is_ajax_load123'] = $this->view->is_ajax_load;
    $this->view->tabs = $menuTabs;
    $this->view->active_tab_list = $list_limit;
    $this->view->active_tab_image = $grid_limit;
    $params['limit'] = $limit = $list_limit > $grid_limit ? $list_limit : $grid_limit;
    $this->view->sitestoresitestore = $sitestore = Engine_Api::_()->sitestore()->getLising($menuTabs[$firstIndex]['tabShow'], $params);

//    if (count($sitestoreRecently) > 0) {
//      $this->view->sitestoresitestore = $sitestore = $sitestoreRecently = Engine_Api::_()->sitestore()->getLising('Recently Posted');
////      $this->view->active_tab_list = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.recent.widgets', 10);
////      $this->view->active_tab_image = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.recent.thumbs', 15);
//    } else if (count($sitestoreViewed) > 0) {
//      $this->view->sitestoresitestore = $sitestore = $sitestoreViewed = Engine_Api::_()->sitestore()->getLising('Most Viewed');
//
////      $this->view->active_tab_list = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.popular.widgets', 10);
////      $this->view->active_tab_image = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.popular.thumbs', 15);
//    } else if (count($sitestoreRandom) > 0) {
//      $this->view->sitestoresitestore = $sitestore = $sitestoreRandom = Engine_Api::_()->sitestore()->getLising('Random');
//
////      $this->view->active_tab_list = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.random.widgets', 10);
////      $this->view->active_tab_image = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.random.thumbs', 15);
//    } else if (count($sitestoreFeatured) > 0) {
//      $this->view->sitestoresitestore = $sitestore = $sitestoreFeatured = Engine_Api::_()->sitestore()->getLising('Featured');
//
////      $this->view->active_tab_list = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.featured.list', 10);
////      $this->view->active_tab_image = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.featured.thumbs', 15);
//    } else if (count($sitestoreSponosred) > 0) {
//      $this->view->sitestoresitestore = $sitestore = $sitestoreSponosred = Engine_Api::_()->sitestore()->getLising('Sponosred');
//      ;
//
////      $this->view->active_tab_list = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.list', 10);
////      $this->view->active_tab_image = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.thumbs', 15);
//    }

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitestore()->enableLocation();
    $this->view->sitestore = '';

    if (!empty($this->view->map_view)) {

      $this->view->flageSponsored = 0;

      if (!empty($checkLocation)) {
        $ids = array();
        $sponsored = array();
        foreach ($sitestore as $sitestore_store) {
          $id = $sitestore_store->getIdentity();
          $ids[] = $id;
          $sitestore_temp[$id] = $sitestore_store;
        }
        $values['store_ids'] = $ids;

        $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocation($values);
        foreach ($locations as $location) {
          if ($sitestore_temp[$location->store_id]->sponsored) {
            $this->view->flageSponsored = 1;
            break;
          }
        }
        $this->view->sitestore = $sitestore_temp;
      }
    }

    //STORE-RATING IS ENABLE OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
  }

}

?>
