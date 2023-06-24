<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensionas
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function routeShutdown(Zend_Controller_Request_Abstract $request) {


    if (substr($request->getPathInfo(), 1, 5) == "admin") {
      $module = $request->getModuleName();
      $controller = $request->getControllerName();
      $action = $request->getActionName();

      if ($module == 'sitemobile' && $controller == 'admin-content' && $action == 'index') {
        $sitepageLayoutCreate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate');
        if (!empty($sitepageLayoutCreate)) {
          $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page', null);
          if (!empty($page_id)) {
            $corepageTable = Engine_Api::_()->getDbtable('pages', 'sitemobile');
            $corepageTableName = $corepageTable->info('name');
            $select = $corepageTable->select()
                    ->from($corepageTableName)
                    ->where('page_id' . ' = ?', $page_id)
                    ->where('name' . ' = ?', 'sitepage_index_view')
                    ->limit(1);
            $corepageTableInfo = $corepageTable->fetchRow($select);
          }
          if (!empty($corepageTableInfo)) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoRoute(array('module' => 'sitepage', 'controller' => 'mobile-layout', 'action' => 'layout', 'page' => $page_id), 'admin_default', false);
          }
        }
      }
    }

    // CHECK IF ADMIN
    if (Zend_Registry::isRegistered('advancedmembers_enabled')) {
      Engine_Api::_()->getApi('settings', 'core')->advancedmembers_enabled = Zend_Registry::get('advancedmembers_enabled');
    }
    $module = $request->getModuleName();
    $apiSitemobile = Engine_Api::_()->sitemobile();
    if (!$apiSitemobile->checkMode('tablet-mode') && !$apiSitemobile->checkMode('mobile-mode')) {
      if (substr($request->getPathInfo(), 1, 5) == "admin" || substr($request->getPathInfo(), 1, 23) == 'sitemobile/theme-roller') {
        return;
      }
      if ($module == 'sitemobile') {
        $request->setModuleName('core');
        $request->setControllerName('index');
        $request->setActionName('index');
      }
      return;
    }

    $request = $this->customizationsEquivalentSupportedRequest($request);
    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();


    $photoGallery = $request->getParam('photoGallery', null);
    if (!empty($photoGallery) && $apiSitemobile->isSupportedModulePhotoGallery($module)) {
      $request->setModuleName('sitemobile');
      $request->setControllerName('photo-gallery');
      $request->setActionName('view');
      $request->setParam("module_name", $module);
      $module = 'sitemobile';
      $format = $request->setParam("format", 'frame');
    }
    if ($module == 'user' && $controller == "profile" && $action == "index") {
      $request->setControllerName('mobile-profile');
    }

    if ($module != 'sitemobile' && $module != 'sitemobileapp') {
      $sr_response = $apiSitemobile->setupRequest($request);
      $redirect_success = $sr_response['status'] > 0;
      if (!$redirect_success) {
        $request->setModuleName('sitemobile');
        $request->setControllerName('error');
        $request->setActionName('notsupport');
        $sr_response = $apiSitemobile->setupRequest($request);
      }
    }
    $apiSitemobile->setContentStorage();

    $format = $request->getParam("format");
    if ($format == 'smoothbox') {
      $request->setParam("contentType", 'dialog');
    }

    if ($format != 'smoothbox' && $request->getParam("contentType") == 'dialog') {
      $request->setParam("format", 'smoothbox');
    }
    $formatType = $request->getParam("formatType");
    if ($format == 'html') {
      $request->setParam("formatType", 'html');
    } else if (!$formatType && $this->isAjaxPageRequest($request)) {
      $request->setParam("formatType", 'smjson');
    }
    $this->loadStorageServie();
    $this->loadStorageFiles();
    // Create layout
    $layout = Zend_Layout::startMvc();
    // Set options
    $layout->setViewBasePath(APPLICATION_PATH . "/application/modules/Sitemobile/layouts", 'Core_Layout_View')
            ->setViewSuffix('tpl')
            ->setLayout(null);
    // Add themes
    $theme = null;
    $themes = array();
    $themesInfo = array();
    $themeTable = Engine_Api::_()->getDbtable('themes', 'sitemobile');

    $themeSelect = $themeTable->select()
            ->where('active = ?', 1)
            ->limit(1);
    $theme = $themeTable->fetchRow($themeSelect);

    if ($theme) {
      $themes[] = $theme->name;
      $themesInfo[$theme->name] = include APPLICATION_PATH_COR . DS
              . 'themes/sitemobile_tablet' . DS . $theme->name . DS . 'manifest.php';
    }

    $layout->themes = $themes;
    $layout->themesInfo = $themesInfo;
    Zend_Registry::set('Themes', $themesInfo);
    Zend_Registry::get('Zend_View')->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemobile/View/Helper', 'Sitemobile_View_Helper');
    Zend_Registry::get('Zend_View')->registerHelper(new Sitemobile_View_Helper_HeadTranslate(), 'headTranslate');
  }

  protected function isAjaxPageRequest(Zend_Controller_Request_Abstract $request) {
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || $request->getParam('REQUEST_TYPE') == 'xmlhttprequest';
  }

  private function customizationsEquivalentSupportedRequest(Zend_Controller_Request_Abstract $request) {
    $reqModule = $module = $request->getModuleName();
    $reqController = $controller = $request->getControllerName();
    $reqAction = $action = $request->getActionName();
    
    switch ($module) {
      case 'core':
      case 'shoppingtheme':
        $module = 'core';
        if ($controller == 'pages' && $action == 'grandopening') { //GRANDOPNING PLUGIN EQUIVALENT SE CORE 
          $controller = 'index';
          $action = 'landing';
        } elseif ($controller == 'index' && $action == 'index') {
          $action = 'landing';
        }
        break;
//      case 'sitealbum':
      case 'ialbum':
      case 'headvancedalbum': //HE ADVANCED PHOTO ALBUMS PLUGIN EQUIVALENT SE ALBUM 
        $module = 'album';
        if ($reqModule == 'headvancedalbum' && $action == 'view' && $controller == 'index') {
          $controller = 'album';
        }
         if ($reqModule == 'sitealbum' && $action == 'index' && $controller == 'index') {
          $action = 'browse';
        }
        break;
      case 'pagealbum': // HIREEXPERTS PAGE ALBUM PLUGIN EQUIVALENT SE VIDEO
        if ($controller == 'albums' && ($action == 'browse' || $action == 'manage')) {
          $module = 'album';
          $controller = 'index';
        }
        break;
      case 'ynadvsearch':
        $module = 'core'; // YOUNET ADVANCED SEARCH PLUGIN EQUIVALENT SE CORE 
        break;
      case 'pageblog': // HIREEXPERTS PAGE BLOG PLUGIN EQUIVALENT SE VIDEO
        if ($controller == 'blogs' && $action == 'browse') {
          $module = 'blog';
          $controller = 'index';
          $action = 'index';
        } elseif ($controller == 'blogs' && $action == 'manage') {
          $module = 'blog';
          $controller = 'index';
        }
        break;
      case 'ynblog':
        $module = 'blog'; // YOUNET ADVANCED BLOG PLUGIN EQUIVALENT SE BLOG 
        break;
      case 'pageevent': // HIREEXPERTS PAGE EVENT PLUGIN EQUIVALENT SE VIDEO
        if ($controller == 'events' && ($action == 'browse' || $action == 'manage')) {
          $module = 'event';
          $controller = 'index';
        }
        break;
      case 'ynevent':
      case 'heevent':
        $module = 'event'; // YOUNET ADVANCED EVENT PLUGIN EQUIVALENT SE EVENT 
        break;
      case 'ynforum':
        $module = 'forum'; // YOUNET ADVANCED FORUM PLUGIN EQUIVALENT SE EVENT 
        break;
      case 'ynmusic':
        $module = 'music'; // YOUNET ADVANCED MUSIC PLUGIN EQUIVALENT SE MUSIC 
        break;
      case 'advgroup':
        $module = 'group'; // YOUNET ADVANCED GROUP PLUGIN EQUIVALENT SE GROUP 
        break;
      case 'avatar': // YOUNET AVATAR PLUGIN PLUGIN EQUIVALENT SE USER 
      case 'socialdna': //SOICAL DNA PLUGIN EQUIVALENT SE USER 
      case 'timeline': //WebHive Team Timeline PLUGIN EQUIVALENT SE USER 
        $module = 'user';
      case 'user':
        if ($controller == 'auth' && $action == 'login') {
          $action = 'login-mobile';
        } elseif ($controller == 'signup' && $action == 'index') {
          $action = 'index-mobile';
        }
        break;
      case 'advancedmembers': // SpurIT Advanced Members PLUGIN EQUIVALENT SE VIDEO
        if ($controller == 'page' && $action == 'index') {
          $module = 'user';
          $controller = 'index';
          $action = 'browse';
        }
        break;
      case 'pagevideo': // HIREEXPERTS PAGE VIDEO PLUGIN EQUIVALENT SE VIDEO
        if ($controller == 'videos' && ($action == 'browse' || $action == 'manage')) {
          $module = 'video';
          $controller = 'index';
        }
        break;
      case 'siteforum':
        $module = 'forum'; // YOUNET ADVANCED VIDEO PLUGIN EQUIVALENT SE VIDEO
//        if ($controller == 'index' && $action == 'index') {
//          $action = 'browse';
//        }
        break;
    case 'sitevideo':
        
        $module = 'video'; // YOUNET ADVANCED VIDEO PLUGIN EQUIVALENT SE VIDEO
        if ($controller == 'video' && $action == 'index') {
          $action = 'browse';
          $controller = 'index';
        } else if($controller == 'video' && $action == 'manage') {
            $action = 'manage';
          $controller = 'index';
        } else if($controller == 'video' && $action == 'create') {
            $action = 'create';
          $controller = 'index';
        }   else if($controller == 'video' && $action == 'view') {
            $action = 'view';
          $controller = 'index';
          $module = 'video';
        }
        break;
      case 'ynvideo':
        $module = 'video'; // YOUNET ADVANCED VIDEO PLUGIN EQUIVALENT SE VIDEO
        if ($controller == 'index' && $action == 'index') {
          $action = 'browse';
        }
        break;
        case 'siteadvsearch':
        $module = 'core'; // YOUNET ADVANCED VIDEO PLUGIN EQUIVALENT SE VIDEO
        if ($controller == 'index' && $action == 'index') {
          $controller = 'search';
        }
        break;
        
       case 'advancedmessages':
       case 'advmessages': 
        $module = 'messages'; // YOUNET ADVANCED MESSAGE PLUGIN EQUIVALENT SE VIDEO
        $controller = 'messages';
        if($action == 'index')
          $action = $request->getParam("action");
        break;  
        
      default:
        return $request;
    }
    if ($reqModule != $module) {
      $request->setModuleName($module);
      $request->setParam("module", $module);
    }
    if ($reqController != $controller) {
      $request->setControllerName($controller);
      $request->setParam("controller", $controller);
    }
    if ($reqAction != $action) {
      $request->setActionName($action);
      $request->setParam("action", $action);
    }

    return $request;
  }

  public function onRenderLayoutMobileSMDefault($event, $mode = null) {
    $view = $event->getPayload();
    if (!($view instanceof Zend_View_Interface)) {
      return;
    }
    $settings = Engine_Api::_()->getDbtable('settings', 'core');
    // Google analytics
    if (($code = $settings->core_analytics_code)) {
      $code = $view->string()->escapeJavascript($code);
      $script = <<<EOF
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '$code']);
_gaq.push(['_trackPageview']);

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
EOF;
      $view->headScriptSM()->appendScript($script);
    }
  }

  public function loadStorageServie() {
    if (!Engine_API::_()->sitemobile()->isApp())
      return;
    $loader = Engine_Loader::load('Sitemobile_Loader');
    $loader->setComponentsObject('Sitemobileapp_Model_DbTable_Services', 'Storage_Model_DbTable_Services');
  }

  public function loadStorageFiles() {
      
    $class = 'Sitemobile_Loader';
    Engine_Loader::loadClass($class);
    $loader = new $class();            

    $loader->setComponentsObject('Sitemobile_Model_DbTable_Files', 'Storage_Model_DbTable_Files');
  }

}
