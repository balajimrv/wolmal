<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adintegration.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$session = new Zend_Session_Namespace();
if (!empty($session->show_hide_ads)) {
  if ($session->store_communityad_integration == 1)
    $communityad_integration = $store_communityad_integration = $session->store_communityad_integration;
  else
   $communityad_integration = $store_communityad_integration = 0;
}
else {
  $communityad_integration = $store_communityad_integration = 1;
}
?>
<?php

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>