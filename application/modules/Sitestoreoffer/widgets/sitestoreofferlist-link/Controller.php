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
class Sitestoreoffer_Widget_SitestoreofferlistLinkController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $enable_public_private = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
     $this->view->statistics = $this->_getParam('statistics', array("startdate", "enddate", "couponcode", 'discount', 'claim' ,'expire'));
     
     $this->view->enable_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.coupon.url', 0);
    
    if(!empty($enable_public_private)){
      return $this->setNoRender();
    }

  }
}

?>