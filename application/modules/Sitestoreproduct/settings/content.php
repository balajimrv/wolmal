<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.video');
$storeCategoriesTable = Engine_Api::_()->getDbTable('categories', 'sitestore');
$storeCategories = $storeCategoriesTable->getCategories();
if (count($storeCategories) != 0) {
  $storeCategories_prepared[0] = "";
  foreach ($storeCategories as $category) {
    $storeCategories_prepared[$category->category_id] = $category->category_name;
  }
  $detactLocationElement = array(
      'Select',
      'detactLocation',
      array(
          'label' => 'Do you want to display products based on user’s current location?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => '0'
      )
  );
  $truncationLocationElement = array(
      'Text',
      'truncationLocation',
      array(
          'label' => 'Truncation Limit of Location (Depend on Location)',
          'value' => 50,
      )
  );
  $defaultLocationDistanceElement = array(
      'Select',
      'defaultLocationDistance',
      array(
          'label' => "Choose the miles within which products will be displayed.",
          'multiOptions' => array(
              '0' => '',
              '1' => '1 Mile',
              '2' => '2 Miles',
              '5' => '5 Miles',
              '10' => '10 Miles',
              '20' => '20 Miles',
              '50' => '50 Miles',
              '100' => '100 Miles',
              '250' => '250 Miles',
              '500' => '500 Miles',
              '750' => '750 Miles',
              '1000' => '1000 Miles',
          ),
          'value' => '1000'
      )
  );
  $storeCategoryElement = array(
      'Select',
      'category_id',
      array(
          'label' => 'Category',
          'multiOptions' => $storeCategories_prepared,
          'RegisterInArrayValidator' => false,
          'onchange' => 'addOptions(this.value, "cat_dependency", "subcategory_id", 0); setHiddenValues("category_id")'
  ));
  $storeSubCategoryElement = array(
      'Select',
      'subcategory_id',
      array(
          'RegisterInArrayValidator' => false,
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => 'application/modules/Sitestoreproduct/views/scripts/_storeCategory.tpl',
                      'class' => 'form element')))
  ));
}
$category_products_multioptions = array(
    'view_count' => $view->translate('Views'),
    'like_count' => $view->translate('Likes'),
    'comment_count' => $view->translate('Comments'),
    'review_count' => $view->translate('Reviews'),
);
//CHECK IF FACEBOOK PLUGIN IS ENABLE
$fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
if (!empty($fbmodule) && !empty($fbmodule->enabled) && $fbmodule->version > '4.2.7p1') {
  $show_like_button = array(
      '1' => $view->translate('Yes, show SocialEngine Core Like button'),
      '2' => $view->translate('Yes, show Facebook Like button'),
      '0' => $view->translate('No'),
  );
  $default_value = 2;
} else {
  $show_like_button = array(
      '1' => $view->translate('Yes, show SocialEngine Core Like button'),
      '0' => $view->translate('No'),
  );
  $default_value = 1;
}
$popularity_options = array(
    'view_count' => $view->translate('Most Viewed'),
    'like_count' => $view->translate('Most Liked'),
    'comment_count' => $view->translate('Most Commented'),
    'review_count' => $view->translate('Most Reviewed'),
    'rating_avg' => $view->translate('Most Rated (Average Rating)'),
    'rating_editor' => $view->translate('Most Rated (Editor Rating)'),
    'rating_users' => $view->translate('Most Rated (User Ratings)'),
    'product_id' => $view->translate('Most Recent'),
    'modified_date' => $view->translate('Recently Updated'),
    'discount_amount' => $view->translate('Most Discounted'),
);
$product_type_options = array(
    'all' => $view->translate('All'),
    'simple' => $view->translate('Simple Product'),
    'configurable' => $view->translate('Configurable Product'),
    'virtual' => $view->translate('Virtual Product'),
    'grouped' => $view->translate('Grouped Product'),
    'bundled' => $view->translate('Bundled Product'),
    'downloadable' => $view->translate('Downloadable Product'),
);
if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0))
  $information_array = array("ownerPhoto" => "Product Owner's Photo", "ownerName" => "Owner's Name", "modifiedDate" => "Modified Date", "viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "tags" => "Tags / Brand", "stores" => "Stores", "category" => "Category", "location" => "Location");
else
  $information_array = array("ownerPhoto" => "Product Owner's Photo", "ownerName" => "Owner's Name", "modifiedDate" => "Modified Date", "viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "tags" => "Tags / Brand", "stores" => "Stores", "category" => "Category");
$featuredSponsoredElement = array(
    'Select',
    'fea_spo',
    array(
        'label' => $view->translate('Show Products'),
        'multiOptions' => array(
            '' => '',
            'newlabel' => $view->translate('New Only'),
            'featured' => $view->translate('Featured Only'),
            'sponsored' => $view->translate('Sponsored Only'),
            'fea_spo' => $view->translate('Both Featured and Sponsored'),
        ),
        'value' => '',
    )
);
$statisticsElement = array(
    'MultiCheckbox',
    'statistics',
    array(
        'label' => $view->translate('Choose the statistics that you want to be displayed for the Products in this block.'),
        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'reviewCount' => 'Reviews'),
    ),
);
$statisticsLocationElement = array(
    'MultiCheckbox',
    'statistics',
    array(
        'label' => $view->translate('Choose the statistics that you want to be displayed for the Products in this block.'),
        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'reviewCount' => 'Reviews', "location" => "Location"),
    ),
);
$statisticsWishlistElement = array(
    'MultiCheckbox',
    'statisticsWishlist',
    array(
        'label' => $view->translate('Choose the statistics that you want to be displayed for the Wishlist in this block.'),
        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "followCount" => "Followers", "productCount" => "Products"),
    ),
);
$statisticsStoreElement = array(
    'MultiCheckbox',
    'statistics',
    array(
        'label' => $view->translate('Choose the statistics that you want to be displayed for the Stores in this block.'),
        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'reviewCount' => 'Reviews'),
    ),
);
$ratingTypeElement = array(
    'Select',
    'ratingType',
    array(
        'label' => $view->translate('Rating Type'),
        'multiOptions' => array('rating_avg' => $view->translate('Average Ratings'), 'rating_editor' => $view->translate('Only Editor Ratings'), 'rating_users' => $view->translate('Only User Ratings'), 'rating_both' => $view->translate('Both User and Editor Ratings')),
    )
);
$ratingElement = array(
    'Select',
    'ratingType',
    array(
        'label' => 'Rating Type',
        'multiOptions' => array('rating_avg' => 'Average Ratings', 'rating_editor' => 'Only Editor Ratings', 'rating_users' => 'Only User Ratings', 'rating_both' => 'Both User and Editor Ratings'),
    )
);
$categories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategories(null, 0, 0, 1);
$categories_prepared = array();
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
  $categoryElement = array(
      'Select',
      'category_id',
      array(
          'label' => 'Category',
          'multiOptions' => $categories_prepared,
          'RegisterInArrayValidator' => false,
          'onchange' => 'addOptions(this.value, "cat_dependency", "subcategory_id", 0); setHiddenValues("category_id")'
  ));
  $subCategoryElement = array(
      'Select',
      'subcategory_id',
      array(
          'RegisterInArrayValidator' => false,
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => 'application/modules/Sitestoreproduct/views/scripts/_category.tpl',
                      'class' => 'form element')))
  ));
}
$calendarElement = array(
    'Select',
    'date',
    array(
        'RegisterInArrayValidator' => false,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => 'application/modules/Sitestoreproduct/views/scripts/_calendar.tpl',
                    'class' => 'form element')))
        ));
$hiddenCatElement = array(
    'Text',
    'hidden_category_id',
    array(
        ));
$hiddenSubCatElement = array(
    'Text',
    'hidden_subcategory_id',
    array(
        ));
$hiddenSubSubCatElement = array(
    'Text',
    'hidden_subsubcategory_id',
    array(
        ));
