<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Favourites.php.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestore_Model_DbTable_Favourites extends Engine_Db_Table {

  protected $_rowClass = "Sitestore_Model_Favourite";

  //THIS IS FOR ADD fAVOURITE  LINK IS NOT SHOW
  public function isShow($storeId) {

   $favouriteTableName = $this->info('name');
   $select = $this->select()
										->from($favouriteTableName, array('favourite_id'))
										->where('store_id = ?', $storeId)
										->limit(1);
    $findResults = $select->query()->fetchALL();
    if ( !empty($findResults) ) {
      return 1;
    }
    else {
      return 0;
    }
  }

  //THIS FOR DELETE fAVOURITE  LINK IS NOT SHOW
  public function isnotShow($storeId)
  {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $favouriteTableName = $this->info('name');
    $select = $this->select()
										->from($favouriteTableName, array('favourite_id'))
										->where('store_id_for = ?', $storeId)
                    ->where('	owner_id = ?', $viewer_id)
										->limit(1);
    $findResults = $select->query()->fetchALL();

    if ((count($findResults) >= 1)) {
      return 1;
    }
    else {
			return 0;
    }
  }

  // DELETE LINK.
  public function deleteLink($store_id, $viewer_id) {

    $favouritesName = $this->info('name');
    $storetable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storetable->info('name');
    $select = $this->select();
    $select = $select
            ->setIntegrityCheck(false)
            ->from($favouritesName, null)
            ->join($storeTableName, $favouritesName . '.store_id = ' . $storeTableName . '.store_id', array('store_id', 'title'))
            ->where($favouritesName . '.store_id_for = ?', $store_id)
            ->where($favouritesName . '.owner_id = ?', $viewer_id);
    return $this->fetchALL($select);
  }

	/**
   * Return linked stores result
   *
   * @param int $sitestore_id
	 * @param int $LIMIT
   */
  public function linkedStores($sitestore_id, $LIMIT,$params = array(), $flag = null) {

		$storestable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storesName = $storestable->info('name');
    $favouritesName = $this->info('name');
    $select = $storestable->select()
						->setIntegrityCheck(false);
						
		if (empty($flag)) {
			$select->from($storestable, array('store_id', 'title','photo_id'))
			      ->join($favouritesName, $favouritesName . '.store_id_for = ' . $storesName . '.store_id')
			      ->where($favouritesName . '.store_id =?', $sitestore_id);
		}
		
		//START WORK FOR PARENT STORE AND SUB STORE.
//		if ($flag == 'substore') {
//			$select->from($storestable, array('store_id', 'title','photo_id', 'owner_id'))
//			->where($storesName . '.	parent_id =?', $sitestore_id)
//			->where($storesName . '.	substore =?', '1');
//		}
		
	  if ($flag == 'parentstore') {
			$select->from($storestable, array('store_id', 'title','photo_id', 'owner_id'))
			->where($storesName . '.	store_id =?', $sitestore_id)
			->where($storesName . '.	parent_id =?', '0')
			->where($storesName . '.	substore =?', '0');
		}
		//END WORK FOR PARENT STORE AND SUB STORE.
		
		$select->limit($LIMIT);
		if ( isset($params['category_id']) && !empty($params['category_id']) ) {
			$select = $select->where($storesName . '.	category_id =?', $params['category_id']);
		}
		if ( isset($params['featured']) && ($params['featured'] == '1') ) {
			$select = $select->where($storesName . '.	featured =?', '0');
		}
		elseif ( isset($params['featured']) && ($params['featured'] == '2') ) {
			$select = $select->where($storesName . '.	featured =?', '1');
		}

		if ( isset($params['sponsored']) && ($params['sponsored'] == '1') ) {
			$select = $select->where($storesName . '.	sponsored =?', '0');
		}
		elseif ( isset($params['sponsored']) && ($params['sponsored'] == '2') ) {
			$select = $select->where($storesName . '.	sponsored =?', '1');
		}

    if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      return Zend_Paginator::factory($select);
    }

    return $userListings = $storestable->fetchAll($select);
	}
}
?>