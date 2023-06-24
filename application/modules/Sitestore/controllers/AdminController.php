<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_AdminController extends Core_Controller_Action_Admin {

  //ACTION FOR MAKE THE SITESTORE FEATURED/UNFEATURED
  public function featuredAction() {

    $store_id = $this->_getParam('id');
    if (!empty($store_id)) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      $sitestore->featured = !$sitestore->featured;
      $sitestore->save();
    }
    $this->_redirect('admin/sitestore/viewsitestore');
  }

  //ACTION FOR MAKE THE SITESTORE OPEN/CLOSED
  public function opencloseAction() {

    $store_id = $this->_getParam('id');
    if (!empty($store_id)) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      $sitestore->closed = !$sitestore->closed;
      $sitestore->save();
    }
    $this->_redirect('admin/sitestore/viewsitestore');
  }

  //ACTION FOR MAKE SPONSORED /UNSPONSORED
  public function sponsoredAction() {

    $store_id = $this->_getParam('id');
    if (!empty($store_id)) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      $sitestore->sponsored = !$sitestore->sponsored;
      $sitestore->save();
    }
    $this->_redirect('admin/sitestore/viewsitestore');
  }

  //ACTION FOR MAKE SITESTORE APPROVE/DIS-APPROVE
  public function approvedAction() {

    global $sitestore_is_auth;
    $store_id = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $email = array();
    try {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      if (!empty($sitestore_is_auth)) {
        $sitestore->approved = !$sitestore->approved;
      }

      if (!empty($sitestore->approved)) {    
        
        if (!empty($sitestore->pending)) {
          $sendActiveMail = 1;
          $sitestore->pending = 0;
        }

        if (empty($sitestore->aprrove_date)) {
          $sitestore->aprrove_date = date('Y-m-d H:i:s');
        }

        $diff_days = 0;
        $package = $sitestore->getPackage();
        if (($sitestore->expiration_date !== '2250-01-01 00:00:00' && !empty($sitestore->expiration_date) && $sitestore->expiration_date !== '0000-00-00 00:00:00') && date('Y-m-d', strtotime($sitestore->expiration_date)) > date('Y-m-d')) {
          $diff_days = round((strtotime($sitestore->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
        }


        if (($diff_days <= 0 && $sitestore->expiration_date !== '2250-01-01 00:00:00') || empty($sitestore->expiration_date) || $sitestore->expiration_date == '0000-00-00 00:00:00') {
          if (!$package->isFree()) {
            if ($sitestore->status != "active") {
              $relDate = new Zend_Date(time());
              $relDate->add((int) 1, Zend_Date::DAY);
              $sitestore->expiration_date = date('Y-m-d H:i:s', $relDate->toValue());
            } else {

              $expirationDate = $package->getExpirationDate();
              if (!empty($expirationDate))
                $sitestore->expiration_date = date('Y-m-d H:i:s', $expirationDate);
              else
                $sitestore->expiration_date = '2250-01-01 00:00:00';
            }
          }else {

            $expirationDate = $package->getExpirationDate();
            if (!empty($expirationDate))
              $sitestore->expiration_date = date('Y-m-d H:i:s', $expirationDate);
            else
              $sitestore->expiration_date = '2250-01-01 00:00:00';
          }
        }
        if ($sendActiveMail) {
          Engine_Api::_()->sitestore()->sendMail("ACTIVE", $sitestore->store_id);
          if (!empty($sitestore) && !empty($sitestore->draft) && empty($sitestore->pending)) {
            Engine_Api::_()->sitestore()->attachStoreActivity($sitestore);
          }
        } else {
          Engine_Api::_()->sitestore()->sendMail("APPROVED", $sitestore->store_id);
        }
      } else {             
        Engine_Api::_()->sitestore()->sendMail("DISAPPROVED", $sitestore->store_id);
      }
      $sitestore->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitestore/viewsitestore');
  }

  //ACTION FOR MAKE SITESTORE APPROVE/DIS-APPROVE
  public function renewAction() {

    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $store_id = $this->_getParam('id');
      if ($this->getRequest()->isPost()) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
          $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
          if (!empty($sitestore->approved)) {
            $package = $sitestore->getPackage();
            if ($sitestore->expiration_date !== '2250-01-01 00:00:00') {

              $expirationDate = $package->getExpirationDate();
              $expiration = $package->getExpirationDate();

              $diff_days = 0;
              if (!empty($sitestore->expiration_date) && $sitestore->expiration_date !== '0000-00-00 00:00:00') {
                $diff_days = round((strtotime($sitestore->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
              }
              if ($expiration) {
                $date = date('Y-m-d H:i:s', $expiration);

                if ($diff_days >= 1) {

                  $diff_days_expiry = round((strtotime($date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                  $incrmnt_date = date('d', time()) + $diff_days_expiry + $diff_days;
                  $incrmnt_date = date('Y-m-d H:i:s', mktime(date("H"), date("i"), date("s"), date("m"), $incrmnt_date));
                } else {
                  $incrmnt_date = $date;
                }

                $sitestore->expiration_date = $incrmnt_date;
              } else {
                $sitestore->expiration_date = '2250-01-01 00:00:00';
              }
            }
            if ($package->isFree())
              $sitestore->status = "initial";
            else
              $sitestore->status = "active";
          }
          $sitestore->search = 1;
          $sitestore->save();
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }

        $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
        ));
      }
    }
    $this->renderScript('admin/renew.tpl');
  }

  //ACTION FOR DELETE THE SITESTORE
  public function deleteAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->store_id = $store_id = $this->_getParam('id');

    if ($this->getRequest()->isPost()) {
    
      //START SUB STORE WORK
//			$getSubStoreids = Engine_Api::_()->getDbtable('stores', 'sitestore')->getsubStoreids($store_id);
//			foreach($getSubStoreids as $getSubStoreid) {
//				Engine_Api::_()->sitestore()->onStoreDelete($getSubStoreid['store_id']);
//			}
			//END SUB STORE WORK

      Engine_Api::_()->sitestore()->onStoreDelete($store_id);
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    $this->renderScript('admin/delete.tpl');
  }

  //ACTION FOR CHANGE THE OWNER OF THE STORE
  public function changeOwnerAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET STORE ID
    $this->view->store_id = $store_id = $this->_getParam('id');

    //FORM
    $form = $this->view->form = new Sitestore_Form_Admin_Changeowner();

    //SET ACTION
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //OLD OWNER ID
    $oldownerid = $sitestore->owner_id;

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
      $values = $form->getValues();

      //GET USER ID WHICH IS NOW NEW USER
      $changeuserid = $values['user_id'];

      //CHANGE USER TABLE
      $changed_user = Engine_Api::_()->getItem('user', $changeuserid);

      //OWNER USER TABLE
      $user = Engine_Api::_()->getItem('user', $sitestore->owner_id);

      //STORE URL
      $store_url = Engine_Api::_()->sitestore()->getStoreUrl($sitestore->store_id);

      //GET STORE TITLE
      $storetitle = $sitestore->title;

      //STORE OBJECT LINK
      $storeobjectlink = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view');

      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        
        $productTable = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
        $productIds = $productTable->select()
                ->from($productTable->info('name'), 'product_id')
                ->where('store_id = ?', $store_id)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
        foreach($productIds as $productId) {
          $params = array();
          $params['changeuserid'] = $changeuserid;
          $params['product_id'] = $productId;
          Engine_Api::_()->sitestoreproduct()->changeOwner($params);            
        }     
        
        //UPDATE STORE TABLE
        Engine_Api::_()->getDbtable('stores', 'sitestore')->update(array('owner_id' => $changeuserid), array('store_id = ?' => $store_id));

        //GET STORE URL
        $store_baseurl = 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true);

        //MAKING STORE TITLE LINK
        $store_title_link = '<a href="' . $store_baseurl . '"  >' . $storetitle . ' </a>';

        //GET ADMIN EMAIL
        $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;

        //EMAIL THAT GOES TO OLD OWNER
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'SITESTORE_CHANGEOWNER_EMAIL', array(

            'store_title' => $storetitle,
            'store_title_with_link' => $store_title_link,
            'object_link' => $store_baseurl,
            'site_contact_us_link' => 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/contact',
            'email' => $email,
            'queue' => true

        ));

        //EMAIL THAT GOES TO NEW OWNER
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($changed_user->email, 'SITESTORE_BECOMEOWNER_EMAIL', array(
            'store_title' => $storetitle,
            'store_title_with_link' => $store_title_link,
            'object_link' => $store_baseurl,
            'site_contact_us_link' => 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/contact',
            'email' => $email,
            'queue' => true

        ));


		    //START FOR INRAGRATION WORK WITH OTHER PLUGIN. DELETE ACCORDING TO STORE ID.
				$sitestoreintegrationEnabled = Engine_Api::_()->getDbtable('modules',
				'core')->isModuleEnabled('sitestoreintegration');
			  if(!empty($sitestoreintegrationEnabled)) {
					$contentsTable = Engine_Api::_()->getDbtable('contents', 'sitestoreintegration');
					$contentsTable->delete(array('store_id = ?' => $store_id));
				}
        //END FOR INRAGRATION WORK WITH OTHER PLUGIN.

        //UPDATE IN CONTENT STORE TABLE
        Engine_Api::_()->getDbtable('contentstores', 'sitestore')->update(array('user_id' => $changeuserid), array('store_id = ?' => $store_id));

        //UPDATE PHOTO TABLE
        Engine_Api::_()->getDbtable('photos', 'sitestore')->update(array('user_id' => $changeuserid), array('user_id = ?' => $oldownerid, 'store_id = ?' => $store_id));

        //UPDATE ALBUM TABLE
        Engine_Api::_()->getDbtable('albums', 'sitestore')->update(array('owner_id' => $changeuserid), array('owner_id = ?' => $oldownerid, 'store_id = ?' => $store_id));

        //UPDATE AND DELETE IN MANAGE ADMIN TABLE
        Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->delete(array('user_id = ?' => $changeuserid, 'store_id = ?' => $store_id));
        Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->update(array('user_id' => $changeuserid), array('user_id = ?' => $oldownerid, 'store_id = ?' => $store_id));

        //COMMIT
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //SUCCESS
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 300,
              'parentRefresh' => 300,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The store owner has been changed succesfully.'))
      ));
    }
  }

  //ACTION FOR GETTING THE LIST OF USERS
  public function getOwnerAction() {

  	//GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $this->_getParam('store_id'));

    //USER TABLE
    $tableUser = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $tableUser->info('name');
    $noncreate_owner_level = array();
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
      $can_create = 0;
      if ($level->type != "public") {
        $can_create = Engine_Api::_()->authorization()->getPermission($level->level_id, 'sitestore_store', 'edit');
        if (empty($can_create)) {
          $noncreate_owner_level[] = $level->level_id;
        }
      }
    }

    //SELECT
    $select = $tableUser->select()
            ->where('displayname  LIKE ? ', '%' . $this->_getParam('text') . '%')
            ->where('user_id !=?', $sitestore->owner_id)
            ->order('displayname ASC')
            ->limit($this->_getParam('limit', 40));

    if (!empty($noncreate_owner_level)) {
      $str = (string) ( is_array($noncreate_owner_level) ? "'" . join("', '", $noncreate_owner_level) . "'" : $noncreate_owner_level );
      $select->where($userTableName . '.level_id not in (?)', new Zend_Db_Expr($str));
    }

    //FETCH
    $userlists = $tableUser->fetchAll($select);

    //MAKING DATA
    $data = array();
    $mode = $this->_getParam('struct');

    if ($mode == 'text') {
      foreach ($userlists as $userlist) {
        $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array(
                'id' => $userlist->user_id,
                'label' => $userlist->displayname,
                'photo' => $content_photo
        );
      }
    } else {
      foreach ($userlists as $userlist) {
        $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array(
                'id' => $userlist->user_id,
                'label' => $userlist->displayname,
                'photo' => $content_photo
        );
      }
    }

    return $this->_helper->json($data);
  }

  //ACTION FOR CHANGE THE CATEGORY OF THE STORE
  public function changeCategoryAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET STORE ID
    $this->view->store_id = $store_id = $this->_getParam('id');

    //GET SITESTORE ITEM
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //GET CATEGORY ID
    $this->view->category_id = $previous_category_id = $sitestore->category_id;

    //GET SUBCATEGORY
    $this->view->subcategory_id = $subcategory_id = $sitestore->subcategory_id;

    //GET SUBSUBCATEGORY
    $this->view->subsubcategory_id = $subsubcategory_id = $sitestore->subsubcategory_id;

    //GET ROW
    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($subcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subcategory_name = $row->category_name;
    }

    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($subsubcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subsubcategory_name = $row->category_name;
    }

    //FORM
    $form = $this->view->form = new Sitestore_Form_Admin_Changecategory();

    //POPULATE
    $value['category_id'] = $sitestore->category_id;
    $form->populate($value);

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $is_error = 0;
      //GET FORM VALUES
      $values = $form->getValues();
      if (empty($values['category_id'])) {
        $is_error = 1;
        $this->view->category_id = 0;
      }

      //SET ERROR
      if ($is_error == 1) {
        $error = $this->view->translate('Store Category * Please complete this field - it is required.');
        $this->view->status = false;
        $error = Zend_Registry::get('Zend_Translate')->_($error);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //SAVE VALUES
        $sitestore->category_id = $values['category_id'];
        $sitestore->subcategory_id = $values['subcategory_id'];
				$sitestore->subsubcategory_id = $values['subsubcategory_id'];
        $sitestore->save();
        $db->commit();

        //START SITESTOREREVIEW CODE
        $sitestoreReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
        if ($sitestoreReviewEnabled && $previous_category_id != $sitestore->category_id) {
          Engine_Api::_()->getDbtable('ratings', 'sitestorereview')->editStoreCategory($sitestore->store_id, $previous_category_id, $sitestore->category_id);
        }
        //END SITESTOREREVIEW CODE
        
        //START SITESTOREMEMBER CODE
        $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
        if ($sitestorememberEnabled && $previous_category_id != $sitestore->category_id) {
          $db->query("UPDATE `engine4_sitestore_membership` SET `role_id` = '0' WHERE `engine4_sitestore_membership`.`store_id` = ". $sitestore->store_id. ";");
        }
        //END SITESTOREMEMBER CODE

        //PROFILE MAPPING WORK IF CATEGORY IS EDIT
        if ($previous_category_id != $sitestore->category_id) {
          Engine_Api::_()->getDbtable('profilemaps', 'sitestore')->editCategoryMapping($sitestore);
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //SUCCESS
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 300,
              'parentRefresh' => 300,
              'messages' => array(Zend_Registry::get('Zend_Translate')->_('The category has been changed successfully.'))
      ));
    }
  }

  //ACTION FOR DISPLAY/HIDE FIELDS OF SEARCH FORM
  public function diplayFormAction() {

    $field_id = $this->_getParam('id');
    $display = $this->_getParam('display');
    if (!empty($field_id)) {
      Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'sitestore', 'searchformsetting_id =?' => (int) $field_id));
    }
    $this->_redirect('admin/sitestore/settings/form-search');
  }
  
  //ACTION FOR ENABLE/DISABLE ALL PRODUCTS OF STORE THE SITESTORE
  public function toggleStoreProductsStatusAction() {
    
     //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->_helper->layout->setLayout('admin-simple');
    $store_id = $this->_getParam('id');
    $closed = $this->_getParam('closed');
    if(empty ($closed)){
      $this->view->enable = $enable = 1;
    }else{
      $this->view->enable = $enable = 0;
    }
    $action = $this->_getParam('location');
    if ($this->getRequest()->isPost()) {
      if (array_key_exists('Yes', $_POST)) {
        Engine_Api::_()->getDbtable('stores', 'sitestore')->toggleStoreProductsStatus($store_id, $enable);
      }

      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
//              'parentRefresh' => 10,
        'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sitestore', 'controller' => 'admin', 'action' => $action ,'id' => $store_id, 'closed' => $closed), 'default', true),
//              'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    
  }

}
?>