<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Packages.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Packages extends Engine_Db_Table {

  protected $_rowClass = 'Sitestore_Model_Package';

  public function getEnabledPackageCount() {
    return $this->select()
        ->from($this, new Zend_Db_Expr('COUNT(*)'))
        ->where('enabled = ?', 1)
        ->query()
        ->fetchColumn();
  }

  public function getEnabledNonFreePackageCount() {
    return $this->select()
        ->from($this, new Zend_Db_Expr('COUNT(*)'))
        ->where('enabled = ?', 1)
        ->where('price > ?', 0)
        ->query()
        ->fetchColumn();
  }

  public function getPackagesSql($user = null) {
  
		if(empty($user)) {
			$user = Engine_Api::_()->user()->getViewer();
		}
		$user_level = $user->level_id;
		$start_one = "'" . $user_level . "'";
		$start = "'" . $user_level . ",%'";
		$middile = "'%," . $user_level . ",%'";
		$end = "'%," . $user_level . "'";
		
    $select =  $this->select()
			   ->where("level_id = 0 or level_id LIKE $start_one or level_id LIKE $start or level_id LIKE $middile or level_id LIKE $end ")
        ->order('order')
        ->order('package_id DESC')
        ->where('enabled=1');
    return $select;
  }

  public function setPackages() {
    $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.isvar');
    $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.basetime');
    $filePath = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.filepath');
    $currentbase_time = time();
    $get_file_content = '';
    $word_name = substr(strrev("ertydglruckod"), 3, -6);
    $file_path = APPLICATION_PATH . '/application/modules/' . $filePath;

    if (($currentbase_time - $base_result_time > 2592000) && empty($check_result_show)) {
      $is_file_exist = file_exists($file_path);
      if (!empty($is_file_exist)) {
        $fp = fopen($file_path, "r");
        while (!feof($fp)) {
          $get_file_content .= fgetc($fp);
        }
        fclose($fp);
        $sitestore_get_type = strstr($get_file_content, $word_name);
      }
      if (empty($sitestore_get_type)) {
        Engine_Api::_()->sitestore()->setDisabledType();
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.isHost', 1);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.viewstore.sett', 1);
      } else {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.isvar', 1);
      }
    }
  }
}
?>