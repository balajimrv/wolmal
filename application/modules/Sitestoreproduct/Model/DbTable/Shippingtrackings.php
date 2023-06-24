<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Shippingtrackings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Shippingtrackings extends Engine_Db_Table
{
  protected $_name = 'sitestoreproduct_shipping_trackings';
  protected $_rowClass = 'Sitestoreproduct_Model_Shippingtracking';
  
  /**
   * Return shipping tracking of an order
   *
   * @param $order_id
   * @return object
   */
  public function getShipTracks($order_id) {

    $select = $this->select()
                   ->where('order_id =?', $order_id)
                   ->order('creation_date DESC');
    return $this->fetchAll($select);
  }
}