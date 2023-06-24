INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sitestore_admin_main_global_widget', 'sitestore', 'Widget Settings', '', '{"route":"admin_default","module":"sitestoreproduct","controller":"settings","action":"widget-settings"}', 'sitestore_admin_main_settings', '', 15);

UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$subject} opened a store:' WHERE `engine4_activity_actiontypes`.`type` = 'sitestore_new' LIMIT 1 ;

ALTER TABLE  `engine4_sitestore_hideprofilewidgets` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_addresses` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_cartproducts` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_carts` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_order_addresses` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_order_comments` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_order_downloads` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_order_products` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_ordercheques` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_orders` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_otherinfo` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_payment_requests` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_regions` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_remaining_amounts` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_shipping_methods` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_shipping_trackings` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_taxes` ENGINE = INNODB;
ALTER TABLE  `engine4_sitestoreproduct_tax_rates` ENGINE = INNODB;