
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

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('core_main_sitestoreproduct', 'sitestoreproduct', 'Stores',  'Sitestoreproduct_Plugin_Menus::canViewSitestoreproducts', '{"route":"sitestoreproduct_general", "action":"home"}', 'core_main', '', 36, 1, 1),
('sitestoreproduct_main_home', 'sitestoreproduct', 'Products Home', 'Sitestoreproduct_Plugin_Menus::canViewSitestoreproducts', '{"route":"sitestoreproduct_general","action":"home"}', 'sitestore_main', '', '30', 1, 1),
('sitestoreproduct_main_browse', 'sitestoreproduct', 'Browse Products', 'Sitestoreproduct_Plugin_Menus::canViewSitestoreproducts', '{"route":"sitestoreproduct_general","action":"index"}', 'sitestore_main','', '40', 1,1),
('sitestoreproduct_main_categories', 'sitestoreproduct', 'Categories', 'Sitestoreproduct_Plugin_Menus::canViewCategories', '{"route":"sitestoreproduct_review_categories","action":"categories"}', 'sitestore_main', '', '50', 1, 1),
('sitestoreproduct_main_reviews', 'sitestoreproduct', 'Browse Reviews', 'Sitestoreproduct_Plugin_Menus::canViewBrosweReview', '{"route":"sitestoreproduct_review_browse", "action":"browse"}', 'sitestore_main', '', '60', 1, 1),
('sitestoreproduct_main_wishlists', 'sitestoreproduct', 'Wishlists', 'Sitestoreproduct_Plugin_Menus::canViewWishlist', '{"route":"sitestoreproduct_wishlist_general","action":"browse"}', 'sitestore_main', '','70', 1, 1),
('sitestoreproduct_main_account', 'sitestoreproduct', 'My Store Account', 'Sitestoreproduct_Plugin_Menus::isLogin', '{"route":"sitestoreproduct_general","action":"manage-address"}', 'sitestore_main', '', '90', 1, 1);

 INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
 ('sitestore_account_main', 'standard', 'Sitestoreproduct Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitestoreproduct_account_wishlist', 'sitestoreproduct', 'My Wishlists', '', '{"route":"sitestoreproduct_wishlist_general","controller":"wishlist","action":"my-wishlists"}', 'sitestore_account_main', '', 1, 1, 1),
('sitestoreproduct_account_order', 'sitestoreproduct', 'My Orders', '', '{"route":"sitestoreproduct_product_general","action":"my-order"}', 'sitestore_account_main', '', 2, 1, 1),
('sitestoreproduct_account_storelike', 'sitestoreproduct', 'Stores I Like', 'Sitestore_Plugin_Menus::canCreateSitestores', '{"route":"sitestore_like","action":"mylikes"}', 'sitestore_account_main', '', 3, 1, 1),
('sitestoreproduct_account_address', 'sitestoreproduct', 'My Addresses', '', '{"route":"sitestoreproduct_general","action":"manage-address"}', 'sitestore_account_main', '', 4, 1, 1),
('sitestoreproduct_account_storemanage', 'sitestoreproduct', 'My Stores', '', '{"route":"sitestore_general","action":"manage"}', 'sitestore_account_main', '', 5, 1, 1);



INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitestoreproduct_index_index', 'Sitestoreproduct_Form_Search', 'search', '{"type":"sitestoreproduct_product"}', '', '{"route":"sitestoreproduct_general","action":"index"}'),
('sitestoreproduct_review_browse', 'Sitestoreproduct_Form_Review_Search', 'search', '{"type":"sitestoreproduct_review"}', '', '{"route":"sitestoreproduct_general","action":"index"}'),
('sitestoreproduct_wishlist_browse', 'Sitestoreproduct_Form_Wishlist_Search', 'search', '{"type":"sitestoreproduct_wishlist"}', '','{"route":"sitestoreproduct_wishlist_general","action":"browse"}');

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitestoreproduct_index_view', 'sitestoreproduct_profile', 'sitestoreproduct_product');

