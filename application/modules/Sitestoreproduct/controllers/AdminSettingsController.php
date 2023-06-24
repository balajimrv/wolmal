<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminSettingsController extends Core_Controller_Action_Admin {

  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {    
     
    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
				->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_settings');    
    
    //GET NAVIGATION
    $this->view->navigationStoreGlobal = Engine_Api::_()->getApi('menus', 'core')
				->getNavigation('sitestore_admin_main_settings', array(), 'sitestore_admin_main_global_product');
    
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Settings_Global();
    
    $this->view->hasLanguageDirectoryPermissions = $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitestoreproduct')->hasDirectoryPermissions();

    if (!empty($_POST['sitestoreproduct_lsettings'])) {
      $_POST['sitestoreproduct_lsettings'] = trim($_POST['sitestoreproduct_lsettings']);
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');

    //GET THE PREVIOUS SETTINGS
    $previousTitleSin = $settings->getSetting('sitestoreproduct.titlesingular', 'Product');
    $previousTitlePlu = $settings->getSetting('sitestoreproduct.titleplural', 'Products');
    $previousSlugSin = $settings->getSetting('sitestoreproduct.slugsingular', 'product');
    $previousSlugPlu = $settings->getSetting('sitestoreproduct.slugsingular', 'products');
    $previousSitestoreproductBrands = $settings->getSetting('sitestoreproduct_brands', 1);

    $this->view->isModsSupport = Engine_Api::_()->sitestoreproduct()->isModulesSupport();

    // CHECK ANY ENABLE LOCATIONS
    $this->view->isAnyCountryEnable = Engine_Api::_()->getDbtable('regions', 'sitestoreproduct')->isAnyCountryEnable();

    // CHECK ENABLE PAYMENT GATEWAYS
    $this->view->isAnyGatewayEnable = $enable_gateway = Engine_Api::_()->getDbtable('gateways', 'payment')->select()->where('enabled = 1')->limit(1)->query()->fetchAll();

    if ($hasLanguageDirectoryPermissions && isset($_POST['sitestoreproduct_titlesingular']) && isset($_POST['sitestoreproduct_titleplural']) && !empty($_POST['sitestoreproduct_titlesingular']) && !empty($_POST['sitestoreproduct_titleplural']) && ($previousTitleSin != $_POST['sitestoreproduct_titlesingular'] || $previousTitlePlu != $_POST['sitestoreproduct_titleplural'])) {
      $language_pharse = array('text_products' => $_POST['sitestoreproduct_titleplural'], 'text_product' => $_POST['sitestoreproduct_titlesingular']);
      Engine_Api::_()->getApi('language', 'sitestoreproduct')->setTranslateForListType($language_pharse);
    }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.language.phrases'))
          $form->populate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.language.phrases'));
        if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
          $values = $form->getValues();
          
          $languages = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.languages');
      if (!empty($languages)) {
        for ($i = 0; $i < count($languages); $i++) {
          $language_key = 'sitestoreproduct.languages.' . $i;
          Engine_Api::_()->getApi('settings', 'core')->removeSetting($language_key);
        }
      }
      
      if (!empty($values['sitestoreproduct_multilanguage']) && empty($values['sitestoreproduct_languages']) ) {
        $error = $this->view->translate("Please select atleast one language for multilanguage functionality or select no in setting for multilanguage of products");
        $form->addError($error);
        return;
      }

      // CREATING COLUMNS ACCORDING TO LANGUAGES
      if (count($values['sitestoreproduct_languages']) > 1) {
        Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->createColumns($values['sitestoreproduct_languages']);
      }
      
      if( !empty($values['sitestoreproduct_contactdetail']) ){
                $values['temp_sitestoreproduct_contactdetail'] = @serialize($values['sitestoreproduct_contactdetail']);
            }

          if ($values['sitestoreproduct_slugsingular'] == $values['sitestoreproduct_slugplural']) {
            $error = $this->view->translate("Singular Slug and Plural Slug can't be same.");
            $error = Zend_Registry::get('Zend_Translate')->_($error);

            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
          }

          if (!empty($values['sitestoreproduct_translationfile'])) {
            $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitestoreproduct')->hasDirectoryPermissions();
            if (!$hasLanguageDirectoryPermissions) {
              $error = $this->view->translate("Language file for stores could not be overwritten. because you do not have write permission chmod -R 777 recursively to the directory '/application/languages/'. Please login in over your Cpanel or FTP and give the recursively write permission to this directory and try again.");
              $error = Zend_Registry::get('Zend_Translate')->_($error);
              $form->getDecorator('errors')->setOption('escape', false);
              $form->addError($error);
              return;
            }
          }
          
          // DISABLE ALL EXISTING TAXES IF VAT IS ENABLED
          if( isset($values['sitestoreproduct_vat']) && !empty($values['sitestoreproduct_vat'])) {
            $taxTable = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct');
            $enabledTaxes = $taxTable->getEnabledTaxes();
            if( !empty($enabledTaxes) ) {
              foreach($enabledTaxes as $tax_id) {
                $taxTable->update(array('status' => 0), array('tax_id  =?' => $tax_id));
              }
            }
          }

          if (isset($values['sitestoreproduct_currency']))
            unset($values['sitestoreproduct_currency']);
          
          foreach ($values as $key => $value) {
            if ($key != 'sitestoreproduct_sponsoredcolor' && $key != 'sitestoreproduct_featuredcolor' && $key != 'sitestoreproduct_currency')
              Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
          }
        }

    //GET THE NEW SETTINGS
    $newTitleSin = $settings->getSetting('sitestoreproduct.titlesingular', 'Product');
    $newTitlePlu = $settings->getSetting('sitestoreproduct.titleplural', 'Products');
    $newSlugSin = $settings->getSetting('sitestoreproduct.slugsingular', 'product');
    $newSlugPlu = $settings->getSetting('sitestoreproduct.slugsingular', 'products');

    $productTypeApi = Engine_Api::_()->getApi('productType', 'sitestoreproduct');

    if ($hasLanguageDirectoryPermissions && ($previousTitleSin != $newTitleSin || $previousTitlePlu != $newTitlePlu)) {

      $productTypeApi->widgetizedPagesEdit('home', $newTitleSin, $newTitlePlu);
      $productTypeApi->widgetizedPagesEdit('index', $newTitleSin, $newTitlePlu);
      $productTypeApi->widgetizedPagesEdit('view', $newTitleSin, $newTitlePlu);
      $productTypeApi->widgetizedPagesEdit('map', $newTitleSin, $newTitlePlu);

      $productTypeApi->mainNavigationEdit();
      $productTypeApi->gutterNavigationEdit();

      $productTypeApi->activityFeedQueryEdit($newTitleSin, $newTitlePlu);
      $productTypeApi->searchFormSettingEdit($newTitleSin, $newTitlePlu);

      //START FOR PAGE INRAGRATION.
      $sitestoreintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
      if (!empty($sitestoreintegrationEnabled)) {
        Engine_Api::_()->sitestoreintegration()->pageintergrationTitleEdit($newTitlePlu);
      }
      //END FOR PAGE INRAGRATION.
    }

    //BANNED PAGE URL WORK.
    if ($previousSlugSin != $newSlugSin || $previousSlugPlu != $newSlugPlu) {
      $productTypeApi->addBannedUrls();
    }

    if (isset($_POST['sitestoreproduct_brands']) && $previousSitestoreproductBrands != $_POST['sitestoreproduct_brands']) {
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      if (!empty($_POST['sitestoreproduct_brands'])) {
        $db->query("UPDATE `engine4_core_content` SET `params` = REPLACE(params, 'Tags', 'Brands') WHERE `name` = 'sitestoreproduct.tagcloud-sitestoreproduct' AND `params` Like '%Tags%'");
      } else {
        $db->query("UPDATE `engine4_core_content` SET `params` = REPLACE(params, 'Brands', 'Tags') WHERE `name` = 'sitestoreproduct.tagcloud-sitestoreproduct' AND `params` Like '%Brands%'");
      }
    }
    
    $oldLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.map.city', "World");
    $newLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.map.city', "World");
    
    $this->setDefaultMapCenterPoint($oldLocation, $newLocation); 
//    if(!empty($_POST['sitestoreproduct_combination']) && (isset($_POST['sitestoreproduct_check_combination_quantity']) && !empty($_POST['sitestoreproduct_check_combination_quantity']))){
//      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
//      $db->query("UPDATE `engine4_sitestoreproduct_products` SET `stock_unlimited` = 1 WHERE `product_type` = 'configurable' OR `product_type` = 'virtual'");
//    }
//    elseif(!empty($_POST['sitestoreproduct_combination']) && (isset($_POST['sitestoreproduct_check_combination_quantity']) && empty($_POST['sitestoreproduct_check_combination_quantity']))){
//      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
//      $db->query("UPDATE `engine4_sitestoreproduct_products` SET `stock_unlimited` = 0 WHERE (`product_type` = 'configurable' OR `product_type` = 'virtual') AND `in_stock` != 0");
//    }
  }

  // Added phrase in language file.
  public function addPhraseAction($phrase) {

    if ($phrase) {
      //file path name
      $targetFile = APPLICATION_PATH . '/application/languages/en/custom.csv';
      if (!file_exists($targetFile)) {
        //Sets access of file
        touch($targetFile);
        //changes permissions of the specified file.
        chmod($targetFile, 0777);
      }
      if (file_exists($targetFile)) {
        $writer = new Engine_Translate_Writer_Csv($targetFile);
        $writer->setTranslations($phrase);
        $writer->write();
        //clean the entire cached data manually
        @Zend_Registry::get('Zend_Cache')->clean();
      }
    }
  }

  //ACTION FOR LEVEL SETTINGS
  public function levelAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_level');

    //GET LEVEL ID
    if (null != ($id = $this->_getParam('id'))) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    //MAKE FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Settings_Level(array(
        'public' => ( in_array($level->type, array('public')) ),
        'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);

    //POPULATE DATA
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('sitestoreproduct_product', $id, array_keys($form->getValues())));

    $wishlistArray = array();
    $wishlistArray['create_wishlist'] = $permissionsTable->getAllowed('sitestoreproduct_wishlist', $id, 'create');
    $wishlistArray['wishlist'] = $permissionsTable->getAllowed('sitestoreproduct_wishlist', $id, 'view');
    $wishlistArray['auth_wishlist'] = $permissionsTable->getAllowed('sitestoreproduct_wishlist', $id, 'auth_view');
    $form->populate($wishlistArray);

    //CHECK POST
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK VALIDITY
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $values = $form->getValues();

    $wishlistSettings = array();
    $otherSettings = array();
    foreach ($values as $key => $value) {
      if ($key == 'create_wishlist') {
        $wishlistSettings['create'] = $value;
      } elseif ($key == 'wishlist') {
        $wishlistSettings['view'] = $value;
      } elseif ($key == 'auth_wishlist') {
        $wishlistSettings['auth_view'] = $value;
      } else {
        $otherSettings[$key] = $value;
      }
    }

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();
    try {

      //SET PERMISSION
      $permissionsTable->setAllowed('sitestoreproduct_product', $id, $values);
      include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR GETTING THE CATGEORIES, SUBCATEGORIES AND 3RD LEVEL CATEGORIES
  public function categoriesAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorecategories');

    $this->view->success_msg = $this->_getParam('success');

    //GET TASK
    if (isset($_POST['task'])) {
      $task = $_POST['task'];
    } elseif (isset($_GET['task'])) {
      $task = $_GET['task'];
    } else {
      $task = "main";
    }

    $orientation = $this->view->layout()->orientation;
    if ($orientation == 'right-to-left') {
      $this->view->directionality = 'rtl';
    } else {
      $this->view->directionality = 'ltr';
    }

    $local_language = $this->view->locale()->getLocale()->__toString();
    $local_language = explode('_', $local_language);
    $this->view->language = $local_language[0];

    //GET CATEGORIES TABLE
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');
    $tableCategoryName = $tableCategory->info('name');

    //GET STORAGE API
    $this->view->storage = Engine_Api::_()->storage();

    //GET PRODUCT TABLE
    $tableSitestoreproduct = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');

    if ($task == "changeorder") {
      $divId = $_GET['divId'];
      $sitestoreproductOrder = explode(",", $_GET['sitestoreproductorder']);
      //RESORT CATEGORIES
      if ($divId == "categories") {
        for ($i = 0; $i < count($sitestoreproductOrder); $i++) {
          $category_id = substr($sitestoreproductOrder[$i], 4);
          $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
        }
      } elseif (substr($divId, 0, 7) == "subcats") {
        for ($i = 0; $i < count($sitestoreproductOrder); $i++) {
          $category_id = substr($sitestoreproductOrder[$i], 4);
          $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
        }
      } elseif (substr($divId, 0, 11) == "treesubcats") {
        for ($i = 0; $i < count($sitestoreproductOrder); $i++) {
          $category_id = substr($sitestoreproductOrder[$i], 4);
          $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
        }
      }
    }

    $categories = array();
    $category_info = $tableCategory->getCategories(null, 0, 0, 1);
    foreach ($category_info as $value) {
      $sub_cat_array = array();
      $subcategories = $tableCategory->getSubCategories($value->category_id);
      foreach ($subcategories as $subresults) {
        $subsubcategories = $tableCategory->getSubCategories($subresults->category_id);
        $treesubarrays[$subresults->category_id] = array();

        foreach ($subsubcategories as $subsubcategoriesvalues) {

          //GET TOTAL PRODUCT COUNT
          $subsubcategory_sitestoreproduct_count = $tableSitestoreproduct->getProductsCount($subsubcategoriesvalues->category_id, 'subsubcategory_id');

          $treesubarrays[$subresults->category_id][] = $treesubarray = array(
              'tree_sub_cat_id' => $subsubcategoriesvalues->category_id,
              'tree_sub_cat_name' => $subsubcategoriesvalues->category_name,
              'count' => $subsubcategory_sitestoreproduct_count,
              'file_id' => $subsubcategoriesvalues->file_id,
              'banner_id' => $subsubcategoriesvalues->banner_id,
              'order' => $subsubcategoriesvalues->cat_order,
              'apply_compare' => $subsubcategoriesvalues->apply_compare,
              'sponsored' => $subsubcategoriesvalues->sponsored);
        }

        //GET TOTAL PRODUCTS COUNT
        $subcategory_sitestoreproduct_count = $tableSitestoreproduct->getProductsCount($subresults->category_id, 'subcategory_id');

        $sub_cat_array[] = $tmp_array = array(
            'sub_cat_id' => $subresults->category_id,
            'sub_cat_name' => $subresults->category_name,
            'tree_sub_cat' => $treesubarrays[$subresults->category_id],
            'count' => $subcategory_sitestoreproduct_count,
            'file_id' => $subresults->file_id,
            'banner_id' => $subresults->banner_id,
            'order' => $subresults->cat_order,
            'apply_compare' => $subresults->apply_compare,
            'sponsored' => $subresults->sponsored);
      }

      //GET TOTAL PRODUCTS COUNT
      $category_sitestoreproduct_count = $tableSitestoreproduct->getProductsCount($value->category_id, 'category_id');

      $categories[] = $category_array = array('category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          'count' => $category_sitestoreproduct_count,
          'file_id' => $value->file_id,
          'banner_id' => $value->banner_id,
          'sponsored' => $value->sponsored,
          'apply_compare' => $value->apply_compare,
          'sub_categories' => $sub_cat_array);
    }

    $this->view->categories = $categories;

    //GET CATEGORIES TABLE
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');
    $tableCategoryName = $tableCategory->info('name');
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->category_id = $category_id = $request->getParam('category_id', 0);
    $perform = $request->getParam('perform', 'add');
    $cat_dependency = 0;
    $subcat_dependency = 0;
    if ($category_id) {
      $category = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id);
      if ($category && empty($category->cat_dependency)) {
        $cat_dependency = $category->category_id;
      } elseif ($category && !empty($category->cat_dependency)) {
        $cat_dependency = $category->category_id;
        $subcat_dependency = $category->category_id;
      }
    }

    if ($perform == 'add') {
      $this->view->form = $form = new Sitestoreproduct_Form_Admin_Categories_Add();

      //CHECK POST
      if (!$this->getRequest()->isPost()) {
        return;
      }

      //CHECK VALIDITY
      if (!$form->isValid($this->getRequest()->getPost())) {

        if (empty($_POST['category_name'])) {
          $form->addError($this->view->translate("Category Name * Please complete this field - it is required."));
        }
        return;
      }

      //PROCESS
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $row_info = $tableCategory->fetchRow($tableCategory->select()->from($tableCategoryName, 'max(cat_order) AS cat_order'));
        $cat_order = $row_info['cat_order'] + 1;

        //GET CATEGORY TITLE
        $category_name = str_replace("'", "\'", trim($values['category_name']));
        $values['cat_order'] = $cat_order;
        $values['category_name'] = $category_name;
        $values['cat_dependency'] = $cat_dependency;
        $values['subcat_dependency'] = $subcat_dependency;

        include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';

        //UPLOAD ICON
        if (isset($_FILES['icon'])) {
          $photoFile = $row->setPhoto($form->icon);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $row->file_id = $photoFile->file_id;
          }
        }

        //UPLOAD CATEGORY PHOTO
        if (isset($_FILES['photo'])) {
          $photoFile = $row->setPhoto($form->photo, true);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $row->photo_id = $photoFile->file_id;
          }
        }

        //UPLOAD BANNER
        if (isset($_FILES['banner'])) {
          $photoFile = $row->setPhoto($form->banner);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $row->banner_id = $photoFile->file_id;
          }
        }

        $banner_url = preg_match('/\s*[a-zA-Z0-9]{2,5}:\/\//', $values['banner_url']);

        if (empty($banner_url)) {
          if ($values['banner_url']) {
            $row->banner_url = "http://" . $values['banner_url'];
          } else {
            $row->banner_url = $values['banner_url'];
          }
        } else {
          $row->banner_url = $values['banner_url'];
        }

        $category_id = $row->save();
        
                
        // IF SITETHEME IS ENABLED, THEN INSERT NEW CREATED PARENT CATEGORY ENTRY IN CORE_MENUITEMS
        $isSitethemeEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetheme');
        if( !empty($isSitethemeEnable) && empty($row->cat_dependency) && empty($row->subcat_dependency) )
        {
          $categoryOrder = ++$row->cat_order;
          $db = Engine_Db_Table::getDefaultAdapter();//$this->getDb();
          $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('sitestoreproduct_category_$category_id', 'sitestoreproduct', '$row->category_name', 'Sitetheme_Plugin_Menus::categoryUrl', '', 'sitetheme_main', '', '1', '0', $categoryOrder)");
        }

        $row->afterCreate();
        if (empty($cat_dependency) && empty($subcat_dependency)) {
          Engine_Api::_()->getApi('productType', 'sitestoreproduct')->categoriesPageCreate(array(0 => $category_id));
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_helper->redirector->gotoRoute(array('action' => 'categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'));
    } else {
      $this->view->form = $form = new Sitestoreproduct_Form_Admin_Categories_Edit();
      $category = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id);
      $form->populate($category->toArray());

      //CHECK POST
      if (!$this->getRequest()->isPost()) {
        return;
      }

      //CHECK VALIDITY
      if (!$form->isValid($this->getRequest()->getPost())) {

        if (empty($_POST['category_name'])) {
          $form->addError($this->view->translate("Category Name * Please complete this field - it is required."));
        }
        return;
      }
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET CATEGORY TITLE
        $category_name = str_replace("'", "\'", trim($values['category_name']));

        $category->category_name = $category_name;
        $category->meta_title = $values['meta_title'];
        $category->meta_description = $values['meta_description'];
        $category->meta_keywords = $values['meta_keywords'];
        $category->sponsored = $values['sponsored'];
        $category->banner_title = $values['banner_title'];
        $category->banner_url_window = $values['banner_url_window'];
        $category->category_slug = $values['category_slug'];
        $category->top_content = $values['top_content'];
        $category->bottom_content = $values['bottom_content'];
        $cat_dependency = $category->cat_dependency;
        $subcat_dependency = $category->subcat_dependency;
        if ($category_id && empty($subcat_dependency) && !empty($cat_dependency)) {
          $cat_dependency = $cat_dependency;
          $subcat_dependency = 0;
        } elseif ($category_id && !empty($subcat_dependency) && !empty($cat_dependency)) {
          $cat_dependency = $cat_dependency;
          $subcat_dependency = $subcat_dependency;
        }

        $category->cat_dependency = $cat_dependency;
        $category->subcat_dependency = $subcat_dependency;

        //UPLOAD ICON
        if (isset($_FILES['icon'])) {
          $previous_file_id = $category->file_id;
          $photoFile = $category->setPhoto($form->icon);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $category->file_id = $photoFile->file_id;

            //DELETE PREVIOUS CATEGORY ICON
            if ($previous_file_id) {
              $file = Engine_Api::_()->getItem('storage_file', $previous_file_id);
              if(!empty($file))
                $file->delete();
            }
          }
        }

        if (isset($_FILES['photo'])) {
          $previous_photo_id = $category->photo_id;
          $photoFile = $category->setPhoto($form->photo, true);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $category->photo_id = $photoFile->file_id;

            //DELETE PREVIOUS CATEGORY ICON
            if ($previous_photo_id) {
              $file = Engine_Api::_()->getItem('storage_file', $previous_photo_id);
              if(!empty($file))
                $file->delete();
            }
          }
        }

        //UPLOAD BANNER
        if (isset($_FILES['banner'])) {
          $previous_banner_id = $category->banner_id;
          $photoFile = $category->setPhoto($form->banner);
          //UPDATE FILE ID IN CATEGORY TABLE
          if (!empty($photoFile->file_id)) {
            $category->banner_id = $photoFile->file_id;

            //DELETE PREVIOUS CATEGORY BANNER
            if ($previous_banner_id) {
              $file = Engine_Api::_()->getItem('storage_file', $previous_banner_id);
              if(!empty($file))
                $file->delete();
            }
          }
        }

        $banner_url = preg_match('/\s*[a-zA-Z0-9]{2,5}:\/\//', $values['banner_url']);

        if (empty($banner_url)) {
          if ($values['banner_url']) {
            $category->banner_url = "http://" . $values['banner_url'];
          } else {
            $category->banner_url = $values['banner_url'];
          }
        } else {
          $category->banner_url = $values['banner_url'];
        }

        $category_id = $category->save();

        if (isset($values['removephoto']) && !empty($values['removephoto'])) {
          //DELETE CATEGORY ICON
          $file = Engine_Api::_()->getItem('storage_file', $category->photo_id);          

          //UPDATE FILE ID IN CATEGORY TABLE
          $category->photo_id = 0;
          $category->save();
          if(!empty($file))
            $file->delete();
        }

        if (isset($values['removeicon']) && !empty($values['removeicon'])) {
          //DELETE CATEGORY ICON
          $file = Engine_Api::_()->getItem('storage_file', $category->file_id);          

          //UPDATE FILE ID IN CATEGORY TABLE
          $category->file_id = 0;
          $category->save();
          if(!empty($file))
            $file->delete();
        }

        if (isset($values['removebanner']) && !empty($values['removebanner'])) {
          //DELETE CATEGORY ICON
          $file = Engine_Api::_()->getItem('storage_file', $category->banner_id);          

          //UPDATE FILE ID IN CATEGORY TABLE
          $category->banner_id = 0;
          $category->save();
          if(!empty($file))
            $file->delete();
        }


        if (empty($cat_dependency) && empty($subcat_dependency)) {
          Engine_Api::_()->getApi('productType', 'sitestoreproduct')->categoriesPageCreate(array(0 => $category_id));
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_helper->redirector->gotoRoute(array('action' => 'categories', 'controller' => 'settings', 'category_id' => $category_id, 'perform' => 'edit'));
    }
  }

  //ACTION FOR MAPPING OF PRODUCTS
  Public function mappingCategoryAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET CATEGORY ID AND OBJECT
    $this->view->catid = $catid = $this->_getParam('category_id');
    $category = Engine_Api::_()->getItem('sitestoreproduct_category', $catid);

    //GET CATEGORY DEPENDANCY
    $this->view->subcat_dependency = $subcat_dependency = $this->_getParam('subcat_dependency');

    //CREATE FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Settings_Mapping();

    $this->view->close_smoothbox = 0;

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    if ($this->getRequest()->isPost()) {

      //GET FORM VALUES
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET PRODUCT TABLE
        $tableSitestoreproduct = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
        $tableSitestoreproductName = $tableSitestoreproduct->info('name');

        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
        $reviewTableName = $reviewTable->info('name');

        //GET CATEGORY TABLE
        $tableCategory = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct');

        //ON CATEGORY DELETE
        $rows = $tableCategory->getSubCategories($catid);
        foreach ($rows as $row) {
          $subrows = $tableCategory->getSubCategories($row->category_id);
          foreach ($subrows as $subrow) {
            $subrow->delete();
          }
          $row->delete();
        }

        $previous_cat_profile_type = $tableCategory->getProfileType(null, $catid);
        $new_cat_profile_type = $tableCategory->getProfileType(null, $values['new_category_id']);

        /// PRODUCTS WHICH HAVE THIS CATEGORY
        if ($previous_cat_profile_type != $new_cat_profile_type && !empty($values['new_category_id'])) {
          $products = $tableSitestoreproduct->getCategoryList($catid, 'category_id');

          foreach ($products as $product) {

            //DELETE ALL MAPPING VALUES FROM FIELD TABLES
            Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'values')->delete(array('item_id = ?' => $product->product_id));
            Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'search')->delete(array('item_id = ?' => $product->product_id));
            //UPDATE THE PROFILE TYPE OF ALREADY CREATED PRODUCTS
            $tableSitestoreproduct->update(array('profile_type' => $new_cat_profile_type), array('product_id = ?' => $product->product_id));

            //REVIEW PROFILE TYPE UPDATION WORK
            $reviewIds = $reviewTable->select()
                    ->from($reviewTableName, 'review_id')
                    ->where('resource_id = ?', $product->product_id)
                    ->where('resource_type = ?', 'sitestoreproduct_product')
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
            if (!empty($reviewIds)) {
              foreach ($reviewIds as $reviewId) {
                //DELETE ALL MAPPING VALUES FROM FIELD TABLES
                Engine_Api::_()->fields()->getTable('sitestoreproduct_review', 'values')->delete(array('item_id = ?' => $reviewId));
                Engine_Api::_()->fields()->getTable('sitestoreproduct_review', 'search')->delete(array('item_id = ?' => $reviewId));

                //UPDATE THE PROFILE TYPE OF ALREADY CREATED REVIEWS
                $reviewTable->update(array('profile_type_review' => $new_cat_profile_type), array('resource_id = ?' => $reviewId));
              }
            }
          }
        }

        //PRODUCT TABLE CATEGORY DELETE WORK
        if (isset($values['new_category_id']) && !empty($values['new_category_id'])) {
          $tableSitestoreproduct->update(array('category_id' => $values['new_category_id']), array('category_id = ?' => $catid));
        } else {
          $selectListings = $tableSitestoreproduct->select()
                  ->from($tableSitestoreproduct->info('name'))
                  ->where('category_id = ?', $catid);

          foreach ($tableSitestoreproduct->fetchAll($selectListings) as $listing) {
            $listing->delete();
          }
        }

        $page_id = Zend_Db_Table_Abstract::getDefaultAdapter()->query("SELECT `page_id` FROM `engine4_core_pages` WHERE `engine4_core_pages`.`name` = 'sitestoreproduct_index_category-home_category_" . $catid . "'")->fetch();
        if (!empty($page_id)) {
          Zend_Db_Table_Abstract::getDefaultAdapter()->query("DELETE FROM `engine4_core_pages` WHERE `engine4_core_pages`.`page_id` = " . $page_id['page_id'] . " LIMIT 1");
          Zend_Db_Table_Abstract::getDefaultAdapter()->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = " . $page_id['page_id'] . "");
        }
        
        // IF SITETHEME IS ENABLED, THEN DELETE PARENT CATEGORY ENTRY FROM CORE_MENUITEMS
        $isSitethemeEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetheme');
        if( !empty($isSitethemeEnable) && empty($category->cat_dependency) && empty($category->subcat_dependency) )
        {
          $db = Engine_Db_Table::getDefaultAdapter();//$this->getDb();
          $db->query("DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'sitestoreproduct_category_$catid' LIMIT 1");
        }

        $category->delete();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }

    $this->view->close_smoothbox = 1;
  }

  //ACTION FOR GETTING THE MEMBER WHICH CAN BE CLAIMED THE PAGE
  function getProductsAction() {

    $store_id = $this->_getParam('store_id', null);

    $pageTable = Engine_Api::_()->getDbTable('pages', 'core');
    $page_name = $pageTable->select()
            ->from($pageTable->info('name'), 'name')
            ->where('page_id = ?', $store_id)
            ->query()
            ->fetchColumn();

    //GET PRODUCT TABLE
    $sitestoreproductTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    $sitestoreproductTableName = $sitestoreproductTable->info('name');

    //MAKE QUERY
    $select = $sitestoreproductTable->select()
            ->where('title  LIKE ? ', '%' . $this->_getParam('text') . '%')
            ->where($sitestoreproductTableName . '.approved = ?', '1')
            ->where($sitestoreproductTableName . '.draft = ?', '0')
            ->where($sitestoreproductTableName . '.search = ?', '1')
            ->order('title ASC')
            ->limit($this->_getParam('limit', 40));

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
      $select->where($sitestoreproductTableName . '.closed = ?', 0);
    }

    //FETCH RESULTS
    $usersitestoreproducts = $sitestoreproductTable->fetchAll($select);
    $data = array();
    $mode = $this->_getParam('struct');

    if ($mode == 'text') {
      foreach ($usersitestoreproducts as $usersitestoreproduct) {
        $content_photo = $this->view->itemPhoto($usersitestoreproduct, 'thumb.icon');
        $data[] = array(
            'id' => $usersitestoreproduct->product_id,
            'label' => $usersitestoreproduct->title,
            'photo' => $content_photo
        );
      }
    } else {
      foreach ($usersitestoreproducts as $usersitestoreproduct) {
        $content_photo = $this->view->itemPhoto($usersitestoreproduct, 'thumb.icon');
        $data[] = array(
            'id' => $usersitestoreproduct->product_id,
            'label' => $usersitestoreproduct->title,
            'photo' => $content_photo
        );
      }
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR GETTING THE MEMBER WHICH CAN BE CLAIMED THE PAGE
  function getReviewsAction() {

    //GET PRODUCT TABLE
    $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
    $reviewTableName = $reviewTable->info('name');

    //MAKE QUERY
    $select = $reviewTable->select()
            ->where('title  LIKE ? ', '%' . $this->_getParam('text') . '%')
            ->where($reviewTableName . '.type != ?', 'visitor')
            ->where($reviewTableName . '.status = ?', '1')
            ->order('title ASC')
            ->limit($this->_getParam('limit', 40));

    //FETCH RESULTS
    $reviews = $reviewTable->fetchAll($select);
    $data = array();
    $mode = $this->_getParam('struct');

    if ($mode == 'text') {
      foreach ($reviews as $review) {
        $content_photo = $this->view->itemPhoto($review->getOwner(), 'thumb.icon');
        $data[] = array(
            'id' => $review->review_id,
            'label' => $review->title,
            'photo' => $content_photo
        );
      }
    } else {
      foreach ($reviews as $review) {
        $content_photo = $this->view->itemPhoto($review->getOwner(), 'thumb.icon');
        $data[] = array(
            'id' => $review->review_id,
            'label' => $review->title,
            'photo' => $content_photo
        );
      }
    }
    return $this->_helper->json($data);
  }

  //ACTINO FOR SEARCH FORM TAB
  public function formSearchAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_form_search');

    //GET SEARCH TABLE
    $tableSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

    //CHECK POST
    if ($this->getRequest()->isPost()) {

      //BEGIN TRANSCATION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      $values = $_POST;
      $rowCategory = $tableSearchForm->getFieldsOptions('sitestoreproduct', 'category_id');
      $rowLocation = $tableSearchForm->getFieldsOptions('sitestoreproduct', 'location');
      $defaultCategory = 0;
      $defaultAddition = 0;
      $count = 1;
      try {
        foreach ($values['order'] as $key => $value) {
          $multiplyAddition = $count * 5;
          $tableSearchForm->update(array('order' => $defaultAddition + $defaultCategory + $key + $multiplyAddition + 1), array('searchformsetting_id = ?' => (int) $value));

          if (!empty($rowCategory) && $value == $rowCategory->searchformsetting_id) {
            $defaultCategory = 1;
            $defaultAddition = 10000000;
          }
          $count++;
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }

    //MAKE QUERY
    $select = $tableSearchForm->select()->where('module = ?', 'sitestoreproduct')->order('order');

    include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
  }

  //ACTION FOR DISPLAY/HIDE FIELDS OF SEARCH FORM
  public function diplayFormAction() {

    $field_id = $this->_getParam('id');
    $name = $this->_getParam('name');
    $display = $this->_getParam('display');
    if (!empty($field_id)) {

      if ($name == 'location' && $display == 0) {
        Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitestoreproduct', 'name = ?' => 'proximity'));
      }

      Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitestoreproduct', 'searchformsetting_id = ?' => (int) $field_id));
    }
    $this->_redirect('admin/sitestoreproduct/settings/form-search');
  }

  //ACTION FOR SHOW STATISTICS OF PRODUCT PLUGIN
  public function statisticAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_statistic');

    //GET PRODUCT TABLE
    $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
    $productTableName = $productTable->info('name');

    //GET PRODUCT DETAILS
    $select = $productTable->select()->from($productTableName, 'count(*) AS totalproduct');
    $this->view->totalSitestoreproduct = $select->query()->fetchColumn();

    $this->view->totalEditors = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct')->getEditorsCount();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totalpublish')->where('draft = ?', 0);
    $this->view->totalPublish = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totaldrafted')->where('draft = ?', 1);
    $this->view->totalDrafted = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totalclosed')->where('closed = ?', 1);
    $this->view->totalClosed = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totalopen')->where('closed = ?', 0);
    $this->view->totalOpen = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totalapproved')->where('approved = ?', 1);
    $this->view->totalapproved = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totaldisapproved')->where('approved = ?', 0);
    $this->view->totaldisapproved = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totalfeatured')->where('featured = ?', 1);
    $this->view->totalfeatured = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totalsponsored')->where('sponsored = ?', 1);
    $this->view->totalsponsored = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totalSimpleProducts')->where("product_type LIKE 'simple'");
    $this->view->totalSimpleProducts = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totalConfigurableProducts')->where("product_type LIKE 'configurable'");
    $this->view->totalConfigurableProducts = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totalVirtualProducts')->where("product_type LIKE 'virtual'");
    $this->view->totalVirtualProducts = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totalGroupedProducts')->where("product_type LIKE 'grouped'");
    $this->view->totalGroupedProducts = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totalBundledProducts')->where("product_type LIKE 'bundled'");
    $this->view->totalBundledProducts = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'count(*) AS totalDownloadableProducts')->where("product_type LIKE 'downloadable'");
    $this->view->totalDownloadableProducts = $select->query()->fetchColumn();

    $select = $productTable->select()->from($productTableName, 'sum(comment_count) AS totalcomments');
    $this->view->totalProductComments = $select->query()->fetchColumn();
    if (empty($this->view->totalProductComments))
      $this->view->totalProductComments = 0;

    $select = $productTable->select()->from($productTableName, 'sum(like_count) AS totalLikes');
    $this->view->totalProductLikes = $select->query()->fetchColumn();
    if (empty($this->view->totalProductLikes))
      $this->view->totalProductLikes = 0;

    //GET REVIEW TABLE
    $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
    $reviewTableName = $reviewTable->info('name');

    //GET REVIEW DETAILS
    $select = $reviewTable->select()->setIntegrityCheck(false)
            ->from($reviewTableName, 'count(*) AS totalreview')
            ->where($reviewTableName . '.resource_type = ?', 'sitestoreproduct_product')
            ->where($reviewTableName . '.type = ?', 'editor');

    $select->joinLeft("$productTableName", "$productTableName.product_id = $reviewTableName.resource_id", null);

    $this->view->totalEditorReviews = $select->query()->fetchColumn();

    $select = $reviewTable->select()->setIntegrityCheck(false)
            ->from($reviewTableName, 'count(*) AS totalreview')
            ->where($reviewTableName . '.status = ?', 0)
            ->where($reviewTableName . '.resource_type = ?', 'sitestoreproduct_product')
            ->where($reviewTableName . '.type = ?', 'editor');

    $select->joinLeft("$productTableName", "$productTableName.product_id = $reviewTableName.resource_id", null);

    $this->view->totalDraftEditorReviews = $select->query()->fetchColumn();

    $select = $reviewTable->select()->setIntegrityCheck(false)
            ->from($reviewTableName, 'count(*) AS totalreview')
            ->where($reviewTableName . '.resource_type = ?', 'sitestoreproduct_product')
            ->where($reviewTableName . '.type = ?', 'user')
            ->where($reviewTableName . '.owner_id != ?', 0);

    $select->joinLeft("$productTableName", "$productTableName.product_id = $reviewTableName.resource_id", null);

    $this->view->totalUserReviews = $select->query()->fetchColumn();

    $select = $reviewTable->select()->setIntegrityCheck(false)
            ->from($reviewTableName, 'count(*) AS totalreview')
            ->where($reviewTableName . '.resource_type = ?', 'sitestoreproduct_product')
            ->where($reviewTableName . '.type = ?', 'user')
            ->where($reviewTableName . '.owner_id = ?', 0)
            ->where($reviewTableName . '.status = ?', 1);

    $select->joinLeft("$productTableName", "$productTableName.product_id = $reviewTableName.resource_id", null);

    $this->view->totalApprovedVisitorsReviews = $select->query()->fetchColumn();

    $select = $reviewTable->select()->setIntegrityCheck(false)
            ->from($reviewTableName, 'count(*) AS totalreview')
            ->where($reviewTableName . '.resource_type = ?', 'sitestoreproduct_product')
            ->where($reviewTableName . '.type = ?', 'user')
            ->where($reviewTableName . '.owner_id = ?', 0)
            ->where($reviewTableName . '.status = ?', 0);

    $select->joinLeft("$productTableName", "$productTableName.product_id = $reviewTableName.resource_id", null);

    $this->view->totalDisApprovedVisitorsReviews = $select->query()->fetchColumn();

    $select = $reviewTable->select()->setIntegrityCheck(false)
            ->from($reviewTableName, 'count(*) AS totalreview');

    $select->joinLeft("$productTableName", "$productTableName.product_id = $reviewTableName.resource_id", null);


    $this->view->totalReviews = $select->query()->fetchColumn();

    //GET THE TOTAL DISCUSSIONES
    $discussionTable = Engine_Api::_()->getDbtable('topics', 'sitestoreproduct');
    $discussionTableName = $discussionTable->info('name');
    $select = $discussionTable->select()->setIntegrityCheck(false)
            ->from($discussionTableName, 'count(*) AS totaldiscussion');

    $select->joinLeft("$productTableName", "$productTableName.product_id = $discussionTableName.product_id", null);

    $this->view->totalDiscussionTopics = $select->query()->fetchColumn();

    //GET THE TOTAL POSTS
    $discussionPostTable = Engine_Api::_()->getDbtable('posts', 'sitestoreproduct');
    $discussionPostTableName = $discussionPostTable->info('name');
    $select = $discussionPostTable->select()->setIntegrityCheck(false)
            ->from($discussionPostTableName, 'count(*) AS totalpost');

    $select->joinLeft("$productTableName", "$productTableName.product_id = $discussionPostTableName.product_id", null);


    $this->view->totalDiscussionPosts = $select->query()->fetchColumn();

    //GET THE TOTAL PHOTOS
    $photoTable = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct');
    $photoTableName = $photoTable->info('name');
    $select = $photoTable->select()->setIntegrityCheck(false)
            ->from($photoTableName, 'count(*) AS totalphoto');

    $select->joinLeft("$productTableName", "$productTableName.product_id = $photoTableName.product_id", null);


    $this->view->totalPhotos = $select->query()->fetchColumn();

    //GET THE TOTAL VIDEOS
    $type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.video');
    if (empty($type_video)) {
      $videoTable = Engine_Api::_()->getDbtable('videos', 'sitestoreproduct');
    } else {
      $videoTable = Engine_Api::_()->getDbtable('clasfvideos', 'sitestoreproduct');
    }
    $videoTableName = $videoTable->info('name');
    $select = $videoTable->select()->setIntegrityCheck(false)
            ->from($videoTableName, 'count(*) AS totalvideo');

    $select->joinLeft("$productTableName", "$productTableName.product_id = $videoTableName.product_id", null);


    $this->view->totalVideos = $select->query()->fetchColumn();

    //GET WISHLITS FOR PRODUCT
    $this->view->totalWishlists = Engine_Api::_()->getDbTable('wishlists', 'sitestoreproduct')->getWishlistCount();
  }

  public function compareAction() {

    //GET NAGIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_compare');
    $compare_categories_id = 0;
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Compare_Settings();
    $category_id = $this->_getParam('category_id', null);
    $subcategory_id = $this->_getParam('subcategory_id', null);
    $subsubcategory_id = $this->_getParam('subsubcategory_id', null);

    $paramsArray = array(
        'category_id' => $category_id,
        'subcategory_id' => $subcategory_id,
        'subsubcategory_id' => $subsubcategory_id
    );
    $category_ids = array();

    if (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.compare', 1)))
      return;

    if (isset($form->tags))
      $form->tags->setLabel('Tags');

    $categories = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoriesList(0);
    $categoriesFormMultiOptions = array();
    $checkCategoriesFlage = true;
    foreach ($categories as $category) {
      if ($checkCategoriesFlage && !empty($category_id) && $category_id == $category->category_id) {
        $checkCategoriesFlage = false;
        $compare_categories_id = $category_id;
        $category_ids[0] = $category->category_id;
      }
      $categoriesFormMultiOptions[$category->category_id] = $category->category_name;
    }
    $this->view->countCategories = $countCategories = count($categoriesFormMultiOptions);
    if (empty($countCategories))
      return;
    $form->getElement('category_id')
            ->setMultiOptions($categoriesFormMultiOptions);

    if (empty($category_id) || $checkCategoriesFlage) {
      $category_ids[0] = $compare_categories_id = $paramsArray['category_id'] = $category_id = key($categoriesFormMultiOptions);
    }

    $sub_category_id = $paramsArray['category_id'];
    $firstlevelcategory = Engine_Api::_()->getItem('sitestoreproduct_category', $paramsArray['category_id']);
    if (empty($firstlevelcategory->apply_compare)) {
      $subcategories = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoriesList($paramsArray['category_id']);
    } else {
      $subcategories = array();
    }

    $subcategoriesFormMultiOptions = array();
    $checkCategoriesFlage = true;
    foreach ($subcategories as $category) {
      if ($checkCategoriesFlage && !empty($subcategory_id) && $subcategory_id == $category->category_id) {
        $checkCategoriesFlage = false;
        $compare_categories_id = $category->category_id;
        $category_ids[1] = $category->category_id;
      }
      $subcategoriesFormMultiOptions[$category->category_id] = $category->category_name;
    }
    $countSubcategories = count($subcategoriesFormMultiOptions);

    if ($countSubcategories) {
      $form->getElement('subcategory_id')
              ->setMultiOptions($subcategoriesFormMultiOptions);
      if (empty($subcategory_id) || $checkCategoriesFlage) {
        $category_ids[1] = $compare_categories_id = $paramsArray['subcategory_id'] = $subcategory_id = key($subcategoriesFormMultiOptions);
      }
      $secondlevelcategory = Engine_Api::_()->getItem('sitestoreproduct_category', $subcategory_id);
      if (empty($secondlevelcategory->apply_compare)) {
        $subsubcategories = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategoriesList($subcategory_id);
      } else {
        $subsubcategories = array();
      }
      $subsubcategoriesFormMultiOptions = array();
      $checkCategoriesFlage = true;
      foreach ($subsubcategories as $category) {
        if ($checkCategoriesFlage && !empty($subsubcategory_id) && $subsubcategory_id == $category->category_id) {
          $checkCategoriesFlage = false;
          $compare_categories_id = $category->category_id;
          $category_ids[2] = $category->category_id;
        }
        $subsubcategoriesFormMultiOptions[$category->category_id] = $category->category_name;
      }
      $countSubcategories = count($subsubcategoriesFormMultiOptions);

      if ($countSubcategories) {
        $form->getElement('subsubcategory_id')
                ->setMultiOptions($subsubcategoriesFormMultiOptions);

        if (empty($subsubcategory_id) || $checkCategoriesFlage) {
          $category_ids[2] = $compare_categories_id = $paramsArray['subsubcategory_id'] = $subcategory_id = key($subsubcategoriesFormMultiOptions);
        }
      } else {
        $form->removeElement('subsubcategory_id');
      }
    } else {
      $form->removeElement('subcategory_id');
      $form->removeElement('subsubcategory_id');
    }

    $compareSettingsTable = Engine_Api::_()->getDbtable('compareSettings', 'sitestoreproduct');
    $this->view->compareSettingList = $result = $compareSettingsTable->getCompareList(array(
        'category_id' => $compare_categories_id,
        'fetchRow' => 1
    ));

    if (empty($result)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $resultArray = $result->toArray();
    unset($resultArray['category_id']);
    $resultArray['custom_fields'] = !empty($result->custom_fields) ? Zend_Json_Decoder::decode($result->custom_fields) : array();
    $resultArray['editor_rating_fields'] = !empty($result->editor_rating_fields) ? Zend_Json_Decoder::decode($result->editor_rating_fields) : array();
    $resultArray['user_rating_fields'] = !empty($result->user_rating_fields) ? Zend_Json_Decoder::decode($result->user_rating_fields) : array();
    $resultArray = array_merge($resultArray, $paramsArray);

    $ratingsParamsArray = array();
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2)) {
      $ratingsParams = Engine_Api::_()->getDbtable('ratingparams', 'sitestoreproduct')->reviewParams($category_ids, 'sitestoreproduct_product');
      foreach ($ratingsParams as $value) {
        $ratingsParamsArray[$value->ratingparam_id] = $value->ratingparam_name;
      }
    }
    if (count($ratingsParamsArray) > 0 && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3)) {
      $form->getElement('editor_rating_fields')
              ->setMultiOptions($ratingsParamsArray);
    } else {
      $form->removeElement('editor_rating_fields');
    }

    if (count($ratingsParamsArray) > 0 && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3)) {
      $form->getElement('user_rating_fields')
              ->setMultiOptions($ratingsParamsArray);
    } else {
      $form->removeElement('user_rating_fields');
    }

    $proifle_map_ids = Engine_Api::_()->getDBTable('categories', 'sitestoreproduct')->getAllProfileTypes($category_ids, 1);
    $multiOptionsCustomFields = array();
    foreach ($proifle_map_ids as $proifle_map_id) {
      $selectOption = Engine_Api::_()->getDBTable('metas', 'sitestoreproduct')->getProfileFields($proifle_map_id);
      if ($selectOption) {
        foreach ($selectOption as $key => $value) {
          $multiOptionsCustomFields[$key] = $value['lable'] . " (" . ucfirst($value['type']) . ")";
        }
      }
    }
    if (count($multiOptionsCustomFields) > 0) {
      $form->getElement('field_dummy_3')
              ->setLabel(Engine_Api::_()->getDBTable('options', 'sitestoreproduct')->getFieldLabel($proifle_map_id) . ' Product Profile Questions')
              ->setDescription('Choose the options from below that you want to display under the "Specifications" section on products comparison page.)');

      $form->getElement('custom_fields')
              ->setMultiOptions($multiOptionsCustomFields);
    } else {
      $form->removeElement('field_dummy_3');
      $form->removeElement('custom_fields');
    }

    if (!$this->getRequest()->isPost()) {
      $form->populate($resultArray);
    }
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      unset($values['category_id']);
      if (isset($values['subcategory_id']))
        unset($values['subcategory_id']);
      if (isset($values['subsubcategory_id']))
        unset($values['subsubcategory_id']);
      unset($values['field_dummy_1']);
      unset($values['field_dummy_2']);
      unset($values['field_dummy_3']);
      unset($values['save']);
      if (!isset($values['custom_fields']))
        $values['custom_fields'] = array();
      $values['custom_fields'] = Zend_Json::encode($values['custom_fields']);
      if (isset($values['editor_rating_fields']))
        $values['editor_rating_fields'] = Zend_Json::encode($values['editor_rating_fields']);
      else
        $values['editor_rating_fields'] = null;
      if (isset($values['user_rating_fields']))
        $values['user_rating_fields'] = Zend_Json::encode($values['user_rating_fields']);
      else
        $values['user_rating_fields'] = null;
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
        $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //ACTION FOR SHOWING THE Video
  public function showVideoAction() {

    //GET NAGIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorevideo');

    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_admin_submain', array(), 'sitestoreproduct_admin_submain_general_tab');

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    //MAKE FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Video_General();
    $type_video_value = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.video', 1);

    if ($this->getRequest()->isPost()) {
      
      $currentYouTubeApiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
      if( !empty($_POST['video_youtube_apikey']) && $_POST['video_youtube_apikey'] != $currentYouTubeApiKey ) {
        $response = Engine_Api::_()->seaocore()->verifyYotubeApiKey($_POST['video_youtube_apikey']);
        if( !empty($response['errors']) ) {
          $error_message = array('Invalid API Key');
          foreach( $response['errors'] as $error ) {
            $error_message[] = "Error Reason (" . $error['reason'] . '): ' . $error['message'];
          }
          return $form->video_youtube_apikey->addErrors($error_message);
        }
      }

      $values = $_POST;

      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video')) {
        $coreVideoTable = Engine_Api::_()->getDbtable('videos', 'video');
        $coreVideoTableName = $coreVideoTable->info('name');

        $videoRating = Engine_Api::_()->getDbTable('ratings', 'video');
        $videoRatingName = $videoRating->info('name');        
      }

      $reviewVideoTable = Engine_Api::_()->getDbtable('videos', 'sitestoreproduct');
      $reviewVideoTableName = $reviewVideoTable->info('name');

      $reviewVideoRatingTable = Engine_Api::_()->getDbTable('ratings', 'sitestoreproduct');
      $reviewVideoRatingName = $reviewVideoRatingTable->info('name');

      $sitestoreproductVideoTable = Engine_Api::_()->getDbtable('clasfvideos', 'sitestoreproduct');
      $sitestoreproductVideoTableName = $sitestoreproductVideoTable->info('name');
      if ($type_video_value != $values['sitestoreproduct_show_video']) {
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video') && !empty($values['sitestoreproduct_show_video'])) {

          $selectProductVideos = $reviewVideoTable->select()
                  ->from($reviewVideoTableName, array('video_id', 'product_id'))
                  ->where('is_import != ?', 1)
                  ->group('video_id');
          $productVideoDatas = $reviewVideoTable->fetchAll($selectProductVideos);
          foreach ($productVideoDatas as $productVideoData) {
            $listVideo = Engine_Api::_()->getItem('sitestoreproduct_video', $productVideoData->video_id);
            if (!empty($listVideo)) {

              $db = $sitestoreproductVideoTable->getAdapter();
              $db->beginTransaction();

              try {
                $clasfVideo = $sitestoreproductVideoTable->createRow();
                $clasfVideo->product_id = $productVideoData->product_id;
                $clasfVideo->video_id = $productVideoData->video_id;
                $clasfVideo->is_import = 1;
                $clasfVideo->created = $listVideo->creation_date;

                $clasfVideo->save();
                $db->commit();
              } catch (Exception $e) {
                $db->rollBack();
                throw $e;
              }

              $db = $coreVideoTable->getAdapter();
              $db->beginTransaction();

              try {
                $coreVideo = $coreVideoTable->createRow();
                $coreVideo->title = $listVideo->title;
                $coreVideo->description = $listVideo->description;
                $coreVideo->search = $listVideo->search;
                $coreVideo->owner_id = $listVideo->owner_id;
                $coreVideo->creation_date = $listVideo->creation_date;
                $coreVideo->modified_date = $listVideo->modified_date;

                $coreVideo->view_count = 1;
                if ($listVideo->view_count > 0) {
                  $coreVideo->view_count = $listVideo->view_count;
                }

                $coreVideo->comment_count = $listVideo->comment_count;
                $coreVideo->type = $listVideo->type;
                $coreVideo->code = $listVideo->code;
                $coreVideo->rating = $listVideo->rating;
                $coreVideo->status = $listVideo->status;
                $coreVideo->file_id = 0;
                $coreVideo->duration = $listVideo->duration;
                $coreVideo->save();
                $db->commit();
              } catch (Exception $e) {
                $db->rollBack();
                throw $e;
              }

              //START VIDEO THUMB WORK
              if (!empty($coreVideo->code) && !empty($coreVideo->type) && !empty($listVideo->photo_id)) {
                $storageTable = Engine_Api::_()->getDbtable('files', 'storage');
                $storageData = $storageTable->fetchRow(array('file_id = ?' => $listVideo->photo_id));
                if (!empty($storageData)) {
                  $thumbnail = $storageData->storage_path;

                  $ext = ltrim(strrchr($thumbnail, '.'), '.');
                  $thumbnail_parsed = @parse_url($thumbnail);

                  if (@GetImageSize($thumbnail)) {
                    $valid_thumb = true;
                  } else {
                    $valid_thumb = false;
                  }

                  if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                    $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                    $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
                    $src_fh = fopen($thumbnail, 'r');
                    $tmp_fh = fopen($tmp_file, 'w');
                    stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
                    $image = Engine_Image::factory();
                    $image->open($tmp_file)
                            ->resize(120, 240)
                            ->write($thumb_file)
                            ->destroy();

                    try {
                      $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                          'parent_type' => 'video',
                          'parent_id' => $coreVideo->video_id
                      ));

                      //REMOVE TEMP FILE
                      @unlink($thumb_file);
                      @unlink($tmp_file);
                    } catch (Exception $e) {
                      
                    }

                    $coreVideo->photo_id = $thumbFileRow->file_id;
                    $coreVideo->save();
                  }
                }
              }
              //END VIDEO THUMB WORK
              //START FETCH TAG
              $videoTags = $listVideo->tags()->getTagMaps();
              $tagString = '';

              foreach ($videoTags as $tagmap) {

                if ($tagString != '')
                  $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();

                $owner = Engine_Api::_()->getItem('user', $listVideo->owner_id);
                $tags = preg_split('/[,]+/', $tagString);
                $tags = array_filter(array_map("trim", $tags));
                $coreVideo->tags()->setTagMaps($owner, $tags);
              }
              //END FETCH TAG

              $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
              $likeTableName = $likeTable->info('name');

              //START FETCH LIKES
              $selectLike = $likeTable->select()
                      ->from($likeTableName, 'like_id')
                      ->where('resource_type = ?', 'sitestoreproduct_video')
                      ->where('resource_id = ?', $productVideoData->video_id);
              $selectLikeDatas = $likeTable->fetchAll($selectLike);
              foreach ($selectLikeDatas as $selectLikeData) {
                $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                $newLikeEntry = $likeTable->createRow();
                $newLikeEntry->resource_type = 'video';
                $newLikeEntry->resource_id = $like->resource_id;
                $newLikeEntry->poster_type = 'user';
                $newLikeEntry->poster_id = $like->poster_id;
                $newLikeEntry->creation_date = $like->creation_date;
                $newLikeEntry->save();
              }
              //END FETCH LIKES

              $commentTable = Engine_Api::_()->getDbtable('comments', 'core');
              $commentTableName = $commentTable->info('name');

              //START FETCH COMMENTS
              $selectLike = $commentTable->select()
                      ->from($commentTableName, 'comment_id')
                      ->where('resource_type = ?', 'sitestoreproduct_video')
                      ->where('resource_id = ?', $productVideoData->video_id);
              $selectLikeDatas = $commentTable->fetchAll($selectLike);
              foreach ($selectLikeDatas as $selectLikeData) {
                $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                $newLikeEntry = $commentTable->createRow();
                $newLikeEntry->resource_type = 'video';
                $newLikeEntry->resource_id = $comment->resource_id;
                $newLikeEntry->poster_type = 'user';
                $newLikeEntry->poster_id = $comment->poster_id;
                $newLikeEntry->body = $comment->body;
                $newLikeEntry->creation_date = $comment->creation_date;
                $newLikeEntry->like_count = $comment->like_count;
                $newLikeEntry->save();
              }
              //END FETCH COMMENTS
              //START UPDATE TOTAL LIKES IN PRODUCT-VIDEO TABLE
              $selectLikeCount = $likeTable->select()
                      ->from($likeTableName, array('COUNT(*) AS like_count'))
                      ->where('resource_type = ?', 'sitestoreproduct_video')
                      ->where('resource_id = ?', $coreVideo->video_id);
              $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);

              //END UPDATE TOTAL LIKES IN PRODUCT-VIDEO TABLE
              //START FETCH RATTING DATA
