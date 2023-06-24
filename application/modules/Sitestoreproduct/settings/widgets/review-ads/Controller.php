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
class Sitestoreproduct_Widget_ReviewAdsController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE AD WITH PAGES
  public function indexAction() {

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();
    $load_content = 0;
    $this->view->communityad_id = $communityad_id = $this->_getParam('communityadid', null);
    $sitestoreproductAds = Zend_Registry::isRegistered('sitestoreproductAds') ?  Zend_Registry::get('sitestoreproductAds') : null;
    $this->view->isajax = $isajax = $this->_getParam('isajax', null);
    $this->view->limit = $limit = $this->_getParam('limit', null);

    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if (!$enable_ads || empty($sitestoreproductAds)) {
      return $this->setNoRender();
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.communityads', 1)) {
      return $this->setNoRender();
    }

    if ($this->view->identity) {
      $limit = $this->_getParam('limit', 3);
      $this->view->identity_temp = $this->view->identity;
      $this->view->communityad_id = $communityad_id = $this->_getParam('communityadid', null);

      if (empty($limit)) {
        return $this->setNoRender();
      }
    }

    if (!empty($_GET['load_content']) || empty($communityad_id) || !empty($isajax)) {

      $load_content = 1;
      $this->view->tab = $this->_getParam('tab', null);
      if ($limit == 0 && empty($this->view->identity)) {
        return $this->setNoRender();
      }

      $this->view->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $params = array();
      $params['lim'] = $limit;

      if (Engine_Api::_()->core()->hasSubject()) {
        $subject = Engine_Api::_()->core()->getSubject();
        Engine_Api::_()->core()->clearSubject();
      }

      $fetch_community_ads = Engine_Api::_()->communityad()->getAdvertisement($params);
      if (!empty($subject)) {
        Engine_Api::_()->core()->clearSubject();
        Engine_Api::_()->core()->setSubject($subject);
      }

      if (!empty($fetch_community_ads)) {
        $this->view->communityads_array = $fetch_community_ads;
      } else {
        return $this->setNoRender();
      }
    }

    $this->view->load_content = $load_content;
  }

}