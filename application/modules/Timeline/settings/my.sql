insert IGNORE into `engine4_core_menuitems`(`name`,`module`,`label`,`plugin`,`params`,`menu`,`submenu`,`custom`,`order`)
		                    values ('core_admin_main_plugins_timeline','timeline','Timeline','','{\"route\":\"admin_default\",\"module\":\"timeline\",\"controller\":\"settings\"}','core_admin_main_plugins','',0,999),
                                           ('timeline_admin_main_settings','timeline','Global Settings','','{\"route\":\"admin_default\",\"module\":\"timeline\",\"controller\":\"settings\"}','timeline_admin_main','',0,1),
                                           ('timeline_admin_main_level','timeline','Member Level Settings','','{\"route\":\"admin_default\",\"module\":\"timeline\",\"controller\":\"level\"}','timeline_admin_main','',0,2),
                                           ('timeline_admin_main_icons','timeline','Tabs Icons','','{\"route\":\"admin_default\",\"module\":\"timeline\",\"controller\":\"settings\",\"action\":\"tabs-icons\"}','timeline_admin_main','',0,3),
                                           ('timeline_admin_main_cover','timeline','Cover','','{\"route\":\"admin_default\",\"module\":\"timeline\",\"controller\":\"settings\",\"action\":\"cover\"}','timeline_admin_main','',0,4);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`)
                                     VALUES ('user_settings_profilelayout', 'timeline', 'Profile Settings', 'Timeline_Plugin_Menus::canSelect', '{\"route\":\"default\",\"module\":\"timeline\",\"controller\":\"settings\",\"action\":\"profile-layout\"}', 'user_settings', '', 999);

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'timeline' as `type`,
    'timeline_profile' as `name`,
    1 as `value`,
    null as `params`
  FROM `engine4_authorization_levels` WHERE `type` != 'public';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'timeline' as `type`,
    'user_can_select' as `name`,
    1 as `value`,
    null as `params`
  FROM `engine4_authorization_levels` WHERE `type` != 'public';

CREATE TABLE `engine4_timeline_features` (
  `feature_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `action_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `feature_id` (`feature_id`),
  KEY `FK_engine4_timeline_features_user` (`user_id`),
  KEY `FK_engine4_timeline_features_action` (`action_id`),
  CONSTRAINT `FK_engine4_timeline_features_action` FOREIGN KEY (`action_id`) REFERENCES `engine4_activity_actions` (`action_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_engine4_timeline_features_user` FOREIGN KEY (`user_id`) REFERENCES `engine4_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`)
                                           VALUES ('timeline_cover_update', 'timeline', '{item:$subject} has changed a profile cover.', 1, 5, 1, 1, 1, 1);
