INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`,  `order`, `enable_mobile`, `enable_tablet`) VALUES ( 'core_main_cometchat', 'cometchat', 'Chat', 'Sitemobile_Plugin_Menus', '{"uri":"cometchat"}', 'core_main', NULL,  '100', '1', '1');

UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.dashboard.contentType.mobile' WHERE  `engine4_core_settings`.`name` =  'sitemobile.dashboard.contentType' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.header.position.mobile' WHERE  `engine4_core_settings`.`name` =  'sitemobile.mobile.header.position' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.footer.position.mobile' WHERE  `engine4_core_settings`.`name` =  'sitemobile.mobile.footer.position' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.popup.view.mobile' WHERE  `engine4_core_settings`.`name` =  'sitemobile.popup.view' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.mobile.0' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.options.0' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.mobile.1' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.options.1' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.mobile.2' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.options.2' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.mobile.3' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.options.3' LIMIT 1 ;

UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.dashboard.contentType.tablet' WHERE  `engine4_core_settings`.`name` =  'sitemobile.tablet.dashboard.contentType' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.header.position.tablet' WHERE  `engine4_core_settings`.`name` =  'sitemobile.tablet.header.position' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.footer.position.tablet' WHERE  `engine4_core_settings`.`name` =  'sitemobile.tablet.footer.position' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.popup.view.tablet' WHERE  `engine4_core_settings`.`name` =  'sitemobile.popup.tablet' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.tablet.0' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.tablet.options.0' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.tablet.1' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.tablet.options.1' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.tablet.2' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.tablet.options.2' LIMIT 1 ;
UPDATE `engine4_core_settings` SET  `name` =  'sitemobile.lightbox.options.tablet.3' WHERE  `engine4_core_settings`.`name` =  'sitemobile.lightbox.tablet.options.3' LIMIT 1 ;