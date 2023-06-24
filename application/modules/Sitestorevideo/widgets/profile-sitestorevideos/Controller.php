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
class Sitestorevideo_Widget_ProfileSitestorevideosController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT AND STORE ID
    //GET SUBJECT
    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitestore_store') {
    	$this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    }
    else {
      $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject()->getParent();
    }

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
    $sitestore_isProfile = Zend_Registry::isRegistered('sitestorevideo_isProfile') ? Zend_Registry::get('sitestorevideo_isProfile') : null;

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

    if (empty($sitestore_isProfile)) {
      return $this->setNoRender();
    }
    //PACKAGE BASE PRIYACY END
    //TOTAL VIDEO
    $videoCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestorevideo', 'videos');
    $videoCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
    if (empty($videoCount) && empty($videoCreate) && !(Engine_Api::_()->sitestore()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }

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
    //END MANAGE-ADMIN CHECK

    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = true;
    }

    //MAKE HIGHLIGHTED OR NOT
    $this->view->canMakeHighlighted = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.featured', 1);

    $this->view->module_tabid = $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    //Getting the tab id from the content table.
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    $this->view->widgets = $widgets = Engine_Api::_()->sitestore()->getwidget($layout, $store_id);
    $this->view->content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorevideo.profile-sitestorevideos', $store_id, $layout);
    $isajax = $this->_getParam('isajax', null);
    $this->view->showtoptitle = $showtoptitle = Engine_Api::_()->sitestore()->showtoptitle($layout, $store_id);
    $this->view->isajax = $isajax;
    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {
      $this->view->identity_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;

      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
      if (empty($isManageAdmin)) {
        $this->view->can_create = 0;
      } else {
        $this->view->can_create = 1;
      }
      //END MANAGE-ADMIN CHECK
      //GET SEARCHING PARAMETERS
      $this->view->store = $store = $this->_getParam('store', 1);
      $this->view->search = $search = $this->_getParam('search');
      $this->view->selectbox = $selectbox = $this->_getParam('selectbox');
      $this->view->checkbox = $checkbox = $this->_getParam('checkbox');
      $values = array();
      $values['orderby'] = '';
      if (!empty($selectbox) && $selectbox == 'featured') {
        $values['featured'] = 1;
        $values['orderby'] = 'creation_date';
      }
      if (!empty($search)) {
        $values['search'] = $search;
      }
      if (!empty($selectbox)) {
        $values['orderby'] = $selectbox;
      }
      if (!empty($checkbox) && $checkbox == 1) {
        $values['owner_id'] = $viewer_id;
      }

      $values['store_id'] = $store_id;

      //SEND STORE ID TO THE TPL
      $this->view->store_id = $store_id = $store_id;

      //FETCH VIDEOS
      if ($can_edit) {
        $values['show_video'] = 0;
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitestorevideo_video')->getSitestorevideosPaginator($values);
      } else {
        $values['show_video'] = 1;
        $values['video_owner_id'] = $viewer_id;
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitestorevideo_video')->getSitestorevideosPaginator($values);
      }

      $this->view->paginator = $paginator->setItemCountPerPage(10);

      $this->view->paginator->setCurrentPageNumber($this->_getParam('store', 1));

      if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
        $this->_childCount = $paginator->getTotalItemCount();
      }

      $this->view->current_count = $paginator->getTotalItemCount();
    } else {
      $this->view->show_content = false;
      $title_count = $this->_getParam('titleCount', false);
      $this->view->identity_temp = $this->view->identity;

      $values = array();
      $values['orderby'] = 'creation_date';
      $values['store_id'] = $store_id;
      $values['show_count'] = 1;
      if ($can_edit) {
        $values['show_video'] = 0;
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitestorevideo_video')->getSitestorevideosPaginator($values);
      } else {
        $values['show_video'] = 1;
        $values['video_owner_id'] = $viewer_id;
        $paginator = Engine_Api::_()->getItemTable('sitestorevideo_video')->getSitestorevideosPaginator($values);
      }

      $this->_childCount = $paginator->getTotalItemCount();
    }
     $this->view->sitevideoviewEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideoview'); 
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}
?>