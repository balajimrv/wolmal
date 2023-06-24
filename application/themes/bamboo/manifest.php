<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Bamboo
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 10113 2013-11-04 17:51:42Z andres $
 * @author     Bryan
 */

return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'bamboo',
    'version' => '4.9.1',
    'revision' => '$Revision: 10113 $',
    'path' => 'application/themes/bamboo',
    'repository' => 'socialengine.com',
    'title' => 'Bamboo',
    'thumb' => 'bamboo_theme.jpg',
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
      'application/themes/bamboo',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
    'mobile.css',
  )
) ?>
