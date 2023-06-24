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

$showContent_timeline = array("profileFields" => "Profile Fields", "memberType" => "Member Type", "networks" => "Networks", "profileViews" => "Profile Views", "friends" => "Friends / Followers", "lastUpdated" => "Last Updated", "joined" => "Joined", "enabled" => "Enabled");
$showContent_option = array("profileFields", "memberType", "networks", "profileViews", "friends", "lastUpdated", "joined", "enabled");

return array(
    array(
        'title' => 'User Photo',
        'description' => 'Displays the logged-in member\'s photo.',
        'category' => 'User',
        'type' => 'widget',
        'name' => 'sitemobile.user-home-photo',
        'requirements' => array(
            'viewer',
        ),
    ),
    array(
        'title' => 'Login or Signup',
        'description' => 'Displays a login form and a signup link for members that are not logged in.',
        'category' => 'User',
        'type' => 'widget',
        'name' => 'sitemobile.login-or-signup',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Profile Friends',
        'description' => 'Displays a member\'s friends on their profile.',
        'category' => 'User',
        'type' => 'widget',
        'name' => 'sitemobile.user-profile-friends',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Friends',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Profile Followers',
        'description' => 'Displays a member\'s followers on their profile.',
        'category' => 'User',
        'type' => 'widget',
        'name' => 'sitemobile.user-profile-friends-followers',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Followers',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Profile Following',
        'description' => 'Displays the members a member is following on their profile.',
        'category' => 'User',
        'type' => 'widget',
        'name' => 'sitemobile.user-profile-friends-following',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Following',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Profile Mutual Friends',
        'description' => 'Displays the mutual friends between the viewer and the subject.',
        'category' => 'User',
        'type' => 'widget',
        'name' => 'sitemobile.user-profile-friends-common',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Mutual Friends'
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Profile Fields & Member Info',
        'description' => 'Displays a member\'s profile field data and info (signup date, friend count, etc) on their profile.',
        'category' => 'User',
        'type' => 'widget',
        'name' => 'sitemobile.user-profile-info-fields',
        'requirements' => array(
            'subject' => 'user',
            'showContent' => $showContent_option
        ),
        'adminForm' => array(
            'elements' => array(
								array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the profile field data and info that you want to be show in this block.',
                        'multiOptions' => $showContent_timeline,
                    ),
                ), 
            ),
        ),
    ),
    array(
        'title' => 'Profile Tags',
        'description' => 'Displays photos, blogs, etc that a member has been tagged in.',
        'category' => 'User',
        'type' => 'widget',
        'name' => 'sitemobile.user-profile-tags',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Tags',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
        )
?>