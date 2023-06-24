<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Plugin_Menus {

  // core_footer

  public function onMenuInitialize_CoreFooterSitemobile($row) {

    $router = Zend_Controller_Front::getInstance()->getRouter();

    // Mobile version visible
    $route = array(
        'uri' => $router->assemble(array(), 'default') . '?switch-mode=mobile',
        'enabled' => 1,
        'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Mobile')),
        'data-ajax' => "false");
    if ($row->params)
      $route = array_merge($row->params, $route);
    return $route;
  }

  public function onMenuInitialize_CoreFooterSitemobileTablet($row) {
    if (!Engine_Api::_()->sitemobile()->enabelTablet())
      return false;
    $router = Zend_Controller_Front::getInstance()->getRouter();
    // Mobile version visible
    $route = array(
        'uri' => $router->assemble(array(), 'default') . '?switch-mode=tablet',
        'enabled' => 1,
        'class' => 'no-dloader',
        'label' =>  sprintf(Zend_Registry::get('Zend_Translate')->_('Tablet')));
    if ($row->params)
      $route = array_merge($row->params, $route);
    return $route;
  }

  // mobile_footer
  public function onMenuInitialize_CoreFooterDesktop($row) {
    if (Engine_Api::_()->sitemobile()->isApp())
      return false;
    $router = Zend_Controller_Front::getInstance()->getRouter();
    $route = array(
        'uri' => $router->assemble(array(), 'default') . '?switch-mode=standard',
        'enabled' => 1,
        'class' => 'no-dloader',
        'data-ajax' => "false");
    if ($row->params)
      $route = array_merge($row->params, $route);
    return $route;
  }

  public function onMenuInitialize_CoreFooterAuth($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity()) {
      $route = array(
          'label' => 'Sign Out',
          'data-ajax' => Engine_Api::_()->sitemobile()->isApp() ? "true" : "false",
          'route' => 'user_logout',
          'class' => 'core_main_singout'
      );
      if ($row->params)
        $route = array_merge($row->params, $route);
      return $route;
    } else {
      $route = array(
          'label' => 'Sign In',
          'route' => 'user_login',
//          'params' => array(
//              // Nasty hack
//              'return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI']),
//          ),
      );
      if ($row->params)
        $route = array_merge($row->params, $route);
      return $route;
    }
  }

  public function onMenuInitialize_CoreFooterSignup($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      $route = array(
          'label' => 'Sign Up',
          'data-ajax' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.spam.signup', 0) && !Engine_Api::_()->sitemobile()->isApp() ? "false" : "true",
          'route' => 'user_signup'
      );
      if ($row->params)
        $route = array_merge($row->params, $route);
      return $route;
    }

    return false;
  }

  // core_main

  public function onMenuInitialize_SitemobileMainHome($row) {
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

  public function onMenuInitialize_SitemobileMainProfile($row) {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
    }


    if ($viewer->getIdentity()) {
      $icon = $viewer->getPhotoUrl('thumb.icon');
      $route = array(
          'label' => $viewer->getTitle(),
          'uri' => $viewer->getHref(),
          'icon' => $icon ? $icon : Zend_Registry::get('StaticBaseUrl') . 'application/modules/User/externals/images/nophoto_user_thumb_icon.png',
      );

      if ('sitemobile' == $request->getModuleName() && 'index' == $request->getControllerName() && 'profile' == $request->getActionName() && $subject !== false) {
        if ($viewer->getIdentity() == $subject->getIdentity()) {
          $route['active'] = true;
        }
      }
      if ($row->params)
        $route = array_merge($row->params, $route);
      return $route;
    }
    return false;
  }

  public function onMenuInitialize_SitemobileMainMessages($row) {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return false;
    }

    // Get permission setting
    $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
    if (Authorization_Api_Core::LEVEL_DISALLOW === $permission) {
      return false;
    }

    $message_count = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl() . '/';

    $route = array(
        'label' => $row->label,
        'bubole' => $message_count,
        'route' => 'messages_general',
        'params' => array(
            'action' => 'inbox'
        )
    );

    if ('messages' == $request->getModuleName() && 'messages' == $request->getControllerName()) {
      $route['active'] = true;
    }
    if ($row->params)
      $route = array_merge($row->params, $route);
    return $route;
  }

  public function onMenuInitialize_SitemobileMainNotifications($row) {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return false;
    }

    $notificationCount = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->hasNotifications($viewer);

    $route = array(
        'label' => $row->label,
        'bubole' => $notificationCount,
        'route' => 'recent_activity',
        'params' => array(
            'action' => ''
        )
    );
    if ($row->params)
      $route = array_merge($row->params, $route);
//     if ('messages' == $request->getModuleName()
//             && 'messages' == $request->getControllerName()) {
//       $route['active'] = true;
//     }

    return $route;
  }

  public function onMenuInitialize_SitemobileMainRequests($row) {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return false;
    }

    $requestsCount = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->hasRequests($viewer);

    $route = array(
        'label' => $row->label,
        'bubole' => $requestsCount,
        'route' => 'recent_request',
        'params' => array(
            'action' => ''
        )
    );
    if ($row->params)
      $route = array_merge($row->params, $route);
//     if ('messages' == $request->getModuleName()
//             && 'messages' == $request->getControllerName()) {
//       $route['active'] = true;
//     }

    return $route;
  }

  public function onMenuInitialize_CoreMainSettings($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity()) {
      $route = array(
          'label' => $row->label,
          'route' => 'user_extended',
          'params' => array(
              'controller' => 'settings',
              'action' => 'general',
          )
      );

      if ($row->params)
        $route = array_merge($row->params, $route);
      return $route;
    }

    return false;
  }

  public function sitemobileSearch($row) {
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if (!$require_check) {
      $viewer = Engine_Api::_()->user()->getViewer();
      if (empty($viewer) || !$viewer->getIdentity()) {
        return false;
      }
    }
    return $row;
  }

  public function sitemobileNotifications($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (empty($viewer) || !$viewer->getIdentity()) {
      return false;
    }

    return $row;
  }

  public function onMenuInitialize_CoreMainCometchat($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }
    
    $route = array(
         'uri' => isset($row->params['uri']) && $row->params['uri'] ? $row->params['uri'] : 'cometchat',
         'data-ajax' => "false",
     ); 
   
    if ($row->params)
      $route = array_merge($row->params, $route);

    return $route;
  }

  public function sitemobileFriends($row) {

    $viewer = Engine_Api::_()->user()->getViewer();
    if (empty($viewer) || !$viewer->getIdentity() || !Engine_Api::_()->sitemobile()->isApp()) {
      return false;
    }
    return $row;
  }

}