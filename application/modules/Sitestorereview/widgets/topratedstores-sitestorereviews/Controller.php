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
class Sitestorereview_Widget_TopratedstoresSitestorereviewsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DON'T RENDER IF DISABLE RATING
    $ratingShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
    if (empty($ratingShow)) {
      return $this->setNoRender();
    }

    //GET SITESTORE FOR MOST RATED
		$params[] = array();
		$params['category_id'] = $this->_getParam('category_id',0);
		$params['itemCount'] = $this->_getParam('itemCount', 3);
		
    $this->view->topRatedStores = Engine_Api::_()->sitestore()->getLising('Top Rated', $params);

		//DON'T RENDER IF RESULTS COUNT IS ZERO
    if ((Count($this->view->topRatedStores) <= 0)) {
      return $this->setNoRender();
    }
  }

}
?>
