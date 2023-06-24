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
class Sitestorereview_Widget_RecentSitestorereviewsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $sitestorereview_recentInfo = Zend_Registry::isRegistered('sitestorereview_recentInfo') ? Zend_Registry::get('sitestorereview_recentInfo') : null;

		//FETCH REVIEW DATA
    $params = array();
    $params['category_id'] = $this->_getParam('category_id',0);
		$params['limit'] = $this->_getParam('itemCount', 3);
		$params['store_validation'] = 1;
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('reviews', 'sitestorereview')->reviewRatingData($params);

		//DON'T RENDER IF NO DATA FOUND
    if ((Count($paginator) <= 0) || empty($sitestorereview_recentInfo)) {
      return $this->setNoRender();
    }
  }

}
?>