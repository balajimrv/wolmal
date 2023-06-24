<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: HeadTranslate.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_HeadTranslate extends Zend_View_Helper_Abstract {

  public function headTranslate($string = null) {
    if (null !== $string) {
      if (is_array($string)) {
        foreach ($string as $subString) {
          $this->_getContainer()->append($subString);
        }
      } else {
        $this->_getContainer()->append($string);
      }
    }

    return $this;
  }

  public function render() {
    if ($this->_getContainer()->count() <= 0) {
      return Zend_Json::encode(array());
      ;
    }

    $translateDatas = $this->_getContainer()->getArrayCopy();
    $arrayFormData = array();
    foreach ($translateDatas as $key => $data) {
      if (is_array($data) && count($data) == 2) {
        $arrayFormData[] = $data;
        unset($translateDatas[$key]);
      }
    }
    // Data
    $vars = array_flip(array_unique($translateDatas));
    foreach ($vars as $key => &$value) {
      if(is_array($key))
        continue;
      $value = @$this->view->translate($key);
    }

    $varsArray = array();
    foreach ($vars as $key => $value) {
      $varsArray[$key] = $value;
    }

    foreach ($arrayFormData as $key => $value) {
      $varsArray[$value[0]] = array($this->view->translate(array($value[0], $value[1], 1)), $this->view->translate(array($value[0], $value[1], 2)));
    }

    return Zend_Json::encode($varsArray);
  }
  public function __toString()
  {
    return $this->render();
  }
  public function toString()
  {
    return $this->render();
  }
  /**
   * Get the container
   * 
   * @return ArrayObject
   */
  protected function _getContainer() {
    if (!Zend_Registry::isRegistered(get_class($this))) {
      $container = new ArrayObject();
      Zend_Registry::set(get_class($this), $container);
    } else {
      $container = Zend_Registry::get(get_class($this));
    }
    return $container;
  }

}