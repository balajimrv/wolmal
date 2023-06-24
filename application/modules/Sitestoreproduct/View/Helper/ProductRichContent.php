<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProductRichContent.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_View_Helper_ProductRichContent extends Zend_View_Helper_Abstract {

  public function productRichContent($product_id) {
    $richStr = '';
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $getProduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

    $RESOURCE_TYPE = 'sitestoreproduct_product';
    $sub_title = str_replace("'", '"', $getProduct->getTitle());
    $title = '<span class="sitestoreproduct_feed_title dblock">' . $view->htmlLink($getProduct->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($getProduct->getTitle(), 100), array('title' => $sub_title, 'class' => 'sea_add_tooltip_link', 'rel' => $RESOURCE_TYPE . ' ' . $product_id)) . '</span>';
    $price = Engine_Api::_()->sitestoreproduct()->getProductDiscount($getProduct, false, array('notShowDownpayment' => true));
//    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0)){
//      $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $getProduct->product_id);
//      $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj);
//      $price = $productPricesArray['display_product_price'];
//    }
    
    $photoURL = $getProduct->getPhotoUrl('thumb.profile');
    $photoURL = !empty($photoURL) ? $photoURL : Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_profile.png';
    $image = "<a href='" . $getProduct->getHref() . "' class = 'sea_add_tooltip_link' rel = '$RESOURCE_TYPE " . $product_id . "'>" . '<span class="sitestoreproduct_feed_img" style="background-image:url(' . $photoURL . ');"></span>' . '</a>';      
    $strFlag = "<span class='fright cartbtn'>" . $view->addToCart($getProduct, 1, '') . "</span>";
    
    $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($getProduct->store_id);
    $temp_non_selling_product_price = Engine_Api::_()->sitestoreproduct()->getIsAllowedNonSellingProductPrice($getProduct->store_id);
    
    if(!empty ($temp_allowed_selling) && $getProduct->allow_purchase){
      $product = '<span class="sitestoreproduct_product_feed">' . $image . $title . $strFlag . '<span class="price_info">' . $price . '</span>' . '</span>';
    }else{
      if(!empty($temp_non_selling_product_price)){
        $product = '<span class="sitestoreproduct_product_feed">' . $image . $title . '<span class="price_info">' . $price . '</span>' . '</span>';
      }else{
        $product = '<span class="sitestoreproduct_product_feed">' . $image . $title . '</span>';
      }
    }
    $richStr .= $product;

    $richStr = rtrim($richStr, ", ");

    return $richStr;
  }
  
}
