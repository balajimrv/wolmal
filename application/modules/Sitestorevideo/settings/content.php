<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
// $isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorevideo.isActivate', 0);
// if (empty($isActive)) {
//   return;
// }

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
        'title' => 'Store Profile Videos',
        'description' => 'This widget forms the Videos tab on the Store Profile and displays the videos of the Store. It should be placed in the Tabbed Blocks area of the Store Profile.',
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorevideo.profile-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Videos',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Store Profile Most Commented Videos',
        'description' => "Displays list of a Store's most commented videos. This widget should be placed on the Store Profile.",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorevideo.comment-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Most Commented Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Store Profile Most Recent Videos',
        'description' => "Displays list of a Store's most recent videos. This widget should be placed on the Store Profile.",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorevideo.recent-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Most Recent Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Store Profile Top Rated Videos',
        'description' => "Displays list of a Store's top rated videos. This widget should be placed on the Store Profile.",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorevideo.rate-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Top Rated Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Store Profile Most Liked Videos',
        'description' => "Displays list of a Store's most liked videos. This widget should be placed on the Store Profile.",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorevideo.like-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Most Liked Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Store Profile Featured Videos',
        'description' => "Displays list of store's featured videos. This widget should be placed on the Store Profile.",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorevideo.featurelist-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Featured Store Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Store Profile Highlighted Videos',
        'description' => "Displays list of store's highlighted videos. This widget should be placed on the Store Profile.",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorevideo.highlightlist-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Highlighted Store Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Store Profile Most Viewed Videos',
        'description' => "Displays list of a Store's most viewed videos. This widget should be placed on the Store Profile.",
        'category' => 'Stores / Marketplace - Store Profile',
        'type' => 'widget',
        'name' => 'sitestorevideo.view-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Most Viewed Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Recent Videos',
        'description' => 'Displays the recent videos of the site.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.homerecent-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Recent Videos'
        ),
         'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
			'title' => 'Search Store Videos form',
			'description' => 'Displays the form for searching Store Videos on the basis of various filters. You can edit the fields to be available in this form.',
			'category' => 'Stores / Marketplace - Stores',
			'type' => 'widget',
			'name' => 'sitestorevideo.search-sitestorevideo',
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
											'label' => 'Choose the fields that you want to be available in the Search Store Videos form widget.',
											'multiOptions' => array("1" => "Show","2" => "Browse By", "3" => "Store Title", "4" => "Video Keywords", "5" => "Store Category"),
									),
							),
					),
			)
    ),

     array(
        'title' => 'Store Videos',
        'description' => 'Displays the list of Videos from Stores created on your community. This widget should be placed in the widgetized Store Videos store. Results from the Search Store Videos form are also shown here.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.sitestore-video',
        'defaultParams' => array(
            'title' => 'Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of videos to show)',
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
        'title' => 'Sponsored Videos',
        'description' => 'Displays the Videos from Paid Stores. You can choose the number of entries to be shown.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.sitestore-sponsoredvideo',
        'defaultParams' => array(
            'title' => 'Sponsored Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of videos to show)',
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
        'title' => 'Featured Videos',
        'description' => "Displays Featured Store Videos. You can mark Store Videos as Featured from the “Manage Store Videos” section in the Admin Panel of this extension. You can choose the number of entries to be shown.",
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.homefeaturelist-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Featured Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
   
//     array(
//         'title' => 'Highlighted Videos',
//         'description' => "Displays Highlighted Store Videos. You can mark Store Videos as Highlighted from the “Manage Store Videos” section in the Admin Panel of this extension. You can choose the number of entries to be shown.",
//         'category' => 'Stores / Marketplace - Stores',
//         'type' => 'widget',
//         'name' => 'sitestorevideo.homehighlightlist-sitestorevideos',
//         'defaultParams' => array(
//             'title' => 'Highlighted Videos',
//             'titleCount' => true,
//         ),
//         'adminForm' => array(
// 					'elements' => array(
// 						array(
// 								'Text',
// 								'itemCount',
// 								array(
// 										'label' => 'Count',
// 										'description' => '(number of videos to show)',
// 										'value' => 3,
// 										'validators' => array(
// 											array('Int', true),
// 											array('GreaterThan', true, array(0)),
// 										),
// 								),
// 						),
// 					),
//         ),
//     ),

    array(
        'title' => 'Most Commented Videos',
        'description' => "Displays the Most Commented Store Videos. You can choose the number of entries to be shown.",
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.homecomment-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Most Commented Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Most Viewed Videos',
        'description' => "Displays the Most Viewed Store Videos. You can choose the number of entries to be shown.",
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.homeview-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Most Viewed Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Most Liked Videos',
        'description' => "Displays the Most Liked Store Videos. You can choose the number of entries to be shown.",
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.homelike-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Most Liked Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
        'title' => 'Top Rated Videos',
        'description' => "Displays the Top Rated Store Videos. You can choose the number of entries to be shown.",
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.homerate-sitestorevideos',
        'defaultParams' => array(
            'title' => 'Top Rated Videos',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
			'title' => 'Store Video View',
			'description' => "This widget should be placed on the Store Video View Page.",
      'category' => 'Stores / Marketplace - Stores',
			'type' => 'widget',
			'name' => 'sitestorevideo.video-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
	),

   array(
    'title' => 'People Also Liked',
    'description' => 'Displays a list of other Store Videos that the people who liked this Store Video also liked. You can choose the number of entries to be shown. This widget should be placed on Store Video View Page.',
    'category' => 'Stores / Marketplace - Stores',
    'type' => 'widget',
    'name' => 'sitestorevideo.show-also-liked',
    //'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'People Also Liked',
    ),
    'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
      'subject' => 'sitestorevideo',
    ),
  ),
  array(
    'title' => 'Other Videos From Store',
    'description' => 'Displays a list of other Store Videos corresponding to the Store of which the video is being viewed. You can choose the number of entries to be shown. This widget should be placed on Store Video View Page.',
    'category' => 'Stores / Marketplace - Stores',
    'type' => 'widget',
    'name' => 'sitestorevideo.show-same-poster',
    //'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Other Videos From Store',
    ),
    'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
      'subject' => 'sitestorevideo',
    ),
  ),
  array(
    'title' => 'Similar Videos',
    'description' => 'Displays Store Videos similar to the Store Video being viewed based on tags. You can choose the number of entries to be shown. This widget should be placed on Store Video View Page.',
    'category' => 'Stores / Marketplace - Stores',
    'type' => 'widget',
    'name' => 'sitestorevideo.show-same-tags',
   // 'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Similar Videos',
    ),
     'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of videos to show)',
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
      'subject' => 'sitestorevideo',
    ),
  ),

  array(
        'title' => 'Store’s Featured Videos Slideshow',
        'description' => 'Displays featured videos in an attractive slideshow. You can set the count of the number of videos to show in this widget. If the total number of videos featured are more than that count, then the videos to be displayed will be sequentially picked up.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.featured-videos-slideshow',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Featured Videos',
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
        'title' => 'Store’s Featured Videos Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the featured videos on the site. Multiple settings of this widget makes it highly configurable.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.featured-videos-carousel',
        'defaultParams' => array(
            'title' => 'Featured Videos',
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
                        'label' => 'Videos in a Row',
                        'description' => '(number of videos to show in one row. Video: This field is applicable only when you have selected ‘Horizontal’ in ‘Carousel Type’ field.)',
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
            ),
        ),
    ),

  array(
        'title' => 'Store’s Ajax based Tabbed widget for Videos',
        'description' => 'Displays the Recent, Most Liked, Most Viewed, Most Commented and Featured Videos in separate AJAX based tabs. Settings for this widget are available in the Videos >> Widget Settings section of Stores / Marketplace plugin.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.list-videos-tabs-view',
        'defaultParams' => array(
            'title' => 'Videos',
            'margin_photo'=>12,
            'showViewMore'=>1
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
                  )
              ),
              array(
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
									)
							)
            ),
        ),
    ),

  array(
        'title' => 'Browse Videos',
        'description' => 'Displays the link to view Store’s Videos Browse store.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.sitestorevideolist-link',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),

  array(
        'title' => 'Store’s Video of the Day',
        'description' => 'Displays the Video of the Day as selected by the Admin from the widget settings section of Videos section.',
        'category' => 'Stores / Marketplace - Stores',
        'type' => 'widget',
        'name' => 'sitestorevideo.video-of-the-day',
        'defaultParams' => array(
            'title' => 'Video of the Day'
        ),
    ),

  array(
  'title' => 'Popular Video Tags',
  'description' => 'Shows popular tags with frequency.',
  'category' => 'Stores / Marketplace - Stores',
  'type' => 'widget',
  'name' => 'sitestorevideo.tagcloud-sitestorevideo',
  'adminForm' => array(
       'elements' => array(
         array(
           'hidden',
           'title',
           array(
             'label' => '',
             'order' => 1011
           )
         ),
         array(
           'hidden',
           'nomobile',
           array(
             'label' => '',
             'order' => 1012
           )
         ),
         array(
           'hidden',
           'execute',
           array(
             'label' => '',
             'order' => 1013
           )
         ),
         array(
           'hidden',
           'cancel',
           array(
             'label' => '',
             'order' => 1014
           )
         ),
       )
     ),
    ),

    array(
    'title' => 'Top Creators : Store Videos',
    'description' => 'Displays the Stores which have the most number of Store Videos added in them. Motivates Store Admins to add more content on your website.',
    'category' => 'Stores / Marketplace - Stores',
    'type' => 'widget',
    'name' => 'sitestorevideo.topcreators-sitestorevideo',
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
      'subject' => 'sitestorevideo',
    ),
  ),
)
?>