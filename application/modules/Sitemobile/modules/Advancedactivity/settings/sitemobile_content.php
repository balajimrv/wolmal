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

$url = "http://www.socialengineaddons.com/page/facebook-application-submission";
return array(
    array(
        'title' => 'Advanced Activity Feed',
        'description' => 'Displays the activity feed.',
        'category' => 'Advanced Activity Feed',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-advfeed',
        'defaultParams' => array(
            'title' => 'What\'s New',
            'sitemobileadvfeed_scroll_autoload' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => 'What\'s New'
                    )
                ),
                array(
                    'Radio',
                    'sitemobileadvfeed_scroll_autoload',
                    array(
                        'label' => 'Auto-Loading Activity Feeds On-scroll',
                        'description' => "Do you want to enable auto-loading of old activity feeds when users scroll down to the bottom of Advanced Activity Feeds?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'sitemobileadvfeed_length',
                    array(
                        'label' => 'Overall Feed Length',
                        'description' => "ACTIVITY_FORM_ADMIN_SETTINGS_GENERAL_LENGTH_DESCRIPTION",
                        'value' => 15,
                        'required' => true,
                        'allowEmpty' => false,
                        'validators' => array(
                            array('Int', true),
                            array('Between', true, array(1, 50, true))
                        ),
                    )
                ),
            )
        ),
    ),
    
    array(
        'title' => 'Advanced Activity Facebook Feed',
        'description' => 'Displays the activity feed.',
        'category' => 'Advanced Activity Feed',
        'type' => 'widget',
        'name' => 'advancedactivity.advancedactivityfacebook-userfeed',
        'defaultParams' => array(
            'title' => 'Facebook Feeds',
            'sitemobilefacebookfeed_scroll_autoload' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Dummy',
                    'content',
                    array(
                        'label' => 'Note: Facebook has restricted the feature of displaying Facebook Feeds on other social sites. For more details plesae go to at: ' . $url,
                    
                    )
                ),
                
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => 'Facebook Feeds'
                    )
                ),
                array(
                    'Radio',
                    'sitemobilefacebookfeed_scroll_autoload',
                    array(
                        'label' => 'Auto-Loading Activity Feeds On-scroll',
                        'description' => "Do you want to enable auto-loading of old activity feeds when users scroll down to the bottom of Advanced Activity Feeds?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'sitemobilefacebookfeed_length',
                    array(
                        'label' => 'Overall Feed Length',
                        'description' => "ACTIVITY_FORM_ADMIN_SETTINGS_GENERAL_LENGTH_DESCRIPTION",
                        'value' => 15,
                        'required' => true,
                        'allowEmpty' => false,
                        'validators' => array(
                            array('Int', true),
                            array('Between', true, array(1, 50, true))
                        ),
                    )
                ),
            )
        ),
    ),
           array(
        'title' => 'Advanced Activity Instagram Feed',
        'description' => 'This widget can be placed in the tabbed blocks area of any page of your website which will allow users to display instagram feeds content on their site. It can be placed only once on a particular page and can\'t be placed on a page where Advanced Activity Feeds widget is already placed with instagram enabled.',
        'category' => $view->translate('Advanced Activity Feed'),
        'type' => 'widget',
        'name' => 'advancedactivity.advancedactivityinstagram-userfeed',
        'defaultParams' => array(
            'title' => 'Instagram feeds',
        ),
        'autoEdit' => true,
        'requirements' => array(
            'header-footer',
        ),
        'adminForm' => array(
            'elements' => array(
                 array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => "Instagram",
                    )
                ),
                array(
                    'Radio',
                    'advancedactivity_show_in_header',
                    array(
                        'label' => 'Feed Caption',
                        'description'=> 'Select the type of caption you want to show for your instagram feeds.',
                        'multiOptions' => array(
                            1 => 'Username',
                            0 => 'Date'
                        ),
                        'value' => 1
                )),
                array(
                    'Text',
                    'instagram_feed_count',
                    array(
                        'label' => 'Feed Count',
                        'description'=> 'Number of feeds you want to display at a time (Maximum is 40). [Note: Instagram filters the feeds on the basis of the privacy settings, before dispalying them on website. So it may happen that less feeds get displayed than the count you enter. The recommended solution is to request more than you actually need. If left empty or entered 0 then by default 20 feeds will be shown ]',
                        'value' => 8
                    )
                ),
                array(
                    'Text',
                    'instagram_image_width',
                    array(
                        'label' => 'Feed Width',
                        'description' => 'The width of your feed in pixels (Max. width is 640px).',
                        'value' => 150,
                    )
                ),  
               
            )
        )
    )  ,
    
    array(
        'title' => 'Advanced Activity Linkedin Feed',
        'description' => 'Displays the activity feed.',
        'category' => 'Advanced Activity Feed',
        'type' => 'widget',
        'name' => 'advancedactivity.advancedactivitylinkedin-userfeed',
        'defaultParams' => array(
            'title' => 'Linkedin Feeds',
            'sitemobilelinkedinfeed_scroll_autoload' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => 'Linkedin Feeds'
                    )
                ),
                array(
                    'Radio',
                    'sitemobilelinkedinfeed_scroll_autoload',
                    array(
                        'label' => 'Auto-Loading Activity Feeds On-scroll',
                        'description' => "Do you want to enable auto-loading of old activity feeds when users scroll down to the bottom of Advanced Activity Feeds?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'sitemobilelinkedinfeed_length',
                    array(
                        'label' => 'Overall Feed Length',
                        'description' => "ACTIVITY_FORM_ADMIN_SETTINGS_GENERAL_LENGTH_DESCRIPTION",
                        'value' => 15,
                        'required' => true,
                        'allowEmpty' => false,
                        'validators' => array(
                            array('Int', true),
                            array('Between', true, array(1, 50, true))
                        ),
                    )
                ),
            )
        ),
    ),
    
    array(
        'title' => 'Advanced Activity Twitter Feed',
        'description' => 'Displays the activity feed.',
        'category' => 'Advanced Activity Feed',
        'type' => 'widget',
        'name' => 'advancedactivity.advancedactivitytwitter-userfeed',
        'defaultParams' => array(
            'title' => 'Twitter Feeds',
            'sitemobiletwitterfeed_scroll_autoload' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => 'Twitter Feeds'
                    )
                ),
                array(
                    'Radio',
                    'sitemobiletwitterfeed_scroll_autoload',
                    array(
                        'label' => 'Auto-Loading Activity Feeds On-scroll',
                        'description' => "Do you want to enable auto-loading of old activity feeds when users scroll down to the bottom of Advanced Activity Feeds?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'sitemobiletwitterfeed_length',
                    array(
                        'label' => 'Overall Feed Length',
                        'description' => "ACTIVITY_FORM_ADMIN_SETTINGS_GENERAL_LENGTH_DESCRIPTION",
                        'value' => 15,
                        'required' => true,
                        'allowEmpty' => false,
                        'validators' => array(
                            array('Int', true),
                            array('Between', true, array(1, 50, true))
                        ),
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Requests',
        'description' => 'Displays the current logged-in member\'s requests (i.e. friend requests, group invites, etc).',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'activity.list-requests',
        'defaultParams' => array(
            'title' => 'Requests',
        ),
        'requirements' => array(
            'viewer',
        ),
    ),
);