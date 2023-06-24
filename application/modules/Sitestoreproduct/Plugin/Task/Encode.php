<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Encode.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Plugin_Task_Encode extends Core_Plugin_Task_Abstract {

  public function getTotal() {
    $table = Engine_Api::_()->getDbTable('videos', 'sitestoreproduct');
    return $table->select()
                    ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
                    ->where('status = ?', 0)
                    ->query()
                    ->fetchColumn(0)
    ;
  }

  public function execute() {
    // Check allowed jobs vs executing jobs
    // @todo this does not function correctly as the task system only allows
    // one to run at a time, unless encoding takes more than 15 minutes anyway
    $sitestoreproductvideoTable = Engine_Api::_()->getItemTable('sitestoreproduct_video');
    $maxAllowedJobs = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.video.jobs', 2);
    $currentlyEncodingCount = $sitestoreproductvideoTable
            ->select()
            ->from($sitestoreproductvideoTable->info('name'), new Zend_Db_Expr('COUNT(*)'))
            ->where('status = ?', 2)
            ->query()
            ->fetchColumn(0)
    ;

    // Let's run some more
    $startedCount = 0;
    if ($currentlyEncodingCount < $maxAllowedJobs) {
      //for( $i = $currentlyEncodingCount + 1, $l = $maxAllowedJobs; $i <= $l; $i++ ) {
      $sitestoreproductvideoSelect = $sitestoreproductvideoTable->select()
              ->where('status = ?', 0)
              ->order('video_id ASC')
              ->limit(1)
      ;
      $sitestoreproduct_video = $sitestoreproductvideoTable->fetchRow($sitestoreproductvideoSelect);
      if ($sitestoreproduct_video instanceof Sitestoreproduct_Model_Sitestoreproduct) {
        $startedCount++;
        $this->_process($sitestoreproduct_video);
      }
      //}
    }

    // We didn't do anything
    if ($startedCount <= 0) {
      $this->_setWasIdle();
    }
  }

  protected function _process($sitestoreproduct_video) {
    // Make sure FFMPEG path is set
    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->sitestoreproduct_video_ffmpeg_path;
    if (!$ffmpeg_path) {
      $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Ffmpeg not configured');
      throw new Sitestoreproduct_Model_Exception($error_msg1);
    }
    // Make sure FFMPEG can be run
    if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path)) {
      $output = null;
      $return = null;
      exec($ffmpeg_path . ' -version', $output, $return);
      if ($return > 0) {
        $error_msg2 = Zend_Registry::get('Zend_Translate')->_('Ffmpeg found, but is not executable');
        throw new Sitestoreproduct_Model_Exception($error_msg2);
      }
    }

    // Check we can execute
    if (!function_exists('shell_exec')) {
      $error_msg3 = Zend_Registry::get('Zend_Translate')->_('Unable to execute shell commands using shell_exec(); the function is disabled.');
      throw new Sitestoreproduct_Model_Exception($error_msg3);
    }

    // Check the video temporary directory
    $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' .
            DIRECTORY_SEPARATOR . 'sitestoreproduct_video';
    if (!is_dir($tmpDir)) {
      if (!mkdir($tmpDir, 0777, true)) {
        $error_msg4 = Zend_Registry::get('Zend_Translate')->_('Video temporary directory did not exist and could not be created.');
        throw new Sitestoreproduct_Model_Exception($error_msg4);
      }
    }
    if (!is_writable($tmpDir)) {
      $error_msg5 = Zend_Registry::get('Zend_Translate')->_('Video temporary directory is not writable.');
      throw new Sitestoreproduct_Model_Exception($error_msg5);
    }

    // Get the video object
    if (is_numeric($sitestoreproduct_video)) {
      $sitestoreproduct_video = Engine_Api::_()->getItem('sitestoreproduct_video', $video_id);
    }

    if (!($sitestoreproduct_video instanceof Sitestoreproduct_Model_Video)) {
      $error_msg6 = Zend_Registry::get('Zend_Translate')->_('Argument was not a valid video');
      throw new Sitestoreproduct_Model_Exception($error_msg6);
    }

    // Update to encoding status
    $sitestoreproduct_video->status = 2;
    $sitestoreproduct_video->save();

    // Prepare information
    $owner = $sitestoreproduct_video->getOwner();
    $filetype = $sitestoreproduct_video->code;

    $originalPath = $tmpDir . DIRECTORY_SEPARATOR . $sitestoreproduct_video->getIdentity() . '.' . $filetype;
    $outputPath = $tmpDir . DIRECTORY_SEPARATOR . $sitestoreproduct_video->getIdentity() . '_vconverted.flv';
    $thumbPath = $tmpDir . DIRECTORY_SEPARATOR . $sitestoreproduct_video->getIdentity() . '_vthumb.jpg';

    $sitestoreproductvideoCommand = $ffmpeg_path . ' '
            . '-i ' . escapeshellarg($originalPath) . ' '
            . '-ab 64k' . ' '
            . '-ar 44100' . ' '
            . '-qscale 5' . ' '
            . '-vcodec flv' . ' '
            . '-f flv' . ' '
            . '-r 25' . ' '
            . '-s 480x386' . ' '
            . '-v 2' . ' '
            . '-y ' . escapeshellarg($outputPath) . ' '
            . '2>&1'
    ;

    $thumbCommand = $ffmpeg_path . ' '
            . '-i ' . escapeshellarg($outputPath) . ' '
            . '-f image2' . ' '
            . '-ss 4.00' . ' '
            . '-v 2' . ' '
            . '-y ' . escapeshellarg($thumbPath) . ' '
            . '2>&1'
    ;

    // Prepare output header
    $output = PHP_EOL;
    $output .= $originalPath . PHP_EOL;
    $output .= $outputPath . PHP_EOL;
    $output .= $thumbPath . PHP_EOL;

    // Prepare logger
    $log = null;
    //if( APPLICATION_ENV == 'development' ) {
    $log = new Zend_Log();
    $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/sitestoreproduct_video.log'));
    //}
    // Execute sitestoreproductvideo encode command
    $sitestoreproductvideoOutput = $output .
            $sitestoreproductvideoCommand . PHP_EOL .
            shell_exec($sitestoreproductvideoCommand);

    // Log
    if ($log) {
      $log->log($sitestoreproductvideoOutput, Zend_Log::INFO);
    }

    // Check for failure
    $success = true;

    // Unsupported format
    if (preg_match('/Unknown format/i', $sitestoreproductvideoOutput) ||
            preg_match('/Unsupported codec/i', $sitestoreproductvideoOutput) ||
            preg_match('/patch welcome/i', $sitestoreproductvideoOutput) ||
            !is_file($outputPath) ||
            filesize($outputPath) <= 0) {
      $success = false;
      $sitestoreproduct_video->status = 3;
    }

    // This is for audio files
    else if (preg_match('/sitestoreproductvideo:0kB/i', $sitestoreproductvideoOutput)) {
      $success = false;
      $sitestoreproduct_video->status = 5;
    }

    // Failure
    if (!$success) {

      $db = $sitestoreproduct_video->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $sitestoreproduct_video->save();

        // notify the owner
        $translate = Zend_Registry::get('Zend_Translate');
        $language = (!empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
        $notificationMessage = '';
        if ($sitestoreproduct_video->status == 3) {
          $notificationMessage = $translate->translate(sprintf(
                          'Video conversion failed. Sitestoreproduct format is not supported by FFMPEG. Please try %1$sagain%2$s.', '', ''
                  ), $language);
        } else if ($sitestoreproduct_video->status == 5) {
          $notificationMessage = $translate->translate(sprintf(
                          'SVideo conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '', ''
                  ), $language);
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($owner, $owner, $sitestoreproduct_video, 'sitestoreproduct_video_processed_failed', array(
                    'message' => $notificationMessage,
                    'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view'), 'sitestoreproduct_video_general', true),
                ));

        $db->commit();
      } catch (Exception $e) {
        $sitestoreproductvideoOutput .= PHP_EOL . $e->__toString() . PHP_EOL;
        if ($log) {
          $log->write($e->__toString(), Zend_Log::ERR);
        }
        $db->rollBack();
      }

      // Write to additional log in dev
      if (APPLICATION_ENV == 'development') {
        file_put_contents($tmpDir . '/' . $sitestoreproduct_video->video_id . '.txt', $sitestoreproductvideoOutput);
      }
    }

    // Success
    else {
      // Get duration of the sitestoreproductvideo to caculate where to get the thumbnail
      if (preg_match('/Duration:\s+(.*?)[.]/i', $sitestoreproductvideoOutput, $matches)) {
        list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
        $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
      } else {
        $duration = 0; // Hmm
      }

      // Log duration
      if ($log) {
        $log->log('Duration: ' . $duration, Zend_Log::INFO);
      }

      // Process thumbnail
      $thumbOutput = $output .
              $thumbCommand . PHP_EOL .
              shell_exec($thumbCommand);

      // Log thumb output
      if ($log) {
        $log->log($thumbOutput, Zend_Log::INFO);
      }

      // Resize thumbnail
      $image = Engine_Image::factory();
      $image->open($thumbPath)
              ->resize(120, 240)
              ->write($thumbPath)
              ->destroy();

      // Save sitestoreproductvideo and thumbnail to storage system
      $params = array(
          'parent_id' => $sitestoreproduct_video->getIdentity(),
          'parent_type' => $sitestoreproduct_video->getType(),
          'user_id' => $sitestoreproduct_video->owner_id
      );

      $db = $sitestoreproduct_video->getTable()->getAdapter();
      $db->beginTransaction();

      try {
        $sitestoreproductvideoFileRow = Engine_Api::_()->storage()->create($outputPath, $params);
        $thumbFileRow = Engine_Api::_()->storage()->create($thumbPath, $params);

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();

        // delete the files from temp dir
        unlink($originalPath);
        unlink($outputPath);
        unlink($thumbPath);

        $sitestoreproduct_video->status = 7;
        $sitestoreproduct_video->save();

        // notify the owner
        $translate = Zend_Registry::get('Zend_Translate');
        $notificationMessage = '';
        $language = (!empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
        if ($sitestoreproduct_video->status == 7) {
          $notificationMessage = $translate->translate(sprintf(
                          'Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '', ''
                  ), $language);
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($owner, $owner, $sitestoreproduct_video, 'sitestoreproduct_video_processed_failed', array(
                    'message' => $notificationMessage,
                    'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view'), 'sitestoreproduct_video_general', true),
                ));

        throw $e; // throw
      }

      // Sitestoreproductvideo processing was a success!
      // Save the information
      $sitestoreproduct_video->file_id = $sitestoreproductvideoFileRow->file_id;
      $sitestoreproduct_video->photo_id = $thumbFileRow->file_id;
      $sitestoreproduct_video->duration = $duration;
      $sitestoreproduct_video->status = 1;
      $sitestoreproduct_video->save();

      // delete the files from temp dir
      unlink($originalPath);
      unlink($outputPath);
      unlink($thumbPath);

      // insert action in a seperate transaction if sitestoreproductvideo status is a success
      $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
      $db = $actionsTable->getAdapter();
      $db->beginTransaction();

      try {
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $sitestoreproduct_video->product_id);
        $store = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $isStoreAdmin = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer_id, $store->getIdentity());     
        if($isStoreAdmin && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
          $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $actionTable->addActivity(Engine_Api::_()->user()->getViewer(), $store, 'sitestoreproduct_video_new', null, array('child_id' => $sitestoreproduct->getIdentity()));

          if ($action != null) {
            $actionTable->attachActivity($action, $sitestoreproduct_video);
          }
        } 

        // notify the owner
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($owner, $owner, $sitestoreproduct_video, 'sitestoreproduct_video_processed');

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e; // throw
      }
    }
  }

}