<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Kandy Theme
 * @copyright  Copyright 2006-2012 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 9714 2012-05-07 23:17:50
 * @author     
 */

return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'grid-blue',
    'version' => '4.9.1',
    'revision' => '$Revision: 10113 $',
    'path' => 'application/themes/grid-blue',
    'repository' => 'socialengine.com',
    'title' => 'Grid Blue',
    'thumb' => 'grid_theme.png',
    'author' => 'Webligo Developments',
    'actions' => array(
      'install',
      'upgrade',
      'refresh',
      'remove',
    ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => array(
      'application/themes/grid-blue',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
    'mobile.css',
  ),
  'colorVariants' => array(
    'grid-brown' => array(
      'title' => 'Grid Brown',
    ),
    'grid-dark' => array(
      'title' => 'Grid Dark',
    ),
    'grid-gray' => array(
      'title' => 'Grid Gray',
    ),
    'grid-green' => array(
      'title' => 'Grid Green',
    ),
    'grid-pink' => array(
      'title' => 'Grid Pink',
    ),
    'grid-purple' => array(
      'title' => 'Grid Purple',
    ),
    'grid-red' => array(
      'title' => 'Grid Red',
    ),
  ),
) ?>
