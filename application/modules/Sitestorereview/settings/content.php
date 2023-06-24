<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.isActivate', 0);
if(empty($isActive)){ return; }

$categories = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategories();
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => 'Store Profile Popular Reviews',
        'description' => 'Displays list of Store\'s popular reviews. Setting for this widget is available in widget settings tab of Store - Reviews admin.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorereview.popular-sitestorereviews',
        'defaultParams' => array(
            'title' => 'Most Popular Reviews',
            'titleCount' => true,
        ),
			'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of reviews to show)',
										'value' => 3,
								)
						),
					),
        ),
    ),
    array(
        'title' => 'Store Profile Most Commented Reviews',
        'description' => 'Displays list of Store\'s most commented reviews. Setting for this widget is available in widget settings tab of Store - Reviews admin.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorereview.comment-sitestorereviews',
        'defaultParams' => array(
            'title' => 'Most Commented Reviews',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of reviews to show)',
										'value' => 3,
								)
						),
					),
        ),
    ),
    array(
        'title' => 'Store Profile Most Liked Reviews',
        'description' => 'Displays list of Store\'s most liked reviews. Setting for this widget is available in widget settings tab of Store - Reviews admin.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorereview.like-sitestorereviews',
        'defaultParams' => array(
            'title' => 'Most Liked Reviews',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of reviews to show)',
										'value' => 3,
								)
						),
					),
        ),
    ),
    array(
        'title' => 'Store Profile Reviews',
        'description' => 'This widget forms the Reviews tab on the Store Profile and displays the reviews of the Store. It should be placed in the Tabbed Blocks area of the Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorereview.profile-sitestorereviews',
        'defaultParams' => array(
            'title' => 'Reviews',
        ),
    ),
    array(
        'title' => 'Store Profile Review Rating',
        'description' => 'Displays a Store\'s review ratings on it\'s profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorereview.ratings-sitestorereviews',
        'defaultParams' => array(
            'title' => 'Ratings',
        ),
    ),
    array(
        'title' => 'Top Rated Stores',
        'description' => 'Displays the top rated Stores of the site.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.topratedstores-sitestorereviews',
        'defaultParams' => array(
            'title' => 'Top Rated Stores',
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
        'title' => 'Recent Reviews',
        'description' => 'Displays the most recent reviews of the site.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.recent-sitestorereviews',
        'defaultParams' => array(
            'title' => 'Recent Reviews',
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of reviews to show)',
										'value' => 3,
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
        'title' => 'Top Reviewers',
        'description' => 'This widget shows the top reviewers for the Stores on your site based on the number of reviews posted by them.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.reviewer-sitestorereviews',
        'defaultParams' => array(
            'title' => 'Top Reviewers',
        ),
        'adminForm' => array(
            'elements' => array(
							array(
									'Text',
									'itemCount',
									array(
											'label' => 'Count',
											'description' => '(number of reviewers to show)',
											'value' => 3,
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
        'title' => 'Featured Reviews',
        'description' => 'Displays Featured Reviews as chosen by you from the Manage Ratings & Reviews section in the admin panel of this extension.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.featured-sitestorereviews',
        'defaultParams' => array(
            'title' => 'Featured Reviews',
        ),
        'adminForm' => array(
            'elements' => array(
							array(
									'Text',
									'itemCount',
									array(
											'label' => 'Count',
											'description' => '(number of reviews to show)',
											'value' => 3,
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
        'title' => 'Review of the Day',
        'description' => 'Displays the Review of the Day for Stores as selected by the Admin from the widget settings section of Reviews and Ratings Extension.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.review-of-the-day',
        'defaultParams' => array(
            'title' => 'Review of the Day'
        ),
    ),
    array(
        'title' => 'AJAX Tabbed Reviews widget',
        'description' => 'This tabbed AJAX widget concisely shows important information about reviews in 3 tabs: Recent, Popular, Top Reviewers.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.review-tabs',
        'defaultParams' => array(
            'title' => 'People\'s Reviews',
						'visibility' => array("recent","popular","reviewer")
        ),
        'adminForm' => array(
            'elements' => array(
							array(
									'Text',
									'itemCount',
									array(
											'label' => 'Count',
											'description' => '(No. of Elements)',
											'value' => 3,
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
													'comment_count' => 'Comments'
											),
											'value' => 'view_count',
									)
							),
							array(
									'MultiCheckbox',
									'visibility',
									array(
											'label' => 'Tabs?',
											'multiOptions' => array(
													'recent' => 'Recent',
													'popular' => 'Popular',
													'reviewer' => 'Top Reviewers',
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
        'title' => 'Popular Reviews',
        'description' => 'Displays popular reviews for Stores on your site. From the edit popup of this widget, you can set the number of reviews to show in this widget and the criteria for popularity.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.site-popular-reviews',
        'defaultParams' => array(
            'title' => 'Popular Reviews',
        ),
        'adminForm' => array(
            'elements' => array(
							array(
									'Text',
									'itemCount',
									array(
											'label' => 'Count',
											'description' => '(number of reviews to show)',
											'value' => 3,
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
													'comment_count' => 'Comments'
											),
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
            ),
        ),
    ),
    
    array(
			'title' => 'Store Review View',
			'description' => "This widget should be placed on the Store Review View Page.",
      'category' => 'Stores / Marketplace - Stores',
			'type' => 'widget',
			'name' => 'sitestorereview.review-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
	  ),

    array(
        'title' => 'Store Reviews',
        'description' => 'Displays the list of Reviews from Stores created on your community. This widget should be placed in the widgetized Store Reviews store. Results from the Search Store Reviews form are also shown here.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.sitestore-review',
        'defaultParams' => array(
            'title' => 'Reviews',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of reviews to show)',
                        'value' => 20,
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
			'title' => 'Search Store Reviews form',
			'description' => 'Displays the form for searching Store Reviews on the basis of various filters. You can edit the fields to be available in this form.',
			'category' => 'Stores / Marketplace - Stores',
			'type' => 'widget',
			'name' => 'sitestorereview.search-sitestorereview',
			'defaultParams' => array(
					'title' => '',
          'search_column' => array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5'),
					'titleCount' => true,

			),
			'adminForm' => array(
              'elements' => array(
							array(
									'MultiCheckbox',
									'search_column',
									array(
											'label' => 'Choose the fields that you want to be available in the Search Store Reviews form widget.',
											'multiOptions' => array("1" => "Show","2" => "Browse By", "3" => "Store Title", "4" => "Review Title", "5" => "Store Category"),
									),
							),
					),
			)
    ),

    array(
        'title' => 'Most Commented Reviews',
        'description' => "Displays the Most Commented Store Reviews. You can choose the number of entries to be shown.",
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.homecomment-sitestorereviews',
        'defaultParams' => array(
            'title' => 'Most Commented Reviews',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of reviews to show)',
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
					),
        ),
    ),

    array(
        'title' => 'Most Liked Reviews',
        'description' => "Displays the Most Liked Store Reviews. You can choose the number of entries to be shown.",
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.homelike-sitestorereviews',
        'defaultParams' => array(
            'title' => 'Most Liked Reviews',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of reviews to show)',
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
					),
        ),
    ),

    array(
        'title' => 'Review Details',
        'description' => "Displays overall as well as parametric reviews in detailed manner.",
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.sitestore-review-detail',
        'defaultParams' => array(
            'title' => 'Review Details',
            'titleCount' => true,
        ),
    ),

    array(
        'title' => 'Store’s Featured Reviews Slideshow',
        'description' => 'Displays featured reviews in an attractive slideshow. You can set the count of the number of reviews to show in this widget. If the total number of reviews featured are more than that count, then the reviews to be displayed will be sequentially picked up.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.featured-reviews-slideshow',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Featured Reviews',
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
					),
			),
    ),

  array(
        'title' => 'Store’s Featured Reviews Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the featured reviews on the site. Multiple settings of this widget makes it highly configurable.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorereview.featured-reviews-carousel',
        'defaultParams' => array(
            'title' => 'Featured Reviews',
        ),
        'adminForm' => array(
            'elements' => array(
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
                        'label' => 'Reviews in a Row',
                        'description' => '(number of reviews to show in one row. Note: This field is applicable only when you have selected ‘Horizontal’ in ‘Carousel Type’ field.)',
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
			'title' => 'Browse Reviews Link',
			'description' => 'Displays the link to view Store’s Reviews Browse store',
			'category' => 'Stores / Marketplace - Stores',
			'type' => 'widget',
			'name' => 'sitestorereview.sitestorereviewlist-link',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
	),

  array(
    'title' => 'Top Creators : Store Reviews',
    'description' => 'Displays the Stores which have the most number of Store Reviews added in them. Motivates Store Admins to add more content on your website.',
    'category' => 'Stores / Marketplace - Stores',
    'type' => 'widget',
    'name' => 'sitestorereview.topcreators-sitestorereview',
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
					),
        ),
    'requirements' => array(
      'subject' => 'sitestorereview',
    ),
  ),
)
?>
