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
    'name' => 'kandy-mangoberry',
    'version' => '4.9.1',
    'revision' => '$Revision: 10267 $',
    'path' => 'application/themes/kandy-mangoberry',
    'repository' => 'socialengine.com',
    'title' => 'Kandy Mangoberry',
    'thumb' => 'thumb.png',
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
      'application/themes/kandy-mangoberry',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
    'mobile.css',
  ),
  'colorVariants' => array(
    'kandy-cappuccino' => array(
      'title' => 'Kandy Cappuccino',
    ),
    'kandy-limeorange' => array(
      'title' => 'Kandy Limeorange',
    ),
    'kandy-watermelon' => array(
      'title' => 'Kandy Watermelon',
    ),
  ),
) ?>
