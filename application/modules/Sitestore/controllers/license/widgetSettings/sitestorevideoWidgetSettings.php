<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitestorevideoWidgetSettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
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
	$select = new Zend_Db_Select($db);
	$select_store = $select
										 ->from('engine4_core_pages', 'page_id')
										 ->where('name = ?', 'sitestore_index_view')
										 ->limit(1);
  $store = $select_store->query()->fetchAll();
	if(!empty($store)) {
		$store_id = $store[0]['page_id'];
		
		//INSERTING THE VIDEO WIDGET IN SITESTORE_ADMIN_CONTENT TABLE ALSO.
		Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestorevideo.profile-sitestorevideos', $store_id, 'Videos', 'true', '111');
	 
		//INSERTING THE VIDEO WIDGET IN CORE_CONTENT TABLE ALSO.
		Engine_Api::_()->getApi('layoutcore', 'sitestore')->setContentDefaultInfo('sitestorevideo.profile-sitestorevideos', $store_id, 'Videos', 'true', '111');
		
    //INSERTING THE VIDEO WIDGET IN SITESTORE_CONTENT TABLE ALSO.
    $select = new Zend_Db_Select($db);								
		$contentstore_ids = $select->from('engine4_sitestore_contentstores', 'contentstore_id')->query()->fetchAll();
    foreach ($contentstore_ids as $contentstore_id) {
			if(!empty($contentstore_id)) {
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestorevideo.profile-sitestorevideos', $contentstore_id['contentstore_id'], 'Videos', 'true', '111');
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
	;
	$infomation = $select->query()->fetch();
	$select = new Zend_Db_Select($db);
	$select
					->from('engine4_core_settings')
					->where('name = ?', 'sitestore.communityads')
					->where('value 	 = ?', 1)
					->limit(1);
    ;
  $rowinfo = $select->query()->fetch();

//  $select = new Zend_Db_Select($db);
//  $select
//          ->from('engine4_core_pages')
//          ->where('name = ?', 'sitestorevideo_index_browse')
//          ->limit(1);
//  ;
//  $info = $select->query()->fetch();
//  if ( empty($info) ) {
//    $db->insert('engine4_core_pages', array(
//        'name' => 'sitestorevideo_index_browse',
//        'displayname' => 'Browse Store videos',
//        'title' => 'Store Videos List',
//        'description' => 'This is the store videos.',
//        'custom' => 1,
//    ));
//    $store_id = $db->lastInsertId('engine4_core_pages');
////INSERT MAIN CONTAINER
//    $mainContainer = $contentTable->createRow();
//    $mainContainer->page_id = $store_id;
//    $mainContainer->type = 'container';
//    $mainContainer->name = 'main';
//    $mainContainer->order = 2;
//    $mainContainer->save();
//    $container_id = $mainContainer->content_id;
//
////INSERT MAIN - MIDDLE CONTAINER
//    $mainMiddleContainer = $contentTable->createRow();
//    $mainMiddleContainer->page_id = $store_id;
//    $mainMiddleContainer->type = 'container';
//    $mainMiddleContainer->name = 'middle';
//    $mainMiddleContainer->parent_content_id = $container_id;
//    $mainMiddleContainer->order = 6;
//    $mainMiddleContainer->save();
//    $middle_id = $mainMiddleContainer->content_id;
//
////INSERT MAIN - RIGHT CONTAINER
//    $mainRightContainer = $contentTable->createRow();
//    $mainRightContainer->page_id = $store_id;
//    $mainRightContainer->type = 'container';
//    $mainRightContainer->name = 'right';
//    $mainRightContainer->parent_content_id = $container_id;
//    $mainRightContainer->order = 5;
//    $mainRightContainer->save();
//    $right_id = $mainRightContainer->content_id;
//
////INSERT TOP CONTAINER
//    $topContainer = $contentTable->createRow();
//    $topContainer->page_id = $store_id;
//    $topContainer->type = 'container';
//    $topContainer->name = 'top';
//    $topContainer->order = 1;
//    $topContainer->save();
//    $top_id = $topContainer->content_id;
//
////INSERT TOP- MIDDLE CONTAINER
//    $topMiddleContainer = $contentTable->createRow();
//    $topMiddleContainer->page_id = $store_id;
//    $topMiddleContainer->type = 'container';
//    $topMiddleContainer->name = 'middle';
//    $topMiddleContainer->parent_content_id = $top_id;
//    $topMiddleContainer->order = 6;
//    $topMiddleContainer->save();
//    $top_middle_id = $topMiddleContainer->content_id;
//
//    //INSERT NAVIGATION WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.browsenevigation-sitestore', $top_middle_id, 1);
//
////INSERT VIDEO WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorevideo.sitestore-video', $middle_id, 2);
//
//    //INSERT SEARCH STORE VIDEO WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorevideo.search-sitestorevideo', $right_id, 3, "", "true");
//
//    //INSERT FEATURED STORE VIDEO WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorevideo.homefeaturelist-sitestorevideos', $right_id, 4, "Featured Videos", "true");
//
//    //INSERT SPONSORED STORE VIDEO WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorevideo.sitestore-sponsoredvideo', $right_id, 5, "Sponsored Videos", "true");
//
//    //INSERT MOST COMMENTED STORE VIDEO WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorevideo.homecomment-sitestorevideos', $right_id, 6, "Most Commented Videos", "true");
//
//    //INSERT MOST VIEWED STORE VIDEO WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorevideo.homeview-sitestorevideos', $right_id, 7, "Most Viewed Videos", "true");
//
//    //INSERT MOST LIKED STORE VIDEO WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorevideo.homelike-sitestorevideos', $right_id, 8, "Most Liked Videos", "true");
//
//    //INSERT TOP RATED STORE VIDEO WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorevideo.homerate-sitestorevideos', $right_id, 9, "Top Rated Videos", "true");
//
//    //INSERT RECENT STORE VIDEO WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorevideo.homerecent-sitestorevideos', $right_id, 10, "Recent Videos", "true");
//
//    if ( !empty($infomation)  && !empty($rowinfo) ) {
//      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-ads', $right_id, 11, "", "true");
//    }
//  }

  $select = new Zend_Db_Select($db);

  // Check if it's already been placed
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitestorevideo_index_view')
          ->limit(1);
  ;
  $info = $select->query()->fetch();

  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitestorevideo_index_view',
        'displayname' => 'Stores - Store Video View Page',
        'title' => 'View Store Video',
        'description' => 'This is the view page for a store video.',
        'custom' => 0,
        'provides' => 'subject=sitestorevideo',
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
        'name' => 'sitestorevideo.video-content',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
    ));

    // right column
    $db->insert('engine4_core_content', array(
        'page_id' => $store_id,
        'type' => 'widget',
        'name' => 'sitestorevideo.show-same-tags',
        'parent_content_id' => $right_id,
        'order' => 1,
        'params' => '{"title":"Similar Videos"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $store_id,
        'type' => 'widget',
        'name' => 'sitestorevideo.show-also-liked',
        'parent_content_id' => $right_id,
        'order' => 2,
        'params' => '{"title":"People Also Liked"}',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $store_id,
        'type' => 'widget',
        'name' => 'sitestorevideo.show-same-poster',
        'parent_content_id' => $right_id,
        'order' => 3,
        'params' => '{"title":"Other Videos From Store"}',
    ));

    if ( !empty($infomation)  && !empty($rowinfo) ) {
      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-ads', $right_id, 4, "", "true");
    }

  }

  $select = new Zend_Db_Select($db);
	$select
					->from('engine4_core_modules')
					->where('name = ?', 'mobi')
					->where('enabled 	 = ?', 1)
					->limit(1);
	;  

	$infomation = $select->query()->fetch();
	if(!empty($infomation)) {
		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_pages')
						->where('name = ?', 'sitestorevideo_mobi_view')
						->limit(1);
		;
		$info = $select->query()->fetch();
		if (empty($info)) {
			$db->insert('engine4_core_pages', array(
					'name' => 'sitestorevideo_mobi_view',
					'displayname' => 'Mobile Store Video Profile',
					'title' => 'Mobile Store Video Profile',
					'description' => 'This is the mobile verison of a Store video profile page.',
					'custom' => 0,
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
					'name' => 'sitestorevideo.video-content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '',
			));

			// right column
			$db->insert('engine4_core_content', array(
					'page_id' => $store_id,
					'type' => 'widget',
					'name' => 'sitestorevideo.show-same-tags',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => '{"title":"Similar Videos"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $store_id,
					'type' => 'widget',
					'name' => 'sitestorevideo.show-also-liked',
					'parent_content_id' => $right_id,
					'order' => 2,
					'params' => '{"title":"People Also Liked"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $store_id,
					'type' => 'widget',
					'name' => 'sitestorevideo.show-same-poster',
					'parent_content_id' => $right_id,
					'order' => 3,
					'params' => '{"title":"From the same Member"}',
			));

		}
	}

	$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestorevideo_admin_tabbedwidget", "sitestorevideo", "Tabbed Videos Widget", "", \'{"route":"admin_default","module":"sitestorevideo","controller":"settings", "action": "widget"}\', "sitestorevideo_admin_main", "", 1, 0, 5)');

	$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestorevideo_admin_dayitemwidget", "sitestorevideo", "Video of the Day", "", \'{"route":"admin_default","module":"sitestorevideo","controller":"settings", "action": "manage-day-items"}\', "sitestorevideo_admin_main", "", 1, 0, 6)');

