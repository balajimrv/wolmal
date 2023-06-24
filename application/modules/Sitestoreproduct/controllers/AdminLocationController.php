<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLocationController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminLocationController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE LOCATION
  public function indexAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_shippinglocation');

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->delete(array('country LIKE ?' => $value));
        }
      }
    }

    $page = $this->_getParam('page', 1);
    $this->view->paginator = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getRegionsPaginator(array(
        'orderby' => 'country',
            ));
    $this->view->paginator->setItemCountPerPage(500);
    $this->view->paginator->setCurrentPageNumber($page);
  }

  //ACTION FOR ADD LOCATION (IN SMOOTHBOX ON MANAGE LOCATION PAGE) 
  public function addLocationAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Location_AddLocation();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    $this->view->regions = array();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        //CHECK REGIONS
        $regions = (array) $this->_getParam('regionsArray');
        $regions = @array_filter(array_map('trim', $regions));
        $regions = @array_unique($regions);
        $regions = @array_slice($regions, 0, 100);
        $regionsArray = array();
        foreach ($regions as $region) {
          $regionsArray[] = '\'' . $region . '\'';
        }
        
        $regionStr = @implode(',', $regionsArray);
        $this->view->regions = $regions;
        
        if(!empty($values['all_regions'])) {
          $regionStr = '""';
          $regions = array("");
        }
        
        $isALLRegionAlreadyExist = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getEmptyRegionCount($values['country']);
        if( !empty($isALLRegionAlreadyExist) ){
          $form->addError($this->view->translate("ALL Regions / States already enabled for this country. If you want to create region then delete ALL region entry first."));
          return;
        }
        
        
        $dontSaveInDatabase = false;
        $params = array();
        $params['country'] = $values['country'];
        $params['region'] = $regionStr;
        if( !empty($regionStr) ) {
          $regionAlreadyExist = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->isRegionAlreadyExist($params);
          if (!empty($regionAlreadyExist) && $regionAlreadyExist != 1) {
            return $form->addError("Entered Regions $regionAlreadyExist already exist.");
          }else if(!empty($regionAlreadyExist) && $regionAlreadyExist == 1){
            $dontSaveInDatabase = true;
          }
        }        

        $regionsTable = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct');
        $saveValues = array();
        foreach ($regions as $region) {
          if( empty($dontSaveInDatabase) ) {
            $saveValues['country'] = $values['country'];
            $saveValues['region'] = $region;
            $saveValues['status'] = 1;
            $saveValues['country_status'] = 1;

            $row = $regionsTable->createRow();
            $row->setFromArray($saveValues);
            $row->save();
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
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Location added successfully.'))
      ));
    }

    $this->renderScript('admin-location/add-location.tpl');
  }

  public function manageRegionAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION SAME AS MANAGE LOCATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_shippinglocation');

    $this->view->country = $this->_getParam('country', null);

    //CHECK REQUEST AND PERFORM DELETE OPERATION (SELECT CHECK BOX FOR DELETE)
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      $regionCountFlag = 0;
      foreach ($values as $key => $value) {
        // DELETE REGIONS WHERE region_id = $value (WHERE CHECK BOX NAME IS delete_(region_id))
        if ($key == 'delete_' . $value) {
          $regionCountFlag++;
          $region = Engine_Api::_()->getItem('sitestoreproduct_region', $value);
          $region->delete();
        }
      }
      // IF ALL REGIONS OF A COUNTRY IS DELETED THEN REDIRECT TO LOCATION PAGE
      if ($values ['totalregions'] == $regionCountFlag) {
        $this->_redirect('admin/sitestoreproduct/settings/manage-countries');
      }
    }

    $page = $this->_getParam('page', 1);
    $country = $this->_getParam('country', '');
    $this->view->paginator = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getRegionsPaginator(array('orderby' => 'region',
        'country' => $country
            ));
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator->setCurrentPageNumber($page);
  }

  //ACTION FOR ADD LOCATION (IN SMOOTHBOX ON MANAGE LOCATION PAGE) 
  public function addRegionAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Location_AddRegion();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    $country = $this->_getParam('country');

    $form->populate(array('country' => Zend_Locale::getTranslation($country, 'country')));
    
    $this->view->regions = array();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        //CHECK REGIONS
        $regions = (array) $this->_getParam('regionsArray');
        $regions = array_filter(array_map('trim', $regions));
        $regions = @array_unique($regions);
        $regions = array_slice($regions, 0, 100);
        $regionsArray = array();
        foreach ($regions as $region) {
          $regionsArray[] = '\'' . $region . '\'';
        }

        $regionStr = implode(',', $regionsArray);
        $this->view->regions = $regions;
        if (empty($regions) || !is_array($regions) || count($regions) < 1) {
          $form->country->setValue(Zend_Locale::getTranslation($country, 'country'));
          return $form->addError('You must add at least one region.');
        }
        $params = array();
        $params['country'] = $country;
        $params['region'] = $regionStr;
        $regionAlreadyExist = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->isRegionAlreadyExist($params);
        if (!empty($regionAlreadyExist)) {
          $form->country->setValue(Zend_Locale::getTranslation($country, 'country'));
          return $form->addError("Entered Regions $regionAlreadyExist already exist.");
        }


        $regionsTable = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct');
        $saveValues = array();
        foreach ($regions as $region) {
          $saveValues['country'] = $this->_getParam('country');
          $saveValues['region'] = $region;
          $saveValues['status'] = 1;
          $saveValues['country_status'] = 1;

          $row = $regionsTable->createRow();
          $row->setFromArray($saveValues);
          $row->save();
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Region added successfully.'))
      ));
    
    }
    $this->renderScript('admin-location/add-region.tpl');
    
    
    
    
    
    

   

    

