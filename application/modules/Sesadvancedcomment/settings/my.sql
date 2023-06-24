/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: my.sql.tpl 2017-01-19 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sesadvancedcomment', 'SES - Advanced Nested Comments with Attachments Plugin', 'SES - Advanced Nested Comments with Attachments Plugin', '4.9.0p1', 1, 'extra') ;

INSERT IGNORE INTO `engine4_sesbasic_plugins` (`module_name`, `title`, `description`, `current_version`, `site_version`, `category`, `pluginpage_link`) VALUES  ('sesadvancedcomment', 'SES - Advanced Nested Comments with Attachments Plugin', 'SES - Advanced Nested Comments with Attachments Plugin', '4.9.0p1', '4.9.0p1', 'plugin', 'https://www.socialenginesolutions.com/social-engine/advanced-nested-comments-with-attachments-plugin/');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_settings_sesadvancedcomment', 'sesadvancedcomment', 'SES - Advanced Nested Comments with Attachments', '', '{"route":"admin_default","module":"sesadvancedcomment","controller":"settings","action":"index"}', 'core_admin_main_plugins', '', 4),
('sesadvancedcomment_admin_main_settings', 'sesadvancedcomment', 'Global Settings', '', '{"route":"admin_default","module":"sesadvancedcomment","controller":"settings","action":"index"}', 'sesadvancedcomment_admin_main', '', 1);