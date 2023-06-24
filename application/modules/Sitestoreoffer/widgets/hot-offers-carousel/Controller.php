<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreoffer_Widget_HotOffersCarouselController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    //SEARCH PARAMETER
    $params = array();
    $hotOffer = 1;
    $params['offertype'] = 'hotoffer';
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->category_id = $params['category_id'] = $this->_getParam('category_id',0);
    $this->view->hotOffers = $hotOffers = Engine_Api::_()->getDbTable('offers', 'sitestoreoffer')->getOffers($hotOffer,$params);
    
     $this->view->statistics = $this->_getParam('statistics', array("startdate", "enddate", "couponcode", 'discount', 'claim' ,'expire'));
     $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);
     
    $enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
    
    if(!empty($enable_public_private)){
      return $this->setNoRender();
    }
    
    $this->view->totalCount_offer = count($hotOffers);
    if (!($this->view->totalCount_offer > 0)) {
      return $this->setNoRender();
    }

    $this->view->inOneRow_offer = $inOneRow = $this->_getParam('inOneRow', 3);
    $this->view->noOfRow_offer = $noOfRow = $this->_getParam('noOfRow', 2);
    $this->view->totalItemShowoffer = $totalItemShow = $inOneRow * $noOfRow;
    $params['limit'] = $totalItemShow;
    // List List hot
    $this->view->hotOffers = $this->view->hotOffers = $hotOffers = Engine_Api::_()->getDbTable('offers', 'sitestoreoffer')->getOffers($hotOffer,$params);

    // CAROUSEL SETTINGS  
    $this->view->interval = $interval = $this->_getParam('interval', 250);
    $this->view->count = $count = $hotOffers->count();
    $this->view->heightRow = @ceil($count / $inOneRow);
    $this->view->vertical = $this->_getParam('vertical', 0);
  }

}