UPDATE `engine4_core_menuitems` SET `plugin` = 'Sitestore_Plugin_Menus::showSitestore' WHERE `engine4_core_menuitems`.`name` ='sitestore_gutter_suggesttofriend' LIMIT 1 ;

UPDATE `engine4_core_menuitems` SET `plugin` = 'Sitestore_Plugin_Menus::showSitestore' WHERE `engine4_core_menuitems`.`name` ='sitestoreproduct_gutter_suggesttofriend' LIMIT 1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sitestore_home_wishlist', 'sitestore', 'My Wishlists', 'Sitestoreproduct_Plugin_Menus::myWishlist', '', 'user_home', '', 999);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`)VALUES 
('sitestoreproduct_create', 'sitestoreproduct', '{item:$subject} created a new product {item:$object}.', '0', '', '1');

DELETE FROM `engine4_core_menus` WHERE `engine4_core_menus`.`name` = 'sitestore_main';

UPDATE `engine4_core_menus` SET `title` = 'Stores - Store Main Navigation Menu' WHERE `engine4_core_menus`.`name` ='sitestoreproduct_main' LIMIT 1 ;

UPDATE `engine4_core_menus` SET `title` = 'Stores - Store Quick Navigation Menu' WHERE `engine4_core_menus`.`name` ='sitestore_quick' LIMIT 1 ;

UPDATE `engine4_core_menus` SET `title` = 'Stores - Store Profile Options Menu' WHERE `engine4_core_menus`.`name` ='sitestore_gutter' LIMIT 1 ;

UPDATE `engine4_core_menus` SET `title` = 'Stores - Products Profile Page Options Menu' WHERE `engine4_core_menus`.`name` ='sitestoreproduct_gutter' LIMIT 1 ;

DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'sitestore_substore_gutter_create';