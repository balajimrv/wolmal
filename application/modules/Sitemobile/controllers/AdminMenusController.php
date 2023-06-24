<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminMenusController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_AdminMenusController extends Core_Controller_Action_Admin {

  protected $_menus;
  protected $_enabledModuleNames;

  public function init() {
    // Get list of menus
    $menusTable = Engine_Api::_()->getDbtable('menus', 'sitemobile');
    $menusSelect = $menusTable->select();
    $this->view->menus = $this->_menus = $menusTable->fetchAll($menusSelect);

    $this->_enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'sitemobile')->getEnabledModuleNames();
  }

  public function indexAction() {

    //GET NAVIGATIONS
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitemobile_admin_main', array(), 'sitemobile_admin_main_menus');


    $this->view->name = $name = $this->_getParam('name', 'core_main');

    // Get list of menus
    $menus = $this->_menus;

    // Check if selected menu is in list
    $selectedMenu = $menus->getRowMatching('name', $name);
    if (null === $selectedMenu) {
      throw new Core_Model_Exception('Invalid menu name');
    }
    $this->view->selectedMenu = $selectedMenu;

    // Make select options
    $menuList = array();
    foreach ($menus as $menu) {
      $menuList[$menu->name] = $this->view->translate($menu->title);
    }
    $this->view->menuList = $menuList;

    include_once APPLICATION_PATH . "/application/modules/Sitemobile/controllers/license/license2.php";
  }

  public function createAction() {

    $this->view->name = $name = $this->_getParam('name');
    $this->view->addType = $addType = $this->_getParam('addType');
    // Get list of menus
    $menus = $this->_menus;

    // Check if selected menu is in list
    $selectedMenu = $menus->getRowMatching('name', $name);
    if (null === $selectedMenu) {
      throw new Core_Model_Exception('Invalid menu name');
    }
    $this->view->selectedMenu = $selectedMenu;

    // Get form
    $this->view->form = $form = new Sitemobile_Form_Admin_Menu_ItemCreate(array('addType' => $addType, 'menuName' => $name));

    // Check stuff
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Save
    $values = $form->getValues();
    $label = $values['label'];
    unset($values['label']);
    if (isset($values['data_rel'])) {
      $values['data-rel'] = $values['data_rel'];
      unset($values['data_rel']);
    }


    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'sitemobile');

    $db = $menuItemsTable->getAdapter();
    $db->beginTransaction();

    try {

      $menuItem = $menuItemsTable->createRow();
      $menuItem->label = $label;
      $menuItem->menu = $name;
      $menuItem->module = 'sitemobile'; // Need to do this to prevent it from being hidden
      $menuItem->plugin = NULL;
      $menuItem->submenu = '';
      $menuItem->custom = 1;
      $menuItem->enable_mobile = $values['enable_mobile'];
      $menuItem->enable_tablet = $values['enable_tablet'];
      $menuItem->custom = 1;
      if (isset($menuItem->enable_mobile_app) && isset($values['enable_mobile_app'])) {
        $menuItem->enable_mobile_app = $values['enable_mobile_app'];
        unset($values['enable_mobile_app']);
      }
      if (isset($menuItem->enable_tablet_app) && isset($values['enable_tablet_app'])) {
        $menuItem->enable_tablet_app = $values['enable_tablet_app'];
        unset($values['enable_tablet_app']);
      }

      unset($values['enable_mobile']);
      unset($values['enable_tablet']);
      $menuItem->params = $values;
      $menuItem->save();

      $menuItem->name = 'custom_' . sprintf('%d', $menuItem->id);
      $menuItem->save();

      $this->view->status = true;
      $this->view->form = null;

      $this->view->error = false;

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = true;
    }

    $this->_forward('success', 'utility', 'core', array(
        'parentRefresh' => true,
        'messages' => "Your $addType have been successfully created.",
    ));
  }

  public function editAction() {

    $this->view->name = $name = $this->_getParam('name');
    $this->view->addType = $addType = $this->_getParam('addType');
    // Get menu item
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'sitemobile');

    $db = $menuItemsTable->getAdapter();
    $db->beginTransaction();

    try {
      $menuItemsSelect = $menuItemsTable->select()
              ->where('name = ?', $name);
      if (!empty($this->_enabledModuleNames)) {
        $menuItemsSelect->where('module IN(?)', $this->_enabledModuleNames);
      }
      $this->view->menuItem = $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);

      if (!$menuItem) {
        throw new Core_Model_Exception('missing menu item');
      }

      // Get form
      $this->view->form = $form = new Sitemobile_Form_Admin_Menu_ItemEdit(array('addType' => $addType, 'menuName' => $menuItem->menu));

      // Make safe
      $menuItemData = $menuItem->toArray();
      if (isset($menuItemData['params']) && is_array($menuItemData['params'])) {
        $menuItemData = array_merge($menuItemData, $menuItemData['params']);
      }
      if (!$menuItem->custom && (!isset($menuItemData['uri']) || $menuItemData['uri'] == 'Separator')) {
        $form->removeElement('uri');
      }
      unset($menuItemData['params']);
      if (isset($menuItemData['data-rel'])) {
        $menuItemData['data_rel'] = $menuItemData['data-rel'];
        unset($menuItemData['data-rel']);
      }

      // Check stuff
      if (!$this->getRequest()->isPost()) {
        $form->populate($menuItemData);
        return;
      }
      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

      // Save
      $values = $form->getValues();

      $menuItem->label = $values['label'];
      //$menuItem->enabled = !empty($values['enabled']);
      $menuItem->enable_mobile = !empty($values['enable_mobile']);
      $menuItem->enable_tablet = !empty($values['enable_tablet']);
      unset($values['label']);
      unset($values['enable_mobile']);
      unset($values['enable_tablet']);

      if (isset($menuItem->enable_mobile_app) && isset($values['enable_mobile_app'])) {
        $menuItem->enable_mobile_app = $values['enable_mobile_app'];
        unset($values['enable_mobile_app']);
      }

      if (isset($menuItem->enable_tablet_app) && isset($values['enable_tablet_app'])) {
        $menuItem->enable_tablet_app = $values['enable_tablet_app'];
        unset($values['enable_tablet_app']);
      }

      if ($menuItem->custom || (isset($menuItemData['uri']) && $menuItemData['uri'] != 'Separator')) {
        $menuItem->params = $values;
      } elseif (isset($values['icon'])) {
        if (empty($menuItem->params))
          $menuItem->params = array();
        $menuItem->params = array_merge($menuItem->params, array('icon' => $values['icon']));
      }

      if (!empty($values['data_rel'])) {
        $menuItem->params = array_merge($menuItem->params, array('data-rel' => $values['data_rel']));
      } else if (isset($menuItem->params['data-rel'])) {
        // Remove the target
        $tempParams = array();
        foreach ($menuItem->params as $key => $item) {
          if ($key != 'data-rel') {
            $tempParams[$key] = $item;
          }
        }
        $menuItem->params = $tempParams;
      }
