<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Cartproduct.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Cartproduct extends Core_Model_Item_Abstract {
  
  protected $_searchTriggers = false;
  protected $_parent_type = 'sitestoreproduct_cart';
  
  /**
   * Return the owner
   * 
   */
  public function getOwner($recurseType = null) {

    return $this->getParent()->getOwner();
  }
  
  /**
   * Delete the product and belongings
   * 
   */
  public function _delete() {

    $cartProduct_id = $this->cartproduct_id;
    $db = Engine_Db_Table::getDefaultAdapter();

    $db->beginTransaction();
    try {

      Engine_Api::_()->getDbtable('cartProductFieldSearch', 'sitestoreproduct')->delete(array('item_id = ?' => $cartProduct_id));
      Engine_Api::_()->getDbtable('cartProductFieldValues', 'sitestoreproduct')->delete(array('item_id = ?' => $cartProduct_id));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //DELETE PRODUCT
    parent::_delete();
  }
}
