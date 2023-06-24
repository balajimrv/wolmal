<?php
$db = Zend_Db_Table_Abstract::getDefaultAdapter();
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitemobile_admin_main_manage", "sitemobile", "Icon / Screen Settings", "", \'{"route":"admin_default","module":"sitemobile","controller":"manage","action":"home-icon"}\', "sitemobile_admin_main", "", 2),

("sitemobile_admin_main_content", "sitemobile", "Mobile / Tablet Layout Editor", "", \'{"route":"admin_default","module":"sitemobile","controller":"content"}\', "sitemobile_admin_main", "",3),

("sitemobile_admin_main_themes", "sitemobile", "Mobile / Tablet Theme Editor", "", \'{"route":"admin_default","module":"sitemobile","controller":"themes"}\', "sitemobile_admin_main", "", 4),

("sitemobile_admin_main_menus", "sitemobile", "Mobile / Tablet Menu Editor", "", \'{"route":"admin_default","module":"sitemobile","controller":"menus"}\', "sitemobile_admin_main", "", 5),

("sitemobile_admin_main_module", "sitemobile", "Manage Modules", "", \'{"route":"admin_default","module":"sitemobile","controller":"module","action":"manage"}\', "sitemobile_admin_main", "", 7);');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitemobile_admin_main_manage_home-icon", "sitemobile", "Home Screen Icon Settings", "", \'{"route":"admin_default","module":"sitemobile","controller":"manage","action":"home-icon"}\', "sitemobile_admin_main_manage", "", 1),
("sitemobile_admin_main_manage_splash-screen", "sitemobile", "Splash Screen Settings", "", \'{"route":"admin_default","module":"sitemobile","controller":"manage","action":"splash-screen"}\', "sitemobile_admin_main_manage", "", 2);');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("core_admin_main_layout_sitemobile_content", "sitemobile", "SEAO - Mobile / Tablet Plugin Layout Editor", "", \'{"route":"admin_default","module":"sitemobile","controller":"content"}\', "core_admin_main_layout", "",6),
("core_admin_main_layout_sitemobile_themes", "sitemobile", "SEAO - Mobile / Tablet Plugin Theme Editor", "", \'{"route":"admin_default","module":"sitemobile","controller":"themes"}\', "core_admin_main_layout", "", 7),
("core_admin_main_layout_sitemobile_menus", "sitemobile", "SEAO - Mobile / Tablet Plugin Menu Editor", "", \'{"route":"admin_default","module":"sitemobile","controller":"menus"}\', "core_admin_main_layout", "", 8);');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ( "sitemobile_admin_main_settings_mobile", "sitemobile", "Mobile Settings", NULL, \'{"route":"admin_default","module":"sitemobile","controller":"settings", "action":"mobile"}\', "sitemobile_admin_main_settings", "", "1", "0", "2");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ( "sitemobile_admin_main_settings_tablet", "sitemobile", "Tablet Settings", NULL, \'{"route":"admin_default","module":"sitemobile","controller":"settings", "action":"tablet"}\', "sitemobile_admin_main_settings", "", "1", "0", "3");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ( "sitemobile_admin_main_content_mobile", "sitemobile", "Mobile Layout Editor", NULL, \'{"route":"admin_default","module":"sitemobile","controller":"content"}\', "sitemobile_admin_main_content", "", "1", "0", "1");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ( "sitemobile_admin_main_content_tablet", "sitemobile", "Tablet Layout Editor", NULL, \'{"route":"admin_default","module":"sitemobile","controller":"tablet-content"}\', "sitemobile_admin_main_content", "", "1", "0", "2");');

$db->query('UPDATE `engine4_core_menuitems` SET `enabled` = "1" WHERE `engine4_core_menuitems`.`name` = "core_footer_sitemobile" LIMIT 1 ;');

$db->query('UPDATE `engine4_core_menuitems` SET `enabled` = "1" WHERE `engine4_core_menuitems`.`name` = "core_footer_sitemobile_tablet" LIMIT 1 ;');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ( "sitemobile_admin_main_settings_general", "sitemobile", "General Settings", NULL, \'{"route":"admin_default","module":"sitemobile","controller":"settings"}\', "sitemobile_admin_main_settings", "", "1", "0", "1");');