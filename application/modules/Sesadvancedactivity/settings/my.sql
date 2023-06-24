/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: my.sql.tpl  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sesadvancedactivity', 'Advanced News & Activity Feeds Plugin', 'Advanced News & Activity Feeds Plugin', '4.9.0', 1, 'extra');

INSERT IGNORE INTO `engine4_sesbasic_plugins` (`module_name`, `title`, `description`, `current_version`, `site_version`, `category`, `pluginpage_link`) VALUES  ('sesadvancedactivity', 'Advanced News & Activity Feeds Plugin', 'Advanced News & Activity Feeds Plugin', '4.9.0', '4.9.0', 'plugin', 'https://www.socialenginesolutions.com/social-engine/advanced-news-activity-feeds-plugin/');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_settings_sesadvancedactivity', 'sesadvancedactivity', 'SES - Advanced News & Activity Feeds', '', '{"route":"admin_default","module":"sesadvancedactivity","controller":"settings","action":"index"}', 'core_admin_main_plugins', '', 1),
('sesadvancedactivity_admin_main_settings', 'sesadvancedactivity', 'Global Settings', '', '{"route":"admin_default","module":"sesadvancedactivity","controller":"settings","action":"index"}', 'sesadvancedactivity_admin_main', '', 1),
('sesadvancedactivity_index_onthisday', 'sesadvancedactivity', 'Memories On This Day', 'Sesadvancedactivity_Plugin_Menus::enableonthisday', '{"route":"sesadvancedactivity_onthisday","icon":"application/modules/Sesadvancedactivity/externals/images/onthisday.png"}', 'user_home', '', 6);