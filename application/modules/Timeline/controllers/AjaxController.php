<?php

class Timeline_AjaxController extends Core_Controller_Action_Standard
{
  protected $_viewer;
  public function  init() {
        parent::init();
        $this->_helper->layout->disableLayout(true);
        if (!$this->getRequest()->isPost()) {
            return $this->_answerError("Invalid Data.");
        }
        $check_user = $this->_helper->requireUser()->setNoForward();
        if( !$check_user->isValid() )
            return $this->_answerError("You must login.");

        $this->_viewer = Engine_Api::_()->user()->getViewer();
    }

  public function toggleFeaturedAction() {
    $action_id = (int) $this->_getParam('action_id', null);
    if (empty ($action_id))
        return $this->_answerError('Incorrect action id.');
    $table = Engine_Api::_()->getDbtable('features', 'timeline');
    $item = $table->fetchRow(array('user_id = ?' => $this->_viewer->getIdentity(),
                                   'action_id = ?' => $action_id));
    $db = $table->getAdapter();
    $db->beginTransaction();

    try  {
        if ($item !== null) {
            $item->delete();
            $db->commit();
            return $this->_answer(array('toggle' => 'deleted'));
        }
        else {
            $item = $table->createRow();
            $item->user_id = $this->_viewer->getIdentity();
            $item->action_id = $action_id;
            $item->save();
            $db->commit();
            return $this->_answer(array('toggle' => 'added'));
        }
        
    }
    catch( Exception $e ) {
        $db->rollBack();
        return $this->_answerError($message, false, $e);
    }
  }

   public function uploadAction() {

    $translate = Zend_Registry::get('Zend_Translate');
    try {


        if( !$this->getRequest()->isPost() ) {
           throw new Engine_Exception('Invalid request method');
        }

        $values = $this->getRequest()->getPost();

        if( empty($values['Filename']) )
        {
          throw new Engine_Exception('No file');
        }
        if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
        {
          throw new Engine_Exception('Invalid Upload or file too large');
        }
        $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'timeline';
        if( !is_dir($tmpDir) ) {
          if( !mkdir($tmpDir, 0777, true) ) {
            throw new Engine_Exception('TimeLine temporary directory did not exist and could not be created.');
          }
        }
        if( !is_writable($tmpDir) ) {
          throw new Engine_Exception('TimeLine temporary directory is not writable.');
        }
        $file = $_FILES['Filedata'];


        $name = basename($file['tmp_name']);
        $path = dirname($file['tmp_name']);
        $extension = ltrim(strrchr($file['name'], '.'), '.');

        $mainName  = $tmpDir . DIRECTORY_SEPARATOR . 'cover_original_' . $this->_viewer->getIdentity() . '.' . $extension;
        $mainName_tmp  = $tmpDir . DIRECTORY_SEPARATOR . 'cover_tmp_' . $this->_viewer->getIdentity() . '.jpg';
        file_exists($mainName) && unlink($mainName);
        file_exists($mainName_tmp) && unlink($mainName_tmp);
        rename($file['tmp_name'], $mainName);

        // Store photos

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $image = new Timeline_Library_Gd();
        $image->open($mainName);
        $cover_width = $settings->getSetting('cover_width', '1098');
        $cover_height = $settings->getSetting('cover_height', '250');
        $file_width = $image->getWidth();
        $file_height = $image->getHeight();
        if ($settings->getSetting('cover_smaller_width', 0) and ($file_width < $cover_width or $file_height < $cover_height)) {
            $image->destroy();
            $photoFile = $this->_saveFile($mainName);
            @unlink($mainName);
            return $this->_helper->json(array('status' => true,
                                              'src' => $photoFile->map(),
                                              'saved' => true
                                              ));
        }
        elseif ($file_width < $cover_width or $file_height < $cover_height) {
            $image->destroy();
            @unlink($mainName);
            throw new Engine_Exception($this->view->translate("Please choose an image that's at least %1\$s*%2\$s (width*height) pixels. Your image dimension is %3\$s*%4\$s (width*height).", $cover_width, $cover_height, $file_width, $file_height));
        }
        if ($file_width == $cover_width and $file_height == $cover_height) {
            $image->destroy();
            $photoFile = $this->_saveFile($mainName);
            @unlink($mainName);
            return $this->_helper->json(array('status' => true,
                                              'src' => $photoFile->map(),
                                              'saved' => true
                                              ));
        }
        $image->resize($cover_width, $cover_height)
              ->write($mainName_tmp);

        $width_min = (int)($cover_width / ($file_width/$image->getWidth()));
        $height_min = (int)($cover_height / ($file_height/$image->getHeight()));
        $image->destroy();
      /*  if ($width_min < 60) {
            $width_min = 60;
        }
        if ($height_min < 60) {
            $height_min = 60;
        }*/
        // Remove temp files
        @unlink($file['tmp_name']);

        $this->_helper->json(array('status' => true,
                                   'src' => str_replace(APPLICATION_PATH . DIRECTORY_SEPARATOR, '', $mainName_tmp . '?' . md5(time())),
                                   'original_src' => $extension,
                                   'cover_width' => $cover_width,
                                   'cover_height' => $cover_height,
                                   'width_min' => $width_min,
                                   'saved' => false,
                                   'height_min' => $height_min));
    }

    catch ( Exception $e ) {
        return $this->_answerError($e->getMessage());
    }

  }

