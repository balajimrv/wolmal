<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Menus.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Plugin_Menus
{
  public function canCreateAlbums()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    // Must be able to create albums
    if( !Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') ) {
      return false;
    }
    return true;
  }
	public function canEnableAlbum(){}
	public function canEnableViewAlbum(){}
	public function isLoggedIn(){
		// Must be logged in
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$viewer || !$viewer->getIdentity() ) {
			return false;
		}
		return true;
	}
  public function canViewAlbums()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    // Must be able to view albums
    if( !Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'view') ) {
      return false;
    }
    return true;
  }
}