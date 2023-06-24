<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sespymk
 * @package    Sespymk
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: defaultsettings.php 2017-03-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

$db = Zend_Db_Table_Abstract::getDefaultAdapter();

//People You May Know Page
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sespymk_index_requests')
  ->limit(1)
  ->query()
  ->fetchColumn();
if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sespymk_index_requests',
    'displayname' => 'People You May Know Page',
    'title' => 'People You May Know',
    'description' => 'This page lists all non friends entries.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  
  // Insert top
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'top',
    'page_id' => $page_id,
    'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  
  // Insert main
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'main',
    'page_id' => $page_id,
    'order' => 2,
  ));
  $main_id = $db->lastInsertId();
/*  
  // Insert top-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $page_id,
    'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();*/
  
  // Insert main-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $page_id,
    'parent_content_id' => $main_id,
    'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  
  // Insert main-right
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'right',
    'page_id' => $page_id,
    'parent_content_id' => $main_id,
    'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();
  
  // Insert main-right
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'left',
    'page_id' => $page_id,
    'parent_content_id' => $main_id,
    'order' => 1,
  ));
  $main_left_id = $db->lastInsertId();
  
  
  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sespymk.invite',
    'page_id' => $page_id,
    'parent_content_id' => $main_left_id,
    'order' => $widgetOrder++,
    'params' => '{"title":"Invite Friends","name":"sespymk.invite"}',
  ));
  
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sesmember.featured-sponsored',
    'page_id' => $page_id,
    'parent_content_id' => $main_left_id,
    'order' => $widgetOrder++,
    'params' => '{"viewType":"list","imageType":"rounded","order":"","criteria":"5","info":"most_liked","showLimitData":"1","show_star":"0","show_criteria":["title","view"],"grid_title_truncation":"45","list_title_truncation":"45","height":"180","width":"180","photo_height":"160","photo_width":"250","limit_data":"2","title":"Verified Members","nomobile":"0","name":"sesmember.featured-sponsored"}',
  ));
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sesmember.popular-compliment-members',
    'page_id' => $page_id,
    'parent_content_id' => $main_left_id,
    'order' => $widgetOrder++,
    'params' => '{"viewType":"list","imageType":"square","order":"","criteria":"5","compliment":"4","showLimitData":"0","show_star":"0","show_criteria":["title"],"grid_title_truncation":"45","list_title_truncation":"45","height":"180","width":"180","photo_height":"160","photo_width":"250","limit_data":"2","title":"Funniest Members","nomobile":"0","name":"sesmember.popular-compliment-members"}',
  ));
  
  
  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sespymk.friend-requests',
    'page_id' => $page_id,
    'parent_content_id' => $main_middle_id,
    'order' => $widgetOrder++,
    'params' => '{"itemCount":"2","paginationType":"2","linktopage":"1","title":"Friend Requests","nomobile":"0","name":"sespymk.friend-requests"}',
  ));
  
  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sespymk.suggestion-page',
    'page_id' => $page_id,
    'parent_content_id' => $main_middle_id,
    'order' => $widgetOrder++,
    'params' => '{"showType":"0","onlyphotousers":"1","showdetails":["friends","mutualfriends"],"horiwidth":"186","paginationType":"2","itemCount":"10","title":"People You May Know","nomobile":"0","name":"sespymk.suggestion-page"}',
  ));
  
  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sesmember.browse-search',
    'page_id' => $page_id,
    'parent_content_id' => $main_right_id,
    'order' => $widgetOrder++,
    'params' => '{"view_type":"vertical","search_type":["recentlySPcreated","mostSPviewed","mostSPliked","mylike","myfollow","mostSPreviewed","mostcontributors","mostSPcommented","atoz","ztoa","mostSPrated","featured","sponsored","verified"],"view":["0","1","3","week","month"],"default_search_type":"creation_date ASC","show_advanced_search":"0","network":"no","compliment":"yes","alphabet":"no","friend_show":"yes","search_title":"yes","browse_by":"yes","location":"yes","kilometer_miles":"yes","country":"no","state":"no","city":"no","zip":"no","member_type":"no","has_photo":"yes","is_online":"yes","is_vip":"yes","title":"Friend Search","nomobile":"0","name":"sesmember.browse-search"}',
  ));
  
    // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sesmember.top-reviewers',
    'page_id' => $page_id,
    'parent_content_id' => $main_right_id,
    'order' => $widgetOrder++,
    'params' => '{"viewType":"list","imageType":"square","show_criteria":["vipLabel","rating"],"grid_title_truncation":"45","list_title_truncation":"45","showLimitData":"1","height":"180","width":"180","photo_height":"160","photo_width":"250","limit_data":"3","title":"Top Reviewers","nomobile":"0","name":"sesmember.top-reviewers"}',
  ));
}

//Sent Friend Requests Page
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sespymk_index_friendrequestssent')
  ->limit(1)
  ->query()
  ->fetchColumn();
