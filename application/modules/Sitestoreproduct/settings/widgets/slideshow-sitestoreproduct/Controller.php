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
class Sitestoreproduct_Widget_SlideshowSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() { 

    $values = array();
    $values['limit'] = $this->_getParam('count', 10);
    $this->view->statistics = $this->_getParam('statistics', array("viewCount", "likeCount", "commentCount", "reviewCount"));

    $sitestoreproductSlideshow = Zend_Registry::isRegistered('sitestoreproductSlideshow') ?  Zend_Registry::get('sitestoreproductSlideshow') : null;
    $this->view->category_id = $values['category_id'] = $this->_getParam('hidden_category_id', 0);
    if ($values['category_id']) {
      $values['subcategory_id'] = $this->_getParam('hidden_subcategory_id', 0);
      if ($values['subcategory_id'])
        $values['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id', 0);
    }

    $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
    $this->view->showinStock = $this->_getParam('in_stock', 1);
    $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
    $this->view->title_truncation = $this->_getParam('truncation', 45);
    $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
    $this->view->popularity = $values['popularity'] = $this->_getParam('popularity', 'product_id');
    $this->view->fea_spo = $fea_spo = $this->_getParam('fea_spo', '');
    $this->view->sponsoredIcon = $this->_getParam('sponsoredIcon', 1);
    $this->view->featuredIcon = $this->_getParam('featuredIcon', 1);
    $this->view->newIcon = $this->_getParam('newIcon', 1);
    if ($fea_spo == 'featured') {
      $values['featured'] = 1;
    } elseif ($fea_spo == 'sponsored') {
      $values['sponsored'] = 1;
    } elseif ($fea_spo == 'newlabel') {
      $values['newlabel'] = 1;
    } elseif ($fea_spo == 'fea_spo') {
      $values['sponsored'] = 1;
      $values['featured'] = 1;
    }
    $values['interval'] = $interval = $this->_getParam('interval', 'overall');

    //FETCH FEATURED PRODUCTS
    $this->view->show_slideshow_object = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->productsBySettings($values);

    //RESULTS COUNT
    $this->view->num_of_slideshow = count($this->view->show_slideshow_object) > $values['limit'] ? $values['limit'] : count($this->view->show_slideshow_object);
    if (($this->view->num_of_slideshow <= 0) || empty($sitestoreproductSlideshow)) {
      return $this->setNoRender();
    }

    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
  }

}