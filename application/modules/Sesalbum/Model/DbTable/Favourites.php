<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Favourites.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Model_DbTable_Favourites extends Engine_Db_Table {
  protected $_rowClass = "Sesalbum_Model_Favourite";
  public function isFavourite($params = array()) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
     $select = $this->select()
                    ->where('resource_type = ?', $params['resource_type'])
                    ->where('resource_id = ?', $params['resource_id'])
                    ->where('user_id = ?', $viewer_id)
                    ->query()
                    ->fetchColumn();
		return $select;
  }
		public function getItemfav($resource_type,$itemId){
		$tableFav = Engine_Api::_()->getDbtable('favourites', 'sesalbum');
    $tableMainFav = $tableFav->info('name');
    $select = $tableFav->select()->from($tableMainFav)->where('resource_type =?', $resource_type)->where('user_id =?', Engine_Api::_()->user()->getViewer()->getIdentity())->where('resource_id =?', $itemId);
    return $tableFav->fetchRow($select);	
	}
  public function getFavourites($params = array()) {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $select = $this->select()
            ->from($this->info('name'))
            ->where('resource_type =?', $params['resource_type'])
            ->where('user_id =?', $viewer_id);
		$albumTableName = Engine_Api::_()->getItemTable('album')->info('name');
		if($params['resource_type'] == 'album_photo'){
			$photoTableName = Engine_Api::_()->getItemTable('album_photo')->info('name');
			$select = $select->joinLeft($photoTableName, $photoTableName . '.photo_id = ' . $this->info('name') . '.resource_id', null)
											->where($photoTableName.'.photo_id != ?','');
			$select = $select ->joinLeft($albumTableName, $albumTableName . '.album_id = ' . $photoTableName . '.album_id', null)
												->where($albumTableName.'.album_id !=?','');
		}else{
			$select = $select ->joinLeft($albumTableName, $albumTableName . '.album_id = ' . $this->info('name') . '.resource_id', null)
												->where($albumTableName.'.album_id !=?','');
		}		
    return Zend_Paginator::factory($select);
  }

}