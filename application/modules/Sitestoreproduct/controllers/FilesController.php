<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FilesController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_FilesController extends Core_Controller_Action_Standard {

  protected $_filePath;

  public function init() {    
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
      return;

    if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
      return;

    //ONLY LOGGED IN USER CAN CREATE
    if (!$this->_helper->requireUser()->isValid())
      return;

    //PAGE ID 
    $product_id = $this->_getParam('product_id', NULL);
    
    $product_type = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id)->product_type;
    
    if($product_type != 'downloadable')
      return $this->_forward('notfound', 'error', 'core');
            
    $store_id = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id)->store_id;
    $authValue = Engine_Api::_()->sitestoreproduct()->isStoreAdmin($store_id);

    //IS USER IS PAGE ADMIN OR NOT
    if (empty($authValue))
      return $this->_forward('requireauth', 'error', 'core');
    else if ($authValue == 1)
      return $this->_forward('notfound', 'error', 'core');

    $this->_filePath = (string) APPLICATION_PATH . '/public/sitestoreproduct_product/file_' . $product_id;
  }

  public function indexAction() { 
    $this->view->product_id = $product_id = $this->_getParam('product_id', NULL);

    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

    $this->view->sitestores_view_menu = 14;
    $this->view->published = $sitestoreproduct->search;

    //set item count per page to show all records on a page
    $uploadLimit = Engine_Api::_()->sitestoreproduct()->getUploadLimit($sitestoreproduct->store_id);
    $maxItem = max($uploadLimit['sitestoreproduct_main_files'], $uploadLimit['sitestoreproduct_sample_files']);

    $params = array();
    $params['product_id'] = $product_id;
    $params['type'] = 'main';
    $params['limit'] = $maxItem;
    $params['page'] = $this->_getParam('page', 1);

    //PAGINATOR FOR MAIN FILES
    $this->view->paginator = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->getDownloadableFilesPaginator($params);
  }
  
   public function sampleAction() {
    $this->view->product_id = $product_id = $this->_getParam('product_id', NULL);

    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

    $this->view->TabActive = "downloadable";
    $this->view->published = $sitestoreproduct->search;

    //set item count per page to show all records on a page
    $uploadLimit = Engine_Api::_()->sitestoreproduct()->getUploadLimit($sitestoreproduct->store_id);
    $maxItem = max($uploadLimit['sitestoreproduct_main_files'], $uploadLimit['sitestoreproduct_sample_files']);

    $params = array();
    $params['product_id'] = $product_id;
    $params['limit'] = $maxItem;
    $params['page'] = $this->_getParam('page', 1);
    $params['type'] = 'sample';

    //PAGINATOR FOR MAIN FILES
    $this->view->paginator = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct')->getDownloadableFilesPaginator($params);
  }
  
  public function uploadFileAction() {
    $this->view->uploadType = $uploadType = $this->_getParam('type', NULL);
    $this->view->product_id = $product_id = $this->_getParam('product_id', null);
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
    $this->view->published = $sitestoreproduct->search;

//    $this->view->TabActive = "upload";
    $this->view->TabActive = "downloadable";

    $this->view->path = $path = $this->view->relPath = $relPath = $this->_filePath . '/main';
    if ($uploadType != 'main') {
      $this->view->path = $path = $this->view->relPath = $relPath = $this->_filePath . '/sample';
    }

    //VALIDATION REGARDING NUBER OF FILES
    $store_id = $sitestoreproduct->store_id;
    $uploadLimit = Engine_Api::_()->sitestoreproduct()->getUploadLimit($store_id);
    $downloadableFileTable = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct');
    $currentUploadStats = $downloadableFileTable->getUploadStatus(array("product_id" => $product_id, "type" => $uploadType));

    if( $uploadType == 'main' && !empty($uploadLimit['sitestoreproduct_main_files']) )
    {
      if ($currentUploadStats['files'] >= $uploadLimit['sitestoreproduct_main_files']) {
        $error = $this->view->translate("You will not permitted for uploading file because you already achived the maximum number of uplode %s files.", $uploadLimit['sitestoreproduct_main_files']);
        $this->view->error = Zend_Registry::get('Zend_Translate')->_($error);
        return;
      }
    }

    if( $uploadType == 'sample' && !empty($uploadLimit['sitestoreproduct_sample_files']) )
    {
      if ($currentUploadStats['files'] >= $uploadLimit['sitestoreproduct_sample_files']) {
        $error = $this->view->translate("You will not permitted for uploading file because you already achived the maximum number of uplode %s files.", $uploadLimit['sitestoreproduct_sample_files']);
        $this->view->error = Zend_Registry::get('Zend_Translate')->_($error);
        return;
      }
    }

    $this->view->form = $form = new Sitestoreproduct_Form_File_ProductUpload(array('type' => $uploadType));

    if (!$this->getRequest()->isPost())
      return;

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $formValues = $this->getRequest()->getPost();


    // START FILE VALIDATIONS
    if (empty($_FILES['upload_product'])) {
      $error = 'Failed to upload file due to some server settings (such as php.ini max_upload_filesize). Please contact to site administrator for this problem.';
      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error);
      return;
    }

    // Prevent evil files from being uploaded
    $disallowedExtensions = array('php');
    if (in_array(end(explode(".", $_FILES['upload_product']['name'])), $disallowedExtensions)) {
      $error = 'File type or extension forbidden.';
      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error);
      return;
    }

    $info = $_FILES['upload_product'];
    $targetFile = $path . '/' . $info['name'];
    $vals = array();



    $uploadedSize = ($currentUploadStats['size'] + $info['size']) / 1024;
    if ($uploadType == 'main') {
      ;
      // VALIDATE ALLOWING FILE SIZE LIMIT
      if ($uploadedSize > $uploadLimit['filesize_main']) {
         $error = $this->view->translate("Files uploading size is %s, you have used %s files uploading size and after uploading this file you have %s size, which exceeds the uploading size that's why you will not permitted for uploading this file.(In KB)", round($uploadLimit['filesize_main'],2), round($currentUploadStats['size'] / 1024,2), round($uploadedSize,2));
        $error = Zend_Registry::get('Zend_Translate')->_($error);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }
    } else {
      if ($uploadedSize > $uploadLimit['filesize_sample']) {
         $error = $this->view->translate("Files uploading size is %s, you have used %s files uploading size and after uploading this file you have %s size, which exceeds the uploading size that's why you will not permitted for uploading this file.(In KB)", round($uploadLimit['filesize_sample'],2), round($currentUploadStats['size'] / 1024,2), round($uploadedSize,2));
        $error = Zend_Registry::get('Zend_Translate')->_($error);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }
    }


    if (file_exists($targetFile)) {
      $file_id = $downloadableFileTable->getFileId(array('type' => $uploadType, 'product_id' => $product_id, 'filename' => $info['name']));
      $deleteUrl = $this->view->url(array('action' => 'delete-file', 'product_id'=>$product_id, 'downloadablefile_id' => $file_id, 'type' => $uploadType), 'sitestoreproduct_files', true);
      $deleteUrlLink = '<a href="javascript:void(0);" onclick="Smoothbox.open(\'' . $this->view->escape($deleteUrl) . '\');return false;" >' . Zend_Registry::get('Zend_Translate')->_("delete") . '</a>';
      $error = $this->view->translate("File already exists. Please %s before trying to upload.", $deleteUrlLink);
      $error = Zend_Registry::get('Zend_Translate')->_($error);
      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error);
      return;
    }
    // END FILE VALIDATIONS
    
    // START FILE UPLOADING
    if (!file_exists($this->_filePath)) {
      $mask = @umask(0000);
      $directoryPath = $this->_filePath;
      $submaindirectoryPath = $this->_filePath . '/main';
      $subsampledirectoryPath = $this->_filePath . '/sample';
      @mkdir($directoryPath, 0777, true);
      @mkdir($submaindirectoryPath, 0777, true);
      @mkdir($subsampledirectoryPath, 0777, true);
      @umask($mask);
    }

    if (!is_writable($path)) {
      $error = Zend_Registry::get('Zend_Translate')->_('Path is not writeable. Please give CHMOD 0777 permission to the respective directory or contact to site administrator.');
      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error);
      return;
    }

    if (!move_uploaded_file($info['tmp_name'], $targetFile)) {
      $error = Zend_Registry::get('Zend_Translate')->_("Unable to move file to upload directory.");
      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error);
      return;
    }
    // END FILE UPLOADING

    // START DATABASE INSERTION
    $values['product_id'] = $product_id;
    $values['title'] = $formValues['title'];
    $values['download_limit'] = isset($formValues['download_limit']) ? $formValues['download_limit'] : 0;
