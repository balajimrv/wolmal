<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GetProductInfo.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_View_Helper_GetProductInfo extends Zend_View_Helper_Abstract {

  /*
   * @$product  Product Object
   * @$widget_id  Widgets ID
   * @$view_type  List View | Grid View
   * @$showAddtoCart  Show Add to Cart OR not
   * @$showinStock  Show In Stock value OR not
   * @$showAddToCartButton Show "Add to Cart" Button
   */  
  public function getProductInfo($product, $widget_id = 0, $view_type = null, $showAddtoCart = 1, $showinStock = 1, $showAddToCartButton = false, $priceWithTitle = 0) {
 
    $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($product->store_id);
    $temp_non_selling_product_price = Engine_Api::_()->sitestoreproduct()->getIsAllowedNonSellingProductPrice($product->store_id);
    if( (!empty($temp_allowed_selling) && !empty($product->allow_purchase)) || !empty($temp_non_selling_product_price) ){
    $productInfo = array();
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if( $product->product_type == 'virtual' && !empty($product->product_info) ) {
      $virtualProductOptions = unserialize($product->product_info);
      if( !empty($virtualProductOptions) && !empty($virtualProductOptions['virtual_product_price_range']) && $virtualProductOptions['virtual_product_price_range'] != 'fixed' ) {
        $productInfo['priceRangeBasis'] = Engine_Api::_()->sitestoreproduct()->getProductPriceRangeText($virtualProductOptions['virtual_product_price_range']);
      }
    }
    
    if ( !empty($product->price) && 
         !empty($product->discount) && 
         (@strtotime($product->discount_start_date) <= @time()) && 
         (!empty($product->discount_permanant) || (@time() < @strtotime($product->discount_end_date))) && 
         (empty($product->user_type) || ($product->user_type == 1 && empty($viewer_id)) || ($product->user_type == 2 && !empty($viewer_id)))) 
    { 
      if( empty($product->handling_type) )
        $productInfo['discount']['price'] = @round($product->discount_value * 100 / $product->price, 2);
      else
        $productInfo['discount']['price'] = @round($product->discount_value, 2);

      $productInfo['discount']['price_after_discount'] = $product->price - $product->discount_amount;
    }
    
   if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
    return $this->view->partial('_productInfo.tpl', 'sitestoreproduct', array('productInfo' => $productInfo, 'sitestoreproduct' => $product, 'widget_id' => $widget_id, 'view_type' => $view_type, 'showAddtoCart' => $showAddtoCart, 'showinStock' => $showinStock, 'showAddToCartButton' => $showAddToCartButton, 'priceWithTitle' => $priceWithTitle));
     }else {
    return $this->view->partial('_productInfoSM.tpl', 'sitestoreproduct', array('productInfo' => $productInfo, 'sitestoreproduct' => $product, 'widget_id' => $widget_id, 'view_type' => $view_type, 'showAddtoCart' => $showAddtoCart, 'showinStock' => $showinStock, 'showAddToCartButton' => $showAddToCartButton, 'priceWithTitle' => $priceWithTitle));
    }
    } else {
      return "";
    }
  }
}