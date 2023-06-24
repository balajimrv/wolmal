<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_ItemSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//FETCH DATA
    $this->view->dayitem = Engine_Api::_()->getDbtable('stores', 'sitestore')->getItemOfDay();
   
    //DONT RENDER IF SITESTORE COUNT ZERO
    if (!(count($this->view->dayitem) > 0)) {
      return $this->setNoRender();
    }
  }
}
?>