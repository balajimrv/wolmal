<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "storecoupon";
$module=null;$controller=null;$action=null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) { 
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.manifestUrl', "store-coupons");
}
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitestoreoffer',
        'version' => '-',
        'path' => 'application/modules/Sitestoreoffer',
        'title' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Offers Extension</span></i>',
        'description' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Offers Extension</span></i>',
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
            'path' => 'application/modules/Sitestoreoffer/settings/install.php',
            'class' => 'Sitestoreoffer_Installer',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Sitestoreoffer',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitestoreoffer.csv',
        ),
    ),
    'hooks' => array(
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitestoreoffer_Plugin_Core',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitestoreoffer_offer',
        'sitestoreoffer_photo',
        'sitestoreoffer_claim',
        'sitestoreoffer_album'
    ),
    //'sitemobile_compatible' => true,
    // Route--------------------------------------------------------------------
    'routes' => array(
        'sitestoreoffer_general' => array(
            'route' => $routeStart.'/:action/*',
            'defaults' => array(
                'module' => 'sitestoreoffer',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|create|edit|delete|sticky|print|getoffer|resendoffer|coupon-url-validation|enable-disable)',
            ),
        ),
        'sitestoreoffer_hotoffer' => array(
            'route' => $routeStart.'/hotcoupon/:id/*',
            'defaults' => array(
                'module' => 'sitestoreoffer',
                'controller' => 'admin',
                'action' => 'hotoffer',
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),
        'sitestoreoffer_view' => array(
            'route' => $routeStart . '/:user_id/:offer_id/:slug/*',
            'defaults' => array(
                'module' => 'sitestoreoffer',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'sitestoreoffer_details' => array(
            'route' => $routeStart.'/detail/:id/*',
            'defaults' => array(
                'module' => 'sitestoreoffer',
                'controller' => 'admin',
                'action' => 'detail',
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),
        'sitestoreoffer_home' => array(
            'route' => $routeStart.'/home/*',
            'defaults' => array(
                'module' => 'sitestoreoffer',
                'controller' => 'index',
                'action' => 'home',
            ),
        ),
        'sitestoreoffer_delete' => array(
            'route' => $routeStart.'/delete/:id/*',
            'defaults' => array(
                'module' => 'sitestoreoffer',
                'controller' => 'admin',
                'action' => 'delete',
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),
        'sitestoreoffer_approved' => array(
            'route' => $routeStart.'/approved/:id/*',
            'defaults' => array(
                'module' => 'sitestoreoffer',
                'controller' => 'admin',
                'action' => 'approved',
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),
        'sitestoreoffer_browse' => array(
            'route' => $routeStart.'/browse/*',
            'defaults' => array(
                'module' => 'sitestoreoffer',
                'controller' => 'index',
                'action' => 'browse',
            ),
        ),
    ),
);
?>