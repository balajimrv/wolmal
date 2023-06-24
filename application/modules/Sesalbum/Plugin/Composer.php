<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Composer.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Plugin_Composer extends Core_Plugin_Abstract {
  public function onAttachPhoto($data) {
    if (!is_array($data) || empty($data['photo_id']))
      return;
    $photo = Engine_Api::_()->getItem('album_photo', $data['photo_id']);
    if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
      return;
    if (!empty($data['actionBody']) && empty($photo->description)) {
      $photo->description = $data['actionBody'];
      $photo->save();
    }
    return $photo;
  }
}