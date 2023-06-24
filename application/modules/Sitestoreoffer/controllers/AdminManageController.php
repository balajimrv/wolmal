<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGING THE OFFERS
  public function indexAction() {
    
  //GET NAVIGATION
  $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestoreoffer');    

    //CREATE NAVIGATION TABS
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreoffer_admin_main', array(), 'sitestoreoffer_admin_main_manage');

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitestoreoffer_Form_Admin_Manage_Filter();

    //FETCH OFFER DATAS
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');
    $tableSitestore = Engine_Api::_()->getItemTable('sitestore_store')->info('name');
    $table = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer');
    $rName = $table->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName)
                    ->joinLeft($tableUser, "$rName.owner_id = $tableUser.user_id", 'username')
                    ->joinLeft($tableSitestore, "$rName.store_id = $tableSitestore.store_id", 'title AS sitestore_title');
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }
    if (isset($_POST['search'])) {
      if (!empty($_POST['owner'])) {
        $this->view->owner = $_POST['owner'];
        $select->where($tableUser . '.username  LIKE ?', '%' . $_POST['owner'] . '%');
      }
      if (!empty($_POST['title'])) {
        $this->view->title = $_POST['title'];
        $select->where($rName . '.title  LIKE ?', '%' . $_POST['title'] . '%');
      }
      if (!empty($_POST['sitestore_title'])) {
        $this->view->sitestore_title = $_POST['sitestore_title'];
        $select->where($tableSitestore . '.title  LIKE ?', '%' . $_POST['sitestore_title'] . '%');
      }
      if (!empty($_POST['hotoffer'])) {
        $this->view->hotoffer = $_POST['hotoffer'];
        $_POST['hotoffer']--;
        $select->where($rName . '.hotoffer = ? ', $_POST['hotoffer']);
      }
      
      if (!empty($_POST['coupon_code'])) {
        $this->view->coupon_code = $_POST['coupon_code'];
        $select->where($rName . '.coupon_code  LIKE ?', '%' . $_POST['coupon_code'] . '%');
      }
    }
    $values = array_merge(array(
                'order' => 'offer_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'offer_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
  }

  //ACTION FOR MULTI DELETE OFFERS
  public function multiDeleteAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          //DELETE OFFERS FROM DATABASE AND SCRIBD
          $offer_id = (int) $value;
					Engine_Api::_()->sitestoreoffer()->deleteContent($offer_id);
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

}
?>