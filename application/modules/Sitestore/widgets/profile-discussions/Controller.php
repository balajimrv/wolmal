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
class Sitestore_Widget_ProfileDiscussionsController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR FETCHING THE DISCUSSIONS FOR THE STORES
  public function indexAction() {

    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() == 'sitestore_store') {
      return $this->setNoRender();
    } else {
      if ($subject->getType() == 'sitestoreevent_event') {
        $sitestore = $subject->getParentStore();
      } elseif ($subject->getType() == 'sitestoremusic_playlist') {
        $sitestore = $subject->getParentType();
      } else {
        $sitestore = $subject->getParent();
      }
    }

    if (!$sitestore || $sitestore->getType() !== 'sitestore_store') {
      return $this->setNoRender();
    }

    //GET SITESTORE SUBJECT
    $this->view->sitestore = $sitestore;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
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

    $this->view->canPost = $topicComment = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdicreate');

    if (empty($topicComment)) {
      return $this->setNoRender();
    }

    //GET CURRENT STORE NUMBER     
    $store = $this->_getParam('store', 1);

    //GET PAGINATORS
    $this->view->paginators = $paginators = Engine_Api::_()->getDbtable('topics', 'sitestore')->getStoreTopics($sitestore->store_id, array('resource_type' => $subject->getType(), 'resource_id' => $subject->getIdentity()));
    $paginators->setItemCountPerPage(100)->setCurrentPageNumber($store);

    //ADD COUNT TO TITLE IF CONFIGURED
    if ($this->_getParam('titleCount', false) && $paginators->getTotalItemCount() > 0) {
      $this->_childCount = $paginators->getTotalItemCount();
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}