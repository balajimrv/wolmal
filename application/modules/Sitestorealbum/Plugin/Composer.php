<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id:Composerl.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestorealbum_Plugin_Composer extends Core_Plugin_Abstract {

  public function onAttachSitestorephoto($data) {

    if (!is_array($data) || empty($data['photo_id'])) {
      return;
    }

    $photo = Engine_Api::_()->getItem('sitestore_photo', $data['photo_id']);

    // make the image public
    // CREATE AUTH STUFF HERE
    /*
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
      foreach( $roles as $i=>$role )
      {
      $auth->setAllowed($photo, $role, 'view', ($i <= $roles));
      $auth->setAllowed($photo, $role, 'comment', ($i <= $roles));
      } */

    if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) {
      return;
    }

    return $photo;
  }

}