<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitestoreevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 6590 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Model_Album extends Core_Model_Item_Collection {

  protected $_parent_type = 'sitestoreoffer_offer';
  protected $_owner_type = 'sitestoreoffer_offer';
  protected $_children_types = array('sitestoreoffer_photo');
  protected $_collectible_type = 'sitestoreoffer_photo';

	/**
   * Get authorization
   *
   * @return authorization value
   */
  public function getAuthorizationItem() {
    return $this->getParent('sitestoreoffer_offer');
  }

	/**
   * Delete child things
   *
   */
  protected function _delete() {
    
    $photoTable = Engine_Api::_()->getItemTable('sitestoreoffer_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach ($photoTable->fetchAll($photoSelect) as $sitestoreofferPhoto) {
      $sitestoreofferPhoto->delete();
    }
    parent::_delete();
  }

}
?>