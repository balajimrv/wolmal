<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Album.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Model_Album extends Core_Model_Item_Abstract implements Countable {
  protected $_parent_type = 'user';
  protected $_owner_type = 'user';
  protected $_parent_is_owner = true;
	protected $_type = 'album';
	public function fields()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getApi('core', 'fields'));
  }
  public function getHref($params = array()) {
    $params = array_merge(array(
        'route' => 'sesalbum_specific_album',
        'reset' => true,
				'slug' => $this->getSlug(),
        'album_id' => $this->getIdentity(),
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }
  public function getPhotoUrl($type = null,$status = false,$string = null) {
		
		$viewer = Engine_Api::_()->user()->getViewer();
		if(!Engine_Api::_()->getApi('core', 'sesbasic')->checkAdultContent(array('module'=>'sesalbum')) && $this->adult && !$string && $this->owner_id != $viewer->getIdentity()){
			 return Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum_album_default_adult', 'application/modules/Sesalbum/externals/images/sesalbum_adult.png');	
		}
		
		$cookieData = isset($_COOKIE['sesalbum_lightbox_value']) ? $_COOKIE['sesalbum_lightbox_value'] : '';
		$valid = false;
		if(in_array($this->album_id,explode(',',$cookieData))){
			$valid = true;
		}
		if(!$string && !$valid){
			
			if ($viewer->getIdentity() == 0)
				$level = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
			else
				$level = $viewer;
			$sesprofilelock_enable_module = (array)Engine_Api::_()->getApi('settings', 'core')->getSetting('sesprofilelock.enable.modules');
			if ($this->is_locked && Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('sesprofilelock'))  && in_array('sesalbum',$sesprofilelock_enable_module) && $this->owner_id != $viewer->getIdentity())
				return 'application/modules/Sesalbum/externals/images/locked-album.jpg';
		}
    if (empty($this->photo_id)) {
      $photoTable = Engine_Api::_()->getItemTable('album_photo');
      $photoInfo = $photoTable->select()
              ->from($photoTable, array('photo_id', 'file_id'))
              ->where('album_id = ?', $this->album_id)
              ->order('order ASC')
              ->limit(1)
              ->query()
              ->fetch();
      if (!empty($photoInfo)) {
        $this->photo_id = $photo_id = $photoInfo['photo_id'];
        $this->save();
        $file_id = $photoInfo['file_id'];
      } else {
			 if(!$status)
        return 'application/modules/Sesalbum/externals/images/nophoto_album_thumb_normal.png?c=direct';
			 else
			 	return '';
      }
    } else {
      $photoTable = Engine_Api::_()->getItemTable('album_photo');
      $file_id = $photoTable->select()
              ->from($photoTable, 'file_id')
              ->where('photo_id = ?', $this->photo_id)
              ->query()
              ->fetchColumn();
    }
    if (!$file_id) {
			 $albumTable = Engine_Api::_()->getItemTable('album');
			$albumTableName = $albumTable->info('name');
      $file_id = $albumTable->select()
              ->from($albumTableName, 'art_cover')
              ->where('album_id = ?', $this->album_id)
              ->query()
              ->fetchColumn();
			$direct = true;
		 if (!$file_id) {
			if(!$status)
			 return 'application/modules/Sesalbum/externals/images/nophoto_album_thumb_normal.pngc=direct';
			else
				return '';
		 }
    }
    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, $type);
    if (!$file) {
      if(!$status)
			 return 'application/modules/Sesalbum/externals/images/nophoto_album_thumb_normal.pngc=direct';
			else
				return '';
    }
		if(isset($direct) && $direct)
			$direct = 'direct';
		else
			$direct = '';
    return $file->map().$direct;
  }
  public function getFirstPhoto() {
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    $select = $photoTable->select()
            ->where('album_id = ?', $this->album_id)
            ->order('order ASC')
            ->limit(1);
    return $photoTable->fetchRow($select);
  }
  public function getLastPhoto() {
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    $select = $photoTable->select()
            ->where('album_id = ?', $this->album_id)
            ->order('order DESC')
            ->limit(1);
    return $photoTable->fetchRow($select);
  }
  public function count() {
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    return $photoTable->select()
                    ->from($photoTable, new Zend_Db_Expr('COUNT(photo_id)'))
                    ->where('album_id = ?', $this->album_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }
  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }
  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes() {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }
  protected function _postDelete() {
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    $photoSelect = $photoTable->select()
            ->where('album_id = ?', $this->getIdentity())
    ;
    foreach ($photoTable->fetchAll($photoSelect) as $photo) {
      //$photo->skipAlbumDeleteHook = true;
      try {
        $photo->delete();
      } catch (Exception $e) {
        // Silence
      }
    }
			/*remove location data from location table*/		
		$locationTable = Engine_Api::_()->getDbTable('locations', 'sesbasic');
		$locationTableName = $locationTable->info('name');
		$locationSelect = $locationTable->select()
						->from($locationTableName, array('location_id'))
						->where('resource_id = ?', $this->photo_id)
						->where('resource_type = ?', 'sesalbum_photo');
		$locations = $locationTable->fetchAll($locationSelect);
		foreach ($locations as $location) {
			$location->delete();
		}
    parent::_postDelete();
  }
	public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
	public function setCoverPhoto($photo){
			if( $photo instanceof Zend_Form_Element_File ) {
				$file = $photo->getFileName();
				$fileName = $file;
			} else if( $photo instanceof Storage_Model_File ) {
				$file = $photo->temporary();
				$fileName = $photo->name;
			} else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
				$tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
				$file = $tmpRow->temporary();
				$fileName = $tmpRow->name;
			} else if( is_array($photo) && !empty($photo['tmp_name']) ) {
				$file = $photo['tmp_name'];
				$fileName = $photo['name'];
			} else if( is_string($photo) && file_exists($photo) ) {
				$file = $photo;
				$fileName = $photo;
				$unlink = false;
			} else {
				throw new User_Model_Exception('invalid argument passed to setPhoto');
			}
			  $name = basename($file);
				$extension = ltrim(strrchr($fileName, '.'), '.');
				$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		
    if( !$fileName ) {
      $fileName = $file;
    }
		 $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity(),
      'user_id' => $this->owner_id,
      'name' => $fileName,
    );
    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->autoRotate()
      ->resize(1200, 700)
      ->write($mainPath)
      ->destroy();
		
    // Store
    try {
      $iMain = $filesTable->createFile($mainPath, $params);      
    } catch( Exception $e ) {
			@unlink($file);
      // Remove temp files
      @unlink($mainPath);
     
      // Throw
      if( $e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE ) {
        throw new Sesalbum_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }
    	if(!isset($unlink))
				@unlink($file);
    // Remove temp files
      @unlink($mainPath);
     
    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->art_cover = $iMain->file_id;
    $this->save();
    // Delete the old file?
    if( !empty($tmpRow) ) {
      $tmpRow->delete();
    }
    return $this;
  	
	}
}?>