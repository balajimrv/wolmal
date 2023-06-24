<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Default
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 9747 2012-07-26 02:08:08Z john $
 * @author     Alex
 */
return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'Luminous',
    'version' => '4.8.6p6',
    'path' => 'application/themes/sitemobile_tablet/luminous',
    'repository' => 'socialengineaddOns.com',
    'title' => 'Luminous Theme',
    'thumb' => 'thumb.jpg',
    'author' => 'SocialEngineAddons',
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
      'application/themes/sitemobile_tablet/luminous',
    ),
  ),
  'files' => array(
    'theme.css',
     'structure.css', 
    
  ),
) ?>
