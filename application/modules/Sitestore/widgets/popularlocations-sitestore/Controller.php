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
class Sitestore_Widget_PopularlocationsSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF LOCATION IS DIS-ABLED BY ADMIN
    $locationFieldEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1);
    if ( empty($locationFieldEnable) ) {
      return $this->setNoRender();
    }

    $items_count = $this->_getParam('itemCount', 3);
    $category_id = $this->_getParam('category_id',0);
    // GET SITESTORE SITESTORE FOR MOST RATED
    $this->view->sitestoreLocation = Engine_Api::_()->getDbtable('stores', 'sitestore')->getPopularLocation($items_count,$category_id);

    $this->view->searchLocation = null;
    if ( isset($_GET['sitestore_location']) && !empty($_GET['sitestore_location']) )
      $this->view->searchLocation = $_GET['sitestore_location'];

    //DONT RENDER IF STORE COUNT ZERO
    if ( !(count($this->view->sitestoreLocation) > 0) ) {
      return $this->setNoRender();
    }
  }

}
?>