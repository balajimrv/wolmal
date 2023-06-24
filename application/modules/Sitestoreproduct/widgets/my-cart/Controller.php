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
class Sitestoreproduct_Widget_MyCartController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // GET VIEWER ID
    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->limit = $limit = $this->_getParam('itemCount', 5);
    $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    $cartSubTotal = 0;
    $product_price = array();

    if (empty($viewer_id)) {
      $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');

      $tempUserCart = $product_ids = $product_quantity = $product_attribute = $getCart = array();
      $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);

      if (empty($tempUserCart)) {
        $this->view->cartProductsCount = 0;
        return;
      }

      $cartProductCounts = 0;
      $viewerCartConfig = array();

      foreach ($tempUserCart as $product_id => $values) {
        $product_ids[] = $product_id;
        $product_price[$product_id] = $price = $productTable->getProductDiscountedPrice($product_id);

        if (isset($values['config']) && is_array($values['config'])) {
          $field_id = 0;
          $viewerCartConfig[$product_id] = $values['config'];
          foreach ($values['config'] as $index => $quantity) {
            $cartProductCounts += $quantity['quantity'];
            $product_quantity[$product_id]['config'][$field_id++] = $quantity['quantity'];
            $cartSubTotal += $price * $quantity['quantity'];
          }
        } else {
          $cartProductCounts += $values['quantity'];
          $product_quantity[$product_id] = $values['quantity'];
          $cartSubTotal += $price * $values['quantity'];
        }
      }

      $product_ids = implode(",", $product_ids);
      $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
      $product_attribute = $productTable->getProductAttribute(array("store_id", "title", "photo_id", "product_id", "product_type"), "product_id IN ($product_ids)", true);
      $product_attribute = $productTable->fetchAll($product_attribute);
      $getCart = $product_attribute;
      $this->view->product_quantity = $product_quantity;
      $this->view->viewerCartConfig = $viewerCartConfig;
    } else {
      $cartProductCounts = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getProductCounts();
      $cart_id = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getCartId($viewer_id);

      if (empty($cart_id)) {
        $this->view->cartProductsCount = 0;
        return;
      }

      $getCart = $productTable->getCart($cart_id, false);

      foreach ($getCart as $cart_product) {
        $product_price[$cart_product->product_id] = $price = $productTable->getProductDiscountedPrice($cart_product->product_id);
        $cartSubTotal += $price * $cart_product->quantity;
      }
    }

    $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if (empty($currencySymbol)) {
      $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    }

    $this->view->cartSubTotal = $cartSubTotal;
    $this->view->cartProductsCount = $cartProductCounts;
    $this->view->getCartProducts = $getCart;
    $this->view->productPrice = $product_price;
  }

}