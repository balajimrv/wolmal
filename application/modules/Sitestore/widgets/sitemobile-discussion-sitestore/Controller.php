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
class Sitestore_Widget_SitemobileDiscussionSitestoreController extends Engine_Content_Widget_Abstract {

  protected $_childCount;
  
  //ACTION FOR FETCHING THE DISCUSSIONS FOR THE STORES
  public function indexAction() { 	

    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    //GET SITESTORE SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    
    //GET STORE ID
    $this->view->store_id = $sitestore->store_id;


    //START PACKAGE LEVEL CHECK
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorediscussion")) {
        return $this->setNoRender();
      }
    } else {
      $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sdicreate');
      if (empty($isStoreOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //END PACKAGE LEVEL CHECK
    //TOTAL TOPICS
    $topicCount = Engine_Api::_()->sitestore()->getTotalCount($this->view->store_id, 'sitestore', 'topics');  
    
    $topicComment = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdicreate');
    
    
    if (empty($topicComment) && empty($topicCount) && !(Engine_Api::_()->sitestore()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }
    

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    $this->view->canPost =  Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdicreate');
    //END MANAGE-ADMIN CHECK
    
		//GET PAGINATORS
		$this->view->paginator = $paginator = Engine_Api::_()->getDbtable('topics', 'sitestore')->getStoreTopics($sitestore->store_id);
		$this->view->paginator->setItemCountPerPage(10)->setCurrentPageNumber($this->_getParam('store', 1));
	  $this->_childCount = $paginator->getTotalItemCount();

  }

  public function getChildCount() {
    return $this->_childCount;
  }

}