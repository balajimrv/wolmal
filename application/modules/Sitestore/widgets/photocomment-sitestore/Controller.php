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
class Sitestore_Widget_PhotocommentSitestoreController extends Engine_Content_Widget_Abstract {

	//ACTION FOR SHOWING THE MOST COMMENTED PHOTOS ON STORE PROFILE STORE
  public function indexAction() {

  	//HERE WE CHECKING THE SITESTORE ALBUM IS ENABLED OR NOT
		$sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
		if (!$sitestorealbumEnabled) {
			return $this->setNoRender();
		}
    $is_mostcommentphoto = Zend_Registry::isRegistered('sitestorealbum_ismostCommentedPhoto') ? Zend_Registry::get('sitestorealbum_ismostCommentedPhoto') : null;

    //GET SITESTORE SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('sitestore_store');

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

    if (empty($is_mostcommentphoto)) {
      return $this->setNoRender();
    }
    
    //SEARCH PARAMETER
    $params = array();
		$params['store_id'] = $subject->store_id;
		$params['orderby'] = 'comment_count DESC';
		$params['zero_count'] = 'comment_count';
		$params['limit'] = $this->_getParam('itemCount', 4);

    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitestore');
 		//MAKE PAGINATOR
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