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
class Sitestoreproduct_Widget_ProfileReviewBreadcrumbSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_review')) {
      return $this->setNoRender();
    }

    //GET REVIEWS
    $this->view->reviews = Engine_Api::_()->core()->getSubject();

    //GET PRODUCT 
    $this->view->sitestoreproduct = $sitestoreproduct = $this->view->reviews->getParent();

    //GET TAB ID
    $this->view->tab_id = Engine_Api::_()->sitestoreproduct()->existWidget('sitestoreproduct_reviews', 0);
    
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();

    //GET CATEGORY TABLE
    $this->view->tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');
    if (!empty($sitestoreproduct->category_id)) {
      $this->view->category_name = $this->view->tableCategory->getCategory($sitestoreproduct->category_id)->category_name;
      if (!empty($sitestoreproduct->subcategory_id)) {
        $this->view->subcategory_name = $this->view->tableCategory->getCategory($sitestoreproduct->subcategory_id)->category_name;
        if (!empty($sitestoreproduct->subsubcategory_id)) {
          $this->view->subsubcategory_name = $this->view->tableCategory->getCategory($sitestoreproduct->subsubcategory_id)->category_name;
        }
      }
    }
  }

}