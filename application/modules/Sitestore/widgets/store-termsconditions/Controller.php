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
class Sitestore_Widget_StoreTermsconditionsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
   
      
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    if (Engine_Api::_()->core()->hasSubject('sitestore_store')) {
      $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
      $currentStoreId = $subject->getIdentity();
      $owner_id = $subject->owner_id;
    } else if (Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      $subject = Engine_Api::_()->core()->getSubject('sitestoreproduct_product');
      $object = Engine_Api::_()->getItem('sitestore_store', $subject->store_id);
      $owner_id = $object->owner_id;
      $currentsStoreIdProduct = $subject->store_id;
      $limit = 1;
    } else if (Engine_Api::_()->core()->hasSubject('user')) {
      $subject = Engine_Api::_()->core()->getSubject('user');
      $owner_id = $subject->getIdentity();
    } else {
      $owner_id = $viewer_id;
    }
    if (empty($owner_id))
      return $this->setNoRender();
 
   $resource = $subject->toArray();
        $this->view->termsConditions = Engine_Api::_()->getDbtable('otherinfo', 'sitestore')->getStoreAttribs($resource['store_id'], 'terms_conditions');
  }
}

?>