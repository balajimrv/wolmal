INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sesalbum', 'Advanced Photos & Albums Plugin', '', '4.9.0', 1, 'extra') ;

INSERT IGNORE INTO `engine4_sesbasic_plugins` (`module_name`, `title`, `description`, `current_version`, `site_version`, `category`, `pluginpage_link`) VALUES  ('sesalbum', 'Advanced Photos & Albums Plugin', 'Advanced Photos & Albums Plugin', '4.9.0', '4.9.0', 'plugin', 'http://www.socialenginesolutions.com/social-engine/advanced-photos-albums-plugin/');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_sesalbum', 'sesalbum', 'SES - Advanced Photos & Albums', '', '{"route":"admin_default","module":"sesalbum","controller":"settings","action":"index"}', 'core_admin_main_plugins', '', 999),
('sesalbum_admin_main_settings', 'sesalbum', 'Global Settings', '', '{"route":"admin_default","module":"sesalbum","controller":"settings"}', 'sesalbum_admin_main', '', 1);
