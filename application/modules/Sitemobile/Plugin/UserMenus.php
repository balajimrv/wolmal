<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UserMenus.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Plugin_UserMenus {

  // core_main

  public function onMenuInitialize_CoreMainHome($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $route = array(
        'route' => 'default',
    );

    if ($viewer->getIdentity()) {
      $route['route'] = 'user_general';
      $route['params'] = array(
          'action' => 'home',
      );
      if ('user' == $request->getModuleName() &&
              'index' == $request->getControllerName() &&
              'home' == $request->getActionName()) {
        $route['active'] = true;
      }
    }
    if ($row->params)
      $route = array_merge($row->params, $route);
    return $route;
  }

}
