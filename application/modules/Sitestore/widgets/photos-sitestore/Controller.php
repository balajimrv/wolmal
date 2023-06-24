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
class Sitestore_Widget_PhotosSitestoreController extends Engine_Content_Widget_Abstract {

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

    $sitestorealbum_hasPackageEnable = Zend_Registry::isRegistered('sitestorealbum_hasPackageEnable') ? Zend_Registry::get('sitestorealbum_hasPackageEnable') : null;

    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitestore_store') {
    	$this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    }
    else {
      $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject()->getParent();
    }
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

    if (empty($sitestorealbum_hasPackageEnable)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK    
    //GET WHICH LAYOUT IS SET BY THE ADMIN
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);

    //GET THIRD TYPE LAYOUT IS SET OR NOT
    $this->view->widgets = $widgets = Engine_Api::_()->sitestore()->getwidget($layout, $store_id);

    //GET REQUEST
    $zendRequest = Zend_Controller_Front::getInstance()->getRequest();

    //GET TAB ID
    $this->view->content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $store_id, $layout);

    //GET CURRENT TAB ID
    $this->view->module_tabid = $currenttabid = $zendRequest->getParam('tab', null);

    //CHECK REQUEST IS ISAJAX OR NOT
    $this->view->isajax = $isajax = $this->_getParam('isajax', null);

    //SHOW TOP TITLE
    $this->view->showtoptitle = Engine_Api::_()->sitestore()->showtoptitle($layout, $store_id);

    //CHECK REQUEST IS AJAX OR NOT OR CURRENT TAB ID OR LAYOUT
    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {

      $this->view->identity_temp = $zendRequest->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;

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
      $this->view->albums_order = $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.albumsorder', 1);

      //GET CURRENT STORE NUMBER OF ALBUM
      $currentAlbumStoreNumbers = $this->_getParam('store', 1);

      //SEND CURRENT STORE NUMBER OF ALBUM TO THE TPL
      $this->view->currentAlbumStoreNumbers = $currentAlbumStoreNumbers;

      //SET ALBUMS PARAMS
      $paramsAlbum = array();
      $paramsAlbum['store_id'] = $store_id;

      //GET ALBUM COUNT
      $this->view->album_count = $album_count = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbumsCount($paramsAlbum);
      
      //START ALBUMS PAGINATION
      $stores_vars = Engine_Api::_()->sitestore()->makeStore($album_count, $albums_per_store, $currentAlbumStoreNumbers);
      $stores_array = Array();
      for ($y = 0; $y <= $stores_vars[2] - 1; $y++) {
        if ($y + 1 == $stores_vars[1]) {
          $links = "1";
        } else {
          $links = "0";
        }
        $stores_array[$y] = Array('stores' => $y + 1,
            'links' => $links);
      }

      $this->view->storesarray = $stores_array;
      $this->view->maxstores = $stores_vars[2];
      $this->view->pstarts = 1;
      //END ALBUMS PAGINATION
      
      //SET ALBUMS PARAMS
      $paramsAlbum['start'] = $albums_per_store;
      $paramsAlbum['end'] = $stores_vars[0];
      if(empty($albums_order)) {
        $paramsAlbum['orderby'] = 'album_id ASC';
      } else {
        $paramsAlbum['orderby'] = 'album_id DESC';
      }
      $paramsAlbum['getSpecialField'] = 0;

      $fecthAlbums = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbums($paramsAlbum);
      if (!empty($fecthAlbums)) {
        $album = $this->view->album = $this->view->paginator = $fecthAlbums;
      }

      //SET PHOTOS PARAMS
      $paramsPhoto = array();
      $paramsPhoto['store_id'] = $store_id;
      $paramsPhoto['user_id'] = $sitestore->owner_id;
      $paramsPhoto['album_id'] = $this->view->default_album_id = Engine_Api::_()->getItemTable('sitestore_album')->getDefaultAlbum($store_id)->album_id;
      
      //FETCHING ALL PHOTOS
      $this->view->total_images = $total_photo = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotosCount($paramsPhoto);

      //SEND CURRENT STORE NUMBER TO THE TPL
      $this->view->currentStoreNumbers = $currentStoreNumbers = $this->_getParam('stores', 1);

      //SEND PHOTOS PER STORE TO THE TPL
      $this->view->photos_per_store = $photos_per_store = $zendRequest->getParam('itemCount_photo', 100);

      if(empty($photos_per_store)) {
        $this->view->photos_per_store = $photos_per_store = 100;
      }

      //START PHOTOS PAGINATION
      $store_vars = Engine_Api::_()->sitestore()->makeStore($total_photo, $photos_per_store, $currentStoreNumbers);
      $paramsPhoto['start'] = $photos_per_store;
      $paramsPhoto['end'] = $store_vars[0];
     
      $this->view->paginators = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotos($paramsPhoto);
      $store_array = Array();
      for ($x = 0; $x <= $store_vars[2] - 1; $x++) {
        if ($x + 1 == $store_vars[1]) {
          $link = "1";
        } else {
          $link = "0";
        }
        $store_array[$x] = Array('store' => $x + 1,
            'link' => $link);
      }
      $this->view->storearray = $store_array;
      $this->view->maxstore = $store_vars[2];
      $this->view->pstart = 1;
      //END PHOTOS PAGINATION      
    } else {
      $this->view->show_content = false;
      $title_count = $this->_getParam('titleCount', false);
      $this->view->identity_temp = $this->view->identity;
    }

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

?>