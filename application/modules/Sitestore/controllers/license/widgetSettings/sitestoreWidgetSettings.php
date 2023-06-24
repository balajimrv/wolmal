<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitestoreevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitestoreWidgetSettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
Engine_Api::_()->sitestoreurl()->setBandURL();
Engine_Api::_()->getApi('settings', 'core')->setSetting('send.cheque.to', 'Account Name: 
Account No.: 
Bank: 
Bank Branch Address:');
Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreurl_is_enable', 1);
Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore_change_url', 1);
Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore_likelimit_forurlblock', 5);
$db = Zend_Db_Table_Abstract::getDefaultAdapter();
$sitestore_layout_cover_photo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layout.cover.photo', 1);

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)VALUES (
"sitestoredocument_admin_sub_settings", "sitestore", "Stores", "", \'{"route":"admin_default","module":"sitestoredocument","controller":"settings"}\', "sitestore_document_admin_main", NULL , "1", "0", "1"), 
("sitestoreproduct_document_admin_sub_settings", "sitestoreproduct", "Products", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"document"}\', "sitestore_document_admin_main", NULL , "1", "0", "2");');

//$select = new Zend_Db_Select($db);
//$isMenuExist = $select->from('engine4_core_menuitems')
//									 ->where('name = ?', 'sitestore_document_admin_main_settings')
//									 ->limit(1)->query()->fetchAll();
//
//if( empty($isMenuExist) ) {
//  $db->insert('engine4_core_menuitems', array(
//    'name' => 'sitestore_document_admin_main_settings',
//    'module' => 'sitestore',
//    'label' => 'Documents',
//    'plugin' => 'Sitestoreproduct_Plugin_Menus::makeDocumentUrl',
//    'params' => '',
//    'menu' => 'sitestore_admin_main',
//    'submenu' => '',
//    'enabled' => 1,
//    'custom' => 0,
//    'order' => 50
//  ));
//}

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("sitestore_dashboard_getstarted", "sitestore", "Get Started", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 1),
("sitestore_dashboard_editinfo", "sitestore", "Edit Info", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 2),
("sitestore_dashboard_profileinfo", "sitestore", "Profile Info", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 3),
("sitestore_dashboard_profilepicture", "sitestore", "Profile Picture", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 4),
("sitestore_dashboard_manageproducts", "sitestore", "Manage Products", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 5),
("sitestore_dashboard_managesections", "sitestore", "Manage Sections", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 6),
("sitestore_dashboard_manageorders", "sitestore", "Manage Orders", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 7),
("sitestore_dashboard_shippingmethods", "sitestore", "Shipping Methods", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 8),
("sitestore_dashboard_taxes", "sitestore", "Taxes", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 9),
("sitestore_dashboard_paymentaccount", "sitestore", "Payment Account", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 10),
("sitestore_dashboard_paymentmethod", "sitestore", "Payment Method", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 11),
("sitestore_dashboard_paymentrequests", "sitestore", "Payment Requests", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 12),
("sitestore_dashboard_yourbill", "sitestore", "Your Bill", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 13),
("sitestore_dashboard_transactions", "sitestore", "Transactions", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 14),
("sitestore_dashboard_salesstatistics", "sitestore", "Products Sales Statistics", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 15),
("sitestore_dashboard_graphstatistics", "sitestore", "Sales Graph Statistics", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 16),
("sitestore_dashboard_salesreports", "sitestore", "Sales Reports", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 17),
("sitestore_dashboard_overview", "sitestore", "Overview", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 18),
("sitestore_dashboard_contact", "sitestore", "Contact Details", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 19),
("sitestore_dashboard_locations", "sitestore", "Locations", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 20),
("sitestore_dashboard_apps", "sitestore", "Apps", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 21),
("sitestore_dashboard_marketing", "sitestore", "Marketing", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 22),
("sitestore_dashboard_managenotifications", "sitestore", "Manage Notifications", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 23),
("sitestore_dashboard_manageadmins", "sitestore", "Manage Admins", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 24),
("sitestore_dashboard_featuredadmins", "sitestore", "Featured Admins", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 25),
("sitestore_dashboard_editlayout", "sitestore", "Edit Layout", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0,26),
("sitestore_dashboard_importproducts", "sitestore", "Import Products", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 27),
("sitestore_dashboard_editstyle", "sitestore", "Edit Style", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 28),
("sitestore_dashboard_packages", "sitestore", "Packages", "Sitestore_Plugin_Dashboardmenus", "", "sitestore_dashboard", "", 1, 0, 29);');

