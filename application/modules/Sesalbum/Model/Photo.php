<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Photo.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Model_Photo extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = array('title', 'description', 'search');
  protected $_type = 'album_photo';
  public $skipAlbumDeleteHook;

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'sesalbum_extended',
      'reset' => true,
      'controller' => 'photo',
      'action' => 'view',
      'album_id' => $this->album_id,
      'photo_id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, $reset);
  }
  
  public function getAlbum()
  {
    return Engine_Api::_()->getItem('album', $this->album_id);
  }

  public function getParent($type = null)
  {
    if( null === $type || $type === 'album' ) {
      return $this->getAlbum();
    } else {
      return $this->getAlbum()->getParent($type);
    }
  }

  /**
   * Gets a url to the current photo representing this item. Return null if none
   * set
   *
   * @param string The photo type (null -> main, thumb, icon, etc);
   * @return string The photo url
   */
  public function getPhotoUrl($type = null,$data = null,$string = null)
  { 
    $viewer = Engine_Api::_()->user()->getViewer();
    $photo_id = $this->file_id;
		if(!Engine_Api::_()->getApi('core', 'sesbasic')->checkAdultContent(array('module'=>'sesalbum')) && $this->getParent()->adult && !$string && $this->owner_id != $viewer->getIdentity()){
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
				if ($this->getParent()->is_locked && Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('sesprofilelock'))  && in_array('sesalbum',$sesprofilelock_enable_module) && $this->owner_id != $viewer->getIdentity())
					return 'application/modules/Sesalbum/externals/images/locked-album.jpg';
		}
    if( !$photo_id ) {
     return 'application/modules/Sesalbum/externals/images/nophoto_album_thumb_normal.pngc=direct';
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo_id, $type);
    if( !$file ) {
      return 'application/modules/Sesalbum/externals/images/nophoto_album_thumb_normal.pngc=direct';
    }

    return $file->map();
  }

  public function isSearchable()
  {
    $album = $this->getAlbum();
    if( !($album instanceof Core_Model_Item_Abstract) ) {
      return false;
    }
    return $album->isSearchable();
  }

  public function getAuthorizationItem()
  {
    return $this->getAlbum();
  }

  public function isOwner(Core_Model_Item_Abstract $user)
  {
    if( empty($this->album_id) ) {
      return (($this->owner_id == $user->getIdentity()) && ($this->owner_type == $user->getType()));
    }
    return parent::isOwner($user);
  }
	//watermark on photo
	function watermark_image($oldimage_name, $new_image_name,$type,$image_path){
		ini_set('memory_limit','1024M');
    list($owidth,$oheight) = getimagesize($oldimage_name);
    $width = $sourcefile_width =$owidth; $height = $sourcefile_height = $oheight;
    $im = imagecreatetruecolor($width, $height);
		if(strpos(strtolower($type),'png') !== FALSE)
			$image_type = 'png';
		else if(strpos(strtolower($type),'jpg')  !== FALSE || strpos(strtolower($type),'jpeg')  !== FALSE )
			$image_type = 'jpeg';
		else if( strpos(strtolower($type),'gif') !== FALSE)
			$image_type = 'gif';
		switch ($image_type)
    {
      case 'gif': $img_src = imagecreatefromgif($oldimage_name); break;
      case 'jpeg': $img_src = imagecreatefromjpeg($oldimage_name); break;
      case 'png': $img_src = imagecreatefrompng($oldimage_name); break;
      default:  return false;break;
    }
		imagecopyresampled($im, $img_src, 0, 0, 0, 0, $width, $height, $owidth, $oheight);
    $watermark = imagecreatefrompng($image_path);
    list($insertfile_width, $insertfile_height) = getimagesize($image_path);        
		
    $pos = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.position.watermark', 0);
		//middle 
    if( $pos == 0 ) 
    { 
        $dest_x = ( $sourcefile_width / 2 ) - ( $insertfile_width / 2 ); 
        $dest_y = ( $sourcefile_height / 2 ) - ( $insertfile_height / 2 ); 
    } 
		//top left 
    else if( $pos == 1 ) 
    { 
        $dest_x = 0; 
        $dest_y = 0; 
    } 
//top right 
   else if( $pos == 2 ) 
    { 
        $dest_x = $sourcefile_width - $insertfile_width; 
        $dest_y = 0; 
    } 

//bottom right 
    else if( $pos == 3 ) 
    { 
        $dest_x = $sourcefile_width - $insertfile_width; 
        $dest_y = $sourcefile_height - $insertfile_height; 
    } 

//bottom left    
    else if( $pos == 4 ) 
    { 
        $dest_x = 0; 
        $dest_y = $sourcefile_height - $insertfile_height; 
    } 

//top middle 
    else if( $pos == 5 ) 
    { 
        $dest_x = ( ( $sourcefile_width - $insertfile_width ) / 2 ); 
        $dest_y = 0; 
    } 

//middle right 
    else if( $pos == 6 ) 
    { 
        $dest_x = $sourcefile_width - $insertfile_width; 
        $dest_y = ( $sourcefile_height / 2 ) - ( $insertfile_height / 2 ); 
    } 
        
//bottom middle    
    else if( $pos == 7 ) 
    { 
        $dest_x = ( ( $sourcefile_width - $insertfile_width ) / 2 ); 
        $dest_y = $sourcefile_height - $insertfile_height; 
    } 

//middle left 
    else if( $pos == 8 ) 
    { 
        $dest_x = 0; 
        $dest_y = ( $sourcefile_height / 2 ) - ( $insertfile_height / 2 ); 
    } 
    
    
    imagecopy($im, $watermark, $dest_x, $dest_y, 0, 0, $insertfile_width, $insertfile_height);
    imagejpeg($im, $new_image_name, 100);
    imagedestroy($im);
    @unlink($oldimage_name);
    return true;
	}
	//upload photo
  public function setPhoto($photo,$isURL = false,$isUploadDirect = false,$viewer = false)
  {
    if(!$viewer)
      $viewer = Engine_Api::_()->user()->getViewer();
		if(!$isURL){
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
				$fileName = basename($photo);
			} else {
				throw new User_Model_Exception('invalid argument passed to setPhoto');
			}
			  $name = basename($file);
				$extension = ltrim(strrchr($fileName, '.'), '.');
				$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		}else{
			$fileName = time().'_sesalbum';
      $imageName = $photo;
      $photo = current(explode('?',$photo));
			$PhotoExtension='.'.pathinfo($photo, PATHINFO_EXTENSION);
			$filenameInsert=$fileName.$PhotoExtension;
			$copySuccess=@copy($imageName, APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.$filenameInsert);
			if($copySuccess)
				$file = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'.DIRECTORY_SEPARATOR.$filenameInsert;
			else	
				return false;
			$name = basename($photo);
			$extension = ltrim(strrchr($name, '.'), '.');
			$base = rtrim(substr(basename($name), 0, strrpos(basename($name), '.')), '.');
		}
    if( !$fileName ) {
      $fileName = $file;
    }
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity(),
      'user_id' => $this->owner_id,
      'name' => $fileName,
    );
    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
		/*setting of image dimentions from core settings*/
		$core_settings = Engine_Api::_()->getApi('settings', 'core');
    $main_height = $core_settings->getSetting('sesalbum.mainheight', 1600);
		$main_width = $core_settings->getSetting('sesalbum.mainwidth', 1600);
		$normal_height = $core_settings->getSetting('sesalbum.normalheight', 500);
		$normal_width = $core_settings->getSetting('sesalbum.normalwidth', 500);
    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->autoRotate()
      ->resize($main_width, $main_height)
      ->write($mainPath)
      ->destroy();
		// Resize image (normal) make same image for activity feed so it open in pop up with out jump effect.
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->autoRotate()
      ->resize($normal_width, $normal_height)
      ->write($normalPath)
      ->destroy();
		//watermark on main photo
		if(!$isUploadDirect){
			$enableWatermark = $core_settings->getSetting('sesalbum.watermark.enable', 0);
			if($enableWatermark == 1){
			$watermarkImage = Engine_Api::_()->authorization()->getPermission($viewer->level_id,'album', 'watermark');
				if(is_file($watermarkImage)){
					if(isset($extension))
						$type = $extension;
					else
						$type = $PhotoExtension;
					$mainFileUploaded =   APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'.DIRECTORY_SEPARATOR.$name;
					$fileName = current(explode('.',$fileName));
					$fileNew = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'.DIRECTORY_SEPARATOR.time().'_'.$fileName.".jpg";	
					$watemarkImageResult = $this->watermark_image($mainPath, $fileNew,$type,$watermarkImage);
					if($watemarkImageResult){
						@unlink($mainPath);
						$image->open($fileNew)
                  ->autoRotate()
									->resize($main_width, $main_height)
									->write($mainPath)
									->destroy();
						@unlink($fileNew);
					}
					$watermarkImageNew = Engine_Api::_()->authorization()->getPermission($viewer->level_id,'album', 'watermarkthumb');
						if(!is_file($watermarkImageNew)){
							$fileNew = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'.DIRECTORY_SEPARATOR.time().'_'.$fileName.".jpg";
							$watemarkImageResult = $this->watermark_image($normalPath, $fileNew,$type,$watermarkImage);
							if($watemarkImageResult){
								@unlink($normalPath);
								$image->open($fileNew)
                      ->autoRotate()
											->resize($main_width, $main_height)
											->write($normalPath)
											->destroy();
								@unlink($fileNew);
							}
						}
					}
				}		
			}
			
			//thumb photo watermark
			if($enableWatermark == 1){
			$watermarkImage = Engine_Api::_()->authorization()->getPermission($viewer->level_id,'album', 'watermarkthumb');
				if(is_file($watermarkImage)){
					if(isset($extension))
						$type = $extension;
					else
						$type = $PhotoExtension;
					$fileNew = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'.DIRECTORY_SEPARATOR.time().'_'.$fileName.".jpg";
					$watemarkImageThumbResult = $this->watermark_image($normalPath, $fileNew,$type,$watermarkImage);
					if($watemarkImageThumbResult){
						@unlink($normalPath);
						$image->open($fileNew)
                  ->autoRotate()
									->resize($normal_width, $normal_height)
									->write($normalPath)
									->destroy();
						@unlink($fileNew);
				}		
			}			
		}
		// normal main  image resize
    $normalMainPath = $path . DIRECTORY_SEPARATOR . $base . '_nm.' . $extension;
    $image = Engine_Image::factory();
    $image->open($normalPath)
      ->autoRotate()
      ->resize($normal_width, $normal_height)
      ->write($normalMainPath)
      ->destroy();
		// Resize image (icon)
    $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file);
    $image->autoRotate();
    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;
    $image->resample($x, $y, $size, $size, 150, 150)
      ->write($squarePath)
      ->destroy();
    // Store
    try {
			$iSquare = $filesTable->createFile($squarePath, $params);
      $iMain = $filesTable->createFile($mainPath, $params);
      $iIconNormal = $filesTable->createFile($normalPath, $params);
			$iNormalMain = $filesTable->createFile($normalMainPath, $params);
			$iMain->bridge($iNormalMain, 'thumb.normalmain');
			$iMain->bridge($iIconNormal, 'thumb.normal');
			$iMain->bridge($iSquare, 'thumb.icon');
    } catch( Exception $e ) {
			@unlink($file);
      // Remove temp files
      @unlink($mainPath);
      @unlink($normalPath);
			@unlink($squarePath);
			@unlink($normalMainPath);
      // Throw
      if( $e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE ) {
        throw new Sesalbum_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }
			@unlink($file);
    // Remove temp files
      @unlink($mainPath);
      @unlink($normalPath);
			@unlink($squarePath);
			@unlink($normalMainPath);
    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->file_id = $iMain->file_id;
		$this->ip_address = $_SERVER['REMOTE_ADDR'];
    $this->save();
    // Delete the old file?
    if( !empty($tmpRow) ) {
      $tmpRow->delete();
    }
    return $this;
  }
	//get photo index
  public function getPhotoIndex()
  {
    return $this->getTable()
        ->select()
        ->from($this->getTable(), new Zend_Db_Expr('COUNT(photo_id)'))
        ->where('album_id = ?', $this->album_id)
        ->where('`order` < ?', $this->order)
        ->order('order ASC')
        ->limit(1)
        ->query()
        ->fetchColumn();
  }
  //get next photo
  public function getNextPhoto()
  {
    $table = $this->getTable();
    $select = $table->select()
        ->where('album_id = ?', $this->album_id)
        ->where('`order` > ?', $this->order)
        ->order('order ASC')
        ->limit(1);
    $photo = $table->fetchRow($select);
    
    if( !$photo ) {
      // Get first photo instead
      $select = $table->select()
          ->where('album_id = ?', $this->album_id)
          ->order('order ASC')
          ->limit(1);
      $photo = $table->fetchRow($select);
    }
    
    return $photo;
  }
  //get previous photo
  public function getPreviousPhoto()
  {
    $table = $this->getTable();
    $select = $table->select()
        ->where('album_id = ?', $this->album_id)
        ->where('`order` < ?', $this->order)
        ->order('order DESC')
        ->limit(1);
    $photo = $table->fetchRow($select);
    
    if( !$photo ) {
      // Get last photo instead
      $select = $table->select()
          ->where('album_id = ?', $this->album_id)
          ->order('order DESC')
          ->limit(1);
      $photo = $table->fetchRow($select);
    }
    
    return $photo;
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes()
  {
		
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   * */
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }
	public function getTitle(){
			return $this->title;
	}
  protected function _postDelete()
  {
		
    $mainPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id);
    $thumbPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.normal');
		$thumbNormailPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.normalmain');
     $thumbIcon = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id,'thumb.icon');
    // Delete thumb
    if( $thumbPhoto && $thumbPhoto->getIdentity() ) {
      try {
        $thumbPhoto->delete();
      } catch( Exception $e ) {}
    }
		// Delete thumbIcon
    if( $thumbIcon && $thumbIcon->getIdentity() ) {
      try {
        $thumbIcon->delete();
      } catch( Exception $e ) {}
    }
		// Delete thumbNormailPhoto
    if( $thumbNormailPhoto && $thumbNormailPhoto->getIdentity() ) {
      try {
        $thumbNormailPhoto->delete();
      } catch( Exception $e ) {}
    }
    // Delete main
    if( $mainPhoto && $mainPhoto->getIdentity() ) {
      try {
        $mainPhoto->delete();
      } catch( Exception $e ) {}
    }
    // Change album cover if applicable
    try {
      if( !empty($this->album_id) && !$this->skipAlbumDeleteHook ) {
        $album = $this->getAlbum();
        $nextPhoto = $this->getNextPhoto();
        if( ($album instanceof Sesalbum_Model_Album) &&
            ($nextPhoto instanceof Sesalbum_Model_Photo) &&
            (int) $album->photo_id == (int) $this->getIdentity() ) {
          $album->photo_id = $nextPhoto->getIdentity();
          $album->save();
        }
      }
    } catch( Exception $e ) {}
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
}
