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
class Sitestorereview_Widget_RatingsSitestorereviewsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //SET NO RENDER IF NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT
		$store_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_url', null);
		$store_id = Engine_Api::_()->sitestore()->getStoreId($store_url);

    //GET OBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store',$store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

    $sitestorereview_ratingInfo = Zend_Registry::isRegistered('sitestorereview_ratingInfo') ? Zend_Registry::get('sitestorereview_ratingInfo') : null;
    if (empty($sitestorereview_ratingInfo)) {
      return $this->setNoRender();
    }

    //SET NO RENDER IF NO REVIEW CORROSPONDING TO THIS STORE ID
    $noReviewCheck =  Engine_Api::_()->getDbTable('reviews', 'sitestorereview')->getAvgRecommendation($sitestore->store_id);
		if (empty($noReviewCheck)) {
      return $this->setNoRender();
    }

		if (!empty($noReviewCheck)) {
			$this->view->noReviewCheck = $noReviewCheck->toArray();
			if($this->view->noReviewCheck)
			$this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
		}

    //GETTING RATING DATA
    $this->view->ratingData = $ratingData = Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->ratingbyCategory($sitestore->store_id);

    if (empty($ratingData)) {
      return $this->setNoRender();
    }

    //GET VIEWER INFO
		$viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    //CAN CREATE A REVIEW OR NOT
    $hasPosted = Engine_Api::_()->getDbTable('reviews', 'sitestorereview')->canPostReview($sitestore->store_id, $viewer_id);
		$level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestorereview_review', 'create');
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
    //END MANAGE ADMIN AND STORE-OWNER CAN NOT RATE & REVIEW

    //TOTAL REVIEWS BELONGS TO THIS STORE
    $this->view->totalReviews = Engine_Api::_()->getDbTable('reviews', 'sitestorereview')->totalReviews($sitestore->store_id);

    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    $this->view->content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorereview.profile-sitestorereviews', $sitestore->store_id, $layout);
  }

}
?>