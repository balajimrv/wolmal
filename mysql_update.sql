#Table Name: engine4_core_menuitems

SELECT * FROM `engine4_core_menuitems` WHERE `name`="sescustomize_admin_main_ebbb"; 

UPDATE `engine4_core_menuitems` SET `label` = 'FB/EB/BB Total' WHERE `engine4_core_menuitems`.`id` = 666; 

CREATE TABLE `wffbnbcg_dem982`.`engine4_sescustomize_fbvalues` (  `ebvalue_id` int(11) NOT NULL,  `total` text NOT NULL,  `user_id` int(11) NOT NULL,  `type` enum('redeem','insert','bank') NOT NULL COMMENT 'redeem = withdraw,insert = earn',  `order_id` int(11) UNSIGNED NOT NULL,  `creation_date` datetime NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `engine4_sescustomize_fbvalues` CHANGE `ebvalue_id` `fbvalue_id` INT(11) NOT NULL AUTO_INCREMENT; 

UPDATE `engine4_core_menuitems` SET `params` = '{\"route\":\"admin_default\",\"module\":\"sescustomize\",\"controller\":\"manage\",\"action\":\"fbvalue\"}' WHERE `engine4_core_menuitems`.`id` = 666; 

ALTER TABLE `engine4_authorization_levels` ADD `level_order` INT NOT NULL AFTER `flag`;

ALTER TABLE `engine4_authorization_levels` ADD `reward` FLOAT NOT NULL AFTER `flag`;

ALTER TABLE `engine4_authorization_levels` CHANGE `reward` `reward` INT NOT NULL; 

ALTER TABLE `engine4_semods_userpoints` ADD `award_count` INT NOT NULL DEFAULT '0' AFTER `userpoints_totalspent`; 