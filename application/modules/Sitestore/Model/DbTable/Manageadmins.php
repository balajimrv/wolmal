<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Manageadmins.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Manageadmins extends Engine_Db_Table {

  protected $_rowClass = "Sitestore_Model_Manageadmin";

	/**
   * Return manage admins
   *
   * @param int $store_id
   */
  public function getManageAdminUser($store_id) {

    $usertable = Engine_Api::_()->getDbtable('users', 'user');
    $userName = $usertable->info('name');
    $manageName = $this->info('name');
    $manageHistoriesQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from($manageName)
            ->joinleft($userName, $manageName . '.store_id = ' . $userName . '.user_id', array('displayname', 'photo_id'))
            ->where('store_id = ?', $store_id);
    return Zend_Paginator::factory($manageHistoriesQuery);
  }

	/**
   * Return manage admin ids
   *
   * @param int $store_id
   */
	public function getManageAdmin($store_id, $user_id = null) {

    $select = $this->select()
                    ->from($this->info('name'))
                    ->where('store_id = ?', $store_id);
    if (!empty($user_id)) {
			$select->where('user_id <> ?', $user_id);
    }
    return $this->fetchAll($select);
	}

	/**
   * Return manage admin stores_id
   *
   * @param int $viewer_id
   */
	public function getManageAdminStores($viewer_id) {

		$select = $this->select()
						->from($this->info('name'), 'store_id')
						->where('user_id = ?', $viewer_id);
		return $this->fetchAll($select);
	}

	/**
   * Return linked stores result
   *
   * @param int $store_id
   */
  public function linkedStores($store_id) {

    $manageAdminName = $this->info('name');
    $select = $this->select()->from($manageAdminName, 'user_id')->where('store_id = ?', $store_id);
    return $user_idarray = $this->fetchAll($select)->toArray();
	}

	/**
   * Return featured manage admins
   *
   * @param int $store_id
   */
	public function featuredAdmins($store_id) {

		$select = $this->select()
						->where('featured = ?', 1)
						->where('store_id = ?', $store_id);

		if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      return Zend_Paginator::factory($select);
    }

		return $this->fetchAll($select);
	}

	/**
   * Return store admin.
   *
   * @param int $user_id
   * @param int $store_id
   */
	public function isStoreAdmins($user_id, $store_id) {

    $select = $this->select()
						->from($this->info('name'))
						->where('user_id = ?', $user_id)
						->where('store_id = ?', $store_id);
    return $this->fetchRow($select);
	}
	
	/**
   * Return manage admin ids array
   *
   * @param int $store_id
   */
	public function getManageAdminIds($store_id, $param = null) {

		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->select()
							->from($this->info('name'), array('user_id'));
		if(!empty($param) && $param == 'storeintergration') { 
			$select->where('user_id <> ?', $viewer_id);
		}
		$user_ids = $select->where('store_id = ?', $store_id)
											->query()
											->fetchAll(Zend_Db::FETCH_COLUMN);
    return $user_ids;
	}
	  
  public function getCountUserAsAdmin($params = array()) {
 
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storeTable->info('name');
    
    $manageAdminTableName = $this->info('name');

    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($manageAdminTableName, array("COUNT(engine4_sitestore_manageadmins.store_id) AS adminCount"))
                    ->joinLeft($storeTableName, "$storeTableName.store_id = $manageAdminTableName.store_id", array())
       ->where("$manageAdminTableName.user_id = ?", $viewer_id)
       ->where("$storeTableName.owner_id != ?", $viewer_id)     
       ->group("$storeTableName.store_id")     
       ;   

    return $select->query()->fetchColumn();
  }
  
	public function getManageAdminAttribs($attrib, $params = array()) {

		$select = $this->select()
						->from($this->info('name'), $attrib);
    
    if( isset($params['store_id']) && !empty($params['store_id']) ) {
			$select->where('store_id = ?', $params['store_id']);
    }
    
    if( isset($params['user_id']) && !empty($params['user_id']) ) {
			$select->where('user_id = ?', $params['user_id']);
    }
    
    if( isset($params['limit']) && !empty($params['limit']) ) {
			$select->limit($params['limit']);
    }
		
    if( isset($params['fetch_column']) && !empty($params['fetch_column']) ) {
      return $select->query()->fetchColumn();
    }
	}
}
?>