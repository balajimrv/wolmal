
/**
 * SocialEngine - SocialEngineMods
 *
 */

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('activitypoints', 'Activity Points plugin', 'Activity Points plugin', '4.0.0', 1, 'extra');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('core_main_points_topusers', 'core', 'Top Users', 'Activitypoints_Plugin_Menus', '{"route":"topusers"}', 'core_main', '', 0, 1);

INSERT IGNORE INTO `engine4_core_menuitems` (`name` ,`module` ,`label` ,`plugin` ,`params` ,`menu` ,`submenu` ,`custom` ,`order` ) VALUES (
'user_profile_sendpoints', 'activitypoints', 'Send Points', 'Activitypoints_Plugin_Menus', '', 'user_profile', '', '0', '0');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('core_admin_main_plugins_activitypoints', 'activitypoints', 'Activity Points', '', '{"route":"admin_default","module":"activitypoints","controller":"settings","action":"index"}', 'core_admin_main_plugins', '', 0, 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activitypoints_admin_main_settings', 'activitypoints', 'General Settings', '', '{"route":"admin_default","module":"activitypoints","controller":"settings"}', 'activitypoints_admin_main', '', 0, 1),
('activitypoints_admin_main_levels', 'activitypoints', 'Levels Settings', '', '{"route":"admin_default","module":"activitypoints","controller":"levels"}', 'activitypoints_admin_main', '', 0, 2),
('activitypoints_admin_main_manage', 'activitypoints', 'View Users', '', '{"route":"admin_default","module":"activitypoints","controller":"manage"}', 'activitypoints_admin_main', '', 0, 3),
('activitypoints_admin_main_assignpoints', 'activitypoints', 'Assign Points', '', '{"route":"admin_default","module":"activitypoints","controller":"assignpoints"}', 'activitypoints_admin_main', '', 0, 4),
('activitypoints_admin_main_givepoints', 'activitypoints', 'Give Points', '', '{"route":"admin_default","module":"activitypoints","controller":"givepoints"}', 'activitypoints_admin_main', '', 0, 5),
('activitypoints_admin_main_pointranks', 'activitypoints', 'Point Ranks', '', '{"route":"admin_default","module":"activitypoints","controller":"pointranks"}', 'activitypoints_admin_main', '', 0, 6),
('activitypoints_admin_main_transactions', 'activitypoints', 'Transactions', '', '{"route":"admin_default","module":"activitypoints","controller":"transactions"}', 'activitypoints_admin_main', '', 0, 7),
('activitypoints_admin_main_help', 'activitypoints', 'Help', '', '{"route":"admin_default","module":"activitypoints","controller":"help"}', 'activitypoints_admin_main', '', 0, 100);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activitypoints_admin_main_transactions', 'activitypoints', 'Transactions', '', '{"route":"admin_default","module":"activitypoints","controller":"transactions"}', 'activitypoints_admin_main', '', '0', '5'
);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activitypoints_admin_edit_user', 'activitypoints', 'Edit User', '', '{"route":"admin_default","module":"activitypoints","controller":"manage", "action":"edit"}', 'activitypoints_admin_edit', '', 0, 1);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activitypoints_admin_edit_stats', 'activitypoints', 'Activity Statistics', '', '{"route":"admin_default","module":"activitypoints","controller":"manage", "action":"stats"}', 'activitypoints_admin_edit', '', 0, 2);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activitypoints_admin_edit_transactions', 'activitypoints', 'Transactions', '', '{"route":"admin_default","module":"activitypoints","controller":"manage", "action":"transactions"}', 'activitypoints_admin_edit', '', 0, 3);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activitypoints_admin_edit_quotas', 'activitypoints', 'Quotas and Usage', '', '{"route":"admin_default","module":"activitypoints","controller":"manage", "action":"quotas"}', 'activitypoints_admin_edit', '', 0, 4);


