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
class Sitestorereview_Widget_FeaturedReviewsSlideshowController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    //SEARCH PARAMETER
    $params = array();
    $params['store_validation'] = 1;
		$params['featured'] = 1;
    $params['limit'] = $this->_getParam('itemCountPerStore', 10);
    $params['category_id'] = $this->_getParam('category_id',0);
    $this->view->show_slideshow_object = $featuredReviews = Engine_Api::_()->getDbTable('reviews', 'sitestorereview')->reviewRatingData($params);
    
    $count = $featuredReviews->getTotalItemCount();
    // Count Featured Reviews
    $this->view->num_of_slideshow = $count;
    // Number of the result.
    if (empty($this->view->num_of_slideshow)) {
      return $this->setNoRender();
    }
  }

}
?>