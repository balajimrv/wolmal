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
class Sitestoreoffer_Widget_SitestoreHotofferController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $getPackageOffer = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestoreoffer');

    //NUMBER OF OFFERS IN LISTING
    $totalOffers = $this->_getParam('itemCount', 3);
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $sitestoreoffer_hotoffer = Zend_Registry::isRegistered('sitestoreoffer_hotoffer') ? Zend_Registry::get('sitestoreoffer_hotoffer') : null;

    $this->view->statistics = $this->_getParam('statistics', array("enddate", "couponcode", 'discount','expire'));

    $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);
    //GET OFFER DATAS
    $offerType = 'hot';
    $category_id = $this->_getParam('category_id', 0);
    $this->view->recentlyview = $row = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer')->getWidgetOffers($totalOffers, $offerType, $category_id);
    $this->view->hotOffer = 1;

    $enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);

    if (!empty($enable_public_private)) {
      return $this->setNoRender();
    }
    if (( Count($row) <= 0 ) || empty($sitestoreoffer_hotoffer) || empty($getPackageOffer)) {
      return $this->setNoRender();
    }
  }

}

?>