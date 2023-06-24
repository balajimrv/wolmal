<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoremember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestore_Widget_StorecoverPhotoSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
		if (!Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitestorealbum' )) {
			return $this->setNoRender();
		}
		
    $this->view->show_member = $show_member = $this->_getParam('show_member', 1);

    //GET SITESTORE SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
		$this->view->photo = $photo = Engine_Api::_()->getItem('sitestore_photo', $sitestore->store_cover);
		//CHECK REQUEST IS ISAJAX OR NOT
    $this->view->isAjax = $isAjax = $this->_getParam('isAjax', null);
		if (empty($show_member) && empty($sitestore->store_cover) && empty($can_edit)) {
			return $this->setNoRender();
		}
		
    $zendRequest = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->identity_temp = $currenttabid = $zendRequest->getParam('tab', null);

    //START MANAGE-ADMIN CHECK
    $this->view->can_edit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
     $this->view->paginatorCount = 0;
    if($isAjax && Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'sitestoremember' )) {
			$this->view->paginator = $paginator = Engine_Api::_()->getDbtable('membership', 'sitestore')->getJoinMembers($sitestore->store_id);
    }
  }
}