<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FormMultiCheckbox.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_FormMultiCheckbox extends Sitemobile_View_Helper_FormRadio {

  /**
   * Input type to use
   * @var string
   */
  protected $_inputType = 'checkbox';

  /**
   * Whether or not this element represents an array collection by default
   * @var bool
   */
  protected $_isArray = true;

  /**
   * Generates a set of checkbox button elements.
   *
   * @access public
   *
   * @param string|array $name If a string, the element name.  If an
   * array, all other parameters are ignored, and the array elements
   * are extracted in place of added parameters.
   *
   * @param mixed $value The checkbox value to mark as 'checked'.
   *
   * @param array $options An array of key-value pairs where the array
   * key is the checkbox value, and the array value is the radio text.
   *
   * @param array|string $attribs Attributes added to each radio.
   *
   * @return string The radio buttons XHTML.
   */
  public function formMultiCheckbox($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n") {
    return
            '<input type="hidden" name="' . rtrim($name, '[]') . '" value="" />' .
            $this->formRadio($name, $value, $attribs, $options, $listsep);
  }

}