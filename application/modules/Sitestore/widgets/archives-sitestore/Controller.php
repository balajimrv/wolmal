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
class Sitestore_Widget_ArchivesSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET ARCHIVE SITESTORE OF SITESTORE USER
    $archiveSitestore =  Engine_Api::_()->getDbtable('stores', 'sitestore')->getArchiveSitestore(null);

		//CALL TO handleArchiveSitestore ACTION OF SAME CONTROLLER
    $this->view->archive_sitestore = $this->_handleArchiveSitestore($archiveSitestore);

    if (!(count($this->view->archive_sitestore) > 0)) {
      return $this->setNoRender();
    }

    if (isset($_GET['start_date'])) {
      $this->view->start_date = $_GET['start_date'];
		}
  }

  //ACTION FOR handleArchiveSitestore
  protected function _handleArchiveSitestore($results) {

    $localeObject = Zend_Registry::get('Locale');
    $sitestore_dates = array();
    foreach ($results as $result)
      $sitestore_dates[] = strtotime($result->creation_date);

    //GET ARCHIVE SITESTORE
    $time = time();
    $archive_sitestore = array();

    foreach ($sitestore_dates as $sitestore_date) {
      $ltime = localtime($sitestore_date, TRUE);
      $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
      $ltime["tm_year"] = $ltime["tm_year"] + 1900;

      //LESS THAN A YEAR AGO - MONTHS
      if ($sitestore_date + 31536000 > $time) {
        $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, $ltime["tm_mon"] + 1, 1, $ltime["tm_year"]);
        $label = date('F Y', $sitestore_date);
        $type = 'month';
      }

      //MORE THAN A YEAR AGO - YEARS
      else {
        $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"] + 1);
        $type = 'year';

        $dateObject = new Zend_Date($sitestore_date);
        $format = $localeObject->getTranslation('yyyy', 'dateitem', $localeObject);
        if (!$format) {
          $format = $localeObject->getTranslation('y', 'dateitem', $localeObject);
        }
        $label = $dateObject->toString($format, $localeObject);
      }

      if (!isset($archive_sitestore[$date_start])) {
        $archive_sitestore[$date_start] = array(
            'type' => $type,
            'label' => $label,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'count' => 1
        );
      } else {
        $archive_sitestore[$date_start]['count']++;
      }
    }
    return $archive_sitestore;
  }
}

?>