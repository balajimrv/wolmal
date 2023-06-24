INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('sitestore', 'Stores / Marketplace - Ecommerce', 'Stores / Marketplace - Ecommerce', '4.9.1p1', 1, 'extra'),

('sitestoreadmincontact', 'Stores / Marketplace - Ecommerce Contact Store Owners Extension', 'Stores / Marketplace - Ecommerce Contact Store Owners Extension', '4.9.0', 1, 'extra'),

('sitestorealbum', 'Stores / Marketplace - Ecommerce Photo Albums Extension', 'Stores / Marketplace - Ecommerce Photo Albums Extension', '4.9.0', 1, 'extra'),

('sitestoreform', 'Stores / Marketplace - Ecommerce Form Extension', 'Stores / Marketplace - Ecommerce Form Extension', '4.9.0', 1, 'extra'),

('sitestoreinvite', 'Stores / Marketplace - Ecommerce Inviter Extension', 'Stores / Marketplace - Ecommerce Inviter Extension', '4.9.0', 1, 'extra'),

('sitestoreoffer', 'Stores / Marketplace - Ecommerce Offers Extension', 'Stores / Marketplace - Ecommerce Offers Extension', '4.9.0', 1, 'extra'),

('sitestoreproduct', 'Stores / Marketplace - Ecommerce Products Extension', 'Stores / Marketplace - Ecommerce Products Extension', '4.9.1p1', 1, 'extra'),

('sitestorereview', 'Stores / Marketplace - Ecommerce Reviews and Ratings Extension', 'Stores / Marketplace - Ecommerce Reviews and Ratings Extension', '4.9.0', 1, 'extra'),

('sitestoreurl', 'Stores / Marketplace - Ecommerce Short Store URL Extension', 'Stores / Marketplace - Ecommerce Short Store URL Extension', '4.9.0', 1, 'extra'),

('sitestorevideo', 'Stores / Marketplace - Ecommerce Videos Extension', 'Stores / Marketplace - Ecommerce Videos Extension', '4.9.1p1', 1, 'extra'),

('sitestorelikebox', 'Stores / Marketplace - Ecommerce Embeddable Badges, Like Box Extension', 'Stores / Marketplace - Ecommerce Embeddable Badges, Like Box Extension', '4.9.0', 1, 'extra');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitestoreproduct_index_storehome', 'sitestoreproduct', 'Stores Home', 'Sitestoreproduct_Plugin_Menus::canViewSitestoreproducts', '{"route":"sitestore_general","action":"home"}', 'sitestoreproduct_main', NULL, '1', '0', 1),
('sitestoreproduct_index_storebrowse', 'sitestoreproduct', 'Browse Stores', 'Sitestoreproduct_Plugin_Menus::canViewSitestoreproducts', '{"route":"sitestore_general","action":"index"}', 'sitestoreproduct_main', NULL, '1', '0', 5),
('sitestore_main_location', 'sitestore', 'Browse Locations',  'Sitestore_Plugin_Menus::canViewSitestores', '{"route":"sitestore_general","action":"map"}', 'sitestoreproduct_main', '', 0, 0, 6),
('sitestoreproduct_main_home', 'sitestoreproduct', 'Products Home', 'Sitestoreproduct_Plugin_Menus::canViewSitestoreproducts', '{"route":"sitestoreproduct_general","action":"home"}', 'sitestoreproduct_main', NULL, '1', '0', 10),
('sitestoreproduct_main_browse', 'sitestoreproduct', 'Browse Products', 'Sitestoreproduct_Plugin_Menus::canViewSitestoreproducts', '{"route":"sitestoreproduct_general","action":"index"}', 'sitestoreproduct_main', NULL, '1', '0', 15),
('sitestoreproduct_index_pinboard', 'sitestoreproduct', 'Products Pinboard', '', '{"route":"sitestoreproduct_general","action":"pinboard"}', 'sitestoreproduct_main', NULL, '1', '0', 20),
('sitestoreproduct_main_categories', 'sitestoreproduct', 'Categories',  'Sitestoreproduct_Plugin_Menus::canViewCategories', '{"route":"sitestoreproduct_review_categories","action":"categories"}', 'sitestoreproduct_main', '', 1, 0, 25),
('sitestoreproduct_index_manageCart', 'sitestoreproduct', 'My Cart', NULL, '{"route":"sitestoreproduct_product_general", "action":"cart"}', 'sitestoreproduct_main', NULL, 1, 0, 35),
('sitestore_main_offer', 'sitestoreoffer', 'Offers', 'Sitestoreoffer_Plugin_Menus::canViewOffers', '{"route":"sitestoreoffer_home","action":"home"}', 'sitestoreproduct_main', NULL, 1, 0, 36),
('sitestoreproduct_main_reviews', 'sitestoreproduct', 'Browse Reviews', 'Sitestoreproduct_Plugin_Menus::canViewBrosweReview', '{"route":"sitestoreproduct_review_browse", "action":"browse"}', 'sitestoreproduct_main', '', 1, 0, 40),
('sitestoreproduct_main_wishlists', 'sitestoreproduct', 'Wishlists', 'Sitestoreproduct_Plugin_Menus::canViewWishlist', '{"route":"sitestoreproduct_wishlist_general","action":"browse"}', 'sitestoreproduct_main', '', 1, 0, 45),
('sitestoreproduct_main_editors', 'sitestoreproduct', 'Editors', 'Sitestoreproduct_Plugin_Menus::canViewEditors', '{"route":"sitestoreproduct_editor_general","action":"home"}', 'sitestoreproduct_main', '', 1, 0, 50),
('sitestoreproduct_main_account', 'sitestoreproduct', 'My Store Account', 'Sitestoreproduct_Plugin_Menus::isLogin', '{"route":"sitestoreproduct_general", "action":"account"}', 'sitestoreproduct_main', NULL, 1, 0, 30),
('sitestore_main_claim', 'sitestore', 'Claim a Store', 'Sitestore_Plugin_Menus::canViewClaims', '{"route":"sitestore_claimstores"}', 'sitestoreproduct_main', NULL, 1, 0, 37),

