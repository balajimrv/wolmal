<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Modules.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Model_DbTable_Modules extends Engine_Db_Table {

  protected $_enabledModuleNamesMobile;

  public function getEnabledModuleNames() {

    //Get list of all modules from sitemobile module table which are enabled in core module table & in sitemobile module table.
    $coreModulesName = Engine_Api::_()->getDbtable('modules', 'core')->info('name');
    $sitemobileGetMobileInfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.module.info', null);
    $smModulesName = $this->info('name');

    $select = $this->select()
            ->from($smModulesName, "$smModulesName.name")
            ->setIntegrityCheck(false)
            ->join($coreModulesName, "($smModulesName.name = $coreModulesName.name)", array());

    $enable_type = null;
    if (Engine_Api::_()->sitemobile()->checkMode('tablet-mode')) {
      $enable_type = 'enable_tablet';
    } elseif (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
      $enable_type = 'enable_mobile';
    }

    if ($enable_type && Engine_Api::_()->sitemobile()->isApp()) {
      $enable_type .= '_app';
    }

    if ($enable_type) {
      $select->where("$smModulesName.$enable_type = ?", 1);
    }

    $enabledModuleNames = $select->where("$coreModulesName.enabled = ?", 1)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);

    $enabledModuleNames = empty($sitemobileGetMobileInfo) ? $sitemobileGetMobileInfo : $enabledModuleNames;
    if ($this->checkForCometchat() || $this->checkForArrowchat())
      $enabledModuleNames[] = 'cometchat';
    return $enabledModuleNames;
  }

  public function getManageModulesList($params = array()) {

    //Get list of all modules from sitemobile module table which are enabled in core module table and visible in sitemobile.
    $coreModulesName = Engine_Api::_()->getDbtable('modules', 'core')->info('name');

    $smModulesName = $this->info('name');

    if (Engine_Api::_()->hasModuleBootstrap('sitemobileapp')) {
      $column_array = array("$smModulesName.name", "$smModulesName.integrated", "$smModulesName.enable_mobile", "$smModulesName.integrated", "$smModulesName.enable_tablet", "$coreModulesName.title", "$smModulesName.enable_tablet_app", "$smModulesName.enable_mobile_app");
    } else {
      $column_array = array("$smModulesName.name", "$smModulesName.integrated", "$smModulesName.enable_mobile", "$smModulesName.integrated", "$smModulesName.enable_tablet", "$coreModulesName.title");
    }


    $select = $this->select()
            ->from($smModulesName, $column_array)
            ->setIntegrityCheck(false)
            ->join($coreModulesName, "($smModulesName.name = $coreModulesName.name)", array())
            ->where("$smModulesName.visibility = ?", 1)
            ->where("$coreModulesName.enabled = ?", 1);
    if (isset($params['integrated'])) {
      $select->where("$smModulesName.integrated = ?", $params['integrated']);
    }
    $modules = $this->fetchAll($select);

    return $modules;
  }

  public function getExtensionsList($params = array()) {
    if (!isset($params['modulename']) || !$params['modulename'])
      return;
    //Get list of all modules from sitemobile module table which are enabled in core module table and visible in sitemobile.
    $coreModulesName = Engine_Api::_()->getDbtable('modules', 'core')->info('name');

    $smModulesName = $this->info('name');

    $select = $this->select()
            ->from($smModulesName, array("$smModulesName.name"))
            ->setIntegrityCheck(false)
            ->join($coreModulesName, "($smModulesName.name = $coreModulesName.name)", array())
            ->where("$smModulesName.name Like ?", $params['modulename'] . "%")
            ->where("$smModulesName.name <> ?", $params['modulename']);
    //  ->orWhere("$smModulesName.name Like ?", "sitebusiness%")
    //  ->orWhere("$smModulesName.name Like ?", "sitegroup%")
    //  ->where("$smModulesName.visibility = ?", 1);

    $modules = $this->fetchAll($select);

    return $modules;
  }

  public function getAllMobileEnabledModuleNames() {

    //Get list of all modules from sitemobile module table which are enabled in core module table & in sitemobile module table.
    $coreModulesName = Engine_Api::_()->getDbtable('modules', 'core')->info('name');

    $smModulesName = $this->info('name');

    $select = $this->select()
            ->from($smModulesName, "$smModulesName.name")
            ->setIntegrityCheck(false)
            ->join($coreModulesName, "($smModulesName.name = $coreModulesName.name)", array());
    $enabledModuleNames = $select->where("$coreModulesName.enabled = ?", 1)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
    if ($this->checkForCometchat() || $this->checkForArrowchat())
      $enabledModuleNames[] = 'cometchat';
    return $enabledModuleNames;
  }

  public function checkForCometchat() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $tables = $db->query("SHOW TABLES LIKE 'cometchat%'")->fetchAll();

    if (!$tables || empty($tables) || count($tables) < 1)
      return false;

    return true;
  }

  public function checkForArrowchat() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $tables = $db->query("SHOW TABLES LIKE 'arrowchat%'")->fetchAll();

    if (!$tables || empty($tables) || count($tables) < 1)
      return false;

    return true;
  }

  public function isModuleEnabled($name) {
    return in_array($name, $this->getEnabledModuleNamesMobile());
  }

  public function getEnabledModuleNamesMobile() {
    if (null === $this->_enabledModuleNamesMobile) {
      $enable_type = null;
      if (Engine_Api::_()->sitemobile()->checkMode('tablet-mode')) {
        $enable_type = 'enable_tablet';
      } elseif (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
        $enable_type = 'enable_mobile';
      }

      if ($enable_type && Engine_Api::_()->sitemobile()->isApp()) {
        $enable_type .= '_app';
      }

      if ($enable_type) {
        $coreModulesName = Engine_Api::_()->getDbtable('modules', 'core')->info('name');

        $smModulesName = $this->info('name');
        $this->_enabledModuleNamesMobile = $this->select()
                ->from($this, 'name')
                ->setIntegrityCheck(false)
                ->join($coreModulesName, "($smModulesName.name = $coreModulesName.name)", array())
                ->where("$coreModulesName.enabled = ?", 1)
                ->where("$enable_type = ?", true)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
      }
    }

    return $this->_enabledModuleNamesMobile;
  }

}