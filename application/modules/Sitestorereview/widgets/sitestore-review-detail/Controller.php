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
class Sitestorereview_Widget_SitestoreReviewDetailController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->sitestorereview = $sitestorereview = Engine_Api::_()->getItem('sitestorereview_review', Zend_Controller_Front::getInstance()->getRequest()->getParam('review_id'));
    if (empty($sitestorereview)) {
      return $this->setNoRender();
    }
  }

}
?>