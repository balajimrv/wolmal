<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: content.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
return array(
  array(
    'title' => 'Profile Music',
    'description' => 'Displays a member\'s music on their profile.',
    'category' => 'Music',
    'type' => 'widget',
    'name' => 'sitemobile.music-profile-music',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Music',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
 
) ?>