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
class Sitestoreproduct_Widget_CategoriesSponsoredController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $itemCount = $this->_getParam('itemCount', 0);
    $this->view->showIcon = $this->_getParam('showIcon', 1);

    //GET CATEGORY TABLE
    $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct');

    //GET SPONSORED CATEGORIES
    $this->view->categories = $categories = $tableCategory->getCategories(null, 0, 1, 0, $itemCount);
    
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();

    //GET STORAGE API
    $this->view->storage = Engine_Api::_()->storage();

    //GET SPONSORED CATEGORIES COUNT
    $this->view->totalCategories = Count($categories);

    if ($this->view->totalCategories <= 0) {
      return $this->setNoRender();
    }
  }

}