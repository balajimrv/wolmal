<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: List.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_List extends Core_Model_Item_Abstract {

  protected $_owner_type = 'sitestore';

  protected $_child_type = 'user';

  public $ignorePermCheck = true;
  protected $_searchTriggers = false;

  public function getListItemTable() {
    return Engine_Api::_()->getItemTable('core_like');
  }

  public function get($child) {
    $table = $this->getListItemTable();
    $select = $table->select()
      ->where('resource_id = ?', $this->store_id)
      ->where('resource_type = ?', 'sitestore_store')
      ->where('poster_id = ?', $child->getIdentity())
      ->limit(1);

    return $table->fetchRow($select);
  }

  public function has(Core_Model_Item_Abstract $child)	{
    return ( null !== $this->get($child) );
  }
}