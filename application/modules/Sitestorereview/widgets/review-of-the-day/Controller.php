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

class Sitestorereview_Widget_ReviewOfTheDayController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET REVIEW OF THE DAY
    $this->view->reviewOfDay = Engine_Api::_()->getDbtable('reviews', 'sitestorereview')->reviewOfDay();
  
		//DON'T RENDER IF NO REVIEW OF THE DAY IS EXIST
    if (empty($this->view->reviewOfDay)) {
      return $this->setNoRender();
    }
  }
}
?>