<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteverify_Plugin_Core
{

  public function onUserDeleteAfter($event)
  {
    $payload = $event->getPayload();
    $user_id = $payload['identity'];
    $verifyTable = Engine_Api::_()->getDbtable('verifies', 'siteverify');
    $select = $verifyTable->select()->where("resource_id = $user_id OR poster_id = $user_id");
    $select = $select->where('resource_type = ?', 'user');
    $rows = $verifyTable->fetchAll($select);
    foreach ($rows as $row)
    {
      $row->delete();
    }
  }
}
