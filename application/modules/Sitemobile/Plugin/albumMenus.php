<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: albumMenus.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Plugin_albumMenus {

  
  public function canViewPhotos()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    //IF SITEALBUM PLUGIN IS NOT INSTALLED THEN WE WILL NOT SHOW THE BROWSE PHOTOS TAB.
    $sitealbum = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitealbum' );
    // Must be able to view albums
    if( !Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'view') || !$sitealbum) {
      return false;
    } 

    return true;
  }
  
  
  public function onMenuInitialize_AlbumProfileAdd() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if ($subject->getType() !== 'album') {
      throw new Album_Model_Exception('Whoops, not a album!');
    }

    if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'create')) {
      return false;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'create')) {
      return false;
    }

    return array(
        'label' => 'Add More Photos',
        'icon' => 'application/modules/Album/externals/images/upload.png',
        'route' => 'album_general',
        'params' => array(
            'action' => 'upload',
            'album_id' => $subject->getIdentity()
        )
    );
  }

  public function onMenuInitialize_AlbumProfileEdit() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'album') {
      throw new Album_Model_Exception('Whoops, not a album!');
    }

    if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    return array(
        'label' => 'Edit Settings',
        'icon' => 'application/modules/Album/externals/images/edit.png',
        'route' => 'album_specific',
        'params' => array(
            'action' => 'edit',
            'album_id' => $subject->getIdentity()
        )
    );
  }

  public function onMenuInitialize_AlbumProfileManage() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'album') {
      throw new Album_Model_Exception('Whoops, not a album!');
    }

    if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'edit')) {
      return false;
    }

    return array(
        'label' => 'Manage Photos',
        'icon' => 'application/modules/Album/externals/images/edit.png',
        'route' => 'album_specific',
        'params' => array(
            'action' => 'editphotos',
            'album_id' => $subject->getIdentity()
        )
    );
  }

  public function onMenuInitialize_AlbumProfileDelete() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() !== 'album') {
      throw new Album_Model_Exception('Whoops, not a album!');
    }

    if (!$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'delete')) {
      return false;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'delete')) {
      return false;
    }

    return array(
        'label' => 'Delete Album',
        'icon' => 'application/modules/Album/externals/images/delete.png',
        'route' => 'album_specific',
        'class' => 'smoothbox',
        'params' => array(
            'action' => 'delete',
            'album_id' => $subject->getIdentity()
        )
    );
  }


  //PHOTO VIEW PAGE OPTIONS
  public function onMenuInitialize_AlbumPhotoEdit($row) {

     //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();

    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if (!$subject->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'edit')) {
      return false;
    }

    return array(
        'label' => 'Edit',
        'route' => 'album_extended',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
           'controller' => 'photo',
           'action' => 'edit',
           'photo_id' => $subject->photo_id
        )
    );
  }

 //PHOTO VIEW PAGE OPTIONS
  public function onMenuInitialize_AlbumPhotoDelete($row) {

     //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();

    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if (!$subject->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'edit')) {
      return false;
    }

    return array(
        'label' => 'Delete',
        'route' => 'album_extended',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
           'controller' => 'photo',
           'action' => 'delete',
           'photo_id' => $subject->photo_id
        )
    );
  }

  public function onMenuInitialize_AlbumPhotoShare($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
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

  public function onMenuInitialize_AlbumPhotoReport($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
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

  public function onMenuInitialize_AlbumPhotoMakeProfilePhoto($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
      return false;
    }

    return array(
        'label' => 'Make Profile Photo',
        'class' => 'smoothbox ui-btn-default ui-btn-action',
        'route' => 'user_extended',
        'params' => array(
            'module' => 'user',
            'controller' => 'edit',
            'action' => 'external-photo',
            'photo' => $subject->getGuid(),
        )
    );
  }
}