<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesbasic
 * @package    Sesbasic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Core.php 2015-07-25 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesbasic_Api_Core extends Core_Api_Abstract {
  public function isModuleEnable($name = '') {
    $moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
    return $moduleTable->select()->from($moduleTable->info('name'), new Zend_Db_Expr('COUNT(*)'))->where('name In (?)', $name)->where('enabled =?', 1)->query()->fetchColumn();
  }  
	public function checkSesPaymentExtentionsEnable(){
		$moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
    return $moduleTable->select()->from($moduleTable->info('name'), new Zend_Db_Expr('COUNT(*)'))->where('name In ("seseventticket","sesadvancedactivity", "sesvideosell", "sescrowdfunding")')->where('enabled =?', 1)->query()->fetchColumn();	
	}
  public function dateFormat($date = null,$changetimezone = '',$object = '',$formate = 'M d, Y h:m A') {
  
		if($changetimezone != '' && $date){
			$date = strtotime($date);
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($object->timezone);
			if($formate == '')
				$dateChange = date('Y-m-d h:i:s',$date);
			else{
				$dateChange = date('M d, Y h:i A',$date);
			}
			date_default_timezone_set($oldTz);
			return $dateChange.' ('.$object->timezone.')';
		}
    if($date){
      return date('M d, Y h:i A', strtotime($date));
    }
  }
	public function multiCurrencyActive(){
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$fullySupportedCurrencies = $this->getSupportedCurrency();
		foreach ($fullySupportedCurrencies as $key => $values) {
      if ($settings->getSetting('sesbasic.'.$key.'active',0)){
				//currency found  return true and exit.
				return true;
			}
    }	
		return false;
	}
	public function isMultiCurrencyAvailable(){
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$fullySupportedCurrencies = $this->getSupportedCurrency();
		foreach ($fullySupportedCurrencies as $key => $values) {
      if ($settings->getSetting('sesbasic.' . $key)){
        $fullySupportedCurrenciesExists[$key] = $values;
				//currency found  return true and exit.
				return true;
			}
    }	
		return false;
	}
	
	public function updateCurrencyValues(){
		$isMultiCurrencyAvailable = $this->multiCurrencyActive();
		if(!$isMultiCurrencyAvailable)
			return;
		$getDefaultCurrency = $this->defaultCurrency();
		if(!$getDefaultCurrency)
			return;
		$fullySupportedCurrencies = $this->getSupportedCurrency();
		//End chnage currency work
		$settings = Engine_Api::_()->getApi('settings', 'core');
		foreach($fullySupportedCurrencies as $key=>$value){ 
			$settings = Engine_Api::_()->getApi('settings', 'core');
			$getPriceActual = $settings->getSetting('sesbasic.'.$key);
			if($getDefaultCurrency == $key)
				continue;
			$currency = strtolower($getDefaultCurrency.$key);
			$url = 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20csv%20where%20url%3D%22http%3A%2F%2Ffinance.yahoo.com%2Fd%2Fquotes.csv%3Fe%3D.csv%26f%3Dnl1d1t1%26s%3D'.$currency.'%3DX%22%3B&format=json';
			$content = file_get_contents($url);
			$values = json_decode($content,true);
			if(isset($values['query']['results']['row']['col1'])){
			 $currencyValue =	@round($values['query']['results']['row']['col1'],2);
			 $settings->setSetting('sesbasic.' . $key, $currencyValue);
			}else
				continue;
		}	
	}
	
	public function getSupportedCurrency(){
		 // Populate currency options
    $supportedCurrencyIndex = array();
    $fullySupportedCurrencies = array();
    $supportedCurrencies = array();
    $gateways = array();
    $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    foreach ($gatewaysTable->fetchAll() as $gateway) {
      $gateways[$gateway->gateway_id] = $gateway->title;
      $gatewayObject = $gateway->getGateway();
      $currencies = $gatewayObject->getSupportedCurrencies();
      if (empty($currencies))
        continue;
      $supportedCurrencyIndex[$gateway->title] = $currencies;
      if (empty($fullySupportedCurrencies)) {
        $fullySupportedCurrencies = $currencies;
      } else {
        $fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
      }
      $supportedCurrencies = array_merge($supportedCurrencies, $currencies);
    }
    $supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);
    $translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
    $fullySupportedCurrencies = array_intersect_key($translationList, array_flip($fullySupportedCurrencies));
    return $fullySupportedCurrencies;	
	}
	 protected function getCurrencySymbolValue($price, $currency = '', $change_rate = '') {
    $currentCurrency = empty($_COOKIE['sesbasic_currencyId']) ? $currency : $_COOKIE['sesbasic_currencyId'];
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if ($currentCurrency != '') {
      $currencyValue = $settings->getSetting('sesbasic.' . $currentCurrency);
      if ($currencyValue != '' && $change_rate == '') {
        return $currencyValue * $price;
      } else if ($change_rate != '') {
        return $change_rate * $price;
      }
    }
    return '';
  }
	 //return price with symbol and change rate param for payment history.
  public function getCurrencyPrice($price = 0, $givenSymbol = '', $change_rate = '') {
    //if (empty($price) || $price == 0)
      //return 0;
		$price = (float)$price;
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $precisionValue = $settings->getSetting('sesbasic.precision', 2);
    $defaultParams['precision'] = $precisionValue;
    if ($givenSymbol == '') {
      $defaultCurrency = $settings->getSetting('sesbasic.defaultcurrency', 'USD');
      if (isset($_COOKIE['sesbasic_currencyId']) && !empty($_COOKIE['sesbasic_currencyId']) && $_COOKIE['sesbasic_currencyId'] != $defaultCurrency) {
        $changePrice = $this->getCurrencySymbolValue($price, '', $change_rate);
        $currency = $_COOKIE['sesbasic_currencyId'];
        if ($changePrice != '')
          $price = $changePrice;
      } else
        $currency = $defaultCurrency;
    }else if ($change_rate != '') {
      $changePrice = $this->getCurrencySymbolValue($price, '', $change_rate);
      if ($changePrice != '')
        $price = $changePrice;
      $currency = $givenSymbol;
    } else
      $currency = $givenSymbol;
    $priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($price, $currency, $defaultParams);
    return $priceStr;
  }
  public function getCurrentCurrency() {
    return empty($_COOKIE['sesbasic_currencyId']) ? Engine_Api::_()->getApi('settings', 'core')->getSetting('sesbasic.defaultcurrency', 'USD') : $_COOKIE['sesbasic_currencyId'];
  }
  public function defaultCurrency() {
    return Engine_Api::_()->getApi('settings', 'core')->getSetting('sesbasic.defaultcurrency', 'USD');
  }
	
	public function checkAdultContent($params = array()){
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.allow.adult.filtering',1);
		if(!$enable)
			return 1;
		$viewer_id = $viewer->getIdentity();
		if($viewer_id == 0){
			return isset($_COOKIE['ses_adult_filter']) ? $_COOKIE['ses_adult_filter'] : 1;
		}else{
			return Engine_Api::_()->getApi('settings', 'core')->getSetting('ses.allow.adult.content.'.$viewer_id, 1);	
		}
	}
  public function pluginVersion($name = null) {
    $moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
    return $moduleTable->select()->from($moduleTable->info('name'), array('version'))->where('name =?', $name)->where('enabled =?', 1)->query()->fetchColumn();
  }
	//get location based cookie data
	function getUserLocationBasedCookieData(){
		$locationVal = $lat = $lng = '';
		if(isset($_COOKIE['sesbasic_location_data']) && isset($_COOKIE['sesbasic_location_lat']) && isset($_COOKIE['sesbasic_location_lng'])){
			$locationVal = $_COOKIE['sesbasic_location_data'];
			$lat = $_COOKIE['sesbasic_location_lat'];
			$lng = $_COOKIE['sesbasic_location_lng'];
		}
		return array('location'=>$locationVal,'lat'=>$lat,'lng'=>$lng);
	}
	
	 //get next previous item for other module.
  public function SesNextPreviousPhoto($photo_item, $condition, $resourcePhoto, $child_id, $parent_id, $allPhoto = false) {
    $GetTableNamePhotoMain = Engine_Api::_()->getItemTable($resourcePhoto);
    $tableNamePhotoMain = $GetTableNamePhotoMain->info('name');
    $select = $GetTableNamePhotoMain->select()
            ->from($tableNamePhotoMain);
    if (!$allPhoto) {
      $select->where("$tableNamePhotoMain.$child_id $condition  ?", $photo_item->$child_id)->limit(1);
      ;
    }
    $select->where("$tableNamePhotoMain.$parent_id =  ?", $photo_item->$parent_id);
    if ($allPhoto) {
      $select->order("$tableNamePhotoMain.$child_id ASC");
      return Zend_Paginator::factory($select);
    }
    if ($condition == '<')
      $select->order($tableNamePhotoMain . ".$child_id DESC");
    return $GetTableNamePhotoMain->fetchRow($select);
  }  
  public function pluginInstalled($name = null) {
    $moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
    return $moduleTable->select()->from($moduleTable->info('name'), array('name'))->where('name =?', $name)->query()->fetchColumn();
  }
  public function textTruncation($text, $textLength = null) {
    $text = strip_tags($text);
    return ( Engine_String::strlen($text) > $textLength ? Engine_String::substr($text, 0, $textLength) . '...' : $text);
  }
	
	public function pageTabIdOnPage($widgetName,$pageName,$type = 'widget') {
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    $contentTableName = $contentTable->info('name');
		$pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pageTableName = $pageTable->info('name');
    $select = $contentTable->select()
            ->setIntegrityCheck(false)
            ->from($contentTableName)
						->join($pageTableName, $pageTableName.".page_id = .".$contentTableName.".page_id  ",null)
            ->where($pageTableName . '.name = ?', $pageName)
            ->where($contentTableName . '.name = ?', $widgetName)
						->where($contentTableName . '.type = ?', $type);
    return $contentTable->fetchRow($select);
  }
	
  public function isWidgetEnable($type = 'widget', $name = '') {
    $widgetTable = Engine_Api::_()->getDbTable('content', 'core');
    return $widgetTable->select()
            ->from($widgetTable, 'content_id')
            ->where($widgetTable->info('name') . '.type = ?', $type)
            ->where($widgetTable->info('name') . '.name = ?', $name)
            ->query()
            ->fetchColumn();
  }
	public function getUserFnameLname($user_id = null){
		//if no user id given take logged in user details
		$returnRes = array();
		if(!$user_id){
			$user = Engine_Api::_()->user()->getViewer();
			if($user->getIdentity() != 0)
				$user_id = $user->getIdentity();
			else
				return $returnRes;
		}
			$db = Engine_Db_Table::getDefaultAdapter();
			$result = $db->query("SELECT  mv.value,mf.type FROM engine4_user_fields_values as mv LEFT JOIN engine4_user_fields_meta as mf ON (mf.field_id = mv.field_id) WHERE mv.item_id = ".$user_id." && (mf.type = 'first_name' || mf.type = 'last_name')")->fetchAll();
			if(count($result)){
					foreach($result as $val){
						if(isset($val['type']) && $val['type'] == 'first_name')	
							$returnRes['first_name'] = $val['value'];
						else
							$returnRes['last_name'] = $val['value'];
					}
			}	
		return $returnRes;
	}
  public function getWidgetTabId($params = array()) {
    $table = Engine_Api::_()->getDbTable('content', 'core');
    return $table->select()
                    ->from($table, 'content_id')
                    ->where('name =?', $params['name'])
                    ->query()
                    ->fetchColumn();
  }
    // get photo like status
  public function getLikeStatus($resource_id = '', $resource_type = '') {;
    if ($resource_id != '') {
      $userId = Engine_Api::_()->user()->getViewer()->getIdentity();
      if ($userId == 0)
        return false;
      $coreLikeTable = Engine_Api::_()->getDbtable('likes', 'core');
      $total_likes = $coreLikeTable->select()->from($coreLikeTable->info('name'), new Zend_Db_Expr('COUNT(like_id) as like_count'))->where('resource_type =?', $resource_type)->where('poster_id =?', $userId)->where('poster_type =?', 'user')->where('	resource_id =?', $resource_id)->limit(1)->query()->fetchColumn();
      if ($total_likes > 0) {
        return true;
      } else {
        return false;
      }
    }
    return false;
  }
  public function getwidgetizePage($params = array()) {

    $corePages = Engine_Api::_()->getDbtable('pages', 'core');
    $corePagesName = $corePages->info('name');
    $select = $corePages->select()
            ->from($corePagesName, array('*'))
            ->where('name = ?', $params['name'])
            ->limit(1);
    return $corePages->fetchRow($select);
  }
  
  public function totalSiteMembersCount() {
  
	  $table = Engine_Api::_()->getDbtable('users', 'user');
    $info = $table->select()
            ->from($table, array('COUNT(*) AS count'))
            ->where('enabled = ?', true)
            ->query()
            ->fetch();
    return $info['count'];
  }
    
  //Change System Mode of Site
  public function changeEnvironmentMode($system_mode) {
   
    if ($system_mode == 1) {
      $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
      if( file_exists($global_settings_file) ) {
        $g = include $global_settings_file;
        if (!is_array($g)) {
          $g = (array) $g;
        }
      } else {
        $g = array();
      }
      if (!is_writable($global_settings_file)) {
        if (!is_writable($global_settings_file)) {
          $this->view->success = false;
          $this->view->error   = 'Unable to write to settings file; please CHMOD 666 the file /application/settings/general.php, then try again.';
          return;
        } else {
          // it worked; continue.
        }
      }

      if ($system_mode == 1) {
        $g['environment_mode'] = 'development';
        $file_contents  = "<?php defined('_ENGINE') or die('Access Denied'); return ";
        $file_contents .= var_export($g, true);
        $file_contents .= "; ?>";
        $this->view->success = @file_put_contents($global_settings_file, $file_contents);
        // clear scaffold cache
        Core_Model_DbTable_Themes::clearScaffoldCache();
        // Increment site counter
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $settings->core_site_counter = $settings->core_site_counter + 1;
        return;
      }
    }
  }
  
  public function checkPluginVersion($moduleName, $sesbasic_currentversion) {
	  $db = Engine_Db_Table::getDefaultAdapter();
		$select = new Zend_Db_Select($db);
		$select->from('engine4_core_modules')
						->where('name = ?', $moduleName);
		$results = $select->query()->fetchObject();
		$sesbasic_enabled = $results->version;
		$sesbasicSiteversion = @explode('p', $sesbasic_currentversion);
		$sesbasiCurrentversionE = @explode('p', $sesbasic_enabled);    
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
			} else{
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
	//upload photo with watermark
	//watermark on photo
	function watermark_image($oldimage_name, $new_image_name,$type,$image_path,$modulename){
		ini_set('memory_limit','1024M');
    list($owidth,$oheight) = getimagesize($oldimage_name);
    $width = $sourcefile_width =$owidth; $height = $sourcefile_height = $oheight;
    $im = imagecreatetruecolor($width, $height);
		if(strpos(strtolower($type),'png') !== FALSE)
			$image_type = 'png';
		else if(strpos(strtolower($type),'jpg')  !== FALSE || strpos(strtolower($type),'jpeg')  !== FALSE )
			$image_type = 'jpeg';
		else if( strpos(strtolower($type),'gif') !== FALSE)
			$image_type = 'gif';
		switch ($image_type)
    {
      case 'gif': $img_src = imagecreatefromgif($oldimage_name); break;
      case 'jpeg': $img_src = imagecreatefromjpeg($oldimage_name); break;
      case 'png': $img_src = imagecreatefrompng($oldimage_name); break;
      default:  return false;break;
    }
		imagecopyresampled($im, $img_src, 0, 0, 0, 0, $width, $height, $owidth, $oheight);
    $watermark = imagecreatefrompng($image_path);
    list($insertfile_width, $insertfile_height) = getimagesize($image_path);        
		
    $pos = Engine_Api::_()->getApi('settings', 'core')->getSetting($modulename.'.position.watermark', 0);
		//middle 
    if( $pos == 0 ) 
    { 
        $dest_x = ( $sourcefile_width / 2 ) - ( $insertfile_width / 2 ); 
        $dest_y = ( $sourcefile_height / 2 ) - ( $insertfile_height / 2 ); 
    } 
		//top left 
    else if( $pos == 1 ) 
    { 
        $dest_x = 0; 
        $dest_y = 0; 
    } 
//top right 
   else if( $pos == 2 ) 
    { 
        $dest_x = $sourcefile_width - $insertfile_width; 
        $dest_y = 0; 
    } 

//bottom right 
    else if( $pos == 3 ) 
    { 
        $dest_x = $sourcefile_width - $insertfile_width; 
        $dest_y = $sourcefile_height - $insertfile_height; 
    } 

//bottom left    
    else if( $pos == 4 ) 
    { 
        $dest_x = 0; 
        $dest_y = $sourcefile_height - $insertfile_height; 
    } 

//top middle 
    else if( $pos == 5 ) 
    { 
        $dest_x = ( ( $sourcefile_width - $insertfile_width ) / 2 ); 
        $dest_y = 0; 
    } 

//middle right 
    else if( $pos == 6 ) 
    { 
        $dest_x = $sourcefile_width - $insertfile_width; 
        $dest_y = ( $sourcefile_height / 2 ) - ( $insertfile_height / 2 ); 
    } 
        
//bottom middle    
    else if( $pos == 7 ) 
    { 
        $dest_x = ( ( $sourcefile_width - $insertfile_width ) / 2 ); 
        $dest_y = $sourcefile_height - $insertfile_height; 
    } 

//middle left 
    else if( $pos == 8 ) 
    { 
        $dest_x = 0; 
        $dest_y = ( $sourcefile_height / 2 ) - ( $insertfile_height / 2 ); 
    } 
    
    
    imagecopy($im, $watermark, $dest_x, $dest_y, 0, 0, $insertfile_width, $insertfile_height);
    imagejpeg($im, $new_image_name, 100);
    imagedestroy($im);
    @unlink($oldimage_name);
    return true;
	}
	//upload photo
  public function setPhoto($photo,$isURL = false,$isUploadDirect = false,$modulename,$memberlevelType,$photoParams = array(),$item, $package = false)
  {
		if(!$isURL){
			if( $photo instanceof Zend_Form_Element_File ) {
				$file = $photo->getFileName();
				$fileName = $file;
			} else if( $photo instanceof Storage_Model_File ) {
				$file = $photo->temporary();
				$fileName = $photo->name;
			} else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
				$tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
				$file = $tmpRow->temporary();
				$fileName = $tmpRow->name;
			} else if( is_array($photo) && !empty($photo['tmp_name']) ) {
				$file = $photo['tmp_name'];
				$fileName = $photo['name'];
			} else if( is_string($photo) && file_exists($photo) ) {
				$file = $photo;
				$fileName = $photo;
			} else {
				throw new User_Model_Exception('invalid argument passed to setPhoto');
			}
			  $name = basename($file);
				$extension = ltrim(strrchr($fileName, '.'), '.');
				$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		}else{
			$fileName = time().'_'.$modulename;
			$PhotoExtension='.'.pathinfo($photo, PATHINFO_EXTENSION);
			$filenameInsert=$fileName.$PhotoExtension;
			$copySuccess=@copy($photo, APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.$filenameInsert);
			if($copySuccess)
				$file = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'.DIRECTORY_SEPARATOR.$filenameInsert;
			else	
				return false;
			$name = basename($photo);
			$extension = ltrim(strrchr($name, '.'), '.');
			$base = rtrim(substr(basename($name), 0, strrpos(basename($name), '.')), '.');
		}
    if( !$fileName ) {
      $fileName = $file;
    }
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $item->getType(),
      'parent_id' => $item->getIdentity(),
      'name' => $fileName,
    );
    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
		/*setting of image dimentions from core settings*/
		$core_settings = Engine_Api::_()->getApi('settings', 'core');
    $main_height = $core_settings->getSetting($modulename.'.mainheight', 1600);
		$main_width = $core_settings->getSetting($modulename.'.mainwidth', 1600);
		$normal_height = $core_settings->getSetting($modulename.'.normalheight', 500);
		$normal_width = $core_settings->getSetting($modulename.'.normalwidth', 500);
    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize($main_width, $main_height)
      ->write($mainPath)
      ->destroy();
		// Resize image (normal) make same image for activity feed so it open in pop up with out jump effect.
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize($normal_width, $normal_height)
      ->write($normalPath)
      ->destroy();
		//watermark on main photo
		if(!$isUploadDirect){
			$enableWatermark = $core_settings->getSetting($modulename.'.watermark.enable', 0);
			if($enableWatermark == 1){
			$viewer = Engine_Api::_()->user()->getViewer();
			$watermarkImage = Engine_Api::_()->authorization()->getPermission($viewer->level_id,$memberlevelType, 'watermark');
				if(is_file($watermarkImage)){
					if(isset($extension))
						$type = $extension;
					else
						$type = $PhotoExtension;
					$mainFileUploaded =   APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'.DIRECTORY_SEPARATOR.$name;
					$fileName = current(explode('/',$name));
					$fileName = explode('.', $fileName);
					if(isset($fileName[0]))
					$name = $fileName[0];
					else
					$name = time();
					$fileNew = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'.DIRECTORY_SEPARATOR.time().'_'.$name.".jpg";
					$watemarkImageResult = $this->watermark_image($mainPath, $fileNew,$type,$watermarkImage,$modulename);
					if($watemarkImageResult){
						@unlink($mainPath);
						$image->open($fileNew)
									->resize($main_width, $main_height)
									->write($mainPath)
									->destroy();
						@unlink($fileNew);
					}
					$watermarkImageNew = Engine_Api::_()->authorization()->getPermission($viewer->level_id,$memberlevelType, 'watermarkthumb');
						if(!is_file($watermarkImageNew)){
							$fileNew = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'.DIRECTORY_SEPARATOR.time().'_'.$fileName.".jpg";
							$watemarkImageResult = $this->watermark_image($normalPath, $fileNew,$type,$watermarkImage,$modulename);
							if($watemarkImageResult){
								@unlink($normalPath);
								$image->open($fileNew)
											->resize($main_width, $main_height)
											->write($normalPath)
											->destroy();
								@unlink($fileNew);
							}
						}
					}
				}		
			}
			
			//thumb photo watermark
			if($enableWatermark == 1){
			$viewer = Engine_Api::_()->user()->getViewer();
			$watermarkImage = Engine_Api::_()->authorization()->getPermission($viewer->level_id,$memberlevelType, 'watermarkthumb');
				if(is_file($watermarkImage)){
					if(isset($extension))
						$type = $extension;
					else
						$type = $PhotoExtension;
					$fileNew = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'.DIRECTORY_SEPARATOR.time().'_'.$fileName.".jpg";
					$watemarkImageThumbResult = $this->watermark_image($normalPath, $fileNew,$type,$watermarkImage,$modulename);
					if($watemarkImageThumbResult){
						@unlink($normalPath);
						$image->open($fileNew)
									->resize($normal_width, $normal_height)
									->write($normalPath)
									->destroy();
						@unlink($fileNew);
				}		
			}			
		}
		// normal main  image resize
    $normalMainPath = $path . DIRECTORY_SEPARATOR . $base . '_nm.' . $extension;
    $image = Engine_Image::factory();
    $image->open($normalPath)
      ->resize($normal_width, $normal_height)
      ->write($normalMainPath)
      ->destroy();
		// Resize image (icon)
    $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file);
    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;
    $image->resample($x, $y, $size, $size, 150, 150)
      ->write($squarePath)
      ->destroy();
    // Store
    try {
			$iSquare = $filesTable->createFile($squarePath, $params);
      $iMain = $filesTable->createFile($mainPath, $params);
      $iIconNormal = $filesTable->createFile($normalPath, $params);
			$iNormalMain = $filesTable->createFile($normalMainPath, $params);
			$iMain->bridge($iNormalMain, 'thumb.normalmain');
			$iMain->bridge($iIconNormal, 'thumb.normal');
			$iMain->bridge($iSquare, 'thumb.icon');
    } catch( Exception $e ) {
			@unlink($file);
      // Remove temp files
      @unlink($mainPath);
      @unlink($normalPath);
			@unlink($squarePath);
			@unlink($normalMainPath);
      // Throw
      if( $e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE ) {
        throw new Sesbasic_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }
			@unlink($file);
    // Remove temp files
      @unlink($mainPath);
      @unlink($normalPath);
			@unlink($squarePath);
			@unlink($normalMainPath);
    // Delete the old file?
    if( !empty($tmpRow) ) {
      $tmpRow->delete();
    }
    if($package)
		 return $iMain->file_id;;
		 $photoParams['file_id'] = $iMain->file_id; // This might be wrong
     $photoParams['photo_id'] = $iMain->file_id;
		 $row = Engine_Api::_()->getDbtable('photos', $modulename)->createRow();
		 
     $row->setFromArray($photoParams);
     $row->save();
     return $row;
  }
  
  public function getRow(Core_Model_Item_Abstract $resource, User_Model_User $user) {
  
    $id = $resource->getIdentity().'_'.$user->getIdentity();
    $table = Engine_Api::_()->getDbTable('membership', 'user');
    $select = $table->select()
      ->where('resource_id = ?', $resource->getIdentity())
      ->where('user_id = ?', $user->getIdentity());
    $select = $select->limit(1);
    $row = $table->fetchRow($select);
    return $row;
  }
  
  public function getColumnName($value) {
 
    switch ($value) {
      case 'recently created': 
      $optionKey = 'creation_date DESC';
      break;
          case 'most viewed': 
      $optionKey = 'view_count DESC';
      break;
          case 'most liked': 
      $optionKey = 'like_count DESC';
      break;
          case 'most rated': 
      $optionKey = 'rating DESC';
      break;
          default:
      $optionKey = $value;
    };
    return $optionKey;
  }
  
  public function hasCheckMessage($user) {

    // Not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity() || $viewer->getGuid(false) === $user->getGuid(false)) {
      return false;
    }

    // Get setting?
    $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
    if (Authorization_Api_Core::LEVEL_DISALLOW === $permission) {
      return false;
    }
    $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
    if ($messageAuth == 'none') {
      return false;
    } else if ($messageAuth == 'friends') {
      // Get data
      $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
      if (!$direction) {
        //one way
        $friendship_status = $viewer->membership()->getRow($user);
      }
      else
        $friendship_status = $user->membership()->getRow($viewer);

      if (!$friendship_status || $friendship_status->active == 0) {
        return false;
      }
    }
    return true;
  }
  
  public function getIdentityWidget($name, $type, $corePages) {
    $widgetTable = Engine_Api::_()->getDbTable('content', 'core');
    $widgetPages = Engine_Api::_()->getDbTable('pages', 'core')->info('name');
    $identity = $widgetTable->select()
            ->setIntegrityCheck(false)
            ->from($widgetTable, 'content_id')
            ->where($widgetTable->info('name') . '.type = ?', $type)
            ->where($widgetTable->info('name') . '.name = ?', $name)
            ->where($widgetPages . '.name = ?', $corePages)
            ->joinLeft($widgetPages, $widgetPages . '.page_id = ' . $widgetTable->info('name') . '.page_id')
            ->query()
            ->fetchColumn();
    return $identity;
  }
  
  public function getViewerPrivacy($resourceType= null, $privacy = null) {
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewerId = $viewer->getIdentity();
    if(!$viewerId) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $select = new Zend_Db_Select($db);
      $select->from('engine4_authorization_levels', 'level_id')->where('type = ?', 'public');
      $levelId = $select->query()->fetchColumn();
      return Engine_Api::_()->authorization()->getPermission($levelId, $resourceType, $privacy);
    }
    else {
      return Engine_Api::_()->authorization()->getPermission($viewer, $resourceType, $privacy);
    }
  }
  public function facebookShareUrl($href = '',$subject = ''){
    if(!$href)
      return 'javascript:;';
    $href = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] .$href;
    return 'https://www.facebook.com/sharer/sharer.php?u='.urlencode($href);
    
  }
  public function twitterShareUrl($href = '',$subject = ''){
    if(!$href)
      return 'javascript:;';
     $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $href);
     return 'https://twitter.com/share?url=' . $urlencode . '&text=' . htmlspecialchars(urlencode(html_entity_decode($subject->getTitle('encode'), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8')."%0a";
  }
  public function googlePlusShareUrl($href = '', $subject = ''){
    if(!$href)
      return 'javascript:;';
     $href = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] .$href;
    return 'https://plus.google.com/share?url='.urlencode($href).'&t='.$subject->getTitle();
  }
  public function LinkedinShareUrl($href = '', $subject = ''){
    if(!$href)
      return 'javascript:;';
     $href = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] .$href;
    return 'https://www.linkedin.com/shareArticle?mini=true&url='.$href;
  }
   public function getFieldsStructurePartial($spec, $parent_field_id = null)
  {
    // Spec must be a item for this one
    if( !($spec instanceof Core_Model_Item_Abstract) )
    {
      throw new Fields_Model_Exception("First argument of getFieldsValues must be an instance of Core_Model_Item_Abstract");
    }

    $type = Engine_Api::_()->fields()->getFieldType($spec);
    $parentMeta = null;
    $parentValue = null;

    // Get current field values
    if( $parent_field_id ) {
      $parentMeta = Engine_Api::_()->fields()->getFieldsMeta($type)->getRowMatching('field_id', $parent_field_id);
      $parentValueObject = $parentMeta->getValue($spec);
      if( is_array($parentValueObject) ) {
        $parentValue = array();
        foreach( $parentValueObject as $parentValueObjectSingle ) {
          $parentValue[] = $parentValueObjectSingle->value;
        }
      } else if( is_object($parentValueObject) ) {
        $parentValue = $parentValueObject->value;
      }
    }

    // Build structure
    $structure = array();
    foreach( Engine_Api::_()->fields()->getFieldsMaps($spec)->getRowsMatching('field_id', (int) $parent_field_id) as $map ) {
      // Get child field
      $field = Engine_Api::_()->fields()->getFieldsMeta($type)->getRowMatching('field_id', $map->child_id);
      if( empty($field) ) {
        continue;
      }
      // Add to structure
      $structure[$map->getKey()] = $map;
      // Get dependents
      if( $field->canHaveDependents() )
      {
        $structure += $this->getFieldsStructurePartial($spec, $field->field_id);
      }
    }
    
    return $structure;
  }
}