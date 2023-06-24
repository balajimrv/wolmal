<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Topics.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Topics extends Engine_Db_Table {

  protected $_rowClass = 'Sitestoreproduct_Model_Topic';

  public function getProductTopices($lisiibg_id) {

    //MAKE QUERY
    $select = $this->select()
            ->where('product_id = ?', $lisiibg_id)
            ->order('sticky DESC')
            ->order('modified_date DESC');

    //RETURN RESULTS
    return Zend_Paginator::factory($select);
  }

}