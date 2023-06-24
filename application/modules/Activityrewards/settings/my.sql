
/**
 * SocialEngine - SocialEngineMods
 *
 */

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('activityrewards', 'Activity Rewards', 'Activity Rewards plugin', '4.0.0', 1, 'extra');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activityrewards_admin_main_offers', 'activityrewards', 'Offers', '', '{"route":"admin_default","module":"activityrewards","controller":"offers"}', 'activitypoints_admin_main', '', 0, 10),
('activityrewards_admin_main_shop', 'activityrewards', 'Points Shop', '', '{"route":"admin_default","module":"activityrewards","controller":"shop"}', 'activitypoints_admin_main', '', 0, 11);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activityrewards_admin_offer_edit', 'activityrewards', 'Edit Offer', '', '{"route":"admin_default","module":"activityrewards","controller":"offer", "action":"edit"}', 'activitypoints_admin_offer', '', 0, 1);


/* user navigation */
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activityrewards_earn', 'activityrewards', 'Earn Points', 'Activityrewards_Plugin_Menus', '{"route":"activityrewards_earn","module":"activityrewards","controller":"offers","action":"index"}', 'activitypoints_main', '', '0', '10');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`) VALUES
('activityrewards_spend', 'activityrewards', 'Spend Points', 'Activityrewards_Plugin_Menus', '{"route":"activityrewards_spend","module":"activityrewards","controller":"shop","action":"index"}', 'activitypoints_main', '', '0', '11');

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES('activitypoints.enable.offers', '1');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES('activitypoints.enable.shop', '1');


ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;

/* level permissions */
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(1, 'activityrewards_earner', 'comment', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(1, 'activityrewards_earner', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(1, 'activityrewards_spender', 'comment', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(1, 'activityrewards_spender', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(2, 'activityrewards_earner', 'comment', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(2, 'activityrewards_earner', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(2, 'activityrewards_spender', 'comment', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(2, 'activityrewards_spender', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(3, 'activityrewards_earner', 'comment', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(3, 'activityrewards_earner', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(3, 'activityrewards_spender', 'comment', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(3, 'activityrewards_spender', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(4, 'activityrewards_earner', 'comment', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(4, 'activityrewards_earner', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(4, 'activityrewards_spender', 'comment', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(4, 'activityrewards_spender', 'view', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(5, 'activityrewards_earner', 'comment', 1, NULL);
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES(5, 'activityrewards_earner', 'view', 1, NULL);


CREATE TABLE IF NOT EXISTS `engine4_semods_userpointearnertypes` (
  `userpointearnertype_id` int(11) NOT NULL auto_increment,
  `userpointearnertype_type` int(11) NOT NULL,
  `userpointearnertype_typename` varchar(50) NOT NULL,
  `userpointearnertype_name` varchar(20) NOT NULL,
  `userpointearnertype_title` varchar(255) NOT NULL,
  `form` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`userpointearnertype_id`),
  UNIQUE KEY `userpointearnertype_type` (`userpointearnertype_type`)
) DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;


INSERT IGNORE INTO `engine4_semods_userpointearnertypes` (`userpointearnertype_type`, `userpointearnertype_typename`, `userpointearnertype_name`, `userpointearnertype_title`, `form`, `model`) VALUES(100, 'Affiliate', 'affiliate', 'Affiliate', '', '');
INSERT IGNORE INTO `engine4_semods_userpointearnertypes` (`userpointearnertype_type`, `userpointearnertype_typename`, `userpointearnertype_name`, `userpointearnertype_title`, `form`, `model`) VALUES(400, 'Generic', 'generic', 'Generic', '', '');


CREATE TABLE IF NOT EXISTS `engine4_semods_userpointspendertypes` (
  `userpointspendertype_id` int(11) NOT NULL auto_increment,
  `userpointspendertype_type` int(11) NOT NULL,
  `userpointspendertype_typename` varchar(50) NOT NULL,
  `userpointspendertype_name` varchar(20) NOT NULL,
  `userpointspendertype_title` varchar(255) NOT NULL,
  `form` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`userpointspendertype_id`),
  UNIQUE KEY `userpointspendertype_type` (`userpointspendertype_type`)
) DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

INSERT IGNORE INTO `engine4_semods_userpointspendertypes` (`userpointspendertype_id`, `userpointspendertype_type`, `userpointspendertype_typename`, `userpointspendertype_name`, `userpointspendertype_title`, `form`, `model`) VALUES(10, 200, 'Level Upgrade', 'levelupgrade', 'Level Upgrade', '', '');
INSERT IGNORE INTO `engine4_semods_userpointspendertypes` (`userpointspendertype_id`, `userpointspendertype_type`, `userpointspendertype_typename`, `userpointspendertype_name`, `userpointspendertype_title`, `form`, `model`) VALUES(11, 400, 'Generic', 'generic', 'Generic', '', '');

UPDATE  `engine4_core_settings` SET  `value` = '1' WHERE  `name` =  'activitypoints.enable.offers';
UPDATE  `engine4_core_settings` SET  `value` = '1' WHERE  `name` =  'activitypoints.enable.shop';
