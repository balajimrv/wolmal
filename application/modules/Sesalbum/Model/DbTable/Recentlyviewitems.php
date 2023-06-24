<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Recentlyviewitems.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Model_DbTable_Recentlyviewitems extends Engine_Db_Table
{
	protected $_name = 'sesalbum_recentlyviewitems';
  protected $_rowClass = 'Sesalbum_Model_Recentlyviewitem';	
	public function getitem($params = array()){
		if($params['type'] == 'album_photo'){
			$itemTable = Engine_Api::_()->getItemTable('album_photo');
			$itemTableName = $itemTable->info('name');
			$fieldName = 'photo_id';
		}else{
			$itemTable = Engine_Api::_()->getItemTable('album');
			$itemTableName = $itemTable->info('name');
			$fieldName = 'album_id';
			$not = true;
		}		
		$subquery = $this->select()->from($this->info('name'),array('*','MAX(creation_date) as maxcreadate'))->group($this->info('name').".resource_id")->where($this->info('name').'.resource_type =?', $params['type']);
		$select = $this->select()
							->from(array('engine4_sesalbum_recentlyviewitems' => $subquery))
							->where('resource_type = ?' ,$params['type'])
							->setIntegrityCheck(false)
						  ->order('maxcreadate DESC')
							->where($itemTableName.'.photo_id != ?','')
							->group($this->info('name').'.resource_id');
		if($params['criteria'] == 'by_me'){
			$select->where($this->info('name').'.owner_id =?',Engine_Api::_()->user()->getViewer()->getIdentity());
		}else if($params['criteria'] == 'by_myfriend'){
		/*friends array*/
			$friendIds = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
			if(count($friendIds) == 0)
				return array();
			$select->where($this->info('name').".owner_id IN ('".implode(',',$friendIds)."')");
		}
		$select->joinLeft($itemTableName, $itemTableName . ".$fieldName =  ".$this->info('name') . '.resource_id',array('photo_id','album_id'));
		$select->where($itemTableName.'.'.$fieldName.' != ?','');
	if(!isset($not)){
		$albumTable = Engine_Api::_()->getItemTable('album');
		$albumTableName = $albumTable->info('name');
		$select->joinLeft($albumTableName, $albumTableName . ".album_id =  ".$itemTableName. '.album_id',null);
		$select->where($albumTableName.'.album_id != ?','');
	}else{
		$select->where($itemTableName.'.album_id != ?','');
	}
	if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1))
			$select->where('type IS NULL');
			
		//store data in 
		$tempArray = array();
		$tempStorePhotoIds = '';
		$viewer = Engine_Api::_()->user()->getViewer();
		$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
		if ($album_enable_check_privacy)
		{
				//fecth all
				$photos = $this->fetchAll($select);
				//loop over all albums once
				foreach ($photos as $photo)
				{
					$album =  Engine_Api::_()->getItem('album', $photo->album_id);
					if(!$album)
						continue;
					//check authorization album
					if ($album->authorization()->isAllowed($viewer, 'view'))
					{
						$tempStorePhotoIds .= $photo->{$fieldName}.',';
					}
						if( count($tempArray) >= $params['limit']){
							break;
					}
				}
					$tempStorePhotoIds = trim($tempStorePhotoIds,',');
					if($tempStorePhotoIds){
							$select = $select->where($itemTableName.'.'.$fieldName.' IN (' . $tempStorePhotoIds . ')');
					}else{
							$select = $select->where($itemTableName.'.'.$fieldName.' IN (0)');
					}
		}	
		if(isset( $params['limit'])){
			$select->limit( $params['limit'])	;
		}
		return $this->fetchAll($select);
	}
}