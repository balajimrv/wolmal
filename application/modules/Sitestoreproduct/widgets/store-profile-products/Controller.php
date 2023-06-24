<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_StoreProfileProductsController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        $this->view->directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
        $this->view->isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);

        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            //SITEMOBILE CODE
            $this->view->isajax = $this->_getParam('isajax', false);
            if ($this->view->isajax) {
                $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            }
            $this->view->viewmore = $this->_getParam('viewmore', false);
            $this->view->is_ajax_load = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                if ($this->_getParam('contentpage', 1) > 1 || $this->_getParam('page', 1) > 1)
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                $this->getElement()->removeDecorator('Title');
            }
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->temp_tab = $request->getParam('tab', null);
        $this->view->user_layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
        //SET NO RENDER IF NO SUBJECT     
        $this->view->ajaxView = $ajaxView = $this->_getParam('ajaxView', false);
        // $this->view->showRating = 1; //$this->_getParam('showRating', 1);

        $this->view->params = $params = $this->_getAllParams();
        if ($this->_getParam('ajaxView', false)) {
            $this->view->ajaxView = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->ajaxView = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
                $this->view->showContent = true;
            }
        } else {
            $this->view->showContent = true;
        }



        $this->view->load_content = $load_content = $this->_getParam('load_content', 0);
        $temp_store_id = $this->_getParam('store_id', 1);
        $temp_is_subject = $this->_getParam('temp_is_subject', 1);
        $this->view->temp_layouts_views = $this->_getParam('temp_layouts_views', null);
        $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));
        $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
        $this->view->showInStock = $this->_getParam('in_stock', 1);
        $this->view->showLocation = $this->_getParam('showLocation', 0);
        $this->view->identity = $this->_getParam('identity', $this->view->identity);
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_both');
        $this->view->title_truncation = $this->_getParam('truncation', 50);
        $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 90);
        $this->view->postedby = $this->_getParam('postedby', 1);
        $this->view->isajax = $isAjax = $this->_getParam('isajax', null);
        $this->view->search = $search = $this->_getParam('search', null);
        $this->view->location = $location = $this->_getParam('location', null);
        $this->view->checkbox = $checkbox = $this->_getParam('checkbox', null);
        $this->view->selectbox = $selectbox = $this->_getParam('selectbox', null);
        $this->view->bottomLine = $this->_getParam('bottomLine', 1);
        $this->view->viewType = $tmpViewType = $this->_getParam('viewType', 0);
        $this->view->layouts_order = $defaultOrder = $this->_getParam('layouts_order', 2);
        $this->view->temViewType = $temViewType = $this->_getParam('temViewType', 0);
        $this->view->itemCount = $itemCount = $this->_getParam('itemCount', 12);
        $this->view->orderby = $tempOrderBy = $this->_getParam('orderby', 'product_id');
        $this->view->showClosed = $tempShowClosed = $this->_getParam('showClosed', 1);
        $this->view->columnWidth = $tempColumnWidth = $this->_getParam('columnWidth', '199');
        $this->view->columnHeight = $tempColumnHeight = $this->_getParam('columnHeight', '325');
        $this->view->searchByOptions = $this->_getParam('searchByOptions', array(1, 2, 3, 4, 5));

        $this->view->temp_product_types = $this->_getParam('temp_product_types', null);
        $temp_product_types = $this->_getParam('allowed_product_types', null);
        $temp_product_types = empty($temp_product_types) ? array('simple', 'grouped', 'configurable', 'virtual', 'bundled', 'downloadable') : $temp_product_types;
        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        if (!empty($viewer->level_id))
            $this->view->viewer_level_id = $viewer->level_id;

        //GET SUBJECT AND STORE ID AND STORE OWNER ID
        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->view->is_subject = 0;
            $store_id = Zend_Registry::isRegistered('store_id') ? Zend_Registry::get('store_id') : null;
            $store_id = !empty($store_id) ? $store_id : $temp_store_id;
            $this->view->storeSubject = $storeSubject = Engine_Api::_()->getItem('sitestore_store', $store_id);
        } else {
            $this->view->is_subject = empty($temp_is_subject) ? 0 : 1;
            $this->view->storeSubject = $storeSubject = Engine_Api::_()->core()->getSubject('sitestore_store');
        }
        $this->view->is_subject = 1;
        $this->view->store_id = $store_id = $storeSubject->store_id;

        if (!empty($this->view->temp_layouts_views))
            $ShowViewArray = @explode(",", $this->view->temp_layouts_views);

        $this->view->temp_layouts_views = @implode(",", $ShowViewArray);

        if (!empty($this->view->temp_product_types))
            $temp_product_types = @explode(",", $this->view->temp_product_types);

        $this->view->temp_product_types = @implode(",", $temp_product_types);

        $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();

        $this->view->list_view = 0;
        $this->view->grid_view = 0;
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

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            if ($this->view->defaultView == -1) {
                return $this->setNoRender();
            }
        }

        if (empty($temViewType))
            $temViewType = $this->view->defaultView;
        $this->view->temViewType = $this->view->defaultView = $temViewType;

