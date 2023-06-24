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
class Sitestore_Widget_SitemobilePhotosSitestoreController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR GETTING THE ALBUMS AND PHOTOS
  public function indexAction() {

    //HERE WE CHECKING THE SITESTORE ALBUM IS ENABLED OR NOT
    $sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
    if (!$sitestorealbumEnabled) {
      return $this->setNoRender();
    }

    //GET HOW MANY ALBUMS DO YOU WANT SHOW ON PER STORE
    $this->view->itemCount = $albums_per_store = $this->_getParam('itemCount', 10);

    //GET HOW MANY PHOTOS DO YOU WANT SHOW ON PER STORE
    $this->view->itemCount_photo = $this->_getParam('itemCount_photo', 100);

    //ALBUMS ORDER
    $this->view->albums_order = $this->_getParam('albumsorder', 1);

    //GET VIEWER ID
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //DON'T RENDER IF SUNJECT IS NOT THERE
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SITESTORE SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

    //GET STORE ID
    $store_id = $sitestore->store_id;

    //START PACKAGE WORK
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorealbum")) {
        return $this->setNoRender();
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'spcreate');
      if (empty($isStoreOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //END PACKAGE WORK

    //TOTAL ALBUMS
    $albumCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestore', 'albums');     
    $photoCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'spcreate');
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $canEdit = 0;
    } else {
      $this->view->can_edit = $canEdit = 1;
    }
    
    if (empty($photoCreate) && empty($albumCount) && empty($canEdit) && !(Engine_Api::_()->sitestore()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }

    //GET REQUEST
    $zendRequest = Zend_Controller_Front::getInstance()->getRequest();

		$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'spcreate');
		if ($isManageAdmin || $canEdit) {
			$this->view->allowed_upload_photo = 1;
		} else {
			$this->view->allowed_upload_photo = 0;
		}

    //ALBUMS PER STORE
		$this->view->albums_per_store = $albums_per_store = $zendRequest->getParam('itemCount', 10);

		if(empty($albums_per_store)) {
			$this->view->albums_per_store = $albums_per_store = 10;
		}

		//ALBUMS ORDER
		$this->view->albums_order = $albums_order = $zendRequest->getParam('albumsorder', 1);

		//GET CURRENT STORE NUMBER OF ALBUM
		$currentAlbumStoreNumbers = $store = $this->_getParam('store', 1);

		//SEND CURRENT STORE NUMBER OF ALBUM TO THE TPL
		$this->view->currentAlbumStoreNumbers = $currentAlbumStoreNumbers;

		//SET ALBUMS PARAMS
		$paramsAlbum = array();
		$paramsAlbum['store_id'] = $store_id;

		//GET ALBUM COUNT
		$this->view->album_count = $album_count = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbumsCount($paramsAlbum);
		
		//SET ALBUMS PARAMS
		if(empty($albums_order)) {
			$paramsAlbum['orderby'] = 'album_id ASC';
		} else {
			$paramsAlbum['orderby'] = 'album_id DESC';
		}
		$paramsAlbum['getSpecialField'] = 0;

		$fecthAlbums = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbums($paramsAlbum);

		$album = $this->view->album = $paginator = $this->view->paginator = $fecthAlbums;
		$this->view->paginator = $paginator->setItemCountPerPage($albums_per_store);
		$this->view->paginator->setCurrentPageNumber($this->_getParam('store', 1));

		//SET PHOTOS PARAMS
		$paramsPhoto = array();
		$paramsPhoto['store_id'] = $store_id;
		$paramsPhoto['user_id'] = $sitestore->owner_id;
		$paramsPhoto['album_id'] = $this->view->default_album_id = Engine_Api::_()->getItemTable('sitestore_album')->getDefaultAlbum($store_id)->album_id;
		
		//FETCHING ALL PHOTOS
		$this->view->total_images = $total_photo = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotosCount($paramsPhoto);

		//SEND PHOTOS PER STORE TO THE TPL
		$this->view->photos_per_store = $photos_per_store = $zendRequest->getParam('itemCount_photo', 100);

		if(empty($photos_per_store)) {
			$this->view->photos_per_store = $photos_per_store = 100;
		}

		$this->view->paginators = $paginators = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotos($paramsPhoto);
		$this->view->paginators = $paginators->setItemCountPerPage($photos_per_store);
		$this->view->paginators->setCurrentPageNumber($this->_getParam('stores', 1));
		//SET PHOTOS PARAMS
		$paramsPhoto = array();
		$paramsPhoto['store_id'] = $sitestore->store_id;

    //SET COUNT TO THE TITLE
    $this->_childCount = $this->view->locale()->toNumber(Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotosCount($paramsPhoto));

  }

  public function getChildCount() {
    return $this->_childCount;
  }

}