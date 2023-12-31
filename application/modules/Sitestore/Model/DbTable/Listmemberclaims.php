<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Listmemberclaims.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Listmemberclaims extends Engine_Db_Table {

  protected $_rowClass = 'Sitestore_Model_Listmemberclaim';

	/**
   * Return claim member
   *
   * @param int $userid
   * @return Zend_Db_Table_Select
   */  
  public function getClaimListMember($userid) {
  	
  	$select = $this->select();  	
  	if(isset($userid)) {
  		$select->where('user_id =?', $userid);
  	}
 	
  	return $select;
  }
  
	/**
   * Return users lists whose stores can be claimed
   *
   * @param int $text
   * @param int $limit
   * @return user lists
   */   
  public function getMembers($text, $limit) {

    //SELECT
    $user_idarray = $this->fetchAll($this->select()->from($this->info('name'), 'user_id'));

    //MAKING USER ID ARRAY
    $user_id_array = '';
    if (!empty($user_idarray)) {
      foreach ($user_idarray as $user_ids) {
        $user_id_array = $user_ids->user_id . ',' . $user_id_array;
      }
    }
    $user_id_array = $user_id_array . '0';

    //GET USER TABLE
    $tableUser = Engine_Api::_()->getDbtable('users', 'user');

    //SELECT
    $select = $tableUser->select()
            ->where('displayname  LIKE ? ', '%' .$text . '%')
            ->where($tableUser->info('name') . '.user_id NOT IN (' . $user_id_array . ')')
            ->order('displayname ASC')
            ->limit($limit);

    //FETCH
    return$tableUser->fetchAll($select);      
  }
  
}

?>