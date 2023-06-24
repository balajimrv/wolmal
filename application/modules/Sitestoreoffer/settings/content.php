<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
//$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.isActivate', 0);
//if (empty($isActive)) {
//  return;
//}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$categories = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories();
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
}

$popularity_options = array(
    'view_count' => 'Most Viewed',
    'like_count' => 'Most Liked',
    'comment_count' => 'Most Commented',
    'popular' => 'Most Claimed',
);

$statisticsElement = array(
    'MultiCheckbox',
    'statistics',
    array(
        
        'label' => $view->translate('Choose the statistics that you want to be displayed for the Coupns in this block.'),
        'multiOptions' => array("startdate" => "Start Date", "enddate" => "End Date", "couponcode" => "Coupon Code", 'discount' => 'Discount', 'couponurl' => 'Coupon Url', 'minpurchase' => 'Minimum Purchase', 'claim' => 'Coupon Left Count', 'expire' => 'Expired'),
    ),
);

return array(
    array(
        'title' => 'Store Profile Coupons',
        'description' => 'This widget forms the Coupons tab on the Store Profile and displays the coupons of the Store. It should be placed in the Tabbed Blocks area of the Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestoreoffer.profile-sitestoreoffers',
        'defaultParams' => array(
            'title' => 'Coupons',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $statisticsElement
            ),
        ),
    ),
    array(
        'title' => 'Latest Store Coupons',
        'description' => 'Displays the latest Store Coupons that have been created. You can choose the number of entries to be shown.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestoreoffer.sitestore-latestoffer',
        'defaultParams' => array(
            'title' => 'Latest Store Coupons',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of coupons to show)',
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
                $statisticsElement,
            ),
        ),
    ),
    array(
        'title' => 'Hot Store Coupons',
        'description' => 'Displays Store Coupons that have been marked as Hot. You can choose the number of entries to be shown',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestoreoffer.sitestore-hotoffer',
        'defaultParams' => array(
            'title' => 'Hot Store Coupons',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of coupons to show)',
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
                $statisticsElement,
            ),
        ),
    ),
    array(
        'title' => 'Stores: Browse Coupons',
        'description' => 'Displays the list of Coupons from Stores created on your community. This widget should be placed in the widgetized Store Coupons store. Results from the Search Store Coupons form are also shown here.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestoreoffer.sitestore-offer',
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
                        'description' => '(number of coupons to show)',
                        'value' => 20,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
                $statisticsElement,
            ),
        ),
    ),
    array(
        'title' => 'Available Coupons',
        'description' => 'Displays the Store Coupons based on their ending dates, in 3 tabs: ‘This Week’, ‘This Month’ and ‘Overall’. Users can see more coupons right within that widget. You can choose the number of entries to be shown.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestoreoffer.sitestore-dateoffer',
        'defaultParams' => array(
            'title' => 'Available Coupons',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of coupons to show)',
                        'value' => 5,
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
                $statisticsElement,
            ),
        ),
    ),
    array(
        'title' => 'Search Store Coupons form',
        'description' => 'Displays the form for searching Store Coupons on the basis of various filters. You can edit the fields to be available in this form.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestoreoffer.search-sitestoreoffer',
        'defaultParams' => array(
            'title' => '',
            'search_column' => array("0" => "1", "1" => "2", "2" => "3", "3" => "4"),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'search_column',
                    array(
                        'label' => 'Choose the fields that you want to be available in the Search Store Coupons form widget.',
                        'multiOptions' => array("1" => "Browse By", "2" => "Store Title", "3" => "Coupon Title", "4" => "Store Category"),
                    ),
                ),
            ),
        )
    ),
