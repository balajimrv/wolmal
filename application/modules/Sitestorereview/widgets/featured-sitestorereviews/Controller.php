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
class Sitestorereview_Widget_FeaturedSitestorereviewsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  { 
		//GET PARAMETERS FOR FETCH DATA
		$this->view->itemCount = $itemCount = $this->_getParam('itemCount', 3);
		$this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
		$this->view->store = $store = $this->_getParam('store');

		//FETCH REVIEW DATA
    $params = array();
		$params['limit'] = $itemCount;
		$params['store_validation'] = 1;
		$params['featured'] = 1;
		$params['category_id'] = $this->_getParam('category_id',0);
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('reviews', 'sitestorereview')->reviewRatingData($params);
    $paginator->setItemCountPerPage($itemCount);
    $this->view->paginator = $paginator->setCurrentPageNumber($store);

		//CALCULATE TOTAL STORES
		$total_items = $paginator->getTotalItemCount(); 
		$this->view->total_store =  $total_items/$itemCount;
		if($this->view->total_store > (int)$this->view->total_store) {
			$this->view->total_store += 1;
		}

		//DON'T RENDER IF NO DATA FOUND
    if ($total_items <= 0) {
      return $this->setNoRender();
    }
  }
}
?>