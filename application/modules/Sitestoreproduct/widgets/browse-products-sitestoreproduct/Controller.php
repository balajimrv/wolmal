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
class Sitestoreproduct_Widget_BrowseProductsSitestoreproductController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

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

        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();

        //GET SETTINGS     
        $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));

        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $sitestoreproductBroseProduct = Zend_Registry::isRegistered('sitestoreproductBroseProduct') ? Zend_Registry::get('sitestoreproductBroseProduct') : null;

        $this->view->params = $params = $request->getParams();

        //GET SETTINGS     
        $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));
        $this->view->paginationType = $this->_getParam('show_content', 2);

        $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
        $this->view->showinStock = $this->_getParam('in_stock', 1);
        $this->view->statistics = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "reviewCount", "viewRating"));
        $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
        $defaultOrder = $this->_getParam('layouts_order', 1);
        $this->view->ratingType = $this->_getParam('ratingType', 'rating_both');
        $this->view->title_truncation = $this->_getParam('truncation', 25);
        $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 90);
        $this->view->postedby = $this->_getParam('postedby', 1);
        $this->view->sponsoredIcon = $this->_getParam('sponsoredIcon', 1);
        $this->view->featuredIcon = $this->_getParam('featuredIcon', 1);
        $this->view->newIcon = $this->_getParam('newIcon', 1);

        $this->view->list_view = 0;
        $this->view->grid_view = 0;
        $this->view->defaultView = -1;
        if (is_array($ShowViewArray) && in_array("1", $ShowViewArray)) {
            $this->view->list_view = 1;
            if ($this->view->defaultView == -1 || $defaultOrder == 1)
                $this->view->defaultView = 0;
        }
        if (is_array($ShowViewArray) && in_array("2", $ShowViewArray)) {
            $this->view->grid_view = 1;
            if ($this->view->defaultView == -1 || $defaultOrder == 2)
                $this->view->defaultView = 1;
        }


        if(isset($params['view_type']))
            $this->view->defaultView = $params['view_type'];

        if ($this->view->defaultView == -1) {
            return $this->setNoRender();
        }
        $customFieldValues = array();
        $values = array();

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $sitestoreproductBroseProduct = Zend_Registry::isRegistered('sitestoreproductBroseProduct') ? Zend_Registry::get('sitestoreproductBroseProduct') : null;

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

        if (!empty($this->view->statistics) && !(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
            $key = array_search('reviewCount', $this->view->statistics);
            if (!empty($key)) {
                unset($this->view->statistics[$key]);
            }
        }

        if (isset($params['tag']) && !empty($params['tag'])) {
            $tag = $params['tag'];
            $tag_id = $params['tag_id'];
        }
        
        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
          $this->view->detactLocation = Engine_Api::_()->sitestoreproduct()->enableLocation();
        }
        if ($this->view->detactLocation) {
          $params['locationmiles'] = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
          $params['latitude'] = $this->_getParam('latitude', 0);
          $params['longitude'] = $this->_getParam('longitude', 0);
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

        $this->view->formValues = $this->view->formValuesSM = $form->getValues();

        $values = array_merge($values, $form->getValues());

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

        $orderBy = $request->getParam('orderby', null);
        if (empty($orderBy)) {
            $values['orderby'] = $this->_getParam('orderby', 'product_id');
        }
        $values['limit'] = $values['itemCount'] = $itemCount = $this->_getParam('itemCount', 10);
        $this->view->bottomLine = $this->_getParam('bottomLine', 1);
        $values['viewType'] = $this->view->viewType = $this->_getParam('viewType', 0);
        $values['showClosed'] = $this->_getParam('showClosed', 1);
        $values['is_widget'] = 1;

        if ($request->getParam('titleAjax')) {
            $values['search'] = $request->getParam('titleAjax');
        } elseif ($request->getParam('miniMenuProductSearch'))
            $values['search'] = $request->getParam('miniMenuProductSearch');
        elseif ($request->getParam('mainMenuProductSearch'))
            $values['search'] = $request->getParam('mainMenuProductSearch');


        // GET PRODUCTS
        $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getSitestoreproductsPaginator($values, $customFieldValues);
        $paginator->setItemCountPerPage($values['itemCount']);
        $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
        $this->view->totalCount = $paginator->getTotalItemCount();
        $this->view->totalResults = $paginator->getTotalItemCount();

        $this->view->flageSponsored = 0;

        $this->view->search = 0;
        if (!empty($this->_getAllParams) && Count($this->_getAllParams) > 1) {
            $this->view->search = 1;
        }

        if (empty($sitestoreproductBroseProduct)) {
            return $this->setNoRender();
        }

        //SEND FORM VALUES TO TPL
        $this->view->formValues = $values;

        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            //SITEMOBILE CODE
            $this->view->isajax = $this->_getParam('isajax', 0);
            $this->view->identity = $values['identity'] = $this->_getParam('identity', $this->view->identity);
            $this->view->showinStock = $this->_getParam('in_stock', 1);
            $this->view->statistics = $this->_getParam('statistics', array());
            $this->view->postedby = $this->_getParam('postedby', 0);
            $this->view->columnWidth = $this->_getParam('columnWidth', '180');
            $this->view->columnHeight = $this->_getParam('columnHeight', '225');
            $this->view->title_truncation = $this->_getParam('truncation', 25);
            $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 25);
            $this->view->ratingType = $this->_getParam('ratingType', 'rating_both');
            $this->view->layouts_views = $values['layouts_views'] = $this->_getParam('layouts_views', array("1", "2"));
//    $this->view->params = $values;
            $this->view->totalCount = $paginator->getTotalItemCount();
            $this->view->viewType = $this->_getParam('viewType', 'gridview');

            $this->view->view_selected = $this->_getParam('viewType', 'gridview');
            $reqview_selected = Zend_Controller_Front::getInstance()->getRequest()->getParam('view_selected');
            if ($reqview_selected && count($this->view->layouts_views) > 1) {
                $this->view->view_selected = $reqview_selected;
                $this->view->formValuesSM['view_selected'] = $reqview_selected;
            }
        }

        //CAN CREATE PAGES OR NOT
        //$this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitestoreproduct_product', $viewer, "create");
        $this->view->ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct');
        $this->view->columnWidth = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $this->_getParam('columnHeight', '328');
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
