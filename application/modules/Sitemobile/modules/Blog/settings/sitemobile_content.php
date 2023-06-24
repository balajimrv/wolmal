<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitemobile_content.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    array(
        'title' => 'Profile Blogs',
        'description' => 'Displays a member\'s blog entries on their profile.',
        'category' => 'Blogs',
        'type' => 'widget',
        'name' => 'sitemobile.blog-profile-blogs',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Blogs',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
        )
?>