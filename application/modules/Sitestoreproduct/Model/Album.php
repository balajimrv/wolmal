<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_Album extends Core_Model_Item_Collection {

  protected $_searchTriggers = false;
  protected $_modifiedTriggers = false;
  protected $_parent_type = 'sitestoreproduct_product';
  protected $_owner_type = 'user';
  protected $_children_types = array('sitestoreproduct_photo');
  protected $_collectible_type = 'sitestoreproduct_photo';

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {
    
    return $this->getOwner()->getHref($params);
  }

  public function getAuthorizationItem() {
    
    return $this->getParent('sitestoreproduct_product');
  }

  protected function _delete() {
    
    //DELTE ALL CHILD POST
    $photoTable = Engine_Api::_()->getItemTable('sitestoreproduct_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach ($photoTable->fetchAll($photoSelect) as $sitestoreproductPhoto) {
      $sitestoreproductPhoto->delete();
    }
    parent::_delete();
  }

}