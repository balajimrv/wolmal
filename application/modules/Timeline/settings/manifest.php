<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'timeline',
    'version' => '4.2.5p9',
    'path' => 'application/modules/Timeline',
    'title' => 'Timeline',
    'description' => '',
    'author' => 'WebHive Team',
    'actions' => array (
                      0 => 'install',
                      1 => 'upgrade',
                      2 => 'refresh',
                      3 => 'enable',
                      4 => 'disable',
                    ),
    'dependencies' => array(
                array(
                    'type' => 'module',
                    'name' => 'core',
                    'minVersion' => '4.2.2',
                ),
            ),  
    'callback' => array (
                          'path' => 'application/modules/Timeline/settings/install.php',
                          'class' => 'Timeline_Installer'
                        ),  
    'directories' => 
    array (
      0 => 'application/modules/Timeline',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/timeline.csv',
      1 => 'whshow_thumb_timeline.php'
    ),
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'timeline_admin_manage_level' => array(
      'route' => 'admin/timeline/level/:level_id',
      'defaults' => array(
        'module' => 'timeline',
        'controller' => 'admin-level',
        'action' => 'index',
        'level_id' => 1
      )
    )
  )
); ?>