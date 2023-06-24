<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "storevideos";
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
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.manifestUrl', "store-videos");
}
return array(
    // Package -------------------------------------------------------------------
    'package' => array(
        'type' => 'module',
        'name' => 'sitestorevideo',
        'version' => '-',
        'path' => 'application/modules/Sitestorevideo',
        'repository' => 'null',
        'title' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Videos Extension</span></i>',
        'description' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Videos Extension</span></i>',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thrusday, 05 May 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2012-2013 BigStep Technologies Pvt. Ltd.',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitestorevideo/settings/install.php',
            'class' => 'Sitestorevideo_Installer',
        ),
        'directories' => array(
            'application/modules/Sitestorevideo',
        ),
        'files' => array(
            'application/languages/en/sitestorevideo.csv',
        ),
    ),
    // hooks
    'hooks' => array(
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitestorevideo_Plugin_Core',
        )
    ),
    // Compose
    'composer' => array(
        'sitestorevideo' => array(
            'script' => array('_composeSitestoreVideo.tpl', 'sitestorevideo'),
            'plugin' => 'Sitestorevideo_Plugin_Composer',
            'auth' => array('sitestore_store', 'svcreate'),
        ),
    ),
    //'sitemobile_compatible' => true,
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitestorevideo_video'
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sitestorevideo_general' => array(
            'route' => $routeStart . '/:action/*',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'index',
                'action' => 'view',
            ),
            'reqs' => array(
                'action' => '(index|create)',
            )
        ),
        'sitestorevideo_create' => array(
            'route' => $routeStart . '/create/:store_id/*',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'index',
                'action' => 'create'
            ),
            'reqs' => array(
                'store_id' => '\d+'
            )
        ),
        'sitestorevideo_edit' => array(
            'route' => $routeStart . '/edit/:video_id/*',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'index',
                'action' => 'edit'
            )
        ),
        'sitestorevideoadmin_delete' => array(
            'route' => $routeStart . '/admin/delete/:video_id/:store_id/*',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'admin-manage',
                'action' => 'delete'
            ),
            'reqs' => array(
                'video_id' => '\d+',
                'store_id' => '\d+'
            )
        ),
        'sitestorevideo_delete' => array(
            'route' => $routeStart . '/delete/:video_id/:store_id/*',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'index',
                'action' => 'delete'
            ),
            'reqs' => array(
                'video_id' => '\d+',
                'store_id' => '\d+'
            )
        ),
        'sitestorevideo_view' => array(
            'route' => $routeStart . '/:user_id/:video_id/:slug/*',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'sitestorevideo_featured' => array(
            'route' => $routeStart . '/featured/:video_id/*',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'index',
                'action' => 'featured'
            ),
            'reqs' => array(
                'video_id' => '\d+'
            )
        ),
        'sitestorevideo_highlighted' => array(
            'route' => $routeStart . '/highlighted/:video_id/*',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'index',
                'action' => 'highlighted'
            ),
            'reqs' => array(
                'video_id' => '\d+'
            )
        ), 

         'sitestorevideo_browse' => array(
            'route' => $routeStart.'/browse/*',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'index',
                'action' => 'browse',
            ),
        ),

         'sitestorevideo_home' => array(
            'route' => $routeStart.'/home/*',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'index',
                'action' => 'home',
            ),
        ), 

        'sitestorevideo_featuredvideo' => array(
            'route' => $routeStart .'/admin/featuredvideo/:id/*',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'admin-manage',
                'action' => 'featuredvideo',
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),
        'sitestorevideo_highlightedvideo' => array(
            'route' => $routeStart .'/admin/highlightedvideo/:id/*',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'admin-manage',
                'action' => 'highlightedvideo',
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),

         'sitestorevideo_tags' => array(
            'route' => $routeStart . '/tagscloud/:store/',
            'defaults' => array(
                'module' => 'sitestorevideo',
                'controller' => 'index',
                'action' => 'tags-cloud',
                'store' => 1
            )
        ),
    )
)
?>
