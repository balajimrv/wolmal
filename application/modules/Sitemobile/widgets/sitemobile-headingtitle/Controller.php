<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Widget_SitemobileHeadingtitleController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // Don't render this if not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->pageHeaderTitle = $pageHeaderTitle = null;

    if (Zend_Registry::isRegistered('sitemapPageHeaderTitle')) {
      $this->view->pageHeaderTitle = $pageHeaderTitle = Zend_Registry::get('sitemapPageHeaderTitle');
    }
    
    $this->view->displayNonLoggedInPages = $this->_getParam('nonloggedin', 1);
    $defaultTitleShow = 0;
    //FOR APP DEFAULT WILL BE 1
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $moduleName = $request->getModuleName();  
    $controllerName = $request->getControllerName(); 
		$actionName = $request->getActionName();
    $allParams = $request->getParams();
    $pagename = $moduleName . '_' . $controllerName . '_' . $actionName;  
    if(Engine_Api::_()->sitemobile()->isApp())
      $defaultTitleShow = 2;
    $this->view->displayLoggedInPages = $this->_getParam('loggedin', $defaultTitleShow);
    
    if($pagename == 'user_index_home' && $this->view->displayLoggedInPages == 2)
       $this->view->displayLoggedInPages = 0;
    if( !$viewer->getIdentity() && !$this->view->displayNonLoggedInPages) {
      return $this->setNoRender();
    }
    if($viewer->getIdentity()  && !$this->view->displayLoggedInPages) {
      return $this->setNoRender();
    }

  }

}