<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminReviewController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AdminReviewController extends Core_Controller_Action_Admin {

  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorereview');

    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_admin_reviewmain', array(), 'sitestoreproduct_admin_reviewmain_general');

    // Make form
    $this->view->form = $form = new Sitestoreproduct_Form_Admin_Review_Global();

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
    
    $form->addNotice('Your changes have been saved successfully.');
  }

  //ACTION FOR MANAGING REVIEWS
  public function manageAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorereview');

    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_admin_reviewmain', array(), 'sitestoreproduct_admin_reviewmain_manage');

    //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION  
    $this->view->formFilter = $formFilter = new Sitestoreproduct_Form_Admin_Manage_Filter();

    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');

    $tableProduct = Engine_Api::_()->getItemTable('sitestoreproduct_product')->info('name');

    $tableReviewRating = Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct');
    $tableReviewRatingName = $tableReviewRating->info('name');

    $table = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
    $rName = $table->info('name');
    $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($rName)
            ->joinLeft($tableUser, "$rName.owner_id = $tableUser.user_id", array('username', 'email'))
            ->joinLeft($tableProduct, "$rName.resource_id = $tableProduct.product_id", array('title AS product_title', 'rating_users', 'rating_editor'))
            ->joinLeft($tableReviewRatingName, "$rName.review_id = $tableReviewRatingName.review_id", array('rating As review_rating', 'ratingparam_id'))
            ->where($tableReviewRatingName . '.ratingparam_id = ?', 0);

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    //REVIEW SEARCH WORK
    if (isset($_POST['search'])) {
      if (!empty($_POST['review_title'])) {
        $this->view->review_title = $_POST['review_title'];
        $select->where($rName . '.title  LIKE ?', '%' . $_POST['review_title'] . '%');
      }
      if (!empty($_POST['product_title'])) {
        $this->view->product_title = $_POST['product_title'];
        $select->where($tableProduct . '.title  LIKE ?', '%' . $_POST['product_title'] . '%');
      }

      if (!empty($_POST['name'])) {
        $this->view->name = $_POST['name'];
        $select->where($tableUser . '.username  LIKE ?', '%' . $_POST['name'] . '%')
                ->orWhere($rName . '.anonymous_name LIKE ?', '%' . $_POST['name'] . '%');
      }
      if (!empty($_POST['email'])) {
        $this->view->name = $_POST['email'];
        $select->where($tableUser . '.email  LIKE ?', '%' . $_POST['email'] . '%')
                ->orWhere($rName . '.anonymous_email LIKE ?', '%' . $_POST['email'] . '%');
      }
      if (!empty($_POST['review_type'])) {
        $this->view->review_type = $_POST['review_type'];
        $select->where($rName . '.type  LIKE ?', '%' . $_POST['review_type'] . '%');
      }
      if (isset($_POST['review_status'])) {
        $this->view->review_status = $review_status = $_POST['review_status'];
        if ($review_status == 3) {
          $select->where($rName . '.status =?', 0);
          $this->view->review_status = 3;
        } else if ($review_status == 1) {
          $select->where($rName . '.status =?', 1);
        } else if ($review_status == 2) {
          $select->where($rName . '.status =?', 2);
        }
      }
    } else {
      $this->view->review_title = '';
      $this->view->product_title = '';
      $this->view->name = '';
      $this->view->email = '';
      $this->view->review_status = '';
      $this->view->review_type = '';
    }

    $values = array_merge(array(
        'order' => 'review_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'review_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    $this->view->paginator = array();
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  //ACTION FOR DELETING A REVIEW
  public function deleteAction() {

    $review_id = $this->_getParam('review_id');
    $this->view->review = $review = Engine_Api::_()->getItem('sitestoreproduct_review', (int) $review_id);

    if ($this->getRequest()->isPost()) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $review->delete();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Review has been deleted successfully.'))
      ));
    } else {
      $this->renderScript('admin-review/delete.tpl');
    }
  }

  //ACTION FOR MULTI DELETE REVIEWS
  public function multiDeleteAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          //DELETE DOCUMENTS FROM DATABASE AND SCRIBD
          Engine_Api::_()->getItem('sitestoreproduct_review', (int) $value)->delete();
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('controller' => 'review', 'action' => 'manage'));
  }

  //ACTION FOR WHAT SHOULD BE HAPPEN WITH THE REVIEWS WHICH ARE BY THE NON LOGGED_IN USERS
  public function takeActionAction() {

    //GET REVIEW ITEM
    $this->view->review = $review = Engine_Api::_()->getItem('sitestoreproduct_review', $this->_getParam('review_id'));

    //GET ITEM
    $this->view->sitestoreproduct = $sitestoreproduct = $review->getParent();

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost()) {

      //GET STATUS
      $status = $_POST['status'];

      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $review->status = $status;
        $review->save();

        if ($status == 1) {

          Engine_Api::_()->getDbtable('ratings', 'sitestoreproduct')->listRatingUpdate($review->resource_id, $review->resource_type);
          $sitestoreproduct->review_count++;
          $sitestoreproduct->save();

          //SEND EMAIL FOR REVIEW APPROVED
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($review->anonymous_email, 'SITESTOREPRODUCT_REVIEW_APPROVED', array(
              'review_title' => $review->title,
              'review_description' => $review->body,
              'review_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array('review_id' => $review->review_id, 'product_id' => $sitestoreproduct->getIdentity()), "sitestoreproduct_view_review", true) . '"  >' . 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array('review_id' => $review->review_id, 'product_id' => $sitestoreproduct->getIdentity()), "sitestoreproduct_view_review", true) . ' </a>',
              'email' => Engine_Api::_()->getApi('settings', 'core')->core_mail_from,
              'queue' => false
          ));

          //GET VIEWER
          $viewer = Engine_Api::_()->user()->getViewer();
          $viewer_id = $viewer->getIdentity();

          if (empty($review->owner_id)) {
            $object_parent_with_link = '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] . '/' . $sitestoreproduct->getHref() . '">' . $sitestoreproduct->getTitle() . '</a>';
            $subjectOwner = $sitestoreproduct->getOwner('user');
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $notifyApi->addNotification($subjectOwner, $viewer, $review, 'sitestoreproduct_approved_review', array("object_parent_with_link" => $object_parent_with_link, "anonymous_name" => $review->anonymous_name));
          }
        } else if ($status == 2) {
          //SEND EMAIL FOR REVIEW DISAPPROVED
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($review->anonymous_email, 'SITESTOREPRODUCT_REVIEW_DISAPPROVED', array(
              'review_title' => $review->title,
              'review_description' => $review->body,
              'review_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array('review_id' => $review->review_id, 'product_id' => $sitestoreproduct->getIdentity()), "sitestoreproduct_view_review", true) . '"  >' . 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array('review_id' => $review->review_id, 'product_id' => $sitestoreproduct->getIdentity()), "sitestoreproduct_view_review", true) . ' </a>',
              'email' => $email,
              'queue' => false
          ));
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'parentRefresh' => true,
          'parentRedirectTime' => 1,
          'messages' => array('Your action has been submitted and email successfully sent to the reviewer.')
      ));
    }
  }

  //ACTION FOR MAKE REVIEW FEATURED
  public function featuredAction() {

    //GET REVIEW ITEM
    $review = Engine_Api::_()->getItem('sitestoreproduct_review', $this->_getParam('review_id'));
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      if ($review->featured == 0) {
        $review->featured = 1;
      } else {
        $review->featured = 0;
      }
      $review->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitestoreproduct/review/manage');
  }

}