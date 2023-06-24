<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitestoreviewWidgetSettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

//START LANGUAGE WORK
Engine_Api::_()->getApi('language', 'sitestore')->languageChanges();
//END LANGUAGE WORK

//GET DB
$db = Zend_Db_Table_Abstract::getDefaultAdapter();	
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitestorereview_admin_main_level", "sitestorereview", "Member Level Settings", "", \'{"route":"admin_default","module":"sitestorereview","controller":"settings","action":"level"}\', "sitestorereview_admin_main", "", 2)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestorereview_admin_main_dayitems", "sitestorereview", "Review of the Day", "", \'{"route":"admin_default","module":"sitestorereview","controller":"settings", "action": "manage-day-items"}\', "sitestorereview_admin_main", "", 1, 0, 4)');


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
		
		//INSERTING THE POLL WIDGET IN SITESTORE_ADMIN_CONTENT TABLE ALSO.
		Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestorereview.profile-sitestorereviews', $store_id, 'Reviews', 'true', '113');
	 
		//INSERTING THE POLL WIDGET IN CORE_CONTENT TABLE ALSO.
		Engine_Api::_()->getApi('layoutcore', 'sitestore')->setContentDefaultInfo('sitestorereview.profile-sitestorereviews', $store_id, 'Reviews', 'true', '113');
		
    //INSERTING THE POLL WIDGET IN SITESTORE_CONTENT TABLE ALSO.
    $select = new Zend_Db_Select($db);								
		$contentstore_ids = $select->from('engine4_sitestore_contentstores', 'contentstore_id')->query()->fetchAll();
    foreach ($contentstore_ids as $contentstore_id) {
			if(!empty($contentstore_id)) {
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestorereview.profile-sitestorereviews', $contentstore_id['contentstore_id'], 'Reviews', 'true', '113');
        
        $select = new Zend_Db_Select($db);
		    $select_content = $select
		                 ->from('engine4_sitestore_content')
		                 ->where('contentstore_id = ?', $contentstore_id['contentstore_id'])
		                 ->where('type = ?', 'widget')
		                 ->where('name = ?', 'sitestorereview.ratings-sitestorereviews')
		                 ->limit(1);
		    $content = $select_content->query()->fetchAll();
		    if(empty($content)) {
		      $select = new Zend_Db_Select($db);
		      $select_container = $select
		                   ->from('engine4_sitestore_content', 'content_id')
		                   ->where('contentstore_id = ?', $contentstore_id['contentstore_id'])
		                   ->where('type = ?', 'container')
		                   ->limit(1);
		      $container = $select_container->query()->fetchAll();
		      if(!empty($container)) {
		        $container_id = $container[0]['content_id'];
		        $select = new Zend_Db_Select($db);
		        $select_left = $select
		                   ->from('engine4_sitestore_content')
		                   ->where('parent_content_id = ?', $container_id)
		                   ->where('type = ?', 'container')
												->where('contentstore_id = ?', $contentstore_id['contentstore_id'])
												->where('name in (?)', array('left', 'right'))
		                   ->limit(1);
		        $left = $select_left->query()->fetchAll();
		        if(!empty($left)) {
			        $left_id = $left[0]['content_id'];
			        $db->insert('engine4_sitestore_content', array(
			        'contentstore_id' => $contentstore_id['contentstore_id'],
			        'type' => 'widget',
			        'name' => 'sitestorereview.ratings-sitestorereviews',
			        'parent_content_id' => $left_id,
			        'order' => 15,
			        'params' => '{"title":"Ratings","titleCount":""}',
			        ));
		       	}
		      }
		    }       
			}
		}
		
	  $select = new Zend_Db_Select($db);
    $select_content = $select
                 ->from('engine4_sitestore_admincontent')
                 ->where('store_id = ?', $store_id)
                 ->where('type = ?', 'widget')
                 ->where('name = ?', 'sitestorereview.ratings-sitestorereviews')
                 ->limit(1);
    $content = $select_content->query()->fetchAll();
    if(empty($content)) {
      $select = new Zend_Db_Select($db);
      $select_container = $select
                   ->from('engine4_sitestore_admincontent', 'admincontent_id')
                   ->where('store_id = ?', $store_id)
                   ->where('type = ?', 'container')
                   ->limit(1);
      $container = $select_container->query()->fetchAll();
      if(!empty($container)) {
        $container_id = $container[0]['admincontent_id'];
        $select = new Zend_Db_Select($db);
        $select_left = $select
                   ->from('engine4_sitestore_admincontent')
                   ->where('parent_content_id = ?', $container_id)
                   ->where('type = ?', 'container')
										->where('store_id = ?', $store_id)
										->where('name in (?)', array('left', 'right'))
                   ->limit(1);
        $left = $select_left->query()->fetchAll();
        if(!empty($left)) {
	        $left_id = $left[0]['admincontent_id'];
	        $db->insert('engine4_sitestore_admincontent', array(
	        'store_id' => $store_id,
	        'type' => 'widget',
	        'name' => 'sitestorereview.ratings-sitestorereviews',
	        'parent_content_id' => $left_id,
	        'order' => 15,
	        'params' => '{"title":"Ratings","titleCount":""}',
	        ));
       	}
      }
    } 
    
	  $select = new Zend_Db_Select($db);
    $select_content = $select
                 ->from('engine4_core_content')
                 ->where('page_id = ?', $store_id)
                 ->where('type = ?', 'widget')
                 ->where('name = ?', 'sitestorereview.ratings-sitestorereviews')
                 ->limit(1);
    $content = $select_content->query()->fetchAll();
    if(empty($content)) {
      $select = new Zend_Db_Select($db);
      $select_container = $select
                   ->from('engine4_core_content', 'content_id')
                   ->where('page_id = ?', $store_id)
                   ->where('type = ?', 'container')
                   ->limit(1);
      $container = $select_container->query()->fetchAll();
      if(!empty($container)) {
        $container_id = $container[0]['content_id'];
        $select = new Zend_Db_Select($db);
        $select_left = $select
                   ->from('engine4_core_content')
                   ->where('parent_content_id = ?', $container_id)
                   ->where('type = ?', 'container')
										->where('page_id = ?', $store_id)
										->where('name in (?)', array('left', 'right'))
                   ->limit(1);
        $left = $select_left->query()->fetchAll();
        if(!empty($left)) {
	        $left_id = $left[0]['content_id'];
	        $db->insert('engine4_core_content', array(
	        'page_id' => $store_id,
	        'type' => 'widget',
	        'name' => 'sitestorereview.ratings-sitestorereviews',
	        'parent_content_id' => $left_id,
	        'order' => 15,
	        'params' => '{"title":"Ratings","titleCount":""}',
	        ));
       	}
      }
    }   
	}
	
	//PUT TOP RATED WIDGET AT STORE HOME
