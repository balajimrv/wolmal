<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_Album extends Core_Model_Item_Collection {

  protected $_parent_type = 'sitestore_store';
  protected $_owner_type = 'user';
  protected $_children_types = array('sitestore_photo');
  protected $_collectible_type = 'sitestore_photo';
  protected $_searchTriggers = false;

  /**
   * Return store object
   *
   * @return store object
   * */
  public function getParent($recurseType = null) {
    
    if($recurseType == null) $recurseType = 'sitestore_store';

    return Engine_Api::_()->getItem($recurseType, $this->store_id);
  }
  
  /**
   * Gets an absolute URL to the album to view this item
   *
   * @param array $params
   * @return string
   */
  public function getHref($params = array()) {
    $tab_id='';
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
		if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.sitemobile-photos-sitestore', $this->store_id, $layout);
		} else {
			$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $this->store_id, $layout);
		}
    $params = array_merge(array(
        'route' => 'sitestore_albumphoto_general',
        'reset' => true,
        'store_id' => $this->store_id,
        'album_id' => $this->getIdentity(),
        'slug' => $this->getSlug(),
        'tab' => $tab_id
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
  }

  /**
   * Return a alubm slug
   *
   * @return slug
   * */
  public function getSlug($str = null, $maxstrlen = 255) {
    
    if( null === $str ) {
      $str = $this->getTitle();
    }

    return Engine_Api::_()->seaocore()->getSlug($str, 225);
  }

  /**
   * Return a album title
   *
   * @return title
   * */
  public function getTitle() {

    return $this->title;
  }

  /**
   * Return a truncate ownername
   *
   * @param int ownername
   * @return truncate ownername
   * */
  public function truncateOwner($owner_name) {

    $tmpBody = strip_tags($owner_name);
    return ( Engine_String::strlen($tmpBody) > 10 ? Engine_String::substr($tmpBody, 0, 10) . '..' : $tmpBody );
  }

  /**
   * Delete Photos
   * */
  protected function _delete() {

    $photoSelect = Engine_Api::_()->getItemTable('sitestore_photo')->select()->where('album_id = ?', $this->getIdentity());
    foreach (Engine_Api::_()->getDbTable('photos', 'sitestore')->fetchAll($photoSelect) as $sitestorePhoto) {
      $sitestorePhoto->delete();
    }
    parent::_delete();
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   * */
  public function tags() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }

}

?>