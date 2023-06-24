<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: content.php 9832 2012-11-28 01:31:18Z richard $
 * @author     John
 */
return array(
  array(
    'title' => 'Profile Forum Posts',
    'description' => 'Displays a member\'s forum posts on their profile.',
    'category' => 'Forum',
    'type' => 'widget',
    'name' => 'sitemobile.profile-forum-posts',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Forum Posts',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
) ?>