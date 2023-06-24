<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: musicMenus.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Plugin_musicMenus {

   public function onMenuInitialize_MusicEdit($row) { 
    $playlist = Engine_Api::_()->core()->getSubject('music_playlist');
    if (!$playlist->isEditable()) {
      return false;
    }
    
    return array(
        'label' => 'Edit Playlist',
        'route' => 'music_playlist_specific',
        'class' => 'ui-btn-action',
        'params' => array(
            'action' => 'edit',
            'playlist_id' => $playlist->playlist_id,
            'slug' => $playlist->getTitle(),
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_MusicDelete($row) {
    $playlist = Engine_Api::_()->core()->getSubject('music_playlist');
    if (!$playlist->isDeletable()) {
      return false;
    }

    return array(
        'label' => 'Delete Playlist',
        'route' => 'music_playlist_specific',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
            'action' => 'delete',
            'playlist_id' => $playlist->playlist_id,
            'slug' => $playlist->getTitle(),
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_MusicShare($row) {
    $playlist = Engine_Api::_()->core()->getSubject('music_playlist');
    if (empty($playlist)) {
      return false;
    }
    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    //CHECKS FOR SHARE PLAYLIST
    if (empty($viewer_id)) {
      return false;
    }

    return array(
        'label' => 'Share',
        'route'=>'default',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module'=>'activity',
            'controller'=>'index',
	          'action'=>'share',
            'type'=>'music_playlist',
            'id' => $playlist->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_MusicReport($row) {
    $playlist = Engine_Api::_()->core()->getSubject('music_playlist');
    if (empty($playlist)) {
      return false;
    }
    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    //CHECKS FOR REPORT PLAYLIST
    if (empty($viewer_id)) {
      return false;
    }

    return array(
        'label' => 'Report',
        'route' => 'default',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $playlist->getGuid(),
        )
    );
  }
}