<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: HeadHomeScreenIcon.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_View_Helper_HeadHomeScreenIcon extends Zend_View_Helper_Placeholder_Container_Standalone {

  protected $_link = array();
  protected $_homescreenIcon = array(
      '16' => array('x' => '16', 'y' => '16'),
      '32' => array('x' => '32', 'y' => '32'),
      '57' => array('x' => '57', 'y' => '57'),
      '72' => array('x' => '72', 'y' => '72'),
      '76' => array('x' => '76', 'y' => '76'),
      '114' => array('x' => '114', 'y' => '114'),
      '120' => array('x' => '120', 'y' => '120'),
      '144' => array('x' => '144', 'y' => '144'),
      '152' => array('x' => '152', 'y' => '152'),
      '158' => array('x' => '158', 'y' => '158'));
  protected $_itemKeys = array('href','rel', 'sizes');

  //Create home screen icon's links and add those links on web page.
  public function headHomeScreenIcon() {
    $file_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.homescreen.fileId');

    if ($file_id) {

      foreach ($this->_homescreenIcon as $k => $value) {
        $key = $value['x'] . 'x' . $value['y'];
        $type = 'thumb.' . $key;
        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, $type);
        if ($file && $file->map()) {
          $fileHref = explode('?', $file->map());
          if ($key == '16x16' || $key == '32x32') {
            $this->_link[] = array('rel' => 'icon', 'sizes' => $key, 'href' => $fileHref[0]);
          }  elseif (in_array($k, array('57', '76', '120', '152')) && $this->isIOSDevice()) {
            $this->_link[] = array('rel' => 'apple-touch-icon', 'sizes' => $key, 'href' => $fileHref[0]);
          } elseif ($this->isIOSDevice() || $k == '158') {
            $this->_link[] = array('rel' => 'apple-touch-icon-precomposed', 'sizes' => $key, 'href' => $fileHref[0]);
          } 
        }
      }
    }
    return $this;
  }

  public function toString($indent = null) {
    $items = array();
    foreach ($this->_link as $attributes) {
      $link = '<link ';
      foreach ($this->_itemKeys as $itemKey) {
        if (isset($attributes[$itemKey])) {
          if (is_array($attributes[$itemKey])) {
            foreach ($attributes[$itemKey] as $key => $value) {
              $link .= sprintf('%s="%s" ', $key, ($this->_autoEscape) ? $this->_escape($value) : $value);
            }
          } else {
            $link .= sprintf('%s="%s" ', $itemKey, ($this->_autoEscape) ? $this->_escape($attributes[$itemKey]) : $attributes[$itemKey]);
          }
        }
      }

      if ($this->view instanceof Zend_View_Abstract) {
        $link .= ($this->view->doctype()->isXhtml()) ? '/>' : '>';
      } else {
        $link .= '/>';
      }

      if (($link == '<link />') || ($link == '<link >')) {
        return '';
      }
      $items[] = $link;
    }
    $indent = (null !== $indent) ? $this->getWhitespace($indent) : $this->getIndent();
    return $indent . implode("\n" . $indent, $items);
  }

  protected function isIOSDevice() {
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    return ((false !== stripos($useragent, 'iphone')) || (false !== stripos($useragent, 'ipod')) ||
            (false !== stripos($useragent, 'ipad')) );
  }

}
