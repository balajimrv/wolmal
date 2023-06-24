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
class Sitestoreproduct_Widget_ProfileReviewSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_review')) {
      return $this->setNoRender();
    }

    //UNSET THE CAPTECHA WORD
    $session = new Zend_Session_Namespace();
    if (isset($session->setword)) {
      unset($session->setword);
    }

    //SET PARAMS
    $this->view->params = $params = $this->_getAllParams();

    //GET VIEWER DETAIL
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $this->view->level_id = $level_id = $viewer->level_id;
    } else {
      $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    //GET REVIEW TABLE
    $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');

    //GET RATING TABLE
    $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct');

    //GET SITESTOREPRODUCT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject()->getParent();
    $sitestoreproductProfileReview = Zend_Registry::isRegistered('sitestoreproductProfileReview') ? Zend_Registry::get('sitestoreproductProfileReview') : null;

    $this->view->product_id = $product_id = $sitestoreproduct->product_id;
    $resource_type = $sitestoreproduct->getType();

    if (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1 || empty($sitestoreproductProfileReview)) {
      return $this->setNoRender();
    }

    //SET HAS POSTED
    if (empty($viewer_id)) {
      $hasPosted = $this->view->hasPosted = 0;
    } else {
      $params = array();
      $params['resource_id'] = $sitestoreproduct->product_id;
      $params['resource_type'] = $resource_type;
      $params['viewer_id'] = $viewer_id;
      $params['type'] = 'user';
      $hasPosted = $this->view->hasPosted = $reviewTable->canPostReview($params);
    }

    //GET WIDGET PARAMETERS
    $coreApi = Engine_Api::_()->getApi('settings', 'core');
    $this->view->sitestoreproduct_proscons = $sitestoreproduct_proscons = $coreApi->getSetting('sitestoreproduct.proscons', 1);
    $this->view->sitestoreproduct_limit_proscons = $sitestoreproduct_limit_proscons = $coreApi->getSetting('sitestoreproduct.limit.proscons', 500);
    $this->view->sitestoreproduct_recommend = $sitestoreproduct_recommend = $coreApi->getSetting('sitestoreproduct.recommend', 1);
    $this->view->sitestoreproduct_report = $coreApi->getSetting('sitestoreproduct.report', 1);
    $this->view->sitestoreproduct_email = $coreApi->getSetting('sitestoreproduct.email', 1);
    $this->view->sitestoreproduct_share = $coreApi->getSetting('sitestoreproduct.share', 1);

    $this->view->create_level_allow = $create_level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "review_create");

    $create_review = ($sitestoreproduct->owner_id == $viewer_id) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.allowownerreview', 0) : 1;

    if (!$create_review || empty($create_level_allow)) {
      $this->view->can_create = 0;
    } else {
      $this->view->can_create = 1;
    }

    $this->view->can_delete = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "review_delete");

    $this->view->can_reply = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "review_reply");

    $this->view->can_update = $can_update = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestoreproduct_product', "review_update");

    //MAKE CREATE FORM
    if ($this->view->can_create && !$hasPosted) {

      //FATCH REVIEW CATEGORIES
      $categoryIdsArray = array();
      $categoryIdsArray[] = $sitestoreproduct->category_id;
      $categoryIdsArray[] = $sitestoreproduct->subcategory_id;
      $categoryIdsArray[] = $sitestoreproduct->subsubcategory_id;
      $profileTypeReview = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getProfileType($categoryIdsArray, 0, 'profile_type_review');

      $this->view->form = $form = new Sitestoreproduct_Form_Review_Create(array("settingsReview" => array('sitestoreproduct_proscons' => $this->view->sitestoreproduct_proscons, 'sitestoreproduct_limit_proscons' => $this->view->sitestoreproduct_limit_proscons, 'sitestoreproduct_recommend' => $this->view->sitestoreproduct_recommend), 'item' => $sitestoreproduct, 'profileTypeReview' => $profileTypeReview));
    }

    $this->view->review_id = $review_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('review_id');

    //UPDATE FORM
    if ($can_update && $hasPosted) {
      $this->view->update_form = new Sitestoreproduct_Form_Review_Update(array('item' => $sitestoreproduct));
    }

    //GET REVIEW ITEM
    $this->view->reviews = Engine_Api::_()->getItem('sitestoreproduct_review', $review_id);
    $this->view->tab_id = Engine_Api::_()->sitestoreproduct()->getTabId('sitestoreproduct.user-sitestoreproduct');
    $params = array();
    $params['resource_id'] = $product_id;
    $params['resource_type'] = $resource_type;
    $params['type'] = 'user';
    $this->view->totalReviews = $reviewTable->totalReviews($params);

    if ($this->view->reviews->profile_type_review) {
      //CUSTOM FIELDS
      $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestoreproduct/View/Helper', 'Sitestoreproduct_View_Helper');
      $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($this->view->reviews);
    }

    //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
    $noReviewCheck = $reviewTable->getAvgRecommendation($params);
		if (!empty($noReviewCheck)) {
			$this->view->noReviewCheck = $noReviewCheck->toArray();
			if($this->view->noReviewCheck)
			$this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
			$this->view->ratingDataTopbox = $ratingTable->ratingbyCategory($product_id, 'user', $resource_type);
		}

    //FATCH REVIEW CATEGORIES
    $categoryIdsArray = array();
    $categoryIdsArray[] = $sitestoreproduct->category_id;
    $categoryIdsArray[] = $sitestoreproduct->subcategory_id;
    $categoryIdsArray[] = $sitestoreproduct->subsubcategory_id;
    $this->view->reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct')->reviewParams($categoryIdsArray, $resource_type);
    $this->view->total_reviewcats = Count($this->view->reviewCategory);
    $this->view->reviewRateData = $ratingTable->ratingsData($review_id);
    $this->view->reviewRateMyData = $ratingTable->ratingsData($hasPosted);
    $this->view->checkPage = "reviewProfile";
    $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', '');
    $this->view->price = $sitestoreproduct->price;
  }

}