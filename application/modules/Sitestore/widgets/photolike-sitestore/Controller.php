<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_PhotolikeSitestoreController extends Engine_Content_Widget_Abstract {

	//ACTION FOR MOST LIKED PHOTOS ON STORE PROFILE STORE
  public function indexAction() {

  	//HERE WE CHECKING THE SITESTORE ALBUM IS ENABLED OR NOT
		$sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
		if (!$sitestorealbumEnabled) {
			return $this->setNoRender();
		}

    //GET SITESTORE SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
    
    //GET STORE ID
    $store_id = $subject->store_id;

    //PACKAGE WORK START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($subject->package_id, "modules", "sitestorealbum")) {
        return $this->setNoRender();
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($subject, 'spcreate');
      if (empty($isStoreOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //PACKAGE WORK END    
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    
    $this->view->is_ShowLightBox = $sitestorealbum_isShowLightBox = Zend_Registry::isRegistered('sitestorealbum_isShowLightBox') ? Zend_Registry::get('sitestorealbum_isShowLightBox') : null;

    if (empty($sitestorealbum_isShowLightBox)) {
      return $this->setNoRender();
    }   
    
    //SEARCH PARAMETER
    $params = array();
		$params['store_id'] = $store_id;
		$params['orderby'] = 'like_count DESC';
		$params['zero_count'] = 'like_count';
		$params['limit'] = $this->_getParam('itemCount', 4);
    
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitestore');
		$this->view->paginator = $paginator = $photoTable->widgetPhotos($params);   
    
    $this->view->count =  $photoTable->countTotalPhotos($params);
    
    if (Count($paginator) <= 0) {
      return $this->setNoRender();
    }
    
    //SHOWS PHOTOS IN THE LIGHTBOX
    //$this->view->showLightBox = Engine_Api::_()->sitestore()->canShowPhotoLightBox();
  }

}

?>