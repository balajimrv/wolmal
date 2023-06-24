<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Stores.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Stores extends Engine_Db_Table {
 protected $_rowClass = "Sitestore_Model_Store";
  protected $_name = 'sitestore_stores';

//  protected $_rowClass = "Sitestore_Model_Store";

  /**
   * Get stores to add as item of the day
   * @param string $title : search text
   * @param int $limit : result limit
   */
  public function getDayItems($title, $limit=10,$category_id = null) {

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('store_id', 'owner_id', 'title', 'photo_id'));
            
    if(!empty($category_id)) {
       $select->where('category_id = ?',$category_id);
    }

    $select->where($this->info('name') . ".title LIKE ? OR " . $this->info('name') . ".location LIKE ? ", '%' . $title . '%')
            ->where('closed = ?', '0')
            ->where('declined = ?', '0')
            ->where('approved = ?', '1')
            ->where('draft = ?', '1')
            ->order('title ASC')
            ->limit($limit);

    //FETCH RESULTS
    return $this->fetchAll($select);
  }

  public function getStoresSelectSql($params=array()) {
    $tableName = $this->info('name');
    $select = $this->select()
            ->where("{$tableName}.search = ?", 1)
            ->where("{$tableName}.closed = ?", '0')
            ->where("{$tableName}.approved = ?", '1')
            ->where("{$tableName}.declined = ?", '0')
            ->where("{$tableName}.draft = ?", '1');
    if (Engine_Api::_()->sitestore()->hasPackageEnable())
      $select->where("{$tableName}.expiration_date  > ?", date("Y-m-d H:i:s"));

    if (isset($params['limit']) && !empty($params['limit']))
      $select->limit($params['limit']);
    return $select;
  }

  public function getTagCloud($limit=100, $category_id, $count_only = 0) {

    $tableTagmaps = 'engine4_core_tagmaps';
    $tableTags = 'engine4_core_tags';

    $tableSitestores = $this->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($tableSitestores, 'title')
            ->joinInner($tableTagmaps, "$tableSitestores.store_id = $tableTagmaps.resource_id", array('COUNT(engine4_core_tagmaps.resource_id) AS Frequency'))
            ->joinInner($tableTags, "$tableTags.tag_id = $tableTagmaps.tag_id", array('text', 'tag_id'))
            ->where($tableSitestores . '.approved = ?', "1");
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.status.show', 0);

    if ($stusShow == 0) {
      $select = $select->where($tableSitestores . '.closed = ?', "0");
    }

    if (Engine_Api::_()->sitestore()->hasPackageEnable())
      $select->where($tableSitestores . '.expiration_date  > ?', date("Y-m-d H:i:s"));

    $select->where($tableSitestores . ".search = ?", 1);
    $select = $select->where($tableSitestores . '.draft = ?', "1")
            ->where($tableSitestores . '.declined = ?', '0')
            ->where($tableTagmaps . '.resource_type = ?', 'sitestore_store')
            ->group("$tableTags.text")
            ->order("Frequency DESC");

    if (!empty($category_id)) {
			$select = $select->where($tableSitestores . '.	category_id =?', $category_id);
    }

    //Start Network work
    $select = $this->getNetworkBaseSql($select);
    //End Network work

		if(!empty($count_only)) {
			$total_results = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			return Count($total_results);
		}

		$select = $select->limit($limit);

    return $select->query()->fetchAll();
  }

  /**
   * Return stores which have this category and this mapping
   *
   * @param int category_id
   * @param int profile_type
   * @return Zend_Db_Table_Select
   */
  public function getCategoryStore($category_id, $profile_type) {
    $select = $this->select()
            ->from($this->info('name'), 'store_id')
            ->where('category_id = ?', $category_id)
            ->where('profile_type != ?', $profile_type);
    return $this->fetchAll($select)->toArray();
  }

  /**
   * Return stores which can user have to choice to claim
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
  public function getSuggestClaimStore($params) {
    //SELECT
    $select = $this->select()
            ->from($this->info('name'), array('store_id', 'title', 'userclaim', 'photo_id', 'owner_id'))
            ->where('approved = ?', '1')
            ->where('declined = ?', '0')
            ->where('draft = ?', '1');

    if (isset($params['store_id']) && !empty($params['store_id'])) {
      $select = $select->where('store_id = ?', $params['store_id']);
    }

    if (isset($params['viewer_id']) && !empty($params['viewer_id'])) {
      $select = $select->where('owner_id != ?', $params['viewer_id']);
    }

    if (isset($params['title']) && !empty($params['title'])) {
      $select = $select->where('title LIKE ? ', '%' . $params['title'] . '%');
    }

    if (isset($params['limit']) && !empty($params['limit'])) {
      $select = $select->limit($params['limit']);
    }

    if (isset($params['orderby']) && !empty($params['orderby'])) {
      $select = $select->order($params['orderby']);
    }
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.status.show', 0);
    if ($stusShow == 0) {
      $select = $select
              ->where('closed = ?', '0');
    }
    if (Engine_Api::_()->sitestore()->hasPackageEnable())
      $select->where('expiration_date  > ?', date("Y-m-d H:i:s"));

    //FETCH
    return $this->fetchAll($select);
  }

  /**
   * Get Popular location base on city and state
   *
   */
  public function getPopularLocation($items_count,$category_id) {
    $limit = $items_count;
    $storeName = $this->info('name');
    $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestore');
    $locationName = $locationTable->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($storeName, null)
            ->join($locationName, "$storeName.store_id = $locationName.store_id", array("city", "count(city) as count_location", "state", "count(state) as count_location_state"))
            ->group("city")
            ->group("state")
            ->order("count_location DESC")
            ->limit($limit);
    if (!empty($category_id)) {
			$select = $select->where($storeName . '.	category_id =?', $category_id);
    }
    $select->where($storeName . '.approved = ?', '1')
            ->where($storeName . '.declined = ?', '0')
            ->where($storeName . '.draft = ?', '1');
    $select->where($storeName . ".search = ?", 1);
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.status.show', 0);
    if ($stusShow == 0) {
      $select = $select
              ->where($storeName . '.closed = ?', '0');
    }
    if (Engine_Api::_()->sitestore()->hasPackageEnable())
      $select->where($storeName . '.expiration_date  > ?', date("Y-m-d H:i:s"));

    //Start Network work
    $select = $this->getNetworkBaseSql($select, array('not_groupBy' => 1));
    //End Network work

    return $this->fetchAll($select);
  }

  /**
   * Get Arcive Stores
   *
   * @param int $user_id
   * @return object
   */
  public function getArchiveSitestore($user_id = null) {

    $rName = $this->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($rName)
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.declined = ?', '0')
            ->where($rName . '.draft = ?', '1');
    $select->where($rName . ".search = ?", 1);
    
    if (Engine_Api::_()->sitestore()->hasPackageEnable())
      $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));;

    if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
      $select->where($rName . '.owner_id = ?', $params['user_id']);
    }

    //Start Network work
    $select = $this->getNetworkBaseSql($select);
    //END NETWORK WORK
    return $this->fetchAll($select);
  }

  /**
   * Get Stores relative to store owner
   *
   * @param int $store_id
   * @param int $owner_id
   * @return objects
   */
  public function userStore($owner_id, $store_id, $params = array(), $popularity) {

    $rName = $this->info('name');
    $select = $this->select()
            ->where($rName . '.closed = ?', '0')
            ->where($rName . '.approved = ?', '1')
            ->where($rName . '.declined = ?', '0')
            ->where($rName . '.draft = ?', '1')
            ->where($rName . ".search = ?", 1)
            ->where($rName . '.store_id <> ?', $store_id)
            ->where($rName . '.owner_id = ?', $owner_id);  
    
    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select = $select->where($rName . '.	category_id =?', $params['category_id']);
    }
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
    if (Engine_Api::_()->sitestore()->hasPackageEnable())
      $select->where($rName . '.expiration_date  > ?', date("Y-m-d H:i:s"));

    $select->order("$popularity DESC")
            ->order("creation_date DESC");
    $limit = $params['totalstores'];

    //Start Network work
    $select = $this->getNetworkBaseSql($select, array('setIntegrity' => 1));
    //End Network work
    $select = $select->limit($limit);

    return $this->fetchALL($select);
  }

  /**
   * Get Stores for links
   *
   * @param int $store_id
   * @param int $viewer_id
   * @return objects
   */
  public function getStores($store_id, $viewer_id) {

    $sitestoreName = $this->info('name');
    $select = $this->select()
            ->where($sitestoreName . '.store_id <> ?', $store_id)
            ->where($sitestoreName . '.owner_id  =?', $viewer_id)
            ->where('NOT EXISTS (SELECT `store_id` FROM `engine4_sitestore_favourites` WHERE `store_id_for`=' . $store_id . ' AND `store_id` = ' . $sitestoreName . '.`store_id`) ');

    return $this->fetchALL($select)->toArray();
  }

  public function sitestoreselect($store_id) {
    $sitestoreselect = $this->select()->where('store_id =?', $store_id);
    return $userStores = $this->fetchALL($sitestoreselect);
  }

  /**
   * Get Discussed Stores
   *
   * @return all discussed stores
   */
  public function getDiscussedStore($params = array()) {
    $sitestore_tableName = $this->info('name');
    $topic_tableName = Engine_Api::_()->getDbTable('topics', 'sitestore')->info('name');
    $select = $this->select()->setIntegrityCheck(false)
            ->from($sitestore_tableName)
            ->join($topic_tableName, $topic_tableName . '.store_id = ' . $sitestore_tableName . '.store_id', array('count(*) as counttopics', '(sum(post_count) - count(*) ) as total_count'))
            ->where($sitestore_tableName . '.closed = ?', '0')
            ->where($sitestore_tableName . '.approved = ?', '1')
            ->where($sitestore_tableName . '.draft = ?', '1')
            ->where($topic_tableName . '.post_count > ?', '1')
            ->group($topic_tableName . '.store_id');
    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select = $select->where($sitestore_tableName . '.	category_id =?', $params['category_id']);
    }
    if (isset($params['featured']) && ($params['featured'] == '1')) {
      $select = $select->where($sitestore_tableName . '.	featured =?', '0');
    } elseif (isset($params['featured']) && ($params['featured'] == '2')) {
      $select = $select->where($sitestore_tableName . '.	featured =?', '1');
    }

    if (isset($params['sponsored']) && ($params['sponsored'] == '1')) {
      $select = $select->where($sitestore_tableName . '.	sponsored =?', '0');
    } elseif (isset($params['sponsored']) && ($params['sponsored'] == '2')) {
      $select = $select->where($sitestore_tableName . '.	sponsored =?', '1');
    }
    $select->order('total_count DESC')
            ->order('counttopics DESC')
            ->limit($params['totalstores']);
    if (Engine_Api::_()->sitestore()->hasPackageEnable())
      $select->where($sitestore_tableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    $select->where($sitestore_tableName . ".search = ?", 1);

    //START NETWORK WORK
    $select = $this->getNetworkBaseSql($select);
    //END NETWORK WORK
    return $this->fetchALL($select);
  }

  /**
   * Get stores based on category
   * @param string $title : search text
   * @param int $category_id : category id
   * @param char $popularity : result sorting based on views, reviews, likes, comments
   * @param char $interval : time interval
   * @param string $sqlTimeStr : Time durating string for where clause 
   */
  public function storesByCategory($category_id, $popularity, $interval, $sqlTimeStr, $totalStores) {
    $groupBy = 1; 
    $storeTableName = $this->info('name');

    if ($interval == 'overall' || $popularity == 'view_count') {
      $groupBy = 0;
      $select = $this->select()
              ->from($storeTableName, array('store_id', 'title', 'photo_id', 'store_url', 'owner_id', "$popularity AS populirityCount"))
              ->where($storeTableName . '.category_id = ?', $category_id)
              ->where($storeTableName . '.closed = ?', '0')
              ->where($storeTableName . '.approved = ?', '1')
              ->where($storeTableName . '.declined = ?', '0')
              ->where($storeTableName . '.draft = ?', '1')
              ->order("$popularity DESC")
              ->order("creation_date DESC")
              ->limit($totalStores);
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        $select->where($storeTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    } elseif ($popularity == 'review_count' && $interval != 'overall') {

      $popularityTable = Engine_Api::_()->getDbtable('reviews', 'sitestorereview');
      $popularityTableName = $popularityTable->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($storeTableName, array('store_id', 'title', 'photo_id', 'store_url', 'owner_id', "$popularity AS populirityCount"))
              ->joinLeft($popularityTableName, $popularityTableName . '.store_id = ' . $storeTableName . '.store_id', array("COUNT(review_id) as total_count"))
              ->where($storeTableName . '.category_id = ?', $category_id)
              ->where($storeTableName . '.closed = ?', '0')
              ->where($storeTableName . '.approved = ?', '1')
              ->where($storeTableName . '.declined = ?', '0')
              ->where($storeTableName . '.draft = ?', '1')
              ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
              ->group($storeTableName . '.store_id')
              ->order("total_count DESC")
              ->order($storeTableName . ".creation_date DESC")
              ->limit($totalStores);
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        $select->where($storeTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    } elseif ($popularity == 'member_count' && $interval != 'overall') {

      $popularityTable = Engine_Api::_()->getDbtable('membership', 'sitestore');
      $popularityTableName = $popularityTable->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($storeTableName, array('store_id', 'title', 'photo_id', 'store_url', 'owner_id', "$popularity AS populirityCount"))
              ->joinLeft($popularityTableName, $popularityTableName . '.store_id = ' . $storeTableName . '.store_id', array("COUNT(member_id) as total_count"))
              ->where($storeTableName . '.category_id = ?', $category_id)
              ->where($storeTableName . '.closed = ?', '0')
              ->where($storeTableName . '.approved = ?', '1')
              ->where($storeTableName . '.declined = ?', '0')
              ->where($storeTableName . '.draft = ?', '1')
              ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
              ->group($storeTableName . '.store_id')
              ->order("total_count DESC")
              ->order($storeTableName . ".creation_date DESC")
              ->limit($totalStores);
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        $select->where($storeTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    }
    elseif ($popularity != 'view_count' && $popularity != 'review_count' && $popularity != 'member_count' && $interval != 'overall') {

      if ($popularity == 'like_count') {
        $popularityType = 'like';
      } else {
        $popularityType = 'comment';
      }

      $id = $popularityType . "_id";

      $popularityTable = Engine_Api::_()->getDbtable("$popularityType" . "s", 'core');
      $popularityTableName = $popularityTable->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($storeTableName, array('store_id', 'title', 'photo_id', 'store_url', 'owner_id', "$popularity AS populirityCount"))
              ->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $storeTableName . '.store_id', array("COUNT($id) as total_count"))
              ->where($storeTableName . '.category_id = ?', $category_id)
              ->where($storeTableName . '.closed = ?', '0')
              ->where($storeTableName . '.approved = ?', '1')
              ->where($storeTableName . '.declined = ?', '0')
              ->where($storeTableName . '.draft = ?', '1')
              ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
              ->group($storeTableName . '.store_id')
              ->order("total_count DESC")
              ->order($storeTableName . ".creation_date DESC")
              ->limit($totalStores);
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        $select->where($storeTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }
    }
    //Start Network work
    $select = $this->getNetworkBaseSql($select, array('not_groupBy' => $groupBy));
    //End Network work
    
    return $this->fetchAll($select);
  }

  public function getItemOfDay() {

    //$sitestoreTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $sitestoreTableName = $this->info('name');

    $itemofthedaytable = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore');
    $itemofthedayName = $itemofthedaytable->info('name');

    $select = $this->select();
    $select = $select->setIntegrityCheck(false)
            ->from($sitestoreTableName)
            ->join($itemofthedayName, $sitestoreTableName . ".store_id = " . $itemofthedayName . '.resource_id', array('start_date'))
            ->where($sitestoreTableName . '.closed = ?', '0')
            ->where($sitestoreTableName . '.declined = ?', '0')
            ->where($sitestoreTableName . '.approved = ?', '1')
            ->where($sitestoreTableName . '.draft = ?', '1')
            ->where($itemofthedayName . '.resource_type=?', 'sitestore_store')
            ->where($itemofthedayName . '.start_date <=?', date('Y-m-d'))
            ->where($itemofthedayName . '.end_date >=?', date('Y-m-d'))
            ->order('RAND()');
    return $this->fetchRow($select);
  }

  public function getNetworkBaseSql($select, $params=array()) {
    if (empty($select))
      return;
    $sitestore_tableName = $this->info('name');
    //START NETWORK WORK
    $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.network', 0);
    if (!empty($enableNetwork) || (isset($params['browse_network']) && !empty($params['browse_network']))) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      if (!Engine_Api::_()->getApi('subCore', 'sitestore')->storeBaseNetworkEnable()) {
        $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer->getIdentity()));

        if (!empty($viewerNetwork)) {
          if (isset($params['setIntegrity']) && !empty($params['setIntegrity'])) {
            $select->setIntegrityCheck(false)
                    ->from($sitestore_tableName);
          }
          $networkMembershipName = $networkMembershipTable->info('name');
          $select
                  ->join($networkMembershipName, "`{$sitestore_tableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                  ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
                  ->where("`{$networkMembershipName}_2`.user_id = ? ", $viewer->getIdentity());
          if (!isset($params['not_groupBy']) || empty($params['not_groupBy'])) {
            $select->group($sitestore_tableName . ".store_id");
          }
          if (isset($params['extension_group']) && !empty($params['extension_group'])) {
            $select->group($params['extension_group']);
          }
        }
      } else {
        $viewerNetwork = $networkMembershipTable->getMembershipsOfInfo($viewer);
        $str = array();
        $columnName = "`{$sitestore_tableName}`.networks_privacy";
        foreach ($viewerNetwork as $networkvalue) {
          $network_id = $networkvalue->resource_id;
          $str[] = "'" . $network_id . "'";
          $str[] = "'" . $network_id . ",%'";
          $str[] = "'%," . $network_id . ",%'";
          $str[] = "'%," . $network_id . "'";
        }
        if (!empty($str)) {
          $likeNetworkVale = (string) ( join(" or $columnName  LIKE ", $str) );
          $select->where($columnName . ' LIKE ' . $likeNetworkVale . ' or ' . $columnName . " IS NULL");
        } else {
          $select->where($columnName . " IS NULL");
        }
      }
    }
    //END NETWORK WORK
    return $select;
  }

  public function countUserStores($user_id) {
    $count = 0;
    $select = $this
            ->select()
            ->from($this->info('name'), array('count(*) as count'))
            ->where("owner_id = ?", $user_id);

    return $select->query()->fetchColumn();
  }

  /**
   * Return store is existing or not.
   *
   * @return Zend_Db_Table_Select
   */
  public function checkStore() {

    //MAKE QUERY
    $hasStore = $this->select()
                    ->from($this->info('name'), array('store_id'))
                    ->query()
                    ->fetchColumn();

    //RETURN RESULTS
    return $hasStore;
  }
  
  // get lising according to requerment
  public function getListing($sitestoretype = '', $params = array()) {

    $limit = 10;
    $table = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $sitestoreTableName = $table->info('name');
    $coreTable = Engine_Api::_()->getDbtable('likes', 'core');
    $coreName = $coreTable->info('name');
    $select = $table->select()
            ->where($sitestoreTableName . '.closed = ?', '0')
            ->where($sitestoreTableName . '.approved = ?', '1')
            ->where($sitestoreTableName . '.draft = ?', '1')
            ->where($sitestoreTableName . ".search = ?", 1);

    //$select = $this->expirySQL($select);   

    if (isset($params['store_id']) && !empty($params['store_id'])) {
      $select->where($sitestoreTableName . '.store_id != ?', $params['store_id']);
    }

    if (isset($params['category_id']) && !empty($params['category_id'])) {
      $select->where($sitestoreTableName . '.category_id = ?', $params['category_id']);
    }

    if (isset($params['subcategory_id']) && !empty($params['subcategory_id'])) {
      $select->where($sitestoreTableName . '.subcategory_id = ?', $params['subcategory_id']);
    }

    if (isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id'])) {
      $select->where($sitestoreTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
    }

    if (isset($params['popularity']) && !empty($params['popularity'])) {
      $select->order($params['popularity'] . " DESC");
    }

    if (isset($params['featured']) && !empty($params['featured']) || $sitestoretype == 'featured') {
      $select->where("$sitestoreTableName.featured = ?", 1);
    }

    if (isset($params['sponsored']) && !empty($params['sponsored']) || $sitestoretype == 'sponsored') {
      $select = $select->where($sitestoreTableName . '.sponsored = ?', '1');
    }

    //Start Network work
    $select = $table->getNetworkBaseSql($select, array('setIntegrity' => 1));
    //End Network work

    if ($sitestoretype == 'most_popular') {
      $select = $select->order($sitestoreTableName . '.view_count DESC');
    }

    if ($sitestoretype == 'most_reviews' || $sitestoretype == 'most_reviewed') {
      $select = $select->order($sitestoreTableName . '.review_count DESC');
    }

    if(isset($params['similar_items_order']) && !empty($params['similar_items_order'])) {
      if(isset($params['ratingType']) && !empty($params['ratingType']) && $params['ratingType'] != 'rating_both') {
        $ratingType = $params['ratingType'];
        $select->order($sitestoreTableName . ".$ratingType DESC");
      }
      else {
        $select->order($sitestoreTableName . '.rating_avg DESC');
      }
      $select->order('RAND()');
      
    }else {
      $select->order($sitestoreTableName . '.store_id DESC');
    }

    if (isset($params['limit']) && !empty($params['limit'])) {
      $limit = $params['limit'];
    }

    $select->group($sitestoreTableName . '.store_id');

    if (isset($params['start_index']) && $params['start_index'] >= 0) {
      $select = $select->limit($limit, $params['start_index']);
      return $table->fetchAll($select);
    } else {

      $paginator = Zend_Paginator::factory($select);
      if (!empty($params['store'])) {
        $paginator->setCurrentPageNumber($params['store']);
      }

      if (!empty($params['limit'])) {
        $paginator->setItemCountPerPage($limit);
      }

      return $paginator;
    }
  }  

 /**
   * Return Location Base Stores
   * 
   * @return $stores
   */
  public function getLocationBaseContents($params=array()) {

    if(empty($params['search']))  
    return;
    $limit = 5;
    if (isset($params['limit']) && !empty($params['limit'])) {
      $limit = $params['limit'];
    } else {
      $limit = 5;
    }
    $select = $this->getStoresSelectSql(array("limit" => $limit));
    //Start Network work
    $select = $this->getNetworkBaseSql($select);
    //End Network work
    $storeName = $this->info('name');
    $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestore');
    $locationName = $locationTable->info('name');
    $select
            ->setIntegrityCheck(false)
            ->from($storeName, array('title', 'store_id', 'location', 'photo_id', 'category_id'))
            ->join($locationName, "$storeName.store_id = $locationName.store_id", array("latitude", "longitude", "formatted_address"));

    if (isset($params['search']) && !empty($params['search'])) {
			$select->where("`{$storeName}`.title LIKE ? or `{$storeName}`.location LIKE ? or `{$locationName}`.city LIKE ?", "%" . $params['search'] . "%");
    }
    
    if (isset($params['resource_id']) && !empty($params['resource_id'])) {
			$select->where($locationName . '.store_id not in (?)', new Zend_Db_Expr(trim($params['resource_id'], ',')));
    }

    $select->order('creation_date DESC');

    return $this->fetchAll($select);
  }

  /* Return Location Base Stores
   * 
   * @return $stores
   */
  public function getPreviousLocationBaseContents($params=array()) {

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->getStoresSelectSql(array("limit" => 5));

    //START NETWORK WORK
    $select = $this->getNetworkBaseSql($select);
    //END NETWORK WORK
   
    //GET STORE TABLE NAME
    $storeTableName = $this->info('name');

    //LOCATION TABLE NAME
    $locationTableName = Engine_Api::_()->getDbtable('locations', 'sitestore')->info('name');

    //GET ADD LOCATION TABLE NAME
    $addlocationsTableName =  Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin')->info('name');

    $select =  $select
            ->setIntegrityCheck(false)
            ->from($storeTableName, array('title', 'store_id', 'location', 'photo_id', 'category_id'))
            ->join($addlocationsTableName, "$addlocationsTableName.object_id = $storeTableName.store_id", null)
            ->join($locationTableName, "$locationTableName.location_id = $addlocationsTableName.location_id", array("latitude", "longitude", "formatted_address"))
            ->where("$addlocationsTableName.object_type =?", "sitestore_store")
            ->where("$addlocationsTableName.owner_id =?", $viewer_id)
            ->group("$addlocationsTableName.object_id")
            ->order("$storeTableName.creation_date DESC");

    return $this->fetchAll($select);
  }
  
//    /**
//    * Return Count Location Base Stores
//    * 
//    * @return $stores
//    */
// 	public function getLocationCount() {
// 	
// 	  $storeTableName = $this->info('name');
// 		$select = $this->select()->from($storeTableName, array('location'))
// 						->where($storeTableName . '.approved = 1')
// 						->where($storeTableName . '.draft = ?', '1')
// 						->where($storeTableName . '.location != ?', '')
// 						->where($storeTableName . '.closed = ?', '0');
// 		return $select->query()->fetchColumn();
// 	}

  /**
   * Return stores which have this category and this mapping
   *
   * @param int category_id
   * @return Zend_Db_Table_Select
   */
  public function getCategorySitestore($category_id) {

    //RETURN IF CATEGORY ID IS NULL
    if (empty($category_id)) {
      return null;
    }

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), 'store_id')
            ->where('category_id = ?', $category_id);

    //GET DATA
    $categoryData = $this->fetchAll($select);

    if (!empty($categoryData)) {
      return $categoryData->toArray();
    }

    return null;
  }
  
  public function getStoreName($store_id)
  {
    $select = $this->select()
            ->from($this->info('name'), 'title')
            ->where('store_id = ?', $store_id);
    return $select->query()->fetchColumn();
  }
  
  public function getStoreId($owner_id)
  {
    $select = $this->select()
            ->from($this->info('name'), 'store_id')
            ->where('owner_id = ?', $owner_id);
    return $select->query()->fetchAll();
  }
  
  public function getsubStoreids($store_id)	{
  
    $select = $this->select()
            ->from($this->info('name'), 'store_id')
            ->where('substore = ?', '1')
            ->where('parent_id = ?', $store_id);
    return $select->query()->fetchAll();
  }
  
  public function getStoreObject($store_id)
  {
    $select = $this->select()
            ->from($this->info('name'), array('store_id', 'title', 'store_url'))
            ->where("store_id IN ($store_id)");
    return $this->fetchAll($select);
  }
  
  public function getStoresCount()
  {
    $select = $this->select()
            ->from($this->info('name'), array('COUNT(store_id)'));

    return $select->query()->fetchColumn();
  }
  
  public function getLikeCounts($params = array()) {
    
		//GETTING THE CURRENT USER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $coreLikeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $coreLikeTableName = $coreLikeTable->info( 'name' ) ;
    $moduleTable = Engine_Api::_()->getItemTable( 'sitestore_store' ) ;
    $moduleTableName = $moduleTable->info( 'name' ) ;

    $like_select = $moduleTable->select()
            ->setIntegrityCheck( false )
						->from( $coreLikeTableName, null)
						->join($moduleTableName, "$coreLikeTableName.resource_id = $moduleTableName.store_id", array("COUNT(store_id) as likeCount"))
						->where( $coreLikeTableName . '.resource_type = ?' , 'sitestore_store' )
						->where($moduleTableName . '.approved = ?', '1')
						->where($moduleTableName . '.declined = ?', '0')
						->where($moduleTableName . '.draft = ?', '1')
						->where($moduleTableName . ".search = ?", 1)
						->where( $coreLikeTableName . '.poster_id = ?' , $user_id );
		if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.status.show', 0) == 0) {
			$like_select = $like_select->where($moduleTableName . '.closed = ?', '0');
		}
    
    return $like_select->query()->fetchColumn();    
  }
  
  /**
   * Get store attribute
   * @param int $store_id
   * 
   * @return store attribute
   */
  public function getStoreAttribute($store_id, $attributName)
  {
    $select = $this->select()->from($this->info('name'), $attributName)->where('store_id = ?', $store_id);
    return $select->query()->fetchColumn();
  }
  
  public function storeInformation($owner_id, $limit, $currentStoreId, $currentsStoreIdProduct) {
    $select = $this->select();

    $storeTableName = $this->info('name');

    $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
    $orderTableName = $orderTable->info('name');
    $select
            ->setIntegrityCheck(false)
            ->from($storeTableName//, array("$storeTableName.store_id", "$storeTableName.store_url", "$storeTableName.like_count"))
            )
            ->joinLeft($orderTableName, "$storeTableName.store_id = $orderTableName.store_id", array("SUM(item_count) as order_count"));
    $select->where("$storeTableName.owner_id =?", $owner_id);
    $select->group("$storeTableName.store_id");
    
     if (isset($currentStoreId) && !empty($currentStoreId)) {
       $select->where("$storeTableName.store_id !=?", $currentStoreId);
    }
    if (isset($currentsStoreIdProduct) && !empty($currentsStoreIdProduct)) {
       $select->where("$storeTableName.store_id =?", $currentsStoreIdProduct);
    }
     if (isset($limit) && !empty($limit)) {
       $select->limit($limit);
    }
    return $this->fetchAll($select);
  }

  /*    
  public function getLikeCounts($params = array()) {
    
		//GETTING THE CURRENT USER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $coreLikeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $coreLikeTableName = $coreLikeTable->info( 'name' ) ;
    $moduleTable = Engine_Api::_()->getItemTable( 'sitestore_store' ) ;
    $moduleTableName = $moduleTable->info( 'name' ) ;

    $like_select = $moduleTable->select()
            ->setIntegrityCheck( false )
						->from( $coreLikeTableName, null)
						->join($moduleTableName, "$coreLikeTableName.resource_id = $moduleTableName.store_id", array("COUNT(store_id) as likeCount"))
						->where( $coreLikeTableName . '.resource_type = ?' , 'sitestore_store' )
						->where($moduleTableName . '.approved = ?', '1')
						->where($moduleTableName . '.declined = ?', '0')
						->where($moduleTableName . '.draft = ?', '1')
						->where($moduleTableName . ".search = ?", 1)
						->where( $coreLikeTableName . '.poster_id = ?' , $user_id );
		if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.status.show', 0) == 0) {
			$like_select = $like_select->where($moduleTableName . '.closed = ?', '0');
		}
    
    return $like_select->query()->fetchColumn();    
  }*/

  public function toggleStoreProductsStatus($store_id, $enable) {
    
    if (!empty($store_id)) {
      $productObj = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductsByStore($store_id);
      foreach ($productObj as $product) {
        $product->approved = $enable;
        $product->save();
      }
    }
  }
  
  public function countOwnerStores($owner_id) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $storeTableName = $this->info('name');
    $select = $this
            ->select()
            ->from($storeTableName, array('count(*) as count'))
            ->where('owner_id = ?', $owner_id);

    if($viewer_id != $owner_id) {
      $select->where('closed = ?', 0)
              ->where('approved = ?', 1)
              ->where('declined = ?', 0)
              ->where('draft = ?', 1)
              ->where('search = ?', 1);
    }

    return $select->query()->fetchColumn();
  }
  
  //FUNCTION TO SET THE MINIMUM SHIPPING COST
  public function setMinShippingCost($store_id, $min_shipping_cost){
    if(!empty($store_id))
      $this->update(array('min_shipping_cost' => $min_shipping_cost), array('store_id = ?' => $store_id));
  }
  
}
