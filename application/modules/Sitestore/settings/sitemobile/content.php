<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.isActivate', 0);
if (empty($isActive)) {
    return;
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$ads_Array = $categories_prepared = array();
$categories = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories();
if (count($categories) != 0) {
    $categories_prepared[0] = "";
    foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
    }
}

$popularity_options_store = array(
    'Recently Posted' => $view->translate('Recently Posted'),
    'Most Viewed' => $view->translate('Most Viewed'),
    'Featured' => $view->translate('Featured'),
    'Sponosred' => $view->translate('Sponosred'),
);

$category_stores_multioptions = array(
    'view_count' => $view->translate('Views'),
    'like_count' => $view->translate('Likes'),
    'comment_count' => $view->translate('Comments'),
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
    $category_stores_multioptions['review_count'] = $view->translate('Reviews');
}


$showContentOptions = array("mainPhoto" => "Store Profile Photo", "title" => "Store Title", "sponsored" => "Sponsored Label", "featured" => "Featured Label", "category" => "Category", "subcategory" => "Sub-Category", "subsubcategory" => "3rd Level Category", "likeButton" => "Like Button", "followButton" => "Follow", "description" => "About / Description", "phone" => "Phone", "email" => "Email", "website" => "Website", "location" => "Store Location", "tags" => "Tags", "price" => "Price");
$showContentDefault = array("mainPhoto", "title", "sponsored", "featured", "category", "subcategory", "subsubcategory", "likeButton", "followButton", "description", "phone", "email", "website", "location", "tags", "price");

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
    $showContentOptions['addButton'] = 'Add People Button';
    $showContentOptions['joinButton'] = 'Join Store Button / Cancel Membership Request Button';
    $showContentDefault[] = 'addButton';
    $showContentDefault[] = 'joinButton';
}

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge')) {
    $showContentOptions['badge'] = 'Badge';
    $showContentDefault[] = 'badge';
}

$detactLocationElement = array(
    'Select',
    'detactLocation',
    array(
        'label' => 'Do you want to display members based on user’s current location?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    )
);
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.proximity.search.kilometer', 0)) {
    $locationDescription = "Choose the kilometers within which stores will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Kilometer";
    $locationLable = "Kilometers";
} else {
    $locationDescription = "Choose the miles within which stores will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
    $locationLableS = "Mile";
    $locationLable = "Miles";
}


