<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_AdminSettingsController extends Core_Controller_Action_Admin {

  //ACTINO FOR GLOBAL SETTINGS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestoreoffer');

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreoffer_admin_main', array(), 'sitestoreoffer_admin_main_settings');

    $this->view->form = $form = new Sitestoreoffer_Form_Admin_Global();

    if ($this->getRequest()->isPost()) {
      $sitestoreKeyVeri = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.lsettings', null);
      if (!empty($sitestoreKeyVeri)) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.lsettings', trim($sitestoreKeyVeri));
      }
//      if ($_POST['sitestoreoffer_lsettings']) {
//        $_POST['sitestoreoffer_lsettings'] = trim($_POST['sitestoreoffer_lsettings']);
//      }
    }
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      // It is only for installtion time use after it remove
      if (Engine_Api::_()->sitestore()->hasPackageEnable() && isset($values['include_in_package']) && !empty($values['include_in_package'])) {
        Engine_Api::_()->sitestore()->oninstallPackageEnableSubMOdules('sitestoreoffer');
      }
      // It is only for installtion time use after it remove

      foreach ($values as $key => $value) {
        if ($key != 'submit') {
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
      }
    }
  }

  //ACTION FOR WIDGET SETTINGS
  public function widgetAction() {

    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestoreoffer');

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreoffer_admin_main', array(), 'sitestoreoffer_admin_main_offer_tab');
    $this->view->tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitestoreoffer', 'type' => 'offers'));
    $this->view->isprivate  = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);
  }

  //ACTION FOR OFFER OF THE DAY
  public function manageDayItemsAction() {

    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestoreoffer');

    //TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreoffer_admin_main', array(), 'sitestoreoffer_admin_main_dayitems');
    
    $this->view->isprivate  = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorecoupon.isprivate', 0);

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitestoreoffer_Form_Admin_Manage_Filter();
    $store = $this->_getParam('store', 1);

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    $values = array_merge(array(
        'order' => 'start_date',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    $this->view->offerOfDaysList = $offerOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore')->getItemOfDayList($values, 'offer_id', 'sitestoreoffer_offer');
    $offerOfDay->setItemCountPerPage(50);
    $offerOfDay->setCurrentPageNumber($store);
  }

  //ACTION FOR ADDING OFFER OF THE DAY
  public function addOfferOfDayAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitestoreoffer_Form_Admin_ItemOfDayday();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setTitle('Add an Coupon of the Day')
            ->setDescription('Select a start date and end date below and the corresponding Coupon from the auto-suggest Coupon field. The selected Coupon will be displayed as "Coupon of the Day" for this duration and if more than one offers are found to be displayed in the same duration then they will be dispalyed randomly one at a time.');
    $form->getElement('title')->setLabel('Coupon Name');

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore');

        //FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $values["resource_id"])->where('resource_type = ?', 'sitestoreoffer_offer');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $values["resource_id"];
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
        $row->resource_type = 'sitestoreoffer_offer';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Coupon of the Day has been added successfully.'))
      ));
    }
  }

  // ACTION FOR CHANGE SETTINGS OF TABBED OFFER WIDZET TAB
  public function editTabAction() {

    $this->view->tabs = $tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitestoreoffer', 'type' => 'offers', 'enabled' => 1));
    //FORM GENERATION
    $this->view->form = $form = new Sitestoreoffer_Form_Admin_EditTab();
    $id = $this->_getParam('tab_id');

    $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
    //CHECK POST
    if (!$this->getRequest()->isPost()) {
      $values = $tab->toarray();
      $form->populate($values);
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $tab->setFromArray($values);
      $tab->save();
      $db->commit();
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Edit Tab Settings Sucessfully.'))
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR UPDATE ORDER  OF OFFERS WIDGTS TAB
  public function updateOrderAction() {
    //CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      $values = $_POST;
      try {
        foreach ($values['order'] as $key => $value) {
          $tab = Engine_Api::_()->getItem('seaocore_tab', (int) $value);
          if (!empty($tab)) {
            $tab->order = $key + 1;
            $tab->save();
          }
        }
        $db->commit();
        $this->_helper->redirector->gotoRoute(array('action' => 'widget'));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //ACTION FOR MAKE TAB ENABLE/DISABLE
  public function enabledAction() {
    $id = $this->_getParam('tab_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
    try {
      $tab->enabled = !$tab->enabled;
      $tab->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitestoreoffer/settings/widget');
  }

  //ACTION FOR OFFER SUGGESTION DROP-DOWN
  public function getOfferAction() {
    $title = $this->_getParam('text', null);
    $limit = $this->_getParam('limit', 40);
    $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storeTable->info('name');
    $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $allowName = $allowTable->info('name');
    $offerTable = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');
    $offerName = $offerTable->info('name');
    $data = array();
    $select = $offerTable->select()
            ->setIntegrityCheck(false)
            ->from($offerName)
            ->join($storeTableName, $storeTableName . '.store_id = ' . $offerName . '.store_id', array('title AS store_title', 'photo_id as store_photo_id'))
            ->join($allowName, $allowName . '.resource_id = ' . $storeTableName . '.store_id', array('resource_type', 'role'))
            ->where($allowName . '.resource_type = ?', 'sitestore_store')
            ->where($allowName . '.role = ?', 'registered')
            ->where($allowName . '.action = ?', 'view')
            ->where($offerName . '.title  LIKE ? ', '%' . $title . '%')
            ->limit($limit)
            ->order($offerName . '.creation_date DESC');
    $select = $select
            ->where($storeTableName . '.closed = ?', '0')
            ->where($storeTableName . '.approved = ?', '1')
            ->where($storeTableName . '.declined = ?', '0')
            ->where($storeTableName . '.draft = ?', '1');
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $select->where($storeTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $offers = $offerTable->fetchAll($select);

    foreach ($offers as $offer) {
      if ($offer->photo_id) {
        $content_photo = $this->view->itemPhoto($offer, 'thumb.normal');
      } else {
        $content_photo = "<img src='" . $this->view->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/offer_thumb.png' alt='' />";
      }
      $data[] = array(
          'id' => $offer->offer_id,
          'label' => $offer->title,
          'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR DELETE OFFER OF DAY ENTRY
  public function deleteOfferOfDayAction() {
    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getDbtable('itemofthedays', 'sitestore')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    $this->renderScript('admin-settings/delete.tpl');
  }

  //ACTION FOR MULTI DELETE OFFER ENTRIES
  public function multiDeleteOfferAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {

          $sitestoreitemofthedays = Engine_Api::_()->getItem('sitestore_itemofthedays', (int) $value);
          if (!empty($sitestoreitemofthedays)) {
            $sitestoreitemofthedays->delete();
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage-day-items'));
  }

}

?>