$db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`)VALUES 
("sitestoreproduct_order_place_to_admin", "sitestoreproduct", "[host],[store_name],[store_title],[order_invoice],[order_id],[order_no]");');

$db->query('INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`)VALUES 
  ("sitestore_dashboard", "standard", "Stores - Store Dashboard Menu", "999");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestore_admin_main_activity_feed", "sitestore", "Activity Feed", "",\'{"route":"admin_default","module":"sitestore","controller":"settings","action":"activity-feed"}\', "sitestore_admin_main", "", 1, 0, 998);');


$db->query('
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("sitestore_admin_main_global_store", "sitestore", "Stores", "", \'{"route":"admin_default","module":"sitestore","controller":"settings","action":"index"}\', "sitestore_admin_main_settings", "", 1, 0, 1),
("sitestore_admin_main_global_product", "sitestore", "Products", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"settings","action":"index"}\', "sitestore_admin_main_settings", "", 1, 0, 5),
("sitestore_admin_main_global_invite", "sitestore", "Inviter", "", \'{"route":"admin_default","module":"sitestoreinvite","controller":"global","action":"global"}\', "sitestore_admin_main_settings", "", 1, 0, 10),
("sitestore_admin_main_global_widget", "sitestore", "Widget Settings", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"settings","action":"widget-settings"}\', "sitestore_admin_main_settings", "", 1, 0, 15);
');

//$db->query('
//INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
//("sitestore_admin_main_global_store", "sitestore", "Stores", "", \'{"route":"admin_default","module":"sitestore","controller":"settings","action":"index"}\', "sitestore_admin_main_settings", "", 1, 0, 1),
//("sitestore_admin_main_global_product", "sitestore", "Products", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"settings","action":"index"}\', "sitestore_admin_main_settings", "", 1, 0, 5),
//("sitestore_admin_main_global_invite", "sitestore", "Inviter", "", \'{"route":"admin_default","module":"sitestoreinvite","controller":"global","action":"global"}\', "sitestore_admin_main_settings", "", 1, 0, 10),
//("sitestore_admin_main_global_widget", "sitestore", "Widget Settings", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"settings","action":"widget-settings"}\', "sitestore_admin_main_settings", "", 1, 0, 15),
//("sitestore_admin_main_global_integration", "sitestore", "Multiple Listings and Products Showcase", "", \'{"route":"admin_default","module":"sitestoreintegration","controller":"settings"}\', "sitestore_admin_main_settings", "", 1, 0, 20);
//');

$db->query('INSERT IGNORE INTO engine4_sitestore_packages (title, description, price, recurrence, recurrence_type, duration, duration_type, sponsored, featured, tellafriend, print, overview, map, insights, contact_details, sendupdate , modules, approved, enabled, defaultpackage, store_settings) VALUES
("Free Store Package", "This is a free store package. One does not need to pay for creating a store of this package.", "0.00", 0, "forever", 0, "forever", 0, 0, 1, 1, 1, 1, 0,1,1, "", 1, 1, 1, "");');

$check_table = Engine_Api::_()->getDbtable('menuItems', 'core');
$check_name = $check_table->info('name');

$menuitemsTable = Engine_Api::_()->getDbtable('menuItems', 'core');
$menuitemsTableName = $menuitemsTable->info('name');

//$selectmenuitems = $menuitemsTable->select()
//	->from($menuitemsTableName, array('name'))
//	->where('name =?', 'core_admin_main_plugins_sitestoreextensions')
//	->where('module =?', 'sitestore')
//	->limit(1);
//$fetchmenuitems = $selectmenuitems->query()->fetchAll();
//if (empty($fetchmenuitems)) {
//$menuitems = $menuitemsTable->createRow();
//$menuitems->name = 'core_admin_main_plugins_sitestoreextensions';
//$menuitems->module = 'sitestore';
//$menuitems->label = 'SEAO - Stores / Marketplace - Ecommerce Plugin - Extensions';
//$menuitems->plugin = Null;
//$menuitems->params = '{"route":"admin_default","module":"sitestore","controller":"extension", "action": "index"}';
//$menuitems->menu = 'core_admin_main_plugins';
//$menuitems->submenu = Null;
//$menuitems->enabled = '1';
//$menuitems->custom = '0';
//$menuitems->order = '999';
//$menuitems->save();
//}
$contentTable = Engine_Api::_()->getDbtable('content', 'core');
$contentTableName = $contentTable->info('name');
$storeTable = Engine_Api::_()->getDbtable('pages', 'core');
$storeTableName = $storeTable->info('name');
//$selectStore = $storeTable->select()
//	->from($storeTableName, array('page_id'))
//	->where('name =?', 'sitestore_index_home')
//	->limit(1);
//$fetchStoreId = $selectStore->query()->fetchAll();
//if (empty($fetchStoreId)) {
//	$storeCreate = $storeTable->createRow();
//	$storeCreate->name = 'sitestore_index_home';
//	$storeCreate->displayname = 'Stores Home';
//	$storeCreate->title = 'Stores Home';
//	$storeCreate->description = 'This is the store home store.';
//	$storeCreate->custom = 0;
//	$storeCreate->save();
//	$store_id = $storeCreate->page_id;
//	
//	// INSERT MAIN CONTAINER
//	$mainContainer = $contentTable->createRow();
//	$mainContainer->page_id = $store_id;
//	$mainContainer->type = 'container';
//	$mainContainer->name = 'main';
//	$mainContainer->order = 2;
//	$mainContainer->save();
//	$container_id = $mainContainer->content_id;
//	
//	// INSERT MAIN-MIDDLE CONTAINER
//	$mainMiddleContainer = $contentTable->createRow();
//	$mainMiddleContainer->page_id = $store_id;
//	$mainMiddleContainer->type = 'container';
//	$mainMiddleContainer->name = 'middle';
//	$mainMiddleContainer->parent_content_id = $container_id;
//	$mainMiddleContainer->order = 6;
//	$mainMiddleContainer->save();
//	$middle_id = $mainMiddleContainer->content_id;	
//	
//	// INSERT MAIN-LEFT CONTAINER
//	$mainLeftContainer = $contentTable->createRow();
//	$mainLeftContainer->page_id = $store_id;
//	$mainLeftContainer->type = 'container';
//	$mainLeftContainer->name = 'left';
//	$mainLeftContainer->parent_content_id = $container_id;
//	$mainLeftContainer->order = 4;
//	$mainLeftContainer->save();
//	$left_id = $mainLeftContainer->content_id;
//	
//	// INSERT MAIN-RIGHT CONTAINER
//	$mainRightContainer = $contentTable->createRow();
//	$mainRightContainer->page_id = $store_id;
//	$mainRightContainer->type = 'container';
//	$mainRightContainer->name = 'right';
//	$mainRightContainer->parent_content_id = $container_id;
//	$mainRightContainer->order = 5;
//	$mainRightContainer->save();
//	$right_id = $mainRightContainer->content_id;
//	
//	// INSERT TOP CONTAINER
//	$topContainer = $contentTable->createRow();
//	$topContainer->page_id = $store_id;
//	$topContainer->type = 'container';
//	$topContainer->name = 'top';
//	$topContainer->order = 1;
//	$topContainer->save();
//	$top_id = $topContainer->content_id;
//	
//	// INSERT TOP-MIDDLE CONTAINER
//	$topMiddleContainer = $contentTable->createRow();
//	$topMiddleContainer->page_id = $store_id;
//	$topMiddleContainer->type = 'container';
//	$topMiddleContainer->name = 'middle';
//	$topMiddleContainer->parent_content_id = $top_id;
//	$topMiddleContainer->order = 1;
//	$topMiddleContainer->save();
//	$top_middle_id = $topMiddleContainer->content_id;
//					
//	// INSERT "Store of the day" WIDGET
//  Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.item-sitestore', $left_id, 1, "Store of the day", "true");
//	
//  // INSERT NAVIGATION WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.browsenevigation-sitestore', $top_middle_id, 2,'','true');
//	
//	// INSERT ZERO STORE WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.zerostore-sitestore', $middle_id, 3,'','true');
//
//	// INSERT "Featured Stores" WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.slideshow-sitestore', $middle_id, 4, "Featured Stores", "true");
//	
//	// INSERT "Categories" WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.categories', $middle_id, 5, "Categories", "true");
//
//	// INSERT RANDOM STORES WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.recently-popular-random-sitestore', $middle_id, 6,'','true');
//	
//	// INSERT SEARCH WIDGET
//  Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.search-sitestore', $right_id, 7,'','true');
//	
//  // INSERT NEW STORE WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.newstore-sitestore', $right_id, 8,'','true');
//
//	// INSERT "Sponsored Stores" WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.sponsored-sitestore', $right_id, 9, "Sponsored Stores", "true");
//
//	// INSERT TAG CLOUD WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.tagcloud-sitestore', $right_id, 10,'','true');
//
//	// INSERT "Recommended Stores" WIDGET
//	$isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
//	if( !empty($isModEnabled) ) {
//		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'suggestion.common-suggestion', $right_id, 999, '', 'true', '{"title":"Recommended Store","resource_type":"sitestore","getWidAjaxEnabled":"1","getWidLimit":"5","nomobile":"0","name":"suggestion.common-suggestion"}');
//	}	
//
//	// INSERT "Most Liked Stores" WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.mostlikes-sitestore', $left_id, 11, "Most Liked Stores", "true");
//	
//	//INSERT "Most Followed Pages" WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.mostfollowers-sitestore', $left_id, 12, "Most Followed Stores", "true");
//
//	// INSERT "Most Commented Stores" WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.mostcommented-sitestore', $left_id, 13, "Most Commented Stores", "true");
//
//	// INSERT "Recently Viewed" WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.recentview-sitestore', $left_id, 14, "Recently Viewed", "true");
//
//	// INSERT "Recently Viewed By Friends" WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.recentfriend-sitestore', $left_id, 15, "Recently Viewed By Friends", "true");
//
//}
//
//$selectStore = $storeTable->select()
//	->from($storeTableName, array('page_id'))
//	->where('name =?', 'sitestore_index_index')
//	->limit(1);
//$store_id = $selectStore->query()->fetchAll();
//if (empty($store_id)) {
//	$storeCreate = $storeTable->createRow();
//	$storeCreate->name = 'sitestore_index_index';
//	$storeCreate->displayname = 'Browse Stores';
//	$storeCreate->title = 'Browse Stores';
//	$storeCreate->description = 'This is the store browse store.';
//	$storeCreate->custom = 0;
//	$storeCreate->save();
//	$store_id = $storeCreate->page_id;
//
//	// INSERT MAIN CONTAINER
//	$mainContainer = $contentTable->createRow();
//	$mainContainer->page_id = $store_id;
//	$mainContainer->type = 'container';
//	$mainContainer->name = 'main';
//	$mainContainer->order = 2;
//	$mainContainer->save();
//	$container_id = $mainContainer->content_id;
//
//	// INSERT MAIN - MIDDLE CONTAINER
//	$mainMiddleContainer = $contentTable->createRow();
//	$mainMiddleContainer->page_id = $store_id;
//	$mainMiddleContainer->type = 'container';
//	$mainMiddleContainer->name = 'middle';
//	$mainMiddleContainer->parent_content_id = $container_id;
//	$mainMiddleContainer->order = 6;
//	$mainMiddleContainer->save();
//	$middle_id = $mainMiddleContainer->content_id;
//
//	// INSERT MAIN - RIGHT CONTAINER
//	$mainRightContainer = $contentTable->createRow();
//	$mainRightContainer->page_id = $store_id;
//	$mainRightContainer->type = 'container';
//	$mainRightContainer->name = 'right';
//	$mainRightContainer->parent_content_id = $container_id;
//	$mainRightContainer->order = 5;
//	$mainRightContainer->save();
//	$right_id = $mainRightContainer->content_id;
//
//	// INSERT TOP CONTAINER
//	$topContainer = $contentTable->createRow();
//	$topContainer->page_id = $store_id;
//	$topContainer->type = 'container';
//	$topContainer->name = 'top';
//	$topContainer->order = 1;
//	$topContainer->save();
//	$top_id = $topContainer->content_id;
//
//	// INSERT TOP- MIDDLE CONTAINER
//	$topMiddleContainer = $contentTable->createRow();
//	$topMiddleContainer->page_id = $store_id;
//	$topMiddleContainer->type = 'container';
//	$topMiddleContainer->name = 'middle';
//	$topMiddleContainer->parent_content_id = $top_id;
//	$topMiddleContainer->order = 6;
//	$topMiddleContainer->save();
//	$top_middle_id = $topMiddleContainer->content_id;
//
//	// INSERT NAVIGATION WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.browsenevigation-sitestore', $top_middle_id, 1,'','true');
//
//  //INSERT NAVIGATION WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.alphabeticsearch-sitestore', $top_middle_id, 2,'','true');
//
//	// INSERT STORES WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.stores-sitestore', $middle_id, 2,'','true');
//
//	// INSERT "Categories" WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.categories-sitestore', $right_id, 3, "Categories", "true");
//
//	// INSERT SEARCH STORE WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.search-sitestore', $right_id, 4,'','true');
//
//	// INSERT NEW STORE WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.newstore-sitestore', $right_id, 5,'','true');
//
//	// INSERT "Popular Locations" WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.popularlocations-sitestore', $right_id, 6, "Popular Locations", 'true');
//
//	// INSERT TAG CLOUD WIDGET
//	Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.tagcloud-sitestore', $right_id, 7,'','true');
//}

include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/AdminviewstorewidgetController.php';
$selectStore = $storeTable->select()
        ->from($storeTableName, array('page_id'))
        ->where('name =?', 'user_profile_index')
        ->limit(1);
$store_id = $selectStore->query()->fetchAll();
if (!empty($store_id)) {
    $store_id = $store_id[0]['page_id'];
    $selectWidgetId = $contentTable->select()
            ->from($contentTableName, array('content_id'))
            ->where('page_id =?', $store_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'core.container-tabs')
            ->limit(1);
    $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
    if (!empty($fetchWidgetContentId)) {
        $tab_id = $fetchWidgetContentId[0]['content_id'];
        $contentWidget = $contentTable->createRow();
        $contentWidget->page_id = $store_id;
        $contentWidget->type = 'widget';
        $contentWidget->name = 'sitestore.profile-sitestore';
        $contentWidget->parent_content_id = $tab_id;
        $contentWidget->order = 999;
        $contentWidget->params = '{"title":"Stores","titleCount":true}';
        $contentWidget->save();
    }
}

$tableContent = Engine_Api::_()->getDbtable('admincontent', 'sitestore');
$tableContentName = $tableContent->info('name');
$select = new Zend_Db_Select($db);
$select_store = $select
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sitestore_index_view')
        ->limit(1);
$store = $select_store->query()->fetchAll();
if (!empty($store)) {
    $store_id = $store[0]['page_id'];
    // INSERT MAIN CONTAINER
    $mainContainer = $tableContent->createRow();
    $mainContainer->store_id = $store_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->admincontent_id;

    // INSERT MAIN-MIDDLE CONTAINER
    $mainMiddleContainer = $tableContent->createRow();
    $mainMiddleContainer->store_id = $store_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->admincontent_id;

    // INSERT MAIN-LEFT CONTAINER
    $mainLeftContainer = $tableContent->createRow();
    $mainLeftContainer->store_id = $store_id;
    $mainLeftContainer->type = 'container';
    $mainLeftContainer->name = 'right';
    $mainLeftContainer->parent_content_id = $container_id;
    $mainLeftContainer->order = 4;
    $mainLeftContainer->save();
    $left_id = $mainLeftContainer->admincontent_id;
    $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showmore', 9);

    //INSERT MAIN-MIDDLE TAB CONTAINER
    //if(Engine_Api::_()->getApi("settings", "core")->getSetting('sitestore.layout.setting', 1)){
    $middleTabContainer = $tableContent->createRow();
    $middleTabContainer->store_id = $store_id;
    $middleTabContainer->type = 'widget';
    $middleTabContainer->name = 'core.container-tabs';
    $middleTabContainer->parent_content_id = $middle_id;
    $middleTabContainer->order = 10;
    $middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
    $middleTabContainer->save();
    $middle_tab = $middleTabContainer->admincontent_id;
    // }      
    //INSERTING THUMB PHOTO WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.thumbphoto-sitestore', $middle_id, 3, '', 'true');

    if (empty($sitestore_layout_cover_photo)) {
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');
        //INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
            Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestoremember.storecover-photo-sitestoremembers', $middle_id, 2, '', 'true');
        }



        //INSERTING TITLE WIDGET
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.title-sitestore', $middle_id, 4, '', 'true');

        //INSERTING LIKE WIDGET
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'seaocore.like-button', $middle_id, 5, '', 'true');

        //INSERTING FOLLOW WIDGET
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'seaocore.seaocore-follow', $middle_id, 6, '', 'true');


        //INSERTING MAIN PHOTO WIDGET 
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.mainphoto-sitestore', $left_id, 10, '', 'true');
    } else {
        //INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');

        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.store-cover-information-sitestore', $middle_id, 2, '', 'true');
    }
    //INSERTING FACEBOOK LIKE WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'Facebookse.facebookse-commonlike', $middle_id, 7, '', 'true');
    }
    //INSERTING CONTACT DETAIL WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.contactdetails-sitestore', $middle_id, 8, '', 'true');

    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestoreproduct.sitestoreproduct-products', $left_id, 11, '', 'true', '{"title":"Top Selling Products","titleCount":true,"statistics":"","viewType":"gridview","columnWidth":"180","popularity":"last_order_all","product_type":"all","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","ratingType":"rating_avg","columnHeight":"328","itemCount":"3","truncation":"16","nomobile":"0","name":"sitestoreproduct.sitestoreproduct-products"}');

    //INSERTING OPTIONS WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.options-sitestore', $left_id, 12, '', 'true');

    //INSERTING WIDGET LINK WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.widgetlinks-sitestore', $left_id, 11, '', 'true');

    //INSERTING INFORMATION WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.information-sitestore', $left_id, 10, 'Information', 'true');

    //INSERTING WRITE SOMETHING ABOUT WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

    //INSERTING RATING WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestorereview.ratings-sitestorereviews', $left_id, 16, 'Ratings', 'true');
    }

    //INSERTING BADGE WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge')) {
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestorebadge.badge-sitestorebadge', $left_id, 17, 'Badge', 'true');
    }

    //INSERTING YOU MAY ALSO LIKE WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.suggestedstore-sitestore', $left_id, 18, 'You May Also Like', 'true');

    $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitestore.socialshare-sitestore"}';

    //INSERTING SOCIAL SHARE WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.socialshare-sitestore', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

