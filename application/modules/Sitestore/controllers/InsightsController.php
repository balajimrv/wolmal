<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: InsightController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_InsightsController extends Core_Controller_Action_Standard {

  protected $_navigation;
  protected $_viewer;
  protected $_viewer_id;
  protected $_periods = array(
      Zend_Date::DAY, //dd
      Zend_Date::WEEK, //ww
      Zend_Date::MONTH, //MM
      Zend_Date::YEAR, //y
  );
  protected $_allPeriods = array(
      Zend_Date::SECOND,
      Zend_Date::MINUTE,
      Zend_Date::HOUR,
      Zend_Date::DAY,
      Zend_Date::WEEK,
      Zend_Date::MONTH,
      Zend_Date::YEAR,
  );
  protected $_periodMap = array(
      Zend_Date::DAY => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
      ),
      Zend_Date::WEEK => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::WEEKDAY_8601 => 1,
      ),
      Zend_Date::MONTH => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::DAY => 1,
      ),
      Zend_Date::YEAR => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::DAY => 1,
          Zend_Date::MONTH => 1,
      ),
  );

  public function init() {
    $this->_viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $this->_viewer_id = $this->_viewer->getIdentity();
  }

  public function indexAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->store_id = $store_id = $this->_getParam('store_id');
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'insight');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $chunk = Zend_Date::DAY;
    $period = Zend_Date::WEEK;
    $start = time();

    // Make start fit to period?
    $startObject = new Zend_Date($start);

    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }
    $startObject->add(1, $chunk);

    $this->view->package_id = $package_id = $this->_getParam('package_id');
    $this->view->sitestores_view_menu = 8;
    $this->view->store_owner_id = $sitestore->owner_id;

    if (!Engine_Api::_()->core()->hasSubject('sitestore')) {
      Engine_Api::_()->core()->setSubject($sitestore);
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');
    $this->view->filterForm = $filterForm = new Sitestore_Form_Insights_Filter();
    $values = array();
    $values['store_id'] = $store_id;
    $values['viewer_id'] = $this->_viewer_id;
    $values['month_activeusers'] = 1;

    // Get data
    $statObject = Engine_Api::_()->getDbtable('storestatistics', 'sitestore')->getInsights($values);
    $rawData = Engine_Api::_()->getDbtable('storestatistics', 'sitestore')->fetchAll($statObject);
    $total_likes = Engine_Api::_()->sitestore()->getStoreLikes($values);

    // check if comments should be displayed or not
    $this->view->show_comments = $show_comments = Engine_Api::_()->sitestore()->displayCommentInsights();
    if (!empty($show_comments)) {
      $this->view->total_comments = $total_comments = Engine_Api::_()->sitestore()->getStoreComments($values);
    }

    $new_responder_array = array();
    $merged_array = array();
    $total_users = 0;

    // count total monthly active users
    foreach ($rawData as $rawDatum) {
      $new = 0;
      $rawDatumDate = strtotime($rawDatum->response_date);
      $array = array();

      if (!empty($rawDatum->viewer_id)) {
        $array[] = $rawDatum->viewer_id;
        $new_responder_array[] = $rawDatum->viewer_id;
      }
      $merged_array = array_unique(array_merge($array, $merged_array));
      if (!empty($merged_array)) {
        $new = count($merged_array);
      }
      if (!empty($rawDatum->summation_view)) {
        $total_users = $new;
      }
    }
    $this->view->total_users = $total_users;
    $this->view->total_likes = $total_likes;
    $this->view->total_views = $sitestore->view_count;

    $creation_date = Engine_Api::_()->getItem('sitestore_store', $store_id)->creation_date;
    $this->view->prev_link = 1;
    $this->view->startObject = $startObject = strtotime($startObject);
    $this->view->creation_date = $creation_date = strtotime($creation_date);
    if ($creation_date > $startObject) {
      $this->view->prev_link = 0;
    }
  }

  public function storeStatisticsAction() {
    $store_id = $this->_getParam('store_id');
    if (!empty($store_id)) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $viewer_id = $viewer->getIdentity();
      //GET SUBJECT AND STORE ID
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      $owner = $sitestore->getOwner();
      $values = array('store_id' => $sitestore->store_id);
      $sub_status_table = Engine_Api::_()->getDbTable('storestatistics', 'sitestore');
      $statObject = $sub_status_table->storeReportInsights($values);
      $raw_views = $sub_status_table->fetchRow($statObject);
      $raw_views_count = $raw_views['views'];
      if (!$owner->isSelf($viewer)) {
        $sitestore->view_count++;
        $sitestore->save();
        $sub_status_table->storeViewCount($store_id);
      } else if ($sitestore->view_count == 1 && empty($raw_views_count)) {
        $sub_status_table->storeViewCount($store_id);
      }
    }
    exit(0);
  }


  public function chartDataAction() {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    // Get params
    $type = $this->_getParam('type');
    $start = $this->_getParam('start');
    $offset = $this->_getParam('offset', 0);
    $mode = $this->_getParam('mode');
    $chunk = $this->_getParam('chunk');
    $period = $this->_getParam('period');
    $periodCount = $this->_getParam('periodCount', 1);
    $store_id = $this->_getParam('store_id');

    // Validate chunk/period
    if (!$chunk || !in_array($chunk, $this->_periods)) {
      $chunk = Zend_Date::DAY;
    }
    if (!$period || !in_array($period, $this->_periods)) {
      $period = Zend_Date::MONTH;
    }

    if (array_search($chunk, $this->_periods) >= array_search($period, $this->_periods)) {
      die('whoops.');
      return;
    }

    // Validate start
    if ($start && !is_numeric($start)) {
      $start = strtotime($start);
    }
    if (!$start) {
      $start = time();
    }

    Zend_Date::setOptions(array(
        'extend_month' => true,
    ));

    // Make start fit to period?
    $startObject = new Zend_Date($start);
    $startObject->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));

    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }

    // Do offset
    if ($offset != 0) {
      $startObject->add($offset, $period);
    }

    // Get end time
    $endObject = new Zend_Date($startObject->getTimestamp());
    $endObject->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $endObject->add($periodCount, $period);

    $end_tmstmp_obj = new Zend_Date(time());
    $end_tmstmp_obj->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $end_tmstmp = $end_tmstmp_obj->getTimestamp();
    if ($endObject->getTimestamp() < $end_tmstmp) {
      $end_tmstmp = $endObject->getTimestamp();
    }
    $end_tmstmp_object = new Zend_Date($end_tmstmp);
    $end_tmstmp_object->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));

    $param['store_id'] = $store_id;
    $param['startObject'] = $startObject;
    $param['endObject'] = $endObject;

    $statsTable = Engine_Api::_()->getDbtable('storestatistics', 'sitestore');
    $statsSelect = $statsTable->getInsights($param);

    $rawData = $statsTable->fetchAll($statsSelect);

    // Now create data structure
    $currentObject = clone $startObject;
    $nextObject = clone $startObject;

    $data_views = array();
    $data_likes = array();
    $data_users = array();
    $dataLabels = array();
    $cumulative_views = 0;
    $cumulative_likes = 0;
    $cumulative_users = 0;
    $previous_views = 0;
    $previous_likes = 0;
    $previous_users = 0;

    $data_comments = array();
    $cumulative_comments = 0;
    $previous_comments = 0;
    $old_responder_array = array();

    // check if comments should be displayed or not
    $this->view->show_comments = $show_comments = Engine_Api::_()->sitestore()->displayCommentInsights();

    do {
      $nextObject->add(1, $chunk);

      $currentObjectTimestamp = $currentObject->getTimestamp();
      $nextObjectTimestamp = $nextObject->getTimestamp();

      $data_views[$currentObjectTimestamp] = $cumulative_views;
      $data_likes[$currentObjectTimestamp] = $cumulative_likes;
      $data_users[$currentObjectTimestamp] = $cumulative_users;
      if (!empty($show_comments)) {
        $data_comments[$currentObjectTimestamp] = $cumulative_comments;
      }

      // Get everything that matches
      $currentPeriodCount_views = 0;
      $currentPeriodCount_likes = 0;
      $currentPeriodCount_users = 0;
      if (!empty($show_comments)) {
        $currentPeriodCount_comments = 0;
      }
      $new_responder_array = array();
      $merged_array = array();
      $new_users = 0;

      foreach ($rawData as $rawDatum) {
        $new = 0;
        $rawDatumDate = strtotime($rawDatum->response_date);
        if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) {
          $array = array();

          if (!empty($rawDatum->viewer_id)) {
            $array[] = $rawDatum->viewer_id;
            $new_responder_array[] = $rawDatum->viewer_id;
          }
          $merged_array = array_unique(array_merge($array, $merged_array));

          if (!empty($merged_array)) {
            $new = count($merged_array);
          }
          $currentPeriodCount_views += $rawDatum->summation_view;
          if (!empty($rawDatum->summation_view)) {
            $currentPeriodCount_users = $new;
          }
        }
      }
      $old_responder_array = array_unique(array_merge($new_responder_array, $old_responder_array));
      if (!empty($old_responder_array)) {
        $new_users = count($old_responder_array);
      }

      $vals['store_id'] = $store_id;
      $vals['startTime'] = $currentObjectTimestamp;
      $vals['endTime'] = $nextObjectTimestamp;
      $currentPeriodCount_likes = Engine_Api::_()->sitestore()->getStoreLikes($vals);
      if (!empty($show_comments)) {
        $currentPeriodCount_comments = Engine_Api::_()->sitestore()->getStoreComments($vals);
      }

      // Now do stuff with it
      switch ($mode) {
        default:
        case 'normal':
          $data_views[$currentObjectTimestamp] = $currentPeriodCount_views;
          $data_likes[$currentObjectTimestamp] = $currentPeriodCount_likes;
          $data_users[$currentObjectTimestamp] = $currentPeriodCount_users;
          if (!empty($show_comments)) {
            $data_comments[$currentObjectTimestamp] = $currentPeriodCount_comments;
          }
          break;
        case 'cumulative':
          $cumulative_views += $currentPeriodCount_views;
          $cumulative_likes += $currentPeriodCount_likes;
          $data_views[$currentObjectTimestamp] = $cumulative_views;
          $data_likes[$currentObjectTimestamp] = $cumulative_likes;
          $data_users[$currentObjectTimestamp] = $new_users;

          if (!empty($show_comments)) {
            $cumulative_comments += $currentPeriodCount_comments;
            $data_comments[$currentObjectTimestamp] = $cumulative_comments;
          }
          break;
        case 'delta':
          $data_views[$currentObjectTimestamp] = $currentPeriodCount_views - $previous_views;
          $data_likes[$currentObjectTimestamp] = $currentPeriodCount_likes - $previous_likes;
          $data_users[$currentObjectTimestamp] = $currentPeriodCount_users - $previous_users;
          $previous_views = $currentPeriodCount_views;
          $previous_likes = $currentPeriodCount_likes;
          $previous_users = $currentPeriodCount_users;
          if (!empty($show_comments)) {
            $data_comments[$currentObjectTimestamp] = $currentPeriodCount_comments - $previous_comments;
            $previous_comments = $currentPeriodCount_comments;
          }
          break;
      }

      $currentObject->add(1, $chunk);
    } while ($nextObject->getTimestamp() < $end_tmstmp);

    $data_views_count = count($data_views);
    $data_likes_count = count($data_likes);
    $data_users_count = count($data_users);
    if (!empty($show_comments)) {
      $data_comments_count = count($data_comments);
    }
    else
      $data_comments_count = 0;
    $data = array();

    switch ($type) {

      case 'all':
        $merged_data_array = array_merge($data_views, $data_likes, $data_users, $data_comments);
        $data_count_max = max($data_views_count, $data_likes_count, $data_users_count, $data_comments_count);
        $data = $data_views;
        break;

      case 'view':
        $data = $merged_data_array = $data_views;
        $data_count_max = $data_views_count;
        break;

      case 'like':
        $data = $merged_data_array = $data_likes;
        $data_count_max = $data_likes_count;
        break;

      case 'active_users':
        $data = $merged_data_array = $data_users;
        $data_count_max = $data_users_count;
        break;

      case 'comment':
        $data = $merged_data_array = $data_comments;
        $data_count_max = $data_comments_count;
        break;
    }

    // Reprocess label
    $labelStrings = array();
    $labelDate = new Zend_Date();
    foreach ($data as $key => $value) {
      if ($key <= $end_tmstmp) {
        $labelDate->set($key);
        $labelStrings[] = $this->view->locale()->toDate($labelDate, array('size' => 'short')); //date('D M d Y', $key);
      } else {
        $labelDate->set($end_tmstmp);
        $labelStrings[] = date('n/j/y', $end_tmstmp);
      }
    }


    // Let's expand them by 1.1 just for some nice spacing
    $minVal = min($merged_data_array);
    $maxVal = max($merged_data_array);
    $minVal = floor($minVal * ($minVal < 0 ? 1.1 : (1 / 1.1)) / 10) * 10;
    $maxVal = ceil($maxVal * ($maxVal > 0 ? 1.1 : (1 / 1.1)) / 10) * 10;

    // Remove some labels if there are too many
    $xlabelsteps = 1;

    if ($data_count_max > 10) {
      $xlabelsteps = ceil($data_count_max / 10);
    }

    // Remove some grid lines if there are too many
    $xsteps = 1;
    if ($data_count_max > 100) {
      $xsteps = ceil($data_count_max / 100);
    }
    $steps = null;
    if (empty($maxVal)) {
      $steps = 1;
    }

    // Create base chart
    require_once 'OFC/OFC_Chart.php';

    // Make x axis labels
    $x_axis_labels = new OFC_Elements_Axis_X_Label_Set();
    $x_axis_labels->set_steps($xlabelsteps);
    $x_axis_labels->set_labels($labelStrings);

    // Make x axis
    $labels = new OFC_Elements_Axis_X();
    $labels->set_labels($x_axis_labels);
    $labels->set_colour("#416b86");
    $labels->set_grid_colour("#dddddd");
    $labels->set_steps($xsteps);

    // Make y axis
    $yaxis = new OFC_Elements_Axis_Y();
    $yaxis->set_range($minVal, $maxVal, $steps);
    $yaxis->set_colour("#416b86");
    $yaxis->set_grid_colour("#dddddd");

    // Make title
    $translate = Zend_Registry::get('Zend_Translate');
    $titleStr = $translate->_('_SITESTORE_INSIGHTS_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type), '_')));
    $title = new OFC_Elements_Title($titleStr . ' - ' . $this->view->locale()->toDateTime($startObject) . $translate->_(' to ') . $this->view->locale()->toDateTime($end_tmstmp_object));

    // Make full chart
    $chart = new OFC_Chart();
    $chart->set_bg_colour(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graph.bgcolor', '#ffffff'));

    $chart->set_x_axis($labels);
    $chart->add_y_axis($yaxis);

    $view_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphview.width', '3');
    $like_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphlike.width', '3');
    $activeuser_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphuser.width', '3');

    $view_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphview.color', '#3299CC');
    $like_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphlike.color', '#CD6839');
    $activeuser_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphuser.color', '#9F5F9F');
    if (!empty($show_comments)) {
      $comment_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphcomment.width', '3');
      $comment_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.graphcomment.color', '#458B00');
    }

    // Make data
    switch ($type) {

      case 'all':
        $graph1 = new OFC_Charts_Line();
        $graph1->set_values(array_values($data_views));
        $views_lan = Zend_Registry::get('Zend_Translate')->_('Views');
        $graph1->set_key($views_lan, '12');
        $graph1->set_width($view_width);
        $graph1->set_colour($view_color);
        $chart->add_element($graph1);

        $graph2 = new OFC_Charts_Line();
        $graph2->set_values(array_values($data_likes));
        $likes_lan = Zend_Registry::get('Zend_Translate')->_('Likes');
        $graph2->set_key($likes_lan, '12');
        $graph2->set_width($like_width);
        $graph2->set_colour($like_color);
        $chart->add_element($graph2);

        if (!empty($show_comments)) {
          $graph3 = new OFC_Charts_Line();
          $comments_lan = Zend_Registry::get('Zend_Translate')->_('Comments');
          $graph3->set_values(array_values($data_comments));
          $graph3->set_key($comments_lan, '12');
          $graph3->set_width($comment_width);
          $graph3->set_colour($comment_color);
          $chart->add_element($graph3);
        }

        $graph4 = new OFC_Charts_Line();
        $active_users = Zend_Registry::get('Zend_Translate')->_('Active Users');
        $graph4->set_values(array_values($data_users));
        $graph4->set_key($active_users, '12');
        $graph4->set_width($activeuser_width);
        $graph4->set_colour($activeuser_color);
        $chart->add_element($graph4);
        break;

      case 'view':
        $graph1 = new OFC_Charts_Line();
        $graph1->set_values(array_values($data_views));
        $graph1->set_key('Views', '12');
        $graph1->set_width($view_width);
        $graph1->set_colour($view_color);
        $chart->add_element($graph1);
        break;

      case 'like':
        $graph2 = new OFC_Charts_Line();
        $graph2->set_values(array_values($data_likes));
        $graph2->set_key('Likes', '12');
        $graph2->set_width($like_width);
        $graph2->set_colour($like_color);
        $chart->add_element($graph2);
        break;

      case 'comment':
        $graph3 = new OFC_Charts_Line();
        $graph3->set_values(array_values($data_comments));
        $graph3->set_key('Comments', '12');
        $graph3->set_width($comment_width);
        $graph3->set_colour($comment_color);
        $chart->add_element($graph3);
        break;

      case 'active_users':
        $graph4 = new OFC_Charts_Line();
        $graph4->set_values(array_values($data_users));
        $graph4->set_key('Active Users', '12');
        $graph4->set_width($activeuser_width);
        $graph4->set_colour($activeuser_color);
        $chart->add_element($graph4);
        break;
    }

    $chart->set_title($title);

    // Send
    $this->getResponse()->setBody($chart->toPrettyString());
  }

  public function exportReportAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_quick');
            
    $this->view->showMarkerInDate=$this->showMarkerInDate();

    $this->view->sitestores_view_menu = 23;
    $this->view->store_id = $store_id = $this->_getParam('store_id');
    if (empty($store_id) && !empty($_GET['store_id'])) {
      $store_id = $_GET['store_id'];
    }
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'insight');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    if (!Engine_Api::_()->core()->hasSubject('sitestore')) {
      Engine_Api::_()->core()->setSubject($sitestore);
    }

    // Get viewer's Stores
    $storesTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $storesName = $storesTable->info('name');

    // to calculate the oldest store's creation year
    $select = $storesTable->select();
    $select
            ->from($storesName, array('store_id', 'MIN(creation_date) as min_year'))
            ->where('owner_id = ?', $this->_viewer_id)
            ->group('store_id')
            ->limit(1);

    $this->view->no_stores = 0;
    $min_year = $storesTable->fetchRow($select);
    $date = explode(' ', $min_year['min_year']);
    $yr = explode('-', $date[0]);
    $current_yr = date('Y', time());
    $year_array = array();
    $year_array[$current_yr] = $current_yr;
    while ($current_yr != $yr[0]) {
      $current_yr--;
      $year_array[$current_yr] = $current_yr;
    }

    $this->view->reportform = $reportform = new Sitestore_Form_Insights_Report();
    $reportform->year_start->setMultiOptions($year_array);
    $reportform->year_end->setMultiOptions($year_array);
    $reportform->store_id->setValue($store_id);
    $this->view->prefield = 0;

    // populate form
    if (!empty($_GET['time_summary'])) {
      $this->view->prefield = 1;
      $reportform->populate($_GET);

      // Get Form Values
      $values = $reportform->getValues();
      $start_cal_date = $values['start_cal'];
      $end_cal_date = $values['end_cal'];
      $start_tm = strtotime($start_cal_date);
      $end_tm = strtotime($end_cal_date);
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);

      if (empty($values['format_report'])) {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('store_id' => $store_id, 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'sitestore_webstorereport', true) . '?' . $url_values[1];
      } else {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'sitestore', 'controller' => 'insights', 'action' => 'export-excel', 'store_id' => $store_id, 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'default', true) . '?' . $url_values[1];
      }
      // Session Object
      $session = new Zend_Session_Namespace('empty_redirect');
      if (isset($session->empty_session) && !empty($session->empty_session)) {
        unset($session->empty_session);
      } else {
        header("Location: $url");
      }
    }

    $this->view->empty = $this->_getParam('empty', 0);
  }


  public function showMarkerInDate() {
    $localeObject = Zend_Registry::get('Locale');
    $dateLocaleString = $localeObject->getTranslation('long', 'Date', $localeObject);
    $dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
    $dateLocaleString = strtolower($dateLocaleString);
    $dateLocaleString = preg_replace('/[^ymd]/i', '', $dateLocaleString);
    $dateLocaleString = preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('y', 'm', 'd'), $dateLocaleString);
    $dateFormat = $dateLocaleString;
    return $dateFormat == "mdy" ? 1 : 0;
  }

  public function exportExcelAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->sitestores_view_menu = 23;
    $this->view->post = $post = 0;
    $start_daily_time = $this->_getParam('start_daily_time', time());
    $end_daily_time = $this->_getParam('end_daily_time', time());

    if (!empty($_GET)) {
      $this->_helper->layout->setLayout('default-simple');
      $this->view->post = $post = 1;
      $values = $_GET;
      $this->view->store_id = $store_id = $values['store_id'];
      $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
      if (empty($isManageAdmin)) {
        return $this->_forward('requireauth', 'error', 'core');
      }

      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'insight');
      if (empty($isManageAdmin)) {
        return $this->_forward('requireauth', 'error', 'core');
      }
      //END MANAGE-ADMIN CHECK

      if (!Engine_Api::_()->core()->hasSubject('sitestore')) {
        Engine_Api::_()->core()->setSubject($sitestore);
      }

      $values = array_merge(array(
          'start_daily_time' => $start_daily_time,
          'end_daily_time' => $end_daily_time,
          'user_report' => '5',
          'viewer_id' => $this->_viewer_id,
              ), $values);
      $this->view->values = $values;

      $statTable = Engine_Api::_()->getDbtable('storestatistics', 'sitestore');
      $statsName = $statTable->info('name');

      // check if comments should be displayed or not
      $this->view->show_comments = $show_comments = Engine_Api::_()->sitestore()->displayCommentInsights();
      $rawdata = array();

      // FETCH VIEWS
      $statObject2 = $statTable->storeReportInsights($values);
      $rawviews = $statTable->fetchAll($statObject2);

      $rawviews_array = $rawviews->toarray();

      if (!empty($rawviews_array)) {
        foreach ($rawviews_array as $views) {
          $response_date = explode(' ', $views['response_date']);
          $date_array = explode('-', $response_date[0]);

          switch ($values['time_summary']) {
            case 'Monthly':
              $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
              $rawdata[$date_value[0]]['views'] = $views['views'];
              $rawdata[$date_value[0]]['date_value'] = $date_value;
              break;

            case 'Daily':
              $response_time = strtotime($views['response_date']);
              $labelDate = new Zend_Date();
              $labelDate->set($response_time);
              $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
              $rawdata[$response_date[0]]['views'] = $views['views'];
              $rawdata[$response_date[0]]['date_value'] = $date_value;
              break;
          }
        }
      }

      // FETCH ACTIVE USERS
      $values = array_merge(array(
          'active_user' => '10',
              ), $values);

      $statObject = $statTable->storeReportInsights($values);
      $raw_activeusers = $statTable->fetchAll($statObject);
      $raw_activeusers_array = $raw_activeusers->toarray();


      if (!empty($raw_activeusers_array)) {
        foreach ($raw_activeusers_array as $activeusers) {
          $response_date = explode(' ', $activeusers['response_date']);
          $date_array = explode('-', $response_date[0]);

          switch ($values['time_summary']) {
            case 'Monthly':
              $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
              $rawdata[$date_value[0]]['active_users'] = $activeusers['viewers'];
              $rawdata[$date_value[0]]['date_value'] = $date_value;
              break;

            case 'Daily':
              $response_time = strtotime($activeusers['response_date']);
              $labelDate = new Zend_Date();
              $labelDate->set($response_time);
              $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
              $rawdata[$response_date[0]]['active_users'] = $activeusers['viewers'];
              $rawdata[$response_date[0]]['date_value'] = $date_value;
              break;
          }
        }
      }

      // FETCH LIKES
      $this->view->total_likes = $total_likes = Engine_Api::_()->sitestore()->getReportLikes($values);
      foreach ($total_likes as $likes) {
        if (!empty($likes['creation_date'])) {
          $response_date = explode(' ', $likes['creation_date']);
          $date_array = explode('-', $response_date[0]);

          switch ($values['time_summary']) {
            case 'Monthly':
              $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
              $rawdata[$date_value[0]]['likes'] = $likes['store_likes'];
              $rawdata[$date_value[0]]['date_value'] = $date_value;
              break;

            case 'Daily':
              $response_time = strtotime($likes['creation_date']);
              $labelDate = new Zend_Date();
              $labelDate->set($response_time);
              $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
              $rawdata[$response_date[0]]['likes'] = $likes['store_likes'];
              $rawdata[$response_date[0]]['date_value'] = $date_value;
              break;
          }
        }
      }

      // FETCH COMMENTS
      if (!empty($show_comments)) {
        $this->view->total_comments = $total_comments = Engine_Api::_()->sitestore()->getReportComments($values);
        foreach ($total_comments as $comments) {
          if (!empty($comments['creation_date'])) {
            $response_date = explode(' ', $comments['creation_date']);
            $date_array = explode('-', $response_date[0]);

            switch ($values['time_summary']) {
              case 'Monthly':
                $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
                $rawdata[$date_value[0]]['comments'] = $comments['store_comments'];
                $rawdata[$date_value[0]]['date_value'] = $date_value;
                break;

              case 'Daily':
                $response_time = strtotime($comments['creation_date']);
                $labelDate = new Zend_Date();
                $labelDate->set($response_time);
                $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
                $rawdata[$response_date[0]]['comments'] = $comments['store_comments'];
                $rawdata[$response_date[0]]['date_value'] = $date_value;
                break;
            }
          }
        }
      }
      $this->view->rawdata = $rawdata;

      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('store_id' => $values['store_id'], 'empty' => 1), 'sitestore_reports', true) . '?' . $url_values[1];

      // in case no data, redirect to the report store with an empty parameter
      if (empty($rawdata)) {

        // Session Object
        $session = new Zend_Session_Namespace('empty_redirect');
        $session->empty_session = 1;
        header("Location: $url");
      }
    }
  }

  public function exportWebstoreAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_quick');

    $this->view->sitestores_view_menu = 23;
    $this->view->post = $post = 0;
    $start_daily_time = $this->_getParam('start_daily_time', time());
    $end_daily_time = $this->_getParam('end_daily_time', time());

    if (!empty($_GET)) {
      $this->view->post = $post = 1;
      $values = $_GET;
      $this->view->store_id = $store_id = $values['store_id'];
      $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
      if (empty($isManageAdmin)) {
        return $this->_forward('requireauth', 'error', 'core');
      }

      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'insight');
      if (empty($isManageAdmin)) {
        return $this->_forward('requireauth', 'error', 'core');
      }
      //END MANAGE-ADMIN CHECK

      if (!Engine_Api::_()->core()->hasSubject('sitestore')) {
        Engine_Api::_()->core()->setSubject($sitestore);
      }

      $values = array_merge(array(
          'start_daily_time' => $start_daily_time,
          'end_daily_time' => $end_daily_time,
          'user_report' => '5',
          'viewer_id' => $this->_viewer_id,
          'total_stats' => '10',
              ), $values);
      $this->view->values = $values;

      $statTable = Engine_Api::_()->getDbtable('storestatistics', 'sitestore');
      $statsName = $statTable->info('name');

      $totalObject = $statTable->storeReportInsights($values);
      $rawTotalviews = $statTable->fetchRow($totalObject);
      if (!empty($rawTotalviews)) {
        $this->view->total_views = $rawTotalviews = $rawTotalviews['views'];
      } else {
        $this->view->total_views = $rawTotalviews = 0;
      }
      $totallikes = Engine_Api::_()->sitestore()->getReportLikes($values);
      if (!empty($totallikes)) {
        $this->view->totallikes = $totallikes = $totallikes[0]['store_likes'];
      } else {
        $this->view->totallikes = $totallikes = 0;
      }

      // check if comments should be displayed or not
      $this->view->show_comments = $show_comments = Engine_Api::_()->sitestore()->displayCommentInsights();
      if (!empty($show_comments)) {
        $totalcomments = Engine_Api::_()->sitestore()->getReportComments($values);
        if (!empty($totalcomments)) {
          $this->view->totalcomments = $totalcomments = $totalcomments[0]['store_comments'];
        } else {
          $this->view->totalcomments = $totalcomments = 0;
        }
      }

      $totalObject2 = $statTable->storeReportInsights($values);
      $rawTotalusers = $statTable->fetchRow($totalObject2);
      if (!empty($rawTotalusers)) {
        $rawTotalusers = $rawTotalusers['viewers'];
      }
      else
        $rawTotalusers = 0;

      // check non-logged-in user views and decrement active users by 1 if exist
      $select_non_loggedins = $statTable->select();
      $select_non_loggedins->from($statsName, array('storestatistic_id', 'viewer_id'))->where('store_id = ?', $store_id)->where('viewer_id = ?', 0)->limit(1);
      $is_non_loggedin = $statTable->fetchRow($select_non_loggedins);

      if (!empty($is_non_loggedin)) {
        $rawTotalusers = $rawTotalusers - 1;
      }
      $this->view->totalusers = $rawTotalusers;

      unset($values['total_stats']);
      $rawdata = array();

      // FETCH VIEWS
      $statObject2 = $statTable->storeReportInsights($values);
      $rawviews = $statTable->fetchAll($statObject2);
      $rawviews_array = $rawviews->toarray();

      if (!empty($rawviews_array)) {
        foreach ($rawviews_array as $views) {
          $response_date = explode(' ', $views['response_date']);
          $date_array = explode('-', $response_date[0]);

          switch ($values['time_summary']) {
            case 'Monthly':
              $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
              $rawdata[$date_value[0]]['views'] = $views['views'];
              $rawdata[$date_value[0]]['date_value'] = $date_value;
              break;

            case 'Daily':
              $response_time = strtotime($views['response_date']);
              $labelDate = new Zend_Date();
              $labelDate->set($response_time);
              $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
              $rawdata[$response_date[0]]['views'] = $views['views'];
              $rawdata[$response_date[0]]['date_value'] = $date_value;
              break;
          }
        }
      }

      // FETCH ACTIVE USERS
      $values = array_merge(array(
          'active_user' => '10',
              ), $values);

      $statObject = $statTable->storeReportInsights($values);
      $raw_activeusers = $statTable->fetchAll($statObject);
      $raw_activeusers_array = $raw_activeusers->toarray();

      if (!empty($raw_activeusers_array)) {
        foreach ($raw_activeusers_array as $activeusers) {
          $response_date = explode(' ', $activeusers['response_date']);
          $date_array = explode('-', $response_date[0]);

          switch ($values['time_summary']) {
            case 'Monthly':
              $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
              $rawdata[$date_value[0]]['active_users'] = $activeusers['viewers'];
              $rawdata[$date_value[0]]['date_value'] = $date_value;
              break;

            case 'Daily':
              $response_time = strtotime($activeusers['response_date']);
              $labelDate = new Zend_Date();
              $labelDate->set($response_time);
              $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
              $rawdata[$response_date[0]]['active_users'] = $activeusers['viewers'];
              $rawdata[$response_date[0]]['date_value'] = $date_value;
              break;
          }
        }
      }

      // FETCH LIKES
      $this->view->total_likes = $total_likes = Engine_Api::_()->sitestore()->getReportLikes($values);
      foreach ($total_likes as $likes) {
        if (!empty($likes['creation_date'])) {
          $response_date = explode(' ', $likes['creation_date']);
          $date_array = explode('-', $response_date[0]);

          switch ($values['time_summary']) {
            case 'Monthly':
              $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
              $rawdata[$date_value[0]]['likes'] = $likes['store_likes'];
              $rawdata[$date_value[0]]['date_value'] = $date_value;
              break;

            case 'Daily':
              $response_time = strtotime($likes['creation_date']);
              $labelDate = new Zend_Date();
              $labelDate->set($response_time);
              $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
              $rawdata[$response_date[0]]['likes'] = $likes['store_likes'];
              $rawdata[$response_date[0]]['date_value'] = $date_value;
              break;
          }
        }
      }

      // FETCH COMMENTS
      if (!empty($show_comments)) {
        $this->view->total_comments = $total_comments = Engine_Api::_()->sitestore()->getReportComments($values);
        foreach ($total_comments as $comments) {
          if (!empty($comments['creation_date'])) {
            $response_date = explode(' ', $comments['creation_date']);
            $date_array = explode('-', $response_date[0]);

            switch ($values['time_summary']) {
              case 'Monthly':
                $date_value = date('F, Y', mktime(0, 0, 0, $date_array[1], $date_array[2], $date_array[0]));
                $rawdata[$date_value[0]]['comments'] = $comments['store_comments'];
                $rawdata[$date_value[0]]['date_value'] = $date_value;
                break;

              case 'Daily':
                $response_time = strtotime($comments['creation_date']);
                $labelDate = new Zend_Date();
                $labelDate->set($response_time);
                $date_value = $this->view->locale()->toDate($labelDate, array('size' => 'long'));
                $rawdata[$response_date[0]]['comments'] = $comments['store_comments'];
                $rawdata[$response_date[0]]['date_value'] = $date_value;
                break;
            }
          }
        }
      }
      $this->view->rawdata = $rawdata;

      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('store_id' => $values['store_id'], 'empty' => 1), 'sitestore_reports', true) . '?' . $url_values[1];

      // in case no data, redirect to the report store with an empty parameter
      if (empty($rawdata)) {

        // Session Object
        $session = new Zend_Session_Namespace('empty_redirect');
        $session->empty_session = 1;
        header("Location: $url");
      }
    }
  }
}
?>