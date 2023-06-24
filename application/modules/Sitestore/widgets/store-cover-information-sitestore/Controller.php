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
class Sitestore_Widget_StoreCoverInformationSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->core()->hasSubject() || !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
      return $this->setNoRender();
    }

    //GET SETTING
    $statisticsElement = $this->_getParam('showContent', array("mainPhoto", "title", "followButton", "likeButton", "followCount", "likeCount"));
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
			$statisticsElement['']="memberCount";
			$statisticsElement[''] = 'addButton';
			$statisticsElement[''] = 'joinButton';
		}
		$this->view->showContent  = $statisticsElement;
		
    //GET VIEWER ID
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    //GET SITESTORE SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    $this->view->photo = $photo = Engine_Api::_()->getItem('sitestore_photo', $sitestore->store_cover);
    $this->view->columnHeight = $this->_getParam('columnHeight', '300');
    //START MANAGE-ADMIN CHECK
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
			$this->view->allowStore = Engine_Api::_()->sitestore()->allowInThisStore($sitestore, "sitestoremember", 'smecreate');
    }

    $this->view->cover_params = array('top' => 0, 'left' => 0);
    if($sitestore->store_cover) {
			$tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitestore');
			$album = $tableAlbum->getSpecialAlbum($sitestore, 'cover');
			
			if($album->cover_params)
				$this->view->cover_params = $album->cover_params;
		}
  }

}

?>