//  //INSERTING FOUR SQUARE WIDGET 
//  Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.foursquare-sitestore', $left_id, 20,'','true');
    //INSERTING INSIGHTS WIDGET 
//   Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.insights-sitestore', $left_id, 21, 'Insights','true');
    //INSERTING FEATURED OWNER WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.featuredowner-sitestore', $left_id, 22, 'Owners', 'true');

    //INSERTING ALBUM WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.albums-sitestore', $left_id, 23, 'Albums', 'true');
    }

    //INSERTING STORE PROFILE PLAYER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestoremusic.profile-player', $left_id, 24, '', 'true');
    }

    //INSERTING 'Linked Stores' WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.favourite-store', $left_id, 25, 'Linked Stores', '', 'true');

    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestoreproduct.store-profile-products', $middle_tab, 1, 'Products', 'true', '{"columnHeight":325,"columnWidth":165,"defaultWidgetNo":13}');

    //INSERTING ACTIVITY FEED WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
        $advanced_activity_params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true', $advanced_activity_params);
    } else {
        Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'activity.feed', $middle_tab, 2, 'Updates', 'true');
    }

    //INSERTING INFORAMTION WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.info-sitestore', $middle_tab, 3, 'Info', 'true');

    //INSERTING OVERVIEW WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.overview-sitestore', $middle_tab, 4, 'Overview', 'true');

    //INSERTING LOCATION WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'sitestore.location-sitestore', $middle_tab, 5, 'Map', 'true');

    //INSERTING LINKS WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataWidget($tableContent, $tableContentName, $store_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');

    // Work for advancedactivity feed plugin(feed widget place by default).
    $aafModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled(
            'advancedactivity');
    //Quary for update widget of seaocore activity feed.
    if (!empty($aafModuleEnabled)) {
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_content')->where('name = ?', 'seaocore.feed');
        $results = $select->query()->fetchAll();
        if (!empty($results)) {
            foreach ($results as $result) {
                $params = '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
                $db->query('UPDATE  `engine4_core_content` SET  `name` =  "advancedactivity.home-feeds", `params`=\'' . $params . '\' WHERE `engine4_core_content`.`name` ="seaocore.feed";');
            }
        }

        $db->query("INSERT IGNORE INTO `engine4_advancedactivity_contents` ( `module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES ('sitestore', 'sitestore', 'Stores', '1', '999', '1')");
        $db->query("INSERT IGNORE INTO `engine4_advancedactivity_customtypes` ( `module_name`, `resource_type`, `resource_title`, `enabled`, `order`, `default`) VALUES ('sitestore', 'sitestore_store', 'Stores', '1', '999', '1')");
    }
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sitestore_index_pinboard_browse')
        ->limit(1);
