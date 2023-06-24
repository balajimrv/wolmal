<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Region.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Region extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

   /**
   * Delete the region and belongings
   * 
   */
  public function _delete() {

    $region_id = $this->region_id;
    $db = Engine_Db_Table::getDefaultAdapter();

    $db->beginTransaction();
    try {
     
      Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->delete(array('state = ?' => $region_id));
      Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct')->delete(array('region = ?' => $region_id));
      Engine_Api::_()->getDbtable('addresses', 'sitestoreproduct')->delete(array('state = ?' => $region_id));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //DELETE PRODUCT
    parent::_delete();
  }
}