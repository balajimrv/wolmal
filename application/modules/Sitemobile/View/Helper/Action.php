<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Action.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_Action extends Zend_View_Helper_Action {

  /**
   * Retrieve rendered contents of a controller action
   *
   * If the action results in a forward or redirect, returns empty string.
   * 
   * @param  string $action 
   * @param  string $controller 
   * @param  string $module Defaults to default module
   * @param  array $params 
   * @return string
   */
  public function action($action, $controller, $module = null, array $params = array()) {

    if (null === $module) {
      $module = $this->defaultModule;
    }
    if ($module == "storage") {
      if ($controller == "upload") {
        $module = 'sitemobile';
      }
    }
    
    if ((($module == "suggestion" && $controller == "widget" && $action == "request-accept") || $module == 'peopleyoumayknow' && $controller == "widget" && $action == "friend") && isset($params['notification'])) {

      if ($params['notification']->type == 'friend_request') {
        $module = 'user';
        $controller = 'friends';
        $action = 'request-friend';
      } elseif ($params['notification']->type == 'friend_follow_request') {
        $module = 'user';
        $controller = 'friends';
        $action = 'request-follow';
      }
    }

    $this->resetObjects();
    if (null === $module) {
      $module = $this->defaultModule;
    }

    // clone the view object to prevent over-writing of view variables
    $viewRendererObj = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
    Zend_Controller_Action_HelperBroker::addHelper(clone $viewRendererObj);

    $this->request->setParams($params)
            ->setModuleName($module)
            ->setControllerName($controller)
            ->setActionName($action)
            ->setDispatched(true);

    $sr_response = Engine_Api::_()->sitemobile()->setupRequest($this->request);

    $this->dispatcher->dispatch($this->request, $this->response);

    // reset the viewRenderer object to it's original state
    Zend_Controller_Action_HelperBroker::addHelper($viewRendererObj);


    if (!$this->request->isDispatched()
            || $this->response->isRedirect()) {
      // forwards and redirects render nothing 
      return '';
    }

    $return = $this->response->getBody();
    $this->resetObjects();
    return $return;
    // return parent::action($action, $controller, $module, $params);
  }

}