//              $selectVideoRating = $videoRating->select()
//                      ->from($videoRatingName)
//                      ->where('video_id = ?', $productVideoData->video_id);

              $videoRatingDatas = $videoRating->fetchAll($selectVideoRating);
              if (!empty($videoRatingDatas)) {
                $videoRatingDatas = $videoRatingDatas->toArray();
              }

              foreach ($videoRatingDatas as $videoRatingData) {

                $reviewVideoRatingTable->insert(array(
                    'video_id' => $coreVideo->video_id,
                    'user_id' => $videoRatingData['user_id'],
                    'product_id' => $coreVideo->product_id,
                    'rating' => $videoRatingData['rating']
                ));
              }
              //END FETCH RATTING DATA
              $reviewVideoTable->update(array('is_import' => 1), array('video_id = ?' => $productVideoData->video_id));
            }
          }
          //END FETCH VIDEO DATA
        } else {
          //START FETCH VIDEO DATA


          $selectSitestoreproductVideos = $sitestoreproductVideoTable->select()
                  ->from($sitestoreproductVideoTableName, array('video_id', 'product_id'))
                  ->where('is_import != ?', 1)
                  ->group('video_id');
          $sitestoreproductVideoDatas = $sitestoreproductVideoTable->fetchAll($selectSitestoreproductVideos);
          foreach ($sitestoreproductVideoDatas as $sitestoreproductVideoData) {
            $sitestoreproductVideo = Engine_Api::_()->getItem('video', $sitestoreproductVideoData->video_id);
            if (!empty($sitestoreproductVideo)) {
              $db = $reviewVideoTable->getAdapter();
              $db->beginTransaction();

              try {
                $productVideo = $reviewVideoTable->createRow();
                $productVideo->product_id = $sitestoreproductVideoData->product_id;
                $productVideo->title = $sitestoreproductVideo->title;
                $productVideo->description = $sitestoreproductVideo->description;
                $productVideo->search = $sitestoreproductVideo->search;
                $productVideo->owner_id = $sitestoreproductVideo->owner_id;
                $productVideo->creation_date = $sitestoreproductVideo->creation_date;
                $productVideo->modified_date = $sitestoreproductVideo->modified_date;

                $productVideo->view_count = 1;
                if ($sitestoreproductVideo->view_count > 0) {
                  $productVideo->view_count = $sitestoreproductVideo->view_count;
                }

                $productVideo->comment_count = $sitestoreproductVideo->comment_count;
                $productVideo->type = $sitestoreproductVideo->type;
                $productVideo->code = $sitestoreproductVideo->code;
                $productVideo->rating = $sitestoreproductVideo->rating;
                $productVideo->status = $sitestoreproductVideo->status;
                $productVideo->file_id = 0;
                $productVideo->duration = $sitestoreproductVideo->duration;
                $productVideo->is_import = 1;
                $productVideo->save();
                $db->commit();
              } catch (Exception $e) {
                $db->rollBack();
                throw $e;
              }

              //START VIDEO THUMB WORK
              if (!empty($productVideo->code) && !empty($productVideo->type) && !empty($sitestoreproductVideo->photo_id)) {
                $storageTable = Engine_Api::_()->getDbtable('files', 'storage');
                $storageData = $storageTable->fetchRow(array('file_id = ?' => $sitestoreproductVideo->photo_id));
                if (!empty($storageData)) {
                  $thumbnail = $storageData->storage_path;

                  $ext = ltrim(strrchr($thumbnail, '.'), '.');
                  $thumbnail_parsed = @parse_url($thumbnail);

                  if (@GetImageSize($thumbnail)) {
                    $valid_thumb = true;
                  } else {
                    $valid_thumb = false;
                  }

                  if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                    $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                    $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
                    $src_fh = fopen($thumbnail, 'r');
                    $tmp_fh = fopen($tmp_file, 'w');
                    stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
                    $image = Engine_Image::factory();
                    $image->open($tmp_file)
                            ->resize(120, 240)
                            ->write($thumb_file)
                            ->destroy();

                    try {
                      $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                          'parent_type' => 'sitestoreproduct_video',
                          'parent_id' => $productVideo->video_id
                      ));

                      //REMOVE TEMP FILE
                      @unlink($thumb_file);
                      @unlink($tmp_file);
                    } catch (Exception $e) {
                      
                    }

                    $productVideo->photo_id = $thumbFileRow->file_id;
                    $productVideo->save();
                  }
                }
              }
              //END VIDEO THUMB WORK
              //START FETCH TAG
              $videoTags = $sitestoreproductVideo->tags()->getTagMaps();
              $tagString = '';

              foreach ($videoTags as $tagmap) {

                if ($tagString != '')
                  $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();

                $owner = Engine_Api::_()->getItem('user', $sitestoreproductVideo->owner_id);
                $tags = preg_split('/[,]+/', $tagString);
                $tags = array_filter(array_map("trim", $tags));
                $productVideo->tags()->setTagMaps($owner, $tags);
              }
              //END FETCH TAG

              $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
              $likeTableName = $likeTable->info('name');

              //START FETCH LIKES
              $selectLike = $likeTable->select()
                      ->from($likeTableName, 'like_id')
                      ->where('resource_type = ?', 'video')
                      ->where('resource_id = ?', $sitestoreproductVideoData->video_id);
              $selectLikeDatas = $likeTable->fetchAll($selectLike);
              foreach ($selectLikeDatas as $selectLikeData) {
                $like = Engine_Api::_()->getItem('core_like', $selectLikeData->like_id);

                $newLikeEntry = $likeTable->createRow();
                $newLikeEntry->resource_type = 'sitestoreproduct_video';
                $newLikeEntry->resource_id = $productVideo->video_id;
                $newLikeEntry->poster_type = 'user';
                $newLikeEntry->poster_id = $like->poster_id;
                $newLikeEntry->creation_date = $like->creation_date;
                $newLikeEntry->save();
              }
              //END FETCH LIKES

              $commentTable = Engine_Api::_()->getDbtable('comments', 'core');
              $commentTableName = $commentTable->info('name');

              //START FETCH COMMENTS
              $selectLike = $commentTable->select()
                      ->from($commentTableName, 'comment_id')
                      ->where('resource_type = ?', 'video')
                      ->where('resource_id = ?', $sitestoreproductVideoData->video_id);
              $selectLikeDatas = $commentTable->fetchAll($selectLike);
              foreach ($selectLikeDatas as $selectLikeData) {
                $comment = Engine_Api::_()->getItem('core_comment', $selectLikeData->comment_id);

                $newLikeEntry = $commentTable->createRow();
                $newLikeEntry->resource_type = 'sitestoreproduct_video';
                $newLikeEntry->resource_id = $productVideo->video_id;
                $newLikeEntry->poster_type = 'user';
                $newLikeEntry->poster_id = $comment->poster_id;
                $newLikeEntry->body = $comment->body;
                $newLikeEntry->creation_date = $comment->creation_date;
                $newLikeEntry->like_count = $comment->like_count;
                $newLikeEntry->save();
              }
              //END FETCH COMMENTS
              //START UPDATE TOTAL LIKES IN PRODUCT-VIDEO TABLE
              $selectLikeCount = $likeTable->select()
                      ->from($likeTableName, array('COUNT(*) AS like_count'))
                      ->where('resource_type = ?', 'sitestoreproduct_video')
                      ->where('resource_id = ?', $productVideo->video_id);
              $selectLikeCounts = $likeTable->fetchAll($selectLikeCount);
              if (!empty($selectLikeCounts)) {
                $selectLikeCounts = $selectLikeCounts->toArray();
                $productVideo->like_count = $selectLikeCounts[0]['like_count'];
                $productVideo->save();
              }
              //END UPDATE TOTAL LIKES IN PRODUCT-VIDEO TABLE
              //START FETCH RATTING DATA
