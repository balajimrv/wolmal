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

if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.proximity.search.kilometer', 0)) {
  $locationDescription = "Choose the kilometers within which products will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
  $locationLableS = "Kilometer";
  $locationLable = "Kilometers";
} else {
  $locationDescription = "Choose the miles within which products will be displayed. (This setting will only work, if you have chosen 'Yes' in the above setting.)";
  $locationLableS = "Mile";
  $locationLable = "Miles";
}

$detactLocationElement =                 array(
                    'Select',
                    'detactLocation',
                    array(
                        'label' => 'Do you want to display listings based on user’s current location?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => '0'
                    )
                );
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

    if (count($storeCategories) != 0) {
      $storeCategories_prepared[0] = "";
      foreach ($storeCategories as $category) {
        $storeCategories_prepared[$category->category_id] = $category->category_name;
      }

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
    'top_selling' => $view->translate('Top Selling'),
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
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
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
        'title' => $view->translate('Member Profile: Profile Products'),
        'description' => $view->translate('Displays a member\'s products on their profile. This widget should be placed in the Tabbed Blocks area of Member Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.profile-sitestoreproduct',
        'defaultParams' => array(
            'title' => 'Products',
            'titleCount' => true,
            'statistics' => array(),
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
                        'value' => '225',
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
        ),
        'adminForm' => array(
            'elements' => array(
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
        'title' => $view->translate('Product Profile: Product Videos'),
        'description' => $view->translate('This widget forms the Videos tab on the Product Profile page and displays the videos of the product. This widget should be placed in the Tabbed Blocks area of the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.video-sitestoreproduct',
        'defaultParams' => array(
            'title' => 'Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
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
        'title' => $view->translate('Product Profile: Owner Products'),
        'description' => $view->translate('Displays a list of other products owned by the product owner. This widget should be placed on Reviews: Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.userproduct-sitestoreproduct',
        'defaultParams' => array(
            'title' => "%s's Products",
            'titleCount' => true,
            'statistics' => array("likeCount", "reviewCount","commentCount"),
            'viewType' => 'gridview',
            'columnHeight' => '280',
            'count' =>20          
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
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available stock quantity.'),
                        'multiOptions' => array( 1 => $view->translate('Yes'), 0 => $view->translate('No') ),
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
                        'value' => '280',
                    )
                ),                
                array(
                    'Text',
                    'count',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 20,
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
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available quantity for the "In Stock" products?'),
                        'multiOptions' => array( 1 => $view->translate('Yes'), 0 => $view->translate('No') ),
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
        'title' => $view->translate('Product Profile: About Product'),
        'description' => $view->translate('Displays the About Product information for products as entered by product owners. This widget should be placed on the Product Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.write-sitestoreproduct',
        'defaultParams' => array(
            'title' => 'About Product',
            'titleCount' => true,
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
            'layouts_views' => array("1", "2"),
            'layouts_order' => 1,
            'statistics' => array("viewCount", "likeCount", "commentCount", "reviewCount"),
            'columnWidth' => '180',
            'truncationGrid' => 25,
            'viewType'=>'gridview'
            
        ),
        'adminForm' => array(
            'elements' => array(
                $ratingTypeElement,
                array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => $view->translate('Choose the view types that you want to be available for products.'),
                        'multiOptions' => array("1" => $view->translate("List View"), "2" => $view->translate("Grid View")),
                    ),
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
                        'value' => '225',
                    )
                ),                     
                $statisticsElement,
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available stock quantity.'),
                        'multiOptions' => array( 1 => $view->translate('Yes'), 0 => $view->translate('No') ),
                        'value' => '1',
                    )
                ),
                 array(
                    'Radio',
                    'postedby',
                    array(
                        'label' => $view->translate("Show created by option. (Selecting 'Yes' here will display the member's name who has created the product.)"),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
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
                        'value' => 25,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
                $detactLocationElement,
                $defaultLocationDistanceElement,
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
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available stock quantity.'),
                        'multiOptions' => array( 1 => $view->translate('Yes'), 0 => $view->translate('No') ),
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
            'title' => $view->translate('Products'),
            'titleCount' => true,
            'statistics' => array("likeCount", "reviewCount"),
            'layouts_views' => array("1","2"),
            'viewType' => 'gridview',
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                 array(
                    'MultiCheckbox',
                    'layouts_views',
                    array(
                        'label' => $view->translate('Choose the view types that you want to be available for products.'),
                        'multiOptions' => array("1" => $view->translate("List View"), "2" => $view->translate("Grid View")),
                    ),
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
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available stock quantity.'),
                        'multiOptions' => array( 1 => $view->translate('Yes'), 0 => $view->translate('No') ),
                        'value' => '1',
                    )
                ),
                $ratingTypeElement,
                $featuredSponsoredElement,               
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
                        'value' => '225',
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
//                $detactLocationElement,
//                $defaultLocationDistanceElement,
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
            '_hasMobileMode' => 'pack',
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
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available stock quantity.'),
                        'multiOptions' => array( 1 => $view->translate('Yes'), 0 => $view->translate('No') ),
                        'value' => '1',
                    )
                ),
                array(
                    'Radio',
                    'show',
                    array(
                        'label' => $view->translate('Show recently viewed products of:'),
                        'multiOptions' => array(
                            '1' => $view->translate('Currently logged-in member’s friends.'),
                            '0' => $view->translate('Currently logged-in member.'),
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
                $statisticsElement,
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
        ),
        'adminForm' => array(
            'elements' => array(
            )
        )
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
            'statistics' => array("likeCount", "reviewCount","commentCount"),
            'viewType' => 'gridview',
            'columnHeight' => '280',
            'count' =>20  
        ),
        'adminForm' => array(
            'elements' => array(
                $ratingTypeElement,
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available stock quantity.'),
                        'multiOptions' => array( 1 => $view->translate('Yes'), 0 => $view->translate('No') ),
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
                        'value' => '280',
                    )
                ),                  
                $statisticsElement,
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of Products to show)'),
                        'value' => 20,
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
                    array()
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
        'title' => $view->translate('Editor Profile: Similar Editor'),
        'description' => $view->translate('Displays Editors similar to the Editors whose profile is being viewed. Multiple settings are available to customize this widget. This widget should be placed on Editor Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.editors-sitestoreproduct',
        'defaultParams' => array(
            'title' => $view->translate('Similar Editors'),
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(              
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
        'title' => $view->translate('Editor Profile: Editor’s Member Profile Photo, Name, Description and Designation'),
        'description' => $view->translate('Displays Editors’ member profile photo, name, about, details and designation on their editor profile. This widget should be placed on Products - Editor Profile page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'name' => 'sitestoreproduct.editor-photo-sitestoreproduct',
        'defaultParams' => array(
            'title' => '',
            'showContent' => array("photo", "title", "about", "details", "designation", "emailMe")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'showContent',
                    array(
                        'label' => $view->translate('Select the information options that you want to be available in this block.'),
                        'multiOptions' => array("photo" => "Photo", "title" => "Title", "about" => "About", "details" => "Description", "designation" => "Designation", "forEditor" => "For Editor", "emailMe" => "Email Me"),
                    ),
                ),
            )
        )
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
//                        'value' => 0,
//                    )
//                ),
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
                ),array(
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
            )
        )
    ),
//    array(
//        'title' => $view->translate('Product Profile: Product Rating'),
//        'description' => $view->translate('This widget displays the overall rating given to the product by editors, member of your site and other users along with the rating parameters as configured by you from the Reviews & Ratings section in the Admin Panel. You can choose who should be able to give review from the Admin Panel. Multiple settings are available to customize this widget. This widget should be placed in the left column on the Product Profile page.'),
//        'category' => $view->translate('Stores / Marketplace - Products'),
//        'type' => 'widget',
//        'name' => 'sitestoreproduct.overall-ratings',
//        'defaultParams' => array(
//            'title' => 'Reviews',
//            'titleCount' => true,
//        ),
//        'adminForm' => array(
//            'elements' => array(
//                array(
//                    'Radio',
//                    'show_rating',
//                    array(
//                        'label' => $view->translate('Select from below type of ratings to be displayed in this widget'),
//                        'multiOptions' => array(
//                            'avg' => $view->translate('Combined Editor and User Rating'),
//                            'both' => $view->translate('Editor and User Ratings separately'),
//                            'editor' => $view->translate('Only Editor Ratings'),
//                        ),
//                        'value' => 'avg',
//                    ),
//                ),
//                array(
//                    'Radio',
//                    'ratingParameter',
//                    array(
//                        'label' => $view->translate('Do you want to show Rating Parameters in this widget?'),
//                        'multiOptions' => array(
//                            1 => $view->translate('Yes'),
//                            0 => $view->translate('No')
//                        ),
//                        'value' => 1,
//                    )
//                ),
//            ),
//        )
//    ),
    array(
        'title' => $view->translate('Browse Wishlists'),
        'description' => $view->translate('Displays a list of all the wishlists on your site. This widget should be placed on the Browse Wishlists page.'),
        'category' => $view->translate('Stores / Marketplace - Products'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.wishlist-browse',
        'defaultParams' => array(
            'title' => '',
            'statisticsWishlist' => array("viewCount", "likeCount", "followCount", "productCount"),
        ),
        'adminForm' => array(
            'elements' => array(
                $statisticsWishlistElement,       
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Number of wishlists to show per page'),
                        'value' => 20,
                    )
                ),
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
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available stock quantity.'),
                        'multiOptions' => array( 1 => $view->translate('Yes'), 0 => $view->translate('No') ),
                        'value' => '1',
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
        'title' => $view->translate('Manage Products'),
        'description' => $view->translate('This widget forms the Products tab on the Store Profile and displays the products of the Store. It should be placed in the Tabbed Blocks area of the Store Profile.'),
        'category' => $view->translate('Stores / Marketplace - Store Profile'),
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'sitestoreproduct.store-profile-products',
        'defaultParams' => array(
            'title' => $view->translate('Products'),
            'titleCount' => true,
            'statistics' => array("likeCount", "reviewCount"),
            'columnWidth' => '180'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'in_stock',
                    array(
                        'label' => $view->translate('Do you want to show the available stock quantity.'),
                        'multiOptions' => array( 1 => $view->translate('Yes'), 0 => $view->translate('No') ),
                        'value' => '1',
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
                        'value' => 25,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
            ),
        ),
    ), 
);

$video_widgets = array(
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


if (empty($type_video)) {
  $final_array = array_merge($final_array, $video_widgets);
}

if (!empty($ads_Array)) {
  $final_array = array_merge($final_array, $ads_Array);
}

return $final_array;
