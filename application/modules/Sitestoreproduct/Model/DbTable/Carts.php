<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Carts.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Carts extends Engine_Db_Table
{
  protected $_rowClass = 'Sitestoreproduct_Model_Cart';

  /**
   * Return the number of products, which exist in cart.
   */
  public function getProductCounts($cartId = null)
  {
    if( empty($cartId) )
    {
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $cartTableName = $this->info('name');    
      $cartId = $this->select()
                     ->from($cartTableName, 'cart_id')
                     ->where('owner_id = ?', $viewer_id)
                     ->query()->fetchColumn();
    }
    
    if( !empty($cartId) ) {
      $cartProductTable = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
      
      $cartProductCount = $cartProductTable->select()
                                           ->from($cartProductTable->info('name'), 'SUM(quantity)')
                                           ->where('cart_id =?', $cartId)
                                           ->query()->fetchColumn();

      return $cartProductCount;
    }
    
    return 0;
  }

  /**
   * Return cart id of the current viewer
   *
   * @param $viewer id
   * @return int
   */
  public function getCartId($viewer_id)
  {
    $select = $this->select()->from($this->info('name'), array('cart_id'))->where('owner_id = ?', $viewer_id);
    return $select->query()->fetchColumn();
  }
    public function checkActivityBridges($type = 0) {
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $cartId = $this->getCartId($viewer_id);
      if( !empty($cartId) ) {
      $cartProductTable = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
      
      $cartProducts = $cartProductTable->select() 
                                           ->from($cartProductTable->info('name'), array('product_id','quantity'))
                                           ->where('cart_id =?', $cartId)
                                           ->query()->fetchAll();
      $finalValue = 0;
      foreach($cartProducts as $cartProduct) {
          $infoTable = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct');
          $discountValue = $infoTable->select()
                           ->from($infoTable->info('name'), 'discount_value')
                           ->where('product_id =?',$cartProduct['product_id'])
                           ->query()->fetchColumn();
          $finalValue += $cartProduct['quantity']*$discountValue;
          
      }
      if($type == 1) {
          $userpoints = Engine_Api::_()->getApi('core', 'activitypoints')->getPoints($viewer_id);
          $userpointsCount = $userpoints['userpoints_count'];
          if($userpointsCount > $finalValue) {
            $_SESSION['cart_discount_value'] = $finalValue;
            return 1;
          }
          else
          return 0;
      }
    }
      
  }
}