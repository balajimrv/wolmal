<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestore_Widget_MostviewedSitestoreController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $params =array();
    $current_time = date("Y-m-d H:i:s");
    $params['totalstores'] = $this->_getParam('itemCount', 3);
    $params['category_id'] = $this->_getParam('category_id',0);
    $params['featured'] = $this->_getParam('featured',0);
    $params['sponsored'] = $this->_getParam('sponsored',0);
    $interval = $this->_getParam('interval', 'overall');

			//MAKE TIMING STRING
		if($interval == 'week') {
			$time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
			$sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" ;
		}
		elseif($interval == 'month') {
			$time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
			$sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
		}
		else {
			$sqlTimeStr = '';
		}
    $params['sqlTimeStr'] = $sqlTimeStr;


    //GET SITESTORE FOR MOST VIEWED
    $this->view->sitestores = Engine_Api::_()->sitestore()->getLising('Most Viewed List',$params,$interval, $sqlTimeStr);

    if (!(count($this->view->sitestores) > 0)) {
			return $this->setNoRender();
		}
  }
}

?>