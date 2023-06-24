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
class Sitestoreproduct_Widget_SponsoredSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->vertical = $this->_getParam('viewType', 0);
    $values = array();
    $sitestoreproductSponsored = Zend_Registry::isRegistered('sitestoreproductSponsored') ?  Zend_Registry::get('sitestoreproductSponsored') : null;

		$this->view->category_id = $values['category_id'] = $this->_getParam('hidden_category_id');
		$this->view->subcategory_id = $values['subcategory_id'] = $this->_getParam('hidden_subcategory_id');
		$this->view->subsubcategory_id = $values['subsubcategory_id'] = $this->_getParam('hidden_subsubcategory_id');

    $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
    $this->view->showinStock = $this->_getParam('in_stock', 1);
    $this->view->priceWithTitle = $this->_getParam('priceWithTitle', 0);
    $this->view->showPagination = $this->_getParam('showPagination', 1);
    $this->view->interval = $this->_getParam('interval', 300);
    $this->view->blockHeight = $this->_getParam('blockHeight', 250);
    $this->view->blockWidth = $this->_getParam('blockWidth', 150);
    $this->view->showOptions = $this->_getParam('showOptions', array("category","rating","review","compare","wishlist"));

    $this->view->title_truncation = $this->_getParam('truncation', 50);
    $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
    $this->view->viewType = $this->_getParam('viewType', 0);
    $this->view->limit = $values['limit'] = $this->_getParam('itemCount', 3);
    $this->view->sponsoredIcon = $this->_getParam('sponsoredIcon', 1);
    $this->view->featuredIcon = $this->_getParam('featuredIcon', 1);
    $this->view->newIcon = $this->_getParam('newIcon', 1);
    $this->view->popularity = $values['popularity'] = $this->_getParam('popularity', 'product_id');
    $this->view->fea_spo = $fea_spo = $this->_getParam('fea_spo', null);
    if ($fea_spo == 'featured') {
      $values['featured'] = 1;
    } elseif ($fea_spo == 'newlabel') {
      $values['newlabel'] = 1;
    } elseif ($fea_spo == 'sponsored') {
      $values['sponsored'] = 1;
    } elseif ($fea_spo == 'fea_spo') {
      $values['sponsored'] = 1;
      $values['featured'] = 1;
    }

    //FETCH SPONSERED PRODUCTS
    $this->view->products = $product = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProduct('', $values);

    //GET LIST COUNT
    $this->view->totalCount = $product->getTotalItemCount();
    if ( ($this->view->totalCount <= 0) || empty($sitestoreproductSponsored) ) {
      return $this->setNoRender();
    }
  }

}
