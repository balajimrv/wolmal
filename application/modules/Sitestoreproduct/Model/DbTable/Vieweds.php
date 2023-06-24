<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Vieweds.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Vieweds extends Engine_Db_Table {

  protected $_rowClass = "Sitestoreproduct_Model_Viewed";

  public function setVieweds($product_id, $viewer_id) {
    
    if(empty($viewer_id)) {
      return;
    }
    
    //GET IF ENTRY IS EXIST FOR SAME PRODUCT AND SAME VIEWER ID
    $select = $this->select()
            ->where('product_id = ?', $product_id)
            ->where('viewer_id = ?', $viewer_id);    
    $vieweds = $this->fetchRow($select);

    if (empty($vieweds)) {
      $row = $this->createRow();
      $row->product_id = $product_id;
      $row->viewer_id = $viewer_id;
      $row->date = new Zend_Db_Expr('NOW()');
      $row->save();      
    }
    else {
      $vieweds->date = new Zend_Db_Expr('NOW()');
      $vieweds->save();     
    }
    
		//DELETE ENTRIES IF MORE THAN 10
		$count = $this->select()
						->from($this->info('name'), array('COUNT(viewed_id) as total_entries'))
						->where('viewer_id = ?', $viewer_id)
						->query()
						->fetchColumn();
		if($count > 10) {
			
			//DELETE ENTRIES IF MORE THAN 10
			$select = $this->select()
							->from($this->info('name'), array('viewed_id'))
							->where('viewer_id = ?', $viewer_id)
							->order('date ASC')
							->limit($count-10);
			$oldDatas = $this->fetchAll($select);
			foreach($oldDatas as $oldData) {
				$this->delete(array('viewed_id = ?' => $oldData->viewed_id));
			}
		}       
  }

}
