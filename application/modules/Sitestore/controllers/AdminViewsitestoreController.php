<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminViewsitestoreController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_AdminViewsitestoreController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE STORES
  public function indexAction() {

    if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.task.updateexpiredstores') + 900) <= time()) {
      Engine_Api::_()->sitestore()->updateExpiredStores();
    }

    //TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_viewsitestore');

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitestore_Form_Admin_Manage_Filter();

    //GET STORE ID
    $store = $this->_getParam('page', 1);

    //MAKE QUERY
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');

    $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $tableStoreName = $tableStore->info('name');

    // There were default query of find out stores, now we join with sitestore orders table. If exist.
      $tableSitestorOrders = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
      $tableNameSitestorOrders = $tableSitestorOrders->info('name');

      $select = $tableStore->select()
              ->setIntegrityCheck(false)
              ->from($tableStoreName)
              ->joinLeft($tableUser, "$tableStoreName.owner_id = $tableUser.user_id", 'username')
              ->joinLeft($tableNameSitestorOrders, "$tableStoreName.store_id = $tableNameSitestorOrders.store_id", array("SUM($tableNameSitestorOrders.grand_total) as grand_total", "COUNT($tableNameSitestorOrders.order_id) as orders", "SUM($tableNameSitestorOrders.commission_value) as commission", "SUM($tableNameSitestorOrders.item_count) as sold_products"))
              ->group("$tableStoreName.store_id");

    $values = array();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }
    foreach ($values as $key => $value) {

      if (null == $value) {
        unset($values[$key]);
      }
    }

    //SEARCHING
    $this->view->owner = '';
    $this->view->title = '';
    $this->view->sponsored = '';
    $this->view->approved = '';
    $this->view->featured = '';
    $this->view->status = '';
    $this->view->storebrowse = '';
    $this->view->category_id = '';
    $this->view->subcategory_id = '';
    $this->view->subsubcategory_id = '';
    $this->view->package_id = '';

    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $packageTable = Engine_Api::_()->getDbtable('packages', 'sitestore');

      $packageselect = $packageTable->select()->from($packageTable->info("name"), array("package_id", "title"))->order("package_id DESC");
      $this->view->packageList = $packageTable->fetchAll($packageselect);
    }


    $values = array_merge(array(
        'order' => 'store_id',
        'order_direction' => 'DESC',
            ), $values);


    if (!empty($_POST['owner'])) {
      $user_name = $_POST['owner'];
    } elseif (!empty($_GET['owner'])) {
      $user_name = $_GET['owner'];
    } else {
      $user_name = '';
    }


    if (!empty($_POST['title'])) {
      $store_name = $_POST['title'];
    } elseif (!empty($_GET['title'])) {
      $store_name = $_GET['title'];
    } elseif ($this->_getParam('title', '')) {
      $store_name = $this->_getParam('title', '');
    } else {
      $store_name = '';
    }

    //SEARCHING
    $this->view->owner = $values['owner'] = $user_name;
    $this->view->title = $values['title'] = $store_name;

    if (!empty($store_name)) {
      $select->where($tableStoreName . '.title  LIKE ?', '%' . $store_name . '%');
    }

    if (!empty($user_name)) {
      $select->where($tableUser . '.displayname  LIKE ?', '%' . $user_name . '%');
    }

    if (isset($_POST['search'])) {

      if (!empty($_POST['sponsored'])) {
        $this->view->sponsored = $_POST['sponsored'];
        $_POST['sponsored']--;

        $select->where($tableStoreName . '.sponsored = ? ', $_POST['sponsored']);
      }
      if (!empty($_POST['store_status'])) {

        $this->view->store_status = $_POST['store_status'];
        switch ($this->view->store_status) {
          case 1:
            $select->where($tableStoreName . '.aprrove_date  IS NULL');
            break;
          case 2:
            $select->where($tableStoreName . '.approved = ? ', 1);
            break;
          case 3:
            $select->where($tableStoreName . '.aprrove_date  IS NOT NULL');
            $select->where($tableStoreName . '.approved = ? ', 0);
            break;
          case 4:
            $select->where($tableStoreName . '.declined  = ? ', 1);
            break;
        }
      }
      if (!empty($_POST['featured'])) {
        $this->view->featured = $_POST['featured'];
        $_POST['featured']--;
        $select->where($tableStoreName . '.featured = ? ', $_POST['featured']);
      }
      if (!empty($_POST['status'])) {
        $this->view->status = $_POST['status'];
        $_POST['status']--;
        $select->where($tableStoreName . '.closed = ? ', $_POST['status']);
      }

      if (!empty($_POST['package_id'])) {
        $this->view->package_id = $_POST['package_id'];
        $select->where($tableStoreName . '.package_id = ? ', $_POST['package_id']);
      }
      if (!empty($_POST['storebrowse'])) {
        $this->view->storebrowse = $_POST['storebrowse'];
        $_POST['storebrowse']--;
        if ($_POST['storebrowse'] == 0) {
          $select->order($tableStoreName . '.view_count DESC');
        } elseif ($_POST['storebrowse'] == 1) {
          $select->order($tableStoreName . '.creation_date DESC');
        } elseif ($_POST['storebrowse'] == 2) {
          $select->order($tableStoreName . '.comment_count DESC');
        } elseif ($_POST['storebrowse'] == 3) {
          $select->order($tableStoreName . '.like_count DESC');
        }
      }

      if (!empty($_POST['storebrowse'])) {
        $this->view->storebrowse = $_POST['storebrowse'];
        $_POST['storebrowse']--;
        if ($_POST['storebrowse'] == 0) {
          $select->order('grand_total DESC');
        } elseif ($_POST['storebrowse'] == 1) {
          $select->order('orders DESC');
        } elseif ($_POST['storebrowse'] == 2) {
          $select->order('commission DESC');
        } elseif ($_POST['storebrowse'] == 3) {
          $select->order('sold_products DESC');
        }
      }

      if (!empty($_POST['category_id']) && empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
        $this->view->category_id = $_POST['category_id'];
        $select->where($tableStoreName . '.category_id = ? ', $_POST['category_id']);
      } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
        $this->view->category_id = $_POST['category_id'];
        $subcategory_id = $this->view->subcategory_id = $_POST['subcategory_id'];
        $table = Engine_Api::_()->getDbtable('categories', 'sitestore');
        $categoriesName = $table->info('name');
        $selectcategory = $table->select()->from($categoriesName, 'category_name')
                ->where("(category_id = $subcategory_id)");
        $row = $table->fetchRow($selectcategory);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $row->category_name;
        }

        $select->where($tableStoreName . '.category_id = ? ', $_POST['category_id'])
                ->where($tableStoreName . '.subcategory_id = ? ', $_POST['subcategory_id']);
      } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && !empty($_POST['subsubcategory_id'])) {

        $this->view->category_id = $_POST['category_id'];
        $subcategory_id = $this->view->subcategory_id = $_POST['subcategory_id'];
        $subsubcategory_id = $this->view->subsubcategory_id = $_POST['subsubcategory_id'];

        $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($subcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $row->category_name;
        }
        $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($subsubcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subsubcategory_name = $row->category_name;
        }
        $select->where($tableStoreName . '.category_id = ? ', $_POST['category_id'])
                ->where($tableStoreName . '.subcategory_id = ? ', $_POST['subcategory_id'])
                ->where($tableStoreName . '.subsubcategory_id = ? ', $_POST['subsubcategory_id']);
        ;
      }
    }

    $this->view->formValues = array_filter($values);
    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'store_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPageNumber($store);
  }

  //VIEW STORE DETAIL
  public function detailAction() {

    $id = $this->_getParam('id');

    $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $tableStoreName = $tableStore->info('name');

    $select = $tableStore->select()
            ->setIntegrityCheck(false)
            ->from($tableStoreName)
            ->where($tableStoreName . '.store_id = ?', $id)
            ->limit(1);
    $this->view->sitestoreDetail = $detail = $tableStore->fetchRow($select);

    $this->view->manageAdminEnabled = $manageAdminEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1);
    if (!empty($manageAdminEnabled)) {
      $manageadminTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
      $manageadminTableName = $manageadminTable->info('name');
      $select = $manageadminTable->select()
              ->from($manageadminTableName, array('COUNT(*) AS count'))
              ->where('store_id = ?', $id);
      $rows = $tableStore->fetchAll($select)->toArray();
      $this->view->admin_total = $rows[0]['count'];
    }

    $this->view->category_id = $category_id = $detail['category_id'];
    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($category_id);
    if (!empty($row->category_name)) {
      $this->view->category_name = $row->category_name;
    }
    $this->view->subcategory_id = $subcategory_id = $detail['subcategory_id'];
    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($subcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subcategory_name = $row->category_name;
    }
    $this->view->subsubcategory_id = $subsubcategory_id = $detail['subsubcategory_id'];
    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($subsubcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subsubcategory_name = $row->category_name;
    }
    //SITESTORE-REVIEW PLUGIN IS INSTALLED OR NOT
    $this->view->isEnabledSitestorereview = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
  }

  //ACTION FOR MULTI-DELETE OF STORES
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {

      $values = $this->getRequest()->getPost();

      foreach ($values as $key => $value) {

        if ($key == 'delete_' . $value) {

          //DELETE SITESTORES FROM DATABASE
          $store_id = (int) $value;

          //START SUB STORE WORK
//          $getSubStoreids = Engine_Api::_()->getDbtable('stores', 'sitestore')->getsubStoreids($store_id);
//          foreach ($getSubStoreids as $getSubStoreid) {
//            Engine_Api::_()->sitestore()->onStoreDelete($getSubStoreid['store_id']);
//          }
          //END SUB STORE WORK

          Engine_Api::_()->sitestore()->onStoreDelete($store_id);
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  //ACTION FOR STORE EDIT
  public function editAction() {

    //TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_viewsitestore');

    //GET STORE ID AND STORE OBJECT
    $store_id = $this->_getParam('id');
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //FORM GENERATION
    $this->view->form = $form = new Sitestore_Form_Admin_Manage_Edit();

    if (!empty($sitestore->declined)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $status_storeOption = array();
    $approved = $sitestore->approved;
    if (empty($sitestore->aprrove_date) && empty($approved)) {
      $status_storeOption["0"] = "Approval Pending";
      $status_storeOption["1"] = "Approved Store";
      $status_storeOption["2"] = "Declined Store";
    } else {
      $status_storeOption["1"] = "Approved";
      $status_storeOption["0"] = "Dis-Approved";
    }
    $form->getElement("status_store")->setMultiOptions($status_storeOption);

    if (!$this->getRequest()->isPost()) {

      $form->getElement("closed")->setValue($sitestore->closed);
      $form->getElement("status_store")->setValue($sitestore->approved);
      $form->getElement("featured")->setValue($sitestore->featured);
      $form->getElement("sponsored")->setValue($sitestore->sponsored);
      $title = "<a href='" . $this->view->url(array('store_url' => $sitestore->store_url), 'sitestore_entry_view') . "'  target='_blank'>" . $sitestore->title . "</a>";
      $form->title_dummy->setDescription($title);
      if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
        $form->package_title->setDescription("<a href='" . $this->view->url(array('route' => 'admin_default', 'module' => 'sitestore', 'controller' => 'package', 'action' => 'packge-detail', 'id' => $sitestore->package_id), 'admin_default') . "'  class ='smoothbox'>" . ucfirst($sitestore->getPackage()->title) . "</a>");

        $package = $sitestore->getPackage();
        if ($package->isFree()) {

          $form->getElement("status")->setMultiOptions(array("free" => "NA (Free)"));
          $form->getElement("status")->setValue("free");
          $form->getElement("status")->setAttribs(array('disable' => true));
        } else {
          $form->getElement("status")->setValue($sitestore->status);
        }
      }
    } elseif ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //PROCESS
        $values = $form->getValues();
        
        if(!empty ($values) && isset ($values['toggle_products_status'])){
          if(isset ($values['toggle_products_status']) && $values['toggle_products_status'] == 2){
            Engine_Api::_()->getDbtable('stores', 'sitestore')->toggleStoreProductsStatus($store_id, 1);
          }elseif(isset ($values['toggle_products_status']) && $values['toggle_products_status'] == 3){
            Engine_Api::_()->getDbtable('stores', 'sitestore')->toggleStoreProductsStatus($store_id, 0);
          }
        }
        
        if ($values['status_store'] == 2) {
          $values['declined'] = 1;
        } else {
          $approved = $values['status_store'];
        }
        $sitestore->setFromArray($values);
        if (!empty($sitestore->declined)) {
          Engine_Api::_()->sitestore()->sendMail("DECLINED", $sitestore->store_id);
        }
        $sitestore->save();
        $db->commit();
        if ($approved != $sitestore->approved) {

          return $this->_helper->redirector->gotoRoute(array('module' => 'sitestore', 'controller' => 'admin', 'action' => 'approved', "id" => $store_id), "default", true);
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

}

?>