$final_array = array(
    array(
        'title' => $view->translate('Product Profile: Overview'),
        'description' => $view->translate('This widget forms the Overview tab on the Product Profile page and displays the overview of the product, which the owner has created using the editor in product dashboard. This widget should be placed in the Tabbed Blocks area of the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.overview-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Overview'),
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => $view->translate('Widget Content Loading'),
                        'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showAfterEditorReview',
                    array(
                        'label' => $view->translate('Do you want to display this block even when the Overview is shown in "Product Profile: Editor Review / Overview / Description" widget?'),
                        'multiOptions' => array(
                            2 => $view->translate('Yes, always display this block.'),
                            1 => $view->translate('No, display this block when Overview is not displayed in that widget.'),
                        // 0 => 'Show Overview in Editor Review tab only till Editor Review has not been written.'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showComments',
                    array(
                        'label' => $view->translate('Enable Comments'),
                        'description' => $view->translate('Do you want to enable comments in this widget? (If enabled, then users will be able to comment on the product being viewed. Note: If you enable this, then you should not place the ‘Product / Review Profile: Comments & Replies’ widget on Product Profile page.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 0,
                    )
                ),
            )
        )
    ),
    array(
        'title' => $view->translate('Product Archives'),
        'description' => $view->translate('Displays the month-wise archives for the products created on your site.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.archives-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Archives'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Navigation Tabs'),
        'description' => $view->translate('Displays the Navigation tabs for \'Stores / Marketplace Plugin\' having links of Products, Editors, Wishlists etc. This widget should be placed at the top of \'Editors Home\', \'Categories Home\', \'Products Home\', \'Browse Products\', \'Browse Products\' and \'Browse Reviews\' page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.navigation-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Categories Hierarchy for Products (sidebar)'),
        'description' => $view->translate('Displays the Categories, Sub-categories and 3rd Level-categories of Products in an expandable form. Clicking on them will redirect the viewer to Browse Products page displaying the list of products created in that category. Multiple settings are available to customize this widget. It is recommended to place this widget in \'Full Width\'.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.categories-sidebar-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Categories'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Categories Home: Categories Hierarchy for Products'),
        'description' => $view->translate('Displays the Categories, Sub-categories and 3rd Level-categories of Products in an expandable form. Clicking on them will redirect the viewer to the list of products created in that category. Multiple settings are available to customize this widget. This widget should be placed on the Categories Home Page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.categories-home',
        'defaultParams' => array(
            'title' => $view->translate('Categories'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => $view->translate('Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 products in them?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'show2ndlevelCategory',
                    array(
                        'label' => $view->translate('Do you want to show sub-categories in this widget?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'show3rdlevelCategory',
                    array(
                        'label' => $view->translate('Do you want to show 3rd level category to the viewer? This settings will only work if you choose to show sub-categories from the setting above.'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Select',
                    'orderBy',
                    array(
                        'label' => $view->translate('Categories Ordering'),
                        'multiOptions' => array('category_name' => $view->translate('Alphabetical'), 'cat_order' => $view->translate('Ordering as in categories tab')),
                        'value' => 'category_name',
                    ),
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Show 3rd level categories of sub-categories in'),
                        'multiOptions' => array('expanded' => $view->translate('Expanded View'), 'collapsed' => $view->translate('Collapsed View')),
                        'value' => 'expanded',
                    )
                ),
                array(
                    'Radio',
                    'showCount',
                    array(
                        'label' => $view->translate('Show Products count along with Categories,Sub-categories and 3rd level categories.
'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 0,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => $view->translate('Category Profile: Breadcrumb'),
        'description' => $view->translate('Displays breadcrumb of the category. This widget should be placed on the Category Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.categories-home-breadcrumb',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
            ))
    ),
    array(
        'title' => $view->translate('Categories Hierarchy for Products'),
        'description' => $view->translate('Displays the Categories, Sub-categories and 3<sup>rd</sup> Level-categories of products in an expandable form. Clicking on them will redirect the viewer to the list of products created in that category. Multiple settings are available to customize this widget. It is recommended to place this widget in the middle column of the Products Home page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.categories-middle-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Categories'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'showAllCategories',
                    array(
                        'label' => $view->translate('Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 products in them?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'show2ndlevelCategory',
                    array(
                        'label' => $view->translate('Do you want to show sub-categories in this widget?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'show3rdlevelCategory',
                    array(
                        'label' => $view->translate('Do you want to show 3rd level category to the viewer? This settings will only work if you choose to show sub-categories from the setting above.'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showCount',
                    array(
                        'label' => $view->translate('Show Products count along with Categories,Sub-categories and 3rd level categories.
'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 0,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => $view->translate('Sponsored Categories'),
        'description' => $view->translate('Displays the Sponsored categories, sub-categories and 3<sup>rd</sup> level-categories. You can make categories as Sponsored from "Categories" section of Admin Panel.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.categories-sponsored',
        'defaultParams' => array(
            'title' => $view->translate('Sponsored Categories'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of categories to show. Enter 0 for displaying all categories.)'),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'showIcon',
                    array(
                        'label' => $view->translate('Do you want to display the icons along with the categories in this block?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
            )
        ),
    ),
    array(
        'title' => $view->translate('Member Profile: Profile Products'),
        'description' => $view->translate('Displays a member\'s products on their profile. This widget should be placed in the Tabbed Blocks area of Member Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.profile-sitestoreproduct',
        'defaultParams' => array(
            'title' => 'Products',
            'titleCount' => true,
            'statistics' => array("viewCount", "likeCount", "commentCount", "reviewCount"),
            'viewType' => 'gridview',
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                $ratingTypeElement,
                $statisticsElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 35,
                    )
                ),
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show Add to Cart button.'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the in stock quantity.'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Choose the View Type for products.'),
                        'multiOptions' => array(
                            'listview' => $view->translate('List View'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                        'value' => 'gridview',
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '165',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '325',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 10,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Popular Products Slideshow'),
        'description' => $view->translate('Displays products based on the Popularity Criteria and other settings configured by you in an attractive slideshow with interactive controls. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.slideshow-sitestoreproduct',
        'autoEdit' => 'true',
        'defaultParams' => array(
            'title' => $view->translate('Featured Products'),
            'titleCount' => true,
            'statistics' => array("viewCount", "likeCount", "commentCount", "reviewCount")
        ),
        'adminForm' => array(
            array(
                'Radio',
                'add_to_cart',
                array(
                    'label' => $view->translate("Do you want to show cart options like 'Add to Cart'or 'Out of Stock'."),
                    'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                    'value' => '1',
                )
            ),
            array(
                'Radio',
                'in_stock',
                array(
                    'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                    'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                    'value' => '1',
                )
            ),
            'elements' => array(
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $ratingTypeElement,
                $featuredSponsoredElement,
                $statisticsElement,
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => array_merge($popularity_options, array('random' => 'Random')),
                        'value' => 'product_id',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => $view->translate('Popularity Duration (This duration will be applicable to these Popularity Criteria:  Most Liked, Most Commented, Most Rated and Most Recent.)'),
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Radio',
                    'featuredIcon',
                    array(
                        'label' => $view->translate('Do you want to show the featured icon / label? (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'sponsoredIcon',
                    array(
                        'label' => $view->translate('Do you want to show the sponsored icon / label? (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'newIcon',
                    array(
                        'label' => $view->translate('Do you want to show the new icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 45,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'count',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of lisings to show)'),
                        'value' => 10,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Product Photos Slideshow'),
        'description' => $view->translate('Displays a Video and Photos selected by the product owners from their Product dashboard in an attractive slideshow. (If you place this widget, then users will be able to select photos and a video to be displayed in this slideshow from Photos and Videos section respectively of their Product Dashboard. Note: If you place this widget, then you should disable the product photos slideshow setting available in the \'Product Profile: Editor Review / Overview / Description\' widget.) It should be placed on Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.slideshow-list-photo',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'slideshow_height',
                    array(
                        'label' => $view->translate('Enter the heigth of the slideshow (in pixels).'),
                        'value' => 400,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'slideshow_width',
                    array(
                        'label' => $view->translate('Enter the width of the slideshow (in pixels).'),
                        'value' => 600,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Radio',
                    'showCaption',
                    array(
                        'label' => $view->translate('Do you want to show image description in this Slideshow?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showButtonSlide',
                    array(
                        'label' => "Do you want to show thumbnails for photos and video navigation in this Slideshow? (If you select No, then small circles will be shown at Slideshow bottom for slides navigation.)",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mouseEnterEvent',
                    array(
                        'label' => "By which action do you want slides navigation to occur from thumbnails / small circles?",
                        'multiOptions' => array(
                            1 => 'Mouse-over',
                            0 => 'On-click'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'thumbPosition',
                    array(
                        'label' => "Where do you want to show image thumbnails?",
                        'multiOptions' => array(
                            'bottom' => 'In the bottom of Slideshow',
                            'left' => 'In the left of Slideshow',
                            'right' => 'In the right of Slideshow',
                        ),
                        'value' => 'bottom',
                    )
                ),
                array(
                    'Radio',
                    'autoPlay',
                    array(
                        'label' => "Do you want the Slideshow to automatically start playing when Product Profile page is opened?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'slidesLimit',
                    array(
                        'label' => $view->translate('How many slides you want to show in slideshow?'),
                        'value' => 12,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'captionTruncation',
                    array(
                        'label' => $view->translate('Truncation limit for slideshow description'),
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
            )
        )
    ),
    array(
        'title' => $view->translate('Review / Editor Profile: Social Share Buttons'),
        'description' => $view->translate("Contains Social Sharing buttons and enables users to easily share Reviews / Editors' profiles on their favorite Social Networks. It is recommended to place this widget on the Review Profile page or Editor Profile page. You can customize the code for social sharing buttons from Global Settings of this plugin by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>"),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.socialshare-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Social Share'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product / Wishlist Profile: Share and Report Options'),
        'description' => $view->translate("Displays the various action link options to users viewing a product / wishlist (Report, Print, Share, etc). It also contains Social Sharing buttons to enable users to easily share products / wishlists on their favourite Social Network. You can customize the code for social sharing buttons from Global Settings of this plugin by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>. You can manage the Action Links available in this widget from the Edit settings of this widget. This widget should be placed on the Product Profile page or the Wishlist Profile page."),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.share',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => $view->translate('Share and Report'),
            'titleCount' => true,
            'options' => array("siteShare", "friend", "report", "print", "socialShare"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'options',
                    array(
                        'label' => $view->translate('Select the options that you want to display in this block.'),
                        'multiOptions' => array("siteShare" => "Site Share", "friend" => "Tell a Friend", "report" => "Report", 'print' => 'Print', 'socialShare' => 'Social Share'),
                    //'value' => array("siteShare","friend","report","print","socialShare"),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Product Title'),
        'description' => $view->translate('Displays the Title of the product. This widget should be placed on the Product Profile page, in the middle column at the top.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.title-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Specifications'),
        'description' => $view->translate('Displays the Questions added from the "Profile Fields" section in the Admin Panel. This widget should be placed in the Tabbed Blocks area of Products Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.specification-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Specs'),
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => $view->translate('Widget Content Loading'),
                        'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                )
            )
        )
    ),
    array(
        'title' => $view->translate('Product / Review Profile: Quick Specifications'),
        'description' => $view->translate('Displays the Questions enabled to be shown in this widget from the \'Profile Fields\' section in the Admin Panel. This widget should be placed in the right / left column on the Review Profile page or Products Profile.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.quick-specification-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Quick Specifications'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'show_specificationlink',
                    array(
                        'label' => $view->translate('Show \'Full Specification\' link. (Note: This link will only be displayed, if you have placed \'Product Profile: Specification\' widget in the Tabbed Blocks area of the Product Profile page as users will be redirected to this tab on clicking the link.'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Text',
                    'show_specificationtext',
                    array(
                        'label' => $view->translate('Please enter the text below which you want to display in place of "Full Specifications" link in this widget.'),
                        'value' => 'Full Specifications',
                    ),
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Number of specifications to show'),
                        'value' => 5,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Product Information'),
        'description' => $view->translate('Displays the owner, category, tags / brand, views, and other information about a product. This widget should be placed on Product Profile page in the left column.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.information-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Information'),
            'titleCount' => true,
            'showContent' => array("ownerPhoto", "ownerName", "modifiedDate", "viewCount", "likeCount", "commentCount", "tags", "category")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => $view->translate('Select the information options that you want to be available in this block.'),
                        'multiOptions' => $information_array,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Product Cover Photo'),
        'description' => $view->translate('Displays the main cover photo of a product. This widget must be placed on the Product Profile page at the top of left column.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.mainphoto-sitestoreproduct',
        'defaultParams' => array(
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'ownerName',
                    array(
                        'label' => $view->translate('Do you want to display product owner’s name in this widget?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 0,
                    )
                ),
            )
        )
    ),
    array(
        'title' => $view->translate("Product Profile: Product Owner's Photo"),
        'description' => $view->translate("Displays the Product owner's photo with owner's name. This widget should be placed in the right column of Product Profile Page."),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.product-owner-photo',
        'requirements' => array(
            'subject' => 'sitestoreproduct_product',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Product Options'),
        'description' => $view->translate('Displays the various action link options to users viewing a Product. This widget should be placed on the Product Profile page in the left column, below the product profile photo.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.options-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Product Photos'),
        'description' => $view->translate('This widget forms the Photos tab on the Product Profile page and displays the photos of the product. This widget should be placed in the Tabbed Blocks area of the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.photos-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Photos'),
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => $view->translate('Widget Content Loading'),
                        'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(Number of photos to show)'),
                        'value' => 20,
                    )
                ),
            )
        )
    ),
    array(
        'title' => $view->translate('Product Profile: Product Documents'),
        'description' => $view->translate('Displays a product’s documents on its profile.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.product-document',
        'defaultParams' => array(
            'title' => $view->translate('Documents'),
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => $view->translate('Widget Content Loading'),
                        'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
            )
        )
    ),
    array(
        'title' => $view->translate('Product Profile: Product Videos'),
        'description' => $view->translate('This widget forms the Videos tab on the Product Profile page and displays the videos of the product. This widget should be placed in the Tabbed Blocks area of the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.video-sitestoreproduct',
        'defaultParams' => array(
            'title' => 'Videos',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => $view->translate('Widget Content Loading'),
                        'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'count',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of videos to show)'),
                        'value' => 10,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 35,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
            )
        )
    ),
    array(
        'title' => $view->translate('Popular Product Tags / Brands'),
        'description' => $view->translate('Displays popular tags / brands with frequency. This widget should be placed on the \'Product Profile\' / \'Browse Products\' / \'Products Home\' pages.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.tagcloud-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Popular Brands (%s)'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => $view->translate('Title'),
                        'description' => $view->translate("Enter below the format in which you want to display the title of the widget. (Note: To display count of tags / brands on products browse and home pages, enter title as: Title (%s). To display product owner’s name on product profile page, enter title as: %s's Tags / Brands.)"),
                        'value' => 'Popular Brands (%s)',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of tags / brands to show)'),
                        'value' => 25,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Owner Products'),
        'description' => $view->translate('Displays a list of other products owned by the product owner. This widget should be placed on Reviews: Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.userproduct-sitestoreproduct',
        'defaultParams' => array(
            'title' => "%s's Products",
            'titleCount' => true,
            'statistics' => array("likeCount", "reviewCount")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => $view->translate('Title'),
                        'description' => $view->translate("Enter below the format in which you want to display the title of the widget. (Note: To display product owner’s name on product profile page, enter title as: %s's Products.)"),
                        'value' => "%s's Products",
                    )
                ),
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                $ratingTypeElement,
                $statisticsElement,
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Choose the View Type for products.'),
                        'multiOptions' => array(
                            'listview' => $view->translate('List View'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                        'value' => 'gridview',
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '328',
                    )
                ),
                array(
                    'Text',
                    'count',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 24,
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
        'title' => $view->translate('Product Profile: About Product'),
        'description' => $view->translate('Displays the About Product information for products as entered by product owners. This widget should be placed on the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.write-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Browse Products: Breadcrumb'),
        'description' => $view->translate('Displays breadcrumb based on the categories searched from the search form widget. This widget should be placed on Browse Products page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.browse-breadcrumb-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Browse Products'),
        'description' => $view->translate('Displays a list of all the products on your site. This widget should be placed on Browse Products page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.browse-products-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'layouts_views' => array("1", "2", "3"),
            'layouts_order' => 1,
            'statistics' => array("viewCount", "likeCount", "commentCount", "reviewCount"),
            'columnWidth' => '180',
            'truncationGrid' => 90
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'featuredIcon',
                    array(
                        'label' => $view->translate('Do you want to show the featured icon / label? (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'sponsoredIcon',
                    array(
                        'label' => $view->translate('Do you want to show the sponsored icon / label? (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'newIcon',
                    array(
                        'label' => $view->translate('Do you want to show the new icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                $ratingTypeElement,
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => $view->translate('Choose the view types that you want to be available for products.'),
                        'multiOptions' => array("1" => $view->translate("List View"), "2" => $view->translate("Grid View"), "3" => $view->translate("Map View")),
                    //'value' => array("0" => "1", "1" => "2", "2" => "3"),
                    ),
                ),
                array(
                    'Radio',
                    'layouts_order',
                    array(
                        'label' => $view->translate('Select a default view type for Products.'),
                        'multiOptions' => array("1" => $view->translate("List View"), "2" => $view->translate("Grid View"), "3" => $view->translate("Map View")),
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '165',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '315',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => $view->translate('Choose the statistics that you want to be displayed for the Products in this block.'),
                        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'reviewCount' => 'Reviews', 'viewRating' => 'Ratings', 'location' => 'Location'),
                    ),
                ),
                //  'viewRating' => 'Ratings'
//                array(
//                    'Radio',
//                    'viewType',
//                    array(
//                        'label' => $view->translate("Do you want to show 'Where to Buy' options associated with the products in this block? (Note: If you select 'Yes' below, then you should place this widget in the Right Extended / Left Extended Column.)"),
//                        'multiOptions' => array(
//                            '1' => $view->translate('Yes'),
//                            '0' => $view->translate('No'),
//                        ),
//                        'value' => '1',
//                    )
//                ),
                array(
                    'Radio',
                    'bottomLine',
                    array(
                        'label' => $view->translate('Choose from below what you want to display with product title.'),
                        'multiOptions' => array(
                            '1' => $view->translate("Editor Review's Bottom line (If there is no Editor Review, then Product's description will be displayed.)"),
                            '0' => $view->translate("Product's description"),
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => $view->translate("Show created by option. (Selecting 'Yes' here will display the member's name who has created the product, only if you have chosen List View from the above setting.)"),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => $view->translate('Default ordering in Browse Products. (Note: Selecting multiple ordering will make your page load slower.)'),
                        'multiOptions' => array(
                            'product_id' => $view->translate('All products in descending order of creation.'),
                            'view_count' => $view->translate('All products in descending order of views.'),
                            'title' => $view->translate('All products in alphabetical order.'),
                            'sponsored' => $view->translate('Sponsored products followed by others in descending order of creation.'),
                            'featured' => $view->translate('Featured products followed by others in descending order of creation.'),
                            'fespfe' => $view->translate('Sponsored & Featured products followed by Sponsored products followed by Featured products followed by others in descending order of creation.'),
                            'spfesp' => $view->translate('Featured & Sponsored products followed by Featured products followed by Sponsored products followed by others in descending order of creation.'),
                            'newlabel' => $view->translate('Products marked as New followed by others in descending order of creation.'),
                        ),
                        'value' => 'product_id',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 10,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 25,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'truncationGrid',
                    array(
                        'label' => $view->translate('Title Truncation Limit in Grid View'),
                        'value' => 90,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
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
                $defaultLocationDistanceElement
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Best Alternatives'),
        'description' => $view->translate('Displays products similar to the product being viewed as Best Alternative products. The similar products are shown based on the products selected by the editors as similar products from the product profile page or bottom-level category of the product being viewed. This widget should be placed on Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.similar-items-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Best Alternatives'),
            'titleCount' => true,
            'statistics' => array("likeCount", "commentCount", "reviewCount")
        ),
        'adminForm' => array(
            'elements' => array(
                $ratingTypeElement,
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Display Type'),
                        'multiOptions' => array(
                            '1' => $view->translate('Horizontal'),
                            '0' => $view->translate('Vertical'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '328',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => $view->translate('Choose the statistics that you want to be displayed for the Products in this block.'),
                        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'reviewCount' => 'Reviews', 'viewRating' => 'Ratings'),
                    ),
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 24,
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
        'title' => $view->translate('Popular / Recent / Random Products'),
        'description' => $view->translate('Displays Products based on the Popularity Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.products-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Reviews'),
            'titleCount' => true,
//            'statistics' => array("likeCount", "reviewCount"),
            'viewType' => 'gridview',
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'priceWithTitle',
                    array(
                        'label' => $view->translate('Do you want to show the price of product with title?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '0',
                    )
                ),
                array(
                    'Radio',
                    'showCategory',
                    array(
                        'label' => $view->translate('Do you want to show the category of products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                $ratingTypeElement,
                $featuredSponsoredElement,
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Choose the View Type for products.'),
                        'multiOptions' => array(
                            'listview' => $view->translate('List View'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                        'value' => 'gridview',
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '328',
                    )
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => $popularity_options,
                        'value' => 'view_count',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => $view->translate('Popularity Duration (This duration will be applicable to these Popularity Criteria:  Most Liked, Most Commented, Most Rated and Most Recent.)'),
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $statisticsElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 3,
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
            ),
        ),
    ),
    array(
        'title' => $view->translate('Popular / Recent / Random Products - Grid'),
        'description' => $view->translate('Displays Products based on the Popularity Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.products-sitestoreproduct-grid',
        'defaultParams' => array(
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '195',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '328',
                    )
                ),
                $featuredSponsoredElement,
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => $popularity_options,
                        'value' => 'view_count',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => $view->translate('Popularity Duration (This duration will be applicable to these Popularity Criteria:  Most Liked, Most Commented, Most Rated and Most Recent.)'),
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 3,
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
            ),
        ),
    ),
    array(
        'title' => $view->translate('Most Discussed Products'),
        'description' => $view->translate('Displays the products having the most number of discussions. Multiple settings available in the Edit Settings of this widget.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.most-discussed-products',
        'defaultParams' => array(
            'title' => $view->translate('Reviews'),
            'titleCount' => true,
            'viewType' => 'listview',
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                $ratingTypeElement,
                $featuredSponsoredElement,
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Choose the View Type for products.'),
                        'multiOptions' => array(
                            'listview' => $view->translate('List View'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                        'value' => 'gridview',
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '328',
                    )
                ),
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 3,
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
            ),
        ),
    ),
    array(
        'title' => $view->translate('Search Products Form'),
        'description' => $view->translate('Displays the form for searching Products on the basis of various fields and filters. Settings for this form can be configured from the Search Form Settings section.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.search-sitestoreproduct',
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
                        'label' => $view->translate('Show Search Form'),
                        'multiOptions' => array(
                            'horizontal' => $view->translate('Horizontal'),
                            'vertical' => $view->translate('Vertical'),
                        ),
                        'value' => 'vertical'
                    )
                ),
                array(
                    'Radio',
                    'resultsAction',
                    array(
                        'label' => 'Select the page where you want to display the results of search.',
                        'multiOptions' => array(
                            'index' => 'Browse Products',
                            'pinboard' => 'Browse Products - Pinboard View',
                        ),
                        'value' => 'index',
                    )
                ),
                array(
                    'Radio',
                    'subcategoryFiltering',
                    array(
                        'label' => $view->translate('Do you want to allow Subcategory and 3rd Level Category filtering?'),
                        'multiOptions' => array(
                            '1' => $view->translate('Yes'),
                            '0' => $view->translate('No'),
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Radio',
                    'priceFieldType',
                    array(
                        'label' => $view->translate('Enable price slider'),
                        'multiOptions' => array(
                            'slider' => $view->translate('Yes, show the slider.'),
                            'text' => $view->translate('No, show the min and max price text box instead of slider.'),
                        ),
                        'value' => 'slider'
                    )
                ),
                array(
                    'text',
                    'minPrice',
                    array(
                        'label' => $view->translate('Slider range starting value if enabled.'),
                        'value' => 0
                    )
                ),
                array(
                    'text',
                    'maxPrice',
                    array(
                        'label' => $view->translate('Slider range ending value if enabled.'),
                        'value' => 999
                    )
                ),
                array(
                    'Radio',
                    'currencySymbolPosition',
                    array(
                        'label' => $view->translate('Currency Symbol Position'),
                        'multiOptions' => array(
                            'right' => $view->translate('Right side of the price'),
                            'left' => $view->translate('Left side of the price'),
                        ),
                        'value' => 'left'
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
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'whatWhereWithinmile',
                    array(
                        'label' => 'Do you want to show "What, Where and Within Miles" in single row and bold text label? [Note: This setting will not work when form is placed in right/left column.]',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'advancedSearch',
                    array(
                        'label' => 'Do you want to show all advanced search fields expanded  [Note: This setting will not work if above setting set "No" and when form is placed in right/left column.]',
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
        'title' => $view->translate('AJAX based Products Carousel'),
        'description' => $view->translate('This widget contains an attractive AJAX based carousel, showcasing the products on the site. You can choose to show sponsored / featured / new products in this widget from the settings of this widget. You can place this widget multiple times on a page with different criterion chosen for each placement.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.sponsored-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Products Carousel'),
            'titleCount' => true,
            'showOptions' => array("category", "rating", "review", "compare", "wishlist"),
        ),
        'adminForm' => array(
            'elements' => array(
                $ratingTypeElement,
                $featuredSponsoredElement,
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'priceWithTitle',
                    array(
                        'label' => $view->translate('Do you want to show the price of product with title?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '0',
                    )
                ),
                array(
                    'Radio',
                    'showPagination',
                    array(
                        'label' => $view->translate('Do you want to show next / previous pagination?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Carousel Type'),
                        'multiOptions' => array(
                            '0' => $view->translate('Horizontal'),
                            '1' => $view->translate('Vertical'),
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'blockHeight',
                    array(
                        'label' => $view->translate('Enter the height of each slideshow item.'),
                        'value' => 305,
                    )
                ),
                array(
                    'Text',
                    'blockWidth',
                    array(
                        'label' => $view->translate('Enter the width of each slideshow item.'),
                        'value' => 200,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Enter number of products in a Row / Column for Horizontal / Vertical Carousel Type respectively as selected by you from the above setting.'),
                        'value' => 4,
                    )
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => $popularity_options,
                        'value' => 'product_id',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'showOptions',
                    array(
                        'label' => $view->translate('Choose the action link or detail to be available for each product.'),
                        'multiOptions' => array("category" => "Category", "rating" => "Rating", "review" => "Review", "compare" => "Compare", "wishlist" => "Add to Wishlist"),
                    ),
                ),
                array(
                    'Radio',
                    'featuredIcon',
                    array(
                        'label' => $view->translate('Do you want to show the featured icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'sponsoredIcon',
                    array(
                        'label' => $view->translate('Do you want to show the sponsored icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'newIcon',
                    array(
                        'label' => $view->translate('Do you want to show the new icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => $view->translate('Speed'),
                        'description' => $view->translate('(transition interval between two slides in millisecs)'),
                        'value' => 300,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 50,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product of the Day'),
        'description' => $view->translate('Displays a product as product of the day. You can choose the product to be shown in this widget from the settings of this widget. Other settings are also available.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.item-sitestoreproduct',
        'adminForm' => 'Sitestoreproduct_Form_Admin_Settings_Dayitem',
        'defaultParams' => array(
            'title' => $view->translate('Product of the Day'),
        ),
    ),
    array(
        'title' => $view->translate('Review of the Day'),
        'description' => $view->translate('Displays a review as review of the day. You can choose the review to be shown in this widget from the settings of this widget. Other settings are also available.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.review-of-the-day',
        'adminForm' => 'Sitestoreproduct_Form_Admin_Settings_Reviewdayitem',
        'defaultParams' => array(
            'title' => $view->translate('Review of the Day'),
        ),
    ),
    array(
        'title' => $view->translate('Recently Viewed by Users'),
        'description' => $view->translate('Displays products that have been recently viewed by Users of your site. Multiple settings are available for this widget in its Edit section.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.recently-viewed-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Recently Viewed By Friends'),
            'titleCount' => true,
            'statistics' => array("likeCount", "reviewCount"),
        ),
        'adminForm' => array(
            'elements' => array(
                $ratingTypeElement,
                $featuredSponsoredElement,
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'show',
                    array(
                        'label' => $view->translate('Show recently viewed products of:'),
                        'multiOptions' => array(
                            '2' => $view->translate('Viewed by any user.'),
                            '1' => $view->translate('Currently logged-in member’s friends.'),
                            '0' => $view->translate('Self(Currently logged-in member).'),
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Choose the View Type for products.'),
                        'multiOptions' => array(
                            'listview' => $view->translate('List View'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                        'value' => 'gridview',
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '328',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => $view->translate('Choose the statistics that you want to be displayed for the Products in this block.'),
                        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'reviewCount' => 'Reviews', 'viewRating' => 'Ratings'),
                    ),
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
                    ),
                ),
                array(
                    'Text',
                    'count',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 3,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Message for Zero Products'),
        'description' => $view->translate('This widget should be placed in the top of the middle column of Products Home page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.zeroproduct-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Close Product Message'),
        'description' => $view->translate('If a Product is closed, then show its message.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.closeproduct-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Like Button for Products'),
        'description' => $view->translate('This is the Like Button to be placed on Product Profile Page. The best place to put this widget is right above the Tabbed Block of the Review: Product Profile Page. If you have the Likes Plugins and Widgets from SocialEngineAddOns installed on your site, then you may replace this button widget of that plugin.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'seaocore.like-button',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: "Write a Review" Button for Products'),
        'description' => $view->translate('This is the "Write a Review" Button to be placed on Product Profile page. When clicked, users will be redirected to write review for the product being viewed. The best place to put this widget is right above the Tabbed Block of the Review: Product Profile Page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.review-button',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Product Discussions'),
        'description' => $view->translate('This widget forms the Discussions tab on the Product Profile page and displays the discussions of the product. This widget should be placed in the Tabbed Blocks area of the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.discussion-sitestoreproduct',
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => $view->translate('Widget Content Loading'),
                        'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                )
            )
        )
    ),
    array(
        'title' => $view->translate('Product Profile: Product Likes'),
        'description' => $view->translate('Displays that which all users have liked a product. This widget should be placed on the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'seaocore.people-like',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of users to show)'),
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
    array(
        'title' => $view->translate('Ajax based main Products Home widget'),
        'description' => $view->translate("Contains multiple Ajax based tabs showing Recently Created, Popular, Most Reviewed, Featured and Sponsored products in a block in separate ajax based tabs respectively. You can configure various settings for this widget from the Edit settings."),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.recently-popular-random-sitestoreproduct',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
            'statistics' => array("viewCount", "likeCount", "commentCount", "reviewCount"),
            'layouts_views' => array("listZZZview", "gridZZZview", "mapZZZview"),
            'ajaxTabs' => array("recent", "mostZZZreviewed", "mostZZZpopular", "featured", "sponsored", "topZZZselling", "newZZZarrivals"),
            'recent_order' => 1,
            'reviews_order' => 2,
            'popular_order' => 3,
            'featured_order' => 4,
            'sponsored_order' => 5,
            'top_selling_order' => 6,
            'new_arrival_order' => 7,
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'priceWithTitle',
                    array(
                        'label' => $view->translate('Do you want to show the price of product with title?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '0',
                    )
                ),
                array(
                    'Radio',
                    'showCategory',
                    array(
                        'label' => $view->translate('Do you want to show the category of products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'showLocation',
                    array(
                        'label' => $view->translate('Do you want to show the location of products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '0',
                    )
                ),
                $ratingTypeElement,
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $statisticsElement,
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => $view->translate('Choose the view types that you want to be available for products on the home and browse pages of products.'),
                        'multiOptions' => array("listZZZview" => "List View", "gridZZZview" => "Grid View", "mapZZZview" => "Map View")
                    ),
                ),
                array(
                    'Radio',
                    'defaultOrder',
                    array(
                        'label' => $view->translate('Select a default view type for Products'),
                        'multiOptions' => array("listZZZview" => $view->translate("List View"), "gridZZZview" => $view->translate("Grid View"), "mapZZZview" => "Map View"),
                        'value' => "gridZZZview",
                    )
                ),
                array(
                    'Radio',
                    'defaultOrder',
                    array(
                        'label' => $view->translate('Select a default view type for Products'),
                        'multiOptions' => array("listZZZview" => $view->translate("List View"), "gridZZZview" => $view->translate("Grid View"), "mapZZZview" => "Map View"),
                        'value' => "gridZZZview",
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '158',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '300',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'ajaxTabs',
                    array(
                        'label' => $view->translate('Select the tabs that you want to be available in this block.'),
                        'multiOptions' => array("recent" => "Recent", "mostZZZreviewed" => "Most Reviewed", "mostZZZpopular" => "Most Popular", "featured" => "Featured", "sponsored" => "Sponsored", "topZZZselling" => "Top Selling", "newZZZarrivals" => "New Arrivals")
                    )
                ),
                array(
                    'Text',
                    'recent_order',
                    array(
                        'label' => $view->translate('Recent Tab (order)'),
                        'value' => 1
                    ),
                ),
                array(
                    'Text',
                    'reviews_order',
                    array(
                        'label' => $view->translate('Most Reviewed Tab (order)'),
                        'value' => 2
                    ),
                ),
                array(
                    'Text',
                    'popular_order',
                    array(
                        'label' => $view->translate('Most Popular Tab (order)'),
                        'value' => 3
                    ),
                ),
                array(
                    'Text',
                    'featured_order',
                    array(
                        'label' => $view->translate('Featured Tab (order)'),
                        'value' => 4
                    ),
                ),
                array(
                    'Text',
                    'sponsored_order',
                    array(
                        'label' => $view->translate('Sponsored Tab (order)'),
                        'value' => 5
                    ),
                ),
                array(
                    'Text',
                    'top_selling_order',
                    array(
                        'label' => $view->translate('Top Selling Tab (order)'),
                        'value' => 6
                    ),
                ),
                array(
                    'Text',
                    'new_arrival_order',
                    array(
                        'label' => $view->translate('New Arrivals Tab (order)'),
                        'value' => 7
                    ),
                ),
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => $view->translate('Show created by option. (Selecting "Yes" here will display the member\'s name who has created the product.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => $view->translate('Number of Products to show'),
                        'value' => 12,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    ),
                ),
                array(
                    'Text',
                    'truncationList',
                    array(
                        'label' => $view->translate('Title Truncation Limit in List View'),
                        'value' => 600,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'truncationGrid',
                    array(
                        'label' => $view->translate('Title Truncation Limit in Grid View'),
                        'value' => 90,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement
            )
        ),
    ),
    array(
        'title' => $view->translate('Categorically Popular Products'),
        'description' => $view->translate('This attractive widget categorically displays the most popular products on your site. It displays 5 Products for each category. From the edit popup of this widget, you can choose the number of categories to show, criteria for popularity and the duration for consideration of popularity.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.category-products-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Popular Products'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('No. of categories to show. Enter 0 to show all categories.'),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'productCount',
                    array(
                        'label' => $view->translate('No. of products to be shown in each category.'),
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
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => $category_products_multioptions,
                        'value' => 'view_count',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => $view->translate('Popularity Duration (This duration will be applicable to all Popularity Criteria except Views.)'),
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 25,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
            ),
        ),
    ), array(
        'title' => $view->translate('Products thumb'),
        'description' => $view->translate("Displays the thumbnails of the products which belong to the same Store."),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.thumb-list',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => $view->translate('Number of products thumb'),
                        'value' => 5,
                    ),
                ), array(
                    'Radio',
                    'productTitle',
                    array(
                        'label' => $view->translate('Do you want to show title of the products.'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ), array(
                    'Radio',
                    'linkSee',
                    array(
                        'label' => $view->translate('Do you want to show link for View Store.'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ), array(
                    'Radio',
                    'productNonImage',
                    array(
                        'label' => $view->translate('Do you want to show products which don\'t have product\'s image.'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ), array(
                    'Select',
                    'popularity',
                    array(
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => $popularity_options,
                        'value' => 'view_count',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Auto-suggest search for Products'),
        'description' => $view->translate("Displays auto-suggest search box for Products. As user types, Products will be displayed in an auto-suggest box. This widget also allows users can also search products based on their categories. Multiple settings are available in the Edit settings of this widget."),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.searchbox-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate("Search"),
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'formElements',
                    array(
                        'label' => $view->translate('Choose the options that you want to be displayed in this block.'),
//                        'multiOptions' => array("textElement" => "Auto-suggest for Keywords", "categoryElement" => "Category Filtering", "linkElement" => "Advanced Search link"),
                        'multiOptions' => array("textElement" => "Auto-suggest for Keywords", "categoryElement" => "Category Filtering"),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'categoriesLevel',
                    array(
                        'label' => $view->translate('Select the category level belonging to which categories will be displayed in the category drop-down of this widget.'),
                        'multiOptions' => array("category" => "Category", "subcategory" => "Sub-category", "subsubcategory" => "3rd level category"),
                    ),
                ),
                array(
                    'Text',
                    'textWidth',
                    array(
                        'label' => 'Width for AutoSuggest',
                        'value' => 580,
                    )
                ),
                array(
                    'Text',
                    'categoryWidth',
                    array(
                        'label' => 'Width for Category Filtering',
                        'value' => 220,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Related Products'),
        'description' => $view->translate('Displays a list of all products related to the product being viewed. The related products are shown based on the tags / brands and top-level category of the product being viewed. You can choose the related product criteria from the Edit Settings. This widget should be placed on the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.related-products-view-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Related Products'),
            'titleCount' => true,
            'statistics' => array("likeCount", "reviewCount")
        ),
        'adminForm' => array(
            'elements' => array(
                $ratingTypeElement,
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'related',
                    array(
                        'label' => $view->translate('Choose which all Products should be displayed here as Products related to the current Product.'),
                        'multiOptions' => array(
                            'tags' => $view->translate("Products having same tags / brand."),
                            'categories' => $view->translate('Products associated with same \'Categories\'.')
                        ),
                        'value' => 'categories',
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Choose the View Type for products.')
                        ,
                        'multiOptions' => array(
                            'listview' => $view->translate('List View'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                        'value' => 'gridview',
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '328',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => $view->translate('Choose the statistics that you want to be displayed for the Products in this block.'),
                        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'reviewCount' => 'Reviews', 'viewRating' => 'Ratings'),
                    ),
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 24,
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
        'title' => $view->translate('Product Profile: Editor Review / Overview / Description'),
        'description' => $view->translate("This widget forms a tab on the Product Profile page which displays Editor Review / Overview / Description of the product. If Editor Review is written, then the Editor Review will be shown in this block, otherwise Overview of the product will display. If Overview is also not written, then the description of the product will be shown. Multiple settings are available to customize this widget. This widget should be placed in Tabbed Blocks area of the Product Profile page."),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.editor-reviews-sitestoreproduct',
        'autoEdit' => true,
        'defaultParams' => array(
            'titleEditor' => $view->translate("Review"),
            'titleOverview' => $view->translate("Overview"),
            'titleDescription' => $view->translate("Description"),
            'titleCount' => "",
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'titleEditor',
                    array(
                        'label' => $view->translate('Title for Editor Review'),
                        'value' => $view->translate("Review"),
                    )
                ),
                array(
                    'Text',
                    'titleOverview',
                    array(
                        'label' => $view->translate('Title for Overview'),
                        'value' => $view->translate("Overview"),
                    )
                ),
                array(
                    'Text',
                    'titleDescription',
                    array(
                        'label' => $view->translate('Title for Description'),
                        'value' => $view->translate("Description"),
                    )
                ),
                array(
                    'Hidden',
                    'title',
                    array('order'=>1021)
                ),
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => $view->translate('Widget Content Loading'),
                        'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'show_slideshow',
                    array(
                        'label' => $view->translate('Show Slideshow'),
                        'description' => $view->translate('Do you want to display product photos slideshow in this block? (If you select \'Yes\', then users will be able to select photos and a video to be displayed in this slideshow from Photos and Videos section respectively of their Product Dashboard. Note: If you enable this, then you should not place the \'Product Profile: Product Photos Slideshow\' widget on Product Profile page.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'slideshow_height',
                    array(
                        'label' => $view->translate('Enter the heigth of the slideshow (in pixels).'),
                        'value' => 400,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'slideshow_width',
                    array(
                        'label' => $view->translate('Enter the width of the slideshow (in pixels).'),
                        'value' => 600,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Radio',
                    'showCaption',
                    array(
                        'label' => $view->translate('Do you want to show image description in this Slideshow?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showButtonSlide',
                    array(
                        'label' => "Do you want to show thumbnails for photos and video navigation in this Slideshow? (If you select No, then small circles will be shown at Slideshow bottom for slides navigation.)",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'mouseEnterEvent',
                    array(
                        'label' => "By which action do you want slides navigation to occur from thumbnails / small circles?",
                        'multiOptions' => array(
                            1 => 'Mouse-over',
                            0 => 'On-click'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'thumbPosition',
                    array(
                        'label' => "Where do you want to show image thumbnails?",
                        'multiOptions' => array(
                            'bottom' => 'In the bottom of Slideshow',
                            'left' => 'In the left of Slideshow',
                            'right' => 'In the right of Slideshow',
                        ),
                        'value' => 'bottom',
                    )
                ),
                array(
                    'Radio',
                    'autoPlay',
                    array(
                        'label' => "Do you want the Slideshow to automatically start playing when Product Profile page is opened?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'slidesLimit',
                    array(
                        'label' => $view->translate('How many slides you want to show in slideshow?'),
                        'value' => 20,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Text',
                    'captionTruncation',
                    array(
                        'label' => $view->translate('Truncation limit for slideshow description'),
                        'value' => 200,
                    ),
                    'validators' => array(
                        array('Int', true),
                        array('GreaterThan', true, array(0)),
                    ),
                ),
                array(
                    'Radio',
                    'showComments',
                    array(
                        'label' => $view->translate('Enable Comments'),
                        'description' => $view->translate('Do you want to enable comments in this widget? (If enabled, then users will be able to comment on the product being viewed. Note: If you enable this, then you should not place the ‘Product / Review Profile: Comments & Replies’ widget on Product Profile page.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
            )
        )
    ),
    array(
        'title' => $view->translate('Editor / Member Profile: Profile Reviews'),
        'description' => $view->translate('Displays a list of all the reviews written by the editors / members of your site whose profile is being viewed. From Edit settings of this widget, you can choose to show Editor reviews or User Reviews in this widget. This widget should be placed in the Tabbed Blocks area of Editor Profile page or Member Profile page. '),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.editor-profile-reviews-sitestoreproduct',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => $view->translate("Reviews"),
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'type',
                    array(
                        'label' => $view->translate('Review Type'),
                        'description' => $view->translate('Choose the type of reviews that you want to display in this widget.'),
                        'multiOptions' => array(
                            'user' => 'User Reviews',
                            'editor' => 'Editor Reviews'
                        ),
                        'value' => 'user',
                    ),
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of reviews to show)'),
                        'value' => 10,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Editor / Member Profile: Comments & Replies'),
        'description' => $view->translate("Displays a list of all the comments and replies by the members on Products and Reviews on your site. This widget should be placed in the Tabbed Blocks area of Editor Profile page or Member Profile page."),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.editor-replies-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate("Replies"),
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of comments & replies to show)'),
                        'value' => 5,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Editor Profile: Editor’s Member Profile Photo'),
        'description' => $view->translate('Displays Editors’ member profile photo on their editor profile. This widget should be placed on Editor Profile page in the right / left column.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.editor-photo-sitestoreproduct',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Featured Editor'),
        'description' => $view->translate('Displays the Featured Editor on your site. Edit settings of this widget contains option to select Featured Editor.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'autoEdit' => true,
        'type' => 'widget',
        'name' => 'sitestoreproduct.editor-featured-sitestoreproduct',
        'adminForm' => 'Sitestoreproduct_Form_Admin_Editors_Featured',
        'defaultParams' => array(
            'title' => $view->translate('Featured Editor'),
        ),
    ),
    array(
        'title' => $view->translate('Editors Home: Editors'),
        'description' => $view->translate("Displays a list of all the editors on site. This widget should be placed on 'Editors Home' page."),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.editors-home',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'superEditor',
                    array(
                        'label' => $view->translate('Show Super Editor.'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    ),
                ),
            ),
        )
    ),
    array(
        'title' => $view->translate('Editors Statistics'),
        'description' => $view->translate('Displays statistics of all the Editors on your site added by you from the \'Manage Editors\' section in the Admin Panel.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.editors-home-statistics-sitestoreproduct',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        )
    ),
    array(
        'title' => $view->translate('Product Profile: About Editor'),
        'description' => $view->translate('Displays the description (written by you from the \'Manage Editor\' section in the Admin Panel and Editors) about the Editor who has written \'Editor Review\' for the product. This widget should be placed on the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.about-editor-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('About Me'),
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Editor / Member Profile: About Editor'),
        'description' => $view->translate('Displays the description written by you (from the \'Manage Editors\' section in the Admin Panel) and Editors (using this widget) about the Editor whose Editor Profile is being viewed. This widget should be placed on the Editor Profile page or Member Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.editor-profile-info',
        'defaultParams' => array(
            'title' => $view->translate("About Me"),
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'show_badge',
                    array(
                        'label' => $view->translate('Displays the  badge assigned by you from \'Manage Editors\' section.'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    ),
                ),
                array(
                    'Radio',
                    'show_designation',
                    array(
                        'label' => $view->translate('Displays the designation assigned by you from \'Manage Editors\' section.'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Editor / Member Profile: Editor’s Name and Designation'),
        'description' => $view->translate('Displays the name and designation of the Editor whose profile is being viewed. This widget should be placed on the Editor Profile page or Member Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.editor-profile-title',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'show_designation',
                    array(
                        'label' => $view->translate('Do you want to display Editor’s designation in this block? (You can assign the designation from the ‘Manage Editors’ section of this plugin.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: User Reviews'),
        'description' => $view->translate('This widget forms the User Reviews tab on the Product Profile page and displays all the reviews written by the users of your site for the Product being viewed. This widget should be placed in the Tabbed Blocks area of the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.user-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate("User Reviews"),
            'titleCount' => "true",
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemProsConsCount',
                    array(
                        'label' => $view->translate('Number of reviews’ Pros and Cons to be displayed in the search results using \'Only Pros\' and \'Only Cons\' in the \'Show\' review search bar.'),
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'itemReviewsCount',
                    array(
                        'label' => $view->translate('Number of user reviews to show'),
                        'value' => 3,
                    )
                ),
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => $view->translate('Widget Content Loading'),
                        'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)'),
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
        'title' => $view->translate('Review Profile: Review View'),
        'description' => $view->translate('Displays the main Review. You can configure various setting from Edit Settings of this widget. This widget should be placed on Review Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.profile-review-sitestoreproduct',
        'defaultParams' => array(
            'title' => 'Reviews',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Breadcrumb'),
        'description' => $view->translate('Displays breadcrumb of the product based on the categories. This widget should be placed on the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.list-profile-breadcrumb',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'viewStoreBreadCrumb',
                    array(
                        'label' => $view->translate('Show the product store breadcrumb'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
            ))
    ),
    array(
        'title' => $view->translate('Product Profile: Full Width Product Information & Options'),
        'description' => $view->translate('Displays product profile photo with product information and various action links that can be performed on the Products from their Profile page (edit, delete, tell a friend, share, etc.). You can manage the Action Links available in this widget from the Menu Editor section by choosing Product Profile Page Options Menu. You can choose various information options from the Edit settings of this widget. This widget should be placed on the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.full-width-list-information-profile',
        'defaultParams' => array(
            'title' => '',
            'showContent' => array("postedDate", "postedBy", "viewCount", "likeCount", "commentCount", "photo", "photosCarousel", "tags", "description")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'like_button',
                    array(
                        'label' => $view->translate('Do you want to enable Like button in this block?'),
                        'multiOptions' => $show_like_button,
                        'value' => $default_value,
                    ),
                ),
                array(
                    'Radio',
                    'storeInfo',
                    array(
                        'label' => 'Do you want to show "Product’s Store Information"?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showDescription',
                    array(
                        'label' => 'Do you want to show product\'s short description?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'actionLinks',
                    array(
                        'label' => $view->translate('Do you want action links like print, tell a friend, edit details, etc to be available for the store products?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showProductRating',
                    array(
                        'label' => 'Do you want to show product rating?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showPaymentMethods',
                    array(
                        'label' => 'Do you want to show store payment methods?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                )
            )
        )
    ),
    array(
        'title' => $view->translate('Product Profile: Product Information & Options'),
        'description' => $view->translate('Displays product profile photo with product information and various action links that can be performed on the Products from their Profile page (edit, delete, tell a friend, share, etc.). You can manage the Action Links available in this widget from the Menu Editor section by choosing Product Profile Page Options Menu. You can choose various information options from the Edit settings of this widget. This widget should be placed on the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.list-information-profile',
        'defaultParams' => array(
            'title' => '',
            'showContent' => array("postedDate", "postedBy", "viewCount", "likeCount", "commentCount", "photo", "photosCarousel", "tags", "description")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'like_button',
                    array(
                        'label' => $view->translate('Do you want to enable Like button in this block?'),
                        'multiOptions' => $show_like_button,
                        'value' => $default_value,
                    ),
                ),
                array(
                    'Radio',
                    'storeInfo',
                    array(
                        'label' => 'Do you want to show store?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showDescription',
                    array(
                        'label' => 'Do you want to show short description?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'actionLinks',
                    array(
                        'label' => $view->translate('Do you want action links like print, tell a friend, edit details, etc. to the available for the products in this block?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showPaymentMethods',
                    array(
                        'label' => 'Do you want to show store payment methods?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            )
        )
    ),
    array(
        'title' => $view->translate('Product Profile: Product Rating'),
        'description' => $view->translate('This widget displays the overall rating given to the product by editors, member of your site and other users along with the rating parameters as configured by you from the Reviews & Ratings section in the Admin Panel. You can choose who should be able to give review from the Admin Panel. Multiple settings are available to customize this widget. This widget should be placed in the left column on the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.overall-ratings',
        'defaultParams' => array(
            'title' => 'Reviews',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'show_rating',
                    array(
                        'label' => $view->translate('Select from below type of ratings to be displayed in this widget'),
                        'multiOptions' => array(
                            'avg' => $view->translate('Combined Editor and User Rating'),
                            'both' => $view->translate('Editor and User Ratings separately'),
                            'editor' => $view->translate('Only Editor Ratings'),
                        ),
                        'value' => 'avg',
                    ),
                ),
                array(
                    'Radio',
                    'ratingParameter',
                    array(
                        'label' => $view->translate('Do you want to show Rating Parameters in this widget?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
            ),
        )
    ),
    array(
        'title' => $view->translate('Top Reviewers'),
        'description' => $view->translate('This widget shows the top reviewers for the products on your site based on the number of reviews posted by them. Multiple settings are available for this widget.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.top-reviewers-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Top Reviewers'),
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'type',
                    array(
                        'label' => $view->translate('Review Type'),
                        'description' => $view->translate('Choose the review type for which maximum reviewers should be shown in this widget.'),
                        'multiOptions' => array(
                            'overall' => $view->translate('Overall'),
                            'user' => $view->translate('User Reviews'),
                            'editor' => $view->translate('Editor Reviews')
                        ),
                        'value' => 'user'
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of reviewers to show)'),
                        'value' => 3,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Top Creators / Buyers'),
        'description' => $view->translate('This widget shows the top creators on your site based on the number of products created by them or top buyers on your site based on maximum products (on the basis of quantity / price) bought by them. Multiple settings are available for this widget.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.top-posters',
        'defaultParams' => array(
            'title' => $view->translate('Top Product Buyers'),
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'popularity',
                    array(
                        'label' => $view->translate('Show'),
                        'multiOptions' => array(
                            'top_poster' => 'Top Posters',
                            'top_buyer' => 'Top Buyers',
                        ),
                        'value' => 'top_buyer',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => $view->translate('Popularity Duration'),
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Number of creators / buyers to show'),
                        'value' => 3,
                    )
                ),
                array(
                    'Radio',
                    'listing_based_on',
                    array(
                        'label' => $view->translate('Select the criteria on the basis of which buyers will be shown in this block'),
                        'multiOptions' => array(
                            'price' => 'Price of products purchased',
                            'item' => 'Quantity of products purchased',
                        ),
                        'value' => 'price',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Editor Profile: Similar Editor'),
        'description' => $view->translate('Displays Editors similar to the Editors whose profile is being viewed. Multiple settings are available to customize this widget. This widget should be placed on Editor Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.editors-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Site Editors'),
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Display Type'),
                        'multiOptions' => array(
                            '1' => $view->translate('Horizontal'),
                            '0' => $view->translate('Vertical'),
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of editors to show)'),
                        'value' => 4,
                    )
                ),
                array(
                    'Radio',
                    'superEditor',
                    array(
                        'label' => $view->translate('Show Super Editor.'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Popular / Recent / Random Reviews'),
        'description' => $view->translate('Displays Reviews based on the Popularity Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.popular-reviews-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Popular Reviews'),
            'statistics' => array("viewCount"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'type',
                    array(
                        'label' => $view->translate('Review Type'),
                        'multiOptions' => array(
                            'overall' => $view->translate('All Reviews'),
                            'user' => $view->translate('User Reviews'),
                            'editor' => $view->translate('Editor Reviews'),
                        ),
                        'value' => 'user'
                    )
                ),
                array(
                    'Radio',
                    'status',
                    array(
                        'label' => $view->translate('Do you want to show only featured reviews.'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => $view->translate('Popularity Criteria.(The popularity criterion: Most Helpful, Most Liked, Most Commented and Most Replied will not be applicable, if you have chosen Editor Reviews from the \'Review Type\' setting above.)'),
                        'multiOptions' => array(
                            'view_count' => $view->translate('Most Viewed'),
                            'like_count' => $view->translate('Most Liked'),
                            'comment_count' => $view->translate('Most Commented'),
                            'helpful_count' => $view->translate('Most Helpful'),
                            'reply_count' => $view->translate('Most Replied'),
                            'review_id' => $view->translate('Most Recent'),
                            'modified_date' => $view->translate('Recently Updated')
                        ),
                        'value' => 'view_count',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => $view->translate('Popularity Duration (This duration will be applicable to these Popularity Criteria: Most Liked, Most Commented, Most Recent and Recently Updated)'),
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                array(
                    'Radio',
                    'groupby',
                    array(
                        'label' => $view->translate('Show multiple reviews from the same editor / user.'),
                        'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => $view->translate('Choose the statistics to be displayed for the reviews in this widget. (Note: This settings will not work if you choose to show Editor Reviews from the "Review Type" setting above.)'),
                        'multiOptions' => array("viewCount" => $view->translate("Views"), "likeCount" => $view->translate("Likes"), "commentCount" => $view->translate("Comments"), 'replyCount' => $view->translate('Replies'), 'helpfulCount' => $view->translate('Helpful')),
                    //'value' => array("viewCount","likeCount"),
                    ),
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of reviews to show)'),
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation limit'),
                        'value' => 16,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Browse Reviews: Search Reviews Form'),
        'description' => $view->translate('Displays the form for searching reviews. It is recommended to place this widget on Browse Reviews page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.review-browse-search',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Browse Reviews: User Reviews Statistics'),
        'description' => $view->translate('Displays statistics for all the reviews written by the users of your site. This widget should be placed in the left column of the Browse Review page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.reviews-statistics',
        'defaultParams' => array(
            'title' => 'Reviews Statistics',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Editor / Member Profile: Editor’s Reviews Statistics'),
        'description' => $view->translate('Displays statistics for all the editor reviews written by the Editor whose Editor Profile is being viewed. This widget should be placed on the Editor Profile page or Member Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.editor-profile-statistics',
        'defaultParams' => array(
            'title' => 'Editor Statistics',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Review Profile: Breadcrumb'),
        'description' => $view->translate('Displays breadcrumb of the review based on the categories and the product to which it belongs. This widget should be placed on the Review Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.profile-review-breadcrumb-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Stores / Marketplace - Products: Browse Wishlists'),
        'description' => $view->translate('Displays a list of wishlists created by adding products from various stores on your site. This widget should be placed on the "Stores - Browse Wishlists" page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.wishlist-browse',
        'defaultParams' => array(
            'title' => '',
            'viewTypes' => array("list", "grid"),
            'statisticsWishlist' => array("viewCount", "likeCount", "followCount", "productCount"),
            'viewTypeDefault' => 'grid',
            'listThumbsCount' => 4,
        ),
        'adminForm' => 'Sitestoreproduct_Form_Admin_Widgets_WishlistBrowse',
    ),
    array(
        'title' => $view->translate('Search Wishlists Form'),
        'description' => $view->translate('Displays the form for searching wishlists. It is recommended to place this widget on Browse Wishlists page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.wishlist-browse-search',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Form Type'),
                        'multiOptions' => array(
                            'horizontal' => $view->translate('Horizontal'),
                            'vertical' => $view->translate('Vertical'),
                        ),
                        'value' => 'horizontal'
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Popular / Recent / Random Wishlists'),
        'description' => $view->translate('Displays Wishlists based on the Popularity Criteria and other settings that you choose for this widget. You can place this widget multiple times on a page with different popularity criterion chosen for each placement.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.wishlist-products',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => $view->translate('Popular Wishlists'),
            'statisticsWishlist' => array("followCount", "productCount"),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'type',
                    array(
                        'label' => $view->translate('Show Wishlists of:'),
                        'multiOptions' => array(
                            'friends' => $view->translate('Currently logged-in member’s friends.'),
                            'viewer' => $view->translate('Currently logged-in member.'),
                            'none' => $view->translate('Everyone')
                        ),
                        'value' => 'none'
                    )
                ),
                array(
                    'Select',
                    'orderby',
                    array(
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => array(
                            'total_item' => $view->translate('Having maximum number of Products'),
                            'creation_date' => $view->translate('Most Recent'),
                            'view_count' => $view->translate('Most Viewed'),
                            'like_count' => $view->translate('Most Liked'),
                            'follow_count' => $view->translate('Most Followed'),
                            'RAND()' => $view->translate('Random')
                        ),
                        'value' => 'creation_date',
                    )
                ),
                $statisticsWishlistElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Number of wishlists to show'),
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation limit'),
                        'value' => 16,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Create a New Wishlist'),
        'description' => $view->translate('Displays the link to Create a New Wishlist.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.wishlist-creation-link',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Wishlist Profile: Added Products'),
        'description' => $view->translate('Displays a list of all the products added in the wishlist being viewed. This widget should be placed on the Wishlist Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.wishlist-profile-items',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'shareOptions' => array("siteShare", "friend", "report", "print", "socialShare"),
            'viewTypes' => array("list", "pin"),
            'viewTypeDefault' => 'pin',
            'statistics' => array("likeCount", "reviewCount"),
            'statisticsWishlist' => array("viewCount", "likeCount", "followCount", "productCount"),
            'show_buttons' => array("wishlist", "comment", "like", "share", "facebook", "pinit")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'search_box',
                    array(
                        'label' => $view->translate('Do you want to display the search form for wishlists in this block?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '0',
                    )
                ),
                $ratingTypeElement,
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => $view->translate('Show created by option. (Selecting "Yes" here will display the member\'s name who has created the wishlist.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'shareOptions',
                    array(
                        'label' => $view->translate('Select the options that you want to display in this block.'),
                        'multiOptions' => array("siteShare" => "Site Share", "friend" => "Tell a Friend", "report" => "Report", 'print' => 'Print', 'socialShare' => 'Social Share'),
                    //'value' => array("siteShare","friend","report","print","socialShare"),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'viewTypes',
                    array(
                        'label' => $view->translate('Choose the view types.'),
                        'multiOptions' => array("list" => $view->translate("List View"), "pin" => $view->translate("Pinboard View")),
                    ),
                ),
                array(
                    'Radio',
                    'viewTypeDefault',
                    array(
                        'label' => $view->translate('Choose the default view type'),
                        'multiOptions' => array("list" => $view->translate("List View"), "pin" => $view->translate("Pinboard View")),
                        'value' => 'pin',
                    )
                ),
                array(
                    'Text',
                    'itemWidth',
                    array(
                        'label' => $view->translate('One product Width'),
                        'description' => $view->translate('Enter the width for each pinboard item.'),
                        'value' => 235,
                    )
                ),
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => $view->translate('Do you want to display the images without stretching them to the width of each wishlist block? (This setting will only work, if you have chosen Pinboard View from the above setting.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '0',
                    )
                ),
                $statisticsElement,
                $statisticsWishlistElement,
                array(
                    'Radio',
                    'postedbyInList',
                    array(
                        'label' => $view->translate('Show store name. (Selecting "Yes" here will display the name of the store in which the product has been created.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'followLike',
                    array(
                        'label' => $view->translate('Choose the action link to be available for wishlists displayed in this block.'),
                        'multiOptions' => array(
                            'follow' => $view->translate('Follow / Unfollow'),
                            'like' => $view->translate('Like / Unlike'),
                        ),
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => $view->translate('Choose the action links that you want to be available for each Product pinboard item.'),
                        'multiOptions' => array("wishlist" => "Wishlist", "compare" => "Compare", "comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
                    ),
                ),
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => $view->translate("Enter the trucation limit for the Product Description. (If you want to hide the description, then enter '0'.)"),
                        'value' => 100,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 10,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Profile: Product Photos Carousel'),
        'description' => $view->translate('Displays photo thumbnails in an attractive carousel, clicking on which opens the photo in lightbox. This widget should be placed on the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.photos-carousel',
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of photos to show)'),
                        'value' => 2,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
        'requirements' => array(
            'subject' => 'sitestoreproduct',
        ),
    ),
    array(
        'title' => $view->translate('Product / Review Profile: Comments & Replies'),
        'description' => $view->translate('Enable users to comment and reply on the product / review being viewed. Displays all the comments and replies on the products / reviews. This widget should be placed on Product Profile page or Review Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'seaocore.seaocores-nestedcomments',
        'defaultParams' => array(
            'title' => $view->translate('Comments')
        ),
        'requirements' => array(
            'subject',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Category Navigation Bar'),
        'description' => $view->translate('Displays categories in this block. You can configure various settings for this widget from the Edit settings.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.producttypes-categories',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'viewDisplayHR',
                    array(
                        'label' => $view->translate('Select the placement position of the navigation bar'),
                        'multiOptions' => array(
                            1 => $view->translate('Horizontal'),
                            0 => $view->translate('Vertical')
                        ),
                        'value' => 1,
                    )
                ),
//                array(
//                    'Radio',
//                    'showSubCategory',
//                    array(
//                        'label' => $view->translate('Select the display type of the category.'),
//                        'multiOptions' => array(
//                            1 => $view->translate('According to Sub Category'),
//                            0 => $view->translate('According to Category')
//                        ),
//                        'value' => 1,
//                    )
//                ),
            ))
    ),
    array(
        'title' => $view->translate('Categories Title'),
        'description' => $view->translate('Display the categories name on category home page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => false,
        'name' => 'sitestoreproduct.category-name-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Categories Banner'),
        'description' => $view->translate('Displays banners for categories, sub-categories and 3rd level categories on Browse Products page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => false,
        'name' => 'sitestoreproduct.categories-banner-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Review Profile: Owner Reviews'),
        'description' => $view->translate('Displays the other reviews posted by the owner of the review which is being viewed. This widget should be placed on Review Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.ownerreviews-sitestoreproduct',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'statistics' => array("likeCount", "replyCount", "commentCount")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => $view->translate('Choose the statistics to be displayed for the reviews in this widget.'),
                        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'replyCount' => 'Replies', 'helpfulCount' => 'Helpful'),
                    //'value' => array("likeCount","replyCount","commentCount"),
                    ),
                ),
                array(
                    'Text',
                    'count',
                    array(
                        'label' => $view->translate('Number of reviews to show'),
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 24,
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
        'title' => $view->translate('Browse Stores Product: Pinboard View'),
        'description' => 'Displays a list of all the products on site in attractive Pinboard View. You can also choose to display products based on user’s current location by using the Edit Settings of this widget. It is recommended to place this widget on “Browse Products ‘s Pinboard View” page',
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.pinboard-browse',
        'defaultParams' => array(
            'title' => $view->translate('Recent'),
            'statistics' => array("likeCount", "reviewCount", "ratingStar", "productCreationTime"),
            'show_buttons' => array("wishlist", "compare", "comment", "like", 'share', 'facebook', 'twitter', 'pinit')
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                $ratingTypeElement,
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => $view->translate('Choose the statistics that you want to be displayed for the Products in this block.'),
                        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'reviewCount' => 'Reviews', 'ratingStar' => 'Rating Stars', 'productCreationTime' => 'Product Creation Time(Show in Bottom)'),
                    ),
                ),
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => $view->translate('Show created by option. (Selecting "Yes" here will display the member\'s name who has created the product.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Select',
                    'autoload',
                    array(
                        'label' => $view->translate('Do you want to enable auto-loading of old pinboard items when users scroll down to the bottom of this page?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Select',
                    'defaultLoadingImage',
                    array(
                        'label' => $view->translate('Do you want to show a Loading image when this widget renders on a page?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'itemWidth',
                    array(
                        'label' => $view->translate('One Item Width'),
                        'description' => $view->translate('Enter the width for each pinboard item.'),
                        'value' => 235,
                    )
                ),
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => $view->translate('Do you want to display the images without stretching them to the width of each pinboard item?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '0',
                    )
                ),
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'noOfTimes',
                    array(
                        'label' => $view->translate('Auto-Loading Count'),
                        'description' => $view->translate('Enter the number of times that auto-loading of old pinboard items should occur on scrolling down. (Select 0 if you do not want such a restriction and want auto-loading to occur always. Because of auto-loading on-scroll, users are not able to click on links in footer; this setting has been created to avoid this.)'),
                        'value' => 0,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => $view->translate('Choose the action links that you want to be available for the Products displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)'),
                        'multiOptions' => array("wishlist" => "Wishlist", "compare" => "Compare", "comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
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
                        'value' => 0
                    )
                ),
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => $view->translate("Enter the trucation limit for the Product Description. (If you want to hide the description, then enter '0'.)"),
                        'value' => 0,
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
            ),
        ),
    ),
    array(
        'title' => $view->translate('Products Home: Pinboard View'),
        'description' => $view->translate('Displays products in Pinboard View on the Products Home page. Multiple settings are available to customize this widget.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.pinboard-products-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Recent'),
            'statistics' => array("likeCount", "reviewCount", "ratingStar", "productCreationTime"),
            'show_buttons' => array("wishlist", "compare", "comment", "like", 'share', 'facebook', 'twitter', 'pinit')
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'featuredIcon',
                    array(
                        'label' => $view->translate('Do you want to show the featured icon / label? (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'sponsoredIcon',
                    array(
                        'label' => $view->translate('Do you want to show the sponsored icon / label? (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'newIcon',
                    array(
                        'label' => $view->translate('Do you want to show the new icon / label. (You can choose the marker from the \'Global Settings\' section in the Admin Panel.)'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                $ratingTypeElement,
                $featuredSponsoredElement,
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => $view->translate('Popularity Criteria'),
                        'multiOptions' => $popularity_options,
                        'value' => 'product_id',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => $view->translate('Popularity Duration (This duration will be applicable to these Popularity Criteria:  Most Liked, Most Commented, Most Rated and Most Recent.)'),
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => $view->translate('Choose the statistics that you want to be displayed for the Products in this block.'),
                        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'reviewCount' => 'Reviews', 'ratingStar' => 'Rating Stars', 'productCreationTime' => 'Product Creation Time(Show in Bottom)'),
                    ),
                ),
                array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => $view->translate('Show created by option. (Selecting "Yes" here will display the member\'s name who has created the product.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Select',
                    'autoload',
                    array(
                        'label' => $view->translate('Do you want to enable auto-loading of old pinboard items when users scroll down to the bottom of this page?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1'
                    )
                ),
                array(
                    'Select',
                    'defaultLoadingImage',
                    array(
                        'label' => $view->translate('Do you want to show a Loading image when this widget renders on a page?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'itemWidth',
                    array(
                        'label' => $view->translate('One Item Width'),
                        'description' => $view->translate('Enter the width for each pinboard item.'),
                        'value' => 235,
                    )
                ),
                array(
                    'Radio',
                    'withoutStretch',
                    array(
                        'label' => $view->translate('Do you want to display the images without stretching them to the width of each pinboard item?'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '0',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'noOfTimes',
                    array(
                        'label' => $view->translate('Auto-Loading Count'),
                        'description' => $view->translate('Enter the number of times that auto-loading of old pinboard items should occur on scrolling down. (Select 0 if you do not want such a restriction and want auto-loading to occur always. Because of auto-loading on-scroll, users are not able to click on links in footer; this setting has been created to avoid this.)'),
                        'value' => 0,
                    )
                ),
                array(
                    'MultiCheckbox',
                    'show_buttons',
                    array(
                        'label' => $view->translate('Choose the action links that you want to be available for the Products displayed in this block. (This setting will only work, if you have chosen Pinboard View from the above setting.)'),
                        'multiOptions' => array("wishlist" => "Wishlist", "compare" => "Compare", "comment" => "Comment", "like" => "Like / Unlike", 'share' => 'Share', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'pinit' => 'Pin it', 'tellAFriend' => 'Tell a Friend', 'print' => 'Print')
                    //'value' =>array("viewCount","likeCount","commentCount","reviewCount"),
                    ),
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
                        'value' => 0
                    )
                ),
                array(
                    'Text',
                    'truncationDescription',
                    array(
                        'label' => $view->translate("Enter the trucation limit for the Product Description. (If you want to hide the description, then enter '0'.)"),
                        'value' => 0,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Manage Cart',
        'description' => 'Manage Cart',
        'category' => 'Stores / Marketplace - Products',
        'type' => 'widget',
        'name' => 'sitestoreproduct.manage-cart',
    ),
    array(
        'title' => 'Checkout Process',
        'description' => 'This widget displays the step by step information entered by the user during the checkout process. Each checkout step has an edit link to edit the information. This widget should be placed on Checkout page.',
        'category' => 'Stores / Marketplace - Products',
        'type' => 'widget',
        'name' => 'sitestoreproduct.checkout-process',
    ),
//    array(
//        'title' => $view->translate('Shopping Cart'),
//        'description' => $view->translate('Display the shopping cart of current viewer.'),
//        'category' => $view->translate('Stores / Marketplace - Products'),
//        'type' => 'widget',
////        'autoEdit' => true,
//        'name' => 'sitestoreproduct.shopping-cart',
//        'defaultParams' => array(
//            'title' => $view->translate('Shopping Cart'),
//            'titleCount' => true,
//        ),
//        'adminForm' => array(
//            'elements' => array(
//                array(
//                    'select',
//                    'position',
//                    array(
//                        'label' => $view->translate('Position'),
//                        'multiOptions' => array(
//                            0 => $view->translate('Left'),
//                            1 => $view->translate('Right'),
//                            2 => $view->translate('Bottom-Left'),
//                            3 => $view->translate('Bottom-Right')
//                        ),
//                        'value' => 0,
//                    )
//                ),
//                array(
//                    'text',
//                    'margin',
//                    array(
//                        'label' => $view->translate('Margin'),
//                    )
//                ),
//            )
//        ),
//    ),
    array(
        'title' => 'Store Sales Figures (Dashboard)',
        'description' => 'This widget displays the store sales figures of the current day, week and month to the store admins. This widget should be placed on statistics page.',
        'category' => 'Stores / Marketplace - Products',
        'type' => 'widget',
        'name' => 'sitestoreproduct.statistics-box',
    ),
    array(
        'title' => $view->translate('Recent Orders'),
        'description' => $view->translate('Recent Orders'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.latest-orders',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => $view->translate('Recent Orders'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Orders'),
                        'description' => $view->translate('(number of latest orders to display in widget.Enter 0 for displaying all orders.)'),
                        'value' => 5,
                    )
                )
            ))
    ),
    array(
        'title' => $view->translate('My Cart'),
        'description' => $view->translate('This widget displays the products in the cart of user currently viewing this widget. From the edit settings you can choose the number of products to be shown in this widget.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.my-cart',
        'defaultParams' => array(
            'title' => $view->translate('My Cart'),
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Number of products to show'),
                        'value' => 5,
                    )
                ),
            )
        )
    ),
    array(
        'title' => $view->translate('Top Selling Stores'),
        'description' => $view->translate('This widget shows the top stores on your site based on the number of products sold by them. Multiple settings are available for this widget.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.top-selling-store',
        'defaultParams' => array(
            'title' => $view->translate('Top Selling Stores'),
            'titleCount' => true,
            'autoEdit' => true,
//            'loaded_by_ajax' => 1
        ),
        'adminForm' => array(
            'elements' => array(
//                array(
//                    'Radio',
//                    'loaded_by_ajax',
//                    array(
//                        'label' => $view->translate('Widget Content Loading'),
//                        'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)'),
//                        'multiOptions' => array(
//                            1 => $view->translate('Yes'),
//                            0 => $view->translate('No')
//                        ),
//                        'value' => 1,
//                    )
//                ),
//                array(
//                    'Select',
//                    'category_id',
//                    array(
//                        'label' => 'Category',
//                        'multiOptions' => $store_categories_prepared,
//                    )
//                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => $view->translate('Popularity Duration'),
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                $storeCategoryElement,
                $storeSubCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $statisticsStoreElement,
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 25,
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Choose the View Type for stores.'),
                        'multiOptions' => array(
                            'listview' => $view->translate('List View'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                        'value' => 'gridview',
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '328',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Number of products to show'),
                        'value' => 5,
                    )
                ),
                array(
                    'Radio',
                    'display_by',
                    array(
                        'label' => $view->translate('Show Stores Based On'),
                        'multiOptions' => array(
                            1 => $view->translate('Maximum Sales (Amount)'),
                            0 => $view->translate('Maximum Sales (Products)')
                        ),
                        'value' => 0,
                    )
                ),
            )
        )
    ),
    array(
        'title' => $view->translate('Recently Sold / Top Selling Products'),
        'description' => $view->translate('Displays Products based on the various settings that you choose for this widget. You can place this widget multiple times on a page with different criterion chosen for each placement.'),
        'category' => $view->translate('Stores / Marketplace - Stores'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.sitestoreproduct-products',
        'defaultParams' => array(
            'title' => $view->translate('Recently Selled Products'),
            'titleCount' => true,
//            'statistics' => array("likeCount", "reviewCount"),
            'viewType' => 'gridview',
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'popularity',
                    array(
                        'label' => $view->translate('Show Products'),
                        'multiOptions' => array(
                            'last_order_all' => 'Recently ordered',
                            'last_order_viewer' => 'Recently ordered by current viewer',
                            'top_selling' => 'Top selling',
                        ),
                        'value' => 'last_order_all',
                    )
                ),
                array(
                    'Select',
                    'product_type',
                    array(
                        'label' => $view->translate('Product Type'),
                        'multiOptions' => $product_type_options,
                        'value' => 'all',
                    )
                ),
                array(
                    'Select',
                    'interval',
                    array(
                        'label' => $view->translate('Popularity Duration'),
                        'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
                        'value' => 'overall',
                    )
                ),
                $categoryElement,
                $subCategoryElement,
                $hiddenCatElement,
                $hiddenSubCatElement,
                $hiddenSubSubCatElement,
                $ratingTypeElement,
                $statisticsElement,
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Choose the View Type for products.'),
                        'multiOptions' => array(
                            'listview' => $view->translate('List View'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                        'value' => 'gridview',
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '180',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '328',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Number of Products to show'),
                        'value' => 3,
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
            ),
        ),
    ),
    array(
        'title' => $view->translate('Store Statistics'),
        'description' => $view->translate('Displays the current statistics like total commision paid, total tax, total sales based on duration, etc for the Store being viewed. This widget should be based on the Store Dashboard page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.store-overview',
        'defaultParams' => array(
            'title' => $view->translate('Store Statistics'),
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Manage Products'),
        'description' => $view->translate('This widget forms the Products tab on the Store Profile and displays the products of the Store. It should be placed in the Tabbed Blocks area of the Store Profile.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.store-profile-products',
        'defaultParams' => array(
            'title' => 'Products',
            'layouts_views' => array("1", "2", "3"),
            'layouts_order' => 1,
            // 'statistics' => array("viewCount", "likeCount", "commentCount", "reviewCount"),
            'columnWidth' => '197',
            'truncationGrid' => 90
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'ajaxView',
                    array(
                        'label' => $view->translate('Widget Content Loading'),
                        'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 0,
                    )
                ),
                $ratingTypeElement,
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => $view->translate('Choose the view types that you want to be available for products.'),
                        'multiOptions' => array("1" => $view->translate("List View"), "2" => $view->translate("Grid View"), "3" => $view->translate("Map View")),
                        'value' => array("0" => "1", "1" => "2"),
                    ),
                ),
                array(
                    'Radio',
                    'layouts_order',
                    array(
                        'label' => $view->translate('Select a default view type for Products.'),
                        'multiOptions' => array("1" => $view->translate("List View"), "2" => $view->translate("Grid View"), "3" => $view->translate("Map View")),
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '165',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '325',
                    )
                ),
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => $view->translate('Choose the statistics that you want to be displayed for the Products in this block.'),
                        'multiOptions' => array("likeCount" => "Likes", 'viewRating' => 'Ratings'),
                    ),
                ),
                array(
                    'MultiCheckbox',
                    'allowed_product_types',
                    array(
                        'label' => $view->translate('Choose the product types, which products you want to be display.'),
                        'multiOptions' => array(
                            'simple' => 'Simple Products',
                            'grouped' => 'Grouped Products',
                            'configurable' => 'Configurable Products',
                            'virtual' => 'Virtual Products',
                            'bundled' => 'Bundled Products',
                            'downloadable' => 'Downloadable Products',
                        ),
                    ),
                ),
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show "Add to Cart" button OR "Out of Stock" for "In Stock" and "Out of Stock" products respectively?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'showLocation',
                    array(
                        'label' => $view->translate('Do you want to show the location of the products?'),
                        'multiOptions' => array(1 => $view->translate('Yes'), 0 => $view->translate('No')),
                        'value' => '0',
                    )
                ),
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => $view->translate('Default ordering in Browse Products. (Note: Selecting multiple ordering will make your page load slower.)'),
                        'multiOptions' => array(
                            'product_id' => $view->translate('All products in descending order of creation.'),
                            'view_count' => $view->translate('All products in descending order of views.'),
                            'title' => $view->translate('All products in alphabetical order.'),
                            'sponsored' => $view->translate('Sponsored products followed by others in descending order of creation.'),
                            'featured' => $view->translate('Featured products followed by others in descending order of creation.'),
                            'fespfe' => $view->translate('Sponsored & Featured products followed by Sponsored products followed by Featured products followed by others in descending order of creation.'),
                            'spfesp' => $view->translate('Featured & Sponsored products followed by Featured products followed by Sponsored products followed by others in descending order of creation.'),
                            'newlabel' => $view->translate('Products marked as New followed by others in descending order of creation.'),
                            'highlighted' => $view->translate('Highlighted products followed by other in decending order of creation.'),
                        ),
                        'value' => 'sponsored',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 9,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 50,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'Text',
                    'truncationGrid',
                    array(
                        'label' => $view->translate('Title Truncation Limit in Grid View'),
                        'value' => 90,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                array(
                    'MultiCheckbox',
                    'searchByOptions',
                    array(
                        'label' => 'Select the search options you want to display.',
                        'multiOptions' => array(
                            '1' => 'Search',
                            '2' => 'Browse by',
                            '3' => 'Browse by Category',
                            '4' => 'Browse by Section',
                            '5' => "Downpayment (This setting will only work when downpayment is enable and 'Payment for Orders' should be 'Direct Payment to Sellers' from the 'Global Settings' section in the Admin Panel.)",
                            '6' => 'Location',
                        ),
                    ),
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement
            ),
        ),
    ),
    array(
        'title' => $view->translate('Store Startup Link'),
        'description' => $view->translate('Displays link for the store startup page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.store-startup-link',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Store Product Link'),
        'description' => $view->translate('Displays link for the store product page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.store-product-link',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => $view->translate('Categories: Categories in Grid View'),
        'description' => $view->translate('Displays the Categories in grid view at categories page and Sub-categories  at category home page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.categories-grid-view',
        'defaultParams' => array(
            'title' => $view->translate('Categories'),
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'showSubCategoriesCount',
                    array(
                        'label' => $view->translate('Show number of Sub-Categories in a Category on mouse hover.'),
                        'value' => '5',
                        'maxlength' => 1
                    )
                ),
                array(
                    'Radio',
                    'showCount',
                    array(
                        'label' => $view->translate('Show Products count along with Categories, Sub-categories and 3rd level categories.
'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '234',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '216',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => $view->translate('Store Startup Sub Page'),
        'description' => $view->translate('Displays the chosen sub page from the store startup pages as selected by you from the edit settings of this widget. You can edit the page content from the "Store Startup Pages" section of this plugin.'),
        'category' => $view->translate('Stores / Marketplace - Stores'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.store-startuppage',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'page_id',
                    array(
                        'label' => 'Page',
                        'multiOptions' => array(1 => "Get Started", 2 => "Basics", 3 => "Success Stories", 4 => "Tools")
                    ))
            ),
        ),
    ),
    array(
        'title' => 'Product Profile Contact Details',
        'description' => "Displays the Contact Details of a product. This widget should be placed on the Product Profile.",
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.contactdetails-sitestoreproduct',
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
                        'label' => 'Do you want users to send emails to Products via a customized pop up when they click on "Email Me" link?',
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
        'title' => $view->translate('Member Profile: Wishlist Added Products'),
        'description' => $view->translate('Displays a list of all the products added in the wishlist being viewed. This widget should be placed on the Wishlist Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.wishlist-member-profile-items',
        'defaultParams' => array(
            'title' => 'Products',
            'titleCount' => true,
            'statistics' => array("viewCount", "likeCount", "commentCount", "reviewCount"),
            'viewType' => 'gridview',
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                $ratingTypeElement,
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => $view->translate('Choose the statistics that you want to be displayed for the Products in this block.'),
                        'multiOptions' => array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", 'reviewCount' => 'Reviews', 'viewRating' => 'Ratings'),
                    ),
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => $view->translate('Title Truncation Limit'),
                        'value' => 35,
                    )
                ),
                array(
                    'Radio',
                    'add_to_cart',
                    array(
                        'label' => $view->translate('Do you want to show Add to Cart button.'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the in stock quantity.'),
                        //'description' => $view->translate('(If selected "No", only one review will be displayed from a reviewer.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => $view->translate('Choose the View Type for products.'),
                        'multiOptions' => array(
                            'listview' => $view->translate('List View'),
                            'gridview' => $view->translate('Grid View'),
                        ),
                        'value' => 'gridview',
                    )
                ),
                array(
                    'Text',
                    'columnWidth',
                    array(
                        'label' => $view->translate('Column Width For Grid View.'),
                        'value' => '165',
                    )
                ),
                array(
                    'Text',
                    'columnHeight',
                    array(
                        'label' => $view->translate('Column Height For Grid View.'),
                        'value' => '325',
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 10,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Store Profile Store Sections',
        'description' => 'Displays all the Sections of the Store being currently viewed. After clicking on any Section, user will be redirected to the Manage Products tab placed on the Stores - Store Profile page. This widget should be placed in the right / left column of the Stores - Store Profile page. (Note: If you are placing this widget, then please make sure that you also place the “Manage Products” widget.)',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestoreproduct.section-list',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ), 'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'limit',
                    array(
                        'label' => $view->translate('Section Count'),
                        'description' => $view->translate('Enter the number of Sections to be shown in this block'),
                        'value' => 5,
                    ),
                ), array(
                    'Radio',
                    'order',
                    array(
                        'label' => 'Choose the order of Sections to be shown in this block.',
                        'multiOptions' => array(
                            1 => 'Descending order',
                            0 => 'Ascending order'
                        ),
                        'value' => 1,
                    )
                ), array(
                    'MultiCheckbox',
                    'product',
                    array(
                        'label' => 'Do you want to show Sections according to the maximum number of products in them?',
                        'multiOptions' => array("product" => "Product"),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Product Profile: Left / Right Column Map',
        'description' => 'This widget displays the map showing location of the Product being currently viewed. It should be placed in the left / right column of the Stores - Product Profile page.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestoreproduct.location-sidebar-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'height',
                    array(
                        'label' => 'Enter the height of the map (in pixels).',
                        'value' => 200,
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Product Profile: Map',
        'description' => 'This widget forms the Map tab on the Product Profile page. It displays the map showing the product position as well as the location details of the product. It should be placed in the Tabbed Blocks area of the Stores - Product Profile page.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestoreproduct.location-sitestoreproduct',
        'defaultParams' => array(
            'title' => 'Map',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Search Products Location Form',
        'description' => 'Displays the form for searching Products corresponding to location on the basis of various filters.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestoreproduct.location-search',
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
        'title' => 'Browse Products’ Locations',
        'description' => 'Displays a list of all the product having location entered corresponding to them on the site. This widget should be placed on Browse Products’ Locations store.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestoreproduct.browselocation-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
);
$video_widgets = array(
    array(
        'title' => $view->translate('Video View Page: People Also Liked'),
        'description' => $view->translate('Displays a list of other Product Videos that the people who liked this Product Video also liked. You can choose the number of entries to be shown. This widget should be placed on Video View Page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.show-also-liked',
        'defaultParams' => array(
            'title' => $view->translate('People Also Liked'),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of videos to show)'),
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
        'requirements' => array(
            'subject' => 'sitestoreproduct',
        ),
    ),
    array(
        'title' => $view->translate('Video View Page: Other Videos From Product'),
        'description' => $view->translate('Displays a list of other Product Videos corresponding to the Product of which the video is being viewed. You can choose the number of entries to be shown. This widget should be placed on Video View Page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.show-same-poster',
        //'isPaginated' => true,
        'defaultParams' => array(
            'title' => $view->translate('Other Videos From Product'),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of videos to show)'),
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
        'requirements' => array(
            'subject' => 'sitestoreproduct',
        ),
    ),
    array(
        'title' => $view->translate('Video View Page: Similar Videos'),
        'description' => $view->translate('Displays Product Videos similar to the Product Video being viewed based on tags. You can choose the number of entries to be shown. This widget should be placed on Video View Page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.show-same-tags',
        'defaultParams' => array(
            'title' => $view->translate('Similar Videos'),
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of videos to show)'),
                        'value' => 3,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Product Video View'),
        'description' => $view->translate("This widget should be placed on the Video View page."),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.video-content',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
);
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
  $ads_Array = array(
      array(
          'title' => $view->translate('Review Ads Widget'),
          'description' => $view->translate('Displays community ads and links to view various pages of Advertisements / Community Ads plugin.'),
          'category' => $view->translate('Stores / Marketplace - Products'),
          'type' => 'widget',
          'name' => 'sitestoreproduct.review-ads',
          'defaultParams' => array(
              'title' => '',
              'titleCount' => true,
          ),
          'adminForm' => array(
              'elements' => array(
              ),
          ),
  ));
}
if (empty($type_video)) {
  $final_array = array_merge($final_array, $video_widgets);
}
if (!empty($ads_Array)) {
  $final_array = array_merge($final_array, $ads_Array);
}
return $final_array;
