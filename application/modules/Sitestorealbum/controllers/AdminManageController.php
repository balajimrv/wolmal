<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorealbum_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGING THE ALBUMS
  public function indexAction() {
    
		//GET NAVIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorealbum');     
    
    //CREATE NAVIGATION TABS
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestorealbum_admin_main', array(), 'sitestorealbum_admin_main_manage');

    //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION  
    $this->view->formFilter = $formFilter = new Sitestorealbum_Form_Admin_Manage_Filter();

    //GET STORE NUMBER
    $store = $this->_getParam('page', 1);

    //GET USER TABLE NAME
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');

    //GET SITESTORE TABLE NAME
    $tablesitestore = Engine_Api::_()->getItemTable('sitestore_store')->info('name');

    //GET ALBUM TABLE
    $table = Engine_Api::_()->getDbtable('albums', 'sitestore');

    //GET ALBUM TABLE NAME
    $rName = $table->info('name');

    //SELECT 
    $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($rName, array('album_id', 'store_id', 'owner_id', 'creation_date', 'title', 'view_count', 'like_count', 'comment_count', 'default_value','featured','type'))
            ->joinLeft($tableUser, "$rName.owner_id = $tableUser.user_id", 'username')
            ->joinLeft($tablesitestore, "$rName.store_id = $tablesitestore.store_id", 'title AS sitestore_title');
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
        'order' => 'album_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'album_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
    //MAKE PAGINATOR
    $this->view->paginator = array();
    include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
  }

  //ACTION FOR MULTI DELETE ALBUM
  public function multiDeleteAction() {
    
    //GET ALBUM IDS
    $this->view->ids = $album_ids = $this->_getParam('ids', null);

    //COMFIRM
    $confirm = $this->_getParam('confirm', false);

    //COUNT ALBUM IDS
    $this->view->count = count(explode(",", $album_ids));

    // FORM VALIDATION
    if ($this->getRequest()->isPost() && $confirm == true) {
      //GETTING EXPLODES ALBUM IDS
      $album_ids_array = explode(",", $album_ids);
      foreach ($album_ids_array as $album_id) {
				//DELETE ALBUM AND IMAGE
				Engine_Api::_()->sitestorealbum()->deleteContent($album_id);
      }
      //REDIRECTING
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  //ACTION FOR DELETE THE ALBUM
  public function deleteAction() {
    
    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //SEND ALBUM ID TO THE TPL
    $this->view->album_id = $album_id = $this->_getParam('id');

    //GET SITESTORE ALBUM ITEM
    $sitestorealbum = Engine_Api::_()->getItem('sitestore_album', $album_id);

    //SEND DEFAULT ALBUM VALUE TO THE TPL
    $this->view->default_album_value = $sitestorealbum->default_value;

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost()) {
      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        
        //DELETE ALBUM AND IMAGE
        Engine_Api::_()->sitestorealbum()->deleteContent($album_id);

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
    //OUTPUT
    $this->renderScript('admin-manage/delete.tpl');
  }

  //ACTION FOR MAKE ALBUM FEATURED AND REMOVE FEATURED ALBUM 
  public function featuredAction() {

    //GET OFFER ID
    $albumId = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitestorealbum = Engine_Api::_()->getItem('sitestore_album', $albumId);
      if ($sitestorealbum->featured == 0) {
        $sitestorealbum->featured = 1;
      } else {
        $sitestorealbum->featured = 0;
      }
      $sitestorealbum->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitestorealbum/manage');
  }

}

?>