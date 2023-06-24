<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: defaultsettings.php  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

$db = Zend_Db_Table_Abstract::getDefaultAdapter();
$table_exist_link = $db->query('SHOW TABLES LIKE \'engine4_core_links\'')->fetch();
if (!empty($table_exist_link)) {
  $gif = $db->query('SHOW COLUMNS FROM engine4_core_links LIKE \'ses_aaf_gif\'')->fetch();
  if (empty($gif)) {
    $db->query('ALTER TABLE `engine4_core_links` ADD `ses_aaf_gif` TINYINT(1) NOT NULL DEFAULT "0";');
  }
}

$table_exist_action = $db->query('SHOW TABLES LIKE \'engine4_activity_actions\'')->fetch();
if (!empty($table_exist_action)) {
  $privacy = $db->query('SHOW COLUMNS FROM engine4_activity_actions LIKE \'privacy\'')->fetch();
  if (empty($privacy)) {
    $db->query('ALTER TABLE `engine4_activity_actions` ADD `privacy` TEXT NULL AFTER `body`;');
  }
  $privacy = $db->query('SHOW COLUMNS FROM engine4_activity_actions LIKE \'commentable\'')->fetch();
  if (empty($privacy)) {
    $db->query('ALTER TABLE `engine4_activity_actions` ADD `commentable` TINYINT(1) NOT NULL DEFAULT "1";');
  }
  $reaction = $db->query('SHOW COLUMNS FROM engine4_activity_actions LIKE \'reaction_id\'')->fetch();
  if (empty($reaction)) {
    $db->query('ALTER TABLE `engine4_activity_actions` ADD `reaction_id` INT(11) NOT NULL DEFAULT \'0\'');
  }
  $schedule_time = $db->query('SHOW COLUMNS FROM engine4_activity_actions LIKE \'schedule_time\'')->fetch();
  if (empty($schedule_time)) {
    $db->query('ALTER TABLE `engine4_activity_actions` ADD `schedule_time` varchar(256) NOT NULL;');
  }
}
		
//Memories On This Day Page
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesadvancedactivity_index_onthisday')
  ->limit(1)
  ->query()
  ->fetchColumn();

// insert if it doesn't exist yet
if( !$page_id ) {
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesadvancedactivity_index_onthisday',
    'displayname' => 'SES - Advanced News & Activity Feeds - Memories On This Day Page',
    'title' => 'Memories On This Day',
    'description' => 'This page show memories and feeds on this day.',
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

  // Insert top-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $page_id,
    'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $page_id,
    'parent_content_id' => $main_id,
    'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert main-left
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
    'name' => 'sesadvancedactivity.feed',
    'page_id' => $page_id,
    'parent_content_id' => $main_middle_id,
    'order' => 1,
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sesadvancedactivity.onthisday-banner',
    'page_id' => $page_id,
    'parent_content_id' => $main_middle_id,
    'order' => 1,
  ));
  // insert left content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'user.home-links',
    'page_id' => $page_id,
    'parent_content_id' => $main_left_id,
    'order' => 1,
  ));
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'core.statistics',
    'page_id' => $page_id,
    'parent_content_id' => $main_left_id,
    'order' => 2,
  ));
}

//Welcome Tab Page
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesadvancedactivity_index_welcome')
  ->limit(1)
  ->query()
  ->fetchColumn();