if( !$page_id ) {
  $widgetOrder = 1;
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sespymk_index_friendrequestssent',
    'displayname' => 'Sent Friend Requests Page',
    'title' => 'Sent Friend Requests',
    'description' => 'This page lists sent friend requests entries.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  
  // Insert top
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'top',
    'page_id' => $page_id,
    'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  
  // Insert main
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'main',
    'page_id' => $page_id,
    'order' => 2,
  ));
  $main_id = $db->lastInsertId();
/*  
  // Insert top-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $page_id,
    'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();*/
  
  // Insert main-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $page_id,
    'parent_content_id' => $main_id,
    'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  
  // Insert main-right
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'right',
    'page_id' => $page_id,
    'parent_content_id' => $main_id,
    'order' => 1,
  ));
  $main_right_id = $db->lastInsertId();
  
  // Insert main-right
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'left',
    'page_id' => $page_id,
    'parent_content_id' => $main_id,
    'order' => 1,
  ));
  $main_left_id = $db->lastInsertId();
  
  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sespymk.invite',
    'page_id' => $page_id,
    'parent_content_id' => $main_left_id,
    'order' => $widgetOrder++,
    'params' => '{"title":"Invite Friends","name":"sespymk.invite"}',
  ));
  
  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sesmember.featured-sponsored',
    'page_id' => $page_id,
    'parent_content_id' => $main_left_id,
    'order' => $widgetOrder++,
    'params' => '{"viewType":"list","imageType":"square","order":"","criteria":"5","info":"most_liked","showLimitData":"1","show_star":"0","show_criteria":["title","view"],"grid_title_truncation":"45","list_title_truncation":"45","height":"180","width":"180","photo_height":"160","photo_width":"250","limit_data":"2","title":"Verified Members","nomobile":"0","name":"sesmember.featured-sponsored"}',
  ));
  
  
  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sesmember.popular-compliment-members',
    'page_id' => $page_id,
    'parent_content_id' => $main_left_id,
    'order' => $widgetOrder++,
    'params' => '{"viewType":"list","imageType":"square","order":"","criteria":"5","compliment":"1","showLimitData":"1","show_star":"0","show_criteria":["title"],"grid_title_truncation":"45","list_title_truncation":"45","height":"180","width":"180","photo_height":"160","photo_width":"250","limit_data":"2","title":"Beautiful Members","nomobile":"0","name":"sesmember.popular-compliment-members"}',
  ));

  
  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sespymk.friendrequestsent-page',
    'page_id' => $page_id,
    'parent_content_id' => $main_middle_id,
    'order' => $widgetOrder++,
    'params' => '{"itemCount":"3","paginationType":"2","linktopage":"1","title":"Friend Requests Sent","nomobile":"0","name":"sespymk.friendrequestsent-page"}',
  ));
  
  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sespymk.suggestion-page',
    'page_id' => $page_id,
    'parent_content_id' => $main_middle_id,
    'order' => $widgetOrder++,
    'params' => '{"showType":"0","onlyphotousers":"1","showdetails":["friends","mutualfriends"],"height":"230","horiwidth":"150","horiheight":"150","paginationType":"2","itemCount":"10","title":"People You May Know","nomobile":"0","name":"sespymk.suggestion-page"}',
  ));
  
    // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sesmember.browse-search',
    'page_id' => $page_id,
    'parent_content_id' => $main_right_id,
    'order' => $widgetOrder++,
    'params' => '{"view_type":"vertical","search_type":["recentlySPcreated","mostSPviewed","mostSPliked","mylike","myfollow","mostSPreviewed","mostcontributors","mostSPcommented","atoz","ztoa","mostSPrated","featured","sponsored","verified"],"view":["0","1","3","week","month"],"default_search_type":"creation_date ASC","show_advanced_search":"0","network":"no","compliment":"yes","alphabet":"no","friend_show":"yes","search_title":"yes","browse_by":"yes","location":"yes","kilometer_miles":"yes","country":"no","state":"no","city":"no","zip":"no","member_type":"no","has_photo":"yes","is_online":"yes","is_vip":"yes","title":"Friend Search","nomobile":"0","name":"sesmember.browse-search"}',
  ));
  
      // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sesmember.top-reviewers',
    'page_id' => $page_id,
    'parent_content_id' => $main_right_id,
    'order' => $widgetOrder++,
    'params' => '{"viewType":"list","imageType":"square","show_criteria":["vipLabel","rating"],"grid_title_truncation":"45","list_title_truncation":"45","showLimitData":"1","height":"180","width":"180","photo_height":"160","photo_width":"250","limit_data":"3","title":"Top Reviewers","nomobile":"0","name":"sesmember.top-reviewers"}',
  ));
}
$db->query('UPDATE `engine4_core_menuitems` SET `params` = \'{"route":"sespymk_general","module":"sespymk"}\' WHERE `engine4_core_menuitems`.`name` = "sespymk_home_findfriends";');