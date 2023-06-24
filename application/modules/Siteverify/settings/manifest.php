<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'siteverify',
        'version' => '4.9.0',
        'path' => 'application/modules/Siteverify',
        'title' => 'Members Verification Plugin',
        'description' => 'Members Verification Plugin',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'copyright' => 'Copyright 2015-2016 BigStep Technologies Pvt. Ltd.',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Siteverify/settings/install.php',
            'class' => 'Siteverify_Installer',
        ),
        'directories' => array(
            'application/modules/Siteverify',
        ),
        'files' => array(
            'application/languages/en/siteverify.csv',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'siteverify_verify',
    ),
     // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserDeleteAfter',
      'resource' => 'Siteverify_Plugin_Core'
    )
  ),
);
?>