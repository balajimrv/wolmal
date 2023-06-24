<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobiController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorealbum_MobiController extends Core_Controller_Action_Standard {

  public function init() {

    //HERE WE CHECKING THE SITESTORE ALBUM IS ENABLED OR NOT
    $sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
    if (!$sitestorealbumEnabled) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();
    $store_id = $this->_getParam('store_id', $this->_getParam('id', null));

    //PACKAGE BASE PRIYACY START
    if (!empty($store_id)) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      if ($sitestore) {
        Engine_Api::_()->core()->setSubject($sitestore);      
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorealbum")) {
            return $this->_forward('requireauth', 'error', 'core');
          }
        } else {
          $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'spcreate');
          if (empty($isStoreOwnerAllow)) {
            return $this->_forward('requireauth', 'error', 'core');
          }
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if (Engine_Api::_()->core()->hasSubject() != null) {
        $photo = Engine_Api::_()->core()->getSubject();
        $album = $photo->getCollection();
        $store_id = $album->store_id;
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