//              $selectVideoRating = $videoRating->select()
//                      ->from($videoRatingName)
//                      ->where('video_id = ?', $sitestoreproductVideoData->video_id);

              $videoRatingDatas = $videoRating->fetchAll($selectVideoRating);
              if (!empty($videoRatingDatas)) {
                $videoRatingDatas = $videoRatingDatas->toArray();
              }

              foreach ($videoRatingDatas as $videoRatingData) {

                $reviewVideoRatingTable->insert(array(
                    'video_id' => $productVideo->video_id,
                    'user_id' => $videoRatingData['user_id'],
                    'product_id' => $productVideo->product_id,
                    'rating' => $videoRatingData['rating']
                ));
              }
              //END FETCH RATTING DATA
              $sitestoreproductVideoTable->update(array('is_import' => 0), array('video_id = ?' => $sitestoreproductVideoData->video_id));
            }
          }
        }
      }

      if ($type_video_value != $values['sitestoreproduct_show_video']) {
        if (!empty($values['sitestoreproduct_show_video'])) {

          $db->query("UPDATE `engine4_activity_actiontypes` SET `enabled` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'video_sitestoreproduct' ");
          $db->query("UPDATE `engine4_activity_actiontypes` SET `enabled` = '0' WHERE `engine4_activity_actiontypes`.`type` = 'sitestoreproduct_video_new' ");
        } elseif (empty($values['sitestoreproduct_show_video'])) {

          $db->query("UPDATE `engine4_activity_actiontypes` SET `enabled` = '1' WHERE `engine4_activity_actiontypes`.`type` = 'sitestoreproduct_video_new' ");
          $db->query("UPDATE `engine4_activity_actiontypes` SET `enabled` = '0' WHERE `engine4_activity_actiontypes`.`type` = 'video_sitestoreproduct' ");
        }
      }

      // Okay, save
      foreach ($values as $key => $value) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      if (!empty($values['sitestoreproduct_show_video'])) {
        Engine_Api::_()->getDbtable('menuItems', 'core')->update(array('enabled' => 0), array('name = ?' => 'sitestoreproduct_admin_submain_setting_tab', 'module = ?' => 'sitestoreproduct'));
        Engine_Api::_()->getDbtable('menuItems', 'core')->update(array('enabled' => 0), array('name = ?' => 'sitestoreproduct_admin_submain_utilities_tab', 'module = ?' => 'sitestoreproduct'));
      } else {
        Engine_Api::_()->getDbtable('menuItems', 'core')->update(array('enabled' => 1), array('name = ?' => 'sitestoreproduct_admin_submain_setting_tab', 'module = ?' => 'sitestoreproduct'));
        Engine_Api::_()->getDbtable('menuItems', 'core')->update(array('enabled' => 1), array('name = ?' => 'sitestoreproduct_admin_submain_utilities_tab', 'module = ?' => 'sitestoreproduct'));
      }
      return $this->_helper->redirector->gotoRoute(array('action' => 'show-video'));
    }
  }

