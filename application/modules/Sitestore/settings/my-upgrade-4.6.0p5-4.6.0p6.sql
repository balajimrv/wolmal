ALTER TABLE `engine4_sitestoreproduct_wishlists` ADD `featured` TINYINT( 1 ) NOT NULL DEFAULT '0'; 

UPDATE `engine4_core_modules` SET `title` = 'Stores / Marketplace - Ecommerce',
`description` = 'Stores / Marketplace - Ecommerce' WHERE `engine4_core_modules`.`name` = 'sitestore' LIMIT 1 ;

UPDATE `engine4_core_modules` SET `title` = 'Stores / Marketplace - Ecommerce Contact Store Owners Extension',
`description` = 'Stores / Marketplace - Ecommerce Contact Store Owners Extension' WHERE `engine4_core_modules`.`name` = 'sitestoreadmincontact' LIMIT 1 ;

UPDATE `engine4_core_modules` SET `title` = 'Stores / Marketplace - Ecommerce Photo Albums Extension',
`description` = 'Stores / Marketplace - Ecommerce Photo Albums Extension' WHERE `engine4_core_modules`.`name` = 'sitestorealbum' LIMIT 1 ;

UPDATE `engine4_core_modules` SET `title` = 'Stores / Marketplace - Ecommerce Form Extension',
`description` = 'Stores / Marketplace - Ecommerce Form Extension' WHERE `engine4_core_modules`.`name` = 'sitestoreform' LIMIT 1 ;

UPDATE `engine4_core_modules` SET `title` = 'Stores / Marketplace - Ecommerce Inviter Extension',
`description` = 'Stores / Marketplace - Ecommerce Inviter Extension' WHERE `engine4_core_modules`.`name` = 'sitestoreinvite' LIMIT 1 ;

UPDATE `engine4_core_modules` SET `title` = 'Stores / Marketplace - Ecommerce Embeddable Badges, Like Box Extension',
`description` = 'Stores / Marketplace - Ecommerce Embeddable Badges, Like Box Extension' WHERE `engine4_core_modules`.`name` = 'sitestorelikebox' LIMIT 1 ;

UPDATE `engine4_core_modules` SET `title` = 'Stores / Marketplace - Ecommerce Offers Extension',
`description` = 'Stores / Marketplace - Ecommerce Offers Extension' WHERE `engine4_core_modules`.`name` = 'sitestoreoffer' LIMIT 1 ;

UPDATE `engine4_core_modules` SET `title` = 'Stores / Marketplace - Ecommerce Products Extension',
`description` = 'Stores / Marketplace - Ecommerce Products Extension' WHERE `engine4_core_modules`.`name` = 'sitestoreproduct' LIMIT 1 ;

UPDATE `engine4_core_modules` SET `title` = 'Stores / Marketplace - Ecommerce Reviews and Ratings Extension',
`description` = 'Stores / Marketplace - Ecommerce Reviews and Ratings Extension' WHERE `engine4_core_modules`.`name` = 'sitestorereview' LIMIT 1 ;

UPDATE `engine4_core_modules` SET `title` = 'Stores / Marketplace - Ecommerce Short Store URL Extension',
`description` = 'Stores / Marketplace - Ecommerce Short Store URL Extension' WHERE `engine4_core_modules`.`name` = 'sitestoreurl' LIMIT 1 ;

UPDATE `engine4_core_modules` SET `title` = 'Stores / Marketplace - Ecommerce Videos Extension',
`description` = 'Stores / Marketplace - Ecommerce Videos Extension' WHERE `engine4_core_modules`.`name` = 'sitestorevideo' LIMIT 1 ;
