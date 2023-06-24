<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Api_Album extends Core_Api_Abstract {

  
public function isLessThan417AlbumModule() {
    $albumModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitealbum');
    if($albumModule) {
      $albumModuleVersion = $albumModule->version;
      if ($albumModuleVersion < '4.1.7') {
        return true;
      } else {
        return false;
      }
    }
    return false;
  }  
Public function photoBySettings($params = array(), $widgetName = null) {

    $parentTable = Engine_Api::_()->getItemTable('album');
    $parentTableName = $parentTable->info('name');

    $table = Engine_Api::_()->getItemTable('album_photo');
    $tableName = $table->info('name');
    $sitealbumModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitealbum')->enabled;
    //IF SITEALBUM PLUGIN IS THERE OR NOT.
    if(!$sitealbumModule) {
      
      $fields = array('album_id', 'photo_id', 'title', 'file_id', 'owner_id', 'view_count', 'comment_count', 'description', 'creation_date');
    }
    else {
       $fields = array('album_id', 'photo_id', 'title', 'file_id', 'owner_id', 'view_count', 'comment_count', 'rating', 'seao_locationid', 'location', 'like_count', 'description', 'creation_date');
     }
    $select = $table->select()
            ->from($tableName, $fields);

    if (!Engine_Api::_()->getApi('album', 'sitemobile')->isLessThan417AlbumModule()) {
      $select->joinLeft($parentTableName, $parentTableName . '.album_id=' . $tableName . '.album_id', null);
    } else {
      $select->joinLeft($parentTableName, $parentTableName . '.album_id=' . $tableName . '.collection_id', null);
    }

    $select->where($parentTableName . '.search = ?', true);

    if (isset($params['orderby']) && !empty($params['orderby'])) {
      switch ($params['orderby']) {
        case 'modified_date':
          $select->order($tableName . '.modified_date DESC');
          break;
        case 'creation_date':
          $select->order($tableName . '.creation_date DESC');
          break;
        case 'like_count':
          $select->order($tableName . '.like_count DESC');
          break;
        case 'view_count':
          $select->order($tableName . '.view_count DESC');
          break;
        case 'comment_count':
          $select->order($tableName . '.comment_count DESC');
          break;
        case 'rating':
          $select->order($tableName . '.rating DESC');
          break;
        case 'featured':
          $select->where($tableName . '.featured = ?', 1);
          $select->order('Rand()');
          break;
        case 'random':
          $select->order('Rand()');
          break;
      }
    }

    if (isset($params['featured']) && !empty($params['featured'])) {
      $select->where($tableName . '.featured = ?', 1);
    }

    if (!$this->canShowSpecialAlbum())
      $select->where('type IS NULL');

    if ($widgetName == 'Featured Photos') {
      $select = $this->addPrivacyAlbumsSQl($select, $tableName);
    } else {
      $select = $this->addPrivacyAlbumsSQl($select, $tableName);
    }

//    if ($activTab->name != 'featured_albums' && $activTab->name != 'random_albums') {
//      $select->order('creation_date DESC');
//    }
    
    return Zend_Paginator::factory($select);
  }
  
   public function addPrivacyAlbumsSQl($select, $tableName = null) {

    $privacybase = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.nonprivacybase', 0);
    if (empty($privacybase))
      return $select;

    $column = $tableName ? "$tableName.album_id" : "album_id";

    return $select->where("$column IN(?)", $this->getOnlyViewableAlbumsId());
  }
  
/**
   * Check download photo is enable or not
   * @return bool
   */
  public function canShowSpecialAlbum() {
    return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.photo.specialalbum', 1);
  }

  public function getOnlyViewableAlbumsId() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $albums_ids = array();
    $cache = Zend_Registry::get('Zend_Cache');
    $cacheName = 'album_ids_user_id_' . $viewer->getIdentity();

    $data = APPLICATION_ENV == 'development' ? ( Zend_Registry::isRegistered($cacheName) ? Zend_Registry::get($cacheName) : null ) : $cache->load($cacheName);
    if ($data && is_array($data)) {
      $albums_ids = $data;
    } else {
      set_time_limit(0);
      $table = Engine_Api::_()->getItemTable('album');
      $album_select = $table->select()
              ->where('search = ?', true)
              ->order('album_id DESC');

      if (!Engine_Api::_()->getApi('album', 'sitemobile')->canShowSpecialAlbum())
        $album_select->where('type IS NULL');
      // Create new array filtering out private albums
      $i = 0;
      foreach ($album_select->getTable()->fetchAll($album_select) as $album) {
        if ($album->isOwner($viewer) || Engine_Api::_()->authorization()->isAllowed($album, $viewer, 'view')) {
          $albums_ids[$i++] = $album->album_id;
        }
      }

      // Try to save to cache
      if (empty($albums_ids))
        $albums_ids = array(0);

      if (APPLICATION_ENV == 'development') {
        Zend_Registry::set($cacheName, $albums_ids);
      } else {
        $cache->save($albums_ids, $cacheName);
      }
    }

    return $albums_ids;
  }
  
  /**
   * Check lightbox is enable or not for photos show
   * @return bool
   */
  public function showLightBoxPhoto() {

    $session = new Zend_Session_Namespace('mobile');
    if (isset($session->mobile) && $session->mobile)
      return false;

    return SEA_SITEALBUM_LIGHTBOX;
  }

}
