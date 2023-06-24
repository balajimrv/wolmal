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

$showCreationLink = array(
    'Radio',
    'creationLink',
    array(
        'label' => 'Which link you want to show in this widget.',
        'multiOptions' => array(
            '1' => 'Store creation',
            '0' => 'Store creation',
        ),
        'value' => 1,
    )
);

$featuredSponsoredElement = array(
    'Select',
    'fea_spo',
    array(
        'label' => 'Show Stores',
        'multiOptions' => array(
            '' => '',
            'featured' => 'Featured Only',
            'sponsored' => 'Sponsored Only',
            'fea_spo' => 'Both Featured and Sponsored',
        ),
        'value' => 'sponsored',
    )
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {

  $popularity_options = array(
      'view_count' => 'Most Viewed',
      'like_count' => 'Most Liked',
      'comment_count' => 'Most Commented',
      'review_count' => 'Most Reviewed',
      'rating' => 'Most Rated',
      'store_id' => 'Most Recent',
      'modified_date' => 'Recently Updated',
  );
} else {
  $popularity_options = array(
      'view_count' => 'Most Viewed',
      'like_count' => 'Most Liked',
      'comment_count' => 'Most Commented',
      'store_id' => 'Most Recent',
      'modified_date' => 'Recently Updated',
  );
}

$category_stores_multioptions = array(
    'view_count' => 'Views',
    'like_count' => 'Likes',
    'comment_count' => 'Comments',
);
$pinboardShowsOptions = array(
    "viewCount" => "Views",
    "likeCount" => "Likes",
    "commentCount" => "Comments",
    'followCount' => 'Followers',
    "price" => 'Price',
    "location" => "Location"
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
  $pinboardShowsOptions['reviewsRatings'] = "Reviews & Ratings";
}

$pinboardPopularityOptions = array(
    'view_count' => 'Most Viewed',
    'like_count' => 'Most Liked',
    'comment_count' => 'Most Commented',
    'follow_count' => 'Most Following',
    'store_id' => 'Most Recent',
    'modified_date' => 'Recently Updated',
);

$showContent_timeline = array("mainPhoto" => "Store Profile Photo", "title" => "Store Title", "followButton" => "Follow Button", "likeButton" => "Like Button", "likeCount" => "Total Likes", "followCount" => "Total Followers");
$showContent_option = array("mainPhoto", "title", "followButton", "likeButton", "followCount", "likeCount");

$layouts_tabs = array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5');
$layouts_tabs_options = array("1" => "Recent", "2" => "Most Popular", "3" => "Random", "4" => "Featured", "5" => "Sponsored");
$joined_order = array();
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
  $showContent_timeline['memberCount'] = 'Total Members';
  $showContent_timeline['addButton'] = 'Add People Button';
  $showContent_timeline['joinButton'] = 'Join Store Button';

  $showContent_option[] = 'addButton';
  $showContent_option[] = 'joinButton';
  $showContent_option[] = 'memberCount';

  $pinboardShowsOptions['memberCount'] = 'Members';
  $pinboardPopularityOptions['member_count'] = "Most Joined Stores";
  $layouts_tabs['5'] = "6";
  $layouts_tabs_options["6"] = "Most Joined Stores";
  $joined_order = array(
      'Text',
      'joined_order',
      array(
          'label' => 'Most Joined Stores Tab (order)',
      ),
  );
}



$statisticsElement = array("likeCount" => "Likes", "followCount" => "Followers", "viewCount" => "Views", "commentCount" => "Comments");
$statisticsElementValue = array("viewCount", "likeCount", "followCount", "commentCount");

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
  $statisticsElement['reviewCount'] = "Reviews";
  $statisticsElementValue[] = "reviewCount";
}
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
  $statisticsElement['memberCount'] = "Members";
  $statisticsElementValue = "memberCount";
}
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
  $category_stores_multioptions['member_count'] = 'Members';
}
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
  $category_stores_multioptions['review_count'] = 'Reviews';
}
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
        'title' => 'Store Archives',
        'description' => 'Displays the month-wise archives for the stores posted on your site.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.archives-sitestore',
        'defaultParams' => array(
            'title' => 'Archives',
            'titleCount' => true,
        ),
