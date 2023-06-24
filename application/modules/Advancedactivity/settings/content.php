<?php
 /**
* SocialEngine
*
* @category   Application_Extensions
* @package    Advancedactivity
* @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: content.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$final_array =  array(
    array(
        'title' => $view->translate('Advanced Activity Feeds'),
//        'description' => $view->translate('Displays the advanced activity feeds on your site. This widget facilitates you to enable any of the 5 available tabs: Welcome, Site Activity Feeds, Instagram Feeds, Twitter Feeds and Linkedin Feeds at various widget locations. On content profile / view pages, in site activity feeds, the feeds for respective content profile will show, whereas at other locations of this widget, overall site activity feeds will show. The Welcome, Instagram, Twitter and Linkedin tabs will not be shown on the content profile / view pages even if they are enabled. Instagram, Twitter and Linkedin tabs will show the logged-in user\'s Instagram, Twitter and Linkedin feeds. It is recommended to place this widget where SocialEngine\'s Core Activity Feed widget is placed.'),
        'description' => $view->translate('Displays the advanced activity feeds on your site. This widget facilitates you to enable any of the 5 available tabs: Welcome, Site Activity Feeds, Twitter Feeds and Linkedin Feeds at various widget locations. <br>If placed on Content / Member profile pages, it will display the feeds related to content / member, else overall site activity feeds will show.'),
        'category' => $view->translate('Advanced Activities'),
        'type' => 'widget',
        'name' => 'advancedactivity.home-feeds',
        'defaultParams' => array(
            'title' => 'What\'s New',
            'showFeeds' => 1,
            'advancedactivity_tabs'=>array("aaffeed")
        ),
        'autoEdit' => true,
        'adminForm' => 'Advancedactivity_Form_Admin_homeFeeds',
    ),
    
    array(
        'title' => $view->translate('Welcome: Search for People'),
        'description' => $view->translate('This is a widget for the Welcome Tab. This block shows to users a search field to search for their friends who might be members of the site. This enables them to easily and quickly add them as friends to grow their network on your site.'), 
        'category' => $view->translate('Advanced Activities'),
        'type' => 'widget',
        'name' => 'advancedactivity.search-for-people',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => $view->translate('Welcome: Profile Photo Uploading'),
        'description' => $view->translate('This is a widget for the Welcome Tab. This block will enable users to easily and quickly upload a profile photo, thus increasing trust on your website.'),
        'category' => $view->translate('Advanced Activities'),
        'type' => 'widget',
        'name' => 'advancedactivity.profile-photo',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => $view->translate('Welcome: Custom Blocks'),
        'description' => $view->translate("This is a widget for the Welcome Tab. You can use custom blocks to show welcome content to users which is different from the already available blocks. For example, you can introduce those features / aspects of your website that form your site's most important core features. To manage content of this widget, please go to the Custom Blocks tab in Welcome Settings of Advanced Activity Feeds plugin."),
        'category' => $view->translate('Advanced Activities'),
        'type' => 'widget',
        'name' => 'advancedactivity.custom-block',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => $view->translate('Welcome: Welcome Message'),
        'description' => $view->translate('This is a widget for the Welcome Tab. This block shows to users a welcome message with their name in it, thus increasing personalization on your website.'),
        'category' => $view->translate('Advanced Activities'),
        'type' => 'widget',
        'name' => 'advancedactivity.welcome-message',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
//    array(
//        'title' => 'Instagram feeds',
//        'description' => 'This widget can be placed in the tabbed blocks area of any page of your website which will allow users to display instagram feeds content on their site. It can be placed only once on a particular page and can\'t be placed on a page where Advanced Activity Feeds widget is already placed with instagram enabled.',
//        'category' => $view->translate('Advanced Activities'),
//        'type' => 'widget',
//        'name' => 'advancedactivity.advancedactivityinstagram-userfeed',
//        'defaultParams' => array(
//            'title' => 'Instagram feeds',
//        ),
//        'autoEdit' => true,
//        'requirements' => array(
//            'header-footer',
//        ),
//        'adminForm' => array(
//            'elements' => array(
//                array(
//                    'Radio',
//                    'advancedactivity_show_in_header',
//                    array(
//                        'label' => 'Feed Caption',
//                        'description'=> 'Select the type of caption you want to show for your instagram feeds.',
//                        'multiOptions' => array(
//                            1 => 'Username',
//                            0 => 'Date'
//                        ),
//                        'value' => 1
//                )),
//                array(
//                    'Text',
//                    'instagram_feed_count',
//                    array(
//                        'label' => 'Feed Count',
//                        'description'=> 'Number of feeds you want to display at a time (Maximum is 40). [Note: Instagram filters the feeds on the basis of the privacy settings, before dispalying them on website. So it may happen that less feeds get displayed than the count you enter. The recommended solution is to request more than you actually need. If left empty or entered 0 then by default 20 feeds will be shown ]',
//                        'value' => 8
//                    )
//                ),
//                array(
//                    'Text',
//                    'instagram_image_width',
//                    array(
//                        'label' => 'Feed Width',
//                        'description' => 'The width of your feed in pixels (Max. width is 640px).',
//                        'value' => 150,
//                    )
//                ),
//                array(
//                    'Radio',
//                    'instagram_disable_viewmore',
//                    array(
//                        'label' => 'View More',
//                        'description'=> 'Do you want to disable view more for this widget?[Note: Disabling view more button will limit the shown feeds to the less then or equal to the feed count.]',
//                        'multiOptions' => array(
//                            1 => 'Yes',
//                            0 => 'No'
//                        ),
//                        'value' => 0
//                )),
//            )
//        )
//    )    
//    array(
//        'title' => $view->translate('Activity Post Feed Button Widget'),
//        'description' => $view->translate('This is a widget for the show post feed button.'),
//        'category' => $view->translate('Advanced Activities'),
//        'type' => 'widget',
//        'name' => 'advancedactivity.post-feed-button',
//        'defaultParams' => array(
//            'title' => '',
//        ),
//        'autoEdit' => true,
//        'adminForm' => array(
//            'elements' => array(
//                array(
//                    "Textarea",
//                    "description",
//                    array(
//                        'label' => 'Description',
//                    )
//                ),
//            )
//        )
//    ),
 );

return $final_array;
?>
