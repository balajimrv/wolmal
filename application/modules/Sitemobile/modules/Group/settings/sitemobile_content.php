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
        'title' => 'Profile Groups',
        'description' => 'Displays a member\'s groups on their profile.',
        'category' => 'SE Groups',
        'type' => 'widget',
        'name' => 'sitemobile.group-profile-groups',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Groups',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Group Profile Discussions',
        'description' => 'Displays a group\'s discussions on its profile.',
        'category' => 'SE Groups',
        'type' => 'widget',
        'name' => 'sitemobile.group-profile-discussions',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Info',
        'description' => 'Displays a group\'s info (creation date, member count, leader, officers, etc) on its profile.',
        'category' => 'SE Groups',
        'type' => 'widget',
        'name' => 'sitemobile.group-profile-info',
        'requirements' => array(
            'subject' => 'group',
        ),
        'adminForm' => array(
            'elements' => array(
                      array(
                    'Radio',
                    'groupInfoCollapsible',
                    array(
                        'label' => $view->translate('Do you want to show the group detail collapsible?'),
                        //'description' => $view->translate('Do you want to show the event detail collapsible?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 0,
                    )
                ),
                      array(
                    'Radio',
                    'groupInfoCollapsibleDefault',
                    array(
                        'label' => $view->translate('Do you want to show the group detail collapsible default?'),
                        //'description' => $view->translate('Do you want to show the event detail collapsible?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Group Profile Members',
        'description' => 'Displays a group\'s members on its profile.',
        'category' => 'SE Groups',
        'type' => 'widget',
        'name' => 'sitemobile.group-profile-members',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Members',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Photos',
        'description' => 'Displays a group\'s photos on its profile.',
        'category' => 'SE Groups',
        'type' => 'widget',
        'name' => 'sitemobile.group-profile-photos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    array(
        'title' => 'Group Profile Events',
        'description' => 'Displays a group\'s events on its profile',
        'category' => 'SE Groups',
        'type' => 'widget',
        'name' => 'sitemobile.group-profile-events',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Events',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
    
    array(
        'title' => 'Group Profile Options',
        'description' => 'Displays a list of actions that can be performed on the page which is being viewed currently (join, invite, share, etc).',
        'category' => 'SE Group Profile',
        'type' => 'widget',
        'name' => 'sitemobile.group-profile-options',
        'requirements' => array(
            'subject' => 'group',
        ),
    ),
);