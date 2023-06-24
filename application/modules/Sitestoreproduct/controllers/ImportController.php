<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitestoreproduct
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ImportController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_ImportController extends Seaocore_Controller_Action_Standard {

//ACTION FOR IMPORTING DATA FROM CSV FILE
  public function importAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
//INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);
    $this->_helper->layout->setLayout('default-simple');
    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    
    
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    
    $this->view->canEdit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    
     // Comment Privacy
    $this->view->isCommentsAllow = Engine_Api::_()->sitestore()->isCommentsAllow("sitestoreproduct_product");
    
// COUNT PRODUCT CREATED BY THIS STORE AND MAXIMUM PRODUCT CREATION LIMIT
    $this->view->current_count = $currentCount = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductsCountInStore($store_id);
    $this->view->quota = $quota = Engine_Api::_()->sitestoreproduct()->getProductLimit($store_id);

    $this->view->maxLimit = $maxLimit = empty($quota)? 0: ($quota - $currentCount);

    $this->view->shipping_method_exist = Engine_Api::_()->getDbtable('shippingmethods', 'sitestoreproduct')->isAnyShippingMethodExist($store_id);

//MAKE FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Import_Import();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
//MAKE SURE THAT FILE EXTENSION SHOULD NOT DIFFER FROM ALLOWED TYPE
      $ext = str_replace(".", "", strrchr($_FILES['filename']['name'], "."));
      if (!in_array($ext, array('csv', 'CSV'))) {
        $error = $this->view->translate("Invalid file extension. Only 'csv' extension is allowed.");
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }
//START READING DATA FROM CSV FILE
      $fname = $_FILES['filename']['tmp_name'];
      $fp = fopen($fname, "r");

      if (!$fp) {
        echo "$fname File opening error";
        exit;
      }

      $formData = array();
      $formData = $form->getValues();
      if (!empty($formData))
        $this->view->flag = 1;

      if ($formData['import_seperate'] == 1) {
        while ($buffer = fgets($fp, 4096)) {
          $explode_array[] = explode('|', $buffer);
        }
      } else {
        while ($buffer = fgets($fp, 4096)) {
          $explode_array[] = explode(',', $buffer);
        }
      }
//END READING DATA FROM CSV FILE
      $tempCount = 0;
      $import_count = 0;
      $errorArray = array();
      foreach ($explode_array as $explode_data) {

        $tempCount++;
//GET PRODUCT DETAILS FROM DATA ARRAY
        $values = array();
        $values['title'] = trim($explode_data[0]);
        $values['description'] = trim($explode_data[1]);
        $values['product_code'] = trim($explode_data[2]);
        $values['product_type'] = trim($explode_data[3]);
        $values['category'] = trim($explode_data[4]);
        $values['subcategory'] = trim($explode_data[5]);
        $values['subsubcategory'] = trim($explode_data[6]);
        $values['price'] = trim($explode_data[7]);
        $values['weight'] = trim($explode_data[8]);
        $values['in_stock'] = trim($explode_data[9]);
        $values['section'] = trim($explode_data[10]);
        
        // WORK FOR TRANSLATE IMPORT STARTS
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
      $languages = Engine_Api::_()->sitestoreproduct()->getLanguages();
        if (count($languages) > 1) {
            $localeMultiOptions = Engine_Api::_()->sitestoreproduct()->getLanguageArray();

            if (!empty($languages)) {
              $temp_index_counter = 0;
              foreach ($languages as $label) {

                if ($label == 'en')
                  continue;

                $lang_name = $label;
                if (isset($localeMultiOptions[$label])) {
                  $lang_name = $localeMultiOptions[$label];
                }

                $title_field = "title_$label";
                $body_field = "body_$label";
                $values[$title_field] = trim($explode_data[17 + $temp_index_counter]);
                $values[$body_field] = trim($explode_data[17 + $temp_index_counter + 1]);
                $temp_index_counter++;
              }
            }
          }
        // WORK FOR TRANSLATE IMPORT ENDS

        $tempValues['img_main'] = trim($explode_data[11]);
        $tempValues['img_1'] = trim($explode_data[12]);
        $tempValues['img_2'] = trim($explode_data[13]);
        $tempValues['img_3'] = trim($explode_data[14]);
        $tempValues['img_4'] = trim($explode_data[15]);
        $tempValues['img_5'] = trim($explode_data[16]);

        $values['img_name'] = @serialize($tempValues);

        $values['start_date'] = date('Y-m-d H:i:s');
        $values['store_id'] = $store_id;
        $values['approved'] = $formData['approved'];

        if ((!empty($maxLimit)) && ($import_count == $maxLimit)) {
          $errorArray[] = "Maximum limit exceeded";
          break;
        }

        $category_id = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoriesIdByName($values['category'], 0, 0);
        $tempErrorArray = Engine_Api::_()->sitestoreproduct()->importValidation($tempCount, $store_id, $values['product_type'], $category_id);

        if (!empty($tempErrorArray)) {
          $errorArray = array_merge($tempErrorArray, $errorArray);
          continue;
        }

        $db = Engine_Api::_()->getDbtable('imports', 'sitestoreproduct')->getAdapter();
        $db->beginTransaction();

        try {
          $import = Engine_Api::_()->getDbtable('imports', 'sitestoreproduct')->createRow();
          $import->setFromArray($values);
          $import->save();
//COMMIT
          $db->commit();
          if (empty($import_count)) {
            $first_import_id = $last_import_id = $import->import_id;
//SAVE DATA IN `engine4_sitestoreproduct_importfiles` TABLE
            $db = Engine_Api::_()->getDbtable('importfiles', 'sitestoreproduct')->getAdapter();
            $db->beginTransaction();

            try {
//FETCH PRIVACY
              if (empty($formData['auth_view'])) {
                $formData['auth_view'] = "everyone";
              }
              if (empty($formData['auth_comment'])) {
                $formData['auth_comment'] = "everyone";
              }
//SAVE OTHER DATA IN engine4_sitestoreproduct_importfiles TABLE
              $importFile = Engine_Api::_()->getDbtable('importfiles', 'sitestoreproduct')->createRow();
              $importFile->filename = $_FILES['filename']['name'];
              $importFile->status = 'Pending';
              $importFile->first_import_id = $first_import_id;
              $importFile->last_import_id = $last_import_id;
              $importFile->current_import_id = $first_import_id;
              $importFile->first_product_id = 0;
              $importFile->last_product_id = 0;
              $importFile->view_privacy = $formData['auth_view'];
              $importFile->comment_privacy = $formData['auth_comment'];
              $importFile->store_id = $store_id;
              $importFile->save();

//COMMIT
              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }
          } else {

//UPDATE LAST IMPORT ID
            $last_import_id = $import->import_id;
            $importFile->last_import_id = $last_import_id;
            $importFile->save();
          }

          $import_count++;
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }
      $this->view->successfulEntries = $import_count;
      $errorCount = @count($errorArray);
      $this->view->errors = $errorCount;
      $this->view->errorArray = $errorArray;
      $this->view->errorCount = $errorCount;
      $lastInsertedRowObj = Engine_Api::_()->getDbtable('importfiles', 'sitestoreproduct')->lastInsertedRow($store_id);
      foreach ($lastInsertedRowObj as $row)
        $importId = $row->maximum;
      $this->view->importFileId = $importId;
      $importFile = Engine_Api::_()->getItem('sitestoreproduct_importfile', $importId);
      $first_import_id = $importFile->first_import_id;
      $last_import_id = $importFile->last_import_id;
      if (!empty($first_import_id) && !empty($last_import_id))
        $this->view->importedObject = $latestImportedFilesObj = Engine_Api::_()->getDbtable('imports', 'sitestoreproduct')->latestImportedFiles($first_import_id, $last_import_id, $store_id);
    }
  }

  public function indexAction() {
    $this->view->store_id = $store_id = $this->_getParam('store_id');
    $tempFlag = $this->_getParam('tempFlag',null);
    if(!empty($tempFlag)){
        $importfile_id = $this->_getParam('importFileId');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
      //IMPORT FILE OBJECT
        $importFile = Engine_Api::_()->getItem('sitestoreproduct_importfile', $importfile_id);

        if (!empty($importFile)) {

          $first_import_id = $importFile->first_import_id;
          $last_import_id = $importFile->last_import_id;

      //MAKE QUERY FOR FETCH THE DATA
          $tableImport = Engine_Api::_()->getDbtable('imports', 'sitestoreproduct');

          $sqlStr = "import_id BETWEEN " . "'" . $first_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

          $select = $tableImport->select()
                  ->from($tableImport->info('name'), array('import_id'))
                  ->where($sqlStr);
          $importDatas = $select->query()->fetchAll();

          if (!empty($importDatas)) {
            foreach ($importDatas as $importData) {
              $import_id = $importData['import_id'];
//DELETE IMPORT DATA BELONG TO IMPORT FILE
              $tableImport->delete(array('import_id = ?' => $import_id));
            }
          }

//FINALLY DELETE IMPORT FILE DATA
          Engine_Api::_()->getDbtable('importfiles', 'sitestoreproduct')->delete(array('importfile_id = ?' => $importfile_id));
        }

        $db->commit();
      
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
     }
    
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    
    $this->view->canEdit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    
    if( !Engine_Api::_()->sitestore()->hasPackageEnable() && !Engine_Api::_()->sitestoreproduct()->getLevelSettings("allow_store_create") ) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    
    
    
    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitestoreproduct');
    $select = $tableImportFile->select();
    $select->where('store_id =?', $store_id);
    $importTableObj = $tableImportFile->fetchAll($select);
    $this->view->rowCount = @COUNT($importTableObj);
  }

