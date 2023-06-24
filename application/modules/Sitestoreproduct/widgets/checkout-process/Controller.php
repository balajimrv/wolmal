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
class Sitestoreproduct_Widget_CheckoutProcessController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controller = $front->getRequest()->getControllerName();

    if ($module != 'sitestoreproduct' || $controller != 'index' || $action != 'checkout')
      return $this->setNoRender();

    $sitestoreproduct_checkout_no_payment_gateway_enable = Zend_Registry::isRegistered('sitestoreproduct_checkout_no_payment_gateway_enable') ? Zend_Registry::get('sitestoreproduct_checkout_no_payment_gateway_enable') : null;
    $sitestoreproduct_checkout_store_no_payment_gateway_enable = Zend_Registry::isRegistered('sitestoreproduct_checkout_store_no_payment_gateway_enable') ? Zend_Registry::get('sitestoreproduct_checkout_store_no_payment_gateway_enable') : null;
    $sitestoreproduct_checkout_no_region_enable = Zend_Registry::isRegistered('sitestoreproduct_checkout_no_region_enable') ? Zend_Registry::get('sitestoreproduct_checkout_no_region_enable') : null;
    $sitestoreproduct_checkout_viewer_cart_empty = Zend_Registry::isRegistered('sitestoreproduct_checkout_viewer_cart_empty') ? Zend_Registry::get('sitestoreproduct_checkout_viewer_cart_empty') : null;

    if (!empty($sitestoreproduct_checkout_no_payment_gateway_enable) || !empty($sitestoreproduct_checkout_store_no_payment_gateway_enable) || !empty($sitestoreproduct_checkout_no_region_enable) || !empty($sitestoreproduct_checkout_viewer_cart_empty))
      return $this->setNoRender();

    $this->view->sitestoreproduct_other_product_type = Zend_Registry::isRegistered('sitestoreproduct_other_product_type') ? Zend_Registry::get('sitestoreproduct_other_product_type') : null;
  }

}
