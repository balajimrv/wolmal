<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FormCalenderDateTime.php 9747 2012-07-26 02:08:08Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_View_Helper_FormCalendarDateTime extends Zend_View_Helper_FormElement {

  public function formCalendarDateTime($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n") {
    $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
    extract($info); // name, value, attribs, options, listsep, disable
    // Get date format
    if (isset($attribs['dateFormat'])) {
      $dateFormat = $attribs['dateFormat'];
      //unset($attribs['dateFormat']);
    } else {
      $dateFormat = 'ymd';
    }

    // Get use military time
    if (isset($attribs['useMilitaryTime'])) {
      $useMilitaryTime = $attribs['useMilitaryTime'];
      //unset($attribs['useMilitaryTime']);
    } else {
      $useMilitaryTime = true;
    }

    // Check value type
    if (is_string($value) && preg_match('/^(\d{4})-(\d{2})-(\d{2})( (\d{2}):(\d{2})(:(\d{2}))?)?$/', $value, $m)) {
      $tmpDateFormat = trim(str_replace(array('d', 'y', 'm'), array('/%3$d', '/%1$d', '/%2$d'), $dateFormat), '/');
      $value = array();

      // Get date
      $value['date'] = sprintf($tmpDateFormat, $m[1], $m[2], $m[3]);
      if ($value['date'] == '0/0/0') {
        unset($value['date']);
      }

      // Get time
      if (isset($m[6])) {
        $value['hour'] = $m[5];
        $value['minute'] = $m[6];
        if (!$useMilitaryTime) {
          $value['ampm'] = ( $value['hour'] >= 12 ? 'PM' : 'AM' );
          if (0 == (int) $value['hour']) {
            $value['hour'] = 12;
          } else if ($value['hour'] > 12) {
            $value['hour'] -= 12;
          }
        }
      }
    }

    if (!is_array($value)) {
      $value = array();
    }


    // Prepare javascript
    // Prepare month and day names
    $localeObject = Zend_Registry::get('Locale');

    $months = Zend_Locale::getTranslationList('months', $localeObject);
    if ($months['default'] == NULL) {
      $months['default'] = "wide";
    }
    $months = $months['format'][$months['default']];

    $days = Zend_Locale::getTranslationList('days', $localeObject);
    if ($days['default'] == NULL) {
      $days['default'] = "wide";
    }
    $days = $days['format'][$days['default']];

    $calendarFormatString = trim(preg_replace('/\w/', '$0/', $dateFormat), '/');
    $calendarFormatString = str_replace('y', 'Y', $calendarFormatString);

//    $this->view->headLinkSM()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitemobile/externals/styles/datepicker.css');

    $attribs['dateAttribs'] = array_merge(array('name' => $name . '[date]', 'id' => $name . '-date'), (array) @$attribs['dateAttribs']);
    return
            "<input type='hidden' data-type='date'"
            . $this->_htmlAttribs($attribs['dateAttribs'])
            . "value='" . @$value['date'] . "'/>"
            . $this->view->formTime($name, $value, $attribs, $options)
    ;
  }

}