//ACTION FOR MANAGING THE CSV FILES DATAS
  public function manageAction() {

    $this->view->store_id = $store_id = $this->_getParam('store_id');
    
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    
    $this->view->canEdit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

    $this->view->current_count = $currentCount = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductsCountInStore($store_id);
    $this->view->quota = $quota = Engine_Api::_()->sitestoreproduct()->getProductLimit($store_id);
    $this->view->maxLimit = $maxLimit = empty($quota)? 0: ($quota - $currentCount);

    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitestoreproduct');
    $select = $tableImportFile->select();

//IF IMPORT IS IN RUNNING STATUS FOR SOME FILE THAN DONT SHOW THE START BUTTON FOR ALL
    $importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running', 'store_id =?' => $store_id));
    $this->view->runningSomeImport = 0;
    if (!empty($importFileStatusData)) {
      $this->view->runningSomeImport = 1;
    }

    $values = array();
    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }
    $values = array_merge(array(
        'order' => 'importfile_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);
    $select->where('store_id =?', $store_id);

    $select->order((!empty($values['order']) ? $values['order'] : 'importfile_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->total_slideshows = $paginator->getTotalItemCount();
    $this->view->paginator->setItemCountPerPage(100);
  }

//ACTION FOR IMPORTING DATA FROM CSV FILE
  public function dataImportAction() {
    $store_id = $this->_getParam('store_id');
//INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->view->current_count = $currentCount = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductsCountInStore($store_id);
    $this->view->quota = $quota = Engine_Api::_()->sitestoreproduct()->getProductLimit($store_id);
    $this->view->maxLimit = $maxLimit = empty($quota)? 0: ($quota - $currentCount);

    $this->_helper->layout->setLayout('default-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');
    $current_import = $this->_getParam('current_import');
//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

//RETURN IF importfile_id IS EMPTY
    if (empty($importfile_id)) {
      return;
    }

//GET IMPORT FILE OBJECT
    $importFile = Engine_Api::_()->getItem('sitestoreproduct_importfile', $importfile_id);
    if (empty($importFile)) {
      return;
    }
//CHECK IF IMPORT WORK IS ALREADY IN RUNNING STATUS FOR SOME FILE
    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitestoreproduct');
    $importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running', 'store_id =?' => $store_id));
    if (!empty($importFileStatusData) && empty($current_import)) {
      return;
    }
//UPDATE THE STATUS
    $importFile->status = 'Running';
    $importFile->save();

    $first_import_id = $importFile->first_import_id;
    $last_import_id = $importFile->last_import_id;

    $current_import_id = $importFile->current_import_id;
    $return_current_import_id = $this->_getParam('current_import_id');
    if (!empty($return_current_import_id)) {
      $current_import_id = $this->_getParam('current_import_id');
    }


//MAKE QUERY
    $tableImport = Engine_Api::_()->getDbtable('imports', 'sitestoreproduct');

    $sqlStr = "import_id BETWEEN " . "'" . $current_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

    $select = $tableImport->select()
            ->from($tableImport->info('name'), array('import_id'))
            ->where($sqlStr);
    $importDatas = $select->query()->fetchAll();

    if (empty($importDatas)) {
      return;
    }
//START COLLECTING COMMON DATAS
    $table = Engine_Api::_()->getItemTable('sitestoreproduct_product');
//END COLLECTING COMMON DATAS
    $import_count = 0;
//START THE IMPORT WORK
    foreach ($importDatas as $importData) {
      if ((!empty($maxLimit)) && ($import_count == $maxLimit)) {
        break;
      }

//GET IMPORT FILE OBJECT
      $importFile = Engine_Api::_()->getItem('sitestoreproduct_importfile', $importfile_id);

//BREAK IF STATUS IS STOP
      if ($importFile->status == 'Stopped') {
        break;
      }
      $import_id = $importData['import_id'];
      if (empty($import_id)) {
        continue;
      }
      $import = Engine_Api::_()->getItem('sitestoreproduct_import', $import_id);
      if (empty($import)) {
        continue;
      }
//GET PRODUCT DETAILS FROM DATA ARRAY
      $values = array();
      $values['title'] = $import->title;
      $category_name = $import->category;
      $subcategory_name = $import->subcategory;
      $subsubcategory_name = $import->subsubcategory;

//      START WORK FOR CATEGORY 
      $category_id = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoriesIdByName($category_name, 0, 0);

      if (!empty($subcategory_name)) {
        $subCategory_id = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoriesIdByName($subcategory_name, $category_id, 0);
        $values['subcategory_id'] = $subCategory_id;


        if (!empty($subsubcategory_name) && !empty($subCategory_id)) {
          $subsubCategory_id = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoriesIdByName($subsubcategory_name, $subCategory_id, $subCategory_id);
          $values['subsubcategory_id'] = $subsubCategory_id;
        }
      }
      $values['category_id'] = $category_id;
//      END WORK FOR CATEGORY

      $values['body'] = $import->description;
      $values['start_date'] = $import->start_date;
      $values['approved'] = $import->approved;
      $values['price'] = $import->price;
      $values['product_code'] = $import->product_code;
      $values['product_type'] = $import->product_type;
      $values['weight'] = $import->weight;
      $values['in_stock'] = $import->in_stock;
      $values['owner_id'] = Engine_Api::_()->getItem('sitestore_store', $store_id)->owner_id;
      $values['store_id'] = $store_id;
      $values['draft'] = $import->approved;
      
      //WORK FOR TRANSLATE IN IMPORT STARTS
      $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
      $languages = Engine_Api::_()->sitestoreproduct()->getLanguages();
        if (count($languages) > 1) {
          $localeMultiOptions = Engine_Api::_()->sitestoreproduct()->getLanguageArray();

          if (!empty($languages)) {
            foreach ($languages as $label) {

              if ($label == 'en')
                continue;

              $lang_name = $label;
              if (isset($localeMultiOptions[$label])) {
                $lang_name = $localeMultiOptions[$label];
              }

              $title_field = "title_$label";
              $body_field = "body_$label";
              $overview_field = "overview_$label";

              $values[$title_field] = $import->$title_field;
              $values[$body_field] = $import->$body_field;
            }
          }
        }
      
      //WORK FOR TRANSLATE IN IMPORT ENDS

      $tempValues = @unserialize($import->img_name);
      $mainImage = $tempValues['img_main'];
      $albumImageArray = array($tempValues['img_1'], $tempValues['img_2'], $tempValues['img_3'], $tempValues['img_4'], $tempValues['img_5']);
//IF PRODUCT TITLE AND CATEGORY IS EMPTY THEN CONTINUE;
//      if (empty($values['title'])) {
//        continue;
//      }
//      $productRow = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->fetchRow(array('product_code LIKE ?' => $values['product_code']))->product_code;
//      if (!empty($productRow) || !@preg_match('/^[a-zA-Z0-9-_]+$/', $values['product_code'])) {
//        continue;
//      }
      
      if(!empty($import->product_id))
        continue;

      $db = $table->getAdapter();
      $db->beginTransaction();

      try {
        $sitestoreproduct = $table->createRow();
        $sitestoreproduct->setFromArray($values);
        $sitestoreproduct->approved = 1;
        $sitestoreproduct->approved_date = date('Y-m-d H:i:s');
        $sitestoreproduct->view_count = 0;

//     START WORK OF SECTION   
        if (!empty($import->section)) {
          $isSectionExitObj = Engine_Api::_()->getDbtable('sections', 'sitestoreproduct')->isSectionExist($import->section, $store_id);
          foreach ($isSectionExitObj as $section)
            $section_id = $section->section_id;
          if (!empty($section_id))
            $sitestoreproduct->section_id = $section_id;
          else {
            $tableSections = Engine_Api::_()->getDbTable('sections', 'sitestoreproduct');
            $tableSectionsName = $tableSections->info('name');
            $row_info = $tableSections->fetchRow($tableSections->select()->from($tableSectionsName, 'max(sec_order) AS sec_order'));
            $sec_order = $row_info['sec_order'] + 1;
            $row = $tableSections->createRow();
            $row->section_name = $import->section;
            $row->sec_order = $sec_order;
            $row->store_id = $store_id;
            $newsec_id = $row->save();
            $sitestoreproduct->section_id = $newsec_id;
          }
        } else {
          $sitestoreproduct->section_id = 0;
        }
        
        
        
        
        $sitestoreproduct->save();
        $product_id = $sitestoreproduct->product_id;
        Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->insert(array(
            'product_id' => $product_id));

//     END WORK OF SECTION  
        if ($sitestoreproduct->product_type == 'configurable' || $sitestoreproduct->product_type == 'virtual') {
          $sitestoreform = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options')->createRow();
          $sitestoreform->label = $values['title'];
          $sitestoreform->field_id = 1;
          $option_id = $sitestoreform->save();
          $optionids = Engine_Api::_()->getDbtable('productfields', 'sitestoreproduct')->createRow();
          $optionids->option_id = $option_id;
          $optionids->product_id = $sitestoreproduct->product_id;
          $optionids->save();
        }

        $import->product_id= $sitestoreproduct->product_id;
        $import->save();
        
//START PRODUCT IMAGE IMPORTING WORK     
         if (strpos($mainImage,'?') !== false){
          $explodedMainImage = explode("?", $mainImage);
          $mainImage = $explodedMainImage[0];
        }
        if (!empty($mainImage) && !strstr($mainImage, 'http') && !strstr($mainImage, 'https') && strstr($mainImage, 'public/')) {
          $return_url = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
          $mainImage = $return_url . $_SERVER['HTTP_HOST'] . $mainImage;
        }

        if (!empty($mainImage) && ((strstr($mainImage, 'http') || strstr($mainImage, 'https')))) {
          $sitestoreproduct->setImportPhoto($mainImage, 1);
          $albumTable = Engine_Api::_()->getDbtable('albums', 'sitestoreproduct');
          $album_id = $albumTable->update(array('photo_id' => $sitestoreproduct->photo_id), array('product_id = ?' => $sitestoreproduct->product_id));
        }
//END PROFILE IMAGE IMPORTING IMPORTING WORK
// START ALBUM IMAGE WORK
        if (!empty($albumImageArray)) {
          foreach ($albumImageArray as $albumImage) {
               if (strpos($albumImage,'?') !== false){
          $explodedAlbumImage = explode("?", $albumImage);
           $albumImage = $explodedAlbumImage[0];
           }
            if (!empty($albumImage) && !strstr($albumImage, 'http') && !strstr($albumImage, 'https') && strstr($albumImage, 'public/')) {
              $return_url = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://";
              $albumImage = $return_url . $_SERVER['HTTP_HOST'] . $albumImage;
            }
            if (!empty($albumImage) && (strstr($albumImage, 'http') || strstr($albumImage, 'https')))
              $sitestoreproduct->setImportPhoto($albumImage, 0);
          }
        }
// END ALBUM IMAGE IMPORTING WORK
        $importFile->current_import_id = $import->import_id;
        $importFile->last_product_id = $product_id;
        $importFile->save();

        if (empty($importFile->first_product_id)) {
          $importFile->first_product_id = $product_id;
          $importFile->save();
        }
        $import_count++;

        $auth = Engine_Api::_()->authorization()->context;
        $siteStoreProductEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct');
        if (!empty($siteStoreProductEnabled)) {
          $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }

        if (empty($importFile->view_privacy)) {
          $importFile->view_privacy = "everyone";
        }

        if (empty($importFile->comment_privacy)) {
          $importFile->comment_privacy = "everyone";
        }

        $viewMax = array_search($importFile->view_privacy, $roles);
        $commentMax = array_search($importFile->comment_privacy, $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($sitestoreproduct, $role, 'view', ($i <= $viewMax));
          $auth->setAllowed($sitestoreproduct, $role, 'comment', ($i <= $commentMax));
        }

        $siteStoreProductEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct');
        if (!empty($siteStoreProductEnabled)) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
        }
//Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
//IF ALL PRODUCTS HAS BEEN IMPORTED THAN CHANGE THE STATUS
      if ($importFile->current_import_id == $importFile->last_import_id) {
        $importFile->status = 'Completed';
        $tempFlagStatus = true;
      }
      $importFile->save();
      if (empty($tempFlagStatus) && $import_count >= 100) {
        $current_import_id = $importFile->current_import_id + 1;
        $this->_redirect("sitestoreproduct/import/data-import?current_import_id=$current_import_id&importfile_id=$importfile_id&current_import=1&store_id=$store_id");
      }
    }
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Importing is done successfully !'))
    ));
  }

//ACTION FOR STOP IMPORTING DATA
  public function stopAction() {

//UPDATE THE STATUS TO STOP
    $importfile_id = $this->_getParam('importfile_id');
    $store_id = $this->_getParam('store_id');
    $importFile = Engine_Api::_()->getItem('sitestoreproduct_importfile', $importfile_id);
    $importFile->status = 'Stopped';
    $importFile->save();
//REDIRECTING TO MANAGE PRODUCT IF FORCE STOP
    return $this->_helper->redirector->gotoRoute(array('action' => 'store', 'store_id' => $store_id, 'type' => 'import', 'menuId' => 89, 'method' => 'manage'), 'sitestore_store_dashboard', false);
  }

//ACTION FOR ROLLBACK IMPORTING DATA
  public function rollbackAction() {

//INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('default-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

//FETCH IMPORT FILE OBJECT
    $importFile = Engine_Api::_()->getItem('sitestoreproduct_importfile', $importfile_id);
    


//IF STATUS IS PENDING THAN RETURN
    if ($importFile->status == 'Pending') {
      return;
    }

    $returned_current_product_id = $this->_getParam('current_product_id');

    $redirect = 0;
    if (isset($_GET['redirect'])) {
      $redirect = $_GET['redirect'];
    }

    if (empty($redirect) && isset($_POST['redirect'])) {
      $redirect = $_POST['redirect'];
    }

//START ROLLBACK IF CONFIRM BY USER OR RETURNED CURRENT PRODUCT ID IS NOT EMPTY
    if (!empty($redirect)) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $first_product_id = $importFile->first_product_id;
        $last_product_id = $importFile->last_product_id;

        if (!empty($first_product_id) && !empty($last_product_id)) {
          $tableProduct = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
          $otherInfo = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct');
          $current_product_id = $first_product_id;

          if (!empty($returned_current_product_id)) {
            $current_product_id = $returned_current_product_id;
          }

//MAKE QUERY
          $sqlStr = "product_id BETWEEN " . "'" . $current_product_id . "'" . " AND " . "'" . $last_product_id . "'" . "";

          $select = $tableProduct->select()
                  ->from($tableProduct->info('name'), array('product_id'))
                  ->where($sqlStr);
          $productDatas = $select->query()->fetchAll();

          if (!empty($productDatas)) {
            $rollback_count = 0;
            foreach ($productDatas as $productData) {
              $product_id = $productData['product_id'];
//DELETE PRODUCT
              $tableProduct->delete(array('product_id = ?' => $product_id));
              $otherInfo->delete(array('product_id = ?' => $product_id));

              $db->commit();
              $rollback_count++;
//REDIRECTING TO SAME ACTION AFTER EVERY 100 ROLLBACKS
              if ($rollback_count >= 100) {
                $current_product_id = $product_id + 1;
                $this->_redirect("sitestoreproduct/import/rollback?current_product_id=$current_product_id&importfile_id=$importfile_id&redirect=1");
             
              }
            }
          }
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      
      for($tempCount=$importFile->first_import_id;$tempCount<=$importFile->last_import_id;$tempCount++)     {
        $import = Engine_Api::_()->getItem('sitestoreproduct_import', $tempCount);
        if(!empty ($import)){
        $import->product_id = 0;
        $import->save();
        }
      }

//UPDATE THE DATA IN engine4_sitestoreproduct_importfiles TABLE
      $importFile->status = 'Pending';
      $importFile->first_product_id = 0;
      $importFile->last_product_id = 0;
      $importFile->current_import_id = $importFile->first_import_id;
      $importFile->save();
      

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Rollbacked successfully !'))
      ));
    }
    $this->renderScript('import/rollback.tpl');
  }

