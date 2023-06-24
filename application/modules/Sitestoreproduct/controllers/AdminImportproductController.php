<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminImportproductController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminImportproductController extends Core_Controller_Action_Admin {

  //ACTION FOR SHOWING IMPORT INSTRUCTIONS
  public function indexAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_admin_main', array(), 'sitestoreproduct_admin_main_import');
    
  }
  
  //ACTION FOR IMPORTING DATA FROM CSV FILE
  public function importAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);
    $tempImportFlag = false;

    $this->_helper->layout->setLayout('admin-simple');

    //MAKE FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Import_Import();

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

      if ((!$fp) && empty($tempImportFlag)) {
        echo "$fname File opening error";
        exit;
      }
      
      if( empty($tempImportFlag) ) {
        return;
      }

      $formData = array();
      $formData = $form->getValues();

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

      $import_count = 0;
      foreach ($explode_array as $explode_data) {

        //GET SITESTOREPRODUCT DETAILS FROM DATA ARRAY
        $values = array();
        $values['title'] = trim($explode_data[0]);
        $values['category'] = trim($explode_data[2]);
        $values['sub_category'] = trim($explode_data[3]);
        $values['body'] = trim($explode_data[4]);
        $values['overview'] = trim($explode_data[5]);
        $values['tags'] = trim($explode_data[6]);
        $values['location'] = trim($explode_data[7]);
        

        //IF SITESTOREPRODUCT TITLE AND CATEGORY IS EMPTY THEN CONTINUE;
        if (empty($values['title']) || empty($values['category']) || empty($values['body'])) {
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

              //SAVE OTHER DATA IN engine4_sitestoreproduct_importfiles TABLE
              $importFile = Engine_Api::_()->getDbtable('importfiles', 'sitestoreproduct')->createRow();
              $importFile->filename = $_FILES['filename']['name'];
              $importFile->status = 'Pending';
              $importFile->first_import_id = $first_import_id;
              $importFile->last_import_id = $last_import_id;
              $importFile->current_import_id = $first_import_id;
              $importFile->first_product_id = 0;
              $importFile->last_product_id = 0;
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

      //CLOSE THE SMOOTHBOX
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRedirect' => $this->_helper->url->url(array('module' => 'sitestoreproduct', 'controller' => 'admin-importproduct', 'action' => 'manage')),
          'parentRedirectTime' => '15',
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('CSV file has been imported succesfully !'))
      ));
    }
  }

  //ACTION FOR IMPORTING DATA FROM CSV FILE
  public function dataImportAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');
    $import_current = $this->_getParam('import_current');

    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitestoreproduct');
    $finalIds = array();
    if (empty($importfile_id) && isset($_GET['multi_import']) && !empty($_GET['multi_import'])) {

      if (!empty($import_current)) {

        foreach ($_GET as $key => $value) {
          if ($key == 'd_' . $value) {
            $finalIds[] = (int) $value;
          }
        }

        $firstFinalId = 0;
        foreach ($finalIds as $value) {
          $firstFinalId = $value;
          break;
        }

        if (!empty($finalIds) && !empty($firstFinalId) && is_numeric($firstFinalId)) {
          $this->view->importfile_id = $importfile_id = $firstFinalId;
        }
      } else {

        $importfile_ids = array();
        foreach ($_GET as $key => $value) {
          if ($key == 'd_' . $value) {
            $importfile_ids[] = (int) $value;
          }
        }

        $selectPendingIds = $tableImportFile->select()->from($tableImportFile->info('name'), 'importfile_id')->where('status = ?', 'Pending');
        $pendingIds = $selectPendingIds->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        if (!empty($pendingIds) && !empty($importfile_ids)) {
          $finalIds = array_intersect($pendingIds, $importfile_ids);
        }

        $firstFinalId = 0;
        foreach ($finalIds as $value) {
          $firstFinalId = $value;
          break;
        }

        if (!empty($finalIds) && !empty($firstFinalId) && is_numeric($firstFinalId)) {
          $this->view->importfile_id = $importfile_id = $firstFinalId;
        }
      }
    }

    $session = new Zend_Session_Namespace();
    $session->importfile_id = $importfile_id;

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
    $importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running'));
    if (!empty($importFileStatusData) && empty($import_current)) {
      return;
    }

    //UPDATE THE STATUS
    $importFile->status = 'Running';
    $importFile->save();

    $first_import_id = $importFile->first_import_id;
    $last_import_id = $importFile->last_import_id;

    $import_current_id = $importFile->current_import_id;
    $return_import_current_id = $this->_getParam('import_current_id');
    if (!empty($return_import_current_id)) {
      $import_current_id = $this->_getParam('import_current_id');
    }

    //MAKE QUERY
    $tableImport = Engine_Api::_()->getDbtable('imports', 'sitestoreproduct');

    $sqlStr = "import_id BETWEEN " . "'" . $import_current_id . "'" . " AND " . "'" . $last_import_id . "'" . "";

    $select = $tableImport->select()
            ->from($tableImport->info('name'), array('import_id'))
            ->where($sqlStr);
    $importDatas = $select->query()->fetchAll();

    if (empty($importDatas)) {
      return;
    }

    //START CODE FOR CREATING THE CSVToSitestoreproductImport.log FILE
    if (!file_exists(APPLICATION_PATH . '/temporary/log/CSVToSitestoreproductImport.log')) {
      $log = new Zend_Log();
      try {
        $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/CSVToSitestoreproductImport.log'));
      } catch (Exception $e) {
        //CHECK DIRECTORY
        if (!@is_dir(APPLICATION_PATH . '/temporary/log') &&
                @mkdir(APPLICATION_PATH . '/temporary/log', 0777, true)) {
          $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/CSVToSitestoreproductImport.log'));
        } else {
          //Silence ...
          if (APPLICATION_ENV !== 'production') {
            $log->log($e->__toString(), Zend_Log::CRIT);
          } else {
            //MAKE SURE LOGGING DOESN'T CAUSE EXCEPTIONS
            $log->addWriter(new Zend_Log_Writer_Null());
          }
        }
      }
    }

    //GIVE WRITE PERMISSION TO LOG FILE IF EXIST
    if (file_exists(APPLICATION_PATH . '/temporary/log/CSVToSitestoreproductImport.log')) {
      @chmod(APPLICATION_PATH . '/temporary/log/CSVToSitestoreproductImport.log', 0777);
    }
    //END CODE FOR CREATING THE CSVToSitestoreproductImport.log FILE
    //GET SITESTOREPRODUCT TABLE
    $sitestoreproductTable = Engine_Api::_()->getItemTable('sitestoreproduct_product');

    $import_count = 0;

    //START THE IMPORT WORK
    foreach ($importDatas as $importData) {

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

      //GET SITESTOREPRODUCT DETAILS FROM DATA ARRAY
      $values = array();
      $values['title'] = $import->title;
      $sitestoreproduct_category = $import->category;
      $sitestoreproduct_subcategory = $import->sub_category;
      $values['body'] = $import->body;
      $values['overview'] = $import->overview;
      $values['location'] = $import->location;
      $sitestoreproduct_tags = $import->tags;
      $values['owner_id'] = $viewer->getIdentity();

      //IF SITESTOREPRODUCT TITLE AND DESCRIPTION IS EMPTY THEN CONTINUE;
      if (empty($values['title']) || empty($sitestoreproduct_category) || empty($values['body'])) {
        continue;
      }

      $db = $sitestoreproductTable->getAdapter();
      $db->beginTransaction();

      try {

        $sitestoreproduct = $sitestoreproductTable->createRow();
        $sitestoreproduct->setFromArray($values);
        $sitestoreproduct->approved = 1;
        $sitestoreproduct->approved_date = date('Y-m-d H:i:s');
        $sitestoreproduct->save();
        $product_id = $sitestoreproduct->product_id;

        $importFile->current_import_id = $import->import_id;
        $importFile->last_product_id = $product_id;
        $importFile->save();

        if (empty($import_count)) {
          $importFile->first_product_id = $product_id;
          $importFile->save();
        }
        $import_count++;

        //START CATEGORY WORK
        $sitestoreproductCategoryTable = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct');
        $sitestoreproductCategory = $sitestoreproductCategoryTable->fetchRow(array('category_name = ?' => $sitestoreproduct_category, 'cat_dependency = ?' => 0));
        if (!empty($sitestoreproductCategory)) {
          $sitestoreproduct->category_id = $sitestoreproductCategory->category_id;

          $sitestoreproductSubcategory = $sitestoreproductCategoryTable->fetchRow(array('category_name = ?' => $sitestoreproduct_subcategory, 'cat_dependency = ?' => $sitestoreproduct->category_id));

          if (!empty($sitestoreproductSubcategory)) {
            $sitestoreproduct->subcategory_id = $sitestoreproductSubcategory->category_id;
          }
        }

        if (empty($sitestoreproduct->category_id)) {
          $db->rollBack();
          continue;
        }
        //END CATEGORY WORK

        $sitestoreproduct->save();

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct');

        $row = $tableOtherinfo->getOtherinfo($sitestoreproduct->getIdentity());
        if (empty($row)) {
          $tableOtherinfo->insert(array(
              'product_id' => $sitestoreproduct->getIdentity(),
              'overview' => $values['overview']
          ));
        }

        //SAVE TAGS
        $tags = preg_split('/[#]+/', $sitestoreproduct_tags);
        $tags = array_filter(array_map("trim", $tags));
        $sitestoreproduct->tags()->addTagMaps($viewer, $tags);
        $sitestoreproduct->save();

        //SET PRIVACY
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $privacyMax = array_search('everyone', $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($sitestoreproduct, $role, "view", ($i <= $privacyMax));
          $auth->setAllowed($sitestoreproduct, $role, "view", ($i <= $privacyMax));
          $auth->setAllowed($sitestoreproduct, $role, "comment", ($i <= $privacyMax));
          $auth->setAllowed($sitestoreproduct, $role, "comment", ($i <= $privacyMax));
        }

        //Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //IF ALL SITESTOREPRODUCTS HAS BEEN IMPORTED THAN CHANGE THE STATUS
      if ($importFile->current_import_id == $importFile->last_import_id) {
        $importFile->status = 'Completed';
      }
      $importFile->save();

      //CREATE LOG ENTRY IN LOG FILE
      if (file_exists(APPLICATION_PATH . '/temporary/log/CSVToSitestoreproductImport.log')) {

        $stringData = '';
        if ($import_count == 1) {
          $stringData .= "\n\n----------------------------------------------------------------------------------------------------------------\n";
          $stringData .= $this->view->translate("Import History of '") . $importFile->filename . $this->view->translate("' with file id: ") . $importFile->importfile_id . $this->view->translate(", created on ") . $importFile->creation_date . $this->view->translate(" is given below.");
          $stringData .= "\n----------------------------------------------------------------------------------------------------------------\n\n";
        }

        $myFile = APPLICATION_PATH . '/temporary/log/CSVToSitestoreproductImport.log';
        $fh = fopen($myFile, 'a') or die("can't open file");
        $current_time = date('D, d M Y H:i:s T');
        $product_id = $sitestoreproduct->product_id;
        $sitestoreproduct_title = $sitestoreproduct->title;
        $stringData .= $this->view->translate("Successfully created a new product at ") . $current_time . $this->view->translate(". ID and title of that List are ") . $product_id . $this->view->translate(" and '") . $sitestoreproduct_title . $this->view->translate("' respectively.") . "\n\n";
        fwrite($fh, $stringData);
        fclose($fh);
      }

      if ($import_count >= 100) {

        if (!empty($finalIds)) {
          $queryString = '';
          foreach ($finalIds as $key => $value) {
            $queryString .= "d_$value=$value&";
          }
          $queryString = rtrim($queryString, '&');

          $import_current_id = $importFile->current_import_id + 1;
          $this->_redirect("admin/sitestoreproduct/importproduct/data-import?multi_import=1&import_current_id=$import_current_id&import_current=1&$queryString");
        } else {
          $import_current_id = $importFile->current_import_id + 1;
          $this->_redirect("admin/sitestoreproduct/importproduct/data-import?importfile_id=$importfile_id&import_current_id=$import_current_id&import_current=1");
        }
      } elseif (!empty($finalIds) && $importFile->status == 'Completed') {

        foreach ($finalIds as $key => $value) {
          if ($value == $importfile_id) {
            unset($finalIds[$key]);
          }
        }

        $queryString = '';
        foreach ($finalIds as $key => $value) {
          $queryString .= "d_$value=$value&";
        }
        $queryString = rtrim($queryString, '&');

        if (!empty($finalIds)) {
          $this->_redirect("admin/sitestoreproduct/importproduct/data-import?multi_import=1&$queryString");
        }
      }
    }

    return $this->_helper->redirector->gotoRoute(array('module' => 'sitestoreproduct', 'controller' => 'importproduct', 'action' => 'manage'), "admin_default", true);
  }

  //ACTION FOR MANAGING THE CSV FILES DATAS
  public function manageAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_admin_main', array(), 'sitestoreproduct_admin_main_import');

    //FORM CREATION FOR SORTING
    $this->view->formFilter = $formFilter = new Sitestoreproduct_Form_Admin_Import_Filter();
    $sitestoreproduct = $this->_getParam('page', 1);

    $tableImportFile = Engine_Api::_()->getDbTable('importfiles', 'sitestoreproduct');
    $select = $tableImportFile->select();

    //IF IMPORT IS IN RUNNING STATUS FOR SOME FILE THAN DONT SHOW THE START BUTTON FOR ALL
    $importFileStatusData = $tableImportFile->fetchRow(array('status = ?' => 'Running'));
    $this->view->runningSomeImport = 0;
    if (!empty($importFileStatusData)) {
      $this->view->runningSomeImport = 1;
    }

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

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

    $select->order((!empty($values['order']) ? $values['order'] : 'importfile_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->total_slideshows = $paginator->getTotalItemCount();
    $this->view->paginator->setItemCountPerPage(200);
    $this->view->paginator = $paginator->setCurrentPageNumber($sitestoreproduct);
  }

  //ACTION FOR STOP IMPORTING DATA
  public function stopAction() {

    //UPDATE THE STATUS TO STOP
    $session = new Zend_Session_Namespace();
    $importfile_id = $session->importfile_id;

    if (empty($importfile_id)) {
      $importfile_id = $this->_getParam('importfile_id');
    }

    if (empty($importfile_id)) {
      return;
    }

    $importFile = Engine_Api::_()->getItem('sitestoreproduct_importfile', $importfile_id);
    $importFile->status = 'Stopped';
    $importFile->save();

    //UNSET THE SESSION VARIABLE
    if (isset($session->importfile_id)) {
      unset($session->importfile_id);
    }

    //REDIRECTING TO MANAGE SITESTOREPRODUCT IF FORCE STOP
    $forceStop = $this->_getParam('forceStop');
    if (!empty($forceStop)) {
      //return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
      $this->_redirect('admin/sitestoreproduct/importproduct/manage');
    }
  }

  //ACTION FOR ROLLBACK IMPORTING DATA
  public function rollbackAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->importfile_id = $importfile_id = $this->_getParam('importfile_id');

    //FETCH IMPORT FILE OBJECT
    $importFile = Engine_Api::_()->getItem('sitestoreproduct_importfile', $importfile_id);

    //IF STATUS IS PENDING THAN RETURN
    if ($importFile->status == 'Pending') {
      return;
    }

    $returend_current_product_id = $this->_getParam('current_product_id');

    $redirect = 0;
    if (isset($_GET['redirect'])) {
      $redirect = $_GET['redirect'];
    }

    if (empty($redirect) && isset($_POST['redirect'])) {
      $redirect = $_POST['redirect'];
    }

    //START ROLLBACK IF CONFIRM BY USER OR RETURNED CURRENT SITESTOREPRODUCT ID IS NOT EMPTY
    if (!empty($redirect)) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $first_product_id = $importFile->first_product_id;
        $last_product_id = $importFile->last_product_id;

        if (!empty($first_product_id) && !empty($last_product_id)) {
          $sitestoreproductTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');

          $current_product_id = $first_product_id;

          if (!empty($returend_current_product_id)) {
            $current_product_id = $returend_current_product_id;
          }

          //MAKE QUERY
          $sqlStr = "product_id BETWEEN " . "'" . $current_product_id . "'" . " AND " . "'" . $last_product_id . "'" . "";

          $select = $sitestoreproductTable->select()
                  ->from($sitestoreproductTable->info('name'), array('product_id'))
                  ->where($sqlStr);
          $sitestoreproductDatas = $select->query()->fetchAll();

          if (!empty($sitestoreproductDatas)) {
            $rollback_count = 0;
            foreach ($sitestoreproductDatas as $sitestoreproductData) {
              $product_id = $sitestoreproductData['product_id'];

              //DELETE SITESTOREPRODUCT
              $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
              $sitestoreproduct->delete();

              $db->commit();

              $rollback_count++;

              //REDIRECTING TO SAME ACTION AFTER EVERY 100 ROLLBACKS
              if ($rollback_count >= 100) {
                $current_product_id = $product_id + 1;
                $this->_redirect("admin/sitestoreproduct/importproduct/rollback?importfile_id=$importfile_id&current_product_id=$current_product_id&redirect=1");
              }
            }
          }
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
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
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Rollbacked successfully !'))
      ));
    }
    $this->renderScript('admin-importproduct/rollback.tpl');
  }

  //ACTION FOR DELETE IMPORT FILES AND IMPORT DATA
  public function deleteAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');
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
    $this->renderScript('admin-importproduct/delete.tpl');
  }

  //ACTION FOR DELETE SLIDESHOW AND THEIR BELONGINGS
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      //IF ADMIN CLICK ON DELETE SELECTED BUTTON

      foreach ($values as $key => $value) {
        if ($key == 'd_' . $value) {
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
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
  }

  //ACTION FOR DOWNLOADING THE CSV TEMPLATE FILE
  public function downloadAction() {

    $path = $this->_getPath();
    $file_path = "$path/example_product_import.csv";

    @chmod($path, 0777);
    @chmod($file_path, 0777);

    $file_string = "";
      $file_string = "Title|Category|Sub-Category|Description|Overview|Tag_String|Location";

    @chmod($path, 0777);
    @chmod($file_path, 0777);
    $fp = fopen(APPLICATION_PATH . '/temporary/example_product_import.csv', 'w+');
    fwrite($fp, $file_string);
    fclose($fp);

    //KILL ZEND'S OB
    while (ob_get_level() > 0) {
      ob_end_clean();
    }

    $path = APPLICATION_PATH . "/temporary/example_product_import.csv";
    header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
    header("Content-Transfer-Encoding: Binary", true);
    //header("Content-Type: application/x-tar", true);
    header("Content-Type: application/force-download", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Type: application/download", true);
    header("Content-Description: File Transfer", true);
    header("Content-Length: " . filesize($path), true);
    readfile("$path");

    exit();
  }

  protected function _getPath($key = 'path') {

    $basePath = realpath(APPLICATION_PATH . "/temporary");
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