<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Installer extends Engine_Package_Installer_Module {

    function onPreinstall() {
        // Main Directory
        $db = $this->getDb();
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_activity_actiontypes'")->fetch();
        if (!empty($table_exist)) {
            $is_object_thumbColumn = $db->query("SHOW COLUMNS FROM `engine4_activity_actiontypes` LIKE 'is_object_thumb'")->fetch();
            if (empty($is_object_thumbColumn)) {
                $db->query("ALTER TABLE `engine4_activity_actiontypes` ADD `is_object_thumb` BOOL NOT NULL DEFAULT '0'");
            }
        }

        $PRODUCT_TYPE = 'sitestore';
        $PLUGIN_TITLE = 'Sitestore';
        $PLUGIN_VERSION = '4.9.1p1';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'Stores / Marketplace - Ecommerce Plugin';
        $PRODUCT_TITLE = 'Stores / Marketplace - Ecommerce Plugin';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.8.11';
        $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
        $is_file = file_exists($file_path);
        if (empty($is_file)) {
            include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license4.php";
        } else {
            $db = $this->getDb();
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
            $is_Mod = $select->query()->fetchObject();
            if (empty($is_Mod)) {
                include_once $file_path;
            }
        }

        parent::onPreinstall();
    }

    protected function _setStoreUpgradeQueries($db) {
        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_products'")->fetch();
        if (!empty($is_table_exist)) {
            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_products LIKE 'allow_purchase'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_products`  ADD `allow_purchase` TINYINT(1) NOT NULL DEFAULT '1' ;");
            }

            $column_product_code_exist = $db->query('SHOW COLUMNS FROM engine4_sitestoreproduct_products LIKE \'product_code\'')->fetch();
            if (!empty($column_product_code_exist)) {
                $db->query('ALTER TABLE `engine4_sitestoreproduct_products` CHANGE `product_code` `product_code` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL');
            }

            $column_stock_unlimited_exist = $db->query('SHOW COLUMNS FROM engine4_sitestoreproduct_products LIKE \'stock_unlimited\'')->fetch();
            if (!empty($column_stock_unlimited_exist)) {
                $db->query('ALTER TABLE `engine4_sitestoreproduct_products` CHANGE `stock_unlimited` `stock_unlimited` TINYINT( 1 ) UNSIGNED NULL DEFAULT "1" COMMENT \'"1" => "Yes", "0" => "No"\'');
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_order_products'")->fetch();
        if (!empty($is_table_exist)) {
            $column_product_sku_exist = $db->query('SHOW COLUMNS FROM engine4_sitestoreproduct_order_products LIKE \'product_sku\'')->fetch();
            if (!empty($column_product_sku_exist)) {
                $db->query('ALTER TABLE `engine4_sitestoreproduct_order_products` CHANGE `product_sku` `product_sku` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL');
            }

            $column_order_product_info_exist = $db->query('SHOW COLUMNS FROM engine4_sitestoreproduct_order_products LIKE \'order_product_info\'')->fetch();
            if (empty($column_order_product_info_exist)) {
                $db->query('ALTER TABLE `engine4_sitestoreproduct_order_products` ADD `order_product_info` TEXT NULL');
            }

            $column_downpayment_exist = $db->query('SHOW COLUMNS FROM engine4_sitestoreproduct_order_products LIKE \'downpayment\'')->fetch();
            if (empty($column_downpayment_exist)) {
                $db->query('ALTER TABLE `engine4_sitestoreproduct_order_products` ADD `downpayment` FLOAT UNSIGNED NOT NULL DEFAULT "0" AFTER `price`');
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_cartproducts'")->fetch();
        if (!empty($is_table_exist)) {
            $column_other_info_exist = $db->query('SHOW COLUMNS FROM engine4_sitestoreproduct_cartproducts LIKE \'other_info\'')->fetch();
            if (empty($column_other_info_exist)) {
                $db->query('ALTER TABLE `engine4_sitestoreproduct_cartproducts` ADD `other_info` TEXT NULL');
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_otherinfo'")->fetch();
        if (!empty($is_table_exist)) {
            $column_downpayment_value_exist = $db->query('SHOW COLUMNS FROM engine4_sitestoreproduct_otherinfo LIKE \'downpayment_value\'')->fetch();
            if (empty($column_downpayment_value_exist)) {
                $db->query('ALTER TABLE `engine4_sitestoreproduct_otherinfo` ADD `downpayment_value` FLOAT NOT NULL DEFAULT "0"');
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_orders'")->fetch();
        if (!empty($is_table_exist)) {
            $column_downpayment_total_exist = $db->query('SHOW COLUMNS FROM engine4_sitestoreproduct_orders LIKE \'downpayment_total\'')->fetch();
            if (empty($column_downpayment_total_exist)) {
                $db->query('ALTER TABLE `engine4_sitestoreproduct_orders` ADD `downpayment_total` FLOAT UNSIGNED NOT NULL DEFAULT "0" AFTER `cheque_id`');
            }

            $column_is_downpayment_exist = $db->query('SHOW COLUMNS FROM engine4_sitestoreproduct_orders LIKE \'is_downpayment\'')->fetch();
            if (empty($column_is_downpayment_exist)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_orders` ADD `is_downpayment` TINYINT NOT NULL DEFAULT '0' COMMENT '0 => Not downpayment, 1 => With Downpayment, 2=> Completed after downpayment remaining amount'");
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_store_gateways'")->fetch();
        if (!empty($is_table_exist)) {
            $column_gateway_type_exist = $db->query('SHOW COLUMNS FROM engine4_sitestoreproduct_store_gateways LIKE \'gateway_type\'')->fetch();
            if (empty($column_gateway_type_exist)) {
                $db->query('ALTER TABLE `engine4_sitestoreproduct_store_gateways` ADD `gateway_type` TINYINT NOT NULL DEFAULT "0" COMMENT "0 => Direct Payment, 1 => Downpayment, 2 => Remaining Payment"');
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_transactions'")->fetch();
        if (!empty($is_table_exist)) {
            $column_is_remaining_amount_payment_exist = $db->query('SHOW COLUMNS FROM engine4_sitestoreproduct_transactions LIKE \'is_remaining_amount_payment\'')->fetch();
            if (empty($column_is_remaining_amount_payment_exist)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_transactions` ADD `is_remaining_amount_payment` TINYINT NOT NULL DEFAULT '0' COMMENT '0 => No, 1 => Yes, in case of Downpayment'");
            }
        }

        // MAIL TO SITEADMIN AT STORE CREATION
        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('SITESTORE_STORE_CREATION', 'sitestore', '[host],[object_title_link],[object_title],[sender],[object_link],[object_description]');");
    }

    protected function _setVatWorkQueries($db) {
        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_taxes'")->fetch();
        if (!empty($is_table_exist)) {
            $is_vat_exist = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_taxes LIKE 'is_vat'")->fetch();
            if (empty($is_vat_exist)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_taxes` ADD `is_vat` TINYINT NOT NULL DEFAULT '0' COMMENT '0 => Tax, 1 => VAT';");
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_order_products'")->fetch();
        if (!empty($is_table_exist)) {
            $tax_title_exist = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_order_products LIKE 'tax_title'")->fetch();
            if (!empty($tax_title_exist)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_order_products` CHANGE `tax_title` `tax_title` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;");
            }
        }
    }

    private function _setFullWidthProductProfilePage($db) {
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules')
                ->where('name = ?', "sitestore")
                ->where('enabled = ?', 1);
        $isModEnabled = $select->query()->fetchObject();
        if (!empty($isModEnabled)) {
            // CHECK PLUGIN ACTIVATED OR NOT
            $isActivate = $db->query("SELECT `value` FROM `engine4_core_settings` WHERE `name` LIKE '%sitestore.isActivate%' LIMIT 1")->fetchColumn();
            if (empty($isActivate))
                return;

            $running_version = $isModEnabled->version;
            $product_version = "4.8.7";
            $shouldUpgrade = false;

            // CHECK VERSION
            if (!empty($running_version) && !empty($product_version)) {
                $temp_running_verion_2 = $temp_product_verion_2 = 0;
                if (strstr($product_version, "p")) {
                    $temp_starting_product_version_array = @explode("p", $product_version);
                    $temp_product_verion_1 = $temp_starting_product_version_array[0];
                    $temp_product_verion_2 = $temp_starting_product_version_array[1];
                } else {
                    $temp_product_verion_1 = $product_version;
                }
                $temp_product_verion_1 = @str_replace(".", "", $temp_product_verion_1);


                if (strstr($running_version, "p")) {
                    $temp_starting_running_version_array = @explode("p", $running_version);
                    $temp_running_verion_1 = $temp_starting_running_version_array[0];
                    $temp_running_verion_2 = $temp_starting_running_version_array[1];
                } else {
                    $temp_running_verion_1 = $running_version;
                }
                $temp_running_verion_1 = @str_replace(".", "", $temp_running_verion_1);


                if (($temp_running_verion_1 < $temp_product_verion_1) || (($temp_running_verion_1 == $temp_product_verion_1) && ($temp_running_verion_2 < $temp_product_verion_2))) {
                    $shouldUpgrade = true;
                }
            }

            if (!empty($shouldUpgrade)) {
                $productProfilePageId = $db->query("SELECT `page_id` FROM `engine4_core_pages` WHERE `name` LIKE '%sitestoreproduct_index_view%' LIMIT 1")->fetchColumn();
                if (!empty($productProfilePageId)) {
                    $getRowContentId = $db->query("SELECT `content_id` FROM `engine4_core_content` WHERE `page_id` = " . $productProfilePageId . " AND `name` LIKE '%sitestoreproduct.list-information-profile%' LIMIT 1")->fetchColumn();
                    if (!empty($getRowContentId)) {
                        // GET TOP CONTAINER ID.
                        $topContainerID = $db->query("SELECT `content_id` FROM `engine4_core_content` WHERE `page_id` =" . $productProfilePageId . " AND `type` LIKE 'container' AND `name` LIKE 'top' LIMIT 1")->fetchColumn();
                        if (!empty($topContainerID)) {
                            // GET TOP-MIDDLE CONTAINER ID.
                            $topMiddleContainerID = $db->query("SELECT `content_id` FROM `engine4_core_content` WHERE `page_id` =" . $productProfilePageId . " AND `type` LIKE 'container' AND `name` LIKE 'middle' AND `parent_content_id`=" . $topContainerID . " LIMIT 1")->fetchColumn();
                            if (!empty($topMiddleContainerID)) {
                                // UPDATE ROW
                                $db->query("UPDATE `engine4_core_content` SET `name` = 'sitestoreproduct.full-width-list-information-profile',
`order` = '4', `parent_content_id`= '" . $topMiddleContainerID . "' WHERE `engine4_core_content`.`content_id` = " . $getRowContentId . " LIMIT 1 ;");
                            }
                        }
                    }
                }
            }
        }
        return;
    }

    function onInstall() {
        $db = $this->getDb();
        $db->query('UPDATE `engine4_activity_actiontypes` SET `body` = \'{item:$object} added {var:$count} photo(s) to the album {itemChild:$object:sitestore_album:$child_id}:\' WHERE `engine4_activity_actiontypes`.`type` = \'sitestorealbum_admin_photo_new\' LIMIT 1 ;');

        $db->query('UPDATE `engine4_activity_actiontypes` SET `body` = \'{item:$subject} added {var:$count} photo(s) to the album {itemChild:$object:sitestore_album:$child_id} of store {item:$object}:\' WHERE `engine4_activity_actiontypes`.`type` = \'sitestorealbum_photo_new\' LIMIT 1 ;');

        $select = new Zend_Db_Select($db);
        $select->from('engine4_activity_actions', array('object_id', 'params', 'action_id'))
                ->where('type =?', 'sitestorealbum_admin_photo_new')
                ->orWhere('type =?', 'sitestorealbum_photo_new');
        $results = $select->query()->fetchAll();

        foreach ($results as $result) {
            if (strstr($result['params'], 'slug')) {
                $decoded_cover_param = Zend_Json_Decoder::decode($result['params']);
                $count = $decoded_cover_param['count'];
                $select = new Zend_Db_Select($db);
                $album_id = $select->from('engine4_sitestore_albums', 'album_id')
                                ->where('store_id =?', $result['object_id'])
                                ->order('album_id DESC')
                                ->limit(1)
                                ->query()->fetchColumn();

                $db->query('UPDATE `engine4_activity_actions` SET `params` = \' ' . array('child_id' => $album_id, 'count' => $count) . ' \' WHERE `engine4_activity_actions`.`action_id` = "' . $result['action_id'] . '" LIMIT 1 ;');
            }
        }
        $this->_setFullWidthProductProfilePage($db);
        $this->_offertocoupon($db);
        $this->_widgetParamsChange($db);
        $this->_setVatWorkQueries($db);
        $this->_setStoreUpgradeQueries($db);
        $this->_getSitestoreInstaller($db);
        $this->_getSitestoreadmincontactInstaller($db);
        $this->_getSitestorealbumInstaller($db);
        $this->_getSitestoreinviteInstaller($db);
        $this->_getSitestoreofferInstaller($db);
        $this->_getSitestoreurlInstaller($db);
        $this->_getSitestorevideoInstaller($db);
        $this->_getSitestoreproductInstaller($db);
        $this->_getSitestoreformInstaller($db);
        $this->_integrateStoreWithSuggestion($db);
        $this->_integrateProductWithSuggestion($db);
        $this->_integrateProductWithFacebook($db);
        $this->_integrateStoreWithFacebook($db);
        $this->_createSocialEngineMobilePages($db);
        $this->_getSitestorelocationInstaller($db);
        $this->_setCustomSettings($db);

        // Remove tilteCount problem from widget params
        $this->_changeWidgetParam($db, 'sitestore', '4.8.3');
//        $this->_SiestoredocumentMeraModule($db); // MOVE TO MERA MODULES
//        $this->_SiestoreintegrationMeraModule($db); // MOVE TO MERA MODULES

        $this->_checkinWork($db);
        $sitecreditEnabled = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitecredit')
                ->where('enabled = ?', 1)
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (!empty($sitecreditEnabled)) {
            if($this->isTableExists('engine4_sitestoreproduct_orders')){
                if(!$this->isColumnExists('engine4_sitestoreproduct_orders','credit_point')){
                    $db->query("ALTER TABLE `engine4_sitestoreproduct_orders` ADD `credit_point` INT(11) NOT NULL");
                }
                if(!$this->isColumnExists('engine4_sitestoreproduct_orders','credit_value')){
                    $db->query("ALTER TABLE `engine4_sitestoreproduct_orders` ADD `credit_value` INT(11) NOT NULL");
                }
            }
        }
        //START SITEGATEWAY RELATED WORK
        $sitegatewayEnabled = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitegateway')
                ->where('enabled = ?', 1)
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (!empty($sitegatewayEnabled)) {

            $orderTable = $db->query('SHOW TABLES LIKE \'engine4_sitestoreproduct_orders\'')->fetch();
            if (!empty($orderTable)) {
                $payment_split_column = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_orders LIKE 'payment_split'")->fetch();
                if (empty($payment_split_column)) {
                    $db->query("ALTER TABLE `engine4_sitestoreproduct_orders` ADD `payment_split` TINYINT(1) NOT NULL DEFAULT '0';");
                }
            }

            $storeGatewayTable = $db->query('SHOW TABLES LIKE \'engine4_sitestoreproduct_gateways\'')->fetch();
            if (!empty($storeGatewayTable)) {
                $storeIdIndex = $db->query("SHOW INDEX FROM `engine4_sitestoreproduct_gateways` WHERE Key_name = 'store_id'")->fetch();
                if (!empty($storeIdIndex)) {
                    $db->query("ALTER TABLE `engine4_sitestoreproduct_gateways` DROP INDEX store_id");
                    $db->query("ALTER TABLE `engine4_sitestoreproduct_gateways` ADD INDEX (`store_id`)");
                }
            }

            $storeBillTable = $db->query('SHOW TABLES LIKE \'engine4_sitestoreproduct_storebills\'')->fetch();
            if (!empty($storeBillTable)) {
                $storeIdIndex = $db->query("SHOW INDEX FROM `engine4_sitestoreproduct_storebills` WHERE Key_name = 'store_id'")->fetch();
                if (!empty($storeIdIndex)) {
                    $db->query("ALTER TABLE `engine4_sitestoreproduct_storebills` DROP INDEX store_id");
                    $db->query("ALTER TABLE `engine4_sitestoreproduct_storebills` ADD INDEX (`store_id`)");
                }

                $gateway_id_column = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_storebills LIKE 'gateway_id'")->fetch();
                if (empty($gateway_id_column)) {
                    $db->query("ALTER TABLE `engine4_sitestoreproduct_storebills` ADD `gateway_id` INT(11) NOT NULL AFTER `status`;");
                }
            }
        }
        //END SITEGATEWAY RELATED WORK
        //CODE FOR INCREASE THE SIZE OF engine4_activity_attachments's FIELD type
        $type_array = $db->query("SHOW FULL COLUMNS FROM engine4_sitestoreproduct_sections LIKE 'section_name'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Collation'];
            if ($varchar != "utf8_unicode_ci") {
                $db->query('ALTER TABLE `engine4_sitestoreproduct_sections` CHANGE `section_name` `section_name` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
            }
        }

//     $select = new Zend_Db_Select($db);
//     $select
//             ->from('engine4_core_modules')
//             ->where('name = ?', 'sitestore')
//             ->where('version <= ?', '4.6.0');
//     $is_enabled = $select->query()->fetchObject();
//     if (!empty($is_enabled)) {

        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitestore_mobileadmincontent` (
				`mobileadmincontent_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`store_id` int(11) unsigned NOT NULL,
				`type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
				`name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
				`parent_content_id` int(11) unsigned DEFAULT NULL,
				`order` int(11) NOT NULL DEFAULT '1',
				`params` text COLLATE utf8_unicode_ci,
				`attribs` text COLLATE utf8_unicode_ci,
				`module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				`default_admin_layout` tinyint(4) NOT NULL DEFAULT '0',
				PRIMARY KEY (`mobileadmincontent_id`),
				KEY `store_id` (`store_id`,`order`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");

        //$db->query("DROP TABLE IF EXISTS `engine4_sitestore_mobilecontent`;");
        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitestore_mobilecontent` (
				`mobilecontent_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`mobilecontentstore_id` int(11) unsigned NOT NULL,
				`type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
				`name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
				`parent_content_id` int(11) unsigned DEFAULT NULL,
				`order` int(11) NOT NULL DEFAULT '1',
				`params` text COLLATE utf8_unicode_ci,
				`attribs` text COLLATE utf8_unicode_ci,
				`module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
				`widget_admin` tinyint(1) NOT NULL DEFAULT '1',
				PRIMARY KEY (`mobilecontent_id`),
				KEY `store_id` (`mobilecontentstore_id`,`order`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");

        //	$db->query("DROP TABLE IF EXISTS `engine4_sitestore_mobilecontentstores`;");
        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitestore_mobilecontentstores` (
				`mobilecontentstore_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(11) unsigned NOT NULL,
				`store_id` int(11) unsigned NOT NULL,
				`name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
				`displayname` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
				`url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
				`title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`description` text COLLATE utf8_unicode_ci,
				`keywords` text COLLATE utf8_unicode_ci NOT NULL,
				`custom` tinyint(1) NOT NULL DEFAULT '1',
				`fragment` tinyint(1) NOT NULL DEFAULT '0',
				`layout` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
				`view_count` int(11) unsigned NOT NULL DEFAULT '1',
				PRIMARY KEY (`mobilecontentstore_id`),
				KEY `store_id` (`store_id`),
				KEY `user_id` (`user_id`),
				KEY `name` (`name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if ($is_sitemobile_object) {
            include APPLICATION_PATH . "/application/modules/Sitestore/controllers/license/mobileLayoutCreation.php";
            $db->query('INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`, `enable_mobile`, `enable_tablet`) VALUES ("core_main_sitestoreproductmycart", "sitestoreproduct", "My Cart", NULL, "{\"route\":\"sitestoreproduct_product_general\", \"action\":\"cart\"}", "core_main", "", 37, 1, 1);');
        }
        //}
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`,`is_object_thumb`) VALUES ("sitestore_admin_profile_photo", "sitestore", "{item:$object} updated a new profile photo.", 1, 3, 2, 1, 1, 1, 1);');

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siteevent')
                ->where('enabled = ?', 1);
        $is_siteevent_object = $select->query()->fetchObject();
        if ($is_siteevent_object) {
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_store_host", "siteevent", \'{item:$subject} has made your store {var:$store} host of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.\', "");');
            $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES("SITEEVENT_STORE_HOST", "siteevent", "[host],[email],[sender],[event_title_with_link],[event_url],[store_title_with_link]");');
            $itemMemberTypeColumn = $db->query("SHOW COLUMNS FROM `engine4_siteevent_modules` LIKE 'item_membertype'")->fetch();
            if (empty($itemMemberTypeColumn)) {
                $db->query("ALTER TABLE `engine4_siteevent_modules` ADD `item_membertype` VARCHAR( 255 ) NOT NULL AFTER `item_title`");
            }
            $db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitestore_store', 'store_id', 'sitestore', '0', '0', 'Store Events', 'a:2:{i:0;s:18:\"contentlikemembers\";i:1;s:20:\"contentfollowmembers\";}')");
            $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "siteevent.event.leader.owner.sitestore.store", "1");');
        }

        $this->setActivityFeeds();

        $is_store_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestore_stores'")->fetch();
        if (!empty($is_store_table_exist)) {
            $is_min_ship_cost_exist = $db->query("SHOW COLUMNS FROM engine4_sitestore_stores LIKE 'min_shipping_cost'")->fetch();
            if (empty($is_min_ship_cost_exist)) {
                $db->query("ALTER TABLE `engine4_sitestore_stores` ADD `min_shipping_cost` FLOAT UNSIGNED NOT NULL DEFAULT '0';");
            }
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'documentintegration')
                ->where('enabled = ?', 1);
        $is_documentintegration_object = $select->query()->fetchObject();
        if ($is_documentintegration_object) {
            $db->query("INSERT IGNORE INTO `engine4_document_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`) VALUES ('sitestore_store', 'store_id', 'sitestore', '0', '0', 'Store Documents')");
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitestore_admin_main_managedocument", "documentintegration", "Manage Documents", "", \'{"uri":"admin/document/manage-document/index/contentType/sitestore_store/contentModule/sitestore"}\', "sitestore_admin_main", "", 0, 0, 25);');
            $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "document.leader.owner.sitestore.store", "1");');
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitevideo')
                ->where('enabled = ?', 1);
        $is_sitevideo_object = $select->query()->fetchObject();
        if ($is_sitevideo_object) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_settings')
                    ->where('name = ?', 'sitestore.isActivate')
                    ->where('value = ?', 1);
            $sitestore_isActivate_object = $select->query()->fetchObject();
            if ($sitestore_isActivate_object) {

                $db->query("INSERT IGNORE INTO `engine4_sitevideo_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitestore_store', 'store_id', 'sitestore', '0', '0', 'Stores Videos', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
                $db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitestore_admin_main_managevideo", "sitevideo", "Manage Videos", "", \'{"uri":"admin/sitevideo/manage-video/index/contentType/sitestore_store/contentModule/sitestore"}\', "sitestore_admin_main", "", 0, 0, 24);');
                $db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "sitevideo.video.leader.owner.sitestore.store", "1");');
            }
        }

        $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestore_dashboard_kycupload', 'sitestore', 'Upload KYC', 'Sitestore_Plugin_Dashboardmenus','', 'sitestore_dashboard', NULL, '1', '0', '115');");

        $store_order_column = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_orders LIKE 'payout_status'")->fetch();
        if (empty($store_order_column)) {
            $db->query("ALTER TABLE `engine4_sitestoreproduct_orders` ADD `payout_status` VARCHAR(50) NOT NULL ;");
        }

        $store_order_column = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_orders LIKE 'refund_status'")->fetch();
        if (empty($store_order_column)) {
            $db->query("ALTER TABLE `engine4_sitestoreproduct_orders` ADD `refund_status` VARCHAR(50) NOT NULL ;");
        }

        $store_order_column = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_orders LIKE 'gateway_type'")->fetch();
        if (empty($store_order_column)) {
            $db->query("ALTER TABLE `engine4_sitestoreproduct_orders` ADD `gateway_type` VARCHAR(25) NOT NULL ;");
        }


        parent::onInstall();
    }

    function onEnable() {
        $db = $this->getDb();
        $getModsName = array('sitestoreadmincontact', 'sitestorealbum', 'sitestoreform', 'sitestoreinvite', 'sitestoreoffer', 'sitestoreproduct', 'sitestorereview', 'sitestoreurl', 'sitestorevideo', 'sitestorelikebox');
        foreach ($getModsName as $modName) {
            $db->query('UPDATE `engine4_core_modules` SET `enabled` = "1" WHERE `engine4_core_modules`.`name` = "' . $modName . '" LIMIT 1');
        }
        parent::onEnable();
    }

    function onDisable() {
        $db = $this->getDb();

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules', array('name'))
                ->where('name =?', 'sitetheme')
                ->where('enabled = ?', 1);
        $moduleData = $select->query()->fetchAll();
        if (!empty($moduleData)) {
            $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
            $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Note: Please disable the Shopping Hub - a Social Commerce Theme plugin before disabling the Stores / Marketplace - Ecommerce.');
            echo "<div style='background-color: #E9F4FA;border-radius:7px 7px 7px 7px;float:left;overflow: hidden;padding:10px;'><div style='background:#FFFFFF;border:1px solid #D7E8F1;overflow:hidden;padding:20px;'><span style='color:red'>$error_msg1</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.</div></div>";
            die;
        }

        $getModsName = array('sitestoreadmincontact', 'sitestorealbum', 'sitestoreform', 'sitestoreinvite', 'sitestoreoffer', 'sitestoreproduct', 'sitestorereview', 'sitestoreurl', 'sitestorevideo', 'sitestorelikebox');
        foreach ($getModsName as $modName) {
            $db->query('UPDATE `engine4_core_modules` SET `enabled` = "0" WHERE `engine4_core_modules`.`name` = "' . $modName . '" LIMIT 1');
        }
        parent::onDisable();
    }

    protected function _SiestoredocumentMeraModule($db) {
        $db->query('
DROP TABLE IF EXISTS `engine4_sitestoredocument_categories` ;
');

        $db->query('
CREATE TABLE IF NOT EXISTS `engine4_sitestoredocument_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(64) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;  
');


        $db->query('
DROP TABLE IF EXISTS `engine4_sitestoredocument_documents`;
');

        $db->query('
CREATE TABLE IF NOT EXISTS `engine4_sitestoredocument_documents` (
  `document_id` int(10) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `store_id` int(10) NOT NULL,
  `sitestoredocument_title` text NOT NULL,
  `sitestoredocument_slug` text NOT NULL,
  `sitestoredocument_description` text NOT NULL,
  `filename_id` int(12) NOT NULL,
  `storage_path` text NOT NULL,
  `sitestoredocument_license` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `sitestoredocument_private` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `filemime` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `filesize` bigint(20) NOT NULL,
  `doc_id` int(11) NOT NULL DEFAULT "0",
  `secret_password` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `access_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `fulltext` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `thumbnail` text,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `comment_count` int(11) NOT NULL DEFAULT "0",
  `like_count` int(11) NOT NULL DEFAULT "0",
  `views` int(10) NOT NULL,
  `rating` float NOT NULL,
  `email_allow` tinyint(1) NOT NULL DEFAULT "1",
  `download_allow` tinyint(1) NOT NULL DEFAULT "1",
  `secure_allow` tinyint(1) NOT NULL DEFAULT "1",
  `search` tinyint(2) NOT NULL DEFAULT "1",
  `draft` tinyint(2) NOT NULL DEFAULT "0",
  `highlighted` tinyint(1) NOT NULL,
  `featured` tinyint(1) NOT NULL,
  `approved` tinyint(2) NOT NULL DEFAULT "0",
  `status` tinyint(2) NOT NULL DEFAULT "0",
  `activity_feed` tinyint(2) NOT NULL DEFAULT "0",
  PRIMARY KEY (`document_id`),
  KEY `search` (`search`),
  KEY `owner_id` (`owner_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
');

        $db->query('
DROP TABLE IF EXISTS `engine4_sitestoredocument_ratings`;
');

        $db->query('
CREATE TABLE IF NOT EXISTS `engine4_sitestoredocument_ratings` (
  `document_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`document_id`,`user_id`),
  KEY `document_id` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
');

        $db->query('
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitestoredocument_admin_main_settings", "sitestoredocument", "Global Settings", "", \'{"route":"admin_default","module":"sitestoredocument","controller":"settings"}\', "sitestoredocument_admin_main", "", 1);
');

        $db->query('
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitestoredocument_new", "sitestoredocument", "{item:$subject} created a new document in the store {item:$object}:", 1, 3, 2, 1, 1, 1);   
');

        $db->query('
DROP TABLE IF EXISTS `engine4_sitestoredocument_document_fields_maps`;
');

        $db->query('
CREATE TABLE IF NOT EXISTS `engine4_sitestoredocument_document_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
');

        $db->query('
DROP TABLE IF EXISTS `engine4_sitestoredocument_document_fields_meta`;
');

        $db->query('
CREATE TABLE IF NOT EXISTS `engine4_sitestoredocument_document_fields_meta` (
  `field_id` int(11) NOT NULL auto_increment,
  `type` varchar(24) collate latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL default "",
  `alias` varchar(32) NOT NULL default "",
  `required` tinyint(1) NOT NULL default "0",
  `display` tinyint(1) unsigned NOT NULL,
  `search` tinyint(1) unsigned NULL default NULL,
  `order` smallint(3) unsigned NOT NULL default "999",
  `config` text NOT NULL,
  `validators` text NULL,
  `filters` text NULL,
  `style` text NULL,
  `error` text NULL,
  PRIMARY KEY  (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
');

        $db->query('
DROP TABLE IF EXISTS `engine4_sitestoredocument_document_fields_options`;
');

        $db->query('
CREATE TABLE IF NOT EXISTS `engine4_sitestoredocument_document_fields_options` (
  `option_id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL default "999",
  PRIMARY KEY  (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
');

        $db->query('
DROP TABLE IF EXISTS `engine4_sitestoredocument_document_fields_values`;
');

        $db->query('
CREATE TABLE IF NOT EXISTS `engine4_sitestoredocument_document_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL default "0",
  `value` text NOT NULL,
  PRIMARY KEY  (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
');

        $db->query('
DROP TABLE IF EXISTS `engine4_sitestoredocument_document_fields_search`;
');

        $db->query('
CREATE TABLE IF NOT EXISTS `engine4_sitestoredocument_document_fields_search` (
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
');

        $db->query('
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "sitestore_store" as `type`,
    "auth_sdcreate" as `name`,
    5 as `value`,
    \'["registered","owner_network","owner_member_member","owner_member","owner", "member", "like_member"]\' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");
');

        $db->query('
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "sitestore_store" as `type`,
    "sdcreate" as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin", "user");
');

        $db->query('
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "sitestoredocument_document" as `type`,
    "comment" as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin");
');

        $db->query('
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    "sitestoredocument_document" as `type`,
    "comment" as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN("user");
');

        $db->query('
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `vars`) VALUES
("notify_sitestoreowner_sitestoredocument_delete", "[document_title_with_link], [store_title_with_link], [delete_document_link]"),
("notify_docowner_sitestoredocument_delete", "[document_title_with_link], [store_title_with_link], [delete_document_link], [edit_document_link]");
');
    }

    protected function _SiestoreintegrationMeraModule($db) {
        $db->query('
            DROP TABLE IF EXISTS `engine4_sitestoreintegration_contents`;
');
        $db->query('
            CREATE TABLE IF NOT EXISTS `engine4_sitestoreintegration_contents` (
  `content_id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `resource_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `resource_id` int(11) NOT NULL,
  `resource_owner_id` int(11) NOT NULL,
  PRIMARY KEY (`content_id`),
  UNIQUE KEY `resource_type` (`resource_type`,`resource_id`,`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
');
        $db->query('
            DROP TABLE IF EXISTS `engine4_sitestoreintegration_mixsettings`;
');
        $db->query('
            CREATE TABLE IF NOT EXISTS `engine4_sitestoreintegration_mixsettings` (
`mixsetting_id` int(11) NOT NULL AUTO_INCREMENT,
`module` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
`resource_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
	`resource_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
`item_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`enabled` tinyint(1) NOT NULL,
PRIMARY KEY (`mixsetting_id`),
UNIQUE KEY `resource_type` (`resource_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
');
        $db->query('
            INSERT IGNORE INTO `engine4_sitestoreintegration_mixsettings` (`module`, `resource_type`, `resource_id`, `item_title`, `enabled`) VALUES
("list", "list_listing_0", "listing_id", "Listings", 0),
("sitepage", "sitepage_page_0", "page_id", "Pages", 0),
("sitereview", "sitereview_listing_1", "listing_id", "Products", 0);
');

        $db->query('
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("sitestore_list_gutter_create", "sitestore", "Post a new Listing", "Sitestore_Plugin_Menus", \'{"route":"list_general", "class":"buttonlink item_icon_list_listing","action":"create"}\', "sitestore_gutter", "", 1, 0, 999),
("sitestore_sitepage_gutter_create", "sitestore", "Create New Page", "Sitestore_Plugin_Menus", "", "sitestore_gutter", "", 1, 0, 999),
("sitestore_document_gutter_create", "sitestore", "Post New Documents", "Sitestore_Plugin_Menus", \'{"route":"document_create", "class":"buttonlink item_icon_document","action":"create"}\', "sitestore_gutter", "", 1, 0, 999),
("sitestore_sitegroup_gutter_create", "sitestore", "Create New Group", "Sitestore_Plugin_Menus", "", "sitestore_gutter", "", 1, 0, 999),
("sitestore_sitestoreproduct_gutter_create", "sitestore", "Create New Product", "Sitestore_Plugin_Menus", "", "sitestore_gutter", "", 1, 0, 999);            
');
        $db->query('
INSERT IGNORE INTO `engine4_sitestoreintegration_mixsettings` (`module`, `resource_type`, `resource_id`, `item_title`, `enabled`) VALUES
("document", "document_0", "document_id", "Documents", 0),
("sitegroup", "sitegroup_group_0", "group_id", "Groups", 0),
("sitestoreproduct", "sitestoreproduct_product_0", "product_id", "Products", 0);            
');
    }

    protected function _checkFfmpegPath($modName) {

        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        //CHECK FFMPEG PATH FOR CORRECTNESS
        if (function_exists('exec') && function_exists('shell_exec') && extension_loaded("ffmpeg")) {

            //API IS NOT AVAILABLE
            //$ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
            $ffmpeg_path = $db->select()
                    ->from('engine4_core_settings', 'value')
                    ->where('name = ?', $modName . '_video.ffmpeg.path')
                    ->limit(1)
                    ->query()
                    ->fetchColumn(0);

            $output = null;
            $return = null;
            if (!empty($ffmpeg_path)) {
                exec($ffmpeg_path . ' -version', $output, $return);
            }

            //TRY TO AUTO-GUESS FFMPEG PATH IF IT IS NOT SET CORRECTLY
            $ffmpeg_path_original = $ffmpeg_path;
            if (empty($ffmpeg_path) || $return > 0 || stripos(join('', $output), 'ffmpeg') === false) {
                $ffmpeg_path = null;

                //WINDOWS
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    // @todo
                }
                //NOT WINDOWS
                else {
                    $output = null;
                    $return = null;
                    @exec('which ffmpeg', $output, $return);
                    if (0 == $return) {
                        $ffmpeg_path = array_shift($output);
                        $output = null;
                        $return = null;
                        exec($ffmpeg_path . ' -version', $output, $return);
                        if (0 == $return) {
                            $ffmpeg_path = null;
                        }
                    }
                }
            }
            if ($ffmpeg_path != $ffmpeg_path_original) {
                $count = $db->update('engine4_core_settings', array(
                    'value' => $ffmpeg_path,
                        ), array(
                    'name = ?' => $modName . '.video.ffmpeg.path',
                ));
                if ($count === 0) {
                    try {
                        $db->insert('engine4_core_settings', array(
                            'value' => $ffmpeg_path,
                            'name' => $modName . '.video.ffmpeg.path',
                        ));
                    } catch (Exception $e) {
                        
                    }
                }
            }
        }
    }

    protected function _getSitestoreInstaller($db) {


        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_cartproduct_fields_options'")->fetch();
        if (!empty($is_table_exist)) {
            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_cartproduct_fields_options LIKE 'quantity'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_cartproduct_fields_options` ADD `quantity` INT NOT NULL;");
            }

            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_cartproduct_fields_options LIKE 'quantity_unlimited'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_cartproduct_fields_options` ADD `quantity_unlimited` TINYINT( 1 ) UNSIGNED NULL DEFAULT '1' COMMENT '\"1\" => \"Yes\", \"0\" => \"No\"'; ");
            }

            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_cartproduct_fields_options LIKE 'price'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_cartproduct_fields_options` ADD `price` DECIMAL( 16, 2 ) NOT NULL DEFAULT '0.00';");
            }

            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_cartproduct_fields_options LIKE 'price_increment'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_cartproduct_fields_options` ADD `price_increment` TINYINT( 1 ) NULL DEFAULT '1' COMMENT '\"1\" => \"Increment\", \"0\" => \"Decrement\"';");
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_cartproduct_fields_values'")->fetch();

        if (!empty($is_table_exist)) {
            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_cartproduct_fields_values LIKE 'category_attribute'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_cartproduct_fields_values` ADD `category_attribute` TINYINT( 1 ) NOT NULL DEFAULT '0';");
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_order_products'")->fetch();
        if (!empty($is_table_exist)) {
            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_order_products LIKE 'config_info'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_order_products` ADD `config_info` TEXT NULL ;");
            }
        }

        // CREATE OPTION FOR PRECREATED CHECKBOX FIELDS

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_cartproduct_fields_meta'")->fetch();
        if (!empty($is_table_exist)) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_sitestoreproduct_cartproduct_fields_meta', array('field_id', 'label'))
                    ->where('type =?', 'checkbox');
            $meta_result = $select->query()->fetchAll();

            foreach ($meta_result as $result) {
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_sitestoreproduct_cartproduct_fields_options')
                        ->where('field_id =?', $result['field_id']);
                $option_result = $select->query()->fetchAll();
                if (empty($option_result)) {
                    $db->query('INSERT IGNORE INTO `engine4_sitestoreproduct_cartproduct_fields_options` (`field_id`, `label`, `order`, `quantity`, `quantity_unlimited`, `price`, `price_increment`) VALUES ("' . $result['field_id'] . '", "' . $result['label'] . '", "999", "0", "1", "0.00", "1")');
                }
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_taxes'")->fetch();
        if (!empty($is_table_exist)) {
            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_taxes LIKE 'save_price_with_vat' ")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_taxes` ADD `save_price_with_vat` TINYINT NOT NULL DEFAULT '0';");
            }

            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_taxes LIKE 'show_price_with_vat' ")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_taxes` ADD `show_price_with_vat` TINYINT NOT NULL DEFAULT '0';");
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_otherinfo'")->fetch();
        if (!empty($is_table_exist)) {
            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_otherinfo LIKE 'special_vat'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_otherinfo` ADD `special_vat` FLOAT NULL;");
            }
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitestore')
                ->where('version <= ?', '4.8.3');
        $getVersion = $select->query()->fetchObject();
        if (!empty($getVersion)) {
            $storeData = $db->query('SELECT * FROM `engine4_sitestore_packages`')->fetchAll();
            foreach ($storeData as $storeObj) {
                $store_settings = $storeObj['store_settings'];
                if (!empty($store_settings)) {
                    $store_settings = @unserialize($store_settings);
                    if (!isset($store_settings['allow_selling_products'])) {
                        $store_settings['allow_selling_products'] = 1;
                    }
                    if (!isset($store_settings['sale_to_access_levels'])) {
                        $store_settings['sale_to_access_levels'] = 0;
                    }

                    $str_store_settings = @serialize($store_settings);
                    $db->update('engine4_sitestore_packages', array(
                        'store_settings' => $str_store_settings,
                            ), array(
                        'package_id = ?' => $storeObj['package_id'],
                    ));
                }
            }
        }


        $listingtypeTableExist = $db->query('SHOW TABLES LIKE \'engine4_sitereview_listingtypes\'')->fetch();
        if (!empty($listingtypeTableExist)) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_sitereview_listingtypes', array('slug_singular', 'slug_plural'));
            $listingTypeDatas = $select->query()->fetchAll();
            foreach ($listingTypeDatas as $listingTypeData) {
                $urls = array("$listingTypeData->slug_plural", "$listingTypeData->slug_singular");
            }
        }






        //CODE FOR INCREASE THE SIZE OF engine4_activity_notificationsettings's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notificationsettings LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $db->query("ALTER TABLE `engine4_activity_notificationsettings` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestore_manageadmins'")->fetch();
        if (!empty($is_table_exist)) {
            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestore_manageadmins LIKE 'sitestoreproduct_notification'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestore_manageadmins` ADD `sitestoreproduct_notification` TINYINT( 1 ) NOT NULL DEFAULT '0'");
            }
        }

        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitestore';");
        //START FOLLOW WORK
        //IF 'engine4_seaocore_follows' TABLE IS NOT EXIST THAN CREATE'
        $seocoreFollowTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_follows\'')->fetch();
        if (empty($seocoreFollowTable)) {
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocore_follows` (
        `follow_id` int(11) unsigned NOT NULL auto_increment,
        `resource_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
        `resource_id` int(11) unsigned NOT NULL,
        `poster_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
        `poster_id` int(11) unsigned NOT NULL,
        `creation_date` datetime NOT NULL,
        PRIMARY KEY  (`follow_id`),
        KEY `resource_type` (`resource_type`, `resource_id`),
        KEY `poster_type` (`poster_type`, `poster_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;");
        }

        $select = new Zend_Db_Select($db);
        $advancedactivity = $select->from('engine4_core_modules', 'name')
                ->where('name = ?', 'advancedactivity')
                ->query()
                ->fetchcolumn();

        $is_enabled = $select->query()->fetchObject();
        if (!empty($advancedactivity)) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`) VALUES ("follow_sitestore_store", "sitestore", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:store}: {body:$body}\', 1, 5, 1, 1, 1, 1, 1)');
        } else {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("follow_sitestore_store", "sitestore", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:store}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        }
        //END FOLLOW WORK
        // ADD 'CREATION_DATE' FIELDS IN CORE_LIKE TABLE.
        $type_array = $db->query("SHOW COLUMNS FROM engine4_core_likes LIKE 'creation_date'")->fetch();
        if (empty($type_array)) {
            $run_query = $db->query("ALTER TABLE `engine4_core_likes` ADD `creation_date` DATETIME NOT NULL");
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        // CODE FOR INCREASE THE SIZE OF engine4_activity_stream's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_stream LIKE 'target_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_stream` CHANGE `target_type` `target_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_stream LIKE 'object_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_stream` CHANGE `object_type` `object_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_actions LIKE 'object_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_actions` CHANGE `object_type` `object_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_allow's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_allow LIKE 'resource_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_allow` CHANGE `resource_type` `resource_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_attachments's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_attachments LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_attachments` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'subject_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `subject_type` `subject_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_actiontypes's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_actiontypes LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_actiontypes` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'object_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `object_type` `object_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'label'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_core_menuitems` CHANGE `label` `label` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        $storeTime = time();
        $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
		('sitestore.basetime', $storeTime ),
		('sitestore.isvar', 0 ),
		('sitestore.filepath', 'Sitestore/controllers/license/license2.php');");

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitestore_index_map')
                ->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitestore_index_map',
                'displayname' => 'Browse Stores Locations',
                'title' => 'Browse Stores Locations',
                'description' => 'Browse Stores Locations',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            //CONTAINERS
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => Null,
                'order' => 2,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'params' => '',
            ));
            $top_middle_id = $db->lastInsertId('engine4_core_content');

            //INSERT MAIN - MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // Top Middle
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestore.browsenevigation-sitestore',
                'parent_content_id' => $top_middle_id,
                'order' => 1,
                'params' => '',
            ));

            //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestore.location-search',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestore.browselocation-sitestore',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":"true"}',
            ));
        }

        // CODE FOR CREATING TABLES FOR PRODUCT COMBINATION WORK

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_combinations'")->fetch();
        if (empty($is_table_exist)) {
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_combinations` (
                  `combination_id` int(11) NOT NULL AUTO_INCREMENT,
                  `status` tinyint(4) NOT NULL DEFAULT '1',
                  `quantity` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`combination_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_combination_attributes'")->fetch();
        if (empty($is_table_exist)) {
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_combination_attributes` (
                  `attribute_id` int(11) NOT NULL AUTO_INCREMENT,
                  `product_id` int(11) NOT NULL,
                  `field_id` int(11) NOT NULL,
                  `combination_attribute_id` int(11) NOT NULL,
                  `price_increment` tinyint(4) NOT NULL DEFAULT '1',
                  `price` decimal(16,2) NOT NULL DEFAULT '0.00',
                  `order` int(11) NOT NULL,
                  PRIMARY KEY (`attribute_id`),
                  UNIQUE KEY `product_id` (`product_id`,`field_id`,`combination_attribute_id`)
                  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_combination_attributes_map'")->fetch();
        if (empty($is_table_exist)) {
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_combination_attributes_map` (
                  `map_id` int(11) NOT NULL AUTO_INCREMENT,
                  `combination_id` int(11) NOT NULL,
                  `attribute_id` int(11) NOT NULL,
                  PRIMARY KEY (`map_id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");

            // ADMIN BASED PRODUCT DASHBOARD WORK 
            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_index_edit', 'sitestoreproduct', 'Edit Info', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '1');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_index_overview', 'sitestoreproduct', 'Overview', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '2');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_siteform_index', 'sitestoreproduct', 'Product Attributes', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '3');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_siteform_productcategoryattribute', 'sitestoreproduct', 'Product Variations', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '4');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_dashboard_changephoto', 'sitestoreproduct', 'Profile Picture', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '5');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_dashboard_contact', 'sitestoreproduct', 'Contact Details', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '6');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_album_editphotos', 'sitestoreproduct', 'Photos', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '7');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_videoedit_edit', 'sitestoreproduct', 'Videos', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '8');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_dashboard_productdocument', 'sitestoreproduct', 'Manage Documents', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '9');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_dashboard_metadetail', 'sitestoreproduct', 'Meta Keywords', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '10');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_index_editstyle', 'sitestoreproduct', 'Edit Style', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '11');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_printingtag_printtag', 'sitestoreproduct', 'Print Tag', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '12');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_dashboard_producthistory', 'sitestoreproduct', 'History', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '13');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_files_index', 'sitestoreproduct', 'Downloadable Information', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '3');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_product_bundleproductattributes', 'sitestoreproduct', 'Product Attributes', 'Sitestoreproduct_Plugin_Dashboardmenus', '', 'sitestoreproduct_dashboard', NULL, '1', '0', '4');");


            $db->query("INSERT IGNORE INTO `engine4_core_menus` (`name` ,`type` ,`title` ,`order`)VALUES ('sitestoreproduct_dashboard', 'standard', 'Stores - Product Dashboard Menu', '999');");

            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestore_store_fields_meta LIKE 'display_bill'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestore_store_fields_meta` ADD `display_bill` TINYINT NULL DEFAULT '0' ; ");
            }
        }
    }

    protected function _getSitestoreadmincontactInstaller($db) {
        //CODE FOR INCREASE THE SIZE OF engine4_core_menuitems's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'label'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_core_menuitems` CHANGE `label` `label` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_core_menuitems's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'menu'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_core_menuitems` CHANGE `menu` `menu` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }
    }

    protected function _getSitestorealbumInstaller($db) {
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_settings')
                ->where('name = ?', 'sitestore.feed.type');
        $info = $select->query()->fetch();
        $enable = 1;
        if (!empty($info))
            $enable = $info['value'];
        $db->query('INSERT IGNORE INTO  `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES("sitestorealbum_admin_photo_new", "sitestorealbum", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title}:", ' . $enable . ', 6, 2, 1, 1, 1, 1)');
    }

    protected function _getSitestoreinviteInstaller($db) {
//    $select = new Zend_Db_Select($db);
//    $select
//            ->from('engine4_core_settings', array('value'))
//            ->where('name = ?', 'storeinvite.show.webmail');
//    $inviteModules = $select->query()->fetchColumn();
//    if (!empty($inviteModules)) {
//      $webmail_values = unserialize($inviteModules);
//      //IF FACEBOOK_MAIL DOES NOT EXIST IN TABLE THEN INSERT THIS 
//      if (!in_array('facebook_mail', $webmail_values)) {
//        $webmail_values[] = 'facebook_mail';
//      }
//      //IF LINKEDIN MAIL DOES NOT EXIST IN TABLE THEN INSERT THIS  
//      if (!in_array('linkedin_mail', $webmail_values)) {
//        $webmail_values[] = 'linkedin_mail';
//      }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_settings', array('value'))
                ->where('name = ?', 'storeinvite.show.webmail');
        $inviteModules = $select->query()->fetchColumn();
        if (!empty($inviteModules)) {
            $webmail_values = unserialize($inviteModules);
            //IF FACEBOOK_MAIL DOES NOT EXIST IN TABLE THEN INSERT THIS 
            if (!in_array('facebook_mail', $webmail_values)) {
                $webmail_values[] = 'facebook_mail';
            }
            //IF LINKEDIN MAIL DOES NOT EXIST IN TABLE THEN INSERT THIS  
            if (!in_array('linkedin_mail', $webmail_values)) {
                $webmail_values[] = 'linkedin_mail';
            }
            //IF TWITTER MAIL DOES NOT EXIST IN TABLE THEN INSERT THIS  
            if (!in_array('twitter_mail', $webmail_values)) {
                $webmail_values[] = 'twitter_mail';
            }
            $webmail_values = serialize($webmail_values);


            $db->query("UPDATE `engine4_core_settings` SET `value` = '$webmail_values' WHERE `engine4_core_settings`.`name` = 'storeinvite.show.webmail' LIMIT 1;");
        } else {
            $db->query('INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
				("storeinvite.show.webmail", \'a:7:{i:0;s:5:"gmail";i:1;s:5:"yahoo";i:2;s:11:"window_mail";i:3;s:3:"aol";i:4;s:13:"facebook_mail";i:5;s:13:"linkedin_mail";i:6;s:12:"twitter_mail";}\');'
            );
        }
        //}
    }

    protected function _getSitestoreofferInstaller($db) {
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitestoreoffer')
                ->where('version < ?', '4.2.6');
        $is_enabled = $select->query()->fetchObject();

        if ($is_enabled) {
            $db->update('engine4_activity_actiontypes', array('is_generated' => '1'), array('type = ?' => 'sitestoreoffer_home'));
        }




        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_settings')
                ->where('name = ?', 'sitestore.feed.type');
        $info = $select->query()->fetch();
        $enable = 1;
        if (!empty($info))
            $enable = $info['value'];
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES("sitestoreoffer_admin_new", "sitestoreoffer", "{item:$object} added a new offer:", ' . $enable . ', 6, 2, 1, 1, 1, 1)');
    }

    protected function _getSitestoreurlInstaller($db) {
        $table_exist = $db->query('SHOW TABLES LIKE \'engine4_seaocore_bannedpageurls\'')->fetch();
        if (empty($table_exist)) {
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocore_bannedpageurls` (
										`bannedpageurl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
										`word` varchar(255) COLLATE utf8_Unicode_ci NOT NULL,
										PRIMARY KEY (`bannedpageurl_id`),
										UNIQUE KEY `word` (`word`)
									) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");

            $db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`word`) VALUES
										('music'),('polls'),('blogs'),('videos'),	('classifieds'),('albums'),('events'),	('groups'),('group'),
										('forums'),('invite'),('recipeitems'),('ads'),	('likes'),('documents'),('sitepage'),
										('sitepagepoll'),('sitepageoffer'),('sitepagevideo'),('sitepagedocument'),('sitepagenote'),
										('sitepageevent'),('sitepagemusic'),('sitepageinvite'),('sitepagereview'),('sitepagebadge'),
									  ('sitepageform'),('sitepagealbum'),('sitepagediscussion'),('sitebusiness'),
										('sitebusinesspoll'),('sitebusinessoffer'),('sitebusinessvideo'),('sitebusinessdocument'),('sitebusinessnote'),
										('sitebusinessevent'),('sitebusinessmusic'),('sitebusinessinvite'),('sitebusinessreview'),('sitebusinessbadge'),
									  ('sitebusinessform'),('sitebusinessalbum'),('sitebusinessdiscussion'),('sitegroup'),
										('sitegrouppoll'),('sitegroupoffer'),('sitegroupvideo'),('sitegroupdocument'),('sitegroupnote'),
										('sitegroupevent'),('sitegroupmusic'),('sitegroupinvite'),('sitegroupreview'),('sitegroupbadge'),
									  ('sitegroupform'),('sitegroupalbum'),('sitegroupdiscussion'),('sitestore'),
										('sitestorepoll'),('sitestoreoffer'),('sitestorevideo'),('sitestoredocument'),('sitestorenote'),
										('sitestoreevent'),('sitestoremusic'),('sitestoreinvite'),('sitestorereview'),('sitestorebadge'),
									  ('sitestoreform'),('sitestorealbum'),('sitestorediscussion'),('recipe'),('sitelike'),('suggestion'),('advanceslideshow'),('feedback'),('grouppoll'),('groupdocumnet'),('sitealbum'),('siteslideshow'),('userconnection'),('communityad'),('list'),('article'),
										('listing'),('store'),('page-videos'),('pageitem'),('pageitems'),('page-events'),('page-documents'),('page-offers'),('page-notes'),('page-invites'),('page-form'),('page-music'),
										('page-reviews'),('businessitem'),('businessitems'),('business-events'),('business-documents'),('business-offers'),('business-notes'),('business-invites'),('business-form'),('business-music'),
										('business-reviews'),('group-videos'),('groupitem'),('groupitems'),('group-events'),('group-documents'),('group-offers'),('group-notes'),('group-invites'),('group-form'),('group-music'),('group-reviews'),('store-videos'),('storeitem'),('storeitems'),('store-events'),
									  ('store-documents'),('store-offers'),('store-notes'),('store-invites'),('store-form'),('store-music'),('store-reviews'),('listingitems'),('market'),('document'),('pdf'),('pokes'),('facebook'),('album'),('photo'),('files'),('file'),('page'),
									  ('store'),('backup'),('question'),('answer'),('questions'),('answers'),('newsfeed'),('birthday'),('wall'),('profiletype'),('memberlevel'),('members'),('member'),('memberlevel'),
					          ('level'),('slideshow'),('seo'),('xml'),('cmspages'),('favoritepages'),('help'),('rss'),
										('stories'),('story'),('visits'),('points'),('vote'),('advanced'),('listingitem');");
        }

        $table_url_exist = $db->query('SHOW TABLES LIKE \'engine4_sitepage_bannedpageurls\'')->fetch();
        if (!empty($table_url_exist)) {
            $db->query("RENAME TABLE `engine4_sitepage_bannedpageurls` TO `engine4_seaocore_bannedpageurls` ");
        }


        //CHECK THAT SITESTORE PLUGIN IS INSTALLED OR NOT
//    $select = new Zend_Db_Select($db);
//    $select
//            ->from('engine4_core_modules')
//            ->where('name = ?', 'sitestoreurl')
//            ->where('enabled = ?', 1);
//    $check_sitestoreurl = $select->query()->fetchObject();
//    if (empty($check_sitestoreurl)) {
//      $includeModules = array("sitepage" => "sitepage", "sitepagedocument" => 'Documents', "sitepageoffer" => 'Offers', "sitepageform" => "Form", "sitepagediscussion" => "Discussions", "sitepagenote" => "Notes", "sitepagealbum" => "Photos", "sitepagevideo" => "Videos", "sitepageevent" => "Events", "sitepagepoll" => "Polls", "sitepageinvite" => "Invite & Promote", "sitepagebadge" => "Badges", "sitepagelikebox" => "External Badge", "sitepagemusic" => "Music", "sitegroup" => "sitegroup", "sitegroupdocument" => 'Documents', "sitegroupoffer" => 'Offers', "sitegroupform" => "Form", "sitegroupdiscussion" => "Discussions", "sitegroupnote" => "Notes", "sitegroupalbum" => "Photos", "sitegroupvideo" => "Videos", "sitegroupevent" => "Events", "sitegrouppoll" => "Polls", "sitegroupinvite" => "Invite & Promote", "sitegroupbadge" => "Badges", "sitegrouplikebox" => "External Badge", "sitegroupmusic" => "Music", "sitestore" => "sitestore", "sitestoredocument" => 'Documents', "sitestoreoffer" => 'Offers', "
//sitestoreform" => "Form", "sitestorediscussion" => "Discussions", "sitestorenote" => "Notes", "sitestorealbum" => "Photos", "sitestorevideo" => "Videos", "sitestoreevent" => "Events", "sitestorepoll" => "Polls", "sitestoreinvite" => "Invite & Promote", "sitestorebadge" => "Badges", "sitestorelikebox" => "External Badge", "sitestoremusic" => "Music", "sitebusiness" => "sitebusiness", "sitebusinessdocument" => 'Documents', "sitebusinessoffer" => 'Offers', "sitebusinessform" => "Form", "sitebusinessdiscussion" => "Discussions", "sitebusinessnote" => "Notes", "sitebusinessalbum" => "Photos", "sitebusinessvideo" => "Videos", "sitebusinessevent" => "Events", "sitebusinesspoll" => "Polls", "sitebusinessinvite" => "Invite & Promote", "sitebusinessbadge" => "Badges", "sitebusinesslikebox" => "External Badge", "sitebusinessmusic" => "Music", "list" => "list");
//      $select = new Zend_Db_Select($db);
//      $select
//              ->from('engine4_core_modules', 'name')
//              ->where('enabled = ?', 1);
//      $enableAllModules = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
//      $enableModules = array_intersect(array_keys($includeModules), $enableAllModules);
//
//      foreach ($enableAllModules as $moduleName) {
//        if (!in_array($moduleName, $enableModules)) {
//          $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
//          $contentItem = array();
//          if (@file_exists($file_path)) {
//            $ret = include $file_path;
//            $is_exist = array();
//            if (isset($ret['routes'])) {
//              foreach ($ret['routes'] as $item) {
//                $route = $item['route'];
//                $route_array = explode('/', $route);
//                $route_url = strtolower($route_array[0]);
//
//                if (!empty($route_url) && !in_array($route_url, $is_exist)) {
//                  $db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`word`) VALUES ('" . $route_url . "')");
//                }
//                $is_exist[] = $route_url;
//              }
//            }
//          }
//        } else {
//          if ($moduleName == 'sitepage' || $moduleName == 'sitestore' || $moduleName == 'sitegroup') {
//            $name = $moduleName . '.manifestUrlS';
//          } else {
//            $name = $moduleName . '.manifestUrl';
//          }
//          $select = new Zend_Db_Select($db);
//          $select
//                  ->from('engine4_core_settings', 'value')
//                  ->where('name = ?', $name)
//                  ->limit(1);
//          $route_url = strtolower($select->query()->fetchAll(Zend_Db::FETCH_COLUMN));
//          if (!empty($route_url)) {
//            $db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','" . $route_url . "')");
//          }
//        }
//      }
//    }

        $this->bannedUrlWork();
    }

    //RUN THIS CODE ON FIRST TIME INSTALLATION OF STORE URL PLUGIN
    protected function bannedUrlWork() {

        //GET DB
        $db = $this->getDb();

        $bannedpageurlsTableExist = $db->query('SHOW TABLES LIKE \'engine4_seaocore_bannedpageurls\'')->fetch();
        $listingtypeTableExist = $db->query('SHOW TABLES LIKE \'engine4_sitereview_listingtypes\'')->fetch();
        $select = new Zend_Db_Select($db);
        $isActivate = $select->from('engine4_core_settings', 'name')
                ->where('name = ?', 'sitestoreurl.is.enable')
                ->where('value = ?', 1)
                ->query()
                ->fetchColumn();
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_modules', 'name')
                ->where('name = ?', 'sitestoreurl')
                ->query()
                ->fetchcolumn();
        $isSitestoreurlenabled = $select->query()->fetchObject();

        if (!empty($bannedpageurlsTableExist) && !empty($listingtypeTableExist) && !empty($isActivate) && !empty($isSitestoreurlenabled)) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_sitereview_listingtypes', array('slug_singular', 'slug_plural'));
            $listingTypeDatas = $select->query()->fetchAll();
            foreach ($listingTypeDatas as $listingTypeData) {

                $urls = array("$listingTypeData->slug_plural", "$listingTypeData->slug_singular");

                foreach ($urls as $url) {

                    $bannedWordsNew = preg_split('/\s*[,\n]+\s*/', $url);

                    $words = array_map('strtolower', array_filter(array_values($bannedWordsNew)));

                    $select = new Zend_Db_Select($db);
                    $data = $select->from('engine4_seaocore_bannedpageurls', 'word')
                            ->query()
                            ->fetchAll(Zend_Db::FETCH_COLUMN);

                    if (in_array($words[0], $data)) {
                        return;
                    }

                    $words = array_map('strtolower', array_filter(array_values($words)));

                    $select = new Zend_Db_Select($db);
                    $data = $select
                            ->from('engine4_seaocore_bannedpageurls', 'word')
                            ->query()
                            ->fetchAll(Zend_Db::FETCH_COLUMN);

                    $newWords = array_diff($words, $data);
                    foreach ($newWords as $newWord) {
                        $db->insert('engine4_seaocore_bannedpageurls', array(
                            'word' => $newWord,
                        ));
                    }
                }
            }
        }
    }

    protected function _getSitestorevideoInstaller($db) {
        $this->_checkFfmpegPath('sitestorevideo');

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_settings')
                ->where('name = ?', 'sitestore.feed.type');
        $info = $select->query()->fetch();
        $enable = 1;
        if (!empty($info))
            $enable = $info['value'];
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES("sitestorevideo_admin_new", "sitestorevideo", "{item:$object} posted a new video:",' . $enable . ', 6, 2, 1, 1, 1, 1)');
    }

    // INTEGRATE STORE PLUGIN WITH SUGGESTION
    protected function _integrateStoreWithSuggestion($db) {
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'suggestion')
                ->where('enabled = ?', '1');
        $sitestore_temp = $select->query()->fetchObject();
        if (!empty($sitestore_temp)) {
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name` , `module` , `label` , `plugin` ,`params`, `menu`, `enabled`, `custom`, `order`) VALUES ("sitestore_gutter_suggesttofriend", "suggestion", "Suggest to Friends", \'Sitestore_Plugin_Menus::showSitestore\', \'{"route":"suggest_to_friend_link","class":"buttonlink icon_review_friend_suggestion smoothbox", "type":"popup"}\', "sitestore_gutter", 1, 0, 999 )');

            $select = new Zend_Db_Select($db);
            $select->from('engine4_activity_notificationtypes')->where('type = ?', 'sitestore_suggestion');
            $fetch = $select->query()->fetchObject();
            if (empty($fetch)) {
                $db->query('
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`,`is_request`, `handler`, `default`)VALUES (
"sitestore_suggestion", "suggestion", \'{item:$subject} has suggested to you a {item:$object:store}.\', "1", "suggestion.widget.get-notify", "1" )     
');
            }

            $select = new Zend_Db_Select($db);
            $select->from('engine4_suggestion_module_settings')->where('module = ?', 'sitestore');
            $fetch = $select->query()->fetchObject();
            if (empty($fetch)) {
                $db->query('
INSERT IGNORE INTO `engine4_suggestion_module_settings` (`module`, `item_type`, `field_name`, `owner_field`, `item_title`, `button_title`, `enabled`, `notification_type`, `quality`, `link`, `popup`, `recommendation`, `default`, `settings`) VALUES
("sitestore", "sitestore_store", "store_id", "owner_id", "Store", "View Store", 1, "sitestore_suggestion", 0, 1, 1, 1, 1, \'a:0:{}\');
');
            }

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_mailtemplates')->where('type = ?', 'notify_siteestore_suggestion');
            $fetch = $select->query()->fetchObject();
            if (empty($fetch)) {
                $db->query('
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`)VALUES 
("notify_sitestore_suggestion", "suggestion", "[suggestion_sender], [suggestion_entity], [email], [link]"
);    
');
            }
        }
    }

    // INTEGRATE STORE PLUGIN WITH FACEBOOK
    protected function _integrateStoreWithFacebook($db) {
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'facebookse')
                ->where('version  > ?', '4.2.7p1')
                ->where('enabled = ?', '1');
        $facebookse = $select->query()->fetchObject();
        if (!empty($facebookse)) {

            //CHECK IF THIS ENTRY ALREADY EXIST THERE
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_facebookse_mixsettings')
                    ->where('resource_type = ?', 'sitestore_store');

            $sitestore_temp = $select->query()->fetchObject();
            if (empty($sitestore_temp))
                $db->query("INSERT IGNORE INTO `engine4_facebookse_mixsettings` (`module`, `module_name`, `resource_type`, `resource_id`, `owner_field`, `module_title`, `module_description`, `enable`, `send_button`, `like_type`, `like_faces`, `like_width`, `like_font`, `like_color`, `layout_style`, `opengraph_enable`, `title`, `photo_id`, `description`, `types`, `fbadmin_appid`, `commentbox_enable`, `commentbox_privacy`, `commentbox_width`, `commentbox_color`, `module_enable`, `default`, `activityfeed_type`, `streampublish_message`, `streampublish_story_title`, `streampublish_link`, `streampublish_caption`, `streampublish_description`, `streampublish_action_link_text`, `streampublish_action_link_url`, `streampublishenable`, `activityfeedtype_text`) VALUES('sitestore', 'Stores / Marketplace - Stores', 'sitestore_store', 'store_id', 'owner_id', 'title', 'body', 1, 1, 'like', 0, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 1, 1, 450, 'light', 1, 1, 'sitestore_new', 'View my Store!', '{*sitestore_title*}', '{*
sitestore_url*}', '{*actor*} created a new Store  on {*site_title*}: {*site_url*}.', '{*sitestore_desc*}', 'View Store', '{*sitestore_url*}', 1, 'Creating a Stores / Marketplace - Store')");
        }
    }

    // INTEGRATE PRODUCT PLUGIN WITH FACEBOOK
    protected function _integrateProductWithFacebook($db) {
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'facebookse')
                ->where('version  > ?', '4.2.7p1')
                ->where('enabled = ?', '1');
        $facebookse = $select->query()->fetchObject();
        if (!empty($facebookse)) {

            //CHECK IF THIS ENTRY ALREADY EXIST THERE
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_facebookse_mixsettings')
                    ->where('resource_type = ?', 'sitestoreproduct_product');

            $sitestore_temp = $select->query()->fetchObject();
            if (empty($sitestore_temp))
                $db->query("INSERT IGNORE INTO `engine4_facebookse_mixsettings` (`module`, `module_name`, `resource_type`, `resource_id`, `owner_field`, `module_title`, `module_description`, `enable`, `send_button`, `like_type`, `like_faces`, `like_width`, `like_font`, `like_color`, `layout_style`, `opengraph_enable`, `title`, `photo_id`, `description`, `types`, `fbadmin_appid`, `commentbox_enable`, `commentbox_privacy`, `commentbox_width`, `commentbox_color`, `module_enable`, `default`, `activityfeed_type`, `streampublish_message`, `streampublish_story_title`, `streampublish_link`, `streampublish_caption`, `streampublish_description`, `streampublish_action_link_text`, `streampublish_action_link_url`, `streampublishenable`, `activityfeedtype_text`) VALUES('sitestoreproduct', 'Stores / Marketplace - Products', 'sitestoreproduct_product', 'product_id', 'owner_id', 'title', 'body', 1, 1, 'like', 1, 450, '', 'light', 'standard', 0, '', 0, '', '', 1, 0, 1, 450, 'light', 1, 1, 'sitestoreproduct_new', 'View my Product!', '{
*sitestoreproduct_title*}', '{*sitestoreproduct_url*}',  '{*actor*} created a new Product: {*sitestoreproduct_title*} on Store: {*sitestore_title*} on {*site_title*}: {*site_url*}.', '{*sitestoreproduct_desc*}', 'View Product', '{*sitestoreproduct_url*}', 1, 'Creating a Stores / Marketplace - Product')");
        }
    }

    // INTEGRATE STORE PRODUCT PLUGIN WITH SUGGESTION
    protected function _integrateProductWithSuggestion($db) {
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'suggestion')
                ->where('enabled = ?', '1');
        $sitestoreproduct_temp = $select->query()->fetchObject();
        if (!empty($sitestoreproduct_temp)) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_activity_notificationtypes')->where('type = ?', 'sitestoreproduct_suggestion');
            $fetch = $select->query()->fetchObject();
            if (empty($fetch)) {
                $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`,`is_request`, `handler`, `default`)VALUES (
"sitestoreproduct_suggestion", "suggestion", \'{item:$subject} has suggested to you a {item:$object:product}.\', "1", "suggestion.widget.get-notify", "1" )     
');
            }

            $select = new Zend_Db_Select($db);
            $select->from('engine4_suggestion_module_settings')->where('module = ?', 'sitestoreproduct');
            $fetch = $select->query()->fetchObject();
            if (empty($fetch)) {
                $db->query('INSERT IGNORE INTO `engine4_suggestion_module_settings` (`module`, `item_type`, `field_name`, `owner_field`, `item_title`, `button_title`, `enabled`, `notification_type`, `quality`, `link`, `popup`, `recommendation`, `default`, `settings`) VALUES
("sitestoreproduct", "sitestoreproduct_product", "product_id", "owner_id", "Product", "View Product", 1, "sitestoreproduct_suggestion", 0, 1, 1, 1, 1, \'a:0:{}\');
');
            }

            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_mailtemplates')->where('type = ?', 'notify_siteestore_suggestion');
            $fetch = $select->query()->fetchObject();
            if (empty($fetch)) {
                $db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`)VALUES 
("notify_sitestoreproduct_suggestion", "suggestion", "[suggestion_sender], [suggestion_entity], [email], [link]"
);    
');
            }
        }
    }

    protected function _getSitestoreproductInstaller($db) {

        $db->query("INSERT IGNORE INTO  `engine4_core_mailtemplates` (`type`, `module`, `vars`)
VALUES (
'SITESTOREPRODUCT_ASKOPINION_EMAIL',  'sitestoreproduct',  '[host],[email],[sender],[message][object_link]')");

        $db->query("
CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(128) NOT NULL,
  `sec_order` int(2) NOT NULL,
  `store_id` int(11) NOT NULL,
  PRIMARY KEY (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");
        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_products'")->fetch();
        if (!empty($is_table_exist)) {
            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_products LIKE 'section_id'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_products` ADD `section_id` INT NOT NULL DEFAULT '0' AFTER `photo_id`;");
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_orders'")->fetch();
        if (!empty($is_table_exist)) {
            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_orders LIKE 'is_private_order'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_orders` ADD `is_private_order` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '0 => Send activity feed, 1 => Not send activity feed'");
            }
        }

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_otherinfo'")->fetch();
        if (!empty($is_table_exist)) {
            $type_array = $db->query("SHOW FIELDS FROM engine4_sitestoreproduct_otherinfo where Field ='phone'")->fetch();

            if (!empty($type_array) && !empty($type_array['Type']) && strstr($type_array['Type'], "int")) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_otherinfo` CHANGE `phone` `phone` VARCHAR( 256 ) NULL DEFAULT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_actiontypes's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_actiontypes LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_actiontypes` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_core_mailtemplates's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_core_mailtemplates LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 128) {
                $run_query = $db->query("ALTER TABLE `engine4_core_mailtemplates` CHANGE `type` `type` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }
        //CHANGE IN SITESTORE PACKAGES TABLE
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestore_packages'")->fetch();
        if (!empty($table_exist)) {
            $column_exist = $db->query("SHOW COLUMNS FROM `engine4_sitestore_packages` LIKE 'store_settings'")->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE  `engine4_sitestore_packages` ADD  `store_settings` TEXT COLLATE utf8_unicode_ci;");
//        $filesize = (int) ini_get('upload_max_filesize') * 1024;
//        $sitestoreproductValues['product_type'] = array('simple', 'configurable', 'virtual', 'grouped', 'bundled', 'downloadable');
//        $sitestoreproductValues['sitestoreproduct_main_files'] = 5;
//        $sitestoreproductValues['sitestoreproduct_sample_files'] = 5;
//        $sitestoreproductValues['filesize_main'] = $filesize;
//        $sitestoreproductValues['filesize_sample'] = $filesize;
//        $sitestoreproductValues['max_product'] = 50;
//        $sitestoreproductValues['comission_handling'] = 1;
//        $sitestoreproductValues['comission_fee'] = '';
//        $sitestoreproductValues['comission_rate'] = 5;
//        $sitestoreproductValues['allow_selling_products'] = 1;
//        $sitestoreproductValues['online_payment_threshold'] = 0;
//        $sitestoreproductValues['transfer_threshold'] = 100;
//        $storeSettingValues = @serialize($sitestoreproductValues);
//        $db->query("UPDATE `engine4_sitestore_packages` SET `store_settings` = '$storeSettingValues'");
            }
        }

        //IF 'engine4_seaocore_follows' TABLE IS NOT EXIST THAN CREATE'
        $seocoreFollowTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_follows\'')->fetch();
        if (empty($seocoreFollowTable)) {
            $db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocore_follows` (
        `follow_id` int(11) unsigned NOT NULL auto_increment,
        `resource_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
        `resource_id` int(11) unsigned NOT NULL,
        `poster_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
        `poster_id` int(11) unsigned NOT NULL,
        `creation_date` datetime NOT NULL,
        PRIMARY KEY  (`follow_id`),
        KEY `resource_type` (`resource_type`, `resource_id`),
        KEY `poster_type` (`poster_type`, `poster_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;");

            $select = new Zend_Db_Select($db);
            $advancedactivity = $select->from('engine4_core_modules', 'name')
                    ->where('name = ?', 'advancedactivity')
                    ->query()
                    ->fetchcolumn();

            $is_enabled = $select->query()->fetchObject();
            if (!empty($advancedactivity)) {
                $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`) VALUES ("follow_sitestoreproduct_wishlist", "sitestoreproduct", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:wishlist}: {body:$body}\', 1, 5, 1, 1, 1, 1, 1)');
            } else {
                $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("follow_sitestoreproduct_wishlist", "sitestoreproduct", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:wishlist}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_actions FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_actions LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_actions` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_actions FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notificationtypes LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 128) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_notificationtypes` CHANGE `type` `type` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_activity_stream FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_stream LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 64) {
                $run_query = $db->query("ALTER TABLE `engine4_activity_stream` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'name'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `name` `name` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_allow's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_allow LIKE 'resource_type'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_allow` CHANGE `resource_type` `resource_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }

        //CHANGE IN CORE COMMENT TABLE
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_core_comments'")->fetch();
        if (!empty($table_exist)) {
            $column_exist = $db->query("SHOW COLUMNS FROM `engine4_core_comments` LIKE 'parent_comment_id'")->fetch();
            if (empty($column_exist)) {
                $db->query("ALTER TABLE  `engine4_core_comments` ADD  `parent_comment_id` INT( 11 ) NOT NULL DEFAULT  '0';");
            }
        }

        //CODE FOR INCREASE THE SIZE OF engine4_authorization_allow's FIELD type
        $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_allow LIKE 'action'")->fetch();
        if (!empty($type_array)) {
            $varchar = $type_array['Type'];
            $length_varchar = explode("(", $varchar);
            $length = explode(")", $length_varchar[1]);
            $length_type = $length[0];
            if ($length_type < 32) {
                $run_query = $db->query("ALTER TABLE `engine4_authorization_allow` CHANGE `action` `action` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
            }
        }


        $table_categories = $db->query('SHOW TABLES LIKE \'engine4_sitestoreproduct_categories\'')->fetch();
        if (!empty($table_categories)) {
            $db->query("ALTER TABLE `engine4_sitestoreproduct_categories` CHANGE  `top_content`  `top_content` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE  `bottom_content`  `bottom_content` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
        }

        $this->_checkFfmpegPath('sitestoreproduct');
    }

    protected function _createSocialEngineMobilePages($db) {
        // Mobile Stores Home
        // store
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitestore_mobi_home')
                ->limit(1);
        ;
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitestore_mobi_home',
                'displayname' => 'Mobile Stores Home',
                'title' => 'Mobile Stores Home',
                'description' => 'This is the mobile verison of a Stores home page.',
                'custom' => 0
            ));
            $store_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // widgets entry
            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitestore.browsenevigation-sitestore',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"title":"","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitestore.zerostore-sitestore',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitestore.search-sitestore',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitestore.recently-popular-random-sitestore',
                'parent_content_id' => $middle_id,
                'order' => 4,
                'params' => '{"title":"","titleCount":"true"}',
            ));
        }

        // Mobile Browse Stores
        // store
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitestore_mobi_index')
                ->limit(1);
        ;
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitestore_mobi_index',
                'displayname' => 'Mobile Browse Stores',
                'title' => 'Mobile Browse Stores',
                'description' => 'This is the mobile verison of a stores browse store.',
                'custom' => 0
            ));
            $store_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');


            // widgets entry
            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitestore.browsenevigation-sitestore',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"title":"","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitestore.search-sitestore',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true"}',
            ));
            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitestore.stores-sitestore',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":"true"}',
            ));
        }

        //
        // Mobile Stores Profile
        // store
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitestore_mobi_view')
                ->limit(1);
        ;
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitestore_mobi_view',
                'displayname' => 'Mobile Store Profile',
                'title' => 'Mobile Store Profile',
                'description' => 'This is the mobile verison of a listing profile.',
                'custom' => 0
            ));
            $store_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // widgets entry

            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitestore.title-sitestore',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"title":"","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitestore.mainphoto-sitestore',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true"}',
            ));


            // middle tabs
            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'parent_content_id' => $middle_id,
                'order' => 4,
                'params' => '{"max":"6"}',
            ));
            $tab_middle_id = $db->lastInsertId('engine4_core_content');


            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'seaocore.feed',
                'parent_content_id' => $tab_middle_id,
                'order' => 1,
                'params' => '{"title":"What\'s New","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitestore.info-sitestore',
                'parent_content_id' => $tab_middle_id,
                'order' => 2,
                'params' => '{"title":"Info","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitestore.overview-sitestore',
                'parent_content_id' => $tab_middle_id,
                'order' => 3,
                'params' => '{"title":"Overview","titleCount":"true"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitestore.location-sitestore',
                'parent_content_id' => $tab_middle_id,
                'order' => 4,
                'params' => '{"title":"Map","titleCount":"true"}',
            ));
        }

        //START: UPGRADE QUERIES
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitestore')
                ->where('version <= ?', '4.8.9p9')
                ->where('enabled = ?', 1);
        $checkVersion = $select->query()->fetchObject();
        if (!empty($checkVersion)) {

            $tableSitemobileMenus = $db->query("SHOW TABLES LIKE 'engine4_sitemobile_menus'")->fetch();
            if (!empty($tableSitemobileMenus)) {

                $db->query("INSERT IGNORE INTO `engine4_sitemobile_menus` (`name`,`type`,`title`) VALUES
('sitestore_manage_mobile_main','standard','Store Manage Main Menu')");
            }

            $tableSitemobileMenuItems = $db->query("SHOW TABLES LIKE 'engine4_sitemobile_menuitems'")->fetch();
            if (!empty($tableSitemobileMenuItems)) {

                $db->query("INSERT IGNORE INTO `engine4_sitemobile_menuitems` (`name`,`module`,`label`,`plugin`,`params`,`menu`,`submenu`,`custom`,`order`,`enable_mobile`,`enable_tablet`) VALUES
('sitestore_manage_mobile_main_orders','sitestore','Orders','Sitestore_Plugin_Menus','','sitestore_manage_mobile_main','',0,1,1,1),
('sitestore_manage_mobile_main_products','sitestore','Products','Sitestore_Plugin_Menus','','sitestore_manage_mobile_main','',0,2,1,1),
('sitestore_manage_mobile_main_shops','sitestore','Shops','Sitestore_Plugin_Menus','','sitestore_manage_mobile_main','',0,3,1,1);");
            }

            $tableSitemobileContent = $db->query("SHOW TABLES LIKE 'engine4_sitemobile_content'")->fetch();
            if (!empty($tableSitemobileContent)) {

                $db->query("UPDATE `engine4_sitemobile_content` SET `name` = 'sitestore.sitemobile-custom-managestores',`module` = 'sitestore'
WHERE `name` = 'core.content' AND `page_id` = (SELECT `page_id` FROM `engine4_sitemobile_pages` WHERE `name` = 'sitestore_index_manage');");
                $db->query("DELETE FROM `engine4_sitemobile_content`
WHERE (`name` = 'sitemobile.sitemobile-navigation' OR `name` = 'sitemobile.sitemobile-advancedsearch')
AND `page_id` = (SELECT `page_id` FROM `engine4_sitemobile_pages` WHERE `name` = 'sitestore_index_manage');");
            }

            $tableSitemobilePages = $db->query("SHOW TABLES LIKE 'engine4_sitemobile_pages'")->fetch();
            if (!empty($tableSitemobilePages)) {

                $db->query("UPDATE `engine4_sitemobile_pages` SET `name` = 'sitestoreproduct_product_manage' WHERE `displayname` = 'Stores - Manage Store Products';");
                $db->query("UPDATE `engine4_sitemobile_pages` SET `name` = 'sitestoreproduct_index_manage-order' WHERE `displayname` = 'Stores - Manage Store Orders';");
            }

            if (!empty($tableSitemobileContent)) {
                $db->query("UPDATE `engine4_sitemobile_content` SET `module` = 'sitemobile'
WHERE `name` = 'sitemobile.sitemobile-navigation' AND `page_id` = (SELECT `page_id` FROM `engine4_sitemobile_pages` WHERE `name` = 'sitestoreproduct_product_manage');");
                $db->query("UPDATE `engine4_sitemobile_content` SET `module` = 'sitemobile'
WHERE `name` = 'sitemobile.sitemobile-navigation' AND `page_id` = (SELECT `page_id` FROM `engine4_sitemobile_pages` WHERE `name` = 'sitestoreproduct_index_manage-order');");
            }

            if (!empty($tableSitemobilePages)) {
                $db->query("UPDATE `engine4_sitemobile_pages` SET `name` = 'sitestoreproduct_index_create-mobile' WHERE `displayname` = 'Stores - Create Store Product';");
            }

            if (!empty($tableSitemobileContent)) {
                $db->query("UPDATE `engine4_sitemobile_content` SET `module` = 'core'
WHERE `name` = 'core.content' AND `page_id` = (SELECT `page_id` FROM `engine4_sitemobile_pages` WHERE `name` = 'sitestoreproduct_index_create-mobile');");
            }
        }
    }

    public function _checkinWork() {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitetagcheckin')
                ->where('enabled = ?', 1);
        $is_sitetagcheckin_object = $select->query()->fetchObject();
        if (!empty($is_sitetagcheckin_object)) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES("sitetagcheckin_ssal_photo_new", "sitetagcheckin", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title} - {var:$prefixadd} {var:$location}.", 1, 5, 1, 3, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES("sitetagcheckin_store_tagged", "sitetagcheckin", "{item:$subject} mentioned your store with a {item:$object:$label}.", "0", "", "1")');
            $db->query('INSERT IGNORE INTO `engine4_sitetagcheckin_contents` (`module`, `resource_type`, `resource_id`, `value`, `default`, `enabled`) VALUES("sitestore", "sitestore_store", "store_id", "1", "1", "1")');
        }
    }

    protected function _getSitestoreformInstaller($db) {

        $column_tab_exist = $db->query('SHOW COLUMNS FROM engine4_sitestoreform_sitestoreforms LIKE \'offer_tab_name\'')->fetch();
        if (empty($column_tab_exist)) {
            $db->query('ALTER TABLE `engine4_sitestoreform_sitestoreforms` ADD `offer_tab_name` VARCHAR( 32 ) NOT NULL AFTER `activemessage`');
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_menuitems')
                ->where('name = ?', 'sitestoreform_admin_main_fields');
        $queary_info = $select->query()->fetchAll();
        if (empty($queary_info)) {
            $db->insert('engine4_core_menuitems', array(
                'name' => 'sitestoreform_admin_main_fields',
                'module' => 'sitestoreform',
                'label' => 'Form Questions',
                'plugin' => '',
                'params' => '{"route":"admin_default","module":"sitestoreform","controller":"fields","action":"index"}',
                'menu' => 'sitestoreform_admin_main',
                'submenu' => '',
                'order' => 3,
            ));
        }
    }

    public function onPostInstall() {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if (!empty($is_sitemobile_object)) {
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitestore')
                    ->where('version <= ?', '4.7.0p2');
            $is_sitestore_object = $select->query()->fetchObject();
            if (!empty($is_sitestore_object)) {
                $sitemobileModuleTable = $db->query('SHOW TABLES LIKE \'engine4_sitemobile_modules\'')->fetch();
                if (!empty($sitemobileModuleTable)) {
                    $db->query("DELETE FROM `engine4_sitemobile_modules` WHERE `name` LIKE  '%sitestore%';");
                }
            }

            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES ('sitestore','1')");
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_sitemobile_modules')
                    ->where('name = ?', 'sitestore')
                    ->where('integrated = ?', 0);
            $is_sitemobile_object = $select->query()->fetchObject();
            if ($is_sitemobile_object) {
                $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
                $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
                if ($controllerName == 'manage' && $actionName == 'install') {
                    $view = new Zend_View();
                    $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitestore/integrated/0/redirect/install');
                }
            }
        }

        //Work for the word changes in the store plugin .csv file.
        $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        if ($controllerName == 'manage' && ($actionName == 'install' || $actionName == 'query')) {
            $view = new Zend_View();
            $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            if ($actionName == 'install') {
                $redirector->gotoUrl($baseUrl . 'admin/sitestore/settings/language/redirect/install');
            } else {
                $redirector->gotoUrl($baseUrl . 'admin/sitestore/settings/language/redirect/query');
            }
        }
    }

    public function _offertocoupon($db) {
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreoffer_offers'")->fetch();
        if (!empty($table_exist)) {
            $is_object_thumbColumn = $db->query("SHOW COLUMNS FROM `engine4_sitestoreoffer_offers` LIKE 'discount_amount'")->fetch();
            if (empty($is_object_thumbColumn)) {
                $db->query('ALTER TABLE `engine4_sitestoreoffer_offers` ADD `discount_type` TINYINT NOT NULL ,
    ADD `discount_amount` decimal(16,2) NOT NULL ,
    ADD `minimum_purchase` bigint(20) DEFAULT NULL ,
    ADD `product_ids` VARCHAR( 128 ) NOT NULL ,
    ADD `claim_user_count` int(11) DEFAULT NULL ,
    ADD `start_time` DATETIME NOT NULL ,
    ADD `public` TINYINT NOT NULL DEFAULT "0",
    ADD `approved` TINYINT NOT NULL DEFAULT "1",
    ADD `min_product_quantity` int(11) DEFAULT NULL ,
    ADD `status` TINYINT NOT NULL ');

                /* For Already created Offers */
                $select = new Zend_Db_Select($db);
                $select
                        ->from('engine4_sitestoreoffer_offers', array('offer_id'));
                $coupon_result = $select->query()->fetchAll();
                foreach ($coupon_result as $coupon) {
                    $db->query('UPDATE `engine4_sitestoreoffer_offers` SET `start_time` = NOW(), `discount_type` = 0, `discount_amount` = 0, `public` = 0 , `approved` = 0, `status` = 1 WHERE `engine4_sitestoreoffer_offers`.`offer_id` = "' . $coupon['offer_id'] . '" LIMIT 1 ; ');
                }
            }
        }
        $is_object_couponColumn = $db->query("SHOW COLUMNS FROM `engine4_sitestoreproduct_orders` LIKE 'coupon_detail'")->fetch();
        if (empty($is_object_couponColumn))
            $db->query("ALTER TABLE `engine4_sitestoreproduct_orders` ADD `coupon_detail` TEXT NULL; ");

        $db->query('UPDATE `engine4_activity_notificationtypes` SET `body` = \'{item:$subject} has created a store coupon {var:$eventname}.\' WHERE `engine4_activity_notificationtypes`.`type` = "sitestoreoffer_create" LIMIT 1 ;');

        $db->query("UPDATE `engine4_core_menuitems` SET `label` = 'Coupons' WHERE `engine4_core_menuitems`.`name` = 'sitestore_admin_main_sitestoreoffer' LIMIT 1 ; ");

        $db->query("UPDATE `engine4_core_menuitems` SET `label` = 'Coupons' WHERE `engine4_core_menuitems`.`name` = 'sitestore_main_offer' LIMIT 1 ; ");

        $db->query("UPDATE `engine4_core_menuitems` SET `label` = 'Manage Store Coupons' WHERE `engine4_core_menuitems`.`name` = 'sitestoreoffer_admin_main_manage' LIMIT 1 ; ");

        $db->query("UPDATE `engine4_core_menuitems` SET `label` = 'Tabbed Coupons Widget' WHERE `engine4_core_menuitems`.`name` = 'sitestoreoffer_admin_main_offer_tab' LIMIT 1 ; ");

        $db->query("UPDATE `engine4_core_menuitems` SET `label` = 'Coupon of the Day' WHERE `engine4_core_menuitems`.`name` = 'sitestoreoffer_admin_main_dayitems' LIMIT 1 ; ");

        $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'sitestoreoffer.sitestore-sponsoredoffer' LIMIT 1 ");

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_content', array('params', 'content_id'))
                ->where('name LIKE ?', '%sitestoreoffer%');
        $results = $select->query()->fetchAll();
        foreach ($results as $result) {
            if (strstr($result['params'], 'Offer')) {
                $result['params'] = str_replace('Offer', 'Coupon', $result['params']);
                $db->query('UPDATE `engine4_core_content` SET `params` = \' ' . $result['params'] . ' \' WHERE `engine4_core_content`.`content_id` = "' . $result['content_id'] . '" LIMIT 1 ;');
            }
        }

        $db->query("UPDATE `engine4_core_settings` SET `value` = 'store-coupons' WHERE `engine4_core_settings`.`name` = 'sitestoreoffer.manifestUrl' LIMIT 1 ; ");

        $db->query("ALTER TABLE `engine4_sitestoreoffer_offers` CHANGE `minimum_purchase` `minimum_purchase` BIGINT( 20 ) NULL ; ");

        $db->query("UPDATE `engine4_core_pages` SET `displayname` = 'Store Coupon View Page', `title` = 'View Store Coupon',  `description` = 'This is the view store for a store coupon.' WHERE `engine4_core_pages`.`name` = 'sitestoreoffer_index_view' LIMIT 1 ; ");

        $db->query("UPDATE `engine4_core_pages` SET `displayname` = 'Store Coupons Home', `title` = 'Store Coupons Home',  `description` = 'This is store coupon home store.' WHERE `engine4_core_pages`.`name` = 'sitestoreoffer_index_home' LIMIT 1 ; ");

        $db->query("UPDATE `engine4_core_pages` SET `displayname` = 'Stores: Browse Coupons', `title` = 'Store Coupons List', `description` = 'This is the store coupons.' WHERE `engine4_core_pages`.`name` = 'sitestoreoffer_index_browse' LIMIT 1 ; ");
    }

    protected function _widgetParamsChange($db) {
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_content', array('params', 'content_id'))
                ->where("type = 'widget'")
                ->where('name LIKE ?', 'sitestoreproduct.recently-popular-random-sitestoreproduct');
        $results = $select->query()->fetchAll();

        foreach ($results as $result) {
            if (strstr($result['params'], 'newZZZarrival')) {
                if (strstr($result['params'], 'newZZZarrivals'))
                    continue;
                $result['params'] = str_replace('newZZZarrival', 'newZZZarrivals', $result['params']);
                $db->query('UPDATE `engine4_core_content` SET `params` = \' ' . $result['params'] . ' \' WHERE `engine4_core_content`.`content_id` = "' . $result['content_id'] . '" LIMIT 1 ;');
            }
        }
    }

    protected function _changeWidgetParam($db, $pluginName, $version) {

        $isModuleExist = $db->query("SELECT * FROM `engine4_core_modules` WHERE `name` = '$pluginName'")->fetch();
        if (!empty($isModuleExist)) {
            $curr_module_version = strcasecmp($isModuleExist['version'], $version);
            if ($curr_module_version < 0) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_content', array('params', 'content_id'))
                        ->where('name LIKE ?', '%' . $pluginName . '%');
                $results = $select->query()->fetchAll();

                foreach ($results as $result) {
                    if (strstr($result['params'], '"titleCount":}')) {
                        $result['params'] = str_replace('"titleCount":}', '"titleCount":true}', $result['params']);
                        $db->query('UPDATE `engine4_core_content` SET `params` = \' ' . $result['params'] . ' \' WHERE `engine4_core_content`.`content_id` = "' . $result['content_id'] . '" LIMIT 1 ;');
                    }
                }
            }
        }
    }

    protected function _getSitestorelocationInstaller($db) {

        $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitestoreproduct_products'")->fetch();
        if (!empty($is_table_exist)) {
            $type_array = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_products LIKE 'location'")->fetch();
            if (empty($type_array)) {
                $db->query("ALTER TABLE `engine4_sitestoreproduct_products` ADD `location` TEXT NULL DEFAULT NULL;");
            }
        }

        $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitestoreproduct_locations` (
              `location_id` int(11) NOT NULL AUTO_INCREMENT,
              `product_id` int(11) NOT NULL,
              `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `latitude` double NOT NULL,
              `longitude` double NOT NULL,
              `formatted_address` text COLLATE utf8_unicode_ci,
              `country` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
              `state` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
              `zipcode` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
              `city` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
              `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `zoom` tinyint(2) NOT NULL,
              PRIMARY KEY (`location_id`),
              KEY `product_id` (`product_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");

        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES (NULL, "sitestoreproduct_main_location", "sitestoreproduct", "Browse Product Locations", \'Sitestoreproduct_Plugin_Menus::canBrowseLocation\', \'{"route":"sitestoreproduct_general","action":"map"}\', "sitestoreproduct_main", NULL, "0", "0", "999") ');

        $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES (NULL, "sitestoreproduct_dashboard_editlocation", "sitestoreproduct", "Location", "Sitestoreproduct_Plugin_Dashboardmenus", \'{"route":"sitestoreproduct_general", "action":"editlocation"}\', "sitestoreproduct_dashboard", NULL, "1", "0", "999") ');
        $db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`searchformsetting_id`, `module`, `name`, `display`, `order`, `label`) VALUES (NULL, 'sitestoreproduct', 'location', '1', '35', 'Location') ");

        $db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`searchformsetting_id`, `module`, `name`, `display`, `order`, `label`) VALUES (NULL, 'sitestoreproduct', 'city', '1', '60', 'City') ");

        $db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`searchformsetting_id`, `module`, `name`, `display`, `order`, `label`) VALUES (NULL, 'sitestoreproduct', 'proximity', '1', '70', 'Proximity Search') ");

        $db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`searchformsetting_id`, `module`, `name`, `display`, `order`, `label`) VALUES (NULL, 'sitestoreproduct', 'country', '1', '75', 'Country') ");

        $db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`searchformsetting_id`, `module`, `name`, `display`, `order`, `label`) VALUES (NULL, 'sitestoreproduct', 'street', '1', '80', 'Street') ");

        $db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`searchformsetting_id`, `module`, `name`, `display`, `order`, `label`) VALUES (NULL, 'sitestoreproduct', 'state', '1', '85', 'State') ");

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_pages')
                ->where('name = ?', 'sitestoreproduct_index_map')
                ->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'sitestoreproduct_index_map',
                'displayname' => 'Browse Products Locations',
                'title' => 'Browse Products Locations',
                'description' => 'Browse Products Locations',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            //CONTAINERS
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => Null,
                'order' => 2,
                'params' => '',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'params' => '',
            ));
            $top_middle_id = $db->lastInsertId('engine4_core_content');

            //INSERT MAIN - MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // Top Middle
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.navigation-sitestoreproduct',
                'parent_content_id' => $top_middle_id,
                'order' => 1,
                'params' => '',
            ));

            //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.location-search',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.browselocation-sitestoreproduct',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '{"title":"","titleCount":"true"}',
            ));
        }
    }

    public function setActivityFeeds() {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'nestedcomment')
                ->where('enabled = ?', 1);
        $is_nestedcomment_object = $select->query()->fetchObject();
        if ($is_nestedcomment_object) {
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitestore_store", "sitestore", \'{item:$subject} replied to a comment on {item:$owner}\'\'s store {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitestore_album", "sitestorealbum", \'{item:$subject} replied to a comment on {item:$owner}\'\'s store album {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitestore_photo", "sitestorealbum", \'{item:$subject} replied to a comment on {item:$owner}\'\'s store album photo {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitestoredocument_document", "sitestoredocument", \'{item:$subject} replied to a comment on {item:$owner}\'\'s store document {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitestoreoffer_offer", "sitestoreoffer", \'{item:$subject} replied to a comment on {item:$owner}\'\'s store offer {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitestoreproduct_product", "sitestoreproduct", \'{item:$subject} replied to a comment on {item:$owner}\'\'s store product {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');

            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitestorereview_review", "sitestorereview", \'{item:$subject} replied to a comment on {item:$owner}\'\'s store review {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("nestedcomment_sitestorevideo_video", "sitestorevideo", \'{item:$subject} replied to a comment on {item:$owner}\'\'s store video {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
            $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("sitestore_activityreply", "sitestore", \'{item:$subject} has replied on {var:$eventname}.\', 0, "");');
        }
    }

    protected function _setCustomSettings($db) {
        $db->query('UPDATE `engine4_core_menuitems` SET `enabled` = "0" WHERE `engine4_core_menuitems`.`name` = "sitestorevideo_admin_widget_settings" LIMIT 1 ;');

//    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitestoreproduct_admin_main_template", "sitestoreproduct", "Product Layout", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"general", "action":"set-template"}\', "sitestore_admin_main", "", 1, 0, 28)');
    }
    function isTableExists($tableName){
        $db = $this->getDb();
        $isTableExists = $db->query("SHOW TABLES LIKE '$tableName'")->fetch();
        if($isTableExists){
            return true;
        }
        return false;
    }

    function isColumnExists($tableName,$columnName){
        $db = $this->getDb();
        $isColumnExists = $db->query("SHOW COLUMNS FROM $tableName LIKE '$columnName'")->fetch();
        if($isColumnExists)
            return true;
        
        return false;
    }

}

?>