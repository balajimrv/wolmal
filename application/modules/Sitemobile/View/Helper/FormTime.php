<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FormTime.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_FormTime extends Zend_View_Helper_FormElement {

  public function formTime($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n") {
    $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
    extract($info); // name, value, attribs, options, listsep, disable

    $timeLocaleString = '<fieldset data-role="controlgroup" data-type="horizontal" data-corners="false" data-inset="true" data-mini="true" class="formtime_fieldset">' . '%1$s%2$s' . ( @$attribs['useMilitaryTime'] ? '' : '%3$s' ) . '</fieldset>';

    return sprintf(
                    $timeLocaleString, $this->view->formSelect($name . '[hour]', @$value['hour'], @$attribs['hourAttribs'], $options['hour']), $this->view->formSelect($name . '[minute]', @$value['minute'], @$attribs['minuteAttribs'], $options['minute']), $this->view->formSelect($name . '[ampm]', @$value['ampm'], @$attribs['secondAttribs'], $options['ampm'])
    );
  }

}