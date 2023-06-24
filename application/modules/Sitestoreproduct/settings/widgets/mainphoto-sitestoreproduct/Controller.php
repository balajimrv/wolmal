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
class Sitestoreproduct_Widget_MainphotoSitestoreproductController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }

    //GET SUBJECT AND OTHER SETTINGS
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');
    $this->view->show_featured = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.featured', 1);
    $this->view->featured_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.featuredcolor', '#30a7ff');
    $this->view->show_sponsered = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsored', 1);
    $this->view->sponsored_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505');;
    
    $this->view->ownerName = $this->_getParam('ownerName', 0);

    //GET VIEWER AND CHECK VIEWER CAN EDIT PHOTO OR NOT
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->can_edit = $sitestoreproduct->authorization()->isAllowed($viewer, 'edit');
  }

}