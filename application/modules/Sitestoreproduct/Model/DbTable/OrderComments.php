<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: OrderComments.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_orderComments extends Engine_Db_Table
{
  protected $_name = 'sitestoreproduct_order_comments';

  /**
   * Return buyer comments
   *
   * @param $order_id
   * @param $owner_id
   * @return object
   */
  public function getBuyerComments($order_id, $owner_id)
  {
    $select = $this->select()->from($this->info('name'),array('creation_date', 'comment'))->where('order_id =?', $order_id)->where('owner_id =? AND user_type = 0', $owner_id)->order('creation_date DESC');

    return $this->fetchAll($select);
  }
  
  /**
   * Return seller comments
   *
   * @param $order_id
   * @param array $params
   * @return object
   */
  public function getSellerComments($order_id, $params)
  {
    $select = $this->select()->from($this->info('name'),array('creation_date', 'comment'))->where('order_id =? AND user_type = 1', $order_id);
    if( !empty($params['buyer']) )
    {
      $select->where("buyer_status = 1");
    }
    else if( !empty($params['page_admin']) )
    {
      $select->where("store_admin_status = 1");
    }
    
    $select->order('creation_date DESC');

    return $this->fetchAll($select);
  }
  
  /**
   * Return get siteadmin comment
   *
   * @param $order_id
   * @param array $params
   * @return object
   */
  public function getSiteAdminComments($order_id, $params)
  {
    $select = $this->select()->from($this->info('name'),array('creation_date', 'comment'))->where('order_id =? AND user_type = 2', $order_id);
    if( !isset($params['is_siteadmin_owner']) || empty($params['is_siteadmin_owner']) )
    {
      if( !empty($params['buyer']) )
      {
        $select->where("buyer_status = 1");
      }
      else if( !empty($params['page_admin']) )
      {
        $select->where("store_admin_status = 1");
      }
      else if( !empty($params['page_owner']) )
      {
        $select->where("store_owner_status = 1");
      }
    }
    
    $select->order('creation_date DESC');

    return $this->fetchAll($select);
  }
  
}