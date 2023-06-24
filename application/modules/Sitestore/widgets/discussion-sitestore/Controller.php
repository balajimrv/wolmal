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
class Sitestore_Widget_DiscussionSitestoreController extends Engine_Content_Widget_Abstract {

  protected $_childCount;
  
  //ACTION FOR FETCHING THE DISCUSSIONS FOR THE STORE
  public function indexAction() { 	
  	
    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    //GET SUBJECT
    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitestore_store') {
    	$this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    }
    else {
      $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject()->getParent();
    }
     
    
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
    
    //GET WHICH LAYOUT IS SET BY THE ADMIN
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    
    //GET THIRD TYPE LAYOUT IS SET OR NOT
    $this->view->widgets = $widgets = Engine_Api::_()->sitestore()->getwidget($layout, $sitestore->store_id);
    
    //GET TAB ID
    $this->view->content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.discussion-sitestore', $sitestore->store_id, $layout);
    
    //GET CURRENT TAB ID
    $this->view->module_tabid = $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    
    //CHECK REQUEST IS ISAJAX OR NOT
    $this->view->isajax = $isajax = $this->_getParam('isajax', null);
    
    //SHOW TOP TITLE
    $this->view->showtoptitle = Engine_Api::_()->sitestore()->showtoptitle($layout, $sitestore->store_id);
    
    //CHECK REQUEST IS AJAX OR NOT OR CURRENT TAB ID OR LAYOUT
    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {
      $this->view->identity_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;  

      //GET CURRENT STORE NUMBER
      $store = $this->_getParam('store', 1);
      
      //GET PAGINATORS
      $this->view->paginators = $paginators = Engine_Api::_()->getDbtable('topics', 'sitestore')->getStoreTopics($sitestore->store_id);
      $paginators->setItemCountPerPage(10)->setCurrentPageNumber($store);

      //ADD COUNT TO TITLE IF CONFIGURED
      if ($this->_getParam('titleCount', false) && $paginators->getTotalItemCount() > 0) {
        $this->_childCount = $paginators->getTotalItemCount();
      }
    } else {
      $this->view->show_content = false;
      $title_count = $this->_getParam('titleCount', false);
      $this->view->identity_temp = $this->view->identity;
      $this->_childCount = Engine_Api::_()->sitestore()->getTotalCount( $this->view->store_id, 'sitestore', 'topics');
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }
}

?>