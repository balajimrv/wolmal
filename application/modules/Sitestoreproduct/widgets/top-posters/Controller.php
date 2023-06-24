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
class Sitestoreproduct_Widget_TopPostersController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    //GET SETTINGS
    $params = array();
    $params['limit'] = $this->_getParam('itemCount', 3);
    $params['interval'] = $interval = $this->_getParam('interval', 'overall');
    
    $this->view->popularity = $popularity = $this->_getParam('popularity', 'top_poster');

    //GET RESULTS
    if( $popularity == 'top_poster' )
    {
      $this->view->posters = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->topPosters($params);
    }
    else if( $popularity == 'top_buyer' )
    {
      $this->view->listing_based_on = $params['listing_based_on'] = $this->_getParam('listing_based_on', 'price');
      $this->view->posters = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->getTopBuyers($params);
    }

    //DON'T RENDER IF NO DATA
    if (Count($this->view->posters) <= 0) {
      return $this->setNoRender();
    }
  }

}
