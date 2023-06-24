
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('sitemobile', 'Mobile / Tablet Plugin', 'Mobile / Tablet Plugin', '4.8.12', 1, 'extra');

-- --------------------------------------------------------

--
-- Disable the mobi plugin form table `engine4_core_modules`
--

UPDATE `engine4_core_modules` SET `enabled` = '0' WHERE `engine4_core_modules`.`name` = 'mobi' LIMIT 1 ;

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `order`) VALUES
('core_footer_sitemobile', 'sitemobile', 'Mobile', 'Sitemobile_Plugin_Menus::onMenuInitialize_CoreFooterSitemobile', '', 'core_footer', '', 0, 4),
('core_footer_sitemobile_tablet', 'sitemobile', 'Tablet', 'Sitemobile_Plugin_Menus::onMenuInitialize_CoreFooterSitemobileTablet', '', 'core_footer', '', 0, 5);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
( 'core_main_search', 'core', 'Search', 'Sitemobile_Plugin_Menus::sitemobileSearch', '{"route":"default","controller":"search"}', 'core_main', '', 3, 0, 0),
('core_main_profile', 'user', 'Profile', 'Sitemobile_Plugin_Menus::onMenuInitialize_SitemobileMainProfile', '', 'core_main', '', 4, 1, 1),
('core_main_messages', 'messages', 'Inbox', 'Sitemobile_Plugin_Menus::onMenuInitialize_SitemobileMainMessages', '', 'core_main', '',  5, 1, 1),
( 'core_main_update', 'core', 'Notifications', 'Sitemobile_Plugin_Menus::onMenuInitialize_SitemobileMainNotifications', '{"route":"default","module":"activity","controller":"notifications"}', 'core_main', '',  6, 1, 1),
('core_main_auth', 'user', 'Auth', 'Sitemobile_Plugin_Menus::onMenuInitialize_CoreFooterAuth', '', 'core_main', '', 997, 1, 1),
('core_main_signup', 'sitemobile', 'Sign Up', 'Sitemobile_Plugin_Menus::onMenuInitialize_CoreFooterSignup', '', 'core_main', '',998, 1, 1),
('core_main_desktop', 'sitemobile', 'Full Site', 'Sitemobile_Plugin_Menus::onMenuInitialize_CoreFooterDesktop', '', 'core_main', '',999, 1, 1),
('core_footer_desktop', 'sitemobile', 'Full Site', 'Sitemobile_Plugin_Menus::onMenuInitialize_CoreFooterDesktop', '', 'core_footer', '',4, 1, 1),
('core_footer_signup', 'sitemobile', 'Sign Up', 'Sitemobile_Plugin_Menus::onMenuInitialize_CoreFooterSignup', '', 'core_footer', '',1000, 1, 1),
('core_footer_auth', 'user', 'Auth', 'Sitemobile_Plugin_Menus::onMenuInitialize_CoreFooterAuth', '', 'core_footer', '', 1001, 1, 1),
('user_settings_delete', 'user', 'Delete Account', 'User_Plugin_Menus::canDelete', '{"route":"user_extended", "module":"user", "controller":"settings", "action":"delete"}', 'user_settings', '', 6, 1, 1),
('album_main_browse', 'album', 'Albums', 'Album_Plugin_Menus::canViewAlbums', '{"route":"album_general","action":"browse"}', 'album_main', '', 1, 1, 1),
('album_main_manage', 'album', 'My Albums', 'Album_Plugin_Menus::canCreateAlbums', '{"route":"album_general","action":"manage"}', 'album_main', '',3, 1, 1),
('album_main_upload', 'album', 'Add New Photos', 'Album_Plugin_Menus::canCreateAlbums', '{"route":"album_general","action":"upload"}', 'album_main', '', 4, 0, 0),
('album_quick_upload', 'album', 'Add New Photos', 'Album_Plugin_Menus::canCreateAlbums', '{"route":"album_general","action":"upload","class":"buttonlink"}', 'album_quick', '', 1, 1, 1),
('blog_main_browse', 'blog', 'Browse Entries', 'Blog_Plugin_Menus::canViewBlogs', '{"route":"blog_general"}', 'blog_main', '', 1, 1, 1),
('blog_main_manage', 'blog', 'My Entries', 'Blog_Plugin_Menus::canCreateBlogs', '{"route":"blog_general","action":"manage"}', 'blog_main', '', 2, 1, 1),
('blog_main_create', 'blog', 'Write New Entry', 'Blog_Plugin_Menus::canCreateBlogs', '{"route":"blog_general","action":"create"}', 'blog_main', '', 3, 0, 0),
('blog_quick_create', 'blog', 'Write New Entry', 'Blog_Plugin_Menus::canCreateBlogs', '{"route":"blog_general","action":"create","class":"buttonlink"}', 'blog_quick', '', 1, 1, 1),
('event_quick_create', 'event', 'Create New Event', 'Event_Plugin_Menus::canCreateEvents', '{"route":"event_general","action":"create","class":"buttonlink"}', 'event_quick', '', 1, 1, 1),
('group_quick_create', 'group', 'Create New Group', 'Group_Plugin_Menus::canCreateGroups', '{"route":"group_general","action":"create","class":"buttonlink"}', 'group_quick', '', 1, 1, 1),
('video_quick_create', 'video', 'Post New Video', 'Video_Plugin_Menus::canCreateVideos', '{"route":"video_general","action":"create","class":"buttonlink"}', 'video_quick', '', 1, 1, 1),
('event_main_birthday', 'birthday', 'Birthdays', 'Sitemobile_Plugin_BirthdayMenus::onMenuInitialize_HasAdd', '{"route":"birthday_extended","module":"birthday","controller":"index", "action":"view"}', 'event_main', '', 10, 1, 1),
('user_home_birthday', 'birthday', 'Birthdays', 'Birthday_Plugin_Menus', '', 'user_home', '', 12,1,1);