  public function cropAction() {
    try {
        $values = $this->getRequest()->getPost();
        if (empty ($values['original_src'])) {
            throw new Engine_Exception('Invalid file');
        }
        $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'timeline';
        $mainName_tmp  = $tmpDir . DIRECTORY_SEPARATOR . 'cover_tmp_' . $this->_viewer->getIdentity() . '.jpg';
        $file_original = $tmpDir . DIRECTORY_SEPARATOR . 'cover_original_' . $this->_viewer->getIdentity() . '.' . $values['original_src'];
        $file_cover = $tmpDir . DIRECTORY_SEPARATOR . 'cover_' . $this->_viewer->getIdentity() . '.jpg';
        if (!file_exists($file_original) or !file_exists($mainName_tmp)) {
            throw new Engine_Exception('Invalid file');
        }
        if (!isset ($values['x']) or !isset ($values['y']) or !isset ($values['w']) or !isset ($values['h'])) {
            throw new Engine_Exception('Invalid crop dimension');
        }
        $values['x'] = (int)$values['x'];
        $values['y'] = (int)$values['y'];
        $values['w'] = (int)$values['w'];
        $values['h'] = (int)$values['h'];

        $image = new Timeline_Library_Gd();
        $image->open($mainName_tmp);
        $tmp_width = $image->getWidth();
        $tmp_height = $image->getHeight();
        $image->destroy();
        $image->open($file_original);
        $original_width = $image->getWidth();
        $original_height = $image->getHeight();
        $width_ratio = $original_width/$tmp_width;
        $height_ratio = $original_height/$tmp_height;
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $image->crop($values['x']*$width_ratio, $values['y']*$height_ratio, $values['w']*$width_ratio, $values['h']*$height_ratio)
              ->resize($settings->getSetting('cover_width', '1098'), $settings->getSetting('cover_height', '250'), false)
              ->write($file_cover)
              ->destroy();
        $photoFile = $this->_saveFile($file_cover);
        unlink($file_cover);
        unlink($file_original);
        unlink($mainName_tmp);
        $this->_helper->json(array('status' => true,
                                   'src' => $photoFile->map() ));
    }

    catch ( Exception $e ) {
        return $this->_answerError($e->getMessage());
    }

  }

  protected function _answerError($message, $reload = false, Exception $e = NULL) {
      $json_out = array('status' => false,
                        'reload' => $reload,
                        'error' => Zend_Registry::get('Zend_Translate')->_($message));
      if ($e) {
         $error_code = Engine_Api::getErrorCode(true);
         $log = Zend_Registry::get('Zend_Log');
         $output = '';
         $output .= PHP_EOL . 'Error Code: ' . $error_code . PHP_EOL;
         $output .= $e->__toString();
         $log->log($output, Zend_Log::CRIT);
         $json_out['error'] .= ' ' . $this->view->translate("Please report this to your site administrator with Error Code %s", $error_code);
      }
      return $this->_helper->json($json_out);
  }

  protected function _answer(array $data) {
      return $this->_helper->json(array_merge(array('status' => true), $data));
  }

  protected function _saveFile($file_cover) {
      $photo_params = array('parent_type = ?' => 'user',
                            'parent_id = ?' => $this->_viewer->getIdentity(),
                            'user_id = ?' => $this->_viewer->getIdentity(),
                            'type = ?' => 'cover.timeline'
                           );
      $storage_file = Engine_Api::_()->getItemTable('storage_file');
      $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
      $db = $storage_file->getAdapter();
      $db->beginTransaction();
      try {
          $files = $storage_file->fetchAll($photo_params);
          if (count($files)) {
              foreach ($files as $file) {
                  $oldActivity = $actionsTable->fetchAll(array('type = ?' => 'timeline_cover_update',
                                                               'subject_id = ?' => $this->_viewer->getIdentity()));
                  if ($oldActivity->count()) {
                      foreach ($oldActivity as $oldAct) {
                          $oldAct->delete();
                      }
                      unset($oldActivity);
                  }
                  $file->remove();
              }
          }
          $photo_params = array('parent_type' => 'user',
                                'parent_id' => $this->_viewer->getIdentity(),
                                'user_id' => $this->_viewer->getIdentity(),
                                'type' => 'cover.timeline'
                             );
          $cover_file = $storage_file->createFile($file_cover,  $photo_params);
          // Insert activity
          $action = $actionsTable->addActivity($this->_viewer, $this->_viewer, 'timeline_cover_update');

          if($action!=null){
              $actionsTable->attachActivity($action, $cover_file);
          }
          $db->commit();
      }
      catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      return $cover_file;
  }
}