//  public function readmeAction() {
//
//    $this->view->faq = 0;
//    $this->view->faq_type = $this->_getParam('faq_type', 'general');
//  }

  public function applayCompareAction() {
    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET CATEGORY ID
    $this->view->category_id = $category_id = $this->_getParam('category_id');

    //GET CATEGORY ITEM
    $this->view->category = $category = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id);

    if (!$this->getRequest()->isPost()) {
      return;
    }
    //UPDATE FILE ID IN CATEGORY TABLE
    $category->applyCompare();
    //$category->save();
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 1000,
        'parentRefresh' => true,
        'messages' => Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.')
    ));
  }

  //ACTION FOR DELETE THE PRODUCT
  public function deleteCategoryAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $category_id = $this->_getParam('category_id');
    $cat_dependency = $this->_getParam('cat_dependency');

    $this->view->category_id = $category_id;

    //GET CATEGORIES TABLE
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');
    $tableCategoryName = $tableCategory->info('name');

    //GET PRODUCT TABLE
    $tableSitestoreproduct = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');

    if ($this->getRequest()->isPost()) {
      //if($cat_dependency != 0) {
      //IF SUB-CATEGORY AND 3RD LEVEL CATEGORY IS MAPPED
      $previous_cat_profile_type = $tableCategory->getProfileType(null, $category_id);

      if ($previous_cat_profile_type) {

        //SELECT PRODUCTS WHICH HAVE THIS CATEGORY
        $products = $tableSitestoreproduct->getCategoryList($category_id, 'category_id');

        foreach ($products as $product) {

          //DELETE ALL MAPPING VALUES FROM FIELD TABLES
          Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'values')->delete(array('item_id = ?' => $product->product_id));
          Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'search')->delete(array('item_id = ?' => $product->product_id));

          //UPDATE THE PROFILE TYPE OF ALREADY CREATED PRODUCTS
          $tableSitestoreproduct->update(array('profile_type' => 0), array('product_id = ?' => $product->product_id));

          //GET REVIEW TABLE
          $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
          $reviewTableName = $reviewTable->info('name');

          //REVIEW PROFILE TYPE UPDATION WORK
          $reviewIds = $reviewTable->select()
                  ->from($reviewTableName, 'review_id')
                  ->where('resource_id = ?', $product->product_id)
                  ->where('resource_type = ?', 'sitestoreproduct_product')
                  ->query()
                  ->fetchAll(Zend_Db::FETCH_COLUMN)
          ;
          if (!empty($reviewIds)) {
            foreach ($reviewIds as $reviewId) {
              //DELETE ALL MAPPING VALUES FROM FIELD TABLES
              Engine_Api::_()->fields()->getTable('sitestoreproduct_review', 'values')->delete(array('item_id = ?' => $reviewId));
              Engine_Api::_()->fields()->getTable('sitestoreproduct_review', 'search')->delete(array('item_id = ?' => $reviewId));

              //UPDATE THE PROFILE TYPE OF ALREADY CREATED REVIEWS
              $reviewTable->update(array('profile_type_review' => 0), array('resource_id = ?' => $reviewId));
            }
          }
        }
      }

      //SITESTORE TABLE SUB-CATEGORY/3RD LEVEL DELETE WORK
      $tableSitestoreproduct->update(array('subcategory_id' => 0, 'subsubcategory_id' => 0), array('subcategory_id = ?' => $category_id));
      $tableSitestoreproduct->update(array('subsubcategory_id' => 0), array('subsubcategory_id = ?' => $category_id));

      $tableCategory->delete(array('cat_dependency = ?' => $category_id, 'subcat_dependency = ?' => $category_id));
      $tableCategory->delete(array('category_id = ?' => $category_id));

      //}
      //GET URL
      $url = $this->_helper->url->url(array('action' => 'categories', 'controller' => 'settings', 'perform' => 'add', 'category_id' => 0));
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRedirect' => $url,
          'parentRedirectTime' => 1,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }

    $this->renderScript('admin-settings/delete-category.tpl');
  }

  public function integrationsAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_integrations');
  }

  //ACTION FOR AD SHOULD BE DISPLAY OR NOT ON PAGES
  public function adsettingsAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_adsettings');

    //FORM
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Adsettings();

    //CHECK THAT COMMUNITY AD PLUGIN IS ENABLED OR NOT
    $communityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if ($communityadEnabled) {
      $this->view->ismoduleenabled = $ismoduleenabled = 1;
    } else {
      $this->view->ismoduleenabled = $ismoduleenabled = 0;
    }

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
    }
  }

  public function graphAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_productgraph');

    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Settings_Graph();

    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
      $values = $form->getValues();

      foreach ($values as $key => $value) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }

