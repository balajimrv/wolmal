<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2012-18-05 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreoffer_Widget_OffersSitestoreoffersController extends Engine_Content_Widget_Abstract {

  public function indexAction() {  	

		//GET WIDGET SETTINGS
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$this->view->popularity = $popularity = $this->_getParam('popularity', 'view_count');
		$totalOffers = $this->_getParam('itemCount', 3);
		$category_id = $this->_getParam('category_id', 0);
		
   $this->view->statistics = $this->_getParam('statistics', array("enddate", "couponcode", 'discount','expire'));
    
    $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);
    
    $getPackageOffer = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestoreoffer');
    $offerType = 'alloffers';

		//GET STORE RESULTS
		$this->view->recentlyview = $row = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer')->getWidgetOffers($totalOffers, $offerType,$category_id,$popularity);
		
    $enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
    
    if(!empty($enable_public_private)){
      return $this->setNoRender();
    }
    //SET NO RENDER
    if ( ( Count($row) <= 0 ) || empty($getPackageOffer) ) {
      return $this->setNoRender();
    }
  }

}