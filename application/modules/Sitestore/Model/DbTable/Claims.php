<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Claims.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Claims extends Engine_Db_Table {

  protected $_rowClass = 'Sitestore_Model_Claim';

	/**
   * Return status
   *
   * @param array params
   * @return status
   */
  public function getClaimStatus($params) {

  	$select = $this->select()->from($this->info('name'), 'status');
		if(isset($params['store_id']) && !empty($params['store_id'])) {
			$select = $select->where('store_id = ?', $params['store_id']);
		}
		if(isset($params['viewer_id']) && !empty($params['viewer_id'])) {
			$select = $select->where('user_id = ?', $params['viewer_id']);
		}
    return $this->fetchRow($select);
  }

	/**
   * Return viewer cliams
   *
   * @param int $viewer_id
   */
	public function getViewerClaims($viewer_id) {

    $claim_id = 0;
    $claim_id = $this
              ->select()
              ->from($this->info('name'), array('claim_id'))
              ->where("user_id = ?", $viewer_id)
              ->order('creation_date')
              ->query()
              ->fetchColumn();
    return $claim_id;	
	}

  /**
   * Gets claim stores
   *
   * @param string $viewer_id
   * @param  Zend_Db_Table_Select
   */
  public function getMyClaimStores($viewer_id) {

    //GET STORE TABLE AND ITS NAME
    $tableStore = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $tableStoreName = $tableStore->info('name');
    $tableClaimName = $this->info('name');
    //SELECT
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($this->info('name'))
            ->joinInner($tableStoreName, "$tableClaimName.store_id = $tableStoreName.store_id", array('store_id', 'photo_id', 'title', 'owner_id'))
            ->where($tableClaimName . '.user_id = ?', $viewer_id)
            ->order('claim_id DESC');

    return Zend_Paginator::factory($select);
  }
}

?>