$widgetOrder = 1;
// insert if it doesn't exist yet
if( !$page_id ) {
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesadvancedactivity_index_welcome',
    'displayname' => 'SES - Advanced News & Activity Feeds - Welcome Tab Page',
    'title' => 'Welcome Tab Page',
    'description' => 'This page shows welcome tab in activity feeds.',
    'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  // Insert main
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'main',
    'page_id' => $page_id,
    'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  
  // Insert main-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $page_id,
    'parent_content_id' => $main_id,
    'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();
  
  $db->insert('engine4_core_content', array(
    'page_id' => $page_id,
    'type' => 'widget',
    'name' => 'sesadvancedactivity.welcometab-sections',
    'parent_content_id' => $main_middle_id,
    'order' => $widgetOrder++,
    'params' => '{"title":"","displaysections":"4","nomobile":"0","name":"sesadvancedactivity.welcometab-sections"}',
  ));
  
  $db->insert('engine4_core_content', array(
    'page_id' => $page_id,
    'type' => 'widget',
    'name' => 'sesbasic.simple-html-block',
    'parent_content_id' => $main_middle_id,
    'order' => $widgetOrder++,
    'params' => '{"bodysimple":"<div class=\"sesact_welcome_txt_block sesbasic_clearfix\">\r\n  <p class=\"sesact_welcome_txt_block_des\">Welcome to the new feeds. You can now share your files, sell products, upload multiple photos, videos & much more. Schedule post to be shared at a later date and choose targeted audience for your posts. So, start sharing!!<\/p>\r\n\t<p class=\"sesact_welcome_txt_block_img\">\r\n\t\t<img src=\"application\/modules\/Sesadvancedactivity\/externals\/images\/welcome.png\" alt=\"\" \/>\r\n\t<\/p>\r\n<\/div>","show_content":"1","title":"","nomobile":"0","name":"sesbasic.simple-html-block"}',
  ));
  
  $db->insert('engine4_core_content', array(
    'page_id' => $page_id,
    'type' => 'widget',
    'name' => 'sesadvancedactivity.welcometab-sections',
    'parent_content_id' => $main_middle_id,
    'order' => $widgetOrder++,
    'params' => '{"title":"","displaysections":"1","nomobile":"0","name":"sesadvancedactivity.welcometab-sections"}',
  ));
  $db->insert('engine4_core_content', array(
    'page_id' => $page_id,
    'type' => 'widget',
    'name' => 'sesadvancedactivity.welcometab-sections',
    'parent_content_id' => $main_middle_id,
    'order' => $widgetOrder++,
    'params' => '{"title":"","displaysections":"2","nomobile":"0","name":"sesadvancedactivity.welcometab-sections"}',
  ));
  $db->insert('engine4_core_content', array(
    'page_id' => $page_id,
    'type' => 'widget',
    'name' => 'sesadvancedactivity.welcometab-sections',
    'parent_content_id' => $main_middle_id,
    'order' => $widgetOrder++,
    'params' => '{"title":"","displaysections":"3","nomobile":"0","name":"sesadvancedactivity.welcometab-sections"}',
  ));
}


//Hashtag Feeds Page
$page_id = $db->select()
  ->from('engine4_core_pages', 'page_id')
  ->where('name = ?', 'sesadvancedactivity_index_hashtag')
  ->limit(1)
  ->query()
  ->fetchColumn();