//    if( !empty($ajaxView) && empty($load_content) && empty($isAjax) )
//      return;



        if (empty($storeSubject)) {
            return $this->setNoRender();
        }

        $this->view->store_id = $store_id = $storeSubject->store_id;

        if (!empty($viewer_id))
            $this->view->authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);
        if (empty($storeSubject->approved) || !empty($storeSubject->closed) || empty($storeSubject->search) || empty($storeSubject->draft) || !empty($storeSubject->declined)) {
            return $this->setNoRender();
        }

        //GET LAYOUT
        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);

//    //PACKAGE BASE PRIYACY START
//    if (!Engine_Api::_()->sitestore()->hasPackageEnable()) {
//      $canStoreCreate = Engine_Api::_()->sitestoreproduct()->getLevelSettings("allow_store_create");
//      if (empty($canStoreCreate)) {
//        return $this->setNoRender();
//      }
//    }

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'edit');

        $this->view->content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreproduct.store-profile-products', $store_id, $layout);
        $this->view->widgets = $widgets = Engine_Api::_()->sitestore()->getwidget($layout, $store_id);

        if (empty($isManageAdmin)) {
            $this->view->can_edit = $can_edit = 0;
        } else {
            $this->view->can_edit = $can_edit = 1;
        }


        $this->view->showContent = true;
        //GET SETTINGS    

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $customFieldValues = array();
        $values = array();

        if (!empty($checkbox) && $checkbox == 1) {
            $values['owner_id'] = $viewer_id;
        }

        $this->view->params = $params = $request->getParams();
        if (!isset($params['category_id']))
            $params['category_id'] = 0;
        if (!isset($params['subcategory_id']))
            $params['subcategory_id'] = 0;
        if (!isset($params['subsubcategory_id']))
            $params['subsubcategory_id'] = 0;
        $this->view->category_id = $params['category_id'];
        $this->view->subcategory_id = $params['subcategory_id'];
        $this->view->subsubcategory_id = $params['subsubcategory_id'];

        //SHOW CATEGORY NAME
        $this->view->categoryName = '';
        if ($this->view->category_id) {
            $this->view->categoryName = Engine_Api::_()->getItem('sitestoreproduct_category', $this->view->category_id)->category_name;
            $this->view->categoryObject = Engine_Api::_()->getItem('sitestoreproduct_category', $this->view->category_id);

            if ($this->view->subcategory_id) {
                $this->view->categoryName = Engine_Api::_()->getItem('sitestoreproduct_category', $this->view->subcategory_id)->category_name;
                $this->view->categoryObject = Engine_Api::_()->getItem('sitestoreproduct_category', $this->view->subcategory_id);

                if ($this->view->subsubcategory_id) {
                    $this->view->categoryName = Engine_Api::_()->getItem('sitestoreproduct_category', $this->view->subsubcategory_id)->category_name;
                    $this->view->categoryObject = Engine_Api::_()->getItem('sitestoreproduct_category', $this->view->subsubcategory_id);
                }
            }
        }

        $tempStatistics = $this->_getParam('tempStatistics', null);
        if (!empty($tempStatistics) && ($tempStatistics != 'none'))
            $statistics = explode(",", $tempStatistics);
        else if (!empty($tempStatistics) && ($tempStatistics == 'none'))
            $statistics = array();
        else
            $statistics = $this->_getParam('statistics', array("likeCount", "viewRating"));

        $this->view->statistics = $statistics;
        if (!empty($this->view->statistics) && !(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 3)) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 3) == 1) {
            $key = array_search('reviewCount', $this->view->statistics);
            if (!empty($key)) {
                unset($this->view->statistics[$key]);
            }
        }

        if (isset($params['tag']) && !empty($params['tag'])) {
            $tag = $params['tag'];
            $tag_id = $params['tag_id'];
        }

        $page = 1;
        if (isset($params['page']) && !empty($params['page'])) {
            $page = $params['page'];
        }

        //GET VALUE BY POST TO GET DESIRED PRODUCTS
        if (!empty($params)) {
            $values = array_merge($values, $params);
        }

        //FORM GENERATION
        $form = new Sitestoreproduct_Form_Search(array('type' => 'sitestoreproduct_product'));

        if (!empty($params)) {
            $form->populate($params);
        }

        $this->view->formValues = $form->getValues();

        $values = array_merge($values, $form->getValues());

        $values['selected_product_types'] = $temp_product_types;

        $orderBy = $request->getParam('orderby', null);
        if (empty($orderBy)) {
            $values['orderby'] = $tempOrderBy;
        }
        if (!empty($selectbox) && $selectbox == 'featured') {
            $values['featured'] = 1;
            $values['orderby'] = 'creation_date';
        }
        if (!empty($search)) {
            $values['search'] = $search;
        }

        if (!empty($location)) {
            $values['location'] = $location;
        }
        if (!empty($selectbox)) {
            if ($selectbox == 'selling_price_count') {
                $values['selling_price_count'] = 'selling_price_count';
            } else if ($selectbox == 'selling_item_count') {
                $values['selling_item_count'] = 'selling_item_count';
            } else {
                $values['orderby'] = $selectbox;
            }
        } else {
            $values['orderby'] = !empty($tempOrderBy) ? $tempOrderBy : 'creation_date';
        }

        $values['page'] = $page;

        //GET LISITNG FPR PUBLIC PAGE SET VALUE
        $values['type'] = 'browse';

        if (@$values['show'] == 2) {

            //GET AN ARRAY OF FRIEND IDS
            $friends = $viewer->membership()->getMembers();

            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }

            $values['users'] = $ids;
        }

        $this->view->assign($values);

        //CORE API
        $this->view->settings = $settings = Engine_Api::_()->getApi('settings', 'core');

        //CUSTOM FIELD WORK
        $customFieldValues = array_intersect_key($values, $form->getFieldElements());
        $customFieldValues = $this->removeEmptyFieldsFromFieldArray($customFieldValues);
        if ($form->show->getValue() == 3 && !isset($_GET['show'])) {
            @$values['show'] = 3;
        }

        $values['limit'] = $itemCount;
        $values['viewType'] = $tmpViewType;
        $values['showClosed'] = $tempShowClosed;
        $values['is_widget'] = 1;

        // GET PRODUCTS
