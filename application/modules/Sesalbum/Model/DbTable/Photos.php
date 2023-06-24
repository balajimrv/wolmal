<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Photos.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Model_DbTable_Photos extends Engine_Db_Table
{
  protected $_rowClass = 'Sesalbum_Model_Photo';
  protected $_name = 'album_photos';
  
  public function getPhotoSelect($params = array())
  {
    $select = $this->select();
    
    if( !empty($params['album']) && $params['album'] instanceof Sesalbum_Model_Album ) {
      $select->where('album_id = ?', $params['album']->getIdentity());
    } else if( !empty($params['album_id']) && is_numeric($params['album_id']) ) {
      $select->where('album_id = ?', $params['album_id']);
    }
    if( !isset($params['order']) ) {
      $select->order('order ASC');
    } else if( is_string($params['order']) ) {
      $select->order($params['order']);
    }
	
	//fecth all
		$photos = $this->fetchAll($select);
		//store data in 
		$tempArray = array();
		$tempStorePhotoIds = '';
		$viewer = Engine_Api::_()->user()->getViewer();
		$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
		if ($album_enable_check_privacy)
		{
			//loop over all albums once
			foreach ($photos as $photo)
			{
				$album = $photo->getParent();
				if(!$album)
					continue;
				//check authorization album
				if ($album->authorization()->isAllowed($viewer, 'view'))
				{
					$tempArray[] = $photo;
					$tempStorePhotoIds .= $photo->getIdentity().',';
				}
				if(empty($params['pagNator'])){
					if(isset($params['limit_data']) && count($tempArray) >= $params['limit_data']){
						break;
					}
				}
			}
			if(empty($params['pagNator'])){
				$tempStorePhotoIds = trim($tempStorePhotoIds,',');
				if($tempStorePhotoIds){
						$select = $select->where('photo_id IN (' . $tempStorePhotoIds . ')');
				}else{
						$select = $select->where('photo_id IN (0)');
				}
				if(isset($params['limit_data'])){
					return $this->fetchAll($select);
				}else
					return $this->fetchAll($select);
			}else
					return Zend_Paginator::factory($tempArray);
	}
		
	if(empty($params['pagNator'])){
		if(isset($params['limit_data'])){
			$select->limit($params['limit_data']);
			return $this->fetchAll($select);
		}else
			return $this->fetchAll($select);
	}else
			return Zend_Paginator::factory($select);		
  }
  public function getPhotoPaginator(array $params)
  {
    return Zend_Paginator::factory($this->getPhotoSelect($params));
  }
	public function countPhotos(){
		return $this->select()->from($this->info('name'), new Zend_Db_Expr('COUNT(photo_id) as total_photos'))->limit(1)->query()->fetchColumn();;
	}
	public function getPhoto($params){
		$photo_table = Engine_Api::_()->getDbtable('photos', 'sesalbum');
    $photoTableName = $photo_table->info('name');
		$album_table = Engine_Api::_()->getDbtable('albums', 'sesalbum');
    $albumTableName = $album_table->info('name');
    $select = $photo_table->select()
            ->from($photoTableName)
            ->setIntegrityCheck(false)
            ->where('search =?', true)
						 ->joinLeft($albumTableName, $albumTableName . '.album_id=' . $photoTableName . '.album_id', null)
            ->where($photoTableName.".title  LIKE ?  ", '%' . $params['text'] . '%')
            ->order($photoTableName . '.photo_id DESC')
            ->group($photoTableName . '.photo_id');
		if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1))
			$select->where($albumTableName.'.type IS NULL');
		
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
				$album = $photo->getParent();
				if(!$album)
					continue;
				//check authorization album
				if ($album->authorization()->isAllowed($viewer, 'view'))
				{
					$tempArray[] = $photo;
					$tempStorePhotoIds .= $photo->getIdentity().',';
				}
					if( count($tempArray) >= 10){
						break;
				}
			}
				$tempStorePhotoIds = trim($tempStorePhotoIds,',');
				if($tempStorePhotoIds){
						$select = $select->where($photoTableName.'.photo_id IN (' . $tempStorePhotoIds . ')');
				}else{
						$select = $select->where($photoTableName.'.photo_id IN (0)');
				}
				return $this->fetchAll($select);
		}	
    return $photo_table->fetchAll($select);	
	}
	public function order($album){
		// Get a list of all photos in this album, by order
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    return $photoTable->select()
            ->from($photoTable, 'photo_id')
            ->where('album_id = ?', $album->getIdentity())
            ->order('order ASC')
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN)
    ;	
	}
	public function profilePhotos($params){
		$select = $this->select()
			->from($this->info('name'))
			->where('owner_id = ?',$params['userId'])
			->order('creation_date DESC');
		if(isset($params['is_featured']))
			$select = $select->where('is_featured =?',1);
		if(isset($params['is_sponsored']))
			$select = $select->where('is_sponsored =?',1);
		if(isset($params['limit_data']))
			$select = $select->limit($params['limit_data']);
		return Zend_Paginator::factory($select);
	}
	public function getFavourite($params = array()){
		$tableFav = Engine_Api::_()->getDbtable('favourites', 'sesalbum');
		$tableFav = $tableFav->info('name');
		$select = $this->select()
							->from($this->info('name'))
							->where('photo_id = ?',$params['resource_id'])
							->setIntegrityCheck(false)
							->where('resource_type =?','album_photo')
							->joinLeft($tableFav, $tableFav . '.resource_id=' . $this->info('name') . '.photo_id',array('user_id'));
							return  Zend_Paginator::factory($select);
	}
	public function featuredSponsored($value = array()){
			$select = $this->select()->from($this->info('name'),array('*'));
		  $GetTableNameAlbum= Engine_Api::_()->getItemTable('album');
			$tableNameAlbum = $GetTableNameAlbum->info('name');
			$select->setIntegrityCheck(false)
							->where($tableNameAlbum.'.album_id IS NOT NULL');
			if($value['criteria'] == 1){
				 $select->where($this->info('name').'.is_featured =?','1');
 		  }else if($value['criteria'] == 2){
			 $select->where($this->info('name').'.is_sponsored =?','1');
		  }else if($value['criteria'] == 3){
				$select->where($this->info('name').'.is_featured = 1 OR '.$this->info('name').'.is_sponsored = 1');
		  }else if($value['criteria'] == 4){
			 $select->where($this->info('name').'.is_featured = 0 AND '.$this->info('name').'.is_sponsored = 0');
		  }
			$select->joinLeft($tableNameAlbum, $tableNameAlbum . '.album_id = ' . $this->info('name') . '.album_id', null);
			if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1))
      	$select->where($tableNameAlbum.'.type IS NULL');
			
			switch($value['info']){
			case 'recently_created':
				$select->order('creation_date DESC');
				break;
			case 'most_viewed':
				$select->order('view_count DESC');
				break;
			case 'most_liked':
				$select->order('like_count DESC');
				break;
			case 'most_rated':
				$select->order('rating DESC');
				break;
			case 'most_favourite':
				$select->order('favourite_count DESC');
				break;
			case 'most_download':
				$select->order('download_count DESC');
				break;
			case 'most_commented':
				$select->order('comment_count DESC');
				break;
			case 'random':
					$select->order('Rand()');
			break;
		}
				
			//store data in 
			$tempArray = array();
			$viewer = Engine_Api::_()->user()->getViewer();
			$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
			if ($album_enable_check_privacy)
			{
				//fecth all
				$photos = $this->fetchAll($select);
				//loop over all albums once
				foreach ($photos as $photo)
				{
					$album = $photo->getParent();
					if(!$album)
						continue;
					//check authorization album
					if ($album->authorization()->isAllowed($viewer, 'view'))
					{
						$tempArray[] = $photo;
					}
				}
				return Zend_Paginator::factory($tempArray);
			}				
			return  Zend_Paginator::factory($select);
	}
	public function photoOfYou($params){
			$photoTable = Engine_Api::_()->getItemTable('photo');
			$photoTableName = $photoTable->info('name');
			$albumTable = Engine_Api::_()->getItemTable('album');
			$albumTableName = $albumTable->info('name');
			$select = $photoTable->select()
				->from($photoTableName)
			  ->setIntegrityCheck(false)
			  ->joinLeft($albumTableName, $albumTableName . '.album_id=' . $photoTableName . '.album_id',null)
				->where($photoTableName . '.owner_id = ?',$params['userId'])
				->where($photoTableName.'.album_id != ?','0')
				->where($photoTableName.'.album_id != ?','')
				->order($photoTableName.'.creation_date DESC');
			
			//store data in 
			$tempArray = array();
			$viewer = Engine_Api::_()->user()->getViewer();
			$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
			if ($album_enable_check_privacy)
			{
				//fecth all
				$photos = $this->fetchAll($select);
				//loop over all albums once
				foreach ($photos as $photo)
				{
					$album = $photo->getParent();
					if(!$album)
						continue;
					//check authorization album
					if ($album->authorization()->isAllowed($viewer, 'view'))
					{
						$tempArray[] = $photo;
					}
					if(isset($params['limit_data']) && count($tempArray) >= $params['limit_data'])
						break;
				}
				return Zend_Paginator::factory($tempArray);
			}				
			if(isset($params['limit_data']))
				$select = $select->limit($params['limit_data']);
			return  Zend_Paginator::factory($select);
	}
	public function tabWidgetPhotos($params){
		$parentTable = Engine_Api::_()->getItemTable('album');
		$parentTableName = $parentTable->info('name');
		$tableName = $this->info('name');
		$select = $this->select()
			->from($tableName)
			->joinLeft($parentTableName, $parentTableName . '.album_id=' . $tableName . '.album_id', null)
			->order($tableName.'.'.$params['popularCol'] . ' DESC')
			->where($tableName.'.album_id != ?','0')
			->where($parentTableName.'.album_id != ?','');
		if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1))
			$select->where('type IS NULL');
		if(isset($params['fixedData']) && $params['fixedData'] != '')
			$select = $select->where($tableName.'.'.$params['fixedData'].' =?',1);
		if(isset($params['fixedDataPhoto']) && $params['fixedDataPhoto'] != '')
			$select = $select->where($params['fixedDataPhoto']);	
		if(isset($params['lat']) && isset($params['lng']) && $params['lat'] != '' && $params['lng'] != '' && isset($params['location']) && $params['location'] != ''){
			$tableLocation = Engine_Api::_()->getDbtable('locations', 'sesbasic');
			$tableLocationName = $tableLocation->info('name');
			$origLat = $params['lat'];
			$origLon = $params['lng'];
			$select = 	$select->setIntegrityCheck(false);
			if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.search.type',1) == 1){
				$searchType = 3956;
			}else
				$searchType = 6371;
			$dist = $params['miles']; // This is the maximum distance (in miles) away from $origLat, $origLon in which to search
			$select->joinLeft($tableLocationName, $tableLocationName . '.resource_id = ' . $tableName . '.photo_id AND '.$tableLocationName . '.resource_type = "sesalbum_photo" ',$searchType." * 2 * ASIN(SQRT( POWER(SIN(($origLat - abs(lat))*pi()/180/2),2) + COS($origLat*pi()/180 )*COS(abs(lat)*pi()/180) *POWER(SIN(($origLon-lng)*pi()/180/2),2))) as distance");
			$select->where($tableLocationName.".lng between ($origLon-$dist/abs(cos(radians($origLat))*69)) and ($origLon+$dist/abs(cos(radians($origLat))*69)) and ".$tableLocationName.".lat between ($origLat-($dist/69)) and ($origLat+($dist/69))");
			$select->order('distance');
			$select->having("distance < $dist");
	 }
		if (isset($params['category_id']) && intval($params['category_id'])) {
			$select->where($parentTableName.".category_id = ?", $params['category_id']);
			if (isset($params['subcat_id']) && intval($params['subcat_id'])) $select->where($parentTableName.".subcat_id = ?", $params['subcat_id']);
			if (isset($sparam['subsubcat_id']) && intval($params['subsubcat_id']))  $select->where($parentTableName.".subsubcat_id = ?", $params['subsubcat_id']);
		}
		
		if (isset($params['popularCol']) && ($params['popularCol']) && $params['popularCol'] == 'is_featured') $select->where($tableName.".is_featured = ?", 1);
		if (isset($params['popularCol']) && ($params['popularCol']) && $params['popularCol'] == 'is_sponsored') $select->where($tableName.".is_sponsored = ?", 1);
				
    if (isset($params['search']) && $params['search'] != '') {
      $select->where($tableName.".title  LIKE ?", '%' . $params['search'] . '%');
    }
		if(isset($params['order']) && $params['order'] == 'is_sponsored' && $params['order'] == 'is_featured')
			$select->order('view_count DESC');
		// Create new array filtering out private albums
		$viewer = Engine_Api::_()->user()->getViewer();
		$new_select = $select;
		if (isset($params['show']) && $params['show'] == 2 && $viewer->getIdentity()) {
      $users = $viewer->membership()->getMembershipsOfIds();
			$select->where($tableName.'.owner_id IN (?)',$users);
    }
		/*$new_select = array();
		$i = 0;
		foreach($photo_select->getTable()->fetchAll($photo_select) as $photo )  {
			if (Engine_Api::_()->authorization()->isAllowed($photo, $viewer, 'view')){
				$new_select[$i++] = $photo;
			}
		}*/
		
		//store data in 
		$tempArray = array();
		$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
		if ($album_enable_check_privacy)
		{
			//fecth all
			$photos = $this->fetchAll($select);
			//loop over all albums once
			foreach ($photos as $photo)
			{
				$album = $photo->getParent();
				if(!$album)
					continue;
				//check authorization album
				if ($album->authorization()->isAllowed($viewer, 'view'))
				{
					$tempArray[] = $photo;
				}
			}
			return Zend_Paginator::factory($tempArray);
		}	
		return  Zend_Paginator::factory($select);
	}
	public function getPhotoCustom($photo = '',$params = array(),$nextPreviousCondition = '<',$getallphotos = false){
		//status blank means no custom param given to apply, so get the photo as per album and photo id given.
		$status = '';
		//getSEVersion for lower version of SE
		$getmodule = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core');
		if(!empty($getmodule->version) && version_compare($getmodule->version , '4.8.6') < 0){
			$toArray = true	;
		}else
			$toArray = false;
		$GetTableNameAlbum= Engine_Api::_()->getItemTable('album');
		$tableNameAlbum = $GetTableNameAlbum->info('name');
		$GetTableNamePhoto = Engine_Api::_()->getItemTable('photo');
		$tableNamePhoto = $GetTableNamePhoto->info('name');
		 $select = $GetTableNamePhoto->select()
		 				 ->from($GetTableNamePhoto)
						 ->where($tableNamePhoto.'.album_id != ?','0')
						 ->where($tableNameAlbum.'.album_id != ?','');
		 $select->setIntegrityCheck(false);
	
		 //if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1))
      	//$select->where('type IS NULL');
		 $select->joinLeft($tableNameAlbum, $tableNameAlbum . '.album_id = ' . $tableNamePhoto . '.album_id', null);
		// for custom param as per status
		if(isset($params['status']) && $params['status'] == 'is_featured')
			$status = 'is_featured';
		else if(isset($params['status']) && $params['status'] == 'is_sponsored')
			$status = 'is_sponsored';
		else if(isset($params['status']) && $params['status'] == 'comment')
			$status = 'comment';
		else if(isset($params['status']) && $params['status'] == 'favourite')
			$status = 'favourite';
		else if(isset($params['status']) && $params['status'] == 'download')
			$status = 'download';
		else if(isset($params['status']) && $params['status'] == 'view')
			$status = 'view';
		else if(isset($params['status']) && $params['status'] == 'creation')
			$status = 'creation';
		else if(isset($params['status']) && $params['status'] == 'modified')
			$status = 'modified';
		else if(isset($params['status']) && $params['status'] == 'offtheday')
			$status = 'offtheday';	
		else if(isset($params['status']) && $params['status'] == 'like')
			$status = 'like';			
		else if(isset($params['status']) && $params['status'] == 'tagged_photo')
			$status = 'tagged_photo';
		else if(isset($params['status']) && $params['status'] == 'photoofyou')
			$status = 'photoofyou';
		else if(isset($params['status']) && $params['status'] == 'rating')
			$status = 'rating';
		else if(isset($params['status']) && $params['status'] == 'by_myfriend'){
			$status = 'viewWidget';
			$viewWidget = 'by_myfriend';
		}else if(isset($params['status']) && $params['status'] == 'by_me'){
			$status = 'viewWidget';
			$viewWidget = 'by_me';
		}else if(isset($params['status']) && $params['status'] == 'on_site'){
			$status = 'viewWidget';
			$viewWidget = 'on_site';
		}else if(isset($params['status']) && $params['status'] == 'member-featured'){
			$status = 'member-featured';
		}
		if(isset($params['type'])){
			$status = 'special';	
		}
	if($status != '' && !$getallphotos ){
		//limit OFFSET if available 
		if(isset($params['limit']) && $params['limit'] > -1 && !$getallphotos){
			$select->limit(1,$params['limit']);
		}else if(!$getallphotos && isset($params['limit']) && $params['limit'] < 1)
				return;
	}
		// custom query as per status assign
		switch($status){
			case 'special':
			//limit OFFSET if available 
			if(isset($params['limit']) && $params['limit'] > -1 && !$getallphotos){
				$select->limit(1,$params['limit']);
			}else if(isset($params['limit']) && $params['limit'] > 0 && !$getallphotos){
				$select->limit(1,$params['limit']);
			}else if(!$getallphotos && isset($params['limit']) && $params['limit'] < 1)
				return;
			if(isset($params['type']) && ($params['type'] == 'is_sponsored' || $params['type'] == 'is_featured')){
				$select->where("$tableNamePhoto.".$params['type']." = ?",'1');
			}else if(isset($params['type']) && $params['type'] == 'or'){
				$select->where("$tableNamePhoto.is_sponsored = 1 OR $tableNamePhoto.is_featured = 1");
			}else if(isset($params['type']) && $params['type'] == 'neither'){
					$select->where("$tableNamePhoto.is_sponsored != 1 AND $tableNamePhoto.is_featured != 1");
			}
			//$select->where("$tableNamePhoto.photo_id $nextPreviousCondition  ?",$photo->photo_id);
		if(isset($params['status']) && ($params['status'] == 'view' || $params['status'] == 'comment' || $params['status'] == 'like' || $params['status'] == 'favourite' || $params['status'] == 'download'))
			$select->order($params['status'].'_count'.' DESC');
		else if(isset($params['status']) && $params['status'] != 'random')
			$select->order(str_replace('modified','modified_date',$params['status'])." DESC");		
			if($getallphotos)
					return Zend_Paginator::factory($select);
			if($toArray){
				 $photo = $GetTableNamePhoto->fetchAll($select);
				if(!empty($photo))
					$photo = $photo->toArray();
				else 
					$photo = '';
				}else{
					 $photo = $GetTableNamePhoto->fetchRow($select);
				}
				return $photo;
			break;
			case 'is_featured':
				//$select->where("$tableNamePhoto.photo_id $nextPreviousCondition  ?",$photo->photo_id);
				$select->where("$tableNamePhoto.is_featured = ?",'1');
				if(isset($params['order'])){
					$select->order($params['order'].' DESC');
				}
				$noCondition = true;
			break;	
			case 'is_sponsored':
				//$select->where("$tableNamePhoto.photo_id $nextPreviousCondition  ?",$photo->photo_id);
				$select->where("$tableNamePhoto.is_sponsored = ?",'1');
				if(isset($params['order'])){
					$select->order($params['order'].' DESC');
				}
				$noCondition = true;
			break;
			case 'comment':
				$select->order("$tableNamePhoto.comment_count DESC");
				$noCondition = true;
			break;
			case 'favourite':
				$select->order("$tableNamePhoto.favourite_count DESC");
				$noCondition = true;
			break;
			case 'download':
				$select->order("$tableNamePhoto.download_count DESC");
				$noCondition = true;
			break;
			case 'rating':
				$select->order("$tableNamePhoto.rating DESC");
				$noCondition = true;
			break;
			case 'like':
				$select->order("$tableNamePhoto.like_count DESC");
				$noCondition = true;
			break;
			case 'view':
				$select->order("$tableNamePhoto.view_count DESC");
				$noCondition = true;
			break;
			case 'creation':
				$select->order("$tableNamePhoto.creation_date DESC");	
			break;
			case 'modified':
				$select->order("$tableNamePhoto.modified_date DESC");
				$noCondition = true;
			break;
			case 'photoofyou':
				if(empty($params['user']))
					return ;
				$userId = $params['user'];
				$photoTable = Engine_Api::_()->getItemTable('photo');
				$photoTableName = $photoTable->info('name');
				$albumTable = Engine_Api::_()->getItemTable('album');
				$albumTableName = $albumTable->info('name');
				$selectphotoofyou = $photoTable->select()
					->from($photoTableName)
					->setIntegrityCheck(false)
					->joinLeft($albumTableName, $albumTableName . '.album_id=' . $photoTableName . '.album_id',null)
					->where($photoTableName . '.owner_id = ?',$userId)
					 ->where($albumTableName.'.album_id != ?','')
					->order($photoTableName.'.creation_date DESC');
				if(isset($params['limit']) && $params['limit'] > -1 && !$getallphotos){
					$selectphotoofyou->limit(1,$params['limit']);
				}else if(isset($params['limit']) && $params['limit'] > 0 && !$getallphotos){
					$selectphotoofyou->limit(1,$params['limit']);
				}else if(!$getallphotos && isset($params['limit']) && $params['limit'] < 1)
					return;
				if($getallphotos)
					return Zend_Paginator::factory($selectphotoofyou);;
				if($toArray){
				 $photo = $photoTable->fetchAll($selectphotoofyou);
				if(!empty($photo))
					$photo = $photo->toArray();
				else 
					$photo = '';
				}else{
					 $photo = $photoTable->fetchRow($selectphotoofyou);
				}
				return $photo;
			break;
			case 'viewWidget':
				$parentTable =  Engine_Api::_()->getDbtable('recentlyviewitems', 'sesalbum');
				$parentTableName = $parentTable->info('name');
				$albumTable = Engine_Api::_()->getItemTable('album');
				$albumTableName = $albumTable->info('name');
				$itemTable = Engine_Api::_()->getItemTable('album_photo');
				$itemTableName = $itemTable->info('name');
				$selectMyFriends = $parentTable->select()
							->from($parentTableName,array('*'))
							->where('resource_type = ?' ,'album_photo')
							->setIntegrityCheck(false)
							->where($albumTableName.'.album_id != ?','')
						  ->order('creation_date DESC');				
				if(isset($viewWidget) && $viewWidget ==  'by_me'){
					$selectMyFriends->where($parentTableName.'.owner_id =?',Engine_Api::_()->user()->getViewer()->getIdentity());	
				}else if(isset($viewWidget) && $viewWidget == 'by_myfriend'){
				/*friends array*/
					$friendIds = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
					$selectMyFriends->where($parentTableName.".owner_id IN ('".implode(',',$friendIds)."')");
				}
				$selectMyFriends->joinLeft($itemTableName, $itemTableName . ".photo_id =  ".$parentTableName . '.resource_id',array('photo_id'))
				->joinLeft($albumTableName, $albumTableName . '.album_id=' . $itemTableName . '.album_id',null);
				if(isset($params['limit']) && $params['limit'] > -1 && !$getallphotos){
					$selectMyFriends->limit(1,$params['limit']);
				}else if(isset($params['limit']) && $params['limit'] > 0 && !$getallphotos){
					$selectMyFriends->limit(1,$params['limit']);
				}else if(!$getallphotos && isset($params['limit']) && $params['limit'] < 1)
					return;
				if($getallphotos)
					return Zend_Paginator::factory($selectMyFriends);;
				if($toArray){
				 $photo = $parentTable->fetchAll($selectMyFriends);
				if(!empty($photo))
					$photo = $photo->toArray();
				else 
					$photo = '';
				}else{
					 $photo = $parentTable->fetchRow($selectMyFriends);
				}
			return $photo;
			break;
			case 'tagged_photo':
				if(empty($params['user']))
					return ;
				$userId = $params['user'];
				$tableTagmap = Engine_Api::_()->getDbtable('tagMaps', 'core');
				$tableTagName = $tableTagmap->info('name');
				$albumTable = Engine_Api::_()->getItemTable('album');
				$albumTableName = $albumTable->info('name');
				$photoTable = Engine_Api::_()->getItemTable('photo');
				$photoTableName = $photoTable->info('name');
				$selecttagged_photo = $tableTagmap->select()
					->from($tableTagName)
					->setIntegrityCheck(false)
					->where($albumTableName.'.album_id != ?','')
					->joinLeft($photoTableName, $tableTagName . '.resource_id=' . $photoTableName . '.photo_id');
				$selecttagged_photo->joinLeft($albumTableName, $albumTableName . '.album_id=' . $photoTableName . '.album_id', array());	
				$selecttagged_photo->where($tableTagName . '.resource_type = ?', "album_photo")
				->where($tableTagName . '.tag_id = ?', $userId);
				if(isset($params['limit']) && $params['limit'] > -1 && !$getallphotos){
					$selecttagged_photo->limit(1,$params['limit']);
				}else if(isset($params['limit']) && $params['limit'] > 0 && !$getallphotos){
					$selecttagged_photo->limit(1,$params['limit']);
				}else if(!$getallphotos && isset($params['limit']) && $params['limit'] < 1)
					return;
				if($getallphotos)
					return Zend_Paginator::factory($selecttagged_photo);
				if($toArray){
				 $photo = $tableTagmap->fetchAll($selecttagged_photo);
				if(!empty($photo))
					$photo = $photo->toArray();
				else 
					$photo = '';
				}else{
					 $photo = $tableTagmap->fetchRow($selecttagged_photo);
				}
				return $photo;
			break;
			case 'member-featured':				
				$tableFeatured = Engine_Api::_()->getDbTable('featuredphotos','sesmember');
				$tabelNameFeatured = $tableFeatured->info('name');
				if($getallphotos){
					$selectFeaturedData = $tableFeatured->select()->from($tabelNameFeatured,'photo_id')->where('user_id = ?',$photo->owner_id);
					return Zend_Paginator::factory($selectFeaturedData);
				}
				$photoTable = Engine_Api::_()->getItemTable('photo');
				$photoTableName = $photoTable->info('name');
				$albumTable = Engine_Api::_()->getItemTable('album');
				$albumTableName = $albumTable->info('name');
				$selectFeatured = $photoTable->select()
							->from($photoTableName)
							->setIntegrityCheck(false)
							->where ("$albumTableName.album_id != ?",'')
							->where($tabelNameFeatured.".user_id = $photo->owner_id")
							->joinRight($tabelNameFeatured, $tabelNameFeatured . '.photo_id = ' . $photoTableName . '.photo_id',array())
							->joinLeft($albumTableName, $albumTableName . '.album_id = ' . $photoTableName . '.album_id',array());
							
				
				if(isset($params['limit']) && $params['limit'] > -1 && !$getallphotos){
					$selectFeatured->limit(1,$params['limit']);
				}else if(isset($params['limit']) && $params['limit'] > 0 && !$getallphotos){
					$selectFeatured->limit(1,$params['limit']);
				}else if(!$getallphotos && isset($params['limit']) && $params['limit'] < 1)
					return;
				if($toArray){
				 $photo = $photoTable->fetchAll($selectFeatured);
				if(!empty($photo))
					$photo = $photo->toArray();
				else 
					$photo = '';
				}else{
					 $photo = $photoTable->fetchRow($selectFeatured);
				}
				return $photo;
			break;
			case 'offtheday':
			 if($getallphotos){
				$photoTable = Engine_Api::_()->getItemTable('photo');
				$photoTableName = $photoTable->info('name');
				$albumTable = Engine_Api::_()->getItemTable('album');
				$albumTableName = $albumTable->info('name');
				$selectOfftheday = $photoTable->select()
							->from($photoTableName)
							->setIntegrityCheck(false)
							->where ("$albumTableName.album_id != ?",'')
							->where($photoTableName.".album_id = $photo->album_id")
							->where($photoTableName.".photo_id = $photo->photo_id")
							->joinLeft($albumTableName, $albumTableName . '.album_id = ' . $photoTableName . '.album_id',array());
						//limit OFFSET if available 
			
				if($getallphotos)
					return Zend_Paginator::factory($selectOfftheday);;
			
			 }
			return;
			break;
			default:
				if(!$getallphotos)
					$select->where("$tableNamePhoto.order $nextPreviousCondition (SELECT `order` FROM $tableNamePhoto WHERE photo_id = $photo->photo_id )");
				$select->where("$tableNamePhoto.album_id =  ?",$photo->album_id);
			break;
		}		
		if($getallphotos){
				$select->order('order ASC');
				return  Zend_Paginator::factory($select);
		}else{
		if($nextPreviousCondition == '>' && !isset($noCondition))
			$select->order('order ASC');
		else if(!isset($noCondition))
			$select->order('order DESC');
		}
			if($toArray){
				 $photo = $GetTableNamePhoto->fetchAll($select);
				if(!empty($photo))
					$photo = $photo->toArray();
				else 
					$photo = '';
			}else{
			 $photo = $GetTableNamePhoto->fetchRow($select);
			}
		return $photo;
	}
	public function getOfTheDayResults() {
    $select = $this->select()
            ->from($this->info('name'), array('*'))
            ->where('offtheday =?', 1)
						->where('album_id != ?','0')
            ->where('starttime <= DATE(NOW())')
            ->where('endtime >= DATE(NOW())')
            ->order('RAND()');
		//store data in 
		$tempArray = array();
		$viewer = Engine_Api::_()->user()->getViewer();
		$tempStorePhotoIds = '';
		$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
		if ($album_enable_check_privacy)
		{
			//fecth all
			$photos = $this->fetchAll($select);
			//loop over all albums once
			foreach ($photos as $photo)
			{
				$album = $photo->getParent();
				if(!$album)
					continue;
				//check authorization album
				if ($album->authorization()->isAllowed($viewer, 'view'))
				{
					$tempStorePhotoIds .=$photo->getIdentity();
				}
				if($tempStorePhotoIds != '')
					break;
			}
			$tempStorePhotoIds = trim($tempStorePhotoIds,',');
				if($tempStorePhotoIds){
						$select = $select->where('photo_id IN (' . $tempStorePhotoIds . ')');
				}else{
						$select = $select->where('photo_id IN (0)');
				}
		}				
    return $this->fetchRow($select);
  }
}