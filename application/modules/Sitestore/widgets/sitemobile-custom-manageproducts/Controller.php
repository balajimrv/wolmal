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

class Sitestore_Widget_SitemobileCustomManageproductsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        try {
            //GET SUBJECT AND STORE ID AND STORE OWNER ID
            if (!Engine_Api::_()->core()->hasSubject()) {
                return $this->setNoRender();
            } else {
                $this->view->is_subject = true;
                $this->view->storeSubject = $storeSubject = Engine_Api::_()->core()->getSubject('sitestore_store');
            }

            $temp_in_stock = $this->_getParam('temp_in_stock');

            //SET NO RENDER IF NO SUBJECT
            $this->view->store_id = $store_id = $this->_getParam('store_id', null);
            $this->view->checked_product = $this->_getParam('checked_product', 0);
            // $temp_is_subject = $this->_getParam('temp_is_subject', 1);
            $this->view->responseFlag = $this->_getParam('responseFlag', 0);
            $this->view->printingTagsObj = Engine_Api::_()->getDbTable('printingtags', 'sitestoreproduct')->getPrintingTags($store_id);
            $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
            $this->view->directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
            $this->view->isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
            $this->view->allowPrintingTag = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.allow.printingtag', 0);

            //GET VIEWER DETAILS
            $viewer = Engine_Api::_()->user()->getViewer();
            $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
            if (!empty($viewer->level_id))
                $this->view->viewer_level_id = $viewer->level_id;

            $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'view');
            if (empty($isManageAdmin)) {
                return $this->setNoRender();
            }

            //GET LAYOUT
            $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
            $this->view->widgets = $widgets = Engine_Api::_()->sitestore()->getwidget($layout, $store_id);
            $this->view->can_edit = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'edit');

            //GET SETTINGS
            $this->view->temp_layouts_views = $this->_getParam('temp_layouts_views', null);
            $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1"));
            if (!empty($this->view->temp_layouts_views))
                $ShowViewArray = @explode(",", $this->view->temp_layouts_views);
            $this->view->temp_layouts_views = @implode(",", $ShowViewArray);

            $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
            $this->view->temp_statistics = $this->_getParam('statistics', null);
            $this->view->statistics = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "reviewCount"));
            if (!empty($this->view->temp_statistics))
                $this->view->statistics = @explode(",", $this->view->statistics);
            $this->view->temp_statistics = @implode(",", $this->view->statistics);

            $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
            $defaultOrder = $this->_getParam('layouts_order', 2);
            $this->view->ratingType = $this->_getParam('ratingType', 'rating_both');
            $this->view->title_truncation = $this->_getParam('truncation', 50);
            $this->view->title_truncationGrid = $this->_getParam('truncationGrid', 90);
            $this->view->postedby = $this->_getParam('postedby', 1);

            $this->view->search = $search = $this->_getParam('search');
            $this->view->checkbox = $checkbox = $this->_getParam('checkbox');
            $this->view->selectbox = $selectbox = $this->_getParam('selectbox');

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

            $temViewType = $this->_getParam('temViewType', false);
            if (empty($temViewType))
                $temViewType = $this->view->defaultView;
            $this->view->temViewType = $this->view->defaultView = $temViewType;

            $request = Zend_Controller_Front::getInstance()->getRequest();
            $customFieldValues = array();
            $values = array();

            if (!empty($checkbox) && $checkbox == 1) {
                $values['owner_id'] = $viewer_id;
            }

            $this->view->params = $params = $request->getParams();
            $this->view->downpayment = isset($params['downpayment']) ? $params['downpayment'] : '';

            if (!empty($temp_in_stock))
                $params['temp_stock'] = --$temp_in_stock;
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
                $this->view->categoryObject = Engine_Api::_()->getItem('sitestoreproduct_category', $this->view->category_id);
                $this->view->categoryName = $this->view->categoryObject->category_name;

                if ($this->view->subcategory_id) {
                    $this->view->categoryObject = Engine_Api::_()->getItem('sitestoreproduct_category', $this->view->subcategory_id);
                    $this->view->categoryName = $this->view->categoryObject->category_name;

                    if ($this->view->subsubcategory_id) {
                        $this->view->categoryObject = Engine_Api::_()->getItem('sitestoreproduct_category', $this->view->subsubcategory_id);
                        $this->view->categoryName = $this->view->categoryObject->category_name;
                    }
                }
            }

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

            $values = @array_merge($values, $form->getValues());
            $orderBy = $request->getParam('orderby', null);
            if (empty($orderBy)) {
                $values['orderby'] = $this->_getParam('orderby', 'product_id');
            }
            if (!empty($selectbox) && $selectbox == 'featured') {
                $values['featured'] = 1;
                $values['orderby'] = 'creation_date';
            }
            if (!empty($search)) {
                $values['search'] = $search;
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
                $values['orderby'] = 'creation_date';
            }

            $values['page'] = $page;

            //GET LISITNG FPR PUBLIC STORE SET VALUE
            $values['type'] = 'manage';

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
            if ($form->show->getValue() == 3 && !isset($_GET['show'])) {
                @$values['show'] = 3;
            }

            $values['limit'] = $itemCount = $this->_getParam('itemCount', 100);
            $this->view->bottomLine = $this->_getParam('bottomLine', 1);
            $values['viewType'] = $this->view->viewType = $this->_getParam('viewType', 0);
            $values['showClosed'] = $this->_getParam('showClosed', 1);
            $values['is_widget'] = 1;

            // GET PRODUCTS
            $values['store_id'] = $store_id;
            $values['notifyemails'] = true;
            $values['is_owner'] = true;
            $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getSitestoreproductsPaginator($values, $customFieldValues);


            //    $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
            $this->view->current_count = $this->view->totalResults = $paginator->getTotalItemCount();

            $this->view->quota = $quota = Engine_Api::_()->sitestoreproduct()->getProductLimit($store_id);

            $this->view->flageSponsored = 0;

            //SEND FORM VALUES TO TPL
            $this->view->formValues = $values;

            $this->view->ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct');
            $this->view->columnWidth = $this->_getParam('columnWidth', '180');
            $this->view->columnHeight = $this->_getParam('columnHeight', '328');
        } catch (Exception $e) {
            //var_dump($e);die;
            throw $e;
        }
    }

    public function _getParam($key, $default) {
        $param = parent::_getParam($key);
        if (empty($param)) {
            $param = Zend_Controller_Front::getInstance()->getRequest()->getParam($key, $default);
        }

        return $param;
    }
}
