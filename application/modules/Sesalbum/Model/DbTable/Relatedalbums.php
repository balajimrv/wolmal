<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Relatedalbums.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Model_DbTable_Relatedalbums extends Engine_Db_Table
{
	protected $_name = 'sesalbum_relatedalbums';
  protected $_rowClass = 'Sesalbum_Model_Relatedalbum';	
	public function getitem($params = array()){
		$itemTable = Engine_Api::_()->getItemTable('album');
		$itemTableName = $itemTable->info('name');
		$select = $this->select()
							->from($this->info('name'),array('*'))
							->where('resource_id = ?' ,$params['album_id'])
							->setIntegrityCheck(false);		
		$select->joinLeft($itemTableName, $itemTableName . ".album_id =  ".$this->info('name') . '.album_id');
		$select->where($itemTableName.'.album_id != ?','');
		return Zend_Paginator::factory($select);
	}
}