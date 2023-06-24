<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreinvite
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreinvite_Api_Core extends Core_Api_Abstract {

  public function sendSuggestion($reciever_object, $sender_object, $store_id) {
    $suggTable = Engine_Api::_()->getItemTable('suggestion');
    $sugg = $suggTable->createRow();
    $sugg->owner_id = $reciever_object->getIdentity();
    $sugg->sender_id = $sender_object->getIdentity();
    $sugg->entity = 'sitestore';
    $sugg->entity_id = $store_id;
    $sugg->save();

    // Add in the notification table for show in the "update".
    // $reciever_object : Object which are geting suggestion.
    // $sender_obj : Object which are sending suggestion.
    // $sugg : Object from which table we'll link.
    // suggestion_sitestore :notification type.
    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($reciever_object, $sender_object, $sugg, 'store_suggestion');
  }
}