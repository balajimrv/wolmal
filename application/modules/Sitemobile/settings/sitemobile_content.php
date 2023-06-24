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

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
// ALBUM, BLOG, CLASSIFIED, EVENT, FORUM, GROUP, MUSIC,  POLL, VIDEO, 
$multiOptions = array("" => "", 'album' => 'Albums', 'blog' => 'Blogs', 'classified' => 'Classifieds', 'event' => 'Events', 'forum' => 'Forum', 'group' => 'Groups', 'music' => 'Music', 'poll' => 'Poll', 'video' => 'Videos');

$searchOptionsInt = array('sitepage', 'sitestore', 'sitegroup', 'sitebusiness', 'document', 'siteevent', 'sitemember', 'sitealbum', 'sitereview');

$modules = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getManageModulesList(array('integrated' => 1));
$searchOptions = array();
$searchOptions[] = '';
$searchOptions['core'] = 'Global Search Form';
foreach ($modules as $module) {
    if (in_array($module->name, $searchOptionsInt)) {
        $searchOptions[$module->name] = " " . $module->title . " Browse Search Form";
    }
}
if (count($searchOptions) > 1) {
    $searchModuleList = array(
        'Select',
        'module_search',
        array(
            'label' => 'Select the quick form type which you want to show on this page [Note: This setting will only work for expandable search form. A quick form will be displayed according to your form type selection].',
            'multiOptions' => $searchOptions,
            'value' => '',
        )
    );
} else {
    $searchModuleList = array();
}

