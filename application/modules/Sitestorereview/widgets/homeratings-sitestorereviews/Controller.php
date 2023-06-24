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
class Sitestorereview_Widget_HomeratingsSitestorereviewsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //TOTAL REVIEWS BELONGS TO  STORE
    $this->view->totalReviews = Engine_Api::_()->getDbTable('reviews', 'sitestorereview')->totalReviews();
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
    $this->view->content_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorereview.profile-sitestorereviews', $sitestore->store_id, $layout);
  }

}
?>