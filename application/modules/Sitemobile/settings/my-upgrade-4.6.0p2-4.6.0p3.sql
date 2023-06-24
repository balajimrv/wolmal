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