//LOCATION DETECTION FIELD - CHANGE LOCATION WIDGET
$detactLocationElement = array(
    'Select',
    'detactLocation',
    array(
        'label' => 'Do you want to detect user’s current location?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);
$multiMobileOptions = array('friends' => 'Friends');
$multiComposerOptions = array();
$include = array('sitepage', 'sitebusiness', 'sitegroup', 'sitestore', 'list', 'group', 'event', 'recipe', 'album', 'advalbum', 'siteevent');
$module_table = Engine_Api::_()->getDbTable('modules', 'core');
$module_name = $module_table->info('name');
$select = $module_table->select()
        ->from($module_name, array('name', 'title'))
        ->where($module_name . '.type =?', 'extra')
        ->where($module_name . '.name in(?)', $include)
        ->where($module_name . '.enabled =?', 1);

$contentModule = $select->query()->fetchAll();
$include[] = 'friends';
foreach ($contentModule as $module) {
    if ($module['name'] != 'album' && $module['name'] != 'advalbum')
        $multiMobileOptions[$module['name']] = $module['title'];
    if ($module['name'] == 'album' || $module['name'] == 'advalbum')
        $multiComposerOptions['addPhoto'] = 'Add Photo';
}

$multiComposerOptions['addSmilies'] = 'Add Emoticons';
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
if (Engine_Api::_()->hasModuleBootstrap('nestedcomment')) {
    $content_mobile_array = array(
                'title' => 'Comments & Replies',
                'description' => 'Enable users to comment and reply on the content being viewed. Displays all the comments and replies on the Content View page. This widget should be placed on Content View page.',
                'category' => 'Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments',
                'type' => 'widget',
                'name' => 'sitemobile.comments',
                'autoEdit' => 'true',
                'defaultParams' => array(
                    'title' => '',
                    'taggingContent' => array("friends")
                ),
                'adminForm' => array(
                    'elements' => array(
                        array(
                            'MultiCheckbox',
                            'taggingContent',
                            array(
                                'label' => "In the comments on content of this module, which all types of content should be taggable? (users will be able to tag a content entity in their comments using '@' symbol)",
                                'multiOptions' => $multiMobileOptions
                            )
                        ),
                        array(
                            'MultiCheckbox',
                            'showComposerOptions',
                            array(
                                'label' => "Which all types of attachments do you want to allow in comments on this module's content?",
                                'multiOptions' => $multiComposerOptions,
                            //'value' => ''
                            )
                        ),
                        array(
                            'Radio',
                            'showAsNested',
                            array(
                                'label' => "Do you want to enable nested comments feature for this module's content? If selected no, then simple, one-level commenting will be available for this module's content, and users will not be able to 'Reply' to comments.",
                                'multiOptions' => array(
                                    1 => 'Yes',
                                    0 => 'No'
                                ),
                                'value' => 1
                            ),
                        ),
                        array(
                            'Radio',
                            'showAsLike',
                            array(
                                'label' => "Selection of Like / Dislike (By choosing the below options you will be able to enable the Like / Dislike link for your content, comment and replies.)",
                                'multiOptions' => array(
                                    1 => 'Only Like',
                                    0 => 'Both, Like and Dislike'
                                ),
                                'value' => 1
                            ),
                        ),
                        array(
                            'Radio',
                            'showDislikeUsers',
                            array(
                                'label' => "Do you want to show the users who have disliked a content, comment and reply? Sometimes Site Admins do not want to show that who all have disliked a content. So, if you do not want to show this to users then please select ‘No’. [Note: This setting will work only if you have selected 'Both, Like and Dislike' option in the above setting.]",
                                'multiOptions' => array(
                                    1 => 'Yes',
                                    0 => 'No'
                                ),
                                'value' => 1
                            ),
                        ),
                        array(
                            'Radio',
                            'showLikeWithoutIcon',
                            array(
                                'label' => 'How do you want to display the Like / Dislike options for content of this module? This setting will work only when you have selected ‘Both, Like and Dislike’ option in the above setting.',
                                'multiOptions' => array(
                                    1 => 'Only Text',
                                    0 => 'Text With Icon',
                                    3 => 'Vote Up and Vote Down'
                                ),
                                'value' => 1
                            ),
                        ),
                        array(
                            'Radio',
                            'showLikeWithoutIconInReplies',
                            array(
                                'label' => "How do you want to display the Like / Dislike options for comments and replies? This setting will work only when you have selected 'Both, Like and Dislike' option in the above setting.",
                                'multiOptions' => array(
                                    1 => 'Only Text',
                                    0 => 'Text With Icon',
                                    2 => 'Only Icon',
                                    3 => 'Vote Up and Vote Down'
                                ),
                                'value' => 1
                            ),
                        ),
                    ),
                ),
                'requirements' => array(
                    'subject',
                ),
    );
} else {
    $content_mobile_array = array(
        'title' => 'Comments',
        'description' => 'Shows the comments about an item.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.comments',
        'defaultParams' => array(
            'title' => 'Comments'
        ),
        'requirements' => array(
            'subject',
        ),
    );
}
$content_array = array(
    array(
        'title' => 'HTML Block',
        'description' => 'Inserts any HTML of your choice.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'core.html-block',
        'special' => 1,
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title'
                    )
                ),
                array(
                    'Textarea',
                    'data',
                    array(
                        'label' => 'HTML'
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Ad Campaign',
        'description' => 'Shows one of your ad banners. Requires that you have at least one active ad campaign.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.ad-campaign',
        // 'special' => 1,
        'autoEdit' => true,
        'adminForm' => 'Core_Form_Admin_Widget_Ads',
    ),
    array(
        'title' => 'Background / Watermark Image',
        'description' => 'Shows the background/watermark image.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.background-image',
        // 'special' => 1,
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Hidden',
                    'title',
                    array(
                        'label' => 'Image Path',
                    )
                ),
                array(
                    'Text',
                    'backgroundImage',
                    array(
                        'label' => 'Enter the Background / Watermark Image Path.',
                    )
                ),
            )
        )
    ),
    array(/* change */
        'title' => 'Tab Container',
        'description' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.container-tabs-columns',
        'special' => 1,
        'defaultParams' => array(
            'layoutContainer' => 'horizontal',
        // 'max' => 4,
        ),
        'canHaveChildren' => true,
        'childAreaDescription' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
        //'special' => 1,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                    )
                ),
                array(
                    'Select',
                    'layoutContainer',
                    array(
                        'label' => 'Choose the view that you want to be available for the tabs in this tab container.',
                        'default' => 'tab',
                        'multiOptions' => array(
                            'tab' => 'Tab Collapsible View',
                            // 'vertical' => 'Vertical View',
                            'horizontal' => 'Horizontal Tab View',
                            'horizontal_icon' => 'Horizontal Tab with Icon View',
                            'panel' => 'Tab Panel View',
                        ),
                        'value' => 'horizontal_icon'
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Content',
        'description' => 'Shows the page\'s primary content area. (Not all pages have primary content)',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'core.content',
        'requirements' => array(
            'page-content',
        ),
    ),
    array(
        'title' => 'Site Logo',
        'description' => 'Shows your site-wide main logo. Images are uploaded via the "File Media Manager".',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-menu-logo',
        'adminForm' => 'Sitemobile_Form_Admin_Widget_Logo',
        'requirements' => array(
            'header-footer',
        ),
    ),
    array(
        'title' => 'Dashboard Menu',
        'description' => 'Shows the dashboard menu. You can edit its contents in your dashboard menu editor.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.dashboard',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'showSearch',
                    array(
                        'label' => 'Do you want to be show search form',
                        'default' => '1',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        )
                    )
                ),
            ),
        )
    ),
    array(
        'title' => 'Back Button',
        'description' => 'Shows back button.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.back-button',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'buttonType',
                    array(
                        'label' => 'Select the display type of back button.',
                        'default' => 'text',
                        'multiOptions' => array(
                            'notext' => 'Only Icon',
                            'text' => 'Only Text',
                            'both' => 'Both Icon and Text',
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Profile Links',
        'description' => 'Displays a member\'s, group\'s, or event\'s links on their profile.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.profile-links',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Links',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject',
        ),
    ),
    array(
        'title' => 'Footer',
        'description' => 'Shows the footer menu.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-footer',
        'requirements' => array(
            'header-footer',
        ),
        'defaultParams' => array(
            'shows' => array("copyright", "menusFooter", "languageChooser", "affiliateCode")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'shows',
                    array(
                        'label' => 'Select the options below that you want to be displayed in this block.',
                        'multiOptions' => array("copyright" => "Copyright", "menusFooter" => "Footer Menus", "languageChooser" => "Language Chooser", 'affiliateCode' => 'Affiliate Code'),
                    ),
                )
            )
        )
    ),
    $content_mobile_array,
    array(
        'title' => 'Startup Image',
        'description' => 'Shows the  startup image that will appears during start-up, before loading of site. You can add a Startup Image from the "Layout" >> "File & Media Manager". This widget should be placed on the "Startup Page"',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.startup',
        'adminForm' => 'Sitemobile_Form_Admin_Widget_Startup',
        'requirements' => array(
            'header-footer',
        ),
    ),
