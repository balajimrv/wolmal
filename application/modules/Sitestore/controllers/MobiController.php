<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_MobiController extends Core_Controller_Action_Standard {

  protected $_navigation;

  //SET THE VALUE FOR ALL ACTION DEFAULT
  public function init() {

		//STORE VIEW AUTHORIZATION
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->isValid())
      return;

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
        ->addActionContext('rate', 'json')
        ->addActionContext('validation', 'html')
        ->initContext();

		//GET STORE URL AND STORE ID
    $store_url = $this->_getParam('store_url', $this->_getParam('store_url', null));
    $store_id = $this->_getParam('store_id', $this->_getParam('store_id', null));

    if ($store_url) {
      $id = Engine_Api::_()->sitestore()->getStoreId($store_url);
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $id);
      if ($sitestore) {
        Engine_Api::_()->core()->setSubject($sitestore);
      }
    } elseif ($store_id) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      if ($sitestore) {
        Engine_Api::_()->core()->setSubject($sitestore);
      }
    }
 
		//FOR UPDATE EXPIRATION
    if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.task.updateexpiredstores') + 900) <= time()) {
      Engine_Api::_()->sitestore()->updateExpiredStores();
    }
  }

  //ACTION FOR SHOWING THE STORE LIST
  public function indexAction() {

		//STORE VIEW AUTHORIZATION
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->isValid())
      return;

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
          ->setNoRender()
          ->setEnabled();
    }
  }

  //ACTION FOR SHOWING THE HOME STORE
  public function homeAction() {

		//STORE VIEW AUTHORIZATION
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->isValid())
      return;

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
          ->setNoRender()
          ->setEnabled();
    }
  }

	//ACTION FOR VIEW PROFILE STORE
  public function viewAction() {

		//RETURN IF SUBJECT IS NOT SET
    if (!$this->_helper->requireSubject('sitestore_store')->isValid())
      return;

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

		//GET STORE SUBJECT AND THEN CHECK VALIDATION
    $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
    if (empty($sitestore)) {
      return $this->_forward('notfound', 'error', 'core');
    }

    $store_id = $sitestore->store_id;

    $memory_size = ini_get('memory_limit');
    $memory_Size_int_array = explode("M", $memory_size);
    $memory_Size_int = $memory_Size_int_array[0];
    if ($memory_Size_int <= 32)
      ini_set('memory_limit', '64M');
    $maxView = 19;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    if (!Engine_Api::_()->sitestore()->canViewStore($sitestore)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

		$current_date = date('Y-m-d H:i:s');
    $this->view->headScript()->appendFile($this->view->layout()->staticBaseUrl.'application/modules/Sitestore/externals/scripts/core.js')  
                             ->appendFile($this->view->layout()->staticBaseUrl.'application/modules/Sitestore/externals/scripts/hideTabs.js');
//     $sitestoretable = Engine_Api::_()->getDbtable('vieweds', 'sitestore');
// 
// 		//FUNCTION CALLING AND PASS STORE ID AND VIEWER ID
// 		$sitestoreresult = Engine_Api::_()->getDbtable('storestatistics', 'sitestore')->getVieweds($viewer_id, $store_id);
// 
//     $count = count($sitestoreresult);
//     if (empty($count)) {
//       $row = $sitestoretable->createRow();
//       $row->store_id = $store_id;
//       $row->viewer_id = $viewer_id;
//       $row->save();
//     } else {
//       $sitestoretable->update(array('date' => $current_date), array('store_id = ?' => $store_id, 'viewer_id' => $viewer_id));
//     }

    //INCREMENT IN NUMBER OF VIEWS
    $owner = $sitestore->getOwner();
   
    $sub_status_table = Engine_Api::_()->getDbTable('storestatistics', 'sitestore');
   
		//INCREMENT STORE VIEWS DATE-WISE
    $values = array('store_id' => $sitestore->store_id);

    $statObject = $sub_status_table->storeReportInsights($values);
    $raw_views = $sub_status_table->fetchRow($statObject);
    $raw_views_count = $raw_views['views'];
    if (!$owner->isSelf($viewer) || ($sitestore->view_count == 1 && empty($raw_views_count))) {
      $sub_status_table->storeViewCount($store_id);
    }

    if (!$owner->isSelf($viewer)) {
      $sitestore->view_count++;
    }

    $sitestore->save();

		//CHECK TO SEE IF PROFILE STYLES IS ALLOWED
    $style_perm = 1;

    if ($style_perm) {

      //GET STYLE
      $table = Engine_Api::_()->getDbtable('styles', 'core');
      $select = $table->select()
              ->where('type = ?', $sitestore->getType())
              ->where('id = ?', $sitestore->getIdentity())
              ->limit();

      $row = $table->fetchRow($select);
      if (null !== $row && !empty($row->style)) {
        $this->view->headStyle()->appendStyle($row->style);
      }
    }

    if (null !== ($tab = $this->_getParam('tab'))) {
      $friend_tab_function = <<<EOF
                                        var content_id = "$tab";
                                        this.onload = function()
                                        {
      																		if(window.tabContainerSwitch) 
      																		{
                                              tabContainerSwitch($('main_tabs').getElement('.tab_' + content_id));
																					}
                                        }
EOF;
      $this->view->headScript()->appendScript($friend_tab_function);
    }    
    
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
  
		// Start: Suggestion work.
    $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    // Here we are delete this poll suggestion if viewer have.
    if (!empty($is_moduleEnabled)) {
      Engine_Api::_()->getApi('suggestion', 'sitestore')->deleteSuggestion($viewer->getIdentity(), 'sitestore_store', $store_id, 'store_suggestion');
    }
    // End: Suggestion work.
    
		if ($coreversion < '4.1.0') {

			$this->_helper->content->render();
		} else {
			$this->_helper->content->setNoRender()->setEnabled();
		}
  }
}
?>