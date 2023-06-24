<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Plugin_Menus {

  public function canViewVideos() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.video.show.menu', 1)) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('videos', 'sitestorevideo');
    $rName = $table->info('name');
    $table_stores = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $rName_stores = $table_stores->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName_stores, array('photo_id', 'title as sitestore_title'))
                    ->join($rName, $rName . '.store_id = ' . $rName_stores . '.store_id')
                    ->where($rName . '.status = ?', '1')
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

  //SITEMOBILE STORE VIDEO MENUS
  public function onMenuInitialize_SitestorevideoAdd($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    $video_id = $subject->getIdentity();

    $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);
    $store_id = $sitestorevideo->store_id;
    if (empty($sitestorevideo)) {
      return false;
    }
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
    if (empty($isManageAdmin)) {
      $can_create = 0;
    } else {
      $can_create = 1;
    }

    if (empty($can_create)) {
      return false;
    }
    return array(
        'label' => 'Add Video',
        'route' => 'sitestorevideo_create',
        'class' => 'ui-btn-action',
        'params' => array(
            'store_id' => $store_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitestorevideoEdit($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    $video_id = $subject->getIdentity();

    $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);
    $store_id = $sitestorevideo->store_id;
    if (empty($sitestorevideo)) {
     return false;
    }

    $check = $this->commonChecks();
    if (empty($check)) {
      return false;
    }
    return array(
        'label' => 'Edit Video',
        'route' => 'sitestorevideo_edit',
        'class' => 'ui-btn-action',
        'params' => array(
            'video_id' => $video_id,
            'store_id' => $store_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitestorevideoDelete($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    $video_id = $subject->getIdentity();

    $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);
    $store_id = $sitestorevideo->store_id;
    if (empty($sitestorevideo)) {
      return false;
    }

    $check = $this->commonChecks();
    if (empty($check)) {
      return false;
    }
    return array(
        'label' => 'Delete Video',
        'route' => 'sitestorevideo_delete',
        'class' => 'ui-btn-danger',
        'params' => array(
            'video_id' => $video_id,
            'store_id' => $store_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function commonChecks() {
    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();

    $video_id = $subject->getIdentity();

    $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);
    $store_id = $sitestorevideo->store_id;
    if (empty($sitestorevideo)) {
      return false;
    }
    $getPackagevideoView = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestorevideo');
    $video = empty($getPackagevideoView) ? null : $sitestorevideo;

    $owner_id = $video->owner_id;
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if ($owner_id != $viewer_id && empty($can_edit)) {
      return false;
    } else {
      return true;
    }
  }
}
?>