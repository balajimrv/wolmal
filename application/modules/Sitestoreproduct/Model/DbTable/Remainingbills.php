<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Remainingbills.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Remainingbills extends Engine_Db_Table
{
  protected $_name = 'sitestoreproduct_remaining_bills';

  /**
   * Return store_id
   *
   * Is store remaining bill exist or not
   * @param $store_id
   * @return object
   */
  public function isStoreRemainingBillExist($store_id)
  {
    $select = $this->select()
                   ->from($this->info('name'),array("store_id"))
                   ->where('store_id =?', $store_id)
                   ->query()->fetchColumn();
    
    return $select;
  }
}