<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Api_Core extends Core_Api_Abstract {

  protected $_GETMENUSID = 15782;
  protected $_privacy = array();  // $_privacy['guid']['user_id']['privacy_type']=$privacy;

  /**
   * Privacy settings base on level and package
   *
   * @param object $sitestore
   * @param string $privacy_type
   * @return int $is_manage_admin
   */

  public function isManageAdmin($sitestore, $privacy_type) {
    if (empty($sitestore) || empty($privacy_type))
      return 0;
    $store_id = $sitestore->store_id;
    //store is declined then not edit store
    if (!empty($sitestore->declined) && ($privacy_type == "edit")) {
      return 0;
    }

    if ($privacy_type == "view" && !$this->canViewStore($sitestore)) {
      return 0;
    }

    if ($this->hasPackageEnable()) {

      $packageInclude = array("tellafriend" => "tfriend", "print" => "print", "overview" => "overview", "map" => "map", "insights" => "insight", /* "layout" => "layout", */ "contact_details" => "contact", "profile" => "profile", "sendupdate" => "sendupdate", "twitter" => "twitter");
      $packageOwnerModules = array("sitestoreoffer" => "offer", "sitestoreform" => "form", "sitestoreinvite" => "invite", "sitestorebadge" => "badge", "sitestorelikebox" => "likebox", "sitestoredocument" => "document", "sitestoremember" => "smecreate");
      $subModules = array("sitestoredocument" => "sdcreate", "sitestorenote" => "sncreate", "sitestorepoll" => "splcreate", "sitestoreevent" => "secreate", "sitestorevideo" => "svcreate", "sitestorealbum" => "spcreate", "sitestoremusic" => "smcreate");

      // $packageSubModule = $this->getEnableSubModules();
      //non sub modules
      $search_Key = array_search($privacy_type, $packageInclude);
      if (!empty($search_Key)) {
        return $this->allowPackageContent($sitestore->package_id, $search_Key);
      }

      //owner base submodules
      $packageOwnerSubModule = @array_search($privacy_type, $packageOwnerModules);
      if (!empty($packageOwnerSubModule)) {
        return $this->allowPackageContent($sitestore->package_id, "modules", $packageOwnerSubModule);
      }

      //owner base and also depeanded on viewer
      $subModule = @array_search($privacy_type, $subModules);
      if (!empty($subModule)) {
        if (!$this->allowPackageContent($sitestore->package_id, "modules", $subModule))
          return 0;
      }
    }else {

      $levelInclude = array("tellafriend" => "tfriend", "print" => "print", "overview" => "overview", "map" => "map", "insights" => "insight", /* "layout" => "layout", */ "contact_details" => "contact", "profile" => "profile", "sendupdate" => "sendupdate", "twitter" => "twitter");
      $levelOwnerModules = array("sitestoreoffer" => "offer", "sitestoreform" => "form", "sitestoreinvite" => "invite", "sitestorebadge" => "badge", "sitestorelikebox" => "likebox", "sitestoredocument" => "document", "sitestoremember" => "smecreate");
      $levelStoreBaseSubModule = array("sitestoredocument" => "sdcreate", "sitestorenote" => "sncreate", "sitestorepoll" => "splcreate", "sitestoreevent" => "secreate", "sitestorevideo" => "svcreate", "sitestorealbum" => "spcreate", "sitestoremusic" => "smcreate");
      //non sub modules
      $search_Key = array_search($privacy_type, $levelInclude);
      if (!empty($search_Key)) {
        $store_owner = Engine_Api::_()->getItem('user', $sitestore->owner_id);
        $can_edit = $this->getManageAdminPrivacyCache('sitestore_store', "level_" . $store_owner->level_id, $privacy_type);
        if ($can_edit == -1) {
          $can_edit = Engine_Api::_()->authorization()->getPermission($store_owner->level_id, 'sitestore_store', $privacy_type); //Engine_Api::_()->authorization()->isAllowed($sitestore, $store_owner, $privacy_type);
          $this->setManageAdminPrivacyCache('sitestore_store', "level_" . $store_owner->level_id, $privacy_type, $can_edit);
        }
        if (empty($can_edit)) {
          return 0;
        } else {
          return 1;
        }
      }

      //owner base submodules
      $levelsubModule = @array_search($privacy_type, $levelOwnerModules);
      if (!empty($levelsubModule)) {
        $store_owner = Engine_Api::_()->getItem('user', $sitestore->owner_id);
        $can_edit = $this->getManageAdminPrivacyCache('sitestore_store', "level_" . $store_owner->level_id, $privacy_type);
        if ($can_edit == -1) {
          $can_edit = Engine_Api::_()->authorization()->getPermission($store_owner->level_id, 'sitestore_store', $privacy_type);
          $this->setManageAdminPrivacyCache('sitestore_store', "level_" . $store_owner->level_id, $privacy_type, $can_edit);
        }
        if (empty($can_edit)) {
          return 0;
        } else {
          return 1;
        }
      }

      //owner base and also depeanded on viewer
      $levelsubModule = @array_search($privacy_type, $levelStoreBaseSubModule);
      if (!empty($levelsubModule)) {
        $store_owner = Engine_Api::_()->getItem('user', $sitestore->owner_id);
        $can_edit = $this->getManageAdminPrivacyCache('sitestore_store', "level_" . $store_owner->level_id, $privacy_type);
        if ($can_edit == -1) {
          $can_edit = Engine_Api::_()->authorization()->getPermission($store_owner->level_id, 'sitestore_store', $privacy_type);
          $this->setManageAdminPrivacyCache('sitestore_store', "level_" . $store_owner->level_id, $privacy_type, $can_edit);
        }
        if (empty($can_edit)) {
          return 0;
        }
      }
    }
    $existManageAdmin = 0;
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $manageAdminEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1);
    if (!empty($viewer_id) && !empty($manageAdminEnable)) {
      $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
      $manageadminTableName = $manageadminTable->info('name');
      $select = $manageadminTable->select()
              ->from($manageadminTableName, 'manageadmin_id')
              ->where('user_id = ?', $viewer_id)
              ->where('store_id = ?', $store_id)
              ->limit(1);
      $row = $manageadminTable->fetchAll($select)->toArray();
      if (!empty($row[0]['manageadmin_id'])) {
        $existManageAdmin = 1;
      } else {
        $existManageAdmin = 0;
      }
    }

    $is_manage_admin = 1;
    if ($existManageAdmin == 0 || $viewer_id == $sitestore->owner_id) {
      if (empty($viewer_id)) {
        $viewer = null;
        $viewer_id = 0;
      }
      $viewer_guid = 'user' . "_" . $viewer_id;
      $can_edit = $this->getManageAdminPrivacyCache($sitestore->getGuid(), $viewer_guid, $privacy_type);
      if ($can_edit == -1) {
        $can_edit = Engine_Api::_()->authorization()->isAllowed($sitestore, $viewer, $privacy_type);
        $this->setManageAdminPrivacyCache($sitestore->getGuid(), $viewer_guid, $privacy_type, $can_edit);
      }

      if (empty($can_edit)) {
        $is_manage_admin = 0;
      }
    } elseif ($existManageAdmin == 1 && $viewer_id != $sitestore->owner_id) {
      $store_owner = Engine_Api::_()->getItem('user', $sitestore->owner_id);
      $can_edit = $this->getManageAdminPrivacyCache($sitestore->getGuid(), $store_owner->getGuid(), $privacy_type);
      if ($can_edit == -1) {
        $can_edit = Engine_Api::_()->authorization()->isAllowed($sitestore, $store_owner, $privacy_type);
        $this->setManageAdminPrivacyCache($sitestore->getGuid(), $store_owner->getGuid(), $privacy_type, $can_edit);
      }

      if (empty($can_edit)) {
        $is_manage_admin = 0;
      }
    }


    return $is_manage_admin;
  }


  //Function to return Currency Conversion rate
  public function getPriceWithCurrency($price, $priceOnly = 0, $search = 0) {    
 
      if (empty($price)) {
          return $price;
      } 
      if(Engine_Api::_()->hasModuleBootstrap('sitemulticurrency') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemulticurrency') && Engine_Api::_()->getDbtable('modules', 'sitemulticurrency')->isModuleEnable('sitestore')) {  
          $priceStr = Engine_Api::_()->sitemulticurrency()->convertCurrencyRate($price, $priceOnly, $search);  
      } else {  
          $defaultParams = array();
          $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      if (empty($viewer_id)) {
          $defaultParams['locale'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
      } 
          $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
          $defaultParams['precision'] = 2;
          $price = (float) $price;
          if(!empty($priceOnly))
          return $price;
        
          $priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($price, $currency, $defaultParams); 
      } 
      return $priceStr;
  }
  public function setManageAdminPrivacyCache($index, $member_index, $privacy_type, $privacy) {
    return $this->_privacy[$index][$member_index][$privacy_type] = $privacy;
  }

  public function getManageAdminPrivacyCache($index, $member_index, $privacy_type) {
    //  print_r($this->_privacy);
    if (isset($this->_privacy[$index][$member_index][$privacy_type])) {
      return $this->_privacy[$index][$member_index][$privacy_type];
    } else {
      return -1;
    }
  }

  /**
   * viewer is store owner or store admin
   *
   * @param object $sitestore
   * @return bool $isStoreOwnerFlage
   */
  public function isStoreOwner($sitestore, $user = null) {
    if (empty($user))
      $user = Engine_Api::_()->user()->getViewer();
    $viewer_id = $user->getIdentity();

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $isStoreOwnerFlage = false;
    if (empty($viewer_id))
      return $isStoreOwnerFlage;
    if ($sitestore->owner_id == $viewer_id)
      return true;

    $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
    $manageadminTableName = $manageadminTable->info('name');
    $select = $manageadminTable->select()
            ->from($manageadminTableName, 'manageadmin_id')
            ->where('user_id = ?', $viewer_id)
            ->where('store_id = ?', $sitestore->store_id);
    $row = $manageadminTable->fetchRow($select);
    if (!empty($row))
      $isStoreOwnerFlage = true;

    return $isStoreOwnerFlage;
  }

  /**
   * allow to store owner
   *
   * @param object $sitestore
   * @param string $privacy_type
   * @return bool $canDo
   */
  public function isStoreOwnerAllow($sitestore, $privacy_type) {
    if (empty($sitestore))
      return;
    $store_owner = Engine_Api::_()->getItem('user', $sitestore->owner_id);

    return (bool) $canDo = Engine_Api::_()->authorization()->getPermission($store_owner->level_id, 'sitestore_store', $privacy_type);
  }

  public function setDisabledType() {
    $modArray = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.mod.settings', 0));
    $modArrayType = unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.mod.types', 0));
    if (!empty($modArray)) {
      foreach ($modArray as $modName) {
        $newModArray[] = strrev($modName);
      }
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.mod.settings', serialize($newModArray));
    }
    if (!empty($modArrayType)) {
      foreach ($modArrayType as $modNameType) {
        $newModArrayType[] = strrev($modNameType);
      }
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.mod.types', serialize($newModArrayType));
    }
  }

  /**
   * Get store id
   *
   * @param string $store_url
   * @return int $storeID
   */
  public function getStoreId($store_url, $storeId = null) {
    $storeID = 0;
    if (!empty($store_url)) {
      $sitestore_table = Engine_Api::_()->getItemTable('sitestore_store');
      if (!empty($storeId)) {
        $store = $sitestore_table->fetchRow(array('store_url = ?' => $store_url, 'store_id != ?' => $storeId));
      } else {
        $store = $sitestore_table->fetchRow(array('store_url = ?' => $store_url));
      }
      if (!empty($store))
        $storeID = $store->store_id;
    }

    return $storeID;
  }

  /**
   * Get store url
   *
   * @param int $store_id
   * @return string $storeUrl
   */
  public function getStoreUrl($store_id) {

    $storeUrl = 0;
    if (!empty($store_id)) {
      $sitestore_table = Engine_Api::_()->getItemTable('sitestore_store');
      $store = $sitestore_table->fetchRow(array('store_id = ?' => $store_id));
      if (!empty($store))
        $storeUrl = $store->store_url;
    }
    return $storeUrl;
  }

  /**
   * Get store list
   *
   * @param array $params
   * @param array $customParams
   * @return array $paginator;
   */
  public function getSitestoresPaginator($params = array(), $customParams = null) {

    $paginator = Zend_Paginator::factory($this->getSitestoresSelect($params, $customParams));
    if (!empty($params['store'])) {
      $paginator->setCurrentPageNumber($params['store']);
    }

    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  /**
   * Get store select query
   *
   * @param array $params
   * @param array $customParams
   * @return string $select;
   */
  public function getSitestoresSelect($params = array(), $customParams = null) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $table = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $rName = $table->info('name');

    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
    if (!empty($moduleEnabled)) {
      $membertable = Engine_Api::_()->getDbtable('membership', 'sitestore');
      $membertableName = $membertable->info('name');
    }

    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'search')->info('name');

    $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestore');
    $locationName = $locationTable->info('name');
    $select = $table->select()->setIntegrityCheck(false);

    if (isset($params['browse_store']) && !empty($params['browse_store'])) {
      $columnsArray = array('store_id', 'title', 'store_url', 'body', 'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'modified_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'closed', 'offer', 'email', 'website', 'phone', 'package_id', 'follow_count');

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
        $columnsArray = array_merge(array('member_count'), $columnsArray);
        $columnsArray = array_merge(array('member_title'), $columnsArray);
      }

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge'))
        $columnsArray[] = 'badge_id';

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer'))
        $columnsArray[] = 'offer';

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
        $columnsArray[] = 'review_count';
        $columnsArray[] = 'rating';
      }

      $select = $select->from($rName, $columnsArray);
    } else {
      $select = $select->from($rName);
    }

    if (isset($params['type']) && !empty($params['type']) && $params['type'] != 'manage') {
      if ($params['type'] == 'browse' || $params['type'] == 'home') {
        $select = $select
                ->where($rName . '.approved = ?', '1')
                ->where($rName . '.declined = ?', '0')
                ->where($rName . '.draft = ?', '1');

        if (!empty($moduleEnabled) && isset($params['type_location']) && $params['type_location'] != 'browseLocation' && $params['type_location'] != 'browseStore' && $params['type_location'] == 'profilebrowseStore') {
          $select = $select->join($membertableName, "$membertableName.store_id = $rName.store_id", array('user_id AS store_owner_id'));
          if (!empty($values['adminstores'])) {
            $select = $select->where($membertableName . '.user_id = ?', $params['user_id']);
          }
          if (isset($params['onlymember']) && !empty($params['onlymember'])) {
            $select = $select->where($rName . '.store_id IN (?)', (array) $params['onlymember']);
          }
          $select = $select->where($membertableName . '.active = ?', 1);
        }


        $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.status.show', 0);
        if ($stusShow == 0) {
          $select = $select
                  ->where($rName . '.closed = ?', '0');
        }
        if ($this->hasPackageEnable())
          $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      } elseif ($params['type'] == 'browse_home_zero') {
        $select = $select
                ->where($rName . '.closed = ?', '0')
                ->where($rName . '.approved = ?', '1')
                ->where($rName . '.declined = ?', '0')
                ->where($rName . '.draft = ?', '1');
        if ($this->hasPackageEnable())
          $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
      $select->where($rName . ".search = ?", 1);
    }
    if (isset($customParams)) {

      $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
      $coreversion = $coremodule->version;
      if ($coreversion > '4.1.7') {

        //PROCESS OPTIONS
        $tmp = array();
        foreach ($customParams as $k => $v) {
          if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
            continue;
          } else if (false !== strpos($k, '_field_')) {
            list($null, $field) = explode('_field_', $k);
            $tmp['field_' . $field] = $v;
          } else if (false !== strpos($k, '_alias_')) {
            list($null, $alias) = explode('_alias_', $k);
            $tmp[$alias] = $v;
          } else {
            $tmp[$k] = $v;
          }
        }
        $customParams = $tmp;
      }

      $select = $select
              ->setIntegrityCheck(false)
              ->joinLeft($searchTable, "$searchTable.item_id = $rName.store_id", null);

      $searchParts = Engine_Api::_()->fields()->getSearchQuery('sitestore_store', $customParams);
      foreach ($searchParts as $k => $v) {
        //$v = str_replace("%2C%20",", ",$v);
        $select->where("`{$searchTable}`.{$k}", $v);
      }
    }

    if (isset($params['sitestore_price']) && !empty($params['sitestore_price'])) {

      if ((!empty($params['sitestore_price']['min']) && !empty($params['sitestore_price']['max']))) {

        if ($params['sitestore_price']['max'] < $params['sitestore_price']['min']) {
          $min = $params['sitestore_price']['max'];
          $max = $params['sitestore_price']['min'];
        } else {
          $min = $params['sitestore_price']['min'];
          $max = $params['sitestore_price']['max'];
        }
      $select = $select->where($rName . '.price >= ?', $this->getPriceWithCurrency($min, 1, 1))->where($rName . '.price <= ?', $this->getPriceWithCurrency($max, 1, 1));
      }

      if ((empty($params['sitestore_price']['min']) && !empty($params['sitestore_price']['max']))) {
        $select = $select->where($rName . '.price <= ?', $this->getPriceWithCurrency($params['sitestore_price']['max'], 1, 1));
      }

      if ((!empty($params['sitestore_price']['min']) && empty($param['sitestore_price']['max']))) {
        $select = $select->where($rName . '.price >= ?',$this->getPriceWithCurrency($params['sitestore_price']['min'], 1, 1));
      }
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer') && isset($params['offer_type']) && !empty($params['offer_type'])) {
      $offerTable = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');
      $offerTableName = $offerTable->info('name');
      $today = date("Y-m-d H:i:s");
      $select->setIntegrityCheck(false)
              ->join($offerTableName, "$offerTableName.store_id = $rName.store_id", array(''))
              ->where("$offerTableName.end_settings = 0 OR ($offerTableName.end_settings = 1 AND $offerTableName.end_time >= '$today')");

      if ($params['offer_type'] == 'hot') {
        $select->where("$offerTableName.hotoffer = 1");
      } elseif ($params['offer_type'] == 'featured') {
        $select->where("$offerTableName.sticky = 1");
      }
    }

