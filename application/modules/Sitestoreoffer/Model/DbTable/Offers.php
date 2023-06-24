<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Offers.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Model_DbTable_Offers extends Engine_Db_Table {

  protected $_rowClass = "Sitestoreoffer_Model_Offer";

  /**
   * Get store offers if sticky is 1
   *
   * @param int $store_Id
   * @return array Zend_Db_Table_Select;
   */
  public function getSitestoreoffer($store_id) {
    $result_offer = $this->fetchRow(array("store_id =?" => $store_id, 'sticky= ?' => "1"));
    return $result_offer;
  }

  /**
   * Get store offer detail
   *
   * @param int $offerId
   * @return array Zend_Db_Table_Select;
   */
  public function getOfferDetail($offerId = null) {
    
    if(empty($offerId))
      return ;
    $select = $this->select()->where($this->info('name') . '.offer_id = ?', $offerId)->limit(1);
    return $this->fetchRow($select);
  }

  /**
   * Make sticky and corrosponding data entry
   *
   * @param int $offer_id
   * @param int $store_id
   */
  public function makeSticky($offer_id, $store_id) {

    $sticky = $this->select()
                    ->from($this->info('name'), array('sticky'))
                    ->where('offer_id = ?', $offer_id)
                    ->query()
                    ->fetchColumn();
    if (!empty($sticky)) {
      $this->update(array('sticky' => 0), array('offer_id = ?' => $offer_id, 'store_id = ?' => $store_id));
      $sitestoreTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
      $sitestoreTable->update(array('offer' => 0), array('store_id = ?' => $store_id));
    } else {
      $this->update(array('sticky' => 1), array('offer_id = ?' => $offer_id, 'store_id = ?' => $store_id));
      $sitestoreTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
      $sitestoreTable->update(array('offer' => 1), array('store_id = ?' => $store_id));
      $this->update(array('sticky' => 0), array('offer_id != ?' => $offer_id, 'store_id = ?' => $store_id));
    }
  }
  
  public function changeStatus($offer_id , $store_id)
  {
    $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offer_id);
    $sitestoreoffer->status = empty($sitestoreoffer->status)? 1: 0;
    $sitestoreoffer->save();
    return;
//    
//    if(!empty($status))
//        $this->update(array('status' => 0), array('offer_id = ?' => $offer_id, 'store_id = ?' => $store_id));
//    else
//        $this->update(array('status' => 1), array('offer_id = ?' => $offer_id, 'store_id = ?' => $store_id));
//      
  }

  /**
   * Return store offers
   *
   * @param int $totalOffers
   * @param string $offerType
   * @return Zend_Db_Table_Select
   */
  public function getWidgetOffers($totalOffers, $offerType,$category_id,$popularity = null) {

    //OFFER TABLE NAME
    $offerTableName = $this->info('name');

    //STORE TABLE
    $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storeTable->info('name');
  
    $storePackagesTable = Engine_Api::_()->getDbtable('packages', 'sitestore');
    $storePackageTableName = $storePackagesTable->info('name');

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
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($storeTableName, array('photo_id', 'title as sitestore_title'))
                    ->join($offerTableName, $offerTableName . '.store_id = ' . $storeTableName . '.store_id');

    if ($offerType == 'sponsored') {
       
        $select = $select->where("($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$currentTime' OR $offerTableName.end_settings = 0)");
				$select->join($storePackageTableName, "$storePackageTableName.package_id = $storeTableName.package_id",array('package_id', 'price'));
        $select->where($storePackageTableName . '.price != ?', '0.00');
        $select->order($storePackageTableName . '.price' . ' DESC');

    }

    if ($offerType == 'hot') {
      $select = $select->where("($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$currentTime' OR $offerTableName.end_settings = 0)  AND ($offerTableName.hotoffer  = 1)")
                      ->order('RAND() DESC ');
    } elseif ($offerType == 'latest') {
      $select = $select->where("($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$currentTime' OR $offerTableName.end_settings = 0)")
                      ->limit($totalOffers)
                      ->order('creation_date DESC');
    }

    if ($offerType == 'alloffers') {
      $select = $select->where("($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$currentTime' OR $offerTableName.end_settings = 0)");
			if ($popularity == 'view_count') {
				$select->order($offerTableName . '.view_count' . ' DESC');
			}
      elseif ($popularity == 'like_count') {
			  $select->order($offerTableName . '.like_count' . ' DESC');
			}
			elseif ($popularity == 'comment_count') {
				$select->order($offerTableName . '.comment_count' . ' DESC');
			}
			elseif ($popularity == 'popular') {
        $select->where($offerTableName . '.claimed !=?','0');
				$select->order($offerTableName . '.claimed' . ' DESC');
        $select->order($offerTableName . '.creation_date DESC');
			}
    }


    if (!empty($category_id)) {
			$select = $select->where($storeTableName . '.	category_id =?', $category_id);
		}
    $select = $select->limit($totalOffers);

    $select = $select
                    ->where($storeTableName . '.closed = ?', '0')
                    ->where($storeTableName . '.approved = ?', '1')
                    ->where($storeTableName . '.search = ?', '1')
                    ->where($storeTableName . '.declined = ?', '0')
                    ->where($storeTableName . '.draft = ?', '1');

    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $select->where($storeTableName . '.expiration_date  > ?', $currentTime);
    }

    //Start Network work
    $select = $storeTable->getNetworkBaseSql($select, array('not_groupBy' => 1, 'extension_group' => $offerTableName . ".offer_id"));
    //End Network work
    $select
                    ->where($offerTableName . '.status = ?', '1')
                    ->where($offerTableName . '.public = ?', '1')
                    ->where($offerTableName . '.approved = ?', '1');
    return $this->fetchAll($select);
  }

  /**
   * Return store offers
   *
   * @param string $hotOffer
   * @return Zend_Db_Table_Select
   */
  public function getOffers($hotOffer = 'null',$params = array(),$sponsoredOffer = null) {

    //OFFER TABLE NAME
    $offerTableName = $this->info('name');

    //STORE TABLE
    $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storeTable->info('name');

    $storePackagesTable = Engine_Api::_()->getDbtable('packages', 'sitestore');
    $storePackageTableName = $storePackagesTable->info('name');
    
    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $currentTime = date("Y-m-d H:i:s");
    if (!empty($viewer_id)) {
      // Convert times
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($viewer->timezone);
      $currentTime = date("Y-m-d H:i:s");
    }
    
    //QUERY MAKING
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($storeTableName, array('photo_id', 'title as sitestore_title'))
                    ->join($offerTableName, $offerTableName . '.store_id = ' . $storeTableName . '.store_id')
                    ->join($storePackageTableName, "$storePackageTableName.package_id = $storeTableName.package_id",array('package_id', 'price'));

    if (empty($hotOffer) && (isset($params['orderby']) && $params['orderby'] != 'end_offer')) {
      $select = $select
                      ->where("($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$currentTime' OR $offerTableName.end_settings = 0)");
    } elseif ($hotOffer == 1) {
      $select = $select
                      ->where("($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$currentTime' OR $offerTableName.end_settings = 0)  AND ($offerTableName.hotoffer  = 1)");
    }

    if(!empty($sponsoredOffer)) {

      $select->where($storePackageTableName . '.price != ?', '0.00');
      $select->order($storePackageTableName . '.price' . ' DESC');

    }

    if (!empty($params['category'])) {
      $select->where($storeTableName . '.category_id = ?', $params['category']);
    }

    if (!empty($params['category_id'])) {
      $select->where($storeTableName . '.category_id = ?', $params['category_id']);
    }

		if (!empty($params['subcategory'])) {
      $select->where($storeTableName . '.subcategory_id = ?', $params['subcategory']);
    }

    if (!empty($params['subcategory_id'])) {
      $select->where($storeTableName . '.subcategory_id = ?', $params['subcategory_id']);
    }

    if (!empty($params['subsubcategory'])) {
      $select->where($storeTableName . '.subsubcategory_id = ?', $params['subsubcategory']);
    }

    if (!empty($params['subsubcategory_id'])) {
      $select->where($storeTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
    }
		if(empty($params['orderby'])) {
			$order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.order', 1);
			switch ($order) {
				case "1":
					$select->order($offerTableName . '.creation_date DESC');
					break;
				case "2":
					$select->order($offerTableName . '.title');
					break;
				case "3":
					$select->order($offerTableName . '.hotoffer' . ' DESC');
					break;
				case "4":
					$select->order($storeTableName . '.package_id' . ' DESC');
					break;
				case "5":
					$select->order($offerTableName . '.hotoffer' . ' DESC');
					$select->order($storePackageTableName . '.price' . ' DESC');
					break;
				case "6":
					$select->order($storePackageTableName . '.price' . ' DESC');
					$select->order($offerTableName . '.hotoffer' . ' DESC');
					break;
			}
		}
    
		if (isset($params['orderby']) || !empty($params['offer'])) {
        if($params['orderby'] == 'hotoffer') {
        $select->where($offerTableName . '.hotoffer = ?', '1');
        }
        elseif ($params['orderby'] == 'end_week') {
          $time_duration = date('Y-m-d H:i:s', strtotime('7 days'));
					$sqlTimeStr = ".end_time BETWEEN " . "'" . $current_time . "'" . " AND " . "'" . $time_duration . "'";
					$select = $select->where($offerTableName . "$sqlTimeStr");
					$select = $select
												->where("($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$currentTime')");
        }
        elseif ($params['orderby'] == 'end_offer') {
					$select = $select
												->where("($offerTableName.end_settings = 1 AND $offerTableName.end_time < '$currentTime')");
        }
        elseif ($params['orderby'] == 'end_month') {
          $time_duration = date('Y-m-d H:i:s', strtotime('1 months'));
					$sqlTimeStr = ".end_time BETWEEN " . "'" . $current_time . "'" . " AND " . "'" . $time_duration . "'";
					$select = $select->where($offerTableName . "$sqlTimeStr");
					$select = $select
												->where("($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$currentTime')");
        }
        elseif ($params['orderby'] == 'sponsored offer') {
           //$select->where($offerTableName . '.paid = ?', '1');
           $select->where($storePackageTableName . '.price != ?', '0.00');
           $select->order($storePackageTableName . '.price' . ' DESC');
        }
        elseif ($params['orderby'] == 'view_count' || $params['offer'] == 'view') {
          $select->order($offerTableName . '.view_count' . ' DESC');
        }
        elseif ($params['orderby'] == 'comment_count' || $params['offer'] == 'comment') {
          $select->order($offerTableName . '.comment_count' . ' DESC');
        }
        elseif ($params['orderby'] == 'like_count' || $params['offer'] == 'like') {
          $select->order($offerTableName . '.like_count' . ' DESC');
        }
        elseif ($params['orderby'] == 'claimed' || $params['offer'] == 'popular') {
          $select->where($offerTableName . '.claimed != ?', '0');
          $select->order($offerTableName . '.claimed' . ' DESC');
        }
				elseif ((isset($params['sitestore_location']) && !empty($params['sitestore_location'])) || (!empty($params['formatted_address']))) {
          $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestore');
          $locationName = $locationTable->info('name');
					$enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximitysearch', 1);
					if (isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable))) {
						$longitude = 0;
						$latitude = 0;
						$selectLocQuery = $locationTable->select()->where('location = ?', $params['sitestore_location']);
						$locationValue = $locationTable->fetchRow($selectLocQuery);

						//check for zip code in location search.
						if(empty($params['Latitude']) && empty($params['Longitude'])) {
							if (empty($locationValue)) {
                $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['sitestore_location'], 'module' => 'Stores / Marketplace - Ecommerce Offers'));
                if(!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
                    $latitude = $locationResults['latitude'];
                    $longitude = $locationResults['longitude'];
                }
							} else {
								$latitude = (float) $locationValue->latitude;
								$longitude = (float) $locationValue->longitude;
							}
						} else {
							$latitude = (float) $params['Latitude'];
							$longitude = (float) $params['Longitude'];
						}

						$radius = $params['locationmiles']; //in miles

						$flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximity.search.kilometer', 0);
						if (!empty($flage)) {
							$radius = $radius * (0.621371192);
						}
						//$latitudeRadians = deg2rad($latitude);
    $latitudeSin = "sin(radians($latitude))";
    $latitudeCos = "cos(radians($latitude))";
						$select->join($locationName, "$storeTableName.store_id = $locationName.store_id   ", null);
						$sqlstring = "((degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
						$sqlstring .= ") OR (" . $locationName . ".latitude = '" . $latitude . "' AND  " . $locationName . ".longitude= '" . $longitude . "'))";
						$select->where($sqlstring);
					} 
					else {
// 						if ($params['sitestore_postalcode'] == 'postalCode') { 
// 							$select->join($locationName, "$storeTableName.store_id = $locationName.store_id", null);
// 							$select->where("`{$locationName}`.formatted_address LIKE ? ", "%" . $params['formatted_address'] . "%");
// 						} 
// 						else {
							$select->join($locationName, "$storeTableName.store_id = $locationName.store_id", null);
							$select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['sitestore_location']) . "%");
						//}
					}
				}
        else {
          $select->order($offerTableName . '.creation_date DESC');
        }
    }
    $select->order($offerTableName . '.creation_date DESC');
    if (!empty($params['title'])) {

       $select->where($storeTableName . ".title LIKE ? ", '%' . $params['title'] . '%');
    }


    if (!empty($params['search_offer'])) {

       $select->where($offerTableName . ".title LIKE ? ", '%' . $params['search_offer'] . '%');
    }

    $select = $select
                    ->where($storeTableName . '.closed = ?', '0')
                    ->where($storeTableName . '.approved = ?', '1')
                    ->where($storeTableName . '.search = ?', '1')
                    ->where($storeTableName . '.declined = ?', '0')
                    ->where($storeTableName . '.draft = ?', '1');

    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $select->where($storeTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }

   if (isset($params['orderby']) && $params['orderby'] == 'Networks') {
          $select = $storeTable->getNetworkBaseSql($select, array('browse_network' => 1));

     }
   
    //Start Network work
  $select = $storeTable->getNetworkBaseSql($select, array('not_groupBy' => 1, 'extension_group' => $offerTableName . ".offer_id"));
    //End Network work
  
    if (!empty($viewer_id)) {
      // Convert times
      date_default_timezone_set($oldTz);
    }
  
    $select
                    ->where($offerTableName . '.status = ?', '1')
                    ->where($offerTableName . '.public = ?', '1')
                    ->where($offerTableName . '.approved = ?', '1');
    if(isset($params['offertype']) && $params['offertype'] = 'hotoffer') {
			if (isset($params['limit']) && !empty($params['limit'])) {
				if (!isset($params['start_index']))
					$params['start_index'] = 0;
				$select->limit($params['limit'], $params['start_index']);
			}
      return $this->fetchAll($select);
    }
    else {
			return Zend_Paginator::factory($select);
    }
  }

  public function getOfferList() {
    global $sitestoreoffer_list;
    return $sitestoreoffer_list;
  }

  /**
   * Get store offers list
   *
   * @param array $params
   * @param int $var
   * @param int $show_count
   * @return array $paginator;
   */
  public function getsitestoreoffersPaginator($params = 0, $var = null, $show_count = null, $can_create_offer = null) {
    $paginator = Zend_Paginator::factory($this->getsitestoreoffersSelect($params, $var, $show_count, $can_create_offer));
    if (!empty($params['store'])) {
      $paginator->setCurrentPageNumber($params['store']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }    
    return $paginator;
  }

  /**
   * Get store offer select query
   *
   * @param array $params
   * @param int $var
   * @param int $show_count
   * @return string $select;
   */
  public function getsitestoreoffersSelect($store_id = 0, $var, $show_count = null, $can_create_offer = null) {

    //OFFER TABLE NAME
    $offerTable = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');
    $offerTableName = $offerTable->info('name');

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    
    //GET CURRENT TIME
    $currentTime = date("Y-m-d H:i:s");
    if (!empty($viewer_id)) {
      // Convert times
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($viewer->timezone);
      $currentTime = date("Y-m-d H:i:s");
    }

    //QUERY MAKING
    if ($show_count) {
      $select = $offerTable->select()
                      ->from($offerTableName, array(
                          'COUNT(*) AS show_count'))
                      ->where($offerTableName . '.store_id = ?', $store_id);
    } else {
      $select = $offerTable->select()
                      ->from($offerTableName)
                      ->where($offerTableName . '.store_id = ?', $store_id)
                      ->order('sticky DESC')
                      ->order('creation_date DESC');
    }

    if (!empty($can_create_offer)) {
      $select = $offerTable->select()
                      ->from($offerTableName)
                      ->where($offerTableName . '.store_id = ?', $store_id)
                      ->order('sticky DESC')
                      ->order('creation_date DESC');
    } elseif ($var == 1) {
      $select = $select
                      ->where("($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$currentTime' OR $offerTableName.end_settings = 0)");
    }
    if(empty($can_create_offer)){
      $select = $select
                    ->where($offerTableName . '.status = ?', '1')
                    ->where($offerTableName . '.public = ?', '1')
                    ->where($offerTableName . '.approved = ?', '1');
    }
    if (!empty($viewer_id)) {
      // Convert times
      date_default_timezone_set($oldTz);
    }
    return $select;
  }

  public function topcreatorData($limit = null,$category_id) {

    //OFFER TABLE NAME
    $offerTableName = $this->info('name');

    //STORE TABLE
    $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storeTable->info('name');

    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($storeTableName, array('photo_id', 'title as sitestore_title','store_id'))
                    ->join($offerTableName, "$storeTableName.store_id = $offerTableName.store_id", array('COUNT(engine4_sitestore_stores.store_id) AS item_count'))
                    ->where($storeTableName.'.approved = ?', '1')
										->where($storeTableName.'.declined = ?', '0')
										->where($storeTableName.'.draft = ?', '1')
                    ->where($offerTableName . '.status = ?', '1')
                    ->where($offerTableName . '.public = ?', '1')
                    ->where($offerTableName . '.approved = ?', '1')
                    ->group($offerTableName . ".store_id")
                    ->order('item_count DESC')
                    ->limit($limit);
    if (!empty($category_id)) {
      $select->where($storeTableName . '.category_id = ?', $category_id);
    }
    return $select->query()->fetchAll();
  }

  /**
   * Return offer count
   *
   * @param int $store_id
   * @return offer count
   */
  public function getStoreOfferCount($store_id) {

    $selectOffer = $this->select()
                    ->from($this->info('name'), 'count(*) as count')
                    ->where('store_id = ?', $store_id);
    $data = $this->fetchRow($selectOffer);
    return $data->count;
  }

  /**
   * Return offer of the day
   *
   * @return Zend_Db_Table_Select
   */
  public function offerOfDay() {

     //GET LOGGED IN USER INFORMATION
    $db = $this->getAdapter();
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if (!empty($viewer_id)) {
      // Convert times
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($viewer->timezone);
    }
  
    //CURRENT DATE TIME
    $date = date("Y-m-d H:i:s");

    //GET ITEM OF THE DAY TABLE NAME
    $offerOfTheDayTableName = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore')->info('name');

		//GET STORE TABLE NAME
		$storeTableName = Engine_Api::_()->getDbtable('stores', 'sitestore')->info('name');

    //GET OFFER TABLE NAME
    $offerTableName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($offerTableName, array('offer_id', 'title', 'store_id', 'owner_id', 'description','photo_id','claimed','claim_count','end_settings','end_time', 'start_time', 'url', 'coupon_code', 'discount_type', 'discount_amount', 'minimum_purchase', 'status' , 'public', 'approved'))
                    ->join($offerOfTheDayTableName, $offerTableName . '.offer_id = ' . $offerOfTheDayTableName . '.resource_id', array('resource_type'))
										->join($storeTableName, $offerTableName . '.store_id = ' . $storeTableName . '.store_id', array('approved', 'declined', 'draft'))
										->where($storeTableName.'.approved = ?', '1')
										->where($storeTableName.'.declined = ?', '0')
										->where($storeTableName.'.draft = ?', '1')
                    ->where($offerTableName . '.status = ?', '1')
                    ->where($offerTableName . '.public = ?', '1')
                    ->where($offerTableName . '.approved = ?', '1')
                    ->where($offerOfTheDayTableName . '.resource_type = ?', 'sitestoreoffer_offer')
                    ->where('start_time <= ?', $date)
                    ->where('(' . $db->quoteInto('end_settings = ?', 0) . ') OR (' . $db->quoteInto('end_settings = ?', 1) . ' AND ' . $db->quoteInto('end_time >= ?', $date) . ')')
                    //->where('end_settings = 0 OR end_time >= ?', $date)
                    ->order('Rand()');
		//STORE SHOULD BE AUTHORIZED
    if (Engine_Api::_()->sitestore()->hasPackageEnable())
      $select->where($storeTableName.'.expiration_date  > ?', date("Y-m-d H:i:s"));

		//STORE SHOULD BE AUTHORIZED
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.status.show', 0);
    if ($stusShow == 0) {
      $select->where($storeTableName.'.closed = ?', '0');
    }

    if (!empty($viewer_id)) {
      // Convert times
      date_default_timezone_set($oldTz);
    }
    //RETURN RESULTS
    return $this->fetchRow($select);
  }
  
//  public function isCouponExist($coupn_code)
//  {
//    return $this->select()
//              ->from($this->info('name'), 'offer_id')
//              ->where('coupon_code LIKE ?', $coupn_code)
//              ->query()->fetchColumn();
//  }
  
  public function getCouponInfo($params, $columns = array('offer_id'))
  {
      $select = $this->select()
              ->from($this->info('name'), $columns);
      
      if(array_key_exists('coupon_code', $params))
             $select->where('coupon_code LIKE ?', $params['coupon_code']);
      
      if(array_key_exists('fetchColumn', $params))
          return $select->query()->fetchColumn();
      
      if(array_key_exists('fetchRow', $params))
          return $this->fetchRow($select);
      
      if(array_key_exists('fetchAll', $params))
          return $select->query()->fetchAll();
             
    return $select;
  }
  
  public function updateClaims($coupon_id){
    $uses_count = $this->select()
                  ->from($this->info('name'), 'claimed')
                  ->where('offer_id = ?', $coupon_id)
                  ->limit(1)
                  ->query()->fetchColumn();
    $uses_count = ++$uses_count;       
    
    $this->update(array(
            'claimed' => $uses_count,
                ), array(
            'offer_id = ?' => $coupon_id
        ));
    return ;
  }

}
?>