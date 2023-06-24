<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_IndexController extends Seaocore_Controller_Action_Standard {

  protected $_navigation;

  //SET THE VALUE FOR ALL ACTION DEFAULT
  public function init() {

    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->isValid())
      return;

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();

    //GET STORE ID AND STORE URL
    $store_url = $this->_getParam('store_url', $this->_getParam('store_url', null));
    $store_id = $this->_getParam('store_id', $this->_getParam('store_id', null));

    if ($store_url) {
      $store_id = Engine_Api::_()->sitestore()->getStoreId($store_url);
    }
    if ($store_id) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      if ($sitestore) {
        Engine_Api::_()->core()->setSubject($sitestore);
      }
    }

    //FOR UPDATE EXPIRATION
    if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.task.updateexpiredstores') + 900) <= time()) {
      Engine_Api::_()->sitestore()->updateExpiredStores();
    }
  }

  //ACTION FOR SHOWING THE STORE LIST
  public function indexAction() {

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    }
  }

  //ACTION FOR SHOWING THE STORE LIST
  public function pinboardBrowseAction() {

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setContentName("sitestore_index_pinboard_browse")
              ->setEnabled();
    }
  }

  //ACTION FOR SHOWING THE HOME STORE
  public function homeAction() {

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled();
    }
  }

  //ACTION FOR BROWSE LOCATION STORES.
  public function mapAction() {

    $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1);

    if (empty($enableLocation)) {
      return $this->_forwardCustom('notfound', 'error', 'core');
    } else {
      $this->_helper->content->setEnabled();
    }
  }

  //ACTION FOR BROWSE LOCATION STORES.
  public function mobilemapAction() {

    $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1);

    if (empty($enableLocation)) {
      return $this->_forwardCustom('notfound', 'error', 'core');
    } else {
      $this->_helper->content->setEnabled();
    }
  }

  //ACTION FOR SHOWING SPONSERED STORE AT HOME STORE
  public function homeSponsoredAction() {

    //RETURN THE OBJECT OF LIMIT PER STORE FROM CORE SETTING TABLE
    $limit_sitestore = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponserdsitestore.widgets', 4);
    $limit_sitestore_horizontal = $limit_sitestore * 2;

    $totalSitestore = Engine_Api::_()->sitestore()->getLising('Total Sponsored Sitestore');

    // Total Count Sponsored Store
    $totalCount = $totalSitestore->count();

    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'];

    if ($startindex > $totalCount) {
      $startindex = $totalCount - $limit_sitestore;
    }

    if ($startindex < 0) {
      $startindex = 0;
    }

    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $direction = $_GET['direction'];
    $values['start_index'] = $startindex;
    $values['totalstores'] = $_GET['limit'];
    $values['category_id'] = $_GET['category_id'];
    $this->view->titletruncation = $_GET['titletruncation'];
    $this->view->totalstores = $_GET['limit'];
    // Sitestore Sitestore Sponsored
    $this->view->sitestoresitestore = $result = Engine_Api::_()->sitestore()->getLising('Sponsored Sitestore AJAX', $values);

    //RUN THE SELECT QUERY
    // get the base url
    $this->view->Featured_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

    //Pass the total number of result in tpl file
    $this->view->count = count($result);

    //Pass the direction of button in tpl file
    $this->view->direction = $direction;
  }

  //ACTION FOR SHOWING SPONSORED LISTINGS IN WIDGET
  public function ajaxCarouselAction() {

    //CORE SETTINGS API
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //SEAOCORE API
    $this->view->seacore_api = Engine_Api::_()->seaocore();

    //RETURN THE OBJECT OF LIMIT PER STORE FROM CORE SETTING TABLE
    $this->view->sponserdSitestoresCount = $limit_sitestore = $_GET['curnt_limit'];
    $limit_sitestore_horizontal = $limit_sitestore * 2;

    $values = array();
    $values = $this->_getAllParams();

    //GET COUNT
    $totalCount = $_GET['total'];

    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'];

    if ($startindex > $totalCount) {
      $startindex = $totalCount - $limit_sitestore;
    }

    if ($startindex < 0) {
      $startindex = 0;
    }

    $this->view->sponsoredIcon = $this->_getParam('sponsoredIcon', 1);
    //$this->view->showOptions = $this->_getParam('showOptions', array("category", "rating", "review"));
    $this->view->featuredIcon = $this->_getParam('featuredIcon', 1);
    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $this->view->direction = $_GET['direction'];
    $values['start_index'] = $startindex;
    $sitestoreTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $this->view->totalItemsInSlide = $values['limit'] = $limit_sitestore_horizontal;
    $this->view->popularity = $values['popularity'] = $this->_getParam('popularity', 'store_id');
    $this->view->fea_spo = $fea_spo = $this->_getParam('fea_spo', null);
    if ($fea_spo == 'featured') {
      $values['featured'] = 1;
    } elseif ($fea_spo == 'sponsored') {
      $values['sponsored'] = 1;
    } elseif ($fea_spo == 'fea_spo') {
      $values['sponsored'] = 1;
      $values['featured'] = 1;
    }

    //GET LISTINGS
    $this->view->sitestores = $sitestoreTable->getListing('', $values);
    $this->view->count = count($this->view->sitestores);
    $this->view->vertical = $_GET['vertical'];
    $this->view->ratingType = $this->_getParam('ratingType', 'rating');
    $this->view->title_truncation = $this->_getParam('title_truncation', 50);
    $this->view->blockHeight = $this->_getParam('blockHeight', 245);
    $this->view->blockWidth = $this->_getParam('blockWidth', 150);
    $this->view->statistics = Zend_Json_Decoder::decode($this->_getParam('statistics'));
  }

  //ACTION FOR STORE PROFILE STORE
  public function viewAction() {
    if (!Engine_Api::_()->core()->hasSubject())
      return $this->_forwardCustom('notfound', 'error', 'core');
    //RETURN IF SUBJECT IS NOT SET
    if (!$this->_helper->requireSubject('sitestore_store')->isValid())
      return;

    //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        if(!Zend_Registry::isRegistered('sitemobileNavigationName')){
        Zend_Registry::set('sitemobileNavigationName','setNoRender');
        }
    }
    
    //VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET SUBJECT AND STORE ID
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    $store_id = $sitestore->store_id;

    $this->view->headLink()
            ->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/style_sitestore_profile.css');

