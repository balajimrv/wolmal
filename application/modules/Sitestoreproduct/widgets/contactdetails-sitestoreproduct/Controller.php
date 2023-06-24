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
class Sitestoreproduct_Widget_ContactdetailsSitestoreproductController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE RANDOM ALBUMS AND PHOTOS BY OTHERS 
  public function indexAction() {
    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');
    $this->view->sitestoreProductOtherInfo = $sitestoreProductOtherInfo = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->getOtherinfo($sitestoreproduct->product_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->can_edit = false;
    $viewer_id = $viewer->getIdentity();
    $product_owner_id = $sitestoreproduct->owner_id;
    if( !empty($viewer_id) && !empty($product_owner_id) && ($viewer_id == $product_owner_id) )
        $this->view->can_edit = true;

    if (empty($sitestoreProductOtherInfo->phone) && empty($sitestoreProductOtherInfo->email) && empty($sitestoreProductOtherInfo->website) && !$this->view->can_edit) {
      return $this->setNoRender();
    }

    //GET SETTINGS
    $pre_field = array("0" => "1", "1" => "2", "2" => "3");
    $contacts = $this->_getParam('contacts', $pre_field);

    if (empty($contacts)) {
      $this->setNoRender();
    } else {
      //INITIALIZATION
      $this->view->show_phone = $this->view->show_email = $this->view->show_website = 0;
      if (in_array(1, $contacts)) {
        $this->view->show_phone = 1;
      }
      if (in_array(2, $contacts)) {
        $this->view->show_email = 1;
      }
      if (in_array(3, $contacts)) {
        $this->view->show_website = 1;
      }
    }
    $user = Engine_Api::_()->user()->getUser($sitestoreproduct->owner_id);
    $view_options = @unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('temp.sitestoreproduct.contactdetail', array()));
    $availableLabels = array('phone' => 'Phone', 'website' => 'Website', 'email' => 'Email');
    $this->view->options_create = array_intersect_key($availableLabels, array_flip($view_options));   
  }
}