INSERT IGNORE INTO `engine4_sitemobile_menus` (`id`, `name`, `type`, `title`, `order`) VALUES (NULL, 'sitestoreproduct_profile', 'standard', 'Products - Profile Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('sitestoreproduct_gutter_messageowner', 'sitestoreproduct', 'Message Owner', 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterMessageowner', '', 'sitestoreproduct_profile', NULL, '0', '2', '1', '1'),
('sitestoreproduct_gutter_share', 'sitestoreproduct', 'Share', 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterShare', '', 'sitestoreproduct_profile', NULL, '0', '3', '1', '1'),
('sitestoreproduct_gutter_tfriend', 'sitestoreproduct', 'Tell a Friend', 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterTfriend', '', 'sitestoreproduct_profile', NULL, '0', '4', '1', '1'),
('sitestoreproduct_gutter_report', 'sitestoreproduct', 'Report', 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterReport', '', 'sitestoreproduct_profile', NULL, '0', '5', '1', '1'),
('sitestoreproduct_gutter_publish', 'sitestoreproduct', 'Publish', 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterPublish', '', 'sitestoreproduct_profile', NULL, '0', '6', '1', '1');

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`,`enable_mobile`,`enable_tablet`) VALUES
( 'user_profile_wishlist', 'sitestoreproduct', 'Wishlists', 'Sitestoreproduct_Plugin_Menus::userProfileWishlist', '', 'user_profile', '',  0, 999, 1, 1);


INSERT IGNORE INTO `engine4_sitemobile_menus` ( `name`, `type`, `title`, `order`) VALUES
( 'sitestoreproduct_wishlist_gutter', 'standard', 'Product: Wishlist Profile Options Menu', 999);

INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
( 'sitestoreproduct_wishlist_gutter_share', 'sitestoreproduct', 'Share', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_wishlist_gutter', '', 0, 4, 1, 1),
( 'sitestoreproduct_wishlist_gutter_tfriend', 'sitestoreproduct', 'Tell a Friend', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_wishlist_gutter', '', 0, 5, 1, 1),
( 'sitestoreproduct_wishlist_gutter_report', 'sitestoreproduct', 'Report','Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_wishlist_gutter', '', 0, 6, 1, 1);


INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES 
('sitestoreproduct_wishlist_profile', 'sitestoreproduct_wishlist_gutter', 'sitestoreproduct_wishlist');


INSERT IGNORE INTO `engine4_sitemobile_searchform` (`name`, `class`, `search_filed_name`, `params`, `script_render_file`, `action`) VALUES
('sitestoreproduct_review_browse', 'Sitestoreproduct_Form_Review_Search', 'search', '{"type":"sitestoreproduct_review"}', '', ''),
('sitestoreproduct_wishlist_browse', 'Sitestoreproduct_Form_Wishlist_Search', 'search', '', 'application/modules/Sitestoreproduct/views/sitemobile/scripts/searchform/wishlistSearch.tpl', ''),

('sitestoreproduct_index_manage', 'Sitestoreproduct_Form_Search', 'search', '', '', ''),
('sitestoreproduct_index_home', 'Sitestoreproduct_Form_Search', 'search', '', '', ''),
('sitestoreproduct_index_index', 'Sitestoreproduct_Form_Search', 'search', '', '', '');


INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitestoreproduct_topic_view', 'sitestoreproduct_topic', 'sitestoreproduct_topic');


INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('Sitestoreproduct_topic_watch', 'sitestoreproduct', 'Watch Topic', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_topic', NULL, '0', '1', '1', '1'), 
('Sitestoreproduct_topic_sticky', 'sitestoreproduct', 'Make Sticky', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_topic', NULL, '0', '2', '1', '1'),
('Sitestoreproduct_topic_open', 'sitestoreproduct', 'Open', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_topic', NULL, '0', '3', '1', '1'),
('Sitestoreproduct_topic_rename', 'sitestoreproduct', 'Rename', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_topic', NULL, '0', '4', '1', '1'),
('Sitestoreproduct_topic_delete', 'sitestoreproduct', 'Delete', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_topic', NULL, '0', '5', '1', '1');

INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`, `type`, `title`, `order`) VALUES 
('sitestoreproduct_topic', 'standard', 'Review Topic Options Menu', '999');

INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitestoreproduct_photo_view', 'sitestoreproduct_photo', 'sitestoreproduct_photo');


INSERT IGNORE INTO `engine4_sitemobile_navigation` 
(`name`, `menu`, `subject_type`) VALUES
('sitestoreproduct_review_view', 'sitestoreproduct_review', 'sitestoreproduct_review');

INSERT IGNORE INTO `engine4_sitemobile_menus` (`id`, `name`, `type`, `title`, `order`) VALUES (NULL, 'sitestoreproduct_review', 'standard', 'Products - Review Profile Options Menu', '999');

--
-- Dumping data for table `engine4_sitemobile_menuitems`
--

INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES 
('sitestoreproduct_review_update', 'sitestoreproduct', 'Update your Review', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_review', NULL, '0', '1', '1', '1'),
('sitestoreproduct_review_create', 'sitestoreproduct', 'Write a Review', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_review', NULL, '0', '2', '1', '1'),
('sitestoreproduct_review_share', 'sitestoreproduct', 'Share Review', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_review', NULL, '0', '3', '1', '1'),
('sitestoreproduct_review_email', 'sitestoreproduct', 'Email Review', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_review', NULL, '0', '4', '1', '1'),
('sitestoreproduct_review_delete', 'sitestoreproduct', 'Delete Review', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_review', NULL, '0', '5', '1', '1'),
('sitestoreproduct_review_report', 'sitestoreproduct', 'Report Review', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_review', NULL, '0', '6', '1', '1');

INSERT IGNORE INTO `engine4_sitemobile_menus` (`id`, `name`, `type`, `title`, `order`) VALUES (NULL, 'sitestoreproduct_photo', 'standard', 'Product Photo View Options Menu', '999');



INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
('sitestoreproduct_photo_share', 'sitestoreproduct', 'Share', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_photo', NULL, '0', '3', '1', '1'),
('sitestoreproduct_photo_report', 'sitestoreproduct', 'Report', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_photo', NULL, '0', '4', '1', '1'),
('sitestoreproduct_photo_profile', 'sitestoreproduct', 'Make Profile Photo', 'Sitestoreproduct_Plugin_Menus', '', 'sitestoreproduct_photo', NULL, '0', '5', '1', '1');
