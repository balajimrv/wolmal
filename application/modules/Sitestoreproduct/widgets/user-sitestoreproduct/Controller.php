<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_UserSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //CHECK SUBJECT
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }

    //GET PRODUCT SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    $this->view->product_id = $product_id = $sitestoreproduct->getIdentity();

    $sitestoreproductGetAttemptType = Zend_Registry::isRegistered('sitestoreproductGetAttemptType') ? Zend_Registry::get('sitestoreproductGetAttemptType') : null;
//    $sitestoreproductLsettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.lsettings', false);
//    $sitestoreproductProductTypeOrder = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.producttype.order', false);
//    $sitestoreproductProfileOrder = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.profile.order', false);
//    $sitestoreproductViewAttempt = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.view.attempt', false);
//    $sitestoreproductViewType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.viewtype', false);
//    $sitestoreproductViewAttempt = !empty($sitestoreproductGetAttemptType)? $sitestoreproductGetAttemptType: @convert_uudecode($sitestoreproductViewAttempt);
    $sitestoreproductUserReview = Zend_Registry::isRegistered('sitestoreproductUserReview') ?  Zend_Registry::get('sitestoreproductUserReview') : null;
    if (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1 || empty($sitestoreproductUserReview)) {
      return $this->setNoRender();
    }

    //GET REVIEW TABLE
    $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');

    //SET PARAMS
    $this->view->params = $this->_getAllParams();

    //UNSET CAPTCHA WORD
    $session = new Zend_Session_Namespace();
    if (isset($session->setword)) {
      unset($session->setword);
    }

    //LOADED BY AJAX
    if ($this->_getParam('loaded_by_ajax', false)) {
      $this->view->loaded_by_ajax = true;
      if ($this->_getParam('is_ajax_load', false)) {
        $this->view->is_ajax_load = true;
        $this->view->loaded_by_ajax = false;
        if (!$this->_getParam('onloadAdd', false))
          $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      } else {
        $params['resource_id'] = $product_id;
        $params['resource_type'] = $sitestoreproduct->getType();
        $params['type'] = 'user';
        $paginator = $reviewTable->listReviews($params);
        $this->_childCount = $paginator->getTotalItemCount();
        return;
      }
    }
    $this->view->showContent = true;

    //GET VIEWER ID
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    
    $tempGetFinalNumber = $sitestoreproductSponsoredOrder = $sitestoreproductFeaturedOrder = 0;
    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $this->view->level_id = $level_id = $viewer->level_id;
    } else {
      $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    $autorizationApi = Engine_Api::_()->authorization();
    $this->view->create_level_allow = $create_level_allow = $autorizationApi->getPermission($level_id, 'sitestoreproduct_product', "review_create");
//    if( !empty($sitestoreproductViewType) || (!empty($sitestoreproductProfileOrder) && !empty($sitestoreproductProductTypeOrder) && ($sitestoreproductProductTypeOrder == $sitestoreproductProfileOrder)) ) {
//      $this->view->isEnabledProductType = true;
//    }

    $this->view->can_update = $can_update = $autorizationApi->getPermission($level_id, 'sitestoreproduct_product', "review_update");

    $create_review = ($sitestoreproduct->owner_id == $viewer_id) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.allowownerreview', 0) : 1;

    if (!$create_review || empty($create_level_allow)) {
      $this->view->can_create = 0;
    } else {
      $this->view->can_create = 1;
    }

    //GET RATING TABLE
    $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitestoreproduct');
    $coreApi = Engine_Api::_()->getApi('settings', 'core');

    //GET WIDGET PARAMETERS
    $this->view->sitestoreproduct_proscons = $sitestoreproduct_proscons = $coreApi->getSetting('sitestoreproduct.proscons', 1);
    $sitestoreproduct_limit_proscons = $coreApi->getSetting('sitestoreproduct.limit.proscons', 500);
    $sitestoreproduct_recommend = $coreApi->getSetting('sitestoreproduct.recommend', 1);
    $this->view->sitestoreproduct_report = $coreApi->getSetting('sitestoreproduct.report', 1);
    $this->view->sitestoreproduct_email = $coreApi->getSetting('sitestoreproduct.email', 1);
    $this->view->sitestoreproduct_share = $coreApi->getSetting('sitestoreproduct.share', 1);

    //GET REVIEW ID
    if (!empty($viewer_id)) {
      $params = array();
      $params['resource_id'] = $sitestoreproduct->product_id;
      $params['resource_type'] = $sitestoreproduct->getType();
      $params['viewer_id'] = $viewer_id;
      $params['type'] = 'user';
      $review_id = $this->view->hasPosted = $reviewTable->canPostReview($params);
    } else {
      $review_id = $this->view->hasPosted = 0;
    }

    //CREATE FORM
    if ($this->view->can_create && !$review_id) {

      //FATCH REVIEW CATEGORIES
      $categoryIdsArray = array();
      $categoryIdsArray[] = $sitestoreproduct->category_id;
      $categoryIdsArray[] = $sitestoreproduct->subcategory_id;
      $categoryIdsArray[] = $sitestoreproduct->subsubcategory_id;
      $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

      $this->view->form = new Sitestoreproduct_Form_Review_Create(array("settingsReview" => array('sitestoreproduct_proscons' => $sitestoreproduct_proscons, 'sitestoreproduct_limit_proscons' => $sitestoreproduct_limit_proscons, 'sitestoreproduct_recommend' => $sitestoreproduct_recommend), 'item' => $sitestoreproduct, 'profileTypeReview' => $profileTypeReview));
    }

    //UPDATE FORM
    if ($can_update && $review_id) {
      $this->view->update_form = $update_form = new Sitestoreproduct_Form_Review_Update(array('item' => $sitestoreproduct));
    }

    //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
    $params = array();
    $params['resource_id'] = $product_id;
    $params['resource_type'] = $sitestoreproduct->getType();
    $params['type'] = 'user';
    $noReviewCheck = $reviewTable->getAvgRecommendation($params);
		if (!empty($noReviewCheck)) {
			$this->view->noReviewCheck = $noReviewCheck->toArray();
			if($this->view->noReviewCheck)
			$this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
		}
    $this->view->ratingDataTopbox = $ratingTable->ratingbyCategory($product_id, 'user', $sitestoreproduct->getType());

    $this->view->isajax = $this->_getParam('isajax', 0);

    //GET FILTER
    $option = $this->_getParam('option', 'fullreviews');
    $this->view->reviewOption = $params['option'] = $option;

    //SET ITEM PER PAGE
    if ($option == 'prosonly' || $option == 'consonly') {
      $this->view->itemProsConsCount = $setItemCountPerPage = $this->_getParam('itemProsConsCount', 20);
    } else {
      $this->view->itemReviewsCount = $setItemCountPerPage = $this->_getParam('itemReviewsCount', 5);
    }

    //GET SORTING ORDER
    $this->view->reviewOrder = $params['order'] = $this->_getParam('order', 'creationDate');
    $this->view->rating_value = $this->_getParam('rating_value', 0);

    $params['rating'] = 'rating';
    $params['rating_value'] = $this->view->rating_value;
    $params['resource_id'] = $product_id;
    $params['resource_type'] = $sitestoreproduct->getType();
    $params['type'] = 'user';
    $this->view->params = $params;
    $paginator = $reviewTable->listReviews($params);
    $this->view->paginator = $paginator->setItemCountPerPage($setItemCountPerPage);
    $this->view->current_page = $current_page = $this->_getParam('page', 1);
    $this->view->paginator = $paginator = $paginator->setCurrentPageNumber($current_page);
    //GET TOTAL REVIEWS
    $this->_childCount = $this->view->totalReviews = $paginator->getTotalItemCount();

    //FATCH REVIEW CATEGORIES
    $categoryIdsArray = array();
    $categoryIdsArray[] = $sitestoreproduct->category_id;
    $categoryIdsArray[] = $sitestoreproduct->subcategory_id;
    $categoryIdsArray[] = $sitestoreproduct->subsubcategory_id;
    $this->view->reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct')->reviewParams($categoryIdsArray, $sitestoreproduct->getType());

    //COUNT REVIEW CATEGORY
    $this->view->total_reviewcats = Count($this->view->reviewCategory);

    //GET REVIEW RATE DATA
    $this->view->reviewRateMyData = $this->view->reviewRateData = $ratingTable->ratingsData($review_id);

    //CAN DELETE
    $this->view->can_delete = $autorizationApi->getPermission($level_id, 'sitestoreproduct_product', "review_delete");

    //CAN REPLY
    $this->view->can_reply = $autorizationApi->getPermission($level_id, 'sitestoreproduct_product', "review_reply");

    //CHECK PAGE
    $this->view->checkPage = "productProfile";

    //CUSTOM FIELDS
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestoreproduct/View/Helper', 'Sitestoreproduct_View_Helper');
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}