INSERT IGNORE INTO `engine4_sitemobile_menus` (`name` ,`type` ,`title` ,`order`)
VALUES ('event_quick', 'standard', 'Event Quick Navigation Menu', '999'),('group_quick', 'standard', 'Group Quick Navigation Menu', '999'),
('video_quick', 'standard', 'Video Quick Navigation Menu', '999');


CREATE TABLE IF NOT EXISTS `engine4_sitemobile_searchform` (
`name` VARCHAR( 128 ) NOT NULL ,
`class` VARCHAR( 128 ) NOT NULL ,
`search_filed_name` VARCHAR( 128 ) NOT NULL ,
`params` TEXT NOT NULL ,
`script_render_file` VARCHAR( 255 ) NOT NULL ,
`action` varchar(255) NOT NULL,
UNIQUE (
`name`
)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('group_index_browse', 'Sitemobile_modules_Group_Form_Filter_Browse', 'search_text', '', '', ''),
('event_index_manage', 'Sitemobile_modules_Event_Form_Filter_Manage', 'text', '', '', ''),
('event_index_browse', 'Sitemobile_modules_Event_Form_Filter_Browse', 'search_text', '', '', ''),
('group_index_manage', 'Sitemobile_modules_Group_Form_Filter_Manage', 'text', '', '', ''),
('album_index_browse', 'Sitemobile_modules_Album_Form_Filter_Search', 'search', '', '', ''),
('album_index_manage', 'Sitemobile_modules_Album_Form_Filter_Search', 'search', '', '', ''),
('video_index_manage', 'Sitemobile_modules_Video_Form_Filter_Search', 'text', '', '', ''),
('video_index_browse', 'Sitemobile_modules_Video_Form_Filter_Search', 'text', '', '', ''),
('blog_index_manage', 'Sitemobile_modules_Blog_Form_Filter_Search', 'search', '', '', ''),
('blog_index_index', 'Sitemobile_modules_Blog_Form_Filter_Search', 'search', '', '', ''),
('user_index_browse', 'Sitemobile_modules_User_Form_Filter_Search', 'displayname', '{"type":"user"}', '', '');



CREATE TABLE IF NOT EXISTS `engine4_sitemobile_navigation` (
  `name` varchar(255) NOT NULL,
  `menu` varchar(255) NOT NULL,
  `subject_type` varchar(255) NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

INSERT IGNORE INTO `engine4_sitemobile_navigation` (`name`, `menu`, `subject_type`) VALUES ('blog', 'blog_quick', ''),('blog_index_view', 'blog_gutter', 'blog'),('blog_index_list', 'blog_gutter', 'blog');


INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
('event_topic', 'standard', 'Event Topic Options Menu', '999'),
('event_photo_view', 'standard', 'Event Photo View Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES 
('event', 'event_quick', ''), 
('event_profile_index', 'event_profile', 'event'),
('event_photo_list', 'event_photo', 'event'),
('event_topic_view', 'event_topic', 'event_topic'),
('event_photo_view', 'event_photo_view', 'event_photo');


INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('event_topic_watch', 'event', 'Watch Topic', 'Sitemobile_Plugin_eventMenus', '', 'event_topic', NULL, '0', '1', '1', '1'), 
('event_topic_sticky', 'event', 'Make Sticky', 'Sitemobile_Plugin_eventMenus', '', 'event_topic', NULL, '0', '2', '1', '1'),
('event_topic_open', 'event', 'Open', 'Sitemobile_Plugin_eventMenus', '', 'event_topic', NULL, '0', '3', '1', '1'),
('event_topic_rename', 'event', 'Rename', 'Sitemobile_Plugin_eventMenus', '', 'event_topic', NULL, '0', '4', '1', '1'),
('event_topic_delete', 'event', 'Delete', 'Sitemobile_Plugin_eventMenus', '', 'event_topic', NULL, '0', '5', '1', '1'),
('event_photo_edit', 'event', 'Edit', 'Sitemobile_Plugin_eventMenus', '', 'event_photo_view', NULL, '0', '1', '1', '1'),
('event_photo_delete', 'event', 'Delete', 'Sitemobile_Plugin_eventMenus', '', 'event_photo_view', NULL, '0', '2', '1', '1'),
('event_photo_share', 'event', 'Share', 'Sitemobile_Plugin_eventMenus', '', 'event_photo_view', NULL, '0', '3', '1', '1'),
('event_photo_report', 'event', 'Report', 'Sitemobile_Plugin_eventMenus', '', 'event_photo_view', NULL, '0', '4', '1', '1'),
('event_photo_makeprofilephoto', 'event', 'Make Profile Photo', 'Sitemobile_Plugin_eventMenus', '', 'event_photo_view', NULL, '0', '5', '1', '1');


INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
('group_topic', 'standard', 'Group Topic Options Menu', '999'),
('group_photo_view', 'standard', 'Group Photo View Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES 
('group', 'group_quick', ''), 
('group_profile_index', 'group_profile', 'group'),
('group_photo_list', 'group_photo', 'group'),
('group_topic_view', 'group_topic', 'group_topic'),
('group_photo_view', 'group_photo_view', 'group_photo');



INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('group_topic_watch', 'group', 'Watch Topic', 'Sitemobile_Plugin_groupMenus', '', 'group_topic', NULL, '0', '1', '1', '1'), 
('group_topic_sticky', 'group', 'Make Sticky', 'Sitemobile_Plugin_groupMenus', '', 'group_topic', NULL, '0', '2', '1', '1'),
('group_topic_open', 'group', 'Open', 'Sitemobile_Plugin_groupMenus', '', 'group_topic', NULL, '0', '3', '1', '1'),
('group_topic_rename', 'group', 'Rename', 'Sitemobile_Plugin_groupMenus', '', 'group_topic', NULL, '0', '4', '1', '1'),
('group_topic_delete', 'group', 'Delete', 'Sitemobile_Plugin_groupMenus', '', 'group_topic', NULL, '0', '5', '1', '1'),
('group_photo_edit', 'group', 'Edit', 'Sitemobile_Plugin_groupMenus', '', 'group_photo_view', NULL, '0', '1', '1', '1'),
('group_photo_delete', 'group', 'Delete', 'Sitemobile_Plugin_groupMenus', '', 'group_photo_view', NULL, '0', '2', '1', '1'),
('group_photo_share', 'group', 'Share', 'Sitemobile_Plugin_groupMenus', '', 'group_photo_view', NULL, '0', '3', '1', '1'),
('group_photo_report', 'group', 'Report', 'Sitemobile_Plugin_groupMenus', '', 'group_photo_view', NULL, '0', '4', '1', '1'),
('group_photo_makeprofilephoto', 'group', 'Make Profile Photo', 'Sitemobile_Plugin_groupMenus', '', 'group_photo_view', NULL, '0', '5', '1', '1');


INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES 
('album', 'album_quick', ''), 
('album_album_view', 'album_profile', 'album'),
('album_photo_view', 'album_photo_view', 'album_photo');


INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('album_photo_edit', 'album', 'Edit', 'Sitemobile_Plugin_albumMenus', '', 'album_photo_view', NULL, '0', '1', '1', '1'),
('album_photo_delete', 'album', 'Delete', 'Sitemobile_Plugin_albumMenus', '', 'album_photo_view', NULL, '0', '2', '1', '1'),
('album_photo_share', 'album', 'Share', 'Sitemobile_Plugin_albumMenus', '', 'album_photo_view', NULL, '0', '3', '1', '1'),
('album_photo_report', 'album', 'Report', 'Sitemobile_Plugin_albumMenus', '', 'album_photo_view', NULL, '0', '4', '1', '1'),
('album_photo_makeprofilephoto', 'album', 'Make Profile Photo', 'Sitemobile_Plugin_albumMenus', '', 'album_photo_view', NULL, '0', '5', '1', '1');


INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES
('album_photo_view', 'standard', 'Album Photo View Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('video', 'video_quick', ''), 
('video_index_view', 'video_profile', 'video');


INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('user', 'user_quick', ''), 
('user_mobile-profile_index', 'user_profile', 'user'),
('user_index_home', 'user_home', 'user');


INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('messages', 'message_quick', '');


-- --------------------------------------------------------
--
-- Music Plugin
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('core_main_music', 'music', 'Music',  'Music_Plugin_Menus::canViewPlaylists', '{"route":"music_general", "action":"browse"}', 'core_main', '', 38, 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES
('music', 1, 0, 0, 0);

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('music_main_browse', 'music', 'Browse Music', 'Music_Plugin_Menus::canViewPlaylists', '{"route":"music_general","action":"browse"}', 'music_main', '', 1, 1, 1),
('music_main_manage', 'music', 'My Music', 'Music_Plugin_Menus::canCreatePlaylists', '{"route":"music_general","action":"manage"}', 'music_main', '', 2, 1, 1),
('music_main_create', 'music', 'Upload Music', 'Music_Plugin_Menus::canCreatePlaylists', '{"route":"music_general","action":"create"}', 'music_main', '', 3, 0, 0),

('music_quick_create', 'music', 'Upload Music', 'Music_Plugin_Menus::canCreatePlaylists', '{"route":"music_general","action":"create","class":"buttonlink"}', 'music_quick', '', 1, 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('music_index_manage', 'Sitemobile_modules_Music_Form_Filter_Search', 'search', '', '', ''),
('music_index_browse', 'Sitemobile_modules_Music_Form_Filter_Search', 'search', '', '', '');

INSERT IGNORE INTO `engine4_sitemobile_navigation` (`name`, `menu`, `subject_type`) VALUES
('music', 'music_quick', ''),
('music_playlist_view', 'music_profile', 'music_playlist');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('music_edit', 'music', 'Edit Playlist', 'Sitemobile_Plugin_musicMenus', '', 'music_profile', NULL, '0', '3', '1', '1'),
('music_delete', 'music', 'Delete Playlist', 'Sitemobile_Plugin_musicMenus', '', 'music_profile', NULL, '0', '4', '1', '1'),
('music_share', 'music', 'Share', 'Sitemobile_Plugin_musicMenus', '', 'music_profile', NULL, '0', '5', '1', '1'),
('music_report', 'music', 'Report', 'Sitemobile_Plugin_musicMenus', '', 'music_profile', NULL, '0', '6', '1', '1');

-- --------------------------------------------------------
--
-- Poll Plugin
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('core_main_poll', 'poll', 'Polls',  'Poll_Plugin_Menus::canViewPolls', '{"route":"poll_general", "action":"browse"}', 'core_main', '', 40, 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES
('poll', 1, 0, 0, 0);

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('poll_main_browse', 'poll', 'Browse Polls', 'Poll_Plugin_Menus::canViewPolls', '{"route":"poll_general","action":"browse"}', 'poll_main', '', 1, 1, 1),
('poll_main_manage', 'poll', 'My Polls', 'Poll_Plugin_Menus::canCreatePolls', '{"route":"poll_general","action":"manage"}', 'poll_main', '', 2, 1, 1),
('poll_main_create', 'poll', 'Create New Poll', 'Poll_Plugin_Menus::canCreatePolls', '{"route":"poll_general","action":"create"}', 'poll_main', '', 3, 0, 0),

('poll_quick_create', 'poll', 'Create New Poll', 'Poll_Plugin_Menus::canCreatePolls', '{"route":"poll_general","action":"create","class":"buttonlink"}', 'poll_quick', '', 1, 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('poll_index_manage', 'Sitemobile_modules_Poll_Form_Filter_Search', 'search', '', '', ''),
('poll_index_browse', 'Sitemobile_modules_Poll_Form_Filter_Search', 'search', '', '', '');

INSERT IGNORE INTO `engine4_sitemobile_navigation` (`name`, `menu`, `subject_type`) VALUES
('poll', 'poll_quick', ''),
('poll_poll_view', 'poll_profile', 'poll');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('poll_share', 'poll', 'Share', 'Sitemobile_Plugin_PollMenus', '', 'poll_profile', NULL, '0', '1', '1', '1'),
('poll_report', 'poll', 'Report', 'Sitemobile_Plugin_PollMenus', '', 'poll_profile', NULL, '0', '2', '1', '1');


-- --------------------------------------------------------
--
-- Advanced Activity Plugin
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('core_main_socialfeed', 'advancedactivity', 'Social Feeds',  'Advancedactivity_Plugin_Menus::canViewSMFeeds', '{"route":"default", "action":"index", "controller" : "socialfeed", "module" : "advancedactivity"}', 'core_main', '', 3, 1, 1);

-- --------------------------------------------------------
--
-- Forum Plugin
--

 INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
 ('core_main_forum', 'forum', 'Forum',  '', '{"route":"forum_general"}', 'core_main', '', 50, 1, 1);
 
 INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES
 ('forum', 1, 0, 0, 0);
 
 INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
 ('forum_topic', 'standard', 'Forum Topic Options Menu', '999');
 
 INSERT IGNORE INTO `engine4_sitemobile_navigation` (`name`, `menu`, `subject_type`) VALUES
 ('forum_topic_view', 'forum_topic', 'forum_topic');
 
 
 INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
 ('Forum_topic_watch', 'forum', 'Watch Topic', 'Sitemobile_Plugin_ForumMenus', '', 'forum_topic', NULL, '0', '1', '1', '1'), 
 ('Forum_topic_sticky', 'forum', 'Make Sticky', 'Sitemobile_Plugin_ForumMenus', '', 'forum_topic', NULL, '0', '2', '1', '1'),
 ('Forum_topic_open', 'forum', 'Open', 'Sitemobile_Plugin_ForumMenus', '', 'forum_topic', NULL, '0', '3', '1', '1'),
 ('Forum_topic_rename', 'forum', 'Rename', 'Sitemobile_Plugin_ForumMenus', '', 'forum_topic', NULL, '0', '4', '1', '1'),
 ('Forum_topic_move', 'forum', 'Move', 'Sitemobile_Plugin_ForumMenus', '', 'forum_topic', NULL, '0', '5', '1', '1'),
 ('Forum_topic_delete', 'forum', 'Delete', 'Sitemobile_Plugin_ForumMenus', '', 'forum_topic', NULL, '0', '6', '1', '1');


UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.dashboard.contentType.mobile' WHERE  `engine4_core_settings`.`name` =  'sitemobile.dashboard.contentType' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.header.position.mobile' WHERE  `engine4_core_settings`.`name` =  'sitemobile.mobile.header.position' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.footer.position.mobile' WHERE  `engine4_core_settings`.`name` =  'sitemobile.mobile.footer.position' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.popup.view.mobile' WHERE  `engine4_core_settings`.`name` =  'sitemobile.popup.view' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.mobile.0' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.options.0' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.mobile.1' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.options.1' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.mobile.2' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.options.2' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.mobile.3' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.options.3' LIMIT 1 ;

UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.dashboard.contentType.tablet' WHERE  `engine4_core_settings`.`name` =  'sitemobile.tablet.dashboard.contentType' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.header.position.tablet' WHERE  `engine4_core_settings`.`name` =  'sitemobile.tablet.header.position' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.footer.position.tablet' WHERE  `engine4_core_settings`.`name` =  'sitemobile.tablet.footer.position' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.popup.view.tablet' WHERE  `engine4_core_settings`.`name` =  'sitemobile.popup.tablet' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.tablet.0' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.tablet.options.0' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.tablet.1' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.tablet.options.1' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.tablet.2' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.tablet.options.2' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.tablet.3' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.tablet.options.3' LIMIT 1 ;

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('advancedactivity_settings', 'advancedactivity', 'Hide Content Feed', '', '{"route":"advancedactivity_extended","controller":"feed","action":"edit-hide-options"}', 'user_settings', NULL, '0', '4', '1', '1');




-- --------------------------------------------------------

--
-- Table structure for table `engine4_storage_files`
--

DROP TABLE IF EXISTS `engine4_sitemobile_storage_files`;
CREATE TABLE IF NOT EXISTS `engine4_sitemobile_storage_files` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_file_id` int(10) unsigned DEFAULT NULL,
  `type` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `parent_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `service_id` int(10) unsigned NOT NULL DEFAULT '1',
  `storage_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mime_major` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `mime_minor` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `hash` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`file_id`),
  UNIQUE KEY `parent_file_id` (`parent_file_id`,`type`),
  KEY `PARENT` (`parent_type`,`parent_id`),
  KEY `user_id` (`user_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
( 'core_main_invite', 'invite', 'Invite', 'Invite_Plugin_Menus::canInvite', '{"route":"default","module":"invite"}', 'core_main', '', 999, 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES ('invite', '1', '1', '1', '1');
