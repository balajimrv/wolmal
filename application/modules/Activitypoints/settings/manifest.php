<?php

return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'activitypoints',
    'version' => '4.1.2',
    'path' => 'application/modules/Activitypoints',
    'repository' => 'socialenginemods.net',
    'title' => 'Activity Points plugin',
    'description' => 'Activity Points plugin',
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
      'path' => 'application/modules/Activitypoints/settings/install.php',
      'class' => 'Activitypoints_Installer',
    ),
    'dependencies' =>
    array(
      array (
        'type'  => 'module',
        'name'  => 'semods',
        'minVersion'  => '4.0.1',
        'required' => true
      ),
    ),
    'directories' =>
    array (
      'application/modules/Activitypoints',
    ),
    'files' => array(
      'application/languages/en/activitypoints.csv',
    ),
  ),
  // Content -------------------------------------------------------------------
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onSemodsAddActivity',
      'resource' => 'Activitypoints_Plugin_Core',
    ),
    //SES Custom work
    array(
      'event' => 'addActivity',
      'resource' => 'Activitypoints_Plugin_Core',
    ),
    //SES Custom work
    array(
      'event' => 'onFriendsinviterRefer',
      'resource' => 'Activitypoints_Plugin_Core',
    ),
    array(
      'event' => 'onFriendsinviterStats',
      'resource' => 'Activitypoints_Plugin_Core',
    ),
    array(
      'event' => 'onAlbumCreateAfter',
      'resource' => 'Activitypoints_Plugin_Core',
    ),
    array(
      'event' => 'onCoreLikeCreateAfter',
      'resource' => 'Activitypoints_Plugin_Core',
    ),
    array(
      'event' => 'onForumTopicCreateAfter',
      'resource' => 'Activitypoints_Plugin_Core',
    ),
    array(
      'event' => 'onForumPostCreateAfter',
      'resource' => 'Activitypoints_Plugin_Core',
    ),
    array(
      'event' => 'onMessagesMessageCreateAfter',
      'resource' => 'Activitypoints_Plugin_Core',
    ),
    array(
      'event' => 'onMusicSongCreateAfter',
      'resource' => 'Activitypoints_Plugin_Core',
    ),

    array(
      'event' => 'onActivityCommentCreateAfter',
      'resource' => 'Activitypoints_Plugin_Core',
    ),

    array(
      'event' => 'onFieldsValuesSave',
      'resource' => 'Activitypoints_Plugin_Core',
    ),


  ),
 // Routes --------------------------------------------------------------------
  'routes' => array(
    // Public
    // User
    'activitypoints_topusers' => array(
      'route' => '/topusers/*',
      'defaults' => array(
        'module' => 'activitypoints',
        'controller' => 'topusers',
        'action' => 'index'
      )
    ),
    //'activitypoints_topusers' => array(
    'topusers' => array(
      'route' => '/topusers/*',
      'defaults' => array(
        'module' => 'activitypoints',
        'controller' => 'topusers',
        'action' => 'index'
      )
    ),
    'activitypoints_vault' => array(
      'route' => '/points/vault/*',
      'defaults' => array(
        'module' => 'activitypoints',
        'controller' => 'index',
        'action' => 'index'
      )
    ),
    'activitypoints_transactions' => array(
      'route' => '/points/transactions/*',
      'defaults' => array(
        'module' => 'activitypoints',
        'controller' => 'index',
        'action' => 'transactions'
      )
    ),
    'activitypoints_earn' => array(
      'route' => '/points/earn/*',
      'defaults' => array(
        'module' => 'activitypoints',
        'controller' => 'index',
        'action' => 'earn'
      )
    ),
    'activitypoints_spend' => array(
      'route' => '/points/spend/*',
      'defaults' => array(
        'module' => 'activitypoints',
        'controller' => 'index',
        'action' => 'spend'
      )
    ),
    'activitypoints_help' => array(
      'route' => '/points/help/:action/*',
      'defaults' => array(
        'module' => 'activitypoints',
        'controller' => 'index',
        'action' => 'help'
      ),
                  'reqs' => array(
                'action' => '(help|business|collection|direct|bridges)',
            )
    ),

    'activitypoints_admin_main_levels' => array(
      'route' => 'admin/activitypoints/levels/:level_id',
      'defaults' => array(
        'module' => 'activitypoints',
        'controller' => 'admin-levels',
        'action' => 'index',
        'level_id' => 1
      )
    ),

  )
); ?>