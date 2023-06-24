<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminWishlistController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminWishlistController extends Core_Controller_Action_Admin {
  
  //ACTION FOR MAKING THE FEATURED /UNFEATURED
  public function featuredAction() {

    $wishlist_id = $this->_getParam('wishlist_id');
    if (!empty($wishlist_id)) {
      $wishlistObj = Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id);
      $wishlistObj->featured = !$wishlistObj->featured;
      $wishlistObj->save();
    }
    $this->_redirect('admin/sitestoreproduct/wishlist/manage');
  }

  //ACTION FOR MANAGE PLAYLISTS
  public function manageAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_wishlist');

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitestoreproduct_Form_Admin_Filter();

    //GET CURRENT PAGE NUMBER
    $page = $this->_getParam('page', 1);

    //GET USER TABLE NAME
    $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

    //GET WISHLIST PAGE TABLE
    $wishlistProductTable = Engine_Api::_()->getDbtable('wishlistmaps', 'sitestoreproduct');
    $wishlistProductTableName = $wishlistProductTable->info('name');

    //MAKE QUERY
    $tableWishlist = Engine_Api::_()->getDbtable('wishlists', 'sitestoreproduct');
    $tableWishlistName = $tableWishlist->info('name');
    $select = $tableWishlist->select()
            ->setIntegrityCheck(false)
            ->from($tableWishlistName)
            ->joinLeft($wishlistProductTableName, "$wishlistProductTableName.wishlist_id = $tableWishlistName.wishlist_id", array("COUNT($wishlistProductTableName.wishlist_id) AS total_item"))
            ->joinLeft($tableUserName, "$tableWishlistName.owner_id = $tableUserName.user_id", 'username')
            ->group($tableWishlistName . '.wishlist_id');

    //GET VALUES
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array('order' => 'wishlist_id', 'order_direction' => 'DESC'), $values);

    if (!empty($_POST['user_name'])) {
      $user_name = $_POST['user_name'];
    } elseif (!empty($_GET['user_name']) && !isset($_POST['post_search'])) {
      $user_name = $_GET['user_name'];
    } else {
      $user_name = '';
    }

    if (!empty($_POST['wishlist_name'])) {
      $wishlist_name = $_POST['wishlist_name'];
    } elseif (!empty($_GET['wishlist_name']) && !isset($_POST['post_search'])) {
      $wishlist_name = $_GET['wishlist_name'];
    } else {
      $wishlist_name = '';
    }
    
    if (!empty($_POST['featured'])) {
      $featured = $_POST['featured'];
    } elseif (!empty($_GET['featured']) && !isset($_POST['post_search'])) {
      $featured = $_GET['featured'];
    } else {
      $featured = '';
    }

    if (!empty($_POST['product_name'])) {
      $product_name = $_POST['product_name'];
    } elseif (!empty($_GET['product_name']) && !isset($_POST['post_search'])) {
      $product_name = $_GET['product_name'];
    } elseif ($this->_getParam('product_name', '') && !isset($_POST['post_search'])) {
      $product_name = $this->_getParam('product_name', '');
    } else {
      $product_name = '';
    }

    //SEARCHING
    $this->view->user_name = $values['user_name'] = $user_name;
    $this->view->wishlist_name = $values['wishlist_name'] = $wishlist_name;
    $this->view->featured = $values['featured'] = $featured;
    $this->view->product_name = $values['product_name'] = $product_name;

    if (!empty($user_name)) {
      $select->where($tableUserName . '.username  LIKE ?', '%' . $user_name . '%');
    }
    if (!empty($wishlist_name)) {
      $select->where($tableWishlistName . '.title  LIKE ?', '%' . $wishlist_name . '%');
    }
    if (!empty($featured)) {
      if( $featured == 2 )
        $featured = 0;
      $select->where($tableWishlistName . '.featured  = ?', $featured);
    }
    if (!empty($product_name)) {
      $tablePageName = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->info('name');
      $select->joinLeft($tablePageName, "$wishlistProductTableName.product_id = $tablePageName.product_id", array('title AS page_title'))
              ->where($tablePageName . '.title  LIKE ?', '%' . $product_name . '%');
    }

    //ASSIGN VALUES TO THE TPL
    $this->view->formValues = array_filter($values);
    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'wishlist_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    
    include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
  }

  //ACTION FOR DELETE THE WISHLIST
  public function deleteAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET WISHLIST ID
    $this->view->wishlist_id = $wishlist_id = $this->_getParam('wishlist_id');

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //DELETE WISHLIST CONTENT
        Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id)->delete();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }
    $this->renderScript('admin-wishlist/delete.tpl');
  }

  //ACTION FOR MULTI DELETE WISHLIST
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {

          //GET WISHLIST ID
          $wishlist_id = (int) $value;

          //DELETE WISHLIST CONTENT
          Engine_Api::_()->getItem('sitestoreproduct_wishlist', $wishlist_id)->delete();
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
  }

}