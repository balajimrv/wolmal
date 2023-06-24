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
class Sitestoreproduct_Widget_ReviewsStatisticsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
    $paginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'resource_type' => 'sitestoreproduct_product'));

    $this->view->totalReviews = $paginator->getTotalItemCount();
    $recommendpaginator = $reviewTable->getReviewsPaginator(array('type' => 'user', 'recommend' => 1, 'resource_type' => 'sitestoreproduct_product'));

    $this->view->totalRecommend = $recommendpaginator->getTotalItemCount();
    $ratingTable = Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct');
    $ratingCount = array();
    
    for ($i = 5; $i > 0; $i--) {
      $ratingCount[$i] = $ratingTable->getNumbersOfUserRating(0, 'user', 0, $i, 0, 'sitestoreproduct_product');
    }
    
    $this->view->ratingCount = $ratingCount;
  }

}
