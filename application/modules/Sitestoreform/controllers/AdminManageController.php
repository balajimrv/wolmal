<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreform_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGING FORMS
  public function indexAction() {
    
    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestoreform');    

    //CREATE NAVIGATION TABS
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitestoreform_admin_main', array(), 'sitestoreform_admin_main_manage');

    //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION  
    $this->view->formFilter = $formFilter = new Sitestoreform_Form_Admin_Manage_Filter();
    $store = $this->_getParam('page', 1);
    $table = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform');
    $table_name = $table->info('name');
    $sitestore_table = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $table_name1 = $sitestore_table->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($table_name)
                    ->joinInner($table_name1, "$table_name.store_id = $table_name1.store_id", array('title as sitestore_title', 'store_id as sitestore_id'));

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
                'order' => 'store_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'store_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
  }

  //ACTION FOR DISABLING THE FORM
  public function disableFormAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET STORE ID
    $store_id = $this->_getParam('id');

    //GET FORM DATA
    $formTable = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform');
    $formSelect = $formTable->select()->where('store_id = ?', $store_id);
    $formSelectData = $formTable->fetchRow($formSelect);

    //GET FORM ID AND OBJECT
    $this->view->form_id = $sitestoreform_id = $formSelectData->sitestoreform_id;
    $sitestoreform = Engine_Api::_()->getItem('sitestoreform', $sitestoreform_id);

    //SEND STATUS TO TPL
    $this->view->status = $sitestoreform->status;

    //SMOOTHBOX
    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {//NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    if (!$this->getRequest()->isPost())
      return;
    $db = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform')->getAdapter();
    $db->beginTransaction();
    try {
      if ($sitestoreform->status == 0) {
        $sitestoreform->status = 1;
      } else {
        $sitestoreform->status = 0;
      }

      $sitestoreform->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      throw $e;
    }
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
    ));
  }

}
?>