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
class Sitestoreproduct_Widget_OverallRatingsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //SET NO RENDER IF NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');

    //GET SETTING
    $this->view->show_rating = $show_rating = $this->_getParam('show_rating', 'both');
    $this->view->ratingParameter = $ratingParameter = $this->_getParam('ratingParameter', 1);
    //DO NOT RENDER THIS WIDGET IF BOTH TYPE OF REVIEWS ARE NOT ALLOWED
    $this->view->reviewsAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2);
    if (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2))) {
      return $this->setNoRender();
    } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1) {
      $this->view->show_rating = $show_rating = 'editor';
    } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2) {
      $this->view->show_rating = $show_rating = 'avg';
    }

    $this->view->product_id = $product_id = $sitestoreproduct->getIdentity();

    //GET REVIEW TABLE
    $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
    //GET RATING TABLE
    $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitestoreproduct');

    if ($show_rating == 'both' || $show_rating == 'avg') {
      //START TOP SECTION FOR OVERALL RATING AND IT'S PARAMETER
      $params = array();
      $params['resource_id'] = $product_id;
      $params['resource_type'] = $sitestoreproduct->getType();
      $noReviewCheck = $reviewTable->getAvgRecommendation($params);
			if (!empty($noReviewCheck)) {
				$this->view->noReviewCheck = $noReviewCheck->toArray();
				if($this->view->noReviewCheck)
				$this->view->recommend_percentage = round($noReviewCheck[0]['avg_recommend'] * 100, 3);
			}
      $type = null;
      if ($show_rating == 'both') {
        $type = 'user';
      }
      $this->view->type = $type;
      $this->view->ratingData = $ratingTable->ratingbyCategory($product_id, $type, $sitestoreproduct->getType());
    }

    $this->view->editorReview = 0;
    if ($show_rating == 'both' || $show_rating == 'editor') {
      $this->view->ratingEditorData = $ratingTable->ratingbyCategory($product_id, 'editor', $sitestoreproduct->getType());
      $this->view->editorReview = $sitestoreproduct->getEditorReview();
    }
  }

}