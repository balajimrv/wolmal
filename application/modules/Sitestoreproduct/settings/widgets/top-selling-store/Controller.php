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
class Sitestoreproduct_Widget_TopSellingStoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() 
  {
    $this->view->statistics = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "reviewCount"));
    $this->view->title_truncation = $this->_getParam('truncation', 25);
    
    $values = array();
    $this->view->category_id =  $values['category_id'] = $this->_getParam('category_id',0);
    
    $values['limit'] = $this->_getParam('itemCount', 5);
    $this->view->display_by = $display_by = $this->_getParam('display_by', 0);
    $values['ratingType'] = $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
    
    $this->view->columnWidth = $this->_getParam('columnWidth', '180');
    $this->view->columnHeight = $this->_getParam('columnHeight', '328');
    $values['interval'] = $interval = $this->_getParam('interval', 'overall');
    
    $this->view->category_id = $values['category_id'] = $this->_getParam('hidden_category_id', 0);
    
    if ($values['category_id']) {
      $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitestore');
      // GET CATEGORY
      $categoriesNmae = $tableCategories->getCategory($values['category_id']);
      if (!empty($categoriesNmae->category_name)) {
        $this->view->category_name = $categoriesNmae->category_name;
      }
      
      $this->view->subcategory_id = $values['subcategory_id'] = $this->_getParam('hidden_subcategory_id', 0);
      if ($values['subcategory_id']) {
        // GET SUB-CATEGORY
        $subcategory_name = $tableCategories->getCategory($values['subcategory_id']);
        if (!empty($subcategory_name->category_name)) {
          $this->view->subcategory_name = $subcategory_name->category_name;
        }
        
        $this->view->subsubcategory_id = $values['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id', 0);
        if ($values['subsubcategory_id']) {
          // GET SUB-SUB-CATEGORY
          $subsubcategory_name = $tableCategories->getCategory($values['subsubcategory_id']);
          if (!empty($subsubcategory_name->category_name)) {
            $this->view->subsubcategory_name = $subsubcategory_name->category_name;
          }
        }
      }
    }
    
    if( $display_by == 1 )
      $fetch_column = 'grand_total';
    else
      $fetch_column = 'item_count';

    $this->view->top_selling_store = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getTopSellingStore($fetch_column, $values);
    
    //DON'T RENDER IF NO DATA
    if (Count($this->view->top_selling_store) <= 0) {
      return $this->setNoRender();
    }
    
    $this->view->viewType = $this->_getParam('viewType', 'gridview');
  }

}
