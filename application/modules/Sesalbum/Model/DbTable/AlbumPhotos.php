<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AlbumPhotos.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Model_DbTable_AlbumPhotos extends Engine_Db_Table
{ 
	protected $_rowClass = 'Sesalbum_Model_Photo';
  protected $_name = 'album_photos';
  
  public function getPhotoPaginator(array $params)
  {
    return Zend_Paginator::factory($this->getPhotoSelect($params));
  }
  
	public function getPhotoSelect(array $params)
  {
    $select = $this->select();
    if( !empty($params['album']) && $params['album'] instanceof Sesalbum_Model_Album ) {
      $select->where('album_id = ?', $params['album']->getIdentity());
    } else if( !empty($params['album_id']) && is_numeric($params['album_id']) ) {
      $select->where('album_id = ?', $params['album_id']);
    }
    if( !isset($params['order']) ) {
      $select->order('order ASC');
    } else if( is_string($params['order']) ) {
      $select->order($params['order']);
    }
    return $select;
  }
}