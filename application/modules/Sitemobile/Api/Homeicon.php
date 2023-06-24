<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Homeicon.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Api_Homeicon extends Core_Api_Abstract {

  public $_homescreenIcon = array('16' => array('x' => '16', 'y' => '16'),
      '32' => array('x' => '32', 'y' => '32'),
      '57' => array('x' => '57', 'y' => '57'),
      '72' => array('x' => '72', 'y' => '72'),
      '76' => array('x' => '76', 'y' => '76'),
      '114' => array('x' => '114', 'y' => '114'),      
      '120' => array('x' => '120', 'y' => '120'),
      '144' => array('x' => '144', 'y' => '144'),
      '152' => array('x' => '152', 'y' => '152'),
      '158' => array('x' => '158', 'y' => '158'));

  public function addHomeIcon() {

    // Fetch core.menu-logo params from core.content table.
    $coreContentTable = Engine_Api::_()->getDbtable('content', 'core');

    $logoRow = $coreContentTable->select()
            ->From($coreContentTable->info('name'), "params")
            ->where("name = ?", 'core.menu-logo')
            ->query()
            ->fetchColumn();

    $params = json_decode($logoRow);
    if(isset($params->logo)) {
			$photo = $params->logo;
			//Set photo iff logo of site exist. Makes logo of site as home icon on plugin activation.
			if (!empty($photo)) {
				$this->setHomeIcon($photo);
			}
    }
  }

  public function setHomeIcon($photo) {

    $homescreenIcon = $this->_homescreenIcon;

    // if (!empty($photo)) {
    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
      $fileName = $photo;
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
      $image = Engine_Image::factory();
      $image->open($file);

      $size = min($image->height, $image->width);
      $x = ($image->width - $size) / 2;
      $y = ($image->height - $size) / 2;

      $image->resample($x, $y, $size, $size, 250, 250)
              ->write($mainPath)
              ->destroy();

      $iMain = $filesTable->createSystemFile($mainPath);

      // Resize image (profile)
      foreach ($homescreenIcon as $key => $value) {
        $x_i = $value['x'];
        $y_i = $value['y'];
        $key = $x_i . 'x' . $y_i;
        $iconPath = $path . DIRECTORY_SEPARATOR . $base . "_$key." . $extension;
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, $x_i, $y_i)
                ->write($iconPath)
                ->destroy();
        $iPath = $filesTable->createSystemFile($iconPath);
        $iMain->bridge($iPath, "thumb.$key");
        @unlink($iPath);
      }

      // Remove temp files
      @unlink($mainPath);

      //GET OLD PHOTO ID FROM CORE SETTINGS TABLE AND UPDATE IT WITH NEW PHOTO ID.
      $this->view->photo_id = $file_id = $iMain->getIdentity();
      $oldFileID = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.homescreen.fileId');
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($oldFileID);
      if ($file) {
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
      }
      if ($file) {
        $file->remove();
      }

      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitemobile.homescreen.fileId', $file_id);

      $status = true;
      $error = false;
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $status = false;
      $error = true;
    }
//}
  }

}

