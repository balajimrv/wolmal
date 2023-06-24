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
class Sitestoreproduct_Widget_PinboardBrowseController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->params = $this->_getAllParams();
        $this->view->params['defaultLoadingImage'] = $this->_getParam('defaultLoadingImage', 1);
        if (!isset($this->view->params['noOfTimes']) || empty($this->view->params['noOfTimes']))
            $this->view->params['noOfTimes'] = 1000;

        if ($this->_getParam('autoload', true)) {
            $this->view->autoload = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->autoload = false;
                if ($this->_getParam('contentpage', 1) > 1)
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                $this->getElement()->removeDecorator('Title');
            }
        } else {
            $this->view->is_ajax_load = $this->_getParam('is_ajax_load', false);
            if ($this->_getParam('contentpage', 1) > 1) {
                $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            }
        }

        $params = $this->view->params;
        $params['limit'] = $this->_getParam('itemCount', 3);
        $this->view->postedby = $this->_getParam('postedby', 1);
        $this->view->commentSection = $this->_getParam('commentSection', 0);
        $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
        $this->view->showinStock = $this->_getParam('in_stock', 1);
        $this->view->statistics = $this->_getParam('statistics', array("likeCount", "reviewCount", "ratingStar", "productCreationTime"));
        $this->view->truncationDescription = $this->_getParam('truncationDescription', 0);
        $params['ratingType'] = $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
        $params['paginator'] = 1;
        
        $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
        if ($this->view->detactLocation) {
            $this->view->detactLocation = Engine_Api::_()->siteevent()->enableLocation();
        }
        
        if ($this->view->detactLocation) {
            $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
            $params['latitude'] = $this->_getParam('latitude', 0);
            $params['longitude'] = $this->_getParam('longitude', 0);
        }        

        $values = $customFieldValues = array();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->params = $params = array_merge($params, $request->getParams());
        if (!isset($params['category_id']))
          $params['category_id'] = 0;
        if (!isset($params['subcategory_id']))
          $params['subcategory_id'] = 0;
        if (!isset($params['subsubcategory_id']))
          $params['subsubcategory_id'] = 0;
        $values['category_id'] = $this->view->params['category_id'] = $this->view->category_id = $params['category_id'];
        $values['subcategory_id'] = $this->view->params['subcategory_id'] = $this->view->subcategory_id = $params['subcategory_id'];
        $values['subsubcategory_id'] = $this->view->params['subsubcategory_id'] = $this->view->subsubcategory_id = $params['subsubcategory_id'];     
        
        //GET VIEWER DETAILS
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer->getIdentity();   
        
        //CATEGORY WORK
        $formArray = array_merge($_GET, $_POST);
//        $this->view->params['category_id'] = $params['category_id'] = isset($formArray['category_id']) ? $formArray['category_id'] : $this->_getParam('hidden_category_id');
//        $this->view->params['subcategory_id'] = $params['subcategory_id'] = isset($formArray['subcategory_id']) ? $formArray['subcategory_id'] : $this->_getParam('hidden_subcategory_id');
//        $this->view->params['subsubcategory_id'] = $params['subsubcategory_id'] = isset($formArray['subsubcategory_id']) ? $formArray['subsubcategory_id'] : $this->_getParam('hidden_subsubcategory_id');

        //FORM GENERATION
        $locationSettings = array(
            'locationDetection' => $this->view->detactLocation,
        );
        
        if(isset($params['price'])) {
            $priceSettings['priceFieldType'] = 'textBox';
        }  
        elseif(isset($params['minPrice'])) {
            $priceSettings['priceFieldType'] = 'slider';
        }  
        
        $form = new Sitestoreproduct_Form_Search(array('type' => 'sitestoreproduct_product', 'locationSettings' => $locationSettings, 'priceSettings' => $priceSettings));

        if (!empty($params)) {
            $form->populate($params);
        }
        
        $this->view->formValues = $values = array_merge($values, $form->getValues());
        
//        //GET VALUE BY POST TO GET DESIRED PRODUCTS
//        if (!empty($params)) {
//          $values = array_merge($values, $params);
//        }
        
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
        if ($form->show->getValue() == 3 && !isset($_GET['show'])) {
            @$values['show'] = 3;
        } 

        //GET PRODUCTS
        $this->view->products = $paginator = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getSitestoreproductsPaginator($values, $customFieldValues);
        $this->view->totalCount = $paginator->getTotalItemCount();

        $paginator->setCurrentPageNumber($this->_getParam('contentpage', 1));
        $paginator->setItemCountPerPage($params['limit']);

        $this->view->countPage = $paginator->count();
        if (isset($this->view->params['noOfTimes']) && $this->view->params['noOfTimes'] > $this->view->countPage) {
            $this->view->params['noOfTimes'] = $this->view->countPage;
        }

        $this->view->show_buttons = $this->_getParam('show_buttons', array("wishlist", "compare", "comment", "like", 'share', 'facebook', 'twitter', 'pinit'));
    }

}
