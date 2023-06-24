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
class Sitestore_Widget_RandomSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params = array();
    $params['totalstores'] = $this->_getParam('itemCount', 3);
    $params['category_id'] = $this->_getParam('category_id', 0);
    $params['featured'] = $this->_getParam('featured', 0);
    $params['sponsored'] = $this->_getParam('sponsored', 0);
    
    $this->view->sitestores = Engine_Api::_()->sitestore()->getLising('Random List',$params);
    $sitestore_random = Zend_Registry::isRegistered('sitestore_random') ? Zend_Registry::get('sitestore_random') : null;

    if ( !(count($this->view->sitestores) > 0) || empty($sitestore_random) ) {
      return $this->setNoRender();
    }
  }
}
?>