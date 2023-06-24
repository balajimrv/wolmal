<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_Widget_SitemobileProfileSitestorevideosController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT AND STORE ID
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    $store_id = $sitestore->store_id;

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET LEVEL INFO
    if (!empty($viewer_id)) {
      $this->view->level_id = $level_id = $viewer->level_id;
    } else {
      $this->view->level_id = $level_id = 0;
    }

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo")) {
        return $this->setNoRender();
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
      if (empty($isStoreOwnerAllow)) {
        return $this->setNoRender();
      }
    }

    //PACKAGE BASE PRIYACY END
    
    //TOTAL VIDEO
    $videoCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestorevideo', 'videos');   
    $videoCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
        
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }

    if (empty($videoCount) && empty($videoCreate) && empty($can_edit) && !(Engine_Api::_()->sitestore()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
  
    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = true;
    } 

		//START MANAGE-ADMIN CHECK
		$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
		if (empty($isManageAdmin) && empty($can_edit)) {
			$this->view->can_create = 0;
		} else {
			$this->view->can_create = 1;
		}
		//END MANAGE-ADMIN CHECK

		$this->view->store_id = $values['store_id'] = $store_id;

		//FETCH VIDEOS
		if ($can_edit) {
			$values['show_video'] = 0;
			$this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitestorevideo_video')->getSitestorevideosPaginator($values);
		} else {
			$values['show_video'] = 1;
			$values['video_owner_id'] = $viewer_id;
			$this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitestorevideo_video')->getSitestorevideosPaginator($values);
		}

		$this->view->paginator->setItemCountPerPage(10)->setCurrentPageNumber($this->_getParam('store', 1));
		$this->_childCount = $paginator->getTotalItemCount();

  }

  public function getChildCount() {
    return $this->_childCount;
  }

}
