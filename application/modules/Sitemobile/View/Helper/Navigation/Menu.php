<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Menu.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_Navigation_Menu extends Zend_View_Helper_Navigation_Menu {

  /**
   * View helper entry point:
   * Retrieves helper and optionally sets container to operate on
   *
   * @param  Zend_Navigation_Container $container  [optional] container to
   *                                               operate on
   * @return Zend_View_Helper_Navigation_Menu      fluent interface,
   *                                               returns self
   */
  public function menu(Zend_Navigation_Container $container = null) {
    if (null !== $container) {
      $this->setContainer($container);
    }
    $this->setPartial(array('_navigation.tpl', 'sitemobile'));
    return $this;
  }

}