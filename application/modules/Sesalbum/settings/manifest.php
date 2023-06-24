<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manifest.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sesalbum',
        'version' => '4.9.0',
        'path' => 'application/modules/Sesalbum',
        'title' => 'SES - Advanced Photos & Albums Plugin',
        'description' => 'SES - Advanced Photos & Albums Plugin',
        'author' => '<a href="http://www.socialenginesolutions.com" style="text-decoration:underline;" target="_blank">SocialEngineSolutions</a>',
        'callback' => array(
            'path' => 'application/modules/Sesalbum/settings/install.php',
            'class' => 'Sesalbum_Installer',
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
            0 => 'application/modules/Sesalbum',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sesalbum.csv',
        ),
    ),
    // Compose -------------------------------------------------------------------
    'composer' => array(
      'photo' => array(
        'script' => array('_composePhoto.tpl', 'sesalbum'),
        'plugin' => 'Sesalbum_Plugin_Composer',
        'auth' => array('album', 'create'),
      ),
      'album' => array(
        'script' => array('_composeAlbum.tpl', 'sesalbum'),
        'plugin' => 'Sesalbum_Plugin_Composer',
        'auth' => array('album', 'create'),
      ),
    ),
    // Items --------------------------------------------------------------------
    'items' => array(
        'sesalbum_offthedays',
        'album',
        'sesalbum_category',
				'album_category',
        'album_photo',
        'photo',
				'sesalbum_photo',
				'sesalbum_album',
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Sesalbum_Plugin_Core'
        ),
        array(
            'event' => 'onUserProfilePhotoUpload',
            'resource' => 'Sesalbum_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteAfter',
            'resource' => 'Sesalbum_Plugin_Core'
        ),
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sesalbum_Plugin_Core'
        ),
				array(
            'event' => 'onRenderLayoutDefaultSimple',
            'resource' => 'Sesalbum_Plugin_Core'
        ),
				array(
            'event' => 'onRenderLayoutMobileDefault',
            'resource' => 'Sesalbum_Plugin_Core'
        ),
				array(
            'event' => 'onRenderLayoutMobileDefaultSimple',
            'resource' => 'Sesalbum_Plugin_Core'
        )
				
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sesalbum_extended' => array(
            'route' => 'albums/:controller/:action/*',
            'defaults' => array(
                'module' => 'sesalbum',
                'controller' => 'index',
                'action' => 'index'
            ),
        ),
				'sesalbum_specific_album' => array(
            'route' =>  'albums/:slug/:album_id',
            'defaults' => array(
                'module' => 'sesalbum',
                'controller' => 'album',
                'action' => 'view',
								'slug' =>''
            ),
            'reqs' => array(
							'album_id' => '\d+'
						)
        ),
				 'sesalbum_specific' => array(
            'route' => 'albums/:action/:album_id/*',
            'defaults' => array(
                'module' => 'sesalbum',
                'controller' => 'album',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(compose-upload|delete|edit|editphotos|upload|order|related-album)',
            ),
        ),
				'sesalbum_general' => array(
            'route' => 'albums/:action/*',
            'defaults' => array(
                'module' => 'sesalbum',
                'controller' => 'index',
                'action' => 'welcome',
            ),
            'reqs' => array(
                'action' => '(home|browse|create|list|manage|upload|upload-photo|like|download|photo-home|browse-photo|share|tags|existing-photos)',
            ),
        ),
        'sesalbum_photo_specific' => array(
            'route' => 'albums/photos/:action/:album_id/:photo_id/*',
            'defaults' => array(
                'module' => 'sesalbum',
                'controller' => 'photo',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(view|rotate|crop|flip|location|all-photos|last-element-data)',
            ),
        ),				
        'sesalbum_category_view' => array(
            'route' => 'albums/category/:category_id/*',
            'defaults' => array(
                'module' => 'sesalbum',
                'controller' => 'category',
                'action' => 'index',
            )
        ),
        'sesalbum_category' => array(
            'route' => 'albums/categories/:action/*',
            'defaults' => array(
                'module' => 'sesalbum',
                'controller' => 'category',
                'action' => 'browse',
            ),
            'reqs' => array(
                'action' => '(index|browse)',
            )
        ),
    ),
);
?>