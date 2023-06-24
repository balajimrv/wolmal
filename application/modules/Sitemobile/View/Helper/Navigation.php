<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Navigation.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_Navigation extends Zend_View_Helper_Navigation {
  /**
   * View helper namespace
   *
   * @var string
   */
  const SMNS = 'Sitemobile_View_Helper_Navigation';

  public function navigation(Zend_Navigation_Container $container = null) {
    if (null !== $container) {
      $this->setContainer($container);
    }

    return $this;
  }

  /**
   * Returns the helper matching $proxy
   *
   * The helper must implement the interface
   * {@link Zend_View_Helper_Navigation_Helper}.
   *
   * @param string $proxy                        helper name
   * @param bool   $strict                       [optional] whether
   *                                             exceptions should be
   *                                             thrown if something goes
   *                                             wrong. Default is true.
   * @return Zend_View_Helper_Navigation_Helper  helper instance
   * @throws Zend_Loader_PluginLoader_Exception  if $strict is true and
   *                                             helper cannot be found
   * @throws Zend_View_Exception                 if $strict is true and
   *                                             helper does not implement
   *                                             the specified interface
   */
  public function findHelper($proxy, $strict = true) {
    if (isset($this->_helpers[$proxy])) {
      return $this->_helpers[$proxy];
    }
    if ($proxy == 'menu' && !$this->view->getPluginLoader('helper')->getPaths(self::SMNS)) {
      $this->view->addHelperPath(
              APPLICATION_PATH . str_replace('_', '/', '_application_modules_' . self::SMNS), self::SMNS);
    } else if (!$this->view->getPluginLoader('helper')->getPaths(self::NS)) {
      $this->view->addHelperPath(
              str_replace('_', '/', self::NS), self::NS);
    }

    if ($strict) {
      $helper = $this->view->getHelper($proxy);
    } else {
      try {
        $helper = $this->view->getHelper($proxy);
      } catch (Zend_Loader_PluginLoader_Exception $e) {
        return null;
      }
    }

    if (!$helper instanceof Zend_View_Helper_Navigation_Helper) {
      if ($strict) {
        // require_once 'Zend/View/Exception.php';
        throw new Zend_View_Exception(sprintf(
                        'Proxy helper "%s" is not an instance of ' .
                        'Zend_View_Helper_Navigation_Helper', get_class($helper)));
      }

      return null;
    }

    $this->_inject($helper);
    $this->_helpers[$proxy] = $helper;

    return $helper;
  }

}