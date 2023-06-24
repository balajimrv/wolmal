<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GetGroupProductStartingPrice.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_View_Helper_GetGroupProductStartingPrice extends Zend_View_Helper_Abstract {

  public function getGroupProductStartingPrice($groupProductId) {

    $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    $groupProductPrice = array();
//    $mappedProductsIds = $productTable->getCombinedProducts(array('product_id' => $groupProductId, 'getMappedIds' => true));
    $mappedProductsIds = $productTable->getCombinedProducts(array('product_id' => $groupProductId));
    
    if( !empty($mappedProductsIds) )
    {
      foreach( $mappedProductsIds as $product_obj )
      {
        $product_id = $product_obj->product_id;
        $groupProductPrice[] = $productTable->getProductDiscountedPrice($product_id);
      }
      return min($groupProductPrice);
    }
    return false;
    
  }
}