//    $values['user_id'] = $viewer_id;
        $values['store_id'] = $store_id;
        $values['notifyemails'] = true;

        $this->view->detactLocation = $values['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->sitestoreproduct()->enableLocation();
        }
        if ($this->view->detactLocation) {
            $values['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $values['latitude'] = $this->_getParam('latitude', 0);
            $values['longitude'] = $this->_getParam('longitude', 0);
        }

        // if (!empty($_GET['load_content']) || empty($ajax_enabled)) {
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getSitestoreproductsPaginator($values, $customFieldValues);
//      $paginator->setItemCountPerPage($itemCount);
//      $this->view->paginator_num = $paginator->setCurrentPageNumber($values['page']);
        $getTotalItemCount = $this->view->current_count = $this->_childCount = $this->view->totalResults = $paginator->getTotalItemCount();

        $this->view->quota = $quota = Engine_Api::_()->sitestoreproduct()->getProductLimit($store_id);
        $this->view->flageSponsored = 0;

//    $this->view->search = 0;
//    if (!empty($this->_getAllParams) && Count($this->_getAllParams) > 1) {
//      $this->view->search = 1;
//    }
        //SEND FORM VALUES TO TPL
        $this->view->formValues = $values;

        //CAN CREATE PAGES OR NOT
        //$this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, "create");    
        $this->view->ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct');
        $this->view->columnWidth = $tempColumnWidth;
        $this->view->columnHeight = $tempColumnHeight;
        $this->getElement()->setTitle($this->view->translate($this->_getParam('title', 'Products')));

        $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitestoreproduct()->enableLocation();
        if (in_array("3", $ShowViewArray)) {

            if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {
                $ids = array();
                $sponsored = array();
                foreach ($paginator as $product) {
                    $id = $product->getIdentity();
                    $ids[] = $id;
                    $product_temp[$id] = $product;
                }
                $values['product_ids'] = $ids;
                $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitestoreproduct')->getLocation($values);

                foreach ($locations as $location) {
                    if ($product_temp[$location->product_id]->sponsored) {
                        $this->view->flageSponsored = 1;
                        break;
                    }
                }
                $this->view->locationsProduct = $product_temp;
            } else {
                $this->view->enableLocation = 0;
            }
        }
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            //SITEMOBILE CODE
            $limit = $this->_getParam('limit', 0);
            if ($limit) {
                $values['limit'] = $limit;
            }

            $values['page'] = $this->_getParam('page', 1);
            $this->view->isajax = $this->_getParam('isajax', 0);
            $this->view->identity = $values['identity'] = $this->_getParam('identity', $this->view->identity);
            $this->view->showInStock = $values['in_stock'] = $this->_getParam('in_stock', 1);
            $this->view->statistics = $values['statistics'] = $this->_getParam('statistics', array());
            $this->view->postedby = $values['postedby'] = $this->_getParam('postedby', 0);
            $this->view->columnWidth = $values['columnWidth'] = $this->_getParam('columnWidth', '180');
            $this->view->columnHeight = $values['columnHeight'] = $this->_getParam('columnHeight', '225');
            $this->view->title_truncationGrid = $values['truncationGrid'] = $this->_getParam('truncationGrid', 25);
            $this->view->ratingType = $values['ratingType'] = $this->_getParam('ratingType', 'rating_both');
            $this->view->params = $values;
            $values['paginator'] = true;
            $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getSitestoreproductsPaginator($values, $customFieldValues);
            $getTotalItemCount = $this->view->totalCount = $paginator->getTotalItemCount();
            $paginator->setItemCountPerPage($values['limit']);
            $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
        }


        if (!empty($this->view->isajax)) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }

        if (empty($can_edit) && empty($getTotalItemCount)) {
            return $this->setNoRender();
        }

        $this->view->sections = $sections = Engine_Api::_()->getDbTable('sections', 'sitestoreproduct')->getStoreSections($store_id);
        $isSectionAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.section.allowed', 1);
        $this->view->sectionCount = !empty($isSectionAllowed) ? count($sections) : 0;

        $this->view->categories = $categories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategories(null, 0, 0, 1);
        $this->view->categoryCount = count($categories);
    }

    //RETURN THE COUNT OF THE product
    public function getChildCount() {
        return $this->_childCount;
    }

    /*
     * Remove the empty profile fields value from the array.
     */

    private function removeEmptyFieldsFromFieldArray($customFieldValues) {
        foreach ($customFieldValues as $key => $value) {
            if (!isset($value) && empty($value))
                unset($customFieldValues[$key]);

            if (is_array($value)) {
                $getTempResponse = $this->removeEmptyFieldsFromFieldArray($value);
                if (empty($getTempResponse))
                    unset($customFieldValues[$key]);
                else
                    $customFieldValues = array_merge($getTempResponse, $customFieldValues);
            }
        }

        return $customFieldValues;
    }

}
