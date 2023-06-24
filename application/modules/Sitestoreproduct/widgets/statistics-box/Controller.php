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
class Sitestoreproduct_Widget_StatisticsBoxController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $store_id = $request->getParam('store_id', null);

    $orderTable = Engine_Api::_()->getDbTable('orders', 'sitestoreproduct');

    $this->view->todaySale = $orderTable->getStoreEarning($store_id, 'today');
    $this->view->weekSale = $orderTable->getStoreEarning($store_id, 'week');
    $this->view->monthSale = $orderTable->getStoreEarning($store_id, 'month');
    $this->view->siteTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', '');
    
    $directPayment = false;
    $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
    if( empty($isAdminDrivenStore) ) {
      $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
      if( empty($isPaymentToSiteEnable) ) {
        $directPayment = true;
      }
    }
    $this->view->directPayment = $directPayment;
      
    $this->view->currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if (empty($this->view->currencySymbol))
      $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
  }
}