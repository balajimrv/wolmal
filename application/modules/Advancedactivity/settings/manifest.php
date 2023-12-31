<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'advancedactivity',
        'version' => '4.8.12p5',
        'path' => 'application/modules/Advancedactivity',
        'title' => 'Advanced Activity Feeds / Wall Plugin',
        'description' => 'Advanced Activity Feeds / Wall Plugin',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'callback' => array(
            'path' => 'application/modules/Advancedactivity/settings/install.php',
            'class' => 'Advancedactivity_Installer',
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
            0 => 'application/modules/Advancedactivity',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/advancedactivity.csv',
        ),
    ),
    // Compose -------------------------------------------------------------------
    'compose' => array(
        array('_composeFacebook.tpl', 'advancedactivity'),
        array('_composeTwitter.tpl', 'advancedactivity'),
    //   array('_composeSocialengine.tpl', 'advancedactivity'),
    ),
    'composer' => array(
        'advanced_facebook' => array(
            'script' => array('_composeFacebook.tpl', 'advancedactivity'),
        ),
        'advanced_twitter' => array(
            'script' => array('_composeTwitter.tpl', 'advancedactivity'),
        ),
        'advanced_linkedin' => array(
            'script' => array('_composeLinkedin.tpl', 'advancedactivity'),
        ),
//        'advanced_socialengine' => array(
//            'script' => array('_composeSocialengine.tpl', 'advancedactivity'),
//        ),
        'tag' => array(
            'script' => array('_composeTag.tpl', 'advancedactivity'),
            'plugin' => 'Advancedactivity_Plugin_Composer_Tag',
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'addActivity',
            'resource' => 'Advancedactivity_Plugin_Core',
        ),
        array(
            'event' => 'getActivity',
            'resource' => 'Advancedactivity_Plugin_Core',
        ),
        array(
            'event' => 'onItemDeleteBefore',
            'resource' => 'Advancedactivity_Plugin_Core',
        ),
        array(
            'event' => 'onUserCreateAfter',
            'resource' => 'Advancedactivity_Plugin_Core',
        ),
        array(
            'event' => 'onAlbumPhotoUpdateAfter',
            'resource' => 'Advancedactivity_Plugin_Core',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'advancedactivity_content',
        'advancedactivity_customtype',
        'advancedactivity_list',
        'advancedactivity_report',
        'advancedactivity_list_item',
        'advancedactivity_customblock',
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'advancedactivity_extended' => array(
            'route' => 'advancedactivitys/:controller/:action/*',
            'defaults' => array(
                'module' => 'advancedactivity',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'controller' => '\D+',
                'action' => '\D+',
            )
        ),
    )
);
?>
