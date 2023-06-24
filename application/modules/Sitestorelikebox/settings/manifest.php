<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$routeStartP = "storeitems";
$routeStartS = "storeitem";
$module=null;$controller=null;$action=null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
  $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlP', "stores");
  $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manifestUrlS', "seller");
}
return array (
  'package' =>
  array (
    'type' => 'module' ,
    'name' => 'sitestorelikebox' ,
    'version' => '-' ,
    'path' => 'application/modules/Sitestorelikebox' ,
    'title' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Embeddable Badges, Like Box Extension</span></i>' ,
    'description' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Embeddable Badges, Like Box Extension</span></i>' ,
    'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
		'callback' => array(
				'path' => 'application/modules/Sitestorelikebox/settings/install.php',
				'class' => 'Sitestorelikebox_Installer',
		),
    'actions' =>
    array (
      0 => 'install' ,
      1 => 'upgrade' ,
      2 => 'refresh' ,
      3 => 'enable' ,
      4 => 'disable' ,
    ) ,
    'directories' =>
    array (
      0 => 'application/modules/Sitestorelikebox' ,
    ) ,
    'files' =>
    array (
      0 => 'application/languages/en/sitestorelikebox.csv' ,
    ) ,
  ) ,
  //Route--------------------------------------------------------------------
  'routes' => array (
    'sitestorelikebox_general' => array (
      'route' => $routeStartP.'/likebox/:action/*' ,
      'defaults' => array (
        'module' => 'sitestorelikebox' ,
        'controller' => 'index' ,
      ) ,
      'reqs' => array (
        'action' => '(index|like-box|get-like-code|has-login|login|like|unlike)' ,
      ) ,
    ) ,
  ) ,
) ;
?>