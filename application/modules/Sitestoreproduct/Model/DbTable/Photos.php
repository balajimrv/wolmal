<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photos.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Photos extends Engine_Db_Table {

  protected $_rowClass = 'Sitestoreproduct_Model_Photo';

  public function getPhotoId($product_id = null, $file_id = null) {

    $photo_id = 0;
    $photo_id = $this->select()
            ->from($this->info('name'), array('photo_id'))
            ->where("product_id = ?", $product_id)
            ->where("file_id = ?", $file_id)
            ->query()
            ->fetchColumn();

    return $photo_id;
  }

  /**
   * Return photos
   *
   * @param string $product_id
   * @return photos
   */
  public function GetProductPhoto($product_id, $params = array()) {

    $select = $this->select()
            ->where('product_id = ?', $product_id);
    if (isset($params['show_slidishow']))
      $select->where('show_slidishow = ?', $params['show_slidishow']);

    if (isset($params['limit']) && !empty($params['limit']))
      $select->limit($params['limit']);

    if (isset($params['order']) && !empty($params['order']))
      $select->order($params['order']);
    return $this->fetchAll($select)->toArray();
  }

}