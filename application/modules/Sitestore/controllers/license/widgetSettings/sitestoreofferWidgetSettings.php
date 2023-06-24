<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitestoreofferWidgetSettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
//START LANGUAGE WORK
Engine_Api::_()->getApi('language', 'sitestore')->languageChanges();
//END LANGUAGE WORK 
//GET DB
$db = Zend_Db_Table_Abstract::getDefaultAdapter();	

//CHECK THAT SITESTORE PLUGIN IS ACTIVATED OR NOT
$select = new Zend_Db_Select($db);
	$select
  ->from('engine4_core_settings')
  ->where('name = ?', 'sitestore.is.active')
	->limit(1);
$sitestore_settings = $select->query()->fetchAll();
if(!empty($sitestore_settings)) {
	$sitestore_is_active = $sitestore_settings[0]['value'];
}
else {
	$sitestore_is_active = 0;
}

//CHECK THAT SITESTORE PLUGIN IS INSTALLED OR NOT
$select = new Zend_Db_Select($db);
	$select
	  ->from('engine4_core_modules')
  ->where('name = ?', 'sitestore')
	->where('enabled = ?', 1);
$check_sitestore = $select->query()->fetchObject();
if(!empty($check_sitestore)  && !empty($sitestore_is_active)) {
	//PUT SITESTORE OFFER TAB AT SITESTORE PROFILE STORE
	$select = new Zend_Db_Select($db);
	$select_store = $select
											 ->from('engine4_core_pages', 'page_id')
											 ->where('name = ?', 'sitestore_index_view')
											 ->limit(1);
  $store = $select_store->query()->fetchAll();
	if(!empty($store)) {
		$store_id = $store[0]['page_id'];
		//INSERTING THE DOCUMENT WIDGET IN SITESTORE_ADMIN_CONTENT TABLE ALSO.
		Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestoreoffer.profile-sitestoreoffers', $store_id, 'Coupons', 'true', '116');
	 
		//INSERTING THE DOCUMENT WIDGET IN CORE_CONTENT TABLE ALSO.
		Engine_Api::_()->getApi('layoutcore', 'sitestore')->setContentDefaultInfo('sitestoreoffer.profile-sitestoreoffers', $store_id, 'Coupons', 'true', '116');
		
    //INSERTING THE DOCUMENT WIDGET IN SITESTORE_CONTENT TABLE ALSO.
    $select = new Zend_Db_Select($db);								
		$contentstore_ids = $select->from('engine4_sitestore_contentstores', 'contentstore_id')->query()->fetchAll();
    foreach ($contentstore_ids as $contentstore_id) {
			if(!empty($contentstore_id)) {
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestoreoffer.profile-sitestoreoffers', $contentstore_id['contentstore_id'], 'Coupons', 'true', '116');
			}
		}
	}

	//PUT TOP RATED WIDGET AT STORE HOME
	$select = new Zend_Db_Select($db);
	$select_store = $select
											 ->from('engine4_core_pages', 'page_id')
											 ->where('name = ?', 'sitestore_index_home')
											 ->limit(1);
  $store = $select_store->query()->fetchAll();
	if(!empty($store)) {
		$store_id = $store[0]['page_id'];
		$select = new Zend_Db_Select($db);
		$select_content = $select
														->from('engine4_core_content')
														->where('page_id = ?', $store_id)
														->where('type = ?', 'widget')
														->where('name = ?', 'sitestoreoffer.sitestore-hotoffer')
														->limit(1);
		$content = $select_content->query()->fetchAll();
		if(empty($content)) {
			$select = new Zend_Db_Select($db);
			$select_container = $select
																->from('engine4_core_content', 'content_id')
																->where('page_id = ?', $store_id)
																->where('type = ?', 'container')
																->where('name = ?', 'main')
																->limit(1);
			$container = $select_container->query()->fetchAll();
			if(!empty($container)) {
				$container_id = $container[0]['content_id'];

				$select = new Zend_Db_Select($db);
				$select_left = $select
																->from('engine4_core_content')
																->where('parent_content_id = ?', $container_id)
																->where('type = ?', 'container')
																->where('name = ?', 'left')
																->limit(1);
				$left = $select_left->query()->fetchAll();
				if(!empty($left)) {
					$left_id = $left[0]['content_id'];
					$select = new Zend_Db_Select($db);
					$select_tab = $select
																->from('engine4_core_content')
																->where('type = ?', 'widget')
																->where('name = ?', 'core.container-tabs')
																->where('page_id = ?', $store_id)
																->limit(1);
					$tab = $select_tab->query()->fetchAll();
					if(!empty($tab)) {
						$tab_id = $tab[0]['content_id'];
					}

					$db->insert('engine4_core_content', array(
					'page_id' => $store_id,
					'type' => 'widget',
					'name' => 'sitestoreoffer.sitestore-hotoffer',
					'parent_content_id' => ($left_id ? $left_id : $tab_id),
					'order' => 999,
					'params' => '{"title":"Hot Store Coupons","titleCount":"true"}',
					));
				}
			}
		}
	}
	$contentTable = Engine_Api::_()->getDbtable('content', 'core');
  $contentTableName = $contentTable->info('name');

   $select = new Zend_Db_Select($db);
   $select
 					->from('engine4_core_modules')
 					->where('name = ?', 'communityad')
 					->where('enabled 	 = ?', 1)
 					->limit(1);
 	$infomation = $select->query()->fetch();
 	$select = new Zend_Db_Select($db);
 	$select
 					->from('engine4_core_settings')
 					->where('name = ?', 'sitestore.communityads')
 					->where('value 	 = ?', 1)
 					->limit(1);
 	$rowinfo = $select->query()->fetch();
 
   $select = new Zend_Db_Select($db);
   $select
           ->from('engine4_core_pages')
           ->where('name = ?', 'sitestoreoffer_index_browse')
           ->limit(1);
   $info = $select->query()->fetch();
   if ( empty($info) ) {
     $db->insert('engine4_core_pages', array(
         'name' => 'sitestoreoffer_index_browse',
         'displayname' => 'Stores: Browse Coupons',
         'title' => 'Store Coupons List',
         'description' => 'This is the store coupons.',
         'custom' => 1,
     ));
     $store_id = $db->lastInsertId('engine4_core_pages');
 //INSERT MAIN CONTAINER
     $mainContainer = $contentTable->createRow();
     $mainContainer->page_id = $store_id;
     $mainContainer->type = 'container';
     $mainContainer->name = 'main';
     $mainContainer->order = 2;
     $mainContainer->save();
     $container_id = $mainContainer->content_id;
 
 //INSERT MAIN - MIDDLE CONTAINER
     $mainMiddleContainer = $contentTable->createRow();
     $mainMiddleContainer->page_id = $store_id;
     $mainMiddleContainer->type = 'container';
     $mainMiddleContainer->name = 'middle';
     $mainMiddleContainer->parent_content_id = $container_id;
     $mainMiddleContainer->order = 6;
     $mainMiddleContainer->save();
     $middle_id = $mainMiddleContainer->content_id;
 
 //INSERT MAIN - RIGHT CONTAINER
     $mainRightContainer = $contentTable->createRow();
     $mainRightContainer->page_id = $store_id;
     $mainRightContainer->type = 'container';
     $mainRightContainer->name = 'right';
     $mainRightContainer->parent_content_id = $container_id;
     $mainRightContainer->order = 5;
     $mainRightContainer->save();
     $right_id = $mainRightContainer->content_id;
 
 //INSERT TOP CONTAINER
     $topContainer = $contentTable->createRow();
     $topContainer->page_id = $store_id;
     $topContainer->type = 'container';
     $topContainer->name = 'top';
     $topContainer->order = 1;
     $topContainer->save();
     $top_id = $topContainer->content_id;
 
 //INSERT TOP- MIDDLE CONTAINER
     $topMiddleContainer = $contentTable->createRow();
     $topMiddleContainer->page_id = $store_id;
     $topMiddleContainer->type = 'container';
     $topMiddleContainer->name = 'middle';
     $topMiddleContainer->parent_content_id = $top_id;
     $topMiddleContainer->order = 6;
     $topMiddleContainer->save();
     $top_middle_id = $topMiddleContainer->content_id;
 
     //INSERT NAVIGATION WIDGET
     Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.browsenevigation-sitestore', $top_middle_id, 1);
 
 //INSERT OFFER WIDGET
     Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreoffer.sitestore-offer', $middle_id, 2);
 
     //INSERT SEARCH STORE OFFER WIDGET
     Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreoffer.search-sitestoreoffer', $right_id, 3, "", "true");
 
     //INSERT HOT STORE OFFER WIDGET
     Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreoffer.sitestore-hotoffer', $right_id, 4, "Hot Store Coupons", "true");
 
     //INSERT LATEST STORE OFFER WIDGET
     Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreoffer.sitestore-latestoffer', $right_id, 5, "Latest Store Coupons", "true");
   
    //INSERT SPONSORED STORE OFFER WIDGET
//     Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreoffer.sitestore-sponsoredoffer', $right_id, 6, "Sponsored Coupons", "true");
 
     //INSERT AVAILABLE STORE OFFER WIDGET
     Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreoffer.sitestore-dateoffer', $right_id, 7, "Available Coupons", "true");
 
     if ( $infomation && $rowinfo ) {
 
       //INSERT STORE ADA WIDGET
       Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-ads', $right_id, 7, "", "true");
     }
   }
   // Check if it's already been placed
 	$select = new Zend_Db_Select($db);
 	$select
 					->from('engine4_core_pages')
 					->where('name = ?', 'sitestoreoffer_index_view')
 					->limit(1);
 
 	$info = $select->query()->fetch();
 
 	if ( empty($info) ) {
 		$db->insert('engine4_core_pages', array(
 				'name' => 'sitestoreoffer_index_view',
 				'displayname' => 'Store Coupon View Page',
 				'title' => 'View Store Coupon',
 				'description' => 'This is the view store for a store coupon.',
 				'custom' => 1,
 				'provides' => 'subject=sitestoreoffer',
 		));
 		$store_id = $db->lastInsertId('engine4_core_pages');
 
 		// containers
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'container',
 				'name' => 'main',
 				'parent_content_id' => null,
 				'order' => 1,
 				'params' => '',
 		));
 		$container_id = $db->lastInsertId('engine4_core_content');
 
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'container',
 				'name' => 'right',
 				'parent_content_id' => $container_id,
 				'order' => 1,
 				'params' => '',
 		));
 		$right_id = $db->lastInsertId('engine4_core_content');
 
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'container',
 				'name' => 'middle',
 				'parent_content_id' => $container_id,
 				'order' => 3,
 				'params' => '',
 		));
 		$middle_id = $db->lastInsertId('engine4_core_content');
 
 		// middle column content
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'widget',
 				'name' => 'sitestoreoffer.offer-content',
 				'parent_content_id' => $middle_id,
 				'order' => 1,
 				'params' => '',
 		));
 
     if ( !empty($infomation)  && !empty($rowinfo) ) {
       Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-ads', $right_id, 4, "", "true");
     }
 
 	}

  $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestoreoffer_admin_main_offer_tab", "sitestoreoffer", "Tabbed Coupons Widget", "", \'{"route":"admin_default","module":"sitestoreoffer","controller":"settings", "action": "widget"}\', "sitestoreoffer_admin_main", "", 1, 0, 3)');

	$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestoreoffer_admin_main_dayitems", "sitestoreoffer", "Coupon of the Day", "", \'{"route":"admin_default","module":"sitestoreoffer","controller":"settings", "action": "manage-day-items"}\', "sitestoreoffer_admin_main", "", 1, 0, 4)');

 	$select = new Zend_Db_Select($db);
 	$select
 					->from('engine4_core_pages')
 					->where('name = ?', 'sitestoreoffer_index_home')
 					->limit(1);
 	$info = $select->query()->fetch();
 	if (empty($info)) {
 		$db->insert('engine4_core_pages', array(
 				'name' => 'sitestoreoffer_index_home',
 				'displayname' => 'Store Coupons Home',
 				'title' => 'Store Coupons Home',
 				'description' => 'This is store coupon home store.',
 				'custom' => 1
 		));
 		$store_id = $db->lastInsertId('engine4_core_pages');
 
 		// containers
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'container',
 				'name' => 'main',
 				'parent_content_id' => null,
 				'order' => 2,
 				'params' => '',
 		));
 		$container_id = $db->lastInsertId('engine4_core_content');
 
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'container',
 				'name' => 'right',
 				'parent_content_id' => $container_id,
 				'order' => 5,
 				'params' => '',
 		));
 		$right_id = $db->lastInsertId('engine4_core_content');
 
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'container',
 				'name' => 'left',
 				'parent_content_id' => $container_id,
 				'order' => 4,
 				'params' => '',
 		));
 		$left_id = $db->lastInsertId('engine4_core_content');
 
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'container',
 				'name' => 'top',
 				'parent_content_id' => null,
 				'order' => 1,
 				'params' => '',
 		));
 		$top_id = $db->lastInsertId('engine4_core_content');
 
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'container',
 				'name' => 'middle',
 				'parent_content_id' => $top_id,
 				'order' => 6,
 				'params' => '',
 		));
 		$top_middle_id = $db->lastInsertId('engine4_core_content');
 
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'container',
 				'name' => 'middle',
 				'parent_content_id' => $container_id,
 				'order' => 6,
 				'params' => '',
 		));
 		$middle_id = $db->lastInsertId('engine4_core_content');
 
 	// Top Middle
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'widget',
 				'name' => 'sitestore.browsenevigation-sitestore',
 				'parent_content_id' => $top_middle_id,
 				'order' => 3,
 				'params' => '',
 		));
 
 		// Left
 	
 		//INSERT TOP RATED STORE OFFER WIDGET
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'widget',
 				'name' => 'sitestoreoffer.offers-sitestoreoffers',
 				'parent_content_id' => $left_id,
 				'order' => 13,
 				'params' => '{"title":"Most Popular Coupons","popularity":"popular","titleCount":"true"}',
 		));
 
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'widget',
 				'name' => 'sitestoreoffer.offers-sitestoreoffers',
 				'parent_content_id' => $left_id,
 				'order' => 14,
 				'params' => '{"title":"Most Viewed Coupons","popularity":"view_count","titleCount":"true"}',
 		));
 
 	// Middele
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'widget',
 				'name' => 'sitestoreoffer.hot-offers-slideshow',
 				'parent_content_id' => $middle_id,
 				'order' => 16,
 				'params' => '{"title":"Hot Coupons","vertical":"0", "noOfRow":"2","inOneRow":"3","interval":"250","name":"sitestoreoffer.hot-offers-slideshow"}',
 		));
 
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'widget',
 				'name' => 'sitestoreoffer.list-offers-tabs-view',
 				'parent_content_id' => $middle_id,
 				'order' => 17,
 				'params' => '{"title":"Coupons","margin_photo":"12"}',
 		));
 		// Right Side
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'widget',
 				'name' => 'sitestoreoffer.sitestoreofferlist-link',
 				'parent_content_id' => $right_id,
 				'order' => 19,
 				'params' => '',
 		));
 
 		// Right Side
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'widget',
 				'name' => 'sitestoreoffer.search-sitestoreoffer',
 				'parent_content_id' => $right_id,
 				'order' => 18,
 				'params' => '',
 		));
 
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'widget',
 				'name' => 'sitestoreoffer.offer-of-the-day',
 				'parent_content_id' => $left_id,
 				'order' => 12,
 				'params' => '{"title":"Coupon of the Day"}',
 		));
 
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'widget',
 				'name' => 'sitestoreoffer.offers-sitestoreoffers',
 				'parent_content_id' => $right_id,
 				'order' => 22,
 				'params' => '{"title":"Most Commented Coupons","popularity":"comment_count","titleCount":"true"}',
 		));
 
 		$db->insert('engine4_core_content', array(
 				'page_id' => $store_id,
 				'type' => 'widget',
 				'name' => 'sitestoreoffer.offers-sitestoreoffers',
 				'parent_content_id' => $right_id,
 				'order' => 23,
 				'params' => '{"title":"Most Liked Coupons","popularity":"like_count","titleCount":"true"}',
 		));
 		
     //INSERT HOT STORE OFFER WIDGET
//     Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreoffer.sitestore-hotoffer', $left_id, 15, "Hot Store Coupons", "true");
 
     //INSERT LATEST STORE OFFER WIDGET
//     Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreoffer.sitestore-latestoffer', $left_id, 16, "Latest Store Coupons", "true");
 	}
}

?>