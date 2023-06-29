INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  
('core_admin_main_plugins_sescustomize', 'sescustomize', 'SES - Customize', '', '{"route":"admin_default","module":"sescustomize","controller":"settings"}', 'core_admin_main_plugins', '', 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sescustomize_admin_main_settings', 'sescustomize', 'Global Settings', '', '{"route":"admin_default","module":"sescustomize","controller":"settings"}', 'sescustomize_admin_main', '', 1),
('sescustomize_admin_main_manage', 'sescustomize', 'Manage Members', '', '{"route":"admin_default","module":"sescustomize","controller":"manage"}', 'sescustomize_admin_main', '', 2),
('sescustomize_admin_main_transaction', 'sescustomize', 'Transactions', '', '{"route":"admin_default","module":"sescustomize","controller":"transaction"}', 'sescustomize_admin_main', '', 2),
('sescustomize_admin_main_trans', 'sescustomize', 'Payment Requests', '', '{"route":"admin_default","module":"sescustomize","controller":"transaction"}', 'sescustomize_admin_main_transaction', '', 1),
('sescustomize_admin_main_tranmade', 'sescustomize', 'Manage Payment Made', '', '{"route":"admin_default","module":"sescustomize","controller":"transaction","action":"payment-made"}', 'sescustomize_admin_main_transaction', '', 2),
('sescustomize_admin_main_ebbb', 'sescustomize', 'Eb/BB Total', '', '{"route":"admin_default","module":"sescustomize","controller":"manage","action":"ebvalue"}', 'sescustomize_admin_main', '', 10);

DROP TABLE IF EXISTS `engine4_sescustomize_bbvalues`;
CREATE TABLE IF NOT EXISTS `engine4_sescustomize_bbvalues` (
  `bbvalue_id` int(11) unsigned NOT NULL auto_increment,
  `value` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
   PRIMARY KEY (`bbvalue_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `engine4_sescustomize_reedemrequests`;
CREATE TABLE IF NOT EXISTS  `engine4_sescustomize_reedemrequests` (
  `reedemrequest_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `amount` text NOT NULL,
  `note` text,
  `bank_name` varchar(255) NOT NULL,
  `ifsc_code` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `account_holder_name` varchar(255) NOT NULL,
  `monile_number` varchar(255) NOT NULL,
  `admin_note` text,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`reedemrequest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `engine4_sescustomize_fbvalues` (
  `fbvalue_id` int(11) NOT NULL AUTO_INCREMENT,
  `total` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('redeem','insert','bank') NOT NULL COMMENT 'redeem = withdraw,insert = earn',
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`fbvalue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

#{"route":"sescustomize_bridges","module":"sescustomize","controller":"index","action":"bridges"}
ALTER TABLE `engine4_sesbasic_bridges` ADD `order_id` INT(11) NOT NULL DEFAULT '0' AFTER `bridge_id`;

ALTER TABLE `engine4_sitestoreproduct_order_products` ADD `gst_rax` VARCHAR(255) NOT NULL DEFAULT '0' AFTER `field_id`;

ALTER TABLE `engine4_sitestoreproduct_products` ADD `gst_title` VARCHAR(255) NOT NULL AFTER `location`, ADD `gst_tax` VARCHAR(255) NOT NULL DEFAULT '0' AFTER `gst_title`;
ALTER TABLE `engine4_sitestoreproduct_orders` ADD `discount_value` VARCHAR(255) NOT NULL DEFAULT '0' AFTER `payment_split`;




CREATE TABLE `engine4_sesbasic_pointcounts` (
  `pointcount_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` varchar(255) NOT NULL DEFAULT '0',
  `date` varchar(45) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `engine4_sesbasic_pointcounts`
  ADD PRIMARY KEY (`pointcount_id`),
  ADD UNIQUE KEY `Unique` (`user_id`,`date`);
  
ALTER TABLE `engine4_sesbasic_pointcounts`
  MODIFY `pointcount_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

ALTER TABLE `engine4_sescustomize_reedemrequests` ADD `pan_no` VARCHAR(255) NOT NULL AFTER `status`, ADD `pan_name` VARCHAR(255) NOT NULL AFTER `pan_no`, ADD `pan_dob` VARCHAR(255) NOT NULL AFTER `pan_name`;


INSERT INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ('sescustomize_reedemrequest', 'sescustomize', '{item:$subject} {var:$title} your payment request {var:$itemurl}.', '0', '', '1');