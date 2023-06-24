<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Remainingamounts.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Remainingamounts extends Engine_Db_Table
{
  protected $_name = 'sitestoreproduct_remaining_amounts';

  /**
   * Return remaining amount of a store
   *
   * @param $store_id
   * @return object
   */
  public function getReaminingAmount($store_id)
  {
    $select = $this->select()
                   ->from($this->info('name'),array("remaining_amount", "store_id"))
                   ->where('store_id =?', $store_id);
    return $this->fetchRow($select);
  }
}