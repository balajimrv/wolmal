<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "storeinvites";
$module=null;$controller=null;$action=null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) { 
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreinvite.manifestUrl', "store-invites");
}
return array(
    // Package -------------------------------------------------------------------
    'package' => array(
        'type' => 'module',
        'name' => 'sitestoreinvite',
        'version' => '-',
        'path' => 'application/modules/Sitestoreinvite',
        'repository' => 'null',
        'title' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Inviter Extension</span></i>',
        'description' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Inviter Extension</span></i>',
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
            'path' => 'application/modules/Sitestoreinvite/settings/install.php',
            'class' => 'Sitestoreinvite_Installer',
        ),
        'directories' => array(
            'application/modules/Sitestoreinvite',
        ),
        'files' => array(
            'application/languages/en/sitestoreinvite.csv',
        ),
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sitestoreinvite_invite' => array(
            'route' => $routeStart.'/invitefriends/:user_id/:sitestore_id/',
            'defaults' => array(
                'module' => 'sitestoreinvite',
                'controller' => 'index',
                'action' => 'friendsstoreinvite'
            ),
            'reqs' => array(
                'user_id' => '\d+',
                'sitestore_id' => '\d+'
            )
        ),
        'sitestoreinvite_invitefriends' => array(
            'route' => $routeStart.'/inviteusers/:user_id/:sitestore_id/',
            'defaults' => array(
                'module' => 'sitestoreinvite',
                'controller' => 'index',
                'action' => 'inviteusers'
            ),
            'reqs' => array(
                'user_id' => '\d+',
                'sitestore_id' => '\d+'
            )
        ),
        'sitestoreinvite_app_config' => array(
            'route' => 'admin/sitestoreinvite/global/appconfigs',
            'defaults' => array(
                'module' => 'sitestoreinvite',
                'controller' => 'admin-global',
                'action' => 'appconfigs'
            )
        ),
        'sitestoreinvite_global_global' => array(
            'route' => 'admin/sitestoreinvite/global/global',
            'defaults' => array(
                'module' => 'sitestoreinvite',
                'controller' => 'admin-global',
                'action' => 'global'
            )
        )
    )
);
?>