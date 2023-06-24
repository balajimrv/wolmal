<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesbasic
 * @package    Sesbasic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: install.php 2015-07-25 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesbasic_Installer extends Engine_Package_Installer_Module {

  public function onPreinstall() {
  
    $db = $this->getDb();

    //Check SocialEngine 4.9.0 version
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'core')
            ->where('version = ?', '4.9.0');
    $results = $select->query()->fetchObject();
    if (empty($results)) {
      return $this->_error('<div class="global_form"><div><div><p style="color:red;">Currently, you have SocialEngine Old version on your site. So, plugin upgrade SE 4.9.0 on your site to install SocialEngineSolutions Basic Required Plugin. Please download the latest version of SE 4.9.0 from your SocialEngine Account from <a href="http://socialengine.com/" target="_blank">SocialEngine.com</a> website.</p></div></div></div>');
    }
    
    //upgraded plugin work
    $db->query('CREATE TABLE IF NOT EXISTS `engine4_sesbasic_plugins` (
      `plugin_id` int(11) NOT NULL AUTO_INCREMENT,
      `module_name` varchar(64) NOT NULL,
      `title` varchar(64) NOT NULL,
      `description` text NULL,
      `current_version` varchar(32) NOT NULL,
      `site_version` varchar(32) NOT NULL,
      `category` varchar(64) NOT NULL,
      `pluginpage_link` VARCHAR(255) NOT NULL,
      PRIMARY KEY (`plugin_id`),
      UNIQUE KEY `module_name` (`module_name`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;');
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name LIKE ?', '%ses%');
    $results = $select->query()->fetchAll();
    if (!empty($results)) {
      foreach ($results as $result) {
        $db->query("INSERT IGNORE INTO `engine4_sesbasic_plugins` (`module_name`, `title`, `description`, `current_version`, `site_version`, `category`, `pluginpage_link`) VALUES  ('".$result['name']."', '".$result['title']."', '".$result['description']."', '', '".$result['version']."', 'plugin', '');");
      }
    }
    $db->query('UPDATE `engine4_sesbasic_plugins` SET `current_version` = "4.8.13p9", `pluginpage_link` = "http://www.socialenginesolutions.com/social-engine/socialenginesolutions-basic-required-plugin/" WHERE `engine4_sesbasic_plugins`.`module_name` = "sesbasic";');
    
    parent::onPreinstall();
  }

  public function onInstall() {

    $db = $this->getDb();
    
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
    ("sesbasic_admin_upgradeplugin", "sesbasic", "Installed Plugins", "", \'{"route":"admin_default","module":"sesbasic","controller":"settings","action":"upgrade-plugins"}\', "sesbasic_admin_main", "", 6),
		("sesbasic_admin_notinstalledplugin", "sesbasic", "Not Installed Plugins", "", \'{"route":"admin_default","module":"sesbasic","controller":"settings","action":"notinstalled-plugins"}\', "sesbasic_admin_main", "", 7);');
    
    
    //INCREASE THE SIZE OF engine4_core_menuitems's FIELD type
    $menu_column = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'menu'")->fetch();
    if (!empty($menu_column)) {
      $db->query("ALTER TABLE `engine4_core_menuitems` CHANGE `menu` `menu` VARCHAR( 256 ) CHARACTER SET latin1 COLLATE latin1_general_ci NULL DEFAULT NULL");
    }

    $label_column = $db->query("SHOW COLUMNS FROM engine4_core_menuitems LIKE 'label'")->fetch();
    if (!empty($label_column)) {
      $db->query("ALTER TABLE `engine4_core_menuitems` CHANGE `label` `label` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
    }

    $type_column = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'type'")->fetch();
    if (!empty($type_column)) {
      $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 256 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
    }

    $type_column = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'name'")->fetch();
    if (!empty($type_column)) {
      $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `name` `name` VARCHAR(256) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
    }
    
    $type_column = $db->query("SHOW COLUMNS FROM engine4_activity_actiontypes LIKE 'type'")->fetch();
    if (!empty($type_column)) {
      $db->query("ALTER TABLE `engine4_activity_actiontypes` CHANGE `type` `type` VARCHAR( 256 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;");
    }
    
    $type_column = $db->query("SHOW COLUMNS FROM engine4_activity_notificationtypes LIKE 'type'")->fetch();
    if (!empty($type_column)) {
      $db->query("ALTER TABLE `engine4_activity_notificationtypes` CHANGE `type` `type` VARCHAR( 256 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;");
    }

    $type_column = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'type'")->fetch();
    if (!empty($type_column)) {
      $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `type` `type` VARCHAR( 256 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;");
    }
    $type_column = $db->query("SHOW COLUMNS FROM engine4_activity_actions LIKE 'type'")->fetch();
    if (!empty($type_column)) {
      $db->query("ALTER TABLE `engine4_activity_actions` CHANGE `type` `type` VARCHAR( 256 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;");
    }
    

    parent::onInstall();
  }

  public function onDisable() {

    $db = $this->getDb();
    $sesModules = array('sesalbum', 'seschristmas', 'sescleanwide', 'sesdemouser', 'seshtmlbackground', 'sesmodern', 'sesmusic', 'sespagebuilder', 'sespoke', 'Sesspectromedia', 'sesteam', 'sesvideo');
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
    foreach ($sesModules as $sesModule) {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')
              ->where('name = ?', "$sesModule")
              ->where('enabled = ?', 1);
      $moduleEnabled = $select->query()->fetchObject();
      if (!empty($moduleEnabled)) {
        $errorMsg .= '<div class="global_form"><div><div><p style="color:red;">Note: ' . $moduleEnabled->title . ' is enabled on your website. So, to disable the SocialEngineSolutions Basic Required Plugin, please first disable the ' . $moduleEnabled->title . ' from the "<a href="' . $base_url . '"/manage">Manage Packages</a>" section.</p></div></div></div>';
      }
    }
    if ($errorMsg) {
      echo $errorMsg . '<br />';
      die;
    }
    parent::onDisable();
  }

}