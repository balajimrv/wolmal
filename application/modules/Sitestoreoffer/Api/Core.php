<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Api_Core extends Core_Api_Abstract {

  /**
   * Delete the sitestoreoffer album and photos
   * 
   * @param int $offer_id
   */
  public function deleteContent($offer_id) {

		//GET THE SITESTORENOTE ITEM
    $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offer_id);

		if(empty($sitestoreoffer)) {
			return;
		}

    $tablePhoto = Engine_Api::_()->getItemTable('sitestoreoffer_photo');
    $select = $tablePhoto->select()->where('offer_id = ?', $offer_id);
    $rows = $tablePhoto->fetchAll($select);
    if (!empty($rows)) {
      foreach ($rows as $photo) {
        $photo->delete();
      }
    }

    $tableAlbum = Engine_Api::_()->getItemTable('sitestoreoffer_album');
    $select = $tableAlbum->select()->where('offer_id = ?', $offer_id);
    $rows = $tableAlbum->fetchAll($select);
		if (!empty($rows)) {
	    foreach ($rows as $album) {
	      $album->delete();
	    }
		}

		$sitestoreoffer->delete();
	}
	
	public function tabofferDuration($sqlTimeStr = NULL, $totalOffers,$category_id) {

    //OFFER TABLE NAME
    $offerTable = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');
    $offerTableName = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer')->info('name');

    //STORE TABLE
    $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storeTable->info('name');

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $currentTime = date("Y-m-d H:i:s");
    if (!empty($viewer_id)) {
      // Convert times
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($viewer->timezone);
      $currentTime = date("Y-m-d H:i:s");
      date_default_timezone_set($oldTz);
    }
    //QUERY MAKING
    $select = $offerTable->select()
                    ->setIntegrityCheck(false)
                    ->from($storeTableName, array('photo_id', 'title as sitestore_title'))
                    ->join($offerTableName, $offerTableName . '.store_id = ' . $storeTableName . '.store_id');
    if ( empty($sqlTimeStr) ) {
      $select = $select
                      ->where("($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$currentTime' OR $offerTableName.end_settings = 0)");
    }
    else {
      //$select = $select->where($offerTableName . "$sqlTimeStr  or " . $offerTableName . '.end_time < 1');
      $select = $select
                      ->where("($offerTableName.end_settings = 1 AND $offerTableName $sqlTimeStr  OR $offerTableName.end_settings = 0)");
    }

    $select = $select
                    ->where($storeTableName . '.closed = ?', '0')
                    ->where($storeTableName . '.approved = ?', '1')
                    ->where($storeTableName . '.search = ?', '1')
                    ->where($storeTableName . '.declined = ?', '0')
                    ->where($storeTableName . '.draft = ?', '1')
                    ->where($offerTableName . '.status = ?',  '1')
                    ->where($offerTableName . '.approved = ?',  '1')
                    ->where($offerTableName . '.public = ?',  '1')
                      ;

    if (!empty($category_id)) {
			$select = $select->where($storeTableName . '.	category_id =?', $category_id);
		}
		
    if ( Engine_Api::_()->sitestore()->hasPackageEnable() ) {
      $select->where($storeTableName . '.expiration_date  > ?', $currentTime);
    }

    //Start Network work
    $select = $storeTable->getNetworkBaseSql($select, array('not_groupBy' => 1, 'extension_group' => $offerTableName . ".offer_id"));
    //End Network work
    $select = $select->limit($totalOffers);

    return Zend_Paginator::factory($select);
  }
  
  public function getCurrencySymbolPrice($price)
  {
    $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($price, $currency, array('precision' => 2));
    return $priceStr;
  }

}
?>