//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graph.bgcolor', $values ['sitestoreproduct_graph_bgcolor']);
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graphgrossamount.width', $values ['sitestoreproduct_graphgrossamount_width']);
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graphgrossamount.color', $values ['sitestoreproduct_graphgrossamount_color']);
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graphnetamount.width', $values ['sitestoreproduct_graphnetamount_width']);
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graphnetamount.color', $values ['sitestoreproduct_graphnetamount_color']);
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graphtransactions.width', $values ['sitestoreproduct_graphtransactions_width']);
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graphtransactions.color', $values ['sitestoreproduct_graphtransactions_color']);
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graphcommission.width', $values ['sitestoreproduct_graphcommission_width']);
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graphcommission.color', $values ['sitestoreproduct_graphcommission_color']);
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graphtax.width', $values ['sitestoreproduct_graphtax_width']);
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graphtax.color', $values ['sitestoreproduct_graphtax_color']);
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graphshippingprice.width', $values ['sitestoreproduct_graphshippingprice_width']);
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.graphshippingprice.color', $values ['sitestoreproduct_graphshippingprice_color']);
    }
  }

  public function orderStatisticsAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_statistic');
    $this->view->type = true;

    $tableObject = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $this->view->totalStores = $totalStoreCount = $tableObject->getStoresCount();

    $orderTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
    $adminAmountDetails = $orderTable->getAdminAmountDetails();
    $this->view->adminAmountDetails = $adminAmountDetails[0];
    $this->view->totalOrders = $orderTable->getStatusOrders();
