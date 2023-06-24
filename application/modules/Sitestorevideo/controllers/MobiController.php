<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobiController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_MobiController extends Core_Controller_Action_Standard {

  public function init() {
  	
    //GET STORE ID
    $store_id = $this->_getParam('store_id');

    //PACKAGE BASE PRIYACY START
    if (!empty($store_id)) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo")) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      } else {
        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
        if (empty($isStoreOwnerAllow)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if ($this->_getParam('video_id') != null) {
        $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $this->_getParam('video_id'));
        $store_id = $sitestorevideo->store_id;
      }
    }
    
    //GET VIDEO ID
    $video_id = $this->_getParam('video_id');
    if ($video_id) {
      $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);
      if ($sitestorevideo) {
        Engine_Api::_()->core()->setSubject($sitestorevideo);
      }
    }
  }

  //ACTION FOR VIEW THE VIDEO
  public function viewAction() {
    
    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }
  
}

?>