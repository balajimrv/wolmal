<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Language.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Api_Language extends Core_Api_Abstract {

  protected $_languagePath;
  protected $_defaultLanguagePath;
  //protected $_defalutFileName = 'sitereview_listingtype_genral.csv';
 // protected $_fileNamePre = "sitereview_";
  protected $_hasUnlinkFlag = true;

  public function __construct() {
    $this->_languagePath = APPLICATION_PATH . '/application/languages';
    $this->_defaultLanguagePath = APPLICATION_PATH . '/application/languages/en/';
  }

  public function getDataWithoutKeyPhase($flag = null) {
		$coreSettings = Engine_Api::_()->getApi('settings', 'core');
		
	  $plural	= $coreSettings->getSetting( "language.phrases.stores" ,'stores');
		$singular = $coreSettings->getSetting( "language.phrases.store" ,'store');
		if (!empty($flag)) {
		return $flag == 1 ? array('text_stores' => 'stores', 'text_store' => 'store') : array('text_stores' => '$plural$', 'text_store' => '$singular$');
		} else {
    return array('text_stores' => $plural, 'text_store' => $singular);
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
  
  public function setUnlinkFlag($flage=true) {
    $this->_hasUnlinkFlag = $flage;
  }

  public function checkLocal($locale='en') {
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

  public function addLanguageFile($fileName, $locale ='en', $replaceDatas=array(), $replaceDataWithoutKey=array(), $oldFileName=null) {
    if (empty($fileName) || !$this->checkLocal($locale)) {
      return;
    }
    $output = array();
    $dataLocale = array();

    $output = $dataEn = $this->loadTranslationData('en', $fileName);
 
//    if ($locale !== 'en') {
//      $dataLocale = $this->loadTranslationData($locale);
//    }
//    $output = array_merge($dataEn, $dataLocale);
    if (empty($output))
      return;
    $output = $this->convertData($output, $replaceDatas, $replaceDataWithoutKey);
    $language_file = $this->_languagePath . '/' . $locale . '/' . $fileName;

    if ($this->_hasUnlinkFlag && file_exists($language_file)) {
      @unlink($language_file);
    }

//     if ($oldFileName) {
//       $old_language_file = $this->_languagePath . '/' . $locale . '/' . $oldFileName;
//       if (file_exists($old_language_file)) {
//         @unlink($old_language_file);
//       }
//     }
    touch($language_file);
    chmod($language_file, 0777);

    $export = new Engine_Translate_Writer_Csv($language_file);
    $export->setTranslations($output);
    $export->write();
  }

  public function addLanguageFiles($fileName, $replaceDatas=array(), $replaceDataWithoutKey=array(), $oldFileName=null) {
    $translate = Zend_Registry::get('Zend_Translate');

    // Prepare language list
    $languageList = $translate->getList();
    foreach ($languageList as $key) { 
      $this->addLanguageFile($fileName, $key, $replaceDatas, $replaceDataWithoutKey, $oldFileName);
    }
  }

//  public function getMessages($locale) {
//    return $this->_loadTranslationData($locale);
//  }

  protected function loadTranslationData($locale='en',$filename = null,  array $options = array()) {
    $file_data = array();
    $options['delimiter'] = ";";
    $options['length'] = 0;
    $options['enclosure'] = '"';
    //$options = $options;
    $filename = APPLICATION_PATH . '/application/languages/en/'. $filename;
    //$this->_defaultLanguagePath . '/' . $locale . '/' . $this->_defalutFileName;
    $tmp = Engine_Translate_Parser_Csv::parse($filename, 'null', $options);
    if (!empty($tmp['null']) && is_array($tmp['null'])) {
      $file_data = $tmp['null'];
    } else {
      $file_data = array();
    }
    return $file_data;

//    if (file_exists($filename)) {
//      $export = new Engine_Translate_Writer_Csv($filename);
//      return $export->getTranslations();
//    } else {
//      return array();
//    }
//    $file = @fopen($filename, 'rb');
//    if ($file) {
//
//      while (($data = fgetcsv($file, $options['length'], $options['delimiter'], $options['enclosure'])) !== false) {
//        if (substr($data[0], 0, 1) === '#') {
//          $data[1] = '';
//        }
//
//        if (!isset($data[1])) {
//          continue;
//        }
//
//        if (count($data) == 2) {
//          $file_data[$data[0]] = $data[1];
//        } else {
//          $singular = array_shift($data);
//          $file_data[$singular] = $data;
//        }
//      }
//    }
//    return $file_data;
  }

//   public function getReplaceData($listType) {
//     $replaceDatas = array();
// 
//     $Listtype_Title_Singular = 'group';
//     $Listtype_Title_Singular_KEY = "store";
// 
//     $replaceDatas[strtolower($Listtype_Title_Singular_KEY)] = strtolower($Listtype_Title_Singular);
//     $replaceDatas[ucfirst($Listtype_Title_Singular_KEY)] = ucfirst($Listtype_Title_Singular);
//     $replaceDatas[strtoupper($Listtype_Title_Singular_KEY)] = strtoupper($Listtype_Title_Singular);
// 
//     $Listtype_Title_Plural = 'groups';
//     $Listtype_Title_Plural_KEY = "stores";
// 
//     $replaceDatas[strtolower($Listtype_Title_Plural_KEY)] = strtolower($Listtype_Title_Plural);
//     $replaceDatas[ucfirst($Listtype_Title_Plural_KEY)] = ucfirst($Listtype_Title_Plural);
//     $replaceDatas[strtoupper($Listtype_Title_Plural_KEY)] = strtoupper($Listtype_Title_Plural);
// 
// //     $LISTTYPE_VAR = "listtype_" . $listType->listingtype_id;
// //     $LISTTYPE_KEY = "listtype_1";
// //     $replaceDatas[strtolower($LISTTYPE_KEY)] = strtolower($LISTTYPE_VAR);
// //     $replaceDatas[ucfirst($LISTTYPE_KEY)] = ucfirst($LISTTYPE_VAR);
// //     $replaceDatas[strtoupper($LISTTYPE_KEY)] = strtoupper($LISTTYPE_VAR);
// 
//     return $replaceDatas;
//   }

  public function getReplaceDataWithoutKey($listType, $flag = null) {
    $replaceWithOutKeyDatas = array();
    $replaceWithOutKeyDatasDefault = $listType;
    if(empty ($replaceWithOutKeyDatasDefault))
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
    } //print_r($replaceWithOutKeyDatas);die;
    return $replaceWithOutKeyDatas;
  }

  public function setTranslateForListType($listType, $flag= null, $recall = true) {
    //$lisitngType_id = $listType->listingtype_id;
    
    if($recall) {
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this->setTranslateForListType(array('text_stores' => '$plural$', 'text_store' => '$singular$'), $flag, false);
        if(empty($flag)) {
            $coreSettings->setSetting("language.phrases.stores", '$plural$');
            $coreSettings->setSetting("language.phrases.store", '$singular$');
        }else {
            $flag = 2;
        }
    }      
      
		$coreModulesTable = Engine_Api::_()->getDbtable('modules', 'core');
		$coreModulesTableName = $coreModulesTable->info('name');
		$select = $coreModulesTable->select()
													->from($coreModulesTableName)
													->where($coreModulesTableName . '.name LIKE ?', '%' . 'sitestore' . '%');
		$datas = $coreModulesTable->fetchAll($select);
		foreach ($datas as $data) {
			$fileName =   $data['name'] . '.csv';
			//$fileName =   'sitestore.csv';
		  $oldFileName = null;
		//  if ($listType->csv_file_name)
			//  $oldFileName = $listType->csv_file_name;

			$replaceDatas = array(); //$this->getReplaceData($listType);
			
			$replaceDataWithoutKey = $this->getReplaceDataWithoutKey($listType, $flag);
			$this->addLanguageFiles($fileName, $replaceDatas, $replaceDataWithoutKey, $oldFileName);
			//$listType->csv_file_name = $fileName;
			//$listType->save();
    }
  }

//   public function removeTranslateForListType($listType) {
//     $fileName = $this->_fileNamePre . strtolower($listType->title_plural) . ".csv";
//     $translate = Zend_Registry::get('Zend_Translate');
// 
//     // Prepare language list
//     $languageList = $translate->getList();
//     foreach ($languageList as $locale) {
//       $language_file = $this->_languagePath . '/' . $locale . '/' . $fileName;
// 
//       if (file_exists($language_file)) {
//         @unlink($language_file);
//       }
//     }
//   }

  public function convertData($datas, $replaceDatas, $replaceDataWithoutKey) {
    $data = array();
   // $noreplace = 'store_title';
    foreach ($datas as $data_key => $data) {
    
//     if (strstr($data, 'store_title') || strstr($data, 'store_description') || strstr($data, 'store_title_with_link') || strstr($data, 'store_url')) {
// 			continue;
//     }
//       foreach ($replaceDatas as $search => $replace) {
//         $pos = strpos($data_key, $search);
//         if ($pos !== false) {
//           if (isset($datas[$data_key]))
//             unset($datas[$data_key]);
//           $data_key = str_replace($search, $replace, $data_key);
//         }
//         $data = str_replace($search, $replace, $data);
//       }
      foreach ($replaceDataWithoutKey as $search => $replace) { 
      
        $data = str_replace($search, $replace, $data); 
				if (strstr($data, $replace . "_title")) {
					$data = str_replace($replace . "_title", 'store_title', $data);
				}
				if (strstr($data, $replace . "_description")) {
					$data = str_replace($replace . "_description", 'store_description', $data);
				}
				if (strstr($data, $replace . "_title_with_link")) {
					$data = str_replace($replace . "_title_with_link", 'store_title_with_link', $data);
				}
				if (strstr($data, $replace . "_url")) {
					$data = str_replace($replace . "_url", 'store_url', $data);
				}
      }
      //print_R();die;
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
			->where('name = ?', 'language.phrases.store');
		$language_store = $select->query()->fetch();

		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_settings')
			->where('name = ?', 'language.phrases.stores');
		$language_stores = $select->query()->fetch();
        
        if ((isset($language_stores['value']) && $language_stores['value'] == '$plural$') || (isset($language_store['value']) && $language_store['value'] == '$singular$')) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting( "language.phrases.stores" ,'stores');
            Engine_Api::_()->getApi('settings', 'core')->setSetting( "language.phrases.store" ,'store');
            return;
        }
        
		if (isset($language_stores['value']) && $language_stores['value'] != 'stores' && isset($language_store['value'])  && $language_store['value'] != 'store') {
			$language_pharse = array('text_stores' => $language_stores['value'], 'text_store' => $language_store['value']);
			Engine_Api::_()->getApi('language', 'sitestore')->setTranslateForListType($language_pharse, 1);
		}
		//END LANGUAGE WORK
  }
  
}