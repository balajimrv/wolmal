INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sespymk', 'SES - People You May Know Plugin', 'SES - People You May Know Plugin', '4.9.0', 1, 'extra') ;

INSERT IGNORE INTO `engine4_sesbasic_plugins` (`module_name`, `title`, `description`, `current_version`, `site_version`, `category`, `pluginpage_link`) VALUES  ('sespymk', 'SES - People You May Know Plugin', 'SES - People You May Know Plugin', '4.9.0', '4.9.0', 'plugin', '');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_sespymk', 'sespymk', 'SES - People You May Know', '', '{"route":"admin_default","module":"sespymk","controller":"settings"}', 'core_admin_main_plugins', '', 999),
('sespymk_admin_main_settings', 'sespymk', 'Global Settings', '', '{"route":"admin_default","module":"sespymk","controller":"settings"}', 'sespymk_admin_main', '', 1);