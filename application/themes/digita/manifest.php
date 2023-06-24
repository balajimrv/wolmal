<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Digita
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 10267 2014-06-10 00:55:28Z lucas $
 * @author     Bryan
 */

return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'digita',
    'version' => '4.9.1',
    'revision' => '$Revision: 10267 $',
    'path' => 'application/themes/digita',
    'repository' => 'socialengine.com',
    'title' => 'Digita',
    'thumb' => 'digita.jpg',
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
      'application/themes/digita',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
    'mobile.css',
  ),
) ?>
