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
class Sitestoreoffer_Widget_HotOffersSlideshowController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    
    //SEARCH PARAMETER
    $params = array();
    $hotOffer = 1;
    $params['offertype'] = 'hotoffer';
    $params['category_id'] = $this->_getParam('category_id',0);
    $params['limit'] = $this->_getParam('itemCountPerPage', 10);
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
   
    $this->view->statistics = $this->_getParam('statistics', array("startdate", "enddate", "couponcode", 'discount', 'claim' ,'expire'));
    
     $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);
    $enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
    
    if(!empty($enable_public_private)){
      return $this->setNoRender();
    }
    
    $this->view->show_slideshow_object = $this->view->hotOffers = $hotOffers = Engine_Api::_()->getDbTable('offers', 'sitestoreoffer')->getOffers($hotOffer,$params);

    // Count Hot Offers
    $this->view->num_of_slideshow = count($hotOffers);
    
    $enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
    
    if(!empty($enable_public_private)){
      return $this->setNoRender();
    }
    // Number of the result.
    if (empty($this->view->num_of_slideshow)) {
      return $this->setNoRender();
    }
  }

}
?>