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

class Sitestorereview_Widget_ReviewTabsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET PARAMETERS FOR FETCH DATA
		$this->view->category_id = $category_id = $this->_getParam('category_id',0);
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
		$this->view->tabName = $tabName = $this->_getParam('tabName', 'recent');
		$this->view->itemCount = $itemCount = $this->_getParam('itemCount', 3);
		$this->view->popularity = $popularity = $this->_getParam('popularity', 'view_count');
		$default_visibility = array('recent','popular','reviewer');
		$this->view->visibility = $this->_getParam('visibility', $default_visibility);

		if($tabName == 'popular') {

			//GET RESULTS
			$params = array();
			$params['orderby'] = "$popularity DESC";
			$params['category_id'] = $category_id;
			$params['zero_count'] = $popularity;
			$params['limit'] = $itemCount;
			$params['store_validation'] = 1;
			$this->view->paginator = Engine_Api::_()->getDbtable('reviews', 'sitestorereview')->reviewRatingData($params,$category_id);

		}
		elseif($tabName == 'reviewer') {

			//GET RESULTS
			$this->view->paginator = Engine_Api::_()->getDbtable('reviews', 'sitestorereview')->topReviewers($itemCount);
		}
		else {

			//GET RESULTS
			$params = array();
			$params['category_id'] = $category_id;
			$params['limit'] = $itemCount;
			$params['store_validation'] = 1;
			$this->view->paginator = Engine_Api::_()->getDbtable('reviews', 'sitestorereview')->reviewRatingData($params);
		}
	}
}
?>