//      if (isset($values['isseparator']) && $values['isseparator']) {
//        $menuItem->params = array('uri' => 'Separator', 'isseparator' => 'true');
//      }
      $menuItem->save();

      $this->view->status = true;
      $this->view->form = null;

      $this->view->error = false;

      $db->commit();

      $this->_forward('success', 'utility', 'core', array(
          'parentRefresh' => true,
          'smoothboxClose' => true,
          'messages' => "Your changes have been saved successfully.",
      ));
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = true;
    }
  }

  public function deleteAction() {

    $this->view->name = $name = $this->_getParam('name');
    $this->view->addType = $addType = $this->_getParam('addType');
    // Get menu item
    $menuItemsTable = Engine_Api::_()->getDbtable('menuItems', 'sitemobile');
    $db = $menuItemsTable->getAdapter();
    $db->beginTransaction();

    try {
      $menuItemsSelect = $menuItemsTable->select()
              ->where('name = ?', $name)
              ->order('order ASC');
      if (!empty($this->_enabledModuleNames)) {
        $menuItemsSelect->where('module IN(?)', $this->_enabledModuleNames);
      }
      $this->view->menuItem = $menuItem = $menuItemsTable->fetchRow($menuItemsSelect);

      if (!$menuItem || !$menuItem->custom) {
        throw new Core_Model_Exception('missing menu item');
      }

      // Get form
      $this->view->form = $form = new Sitemobile_Form_Admin_Menu_ItemDelete(array('addType' => $addType));

      // Check stuff
      if (!$this->getRequest()->isPost()) {
        return;
      }
      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

      $menuItem->delete();

      $this->view->form = null;
      $this->view->status = true;
      $this->view->error = false;
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = true;
    }
  }

  public function orderAction() {
    //TO RE-ARRANGE ORDER
    if (!$this->getRequest()->isPost()) {
      return;
    }

    $table = Engine_Api::_()->getDbtable('menuItems', 'sitemobile');
    $menuitems = $table->fetchAll($table->select()->where('menu = ?', $this->getRequest()->getParam('menu')));
    foreach ($menuitems as $menuitem) {
      $order = $this->getRequest()->getParam('admin_menus_item_' . $menuitem->name);
      if (!$order) {
        $order = 999;
      }
      $menuitem->order = $order;
      $menuitem->save();
    }
    return;
  }

  //TO DISABLE/ENABLE ANY MODULE.
  public function enableMobileAction() {

    //Get params enabled & name to identify the module and its corresponding disable or enable action.
    $this->view->enable_mobile = $enable_mobile = $this->_getParam('enable_mobile');
    $this->view->name = $moduleName = $this->_getParam('name');
    $menusTable = Engine_Api::_()->getDbtable('menuItems', 'sitemobile');
    $menusTable->update(array(
        'enable_mobile' => $enable_mobile,
            ), array(
        'name = ?' => $moduleName
    ));
    $this->_redirect('admin/sitemobile/menus');
  }

  //TO DISABLE/ENABLE ANY MODULE.
  public function enableMobileAppAction() {

    //Get params enabled & name to identify the module and its corresponding disable or enable action.
    $this->view->enable_mobile_app = $enable_mobile_app = $this->_getParam('enable_mobile_app');
    $this->view->name = $moduleName = $this->_getParam('name');
    $menusTable = Engine_Api::_()->getDbtable('menuItems', 'sitemobile');
    $menusTable->update(array(
        'enable_mobile_app' => $enable_mobile_app,
            ), array(
        'name = ?' => $moduleName
    ));
    $this->_redirect('admin/sitemobile/menus');
  }

  //TO DISABLE/ENABLE ANY MODULE.
  public function enableTabletAction() {

    //Get params enabled & name to identify the module and its corresponding disable or enable action.
    $this->view->enable_tablet = $enable_tablet = $this->_getParam('enable_tablet');
    $this->view->name = $moduleName = $this->_getParam('name');
    $menusTable = Engine_Api::_()->getDbtable('menuItems', 'sitemobile');
    $menusTable->update(array(
        'enable_tablet' => $enable_tablet,
            ), array(
        'name = ?' => $moduleName
    ));
    $this->_redirect('admin/sitemobile/menus');
  }

  //TO DISABLE/ENABLE ANY MODULE.
  public function enableTabletAppAction() {

    //Get params enabled & name to identify the module and its corresponding disable or enable action.
    $this->view->enable_tablet_app = $enable_tablet_app = $this->_getParam('enable_tablet_app');
    $this->view->name = $moduleName = $this->_getParam('name');
    $menusTable = Engine_Api::_()->getDbtable('menuItems', 'sitemobile');
    $menusTable->update(array(
        'enable_tablet_app' => $enable_tablet_app,
            ), array(
        'name = ?' => $moduleName
    ));
    $this->_redirect('admin/sitemobile/menus');
  }

}