//ACTION FOR DELETE IMPORT FILES AND IMPORT DATA
  public function deleteAction() {
    $this->_helper->layout->setLayout('default-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

//IF CONFIRM FOR DATA DELETION
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
//IMPORT FILE OBJECT
        $importFile = Engine_Api::_()->getItem('sitestoreproduct_importfile', $importfile_id);

        if (!empty($importFile)) {

          $first_import_id = $importFile->first_import_id;
          $last_import_id = $importFile->last_import_id;

//MAKE QUERY FOR FETCH THE DATA
          $tableImport = Engine_Api::_()->getDbtable('imports', 'sitestoreproduct');

          $sqlStr = "import_id BETWEEN " . "'" . $first_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

          $select = $tableImport->select()
                  ->from($tableImport->info('name'), array('import_id'))
                  ->where($sqlStr);
          $importDatas = $select->query()->fetchAll();

          if (!empty($importDatas)) {
            foreach ($importDatas as $importData) {
              $import_id = $importData['import_id'];
//DELETE IMPORT DATA BELONG TO IMPORT FILE
              $tableImport->delete(array('import_id = ?' => $import_id));
            }
          }

//FINALLY DELETE IMPORT FILE DATA
          Engine_Api::_()->getDbtable('importfiles', 'sitestoreproduct')->delete(array('importfile_id = ?' => $importfile_id));
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Import data has been deleted successfully !'))
      ));
    }
    $this->renderScript('import/delete.tpl');
  }

