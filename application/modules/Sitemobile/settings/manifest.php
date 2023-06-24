<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    // Package -------------------------------------------------------------------
    'package' => array( 
        'type' => 'module',
        'name' => 'sitemobile',
        'version' => '4.8.12',
        'path' => 'application/modules/Sitemobile',
        'title' => 'Mobile / Tablet Plugin',
        'description' => 'Mobile / Tablet Plugin',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'core',
                'minVersion' => '4.1.5',
            ),
        ),
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitemobile/settings/install.php',
            'class' => 'Sitemobile_Installer',
        ),
        'directories' => array(
            'application/modules/Sitemobile',
            'application/themes/sitemobile_tablet'
        ),
        'files' => array(
            'application/languages/en/sitemobile.csv',
            'application/libraries/Zend/View/Abstract.php'
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
      array(
        'event' => 'onRenderLayoutMobileSMDefault',
        'resource' => 'Sitemobile_Plugin_Core',
      ),
      array(
        'event' => 'onItemDeleteBefore',
        'resource' => 'Sitemobile_Plugin_Storage',
      ),
      array(
        'event' => 'onStorageFileUpdateBefore',
        'resource' => 'Sitemobile_Plugin_Storage',
      ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitemobile_datahtmlattrib',
        'sitemobile_tablet_page'
    //'sitemobile_splashscreen'
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sitemobile_general' => array(
            'route' => 'mobile/:action/*',
            'defaults' => array(
                'module' => 'sitemobile',
                'controller' => 'browse',
                'action' => '(browse)',
            ),
            'reqs' => array(
                'action' => '\D+',
            )
        ),
        'sitemobile_dashboard' => array(
            'route' => 'dashboard/*',
            'defaults' => array(
                'module' => 'sitemobile',
                'controller' => 'browse',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(browse)',
            )
        ),
        'recent_request' => array(
            'route' => 'activity/notifications/requests',
            'defaults' => array(
                'module' => 'activity',
                'controller' => 'notifications',
                'showrequest' => 1
            )
        )
    )
        )
?>
