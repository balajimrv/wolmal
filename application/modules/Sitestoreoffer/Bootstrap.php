<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  protected function _initFrontController() {
  }
  
  public function __construct($application) {
		parent::__construct($application);
    $this->initViewHelperPath();
  }
}
?>