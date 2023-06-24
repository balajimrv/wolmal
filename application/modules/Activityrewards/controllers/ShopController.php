<?php

class Activityrewards_ShopController extends Core_Controller_Action_Standard
{

  public function indexAction()
  {

    $this->_helper->requireUser()->isValid();

    if( !$this->_helper->requireAuth()->setAuthParams('activitypoints', null, 'use')->isValid() ) return;

    if(Semods_Utils::getSetting('activityrewards.enable_shop',0) == 0) {
      return $this->_helper->_redirector->gotoRoute(array(), 'activitypoints_vault');
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $api = Engine_Api::_()->getApi('core', 'activitypoints');

    $this->setupNavigation();

    $this->view->form = $form = new Activityrewards_Form_Shop_Search();

    // Process form
    if( $form->isValid($this->getRequest()->getPost()) ) {
      $values = $form->getValues();
    } else {
      $values = array();
    }
    
    $this->view->assign($values);
    
    if (!empty($values['tag'])) $this->view->tag_text = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;

    $view = $this->view;
	
	$values['type'] = 'spender';

    $this->view->paginator = $paginator = Engine_Api::_()->activityrewards()->getItemsPaginator($values);
	
    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('activityrewards.offers_page', 10);
    $paginator->setItemCountPerPage($items_count);
    $this->view->paginator = $paginator->setCurrentPageNumber( $values['page'] );
	
	$max_tags = 10;
	
	// get all tags for this type
	$tableTagMaps = Engine_Api::_()->getDbtable('tagmaps', 'core');
	$tableTagMaps_name = $tableTagMaps->info('name');
	$tableTags = Engine_Api::_()->getDbtable('tags', 'core');
	$tableTags_name = $tableTags->info('name');
	
	$select = $tableTagMaps->select()
		  ->setIntegrityCheck(false)
		  ->from($tableTagMaps_name)
		  ->join($tableTags_name,"{$tableTagMaps_name}.tag_id = {$tableTags_name}.tag_id","*")
		  ->where("`{$tableTagMaps_name}`.resource_type = ?", 'activityrewards_spender')
		  ->where("`{$tableTagMaps_name}`.tagger_type = ?", 'user')
		  ->where("`{$tableTagMaps_name}`.tag_type = ?", 'core_tag')
		  ->limit($max_tags);
		  //->order('RAND()')

	$tags = $tableTagMaps->fetchAll($select);

	$this->view->itemTags = $tags;

  }



  public function viewAction()
  {

    $this->_helper->requireUser()->isValid();

    $viewer = Engine_Api::_()->user()->getViewer();
    $item = Engine_Api::_()->getDbTable('spender','activityrewards')->fetchRow(array("userpointspender_id=?" => $this->_getParam('item_id')));

    if( !$item ) {
      return $this->_helper->_redirector->gotoRoute(array(), 'activityrewards_spend');
	}
	
	if(!$item->userpointspender_enabled) {
      return $this->_helper->_redirector->gotoRoute(array(), 'activityrewards_spend');
	}
	
	// custom per-level check
	if(!$item->canView($viewer)) {
      return $this->_helper->_redirector->gotoRoute(array(), 'activityrewards_spend');
	}
	

	if($this->_getParam('task') == "dobuy") {
	  
	  $result = $item->transact( $viewer );
	  
	  if( $result === false ) {

		$is_error = 1;
		$this->view->error_message = $item->_err_msg;

	  } elseif (is_array($result) && isset($result['redirect'])) {
		
		return $this->_redirect($result['redirect'], array('prependBase' => false));
		
	  } else {
		
		$transaction_message = !empty($item->_transaction_message) ? $item->_transaction_message : Zend_Registry::get('Zend_Translate')->_( 100016678 );

		$session = new Zend_Session_Namespace('Activitypoints_Flash_Message');
		$session->success_message = $transaction_message;

		return $this->_helper->_redirector->gotoRoute(array('success'=>'1'), 'activitypoints_transactions');

	  }
	  
	}


	$item->userpointspender_views++;
	$item->save();

	$this->view->item = $item;
  }



  public function setupNavigation() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('activitypoints_main', array(), 'activityrewards_admin_main_shop');

  }


}