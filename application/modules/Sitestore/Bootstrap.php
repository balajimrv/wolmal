<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  protected function _initFrontController() {
    // Main Data
    $this->initActionHelperPath();
    include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license.php';
    
//     $headScript = new Zend_View_Helper_HeadScript();
//     $headScript->appendFile('application/modules/Sitestore/externals/scripts/core.js');

    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Sitestore_Plugin_Core);
  }

}
?>
