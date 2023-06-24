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

CREATE TABLE IF NOT EXISTS `engine4_sitestore_packages_planmaps` (
  `planmap_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  PRIMARY KEY (`planmap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

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

CREATE TABLE IF NOT EXISTS `engine4_sitestore_otherinfo` (
  `store_id` int(11) NOT NULL,
  `terms_conditions` text,
  PRIMARY KEY (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
