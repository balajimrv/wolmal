<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Partial.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_Partial extends Zend_View_Helper_Partial {

  /**
   * Variable to which object will be assigned
   * @var string
   */
  protected $_objectKey;

  /**
   * Renders a template fragment within a variable scope distinct from the
   * calling View object.
   *
   * If no arguments are passed, returns the helper instance.
   *
   * If the $model is an array, it is passed to the view object's assign()
   * method.
   *
   * If the $model is an object, it first checks to see if the object
   * implements a 'toArray' method; if so, it passes the result of that
   * method to to the view object's assign() method. Otherwise, the result of
   * get_object_vars() is passed.
   *
   * @param  string $name Name of view script
   * @param  string|array $module If $model is empty, and $module is an array,
   *                              these are the variables to populate in the
   *                              view. Otherwise, the module in which the
   *                              partial resides
   * @param  array $model Variables to populate in the view
   * @return string|Zend_View_Helper_Partial
   */
  public function partial($name = null, $module = null, $model = null) {
    if ($module == 'storage' && $name == 'upload/upload.tpl') {
      $module = 'sitemobile';
    } else if ($module == 'core' && $name == '_navIcons.tpl') {
      $module = 'sitemobile';
    } else if ($module == 'fields' && $name == '_jsSwitch.tpl') {
      $module = 'sitemobile';
    } else if ((null !== $module) && is_string($module)) {
      // require_once 'Zend/Controller/Front.php';
      $moduleDir = Zend_Controller_Front::getInstance()->getControllerDirectory($module);
      if (null === $moduleDir) {
        // require_once 'Zend/View/Helper/Partial/Exception.php';
        throw new Zend_View_Helper_Partial_Exception('Cannot render partial; module does not exist');
      }
      $viewsDir = dirname($moduleDir) . '/views/sitemobile/scripts/' . $name;
      if (file_exists($viewsDir)) {
        $name = str_replace(APPLICATION_PATH.DS,'',$viewsDir);
        $module = null;
      }
      
    }
    return parent:: partial($name, $module, $model);
  }

}