$info = $select->query()->fetch();

if (empty($info)) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitestore_index_pinboard_browse',
        'displayname' => 'Browse Stores’ Pinboard View',
        'title' => 'Browse Stores’ Pinboard View',
        'description' => 'Browse Stores’ Pinboard View',
        'custom' => 0,
    ));
    $store_id = $db->lastInsertId('engine4_core_pages');

    $db->insert('engine4_core_content', array(
        'page_id' => $store_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
    ));
    $top_id = $db->lastInsertId('engine4_core_content');

    //CONTAINERS
    $db->insert('engine4_core_content', array(
        'page_id' => $store_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => Null,
        'order' => 2,
        'params' => '',
    ));
    $container_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $store_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'params' => '',
    ));
    $top_middle_id = $db->lastInsertId('engine4_core_content');

    //INSERT MAIN - MIDDLE CONTAINER
    $db->insert('engine4_core_content', array(
        'page_id' => $store_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 2,
        'params' => '',
    ));
    $middle_id = $db->lastInsertId('engine4_core_content');

    // Top Middle
    $db->insert('engine4_core_content', array(
        'page_id' => $store_id,
        'type' => 'widget',
        'name' => 'sitestore.browsenevigation-sitestore',
        'parent_content_id' => $top_middle_id,
        'order' => 1,
        'params' => '',
    ));

    //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
    $db->insert('engine4_core_content', array(
        'page_id' => $store_id,
        'type' => 'widget',
        'name' => 'sitestore.horizontal-search',
        'parent_content_id' => $middle_id,
        'order' => 2,
        'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1","browseredirect":"pinboard"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $store_id,
        'type' => 'widget',
        'name' => 'sitestore.pinboard-browse',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '{"title":"","titleCount":true,"postedby":"1","showoptions":["viewCount","likeCount","commentCount","price","location"],"detactLocation":"0","defaultlocationmiles":"1000","itemWidth":"237","withoutStretch":"0","itemCount":"12","show_buttons":["comment","like","share","facebook","twitter"],"truncationDescription":"100"}',
    ));
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'siteevent')
        ->where('enabled = ?', 1);
