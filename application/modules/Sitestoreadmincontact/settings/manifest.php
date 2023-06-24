<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreadmincontact
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitestoreadmincontact',
        'version' => '-',
        'path' => 'application/modules/Sitestoreadmincontact',
        'title' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Contact Store Owners Extension</span></i>',
        'description' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Contact Store Owners Extension</span></i>',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' => array(
            'path' => 'application/modules/Sitestoreadmincontact/settings/install.php',
            'class' => 'Sitestoreadmincontact_Installer',
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
            0 => 'application/modules/Sitestoreadmincontact',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitestoreadmincontact.csv',
        ),
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sitestoreadmincontact_messages_general' => array(
            'route' => 'sitestoreadmincontact/:action/*',
            'defaults' => array(
                'module' => 'sitestoreadmincontact',
                'controller' => 'index',
                'action' => '(index)',
            ),
            'reqs' => array(
                'action' => '\D+',
            )
        ),
    )
);
?>