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
class Sitestoreproduct_Widget_ItemSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $product_id = $this->_getParam('product_id');

    if (empty($product_id)) {
      return $this->setNoRender();
    }

    //GET ITEM OF THE DAY
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getItemOfTheDay($product_id);

    $sitestoreproductGetItem = Zend_Registry::isRegistered('sitestoreproductGetItem') ? Zend_Registry::get('sitestoreproductGetItem') : null;

    if (empty($sitestoreproduct) || empty($sitestoreproductGetItem)) {
      return $this->setNoRender();
    }

    $starttime = $this->_getParam('starttime');
    $endtime = $this->_getParam('endtime');
    $currenttime = date('Y-m-d H:i:s');

    if (!empty($starttime) && $currenttime < $starttime) {
      return $this->setNoRender();
    }

    if (!empty($endtime) && $currenttime > $endtime) {
      return $this->setNoRender();
    }

    $this->view->ratingType = $this->_getParam('ratingType', 'rating_avg');
    $this->view->showAddToCart = $this->_getParam('add_to_cart', 1);
    $this->view->showinStock = $this->_getParam('in_stock', 1);

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
      if ($sitestoreproduct->closed == 1 || empty($sitestoreproduct->approved) || $sitestoreproduct->draft == 1 || empty($sitestoreproduct->search) || empty($sitestoreproductGetItem)) {
        $this->setNoRender();
      }
    } else {
      if (empty($sitestoreproduct->approved) || $sitestoreproduct->draft == 1 || empty($sitestoreproduct->search) || empty($sitestoreproductGetItem)) {
        $this->setNoRender();
      }
    }
  }

}
