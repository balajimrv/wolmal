<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: OrderRichContent.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_View_Helper_OrderRichContent extends Zend_View_Helper_Abstract {

  public function orderRichContent($id) { 
    $richStr = '';
    $flag = 1;
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    
    $product_ids = Engine_Api::_()->getDbtable("orderProducts", 'sitestoreproduct')->getOrderProductsDetail($id);
    $productsCount = @COUNT($product_ids);
    foreach ($product_ids as $productId) {
      
//      $productId['price'] = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productId['price']);
      if ($flag > 2)
        break;

      $getProduct = Engine_Api::_()->getItem('sitestoreproduct_product', $productId['product_id']);
      
      if( empty($getProduct) )
        continue;
      
      $productId['price'] = Engine_Api::_()->sitestoreproduct()->getProductDiscount($getProduct, false, array('notShowDownpayment' => true));

      $RESOURCE_TYPE = 'sitestoreproduct_product';
      $order_id = $productId['order_id'];
      $sub_title = str_replace("'", '"', $getProduct->getTitle());
      $title = '<span class="sitestoreproduct_feed_title dblock">' . $view->htmlLink($getProduct->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($getProduct->getTitle(), 100), array('title' => $sub_title, 'class' => 'sea_add_tooltip_link', 'rel' => $RESOURCE_TYPE . ' ' . $productId['product_id'])) . '</span>';
      if( !empty($productId['configuration']) ) {
        $configuration = Zend_Json::decode($productId['configuration']);
          $tempConfigCount = 0;
          foreach($configuration as $config_name => $config_value):
            if( !empty($tempConfigCount) ) :
              $title .= ', ';
            endif;
            $title .= "<span class='sitestoreproduct_stats'><b>$config_name:</b> $config_value </span>";
            $tempConfigCount++;
          endforeach;
      }
      $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($getProduct->store_id);
      $temp_non_selling_product_price = Engine_Api::_()->sitestoreproduct()->getIsAllowedNonSellingProductPrice($getProduct->store_id);
      if( (!empty($temp_allowed_selling) && !empty($getProduct->allow_purchase)) || !empty($temp_non_selling_product_price)){
        $temp_hide_price = "style='display:block'";
      } else {
        $temp_hide_price = "style='display:none'";
      }
      
//      $price = "<span class='sitestoreproduct_feed_stats'>" . $productId['quantity'] . ' x <strong class="sitestoreproduct_price_sale">' . $productId['price'] . "</strong></span>";
      
      $price = "<span class='sitestoreproduct_feed_stats'>" . $productId['quantity'] . " x ".$productId['price']."</span>";

      $photoURL = $getProduct->getPhotoUrl('thumb.profile');
      $photoURL = !empty($photoURL) ? $photoURL : Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_profile.png';
      $image = "<a href='" . $getProduct->getHref() . "' class = 'sea_add_tooltip_link' rel = '$RESOURCE_TYPE " . $productId['product_id'] . "'>" . '<span class="sitestoreproduct_feed_img" style="background-image:url(' . $photoURL . ');"></span>' . '</a>';      

      if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
        if (!empty($getProduct->closed) || !empty($getProduct->draft) || empty($getProduct->approved)  || $getProduct->start_date > date('Y-m-d H:i:s')|| ($getProduct->end_date < date('Y-m-d H:i:s') && !empty($getProduct->end_date_enable)) ) 
          $addToCart = '';
        else
          $addToCart = "<span class='fright cartbtn'>" .$view->addToCart($getProduct, 0, '', false) . "</span>";
      }
      else {
        if (!empty($getProduct->draft) || empty($getProduct->approved)  || $getProduct->start_date > date('Y-m-d H:i:s')|| ($getProduct->end_date < date('Y-m-d H:i:s') && !empty($getProduct->end_date_enable)) ) 
          $addToCart = '';
        else
          $addToCart = "<span class='fright cartbtn'>" .$view->addToCart($getProduct, 0, '', false) . "</span>";        
      }
      
      if(!empty($temp_allowed_selling) && !empty($getProduct->allow_purchase)){
        $product = '<span class="sitestoreproduct_product_feed">' . $image . $title . $addToCart . '<span class="price_info" ' . $temp_hide_price . '>' .$price . "</span>" . '</span>';
      }else{
        $product = '<span class="sitestoreproduct_product_feed">' . $image . $title . '<span class="price_info" ' . $temp_hide_price . '>' .$price . "</span>" . '</span>';
      }
      $richStr .= $product;
      $flag++;
    }
    
    if( !empty($richStr) )
    {
      $richStr = rtrim($richStr, ", ");

      if ($productsCount > 3) {
        $order_id = Engine_Api::_()->sitestoreproduct()->getDecodeToEncode((string) $order_id);
        $url = $view->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'order-products', 'id' => $order_id), 'default', true);
        $richStr .= '<span class="sitestoreproduct_product_feed_more dblock clr"><a href="javascript:void(0)" onclick="Smoothbox.open(\'' . $url . '\');">'.$this->view->translate("View all Products").'</a></span>';
      }

      return $richStr;
    }
    return false;
  }
  
}