//    array(
//        'title' => 'Statistics',
//        'description' => 'Shows some basic usage statistics about your community.',
//        'category' => 'Core',
//        'type' => 'widget',
//        'name' => 'core.statistics',
//        'defaultParams' => array(
//            'title' => 'Statistics'
//        ),
//        'requirements' => array(
//            'no-subject',
//        ),
//    ),
    array(
        'title' => 'Contact Form',
        'description' => 'Displays the contact form.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'core.contact',
        'requirements' => array(
            'no-subject',
        ),
        'defaultParams' => array(
            'title' => 'Contact',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Quick Action Links',
        'description' => 'Enables you to display notifications, requests and messages received by a member, products added in the cart and member’s current location / location selected by him.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-notification-request-messages',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loadingViaAjax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' =>'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content. (Note: Select ‘No’, if you are placing this widget in the Footer of your site.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'activePage',
                    array(
                        'description' => 'Do you want to show icons for "notifications, requests and messages" in the header for logged-in users on all Pages of your app / mobile website?',
                        'multiOptions' => array(
                            1 => 'No, show these icons only on Member Home Page and on other pages with Dashboard icon in header, show total notifications count.',
                            2 => 'Yes'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'location',
                    array(
                        'label' => 'Do you want to enable ‘location’ field, in the header for logged-in users? (If enabled a ‘location marker’ type icon will be visible in the header, for all the pages of your app / mobile website.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Advanced Search',
        'description' => 'Add the ability to search your site’s content on any page..',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-advancedsearch',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'search',
                    array(
                        'label' => 'Select the display type for Search.',
                        'multiOptions' => array(
                            1 => 'Only Search Text field',
                            3 => 'Expanded Advanced Search',
                            2 => 'Search Text field with expandable Advanced Search options',
                        ),
                        'value' => 2,
                    )
                ),
                $searchModuleList,
            )
        )
    ),
    array(
        'title' => 'Options',
        'description' => 'Displays a list of actions that can be performed on the page which is being viewed currently (edit, report, join, invite, etc).',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-options'
    ),
    array(
        'title' => 'Profile Photo and Status',
        'description' => 'Displays a profiles photo and status on it\'s profile.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.profile-photo-status'
// 			'requirements' => array(
// 				'subject' => 'event',
// 			),
    ),
    array(
        'title' => 'Scroll To Top',
        'description' => 'This widget displays a "Scroll To Top" button when users scroll down to the bottom of the page. This widget should be placed at the height of your page where you want the user to be scrolled-to upon clicking.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.scroll-to-top',
        'adminForm' => array(
            'elements' => array(
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => ''
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Announcements',
        'description' => 'Displays recent announcements.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.list-announcements',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Announcements',
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Navigation',
        'description' => 'Displays Navigation.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-navigation',
//        'isPaginated' => true,
        'defaultParams' => array(
            'title' => '',
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Displays User Photo and Title.',
        'description' => 'Displays User Photo and Title.',
        'category' => 'User',
        'type' => 'widget',
        'name' => 'sitemobile.user-photo-title',
//        'isPaginated' => true,
        'defaultParams' => array(
            'title' => '',
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Page Title',
        'description' => 'Displays the title of the page.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-headingtitle',
        'isPaginated' => false,
        'defaultParams' => array(
            'title' => '',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'nonloggedin',
                    array(
                        'label' => 'Show Page Title to users (non-logged in users) of your site.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'loggedin',
                    array(
                        'label' => 'Show Page Title to members (logged-in users) of your site.',
                        'multiOptions' => array(
                            1 => "Yes, show titles on all the pages",
                            2 => "Yes, show titles on all the pages except Member Home Page",
                            0 => "No, do not show titles"
                        ),
                        'value' => 0,
                    )
                )
            )
        )
    ),
);

if (Engine_Api::_()->seaocore()->getLocationsTabs()) {
    $content_array[] = array(
        'title' => "Change User's Location",
        'description' => "Displays user's location with 'change my location' link to change current location. Setting to choose how user's can change their location is available in the edit section of this widget.",
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'seaocore.change-my-location',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                $detactLocationElement,
                array(
                    'Radio',
                    'showSeperateLink',
                    array(
                        'label' => 'How do you want users to change their location? [Note: This setting will not work if you have enabled "Specific Locations" setting.]',
                        'multiOptions' => array(
                            1 => 'By using "change my location" link.',
                            0 => 'By clicking on existing location name.'
                        ),
                        'value' => 0,
                    )
                )
            ),
        ),
    );
}

return $content_array;
?>