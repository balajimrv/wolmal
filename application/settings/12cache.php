<?php
defined('_ENGINE') or die('Access Denied');
return array (
  'default_backend' => 'File',
  'frontend' => 
  array (
    'core' => 
    array (
      'automatic_serialization' => true,
      'cache_id_prefix' => 'Engine4w9e_',
      'lifetime' => '120',
      'caching' => true,
      'gzip' => true,
    ),
  ),
  'backend' => 
  array (
    'Engine_Cache_Backend_Redis' => 
    array (
      'servers' => 
      array (
        0 => 
        array (
          'host' => '127.0.0.1',
          'port' => 6379,
        ),
      ),
    ),
  ),
  'default_file_path' => '/home/wffbnbcg/public_html/temporary/cache',
); ?>