$defaultLocationDistanceElement = array(
    'Select',
    'defaultLocationDistance',
    array(
        'label' => $locationDescription,
        'multiOptions' => array(
            '0' => '',
            '1' => '1 ' . $locationLableS,
            '2' => '2 ' . $locationLable,
            '5' => '5 ' . $locationLable,
            '10' => '10 ' . $locationLable,
            '20' => '20 ' . $locationLable,
            '50' => '50 ' . $locationLable,
            '100' => '100 ' . $locationLable,
            '250' => '250 ' . $locationLable,
            '500' => '500 ' . $locationLable,
            '750' => '750 ' . $locationLable,
            '1000' => '1000 ' . $locationLable,
        ),
        'value' => '1000'
    )
);
$final_array = array(
    array(
        'title' => $view->translate('Browse Stores'),
        'description' => $view->translate('Displays a list of all the stores on site. This widget should be placed on the Browse Stores store.'),
        'category' => $view->translate('Stores / Marketplace - Stores'),
        'type' => 'widget',
        'name' => 'sitestore.sitemobile-stores-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_views' => array("0" => "1", "1" => "2", "2" => "3"),
            'view_selected' => 'grid',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => $view->translate('Choose the view types that you want to be available for stores on the stores home and browse stores.'),
                        'multiOptions' => array("1" => "List View", "2" => "Grid View")
                    ),
                ),
                array(
                    'Radio',
                    'view_selected',
                    array(
                        'label' => $view->translate('Select a default view type for Stores.'),
                        'multiOptions' => array("list" => "List View", "grid" => "Grid View")
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '325',
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => $view->translate('Category'),
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'content_display',
                    array(
                        'label' => $view->translate('Choose the options that you want to be displayed for the Stores in this block.'),
                        'multiOptions' => array(
                            "featured" => "Featured Label",
                            "sponsored" => "Sponsored Label",
                            "closed" => "Close Store Icon",
                            "ratings" => "Ratings",
                            "date" => "Creation Date",
                            "owner" => "Posted By",
                            "likeCount" => "Likes",
                            "followCount" => "Followers",
                            "memberCount" => "Members",
                            "reviewCount" => "Reviews",
                            "commentCount" => "Comments",
                            "viewCount" => "Views",
                            "location" => "Location",
                            "price" => "Price",
                        )
                    ),
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        )
    ),
    array(
        'title' => $view->translate('Popular / Recent Stores'),
        'description' => $view->translate('Displays stores based on the Popularity Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.'),
        'category' => $view->translate('Stores / Marketplace - Stores'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestore.sitemobile-popular-stores',
        'defaultParams' => array(
            'title' => $view->translate('Stores'),
            'titleCount' => true,
            'content_display' => array("date", "owner", "ratings", "likeCount", "reviewCount", "viewCount"),
            'viewType' => 'gridview',
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => $view->translate('Choose the view types that you want to be available for stores on the stores home.'),
                        'multiOptions' => array("1" => "List View", "2" => "Grid View"),
                        'value' => array("1", "2"),
                    ),
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Choose the View Type for Stores.'),
                        'multiOptions' => array(
                            'listview' => $view->translate('List View'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                        'value' => 'gridview',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '230',
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => $view->translate('Category'),
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'content_display',
                    array(
                        'label' => $view->translate('Choose the options that you want to be displayed for the Stores in this block.'),
                        'multiOptions' => array(
                            "ratings" => "Ratings",
                            "date" => "Creation Date",
                            "owner" => "Posted By",
                            "likeCount" => "Likes",
                            "followCount" => "Followers",
                            "reviewCount" => "Reviews",
                            "commentCount" => "Comments",
                            "viewCount" => "Views",
                            "location" => "Location",
                        )
                    ),
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => $popularity_options_store,
                        'value' => 'Recently Posted',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Stores to show)'),
                        'value' => 5,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 16,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                $detactLocationElement,
                array(
                    'Select',
                    'locationmiles',
                    array(
                        'label' => $locationDescription,
                        'multiOptions' => array(
                            '0' => '',
                            '1' => '1 ' . $locationLableS,
                            '2' => '2 ' . $locationLable,
                            '5' => '5 ' . $locationLable,
                            '10' => '10 ' . $locationLable,
                            '20' => '20 ' . $locationLable,
                            '50' => '50 ' . $locationLable,
                            '100' => '100 ' . $locationLable,
                            '250' => '250 ' . $locationLable,
                            '500' => '500 ' . $locationLable,
                            '750' => '750 ' . $locationLable,
                            '1000' => '1000 ' . $locationLable,
                        ),
                        'value' => '1000'
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Categories, Sub-categories and 3<sup>rd</sup> Level-categories (sidebar)',
        'description' => 'Displays the Categories, Sub-categories and 3<sup>rd</sup> Level-categories of stores in an expandable form. Clicking on them will redirect the viewer to the list of stores created in that category.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.categories-sitestore',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Info'),
        'description' => $view->translate('This widget forms the Info tab on the Store Profile and displays the information of the Store. It should be placed in the Tabbed Blocks area of the Store Profile.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.sitemobile-info-sitestore',
        'defaultParams' => array(
            'title' => $view->translate('Info'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Store Profile Overview',
        'description' => 'Displays rich overview on Store\'s profile, created by its admin using the editor from Store Dashboard. This should be placed in the Tabbed Blocks area of Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.sitemobile-overview-sitestore',
        'defaultParams' => array(
            'title' => 'Overview',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Map'),
        'description' => $view->translate('This widget forms the Map tab on the Store Profile. It displays the map showing the Store position as well as the location details of the store. It should be placed in the Tabbed Blocks area of the Store Profile. This feature will be available to Stores based on their Package and Member Level of their owners.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.sitemobile-location-sitestore',
        'defaultParams' => array(
            'title' => $view->translate('Map'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Alphabetic Filtering of Stores'),
        'description' => $view->translate("This widget enables users to alphabetically filter the stores on your site by clicking on the desired alphabet. The widget shows all the alphabets for filtering."),
        'category' => $view->translate('Stores / Marketplace - Stores'),
        'type' => 'widget',
        'name' => 'sitestore.alphabeticsearch-sitestore',
        'defaultParams' => array(
            'title' => $view->translate(""),
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Store Profile Cover Photo and Information',
        'description' => 'Displays the cover photo of a Store. From the Edit Settings section of this widget, you can also choose to display store member’s profile photos, if Store Admin has not selected a cover photo. It is recommended to place this widget on the Store Profile at the top.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.sitemobile-storecover-photo-information',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'showContent' => $showContentDefault
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => $showContentOptions,
                    ),
                ),
                array(
                    'Radio',
                    'strachPhoto',
                    array(
                        'label' => 'Do you want store profile pictures to be displayed in consistent blocks of fixed dimension below the cover photo on your site?',
                        'multiOptions' => array("1" => "Yes (Though the dimensions of the store profile picture block will be consistent, and the photos with unequal dimension will be shown in the center of the block.)", "0" => "No (The dimension of the store profile picture block will not be fixed. In this case block’s dimensions will depend on the dimensions of store profile picture.)"),
                        'value' => 0
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Store Profile Likes',
        'description' => 'Displays list of user who have liked the store. This widget should be placed on Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'seaocore.sitemobile-people-like',
        'defaultParams' => array(
            'title' => $view->translate("Member Likes"),
            'titleCount' => "true",
        ),
    ),
    array(
        'title' => 'Content Profile: Content Followers',
        'description' => 'Displays a list of all the users who are Following the content on which this widget is placed. This widget should be placed on any content’s profile / view page.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'seaocore.sitemobile-followers',
        'defaultParams' => array(
            'title' => "Followers",
            'titleCount' => "true",
        ),
    ),
    array(
        'title' => 'Store Profile Featured Store Admins',
        'description' => "Displays the Featured Admins of a store. This widget should be placed on the Store Profile.",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.featuredowner-sitestore',
        'defaultParams' => array(
            'title' => "Store Admins",
            'titleCount' => "true",
        ),
    ),
//    array(
//        'title' => 'Sub Stores of a Store',
//        'description' => 'Displays the sub stores created in the Store which is being viewed currently. This widget should be placed on the Store Profile page.',
//        'category' => 'Store Profile',
//        'type' => 'widget',
//        'name' => 'sitestore.substore-sitestore',
//        'defaultParams' => array(
//            'title' => 'Sub Stores of a Store',
//            'titleCount' => true,
//        ),
//        'adminForm' => array(
//            'elements' => array(
//                array(
//                    'Text',
//                    'itemCount',
//                    array(
//                        'label' => 'Count',
//                        'description' => '(number of stores to show)',
//                        'value' => 3,
//                        'validators' => array(
//                            array('Int', true),
//                            array('GreaterThan', true, array(0)),
//                        ),
//                    )
//                ),
//            ),
//        ),
//    ),
    array(
        'title' => 'Store Profile Linked Stores',
        'description' => 'Displays list of stores linked to a store. This widget should be placed on the Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.favourite-store',
        'defaultParams' => array(
            'title' => 'Linked Stores',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of stores to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Select',
                    'featured',
                    array(
                        'label' => 'Featured',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
                array(
                    'Select',
                    'sponsored',
                    array(
                        'label' => 'Sponsored',
                        'multiOptions' => array(
                            0 => '',
                            2 => 'Yes',
                            1 => 'No',
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Profile Stores',
        'description' => 'Displays members\' stores on their profile.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.profile-sitestore',
        'defaultParams' => array(
            'title' => 'Stores / Marketplace - Stores',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'storeAdmin',
                    array(
                        'label' => 'Which all Stores related to the user do you want to display in this tab widget on their profile?',
                        'multiOptions' => array(
                            1 => 'Stores Owned by the user. (Store Owner)',
                            2 => 'Stores Administered by the user. (Store Admin)'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Close Store Message',
        'description' => 'If a Store is closed, then show this message.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.closestore-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Breadcrumb'),
        'description' => $view->translate('Displays breadcrumb of the store based on the categories. This widget should be placed on the Store Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.store-profile-breadcrumb',
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
    $joined_array = array(
        array(
            'title' => 'Joined / Owned Stores',
            'description' => 'Displays stores administered and joined by members on their profiles. This widget should be placed on the Member Profile page.',
            'category' => 'Stores / Marketplace - Stores',
            'type' => 'widget',
            'name' => 'sitestore.profile-joined-sitestore',
            'defaultParams' => array(
                'title' => 'Joined / Owned Stores',
                'titleCount' => true,
            ),
            'adminForm' => array(
                'elements' => array(
                    array(
                        'Radio',
                        'storeAdminJoined',
                        array(
                            'label' => 'Which all Stores related to the user do you want to display in this tab widget on their profile?',
                            'multiOptions' => array(
                                1 => 'Both Stores Administered and Joined by user',
                                2 => 'Only Stores Joined by user'
                            ),
                            'value' => 2,
                        )
                    ),
                    array(
                        'Text',
                        'textShow',
                        array(
                            'label' => 'Enter the verb to be displayed for the store admin approved members. (If you do not want to display any verb, then simply leave this field blank.)',
                            'value' => 'Verified',
                        ),
                    ),
                    array(
                        'Radio',
                        'showMemberText',
                        array(
                            'label' => 'Show Member Text?',
                            'multiOptions' => array(
                                1 => 'Yes',
                                0 => 'No'
                            ),
                            'value' => 1,
                        )
                    ),
                    array(
                        'Select',
                        'category_id',
                        array(
                            'label' => 'Category',
                            'multiOptions' => $categories_prepared,
                        )
                    ),
                )
            ),
        )
    );
}
if (!empty($joined_array)) {
    $final_array = array_merge($final_array, $joined_array);
}

$custom_array = array(
    array(
        'title' => 'Mobile Manage Stores',
        'description' => 'Displays stores administered by viewer.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.sitemobile-custom-managestores',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Mobile Manage Products',
        'description' => 'Displays products in a store which is administered by viewer.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.sitemobile-custom-manageproducts',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Mobile Manage Orders',
        'description' => 'Displays orders in a store which is administered by viewer.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.sitemobile-custom-manageorders',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
);
if (!empty($custom_array)) {
    $final_array = array_merge($final_array, $custom_array);
}

return $final_array;
?>