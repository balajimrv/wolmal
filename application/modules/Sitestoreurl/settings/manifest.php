<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreurl
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitestoreurl',
        'version' => '-',
        'path' => 'application/modules/Sitestoreurl',
        'title' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Short Store URL Extension</span></i>',
        'description' => '<i><span style="color:#999999">Stores / Marketplace - Ecommerce Short Store URL Extension</span></i>',
        'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitestoreurl/settings/install.php',
            'class' => 'Sitestoreurl_Installer'
        ),
        'directories' => array(
            'application/modules/Sitestoreurl'
        ),
        'files' => array(
            'application/languages/en/sitestoreurl.csv'
        )
    )
);