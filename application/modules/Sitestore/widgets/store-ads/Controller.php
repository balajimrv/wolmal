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
class Sitestore_Widget_StoreAdsController extends Engine_Content_Widget_Abstract {

	//ACTION FOR SHOWING THE AD WITH STORE
  public function indexAction() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $module = $request->getModuleName();
		$controller = $request->getControllerName();
    $action = $request->getActionName();
    $load_content = 0;
    $this->view->communityad_id = $communityad_id = $this->_getParam('communityadid', null);
    $this->view->isajax = $isajax = $this->_getParam('isajax', null);
    $this->view->limit = $limit = $this->_getParam('limit', null);

    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if (!$enable_ads) {
      return $this->setNoRender();
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1)) {
      return $this->setNoRender();
    }
    

    if ($this->view->identity) {
      $limit = 0;
      $this->view->identity_temp =$this->view->identity;
      $this->view->communityad_id = $communityad_id = $this->_getParam('communityadid', "communityadid_widget_showads");

      switch ($module) {

        case "sitestoreevent":
         if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adeventview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adeventbrowse', 3);
          }
          break;
        case "sitestorevideo":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideoview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideobrowse', 3);
          }
          break;
        case "sitestorenote":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adnoteview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adnotebrowse', 3);
          }
          break;
        case "sitestoremember":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admemberwidget', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admemberbrowse', 3);
          }
          break;
        case "sitestorepoll":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adpollview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adpollbrowse', 3);
          }
          break;
        case "sitestoreoffer":
          $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adofferlist', 3);
          break;
        case "sitestoremusic":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admusicview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.admusicbrowse', 3);
          }
          break;
        case "sitestorereview":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adreviewview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adreviewbrowse', 3);
          }
          break;
        case "sitestoredocument":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addocumentview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.addocumentbrowse', 3);
          }
          break;
        case "sitestorebadge":
          $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adbadgeview', 3);
          break;
        case "sitestore":
          if ($controller == 'album' && $action == 'view' ) {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumview', 3);
          } elseif($controller == 'album' && $action == 'browse' ) {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adalbumbrowse', 3);
          }
          break;
      }
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
//       elseif (!empty($this->view->identity)) {
//         if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')) {
//           $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adeventwidget', 3);
//           if (empty($limit)) {
//             return $this->setNoRender();
//           }
//         }
//       }

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

?>