//     if (isset($params['sitestore_postalcode']) && empty($params['sitestore_postalcode']) && isset($params['locationmiles']) && empty($params['locationmiles'])) {
    //check for stret , city etc in location search.
    if (isset($params['sitestore_street']) && !empty($params['sitestore_street'])) {
      $select->join($locationName, "$rName.store_id = $locationName.store_id   ", null);
      $select->where($locationName . '.address   LIKE ? ', '%' . $params['sitestore_street'] . '%');
    } if (isset($params['sitestore_city']) && !empty($params['sitestore_city'])) {
      $select->join($locationName, "$rName.store_id = $locationName.store_id   ", null);
      $select->where($locationName . '.city = ?', $params['sitestore_city']);
    } if (isset($params['sitestore_state']) && !empty($params['sitestore_state'])) {
      $select->join($locationName, "$rName.store_id = $locationName.store_id   ", null);
      $select->where($locationName . '.state = ?', $params['sitestore_state']);
    } if (isset($params['sitestore_country']) && !empty($params['sitestore_country'])) {
      $select->join($locationName, "$rName.store_id = $locationName.store_id   ", null);
      $select->where($locationName . '.country = ?', $params['sitestore_country']);
    }
// 		} else {
// 			$select->join($locationName, "$rName.store_id = $locationName.store_id   ", null);
// 			$select->where($locationName . '.zipcode = ?', $params['sitestore_postalcode']);
// 		}
    
    if (!isset($params['sitestore_location']) && isset($params['locationSearch']) && !empty($params['locationSearch'])) {
        $params['sitestore_location'] = $params['locationSearch'];

        if (isset($params['locationmilesSearch'])) {
            $params['locationmiles'] = $params['locationmilesSearch'];
        }
    }       

    if ((isset($params['sitestore_location']) && !empty($params['sitestore_location'])) || (!empty($params['formatted_address']))) {
      $enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximitysearch', 1);
      if (isset($params['locationmiles']) && (!empty($params['locationmiles']) && !empty($enable))) {
        $longitude = 0;
        $latitude = 0;


        //check for zip code in location search.
        if (empty($params['Latitude']) && empty($params['Longitude'])) {
          $selectLocQuery = $locationTable->select()->where('location = ?', $params['sitestore_location']);
          $locationValue = $locationTable->fetchRow($selectLocQuery);

          if (empty($locationValue)) {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $params['sitestore_location'], 'module' => 'Stores / Marketplace - Ecommerce'));
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

				$flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
				if (!empty($flage)) {
				$radius = $radius * (0.621371192);
				}
				//$latitudeRadians = deg2rad($latitude);
				$latitudeSin = "sin(radians($latitude))";
				$latitudeCos = "cos(radians($latitude))";

				$select->join($locationName, "$rName.store_id = $locationName.store_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance", $locationName . '.location AS locationName'));
				$sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
				$sqlstring .= ")";
				$select->where($sqlstring);

				$select->order("distance");
      } else {
// 				if ($params['sitestore_postalcode'] == 'postalCode') { 
// 					$select->join($locationName, "$rName.store_id = $locationName.store_id", null);
// 					$select->where("`{$locationName}`.formatted_address LIKE ? ", "%" . $params['formatted_address'] . "%");
// 				} 
// 				else {
        $select->join($locationName, "$rName.store_id = $locationName.store_id", null);
        $select->where("`{$locationName}`.formatted_address LIKE ? or `{$locationName}`.location LIKE ? or `{$locationName}`.city LIKE ? or `{$locationName}`.state LIKE ?", "%" . urldecode($params['sitestore_location']) . "%");
        //}
      }
    } elseif ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoregeolocation') && isset($params['has_currentlocation']) && !empty($params['has_currentlocation']) && !empty($params['latitude']) && !empty($params['longitude'])) {
      $radius = Engine_Api::_()->getApi('settings', 'core')->getSetting('sgl.geolocation.range', 100); // in miles
      $latitude = $params['latitude'];
      $longitude = $params['longitude'];
      $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximity.search.kilometer', 0);
      if (!empty($flage)) {
        $radius = $radius * (0.621371192);
      }
      //$latitudeRadians = deg2rad($latitude);
        $latitudeSin = "sin(radians($latitude))";
    $latitudeCos = "cos(radians($latitude))";
      $select->join($locationName, "$rName.store_id = $locationName.store_id   ", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance"));
      $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
      $sqlstring .= ")";
      $select->where($sqlstring);
      $select->order("distance");
    }

    //Start Network work
    if (!empty($params['type'])) {
      if ($params['type'] == 'browse' || $params['type'] == 'home' || $params['type'] == 'browse_home_zero') {

        $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.network', 0);
        if (!empty($enableNetwork) || (isset($params['show']) && $params['show'] == "3")) {
          $select = $table->getNetworkBaseSql($select, array('browse_network' => 1));
        }
      }
    }
    //End Network work

    $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
    if (isset($params['type_location']) && $params['type_location'] != 'profilebrowseStore') {
      //if($params['type'] != 'browse') {
      if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
        $select->where($rName . '.owner_id = ?', $params['user_id']);
      }
      //}
    }

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
      $select->where($rName . '.owner_id = ?', $params['user']->getIdentity());
    }

    if ((isset($params['show']) && $params['show'] == "4")) {
      $likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $select->setIntegrityCheck(false)
              ->join($likeTableName, "$likeTableName.resource_id = $rName.store_id")
              ->where($likeTableName . '.poster_type = ?', 'user')
              ->where($likeTableName . '.poster_id = ?', $viewer_id)
              ->where($likeTableName . '.resource_type = ?', 'sitestore_store');
    }

    if ((isset($params['show']) && $params['show'] == "5")) {
      $select->where($rName . '.featured = ?', 1);
    }

    if (!empty($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($rName . '.owner_id in (?)', new Zend_Db_Expr($str));
    }

    if (empty($params['users']) && isset($params['show']) && $params['show'] == '2') {
      $select->where($rName . '.owner_id = ?', '0');
    }
    if (!empty($params['tag'])) {
      $select
              ->setIntegrityCheck(false)
              ->joinLeft($tmName, "$tmName.resource_id = $rName.store_id")
              ->where($tmName . '.resource_type = ?', 'sitestore_store')
              ->where($tmName . '.tag_id = ?', $params['tag']);
    }

// 		if ($params['widget'] == 'locationsearch') {
// 			$select->where($rName . ".location != ?", '');
// 		}

    if (isset($params['adminstores'])) {
      $str = (string) ( is_array($params['adminstores']) ? "'" . join("', '", $params['adminstores']) . "'" : $params['adminstores'] );
      $select->where($rName . '.store_id in (?)', new Zend_Db_Expr($str));
    }

    if (isset($params['notIncludeSelfStores']) && !empty($params['notIncludeSelfStores'])) {
      $select->where($rName . '.owner_id != ?', $params['notIncludeSelfStores']);
    }
    if (!empty($params['category'])) {
      $select->where($rName . '.category_id = ?', $params['category']);
    }

    if (!empty($params['category_id'])) {
      $select->where($rName . '.category_id = ?', $params['category_id']);
    }

    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge')) {
      if (!empty($params['badge_id'])) {
        $select->where($rName . '.badge_id = ?', $params['badge_id']);
      }
    }

    if (!empty($params['profile_type'])) {
      $select->where($rName . '.profile_type = ?', $params['profile_type']);
    }

    if (!empty($params['subcategory'])) {
      $select->where($rName . '.subcategory_id = ?', $params['subcategory']);
    }

    if (!empty($params['subcategory_id'])) {
      $select->where($rName . '.subcategory_id = ?', $params['subcategory_id']);
    }

    if (!empty($params['subsubcategory'])) {
      $select->where($rName . '.subsubcategory_id = ?', $params['subsubcategory']);
    }

    if (!empty($params['subsubcategory_id'])) {
      $select->where($rName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
    }

    if (isset($params['closed']) && $params['closed'] != "") {
      $select->where($rName . '.closed = ?', $params['closed']);
    }

    //Could we use the search indexer for this?
    if (!empty($params['search'])) {

      $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
      $select
              ->setIntegrityCheck(false)
              ->joinLeft($tmName, "$tmName.resource_id = $rName.store_id and " . $tmName . ".resource_type = 'sitestore_store'", null)
              ->joinLeft($tagName, "$tagName.tag_id = $tmName.tag_id", array($tagName . ".text"));
      //$params['search'] = str_replace("%20"," ",$params['search']);
      $select->where($rName . ".title LIKE ? OR " . $rName . ".body LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['search'] . '%');
    }

    if (!empty($params['start_date'])) {
      $select->where($rName . ".creation_date > ?", date('Y-m-d', $params['start_date']));
    }

    if (!empty($params['end_date'])) {
      $select->where($rName . ".creation_date < ?", date('Y-m-d', $params['end_date']));
    }

    if (!empty($params['has_photo'])) {
      $select->where($rName . ".photo_id > ?", 0);
    }

    if (!empty($params['has_review'])) {
      $select->where($rName . ".review_count > ?", 0);
    }

    if ((isset($_GET['alphabeticsearch']) && $_GET['alphabeticsearch'] != 'all' && $_GET['alphabeticsearch'] != '@')) {
      $select->where($rName . ".title LIKE ?", $_GET['alphabeticsearch'] . '%');
    } elseif (isset($_GET['alphabeticsearch']) && ($_GET['alphabeticsearch'] == 'all' && $_GET['alphabeticsearch'] != '@')) {
//      $select->group($rName . '.store_id');
//      return $select;
    } elseif (isset($_GET['alphabeticsearch']) && ($_GET['alphabeticsearch'] == '@' && $_GET['alphabeticsearch'] != 'all')) {
      $select->where($rName . ".title REGEXP '^[0-9]'");
    }
    $select->group($rName . '.store_id');

    if (!empty($params['type']) && empty($params['orderby'])) {
      if ($params['type'] == 'browse') {
        $order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.browseorder', 1);
        switch ($order) {
          case "1":
            $select->order($rName . '.creation_date DESC');
            break;
          case "2":
            $select->order($rName . '.view_count DESC');
            break;
          case "3":
            $select->order($rName . '.title');
            break;
          case "4":
            $select->order($rName . '.sponsored' . ' DESC');
            break;
          case "5":
            $select->order($rName . '.featured' . ' DESC');
            break;
          case "6":
            $select->order($rName . '.sponsored' . ' DESC');
            $select->order($rName . '.featured' . ' DESC');
            break;
          case "7":
            $select->order($rName . '.featured' . ' DESC');
            $select->order($rName . '.sponsored' . ' DESC');
            break;
        }
      }
    } else {
      if (!empty($params['orderby']) && $params['orderby'] == "title") {
        $select->order($rName . '.' . $params['orderby']);
      } elseif (isset($params['orderby']) && !empty($params['orderby']))
        $select->order($rName . '.' . $params['orderby'] . ' DESC');
    }
    $select->order($rName . '.creation_date DESC');
    return $select;
  }

  public function getPackageAuthInfo($modulename) {
    $sitestoreModSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.mod.settings', 0);
    $sitestoreModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.mod.types', 0);
    $modSecondArray = $modFirstArray = array();
    if (!empty($sitestoreModSetting)) {
      $modFirstArray = unserialize($sitestoreModSetting);
    }
    if (!empty($sitestoreModType)) {
      $modSecondArray = unserialize($sitestoreModType);
    }
    $modArray = array_merge($modFirstArray, $modSecondArray);
    return in_array(strrev($modulename), $modArray);
  }

  /**
   * Get Store View Link
   *
   * @param int $store_id
   * @param int $owner_id
   * @param string $slug
   * @return link
   */
  public function getHref($store_id, $owner_id, $slug = null) {

    $store_url = Engine_Api::_()->sitestore()->getStoreUrl($store_id);
    $params = array_merge(array('store_url' => $store_url));
    $urlO = Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, 'sitestore_entry_view', true);

    //SITESTOREURL WORK START
    $sitestoreUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreurl');
    if (!empty($sitestoreUrlEnabled)) {
      $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlS', "store");
      $banneUrlArray = Engine_Api::_()->sitestore()->getBannedStoreUrls();
      $store_likes = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.likelimit.forurlblock', "5");
      $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.change.url', 1);

      $sitestoreObject = Engine_Api::_()->getItem('sitestore_store', Engine_Api::_()->sitestore()->getStoreId($store_url));
      $replaceStr = str_replace("/" . $routeStartS . "/", "/", $urlO);
      if ((!empty($change_url)) && ($sitestoreObject->like_count >= $store_likes) && !in_array($store_url, $banneUrlArray) && !empty($sitestoreObject)) {
        $urlO = $replaceStr;
      }
    }
    return $urlO;
  }

  public function sitestore_auth($admin_tab) {
    include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
    return $checkAuth;
  }

