INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('advancedactivity', 'Advanced Activity', 'Advanced Activity', '4.8.12p5', 1, 'extra') ;

-- ----------------------------------------------------------


UPDATE  `engine4_activity_actiontypes` SET  `body` =  '{actors:$subject:$object}:\r\n{body:$body}' WHERE  `engine4_activity_actiontypes`.`type` =  'post' LIMIT 1 ;

UPDATE  `engine4_activity_actiontypes` SET  `body` =  '{item:$subject}\r\n{body:$body}' WHERE  `engine4_activity_actiontypes`.`type` =  'post_self' LIMIT 1 ;

UPDATE  `engine4_activity_actiontypes` SET  `body` =  '{item:$subject}\r\n{body:$body}' WHERE  `engine4_activity_actiontypes`.`type` =  'status' LIMIT 1 ;

UPDATE `engine4_core_jobtypes` SET `enabled` = '0' WHERE `engine4_core_jobtypes`.`type` ='activity_maintenance_rebuild_privacy' LIMIT 1 ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_advancedactivity_savefeed`
--

DROP TABLE IF EXISTS `engine4_advancedactivity_savefeeds`;
CREATE TABLE `engine4_advancedactivity_savefeeds` (
`user_id` INT( 11 ) NOT NULL ,
`action_type` VARCHAR( 128 ) NOT NULL ,
`action_id` INT( 11 ) NOT NULL ,
PRIMARY KEY ( `user_id` , `action_id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;


INSERT IGNORE INTO `engine4_advancedactivity_contents` ( `module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES ('network', 'only_network', 'My Networks', '0', '1', '1');


CREATE TABLE IF NOT EXISTS `engine4_advancedactivity_linkedin` (
  `user_id` int(10) unsigned NOT NULL,
  `linkedin_uid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `linkedin_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `linkedin_secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `linkedin_uid` (`linkedin_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_advancedactivity_contents` ( `module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES ('core', 'user_saved', 'Saved Feeds', '1', '999', '1');


INSERT IGNORE INTO `engine4_advancedactivity_contents` ( `module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES ('sitegroup', 'sitegroup', 'Groups', '1', '999', '1');
INSERT IGNORE INTO `engine4_advancedactivity_customtypes` ( `module_name`, `resource_type`, `resource_title`, `enabled`, `order`, `default`) VALUES ('sitegroup', 'sitegroup_group', 'Groups', '1', '999', '1');

INSERT IGNORE INTO `engine4_advancedactivity_contents` ( `module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES ('sitebusiness', 'sitebusiness', 'Businesses', '1', '999', '1'),('sitestoreproduct', 'sitestoreproduct', 'Store Products', '1', '999', '1');

INSERT IGNORE INTO `engine4_advancedactivity_customtypes` ( `module_name`, `resource_type`, `resource_title`, `enabled`, `order`, `default`) VALUES ('sitebusiness', 'sitebusiness_business', 'Businesses', '1', '999', '0'),('sitestoreproduct', 'sitestoreproduct_product', 'Store Products', '1', '999', '0');

INSERT IGNORE INTO `engine4_advancedactivity_contents` ( `module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES ('sitestore', 'sitestore', 'Stores', '1', '999', '1');
INSERT IGNORE INTO `engine4_advancedactivity_customtypes` ( `module_name`, `resource_type`, `resource_title`, `enabled`, `order`, `default`) VALUES ('sitestore', 'sitestore_store', 'Stores', '1', '999', '1');


INSERT IGNORE INTO `engine4_advancedactivity_contents` ( `module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES ('siteevent', 'siteevent', 'Event', '1', '999', '1');
INSERT IGNORE INTO `engine4_advancedactivity_customtypes` ( `module_name`, `resource_type`, `resource_title`, `enabled`, `order`, `default`) VALUES ('siteevent', 'siteevent_event', 'Events', '1', '999', '1');

CREATE TABLE IF NOT EXISTS `engine4_advancedactivity_instagram` (
  `user_id` int(10) unsigned NOT NULL,
  `instagram_uid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `instagram_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `instagram_secret` varchar(255) COLLATE utf8_unicode_ci NULL DEFAULT '',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `instagram_uid` (`instagram_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
