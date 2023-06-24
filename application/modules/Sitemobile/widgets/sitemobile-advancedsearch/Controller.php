<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Widget_SitemobileAdvancedsearchController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {
    $this->_mobileAppFile = true;

    $zendInstance = Zend_Controller_Front::getInstance();
    $request = $zendInstance->getRequest();
    $this->view->params = $p = $request->getParams();
    $this->view->action = $action = $zendInstance->getRouter()->assemble(array());
    if ($p['module'] == 'ynforum')
      $p['module'] = 'forum';
    $this->view->pageName = $pageName = $module_controller_action = $p['module'] . '_' . $p['controller'] . '_' . $p['action'];
    $widgetParams = $this->_getAllParams();
 
    //SPECIAL SEARCH FORMS CREATED FOR MODULES
    if (isset($widgetParams['module_search']) && $widgetParams['module_search'] && Engine_Api::_()->sitemobile()->isSupportedModule($widgetParams['module_search'])) {
      $this->view->module_search = $widgetParams['module_search'];
      //ADDED FOR ADV. MEMBER
      if($widgetParams['module_search']=='sitemember'){
        $pageName = 'sitemember_location_userby-locations';
      }else if($widgetParams['module_search']=='sitealbum'){
        $pageName = 'sitealbum_index_index';
      }else{
      $pageName = $widgetParams['module_search'] . "_index_home";
      }
      //ARRAY OF ELEMENTS WHICH ARE DISPLAYED IN QUICK FORM
      switch ($widgetParams['module_search']){
        case 'sitemember':
        case 'sitealbum':
        case 'sitereview':
          $this->view->fieldElements = array("search","location", "locationmiles","done");
          break;
        case 'siteevent':
          $this->view->fieldElements = array("search","category_id","location", "locationmiles","done");
          break;
        case 'sitepage':
          $this->view->fieldElements = array("search","sitepage_location", "locationmiles","extra-done");
          break;
        case 'sitegroup':
          $this->view->fieldElements = array("search","sitegroup_location", "locationmiles","extra-done");
          break;
        case 'sitebusiness':
          $this->view->fieldElements = array("search","sitebusiness_location", "locationmiles","extra-done");
          break;
        case 'core':
          $this->view->fieldElements = array("query","type_button", "submit");
          break;
      }
    }else{
      $this->view->fieldElements = array();
    }
    
    //SENDING ID OF LOCATION FIELDS - IF THEY EXISTS ON PAGE THEN APPLY AUTOCOMPLETE ON THEM
    $this->view->locationFieldId = 'location';
    if (in_array($p['module'], array('sitepage', 'sitegroup', 'sitebusiness', 'sitestore'))) {
      $this->view->locationFieldId = $p['module'] . "_" . $this->view->locationFieldId;
    }
    
    if (!isset($widgetParams['search'])) {
      $widgetParams['search'] = 2;
    }

    $params['name'] = $pageName;
    $this->view->searchRow = $searchRow = Engine_Api::_()->getDbtable('searchform', 'sitemobile')->getSearchForm($params);

    if (!empty($searchRow)) { 
      $params = array();
      $className = $searchRow->class;
      if (!empty($searchRow->params)) {
        $params = Zend_Json_Decoder::decode($searchRow->params);
      }
      $params['hasMobileMode'] = true;
      //FOR SITEEVENT BY DEFAULT BROWSE BY STARTTIME
      if ($p['module'] == 'siteevent') {
        $p['orderby'] = $orderBy = $request->getParam('orderby', null);
        if (empty($orderBy)) {
          $p['orderby'] = $this->_getParam('orderby', 'starttime');
        }
      }
      $this->view->form = $form = new $className($params);
      $this->view->form->populate($p);
      $this->view->searchField = $searchRow->search_filed_name;
      $this->view->search = $request->getParam($searchRow->search_filed_name, null);
      $this->view->action = $form->getAction();
    } elseif ($module_controller_action == 'messages_messages_inbox' || $module_controller_action == 'messages_messages_outbox' || $module_controller_action == 'messages_messages_search') {
      $this->view->action = $action = $zendInstance->getRouter()->assemble(array('action' => 'search'));
      $widgetParams['search'] = 1;
    } elseif ($module_controller_action == 'forum_index_index') {
      $this->view->action = $action = $zendInstance->getRouter()->assemble(array('controller' => 'search'), 'default', true);
      $widgetParams['search'] = 1;
      $this->view->searchField = 'query';
    } elseif ($module_controller_action == 'suggestion_index_viewfriendsuggestion') {
      $this->view->action = $action = $zendInstance->getRouter()->assemble(array(), 'user_extended', true);
      $widgetParams['search'] = 1;
      $this->view->searchField = 'displayname';
    } elseif ($module_controller_action == 'peopleyoumayknow_index_index') {
      $this->view->action = $action = $zendInstance->getRouter()->assemble(array(), 'user_extended', true);
      $widgetParams['search'] = 1;
      $this->view->searchField = 'displayname';
    } elseif ($module_controller_action == 'user_index_friends') {
      $this->view->action = $action = $zendInstance->getRouter()->assemble(array(), 'sitemobileapp_friends', true);
      $widgetParams['search'] = 1;
      $this->view->searchField = 'displayname';
    } elseif ($module_controller_action == 'sitehashtag_index_index') {
      $this->view->action = $action = $zendInstance->getRouter()->assemble(array('controller' => 'index', 'action' => 'index'), 'sitehashtag_general', true);
      $widgetParams['search'] = 1;
      $this->view->searchField = 'search';
      $this->view->search = $request->getParam($this->view->searchField, '');
    } else {
      // check public settings
      $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
      if (!$require_check) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity())
          return $this->setNoRender();
      }
      $this->view->form = $form = new Sitemobile_modules_Core_Form_Filter_Search();
      $this->view->action = $form->getAction();
      $this->view->searchField = 'query';
    }
    
    $this->view->widgetParams = $widgetParams;

    $reqview_selected = Zend_Controller_Front::getInstance()->getRequest()->getParam('view_selected');
    if ($reqview_selected && $this->view->form) {
      $this->view->form->addElement('Hidden', 'view_selected', array(
          'value' => $reqview_selected
      ));
    } 
    //FOR PAGE PLUGIN SEARCH FORM ELEMENT - DISPLAY SHOW OPTION ONLY FOR LOGGED IN USER
    if($p['module'] == 'sitepage' && !Engine_Api::_()->user()->getViewer()->getIdentity()){ 
        $form->removeElement('show');
    }
  }
}