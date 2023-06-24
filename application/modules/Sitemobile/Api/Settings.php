<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Settings.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Api_Settings extends Core_Api_Abstract {

  public function getSetting($name, $params = array()) {
 
    $enable_type = '';
    if (Engine_Api::_()->sitemobile()->isApp()) {
      $enable_type .= 'app';
    }

    if (Engine_Api::_()->sitemobile()->checkMode('tablet-mode')) {
      $enable_type .= 'tablet';
    } else {
      $enable_type .= 'mobile';
    }
		$name .= '.' . $enable_type;
		$coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
    $dafaultValue = null; 
    if(isset($params['dafaultValues']) && isset($params['dafaultValues'][$enable_type])) {
			$dafaultValue = $params['dafaultValues'][$enable_type];
    }

		if(isset($params['dafaultValue'])) {
			$dafaultValue = $params['dafaultValue'];
    }

		return $coreSettingsApi->getSetting($name, $dafaultValue);

  }

}

?>