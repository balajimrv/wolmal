<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: common_style_css.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
// 	if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup'))) {
// 		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_store_group.css');
// 	}
// 	elseif(Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
// 		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_store.css');
// 	} 
// 	elseif(Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup')) {
// 		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/common_style_store_group.css');
// 
// 	}
//   else {
		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/style_sitestore.css');
  //}
?>