<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitestoreproduct
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ExportController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_ExportController extends Seaocore_Controller_Action_Standard {

  public function indexAction() {
    
    //ONLY LOGGED IN USER CAN EXPORT
    if (!$this->_helper->requireUser()->isValid())
        return;
        
    $store_id = $this->_getParam('store_id', null);
    $checkedProduct = $this->_getParam('checked_products', null); 
    $searchParams = $this->_getParam('searchParams', null);
    
    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

    //IS USER IS STORE ADMIN OR NOT
    if (empty($authValue))
        return $this->_forward('requireauth', 'error', 'core');
    else if ($authValue == 1)
        return $this->_forward('notfound', 'error', 'core');
    
    if(!empty($searchParams))
      $searchParams = Zend_Json::decode($searchParams);
    
    if(!empty($checkedProduct)) {
      $selected_product = trim($checkedProduct, ",");
      $selected_product = str_replace("<", "", $selected_product);
      $selected_product = str_replace(">", "", $selected_product);
      $selected_product = trim(trim($selected_product, ""), ",");
      $searchParams['product_ids'] = $selected_product;
    }
    
    $searchParams['store_id'] = $store_id;
    $searchParams['selected_product_types'] = array('simple', 'configurable', 'virtual', 'downloadable');
    
    $select = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->getSitestoreproductsSelect($searchParams);
    $exportedProductsObj = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->fetchAll($select);
//    $exportedProductsObj = Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->exportProduct($store_id, $searchParams);
    $getExtraCode = '';
    $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    foreach ($exportedProductsObj as $product) {
      $section_name = '';
      $category_name = '';
      $subcategory_name = '';
      $subsubcategory_name = '';
      
      // WORK OF SECTION
      if (!empty($product['section_id']))
        $section_name = Engine_Api::_()->getDbtable('sections', 'sitestoreproduct')->getSectionName($product['section_id']);

      // WORK OF ALL IMAGES INCLUDING PROFILE IMAGE
      $images = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct')->GetProductPhoto($product['product_id'], array('limit' => $this->_getParam('slidesLimit', 6), 'order' => 'order ASC'));
      $photoUrl = array();
      foreach ($images as $imageFileId) {
        $tmpRow = Engine_Api::_()->getItem('storage_file', $imageFileId['file_id']);
        if (!empty($tmpRow))
          $photoUrl[] = $tmpRow->getPhotoUrl();
      }

      for($temp =0;$temp<6;$temp++){
        if(strpos($photoUrl[$temp],'?') !== false){
          $explodedAlbumImage = explode("?", $photoUrl[$temp]);
          $photoUrl[$temp] = $explodedAlbumImage[0];
        }
        if(!strstr($photoUrl[$temp], 'http') && strstr($photoUrl[$temp], 'public/')){
          $return_url = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
          $photoUrl[$temp] = $return_url . $_SERVER['HTTP_HOST'] . $photoUrl[$temp];
        }
      }
      
      // WORK OF CATEGORY
      if (!empty($product['category_id'])) {
        $category_name = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoryNameById($product['category_id']);
        if (!empty($product['subcategory_id'])) {
          $subcategory_name = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoryNameById($product['subcategory_id']);
          if (!empty($product['subsubcategory_id']))
            $subsubcategory_name = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoryNameById($product['subsubcategory_id']);
        }
      }
       
      // REMOVE EXTRA NEW LINE FROM DESCRIPTION
       $product['body'] = str_replace("\n", "", $product['body']);
       $product['body'] = str_replace("\r", "", $product['body']);

       $getExtraCode .= $product['title'] . "|" . $product['body'] . "|" . $product['product_code'] . "|" . $product['product_type'] . "|" . $category_name . "|" . $subcategory_name . "|" . $subsubcategory_name . "|" . $product['price'] . "|" . $product['weight'] . "|" . $product['in_stock'] . "|" . $section_name . "|" . $photoUrl[0] . "|" . $photoUrl[1] . "|" . $photoUrl[2] . "|" . $photoUrl[3] . "|" . $photoUrl[4] . "|" . $photoUrl[5];
       
       // MULTILANGUAGE EXPORT WORK STARTS
      $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
      $languages = Engine_Api::_()->sitestoreproduct()->getLanguages();
      if (count($languages) > 1) {
        $localeMultiOptions = Engine_Api::_()->sitestoreproduct()->getLanguageArray();

          foreach ($languages as $label) {

            if ($label == 'en')
              continue;

            $lang_name = $label;
            if (isset($localeMultiOptions[$label])) {
              $lang_name = $localeMultiOptions[$label];
            }

            $title_field = "title_$label";
            $body_field = "body_$label";

            $product_attribute = $productTable->getProductAttribute(array($title_field, $body_field), array('product_id' => $product['product_id']), false);
            $product_attribute = $productTable->fetchRow($product_attribute);
            $product_attribute[$body_field] = str_replace("\n", "", $product_attribute[$body_field]);
            $product_attribute[$body_field] = str_replace("\r", "", $product_attribute[$body_field]);

            $getExtraCode .= "|" . $product_attribute[$title_field] . "|" . $product_attribute[$body_field];
          }
        }

      $getExtraCode .= "\n";
      // MULTILANGUAGE EXPORT WORK ENDS
    }
    $getExtraCode = @trim($getExtraCode, " \n.");
    $myFile = APPLICATION_PATH . "/application/modules/Sitestoreproduct/settings/export_products.csv";
    $is_file_exist = file_exists($myFile);
    if (empty($is_file_exist)) {
      return;
    }
    @chmod($myFile, 0777);
    $fh = fopen($myFile, 'w') or die("can't open file");
    fwrite($fh, $getExtraCode);
    fclose($fh);

    $basePath = realpath(APPLICATION_PATH . "/application/modules/Sitestoreproduct/settings");
    $path = $this->_getPath();

    if (file_exists($path) && is_file($path)) {
 // zend's ob
      $isGZIPEnabled = false;
      if (ob_get_level()) {
        $isGZIPEnabled = true;
          @ob_end_clean();
      }

      header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
      header("Content-Transfer-Encoding: Binary", true);
      header("Content-Type: application/x-tar", true);
      header("Content-Type: application/force-download", true);
      header("Content-Type: application/octet-stream", true);
      header("Content-Type: application/download", true);
      header("Content-Description: File Transfer", true);
      if(empty($isGZIPEnabled))
      header("Content-Length: " . filesize($path), true);
      readfile("$path");
    }
    exit();
  }

  protected function _getPath($key = 'path') {
    $basePath = realpath(APPLICATION_PATH . "/application/modules/Sitestoreproduct/settings");
     @chmod($basePath, 0777);
    return $this->_checkPath('export_products.csv', $basePath);
  }

  protected function _checkPath($path, $basePath) {
    //SANATIZE
    $path = preg_replace('/\.{2,}/', '.', $path);
    $path = preg_replace('/[\/\\\\]+/', '/', $path);
    $path = trim($path, './\\');
    $path = $basePath . '/' . $path;

    //Resolve
    $basePath = realpath($basePath);
    $path = realpath($path);

    //CHECK IF THIS IS A PARENT OF THE BASE PATH
    if ($basePath != $path && strpos($basePath, $path) !== false) {
      return $this->_helper->redirector->gotoRoute(array());
    }
    return $path;
  }
}