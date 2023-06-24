<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Musicbox Theme
 * @copyright  Copyright 2006-2012 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 9714 2012-05-07 23:17:50
 * @author     
 */

return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'musicbox-blue',
    'version' => '4.9.1',
    'revision' => '$Revision: 10267 $',
    'path' => 'application/themes/musicbox-blue',
	  'repository' => 'socialengine.com',
    'title' => 'Musicbox Blue',
    'thumb' => 'musicbox.png',
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
      'application/themes/musicbox-blue',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
    'mobile.css',
  ),
  'nophoto' => array(
    'user' => array(
      'thumb_icon' => 'application/themes/musicbox-blue/images/nophoto_user_thumb_icon.png',
      'thumb_profile' => 'application/themes/musicbox-blue/images/nophoto_user_thumb_profile.png',
    ),
    'group' => array(
      'thumb_normal' => 'application/themes/musicbox-blue/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/themes/musicbox-blue/images/nophoto_event_thumb_profile.jpg',
    ),
    'event' => array(
      'thumb_normal' => 'application/themes/musicbox-blue/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/themes/musicbox-blue/images/nophoto_event_thumb_profile.jpg',
    ),
  ),
  'colorVariants' => array(
    'musicbox-brown' => array(
      'title' => 'Musicbox Brown',
    ),
    'musicbox-gray' => array(
      'title' => 'Musicbox Gray',
    ),
    'musicbox-green' => array(
      'title' => 'Musicbox Green',
    ),
   'musicbox-pink' => array(
      'title' => 'Musicbox Pink',
    ),
    'musicbox-purple' => array(
      'title' => 'Musicbox Purple',
    ),
    'musicbox-red' => array(
      'title' => 'Musicbox Red',
    ),
    'musicbox-yellow' => array(
      'title' => 'Musicbox Yellow',
    ),
  ),
) ?>
