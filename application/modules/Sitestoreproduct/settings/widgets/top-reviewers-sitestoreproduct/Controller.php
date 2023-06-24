<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_TopReviewersSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    //GET SETTINGS
    $params = array();
    $params['limit'] = $this->_getParam('itemCount', 3);
    $this->view->type = $params['type'] = $this->_getParam('type', 'user');

    $params['resource_type'] = 'sitestoreproduct_product';

    //GET RESULTS
    $this->view->reviewers = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct')->topReviewers($params);

    //DON'T RENDER IF NO DATA
    if (Count($this->view->reviewers) <= 0) {
      return $this->setNoRender();
    }
  }

}
