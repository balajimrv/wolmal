<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "storereviews";
$module=null;$controller=null;$action=null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) { 
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.manifestUrl', "store-reviews");
}
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitestorereview',
        'version' => '-',
        'path' => 'application/modules/Sitestorereview',
        'title' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Reviews and Ratings Extension</span></i>',
        'description' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Reviews and Ratings Extension</span></i>',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thrusday, 05 May 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2012-2013 BigStep Technologies Pvt. Ltd.',
        'callback' => array(
            'path' => 'application/modules/Sitestorereview/settings/install.php',
            'class' => 'Sitestorereview_Installer',
        ),
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Sitestorereview',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitestorereview.csv',
        ),
    ),
    'hooks' => array(
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitestorereview_Plugin_Core',
        ),
    ),
    'items' => array(
        'sitestorereview_review',
        'sitestorereview_reviewcat'
    ),
   // 'sitemobile_compatible' => true,
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sitestorereview_create' => array(
            'route' => $routeStart.'/create/:store_id/*',
            'defaults' => array(
                'module' => 'sitestorereview',
                'controller' => 'index',
                'action' => 'create'
            ),
            'reqs' => array(
                'store_id' => '\d+'
            )
        ),
        'sitestorereview_detail_view' => array(
            'route' => $routeStart.'/:owner_id/:review_id/:slug/*',
            'defaults' => array(
                'module' => 'sitestorereview',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'owner_id' => '\d+',
                'review_id' => '\d+'
            )
        ),
        'sitestorereview_edit' => array(
            'route' => $routeStart.'/edit/:review_id/:store_id/*',
            'defaults' => array(
                'module' => 'sitestorereview',
                'controller' => 'index',
                'action' => 'edit'
            )
        ),
        'sitestorereview_delete' => array(
            'route' => $routeStart.'/delete/:review_id/:store_id/*',
            'defaults' => array(
                'module' => 'sitestorereview',
                'controller' => 'index',
                'action' => 'delete'
            ),
            'reqs' => array(
                'review_id' => '\d+',
                'store_id' => '\d+'
            )
        ),

         'sitestorereview_home' => array(
            'route' => $routeStart.'/home/*',
            'defaults' => array(
                'module' => 'sitestorereview',
                'controller' => 'index',
                'action' => 'home',
            ),
        ), 

        'sitestorereview_browse' => array(
            'route' => $routeStart.'/browse/*',
            'defaults' => array(
                'module' => 'sitestorereview',
                'controller' => 'index',
                'action' => 'browse',
            ),
        ),
    ),
);
?>