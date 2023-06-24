<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Birthday_IndexController extends Core_Controller_Action_Standard {

  public function indexAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    // GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('birthday_main');

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $param['viewer_id'] = $viewer_id;
    //check the type of widget admin wants to display for birthday notification at home page
    $this->view->display_action = $display_action = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.widget', 3);

    // GET THE MONTH FROM URL IF PRESENT OTHERWISE SET IT TO THE CURRENT MONTH
    $date = $this->_getParam('date_current', null);
    if (empty($date)) {
      $date = time();
    }

    // unset the limits if admin has set the widget view as calender
    if ($display_action == 3) {
      $param['display_today_birthday'] = "M";
      $param['limit'] = 0;
      $param['active_month'] = $date;
    }

    // get the data about members who have their birthday in the month shown in the calender
    $field_object = Engine_Api::_()->getDbTable('metas', 'birthday')->getFields_birthday($param);

    // Get paginator
    $result = Engine_Api::_()->getDbTable('metas', 'birthday')->fetchAll($field_object);
    $this->view->result = $result;



    // GET THIS, LAST AND NEXT MONTHS
    $this->view->date_current = $date = mktime(0, 0, 0, date("m", $date), 1, date("Y", $date));
    $this->view->date_next = $date_next = mktime(0, 0, 0, date("m", $date) + 1, 1, date("Y", $date));
    $this->view->date_last = $date_last = mktime(0, 0, 0, date("m", $date) - 1, 1, date("Y", $date));

    //GET THE NUMBER OF DAYS IN THE MONTH
    $days_in_month = date('t', $date);

    //GET THE FIRST DAY OF THE MONTH
    $first_day_of_month = date("w", $date);
    if ($first_day_of_month == 0) {
      $first_day_of_month = 7;
    }
    $this->view->first_day_of_month = $first_day_of_month;

    //GET THE LAST DAY OF THE MONTH
    $this->view->last_day_of_month = $last_day_of_month = ($first_day_of_month - 1) + $days_in_month;

    //GET THE TOTAL NUMBER OF CELLS TO BE DISPLAYED IN THE CALENDER TABLE
    $this->view->total_cells = $total_cells = (floor($last_day_of_month / 7) + 1) * 7;

    //GET CURRENT MONTH THAT HAS TO BE DISPLAYED
    $this->view->current_month = $current_month = date("m", $date);

    //GET THE TEXT OF THE CURRENT MONTH
    $this->view->current_month_text = $current_month_text = date("F", $date);

    //GET THE YEAR OF THE CURRENT MONTHS		
    $this->view->current_year = $current_month_text = date("Y", $date);

    // get the base url
    $this->view->sugg_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    $select_content = $contentTable->select()->where('name = ?', 'birthday.show-birthdays');
    $params = $contentTable->fetchRow($select_content);
    if (isset($params->params['title'])) {
      $widget_title = $params->params['title'];
    } else {
      $widget_title = 'Birthdays';
    }
    $this->view->title_widget = $widget_title;
  }

  // IT DETERMINES THE ACTION FOR THE VIEW ALL LINK IN THE BIRTHDAY WIDGET IN CASE OF NON-CALENDER WIDGET
  public function viewAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    // get viewer
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $param['viewer_id'] = $viewer_id;
    $this->view->isajax = $this->_getParam('isajax', 0);

    $this->view->lastHeader = $this->_getParam('lastHeader', null);
    //check Birthday field privacy so as to show age or not
    $metatable = Engine_Api::_()->getDbTable('metas', 'birthday');
    $rmetaName = $metatable->info('name');
    $select = $metatable->select()
            ->from($rmetaName, array('display'))
            ->where('type = ?', 'birthdate')
            ->limit(1);
    $result = $metatable->fetchRow($select);
    $this->view->age_display = $age_display = $result['display'];

    $param['limit'] = 0;
    $param['display_today_birthday'] = 0;

    // initialize arrays
    $birthday_array = array();
    $date_next = array();
    $next_month_text = array();

    // define current date parameters
    $date = time();
    $month_string = "";
    $month_string2 = "";
    $current_date = date('d', $date);
    $current_month = (int) date('m', $date);

    $tomorrow_date = date('d', $date) + 1;

    $tomorrow_date_month_year = date('m-d-Y', mktime(0, 0, 0, date("m"), $tomorrow_date));
    $weekdays_left = 6 - date('w', $date);
    $weekend_date = date('d', $date) + $weekdays_left;
    $weekend_date_month_year = date('m-d-Y', mktime(0, 0, 0, date("m"), $weekend_date));
    $j = 1;
    $week_days = array();
    $week_next_date = $tomorrow_date;
    while ($j < $weekdays_left) {
      $week_next_date = $week_next_date + 1;
      $week_days[$j] = date('m-d-Y', mktime(0, 0, 0, date("m"), $week_next_date));
      $j++;
    }

    $birthday_host = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
    $this->view->current_year = $current_year = date('Y', time());
    $current_date_month = date('m-d', $date);

    $month_string.= $current_month;
    $i = 0;
    while ($i <= 10) {

      $current_month = $current_month + 1;
      if ($current_month == 13) {
        $current_month = 1;
      }
      $month_string.= ',' . $current_month;
      $i = $i + 1;
    }
    $current_month = $current_month + 1;
    if ($current_month == 13) {
      $current_month = 01;
    }
    $day_string = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31";
    $dataarray1 = array();
    $dataarray2 = array();

    // Get Current page number
    $this->view->current_page = $current_page = $this->_getParam('page', 1);

    $this->view->items_per_page = $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.listing', 2);
    $startindex = ($current_page - 1) * $items_per_page;

    $valuetable = Engine_Api::_()->getDbTable('values', 'birthday');
    $rvalueName = $valuetable->info('name');

    // get total number of entries so as to check the condition for not show the birthday widget
    $field_object = Engine_Api::_()->getDbTable('metas', 'birthday')->getFields_birthday($param);
    $field_object_temp = $field_object;
    $field_object_temp->where("DATE_FORMAT(" . $rvalueName . " .value, '%m-%d') >= ?", $current_date_month)
            ->order('Month ASC')
            ->order('Day ASC')
            ->limit($items_per_page, $startindex);
    $dataarray1 = $valuetable->fetchAll($field_object_temp)->toarray();
    $count = count($dataarray1);
    if ($count < $items_per_page) {
      $field_object2 = Engine_Api::_()->getDbTable('metas', 'birthday')->getFields_birthday($param);
      $month_string2.= "1";
      $i2 = 1;
      while ($i2 < $current_month) {
        $i2++;
        $month_string2.= "," . $i2;
      }
      $limit = $items_per_page - $count;
      $startindex = $this->_getParam('startindex', 0);

      $field_object2->where("DATE_FORMAT(" . $rvalueName . " .value, '%m-%d') >= ?", '01-01')
              ->where("DATE_FORMAT(" . $rvalueName . " .value, '%m-%d') < ?", $current_date_month)
              ->order('Month ASC')
              ->order('Day ASC')
              ->limit($limit, $startindex);
      $dataarray2 = $valuetable->fetchAll($field_object2)->toarray();
      $this->view->next_start = count($dataarray2) + $startindex;
      if ($startindex - $items_per_page < 0) {
        $this->view->prev_start = 0;
      } else {
        $this->view->prev_start = $startindex - $items_per_page;
      }
    }

    // count all the birthday entries
    $field_object1 = Engine_Api::_()->getDbTable('metas', 'birthday')->getFields2($param);
    $field_object1->where("DATE_FORMAT(" . $rvalueName . " .value, '%m-%d') >= ?", '01-01')
            ->where("DATE_FORMAT(" . $rvalueName . " .value, '%m-%d') <= ?", '12-31');

    $result_count = Engine_Api::_()->getDbTable('metas', 'birthday')->fetchRow($field_object1);
    if (!empty($result_count)) {
      $total_count = $result_count->total_count;
    } else {
      $total_count = 0;
    }
    $this->view->total_count = $total_count;
    $this->view->total_pages = $total_pages = ceil($total_count / $items_per_page);

    $paginator = array_merge($dataarray1, $dataarray2);
    $this->view->paginator = $paginator;
    $birthday_entry = 0;
    $year_incr = 0;

    // for each birthday entry
    foreach ($paginator as $values) {
      $date_array = explode("-", $values['value']);
      $age = date("Y", time()) - $date_array[0];

      if ($date_array[1] > date("m", time())) {
        $age = $age - 1;
      } elseif ($date_array[1] == date("m", time())) {
        if ($date_array[2] > date("d", time())) {
          $age = $age - 1;
        }
      }

      // for today's birthdays
      if ($current_month == $date_array[1] && $current_date == $date_array[2]) {
        $birthday_array['today'][$birthday_entry][] = $values['item_id'];
        $birthday_array['today'][$birthday_entry][] = $values['value'];
        $birthday_array['today'][$birthday_entry][] = $age;
      }

      // for tomorrow's birthdays
      $tomorrow_array = explode('-', $tomorrow_date_month_year);
      if ($tomorrow_array[0] == $date_array[1] && $tomorrow_array[1] == $date_array[2]) {
        $birthday_array['tomorrow'][$birthday_entry][] = $values['item_id'];
        $birthday_array['tomorrow'][$birthday_entry][] = $values['value'];
        $birthday_array['tomorrow'][$birthday_entry][] = $age;
        $birthday_array['tomorrow'][$birthday_entry][] = $tomorrow_array[2];
      }

      // for this week's birthdays
      $weekend_array = explode('-', $weekend_date_month_year);
      foreach ($week_days as $key => $value) {
        $value_array = explode('-', $value);
        if ($date_array[2] == $value_array[1] && $date_array[1] == $value_array[0]) {
          $birthday_array['week'][$birthday_entry][] = $values['item_id'];
          $birthday_array['week'][$birthday_entry][] = $values['value'];
          $birthday_array['week'][$birthday_entry][] = $age;
          $birthday_array['week'][$birthday_entry][] = $weekend_array[2];
        }
      }

      // for this month's birthday
      if ($weekend_array[0] == $current_month) {
        if ($current_month == $date_array[1] && $weekend_array[1] < $date_array[2] && $date_array[2] > $tomorrow_array[1]) {
          $birthday_array['this_month'][$birthday_entry][] = $values['item_id'];
          $birthday_array['this_month'][$birthday_entry][] = $values['value'];
          $birthday_array['this_month'][$birthday_entry][] = $age;
        }
      }

      // initialize next month and corresponding timestamp to current values
      $next_month = $current_month;
      $date_next[0] = mktime(0, 0, 0, date("m", $date), 1, $current_year);

      // initialize a counter to 1 and then execute a loop 11 times for all the 11 months except the current month
      $i = 1;
      $next_month_flag = 0;
      while ($i <= 11) {
        $next_month = $next_month + 1;
        if ($next_month == 13) {
          $next_month = 1;
        }
        $date_next[$i] = mktime(0, 0, 0, date("m", $date_next[$i - 1]) + 1, 1, $current_year);
        $next_month_text[$i] = date('F', $date_next[$i]);

        // check if month in birthday column matches this month
        if ($weekend_array[0] != $current_month && $next_month_flag == 0) {
          if ($next_month == $date_array[1] && $date_array[2] > $weekend_array[1]) {
            if ($year_incr == 0 && $next_month >= 1 && $next_month < $current_month) {
              $current_year = $current_year + 1;
              $year_incr = 1;
            }
            // LEAP YEAR CALCULATION
            if ($current_year % 4 == 0 || ($current_year % 100 == 0 && $current_year % 400 == 0)) {
              $leap_year = 1;
            } else {
              $leap_year = 0;
            }
            if (!($leap_year == 0 && $next_month == 2 && $date_array[2] == 29)) {
              $birthday_array[$next_month_text[$i]][$birthday_entry][] = $values['item_id'];
              $birthday_array[$next_month_text[$i]][$birthday_entry][] = $values['value'];
              $birthday_array[$next_month_text[$i]][$birthday_entry][] = $age;
              $birthday_array[$next_month_text[$i]][$birthday_entry][] = $current_year;
              $next_month_flag = 1;
            }
          }
        } else {
          if ($next_month == $date_array[1]) {
            if ($year_incr == 0 && $next_month >= 1 && $next_month < $current_month) {
              $current_year = $current_year + 1;
              $year_incr = 1;
            }
            // LEAP YEAR CALCULATION
            if ($current_year % 4 == 0 || ($current_year % 100 == 0 && $current_year % 400 == 0)) {
              $leap_year = 1;
            } else {
              $leap_year = 0;
            }
            if (!($leap_year == 0 && $next_month == 2 && $date_array[2] == 29)) {
              $birthday_array[$next_month_text[$i]][$birthday_entry][] = $values['item_id'];
              $birthday_array[$next_month_text[$i]][$birthday_entry][] = $values['value'];
              $birthday_array[$next_month_text[$i]][$birthday_entry][] = $age;
              $birthday_array[$next_month_text[$i]][$birthday_entry][] = $current_year;
              $next_month_flag = 1;
            }
          }
        }
        $i = $i + 1;
      }

      // For the remaining birthdays of this month before current date
      if ($current_month == $date_array[1] && $current_date > $date_array[2]) {
        if ($year_incr == 0) {
          $current_year = $current_year + 1;
          $year_incr = 1;
        }
        $birthday_array['this_month_remaining'][$birthday_entry][] = $values['item_id'];
        $birthday_array['this_month_remaining'][$birthday_entry][] = $values['value'];
        $birthday_array['this_month_remaining'][$birthday_entry][] = $age;
        $birthday_array['this_month_remaining'][$birthday_entry][] = $current_year;
      }

      $birthday_entry = $birthday_entry + 1;
    }

    $this->view->birthday_array = $birthday_array;
    $this->view->next_month_text = $next_month_text;

    // get the base url
    $this->view->sugg_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

    // Render
    $this->_helper->content
            //->setNoRender()
            ->setEnabled()
    ;
  }

  public function viewallcalenderAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    // get viewer
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $param['viewer_id'] = $viewer_id;
    $param['limit'] = 0;
    $param['display_today_birthday'] = 0;

    //check Birthday field privacy so as to show age or not
    $metatable = Engine_Api::_()->getDbTable('metas', 'birthday');
    $rmetaName = $metatable->info('name');
    $select = $metatable->select()
            ->from($rmetaName, array('display'))
            ->where('type = ?', 'birthdate')
            ->limit(1);
    $result = $metatable->fetchRow($select);
    $this->view->age_display = $age_display = $result['display'];

    // initialize arrays
    $birthday_array = array();
    $date_next = array();
    $next_month_text = array();

    // define current date parameters
    $this->view->current_date = $current_date = $this->_getParam('date', null);
    $this->view->current_month = $current_month = $this->_getParam('month', null);
    $this->view->current_year = $current_year = $this->_getParam('year', null);
    $date = mktime(0, 0, 0, $current_month, $current_date, $current_year);
    $this->view->current_month_text = $current_month_text = date('F', $date);
    $current_date_month = date('m-d', $date);

    // set params
    $param['date'] = $current_date;
    $param['month'] = $current_month;
    $param['year'] = $current_year;
    $month_string = "";
    $month_string2 = "";
    $month_string.= $current_month;
    $i = 0;
    while ($i <= 10) {

      $current_month = $current_month + 1;
      if ($current_month == 13) {
        $current_month = 1;
      }
      $month_string.= ',' . $current_month;
      $i = $i + 1;
    }
    $current_month = $current_month + 1;
    if ($current_month == 13) {
      $current_month = 01;
    }
    $day_string = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31";
    $dataarray1 = array();
    $dataarray2 = array();

    // Get Current page number
    $this->view->current_page = $current_page = $this->_getParam('page', 1);

    $this->view->items_per_page = $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.listing', 20);
    $startindex = ($current_page - 1) * $items_per_page;

    $valuetable = Engine_Api::_()->getDbTable('values', 'birthday');
    $rvalueName = $valuetable->info('name');

    // get total number of entries so as to check the condition for not show the birthday widget
    $field_object = Engine_Api::_()->getDbTable('metas', 'birthday')->getFields_birthday($param);
    $field_object_temp = $field_object;
    $field_object_temp->where("DATE_FORMAT(" . $rvalueName . " .value, '%m-%d') >= ?", $current_date_month)
            ->order('Month ASC')
            ->order('Day ASC')
            ->limit($items_per_page, $startindex);

    $dataarray1 = $valuetable->fetchAll($field_object_temp)->toarray();
    $count = count($dataarray1);

    if ($count < $items_per_page) {
      $field_object2 = Engine_Api::_()->getDbTable('metas', 'birthday')->getFields_birthday($param);
      $month_string2.= "1";
      $i2 = 1;
      while ($i2 < $current_month) {
        $i2++;
        $month_string2.= "," . $i2;
      }
      $limit = $items_per_page - $count;
      $startindex = $this->_getParam('startindex', 0);

      $field_object2->where("DATE_FORMAT(" . $rvalueName . " .value, '%m-%d') >= ?", '01-01')
              ->where("DATE_FORMAT(" . $rvalueName . " .value, '%m-%d') < ?", $current_date_month)
              ->order('Month ASC')
              ->order('Day ASC')
              ->limit($limit, $startindex);

      $dataarray2 = $valuetable->fetchAll($field_object2)->toarray();
      $this->view->next_start = count($dataarray2) + $startindex;
      if ($startindex - $items_per_page < 0) {
        $this->view->prev_start = 0;
      } else {
        $this->view->prev_start = $startindex - $items_per_page;
      }
    }

    // count all the birthday entries
    $field_object1 = Engine_Api::_()->getDbTable('metas', 'birthday')->getFields2($param);
    $field_object1->where("DATE_FORMAT(" . $rvalueName . " .value, '%m-%d') >= ?", '01-01')
            ->where("DATE_FORMAT(" . $rvalueName . " .value, '%m-%d') <= ?", '12-31');
    $result_count = Engine_Api::_()->getDbTable('metas', 'birthday')->fetchRow($field_object1);
    $this->view->total_count = $total_count = $result_count->total_count;
    $this->view->total_pages = $total_pages = ceil($total_count / $items_per_page);

    $paginator = array_merge($dataarray1, $dataarray2);
    $this->view->paginator = $paginator;
    $birthday_entry = 0;
    $year_incr = 0;

    // for each birthday entry
    foreach ($paginator as $values) {
      $date_array = explode("-", $values['value']);
      $age = date("Y", time()) - $date_array[0];
      if ($date_array[1] > date("m", time())) {
        $age = $age - 1;
      } elseif ($date_array[1] == date("m", time())) {
        if ($date_array[2] > date("d", time())) {
          $age = $age - 1;
        }
      }

      // for birthdays on selected date
      if ($current_month == $date_array[1] && $current_date == $date_array[2]) {
        $birthday_array[$current_date][$birthday_entry][] = $values['item_id'];
        $birthday_array[$current_date][$birthday_entry][] = $values['value'];
        $birthday_array[$current_date][$birthday_entry][] = $age;
      }

      // for birthdays in selected date's month
      if ($current_month == $date_array[1] && $current_date < $date_array[2]) {
        $birthday_array[$current_month_text][$birthday_entry][] = $values['item_id'];
        $birthday_array[$current_month_text][$birthday_entry][] = $values['value'];
        $birthday_array[$current_month_text][$birthday_entry][] = $age;
      }

      // initialize next month and corresponding timestamp to current values
      $next_month = $current_month;
      $date_next[0] = mktime(0, 0, 0, date("m", $date), 1, $current_year);
      $leap_year = 0;

      // initialize a counter to 1 and then execute a loop 11 times for all the 11 months except the current month
      $i = 1;
      while ($i <= 11) {
        $next_month = $next_month + 1;
        if ($next_month == 13) {
          $next_month = 1;
        }
        $date_next[$i] = mktime(0, 0, 0, date("m", $date_next[$i - 1]) + 1, 1, $current_year);
        $next_month_text[$i] = date('F', $date_next[$i]);

        // check if month in birthday column matches this month
        if ($next_month == $date_array[1]) {
          if ($year_incr == 0 && $next_month >= 1 && $next_month < $current_month) {
            $current_year = $current_year + 1;
            $year_incr = 1;
          }
          // LEAP YEAR CALCULATION
          if ($current_year % 4 == 0 || ($current_year % 100 == 0 && $current_year % 400 == 0)) {
            $leap_year = 1;
          } else {
            $leap_year = 0;
          }
          if (!($leap_year == 0 && $next_month == 2 && $date_array[2] == 29)) {
            $birthday_array[$next_month_text[$i]][$birthday_entry][] = $values['item_id'];
            $birthday_array[$next_month_text[$i]][$birthday_entry][] = $values['value'];
            $birthday_array[$next_month_text[$i]][$birthday_entry][] = $age;
            $birthday_array[$next_month_text[$i]][$birthday_entry][] = $current_year;
          }
        }
        $i = $i + 1;
      }

      // For the remaining birthdays of this month before current date
      if ($current_month == $date_array[1] && $current_date > $date_array[2]) {
        if ($year_incr == 0) {
          $current_year = $current_year + 1;
          $year_incr = 1;
        }
        $birthday_array['this_month_remaining'][$birthday_entry][] = $values['item_id'];
        $birthday_array['this_month_remaining'][$birthday_entry][] = $values['value'];
        $birthday_array['this_month_remaining'][$birthday_entry][] = $age;
        $birthday_array['this_month_remaining'][$birthday_entry][] = $current_year;
      }

      $birthday_entry = $birthday_entry + 1;
    }
    $this->view->birthday_array = $birthday_array;
    $this->view->next_month_text = $next_month_text;

    // get the base url
    $this->view->sugg_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
  }

  // get the today birthday member and send default activity feed. this work move in the task folder. (Done (only removet this code.))
  public function activityAction() {

    $usertable = Engine_Api::_()->getDbTable('users', 'user');
    $ruserName = $usertable->info('name');

    $metaTable = Engine_Api::_()->getDbTable('metas', 'birthday');
    $rmetaName = $metaTable->info('name');

    $valuetable = Engine_Api::_()->getDbTable('values', 'birthday');
    $rvalueName = $valuetable->info('name');

    $select = $usertable->select()
            ->setIntegrityCheck(false)
            ->from($ruserName, array($ruserName . '.displayname', $ruserName . '.user_id', $ruserName . '.email', $ruserName . '.photo_id'))
            ->join($rvalueName, $rvalueName . '.item_id = ' . $ruserName . '.user_id', array())
            ->join($rmetaName, $rmetaName . '.field_id = ' . $rvalueName . '.field_id', array())
            ->where($rmetaName . '.type = ?', 'birthdate')
            ->where("DATE_FORMAT(" . $rvalueName . " .value, '%m-%d') = ?", date('m-d'));
    $result = $usertable->fetchAll($select);

    foreach ($result as $results) {
      $subject = Engine_Api::_()->getItem('user', $results['user_id']);
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($subject, $subject, 'member_birthday_wish', null, null, null);
    }
  }

  // display the activity feed .
  public function activitydisplayAction() {

    //get the viewer_id.
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $this->view->result = $result = Engine_Api::_()->getDbTable('metas', 'birthday')->activity($viewer_id, 'post', Null);

    $this->view->countResult = count($result);

    $this->view->member_birthday = Engine_Api::_()->getDbTable('metas', 'birthday')->activity($viewer_id, Null, 'member_birthday_wish');
  }

  // For ajax reqest accept and entry in the activity fedd table.
  public function statusubmitAction() {

    //get the viewer and viewer_id.
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject_id = $viewer->getIdentity();
    //get the object id and body text .
    $object_id = $this->_getParam('object_id');
    //subject the user.
    $subject = Engine_Api::_()->user()->getUser($object_id);
    //get the body text .
    $body = $this->_getParam('body');
    $subjectOwner = $subject->getOwner();
    if (!$viewer->isSelf($subject) &&
            $subject instanceof User_Model_User) {
      $notificationType = 'post_' . $subject->getType();
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subject, $viewer, $subject, $notificationType, array(
          'url1' => $subject->getHref(),
      ));
    }
    //activity action table.
    $this->view->action = $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'birthday_post', $body);
  }

  //For show to view more post.
  public function viewmoreAction() {

    $object_id = $this->_getParam('object_id');
    $action_id = $this->_getParam('action_id');
    $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $activityName = $activityTable->info('name');

    $activity_select = $activityTable->select()
            ->where($activityName . '.type = ?', 'post')
            ->where($activityName . '.object_id = ?', $object_id)
            ->where($activityName . '.action_id < ?', $action_id)
            ->order($activityName . '.date DESC');
    $this->view->viewmoreResult = $activity_result = $activityTable->fetchAll($activity_select);
  }

  //For show to other fiend post.
  public function getotherpostAction() {

    $object_id = $this->_getParam('id');
    $temp_id = $this->_getParam('temp_id');
    $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $activityName = $activityTable->info('name');

    $activity_select = $activityTable->select()
            ->where($activityName . '.type = ?', 'post')
            ->where($activityName . '.object_id = ?', $object_id)
            ->where($activityName . '.subject_id != ?', $temp_id)
            ->group($activityName . '.subject_id')
            ->order($activityName . '.date DESC');
    $this->view->activityPostResult = $activityTable->fetchAll($activity_select);
  }

}

?>
