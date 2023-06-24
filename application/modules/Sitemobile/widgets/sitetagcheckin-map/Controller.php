<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Widget_SitetagcheckinMapController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //IF THERE IS NO SUBJECT THEN SET NO RENDER
    if (!Engine_Api::_()->core()->hasSubject('user')) {
      return $this->setNoRender();
    }

    //GET SUBJECT IF NECESSARY
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET VIEWER ID
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET USER SUBJECT
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');

    //GET ADDLOCATION TABLE
    $addlocationsTable = Engine_Api::_()->getDbtable('addlocations', 'sitetagcheckin');

    //GET FILTER BASE COUNT
    $totalMapLocation = $addlocationsTable->getFilterBasedCount($subject, 'all');
    $sitetagcheckin_map_view = Zend_Registry::isRegistered('sitetagcheckin_map_view') ? Zend_Registry::get('sitetagcheckin_map_view') : null;

    //IF THERE IS NO RESULT THEN SET NO RENDER
    if (empty($sitetagcheckin_map_view))
      return $this->setNoRender();

    //GET ALL LOCATIONS 
    $locations = $addlocationsTable->getAllLocations($subject, 'all');

    //GET ALL CHECKIN UPDATES
    $this->view->updates = $addlocationsTable->getAllLocations($subject, 'checkin');

    //COUNT TOTAL UPDATES
    $this->view->totalUpdates = $addlocationsTable->getFilterBasedCount($subject, 'checkin');

    //GET ALL CHECKINS
    $this->view->checkins = $addlocationsTable->getAllLocations($subject, 'checkin', 'parent_id', 1);

    //GET TOTAL CHECKINS
    $this->view->totalCheckins = $addlocationsTable->getFilterBasedCount($subject, 'checkin', 'parent_id');

    //GET TAGGED PHOTOS
    $this->view->taggedPhotos = $addlocationsTable->getAllLocations($subject, 'tagging');

    //GET TAGGED PHOTOS COUNT
    $this->view->totalTaggedPhotos = $addlocationsTable->getFilterBasedCount($subject, 'tagging');

    //INITIALISE SHOW MAP
    $this->view->show_map = 1;

    //SHOW MAP PHOTO WHEN USER HAS COME FROM THE PHOTO WIDGET
    $this->view->show_map_photo = Zend_Controller_Front::getInstance()->getRequest()->getParam('show_map_photo', 1);
    $this->view->countPhoto = $this->_getParam('itemCount', 7);
    //GET TOTAL EVENTS
    $data = $addlocationsTable->getEventsData($subject);
    $this->view->totalEventCount = $totalCount = Count($data);

    //MAKE LOCATION ARRAY OF EVENT
    $eventLocationsArray = array();
    foreach ($data as $key => $values) {
      $increaseCount = 0;
      for ($i = 0; $i < $totalCount; $i++) {
        if (!empty($eventLocationsArray) && isset($eventLocationsArray[$i])) {
          if ($values['location_id'] == $eventLocationsArray[$i]['location_id']) {
            $increaseCount = 1;
            $index = $i;
            break;
          }
        }
      }
      if (!empty($increaseCount)) {
        $eventLocationsArray[$index]['count']++;
      } else {
        $eventLocationsArray[$key] = $values;
        $eventLocationsArray[$key]['count'] = 1;
      }
    }
    $data_array_merge = $this->view->eventLocationsArray = $eventLocationsArray;
    if (!empty($locations)) {
      $data_array_merge = array_merge($locations->toarray(), $eventLocationsArray);
    }

    $totalDataCount = Count($data_array_merge);
    $totalLocationsArray = array();
    foreach ($data_array_merge as $key => $values1) {
      $increaseCount = 0;
      for ($i = 0; $i < $totalDataCount; $i++) {
        if (!empty($totalLocationsArray) && isset($totalLocationsArray[$i])) {
          if ($values1['location_id'] == $totalLocationsArray[$i]['location_id']) {
            $increaseCount = 1;
            $index = $i;
            break;
          }
        }
      }
      if (!empty($increaseCount)) {
        $totalLocationsArray[$index]['count'] = $totalLocationsArray[$index]['count'] + $values1['count'];
      } else {
        $totalLocationsArray[$key] = $values1;
      }
    }

    $this->view->locations = $totalLocationsArray;
    $totalContentcount = 0;
    foreach ($totalLocationsArray as $counts) {
      $totalContentcount = $totalContentcount + $counts['count'];
    }
    $this->view->totalMapLocation = $totalContentcount;

    if (isset($viewer->username) && '' != trim($viewer->username)) {
      $this->view->profileAddress = $viewer->username;
    } else if (isset($viewer->user_id) && $viewer->user_id > 0) {
      $this->view->profileAddress = $viewer->user_id;
    }
  }

}

?>