//    $this->view->totalReceivedOrders = $orderTable->getStatusOrders(array('received_orders' => 1));
    $this->view->approvalPendingOrders = $orderTable->getStatusOrders(array('order_status' => 0));
    $this->view->paymentPendingOrders = $orderTable->getStatusOrders(array('order_status' => 1));
    $this->view->processingOrders = $orderTable->getStatusOrders(array('order_status' => 2));
    $this->view->onholdOrders = $orderTable->getStatusOrders(array('order_status' => 3));
    $this->view->fraudOrders = $orderTable->getStatusOrders(array('order_status' => 4));
    $this->view->completedOrders = $orderTable->getStatusOrders(array('order_status' => 5));
    $this->view->cancelOrders = $orderTable->getStatusOrders(array('order_status' => 6));

    $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;
    if (empty($currencySymbol))
      $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
  }
  
  public function widgetSettingsAction()
  {
    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
				->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_settings');    
    
    $this->view->navigationStoreWidget = Engine_Api::_()->getApi('menus', 'core')
				->getNavigation('sitestore_admin_main_settings', array(), 'sitestore_admin_main_global_widget');
        
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Settings_Widget();
    
    if( !$this->getRequest()->isPost() )
      return;
      
    //CHECK VALIDITY
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    
    $values = $form->getValues();
    
    // START WORK OF UPDATING THE WIDGET HEIGHT AND WIDTH
    $this->_updateWidget('sitestoreproduct_index_home', 'sitestoreproduct.sponsored-sitestoreproduct', array('height' => 'blockHeight', 'heightValue' => $values['store_product_home_ajax_based_product_carousel_height'], 'width' => 'blockWidth', 'widthValue' => $values['store_product_home_ajax_based_product_carousel_width']));
    
    $this->_updateWidget('sitestoreproduct_index_home', 'sitestoreproduct.recently-popular-random-sitestoreproduct', array('height' => 'columnHeight', 'heightValue' => $values['store_product_home_ajax_based_main_product_height'], 'width' => 'columnWidth', 'widthValue' => $values['store_product_home_ajax_based_main_product_width']));
    
    $this->_updateWidget('sitestoreproduct_index_index', 'sitestoreproduct.browse-products-sitestoreproduct', array('height' => 'columnHeight', 'heightValue' => $values['browse_products_height'], 'width' => 'columnWidth', 'widthValue' => $values['browse_products_width']));
    
    $this->_updateWidget('sitestoreproduct_index_categories', 'sitestoreproduct.categories-grid-view', array('height' => 'columnHeight', 'heightValue' => $values['category_home_height'], 'width' => 'columnWidth', 'widthValue' => $values['category_home_width']));
    
    $this->_updateWidget('sitestoreproduct_index_categories', 'sitestoreproduct.recently-popular-random-sitestoreproduct', array('height' => 'columnHeight', 'heightValue' => $values['categories_ajax_based_main_product_height'], 'width' => 'columnWidth', 'widthValue' => $values['categories_ajax_based_main_product_width']));
    
    // FOR CATEGORY PAGES
    $corePageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $corePages = $corePageTable->select()
                               ->from($corePageTable->info('name'), array('page_id', 'name'))
                               ->where('name  LIKE ? ', '%' . 'sitestoreproduct_index_category-home_category_' . '%')
                               ->query()->fetchAll();
    
    if( !empty($corePages) )
    {
      foreach($corePages as $page)
      {
        $this->_updateWidget($page['name'], 'sitestoreproduct.categories-grid-view', array('height' => 'columnHeight', 'heightValue' => $values['categories_height'], 'width' => 'columnWidth', 'widthValue' => $values['categories_width'], 'corePageId' => $page['page_id'], 'defaultWidgetNo' => 7, 'isCategory' => true));

        $this->_updateWidget($page['name'], 'sitestoreproduct.recently-popular-random-sitestoreproduct', array('height' => 'columnHeight', 'heightValue' => $values['categories_ajax_based_main_products_home_height'], 'width' => 'columnWidth', 'widthValue' => $values['categories_ajax_based_main_products_home_width'], 'corePageId' => $page['page_id'], 'defaultWidgetNo' => 8, 'isCategory' => true));
        
        $this->_updateWidget($page['name'], 'sitestoreproduct.sponsored-sitestoreproduct', array('height' => 'blockHeight', 'heightValue' => $values['most_rated_products_height'], 'width' => 'blockWidth', 'widthValue' => $values['most_rated_products_width'], 'corePageId' => $page['page_id'], 'defaultWidgetNo' => 9, 'isCategory' => true));
        
        $this->_updateWidget($page['name'], 'sitestoreproduct.sponsored-sitestoreproduct', array('height' => 'blockHeight', 'heightValue' => $values['most_viewed_products_height'], 'width' => 'blockWidth', 'widthValue' => $values['most_viewed_products_width'], 'corePageId' => $page['page_id'], 'defaultWidgetNo' => 10, 'isCategory' => true));
        
        $this->_updateWidget($page['name'], 'sitestoreproduct.sponsored-sitestoreproduct', array('height' => 'blockHeight', 'heightValue' => $values['most_liked_products_width'], 'width' => 'blockWidth', 'widthValue' => $values['most_liked_products_width'], 'corePageId' => $page['page_id'], 'defaultWidgetNo' => 11, 'isCategory' => true));
      }
    }
    
    $this->_updateWidget('sitestore_index_view', 'sitestoreproduct.store-profile-products', array('height' => 'columnHeight', 'heightValue' => $values['manage_products_height'], 'width' => 'columnWidth', 'widthValue' => $values['manage_products_width']));
    
    $this->_updateWidget('user_profile_index', 'sitestoreproduct.profile-sitestoreproduct', array('height' => 'columnHeight', 'heightValue' => $values['member_profile_height'], 'width' => 'columnWidth', 'widthValue' => $values['member_profile_width']));
    
    
    // WIDGETS HAVING ONLY WIDTH
    $this->_updateWidget('sitestoreproduct_wishlist_profile', 'sitestoreproduct.wishlist-profile-items', array('width' => 'itemWidth', 'widthValue' => $values['wishlist_profile_width']));    
    $this->_updateWidget('sitestoreproduct_index_pinboard', 'sitestoreproduct.pinboard-products-sitestoreproduct', array('width' => 'itemWidth', 'widthValue' => $values['pinboard_width']));    

      
  }
  
  protected function _updateWidget($pageName, $widget_name, $params)
  {
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    $contentTableName = $contentTable->info('name');
    $corePageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $corePageTableName = $corePageTable->info('name');
    
    if( isset($params['corePageId']) && !empty($params['corePageId']) )
      $corePageId = $params['corePageId'];
    else
    {
      $corePageId = $corePageTable->select()
                                  ->from($corePageTableName, array('page_id'))
                                  ->where('name =?', "$pageName")
                                  ->limit(1)
                                  ->query()->fetchColumn();
    }
    
    if( !empty($corePageId) )
    {
      $selectWidget = $contentTable->select()
                                   ->from($contentTableName, array('content_id', 'params'))
                                   ->where('page_id =?', $corePageId)
                                   ->where('name =?', $widget_name);
      $widgetArray = $contentTable->fetchAll($selectWidget);

      if( !empty($widgetArray) )
      {
        foreach ($widgetArray as $widget)
        {
          $widgetParams = $widget->params;
          
          if( isset($params['isCategory']) && !empty($params['isCategory']) )
          {
            if( isset($params['defaultWidgetNo']) && !empty($params['defaultWidgetNo']) && $params['defaultWidgetNo'] == $widgetParams['default_widget_no'] )
            {
              $widgetParams[$params['width']] = $params['widthValue'];
              $widgetParams[$params['height']] = $params['heightValue'];
            }
            else
              continue;
          }
          else
          {
            $widgetParams[$params['width']] = $params['widthValue'];

            if( isset($params['height']) && !empty($params['height']) )
              $widgetParams[$params['height']] = $params['heightValue'];
          }
          $widget->params = $widgetParams;
          $widget->save();
        }
      }
    }
  }
  
   	//ACTION FOR SET THE DEFAULT MAP CENTER POINT
  public function setDefaultMapCenterPoint($oldLocation, $newLocation) {
    if ($oldLocation !== $newLocation && $newLocation !== "World" && $newLocation !== "world") {
      $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $newLocation, 'module' => 'Stores / Marketplace - Ecommerce'));
      if (!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
        $latitude = $locationResults['latitude'];
        $longitude = $locationResults['longitude'];
      }

      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.map.latitude', $latitude);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestoreproduct.map.longitude', $longitude);
    }
  }
}