//        'adminForm' => array(
////          'elements' => array(
////              $showStoreElement,
////          ),
//        ),
    ),
    array(
        'title' => 'Navigation Tabs',
        'description' => 'Displays the Navigation tabs stores having links of Stores Home, Browse Stores, etc. This widget should be placed at the top of Stores Home and Browse Stores stores.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.browsenevigation-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
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
        'title' => 'Categories, Sub-categories and 3<sup>rd</sup> Level-categories',
        'description' => 'Displays the Categories, Sub-categories and 3<sup>rd</sup> Level-categories of stores in an expandable form. Clicking on them will redirect the viewer to the list of stores created in that category.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.categories',
        'defaultParams' => array(
            'title' => 'Categories',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 stores in them? (Note: Selecting "Yes" will display all the categories WITHOUT  the count of the stores created in them if "Browse by Networks" are enabled  from the Global Settings of Stores / Marketplace Plugin and display all the categories with count if selected "No" for Browse by Networks. While selecting "No" here will display categories with count of the stores.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'show2ndlevelCategory',
                    array(
                        'label' => 'Do you want to show sub-categories in this widget?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'show3rdlevelCategory',
                    array(
                        'label' => 'Do you want to show 3rd level category to the viewer? (This setting will depend on above setting.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Profile Stores',
        'description' => 'Displays members\' stores on their profile.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.profile-sitestore',
        'defaultParams' => array(
            'title' => 'Stores',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
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
        'title' => 'Featured Stores Slideshow',
        'description' => 'Displays the Featured Stores in the form of an attractive Slideshow with interactive controls.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.slideshow-sitestore',
        'defaultParams' => array(
            'title' => 'Featured Stores',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of stores to show)',
                        'value' => 10,
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
            ),
        ),
    ),
    array(
        'title' => 'Sponsored Stores Slideshow',
        'description' => 'Displays the Sponsored Stores in the form of an attractive Slideshow with interactive controls.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.sponsored-slideshow-sitestore',
        'defaultParams' => array(
            'title' => 'Sponsored Stores',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of stores to show)',
                        'value' => 10,
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
            ),
        ),
    ),
    array(
        'title' => 'Open a New Store',
        'description' => 'Displays the link to Post a New Store.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.newstore-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $showCreationLink,
            ),
        ),
    ),
    array(
        'title' => 'Most Commented Stores',
        'description' => 'Displays the list of Stores having maximum number of comments.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.mostcommented-sitestore',
        'defaultParams' => array(
            'title' => 'Most Commented Stores',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
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
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Time Period',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Followed Stores',
        'description' => 'Displays a list of stores having maximum number of followers. You can choose the number of entries to be shown.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.mostfollowers-sitestore',
        'defaultParams' => array(
            'title' => 'Most Followed Stores',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
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
                    ),
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
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Time Period',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Most Liked Stores',
        'description' => 'Displays list of stores having maximum number of likes.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.mostlikes-sitestore',
        'defaultParams' => array(
            'title' => 'Most Liked Stores',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
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
                    ),
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
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Time Period',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Popular Store Tags',
        'description' => 'Shows popular tags with frequency.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.tagcloud-sitestore',
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => '',
                        'order' => 1031
                    )
                ),
                array(
                    'hidden',
                    'nomobile',
                    array(
                        'label' => '',
                        'order' => 1032
                    )
                ),
//          array(
//            'hidden',
//            'execute',
//            array(
//              'label' => ''
//            )
//          ),
//          array(
//            'hidden',
//            'cancel',
//            array(
//              'label' => ''
//            )
//          ),
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
        'title' => 'Recent Stores',
        'description' => 'Displays list of recently created Stores.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.recentlyposted-sitestore',
        'defaultParams' => array(
            'title' => 'Recent',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of stores to show)',
                        'value' => 10,
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
        'title' => 'Popular Stores',
        'description' => 'Displays list of popular stores on the site.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.mostviewed-sitestore',
        'defaultParams' => array(
            'title' => 'Most Popular',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of stores to show)',
                        'value' => 10,
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
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Time Period',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Browse Stores: Pinboard View',
        'description' => 'Displays a list of all the stores on site in attractive Pinboard View. You can also choose to display stores based on user’s current location by using the Edit Settings of this widget. It is recommended to place this widget on "Browse Stores\'s Pinboard View" store. ',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.pinboard-browse',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,  
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => 'Show posted by option. (Selecting "Yes" here will display the member\'s name who has created the store.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'showfeaturedLable',
                    array(
                        'label' => 'Do you want “Featured Label” for the Stores to be displayed in block?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showsponsoredLable',
                    array(
                        'label' => 'Do you want “Sponsored Label”  for the Stores to be displayed in block?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'showoptions',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the Stores in this block.',
                        'multiOptions' => $pinboardShowsOptions,
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
                ),
                array(
                    'Select',
                    'detactLocation',
                    array(
                        'label' => 'Do you want to display stores based on user’s current location?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0'
                    )
                ),
                array(
                    'Select',
                    'defaultlocationmiles',
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
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
                array(
                    'Text',
                    'itemWidth',
                    array(
                        'label' => 'One Item Width',
                        'description' => 'Enter the width for each pinboard item.',
                        'value' => 237,
                    )
                ),
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => 'Do you want to display the images without stretching them to the width of each pinboard item?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Stores to show)',
                        'value' => 12,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for the Stores displayed in this block.',
                        'multiOptions' => array("comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
                ),
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => "Enter the truncation limit for the Store Description. (If you want to hide the description, then enter '0'.)",
                        'value' => 100,
                    )
                ),
                array(
                    'Select',
                    'commentSection',
                    array(
                        'label' => 'Do you want to display comments?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Select',
                    'defaultLoadingImage',
                    array(
                        'label' => 'Do you want to show a Loading image when this widget renders on a page?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    )
                ),
// 						array(
// 							'Radio',
// 							'getdirection',
// 							array(
// 									'label' => 'Show get direction link.',
// 									'multiOptions' => array(
// 											1 => 'Yes',
// 											0 => 'No'
// 									),
// 									'value' => 1,
// 							),
// 						),
            ),
        )
    ),
