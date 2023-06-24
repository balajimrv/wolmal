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
class Sitestore_Widget_ViewMySitestoreController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        
        if(empty($viewer_id))
          return $this->setNoRender();
        
        $this->view->storeCount = $UserStores = Engine_Api::_()->getDbTable('stores', 'sitestore')->countUserStores($viewer_id);
        
        if( empty($UserStores) )
          return $this->setNoRender();        
  }
}

?>