//  $select = new Zend_Db_Select($db);
//	$select_store = $select
//											 ->from('engine4_core_pages', 'page_id')
//											 ->where('name = ?', 'sitestore_index_home')
//											 ->limit(1);
//  $store = $select_store->query()->fetchAll();
//	if(!empty($store)) {
//		$store_id = $store[0]['page_id'];
//		$select = new Zend_Db_Select($db);
//		$select_content = $select
//														->from('engine4_core_content')
//														->where('page_id = ?', $store_id)
//														->where('type = ?', 'widget')
//														->where('name = ?', 'sitestorereview.topratedstores-sitestorereviews')
//														->limit(1);
//		$content = $select_content->query()->fetchAll();
//		if(empty($content)) {
//			$select = new Zend_Db_Select($db);
//			$select_container = $select
//																->from('engine4_core_content', 'content_id')
//															->where('page_id = ?', $store_id)
//																->where('type = ?', 'container')
//																->where('name = ?', 'main')
//																->limit(1);
//			$container = $select_container->query()->fetchAll();
//		  if(!empty($container)) {
//				$container_id = $container[0]['content_id'];
//				$select = new Zend_Db_Select($db);
//				$select_left = $select
//																->from('engine4_core_content')
//															->where('parent_content_id = ?', $container_id)
//																->where('type = ?', 'container')
//																->where('name = ?', 'left')
//																->limit(1);
//				$left = $select_left->query()->fetchAll();
//				if(!empty($left)) {
//					$left_id = $left[0]['content_id'];
//					$select = new Zend_Db_Select($db);
//						$select_tab = $select
//																->from('engine4_core_content')
//																->where('type = ?', 'widget')
//																->where('name = ?', 'core.container-tabs')
//																->where('page_id = ?', $store_id)
//																->limit(1);
//						$tab = $select_tab->query()->fetchAll();
//						if(!empty($tab)) {
//							$tab_id = $tab[0]['content_id'];
//					}
//
//					$db->insert('engine4_core_content', array(
//					'page_id' => $store_id,
//						'type' => 'widget',
//						'name' => 'sitestorereview.topratedstores-sitestorereviews',
//						'parent_content_id' => ($left_id ? $left_id : $tab_id),
//						'order' => 998,
//						'params' => '{"title":"Top Rated Stores","titleCount":"true"}',
//						));
//			  }
//			}
//		}
//	}
	
	
	
	
	
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
//          ->where('name = ?', 'sitestorereview_index_browse')
//          ->limit(1);
//  ;
//  $info = $select->query()->fetch();
//  if ( empty($info) ) {
//    $db->insert('engine4_core_pages', array(
//        'name' => 'sitestorereview_index_browse',
//        'displayname' => 'Browse Store Reviews',
//        'title' => 'Store Reviews List',
//        'description' => 'This is the store reviews.',
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
////INSERT REVIEW WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.sitestore-review', $middle_id, 2,"Reviews");
//
//    //INSERT SEARCH STORE REVIEW WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.search-sitestorereview', $right_id, 3, "", "true");
//
//    //INSERT FEATURED STORE REVIEW WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.featured-sitestorereviews', $right_id, 4, "Featured Reviews", "true");
//
//    //INSERT MOST COMMENTED STORE REVIEW WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.homecomment-sitestorereviews', $right_id, 5, "Most Commented Reviews", "true");
//
//    //INSERT MOST POPULAR STORE REVIEW WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.site-popular-reviews', $right_id, 6, "Most Popular Reviews", "true");
//
//    //INSERT MOST LIKED STORE REVIEW WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.homelike-sitestorereviews', $right_id, 7, "Most Liked Reviews", "true");
//
//    //INSERT TOP REVIEWER STORE REVIEW WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.reviewer-sitestorereviews', $right_id, 8, "Top Reviewers", "true");
//
//    //INSERT TITEM OF THE DAY STORE REVIEW WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.review-of-the-day', $right_id, 9, "Review of the Day", "true");
//
//    //INSERT RECENT STORE REVIEW WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.recent-sitestorereviews', $right_id, 10, "Recent Reviews", "true");
//
//    if ( $infomation && $rowinfo ) {
//      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-ads', $right_id, 11, "", "true");
//    }
//  }

  $select = new Zend_Db_Select($db);

  // Check if it's already been placed
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitestorereview_index_view')
          ->limit(1);
  ;
  $info = $select->query()->fetch();

  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitestorereview_index_view',
        'displayname' => 'Stores - Store Review View Page',
        'title' => 'View Store Review',
        'description' => 'This is the view page for a store review.',
        'custom' => 0,
        'provides' => 'subject=sitestorereview',
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
        'name' => 'sitestorereview.review-content',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
    ));

    $db->insert('engine4_core_content', array(
        'page_id' => $store_id,
        'type' => 'widget',
        'name' => 'sitestorereview.sitestore-review-detail',
        'parent_content_id' => $right_id,
        'order' => 1,
        'params' => '{"title":"Review Details"}',
    ));

    if ( $infomation && $rowinfo ) {
      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-ads', $right_id, 2, "", "true");
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
		->where('name = ?', 'sitestorereview_mobi_view')
		->limit(1);
		;
		$info = $select->query()->fetch();
		if (empty($info)) {
			$db->insert('engine4_core_pages', array(
						'name' => 'sitestorereview_mobi_view',
						'displayname' => 'Mobile Store Review Profile',
						'title' => 'Mobile Store Review Profile',
						'description' => 'This is the mobile verison of a Store review profile page.',
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
					'name' => 'sitestorereview.review-content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $store_id,
					'type' => 'widget',
					'name' => 'sitestorereview.sitestore-review-detail',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => '{"title":"Review Details"}',
			));
		}
	}

