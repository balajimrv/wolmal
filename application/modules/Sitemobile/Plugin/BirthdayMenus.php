<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BirthdayMenus.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Plugin_BirthdayMenus {

  public function onMenuInitialize_HasAdd($row) {

    if( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return false;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $result = $row->toarray();
    return array_merge($result, array(
                'route' => 'birthday_extended',
                'params' => array(
                    'controller' => 'index',
                    'action' => 'view'
                )
            ));
  }

}
