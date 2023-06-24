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
class Sitestorereview_Widget_HomelikeSitestorereviewsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
   
		$params = array();
		$params['orderby'] = 'like_count DESC';
		$params['zero_count'] = 'like_count';
		$params['category_id'] = $this->_getParam('category_id',0);
		$params['limit'] = $this->_getParam('itemCount', 3);
    $params['store_validation'] = 1;
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('reviews', 'sitestorereview')->reviewRatingData($params);

    if (Count($paginator) <= 0) {
      return $this->setNoRender();
    }
  }

}
?>