$widgetOrder = 1;
// insert if it doesn't exist yet
if( !$page_id ) {
  // Insert page
  $db->insert('engine4_core_pages', array(
    'name' => 'sesadvancedactivity_index_hashtag',
    'displayname' => 'SES - Advanced News & Activity Feeds - Hashtag Feeds Page',
    'title' => 'Hashtags',
    'description' => 'This page show hashtag feeds.',
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

  // Insert top-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $page_id,
    'parent_content_id' => $top_id,
  ));
  $top_middle_id = $db->lastInsertId();

  // Insert main-middle
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'middle',
    'page_id' => $page_id,
    'parent_content_id' => $main_id,
    'order' => 2,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert main-left
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'left',
    'page_id' => $page_id,
    'parent_content_id' => $main_id,
    'order' => 1,
  ));
  $main_left_id = $db->lastInsertId();
  
  // Insert main-right
  $db->insert('engine4_core_content', array(
    'type' => 'container',
    'name' => 'right',
    'page_id' => $page_id,
    'parent_content_id' => $main_id,
    'order' => 3,
  ));
  $main_right_id = $db->lastInsertId();

  // Insert content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sesadvancedactivity.feed',
    'page_id' => $page_id,
    'parent_content_id' => $main_middle_id,
    'order' => $widgetOrder++,
    'params' => '{"title":"What\'s New","design":"3","scrollfeed":"1","autoloadTimes":"3","userphotoalign":"left","enablewidthsetting":"0","sesact_image1":null,"sesact_image1_width":"500","sesact_image1_height":"450","sesact_image2":null,"sesact_image2_width":"289","sesact_image2_height":"200","sesact_image3":null,"sesact_image3_bigwidth":"328","sesact_image3_bigheight":"300","sesact_image3_smallwidth":"250","sesact_image3_smallheight":"150","sesact_image4":null,"sesact_image4_bigwidth":"578","sesact_image4_bigheight":"300","sesact_image4_smallwidth":"192","sesact_image4_smallheight":"100","sesact_image5":null,"sesact_image5_bigwidth":"289","sesact_image5_bigheight":"260","sesact_image5_smallwidth":"289","sesact_image5_smallheight":"130","sesact_image6":null,"sesact_image6_width":"289","sesact_image6_height":"150","sesact_image7":null,"sesact_image7_bigwidth":"192","sesact_image7_bigheight":"150","sesact_image7_smallwidth":"144","sesact_image7_smallheight":"150","sesact_image8":null,"sesact_image8_width":"144","sesact_image8_height":"150","sesact_image9":null,"sesact_image9_width":"192","sesact_image9_height":"150","nomobile":"0","name":"sesadvancedactivity.feed"}',
  ));
  // insert left content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'user.home-links',
    'page_id' => $page_id,
    'parent_content_id' => $main_left_id,
    'order' => $widgetOrder++,
  ));
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'core.statistics',
    'page_id' => $page_id,
    'parent_content_id' => $main_left_id,
    'order' => $widgetOrder++,
    'params' => '{"title":"Statistics"}',
  ));
  // insert right content
  $db->insert('engine4_core_content', array(
    'type' => 'widget',
    'name' => 'sesadvancedactivity.top-trends',
    'page_id' => $page_id,
    'parent_content_id' => $main_right_id,
    'order' => $widgetOrder++,
    'params' => '{"title":"Trending","limit":"10","nomobile":"0","name":"sesadvancedactivity.top-trends"}',
  ));
}

//Update all core feed to our feed
$db->query("UPDATE `engine4_core_content` SET `name` = 'sesadvancedactivity.feed' WHERE `engine4_core_content`.`name` = 'activity.feed';");

