<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_Widget_UserPhotoTitleController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    // Don't render this if not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return $this->setNoRender();
    }


    $this->view->addCoverPhoto = Engine_Api::_()->hasModuleBootstrap('siteusercoverphoto');
    if ($this->view->addCoverPhoto) {
      $user_level_id = $viewer->level_id;
      if (isset($viewer->user_cover)) {
        $this->view->photo = $photo = Engine_Api::_()->getItem(Engine_Api::_()->hasModuleBootstrap('advalbum') ? 'advalbum_photo' : 'album_photo', $viewer->user_cover);
      } elseif (Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user_level_id.id")) {
        
      }
    }
  }

//  public function getCacheKey() {
//    $viewer = Engine_Api::_()->user()->getViewer();
//    $translate = Zend_Registry::get('Zend_Translate');
//    return $viewer->getIdentity() . $translate->getLocale() . sprintf('%d', $viewer->photo_id);
//  }

}