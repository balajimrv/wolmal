<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Albums.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Model_DbTable_Albums extends Engine_Db_Table
{ 
	protected $_rowClass = 'Sesalbum_Model_Album';
  protected $_name = 'album_albums';
	public function getAlbumPaginator()
  { 
    return $this->getAlbumSelect();
  }
	public function getUserAlbum(){
			$viewer = Engine_Api::_()->user()->getViewer();
			$tableName = $this->info('name');
			$select = $this->select()
				->from($tableName)
				->where('owner_id =?',$viewer->getIdentity())
				->order('type DESC')
        ->where('draft =?',0);
		
			return Zend_Paginator::factory($select);	
	}
	public function getUserAlbumCount($params = array()){
		return $this->select()->from($this->info('name'), new Zend_Db_Expr('COUNT(album_id) as total_albums'))->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())->where('type IS NULL')->where('draft =?',0)->limit(1)->query()->fetchColumn();
	}
	public function editPhotos(){
		$albumTable = Engine_Api::_()->getItemTable('album');
    $myAlbums = $albumTable->select()
            ->from($albumTable, array('album_id', 'title'))
            ->where('owner_type = ?', 'user')
            ->where('draft =?',0)
            ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
            ->query()
            ->fetchAll();	
	 return $myAlbums;
	}
	public function getAlbumsAction($params = array()){
		$album_table = Engine_Api::_()->getDbtable('albums', 'sesalbum');
    $albumTableName = $album_table->info('name');
    $select = $album_table->select()
            ->from($albumTableName)
            ->setIntegrityCheck(false)
            ->where('search =?', true)
            ->where('draft =?',0)
            ->where("title  LIKE ?", '%' . $params['text'] . '%')
           	->order($albumTableName . '.album_id ASC')->limit('10')
            ->group($albumTableName . '.album_id');	
	 if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1))
    	$select->where($albumTableName.'.type IS NULL');
	 return $album_table->fetchAll($select);
	}
	public function getAlbumSelect($value = array()){
		 // Prepare data
		$albumTableName = $this->info('name');
		$tableTagmap = Engine_Api::_()->getDbtable('tagMaps', 'core');
		$tableTagName = $tableTagmap->info('name');
		$tableTag = Engine_Api::_()->getDbtable('tags', 'core');
		$tableMainTagName = $tableTag->info('name');
    $select = $this->select()
										->from($albumTableName)
										->setIntegrityCheck(false)
                    ->where('draft =?',0)
										->where('search =?',true)
										->where($albumTableName.'.photo_id !=?','')
										->group($albumTableName.'.album_id');
	if(!isset($value['order']))
		$value['order'] = '';
	
    if( !in_array($value['order'], $this->info('cols')) )
      $value['order'] = 'modified_date';
	if(isset($value['lat']) && isset($value['lng']) && $value['lat'] != '' && $value['lng'] != '' && isset($value['location']) && $value['location'] != ''){
		$tableLocation = Engine_Api::_()->getDbtable('locations', 'sesbasic');
		$tableLocationName = $tableLocation->info('name');
		$origLat = $value['lat'];
		$origLon = $value['lng'];
		if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.search.type',1) == 1){
			$searchType = 3956;
		}else
			$searchType = 6371;
		$dist = $value['miles'];//This is the maximum distance (in miles) away from $origLat, $origLon in which to search
		$select->joinLeft($tableLocationName, $tableLocationName . '.resource_id = ' . $albumTableName . '.album_id AND '.$tableLocationName . '.resource_type = "sesalbum_album" ',$searchType." * 2 * ASIN(SQRT( POWER(SIN(($origLat - abs(lat))*pi()/180/2),2) + COS($origLat*pi()/180 )*COS(abs(lat)*pi()/180) *POWER(SIN(($origLon-lng)*pi()/180/2),2))) as distance");
		$select->where($tableLocationName.".lng between ($origLon-$dist/abs(cos(radians($origLat))*69)) and ($origLon+$dist/abs(cos(radians($origLat))*69)) and ".$tableLocationName.".lat between ($origLat-($dist/69)) and ($origLat+($dist/69))");
		$select->order('distance');
		$select->having("distance < $dist");
	}else
		$select->order($value['order'] . ' DESC');	
	if(isset($value['order']) && $value['order'] == 'is_sponsored' && $value['order'] == 'is_featured')
		$select->order('view_count DESC');
	if(isset($value['fixedDataAlbum']) && $value['fixedDataAlbum'] != '')
		$select->where($value['fixedDataAlbum']);
		if(isset($value['user_id']) && intval($value['user_id'])) $select->where('owner_id = ?',$value['user_id']);
	if(empty($value['user_id']) || (isset($value['user_id']) && $value['user_id'] != Engine_Api::_()->user()->getViewer()->getIdentity())){
		if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1))
			$select->where('type IS NULL');
	}
		if (isset($value['category_id']) && intval($value['category_id'])){
			 $select->where("category_id = ?", $value['category_id']);
			 if (isset($value['subcat_id']) && intval($value['subcat_id'])) $select->where("subcat_id = ?", $value['subcat_id']);
			 if (isset($value['subsubcat_id']) && intval($value['subsubcat_id'])) $select->where("subsubcat_id = ?", $value['subsubcat_id']);		
		}
		if (isset($value['order']) && ($value['order']) && $value['order'] == 'is_featured') $select->where("is_featured = ?", 1); 
		if (isset($value['order']) && ($value['order']) && $value['order'] == 'is_sponsored') $select->where("is_sponsored = ?", 1);
		if(isset($value['tag_id']) && intval($value['tag_id'])){
			$select->joinLeft($tableTagName, $tableTagName . '.resource_id=' . $albumTableName . '.album_id',null)
										->joinLeft($tableMainTagName, $tableMainTagName . '.tag_id = ' . $tableTagName . '.tag_id',null);
			$select->where("$tableTagName. tag_id  = ?",$value['tag_id']);
			$select->where($tableTagName.'.resource_type =?','album');
      if (isset($value['search']) && $value['search'] != '') {
        $select->where("title  LIKE ?  OR  $tableMainTagName.text LIKE ? ", '%' . $value['search'] . '%');
      }
      $search = true;
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if (isset($value['show']) && $value['show'] == 2 && $viewer->getIdentity()) {
      $users = $viewer->membership()->getMembershipsOfIds();
			$select->where($albumTableName.'.owner_id IN (?)',$users);
    }
    if(empty($search)) {
      if (isset($value['search']) && $value['search'] != '') {
        $select->where("title  LIKE ? ", '%' . $value['search'] . '%');
      }
    }
		if( !empty($value['owner']) && 
        $value['owner'] instanceof Core_Model_Item_Abstract ) {
      $select
        ->where("$albumTableName.owner_type = ?", $value['owner']->getType())
        ->where("$albumTableName.owner_id = ?", $value['owner']->getIdentity());
			return $select;
    }
    
		//fecth all
		$albums = $this->fetchAll($select);
		//store data in 
		$tempArray = array();
		$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
		if ($album_enable_check_privacy)
		{
			//loop over all albums once
			foreach ($albums as $album)
			{
				//check authorization album
				if ($album->authorization()->isAllowed($viewer, 'view'))
				{
					$tempArray[] = $album;
				}
			}
			return Zend_Paginator::factory($tempArray);
		}
		return Zend_Paginator::factory($select);
	}
	public function getAlbums($params = array(),$paginator = true){
			$tableName = $this->info('name');
			$vcName = Engine_Api::_()->getDbtable('photos', 'sesalbum');
			$vcmName = $vcName->info('name');
			$select = $this->select()
				->from($tableName)
				->setIntegrityCheck(false)
        ->where('draft =?',0)
				->joinLeft($vcmName, "$vcmName.album_id = $tableName.album_id", array("total_photos"=>"COUNT($vcmName.photo_id)"))
				->group("$vcmName.album_id");
			if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1))
      	$select->where('type IS NULL');
			if(isset($params['popularity_album'])){
					switch($params['popularity_album']){
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
						case 'featured':
								$select->order('is_featured DESC');
						break;
						case 'sponsored':
								$select->order('is_sponsored DESC');
						break;
				 }
			 }else{
					if(!empty($params['order']))
						$select = $select->order($tableName.'.view_count DESC');
					else
						$select = $select->order("$tableName.album_id DESC");
			 }
			if(!empty($params['category_id']))
				$select = $select->where($tableName.'.category_id =?',$params['category_id']);
			if(!empty($params['subcat_id']))
				$select = $select->where($tableName.'.subcat_id =?',$params['subcat_id']);
			if(!empty($params['subsubcat_id']))
				$select = $select->where($tableName.'.subsubcat_id =?',$params['subsubcat_id']);
			if(!empty($params['album_id']))
				$select = $select->where($tableName.'.album_id =?',$params['album_id']);
			
			
		
		//store data in 
		$tempArray = array();
		$tempStorePhotoIds = '';
		$viewer = Engine_Api::_()->user()->getViewer();
		$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
		if ($album_enable_check_privacy)
		{
			//fecth all
			$albums = $this->fetchAll($select);
			//loop over all albums once
			foreach ($albums as $album)
			{
				//check authorization album
				if ($album->authorization()->isAllowed($viewer, 'view'))
				{
					$tempArray[] = $album;
					$tempStorePhotoIds .= $album->getIdentity().',';
					if(!empty($params['limit_data']) && count($tempArray) >= $params['limit_data'])
						break;
				}
			}
			if(!empty($params['limit_data'])){
				if($tempStorePhotoIds){
							$tempStorePhotoIds = trim($tempStorePhotoIds,',');
							$select = $select->where($tableName.'.album_id IN (' . $tempStorePhotoIds . ')');
					}else{
							$select = $select->where($tableName.'.album_id IN (0)');
					}
			}else
				return Zend_Paginator::factory($tempArray);
		}	
			if(!empty($params['limit_data']))
				$select = $select->limit($params['limit_data']);
			if($paginator)
				$paginator = $this->fetchAll($select);
			else
				$paginator = Zend_Paginator::factory($select);
			return  $paginator;
	}
	public function countAlbums(){
		return $this->select()->from($this->info('name'), new Zend_Db_Expr('COUNT(album_id) as total_albums'))->limit(1)->query()->fetchColumn();
	}
	public function featuredSponsored($value = array()){
			$select = $this->select()->from($this->info('name'),array('*'))->where('draft =?',0);
			if($value['criteria'] == 1){
				 $select->where($this->info('name').'.is_featured =?','1');
 		  }else if($value['criteria'] == 2){
			 $select->where($this->info('name').'.is_sponsored =?','1');
		  }else if($value['criteria'] == 3){
				$select->where($this->info('name').'.is_featured = 1 OR '.$this->info('name').'.is_sponsored = 1');
		  }else if($value['criteria'] == 4){
			 $select->where($this->info('name').'.is_featured = 0 AND '.$this->info('name').'.is_sponsored = 0');
		  }
			if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1))
      	$select->where($this->info('name').'.type IS NULL');
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
			case 'most_commented':
				$select->order('comment_count DESC');
				break;
			case 'most_download':
				$select->order('download_count DESC');
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
			$albums = $this->fetchAll($select);
			//loop over all albums once
			foreach ($albums as $album)
			{
				//check authorization album
				if ($album->authorization()->isAllowed($viewer, 'view'))
				{
					$tempArray[] = $album;
				}
			}
				return Zend_Paginator::factory($tempArray);
		}	
			return  Zend_Paginator::factory($select);
	}
	public function getSpecialAlbum(User_Model_User $user, $type)
  {
    if( !in_array($type, array('wall', 'profile', 'message', 'blog','forum','group','event')) ) {
      throw new Sesalbum_Model_Exception('Unknown special album type');
    }
    $select = $this->select()
        ->where('owner_type = ?', $user->getType())
        ->where('owner_id = ?', $user->getIdentity())
        ->where('type = ?', $type)
        ->order('album_id ASC')
        ->limit(1);
    $album = $this->fetchRow($select);
    // Create wall photos album if it doesn't exist yet
    if( null === $album ) {
      $translate = Zend_Registry::get('Zend_Translate');
      $album = $this->createRow();
      $album->owner_type = 'user';
      $album->owner_id = $user->getIdentity();
      $album->title = $translate->_(ucfirst($type) . ' Photos');
      $album->type = $type;
      if( $type == 'message' ) {
        $album->search = 0;
      } else {
        $album->search = 1;
      }
      $album->save();
      // Authorizations
      if( $type != 'message' ) {
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($album, 'everyone', 'view',    true);
        $auth->setAllowed($album, 'everyone', 'comment', true);
      }
    }
    return $album;
  }	
	public function profileAlbums($params = array()){
		$parentTable = Engine_Api::_()->getItemTable('album');
		$parentTableName = $parentTable->info('name');			
		$select = $parentTable->select()
			->from($parentTableName)
      ->where('draft =?',0)
			->where($parentTableName . '.owner_id = ?',$params['userId']);
			
	if(!isset($params['allowSpecialAlbums'])){
		if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1) && isset($params['widget']))
      	$select->where('type IS NULL');
	}
	if(isset($params['join'])){
		$tableRelated = Engine_Api::_()->getDbtable('relatedalbums', 'sesalbum');
		$tableRelated = $tableRelated->info('name');
		$select->setIntegrityCheck(false)
					 ->joinLeft($tableRelated, $tableRelated . '.album_id = ' . $this->info('name') . '.album_id AND '.$tableRelated.'.resource_id = '.$params['album_id'],array('relatedalbum_id'))
					 ->order('relatedalbum_id DESC');;	
	}
	if(isset($params['notInclude']))
	 $select->where($parentTableName.'.album_id != '.$params['notInclude']);
	$select->order('creation_date DESC');
		if(isset($params['is_featured']))
			$select = $select->where('is_featured =?',1);
		if(isset($params['is_sponsored']))
			$select = $select->where('is_sponsored =?',1);
		if(isset($params['photo_id']))
			$select = $select->where('photo_id !=?','');
		if(isset($params['limit_data']))
				$select = $select->limit($params['limit_data']);
				
		//store data in 
		$tempArray = array();
		$viewer = Engine_Api::_()->user()->getViewer();
		$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
		
		if ($album_enable_check_privacy)
		{
			//fecth all
			$albums = $this->fetchAll($select);
			//loop over all albums once
			foreach ($albums as $album)
			{
				//check authorization album
				if ($album->authorization()->isAllowed($viewer, 'view'))
				{
					$tempArray[] = $album;
				}
			}
				return Zend_Paginator::factory($tempArray);
		}
		
		return Zend_Paginator::factory($select);
	}
	public function tabWidgetAlbums($params){
		$tableName = $this->info('name');
		$new_select = $this->select()
			->from($tableName)
      ->where('draft =?',0)
			->where($tableName.'.photo_id !=?','')
			->order($params['popularCol'] . ' DESC');
		if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.wall.profile', 1))
			$new_select->where('type IS NULL');
		if(isset($params['fixedData']) && $params['fixedData'] != ''){
			$new_select = $new_select->where($tableName.'.'.$params['fixedData'].' =?',1);	
		}
		
		//store data in 
		$tempArray = array();
		$viewer = Engine_Api::_()->user()->getViewer();
		$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
		
		if ($album_enable_check_privacy)
		{
			//fecth all
			$albums = $this->fetchAll($new_select);
			//loop over all albums once
			foreach ($albums as $album)
			{
				//check authorization album
				if ($album->authorization()->isAllowed($viewer, 'view'))
				{
					$tempArray[] = $album;
				}
			}
				return Zend_Paginator::factory($tempArray);
		}
		return  Zend_Paginator::factory($new_select);
	}
	public function getFavourite($params = array()){
		$tableFav = Engine_Api::_()->getDbtable('favourites', 'sesalbum');
		$tableFav = $tableFav->info('name');
		$select = $this->select()
							->from($this->info('name'))
              ->where('draft =?',0)
							->where('album_id = ?',$params['resource_id'])
							->setIntegrityCheck(false)
							->where('resource_type =?','album')
							->joinLeft($tableFav, $tableFav . '.resource_id=' . $this->info('name') . '.album_id',array('user_id'));		
	 
		//store data in 
		$tempArray = array();
		$viewer = Engine_Api::_()->user()->getViewer();
		$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
		if ($album_enable_check_privacy)
		{
			//fecth all
			$albums = $this->fetchAll($select);
			//loop over all albums once
			foreach ($albums as $album)
			{
				//check authorization album
				if ($album->authorization()->isAllowed($viewer, 'view'))
				{
					$tempArray[] = $album;
				}
			}
				return Zend_Paginator::factory($tempArray);
		}
			return  Zend_Paginator::factory($select);
	}
	public function getOfTheDayResults() {
    $select = $this->select()
            ->from($this->info('name'), array('*'))
            ->where('offtheday =?', 1)
            ->where('draft =?',0)
            ->where('starttime <= DATE(NOW())')
            ->where('endtime >= DATE(NOW())')
            ->order('RAND()');
						
		//store data in 
		$tempStorePhotoIds = '';
		$viewer = Engine_Api::_()->user()->getViewer();
		$album_enable_check_privacy = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('sesalbum.enable.check.privacy', 0);
		if ($album_enable_check_privacy)
		{
			//fecth all
			$albums = $this->fetchAll($new_select);
			//loop over all albums once
			foreach ($albums as $album)
			{
				//check authorization album
				if ($album->authorization()->isAllowed($viewer, 'view'))
				{
					$tempStorePhotoIds .= $album->getIdentity();
				}
				if($tempStorePhotoIds != '')
					break;
			}
			$tempStorePhotoIds = trim($tempStorePhotoIds,',');
				if($tempStorePhotoIds){
						$select = $select->where('album_id IN (' . $tempStorePhotoIds . ')');
				}else{
						$select = $select->where('album_id IN (0)');
				}
		}	
		return	$this->fetchRow($select);
  }
}