$is_siteevent_object = $select->query()->fetchObject();
if ($is_siteevent_object) {
    $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_store_host", "siteevent", \'{item:$subject} has made your store {var:$store} host of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.\', "");');
    $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES("SITEEVENT_STORE_HOST", "siteevent", "[host],[email],[sender],[event_title_with_link],[event_url],[store_title_with_link]");');
    $db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitestore_store', 'store_id', 'sitestore', '0', '0', 'Store Events', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitestore_admin_main_manage", "siteevent", "Manage Events", "", \'{"uri":"admin/siteevent/manage/index/contentType/sitestore_store/contentModule/sitestore"}\', "sitestore_admin_main", "", 1, 0, 24);');
    $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "siteevent.event.leader.owner.sitestore.store", "1");');
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitevideointegration')
        ->where('enabled = ?', 1);
$is_sitevideointegration_object = $select->query()->fetchObject();
if ($is_sitevideointegration_object) {
    $db->query("INSERT IGNORE INTO `engine4_sitevideo_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitestore_store', 'store_id', 'sitestore', '0', '0', 'Store Videos', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitestore_admin_main_managevideo", "sitevideointegration", "Manage Videos", "", \'{"uri":"admin/sitevideo/manage-video/index/contentType/sitestore_store/contentModule/sitestore"}\', "sitestore_admin_main", "", 0, 0, 24);');
    $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitevideo.video.leader.owner.sitestore.store", "1");');
}

$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'documentintegration')
        ->where('enabled = ?', 1);
$is_documentintegration_object = $select->query()->fetchObject();
if ($is_documentintegration_object) {
    $db->query("INSERT IGNORE INTO `engine4_document_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`) VALUES ('sitestore_store', 'store_id', 'sitestore', '0', '0', 'Store Documents')");
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitestore_admin_main_managedocument", "documentintegration", "Manage Documents", "", \'{"uri":"admin/document/manage-document/index/contentType/sitestore_store/contentModule/sitestore"}\', "sitestore_admin_main", "", 0, 0, 25);');
    $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "document.leader.owner.sitestore.store", "1");');
}