//    $values = $form->getValues();
//    $values['country'] = $this->_getParam('country');
//
//    // CHECK INSERTED REGIN/STATE ALREDY EXIST OR NOT.
//    $params['region'] = $values['region'];
//    $params['country_name'] = $this->_getParam('country');
//    $regionResult = $regionObj->getRegionsByName($params);
//    if (!empty($regionResult)) {
//      $form->country->setValue(Zend_Locale::getTranslation($this->_getParam('country'), 'country'));
//      $error_owner = $this->view->translate('Region / State already exist for this country.');
//      $form->getDecorator('errors')->setOption('escape', false);
//      $form->addError($error_owner);
//      return;
//    }
//
//    $db = $regionObj->getAdapter();
//    $db->beginTransaction();
//    try {
//      // CREATE REGION ROW
//      $row = $regionObj->createRow();
//      $row->setFromArray($values);
//      $row->save();
//
//      $db->commit();
//      $this->_forward('success', 'utility', 'core', array(
//          'smoothboxClose' => 10,
//          'parentRefresh' => 10,
//          'messages' => array('')
//      ));
//    } catch (Exception $e) {
//      $db->rollBack();
//      throw $e;
//    }
  }

  // ENABLE AND DISABLE REGION ON MANAGE REGION PAGE
  public function regionenableAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $region_id = $this->_getParam('regionId', null);

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $regions = Engine_Api::_()->getItem('sitestoreproduct_region', $region_id);
      // CHANGING STATUS TO COMPLEMENT OF PRESENT STATUS VALUE
      $regions->status = !$regions->status;
      $regions->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'manage-region', 'country' => $regions->country));
  }

  // ENABLE AND DISABLE REGION ON MANAGE COUNTRIES PAGE
  public function countryenableAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $country = $this->_getParam('country');
    $currentStatus = $this->_getParam('current_status', null);
    $newCounrtyStatus = !$currentStatus;

    $regionObj = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct');

    $db = $regionObj->getAdapter();
    $db->beginTransaction();

    try {
      // CREATE REGION ROW
      $regionObj->update(array('country_status' => $newCounrtyStatus), array('country LIKE ?' => $country));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function editLocationAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Location_EditLocation();

    $regionObj = Engine_Api::_()->getItem('sitestoreproduct_region', $this->_getParam('id', false));

    //POPULATE LOCATION WITH UNSET country VALUE
    $regionPopulateArray = $regionObj->toArray();

    $country = Zend_Locale::getTranslation($regionObj->country, 'country');

    $regionPopulateArray['country'] = $country;
    $form->populate($regionPopulateArray);

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValidPartial($this->getRequest()->getPost())) {
      return;
    }

    $regionValidatorObj = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct');

    $values = $form->getValues();
    // CHECK INSERTED REGIN/STATE ALREDY EXIST OR NOT.
    $params['region'] = $values['region'];
    $params['country_name'] = $regionObj->country;
    $regionResult = $regionValidatorObj->getRegionsByName($params);
    if (!empty($regionResult)) {
      $error_owner = $this->view->translate('Region / State already exist for this country.');
      $form->getDecorator('errors')->setOption('escape', false);
      $form->addError($error_owner);
      return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      //UPDATING REGION ROW
      $regionObj->region = $values['region'];
      $regionObj->save();

      $db->commit();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Location edited successfully.'))
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION DELETE LOCATION ON CLICKING THE DELETE LINK
  public function deleteLocationAction() {
    // IN SMOOTHBOX
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->region_id = $id;

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $region = Engine_Api::_()->getItem('sitestoreproduct_region', $id);
        $region->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('location deleted successfully.'))
      ));
    }
  }

  //ACTION DELETE LOCATION ON CLICKING THE DELETE LINK
