<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_PackageController extends Core_Controller_Action_Standard {

  //ACTION FOR SHOW PACKAGES
  public function indexAction() {

    //USER VALIDATON
    if (!$this->_helper->requireUser()->isValid())
      return;

    //STORE CREATION PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'create')->isValid())
      return;
      
    if (!Engine_Api::_()->sitestore()->hasPackageEnable()) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'create'), 'sitestore_general', true);     
    }
    
    $this->view->store_id = $this->_getParam('store_id', null);
    $this->view->parent_id = $parent_id = $this->_getParam('parent_id', null);
    
    // NO SUB-STORE
    if( !empty($parent_id) )
      return $this->_forward('notfound', 'error', 'core');
      
    $is_package_view = Zend_Registry::isRegistered('sitestore_package_view') ? Zend_Registry::get('sitestore_package_view') : null;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    //Start Coupon plugin work.
		$couponEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitecoupon');
		if (!empty($couponEnabled)) {
			$modules_enabled = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'modules.enabled' ) ;
			if (!empty($modules_enabled)) {
				$this->view->modules_enabled = unserialize($modules_enabled) ;
			}
		}
		//End coupon plugin work.

    $packages_select = Engine_Api::_()->getItemtable('sitestore_package')->getPackagesSql(null);
    
    $paginator = Zend_Paginator::factory($packages_select);
    $getPaginatorCount = $paginator->getTotalItemCount();
    
    if (empty($is_package_view)) {
      $this->view->paginator = array();
    } else {
      $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('store'));
    }
    
    if( !empty($getPaginatorCount) && $getPaginatorCount == 1 ){
      $getPaginatorCurrentItems = $paginator->getCurrentItems();
      if( !empty($getPaginatorCurrentItems) && !empty($getPaginatorCurrentItems[0]) && !empty($getPaginatorCurrentItems[0]->package_id) )
        return $this->_helper->redirector->gotoRoute(array("action"=>"create" ,'id' => $getPaginatorCurrentItems[0]->package_id, 'parent_id' => $this->view->parent_id), 'sitestore_general', true);
    }
    

