<?php

return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'semods',
    'version' => '4.0.4',
    'path' => 'application/modules/Semods',
    'repository' => 'socialenginemods.net',
    'title' => 'SocialEngineMods Library',
    'description' => 'SocialEngineMods Library',
    'author' => 'SocialEngineMods',
    'actions' =>
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      //3 => 'remove',
    ),
    'callback' => array(
      'path' => 'application/modules/Semods/settings/install.php',
      'class' => 'Semods_Installer',
    ),
    'directories' =>
    array (
      0 => 'application/modules/Semods',
    ),
  ),
  'hooks' => array(
    array(
      'event' => 'getAdminNotifications',
      'resource' => 'Semods_Plugin_Core',
    )
  ),
); ?>