('user_profile_wishlist', 'sitestoreproduct', 'Wishlists', 'Sitestoreproduct_Plugin_Menus::userProfileWishlist', '', 'user_profile', '', '1', '0', 999),
('mobi_browse_sitestore', 'sitestore', 'Stores', 'Sitestore_Plugin_Menus::canViewSitestores', '{"route":"sitestore_general","action":"home"}', 'mobi_browse', '', 1, 0, 5);

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('modules.likebox','a:10:{i:0;s:13:"sitestorealbum";i:1;s:12:"sitestorepoll";i:2;s:16:"sitestoredocument";i:3;s:13:"sitestoreoffer";i:4;s:13:"sitestorevideo";i:5;s:13:"sitestoreevent";i:6;s:12:"sitestorenote";i:7;s:18:"sitestorediscussion";i:8;s:13:"sitestoremusic";i:9;s:6:"review";}');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sitestore_home_wishlist', 'sitestore', 'My Wishlists', 'Sitestoreproduct_Plugin_Menus::myWishlist', '', 'user_home', '', 999),
('sitestoreproduct_gutter_opinionfriend', 'sitestoreproduct', 'Ask for an Opinion', 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterOpinionfriend', '', 'sitestoreproduct_gutter', '', '5');

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`)VALUES 
('sitestoreproduct_create', 'sitestore', '{item:$subject} created a new product {item:$object}.', '0', '', '1');

-- INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `custom`,`order` )VALUES
-- ('sitestore_main_document', 'sitestoredocument', 'Documents', 'Sitestoredocument_Plugin_Menus::canViewDocuments', '{"route":"sitestoredocument_home","action":"home"}', 'sitestore_main', '', 1,0, '999');
-- 
-- INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
-- ('sitestore_sitereview_gutter_create_1', 'sitestore', 'Post New Product', 'Sitestore_Plugin_Menus::sitestoresitereviewGutterCreate', '{"route":"sitereview_general_listtype_1", "action":"create", "listing_id": "1", "class":"buttonlink item_icon_sitereview_listtype_1"}', '', '', 1, 0, 999);


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sitestore_admin_main_commission', 'sitestore', 'Commissions', 'Sitestoreproduct_Plugin_Menus::showAdminCommissionTab', '{"route":"admin_default","module":"sitestoreproduct","controller":"manage", "action":"commission"}', 'sitestore_admin_main', '', 19);


DROP TABLE IF EXISTS `engine4_sitestoreproduct_importfiles`;
CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_importfiles` (
  `importfile_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `first_import_id` int(11) unsigned NOT NULL,
  `last_import_id` int(11) unsigned NOT NULL,
  `current_import_id` int(11) unsigned NOT NULL,
  `first_product_id` int(11) unsigned NOT NULL,
  `last_product_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `view_privacy` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `comment_privacy` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `store_id` int(11) NOT NULL,
  PRIMARY KEY (`importfile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `engine4_sitestoreproduct_imports`;
CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_imports` (
  `import_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `product_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `product_type` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `subcategory` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `subsubcategory` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `price` decimal(16,2) unsigned DEFAULT '0.00',
  `weight` float unsigned DEFAULT '0',
  `in_stock` int(11) unsigned DEFAULT '0',
  `store_id` int(11) NOT NULL,
  `img_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `section` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`import_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `engine4_sitestoreproduct_printingtags`;
CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_printingtags` (
  `printingtag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(255) NOT NULL,
  `height` float NOT NULL,
  `width` float NOT NULL,
  `status` tinyint(4) NOT NULL,
  `store_id` int(11) NOT NULL,
  `title` smallint(2) NOT NULL,
  `category` smallint(2) NOT NULL,
  `price` smallint(2) NOT NULL,
  `qr` smallint(2) NOT NULL,
  `font_settings` text NOT NULL,
  `coordinates` text NOT NULL,
  PRIMARY KEY (`printingtag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `engine4_sitestoreproduct_documents`;
CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_documents` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `status` smallint(6) NOT NULL,
  `product_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `filename` text NOT NULL,
  `file_id` int(11) NOT NULL,
  `privacy` smallint(6) NOT NULL,
  `approve` smallint(6) NOT NULL,
  PRIMARY KEY (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `engine4_sitestore_packages_planmaps`;
CREATE TABLE IF NOT EXISTS `engine4_sitestore_packages_planmaps` (
  `planmap_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  PRIMARY KEY (`planmap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `engine4_sitestoreproduct_order_downpayments`;
CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_order_downpayments` (
  `orderdownpayment_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `gateway_id` smallint(6) NOT NULL,
  `cheque_id` int(10) unsigned NOT NULL DEFAULT '0',
  `gateway_profile_id` varchar(128) DEFAULT NULL,
  `payment` float unsigned NOT NULL,
  `payment_status` varchar(64) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`orderdownpayment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `engine4_sitestore_otherinfo`;
CREATE TABLE IF NOT EXISTS `engine4_sitestore_otherinfo` (
  `store_id` int(11) NOT NULL,
  `terms_conditions` text,
  PRIMARY KEY (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `engine4_sitestoreoffer_ordercoupons`;
CREATE TABLE IF NOT EXISTS `engine4_sitestoreoffer_ordercoupons` (
  `ordercoupon_id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` tinyint(4) NOT NULL,
  `buyer_id` tinyint(4) NOT NULL,
  `store_id` tinyint(4) NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`ordercoupon_id`),
  KEY `coupon_id` (`coupon_id`,`buyer_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)VALUES (
'sitestoredocument_admin_sub_settings', 'sitestore', 'Stores', '', '{"route":"admin_default","module":"sitestoredocument","controller":"settings"}', 'sitestore_document_admin_main', NULL , '1', '0', '1'), 
('sitestoreproduct_document_admin_sub_settings', 'sitestoreproduct', 'Products', '', '{"route":"admin_default","module":"sitestoreproduct","controller":"document"}', 'sitestore_document_admin_main', NULL , '1', '0', '2');

-- INSERT IGNORE INTO `engine4_core_menuitems` ( `name` , `module` , `label` , `plugin` , `params` , `menu` , `submenu` , `enabled` , `custom`,`order` )VALUES
-- ('sitestore_document_admin_main_settings', 'sitestore', 'Documents', 'Sitestoreproduct_Plugin_Menus::makeDocumentUrl', '', 'sitestore_admin_main', '', 1, 0, '50');

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`)VALUES 
  ('sitestore_dashboard', 'standard', 'Stores - Store Dashboard Menu', '999');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('sitestore_dashboard_getstarted', 'sitestore', 'Get Started', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 1),
('sitestore_dashboard_editinfo', 'sitestore', 'Edit Info', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 2),
('sitestore_dashboard_profileinfo', 'sitestore', 'Profile Info', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 3),
('sitestore_dashboard_profilepicture', 'sitestore', 'Profile Picture', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 4),
('sitestore_dashboard_manageproducts', 'sitestore', 'Manage Products', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 5),
('sitestore_dashboard_managesections', 'sitestore', 'Manage Sections', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 6),
('sitestore_dashboard_manageorders', 'sitestore', 'Manage Orders', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 7),
('sitestore_dashboard_shippingmethods', 'sitestore', 'Shipping Methods', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 8),
('sitestore_dashboard_taxes', 'sitestore', 'Taxes', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 9),
('sitestore_dashboard_paymentaccount', 'sitestore', 'Payment Account', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 10),
('sitestore_dashboard_paymentmethod', 'sitestore', 'Payment Method', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 11),
('sitestore_dashboard_paymentrequests', 'sitestore', 'Payment Requests', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 12),
('sitestore_dashboard_yourbill', 'sitestore', 'Your Bill', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 13),
('sitestore_dashboard_transactions', 'sitestore', 'Transactions', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 14),
('sitestore_dashboard_salesstatistics', 'sitestore', 'Products Sales Statistics', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 15),
('sitestore_dashboard_graphstatistics', 'sitestore', 'Sales Graph Statistics', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 16),
('sitestore_dashboard_salesreports', 'sitestore', 'Sales Reports', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 17),
('sitestore_dashboard_overview', 'sitestore', 'Overview', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 18),
('sitestore_dashboard_contact', 'sitestore', 'Contact Details', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 19),
('sitestore_dashboard_locations', 'sitestore', 'Locations', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 20),
('sitestore_dashboard_apps', 'sitestore', 'Apps', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 21),
('sitestore_dashboard_marketing', 'sitestore', 'Marketing', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 22),
('sitestore_dashboard_managenotifications', 'sitestore', 'Manage Notifications', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 23),
('sitestore_dashboard_manageadmins', 'sitestore', 'Manage Admins', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 24),
('sitestore_dashboard_featuredadmins', 'sitestore', 'Featured Admins', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 25),
('sitestore_dashboard_editlayout', 'sitestore', 'Edit Layout', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0,26),
('sitestore_dashboard_importproducts', 'sitestore', 'Import Products', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 27),
('sitestore_dashboard_terms_conditions', 'sitestore', 'Terms & Conditions', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 28),
('sitestore_dashboard_editstyle', 'sitestore', 'Edit Style', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 29),
('sitestore_dashboard_packages', 'sitestore', 'Packages', 'Sitestore_Plugin_Dashboardmenus', '', 'sitestore_dashboard', '', 1, 0, 30);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`)VALUES 
('sitestoreproduct_order_place_to_admin', 'sitestoreproduct', '[host],[store_name],[store_title],[order_invoice],[order_id],[order_no]');

-- ------------------------------------------------------
--
-- Dumping data for table `engine4_core_menuitems`
--
UPDATE `engine4_core_menuitems`
SET `params` = '{"route":"sitestoreproduct_general","action":"home","icon":"fa-shopping-basket"}'
WHERE `name` = 'core_main_sitestoreproduct';

--
-- Change the Commentable & Shareable values
--
UPDATE engine4_activity_actiontypes SET commentable=3,shareable=3 WHERE type='comment_sitestore_store' and module='sitestore';

UPDATE engine4_activity_actiontypes SET commentable=3,shareable=3 WHERE (type='comment_sitestoreproduct_photo' or type = 'comment_sitestoreproduct_product' or type = 'comment_sitestoreproduct_review' or type = 'comment_sitestoreproduct_video') and module='sitestoreproduct';