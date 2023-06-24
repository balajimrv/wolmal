<?php


/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: RequireSubject.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Controller_Action_Helper_RequireSubject extends
Sitemobile_Controller_Action_Helper_RequireAbstract {

  protected $_errorAction = array('requiresubject', 'error', 'sitemobile');
  protected $_requiredType;
  protected $_actionRequireTypes = array();

  public function direct($type = null) {
    if (null !== $type) {
      $this->setRequiredType($type);
    }
    return parent::direct();
  }

  public function reset() {
    parent::reset();

    $this->_errorAction = array('requiresubject', 'error', 'sitemobile');
    $this->_requiredType = null;
    $this->_actionRequireTypes = array();

    return $this;
  }

  public function checkRequire() {

    try {
      if (Engine_Api::_()->core()->hasSubject()) {
        $subject = Engine_Api::_()->core()->getSubject();
      } else {
        $subject = null;
      }
    } catch (Exception $e) {
      $subject = null;
    }

    $actionName = $this->getFrontController()->getRequest()->getActionName();
    $ret = true;

    if (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity()) {
      $ret = false;
    } else if (null !== $this->_requiredType && $subject->getType() != $this->_requiredType) {
      $ret = false;
    } else if (null !== ($requireType = $this->getActionRequireType($actionName)) &&
            $subject->getType() != $requireType) {
      $ret = false;
    }
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStack()->ViewRenderer;
    $viewRenderer->setViewBasePathSpec(':moduleDir/views');
    if (!$ret && APPLICATION_ENV == 'development' && Zend_Registry::isRegistered('Zend_Log') && ($log = Zend_Registry::get('Zend_Log')) instanceof Zend_Log) {
      $target = $this->getRequest()->getModuleName() . '.' .
              $this->getRequest()->getControllerName() . '.' .
              $this->getRequest()->getActionName();
      $log->log('Require class ' . get_class($this) . ' failed check for: ' . $target, Zend_Log::DEBUG);
    }

    return $ret;
  }

  public function setRequiredType($type = null) {
    $this->_requiredType = $type;
    return $this;
  }

  // Action requires

  public function setActionRequireTypes(array $data) {
    foreach ($data as $key => $value) {
      $this->setActionRequireType($key, $value);
    }
    return $this;
  }

  public function setActionRequireType($action, $type = null) {
    $this->_actionRequireTypes[$action] = $type;
    $this->addActionRequire($action);
    return $this;
  }

  public function hasActionRequireType($action) {
    return ( null !== $this->getActionRequireType($action) );
  }

  public function getActionRequireType($action) {
    if (!isset($this->_actionRequireTypes[$action])) {
      return null;
    }
    return $this->_actionRequireTypes[$action];
  }

  public function removeActionRequireType($action) {
    unset($this->_actionRequireTypes);
    return $this;
  }

}