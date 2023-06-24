<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorealbum_Widget_AlbumContentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
  
    //GET VIEWER
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //GET VIEWER ID
    $viewer_id = $viewer->getIdentity();

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $engineApiSitestore = Engine_Api::_()->sitestore();
    //GET ALBUM ID
    $this->view->album_id = $album_id = $request->getParam('album_id');

    //GET ALBUM ITEM
    $this->view->album = $album = Engine_Api::_()->getItem('sitestore_album', $album_id);

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $request->getParam('tab');


    //GET SITESTORE ID
    $store_id = $request->getParam('store_id');

    //GET SITESITESTORE ITEM
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = $engineApiSitestore->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = $engineApiSitestore->isManageAdmin($sitestore, 'comment');
    if (empty($isManageAdmin)) {
      $this->view->can_comment = 0;
    } else {
      $this->view->can_comment = 1;
    }

    $isManageAdmin = $engineApiSitestore->isManageAdmin($sitestore, 'spcreate');
    if (empty($isManageAdmin)) {
      $this->view->canCreatePhoto = $canCreatePhoto = 0;
    } else {
      $this->view->canCreatePhoto = $canCreatePhoto = 1;
    }
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    if( $is_ajax ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    $isManageAdmin = $engineApiSitestore->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = 0;
    } else {
      $this->view->can_edit = 1;
    }
    if (empty($viewer->level_id)) {
      $this->view->level_id = $level_id = 0;
    }
    else {
      $this->view->level_id = $level_id = $viewer->level_id;
    }
    $this->view->allowView = false;
    if (!empty($viewer_id) && $level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($sitestore, 'everyone', 'view') === 1 ? true : false ||$auth->isAllowed($sitestore, 'registered', 'view') === 1 ? true : false;
    } 

    //END MANAGE-ADMIN CHECK
    //CHECK THAT USER CAN UPLOAD PHOTO OR NOT
    $this->view->upload_photo = 0;

    if ($canCreatePhoto == 1 && ($engineApiSitestore->isStoreOwner($sitestore) || $album->default_value == 1)) {
      $this->view->upload_photo = 1;
    }

    //GET CURRENT SITESTORE NUMBER
    $currentStoreNumbers = $request->getParam('stores', 1);

    //SEND CURRENT SITESTORE NUMBER TO THE TPL
    $this->view->currentStoreNumbers = $currentStoreNumbers;

    //SEND PHOTOS PER SITESTORE TO THE TPL
    $this->view->photos_per_store = $photos_per_store = 10;

    //SET SITESTORE PHOTO PARAMS
    $paramsPhoto = array();
    $paramsPhoto['store_id'] = $store_id;
    $paramsPhoto['album_id'] = $album_id;
    $paramsPhoto['order'] = 'order ASC';
    $paramsPhoto['viewStore'] = 1;
    //FETCHING ALL PHOTOS
    $total_photo = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotosCount($paramsPhoto);
    if (!empty($total_photo)) {
      if (Engine_Api::_()->core()->hasSubject()) {
        Engine_Api::_()->core()->clearSubject();
      }
      Engine_Api::_()->core()->setSubject($album);
    }

    //SET DEFAULT ALBUM VALUE
    $this->view->default_value = $album->default_value;

    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['store_id'] = $store_id;
    $paramsAlbum['viewStore'] = 1;
    
    //GET ALBUM COUNT
    $this->view->album_count =  Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbumsCount($paramsAlbum);
    
    //MAKING PAGINATION 
    $store_vars = $engineApiSitestore->makeStore($total_photo, $photos_per_store, $currentStoreNumbers);
    $store_array = array();
    for ($x = 0; $x <= $store_vars[2] - 1; $x++) {
      if ($x + 1 == $store_vars[1]) {
        $link = "1";
      } else {
        $link = "0";
      }
      $store_array[$x] = array('store' => $x + 1, 'link' => $link);
    }
    $this->view->storearray = $store_array;
    $this->view->maxstore = $store_vars[2];
    $this->view->pstart = 1;

    //GET TOTAL IMAGES
    $this->view->total_images =  $total_photo;

    //SET SITESTORE PHOTO PARAMS
    $paramsPhoto['start'] = $photos_per_store;
    $paramsPhoto['end'] = $store_vars[0];
    $paramsPhoto['viewStore'] = 1;
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $paramsPhoto['albumviewStore'] = 1;
    }
    //GETTING THE PHOTOS ACCORDING TO LIMIT
    $this->view->photos = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotos($paramsPhoto);

    //INCREMENT VIEWS
    if (!$album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
      $album->view_count++;
    }

    //SAVE
    $album->save();

    //START: "SUGGEST TO FRIENDS" LINK WORK
    $store_flag = 0;
    $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
		if( !empty($is_suggestion_enabled) ) {
			$is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
			$isSupport = Engine_Api::_()->getApi('suggestion', 'sitestore')->isSupport();
			//HERE WE ARE DELETE THIS ALBUM SUGGESTION IF VIEWER HAVE
			if (!empty($is_moduleEnabled)) {
				Engine_Api::_()->getApi('suggestion', 'sitestore')->deleteSuggestion($viewer_id, 'store_album', $request->getParam('album_id'), 'store_album_suggestion');
			}

			$SuggVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion')->version;
			$versionStatus = strcasecmp($SuggVersion, '4.1.7p1');
			if( $versionStatus >= 0 ){ 
				$modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitestorealbum', $request->getParam('album_id'), 1,null);
				if (!empty($modContentObj)) {
					$contentCreatePopup = @COUNT($modContentObj);
				}
			}

			if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1)) {
				if ($sitestore->expiration_date <= date("Y-m-d H:i:s")) {
					$store_flag = 1;
				}
			}
			if (!empty($contentCreatePopup) && !empty($isSupport) && empty($sitestore->closed) && !empty($sitestore->approved) && empty($sitestore->declined) && !empty($sitestore->draft) && empty($store_flag) && !empty($viewer_id) && !empty($is_suggestion_enabled)) {
				$this->view->albumSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitestore', 'album_sugg_link');
			}
		}
    //END: "SUGGEST TO FRIENDS" LINK WORK    
  }

}
?>