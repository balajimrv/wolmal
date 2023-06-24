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
class Sitestoreproduct_Widget_CategoriesBannerSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $sitestoreproductCategoriesBanner = Zend_Registry::isRegistered('sitestoreproductCategoriesBanner') ?  Zend_Registry::get('sitestoreproductCategoriesBanner') : null;
    
    $category_id = $request->getParam('subsubcategory_id', null);
    if (empty($category_id)) {
      $category_id = $request->getParam('subcategory_id', null);
      if (empty($category_id)) {
        $category_id = $request->getParam('category_id', null);
      }
    }

    //SET NO RENDER
    if (empty($category_id) || empty($sitestoreproductCategoriesBanner))
      return $this->setNoRender();

    //GET CATEGORY ITEM
    $this->view->category = $category = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id);

    //SET NO RENDER
    if (empty($category->banner_id))
      return $this->setNoRender();

    //GET STORAGE API
    $this->view->storage = Engine_Api::_()->storage();
  }

}