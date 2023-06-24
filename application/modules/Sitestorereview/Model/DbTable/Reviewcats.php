<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Reviewcats.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Model_DbTable_Reviewcats extends Engine_Db_Table {

  protected $_rowClass = 'Sitestorereview_Model_Reviewcat';

	/**
   * Return review parameters data according to store category
   *
   * @param Int store_id
	 * @param Int viewer_id
   * @return Zend_Db_Table_Select
   */
	public function reviewParams($category_id) {
    $select = $this->select()
                    ->from($this->info('name'), array('reviewcat_id', 'reviewcat_name'))
                    ->where('category_id = ?', $category_id);
    return $this->fetchAll($select);
	}

	/**
   * Return review parameters and category data
   *
   * @return Zend_Db_Table_Select
   */
	public function reviewCatParams() {

    $tableStoreCats = Engine_Api::_()->getDbtable('categories', 'sitestore');
    $tableStoreCatsName = $tableStoreCats->info('name');

		$tableReviewCatsName = $this->info('name');

    $select = $tableStoreCats->select()
                    ->setIntegrityCheck(false)
                    ->from($tableStoreCatsName)
                    ->joinLeft($this->info('name'), "$tableStoreCatsName.category_id = $tableReviewCatsName.category_id", array('reviewcat_name', 'reviewcat_id'))
                    ->where($tableStoreCatsName . ".cat_dependency = ?", 0);
    return $tableStoreCats->fetchAll($select);

	}

}
?>