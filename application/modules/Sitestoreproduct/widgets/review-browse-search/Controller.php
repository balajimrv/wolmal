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
class Sitestoreproduct_Widget_ReviewBrowseSearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    $searchForm = $this->view->searchForm = new Sitestoreproduct_Form_Review_Search(array('type' => 'sitestoreproduct_review'));
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->requestParams = $requestParams = $request->getParams();

    if (isset($requestParams['page'])) {
      unset($requestParams['page']);
    }

    $searchForm
            ->setMethod('get')
            ->populate($requestParams)
    ;

    $categories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategories(null, 0, 0, 1);
    $categories_slug[0] = "";
    if (count($categories) != 0) {
      foreach ($categories as $category) {
        $categories_slug[$category->category_id] = $category->getCategorySlug();
      }
    }
    $this->view->categories_slug = $categories_slug;    
  }

}