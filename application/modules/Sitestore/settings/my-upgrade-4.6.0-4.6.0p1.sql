DELETE FROM `engine4_core_menuitems` WHERE `name` = 'sitestore_admin_main_global_url';
DELETE FROM `engine4_core_menuitems` WHERE `name` = 'sitestore_admin_main_import';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sitestore_admin_main_global_url', 'sitestore', 'Short Store URL', '', '{"route":"admin_default","module":"sitestoreurl","controller":"settings","action":"index"}', 'sitestore_admin_main', '', 29),
('sitestoreurl_admin_global_url', 'sitestoreurl', 'General Settings', '', '{"route":"admin_default","module":"sitestoreurl","controller":"settings","action":"index"}', 'sitestoreurl_admin_main', '', 1),
('sitestoreurl_admin_blockurl', 'sitestoreurl', 'Banned URLs', '', '{"route":"admin_default","module":"sitestoreurl","controller":"settings","action":"banningurl"}', 'sitestoreurl_admin_main', '', 2),
('sitestoreurl_admin_main_url', 'sitestoreurl', 'Stores with Banned URLs', '', '{"route":"admin_default","module":"sitestoreurl","controller":"settings","action":"storeurl"}', 'sitestoreurl_admin_main', '', 3);

UPDATE `engine4_core_menuitems` SET `label` = 'Forms' WHERE `engine4_core_menuitems`.`name` LIKE 'sitestore_admin_main_sitestoreform';
UPDATE `engine4_core_menuitems` SET `label` = 'Photo Albums' WHERE `engine4_core_menuitems`.`name` LIKE 'sitestore_admin_main_sitestorealbum';
UPDATE `engine4_core_menuitems` SET `label` = 'Reviews & Ratings' WHERE `engine4_core_menuitems`.`name` LIKE 'sitestore_admin_main_sitestorereview';

UPDATE `engine4_activity_notificationtypes` SET `module` = 'sitestore' WHERE `engine4_activity_notificationtypes`.`module` LIKE 'sitestoreadmincontact' OR `engine4_activity_notificationtypes`.`module` LIKE 'sitestorealbum' OR `engine4_activity_notificationtypes`.`module` LIKE 'sitestoreform' OR `engine4_activity_notificationtypes`.`module` LIKE 'sitestoreinvite' OR `engine4_activity_notificationtypes`.`module` LIKE 'sitestorelikebox' OR `engine4_activity_notificationtypes`.`module` LIKE 'sitestoreoffer' OR `engine4_activity_notificationtypes`.`module` LIKE 'sitestoreproduct' OR `engine4_activity_notificationtypes`.`module` LIKE 'sitestorereview' OR `engine4_activity_notificationtypes`.`module` LIKE 'sitestoreurl' OR `engine4_activity_notificationtypes`.`module` LIKE 'sitestorevideo';