//ACTION FOR DELETE SLIDESHOW AND THEIR BELONGINGS
  public function multiDeleteAction() {
    $store_id = $this->_getParam('store_id');
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

//IF ADMIN CLICK ON DELETE SELECTED BUTTON
      if (!empty($values['delete'])) {
        foreach ($values as $key => $value) {
          if ($key == 'delete_' . $value) {
            $importfile_id = (int) $value;
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
//IMPORT FILE OBJECT
              $importFile = Engine_Api::_()->getItem('sitestoreproduct_importfile', $importfile_id);
              if (!empty($importFile)) {
                $first_import_id = $importFile->first_import_id;
                $last_import_id = $importFile->last_import_id;
//MAKE QUERY FOR FETCH THE DATA
                $tableImport = Engine_Api::_()->getDbtable('imports', 'sitestoreproduct');

                $sqlStr = "import_id BETWEEN " . "'" . $first_import_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

                $select = $tableImport->select()
                        ->from($tableImport->info('name'), array('import_id'))
                        ->where($sqlStr);
                $importDatas = $select->query()->fetchAll();

                if (!empty($importDatas)) {
                  foreach ($importDatas as $importData) {
                    $import_id = $importData['import_id'];

//DELETE IMPORT DATA BELONG TO IMPORT FILE
                    $tableImport->delete(array('import_id = ?' => $import_id));
                  }
                }
//FINALLY DELETE IMPORT FILE DATA
                Engine_Api::_()->getDbtable('importfiles', 'sitestoreproduct')->delete(array('importfile_id = ?' => $importfile_id));
              }
              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'store', 'store_id' => $store_id, 'type' => 'import', 'menuId' => 89, 'method' => 'manage'), 'sitestore_store_dashboard', false);
  }

//ACTION FOR DOWNLOADING THE CSV TEMPLATE FILE

  public function downloadAction() {
//GET PATH
    $basePath = realpath(APPLICATION_PATH . "/application/modules/Sitestoreproduct/settings");
     @chmod($basePath, 0777);
    $path = $this->_getPath();

    if (file_exists($path) && is_file($path)) {

      // WORK FOR TRANSLATE IMPORT STARTS
      @chmod($path, 0777);
      $fp = fopen($path, 'r+');// OPEN FILE IN READ MODE
      rewind($fp);// SET FILE POINTER TO THE BEGINING OF THE FILE
      
      $temp_language_path = $basePath . "/product_import_language_example.csv"; // PATH OF THE TEMPORARY FILE
      $fp_temp = fopen($temp_language_path, 'w+');// CREATE A TEMPORARY FILE IN WRITE MODE
      @chmod($temp_language_path, 0777);// SET FULL PERMISSIONS TO THE FILE
      
      $line_counter = 0;
      $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
      $languages = Engine_Api::_()->sitestoreproduct()->getLanguages();
      while ($line_counter < 4) {        
        $temp_string = rtrim(fgets($fp)); // REMOVE THE NEWLINE CHARACTER FROM THE FETCHED STRING AND A LINE FROM THE ORGINAL FILE

        if (count($languages) > 1) {
          $localeMultiOptions = Engine_Api::_()->sitestoreproduct()->getLanguageArray();

          if (!empty($languages)) {
            foreach ($languages as $label) {

              if ($label == 'en')
                continue;

              $lang_name = $label;
              if (isset($localeMultiOptions[$label])) {
                $lang_name = $localeMultiOptions[$label];
              }

              $title_field = "title_$label";
              $body_field = "body_$label";
              if($line_counter < 1)
                $temp_string .="|" . $title_field . "|" . $body_field . "\n";
              else{
                $temp_string .="|" . "Product title in ".$lang_name . "|" ."Product body in ".$lang_name. "\n";
              }
            }
          }
        }
        fwrite($fp_temp, $temp_string);// WRITE TO THE TEMPORARY FILE
        $line_counter++;
      }
      // WORK FOR TRANSLATE IMPORT ENDS
      fclose($fp);
      fclose($fp_temp);
      
      // zend's ob
      $isGZIPEnabled = false;
      if (ob_get_level()) {
        $isGZIPEnabled = true;
        @ob_end_clean();
      }
      
      //IF MULTILANGUAL FUNCTIONALITY ENABLED AS WELL AS USER IS HAVING MORE THAN ONE LANGUAGE PACKAGES REPLACE THE PATH OF DOWNLOAD FILE
      if (count($languages) > 1 && !empty($temp_language_path))
        $path = $temp_language_path;

      header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
      header("Content-Transfer-Encoding: Binary", true);
      header("Content-Type: application/x-tar", true);
      header("Content-Type: application/force-download", true);
      header("Content-Type: application/octet-stream", true);
      header("Content-Type: application/download", true);
      header("Content-Description: File Transfer", true);
      if (empty($isGZIPEnabled))
        header("Content-Length: " . filesize($path), true);

      readfile("$path");
      
      //DELETE THE TEMPORARY FILE CREATED FOR DOWNLOAD IF THE MULTILANGUAL SETTING IS ENABLED AND USER IS HAVING MORE THAN 1 LANGUAGE
      if (count($languages) > 1 && !empty($temp_language_path))
        unlink($temp_language_path);
    }
    exit();
  }

  protected function _getPath($key = 'path') {
    $basePath = realpath(APPLICATION_PATH . "/application/modules/Sitestoreproduct/settings");
    return $this->_checkPath($this->_getParam($key, ''), $basePath);
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