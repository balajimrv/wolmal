<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Language.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Api_Language extends Core_Api_Abstract {

  protected $_languagePath;
  protected $_defaultLanguagePath;
  protected $_hasUnlinkFlag = true;

  public function __construct() {
    $this->_languagePath = APPLICATION_PATH . '/application/languages';
    $this->_defaultLanguagePath = APPLICATION_PATH . '/application/languages/en/';
  }

  public function getDataWithoutKeyPhase($flag = null) {
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');

    $plural = $coreSettings->getSetting("sitestoreproduct.titleplural", 'Products');
    $singular = $coreSettings->getSetting("sitestoreproduct.titlesingular", 'Product');
    if (!empty($flag)) {
      return array('text_products' => 'products', 'text_product' => 'product');
    } else {
      return array('text_products' => $plural, 'text_product' => $singular);
    }
  }

  public function hasDirectoryPermissions() {
    $flage = false;
    $test = new Engine_Sanity(array(
        'basePath' => APPLICATION_PATH,
        'tests' => array(
            array(
                'type' => 'FilePermission',
                'name' => 'Language Directory Permissions',
                'path' => 'application/languages',
                'value' => 7,
                'recursive' => true,
                'messages' => array(
                    'insufficientPermissions' => 'Please log in over FTP and set CHMOD 0777 (recursive) on the application/languages/ directory',
                ),
            ),
        ),
    ));
    $test->run();
    foreach ($test->getTests() as $stest) {
      $errorLevel = $stest->getMaxErrorLevel();
      if (empty($errorLevel))
        $flage = true;
    }
    return $flage;
  }

  public function setUnlinkFlag($flage = true) {
    $this->_hasUnlinkFlag = $flage;
  }

  public function checkLocal($locale = 'en') {
    // Check Locale
    $locale = Zend_Locale::findLocale();
    // Make Sure Language Folder Exist
    $languageFolder = is_dir(APPLICATION_PATH . '/application/languages/' . $locale);
    if ($languageFolder === false) {
      $locale = substr($locale, 0, 2);
      $languageFolder = is_dir(APPLICATION_PATH . '/application/languages/' . $locale);
    }
    return $languageFolder;
  }

  public function addLanguageFile($fileName, $locale = 'en', $replaceDatas = array(), $replaceDataWithoutKey = array(), $oldFileName = null) {
    if (empty($fileName) || !$this->checkLocal($locale)) {
      return;
    }
    $output = array();
    $dataLocale = array();

    $output = $dataEn = $this->loadTranslationData('en', $fileName);

    if (empty($output))
      return;
    $output = $this->convertData($output, $replaceDatas, $replaceDataWithoutKey);
    $language_file = $this->_languagePath . '/' . $locale . '/' . $fileName;

    if ($this->_hasUnlinkFlag && file_exists($language_file)) {
      @unlink($language_file);
    }

    touch($language_file);
    chmod($language_file, 0777);

    $export = new Engine_Translate_Writer_Csv($language_file);
    $export->setTranslations($output);
    $export->write();
  }

  public function addLanguageFiles($fileName, $replaceDatas = array(), $replaceDataWithoutKey = array(), $oldFileName = null) {
    $translate = Zend_Registry::get('Zend_Translate');

    // Prepare language list
    $languageList = $translate->getList();
    foreach ($languageList as $key) {
      $this->addLanguageFile($fileName, $key, $replaceDatas, $replaceDataWithoutKey, $oldFileName);
    }
  }

  protected function loadTranslationData($locale = 'en', $filename = null, array $options = array()) {
    $file_data = array();
    $options['delimiter'] = ";";
    $options['length'] = 0;
    $options['enclosure'] = '"';

    $filename = APPLICATION_PATH . '/application/languages/en/' . $filename;

    $tmp = Engine_Translate_Parser_Csv::parse($filename, 'null', $options);
    if (!empty($tmp['null']) && is_array($tmp['null'])) {
      $file_data = $tmp['null'];
    } else {
      $file_data = array();
    }
    return $file_data;
  }

  public function getReplaceDataWithoutKey($listType, $flag = null) {
    $replaceWithOutKeyDatas = array();
    $replaceWithOutKeyDatasDefault = $listType;
    if (empty($replaceWithOutKeyDatasDefault))
      return;
    $defaultPhase = $this->getDataWithoutKeyPhase($flag);
    foreach ($replaceWithOutKeyDatasDefault as $arraykey => $data) {
      if (!isset($defaultPhase[$arraykey]))
        continue;
      if (!empty($flag)) {
        $key = $defaultPhase[$arraykey];
      } else {
        $key = $defaultPhase[$arraykey];
      }

      $replaceWithOutKeyDatas[strtolower($key)] = strtolower($data);
      $replaceWithOutKeyDatas[ucfirst($key)] = ucfirst($data);
      $replaceWithOutKeyDatas[strtoupper($key)] = strtoupper($data);
      $replaceWithOutKeyDatas[ucwords($key)] = ucwords($data);
    }
    return $replaceWithOutKeyDatas;
  }

  public function setTranslateForListType($listType, $flag = null, $recall = true) {

    if($recall) {
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this->setTranslateForListType(array('text_products' => '$plural$', 'text_product' => '$singular$'), null, false);
        $coreSettings->setSetting("sitestoreproduct.titleplural", '$plural$');
        $coreSettings->setSetting("sitestoreproduct.titlesingular", '$singular$');
    }      
      
    $coreModulesTable = Engine_Api::_()->getDbtable('modules', 'core');
    $coreModulesTableName = $coreModulesTable->info('name');
    $select = $coreModulesTable->select()
            ->from($coreModulesTableName)
            ->where($coreModulesTableName . '.name LIKE ?', '%' . 'sitestoreproduct' . '%');
    $datas = $coreModulesTable->fetchAll($select);
    foreach ($datas as $data) {
      $fileName = $data['name'] . '.csv';

      $oldFileName = null;

      $replaceDatas = array();
      $replaceDataWithoutKey = $this->getReplaceDataWithoutKey($listType, $flag);
      $this->addLanguageFiles($fileName, $replaceDatas, $replaceDataWithoutKey, $oldFileName);
    }
  }

  public function convertData($datas, $replaceDatas, $replaceDataWithoutKey) {
    $data = array();

    foreach ($datas as $data_key => $data) {

      foreach ($replaceDataWithoutKey as $search => $replace) {

        $data = str_replace($search, $replace, $data);
        if (strstr($data, $replace . "_title")) {
          $data = str_replace($replace . "_title", 'product_title', $data);
        }
        if (strstr($data, $replace . "_description")) {
          $data = str_replace($replace . "_description", 'product_description', $data);
        }
        if (strstr($data, $replace . "_title_with_link")) {
          $data = str_replace($replace . "_title_with_link", 'product_title_with_link', $data);
        }
        
        if (strstr($data, $replace . "_Name_With_link")) {
          $data = str_replace($replace . "_Name_With_link", 'product_Name_With_link', $data);
        }        
        
        if (strstr($data, $replace . "_name")) {
          $data = str_replace($replace . "_Name", 'product_Name', $data);
        }
      }

      $datas[$data_key] = $data;
    }
    return $datas;
  }

  public function languageChanges() {

    //START LANGUAGE WORK
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitestoreproduct.titlesingular');
    $language_product = $select->query()->fetch();

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitestoreproduct.titleplural');
    $language_products = $select->query()->fetch();
    if (isset($language_products['value']) && $language_products['value'] != 'products' && isset($language_product['value']) && $language_product['value'] != 'product') {
      $language_pharse = array('text_products' => $language_products['value'], 'text_product' => $language_product['value']);
      Engine_Api::_()->getApi('language', 'siteproduct')->setTranslateForListType($language_pharse, $flag = 1);
    }
    //END LANGUAGE WORK
  }
}