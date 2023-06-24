<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Api_Core extends Core_Api_Abstract {

  /**
   * Return a truncate text
   *
   * @param text text 
   * @return truncate text
   * */
  public function truncation($string) {
    $length = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.truncation.limit', 13);
    $string = strip_tags($string);
    return Engine_String::strlen($string) > $length ? Engine_String::substr($string, 0, ($length - 3)) . '...' : $string;
  }

  public function isUpload() {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $imageUpload = Zend_Registry::isRegistered('sitestorevideo_imageUpload') ? Zend_Registry::get('sitestorevideo_imageUpload') : null;
    $isReturn = empty($imageUpload) ? "<a href='javascript:void(0);' onclick='javascript:void(0);'>" . $view->translate("here") . '</a>' : "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>" . $view->translate("here") . '</a>';
    return $isReturn;
  }

  /**
   * Return a video
   *
   * @param array $params
   * @param array $file
   * @param array $values
   * @return video object
   * */
  public function createSitestorevideo($params, $file, $values) {

    if ($file instanceof Storage_Model_File) {
      $params['file_id'] = $file->getIdentity();
    } else {
      //CREATE VIDEO ITEM
      $video = Engine_Api::_()->getDbtable('videos', 'sitestorevideo')->createRow();
      $file_ext = pathinfo($file['name']);
      $file_ext = $file_ext['extension'];
      $video->code = $file_ext;
      $video->save();

      //STORE VIDEO IN TEMPORARY STORAGE OBJECT FOR FFMPEG TO HANDLE
      $storage = Engine_Api::_()->getItemTable('storage_file');
      $storageObject = $storage->createFile($file, array(
                  'parent_id' => $video->getIdentity(),
                  'parent_type' => $video->getType(),
                  'user_id' => $video->owner_id,
              ));

      //REMOVE TEMPORARY FILE
      @unlink($file['tmp_name']);

      $video->file_id = $storageObject->file_id;
      $video->save();

      //ADD TO JOBS
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.html5', false)) {
        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitestorevideo_encode', array(
          'video_id' => $video->getIdentity(),
          'type' => 'mp4',
        ));
      } else {
        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitestorevideo_encode', array(
          'video_id' => $video->getIdentity(),
          'type' => 'flv',
        ));
      }
    }
    return $video;
  }
  
  public function enableComposer() {
    $subject = '';
    if (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
    }
    if ($subject && in_array($subject->getType(), array('sitestore_store', 'sitestoreevent_event'))):
 
      if (in_array($subject->getType(), array('sitestoreevent_event'))):
        $subject = Engine_Api::_()->getItem('sitestore_store', $subject->store_id);
      endif;
      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorevideo")) {
          return false;
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'svcreate');
        if (empty($isStoreOwnerAllow)) {
          return false;
        }
      }
      if (!Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit') && !Engine_Api::_()->sitestore()->isManageAdmin($subject, 'svcreate')):
        return false;
      endif;
      return true;
    endif;
    return false;
  }

}
?>