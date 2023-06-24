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
class Sitestorereview_Widget_ReviewerSitestorereviewsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		//TOTAL ITEMS IN WIDGET
		$itemCount = $this->_getParam('itemCount', 3);
		$category_id = $this->_getParam('category_id',0);

		//GET RESULTS
		$this->view->paginator = Engine_Api::_()->getDbtable('reviews', 'sitestorereview')->topReviewers($itemCount, $category_id);

		//DON'T RENDER IF NO DATA
    if (Count($this->view->paginator) <= 0) {
      return $this->setNoRender();
    }
  }
}
?>