//     array(
//         'title' => 'Store Profile  Cover Photo and Information',
//         'description' => 'Displays the store cover photo with store profile photo, title and various action links that can be performed on the store from their Profile store (Like, Follow, etc.). You can choose various options from the Edit Settings of this widget. This widget should be placed on the Store Profile store.',
//         'category' => 'Stores / Marketplace - Store Profile',
//         'type' => 'widget',
//         'name' => 'sitestore.store-cover-information-sitestore',
//         'defaultParams' => array(
//             'title' => 'Information',
//             'titleCount' => true,
//             'showContent' => $showContent_option
//         ),
//         'adminForm' => array(
//             'elements' => array(
//                 array(
//                     'MultiCheckbox',
//                     'showContent',
//                     array(
//                         'label' => 'Select the information options that you want to be available in this block.',
//                         'multiOptions' => $showContent_timeline,
//                     ),
//                 ), 
//                 array(
//                     'Text',
//                     'columnHeight',
//                     array(
//                         'label' => 'Enter the cover photo height (in px). (Minimum 150 px required.)',
//                         'value' => '300',
//                     )
//                 ),             
//             ),
//         ),
//     ),
    array(
        'title' => 'Browse Stores’ Locations',
        'description' => 'Displays a list of all the stores having location entered corresponding to them on the site. This widget should be placed on Browse Stores’ Locations store.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.browselocation-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//              $showStoreElement,  
// 						array(
// 							'Radio',
// 							'getdirection',
// 							array(
// 									'label' => 'Show get direction link.',
// 									'multiOptions' => array(
// 											1 => 'Yes',
// 											0 => 'No'
// 									),
// 									'value' => 1,
// 							),
// 						),
            ),
        )
    ),
    array(
        'title' => 'Browse Stores',
        'description' => 'Displays a list of all the stores on site. This widget should be placed on the Browse Stores store.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.stores-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_views' => array("0" => "1", "1" => "2", "2" => "3"),
            'layouts_oder' => 1,
            'columnWidth' => 100,
            'statistics' => $statisticsElementValue,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => 'Choose the view types that you want to be available for stores on the stores home and browse stores.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View")
                    ),
                ),
                array(
                    'Radio',
                    'layouts_oder',
                    array(
                        'label' => 'Select a default view type for Stores.',
                        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View")
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => 'Column Width For Grid View.',
                        'value' => '100',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => 'Column Height For Grid View.',
                        'value' => '350',
                    )
                ),
                array(
                    'Text',
                    'turncation',
                    array(
                        'label' => 'Title Truncation Limit For Grid View.',
                        'value' => '40',
                    )
                ),
                array(
                    'Radio',
                    'showlikebutton',
                    array(
                        'label' => 'Do you want to show “Like Button” when users mouse over on Stores in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showfeaturedLable',
                    array(
                        'label' => 'Do you want “Featured Label” for the Stores to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showsponsoredLable',
                    array(
                        'label' => 'Do you want “Sponsored Label”  for the Stores to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showlocation',
                    array(
                        'label' => 'Do you want “Location” of the Stores to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showprice',
                    array(
                        'label' => 'Do you want “Price” of the Stores to be displayed?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showpostedBy',
                    array(
                        'label' => 'Do you want “Posted By” of the Stores to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showdate',
                    array(
                        'label' => 'Do you want “Creation Date” of the Stores to be displayed in grid view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'showContactDetails',
                    array(
                        'label' => 'Do you want “Contact Details” of the Stores to be displayed in list and map view?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Listings in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
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
                    'show_content',
                    array(
                        'label' => 'What do you want for view more content?',
                        'description' => '',
                        'multiOptions' => array(
                            '1' => 'Pagination',
                            '2' => 'Show View More Link at Bottom',
                            '3' => 'Auto Load Stores on Scrolling Down'),
                        'value' => 2,
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        )
    ),
    array(
        'title' => 'Search Stores form',
        'description' => 'Displays the form for searching Stores on the basis of various filters.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.search-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Show Search Form',
                        'multiOptions' => array(
                            'horizontal' => 'Horizontal',
                            'vertical' => 'Vertical',
                        ),
                        'value' => 'vertical'
                    )
                ),
                array(
                    'Radio',
                    'locationDetection',
                    array(
                        'label' => "Allow browser to detect user's current location.",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Random Stores',
        'description' => 'Displays list of Stores randomly.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.random-sitestore',
        'defaultParams' => array(
            'title' => 'Random',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
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
        'title' => 'Sponsored Stores Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the sponsored Stores on the site.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.sponsored-sitestore',
        'defaultParams' => array(
            'title' => 'Sponsored Stores',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of stores to show)',
                        'value' => 4,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Sponsored Carousel Speed',
                        'description' => '(What maximum Carousel Speed should be applied to the sponsored widget?)',
                        'value' => 300,
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
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'description' => '(What maximum limit should be applied to the number of characters in the titles of items in the Sponsored widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.))',
                        'value' => 18,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'AJAX based Stores Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the stores on the site. You can choose to show sponsored / featured in this widget from the settings of this widget. You can place this widget multiple times on a store with different criterion chosen for each placement.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.ajax-carousel-sitestore',
        'defaultParams' => array(
            'title' => 'Stores Carousel',
            'titleCount' => true,
            'statistics' => $statisticsElementValue,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
                $featuredSponsoredElement,
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Listings in this block.',
                        'multiOptions' => $statisticsElement,
                    ),
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'Carousel Type',
                        'multiOptions' => array(
                            '0' => 'Horizontal',
                            '1' => 'Vertical',
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'blockHeight',
                    array(
                        'label' => 'Enter the height of each slideshow item.',
                        'value' => 240,
                    )
                ),
                array(
                    'Text',
                    'blockWidth',
                    array(
                        'label' => 'Enter the width of each slideshow item.',
                        'value' => 150,
                    )
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => $popularity_options,
                        'value' => 'listing_id',
                    )
                ),
                array(
                    'Radio',
                    'featuredIcon',
                    array(
                        'label' => 'Do you want to show the featured icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'sponsoredIcon',
                    array(
                        'label' => 'Do you want to show the sponsored icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of stores to show)',
                        'value' => 4,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Sponsored Carousel Speed',
                        'description' => '(What maximum Carousel Speed should be applied to the sponsored widget?)',
                        'value' => 300,
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
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'description' => '(What maximum limit should be applied to the number of characters in the titles of items in the Sponsored widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.))',
                        'value' => 50,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Store of the Day',
        'description' => 'Displays the Store of the Day as selected by the Admin from the Store of the day section of this plugin.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.item-sitestore',
        'defaultParams' => array(
            'title' => 'Store of the Day',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Recently Viewed Stores',
        'description' => 'Displays list of recently viewed Stores on the site.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.recentview-sitestore',
        'defaultParams' => array(
            'title' => 'Recently Viewed',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
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
        'title' => 'Recently Viewed By Friends',
        'description' => 'Displays list of Stores recently viewed by friends.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.recentfriend-sitestore',
        'defaultParams' => array(
            'title' => 'Recently Viewed By Friends',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
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
        'title' => 'Message for Zero Stores',
        'description' => 'This widget should be placed in the top of the middle column of the Stores Home page.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.zerostore-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
//        'adminForm' => array(
//            'elements' => array(
////                $showStoreElement,
//             ),
//        ),    
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
        'title' => 'Popular Locations',
        'description' => 'Displays list of popular locations of Stores.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.popularlocations-sitestore',
        'defaultParams' => array(
            'title' => 'Popular Locations',
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of locations to show)',
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
            ),
        ),
    ),
    array(
        'title' => 'Store Profile Overview',
        'description' => 'Displays rich overview on Store\'s profile, created by its admin using the editor from Store Dashboard. This should be placed in the Tabbed Blocks area of Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.overview-sitestore',
        'defaultParams' => array(
            'title' => 'Overview',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Store Profile Breadcrumb',
        'description' => 'Displays breadcrumb of the store based on the categories. This widget should be placed on the Store Profile page.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.store-profile-breadcrumb',
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
//    array(
//        'title' => 'Sub Stores of a Store',
//        'description' => 'Displays the sub stores created in the Store which is being viewed currently. This widget should be placed on the Store Profile page.',
//        'category' => 'Stores / Marketplace - Store Profile',
//        'type' => 'widget',
//        'name' => 'sitestore.substore-sitestore',
//        'defaultParams' => array(
//            'title' => 'Sub Stores of a Store',
//            'titleCount' => true,
//        ),
//    ),
//    array(
//        'title' => 'Parent Store of a Sub Store',
//        'description' => 'Displays the parent store in which the currently viewed sub stores is created. This widget should be placed on the Store Profile page.',
//        'category' => 'Stores / Marketplace - Store Profile',
//        'type' => 'widget',
//        'name' => 'sitestore.parentstore-sitestore',
//        'defaultParams' => array(
//            'title' => 'Parent Store of a Sub Store',
//            'titleCount' => true,
//        ),
//    ),
//    array(
//        'title' => "Store Profile 'Save to foursquare' Button",
//        'description' => "This Button will enable store visitors to add the Store\'s place or tip to their foursquare To-Do List. There is also Member Level and Package setting for this button.",
//        'category' => 'Stores / Marketplace - Store Profile',
//        'type' => 'widget',
//        'name' => 'sitestore.foursquare-sitestore',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
    array(
        'title' => 'Store Profile Social Share Buttons',
        'description' => "Contains Social Sharing buttons and enables users to easily share Stores on their favorite Social Networks. This widget should be placed on the Store View Store. You can customize the code for social sharing buttons from Global Settings of this plugin by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.socialshare-sitestore',
        'defaultParams' => array(
            'title' => 'Social Share',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'sitestore_store',
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Hidden',
                    'nomobile',
                    array(
                        'label' => '',
                        'order' => 1033
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Store Profile Title',
        'description' => 'Displays the Title of the Store. This widget should be placed on the Store Profile, in the middle column at the top.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.title-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Store Profile Info',
        'description' => 'This widget forms the Info tab on the Store Profile and displays the information of the Store. It should be placed in the Tabbed Blocks area of the Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestore.info-sitestore',
        'defaultParams' => array(
            'title' => 'Info',
            'titleCount' => true
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the options that you want to show in Basic Information block.',
                        'multiOptions' => array(
                            "posted_by" => "Posted By",
                            "posted" => "Posted",
                            "last_update" => "Last Updated",
                            "members" => "Members",
                            "comments" => "Comments",
                            "views" => "Views",
                            "likes" => "Likes",
                            "followers" => "Followers",
                            "category" => "Category",
                            "tags" => "Tags",
                            "price" => "Price",
                            "location" => "Location",
                            "description" => "Description"
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Store Profile Information Store',
        'description' => 'Displays the owner, category, tags, views and other information about a Store. This widget should be placed on the Store Profile in the left column.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.information-sitestore',
        'defaultParams' => array(
            'title' => 'Information',
            'titleCount' => true,
            'showContent' => array("ownerPhoto", "ownerName", "modifiedDate", "viewCount", "likeCount", "commentCount", "tags", "location", "price", "memberCount", "followerCount", "categoryName", "stores")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array("ownerPhoto" => "Store Owner's Photo", "ownerName" => "Owner's Name", "modifiedDate" => "Modified Date", "viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "tags" => "Tags", "location" => "Location", "price" => "Price", "memberCount" => 'Member', "followerCount" => 'Follower', "categoryName" => "Category", "stores" => "Stores"),
                    ),
                ),
            ),
        ),
    ),
    
     array(
        'title' => 'Terms & Conditions',
        'description' => 'Displays the terms and conditions of a Store. This widget must be placed on the Store Profile at left column.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.store-termsconditions',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Store Profile Photo',
        'description' => 'Displays the main cover photo of a Store. This widget must be placed on the Store Profile at the top of left column.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.mainphoto-sitestore',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Store Profile Map',
        'description' => 'This widget forms the Map tab on the Store Profile. It displays the map showing the Store position as well as the location details of the store. It should be placed in the Tabbed Blocks area of the Store Profile. This feature will be available to Stores based on their Package and Member Level of their owners.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.location-sitestore',
        'defaultParams' => array(
            'title' => 'Map',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Store Profile Options',
        'description' => 'Displays the various action link options to users viewing a Store. This widget should be placed on the Store Profile in the left column, below the Store profile photo.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.options-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Store Profile Owner Store Tags',
        'description' => 'Displays all the tags chosen by the Store owner for his Stores. This widget should be placed on the Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.tags-sitestore',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Store Profile Owner Stores',
        'description' => 'Displays list of other stores owned by the store owner.This widget should be placed on Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.userstore-sitestore',
        'defaultParams' => array(
            'title' => '',
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
                    'popularity',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => array(
                            'view_count' => 'Views',
                            'like_count' => 'Likes',
                        ),
                        'value' => 'view_count',
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
        'title' => 'Store Profile About Store',
        'description' => 'Displays the About Us information for stores. This widget should be placed on the Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.write-store',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Content Profile: Follow Button',
        'description' => 'This is the Follow Button to be placed on the Content Profile page. It enables users to Follow the content being currently viewed.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'seaocore.seaocore-follow',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Content Profile: Like Button for Content',
        'description' => 'This is the Like Button to be placed on the Content Profile page. It enables users to Like the content being currently viewed. The best place to put this widget is right above the Tabbed Blocks on the Content Profile page.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'seaocore.like-button',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Store Profile You May Also Like',
        'description' => 'Displays list of stores that might be liked by user based on the store being currently viewed.This widget should be placed on the Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.suggestedstore-sitestore',
        'defaultParams' => array(
            'title' => 'You May Also Like',
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
        'title' => 'Content Profile: Content Likes',
        'description' => 'Displays the users who have liked the content being currently viewed. This widget should be placed on the  Content Profile page.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'seaocore.people-like',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of users to show)',
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
            ),
        ),
    ),
//    array(
//        'title' => 'Store Profile page Insights',
//        'description' => 'Displays the insights of a Store to its Store Admins. These insights include metrics like views, likes, comments and active users of the Store. This widget should be placed on the Store Profile.',
//        'category' => 'Stores / Marketplace - Store Profile',
//        'type' => 'widget',
//        'name' => 'sitestore.insights-sitestore',
//        'defaultParams' => array(
//            'title' => 'Insights',
//            'titleCount' => true,
//        ),
//    ),
    array(
        'title' => 'Store Profile Alternate Thumb Photo',
        'description' => 'Displays the thumb photo of a Store. This works as an alternate profile photo when you have set the layout of Store Profile to be tabbed, from the Store Layout Settings, and have integrated with the "Advertisements / Community Ads Plugin" by SocialEngineAddOns. In that case, the left column of the Store Profile having the main profile photo gets hidden to accomodate Ads. This widget must be placed on the Store Profile at the top of middle column.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.thumbphoto-sitestore',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showTitle',
                    array(
                        'label' => 'Show Store Profile Title.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Store Profile Apps Links',
        'description' => "Displays the Apps related links like Documents, Form, Photos, Poll, etc on Store Profile, depending on the Stores related apps installed on your site. This widget should be placed on the Store Profile in the left column.",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.widgetlinks-sitestore',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
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
        'title' => 'Store Profile Featured Store Admins',
        'description' => "Displays the Featured Admins of a store. This widget should be placed on the Store Profile.",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.featuredowner-sitestore',
        'defaultParams' => array(
            'title' => "Store admins",
            'titleCount' => "",
        ),
    ),
array(
        'title' => 'Horizontal Search Stores Form',
        'description' => "This widget searches over Store Titles, Locations and Categories. This widget should be placed in full-width / extended column. Multiple settings are available in the edit settings section of this widget.",
        'category' => 'Pages',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestore.horizontal-searchbox-sitestore',
        'defaultParams' => array(
            'title' => "Search",
            'titleCount' => "",
            'loaded_by_ajax' => 0
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'locationDetection',
                    array(
                        'label' => "Allow browser to detect user's current location.",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'formElements',
                    array(
                        'label' => 'Choose the options that you want to be displayed in this block.(Note:Proximity Search will not display if location field will be disabled.)',
                        'multiOptions' => array("textElement" => "Auto-suggest for Keywords", "categoryElement" => "Category Filtering", "locationElement" => "Location field", "locationmilesSearch" => "Proximity Search"),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'categoriesLevel',
                    array(
                        'label' => 'Select the category level belonging to which categories will be displayed in the category drop-down of this widget.',
                        'multiOptions' => array("category" => "Category", "subcategory" => "Sub-category", "subsubcategory" => "3rd level category"),
                    ),
                ),
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => 'Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 stores in them?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'textWidth',
                    array(
                        'label' => 'Width for AutoSuggest',
                        'value' => 275,
                    )
                ),
                array(
                    'Text',
                    'locationWidth',
                    array(
                        'label' => 'Width for Location field',
                        'value' => 250,
                    )
                ),
                array(
                    'Text',
                    'locationmilesWidth',
                    array(
                        'label' => 'Width for Proximity Search field',
                        'value' => 125,
                    )
                ),
                array(
                    'Text',
                    'categoryWidth',
                    array(
                        'label' => 'Width for Category Filtering',
                        'value' => 150,
                    )
                ),
               array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
            ),
        ),
    ),        
    array(
        'title' => 'AJAX Search for Stores',
        'description' => "This widget searches over Store Titles via AJAX. The search interface is similar to Facebook search.",
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.searchbox-sitestore',
        'defaultParams' => array(
            'title' => "Search",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
                array(
                    'Select',
                    'category_id',
                    array(
                        'label' => 'Category',
                        'multiOptions' => $categories_prepared,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Alphabetic Filtering of Stores',
        'description' => "This widget enables users to alphabetically filter the stores on your site by clicking on the desired alphabet. The widget shows all the alphabets for filtering.",
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.alphabeticsearch-sitestore',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Categorically Popular Stores',
        'description' => 'This attractive widget categorically displays the most popular stores on your site. It displays 5 Stores for each category. From the edit popup of this widget, you can choose the number of categories to show, criteria for popularity and the duration for consideration of popularity.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.category-stores-sitestore',
        'defaultParams' => array(
            'title' => 'Popular Stores',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Category Count',
                        'description' => 'No. of Categories to show. Enter 0 for showing all categories.',
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'storeCount',
                    array(
                        'label' => 'Stores Count per Category',
                        'description' => 'No. of Stores to be shown in each Category.',
                        'value' => 5,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => $category_stores_multioptions,
                        'value' => 'view_count',
// 											'onchange'=>'javascript:if($("popularity").value=="view_count"){ $("interval-wrapper").style.display = "none";}else{$("interval-wrapper").style.display = "block"; }',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to all Popularity Criteria except Views.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Select',
                    'columnCount',
                    array(
                        'label' => 'Select categories to be displayed in a row.',
                        'multiOptions' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'),
                        'value' => '2',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Store Profile Contact Details',
        'description' => "Displays the Contact Details of a store. This widget should be placed on the Store Profile.",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.contactdetails-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'contacts' => array("0" => "1", "1" => "2", "2" => "3"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'contacts',
                    array(
                        'label' => 'Select the contact details you want to display',
                        'multiOptions' => array("1" => "Phone", "2" => "Email", "3" => "Website"),
                    ),
                ),
                array(
                    'Radio',
                    'emailme',
                    array(
                        'label' => 'Do you want users to send emails to Stores via a customized pop up when they click on "Email Me" link?',
                        'multiOptions' => array(
                            1 => 'Yes, open customized pop up',
                            0 => 'No, open browser`s default pop up'
                        ),
                        'value' => '0'
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Store Profile Render Layout',
        'description' => "Displays the layout of the store when site header and footer are not rendered.",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.onrender-sitestore',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
    array(
        'title' => 'Popular / Recent / Random / Location Based Stores: Pinboard View',
        'description' => 'Displays stores based on popularity criteria, location and other settings that you want to choose for this widget in attractive Pinboard View. You can place this widget multiple times with different popularity criterion chosen for each placement. You can also choose to display stores based on user’s current location by using the Edit Settings of this widget.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestore.pinboard-stores',
        'defaultParams' => array(
            'title' => 'Recent',
            'statistics' => array("likeCount", "commentCount"),
            'show_buttons' => array("comment", "like", 'share', 'facebook', 'twitter', 'pinit')
        ),
        'adminForm' => array(
            'elements' => array(
//                $showStoreElement,
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
                    'fea_spo',
                    array(
                        'label' => 'Show Stores',
                        'multiOptions' => array(
                            '' => '',
                            'featured' => 'Featured Only',
                            'sponsored' => 'Sponsored Only',
                            'fea_spo' => 'Both Featured and Sponsored',
                        ),
                        'value' => '',
                    )
                ),
                array(
                    'Select',
                    'detactLocation',
                    array(
                        'label' => 'Do you want to display stores based on user’s current location? (Note:- For this you must be enabled the auto-loading.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0'
                    )
                ),
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
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => 'Popularity Criteria',
                        'multiOptions' => $pinboardPopularityOptions,
                        'value' => 'store_id',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity Criteria:  Most Liked, Most Commented, Most Rated and Most Recent.)',
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => 'Show posted by option. (Selecting "Yes" here will display the member\'s name who has created the store.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'showoptions',
                    array(
                        'label' => 'Choose the options that you want to be displayed for the Stores in this block.',
                        'multiOptions' => $pinboardShowsOptions,
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
                ),
                array(
                    'Select',
                    'autoload',
                    array(
                        'label' => 'Do you want to enable auto-loading of old pinboard items when users scroll down to the bottom of this store?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Text',
                    'itemWidth',
                    array(
                        'label' => 'One Item Width',
                        'description' => 'Enter the width for each pinboard item.',
                        'value' => 237,
                    )
                ),
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => 'Do you want to display the images without stretching them to the width of each pinboard item?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of Stores to show)',
                        'value' => 12,
                    )
                ),
                array(
                    'Text',
                    'noOfTimes',
                    array(
                        'label' => 'Auto-Loading Count',
                        'description' => 'Enter the number of times that auto-loading of old pinboard items should occur on scrolling down. (Select 0 if you do not want such a restriction and want auto-loading to occur always. Because of auto-loading on-scroll, users are not able to click on links in footer; this setting has been created to avoid this.)',
                        'value' => 0,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => 'Choose the action links that you want to be available for the Stores displayed in this block.',
                        'multiOptions' => array("comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
                ),
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => "Enter the truncation limit for the Store Description. (If you want to hide the description, then enter '0'.)",
                        'value' => 100,
                    )
                ),
                array(
                    'Select',
                    'defaultLoadingImage',
                    array(
                        'label' => 'Do you want to show a Loading image when this widget renders on a page?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '1'
                    )
                ),
            ),
        ),
    ),
//    
//        array(
//        'title' => 'Store Home: Pinboard View',
//        'description' => 'Displays stores in Pinboard View on the Stores Home store. Multiple settings are available to customize this widget.',
//        'category' => 'Stores / Marketplace - Stores',
//        'type' => 'widget',
//        'autoEdit' => true,
//        'name' => 'sitestore.pinboard-stores-sitestore',
//        'defaultParams' => array(
//            'title' => 'Recent',
//            'statistics' => array("likeCount", "reviewCount"),
//            'show_buttons' => array("comment", "like", 'share', 'facebook', 'twitter', 'pinit')
//        ),
//        'adminForm' => array(
//            'elements' => array(
//                //$listingTypeCategoryElement,
//               // $ratingTypeElement,
//                $featuredSponsoredElement,
//                array(
//                    'Select',
//                    'popularity',
//                    array(
//                        'label' => 'Popularity Criteria',
//                        'multiOptions' => $popularity_options,
//                        'value' => 'store_id',
//                    )
//                ),
//                array(
//                    'Select',
//                    'interval',
//                    array(
//                        'label' => 'Popularity Duration (This duration will be applicable to these Popularity Criteria:  Most Liked, Most Commented, Most Rated and Most Recent.)',
//                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall',
//                        'value' => 'overall',
//                    )
//                ),
//                //$categoryElement,
//                //$hiddenCatElement,
//                //$hiddenSubCatElement,
//                //$hiddenSubSubCatElement,
//                //$statisticsElement,
//                array(
//                    'Radio',
//                    'postedby',
//                    array(
//                        'label' => 'Show posted by option. (Selecting "Yes" here will display the member\'s name who has created the listing.)',
//                        'multiOptions' => array(
//                            1 => 'Yes',
//                            0 => 'No')
//                        ),
//                        'value' => '1',
//                    )
//                ),
//                array(
//                    'Select',
//                    'autoload',
//                    array(
//                        'label' => 'Do you want to enable auto-loading of old pinboard items when users scroll down to the bottom of this store?',
//                        'multiOptions' => array(
//                            1 => 'Yes',
//                            0 => 'No')
//                        ),
//                        'value' => '1'
//                    )
//                ),
//                array(
//                    'Text',
//                    'itemWidth',
//                    array(
//                        'label' => 'One Item Width',
//                        'description' => 'Enter the width for each pinboard item.',
//                        'value' => 237,
//                    )
//                ),
//                array(
//                    'Radio',
//                    'withoutStretch',
//                    array(
//                        'label' => 'Do you want to display the images without stretching them to the width of each pinboard item?',
//                        'multiOptions' => array(
//                            1 => 'Yes',
//                            0 => 'No')
//                        ),
//                        'value' => '0',
//                    )
//                ),
//                array(
//                    'Text',
//                    'itemCount',
//                    array(
//                        'label' => 'Count',
//                        'description' => '(number of Listings to show)',
//                        'value' => 3,
//                    )
//                ),
//                array(
//                    'Text',
//                    'noOfTimes',
//                    array(
//                        'label' => 'Auto-Loading Count',
//                        'description' => 'Enter the number of times that auto-loading of old pinboard items should occur on scrolling down. (Select 0 if you do not want such a restriction and want auto-loading to occur always. Because of auto-loading on-scroll, users are not able to click on links in footer; this setting has been created to avoid this.)',
//                        'value' => 0,
//                    )
//                ),
//                array(
//                    'MultiCheckbox',
//                    'show_buttons',
//                    array(
//                        'label' => 'Choose the action links that you want to be available for the Listings displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)',
//                        'multiOptions' => array("comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
//                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
//                    ),
//                ),
//                array(
//                    'Text',
//                    'truncationDescription',
//                    array(
//                        'label' => "Enter the trucation limit for the Listing Description. (If you want to hide the description, then enter '0'.)"),
//                        'value' => 100,
//                    )
//                ),
//            ),
//        ),
//    ),
    array(
        'title' => 'Search Stores Location Form',
        'description' => 'Displays the form for searching Stores corresponding to location on the basis of various filters.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.location-search',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'street',
                    array(
                        'label' => 'Show street option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'city',
                    array(
                        'label' => 'Show city option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'state',
                    array(
                        'label' => 'Show state option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'country',
                    array(
                        'label' => 'Show country option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Ajax Based Recently Posted, Popular, Random, Featured and Sponsored Stores',
        'description' => "Displays the recently posted, popular, random, featured and sponsored stores in a block in separate ajax based tabs respectively.",
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.recently-popular-random-sitestore',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
            'layouts_views' => array("0" => "1", "1" => "2", "2" => "3"),
            'layouts_oder' => 1,
            'layouts_tabs' => $layouts_tabs,
            'recent_order' => 1,
            'popular_order' => 2,
            'random_order' => 3,
            'featured_order' => 4,
            'sponosred_order' => 5,
            'list_limit' => 10,
            'grid_limit' => 15,
            'columnWidth' => '188',
            'columnHeight' => '350',
            'statistics' => $statisticsElementValue,
        ),
        'adminForm' => 'Sitestore_Form_Admin_Widgets_AjaxBasedRecentlyPosted',
    ),
    array(
        'title' => 'Search Stores Form (Pinboard Results)',
        'description' => 'Displays the form for searching Stores based on location and various filters. (Note: This widget supports both Pinboard and normal layouts for search results.)',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.horizontal-search',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'browseredirect',
                    array(
                        'label' => 'Choose the layout of browse store where you want to display the search results. (If you are placing this widget on a store having Pinboard layout, then choose Pinboard layout below.)',
                        'multiOptions' => array(
                            'pinboard' => 'Pinboard Layout',
                            'default' => 'Normal Layout'
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Radio',
                    'street',
                    array(
                        'label' => 'Show street option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'city',
                    array(
                        'label' => 'Show city option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'state',
                    array(
                        'label' => 'Show state option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'country',
                    array(
                        'label' => 'Show country option.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'View my Stores',
        'description' => 'Displays the link to my stores.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.view-my-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ), array(
        'title' => 'Store Information',
        'description' => 'Displays the links to various stores.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestore.store-information',
        'defaultParams' => array(
            'title' => 'Store Information',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => $view->translate('Number of contents'),
                        'description' => $view->translate('Number of contents, you want to show'),
                        'value' => 5,
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => 'Select the information options that you want to be available in this block.',
                        'multiOptions' => array("like" => "Like", "follow" => 'Follow', "sales" => "Sales", "review" => "Review", "rating" => "Rating", "contact" => "Contact"),
                    ),
                ),
            ),
        ),
    ),
);

if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
  $ads_Array = array(
      array(
          'title' => 'Store Ads Widget',
          'description' => 'Displays community ads in various widgets and view stores of this plugin.',
          'category' => 'Stores / Marketplace - Stores',
          'type' => 'widget',
          'name' => 'sitestore.store-ads',
          'defaultParams' => array(
              'title' => '',
              'titleCount' => true,
          ),
          ));
}


if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
  $joined_array = array(
      array(
          'title' => 'Joined / Owned Stores',
          'description' => 'Displays stores administered and joined by members on their profiles. This widget should be placed on the Member Profile page.',
          'category' => 'Stores / Marketplace - Stores',
          'type' => 'widget',
          'name' => 'sitestore.profile-joined-sitestore',
          'defaultParams' => array(
              'title' => 'Stores',
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
                          'value' => 1,
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

$fbstore_sitestore_Array = array(
    array(
        'title' => 'Store Profile: Facebook Like Box',
        'description' => 'This widget contains the Facebook Like Box which enables Store Admins to gain Likes for their Facebook Store from this website. The edit popup contains the settings to customize the Facebook Like Box. This widget should be placed on the Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestore.fblikebox-sitestore',
        'defaultParams' => array(
            'title' => ''
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    "Text",
                    "title",
                    array(
                        'label' => 'Title',
                        'value' => '',
                    )
                ),
                array(
                    "Text",
                    "fb_width",
                    array(
                        'label' => 'Width',
                        'description' => 'Width of the Facebook Like Box in pixels.',
                        'value' => '220',
                    )
                ),
                array(
                    "Text",
                    "fb_height",
                    array(
                        'label' => 'Height',
                        'description' => 'Height of the Facebook Like Box in pixels (optional).',
                        'value' => '588',
                    )
                ),
                array(
                    "Select",
                    "widget_color_scheme",
                    array(
                        'label' => 'Color Scheme',
                        'description' => 'Color scheme of the Facebook Like Box in pixels.',
                        'multiOptions' => array('light' => 'light', 'dark' => 'dark')
                    )
                ),
                array(
                    "MultiCheckbox",
                    "widget_show_faces",
                    array(
                        //'label' => 'Show Profile Photos in this plugin.',
                        'description' => 'Show Faces',
                        'multiOptions' => array('1' => 'Show profile photos of users who like the linked Facebook Store in the Facebook Like Box.')
                    )
                ),
                array(
                    "Text",
                    "widget_border_color",
                    array(
                        'label' => 'Border Color',
                        'description' => 'Border color of the Facebook Like Box'
                    )
                ),
                array(
                    "MultiCheckbox",
                    "show_stream",
                    array(
                        'description' => 'Stream',
                        'multiOptions' => array('1' => 'Show the Facebook Store profile stream for the public feeds in the Facebook Like Box.'),
                    )
                ),
                array(
                    "MultiCheckbox",
                    "show_header",
                    array(
                        'description' => 'Header',
                        'multiOptions' => array('1' => "Show the 'Find us on Facebook' bar at top. Only shown when either stream or profile photos are present."),
                    )
                ),
            )
        )
        ));

if (!empty($joined_array)) {
  $final_array = array_merge($final_array, $joined_array);
}
if (!empty($ads_Array)) {
  $final_array = array_merge($final_array, $ads_Array);
}
if (!empty($fbstore_sitestore_Array)) {
  $final_array = array_merge($final_array, $fbstore_sitestore_Array);
}
return $final_array;
?>
