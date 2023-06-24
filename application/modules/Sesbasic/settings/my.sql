INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sesbasic', 'SocialEngineSolutions Basic Required Plugin', 'SocialEngineSolutions Basic Required Plugin', '4.9.0', '1', 'extra') ;

INSERT IGNORE INTO `engine4_sesbasic_plugins` (`module_name`, `title`, `description`, `current_version`, `site_version`, `category`, `pluginpage_link`) VALUES  ('sesbasic', 'SocialEngineSolutions Basic Required Plugin', 'SocialEngineSolutions Basic Required Plugin', '4.9.0', '4.9.0', 'plugin', 'http://www.socialenginesolutions.com/social-engine/socialenginesolutions-basic-required-plugin/');


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_plugins_sesbasic', 'sesbasic', 'SES - Basic Required', '', '{"route":"admin_default","module":"sesbasic","controller":"settings","action":"overview"}', 'core_admin_main_plugins', '', 999),
('sesbasic_admin_overview', 'sesbasic', 'SES Overview', '', '{"route":"admin_default","module":"sesbasic","controller":"settings","action":"overview"}', 'sesbasic_admin_main', '', 1),
('sesbasic_admin_global', 'sesbasic', 'Global Settings', '', '{"route":"admin_default","module":"sesbasic","controller":"settings","action":"global"}', 'sesbasic_admin_main', '', 2),
('sesbasic_admin_colorpicker', 'sesbasic', 'Color Picker', '', '{"route":"admin_default","module":"sesbasic","controller":"settings","action":"color-chooser"}', 'sesbasic_admin_main', '', 3),
('sesbasic_admin_manage', 'sesbasic', 'Manage Video Lightbox', '', '{"route":"admin_default","module":"sesbasic","controller":"lightbox","action":"video"}', 'sesbasic_admin_main', '', 4),
('sesbasic_admin_memberlevel', 'sesbasic', 'Member Level Setting', '', '{"route":"admin_default","module":"sesbasic","controller":"lightbox","action":"index"}', 'sesbasic_admin_manage', '', 2),
('sesbasic_admin_videolightbox', 'sesbasic', 'Video Lightbox Settings', '', '{"route":"admin_default","module":"sesbasic","controller":"lightbox","action":"video"}', 'sesbasic_admin_manage', '', 1),
('sesbasic_admin_contactus', 'sesbasic', 'Feature Request', '', '{"route":"admin_default","module":"sesbasic","controller":"settings","action":"contact-us"}', 'sesbasic_admin_main', '', 999),
('sesbasic_admin_main_currency', 'sesbasic', 'Manage Currency', '', '{"route":"admin_default","module":"sesbasic","controller":"settings","action":"currency"}', 'sesbasic_admin_main', '', 10),
('sesbasic_admin_main_instagram', 'sesbasic', 'Instagram', '', '{"route":"admin_default","module":"sesbasic","controller":"settings","action":"instagram"}', 'sesbasic_admin_main', '', 11);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sesbasic_locations`
--
DROP TABLE IF EXISTS `engine4_sesbasic_locations`;
CREATE TABLE IF NOT EXISTS `engine4_sesbasic_locations` (
`location_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`resource_id` INT( 11 ) NOT NULL ,
`lat` DECIMAL( 10, 8 ) NULL ,
`lng` DECIMAL( 11, 8 ) NULL ,
`resource_type` VARCHAR( 65 ) NOT NULL DEFAULT 'sesalbum',
`venue` VARCHAR(255) NULL,
`address` TEXT NULL,
`address2` TEXT NULL,
`city` VARCHAR(255) NULL,
`state` VARCHAR(255) NULL,
`zip` VARCHAR(255) NULL,
`country` VARCHAR(255) NULL,
`modified_date` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY `uniqueKey` (`resource_id`,`resource_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `engine4_sesbasic_integrateothermodules`;
CREATE TABLE IF NOT EXISTS `engine4_sesbasic_integrateothermodules` (
  `integrateothermodule_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(64) NOT NULL,
  `type` varchar(64) NOT NULL,
  `content_type` varchar(64) NOT NULL,
  `content_type_photo` varchar(64) NOT NULL,
  `content_id` varchar(64) NOT NULL,
  `content_id_photo` varchar(64) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`integrateothermodule_id`),
  UNIQUE KEY `content_type` (`type`,`content_type`,`content_id`),
  KEY `module_name` (`module_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sesbasic_saves`
--

DROP TABLE IF EXISTS `engine4_sesbasic_saves`;
CREATE TABLE IF NOT EXISTS `engine4_sesbasic_saves` (
`save_id` int(11) unsigned NOT NULL auto_increment,
`resource_type` varchar(64) NOT NULL,
`resource_id` INT( 11 ) NOT NULL ,
`poster_id` INT( 11 ) NOT NULL ,
`poster_type` varchar(64) NOT NULL,
`creation_date` datetime NOT NULL,
 PRIMARY KEY (`save_id`),
 KEY `resource_type` (`resource_type`, `resource_id`),
 KEY `poster_type` (`poster_type`, `poster_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `engine4_sesbasic_instagram`;
CREATE TABLE IF NOT EXISTS `engine4_sesbasic_instagram` (
  `instagram_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` INT(11) NOT NULL,
  `instagram_uid` varchar(45) NOT NULL,
  `access_token` varchar(255) NOT NULL DEFAULT '',
  `code` varchar(255) NOT NULL DEFAULT '',
  `expires` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
   PRIMARY KEY (`instagram_id`),
   UNIQUE KEY `instagram_uid` (`instagram_uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `engine4_sesbasic_plugins`;
CREATE TABLE IF NOT EXISTS `engine4_sesbasic_plugins` (
  `plugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(64) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` text NULL,
  `current_version` varchar(32) NOT NULL,
  `site_version` varchar(32) NOT NULL,
  `category` varchar(64) NOT NULL,
  `pluginpage_link` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`plugin_id`),
  KEY `module_name` (`module_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;



DROP TABLE IF EXISTS `engine4_sesbasic_usergateways`;
CREATE TABLE IF NOT EXISTS `engine4_sesbasic_usergateways` (
  `usergateway_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(128)  NOT NULL,
  `description` text ,
  `enabled` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `plugin` varchar(128)  NOT NULL,
  `sponsorship` varchar(128)  NOT NULL,
  `config` mediumblob,
  `test_mode` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`usergateway_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sesbasic', 'sesbasic', 'Payment Gateways', '', '{"route":"sesbasic_extended", "module":"sesbasic", "controller":"index", "action":"account-details"}', 'user_settings', '', 20);
