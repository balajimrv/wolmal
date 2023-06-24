<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manifest.php  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'sesadvancedactivity',
    'version' => '4.9.0',
    'path' => 'application/modules/Sesadvancedactivity',
    'title' => 'SES - Advanced News & Activity Feeds Plugin',
    'description' => 'SES - Advanced News & Activity Feeds Plugin',
     'author' => '<a href="http://www.socialenginesolutions.com" style="text-decoration:underline;" target="_blank">SocialEngineSolutions</a>',
    'callback' => array(
			'path' => 'application/modules/Sesadvancedactivity/settings/install.php',
			'class' => 'Sesadvancedactivity_Installer',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Sesadvancedactivity',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/sesadvancedactivity.csv',
      1 => 'public/admin/welcome-icon.png',
      2 => 'public/admin/feed.png'
    ),
  ),
  //Hooks
  'hooks' => array(
    array(
      'event' => 'getActivity',
      'resource' => 'Sesadvancedactivity_Plugin_Core',
    ),
    array(
      'event' => 'addActivity',
      'resource' => 'Sesadvancedactivity_Plugin_Core',
    ),
    array(
      'event' => 'onItemDeleteBefore',
      'resource' => 'Sesadvancedactivity_Plugin_Core',
    ),
    array(
            'event' => 'onRenderLayoutDefault',
        'resource' => 'Sesadvancedactivity_Plugin_Core'
    ),
    array(
        'event' => 'onRenderLayoutDefaultSimple',
        'resource' => 'Sesadvancedactivity_Plugin_Core'
    ),
    array(
        'event' => 'onRenderLayoutMobileDefault',
        'resource' => 'Sesadvancedactivity_Plugin_Core'
    ),
    array(
        'event' => 'onRenderLayoutMobileDefaultSimple',
        'resource' => 'Sesadvancedactivity_Plugin_Core'
    ),
    array(
        'event' => 'onUserLogoutAfter',
        'resource' => 'Sesadvancedactivity_Plugin_Core'
    )
  ),
  // Compose -------------------------------------------------------------------
  'composer' => array(
    'sesadvancedactivityfacebook' => array(
      'script' => array('_composeFacebook.tpl', 'sesadvancedactivity'),
    ),
    'sesadvancedactivitytwitter' => array(
      'script' => array('_composeTwitter.tpl', 'sesadvancedactivity'),
    ),
    'sesadvancedactivitylinkedin' => array(
      'script' => array('_composeLinkedin.tpl', 'sesadvancedactivity'),
    ),
    'sesadvancedactivitylink' => array(
      'script' => array('_composeLink.tpl', 'sesadvancedactivity'),
      'plugin' => 'Sesadvancedactivity_Plugin_LinkComposer',
      'auth' => array('core_link', 'create'),
    ),
     'sesadvancedactivitytargetpost' => array(
      'script' => array('_composetargetpost.tpl', 'sesadvancedactivity'),
    ),
    'fileupload' => array(
      'script' => array('_composefileupload.tpl', 'sesadvancedactivity'),
      'plugin' => 'Sesadvancedactivity_Plugin_FileuploadComposer',
    ),
    'buysell' => array(
      'script' => array('_composebuysell.tpl', 'sesadvancedactivity'),
      'plugin' => 'Sesadvancedactivity_Plugin_BuysellComposer',
    ),
    'sesadvancedactivityfacebookpostembed' => array(
      'script' => array('_composefacebookpostembed.tpl', 'sesadvancedactivity'),
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'sesadvancedactivity_file',
    'sesadvancedactivity_buysell',
    'sesadvancedactivity_action',
    'sesadvancedactivity_filterlist',
    'sesadvancedactivity_event', 'sesadvancedactivity_textcolor'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
     'sesadvancedactivity_extended' => array(
          'route' => 'sesadvancedactivity/ajax/welcome/*',
          'defaults' => array(
              'module' => 'sesadvancedactivity',
              'controller' => 'index',
              'action' => 'welcome',
          ),
          'reqs' => array(
              'controller' => '\D+',
              'action' => '\D+',
          )
      ),
      'sesadvancedactivity_onthisday'=>array(
      'route' => 'onthisday',
      'defaults' => array(
        'module' => 'sesadvancedactivity',
        'controller' => 'index',
        'action' => 'onthisday'
      ),
    ),
     'sesadvancedactivity_hastag' => array(
      'route' => 'hashtag',
      'defaults' => array(
        'module' => 'sesadvancedactivity',
        'controller' => 'index',
        'action' => 'hashtag'
      ),
    ),
  ),
); ?>