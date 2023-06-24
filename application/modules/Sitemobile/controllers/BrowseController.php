<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BrowseController.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_BrowseController extends Core_Controller_Action_Standard {

  public function browseAction() {

    $this->view->navigation = $navigation = Engine_Api::_()
            ->getApi('menus', 'sitemobile')
            ->getNavigation('core_main');

    $sitemobileSettingsApi = Engine_Api::_()->getApi('settings', 'sitemobile');
    $params= array();
    $params['dafaultValues'] = array('mobile' => 'panel_reveal_list', 'tablet' => 'panel_reveal_icon', 'appmobile' => 'panel_reveal_list', 'apptablet' => 'panel_reveal_icon');
    $this->view->dashboardContentType = $sitemobileSettingsApi->getSetting('sitemobile.dashboard.contentType', $params);
//     echo $this->view->dashboardContentType;die;
//     if (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
//       $this->view->dashboardContentType = $coreSettingsApi->getSetting('sitemobile.dashboard.contentType', 'panel_reveal_list');
//     } else {
// 
//       $this->view->dashboardContentType = $coreSettingsApi->getSetting('sitemobile.tablet.dashboard.contentType', 'panel_reveal_icon');
//     }

    $fromWidgt = $this->_getParam('fromWidgt', 0);
    $showSearch = $this->_getParam('showSearch', 1);
    $isSearchWidgets = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemobile.search.widget', false);
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if ($showSearch) {

      $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
      if (!$require_check) {
        if ($viewer->getIdentity()) {
          $this->view->search_check = true;
        } else {
          $this->view->search_check = false;
        }
      } else {
        $this->view->search_check = true;
      }
    } else {
      $this->view->search_check = false;
    }

    if (empty($isSearchWidgets) || in_array($this->view->dashboardContentType ,  array('dashboard_list','dashboard_grid')) && empty($fromWidgt)) {
      // Render
      $this->_helper->content
              //->setNoRender()
              ->setEnabled()
      ;
    }
  }

  public function startupAction() {
    // Render
    $this->_helper->content
            ->setNoRender()
            ->setEnabled()
    ;
  }

}