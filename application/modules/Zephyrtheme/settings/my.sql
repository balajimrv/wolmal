-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menus`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('zephyrtheme', 'Zephyr Theme', 'Options for Zephyr Theme', '1.2.9', 1, 'extra') ;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_zephyrtheme', 'zephyrtheme', 'Zephyr Theme Options', '', '{"route":"admin_default","module":"zephyrtheme","controller":"general"}', 'core_admin_main', '', 999),
('zephyr_admin_main_general', 'zephyrtheme', 'General', '', '{"route":"admin_default","module":"zephyrtheme","controller":"general"}', 'zephyr_admin_main', '', 1),
('zephyr_admin_main_landing', 'zephyrtheme', 'Landing Page', '', '{"route":"admin_default","module":"zephyrtheme","controller":"landingpage"}', 'zephyr_admin_main', '', 2),
('zephyr_admin_main_header', 'zephyrtheme', 'Header', '', '{"route":"admin_default","module":"zephyrtheme","controller":"header"}', 'zephyr_admin_main', '', 3),
('zephyr_admin_main_footer', 'zephyrtheme', 'Footer', '', '{"route":"admin_default","module":"zephyrtheme","controller":"footer"}', 'zephyr_admin_main', '', 4),
('zephyr_admin_main_styling', 'zephyrtheme', 'Styling', '', '{"route":"admin_default","module":"zephyrtheme","controller":"styling"}', 'zephyr_admin_main', '', 5),
('zephyr_admin_main_typography', 'zephyrtheme', 'Typography', '', '{"route":"admin_default","module":"zephyrtheme","controller":"typography"}', 'zephyr_admin_main', '', 6);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes`
 (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
 ('profile_cover_update', 'zephyrtheme', '{item:$subject} has changed the profile cover.', 1, 5, 1, 1, 1, 1);