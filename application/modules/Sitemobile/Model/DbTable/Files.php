<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Files.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Model_DbTable_Files extends Engine_Db_Table {

  protected $_deviceSize = array(
      'mobile' => array('w' => '320', 'h' => '640'),
      'mobile-wid' => array('w' => '480', 'h' => '960'),
      'tablet' => array('w' => '640', 'h' => '1080'),
      'tablet-wid' => array('w' => '960', 'h' => '1920'),
  );
  // Properties
  protected $_name = 'storage_files';
  protected $_mobileStorageTableName = 'engine4_sitemobile_storage_files';
  protected $_storageTableName = 'engine4_storage_files';

  // Constants

  const SPACE_LIMIT_REACHED_CODE = 3999;

  // Properties

  protected $_rowClass = 'Storage_Model_File';
  protected $_files = array();
  protected $_relationships = array();
  protected $_primary = array('file_id');

  public function setTableName($name) {
    $this->_name = $name;
  }

  public function setMobileStorageTableName() {
    $this->setTableName($this->_mobileStorageTableName);
  }

  public function setStorageTableName() {
    $this->setTableName($this->_storageTableName);
  }

  public function isMobileStorageTableName() {
    return $this->_name === $this->_mobileStorageTableName;
  }

  public function getFile($id, $relationship = null) {
    $key = $id . '_' . ( $relationship ? $relationship : 'default' );
    if (!array_key_exists($key, $this->_files)) {
      $file = null;
      if ($relationship) {
        $temprelationship = str_replace('thumb.', '', $relationship);
        if (isset($this->_deviceSize[$temprelationship])) {
          $this->setMobileStorageTableName();
        }
        $select = $this->select()
                ->where('parent_file_id = ?', $id)
                ->where('type = ?', $relationship)
                ->limit(1);

        $file = $this->fetchRow($select);
        $this->setStorageTableName();
      }

      if (null === $file) {
        $file = Engine_Api::_()->getItem('storage_file', $id);
      }

      if ($this->hasValidForMobileFileCreation($file) && $file->file_id===$this->lookupFile($file->file_id, 'thumb.mobile')) {
        $this->addDeviceSizeImages($file);
      }
      $this->_files[$key] = $file;
    }

    return $this->_files[$key];
  }

  public function lookupFile($id, $relationship) {
    // Cached locally
    if (!isset($this->_relationships[$id][$relationship])) {
      $temprelationship = str_replace('thumb.', '', $relationship);
      if (isset($this->_deviceSize[$temprelationship])) {
        $this->setMobileStorageTableName();
      }
      // Lookup in db
      $select = $this->select()
              ->from($this->info('name'), 'file_id')
              ->where('parent_file_id = ?', $id)
              ->where('type = ?', $relationship)
              ->limit(1);

      $row = $this->fetchRow($select);
      $this->setStorageTableName();
      if (null === $row) {
        $this->_relationships[$id][$relationship] = false;
      } else {
        $this->_relationships[$id][$relationship] = $row->file_id;
      }
    }

    if (empty($this->_relationships[$id][$relationship])) {
      return $id;
    }

    return $this->_relationships[$id][$relationship];
  }

  public function createFile($file, $params) {
    $space_limit = (int) Engine_Api::_()->getApi('settings', 'core')
                    ->getSetting('core_general_quota', 0);

    $tableName = $this->info('name');

    // fetch user
    if (!empty($params['user_id']) &&
            null != ($user = Engine_Api::_()->getItem('user', $params['user_id']))) {
      $user_id = $user->getIdentity();
      $level_id = $user->level_id;
    } else if (null != ($user = Engine_Api::_()->user()->getViewer())) {
      $user_id = $user->getIdentity();
      $level_id = $user->level_id;
    } else {
      $user_id = null;
      $level_id = null;
    }

    // member level quota
    if (null !== $user_id && null !== $level_id) {
      $space_limit = (int) Engine_Api::_()->authorization()->getPermission($level_id, 'user', 'quota');
      if ($space_limit) {
        $space_used = (int) $this->select()
                        ->from($tableName, new Zend_Db_Expr('SUM(size) AS space_used'))
                        ->where("user_id = ?", (int) $user_id)
                        ->query()
                        ->fetchColumn(0);
        $space_required = (is_array($file) && isset($file['tmp_name']) ? filesize($file['tmp_name']) : filesize($file));

        if ($space_limit > 0 && $space_limit < ($space_used + $space_required)) {
          throw new Engine_Exception("File creation failed. You may be over your " .
          "upload limit. Try uploading a smaller file, or delete some files to " .
          "free up space. ", self::SPACE_LIMIT_REACHED_CODE);
        }
      }
    }
    $row = $this->createRow();
    $row->setFromArray($params);
    $row->store($file);

    return $row;
  }

  public function createSystemFile($file) {
    $row = $this->createRow();
    $row->setFromArray(array(
        'parent_type' => 'system',
        'parent_id' => 1, // Hack
        'user_id' => null,
    ));
    $row->store($file);
    return $row;
  }

  public function createTemporaryFile($file) {
    $row = $this->createRow();
    $row->setFromArray(array(
        'parent_type' => 'temporary',
        'parent_id' => 1, // Hack
        'user_id' => null,
    ));
    $row->store($file);
    return $row;
  }

  public function gc() {
    // Delete temporary files
    $this->delete(array(
        'parent_type = ?' => 'temporary',
        'creation_date <= ?' => new Zend_Db_Expr('DATE_SUB(NOW(),INTERVAL 1 DAY)'),
    ));

    // @todo should we also check for missing parent/user here?

    return $this;
  }

  public function getStorageLimits() {
    return array(
        '1048576' => '1 MB',
        '5242880' => '5 MB',
        '26214400' => '25 MB',
        '52428800' => '50 MB',
        '104857600' => '100 MB',
        '524288000' => '500 MB',
        '1073741824' => '1 GB',
        '2147483648' => '2 GB',
        '5368709120' => '5 GB',
        '10737418240' => '10 GB',
        0 => 'Unlimited'
    );
  }

  public function addDeviceSizeImages($file) {
    $tmpFile = $file->temporary();
    $info = @getimagesize($tmpFile);
    if (!$info) {
      return;
    }
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $name = basename($tmpFile);
    foreach ($this->_deviceSize as $devName => $content) {
      try {
        $image = Engine_Image::factory();
        $image->open($tmpFile);

        $width = $content['w'];
        $height = $content['h'];
        if ($devName === 'mobile' && $width > $image->width) {
          $width = $image->width;
        }
        if ($width > $image->width) {
          continue;
        }
        if ($height > $image->height) {
          $height = $image->height;
        }
        $imgPath = $path . DIRECTORY_SEPARATOR . $devName . "_" . $name;
        $image->resize($width, $height)
                ->write($imgPath)
                ->destroy();
        $this->setMobileStorageTableName();
        $relationship = 'thumb.' . $devName;
        $newFile = $this->createFile($imgPath, array(
            'parent_type' => $file->parent_type,
            'parent_id' => $file->parent_id,
            'user_id' => $file->user_id,
            'name' => $name,
            'parent_file_id' => $file->file_id,
            'type' =>  $relationship
        ));
        $this->setStorageTableName();
        
        $this->_relationships[$file->file_id][$relationship] = $newFile->file_id;
        @unlink($imgPath);
      } catch (Exception $e) {
      }
    }
    @unlink($tmpFile);
  }

  public function hasValidForMobileFileCreation($file) {
    return empty($file->parent_file_id) && !empty($file->parent_type) && stripos($file->parent_type, 'photo') !== false && $file->mime_major == 'image';
  }

}