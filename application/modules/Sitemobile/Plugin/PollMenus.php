<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PollMenus.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Plugin_PollMenus {

   public function onMenuInitialize_PollShare($row) {
    $poll = Engine_Api::_()->core()->getSubject('poll');
    if (empty($poll)) {
      return false;
    }

    return array(
        'label' => 'Share',
        'route' => 'default',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => 'poll',
            'id' => $poll->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_PollReport($row) {
    $poll = Engine_Api::_()->core()->getSubject('poll');
    if (empty($poll)) {
      return false;
    }

    return array(
        'label' => 'Report',
        'route' => 'default',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $poll->getGuid(),
        )
    );
  }
  
}