<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Quantum Theme
 * @copyright  Copyright 2006-2012 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manifest.php 9714 2012-05-07 23:17:50
 * @author     
 */

return array (
  'package' => array (
    'type' => 'theme',
    'name' => 'quantum-blue',
    'version' => '4.9.1',
    'revision' => '$Revision: 10113 $',
    'path' => 'application/themes/quantum-blue',
    'repository' => 'socialengine.com',
    'title' => 'Quantum Blue',
    'thumb' => 'quantum_theme.png',
    'author' => 'Webligo Developments',
    'actions' => array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
	'callback' => array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => array (
      0 => 'application/themes/quantum-blue',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
    'mobile.css',
  ),
  'colorVariants' => array(
    'quantum-beige' => array(
      'title' => 'Quantum Beige',
    ),
    'quantum-gray' => array(
      'title' => 'Quantum Gray',
    ),
    'quantum-green' => array(
      'title' => 'Quantum Green',
    ),
    'quantum-orange' => array(
      'title' => 'Quantum Orange',
    ),
    'quantum-pink' => array(
      'title' => 'Quantum Pink',
    ),
    'quantum-purple' => array(
      'title' => 'Quantum Purple',
    ),
    'quantum-red' => array(
      'title' => 'Quantum Red',
    ),
  ),
); ?>