//    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup'))) {
//      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/common_style_store_store_group.css');
//    } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore')) {
//      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/common_style_store_store.css');
//    } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup')) {
//      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/common_style_store_group.css');
//    } else {
      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/style_sitestore.css');
   // }

    if (!$sitestore->all_post && !Engine_Api::_()->sitestore()->isStoreOwner($sitestore)) {
      $this->view->headStyle()->appendStyle(".activity-post-container{
    display:none;
    }");
      $this->view->headStyle()->appendStyle(".adv_post_container_box{
    display:none;
    }");
    }
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
      $this->view->headLink()
              ->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitestorealbum/externals/styles/style_sitestorealbum.css'
      );
    }

    $edit_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
 
      //OPEN TAB IN NEW PAGE
     if (!$edit_layout_setting && $this->renderWidgetCustom())     
      return;
    
    //OPEN TAB IN NEW PAGE
     if ($edit_layout_setting && $this->renderUserWidgetCustom())     
      return;
     
    //CALL FUNCTION FOR INCRESING THE MEMORY LIMIT
    $this->setPhpIniMemorySize();
    $maxView = 19;

    //Start store member work for privacy.
    $this->view->sitestoreMemberEnabled = $sitestoreMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
    if (!empty($sitestoreMemberEnabled)) {
      $this->view->member_approval = $sitestore->member_approval;
      $this->view->hasMembers = Engine_Api::_()->getDbTable('membership', 'sitestore')->hasMembers($viewer_id, $sitestore->store_id);

      //START MANAGE-ADMIN CHECK
      $this->view->viewPrivacy = $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
      if (empty($isManageAdmin)) {
        return;
      }

      //STORE VIEW AUTHORIZATION
      if (!Engine_Api::_()->sitestore()->canViewStore($sitestore)) {
        return;
      }
    } else {
      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
      if (empty($isManageAdmin)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }

      //STORE VIEW AUTHORIZATION
      if (!Engine_Api::_()->sitestore()->canViewStore($sitestore)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //End store member work for privacy.
    //INCREMENT IN NUMBER OF VIEWS
    $owner = $sitestore->getOwner();    

    //INCREMENT STORE VIEWS DATE-WISE
//    $values = array('store_id' => $sitestore->store_id);
//    $sub_status_table = Engine_Api::_()->getDbTable('storestatistics', 'sitestore');
//    $statObject = $sub_status_table->storeReportInsights($values);
//    $raw_views = $sub_status_table->fetchRow($statObject);
//    $raw_views_count = $raw_views['views'];
//    if (!$owner->isSelf($viewer)) {
//      $sitestore->view_count++;
//      $sitestore->save();
//      $sub_status_table->storeViewCount($store_id);
//    } else if ($sitestore->view_count == 1 && empty($raw_views_count)) {
//      $sub_status_table->storeViewCount($store_id);
//    }
    $storeStatistics = <<<EOF
       en4.core.runonce.add(function(){
        en4.sitestore.storeStatistics("$store_id");   
       });
EOF;
    $this->view->headScript()->appendScript($storeStatistics);

    $style = $sitestore->getStoreStyle();
    if (!empty($style)) {
      $this->view->headStyle()->appendStyle($style);
    }

    if (null !== ($tab = $this->_getParam('tab'))) {
      //PROVIDE WIDGETISE STORES
      $storeprofile_tab_function = <<<EOF
                                        var content_id = "$tab";
                                        this.onload = function()
                                        {
      																		if(window.tabContainerSwitch) 
      																		{
                                              tabContainerSwitch($('main_tabs').getElement('.tab_' + content_id));
																					}
                                        }
EOF;
      $this->view->headScript()->appendScript($storeprofile_tab_function);
    }
    
    if (!empty($edit_layout_setting)) {
      $showHideHeaderFooter = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.show.hide.header.footer', 'default');
      if ($showHideHeaderFooter == 'default-simple') {
        $this->_helper->layout->setLayout('default-simple');
      }
      $cont = Engine_Content::getInstance();
      if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
				$storage = Engine_Api::_()->getDbtable('contentstores', 'sitestore');
      } else {
				$storage = Engine_Api::_()->getDbtable('mobileContentstores', 'sitestore');
      }
			$cont->setStorage($storage);
			$this->view->sitemain = $this->view->content('sitestore_index_view');
      $cont = Engine_Content::getInstance();
      $storage = Engine_Api::_()->getDbtable('pages', 'core');
      $cont->setStorage($storage);
    } else {
      $this->_helper->content->setNoRender()->setEnabled();
    }

    // Start: Suggestion work.
    $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    // Here we are delete this poll suggestion if viewer have.
    if (!empty($is_moduleEnabled)) {
      Engine_Api::_()->getApi('suggestion', 'sitestore')->deleteSuggestion($viewer->getIdentity(), 'sitestore', $store_id, 'store_suggestion');
    }
    // End: Suggestion work.
  }

  //ACTINO FOR MANAGING MY STORES
  public function manageAction() {

    //USER VALDIATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    $sitestore_manage = Zend_Registry::isRegistered('sitestore_manage') ? Zend_Registry::get('sitestore_manage') : null;

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //STORE CREATION PRIVACY
    $this->view->can_view = $this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->checkRequire();

    if (empty($this->view->can_view)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $this->view->can_edit = $this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->checkRequire();
    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0);
    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitestore()->enableLocation();
    $this->view->can_delete = $this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'delete')->checkRequire();

    //GET VIEWER CLAIMS
    $claim_id = Engine_Api::_()->getDbtable('claims', 'sitestore')->getViewerClaims($viewer_id);

    //CLAIM IS ENABLED OR NOT
    $canClaim = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestore_store', 'claim');

    $this->view->showClaimLink = 0;
    if (!empty($claim_id) && !empty($canClaim)) {
      $this->view->showClaimLink = 1;
    }

    //NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //QUICK NAVIGATION
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_quick');

    //FORM GENERATION
    $this->view->form = $form = new Sitestore_Form_Managesearch(array(
                'type' => 'sitestore_store'
            ));

    $form->removeElement('show');

    //SITESTORE-REVIEW IS ENABLED OR NOT
    $this->view->ratngShow = $ratingShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');

    //CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
    $adminstores = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdminStores($viewer_id);

    //GET STUFF
    $manageadmin_ids = array();
    foreach ($adminstores as $adminstore) {
      $manageadmin_ids[] = $adminstore->store_id;
    }
    $manageadmin_values = array();
    $manageadmin_values['adminstores'] = $manageadmin_ids;
    $manageadmin_values['orderby'] = 'creation_date';
    $manageadmin_data = Engine_Api::_()->sitestore()->getSitestoresPaginator($manageadmin_values, null);
    $this->view->manageadmin_count = $manageadmin_data->getTotalItemCount();
    //END CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
    //PROCESS FORM
    $request = $this->getRequest()->getPost();

    //PROCESS FORM
    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
      if ($values['subcategory_id'] == 0) {
        $values['subsubcategory_id'] = 0;
        $values['subsubcategory'] = 0;
      }
    } else {
      $values = array();
    }

    if (empty($sitestore_manage)) {
      return;
    }

    //CHECK TO SEE IF REQUEST IS FOR SPECIFIC USER'S STORES
    $values['user_id'] = $viewer->getIdentity();
    $values['type'] = 'manage';
    $values['type_location'] = 'manage';

    //GET PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->sitestore()->getSitestoresPaginator($values, null);
    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.store', 10);

    $paginator->setItemCountPerPage($items_count);
    $this->view->paginator = $paginator->setCurrentPageNumber($values['store']);

    //MAXIMUM ALLOWED STORES
    //WE HAVE IMPORT STORES FUNCTIONALITY, WE DONT WANT TO SHOW STORE LIMIT ALERT MESSAGE TO SUPERADMIN SO WE ARE SETTING $this->view->quota = 0;
    $this->view->quota = 0;
    if ($viewer->level_id != 1) {
      $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestore_store', 'max');
    }
    $this->view->current_count = $paginator->getTotalItemCount();
    $this->view->category_id = $values['category_id'];
    $this->view->subcategory_id = $values['subcategory_id'];
    $this->view->subsubcategory_id = $values['subsubcategory_id'];

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
      $coreversion = $coremodule->version;
      if ($coreversion < '4.1.0') {
        $this->_helper->content->render();
      } else {
        $this->_helper->content
                //->setNoRender()
                ->setEnabled();
      }
    }
  }

  // create  sitestore sitestore
  public function createAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;
    
    //SITEMOBILE_MODULE_NOT_SUPPORT_DESC_FOR_SOMEPAGES
    if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) { 
      Engine_API::_()->sitemobile()->setupRequestError();
    }    

    //STORE CREATE PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'create')->isValid())
      return;
    
    //NO SUB-STORE
    $parent_id = $this->_getParam('parent_id', null);
    if (!empty($parent_id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
        
    $package_id = 0;
    $viewer = Engine_Api::_()->user()->getViewer();
    $getMapInfo = Engine_Api::_()->sitestore()->getMapInfo();
    global $sitestore_is_approved;
    $sitestoreHostName = str_replace('www.', '', @strtolower($_SERVER['HTTP_HOST']));
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');
    $getPackageAuth = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestore');

    $sub_status_table = Engine_Api::_()->getDbTable('storestatistics', 'sitestore');
    
    // Comment Privacy
    $this->view->isCommentsAllow = Engine_Api::_()->sitestore()->isCommentsAllow("sitestore_store");
    
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      //REDIRECT
      $package_id = $this->_getParam('id');
      if (empty($package_id) || empty($getMapInfo)) {
        return $this->_forwardCustom('notfound', 'error', 'core');
      }
      $this->view->package = $package = Engine_Api::_()->getItemTable('sitestore_package')->fetchRow(array('package_id = ?' => $package_id, 'enabled = ?' => '1'));
      if (empty($this->view->package) || empty($getMapInfo)) {
        return $this->_forwardCustom('notfound', 'error', 'core');
      }

      if (!empty($package->level_id) && !in_array($viewer->level_id, explode(",", $package->level_id))) {
        return $this->_forwardCustom('notfound', 'error', 'core');
      }
    } else {
      $package_id = Engine_Api::_()->getItemtable('sitestore_package')->fetchRow(array('defaultpackage = ?' => 1))->package_id;
    }

    $maxCount = 10;
    $table = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $tablename = $table->info('name');
    $select = $table->select()->from($tablename, array('count(*) as count'));
    $results = $table->fetchRow($select);
    $this->view->packageCount = Engine_Api::_()->getDbtable( 'packages' , 'sitestore' )->getEnabledPackageCount();
    $sitestore_featured = Zend_Registry::isRegistered('sitestore_featured') ? Zend_Registry::get('sitestore_featured') : null;
    $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
    $row = $manageadminsTable->createRow();

    //FORM VALIDATION
    $this->view->form = $form = new Sitestore_Form_Create(array("packageId" => $package_id, "owner" => $viewer));
    $form->removeElement('toggle_products_status');
    $this->view->sitestoreUrlEnabled = $sitestoreUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreurl');
    $this->view->show_url = $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showurl.column', 1);
    if (!empty($sitestoreUrlEnabled) && empty($show_url)) {
      $form->removeElement('store_url');
      $form->removeElement('store_url_msg');
    }
    if (empty($sitestore_featured)) {
      return;
    }

    $isHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.isHost', 0);
    if (empty($isHost)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.viewstore.sett', convert_uuencode($sitestoreHostName));
    }

    //SET UP DATA NEEDED TO CHECK QUOTA

    $sitestore_category = Zend_Registry::isRegistered('sitestore_category') ? Zend_Registry::get('sitestore_category') : null;
    $values['user_id'] = $viewer->getIdentity();
    // $paginator = Engine_Api::_()->getApi('core', 'sitestore')->getSitestoresPaginator($values);
    $count = Engine_Api::_()->getDbtable('stores', 'sitestore')->countUserStores($values);
    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestore_store', 'max');

    $sitestore_render = Zend_Registry::isRegistered('sitestore_render') ? Zend_Registry::get('sitestore_render') : null;
    $this->view->current_count = $count;

    if (!empty($sitestore_render)) {
      $this->view->sitestore_render = $sitestore_render;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $table = Engine_Api::_()->getItemTable('sitestore_store');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try {
        // Create sitestore
        $values = array_merge($form->getValues(), array(
            'owner_id' => $viewer->getIdentity(),
            'package_id' => $package_id
                ));

        $is_error = 0;
        if (isset($values['category_id']) && empty($values['category_id'])) {
          $is_error = 1;
        }
        if (empty($values['subcategory_id'])) {
          $values['subcategory_id'] = 0;
        }
        if (empty($values['subsubcategory_id'])) {
          $values['subsubcategory_id'] = 0;
        }

        //SET ERROR MESSAGE
        if ($is_error == 1) {
          $this->view->status = false;
          $error = Zend_Registry::get('Zend_Translate')->_('Store Category * Please complete this field - it is required.');
          $form->getDecorator('errors')->setOption('escape', false);
          $form->addError($error);
          return;
        }
        $sitestore = $table->createRow();

        if (Engine_Api::_()->getApi('subCore', 'sitestore')->storeBaseNetworkEnable()) {
          if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
            if (in_array(0, $values['networks_privacy'])) {
              $values['networks_privacy'] = new Zend_Db_Expr('NULL');
            } else {
              $values['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
            }
          }
        }
        if (!empty($sitestoreUrlEnabled)) {
          if (empty($show_url)) {
            $resultStoreTable = $table->select()->where('title =?', $values['title'])->from($table, 'title')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            $count_index = count($resultStoreTable);
            $resultStoreUrl = $table->select()->where('store_url =?', $values['title'])->from($table, 'store_url')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            $count_index_url = count($resultStoreUrl);
          }
          $urlArray = Engine_Api::_()->sitestore()->getBannedUrls();
          if (!empty($show_url)) {
            if (in_array(strtolower($values['store_url']), $urlArray)) {
              $form->addError(Zend_Registry::get('Zend_Translate')->_('Sorry, this URL has been restricted by our automated system. Please choose a different URL.'));
              return;
            }
          } elseif (!empty($sitestoreUrlEnabled)) {
            $laststore_id = $table->select()
                    ->from($table->info('name'), array('store_id'))->order('store_id DESC')
                    ->query()
                    ->fetchColumn();
            $values['store_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
            if (!empty($count_index) || !empty($count_index_url)) {
              $laststore_id = $laststore_id + 1;
              $values['store_url'] = $values['store_url'] . '-' . $laststore_id;
            } else {
              $values['store_url'] = $values['store_url'];
            }
            if (in_array(strtolower($values['store_url']), $urlArray)) {

              $form->addError(Zend_Registry::get('Zend_Translate')->_('Sorry, this Store Title has been restricted by our automated system. Please choose a different Title.', array('escape' => false)));
              return;
            }
          }
        }
        $sitestore->setFromArray($values);


        $user_level = $viewer->level_id;
        if (!Engine_Api::_()->sitestore()->hasPackageEnable()) {
          $sitestore->featured = Engine_Api::_()->authorization()->getPermission($user_level, 'sitestore_store', 'featured');
          $sitestore->sponsored = Engine_Api::_()->authorization()->getPermission($user_level, 'sitestore_store', 'sponsored');
          $sitestore->approved = Engine_Api::_()->authorization()->getPermission($user_level, 'sitestore_store', 'approved');
        } else {
          $sitestore->featured = $package->featured;
          $sitestore->sponsored = $package->sponsored;
          if ($package->isFree() && !empty($sitestore_is_approved) && !empty($getPackageAuth) && !empty($getMapInfo)) {
            $sitestore->approved = $package->approved;
          } else {
            $sitestore->approved = 0;
          }
        }

        if (!empty($sitestore->approved)) {
          $sitestore->pending = 0;
          $sitestore->aprrove_date = date('Y-m-d H:i:s');

          if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
            $expirationDate = $package->getExpirationDate();
            if (!empty($expirationDate))
              $sitestore->expiration_date = date('Y-m-d H:i:s', $expirationDate);
            else
              $sitestore->expiration_date = '2250-01-01 00:00:00';
          }
          else {
            $sitestore->expiration_date = '2250-01-01 00:00:00';
          }
        }
        if (!empty($sitestore_category)) {
          $sitestore->save();
          $store_id = $sitestore->store_id;
        }

        if (!empty($sitestore->approved)) {
          Engine_Api::_()->sitestore()->sendMail("ACTIVE", $sitestore->store_id);
        } else {
          Engine_Api::_()->sitestore()->sendMail("APPROVAL_PENDING", $sitestore->store_id);
        }

        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
        $row = $manageadminsTable->createRow();
        $row->user_id = $sitestore->owner_id;
        $row->store_id = $sitestore->store_id;
        $row->save();

        //START PROFILE MAPS WORK
        Engine_Api::_()->getDbtable('profilemaps', 'sitestore')->profileMapping($sitestore);


        $store_id = $sitestore->store_id;
        if (!empty($sitestoreUrlEnabled) && empty($show_url)) {
          $values['store_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
          if (!empty($count_index) || !empty($count_index_url)) {
            $values['store_url'] = $values['store_url'] . '-' . $store_id;
            $table->update(array('store_url' => $values['store_url']), array('store_id = ?' => $store_id));
          } else {
            $values['store_url'] = $values['store_url'];
            $table->update(array('store_url' => $values['store_url']), array('store_id = ?' => $store_id));
          }
        }

        $sitestoreFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform');
        if ($sitestoreFormEnabled) {
          $tablecontent = Engine_Api::_()->getDbtable('content', 'core');
					$params = $tablecontent->select()
									->from($tablecontent->info('name'),'params')
									->where('name = ?', 'sitestoreform.sitestore-viewform')
									->query()->fetchColumn();
					$decodedParam = Zend_Json::decode($params);
					$tabName = $decodedParam['title'];
          $sitestoreformtable = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform');
          $optionid = Engine_Api::_()->getDbtable('storequetions', 'sitestoreform');
          $table_option = Engine_Api::_()->fields()->getTable('sitestoreform', 'options');
          $sitestoreform = $table_option->createRow();
          $sitestoreform->setFromArray($values);
          $sitestoreform->label = $values['title'];
          $sitestoreform->field_id = 1;
          $option_id = $sitestoreform->save();
          $optionids = $optionid->createRow();
          $optionids->option_id = $option_id;
          $optionids->store_id = $store_id;
          $optionids->save();
          $sitestoreforms = $sitestoreformtable->createRow();
          if(!empty($tabName) && isset($sitestoreforms->offer_tab_name))
            $sitestoreforms->offer_tab_name = $tabName;
          
          $sitestoreforms->description = 'Please leave your feedback below and enter your contact details.';
          $sitestoreforms->store_id = $store_id;
          $sitestoreforms->save();
        }
        //SET PHOTO
        if (!empty($values['photo'])) {
          $sitestore->setPhoto($form->photo);
          $sitestoreinfo = $sitestore->toarray();
          $albumTable = Engine_Api::_()->getDbtable('albums', 'sitestore');
          $album_id = $albumTable->update(array('photo_id' => $sitestoreinfo['photo_id'], 'owner_id' => $sitestoreinfo['owner_id']), array('store_id = ?' => $sitestoreinfo['store_id']));
        } else {
          $sitestoreinfo = $sitestore->toarray();
          $albumTable = Engine_Api::_()->getDbtable('albums', 'sitestore');
          $album_id = $albumTable->insert(array(
              'photo_id' => 0,
              'owner_id' => $sitestoreinfo['owner_id'],
              'store_id' => $sitestoreinfo['store_id'],
              'title' => $sitestoreinfo['title'],
              'creation_date' => $sitestoreinfo['creation_date'],
              'modified_date' => $sitestoreinfo['modified_date']));
        }

        //ADD TAGS
        $tags = preg_split('/[,]+/', $values['tags']);
        $tags = array_filter(array_map("trim", $tags));
        $sitestore->tags()->addTagMaps($viewer, $tags);

        if (!empty($store_id)) {
          $sitestore->setLocation();
        }

        // Set privacy
        $auth = Engine_Api::_()->authorization()->context;

        //get the store admin list.
        $ownerList = $sitestore->getStoreOwnerList();

        $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
        if (!empty($sitestorememberEnabled)) {
          $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }


        if (!isset($values['auth_view']) || empty($values['auth_view'])) {
          $values['auth_view'] = "everyone";
        }

        if (!isset($values['auth_comment']) || empty($values['auth_comment'])) {
          $values['auth_comment'] = "everyone";
        }

        $viewMax = array_search($values['auth_view'], $roles);
        $commentMax = array_search($values['auth_comment'], $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($sitestore, $role, 'view', ($i <= $viewMax));
          $auth->setAllowed($sitestore, $role, 'comment', ($i <= $commentMax));
          $auth->setAllowed($sitestore, $role, 'print', 1);
          $auth->setAllowed($sitestore, $role, 'tfriend', 1);
          $auth->setAllowed($sitestore, $role, 'overview', 1);
          $auth->setAllowed($sitestore, $role, 'map', 1);
          $auth->setAllowed($sitestore, $role, 'insight', 1);
          $auth->setAllowed($sitestore, $role, 'layout', 1);
          $auth->setAllowed($sitestore, $role, 'contact', 1);
          $auth->setAllowed($sitestore, $role, 'form', 1);
          $auth->setAllowed($sitestore, $role, 'offer', 1);
          $auth->setAllowed($sitestore, $role, 'invite', 1);
        }

        $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
        if (!empty($sitestorememberEnabled)) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }

        //START WORK FOR SUB STORE.
//        if (empty($values['auth_sspcreate'])) {
//          $values['auth_sspcreate'] = array("owner");
//        }
//
//        $createMax = array_search($values['auth_sspcreate'], $roles);
//        foreach ($roles as $i => $role) {
//          if ($role === 'like_member') {
//            $role = $ownerList;
//          }
//          $auth->setAllowed($sitestore, $role, 'sspcreate', ($i <= $createMax));
//        }
        //END WORK FOR SUBSTORE

        //START SITESTOREDISCUSSION PLUGIN WORK      
        $sitestorediscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion');
        if ($sitestorediscussionEnabled) {
          //START DISCUSSION PRIVACY WORK
          if (empty($values['sdicreate'])) {
            $values['sdicreate'] = array("owner");
          }

          $createMax = array_search($values['sdicreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitestore, $role, 'sdicreate', ($i <= $createMax));
          }
          //END DISCUSSION PRIVACY WORK
        }
        //END SITESTOREDISCUSSION PLUGIN WORK        
        //START SITESTOREALBUM PLUGIN WORK      
        $sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
        if ($sitestorealbumEnabled) {
          //START PHOTO PRIVACY WORK
          if (empty($values['spcreate'])) {
            $values['spcreate'] = array("owner");
          }

          $createMax = array_search($values['spcreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitestore, $role, 'spcreate', ($i <= $createMax));
          }
          //END PHOTO PRIVACY WORK
        }
        //END SITESTOREALBUM PLUGIN WORK
        //START SITESTOREDOCUMENT PLUGIN WORK
        $sitestoreDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument');
        if ($sitestoreDocumentEnabled|| (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {
          $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
          if (!empty($sitestorememberEnabled)) {
            $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
          } else {
            $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
          }

          if (empty($values['sdcreate'])) {
            $values['sdcreate'] = array("owner");
          }

          $createMax = array_search($values['sdcreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitestore, $role, 'sdcreate', ($i <= $createMax));
          }
        }
        //END SITESTOREDOCUMENT PLUGIN WORK
        //START SITESTOREVIDEO PLUGIN WORK
        $sitestoreVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo');
        if ($sitestoreVideoEnabled|| (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {
          if (empty($values['svcreate'])) {
            $values['svcreate'] = array("owner");
          }

          $createMax = array_search($values['svcreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitestore, $role, 'svcreate', ($i <= $createMax));
          }
        }
        //END SITESTOREVIDEO PLUGIN WORK
        //START SITESTOREPOLL PLUGIN WORK
        $sitestorePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll');
        if ($sitestorePollEnabled) {
          if (empty($values['splcreate'])) {
            $values['splcreate'] = array("owner");
          }

          $createMax = array_search($values['splcreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitestore, $role, 'splcreate', ($i <= $createMax));
          }
        }
        //END SITESTOREPOLL PLUGIN WORK
        //START SITESTORENOTE PLUGIN WORK
        $sitestoreNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote');
        if ($sitestoreNoteEnabled) {
          if (empty($values['sncreate'])) {
            $values['sncreate'] = array("owner");
          }

          $createMax = array_search($values['sncreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitestore, $role, 'sncreate', ($i <= $createMax));
          }
        }
        //END SITESTORENOTE PLUGIN WORK
        //START SITESTOREMUSIC PLUGIN WORK
        $sitestoreMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic');
        if ($sitestoreMusicEnabled) {
          if (empty($values['smcreate'])) {
            $values['smcreate'] = array("owner");
          }

          $createMax = array_search($values['smcreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitestore, $role, 'smcreate', ($i <= $createMax));
          }
        }
        //END SITESTOREMUSIC PLUGIN WORK
        //START SITESTOREEVENT PLUGIN WORK
        $sitestoreEventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent');
				if ($sitestoreEventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {
          if (empty($values['secreate'])) {
            $values['secreate'] = array("owner");
          }

          $createMax = array_search($values['secreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitestore, $role, 'secreate', ($i <= $createMax));
          }
        }
        //END SITESTOREEVENT PLUGIN WORK
        //START SITESTOREMEMBER PLUGIN WORK
        $sitestoreMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
        if ($sitestoreMemberEnabled) {
          $membersTable = Engine_Api::_()->getDbtable('membership', 'sitestore');
          $row = $membersTable->createRow();
          $row->resource_id = $sitestore->store_id;
          $row->store_id = $sitestore->store_id;
          $row->user_id = $sitestore->owner_id;
          $row->save();
          $sitestore->member_count++;
          $sitestore->save();
        }
        $memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('storemember.invite.option', 1);
        $member_approval = Engine_Api::_()->getApi('settings', 'core')->getSetting('storemember.member.approval.option', 1);
        if (empty($memberInvite)) {
          $memberInviteOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('storemember.invite.automatically', 1);
          $sitestore->member_invite = $memberInviteOption;
          $sitestore->save();
        }
        if (empty($member_approval)) {
          $member_approvalOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('storemember.member.approval.automatically', 1);
          $sitestore->member_approval = $member_approvalOption;
          $sitestore->save();
        }
        //END SITESTOREMEMBER PLUGIN WORK
        //START STORE INTEGRATION WORK
        $store_id = $this->_getParam('store_id');
        if (!empty($store_id)) {
          $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
          $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
          if (!empty($moduleEnabled)) {
            $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitestoreintegration');
            $row = $contentsTable->createRow();
            $row->owner_id = $viewer_id;
            $row->resource_owner_id = $sitestore->owner_id;
            $row->store_id = $store_id;
            $row->resource_type = 'sitestore_store';
            $row->resource_id = $sitestore->store_id;
            ;
            $row->save();
          }
        }
        $group_id = $this->_getParam('group_id');
        if (!empty($group_id)) {
          $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
          $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration');
          if (!empty($moduleEnabled)) {
            $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration');
            $row = $contentsTable->createRow();
            $row->owner_id = $viewer_id;
            $row->resource_owner_id = $sitestore->owner_id;
            $row->group = $group_id;
            $row->resource_type = 'sitestore_store';
            $row->resource_id = $sitestore->store_id;
            ;
            $row->save();
          }
        }
        //END STORE INTEGRATION WORK
        
        //CUSTOM FIELD WORK
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.profile.fields', 1)) {
          $customfieldform = $form->getSubForm('fields');
          $customfieldform->setItem($sitestore);
          $customfieldform->saveValues();
        }
        
        //START DEFAULT EMAIL TO SUPERADMIN WHEN ANYONE CREATE PAGES.
        $emails = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.defaultpagecreate.email', Engine_API::_()->seaocore()->getSuperAdminEmailAddress());
        if(!empty($emails)) {
					$emails = explode(",", $emails);
					$host = $_SERVER['HTTP_HOST'];
					$newVar = _ENGINE_SSL ? 'https://' : 'http://';
					$object_link = $newVar . $host . $sitestore->getHref();
					$viewerGetTitle = $viewer->getTitle();
          $storeTitle = $sitestore->getTitle();
					$sender_link = '<a href=' . $newVar . $host . $viewer->getHref() . ">$viewerGetTitle</a>";
          $title_link = '<a href=' . $newVar . $host . $sitestore->getHref() . ">$storeTitle</a>";
          
					foreach ($emails as $email) {
					  $email = trim($email);
						Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'SITESTORE_STORE_CREATION', array(
							'sender' => $sender_link,
							'object_link' => $object_link,
							'object_title_link' => $title_link,
                                                        'object_title' => $storeTitle,
							'object_description' => $sitestore->getDescription(),
							'queue' => false
						));
					}
				}
				//END DEFAULT EMAIL TO SUPERADMIN WHEN ANYONE CREATE PAGES.

        // Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      if (!empty($sitestore) && !empty($sitestore->draft) && empty($sitestore->pending)) {
        Engine_Api::_()->sitestore()->attachStoreActivity($sitestore);


        //START AUTOMATICALLY LIKE THE STORE WHEN MEMBER CREATE A STORE.
        $autoLike = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.automatically.like', 1);
        if (!empty($autoLike)) {
          Engine_Api::_()->sitestore()->autoLike($sitestore->store_id, 'sitestore_store');
        }
        //END AUTOMATICALLY LIKE THE STORE WHEN MEMBER CREATE A STORE.
        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {

          $sitestore_array = array();
          $sitestore_array['type'] = 'sitestore_new';
          $sitestore_array['object'] = $sitestore;

          Engine_Api::_()->facebooksefeed()->sendFacebookFeed($sitestore_array);
        }
      }
//       //EXTRACTING CURRENT ADMIN SETTINGS FOR THIS VIEW STORE.
//       $storeAdminTable = Engine_Api::_()->getDbtable('pages', 'core');
//       $storeAdminTableName = $storeAdminTable->info('name');
//       $selectStoreAdmin = $storeAdminTable->select()
//               ->setIntegrityCheck(false)
//               ->from($storeAdminTableName)
//               ->where('name = ?', 'sitestore_index_view');
//       $storeAdminresult = $storeAdminTable->fetchRow($selectStoreAdmin);
// 
//       //NOW INSERTING THE ROW IN STORE TABLE
//       $storeTable = Engine_Api::_()->getDbtable('contentstores', 'sitestore');
// 
//       //CREATE NEW STORE
//       $storeObject = $storeTable->createRow();
//       $storeObject->displayname = ( null !== ($name = $values['title']) ? $name : 'Untitled' );
//       $storeObject->title = ( null !== ($name = $values['title']) ? $name : 'Untitled' );
//       $storeObject->description = $values['body'];
//       $storeObject->name = "sitestore_index_view";
//       $storeObject->url = $storeAdminresult->url;
//       $storeObject->custom = $storeAdminresult->custom;
//       $storeObject->fragment = $storeAdminresult->fragment;
//       $storeObject->keywords = $storeAdminresult->keywords;
//       $storeObject->layout = $storeAdminresult->layout;
//       $storeObject->view_count = $storeAdminresult->view_count;
//       $storeObject->user_id = $values['owner_id'];
//       $storeObject->store_id = $sitestore->store_id;
//       $contentStoreId = $storeObject->save();
// 
//       //NOW FETCHING STORE CONTENT DEFAULT SETTING INFORMATION FROM CORE CONTENT TABLE FOR THIS STORE.
//       //NOW INSERTING DEFAULT STORE CONTENT SETTINGS IN OUR CONTENT TABLE
//       $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
//       $sitestore_layout_cover_photo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layout.cover.photo', 1);
//       if (!$layout) {
//         Engine_Api::_()->getDbtable('content', 'sitestore')->setContentDefault($contentStoreId, $sitestore_layout_cover_photo);
//       } else {
//         Engine_Api::_()->getApi('layoutcore', 'sitestore')->setContentDefaultLayout($contentStoreId, $sitestore_layout_cover_photo);
//       }

      //REDIRECT
      return $this->_helper->redirector->gotoRoute(array('action' => 'get-started', 'store_id' => $sitestore->store_id, 'saved' => '1'), 'sitestore_dashboard', true);
    } /* else {
      $results = $this->getRequest()->getPost();
      if (!empty($results) && isset($results['subcategory_id'])) {
      $this->view->category_id = $results['category_id'];
      if (!empty($results['subcategory_id'])) {
      $this->view->subcategory_name = Engine_Api::_()->getDbtable('categories', 'sitestore')->getCategory($results['subcategory_id'])->category_name;
      }
      return;
      }
      } */
  }

  //ACTION FOR STORE EDI
  public function editAction() {

    //USER VALDIATION
    if (!$this->_helper->requireUser()->isValid())
      return;
    
    //SITEMOBILE_MODULE_NOT_SUPPORT_DESC_FOR_SOMEPAGES
		if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) { 
      Engine_API::_()->sitemobile()->setupRequestError();
		}    

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
    $getMapInfo = Engine_Api::_()->sitestore()->getMapInfo();
    $this->view->store_id = $store_id = $this->_getParam('store_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $this->view->sitestores_view_menu = 1;
    $getPackageAuth = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestore');
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $previous_category_id = $sitestore->category_id;
    
    // Comment Privacy
    $this->view->isCommentsAllow = Engine_Api::_()->sitestore()->isCommentsAllow("sitestore_store");
    //$previous_location = $sitestore->location;

    $ownerList = $sitestore->getStoreOwnerList();

    if (empty($sitestore) || empty($getMapInfo)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin) || empty($getMapInfo)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    $this->view->owner_id = $owner_id = $sitestore->owner_id;
    $user_subject = Engine_Api::_()->user()->getUser($owner_id);

    //FORM GENERATION
    $this->view->form = $form = new Sitestore_Form_Edit(array('item' => $sitestore, "packageId" => $sitestore->package_id, "owner" => $user_subject));
    $this->view->show_url = $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showurl.column', 1);
    $this->view->edit_url = $edit_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.edit.url', 0);
    $this->view->sitestoreurlenabled = $sitestoreUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreurl');
    if (!empty($sitestoreUrlEnabled) && empty($show_url)) {
      $form->removeElement('store_url');
      $form->removeElement('store_url_msg');
    }
//    if ($sitestore->search) {
//      $form->toggle_products_status->setLabel('Do you want to disable all products of this store?');
//    } else {
//      $form->toggle_products_status->setLabel('Do you want to enable all products of this store?');
//    }
    $this->view->is_ajax = $this->_getParam('is_ajax', '');
    if (!empty($sitestore->draft)) {
      $form->removeElement('draft');
    }
    $form->removeElement('photo');

    $this->view->category_id = $sitestore->category_id;
    $subcategory_id = $this->view->subcategory_id = $sitestore->subcategory_id;
    $this->view->subsubcategory_id = $sitestore->subsubcategory_id;
    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($subcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subcategory_name = $row->category_name;
    }

    $sitestoreFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform');
    if ($sitestoreFormEnabled) {
      $quetion = Engine_Api::_()->getDbtable('storequetions', 'sitestoreform');
      $select_quetion = $quetion->select()->where('store_id = ?', $store_id);
      $result_quetion = $quetion->fetchRow($select_quetion);
      $this->view->option_id = $result_quetion->option_id;
    }

    $values['user_id'] = $viewer_id;

    //SAVE SITESTORE ENTRY
    if (!$this->getRequest()->isPost()) {

      // prepare tags
      $sitestoreTags = $sitestore->tags()->getTagMaps();
      $tagString = '';

      foreach ($sitestoreTags as $tagmap) {

        if ($tagString !== '')
          $tagString .= ', ';
        $tagString .= $tagmap->getTag()->getTitle();
      }

      $this->view->tagNamePrepared = $tagString;
      $form->tags->setValue($tagString);

      // etc
      if ($sitestore->search) {
      $form->toggle_products_status->setLabel('Disable Products?');
      $form->toggle_products_status->setDescription('Do you want to disable all products of this store?');
    } else {
      $form->toggle_products_status->setLabel('Enable Products?');
      $form->toggle_products_status->setDescription('Do you want to enable all products of this store?');
    }
      $form->populate($sitestore->toArray());
      $auth = Engine_Api::_()->authorization()->context;
      $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
      if (!empty($sitestorememberEnabled)) {
        $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      foreach ($roles as $roleString) {
        $role = $roleString;

        if ($form->auth_view && 1 == $auth->isAllowed($sitestore, $role, 'view')) {
          $form->auth_view->setValue($roleString);
        }

        if ($form->auth_comment && 1 == $auth->isAllowed($sitestore, $role, 'comment')) {
          $form->auth_comment->setValue($roleString);
        }

        if ($role == 'everyone')
          continue;

        if ($role === 'like_member') {
          $role = $ownerList;
        }

        //Here we change isAllowed function for like privacy work only for populate.
        $sitestoreAllow = Engine_Api::_()->getApi('allow', 'sitestore');
//        if ($form->auth_sspcreate && 1 == $sitestoreAllow->isAllowed($sitestore, $role, 'sspcreate')) {
//          $form->auth_sspcreate->setValue($roleString);
//        }
        // PHOTO PRIVACY WORK
        if ($form->spcreate && 1 == $sitestoreAllow->isAllowed($sitestore, $role, 'spcreate')) {
          $form->spcreate->setValue($roleString);
        }
        // DISCUSSION PRIVACY WORK
        if ($form->sdicreate && 1 == $sitestoreAllow->isAllowed($sitestore, $role, 'sdicreate')) {
          $form->sdicreate->setValue($roleString);
        }
        //SITESTOREDOCUMENT PRIVACY WORK
        if ($form->sdcreate && 1 == $sitestoreAllow->isAllowed($sitestore, $role, 'sdcreate')) {
          $form->sdcreate->setValue($roleString);
        }
        // SITESTOREVIDEO PRIVACY WORK
        if ($form->svcreate && 1 == $sitestoreAllow->isAllowed($sitestore, $role, 'svcreate')) {
          $form->svcreate->setValue($roleString);
        }
        //START SITESTOREPOLL PRIVACY WORK
        if ($form->splcreate && 1 == $sitestoreAllow->isAllowed($sitestore, $role, 'splcreate')) {
          $form->splcreate->setValue($roleString);
        }
        //START SITESTORENOTE PRIVACY WORK
        if ($form->sncreate && 1 == $sitestoreAllow->isAllowed($sitestore, $role, 'sncreate')) {
          $form->sncreate->setValue($roleString);
        }
        //START SITESTOREMUSIC PRIVACY WORK
        if ($form->smcreate && 1 == $sitestoreAllow->isAllowed($sitestore, $role, 'smcreate')) {
          $form->smcreate->setValue($roleString);
        }
        //START SITESTOREEVENT PRIVACY WORK
        if ($form->secreate && 1 == $sitestoreAllow->isAllowed($sitestore, $role, 'secreate')) {
          $form->secreate->setValue($roleString);
        }
      }

      if (Engine_Api::_()->getApi('subCore', 'sitestore')->storeBaseNetworkEnable()) {
        if (!empty($sitestore->networks_privacy)) {
          $form->networks_privacy->setValue(explode(',', $sitestore->networks_privacy));
        } else {
          $form->networks_privacy->setValue(array(0));
        }
      }
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $values['category_id'] = $this->view->category_id = $sitestore->category_id;
      $values['subcategory_id'] = $this->view->subcategory_id = $sitestore->subcategory_id;
      $values['subsubcategory_id'] = $this->view->subsubcategory_id = $sitestore->subsubcategory_id;
      $form->populate($values);
      if (array_key_exists('search', $values) && $values['search']) {
      $form->toggle_products_status->setLabel('Disable Products?');
      $form->toggle_products_status->setDescription('Do you want to disable all products of this store?');
    } else {
      $form->toggle_products_status->setLabel('Enable Products?');
      $form->toggle_products_status->setDescription('Do you want to enable all products of this store?');
    }
      return;
    }

    // handle save for tags
    $values = $form->getValues($values);
    if (!empty($sitestoreUrlEnabled) && !empty($show_url) && !empty($edit_url)) {
      $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.change.url', 1);
      $urlArray = Engine_Api::_()->sitestore()->getBannedUrls();
      $sitestoreTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
      $selectTable = $sitestoreTable->select()->where('store_id != ?', $store_id)
              ->where('store_url = ?', $values['store_url']);
      $resultSitestoreTable = $sitestoreTable->fetchAll($selectTable);
      if (count($resultSitestoreTable) || (in_array(strtolower($values['store_url']), $urlArray)) && (!empty($change_url))) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('This URL has been restricted by our automated system. Please choose another URL.'));
        return;
      }
    } elseif (!empty($sitestoreUrlEnabled) && empty($show_url)) {
      $urlArray = Engine_Api::_()->sitestore()->getBannedUrls();
      $table = Engine_Api::_()->getDbtable('stores', 'sitestore');
      $resultStoreTable = $table->select()->where('title =?', $values['title'])->from($table, 'title')
                      ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      $count_index = count($resultStoreTable);
      $resultStoreUrl = $table->select()->where('store_url =?', $values['title'])->from($table, 'store_url')
                      ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      $count_index_url = count($resultStoreUrl);

      if (empty($count_index)) {
        $values['store_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
        if (!empty($count_index) || !empty($count_index_url)) {
          $values['store_url'] = $values['store_url'] . '-' . $store_id;
        } else {
          $values['store_url'] = $values['store_url'];
        }
        if (in_array(($values['store_url']), $urlArray)) {

          $form->addError(Zend_Registry::get('Zend_Translate')->_('This Store title has been blocked by our automated system. Please choose another title.', array('escape' => false)));
          return;
        }
      }
    }

    $is_error = 0;
    Engine_Api::_()->getItemtable('sitestore_package')->setPackages();
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.category.edit', 0) && !empty($sitestore->category_id)) {
      $values['category_id'] = $this->view->category_id = $sitestore->category_id;
      $values['subcategory_id'] = $this->view->subcategory_id = $sitestore->subcategory_id;
      $values['subsubcategory_id'] = $this->view->subsubcategory_id = $sitestore->subsubcategory_id;
      $form->populate($values);
      if (array_key_exists('search', $values) && $values['search']) {
      $form->toggle_products_status->setLabel('Disable Products?');
      $form->toggle_products_status->setDescription('Do you want to disable all products of this store?');
    } else {
      $form->toggle_products_status->setLabel('Enable Products?');
      $form->toggle_products_status->setDescription('Do you want to enable all products of this store?');
    }
      
    }
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.category.edit', 0)) {
      if (isset($values['category_id']) && empty($values['category_id'])) {
        $is_error = 1;
        $this->view->category_id = 0;
        $this->view->subsubcategory_id = 0;
        $this->view->subcategory_id = 0;
      }
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.category.edit', 0)) {
      if (isset($values['category_id']) && empty($values['category_id'])) {
        $is_error = 1;
        $this->view->category_id = 0;
        $this->view->subsubcategory_id = 0;
        $this->view->subcategory_id = 0;
      }
    }

    //set error message
    if ($is_error == 1) {
      $this->view->status = false;
      $error = Zend_Registry::get('Zend_Translate')->_('Store Category * Please complete this field - it is required.');
      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error);
      return;
    }

    if ($sitestoreFormEnabled) {
      $sitestoreform_form = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform');
      $quetion = Engine_Api::_()->getDbtable('storequetions', 'sitestoreform');
      $select_quetion = $quetion->select()->where('store_id = ?', $store_id);
      $result_quetion = $quetion->fetchRow($select_quetion);
      if( !empty($result_quetion) ) {
        $this->view->option_id = $result_quetion->option_id;
        $table_option = Engine_Api::_()->fields()->getTable('sitestoreform', 'options');
        $table_option->update(array('label' => $values['title']), array('option_id = ?' => $result_quetion->option_id));
      }
    }

    $tags = preg_split('/[,]+/', $values['tags']);
    $tags = array_filter(array_map("trim", $tags));

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      if (Engine_Api::_()->getApi('subCore', 'sitestore')->storeBaseNetworkEnable()) {

        if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
          if (in_array(0, $values['networks_privacy'])) {
            $values['networks_privacy'] = new Zend_Db_Expr('NULL');
            $form->networks_privacy->setValue(array(0));
          } else {
            $values['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
          }
        }
      }
      
      // WORK FOR ENABLE/DISABLE OF ALL THE PRODUCTS OF THIS STORE
      if (isset($sitestore->search) && $sitestore->search != $values['search']) {
        if (array_key_exists('toggle_products_status', $values) && $values['toggle_products_status']) {
          Engine_Api::_()->getDbtable('stores', 'sitestore')->toggleStoreProductsStatus($store_id, $values['search']);
        }
      }
      $sitestore->setFromArray($values);
      $sitestore->modified_date = date('Y-m-d H:i:s');

      $sitestore->tags()->setTagMaps($viewer, $tags);
      $sitestore->save();

      $location = $sitestore->location;

//       if ($previous_location && (empty($previous_location) || ($location !== $previous_location))) {
//         $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestore');
//         $locationTable->delete(array('store_id =?' => $store_id, 'location =?' => $previous_location));
//       }

//       if (!empty($location) && $location !== $previous_location) {
//         $sitestore->setLocation();
//       }


      /* else {
        $locationTable->delete(arr ay(
        'store_id =?' => $store_id
        ));
        } */
      //CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
      if (!empty($sitestorememberEnabled)) {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      $values = $form->getValues();
      if ($values['auth_view'])
        $auth_view = $values['auth_view'];
      else
        $auth_view = "everyone";
      $viewMax = array_search($auth_view, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($sitestore, $role, 'view', ($i <= $viewMax));
      }

      $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
      if (!empty($sitestorememberEnabled)) {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      if ($values['auth_comment'])
        $auth_comment = $values['auth_comment'];
      else
        $auth_comment = "everyone";
      $commentMax = array_search($auth_comment, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($sitestore, $role, 'comment', ($i <= $commentMax));
      }

      $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
      if (!empty($sitestorememberEnabled)) {
        $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      //START WORK FOR SUB STORE.
//      if ($values['auth_sspcreate'])
//        $substore = $values['auth_sspcreate'];
//      else
//        $substore = "owner";
//      $substoreMax = array_search($substore, $roles);
//
//      foreach ($roles as $i => $role) {
//        if ($role === 'like_member') {
//          $role = $ownerList;
//        }
//        $auth->setAllowed($sitestore, $role, 'sspcreate', ($i <= $substoreMax));
//      }
      //END WORK FOR SUBSTORE
      //START DISCUSSION PRIVACY WORK
      $sitestorediscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion');
      if ($sitestorediscussionEnabled) {
        if ($values['sdicreate'])
          $photo = $values['sdicreate'];
        else
          $photo = "owner";
        $photoMax = array_search($photo, $roles);

        foreach ($roles as $i => $role) {
          if ($role === 'like_member') {
            $role = $ownerList;
          }
          $auth->setAllowed($sitestore, $role, 'sdicreate', ($i <= $photoMax));
        }
      }
      //END DISCUSSION PRIVACY WORK      
      //START PHOTO PRIVACY WORK
      $sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
      if ($sitestorealbumEnabled) {
        if ($values['spcreate'])
          $photo = $values['spcreate'];
        else
          $photo = "owner";
        $photoMax = array_search($photo, $roles);

        foreach ($roles as $i => $role) {
          if ($role === 'like_member') {
            $role = $ownerList;
          }
          $auth->setAllowed($sitestore, $role, 'spcreate', ($i <= $photoMax));
        }
      }
      //END PHOTO PRIVACY WORK
      //START SITESTOREDOCUMENT WORK
      $sitestoreDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument');
      if ($sitestoreDocumentEnabled|| (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {

        if ($values['sdcreate'])
          $sdcreate = $values['sdcreate'];
        else
          $sdcreate = "owner";

        $sdcreateMax = array_search($sdcreate, $roles);

        foreach ($roles as $i => $role) {
          if ($role === 'like_member') {
            $role = $ownerList;
          }
          $auth->setAllowed($sitestore, $role, 'sdcreate', ($i <= $sdcreateMax));
        }
      }
      //END SITESTOREDOCUMENT WORK
      //START SITESTOREVIDEO WORK
      $sitestoreVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo');
      if ($sitestoreVideoEnabled|| (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {
        if ($values['svcreate'])
          $svcreate = $values['svcreate'];
        else
          $svcreate = "owner";
        $svcreateMax = array_search($svcreate, $roles);

        foreach ($roles as $i => $role) {
          if ($role === 'like_member') {
            $role = $ownerList;
          }
          $auth->setAllowed($sitestore, $role, 'svcreate', ($i <= $svcreateMax));
        }
      }
      //END SITESTOREVIDEO WORK
      //START SITESTOREPOLL WORK
      $sitestorePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll');
      if ($sitestorePollEnabled) {
        if ($values['splcreate'])
          $splcreate = $values['splcreate'];
        else
          $splcreate = "owner";
        $splcreateMax = array_search($splcreate, $roles);

        foreach ($roles as $i => $role) {
          if ($role === 'like_member') {
            $role = $ownerList;
          }
          $auth->setAllowed($sitestore, $role, 'splcreate', ($i <= $splcreateMax));
        }
      }
      //END SITESTOREPOLL WORK
      //START SITESTORENOTE WORK
      $sitestoreNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote');
      if ($sitestoreNoteEnabled) {
        $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
        if (!empty($sitestorememberEnabled)) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }
        if ($values['sncreate'])
          $sncreate = $values['sncreate'];
        else
          $sncreate = "owner";
        $sncreateMax = array_search($sncreate, $roles);

        foreach ($roles as $i => $role) {
          if ($role === 'like_member') {
            $role = $ownerList;
          }
          $auth->setAllowed($sitestore, $role, 'sncreate', ($i <= $sncreateMax));
        }
      }
      //END SITESTORENOTE WORK
      //START SITESTOREMUSIC WORK
      $sitestoreMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic');
      if ($sitestoreMusicEnabled) {
        $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
        if (!empty($sitestorememberEnabled)) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }

        if ($values['smcreate'])
          $smcreate = $values['smcreate'];
        else
          $smcreate = "owner";
        $smcreateMax = array_search($smcreate, $roles);

        foreach ($roles as $i => $role) {
          if ($role === 'like_member') {
            $role = $ownerList;
          }
          $auth->setAllowed($sitestore, $role, 'smcreate', ($i <= $smcreateMax));
        }
      }
      //END SITESTORENOTE WORK
      //START SITESTOREEVENT WORK
      $sitestoreEventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent');
			if ($sitestoreEventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {
        $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
        if (!empty($sitestorememberEnabled)) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }
        if ($values['secreate'])
          $secreate = $values['secreate'];
        else
          $secreate = "owner";
        $secreateMax = array_search($secreate, $roles);

        foreach ($roles as $i => $role) {
          if ($role === 'like_member') {
            $role = $ownerList;
          }
          $auth->setAllowed($sitestore, $role, 'secreate', ($i <= $secreateMax));
        }
      }
      //END SITEPAGEEVENT WORK
      //START SITESTOREREVIEW CODE
      $sitestoreReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
      if ($sitestoreReviewEnabled && $previous_category_id != $sitestore->category_id) {
        Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->editStoreCategory($sitestore->store_id, $previous_category_id, $sitestore->category_id);
      }
      //END SITESTOREREVIEW CODE
      //START SITESTOREMEMBER CODE
      $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
      if ($sitestorememberEnabled && $previous_category_id != $sitestore->category_id) {
        $db->query("UPDATE `engine4_sitestore_membership` SET `role_id` = '0' WHERE `engine4_sitestore_membership`.`store_id` = " . $sitestore->store_id . ";");
      }
      //END SITESTOREMEMBER CODE  
      //START PROFILE MAPPING WORK IF CATEGORY IS EDIT
      if ($previous_category_id != $sitestore->category_id) {
        Engine_Api::_()->getDbtable('profilemaps', 'sitestore')->editCategoryMapping($sitestore);
      }

      //END PROFILE MAPPING WORK IF CATEGORY IS EDIT
      //INSERT ACTIVITY IF STORE IS JUST GETTING PUBLISHED
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($sitestore);
      if (count($action->toArray()) <= 0 && isset($values['draft']) && $values['draft'] == '1' && empty($sitestore->pending)) {
        Engine_Api::_()->sitestore()->attachStoreActivity($sitestore);
      }
      $db->commit();

      if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.category.edit', 0) && !empty($sitestore->category_id)) {

        $this->view->category_id = $sitestore->category_id;
        $this->view->subcategory_id = $subcategory_id = $sitestore->subcategory_id;
        $table = Engine_Api::_()->getDbtable('categories', 'sitestore');
        $categoriesName = $table->info('name');

        $select = $table->select()->from($categoriesName, 'category_name')
                ->where("(category_id = $subcategory_id)");

        $row = $table->fetchRow($select);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $row->category_name;
        }
        $form->getElement('category_id')
                ->setIgnore(true)
                ->setAttrib('disable', true)
                ->clearValidators()
                ->setRequired(false)
                ->setAllowEmpty(true)
        ;
      } else {
        $this->view->category_id = $sitestore->category_id;
        $this->view->subcategory_id = $subcategory_id = $sitestore->subcategory_id;
        $this->view->subsubcategory_id = $subsubcategory_id = $sitestore->subsubcategory_id;
        $table = Engine_Api::_()->getDbtable('categories', 'sitestore');
        $categoriesName = $table->info('name');
        $select = $table->select()->from($categoriesName, 'category_name')
                ->where("(category_id = $subcategory_id)");

        $row = $table->fetchRow($select);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $row->category_name;
        }
      }
      $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    try {

      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($sitestore) as $action) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR DELETING STORE
  public function deleteAction() {
    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //GET STORE ID AND OBJECT
    $store_id = $this->_getParam('store_id');
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'delete');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {

      //START SUB STORE WORK
//      $getSubStoreids = Engine_Api::_()->getDbtable('stores', 'sitestore')->getsubStoreids($store_id);
//      foreach ($getSubStoreids as $getSubStoreid) {
//        Engine_Api::_()->sitestore()->onStoreDelete($getSubStoreid['store_id']);
//      }
      //END SUB STORE WORK

      Engine_Api::_()->sitestore()->onStoreDelete($store_id);
      $newVar = _ENGINE_SSL ? 'https://' : 'http://';
      $getBaseUrl = $this->view->url(array('action' => 'account'), 'sitestoreproduct_general', true) . '/menuType/my-stores';
      $this->_redirect($newVar . $_SERVER['HTTP_HOST'] . $getBaseUrl);      
    }
  }

  //ACTION: CLOSE / OPEN STORE
  public function closeAction() {
    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET STORE OBJECT
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $this->_getParam('store_id'));

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK 

    $sitestore->closed = $this->_getParam('closed');
    $sitestore->save();

    $check = $this->_getParam('check');
    if (!empty($check)) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'my-stores'), 'sitestore_manageadmins', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitestore_general', true);
    }
  }

  //ACTION FOR CONSTRUCT TAG CLOUD
  public function tagsCloudAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    //GENERATE TAG-CLOULD HIDDEN FROM
    $this->view->form = $form = new Sitestore_Form_Searchtagcloud();
    $category_id = $this->_getParam('category_id', 0);

    //CONSTRUCTING TAG CLOUD
    $tag_array = array();
    $tag_cloud_array = Engine_Api::_()->getDbtable('stores', 'sitestore')->getTagCloud('', $category_id, 0);
    $tag_id_array = array();
    foreach ($tag_cloud_array as $vales) {
      $tag_array[$vales['text']] = $vales['Frequency'];
      $tag_id_array[$vales['text']] = $vales['tag_id'];
    }

    if (!empty($tag_array)) {
      $max_font_size = 18;
      $min_font_size = 12;
      $max_frequency = max(array_values($tag_array));
      $min_frequency = min(array_values($tag_array));
      $spread = $max_frequency - $min_frequency;

      if ($spread == 0) {
        $spread = 1;
      }

      $step = ($max_font_size - $min_font_size) / ($spread);

      $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);

      $this->view->tag_data = $tag_data;
      $this->view->tag_id_array = $tag_id_array;
    }
    $this->view->tag_array = $tag_array;
  }

  //ACTION FOR FETCHING SUB-CATEGORY
  public function subcategoryAction() {

    $category_id_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id_temp');
    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($category_id_temp);
    if (!empty($row->category_name)) {
      $categoryname = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($row->category_name);
    }
    $data = array();
    $this->view->subcats = $data;
    if (empty($category_id_temp))
      return;
    $results = Engine_Api::_()->getDbTable('categories', 'sitestore')->getSubCategories($category_id_temp);
    foreach ($results as $value) {
      $content_array = array();
      $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($value->category_name);
      $content_array['category_id'] = $value->category_id;
      $content_array['categoryname_temp'] = $categoryname;
      $data[] = $content_array;
    }
    $this->view->subcats = $data;
  }

  //ACTION FOR STORE PUBLISH
  public function publishAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //SMOOTHBOX
    if (null == $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {
      //NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    $store_id = $this->view->store_id = $this->_getParam('store_id');

    if (!$this->getRequest()->isPost())
      return;

    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $this->view->permission = true;
    $this->view->success = false;
    $db = Engine_Api::_()->getDbtable('stores', 'sitestore')->getAdapter();
    $db->beginTransaction();
    if (!empty($_POST['search']))
      $search = 1;
    else
      $search = 0;
    try {
      $sitestore->modified_date = new Zend_Db_Expr('NOW()');
      $sitestore->draft = 1;
      $sitestore->search = $search;
      $sitestore->save();
      $db->commit();
      if (!empty($sitestore->draft) && empty($sitestore->pending)) {
        Engine_Api::_()->sitestore()->attachStoreActivity($sitestore);
      }
      $this->view->success = true;
    } catch (Exception $e) {
      $db->rollback();
      throw $e;
    }

    $this->_forwardCustom('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully Published !'))
    ));
  }

  //ACTION FOR STORE URL VALIDATION AT STORE CREATION TIME
  public function storeurlvalidationAction() {

    $view = Zend_Registry::get('Zend_View');
    $store_url = $this->_getParam('store_url');
    $sitestoreUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreurl');
    if (!empty($sitestoreUrlEnabled)) {
      $urlArray = Engine_Api::_()->sitestore()->getBannedUrls();
    }
    if (empty($store_url)) {
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => '<span style="color:red;"><img src="'.$view->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/cross.png"/>URL not valid.</span>'));
      exit();
    }

    $url_lenght = strlen($store_url);
    if ($url_lenght < 3) {
      $error_msg1 = Zend_Registry::get('Zend_Translate')->_("URL component should be atleast 3 characters long.");
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/cross.png'/>$error_msg1</span>"));
      exit();
    } elseif ($url_lenght > 255) {
      $error_msg2 = Zend_Registry::get('Zend_Translate')->_("URL component should be maximum 255 characters long.");
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/cross.png'/>$error_msg2</span>"));
      exit();
    }

    $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.change.url', 1);
    $check_url = $this->_getParam('check_url');
    if (!empty($check_url)) {
      $storeId = $this->_getParam('store_id');
      $store_id = Engine_Api::_()->sitestore()->getStoreId($store_url, $storeId);
    } else {
      $store_id = Engine_Api::_()->sitestore()->getStoreId($store_url);
    }
    if (!empty($sitestoreUrlEnabled)) {
      if (!empty($store_id) || (in_array(strtolower($store_url), $urlArray))) {
        $error_msg3 = Zend_Registry::get('Zend_Translate')->_("URL not available.");
        echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/cross.png'/>$error_msg3</span>"));
        exit();
      }
    } else {
      if (!empty($store_id)) {
        $error_msg3 = Zend_Registry::get('Zend_Translate')->_("URL not available.");
        echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/cross.png'/>$error_msg3</span>"));
        exit();
      }
    }



    if (!preg_match("/^[a-zA-Z0-9-_]+$/", $store_url)) {
      $error_msg4 = Zend_Registry::get('Zend_Translate')->_("URL component can contain alphabets, numbers, underscores & dashes only.");
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/cross.png'/>$error_msg4</span>"));
      exit();
    } else {
      $error_msg5 = Zend_Registry::get('Zend_Translate')->_("URL Available!");
      echo Zend_Json::encode(array('success' => 1, 'success_msg' => "<span style='color:green;'><img src='".$view->layout()->staticBaseUrl."application/modules/Sitestore/externals/images/tick.png'/>$error_msg5</span>"));
      exit();
    }
  }

  //ACITON FOR LISTING STORES AT HOME STORE
  public function ajaxHomeListAction() {
    $this->view->category_id = $category_id = $this->_getParam('category_id', 0);
    $tab_show_values = $this->_getParam('tab_show', null);
    $this->view->list_view = $this->_getParam('list_view', 0);
    $this->view->grid_view = $this->_getParam('grid_view', 0);
    $this->view->map_view = $this->_getParam('map_view', 0);
    $this->view->defaultView = $this->_getParam('defaultView', 0);
    $this->view->active_tab_list = $list_limit = $this->_getParam('list_limit', 0);
    $this->view->active_tab_image = $grid_limit = $this->_getParam('grid_limit', 0);
    $this->view->columnHeight = $this->_getParam('columnHeight', 350);
    $this->view->columnWidth = $this->_getParam('columnWidth', 188);
    $this->view->showfeaturedLable = $this->_getParam('showfeaturedLable', 1);
    $this->view->showsponsoredLable = $this->_getParam('showsponsoredLable', 1);
    $this->view->showlocation = $this->_getParam('showlocation', 1);
    $this->view->showprice = $this->_getParam('showprice', 1);
    $this->view->showpostedBy = $this->_getParam('showpostedBy', 1);
    $this->view->showdate = $this->_getParam('showdate', 1);
    $this->view->turncation = $this->_getParam('turncation', 15);
    $this->view->showlikebutton = $this->_getParam('turncation', 15);
    
    $params = array();
    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if($this->view->detactLocation) {
			$this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
			$this->view->latitude = $params['latitude'] =$this->_getParam('latitude', 0);
			$this->view->longitude = $params['longitude'] =$this->_getParam('longitude', 0);
    }
    
    $this->view->statistics = Zend_Json_Decoder::decode($this->_getParam('statistics'));
    $limit = $list_limit > $grid_limit ? $list_limit : $grid_limit;
    $this->view->sitestoresitestore = $sitestore = Engine_Api::_()->sitestore()->getLising($tab_show_values, array_merge(array('limit' => $limit, 'category_id' => $category_id), $params));

    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0);

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitestore()->enableLocation();
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
    // Rating enable /disable
    $this->view->ratngShow = $ratingShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');

    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
  }

  public function setPhpIniMemorySize() {
    $memory_size = ini_get('memory_limit');
    $memory_Size_int_array = explode("M", $memory_size);
    $memory_Size_int = $memory_Size_int_array[0];
    if ($memory_Size_int <= 32)
      ini_set('memory_limit', '64M');
  }

  //ACTION FOR GETTING THE STORES WHICH STORES CAN BE SEARCH
  public function getSearchStoresAction() {

    $usersitestores = Engine_Api::_()->getDbtable('stores', 'sitestore')->getDayItems($this->_getParam('text'), $this->_getParam('limit', 10), $this->_getParam('category_id'));
    $data = array();
    $mode = $this->_getParam('struct');
    $count = count($usersitestores);
    if ($mode == 'text') {
      $i = 0;
      foreach ($usersitestores as $usersitestore) {
        $store_url = $this->view->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($usersitestore->store_id)), 'sitestore_entry_view', true);
        $i++;
        $content_photo = $this->view->itemPhoto($usersitestore, 'thumb.icon');
        $data[] = array(
            'id' => $usersitestore->store_id,
            'label' => $usersitestore->title,
            'photo' => $content_photo,
            'store_url' => $store_url,
            'total_count' => $count,
            'count' => $i
        );
      }
    } else {
      $i = 0;
      foreach ($usersitestores as $usersitestore) {
        $store_url = $this->view->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($usersitestore->store_id)), 'sitestore_entry_view', true);
        $content_photo = $this->view->itemPhoto($usersitestore, 'thumb.icon');
        $i++;
        $data[] = array(
            'id' => $usersitestore->store_id,
            'label' => $usersitestore->title,
            'photo' => $content_photo,
            'store_url' => $store_url,
            'total_count' => $count,
            'count' => $i
        );
      }
    }
    if (!empty($data) && $i >= 1) {
      if ($data[--$i]['count'] == $count) {
        $data[$count]['id'] = 'stopevent';
        $data[$count]['label'] = $this->_getParam('text');
        $data[$count]['store_url'] = 'seeMoreLink';
        $data[$count]['total_count'] = $count;
      }
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR FETCHING SUB-CATEGORY
  public function subsubcategoryAction() {

    $subcategory_id_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory_id_temp');

    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($subcategory_id_temp);
    if (!empty($row->category_name)) {
      $categoryname = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategorySlug($row->category_name);
    }
    $data = array();
    $this->view->subsubcats = $data;
    if (empty($subcategory_id_temp))
      return;

    $results = Engine_Api::_()->getDbTable('categories', 'sitestore')->getSubCategories($subcategory_id_temp);

    foreach ($results as $value) {
      $content_array = array();
      $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($value->category_name);
      $content_array['category_id'] = $value->category_id;
      $content_array['categoryname_temp'] = $categoryname;
      $data[] = $content_array;
    }
    $this->view->subsubcats = $data;
  }

  //ACTION FOR SHOWING LOCAITON IN MAP WITH GET DIRECTION
  public function viewMapAction() {

    $this->_helper->layout->setLayout('default-simple');
    $value['id'] = $this->_getParam('id');
    if (!$this->_getParam('id'))
      return $this->_forwardCustom('notfound', 'error', 'core');

    $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestore');
    $select = $locationTable->select();
    $select->where('store_id = ?', $value['id']);
    $item = $locationTable->fetchRow($select);

    $params = (array) $item->toArray();
    if (is_array($params)) {
      $this->view->checkin = $params;
    } else {
      return $this->_forwardCustom('notfound', 'error', 'core');
    }
  }
  
  public function viewOwnerStoresAction(){

    $store_id = $this->_getParam('store_id');
    $owner_id = $this->_getParam('owner_id');

    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0);
    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitestore()->enableLocation();


    //SITESTORE-REVIEW IS ENABLED OR NOT
    $this->view->ratngShow = $ratingShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');

    //CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOT
    $adminstores = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdminStores($owner_id);

    //GET STUFF
    $manageadmin_ids = array();
    foreach ($adminstores as $adminstore) {
      $manageadmin_ids[] = $adminstore->store_id;
    }
    $manageadmin_values = array();
    $manageadmin_values['adminstores'] = $manageadmin_ids;
    $manageadmin_values['orderby'] = 'creation_date';
    $manageadmin_data = Engine_Api::_()->sitestore()->getSitestoresPaginator($manageadmin_values, null);
    $this->view->manageadmin_count = $manageadmin_data->getTotalItemCount();
    //END CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
    
    $values = array();
    $values['user_id'] = $owner_id;
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if($viewer_id == $owner_id){
      $values['type'] = 'manage';
    }else{
      $values['type'] = 'home';
    }
    
    $values['type_location'] = 'manage';

    //GET PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->sitestore()->getSitestoresPaginator($values, null);
    $this->view->current_count = $paginator->getTotalItemCount();
    
  }
  
  //ACTION FOR ENABLE/DISABLE ALL PRODUCTS OF STORE THE SITESTORE
  public function toggleStoreProductsStatusAction() {
    
     //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->_helper->layout->setLayout('admin-simple');
    $store_id = $this->_getParam('store_id');
    $closed = $this->_getParam('closed');
    if(empty ($closed)){
      $this->view->enable = $enable = 1;
    }else{
      $this->view->enable = $enable = 0;
    } 
    if ($this->getRequest()->isPost()) {
      if (array_key_exists('Yes', $_POST)) {
        Engine_Api::_()->getDbtable('stores', 'sitestore')->toggleStoreProductsStatus($store_id, $enable);
      
        $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
//              'parentRefresh' => 10,
        'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_id' => $store_id, 'closed' => $closed), 'sitestore_close', true),
//              'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
        
      } else {
          return $this->_forward('success', 'utility', 'core', array(
                'messages' => $this->view->translate(''),
                'smoothboxClose' => true,
                'parentRefresh' => false,
            ));
      }
    }
    
  }

  protected function renderUserWidgetCustom() {
    if (!Engine_Api::_()->seaocore()->isSitemobileApp())
      return;
    $content_id = $this->_getParam('tab');
    //  $params = $this->_getAllParams();
    // Render by content row
    if (null !== $content_id) {
      $view = $this->_getParam('view');
      $show_container = $this->_getParam('container', true);
      $contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitestore');
      $row = $contentTable->find($content_id)->current();

				if (null !== $row) {
					// Build full structure from children
					$mobilecontentgroup_id = $row->mobilecontentstore_id;
					$pageTable = Engine_Api::_()->getDbtable('mobileContentstores', 'sitestore');
					$content = $contentTable->fetchAll($contentTable->select()->where('mobilecontentstore_id = ?', $mobilecontentstore_id));
					$structure = $pageTable->createElementParams($row);
					$children = $pageTable->prepareContentArea($content, $row);
					if (!empty($children)) {
						$structure['elements'] = $children;
					}
					$structure['request'] = $this->getRequest();
					$structure['action'] = $view;
          if($this->getContainerContent($structure, $show_container))
           return true;       
				} else {
					$contentTable = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitestore');
					$row = $contentTable->find($content_id)->current();
					$store_id = Engine_Api::_()->sitestore()->getMobileWidgetizedStore()->page_id;
					$content = $contentTable->fetchAll($contentTable->select()->where('store_id = ?', $store_id));
					$structure = $contentTable->createElementParams($row);
					$children = $contentTable->prepareContentArea($content, $row);
					if (!empty($children)) {
						$structure['elements'] = $children;
					}
					$structure['request'] = $this->getRequest();
					$structure['action'] = $view;
          if($this->getContainerContent($structure, $show_container))
          return true; 
			}    
    }
  } 
  
}