//  public function deleteCountryAction() {
//    // IN SMOOTHBOX
//    $this->_helper->layout->setLayout('admin-simple');
//    $country = $this->_getParam('country', null);
//    $this->view->country = $country;
//
//    if ($this->getRequest()->isPost()) {
//      $regionObj = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct');
//
//      $db = $regionObj->getAdapter();
//      $db->beginTransaction();
//
//      try {
//        // CREATE REGION ROW
//        $regionObj->delete(array('country LIKE ?' => $country));
//
//        $db->commit();
//      } catch (Exception $e) {
//        $db->rollBack();
//        throw $e;
//      }
//
//      $this->_forward('success', 'utility', 'core', array(
//          'smoothboxClose' => 10,
//          'parentRefresh' => 10,
//          'messages' => array('')
//      ));
//    }
//  }
  
  
  
  //ACTION FOR IMPORTING DATA FROM CSV FILE
  public function importLocationFileAction() {

    //INCREASE THE MEMORY ALLOCATION SIZE AND INFINITE SET TIME OUT
    ini_set('memory_limit', '2048M');
    set_time_limit(0);

    $this->_helper->layout->setLayout('admin-simple');

    //MAKE FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Import();

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

			if($formData['import_seperate'] == 1) {
				while ($buffer = fgets($fp, 4096)) {
					$explode_array[] = explode('|', $buffer);
				}
			}
			else {
				while ($buffer = fgets($fp, 4096)) {
					$explode_array[] = explode(',', $buffer);
				}
			}
      //END READING DATA FROM CSV FILE

      $import_count = 0;
      $regionTable = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct');
      foreach ($explode_array as $explode_data) {

        //GET LOCATION DETAILS FROM DATA ARRAY
        $values = array();
        $values['country'] = trim($explode_data[0]);
        $values['region'] = trim($explode_data[1]);
        $values['status'] = trim($explode_data[2]);
        
        //IF COUNTRY OR REGION IS EMPTY THEN CONTINUE;
        if (empty($values['country']) || empty($values['region'])) {
          continue;
        }                
        
        //TAKING COUNTRIES OBJECT
        $locale = Zend_Registry::get('Zend_Translate')->getLocale();
        $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
        if( !array_key_exists($values['country'], $countries) ) {
          continue;
        }                
        
        $tempParams = $values;
        $tempParams['region'] = str_replace("'", "", $tempParams['region']);
        $regionAlreadyExist = $regionTable->isRegionAlreadyExist($tempParams, true);
        if (!empty($regionAlreadyExist)) {
          continue;
        }
        
        $values['country_status'] = $regionTable->getCountryStatus($values['country']);
                
        $db = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->getAdapter();
        $db->beginTransaction();

        try {
          $region = $regionTable->createRow();
          $region->setFromArray($values);
          $region->save();

          //COMMIT
          $db->commit();

          $import_count++;
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }

      //CLOSE THE SMOOTHBOX
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRedirect' => false,
          'format' => 'smoothbox',
					'messages' => array(Zend_Registry::get('Zend_Translate')->_('CSV file has been imported succesfully !'))
      ));
    }
  }
  
  public function importAction()
  {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_shippinglocation');
  }
  
  //ACTION FOR DOWNLOADING THE CSV TEMPLATE FILE
  public function downloadAction() {
    //GET PATH
    $basePath = realpath(APPLICATION_PATH . "/application/modules/Sitestoreproduct/settings");

    $path = $this->_getPath();

    if (file_exists($path) && is_file($path)) {
      //KILL ZEND'S OB
      $isGZIPEnabled = false;
      if (ob_get_level()) {
        $isGZIPEnabled = true;
//        while (ob_get_level() > 0) {
          @ob_end_clean();
//        }
      }

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
  
  public function viewCountriesCodeAction()
  {
    //TAKING COUNTRIES OBJECT
    $locale = Zend_Registry::get('Zend_Translate')->getLocale();
    $this->view->countriesCode = $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
  }
}