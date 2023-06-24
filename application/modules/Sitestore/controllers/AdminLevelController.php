<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLevelController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_AdminLevelController extends Core_Controller_Action_Admin {

	//ACTION FOR LEVEL SETTINGS
  public function indexAction() {
		//GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_level');

    //GET LEVEL ID
    if (null !== ($id = $this->_getParam('id'))) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

		//LEVEL AUTHORIZATION
    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }
    
		//GET LEVEL ID
    $id = $level->level_id;

    //FORM GENERATION
    $this->view->form = $form = new Sitestore_Form_Admin_Settings_Level(array(
                'public' => ( in_array($level->type, array('public')) ),
                'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
            ));

    $form->level_id->setValue($id);

    //POPULATE DATA    
    $this->view->isEnabledPackage = Engine_Api::_()->sitestore()->hasPackageEnable();
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');    
    $formValue = $permissionsTable->getAllowed('sitestore_store', $id, array_keys($form->getValues()));
    $allow_selling_previous = array_key_exists('allow_selling_products', $formValue) ? $formValue['allow_selling_products'] : 1;
    $form->populate($formValue);

    if (isset($formValue['profile'])) {
      if ($formValue['profile'] == 2) {
        $profileFields = Engine_Api::_()->sitestore()->getLevelProfileFields($id);
        $session = new Zend_Session_Namespace('profileFields');
        $session->profileFields = $profileFields;
      }
    }

    //FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
 
    // Validators for Sitestore
    $storeValues = $this->getRequest()->getPost();

//    if(isset($storeValues['create'])){
//      if ($storeValues['comission_handling'] == 0) 
//        $form->comission_rate->setValidators(array());
//       else 
//        $form->comission_fee->setValidators(array());
//    }
    
    //PROCESS
    $values = $form->getValues();
    $profileFields = array();
    if (isset($values['profile']) && $values['profile'] == 2) {
      foreach ($_POST as $key => $value) {
        if (@strstr($key, '_profilecheck_') != null && $value) {
          $tc = @explode("_profilecheck_", $key);
          $profileFields[] = "1_" . $tc[0] . "_" . $value;
        }
      }

      if (empty($profileFields)) {
        $session->profileFields = $profileFields;
        $error = Zend_Registry::get('Zend_Translate')->_('Please select atleast one profile field.');
        return $form->addError($error);
      }
    }

    $values['profilefields'] = serialize($profileFields);

//    if($values['allow_selling_products'] != $allow_selling_previous){
//      $this->allowSellingProducts($id, $values['allow_selling_products']);
//    }
    
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();
    try {
      include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
      
      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    if (isset($values['profile']) && $values['profile'] == 2) {
      $profileFields = Engine_Api::_()->sitestore()->getLevelProfileFields($id);    
      $session->profileFields = $profileFields;
    }
  }
  
//  protected function allowSellingProducts($id, $allow_selling){
//    
//    $userTable = Engine_Api::_()->getItemTable('user');
//    $userTableName = $userTable->info('name');
//    
//    $storeTable = Engine_Api::_()->getDbTable('stores', 'sitestore');
//    $storeTableName = $storeTable->info('name');
//    
//    $productTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
//    $productTableName = $productTable->info('name');
//    
//    $select = $productTable->select()
//            ->setIntegrityCheck(false)
//            ->from($productTableName)
//            ->join($storeTableName, "($storeTableName.store_id = $productTableName.store_id)", NULL)
//            ->join($userTableName, "($userTableName.user_id = $storeTableName.owner_id)", NULL)
//            ->where($userTableName . '.level_id = ?', $id);
//
//    $productsObj = $productTable->fetchAll($select);
//    foreach($productsObj as $product){
//      $productTable->update(array('allow_purchase' => $allow_selling), array('product_id = ?' => $product->product_id));
//    }
//  }
}

?>