<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Cleanup.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Plugin_Task_Cleanup extends Core_Plugin_Task_Abstract {

  public function execute() {

    Engine_Api::_()->sitestore()->updateExpiredStores();
  }

}

