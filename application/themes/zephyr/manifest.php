<?php
/**
 * PixyThemes
 *
 * @category   Application_Theme
 * @package    Zephyr Theme
 * @copyright  PixyThemes
 * @license    http://www.pixythemes.com/
 * @author     Pixythemes
 */

return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'zephyr',
    'version' => '1.2.9',
    'path' => 'application/themes/zephyr',
    'repository' => 'pixythemes.com',
    'title' => 'Zephyr',
    'thumb' => 'theme.jpg',
    'author' => '<a href="http://pixythemes.com" target="_blank" title="Visit our website!">PixyThemes</a>',
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => 
    array (
      0 => 'application/themes/zephyr',
    ),
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
	2 => 'mobile.css',
	3 => 'media-queries.css',
  ),
); ?>