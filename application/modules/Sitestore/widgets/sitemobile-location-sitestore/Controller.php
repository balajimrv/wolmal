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
class Sitestore_Widget_SitemobileLocationSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //CHECK LOCATION MAP IS ENABLED OR NOT 
    $check_location = Engine_Api::_()->sitestore()->enableLocation();
    if (!Engine_Api::_()->core()->hasSubject() || !$check_location) {
      return $this->setNoRender();
    }

    $this->view->multiple_location = $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.multiple.location', 1);

    //GET SUBJECT
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'map');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    $MainLocationId = 0;
    $this->view->MainLocationObject='';
    if (!empty($sitestore->location)) {
      $MainLocationId = Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocationId($sitestore->store_id, $sitestore->location);
      $this->view->MainLocationObject = Engine_Api::_()->getItem('sitestore_location', $MainLocationId);
    }

    $value['id'] = $sitestore->getIdentity();
    if (!empty($multipleLocation)) {
      $value['mapshow'] = 'Map Tab';
      $value['mainlocationId'] = $MainLocationId;
    }

    //DONT RENDER IF NO LOCATION
    $location = Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocation($value);
    $count = 0;
    if ($multipleLocation) {
      $count = $location->getTotalItemCount();
    } else {
      $count = count($location);
    }

    if (empty($sitestore->location) && empty($count)) {
      return $this->setNoRender();
    }

    //GET PRICE IS ENABLED OR NOT
    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0);

    //START MANAGE-ADMIN CHECK
    $this->view->isManageAdmin = $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

    //END MANAGE-ADMIN CHECK
    $this->view->current_store = $store = $this->_getParam('store', 1);
    $this->view->current_totalstores = $store * 10;

    //DONT RENDER IF NO LOCATION
    $this->view->location = $location;
    if (!empty($multipleLocation)) {
      $location->setItemCountPerPage(10);
      $this->view->location = $location->setCurrentPageNumber($store);
    } else {
      if (empty($location)) {
        return $this->setNoRender();
      }
    }

    //STORE-RATING IS ENABLED OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
  }

}