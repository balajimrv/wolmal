<?php

return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'activityrewards',
    'version' => '4.0.1',
    'path' => 'application/modules/Activityrewards',
    'repository' => 'socialenginemods.net',
    'title' => 'Activity Rewards plugin',
    'description' => 'Activity Rewards plugin',
    'author' => 'SocialEngineMods',
    'actions' =>
    array (
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
    ),
    'callback' => array(
      'path' => 'application/modules/Activityrewards/settings/install.php',
      'class' => 'Activityrewards_Installer',
    ),
    'dependencies' =>
    array(
      array (
        'type'  => 'module',
        'name'  => 'activitypoints',
        'minVersion'  => '4.0.0',
        'required' => true
      ),
    ),
    'directories' =>
    array (
      'application/modules/Activityrewards',
    ),
    'files' => array(
      'application/languages/en/activityrewards.csv',
    ),
  ),
  // Content -------------------------------------------------------------------
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'activityrewards_earner',
    'activityrewards_spender',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    // Public
    // User
    'activityrewards_earn' => array(
      'route' => '/points/earn/*',
      'defaults' => array(
        'module' => 'activityrewards',
        'controller' => 'offers',
        'action' => 'index'
      )
    ),
    'activityrewards_offer_view' => array(
      'route' => 'points/earn/:item_id/:item_title',
      'defaults' => array(
        'module' => 'activityrewards',
        'controller' => 'offers',
        'action' => 'view'
      ),
      'reqs' => array(
        'item_id' => '\d+',
        //'item_title' => '\w+',
      )
    ),
    'activityrewards_spend' => array(
      'route' => '/points/shop/*',
      'defaults' => array(
        'module' => 'activityrewards',
        'controller' => 'shop',
        'action' => 'index'
      )
    ),
    'activityrewards_spend_view' => array(
      'route' => 'points/shop/:item_id/:item_title',
      'defaults' => array(
        'module' => 'activityrewards',
        'controller' => 'shop',
        'action' => 'view'
      ),
      'reqs' => array(
        'item_id' => '\d+',
        //'item_title' => '\w+',
      )
    ),
  )
); ?>