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
class Sitestore_Widget_SponsoredSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params = array();

    $this->view->limit = $params['limit'] = $this->_getParam('itemCount', 4);
    $this->view->category_id = $params['category_id'] = $this->_getParam('category_id', 0);
    $this->view->interval = $this->_getParam('interval', 300);
    $this->view->titletruncation = $this->_getParam('truncation', 18);  
    //GET SPONSERED STORES
    $totalSitestore = Engine_Api::_()->sitestore()->getLising('Total Sponsored Sitestore',$params);
    $sitestore_sponcerd = Zend_Registry::isRegistered('sitestore_sponcerd') ? Zend_Registry::get('sitestore_sponcerd') : null;

    //NO RENDER IF SPONSERED STORES ARE ZERO
    $this->view->totalCount = $totalSitestore->count();
    if ( !($this->view->totalCount > 0) ) {
      return $this->setNoRender();
    }

    //SEND STORE DATA TO TPL
    $this->view->sitestores = $sitestores = Engine_Api::_()->sitestore()->getLising('Sponsored Sitestore',$params);

    $this->view->count = $sitestores->count();
    if ( empty($sitestore_sponcerd) ) {
      return $this->setNoRender();
    }
  }

}
?>