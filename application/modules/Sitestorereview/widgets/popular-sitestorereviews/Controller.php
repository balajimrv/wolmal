<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Widget_PopularSitestorereviewsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //SET NO RENDER IF NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT AND SITESTORE ID
		$store_url = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_url', null);
		$store_id = Engine_Api::_()->sitestore()->getStoreId($store_url);

    //GET OBJECT
    $sitestore_subject = Engine_Api::_()->getItem('sitestore_store',$store_id);

    // PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore_subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

    $sitestorereview_isReview = Zend_Registry::isRegistered('sitestorereview_isReview') ? Zend_Registry::get('sitestorereview_isReview') : null;
    if (empty($sitestorereview_isReview)) {
      return $this->setNoRender();
    }

		$params = array();
		$params['store_id'] = $store_id;
		$params['orderby'] = 'view_count DESC';
		$params['zero_count'] = 'view_count';
		$params['limit'] = $this->_getParam('itemCount', 3);

    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('reviews', 'sitestorereview')->reviewRatingData($params);

    if (Count($paginator) <= 0) {
      return $this->setNoRender();
    }
  }

}
?>