<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminGeneralController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminGeneralController extends Core_Controller_Action_Admin {

    //ACTION FOR MAKING THE SITESTOREPRODUCT FEATURED/UNFEATURED
    public function featuredAction() {

        $product_id = $this->_getParam('product_id');
        if (!empty($product_id)) {
            $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
            $sitestoreproduct->featured = !$sitestoreproduct->featured;
            $sitestoreproduct->save();
        }
        $this->_redirect('admin/sitestoreproduct/manage');
    }

    //ACTION FOR MAKING THE SPONSORED /UNSPONSORED
    public function sponsoredAction() {

        $product_id = $this->_getParam('product_id');
        if (!empty($product_id)) {
            $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
            $sitestoreproduct->sponsored = !$sitestoreproduct->sponsored;
            $sitestoreproduct->save();
        }
        $this->_redirect('admin/sitestoreproduct/manage');
    }

    //ACTION FOR MAKING THE SITESTOREPRODUCT FEATURED/UNFEATURED
    public function newlabelAction() {

        $product_id = $this->_getParam('product_id');
        if (!empty($product_id)) {
            $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
            $sitestoreproduct->newlabel = !$sitestoreproduct->newlabel;
            $sitestoreproduct->save();
        }
        $this->_redirect('admin/sitestoreproduct/manage');
    }

    //ACTION FOR MAKING THE SITESTOREPRODUCT OPEN/CLOSE
    public function openCloseAction() {

        $product_id = $this->_getParam('product_id');
        if (!empty($product_id)) {
            $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
            $sitestoreproduct->closed = !$sitestoreproduct->closed;
            $sitestoreproduct->save();
        }
        $this->_redirect('admin/sitestoreproduct/manage');
    }

//  //ACTION FOR MAKING THE SPONSORED /UNSPONSORED
//  public function sponsoredCategoryAction() {
//
//    $category_id = $this->_getParam('category_id');
//    if (!empty($category_id)) {
//      $category = Engine_Api::_()->getItem('sitestoreproduct_category', $category_id);
//      $category->sponsored = !$category->sponsored;
//      $category->save();
//    }
//    $this->_redirect('admin/sitestoreproduct/settings/categories');
//  }
    //ACTION FOR MAKING THE SITESTOREPRODUCT APPROVE/DIS-APPROVE
    public function approvedAction() {

        $product_id = $this->_getParam('product_id');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $email = array();
        try {

            $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
            $email['subject'] = 'Approved/ Disapproved notification';
            $email['title'] = $sitestoreproduct->title;
            $owner = Engine_Api::_()->user()->getUser($sitestoreproduct->owner_id);
            $email['mail_id'] = $owner->email;
            $sitestoreproduct->approved = !$sitestoreproduct->approved;

            if (!empty($sitestoreproduct->approved)) {
                if (empty($sitestoreproduct->approved_date))
                    $sitestoreproduct->approved_date = date('Y-m-d H:i:s');
                $email['message'] = "Your sitestoreproduct  \"" . $email['title'] . " \" approved ";
                Engine_Api::_()->sitestoreproduct()->aprovedEmailNotification($sitestoreproduct, $email);
            } else {
                $email['message'] = "Your sitestoreproduct " . $email['title'] . "  disapproved ";
                Engine_Api::_()->sitestoreproduct()->aprovedEmailNotification($sitestoreproduct, $email);
            }
            $sitestoreproduct->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirect('admin/sitestoreproduct/manage');
    }

    //ACTION FOR MAKING THE SITESTOREPRODUCT APPROVE/DIS-APPROVE
    public function renewAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $this->view->product_id = $product_id = $this->_getParam('product_id');
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
                $sitestoreproduct->approved_date = date('Y-m-d H:i:s');
                $sitestoreproduct->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Renew Succesfully.')
            ));
        }
        $this->renderScript('admin-general/renew.tpl');
    }

    public function categoriesAction() {

        $element_value = $this->_getParam('element_value', 1);
        $element_type = $this->_getParam('element_type', 'category_id');

        $categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct');
        $select = $categoriesTable->select()
                ->from($categoriesTable->info('name'), array('category_id', 'category_name'))
                ->where("$element_type = ?", $element_value);

        if ($element_type == 'category_id') {
            $select->where('cat_dependency = ?', 0)->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'cat_dependency') {
            $select->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'subcat_dependency') {
            $select->where('cat_dependency = ?', $element_value);
        }

        $categoriesData = $categoriesTable->fetchAll($select);

        $categories = array();
        if (Count($categoriesData) > 0) {
            foreach ($categoriesData as $category) {
                $data = array();
                $data['category_name'] = $category->category_name;
                $data['category_id'] = $category->category_id;
                $categories[] = $data;
            }
        }

        $this->view->categories = $categories;
    }

    public function storeCategoriesAction() {

        $element_value = $this->_getParam('element_value', 1);
        $element_type = $this->_getParam('element_type', 'category_id');

        $categoriesTable = Engine_Api::_()->getDbTable('categories', 'sitestore');
        $select = $categoriesTable->select()
                ->from($categoriesTable->info('name'), array('category_id', 'category_name'))
                ->where("$element_type = ?", $element_value);

        if ($element_type == 'category_id') {
            $select->where('cat_dependency = ?', 0)->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'cat_dependency') {
            $select->where('subcat_dependency = ?', 0);
        } elseif ($element_type == 'subcat_dependency') {
            $select->where('cat_dependency = ?', $element_value);
        }

        $categoriesData = $categoriesTable->fetchAll($select);

        $categories = array();
        if (Count($categoriesData) > 0) {
            foreach ($categoriesData as $category) {
                $data = array();
                $data['category_name'] = $category->category_name;
                $data['category_id'] = $category->category_id;
                $categories[] = $data;
            }
        }

        $this->view->categories = $categories;
    }

    //ACTION FOR DELETE THE PRODUCT
    public function deleteAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $product_id = $this->_getParam('product_id');
        $this->view->product_id = $product_id;

        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getItem('sitestoreproduct_product', $product_id)->delete();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Deleted Succesfully.')
            ));
        }
        $this->renderScript('admin-general/delete.tpl');
    }

    //ACTION FOR CHANGE THE OWNER OF THE LISTING
    public function changeOwnerAction() {

        //LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET LISTING ID
        $this->view->product_id = $this->_getParam('product_id');

        //FORM
        $form = $this->view->form = new Sitestoreproduct_Form_Admin_Changeowner();

        //SET ACTION
        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

            //GET FORM VALUES
            $values = $form->getValues();

            //GET USER ID WHICH IS NOW NEW USER
            $params = array();
            $params['changeuserid'] = $values['user_id'];
            $params['product_id'] = $this->view->product_id;

            Engine_Api::_()->sitestoreproduct()->changeOwner($params);

            //SUCCESS
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 300,
                'parentRefresh' => 300,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The product owner has been changed succesfully.'))
            ));
        }
    }

    //ACTION FOR GETTING THE LIST OF USERS
    public function getOwnerAction() {

        //GET SITESTOREPRODUCT ITEM
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $this->_getParam('product_id'));

        //USER TABLE
        $tableUser = Engine_Api::_()->getDbtable('users', 'user');
        $userTableName = $tableUser->info('name');
        $noncreate_owner_level = array();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
            $can_create = 0;
            if ($level->type != "public") {
                $can_create = Engine_Api::_()->authorization()->getPermission($level->level_id, 'sitestoreproduct_product', "edit");
                if (empty($can_create)) {
                    $noncreate_owner_level[] = $level->level_id;
                }
            }
        }

        //SELECT
        $select = $tableUser->select()
                ->where('displayname  LIKE ? ', '%' . $this->_getParam('text') . '%')
                ->where('user_id !=?', $sitestoreproduct->owner_id)
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

    public function setTemplateAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitestore_admin_main', array(), 'sitestoreproduct_admin_main_template');

        $this->view->form = $form = new Sitestoreproduct_Form_Admin_Template();

