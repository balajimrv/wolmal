
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: my.sql 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menus`
--

INSERT IGNORE INTO `engine4_sitemobile_menus` ( `name`, `type`, `title`, `order`) 
VALUES 
('sitestorealbum_profile', 'standard', 'Store Album Profile Options Menu', '999'),
('sitestorealbum_photo', 'standard', 'Store Album Photo View Options Menu', '999');
-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitestorealbum_viewAlbums', 'sitestorealbum', 'View Albums', 'Sitestorealbum_Plugin_Menus', '', 'sitestorealbum_profile', NULL, 0, 1, 1, 1),
('sitestorealbum_add', 'sitestorealbum', 'Add More Photos', 'Sitestorealbum_Plugin_Menus', '', 'sitestorealbum_profile', NULL, 0, 2, 1, 1),
('sitestorealbum_edit', 'sitestorealbum', 'Edit Album', 'Sitestorealbum_Plugin_Menus', '', 'sitestorealbum_profile', NULL, 0, 3, 1, 1),
('sitestorealbum_delete', 'sitestorealbum', 'Delete Album', 'Sitestorealbum_Plugin_Menus', '', 'sitestorealbum_profile', NULL, 0, 4, 1, 1),
 ('sitestorealbum_photo_edit', 'sitestorealbum', 'Edit', 'Sitestorealbum_Plugin_Menus', '', 'sitestorealbum_photo', NULL, '0', '1', '1', '1'),
 ('sitestorealbum_photo_delete', 'sitestorealbum', 'Delete', 'Sitestorealbum_Plugin_Menus', '', 'sitestorealbum_photo', NULL, '0', '2', '1', '1'),
('sitestorealbum_photo_share', 'sitestorealbum', 'Share', 'Sitestorealbum_Plugin_Menus', '', 'sitestorealbum_photo', NULL, '0', '3', '1', '1'),
('sitestorealbum_photo_report', 'sitestorealbum', 'Report', 'Sitestorealbum_Plugin_Menus', '', 'sitestorealbum_photo', NULL, '0', '4', '1', '1'),
('sitestorealbum_photo_profile', 'sitestorealbum', 'Make Store Profile Photo', 'Sitestorealbum_Plugin_Menus', '', 'sitestorealbum_photo', NULL, '0', '5', '1', '1');

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitestore_album_browse', 'Sitestorealbum_Form_Searchwidget', 'search_album', '', '', '');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitestore_album_view', 'sitestorealbum_profile', 'sitestore_album'),
('sitestore_photo_view', 'sitestorealbum_photo', 'sitestore_photo');
