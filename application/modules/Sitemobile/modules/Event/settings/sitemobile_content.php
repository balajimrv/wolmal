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
        'title' => 'Event Profile Discussions',
        'description' => "Displays an event's discussion on it's profile.",
        'category' => 'Events',
        'type' => 'widget',
        'name' => 'sitemobile.event-profile-discussions',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'event',
        ),
    ),
    array(
        'title' => 'Event Profile Info',
        'description' => 'Displays a event\'s info (creation date, member count, etc) on it\'s profile.',
        'category' => 'Events',
        'type' => 'widget',
        'name' => 'sitemobile.event-profile-info',
        'requirements' => array(
            'subject' => 'event',
        ),
        'adminForm' => array(
            'elements' => array(
                      

array(
                    'Radio',
                    'eventInfoCollapsible',
                    array(
                        'label' => $view->translate('Do you want users to be able to expand and collapse event information shown in this block?'),
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
                    'eventInfoCollapsibleDefault',
                    array(
                        'label' => $view->translate('Do you want event details to be expanded or collapsed by default? (This setting will only work, if you have selected ‘No’ in the above setting)'),
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
        'title' => 'Event Profile Members',
        'description' => 'Displays a event\'s members on it\'s profile.',
        'category' => 'Events',
        'type' => 'widget',
        'name' => 'sitemobile.event-profile-members',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Guests',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'event',
        ),
    ),
    array(
        'title' => 'Event Profile Photos',
        'description' => 'Displays a event\'s photos on it\'s profile.',
        'category' => 'Events',
        'type' => 'widget',
        'name' => 'sitemobile.event-profile-photos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'event',
        ),
    ),
    array(
        'title' => 'Event Profile RSVP',
        'description' => 'Displays options for RSVP\'ing to an event on it\'s profile.',
        'category' => 'Events',
        'type' => 'widget',
        'name' => 'sitemobile.event-profile-rsvp',
        'requirements' => array(
            'subject' => 'event',
        ),
    ),
    array(
        'title' => 'Profile Events',
        'description' => 'Displays a member\'s events on their profile.',
        'category' => 'Events',
        'type' => 'widget',
        'name' => 'sitemobile.event-profile-events',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Events',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
  array(
        'title' => 'Event Profile Options',
        'description' => 'Displays a list of actions that can be performed on the page which is being viewed currently (join, invite, share, etc).',
        'category' => 'Event Profile',
        'type' => 'widget',
        'name' => 'sitemobile.event-profile-options',
        'requirements' => array(
            'subject' => 'event',
        ),
    ),
        )
?>
