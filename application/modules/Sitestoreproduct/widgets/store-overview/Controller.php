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
class Sitestoreproduct_Widget_StoreOverviewController extends Engine_Content_Widget_Abstract {

  public function indexAction() 
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $params = array();
    $params['store_id'] = $store_id = $request->getParam('store_id', null);
    
    if (empty($store_id)) {
      return $this->setNoRender();
    }
    
    $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
    $this->view->store_overview = $orderTable->getStoreOverview($params);
    $this->view->approval_pending_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 0));
    $this->view->payment_pending_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 1));
    $this->view->processing_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 2));
    $this->view->on_hold_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 3));
    $this->view->fraud_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 4));
    $this->view->complete_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 5));
    $this->view->cancel_orders = $orderTable->getStatusOrders(array('store_id' => $store_id, 'order_status' => 6));
    $this->view->total_orders = $orderTable->getStatusOrders(array('store_id' => $store_id));

    //DON'T RENDER IF NO DATA
    if (Count($this->view->store_overview) <= 0) {
      return $this->setNoRender();
    }
    
    $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if (empty($currencySymbol)) {
      $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    }
  }

}
