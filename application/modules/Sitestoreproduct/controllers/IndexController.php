<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_IndexController extends Seaocore_Controller_Action_Standard {

    protected $_session;

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
            return;

        if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
            return;

        //redirect to mobile actions
        if (!$this->getRequest()->isPost()) {
            $mobileSupportedAction = array(
                'delete',
                'copy-product'
            );

            if (!Engine_Api::_()->seaocore()->checkSitemobileMode('fullsite-mode') && in_array($this->getRequest()->getActionName(), $mobileSupportedAction)) {
                return $this->_helper->redirector->gotoRoute(
                                array_merge(
                                        $this->getRequest()->getParams(), array("action" => $this->getRequest()->getActionName() . "-mobile", "rewrite" => null)
                                ), 'default', true);
            }
        }
    }

    public function categoryHomeAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $category_id = $request->getParam('category_id', null);
        Zend_Registry::set('sitestoreproductCategoryId', $category_id);

        //GET STORE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitestoreproduct_index_category-home_category_$category_id");
        $pageObject = $pageTable->fetchRow($pageSelect);

        $tempPage_id = !empty($pageObject) ? $pageObject->page_id : 0;

        $this->_helper->content
                ->setContentName($tempPage_id)
                ->setNoRender()
                ->setEnabled();
    }

    public function startupAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        // CHECK IF ANY PACKAGE AVAILABLE OR NOT FOR THIS MEMBER
        if (!empty($viewer_id) && Engine_Api::_()->sitestore()->hasPackageEnable()) {
            $packages_select = Engine_Api::_()->getItemtable('sitestore_package')->getPackagesSql(null);
            $paginator = Zend_Paginator::factory($packages_select);
            $getPaginatorCount = $paginator->getTotalItemCount();
            if (empty($getPaginatorCount))
                return $this->_forward('requireauth', 'error', 'core');
        }

        $pagesArray = array();
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        $fetchPages = Engine_Api::_()->getItemTable('sitestoreproduct_startuppage')->select()->where("startuppages_id IN (1, 2, 3, 4)")->where("status =?", 1)->query()->fetchAll();
        foreach ($fetchPages as $page) {
            $pagesArray[$page['startuppages_id']] = $page;
        }
        $this->view->pages = $pagesArray;

        $menusClass = new Sitestore_Plugin_Menus();
        $canCreateStores = $menusClass->canCreateSitestores();
        if (!$canCreateStores ||
                (!Engine_Api::_()->sitestore()->hasPackageEnable() &&
                !Engine_Api::_()->sitestoreproduct()->getLevelSettings("allow_store_create")
                )
        ) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        $coreversion = $coremodule->version;
        if ($coreversion < '4.1.0') {
            $this->_helper->content->render();
        } else {
            $this->_helper->content
//              ->setNoRender()
                    ->setEnabled();
        }
    }

    public function getStartedAction() {
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

    public function basicAction() {
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

    public function storiesAction() {
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

    public function toolsAction() {
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

    public function pinboardAction() {
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

    //NONE USER SPECIFIC METHODS
    public function indexAction() {

        //GET STORE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitestoreproduct_index_index");
        $pageObject = $pageTable->fetchRow($pageSelect);

        //SET META PARAMS
        $params = array();
        if (empty($pageObject->title)) {
            $params['default_title'] = $title = $pageObject->title;
        }
        $description = '';
        if (empty($pageObject->description)) {
            $params['description'] = $description = Zend_Registry::get('Zend_Translate')->_('This is the product browse page.');
        }

        //GET PRODUCT CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $browseCategories = Zend_Registry::isRegistered('sitestoreproductBrowseCategories') ? Zend_Registry::get('sitestoreproductBrowseCategories') : null;

        $category_id = $request->getParam('category_id', null);
        if (empty($browseCategories)) {
            $this->view->categoryError = $this->view->translate("There are no categories found for this page.");
            return;
        }

        if (!empty($category_id)) {

            $params['product_type_title'] = $title = Zend_Registry::get('Zend_Translate')->_('Products');
            $meta_title = $tableCategory->getCategory($category_id)->meta_title;
            if (empty($meta_title)) {
                $tempSitestoreproductCat = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id);
                if (!empty($tempSitestoreproductCat))
                    $params['categoryname'] = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname'] = $meta_title;
            }

            $meta_description = $tableCategory->getCategory($category_id)->meta_description;
            if (!empty($meta_description))
                $params['description'] = $meta_description;

            $meta_keywords = $tableCategory->getCategory($category_id)->meta_keywords;
            if (empty($meta_keywords)) {
                $tempSubCategory = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id);
                if (!empty($tempSubCategory))
                    $params['categoryname_keywords'] = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id)->getCategorySlug();
            } else {
                $params['categoryname_keywords'] = $meta_keywords;
            }

            $subcategory_id = $request->getParam('subcategory_id', null);

            if (!empty($subcategory_id)) {
                $meta_title = $tableCategory->getCategory($subcategory_id)->meta_title;
                if (empty($meta_title)) {
                    $tempCategory = Engine_Api::_()->getItem('sitestoreproduct_category', $subcategory_id);
                    if (!empty($tempCategory))
                        $params['subcategoryname'] = Engine_Api::_()->getItem('sitestoreproduct_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname'] = $meta_title;
                }
                $meta_description = $tableCategory->getCategory($subcategory_id)->meta_description;
                if (!empty($meta_description))
                    $params['description'] = $meta_description;

                $meta_keywords = $tableCategory->getCategory($subcategory_id)->meta_keywords;
                if (empty($meta_keywords)) {
                    $tempSubCategory = Engine_Api::_()->getItem('sitestoreproduct_category', $subcategory_id);
                    if (!empty($tempSubCategory))
                        $params['subcategoryname_keywords'] = Engine_Api::_()->getItem('sitestoreproduct_category', $subcategory_id)->getCategorySlug();
                } else {
                    $params['subcategoryname_keywords'] = $meta_keywords;
                }

                $subsubcategory_id = $request->getParam('subsubcategory_id', null);

                if (!empty($subsubcategory_id)) {
                    $meta_title = $tableCategory->getCategory($subsubcategory_id)->meta_title;
                    if (empty($meta_title)) {
                        $tempCate = Engine_Api::_()->getItem('sitestoreproduct_category', $subsubcategory_id);
                        if (!empty($tempCate))
                            $params['subsubcategoryname'] = Engine_Api::_()->getItem('sitestoreproduct_category', $subsubcategory_id)->getCategorySlug();
                    } else {
                        $params['subsubcategoryname'] = $meta_title;
                    }
                    $meta_description = $tableCategory->getCategory($subsubcategory_id)->meta_description;
                    if (!empty($meta_description))
                        $params['description'] = $meta_description;

                    $meta_keywords = $tableCategory->getCategory($subsubcategory_id)->meta_keywords;
                    if (empty($meta_keywords)) {
                        $tempSubCat = Engine_Api::_()->getItem('sitestoreproduct_category', $subsubcategory_id);
                        if (!empty($tempSubCat))
                            $params['subsubcategoryname_keywords'] = Engine_Api::_()->getItem('sitestoreproduct_category', $subsubcategory_id)->getCategorySlug();
                    } else {
                        $params['subsubcategoryname_keywords'] = $meta_keywords;
                    }
                }
            }
        }

        //SET META TITLE
        Engine_Api::_()->sitestoreproduct()->setMetaTitles($params);

        //SET META TITLE
        Engine_Api::_()->sitestoreproduct()->setMetaDescriptionsBrowse($params);

        //GET LOCATION
        if (isset($_GET['location']) && !empty($_GET['location'])) {
            $params['location'] = $_GET['location'];
        }

        //GET TAG
        if (isset($_GET['tag']) && !empty($_GET['tag'])) {
            $params['tag'] = $_GET['tag'];
        }

        //GET TAG
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $params['search'] = $_GET['search'];
        }

        //GET PRODUCT TITLE
        $params['product_type_title'] = Zend_Registry::get('Zend_Translate')->_('Products');

        $params['page'] = 'browse';

        //SET META KEYWORDS
        Engine_Api::_()->sitestoreproduct()->setMetaKeywords($params);

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->content
                    ->setContentName($pageObject->page_id)
                    ->setNoRender()
                    ->setEnabled();
        } else {
            $this->_helper->content
                    ->setNoRender()
                    ->setEnabled();
        }
    }

    //NONE USER SPECIFIC METHODS
    public function homeAction() {
        //GET STORE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitestoreproduct_index_home");
        $pageObject = $pageTable->fetchRow($pageSelect);

        //GET PRODUCT TITLE
        $params['product_type_title'] = Zend_Registry::get('Zend_Translate')->_('Products');

        Zend_Registry::set('sitestoreproduct.currency.symbol', Engine_Api::_()->sitestoreproduct()->getCurrencySymbol());

        //SET META KEYWORDS
        Engine_Api::_()->sitestoreproduct()->setMetaKeywords($params);

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->content
                    ->setContentName($pageObject->page_id)
                    ->setNoRender()
                    ->setEnabled();
        } else {
            $this->_helper->content
                    ->setNoRender()
                    ->setEnabled();
        }
    }

    //NONE USER SPECIFIC METHODS
    public function categoriesAction() {

        //GET STORE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitestoreproduct_index_categories");
        $pageObject = $pageTable->fetchRow($pageSelect);

        //GET PRODUCT TITLE

        $siteinfo = $this->view->layout()->siteinfo;
        $titles = $siteinfo['title'];
        $keywords = $siteinfo['keywords'];

        if (!empty($titles))
            $titles .= ' - ';
        $titles .= 'Products';
        $siteinfo['title'] = $titles;

        if (!empty($keywords))
            $keywords .= ' - ';
        $keywords .= 'Products';
        $siteinfo['keywords'] = $keywords;

        $this->view->layout()->siteinfo = $siteinfo;


        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->content
                    ->setContentName($pageObject->page_id)
                    ->setNoRender()
                    ->setEnabled();
        } else {
            $this->_helper->content
                    ->setNoRender()
                    ->setEnabled();
        }
    }

    //ACTION FOR SHOWING SPONSORED PRODUCTS IN WIDGET
    public function homesponsoredAction() {

        //CORE SETTINGS API
        $settings = Engine_Api::_()->getApi('settings', 'core');

        //SEAOCORE API
        $this->view->seacore_api = Engine_Api::_()->seaocore();

        //RETURN THE OBJECT OF LIMIT PER STORE FROM CORE SETTING TABLE
        $this->view->sponserdSitestoreproductsCount = $limit_sitestoreproduct = $_GET['curnt_limit'];
        $limit_sitestoreproduct_horizontal = $limit_sitestoreproduct * 2;

        $values = array();
        $values = $this->_getAllParams();

        //GET COUNT
        $totalCount = $_GET['total'];

        //RETRIVE THE VALUE OF START INDEX
        $startindex = $_GET['startindex'];

        if ($startindex > $totalCount) {
            $startindex = $totalCount - $limit_sitestoreproduct;
        }

        if ($startindex < 0) {
            $startindex = 0;
        }

        $this->view->sponsoredIcon = $this->_getParam('sponsoredIcon', 1);
        $this->view->showOptions = $this->_getParam('showOptions', array("category", "rating", "review", "compare", "wishlist"));
        $this->view->featuredIcon = $this->_getParam('featuredIcon', 1);
        $this->view->newIcon = $this->_getParam('newIcon', 1);
        $this->view->priceWithTitle = $this->_getParam('priceWithTitle', 0);
        $this->view->showAddToCart = $this->_getParam('showAddToCart', 1);
        $this->view->showinStock = $this->_getParam('showinStock', 1);
        $this->view->identity = $this->_getParam('widget_id', 0);
        //RETRIVE THE VALUE OF BUTTON DIRECTION
        $this->view->direction = $_GET['direction'];
        $values['start_index'] = $startindex;
        $sitestoreproductTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
        $this->view->totalItemsInSlide = $values['limit'] = $limit_sitestoreproduct_horizontal;
        $this->view->popularity = $values['popularity'] = $this->_getParam('popularity', 'product_id');
        $this->view->fea_spo = $fea_spo = $this->_getParam('fea_spo', null);
        if ($fea_spo == 'featured') {
            $values['featured'] = 1;
        } elseif ($fea_spo == 'newlabel') {
            $values['newlabel'] = 1;
        } elseif ($fea_spo == 'sponsored') {
            $values['sponsored'] = 1;
        } elseif ($fea_spo == 'fea_spo') {
            $values['sponsored'] = 1;
            $values['featured'] = 1;
        }
        //GET PRODUCTS
        $this->view->sitestoreproducts = $sitestoreproductTable->getProduct('', $values);
        $this->view->count = count($this->view->sitestoreproducts);
        $this->view->vertical = $_GET['vertical'];
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        $this->view->title_truncation = $this->_getParam('title_truncation', 50);
        $this->view->blockHeight = $this->_getParam('blockHeight', 245);
        $this->view->blockWidth = $this->_getParam('blockWidth', 150);
    }

    //ACTION FOR VIEW PRODUCT PROFILE STORE
    public function viewAction() {
        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET PRODUCT ID AND OBJECT
        $product_id = $this->_getParam('product_id');
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $this->_getParam('product_id'));
        $sitestoreproductViewType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.view.type', false);

        if (empty($sitestoreproduct) || empty($sitestoreproductViewType)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //WHO CAN VIEW THE PRODUCTS
        if (!$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, null, "view")->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //ADD CSS
        $this->view->headLink()
                ->prependStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');
        $this->view->headLink()
                ->prependStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproductprofile.css');
        $this->view->headScript()
                ->appendFile($this->view->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');

        //SET SITESTOREPRODUCT SUBJECT
        Engine_Api::_()->core()->setSubject($sitestoreproduct);

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //GET LEVEL SETTING
        $can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "view");

        //AUTHORIZATION CHECK
        if ($can_view != 2 && ((!empty($sitestoreproduct->draft) || empty($sitestoreproduct->approved)) && ($sitestoreproduct->owner_id != $viewer_id))) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if ($can_view != 2 && ($sitestoreproduct->owner_id != $viewer_id)) {
            $reviewApi = Engine_Api::_()->sitestoreproduct();
            $expirySettings = $reviewApi->expirySettings();
            if ($expirySettings == 2) {
                $approveDate = $reviewApi->adminExpiryDuration();
                if ($approveDate > $sitestoreproduct->approved_date) {
                    return $this->_forward('requireauth', 'error', 'core');
                }
            }
        }

        //STARTE STORE AUTHORIZATION CHECKS
        $sitestoreObj = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestoreObj, 'view');
        if (empty($isManageAdmin)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->sitestore()->canViewStore($sitestoreObj)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //END STORE AUTHORIZATION CHECKS

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.profiletab', 1)) {
            $this->view->headLink()
                    ->prependStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_tabs.css');
            $script = <<<EOF
      en4.core.runonce.add(function() {
          $$('.tabs_alt').addClass('sr_sitestoreproduct_main_tabs_alt');
      });
EOF;
            $this->view->headScript()->appendScript($script);
        }

        //INCREMENT IN NUMBER OF VIEWS
        if (!$sitestoreproduct->getOwner()->isSelf($viewer)) {
            $sitestoreproduct->view_count++;
            $sitestoreproduct->save();
            $params = array();
            $params['resource_id'] = $sitestoreproduct->product_id;
            $params['resource_type'] = $sitestoreproduct->getType();
            $params['viewer_id'] = 0;
            $params['type'] = 'editor';
            $isEditorReviewed = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct')->canPostReview($params);
            if ($isEditorReviewed) {
                $review = Engine_Api::_()->getItem('sitestoreproduct_review', $isEditorReviewed);
                $review->view_count++;
                $review->save();
            }
        }

        //SET PRODUCT VIEW DETAILS
        if (!empty($viewer_id)) {
            Engine_Api::_()->getDbtable('vieweds', 'sitestoreproduct')->setVieweds($product_id, $viewer_id);
        }

        //GET SITESTOREPRODUCT OWNER LEVEL ID
        $owner_level_id = Engine_Api::_()->getItem('user', $sitestoreproduct->owner_id)->level_id;

        //PROFILE STYLE IS ALLOWED OR NOT
        $style_perm = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('sitestoreproduct_product', $owner_level_id, "style");
        if ($style_perm) {

            //GET STYLE TABLE
            $tableStyle = Engine_Api::_()->getDbtable('styles', 'core');

            //MAKE QUERY
            $getStyle = $tableStyle->select()
                    ->from($tableStyle->info('name'), array('style'))
                    ->where('type = ?', 'sitestoreproduct_product')
                    ->where('id = ?', $sitestoreproduct->getIdentity())
                    ->query()
                    ->fetchColumn();

            if (!empty($getStyle)) {
                $this->view->headStyle()->appendStyle($getStyle);
            }
        }

        if (null != ($tab = $this->_getParam('tab'))) {
            //provide widgties page
            $friend_tab_function = <<<EOF
                                        var tab_content_id_sitestoreproduct = "$tab";
                                        this.onload = function()
                                        {
                                                tabContainerSwitch($('main_tabs').getElement('.tab_' + tab_content_id_sitestoreproduct));
                                        }
EOF;
            $this->view->headScript()->appendScript($friend_tab_function);
        }

        //GET STORE OBJECT
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $pageSelect = $pageTable->select()->where('name = ?', "sitestoreproduct_index_view");
        $pageObject = $pageTable->fetchRow($pageSelect);

        //SET META PARAMS
        $params = array();

        //GET PRODUCT CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');

        $category_id = $sitestoreproduct->category_id;
        if (!empty($category_id)) {

            $params['categoryname'] = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id)->getCategorySlug();

            $subcategory_id = $sitestoreproduct->subcategory_id;

            if (!empty($subcategory_id)) {

                $params['subcategoryname'] = ucfirst(Engine_Api::_()->getItem('sitestoreproduct_category', $subcategory_id)->getCategorySlug());

                $subsubcategory_id = $sitestoreproduct->subsubcategory_id;

                if (!empty($subsubcategory_id)) {

                    $params['subsubcategoryname'] = Engine_Api::_()->getItem('sitestoreproduct_category', $subsubcategory_id)->getCategorySlug();
                }
            }
        }

        //GET KEYWORDS
        $params['keywords'] = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($sitestoreproduct->getIdentity(), 'keywords');

        //SET META KEYWORDS
        Engine_Api::_()->sitestoreproduct()->setMetaKeywords($params);

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->content
                    ->setContentName($pageObject->page_id)
                    ->setNoRender()
                    ->setEnabled();
        } else {
            $this->_helper->content
                    ->setNoRender()
                    ->setEnabled();
        }
    }

    //ACTION FOR MANAGING THE PRODUCTS
    public function manageAction() {

        //ONLY LOGGED IN USER CAN VIEW THIS STORE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //CREATION PRIVACY CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "create")->isValid())
            return;

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");

        //GET EDIT AND DELETE SETTINGS
        $this->view->can_edit = $this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "edit")->checkRequire();
        $this->view->can_delete = $this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "delete")->checkRequire();

        //ABLE TO UPLOAD VIDEO OR NOT        
        $this->view->allowed_upload_video = 1;
        $allowed_upload_videoEnable = Engine_Api::_()->sitestoreproduct()->enableVideoPlugin();
        if (empty($allowed_upload_videoEnable))
            $this->view->allowed_upload_video = 0;

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.video', 1)) {
            //CHECK FOR SOCIAL ENGINE CORE VIDEO PLUGIN
            $allowed_upload_video_video = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'create');
            if (empty($allowed_upload_video_video))
                $this->view->allowed_upload_video = 0;
        }

        $allowed_upload_video = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "video");
        if (empty($allowed_upload_video))
            $this->view->allowed_upload_video = 0;

        //ABLE TO ADD PHOTO OR NOT
        $this->view->allowed_upload_photo = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "photo");

        //MAKE FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Search();
        $form->removeElement('show');

        //PROCESS FORM
        unset($form->getElement('orderby')->options['']);
        if ($form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
        } else {
            $values = array();
        }

        //MAKE DATA ARRAY
        $values['user_id'] = $viewer_id;
        $values['type'] = 'manage';
        $values['orderby'] = 'product_id';

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        $this->view->category_id = $values['category_id'] = isset($params['category_id']) ? $params['category_id'] : 0;
        $this->view->subcategory_id = $values['subcategory_id'] = isset($params['subcategory_id']) ? $params['subcategory_id'] : 0;
        $this->view->subsubcategory_id = $values['subsubcategory_id'] = isset($params['subsubcategory_id']) ? $params['subsubcategory_id'] : 0;

        //GET CUSTOM FIELD VALUES
        $customFieldValues = array_intersect_key($values, $form->getFieldElements());

        //GET PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getSitestoreproductsPaginator($values, $customFieldValues);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($values['page']);
        $this->view->current_count = $this->view->paginator->getTotalItemCount();

        $this->view->formValues = $values;
        $form->populate($values);

        $categories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategories(null, 0, 0, 1);
        $categories_slug[0] = "";
        if (count($categories) != 0) {
            foreach ($categories as $category) {
                $categories_slug[$category->category_id] = $category->getCategorySlug();
            }
        }
        $this->view->categories_slug = $categories_slug;

        //MAXIMUM ALLOWED PRODUCTS
        $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "max");

        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    }

    public function listAction(){
               //SET LAYOUT
            
        $viewer = Engine_Api::_()->user()->getViewer();
        
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
       
        $this->view->form = $form = new Sitestoreproduct_Form_Store();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

         $values = $form->getValues();
         $store_id = $values['stores'];
           $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefreshTime' => '100',
                'parentRedirect' => $this->view->url(array('action' => 'create', 'store_id' =>$store_id), 'sitestoreproduct_general', true),
                'format' => 'smoothbox',
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('loading ...'))
            ));
         
         
    }
    //ACTION FOR CREATING A NEW PRODUCT
    public function createAction() {


        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->redirector->gotoRoute(array(
                'module' => 'sitestoreproduct',
                'controller' => 'index',
                'action' => 'create-mobile',
                'store_id' => $this->_getParam("store_id")
                    ), 'default', true);
        }


        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $store_id = $this->_getParam('store_id', null);

        $this->view->page_id = $this->_getParam('page_id', null);

        $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

        //IS USER IS STORE ADMIN OR NOT
        if (empty($authValue))
            return $this->_forward('requireauth', 'error', 'core');
        else if ($authValue == 1)
            return $this->_forward('notfound', 'error', 'core');

        //SEND TAB TO TPL FILE
        $this->view->tab_selected_id = $tab_id = $this->_getParam('tab');
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');

        //GET SITESTORE ITEM
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

        $tableLocation = Engine_Api::_()->getDbTable('locations', 'sitestore');

        if (!empty($sitestore->location))
            $this->view->locationId = $tableLocation->select()->from($tableLocation->info('name'), 'location_id')->where('store_id = ?', $store_id)->where("location = ?", $sitestore->location)->query()->fetchColumn();
        if (!empty($this->view->locationId)) {
            $this->view->locationDetails = Engine_Api::_()->getItem('sitestore_location', $this->view->locationId);
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            if (empty($sitestore->approved) || !empty($sitestore->closed) || empty($sitestore->search) || empty($sitestore->draft) || !empty($sitestore->declined)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        } else {
            if (empty($sitestore->approved) || empty($sitestore->search) || empty($sitestore->draft) || !empty($sitestore->declined)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        //FLAG FOR SHOW MARKER IN DATE
        $this->view->showMarkerInDate = $this->showMarkerInDate();

        //CHECK FOR CREATION PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "create")->isValid())
            return;

        // COUNT PRODUCT CREATED BY THIS STORE AND MAXIMUM PRODUCT CREATION LIMIT
        $this->view->current_count = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductsCountInStore($store_id);
        $this->view->quota = $quota = Engine_Api::_()->sitestoreproduct()->getProductLimit($store_id);

        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitestoreproduct')->defaultProfileId();

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");

        // WORK FOR MULTILANGUAL PRODUCT TITLE STARTS    
        $settings = Engine_Api::_()->getApi('settings', 'core');
        //MULTI LANGUAGE IS ALLOWED OR NOT
        $this->view->multiLanguage = $settings->getSetting('sitestoreproduct.multilanguage', 0);

        // Comment Privacy
        $this->view->isCommentsAllow = Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product");

        //DEFAULT LANGUAGE
        $this->view->defaultLanguage = $defaultLanguage = $settings->getSetting('core.locale.locale', 'en');

        // MULTI LANGUAGE WORK
        $this->view->languageCount = 0;
        $this->view->languageData = array();
        $title_link = $this->view->add_show_hide_title_link = 'title';
        $body_link = $this->view->add_show_hide_body_link = 'body';
        $overview_link = $this->view->add_show_hide_overview_link = 'overview';
        if ($this->view->multiLanguage) {
            //GET LANGUAGE ARRAY
            $localeMultiOptions = Engine_Api::_()->sitestoreproduct()->getLanguageArray();
            $languages = $settings->getSetting('sitestoreproduct.languages');

            $this->view->languageCount = $languageCount = Count($languages);
            $this->view->languageData = array();
            if (is_array($languages)) {
                foreach ($languages as $label) {
                    $this->view->languageData[] = $label;

                    if ($this->view->languageCount >= 2 && $defaultLanguage == $label && $label != 'en') {
                        $title_link = $this->view->add_show_hide_title_link = "title_$label";
                        $body_link = $this->view->add_show_hide_title_link = "body_$label";
                        $overview_link = $this->view->add_show_hide_overview_link = "overview_$label";
                    }
                }
            }
            if (!in_array($defaultLanguage, $this->view->languageData)) {
                $this->view->defaultLanguage = 'en';
            }
        }
        // WORK FOR MULTILANGUAL PRODUCT TITLE ENDS    
        //GET TINYMCE SETTINGS
        $this->view->upload_url = "";
        $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
        if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
            $this->view->upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'sitestoreproduct_general', true);
        }

        $orientation = $this->view->layout()->orientation;
        if ($orientation == 'right-to-left') {
            $this->view->directionality = 'rtl';
        } else {
            $this->view->directionality = 'ltr';
        }

        $local_language = $this->view->locale()->getLocale()->__toString();
        $local_language = explode('_', $local_language);
        $this->view->language = $local_language[0];

        $this->view->category_count = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategories(null, 1, 0, 1);
        $this->view->sitestoreproduct_render = !empty($_POST['product_type']) ? $_POST['product_type'] : null;
        $this->view->expiry_setting = $expiry_setting = Engine_Api::_()->sitestoreproduct()->expirySettings();
        $this->view->allowSellingProducts = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($store_id, false);

        //PRODUCT FLAG        
        $packageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1);

        //GET PRODUCT USING LEVEL OR PACKAGE BASED SETTINGS
        if (!empty($packageEnable)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $sitestore->package_id);
            if (empty($packageObj->store_settings)) {
                $product_types = array('simple', 'configurable', 'virtual', 'grouped', 'bundled', 'downloadable');
            } else {
                $storeSettings = @unserialize($packageObj->store_settings);
                $product_types = $storeSettings['product_type'];
            }
        } else {
            $user = $sitestore->getOwner();
            $product_types = Engine_Api::_()->authorization()->getPermission($user->level_id, "sitestore_store", "product_type");
            $product_types = Zend_Json_Decoder::decode($product_types);
        }
        $this->view->countProductTypes = $countProductTypes = count($product_types);

        // CHECK ANY SHIPPING METHOD IS CREATED BY STORE OR NOT
        $this->view->shipping_method_exist = Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct')->isAnyShippingMethodExist($store_id);

        // CHECK ANY ENABLE LOCATIONS
        $this->view->isAnyCountryEnable = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->isAnyCountryEnable();

        //CHECK TO SHOW II ND STEP OF PRODUCT CREATION
        if ($this->getRequest()->isPost()) {
            $productTypeValue = $this->getRequest()->getPost();
            $this->view->productTypeName = $productTypeValue['product_type'];

            if ($productTypeValue['product_type'] == 'grouped' || $productTypeValue['product_type'] == 'bundled') {
                $currentProductCount = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getProductAvailability($store_id, $productTypeValue['product_type']);
                if ($currentProductCount < 2) {
                    $this->view->lessSimpleProductType = 1;
                    return;
                }
            }
        }

        //CHECK FOR 1ST STEP OF PRODUCT CREATION
        if (!$this->getRequest()->isPost() && !isset($productTypeValue['product_type'])) {
            //IS ANY SHIPPING METHOD EXIST WITH ENABLE STATUS
            $this->view->viewType = 1;

            if ($countProductTypes == 0) {
                $this->view->productType = 0;
                return;
            } else if ($countProductTypes == 1) {
                if (@in_array("bundled", $product_types) || @in_array("grouped", $product_types)) {
                    $productCount = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getProductAvailability($sitestore->store_id, $productTypeValue['product_type']);
                    $this->view->lessSimpleProductType = $this->view->withNoSingleProduct = ($productCount <= 1) ? 1 : 0;
                }
                $this->view->sitestoreproduct_render = end($product_types);

                $this->view->viewType = 0;
                $this->view->productTypeName = $product_types[0];
            } else
                $this->view->productType = $product_types;
        }else {
            $this->view->viewType = 0;
        }

        if ($this->view->productTypeName == 'virtual')
            $this->view->showProductInventory = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.productinventory', 1);
        else
            $this->view->showProductInventory = true;

        $this->view->directPayment = $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $this->view->isDownPaymentEnable = $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
        $this->view->allowProductCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.productcode', 1);

        //MAKE FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Create(array('defaultProfileId' => $defaultProfileId, 'pageId' => $store_id, 'productType' => $this->view->productTypeName, 'allowedProductTypes' => $product_types));

        $temp_allow_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($store_id, false);
        if (empty($temp_allow_selling)) {
            $form->removeElement('allow_purchase');
        }

        if (!empty($_POST['product_type']) && ($_POST['product_type'] == 'configurable' || $_POST['product_type'] == 'virtual' || $_POST['product_type'] == 'downloadable')) {
            $form->search->setValue(0);
//      $form->search->setAttribs(array('disabled' => 'disabled'));
        } else {
            $form->search->setValue(1);
        }

        if (!$this->getRequest()->isPost() || isset($productTypeValue['select'])) {
            return;
        }

        $params = array();
        $params['store_id'] = $store_id;
        $params['expiry_setting'] = $expiry_setting;
        $this->saveProduct($form, $_POST, $params);
    }

    //ACTION FOR STORE URL VALIDATION AT STORE CREATION TIME
    public function productCodeValidationAction() {

        $product_code = $this->_getParam('product_code');

        $staticBaseUrl = Zend_Registry::get('Zend_View')->layout()->staticBaseUrl;

        if (empty($product_code)) {
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => '<span style="color:red;"><img src="' . $staticBaseUrl . 'application/modules/Sitestore/externals/images/cross.png"/>' . $this->view->translate('Product SKU is not available.') . '</span>'));
            exit();
        }

        if (!@preg_match('/^[a-zA-Z0-9-_]+$/', $product_code)) {
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => '<span style="color:red;"><img src="' . $staticBaseUrl . 'application/modules/Sitestore/externals/images/cross.png"/>' . $this->view->translate('Product SKU is not available.') . '</span>'));
            exit();
        }

        $productRow = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->fetchRow(array('product_code LIKE ?' => $product_code))->product_code;

        if (!empty($productRow)) {
            echo Zend_Json::encode(array('success' => 0, 'error_msg' => '<span style="color:red;"><img src="' . $staticBaseUrl . 'application/modules/Sitestore/externals/images/cross.png"/>' . $this->view->translate('Product SKU is not available.') . '</span>'));
            exit();
        } else {
            $success_message = Zend_Registry::get('Zend_Translate')->_("Product SKU Available");
            echo Zend_Json::encode(array('success' => 1, 'success_msg' => '<span style="color:green;"><img src="' . $staticBaseUrl . 'application/modules/Sitestore/externals/images/tick.png"/>' . $success_message . '</span>'));
            exit();
        }
    }

    //ACTION FOR EDITING THE SITESTOREPRODUCT
    public function editAction() {


        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->redirector->gotoRoute(array(
                'module' => 'sitestoreproduct',
                'controller' => 'index',
                'action' => 'edit-mobile',
                'product_id' => $this->_getParam("product_id")
                    ), 'default', true);
        }

        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->product_id = $product_id = $this->_getParam('product_id', NULL);

        //IF STORE ID NOT SUPPLIED THEN STORE NOT FOUND
        if (empty($product_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($sitestoreproduct->store_id);
        $isProductEnabled = Engine_Api::_()->sitestoreproduct()->isProductEnabled();
        $this->view->directPayment = $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $this->view->isDownPaymentEnable = $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
        $this->view->allowProductCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.productcode', 1);

        //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

        $settings = Engine_Api::_()->getApi('settings', 'core');

        //MULTI LANGUAGE IS ALLOWED OR NOT
        $this->view->multiLanguage = $settings->getSetting('sitestoreproduct.multilanguage', 0);

        //DEFAULT LANGUAGE
        $this->view->defaultLanguage = $defaultLanguage = $settings->getSetting('core.locale.locale', 'en');

        //MULTI LANGUAGE WORK
        $this->view->languageCount = 0;
        $this->view->languageData = array();

        $title_link = $this->view->add_show_hide_title_link = 'title';
        $body_link = $this->view->add_show_hide_body_link = 'body';
        if ($this->view->multiLanguage) {
            //GET LANGUAGE ARRAY
            $localeMultiOptions = Engine_Api::_()->sitestoreproduct()->getLanguageArray();
            $languages = $settings->getSetting('sitestoreproduct.languages');
            $this->view->languageCount = Count($languages);
            $this->view->languageData = array();
            if (is_array($languages)) {
                foreach ($languages as $label) {
                    $this->view->languageData[] = $label;
                    if ($this->view->languageCount >= 2 && $defaultLanguage == $label && $label != 'en') {
                        $title_link = $this->view->add_show_hide_title_link = "title_$label";
                        $body_link = $this->view->add_show_hide_title_link = "body_$label";
                    }
                }
            }
        }

        $this->view->sitestores_view_menu = 1;
        $listValues = array();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->category_edit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.categoryedit', 1);
        // Comment Privacy
        $this->view->isCommentsAllow = Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product");
        $sitestoreproductinfo = $sitestoreproduct->toarray();
        $this->view->category_id = $previous_category_id = $sitestoreproduct->category_id;
        $this->view->subcategory_id = $subcategory_id = $sitestoreproduct->subcategory_id;
        $this->view->subsubcategory_id = $subsubcategory_id = $sitestoreproduct->subsubcategory_id;

        $row = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategory($subcategory_id);
        $this->view->subcategory_name = "";
        if (!empty($row)) {
            $this->view->subcategory_name = $row->category_name;
        }

        if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
            Engine_Api::_()->core()->setSubject($sitestoreproduct);
        }

        if (!$this->_helper->requireSubject()->isValid())
            return;

        if (!$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, $viewer, "edit")->isValid()) {
            return;
        }

        //MARK IN CALENDER FLAG
        $this->view->showMarkerInDate = $this->showMarkerInDate();

        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitestoreproduct')->defaultProfileId();

        //GET PROFILE MAPPING ID
        $categoryIds = $oldCategoryIds = array();
        $categoryIds[] = $oldCategoryIds[] = $sitestoreproduct->category_id;
        $categoryIds[] = $oldCategoryIds[] = $sitestoreproduct->subcategory_id;
        $categoryIds[] = $oldCategoryIds[] = $sitestoreproduct->subsubcategory_id;

        $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIds, 0, 'profile_type');

        if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
            $categoryIds = array();
            $categoryIds[] = $_POST['category_id'];
            if (isset($_POST['subcategory_id']) && !empty($_POST['subcategory_id'])) {
                $categoryIds[] = $_POST['subcategory_id'];
            }
            if (isset($_POST['subsubcategory_id']) && !empty($_POST['subsubcategory_id'])) {
                $categoryIds[] = $_POST['subsubcategory_id'];
            }
            $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIds, 0, 'profile_type');
        }

        //GET PRODUCT USING LEVEL OR PACKAGE BASED SETTINGS
        $packageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1);
        if (!empty($packageEnable)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $sitestore->package_id);
            if (empty($packageObj->store_settings)) {
                $product_types = array('simple', 'configurable', 'virtual', 'grouped', 'bundled', 'downloadable');
            } else {
                $storeSettings = @unserialize($packageObj->store_settings);
                $product_types = $storeSettings['product_type'];
            }
        } else {
            $user = $sitestore->getOwner();
            $product_types = Engine_Api::_()->authorization()->getPermission($user->level_id, "sitestore_store", "product_type");
            $product_types = Zend_Json_Decoder::decode($product_types);
        }

        if ($sitestoreproduct->product_type == 'virtual')
            $this->view->showProductInventory = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.productinventory', 1);
        else
            $this->view->showProductInventory = true;

        //MAKE FORM
        $this->view->productTypeName = $_POST['product_type'] = $sitestoreproduct->product_type;
        $this->view->form = $form = new Sitestoreproduct_Form_Edit(array('item' => $sitestoreproduct, 'defaultProfileId' => $defaultProfileId, 'pageId' => $sitestoreproduct->store_id, 'productType' => $sitestoreproduct->product_type, 'allowedProductTypes' => $product_types));

        $inDraft = 1;

        if (empty($sitestoreproduct->draft)) {
            $inDraft = 0;
            $form->removeElement('draft');
        }

        $temp_allow_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($sitestoreproduct->store_id, false);
        if (empty($temp_allow_selling)) {
            $form->removeElement('allow_purchase');
        }

        $intrestedViewerEmailCount = Engine_Api::_()->getDbtable('notifyemails', 'sitestoreproduct')->getNotifyEmail($product_id, 'COUNT(notifyemail_id)')->query()->fetchColumn();
        if (!empty($intrestedViewerEmailCount)) {
            $form->addElement('Checkbox', 'notify_viewer', array(
                'label' => $this->view->translate(array('%s viewer is', '%s viewers are', $intrestedViewerEmailCount), $this->view->locale()->toNumber($intrestedViewerEmailCount)) . ' ' . $this->view->translate('intrested in this product. Do you want to send an email about availability of this product?'),
                'value' => true,
                'order' => 102,
            ));
        }

        $form->removeElement('photo');
        $this->view->expiry_setting = $expiry_setting = Engine_Api::_()->sitestoreproduct()->expirySettings();

        // ASSIGNED THE OTHERINFO TABLE CONTENT VALUES TO FORM
        $this->view->otherInfoObj = $otherInfoObj = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->getOtherinfo($sitestoreproduct->product_id);
        $tempOtherInfos = array();
        $tempOtherInfos['out_of_stock'] = $otherInfoObj->out_of_stock;
        $tempOtherInfos['out_of_stock_action'] = $otherInfoObj->out_of_stock_action;
        $tempOtherInfos['discount'] = $otherInfoObj->discount;
        $tempOtherInfos['handling_type'] = $otherInfoObj->handling_type;
        $tempOtherInfos['discount_start_date'] = $otherInfoObj->discount_start_date;
        $tempOtherInfos['discount_end_date'] = $otherInfoObj->discount_end_date;
        $tempOtherInfos['discount_permanant'] = $otherInfoObj->discount_permanant;
        $tempOtherInfos['user_type'] = $otherInfoObj->user_type;
        $tempOtherInfos['special_vat'] = $otherInfoObj->special_vat;
        $this->view->store_id = $sitestoreproduct->store_id;

//    if( $sitestoreproduct->product_type == 'configurable' || $sitestoreproduct->product_type == 'virtual' )
//    {
//      $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($sitestoreproduct->product_id);
//      $field_meta_id = Engine_Api::_()->getDbTable('cartProductFieldMeta', 'sitestoreproduct')->isAnyConfigurationExist($option_id);
//      if( empty($field_meta_id) )
//        $form->search->setAttribs(array('disabled' => 'disabled'));
//    }
//    
//    if( $sitestoreproduct->product_type == 'downloadable' )
//    {
//      $isAnyMainFileExist = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->isAnyMainFileExist($sitestoreproduct->product_id);
//      if( empty($isAnyMainFileExist) )
//        $form->search->setAttribs(array('disabled' => 'disabled'));
//    }

        if ($sitestoreproduct->product_type == 'virtual' || $sitestoreproduct->product_type == 'bundled')
            $product_info = unserialize($otherInfoObj->product_info);

        $isSitestorereservationModuleExist = Engine_Api::_()->sitestoreproduct()->isSitestorereservationModuleExist();
        if ($sitestoreproduct->product_type == 'virtual' && !empty($isSitestorereservationModuleExist)) {
            $tempOtherInfos['virtual_product_price_range'] = isset($product_info['virtual_product_price_range']) ? $product_info['virtual_product_price_range'] : null;
            $isDateSelectorEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.dateselector', 0);
            if (!empty($isDateSelectorEnable))
                $tempOtherInfos['virtual_product_date_selector'] = isset($product_info['virtual_product_date_selector']) ? $product_info['virtual_product_date_selector'] : null;
        }

        // SHOW DOWNPAYMENT VALUE PRE-FILLED
        if ($sitestoreproduct->product_type != 'grouped' && !empty($isDownPaymentEnable) && !empty($directPayment)) {
            if (empty($otherInfoObj->downpayment_value))
                $tempOtherInfos['downpayment'] = 0;
            else {
                $tempOtherInfos['downpayment'] = 1;
                $tempOtherInfos['downpaymentvalue'] = $otherInfoObj->downpayment_value;
            }
        }

        if ($sitestoreproduct->product_type == 'bundled') {
            $tempOtherInfos['weight_type'] = $otherInfoObj->weight_type;
            $tempOtherInfos['enable_shipping'] = $product_info['enable_shipping'];
            $tempOtherInfos['bundle_product_type'] = $product_info['bundle_product_type'];
        }

        $isSuccessMsg = $this->_getParam('success', NULL);
        if (!empty($isSuccessMsg)) {
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }

        if ($sitestoreproduct->product_type == 'bundled' || $sitestoreproduct->product_type == 'grouped') {
            $isSuccessMsg = $this->_getParam('success', NULL);
            if (!empty($isSuccessMsg)) {
                $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
            }
            $tempMappedIds = $otherInfoObj->mapped_ids;
            if (!empty($tempMappedIds)) {
                $productMappedIds = $tempMappedIds = Zend_Json_Decoder::decode($tempMappedIds);
                $productsArray = $tempProductsArray = array();

                foreach ($tempMappedIds as $tempIdsKey => $product_id) {
                    $productTitle = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductTitle($product_id);
                    if (empty($productTitle)) {
                        unset($productMappedIds[$tempIdsKey]);
                        continue;
                    }
                    $tempProductsArray['title'] = $productTitle;
                    $tempProductsArray['id'] = $product_id;
                    $productsArray[] = $tempProductsArray;
                }

                $this->view->tempMappedIdsStr = @implode(",", $productMappedIds);
                $this->view->productArray = $productsArray;
            }
        }

        if (empty($otherInfoObj->handling_type))
            $tempOtherInfos['discount_price'] = @number_format($otherInfoObj->discount_value, 2, '.', ',');
        else
            $tempOtherInfos['discount_rate'] = @number_format($otherInfoObj->discount_value, 2, '.', ',');


        //SAVE SITESTOREPRODUCT ENTRY
        if (!$this->getRequest()->isPost()) {

            //prepare tags
            $sitestoreproductTags = $sitestoreproduct->tags()->getTagMaps();
            $tagString = '';

            foreach ($sitestoreproductTags as $tagmap) {

                if ($tagString != '')
                    $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();
            }

            $this->view->tagNamePrepared = $tagString;
            $form->tags->setValue($tagString);
            $tempValues = array_merge($sitestoreproduct->toArray(), $tempOtherInfos);

            if (array_key_exists("weight", $tempValues)) {
                $getPercisionValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.rate.precision', 2);
                $tempValues['weight'] = @number_format($tempValues['weight'], 2, '.', ','); //@round($tempValues['weight'], $getPercisionValue);
            }

            $form->populate($tempValues);

            if ($sitestoreproduct->end_date && $sitestoreproduct->end_date != '0000-00-00 00:00:00') {
                $form->end_date_enable->setValue(1);
                // Convert and re-populate times
                $end = strtotime($sitestoreproduct->end_date);
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $end = date('Y-m-d H:i:s', $end);
                date_default_timezone_set($oldTz);

                $form->populate(array(
                    'end_date' => $end,
                ));
            } else if (empty($sitestoreproduct->end_date) || $sitestoreproduct->end_date == '0000-00-00 00:00:00') {
                $date = (string) date('Y-m-d');
                $form->end_date->setValue($date . ' 00:00:00');
            }

            // Convert and re-populate times
            $end = strtotime($sitestoreproduct->start_date);
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($viewer->timezone);
            $end = date('Y-m-d H:i:s', $end);
            date_default_timezone_set($oldTz);

            $form->populate(array(
                'start_date' => $end,
            ));

            if ($sitestoreproduct->product_type != 'grouped' && (empty($otherInfoObj->discount_start_date) || $otherInfoObj->discount_start_date == '0000-00-00 00:00:00')) {
                $date = (string) date('Y-m-d');
                $form->discount_start_date->setValue($date . ' 00:00:00');
            }

            if ($sitestoreproduct->product_type != 'grouped' && (empty($otherInfoObj->discount_end_date) || $otherInfoObj->discount_end_date == '0000-00-00 00:00:00')) {
                $toDate = (string) date('Y-m-d', strtotime("+1 Month"));
                $form->discount_end_date->setValue($toDate . ' 00:00:00');
            }


            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            foreach ($roles as $role) {
                if ($form->auth_view) {
                    if (1 == $auth->isAllowed($sitestoreproduct, $role, "view")) {
                        $form->auth_view->setValue($role);
                    }
                }

                if ($form->auth_comment) {
                    if (1 == $auth->isAllowed($sitestoreproduct, $role, "comment")) {
                        $form->auth_comment->setValue($role);
                    }
                }
            }

            $roles_photo = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            foreach ($roles_photo as $role_photo) {
                if ($form->auth_photo) {
                    if (1 == $auth->isAllowed($sitestoreproduct, $role_photo, "photo")) {
                        $form->auth_photo->setValue($role_photo);
                    }
                }
            }

            $videoEnable = Engine_Api::_()->sitestoreproduct()->enableVideoPlugin();
            if ($videoEnable) {
                $roles_video = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                foreach ($roles_video as $role_video) {
                    if ($form->auth_video) {
                        if (1 == $auth->isAllowed($sitestoreproduct, $role_video, "video")) {
                            $form->auth_video->setValue($role_video);
                        }
                    }
                }
            }

            if (Engine_Api::_()->sitestoreproduct()->listBaseNetworkEnable()) {
                if (empty($sitestoreproduct->networks_privacy)) {
                    $form->networks_privacy->setValue(array(0));
                }
            }

            //POPULATE USER TAX       
            $form->populate(array('user_tax' => @unserialize($sitestoreproduct->user_tax)));

            return;
        }

        // IF POST NO ACCORDIAN SET
        $this->view->form_post = 1;

        //REMOVE VALIDATORS ACCORDING CONDITIONS
        if ($_POST['product_type'] != "grouped") {

            if (!empty($_POST['stock_unlimited'])) {
                $form->in_stock->setValidators(array());
            }

            if ($_POST['discount'] == 0) {
                $form->discount_rate->setValidators(array());
                $form->discount_price->setValidators(array());
                $form->discount_start_date->setValidators(array());
                $form->discount_end_date->setValidators(array());
            } else if ($_POST['handling_type'] == 0) {
                $form->discount_rate->setValidators(array());
            } else {
                $form->discount_price->setValidators(array());
            }


            if ($_POST['product_type'] == "bundled")
                if (!empty($_POST['weight_type']))
                    $form->weight->setValidators(array());
        }

        $getFormElements = $this->getRequest()->getPost();

        if (@array_key_exists('section_id', $getFormElements))
            unset($getFormElements['section_id']);

        if (@array_key_exists('inputsection_id', $getFormElements))
            unset($getFormElements['inputsection_id']);

        //FORM VALIDATION
        if (empty($isProductEnabled) || !$form->isValid($getFormElements)) {
            return;
        }

        //CATEGORY IS REQUIRED FIELD
        if (isset($_POST['category_id']) && empty($_POST['category_id'])) {
            $error = $this->view->translate('Please complete Category field - it is required.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        if ($_POST['product_type'] != "grouped" && !empty($_POST['discount']) && empty($_POST['discount_permanant']) && empty($_POST['discount_end_date']['date'])) {
            $error = $this->view->translate('Please enter Discount end Date - it is required.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        //VARIOUS LOGICAL CHECKS BITWEEN DIFFERENT PRODUCT ATTRIBUTES
        if ($_POST['product_type'] != "grouped" && (isset($_POST['stock_unlimited']) && empty($_POST['stock_unlimited'])) && !empty($_POST['min_order_quantity']) && $_POST['min_order_quantity'] > $_POST['in_stock']) {
            $error = $this->view->translate('Minimum Order Quantity can not be greater than In Stock value.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        if ($_POST['product_type'] != "grouped" && !empty($_POST['max_order_quantity']) && $_POST['max_order_quantity'] < $_POST['min_order_quantity']) {
            $error = $this->view->translate('Minimum Order Quantity can not be greater than Maximum Order Quantity.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        if ($_POST['product_type'] != "grouped" && $_POST['discount'] == 1 && $_POST['handling_type'] == 0 && $_POST['discount_price'] >= $_POST['price']) {
            $error = $this->view->translate('Discount can not be more than and equal to actual price.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        if (($_POST['product_type'] == "grouped" || $_POST['product_type'] == 'bundled') && (empty($_POST['product_ids']) || COUNT(explode(',', $_POST['product_ids'])) <= 1)) {
            $error = $this->view->translate('You have not configured any products for this product. Please select atleast two products for creating this products.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);

            if (!empty($_POST['product_ids'])) {
                $productsArray = $tempProductsArray = array();
                $this->view->tempMappedIdsStr = $_POST['product_ids'];
                $tempMappedIds = explode(',', $_POST['product_ids']);
                foreach ($tempMappedIds as $product_id) {
                    $productTitle = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductTitle($product_id);
                    if (empty($productTitle))
                        continue;
                    $tempProductsArray['title'] = $productTitle;
                    $tempProductsArray['id'] = $product_id;
                    $productsArray[] = $tempProductsArray;
                }
                $this->view->productArray = $productsArray;
            }
            return;
        }

        //SETTING VALUES IN ARRAY FOR SITESTOREPRODUCT_OTHERINFO TABLE
        $sitestoreproductValues = array();
        $sitestoreproduct_values = $form->getValues();

//    if(empty ($temp_allow_selling)){
//        $sitestoreproduct_values['allow_purchase'] = 0;
//    }

        $emailToViewers = (bool) isset($sitestoreproduct_values['notify_viewer']) ? $sitestoreproduct_values['out_of_stock_action'] : false;
        unset($sitestoreproduct_values['notify_viewer']);

        //GET FORM VALUES
        $values = $sitestoreproduct_values;
        if (isset($values['user_tax'])) {
            $values['user_tax'] = @serialize($values['user_tax']);
        }

        // SAVE VALUES IN OHERINFO TABLE.
        if (!empty($values['discount'])) {
            if (empty($values['handling_type'])) {
                $otherInfoObj->discount_amount = @round($values['discount_price'], 2);
                $otherInfoObj->discount_value = @round($values['discount_price'], 2);
                $otherInfoObj->discount_percentage = @round($values['discount_price'] * 100 / $values['price'], 2);
            } else {
                $otherInfoObj->discount_amount = @round(($values['discount_rate'] * $values['price'] / 100), 2);
                $otherInfoObj->discount_value = @round($values['discount_rate'], 2);
                $otherInfoObj->discount_percentage = @round($values['discount_rate'], 2);
            }
        }

        // SAVE DOWNPAYMENT VALUE
        if ($_POST['product_type'] != "grouped" && !empty($isDownPaymentEnable) && !empty($directPayment) && !empty($isSitestorereservationModuleExist)) {
            if (!empty($values['downpayment']))
                $otherInfoObj->downpayment_value = $values['downpaymentvalue'];
            else
                $otherInfoObj->downpayment_value = 0;
            unset($tempOtherInfos['downpayment']);
            unset($tempOtherInfos['downpaymentvalue']);
        }

        unset($tempOtherInfos['enable_shipping']);
        unset($tempOtherInfos['bundle_product_type']);

        if (!empty($isSitestorereservationModuleExist)) {
            unset($tempOtherInfos['virtual_product_price_range']);
            unset($tempOtherInfos['virtual_product_date_selector']);
        }

        // ADDING SPECIAL VAT IN OTHERINFO
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0) && isset($values['special_vat'])) {
            if (empty($values['special_vat'])) {
                $values['special_vat'] = null;
                $otherInfoObj->special_vat = null;
            }
        }

        foreach ($tempOtherInfos as $key => $value) {
            if ($key == 'discount_price' || $key == 'discount_rate')
                continue;
            else {
                if (isset($values[$key]))
                    $otherInfoObj->$key = $values[$key];
            }
            unset($values[$key]);
        }

        if ($values['product_type'] == 'grouped' || $values['product_type'] == 'bundled') {
            $mappedIdsArray = @explode(',', $values['product_ids']);
            $otherInfoObj->mapped_ids = Zend_Json_Encoder::encode($mappedIdsArray);

            if ($values['product_type'] == 'bundled') {
                $otherInfoObj->product_info = @serialize(array('enable_shipping' => $values['enable_shipping'], 'bundle_product_type' => $values['bundle_product_type']));
            }
        }

        if ($values['product_type'] == 'virtual' && !empty($isSitestorereservationModuleExist)) {
            $tempVirtualProductInfo = array();
            if (!empty($sitestoreproduct_values['virtual_product_price_range']))
                $tempVirtualProductInfo['virtual_product_price_range'] = $values['virtual_product_price_range'];

            $isDateSelectorEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.dateselector', 0);
            if (!empty($isDateSelectorEnable))
                $tempVirtualProductInfo['virtual_product_date_selector'] = $values['virtual_product_date_selector'];

            if (!empty($tempVirtualProductInfo)) {
                if (!empty($otherInfoObj->product_info)) {
                    $tempProductInfo = unserialize($otherInfoObj->product_info);
                    $otherInfoObj->product_info = serialize(array_merge($tempProductInfo, $tempVirtualProductInfo));
                } else {
                    $otherInfoObj->product_info = serialize($tempVirtualProductInfo);
                }
            }
        }

        $otherInfoObj->save();

        $tags = preg_split('/[,]+/', $values['tags']);
        $tags = array_filter(array_map("trim", $tags));

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            if (Engine_Api::_()->sitestoreproduct()->listBaseNetworkEnable() && isset($values['networks_privacy']) && !empty($values['networks_privacy']) && in_array(0, $values['networks_privacy'])) {
                $values['networks_privacy'] = new Zend_Db_Expr('NULL');
                $form->networks_privacy->setValue(array(0));
            }
            if ($expiry_setting == 1 && $values['end_date_enable'] == 1) {
                // Convert times
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $end = strtotime($values['end_date']);
                date_default_timezone_set($oldTz);
                $values['end_date'] = date('Y-m-d H:i:s', $end);
            } elseif ($expiry_setting == 1 && isset($values['end_date'])) {
                $values['end_date'] = NULL;
            } elseif (isset($values['end_date'])) {
                unset($values['end_date']);
            }

            // Convert times
            if (isset($values['start_date']) && !empty($values['start_date'])) {
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $end = strtotime($values['start_date']);
                date_default_timezone_set($oldTz);
                $values['start_date'] = date('Y-m-d H:i:s', $end);
            }

            // EMPTY KEYS ARE COMING FROM THE FIELD TYPE. FOR REMOVING THEME FOLLOW THE FOLLOWING STEPS
            $tempFormValuesArray = array();
            foreach ($values as $key => $value) {
                if (!empty($key))
                    $tempFormValuesArray[$key] = $value;
            }

            $values = $tempFormValuesArray;

            if (empty($values['max_order_quantity'])) {
                $values['max_order_quantity'] = NULL;
            }

            if ($_POST['product_type'] == 'grouped')
                $values['stock_unlimited'] = 1;

            $values['weight'] = @number_format($values['weight'], 2, '.', ',');
            $values['price'] = @number_format($values['price'], 2, '.', '');

            $sitestoreproduct->setFromArray($values);
            $sitestoreproduct->modified_date = date('Y-m-d H:i:s');
            $sitestoreproduct->tags()->setTagMaps($viewer, $tags);


            if (!empty($_POST['section_id']) && !empty($_POST['inputsection_id']) && ($_POST['section_id'] == 'new')) {
                $tableSections = Engine_Api::_()->getDbTable('sections', 'sitestoreproduct');
                $tableSectionsName = $tableSections->info('name');

                $row_info = $tableSections->fetchRow($tableSections->select()->from($tableSectionsName, 'max(sec_order) AS sec_order'));
                $sec_order = $row_info['sec_order'] + 1;
                $row = $tableSections->createRow();
                $row->section_name = $_POST['inputsection_id'];
                $row->sec_order = $sec_order;
                $row->store_id = $sitestoreproduct->store_id;
                $newsec_id = $row->save();
                $sitestoreproduct->section_id = $newsec_id;
            }

            if (@array_key_exists("section_id", $_POST) && ($_POST['section_id'] != 'new'))
                $sitestoreproduct->section_id = $_POST['section_id'];



            $sitestoreproduct->save();

            //SAVE CUSTOM FIELDS
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($sitestoreproduct);
            $customfieldform->saveValues();
            if ($customfieldform->getElement('submit_addtocart')) {
                $customfieldform->removeElement('submit_addtocart');
            }

            // SHOW A MESSAGE IF CATEGORY IS CHANGED AND COMBINATIONS EXIST (CONFIGURABLE / VIRTUAL PRODUCTS)
//      if (($_POST['product_type'] == 'configurable' || $_POST['product_type'] == 'virtual')) {
//        $allowCombinations = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1);
//        $combinations = Engine_Api::_()->sitestoreproduct()->getCombinations($product_id);
//        if (!empty($allowCombinations) && (count($combinations) != 0)) {
//          $profileTypes = array();
//          if (!empty($oldCategoryIds[0]))
//            $profileTypes['category_id'] = Engine_Api::_()->getItem('sitestoreproduct_category', $oldCategoryIds[0])->profile_type;
//          if (!empty($oldCategoryIds[1]))
//            $profileTypes['subcategory_id'] = Engine_Api::_()->getItem('sitestoreproduct_category', $oldCategoryIds[1])->profile_type;
//          if (!empty($oldCategoryIds[2]))
//            $profileTypes['subsubcategory_id'] = Engine_Api::_()->getItem('sitestoreproduct_category', $oldCategoryIds[2])->profile_type;
//
//          if ($values['category_id'] != $oldCategoryIds[0])
//            $deleteCombinationMessage = 1;
//          elseif (empty($profileTypes['category_id']) && $values['subcategory_id'] != $oldCategoryIds[1])
//            $deleteCombinationMessage = 1;
//          elseif (empty($profileTypes['category_id']) && empty($profileTypes['subcategory_id']) && $values['subsubcategory_id'] != $oldCategoryIds[2])
//            $deleteCombinationMessage = 1;
//        }
//      }

            if (isset($values['category_id']) && !empty($values['category_id'])) {
                $categoryIds = array();
                $categoryIds[] = $sitestoreproduct->category_id;
                $categoryIds[] = $sitestoreproduct->subcategory_id;
                $categoryIds[] = $sitestoreproduct->subsubcategory_id;
                $sitestoreproduct->profile_type = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIds, 0, 'profile_type');
                if ($sitestoreproduct->profile_type != $previous_profile_type) {

                    $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'values');
                    $fieldvalueTable->delete(array('item_id = ?' => $sitestoreproduct->product_id));

                    Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'search')->delete(array(
                        'item_id = ?' => $sitestoreproduct->product_id,
                    ));

                    if (!empty($sitestoreproduct->profile_type) && !empty($previous_profile_type)) {
                        //PUT NEW PROFILE TYPE
                        $fieldvalueTable->insert(array(
                            'item_id' => $sitestoreproduct->product_id,
                            'field_id' => $defaultProfileId,
                            'index' => 0,
                            'value' => $sitestoreproduct->profile_type,
                        ));
                    }
                }
                $sitestoreproduct->save();
            }

            //NOT SEARCHABLE IF SAVED IN DRAFT MODE
            if (!empty($sitestoreproduct->draft)) {
                $sitestoreproduct->search = 0;
                $sitestoreproduct->save();
            }

            if ($sitestoreproduct->draft == 0 && $sitestoreproduct->search && $inDraft) {
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                $activityFeedType = null;
                if (Engine_Api::_()->sitestore()->isStoreOwner($sitestore) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
                    $activityFeedType = 'sitestoreproduct_admin_new';
                elseif ($sitestore->all_post || Engine_Api::_()->sitestore()->isStoreOwner($sitestore))
                    $activityFeedType = 'sitestoreproduct_new';

                if ($activityFeedType) {
                    $action = $actionTable->addActivity($sitestoreproduct->getOwner(), $sitestore, $activityFeedType);
                    Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
                }
                //MAKE SURE ACTION EXISTS BEFOR ATTACHING THE NOTE TO THE ACTIVITY
                if ($action != null) {
                    $actionTable->attachActivity($action, $sitestoreproduct, Activity_Model_Action::ATTACH_MULTI);
                }
            }

            //CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            if (empty($values['auth_view'])) {
                $values['auth_view'] = array("everyone");
            }

            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = array("everyone");
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($sitestoreproduct, $role, "view", ($i <= $viewMax));
                $auth->setAllowed($sitestoreproduct, $role, "comment", ($i <= $commentMax));
            }

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            if ($values['auth_photo'])
                $auth_photo = $values['auth_photo'];
            else
                $auth_photo = "owner";
            $photoMax = array_search($auth_photo, $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($sitestoreproduct, $role, "photo", ($i <= $photoMax));
            }

            $roles_video = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            if (!isset($values['auth_video']) && empty($values['auth_video'])) {
                $values['auth_video'] = "owner";
            }

            $videoMax = array_search($values['auth_video'], $roles_video);
            foreach ($roles_video as $i => $role_video) {
                $auth->setAllowed($sitestoreproduct, $role_video, "video", ($i <= $videoMax));
            }

            if ($previous_category_id != $sitestoreproduct->category_id) {
                Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct')->editProductCategory($sitestoreproduct->product_id, $previous_category_id, $sitestoreproduct->category_id, $sitestoreproduct->getType());
            }

            if (!empty($emailToViewers) && !empty($intrestedViewerEmailCount)) {
                $intrestedViewerEmailSelect = Engine_Api::_()->getDbtable('notifyemails', 'sitestoreproduct')->getNotifyEmail($product_id, 'buyer_email');
                $intrestedViewerEmail = Engine_Api::_()->getDbtable('notifyemails', 'sitestoreproduct')->fetchAll($intrestedViewerEmailSelect);

                foreach ($intrestedViewerEmail as $email) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($email['buyer_email'], 'sitestoreproduct_notify_to_viewer', array(
                        'object_title' => $this->view->htmlLink($sitestoreproduct->getHref(), $sitestoreproduct->getTitle())
                    ));
                }
            }

            $db->commit();
//      if(!empty($deleteCombinationMessage))
//        $this->view->form = $form->addNotice(sprintf(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully. Categories of this product has been changed, so you should delete all the combinations on %1sclick here%2s which were mapped with the old categories.'), "<a href = " . $this->view->url(array('controller' => 'siteform', 'action' => 'product-category-attributes', 'product_id' => $product_id, 'delete_old_combinations' => 1), 'sitestoreproduct_extended', true) . " >","</a>"));
//        else  
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($sitestoreproduct) as $action) {
                $actionTable->resetActivityBindings($action);
            }

            $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

            $getPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdmin($sitestore->store_id, $viewer->getIdentity());

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

//    if ($_POST['product_type'] == 'grouped' || $_POST['product_type'] == 'bundled') {
        $this->_helper->redirector->gotoRoute(array('route' => 'default', 'success' => true), false);
//    }
    }

    //ACTION TO SET OVERVIEW
    public function overviewAction() {

        //ONLY LOGGED IN USER CAN ADD OVERVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET PRODUCT ID AND OBJECT
        $product_id = $this->_getParam('product_id');
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

        if (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.overview', 1))) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$sitestoreproduct->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "overview")) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $defaultLanguage = $settings->getSetting('core.locale.locale', 'en');

        //MULTI LANGUAGE IS ALLOWED OR NOT
        $this->view->multiLanguage = $settings->getSetting('sitestoreproduct.multilanguage', 0);

        //DEFAULT LANGUAGE
        $this->view->defaultLanguage = $defaultLanguage = $settings->getSetting('core.locale.locale', 'en');

        //MULTI LANGUAGE WORK
        $this->view->languageCount = 0;
        $this->view->languageData = array();

        $overview_link = $this->view->add_show_hide_overview_link = 'overview';
        if ($this->view->multiLanguage) {
            //GET LANGUAGE ARRAY
            $localeMultiOptions = Engine_Api::_()->sitestoreproduct()->getLanguageArray();
            $languages = $settings->getSetting('sitestoreproduct.languages');
            $this->view->languageCount = Count($languages);
            $this->view->languageData = array();
            if (is_array($languages)) {
                foreach ($languages as $label) {
                    $this->view->languageData[] = $label;
                    if ($this->view->languageCount >= 2 && $defaultLanguage == $label && $label != 'en') {
                        $overview_link = $this->view->add_show_hide_overview_link = "overview_$label";
                    }
                }
            }
        }

        //SELECTED TAB
        $this->view->sitestores_view_menu = 2;

        //MAKE FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Overview();

        //IF NOT POSTED
        if (!$this->getRequest()->isPost()) {
            $saved = $this->_getParam('saved');
            if (!empty($saved))
                $this->view->success = Zend_Registry::get('Zend_Translate')->_('Your product has been successfully created. You can enhance your product from this Dashboard by creating other components.');
        }

        $product_id = $sitestoreproduct->getIdentity();

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct');

        // SETTINGS FOR MULTILANGUAL WORK
        $temp_languages = $settings->getSetting('sitestoreproduct.languages');
        $languageCount = Count($temp_languages);

        //SAVE THE VALUE
        if ($this->getRequest()->isPost()) {
            $tableOtherinfo->update(array('overview' => $_POST['overview']), array('product_id = ?' => $product_id));

            // MULTI LANGUAL WORK
            foreach ($temp_languages as $language_label) {
                if ($languageCount >= 2 && $language_label != 'en') {
                    $overview = "overview_" . $language_label;
                    $tableOtherinfo->update(array($overview => $_POST[$overview]), array('product_id = ?' => $product_id));
                }
            }

            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }

        //POPULATE FORM
        $values['overview'] = $tableOtherinfo->getColumnValue($product_id, 'overview');

        // MULTI LANGUAL WORK
        if (is_array($temp_languages)) {
            foreach ($temp_languages as $language_label) {
                if ($languageCount >= 2 && $language_label != 'en') {
                    $overview = "overview_" . $language_label;
                    $values[$overview] = $tableOtherinfo->getColumnValue($product_id, $overview);
                }
            }
        }

        $form->populate($values);
    }

    //ACTION FOR EDIT STYLE OF SITESTOREPRODUCT
    public function editstyleAction() {

        //ONLY LOGGED IN USER CAN EDIT THE STYLE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->sitestores_view_menu = 11;

        //GET PRODUCT ID AND OBJECT
        $product_id = $this->_getParam('product_id');
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
        if (!$sitestoreproduct->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('sitestoreproduct_product', $viewer->level_id, "style")) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->itestores_view_menu = 11;

        //MAKE FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Style();

        //FETCH EXISTING ROWS
        $tableStyle = Engine_Api::_()->getDbtable('styles', 'core');
        $select = $tableStyle->select()
                ->where('type = ?', 'sitestoreproduct_product')
                ->where('id = ?', $product_id)
                ->limit();
        $row = $tableStyle->fetchRow($select);

        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            $form->populate(array('style' => ( null == $row ? '' : $row->style )));
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $style = $form->getValue('style');
        $style = strip_tags($style);

        $forbiddenStuff = array(
            '-moz-binding',
            'expression',
            'javascript:',
            'behaviour:',
            'vbscript:',
            'mocha:',
            'livescript:',
        );
        $style = str_replace($forbiddenStuff, '', $style);

        //SAVE ROW
        if (null == $row) {
            $row = $tableStyle->createRow();
            $row->type = 'sitestoreproduct_product';
            $row->id = $product_id;
        }
        $row->style = $style;
        $row->save();
        $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
    }

    // ACTION FOR DELETE PRODUCT
    public function deleteAction() {
        // LOGGED IN USER CAN DELETE PRODUCT
        if (!$this->_helper->requireUser()->isValid())
            return;

        // GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");

        // GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        // GET PRODUCT ID AND OBJECT
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $this->_getParam('product_id'));

        if (empty($sitestoreproduct)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $store_id = $sitestoreproduct->store_id;

        // AUTHORIZATION CHECK
        $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
        if (empty($authValue) || ($authValue == 1) || !$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, $viewer, "delete")->isValid()) {
            return;
        }

        // DELETE SITESTOREPRODUCT AFTER CONFIRMATION
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
            $tabId = Engine_Api::_()->sitestoreproduct()->getProductTabId();
            $store_url = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($store_id, 'store_url');
            $sitestoreproduct->delete();
            return $this->_helper->redirector->gotoRoute(array('action' => 'store', 'store_id' => $store_id, 'type' => 'product', 'menuId' => 62, 'method' => 'manage'), 'sitestore_store_dashboard', false);
        }
    }

    // ACTION FOR CLOSE / OPEN PRODUCT
    public function closeAction() {

        // LOGGED IN USER CAN CLOSE PRODUCT
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PRODUCT
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $this->_getParam('product_id'));

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //AUTHORIZATION CHECK
        $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($sitestoreproduct->store_id);
        if (empty($authValue) || ($authValue == 1) || !$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, $viewer, "edit")->isValid()) {
            return;
        }

        //BEGIN TRANSCATION
        $db = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getAdapter();
        $db->beginTransaction();

        try {
            $sitestoreproduct->closed = empty($sitestoreproduct->closed) ? 1 : 0;
            $sitestoreproduct->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //RETURN TO MANAGE STORE
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), "sitestoreproduct_general", true);
    }

    //ACTION FOR CONSTRUCT TAG CLOUD
    public function tagscloudAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestoreproduct_main');

        //CONSTRUCTING TAG CLOUD
        $tag_array = array();
        $tag_cloud_array = Engine_Api::_()->sitestoreproduct()->getTags(0, 1000, 0);
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

    //ACTION FOR TELL A FRIEND ABOUT PRODUCT
    public function tellafriendAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewr_id = $viewer->getIdentity();

        //GET FORM
        $this->view->form = $form = new Sitestoreproduct_Form_TellAFriend();

        if (!empty($viewr_id)) {
            $value['sender_email'] = $viewer->email;
            $value['sender_name'] = $viewer->displayname;
            $form->populate($value);
        }

        //FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET PRODUCT ID AND OBJECT
            $product_id = $this->_getParam('product_id', $this->_getParam('id', null));
            $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

            //GET FORM VALUES
            $values = $form->getValues();

            //EXPLODE EMAIL IDS
            $reciver_ids = explode(',', $values['reciver_emails']);
            if (!empty($values['send_me'])) {
                $reciver_ids[] = $values['sender_email'];
            }
            $sender_email = $values['sender_email'];
            $heading = $sitestoreproduct->getTitle();

            //CHECK VALID EMAIL ID FORMAT
            $validator = new Zend_Validate_EmailAddress();
            $validator->getHostnameValidator()->setValidateTld(false);

            if (!$validator->isValid($sender_email)) {
                $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
                return;
            }

            $tempEmailAddress = array();
            foreach ($reciver_ids as $reciver_id) {
                $reciver_id = trim($reciver_id, ' ');
                if (!$validator->isValid($reciver_id)) {
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
                    return;
                }
                $tempEmailAddress[] = $reciver_id;
            }

            $sender = $values['sender_name'];
            $message = $values['message'];

            if (!empty($tempEmailAddress)) {
                foreach ($tempEmailAddress as $email_ids) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($email_ids, 'SITESTOREPRODUCT_TELLAFRIEND_EMAIL', array(
                        'host' => $_SERVER['HTTP_HOST'],
                        'sender' => $sender,
                        'heading' => $heading,
                        'message' => '<div>' . $message . '</div>',
                        'object_link' => $sitestoreproduct->getHref(),
                        'email' => $sender_email,
                        'queue' => false
                    ));
                }
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefreshTime' => '15',
                'format' => 'smoothbox',
                'messages' => Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.')
            ));
        }
    }

    //ACTION FOR ASK OPINION ABOUT PRODUCT
    public function askOpinionAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('default-simple');

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewr_id = $viewer->getIdentity();

        //GET FORM
        $this->view->form = $form = new Sitestoreproduct_Form_AskForAnOpinion();

        if (!empty($viewr_id)) {
            $value['sender_email'] = $viewer->email;
            $value['sender_name'] = $viewer->displayname;
            $form->populate($value);
        }

        //FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET PRODUCT ID AND OBJECT
            $product_id = $this->_getParam('product_id', $this->_getParam('id', null));
            $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

            //GET FORM VALUES
            $values = $form->getValues();

            //EXPLODE EMAIL IDS
            $reciver_ids = explode(',', $values['reciver_emails']);
            if (!empty($values['send_me'])) {
                $reciver_ids[] = $values['sender_email'];
            }
            $sender_email = $values['sender_email'];

            //CHECK VALID EMAIL ID FORMAT
            $validator = new Zend_Validate_EmailAddress();

            if (!$validator->isValid($sender_email)) {
                $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
                return;
            }

            $tempEmailAddress = array();
            foreach ($reciver_ids as $reciver_id) {
                $reciver_id = trim($reciver_id, ' ');
                if (!$validator->isValid($reciver_id)) {
                    $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
                    return;
                }
                $tempEmailAddress[] = $reciver_id;
            }

            $sender = $values['sender_name'];
            $message = $values['message'];
            if (!empty($tempEmailAddress)) {
                foreach ($tempEmailAddress as $email_id) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($email_id, 'SITESTOREPRODUCT_ASKOPINION_EMAIL', array(
                        'host' => $_SERVER['HTTP_HOST'],
                        'sender' => $sender,
                        'message' => '<div>' . $message . '</div>',
                        'object_link' => $sitestoreproduct->getHref(),
                        'email' => $sender_email,
                        'queue' => false
                    ));
                }
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefreshTime' => '15',
                'format' => 'smoothbox',
                'messages' => Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.')
            ));
        }
    }

    public function getPaymentType($object, $itemType) {
        $length = 7;
        $encodeorder = 0;
        $obj_length = strlen($object);
        if ($length > $obj_length)
            $length = $obj_length;
        for ($i = 0; $i < $length; $i++) {
            $encodeorder += ord($object[$i]);
        }
        $req_mode = $encodeorder % strlen($itemType);
        $encodeorder +=ord($itemType[$req_mode]);
        $isEnabled = Engine_Api::_()->sitestore()->isEnabled();
        if (empty($isEnabled)) {
            return 0;
        } else {
            return $encodeorder;
        }
    }

    //ACTION FOR PRINTING THE SITESTOREPRODUCT
    public function printAction() {

        //LAYOUT DEFAULT
        $this->_helper->layout->setLayout('default-simple');

        //GET PRODUCT ID AND OBJECT
        $product_id = $this->_getParam('product_id', $this->_getParam('id', null));
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        //IF PRODUCT IS NOT EXIST
        if (empty($sitestoreproduct)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        if ($sitestoreproduct->category_id != 0) {
            $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct');
            $this->view->category_name = $categoryTable->getCategory($sitestoreproduct->category_id)->category_name;

            if ($sitestoreproduct->subcategory_id != 0) {
                $this->view->subcategory_name = $categoryTable->getCategory($sitestoreproduct->subcategory_id)->category_name;

                if ($sitestoreproduct->subsubcategory_id != 0) {
                    $this->view->subsubcategory_name = $categoryTable->getCategory($sitestoreproduct->subsubcategory_id)->category_name;
                }
            }
        }

        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitestoreproduct);
    }

    //ACTION TO GET SUB-CATEGORY
    public function subCategoryAction() {

        //GET CATEGORY ID
        $category_id_temp = $this->_getParam('category_id_temp');

        //INTIALIZE ARRAY
        $this->view->subcats = $data = array();

        //RETURN IF CATEGORY ID IS EMPTY
        if (empty($category_id_temp))
            return;

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');

        //GET CATEGORY
        $category = $tableCategory->getCategory($category_id_temp);
        if (!empty($category->category_name)) {
            $categoryName = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id_temp)->getCategorySlug();
        }

        //GET SUB-CATEGORY
        $subCategories = $tableCategory->getSubCategories($category_id_temp);

        foreach ($subCategories as $subCategory) {
            $content_array = array();
            $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($subCategory->category_name);
            $content_array['category_id'] = $subCategory->category_id;
            $content_array['categoryname_temp'] = $categoryName;
            $data[] = $content_array;
        }

        $this->view->subcats = $data;
    }

    //ACTION FOR FETCHING SUB-CATEGORY
    public function subsubCategoryAction() {

        //GET SUB-CATEGORY ID
        $subcategory_id_temp = $this->_getParam('subcategory_id_temp');

        //INTIALIZE ARRAY
        $this->view->subsubcats = $data = array();

        //RETURN IF SUB-CATEGORY ID IS EMPTY
        if (empty($subcategory_id_temp))
            return;

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');

        //GET SUB-CATEGORY
        $subCategory = $tableCategory->getCategory($subcategory_id_temp);
        if (!empty($subCategory->category_name)) {
            $subCategoryName = Engine_Api::_()->getItem('sitestoreproduct_category', $subcategory_id_temp)->getCategorySlug();
        }

        //GET 3RD LEVEL CATEGORIES
        $subCategories = $tableCategory->getSubCategories($subcategory_id_temp);
        foreach ($subCategories as $subCategory) {
            $content_array = array();
            $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($subCategory->category_name);
            $content_array['category_id'] = $subCategory->category_id;
            $content_array['categoryname_temp'] = $subCategoryName;
            $data[] = $content_array;
        }
        $this->view->subsubcats = $data;
    }

    //ACTION FOR LIKES THE PRODUCT
    public function likesitestoreproductAction() {

        //GET SETTINGS
        $like_user_str = 0;
        $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
        $this->view->resource_id = $resource_id = $this->_getParam('resource_id');
        $this->view->call_status = $call_status = $this->_getParam('call_status');
        $this->view->page = $page = $this->_getParam('page', 1);
        $search = $this->_getParam('search', '');
        $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', 0);
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $this->view->search = $search;
        if (empty($search)) {
            $this->view->search = $this->view->translate('Search Members');
        }

        if ($call_status == 'friend') {

            //GET CORE LIKE TABLE
            $sub_status_table = Engine_Api::_()->getItemTable('core_like');
            $sub_status_name = $sub_status_table->info('name');

            //GET MEMBERSHIP TABLE
            $membership_table = Engine_Api::_()->getDbtable('membership', 'user');
            $member_name = $membership_table->info('name');

            //GET USER TABLE
            $user_table = Engine_Api::_()->getItemTable('user');
            $user_Name = $user_table->info('name');

            //MAKE QUERY
            $sub_status_select = $user_table->select()
                    ->setIntegrityCheck(false)
                    ->from($sub_status_name, array('poster_id'))
                    ->joinInner($member_name, "$member_name . user_id = $sub_status_name . poster_id", NULL)
                    ->joinInner($user_Name, "$user_Name . user_id = $member_name . user_id")
                    ->where($member_name . '.resource_id = ?', $viewer_id)
                    ->where($member_name . '.active = ?', 1)
                    ->where($sub_status_name . '.resource_type = ?', $resource_type)
                    ->where($sub_status_name . '.resource_id = ?', $resource_id)
                    ->where($sub_status_name . '.poster_id != ?', $viewer_id)
                    ->where($sub_status_name . '.poster_id != ?', 0)
                    ->where($user_Name . '.displayname LIKE ?', '%' . $search . '%')
                    ->order('	like_id DESC');
        } else if ($call_status == 'public') {

            //GET CORE LIKE TABLE
            $sub_status_table = Engine_Api::_()->getItemTable('core_like');
            $sub_status_name = $sub_status_table->info('name');

            //GET USER TABLE
            $user_table = Engine_Api::_()->getItemTable('user');
            $user_Name = $user_table->info('name');

            //MAKE QUERY
            $sub_status_select = $user_table->select()
                    ->setIntegrityCheck(false)
                    ->from($sub_status_name, array('poster_id'))
                    ->joinInner($user_Name, "$user_Name . user_id = $sub_status_name . poster_id")
                    ->where($sub_status_name . '.resource_type = ?', $resource_type)
                    ->where($sub_status_name . '.resource_id = ?', $resource_id)
                    ->where($sub_status_name . '.poster_id != ?', 0)
                    ->where($user_Name . '.displayname LIKE ?', '%' . $search . '%')
                    ->order($sub_status_name . '.like_id DESC');
        }

        $fetch_sub = Zend_Paginator::factory($sub_status_select);
        $fetch_sub->setCurrentPageNumber($page);
        $fetch_sub->setItemCountPerPage(10);
        $check_object_result = $fetch_sub->getTotalItemCount();

        $this->view->user_obj = array();
        if (!empty($check_object_result)) {
            $this->view->user_obj = $fetch_sub;
        } else {
            $this->view->no_result_msg = $this->view->translate('No results were found.');
        }

        //TOTAL LIKE FOR THIS CONTENT
        $this->view->public_count = Engine_Api::_()->sitestoreproduct()->number_of_like('sitestoreproduct_product', $resource_id);

        //NUMBER OF FRIENDS LIKE THIS CONTENT
        $this->view->friend_count = Engine_Api::_()->sitestoreproduct()->friend_number_of_like($resource_type, $resource_id);

        //GET LIKE TITLE
        if ($resource_type == 'member') {
            $this->view->like_title = Engine_Api::_()->getItem('user', $resource_id)->displayname;
        } else {
            $this->view->like_title = Engine_Api::_()->getItem($resource_type, $resource_id)->title;
        }
    }

    //ACTION FOR GLOBALLY LIKE THE PRODUCT
    public function globallikesAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET THE VALUE OF RESOURCE ID AND TYPE 
        $resource_id = $this->_getParam('resource_id');
        $resource_type = $this->_getParam('resource_type');
        $like_id = $this->_getParam('like_id');
        $status = $this->_getParam('smoothbox', 1);
        $this->view->status = true;

        //GET LIKE TABLE
        $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
        $like_name = $likeTable->info('name');

        //GET OBJECT
        $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
        if (empty($like_id)) {

            //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
            $like_id_temp = Engine_Api::_()->sitestoreproduct()->check_availability($resource_type, $resource_id);
            if (empty($like_id_temp)) {

                if (!empty($resource)) {
                    $like_id = $likeTable->addLike($resource, $viewer);
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
                        Engine_Api::_()->sitelike()->setLikeFeed($viewer, $resource);
                }

                $notify_table = Engine_Api::_()->getDbtable('notifications', 'activity');
                $db = $likeTable->getAdapter();
                $db->beginTransaction();
                try {

                    //CREATE THE NEW ROW IN TABLE
                    if ($resource->owner_id != $viewer_id) {
                        $notifyData = $notify_table->createRow();
                        $notifyData->user_id = $resource->owner_id;
                        $notifyData->subject_type = $viewer->getType();
                        $notifyData->subject_id = $viewer->getIdentity();
                        $notifyData->object_type = $resource_type;
                        $notifyData->object_id = $resource_id;
                        $notifyData->type = 'liked';
                        $notifyData->params = $resource->getShortType();
                        $notifyData->date = date('Y-m-d h:i:s', time());
                        $notifyData->save();
                    }
                    $this->view->like_id = $like_id;
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                $like_msg = $this->view->translate('Successfully Liked.');
            }
        } else {
            if (!empty($resource)) {
                $likeTable->removeLike($resource, $viewer);
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
                    Engine_Api::_()->sitelike()->removeLikeFeed($viewer, $resource);
            }
            $like_msg = $this->view->translate('Successfully Unliked.');
        }

        if (empty($status)) {
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array($like_msg))
            );
        }
    }

    //ACTION FOR PUBLISH PRODUCT
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

        //CHECK POST
        if (!$this->getRequest()->isPost())
            return;

        //GET PRODUCT ID AND OBJECT
        $product_id = $this->view->product_id = $this->_getParam('product_id');
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, $viewer, "edit")->isValid()) {
            return;
        }

        $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer_id, $sitestoreproduct->store_id);

        //ONLY OWNER CAN PUBLISH THE PRODUCT
        if (($viewer_id == $sitestoreproduct->owner_id) || !empty($isStoreAdmins) || $viewer->level_id == 1) {
            $this->view->permission = true;
            $this->view->success = false;
            $db = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getAdapter();
            $db->beginTransaction();
            try {

                if (!empty($_POST['search'])) {
                    $sitestoreproduct->search = 1;
                } else {
                    $sitestoreproduct->search = 0;
                }

                $sitestoreproduct->modified_date = new Zend_Db_Expr('NOW()');
                $sitestoreproduct->draft = 0;
                $sitestoreproduct->save();
                $db->commit();
                $this->view->success = true;
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {
            $this->view->permission = false;
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array('Successfully Published !')
        ));
    }

    //ACTION FOR GET THE PRODUCTS BASED ON SEARCHING
    public function getSearchProductsAction() {

        //GET PRODUCTS AND MAKE ARRAY
        $usersitestoreproducts = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getDayItems($this->_getParam('text'), $this->_getParam('limit', 10));
        $data = array();
        $mode = $this->_getParam('struct');
        $count = count($usersitestoreproducts);
        if ($mode == 'text') {
            $i = 0;
            foreach ($usersitestoreproducts as $usersitestoreproduct) {
                $sitestoreproduct_url = $this->view->url(array('product_id' => $usersitestoreproduct->product_id, 'slug' => $usersitestoreproduct->getSlug()), "sitestoreproduct_entry_view", true);
                $i++;
                $content_photo = $this->view->itemPhoto($usersitestoreproduct, 'thumb.icon');
                $data[] = array(
                    'id' => $usersitestoreproduct->product_id,
                    'label' => $usersitestoreproduct->getTitle(),
                    'photo' => $content_photo,
                    'sitestoreproduct_url' => $sitestoreproduct_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        } else {
            $i = 0;
            foreach ($usersitestoreproducts as $usersitestoreproduct) {
                $sitestoreproduct_url = $this->view->url(array('product_id' => $usersitestoreproduct->product_id, 'slug' => $usersitestoreproduct->getSlug()), "sitestoreproduct_entry_view", true);
                $content_photo = $this->view->itemPhoto($usersitestoreproduct, 'thumb.icon');
                $i++;
                $data[] = array(
                    'id' => $usersitestoreproduct->product_id,
                    'label' => $usersitestoreproduct->getTitle(),
                    'photo' => $content_photo,
                    'sitestoreproduct_url' => $sitestoreproduct_url,
                    'total_count' => $count,
                    'count' => $i
                );
            }
        }
        if (!empty($data) && $i >= 1) {
            if ($data[--$i]['count'] == $count) {
                $data[$count]['id'] = 'stopevent';
                $data[$count]['label'] = $this->_getParam('text');
                $data[$count]['sitestoreproduct_url'] = 'seeMoreLink';
                $data[$count]['total_count'] = $count;
            }
        }
        return $this->_helper->json($data);
    }

    //ACTION FOR MESSAGING THE PRODUCT OWNER
    public function messageownerAction() {

        //LOGGED IN USER CAN SEND THE MESSAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET PRODUCT ID AND OBJECT
        $product_id = $this->_getParam("product_id");
        $product = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        //OWNER CANT SEND A MESSAGE TO HIMSELF
        if ($viewer_id == $product->owner_id) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //MAKE FORM
        $this->view->form = $form = new Messages_Form_Compose();
        $form->setDescription('Create your message with the form given below. (This message will be sent to the owner of this Product.)');
        $form->removeElement('to');
        $form->toValues->setValue("$product->owner_id");

        //CHECK METHOD/DATA
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();

        try {
            $values = $this->getRequest()->getPost();

            $is_error = 0;
            if (empty($values['title'])) {
                $is_error = 1;
            }

            //SENDING MESSAGE
            if ($is_error == 1) {
                $error = $this->view->translate('Subject is required field !');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $recipients = preg_split('/[,. ]+/', $values['toValues']);

            //LIMIT RECIPIENTS IF IT IS NOT A SPECIAL SITESTOREPRODUCT OF MEMBERS
            $recipients = array_slice($recipients, 0, 1000);

            //CLEAN THE RECIPIENTS FOR REPEATING IDS
            $recipients = array_unique($recipients);

            $user = Engine_Api::_()->getItem('user', $product->owner_id);

            $product_title = $product->getTitle();
            $http = _ENGINE_SSL ? 'https://' : 'http://';
            $product_title_with_link = '<a href =' . $http . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('product_id' => $product_id, 'slug' => $product->getSlug()), "sitestoreproduct_entry_view") . ">$product_title</a>";

            $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send($viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->view->translate('This message corresponds to the Product: ') . $product_title_with_link);

            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $conversation, 'message_new');

            //INCREMENT MESSAGE COUNTER
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

            $db->commit();

            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR EDITING THE NOTE
    public function displayAction() {

        //GET TEXT AND PRODUCT ID
        $text = $this->_getParam('strr');
        $subjectType = $this->_getParam('subjectType');
        $subjectId = $this->_getParam('subjectId');

        if ($subjectType == 'sitestoreproduct_product') {
            Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->update(array('about' => $text), array('product_id = ?' => $subjectId));
        } else {
            Engine_Api::_()->getDbTable('editors', 'sitestoreproduct')->update(array('about' => $text), array('user_id = ?' => $subjectId));
        }

        exit();
    }

    //ACTION FOR UPLOADING IMAGES THROUGH WYSIWYG EDITOR
    public function uploadPhotoAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->_helper->layout->disableLayout();

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return false;
        }

        if (!Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create')) {
            return false;
        }

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid())
            return;

        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        $fileName = Engine_Api::_()->seaocore()->tinymceEditorPhotoUploadedFileName();
        if (!isset($_FILES[$fileName]) || !is_uploaded_file($_FILES[$fileName]['tmp_name'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }

        $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();

            $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
            ));
            $photo->save();

            $photo->setPhoto($_FILES[$fileName]);

            $this->view->status = true;
            $this->view->name = $_FILES[$fileName]['name'];
            $this->view->photo_id = $photo->photo_id;
            $this->view->photo_url = $photo->getPhotoUrl();

            $table = Engine_Api::_()->getDbtable('albums', 'album');
            $album = $table->getSpecialAlbum($viewer, 'message');

            $photo->album_id = $album->album_id;
            $photo->save();

            if (!$album->photo_id) {
                $album->photo_id = $photo->getIdentity();
                $album->save();
            }

            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($photo, 'everyone', 'view', true);
            $auth->setAllowed($photo, 'everyone', 'comment', true);
            $auth->setAllowed($album, 'everyone', 'view', true);
            $auth->setAllowed($album, 'everyone', 'comment', true);

            $db->commit();
        } catch (Album_Model_Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = $this->view->translate($e->getMessage());
            throw $e;
            return;
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
            throw $e;
            return;
        }
    }

    public function accountAction() {
        // ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        // WHO CAN CREATE STORE
        $menusClass = new Sitestore_Plugin_Menus();
        $this->view->canCreatePages = $menusClass->canCreateSitestores();
        $this->view->canCreate = 1;
        if (!$this->view->canCreatePages || (!Engine_Api::_()->sitestore()->hasPackageEnable() && !Engine_Api::_()->sitestoreproduct()->getLevelSettings("allow_store_create"))) {
            $this->view->canCreate = 0;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        // CHECK IF ANY PACKAGE AVAILABLE OR NOT FOR THIS MEMBER
        if (!empty($viewer_id) && Engine_Api::_()->sitestore()->hasPackageEnable()) {
            $packages_select = Engine_Api::_()->getItemtable('sitestore_package')->getPackagesSql(null);
            $paginator = Zend_Paginator::factory($packages_select);
            $getPaginatorCount = $paginator->getTotalItemCount();
            if (empty($getPaginatorCount))
                $this->view->canCreate = 0;
        }

        // CHECK TO SEE IF REQUEST IS FOR SPECIFIC USER'S STORES
        $values = array();
        $values['user'] = $viewer;
        $values['type'] = 'manage';
        $this->view->totalStores = Engine_Api::_()->sitestore()->getSitestoresPaginator($values, null)->getTotalItemCount();

        // START CODE TO CHECK IF USER IS ADMINS FOR SOME OTHER STORES
        $this->view->countUserStores = Engine_Api::_()->getDbtable('stores', 'sitestore')->countUserStores($viewer_id, 1);
        $this->view->getCountUserAsAdmin = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getCountUserAsAdmin(array());
        $this->view->getLikeCounts = Engine_Api::_()->getDbtable('stores', 'sitestore')->getLikeCounts(array());
        // END CODE TO CHECK IF USER IS ADMINS FOR SOME OTHER STORES

        $this->view->isAnyDownloadableProduct = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->isAnyDownloadableProduct($viewer_id);

        $this->view->canViewWishlist = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_wishlist', 'view');

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");

        $this->view->showMenu = $this->_getParam('menuType', 'my-address');
        $this->view->showSubMenu = $this->_getParam('subMenuType', null);
        $this->view->orderId = $this->_getParam('orderId', 0);
    }

    public function manageOrderAction() {


        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $mobileId = $this->_getParam('store_id', null);
            $mobileSubject = Engine_Api::_()->getItem('sitestore_store', $mobileId);
            if (!empty($mobileSubject) && !Engine_Api::_()->core()->hasSubject()) {
                Engine_Api::_()->core()->setSubject($mobileSubject);
            }

            $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
            $coreversion = $coremodule->version;
            if ($coreversion < '4.1.0') {
                $this->_helper->content->render();
            } else {
                $this->_helper->content
                        //->setNoRender()
                        ->setEnabled();
            }
        } else {
            $this->_helper->layout->disableLayout();
        }


        //ONLY LOGGED IN USER CAN MANAGE
        if (!$this->_helper->requireUser()->isValid())
            return;

        $isPaymentToSiteEnable = true;
        $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
        if (empty($isAdminDrivenStore)) {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
        }
        $this->view->isPaymentToSiteEnable = $isPaymentToSiteEnable;
        $this->view->isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

        // STORE ID 
        $this->view->store_id = $store_id = $this->_getParam('store_id', null);

        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
        if (empty($isManageAdmin)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $this->view->canEdit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');
        //SEND TAB TO TPL FILE
        $this->view->tab_selected_id = $this->_getParam('tab');
        $this->view->call_same_action = $this->_getParam('call_same_action', 0);

        $params = array();
        $params['store_id'] = $store_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 8;

        $isSearch = $this->_getParam('search', null);

        if (!empty($isSearch)) {
            $params['search'] = 1;
            $this->view->newOrderStatus = $params['order_status'] = $this->_getParam('status');
        }

        if (isset($_POST['search'])) {
            $params['search'] = 1;
            $params['order_id'] = $_POST['order_id'];
            $params['username'] = $_POST['username'];
            $params['billing_name'] = $_POST['billing_name'];
            $params['shipping_name'] = $_POST['shipping_name'];
            $params['creation_date_start'] = $_POST['creation_date_start'];
            $params['creation_date_end'] = $_POST['creation_date_end'];
            $params['order_min_amount'] = $_POST['order_min_amount'];
            $params['order_max_amount'] = $_POST['order_max_amount'];
            $params['commission_min_amount'] = $_POST['commission_min_amount'];
            $params['commission_max_amount'] = $_POST['commission_max_amount'];
            $params['delivery_time'] = $_POST['delivery_time'];
            $params['order_status'] = $_POST['order_status'];
            $params['downpayment'] = $_POST['downpayment'];
        }

        //MAKE PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getOrdersPaginator($params);
        $this->view->total_item = $this->view->paginator->getTotalItemCount();
    }

    public function paymentApproveAction() {

        $this->view->order_id = $order_id = $this->_getParam('order_id', null);
        $this->view->order_obj = $order_obj = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);
        $this->view->gateway_id = $gateway_id = $this->_getParam("gateway_id", null);

        if (empty($order_id) || empty($order_obj) || empty($gateway_id) || ( empty($order_obj->cheque_id) && $gateway_id == 3 ) || ($gateway_id == 1)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        if ($gateway_id == 3) {
            $cheque_detail = Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->getChequeDetail($order_obj->cheque_id);
            $this->view->form = $form = new Sitestoreproduct_Form_PaymentApprove($cheque_detail);
        }

        if ($this->getRequest()->isPost()) {
            if ($gateway_id == 3) {
                $form->populate($cheque_detail);
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                if ($gateway_id == 3) {
                    $gateway_transaction_id = $_POST['transaction_no'];
                    $type = 'cheque';
                } else if ($gateway_id == 4) {
                    $gateway_transaction_id = 0;
                    $type = 'cod';
                } else {
                    $gateway_transaction_id = 0;
                    $type = 'payment';
                }

                $transactionData = array(
                    'user_id' => $order_obj->buyer_id,
                    'gateway_id' => $order_obj->gateway_id,
                    'date' => new Zend_Db_Expr('NOW()'),
                    'payment_order_id' => 0,
                    'parent_order_id' => $order_obj->parent_id,
                    'gateway_transaction_id' => $gateway_transaction_id,
                    'type' => $type,
                    'state' => 'okay',
                    'amount' => @round($order_obj->grand_total, 2),
                    'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'),
                    'cheque_id' => $order_obj->cheque_id
                );
                Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct')->insert($transactionData);

                if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                    $transactionParams = array_merge($transactionData, array('resource_type' => 'sitestoreproduct_order'));
                    Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
                }

                // UPDATE PAYMENT STATUS
                Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->update(array("payment_status" => "active"), array("parent_id =?" => $order_obj->parent_id));

                // UPDATE ORDER STATUS
                $orderProductTable = Engine_Api::_()->getDbtable('OrderProducts', 'sitestoreproduct');
                $bundleProductShipping = $orderProductTable->checkBundleProductShipping(array('order_id' => $order_id));
                $anyOtherProducts = $orderProductTable->checkProductType(array('order_id' => $order_id, 'all_downloadable_products' => true));

                if (empty($anyOtherProducts) || !empty($bundleProductShipping)) {
                    Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->update(array('order_status' => 5), array('order_id = ?' => $order_id));
                } else {
                    Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->update(array('order_status' => 2), array('order_id = ?' => $order_id));
                }

                Engine_Api::_()->sitestoreproduct()->orderPlaceMailAndNotification(array('0' => array('order_id' => $order_id)), true);

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefreshTime' => '100',
                'parentRedirect' => $this->view->url(array('action' => 'store', 'store_id' => $order_obj->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'manage-order'), 'sitestore_store_dashboard', true),
                'format' => 'smoothbox',
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Payment approved successfully.'))
            ));
        }
    }

    public function approveRemainingAmountPaymentAction() {

        $this->view->order_id = $order_id = $this->_getParam('order_id', null);
        $this->view->order_obj = $order_obj = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);

        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

        if (empty($directPayment) || empty($isDownPaymentEnable) || empty($order_id) || empty($order_obj) || $order_obj->is_downpayment != 1) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $remainingAmountGatewayId = Engine_Api::_()->getDbtable('orderdownpayments', 'sitestoreproduct')->isRemainingAmountRequestExist($order_id);

        if (!empty($remainingAmountGatewayId) && $remainingAmountGatewayId == 3) {
            $this->view->gateway_id = 3;
            $cheque_detail = Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->getChequeDetail($order_obj->cheque_id);
            $this->view->form = $form = new Sitestoreproduct_Form_PaymentApprove($cheque_detail);
        }

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                if (empty($remainingAmountGatewayId)) {
                    Engine_Api::_()->getDbtable('orderdownpayments', 'sitestoreproduct')->insert(array(
                        'order_id' => $order_id,
                        'gateway_id' => 4,
                        'payment' => round(($order_obj->sub_total - $order_obj->downpayment_total), 2),
                        'payment_status' => 'active',
                        'date' => new Zend_Db_Expr('NOW()'),
                    ));
                } else {
                    Engine_Api::_()->getDbtable('orderdownpayments', 'sitestoreproduct')->update(
                            array('payment_status' => 'active'), array('order_id =?' => $order_id)
                    );
                }
                $order_obj->is_downpayment = 2;
                $order_obj->save();
                $remainingAmountObj = Engine_Api::_()->getDbtable('orderdownpayments', 'sitestoreproduct')->fetchRow(array('order_id = ?' => $order_id));

                if (!empty($remainingAmountObj)) {
                    if ($remainingAmountObj->gateway_id == 3) {
                        $gateway_transaction_id = $_POST['transaction_no'];
                        $type = 'cheque';
                    } else {
                        $gateway_transaction_id = 0;
                        $type = 'cod';
                    }

                    $transactionData = array(
                        'user_id' => $order_obj->buyer_id,
                        'gateway_id' => $remainingAmountObj->gateway_id,
                        'date' => new Zend_Db_Expr('NOW()'),
                        'payment_order_id' => 0,
                        'parent_order_id' => $remainingAmountObj->orderdownpayment_id,
                        'gateway_transaction_id' => $gateway_transaction_id,
                        'type' => $type,
                        'state' => 'okay',
                        'amount' => @round($remainingAmountObj->payment, 2),
                        'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'),
                        'cheque_id' => $remainingAmountObj->cheque_id,
                        'is_remaining_amount_payment' => 1
                    );

                    Engine_Api::_()->getDbtable('transactions', 'sitestoreproduct')->insert();

                    if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                        $transactionParams = array_merge($transactionData, array('resource_type' => 'sitestoreproduct_order'));
                        Engine_Api::_()->sitegateway()->insertTransactions($transactionParams);
                    }
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefreshTime' => '100',
                'parentRedirect' => $this->view->url(array('action' => 'store', 'store_id' => $order_obj->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'manage-order'), 'sitestore_store_dashboard', true),
                'format' => 'smoothbox',
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Remaining amount payment approved successfully.'))
            ));
        }
    }

    public function orderViewAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->order_id = $order_id = $this->_getParam('order_id', null);
        $this->view->page_user = $page_user = $this->_getParam('page_viewer', null);
        $this->view->orderObj = $orderObj = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);
        $this->view->directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $this->view->isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

        if (empty($order_id) || empty($orderObj)) {
            $this->view->sitestoreproduct_view_no_permission = true;
            return;
        }


        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $mobileId = $this->_getParam('store_id', null);
            $mobileSubject = Engine_Api::_()->getItem('sitestore_store', $mobileId);
            if (!empty($mobileSubject) && !Engine_Api::_()->core()->hasSubject()) {
                Engine_Api::_()->core()->setSubject($mobileSubject);
            }
        }


        // IF VIEWER IS STORE OWNER OR STORE ADMIN
        if (empty($page_user)) {
            $this->view->show_tax_type = true;
            $store_id = $this->_getParam('store_id', null);
            if (empty($store_id)) {
                $store_id = $orderObj->store_id;
            }

            if ($store_id != $orderObj->store_id) {
                $this->view->sitestoreproduct_view_no_permission = true;
                return;
            }

            $this->view->store_id = $store_id;

            $this->view->isStoreExist = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($store_id, 'store_id');

            $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

            if ($viewer->level_id == 1) {
                $this->view->site_admin = true;
                $this->view->admin_calling = true;
            } else {
                $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

                //IS USER IS STORE ADMIN OR NOT
                if (empty($authValue) || $authValue == 1) {
                    $this->view->sitestoreproduct_view_no_permission = true;
                    return;
                }
            }

            $store_owner_id = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($store_id, 'owner_id');
            $seller_ids = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdminIds($store_id);

            $params = array();
            if ($viewer->level_id == 1 && $viewer_id == $store_owner_id) {
                $this->view->is_siteadmin_owner = $is_siteadmin_owner = true;
                $params['is_siteadmin_owner'] = $is_siteadmin_owner;
            }

            if ($viewer_id == $store_owner_id) {
                $params['page_owner'] = true;
                $this->view->page_owner = true;
            } else if (in_array($viewer_id, $seller_ids)) {
                $params['page_admin'] = true;
                $this->view->page_admin = true;
            }
        } else {
            if ($viewer_id != $orderObj->buyer_id && $viewer->level_id != 1) {
                $this->view->sitestoreproduct_view_no_permission = true;
                return;
            }

            //IF USER VIEW ORDER THEN HE HAS PERMISSION TO VIEW SELLER COMMENT OR NOT
            $params['buyer'] = true;
            $this->view->isStoreExist = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($orderObj->store_id, 'store_id');
        }

        if ($orderObj->is_downpayment == 2) {
            $getRemainingAmountPaymentDetail = Engine_Api::_()->getDbtable('orderdownpayments', 'sitestoreproduct')->getRemainingAmountPaymentDetail(array('order_id' => $order_id));
            if (!empty($getRemainingAmountPaymentDetail)) {
                $this->view->remainingAmountPayment = true;
                $this->view->remainingAmountGatewayId = $getRemainingAmountPaymentDetail->gateway_id;
                if ($getRemainingAmountPaymentDetail->gateway_id == 3) {
                    $this->view->remaining_amount_cheque_info = Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->getChequeDetail($getRemainingAmountPaymentDetail->cheque_id);
                }
            }
        }

        $this->view->storeTitle = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($orderObj->store_id, 'title');

        if ($orderObj->gateway_id == 3)
            $this->view->cheque_info = Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->getChequeDetail($orderObj->cheque_id);

        $order_comment_table = Engine_Api::_()->getDbtable('OrderComments', 'sitestoreproduct');
        $this->view->callingStatus = $this->_getParam('menuId', 0);
        $this->view->orderProducts = Engine_Api::_()->getDbtable('OrderProducts', 'sitestoreproduct')->getOrderProductsDetail($order_id);
        $this->view->buyerComments = $order_comment_table->getBuyerComments($order_id, $orderObj->buyer_id);
        $this->view->sellerComments = $order_comment_table->getSellerComments($order_id, $params);
        $this->view->siteAdminComments = $order_comment_table->getSiteAdminComments($order_id, $params);
        $this->view->billing_address = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getAddress($order_id);
        $this->view->shipping_address = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getAddress($order_id, true, array('address_type' => 1));
        $this->view->billing_region_name = Engine_Api::_()->getItem('sitestoreproduct_region', $this->view->billing_address->state)->region;
        if (!empty($this->view->shipping_address))
            $this->view->shipping_region_name = Engine_Api::_()->getItem('sitestoreproduct_region', $this->view->shipping_address->state)->region;
        $this->view->anyOtherProductTypes = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->checkProductType(array('order_id' => $order_id, 'virtual' => true));
        $this->view->bundleProductShipping = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->checkBundleProductShipping(array('order_id' => $order_id));
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestore/View/Helper', 'Sitestore_View_Helper');

        if (!empty($sitestore))
            $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitestore);

        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled();
        }
    }

    public function orderShipAction() {

        // ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->order_id = $order_id = $this->_getParam('order_id', null);

        // USER IS BUYER OR NOT
        $this->view->orderObj = $orderObj = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);
        $this->view->store_id = $store_id = $orderObj->store_id;

        if ($viewer_id != $orderObj->buyer_id) {
            $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

            //IS USER IS STORE ADMIN OR NOT
            if (empty($authValue) || $authValue == 1) {
                $this->view->sitestoreproduct_order_ship_no_permission = true;
                return;
            }
        }

        $anyOtherProducts = Engine_Api::_()->getDbtable('OrderProducts', 'sitestoreproduct')->checkProductType(array('order_id' => $order_id, 'virtual' => true));
        if (empty($anyOtherProducts)) {
            $this->view->sitestoreproduct_order_ship_no_permission = true;
            return;
        }

        $this->view->page_user = $this->_getParam('page_viewer');
        $this->view->callingStatus = $this->_getParam('menuId', 0);
        $this->view->isStoreExist = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($store_id, 'store_id');
        $this->view->shipTrackObj = Engine_Api::_()->getDbtable('shippingtrackings', 'sitestoreproduct')->getShipTracks($order_id);
    }

    public function addShipmentAction() {
        // ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $store_id = $this->_getParam('store_id', null);
        $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

        //IS USER IS STORE ADMIN OR NOT
        if (empty($authValue))
            return $this->_forward('requireauth', 'error', 'core');
        else if ($authValue == 1)
            return $this->_forward('notfound', 'error', 'core');

        $this->view->form = $form = new Sitestoreproduct_Form_Addshipment();
        $form->removeElement('status');

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        $values['order_id'] = $order_id = $this->_getParam('order_id');

        $table = Engine_Api::_()->getDbtable('shippingtrackings', 'sitestoreproduct');
        $row = $table->createRow();
        $row->setFromArray($values);
        $row->save();

        $order_obj = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $order_obj->store_id);
        $newVar = _ENGINE_SSL ? 'https://' : 'http://';
        $store_name = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $sitestore->getHref() . '">' . $sitestore->getTitle() . '</a>';

        // SEND MAIL TO BUYER ABOUT SHIPMENT
        if (empty($order_obj->buyer_id)) {
            $order_no = '#' . $order_id;
            $billing_email_id = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getBillingEmailId($order_obj->order_id);
        } else {
            $billing_email_id = $user = Engine_Api::_()->getItem('user', $order_obj->buyer_id);
            $order_no = $this->view->htmlLink($this->view->url(array('action' => 'account', 'menuType' => 'my-orders', 'subMenuType' => 'order-view', 'orderId' => $order_id), 'sitestoreproduct_general', true), '#' . $order_id);
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $sitestore, $order_obj, 'sitestoreproduct_order_ship', array('order_no' => $order_no));
            $order_no = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'account', 'menuType' => 'my-orders', 'subMenuType' => 'order-view', 'orderId' => $order_id), 'sitestoreproduct_general', true) . '">#' . $order_id . '</a>';
        }

        Engine_Api::_()->getApi('mail', 'core')->sendSystem($billing_email_id, 'sitestoreproduct_member_order_ship', array(
            'order_id' => '#' . $order_obj->order_id,
            'order_no' => $order_no,
            'object_title' => $sitestore->getTitle(),
            'object_name' => $store_name,
            'tracking_num' => $values['tracking_num'],
            'service' => $values['service']
        ));

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Shipping details added successfully.'))
        ));
    }

    public function editShipmentAction() {
        // ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();

        $store_id = $this->_getParam('store_id', null);
        $shipping_tracking_id = $this->_getParam('shippingtracking_id');
        if (empty($shipping_tracking_id) || empty($store_id)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

        //IS USER IS STORE ADMIN OR NOT
        if (empty($authValue))
            return $this->_forward('requireauth', 'error', 'core');
        else if ($authValue == 1)
            return $this->_forward('notfound', 'error', 'core');

        $this->view->form = $form = new Sitestoreproduct_Form_Addshipment();

        $shipping_tracking_obj = Engine_Api::_()->getDbtable('shippingtrackings', 'sitestoreproduct');
        $shipping_track = $shipping_tracking_obj->fetchRow(array('shippingtracking_id =?' => $shipping_tracking_id));
        $shipping_track = $shipping_track->toArray();

        $form->populate($shipping_track);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        $shipping_tracking_obj->update(array(
            'service' => $values['service'],
            'title' => $values['title'],
            'tracking_num' => $values['tracking_num'],
            'status' => $values['status'],
            'note' => $values['note']), array('shippingtracking_id =?' => $shipping_tracking_id));


        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Shipping details edited successfully.'))
        ));
    }

    public function getPaymentAuth($strKey) {
        $str = explode("-", $strKey);
        $str = $str[2];
        $char_array = array();
        for ($i = 0; $i < 6; $i++)
            $char_array[] = $str[$i];
        $key = array();
        foreach ($char_array as $value) {
            $v_a = ord($value);
            if ($v_a > 47 && $v_a < 58)
                continue;
            $possition = 0;
            $possition = $v_a % 10;
            if ($possition > 5)
                $possition -=4;
            $key[] = $char_array[$possition];
        }
        $isEnabled = Engine_Api::_()->sitestore()->isEnabled();
        if (empty($isEnabled)) {
            return 0;
        } else {
            return $getStr = implode($key);
        }
    }

    public function deleteShipmentAction() {
        // ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $store_id = $this->_getParam('store_id', null);
        $shipping_tracking_id = $this->_getParam('shippingtracking_id');
        if (empty($shipping_tracking_id) || empty($store_id)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

        //IS USER IS STORE ADMIN OR NOT
        if (empty($authValue))
            return $this->_forward('requireauth', 'error', 'core');
        else if ($authValue == 1)
            return $this->_forward('notfound', 'error', 'core');

        if (!$this->getRequest()->isPost()) {
            return;
        }

        Engine_Api::_()->getDbtable('shippingtrackings', 'sitestoreproduct')->update(array('is_deleted' => 1), array('shippingtracking_id =?' => $shipping_tracking_id));

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Shipping details deleted successfully.'))
        ));
    }

    public function detailShipmentAction() {
        // ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $store_id = $this->_getParam('store_id', null);
        $store_id = base64_decode($store_id);

        if (empty($store_id))
            return $this->_forward('requireauth', 'error', 'core');

//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
//
//    //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

        $shipping_tracking_id = $this->_getParam('shippingtracking_id');
        $shipping_tracking_id = base64_decode($shipping_tracking_id);
        $this->view->shipping_tracking_obj = $shipping_tracking_obj = Engine_Api::_()->getItem('sitestoreproduct_shippingtracking', $shipping_tracking_id);
        if (empty($shipping_tracking_id) || empty($shipping_tracking_obj)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
    }

    public function manageAddressAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $isajax = $this->_getParam('isajax', NULL);
        $this->view->methodType = $this->_getParam('method', NULL);

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $regionObj = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->isAnyCountryEnable();

        if (empty($regionObj)) {
            $this->view->noCountryEnable = 1;
            return;
        }

        if (array_key_exists(0, $regionObj)) {
            $noCountry = 1;
        }

        $this->view->form = $form = new Sitestoreproduct_Form_Addresses(array('viewerId' => 1, 'showShipping' => 1));

        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled();
        }

        if (!empty($noCountry)) {
            $params['owner_id'] = $viewer_id;

            $addressObj = Engine_Api::_()->getDbtable('addresses', 'sitestoreproduct');
            $addressResult = $addressObj->getAddress($params);

            //FLAGS FOR BILLING/SHIPPING ADDRESS EXIST : 1 OR NOT : 0
            $shippingAddId = $billingAddId = 0;
            $this->view->is_populate = 0;
            $this->view->flag_same_address = true;

            foreach ($addressResult as $address) {
                //IF BILLING ADDRESS (TYPE = 0) ALREADY EXIST IN TABLE THEN POPULATE
                if (empty($address->type)) {
                    $addressTempArray = $address->toArray();
                    $addressArray['f_name_billing'] = $addressTempArray['f_name'];
                    $addressArray['l_name_billing'] = $addressTempArray['l_name'];
                    $addressArray['phone_billing'] = $addressTempArray['phone'];
                    $addressArray['country_billing'] = $addressTempArray['country'];
                    $addressArray['city_billing'] = $addressTempArray['city'];
                    $addressArray['locality_billing'] = $addressTempArray['locality'];
                    $addressArray['zip_billing'] = $addressTempArray['zip'];
                    $addressArray['address_billing'] = $addressTempArray['address'];

                    $this->view->billingRegionId = $addressTempArray['state'];
                    $billingAddId = $address->address_id;
                    $this->view->flag_same_address = $addressTempArray['common'];

                    //GETTING BILLING COUNTRY REGIONS
                    $params = array();
                    $params['country'] = $addressTempArray['country'];
                    $shippingCountries = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getRegionsByName($params);

                    $countryArray = array();
                    foreach ($shippingCountries as $shippingCountry) {
                        $countryArray[$shippingCountry['region_id']] = $shippingCountry['region'];
                    }

                    $getBillingCredentioal = @serialize(array("region" => $countryArray, "value" => $addressTempArray['state']));
                    $this->_setParam("getBillingCredentioal", $getBillingCredentioal);
                } else {
                    $addressTempArray = $address->toArray();
                    $addressArray['f_name_shipping'] = $addressTempArray['f_name'];
                    $addressArray['l_name_shipping'] = $addressTempArray['l_name'];
                    $addressArray['phone_shipping'] = $addressTempArray['phone'];
                    $addressArray['country_shipping'] = $addressTempArray['country'];
                    $addressArray['city_shipping'] = $addressTempArray['city'];
                    $addressArray['locality_shipping'] = $addressTempArray['locality'];
                    $addressArray['zip_shipping'] = $addressTempArray['zip'];
                    $addressArray['address_shipping'] = $addressTempArray['address'];
                    $shippingAddId = $address->address_id;
                    $this->view->shippingRegionId = $addressTempArray['state'];

                    //GETTING SHIPPING COUNTRY REGIONS
                    $params = array();
                    $params['country'] = $addressTempArray['country'];
                    $shippingCountries = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getRegionsByName($params);

                    $countryArray = array();
                    $tempShippingRegionFlag = false;
                    foreach ($shippingCountries as $shippingCountry) {
                        if (empty($shippingCountry['region']))
                            $tempShippingRegionFlag = true;
                        $countryArray[$shippingCountry['region_id']] = $shippingCountry['region'];
                    }
                    $this->view->tempShippingRegionFlag = $tempShippingRegionFlag;
                    $getShippingingCredentioal = @serialize(array("region" => $countryArray, "value" => $addressTempArray['state']));
                    $this->_setParam("getShippingingCredentioal", $getShippingingCredentioal);
                }
            }

            if (!empty($shippingAddId)) {
                $this->view->is_populate = 1;
                $form->populate($addressArray);
            }

            //BILLING AND SHIPPING ADDRESS ID
            $this->view->billingAddId = $billingAddId;
            $this->view->shippingAddId = $shippingAddId;
            return;
        }
    }

    public function successAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");
        $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $downpayment_make_payment = null;
        if ($this->_getParam('success_id')) {
            $parent_id = (int) Engine_Api::_()->sitestoreproduct()->getEncodeToDecode($this->_getParam('success_id'));
            $downpayment_make_payment = $this->_getParam('downpayment_make_payment', null);
            $state = $error = '';
        } else {
            $session = new Zend_Session_Namespace('Sitestoreproduct_Order_Payment_Detail');

            if (empty($session->sitestoreproductOrderPaymentDetail['success_id']))
                return $this->_forward('notfound', 'error', 'core');

            $parent_id = $session->sitestoreproductOrderPaymentDetail['success_id'];
            $this->view->state = $state = $session->sitestoreproductOrderPaymentDetail['state'];
            $this->view->error = $error = $session->sitestoreproductOrderPaymentDetail['errorMessage'];
            if (!empty($session->sitestoreproductOrderPaymentDetail['downpayment_make_payment']))
                $downpayment_make_payment = $session->sitestoreproductOrderPaymentDetail['downpayment_make_payment'];
        }

        $this->view->order_id = $parent_id;
        $this->view->downpayment_make_payment = $downpayment_make_payment;

        if (empty($downpayment_make_payment)) {
            $parent_order_obj = Engine_Api::_()->getItem('sitestoreproduct_order', $parent_id);

            if (empty($parent_id) || empty($parent_order_obj)) {
                return $this->_forward('notfound', 'error', 'core');
            }
           
            $order_table = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
            $productsTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
            $formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
            $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');
            $orderProductTable = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct');
  
            $order_ids = $order_table->getOrderIds($parent_id);

            $tempOrderCount = 1;
            $is_any_downloadable_product = false;
            $my_downloadable_link = '';
            $tempCount = @COUNT($order_ids);

            $success_message = '<b>' . $this->view->translate("Thanks for your purchase!") . '</b><br/><br/>';
            $success_message .= $this->view->translate("Your Order ID is") . ' ';
            $viewer_orders = '';
            if (($parent_order_obj->payment_status == 'active' || $parent_order_obj->gateway_id == 3 || $parent_order_obj->gateway_id == 4) && empty($error)) {
               $isSitecreditEnabled = Engine_Api::_()->hasModuleBootstrap('sitecredit');
                    if($isSitecreditEnabled)
                    {
                        $select=$order_table->select()->where('parent_id =?',$parent_id);
                        $orders=$order_table->fetchAll($select);
                        $credit_count=0;
                        foreach ($orders as $value) {
                            if($value->credit_point)
                            $credit_count+=$value->credit_point;
                        }               
                        if($credit_count){
                            $param['type_id']=$parent_id;
                            $param['credit_point']=-$credit_count;
                            $param['type']='store';
                            $param['user_id']=$viewer_id;
                            $param['reason']='used credits in store';
                            $credit_table = Engine_Api::_()->getDbtable('credits','sitecredit');    
                            $credit_table->insertCredit($param);
                        }
                    }
                }
            foreach ($order_ids as $order_id) {
                // IF PAYMENT IS SUCCESSFULLY DONE FOR THE ORDER
                if (($parent_order_obj->payment_status == 'active' || $parent_order_obj->gateway_id == 3 || $parent_order_obj->gateway_id == 4) && empty($error)) {
                    // ENTRY IN CREDIT TABLE FOR DEDUCTION OF CREDITS
                   
                    // CHANGE IN STOCK VALUE FOR ALL PRODUCTS OF ALL ORDERS
                    $orderProducts = $orderProductTable->getOrderProducts($order_id['order_id']);
                    foreach ($orderProducts as $products) {
                        if (empty($products->stock_unlimited)) {
                            $placed_order_ids = isset($_COOKIE['placed_order_id']) ? explode("_", $_COOKIE['placed_order_id']) : array();
                            if (!in_array($order_id['order_id'], $placed_order_ids)) {
                                $productsTable->update(array('in_stock' => new Zend_Db_Expr("in_stock - $products->quantity")), array('product_id = ?' => $products->product_id));
                                $placed_order_ids[] = $order_id['order_id'];
                                $getTempStr = implode("_", $placed_order_ids);
                                setcookie('placed_order_id', $getTempStr, time() + (3600), "/");
                            }
                        }
                        if ($products->product_type == 'virtual' || $products->product_type == 'configurable') {
                            $configuration_info = @unserialize($products->config_info);

                            if (!empty($configuration_info)) {
                                foreach ($configuration_info as $key => $config_info) {
                                    if (isset($config_info['qty_unlimited']) && (empty($config_info['qty_unlimited'])))
                                        $formOptionTable->update(array('quantity' => new Zend_Db_Expr("quantity - $products->quantity")), array('option_id = ?' => $key));

                                    if (isset($config_info['combination_quantity']))
                                        $combinationsTable->update(array('quantity' => new Zend_Db_Expr("quantity - $products->quantity")), array('combination_id = ?' => $key));
                                }
                            }
                        }
                    }

                    // CHANGE EACH ORDER STATUS
                    if ($parent_order_obj->gateway_id != 3 && $parent_order_obj->gateway_id != 4) {
                        $anyOtherProducts = $orderProductTable->checkProductType(array('order_id' => $order_id['order_id'], 'all_downloadable_products' => true));
                        $bundleProductShipping = $orderProductTable->checkBundleProductShipping(array('order_id' => $order_id['order_id']));
                        if (empty($anyOtherProducts) || !empty($bundleProductShipping))
                            $order_table->update(array('order_status' => 5), array('order_id = ?' => $order_id['order_id']));
                        else
                            $order_table->update(array('order_status' => 2), array('order_id = ?' => $order_id['order_id']));
                    }
                }

                // CHECK IS THERE ANY DOWNLOADABLE PRODUCT
                if (empty($is_any_downloadable_product)) {
                    $anyDownloadableProducts = $orderProductTable->checkProductType(array('order_id' => $order_id['order_id']));
                    if (!empty($anyDownloadableProducts)) {
                        $is_any_downloadable_product = true;
                        $my_downloadable_link = $this->view->translate("To download this product, please go to %s page.", $this->view->htmlLink($this->view->url(array('action' => 'account', 'menuType' => 'my-downloadable-products'), 'sitestoreproduct_general', true), $this->view->translate("My Downloadable Products")));
                    }
                }

                // SUCCESS MESSAGE
                if ($tempOrderCount != 1) {
                    if ($tempCount == $tempOrderCount)
                        $viewer_orders .= $this->view->translate(" SITESTOREPRODUCT_CHECKOUT_AND ");
                    else
                        $viewer_orders .= ', ';
                }
                if (empty($viewer_id))
                    $viewer_orders .= '#' . $order_id['order_id'];
                else {
                    $tempViewUrl = $this->view->url(array('action' => 'account', 'menuType' => 'my-orders', 'subMenuType' => 'order-view', 'orderId' => $order_id['order_id']), 'sitestoreproduct_general', true);
                    $viewer_orders .= '<a href="' . $tempViewUrl . '">#' . $order_id['order_id'] . '</a>';
                }
                $tempOrderCount++;
            }
            $success_message .= $viewer_orders . '. <br><br> ';

            if ($parent_order_obj->gateway_id == 3)
                $success_message .= $this->view->translate("Your order has been sent for the confirmation and after that you will receive the invoice of your order.");
            else {
                $success_message .= $this->view->translate("You will soon receive your order confirmation with invoice.");
                if (!empty($viewer_id))
                    $success_message .= $this->view->translate("You can also view the invoice for your order from your store account.") . $my_downloadable_link;
            }
            $this->view->success_message = $success_message;
            $this->view->indexNo = $tempOrderCount;

            $order_table->update(array('payment_status' => $parent_order_obj->payment_status), array('parent_id = ?' => $parent_id));
            if (!isset($_COOKIE['sitestoreproduct_order_place_success']) || (!empty($_COOKIE['sitestoreproduct_order_place_success']) && $_COOKIE['sitestoreproduct_order_place_success'] != $parent_id)) {
                Engine_Api::_()->sitestoreproduct()->orderPlaceMailAndNotification($order_ids);
                setcookie('sitestoreproduct_order_place_success', $parent_id, time() + 3600 * 24);
            }
            $this->view->viewerOrders = $viewer_orders;
        }
    }

    public function shippingMethodsAction() {

        //TO SHOW SHIPPING METHODS ON USER SIDE
        $isViewerSide = $this->_getParam('isViewerSide', null);

        // ONLY LOGGED IN USER 
        if (empty($isViewerSide) && !$this->_helper->requireUser()->isValid())
            return;

        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
        $this->view->addNotice = $this->_getParam('notice', null);

        //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
        if (empty($isManageAdmin) && empty($isViewerSide)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        $this->view->canEdit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

        //FLAG FOR ANY COUNTRY/STATE IS ENABLE : 1 OR NOT : 0 
        $this->view->noCountryEnable = 0;
        $this->view->call_same_action = $this->_getParam('call_same_action', 0);

        $regionObj = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->isAnyCountryEnable();

        if (array_key_exists(0, $regionObj)) {
            $this->view->noCountryEnable = 1;
        }

        $params = array();
        $params['store_id'] = $store_id;
        $params['page'] = $this->_getParam('page', 1);
        if (!empty($isViewerSide)) {
            $params['limit'] = 1000;
            $product_id = $this->_getParam('product_id', null);
            $this->view->sitestoreproduct = $siteStoreProduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        } else {
            $params['limit'] = 8;
        }
        $this->view->paginator = $this->view->paginator = Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct')->getShippingMethodsPaginator($params);

        $this->view->weightUnit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.weight.unit', 'lbs');

        if (!empty($isViewerSide))
            $this->renderScript('/_shippingMethods.tpl');
    }

    function multideleteShippingMethodsAction() {

        $values = $this->getRequest()->getPost();
        foreach ($values['shippingmethod_id'] as $shippingmethod_id) {
            Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct')->delete(array('shippingmethod_id = ?' => $shippingmethod_id));
        }
        $this->view->success = 1;
    }

    // ENABLE AND DISABLE REGION ON MANAGE COUNTRIES STORE
    public function shippingMethodEnableAction() {
        $store_id = $this->_getParam('store_id', null);
        $shippingMethodId = $this->_getParam('id', null);

        $shippingMethodItem = Engine_Api::_()->getItem('sitestoreproduct_shippingmethod', $shippingMethodId);

        // CHANGING ACTIVE TO COMPLEMENT OF PRESENT ACTIVE VALUE
        $shippingMethodItem->status = !$shippingMethodItem->status;
        $shippingMethodItem->save();
        $this->view->activeFlag = $shippingMethodItem->status;
    }

    public function addShippingMethodAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
        $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

        //IS USER IS STORE ADMIN OR NOT
        if (empty($authValue))
            return $this->_forward('requireauth', 'error', 'core');
        else if ($authValue == 1)
            return $this->_forward('notfound', 'error', 'core');

        $this->view->noCountryEnable = 0;
        $regionObj = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->isAnyCountryEnable();
        if (array_key_exists(0, $regionObj)) {
            $this->view->noCountryEnable = 1;
        }

        $this->view->form = $form = new Sitestoreproduct_Form_Shipping_AddMethod();


        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $this->view->currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
        $this->view->weightUnit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.weight.unit', 'lbs');
    }

    public function editShippingMethodAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
        $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

        //IS USER IS STORE ADMIN OR NOT
        if (empty($authValue))
            return $this->_forward('requireauth', 'error', 'core');
        else if ($authValue == 1)
            return $this->_forward('notfound', 'error', 'core');

        $this->view->noCountryEnable = 0;
        $regionObj = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->isAnyCountryEnable();
        if (array_key_exists(0, $regionObj)) {
            $this->view->noCountryEnable = 1;
        }

        $this->view->method_id = $method_id = $this->_getParam('method_id', false);

        $shippingMethodItem = Engine_Api::_()->getItem('sitestoreproduct_shippingmethod', $method_id);
        $shippingMethodPopulateArray = $shippingMethodItem->toArray();

        $regionItem = Engine_Api::_()->getItem('sitestoreproduct_region', $shippingMethodPopulateArray['region']);

        if ($shippingMethodPopulateArray['country'] != 'ALL') {
            $regionCountry = Zend_Locale::getTranslation($shippingMethodPopulateArray['country'], 'country');
            $this->view->flagAllCountries = 0;
        } else {
            $regionCountry = 'All Countries';
            $this->view->flagAllCountries = 1;
        }

        if (empty($shippingMethodPopulateArray['region'])) {
            $region = 'All Regions';
        } else {
            $region = $regionItem->region;
        }

        $this->view->form = $form = new Sitestoreproduct_Form_Shipping_EditMethod(array('location' => $region, 'country' => $regionCountry));

        //SET VALUE OF PRICE/RATE 0 ACCORDING TO HANDLING FEE
        if ($shippingMethodPopulateArray['handling_type'] == 0)
            $shippingMethodPopulateArray['price'] = round($shippingMethodPopulateArray['handling_fee'], 2);
        else
            $shippingMethodPopulateArray['rate'] = round($shippingMethodPopulateArray['handling_fee'], 2);

        if ($shippingMethodPopulateArray['dependency'] != 2)
            $shippingMethodPopulateArray['ship_start_limit'] = round($shippingMethodPopulateArray['ship_start_limit'], 2);

        $this->view->flagWeightFrom = round($shippingMethodPopulateArray['allow_weight_from'], 2);
        $this->view->flagWeightTo = round($shippingMethodPopulateArray['allow_weight_to'], 2);
        $this->view->flagShipFrom = round($shippingMethodPopulateArray['ship_start_limit'], 2);
        $this->view->flagShipTo = round($shippingMethodPopulateArray['ship_end_limit'], 2);
        $this->view->flagShipType = $shippingMethodPopulateArray['ship_type'];

        $form->populate($shippingMethodPopulateArray);

        //IF NOT AJAX REQUEST THEN RETURN.
        if (!empty($isajax))
            return;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $values = $this->getRequest()->getPost();

        if ($values['handling_type'] == 0)
            $form->rate->setValidators(array());
        else
            $form->price->setValidators(array());

        $this->view->flagShipType = $values['ship_type'];
        $this->view->flagWeightFromValidate = $values['allow_weight_from'];
        $this->view->flagWeightToValidate = $values['allow_weight_to'];
        $this->view->flagShipFrom = $values['ship_start_limit'];
        $this->view->flagShipTo = $values['ship_end_limit'];
    }

    public function deleteShippingMethodAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $store_id = $this->_getParam('store_id', null);
        $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

        //IS USER IS STORE ADMIN OR NOT
        if (empty($authValue))
            return $this->_forward('requireauth', 'error', 'core');
        else if ($authValue == 1)
            return $this->_forward('notfound', 'error', 'core');

        $id = $this->_getParam('id');
        $this->view->shippingmethod_id = $id;

        if ($this->getRequest()->isPost()) {
            $shippingMethodItem = Engine_Api::_()->getItem('sitestoreproduct_shippingmethod', $id);

            //IF TABLE OBJECT NOT EMPTY THEN DELETE ROW
            if (!empty($shippingMethodItem))
                $shippingMethodItem->delete();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Shipping method deleted successfully.'))
            ));
        }
    }

    public function changestateAction() {
        $params = array();
        $params['country'] = $this->_getParam('country', null);

        $flag_add_tax_rate = $this->_getParam('flag_add_tax_rate', 0);

        if ($flag_add_tax_rate == 0) {
            $params['store_id'] = $this->_getParam('store_id', null);
            $shippingCountries = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getRegionsByName($params);
        } else {
            $flag_edit_tax_rate = $this->_getParam('flag_edit_tax_rate', 0);
            $params['tax_id'] = $this->_getParam('tax_id', null);
            $shippingCountriesArray = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getRegionsAddTaxRate($params);

            $shippingCountries = $shippingCountriesArray['regions'];
            $this->view->all_region = Zend_Json::encode($shippingCountriesArray['all_region']);
        }

        $emptyRegionIdArray = $countryArray = array();
        $countryArray = array();


        if (!empty($flag_edit_tax_rate)) {
            $regionObj = Engine_Api::_()->getItem('sitestoreproduct_region', $this->_getParam('region_id', null));
            if (empty($regionObj->region))
                $emptyRegionIdArray[] = $regionObj->region_id;

            if ($params['country'] == $regionObj->country)
                $countryArray[] = $regionObj->region . '_' . $this->_getParam('region_id', null);
        }

        foreach ($shippingCountries as $shippingCountry) {
            if (empty($shippingCountry['region']))
                $emptyRegionIdArray[] = $shippingCountry['region_id'];
            $countryArray[] = $shippingCountry['region'] . '_' . $shippingCountry['region_id'];
        }


        $tempFlag = @COUNT($countryArray);
        $tempCount = @COUNT($emptyRegionIdArray);
        $tempFlag = $tempFlag - $tempCount;

        $this->view->tempFlag = $tempFlag;
        $this->view->length = Zend_Json::encode($countryArray);
    }

    //SAVE ADDRESS BY AJAX CALL IN CHECK OUT PROCESS
    public function saveaddressAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $form = new Sitestoreproduct_Form_Addresses(array('viewerId' => $viewer_id, 'showShipping' => $_POST['show_shipping']));
        $valueBilling = $valueShipping = array();

        $valueShipping['owner_id'] = $valueBilling['owner_id'] = $viewer_id;
        $address = array();
        @parse_str($_POST['address'], $address);

        $showShipping = $_POST['show_shipping'];

        $billingAddId = $_POST['billing_add_id'];
        $valueBilling['f_name'] = $valueShipping['f_name'] = $address['f_name_billing'];
        $valueBilling['l_name'] = $valueShipping['l_name'] = $address['l_name_billing'];

        if (empty($viewer_id))
            $valueBilling['email'] = $address['email_billing'];

        $valueBilling['phone'] = $valueShipping['phone'] = $address['phone_billing'];
        $valueBilling['country'] = $valueShipping['country'] = $address['country_billing'];

        if (isset($address['state_billing']) && !empty($address['state_billing']))
            $valueBilling['state'] = $valueShipping['state'] = $address['state_billing'];

        $valueBilling['city'] = $valueShipping['city'] = $address['city_billing'];
        $valueBilling['locality'] = $valueShipping['locality'] = $address['locality_billing'];
        $valueBilling['zip'] = $valueShipping['zip'] = $address['zip_billing'];
        $valueBilling['address'] = $valueShipping['address'] = $address['address_billing'];
        $valueBilling['type'] = 0;

        if (empty($showShipping))
            $address['common'] = 2;

        if ($address['common'] == 2) {
            $valueShipping['common'] = $valueBilling['common'] = 1;
        } else {
            $valueShipping['common'] = $valueBilling['common'] = 0;
            $valueShipping['f_name'] = $address['f_name_shipping'];
            $valueShipping['l_name'] = $address['l_name_shipping'];
            $valueShipping['phone'] = $address['phone_shipping'];
            $valueShipping['country'] = $address['country_shipping'];
            $valueShipping['state'] = $address['state_shipping'];
            $valueShipping['city'] = $address['city_shipping'];
            $valueShipping['locality'] = $address['locality_shipping'];
            $valueShipping['zip'] = $address['zip_shipping'];
            $valueShipping['address'] = $address['address_shipping'];
        }
        $shippingAddId = $_POST['shipping_add_id'];
        $valueShipping['type'] = 1;

        $errorMessage = null;
        $form->setDisableTranslator(true);
        $errorObj = $form->processAjax($address);

        $getErrors = Zend_Json::decode($errorObj);
        //IF SAME ADDRESS IS CHECKED THEN UNSET THE VALIDATORS FOR SHIPPING ADDRESS
        if ($address['common'] == 2) {
            unset($getErrors['f_name_shipping']);
            unset($getErrors['l_name_shipping']);
            unset($getErrors['phone_shipping']);
            unset($getErrors['country_shipping']);
            unset($getErrors['state_shipping']);
            unset($getErrors['city_shipping']);
            unset($getErrors['locality_shipping']);
            unset($getErrors['zip_shipping']);
            unset($getErrors['address_shipping']);
        }

        $errorMessage = array();
        if (!empty($showShipping)) {
            if (empty($viewer_id))
                $tempArray = array('f_name_billing', 'l_name_billing', 'email_billing', 'phone_billing', 'country_billing', 'city_billing', 'locality_billing', 'zip_billing', 'address_billing', 'f_name_shipping', 'l_name_shipping', 'phone_shipping', 'country_shipping', 'city_shipping', 'locality_shipping', 'zip_shipping', 'address_shipping');
            else
                $tempArray = array('f_name_billing', 'l_name_billing', 'phone_billing', 'country_billing', 'city_billing', 'locality_billing', 'zip_billing', 'address_billing', 'f_name_shipping', 'l_name_shipping', 'phone_shipping', 'country_shipping', 'city_shipping', 'locality_shipping', 'zip_shipping', 'address_shipping');
        }else {
            if (empty($viewer_id))
                $tempArray = array('f_name_billing', 'l_name_billing', 'email_billing', 'phone_billing', 'country_billing', 'city_billing', 'locality_billing', 'zip_billing', 'address_billing');
            else
                $tempArray = array('f_name_billing', 'l_name_billing', 'phone_billing', 'country_billing', 'city_billing', 'locality_billing', 'zip_billing', 'address_billing');
        }

        foreach ($tempArray as $values) {
            $errorMessage[$values] = $values . '_error=0';
        }
        $this->view->errorFlag = '0';

        //TAKING ERROR MESSAGES IN $errorMessage STRING
        if (@is_array($getErrors)) {
            foreach ($getErrors as $key => $errorArray) {
                $this->view->errorFlag = '1';
                foreach ($errorArray as $errorMsg) {
                    $errorMsg = $this->view->translate($errorMsg);
                    $errorMessage[$key] = $key . '_error=' . $errorMsg;
                }
            }
        }
        $newErrorStr = @implode('::', $errorMessage);
        $this->view->errorStr = $newErrorStr;


        //IF NOT ANY ERROR THEN PROCEED TO DATABASE OPERATION
        if ($this->view->errorFlag == '0') {
            $this->view->billing_region_id = $valueShipping['state'];
            $this->view->billing_country_id = $valueBilling['country'];
            if (empty($showShipping)) {
                $this->view->shipping_country_id = 0;
                $this->view->shipping_region_id = 0;
            } else {
                $this->view->shipping_country_id = $valueShipping['country'];
                $this->view->shipping_region_id = $valueBilling['state'];
            }

            $addressObj = Engine_Api::_()->getDbtable('addresses', 'sitestoreproduct');

            $params = array();
            $params['owner_id'] = $viewer_id;
            $addressResult = $addressObj->getAddress($params);

            //CHECK BEFORE INSERTING ADDRESS IN DATABASE WHERE IT IS ALREADY EXIST OR NOT
            foreach ($addressResult as $address) {
                //IF BILLING ADDRESS (TYPE = 0) ALREADY EXIST IN TABLE THEN POPULATE
                if (empty($address->type)) {
                    $billingAddId = $address->address_id;
                } else {
                    $shippingAddId = $address->address_id;
                }
            }

            $db = $addressObj->getAdapter();
            $db->beginTransaction();
            try {
                if (empty($billingAddId)) {
                    // SAVE BILLING ADDRESS INFORMATIONS
                    $row = $addressObj->createRow();
                    $row->setFromArray($valueBilling);
                    $billingAddId = $row->save();

                    // SAVE SHIPPING ADDRESS INFORMATIONS
                    $row = $addressObj->createRow();
                    $row->setFromArray($valueShipping);
                    $shippingAddId = $row->save();
                } else {
                    //IF ROW ALREADY EXIST THEN UPDATE
                    if (empty($viewer_id)) {
                        $addressObj->update(array(
                            'f_name' => $valueBilling['f_name'],
                            'l_name' => $valueBilling['l_name'],
                            'email' => $valueBilling['email'],
                            'phone' => $valueBilling['phone'],
                            'address' => $valueBilling['address'],
                            'country' => $valueBilling['country'],
                            'state' => $valueBilling['state'],
                            'city' => $valueBilling['city'],
                            'locality' => $valueBilling['locality'],
                            'zip' => $valueBilling['zip'],
                            'common' => $valueBilling['common']
                                ), array(
                            'address_id = ?' => $billingAddId,
                        ));
                    } else {
                        $addressObj->update(array(
                            'f_name' => $valueBilling['f_name'],
                            'l_name' => $valueBilling['l_name'],
                            'phone' => $valueBilling['phone'],
                            'address' => $valueBilling['address'],
                            'country' => $valueBilling['country'],
                            'state' => $valueBilling['state'],
                            'city' => $valueBilling['city'],
                            'locality' => $valueBilling['locality'],
                            'zip' => $valueBilling['zip'],
                            'common' => $valueBilling['common']
                                ), array(
                            'address_id = ?' => $billingAddId,
                        ));
                    }

                    //IF SHIPPING ADDRESS ALREADY EXIST THEN THEN UPDATE
                    if (!empty($shippingAddId) && !empty($showShipping)) {
                        $addressObj->update(array(
                            'f_name' => $valueShipping['f_name'],
                            'l_name' => $valueShipping['l_name'],
                            'phone' => $valueShipping['phone'],
                            'address' => $valueShipping['address'],
                            'country' => $valueShipping['country'],
                            'state' => $valueShipping['state'],
                            'city' => $valueShipping['city'],
                            'locality' => $valueShipping['locality'],
                            'zip' => $valueShipping['zip'],
                            'common' => $valueShipping['common']
                                ), array(
                            'address_id = ?' => $shippingAddId,
                        ));
                    }
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }return;
        }
    }

    public function printInvoiceAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $order_id = Engine_Api::_()->sitestoreproduct()->getEncodeToDecode($this->_getParam('order_id', null));
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if (empty($order_id)) {
            $this->view->sitestoreproduct_print_invoice_no_permission = true;
            return;
        }

        //USER IS BUYER OR NOT
        $this->view->orderObj = $orderObj = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);
        if ($viewer_id != $orderObj->buyer_id) {
            $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($orderObj->store_id);

            //IS USER IS STORE ADMIN OR NOT
            if (empty($authValue) || $authValue == 1) {
                $this->view->sitestoreproduct_print_invoice_no_permission = true;
                return;
            }
        }

        $this->_helper->layout->setLayout('default-simple');
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');

        if (!empty($orderObj->buyer_id)) {
            $user_table = Engine_Api::_()->getDbtable('users', 'user');
            $select = $user_table->select()->from($user_table->info('name'), array("email", "displayname"))->where('user_id =?', $orderObj->buyer_id);
            $this->view->user_detail = $user_table->fetchRow($select);
        }

        // FETCH SITE LOGO OR TITLE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', 'header')->limit(1);

        $info = $select->query()->fetch();
        if (!empty($info)) {
            $page_id = $info['page_id'];

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_content', array("params"))
                    ->where('page_id = ?', $page_id)
                    ->where("name LIKE '%core.menu-logo%'")
                    ->limit(1);
            $info = $select->query()->fetch();
            $params = json_decode($info['params']);

            if (!empty($params->logo))
                $this->view->logo = $params->logo;
        }

        if ($orderObj->gateway_id == 3)
            $this->view->cheque_info = Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->getChequeDetail($orderObj->cheque_id);

        if (!empty($this->view->directPayment)) {
            $this->view->storeTitle = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($orderObj->store_id, 'title');
            $this->view->storeChequeDetail = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->getStoreChequeDetail(array('store_id' => $orderObj->store_id));
        } else {
            $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
        }
        $this->view->storeAddress = Engine_Api::_()->sitestoreproduct()->getStoreAddress($orderObj->store_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $orderObj->store_id);
        $this->view->order_products = $order_products = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->getOrderProductsDetail($order_id);
        $this->view->billing_address = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getAddress($order_id);
        $this->view->shipping_address = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getAddress($order_id, true, array('address_type' => 1));
        $this->view->billing_region_name = Engine_Api::_()->getItem('sitestoreproduct_region', $this->view->billing_address->state)->region;
        $this->view->shipping_region_name = Engine_Api::_()->getItem('sitestoreproduct_region', $this->view->shipping_address->state)->region;
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestore/View/Helper', 'Sitestore_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitestore);
    }

    public function printPackingSlipAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $order_id = Engine_Api::_()->sitestoreproduct()->getEncodeToDecode($this->_getParam('order_id', null));
        $this->view->orderObj = $orderObj = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);

        $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($orderObj->store_id);

        //IS USER IS STORE ADMIN OR NOT
        if (empty($authValue) || $authValue == 1) {
            $this->view->sitestoreproduct_packing_slip_no_permission = true;
            return;
        }

        $this->_helper->layout->setLayout('default-simple');
        if (!empty($orderObj->buyer_id)) {
            $user_table = Engine_Api::_()->getDbtable('users', 'user');
            $select = $user_table->select()->from($user_table->info('name'), array("email", "displayname"))->where('user_id =?', $orderObj->buyer_id);
            $this->view->user_detail = $user_table->fetchRow($select);
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', 'header')->limit(1);

        $info = $select->query()->fetch();
        if (!empty($info)) {
            $page_id = $info['page_id'];

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_content', array("params"))
                    ->where('page_id = ?', $page_id)
                    ->where("name LIKE '%core.menu-logo%'")
                    ->limit(1);
            $info = $select->query()->fetch();
            $params = json_decode($info['params']);

            if (!empty($params->logo))
                $this->view->logo = $params->logo;
        }
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');

        $this->view->storeAddress = Engine_Api::_()->sitestoreproduct()->getStoreAddress($orderObj->store_id);
        $this->view->order_products = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->getOrderProductsDetail($order_id);
        $this->view->billing_address = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getAddress($order_id);
        $this->view->shipping_address = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getAddress($order_id, true, array('address_type' => 1));
        $this->view->billing_region_name = Engine_Api::_()->getItem('sitestoreproduct_region', $this->view->billing_address->state)->region;
        $this->view->shipping_region_name = Engine_Api::_()->getItem('sitestoreproduct_region', $this->view->shipping_address->state)->region;
    }

    public function orderDetailAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $order_id = $this->_getParam('order_id', null);
        $this->view->orderObj = $orderObj = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if (empty($order_id) || empty($orderObj)) {
            $this->view->sitestoreproduct_view_detail_no_permission = true;
            return;
        }

        //USER IS BUYER OR NOT
        if ($viewer_id != $orderObj->buyer_id) {
            $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($orderObj->store_id);

            if (empty($authValue) || $authValue == 1) {
                $this->view->sitestoreproduct_view_detail_no_permission = true;
                return;
            }
        }

        $this->_helper->layout->setLayout('default-simple');
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        $this->view->storeAddress = Engine_Api::_()->sitestoreproduct()->getStoreAddress($orderObj->store_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $orderObj->store_id);
        $this->view->order_products = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->getOrderProductsDetail($order_id);
        $this->view->billing_address = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getAddress($order_id, false, array('address_type' => 0));
        $this->view->shipping_address = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getAddress($order_id, true, array('address_type' => 1));
        $this->view->billing_region_name = Engine_Api::_()->getItem('sitestoreproduct_region', $this->view->billing_address->state)->region;
        $this->view->shipping_region_name = Engine_Api::_()->getItem('sitestoreproduct_region', $this->view->shipping_address->state)->region;

        if (!empty($orderObj->buyer_id)) {
            $user_table = Engine_Api::_()->getDbtable('users', 'user');
            $select = $user_table->select()->from($user_table->info('name'), array("email", "displayname"))->where('user_id =?', $orderObj->buyer_id);
            $this->view->user_detail = $user_table->fetchRow($select);
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', 'header')->limit(1);

        $info = $select->query()->fetch();
        if (!empty($info)) {
            $page_id = $info['page_id'];

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_content', array("params"))
                    ->where('page_id = ?', $page_id)
                    ->where("name LIKE '%core.menu-logo%'")
                    ->limit(1);
            $info = $select->query()->fetch();
            $params = json_decode($info['params']);

            if (!empty($params) && !empty($params->logo))
                $this->view->logo = $params->logo;
        }
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestore/View/Helper', 'Sitestore_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitestore);
    }

    function curPageURLAction() {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    public function checkoutAction() {
        // GET VIEWER
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $this->view->viewer_id = $viewer_id ;
    $creditAllow=false;
    $isSitecreditEnabled = Engine_Api::_()->hasModuleBootstrap('sitecredit');
    if($isSitecreditEnabled){
     $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
     if($isAdminDrivenStore){
      if(!empty($viewer_id)){
        $CreditModuleTable = Engine_Api::_()->getDbtable('modules', 'sitecredit');
      $select=$CreditModuleTable->select()->where('name = ?','sitestore');
      $creditModuleAllow=$CreditModuleTable->fetchRow($select);

      if(!empty($creditModuleAllow->integrated)){

        $param['user_id']=$viewer_id;
        $param['basedon']= 1;
        $param['count']= 1;
        $credits = Engine_Api::_()->getDbtable('credits','sitecredit')->Credits($param);
        $totalCredits= $credits->credit;
        
        $creditCheckout = $creditModuleAllow->minimum_credit;
        if(!empty($creditCheckout)){
         if($totalCredits>$creditCheckout)
          $creditAllow=true;
      } else {
        $creditAllow=true;
      }
    }
  }
}
}
$this->view->creditAllow=$creditAllow;

        $isBuyAllow = Engine_Api::_()->sitestoreproduct()->isBuyAllowed();
        if (empty($isBuyAllow)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $place_order = $this->_getParam('placeOrder', 1);

        $isPaymentToSiteEnable = true;
        $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
        if (empty($isAdminDrivenStore)) {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
        }
        $this->view->isPaymentToSiteEnable = $isPaymentToSiteEnable;

        if (empty($isPaymentToSiteEnable)) {
            $this->view->store_id = $checkout_store_id = $this->_getParam('store_id', null);
            if (empty($checkout_store_id) && ($place_order != 'login')) {
                return $this->_helper->redirector->gotoRoute(array('action' => 'cart'), 'sitestoreproduct_product_general', true);
            }
        }

        // DELETE SESSION VARIABLE OF MAKE PAYMENT, IF EXIST
        $session = new Zend_Session_Namespace('sitestoreproduct_make_payment');
        if (!empty($session->sitestoreproduct_orders_make_payment)) {
            unset($session->sitestoreproduct_orders_make_payment);
        }
    if($creditAllow){
        $creditsession = new Zend_Session_Namespace('sitestoreproduct_cart_credit');
    if (!empty($creditsession->sitestoreproductCartCreditDetail)) {
     $this->view->creditDetail=$creditDetail = unserialize($creditsession->sitestoreproductCartCreditDetail);
     }
    }    
        // IF USER SELECT CHECKOUT AS A REGISTERED MEMBER
        if (($place_order == 'login')) {
            if (empty($viewer_id)) {
                if (!$this->_helper->requireUser()->isValid())
                    return;
            }
            else {
                if (empty($isPaymentToSiteEnable)) {
                    return $this->_helper->redirector->gotoRoute(array('action' => 'checkout', 'store_id' => $checkout_store_id), 'sitestoreproduct_general', true);
                } else {
                    return $this->_helper->redirector->gotoRoute(array('action' => 'checkout'), 'sitestoreproduct_general', true);
                }
            }
        }

        // MANAGE COMPLETE CHECKOUT PROCESS
        $checkout_process = array();

        // FOR LOGGED-OUT VIEWER
        if (empty($viewer_id)) {
            $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');

            if (empty($session->sitestoreproduct_guest_user_cart)) {
                $this->_helper->content->setEnabled();
                $this->view->sitestoreproduct_checkout_viewer_cart_empty = true;
                Zend_Registry::set('sitestoreproduct_checkout_viewer_cart_empty', true);
                return;
            }

            $tempUserCart = array();
            $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);
        } else {
            $cart_obj = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->fetchRow(array('owner_id = ?' => $viewer_id));

            if (empty($cart_obj) || empty($cart_obj->cart_id)) {
                $this->_helper->content->setEnabled();
                Zend_Registry::set('sitestoreproduct_checkout_viewer_cart_empty', true);
                $this->view->sitestoreproduct_checkout_viewer_cart_empty = true;
                return;
            }
        }

        $cartProductTable = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');

        // DELETE PRODUCTS FROM VIEWER CART FOR WHICH THERE ARE NO SHIPPING METHODS AVAILABLE
        if (!empty($_POST['no_shipping_method_stores']) && $place_order == 4) {
            $checkout_cart = @unserialize($_POST['cart_products_detail']);
            $store_products = @unserialize($_POST['stores_products']);
            $no_shipping_method_stores = @unserialize($_POST['no_shipping_method_stores']);
            $store_product_types = @unserialize($_POST['store_product_types']);

            foreach ($no_shipping_method_stores as $store_id) {
                $index = 0;
                $downloadable_product_exist = false;
                foreach ($store_products[$store_id] as $store_product_ids) {
                    // IF THERE IS DOWNLOADABLE PRODUCT, THEN DON'T REMOVE IT FROM VIEWER CART
                    if ($checkout_cart[$store_product_ids]['product_type'] == 'downloadable') {
                        $downloadable_product_exist = true;
                        $index++;
                        continue;
                    } else {
                        // DELETE PRODUCT FROM STORE_PRODUCTS ARRAY AND CHECKOUT_CART WHICH WE PASS TO NEXT PROCESS
                        unset($store_products[$store_id][$index++]);
                        unset($checkout_cart[$store_product_ids]);

                        // DELETE PRODUCT FROM VIEWER CART
                        if (empty($viewer_id)) {
                            unset($tempUserCart[$store_product_ids]);
                        } else {
                            $cartProductTable->delete(array("product_id =?" => $store_product_ids, "cart_id =?" => $cart_obj->cart_id));
                        }
                    }
                }

                // UPDATE LOGGED-OUT VIEWER CART
                if (empty($viewer_id)) {
                    $session->sitestoreproduct_guest_user_cart = @serialize($tempUserCart);
                }

                // IF THERE IS NO DOWNLOADABLE PRODUCTS IN STORE, THEN REMOVE THAT STORE ENTRY FROM STORE_PRODUCTS ARRAY
                if (empty($downloadable_product_exist)) {
                    unset($store_products[$store_id]);
                }

                // NOW THERE IS ONLY DOWNLOADABLE PRODUCTS IN STORE, IF EXIST
                unset($store_product_types[$store_id]);
            }

            // IF CHECKOUT CART IS EMPTY, THEN RETURN TO MANAGE CART STORE
            if (empty($checkout_cart)) {
                echo 'return_to_cart';
                die;
            }

            $this->view->cart_products_detail = @serialize($checkout_cart);
            $this->view->stores_products = @serialize($store_products);
            $this->view->store_product_types = @serialize($store_product_types);
        }

        $checkoutHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.level.createhost', 0);
        $checkoutSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.lsettings', 0);

        // FETCH CURRENT CART PRODUCTS OF VIEWER
        if (empty($viewer_id)) {
            $product_ids = array();
            foreach ($tempUserCart as $product_id => $values) {
                $product_ids[] = $product_id;
            }
            $product_ids = implode(",", $product_ids);

            if (empty($product_ids)) {
                $this->_helper->content->setEnabled();
                $this->view->sitestoreproduct_checkout_viewer_cart_empty = true;
                Zend_Registry::set('sitestoreproduct_checkout_viewer_cart_empty', true);
                return;
            }
            // IF DIRECT PAYMENT MODE IS ENABLED, THEN FETCH ONLY THAT STORE PRODUCTS
            if (empty($isPaymentToSiteEnable))
                $sitestoreproduct_checkout_viewer_cart = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getLoggedOutViewerCartDetail($product_ids, false, array('store_id' => $checkout_store_id));
            else
                $sitestoreproduct_checkout_viewer_cart = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getLoggedOutViewerCartDetail($product_ids);
        } else {
            // IF DIRECT PAYMENT MODE IS ENABLED, THEN FETCH ONLY THAT STORE PRODUCTS
            if (empty($isPaymentToSiteEnable))
                $sitestoreproduct_checkout_viewer_cart = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCheckoutViewerCart($cart_obj->cart_id, array('store_id' => $checkout_store_id));
            else
                $sitestoreproduct_checkout_viewer_cart = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCheckoutViewerCart($cart_obj->cart_id);
        }

        $getPaymentType = $this->getPaymentType($checkoutHost, 'sitestore');
        $getPaymentAuth = $this->getPaymentAuth($checkoutSettings);

        $sitestoreproduct_checkout = Zend_Registry::isRegistered('sitestoreproduct_checkout') ? Zend_Registry::get('sitestoreproduct_checkout') : null;

        // WHEN VIEWER COME TO NEXT STEP OF CHECKOUT PROCESS, THEN FIRST CHECK THE VIEWER CART. IF IT IS CHANGED THEN REDIRECT TO MANAGE CART STORE
        if (isset($_POST['cart_products_detail']) && !empty($_POST['cart_products_detail'])) {
            if (empty($sitestoreproduct_checkout)) {
                echo 'return_to_cart';
                die;
            }
            $this->_helper->layout->setLayout('default-simple');
            $checkout_process = @unserialize($_POST['checkout_process']);
            $this->view->checkout_store_name = $_POST['checkout_store_name'];
            $this->view->sitestoreproduct_downloadable_product = $_POST['sitestoreproduct_downloadable_product'];
            $this->view->sitestoreproduct_other_product_type = $_POST['other_product_type'];
            $this->view->coupon_store_id = $_POST['coupon_store_id'];

            if (empty($_POST['no_shipping_method_stores'])) {
                $this->view->store_product_types = $_POST['store_product_types'];
                $this->view->cart_products_detail = $_POST['cart_products_detail'];
                $this->view->stores_products = $_POST['stores_products'];

                $checkout_cart = @unserialize($_POST['cart_products_detail']);
            }

            if (!empty($_POST['address'])) {
                $this->view->address = $_POST['address'];
            }

            // CHECK THAT VIEWER CART IS CHANGED OR NOT
            if (COUNT($sitestoreproduct_checkout_viewer_cart) != COUNT($checkout_cart)) {
                echo 'return_to_cart';
                die;
            }

            foreach ($sitestoreproduct_checkout_viewer_cart as $cart) {
                $product_id = $cart['product_id'];

                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
                    if (!empty($cart['closed']) || !empty($cart['draft']) || empty($cart['search']) || empty($cart['approved']) || $cart['start_date'] > date('Y-m-d H:i:s') || ( $cart['end_date'] < date('Y-m-d H:i:s') && !empty($cart['end_date_enable']) )) {
                        echo 'return_to_cart';
                        die;
                    }
                } else {
                    if (!empty($cart['draft']) || empty($cart['search']) || empty($cart['approved']) || $cart['start_date'] > date('Y-m-d H:i:s') || ( $cart['end_date'] < date('Y-m-d H:i:s') && !empty($cart['end_date_enable']) )) {
                        echo 'return_to_cart';
                        die;
                    }
                }

                if (empty($viewer_id)) {
                    // FETCH VIRTUAL OR CONFIGURABLE PRODUCTS QUANTITY
                    if ($cart['product_type'] == 'configurable' || $cart['product_type'] == 'virtual') {
                        $cart['quantity'] = 0;
                        foreach ($tempUserCart[$product_id]['config'] as $config_item) {
                            $cart['quantity'] += $config_item['quantity'];
                        }
                    } else {
                        $cart['quantity'] = $tempUserCart[$product_id]['quantity'];
                    }
                }

                if (!empty($viewer_id) && ($cart['product_type'] == 'configurable' || $cart['product_type'] == 'virtual')) {
                    $cartproducts_quantity = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getConfiguration(array("SUM(quantity) as quantity"), $cart['product_id'], $cart['cart_id']);
                    $cart['quantity'] = $cartproducts_quantity[0]['quantity'];
                }

                // IF CHANGE IN VIEWER CART THEN REDIRECT TO MANAGE CART
                if (($product_id != $checkout_cart[$product_id]['product_id']) || ($cart['quantity'] != $checkout_cart[$product_id]['quantity']) || ($checkout_cart[$product_id]['quantity'] > $cart['in_stock'] && empty($cart['stock_unlimited']))) {
                    echo 'return_to_cart';
                    die;
                }

                // IF PRODUCTS CONFIGURATION IS CHANGED THEN ALSO REDIRECT TO MANAGE CART
                if ($checkout_cart[$product_id]['product_type'] == 'configurable' || $checkout_cart[$product_id]['product_type'] == 'virtual') {
                    if (isset($checkout_cart[$product_id]['config']) && !empty($checkout_cart[$product_id]['config'])) {
                        if (empty($viewer_id)) {
                            if (COUNT($tempUserCart[$product_id]['config']) != COUNT($checkout_cart[$product_id]['config'])) {
                                echo 'return_to_cart';
                                die;
                            }

                            foreach ($checkout_cart[$product_id]['config'] as $config_index => $config_item) {
                                if ($config_item != $tempUserCart[$product_id]['config'][$config_index]) {
                                    echo 'return_to_cart';
                                    die;
                                }
                            }
                        } else {
                            $viewer_cartproducts_id = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getConfigurationId($product_id, $cart['cart_id']);
                            if ($viewer_cartproducts_id != $checkout_cart[$product_id]['config']) {
                                echo 'return_to_cart';
                                die;
                            }
                        }
                    } else {
                        echo 'return_to_cart';
                        die;
                    }
                }

                if ($cart['product_type'] != 'downloadable' && $cart['product_type'] != 'virtual') {
                    $this->view->other_product_type_exist = true;
                }
            }
        } else if ($place_order == 1) {
            $this->_helper->content->setEnabled();

            $this->view->loggedoutViewerCheckout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.loggedoutviewercheckout', 1);

            // IF THERE IS NO COUNTRY AVAILABLE FOR SHIPPING
            $region_enable = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->isAnyCountryEnable();
            if (empty($region_enable)) {
                Zend_Registry::set('sitestoreproduct_checkout_no_region_enable', true);
                $this->view->sitestoreproduct_checkout_no_region_enable = true;
                return;
            }

            // CHECK ENABLE PAYMENT GATEWAYS WHEN DOWNPAYMENT IS NOT ENABLED
            $isOnlyCodGatewayEnable = false;
            $this->view->isDownPaymentEnable=$isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
            if (empty($isDownPaymentEnable)) {
                // DIRECT PAYMENT TO SELLER ENABLED
                if (empty($isPaymentToSiteEnable)) {
                    $storeEnabledgateway = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($checkout_store_id, 'store_gateway');
                    if (!empty($storeEnabledgateway)) {
                        $paymentMethod = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.method', 'normal');
                        if ($paymentMethod == 'split') {
                            $siteAdminEnablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.split.gateway', array());
                        } else if ($paymentMethod == 'escrow') {
                            $siteAdminEnablePaymentGateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.escrow.gateway', array());
                        } else {
                            $siteAdminEnablePaymentGateway = Zend_Json_Decoder::decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.gateway', Zend_Json_Encoder::encode(array(0, 1, 2))));
                        }


                        $storeEnabledgateway = Zend_Json_Decoder::decode($storeEnabledgateway);
                        foreach ($storeEnabledgateway as $gatewayName => $gatewayTableId) {
                            if ($gatewayName == 'paypal') {
                                $tempGatewayId = 0;
                            } else if ($gatewayName == 'cheque') {
                                $tempGatewayId = 1;
                            } else if ($gatewayName == 'cod') {
                                $tempGatewayId = 2;
                            } elseif (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                                $tempGatewayId = $gatewayName;
                            }
                            if ($tempGatewayId == 1 || $tempGatewayId == 2) {
                                if (in_array($tempGatewayId, $siteAdminEnablePaymentGateway)) {
                                    $finalStoreEnableGateway[] = $gatewayName;
                                } else {
                                    if ($tempGatewayId == 1) {
                                        $tempGatewayId = "cheque";
                                    } else {
                                        $tempGatewayId = "cash";
                                    }
                                    if (in_array($tempGatewayId, $siteAdminEnablePaymentGateway)) {
                                        $finalStoreEnableGateway[] = $gatewayName;
                                    }
                                }
                            } else {
                                if (in_array($tempGatewayId, $siteAdminEnablePaymentGateway)) {
                                    $finalStoreEnableGateway[] = $gatewayName;
                                }
                            }
                        }

                        $this->view->payment_gateway = $finalStoreEnableGateway;
                        if (count($finalStoreEnableGateway) == 1 && in_array('cod', $finalStoreEnableGateway))
                            $isOnlyCodGatewayEnable = true;
                    }

                    // IF NO PAYMENT GATEWAY ENABLE
                    if (empty($storeEnabledgateway) || empty($finalStoreEnableGateway))
                        $no_payment_gateway_enable = true;

                    if (isset($storeEnabledgateway['cheque']) && !empty($storeEnabledgateway['cheque']))
                        $this->view->storeChequeDetail = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->getStoreChequeDetail(array('store_id' => $checkout_store_id, "storegateway_id" => $storeEnabledgateway['cheque']));
                }
                else {
                    $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
                    $enable_gateway = $gateway_table->select()
                            ->from($gateway_table->info('name'), array('gateway_id', 'title', 'plugin'))
                            ->where('enabled = 1')
                            ->where('plugin not in(?)', array('Sitegateway_Plugin_Gateway_PayPalAdaptive', 'Sitegateway_Plugin_Gateway_MangoPay'))
                            ->query()
                            ->fetchAll();
                    $coreSettings = Engine_Api::_()->getApi('settings', 'core');


                    try {

                        $admin_payment_gateway = Zend_Json_Decoder::decode($coreSettings->getSetting('sitestore.admin.gateway', Zend_Json_Encoder::encode(array(0, 1))));
                    } catch (Exception $ex) {
                        $admin_payment_gateway = $coreSettings->getSetting('sitestore.admin.gateway', Zend_Json_Encoder::encode(array(0, 1)));
                    }

                    if (!empty($admin_payment_gateway)) {
                        foreach ($admin_payment_gateway as $payment_gateway) {
                            if (empty($payment_gateway)) {
                                $this->view->by_cheque_enable = true;
                                $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
                            } else if ($payment_gateway == 1) {
                                $this->view->cod_enable = true;
                            }
                        }
                    }

                    if (empty($enable_gateway) && !empty($admin_payment_gateway) && empty($this->view->by_cheque_enable) && !empty($this->view->cod_enable)) {
                        $isOnlyCodGatewayEnable = true;
                    }
                    // IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
                    if (empty($enable_gateway) && empty($admin_payment_gateway)) {
                        $no_payment_gateway_enable = true;
                    }

                    $this->view->payment_gateway = $enable_gateway;
                }
            } else {
                // DIRECT PAYMENT MODE
                if (empty($isPaymentToSiteEnable)) {
                    $storeEnabledgateway = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->getStoreEnabledGateway(array('store_id' => $checkout_store_id, 'gateway_type' => 1));
                    if (!empty($storeEnabledgateway)) {
                        $siteAdminEnablePaymentGateway = Zend_Json_Decoder::decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.gateway', Zend_Json_Encoder::encode(array(0, 1, 2))));
                        foreach ($storeEnabledgateway as $enabledGatewayName) {
                            if ($enabledGatewayName == 'PayPal') {
                                $tempGatewayId = 0;
                                $gatewayName = 'paypal';
                            } else if ($enabledGatewayName == 'ByCheque') {
                                $tempGatewayId = 1;
                                $gatewayName = 'cheque';
                            } else if ($enabledGatewayName == 'COD') {
                                $tempGatewayId = 2;
                                $gatewayName = 'cod';
                            }
                            if (in_array($tempGatewayId, $siteAdminEnablePaymentGateway)) {
                                $finalStoreEnableGateway[] = $gatewayName;
                            }
                        }
                        $this->view->payment_gateway = $finalStoreEnableGateway;
                        if (count($finalStoreEnableGateway) == 1 && in_array('cod', $finalStoreEnableGateway))
                            $isOnlyCodGatewayEnable = true;
                    }

                    // IF NO PAYMENT GATEWAY ENABLE
                    if (empty($storeEnabledgateway) || empty($finalStoreEnableGateway)) {
                        $no_payment_gateway_enable = true;
                    }
                } else {
                    $this->view->payment_gateway = $enable_gateway = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.defaultpaymentgateway', serialize(array('paypal', 'cheque', 'cod'))));
                    if (count($enable_gateway) == 1 && in_array('cod', $enable_gateway))
                        $isOnlyCodGatewayEnable = true;
                }
            }

            if (empty($sitestoreproduct_checkout_viewer_cart)) {
                Zend_Registry::set('sitestoreproduct_checkout_viewer_cart_empty', true);
                $this->view->sitestoreproduct_checkout_viewer_cart_empty = true;
                return;
            }

            if (empty($viewer_id)) {
                $this->view->sitestoreproduct_checkout_flag = 1;
                $place_order = 1;
            } else {
                $this->view->sitestoreproduct_logged_in_viewer = 1;
                $place_order = 2;
            }

            /* Start Coupon Code Work */
            $coupon_session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
            if (!empty($coupon_session->sitestoreproductCartCouponDetail)) {
                $couponDetail = unserialize($coupon_session->sitestoreproductCartCouponDetail);
                $coupon_store_id = array();
                $coupon_amount = 0;
            }
            /* End Coupon Code Work */

            $temp_store_id = false;
            $cartProductPaymentType = true;
            $totalOrderPrice = $tempIndex = 0;
            $cart_products_detail = $stores_products = $checkout_store_name = array();
            $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
            $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);
            $vatTitle = $this->view->translate('VAT');
            if (!empty($isVatAllow)) {
                $taxTable = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct');
                $taxRateTable = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct');
                $vatCreator = false; //Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.product.vat.creator', 0);
                if (!empty($vatCreator)) {
                    $adminVatDetail = $taxTable->fetchRow(array('store_id = ?' => 0, 'is_vat = ?' => 1));
                    if (!empty($adminVatDetail)) {
                        $vatTitle = $adminVatDetail->title;
                        $adminVatRateDetail = $taxRateTable->fetchRow(array('tax_id = ?' => $adminVatDetail->tax_id));
                    }
                }
            }

            foreach ($sitestoreproduct_checkout_viewer_cart as $value) {

                $productPricesArray = array();
                if (!empty($isVatAllow)) {
                    $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $value['product_id']);
                    $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj);
                    $value['price'] = $productPricesArray['product_price'];
                    $value['discount_amount'] = $productPricesArray['discount'];
                }

                $product_configuration = $product_vat_detail = array();

                $isSellingAllowedProducts = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($value['store_id']);
                $online_payment_threshold = Engine_Api::_()->sitestoreproduct()->getOnlinePaymentThreshold($value['store_id']);
                if (empty($tempIndex) && empty($isPaymentToSiteEnable)) {
                    $tempIndex = 1;
                    $cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($value['product_id']);
                }

                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
                    if (empty($isSellingAllowedProducts) || !empty($value['closed']) || !empty($value['draft']) || empty($value['search']) || empty($value['approved']) || $value['start_date'] > date('Y-m-d H:i:s') || ( $value['end_date'] < date('Y-m-d H:i:s') && !empty($value['end_date_enable']) )) {
                        return $this->_helper->redirector->gotoRoute(array('action' => 'cart'), 'sitestoreproduct_product_general', true);
                    }
                } else {
                    if (empty($isSellingAllowedProducts) || !empty($value['draft']) || empty($value['search']) || empty($value['approved']) || $value['start_date'] > date('Y-m-d H:i:s') || ( $value['end_date'] < date('Y-m-d H:i:s') && !empty($value['end_date_enable']) )) {
                        return $this->_helper->redirector->gotoRoute(array('action' => 'cart'), 'sitestoreproduct_product_general', true);
                    }
                }

                if (empty($viewer_id)) {
                    if (isset($tempUserCart[$value['product_id']]['config']) && ($tempUserCart[$value['product_id']]['type'] == 'configurable' || $tempUserCart[$value['product_id']]['type'] == 'virtual')) {
                        $value['quantity'] = 0;
                        $product_configuration['config'] = $tempUserCart[$value['product_id']]['config'];

                        foreach ($tempUserCart[$value['product_id']]['config'] as $config_item) {
                            $value['quantity'] += $config_item['quantity'];
                        }
                    } else {
                        $value['quantity'] = $tempUserCart[$value['product_id']]['quantity'];
                    }

                    // FOR A PRODUCT, IF PRODUCT QUANTITY IS ZERO THEN REDIRECT TO MANAGE CART STORE
                    if (empty($value['quantity'])) {
                        return $this->_helper->redirector->gotoRoute(array('action' => 'cart'), 'sitestoreproduct_product_general', true);
                    }
                }

                if (!empty($viewer_id) && ($value['product_type'] == 'configurable' || $value['product_type'] == 'virtual')) {
                    $config_product_quantity = 0;
                    $cartproducts_id = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getConfiguration(array("quantity", "cartproduct_id"), $value['product_id'], $value['cart_id']);
                    foreach ($cartproducts_id as $cartproduct_id) {
                        $product_configuration['config'][] = $cartproduct_id['cartproduct_id'];
                        $config_product_quantity += $cartproduct_id['quantity'];
                    }
                    $value['quantity'] = $config_product_quantity;
                }

                // CHECK PRODUCT IS IN STOCK OR NOT
                if ((!empty($value['stock_unlimited']) || $value['quantity'] <= $value['in_stock']) &&
                        (empty($value['max_order_quantity']) || $value['quantity'] <= $value['max_order_quantity']) &&
                        $value['quantity'] >= $value['min_order_quantity']) {
                    // CHECK DISCOUNT
                    if (!empty($value['price']) &&
                            !empty($value['discount']) &&
                            (@strtotime($value['discount_start_date']) <= @time()) &&
                            (!empty($value['discount_permanant']) || (@time() < @strtotime($value['discount_end_date']))) &&
                            (empty($value['user_type']) || ($value['user_type'] == 1 && empty($viewer_id)) || ($value['user_type'] == 2 && !empty($viewer_id)))) {
//            if(!empty($isVatAllow) && !empty($productPricesArray)){
//              $value['price'] = $productPricesArray['product_price_after_discount'];
//            }else{
                        $value['price'] = $value['price'] - $value['discount_amount'];
//            }
                    }
                    $product_sub_total['sub_total'] = $productTotalPrice = @round($value['price'] * $value['quantity'], 2);

                    if (!empty($isVatAllow)) {
                        $product_sub_total['display_sub_total'] = @round($productPricesArray['display_product_price'] * $value['quantity'], 2);
                    }
                } else {
                    return $this->_helper->redirector->gotoRoute(array('action' => 'cart'), 'sitestoreproduct_product_general', true);
                }

                if (!empty($isVatAllow)) {
//          if( !empty($vatCreator) && !empty($adminVatRateDetail) ) {
//            if( empty($adminVatRateDetail->handling_type) ) {
//              $productVatAmount = @round($adminVatRateDetail->tax_value, 2);
//            } else {
//              $productVatAmount = @round($adminVatRateDetail->tax_value * $productTotalPrice / 100, 2);
//            }
//          } else if( empty($vatCreator) ) {
//            $storeVatDetail = $taxTable->fetchRow(array('store_id = ?' => $value['store_id'], 'is_vat = ?' => 1));
//            if( !empty($storeVatDetail) ) {
//              $vatTitle = $storeVatDetail->title;
//              $storeVatRateDetail = $taxRateTable->fetchRow(array('tax_id = ?' => $storeVatDetail->tax_id));
//              if( !empty($storeVatRateDetail) ) {
//                if( empty($storeVatRateDetail->handling_type) ) {
//                  $productVatAmount = @round($storeVatRateDetail->tax_value, 2);
//                } else {
//                  $productVatAmount = @round($storeVatRateDetail->tax_value * $productTotalPrice / 100, 2);
//                }
//              }
//            }
//          }
                    $productVatAmount = $productPricesArray['vat'];
                    if (!empty($productVatAmount)) {
                        $product_vat_detail['vat_amount'] = @round($productVatAmount, 2);
                        $product_vat_detail['vat_title'] = $vatTitle;
                        $product_vat_detail['vat_creator'] = $vatCreator;
                    }
                }

                $cart_products_detail[$value['product_id']] = array_merge(array(
                    'product_id' => $value['product_id'],
                    'store_id' => $value['store_id'],
                    'quantity' => $value['quantity'],
                    'product_type' => $value['product_type'],
                    'price' => $value['price'],
                    'weight' => $value['weight'],
                    'store_tax_id' => $value['user_tax'],
                        ), $product_sub_total, $product_configuration, $product_vat_detail);

                // FOR DOWNPAYMENT
                if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) {
                    $downPaymentAmount = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $value['product_id'], 'price' => $value['price']));

                    $cart_products_detail[$value['product_id']]['downpayment'] = $downPaymentAmount;
                    $cart_products_detail[$value['product_id']]['downpayment_subtotal'] = $value['quantity'] * $downPaymentAmount;
                }

                // ONLINE PAYMENT THRESHOLD CONDITION
                if (empty($isDownPaymentEnable) || empty($cartProductPaymentType)) {
                    if (!empty($online_payment_threshold) && ($online_payment_threshold < $productTotalPrice)) {
                        $this->view->isNotAllowedOnlinePayment = true;
                    }
                } elseif (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) {
                    if (!empty($online_payment_threshold) && ($online_payment_threshold < ($value['quantity'] * $downPaymentAmount))) {
                        $this->view->isNotAllowedOnlinePayment = true;
                    }
                }

                $totalOrderPrice += $cart_products_detail[$value['product_id']]['sub_total'];

                // CONDITIONS ACCORDING TO DIFFERENT-DIFFERENT PRODUCT TYPES
                if ($value['product_type'] == 'downloadable') {
                    $this->view->sitestoreproduct_downloadable_product = $sitestoreproduct_downloadable_product = true;
                } else if ($value['product_type'] == 'virtual') {
                    $virtualProductOptions = unserialize($value['product_info']);
                    if (!empty($virtualProductOptions['virtual_product_price_range']) && $virtualProductOptions['virtual_product_price_range'] != 'fixed')
                        $cart_products_detail[$value['product_id']]['price_range_text'] = Engine_Api::_()->sitestoreproduct()->getProductPriceRangeText($virtualProductOptions['virtual_product_price_range']);

                    $this->view->sitestoreproduct_virtual_product = $sitestoreproduct_virtual_product = true;
                } else if ($value['product_type'] == 'bundled') {
                    $bundleProductInfo = @unserialize($value['product_info']);
                    if (!empty($bundleProductInfo) && empty($bundleProductInfo['enable_shipping']))
                        $this->view->sitestoreproduct_virtual_product = $sitestoreproduct_virtual_product = true;
                    else {
                        Zend_Registry::set('sitestoreproduct_other_product_type', true);
                        $store_product_types[$value['store_id']] = $value['product_type'];
                        $this->view->sitestoreproduct_other_product_type = $sitestoreproduct_other_product_type = true;
                    }
                } else {
                    Zend_Registry::set('sitestoreproduct_other_product_type', true);
                    $store_product_types[$value['store_id']] = $value['product_type'];
                    $this->view->sitestoreproduct_other_product_type = $sitestoreproduct_other_product_type = true;
                }

                $store_id = $value['store_id'];
                $stores_products[$store_id][] = $value['product_id'];

                if ($temp_store_id != $store_id) {
                    $temp_store_id = $store_id;
                    $store_name = $storeTable->getStoreName($store_id);

                    $checkout_store_name[$store_id] = str_replace("'", "::@::", $store_name);

                    if (!empty($couponDetail) && isset($couponDetail[$store_id]) && !empty($couponDetail[$store_id])) {
                        $coupon_amount += $couponDetail[$store_id]['coupon_amount'];
                        $coupon_store_id[] = $store_id;
                    }
                }
            }

            if (!empty($coupon_store_id))
                $this->view->coupon_store_id = serialize($coupon_store_id);

            if (!empty($coupon_amount) && ($totalOrderPrice <= $coupon_amount))
                $totalOrderPrice = 0;

            if (empty($totalOrderPrice))
                $this->view->totalOrderPriceFree = true;

            if ((!empty($totalOrderPrice) && !empty($no_payment_gateway_enable)) || (empty($sitestoreproduct_other_product_type) && empty($sitestoreproduct_virtual_product) && !empty($isOnlyCodGatewayEnable))) {
                if (empty($isPaymentToSiteEnable)) {
                    Zend_Registry::set('sitestoreproduct_checkout_store_no_payment_gateway_enable', true);
                    $this->view->sitestoreproduct_checkout_store_no_payment_gateway_enable = true;
                } else {
                    Zend_Registry::set('sitestoreproduct_checkout_no_payment_gateway_enable', true);
                    $this->view->sitestoreproduct_checkout_no_payment_gateway_enable = true;
                }
                return;
            }

            if (!empty($store_product_types)) {
                $this->view->store_product_types = @serialize($store_product_types);
            }

            $this->view->stores_products = @serialize($stores_products);
            $this->view->checkout_store_name = @serialize($checkout_store_name);
            $this->view->cart_products_detail = str_replace("'", "::?::", @serialize($cart_products_detail));
            $showShippingAddress = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.virtual.product.shipping', 1);

            if ((!empty($sitestoreproduct_virtual_product) && !empty($showShippingAddress)) || !empty($sitestoreproduct_other_product_type)) {
                $showShippingAddress = 1;
            } else {
                $showShippingAddress = 0;
            }

            $form = new Sitestoreproduct_Form_Addresses(array('viewerId' => $viewer_id, 'showShipping' => $showShippingAddress));

            //REMOVING SUBMIT BUTTON FROM FORM
            $form->removeElement('submit');
            $form->removeDisplayGroup('buttons');
            $this->view->form = $form;

            if (!empty($viewer_id)) {
                $params['owner_id'] = $viewer_id;
                $addressObj = Engine_Api::_()->getDbtable('addresses', 'sitestoreproduct');
                $addressResult = $addressObj->getAddress($params);
            }

            //FLAGS FOR BILLING/SHIPPING ADDRESS EXIST : 1 OR NOT : 0
            $shippingAddId = $billingAddId = 0;
            $this->view->is_populate = 0;
            $this->view->flag_same_address = true;

            if (!empty($viewer_id)) {
                foreach ($addressResult as $address) {
                    //IF BILLING ADDRESS (TYPE = 0) ALREADY EXIST IN TABLE THEN POPULATE
                    if (empty($address->type)) {
                        $addressTempArray = $address->toArray();
                        $addressArray['f_name_billing'] = $addressTempArray['f_name'];
                        $addressArray['l_name_billing'] = $addressTempArray['l_name'];
                        $addressArray['email_billing'] = $addressTempArray['email'];
                        $addressArray['phone_billing'] = $addressTempArray['phone'];
                        $addressArray['country_billing'] = $addressTempArray['country'];
                        $addressArray['city_billing'] = $addressTempArray['city'];
                        $addressArray['locality_billing'] = $addressTempArray['locality'];
                        $addressArray['zip_billing'] = $addressTempArray['zip'];
                        $addressArray['address_billing'] = $addressTempArray['address'];

                        $this->view->billingRegionId = $addressTempArray['state'];
                        $billingAddId = $address->address_id;
                        $this->view->flag_same_address = $addressTempArray['common'];

                        //GETTING BILLING COUNTRY REGIONS
                        $params = array();
                        $params['country'] = $addressTempArray['country'];
                        $shippingCountries = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getRegionsByName($params);

                        $countryArray = array();
                        foreach ($shippingCountries as $shippingCountry) {
                            $countryArray[$shippingCountry['region_id']] = $shippingCountry['region'];
                        }

                        $getBillingCredentioal = @serialize(array("region" => $countryArray, "value" => $addressTempArray['state']));
                        $this->_setParam("getBillingCredentioal", $getBillingCredentioal);
                    } else {
                        $addressTempArray = $address->toArray();
                        $addressArray['f_name_shipping'] = $addressTempArray['f_name'];
                        $addressArray['l_name_shipping'] = $addressTempArray['l_name'];
                        $addressArray['email_shipping'] = $addressTempArray['email'];
                        $addressArray['phone_shipping'] = $addressTempArray['phone'];
                        $addressArray['country_shipping'] = $addressTempArray['country'];
                        $addressArray['city_shipping'] = $addressTempArray['city'];
                        $addressArray['locality_shipping'] = $addressTempArray['locality'];
                        $addressArray['zip_shipping'] = $addressTempArray['zip'];
                        $addressArray['address_shipping'] = $addressTempArray['address'];
                        $shippingAddId = $address->address_id;
                        $this->view->shippingRegionId = $addressTempArray['state'];

                        //GETTING SHIPPING COUNTRY REGIONS
                        $params = array();
                        $params['country'] = $addressTempArray['country'];
                        $shippingCountries = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getRegionsByName($params);

                        $countryArray = array();
                        $tempShippingRegionFlag = false;
                        foreach ($shippingCountries as $shippingCountry) {
                            if (empty($shippingCountry['region']))
                                $tempShippingRegionFlag = true;
                            $countryArray[$shippingCountry['region_id']] = $shippingCountry['region'];
                        }

                        $this->view->tempShippingRegionFlag = $tempShippingRegionFlag;
                        $getShippingingCredentioal = @serialize(array("region" => $countryArray, "value" => $addressTempArray['state']));
                        $this->_setParam("getShippingingCredentioal", $getShippingingCredentioal);
                    }
                }
            }

            if (!empty($shippingAddId)) {
                $this->view->is_populate = 1;
                $form->populate($addressArray);
            }

            //BILLING AND SHIPPING ADDRESS ID
            $this->view->billingAddId = $billingAddId;
            $this->view->shippingAddId = $shippingAddId;
        }

        // MANAGE PLACE OF CHECKOUT PROCESS
        switch ($place_order) {
            case 1:
                break;

            case 2:
                if (!empty($_POST['checkout_process'])) {
                    $this->view->checkout_process = $_POST['checkout_process'];
                }

                break;

            case 3:
                $address = array();
                $temp_address = array();
                $temp_address = explode(',', $_POST['param']);
                $address['shipping_region_id'] = $temp_address[0];
                $address['shipping_country'] = $temp_address[1];
                $address['billing_region_id'] = $temp_address[2];
                $address['billing_country'] = $temp_address[3];

                if (!empty($_POST['other_product_type'])) {
                    $enable_shipping_methods = $shipping_methods = array();

                    $stores = @unserialize($_POST['stores_products']);
                    $store_product_types = @unserialize($_POST['store_product_types']);
                    $shipping_method_obj = Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct');

                    foreach ($stores as $key => $value) {
                        $shippingProductTotal = array();
                        $shippingProductTotal['total_price'] = $shippingProductTotal['total_weight'] = $shippingProductTotal['total_quantity'] = 0;
                        foreach ($checkout_cart as $productId => $info) {
                            if ($info['store_id'] == $key) {
                                $noBundleProductShipping = false;
                                if ($info['product_type'] == 'bundled') {
                                    $tempBundleProductInfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($productId, "product_info");
                                    $bundleProductInfo = @unserialize($tempBundleProductInfo);
                                    if (!empty($bundleProductInfo) && empty($bundleProductInfo['enable_shipping']))
                                        $noBundleProductShipping = true;
                                }

                                if ($info['product_type'] != 'downloadable' && empty($noBundleProductShipping)) {
                                    $shippingProductTotal['total_price'] += $info['sub_total'];
                                    $shippingProductTotal['total_weight'] += $info['weight'] * $info['quantity'];
                                    $shippingProductTotal['total_quantity'] += $info['quantity'];
                                    $shippingProductTotal['shipping_region_id'] = $temp_address[0];
                                    $shippingProductTotal['shipping_country'] = $temp_address[1];
                                    $shippingProductTotal['store_id'] = $key;
                                }
                            }
                        }

                        if (!empty($store_product_types[$key])) {
                            $shipping_methods = $shipping_method_obj->getCheckoutShippingMethods($shippingProductTotal);
                        }
                        $enable_shipping_methods[$key] = $shipping_methods;
                    }
                }

                $this->view->shipping_method = @serialize($enable_shipping_methods);
                $this->view->address = @serialize($address);
                $this->view->checkout_process = $_POST['checkout_process'];
                break;

            case 4:
                if (!empty($_POST['other_product_type'])) {
                    $method = explode(',', $_POST['param']);
                    $index = 0;

                    for ($temp_flag = 0; $temp_flag < (count($method) / 4); $temp_flag++) {
                        $shipping_methods[$method[$index]]['title'] = $method[$index + 1];
                        $shipping_methods[$method[$index]]['price'] = $method[$index + 2];
                        $shipping_methods[$method[$index]]['delivery_time'] = $method[$index + 3];
                        $index+= 4;
                    }
                    $checkout_process['shipping_methods'] = $shipping_methods;
                }
                $this->view->checkout_process = @serialize($checkout_process);
                break;

            case 5:
                if (($getPaymentType != $getPaymentAuth)) {
                    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.viewstore.sett', 0);
                    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.viewstore.type', 0);
                    $this->view->paymentRequest = true;
                }

                $coupon_session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
                if (!empty($coupon_session->sitestoreproductCartCouponDetail)) {
                    $this->view->couponDetail = unserialize($coupon_session->sitestoreproductCartCouponDetail);
                }

                if (empty($isPaymentToSiteEnable))
                    $this->view->checkoutStoreId = $checkout_store_id;

                $checkout_process = @unserialize($_POST['checkout_process']);
                if (!empty($viewer_id)) {
                    $this->view->viewer_id = true;
                }

                if (!empty($_POST['param'])) {
                    $payment_info = array();
                    $payment_information = array();
                    $payment_info = @explode(',', $_POST['param']);

                    if (($payment_info[0] != 3)) {
                        $payment_information['method'] = $payment_info[0];
                    } else {
                        $payment_information['method'] = $payment_info[0];
                        $payment_information['cheque_no'] = $payment_info[1];
                        $payment_information['signature'] = $payment_info[2];
                        $payment_information['account_no'] = $payment_info[3];
                        $payment_information['routing_no'] = $payment_info[4];
                    }
                    $checkout_process['payment_information'] = $payment_information;
                }

                $this->view->checkout_process = @serialize($checkout_process);
                break;
        }

        $this->view->sitestoreproduct_checkout_flag = $place_order;
    }

    function orderCommentAction() {
        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->_helper->layout->disableLayout();
        $order_id = $this->_getParam('order_id');
        $order_obj = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $order_obj->store_id);
        $notification_table = Engine_Api::_()->getDbtable('notifications', 'activity');
        $manage_admin_table = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');

        @parse_str($_POST['order_comment'], $order_comment);
        $comment_poster_type = $order_comment['user_type'];
        $newVar = _ENGINE_SSL ? 'https://' : 'http://';
        $store_name = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $sitestore->getHref() . '">' . $sitestore->getTitle() . '</a>';

        // IF COMMENT IS POSTED BY BUYER
        if (empty($comment_poster_type)) {
            $comment_poster_type = 0;
            $notify_buyer = 1;
            $notify_store_owner = 1;
            $notify_store_admin = 1;

            // EMAIL AND NOTIFICATION SEND TO ALL STORE ADMINS
            $getPageAdmins = $manage_admin_table->getManageAdmin($sitestore->store_id);
            $order_no = $this->view->htmlLink($this->view->url(array('action' => 'store', 'store_id' => $order_obj->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id), 'sitestore_store_dashboard', true), '#' . $order_id);
            if (!empty($getPageAdmins)) {
                foreach ($getPageAdmins as $pageAdmin) {
                    if (!empty($pageAdmin->sitestoreproduct_notification)) {
                        continue;
                    }

                    $user = Engine_Api::_()->getItem('user', $pageAdmin->user_id);
                    $notification_table->addNotification($user, $viewer, $order_obj, 'sitestoreproduct_order_comment_from_buyer', array('order_no' => $order_no, 'page' => array($sitestore->getType(), $sitestore->getIdentity())));

                    $order_no = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'store', 'store_id' => $order_obj->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id), 'sitestore_store_dashboard', true) . '">#' . $order_id . '</a>';
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'sitestoreproduct_order_comment_from_buyer', array(
                        'order_id' => '#' . $order_id,
                        'object_name' => $store_name,
                        'order_no' => $order_no,
                        'order_comment' => $order_comment['sitestoreproduct_order_comment_box_' . $comment_poster_type],
                        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'store', 'store_id' => $order_obj->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id), 'sitestore_store_dashboard', false),
                    ));
                }
            }
        } else {
            // EMAIL AND NOTIFICATION SEND TO BUYER
            if (isset($order_comment['notify_buyer']) && $order_comment['notify_buyer']) {
                $notify_buyer = 1;
                $sitestore = Engine_Api::_()->getItem('sitestore_store', $order_obj->store_id);

                if (empty($order_obj->buyer_id)) {
                    $billing_email_id = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getBillingEmailId($order_id);
                    $order_no = '#' . $order_id;
                } else {
                    $billing_email_id = $user = Engine_Api::_()->getItem('user', $order_obj->buyer_id);
                    $order_no = $this->view->htmlLink($this->view->url(array('action' => 'account', 'menuType' => 'my-orders', 'subMenuType' => 'order-view', 'orderId' => $order_id), 'sitestoreproduct_general', true), '#' . $order_id);
                    $notification_table->addNotification($user, $viewer, $order_obj, 'sitestoreproduct_order_comment_to_buyer', array('order_no' => $order_no, 'page' => array($sitestore->getType(), $sitestore->getIdentity())));
                    $order_no = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'account', 'menuType' => 'my-orders', 'subMenuType' => 'order-view', 'orderId' => $order_id), 'sitestoreproduct_general', true) . '">#' . $order_id . '</a>';
                }

                Engine_Api::_()->getApi('mail', 'core')->sendSystem($billing_email_id, 'sitestoreproduct_order_comment_to_member_buyer', array(
                    'order_id' => '#' . $order_id,
                    'order_no' => $order_no,
                    'object_name' => $store_name,
                    'order_comment' => $order_comment['sitestoreproduct_order_comment_box_' . $comment_poster_type],
                ));
            } else {
                $notify_buyer = 0;
            }

            // EMAIL AND NOTIFICATION SEND TO STORE ADMINS
            if (isset($order_comment['notify_store_admin']) && $order_comment['notify_store_admin']) {
                $notify_store_admin = 1;
                $notify_store_owner = 1;  // FOR STORE OWNER
                // SEND NOTIFICATION AND EAMIL TO ALL STORE ADMINS
                $getPageAdmins = $manage_admin_table->getManageAdmin($sitestore->store_id);
                $order_no = $this->view->htmlLink($this->view->url(array('action' => 'store', 'store_id' => $order_obj->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id), 'sitestore_store_dashboard', true), '#' . $order_id);
                if (!empty($getPageAdmins)) {
                    foreach ($getPageAdmins as $pageAdmin) {
                        if (!empty($pageAdmin->sitestoreproduct_notification)) {
                            continue;
                        }

                        $user = Engine_Api::_()->getItem('user', $pageAdmin->user_id);
                        $notification_table->addNotification($user, $viewer, $order_obj, 'sitestoreproduct_order_comment_to_store_admin', array('order_no' => $order_no, 'page' => array($sitestore->getType(), $sitestore->getIdentity())));

                        $order_no = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'store', 'store_id' => $order_obj->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id), 'sitestore_store_dashboard', true) . '">#' . $order_id . '</a>';
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'sitestoreproduct_order_comment_to_store_admin', array(
                            'order_id' => '#' . $order_id,
                            'order_no' => $order_no,
                            'order_comment' => $order_comment['sitestoreproduct_order_comment_box_' . $comment_poster_type],
                            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'store', 'store_id' => $order_obj->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id), 'sitestore_store_dashboard', false),
                        ));
                    }
                }
            } else if ($comment_poster_type == 1) {
                $notify_store_owner = 1;  // FOR STORE OWNER
                $notify_store_admin = 1;
            } else {
                $notify_store_owner = 0;  // FOR STORE OWNER
                $notify_store_admin = 0;
            }
        }

        $date = date('Y-m-d H:i:s');

        Engine_Api::_()->getDbtable('orderComments', 'sitestoreproduct')->insert(array(
            'order_id' => $order_id,
            'owner_id' => $viewer_id,
            'creation_date' => $date,
            'modified_date' => $date,
            'comment' => $order_comment['sitestoreproduct_order_comment_box_' . $comment_poster_type],
            'buyer_status' => $notify_buyer,
            'store_owner_status' => $notify_store_owner,
            'store_admin_status' => $notify_store_admin,
            'user_type' => $order_comment['user_type'],
        ));

        $this->view->user_type = $comment_poster_type;
        $this->view->comment_date = gmdate('M d,Y, g:i A', strtotime($date));
        $this->view->comment_text = $order_comment['sitestoreproduct_order_comment_box_' . $comment_poster_type];
    }

    public function showTooltipInfoAction() {
        $this->view->show_tax_type = $this->_getParam('show_tax_type');
        $this->view->tax = @unserialize($this->_getParam('tax'));
    }

    public function showMarkerInDate() {
        $localeObject = Zend_Registry::get('Locale');
        $dateLocaleString = $localeObject->getTranslation('long', 'Date', $localeObject);
        $dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
        $dateLocaleString = strtolower($dateLocaleString);
        $dateLocaleString = preg_replace('/[^ymd]/i', '', $dateLocaleString);
        $dateLocaleString = preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('y', 'm', 'd'), $dateLocaleString);
        $dateFormat = $dateLocaleString;
        return $dateFormat == "mdy" ? 1 : 0;
    }

    // CALLING FROM THE "VIEW ALL PRODUCTS" FROM THE ACTIVITY FEED.
    public function orderProductsAction() {
        $order_id = $this->_getParam('id');
        $order_id = (int) Engine_Api::_()->sitestoreproduct()->getEncodeToDecode($order_id);

        $products = array();
        $date = array();

        $product_ids = Engine_Api::_()->getDbtable("orderProducts", 'sitestoreproduct')->getOrderProductsDetail($order_id);

        foreach ($product_ids as $product_id) {
            $getProduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id['product_id']);

            if (empty($getProduct))
                continue;

            $products['products'] = $getProduct;
            $products['qty'] = $product_id['quantity'];
            $products['configuration'] = $product_id['configuration'];
            $data[] = $products;
        }
        $this->view->products = $data;
    }

    public function placeOrderAction() {

        // GET VIEWER
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $isPaymentToSiteEnable = true;
        $directPayment = 0;
        $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
        if (empty($isAdminDrivenStore)) {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
            if (empty($isPaymentToSiteEnable)) {
                $directPayment = 1;
            }
        }

        if (empty($isPaymentToSiteEnable)) {
            $checkout_store_id = $this->_getParam('store_id', null);
            if (empty($checkout_store_id)) {
                $this->view->return_sitestoreproduct_manage_cart = true;
                return;
            }
        }

        if (empty($viewer_id)) {
            $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');
            if (empty($session->sitestoreproduct_guest_user_cart)) {
                $this->view->return_sitestoreproduct_manage_cart = true;
                return;
            }

            $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);
            $product_ids = $sitestoreproduct_address = array();
            foreach ($tempUserCart as $product_id => $qty) {
                $product_ids[] = $product_id;
            }
            $product_ids = @implode(",", $product_ids);

            // IF DIRECT PAYMENT MODE IS ENABLED, THEN FETCH ONLY THAT STORE PRODUCTS
            if (empty($isPaymentToSiteEnable))
                $sitestoreproduct_checkout_viewer_cart = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getLoggedOutViewerCartDetail($product_ids, false, array('store_id' => $checkout_store_id));
            else
                $sitestoreproduct_checkout_viewer_cart = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getLoggedOutViewerCartDetail($product_ids);
        } else {
            $cart_obj = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->fetchRow(array('owner_id = ?' => $viewer_id));
            if (empty($cart_obj) || empty($cart_obj->cart_id)) {
                $this->view->return_sitestoreproduct_manage_cart = true;
                return;
            }
            // IF DIRECT PAYMENT MODE IS ENABLED, THEN FETCH ONLY THAT STORE PRODUCTS
            if (empty($isPaymentToSiteEnable))
                $sitestoreproduct_checkout_viewer_cart = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCheckoutViewerCart($cart_obj->cart_id, array('store_id' => $checkout_store_id));
            else
                $sitestoreproduct_checkout_viewer_cart = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getCheckoutViewerCart($cart_obj->cart_id);
        }

        $_POST['cart_products_detail'] = !empty($_POST['cart_products_exist']) ? $_POST['cart_products_exist'] : $_POST['cart_products_detail'];

        $checkout_process_product_details = @unserialize($_POST['cart_products_detail']);
        if (@COUNT($sitestoreproduct_checkout_viewer_cart) != @COUNT($checkout_process_product_details)) {
            $this->view->return_sitestoreproduct_manage_cart = true;
            return;
        }

        foreach ($sitestoreproduct_checkout_viewer_cart as $cart) {
            $product_id = $cart['product_id'];
            if (empty($viewer_id)) {
                if ($cart['product_type'] == 'configurable' || $cart['product_type'] == 'virtual') {
                    $cart['quantity'] = 0;
                    foreach ($tempUserCart[$product_id]['config'] as $config_item) {
                        $cart['quantity'] += $config_item['quantity'];
                    }
                } else {
                    $cart['quantity'] = $tempUserCart[$product_id]['quantity'];
                }
            }

            if (!empty($viewer_id) && ($cart['product_type'] == 'configurable' || $cart['product_type'] == 'virtual')) {
                $cartproducts_quantity = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getConfiguration(array("SUM(quantity) as quantity"), $cart['product_id'], $cart['cart_id']);
                $cart['quantity'] = $cartproducts_quantity[0]['quantity'];
            }

            // IF CHANGE IN VIEWER CART OR PRODUCT IS OUT OF STOCK THEN REDIRECT TO MANAGE CART
            if (( $product_id != $checkout_process_product_details[$product_id]['product_id'] ) ||
                    ( $cart['quantity'] != $checkout_process_product_details[$product_id]['quantity'] ) ||
                    ( $cart['in_stock'] < $checkout_process_product_details[$product_id]['quantity'] && empty($cart['stock_unlimited']) )) {
                $this->view->return_sitestoreproduct_manage_cart = true;
                return;
            }

            // IF PRODUCTS CONFIGURATION IS CHANGED THEN ALSO REDIRECT TO MANAGE CART
            if ($checkout_process_product_details[$product_id]['product_type'] == 'configurable' || $checkout_process_product_details[$product_id]['product_type'] == 'virtual') {
                if (isset($checkout_process_product_details[$product_id]['config']) && !empty($checkout_process_product_details[$product_id]['config'])) {
                    if (empty($viewer_id)) {
                        if (COUNT($tempUserCart[$product_id]['config']) != COUNT($checkout_process_product_details[$product_id]['config'])) {
                            echo 'return_to_cart';
                            die;
                        }

                        foreach ($checkout_process_product_details[$product_id]['config'] as $config_index => $config_item) {
                            if ($config_item != $tempUserCart[$product_id]['config'][$config_index]) {
                                echo 'return_to_cart';
                                die;
                            }
                        }
                    } else {
                        $viewer_cartproducts_id = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getConfigurationId($product_id, $cart['cart_id']);
                        if ($viewer_cartproducts_id != $checkout_process_product_details[$product_id]['config']) {
                            echo 'return_to_cart';
                            die;
                        }
                    }
                } else {
                    echo 'return_to_cart';
                    die;
                }

                // WORK FOR CONFIGURABLE PRODUCTS QUANTITY
                if (isset($checkout_process_product_details[$product_id]['config_info']) && !empty($checkout_process_product_details[$product_id]['config_info'])) {

                    foreach ($checkout_process_product_details[$product_id]['config_info'] as $index => $config_info) {
                        foreach ($config_info as $key => $config) {
                            if (isset($config['qty_unlimited']) && (empty($config['qty_unlimited']))) {
                                if ($config['qty_unlimited'] < $checkout_process_product_details[$product_id]['config_quantity'][$index]) {
                                    echo 'return_to_cart';
                                    die;
                                }
                            }
                            if (isset($config['combination_quantity'])) {
                                if ($config['combination_quantity'] < $checkout_process_product_details[$product_id]['config_quantity'][$index]) {
                                    echo 'return_to_cart';
                                    die;
                                }
                            }
                        }
                    }
                }
            }
        }

        $checkout_process = @unserialize($_POST['checkout_process']);
        $sitestoreproduct_downloadable_product = @unserialize($_POST['sitestoreproduct_downloadable_product']);
        @parse_str($_POST['sitestoreproduct_address'], $sitestoreproduct_address);
        $order_note = explode('::@::', $_POST['order_note']);
        for ($index = 0; $index < count($order_note);) {
            $store_id = $order_note[$index++];
            $sitestoreproduct_checkout_order_note[$store_id] = $order_note[$index++];
        }

        if (empty($checkout_process['shipping_methods']) && empty($sitestoreproduct_downloadable_product)) {
            $this->view->checkout_place_order_error = $this->view->translate('You must select a shipping method to ship your order.');
            return;
        }

        if (empty($checkout_process['payment_information'])) {
            $this->view->checkout_place_order_error = $this->view->translate('Must select a payment method for payment.');
            return;
        }

        if (empty($checkout_process) || empty($checkout_process_product_details) || empty($sitestoreproduct_address)) {
            $this->view->return_sitestoreproduct_manage_cart = true;
            return;
        }

        $temp_parent_id_exist = false;
        $order_table = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
        $order_product_table = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct');
        $order_address_table = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct');
        $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
        if (!empty($isDownPaymentEnable)) {
            $cartProductPaymentType = isset($_POST['cartProductPaymentType']) ? $_POST['cartProductPaymentType'] : false;
        } else {
            $cartProductPaymentType = false;
        }

        // PROCESS
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        // GET IP ADDRESS
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

        try {
            // PAYMENT VIA BY CHEQUE
            if ($checkout_process['payment_information']['method'] == 3) {
                Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->insert(array(
                    'cheque_no' => $checkout_process['payment_information']['cheque_no'],
                    'customer_signature' => $checkout_process['payment_information']['signature'],
                    'account_number' => $checkout_process['payment_information']['account_no'],
                    'bank_routing_number' => $checkout_process['payment_information']['routing_no'],
                ));
                $cheque_id = Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->getAdapter()->lastInsertId();
            }

            $productsTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
            $downloadableFilesTable = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct');
            $orderDownloadsTable = Engine_Api::_()->getDbtable('orderdownloads', 'sitestoreproduct');

            foreach ($checkout_process['payment'] as $key => $value) {
                $order_row = $order_table->createRow();

                // FETCH PARENT ID OF CURRENTLY PLACED ORDERS
                if (!empty($temp_parent_id_exist)) {
                    $order_row->parent_id = $parent_id;
                }

                if ($checkout_process['payment_information']['method'] == 3) {
                    $order_status = 0;  // APPROVAL PENDING
                    $payment_status = 'initial';
                } else if ($checkout_process['payment_information']['method'] == 5) {
                    $order_status = 2;  // PROCESSING
                    $payment_status = 'active';
                } else {
                    $order_status = 1;  // PAYMENT PENDING
                    $payment_status = 'initial';
                }

                $order_row->buyer_id = empty($viewer_id) ? '0' : $viewer_id;
                $order_row->store_id = $key;
                $order_row->creation_date = date('Y-m-d H:i:s');
                $order_row->order_status = $order_status;
                $order_row->item_count = $checkout_process[$key]['item_count'];
                $order_row->sub_total = @round($value['sub_total'], 2);
                $order_row->store_tax = @round($value['store_tax'], 2);
                $order_row->admin_tax = @round($value['admin_tax'], 2);
                $order_row->commission_type = $value['commission_type'];
                $order_row->commission_value = @round($value['commission_value'], 2);
                $order_row->commission_rate = @round($value['commission_rate'], 2);
                $order_row->shipping_price = @round($checkout_process['shipping_methods'][$key]['price'], 2);
                $order_row->delivery_time = $checkout_process['shipping_methods'][$key]['delivery_time'];
                $order_row->shipping_title = $checkout_process['shipping_methods'][$key]['title'];
                
                if(isset($_SESSION['cart_discount_value']))
                  $discountValue = $_SESSION['cart_discount_value'];
                $order_row->discount_value = $discountValue;
                $order_row->save();
                
                if(($checkout_process['payment_information']['method'] == 5) && $value['grand_total'] != 0){
                 if($checkout_process['payment_information']['method'] == 5){
                   $order_row->gateway_id = 50;
                   $user = $this->view->viewer();
                   $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                   $ebvaluestable = Engine_Api::_()->getDbtable('ebvalues', 'sescustomize');
                   $user->eb_count = $user->eb_count - @round($value['grand_total'], 2);
                   $ebvaluestable->insert(array(
                      'user_id' => $viewer_id,
                      'order_id' => $order_row->getIdentity(),
                      'total' => @round($value['grand_total'], 2),
                      'type'=>'redeem',
                      'creation_date' => date('Y-m-d H:i:s'),
                    ));
                    $ebValuesId = $db->lastInsertId();
                   $user->save();
                 }
                 //SES CUSTOM WORK
                  //START DATA UPDATION WORK IN POINTS TABLE
                  $userpoints = Engine_Api::_()->getApi('core', 'activitypoints')->getPoints($viewer_id);
                  
                  $remainingPoints = $userpoints['userpoints_count']-$discountValue;
                  $spentPoints = $userpoints['userpoints_totalspent']+$discountValue;
                  Engine_Api::_()->getDbtable('points', 'activitypoints')->update(array('userpoints_count' => $remainingPoints, 'userpoints_totalspent' => $spentPoints), array('userpoints_user_id =?' => $viewer_id));
                  //END DATA UPDATION WORK IN POINTS TABLE
                  $divided_amount = 1000;
                  $sub_total = $order_row->sub_total;
                  $total_point_basedontotalamout_paid = $sub_total / $divided_amount;
                  $total_BB = floor($total_point_basedontotalamout_paid);
                  $bridgesTable = Engine_Api::_()->getDbtable('bridges', 'sesbasic');
                  //START BUSINESS BRIDGES WORK
                  if($total_BB) {
                      $bridgesTable->insert(array(
                        'buyer_user_id' => $viewer_id,
                        'buyer_bb' => $total_BB,
                        'order_id' =>$order_row->getIdentity(),
                        'type' => 'business',
                        'creation_date' => new Zend_Db_Expr('NOW()'),
                      ));
                      $parentId = $db->lastInsertId();
                  }
                  
                  //END BUSINESS BRIDGES WORK
                if($level_id != 4){
                  //CB Point work
                  $inviterTable = Engine_Api::_()->getDbtable('invites', 'inviter');
                  $firstParentId = $inviterTable->select()->from($inviterTable->info('name'), 'user_id')->where('new_user_id = ?', $viewer_id)->query()->fetchColumn();
                  if($firstParentId) {
                      $total_CB = ($total_BB / 100) * 50; // 50% of BB Point is equal to CB
                      $bridgesTable->insert(array(
                      'buyer_user_id' => $firstParentId,
                      'user_id' => $viewer_id,
                      'buyer_cb' => $total_CB,
                      'order_id' =>$order_row->getIdentity(),
                      'parent_id' => $parentId,
                      'type' => 'collection',
                      'creation_date' => new Zend_Db_Expr('NOW()'),
                    ));
                    if($level_id == 10 || $level_id == 11 || $level_id == 12 || $level_id == 13 || $level_id == 14){
                      $parentId = $db->lastInsertId();
                      $secondParentId = $inviterTable->select()->from($inviterTable->info('name'), 'user_id')->where('new_user_id = ?', $firstParentId)->query()->fetchColumn();
                          if($secondParentId && $total_CB) {
                              $total_DB = ($total_CB / 100) * 10; // 10% of CB Point is equal to DB
                              $bridgesTable->insert(array(
                                'buyer_user_id' => $secondParentId,
                                'user_id' => $firstParentId,
                                'order_id' =>$order_row->getIdentity(),
                                'buyer_db' => $total_DB,
                                'parent_id' => $parentId,
                                'type' => 'direct',
                                'creation_date' => new Zend_Db_Expr('NOW()'),
                              ));
                          }
                    }
                  }
                } 
                  unset($_SESSION['cart_discount_value']);
                  //END SES CUSTOM WORK                  
                }
                else
                  $order_row->gateway_id = $checkout_process['payment_information']['method'];

                $order_row->grand_total = @round($value['grand_total'], 2);
                $order_row->payment_status = $payment_status;
                $order_row->ip_address = $ipExpr;

                $isSitecreditEnabled = Engine_Api::_()->hasModuleBootstrap('sitecredit');
                if($isSitecreditEnabled){
                $order_row->credit_point = empty($value['credit_points']) ? '0' : $value['credit_points'];
                $order_row->credit_value = empty($value['credit_amount']) ? '0' : $value['credit_amount'];
                }
                $order_row->order_note = $sitestoreproduct_checkout_order_note[$key];
                $order_row->is_private_order = $_POST['isPrivateOrder'];
                $order_row->direct_payment = $directPayment;
                if ($checkout_process['payment_information']['method'] == 3) {
                    $order_row->cheque_id = $cheque_id;
                }
                if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) {
                    $order_row->downpayment_total = @round($value['downpayment_total'], 2);
                    $order_row->is_downpayment = 1;
                }
                if (!empty($checkout_process[$key]['coupon_detail'])) {
                    $order_row->coupon_detail = $checkout_process[$key]['coupon_detail'];
                }

                if (Engine_Api::_()->hasModuleBootstrap('sitegateway') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegateway.stripeconnect', 0)) {
                    $stripeGatewayId = Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => 'Sitegateway_Plugin_Gateway_Stripe', 'columnName' => 'gateway_id'));
                    if ($stripeGatewayId == $order_row->gateway_id) {
                        $order_row->payment_split = 1;
                    }


                    $mangopayGatewayId = Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => 'Sitegateway_Plugin_Gateway_MangoPay', 'columnName' => 'gateway_id'));
                    $paypaladaptiveGatewayId = Engine_Api::_()->sitegateway()->getGatewayColumn(array('plugin' => 'Sitegateway_Plugin_Gateway_PayPalAdaptive', 'columnName' => 'gateway_id'));
                    if ($mangopayGatewayId == $order_row->gateway_id || $paypaladaptiveGatewayId == $order_row->gateway_id) {
                        $order_row->payment_split = 1;
                    }
                }
                $payout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.method', 'split');
                if (isset($order_row->gateway_type) && !empty($payout)) {
                    $order_row->gateway_type = $payout;
                }

                $order_id = $order_row->save();

                // Coupon SAVE work //
                $this->updateCouponUses($key);

                if (empty($temp_parent_id_exist)) {
                    $parent_id = $order_id;
                    $order_table->update(array('parent_id' => $order_id), array("order_id = ?" => $order_id));
                    $temp_parent_id_exist = TRUE;
                }

                // BILLING AND SHIPPING ADDRESS ARE DIFFERENT
                if (empty($sitestoreproduct_address['common'])) {
                    $order_address_table_row = $order_address_table->createRow();

                    $order_address_table_row->order_id = $order_id;
                    $order_address_table_row->owner_id = empty($viewer_id) ? '0' : $viewer_id;
                    $order_address_table_row->f_name = $sitestoreproduct_address['f_name_billing'];
                    $order_address_table_row->l_name = $sitestoreproduct_address['l_name_billing'];

                    if (empty($viewer_id))
                        $order_address_table_row->email = $sitestoreproduct_address['email_billing'];

                    $order_address_table_row->phone = $sitestoreproduct_address['phone_billing'];
                    $order_address_table_row->country = $sitestoreproduct_address['country_billing'];
                    $order_address_table_row->state = $sitestoreproduct_address['state_billing'];
                    $order_address_table_row->city = $sitestoreproduct_address['city_billing'];
                    $order_address_table_row->locality = $sitestoreproduct_address['locality_billing'];
                    $order_address_table_row->zip = $sitestoreproduct_address['zip_billing'];
                    $order_address_table_row->address = $sitestoreproduct_address['address_billing'];
                    $order_address_table_row->common = 0;
                    $order_address_table_row->type = 0;
                    $order_address_table_row->save();

                    if (isset($sitestoreproduct_address['common'])) {
                        $order_address_table_row = $order_address_table->createRow();
                        $order_address_table_row->order_id = $order_id;
                        $order_address_table_row->owner_id = empty($viewer_id) ? '0' : $viewer_id;
                        $order_address_table_row->f_name = $sitestoreproduct_address['f_name_shipping'];
                        $order_address_table_row->l_name = $sitestoreproduct_address['l_name_shipping'];
                        $order_address_table_row->phone = $sitestoreproduct_address['phone_shipping'];
                        $order_address_table_row->country = $sitestoreproduct_address['country_shipping'];
                        $order_address_table_row->state = $sitestoreproduct_address['state_shipping'];
                        $order_address_table_row->city = $sitestoreproduct_address['city_shipping'];
                        $order_address_table_row->locality = $sitestoreproduct_address['locality_shipping'];
                        $order_address_table_row->zip = $sitestoreproduct_address['zip_shipping'];
                        $order_address_table_row->address = $sitestoreproduct_address['address_shipping'];
                        $order_address_table_row->common = 0;
                        $order_address_table_row->type = 1;
                        $order_address_table_row->save();
                    }
                } else {
                    $index_flag = 0;
                    while ($index_flag < 2) {
                        $order_address_table_row = $order_address_table->createRow();
                        $order_address_table_row->order_id = $order_id;
                        $order_address_table_row->owner_id = empty($viewer_id) ? '0' : $viewer_id;
                        $order_address_table_row->f_name = $sitestoreproduct_address['f_name_billing'];
                        $order_address_table_row->l_name = $sitestoreproduct_address['l_name_billing'];

                        if (empty($viewer_id))
                            $order_address_table_row->email = $sitestoreproduct_address['email_billing'];

                        $order_address_table_row->phone = $sitestoreproduct_address['phone_billing'];
                        $order_address_table_row->country = $sitestoreproduct_address['country_billing'];
                        $order_address_table_row->state = $sitestoreproduct_address['state_billing'];
                        $order_address_table_row->city = $sitestoreproduct_address['city_billing'];
                        $order_address_table_row->locality = $sitestoreproduct_address['locality_billing'];
                        $order_address_table_row->zip = $sitestoreproduct_address['zip_billing'];
                        $order_address_table_row->address = $sitestoreproduct_address['address_billing'];
                        $order_address_table_row->common = 1;
                        $order_address_table_row->type = $index_flag;
                        $order_address_table_row->save();

                        $index_flag++;
                    }
                }

                // ENTER PRODUCT DETAILS IN ORDER_PRODUCTS TABLE
                foreach ($checkout_process_product_details as $value) {
                    if ($key == $value['store_id']) {
                        $languages_array = Engine_Api::_()->sitestoreproduct()->getLanguageArray();
                        $title_array = array();
                        foreach ($languages_array as $languageKey => $label) {
                            if ($languageKey == 'en') {
                                $title_array[] = 'title';
                                continue;
                            }

                            $title_column = "title_$languageKey";
                            $create_title_column = "`title_$languageKey`";

                            $db = Engine_Db_Table::getDefaultAdapter();

                            //CHECK COLUMNS ARE ALREADY EXISTS
                            $title_column_exist = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_products LIKE '$title_column'")->fetch();

                            if (!empty($title_column_exist)) {
                                $title_array[] = $title_column;
                            }
                        }
                        $productTitles = $productsTable->getProductAttribute($title_array, array('product_id' => $value['product_id']))->query()->fetchAll();

                        $productTitle = serialize($productTitles[0]);

                        $productSku = $productsTable->getProductAttribute('product_code', array('product_id' => $value['product_id']))->query()->fetchColumn();

                        if (isset($value['config']) && ( $value['product_type'] == 'configurable' || $value['product_type'] == 'virtual' )) {
                            foreach ($value['config_name'] as $index => $configuration_name) {
                                if (strstr($configuration_name, '*u'))
                                    $configuration_name = str_replace('*u', '\u', $configuration_name);
                                if ($value['product_type'] == 'virtual' && !empty($value['price_range_text']))
                                    $orderProductInfo = serialize(array('price_range_text' => $value['price_range_text']));
                                else
                                    $orderProductInfo = '';

                                if (isset($value['calendar_date'][$index]) && !empty($value['calendar_date'][$index])) {
                                    if (empty($orderProductInfo)) {
                                        $orderProductInfo = serialize(array('calendarDate' => $value['calendar_date'][$index]));
                                    } else {
                                        $tempOrderProductInfo = unserialize($orderProductInfo);
                                        $orderProductInfo = serialize(array_merge($tempOrderProductInfo, array('calendarDate' => $value['calendar_date'][$index])));
                                    }
                                }

                                $order_product_row = $order_product_table->createRow();
                                $order_product_row->order_id = $order_id;
                                $order_product_row->product_id = $value['product_id'];
                                $order_product_row->product_title = $productTitle;
                                $order_product_row->product_sku = empty($productSku) ? null : $productSku;
                                $order_product_row->price = @round($value['config_price'][$index], 2);
                                $order_product_row->quantity = $value['config_quantity'][$index];
                                $order_product_row->tax_title = isset($value['product_tax_title']) ? $value['product_tax_title'] : '';
                                $order_product_row->tax_amount = isset($value['product_tax_amount']) ? @round($value['product_tax_amount'], 2) : 0;
                                $order_product_row->configuration = $configuration_name;
                                $order_product_row->order_product_info = $orderProductInfo;
                                $order_product_row->config_info = @serialize($value['config_info'][$index]);
                                if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType))
                                    $order_product_row->downpayment = @round($value['config_downpayment'][$index], 2);
                                $order_product_row->save();
                            }
                            $quantity = $value['quantity'];
                        } else {
                            if ($value['product_type'] == 'virtual' && !empty($value['price_range_text']))
                                $orderProductInfo = serialize(array('price_range_text' => $value['price_range_text']));
                            else
                                $orderProductInfo = '';

                            $order_product_row = $order_product_table->createRow();
                            $quantity = $value['quantity'];
                            $order_product_row->order_id = $order_id;
                            $order_product_row->product_id = $value['product_id'];
                            $order_product_row->product_title = $productTitle;
                            $order_product_row->product_sku = empty($productSku) ? null : $productSku;
                            $order_product_row->price = @round($value['price'], 2);
                            $order_product_row->quantity = $quantity;
                            $order_product_row->tax_title = isset($value['product_tax_title']) ? $value['product_tax_title'] : '';
                            $order_product_row->tax_amount = isset($value['product_tax_amount']) ? @round($value['product_tax_amount'], 2) : 0;
                            $order_product_row->order_product_info = $orderProductInfo;
                            if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType))
                                $order_product_row->downpayment = @round($value['downpayment'], 2);
                            $order_product_row->save();
                        }

                        if (isset($value['downpayment']) && !empty($value['downpayment'])) {
                            $order_product_row->price = $value['price'];
                            $order_product_row->downpayment = $value['downpayment'];
                            $order_product_row->save();
                        }

                        if ($value['product_type'] == 'downloadable') {
                            $downloadableFiles = $downloadableFilesTable->getDownloadableFiles(array('product_id' => $value['product_id'], 'type' => 'main', 'status' => 1));

                            foreach ($downloadableFiles as $file) {
                                $fileValues = array();
                                $fileValues['store_id'] = $file['store_id'];
                                $fileValues['order_id'] = $order_id;
                                $fileValues['product_id'] = $value['product_id'];
                                $fileValues['downloadablefile_id'] = $file['downloadablefile_id'];
                                $fileValues['max_downloads'] = $file['download_limit'] * $value['quantity'];
                                $orderDownloadsRow = $orderDownloadsTable->createRow();
                                $orderDownloadsRow->setFromArray($fileValues);
                                $orderDownloadsRow->save();
                            }
                        }
                    }
                }
            }





            // COMMIT
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // DELETE VIEWER CART
        if (!empty($isAdminDrivenStore) || !empty($isPaymentToSiteEnable)) {
            if (empty($viewer_id)) {
                $session->sitestoreproduct_guest_user_cart = false;
            } else {
                $cartId = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getCartId($viewer_id);
                Engine_Api::_()->getItem('sitestoreproduct_cart', $cartId)->delete();
            }
        } else {
            if (empty($viewer_id)) {
                foreach ($tempUserCart as $product_id => $productAttribs) {
                    if ($productAttribs['store_id'] == $checkout_store_id) {
                        unset($tempUserCart[$product_id]);
                    }
                }
                $session->sitestoreproduct_guest_user_cart = @serialize($tempUserCart);
            } else {
                $cartId = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getCartId($viewer_id);
                $storecartProducts = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->getStoreCartProducts($cartId, $checkout_store_id);
                foreach ($storecartProducts as $cartProduct) {
                    Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartProduct->cartproduct_id)->delete();
                }
                Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct')->deleteCart($cartId);
            }
        }

        $coupon_session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');
        if (!empty($coupon_session->sitestoreproductCartCouponDetail)) {
            $coupon_session->sitestoreproductCartCouponDetail = null;
        }
         $credit_session = new Zend_Session_Namespace('sitestoreproduct_cart_credit');
        if (!empty($credit_session->sitestoreproductCartCreditDetail)) {
            $credit_session->sitestoreproductCartCreditDetail = null;
        } 
        $this->view->parent_id = Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($parent_id);
        $this->view->gateway_id = $checkout_process['payment_information']['method'];
    }

    public function paymentAction() {
        $gateway_id = $this->_getParam('gateway_id');
        $downpayment_make_payment = $this->_getParam('downpayment_make_payment', null);
        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();

        if (!empty($directPayment)) {
            $store_id = $this->_getParam('store_id', null);
            if (Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
                $validGatewayId = Engine_Api::_()->sitegateway()->getGatewayColumn(array('pluginLike' => 'Sitegateway_Plugin_Gateway_', 'columnName' => 'gateway_id', 'gateway_id' => $gateway_id));
                if (empty($store_id) || ($gateway_id != 2 && !$validGatewayId)) {
                    return $this->_forward('notfound', 'error', 'core');
                }
            } else {
                if (empty($store_id) || ($gateway_id != 2)) {
                    return $this->_forward('notfound', 'error', 'core');
                }
            }
        }

        $parent_order_id = $order_id = (int) Engine_Api::_()->sitestoreproduct()->getEncodeToDecode($this->_getParam('order_id'));
        if (empty($gateway_id) || empty($parent_order_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->_session = new Zend_Session_Namespace('Payment_Sitestoreproduct');
        $this->_session->unsetAll();
        $this->_session->user_order_id = $parent_order_id;

        if (!empty($downpayment_make_payment)) {
            $this->_session->downpayment_make_payment = $downpayment_make_payment;
        }

        if (!empty($directPayment)) {
            $this->_session->checkout_store_id = $store_id;
        }

        // IF PAYMENT GATEWAY IS 2CHECKOUT
        if ($gateway_id == 1) {
            $order = Engine_Api::_()->getItem('sitestoreproduct_order', $parent_order_id);
            $gateway = Engine_Api::_()->getItem('sitestoreproduct_usergateway', 1);

            // Get gateway plugin
            $gatewayPlugin = $gateway->getGateway();
            $gatewayPlugin->createProduct($order->getGatewayParams());
        }

        return $this->_forwardCustom('process', 'payment', 'sitestoreproduct', array());
    }

    public function makePaymentAction() {
        $session = new Zend_Session_Namespace('sitestoreproduct_make_payment');
        if (!empty($session->sitestoreproduct_orders_make_payment)) {
            unset($session->sitestoreproduct_orders_make_payment);
        }

        $order_id = $this->_getParam('order_id');
        $orderObj = Engine_Api::_()->getItem("sitestoreproduct_order", $order_id);
        $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');

        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

        // CHECK MAKE PAYMENT CONDITION
        if (empty($directPayment) && empty($isDownPaymentEnable) && empty($orderObj->is_downpayment) && empty($orderObj->direct_payment) && (($orderObj->gateway_id == 1 || $orderObj->gateway_id == 2) && $orderObj->payment_status != 'active')) {
            $this->view->makePayment = $orderMakePayment = true;
            // CHECK ORDER PRODUCT AVAILABILITY IN STOCK
            $orderProducts = Engine_Api::_()->getDbtable('OrderProducts', 'sitestoreproduct')->getOrderProducts($order_id);
            foreach ($orderProducts as $orderProduct) {
                if (empty($orderProduct->product_type) || empty($orderProduct->title)) {
                    $this->view->productDeleted = true;
                    return;
                }
                if (($orderProduct->quantity > $orderProduct->in_stock) && empty($orderProduct->stock_unlimited)) {
                    $this->view->outOfStock = true;
                    return;
                }
            }
        } elseif (!empty($isDownPaymentEnable) && $orderObj->is_downpayment == 1 && ( (($orderObj->gateway_id == 1 || $orderObj->gateway_id == 2) && $orderObj->payment_status == 'active') || $orderObj->gateway_id == 3 || $orderObj->gateway_id == 4 )) {
            $this->view->remainingAmountPayment = $orderRemainingAmountPayment = true;
            $this->view->amount_need_to_pay = round(($orderObj->sub_total - $orderObj->downpayment_total), 2);
        } else {
            $this->view->notMakePayment = true;
            return;
        }

        // CHECK AVAILABLE PAYMENT GATEWAY
        if (empty($directPayment)) {
            if (empty($isDownPaymentEnable)) {
                $gateway_table = Engine_Api::_()->getDbtable('gateways', 'payment');
                $site_enable_gateway = $gateway_table->select()
                                ->from($gateway_table->info('name'), array('gateway_id', 'title', 'plugin'))
                                ->where('enabled = 1')->query()->fetchAll();

                if (!empty($site_enable_gateway)) {
                    foreach ($site_enable_gateway as $gatewayDetail) {
                        if ($gatewayDetail['plugin'] === 'Payment_Plugin_Gateway_2Checkout')
                            $this->view->twoCheckoutEnable = true;
                        elseif ($gatewayDetail['plugin'] === 'Payment_Plugin_Gateway_PayPal')
                            $this->view->paypalEnable = true;
                    }
                }

                try {
                    $admin_payment_gateway = Zend_Json_Decoder::decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admin.gateway', Zend_Json_Encoder::encode(array(0, 1))));
                } catch (Exception $ex) {
                    $admin_payment_gateway = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admin.gateway', Zend_Json_Encoder::encode(array(0, 1)));
                }

                if (!empty($admin_payment_gateway)) {
                    foreach ($admin_payment_gateway as $payment_gateway) {
                        if (empty($payment_gateway)) {
                            $this->view->by_cheque_enable = true;
                            $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
                        } else if ($payment_gateway == 1) {
                            $this->view->cod_enable = true;
                        }
                    }
                }
            } else {
                $siteEnabledGateway = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.defaultpaymentgateway', serialize(array('paypal', 'cheque', 'cod'))));

                if (!empty($siteEnabledGateway)) {
                    foreach ($siteEnabledGateway as $gatewayName) {
                        if ($gatewayName == 'paypal')
                            $this->view->paypalEnable = true;
                        elseif ($gatewayName == 'cheque') {
                            $this->view->by_cheque_enable = true;
                            $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
                        } elseif ($gatewayName == 'cod')
                            $this->view->cod_enable = true;
                    }
                }
            }
        } elseif (!empty($directPayment) && !empty($isDownPaymentEnable) && !empty($orderObj->is_downpayment)) {
            $storeEnabledgateway = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->getStoreEnabledGateway(array('store_id' => $orderObj->store_id, 'gateway_type' => 2));
            $finalStoreEnableGateway = array();
            if (!empty($storeEnabledgateway)) {
                $siteAdminEnablePaymentGateway = Zend_Json_Decoder::decode(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allowed.payment.gateway', Zend_Json_Encoder::encode(array(0, 1, 2))));
                foreach ($storeEnabledgateway as $enabledGatewayName) {
                    if ($enabledGatewayName == 'PayPal') {
                        if (in_array(0, $siteAdminEnablePaymentGateway)) {
                            $this->view->paypalEnable = true;
                        }
                    } else if ($enabledGatewayName == 'ByCheque') {
                        if (in_array(1, $siteAdminEnablePaymentGateway)) {
                            $this->view->by_cheque_enable = true;
                        }
                    } else if ($enabledGatewayName == 'COD') {
                        if (in_array(2, $siteAdminEnablePaymentGateway)) {
                            $this->view->cod_enable = true;
                        }
                    }
                }
            }
        }

        if (empty($this->view->cod_enable) && empty($this->view->by_cheque_enable) && empty($this->view->paypalEnable) && empty($this->view->twoCheckoutEnable)) {
            if (!empty($orderMakePayment))
                $this->view->noPaymentGatewayEnable = true;
            else
                $this->view->noStorePaymentGatewayEnable = true;
            return;
        }

        // SUBMIT MAKE PAYMENT REQUEST
        if ($this->getRequest()->isPost()) {
            $cheque_id = 0;
            if ($_POST['payment_method'] == 3 || $_POST['payment_method'] == 4) {
                if ($_POST['payment_method'] == 3) {
                    Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->insert(array(
                        'cheque_no' => $_POST['cheque_no'],
                        'customer_signature' => $_POST['signature'],
                        'account_number' => $_POST['account_no'],
                        'bank_routing_number' => $_POST['routing_no'],
                    ));
                    $cheque_id = Engine_Api::_()->getDbtable('ordercheques', 'sitestoreproduct')->getAdapter()->lastInsertId();

                    if (!empty($orderMakePayment))
                        $orderTable->update(array('cheque_id' => $cheque_id), array('order_id =?' => $order_id));

                    $successMessage = "Your cheque's information has been saved successfully.";
                } else {
                    $successMessage = "";
                }
            } else {
                $successMessage = 'You will soon be redirected to selected payment gateway.';
            }

            // UPDATE INFORMATION FOR REMAINING AMOUNT PAYMENT
            if (!empty($orderRemainingAmountPayment)) {
                $remainingAmountGatewayId = Engine_Api::_()->getDbtable('orderdownpayments', 'sitestoreproduct')->isRemainingAmountRequestExist($order_id);
                if (empty($remainingAmountGatewayId)) {
                    Engine_Api::_()->getDbtable('orderdownpayments', 'sitestoreproduct')->insert(array(
                        'order_id' => $order_id,
                        'gateway_id' => $_POST['payment_method'],
                        'cheque_id' => $cheque_id,
                        'payment' => round(($orderObj->sub_total - $orderObj->downpayment_total), 2),
                        'payment_status' => 'initial',
                        'date' => new Zend_Db_Expr('NOW()'),
                    ));
                    $remainingAmountId = Engine_Api::_()->getDbtable('orderdownpayments', 'sitestoreproduct')->getAdapter()->lastInsertId();
                } else {
                    Engine_Api::_()->getDbtable('orderdownpayments', 'sitestoreproduct')->update(array(
                        'gateway_id' => $_POST['payment_method'],
                        'cheque_id' => $cheque_id,
                        'payment' => round(($orderObj->sub_total - $orderObj->downpayment_total), 2)), array('order_id =?' => $order_id));
                    $remainingAmountId = Engine_Api::_()->getDbtable('orderdownpayments', 'sitestoreproduct')->fetchRow(array('order_id =?' => $order_id))->orderdownpayment_id;
                }


                if ($_POST['payment_method'] == 3 || $_POST['payment_method'] == 4) {
                    $orderObj->is_downpayment = 2;
                    $orderObj->save();
                }
            }

            if ($_POST['payment_method'] == 3 || $_POST['payment_method'] == 4) {
                if (!empty($orderRemainingAmountPayment)) {
                    $redirectUrl = $this->view->url(array('action' => 'success', 'success_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($order_id), 'downpayment_make_payment' => $remainingAmountId), "sitestoreproduct_general", true);
                } else {
                    $redirectUrl = $this->view->url(array('action' => 'success', 'success_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($order_id)), "sitestoreproduct_general", true);
                }
            } else {
                if (!empty($orderRemainingAmountPayment)) {
                    if (empty($directPayment))
                        $redirectUrl = $this->view->url(array('action' => 'payment', 'gateway_id' => $_POST['payment_method'], 'order_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($order_id), 'downpayment_make_payment' => $remainingAmountId), "sitestoreproduct_general", true);
                    else
                        $redirectUrl = $this->view->url(array('action' => 'payment', 'gateway_id' => $_POST['payment_method'], 'store_id' => $orderObj->store_id, 'order_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($order_id), 'downpayment_make_payment' => $remainingAmountId), "sitestoreproduct_general", true);
                } else {
                    $redirectUrl = $this->view->url(array('action' => 'payment', 'gateway_id' => $_POST['payment_method'], 'order_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($order_id)), "sitestoreproduct_general", true);
                }
            }

            if (empty($directPayment) && empty($isDownPaymentEnable) && !empty($orderMakePayment))
                $orderTable->update(array('gateway_id' => $_POST['payment_method']), array('order_id =?' => $order_id));

            if (!empty($orderMakePayment)) {
                // UPDATE PARENT ID OF OTHER ORDERS, IF NEEDED
                $parent_id = $orderTable->getParentId($order_id);
                if ($order_id == $parent_id) {
                    $orderIds = $orderTable->getOrderIds($parent_id);

                    if (!empty($orderIds[1]['order_id'])) {
                        $new_parent_id = $orderIds[1]['order_id'];
                        $orderTable->update(array('parent_id' => $new_parent_id), array('parent_id =?' => $parent_id));
                    }
                }

                $orderTable->update(array('parent_id' => $order_id), array('order_id =?' => $order_id));
            }

            if (empty($session->sitestoreproduct_orders_make_payment)) {
                $session->sitestoreproduct_orders_make_payment = true;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefreshTime' => '10',
                'parentRedirect' => $redirectUrl,
                'format' => 'smoothbox',
                'messages' => Zend_Registry::get('Zend_Translate')->_($successMessage)
            ));
        }
    }

    public function orderCancelAction() {
        if ($this->getRequest()->isPost()) {
            $order_id = $this->_getParam('order_id');
            $admin_calling = $this->_getParam('admin_calling', null);
            $store_id = $this->_getParam('store_id', null);
            Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->update(array('order_status' => 6), array('order_id =?' => $order_id));

            $orderProducts = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct')->getOrderProducts($order_id, true);

            foreach ($orderProducts as $orderProduct) {
                Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->update(array("in_stock" => new Zend_Db_Expr("in_stock + $orderProduct->quantity")), array("product_id =?" => $orderProduct->product_id));
            }

            if (empty($admin_calling))
                $redirectUrl = $this->view->url(array('action' => 'store', 'store_id' => $store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id), 'sitestore_store_dashboard', true);
            else
                $redirectUrl = $this->view->url(array('module' => 'sitestoreproduct', 'controller' => 'manage', 'action' => 'manage-orders'), 'admin_default', true);

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'parentRedirect' => $redirectUrl,
                'parentRedirectTime' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Order canceled successfully.'))
            ));
        }
    }

    public function productTypeDetailsAction() {
        $this->view->productType = array('simple', 'configurable', 'virtual', 'grouped', 'bundled', 'downloadable');
//    $this->view->productType = $productTypes =  @unserialize($this->_getParam('product_types', null));
//    $this->view->productTypeCount = @count($productTypes);
        $this->_helper->layout->setLayout('default-simple');
    }

    public function adminChequeDetailAction() {
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
        $this->view->admin_cheque_detail = Engine_Api::_()->getApi('settings', 'core')->getSetting('send.cheque.to', null);
    }

    public function storeChequeDetailAction() {
        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
        $this->view->title = $store_id = $this->_getParam('title', null);
        $this->view->storeChequeDetail = Engine_Api::_()->getDbtable('sellergateways', 'sitestoreproduct')->getStoreChequeDetail(array('store_id' => $store_id));
    }

    public function nonPaymentOrderAction() {
        $order_id = $this->_getParam('order_id', null);
        $this->view->form = $form = new Sitestoreproduct_Form_Order_NonPayment();

        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        $order = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id);
        $order->non_payment_seller_reason = $values['non_payment_seller_reason'];
        $order->non_payment_seller_message = $values['non_payment_seller_message'];
        $order->save();

        // SEND MAIL TO SITE ADMIN FOR THIS PAYMENT REQUEST
        $admin_email_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact', null);

        if (!empty($admin_email_id)) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $order->store_id);
            $newVar = _ENGINE_SSL ? 'https://' : 'http://';
            $orderUrl = $newVar . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'store', 'store_id' => $order->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order->order_id), 'sitestore_store_dashboard', false);
            $order_no = '<a href="' . $orderUrl . '">#' . $order->order_id . '</a>';
            $store_name = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $sitestore->getHref() . '">' . $sitestore->getTitle() . '</a>';

            Engine_Api::_()->getApi('mail', 'core')->sendSystem($admin_email_id, 'sitestoreproduct_non_payment_order', array(
                'order_id' => '#' . $order->order_id,
                'order_no' => $order_no,
                'object_name' => $store_name,
                'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'store', 'store_id' => $order->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order->order_id), 'sitestore_store_dashboard', false),
            ));
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => true,
            'parentRedirect' => $this->view->url(array('action' => 'store', 'store_id' => $order->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order_id), 'sitestore_store_dashboard', true),
            'parentRedirectTime' => 10,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Order non-payment reported successfully.'))
        ));
    }

    public function sectionsAction() {

        $store_id = $this->_getParam('store_id', null);
        if (empty($store_id))
            return;

        $tempSectionArray = $sectionArray = array();
        $task = $this->_getParam('task', null);
        $view_product = $this->view->view_product = $this->_getParam('view_product', null);
        $this->view->show_sections = $this->_getParam('show_sections', 0);
        $this->view->sections_id = $section_id = $this->_getParam('sections', null);
        $this->view->store_id = $store_id;

        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
        if (empty($isManageAdmin)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }
        $this->view->canEdit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

        if ($task == "save") {
            $tableSections = Engine_Api::_()->getDbTable('sections', 'sitestoreproduct');
            $tableSectionsName = $tableSections->info('name');

            $is_new_section = (int) $this->_getParam('is_new_section', null); //$_GET['section_id'];
            $temp_section_title = $this->_getParam('section_title', null); //$_GET['section_title'];
            $temp_section_title = $this->view->string()->stripTags($temp_section_title);
            $section_title = Engine_Api::_()->sitestore()->parseString($_GET['section_title']);


            if ($section_title == "") {
                if (!empty($is_new_section)) {
                    if ($sec_dependency == 0) {
                        $tableSections->delete(array('section_id = ?' => $is_new_section));
                    }
                }
            } else {
                if (empty($is_new_section)) {
                    $row_info = $tableSections->fetchRow($tableSections->select()->from($tableSectionsName, 'max(sec_order) AS sec_order'));
                    $sec_order = $row_info->sec_order + 1;
                    $row = $tableSections->createRow();
                    $row->section_name = $temp_section_title;
                    $row->sec_order = $sec_order;
                    $row->store_id = $store_id;
                    $newsec_id = $row->save();
                } else {
                    $tableSections->update(array('section_name' => $temp_section_title), array('section_id = ?' => $is_new_section));
                    $newsec_id = $is_new_section;
                }
            }
        } else if ($task == "changeorder") {
            $tableSections = Engine_Api::_()->getDbTable('sections', 'sitestoreproduct');
            $sitestoreorder = $this->_getParam('sitestoreorder', null);
            for ($i = 0; $i < count($sitestoreorder); $i++) {
                $section_id = substr($sitestoreorder[$i], 4);
                $tableSections->update(array('sec_order' => $i + 1), array('section_id = ?' => $section_id));
            }
        }
        $this->view->getSectionObj = Engine_Api::_()->getDbTable('sections', 'sitestoreproduct')->getStoreSections($store_id);
        $this->view->getProductsBySectionObj = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductsBySection($section_id, $store_id);
        $this->view->sectionArray = $sections_info = Engine_Api::_()->getDbTable('sections', 'sitestoreproduct')->getSections($store_id);
        $this->view->totalProductCount = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductsCountInStore($store_id, 0);
    }

    public function saveSectionInfoAction() {
        $formValues = $this->_getParam('formValues', null);
        @parse_str($formValues, $formValues);
        foreach ($formValues as $key => $value) {
            if ($key == 'section_id')
                $section_id = $value;
            else
                $productsIdArray[] = $value;
        }
        $productCount = @COUNT($productsIdArray);
        if ($section_id != "change") {
            for ($tempCount = 0; $tempCount < $productCount; $tempCount++)
                Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->update(array('section_id' => $section_id), array('product_id = ?' => $productsIdArray[$tempCount]));
        }
        $this->_forward('sections', 'index', 'sitestoreproduct', array());
    }

    public function sectionDeleteAction() {
        $sectionId = $this->_getParam('sectionId');
        Engine_Api::_()->getDbtable('sections', 'sitestoreproduct')->delete(array('section_id = ?' => $sectionId));
        $this->_forward('sections', 'index', 'sitestoreproduct', array());
    }

    public function termsAndConditionsAction() {
        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');
        $this->view->store_id = $store_id = $this->_getParam('store_id', null);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $this->view->form = $form = new Sitestoreproduct_Form_TermsConditions();
        $this->view->sitestores_view_menu = 90;
        $storeTermsConditions = Engine_Api::_()->getDbtable('otherinfo', 'sitestore')->getStoreAttribs($store_id, 'terms_conditions');

        if (!$this->getRequest()->isPost()) {
            $form->populate(array('terms_conditions' => $storeTermsConditions));
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        if (empty($storeTermsConditions)) {
            Engine_Api::_()->getDbtable('otherinfo', 'sitestore')->insert(array(
                'store_id' => $store_id,
                'terms_conditions' => $values['terms_conditions'],
            ));
        } else {
            Engine_Api::_()->getDbtable('otherinfo', 'sitestore')->update(array('terms_conditions' => $values['terms_conditions']), array('store_id = ?' => $store_id));
        }
        $this->view->successTermsConditions = true;
    }

    public function termsConditionsDetailsAction() {
        $store_id = $this->_getParam('store_id', null);
        $this->view->termsConditions = Engine_Api::_()->getDbtable('otherinfo', 'sitestore')->getStoreAttribs($store_id, 'terms_conditions');
        $this->view->store_title = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($store_id, 'title');
    }

    private function updateCouponUses($store_id) {

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer'))
            return;
        $coupon_session = new Zend_Session_Namespace('sitestoreproduct_cart_coupon');

        if (!empty($coupon_session->sitestoreproductCartCouponDetail))
            $couponDetail = @unserialize($coupon_session->sitestoreproductCartCouponDetail);

        if (!empty($couponDetail) && isset($couponDetail[$store_id]) && !empty($couponDetail[$store_id]))
            $coupon_code = $couponDetail[$store_id]['coupon_name'];

        if (empty($coupon_code))
            return;


        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $coupon_table = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');
        $coupon_id = $offer_id = $coupon_table->getCouponInfo(array("fetchColumn" => 1, "coupon_code" => $coupon_code));

        if (!empty($viewer_id)) {
            $order_coupon_table = Engine_Api::_()->getDbtable('ordercoupons', 'sitestoreoffer');
            $order_coupon_row = $order_coupon_table->createRow();
            $order_coupon_row->coupon_id = $coupon_id;
            $order_coupon_row->buyer_id = $viewer_id;
            $order_coupon_row->store_id = $store_id;
            $order_coupon_row->creation_date = date('Y-m-d H:i:s');
            $order_coupon_row->save();
        }
        $coupon_table->updateClaims($coupon_id);
        //}
        //}
    }

    public function copyProductAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //MUST BE ABLE TO CRETE PRODUCTS
        if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, "create")) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->view->product_id = $product_id = $this->_getParam('product_id', NULL);
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        if (empty($product_id) || empty($sitestoreproduct)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");
        $this->view->store_id = $store_id = $sitestoreproduct->store_id;
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
        $this->view->directPayment = $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $this->view->isDownPaymentEnable = $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
        $this->view->allowProductCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.productcode', 1);
        $this->view->category_edit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.categoryedit', 1);
        $this->view->category_id = $previous_category_id = $sitestoreproduct->category_id;
        $this->view->subcategory_id = $subcategory_id = $sitestoreproduct->subcategory_id;
        $this->view->subsubcategory_id = $subsubcategory_id = $sitestoreproduct->subsubcategory_id;
        $row = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategory($subcategory_id);
        $subcategory_name = "";
        if (!empty($row))
            $subcategory_name = $row->category_name;
        $this->view->subcategory_name = $subcategory_name;

        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitestoreproduct')->defaultProfileId();

        //GET PROFILE MAPPING ID
        $categoryIds = array();
        $categoryIds[] = $sitestoreproduct->category_id;
        $categoryIds[] = $sitestoreproduct->subcategory_id;
        $categoryIds[] = $sitestoreproduct->subsubcategory_id;
        $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIds, 0, 'profile_type');

//    if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
//      $categoryIds = array();
//      $categoryIds[] = $_POST['category_id'];
//      if (isset($_POST['subcategory_id']) && !empty($_POST['subcategory_id'])) {
//        $categoryIds[] = $_POST['subcategory_id'];
//      }
//      if (isset($_POST['subsubcategory_id']) && !empty($_POST['subsubcategory_id'])) {
//        $categoryIds[] = $_POST['subsubcategory_id'];
//      }
//      $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIds, 0, 'profile_type');
//    }
        //GET PRODUCT USING LEVEL OR PACKAGE BASED SETTINGS
        $packageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1);
        if (!empty($packageEnable)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $sitestore->package_id);
            if (empty($packageObj->store_settings)) {
                $product_types = array('simple', 'configurable', 'virtual', 'grouped', 'bundled', 'downloadable');
            } else {
                $storeSettings = @unserialize($packageObj->store_settings);
                $product_types = $storeSettings['product_type'];
            }
        } else {
            $user = $sitestore->getOwner();
            $product_types = Engine_Api::_()->authorization()->getPermission($user->level_id, "sitestore_store", "product_type");
            $product_types = Zend_Json_Decoder::decode($product_types);
        }

        if ($sitestoreproduct->product_type == 'virtual')
            $this->view->showProductInventory = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.productinventory', 1);
        else
            $this->view->showProductInventory = true;

        //MAKE FORM
        $this->view->productTypeName = $_POST['product_type'] = $sitestoreproduct->product_type;
        $this->view->form = $form = new Sitestoreproduct_Form_Copy(array('item' => $sitestoreproduct, 'defaultProfileId' => $defaultProfileId, 'pageId' => $sitestoreproduct->store_id, 'productType' => $sitestoreproduct->product_type, 'allowedProductTypes' => $product_types));
        $form->setAttrib("data-ajax", "false");

        $form->category_id->setValue($sitestoreproduct->category_id);
        $form->subcategory_id->setValue($sitestoreproduct->subcategory_id);
        $form->subsubcategory_id->setValue($sitestoreproduct->subsubcategory_id);

        $temp_allow_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($sitestoreproduct->store_id);
        if (empty($temp_allow_selling)) {
            $form->removeElement('allow_purchase');
        }

        $form->removeElement('photo');
        $this->view->expiry_setting = $expiry_setting = Engine_Api::_()->sitestoreproduct()->expirySettings();

        // ASSIGNED THE OTHERINFO TABLE CONTENT VALUES TO FORM
        $this->view->otherInfoObj = $otherInfoObj = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->getOtherinfo($sitestoreproduct->product_id);
        $tempOtherInfos = array();
        $tempOtherInfos['overview'] = $otherInfoObj->overview;
        $tempOtherInfos['out_of_stock'] = $otherInfoObj->out_of_stock;
        $tempOtherInfos['out_of_stock_action'] = $otherInfoObj->out_of_stock_action;
        $tempOtherInfos['discount'] = $otherInfoObj->discount;
        $tempOtherInfos['handling_type'] = $otherInfoObj->handling_type;
        $tempOtherInfos['discount_start_date'] = $otherInfoObj->discount_start_date;
        $tempOtherInfos['discount_end_date'] = $otherInfoObj->discount_end_date;
        $tempOtherInfos['discount_permanant'] = $otherInfoObj->discount_permanant;
        $tempOtherInfos['user_type'] = $otherInfoObj->user_type;
        $tempOtherInfos['special_vat'] = $otherInfoObj->special_vat;

        if ($sitestoreproduct->product_type == 'virtual' || $sitestoreproduct->product_type == 'bundled')
            $product_info = unserialize($otherInfoObj->product_info);

        $isSitestorereservationModuleExist = Engine_Api::_()->sitestoreproduct()->isSitestorereservationModuleExist();
        if ($sitestoreproduct->product_type == 'virtual' && !empty($isSitestorereservationModuleExist)) {
            $tempOtherInfos['virtual_product_price_range'] = isset($product_info['virtual_product_price_range']) ? $product_info['virtual_product_price_range'] : null;
            $isDateSelectorEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.dateselector', 0);
            if (!empty($isDateSelectorEnable))
                $tempOtherInfos['virtual_product_date_selector'] = isset($product_info['virtual_product_date_selector']) ? $product_info['virtual_product_date_selector'] : null;
        }

        // SHOW DOWNPAYMENT VALUE PRE-FILLED
        if ($sitestoreproduct->product_type != 'grouped' && !empty($isDownPaymentEnable) && !empty($directPayment)) {
            if (empty($otherInfoObj->downpayment_value))
                $tempOtherInfos['downpayment'] = 0;
            else {
                $tempOtherInfos['downpayment'] = 1;
                $tempOtherInfos['downpaymentvalue'] = $otherInfoObj->downpayment_value;
            }
        }

        if ($sitestoreproduct->product_type == 'bundled' || $sitestoreproduct->product_type == 'grouped') {
            if ($sitestoreproduct->product_type == 'bundled') {
                $tempOtherInfos['weight_type'] = $otherInfoObj->weight_type;
                $tempOtherInfos['enable_shipping'] = $product_info['enable_shipping'];
                $tempOtherInfos['bundle_product_type'] = $product_info['bundle_product_type'];
            }
            $tempMappedIds = $otherInfoObj->mapped_ids;
            if (!empty($tempMappedIds)) {
                $tempMappedIds = Zend_Json_Decoder::decode($tempMappedIds);
                $productsArray = $tempProductsArray = array();
                $this->view->tempMappedIdsStr = @implode(",", $tempMappedIds);
                foreach ($tempMappedIds as $product_id) {
                    $tempProductsArray['title'] = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id)->title;
                    $tempProductsArray['id'] = $product_id;
                    $productsArray[] = $tempProductsArray;
                }
                $this->view->productArray = $productsArray;
            }
        }

        if (empty($otherInfoObj->handling_type))
            $tempOtherInfos['discount_price'] = $otherInfoObj->discount_value;
        else
            $tempOtherInfos['discount_rate'] = $otherInfoObj->discount_value;


        //SAVE SITESTOREPRODUCT ENTRY
        if (!$this->getRequest()->isPost()) {

            //prepare tags
            $sitestoreproductTags = $sitestoreproduct->tags()->getTagMaps();
            $tagString = '';

            foreach ($sitestoreproductTags as $tagmap) {

                if ($tagString != '')
                    $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();
            }

            $this->view->tagNamePrepared = $tagString;
            $form->tags->setValue($tagString);
            $tempValues = array_merge($sitestoreproduct->toArray(), $tempOtherInfos);

            if (array_key_exists("weight", $tempValues)) {
                $getPercisionValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.rate.precision', 2);
                $tempValues['weight'] = @number_format($tempValues['weight'], 2, '.', ','); //@round($tempValues['weight'], $getPercisionValue);
            }

            $tempValues['search'] = 1;
            $tempValues['draft'] = 0;
            if ($sitestoreproduct->product_type == 'configurable' || $sitestoreproduct->product_type == 'downloadable') {
                $tempValues['search'] = 0;
            }
            $form->populate($tempValues);

            if ($sitestoreproduct->end_date && $sitestoreproduct->end_date != '0000-00-00 00:00:00') {
                $form->end_date_enable->setValue(1);
                // Convert and re-populate times
                $end = strtotime($sitestoreproduct->end_date);
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $end = date('Y-m-d H:i:s', $end);
                date_default_timezone_set($oldTz);

                $form->populate(array(
                    'end_date' => $end,
                ));
            } else if (empty($sitestoreproduct->end_date) || $sitestoreproduct->end_date == '0000-00-00 00:00:00') {
                $date = (string) date('Y-m-d');
                $form->end_date->setValue($date . ' 00:00:00');
            }

            // Convert and re-populate times
            $end = strtotime($sitestoreproduct->start_date);
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($viewer->timezone);
            $end = date('Y-m-d H:i:s', $end);
            date_default_timezone_set($oldTz);

            $form->populate(array(
                'start_date' => $end,
            ));

            if ($sitestoreproduct->product_type != 'grouped' && (empty($otherInfoObj->discount_start_date) || $otherInfoObj->discount_start_date == '0000-00-00 00:00:00')) {
                $date = (string) date('Y-m-d');
                $form->discount_start_date->setValue($date . ' 00:00:00');
            }

            if ($sitestoreproduct->product_type != 'grouped' && (empty($otherInfoObj->discount_end_date) || $otherInfoObj->discount_end_date == '0000-00-00 00:00:00')) {
                $toDate = (string) date('Y-m-d', strtotime("+1 Month"));
                $form->discount_end_date->setValue($toDate . ' 00:00:00');
            }


            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            foreach ($roles as $role) {
                if ($form->auth_view) {
                    if (1 == $auth->isAllowed($sitestoreproduct, $role, "view")) {
                        $form->auth_view->setValue($role);
                    }
                }

                if ($form->auth_comment) {
                    if (1 == $auth->isAllowed($sitestoreproduct, $role, "comment")) {
                        $form->auth_comment->setValue($role);
                    }
                }
            }

            $roles_photo = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            foreach ($roles_photo as $role_photo) {
                if ($form->auth_photo) {
                    if (1 == $auth->isAllowed($sitestoreproduct, $role_photo, "photo")) {
                        $form->auth_photo->setValue($role_photo);
                    }
                }
            }

            $videoEnable = Engine_Api::_()->sitestoreproduct()->enableVideoPlugin();
            if ($videoEnable) {
                $roles_video = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                foreach ($roles_video as $role_video) {
                    if ($form->auth_video) {
                        if (1 == $auth->isAllowed($sitestoreproduct, $role_video, "video")) {
                            $form->auth_video->setValue($role_video);
                        }
                    }
                }
            }

            if (Engine_Api::_()->sitestoreproduct()->listBaseNetworkEnable()) {
                if (empty($sitestoreproduct->networks_privacy)) {
                    $form->networks_privacy->setValue(array(0));
                }
            }

            //POPULATE USER TAX
            if (!empty($sitestoreproduct->user_tax))
                $form->populate(array('user_tax' => @unserialize($sitestoreproduct->user_tax)));

            return;
        }

        $params = array();
        $params['copy_product'] = 1;
        $params['store_id'] = $store_id;
        $params['expiry_setting'] = $expiry_setting;
        $this->saveProduct($form, $_POST, $params);
    }

    public function saveProduct($form, $formValues, $params) {
        $this->view->form_post = 1;
        $_POST = $formValues;
        $store_id = $params['store_id'];
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
        if ($_POST['product_type'] == "grouped" || $_POST['product_type'] == "bundled") {
            if (!empty($_POST['product_ids'])) {
                $productsArray = $tempProductsArray = array();
                $this->view->tempMappedIdsStr = $_POST['product_ids'];
                $tempMappedIds = explode(',', $_POST['product_ids']);
                foreach ($tempMappedIds as $product_id) {
                    $tempProductsArray['title'] = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id)->title;
                    $tempProductsArray['id'] = $product_id;
                    $productsArray[] = $tempProductsArray;
                }
                $this->view->productArray = $productsArray;
            }
        }

        //REMOVE VALIDATORS ACCORDING CONDITIONS
        if ($_POST['product_type'] != "grouped") {
            if (!empty($_POST['stock_unlimited']))
                $form->in_stock->setValidators(array());

            if ($_POST['discount'] == 0) {
                $form->discount_rate->setValidators(array());
                $form->discount_price->setValidators(array());
                $form->discount_start_date->setValidators(array());
                $form->discount_end_date->setValidators(array());
            } else if ($_POST['handling_type'] == 0)
                $form->discount_rate->setValidators(array());
            else
                $form->discount_price->setValidators(array());

            if ($_POST['product_type'] == "bundled")
                if (!empty($_POST['weight_type']))
                    $form->weight->setValidators(array());
        }

        if ($_POST['product_type'] != "grouped") {
            if (empty($_POST['min_order_quantity']))
                $_POST['min_order_quantity'] = isset($_POST['in_stock']) ? $_POST['in_stock'] : '';

            if (empty($_POST['max_order_quantity']))
                $_POST['max_order_quantity'] = isset($_POST['in_stock']) ? $_POST['in_stock'] : '';
        }

        $getPostValuesArray = $this->getRequest()->getPost();

        if (@array_key_exists('section_id', $getPostValuesArray))
            unset($getPostValuesArray['section_id']);

        if (@array_key_exists('inputsection_id', $getPostValuesArray))
            unset($getPostValuesArray['inputsection_id']);


        if ($form->isValid($getPostValuesArray)) {

            //PRODUCT IMAGE VALIDATION
            $isMainPhotoRequired = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.mainphoto', 1);
            if (!empty($isMainPhotoRequired) && !empty($params['copy_product']) && (!isset($_POST['temp_image_file_id']) || empty($_POST['temp_image_file_id'])) && (!isset($_POST['temp_image_file_path']) || empty($_POST['temp_image_file_path']))) {
                $error = $this->view->translate("File 'image' was not uploaded.");
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            //CATEGORY IS REQUIRED FIELD
            if (empty($_POST['category_id'])) {
                $error = $this->view->translate('Please complete Category field - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            if ($_POST['product_type'] != "grouped" && !empty($_POST['discount']) && empty($_POST['discount_permanant']) && empty($_POST['discount_end_date']['date'])) {
                $error = $this->view->translate('Please enter Discount end Date - it is required.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            //VARIOUS LOGICAL CHECKS BITWEEN DIFFERENT PRODUCT ATTRIBUTES
            if ($_POST['product_type'] != "grouped" && (isset($_POST['stock_unlimited']) && empty($_POST['stock_unlimited'])) && !empty($_POST['min_order_quantity']) && $_POST['min_order_quantity'] > $_POST['in_stock']) {
                $error = $this->view->translate('Minimum Order Quantity can not be greater than In Stock value.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            if ($_POST['product_type'] != "grouped" && empty($_POST['stock_unlimited']) && !empty($_POST['max_order_quantity']) && $_POST['max_order_quantity'] > $_POST['in_stock']) {
                $error = $this->view->translate('Maximum Order Quantity can not be greater than In Stock value.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            if ($_POST['product_type'] != "grouped" && !empty($_POST['max_order_quantity']) && $_POST['max_order_quantity'] < $_POST['min_order_quantity']) {
                $error = $this->view->translate('Minimum Order Quantity can not be greater than Maximum Order Quantity.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            if ($_POST['product_type'] != "grouped" && $_POST['discount'] == 1 && $_POST['handling_type'] == 0 && $_POST['discount_price'] >= $_POST['price']) {
                $error = $this->view->translate('Discount can not be more than and equal to actual price.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            if (($_POST['product_type'] == "grouped" || $_POST['product_type'] == 'bundled') && (empty($_POST['product_ids']) || COUNT(explode(',', $_POST['product_ids'])) <= 1)) {
                $error = $this->view->translate('To create this product, you must select at least two products from this store.');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            //TAKING VARIOUS VALUES FOR PRODUCT AND OTHERINFO TABLE
            $otherInfoValues = array();
            $sitestoreproduct_values = $form->getValues();

//      if(empty ($temp_allow_selling)){
//        $sitestoreproduct_values['allow_purchase'] = 0;
//      }

            if ($sitestoreproduct_values['product_type'] != 'grouped') {
                if (empty($sitestoreproduct_values['max_order_quantity']))
                    $sitestoreproduct_values['max_order_quantity'] = isset($sitestoreproduct_values['in_stock']) ? $sitestoreproduct_values['in_stock'] : '';

                // START DOWNPAYMENT WORK
                if (!empty($directPayment) && !empty($isDownPaymentEnable)) {
                    if (!empty($sitestoreproduct_values['downpayment']))
                        $otherInfoValues['downpayment_value'] = $sitestoreproduct_values['downpaymentvalue'];
                    else
                        $otherInfoValues['downpayment_value'] = 0;
                    unset($sitestoreproduct_values['downpayment']);
                    unset($sitestoreproduct_values['downpaymentvalue']);
                }
                // END DOWNPAYMENT WORK
                // START DISCOUNT WORK
                if (!empty($sitestoreproduct_values['discount'])) {

                    if (empty($sitestoreproduct_values['handling_type'])) {
                        $otherInfoValues['discount_value'] = $otherInfoValues['discount_amount'] = @round($sitestoreproduct_values['discount_price'], 2);
                        $otherInfoValues['discount_percentage'] = @round($otherInfoValues['discount_value'] * 100 / $sitestoreproduct_values['price'], 2);
                    } else {
                        $otherInfoValues['discount_value'] = @round($sitestoreproduct_values['discount_rate'], 2);
                        $otherInfoValues['discount_percentage'] = @round($sitestoreproduct_values['discount_rate'], 2);
                        $otherInfoValues['discount_amount'] = $sitestoreproduct_values['price'] * $otherInfoValues['discount_value'] / 100;
                    }

                    $otherInfoValues['discount'] = $sitestoreproduct_values['discount'];
                    $otherInfoValues['handling_type'] = $sitestoreproduct_values['handling_type'];
                    $otherInfoValues['discount_start_date'] = $sitestoreproduct_values['discount_start_date'];

                    if (empty($sitestoreproduct_values['discount_permanant']))
                        $otherInfoValues['discount_end_date'] = $sitestoreproduct_values['discount_end_date'];

                    $otherInfoValues['discount_permanant'] = $sitestoreproduct_values['discount_permanant'];
                    $otherInfoValues['user_type'] = $sitestoreproduct_values['user_type'];
                }
                unset($sitestoreproduct_values['discount']);
                unset($sitestoreproduct_values['handling_type']);
                unset($sitestoreproduct_values['discount_price']);
                unset($sitestoreproduct_values['discount_rate']);
                unset($sitestoreproduct_values['discount_start_date']);
                unset($sitestoreproduct_values['discount_end_date']);
                unset($sitestoreproduct_values['discount_permanant']);
                unset($sitestoreproduct_values['user_type']);
                // END DISCOUNT WORK

                if (isset($sitestoreproduct_values['user_tax']))
                    $sitestoreproduct_values['user_tax'] = @serialize($sitestoreproduct_values['user_tax']);

                // START VIRTUAL PRODUCT DATE-CALENDAR AND PRICE BASIS WORK
                if ($sitestoreproduct_values['product_type'] == 'virtual') {
                    $tempVirtualProductInfo = array();
                    $isDateSelectorEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.dateselector', 0);
                    if (!empty($isDateSelectorEnable)) {
                        $tempVirtualProductInfo['virtual_product_date_selector'] = $sitestoreproduct_values['virtual_product_date_selector'];
                        unset($sitestoreproduct_values['virtual_product_date_selector']);
                    }

                    if (!empty($sitestoreproduct_values['virtual_product_price_range'])) {
                        $tempVirtualProductInfo['virtual_product_price_range'] = $sitestoreproduct_values['virtual_product_price_range'];
                        unset($sitestoreproduct_values['virtual_product_price_range']);
                    }

                    if (!empty($tempVirtualProductInfo))
                        $otherInfoValues['product_info'] = serialize($tempVirtualProductInfo);
                }
                // END VIRTUAL PRODUCT DATE-CALENDAR AND PRICE BASIS WORK

                if ($sitestoreproduct_values['product_type'] == 'bundled') {

                    if (@in_array('configurable', $sitestoreproduct_values['bundle_product_type']) || @in_array('virtual', $sitestoreproduct_values['bundle_product_type']))
                        $isConfigurationTabRequired = true;

                    $mappedIdsArray = explode(',', $sitestoreproduct_values['product_ids']);
                    $otherInfoValues['mapped_ids'] = Zend_Json_Encoder::encode($mappedIdsArray);
                    $otherInfoValues['weight_type'] = $sitestoreproduct_values['weight_type'];

                    $otherInfoValues['product_info'] = @serialize(array('enable_shipping' => $sitestoreproduct_values['enable_shipping'], 'bundle_product_type' => $sitestoreproduct_values['bundle_product_type']));

                    if (!empty($sitestoreproduct_values['weight_type']))
                        $sitestoreproduct_values['weight'] = '';

                    unset($sitestoreproduct_values['bundle_product_type']);
                    unset($sitestoreproduct_values['enable_shipping']);
                    unset($sitestoreproduct_values['product_name']);
                    unset($sitestoreproduct_values['product_ids']);
                    unset($sitestoreproduct_values['weight_type']);
                }

                $otherInfoValues['out_of_stock'] = isset($sitestoreproduct_values['out_of_stock']) ? $sitestoreproduct_values['out_of_stock'] : '';
                $otherInfoValues['out_of_stock_action'] = isset($sitestoreproduct_values['out_of_stock_action']) ? $sitestoreproduct_values['out_of_stock_action'] : '';

                // ADDING SPECIAL VAT IN OTHERINFO
                if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0) && isset($sitestoreproduct_values['special_vat'])) {
                    if (empty($sitestoreproduct_values['special_vat']))
                        $otherInfoValues['special_vat'] = null;
                    else
                        $otherInfoValues['special_vat'] = $sitestoreproduct_values['special_vat'];
                }

                unset($sitestoreproduct_values['out_of_stock']);
                unset($sitestoreproduct_values['out_of_stock_action']);
                unset($sitestoreproduct_values['special_vat']);
            }else {
                $otherInfoValues['mapped_ids'] = Zend_Json_Encoder::encode(explode(',', $sitestoreproduct_values['product_ids']));

                unset($sitestoreproduct_values['product_name']);
                unset($sitestoreproduct_values['product_ids']);
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            $user_level = $viewer->level_id;
            try {
                //Create sitestoreproduct
                $values = array_merge($sitestoreproduct_values, array(
                    'owner_type' => $viewer->getType(),
                    'owner_id' => $viewer_id,
                    'featured' => Engine_Api::_()->authorization()->getPermission($user_level, 'sitestoreproduct_product', "featured"),
                    'sponsored' => Engine_Api::_()->authorization()->getPermission($user_level, 'sitestoreproduct_product', "sponsored"),
                    'approved' => Engine_Api::_()->authorization()->getPermission($user_level, 'sitestoreproduct_product', "approved")
                ));

                if (empty($values['subcategory_id'])) {
                    $values['subcategory_id'] = 0;
                }

                if (empty($values['subsubcategory_id'])) {
                    $values['subsubcategory_id'] = 0;
                }

                if ($params['expiry_setting'] == 1 && $values['end_date_enable'] == 1) {
                    // Convert times
                    $oldTz = date_default_timezone_get();
                    date_default_timezone_set($viewer->timezone);
                    $end = strtotime($values['end_date']);
                    date_default_timezone_set($oldTz);
                    $values['end_date'] = date('Y-m-d H:i:s', $end);
                } elseif (isset($values['end_date'])) {
                    unset($values['end_date']);
                }

                // Convert times
                if (isset($values['start_date']) && !empty($values['start_date'])) {
                    $oldTz = date_default_timezone_get();
                    date_default_timezone_set($viewer->timezone);
                    $end = strtotime($values['start_date']);
                    date_default_timezone_set($oldTz);
                    $values['start_date'] = date('Y-m-d H:i:s', $end);
                }

                if (Engine_Api::_()->sitestoreproduct()->listBaseNetworkEnable()) {
                    if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
                        if (in_array(0, $values['networks_privacy'])) {
                            unset($values['networks_privacy']);
                        }
                    }
                }

                $sitestoreproduct = Engine_Api::_()->getItemTable('sitestoreproduct_product')->createRow();
                if ($_POST['product_type'] == 'grouped')
                    $values['stock_unlimited'] = 1;

                if (@array_key_exists('max_order_quantity', $values) && empty($values['max_order_quantity']))
                    unset($values['max_order_quantity']);

                if (empty($values['search'])) {
                    $values['search'] = 0;
                }

                $values['store_id'] = $store_id;

                $values['weight'] = @number_format($values['weight'], 2, '.', ',');
                $values['price'] = @number_format($values['price'], 2, '.', '');

                $sitestoreproduct->setFromArray($values);

                if ($sitestoreproduct->approved) {
                    $sitestoreproduct->approved_date = date('Y-m-d H:i:s');
                }

                if (!empty($_POST['section_id']) && !empty($_POST['inputsection_id']) && ($_POST['section_id'] == 'new')) {
                    $tableSections = Engine_Api::_()->getDbTable('sections', 'sitestoreproduct');
                    $tableSectionsName = $tableSections->info('name');

                    $row_info = $tableSections->fetchRow($tableSections->select()->from($tableSectionsName, 'max(sec_order) AS sec_order'));
                    $sec_order = $row_info['sec_order'] + 1;
                    $row = $tableSections->createRow();
                    $row->section_name = $_POST['inputsection_id'];
                    $row->sec_order = $sec_order;
                    $row->store_id = $store_id;
                    $newsec_id = $row->save();
                    $sitestoreproduct->section_id = $newsec_id;
                }

                if (@array_key_exists("section_id", $_POST) && ($_POST['section_id'] != 'new'))
                    $sitestoreproduct->section_id = $_POST['section_id'];

                if (empty($sitestoreproduct->section_id)) {
                    unset($sitestoreproduct->section_id);
                }

                $sitestoreproduct->save();
                $product_id = $sitestoreproduct->product_id;

                //START PAGE INTEGRATION WORK
                $page_id = $this->_getParam('page_id');
                if (!empty($page_id)) {
                    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration');
                    if (!empty($moduleEnabled)) {
                        $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitepageintegration');
                        $row = $contentsTable->createRow();
                        $row->owner_id = $viewer_id;
                        $row->resource_owner_id = $sitestoreproduct->owner_id;
                        $row->page_id = $page_id;
                        $row->resource_type = 'sitestoreproduct_product';
                        $row->resource_id = $sitestoreproduct->product_id;
                        $row->save();
                    }
                }
                //END PAGE INTEGRATION WORK

                if ($sitestoreproduct->product_type == 'configurable' || $sitestoreproduct->product_type == 'virtual') {
                    $sitestoreform = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options')->createRow();
                    $sitestoreform->label = $values['title'];
                    $sitestoreform->field_id = 1;
                    $option_id = $sitestoreform->save();
                    $optionids = Engine_Api::_()->getDbtable('productfields', 'sitestoreproduct')->createRow();
                    $optionids->option_id = $option_id;
                    $optionids->product_id = $sitestoreproduct->product_id;
                    $optionids->save();

                    // ENTER THE CATEGORY FIELDS IN PRODUCT ATTRIBUTES TABLE
                    $categoryFields = Engine_Api::_()->sitestoreproduct()->getProductCategoryFields($sitestoreproduct);
                    if (!empty($categoryFields)) {
                        $order = 1;
                        foreach ($categoryFields as $field) {
                            if ($field['type'] == 'select') {
                                $sitestoremeta = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'meta')->createRow();
                                $sitestoremeta->option_id = $option_id;
                                $sitestoremeta->type = $field['type'];
                                $sitestoremeta->label = $field['lable'];
                                $sitestoremeta->display = 1;
                                $field_id = $sitestoremeta->save();
                                $sitestoremap = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'maps')->createRow();
                                $sitestoremap->field_id = 1;
                                $sitestoremap->option_id = $option_id;
                                $sitestoremap->child_id = $field_id;
                                $sitestoremap->order = $order++;
                                $sitestoremap->save();
                                if (isset($field['multioptions']) && !empty($field['multioptions'])) {
                                    foreach ($field['multioptions'] as $option) {
                                        $sitestoreoption = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options')->createRow();
                                        $sitestoreoption->field_id = $field_id;
                                        $sitestoreoption->label = $option['label'];
                                        $sitestoreoption->order = $option['order'];
                                        $sitestoreoption->save();
                                    }
                                }
                            }
                        }
                    }
                }

                //SET PHOTO
                if (empty($params['copy_product']) && !empty($values['photo'])) {
                    $sitestoreproduct->setPhoto($form->photo);
                    $albumTable = Engine_Api::_()->getDbtable('albums', 'sitestoreproduct');
                    $album_id = $albumTable->update(array('photo_id' => $sitestoreproduct->photo_id), array('product_id = ?' => $sitestoreproduct->product_id));
                }

                if (!empty($params['copy_product']) && (!empty($_POST["temp_image_file_id"]) || !empty($_POST["temp_image_file_path"]))) {
                    $imageFlag = 0;
                    if (@count($_POST["temp_image_file_id"]) > 0) {
                        foreach ($_POST["temp_image_file_id"] as $file_id) {
                            $sitestoreproduct->setCopyPhoto($flag = 1, $file_id, $sitestoreproduct, $imageFlag);
                            $imageFlag++;
                        }
                    }

                    if (@count($_POST["temp_image_file_path"]) > 0) {
                        foreach ($_POST["temp_image_file_path"] as $file_id) {
                            $sitestoreproduct->setCopyPhoto($flag = 0, $file_id, $sitestoreproduct, $imageFlag);
                            $imageFlag++;
                        }
                    }

                    if (!empty($sitestoreproduct)) {
                        $albumTable = Engine_Api::_()->getDbtable('albums', 'sitestoreproduct');
                        $album_id = $albumTable->update(array('photo_id' => $sitestoreproduct->photo_id), array('product_id = ?' => $sitestoreproduct->product_id));
                    }
                }

                //ADDING TAGS
                $keywords = '';
                if (isset($values['tags']) && !empty($values['tags'])) {
                    $tags = preg_split('/[,]+/', $values['tags']);
                    $tags = array_filter(array_map("trim", $tags));
                    $sitestoreproduct->tags()->addTagMaps($viewer, $tags);

                    foreach ($tags as $tag) {
                        $keywords .= " $tag";
                    }
                }

                //SAVE CUSTOM VALUES AND PROFILE TYPE VALUE
                $customfieldform = $form->getSubForm('fields');
                $customfieldform->setItem($sitestoreproduct);
                $customfieldform->saveValues();

                $categoryIds = array();
                $categoryIds[] = $sitestoreproduct->category_id;
                $categoryIds[] = $sitestoreproduct->subcategory_id;
                $categoryIds[] = $sitestoreproduct->subsubcategory_id;
                $sitestoreproduct->profile_type = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getProfileType($categoryIds, 0, 'profile_type');

                //NOT SEARCHABLE IF SAVED IN DRAFT MODE
                if (!empty($sitestoreproduct->draft)) {
                    $sitestoreproduct->search = 0;
                }
                $sitestoreproduct->save();

                if (!empty($product_id)) {
                    $sitestoreproduct->setLocation();
                }

                //PRIVACY WORK
                $auth = Engine_Api::_()->authorization()->context;

                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

                if (empty($values['auth_view'])) {
                    $values['auth_view'] = array("everyone");
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = array("everyone");
                }

                $viewMax = array_search($values['auth_view'], $roles);
                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($sitestoreproduct, $role, "view", ($i <= $viewMax));
                    $auth->setAllowed($sitestoreproduct, $role, "comment", ($i <= $commentMax));
                }

                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                if (empty($values['auth_photo'])) {
                    $values['auth_photo'] = array("owner");
                }

                if (!isset($values['auth_video']) && empty($values['auth_video'])) {
                    $values['auth_video'] = array("owner");
                }

                $photoMax = array_search($values['auth_photo'], $roles);
                $videoMax = array_search($values['auth_video'], $roles);
                foreach ($roles as $i => $roles) {
                    $auth->setAllowed($sitestoreproduct, $roles, "photo", ($i <= $photoMax));
                    $auth->setAllowed($sitestoreproduct, $roles, "video", ($i <= $videoMax));
                }

                $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct');
                $row = $tableOtherinfo->getOtherinfo($product_id);
                if (empty($row)) {

                    // SAVE MULTILANGUAL OVERVIEW IN OTHERINFO TABLE
                    $otherInfoArray = array(
                        'product_id' => $product_id,
                        'overview' => isset($values['overview']) ? $values['overview'] : '',
                        'out_of_stock' => isset($otherInfoValues['out_of_stock']) ? $otherInfoValues['out_of_stock'] : '',
                        'out_of_stock_action' => isset($otherInfoValues['out_of_stock_action']) ? $otherInfoValues['out_of_stock_action'] : '',
                        'mapped_ids' => isset($otherInfoValues['mapped_ids']) ? $otherInfoValues['mapped_ids'] : '',
                        'weight_type' => isset($otherInfoValues['weight_type']) ? $otherInfoValues['weight_type'] : '',
                        'discount' => isset($otherInfoValues['discount']) ? $otherInfoValues['discount'] : '',
                        'handling_type' => isset($otherInfoValues['handling_type']) ? $otherInfoValues['handling_type'] : '',
                        'discount_value' => isset($otherInfoValues['discount_value']) ? $otherInfoValues['discount_value'] : '',
                        'discount_start_date' => isset($otherInfoValues['discount_start_date']) ? $otherInfoValues['discount_start_date'] : '',
                        'discount_end_date' => isset($otherInfoValues['discount_end_date']) ? $otherInfoValues['discount_end_date'] : '',
                        'discount_permanant' => isset($otherInfoValues['discount_permanant']) ? $otherInfoValues['discount_permanant'] : '',
                        'user_type' => isset($otherInfoValues['user_type']) ? $otherInfoValues['user_type'] : '',
                        'discount_amount' => isset($otherInfoValues['discount_amount']) ? $otherInfoValues['discount_amount'] : '',
                        'discount_percentage' => isset($otherInfoValues['discount_percentage']) ? $otherInfoValues['discount_percentage'] : '',
                        'product_info' => isset($otherInfoValues['product_info']) ? $otherInfoValues['product_info'] : '',
                        'downpayment_value' => isset($otherInfoValues['downpayment_value']) ? $otherInfoValues['downpayment_value'] : '',
                        'special_vat' => isset($otherInfoValues['special_vat']) ? $otherInfoValues['special_vat'] : ''
                    );

                    //DEFAULT LANGUAGE
                    $temp_languages = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.languages', null);
                    if (!empty($temp_languages)) {
                        $languageCount = Count($temp_languages);
                        foreach ($temp_languages as $language_label) {
                            if ($languageCount >= 2 && $language_label != 'en') {
                                $overview = "overview_" . $language_label;
                                $otherInfoArray[$overview] = isset($values[$overview]) ? $values[$overview] : '';
                            }
                        }
                    }

                    Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->insert($otherInfoArray);
                }

                if ($sitestoreproduct->draft == 0 && $sitestoreproduct->search) {
                    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                    $activityFeedType = null;
                    if (Engine_Api::_()->sitestore()->isStoreOwner($sitestore) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
                        $activityFeedType = 'sitestoreproduct_admin_new';
                    elseif ($sitestore->all_post || Engine_Api::_()->sitestore()->isStoreOwner($sitestore))
                        $activityFeedType = 'sitestoreproduct_new';

                    if ($activityFeedType) {
                        $action = $actionTable->addActivity($viewer, $sitestore, $activityFeedType);
                        Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
                    }
                    //MAKE SURE ACTION EXISTS BEFOR ATTACHING THE NOTE TO THE ACTIVITY
                    if ($action != null) {
                        $actionTable->attachActivity($action, $sitestoreproduct, Activity_Model_Action::ATTACH_MULTI);
                    }

                    //SENDING ACTIVITY FEED TO FACEBOOK.
                    $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
                    if (!empty($enable_Facebooksefeed)) {
                        $product_array = array();
                        $product_array['type'] = 'sitestoreproduct_new';
                        $product_array['object'] = $sitestoreproduct;
                        $product_array['store_title'] = $sitestore->getTitle();
                        $product_array['store_url'] = $sitestore->getHref();
                        Engine_Api::_()->facebooksefeed()->sendFacebookFeed($product_array);
                    }
                }

                if (Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitestoreproduct.defaultproductcreate.email')) {
                    $getDefaultProductCreateEmail = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.defaultproductcreate.email', null);
                    $emailAddress = explode(",", $getDefaultProductCreateEmail);
                    foreach ($emailAddress as $email) {
                        if (!empty($email)) {
                            $email = trim($email);
                            $host = $_SERVER['HTTP_HOST'];
                            $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $host . $sitestoreproduct->getHref();

                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'SITESTOREPRODUCT_PRODUCT_CREATION_EDITOR', array(
                                'object_link' => $object_link,
                                'object_title' => $sitestoreproduct->getTitle(),
                                'object_description' => $sitestoreproduct->getDescription(),
                                'queue' => true
                            ));
                        }
                    }
                } else {
                    $users = Engine_Api::_()->getDbtable('editors', 'sitestoreproduct')->getAllEditors();
                    foreach ($users as $user_ids) {
                        if (!empty($user_ids->user_id)) {
                            $subjectOwner = Engine_Api::_()->getItem('user', $user_ids->user_id);

                            if (!empty($subjectOwner->email)) {
                                $host = $_SERVER['HTTP_HOST'];
                                $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $host . $sitestoreproduct->getHref();

                                Engine_Api::_()->getApi('mail', 'core')->sendSystem($subjectOwner->email, 'SITESTOREPRODUCT_PRODUCT_CREATION_EDITOR', array(
                                    'object_link' => $object_link,
                                    'object_title' => $sitestoreproduct->getTitle(),
                                    'object_description' => $sitestoreproduct->getDescription(),
                                    'queue' => true
                                ));
                            }
                        }
                    }
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            // NOTIFICATION TO BUYERS, WHO FOLLOW THE STORE
            if (empty($sitestoreproduct->draft) && !empty($sitestoreproduct->search)) {
                $follow_table = Engine_Api::_()->getDbtable('follows', 'seaocore');
                $followers = $follow_table->select()
                                ->from($follow_table->info('name'), 'poster_id')
                                ->where('resource_type =?', 'sitestore_store')
                                ->where('resource_id =?', $sitestoreproduct->store_id)
                                ->query()->fetchAll();

                if (!empty($followers)) {
                    $notification_table = Engine_Api::_()->getDbtable('notifications', 'activity');
                    foreach ($followers as $follower) {
                        $followerObj = Engine_Api::_()->getItem('user', $follower['poster_id']);
                        if (!empty($followerObj))
                            $notification_table->addNotification($followerObj, $sitestore, $sitestoreproduct, 'sitestoreproduct_create');
                    }
                }
            }

            //UPDATE KEYWORDS IN SEARCH TABLE
            if (!empty($keywords)) {
                Engine_Api::_()->getDbTable('search', 'core')->update(array('keywords' => $keywords), array('type = ?' => 'sitestoreproduct_product', 'id = ?' => $sitestoreproduct->product_id));
            }

//      //OVERVIEW IS ENABLED OR NOT
//      $allowOverview = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "overview");
//
//      //EDIT IS ENABLED OR NOT
//      $alloweEdit = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "edit");
            //REDIRECTION IN CONFIGURABLE AND DOWNLOADABLE PRODUCTS
            if ($sitestoreproduct_values['product_type'] == 'bundled' && !empty($isConfigurationTabRequired)) {

                //Mobile redirect, prevent bug when loading page via AJAX
                if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                    $this->view->product_id = $product_id;
                    $this->view->create_bundle_completed = true;
                    return;
                }

                return $this->_helper->redirector->gotoRoute(array('action' => 'bundle-product-attributes', 'product_id' => $product_id), 'sitestoreproduct_product_general', true);
            } else if ($sitestoreproduct_values['product_type'] == 'configurable' || $sitestoreproduct_values['product_type'] == 'virtual') {
                Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options')->getOptions()->getTable()->flushCache();
                Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'maps')->getMaps()->getTable()->flushCache();
                Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'meta')->getMeta()->getTable()->flushCache();

                //Mobile redirect, prevent bug when loading page via AJAX
                if ($sitestoreproduct_values['product_type'] == 'configurable' && !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                    $this->view->product_id = $product_id;
                    $this->view->option_id = $option_id;
                    $this->view->create_configurable_completed = true;
                    return;
                }

                if ($sitestoreproduct_values['product_type'] == 'configurable') {
                    return $this->_helper->redirector->gotoRoute(array('action' => 'edit', 'product_id' => $sitestoreproduct->product_id, 'saved' => '1'), "sitestoreproduct_specific", true);
                }

                return $this->_helper->redirector->gotoRoute(array('controller' => 'siteform', 'action' => 'index', 'option_id' => $option_id, 'product_id' => $product_id), 'sitestoreproduct_extended', true);
            } else if ($sitestoreproduct_values['product_type'] == 'downloadable') {
                return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'product_id' => $sitestoreproduct->product_id, 'saved' => '1'), "sitestoreproduct_files", true);
            } else {

                //Mobile redirect, prevent bug when loading page via AJAX
                if ($sitestoreproduct_values['product_type'] == 'simple' && !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                    $this->view->product_id = $product_id;
                    $this->view->create_simple_completed = true;
                    return;
                }

                return $this->_helper->redirector->gotoRoute(array('action' => 'edit', 'product_id' => $sitestoreproduct->product_id, 'saved' => '1'), "sitestoreproduct_specific", true);
            }
//      //CHECK FOR LEVEL SETTING
//      else if ($allowOverview && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.overview', 1) && $alloweEdit) {
//        return $this->_helper->redirector->gotoRoute(array('action' => 'overview', 'product_id' => $sitestoreproduct->product_id, 'saved' => '1'), "sitestoreproduct_specific", true);
//      } else if (Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "photo")) {
//        return $this->_helper->redirector->gotoRoute(array('product_id' => $sitestoreproduct->product_id, 'saved' => '1'), "sitestoreproduct_albumspecific", true);
//      } else if (Engine_Api::_()->sitestoreproduct()->allowVideo($sitestoreproduct, $viewer)) {
//        return $this->_helper->redirector->gotoRoute(array('product_id' => $sitestoreproduct->product_id, 'saved' => '1'), "sitestoreproduct_videospecific", true);
//      } else {
//        return $this->_helper->redirector->gotoRoute(array('product_id' => $sitestoreproduct->product_id, 'slug' => $sitestoreproduct->getSlug()), "sitestoreproduct_entry_view", true);
//      }
        }
    }

    public function configurationPriceAction() {

        $configuration_price = $this->_getParam('price', null);
        $show_msg = $this->_getParam('show_msg', null);
        if (!empty($configuration_price)) {
            $priceWithCurrency = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($configuration_price);

            echo ($this->_getParam('show_msg', null)) ? $priceWithCurrency . '*' : $priceWithCurrency;
            exit();
        }
    }

    // AJAX : FETCH CHILD OPTIONS AT PRODUCT PROFILE PAGE FOR CONFIGURABLE / VIRTUAL PRODUCTS
    public function getAttributeChildAction() {
        $combination_attribute_id = $this->_getParam('combination_attribute_id', null);
        $field_id = $this->_getParam('field_id', null);
        $product_id = $this->_getParam('product_id', null);

        //WORK FOR THE VAT IN CONFIGURABLE PRODUCT
        $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj);
        //WORK FOR THE VAT IN CONFIGURABLE PRODUCT


        $order = $this->_getParam('order', null);
        $parent_attributes = $this->_getParam('parent_attribute_ids', null);
        $parent_attribute_ids = array();

        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('combinationAttributeMap', 'sitestoreproduct');
        $combinationAttributeMapsTableName = $combinationAttributeMapsTable->info('name');
        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');
        $combinationsTableName = $combinationsTable->info('name');

        if (!empty($parent_attributes)) {
            foreach ($parent_attributes as $combination_attribute) {
                $parent_attribute_ids [$combination_attribute] = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')->where('combination_attribute_id =?', $combination_attribute)->where('product_id =?', $product_id)->query()->fetchColumn();
            }
        }

        $attribute_id = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')->where('combination_attribute_id =?', $combination_attribute_id)->where('product_id =?', $product_id)->where('field_id =?', $field_id)->query()->fetchColumn();

        $combination_ids = $combinationAttributeMapsTable->select()
                        ->from($combinationAttributeMapsTable->info('name'), 'combination_id')
                        ->where('attribute_id = ?', $attribute_id)
                        ->query()->fetchAll();

        if (!empty($combination_ids)) {
            foreach ($combination_ids as $combination_id) {
                $attributrCombinations[] = $combinationAttributeMapsTable->select()
                                ->setIntegrityCheck(false)
                                ->from($combinationAttributeMapsTableName, 'attribute_id')
                                ->joinleft($combinationsTableName, "$combinationAttributeMapsTableName.combination_id = $combinationsTableName.combination_id")
                                ->where("$combinationAttributeMapsTableName.combination_id =?", $combination_id)
                                ->where("$combinationAttributeMapsTableName.attribute_id != ?", $attribute_id)
                                ->where("$combinationsTableName.quantity > ?", 0)
                                ->where("$combinationsTableName.status = ?", 1)
                                ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            }
        }


        if (!empty($attributrCombinations)) {
            $index = 0;
            $attributeIds = array();
            $parentChildAttributeInfo = array();

            foreach ($attributrCombinations as $attributes) {
                $breakLoop = 0;
                if (!empty($parent_attribute_ids)) {
                    foreach ($parent_attribute_ids as $parent_id) {
                        if (!in_array($parent_id, $attributes)) {
                            $breakLoop = 1;
                            break;
                        }
                    }
                }
                if (!empty($breakLoop))
                    continue;
                foreach ($attributes as $attribute) {
                    if (!in_array($attribute, $attributeIds)) {
                        $attributeIds[] = $attribute;
                    } else
                        continue;
                    $attribute_order = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'order')->where('attribute_id =?', $attribute)->query()->fetchColumn();

                    if (/* $attribute_order == ($order + 1) */ $attribute_order > $order) {
                        $childAttributeInfo = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), array('field_id', 'combination_attribute_id', 'price_increment', 'price'))->where('attribute_id =?', $attribute)->query()->fetchAll();

                        if (!empty($childAttributeInfo)) {
                            $parentChildAttributeInfo[$index]['field_id'] = $childAttributeInfo[0]['field_id'];
                            $parentChildAttributeInfo[$index]['value'] = $childAttributeInfo[0]['combination_attribute_id'];
                            $parentChildAttributeInfo[$index]['order'] = $attribute_order;

                            if (!empty($productPricesArray) && isset($productPricesArray['vatShowType'])) {
                                if (isset($productPricesArray['show_price_with_vat']) && !empty($productPricesArray['show_price_with_vat']) && isset($productPricesArray['save_price_with_vat']) && empty($productPricesArray['save_price_with_vat'])) {
                                    $vat = explode("%", $productPricesArray['vatShowType']);
                                    $childAttributeInfo[0]['price'] = @round(($childAttributeInfo[0]['price'] * 100) / (100 + $vat[0]), 2);
                                }
                                if (isset($productPricesArray['show_price_with_vat']) && empty($productPricesArray['show_price_with_vat']) && isset($productPricesArray['save_price_with_vat']) && !empty($productPricesArray['save_price_with_vat'])) {
                                    $vat = explode("%", $productPricesArray['vatShowType']);
                                    $childAttributeInfo[0]['price'] = @round(((($childAttributeInfo[0]['price'] * $vat[0]) / 100) + $childAttributeInfo[0]['price']), 2);
                                }
                            }

                            if (!empty($childAttributeInfo[0]['price_increment']))
                                $parentChildAttributeInfo[$index]['label'] = Engine_Api::_()->getDbTable('cartproductFieldOptions', 'sitestoreproduct')->getOptionLabel($childAttributeInfo[0]['field_id'], $childAttributeInfo[0]['combination_attribute_id']) . '(+' . $childAttributeInfo[0]['price'] . ')';
                            else
                                $parentChildAttributeInfo[$index]['label'] = Engine_Api::_()->getDbTable('cartproductFieldOptions', 'sitestoreproduct')->getOptionLabel($childAttributeInfo[0]['field_id'], $childAttributeInfo[0]['combination_attribute_id']) . '(-' . $childAttributeInfo[0]['price'] . ')';
                        }

                        $index++;
                    }
                }
            }
        }

        $this->view->attributeArray = Zend_Json::encode($parentChildAttributeInfo);
    }

    public function showCombinationQuantityAction() {

        $combinaiton_attributes = $this->_getParam('combination_attribute_ids', null);
        $product_id = $this->_getParam('product_id', null);
        $quantity = $this->_getParam('quantity', null);
        if (empty($combinaiton_attributes)) {
            $this->view->error_message = 0;
            return;
        }

        $combination_attribute_ids = array();
        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        foreach ($combinaiton_attributes as $field_id => $value) {
            $attribute_id = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')->where('field_id =?', $field_id)->where('combination_attribute_id =?', $value)->where('product_id =?', $product_id)->query()->fetchColumn();
            $combination_attribute_ids[] = $attribute_id;
        }

        $combination_quantity = Engine_Api::_()->sitestoreproduct()->getCombinationQuantity($combination_attribute_ids);

        if ($quantity > $combination_quantity) {
            if ($combination_quantity == 1)
                $this->view->error_message = sprintf(Zend_Registry::get('Zend_Translate')->_("Only 1 quantity of this variation is available in stock. Please enter the quantity as 1."));
            else
                $this->view->error_message = sprintf(Zend_Registry::get('Zend_Translate')->_("Only %s quantities of this variation are available in stock. Please enter the quantity less than or equal to %s."), $combination_quantity, $combination_quantity);
        } else
            $this->view->error_message = 0;
    }

    //ACTION FOR BROWSE LOCATION PRODUCTS.
    public function mapAction() {
        $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0);

        if (empty($enableLocation)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        } else {
            $this->_helper->content->setEnabled();
        }
    }

    public function showRadiusTipAction() {
        $this->_helper->layout->setLayout('default-simple');
    }

    public function createMobileAction() {

        $mobileId = $this->_getParam('store_id', null);
        $mobileSubject = Engine_Api::_()->getItem('sitestore_store', $mobileId);
        if (!empty($mobileSubject) && !Engine_Api::_()->core()->hasSubject()) {
            Engine_Api::_()->core()->setSubject($mobileSubject);
        }

        $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        $coreversion = $coremodule->version;
        if ($coreversion < '4.1.0') {
            $this->_helper->content->render();
        } else {
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled();
        }


        //ONLY LOGGED IN USER CAN CREATE
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $store_id = $this->_getParam('store_id', null);

        $this->view->page_id = $this->_getParam('page_id', null);

        $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

        //IS USER IS STORE ADMIN OR NOT
        if (empty($authValue))
            return $this->_forward('requireauth', 'error', 'core');
        else if ($authValue == 1)
            return $this->_forward('notfound', 'error', 'core');

        //SEND TAB TO TPL FILE
        $this->view->tab_selected_id = $tab_id = $this->_getParam('tab');
        $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');

        //GET SITESTORE ITEM
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

        $tableLocation = Engine_Api::_()->getDbTable('locations', 'sitestore');

        if (!empty($sitestore->location))
            $this->view->locationId = $tableLocation->select()->from($tableLocation->info('name'), 'location_id')->where('store_id = ?', $store_id)->where("location = ?", $sitestore->location)->query()->fetchColumn();
        if (!empty($this->view->locationId)) {
            $this->view->locationDetails = Engine_Api::_()->getItem('sitestore_location', $this->view->locationId);
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            if (empty($sitestore->approved) || !empty($sitestore->closed) || empty($sitestore->search) || empty($sitestore->draft) || !empty($sitestore->declined)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        } else {
            if (empty($sitestore->approved) || empty($sitestore->search) || empty($sitestore->draft) || !empty($sitestore->declined)) {
                return $this->_forward('requireauth', 'error', 'core');
            }
        }

        //FLAG FOR SHOW MARKER IN DATE
        $this->view->showMarkerInDate = $this->showMarkerInDate();

        //CHECK FOR CREATION PRIVACY
        if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "create")->isValid())
            return;

        // COUNT PRODUCT CREATED BY THIS STORE AND MAXIMUM PRODUCT CREATION LIMIT
        $this->view->current_count = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductsCountInStore($store_id);
        $this->view->quota = $quota = Engine_Api::_()->sitestoreproduct()->getProductLimit($store_id);

        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitestoreproduct')->defaultProfileId();

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");

        // WORK FOR MULTILANGUAL PRODUCT TITLE STARTS
        $settings = Engine_Api::_()->getApi('settings', 'core');
        //MULTI LANGUAGE IS ALLOWED OR NOT
        $this->view->multiLanguage = $settings->getSetting('sitestoreproduct.multilanguage', 0);

        // Comment Privacy
        $this->view->isCommentsAllow = Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product");

        //DEFAULT LANGUAGE
        $this->view->defaultLanguage = $defaultLanguage = $settings->getSetting('core.locale.locale', 'en');

        // MULTI LANGUAGE WORK
        $this->view->languageCount = 0;
        $this->view->languageData = array();
        $title_link = $this->view->add_show_hide_title_link = 'title';
        $body_link = $this->view->add_show_hide_body_link = 'body';
        $overview_link = $this->view->add_show_hide_overview_link = 'overview';
        if ($this->view->multiLanguage) {
            //GET LANGUAGE ARRAY
            $localeMultiOptions = Engine_Api::_()->sitestoreproduct()->getLanguageArray();
            $languages = $settings->getSetting('sitestoreproduct.languages');

            $this->view->languageCount = $languageCount = Count($languages);
            $this->view->languageData = array();
            if (is_array($languages)) {
                foreach ($languages as $label) {
                    $this->view->languageData[] = $label;

                    if ($this->view->languageCount >= 2 && $defaultLanguage == $label && $label != 'en') {
                        $title_link = $this->view->add_show_hide_title_link = "title_$label";
                        $body_link = $this->view->add_show_hide_title_link = "body_$label";
                        $overview_link = $this->view->add_show_hide_overview_link = "overview_$label";
                    }
                }
            }
            if (!in_array($defaultLanguage, $this->view->languageData)) {
                $this->view->defaultLanguage = 'en';
            }
        }
        // WORK FOR MULTILANGUAL PRODUCT TITLE ENDS
        //GET TINYMCE SETTINGS
        $this->view->upload_url = "";
        $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
        if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
            $this->view->upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'sitestoreproduct_general', true);
        }

        $orientation = $this->view->layout()->orientation;
        if ($orientation == 'right-to-left') {
            $this->view->directionality = 'rtl';
        } else {
            $this->view->directionality = 'ltr';
        }

        $local_language = $this->view->locale()->getLocale()->__toString();
        $local_language = explode('_', $local_language);
        $this->view->language = $local_language[0];

        $this->view->category_count = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategories(null, 1, 0, 1);
        $this->view->sitestoreproduct_render = !empty($_POST['product_type']) ? $_POST['product_type'] : null;
        $this->view->expiry_setting = $expiry_setting = Engine_Api::_()->sitestoreproduct()->expirySettings();
        $this->view->allowSellingProducts = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($store_id, false);

        //PRODUCT FLAG
        $packageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1);

        //GET PRODUCT USING LEVEL OR PACKAGE BASED SETTINGS
        if (!empty($packageEnable)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $sitestore->package_id);
            if (empty($packageObj->store_settings)) {
                $product_types = array('simple', 'configurable', 'virtual', 'grouped', 'bundled', 'downloadable');
            } else {
                $storeSettings = @unserialize($packageObj->store_settings);
                $product_types = $storeSettings['product_type'];
            }
        } else {
            $user = $sitestore->getOwner();
            $product_types = Engine_Api::_()->authorization()->getPermission($user->level_id, "sitestore_store", "product_type");
            $product_types = Zend_Json_Decoder::decode($product_types);
        }
        $this->view->countProductTypes = $countProductTypes = count($product_types);

        // CHECK ANY SHIPPING METHOD IS CREATED BY STORE OR NOT
        $this->view->shipping_method_exist = Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct')->isAnyShippingMethodExist($store_id);

        // CHECK ANY ENABLE LOCATIONS
        $this->view->isAnyCountryEnable = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->isAnyCountryEnable();

        //CHECK TO SHOW II ND STEP OF PRODUCT CREATION
        if ($this->getRequest()->isPost()) {
            $productTypeValue = $this->getRequest()->getPost();
            $this->view->productTypeName = $productTypeValue['product_type'];

            if ($productTypeValue['product_type'] == 'grouped' || $productTypeValue['product_type'] == 'bundled') {
                $currentProductCount = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getProductAvailability($store_id, $productTypeValue['product_type']);
                if ($currentProductCount < 2) {
                    $this->view->lessSimpleProductType = 1;
                    return;
                }
            }
        }

        //CHECK FOR 1ST STEP OF PRODUCT CREATION
        if (!$this->getRequest()->isPost() && !isset($productTypeValue['product_type'])) {
            //IS ANY SHIPPING METHOD EXIST WITH ENABLE STATUS
            $this->view->viewType = 1;

            if ($countProductTypes == 0) {
                $this->view->productType = 0;
                return;
            } else if ($countProductTypes == 1) {
                if (@in_array("bundled", $product_types) || @in_array("grouped", $product_types)) {
                    $productCount = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getProductAvailability($sitestore->store_id, $productTypeValue['product_type']);
                    $this->view->lessSimpleProductType = $this->view->withNoSingleProduct = ($productCount <= 1) ? 1 : 0;
                }
                $this->view->sitestoreproduct_render = end($product_types);
                ;
                $this->view->viewType = 0;
                $this->view->productTypeName = $product_types[0];
            } else
                $this->view->productType = $product_types;
        }else {
            $this->view->viewType = 0;
        }

        if ($this->view->productTypeName == 'virtual')
            $this->view->showProductInventory = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.productinventory', 1);
        else
            $this->view->showProductInventory = true;

        $this->view->directPayment = $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $this->view->isDownPaymentEnable = $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
        $this->view->allowProductCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.productcode', 1);

        //MAKE FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Mobile_Create(array('defaultProfileId' => $defaultProfileId, 'pageId' => $store_id, 'productType' => $this->view->productTypeName, 'allowedProductTypes' => $product_types));

        $temp_allow_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($store_id, false);
        if (empty($temp_allow_selling)) {
            $form->removeElement('allow_purchase');
        }

        if (!empty($_POST['product_type']) && ($_POST['product_type'] == 'configurable' || $_POST['product_type'] == 'virtual' || $_POST['product_type'] == 'downloadable')) {
            $form->search->setValue(0);
//      $form->search->setAttribs(array('disabled' => 'disabled'));
        } else {
            $form->search->setValue(1);
        }

        if (!$this->getRequest()->isPost() || isset($productTypeValue['select'])) {
            return;
        }

        $params = array();
        $params['store_id'] = $store_id;
        $params['expiry_setting'] = $expiry_setting;
        $this->saveProduct($form, $_POST, $params);
    }

    public function editMobileAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->product_id = $product_id = $this->_getParam('product_id', NULL);

        //IF STORE ID NOT SUPPLIED THEN STORE NOT FOUND
        if (empty($product_id)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        if (empty($sitestoreproduct)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
//    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($sitestoreproduct->store_id);
        $isProductEnabled = Engine_Api::_()->sitestoreproduct()->isProductEnabled();
        $this->view->directPayment = $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $this->view->isDownPaymentEnable = $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
        $this->view->allowProductCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.productcode', 1);

        //IS USER IS STORE ADMIN OR NOT
//    if (empty($authValue))
//      return $this->_forward('requireauth', 'error', 'core');
//    else if ($authValue == 1)
//      return $this->_forward('notfound', 'error', 'core');

        $settings = Engine_Api::_()->getApi('settings', 'core');

        //MULTI LANGUAGE IS ALLOWED OR NOT
        $this->view->multiLanguage = $settings->getSetting('sitestoreproduct.multilanguage', 0);

        //DEFAULT LANGUAGE
        $this->view->defaultLanguage = $defaultLanguage = $settings->getSetting('core.locale.locale', 'en');

        //MULTI LANGUAGE WORK
        $this->view->languageCount = 0;
        $this->view->languageData = array();

        $title_link = $this->view->add_show_hide_title_link = 'title';
        $body_link = $this->view->add_show_hide_body_link = 'body';
        if ($this->view->multiLanguage) {
            //GET LANGUAGE ARRAY
            $localeMultiOptions = Engine_Api::_()->sitestoreproduct()->getLanguageArray();
            $languages = $settings->getSetting('sitestoreproduct.languages');
            $this->view->languageCount = Count($languages);
            $this->view->languageData = array();
            if (is_array($languages)) {
                foreach ($languages as $label) {
                    $this->view->languageData[] = $label;
                    if ($this->view->languageCount >= 2 && $defaultLanguage == $label && $label != 'en') {
                        $title_link = $this->view->add_show_hide_title_link = "title_$label";
                        $body_link = $this->view->add_show_hide_title_link = "body_$label";
                    }
                }
            }
        }

        $this->view->sitestores_view_menu = 1;
        $listValues = array();

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $this->view->category_edit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.categoryedit', 1);
        // Comment Privacy
        $this->view->isCommentsAllow = Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product");
        $sitestoreproductinfo = $sitestoreproduct->toarray();
        $this->view->category_id = $previous_category_id = $sitestoreproduct->category_id;
        $this->view->subcategory_id = $subcategory_id = $sitestoreproduct->subcategory_id;
        $this->view->subsubcategory_id = $subsubcategory_id = $sitestoreproduct->subsubcategory_id;

        $row = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategory($subcategory_id);
        $this->view->subcategory_name = "";
        if (!empty($row)) {
            $this->view->subcategory_name = $row->category_name;
        }

        if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
            Engine_Api::_()->core()->setSubject($sitestoreproduct);
        }

        if (!$this->_helper->requireSubject()->isValid())
            return;

        // CTSTYLE-46 only fix permission for mobile
        if (!Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit')) {
            return $this->_helper->requireAuth()->forward();
        }

        //MARK IN CALENDER FLAG
        $this->view->showMarkerInDate = $this->showMarkerInDate();

        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitestoreproduct')->defaultProfileId();

        //GET PROFILE MAPPING ID
        $categoryIds = $oldCategoryIds = array();
        $categoryIds[] = $oldCategoryIds[] = $sitestoreproduct->category_id;
        $categoryIds[] = $oldCategoryIds[] = $sitestoreproduct->subcategory_id;
        $categoryIds[] = $oldCategoryIds[] = $sitestoreproduct->subsubcategory_id;

        $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIds, 0, 'profile_type');

        if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
            $categoryIds = array();
            $categoryIds[] = $_POST['category_id'];
            if (isset($_POST['subcategory_id']) && !empty($_POST['subcategory_id'])) {
                $categoryIds[] = $_POST['subcategory_id'];
            }
            if (isset($_POST['subsubcategory_id']) && !empty($_POST['subsubcategory_id'])) {
                $categoryIds[] = $_POST['subsubcategory_id'];
            }
            $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIds, 0, 'profile_type');
        }

        //GET PRODUCT USING LEVEL OR PACKAGE BASED SETTINGS
        $packageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1);
        if (!empty($packageEnable)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $sitestore->package_id);
            if (empty($packageObj->store_settings)) {
                $product_types = array('simple', 'configurable', 'virtual', 'grouped', 'bundled', 'downloadable');
            } else {
                $storeSettings = @unserialize($packageObj->store_settings);
                $product_types = $storeSettings['product_type'];
            }
        } else {
            $user = $sitestore->getOwner();
            $product_types = Engine_Api::_()->authorization()->getPermission($user->level_id, "sitestore_store", "product_type");
            $product_types = Zend_Json_Decoder::decode($product_types);
        }

        if ($sitestoreproduct->product_type == 'virtual')
            $this->view->showProductInventory = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.productinventory', 1);
        else
            $this->view->showProductInventory = true;

        //MAKE FORM
        $this->view->productTypeName = $_POST['product_type'] = $sitestoreproduct->product_type;
        $this->view->form = $form = new Sitestoreproduct_Form_Mobile_Edit(array('item' => $sitestoreproduct, 'defaultProfileId' => $defaultProfileId, 'pageId' => $sitestoreproduct->store_id, 'productType' => $sitestoreproduct->product_type, 'allowedProductTypes' => $product_types));

        // CTSTYLE-15 Mobile Compatibility
        $form->setAttrib("data-ajax", "false");

        $inDraft = 1;

        if (empty($sitestoreproduct->draft)) {
            $inDraft = 0;
            $form->removeElement('draft');
        }

        $temp_allow_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($sitestoreproduct->store_id, false);
        if (empty($temp_allow_selling)) {
            $form->removeElement('allow_purchase');
        }

        $intrestedViewerEmailCount = Engine_Api::_()->getDbtable('notifyemails', 'sitestoreproduct')->getNotifyEmail($product_id, 'COUNT(notifyemail_id)')->query()->fetchColumn();
        if (!empty($intrestedViewerEmailCount)) {
            $form->addElement('Checkbox', 'notify_viewer', array(
                'label' => $this->view->translate(array('%s viewer is', '%s viewers are', $intrestedViewerEmailCount), $this->view->locale()->toNumber($intrestedViewerEmailCount)) . ' ' . $this->view->translate('intrested in this product. Do you want to send an email about availability of this product?'),
                'value' => true,
                'order' => 102,
            ));
        }

        $form->removeElement('photo');
        $this->view->expiry_setting = $expiry_setting = Engine_Api::_()->sitestoreproduct()->expirySettings();

        // ASSIGNED THE OTHERINFO TABLE CONTENT VALUES TO FORM
        $this->view->otherInfoObj = $otherInfoObj = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->getOtherinfo($sitestoreproduct->product_id);
        $tempOtherInfos = array();
        $tempOtherInfos['out_of_stock'] = $otherInfoObj->out_of_stock;
        $tempOtherInfos['out_of_stock_action'] = $otherInfoObj->out_of_stock_action;
        $tempOtherInfos['discount'] = $otherInfoObj->discount;
        $tempOtherInfos['handling_type'] = $otherInfoObj->handling_type;
        $tempOtherInfos['discount_start_date'] = $otherInfoObj->discount_start_date;
        $tempOtherInfos['discount_end_date'] = $otherInfoObj->discount_end_date;
        $tempOtherInfos['discount_permanant'] = $otherInfoObj->discount_permanant;
        $tempOtherInfos['user_type'] = $otherInfoObj->user_type;
        $tempOtherInfos['special_vat'] = $otherInfoObj->special_vat;
        $this->view->store_id = $sitestoreproduct->store_id;

//    if( $sitestoreproduct->product_type == 'configurable' || $sitestoreproduct->product_type == 'virtual' )
//    {
//      $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($sitestoreproduct->product_id);
//      $field_meta_id = Engine_Api::_()->getDbTable('cartProductFieldMeta', 'sitestoreproduct')->isAnyConfigurationExist($option_id);
//      if( empty($field_meta_id) )
//        $form->search->setAttribs(array('disabled' => 'disabled'));
//    }
//
//    if( $sitestoreproduct->product_type == 'downloadable' )
//    {
//      $isAnyMainFileExist = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->isAnyMainFileExist($sitestoreproduct->product_id);
//      if( empty($isAnyMainFileExist) )
//        $form->search->setAttribs(array('disabled' => 'disabled'));
//    }

        if ($sitestoreproduct->product_type == 'virtual' || $sitestoreproduct->product_type == 'bundled')
            $product_info = unserialize($otherInfoObj->product_info);

        $isSitestorereservationModuleExist = Engine_Api::_()->sitestoreproduct()->isSitestorereservationModuleExist();
        if ($sitestoreproduct->product_type == 'virtual' && !empty($isSitestorereservationModuleExist)) {
            $tempOtherInfos['virtual_product_price_range'] = isset($product_info['virtual_product_price_range']) ? $product_info['virtual_product_price_range'] : null;
            $isDateSelectorEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.dateselector', 0);
            if (!empty($isDateSelectorEnable))
                $tempOtherInfos['virtual_product_date_selector'] = isset($product_info['virtual_product_date_selector']) ? $product_info['virtual_product_date_selector'] : null;
        }

        // SHOW DOWNPAYMENT VALUE PRE-FILLED
        if ($sitestoreproduct->product_type != 'grouped' && !empty($isDownPaymentEnable) && !empty($directPayment)) {
            if (empty($otherInfoObj->downpayment_value))
                $tempOtherInfos['downpayment'] = 0;
            else {
                $tempOtherInfos['downpayment'] = 1;
                $tempOtherInfos['downpaymentvalue'] = $otherInfoObj->downpayment_value;
            }
        }

        if ($sitestoreproduct->product_type == 'bundled') {
            $tempOtherInfos['weight_type'] = $otherInfoObj->weight_type;
            $tempOtherInfos['enable_shipping'] = $product_info['enable_shipping'];
            $tempOtherInfos['bundle_product_type'] = $product_info['bundle_product_type'];
        }

        if ($sitestoreproduct->product_type == 'bundled' || $sitestoreproduct->product_type == 'grouped') {
            $isSuccessMsg = $this->_getParam('success', NULL);
            if (!empty($isSuccessMsg)) {
                $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
            }
            $tempMappedIds = $otherInfoObj->mapped_ids;
            if (!empty($tempMappedIds)) {
                $productMappedIds = $tempMappedIds = Zend_Json_Decoder::decode($tempMappedIds);
                $productsArray = $tempProductsArray = array();

                foreach ($tempMappedIds as $tempIdsKey => $product_id) {
                    $productTitle = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductTitle($product_id);
                    if (empty($productTitle)) {
                        unset($productMappedIds[$tempIdsKey]);
                        continue;
                    }
                    $tempProductsArray['title'] = $productTitle;
                    $tempProductsArray['id'] = $product_id;
                    $productsArray[] = $tempProductsArray;
                }

                $this->view->tempMappedIdsStr = @implode(",", $productMappedIds);
                $this->view->productArray = $productsArray;
            }
        }

        if (empty($otherInfoObj->handling_type))
            $tempOtherInfos['discount_price'] = $otherInfoObj->discount_value;
        else
            $tempOtherInfos['discount_rate'] = $otherInfoObj->discount_value;


        //SAVE SITESTOREPRODUCT ENTRY
        if (!$this->getRequest()->isPost()) {

            //prepare tags
            $sitestoreproductTags = $sitestoreproduct->tags()->getTagMaps();
            $tagString = '';

            foreach ($sitestoreproductTags as $tagmap) {

                if ($tagString != '')
                    $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();
            }

            $this->view->tagNamePrepared = $tagString;
            $form->tags->setValue($tagString);
            $tempValues = array_merge($sitestoreproduct->toArray(), $tempOtherInfos);

            if (array_key_exists("weight", $tempValues)) {
                $getPercisionValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.rate.precision', 2);
                $tempValues['weight'] = @round($tempValues['weight'], $getPercisionValue);
            }

            $form->populate($tempValues);

            if ($sitestoreproduct->end_date && $sitestoreproduct->end_date != '0000-00-00 00:00:00') {
                $form->end_date_enable->setValue(1);
                // Convert and re-populate times
                $end = strtotime($sitestoreproduct->end_date);
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $end = date('Y-m-d H:i:s', $end);
                date_default_timezone_set($oldTz);

                $form->populate(array(
                    'end_date' => $end,
                ));
            } else if (empty($sitestoreproduct->end_date) || $sitestoreproduct->end_date == '0000-00-00 00:00:00') {
                $date = (string) date('Y-m-d');
                $form->end_date->setValue($date . ' 00:00:00');
            }

            // Convert and re-populate times
            $end = strtotime($sitestoreproduct->start_date);
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($viewer->timezone);
            $end = date('Y-m-d H:i:s', $end);
            date_default_timezone_set($oldTz);

            $form->populate(array(
                'start_date' => $end,
            ));

            if ($sitestoreproduct->product_type != 'grouped' && (empty($otherInfoObj->discount_start_date) || $otherInfoObj->discount_start_date == '0000-00-00 00:00:00')) {
                $date = (string) date('Y-m-d');
                $form->discount_start_date->setValue($date . ' 00:00:00');
            }

            if ($sitestoreproduct->product_type != 'grouped' && (empty($otherInfoObj->discount_end_date) || $otherInfoObj->discount_end_date == '0000-00-00 00:00:00')) {
                $toDate = (string) date('Y-m-d', strtotime("+1 Month"));
                $form->discount_end_date->setValue($toDate . ' 00:00:00');
            }


            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            foreach ($roles as $role) {
                if ($form->auth_view) {
                    if (1 == $auth->isAllowed($sitestoreproduct, $role, "view")) {
                        $form->auth_view->setValue($role);
                    }
                }

                if ($form->auth_comment) {
                    if (1 == $auth->isAllowed($sitestoreproduct, $role, "comment")) {
                        $form->auth_comment->setValue($role);
                    }
                }
            }

            $roles_photo = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            foreach ($roles_photo as $role_photo) {
                if ($form->auth_photo) {
                    if (1 == $auth->isAllowed($sitestoreproduct, $role_photo, "photo")) {
                        $form->auth_photo->setValue($role_photo);
                    }
                }
            }

            $videoEnable = Engine_Api::_()->sitestoreproduct()->enableVideoPlugin();
            if ($videoEnable) {
                $roles_video = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                foreach ($roles_video as $role_video) {
                    if ($form->auth_video) {
                        if (1 == $auth->isAllowed($sitestoreproduct, $role_video, "video")) {
                            $form->auth_video->setValue($role_video);
                        }
                    }
                }
            }

            if (Engine_Api::_()->sitestoreproduct()->listBaseNetworkEnable()) {
                if (empty($sitestoreproduct->networks_privacy)) {
                    $form->networks_privacy->setValue(array(0));
                }
            }

            //POPULATE USER TAX
            $form->populate(array('user_tax' => @unserialize($sitestoreproduct->user_tax)));

            return;
        }

        // IF POST NO ACCORDIAN SET
        $this->view->form_post = 1;

        //REMOVE VALIDATORS ACCORDING CONDITIONS
        if ($_POST['product_type'] != "grouped") {

            if (!empty($_POST['stock_unlimited'])) {
                $form->in_stock->setValidators(array());
            }

            if ($_POST['discount'] == 0) {
                $form->discount_rate->setValidators(array());
                $form->discount_price->setValidators(array());
                $form->discount_start_date->setValidators(array());
                $form->discount_end_date->setValidators(array());
            } else if ($_POST['handling_type'] == 0) {
                $form->discount_rate->setValidators(array());
            } else {
                $form->discount_price->setValidators(array());
            }


            if ($_POST['product_type'] == "bundled")
                if (!empty($_POST['weight_type']))
                    $form->weight->setValidators(array());
        }

        $getFormElements = $this->getRequest()->getPost();

        if (@array_key_exists('section_id', $getFormElements))
            unset($getFormElements['section_id']);

        if (@array_key_exists('inputsection_id', $getFormElements))
            unset($getFormElements['inputsection_id']);

        //FORM VALIDATION
        if (empty($isProductEnabled) || !$form->isValid($getFormElements)) {
            return;
        }

        //CATEGORY IS REQUIRED FIELD
        if (isset($_POST['category_id']) && empty($_POST['category_id'])) {
            $error = $this->view->translate('Please complete Category field - it is required.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        if ($_POST['product_type'] != "grouped" && !empty($_POST['discount']) && empty($_POST['discount_permanant']) && empty($_POST['discount_end_date']['date'])) {
            $error = $this->view->translate('Please enter Discount end Date - it is required.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        //VARIOUS LOGICAL CHECKS BITWEEN DIFFERENT PRODUCT ATTRIBUTES
        if ($_POST['product_type'] != "grouped" && (isset($_POST['stock_unlimited']) && empty($_POST['stock_unlimited'])) && !empty($_POST['min_order_quantity']) && $_POST['min_order_quantity'] > $_POST['in_stock']) {
            $error = $this->view->translate('Minimum Order Quantity can not be greater than In Stock value.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        if ($_POST['product_type'] != "grouped" && !empty($_POST['max_order_quantity']) && $_POST['max_order_quantity'] < $_POST['min_order_quantity']) {
            $error = $this->view->translate('Minimum Order Quantity can not be greater than Maximum Order Quantity.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        if ($_POST['product_type'] != "grouped" && $_POST['discount'] == 1 && $_POST['handling_type'] == 0 && $_POST['discount_price'] >= $_POST['price']) {
            $error = $this->view->translate('Discount can not be more than and equal to actual price.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
        }

        if (($_POST['product_type'] == "grouped" || $_POST['product_type'] == 'bundled') && (empty($_POST['product_ids']) || COUNT(explode(',', $_POST['product_ids'])) <= 1)) {
            $error = $this->view->translate('You have not configured any products for this product. Please select atleast two products for creating this products.');
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);

            if (!empty($_POST['product_ids'])) {
                $productsArray = $tempProductsArray = array();
                $this->view->tempMappedIdsStr = $_POST['product_ids'];
                $tempMappedIds = explode(',', $_POST['product_ids']);
                foreach ($tempMappedIds as $product_id) {
                    $productTitle = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductTitle($product_id);
                    if (empty($productTitle))
                        continue;
                    $tempProductsArray['title'] = $productTitle;
                    $tempProductsArray['id'] = $product_id;
                    $productsArray[] = $tempProductsArray;
                }
                $this->view->productArray = $productsArray;
            }
            return;
        }

        //SETTING VALUES IN ARRAY FOR SITESTOREPRODUCT_OTHERINFO TABLE
        $sitestoreproductValues = array();
        $sitestoreproduct_values = $form->getValues();

//    if(empty ($temp_allow_selling)){
//        $sitestoreproduct_values['allow_purchase'] = 0;
//    }

        $emailToViewers = (bool) isset($sitestoreproduct_values['notify_viewer']) ? $sitestoreproduct_values['out_of_stock_action'] : false;
        unset($sitestoreproduct_values['notify_viewer']);

        //GET FORM VALUES
        $values = $sitestoreproduct_values;
        if (isset($values['user_tax'])) {
            $values['user_tax'] = @serialize($values['user_tax']);
        }

        // SAVE VALUES IN OHERINFO TABLE.
        if (!empty($values['discount'])) {
            if (empty($values['handling_type'])) {
                $otherInfoObj->discount_amount = @round($values['discount_price'], 2);
                $otherInfoObj->discount_value = @round($values['discount_price'], 2);
                $otherInfoObj->discount_percentage = @round($values['discount_price'] * 100 / $values['price'], 2);
            } else {
                $otherInfoObj->discount_amount = @round(($values['discount_rate'] * $values['price'] / 100), 2);
                $otherInfoObj->discount_value = @round($values['discount_rate'], 2);
                $otherInfoObj->discount_percentage = @round($values['discount_rate'], 2);
            }
        }

        // SAVE DOWNPAYMENT VALUE
        if ($_POST['product_type'] != "grouped" && !empty($isDownPaymentEnable) && !empty($directPayment) && !empty($isSitestorereservationModuleExist)) {
            if (!empty($values['downpayment']))
                $otherInfoObj->downpayment_value = $values['downpaymentvalue'];
            else
                $otherInfoObj->downpayment_value = 0;
            unset($tempOtherInfos['downpayment']);
            unset($tempOtherInfos['downpaymentvalue']);
        }

        unset($tempOtherInfos['enable_shipping']);
        unset($tempOtherInfos['bundle_product_type']);

        if (!empty($isSitestorereservationModuleExist)) {
            unset($tempOtherInfos['virtual_product_price_range']);
            unset($tempOtherInfos['virtual_product_date_selector']);
        }

        // ADDING SPECIAL VAT IN OTHERINFO
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0) && isset($values['special_vat'])) {
            if (empty($values['special_vat'])) {
                $values['special_vat'] = null;
                $otherInfoObj->special_vat = null;
            }
        }

        foreach ($tempOtherInfos as $key => $value) {
            if ($key == 'discount_price' || $key == 'discount_rate')
                continue;
            else {
                if (isset($values[$key]))
                    $otherInfoObj->$key = $values[$key];
            }
            unset($values[$key]);
        }

        if ($values['product_type'] == 'grouped' || $values['product_type'] == 'bundled') {
            $mappedIdsArray = @explode(',', $values['product_ids']);
            $otherInfoObj->mapped_ids = Zend_Json_Encoder::encode($mappedIdsArray);

            if ($values['product_type'] == 'bundled') {
                $otherInfoObj->product_info = @serialize(array('enable_shipping' => $values['enable_shipping'], 'bundle_product_type' => $values['bundle_product_type']));
            }
        }

        if ($values['product_type'] == 'virtual' && !empty($isSitestorereservationModuleExist)) {
            $tempVirtualProductInfo = array();
            if (!empty($sitestoreproduct_values['virtual_product_price_range']))
                $tempVirtualProductInfo['virtual_product_price_range'] = $values['virtual_product_price_range'];

            $isDateSelectorEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.dateselector', 0);
            if (!empty($isDateSelectorEnable))
                $tempVirtualProductInfo['virtual_product_date_selector'] = $values['virtual_product_date_selector'];

            if (!empty($tempVirtualProductInfo)) {
                if (!empty($otherInfoObj->product_info)) {
                    $tempProductInfo = unserialize($otherInfoObj->product_info);
                    $otherInfoObj->product_info = serialize(array_merge($tempProductInfo, $tempVirtualProductInfo));
                } else {
                    $otherInfoObj->product_info = serialize($tempVirtualProductInfo);
                }
            }
        }

        $otherInfoObj->save();

        $tags = preg_split('/[,]+/', $values['tags']);
        $tags = array_filter(array_map("trim", $tags));

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            if (Engine_Api::_()->sitestoreproduct()->listBaseNetworkEnable() && isset($values['networks_privacy']) && !empty($values['networks_privacy']) && in_array(0, $values['networks_privacy'])) {
                $values['networks_privacy'] = new Zend_Db_Expr('NULL');
                $form->networks_privacy->setValue(array(0));
            }
            if ($expiry_setting == 1 && $values['end_date_enable'] == 1) {
                // Convert times
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $end = strtotime($values['end_date']);
                date_default_timezone_set($oldTz);
                $values['end_date'] = date('Y-m-d H:i:s', $end);
            } elseif ($expiry_setting == 1 && isset($values['end_date'])) {
                $values['end_date'] = NULL;
            } elseif (isset($values['end_date'])) {
                unset($values['end_date']);
            }

            // Convert times
            if (isset($values['start_date']) && !empty($values['start_date'])) {
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $end = strtotime($values['start_date']);
                date_default_timezone_set($oldTz);
                $values['start_date'] = date('Y-m-d H:i:s', $end);
            }

            // EMPTY KEYS ARE COMING FROM THE FIELD TYPE. FOR REMOVING THEME FOLLOW THE FOLLOWING STEPS
            $tempFormValuesArray = array();
            foreach ($values as $key => $value) {
                if (!empty($key))
                    $tempFormValuesArray[$key] = $value;
            }

            $values = $tempFormValuesArray;

            if (empty($values['max_order_quantity'])) {
                $values['max_order_quantity'] = NULL;
            }

            if ($_POST['product_type'] == 'grouped')
                $values['stock_unlimited'] = 1;

            $sitestoreproduct->setFromArray($values);
            $sitestoreproduct->modified_date = date('Y-m-d H:i:s');
            $sitestoreproduct->tags()->setTagMaps($viewer, $tags);


            if (!empty($_POST['section_id']) && !empty($_POST['inputsection_id']) && ($_POST['section_id'] == 'new')) {
                $tableSections = Engine_Api::_()->getDbTable('sections', 'sitestoreproduct');
                $tableSectionsName = $tableSections->info('name');

                $row_info = $tableSections->fetchRow($tableSections->select()->from($tableSectionsName, 'max(sec_order) AS sec_order'));
                $sec_order = $row_info['sec_order'] + 1;
                $row = $tableSections->createRow();
                $row->section_name = $_POST['inputsection_id'];
                $row->sec_order = $sec_order;
                $row->store_id = $sitestoreproduct->store_id;
                $newsec_id = $row->save();
                $sitestoreproduct->section_id = $newsec_id;
            }

            if (@array_key_exists("section_id", $_POST) && ($_POST['section_id'] != 'new'))
                $sitestoreproduct->section_id = $_POST['section_id'];



            $sitestoreproduct->save();

            //SAVE CUSTOM FIELDS
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($sitestoreproduct);
            $customfieldform->saveValues();
            if ($customfieldform->getElement('submit_addtocart')) {
                $customfieldform->removeElement('submit_addtocart');
            }

            // SHOW A MESSAGE IF CATEGORY IS CHANGED AND COMBINATIONS EXIST (CONFIGURABLE / VIRTUAL PRODUCTS)
//      if (($_POST['product_type'] == 'configurable' || $_POST['product_type'] == 'virtual')) {
//        $allowCombinations = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.combination', 1);
//        $combinations = Engine_Api::_()->sitestoreproduct()->getCombinations($product_id);
//        if (!empty($allowCombinations) && (count($combinations) != 0)) {
//          $profileTypes = array();
//          if (!empty($oldCategoryIds[0]))
//            $profileTypes['category_id'] = Engine_Api::_()->getItem('sitestoreproduct_category', $oldCategoryIds[0])->profile_type;
//          if (!empty($oldCategoryIds[1]))
//            $profileTypes['subcategory_id'] = Engine_Api::_()->getItem('sitestoreproduct_category', $oldCategoryIds[1])->profile_type;
//          if (!empty($oldCategoryIds[2]))
//            $profileTypes['subsubcategory_id'] = Engine_Api::_()->getItem('sitestoreproduct_category', $oldCategoryIds[2])->profile_type;
//
//          if ($values['category_id'] != $oldCategoryIds[0])
//            $deleteCombinationMessage = 1;
//          elseif (empty($profileTypes['category_id']) && $values['subcategory_id'] != $oldCategoryIds[1])
//            $deleteCombinationMessage = 1;
//          elseif (empty($profileTypes['category_id']) && empty($profileTypes['subcategory_id']) && $values['subsubcategory_id'] != $oldCategoryIds[2])
//            $deleteCombinationMessage = 1;
//        }
//      }

            if (isset($values['category_id']) && !empty($values['category_id'])) {
                $categoryIds = array();
                $categoryIds[] = $sitestoreproduct->category_id;
                $categoryIds[] = $sitestoreproduct->subcategory_id;
                $categoryIds[] = $sitestoreproduct->subsubcategory_id;
                $sitestoreproduct->profile_type = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIds, 0, 'profile_type');
                if ($sitestoreproduct->profile_type != $previous_profile_type) {

                    $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'values');
                    $fieldvalueTable->delete(array('item_id = ?' => $sitestoreproduct->product_id));

                    Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'search')->delete(array(
                        'item_id = ?' => $sitestoreproduct->product_id,
                    ));

                    if (!empty($sitestoreproduct->profile_type) && !empty($previous_profile_type)) {
                        //PUT NEW PROFILE TYPE
                        $fieldvalueTable->insert(array(
                            'item_id' => $sitestoreproduct->product_id,
                            'field_id' => $defaultProfileId,
                            'index' => 0,
                            'value' => $sitestoreproduct->profile_type,
                        ));
                    }
                }
                $sitestoreproduct->save();
            }

            //NOT SEARCHABLE IF SAVED IN DRAFT MODE
            if (!empty($sitestoreproduct->draft)) {
                $sitestoreproduct->search = 0;
                $sitestoreproduct->save();
            }

            if ($sitestoreproduct->draft == 0 && $sitestoreproduct->search && $inDraft) {
                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                $activityFeedType = null;
                if (Engine_Api::_()->sitestore()->isStoreOwner($sitestore) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
                    $activityFeedType = 'sitestoreproduct_admin_new';
                elseif ($sitestore->all_post || Engine_Api::_()->sitestore()->isStoreOwner($sitestore))
                    $activityFeedType = 'sitestoreproduct_new';

                if ($activityFeedType) {
                    $action = $actionTable->addActivity($sitestoreproduct->getOwner(), $sitestore, $activityFeedType);
                    Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
                }
                //MAKE SURE ACTION EXISTS BEFOR ATTACHING THE NOTE TO THE ACTIVITY
                if ($action != null) {
                    $actionTable->attachActivity($action, $sitestoreproduct, Activity_Model_Action::ATTACH_MULTI);
                }
            }

            //CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            if (empty($values['auth_view'])) {
                $values['auth_view'] = array("everyone");
            }

            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = array("everyone");
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($sitestoreproduct, $role, "view", ($i <= $viewMax));
                $auth->setAllowed($sitestoreproduct, $role, "comment", ($i <= $commentMax));
            }

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            if ($values['auth_photo'])
                $auth_photo = $values['auth_photo'];
            else
                $auth_photo = "owner";
            $photoMax = array_search($auth_photo, $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($sitestoreproduct, $role, "photo", ($i <= $photoMax));
            }

            $roles_video = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            if (!isset($values['auth_video']) && empty($values['auth_video'])) {
                $values['auth_video'] = "owner";
            }

            $videoMax = array_search($values['auth_video'], $roles_video);
            foreach ($roles_video as $i => $role_video) {
                $auth->setAllowed($sitestoreproduct, $role_video, "video", ($i <= $videoMax));
            }

            if ($previous_category_id != $sitestoreproduct->category_id) {
                Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct')->editProductCategory($sitestoreproduct->product_id, $previous_category_id, $sitestoreproduct->category_id, $sitestoreproduct->getType());
            }

            if (!empty($emailToViewers) && !empty($intrestedViewerEmailCount)) {
                $intrestedViewerEmailSelect = Engine_Api::_()->getDbtable('notifyemails', 'sitestoreproduct')->getNotifyEmail($product_id, 'buyer_email');
                $intrestedViewerEmail = Engine_Api::_()->getDbtable('notifyemails', 'sitestoreproduct')->fetchAll($intrestedViewerEmailSelect);

                foreach ($intrestedViewerEmail as $email) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($email['buyer_email'], 'sitestoreproduct_notify_to_viewer', array(
                        'object_title' => $this->view->htmlLink($sitestoreproduct->getHref(), $sitestoreproduct->getTitle())
                    ));
                }
            }

            $db->commit();
//      if(!empty($deleteCombinationMessage))
//        $this->view->form = $form->addNotice(sprintf(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully. Categories of this product has been changed, so you should delete all the combinations on %1sclick here%2s which were mapped with the old categories.'), "<a href = " . $this->view->url(array('controller' => 'siteform', 'action' => 'product-category-attributes', 'product_id' => $product_id, 'delete_old_combinations' => 1), 'sitestoreproduct_extended', true) . " >","</a>"));
//        else
            $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($sitestoreproduct) as $action) {
                $actionTable->resetActivityBindings($action);
            }

            $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

            $getPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdmin($sitestore->store_id, $viewer->getIdentity());

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if ($_POST['product_type'] == 'grouped' || $_POST['product_type'] == 'bundled') {
            $this->_helper->redirector->gotoRoute(array('route' => 'default', 'success' => true), false);
        }
    }

    public function deleteMobileAction() {
        // LOGGED IN USER CAN DELETE PRODUCT
        if (!$this->_helper->requireUser()->isValid())
            return;

        // GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");

        // GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        // GET PRODUCT ID AND OBJECT
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $this->_getParam('product_id'));
        $this->view->store_id = $store_id = $sitestoreproduct->store_id;
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

        //only fix permission for mobile
        if (empty($sitestore) || !Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'delete')) {
            return $this->_helper->requireAuth()->forward();
        }

        // DELETE SITESTOREPRODUCT AFTER CONFIRMATION
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
            $tabId = Engine_Api::_()->sitestoreproduct()->getProductTabId();
            $store_url = Engine_Api::_()->getDbtable('stores', 'sitestore')->getStoreAttribute($store_id, 'store_url');
            $sitestoreproduct->delete();
            return $this->_helper->redirector->gotoRoute(array('controller' => 'product', 'action' => 'manage', 'store_id' => $store_id), 'sitestoreproduct_extended', false);
        }
    }

    public function copyProductMobileAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //MUST BE ABLE TO CRETE PRODUCTS
        if (!Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, "create")) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this->view->product_id = $product_id = $this->_getParam('product_id', NULL);
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        if (empty($product_id) || empty($sitestoreproduct)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");
        $this->view->store_id = $store_id = $sitestoreproduct->store_id;
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
        $this->view->directPayment = $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $this->view->isDownPaymentEnable = $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
        $this->view->allowProductCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.productcode', 1);
        $this->view->category_edit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.categoryedit', 1);
        $this->view->category_id = $previous_category_id = $sitestoreproduct->category_id;
        $this->view->subcategory_id = $subcategory_id = $sitestoreproduct->subcategory_id;
        $this->view->subsubcategory_id = $subsubcategory_id = $sitestoreproduct->subsubcategory_id;
        $row = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategory($subcategory_id);
        $subcategory_name = "";
        if (!empty($row))
            $subcategory_name = $row->category_name;
        $this->view->subcategory_name = $subcategory_name;

        //GET DEFAULT PROFILE TYPE ID
        $this->view->defaultProfileId = $defaultProfileId = Engine_Api::_()->getDbTable('metas', 'sitestoreproduct')->defaultProfileId();

        //GET PROFILE MAPPING ID
        $categoryIds = array();
        $categoryIds[] = $sitestoreproduct->category_id;
        $categoryIds[] = $sitestoreproduct->subcategory_id;
        $categoryIds[] = $sitestoreproduct->subsubcategory_id;
        $this->view->profileType = $previous_profile_type = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIds, 0, 'profile_type');

        //GET PRODUCT USING LEVEL OR PACKAGE BASED SETTINGS
        $packageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1);
        if (!empty($packageEnable)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $sitestore->package_id);
            if (empty($packageObj->store_settings)) {
                $product_types = array('simple', 'configurable', 'virtual', 'grouped', 'bundled', 'downloadable');
            } else {
                $storeSettings = @unserialize($packageObj->store_settings);
                $product_types = $storeSettings['product_type'];
            }
        } else {
            $user = $sitestore->getOwner();
            $product_types = Engine_Api::_()->authorization()->getPermission($user->level_id, "sitestore_store", "product_type");
            $product_types = Zend_Json_Decoder::decode($product_types);
        }

        if ($sitestoreproduct->product_type == 'virtual')
            $this->view->showProductInventory = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.productinventory', 1);
        else
            $this->view->showProductInventory = true;

        //MAKE FORM
        $this->view->productTypeName = $_POST['product_type'] = $sitestoreproduct->product_type;
        $this->view->form = $form = new Sitestoreproduct_Form_Mobile_Copy(array('item' => $sitestoreproduct, 'defaultProfileId' => $defaultProfileId, 'pageId' => $sitestoreproduct->store_id, 'productType' => $sitestoreproduct->product_type, 'allowedProductTypes' => $product_types));

        $form->category_id->setValue($sitestoreproduct->category_id);
        $form->subcategory_id->setValue($sitestoreproduct->subcategory_id);
        $form->subsubcategory_id->setValue($sitestoreproduct->subsubcategory_id);

        $temp_allow_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($sitestoreproduct->store_id);
        if (empty($temp_allow_selling)) {
            $form->removeElement('allow_purchase');
        }

        $form->removeElement('photo');
        $this->view->expiry_setting = $expiry_setting = Engine_Api::_()->sitestoreproduct()->expirySettings();

        // ASSIGNED THE OTHERINFO TABLE CONTENT VALUES TO FORM
        $this->view->otherInfoObj = $otherInfoObj = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->getOtherinfo($sitestoreproduct->product_id);
        $tempOtherInfos = array();
        $tempOtherInfos['overview'] = $otherInfoObj->overview;
        $tempOtherInfos['out_of_stock'] = $otherInfoObj->out_of_stock;
        $tempOtherInfos['out_of_stock_action'] = $otherInfoObj->out_of_stock_action;
        $tempOtherInfos['discount'] = $otherInfoObj->discount;
        $tempOtherInfos['handling_type'] = $otherInfoObj->handling_type;
        $tempOtherInfos['discount_start_date'] = $otherInfoObj->discount_start_date;
        $tempOtherInfos['discount_end_date'] = $otherInfoObj->discount_end_date;
        $tempOtherInfos['discount_permanant'] = $otherInfoObj->discount_permanant;
        $tempOtherInfos['user_type'] = $otherInfoObj->user_type;
        $tempOtherInfos['special_vat'] = $otherInfoObj->special_vat;

        if ($sitestoreproduct->product_type == 'virtual' || $sitestoreproduct->product_type == 'bundled')
            $product_info = unserialize($otherInfoObj->product_info);

        $isSitestorereservationModuleExist = Engine_Api::_()->sitestoreproduct()->isSitestorereservationModuleExist();
        if ($sitestoreproduct->product_type == 'virtual' && !empty($isSitestorereservationModuleExist)) {
            $tempOtherInfos['virtual_product_price_range'] = isset($product_info['virtual_product_price_range']) ? $product_info['virtual_product_price_range'] : null;
            $isDateSelectorEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.dateselector', 0);
            if (!empty($isDateSelectorEnable))
                $tempOtherInfos['virtual_product_date_selector'] = isset($product_info['virtual_product_date_selector']) ? $product_info['virtual_product_date_selector'] : null;
        }

        // SHOW DOWNPAYMENT VALUE PRE-FILLED
        if ($sitestoreproduct->product_type != 'grouped' && !empty($isDownPaymentEnable) && !empty($directPayment)) {
            if (empty($otherInfoObj->downpayment_value))
                $tempOtherInfos['downpayment'] = 0;
            else {
                $tempOtherInfos['downpayment'] = 1;
                $tempOtherInfos['downpaymentvalue'] = $otherInfoObj->downpayment_value;
            }
        }

        if ($sitestoreproduct->product_type == 'bundled' || $sitestoreproduct->product_type == 'grouped') {
            if ($sitestoreproduct->product_type == 'bundled') {
                $tempOtherInfos['weight_type'] = $otherInfoObj->weight_type;
                $tempOtherInfos['enable_shipping'] = $product_info['enable_shipping'];
                $tempOtherInfos['bundle_product_type'] = $product_info['bundle_product_type'];
            }
            $tempMappedIds = $otherInfoObj->mapped_ids;
            if (!empty($tempMappedIds)) {
                $tempMappedIds = Zend_Json_Decoder::decode($tempMappedIds);
                $productsArray = $tempProductsArray = array();
                $this->view->tempMappedIdsStr = @implode(",", $tempMappedIds);
                foreach ($tempMappedIds as $product_id) {
                    $tempProductsArray['title'] = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id)->title;
                    $tempProductsArray['id'] = $product_id;
                    $productsArray[] = $tempProductsArray;
                }
                $this->view->productArray = $productsArray;
            }
        }

        if (empty($otherInfoObj->handling_type))
            $tempOtherInfos['discount_price'] = $otherInfoObj->discount_value;
        else
            $tempOtherInfos['discount_rate'] = $otherInfoObj->discount_value;


        //SAVE SITESTOREPRODUCT ENTRY
        if (!$this->getRequest()->isPost()) {

            //prepare tags
            $sitestoreproductTags = $sitestoreproduct->tags()->getTagMaps();
            $tagString = '';

            foreach ($sitestoreproductTags as $tagmap) {

                if ($tagString != '')
                    $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();
            }

            $this->view->tagNamePrepared = $tagString;
            $form->tags->setValue($tagString);
            $tempValues = array_merge($sitestoreproduct->toArray(), $tempOtherInfos);

            if (array_key_exists("weight", $tempValues)) {
                $getPercisionValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.rate.precision', 2);
                $tempValues['weight'] = @round($tempValues['weight'], $getPercisionValue);
            }

            $tempValues['search'] = 1;
            $tempValues['draft'] = 0;
            if ($sitestoreproduct->product_type == 'configurable' || $sitestoreproduct->product_type == 'downloadable') {
                $tempValues['search'] = 0;
            }
            $form->populate($tempValues);

            if ($sitestoreproduct->end_date && $sitestoreproduct->end_date != '0000-00-00 00:00:00') {
                $form->end_date_enable->setValue(1);
                // Convert and re-populate times
                $end = strtotime($sitestoreproduct->end_date);
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $end = date('Y-m-d H:i:s', $end);
                date_default_timezone_set($oldTz);

                $form->populate(array(
                    'end_date' => $end,
                ));
            } else if (empty($sitestoreproduct->end_date) || $sitestoreproduct->end_date == '0000-00-00 00:00:00') {
                $date = (string) date('Y-m-d');
                $form->end_date->setValue($date . ' 00:00:00');
            }

            // Convert and re-populate times
            $end = strtotime($sitestoreproduct->start_date);
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($viewer->timezone);
            $end = date('Y-m-d H:i:s', $end);
            date_default_timezone_set($oldTz);

            $form->populate(array(
                'start_date' => $end,
            ));

            if ($sitestoreproduct->product_type != 'grouped' && (empty($otherInfoObj->discount_start_date) || $otherInfoObj->discount_start_date == '0000-00-00 00:00:00')) {
                $date = (string) date('Y-m-d');
                $form->discount_start_date->setValue($date . ' 00:00:00');
            }

            if ($sitestoreproduct->product_type != 'grouped' && (empty($otherInfoObj->discount_end_date) || $otherInfoObj->discount_end_date == '0000-00-00 00:00:00')) {
                $toDate = (string) date('Y-m-d', strtotime("+1 Month"));
                $form->discount_end_date->setValue($toDate . ' 00:00:00');
            }


            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            foreach ($roles as $role) {
                if ($form->auth_view) {
                    if (1 == $auth->isAllowed($sitestoreproduct, $role, "view")) {
                        $form->auth_view->setValue($role);
                    }
                }

                if ($form->auth_comment) {
                    if (1 == $auth->isAllowed($sitestoreproduct, $role, "comment")) {
                        $form->auth_comment->setValue($role);
                    }
                }
            }

            $roles_photo = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
            foreach ($roles_photo as $role_photo) {
                if ($form->auth_photo) {
                    if (1 == $auth->isAllowed($sitestoreproduct, $role_photo, "photo")) {
                        $form->auth_photo->setValue($role_photo);
                    }
                }
            }

            $videoEnable = Engine_Api::_()->sitestoreproduct()->enableVideoPlugin();
            if ($videoEnable) {
                $roles_video = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
                foreach ($roles_video as $role_video) {
                    if ($form->auth_video) {
                        if (1 == $auth->isAllowed($sitestoreproduct, $role_video, "video")) {
                            $form->auth_video->setValue($role_video);
                        }
                    }
                }
            }

            if (Engine_Api::_()->sitestoreproduct()->listBaseNetworkEnable()) {
                if (empty($sitestoreproduct->networks_privacy)) {
                    $form->networks_privacy->setValue(array(0));
                }
            }

            //POPULATE USER TAX
            if (!empty($sitestoreproduct->user_tax))
                $form->populate(array('user_tax' => @unserialize($sitestoreproduct->user_tax)));

            return;
        }

        $params = array();
        $params['copy_product'] = 1;
        $params['store_id'] = $store_id;
        $params['expiry_setting'] = $expiry_setting;
        $this->saveProduct($form, $_POST, $params);
    }

    public function orderPayoutAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $order_id = $this->_getParam('order_id');
        $gateway_id = $this->_getParam('gateway_id');
        $store_id = $this->_getParam('store_id');
        if ($this->getRequest()->isPost()) {
            $gateway = Engine_Api::_()->getItem('sitestoreproduct_usergateway', $gateway_id);
            $params['resource_id'] = $order_id;
            $params['resource_type'] = 'sitestoreproduct_order';
            $params['store_id'] = $store_id;
            $data = $gateway->getPlugin()->payoutTransaction($params);
            $message = $data['message'];

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array($message)
            ));
        }
        $this->renderScript('index/order-payout.tpl');
    }

    public function orderRefundAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $order_id = $this->_getParam('order_id');
        $gateway_id = $this->_getParam('gateway_id');
        $store_id = $this->_getParam('store_id');
        if ($this->getRequest()->isPost()) {
            $gateway = Engine_Api::_()->getItem('sitestoreproduct_usergateway', $gateway_id);
            $params['resource_id'] = $order_id;
            $params['resource_type'] = 'sitestoreproduct_order';
            $params['store_id'] = $store_id;
            $data = $gateway->getPlugin()->refundTransaction($params);
            $message = $data['message'];
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array($message)
            ));
        }
        $this->renderScript('index/order-refund.tpl');
    }

    public function dummyAction() {

        $data = new Sitestoreproduct_Model_DbTable_Orderaddresses();
        $orderid = 3;
        $value = $data->getAddress($orderid);

        print_r($value);
    }

}
