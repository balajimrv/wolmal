<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminviewwidgetController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
//GET CONTENT TABLE
$tableContent = Engine_Api::_()->getDbtable('content', 'core');

//GET CONTENT TABLE NAME
$tableContentName = $tableContent->info('name');

$tableName = Engine_Api::_()->getDbtable('pages', 'core');

//GET WIDGETIZED STORE INFORMATION
$selectStore = Engine_Api::_()->sitestore()->getWidgetizedStore();

if (empty($selectStore)) {
  $storeCreate = $tableName->createRow();
  $storeCreate->name = 'sitestore_index_view';
  $storeCreate->displayname = 'Stores - Store Profile';
  $storeCreate->title = 'Store Profile';
  $storeCreate->description = 'This is the store view page.';
  $storeCreate->custom = 1;
  $store_id = $storeCreate->save();
} else {
  $store_id = $selectStore->page_id;
}

if (!empty($store_id)) {
  $tableContent->delete(array('page_id =?' => $store_id));
  //INSERT MAIN CONTAINER
  $mainContainer = $tableContent->createRow();
  $mainContainer->page_id = $store_id;
  $mainContainer->type = 'container';
  $mainContainer->name = 'main';
  $mainContainer->order = 2;
  $mainContainer->save();
  $container_id = $mainContainer->content_id;

  //INSERT MAIN-MIDDLE CONTAINER
  $mainMiddleContainer = $tableContent->createRow();
  $mainMiddleContainer->page_id = $store_id;
  $mainMiddleContainer->type = 'container';
  $mainMiddleContainer->name = 'middle';
  $mainMiddleContainer->parent_content_id = $container_id;
  $mainMiddleContainer->order = 6;
  $mainMiddleContainer->save();
  $middle_id = $mainMiddleContainer->content_id;

  //INSERT MAIN-LEFT CONTAINER
  $mainLeftContainer = $tableContent->createRow();
  $mainLeftContainer->page_id = $store_id;
  $mainLeftContainer->type = 'container';
  $mainLeftContainer->name = 'right';
  $mainLeftContainer->parent_content_id = $container_id;
  $mainLeftContainer->order = 4;
  $mainLeftContainer->save();
  $left_id = $mainLeftContainer->content_id;
  $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showmore', 9);

	//if(Engine_Api::_()->getApi("settings", "core")->getSetting('sitestore.layout.setting', 1)){
		//INSERT MAIN-MIDDLE-TAB CONTAINER
		$middleTabContainer = $tableContent->createRow();
		$middleTabContainer->page_id = $store_id;
		$middleTabContainer->type = 'widget';
		$middleTabContainer->name = 'core.container-tabs';
		$middleTabContainer->parent_content_id = $middle_id;
		$middleTabContainer->order = 10;
		$middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
		$middleTabContainer->save();
		$middle_tab = $middleTabContainer->content_id;
	//}
		//INSERTING THUMB PHOTO WIDGET
		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.thumbphoto-sitestore', $middle_id, 3, '','true');
  if(empty($sitestore_layout_cover_photo)) {
  
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id,1, '', 'true');

		//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
			Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestoremember.storecover-photo-sitestoremembers', $middle_id, 2, '','true');
    }

		//INSERTING TITLE WIDGET
		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.title-sitestore', $middle_id, 4, '','true');
		
		//INSERTING LIKE WIDGET
		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'seaocore.like-button', $middle_id, 5, '','true');
		
		//INSERTING FOLLOW WIDGET
		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'seaocore.seaocore-follow', $middle_id, 6,'','true');

		//INSERTING FACEBOOK LIKE WIDGET
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
			Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'Facebookse.facebookse-sitestoreprofilelike', $middle_id, 7, '','true');
		}

		//INSERTING MAIN PHOTO WIDGET 
		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.mainphoto-sitestore', $left_id, 10, '','true');

  } 
  else {
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id,1, '', 'true');
  
		//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.store-cover-information-sitestore', $middle_id, 2, '','true');
  }

  //INSERTING CONTACT DETAIL WIDGET
  Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.contactdetails-sitestore', $middle_id, 8, '','true');
  
	//INSERTING WIDGET LINK WIDGET 
  if(!Engine_Api::_()->getApi("settings", "core")->getSetting('sitestore.layout.setting', 1)){
	   Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.widgetlinks-sitestore', $left_id, 11, '', 'true');
  }

	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestoreproduct.sitestoreproduct-products', $left_id, 11, '', 'true', '{"title":"Top Selling Products","titleCount":true,"statistics":"","viewType":"gridview","columnWidth":"180","popularity":"last_order_all","product_type":"all","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","ratingType":"rating_avg","columnHeight":"328","itemCount":"3","truncation":"16","nomobile":"0","name":"sitestoreproduct.sitestoreproduct-products"}');

  //INSERTING OPTIONS WIDGET
  Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.options-sitestore', $left_id, 12, '','true');

  //INSERTING INFORMATION WIDGET 
  Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.information-sitestore', $left_id, 10, 'Information', 'true');

  //INSERTING WRITE SOMETHING ABOUT WIDGET 
  Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'seaocore.people-like', $left_id, 15, '','true');

  //INSERTING RATING WIDGET 
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestorereview.ratings-sitestorereviews', $left_id, 16, 'Ratings', 'true');
  }

  //INSERTING BADGE WIDGET 
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge')) {
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestorebadge.badge-sitestorebadge', $left_id, 17, 'Badge', 'true');
  }

  //INSERTING YOU MAY ALSO LIKE WIDGET 
  Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.suggestedstore-sitestore', $left_id, 18, 'You May Also Like', 'true');
  
	$social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitestore.socialshare-sitestore"}';
	
  //INSERTING SOCIAL SHARE WIDGET 
  Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.socialshare-sitestore', $left_id, 19, 'Social Share','true', $social_share_default_code);

//  //INSERTING FOUR SQUARE WIDGET 
//  Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.foursquare-sitestore', $left_id, 20,'','true');

  //INSERTING INSIGHTS WIDGET 
//   Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.insights-sitestore', $left_id, 21, 'Insights','true');

  //INSERTING FEATURED OWNER WIDGET 
  Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.featuredowner-sitestore', $left_id, 22, 'Owners', 'true');

  //INSERTING ALBUM WIDGET 
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.albums-sitestore', $left_id, 23, 'Albums' ,'true');
  }
 
  //INSERTING STORE PROFILE PLAYER WIDGET 
  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestoremusic.profile-player', $left_id, 24,'','true');
  }
 
  //INSERTING ALBUM WIDGET   
  Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.favourite-store', $left_id, 25, 'Linked Stores', 'true');

	if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct')) {
		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestoreproduct.store-profile-products', $middle_tab, 1, 'Products', 'true', '{"columnHeight":325,"columnWidth":165,"defaultWidgetNo":13}');
	}

  //if(Engine_Api::_()->getApi("settings", "core")->getSetting('sitestore.layout.setting', 1)){
		//INSERTING ACTIVITY FEED WIDGET
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
			$advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
			Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true',$advanced_activity_params);
		} else {
			Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'activity.feed', $middle_tab, 2, 'Updates', 'true');
		}

		//INSERTING INFORAMTION WIDGET
		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.info-sitestore', $middle_tab, 3, 'Info', 'true');

		//INSERTING OVERVIEW WIDGET
		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.overview-sitestore', $middle_tab, 4, 'Overview', 'true');

		//INSERTING LOCATION WIDGET
		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.location-sitestore', $middle_tab, 5, 'Map', 'true');

		//INSERTING LINKS WIDGET
		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($tableContent, $tableContentName, $store_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');
  //}
}
?>