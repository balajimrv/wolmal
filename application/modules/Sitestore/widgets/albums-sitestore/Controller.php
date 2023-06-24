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
class Sitestore_Widget_AlbumsSitestoreController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR SHOWING THE RANDOM ALBUMS AND PHOTOS BY OTHERS
  public function indexAction() {

  	//HERE WE CHECKING THE SITESTORE ALBUM IS ENABLED OR NOT
		$sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
		if (!$sitestorealbumEnabled) {
			return $this->setNoRender();
		}

    //DON'T RENDER IF SUNJECT IS NOT THERE
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SITESTORE SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

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

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

    //GET STORE ID
    $store_id = $sitestore->store_id;

    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['store_id'] = $store_id;
    $paramsAlbum['orderby'] = ' RAND()';

    //GET ALBUM TABLE
    $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitestore');

    //GET PHOTO TABLE
    $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitestore');

    //ALBUM COUNT
		$this->view->albumcount = $tableAlbum->getAlbumsCount($paramsAlbum);

    //GET VIEWER ID
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity(); 
    
    //SET ALBUMS PARAMS
    $paramsAlbum['start'] = 2;
    $paramsAlbum['getSpecialField'] = 0;

    //FETCH ALBUMS
		$fecthAlbums = $tableAlbum->getAlbums($paramsAlbum);
    if (!empty($fecthAlbums)) {
      $this->view->album = $this->view->paginator = $fecthAlbums;
    }

    //IF COUNT IS ZERO THEN NO RENDER
    if ($this->view->albumcount <= 0) {
      return $this->setNoRender();
    }

    //SET PHOTO PARAMS
    $paramsPhoto = array();
    $paramsPhoto['store_id'] = $store_id;
    $paramsPhoto['user_id'] = $sitestore->owner_id;
    $paramsPhoto['album_id'] = Engine_Api::_()->getItemTable('sitestore_album')->getDefaultAlbum($store_id)->album_id;;
    $paramsPhoto['orderby'] = 'RAND()';

    //FETCHING ALL PHOTOS
		$this->view->totalphotosothers = $tablePhoto->getPhotosCount($paramsPhoto);

    //SET PHOTO PARAMS
    $paramsPhoto['start'] = 4;

    //GET ALL PHOTOS
    $this->view->paginators = $tablePhoto->getPhotos($paramsPhoto);

    //SEND CURRENT TAB ID TO THE TPL
    $this->view->tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $store_id, Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0));
  }

}

?>