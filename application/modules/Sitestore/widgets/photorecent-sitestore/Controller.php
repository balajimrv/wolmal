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
class Sitestore_Widget_PhotorecentSitestoreController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR GETTING THE PHOTOS IN THE STRIP
  public function indexAction() {  	

  	//HERE WE CHECKING THE SITESTORE ALBUM IS ENABLED OR NOT
		$sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
		if (!$sitestorealbumEnabled) {
			return $this->setNoRender();
		}

  	//DON'T RENDER IF SUBJECT IS NOT THERE
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    //GET SITESTORE SUBJECT
    $this->view->sitestore_subject = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
    
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

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = 0;
    } else {
      $this->view->can_edit = 1;
    }

    $this->view->sitestorealbum_isManageAdmin = $sitestorealbum_isManageAdmin = Zend_Registry::isRegistered('sitestorealbum_isManageAdmin') ? Zend_Registry::get('sitestorealbum_isManageAdmin') : null;
    if (empty($sitestorealbum_isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK    
    
    //GET LIMIT
    $this->view->limit = $limit = $this->_getParam('itemCount', 7);
    
    //CHECK REQUEST IS AJAX OR NOT
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    
    //IF REQUEST IS AJAX THEN UPDATE PHOTO TABLE
    $phototable = Engine_Api::_()->getDbtable('photos', 'sitestore');
    if (!empty($is_ajax)) {
      $phototable->update(array('photo_hide' => 1), array('photo_id = ?' => $this->_getParam('hide_photo_id', null)));
    }
    
  	//SET STORE PHOTO PARAMS
    $paramsPhoto = array();	    
    $paramsPhoto['store_id'] = $subject->store_id;
    $paramsPhoto['photo_hide'] = 0;
    $paramsPhoto['file_id'] = $subject->photo_id;
    $paramsPhoto['orderby'] = 'creation_date DESC';
    $paramsPhoto['start'] = $limit;

    //MAKE PAGINATOR
    $this->view->paginator = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotos($paramsPhoto);
 
    //SET STORE PHOTO PARAMS
    $paramsPhoto['photo_hide'] = 1;
    
    //FETCHING PHOTOS
		$this->view->count = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotosCount($paramsPhoto);

    //IF COUNT IS ZERO THEN NO RENDER
    if (!(count($this->view->paginator) > 0) && !($this->view->count) > 0) {
      return $this->setNoRender();
    }

    //SET STORE PHOTO PARAMS
    $paramsPhoto['photo_hide'] = 0;
    
    //FETCHING PHOTOS
    $this->view->row_count = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotosCount($paramsPhoto);

    //IF COUNT IS ZERO THEN NO RENDER
    if ($this->view->row_count == 0 && $this->view->can_edit == 0) {
      return $this->setNoRender();
    }
    
    //GETTING THE CURRENT TAB ID
    $this->view->currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
  }

  public function getChildCount() {
    return $this->_childCount;
  }
}

?>