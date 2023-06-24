<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "storealbums";
$module = null;
$controller = null;
$action = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.manifestUrl', "store-albums");
}
return array(
    'package' =>
    array(
        'type' => 'Module',
        'name' => 'sitestorealbum',
        'version' => '-',
        'path' => 'application/modules/Sitestorealbum',
        'title' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Photo Albums Extension</span><i>',
        'description' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Photo Albums Extension</span></i>',
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
        'callback' =>
        array(
            'path' => 'application/modules/Sitestorealbum/settings/install.php',
            'class' => 'Sitestorealbum_Installer',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Sitestorealbum',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitestorealbum.csv',
        ),
    ),
    //'sitemobile_compatible' => true,
    // Compose -------------------------------------------------------------------
    'composer' => array(
        'sitestorephoto' => array(
            'script' => array('_composeSitestorePhoto.tpl', 'sitestorealbum'),
            'plugin' => 'Sitestorealbum_Plugin_Composer',
            'auth' => array('sitestore_store', 'spcreate'),
        ),
    ),
    'routes' => array(
        'sitestorealbumadmin_delete' => array(
            'route' => $routeStart.'/admin/delete/:id/*',
            'defaults' => array(
                'module' => 'sitestorealbum',
                'controller' => 'admin-manage',
                'action' => 'delete'
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),

        'sitestorealbum_featuredalbum' => array(
            'route' => $routeStart.'/admin/featured/:id/*',
            'defaults' => array(
                'module' => 'sitestorealbum',
                'controller' => 'admin-manage',
                'action' => 'featured'
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),

        'sitestorealbum_extended' => array(
            'route' => $routeStart.'/:controller/:action/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'photo',
                'action' => 'index'
            ),
        ),

         'sitestorealbum_browse' => array(
            'route' => $routeStart.'/browse/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'album',
                'action' => 'browse',
            ),
        ),

        'sitestorealbum_home' => array(
           'route' => $routeStart.'/home/*',
            'defaults' => array(
                'module' => 'sitestore',
                'controller' => 'album',
                'action' => 'home',
            ),
        ),
    ),

);
?>