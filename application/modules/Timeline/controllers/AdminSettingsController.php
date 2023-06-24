<?php

class Timeline_AdminSettingsController extends Core_Controller_Action_Admin {
    
  public function indexAction() {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('timeline_admin_main', array(), 'timeline_admin_main_settings');

      $this->view->form = $form = new  Timeline_Form_Admin_Global();

      if( $this->getRequest()->isPost()) {
          $task = $this->getRequest()->getPost('task');
          if ($task == 'save_settings') {
              if ( $form->isValid($this->getRequest()->getPost())) {
                  $values = $form->getValues();
                  $setting_tmp = Engine_Api::_()->getApi('settings', 'core');
                  foreach ($values as $key => $value){
                    $setting_tmp->setSetting($key, $value);
                  }
              }
          }
          
      }
      $this->view->cover = Engine_Api::_()->timeline()->getDefaultTimeLineCover();
  }

  public function coverAction() {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('timeline_admin_main', array(), 'timeline_admin_main_cover');

      $this->view->cover_form = $cover_form = new  Timeline_Form_Admin_CoverUpload();
      $cover = Engine_Api::_()->getItemTable('storage_file')->fetchRow(array('parent_type = ?' => 'system',
                                                                             'type = ?' => 'cover.default'));
      $this->view->hasAdminCover = !empty ($cover);
      $settings = Engine_Api::_()->getApi('settings', 'core');
      if( $this->getRequest()->isPost()) {
          $task = $this->getRequest()->getPost('task');
          if ($task == 'upload_cover') {
              if ( $cover_form->isValid($this->getRequest()->getPost()) and $cover_form->cover->receive()) {
                  $minheight = $settings->getSetting('cover_height', '250');
                  $minwidth = $settings->getSetting('cover_width', '1098');


                    $image = new Timeline_Library_Gd();
                    $filename = $cover_form->cover->getFileName();
                    $image->open($filename);
                    if ($settings->getSetting('cover_smaller_width', 0) and $image->width <= $minwidth) {
                        
                    }
                    elseif ($image->width != $minwidth or $image->height != $minheight) {
                        $image->set_quality(100);
                        $ratio_orig = $image->width/$image->height;

                        if ($minwidth/$minheight < $ratio_orig) {
                            $cover_height_res = $minheight;
                            $cover_width_res = $image->height*$ratio_orig;
                        } else {
                            $cover_width_res = $minwidth;
                            $cover_height_res = $cover_width_res/$ratio_orig;
                        }
                        $new_height = ($image->height < $minheight) ? $image->height : $minheight;
                        $image->resize($cover_width_res, $cover_height_res)
                              ->crop(0,0, $minwidth, $new_height)
                              ->write($filename);
                    }
                    $image->destroy();
                    if (file_exists($filename)) {
                        $db = Engine_Db_Table::getDefaultAdapter();
                        $db->beginTransaction();

                        try {
                            $coverFile = Engine_Api::_()->getItemTable('storage_file')->fetchRow(array('parent_type = ?' => 'system',
                                                                                                       'type = ?' => 'cover.default'));
                            if ($coverFile == null) {
                                Engine_Api::_()->getDbtable('files', 'storage')->createFile($filename, array('parent_type' => 'system',
                                                                                                             'type' => 'cover.default'));
                            }
                            else {
                                $coverFile->store($filename);
                            }
                            $db->commit();
                            file_exists($filename) && unlink($filename);
                            return $this->_helper->redirector->gotoRouteAndExit();
                        }
                        catch( Exception $e ) {
                            unlink($cover_form->cover->getFileName());
                            $db->rollBack();
                            throw $e;
                        }
                    }

              }
          }
      }
      $this->view->cover = Engine_Api::_()->timeline()->getDefaultTimeLineCover();
  }

