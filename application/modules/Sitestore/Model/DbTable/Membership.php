<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Membership.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Membership extends Core_Model_DbTable_Membership {

  protected $_type = 'sitestore_store';

   /**
   * Get storemembers list
   *
   * @param array $params
   * @param string $request_count
   * @return array $paginator;
   */
  public function getSitestoremembersPaginator($params = array(), $request_count = null) {
 
    $paginator = Zend_Paginator::factory($this->getsitestoremembersSelect($params, $request_count));
    if (!empty($params['store'])) {
      $paginator->setCurrentPageNumber($params['store']);
    }
    
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
     
    return $paginator;
  }

  /**
   * Get store member select query
   *
   * @param array $params
   * @param string $request_count
   * @return string $select;
   */
  public function getsitestoremembersSelect($params = array(), $request_count = null) {

    $membershipTableName = $this->info('name');

    $usersTable = Engine_Api::_()->getDbtable('users', 'user');
    $usersTableName = $usersTable->info('name');

    $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storeTable->info('name');

    $storemanageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
    $storemanageadminsTableName = $storemanageadminsTable->info('name');

    $select = $usersTable->select()
												->setIntegrityCheck(false)
												->from($usersTableName)
												->joinleft($membershipTableName, $usersTableName . ".user_id = " . $membershipTableName . '.user_id')
												->joinleft($storeTableName, $membershipTableName . ".store_id = " . $storeTableName . '.store_id', array('owner_id AS store_owner_id'))
												->where($membershipTableName . '.resource_id = ?', $params['store_id']);
					
		if ($request_count == 'request') {
			$select = $select->where($membershipTableName . '.active = ?', '0')
			                 ->where($membershipTableName . '.resource_approved = ?', '0')
			                 ->where($membershipTableName . '.user_approved = ?', '0');
		} else {
			$select = $select->where($membershipTableName . '.active = ?', '1')
					            ->where($membershipTableName . '.user_approved = ?', '1');
		}
		
		//GET THE FRIEND OF LOGIN USER.
		$friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
		if($request_count == 'friend') {			
			if(!empty($friendId)) {
				$select->where($membershipTableName . '.user_id IN (?)', (array) $friendId);
			}
		}
		
	  if($request_count == 'STOREADMINS') {
			$select = $select->join($storemanageadminsTableName, $membershipTableName . ".user_id = " . $storemanageadminsTableName . '.user_id')
				->where($storemanageadminsTableName . '.store_id = ?', $params['store_id']);
		}
		
		if ($request_count == 'otherMember' && !empty($friendId)) {
			$select->where($membershipTableName . '.user_id NOT IN (?)', (array) $friendId);
		}
		
		if(!empty($params['search'])) {
			$select = $select->where($usersTableName . ".displayname LIKE ? ", '%' . $params['search'] . '%');
		}

		if (isset($params['roles_id']) && $params['roles_id']) {
			$toDelete='-1';
			$params['roles_id'] = array_diff($params['roles_id'], array($toDelete));
			$select = $select->where($membershipTableName . '.role_id IN (?)', (array) $params['roles_id']);
		}
		
		if (isset($params['category_roleid']) && !empty($params['category_roleid'])) {
			$select = $select->where($membershipTableName . '.role_id = ?', $params['category_roleid']);
		}
		
		if (isset($params['orderby']) && !empty($params['orderby'])) {
			if ($params['orderby'] == 'featured') {
				$select = $select->where($membershipTableName . '.featured = ?', '1');
			} elseif($params['orderby'] == 'highlighted') {
				$select = $select->where($membershipTableName . '.highlighted = ?', '1');
			} elseif($params['orderby'] == 'displayname') { 
				$select = $select->order($usersTableName . '.displayname ASC');
			} elseif($params['orderby'] == 'storeadmin') {
				$select = $select->join($storemanageadminsTableName, $membershipTableName . ".user_id = " . $storemanageadminsTableName . '.user_id', null)
				->where($storemanageadminsTableName . '.store_id = ?', $params['store_id']);
			} elseif($params['orderby'] == 'myfriend') {
			  $user_id = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
				$select = $select->where($membershipTableName . '.user_id IN (?)', (array) $user_id);
			}
		}
    
		$select = $select->order($membershipTableName . '.highlighted DESC')
			              ->order($membershipTableName . '.join_date DESC');
    return $select;
  }


  /**
   * Return member data
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
  public function widgetMembersData($params = array()) {

		$tableMemberName = $this->info('name');
		
		$tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');
		$tableStoreName = $tableStore->info('name');
		
		$UserTable = Engine_Api::_()->getDbtable('users', 'user');
		$UserTableName = $UserTable->info('name');

		$select = $UserTable->select()
				->setIntegrityCheck(false)
				->from($UserTableName, array('user_id', 'username', 'displayname', 'photo_id'));
				
		if ($params['widget_name'] == 'recent')	 {
			$select->join($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id', array('title AS storemember_title','COUNT("user_id") AS JOINP_COUNT'))->limit($params['limit'])->group("$tableMemberName.user_id")->order($tableMemberName . '.join_date DESC');
		}
		
		if ($params['widget_name'] == 'featured') {
			$select->join($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id', array('title AS storemember_title', 'COUNT("user_id") AS JOINP_COUNT'))->where($tableMemberName . '.featured = ?', 1)->group("$tableMemberName.user_id")->limit($params['limit']);
		}
		
		if ($params['widget_name'] == 'mostvaluable') {
			$select->join($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id', array('title AS storemember_title', 'COUNT("user_id") AS JOINP_COUNT'))
			->group("$tableMemberName.user_id")->limit($params['limit']); 
		}
		
	  if ($params['widget_name'] == 'mostvaluablestores') {
			$select->join($tableMemberName, $UserTableName . ".user_id = " . $tableMemberName . '.user_id', array('title AS storemember_title', 'COUNT("store_id") AS MEMBER_COUNT'))
			->group("$tableMemberName.resource_id")->limit($params['limit']);
		}
		
    $select->join($tableStoreName, $tableMemberName . ".resource_id = " . $tableStoreName . '.store_id', array('title AS store_title', 'store_id', 'owner_id'))
					->where($tableMemberName . '.active = ?', 1)
					->where($tableStoreName . '.closed = ?', '0')
					->where($tableStoreName . '.approved = ?', '1')
					->where($tableStoreName . '.search = ?', '1')
					->where($tableStoreName . '.declined = ?', '0')
					->where($tableStoreName . '.draft = ?', '1');
					
		if ($params['widget_name'] == 'mostvaluablestores') {
			$select->order('MEMBER_COUNT DESC');
		}
   
		if ($params['widget_name'] == 'mostvaluable') {
			$select->order('JOINP_COUNT DESC');
		}

		//End Network work
		return $UserTable->fetchAll($select);
  }
  
  /**
   * Return member of the day
   *
   * @return Zend_Db_Table_Select
   */
  public function memberOfDay() {

    //CURRENT DATE TIME
    $date = date('Y-m-d');

    //GET ITEM OF THE DAY TABLE NAME
    $memberOfTheDayTableName = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore')->info('name');

		//GET STORE TABLE NAME
		$storeTableName = Engine_Api::_()->getDbtable('stores', 'sitestore')->info('name');

    $UserTable = Engine_Api::_()->getDbtable('users', 'user');
		$userTableName = $UserTable->info('name');

		$storeMembershipTableName = $this->info('name');
		
    //MAKE QUERY
    $select = $UserTable->select()
												->setIntegrityCheck(false)
												->from($userTableName)
												->join($memberOfTheDayTableName, $userTableName . '.user_id = ' . $memberOfTheDayTableName . '.resource_id')
												->join($storeMembershipTableName, $userTableName . '.user_id = ' . $storeMembershipTableName . '.user_id', array(''))
												//->where($storeTableName.'.approved = ?', '1')
												//->where($storeTableName.'.declined = ?', '0')
												//->where($storeTableName.'.draft = ?', '1')
												->where($memberOfTheDayTableName . '.resource_type = ?', 'user')
												->where($memberOfTheDayTableName . '.start_date <= ?', $date)
												->where($memberOfTheDayTableName .'.end_date >= ?', $date)
												->order('Rand()');
    return $UserTable->fetchRow($select);
  }
  
  /**
   * Return store members
   *
   * @param array $params
   * @return Zend_Db_Table_Select
   */
  public function getMembers($params = array(), $showMember) {


		//GET THE FRIEND OF LOGIN USER.
		$friendId = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
		
    //VIDEO TABLE NAME
    $membershipTableName = $this->info('name');
    
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $userTable->info('name');

    //STORE TABLE
    $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storeTable->info('name');

    $storePackagesTable = Engine_Api::_()->getDbtable('packages', 'sitestore');
    $storePackageTableName = $storePackagesTable->info('name');

    //QUERY MAKING
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($storeTableName, array('photo_id', 'title as sitestore_title'))
                    ->join($membershipTableName, $membershipTableName . '.resource_id = ' . $storeTableName . '.store_id')
                    ->join($userTableName, $userTableName . '.user_id = ' . $membershipTableName . '.user_id', array('COUNT("user_id") AS JOINP_COUNT', 'displayname'))
                    ->join($storePackageTableName, "$storePackageTableName.package_id = $storeTableName.package_id",array('package_id', 'price'))
                    ->where($membershipTableName . '.active = ?', '1')
                    ->where($membershipTableName . '.user_approved = ?', '1');

		if ($showMember == 'friend' && !empty($friendId)) {
			$select->where($membershipTableName . '.user_id IN (?)', (array) $friendId);
		}
		
		if ($showMember == 'otherMember'  && !empty($friendId)) {
			$select->where($membershipTableName . '.user_id NOT IN (?)', (array) $friendId);
		}

    if (!empty($params['title'])) {
      $select->where($storeTableName . ".title LIKE ? ", '%' . $params['title'] . '%');
    }

		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

		if ((isset($params['orderby']) && $params['orderby'] == 'join_date') || !empty($params['viewedvideo'])) {
			$select = $select
											->order($membershipTableName .'.join_date DESC');
											//->order($membershipTableName .'.creation_date DESC');
		} elseif ((isset($params['orderby']) && $params['orderby'] == 'featured_member') || !empty($params['commentedvideo'])) {
			$select = $select->where($membershipTableName . '.featured = ?', '1')
											->order($membershipTableName .'.featured DESC')
											->order($membershipTableName .'.join_date DESC');
		}
		elseif ((isset($params['orderby']) && $params['orderby'] == 'member_count')) {
			$select = $select->order('JOINP_COUNT DESC');
		}
		
    if (!empty($params['search_member'])) {
				$select->where($userTableName . ".displayname LIKE ? OR " . $userTableName . ".username LIKE ?", '%' . $params['search_member'] . '%');
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

    
    $select = $select->where($storeTableName . '.closed = ?', '0')
                    ->where($storeTableName . '.approved = ?', '1')
                    ->where($storeTableName . '.search = ?', '1')
                    ->where($storeTableName . '.declined = ?', '0')
                    ->where($storeTableName . '.draft = ?', '1');

    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $select->where($storeTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $select->group($membershipTableName . '.user_id');

    return Zend_Paginator::factory($select);
  }
  
  /**
   * Return member object
   *
   * @param int $memberId
   * @return Zend_Db_Table_Select
   */
  public function getMembersObject($memberId) {
  
    $membershipTableName = $this->info('name');
    $select = $this->select()
              ->where($membershipTableName . '.member_id = ?', $memberId);
    return $this->fetchRow($select);
  }
  
  /**
   * Return joined stores
   *
   * @param int $user_id
   * @param string $params
   * @return Zend_Db_Table_Select and paginator
   */
  public function getJoinStores($user_id, $params) {

		$tableMemberName = $this->info('name');

		$tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');
		$tableStoreName = $tableStore->info('name');

		$select = $tableStore->select()
												->setIntegrityCheck(false)
												->from($tableStoreName, array('store_id','title','store_url', 'body', 'owner_id','photo_id','price','creation_date','featured','sponsored','view_count','comment_count','like_count','closed'))
												->joinleft($tableMemberName, $tableStoreName . ".store_id = " . $tableMemberName . '.resource_id', null)
												->where($tableMemberName . '.active = ?', 1)
												->where($tableMemberName . '.user_id = ?', $user_id)
												->where($tableStoreName . '.approved = ?', '1')
												->where($tableStoreName . '.draft = ?', '1')
												->where($tableStoreName . '.search = ?', '1')
												->where($tableStoreName . '.closed = ?', '0')
												->where($tableStoreName . '.declined = ?', '0');
												
		if ($params == 'onlymember') {
			$select->where($tableStoreName . '.owner_id <> ?', $user_id);
		}  

		if ($params == 'memberOfDay' || $params == 'onlymember') {
			$result = $tableStore->fetchAll($select);
		} elseif ($params == 'storeJoin') {
			$result = Zend_Paginator::factory($select);
		} 
		return $result;
  }
  
  /**
   * Check member is join.
   *
   * @param int $viewer_id
   * @param int $store_id
   * @param string $params
   * @return Zend_Db_Table_Select and paginator
   */
  public function hasMembers($viewer_id, $store_id = NULL, $params = NULL) {

    $membershipTableName = $this->info('name');

    $select = $this->select()
						->from($membershipTableName)
						->where('user_id = ?', $viewer_id);
						
		if (!empty($store_id)) {
			$select->where($membershipTableName . '.resource_id = ?', $store_id);
		}
		
		if ($params == 'Leave') {
			$select->where('active = ?', 1);
		}
		
		if ($params == 'Cancel' || $params == 'Accept' || $params == 'Reject') {
			$select->where('active = ?', 0);
		}
		if($params == 'Cancel') {
		$select->where('resource_approved = ?', 0)
		      ->where('user_approved = ?', 0);
		}
		
	  if ($params == 'Accept') {
			$select->where('resource_approved = ?', 1);
		}
		
		if ($params == 'Invite') {
			$select->where('resource_approved = ?', 1)
							->where('user_approved = ?', 1)
							->where('active = ?', 1);
	  }
		$select = $this->fetchRow($select);

    return $select;
	}
	
	/**
   * Return join members
   *
   * @param int $store_id
   * @param int $viewer_id
   * @param int $ownerId
   * @return Zend_Db_Table_Select
   */
  public function getJoinMembers($store_id, $viewer_id = null, $ownerId = null, $onlyMemberWithPhoto = 1) {

		$tableMemberName = $this->info('name');
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $userTable->info('name');
		$select = $this->select()
							->setIntegrityCheck(false)
							->from($tableMemberName, array('user_id'))
							->join($userTableName, $userTableName . '.user_id = ' . $tableMemberName . '.user_id')
							->where($tableMemberName . '.active = ?', 1)
							->where($tableMemberName . '.resource_approved = ?', 1)
							->where($tableMemberName . '.user_approved = ?', 1)
							->where($tableMemberName . '.store_id = ?', $store_id);
							
		if (!empty($viewer_id))  {
			$select->where($tableMemberName . '.user_id <> ?', $ownerId)
			       ->where($tableMemberName . '.user_id <> ?', $viewer_id);
		}
		
		$select->order($tableMemberName . '.join_date DESC');
		$result = Zend_Paginator::factory($select);

    if($onlyMemberWithPhoto) {
      $select->where($userTableName . '.photo_id <> ?', 0);
    }

		return $result;
  }
  
  	/**
   * Return count stores
   *
   * @param int $user_id
   * @return count
   */
  public function countStores($user_id) {

    $tableMemberName = $this->info('name');
		$select = $this->select()
									->from($tableMemberName, new Zend_Db_Expr('COUNT(*)'))
									->where($tableMemberName . '.active = ?', 1)
									->where('user_id = ?', $user_id);

		return	(integer) $select->query()->fetchColumn();
  }
  
  	/**
   * Return count stores
   *
   * @param int $user_id
   * @return count
   */
  public function listMemeberTabWidget($activTab) {
  
    $table = Engine_Api::_()->getDbtable('membership', 'sitestore');
    $tableMembershipName = $table->info('name');

    $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore'); 
    $tableStoreName = $tableStore->info('name');

    $userTable = Engine_Api::_()->getDbtable('users', 'user'); 
    $userTableName = $userTable->info('name');


		$select = $userTable->select()
			->setIntegrityCheck(false)
			->from($userTableName, array('user_id', 'username', 'displayname', 'photo_id'))
			->join($tableMembershipName, $userTableName . ".user_id = " . $tableMembershipName . '.user_id', array('COUNT("user_id") AS JOINP_COUNT'))
			->join($tableStoreName, $tableMembershipName . ".resource_id = " . $tableStoreName . '.store_id', array('title AS sitestore_title', 'store_id', 'owner_id'))
			->where($tableMembershipName . '.active = ?', 1)
			->where($tableStoreName . '.closed = ?', '0')
			->where($tableStoreName . '.approved = ?', '1')
			->where($tableStoreName . '.search = ?', '1')
			->where($tableStoreName . '.declined = ?', '0')
			->where($tableStoreName . '.draft = ?', '1');
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $select->where($tableStoreName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    switch ($activTab->name) {
      case 'recent_storemembers':
       $select->order($tableMembershipName . '.join_date DESC');
      break;
      case 'featured_storemembers':
        $select->where($tableMembershipName .'.featured = ?', 1);
        $select->order('Rand()');
      break;
    }

		$select->group("$tableMembershipName.user_id");

		return Zend_Paginator::factory($select);
  }
  
}