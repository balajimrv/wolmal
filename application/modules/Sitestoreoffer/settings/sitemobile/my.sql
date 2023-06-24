
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

INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES ('sitestoreoffer_profile', 'standard', 'Store Coupon Profile Options Menu', '999');

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitestore_main_offer', 'sitestoreoffer', 'Offers', 'Sitestoreoffer_Plugin_Menus::canViewOffers', '{"route":"sitestoreoffer_browse","action":"browse"}', 'sitestore_main', '','0', '50', 1, 1),
('sitestoreoffer_add', 'sitestoreoffer', 'Add an Coupon', 'Sitestoreoffer_Plugin_Menus', '', 'sitestoreoffer_profile', NULL, '0', '1', '1', '1'),
('sitestoreoffer_edit', 'sitestoreoffer', 'Edit Coupon', 'Sitestoreoffer_Plugin_Menus', '', 'sitestoreoffer_profile', NULL, '0', '2', '1', '1'),
('sitestoreoffer_delete', 'sitestoreoffer', 'Delete Coupon', 'Sitestoreoffer_Plugin_Menus', '', 'sitestoreoffer_profile', NULL, '0', '3', '1', '1'),
('sitestoreoffer_share', 'sitestoreoffer', 'Share', 'Sitestoreoffer_Plugin_Menus', '', 'sitestoreoffer_profile', NULL, '0', '5', '1', '1'),
('sitestoreoffer_report', 'sitestoreoffer', 'Report', 'Sitestoreoffer_Plugin_Menus', '', 'sitestoreoffer_profile', NULL, '0', '6', '1', '1');

-- --------------------------------------------------------

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitestoreoffer_index_browse', 'Sitestoreoffer_Form_Search', 'search_offer', '', '', '');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES 
('sitestoreoffer_index_view', 'sitestoreoffer_profile', 'sitestoreoffer_offer');