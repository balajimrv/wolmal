<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  public function __construct($application) {
    parent::__construct($application);
    if (Engine_API::_()->sitemobile()->isSiteMobileModeEnabled()) {
      $this->initViewHelperPath();
      $this->initPaginator();
      //$this->initThemeDataAttribs();
    
          // [ RESOLVED CONFLICTED WITH PROFILE URL
      if(strpos($_SERVER['REQUEST_URI'], '/profileSM/') !==false){
				$_SERVER['REQUEST_URI'] = str_replace('/profileSM/','/profile/',$_SERVER['REQUEST_URI']);
			}
      $route = array(
          'user_profile' => array(
              'route' => 'profile/:id/*',
              'defaults' => array(
                  'module' => 'user',
                  'controller' => 'profile',
                  'action' => 'index'
              )
          )
      );

      Zend_Registry::get('Zend_Controller_Front')->getRouter()->addConfig(new Zend_Config($route));
      // RESOLVED CONFLICTED WITH PROFILE URL ]
  }  
       include_once APPLICATION_PATH . '/application/modules/Sitemobile/controllers/license/license.php';
  }

  public function _bootstrap($resource = null) {
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Sitemobile_Plugin_Core);
    if (Engine_API::_()->sitemobile()->isSiteMobileModeEnabled()) {
      // Initialize contextSwitch helper
      Zend_Controller_Action_HelperBroker::addHelper(new Sitemobile_Controller_Action_Helper_RequireSubject());
      Zend_Controller_Action_HelperBroker::addHelper(new Sitemobile_Controller_Action_Helper_RequireUser());
      Zend_Controller_Action_HelperBroker::addHelper(new Sitemobile_Controller_Action_Helper_RequireAuth());
      //START CODE FOR MOBILE / TABLET PLUGIN : RESOLVED CONFLICTED WITH PROFILE SHORT URL
      if (Zend_Registry::isRegistered('pus_redirect'))
        Zend_Registry::set('pus_redirect', false);
      //END CODE FOR MOBILE / TABLET PLUGIN : RESOLVED CONFLICTED WITH PROFILE SHORT URL
    }
  }

  protected function initPaginator() {
    // Set up default paginator options
    Zend_Paginator::setDefaultScrollingStyle('Sliding');
    Zend_View_Helper_PaginationControl::setDefaultViewPartial(array(
        'pagination/search.tpl',
        'sitemobile'
    ));
  }

}
