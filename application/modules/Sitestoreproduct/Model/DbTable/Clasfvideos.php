<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Clasfvideos.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Clasfvideos extends Engine_Db_Table {

  protected $_rowClass = 'Sitestoreproduct_Model_Clasfvideo';

  public function getProductVideos($product_id = 0, $pagination = 0, $type_video = null) {

    if ($type_video && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video')) {
      //VIDEO TABLE NAME
      $videoTableName = $this->info('name');

      //GET CORE VIDEO TABLE
      $coreVideoTable = Engine_Api::_()->getDbtable('videos', 'video');
      $coreVideoTableName = $coreVideoTable->info('name');

      //MAKE QUERY
      $select = $coreVideoTable->select()
              ->setIntegrityCheck(false)
              ->from($coreVideoTableName)
              ->join($videoTableName, $coreVideoTableName . '.video_id = ' . $videoTableName . '.video_id', array())
              ->group($coreVideoTableName . '.video_id')
              ->where($videoTableName . '.product_id = ?', $product_id);
    } else {
      //VIDEO TABLE NAME
      $videoTableName = $this->info('name');

      //GET CORE VIDEO TABLE
      $reviewVideoTable = Engine_Api::_()->getDbtable('videos', 'sitestoreproduct');
      $reviewVideoTableName = $reviewVideoTable->info('name');

      //MAKE QUERY
      $select = $reviewVideoTable->select()
              ->from($reviewVideoTableName)
              ->where($reviewVideoTableName . '.product_id = ?', $product_id);
    }
    //FETCH RESULTS
    if (!empty($pagination)) {
      return Zend_Paginator::factory($select);
    } elseif ($type_video) {
      return $row = $coreVideoTable->fetchAll($select);
    } else {
      return $row = $reviewVideoTable->fetchAll($select);
    }
  }

}