//Default Settings
$db->query('INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
("sesadvancedactivity.adsenable", "0"),
("sesadvancedactivity.adsrepeatenable", "0"),
("sesadvancedactivity.adsrepeattimes", "15"),
("sesadvancedactivity.advancednotification", "0"),
("sesadvancedactivity.allowlistprivacy", "1"),
("sesadvancedactivity.allownetworkprivacy", "1"),
("sesadvancedactivity.allownetworkprivacytype", "0"),
("sesadvancedactivity.allowprivacysetting", "1"),
("sesadvancedactivity.attachment.count", "9"),
("sesadvancedactivity.bigtext", "1"),
("sesadvancedactivity.composeroptions.0", "photo"),
("sesadvancedactivity.composeroptions.1", "sesadvancedactivitylink"),
("sesadvancedactivity.composeroptions.10", "sesadvancedactivitylinkedin"),
("sesadvancedactivity.composeroptions.11", "sesadvancedactivitytargetpost"),
("sesadvancedactivity.composeroptions.2", "video"),
("sesadvancedactivity.composeroptions.3", "music"),
("sesadvancedactivity.composeroptions.4", "buysell"),
("sesadvancedactivity.composeroptions.5", "tagUseses"),
("sesadvancedactivity.composeroptions.6", "fileupload"),
("sesadvancedactivity.composeroptions.7", "smilesses"),
("sesadvancedactivity.composeroptions.8", "locationses"),
("sesadvancedactivity.composeroptions.9", "shedulepost"),
("sesadvancedactivity.countfriends", "3"),
("sesadvancedactivity.dobadd", "1"),
("sesadvancedactivity.enableonthisday", "1"),
("sesadvancedactivity.eneblelikecommentshare", "1"),
("sesadvancedactivity.findfriends", "1"),
("sesadvancedactivity.fonttextsize", "24"),
("sesadvancedactivity.friendnotificationbirthday", "1"),
("sesadvancedactivity.friendrequest", "1"),
("sesadvancedactivity.language", "en"),
("sesadvancedactivity.linkedin.enable", "1"),
("sesadvancedactivity.makelandingtab", "0"),
("sesadvancedactivity.networkbasedfiltering", "0"),
("sesadvancedactivity.notificationbirthday", "1"),
("sesadvancedactivity.notificationday", "1"),
("sesadvancedactivity.notificationfriends", "1"),
("sesadvancedactivity.notificationfriendsdays", "30"),
("sesadvancedactivity.numberofdays", "3"),
("sesadvancedactivity.numberoffriends", "3"),
("sesadvancedactivity.profilephotoupload", "0"),
("sesadvancedactivity.reportenable", "1"),
("sesadvancedactivity.showwelcometab", "1"),
("sesadvancedactivity.socialshare", "1"),
("sesadvancedactivity.submitWithAjax", "1"),
("sesadvancedactivity.tabsettings", "Welcome to [site_title], [user_name]"),
("sesadvancedactivity.tabvisibility", "0"),
("sesadvancedactivity.textlimit", "120"),
("sesadvancedactivity.translate", "1"),
("sesadvancedactivity.visiblesearchfilter", "6");');

$db->query('ALTER TABLE `engine4_sesadvancedactivity_filterlists` ADD UNIQUE( `filtertype`);');
$db->query('INSERT IGNORE INTO `engine4_sesadvancedactivity_filterlists` (`filtertype`, `module`, `title`, `active`, `is_delete`, `order`) VALUES
("all", "Core", "All Updates", 1, 0, 1),
("my_networks", "Networks", "My Network", 1, 0, 3),
("my_friends", "Members", "Friends", 1, 0, 2),
("posts", "Core", "Posts", 1, 0, 12),
("saved_feeds", "Core", "Saved Feeds", 1, 0, 13),
("post_self_buysell", "Core", "Sell Something", 1, 0, 9),
("post_self_file", "Core", "Files", 1, 0, 10),
("scheduled_post", "Core", "Scheduled Post", 1, 0, 11),
("event", "Events", "Events", 1, 1, 7),
("album", "Albums", "Photos", 1, 1, 4),
("blog", "Blogs", "Blogs", 1, 1, 8),
("music", "Music", "Music", 1, 1, 6),
("video", "Videos", "Videos", 1, 1, 5),
("poll", "Polls", "Polls", 1, 1, 5),
("group", "Groups", "Groups", 1, 1, 5),
("classified", "Classifieds", "Classifieds", 1, 1, 5),
("sesevent", "SES - Advanced Events Plugin", "Events", 1, 1, 7),
("sesalbum", "SES - Advanced Photos & Albums Plugin", "Photos", 1, 1, 4),
("sesblog", "Advanced Blog Plugin", "Blogs", 1, 1, 8),
("sesmusic", "Advanced Music Albums, Songs & Playlists Plugin", "Music", 1, 1, 6),
("sesvideo", "SES - Advanced Videos & Channels Plugin", "Videos", 1, 1, 5);');

$db->query('ALTER TABLE  `engine4_sesadvancedactivity_eventmessages` CHANGE  `creation_date`  `creation_date` DATETIME NULL DEFAULT NULL ;');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
("sesadvancedactivity_tagged_people", "sesadvancedactivity", \'{item:$subject} tagged you in a {var:$postLink}.\', 0, "", 1),
("sesadvancedactivity_scheduled_live", "sesadvancedactivity", "Your scheduled post has been made live.", 0, "", 1);');

$db->query('UPDATE  `engine4_activity_actiontypes` SET  `module` =  "sesadvancedactivity" WHERE  `engine4_activity_actiontypes`.`type` = "post_self_link";');

$db->query('UPDATE  `engine4_activity_actiontypes` SET  `module` =  "sesadvancedactivity" WHERE  `engine4_activity_actiontypes`.`type` = "post_self_video";');

$db->query('UPDATE  `engine4_activity_actiontypes` SET  `module` =  "sesadvancedactivity" WHERE  `engine4_activity_actiontypes`.`type` = "post_self_photo";');

$db->query('UPDATE  `engine4_activity_actiontypes` SET  `module` =  "sesadvancedactivity" WHERE  `engine4_activity_actiontypes`.`type` = "post_self_music";');

$db->query('UPDATE `engine4_core_menuitems` SET `params` = \'{"route":"sesadvancedactivity_onthisday"}\' WHERE `engine4_core_menuitems`.`name` = "sesadvancedactivity_index_onthisday";');