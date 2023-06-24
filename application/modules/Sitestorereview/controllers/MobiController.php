<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobiController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreevent_MobiController extends Core_Controller_Action_Standard {

  public function init() {
    if (0 !== ($store_id = (int) $this->_getParam('store_id')) &&
            null !== ($sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id))) {
      Engine_Api::_()->core()->setSubject($sitestore);
    }
  }

  //ACTION FOR VIEW THE EVENT
  public function viewAction() {
    
   //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }
  
}

?>