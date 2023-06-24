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
class Sitestore_Widget_UserstoreSitestoreController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DON'T RENDER THIS IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT AND STORE ID
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

    $params = array();
    $params['totalstores'] = $this->_getParam('itemCount', 3);
    $params['category_id'] = $this->_getParam('category_id', 0);
    $params['featured'] = $this->_getParam('featured', 0);
    $params['sponsored'] = $this->_getParam('sponsored', 0);
    $popularity = $this->_getParam('popularity', 'view_count');
    
    $this->view->userStores = $userStores =Engine_Api::_()->getDbtable('stores', 'sitestore')->userStore($sitestore->owner_id, $sitestore->store_id,$params,$popularity);

    if (!(count($this->view->userStores) > 0)) {
      return $this->setNoRender();
    }
  }

}
?>