//    $values['status'] = !empty($formValues['status']) ? 1 : 0;
    $values['type'] = $uploadType;
    $values['owner_id'] = $viewer->getIdentity();
    $values['owner_type'] = Engine_Api::_()->getItem('authorization_level', $viewer->level_id)->type;
    $values['filename'] = $info['name'];
    $values['extension'] = strtolower(ltrim(strrchr($info['name'], '.'), '.'));
    $values['size'] = $info['size'];

    if (!empty($info['size'])) {
      $mimeType = explode("/", $info['type']);
      $values['mime_major'] = $mimeType[0];
      $values['mime_minor'] = $mimeType[1];
    }

    $downloadableFileTable = Engine_Api::_()->getDbtable('downloadablefiles', 'sitestoreproduct');
    $db = $downloadableFileTable->getAdapter();
    $db->beginTransaction();

    try {
      $row = $downloadableFileTable->createRow();
      $row->setFromArray($values);
      $row->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    if( $uploadType == 'main' )
    {
      $action = 'index';
    }
    else if( $uploadType == 'sample' )
    {
      $action = 'sample';
    }
    return $this->_helper->redirector->gotoRoute(array('action' => $action, 'product_id' => $product_id), 'sitestoreproduct_files', true);
  }

  // ENABLE AND DISABLE FILE
  public function fileEnableAction() {
    $downloadablefile_id = $this->_getParam('downloadablefile_id', null);

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $downloadablefile = Engine_Api::_()->getItem('sitestoreproduct_downloadablefile', $downloadablefile_id);
      // CHANGING STATUS TO COMPLEMENT OF PRESENT STATUS VALUE
      $downloadablefile->status = !$downloadablefile->status;
      $downloadablefile->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->view->activeFlag = $downloadablefile->status;
  }

  //ACTION FOR MULTI DELETE WISHLIST
  public function multiDeleteAction() {

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $deleteType = $this->_getParam('type', null);
    $path = $relPath = $this->_filePath . '/' . $deleteType;
    if ($uploadType != 'main') {
      $path = $relPath = $this->_filePath . '/' . $deleteType;
    }

    $values = $this->getRequest()->getPost();

    foreach ($values as $key => $value) {
      if ($key == 'delete_' . $value) {
        $downloadablefileItem = Engine_Api::_()->getItem('sitestoreproduct_downloadablefile', $value);
        $downloadablefile_name = $downloadablefileItem->filename;
        $filePath = $path . '/' . $downloadablefile_name;
        if (is_file($filePath)) {

          if (@unlink($filePath)) {
            $downloadablefileItem->delete();
          }
        }
      }
    }
    
    return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'product_id' => $this->_getParam('product_id', null)), 'sitestoreproduct_files', true);
  }
  
  public function editFileAction() {
    
    $uploadType = $this->_getParam('type', 'main');

    $this->view->form = $form = new Sitestoreproduct_Form_File_EditFile(array('type' => $uploadType));

    $downloadablefileItem = Engine_Api::_()->getItem('sitestoreproduct_downloadablefile', $this->_getParam('downloadablefile_id', null));

    $downloadablefileArray = $downloadablefileItem->toArray();
    $form->populate($downloadablefileArray);
    
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $downloadablefileItem->title = $values['title'];
      $downloadablefileItem->download_limit = $values['download_limit'];

      $downloadablefileItem->save();
      $db->commit();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('File edited successfully.'))
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }
  
  public function deleteFileAction() {

    $deleteType = $this->_getParam('type', null);
    $path = $relPath = $this->_filePath . '/' . $deleteType;
    if ($uploadType != 'main') {
      $path = $relPath = $this->_filePath . '/' . $deleteType;
    }
    
    $this->view->downloadablefile_id = $downloadablefile_id = $this->_getParam('downloadablefile_id', NULL);

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $downloadablefileItem = Engine_Api::_()->getItem('sitestoreproduct_downloadablefile', $downloadablefile_id);
        $downloadablefile_name = $downloadablefileItem->filename;
        $filePath = $path . '/' . $downloadablefile_name;
        if (is_file($filePath)) {

          if (@unlink($filePath)) {
            $downloadablefileItem->delete();
          }
        }
        
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('File deleted successfully.'))
      ));
    }
    
  }

  public function downloadAction() {
    $downloadType = $this->_getParam('type', null);
    // Get path
    $path = $relPath = $this->_filePath . '/' . $downloadType;
    if ($uploadType != 'main') {
      $path = $relPath = $this->_filePath . '/' . $downloadType;
    }
    
    $downloadablefileItem = Engine_Api::_()->getItem('sitestoreproduct_downloadablefile', $this->_getParam('downloadablefile_id', NULL));
    $downloadablefile_name = $downloadablefileItem->filename;
    
    $path = $path . '/' . $downloadablefile_name;

    if (file_exists($path) && is_file($path)) {
      
      $isGZIPEnabled = false;
      if (ob_get_level()) {
        $isGZIPEnabled = true;
        @ob_end_clean();
      }

      header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
      header("Content-Transfer-Encoding: Binary", true);
      header("Content-Type: application/force-download", true);
      header("Content-Type: application/octet-stream", true);
      header("Content-Type: application/download", true);
      header("Content-Description: File Transfer", true);
      
      if(empty($isGZIPEnabled)) {
        header("Content-Length: " . filesize($path), true);
        flush();
      }

      $fp = fopen($path, "r");
      while (!feof($fp)) {
        echo fread($fp, 65536);
        if(empty($isGZIPEnabled))
          flush();
      }
      fclose($fp);
    }

    exit();
  }

}