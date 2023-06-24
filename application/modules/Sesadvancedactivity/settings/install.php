<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesadvancedactivity
 * @package    Sesadvancedactivity
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: install.php  2017-01-12 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesadvancedactivity_Installer extends Engine_Package_Installer_Module {

  public function onPreinstall() {

    $db = $this->getDb();
    $plugin_currentversion = '4.9.0';
    
    //Check: Basic Required Plugin
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sesbasic');
    $results = $select->query()->fetchObject();
    if (empty($results)) {
      return $this->_error('<div class="global_form"><div><div><p style="color:red;">The required SocialEngineSolutions Basic Required Plugin is not installed on your website. Please download the latest version of this FREE plugin from <a href="http://www.socialenginesolutions.com" target="_blank">SocialEngineSolutions.com</a> website.</p></div></div></div>');
    } else {
      $error = include APPLICATION_PATH . "/application/modules/Sesbasic/controllers/checkPluginVersion.php";
      if($error != '1') {
        return $this->_error($error);
      }
		}
		//Check latest version plugins
		$getAllPluginVersion = $this->getAllPluginVersion();
		if(!empty($getAllPluginVersion)) {
      return $this->_error($getAllPluginVersion);
		}
    parent::onPreinstall();
  }

  public function onInstall() {
    $db = $this->getDb();
    parent::onInstall();
  }
  
  //Funcation for check versions
  private function checkpluginversion($pluginVersion, $plugin_currentversion) {
  
    $sesbasicSiteversion = @explode('p', $plugin_currentversion);
    $sesbasiCurrentversionE = @explode('p', $pluginVersion);
    
    if(isset($sesbasiCurrentversionE[0]))
      $sesbasiCurrentVersion = @explode('.', $sesbasiCurrentversionE[0]);
      
    if(isset($sesbasiCurrentversionE[1]))
      $sesbasiCurrentVersionP = $sesbasiCurrentversionE[1];
      
    $finalVersion = 1;
    $versionB  = false;
    
    foreach($sesbasicSiteversion as $versionSite) {
      $sesVersion = explode('.', $versionSite);
      if(count($sesVersion) > 1){
      $counterV = 0;
      foreach($sesVersion as $key => $version) {
        if(isset($sesbasiCurrentVersion[$key]) && $version < $sesbasiCurrentVersion[$key]){
          $versionB = true;
          $finalVersion = 1;
          break;
        }
        if(isset($sesbasiCurrentVersion[$key]) && $version > $sesbasiCurrentVersion[$key] && 	$version != $sesbasiCurrentVersion[$key]) {
          $finalVersion = 0;
          break;
        }
        $counterV++;
      }
      } else {
        //string after p
        if(isset($sesbasiCurrentVersionP)){
          if( $versionSite > $sesbasiCurrentVersionP && $versionSite != $sesbasiCurrentVersionP) {
            $finalVersion = 0;
            break;
          }
        } else {
          $finalVersion = 0;
          break;
        }
      }
      //check if final result is false exit
      if(!$finalVersion || $versionB)
        break;
    }
    return $finalVersion;
  }
  
  //Funcation for check depandencey plugin
  private function getAllPluginVersion() {

    $db = $this->getDb();
    $baseURL = Zend_Controller_Front::getInstance()->getBaseUrl();
    $pluginArrays = array(
      'sesvideo' => '4.8.13p1',
      'sesalbum' => '4.8.13',
    );
    $sespluginupgrademessage = '';
    foreach ($pluginArrays as $key=>$pluginArray) {
      $modulesExist = $db->query("SELECT * FROM  `engine4_core_modules` WHERE  `name` LIKE  '".$key."'")->fetch();
      if (!empty($modulesExist) && !empty($modulesExist['version'])) {
        $modulesExistSES = $this->checkpluginversion($modulesExist['version'], $pluginArray);
        if (empty($modulesExistSES)) {
          $sespluginupgrademessage .= '<div><span style="border-radius: 3px;border: 2px solid #cd4545;background-color: #da5252;padding: 10px;display: block;margin-bottom: 15px;"><p style="color:#fff;font-weight:bold;">Note: Your website does not have the latest version of "' . $modulesExist['title'] . '". Please upgrade "' . $modulesExist['title'] . '" on your website to the latest version available in your SocialEngineSolutions Client Area to enable its integration with "Advanced News & Activity Feeds Plugin". Please <a href="' . $baseURL . '/manage" style="color:#fff;text-decoration:underline;font-weight:bold;">Click here</a> to go Manage Packages.</p></span></div>';
        }
      }
    }
    return $sespluginupgrademessage;
  }
}