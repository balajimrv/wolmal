CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_store_gateways` (
  `storegateway_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `details` text,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`storegateway_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_storebills` (
  `storebill_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(10) unsigned NOT NULL,
  `amount` float unsigned NOT NULL,
  `remaining_amount` float NOT NULL,
  `message` text,
  `creation_date` datetime NOT NULL,
  `status` varchar(64) NOT NULL,
  `gateway_profile_id` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`storebill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_remaining_bills` (
  `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(11) unsigned NOT NULL,
  `remaining_bill` float unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `engine4_sitestore_stores` ADD `store_gateway` VARCHAR( 256 ) NULL;

ALTER TABLE `engine4_sitestoreproduct_orders` ADD `storebill_id` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `payment_request_id`;

ALTER TABLE `engine4_sitestoreproduct_transactions` CHANGE `sender_type` `sender_type` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 => user, 1 => admin, 2 => seller';

ALTER TABLE `engine4_sitestoreproduct_orders` ADD `non_payment_reason` TINYINT NOT NULL DEFAULT '0',
ADD `non_payment_message` TEXT NULL ;

ALTER TABLE `engine4_sitestoreproduct_orders` CHANGE `non_payment_message` `non_payment_seller_message` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ;

ALTER TABLE `engine4_sitestoreproduct_orders` ADD `non_payment_admin_message` TEXT NULL;

ALTER TABLE `engine4_sitestoreproduct_orders` CHANGE `non_payment_reason` `non_payment_seller_reason` TINYINT( 4 ) NOT NULL DEFAULT '0';

ALTER TABLE `engine4_sitestoreproduct_orders` ADD `non_payment_admin_reason` TINYINT NOT NULL DEFAULT '0' AFTER `non_payment_seller_message` ;

ALTER TABLE `engine4_sitestoreproduct_transactions` CHANGE `gateway_id` `gateway_id` TINYINT( 3 ) UNSIGNED NOT NULL COMMENT '1 => 2Checkout, 2 => PayPal, 3 => By Cheque, 4 => Cash on Delivery';

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`,`vars`) VALUES 
('sitestoreproduct_non_payment_order', 'sitestoreproduct', '[host],[store_name],[store_title],[object_link],[order_id],[order_no]'),
('sitestoreproduct_store_commission_reversal_action', 'sitestoreproduct', '[host],[store_name],[store_title],[object_link],[order_id],[order_no]'),
('sitestoreproduct_member_order_place_by_cod', 'sitestoreproduct', '[host],[store_name],[order_id]');

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES 
('sitestore.payment.for.orders', '1');

ALTER TABLE `engine4_sitestoreproduct_orders` ADD `direct_payment` TINYINT NOT NULL DEFAULT '0' COMMENT '0 => Order Payment to Site Admin, 1 => Order Payment to Seller';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sitestore_admin_main_commission', 'sitestore', 'Commissions', 'Sitestoreproduct_Plugin_Menus::showAdminCommissionTab', '{"route":"admin_default","module":"sitestoreproduct","controller":"manage", "action":"commission"}', 'sitestore_admin_main', '', 19);

UPDATE `engine4_core_menuitems` SET `plugin` = 'Sitestoreproduct_Plugin_Menus::showAdminPaymentRequestTab' WHERE `engine4_core_menuitems`.`name` ='sitestore_admin_main_payment' LIMIT 1 ;

ALTER TABLE `engine4_sitestoreproduct_otherinfo` CHANGE `bundle_product_info` `product_info` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;