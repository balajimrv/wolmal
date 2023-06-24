<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGING THE STORE VIDEOS
  public function indexAction() {
    
    //GET NAGIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorevideo');    

    //CREATE NAVIGATION TABS
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitestorevideo_admin_main', array(), 'sitestorevideo_admin_main_manage');

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitestorevideo_Form_Admin_Manage_Filter();

    //USER TABLE NAME
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');

    //STORE TABLE NAME
    $tablesitestore = Engine_Api::_()->getItemTable('sitestore_store')->info('name');

    //STORE-VIDEO TABLE
    $table = Engine_Api::_()->getDbtable('videos', 'sitestorevideo');
    $rName = $table->info('name');

    //MAKE QUERY
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName)
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
                'order' => 'video_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'video_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $store = $this->_getParam('page', 1);
    $this->view->paginator = array();
    include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
  }

  //ACTION FOR MULTI-VIDEO DELETE
  public function deleteSelectedAction() {

    //GET VIDEO IDS
    $this->view->ids = $ids = $this->_getParam('ids', null);

    //COUNT IDS
    $ids_array = explode(",", $ids);
    $this->view->count = count($ids_array);

    //CHECK DELETE CONFIRMATION
    $confirm = $this->_getParam('confirm', false);
    if ($this->getRequest()->isPost() && $confirm == true) {

      foreach ($ids_array as $video_id) {

        //GET STORE VIDEO OBJECT
        $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);

        if ($sitestorevideo) {

          //DELETE RATING DATA
          Engine_Api::_()->getDbtable('ratings', 'sitestorevideo')->delete(array('video_id =?' => $video_id));

          //FINALLY DELETE VIDEO OBJECT
          $sitestorevideo->delete();
        }
      }
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  //ACTION FOR DELETE THE STORE-VIDEO
  public function deleteAction() {

    //DEFAULT LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET VIDEO ID
    $this->view->video_id = $video_id = $this->_getParam('video_id');

    if ($this->getRequest()->isPost()) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET STORE VIDEO OBJECT
        $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);

        //DELETE RATING DATA
        Engine_Api::_()->getDbtable('ratings', 'sitestorevideo')->delete(array('video_id =?' => $video_id));

        //FINALLY DELETE VIDEO OBJECT
        $sitestorevideo->delete();

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
    $this->renderScript('admin-manage/delete.tpl');
  }

   public function killAction()
  {
    $video_id = $this->_getParam('video_id', null);
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $video_id);
        $sitestorevideo->status = 3;
        $sitestorevideo->save();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //ACTION FOR MAKE VIDEO FEATURED AND REMOVE FEATURED VIDEO 
  public function featuredvideoAction() {

    //GET OFFER ID
    $videoId = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $videoId);
      if ($sitestorevideo->featured == 0) {
        $sitestorevideo->featured = 1;
      } else {
        $sitestorevideo->featured = 0;
      }
      $sitestorevideo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitestorevideo/manage');
  }

   //ACTION FOR MAKE VIDEO FEATURED AND REMOVE FEATURED VIDEO 
  public function highlightedvideoAction() {

    //GET OFFER ID
    $videoId = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitestorevideo = Engine_Api::_()->getItem('sitestorevideo_video', $videoId);
      if ($sitestorevideo->highlighted == 0) {
        $sitestorevideo->highlighted = 1;
      } else {
        $sitestorevideo->highlighted = 0;
      }
      $sitestorevideo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitestorevideo/manage');
  }

}
?>