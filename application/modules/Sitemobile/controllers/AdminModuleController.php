<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminModuleController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_AdminModuleController extends Core_Controller_Action_Admin {

  protected $_modulestable;
  protected $_menuitemTable;

  public function init() {
    $this->_modulestable = Engine_Api::_()->getDbtable('modules', 'sitemobile');
    $this->_menuitemTable = Engine_Api::_()->getDbtable('menuItems', 'sitemobile');
  }

  public function manageAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitemobile_admin_main', array(), 'sitemobile_admin_main_module');

    // Get list of all modules from sitemobile module table which are enabled in core module table.
    $this->view->modulesList = $modules = $this->_modulestable->getManageModulesList();
  }

  //TO DISABLE/ENABLE ANY MODULE.
  public function enableMobileAction() {
    set_time_limit(0);
    //Get params enabled & name to identify the module and its corresponding disable or enable action.
    $this->view->enable_mobile = $enable_mobile = $this->_getParam('enable_mobile');
    $this->view->name = $moduleName = $this->_getParam('name');
    $this->view->integrated = $integrated = $this->_getParam('integrated');
    $integratedForApp = $this->_getParam('integratedForApp', 1);
    // Check stuff
//    if (!$this->getRequest()->isPost()) {
//      return;
//    }

    if(empty($integrated) && $enable_mobile == '1' && $moduleName =='sitealbum'){
      if(Engine_Api::_()->hasModuleBootstrap('sitemobileapp')){
        $disableArray = array('enable_mobile' => '0','enable_tablet' => '0','enable_mobile_app' => '0','enable_tablet_app' => '0');}else{
        $disableArray = array('enable_mobile' => '0','enable_tablet' => '0');
      }
      $this->_menuitemTable->update($disableArray, array(
          'name = ?' => 'core_main_album'
      ));
    }
    if (empty($integrated) && ($enable_mobile == '1')) {
      Engine_Api::_()->getApi('modules', 'sitemobile')->addModuleStart($moduleName);
    } elseif (empty($integratedForApp) && ($enable_mobile == '1')) {
      Engine_Api::_()->getApi('modules', 'sitemobile')->addModulesOnlyForApp($moduleName);
    } else {
      //update enabled variable to change its mode.
      $this->_modulestable->update(array(
          'enable_mobile' => $enable_mobile,
              ), array(
          'name = ?' => $moduleName
      ));
      if($moduleName == 'sitemobileandroidapp' || $moduleName == 'sitemobileiosapp'){
         $sitemobileapp = $this->_getParam('sitemobileapp');
         if($sitemobileapp == '1'){
          $this->_modulestable->update(array(
           'enable_mobile' => $enable_mobile,
               ), array(
           'name = ?' => 'sitemobileapp'
          ));         
         }
      }
    }

    $modulesList = $this->_modulestable->getExtensionsList(array('modulename' => $moduleName));
    //Disable all the Page plugin extension which are enabled.
    foreach ($modulesList as $module) {
      $moduleName = $module->name;
      if (empty($integrated) && ($enable_mobile == '1')) {
        Engine_Api::_()->getApi('modules', 'sitemobile')->addModuleStart($moduleName);
      } elseif (empty($integratedForApp) && ($enable_mobile == '1')) {
        Engine_Api::_()->getApi('modules', 'sitemobile')->addModulesOnlyForApp($moduleName);
      } else {
        //update enabled variable to change its mode.
        $this->_modulestable->update(array(
            'enable_mobile' => $enable_mobile,
                ), array(
            'name = ?' => $moduleName
        ));
      }
    }

    //START PAGE PLUGIN LANGUAGE WORK.
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
      //START LANGUAGE WORK
      Engine_Api::_()->getApi('language', 'sitepage')->languageChanges();
      //END LANGUAGE WORK
			if(Engine_Api::_()->sitepage()->getMobileWidgetizedPage()) {
				$mobilepage_id = Engine_Api::_()->sitepage()->getMobileWidgetizedPage()->page_id;
				if($mobilepage_id) {
					Engine_Api::_()->getDbtable('mobileadmincontent', 'sitepage')->setAdminDefaultInfo('siteevent.contenttype-events', $mobilepage_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"50","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');

					Engine_Api::_()->getApi('mobilelayoutcore', 'sitepage')->setContentDefaultInfo('siteevent.contenttype-events', $mobilepage_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"50","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
				}
				//GET SITEPAGE CONTENT TABLE
				$sitepagemobilecontentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitepage');
				//GET SITEPAGE CONTENT PAGES TABLE
				$sitepagemobilecontentpagesTable = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
				$selectsitepagemobilePage = $sitepagemobilecontentpagesTable->select()
							->from($sitepagemobilecontentpagesTable->info('name'), array('mobilecontentpage_id'))
							->where('name =?', 'sitepage_index_view');
				$contentmobilepages_id = $selectsitepagemobilePage->query()->fetchAll();
				foreach ($contentmobilepages_id as $key => $value) {
					$sitepagemobilecontentTable->setDefaultInfo('siteevent.contenttype-events', $value['mobilecontentpage_id'], 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"50","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
				}
			}
    }
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness')) {
      //START LANGUAGE WORK
      Engine_Api::_()->getApi('language', 'sitebusiness')->languageChanges();
      //END LANGUAGE WORK

      //END LANGUAGE WORK
			if(Engine_Api::_()->sitebusiness()->getMobileWidgetizedBusiness()) {
				$mobilebusiness_id = Engine_Api::_()->sitebusiness()->getMobileWidgetizedBusiness()->page_id;
				if($mobilebusiness_id) {
					Engine_Api::_()->getDbtable('mobileadmincontent', 'sitebusiness')->setAdminDefaultInfo('siteevent.contenttype-events', $mobilebusiness_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"50","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');

					Engine_Api::_()->getApi('mobilelayoutcore', 'sitebusiness')->setContentDefaultInfo('siteevent.contenttype-events', $mobilebusiness_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"50","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
				}
				//GET SITEPAGE CONTENT TABLE
				$sitebusinessmobilecontentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitebusiness');
				//GET SITEPAGE CONTENT PAGES TABLE
				$sitebusinessmobilecontentbusinesssTable = Engine_Api::_()->getDbtable('mobileContentbusinesses', 'sitebusiness');
				$selectsitebusinessmobilePage = $sitebusinessmobilecontentbusinesssTable->select()
							->from($sitebusinessmobilecontentbusinesssTable->info('name'), array('mobilecontentbusiness_id'))
							->where('name =?', 'sitebusiness_index_view');
				$contentmobilebusinesss_id = $selectsitebusinessmobilePage->query()->fetchAll();
				foreach ($contentmobilebusinesss_id as $key => $value) {
					$sitebusinessmobilecontentTable->setDefaultInfo('siteevent.contenttype-events', $value['mobilecontentbusiness_id'], 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"50","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
				}
			}

    }
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup')) {
      //START LANGUAGE WORK
      Engine_Api::_()->getApi('language', 'sitegroup')->languageChanges();
      //END LANGUAGE WORK
			if(Engine_Api::_()->sitegroup()->getMobileWidgetizedGroup()) {
				$mobilegroup_id = Engine_Api::_()->sitegroup()->getMobileWidgetizedGroup()->page_id;
				if($mobilegroup_id) {
					Engine_Api::_()->getDbtable('mobileadmincontent', 'sitegroup')->setAdminDefaultInfo('siteevent.contenttype-events', $mobilegroup_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"50","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');

					Engine_Api::_()->getApi('mobilelayoutcore', 'sitegroup')->setContentDefaultInfo('siteevent.contenttype-events', $mobilegroup_id, 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"50","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
				}
				//GET SITEPAGE CONTENT TABLE
				$sitegroupmobilecontentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitegroup');
				//GET SITEPAGE CONTENT PAGES TABLE
				$sitegroupmobilecontentgroupsTable = Engine_Api::_()->getDbtable('mobileContentgroups', 'sitegroup');
				$selectsitegroupmobilePage = $sitegroupmobilecontentgroupsTable->select()
							->from($sitegroupmobilecontentgroupsTable->info('name'), array('mobilecontentgroup_id'))
							->where('name =?', 'sitegroup_index_view');
				$contentmobilegroups_id = $selectsitegroupmobilePage->query()->fetchAll();
				foreach ($contentmobilegroups_id as $key => $value) {
					$sitegroupmobilecontentTable->setDefaultInfo('siteevent.contenttype-events', $value['mobilecontentgroup_id'], 'Events', 'true', '117', '{"title":"Events","titleCount":true,"layouts_views":["1","2","3"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"columnWidth":"199","truncationGrid":"90","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventFilterTypes":["upcoming","past"],"eventOwnerType":["lead","host"],"columnHeight":"330","eventInfo":["hostName","categoryLink","featuredLabel","newLabel","startDate","venueName","location","directionLink","viewCount","likeCount","memberCount"],"titlePosition":"1","descriptionPosition":"0","itemCount":"12","truncationLocation":"50","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.contenttype-events"}');
				}
			}
    }
    //END PAGE PLUGIN LANGUAGE WORK.

    $redirect = $this->_getParam('redirect', false);
    if (!$redirect) {
      $this->_redirect('admin/sitemobile/module/manage');
    } else {
      $this->_redirect('install/manage');
    }
  }

  //TO DISABLE/ENABLE ANY MODULE.
  public function enableMobileAppAction() {

    //Get params enabled & name to identify the module and its corresponding disable or enable action.
    $this->view->enable_mobile_app = $enable_mobile_app = $this->_getParam('enable_mobile_app');
    $this->view->name = $moduleName = $this->_getParam('name');
    $this->view->integrated = $integrated = $this->_getParam('integrated');

    if (empty($integrated) && ($enable_mobile_app == '1')) {
      Engine_Api::_()->getApi('modules', 'sitemobile')->addModuleStart($moduleName);
    } else {
      //update enabled variable to change its mode.
      $this->_modulestable->update(array(
          'enable_mobile_app' => $enable_mobile_app,
              ), array(
          'name = ?' => $moduleName
      ));
    }

    $modulesList = $this->_modulestable->getExtensionsList(array('modulename' => $moduleName));
    foreach ($modulesList as $module) {
      $moduleName = $module->name;
      if (empty($integrated) && ($enable_mobile_app == '1')) {
        Engine_Api::_()->getApi('modules', 'sitemobile')->addModuleStart($moduleName);
      } else {
        //update enabled variable to change its mode.
        $this->_modulestable->update(array(
            'enable_mobile_app' => $enable_mobile_app,
                ), array(
            'name = ?' => $moduleName
        ));
      }
    }

    $redirect = $this->_getParam('redirect', false);
    if (!$redirect) {
      $this->_redirect('admin/sitemobile/module/manage');
    } else {
      $this->_redirect('install/manage');
    }
  }

  //TO DISABLE/ENABLE ANY MODULE.
  public function enableTabletAction() {

    //Get params enabled & name to identify the module and its corresponding disable or enable action.
    $this->view->enable_tablet = $enable_tablet = $this->_getParam('enable_tablet');
    $this->view->name = $moduleName = $this->_getParam('name');
    $this->view->integrated = $integrated = $this->_getParam('integrated');

    if (empty($integrated) && ($enable_tablet == '1')) {
      $apiModuleObject = Engine_Api::_()->getApi('modules', 'sitemobile')->addModuleStart($moduleName);
    } else {
      //update enabled variable to change its mode.
      $this->_modulestable->update(array(
          'enable_tablet' => $enable_tablet,
              ), array(
          'name = ?' => $moduleName
      ));
    }
    $modulesList = $this->_modulestable->getExtensionsList(array('modulename' => $moduleName));
    foreach ($modulesList as $module) {
      $moduleName = $module->name;
      if (empty($integrated) && ($enable_tablet == '1')) {
        $apiModuleObject = Engine_Api::_()->getApi('modules', 'sitemobile')->addModuleStart($moduleName);
      } else {
        //update enabled variable to change its mode.
        $this->_modulestable->update(array(
            'enable_tablet' => $enable_tablet,
                ), array(
            'name = ?' => $moduleName
        ));
      }
    }

    $this->_redirect('admin/sitemobile/module/manage');
  }

  //TO DISABLE/ENABLE ANY MODULE.
  public function enableTabletAppAction() {

    //Get params enabled & name to identify the module and its corresponding disable or enable action.
    $this->view->enable_tablet_app = $enable_tablet_app = $this->_getParam('enable_tablet_app');
    $this->view->name = $moduleName = $this->_getParam('name');
    $this->view->integrated = $integrated = $this->_getParam('integrated');


    if (empty($integrated) && ($enable_tablet_app == '1')) {
      $apiModuleObject = Engine_Api::_()->getApi('modules', 'sitemobile')->addModuleStart($moduleName);
    } else {
      //update enabled variable to change its mode.
      $this->_modulestable->update(array(
          'enable_tablet_app' => $enable_tablet_app,
              ), array(
          'name = ?' => $moduleName
      ));
    }
    $modulesList = $this->_modulestable->getExtensionsList(array('modulename' => $moduleName));
    foreach ($modulesList as $module) {
      $moduleName = $module->name;
      if (empty($integrated) && ($enable_tablet_app == '1')) {
        $apiModuleObject = Engine_Api::_()->getApi('modules', 'sitemobile')->addModuleStart($moduleName);
      } else {
        //update enabled variable to change its mode.
        $this->_modulestable->update(array(
            'enable_tablet_app' => $enable_tablet_app,
                ), array(
            'name = ?' => $moduleName
        ));
      }
    }
    $this->_redirect('admin/sitemobile/module/manage');
  }

}