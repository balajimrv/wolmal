<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Notifyemails.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Notifyemails extends Engine_Db_Table
{
  protected $_name = 'sitestoreproduct_notify_emails';
  
  /**
   * Return notify email detail
   *
   * @param $product_id
   * @param $fetch_column
   * @return string
   */
  public function getNotifyEmail($product_id, $fetch_column)
  {
    return $this->select()
                ->from($this->info('name'), $fetch_column)
                ->where("product_id =?", $product_id)
                ->where("is_notify = 0");
  }
}