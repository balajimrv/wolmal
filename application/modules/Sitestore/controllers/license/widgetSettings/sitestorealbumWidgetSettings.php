<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitestorealbumWidgetSettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

//START LANGUAGE WORK
Engine_Api::_()->getApi('language', 'sitestore')->languageChanges();
//END LANGUAGE WORK

//GET DB
$db = Zend_Db_Table_Abstract::getDefaultAdapter();

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enabled`) VALUES
("sitestorealbum_admin_widget_settings", "sitestorealbum", "Widget Settings", "", \'{"route":"admin_default","module":"sitestorealbum","controller":"album", "action": "index"}\', "sitestorealbum_admin_main", "", 0, 3, 1)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestorealbum_admin_main_photo_featured", "sitestorealbum", "Featured Photos", "", \'{"route":"admin_default","module":"sitestorealbum","controller":"settings", "action": "featured"}\', "sitestorealbum_admin_main", "", 1, 0, 4)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestorealbum_admin_submain_album_tab", "sitestorealbum", "Tabbed Albums Widget", "", \'{"route":"admin_default","module":"sitestorealbum","controller":"album", "action": "index"}\', "sitestorealbum_admin_submain", "", 1, 0, 2)');


$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestorealbum_admin_submain_photo_tab", "sitestorealbum", "Tabbed Photos Widget", "", \'{"route":"admin_default","module":"sitestorealbum","controller":"photo", "action": "index"}\', "sitestorealbum_admin_submain", "", 1, 0, 3)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestorealbum_admin_submain_dayitems", "sitestorealbum", "Album of the Day", "", \'{"route":"admin_default","module":"sitestorealbum","controller":"album", "action": "manage-day-items"}\', "sitestorealbum_admin_submain", "", 1, 0, 4)');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestorealbum_admin_submain_photo_items", "sitestorealbum", "Photo of the Day", "", \'{"route":"admin_default","module":"sitestorealbum","controller":"photo", "action": "photo-of-day"}\', "sitestorealbum_admin_submain", "", 1, 0, 5)');

//CHECK THAT SITESTORE PLUGIN IS ACTIVATED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_settings')
        ->where('name = ?', 'sitestore.is.active')
        ->limit(1);
$sitestore_settings = $select->query()->fetchAll();
if (!empty($sitestore_settings)) {
  $sitestore_is_active = $sitestore_settings[0]['value'];
} else {
  $sitestore_is_active = 0;
}

//CHECK THAT SITESTORE PLUGIN IS INSTALLED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitestore')
        ->where('enabled = ?', 1);
$check_sitestore = $select->query()->fetchObject();
if (!empty($check_sitestore) && !empty($sitestore_is_active)) {
  $select = new Zend_Db_Select($db);
  $select_store = $select
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'sitestore_index_view')
          ->limit(1);
  $store = $select_store->query()->fetchAll();
  if (!empty($store)) {
    $store_id = $store[0]['page_id'];

    //INSERTING THE PHOTO WIDGET IN SITESTORE_ADMIN_CONTENT TABLE ALSO.
    Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestore.photos-sitestore', $store_id, 'Photos', 'true', '110');

    //INSERTING THE PHOTO WIDGET IN CORE_CONTENT TABLE ALSO.
    Engine_Api::_()->getApi('layoutcore', 'sitestore')->setContentDefaultInfo('sitestore.photos-sitestore', $store_id, 'Photos', 'true', '110');

    //INSERTING THE PHOTO WIDGET IN SITESTORE_CONTENT TABLE ALSO.
    $select = new Zend_Db_Select($db);
    $select = $select
            ->from('engine4_sitestore_contentstores', 'contentstore_id');
    $contentstore_ids = $select->query()->fetchAll();
    foreach ($contentstore_ids as $contentstore_id) {
      if (!empty($contentstore_id)) {
        $contentstore_id = $contentstore_id['contentstore_id'];
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestore.photos-sitestore', $contentstore_id, 'Photos', 'true', '110');

        //INSERT THE RANDOM ALBUM WIDGET
        $select = new Zend_Db_Select($db);
        $select_content = $select
                ->from('engine4_sitestore_content')
                ->where('contentstore_id = ?', $contentstore_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'sitestore.albums-sitestore')
                ->limit(1);
        $content = $select_content->query()->fetchAll();
        if (empty($content)) {
          $select = new Zend_Db_Select($db);
          $select_container = $select
                  ->from('engine4_sitestore_content', 'content_id')
                  ->where('contentstore_id = ?', $contentstore_id)
                  ->where('type = ?', 'container')
                  ->limit(1);
          $container = $select_container->query()->fetchAll();
          if (!empty($container)) {
            $container_id = $container[0]['content_id'];
            $select = new Zend_Db_Select($db);
            $select_left = $select
                    ->from('engine4_sitestore_content')
                    ->where('parent_content_id = ?', $container_id)
                    ->where('type = ?', 'container')
										->where('contentstore_id = ?', $contentstore_id)
										->where('name in (?)', array('left', 'right'))
                    ->limit(1);
            $left = $select_left->query()->fetchAll();
            if (!empty($left)) {
              $left_id = $left[0]['content_id'];
              $db->insert('engine4_sitestore_content', array(
                  'contentstore_id' => $contentstore_id,
                  'type' => 'widget',
                  'name' => 'sitestore.albums-sitestore',
                  'parent_content_id' => $left_id,
                  'order' => 25,
                  'params' => '{"title":"Albums","titleCount":""}',
              ));
            }
          }
        }

        //INSERT THE PHOTO STRIP WIDGET IN SITESTORE CONTENT TABLE FOR USER
//         $select = new Zend_Db_Select($db);
//         $select_content = $select
//                 ->from('engine4_sitestore_content')
//                 ->where('contentstore_id = ?', $contentstore_id)
//                 ->where('type = ?', 'widget')
//                 ->where('name = ?', 'sitestore.photorecent-sitestore')
//                 ->limit(1);
//         $content = $select_content->query()->fetchAll();
//         if (empty($content)) {
//           $select = new Zend_Db_Select($db);
//           $select_container = $select
//                   ->from('engine4_sitestore_content', 'content_id')
//                   ->where('contentstore_id = ?', $contentstore_id)
//                   ->where('type = ?', 'container')
//                   ->limit(1);
//           $container = $select_container->query()->fetchAll();
//           if (!empty($container)) {
//             $container_id = $container[0]['content_id'];
//             $select = new Zend_Db_Select($db);
//             $select_middle = $select
//                     ->from('engine4_sitestore_content')
//                     ->where('parent_content_id = ?', $container_id)
//                     ->where('type = ?', 'container')
//                     ->where('name = ?', 'middle')
//                     ->limit(1);
//             $middle = $select_middle->query()->fetchAll();
//             if (!empty($middle)) {
//               $middle_id = $middle[0]['content_id'];
//               $db->insert('engine4_sitestore_content', array(
//                   'contentstore_id' => $contentstore_id,
//                   'type' => 'widget',
//                   'name' => 'sitestore.photorecent-sitestore',
//                   'parent_content_id' => $middle_id,
//                   'order' => 5,
//                   'params' => '{"title":"","titleCount":""}',
//               ));
//             }
//           }
//         }
      }
    }

    //INSERT THE RANDOM ALBUM WIDGET IN ADMIN CONTENT TABLE
    $select = new Zend_Db_Select($db);
    $select_content = $select
            ->from('engine4_sitestore_admincontent')
            ->where('store_id = ?', $store_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitestore.albums-sitestore')
            ->limit(1);
    $content = $select_content->query()->fetchAll();
    if (empty($content)) {
      $select = new Zend_Db_Select($db);
      $select_container = $select
              ->from('engine4_sitestore_admincontent', 'admincontent_id')
              ->where('store_id = ?', $store_id)
              ->where('type = ?', 'container')
              ->limit(1);
      $container = $select_container->query()->fetchAll();
      if (!empty($container)) {
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
        if (!empty($left)) {
          $left_id = $left[0]['admincontent_id'];
          $db->insert('engine4_sitestore_admincontent', array(
              'store_id' => $store_id,
              'type' => 'widget',
              'name' => 'sitestore.albums-sitestore',
              'parent_content_id' => $left_id,
              'order' => 25,
              'params' => '{"title":"Albums","titleCount":""}',
          ));
        }
      }
    }

    //INSERT THE PHOTO STRIP WIDGET IN ADMIN CONTENT TABLE
//     $select = new Zend_Db_Select($db);
//     $select_content = $select
//             ->from('engine4_sitestore_admincontent')
//             ->where('store_id = ?', $store_id)
//             ->where('type = ?', 'widget')
//             ->where('name = ?', 'sitestore.photorecent-sitestore')
//             ->limit(1);
//     $content = $select_content->query()->fetchAll();
//     if (empty($content)) {
//       $select = new Zend_Db_Select($db);
//       $select_container = $select
//               ->from('engine4_sitestore_admincontent', 'admincontent_id')
//               ->where('store_id = ?', $store_id)
//               ->where('type = ?', 'container')
//               ->limit(1);
//       $container = $select_container->query()->fetchAll();
//       if (!empty($container)) {
//         $container_id = $container[0]['admincontent_id'];
//         $select = new Zend_Db_Select($db);
//         $select_middle = $select
//                 ->from('engine4_sitestore_admincontent')
//                 ->where('parent_content_id = ?', $container_id)
//                 ->where('type = ?', 'container')
//                 ->where('name = ?', 'middle')
//                 ->limit(1);
//         $middle = $select_middle->query()->fetchAll();
//         if (!empty($middle)) {
//           $middle_id = $middle[0]['admincontent_id'];
//           $db->insert('engine4_sitestore_admincontent', array(
//               'store_id' => $store_id,
//               'type' => 'widget',
//               'name' => 'sitestore.photorecent-sitestore',
//               'parent_content_id' => $middle_id,
//               'order' => 5,
//               'params' => '{"title":"","titleCount":""}',
//           ));
//         }
//       }
//     }

    //INSERT THE RANDOM ALBUM WIDGET IN CORE CONTENT TABLE
    $select = new Zend_Db_Select($db);
    $select_content = $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $store_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitestore.albums-sitestore')
            ->limit(1);
    $content = $select_content->query()->fetchAll();
    if (empty($content)) {
      $select = new Zend_Db_Select($db);
      $select_container = $select
              ->from('engine4_core_content', 'content_id')
              ->where('page_id = ?', $store_id)
              ->where('type = ?', 'container')
              ->limit(1);
      $container = $select_container->query()->fetchAll();
      if (!empty($container)) {
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
        if (!empty($left)) {
          $left_id = $left[0]['content_id'];
          $db->insert('engine4_core_content', array(
              'page_id' => $store_id,
              'type' => 'widget',
              'name' => 'sitestore.albums-sitestore',
              'parent_content_id' => $left_id,
              'order' => 25,
              'params' => '{"title":"Albums","titleCount":""}',
          ));
        }
      }
    }

    //INSERT THE PHOTO STRIP WIDGET IN CORE CONTENT TABLE
//     $select = new Zend_Db_Select($db);
//     $select_content = $select
//             ->from('engine4_core_content')
//             ->where('page_id = ?', $store_id)
//             ->where('type = ?', 'widget')
//             ->where('name = ?', 'sitestore.photorecent-sitestore')
//             ->limit(1);
//     $content = $select_content->query()->fetchAll();
//     if (empty($content)) {
//       $select = new Zend_Db_Select($db);
//       $select_container = $select
//               ->from('engine4_core_content', 'content_id')
//               ->where('page_id = ?', $store_id)
//               ->where('type = ?', 'container')
//               ->limit(1);
//       $container = $select_container->query()->fetchAll();
//       if (!empty($container)) {
//         $container_id = $container[0]['content_id'];
//         $select = new Zend_Db_Select($db);
//         $select_middle = $select
//                 ->from('engine4_core_content')
//                 ->where('parent_content_id = ?', $container_id)
//                 ->where('type = ?', 'container')
//                 ->where('name = ?', 'middle')
//                 ->limit(1);
//         $middle = $select_middle->query()->fetchAll();
//         if (!empty($middle)) {
//           $middle_id = $middle[0]['content_id'];
//           $db->insert('engine4_core_content', array(
//               'page_id' => $store_id,
//               'type' => 'widget',
//               'name' => 'sitestore.photorecent-sitestore',
//               'parent_content_id' => $middle_id,
//               'order' => 5,
//               'params' => '{"title":"","titleCount":""}',
//           ));
//         }
//       }
//     }
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
//          ->where('name = ?', 'sitestore_album_browse')
//          ->limit(1);
//  ;
//  $info = $select->query()->fetch();
//  if ( empty($info) ) {
//    $db->insert('engine4_core_pages', array(
//        'name' => 'sitestore_album_browse',
//        'displayname' => 'Browse Store albums',
//        'title' => 'Store Albums List',
//        'description' => 'This is the store albums.',
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
////INSERT ALBUM WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorealbum.sitestore-album', $middle_id, 2);
//
//    //INSERT SEARCH STORE ALBUM WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorealbum.search-sitestorealbum', $right_id, 3, "", "true");
//
//    //INSERT RECENT STORE ALBUM WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.mostrecentphotos-sitestore', $right_id, 4, "Recent Photos", "true");
//
//    //INSERT SPONSORED STORE ALBUM WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorealbum.sitestore-sponsoredalbum', $right_id, 5, "Sponsored Albums", "true");
//
//    //INSERT MOST POUPLAR STORE ALBUM WIDGET
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.popularphotos-sitestore', $right_id, 6, "Most Popular Photos", "true");
//
//    if ( $infomation && $rowinfo ) {
//      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-ads', $right_id, 7, "", "true");
//    }
//  }

  $select = new Zend_Db_Select($db);

  // Check if it's already been placed
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitestore_album_view')
          ->limit(1);
  ;
  $info = $select->query()->fetch();

  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitestore_album_view',
        'displayname' => 'Stores - Store Album View Page',
        'title' => 'View Store Album',
        'description' => 'This is the view page for a store album.',
        'custom' => 0,
        'provides' => 'subject=sitestorealbum',
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
        'name' => 'sitestorealbum.album-content',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
    ));

    if ( $infomation && $rowinfo ) {
      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-ads', $right_id, 1, "", "true");
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
		->where('name = ?', 'sitestorealbum_mobi_view')
		->limit(1);
		;
		$info = $select->query()->fetch();
		if (empty($info)) {
			$db->insert('engine4_core_pages', array(
						'name' => 'sitestorealbum_mobi_view',
						'displayname' => 'Mobile Store Album Profile',
						'title' => 'Mobile Store Album Profile',
						'description' => 'This is the mobile verison of a Store album profile page.',
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
					'name' => 'sitestorealbum.album-content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '',
			));
		}
	}

//  $select = new Zend_Db_Select($db);
//$select
//        ->from('engine4_core_pages')
//        ->where('name = ?', 'sitestore_album_home')
//        ->limit(1);
//$info = $select->query()->fetch();
//if (empty($info)) {
//  $db->insert('engine4_core_pages', array(
//      'name' => 'sitestore_album_home',
//      'displayname' => 'Store Albums Home',
//      'title' => 'Store Albums Home',
//      'description' => 'This is store album home store.',
//      'custom' => 1
//  ));
//  $store_id = $db->lastInsertId('engine4_core_pages');
//
//  // containers
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'container',
//      'name' => 'main',
//      'parent_content_id' => null,
//      'order' => 2,
//      'params' => '',
//  ));
//  $container_id = $db->lastInsertId('engine4_core_content');
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'container',
//      'name' => 'right',
//      'parent_content_id' => $container_id,
//      'order' => 5,
//      'params' => '',
//  ));
//  $right_id = $db->lastInsertId('engine4_core_content');
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'container',
//      'name' => 'left',
//      'parent_content_id' => $container_id,
//      'order' => 4,
//      'params' => '',
//  ));
//  $left_id = $db->lastInsertId('engine4_core_content');
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'container',
//      'name' => 'top',
//      'parent_content_id' => null,
//      'order' => 1,
//      'params' => '',
//  ));
//  $top_id = $db->lastInsertId('engine4_core_content');
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'container',
//      'name' => 'middle',
//      'parent_content_id' => $top_id,
//      'order' => 6,
//      'params' => '',
//  ));
//  $top_middle_id = $db->lastInsertId('engine4_core_content');
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'container',
//      'name' => 'middle',
//      'parent_content_id' => $container_id,
//      'order' => 6,
//      'params' => '',
//  ));
//  $middle_id = $db->lastInsertId('engine4_core_content');
//
//// Top Middle
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestore.browsenevigation-sitestore',
//      'parent_content_id' => $top_middle_id,
//      'order' => 3,
//      'params' => '',
//  ));
//// Left
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.photo-of-the-day',
//      'parent_content_id' => $left_id,
//      'order' => 8,
//      'params' => '{"title":"Photo of the Day"}',
//  ));
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.featured-photos',
//      'parent_content_id' => $left_id,
//      'order' => 9,
//      'params' => '{"title":"Featured Photos","titleCount":"true"}',
//  ));
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestore.popularphotos-sitestore',
//      'parent_content_id' => $left_id,
//      'order' => 10,
//      'params' => '{"title":"Most Popular Photos","titleCount":"true"}',
//  ));
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.homephotolike-sitestore',
//      'parent_content_id' => $left_id,
//      'order' => 11,
//      'params' => '{"title":"Most Liked Photos","titleCount":"true"}',
//  ));
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.homephotocomment-sitestore',
//      'parent_content_id' => $left_id,
//      'order' => 12,
//      'params' => '{"title":"Most Commented Photos","titleCount":"true"}',
//  ));
//
//  // Middle
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.featured-albums-slideshow',
//      'parent_content_id' => $middle_id,
//      'order' => 13,
//      'params' => '{"title":"Featured Albums","titleCount":"true"}',
//  ));
//
//// Middele
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.featured-photos-carousel',
//      'parent_content_id' => $middle_id,
//      'order' => 14,
//      'params' => '{"title":"Featured Photos","vertical":"0", "noOfRow":"2","inOneRow":"3","interval":"250","name":"sitestorealbum.featured-photos-carousel"}',
//  ));
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.list-photos-tabs-view',
//      'parent_content_id' => $middle_id,
//      'order' => 15,
//      'params' => '{"title":"Photos","margin_photo":"12"}',
//  ));
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.list-albums-tabs-view',
//      'parent_content_id' => $middle_id,
//      'order' => 16,
//      'params' => '{"title":"Albums","margin_photo":"12"}',
//  ));
//  // Right Side
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.sitestorealbumlist-link',
//      'parent_content_id' => $right_id,
//      'order' => 18,
//      'params' => '',
//  ));
//
//   // Right Side
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.search-sitestorealbum',
//      'parent_content_id' => $right_id,
//      'order' => 17,
//      'params' => '',
//  ));
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.album-of-the-day',
//      'parent_content_id' => $right_id,
//      'order' => 19,
//      'params' => '{"title":"Album of the Day"}',
//  ));
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.featured-albums',
//      'parent_content_id' => $right_id,
//      'order' => 20,
//      'params' => '{"title":"Featured Albums","itemCountPerStore":4}',
//  ));
//
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.list-popular-albums',
//      'parent_content_id' => $right_id,
//      'order' => 21,
//      'params' => '{"title":"Most Liked Albums","itemCountPerStore":"4","popularType":"like","name":"sitestorealbum.list-popular-albums"}',
//  ));
//  $db->insert('engine4_core_content', array(
//      'page_id' => $store_id,
//      'type' => 'widget',
//      'name' => 'sitestorealbum.list-popular-albums',
//      'parent_content_id' => $right_id,
//      'order' => 22,
//      'params' => '{"title":"Popular Albums","itemCountPerStore":"4","popularType":"view","name":"sitestorealbum.list-popular-albums"}',
//  ));
//}
}


$select = new Zend_Db_Select($db);
$select
			->from('engine4_core_modules')
			->where('name = ?', 'sitemobile')
			->where('enabled = ?', 1);
$is_sitemobile_object = $select->query()->fetchObject();
if($is_sitemobile_object)  {
	include APPLICATION_PATH . "/application/modules/Sitestore/controllers/license/mobileLayoutCreation.php";
	include APPLICATION_PATH . "/application/modules/Sitestore/controllers/license/sitemobile/mobileLayoutCreationAlbum.php";
  include APPLICATION_PATH . "/application/modules/Sitestore/controllers/license/sitemobile/mobileLayoutCreationForm.php";
  include APPLICATION_PATH . "/application/modules/Sitestore/controllers/license/sitemobile/mobileLayoutCreationOffer.php";
  include APPLICATION_PATH . "/application/modules/Sitestore/controllers/license/sitemobile/mobileLayoutCreationReview.php";
  include APPLICATION_PATH . "/application/modules/Sitestore/controllers/license/sitemobile/mobileLayoutCreationVideo.php";
}
?>