<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_AdminManageController extends Core_Controller_Action_Admin {
  /* HOME SCREEN ICON ACTIONS
   * Action to display "Home screen icon settings".
   */

  public function homeIconAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitemobile_admin_main', array(), 'sitemobile_admin_main_manage');

    //GET SUB-NAVIGATION
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemobile_admin_main_manage', array(), 'sitemobile_admin_main_manage_home-icon');

    $homescreenIcon = Engine_Api::_()->getApi('homeicon', 'sitemobile')->_homescreenIcon;

    $photosUrl = array();
    $file_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.homescreen.fileId');
    if ($file_id) {
      foreach ($homescreenIcon as $value) {
        $key = $value['x'] . 'x' . $value['y'];
        $type = 'thumb.' . $key;

        //GET NEW FILE ID ICONS
        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, $type);
        if ($file) {
          $photosUrl[$key] = $file->map();
        }
      }
      $this->view->photoUrl = $photosUrl;
    }
  }

  public function addIconAction() {

    $this->view->form = $form = new Sitemobile_Form_Admin_Manage_AddIcon();

    // If  post or form is valid
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      // Set photo if photo is uploaded.
      if (!empty($values['photo'])) {
        $photo = $form->photo;
        Engine_Api::_()->getApi('homeicon', 'sitemobile')->setHomeIcon($photo);
      }

      //smoothbox close.
      $this->_forward('success', 'utility', 'core', array(
          'parentRefresh' => "true",
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your icon has been saved successfully.')),
      ));
    }
  }

  public function removeIconAction() {
    $this->view->form = $form = new Sitemobile_Form_Admin_Manage_RemoveIcon();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $homescreenIcon = Engine_Api::_()->getApi('homeicon', 'sitemobile')->_homescreenIcon;

      $oldFileID = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.homescreen.fileId');

      //TO REMOVE ENTRY FROM CORE SETTING.
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitemobile.homescreen.fileId', 0);

      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($oldFileID);
      if ($file) {
        $file->remove();
      }

      foreach ($homescreenIcon as $value) {
        $key = $value['x'] . 'x' . $value['y'];
        $type = 'thumb.' . $key;
        //DELETE OLD FILE ID ICONS IF UPDATING.
        if ($oldFileID) {
          $oldFile = Engine_Api::_()->getItemTable('storage_file')->getFile($oldFileID, $type);
          if ($oldFile) {
            $oldFile->remove();
          }
        }
      }

      //smoothbox close.
      $this->_forward('success', 'utility', 'core', array(
          'parentRefresh' => "10",
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('The icon has been deleted successfully.')),
      ));
    }
  }

  public function editIconAction() {

    $key = $this->_getParam('key', '');

    $this->view->form = $form = new Sitemobile_Form_Admin_Manage_AddIcon(array('item' => $key));

    $homescreenIcon = Engine_Api::_()->getApi('homeicon', 'sitemobile')->_homescreenIcon;
    // If  post or form is valid
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();

      // Set photo
      if (!empty($values['photo'])) {
        $photo = $form->photo;

        if ($photo instanceof Zend_Form_Element_File) {
          $file = $photo->getFileName();
          $fileName = $file;
        } else {
          throw new Core_Model_Exception('invalid argument passed to setPhoto');
        }

        if (!$fileName) {
          $fileName = basename($file);
        }

        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        $db = $filesTable->getAdapter();
        $db->beginTransaction();

        try {
          // Resize image (main)
          $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;


          $iMainFileId = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.homescreen.fileId', 0);

          $iMain = Engine_Api::_()->getItemTable('storage_file')->getFile($iMainFileId);

          $iOldIconFile = Engine_Api::_()->getItemTable('storage_file')->getFile($iMainFileId, "thumb.$key");
          $keys = explode('x', $key);
          $x_i = $keys['0'];
          $y_i = $keys['1'];
          $iconPath = $path . DIRECTORY_SEPARATOR . $base . "_$key." . $extension;
          $image = Engine_Image::factory();
          $image->open($file);

          $size = min($image->height, $image->width);
          $x = ($image->width - $size) / 2;
          $y = ($image->height - $size) / 2;

          $image->resample(0, 0, $image->width, $image->height, $x_i, $y_i)
                  ->write($iconPath)
                  ->destroy();
          $iPath = $filesTable->createSystemFile($iconPath);
          $iOldIconFile->remove();
          $iMain->bridge($iPath, "thumb.$key");
          @unlink($iPath);

          $this->view->status = true;
          $this->view->error = false;
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          $this->view->status = false;
          $this->view->error = true;
        }
      }

      //smoothbox close.
      $this->_forward('success', 'utility', 'core', array(
          'parentRefresh' => "true",
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your icon has been saved successfully.')),
      ));
    }
  }

  public function cropIconAction() {
    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitemobile_admin_main', array(), 'sitemobile_admin_main_manage');

    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemobile_admin_main_manage', array(), 'sitemobile_admin_main_manage_home-icon');
    //GET KEY
    $this->view->key = $key = $this->_getParam('key');
    $keys = explode('x', $key);
    $x_i = $keys['0'];
    $y_i = $keys['1'];
    // Get form
    $this->view->form = $form = new Sitemobile_Form_Admin_Manage_CropIcon();

    $iMainFileId = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.homescreen.fileId');
    $main_file = Engine_Api::_()->getItemTable('storage_file')->getFile($iMainFileId, null);
    $this->view->photoMain = $main_file->map();

    $iOldIconFile = Engine_Api::_()->getItemTable('storage_file')->getFile($iMainFileId, "thumb.$key");
    $this->view->photoIcon = $iOldIconFile->map();
    // If  post or form is valid
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      if (!empty($iMainFileId)) {
        $file = $main_file->temporary();
        $fileName = $main_file->name;
      }
      if (!$fileName) {
        $fileName = basename($file);
      }

      $extension = ltrim(strrchr(basename($fileName), '.'), '.');
      $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

      // Save
      $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
      $db = $filesTable->getAdapter();
      $db->beginTransaction();

      try {
        //Save croped photo 
        if ($form->getValue('coordinates') !== '') {
          $iconPath = $path . DIRECTORY_SEPARATOR . $base . "_$key." . $extension;
          list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resample($x + .1, $y + .1, $w - .1, $h - .1, $x_i, $y_i)
                  ->write($iconPath)
                  ->destroy();
          $iPath = $filesTable->createSystemFile($iconPath);
          $iOldIconFile->remove();
          $main_file->bridge($iPath, "thumb.$key");
          @unlink($iPath);
        }
        $this->view->status = true;
        $this->view->error = false;
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        $this->view->status = false;
        $this->view->error = true;
      }
      return $this->_helper->redirector->gotoRoute(array('action' => 'home-icon'));
    }
  }

  /* SPALSH SCREEN ICON ACTIONS
   * Action to display "splash screen settings".
   */

  public function splashScreenAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitemobile_admin_main', array(), 'sitemobile_admin_main_manage');
    //GET SUB-NAVIGATION
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitemobile_admin_main_manage', array(), 'sitemobile_admin_main_manage_splash-screen');

    $table = Engine_Api::_()->getDbtable('splashscreens', 'sitemobile');

    //check splash screen of same size already exists or not.
    $select = $table->select()
            ->from($table->info('name'));
    $rows = $select->query()->fetchAll();

    include_once APPLICATION_PATH . "/application/modules/Sitemobile/controllers/license/license2.php";
  }

  public function addSplashScreenAction() {

    $this->view->form = $form = new Sitemobile_Form_Admin_Manage_AddSplashScreen();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      $screenTitle = array(
          '320x460' => 'iPhone',
          '640x920' => 'iPhone (Retina)',
          '640x1096' => 'iPhone 5',
          '768x1004' => 'iPad  portrait',
          '1024x748' => 'iPad landscape',
          '1536x2008' => 'iPad  (Retina) portrait',
          '2048x1496' => 'iPad  (Retina) landscape',
      );
      $key = explode('x', $values['key']);
      $x_i = $key[0]; // $values['width'];
      $y_i = $key[1]; //$values['height'];
      $title = $screenTitle[$values['key']]; //$values['title'];
      $iconSize = $x_i . "x" . $y_i;

      $table = Engine_Api::_()->getDbtable('splashscreens', 'sitemobile');

      $db = $table->getAdapter();
      $db->beginTransaction();

      try {
        // Set photo
        if (!empty($values['photo'])) {
          $photo = $form->photo;


          //check splash screen of same size already exists or not.
          $select = $table->select()
                  ->where('width = ?', $x_i)
                  ->where('height = ?', $y_i)
                  ->limit(1);
          $row = $table->fetchAll($select);

          //if splash screen of that size not exist than insert row.
          if (count($row) > 0) {
            $form->addError("The splash screen of this dimension already exists. Please try to upload a splash screen with different dimensions.");
            return;
          }
          if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
          } else {
            throw new Core_Model_Exception('invalid argument passed to setPhoto');
          }

          if (!$fileName) {
            $fileName = basename($file);
          }

          $extension = ltrim(strrchr(basename($fileName), '.'), '.');
          $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
          $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

          // Save
          $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

          //$iMainFileId = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.splashPhoto');
          // $iMain = Engine_Api::_()->getItemTable('storage_file')->getFile($iMainFileId);

          $iconPath = $path . DIRECTORY_SEPARATOR . $base . "_$iconSize." . $extension;
          $image = Engine_Image::factory();
          $image->open($file);

//          $size = min($image->height, $image->width);
//          $x = ($image->width - $size) / 2;
//          $y = ($image->height - $size) / 2;

          $image->resample(0, 0, $image->width, $image->height, $x_i, $y_i)
                  ->write($iconPath)
                  ->destroy();
          $iPath = $filesTable->createSystemFile($iconPath);

          @unlink($iPath);

          $file_id = $iPath->getIdentity();
        }

        $table->insert(array(
            'title' => $title,
            'file_id' => $file_id,
            'width' => $x_i,
            'height' => $y_i,
        ));
        //smoothbox close.
        $this->_forward('success', 'utility', 'core', array(
            'parentRefresh' => "true",
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your splash screen has been saved successfully.')),
        ));


        $this->view->status = true;
        $this->view->error = false;

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        $this->view->status = false;
        $this->view->error = true;
      }
    }
  }

  public function removeSplashScreenAction() {

    $this->view->form = $form = new Sitemobile_Form_Admin_Manage_RemoveSplashScreen();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $file_id = $this->_getParam('file_id', '');

      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id);
      if ($file) {
        $file->remove();
      }

      Engine_Api::_()->getDbtable('splashscreens', 'sitemobile')->delete(array('file_id = ?' => $file_id));

      //smoothbox close.
      $this->_forward('success', 'utility', 'core', array(
          'parentRefresh' => "10",
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('The splash screen has been deleted successfully.')),
      ));
    }
  }

}