/* user navigation */
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activitypoints_vault', 'activitypoints', 'My Vault', '', '{"route":"activitypoints_vault","module":"activitypoints","controller":"index","action":"index"}', 'activitypoints_main', '', '0', '0');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activitypoints_transactions', 'activitypoints', 'Transactions', '', '{"route":"activitypoints_transactions","module":"activitypoints","controller":"index","action":"transactions"}', 'activitypoints_main', '', '0', '1');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activitypoints_topusers', 'activitypoints', 'Top Users', 'Activitypoints_Plugin_Menus', '{"route":"activitypoints_topusers","module":"activitypoints","controller":"topusers","action":"index"}', 'activitypoints_main', '', '0', '99');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activitypoints_help', 'activitypoints', 'Help', '', '{"route":"activitypoints_help","module":"activitypoints","controller":"index","action":"help"}', 'activitypoints_main', '', '0', '100');

/* user home menu */
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES (
'user_home_points', 'activitypoints', 'View My Points', '', '{"route":"activitypoints_vault","icon":"application/modules/Activitypoints/externals/images/userpoints_coins16.png"}', 'user_home', '', '0', '5');


/* Settings */

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES('activitypoints.enable.pointrank', '1');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES('activitypoints.enable.statistics', '1');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES('activitypoints.enable.topusers', '1');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES('activitypoints.max.topusers', '10');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES('activitypoints.ranktype', '1');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES('activitypoints.topusers.exclude', '');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES('activitypoints.topusers.rankby', '1');


/* Level Permissions */

INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(1, 'activitypoints', 'allow_transfer', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(1, 'activitypoints', 'max_transfer', 3, 1000);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(1, 'activitypoints', 'max_receive', 3, 1000);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(1, 'activitypoints', 'use', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(2, 'activitypoints', 'allow_transfer', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(2, 'activitypoints', 'max_transfer', 3, 1000);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(2, 'activitypoints', 'max_receive', 3, 1000);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(2, 'activitypoints', 'use', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(3, 'activitypoints', 'allow_transfer', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(3, 'activitypoints', 'max_transfer', 3, 1000);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(3, 'activitypoints', 'max_receive', 3, 1000);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(3, 'activitypoints', 'use', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(4, 'activitypoints', 'allow_transfer', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(4, 'activitypoints', 'max_transfer', 3, 1000);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(4, 'activitypoints', 'max_receive', 3, 1000);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(4, 'activitypoints', 'use', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(5, 'activitypoints', 'allow_transfer', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(5, 'activitypoints', 'max_transfer', 3, 1000);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(5, 'activitypoints', 'max_receive', 3, 1000);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(5, 'activitypoints', 'use', 1, NULL);




INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('points_sent', 'user', '{item:$subject} sent you {item:$object:$amount} points.', 0, '');



CREATE TABLE IF NOT EXISTS `engine4_semods_actionpoints` (
  `action_id` int(11) NOT NULL auto_increment,
  `action_type` varchar(50) NOT NULL,
  `action_enabled` tinyint(1) NOT NULL default '1',
  `action_name` varchar(255) NOT NULL,
  `action_points` int(11) NOT NULL,
  `action_pointsmax` int(11) NOT NULL default '0',
  `action_rolloverperiod` int(11) NOT NULL default '0',
  `action_requiredplugin` varchar(100) default NULL,
  `action_group` tinyint(4) NOT NULL default '0',
  `action_module` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `action_custom` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`action_id`),
  UNIQUE KEY `action_type` (`action_type`),
  KEY `action_group` (`action_group`),
  KEY `action_custom` (`action_custom`)
) DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;


/* ACTIONS */
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(1, 'transferpoints', 1, 'Transfer Points', 1, 0, 86400, NULL, -1, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(3, 'receivepoints', 1, 'Receive Points', 1, 0, 86400, NULL, -1, '', 0);

INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(101, 'invite', 1, 'Invite Friends (for each invited friend)', 100, 500, 0, 'Friends Inviter plugin', 101, 'friendsinviter', 1);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(102, 'refer', 1, 'Refer friends (actual signup)', 100, 0, 0, 'Friends Inviter plugin', 101, 'friendsinviter', 1);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(103, 'signup', 1, 'Signup Bonus', 500, 0, 0, NULL, 101, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(104, 'friends', 1, 'Adding a friend', 1, 10, 86400, NULL, 100, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(105, 'message', 1, 'Sending a message to other member', 1, 20, 86400, NULL, 100, '', 1);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(107, 'fields_change_generic', 1, 'Edit / Update profile', 10, 100, 86400, NULL, 100, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(108, 'status', 1, 'Updating status', 1, 50, 86400, NULL, 100, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(109, 'login', 1, 'Login to site (requires logout)', 1, 10, 86400, NULL, 100, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(111, 'event_create', 1, 'Create a new event', 100, 0, 0, 'SocialEngine Events plugin', 3, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(112, 'event_join', 1, 'Joining an event', 1, 0, 0, 'SocialEngine Events plugin', 3, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(115, 'blog_new', 1, 'Post a blog', 1, 0, 0, 'SocialEngine Blogs plugin', 5, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(116, 'comment_blog', 1, 'Comment on a Blog', 1, 0, 0, 'SocialEngine Blogs plugin', 5, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(117, 'classified_new', 1, 'Create a classified', 100, 1000, 86400, 'SocialEngine Classifieds plugin', 4, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(118, 'comment_classified', 1, 'Comment on classified', 10, 100, 86400, 'SocialEngine Classifieds plugin', 4, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(119, 'newalbum', 1, 'Upload an album', 100, 1000, 86400, 'SocialEngine Photos plugin', 6, 'album', 1);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(122, 'group_create', 1, 'Create a new group', 100, 200, 86400, 'SocialEngine Groups plugin', 1, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(123, 'group_join', 1, 'Join a group', 50, 200, 86400, 'SocialEngine Groups plugin', 1, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(125, 'group_topic_create', 1, 'Creating a Group Discussion Topic', 10, 100, 86400, 'SocialEngine Groups plugin', 1, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(126, 'group_topic_reply', 1, 'Replying to group discussion topic', 10, 100, 86400, 'SocialEngine Groups plugin', 1, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(127, 'poll_new', 1, 'Create a poll', 10, 100, 86400, 'SocialEngine Polls plugin', 2, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(129, 'comment_poll', 1, 'Commenting on a Poll', 10, 100, 86400, 'SocialEngine Polls plugin', 2, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(131, 'newmusic', 1, 'Adding a Song', 100, 1000, 86400, 'SocialEngine Music plugin', 9, 'music', 1);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(132, 'tagged', 1, 'Getting tagged', 100, 1000, 86400, NULL, 100, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(134, 'profile_photo_update', 1, 'Updating a profile photo', 10, 100, 86400, NULL, 100, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(135, 'post_self', 1, 'Posting an attachment', 10, 100, 86400, NULL, 100, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(136, 'post', 1, 'Posting a message to someone else''s profile', 10, 100, 86400, NULL, 100, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(138, 'network_join', 1, 'Joining a network', 1, 5, 86400, NULL, 100, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(139, 'music_playlist_new', 1, 'Creating a new music playlist', 10, 100, 86400, 'SocialEngine Music plugin', 9, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(140, 'logout', 1, 'Logging out from website', 0, 0, 0, NULL, 100, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(141, 'group_promote', 1, 'Promoting to officer status in a group', 10, 100, 86400, NULL, 1, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(142, 'group_photo_upload', 1, 'Uploading a new photo to a group', 10, 100, 86400, NULL, 1, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(146, 'event_topic_reply', 1, 'Reply to Event discussion topic', 0, 0, 0, 'SocialEngine Events plugin', 3, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(147, 'event_topic_create', 1, 'Create Event discussion topic', 0, 0, 0, 'SocialEngine Events plugin', 3, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(148, 'event_photo_upload', 1, 'Upload photos to event', 0, 0, 0, 'SocialEngine Events plugin', 3, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(151, 'comment_video', 1, 'Commenting on a Video', 1, 100, 86400, 'SocialEngine Videos plugin', 10, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(153, 'comment_playlist', 1, 'Commenting on a playlist', 1, 10, 86400, 'SocialEngine Music plugin', 9, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(156, 'comment_album_photo', 1, 'Commenting on an album photo', 100, 1000, 86400, 'SocialEngine Photos plugin', 6, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(157, 'comment_album', 1, 'Posting a comment on an album', 100, 1000, 86400, 'SocialEngine Photos plugin', 6, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(160, 'album_photo_new', 1, 'Uploading a new photo', 150, 1500, 86400, 'SocialEngine Photos plugin', 6, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(161, 'video_new', 1, 'Uploading a new Video', 250, 2500, 86400, 'SocialEngine Videos plugin', 10, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(196, 'friends_follow', 1, 'Following a friend', 0, 0, 0, NULL, 100, '', 0);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(197, 'like', 1, 'Liking something', 10, 1000, 86400, NULL, 100, '', 1);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(198, 'forum_topic', 1, 'Creating a forum topic', 100, 1000, 86400, 'SocialEngine Forum plugin', 11, 'forum', 1);
INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_id`, `action_type`, `action_enabled`, `action_name`, `action_points`, `action_pointsmax`, `action_rolloverperiod`, `action_requiredplugin`, `action_group`, `action_module`, `action_custom`) VALUES(199, 'forum_post', 1, 'Posting a forum message', 100, 1000, 86400, 'SocialEngine Forum plugin', 11, 'forum', 1);

INSERT IGNORE INTO `engine4_semods_actionpoints` (`action_type` ,`action_enabled` ,`action_name` ,`action_points` ,`action_pointsmax` ,`action_rolloverperiod` ,`action_requiredplugin` ,`action_group` ,`action_module` ,`action_custom`) VALUES('comment_activity',  '1',  'Comment on Newsfeed Activity ',  '10',  '100',  '86400',  NULL,  '100',  '',  '1');






CREATE TABLE IF NOT EXISTS `engine4_semods_userpointcounters` (
  `userpointcounters_user_id` int(11) unsigned NOT NULL,
  `userpointcounters_action_id` int(11) NOT NULL,
  `userpointcounters_lastrollover` int(4) NOT NULL default '0',
  `userpointcounters_amount` int(11) NOT NULL default '0',
  `userpointcounters_cumulative` int(11) NOT NULL default '0',
  PRIMARY KEY  (`userpointcounters_user_id`,`userpointcounters_action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;



CREATE TABLE IF NOT EXISTS `engine4_semods_userpointstats` (
  `userpointstat_id` int(9) NOT NULL auto_increment,
  `userpointstat_user_id` int(11) unsigned NOT NULL,
  `userpointstat_date` int(9) NOT NULL default '0',
  `userpointstat_earn` int(9) NOT NULL default '0',
  `userpointstat_spend` int(9) NOT NULL default '0',
  PRIMARY KEY  (`userpointstat_id`),
  UNIQUE KEY `userpointstat_user_id` (`userpointstat_user_id`,`userpointstat_date`)
) DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;







CREATE TABLE IF NOT EXISTS `engine4_semods_uptransactions` (
  `uptransaction_id` int(11) NOT NULL auto_increment,
  `uptransaction_user_id` int(11) unsigned NOT NULL,
  `uptransaction_type` int(11) NOT NULL,
  `uptransaction_cat` int(11) NOT NULL default '0',
  `uptransaction_state` tinyint(4) NOT NULL,
  `uptransaction_text` text,
  `uptransaction_date` datetime NOT NULL,
  `uptransaction_amount` int(11) NOT NULL,
  `uptransaction_item_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`uptransaction_id`),
  KEY `uptransaction_item_id` (`uptransaction_item_id`)
) ENGINE=InnoDB ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;



CREATE TABLE IF NOT EXISTS `engine4_semods_userpointearnertypes` (
  `userpointearnertype_id` int(11) NOT NULL auto_increment,
  `userpointearnertype_type` int(11) NOT NULL,
  `userpointearnertype_typename` varchar(50) NOT NULL,
  `userpointearnertype_name` varchar(20) NOT NULL,
  `userpointearnertype_title` varchar(255) NOT NULL,
  `form` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`userpointearnertype_id`),
  UNIQUE KEY `userpointearnertype_type` (`userpointearnertype_type`)
) DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;




CREATE TABLE IF NOT EXISTS `engine4_semods_userpointranks` (
  `userpointrank_id` int(11) NOT NULL auto_increment,
  `userpointrank_amount` int(11) NOT NULL,
  `userpointrank_text` varchar(100) NOT NULL,
  PRIMARY KEY  (`userpointrank_id`)
) DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;


INSERT IGNORE INTO `engine4_semods_userpointranks` (`userpointrank_id`, `userpointrank_amount`, `userpointrank_text`) VALUES (1, 0, 'Rookie');
INSERT IGNORE INTO `engine4_semods_userpointranks` (`userpointrank_id`, `userpointrank_amount`, `userpointrank_text`) VALUES (2, 500, 'Lieutenant');
INSERT IGNORE INTO `engine4_semods_userpointranks` (`userpointrank_id`, `userpointrank_amount`, `userpointrank_text`) VALUES (3, 1000, 'Member');
INSERT IGNORE INTO `engine4_semods_userpointranks` (`userpointrank_id`, `userpointrank_amount`, `userpointrank_text`) VALUES (4, 2000, 'Advanced Member');
INSERT IGNORE INTO `engine4_semods_userpointranks` (`userpointrank_id`, `userpointrank_amount`, `userpointrank_text`) VALUES (5, 10000, 'King');
INSERT IGNORE INTO `engine4_semods_userpointranks` (`userpointrank_id`, `userpointrank_amount`, `userpointrank_text`) VALUES (6, 100000, 'Impossible');



CREATE TABLE IF NOT EXISTS `engine4_semods_userpoints` (
  `userpoints_user_id` int(11) unsigned NOT NULL,
  `userpoints_count` int(11) NOT NULL default '0',
  `userpoints_totalearned` int(11) NOT NULL default '0',
  `userpoints_totalspent` int(11) NOT NULL default '0',
  PRIMARY KEY  (`userpoints_user_id`),
  KEY `userpoints_totalearned` (`userpoints_totalearned`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;




CREATE TABLE IF NOT EXISTS `engine4_semods_userpointspendertypes` (
  `userpointspendertype_id` int(11) NOT NULL auto_increment,
  `userpointspendertype_type` int(11) NOT NULL,
  `userpointspendertype_typename` varchar(50) NOT NULL,
  `userpointspendertype_name` varchar(20) NOT NULL,
  `userpointspendertype_title` varchar(255) NOT NULL,
  `form` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`userpointspendertype_id`),
  UNIQUE KEY `userpointspendertype_type` (`userpointspendertype_type`)
) DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

INSERT IGNORE INTO `engine4_semods_userpointspendertypes` (`userpointspendertype_id`, `userpointspendertype_type`, `userpointspendertype_typename`, `userpointspendertype_name`, `userpointspendertype_title`, `form`, `model`) VALUES(1, 1, 'Post a Classified listing', 'charge', 'Posting a Classified listing', '', '');
INSERT IGNORE INTO `engine4_semods_userpointspendertypes` (`userpointspendertype_id`, `userpointspendertype_type`, `userpointspendertype_typename`, `userpointspendertype_name`, `userpointspendertype_title`, `form`, `model`) VALUES(2, 2, 'Create an Event', 'charge', 'Creating an Event', '', '');
INSERT IGNORE INTO `engine4_semods_userpointspendertypes` (`userpointspendertype_id`, `userpointspendertype_type`, `userpointspendertype_typename`, `userpointspendertype_name`, `userpointspendertype_title`, `form`, `model`) VALUES(3, 3, 'Create a Group', 'charge', 'Creating a Group', '', '');
INSERT IGNORE INTO `engine4_semods_userpointspendertypes` (`userpointspendertype_id`, `userpointspendertype_type`, `userpointspendertype_typename`, `userpointspendertype_name`, `userpointspendertype_title`, `form`, `model`) VALUES(4, 4, 'Create a Poll', 'charge', 'Creating a Poll', '', '');




CREATE TABLE IF NOT EXISTS `engine4_semods_userpointspender` (
  `userpointspender_id` int(11) NOT NULL auto_increment,
  `userpointspender_type` int(4) NOT NULL default '0',
  `userpointspender_name` varchar(100) NOT NULL,
  `userpointspender_title` varchar(255) NOT NULL,
  `userpointspender_body` text NOT NULL,
  `userpointspender_date` int(4) NOT NULL default '0',
  `userpointspender_photo` varchar(10) default NULL,
  `userpointspender_cost` int(11) NOT NULL default '0',
  `userpointspender_views` int(11) NOT NULL default '0',
  `userpointspender_comments` int(11) NOT NULL default '0',
  `userpointspender_comments_allowed` tinyint(1) NOT NULL default '1',
  `userpointspender_enabled` tinyint(1) NOT NULL default '1',
  `userpointspender_transact_state` int(11) NOT NULL default '0',
  `userpointspender_metadata` text NOT NULL,
  `userpointspender_redirect_on_buy` varchar(255) default NULL,
  `userpointspender_tags` varchar(255) default NULL,
  `userpointspender_engagements` int(11) NOT NULL default '0',
  `userpointspender_levels` text,
  `userpointspender_subnets` text,
  `userpointspender_max_acts` int(11) NOT NULL DEFAULT '0',
  `userpointspender_rolloverperiod` int(11) NOT NULL DEFAULT '0',
  `userpointspender_actiontext` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `owner_id` int(10) unsigned NOT NULL,
  `userpointspender_instock` int(11) NOT NULL DEFAULT '0',
  `userpointspender_instock_track` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`userpointspender_id`),
  KEY `userpointspender_type` (`userpointspender_type`)
) DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;


INSERT IGNORE INTO `engine4_semods_userpointspender` (`userpointspender_id`, `userpointspender_type`, `userpointspender_name`, `userpointspender_title`, `userpointspender_body`, `userpointspender_date`, `userpointspender_photo`, `userpointspender_cost`, `userpointspender_views`, `userpointspender_comments`, `userpointspender_comments_allowed`, `userpointspender_enabled`, `userpointspender_transact_state`, `userpointspender_metadata`, `userpointspender_redirect_on_buy`, `userpointspender_tags`, `userpointspender_engagements`, `userpointspender_levels`, `userpointspender_subnets`, `userpointspender_max_acts`, `userpointspender_rolloverperiod`, `userpointspender_actiontext`, `owner_id`, `userpointspender_instock`, `userpointspender_instock_track`) VALUES(1, 1, '', 'Post a Classified listing', 'Post a Classified listing', 0, '', 1, 0, 0, 1, 1, 0, '', NULL, NULL, 0, NULL, NULL, 0, 0, '', 0, 0, 0);
INSERT IGNORE INTO `engine4_semods_userpointspender` (`userpointspender_id`, `userpointspender_type`, `userpointspender_name`, `userpointspender_title`, `userpointspender_body`, `userpointspender_date`, `userpointspender_photo`, `userpointspender_cost`, `userpointspender_views`, `userpointspender_comments`, `userpointspender_comments_allowed`, `userpointspender_enabled`, `userpointspender_transact_state`, `userpointspender_metadata`, `userpointspender_redirect_on_buy`, `userpointspender_tags`, `userpointspender_engagements`, `userpointspender_levels`, `userpointspender_subnets`, `userpointspender_max_acts`, `userpointspender_rolloverperiod`, `userpointspender_actiontext`, `owner_id`, `userpointspender_instock`, `userpointspender_instock_track`) VALUES(2, 2, '', 'Create an Event', 'Create an Event', 0, '', 1, 0, 0, 1, 1, 0, '', NULL, NULL, 0, NULL, NULL, 0, 0, '', 0, 0, 0);
INSERT IGNORE INTO `engine4_semods_userpointspender` (`userpointspender_id`, `userpointspender_type`, `userpointspender_name`, `userpointspender_title`, `userpointspender_body`, `userpointspender_date`, `userpointspender_photo`, `userpointspender_cost`, `userpointspender_views`, `userpointspender_comments`, `userpointspender_comments_allowed`, `userpointspender_enabled`, `userpointspender_transact_state`, `userpointspender_metadata`, `userpointspender_redirect_on_buy`, `userpointspender_tags`, `userpointspender_engagements`, `userpointspender_levels`, `userpointspender_subnets`, `userpointspender_max_acts`, `userpointspender_rolloverperiod`, `userpointspender_actiontext`, `owner_id`, `userpointspender_instock`, `userpointspender_instock_track`) VALUES(3, 3, '', 'Create a Group', 'Create a Group', 0, '', 1, 0, 0, 1, 1, 0, '', NULL, NULL, 0, NULL, NULL, 0, 0, '', 0, 0, 0);
INSERT IGNORE INTO `engine4_semods_userpointspender` (`userpointspender_id`, `userpointspender_type`, `userpointspender_name`, `userpointspender_title`, `userpointspender_body`, `userpointspender_date`, `userpointspender_photo`, `userpointspender_cost`, `userpointspender_views`, `userpointspender_comments`, `userpointspender_comments_allowed`, `userpointspender_enabled`, `userpointspender_transact_state`, `userpointspender_metadata`, `userpointspender_redirect_on_buy`, `userpointspender_tags`, `userpointspender_engagements`, `userpointspender_levels`, `userpointspender_subnets`, `userpointspender_max_acts`, `userpointspender_rolloverperiod`, `userpointspender_actiontext`, `owner_id`, `userpointspender_instock`, `userpointspender_instock_track`) VALUES(4, 4, '', 'Create a Poll', 'Create a Poll', 0, '', 1, 0, 0, 1, 1, 0, '', NULL, NULL, 0, NULL, NULL, 0, 0, '', 0, 0, 0);






CREATE TABLE IF NOT EXISTS `engine4_semods_userpointearner` (
  `userpointearner_id` int(11) NOT NULL auto_increment,
  `userpointearner_type` int(4) NOT NULL default '0',
  `userpointearner_name` varchar(100) NOT NULL,
  `userpointearner_title` varchar(255) NOT NULL,
  `userpointearner_body` text NOT NULL,
  `userpointearner_date` int(4) NOT NULL default '0',
  `userpointearner_photo` varchar(10) default NULL,
  `userpointearner_cost` int(11) NOT NULL default '0',
  `userpointearner_views` int(11) NOT NULL default '0',
  `userpointearner_comments` int(11) NOT NULL default '0',
  `userpointearner_comments_allowed` tinyint(1) NOT NULL default '1',
  `userpointearner_enabled` tinyint(1) NOT NULL default '1',
  `userpointearner_transact_state` int(11) NOT NULL default '0',
  `userpointearner_metadata` text NOT NULL,
  `userpointearner_redirect_on_buy` varchar(255) default NULL,
  `userpointearner_tags` varchar(255) default NULL,
  `userpointearner_field1` int(11) NOT NULL default '0',
  `userpointearner_engagements` int(11) NOT NULL default '0',
  `userpointearner_levels` text NOT NULL,
  `userpointearner_subnets` text NOT NULL,
  `userpointearner_max_acts` int(11) NOT NULL DEFAULT '0',
  `userpointearner_rolloverperiod` int(11) NOT NULL DEFAULT '0',
  `userpointearner_actiontext` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `owner_id` int(10) unsigned NOT NULL,
  `userpointearner_instock` int(11) NOT NULL DEFAULT '0',
  `userpointearner_instock_track` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`userpointearner_id`),
  KEY `userpointearner_field1` (`userpointearner_field1`)
) DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;







/* SYNC USERS TO POINTS TABLE */
INSERT IGNORE INTO engine4_semods_userpoints (userpoints_user_id) (SELECT user_id FROM engine4_users);
