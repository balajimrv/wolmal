<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$renderContactDetailsWidget = array(
      'Radio',
      'renderWidget',
      array(
          'label' => 'Do you want to display this widget content even when this page enabled as store.',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => '0'
      )
  );

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$ads_Array = array();
$social_share_default_code = '<div class="addthis_toolbox addthis_default_style ">
<a class="addthis_button_preferred_1"></a>
<a class="addthis_button_preferred_2"></a>
<a class="addthis_button_preferred_3"></a>
<a class="addthis_button_preferred_4"></a>
<a class="addthis_button_preferred_5"></a>
<a class="addthis_button_compact"></a>
<a class="addthis_counter addthis_bubble_style"></a>
</div>
<script type="text/javascript">
var addthis_config = {
          services_compact: "facebook, twitter, linkedin, google, digg, more",
          services_exclude: "print, email"
}
</script>
<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js"></script>';
$final_array = array(
    array(
        'title' => $view->translate('Store Profile Overview'),
        'description' => $view->translate('Displays rich overview on Store\'s profile, created by you using the editor from Store Dashboard. This should be placed in the Tabbed Blocks area of Store Profile.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.overview-sitestore',
        'defaultParams' => array(
            'title' => $view->translate('Overview'),
            'titleCount' => true,
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
    array(
        'title' => $view->translate('Store Profile Breadcrumb'),
        'description' => $view->translate('Displays breadcrumb of the store based on the categories. This widget should be placed on the Store Profile page.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.store-profile-breadcrumb',
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
//    array(
//        'title' => $view->translate("Store Profile 'Save to foursquare' Button"),
//        'description' => $view->translate("This Button will enable Store visitors to add the Store's place or tip to their foursquare To-Do List. Note that this feature will be available to you based on your Store's Package and your Member Level."),
//        'category' => $view->translate('Store Profile'),
//        'type' => 'widget',
//        'name' => 'sitestore.foursquare-sitestore',
//        'defaultParams' => array(
//            'title' => '',
//            'titleCount' => true,
//        ),
//    ),
		array(
			'title' => $view->translate('Store Profile Social Share Buttons'),
			'description' => $view->translate("Contains Social Sharing buttons and enables users to easily share Stores on their favorite Social Networks. You can personalize the code for social sharing buttons by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>"),
			'category' => $view->translate('Store Profile'),
			'type' => 'widget',
			'name' => 'sitestore.socialshare-sitestore',
			'defaultParams' => array(
					'title' => $view->translate('Social Share'),
					'titleCount' => true,
			),
			'requirements' => array(
				'subject' => 'sitestore_store',
			),
      'autoEdit' => true,
			'adminForm' => array(
				'elements' => array(
					array(
							'Textarea',
							'code',
							array(
									'description' => $view->translate("Social Sharing Buttons Code: You can personalize the code for social sharing buttons by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>"),
									'value' => $social_share_default_code,
									'decorators' => array('ViewHelper', array('Description', array('placement' => 'PREPEND','escape' => false)))
							),
					),
					array(
							'Hidden',
							'nomobile',
							array(
									'label' => '',
							)
					),
				),
			),
		),
    array(
        'title' => $view->translate('Store Profile Title'),
        'description' => $view->translate('Displays the Title of the Store. This widget should be placed on the Store Profile, in the middle column at the top.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.title-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Info'),
        'description' => $view->translate('This widget forms the Info tab on the Store Profile and displays the information of the Store. It should be placed in the Tabbed Blocks area of the Store Profile. You may enter content for this section from the Edit Info and Profile Info sections of the Store Dashboard.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.info-sitestore',
        'defaultParams' => array(
            'title' => $view->translate('Info'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Information Store'),
        'description' => $view->translate('Displays the owner, category, tags, views and other information about a Store. This widget should be placed on the Store Profile in the left column.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.information-sitestore',
        'defaultParams' => array(
            'title' => $view->translate('Information'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Photo'),
        'description' => $view->translate('Displays the main cover photo of a Store. This widget must be placed on the Store Profile at the top of left column.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.mainphoto-sitestore',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Map'),
        'description' => $view->translate('This widget forms the Map tab on the Store Profile. It displays the map showing the Store position as well as the location details of the store. It should be placed in the Tabbed Blocks area of the Store Profile. Location details can be entered from the Location section of the Dashboard. Note that this feature will be available to you based on your Store\'s Package and your Member Level.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.location-sitestore',
        'defaultParams' => array(
            'title' => $view->translate('Map'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Options'),
        'description' => $view->translate('Displays the various action link options to users viewing your Store. This widget should be placed on the Store Profile in the left column, below the Store profile photo.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.options-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Owner Store Tags'),
        'description' => $view->translate('Displays all the tags chosen by the owner of your Store for his Stores. This widget should be placed on the Store Profile.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.tags-sitestore',
        'defaultParams' => array(
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile Owner Stores'),
        'description' => $view->translate('Displays other Stores owned by the owner of your Store. This widget should be placed on the Store Profile.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.userstore-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Store Profile About Store'),
        'description' => $view->translate('Displays the About Us information for your Store. You can enter information for this widget simply by clicking on the widget.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.write-store',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),
    array(
			'title' => $view->translate('Content Profile: Follow Button'),
			'description' => $view->translate('This is the Follow Button to be placed on the Content Profile page. It enables users to Follow the content being currently viewed.'),
			'category' => $view->translate('Store Profile'),
			'type' => 'widget',
			'name' => 'seaocore.seaocore-follow',
			'defaultParams' => array(
					'title' => '',
			),
    ),
    array(
        'title' => $view->translate('Content Profile: Like Button for Content'),
        'description' => $view->translate('This is the Like Button to be placed on the Content Profile page. It enables users to Like the content being currently viewed. The best place to put this widget is right above the Tabbed Blocks on the Content Profile page.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'seaocore.like-button',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => $view->translate('Store Profile You May Also Like'),
        'description' => $view->translate('Displays the other Stores that a user may like, based on your Store.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.suggestedstore-sitestore',
        'defaultParams' => array(
            'title' => $view->translate('You May Also Like'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Content Profile: Content Likes'),
        'description' => $view->translate('Displays the users who have liked the content being currently viewed. This widget should be placed on the  Content Profile page.'),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'seaocore.people-like',
    ),
//    array(
//        'title' => $view->translate('Store Profile Store Insights'),
//        'description' => $view->translate('Displays the insights of your Store to your Store Admins only. These insights include metrics like views, likes, comments and active users of the Store.'),
//        'category' => $view->translate('Store Profile'),
//        'type' => 'widget',
//        'name' => 'sitestore.insights-sitestore',
//        'defaultParams' => array(
//            'title' => $view->translate('Insights'),
//            'titleCount' => true,
//        ),
//    ),
    array(
        'title' => $view->translate('Store Profile Apps Links'),
        'description' => $view->translate("Displays the Apps related links for your Store. This widget should be placed in the left column."),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.widgetlinks-sitestore',
        'defaultParams' => array(
            'title' => "",
            'titleCount' => "",
        ),
    ),
		array(
				'title' => $this->view->translate('Store Profile Linked Stores'),
				'description' => $this->view->translate('Displays Linked Stores of your Store.'),
				'category' => $this->view->translate('Store Profile'),
				'type' => 'widget',
				'name' => 'sitestore.favourite-store',
				'defaultParams' => array(
								'title' => $this->view->translate('Linked Stores'),
								'titleCount' => true,
				),
    ),
    array(
        'title' => $view->translate('Store Profile Featured Store Admins'),
        'description' => $view->translate("Displays the Featured Admins of your Store."),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.featuredowner-sitestore',
        'defaultParams' => array(
            'title' => $view->translate("Store admins"),
            'titleCount' => "",
        ),
    ), 
    array(
        'title' => 'Store Profile Alternate Thumb Photo',
        'description' => 'Displays the thumb photo of a Store. This works as an alternate profile photo when you have set the layout of Store Profile to be tabbed, from the Store Layout Settings, and have integrated with the "Advertisements / Community Ads Plugin" by SocialEngineAddOns. In that case, the left column of the Store Profile having the main profile photo gets hidden to accomodate Ads. This widget must be placed on the Store Profile at the top of middle column.',
        'category' => 'Store Profile',
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
        'title' => $view->translate('Store Profile Contact Details'),
        'description' => $view->translate("Displays the Contact Details of your Store."),
        'category' => $view->translate('Store Profile'),
        'type' => 'widget',
        'name' => 'sitestore.contactdetails-sitestore',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
            'contacts' => array("0" => "1", "1" => "2", "2" => "3"),
        ),
        'adminForm' => array(
            'elements' => array(
                $renderContactDetailsWidget,
                array(
                    'MultiCheckbox',
                    'contacts',
                    array(
                        'label' => $view->translate('Select the contact details you want to display'),
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
);
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
  $ads_Array = array(
      array(
          'title' => $view->translate('Store Profile Alternate Cover Thumb Photo'),
          'description' => $view->translate('Displays the thumb photo of your Store. This widget must be placed on the Store Profile at the top of middle column. This photo shows up only in special cases.'),
          'category' => $view->translate('Store Profile'),
          'type' => 'widget',
          'name' => 'sitestore.thumbphoto-sitestore',
          'defaultParams' => '',
      ),
  );
}
if (!empty($ads_Array)) {
  $final_array = array_merge($final_array, $ads_Array);
}

$fbstore_sitestore_Array = array(
      array(
          'title' => $view->translate('Facebook Like Box'),
          'description' => $view->translate('This widget contains the Facebook Like Box which enables Store Admins to gain Likes for their Facebook Page from this website. The edit popup contains the settings to customize the Facebook Like Box. This widget should be placed on the Store Profile.'),
          'category' => $view->translate('Store Profile'),
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
                        'multiOptions' => array('1' => 'Show profile photos of users who like the linked Facebook Page in the Facebook Like Box.')
                         
                    )
                ),
                
                array(
                    "Text",
                    "widget_border_color",
                    array(
                        'label' => 'Border Color',
                        'description' => 'The border color of the plugin'
                       
                         
                    )
                ),
                array(
                    "MultiCheckbox",
                    "show_stream",
                    array(
                        
                        'description' => 'Stream',
                        'multiOptions' => array('1' => 'Show the Facebook Page profile stream for the public feeds in the Facebook Like Box.'),
                       
                        
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
   
   if (!empty($fbstore_sitestore_Array)) {
  $final_array = array_merge($final_array, $fbstore_sitestore_Array);
}
return $final_array;
?>
