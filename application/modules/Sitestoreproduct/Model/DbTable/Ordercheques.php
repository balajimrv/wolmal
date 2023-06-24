<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Ordercheques.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Ordercheques extends Engine_Db_Table
{
  protected $_name = 'sitestoreproduct_ordercheques';

  /**
   * Return cheque detail
   *
   * @param $cheque_id
   * @return object
   */
  public function getChequeDetail($cheque_id)
  {
    $select = $this->select()
                   ->from($this->info('name'))
                   ->where("ordercheque_id =?", $cheque_id);

    return $select->query()->fetch();
  }
  
}