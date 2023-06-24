<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Storage.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Plugin_Storage extends Zend_Controller_Plugin_Abstract {

  public function onStorageFileUpdateBefore($event) {
    $item = $event->getPayload();
    $table = Engine_Api::_()->getDbtable('files', 'sitemobile');
    if ($table->hasValidForMobileFileCreation($item)) {
      $table->setMobileStorageTableName();
      $select = $table->select()
              ->where('parent_file_id = ?', $item->getIdentity())
              ->where('parent_type = ?', $item->parent_type)
              ->where('parent_id = ?', $item->parent_id)
              ->where('service_id <> ?', $item->service_id);
      $files = $table->fetchAll($select);
      if (count($files)) {
        $storage = $item->getStorageService();
        foreach ($files as $file) {
          try {
            $file->move($storage);
          } catch (Exception $e) {
            
          }
        }
      }
      $table->setStorageTableName();
    }
  }

  public function onItemDeleteBefore($event) {
    $item = $event->getPayload();

    if ($item->getType() !== 'storage_file') {
      $table = Engine_Api::_()->getDbtable('files', 'sitemobile');
      $table->setMobileStorageTableName();
      $select = $table->select()
              ->where('parent_type = ?', $item->getType())
              ->where('parent_id = ?', $item->getIdentity());

      foreach ($table->fetchAll($select) as $file) {
        try {
          $file->delete();
        } catch (Exception $e) {
          if (!($e instanceof Engine_Exception)) {
            $log = Zend_Registry::get('Zend_Log');
            $log->log($e->__toString(), Zend_Log::WARN);
          }
        }
      }
      $table->setStorageTableName();
    }
  }

}
