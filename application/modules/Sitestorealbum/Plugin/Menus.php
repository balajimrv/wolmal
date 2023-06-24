<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorealbum_Plugin_Menus {

  public function canViewAlbums() {

    $isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.isActivate', 0);
    if(empty($isActive)) {
      return false;
    }
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.album.show.menu', 1)) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('albums', 'sitestore');
    $rName = $table->info('name');
    $table_stores = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $rName_stores = $table_stores->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName_stores, array('photo_id', 'title as sitestore_title'))
                    ->join($rName, $rName . '.store_id = ' . $rName_stores . '.store_id')
                    ->where($rName .'.search = ?', 1);

    $select = $select
                    ->where($rName_stores . '.closed = ?', '0')
                    ->where($rName_stores . '.approved = ?', '1')
                    ->where($rName_stores . '.search = ?', '1')
                    ->where($rName_stores . '.declined = ?', '0')
                    ->where($rName_stores . '.draft = ?', '1');
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $select->where($rName_stores . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $row = $table->fetchAll($select);
    $count = count($row);
    if (empty($count)) {
      return false;
    }
    return true;
  }

   //SITEMOBILE STORE ALBUM MENUS
  public function onMenuInitialize_SitestorealbumViewAlbums($row) {

    // $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    //GET ALBUM ID
    $album_id = $subject->getIdentity();

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitestore_album', $album_id);

    if (empty($album))
      return false;

    $store_id = $album->store_id;
    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['store_id'] = $store_id;
    //GET ALBUM COUNT
    $album_count = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbumsCount($paramsAlbum);

    //CHECKS
    if ($album_count <= 1) {
      return false;
    }

    return array(
        'label' => 'View Albums',
        'class' => 'ui-btn-action',
        'route' => 'sitestore_albumphoto_general',
        'params' => array(
            'action' => 'view-album',
            'album_id' => $subject->getIdentity(),
            'store_id' => $store_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitestorealbumAdd($row) {

    //$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $upload_photo = 0;
    $engineApiSitestore = Engine_Api::_()->sitestore();
    //GET ALBUM ID
    $album_id = $subject->getIdentity();

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitestore_album', $album_id);

    if (empty($album))
      return false;

    $store_id = $album->store_id;
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    $isManageAdmin = $engineApiSitestore->isManageAdmin($sitestore, 'spcreate');
    if (empty($isManageAdmin)) {
      $canCreatePhoto = 0;
    } else {
      $canCreatePhoto = 1;
    }

    if ($canCreatePhoto == 1 && ($engineApiSitestore->isStoreOwner($sitestore) || $album->default_value == 1)) {
      $upload_photo = 1;
    }

    if (empty($upload_photo)) {
      return false;
    }

    return array(
        'label' => 'Add More Photos',
        'class' => 'ui-btn-action',
        'route' => 'sitestore_photoalbumupload',
        'params' => array(
            'action' => 'upload',
            'album_id' => $subject->getIdentity(),
            'store_id' => $store_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitestorealbumEdit() {

    //$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    //GET ALBUM ID
    $album_id = $subject->getIdentity();

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitestore_album', $album_id);
    if (empty($album))
      return false;
    $store_id = $album->store_id;

    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    $engineApiSitestore = Engine_Api::_()->sitestore();

    $isManageAdmin = $engineApiSitestore->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (empty($can_edit)) {
      return false;
    }
    return array(
        'label' => 'Edit Album',        
        'route' => 'sitestore_albumphoto_general',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'action' => 'edit',
            'album_id' => $subject->getIdentity(),
            'store_id' => $store_id
        )
    );
  }

  public function onMenuInitialize_SitestorealbumDelete() {

    // $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $album_id = $subject->getIdentity();

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitestore_album', $album_id);
    if (empty($album))
      return false;
    $store_id = $album->store_id;
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    $engineApiSitestore = Engine_Api::_()->sitestore();

    $isManageAdmin = $engineApiSitestore->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (empty($can_edit)) {
      return false;
    }

    //SET DEFAULT ALBUM VALUE
    $default_value = $album->default_value;
    if ($default_value == 1) {
      return false;
    }

    return array(
        'label' => 'Delete Album',
        'route' => 'sitestore_albumphoto_general',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
            'action' => 'delete',
            'album_id' => $subject->getIdentity(),
            'store_id' => $store_id
        )
    );
  }

  //PHOTO VIEW STORE OPTIONS

  public function onMenuInitialize_SitestorealbumPhotoEdit($row) {

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $album_id = $subject->album_id;

    //GET STORE ID
    $store_id = $subject->store_id;

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //PHOTO OWNER, STORE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id == $subject->user_id || $can_edit == 1) {
      $canEdit = 1;
    } else {
      $canEdit = 0;
    }
    //CHECK FOR EDIT
    if (empty($canEdit)) {
      return false;
    }

    return array(
        'label' => 'Edit',
        'route' => 'sitestore_imagephoto_specific',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'action' => 'photo-edit',
            'photo_id' => $subject->getIdentity(),
            'album_id' => $album_id,
            'store_id' => $store_id,
            //'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitestorealbumPhotoDelete($row) {

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $album_id = $subject->album_id;

    //GET STORE ID
    $store_id = $subject->store_id;

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //PHOTO OWNER, STORE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id == $subject->user_id || $can_edit == 1) {
      $canDelete = 1;
    } else {
      $canDelete = 0;
    }
    //CHECK FOR EDIT
    if (empty($canDelete)) {
      return false;
    }
    return array(
        'label' => 'Delete',
        'route' => 'sitestore_imagephoto_specific',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
            'action' => 'remove',
            'photo_id' => $subject->getIdentity(),
            'album_id' => $album_id,
            'store_id' => $store_id,
        )
    );
  }

  public function onMenuInitialize_SitestorealbumPhotoShare($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    if (!SEA_PHOTOLIGHTBOX_SHARE) {
      return false;
    }
    return array(
        'label' => 'Share',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_SitestorealbumPhotoReport($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    if (!SEA_PHOTOLIGHTBOX_REPORT) {
      return false;
    }
    return array(
        'label' => 'Report',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
        )
    );
  }

// $this->canEdit && SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO
  public function onMenuInitialize_SitestorealbumPhotoProfile($row) {
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $album_id = $subject->album_id;

    //GET STORE ID
    $store_id = $subject->store_id;

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //PHOTO OWNER, STORE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id == $subject->user_id || $can_edit == 1) {
      $canEdit = 1;
    } else {
      $canEdit = 0;
    }
    //CHECK FOR EDIT
    if (empty($canEdit) || !SEA_PHOTOLIGHTBOX_MAKEPROFILEPHOTO) {
      return false;
    }
    return array(
        'label' => 'Make Store Profile Photo',
        'route' => 'sitestore_imagephoto_specific',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'sitestore',
            'controller' => 'photo',
            'action' => 'make-store-profile-photo',
            'photo' => $subject->getGuid(),
            'store_id' => $store_id,
        )
    );
  }
}
?>