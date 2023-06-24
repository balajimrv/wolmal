<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteverify_Bootstrap extends Engine_Application_Bootstrap_Abstract {
  protected function _initFrontController() {
    include APPLICATION_PATH . '/application/modules/Siteverify/controllers/license/license.php';
  }
}