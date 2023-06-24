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