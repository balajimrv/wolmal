<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddToCart.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_View_Helper_AddToCart extends Zend_View_Helper_Abstract {

  public function addToCart($product, $widgets_id = 0, $view_type = null, $show_add_to_cart_button = false) {

      $isBuyAllow = Engine_Api::_()->sitestoreproduct()->isBuyAllowed();
      $isStoreType = Engine_Api::_()->sitestoreproduct()->isStoreType();
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

      if (empty($view_type))
        $view_loading_image_id = "";
      else
        $view_loading_image_id = "_" . $view_type;
      
      $add_to_cart = empty($show_add_to_cart_button) ? '' : ("<span>" . $this->view->translate("Add to Cart") . "</span>");

      if (!empty($isBuyAllow)) {
        $update_cart_notification = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.cart.update', 1);
        
        $isSitethemeEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetheme');
        $isSitemenuEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemenu');
        
        if( !empty($isSitethemeEnable) || !empty($isSitemenuEnable) )
        {
          $update_cart_notification = 2;
        }
        
        $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($product->store_id);

        if(!empty($temp_allowed_selling) && !empty($product->allow_purchase)){
        switch ($product->product_type) {
          case 'simple' :
          case 'bundled' :
            if (!empty($product->stock_unlimited) || $product->in_stock >= $product->min_order_quantity)
              return "<a href='javascript:void(0)' class='sitestoreproduct_addtocart_btn' onclick='addToCart(" . $product->product_id . ", " . $isStoreType . ", " . $widgets_id . ", \"" . $view_type . "\", " . $update_cart_notification . ")' title='". $this->view->translate('Add to Cart') .  "'><i id='loading_image_" . $widgets_id . "_" . $product->product_id . "$view_loading_image_id'></i>" . $add_to_cart . "</a>";
            break;

          case 'configurable' :
          case 'virtual' :
          case 'downloadable' :
            if (!empty($product->stock_unlimited) || $product->in_stock >= $product->min_order_quantity)
              return $this->view->htmlLink($product->getHref(), '<i></i>'.$add_to_cart, array('title' => $this->view->translate('Add to Cart'), 'class' => 'sitestoreproduct_addtocart_btn'));
            break;

          case 'grouped' : 
              return $this->view->htmlLink($product->getHref(), '<i></i>'.$add_to_cart, array('title' => $this->view->translate("Add to Cart") , 'class' => 'sitestoreproduct_addtocart_btn'));
            break;
        }
        
        }
      }
    }
  }