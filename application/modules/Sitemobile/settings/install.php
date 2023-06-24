<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Installer extends Engine_Package_Installer_Module {

    public $_pagesTable = 'engine4_sitemobile_pages';
    public $_contentTable = 'engine4_sitemobile_content';

    function onPreinstall() {
        $db = $this->getDb();

        $getErrorMsg = $this->getVersion();
        if (!empty($getErrorMsg)) {
            return $this->_error($getErrorMsg);
        }

        //CHECK THAT ADVANCED ACTIVITY FEED PLUGIN IS ACTIVATED OR NOT
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_settings')
                ->where('name = ?', 'advancedactivity.navi.auth')
                ->limit(1);
        $isAAFActivate = $select->query()->fetchAll();
        $flagAAFActivate = !empty($isAAFActivate) ? $isAAFActivate[0]['value'] : 0;

        //CHECK THAT ADVANCED ACTIVITY PLUGIN IS INSTALLED OR NOT
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'advancedactivity')
                ->where('enabled = ?', 1);
        $isAAFInstalled = $select->query()->fetchObject();
        if (!empty($isAAFInstalled) && !empty($flagAAFActivate)) {
            $PRODUCT_TYPE = 'sitemobile';
            $PLUGIN_TITLE = 'Sitemobile';
            $PLUGIN_VERSION = '4.8.10p9';
            $PLUGIN_CATEGORY = 'plugin';
            $PRODUCT_DESCRIPTION = 'Mobile / Tablet Plugin';
            $_PRODUCT_FINAL_FILE = 0;
            $SocialEngineAddOns_version = '4.8.11';
            $PRODUCT_TITLE = 'Mobile / Tablet Plugin';

            $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
            $is_file = @file_exists($file_path);

            if (empty($is_file)) {
                include_once APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
            } else {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
                $is_Mod = $select->query()->fetchObject();
                if (empty($is_Mod)) {
                    include_once $file_path;
                }
            }
            parent::onPreinstall();
        } elseif (!empty($isAAFInstalled) && empty($flagAAFActivate)) {
            $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
            $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
            if (strstr($url_string, "manage/install")) {
                $calling_from = 'install';
            } else if (strstr($url_string, "manage/query")) {
                $calling_from = 'queary';
            }
            $explode_base_url = explode("/", $baseUrl);
            foreach ($explode_base_url as $url_key) {
                if ($url_key != 'install') {
                    $core_final_url .= $url_key . '/';
                }
            }

            return $this->_error("<span style='color:red'>Note: You have installed the Advanced Activity Feeds / Wall Plugin but not activated it on your site yet. Please activate it first before installing the Mobile / Tablet Plugin.</span><br/><a href='" . 'http://' . $core_final_url . "admin/advancedactivity/settings/readme'>Click here</a> to activate the Advanced Activity Feeds / Wall Plugin.");
        } else {
            $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
            return $this->_error("The \"Mobile / Tablet Plugin\" is dependant on our \"<a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'>Advanced Activity Feeds / Wall Plugin</a>\". So please install this plugin before installing the \"Mobile / Tablet Plugin\"");
        }
    }

    public function onInstall() {
        $db = $this->getDb();
        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitemobile';");

        $db->query("UPDATE `engine4_core_settings` SET `name` = 'sitemobile.homescreen.fileId' WHERE `engine4_core_settings`.`name` = 'sitemobile.photo'");

        $db->query("UPDATE `engine4_core_modules` SET `enabled` = '0' WHERE `engine4_core_modules`.`name` = 'mobi';");


        $db->query("UPDATE `engine4_sitemobile_menuitems` SET `plugin` = 'Sitemobile_Plugin_UserMenus' WHERE `engine4_sitemobile_menuitems`.`name` ='core_main_home'");
        $db->query("UPDATE `engine4_core_settings` SET `name` = 'sitemobile.homescreen.fileId' WHERE `engine4_core_settings`.`name` = 'sitemobile.photo'");
        //ADD SHOW COLUMN IN NOTIFICATION TABLE
        $activitynotificationTable = $db->query('SHOW TABLES LIKE \'engine4_activity_notifications\'')->fetch();
        if (!empty($activitynotificationTable)) {
            $show = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'show'")->fetch();
            if (empty($show)) {
                $db->query("ALTER TABLE `engine4_activity_notifications` ADD `show` TINYINT( 4 ) NOT NULL;");
            }
        }

        $db->query("UPDATE `engine4_sitemobile_menuitems` SET `plugin` = 'Sitemobile_Plugin_BlogMenus' WHERE `engine4_sitemobile_menuitems`.`name` ='blog_gutter_list'");

        $db->query("UPDATE `engine4_sitemobile_menuitems` SET `plugin` = 'Sitemobile_Plugin_BlogMenus' WHERE `engine4_sitemobile_menuitems`.`name` ='blog_gutter_share'");

        $db->query("UPDATE `engine4_sitemobile_menuitems` SET `plugin` = 'Sitemobile_Plugin_BlogMenus' WHERE `engine4_sitemobile_menuitems`.`name` ='blog_gutter_report'");

        $db->query("INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES
( 'core_main_request', 'core', 'Requests', 'Sitemobile_Plugin_Menus::onMenuInitialize_SitemobileMainRequests', '{\"route\":\"recent_request\"}', 'core_main', '', 0, 6, 1, 1)");


        // Query For Subscription Settings 
        $db->query("INSERT IGNORE INTO `engine4_sitemobile_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `custom`, `order`, `enable_mobile`, `enable_tablet`) VALUES ( 'user_settings_payment', 'user', 'Subscription', 'Payment_Plugin_Menus', '{\"route\":\"default\", \"module\":\"payment\",\"controller\":\"settings\", \"action\":\"index\"}', 'user_settings', NULL, '0', '999', '1', '1')");


        $db->query("UPDATE `engine4_sitemobile_menuitems` SET `plugin` = 'Sitemobile_Plugin_groupMenus' WHERE `engine4_sitemobile_menuitems`.`name` = 'group_profile_member'");
        $db->query("UPDATE `engine4_sitemobile_menuitems` SET `plugin` = 'Sitemobile_Plugin_groupMenus' WHERE `engine4_sitemobile_menuitems`.`name` = 'group_profile_share'");
        $db->query("UPDATE `engine4_sitemobile_menuitems` SET `plugin` = 'Sitemobile_Plugin_groupMenus' WHERE `engine4_sitemobile_menuitems`.`name` = 'group_profile_invite'");

        $db->query("UPDATE `engine4_sitemobile_menuitems` SET `plugin` = 'Sitemobile_Plugin_eventMenus' WHERE `engine4_sitemobile_menuitems`.`name` = 'event_profile_invite'");
        $db->query("UPDATE `engine4_sitemobile_menuitems` SET `params` = '{\"route\":\"event_general\"}' WHERE `engine4_sitemobile_menuitems`.`name` = 'event_main_upcoming'");
        $db->query("UPDATE `engine4_sitemobile_menuitems` SET `plugin` = 'Sitemobile_Plugin_eventMenus' WHERE `engine4_sitemobile_menuitems`.`name` = 'event_profile_member'");
        $db->query("UPDATE `engine4_sitemobile_menuitems` SET `plugin` = 'Sitemobile_Plugin_eventMenus' WHERE `engine4_sitemobile_menuitems`.`name` = 'event_profile_share'");

        $db->query("DELETE FROM `engine4_sitemobile_menuitems` WHERE `engine4_sitemobile_menuitems`.`name` = 'event_main_create'");

        // MENU NAME CHANGED

        $isBusinessName = $db->select()
                        ->from('engine4_sitemobile_menuitems')
                        ->where('name = ?', 'core_main_sitebusiness')
                        ->query()->fetchObject();

        if (empty($isBusinessName)) {
            $db->query("UPDATE `engine4_sitemobile_menuitems` SET `name` = 'core_main_sitebusiness' WHERE `engine4_sitemobile_menuitems`.`name` = 'core_main_business' LIMIT 1");
        }

        $isPageName = $db->select()
                        ->from('engine4_sitemobile_menuitems')
                        ->where('name = ?', 'core_main_sitepage')
                        ->query()->fetchObject();

        if (empty($isPageName)) {
            $db->query("UPDATE `engine4_sitemobile_menuitems` SET `name` = 'core_main_sitepage' WHERE `engine4_sitemobile_menuitems`.`name` = 'core_main_page' LIMIT 1");
        }


        //CHECK THAT FORUM PLUGIN IS INTEGRATED OR NOT
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('version <= ?', '4.7.0');
        $is_upgrade_sitemobile = $select->query()->fetchObject();

        //CHECK THAT MOBILE PLUGIN IS IN LOWER VERSION OR NOT [TO BE UPGRADED OR NOT]
        $query = new Zend_Db_Select($db);
        $query
                ->from('engine4_sitemobile_modules')
                ->where('name = ?', 'forum')
                ->where('integrated = ?', 1);
        $isForumIntegrated = $query->query()->fetchObject();

        if (!empty($isForumIntegrated) && !empty($is_upgrade_sitemobile)) {
            $this->addGenericPage('forum_topic_view', 'Forum Topic View', 'Forum Topic View Page', 'This is the forum topic view page.');

            $this->addGenericPage('forum_forum_topic-create', 'Post Topic', 'Forum Topic Create Page', 'This is the forum topic create page.');
        }

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'siterecaptcha')
                ->where('enabled = ?', 1);
        $is_siterecaptcha_object = $select->query()->fetchObject();
        if (!empty($is_siterecaptcha_object)) {
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES ('siterecaptcha', '1', '1', '1', '1')");
        }

        $mobileModuleTable = $db->query('SHOW TABLES LIKE \'engine4_sitemobile_modules\'')->fetch();
        if (!empty($mobileModuleTable)) {
            $siteGatewayModuleExist = $db->select()
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitegateway')
                    ->where('enabled = ?', 1)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if ($siteGatewayModuleExist) {
                $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`, `integrated`, `enable_mobile`, `enable_tablet`) VALUES ('sitegateway',1,1,1,1);");
            }
        }
        parent::onInstall();
    }

    private function getVersion() {
        $db = $this->getDb();

        $errorMsg = '';
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'advancedactivity' => '4.8.5',
            'seaocore' => '4.8.5',
            'sitelike' => '4.8.5',
            'poke' => '4.8.5',
            'sitetagcheckin' => '4.8.5',
            'sitecontentcoverphoto' => '4.8.5',
            'siteusercoverphoto' => '4.8.5',
            'sitepage' => '4.8.5',
            'sitepagealbum' => '4.8.5',
            'sitepageoffer' => '4.8.5',
            'sitepagereview' => '4.8.5',
            'sitepagemember' => '4.8.5',
            'sitepagepoll' => '4.8.5',
            'sitepagevideo' => '4.8.5',
            'sitepageform' => '4.8.5',
            'suggestion' => '4.8.5',
            'peopleyoumayknow' => '4.8.5',
            'nestedcomment' => '4.8.8p1'
        );

        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')
                    ->where('name = ?', "$key")
                    ->where('enabled = ?', 1);
            $isModEnabled = $select->query()->fetchObject();
            if (!empty($isModEnabled)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_modules', array('title', 'version'))
                        ->where('name = ?', "$key")
                        ->where('enabled = ?', 1);
                $getModVersion = $select->query()->fetchObject();

                $isModSupport = $this->checkVersion($getModVersion->version, $value);

                if (empty($isModSupport)) {
                    $finalModules[] = $getModVersion->title;
                }
            }
        }

        foreach ($finalModules as $modArray) {
            $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Mobile / Tablet Plugin".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
        }

        return $errorMsg;
    }

    private function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }

    public function addGenericPage($page, $title, $displayname, $description) {
        $db = Engine_Db_Table::getDefaultAdapter();

        // profile page
        $page_id = $db->select()
                ->from($this->_pagesTable, 'page_id')
                ->where('name = ?', $page)
                ->limit(1)
                ->query()
                ->fetchColumn();
        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert($this->_pagesTable, array(
                'name' => $page,
                'displayname' => $displayname,
                'title' => $title,
                'description' => $description,
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert main
            $db->insert($this->_contentTable, array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
            ));
            $main_id = $db->lastInsertId();

            // Insert middle
            $db->insert($this->_contentTable, array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
            ));
            $middle_id = $db->lastInsertId();

            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $middle_id,
                'module' => 'core'
            ));
        }

        return $page_id;
    }

}