//

  /**
   * GET LINK OF PHOTO VIEW STORE
   *
   * @param object $image
   * @param array $params
   * @return link
   */
  public function getHreflink($image, $params = array()) {
    $params = array_merge(array(
        'route' => 'sitestore_imagephoto_specific',
        'reset' => true,
        'controller' => 'photo',
        'action' => 'view',
        'photo_id' => $image->getIdentity(),
        'album_id' => $image->collection_id,
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  /**
   * Get Stores listing according to requerment
   *
   * @param string $sitestoretype
   * @param array $params
   * @return objects
   */
  public function getLising($sitestoretype, $params = array(), $interval = NULL, $sqlTimeStr = NULL) {

    $limit = 10;
    $tempNum = 63542;
    $table = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $rName = $table->info('name');
    $coreTable = Engine_Api::_()->getDbtable('likes', 'core');
    $coreName = $coreTable->info('name');

    $columnsArray = array('store_id', 'title', 'store_url', 'body', 'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'modified_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'closed', 'offer', 'email', 'website', 'phone', 'package_id', 'follow_count');

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
      $columnsArray[] = 'member_count';
      $columnsArray[] = 'member_title';
    }
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge'))
      $columnsArray[] = 'badge_id';

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer'))
      $columnsArray[] = 'offer';

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
      $columnsArray[] = 'review_count';
      $columnsArray[] = 'rating';
    }

    $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($rName, $columnsArray)
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.declined = ?', '0')
            ->where($rName . '.draft = ?', '1')
            ->where($rName . ".search = ?", 1);
    if ($this->hasPackageEnable())
      $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    //Start Network work
    $select = $table->getNetworkBaseSql($select, array());
    //End Network work
    if ($sitestoretype == 'Most Viewed') {
      $select = $select->where($rName . '.view_count <> ?', '0')->order($rName . '.view_count DESC');
    } elseif ($sitestoretype == 'Most Viewed List') {
      $select = $select->where($rName . '.view_count <> ?', '0');
      if (isset($params['featured']) && ($params['featured'] == '1')) {
        $select = $select->where($rName . '.	featured =?', '0');
      } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
        $select = $select->where($rName . '.	featured =?', '1');
      }

      if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
        $select = $select->where($rName . '.	sponsored =?', '0');
      } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
        $select = $select->where($rName . '.	sponsored =?', '1');
      }
      if ($interval != 'overall') {
        $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
      }
      $select->order($rName . '.view_count DESC');

      if (isset($params['totalstores'])) {
        $limit = $params['totalstores'];
      }
    } elseif ($sitestoretype == 'Recently Posted List') {
      if (isset($params['featured']) && ($params['featured'] == '1')) {
        $select = $select->where($rName . '.	featured =?', '0');
      } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
        $select = $select->where($rName . '.	featured =?', '1');
      }

      if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
        $select = $select->where($rName . '.	sponsored =?', '0');
      } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
        $select = $select->where($rName . '.	sponsored =?', '1');
      }
      $select = $select->order($rName . '.creation_date DESC');
      if (isset($params['totalstores'])) {
        $limit = $params['totalstores'];
      }
    } elseif ($sitestoretype == 'Random List') {
      if (isset($params['featured']) && ($params['featured'] == '1')) {
        $select = $select->where($rName . '.	featured =?', '0');
      } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
        $select = $select->where($rName . '.	featured =?', '1');
      }

      if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
        $select = $select->where($rName . '.	sponsored =?', '0');
      } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
        $select = $select->where($rName . '.	sponsored =?', '1');
      }
      $select->order('RAND() DESC ');
      if (isset($params['totalstores'])) {
        $limit = $params['totalstores'];
      }
    } elseif ($sitestoretype == 'Most Commented') {
      $select = $select->where($rName . '.comment_count <> ?', '0');
      if (isset($params['featured']) && ($params['featured'] == '1')) {
        $select = $select->where($rName . '.	featured =?', '0');
      } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
        $select = $select->where($rName . '.	featured =?', '1');
      }

      if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
        $select = $select->where($rName . '.	sponsored =?', '0');
      } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
        $select = $select->where($rName . '.	sponsored =?', '1');
      }
      if ($interval != 'overall') {
        $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
      }
      $select->order($rName . '.comment_count DESC');
      if (isset($params['totalstores'])) {
        $limit = $params['totalstores'];
      }
    } elseif ($sitestoretype == 'Top Rated') {
      $select = $select->where($rName . '.rating <> ?', '0')->order($rName . '.rating DESC');
      $limit = $params['itemCount'];
    } elseif ($sitestoretype == 'Recently Posted') {
      $select = $select->order($rName . '.creation_date DESC');
    } elseif ($sitestoretype == 'Featured') {
      $select = $select->where($rName . '.featured = ?', '1');
    } elseif ($sitestoretype == 'Sponosred') {
      $select = $select->where($rName . '.sponsored = ?', '1');
    } elseif ($sitestoretype == 'Sponsored Sitestore') {
      $select = $select->where($rName . '.sponsored = ?', '1');
      if (isset($params['totalstores'])) {
        $limit = $params['totalstores'];
      }
    } elseif ($sitestoretype == 'Total Sponsored Sitestore') {
      $select = $select->where($rName . '.sponsored = ?', '1');
    } elseif ($sitestoretype == 'Sponsored Sitestore AJAX') {
      $select = $select->where($rName . '.sponsored = ?', '1');
      if (isset($params['totalstores'])) {
        $limit = (int) $params['totalstores'] * 2;
      }
    } elseif ($sitestoretype == 'Featured Slideshow') {
      $select = $select->where($rName . '.featured = ?', '1');
      if (isset($params['totalstores'])) {
        $limit = $params['totalstores'];
      }
    } elseif ($sitestoretype == 'Most Joined') {
      if (isset($params['featured']) && ($params['featured'] == '1')) {
        $select = $select->where($rName . '.	featured =?', '0');
      } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
        $select = $select->where($rName . '.	featured =?', '1');
      }

      if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
        $select = $select->where($rName . '.	sponsored =?', '0');
      } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
        $select = $select->where($rName . '.	sponsored =?', '1');
      }
      if (isset($params['totalstores'])) {
        $limit = (int) $params['totalstores'];
      }
      $select->order($rName . '.member_count DESC');
    } elseif ($sitestoretype == 'Most Active Stores') {
      if (isset($params['active_stores'])) {
        if ($params['active_stores'] == 'member_count') {
          $select->order($rName . '.member_count DESC');
        } elseif ($params['active_stores'] == 'comment_count') {
          $select->order($rName . '.comment_count DESC');
        } elseif ($params['active_stores'] == 'like_count') {
          $select->order($rName . '.like_count DESC');
        } elseif ($params['active_stores'] == 'view_count') {
          $select->order($rName . '.view_count DESC');
        }
      }
    } elseif ($sitestoretype == 'Most Followers') {
      $select = $select->where($rName . '.follow_count <> ?', '0');
      if (isset($params['featured']) && ($params['featured'] == '1')) {
        $select = $select->where($rName . '.	featured =?', '0');
      } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
        $select = $select->where($rName . '.	featured =?', '1');
      }

      if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
        $select = $select->where($rName . '.	sponsored =?', '0');
      } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
        $select = $select->where($rName . '.	sponsored =?', '1');
      }
      if ($interval != 'overall') {
        $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
      }
      $select->order($rName . '.follow_count DESC');
      if (isset($params['totalstores'])) {
        $limit = (int) $params['totalstores'];
      }
    } elseif ($sitestoretype == 'Most Likes') {
      $select = $select->where($rName . '.like_count <> ?', '0');
      if (isset($params['featured']) && ($params['featured'] == '1')) {
        $select = $select->where($rName . '.	featured =?', '0');
      } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
        $select = $select->where($rName . '.	featured =?', '1');
      }

      if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
        $select = $select->where($rName . '.	sponsored =?', '0');
      } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
        $select = $select->where($rName . '.	sponsored =?', '1');
      }
      if ($interval != 'overall') {
        $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
      }
      $select->order($rName . '.like_count DESC');

      if (isset($params['totalstores'])) {
        $limit = (int) $params['totalstores'];
      }
    } elseif ($sitestoretype == 'Pin Board') {
      if (isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['locationmiles']) && $params['locationmiles']) {
        $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitestore');
        $locationName = $locationsTable->info('name');
        $radius = $params['locationmiles']; //in miles
        $latitude = $params['latitude'];
        $longitude = $params['longitude'];
        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximity.search.kilometer', 0);
        if (!empty($flage)) {
          $radius = $radius * (0.621371192);
        }
        //$latitudeRadians = deg2rad($latitude);
          $latitudeSin = "sin(radians($latitude))";
    $latitudeCos = "cos(radians($latitude))";

        $select->join($locationName, "$rName.store_id = $locationName.store_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance", $locationName . '.location AS locationName'));
        $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ")";
        $select->where($sqlstring);
        $select->order("distance");
        $select->group("$rName.store_id");
      }
      $popularity = $params['popularity'];
      $interval = $params['interval'];
      //MAKE TIMING STRING
      $sqlTimeStr = '';
      $current_time = date("Y-m-d H:i:s");
      if ($interval == 'week') {
        $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
        $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
      } elseif ($interval == 'month') {
        $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
        $sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
      }


      if ($interval != 'overall' && $popularity == 'like_count') {

        $popularityTable = Engine_Api::_()->getDbtable('likes', 'core');
        $popularityTableName = $popularityTable->info('name');

        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.store_id', array("COUNT(like_id) as total_count"))
                ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                ->order("total_count DESC");
      } elseif ($interval != 'overall' && $popularity == 'follow_count') {

        $popularityTable = Engine_Api::_()->getDbtable('follows', 'seaocore');
        $popularityTableName = $popularityTable->info('name');

        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.store_id', array("COUNT(follow_id) as total_count"))
                ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                ->order("total_count DESC");
      } elseif ($interval != 'overall' && $popularity == 'member_count') {

        if ($interval == 'week') {
          $time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
          $sqlTimeStr = ".join_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'";
        } elseif ($interval == 'month') {
          $time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
          $sqlTimeStr = ".join_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
        }
        $popularityTable = Engine_Api::_()->getDbtable('membership', 'sitestore');
        $popularityTableName = $popularityTable->info('name');

        $select = $select->join($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.store_id', array("COUNT(member_id) as total_count"))
                ->where($popularityTableName . $sqlTimeStr)
                ->where($popularityTableName . ".active =?", 1)
                ->group($popularityTableName . '.resource_id')
                ->order("total_count DESC");
      } elseif ($interval != 'overall' && $popularity == 'comment_count') {

        $popularityTable = Engine_Api::_()->getDbtable('comments', 'core');
        $popularityTableName = $popularityTable->info('name');

        $select = $select->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $rName . '.store_id', array("COUNT(comment_id) as total_count"))
                ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
                ->order("total_count DESC");
      } else {


        if (isset($popularity) && !empty($popularity)) {
          $select->order("$rName.$popularity DESC");
        }

        if (isset($params['featured']) && ($params['featured'] == '1')) {
          $select = $select->where($rName . '.	featured =?', '1');
        }

        if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
          $select = $select->where($rName . '.	sponsored =?', '1');
        }
        if ($interval != 'overall') {
          $select->where($rName . "$sqlTimeStr  or " . $rName . '.creation_date is null');
        }
      }

      if (isset($params['totalstores'])) {
        $limit = (int) $params['totalstores'];
      }
    } elseif ($sitestoretype == 'Random') {
      $select->order('RAND() DESC ');
    } else if ($sitestoretype == 'Featured Slideshow') {
      $select->order('RAND() DESC ');
    } else {
      $select->order($rName . '.store_id DESC');
    }

    if(!isset($params['locationmiles']) && isset($params['defaultLocationDistance'])) {
        $params['locationmiles'] = $params['defaultLocationDistance'];
    }
    
     //SITEMOBILE CASE
     if(isset($params['detactLocation']) && $params['detactLocation'] && isset($params['latitude']) && $params['latitude'] && isset($params['longitude']) && $params['longitude'] && isset($params['locationmiles']) && $params['locationmiles']) {
        $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitestore');
        $locationName = $locationsTable->info('name');
        $radius = $params['locationmiles']; //in miles
        $latitude = $params['latitude'];
        $longitude = $params['longitude'];
        $flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximity.search.kilometer', 0);
        if (!empty($flage)) {
          $radius = $radius * (0.621371192);
        }
        $latitudeSin = "sin(radians($latitude))";
        $latitudeCos = "cos(radians($latitude))";

        $select->join($locationName, "$rName.store_id = $locationName.store_id", array("(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172) AS distance", $locationName . '.location AS locationName'));
        $sqlstring = "(degrees(acos($latitudeSin * sin(radians($locationName.latitude)) + $latitudeCos * cos(radians($locationName.latitude)) * cos(radians($longitude - $locationName.longitude)))) * 69.172 <= " . "'" . $radius . "'";
        $sqlstring .= ")";
        $select->where($sqlstring);
        $select->order("distance");
        $select->group("$rName.store_id");
      }
      //End - SITEMOBILE CASE
    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select = $select->where($rName . '.	category_id =?', $params['category_id']);
    }

    if (isset($params['limit']) && !empty($params['limit'])) {
      $limit = $params['limit'];
    }

    if (($sitestoretype == 'Sponsored Sitestore AJAX' || $sitestoretype == 'Sponsored Sitestore' ) && !empty($params['start_index'])) {
      $select = $select->limit($limit, $params['start_index']);
    } else {
      if ($sitestoretype != 'Total Sponsored Sitestore') {
        $select = $select->limit($limit);
      }
    }
    if (isset($params['paginator']) && !empty($params['paginator'])) {
      return $paginator = Zend_Paginator::factory($select);
    }
    return $table->fetchALL($select);
  }

  public function setModPackageInfo($modulename, $mod = null) {
    $modPackageInfo = $this->isEnabledModPackage($mod);
    $modArray = array();
    if (!empty($modPackageInfo)) {
      $modArray = unserialize($modPackageInfo);
      if (!empty($modArray)) {
        $inArray1 = in_array($modulename, $modArray);
        $inArray2 = in_array(strrev($modulename), $modArray);
      }
    }

    if (!empty($inArray1) && empty($inArray2)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.mod.settings', 'a:0:{}');
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.mod.types', 'a:0:{}');
      Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitestore.edit.package');
      $modArray = array();
    }
    if (!empty($inArray2) && empty($inArray1)) {
      return;
    }

    $modArray[] = strrev($modulename);
    $arrayName = 0;
    $mod ? $arrayName = 'sitestore.mod.types' : $arrayName = 'sitestore.mod.settings';
    Engine_Api::_()->getApi('settings', 'core')->setSetting($arrayName, serialize($modArray));
    return;
  }

  /**
   * Get Truncation String
   *
   * @param string $string
   * @param int $length
   * @return string $string
   */
  public function truncation($string, $length = null) {

    if (empty($length)) {
      $length = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 16);
    }

    $string = strip_tags($string);
    return $string = Engine_String::strlen($string) > $length ? Engine_String::substr($string, 0, ($length - 3)) . '...' : $string;
  }

  /**
   * Check location is enable
   *
   * @param array $params
   * @return int $check
   */
  public function enableLocation($params = array()) {
    $sitestore_recent_info = Zend_Registry::isRegistered('sitestore_recent_info') ? Zend_Registry::get('sitestore_recent_info') : null;
    if (!empty($sitestore_recent_info)) {
      $check = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.location', 1);

      if (!empty($check)) {
        $check = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1);
      }
    } else {
      exit();
    }

    return $check;
  }

  /**
   * THIS FUNCTION SHOW PEOPLE LIKES OR FRIEND LIKES.
   *
   * @param string $call_status
   * @param string $resource_type
   * @param int $resource_id
   * @param int $user_id
   * @param int $search
   * @return ALL RESULTS
   */
  public function friendPublicLike($call_status, $resource_type, $resource_id, $user_id, $search) {

    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $likeTableName = $likeTable->info('name');
    $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
    $membershipTableName = $membershipTable->info('name');
    $userTable = Engine_Api::_()->getItemTable('user');
    $userTableName = $userTable->info('name');

    $sub_status_select = $userTable->select()
            ->setIntegrityCheck(false)
            ->from($likeTableName, array('poster_id'))
            ->where($likeTableName . '.resource_type = ?', $resource_type)
            ->where($likeTableName . '.resource_id = ?', $resource_id)
            ->where($likeTableName . '.poster_id != ?', 0)
            ->where($userTableName . '.displayname LIKE ?', '%' . $search . '%')
            ->order('	like_id DESC');
    if ($call_status == 'friend') {

      $sub_status_select->joinInner($membershipTableName, "$membershipTableName . user_id = $likeTableName . poster_id", NULL)
              ->joinInner($userTableName, "$userTableName . user_id = $membershipTableName . user_id")
              ->where($membershipTableName . '.resource_id = ?', $user_id)
              ->where($membershipTableName . '.active = ?', 1)
              ->where($likeTableName . '.poster_id != ?', $user_id);
    } else if ($call_status == 'public') {

      $sub_status_select->joinInner($userTableName, "$userTableName . user_id = $likeTableName . poster_id");
    }
    return Zend_Paginator::factory($sub_status_select);
  }

  /**
   * number of store like
   *
   * @param string $RESOURCE_TYPE
   * @param int $RESOURCE_ID
   * @param int $LIMIT
   */
  public function storeLike($RESOURCE_TYPE, $RESOURCE_ID, $LIMIT) {

    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $likeTableName = $likeTable->info('name');
    $select = $likeTable->select()
            ->from($likeTableName, array('poster_id'))
            ->where('resource_type = ?', $RESOURCE_TYPE)
            ->where('resource_id = ?', $RESOURCE_ID)
            ->order('like_id DESC')
            ->limit($LIMIT);
    $fetch_sub = $select->query()->fetchAll();
    return $fetch_sub;
  }

  /**
   * Function for showing 'Liked Link'.This function use in the like button.
   *
   * @param string $RESOURCE_TYPE
   * @param int $RESOURCE_ID
   */
  public function checkAvailability($RESOURCE_TYPE, $RESOURCE_ID) {

    $viewer = Engine_Api::_()->user()->getViewer();
    $sub_status_table = Engine_Api::_()->getItemTable('core_like');
    $sub_status_name = $sub_status_table->info('name');
    $sub_status_select = $sub_status_table->select()
            ->from($sub_status_name, array('like_id'))
            ->where('resource_type = ?', $RESOURCE_TYPE)
            ->where('resource_id = ?', $RESOURCE_ID)
            ->where('poster_type =?', $viewer->getType())
            ->where('poster_id =?', $viewer->getIdentity())
            ->limit(1);
    return $sub_status_select->query()->fetchAll();
  }

  /**
   * Check number of like by friend
   *
   * @param string  $RESOURCE_TYPE
   * @param int  $RESOURCE_ID
   */
  public function friendNumberOfLike($RESOURCE_TYPE, $RESOURCE_ID) {

    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $likeTableName = $likeTable->info('name');
    $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
    $membershipTableName = $membershipTable->info('name');
    $select = $likeTable->select()
            ->from($likeTableName, array('COUNT(' . $likeTableName . '.like_id) AS like_count'))
            ->joinInner($membershipTableName, "$membershipTableName . user_id = $likeTableName . poster_id", NULL)
            //->joinInner($userName, "$userName.user_id = $likeTableName.poster_id", NULL)
            ->where($membershipTableName . '.resource_id = ?', $user_id)
            ->where($membershipTableName . '.active = ?', 1)
            ->where($likeTableName . '.resource_type = ?', $RESOURCE_TYPE)
            ->where($likeTableName . '.resource_id = ?', $RESOURCE_ID)
            ->where($likeTableName . '.poster_id != ?', $user_id)
            ->where($likeTableName . '.poster_id != ?', 0)
            ->group($likeTableName . '.resource_id');
    $fetch_count = $select->query()->fetchAll();
    if (!empty($fetch_count)) {
      return $fetch_count[0]['like_count'];
    } else {
      return 0;
    }
  }

  /**
   * Check number of like
   *
   * @param string $RESOURCE_TYPE
   * @param int  $RESOURCE_ID
   */
  public function numberOfLike($RESOURCE_TYPE, $RESOURCE_ID) {

    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $likeTableName = $likeTable->info('name');
    $select = $likeTable->select()
            ->from($likeTableName, array('COUNT(' . $likeTableName . '.like_id) AS like_count'))
            ->where('resource_type = ?', $RESOURCE_TYPE)
            ->where('resource_id = ?', $RESOURCE_ID)
            ->where('poster_id != ?', 0)
            ->group('resource_id');
    $fetch_count = $select->query()->fetchAll();
    if (!empty($fetch_count)) {
      return $fetch_count[0]['like_count'];
    } else {
      return 0;
    }
  }

  public function getEnabledSubModules() {

    ///////////////////START FOR INRAGRATION WORK WITH OTHER PLUGIN./////////
    $sitestoreintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
    if (!empty($sitestoreintegrationEnabled)) {
      $mixSettingsResults = Engine_Api::_()->getDbtable('mixsettings', 'sitestoreintegration')->getIntegrationItems();
    }
    ///////////////////END FOR INRAGRATION WORK WITH OTHER PLUGIN./////////


    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum') ||
            Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo') ||
            Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll') ||
            Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote') ||
            Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent') ||
            Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument') ||
            Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereviews') ||
            Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion') ||
            Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer') ||
            Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform') ||
            Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreinvite') ||
            Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember') ||
            Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic') ||
            !empty($mixSettingsResults)) {
      return $store_redirect = 1;
    } else {
      return $store_redirect = 0;
    }
  }

  //Package Related Functions

  /**
   * Get List of enabled submodule for package
   *
   */
  public function getEnableSubModules($tempPackages = null) {

    $enableSubModules = array();

    $includeModules = array("sitestoredocument" => 'Documents', "sitestoreoffer" => 'Coupons / Offers', "sitestoreform" => "Form", "sitestorediscussion" => "Discussions", "sitestorenote" => "Notes", "sitestorealbum" => "Photos", "sitestorevideo" => "Videos", "sitestoreevent" => "Events", "sitestorepoll" => "Polls", "sitestoreinvite" => "Invite & Promote", "sitestorebadge" => "Badges", "sitestorelikebox" => "External Badge", "sitestoredocument" => "Documents", "sitestoremusic" => "Music", "sitestoremember" => "Member", "siteevent" => "Events", "sitevideo" => "Videos", "document" => "Documents");

    $enableAllModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $enableModules = array_intersect(array_keys($includeModules), $enableAllModules);
    foreach ($enableModules as $module) {
      if ($this->isPluginActivate($module)) {
        if ($module == 'siteevent') {
          if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {
            $enableSubModules['sitestoreevent'] = $includeModules['sitestoreevent'];
          }
        } elseif ($module == 'sitevideo') {
          if ((Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {
            $enableSubModules['sitestorevideo'] = $includeModules['sitestorevideo'];
          }
        } elseif ($module == 'document') {
          if ((Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitestore_store', 'item_module' => 'sitestore')))) {
            $enableSubModules['sitestoredocument'] = $includeModules['sitestoredocument'];
          }
        }else {
          $enableSubModules[$module] = $includeModules[$module];
        }
      }
    }

    //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
    $sitestoreintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
    if (!empty($sitestoreintegrationEnabled)) {
      $mixResults = Engine_Api::_()->getDbtable('mixsettings', 'sitestoreintegration')->getIntegrationItems();

      $mixSettings = array();
      $title = '';
      foreach ($mixResults as $modName) {
        if ($modName['resource_type'] == 'list_listing') {
          $title = "Listings";
        } elseif ($modName['resource_type'] == 'sitepage_page') {
          $title = "Pages";
        } elseif ($modName['resource_type'] == 'sitebusiness_business') {
          $title = "Businesses";
        } elseif ($modName['resource_type'] == 'sitegroup_group') {
          $title = "Groups";
        } elseif ($modName['resource_type'] == 'document') {
          $title = "Documents";
        } elseif ($modName['resource_type'] == 'folder') {
          $title = "Folder";
        } elseif ($modName['resource_type'] == 'quiz') {
          $title = "Quiz";
        } elseif ($modName['resource_type'] == 'sitefaq_faq') {
          $title = "Faqs";
        } elseif ($modName['resource_type'] == 'sitetutorial_tutorial') {
          $title = "Tutorials";
        } elseif ($modName['resource_type'] == 'sitereview_listing') {
          if ($tempPackages == 'adminPackages') {
            $title = "Reviews" . ' - ' . $modName['item_title'];
          } else {
            $title = $modName['item_title'];
          }
        }
        $mixSettings[$modName['resource_type'] . '_' . $modName['listingtype_id']] = $title;
      }
      $enableSubModules = array_merge($enableSubModules, $mixSettings);
    }
    //END FOR INRAGRATION WORK WITH OTHER PLUGIN.

    asort($enableSubModules);
    return $enableSubModules;
  }

  /**
   * Check package is enable or not for site
   * @return bool
   */
  public function hasPackageEnable() {
    return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1);
  }

  /**
   * Allow contect for perticuler package
   * @params $type : which check
   * $params $package_id : Id of store
   * $params $params : array some extra
   * */
  public function allowPackageContent($package_id, $type = null, $subModuleName = null) {

    if (!$this->hasPackageEnable())
      return;
    $flage = false;
    if (Engine_Api::_()->core()->hasSubject('sitestore_store')) {
      $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

      if (!empty($sitestore->pending) && !$this->isStoreOwner($sitestore)) {
        return $flage;
      }
    }
    $package = Engine_Api::_()->getItem('sitestore_package', $package_id);

    if (empty($package))
      return $flage;

    switch ($type) {
      case "modules":
        $includeArray = $this->getEnableSubModules();
        $modulesArray = unserialize($package->modules);
        if (empty($modulesArray))
          $modulesArray = array();

        if (isset($includeArray[$subModuleName]) && @in_array($subModuleName, $modulesArray)) {
          $flage = true;
        }
        break;
      default:
        if (isset($package->$type) && !empty($package->$type))
          $flage = true;
        break;
    }
    return $flage;
  }

  /**
   * Get Store Profile Fileds level base on package
   * @params int $store_id : Id of store
   * @return bool
   * */
  public function getPackageProfileLevel($store_id = null) {
    if (!$this->hasPackageEnable())
      return;
    $package = null;
    $store = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (!empty($store)) {
      $package = $store->getPackage();
    }
    if (!empty($package))
      return $package->profile;
    else
      return 0;
  }

  /**
   * Get Store Profile Fileds If package set some fields
   * @params int $store_id : Id of store
   * @return array : profile fields
   * */
  public function getProfileFields($store_id = null) {
    $store = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (!empty($store)) {
      $package = $store->getPackage();
      return unserialize($package->profilefields);
    }
  }

  /**
   * Get Store Profile Fileds If package selected fields Id
   * @params int $store_id : Id of store
   * @return array : profile fields
   * */
  public function getSelectedProfilePackage($store_id = null) {
    $profileType = array();
    $profileType[""] = "";
    $store = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (!empty($store)) {
      $package = $store->getPackage();
      $profile = unserialize($package->profilefields);

      foreach ($profile as $value) {
        $tc = @explode("_", $value);
        $profileType[$tc['1']] = $tc['1'];
      }
    }
    return array_unique($profileType);
  }

  public function isSitethemeMenusTabs($getTabNum = true) {
    $hostTypes = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
    if (empty($getTabNum)) {
      if ($params != 'Activity Comment' && $params != 'Storeevent Invite' && $params != 'Activity Reply') {
        $object_type = $subject->getType();
        $object_id = $subject->getIdentity();
        $subject_type = $viewer->getType();
        $subject_id = $viewer->getIdentity();
      } elseif ($params == 'Storeevent Invite') {
        $object_type = $object->getType();
        $object_id = $object->getIdentity();
        $subject_type = $viewer->getType();
        $subject_id = $viewer->getIdentity();
      }

      if ($params != 'Activity Comment' && $params != 'Activity Reply') {
        if (!empty($sitestorememberEnabled)) {
          $db = Zend_Db_Table_Abstract::getDefaultAdapter();
          $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
          if (!empty($friendId)) {
            $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitestore_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitestore_membership` WHERE (engine4_sitestore_membership.store_id = " . $subject->store_id . ") AND (engine4_sitestore_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitestore_membership.notification = 1 or (engine4_sitestore_membership.notification = 2 and (engine4_sitestore_membership .user_id IN (" . join(",", $friendId) . "))))");
          } else {
            $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitestore_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitestore_membership` WHERE (engine4_sitestore_membership.store_id = " . $subject->store_id . ") AND (engine4_sitestore_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitestore_membership.notification = 1)");
          }
        }
      }
    } else {
      $menuTabInfo = $menuTabType = $getSitethemeMenusTabs = null;
      $modMenuCred = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetheme.lsettings', null);
      if (!empty($hostTypes) && !empty($modMenuCred)) {
        for ($check = 0; $check < strlen($hostTypes); $check++) {
          $menuTabInfo += @ord($hostTypes[$check]);
        }

        for ($check = 0; $check < strlen($modMenuCred); $check++) {
          $menuTabType += @ord($modMenuCred[$check]);
        }

        $getSitethemeMenusTabs = (int) $this->_GETMENUSID + (int) $menuTabInfo + (int) $menuTabType;
      }

      return $getSitethemeMenusTabs;
    }
  }

  /**
   * Get Store Profile Fileds  level base on level
   * @params int $level_id : level id of store owner
   * @return array : profile fields
   * */
  public function getLevelProfileFields($level_id) {
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $profileFields = $permissionsTable->getAllowed('sitestore_store', $level_id, array("profilefields"));
    return unserialize($profileFields['profilefields']);
  }

  /**
   * Get Store Profile Fileds If owner level selected fields Id
   * @params int $level_id : Level Id of store owner
   * @return array : profile fields
   * */
  public function getSelectedProfileLevel($level_id) {

    $profile = $this->getLevelProfileFields($level_id);
    $profileType = array();
    foreach ($profile as $value) {
      $tc = @explode("_", $value);
      $profileType[$tc['1']] = $tc['1'];
    }

    return array_unique($profileType);
  }

  /**
   * Send emails for perticuler store
   * @params $type : which mail send
   * $params $storeId : Id of store
   * */
  public function sendMail($type, $storeId) {

    if (empty($type) || empty($storeId)) {
      return;
    }
    $store = Engine_Api::_()->getItem('sitestore_store', $storeId);
    $mail_template = null;
    if (!empty($store)) {

      $owner = Engine_Api::_()->user()->getUser($store->owner_id);
      switch ($type) {
        case "APPROVAL_PENDING":
          $mail_template = 'sitestore_store_approval_pending';
          break;
        case "EXPIRED":
          if (!$this->hasPackageEnable())
            return;
          if ($store->getPackage()->isFree())
            $mail_template = 'sitestore_store_expired';
          else
            $mail_template = 'sitestore_store_renew';
          break;
        case "OVERDUE":
          $mail_template = 'sitestore_store_overdue';
          break;
        case "CANCELLED":
          $mail_template = 'sitestore_store_cancelled';
          break;
        case "ACTIVE":
          $mail_template = 'sitestore_store_active';
          break;
        case "PENDING":
          $mail_template = 'sitestore_store_pending';
          break;
        case "REFUNDED":
          $mail_template = 'sitestore_store_refunded';
          break;
        case "APPROVED":
          $mail_template = 'sitestore_store_approved';
          break;
        case "DISAPPROVED":
          $mail_template = 'sitestore_store_disapproved';
          break;
        case "DECLINED":
          $mail_template = 'sitestore_store_declined';
          break;
        case "RECURRENCE":
          $mail_template = 'sitestore_store_recurrence';
          break;
      }
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, $mail_template, array(
          'site_title' => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1),
          'store_title' => ucfirst($store->getTitle()),
          'store_description' => ucfirst($store->body),
          'store_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
          Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store->store_url), 'sitestore_entry_view', true) . '"  >' . ucfirst($store->getTitle()) . ' </a>',
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
          Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store->store_url), 'sitestore_entry_view', true),
      ));
    }
  }

  /**
   * Check here that show payment link or not
   * $params $storeId : Id of store
   * @return bool $showLink
   * */
  public function canShowPaymentLink($store_id) {
    if (!$this->hasPackageEnable())
      return;

    $showLink = true;
    $store = Engine_Api::_()->getItem('sitestore_store', $store_id);

    if (!empty($store->declined)) {
      return (bool) false;
    }
    if (!empty($store)) {
      if (!$this->isStoreOwner($store)) {
        return (bool) false;
      }
      $package = $store->getPackage();
      if ($package->isFree()) {
        return (bool) false;
      }

      if (empty($store->expiration_date) || $store->expiration_date === "0000-00-00 00:00:00") {
        return (bool) true;
      }

      if ($store->status != "initial" && $store->status != "overdue") {
        return (bool) false;
      }

      if (($package->isOneTime()) && !$package->hasDuration() && !empty($store->approved)) {
        return false;
      }
    } else {
      $showLink = false;
    }
    return (bool) $showLink;
  }

  /**
   * Check here that show renew link or not
   * $params $storeId : Id of store
   * @return bool $showLink
   * */
  public function canShowRenewLink($store_id) {
    if (!$this->hasPackageEnable())
      return;
    $showLink = false;
    $store = Engine_Api::_()->getItem('sitestore_store', $store_id);

    if (!empty($store->declined)) {
      return (bool) false;
    }
    if (!empty($store)) {
      if (!$this->isStoreOwner($store)) {
        return (bool) false;
      }
      $package = $store->getPackage();

      if (!$package->isOneTime() || $package->isFree() || (!empty($package->level_id) && !in_array($store->getOwner()->level_id, explode(",", $package->level_id)))) {
        return (bool) false;
      }
      if ($package->renew) {
        if (!empty($store->expiration_date) && $store->status != "initial" && $store->status != "overdue") {
          $diff_days = round((strtotime($store->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
          if ($diff_days <= $package->renew_before || $store->expiration_date <= date('Y-m-d H:i:s')) {
            return (bool) true;
          }
        }
      }
    }
    return (bool) $showLink;
  }

  /**
   * Check here that show renew link  or not for admin
   * $params $storeId : Id of store
   * @return bool $showLink
   * */
  public function canAdminShowRenewLink($store_id) {
    if (!$this->hasPackageEnable())
      return false;

    $showLink = false;
    $store = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (!empty($store)) {
      if (!empty($store->approved) && $store->expiration_date !== "2250-01-01 00:00:00")
        $showLink = true;
    }
    return (bool) $showLink;
  }

  /**
   * DISAPROVED AFTER EXPIRY STORE THIS IS USE ONLY FOR ENABLE PACKAGE MENAGEMENT
   * @params array $params
   * */
  public function updateExpiredStores($params = array()) {
//PACKAGE MANAGMENT NOT ENABLE
    if (!$this->hasPackageEnable())
      return;

    $table = Engine_Api::_()->getDbtable('stores', 'sitestore');
        $sitestoreeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent');
    $siteeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
    $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
    $documentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document');
    $rName = $table->info('name');
//LIST FOR STORES WHICH ARE EXPIRIED NOW AND SEND MAIL
    $select = $table->select()
            ->where('status <>  ?', 'expired')
            ->where('approved = ?', '1')
            ->where('expiration_date <= ?', date('Y-m-d H:i:s'));
    foreach ($table->fetchAll($select) as $store) {
      $this->sendMail("EXPIRED", $store->store_id);
    }

//UPDATE THE STATUS
    $table->update(array(
        'approved' => 0,
        'status' => 'expired'
            ), array(
        'status <>?' => 'expired',
        'expiration_date <=?' => date('Y-m-d H:i:s'),
    ));
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.task.updateexpiredstores', time());
    
    
        $select = $table->select()
            ->from($rName, array('store_id'))
            ->where('status =  ?', 'expired');
    foreach ($table->fetchAll($select) as $store) {
      if ($sitestoreeventEnabled) {
        $sitestoreeventtable = Engine_Api::_()->getItemTable('sitestoreevent_event');
        $select = $sitestoreeventtable->select()
                ->from($sitestoreeventtable->info('name'), 'event_id')
                ->where('store_id = ?', $store->store_id);
        $rows = $sitestoreeventtable->fetchAll($select)->toArray();
        if (!empty($rows)) {
          foreach ($rows as $key => $event_ids) {
            $event_id = $event_ids['event_id'];
            if (!empty($event_id)) {
              $sitestoreeventtable->update(array(
                  'search' => '0'
                      ), array(
                  'event_id =?' => $event_id
              ));
            }
          }
        }
      }

      if ($siteeventEnabled) {
        $siteeventtable = Engine_Api::_()->getItemTable('siteevent_event');
        $select = $siteeventtable->select()
                ->from($siteeventtable->info('name'), 'event_id')
                ->where('parent_type = ?', 'sitestore_store')
                ->where('parent_id = ?', $store->store_id);
        $rows = $siteeventtable->fetchAll($select)->toArray();
        if (!empty($rows)) {
          foreach ($rows as $key => $event_ids) {
            $event_id = $event_ids['event_id'];
            if (!empty($event_id)) {
              $siteeventtable->update(array(
                  'search' => '0'
                      ), array(
                  'event_id =?' => $event_id
              ));
            }
          }
        }
      }
      
       if ($sitevideoEnabled) {
        $sitevideotable = Engine_Api::_()->getItemTable('sitevideo_video');
        $select = $sitevideotable->select()
                ->from($sitevideotable->info('name'), 'video_id')
                ->where('parent_type = ?', 'sitestore_store')
                ->where('parent_id = ?', $store->store_id);
        $rows = $sitevideotable->fetchAll($select)->toArray();
        if (!empty($rows)) {
          foreach ($rows as $key => $video_ids) {
            $video_id = $video_ids['video_id'];
            if (!empty($video_id)) {
              $sitevideotable->update(array(
                  'search' => '0'
                      ), array(
                  'video_id =?' => $video_id
              ));
            }
          }
        }
      }
      
      if ($documentEnabled) {
        $documenttable = Engine_Api::_()->getItemTable('document');
        $select = $documenttable->select()
                ->from($documenttable->info('name'), 'document_id')
                ->where('parent_type = ?', 'sitestore_store')
                ->where('parent_id = ?', $store->store_id);
        $rows = $documenttable->fetchAll($select)->toArray();
        if (!empty($rows)) {
          foreach ($rows as $key => $document_ids) {
            $document_id = $document_ids['document_id'];
            if (!empty($document_id)) {
              $documenttable->update(array(
                  'search' => '0'
                      ), array(
                  'document_id =?' => $document_id
              ));
            }
          }
        }
      }
     }
  }
  /**
   * Get expiry date for store
   * $params object $store
   * @return date
   * */
  public function getExpiryDate($store) {
    if (empty($store->expiration_date) || $store->expiration_date === "0000-00-00 00:00:00")
      return "-";
    $translate = Zend_Registry::get('Zend_Translate');
    if ($store->expiration_date === "2250-01-01 00:00:00")
      return $translate->translate('Never Expires');
    else {
      if (strtotime($store->expiration_date) < time())
        return "Expired";

      return date("M d,Y g:i A", strtotime($store->expiration_date));
    }
  }

  /**
   * Get status of store
   * $params object $store
   * @return string
   * */
  public function getStoreStatus($store) {
    $translate = Zend_Registry::get('Zend_Translate');
    if (!empty($store->declined)) {
      return "<span style='color: red;'>" . $translate->translate("Declined") . "</span>";
    }

    if (!empty($store->pending)) {
      return $translate->translate("Approval Pending");
    }
    if (!empty($store->approved)) {
      return $translate->translate("Approved");
    }


    if (empty($store->approved)) {
      return $translate->translate("Dis-Approved");
    }

    return "Approved";
  }

  public function getNumber($str) {
    $flag = 0;
    for ($check = 0; $check < strlen($str); $check++) {
      $flag += @ord($str[$check]);
    }
    return $flag;
  }

  /**
   * On installation time enable submodule for default package
   * $params string $modulename
   * */
  public function oninstallPackageEnableSubMOdules($modulename) {
    if (!Engine_Api::_()->sitestore()->hasPackageEnable())
      return;
    $package = Engine_Api::_()->getItemtable('sitestore_package')->fetchRow(array('defaultpackage = ?' => 1));
    if (!empty($package)) {
      $values = array();
      $values = unserialize($package->modules);
      $values[] = $modulename;
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $package->modules = serialize($values);
        $package->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

//Count the no of likes on a store
  public function getStoreLikes($values = array()) {
    if (empty($values['store_id']))
      return;

    $store_id = $values['store_id'];
    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $like_name = $likeTable->info('name');

    $like_select = $likeTable->select()
            ->from($like_name)
            ->where('resource_type = ?', 'sitestore_store')
            ->where('resource_id = ?', $store_id);

    if (!empty($values['startTime']) && !empty($values['endTime'])) {
      $like_select->where($like_name . '.creation_date >= ?', gmdate('Y-m-d H:i:s', $values['startTime']))
              ->where($like_name . '.creation_date < ?', gmdate('Y-m-d H:i:s', $values['endTime']));
    }
    return count($like_select->query()->fetchAll());
  }

//Calculate the no of likes on a store date or month wise
  public function getReportLikes($values = array()) {
    if (empty($values['store_id']))
      return;

    $store_id = $values['store_id'];
    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $like_name = $likeTable->info('name');

    $like_select = $likeTable->select()
            ->from($like_name, array('COUNT(like_id) as store_likes', 'creation_date'))
            ->where('resource_type = ?', 'sitestore_store')
            ->where('resource_id = ?', $store_id)
            ->group('resource_id');

    if (!empty($values['startTime']) && !empty($values['endTime'])) {
      $like_select->where($like_name . '.creation_date >= ?', gmdate('Y-m-d', $values['startTime']))
              ->where($like_name . '.creation_date < ?', gmdate('Y-m-d', $values['endTime']));
    }

    if (!empty($values['user_report'])) {
      if (!empty($values['time_summary'])) {
        if ($values['time_summary'] == 'Monthly') {
          $startTime = date('Y-m', mktime(0, 0, 0, $values['month_start'], date('d'), $values['year_start']));
          $endTime = date('Y-m', mktime(0, 0, 0, $values['month_end'], date('d'), $values['year_end']));
        } else {
          if (!empty($values['start_daily_time'])) {
            $start = $values['start_daily_time'];
          }
          if (!empty($values['start_daily_time'])) {
            $end = $values['end_daily_time'];
          }
          $startTime = date('Y-m-d', $start);
          $endTime = date('Y-m-d', $end);
        }
      }
      if (!empty($values['time_summary'])) {

        switch ($values['time_summary']) {

          case 'Monthly':
            $like_select
                    ->where("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m') >= ?", $startTime)
                    ->where("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m') <= ?", $endTime);
            if (!isset($values['total_stats']) && empty($values['total_stats'])) {
              $like_select->group("DATE_FORMAT(" . $like_name . " .creation_date, '%m')");
            }
            break;

          case 'Daily':
            $like_select
                    ->where("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m-%d') >= ?", $startTime)
                    ->where("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m-%d') <= ?", $endTime);
            if (!isset($values['total_stats']) && empty($values['total_stats'])) {
              $like_select->group("DATE_FORMAT(" . $like_name . " .creation_date, '%Y-%m-%d')");
            }
            break;
        }
      }
    }
    $like_array = $likeTable->fetchAll($like_select)->toarray();
    return $like_array;
  }

//Calculate the no of comments on a store date or month wise
  public function getReportComments($values = array()) {
    if (empty($values['store_id']))
      return;

    $store_id = $values['store_id'];
    $commentTable = Engine_Api::_()->getItemTable('core_comment');
    $comment_name = $commentTable->info('name');

    $comment_select = $commentTable->select()
            ->from($comment_name, array('COUNT(comment_id) as store_comments', 'creation_date'))
            ->where('resource_type = ?', 'sitestore_store')
            ->where('resource_id = ?', $store_id)
            ->group('resource_id');

    if (!empty($values['startTime']) && !empty($values['endTime'])) {
      $comment_select->where($comment_name . '.creation_date >= ?', gmdate('Y-m-d', $values['startTime']))
              ->where($comment_name . '.creation_date < ?', gmdate('Y-m-d', $values['endTime']));
    }

    if (!empty($values['user_report'])) {
      if (!empty($values['time_summary'])) {
        if ($values['time_summary'] == 'Monthly') {
          $startTime = date('Y-m', mktime(0, 0, 0, $values['month_start'], date('d'), $values['year_start']));
          $endTime = date('Y-m', mktime(0, 0, 0, $values['month_end'], date('d'), $values['year_end']));
        } else {
          if (!empty($values['start_daily_time'])) {
            $start = $values['start_daily_time'];
          }
          if (!empty($values['start_daily_time'])) {
            $end = $values['end_daily_time'];
          }
          $startTime = date('Y-m-d', $start);
          $endTime = date('Y-m-d', $end);
        }
      }
      if (!empty($values['time_summary'])) {

        switch ($values['time_summary']) {

          case 'Monthly':
            $comment_select
                    ->where("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m') >= ?", $startTime)
                    ->where("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m') <= ?", $endTime);
            if (!isset($values['total_stats']) && empty($values['total_stats'])) {
              $comment_select->group("DATE_FORMAT(" . $comment_name . " .creation_date, '%m')");
            }
            break;

          case 'Daily':
            $comment_select
                    ->where("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m-%d') >= ?", $startTime)
                    ->where("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m-%d') <= ?", $endTime);
            if (!isset($values['total_stats']) && empty($values['total_stats'])) {
              $comment_select->group("DATE_FORMAT(" . $comment_name . " .creation_date, '%Y-%m-%d')");
            }
            break;
        }
      }
    }
    $comment_array = $commentTable->fetchAll($comment_select)->toarray();
    return $comment_array;
  }

//Count the no of comments on a store
  public function getStoreComments($values = array()) {
    if (empty($values['store_id']))
      return;

    $store_id = $values['store_id'];
    $commentTable = Engine_Api::_()->getItemTable('core_comment');
    $comment_name = $commentTable->info('name');

    $comment_select = $commentTable->select()
            ->from($comment_name)
            ->where('resource_type = ?', 'sitestore_store')
            ->where('resource_id = ?', $store_id);

    if (!empty($values['startTime']) && !empty($values['endTime'])) {
      $comment_select->where($comment_name . '.creation_date >= ?', gmdate('Y-m-d H:i:s', $values['startTime']))
              ->where($comment_name . '.creation_date < ?', gmdate('Y-m-d H:i:s', $values['endTime']));
    }
    return count($comment_select->query()->fetchAll());
  }

  // This function checks that whether comments have to be displayed in insights or not
  public function displayCommentInsights() {
    $userlayout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    if (!empty($userlayout)) {
      $ContentTable = Engine_Api::_()->getDbtable('content', 'sitestore');
    } else {
      $ContentTable = Engine_Api::_()->getDbtable('content', 'core');
    }
    $select = $ContentTable->select()->where('name= ?', 'sitestore.info-sitestore')->limit(1);
    $infoWidget = $ContentTable->fetchRow($select);
    if (!empty($infoWidget)) {
      return true;
    } else {
      return false;
    }
  }

  public function hasStoreLike($RESOURCE_ID, $viewer_id) {
    if (empty($RESOURCE_ID) || empty($viewer_id))
      return false;

    $sub_status_table = Engine_Api::_()->getItemTable('core_like');

    $sub_status_select = $sub_status_table->select()
            ->where('resource_type = ?', 'sitestore_store')
            ->where('resource_id = ?', $RESOURCE_ID)
            ->where('poster_id = ?', $viewer_id);
    $fetch_sub = $sub_status_table->fetchRow($sub_status_select);
    if (!empty($fetch_sub))
      return true;
    else
      return false;
  }

  /**
   * check photo show in light box or not
   * */
  public function canShowPhotoLightBox() {
    global $sitestorealbum_isLightboxActive;
    if (empty($sitestorealbum_isLightboxActive)) {
      return;
    } else {
      return SEA_SITESTOREALBUM_LIGHTBOX;
    }
  }

  /**
   * check in case draft, not approved viewer can view store
   * */
  public function canViewStore($sitestore) {
    $can_view = true;
    if (empty($sitestore->draft) || (empty($sitestore->aprrove_date)) || (empty($sitestore->approved) && empty($sitestore->pending) ) || !empty($sitestore->declined)) {
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
      if (empty($isManageAdmin)) {
        $can_view = false;
      }
    }
    return $can_view;
  }

  public function attachStoreActivity($sitestore) {
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($sitestore->getOwner(), $sitestore, 'sitestore_new');

      if ($action != null) {
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitestore);
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function onStoreDelete($store_id) {

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    if (empty($store_id) || empty($viewer_id)) {
      return;
    }

    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    Engine_Api::_()->getDbtable('locations', 'sitestore')->delete(array('store_id =?' => $store_id));

    //FETCH PHOTO AND OTHER BELONGINGS
    $table = Engine_Api::_()->getItemTable('sitestore_photo');
    $select = $table->select()->where('store_id = ?', $store_id);
    $rows = $table->fetchAll($select);
    if (!empty($rows)) {
      foreach ($rows as $sitestorephoto) {
        //DELETE PHOTO AND OTHER BELONGINGS
        $sitestorephoto->delete();
      }
    }

    $table = Engine_Api::_()->getItemTable('sitestore_album');
    $select = $table->select()->where('store_id = ?', $store_id);
    $rows = $table->fetchAll($select);
    if (!empty($rows)) {
      foreach ($rows as $sitestorealbum) {
        //DELETE ALBUM AND OTHER BELONGINGS
        $sitestorealbum->delete();
      }
    }

    //END STORE-ALBUM CODE
    //START STORE-BADGE CODE
    $sitestorebadgeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge');
    if ($sitestorebadgeEnabled) {
      //DELETE BADGE REQUESTS CORROSPONDING TO THAT STORE ID
      Engine_Api::_()->getItemTable('sitestorebadge_badgerequest')->delete(array('store_id = ?' => $store_id));
    }
    //END STORE-BADGE CODE
    //START STORE-DISCUSSION CODE
    $sitestoreDiscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion');
    if ($sitestoreDiscussionEnabled) {

      $table = Engine_Api::_()->getItemTable('sitestore_topic');
      $select = $table->select()->where('store_id = ?', $store_id);
      $rows = $table->fetchAll($select);
      if (!empty($rows)) {
        foreach ($rows as $topic) {
          $topic->delete();
        }
      }

      $table = Engine_Api::_()->getItemTable('sitestore_post');
      $select = $table->select()->where('store_id = ?', $store_id);
      $rows = $table->fetchAll($select);
      if (!empty($rows)) {
        foreach ($rows as $post) {
          $post->delete();
        }
      }

      Engine_Api::_()->getDbtable('topicwatches', 'sitestore')->delete(array('store_id =?' => $store_id));
    }
    //END STORE-DISCUSSION CODE
    //START STORE-DOCUMENT CODE
    $sitestoredocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument');
    if ($sitestoredocumentEnabled) {

      //FETCH DOCUMENTS CORROSPONDING TO THAT SITESTORE ID
      $table = Engine_Api::_()->getItemTable('sitestoredocument_document');
      $select = $table->select()
              ->from($table->info('name'), 'document_id')
              ->where('store_id = ?', $store_id);
      $rows = $table->fetchAll($select)->toArray();
      if (!empty($rows)) {
        foreach ($rows as $key => $document_ids) {
          $document_id = $document_ids['document_id'];
          if (!empty($document_id)) {
            Engine_Api::_()->sitestoredocument()->deleteContent($document_id);
          }
        }
      }
    }
    //END STORE-DOCUMENT CODE
    //START STORE-EVENT CODE
    $sitestoreeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent');
    if ($sitestoreeventEnabled) {
      //FETCH Notes CORROSPONDING TO THAT Store ID
      $table = Engine_Api::_()->getItemTable('sitestoreevent_event');
      $select = $table->select()
              ->from($table->info('name'), 'event_id')
              ->where('store_id = ?', $store_id);
      $rows = $table->fetchAll($select)->toArray();
      if (!empty($rows)) {
        foreach ($rows as $key => $event_ids) {
          $event_id = $event_ids['event_id'];
          if (!empty($event_id)) {
            //DELETE EVENT, ALBUM AND EVENT IMAGES
            Engine_Api::_()->sitestoreevent()->deleteContent($event_id);
          }
        }
      }
    }
    //END STORE-EVENT CODE
    //START ADVANCED-EVENT CODE
    $siteeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
    if ($siteeventEnabled) {
      $table = Engine_Api::_()->getItemTable('siteevent_event');
      $select = $table->select()
              ->from($table->info('name'), 'event_id')
              ->where('parent_type = ?', 'sitestore_store')
              ->where('parent_id = ?', $store_id);
      $rows = $table->fetchAll($select)->toArray();
      if (!empty($rows)) {
        foreach ($rows as $key => $event_ids) {
          $resource = Engine_Api::_()->getItem('siteevent_event', $event_ids['event_id']);
          if ($resource)
            $resource->delete();
        }
      }
    }
    
    $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
    if ($sitevideoEnabled) {
      $table = Engine_Api::_()->getItemTable('sitevideo_video');
      $select = $table->select()
              ->from($table->info('name'), 'video_id')
              ->where('parent_type = ?', 'sitestore_store')
              ->where('parent_id = ?', $store_id);
      $rows = $table->fetchAll($select)->toArray();
      if (!empty($rows)) {
        foreach ($rows as $key => $video_ids) {
          $resource = Engine_Api::_()->getItem('sitevideo_video', $video_ids['video_id']);
          if ($resource)
            $resource->delete();
        }
      }
    }
    
    $documentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document');
    if ($documentEnabled) {
      $table = Engine_Api::_()->getItemTable('document');
      $select = $table->select()
              ->from($table->info('name'), 'document_id')
              ->where('parent_type = ?', 'sitestore_store')
              ->where('parent_id = ?', $store_id);
      $rows = $table->fetchAll($select)->toArray();
      if (!empty($rows)) {
        foreach ($rows as $key => $document_ids) {
          $resource = Engine_Api::_()->getItem('document', $document_ids['document_id']);
          if ($resource)
            $resource->delete();
        }
      }
    }
    
    //END ADVANCED-EVENT CODE
    //START STORE-FORM CODE
    $sitestoreFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform');
    if ($sitestoreFormEnabled) {
      $mapstable = Engine_Api::_()->fields()->getTable('sitestoreform', 'maps');
      $optiontable = Engine_Api::_()->fields()->getTable('sitestoreform', 'options');

      $storequetion_table = Engine_Api::_()->getDbtable('storequetions', 'sitestoreform');
      $sitestoreform_table = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform');
      $select = $storequetion_table->select()->where('store_id =?', $store_id);
      $optionid = $storequetion_table->fetchRow($select);
      $option_id = $optionid->option_id;
      if (!empty($option_id)) {
        $matatable = Engine_Api::_()->fields()->getTable('sitestoreform', 'meta');
        $select_options = $matatable->select()->where('option_id =?', $option_id);
        $select_options_result = $select_options->from($matatable->info('name'), array('field_id'));
        $result = $matatable->fetchAll($select_options_result)->toArray();
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
          foreach ($result as $key => $id) {
            $field_id = $id['field_id'];
            $matatable->delete(array('field_id =?' => $field_id));
            $optiontable->delete(array('field_id =?' => $field_id));
          }
          $optiontable->delete(array('option_id =?' => $option_id));
          $storequetion_table->delete(array('option_id =?' => $option_id));
          $mapstable->delete(array('option_id =?' => $option_id));
          $sitestoreform_table->delete(array('store_id =?' => $store_id));
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }
    }
    //END STORE-FORM CODE
    //START STORE-NOTE CODE
    $sitestorenoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote');
    if ($sitestorenoteEnabled) {
      //FETCH Notes CORROSPONDING TO THAT Store ID
      $table = Engine_Api::_()->getItemTable('sitestorenote_note');
      $select = $table->select()
              ->from($table->info('name'), 'note_id')
              ->where('store_id = ?', $store_id);
      $rows = $table->fetchAll($select)->toArray();
      if (!empty($rows)) {
        foreach ($rows as $key => $note_ids) {
          $note_id = $note_ids['note_id'];
          if (!empty($note_id)) {

            //DELETE NOTE, ALBUM AND NOTE IMAGES
            Engine_Api::_()->sitestorenote()->deleteContent($note_id);
          }
        }
      }
    }
    //END STORE-NOTE CODE
    //START STORE-OFFER CODE
    $sitestoreofferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer');
    if ($sitestoreofferEnabled) {
      //FETCH Offers CORROSPONDING TO THAT Store ID
      $table = Engine_Api::_()->getItemTable('sitestoreoffer_offer');
      $select = $table->select()->where('store_id = ?', $store_id);
      $rows = $table->fetchAll($select);
      if (!empty($rows)) {
        foreach ($rows as $sitestoreoffer) {
          Engine_Api::_()->sitestoreoffer()->deleteContent($sitestoreoffer->offer_id);
        }
      }
    }
    //END STORE-OFFER CODE
    //START STORE-POLL CODE
    $sitestorepollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll');
    if ($sitestorepollEnabled) {
      //FETCH POLLS CORROSPONDING TO THAT GROUP ID
      $table = Engine_Api::_()->getItemTable('sitestorepoll_poll');
      $select = $table->select()->where('store_id = ?', $store_id);
      $rows = $table->fetchAll($select);
      if (!empty($rows)) {
        foreach ($rows as $sitestorepoll) {
          //DELETE POLL AND OTHER BELONGINGS
          $sitestorepoll->delete();
        }
      }
    }
    //END STORE-POLL CODE
    //START STORE-REVIEW CODE
    $sitestorereviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
    if ($sitestorereviewEnabled) {

      //FETCH REVIEWS
      $table = Engine_Api::_()->getItemTable('sitestorereview_review');
      $select = $table->select()->where('store_id = ?', $store_id);
      $rows = $table->fetchAll($select);

      if (!empty($rows)) {
        foreach ($rows as $review) {
          Engine_Api::_()->sitestorereview()->deleteContent($review->review_id);
        }
      }
    }
    //END STORE-REVIEW CODE
    //START STORE-WISHLIST CODE
    $sitestorewishlistEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorewishlist');
    if ($sitestorewishlistEnabled) {
      Engine_Api::_()->getDbtable('stores', 'sitestorewishlist')->delete(array('store_id =?' => $store_id));
    }
    //END STORE-WISHLIST CODE
    //START STORE-VIDEO CODE
    $sitestorevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo');
    if ($sitestorevideoEnabled) {
      //FETCH VIDEOS CORROSPONDING TO THAT SITESTORE ID


      $table = Engine_Api::_()->getItemTable('sitestorevideo_video');
      $select = $table->select()->where('store_id = ?', $store_id);
      $rows = $table->fetchAll($select);
      if (!empty($rows)) {
        foreach ($rows as $video) {
          //DELETE VIDEO AND OTHER BELONGINGS
          Engine_Api::_()->getDbtable('ratings', 'sitestorevideo')->delete(array('video_id = ?' => $video->video_id));
          $video->delete();
        }
      }
    }
    //END STORE-VIDEO CODE
    //START STORE-MUSIC CODE
    $sitestoremusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic');
    if ($sitestoremusicEnabled) {
      //FETCH PLAYLIST CORROSPONDING TO THAT STORE ID
      $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sitestoremusic');
      $playlistSelect = $playlistTable->select()->where('store_id = ?', $store_id);
      foreach ($playlistTable->fetchAll($playlistSelect) as $playlist) {
        foreach ($playlist->getSongs() as $song) {
          $song->deleteUnused();
        }
        $playlist->delete();
      }
    }
    //END STORE-MUSIC CODE
    //START STORE-MEMBER CODE
    $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
    if ($sitestorememberEnabled) {
      $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitestore');
      $membershipTable->delete(array('resource_id =?' => $store_id, 'store_id =?' => $store_id));
    }
    //END STORE-MEMBER CODE
    //FINALLY START STORE CODE
    // DELETE TAX
    $taxTable = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct');
    $taxratesTable = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct');
    $taxSelect = $taxTable->select()->where('store_id = ?', $store_id);
    foreach ($taxTable->fetchAll($taxSelect) as $tax) {
      $taxratesTable->delete(array('tax_id =?' => $tax->tax_id));
      $tax->delete();
    }

    // DELETE SHIPPING METHODS
    Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct')
            ->delete(array('store_id =?' => $store_id));

    // DELETE PAYMENT INFO
    Engine_Api::_()->getDbtable('gateways', 'sitestoreproduct')
            ->delete(array('store_id =?' => $store_id));

    // DELETE ALL RESPECTIVE PRODUCTS
    $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    $productSelect = $productTable->select()->where('store_id = ?', $store_id);
    foreach ($productTable->fetchAll($productSelect) as $product) {
      Engine_Api::_()->getItem("sitestoreproduct_product", $product->product_id)->delete();
    }

    $searchTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'search');
    $valuesTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'values');

    $storestatisticsTable = Engine_Api::_()->getDbtable('storestatistics', 'sitestore');

    $writesTable = Engine_Api::_()->getDbtable('writes', 'sitestore');
    $listsTable = Engine_Api::_()->getDbtable('lists', 'sitestore');

    //$viewedsTable = Engine_Api::_()->getDbtable('vieweds', 'sitestore');
    $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');

    $locationsTable = Engine_Api::_()->getDbtable('locations', 'sitestore');

    $authAllowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $claimTable = Engine_Api::_()->getDbtable('claims', 'sitestore');
    $sitestoreitemofthedaysTable = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore');

    $layoutcontentTable = Engine_Api::_()->getDbtable('content', 'sitestore');
    $layoutcontentstoreTable = Engine_Api::_()->getDbtable('contentstores', 'sitestore');
    //GETTING THE CONTENTSTORE ID FROM CONTENTSTORES TABLE SO THAT WE CAN REMOVE THE CONTENT FROM CONTENT TABLE ALSO.
    $LayoutContentStoreName = $layoutcontentstoreTable->info('name');

    $select = $layoutcontentstoreTable->select()->from($LayoutContentStoreName, 'contentstore_id')->where('store_id = ?', $store_id);
    $LayoutContentStoreid = $layoutcontentstoreTable->fetchRow($select);
    if (!empty($LayoutContentStoreid)) {
      $LayoutContentStoreid = $LayoutContentStoreid->toarray();
    }

    $searchTable->delete(array('item_id =?' => $store_id));
    $valuesTable->delete(array('item_id =?' => $store_id));
    $writesTable->delete(array('store_id =?' => $store_id));
    $listsTable->delete(array('store_id =?' => $store_id));
    $storestatisticsTable->delete(array('store_id =?' => $store_id));

    // $viewedsTable->delete(array('store_id =?' => $store_id));
    $manageadminsTable->delete(array('store_id =?' => $store_id));
    $locationsTable->delete(array('store_id =?' => $store_id));

    $sitestoreitemofthedaysTable->delete(array('resource_id =?' => $store_id, 'resource_type' => 'sitestore_store'));

    $authAllowTable->delete(array('resource_id =?' => $store_id, 'resource_type =?' => 'sitestore_store'));
    $claimTable->delete(array('store_id =?' => $store_id));

    //DELETE FIELD ENTRIES IF EXISTS
    $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'values');
    $fieldvalueTable->delete(array(
        'item_id = ?' => $store_id,
    ));

    $fieldsearchTable = Engine_Api::_()->fields()->getTable('sitestore_store', 'search');
    $fieldsearchTable->delete(array(
        'item_id = ?' => $store_id,
    ));

    if (!empty($LayoutContentStoreid)) {
      $layoutcontentTable->delete(array('contentstore_id =?' => $LayoutContentStoreid['contentstore_id']));
    }

    $layoutcontentstoreTable->delete(array('store_id =?' => $store_id));
    $sitestore->cancel();
    $sitestore->delete();

    //END STORE CODE
  }

  public function isEnabledModPackage($mod) {
    if (!empty($mod)) {
      $arrayName = 'sitestore.mod.types';
    } else {
      $arrayName = 'sitestore.mod.settings';
    }
    return Engine_Api::_()->getApi('settings', 'core')->getSetting($arrayName, null);
  }

  public function isEnabled() {
    $sitestoreProductsEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.isproduct.enable', 0);
    $hostType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.viewstore.sett', 0);
    $hostName = convert_uudecode($hostType);
    if ($hostName == 'localhost' || strpos($hostName, '192.168.') != false || strpos($hostName, '127.0.') != false || !empty($sitestoreProductsEnable)) {
      return;
    }

    return 1;
  }

  public function isPluginActivate($modName) {
    $isExist = Engine_Api::_()->getApi('settings', 'core')->getSetting($modName . '.isActivate', 0);
    if(empty($isExist))
      Engine_Api::_()->getApi('settings', 'core')->setSetting($modName . '.isActivate', 1);
    
    return (bool) 1;
  }

  /**
   *  CHECK Payment PLUGIN ENABLE / DISABLE
   * */
  public function enablePaymentPlugin() {
    return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('payment');
  }

  /**
   *  Check Viewer able to edit style
   * */
  public function allowStyle() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if (!empty($viewer_id)) {
      return (bool) Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('sitestore_store', $viewer->level_id, 'style');
    } else {
      return (bool) 0;
    }
  }

  /**
   * get viewer like stores
   */
  public function getMyLikeStores($params = array()) {
//    $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
//    $likeName = $likeTable->info('name');
//    $select = $likeTable->select()
//            ->where($likeName . '.poster_id = ?', $params['poster_id'])
//            ->where($likeName . '.poster_type = ?', $params['poster_type'])
//            ->where($likeName . '.resource_type = ?', $params['resource_type'])
//            ->order($likeName . '.creation_date DESC');
//    return $likeTable->fetchAll($select);
  }

  public function getMapInfo() {
    $productStatus = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.product.status', 0);
    $storeIsproductType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.isproduct.enable', 1);
    $storeActivateProduct = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.activate.product');

    $storeIsproductEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('product.types.info');
    $getQueryArray = @unserialize($storeIsproductEnable);
    $getNumFlag = $this->getNumber($getQueryArray[$productStatus]);

    $store_field_value = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
    $store_field_str = $this->getNumber($store_field_value);
    $getNumber = $getNumFlag * $store_field_str;

    if (($storeActivateProduct == $getNumber) || !empty($storeIsproductEnable) || !empty($storeIsproductType)) {
      return true;
    }
    return false;
  }

  /**
   * Gets member like stores
   *
   * $member User_Model_User 
   */
  public function getMemberLikeStoresOfIds($member) {
    $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
    $likeName = $likeTable->info('name');
    return $select = $likeTable->select()
            ->from($likeName, "resource_id")
            ->where($likeName . '.poster_id = ?', $member->getIdentity())
            ->where($likeName . '.poster_type = ?', $member->getType())
            ->where($likeName . '.resource_type = ?', 'sitestore_store')
            ->order($likeName . '.creation_date DESC')
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
    ;
  }

  /**
   * Gets feed for which viewer like
   *
   * @$user User_Model_User
   * @param array $params
   */
  public function getFeedActionLikedStores(User_Model_User $user, array $params = array()) {
//    $ids = array();
//    if (!(bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feed.likestore', 0))
//      return $ids;
//    $getMyLikes = $this->getMyLikeStores(array("poster_type" => $user->getType(), "poster_id" => $user->getIdentity(), "resource_type" => "sitestore_store"));
//    $count = count($getMyLikes);
//
//    if (!empty($count)) {
//      $resource_ids = array();
//      foreach ($getMyLikes as $likeItems) {
//        $resource_ids[] = $likeItems->resource_id;
//      }
//
//      if (!empty($resource_ids)) {
//        //Proc args
//        extract($params); //action_id, limit, min_id, max_id
//        $actionDbTable = Engine_Api::_()->getDbtable('actions', 'activity');
//        $typesAdmin = array("sitestore_post_self", "sitestorealbum_admin_photo_new", "sitestorevideo_admin_new", "sitestoreevent_admin_new", "sitestorenote_admin_new", "sitestorepoll_admin_new", "sitestoredocument_admin_new", "sitestoreoffer_admin_new", "sitestore_admin_topic_create", "sitestoremusic_admin_new", "sitestore_profile_photo_update");
//        $select = $actionDbTable->select()
//                ->where("type in (?)", new Zend_Db_Expr("'" . join("', '", $typesAdmin) . "'"))
//                ->where("subject_type = ? ", "sitestore_store")
//                ->where("subject_id IN(?)", new Zend_Db_Expr(join(',', $resource_ids)))
//                ->order('action_id DESC')
//                ->limit($limit);
//
//        if (null !== $action_id) {
//          $select->where('action_id = ?', $action_id);
//        } else {
//          if (null !== $min_id) {
//            $select->where('action_id >= ?', $min_id);
//          } else if (null !== $max_id) {
//            $select->where('action_id <= ?', $max_id);
//          }
//        }
//        $results = $actionDbTable->fetchAll($select);
//        foreach ($results as $actionData)
//          $ids[] = $actionData->action_id;
//      }
//    }
//    return $ids;
  }

  /**
   * Gets feed type store title and photo is enable
   *
   * @return bool
   */
  public function isFeedTypeStoreEnable() {
    return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feed.type', 0);
  }

  /**
   * Set default widget in core stores table
   *
   * @param object $table
   * @param string $tablename
   * @param int $store_id
   * @param string $type
   * @param string $widgetname
   * @param int $middle_id 
   * @param int $order 
   * @param string $title 
   * @param int $titlecount    
   */
  function setDefaultDataWidget($table, $tablename, $store_id, $type, $widgetname, $middle_id, $order, $title = null, $titlecount = null, $advanced_activity_params = null) {

    $selectWidgetId = $table->select()
            ->where('store_id =?', $store_id)
            ->where('type = ?', $type)
            ->where('name = ?', $widgetname)
            ->where('parent_content_id = ?', $middle_id)
            ->limit(1);
    $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
    if (empty($fetchWidgetContentId)) {
      $contentWidget = $table->createRow();
      $contentWidget->store_id = $store_id;
      $contentWidget->type = $type;
      $contentWidget->name = $widgetname;
      $contentWidget->parent_content_id = $middle_id;
      $contentWidget->order = $order;
      if (empty($advanced_activity_params)) {
        if( !empty($title) && !empty($titlecount) )
          $contentWidget->params = "{\"title\":\"$title\",\"titleCount\":\"$titlecount\"}";
        else if( !empty($title) )
          $contentWidget->params = "{\"title\":\"$title\"}";
        else if( !empty($titlecount) )
          $contentWidget->params = "{\"titleCount\":\"$titlecount\"}";
      } else {
        $contentWidget->params = "$advanced_activity_params";
      }
      $contentWidget->save();
    }
  }

  /**
   * Return store paginator
   *
   * @param int $total_items
   * @param int $items_per_store
   * @param int $p
   * @return paginator
   */
  public function makeStore($total_items, $items_per_store, $p) {
    if (!$items_per_store)
      $items_per_store = 1;
    $maxstore = ceil($total_items / $items_per_store);
    if ($maxstore <= 0)
      $maxstore = 1;
    $p = ( ($p > $maxstore) ? $maxstore : ( ($p < 1) ? 1 : $p ) );
    $start = ($p - 1) * $items_per_store;
    return array($start, $p, $maxstore);
  }

  /**
   * Return count
   *
   * @param string $tablename
   * @param string $modulename
   * @param int $store_id
   * @param int $title_count
   * @return paginator
   */
  public function getTotalCount($store_id, $modulename, $tablename) {

    if ($modulename == 'siteevent' || $modulename == 'sitevideo' || $modulename == 'document') {
      $table = Engine_Api::_()->getDbtable($tablename, $modulename);
      $count = 0;
      $count = $table
              ->select()
              ->from($table->info('name'), array('count(*) as count'))
              ->where("parent_type = ?", 'sitestore_store')
              ->where("parent_id =?", $store_id)
              ->query()
              ->fetchColumn();
    } else {
      $table = Engine_Api::_()->getDbtable($tablename, $modulename);
      $count = 0;
      $count = $table
              ->select()
              ->from($table->info('name'), array('count(*) as count'))
              ->where("store_id = ?", $store_id)
              ->query()
              ->fetchColumn();
    }
    return $count;
  }

  /**
   * Return tabid
   *
   * @param string $widgetname
   * @param int $storeid
   * @param int $layout
   * @return tabid
   */
  public function GetTabIdinfo($widgetname, $storeid, $layout) {

    global $sitestore_GetTabIdType;
    $tab_id = '';
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      if (!$layout) {
        if (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
          $tablecontent = Engine_Api::_()->getDbtable('content', 'sitemobile');
          $select = $tablecontent->select()
                  ->where('name = ?', $widgetname)
                  ->limit(1);
          $row = $tablecontent->fetchRow($select);
          if ($row !== null) {
            $tab_id = $row->content_id;
          }
        } elseif (Engine_Api::_()->sitemobile()->checkMode('tablet-mode')) {
          $tablecontent = Engine_Api::_()->getDbtable('tabletcontent', 'sitemobile');
          $select = $tablecontent->select()
                  ->where('name = ?', $widgetname)
                  ->limit(1);
          $row = $tablecontent->fetchRow($select);
          if ($row !== null) {
            $tab_id = $row->content_id;
          }
        }
      } else {
        $table = Engine_Api::_()->getDbtable('mobileContentstores', 'sitestore');
        $select = $table->select()
                ->where('name = ?', 'sitestore_index_view')
                ->where('store_id = ?', $storeid)
                ->limit(1);
        $row = $table->fetchRow($select);
        if ($row !== null) {
          $store_id = $row->mobilecontentstore_id;
          if (!empty($store_id)) {
            $tablecontent = Engine_Api::_()->getDbtable('mobileContent', 'sitestore');
            $select = $tablecontent->select()
                    ->where('name = ?', $widgetname)
                    ->where('mobilecontentstore_id = ?', $store_id)
                    ->limit(1);
            $row = $tablecontent->fetchRow($select);
            if ($row !== null) {
              $tab_id = $row->mobilecontent_id;
            }
          }
        } else {
          $page_id = $this->getMobileWidgetizedStore()->page_id;
          if (!empty($page_id)) {
            $tablecontent = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitestore');
            $select = $tablecontent->select()
                    ->where('name = ?', $widgetname)
                    ->where('store_id = ?', $page_id)
                    ->limit(1);
            $row = $tablecontent->fetchRow($select);
            if ($row !== null) {
              $tab_id = $row->mobileadmincontent_id;
            }
          }
        }
      }
      return $sitestore_GetTabIdType ? $tab_id : $sitestore_GetTabIdType;
    }

    if (!$layout) {
      $tablecontent = Engine_Api::_()->getDbtable('content', 'core');
      $select = $tablecontent->select()
              ->where('name = ?', $widgetname)
              ->where('page_id = ?', $this->getWidgetizedStore()->page_id)
              ->limit(1);
      $row = $tablecontent->fetchRow($select);
      if ($row !== null) {
        $tab_id = $row->content_id;
      }
    } else {

      $table = Engine_Api::_()->getDbtable('contentstores', 'sitestore');
      $select = $table->select()
              ->where('name = ?', 'sitestore_index_view')
              ->where('store_id = ?', $storeid)
              ->limit(1);
      $row = $table->fetchRow($select);
      if ($row !== null) {
        $contentstore_id = $row->contentstore_id;
        if (!empty($contentstore_id)) {
          $tablecontent = Engine_Api::_()->getDbtable('content', 'sitestore');
          $select = $tablecontent->select()
                  ->where('name = ?', $widgetname)
                  ->where('contentstore_id = ?', $contentstore_id)
                  ->limit(1);
          $row = $tablecontent->fetchRow($select);
          if ($row !== null) {
            $tab_id = $row->content_id;
          }
        }
      } else {
        $page_id = $this->getWidgetizedStore()->page_id;
        if (!empty($page_id)) {
          $tablecontent = Engine_Api::_()->getDbtable('admincontent', 'sitestore');
          $select = $tablecontent->select()
                  ->where('name = ?', $widgetname)
                  ->where('store_id = ?', $page_id)
                  ->limit(1);
          $row = $tablecontent->fetchRow($select);
          if ($row !== null) {
            $tab_id = $row->admincontent_id;
          }
        }
      }
    }

    return $sitestore_GetTabIdType ? $tab_id : $sitestore_GetTabIdType;
  }

  /**
   * Gets widgetized store
   *
   * @return Zend_Db_Table_Select
   */
  public function getWidgetizedStore() {

    //GET CORE STORE TABLE
    $tableNameStore = Engine_Api::_()->getDbtable('pages', 'core');
    $select = $tableNameStore->select()
            ->from($tableNameStore->info('name'), array('page_id', 'description', 'keywords'))
            ->where('name =?', 'sitestore_index_view')
            ->limit(1);

    return $tableNameStore->fetchRow($select);
  }

  /**
   * Return option of showing the widget of third type layout
   *
   * @param int $store_id
   * @param int $layout
   * @return third type layout show or not
   */
  public function getwidget($layout, $store_id) {
    if (!$layout) {
      $page_id = $this->getWidgetizedStore()->page_id;
      if (!empty($page_id)) {
        $table = Engine_Api::_()->getDbtable('content', 'core');
        $selectContent = $table->select()
                ->from($table->info('name'), 'page_id')
                ->where("name IN ('core.container-tabs', 'sitestore.widgetlinks-sitestore')")
                ->where('page_id =?', $page_id)
                ->limit(1);
        $contentinfo = $selectContent->query()->fetchAll();
        if (empty($contentinfo)) {
          $contentinformation = 0;
        } else {
          $contentinformation = 1;
        }
      }
    } else {
      $table = Engine_Api::_()->getDbtable('contentstores', 'sitestore');
      $select = $table->select()
              ->from($table->info('name'), 'contentstore_id')
              ->where('name = ?', 'sitestore_index_view')
              ->where('store_id = ?', $store_id)
              ->limit(1);
      $row = $table->fetchRow($select);
      if ($row !== null) {
        $store_id = $row->contentstore_id;
        $table = Engine_Api::_()->getDbtable('content', 'sitestore');
        $selectContent = $table->select()
                ->from($table->info('name'), 'contentstore_id')
                ->where("name IN ('core.container-tabs', 'sitestore.widgetlinks-sitestore')")
                ->where('contentstore_id =?', $store_id);
        $contentinfo = $selectContent->query()->fetchAll();
        if (!empty($contentinfo)) {
          $contentinformation = 1;
        } else {
          $contentinformation = 0;
        }
      } else {
        $page_id = $this->getWidgetizedStore()->page_id;
        $table = Engine_Api::_()->getDbtable('admincontent', 'sitestore');
        $selectContent = $table->select()
                ->from($table->info('name'), 'store_id')
                ->where("name IN ('core.container-tabs', 'sitestore.widgetlinks-sitestore')")
                ->where('store_id =?', $page_id);
        $contentinfo = $selectContent->query()->fetchAll();
        if (!empty($contentinfo)) {
          $contentinformation = 1;
        } else {
          $contentinformation = 0;
        }
      }
    }
    return $contentinformation;
  }

  /**
   * Return option of showing the top title for widgets
   *
   * @param int $stores_id
   * @param int $layout
   * @return top title show or not
   */
  public function showtoptitle($layout, $store_id) {
    if (!$layout) {
      $page_id = $this->getWidgetizedStore()->page_id;
      if (!empty($page_id)) {
        $table = Engine_Api::_()->getDbtable('content', 'core');
        $tablename = $table->info('name');
        $selectContent = $table->select()
                ->from($table->info('name'), 'page_id')
                ->where('name =?', 'core.container-tabs')
                ->where('page_id =?', $page_id)
                ->limit(1);
        $contentinfo = $selectContent->query()->fetchAll();
        if (empty($contentinfo)) {
          $contentinformation = 1;
        } else {
          $contentinformation = 0;
        }
      }
    } else {
      $table = Engine_Api::_()->getDbtable('contentstores', 'sitestore');
      $select = $table->select()
              ->from($table->info('name'), 'contentstore_id')
              ->where('name = ?', 'sitestore_index_view')
              ->where('store_id =?', $store_id)
              ->limit(1);
      $row = $table->fetchRow($select);
      if ($row !== null) {
        $store_id = $row->contentstore_id;
        $table = Engine_Api::_()->getDbtable('content', 'sitestore');
        $selectContent = $table->select()
                ->from($table->info('name'), 'contentstore_id')
                ->where('name =?', 'core.container-tabs')
                ->where('contentstore_id =?', $store_id)
                ->limit(1);
        $contentinfo = $selectContent->query()->fetchAll();
        if (empty($contentinfo)) {
          $contentinformation = 1;
        } else {
          $contentinformation = 0;
        }
      } else {
        $page_id = $this->getWidgetizedStore()->page_id;
        $table = Engine_Api::_()->getDbtable('admincontent', 'sitestore');
        $selectContent = $table->select()
                        ->from($table->info('name'), 'store_id')
                        ->where('name =?', 'core.container-tabs')
                        ->where('store_id =?', $page_id)->limit(1);
        ;
        $contentinfo = $selectContent->query()->fetchAll();
        if (empty($contentinfo)) {
          $contentinformation = 1;
        } else {
          $contentinformation = 0;
        }
      }

      return $contentinformation;
    }
  }

  /**
   * Return tabid
   *
   * @param string $widgetname
   * @param int $store_id
   * @param int $layout
   * @return tabid
   */
  public function getTabIdInfoIntegration($widgetname, $storeid, $layout, $resource_type = null) {

    global $sitestore_GetTabIdType;
    $tab_id = '';
    if (!$layout) {
      $tablecontent = Engine_Api::_()->getDbtable('content', 'core');
      $select = $tablecontent->select()
              ->where('name = ?', $widgetname);

      if (!empty($resource_type)) {
        $select
                ->where('params LIKE ?', '%' . $resource_type . '%');
      }

      $select->order('order ASC')
              ->limit(1);
      $row = $tablecontent->fetchRow($select);
      if ($row !== null) {
        $tab_id = $row->content_id;
      }
    } else {
      $table = Engine_Api::_()->getDbtable('contentstores', 'sitestore');
      $select = $table->select()
              ->where('name = ?', 'sitestore_index_view')
              ->where('store_id = ?', $storeid)
              ->limit(1);
      $row = $table->fetchRow($select);
      if ($row !== null) {
        $store_id = $row->contentstore_id;
        $tablecontent = Engine_Api::_()->getDbtable('content', 'sitestore');
        $select = $tablecontent->select()
                ->where('name = ?', $widgetname)
                ->where('contentstore_id = ?', $store_id);

        if (!empty($resource_type)) {
          $select
                  ->where('params LIKE ?', '%' . $resource_type . '%');
        }

        $select->order('order ASC')->limit(1);
        $row = $tablecontent->fetchRow($select);
        if ($row !== null) {
          $tab_id = $row->content_id;
        }
      } else {
        $page_id = $this->getWidgetizedStore()->page_id;
        $tablecontent = Engine_Api::_()->getDbtable('admincontent', 'sitestore');
        $select = $tablecontent->select()
                ->where('name = ?', $widgetname)
                ->where('store_id = ?', $page_id);

        if (!empty($resource_type)) {
          $select
                  ->where('params LIKE ?', '%' . $resource_type . '%');
        }

        $select->order('order ASC')->limit(1);
        $row = $tablecontent->fetchRow($select);
        if ($row !== null) {
          $tab_id = $row->admincontent_id;
        }
      }
    }

    return $sitestore_GetTabIdType ? $tab_id : $sitestore_GetTabIdType;
  }

  /**
   * Return parse string
   *
   * @param string $content
   * @return parse string
   */
  public function parseString($content) {
    return str_replace("'", "\'", trim($content));
  }

  /**
   * Return true or false for ad show on paid stores
   *
   * @param object $sitestore
   * @return true or false
   */
  public function showAdWithPackage($sitestore) {
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      return 0;
    }

    $package = $sitestore->getPackage();
    if (isset($package->ads)) {
      return (bool) $package->ads;
    } else {
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1)) {
        return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adwithpackage', 1);
      } else {
        return 0;
      }
    }
  }

  /**
   * Set default widget in core stores table
   *
   * @param object $table
   * @param string $tablename
   * @param int $store_id
   * @param string $type
   * @param string $widgetname
   * @param int $middle_id
   * @param int $order
   * @param string $title
   * @param int $titlecount
   */
  function setDefaultDataContentWidget($table, $tablename, $store_id, $type, $widgetname, $middle_id, $order, $title = null, $titlecount = null, $advanced_activity_params = null) {

    $selectWidgetId = $table->select()
            ->where('page_id =?', $store_id)
            ->where('type = ?', $type)
            ->where('name = ?', $widgetname)
            ->where('parent_content_id = ?', $middle_id)
            ->limit(1);
    $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
    if (empty($fetchWidgetContentId)) {
      $contentWidget = $table->createRow();
      $contentWidget->page_id = $store_id;
      $contentWidget->type = $type;
      $contentWidget->name = $widgetname;
      $contentWidget->parent_content_id = $middle_id;
      $contentWidget->order = $order;
      if (empty($advanced_activity_params)) {
        if( !empty($title) && !empty($titlecount) )
          $contentWidget->params = "{\"title\":\"$title\",\"titleCount\":\"$titlecount\"}";
        else if( !empty($title) )
          $contentWidget->params = "{\"title\":\"$title\"}";
        else if( !empty($titlecount) )
          $contentWidget->params = "{\"titleCount\":\"$titlecount\"}";
      } else {
        $contentWidget->params = "$advanced_activity_params";
      }
      $contentWidget->save();
    }
  }

  public function getModulelabel($title) {

    $menuitemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
    $selectMenuitemsTable = $menuitemsTable->select()->where('name =?', "core_admin_main_plugins_$title");
    $resultMenuitems = $menuitemsTable->fetchRow($selectMenuitemsTable);
    return $resultMenuitems;
  }

  public function getBannedUrls() {
    $merge_array = array();
    $pageUrlFinalArray = array();
    $groupUrlFinalArray = array();
    $businessUrlFinalArray = array();
    $staticpageUrlFinalArray = array();
    $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
    $urlArray = $bannedPageurlsTable->select()->from($bannedPageurlsTable, 'word')
                    ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

    $enableSitepage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');

    if ($enableSitepage) {
      $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
      $pageUrlArray = $pageTable->select()->from($pageTable, 'page_url')
                      ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      foreach ($pageUrlArray as $url) {
        $pageUrlFinalArray[] = strtolower($url);
      }
      $merge_array = array_merge($urlArray, $pageUrlFinalArray);
    } else {
      $merge_array = $urlArray;
    }

    $enableSitegroup = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup');
    if ($enableSitegroup) {
      $groupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
      $groupUrlArray = $groupTable->select()->from($groupTable, 'group_url')
                      ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      foreach ($groupUrlArray as $url) {
        $groupUrlFinalArray[] = strtolower($url);
      }
      $merge_array = array_merge($merge_array, $groupUrlFinalArray);
    }

    $enableSitebusiness = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');

    if ($enableSitebusiness) {
      $businessTable = Engine_Api::_()->getDbtable('business', 'sitebusiness');
      $businessUrlArray = $businessTable->select()->from($businessTable, 'business_url')
                      ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      foreach ($businessUrlArray as $url) {
        $businessUrlFinalArray[] = strtolower($url);
      }
      $merge_array = array_merge($merge_array, $businessUrlFinalArray);
    }

    $enableSitestaticpage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestaticpage');
    if ($enableSitestaticpage) {
      $staticpageTable = Engine_Api::_()->getDbtable('pages', 'sitestaticpage');
      $staticpageUrlArray = $staticpageTable->select()->from($staticpageTable, 'page_url')
                      ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
      foreach ($staticpageUrlArray as $url) {
        $staticpageUrlFinalArray[] = strtolower($url);
      }
      $merge_array = array_merge($merge_array, $staticpageUrlFinalArray);
    }

    return $merge_array;
  }

  public function getBannedStoreUrls() {

    $merge_array = array();
    // GET THE ARRAY OF BANNED STOREURLS
    if (!defined('SITESTORE_BANNED_URLS')) {
      $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
      $urlArray = $bannedPageurlsTable->select()->from($bannedPageurlsTable, 'word')
                      ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

      $enableSitepage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');

      if ($enableSitepage) {
        $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $pageUrlArray = $pageTable->select()->from($pageTable, 'page_url')
                        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $merge_array = array_merge($urlArray, $pageUrlArray);
      } else {
        $merge_array = $urlArray;
      }

      $enableSitegroup = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup');

      if ($enableSitegroup) {
        $groupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
        $groupUrlArray = $groupTable->select()->from($groupTable, 'group_url')
                        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $merge_array = array_merge($merge_array, $groupUrlArray);
      }

      $enableSitebusiness = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');

      if ($enableSitebusiness) {
        $businessTable = Engine_Api::_()->getDbtable('business', 'sitebusiness');
        $businessUrlArray = $businessTable->select()->from($businessTable, 'business_url')
                        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $merge_array = array_merge($merge_array, $businessUrlArray);
      }

      $enableSitestaticpage = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestaticpage');

      if ($enableSitestaticpage) {
        $staticpageTable = Engine_Api::_()->getDbtable('pages', 'sitestaticpage');
        $staticpageUrlArray = $staticpageTable->select()->from($staticpageTable, 'page_url')
                        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $merge_array = array_merge($merge_array, $staticpageUrlArray);
      }

      define('SITESTORE_BANNED_URLS', serialize($merge_array));
    }
    return $banneUrlArray = unserialize(SITESTORE_BANNED_URLS);
  }

  public function isLessThan417AlbumModule() {
    $storealbumModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitestorealbum');
    $storealbumModuleVersion = $storealbumModule->version;
    if ($storealbumModuleVersion < '4.1.7') {
      return true;
    } else {
      return false;
    }
  }

  //ACTION FOR LIKES
  public function autoLike($resource_id, $resource_type) {

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GETTING THE VALUE OF RESOURCE ID AND RESOURCE TYPE
// 		$resource_id = $this->_getParam('resource_id');
// 		$resource_type = $this->_getParam('resource_type');

    if (empty($viewer_id)) {
      return;
    }

    $likeTable = Engine_Api::_()->getItemTable('core_like');
    $likeTableName = $likeTable->info('name');
    $sub_status_select = $likeTable->select()
            ->from($likeTableName, new Zend_Db_Expr('COUNT(*)'))
            ->where('resource_type = ?', $resource_type)
            ->where('resource_id = ?', $resource_id)
            ->where('poster_type =?', $viewer->getType())
            ->where('poster_id =?', $viewer_id)
            ->limit(1);
    $like_id = (integer) $sub_status_select->query()->fetchColumn();

    //GET THE VALUE OF LIKE ID
    //$like_id = $this->_getParam('like_id');
    //$status = $this->_getParam('smoothbox', 1);
    //$this->view->status = true;
    //GET LIKES.
    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
    $resource = Engine_Api::_()->getItem($resource_type, $resource_id);

    //CHECK FOR LIKE ID
    if (empty($like_id)) {

      //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
      $like_id_temp = Engine_Api::_()->sitestore()->checkAvailability($resource_type, $resource_id);
      if (empty($like_id_temp[0]['like_id'])) {

        if (!empty($resource)) {
          $like_id = $likeTable->addLike($resource, $viewer);
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
            Engine_Api::_()->sitelike()->setLikeFeed($viewer, $resource);
        }

        $notify_table = Engine_Api::_()->getDbtable('notifications', 'activity');
        $db = $likeTable->getAdapter();
        $db->beginTransaction();
        try {

          //CREATE THE NEW ROW IN TABLE
          if (!empty($getOwnerId) && $getOwnerId != $viewer_id) {

            $notifyData = $notify_table->createRow();
            $notifyData->user_id = $getOwnerId;
            $notifyData->subject_type = $viewer->getType();
            $notifyData->subject_id = $viewer_id;
            $notifyData->object_type = $object_type;
            $notifyData->object_id = $resource_id;
            $notifyData->type = 'liked';
            $notifyData->params = $resource->getShortType();
            $notifyData->date = date('Y-m-d h:i:s', time());
            $notifyData->save();
          }

          //PASS THE LIKE ID.
          $this->view->like_id = $like_id;
          $this->view->error_mess = 0;
          $db->commit();
        } catch (Exception $e) {

          $db->rollBack();
          throw $e;
        }
        $like_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Liked.');
      } else {
        $this->view->like_id = $like_id_temp[0]['like_id'];
        $this->view->error_mess = 1;
      }
    }
// 		else {
// 			if (!empty($resource)) {
// 				$likeTable->removeLike($resource, $viewer);
// 				  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
//           Engine_Api::_()->sitelike()->removeLikeFeed($viewer, $resource);
// 			}
// 			$this->view->error_mess = 0;
// 
// 			$like_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Unliked.');
//     }
//     if (empty($status)) {
//       $this->_forward('success', 'utility', 'core', array(
//               'smoothboxClose' => true,
//               'parentRefresh' => true,
//               'messages' => array($like_msg)
//           )
//       );
//     }
  }

  /**
   * Return categoryid
   *
   * @param string $content_id
   * @param string $widgetname
   * @return categoryid
   */
  public function getSitestoreCategoryid($content_id = null, $widgetname) {

    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    $store_id = $contentTable
            ->select()
            ->from($contentTable->info('name'), array('page_id'))
            ->where('content_id =?', $content_id)
            ->query()
            ->fetchColumn();
    //GET CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    $params = $contentTable
            ->select()
            ->from($contentTable->info('name'), array('params'))
            ->where('page_id =?', $store_id)
            ->where('name =?', $widgetname)
            ->query()
            ->fetchColumn();
    if ($params)
      $params = Zend_Json::decode($params);
    if ($params && isset($params['category_id']) && !empty($params['category_id'])) {
      return $params['category_id'];
    } else {
      return 0;
    }
  }

  //SEND NOTIFICATION TO STORE ADMIN WHEN OWN STORE LIKE AND COMMENT.
  public function itemCommentLike($subject, $notificationType, $baseOnContentOwner = null) {

    $item_title = $subject->getShortType();
    $item_title_url = $subject->getHref();
    $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
    $item_title_link = "<a href='$item_title_baseurl'  >" . $item_title . " </a>";

    //FETCH DATA
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

    $manageAdminsIds = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdmin($subject->store_id, $viewer_id);
    foreach ($manageAdminsIds as $value) {

      $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
      $action_notification = unserialize($value['action_notification']);

      if (!empty($value['notification']) && (in_array('like', $action_notification) || in_array('comment', $action_notification))) {
        $row = $notifyApi->createRow();
        $row->user_id = $user_subject->getIdentity();

        if ($notificationType == 'sitestore_contentcomment') {
          if ($baseOnContentOwner) {
            $row->subject_type = $subject->parent_type;
            $row->subject_id = $subjectParent->getIdentity();
          } else {
            $row->subject_type = $viewer->getType();
            $row->subject_id = $viewer->getIdentity();
          }
        } else {
          $row->subject_type = $viewer->getType();
          $row->subject_id = $viewer->getIdentity();
        }
        $row->type = "$notificationType";
        $row->object_type = $subject->getType();
        $row->object_id = $subject->getIdentity();
        $row->params = '{"eventname":"' . $item_title_link . '"}';
        $row->date = date('Y-m-d H:i:s');
        $row->save();
      }
    }
  }

  public function sendNotificationToFollowers($object, $actionObject, $notificationType) {

    $viewer = Engine_Api::_()->user()->getViewer();
    $store_id = $object->store_id;

    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //ITEM TITLE AND TILTE WITH LINK.
    $item_title = $object->title;
    $item_title_url = $object->getHref();
    $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
    $item_title_link = "<a href='$item_title_baseurl'  >" . $item_title . " </a>";

    $followersIds = Engine_Api::_()->getDbTable('follows', 'seaocore')->getFollowers('sitestore_store', $store_id, $viewer->getIdentity());
    $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $notidicationSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feed.type', 0);

    foreach ($followersIds as $value) {
      $user_subject = Engine_Api::_()->user()->getUser($value['poster_id']);
      $row = $notificationsTable->createRow();
      $row->user_id = $user_subject->getIdentity();
      if (!empty($notidicationSettings)) {
        $row->subject_type = $sitestore->getType();
        $row->subject_id = $sitestore->getIdentity();
      } else {
        $row->subject_type = $viewer->getType();
        $row->subject_id = $viewer->getIdentity();
      }
      $row->type = "$notificationType";
      $row->object_type = $sitestore->getType();
      $row->object_id = $sitestore->getIdentity();
      $row->params = '{"eventname":"' . $item_title_link . '"}';
      $row->date = date('Y-m-d H:i:s');
      $row->save();
    }
  }

  public function allowInThisStore($sitestore, $packagePrivacyName, $levelPrivacyName) {
    if ($this->hasPackageEnable()) {
      if (!$this->allowPackageContent($sitestore->package_id, "modules", $packagePrivacyName)) {
        return false;
      }
    } else {
      $isStoreOwnerAllow = $this->isStoreOwnerAllow($sitestore, $levelPrivacyName);
      if (empty($isStoreOwnerAllow)) {
        return false;
      }
    }
    return true;
  }

  public function sendNotificationEmail($object, $actionObject, $notificationType = null, $emailType = null, $params = null) {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');

    $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $notidicationSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.feed.type', 0);

    $manageAdminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $store_id = $object->store_id;

    $subject = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $owner = $subject->getOwner();

    //previous notification is delete.
    $notificationsTable->delete(array('type =?' => "$notificationType", 'object_type = ?' => "sitestore_store", 'object_id = ?' => $store_id, 'subject_id = ?' => $viewer_id));

    //GET STORE TITLE AND STORE TITLE WITH LINK.
    $storetitle = $subject->title;
    //$store_url = Engine_Api::_()->sitestore()->getStoreUrl($subject->store_id);
    //$store_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true);
    //$store_title_link = '<a href="' . $store_baseurl . '"  >' . $storetitle . ' </a>';
    //ITEM TITLE AND TILTE WITH LINK.
    $item_title = $object->title;
    $item_title_url = $object->getHref();
    $item_title_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $item_title_url;
    $item_title_link = "<a href='$item_title_baseurl' style='text-decoration:none;' >" . $item_title . " </a>";

    //POSTER TITLE AND PHOTO WITH LINK
    $poster_title = $viewer->getTitle();
    $poster_url = $viewer->getHref();
    $poster_baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $poster_url;
    $poster_title_link = "<a href='$poster_baseurl' style='font-weight:bold;text-decoration:none;' >" . $poster_title . " </a>";
    if ($viewer->photo_id) {
      $photo = 'http://' . $_SERVER['HTTP_HOST'] . $viewer->getPhotoUrl('thumb.icon');
    } else {
      $photo = 'http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/application/modules/Sitestore/externals/images/nophoto_user_thumb_icon.png';
    }
    $image = "<img src='$photo' />";
    $posterphoto_link = "<tr><td colspan='2' style='height:20px;'></td></tr><tr></tr><tr><td valign='top' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding-right:15px;text-align:left'><a href='$poster_baseurl'  >" . $image . " </a></td><td valign='top' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;width:100%;text-align:left'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;width:100%'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif'><span style='color:#333333;'>";

    //MEASSGE WITH LINK.
    $post_baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $actionObject->getHref();
    $created = $post = ' ';
    if ($notificationType == 'sitestorealbum_create') {
      $post = $poster_title . ' created a new album in your store: ' . $storetitle;
      $created = ' created the album ';
    } elseif ($notificationType == 'sitestoredocument_create') {
      $post = $poster_title . ' created a new document in your store: ' . $storetitle;
      $created = ' created the document ';
    } elseif ($notificationType == 'sitestoreevent_create') {
      $post = $poster_title . ' created a new event in your store: ' . $storetitle;
      $created = ' created the event ';
    } elseif ($notificationType == 'sitestoremusic_create') {
      $post = $poster_title . ' created a new playlist in your store: ' . $storetitle;
      $created = ' created the music ';
    } elseif ($notificationType == 'sitestorenote_create') {
      $post = $poster_title . ' created a new note in your store: ' . $storetitle;
      $created = ' created the note ';
    } elseif ($notificationType == 'sitestoreoffer_create') {
      $post = $poster_title . ' created a new coupon in your store: ' . $storetitle;
      $created = ' created the coupon ';
    } elseif ($notificationType == 'sitestorepoll_create') {
      $post = $poster_title . ' created a new poll in your store: ' . $storetitle;
      $created = ' created the poll ';
    } elseif ($notificationType == 'sitestorevideo_create') {
      $post = $poster_title . ' posted a new video in your store: ' . $storetitle;
      $created = ' created the video ';
    }

    if ($params == 'Activity Comment' || $params == 'Activity Reply') {
      $post_link = "<a href='$post_baseUrl'  >" . 'post' . " </a>";
      $post_linkformail = "<table cellspacing='0' cellpadding='0' border='0' style='border-collapse:collapse;' width='90%'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:20px;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;' width='100%'><tr><td colspan='2' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;border-bottom:1px solid #dddddd;padding-bottom:5px;'><a style='font-weight:bold;margin-bottom:10px;text-decoration:none;' href='$post_baseUrl'>" . 'post' . "</a></td></tr><tr><td valign='top' style='padding:10px 15px 10px 10px;'><a href='$poster_baseurl'  >" . $image . " </a></td><td valign='top' style='padding-top:10px;font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;width:100%;text-align:left;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;width:100%;'><tr><td style='
font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;text-decoration:none;text-decoration:none;'>" . $poster_title_link . 'post' . $item_title_link . '.' . "</td></tr></table></td></tr></table></td></tr></table>";
    } else {
      $post_link = "<a href='$post_baseUrl'  >" . $post . " </a>";
      $post_linkformail = "<table cellspacing='0' cellpadding='0' border='0' style='border-collapse:collapse;' width='90%'><tr><td style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:20px;background-color:#fff;border-left:none;border-right:none;border-top:none;border-bottom:none;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;' width='100%'><tr><td colspan='2' style='font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;border-bottom:1px solid #dddddd;padding-bottom:5px;'><a style='font-weight:bold;margin-bottom:10px;text-decoration:none;' href='$post_baseUrl'>" . $post . "</a></td></tr><tr><td valign='top' style='padding:10px 15px 10px 10px;'><a href='$poster_baseurl'  >" . $image . " </a></td><td valign='top' style='padding-top:10px;font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;width:100%;text-align:left;'><table cellspacing='0' cellpadding='0' style='border-collapse:collapse;width:100%;'><tr><td style='
font-size:11px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;text-decoration:none;'>" . $poster_title_link . $created . $item_title_link . '.' . "</td></tr></table></td></tr></table></td></tr></table>";
    }

    //FETCH DATA
    $manageAdminsIds = $manageAdminTable->getManageAdmin($store_id, $viewer_id);
    foreach ($manageAdminsIds as $value) {
      $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
      $action_notification = unserialize($value['action_notification']);
      if (!empty($value['notification']) && in_array('created', $action_notification) && empty($sitestorememberEnabled)) {
        $row = $notificationsTable->createRow();
        $row->user_id = $user_subject->getIdentity();
        if (!empty($notidicationSettings)) {
          $row->subject_type = $subject->getType();
          $row->subject_id = $subject->getIdentity();
        } else {
          $row->subject_type = $viewer->getType();
          $row->subject_id = $viewer->getIdentity();
        }
        $row->type = "$notificationType";

        if ($params == 'Activity Comment' || $params == 'Activity Reply') {
          $row->object_type = $actionObject->getType();
          $row->object_id = $actionObject->getIdentity();
          $row->params = '{"eventname":"' . $post_link . '"}';
        } else {
          $row->object_type = $subject->getType();
          $row->object_id = $subject->getIdentity();
          $row->params = '{"eventname":"' . $item_title_link . '"}';
        }

        $row->date = date('Y-m-d H:i:s');
        $row->save();
      }

      if ($params != 'Activity Comment' && $params != 'Activity Reply') {
        if (!empty($value['email'])) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, "$emailType", array(
              'store_title' => $storetitle,
              'item_title' => $item_title,
              'body_content' => $post_linkformail,
          ));
        }
      }
    }

    if ($params != 'Activity Comment' && $params != 'Storeevent Invite' && $params != 'Activity Reply') {
      $object_type = $subject->getType();
      $object_id = $subject->getIdentity();
      $subject_type = $viewer->getType();
      $subject_id = $viewer->getIdentity();
    } elseif ($params == 'Storeevent Invite') {
      $object_type = $object->getType();
      $object_id = $object->getIdentity();
      $subject_type = $viewer->getType();
      $subject_id = $viewer->getIdentity();
    }

    if ($params != 'Activity Comment' && $params != 'Activity Reply') {
      if (!empty($sitestorememberEnabled)) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
        if (!empty($friendId)) {
          $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitestore_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitestore_membership` WHERE (engine4_sitestore_membership.store_id = " . $subject->store_id . ") AND (engine4_sitestore_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitestore_membership.notification = 1 or (engine4_sitestore_membership.notification = 2 and (engine4_sitestore_membership .user_id IN (" . join(",", $friendId) . "))))");
        } else {
          $db->query("INSERT IGNORE INTO `engine4_activity_notifications` (`user_id`, `subject_type`, `subject_id`, `object_type`, `object_id`, `type`,`params`, `date`) SELECT `engine4_sitestore_membership`.`user_id` as `user_id` ,	'" . $subject_type . "' as `subject_type`, " . $subject_id . " as `subject_id`, '" . $object_type . "' as `object_type`, " . $object_id . " as `object_id`, '" . $notificationType . "' as `type`, 'null' as `params`, '" . date('Y-m-d H:i:s') . "' as ` date `  FROM `engine4_sitestore_membership` WHERE (engine4_sitestore_membership.store_id = " . $subject->store_id . ") AND (engine4_sitestore_membership.user_id <> " . $viewer->getIdentity() . ") AND (engine4_sitestore_membership.notification = 1)");
        }
      }
    }
  }

  /**
   * Check If The Attachment Types in Activity Feed Should be Enabled or Not
   */
  public function enableComposer($composerType = null) {
    return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($composerType) && method_exists(Engine_Api::_()->getApi('core', $composerType), 'enableComposer') ? Engine_Api::_()->getApi('core', $composerType)->enableComposer() : false;
  }

  /**
   * Gets widgetized page
   *
   * @return Zend_Db_Table_Select
   */
  public function getMobileWidgetizedStore() {

    if (!Engine_Api::_()->hasModuleBootstrap('sitemobile'))
      return false;

    //GET CORE PAGE TABLE
    $tableNamePage = Engine_Api::_()->getDbtable('pages', 'sitemobile');
    $select = $tableNamePage->select()
            ->from($tableNamePage->info('name'), array('page_id', 'description', 'keywords'))
            ->where('name =?', 'sitestore_index_view')
            ->limit(1);

    return $tableNamePage->fetchRow($select);
  }

  public function showTabsWithoutContent() {
    return Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.show.tabs.without.content', 0);
  }
  
  public function isCommentsAllow($type) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if(!empty($viewer_id)) {
      $commentAllowance = Engine_Api::_()->authorization()->getPermission($viewer->level_id, $type, 'comment');
      if(empty($commentAllowance))
      return false;
      else
        return true; 
    }
    return true;
  }

      public function checkEnableForMobile($moduleName) {
        if (!Engine_Api::_()->hasModuleBootstrap('sitemobile'))
            return false;

        //GET CORE PAGE TABLE
        $modulesSitemobile = Engine_Api::_()->getDbtable('modules', 'sitemobile');
        $enable_mobile = $modulesSitemobile->select()
                ->from($modulesSitemobile->info('name'), array('enable_mobile'))
                ->where('name =?', $moduleName)
                ->where('enable_mobile= ?', 1)
                ->query()
                ->fetchColumn();

        return $enable_mobile;
    }
    
    //FUNCTION TO GET THE MINIMUM SHIPPING COST OF A STORE
    public function getStoreMinShippingCost($store_id){
      $stores = Engine_Api::_()->getDbtable('stores', 'sitestore');
      
      $min_shipping_cost = $stores->select()
                ->from($stores->info('name'), array('min_shipping_cost'))
                ->where('store_id = ?', $store_id)
                ->query()
                ->fetchColumn();

      return $min_shipping_cost;  
    }
    
  /**
   * Check here that show payment link or not
   * $params $store_id : Id of store
   * @return bool $showLink
   * */
  public function canShowCancelLink($store_id) {
    

    if (!Engine_Api::_()->sitestore()->hasPackageEnable())
      return;

    $showLink = false;
    $store = Engine_Api::_()->getItem('sitestore_store', $store_id);

    
    if (!empty($store)) {
      $package = $store->getPackage();

      if (!$package->isFree() && $store->status == "active" && !$package->isOneTime() && !empty($store->approved)) {
        return (bool) true;
      }
    }

    return (bool) $showLink;
  }
public function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }
}
