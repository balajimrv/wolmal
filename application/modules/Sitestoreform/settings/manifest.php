<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "storeform";
$module=null;$controller=null;$action=null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) { 
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreform.manifestUrl', "store-form");
}
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitestoreform',
        'version' => '-',
        'path' => 'application/modules/Sitestoreform',
        'title' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Form Extension</span></i>',
        'description' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Form Extension</span></i>',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thrusday, 05 May 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2012-2013 BigStep Technologies Pvt. Ltd.',
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitestoreform/settings/install.php',
            'class' => 'Sitestoreform_Installer',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Sitestoreform',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitestoreform.csv',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitestoreform',
    ),
    //'sitemobile_compatible' => true,
// Route--------------------------------------------------------------------
    'routes' => array(
        'sitestoreform_general' => array(
            'route' => $routeStart.'/:action/*',
            'defaults' => array(
                'module' => 'sitestoreform',
                'controller' => 'siteform',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(home|index|manage|create|packages)',
            ),
        ),
        'sitestoreform_disable' => array(
            'route' => 'storeform/admin-manage/disableform/:id/*',
            'defaults' => array(
                'module' => 'sitestoreform',
                'controller' => 'admin-manage',
                'action' => 'disable-form'
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),
    ),
);
?>