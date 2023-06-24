<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_StoreStatisticsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $chunk = Zend_Date::DAY;
    $period = Zend_Date::WEEK;
    $start = time();

    $store_id = $this->_getParam('store_id', 2);

    $startObject = new Zend_Date($start);

    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }
    $startObject->add(1, $chunk);

    $useradsTable = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct');
    $useradsName = $useradsTable->info('name');

    $date_select = $useradsTable->select()->from($useradsName, array('MIN(creation_date) as earliest_ad_date'))
            ->where('store_id = ?', $store_id);

    $earliest_ad_date = $useradsTable->fetchRow($date_select)->earliest_ad_date;

    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $this->view->prev_link = 1;
    $this->view->store_id = $store_id;
    $this->view->startObject = $startObject = strtotime($startObject);
    $this->view->earliest_ad_date = $earliest_ad_date = strtotime($earliest_ad_date);
    if ($earliest_ad_date > $startObject) {
      $this->view->prev_link = 0;
    }


    $this->view->formFilter = $formFilter = new Sitestoreproduct_Form_Statistics_Filter();
  }

}