  public function tabsIconsAction() {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('timeline_admin_main', array(), 'timeline_admin_main_icons');
      $publicDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'timeline';
      if( !is_dir($publicDir) ) {
          if( !mkdir($publicDir, 0777, true) ) {
            $this->view->message = 'Timeline icons directory did not exist and could not be created.';
            return;
          }
      }
      $iconsDir = $publicDir . DIRECTORY_SEPARATOR . 'tab_icons';
      if( !is_dir($iconsDir) ) {
          if( !mkdir($iconsDir, 0777, true) ) {
            $this->view->message = 'Timeline icons directory did not exist and could not be created.';
            return;
          }
      }
      $tableContent = Engine_Api::_()->getDbtable('content', 'core');
      $tabSelect = $tableContent->select();
      $tabName = $tableContent->info('name');
      $tabSelect->setIntegrityCheck(false)
                ->from($tabName, array('content_id'))
                ->where('`name` = "timeline.container-tabs"');
      $select = $tableContent->select();
      $select->setIntegrityCheck(false)
             ->where($tabName.'.parent_content_id in ?', $tabSelect)
             ->group($tabName.'.name');
      $T = $select->__toString();
      $tabs = $tableContent->fetchAll($select);
      if ($tabs == null) {
          $this->view->message = 'Timeline tabs widget not founded.';
          return;
      }

      $this->view->tabsContents = $tabs;
      $this->view->show_more = true;
      $icons_collection = Array();
      $iconsDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Timeline' . DIRECTORY_SEPARATOR . 'externals' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'icons_collection';
      if( is_dir($iconsDir) ) {
           $icons = scandir($iconsDir);
           foreach ($icons as $icon) {
               if ($icon != "." && $icon != "..") {
                   $icons_collection[] = $icon;
               }
           }
           reset($icons);
      }
      $this->view->icons_collection = $icons_collection;
      if( $this->getRequest()->isPost()) {
          $form = new Timeline_Form_Admin_IconUpload();
          if ($form->isValid($this->getRequest()->getPost()) && $form->icon->isUploaded() && $form->icon->receive()) {
              $icon_id = $form->content_id->getValue();
              if ($icon_id == -1) {
                  $icon_id = 'more';
              }
              $file_icon = APPLICATION_PATH . '/public/timeline/tab_icons/tab_' . $icon_id . '.png';
              file_exists($file_icon) && unlink($file_icon);
              rename($form->icon->getFileName(), $file_icon);
          }
          else {
              $this->view->form = $form;
              $this->view->id_form_error = $form->content_id->getValue();
          }
      }

  }

  public function deleteIconAction() {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->id = $id = $this->_getParam('id');
    $this->view->delete_title = 'Do you want to reset the icon?';
    $this->view->delete_description = 'Are you sure that you want to set default image for this tab?';
    $this->view->button = 'Reset';
    // Check post
    if( $this->getRequest()->isPost())
    {
      if ($id == -1) {
          $id = 'more';
      }
      $file_icon = APPLICATION_PATH . '/public/timeline/tab_icons/tab_' . $id . '.png';
      file_exists($file_icon) && unlink($file_icon);
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh'=> true,
          'messages' => array('Default icon was set.')
      ));
    }

    // Output
    $this->renderScript('etc/delete.tpl');
  }

  public function resetCoverAction() {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->delete_title = 'Reset Cover?';
    $this->view->delete_description = 'Are you sure that you want to reset cover?';
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
          $coverFile = Engine_Api::_()->getItemTable('storage_file')->fetchRow(array('parent_type = ?' => 'system',
                                                                                     'type = ?' => 'cover.default'));
          if ($coverFile != null) {
              $coverFile->remove();
              $db->commit();
          }
      }
      catch( Exception $e ) {
          $db->rollBack();
          throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh'=> true,
          'messages' => array('Completed.')
      ));
    }

    // Output
    $this->renderScript('etc/delete.tpl');
  }

  public function setIconCollectionAction() {
      $this->_helper->layout->disableLayout(true);
      if ($this->getRequest()->isPost()) {
          $tab = $this->_getParam('tab');
          $icon = $this->_getParam('icon');
          $file_icon = APPLICATION_PATH . '/public/timeline/tab_icons/tab_' . $tab . '.png';
          file_exists($file_icon) && unlink($file_icon);
          copy(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Timeline' . DIRECTORY_SEPARATOR . 'externals' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'icons_collection' . DIRECTORY_SEPARATOR . $icon, $file_icon);
          $this->_helper->json(array('status' => true, 'file_icon' => $this->view->baseUrl() . '/application/modules/Timeline/externals/images/icons_collection/' . $icon));
      }
      $this->_helper->json(array('status' => false));
  }
}