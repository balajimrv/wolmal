<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FormButton.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_FormButton extends Zend_View_Helper_FormButton {

  /**
   * Generates a 'button' element.
   *
   * @access public
   *
   * @param string|array $name If a string, the element name.  If an
   * array, all other parameters are ignored, and the array elements
   * are extracted in place of added parameters.
   *
   * @param mixed $value The element value.
   *
   * @param array $attribs Attributes for the element tag.
   *
   * @return string The element XHTML.
   */
  public function formButton($name, $value = null, $attribs = null) {
    $info = $this->_getInfo($name, $value, $attribs);
    extract($info); // name, id, value, attribs, options, listsep, disable
    // Get content
    $content = '';
    if (isset($attribs['content'])) {
      $content = $attribs['content'];
      unset($attribs['content']);
    } else {
      $content = $value;
    }

    // Ensure type is sane
    $type = 'button';
    if (isset($attribs['type'])) {
      $attribs['type'] = strtolower($attribs['type']);
      if (in_array($attribs['type'], array('submit', 'reset', 'button'))) {
        $type = $attribs['type'];
      }
      unset($attribs['type']);
    }

    // build the element
    if ($disable) {
      $attribs['disabled'] = 'disabled';
    }

    $content = ($escape) ? $this->view->escape($content) : $content;

    $xhtml = '<button'
            . ' name="' . $this->view->escape($name) . '"'
            . ' id="' . $this->view->escape($id) . '"'
            . ' type="' . $type . '"';

    // add a value if one is given
    if (!empty($value)) {
      $xhtml .= ' value="' . $this->view->escape($value) . '"';
    }
    $xhtml .= $this->view->dataHtmlAttribs('form_button_' . $type) . " ";
    // add attributes and close start tag
    $xhtml .= $this->_htmlAttribs($attribs) . '>';


    // add content and end tag
    $xhtml .= $content . '</button>';

    return $xhtml;
  }

}