<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreform
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SiteformController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreform_SiteformController extends Sitestoreform_Controller_Abstract {

  protected $_requireProfileType = true;
  
  public function editTabAction() {
    
		$store_id = $this->_getParam('store_id');
		$layout_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
		$contenStoreTable = Engine_Api::_()->getDbtable('content', 'sitestore');
		$adminContentStoreTable = Engine_Api::_()->getDbtable('admincontent', 'sitestore');
		if(empty($layout_type)) {
			$sitestoreformtable = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform');
			$offerWidgetName = $sitestoreformtable->select()
									->from($sitestoreformtable->info('name'),'offer_tab_name')
									->where('store_id = ?', $store_id)
									->query()->fetchColumn();
			if( !empty($offerWidgetName) ) {
				$this->view->offer_tab_name = $offerWidgetName;
			}
			else {
				$tablecontent = Engine_Api::_()->getDbtable('content', 'core');
				$params = $tablecontent->select()
										->from($tablecontent->info('name'),'params')
										->where('name = ?', 'sitestoreform.sitestore-viewform')
										->query()->fetchColumn();
				$decodedParam = Zend_Json::decode($params);
				$tabName = $decodedParam['title'];
				$this->view->offer_tab_name = $tabName;
			}	
		}
		else {
		  $userContent = $contenStoreTable->checkWidgetExist($store_id,'sitestoreform.sitestore-viewform');
		  if(!empty($userContent)) {
		    $decodedParam = Zend_Json::decode($userContent);
				$tabName = $decodedParam['title'];
				$this->view->offer_tab_name = $tabName;
		  }
		  else {
		    $adminContent = $adminContentStoreTable->checkAdminWidgetExist('sitestoreform.sitestore-viewform');
		    $decodedParam = Zend_Json::decode($adminContent);
				$tabName = $decodedParam['title'];
				$this->view->offer_tab_name = $tabName;
		  }
		
		}
		
		$this->view->success = 0;
		if ($this->getRequest()->isPost()) {
		  $userContent = $contenStoreTable->checkWidgetExist($store_id,'sitestoreform.sitestore-viewform');
		  $tab_name = $_POST['tab_name'];
		  $setParam = "{\"title\":\"$tab_name\",\"titleCount\":false}";
		  if(!empty($userContent) && !empty($layout_type)) {
		    $contenStoreTable->update(array('params' => $setParam), array('contentstore_id = ?' => $store_id,'name	 = ?' => 'sitestoreform.sitestore-viewform'));
		  }
		  elseif(!empty($layout_type)) {
				$adminContentStoreTable->update(array('params' => $setParam), array('name	 = ?' => 'sitestoreform.sitestore-viewform'));
		  }
		  else {
		    $sitestoreformtable->update(array('offer_tab_name' => $_POST['tab_name']), array('store_id = ?' => $store_id));
		  }
      $this->view->success = 1;
		}
  }

}
?>