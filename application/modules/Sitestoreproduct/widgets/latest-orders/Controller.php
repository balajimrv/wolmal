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
class Sitestoreproduct_Widget_LatestOrdersController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    $params = array();
    $this->view->storeId = $params['store_id'] = $request->getParam('store_id', null);
    $params['limit'] = $this->_getParam('itemCount', 5);
    
    $this->view->latestOrders = Engine_Api::_()->getDbTable('orders', 'sitestoreproduct')->getLatestOrders($params);

    $this->view->currency_symbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if (empty($this->view->currency_symbol)) {
      $this->view->currency_symbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    }
  }
}
