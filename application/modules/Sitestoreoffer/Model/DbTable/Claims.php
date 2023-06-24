<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Claims.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Model_DbTable_Claims extends Engine_Db_Table {

  protected $_rowClass = "Sitestoreoffer_Model_Claim";

  public function getClaimValue($owner_id,$offer_id,$store_id) {

		$claim_value = $this->select()
				->from('engine4_sitestoreoffer_claims', 'claim_value')
				->where('owner_id =?', $owner_id)
				->where('offer_id =?', $offer_id)
				->where('store_id =?', $store_id)
				->limit(1)
				->query()
				->fetchColumn(0);
    return $claim_value;
  }
  
  public function deleteClaimOffers($offer_id) {

    $select = $this->select()
                  ->where('offer_id =?', $offer_id);
    $resultOfferClaims = $this->fetchAll($select);
  
    foreach ($resultOfferClaims as $offer) {
      $claim = Engine_Api::_()->getItem('sitestoreoffer_claim',$offer->claim_id);
      $claim->delete();
    }
  }
}
?>