//	$select = new Zend_Db_Select($db);
//	$select
//					->from('engine4_core_pages')
//					->where('name = ?', 'sitestorevideo_index_home')
//					->limit(1);
//	$info = $select->query()->fetch();
//	if (empty($info)) {
//		$db->insert('engine4_core_pages', array(
//				'name' => 'sitestorevideo_index_home',
//				'displayname' => 'Store Videos Home',
//				'title' => 'Store Videos Home',
//				'description' => 'This is store video home store.',
//				'custom' => 1
//		));
//		$store_id = $db->lastInsertId('engine4_core_pages');
//
//		// containers
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'container',
//				'name' => 'main',
//				'parent_content_id' => null,
//				'order' => 2,
//				'params' => '',
//		));
//		$container_id = $db->lastInsertId('engine4_core_content');
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'container',
//				'name' => 'right',
//				'parent_content_id' => $container_id,
//				'order' => 5,
//				'params' => '',
//		));
//		$right_id = $db->lastInsertId('engine4_core_content');
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'container',
//				'name' => 'left',
//				'parent_content_id' => $container_id,
//				'order' => 4,
//				'params' => '',
//		));
//		$left_id = $db->lastInsertId('engine4_core_content');
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'container',
//				'name' => 'top',
//				'parent_content_id' => null,
//				'order' => 1,
//				'params' => '',
//		));
//		$top_id = $db->lastInsertId('engine4_core_content');
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'container',
//				'name' => 'middle',
//				'parent_content_id' => $top_id,
//				'order' => 6,
//				'params' => '',
//		));
//		$top_middle_id = $db->lastInsertId('engine4_core_content');
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'container',
//				'name' => 'middle',
//				'parent_content_id' => $container_id,
//				'order' => 6,
//				'params' => '',
//		));
//		$middle_id = $db->lastInsertId('engine4_core_content');
//
//	// Top Middle
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestore.browsenevigation-sitestore',
//				'parent_content_id' => $top_middle_id,
//				'order' => 3,
//				'params' => '',
//		));
//
//		// Left
//	
//		//INSERT TOP RATED STORE VIDEO WIDGET
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorevideo.homerate-sitestorevideos',
//				'parent_content_id' => $left_id,
//				'order' => 13,
//				'params' => '{"title":"Top Rated Videos","titleCount":"true"}',
//		));
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorevideo.homerecent-sitestorevideos',
//				'parent_content_id' => $left_id,
//				'order' => 14,
//				'params' => '{"title":"Recent Videos","titleCount":"true"}',
//		));
//
//		//INSERT MOST VIEWED STORE VIDEO WIDGET
//			Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorevideo.homeview-sitestorevideos', $right_id, 20, "Most Viewed Videos", "true");
//
//	// Middele
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorevideo.featured-videos-carousel',
//				'parent_content_id' => $middle_id,
//				'order' => 16,
//				'params' => '{"title":"Featured Videos","vertical":"0", "noOfRow":"2","inOneRow":"3","interval":"250","name":"sitestorevideo.featured-videos-carousel"}',
//		));
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorevideo.list-videos-tabs-view',
//				'parent_content_id' => $middle_id,
//				'order' => 17,
//				'params' => '{"title":"Videos","margin_photo":"12"}',
//		));
//		// Right Side
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorevideo.sitestorevideolist-link',
//				'parent_content_id' => $right_id,
//				'order' => 19,
//				'params' => '',
//		));
//
//		// Right Side
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorevideo.search-sitestorevideo',
//				'parent_content_id' => $right_id,
//				'order' => 18,
//				'params' => '',
//		));
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorevideo.video-of-the-day',
//				'parent_content_id' => $left_id,
//				'order' => 12,
//				'params' => '{"title":"Video of the Day"}',
//		));
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorevideo.homefeaturelist-sitestorevideos',
//				'parent_content_id' => $right_id,
//				'order' => 21,
//				'params' => '{"title":"Featured Videos","itemCountPerStore":3}',
//		));
//
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorevideo.homecomment-sitestorevideos',
//				'parent_content_id' => $right_id,
//				'order' => 22,
//				'params' => '{"title":"Most Commented Videos","titleCount":"true"}',
//		));
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorevideo.homelike-sitestorevideos',
//				'parent_content_id' => $right_id,
//				'order' => 23,
//				'params' => '{"title":"Most Liked Videos","titleCount":"true"}',
//		));
//	}	
	
	
	
	
}

?>