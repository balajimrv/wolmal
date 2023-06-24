<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitemobile_content.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    array(
        'title' => 'Profile Videos',
        'description' => 'Displays a member\'s videos on their profile.',
        'category' => 'Videos',
        'type' => 'widget',
        'name' => 'sitemobile.video-profile-videos',
        'isPaginated' => true,
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
        )
?>