//     $packagesCountSelect = Engine_Api::_()->getItemtable('sitestore_package')->getPackagesSql(null);
//     $paginatorCountSelect = Zend_Paginator::factory($packagesCountSelect);
//     $totalItemCountSelect = $paginatorCountSelect->getTotalItemCount();
//     
//     if($this->view->paginator->getTotalItemCount() != $totalItemCountSelect) {
//       $this->view->showAllPackageMsg = 1;
//     }
    
  }

	//ACTION FOR PACKAGE DETAIL
  public function detailAction() {

    //USER VALIDATON
    if (!$this->_helper->requireUser()->isValid())
      return;

    //STORE CREATION PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'create')->isValid())
      return;

    $id = $this->_getParam('id');
    if (empty($id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->package = Engine_Api::_()->getItem('sitestore_package', $id);
  }

	//ACTION FOR PACKAGE UPDATION
  public function updatePackageAction() {

    //USER VALIDATON
    if (!$this->_helper->requireUser()->isValid())
      return;

		//PACKAGE ENABLE VALIDATION
    if (!Engine_Api::_()->sitestore()->hasPackageEnable()) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $this->view->sitestores_view_menu = 15;

		//GET STORE ID STORE OBJECT AND THEN CHECK VALIDATIONS
    $this->view->store_id = $store_id = $this->_getParam('store_id');
    if (empty($store_id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (empty($sitestore)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $this->view->package = Engine_Api::_()->getItem('sitestore_package', $sitestore->package_id);
    $table = Engine_Api::_()->getItemtable('sitestore_package');
    $packages_select = $table->getPackagesSql($sitestore->getOwner())
            ->where("update_list = ?", 1)
            ->where("enabled = ?", 1)
            ->where("package_id <> ?", $sitestore->package_id);
    $paginator = Zend_Paginator::factory($packages_select);

    $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('store'));

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');
    $this->view->is_ajax = $this->_getParam('is_ajax', '');
    
    //Start Coupon plugin work.
		$couponEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitecoupon');
		if (!empty($couponEnabled)) {
			$modules_enabled = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'modules.enabled' ) ;
			if (!empty($modules_enabled)) {
				$this->view->modules_enabled = unserialize($modules_enabled) ;
			}
		}
		//End coupon plugin work.
  }

	//ACTION FOR PACKAGE UPGRADE CONFIRMATION
  public function updateConfirmationAction() {

		//USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

		//PACKAGE ENABLE VALIDATION
    if (!Engine_Api::_()->sitestore()->hasPackageEnable()) {
      return $this->_forward('notfound', 'error', 'core');
    }

		//GET STORE ID STORE OBJECT AND THEN CHECK VALIDATIONS
    $this->view->store_id = $store_id = $this->_getParam('store_id');
    if (empty($store_id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (empty($sitestore)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $this->view->package_id = $this->_getParam('package_id');
    $package_chnage = Engine_Api::_()->getItem('sitestore_package', $this->view->package_id);
    if (empty($package_chnage) || !$package_chnage->enabled || (!empty($package_chnage->level_id) && !in_array($sitestore->getOwner()->level_id , explode(",", $package_chnage->level_id)))) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $getPackageAuth = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestore');

    if ($this->getRequest()->getPost()) {

      if (!empty($_POST['package_id'])) {
        $table = $sitestore->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
          $is_upgrade_package = true;
          
          //APPLIED CHECKS BECAUSE CANCEL SHOULD NOT BE CALLED IF ALREADY CANCELLED 
          if($sitestore->status == 'active')
            $sitestore->cancel($is_upgrade_package);
          
          $sitestore->package_id = $_POST['package_id'];
          $package = Engine_Api::_()->getItem('sitestore_package', $sitestore->package_id);

          $sitestore->featured = $package->featured;
          $sitestore->sponsored = $package->sponsored;
          $sitestore->pending = 1;
          $sitestore->expiration_date = new Zend_Db_Expr('NULL');
          $sitestore->status = 'initial';
          if (($package->isFree()) && !empty($getPackageAuth)) {
            $sitestore->approved = $package->approved;
          } else {
            $sitestore->approved = 0;
          }

          if (!empty($sitestore->approved)) {
            $sitestore->pending = 0;
            $expirationDate = $package->getExpirationDate();
            if (!empty($expirationDate))
              $sitestore->expiration_date = date('Y-m-d H:i:s', $expirationDate);
            else
              $sitestore->expiration_date = '2250-01-01 00:00:00';

            if (empty($sitestore->aprrove_date)) {
              $sitestore->aprrove_date = date('Y-m-d H:i:s');
              if (!empty($sitestore) && !empty($sitestore->draft) && empty($sitestore->pending)) {
                Engine_Api::_()->sitestore()->attachStoreActivity($sitestore);
              }
            }
          }
          $sitestore->save();
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
              'format' => 'smoothbox',
              'parentRedirect' => $this->view->url(array('action' => 'update-package', 'store_id' => $sitestore->store_id), 'sitestore_packages', true),
              'parentRedirectTime' => 15,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The package for your Store has been successfully changed.'))
      ));
    }
  }

	//ACTION FOR PACKAGE PAYMENT
  public function paymentAction() {

		//USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

		//GET STORE ID STORE OBJECT AND THEN CHECK VALIDATIONS
    $store_id = $_POST['store_id_session'];
    if (empty($store_id)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (empty($sitestore)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $getPackageAuth = Engine_Api::_()->sitestore()->getPackageAuthInfo('sitestore');
    $package = Engine_Api::_()->getItem('sitestore_package', $sitestore->package_id);

    if ((!$package->isFree()) && !empty($getPackageAuth)) {
      $session = new Zend_Session_Namespace('Payment_Sitestore');
      $session->store_id = $store_id;

      return $this->_helper->redirector->gotoRoute(array(), 'sitestore_payment', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitestore_general', true);
    }
  }
  
  //ACTION FOR PACKAGE CANCEL
  public function cancelAction(){
    if( !($package_id = $this->_getParam('package_id')) ||
        !($package = Engine_Api::_()->getItem('sitestore_package', $package_id)) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'package_id' => null));
    }

    $this->view->package_id = $package_id;
    $store_id = $this->_getParam('store_id');

    $this->view->form = $form = new Sitestore_Form_Packagecancel();

    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Try to cancel
    $this->view->form = null;
    try {
      Engine_Api::_()->getItem('sitestore_store', $store_id)->cancel();
      $this->view->status = true;
    } catch( Exception $e ) {
      $this->view->status = false;
      $this->view->error = $e->getMessage();
    }
  }
  
}
?>