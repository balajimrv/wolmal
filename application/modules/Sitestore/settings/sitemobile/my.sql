
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
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
('sitestore_gutter', 'standard', 'Store Profile Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
( 'sitestore_main_home', 'sitestore', 'Stores Home', 'Sitestore_Plugin_Menus::canViewSitestores', '{"route":"sitestore_general","action":"home"}', 'sitestore_main', '', '10', 1, 1),
( 'sitestore_main_browse', 'sitestore', 'Browse Stores', 'Sitestore_Plugin_Menus::canViewSitestores', '{"route":"sitestore_general","action":"index"}', 'sitestore_main', '', '20', 1, 1),
('sitestore_gutter_share', 'sitestore', 'Share Store', 'Sitestore_Plugin_Menus', '', 'sitestore_gutter', '',5, 1, 1),
('sitestore_gutter_messageowner', 'sitestore', 'Message Owner', 'Sitestore_Plugin_Menus', '', 'sitestore_gutter', '', 6,1, 1),
('sitestore_gutter_tfriend', 'sitestore', 'Tell a friend', 'Sitestore_Plugin_Menus', '', 'sitestore_gutter', '', 7, 1, 1),
('sitestore_gutter_claim', 'sitestore', 'Claim this Store', 'Sitestore_Plugin_Menus', '', 'sitestore_gutter', '', 13, 1, 1),
('sitestore_gutter_report', 'sitestore', 'Report Store', 'Sitestore_Plugin_Menus', '', 'sitestore_gutter', '', 16, 1, 1);

INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitestore_index_home', 'Sitestore_Form_Search', 'search', '{"type":"sitestore_store"}', '', '{"route":"sitestore_general","action":"index"}'),
('sitestore_index_index', 'Sitestore_Form_Search', 'search', '{"type":"sitestore_store"}', '', ''),
('sitestore_index_manage', 'Sitestore_Form_ManageSearch', 'search', '{"type":"sitestore_store"}', '', '');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitestore_index_view', 'sitestore_gutter', 'sitestore_store');


INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES
('sitestorealbum', 0, 0, 0, 0),
('sitestoreform', 0, 0, 0, 0),
('sitestoreoffer', 0,0, 0, 0),
('sitestoreproduct', 0, 0, 0, 0),
('sitestorereview', 0, 0, 0, 0),
('sitestorevideo', 0, 0, 0, 0);



INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`,`type`,`title`) VALUES
('sitestore_manage_mobile_main','standard','Store Manage Main Menu');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`,`module`,`label`,`plugin`,`params`,`menu`,`submenu`,`custom`,`order`,`enable_mobile`,`enable_tablet`) VALUES
('sitestore_manage_mobile_main_orders','sitestore','Orders','Sitestore_Plugin_Menus','','sitestore_manage_mobile_main','',0,1,1,1),
('sitestore_manage_mobile_main_products','sitestore','Products','Sitestore_Plugin_Menus','','sitestore_manage_mobile_main','',0,2,1,1),
('sitestore_manage_mobile_main_shops','sitestore','Shops','Sitestore_Plugin_Menus','','sitestore_manage_mobile_main','',0,3,1,1);

-- Mobile Manage Stores
UPDATE `engine4_sitemobile_content` SET `name` = 'sitestore.sitemobile-custom-managestores',`module` = 'sitestore'
WHERE `name` = 'core.content' AND `page_id` = (SELECT `page_id` FROM `engine4_sitemobile_pages` WHERE `name` = 'sitestore_index_manage');

DELETE FROM `engine4_sitemobile_content`
WHERE (`name` = 'sitemobile.sitemobile-navigation' OR `name` = 'sitemobile.sitemobile-advancedsearch')
AND `page_id` = (SELECT `page_id` FROM `engine4_sitemobile_pages` WHERE `name` = 'sitestore_index_manage');

-- Mobile Manage Products & Orders
UPDATE `engine4_sitemobile_pages` SET `name` = 'sitestoreproduct_product_manage' WHERE `displayname` = 'Stores - Manage Store Products';
UPDATE `engine4_sitemobile_pages` SET `name` = 'sitestoreproduct_index_manage-order' WHERE `displayname` = 'Stores - Manage Store Orders';

UPDATE `engine4_sitemobile_content` SET `module` = 'sitemobile'
WHERE `name` = 'sitemobile.sitemobile-navigation' AND `page_id` = (SELECT `page_id` FROM `engine4_sitemobile_pages` WHERE `name` = 'sitestoreproduct_product_manage');

UPDATE `engine4_sitemobile_content` SET `module` = 'sitemobile'
WHERE `name` = 'sitemobile.sitemobile-navigation' AND `page_id` = (SELECT `page_id` FROM `engine4_sitemobile_pages` WHERE `name` = 'sitestoreproduct_index_manage-order');

-- Mobile Create Product
UPDATE `engine4_sitemobile_pages` SET `name` = 'sitestoreproduct_index_create-mobile' WHERE `displayname` = 'Stores - Create Store Product';
UPDATE `engine4_sitemobile_content` SET `module` = 'core'
WHERE `name` = 'core.content' AND `page_id` = (SELECT `page_id` FROM `engine4_sitemobile_pages` WHERE `name` = 'sitestoreproduct_index_create-mobile');