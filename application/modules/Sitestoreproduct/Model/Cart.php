<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Cart.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Cart extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;
  /**
   * Delete the viewer cart, product and belongings
   * 
   */
  public function _delete() {

    $cart_id = $this->cart_id;
    $db = Engine_Db_Table::getDefaultAdapter();

    $db->beginTransaction();
    try 
    {
      $cartProductsTable = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
      $cartProductsIds = $cartProductsTable->select()->from($cartProductsTable->info('name'),"cartproduct_id")->where("cart_id =?", $cart_id)->query()->fetchAll(Zend_Db::FETCH_COLUMN);

      if( !empty($cartProductsIds) )
      {
        foreach( $cartProductsIds as $cartProduct_id)
        {
          Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartProduct_id)->delete();
        }
      }
          
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //DELETE PRODUCT
    parent::_delete();
  }
}