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
class Sitestore_Widget_StoresSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    
    if (isset($params['is_ajax_load']))
      unset($params['is_ajax_load']);

    if ($this->_getParam('is_ajax_load', false)) {
      $this->view->is_ajax_load = true;
      if ($this->_getParam('page', 1) > 1)
        $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    } else {
      if (!$this->_getParam('detactLocation', 0)) {
        $this->view->is_ajax_load = true;
      } else {
        $this->getElement()->removeDecorator('Title');
        $this->view->is_ajax_load = $this->_getParam('loaded_by_ajax', false);
      }
    }
    if (empty($this->view->is_ajax_load)) {
      $cookieLocation = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
      if (isset($cookieLocation['location']) && !empty($cookieLocation['location'])) {
        $this->view->is_ajax_load = 1;
      }elseif(empty ($cookieLocation)){
        $this->view->is_ajax_load = 1;
      }
    } 

    
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->sitestore_browse = $sitestore_browse = Zend_Registry::isRegistered('sitestore_browse') ? Zend_Registry::get('sitestore_browse') : null;
    $ShowViewArray = $params['layouts_views'] = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));
    $this->view->is_ajax = $isAjax = $this->_getParam('isajax', 0);

    //START VIEW MORE LINK AND AUTOSCROLL CONTENT WORK
    if(!empty($isAjax)) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    //END VIEW MORE LINK AND AUTOSCROLL CONTENT WORK

    $defaultOrder = $params['layouts_oder'] = $this->_getParam('layouts_oder', 1);
    $this->view->columnWidth = $params['columnWidth'] = $this->_getParam('columnWidth', 188);
    $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', 350);
    $this->view->showlikebutton = $params['showlikebutton'] = $this->_getParam('showlikebutton', 1);
    $this->view->showfeaturedLable = $params['showfeaturedLable'] = $this->_getParam('showfeaturedLable', 1);
    $this->view->showsponsoredLable = $params['showsponsoredLable'] = $this->_getParam('showsponsoredLable', 1);
    $this->view->showlocation = $params['showlocation'] = $this->_getParam('showlocation', 1);
    $this->view->showprice = $params['showprice'] = $this->_getParam('showprice', 1);
    $this->view->showpostedBy = $params['showpostedBy'] = $this->_getParam('showpostedBy', 1);
    $this->view->showdate = $params['showdate'] = $this->_getParam('showdate', 1);
    $this->view->turncation = $params['turncation'] = $this->_getParam('turncation', 15);
    $this->view->showContactDetails = $params['showContactDetails'] = $this->_getParam('showContactDetails', 1);
    $this->view->showContent = $params['show_content'] = $this->_getParam('show_content', 2);
    
    $statisticsElement = array("likeCount" , "followCount", "viewCount" , "commentCount");
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
			$statisticsElement['']="reviewCount";
		}
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
			$statisticsElement['']="memberCount";
		}
    $this->view->statistics = $params['statistics'] = $this->_getParam('statistics', $statisticsElement);

    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0);
    $this->view->list_view = 0;
    $this->view->grid_view = 0;
    $this->view->map_view = 0;
    $this->view->defaultView = -1;
    if (in_array("1", $ShowViewArray)) {
      $this->view->list_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 1)
        $this->view->defaultView = 0;
    }
    if (in_array("2", $ShowViewArray)) {
      $this->view->grid_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 2)
        $this->view->defaultView = 1;
    }
    if (in_array("3", $ShowViewArray)) {
      $this->view->map_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 3)
        $this->view->defaultView = 2;
    }

    if ($this->view->defaultView == -1) {
      return $this->setNoRender();
    }
    $customFieldValues = array();
    $values = array();
    $select_category = $params['category_id'] = $this->_getParam('category_id',0);
    if(!empty($select_category) && empty($_GET['category'])) {
      $category = $select_category;
      $category_id = $select_category;
    }
    else {
			$category = $params['category_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
      $category_id = $params['category'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('category', null);
    }
    $subcategory = $params['subcategory_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory_id', null);
    $subcategory_id = $params['subcategory'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory', null);
    $categoryname = $params['categoryname'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
    $subcategoryname = $params['subcategoryname'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
    $subsubcategory = $params['subsubcategory_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory_id', null);
    $subsubcategory_id = $params['subsubcategory'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory', null);
    $subsubcategoryname = $params['subsubcategoryname'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategoryname', null);

    if ($category)
      $this->view->category = $_GET['category'] = $category;
    if ($subcategory)
      $this->view->subcategory = $_GET['subcategory'] = $subcategory;
    if ($categoryname)
      $this->view->categoryname = $_GET['categoryname'] = $categoryname;
    if ($subcategoryname)
      $this->view->subcategoryname = $_GET['subcategoryname'] = $subcategoryname;

    if ($subsubcategory)
       $this->view->subsubcategory = $_GET['subsubcategory'] = $subsubcategory;
    if ($subcategoryname)
     $this->view->subsubcategoryname =  $_GET['subsubcategoryname'] = $subsubcategoryname;

    if ($category_id)
      $this->view->category =  $_GET['category'] = $values['category'] = $category_id;
    if ($subcategory_id)
      $this->view->subcategory = $_GET['subcategory'] = $values['subcategory'] = $subcategory_id;
    if ($subsubcategory_id)
     $this->view->subsubcategory = $_GET['subsubcategory'] = $values['subsubcategory'] = $subsubcategory_id;
    $this->view->tag = $values['tag'] = $params['tag'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('tag', null);
    if (!empty($values['tag']))
      $this->view->tag = $_GET['tag'] = $values['tag'];
    
    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
      $this->view->tag = $tag = $_GET['tag'];
      $store = 1;
      if (isset($_GET['store']) && !empty($_GET['store'])) {
        $store = $_GET['store'];
      }
      unset($_GET);
      $this->view->tag = $_GET['tag'] = $tag;
      $_GET['store'] = $store;
    }

    //GET VALUE BY POST TO GET DESIRED SITESTORES
    if (!empty($_GET)) {
      $values = $_GET;
    }

    $this->view->params = $params;
    
    //FORM GENERATION
    $form = new Sitestore_Form_Search(array('type' => 'sitestore_store'));

    if (!empty($_GET))
      $form->populate($_GET);
    $values = $form->getValues();

		//STORE OFFER IS INSTALLED OR NOT
		$this->view->sitestoreOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer');

    //BADGE CODE
    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge')) {
      if (isset($_POST['badge_id']) && !empty($_POST['badge_id'])) {
        $values['badge_id'] = $_POST['badge_id'];
      }
    }

//     if (($category) != null) {
//       $this->view->category = $values['category'] = $category;
//       $this->view->subcategory = $values['subcategory'] = $subcategory;
//       $this->view->subsubcategory = $values['subsubcategory'] = $subsubcategory;
//     } else {
//       $values['category'] = 0;
//       $values['subcategory'] = 0;
//       $values['subsubcategory'] = 0;
//     }
// 
//     if (($category_id) != null) {
//       $this->view->category_id = $values['category'] = $category_id;
//       $this->view->subcategory_id = $values['subcategory'] = $subcategory_id;
//       $this->view->subsubcategory_id = $values['subsubcategory'] = $subsubcategory_id;
//     } else {
//       $values['category'] = 0;
//       $values['subcategory'] = 0;
//       $values['subsubcategory'] = 0;
//     }

    if (isset($params['page']) && !empty($params['page']))
      $values['page'] = $params['page'];
    else
      $values['page'] = 1;

    //GET LISITNG FPR PUBLIC STORE SET VALUE
    $values['type'] = 'browse';
    $values['type_location'] = 'browseStore';

    if (@$values['show'] == 2) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }

      $values['users'] = $ids;
    }
    
    if ($request->getParam('titleAjax')) { 
      $values['search'] = $request->getParam('titleAjax');
    }     

    //GEO-LOCATION WORK
    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoregeolocation') && isset($values['has_currentlocation']) && !empty($values['has_currentlocation'])) {

      $session = new Zend_Session_Namespace('Current_location');
      if (!isset($session->latitude) || !isset($session->longitude)) {
        $locationResult = null;
        $apiType = Engine_Api::_()->getApi('core', 'sitestoregeolocation')->getGeoApiType();
        if ($apiType == 1) {
          $locationResult = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getMaxmindCurrentLocation();
        } elseif ($apiType == 2) {
          $locationResult = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getMaxmindGeoLiteCountry();
        }
        if (($apiType == 1 || $apiType == 2) && !empty($locationResult)) {
          $this->view->latitude = $values['latitude'] = $session->latitude = $locationResult['latitude'];
          $this->view->longitude = $values['longitude'] = $session->longitude = $locationResult['longitude'];
        }
      } else {
        $this->view->latitude = $values['latitude'] = $session->latitude;
        $this->view->longitude = $values['longitude'] = $session->longitude;
      }
    }
    $this->view->assign($values);

    //STORE-RATING IS ENABLED OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');

    //CUSTOM FIELD WORK
    $customFieldValues = array_intersect_key($values, $form->getFieldElements());
    if ($form->show->getValue() == 3 && !isset($_GET['show'])) {
      @$values['show'] = 3;
    }

		//DON'T SEND CUSTOM FIELDS ARRAY IF EMPTY
		$has_value = 0;
		foreach($customFieldValues as $customFieldValue) {
			if(!empty($customFieldValue)) {
				$has_value = 1;
				break;
			}
		}

		if(empty($has_value)) {
			$customFieldValues = null;
		}

		$values['browse_store'] = 1;
  
   
    $this->view->detactLocation = $values['detactLocation'] = $this->_getParam('detactLocation', 0);
    if ($this->view->detactLocation) {
      $this->view->defaultLocationDistance = $values['defaultLocationDistance'] = $values['locationmiles'] = $this->_getParam('defaultLocationDistance', 1000);
      $values['latitude'] = $this->_getParam('latitude', 0);
      $values['longitude'] = $this->_getParam('longitude', 0);
    }

    if (!$this->view->detactLocation && empty($_GET['sitestore_location']) && empty($_GET['locationSearch'])) {
      unset($values['sitestore_location']);
      if (empty($_GET['latitude']) && isset($values['latitude'])) {
       unset($values['latitude']);
     }

      if (empty($_GET['longitude']) && isset($values['longitude'])) {
        unset($values['longitude']);
      }

      if (empty($_GET['Latitude']) && isset($values['Latitude'])) {
        unset($values['Latitude']);
      }

      if (empty($_GET['Longitude']) && isset($values['Longitude'])) {
        unset($values['Longitude']);
      }
    }  
   

    // GET SITESTORES
    $this->view->paginator = $paginator = Engine_Api::_()->sitestore()->getSitestoresPaginator($values, $customFieldValues);

    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.store', 10);
    $this->view->totalCount = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($items_count);
    $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
    $this->view->sitestore_generic = Zend_Registry::isRegistered('sitestore_generic') ? Zend_Registry::get('sitestore_generic') : null;

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitestore()->enableLocation();
    $this->view->flageSponsored = 0;

    if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {
      $ids = array();
      $sponsored = array();
      foreach ($paginator as $sitestore) {
        $id = $sitestore->getIdentity();
        $ids[] = $id;
        $sitestore_temp[$id] = $sitestore;
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
    } else {
      $this->view->enableLocation = 0;
    }
    if (empty($categoryname)) {
      $_GET['category'] = $this->view->category_id = 0;
      $_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
      $_GET['categoryname'] = 0;
      $_GET['subcategoryname'] = 0;
      $_GET['subsubcategoryname'] = 0;
    }

    $this->view->search = 0;
    if (!empty($_GET)) {
      $this->view->search = 1;
    }
     
    
    $this->view->paramsLocation = array_merge($_GET, $this->_getAllParams());
    $this->view->paramsLocation = array_merge($request->getParams(), $this->view->paramsLocation);
 
    if (isset($_GET['search']) || isset($_POST['search'])) {
      $this->view->detactLocation = 0;
    } else {
      $this->view->detactLocation = $this->_getParam('detactLocation', 0);
    }    
    
    //CAN CREATE STORES OR NOT
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitestore_store', $viewer, 'create');
    $this->view->formValues = $values;
    if (empty($sitestore_browse)) {
      return $this->setNoRender();
    }
  }

}

?>

