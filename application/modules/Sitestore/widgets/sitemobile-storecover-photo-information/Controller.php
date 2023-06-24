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
class Sitestore_Widget_SitemobileStorecoverPhotoInformationController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $this->view->strachPhoto = $this->_getParam('strachPhoto', 0);
    $this->view->showContent = $this->_getParam('showContent', array("mainPhoto", "title", "category", "subcategory", "subsubcategory",  "likeButton", "followButton", "joinButton", "addButton", "description", "phone", "email", "website", "location", "tags", "price", "badge"));

    //GET VIEWER INFORMATION
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    $this->view->allowStore = Engine_Api::_()->sitestore()->allowInThisStore($sitestore, "sitestoremember", 'smecreate');
    $this->view->cover_params = array('top' => 0, 'left' => 0);

    if(Engine_Api::_()->hasModuleBootstrap('sitestorebadge') && isset($sitestore->badge_id))  {
			$this->view->sitestorebadges_value = Engine_Api::_()->getApi('settings', 'core')->sitestorebadge_badgeprofile_widgets;
			$this->view->sitestorebadge = Engine_Api::_()->getItem('sitestorebadge_badge', $sitestore->badge_id);
    }

    if (Engine_Api::_()->hasModuleBootstrap('sitestorealbum') && $sitestore->store_cover) {
      $this->view->photo = $photo = Engine_Api::_()->getItem('sitestore_photo', $sitestore->store_cover);
      $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitestore');
      $album = $tableAlbum->getSpecialAlbum($sitestore, 'cover');
      if ($album->cover_params)
        $this->view->cover_params = $album->cover_params;
    }
    $this->view->sitestoreTags = $sitestore->tags()->getTagMaps();
    $this->view->resource_id = $resource_id = $sitestore->getIdentity();
    $this->view->resource_type = $resource_type = $sitestore->getType();
    $this->view->follow_count = Engine_Api::_()->getDbtable('follows', 'seaocore')->numberOfFollow($resource_type, $resource_id);
    $this->view->subcategory_name = '';
    $this->view->subsubcategory_name = '';
    $categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitestore');
    $this->view->category_name = $categoriesTable->getCategory($sitestore->category_id)->category_name;
    if(isset($categoriesTable->getCategory($sitestore->subcategory_id)->category_name))
    $this->view->subcategory_name = $categoriesTable->getCategory($sitestore->subcategory_id)->category_name;
    if(isset($categoriesTable->getCategory($sitestore->subsubcategory_id)->category_name))
    $this->view->subsubcategory_name = $categoriesTable->getCategory($sitestore->subsubcategory_id)->category_name;
  }

}