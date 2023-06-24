<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    SlipStream
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 10267 2014-06-10 00:55:28Z lucas $
 * @author     Bryan
 */

return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'slipstream',
    'version' => '4.9.1',
    'revision' => '$Revision: 10267 $',
    'path' => 'application/themes/slipstream',
    'repository' => 'socialengine.com',
    'title' => 'Slipstream',
    'thumb' => 'slipstream.jpg',
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
      'application/themes/slipstream',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
    'mobile.css',
  ),
  'nophoto' => array(
    'user' => array(
      'thumb_icon' => 'application/themes/slipstream/images/nophoto_user_thumb_icon.png',
      'thumb_profile' => 'application/themes/slipstream/images/nophoto_user_thumb_profile.png',
    ),
    'group' => array(
      'thumb_normal' => 'application/themes/slipstream/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/themes/slipstream/images/nophoto_event_thumb_profile.jpg',
    ),
    'event' => array(
      'thumb_normal' => 'application/themes/slipstream/images/nophoto_event_thumb_normal.jpg',
      'thumb_profile' => 'application/themes/slipstream/images/nophoto_event_thumb_profile.jpg',
    ),
  ),
) ?>