//        $previousHomeTemplate = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.hometemplate', 'template1');
//        $previousProfileTemplate = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.profiletemplate', 'template1');
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
//            if (isset($values['siteevent_hometemplate']) && !empty($values['siteevent_hometemplate']) && !empty($previousHomeTemplate) && $values['siteevent_hometemplate'] != $previousHomeTemplate) {
//                $templateHome = $values['siteevent_hometemplate'] . "Home";
//                Engine_Api::_()->getApi('template', 'siteevent')->$templateHome();
//            }

            if (isset($values['sitestoreproduct_product_profiletemp']) && !empty($values['sitestoreproduct_product_profiletemp'])) {
                Engine_Api::_()->getApi('template', 'sitestoreproduct')->template1ProductProfile($values['sitestoreproduct_product_profiletemp']);
            }

            foreach ($values as $key => $value) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
            }

            $form->addNotice('Your changes have been saved.');
        }
    }

    public function orderPayoutAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $order_id = $this->_getParam('order_id');
        $gateway_id = $this->_getParam('gateway_id');
        $store_id = $this->_getParam('store_id');
        if ($this->getRequest()->isPost()) {
            $gateway = Engine_Api::_()->getItem('sitestoreproduct_usergateway', $gateway_id);
            $params['resource_id'] = $order_id;
            $params['resource_type'] = 'sitestoreproduct_order';
            $params['store_id'] = $store_id;
            $data = $gateway->getPlugin()->payoutTransaction($params);
            $message = $data['message'];
            
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array($message)
            ));
        }
        $this->renderScript('admin-general/order-payout.tpl');
    }

    public function orderRefundAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $order_id = $this->_getParam('order_id');
        $gateway_id = $this->_getParam('gateway_id');
        $store_id = $this->_getParam('store_id');
        if ($this->getRequest()->isPost()) {
            $gateway = Engine_Api::_()->getItem('sitestoreproduct_usergateway', $gateway_id);
            $params['resource_id'] = $order_id;
            $params['resource_type'] = 'sitestoreproduct_order';
            $params['store_id'] = $store_id;
            $data = $gateway->getPlugin()->refundTransaction($params);
            $message = $data['message'];
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array($message)
            ));
        }
        $this->renderScript('admin-general/order-refund.tpl');
    }

}
