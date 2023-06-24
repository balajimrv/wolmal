<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Offer.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Model_DbTable_Ordercoupons extends Engine_Db_Table {
  
  protected $_parent_type = 'sitestore_store';
  protected $_owner_type = 'user';
  protected $_parent_is_owner = false;
  
  public function getOrderCouponCount($offer_id, $store_id)
  {
    $uses_count = $this->select()
                  ->from($this->info('name'), 'COUNT(*) AS coupon_count')
                  ->where('coupon_id = ?', $offer_id)
                  ->where('store_id = ?', $store_id)
                  ->where('buyer_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
                  ->limit(1)
                  ->query()->fetchColumn();
    return $uses_count;
  }
  
}

?>