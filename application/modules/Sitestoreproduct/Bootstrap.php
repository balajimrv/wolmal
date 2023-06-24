<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  public function __construct($application) {
    parent::__construct($application);
  }
  

  protected function _initFrontController() {
    $this->initActionHelperPath();
    $this->initViewHelperPath();
    
    // Calling Route Shutdown Method.
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Sitestoreproduct_Plugin_Core);
    $headScript = new Zend_View_Helper_HeadScript();
    Zend_Controller_Action_HelperBroker::addHelper(new Sitestoreproduct_Controller_Action_Helper_SitestoreproductHelpers()); 
  }

}
