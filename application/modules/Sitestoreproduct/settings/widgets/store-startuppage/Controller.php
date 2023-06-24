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
class Sitestoreproduct_Widget_StoreStartuppageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
     $page_id = $this->_getParam('page_id', null);
     if( empty($page_id) )
         return $this->setNoRender();
     
     $this->view->object = Engine_Api::_()->getItem('sitestoreproduct_startuppage', $page_id);
  }

}