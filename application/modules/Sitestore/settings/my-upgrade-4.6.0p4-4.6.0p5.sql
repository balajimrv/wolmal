UPDATE `engine4_activity_notificationtypes` SET `module` = 'sitestore' WHERE `engine4_activity_notificationtypes`.`type` = 'sitestoreproduct_create' LIMIT 1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('sitestoreproduct_gutter_opinionfriend', 'sitestoreproduct', 'Ask for an Opinion', 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterOpinionfriend', '', 'sitestoreproduct_gutter', '', '1', '0', '5');

DELETE FROM `engine4_authorization_permissions` WHERE `engine4_authorization_permissions`.`name` = 'auth_sspcreate';

DELETE FROM `engine4_authorization_permissions` WHERE `engine4_authorization_permissions`.`name` = 'sspcreate';

ALTER TABLE `engine4_sitestore_stores` DROP `substore`;