//    array(
//        'title' => 'Sponsored Coupons',
//        'description' => 'Displays the Coupons from Paid Stores. You can choose the number of entries to be shown.',
//        'category' => 'Stores / Marketplace - Stores',
//        'type' => 'widget',
//        'name' => 'sitestoreoffer.sitestore-sponsoredoffer',
//        'defaultParams' => array(
//            'title' => 'Sponsored Coupons',
//            'titleCount' => true,
//        ),
//        'adminForm' => array(
//            'elements' => array(
//                array(
//                    'Text',
//                    'itemCount',
//                    array(
//                        'label' => 'Count',
//                        'description' => '(number of coupons to show)',
//                        'value' => 3,
//                        'validators' => array(
//                            array('Int', true),
//                            array('GreaterThan', true, array(0)),
//                        ),
//                    ),
//                ),
//                array(
//                    'Select',
//                    'category_id',
//                    array(
//                        'label' => 'Category',
//                        'multiOptions' => $categories_prepared,
//                    )
//                ),
//                $statisticsElement,
//            ),
//        ),
//    ),
    array(
        'title' => 'Store Coupon View',
        'description' => "This widget should be placed on the Store Coupon View Page.",
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestoreoffer.offer-content',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                $statisticsElement,
            ),
        ),
      ),
        array(
            'title' => 'Popular / Viewed / Liked Store Coupons',
            'description' => 'Displays Store Coupons based on the Popularity Criteria and other settings that you choose for this widget. You can place this widget multiple times on a store with different popularity criterion chosen for each placement.',
            'category' => 'Stores / Marketplace - Stores',
            'type' => 'widget',
            'autoEdit' => true,
            'name' => 'sitestoreoffer.offers-sitestoreoffers',
            'defaultParams' => array(
                'title' => 'Coupons',
                'titleCount' => true,
            ),
            'adminForm' => array(
                'elements' => array(
                    array(
                        'Text',
                        'itemCount',
                        array(
                            'label' => 'Count',
                            'description' => '(number of Coupons to show)',
                            'value' => 3,
                        )
                    ),
                    array(
                        'Select',
                        'popularity',
                        array(
                            'label' => 'Popularity Criteria',
                            'multiOptions' => $popularity_options,
                            'value' => 'view_count',
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
                    $statisticsElement,
                ),
            ),
        ),
        array(
            'title' => 'Store’s Coupon of the Day',
            'description' => 'Displays the Coupon of the Day as selected by the Admin from the Coupons >> Widget Settings section.',
            'category' => 'Stores / Marketplace - Stores',
            'type' => 'widget',
            'name' => 'sitestoreoffer.offer-of-the-day',
            'defaultParams' => array(
                'title' => 'Coupon of the Day'
            ),
            'adminForm' => array(
                'elements' => array(
                        $statisticsElement
                ),
            ),
         ),
            array(
                'title' => 'Browse Coupons',
                'description' => 'Displays the link to view Store’s Coupons Browse store.',
                'category' => 'Stores / Marketplace - Stores',
                'type' => 'widget',
                'name' => 'sitestoreoffer.sitestoreofferlist-link',
                'defaultParams' => array(
                    'title' => '',
                    'titleCount' => true,
                ),
                'adminForm' => array(
                    'elements' => array(
                        $statisticsElement,
                    ),
                ),
             ),
                array(
                    'title' => 'Store’s Hot Coupons Slideshow',
                    'description' => 'Displays hot coupons in an attractive slideshow. You can set the count of the number of coupons to show in this widget. If the total number of coupons selected as hot are more than that count, then the coupons to be displayed will be sequentially picked up.',
                    'category' => 'Stores / Marketplace - Stores',
                    'type' => 'widget',
                    'name' => 'sitestoreoffer.hot-offers-slideshow',
                    'isPaginated' => true,
                    'defaultParams' => array(
                        'title' => 'Hot Coupons',
                        'itemCountPerStore' => 10,
                    ),
                    'adminForm' => array(
                        'elements' => array(
                            array(
                                'Select',
                                'category_id',
                                array(
                                    'label' => 'Category',
                                    'multiOptions' => $categories_prepared,
                                )
                            ),
                            $statisticsElement,
                        )
                    ),
                ),
            array(
                'title' => 'Store’s Hot Coupons Carousel',
                'description' => 'This widget contains an attractive AJAX based carousel, showcasing the hot coupons on the site. Multiple settings of this widget makes it highly configurable.',
                'category' => 'Stores / Marketplace - Stores',
                'type' => 'widget',
                'name' => 'sitestoreoffer.hot-offers-carousel',
                'defaultParams' => array(
                    'title' => 'Hot Coupons',
                ),
                'adminForm' => array(
                    'elements' => array(
                        array(
                            'Select',
                            'category_id',
                            array(
                                'label' => 'Category',
                                'multiOptions' => $categories_prepared,
                            )
                        ),
                        array(
                            'Radio',
                            'vertical',
                            array(
                                'label' => 'Carousel Type',
                                'multiOptions' => array(
                                    '0' => 'Horizontal',
                                    '1' => 'Vertical',
                                ),
                                'value' => 0,
                            )
                        ),
                        array(
                            'Text',
                            'inOneRow',
                            array(
                                'label' => 'Coupons in a Row',
                                'description' => '(number of coupons to show in one row. Note: This field is applicable only when you have selected ‘Horizontal’ in ‘Carousel Type’ field.)',
                                'value' => 3,
                            )
                        ),
                        array(
                            'Text',
                            'noOfRow',
                            array(
                                'label' => 'Rows',
                                'description' => '(number of rows in one view)',
                                'value' => 2,
                            )
                        ),
                        array(
                            'Text',
                            'interval',
                            array(
                                'label' => 'Speed',
                                'description' => '(transition interval between two slides in millisecs)',
                                'value' => 250,
                            )
                        ),
                        $statisticsElement,
                    ),
                ),
            ),
            array(
                'title' => 'Store’s Ajax based Tabbed widget for Coupons',
                'description' => 'Displays the Upcoming, Most Liked, Most Viewed, Most Commented, Hot and Featured Coupons in separate AJAX based tabs. Settings for this widget are available in the Coupons >> Tabbed Coupons Widget section.',
                'category' => 'Stores / Marketplace - Stores',
                'type' => 'widget',
                'name' => 'sitestoreoffer.list-offers-tabs-view',
                'defaultParams' => array(
                    'title' => 'Coupons',
                    'margin_photo' => 12,
                    'showViewMore' => 1
                ),
                'adminForm' => array(
                    'elements' => array(
                        array(
                            'Text',
                            'margin_photo',
                            array(
                                'label' => 'Horizontal Margin between Elements',
                                'description' => '(Horizontal margin in px between consecutive elements in this widget. You might want to change this value if the content of this widget is not coming properly on your site because of the column width in your theme.)',
                                'value' => 12,
                            )
                        ),
                        array(
                            'Radio',
                            'showViewMore',
                            array(
                                'label' => 'Show "View More" link',
                                'multiOptions' => array(
                                    '1' => 'Yes',
                                    '0' => 'No',
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
                        $statisticsElement,
                    ),
                ),
            ),
        array(
            'title' => 'Top Creators : Store Coupons',
            'description' => 'Displays the Stores which have the most number of Store Coupons added in them. Motivates Store Admins to add more content on your website.',
            'category' => 'Stores / Marketplace - Stores',
            'type' => 'widget',
            'name' => 'sitestoreoffer.topcreators-sitestoreoffer',
            // 'isPaginated' => true,
            'defaultParams' => array(
                'title' => 'Top Creators',
            ),
            'adminForm' => array(
                'elements' => array(
                    array(
                        'Text',
                        'itemCount',
                        array(
                            'label' => 'Count',
                            'description' => '(number of elements to show)',
                            'value' => 5,
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
                    $statisticsElement,
                ),
            ),
            'requirements' => array(
                'subject' => 'sitestoreoffer',
            ),
        ),
    );
?>