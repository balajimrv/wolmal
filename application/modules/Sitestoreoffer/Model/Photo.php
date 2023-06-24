<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitestoreevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photo.php 6590 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreoffer_Model_Photo extends Core_Model_Item_Collectible {

  protected $_parent_type = 'sitestoreoffer_album';
  protected $_owner_type = 'user';
  protected $_collection_type = 'sitestoreoffer_album';

	public function getMediaType() {
		return 'photo';
	}
	
  /**
   * Get photo url
   *
   * @param string $type
   * @return photo url
   */
  public function getPhotoUrl($type = null) {
    if (empty($this->file_id)) {
      return null;
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, $type);
    if (!$file) {
      return null;
    }

    return $file->map();
  }

}
?>