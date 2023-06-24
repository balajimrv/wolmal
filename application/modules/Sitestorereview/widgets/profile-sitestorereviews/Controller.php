<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Widget_ProfileSitestorereviewsController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //SET NO RENDER IF NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
		$this->view->store_url = $store_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_url', null);
		$store_id = Engine_Api::_()->sitestore()->getStoreId($store_url);

    //GET OBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store',$store_id);
    $this->view->store_id = $store_id;
    
    //GET SUBJECT
//     $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
//     $this->view->store_id = $sitestore->store_id;

    if (!empty($viewer_id)) {
      $this->view->level_id = $viewer->level_id;
    } else {
      $this->view->level_id = 0;
    }

    //TOTAL REVIEW
    $reviewCount = Engine_Api::_()->sitestore()->getTotalCount($this->view->store_id, 'sitestorereview', 'reviews');   
    $level_allow = Engine_Api::_()->authorization()->getPermission($this->view->level_id, 'sitestorereview_review', 'create');
    if (empty($level_allow) && empty($reviewCount) && !(Engine_Api::_()->sitestore()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }    

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

    $sitestorereview_isProfile = Zend_Registry::isRegistered('sitestorereview_isProfile') ? Zend_Registry::get('sitestorereview_isProfile') : null;
    if (empty($sitestorereview_isProfile)) {
      return $this->setNoRender();
    }

		//GET REVIEW TABLE
		$reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestorereview');

    //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
    $noReviewCheck = $reviewTable->getAvgRecommendation($sitestore->store_id);

		if (!empty($noReviewCheck)) {
			$this->view->noReviewCheck = $noReviewCheck->toArray();
			if($this->view->noReviewCheck)
			$this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
		}

    $this->view->ratingDataTopbox = Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->ratingbyCategory($sitestore->store_id);
    //END TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER

    //AJAX AND LAYOUT WORK
    $this->view->getPackageReviewView = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestorereview');
    $this->view->module_tabid = $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    $this->view->widgets = $widgets = Engine_Api::_()->sitestore()->getwidget($layout, $sitestore->store_id);
    $this->view->content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorereview.profile-sitestorereviews', $sitestore->store_id, $layout);
    $isajax = $this->_getParam('isajax', null);
    $this->view->isajax = $isajax;
    $this->view->showtoptitle = $showtoptitle = Engine_Api::_()->sitestore()->showtoptitle($layout, $sitestore->store_id);
    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) { 
      $this->view->identity_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;

			//CHECK THAT VIEWER IS POSTED REVIEW OR NOT
      $hasPosted = $reviewTable->canPostReview($sitestore->store_id, $viewer_id);
			//$level_allow = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestorereview_review', 'create');
      if (empty($hasPosted) && !empty($viewer_id) && !empty($level_allow)) {
        $this->view->can_create = 1;
      } else {
        $this->view->can_create = 0;
      }

      //START MANAGE ADMIN AND STORE-OWNER CAN NOT RATE & REVIEW
      $manageadmin_id = Engine_Api::_()->sitestorereview()->adminCantReview($sitestore->store_id, $viewer_id);
      $this->view->is_manageadmin = 0;
      if (!empty($manageadmin_id)) {
        $this->view->is_manageadmin = 1;
      }
      //START MANAGE ADMIN AND STORE-OWNER CAN NOT RATE & REVIEW


      $delete_id = $this->_getParam('delete_id');
      if (!empty($delete_id)) {
        $this->delete($delete_id);
      }

      $this->view->paginator = $paginator = $reviewTable->storeReviews($sitestore->store_id);
      $paginator->setItemCountPerPage(50);
      $this->view->current_store = $this->_getParam('store');
      $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('store'));
      $this->view->totalReviews = $paginator->getTotalItemCount();

			//ADD COUNT TO TITLE IF CONFIGURED
      if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
        $this->_childCount = $paginator->getTotalItemCount();
      } 
    } else {
      $this->view->show_content = false;
      $title_count = $this->_getParam('titleCount', false);
      $this->view->identity_temp = $this->view->identity;
      $this->_childCount = $reviewCount;
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

  //DELETE REVIEW
  public function delete($id) {
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $review = Engine_Api::_()->getItem('sitestorereview_review', $id);

      //DELETE REVIEW FROM DATABASE
      $review->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

}
?>