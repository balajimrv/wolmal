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
class Sitestore_Widget_RecentviewSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params = array();
    $params['totalstores'] = $this->_getParam('itemCount', 3);
    $params['category_id'] = $this->_getParam('category_id', 0);
    $params['featured'] = $this->_getParam('featured', 0);
    $params['sponsored'] = $this->_getParam('sponsored', 0);  
    
    $this->view->recentlyview = Engine_Api::_()->getDbtable('storestatistics', 'sitestore')->recentViewList($params);

    //NOT RENDER IF STORE COUNT ZERO
    if ( !(count($this->view->recentlyview) > 0) ) {
      return $this->setNoRender();
    }
  }

}
?>