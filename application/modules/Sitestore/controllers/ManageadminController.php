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
class Sitestore_ManageadminController extends Seaocore_Controller_Action_Standard {

	//ACTION FOR SHOWING STORES FOR WHICH I AM ADMIN
	public function myStoresAction() {

    //USER VALIDATION
		if (!$this->_helper->requireUser()->isValid())
			return;

    //MANAGE ADMIN IS ALLOWED OR NOT BY ADMIN
		$manageAdminEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1);
		if (empty($manageAdminEnabled)) {
			return $this->_forwardCustom('requireauth', 'error', 'core');
		}

    //GETTING THE VIEWER AND VIEWER ID AND PASS VALUE .TPL FILE.
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->owner_id = $viewer_id = $viewer->getIdentity();

    //CHEKC FOR MEMBER LEVEL SETTINGS FOR EDIT AND DELETE AND CREATE.
		$this->view->can_create = $this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'create')->checkRequire();
		$this->view->can_edit = $this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'edit')->checkRequire();
		$this->view->can_delete = $this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'delete')->checkRequire();

		$this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0);
		$this->view->enableLocation = $checkLocation = Engine_Api::_()->sitestore()->enableLocation();

		//GET NAVIGATION
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

		//GET QUICK NAVIGATION
		$this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
						->getNavigation('sitestore_quick');

		$this->view->form = $form = new Sitestore_Form_Myadminstores();

		//PROCESS FORM
		$values = array();
		if ($form->isValid($this->_getAllParams())) {
			$values = $form->getValues();
		}

		//RATING ENABLE / DISABLE
		$this->view->ratngShow = $ratingShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');

		//GET STORES
		$adminstores = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdminStores($viewer_id);

		//GET STUFF
		$ids = array();
		foreach ($adminstores as $adminstore) {
			$ids[] = $adminstore->store_id;
		}
		$values['adminstores'] = $ids;
		$values['orderby'] = 'creation_date';
		
    $values['notIncludeSelfStores'] = $viewer_id;   

		//GET PAGINATOR.
		$this->view->paginator = $paginator = Engine_Api::_()->sitestore()->getSitestoresPaginator($values, null);
		$items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.store', 10);
    
		$paginator->setItemCountPerPage($items_count);
		$this->view->paginator = $paginator->setCurrentPageNumber($values['store']);

		$this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

		//MAXIMUN ALLOWED STORES.
		$this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestore_store', 'max');
		$this->view->current_count = $paginator->getTotalItemCount();

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $this->_helper->content/*->setNoRender()*/->setEnabled();
    }
  }

  //MANAGE ADMINS ACTION FOR THE STORES.
  public function indexAction() {

    //CHECK PERMISSION FOR VIEW.
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NAVIGATION.
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestoreproduct_main');

    $this->view->sitestores_view_menu = 11;

    //GETTING THE VIEWER AND VIEWER ID.
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer->getIdentity();

    //GETTING THE OBJECT AND STORE ID.
    $this->view->store_id = $store_id = $this->_getParam('store_id', null);
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $this->view->owner_id = $sitestore->owner_id;
    $this->view->is_ajax = $this->_getParam('is_ajax', '');

    //EDIT PRIVACY
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

    $manageAdminAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.manageadmin', 1);
    if (empty($isManageAdmin) || empty($manageAdminAllowed)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

		$manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');

		//FETCH DATA
    $this->view->manageHistories = $manageadminsTable->getManageAdminUser($store_id);

    if ($this->getRequest()->isPost()) {

      $values = $this->getRequest()->getPost();
      $selected_user_id = $values['user_id'];

      $row = $manageadminsTable->createRow();
      $row->user_id = $selected_user_id;
      $row->store_id = $store_id;
      $row->save();

			//START SITESTOREMEMBER PLUGIN WORK
			$sitestoreMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
			
			if ($sitestoreMemberEnabled) {
				$membersTable = Engine_Api::_()->getDbtable('membership', 'sitestore');
				$membersTableName = $membersTable->info('name');
				
				$select = $membersTable->select()
								->from($membersTableName)
								->where('user_id = ?', $selected_user_id)
								->where($membersTableName . '.resource_id = ?', $store_id);
				$select = $membersTable->fetchRow($select);
				
				if(empty($select)) {
					$row = $membersTable->createRow();
					$row->resource_id = $store_id;
					$row->store_id = $store_id;
					$row->user_id = $selected_user_id;
					$row->save();
				}
			}
			//END SITESTOREMEMBER PLUGIN WORK

      $newManageAdmin = Engine_Api::_()->getItem('user', $selected_user_id);

      $sitestore_title = $sitestore->title;
      $store_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id)), 'sitestore_entry_view') . ">$sitestore_title</a>";

      $host = $_SERVER['HTTP_HOST'];
      $store_url = $host . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id)), 'sitestore_entry_view');

      Engine_Api::_()->getApi('mail', 'core')->sendSystem($newManageAdmin->email, 'SITESTORE_MANAGEADMIN_EMAIL', array(
              'store_title_with_link' => $store_title_with_link,
              'sender' => $viewer->toString(),
              'store_url' => $store_url,
              'queue' => true
      ));

      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      $notifyApi->addNotification($newManageAdmin, $viewer, $sitestore, 'sitestore_manageadmin');

      //INCREMENT MESSAGE COUNTER.
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
    }
  }

	//ACTINO FOR USER AUTO-SUGGEST LIST
  public function manageAutoSuggestAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GETTING THE STORE ID.
    $store_id = $this->_getParam('store_id', $this->_getParam('id', null));

		//FETCH DATA
		$user_idarray = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->linkedStores($store_id);

    $user_id_array = '';
    if (!empty($user_idarray)) {
      foreach ($user_idarray as $key => $user_ids) {
        $user_id_array = $user_ids['user_id'] . ',' . $user_id_array;
      }
    }
    $user_id_array = $user_id_array . '0';
    $noncreate_owner_level = array();
    $level_tbl = Engine_Api::_()->getDbtable('levels', 'authorization');
	foreach( $level_tbl->fetchAll($level_tbl->select()->order('level_order ASC')) as $level ) {
      $can_create = 0;
      if ($level->type != "public") {
        $can_create = Engine_Api::_()->authorization()->getPermission($level->level_id, 'sitestore_store', 'create');
        if (empty($can_create)) {
          $noncreate_owner_level[] = $level->level_id;
        }
      }
    }

    $usertable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $usertable->info('name');
    $select = $usertable->select()
            ->where('displayname  LIKE ? ', '%' . $this->_getParam('text') . '%')
            ->where($userTableName . '.user_id NOT IN (' . $user_id_array . ')')
            ->order('displayname ASC')
            ->limit($this->_getParam('limit', 40));

    if (!empty($noncreate_owner_level)) {
      $str = (string) ( is_array($noncreate_owner_level) ? "'" . join("', '", $noncreate_owner_level) . "'" : $noncreate_owner_level );
      $select->where($userTableName . '.level_id not in (?)', new Zend_Db_Expr($str));
    }

    //FETCH ALL RESULT.
    $userlists = $usertable->fetchAll($select);
    $data = array();

		foreach ($userlists as $userlist) {
			$content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
			$data[] = array(
							'id' => $userlist->user_id,
							'label' => $userlist->displayname,
							'photo' => $content_photo
						);
    }

    if ($this->_getParam('sendNow', true)) {

      //RETURN TO THE RETRIVE RESULT.
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  //THIS ACTION FOR DELETE MANAGE ADMIN AND CALLING FROM THE CORE.JS FILE.
  public function deleteAction() {

    $manageadmin_id = (int) $this->_getParam('managedelete_id');
    $owner_id = (int) $this->_getParam('owner_id');
    $store_id = (int) $this->_getParam('store_id');
    $manageAdmintable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
    $manageAdmintable->delete(array('manageadmin_id = ?' => $manageadmin_id));

		//START SITEBUISNESSMEMBER PLUGIN WORK
		$sitestoreMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
		if ($sitestoreMemberEnabled) {
 			$membersTable = Engine_Api::_()->getDbtable('membership', 'sitestore');
//       $membersTable->delete(array('resource_id = ?' => $page_id, 'user_id = ?' => $owner_id));
      //$manageAdmintable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
      $membersTable->delete(array('store_id = ?' => $store_id, 'user_id = ?' => $owner_id));
		}
		//END SITEBUISNESSMEMBER PLUGIN WORK

    //STAR WORK SITESTORE INTREGRATION.
		$sitestoreintegrationEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
    if(!empty($sitestoreintegrationEnabled)) {
			$contentsTable = Engine_Api::_()->getDbtable('contents', 'sitestoreintegration');
			$contentsTable->delete(array('resource_owner_id = ?' => $owner_id, 'store_id = ?' => $store_id ));
    }
    //END WORK OF SITESTORE INTREGRATION.
  }

	//ACTION FOR FEATURED ADMIN
  public function listAction() {

		//SET LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //GET STORE ID AND STORE OBJECT
    $store_id = $this->_getParam('store_id', null);
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //EDIT PRIVACY
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $manageTable = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');

		//FETCH DATA
    $this->view->owners = $manageTable->getManageAdmin($store_id);

    //CHECK POST
    if ($this->getRequest()->isPost()) {

      //GET VALUES FROM FORM
      $values = $this->getRequest()->getPost();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $manageTable->update(array('featured' => 0), array('store_id = ?' => $store_id));

        foreach ($values as $key => $value) {
          $manageTable->update(array('featured' => 1), array('user_id = ?' => $key, 'store_id = ?' => $store_id));
        }
        $db->commit();
        $this->_forwardCustom('success', 'utility', 'core', array(
                'smoothboxClose' => 500,
                'parentRedirect' => $this->_helper->url->url(array('action' => 'featured-owners', 'store_id' => $store_id), 'sitestore_dashboard'),
                'parentRedirectTime' => '1',
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Featured admins of your store have been updated successfully.'))
        ));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }
}
?>