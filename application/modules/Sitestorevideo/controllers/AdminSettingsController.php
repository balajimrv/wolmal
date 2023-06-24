<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorevideo_AdminSettingsController extends Core_Controller_Action_Admin {

  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {

    //GET NAGIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorevideo');

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestorevideo_admin_main', array(), 'sitestorevideo_admin_main_settings');

    $this->view->form = $form = new Sitestorevideo_Form_Admin_Global();

    if ($this->getRequest()->isPost()) {
      $sitestoreKeyVeri = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.lsettings', null);
      if (!empty($sitestoreKeyVeri)) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.lsettings', trim($sitestoreKeyVeri));
      }
      if ($_POST['sitestorevideo_lsettings']) {
        $_POST['sitestorevideo_lsettings'] = trim($_POST['sitestorevideo_lsettings']);
      }
    }
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      
      
      $currentYouTubeApiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
      if( !empty($_POST['video_youtube_apikey']) && $_POST['video_youtube_apikey'] != $currentYouTubeApiKey ) {
        $response = Engine_Api::_()->seaocore()->verifyYotubeApiKey($_POST['video_youtube_apikey']);
        if( !empty($response['errors']) ) {
          $error_message = array('Invalid API Key');
          foreach( $response['errors'] as $error ) {
            $error_message[] = "Error Reason (" . $error['reason'] . '): ' . $error['message'];
          }
          return $form->video_youtube_apikey->addErrors($error_message);
        }
      }
      
      // It is only for installtion time use after it remove
      if (Engine_Api::_()->sitestore()->hasPackageEnable() && isset($values['include_in_package']) && !empty($values['include_in_package'])) {
        Engine_Api::_()->sitestore()->oninstallPackageEnableSubMOdules('sitestorevideo');
      }
      // It is only for installtion time use after it remove

      foreach ($values as $key => $value) {
        if ($key != 'submit') {
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
      }
    }
    
// Check ffmpeg path for correctness
    if( function_exists('exec') ) {
      $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitestorevideo_ffmpeg_path;
      
      $output = null;
      $return = null;
      if( !empty($ffmpeg_path) ) {
        exec($ffmpeg_path . ' -version', $output, $return);
      }
      // Try to auto-guess ffmpeg path if it is not set correctly
      $ffmpeg_path_original = $ffmpeg_path;
      if( empty($ffmpeg_path) || $return > 0 || stripos(join('', $output), 'ffmpeg') === false ) {
        $ffmpeg_path = null;
        // Windows
        if( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ) {
          // @todo
        }
        // Not windows
        else {
          $output = null;
          $return = null;
          @exec('which ffmpeg', $output, $return);
          if( 0 == $return ) {
            $ffmpeg_path = array_shift($output);
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version', $output, $return);
            if( 0 != $return ) {
              $ffmpeg_path = null;
            }
          }
        }
      }
      if( $ffmpeg_path != $ffmpeg_path_original ) {
        Engine_Api::_()->getApi('settings', 'core')->sitestorevideo_ffmpeg_path = $ffmpeg_path;
      }
    }
  }

  //ACTION OF SETTING FOR CREATING VIDEO FROM MY COMPUTER
  public function utilityAction() {

    //GET NAGIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorevideo');

    //GET NAVIAGION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestorevideo_admin_main', array(), 'sitestorevideo_admin_main_utility');

    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitestorevideo_ffmpeg_path;

    $command = "$ffmpeg_path -version 2>&1";
    $this->view->version = $version = @shell_exec($command);

    $command = "$ffmpeg_path -formats 2>&1";
    $this->view->format = $format = @shell_exec($command);
  }

  //ACTION FOR WIDGET SETTINGS
  public function widgetAction() {

    //GET NAGIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorevideo');

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestorevideo_admin_main', array(), 'sitestorevideo_admin_tabbedwidget');

    $this->view->tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitestorevideo', 'type' => 'videos'));
  }

  //ACTION FOR VIDEO OF THE DAY
  public function manageDayItemsAction() {

    //GET NAGIGATION
    $this->view->navigationStore = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_sitestorevideo');

    //TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestorevideo_admin_main', array(), 'sitestorevideo_admin_dayitemwidget');

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitestorevideo_Form_Admin_Manage_Filter();
    $store = $this->_getParam('page', 1);

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    $values = array_merge(array(
        'order' => 'start_date',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    $this->view->videoOfDaysList = $videoOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore')->getItemOfDayList($values, 'video_id', 'sitestorevideo_video');
    $videoOfDay->setItemCountPerPage(50);
    $videoOfDay->setCurrentPageNumber($store);
  }

  //ACTION FOR ADDING VIDEO OF THE DAY
  public function addVideoOfDayAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitestorevideo_Form_Admin_ItemOfDayday();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setTitle('Add Video of the Day')
            ->setDescription('Select a start date and end date below and the corresponding Video from the auto-suggest Video field. The selected Video will be displayed as "Video of the Day" for this duration and if more than one videos are found to be displayed in the same duration then they will be dispalyed randomly one at a time.');
    $form->getElement('title')->setLabel('Video Name');

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore');

        //FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $values["resource_id"])->where('resource_type = ?', 'sitestorevideo_video');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $values["resource_id"];
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
        $row->resource_type = 'sitestorevideo_video';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Video of the Day has been added successfully.'))
              ));
    }
  }

  //ACTION FOR VIDEO SUGGESTION DROP-DOWN
  public function getVideoAction() {
    $title = $this->_getParam('text', null);
    $limit = $this->_getParam('limit', 40);
    $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storeTableName = $storeTable->info('name');
    $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $allowName = $allowTable->info('name');
    $videoTable = Engine_Api::_()->getDbtable('videos', 'sitestorevideo');
    $videoName = $videoTable->info('name');
    $data = array();
    $select = $videoTable->select()
            ->setIntegrityCheck(false)
            ->from($videoName)
            ->join($storeTableName, $storeTableName . '.store_id = ' . $videoName . '.store_id', array('title AS store_title', 'photo_id as store_photo_id'))
            ->join($allowName, $allowName . '.resource_id = ' . $storeTableName . '.store_id', array('resource_type', 'role'))
            ->where($allowName . '.resource_type = ?', 'sitestore_store')
            ->where($allowName . '.role = ?', 'registered')
            ->where($allowName . '.action = ?', 'view')
            ->where($videoName . '.search = ?', 1)
            ->where($videoName . '.title  LIKE ? ', '%' . $title . '%')
            ->limit($limit)
            ->order($videoName . '.creation_date DESC');
    $select = $select
            ->where($storeTableName . '.closed = ?', '0')
            ->where($storeTableName . '.approved = ?', '1')
            ->where($storeTableName . '.declined = ?', '0')
            ->where($storeTableName . '.draft = ?', '1');
    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
      $select->where($storeTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $videos = $videoTable->fetchAll($select);

    foreach ($videos as $video) {
      $content_photo = $this->view->itemPhoto($video, 'thumb.normal');
      $data[] = array(
          'id' => $video->video_id,
          'label' => $video->title,
          'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR DELETE VIDEO OF DAY ENTRY
  public function deleteVideoOfDayAction() {
    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getDbtable('itemofthedays', 'sitestore')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
              ));
    }
    $this->renderScript('admin-settings/delete.tpl');
  }

  //ACTION FOR MULTI DELETE VIDEO ENTRIES
  public function multiDeleteVideoAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {

          $sitestoreitemofthedays = Engine_Api::_()->getItem('sitestore_itemofthedays', (int) $value);
          if (!empty($sitestoreitemofthedays)) {
            $sitestoreitemofthedays->delete();
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage-day-items'));
  }

  // ACTION FOR CHANGE SETTINGS OF TABBED VIDEO WIDZET TAB
  public function editTabAction() {
    //FORM GENERATION
    $this->view->form = $form = new Sitestorevideo_Form_Admin_EditTab();
    $id = $this->_getParam('tab_id');

    $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
    //CHECK POST
    if (!$this->getRequest()->isPost()) {
      $values = $tab->toarray();
      $form->populate($values);
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $tab->setFromArray($values);
      $tab->save();
      $db->commit();
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Edit Tab Settings Sucessfully.'))
              ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR UPDATE ORDER  OF VIDEOS WIDGTS TAB
  public function updateOrderAction() {
    //CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      $values = $_POST;
      try {
        foreach ($values['order'] as $key => $value) {
          $tab = Engine_Api::_()->getItem('seaocore_tab', (int) $value);
          if (!empty($tab)) {
            $tab->order = $key + 1;
            $tab->save();
          }
        }
        $db->commit();
        $this->_helper->redirector->gotoRoute(array('action' => 'widget'));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //ACTION FOR MAKE TAB ENABLE/DISABLE
  public function enabledAction() {
    $id = $this->_getParam('tab_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
    try {
      $tab->enabled = !$tab->enabled;
      $tab->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitestorevideo/settings/widget');
  }
}

?>