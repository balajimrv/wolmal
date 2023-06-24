<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteverify_Installer extends Engine_Package_Installer_Module {

  function onPreinstall() {
    $db = $this->getDb();

    $PRODUCT_TYPE = 'siteverify';
    $PLUGIN_TITLE = 'Siteverify';
    $PLUGIN_VERSION = '4.8.8';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'Members Verification Plugin';
    $PRODUCT_TITLE = 'Members Verification Plugin';
    $_PRODUCT_FINAL_FILE = 0;
    $SocialEngineAddOns_version = '4.8.7p10';
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);
    if (empty($is_file)) {
      include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
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

  public function onInstall() {
    $db = $this->getDb();
    $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='siteverify';");
    $db->query('UPDATE  `engine4_activity_notificationtypes` SET  `body` =  \'You have been verified by {item:$subject}.\' WHERE `engine4_activity_notificationtypes`.`type` =  "siteverify_new";');
    
    parent::onInstall();
  }

}

?>