<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: HeadSplashScreen.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_View_Helper_HeadSplashScreen extends Zend_View_Helper_Placeholder_Container_Standalone {

  protected $_link = array(), $_itemKeys = array('href', 'media','rel');

  //Create spalsh screen icons link and add those links on web page.
  public function headSplashScreen() {
    if (!$this->isIOSDevice())
      return $this;
    $table = Engine_Api::_()->getDbtable('splashscreens', 'sitemobile');
    //check splash screen of same size already exists or not.
    $select = $table->select()
            ->from($table->info('name'));
    $rows = $select->query()->fetchAll();

    //if splash screen of that size not exist then insert row.
    if (count($rows) > 0) {
      // $photosUrl = array();
      foreach ($rows as $row) {
        $key = $row['width'] . 'x' . $row['height'];
        $media=$this->getMedia($key);
        if ($media && $row['file_id']) {
          $file = Engine_Api::_()->getItemTable('storage_file')->getFile($row['file_id'], null);
          if ($file) {
            $fileHref = explode('?', $file->map());
            $this->_link[$key]['href'] = $fileHref[0];
            $this->_link[$key]['size'] = $key;
            $this->_link[$key]['rel'] = 'apple-touch-startup-image';
            $this->_link[$key]['media'] = $media;
          }
        }
      }
    }
    return $this;
  }

  public function toString() {
    $items = array();
    $indent = null;
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
    return true;
  }

  protected function getMedia($key) {
    $media = null;
    switch ($key) {
      /* iPhone */
      case '320x460':
        $media = "(device-width: 320px) and (device-height: 480px)
                 and (-webkit-device-pixel-ratio: 1)";
        break;
      /* iPhone (Retina) */
      case '640x920':
        $media = "(device-width: 320px) and (device-height: 480px)
                 and (-webkit-device-pixel-ratio: 2)";
        break;
      /* iPhone 5 */
      case '640x1096':
        $media = "(device-width: 320px) and (device-height: 568px)
                 and (-webkit-device-pixel-ratio: 2)";
        break;
      /* iPad  portrait */
      case '768x1004':
        $media = "(device-width: 768px) and (device-height: 1024px)
                 and (orientation: portrait)
                 and (-webkit-device-pixel-ratio: 1)";
        break;
      /* iPad landscape */
      case '748x1024':
        $media = "(device-width: 768px) and (device-height: 1024px)
                 and (orientation: landscape)
                 and (-webkit-device-pixel-ratio: 1)";
        break;
      /* iPad  (Retina) portrait */
      case '1536x2008':
        $media = "(device-width: 768px) and (device-height: 1024px)
                 and (orientation: portrait)
                 and (-webkit-device-pixel-ratio: 2)";
        break;
      /* iPad  (Retina) landscape */
      case '1496x2048':
        $media = "(device-width: 768px) and (device-height: 1024px)
                 and (orientation: landscape)
                 and (-webkit-device-pixel-ratio: 2)";
        break;
    }
    return $media;
  }
}