//  $select = new Zend_Db_Select($db);
//	$select
//					->from('engine4_core_pages')
//					->where('name = ?', 'sitestorereview_index_home')
//					->limit(1);
//	$info = $select->query()->fetch();
//	if (empty($info)) {
//		$db->insert('engine4_core_pages', array(
//				'name' => 'sitestorereview_index_home',
//				'displayname' => 'Store Reviews Home',
//				'title' => 'Store Reviews Home',
//				'description' => 'This is store review home store.',
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
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorereview.recent-sitestorereviews',
//				'parent_content_id' => $left_id,
//				'order' => 16,
//				'params' => '{"title":"Recent Reviews","titleCount":"true"}',
//		));
//
//		// Middle
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorereview.featured-reviews-slideshow',
//				'parent_content_id' => $middle_id,
//				'order' => 15,
//				'params' => '{"title":"Featured Reviews","titleCount":"true"}',
//		));
//
//    $db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestore.category-stores-sitestore',
//				'parent_content_id' => $middle_id,
//				'order' => 16,
//				'params' => '{"title":"Most Reviewed this Month","titleCount":true,"itemCount":"6","storeCount":"3","popularity":"review_count","interval":"month","nomobile":"1"}',
//		));
//
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorereview.review-tabs',
//				'parent_content_id' => $middle_id,
//				'order' => 17,
//				'params' => '{"title":"People\'s Reviews"}',
//		));
//		// Right Side
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorereview.sitestorereviewlist-link',
//				'parent_content_id' => $right_id,
//				'order' => 19,
//				'params' => '',
//		));
//
//		// Right Side
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorereview.search-sitestorereview',
//				'parent_content_id' => $right_id,
//				'order' => 18,
//				'params' => '',
//		));
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorereview.review-of-the-day',
//				'parent_content_id' => $left_id,
//				'order' => 13,
//				'params' => '{"title":"Review of the Day"}',
//		));
//
//    $db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorereview.topratedstores-sitestorereviews',
//				'parent_content_id' => $left_id,
//				'order' => 14,
//				'params' => '{"title":"Top Rated Stores","itemCountPerStore":3}',
//		));
//
//    $db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorereview.reviewer-sitestorereviews',
//				'parent_content_id' => $left_id,
//				'order' => 15,
//				'params' => '{"title":"Top Reviewers","itemCountPerStore":3}',
//		));
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorereview.homecomment-sitestorereviews',
//				'parent_content_id' => $right_id,
//				'order' => 22,
//				'params' => '{"title":"Most Commented Reviews","titleCount":"true"}',
//		));
//
//		$db->insert('engine4_core_content', array(
//				'page_id' => $store_id,
//				'type' => 'widget',
//				'name' => 'sitestorereview.homelike-sitestorereviews',
//				'parent_content_id' => $right_id,
//				'order' => 21,
//				'params' => '{"title":"Most Liked Reviews","titleCount":"true"}',
//		));
//	}
}

?>