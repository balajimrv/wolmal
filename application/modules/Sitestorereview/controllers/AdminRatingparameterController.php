<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminRatingparameterController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_AdminRatingparameterController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGING RATING PARAMETERS
  public function manageAction() {
    
    //GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorereview');        
    //GET NAVIGAION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitestorereview_admin_main', array(), 'sitestorereview_admin_main_params');

    //SHOW REVIEW PARAMETERS
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('reviewcats', 'sitestorereview')->reviewCatParams();

    $reviewcat_cat_array = array();
    foreach ($paginator as $item) {
      $reviewcat_cat_array[$item->category_id][0] = $item->category_name;
      $reviewcat_cat_array[$item->category_id][$item->reviewcat_id] = $item->reviewcat_name;
    }

    $this->view->reviewcat_cat_array = $reviewcat_cat_array;
  }

  //ACTION FOR CREATE NEW REVIEW PARAMETER
  public function createAction() {
    $this->_helper->layout->setLayout('admin-simple');

    //GENERATE FORM
    $form = $this->view->form = new Sitestorereview_Form_Admin_Ratingparameter_Create();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        //ADD REVIEW CATEGORY TO THE DATABASE
        $tableReviewParams = Engine_Api::_()->getDbtable('reviewcats', 'sitestorereview');

        //INSERT THE REVIEW CATEGORY IN TO THE DATABASE
        $row = $tableReviewParams->createRow();
        $row->category_id = $this->_getParam('category_id');
        $row->reviewcat_name = $values["reviewcat_name"];
        $row->save();

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

    $this->renderScript('admin-ratingparameter/create.tpl');
  }

  //ACTION FOR EDITTING THE REVIEW PARAMETER NAME
  public function editAction() {
    $this->_helper->layout->setLayout('admin-simple');

    if (!($category_id = $this->_getParam('category_id'))) {
      die('No identifier specified');
    }

    //FETCH PARAMETERS ACCORDING TO THIS CATEGORY
    $reviewCategories = Engine_Api::_()->getDbtable('reviewcats', 'sitestorereview')->reviewParams($category_id);

    //GENERATE A FORM
    $form = $this->view->form = new Sitestorereview_Form_Admin_Ratingparameter_Edit();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    $form->setField($reviewCategories->toArray());

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      foreach ($values as $key => $value) {
        $reviewcat_id = explode('reviewcat_name_', $key);
        $reviewcat = Engine_Api::_()->getItem('sitestorereview_reviewcat', $reviewcat_id[1]);

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
          //EDIT CATEGORY NAMES
          $reviewcat->reviewcat_name = $value;
          $reviewcat->save();

          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }

    $this->renderScript('admin-ratingparameter/edit.tpl');
  }

  //ACTION FOR DELETING THE REVIEW PARAMETERS
  public function deleteAction() {
    $this->_helper->layout->setLayout('admin-simple');

    if (!($category_id = $this->_getParam('category_id'))) {
      die('No identifier specified');
    }

    //GENERATE FORM
    $form = $this->view->form = new Sitestorereview_Form_Admin_Ratingparameter_Delete();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      foreach ($values as $key => $value) {
        if ($value == 1) {
          $reviewcat_id = explode('reviewcat_name_', $key);
          $reviewcat = Engine_Api::_()->getItem('sitestorereview_reviewcat', $reviewcat_id[1]);

          Engine_Api::_()->sitestorereview()->deleteReviewCategory($reviewcat_id[1]);

          $db = Engine_Db_Table::getDefaultAdapter();
          $db->beginTransaction();

          try {
            //DELETE THE REVIEW PARAMETERS
            $reviewcat->delete();
            $db->commit();
          } catch (Exception $e) {
            $db->rollBack();
            throw $e;
          }
        }
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }
    $this->renderScript('admin-ratingparameter/delete.tpl');
  }

}
?>