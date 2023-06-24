<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminClaimController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_AdminClaimController extends Core_Controller_Action_Admin {

  //ACTION FOR GETTING THE LIST OF CLAIMABLE STORE CREATORS
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_claim');

    //GET STORE
    $store = $this->_getParam('page', 1);

    //FILTER FORM
    $this->view->formFilter = $formFilter = new Sitestore_Form_Admin_Filter();

    //GET LIST MEMBER CLAIM TABLE
    $tableListMemberClaim = Engine_Api::_()->getDbtable('listmemberclaims', 'sitestore');

    //GET LIST MEMBER CLAIM TABLE NAME
    $tableListMemberClaimsName = $tableListMemberClaim->info('name');

    //GET USER TABLE NAME
    $tableUserName = Engine_Api::_()->getDbtable('users', 'user')->info('name');

    //SELECTING THE USERS WHOSE STORE CAN BE CLAIMED
    $select = $tableListMemberClaim->select()
            ->setIntegrityCheck(false)
            ->from($tableListMemberClaimsName)
            ->join($tableUserName, $tableUserName . '.user_id = ' . $tableListMemberClaimsName . '.user_id');
    $values = array();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    //VALUES
    $values = array_merge(array(
        'order' => "$tableListMemberClaimsName.user_id",
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    //SELECT
    $select->order((!empty($values['order']) ? $values['order'] : "$tableListMemberClaimsName.user_id" ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPagenumber($store);
  }

  //ACTION FOR GETTING THE LIST OF CLAIM MEMBER
  public function listclaimmemberAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM
    $form = $this->view->form = new Sitestore_Form_Admin_Listclaimmember();

    //SET ACTION
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET VALUES
      $values = $form->getValues();

      //GET USER ID
      $userid = $values['user_id'];

      //CHECK USER ID
      if ($userid == 0) {
        $this->view->status = false;
        $error = Zend_Registry::get('Zend_Translate')->_('This is not a valid user name. Please select a user name from the autosuggest.');
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //GET LIST MEMBER CLAIM TABLE
        $table = Engine_Api::_()->getDbTable('listmemberclaims', 'sitestore');

        //FETCH				
        $row = $table->fetchRow($table->getClaimListMember($userid));
        if ($row === null) {
          include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 300,
          'parentRefresh' => 300,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('The claimed store creator has been added successfully.'))
      ));
    }
  }

  //ACTION FOR GETTING THE MEMBER WHICH CAN BE CLAIMED THE STORE
  function getmemberAction() {

    //FETCH USER LIST
    $userlists = Engine_Api::_()->getDbTable('listmemberclaims', 'sitestore')->getMembers($this->_getParam('text'), $this->_getParam('limit', 40));

    //MAKING DATA
    $data = array();
    $mode = $this->_getParam('struct');
    if ($mode == 'text') {
      foreach ($userlists as $userlist) {
        $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array('id' => $userlist->user_id, 'label' => $userlist->displayname, 'photo' => $content_photo);
      }
    } else {
      foreach ($userlists as $userlist) {
        $content_photo = $this->view->itemPhoto($userlist, 'thumb.icon');
        $data[] = array('id' => $userlist->user_id, 'label' => $userlist->displayname, 'photo' => $content_photo);
      }
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR DELETEING THE CLAIMABLE STORE CREATORS
  public function deleteClaimableMemberAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost()) {
      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //MEMBER DELETE
        Engine_Api::_()->getDbtable('listmemberclaims', 'sitestore')->delete(array('user_id =?' => $this->_getParam('user_id')));
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    //OUTPUT
    $this->renderScript('admin-claim/delete-claimable-member.tpl');
  }

  //ACTION FOR DISPLAYING THE LIST OP CLAIM STORES
  public function processclaimAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_claim');

    //FILTER FORM         
    $this->view->formFilter = $formFilter = new Sitestore_Form_Admin_Filter();

    //CLAIM TABLE
    $tableClaim = Engine_Api::_()->getDbtable('claims', 'sitestore');
    $tableClaimName = $tableClaim->info('name');

    //USER TABLE NAME
    $tableUserName = Engine_Api::_()->getDbtable('users', 'user')->info('name');

    //STORE TABLE NAME
    $tableStoreName = Engine_Api::_()->getDbtable('stores', 'sitestore')->info('name');

    //GET STORE
    $store = $this->_getParam('page', 1);

    //SELECT
    $select = $tableClaim->select()
            ->setIntegrityCheck(false)
            ->from($tableClaimName)
            ->join($tableUserName, $tableUserName . '.user_id = ' . $tableClaimName . '.user_id', array('displayname'))
            ->join($tableStoreName, $tableStoreName . '.store_id = ' . $tableClaimName . '.store_id', array('title', 'owner_id'))
            ->group($tableClaimName . '.claim_id');

    //VALUES     
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    //VALUES
    $values = array_merge(array(
        'order' => 'store_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : "store_id" ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //MAKE PAGINATOR			
    $this->view->paginator = array();
    include APPLICATION_PATH . '/application/modules/Sitestore/controllers/license/license2.php';
  }

  //ACTION FOR WHAT SHOULD BE HAPPEN WITH THE STORES WHICH ARE CLAIMED BY THE USERS
  public function takeActionAction() {

    //GET STORE ID
    $this->view->sitestore_id = $storeid = $this->_getParam('store_id');

    //GET CLAIM ID
    $claimid = $this->_getParam('claim_id');

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $storeid);

    //SEND SITESTORE TITLE TO THE TPL
		$storetitle = $this->view->sitestore_title = $sitestore->title;

    //GET CLAIM ITEM
    $claiminfo = Engine_Api::_()->getItem('sitestore_claim', $claimid);

    //GET CLAIM ROW
    $this->view->claiminfo = $claiminfo;

    //COMMENTS
    $comments_mail = '';

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost()) {
      //GET STATUS
      $status = $_POST['status'];

      //GET COMMENTS    
      $comments = $_POST['comments'];

      //CHECK COMMENTS VALIDATION
      if (!empty($comments)) {
        $comments_mail .= Zend_Registry::get('Zend_Translate')->_("Administrator's Comments: ") . $comments;
      }

      //GET MODIFIED DATE
      $modified_date = new Zend_Db_Expr('NOW()');

      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      //GET STORE URL
      $store_url = Engine_Api::_()->sitestore()->getStoreUrl($storeid);

      //GET ADMIN EMAIL
      $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;

      try {
        //GET SITESTORE TABLE
        $tableStores = Engine_Api::_()->getDbtable('stores', 'sitestore');
        $tableClaim = Engine_Api::_()->getDbtable('claims', 'sitestore');
        //CHECK STATUS
        if ($status != 2) {
          if ($status == 1) {
            //UPDATE STORE TABLE
            $tableStores->update(array('owner_id' => $claiminfo->user_id, 'userclaim' => 0), array('store_id = ?' => $storeid));

            //SEND CHANGE OWNER EMAIL
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(Engine_Api::_()->getItem('user', $sitestore->owner_id)->email, 'SITESTORE_CHANGEOWNER_EMAIL', array(
                'store_title' => $storetitle,
                'store_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true) . '"  >' . $storetitle . ' </a>',
                'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true),
                'site_contact_us_link' => 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/contact',
                'email' => $email,
                'queue' => true
            ));

            //START FOR INRAGRATION WORK WITH OTHER PLUGIN. DELETE ACCORDING TO STORE ID.
					  $sitestoreintegrationEnabled = Engine_Api::_()->getDbtable('modules',
						'core')->isModuleEnabled('sitestoreintegration');
						if(!empty($sitestoreintegrationEnabled)) {
							$contentsTable = Engine_Api::_()->getDbtable('contents', 'sitestoreintegration');
							$contentsTable->delete(array('store_id = ?' => $storeid));
						}
						//END FOR INRAGRATION WORK WITH OTHER PLUGIN.

            //UPDATE IN CONTENT STORE TABLE
            Engine_Api::_()->getDbtable('contentstores', 'sitestore')->update(array('user_id' => $claiminfo->user_id), array('store_id = ?' => $storeid));

            //UPDATE AND DELETE IN MANAGE ADMIN TABLE
            Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->delete(array('user_id = ?' => $claiminfo->user_id, 'store_id = ?' => $storeid));
            Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->update(array('user_id' => $claiminfo->user_id), array('user_id = ?' => $sitestore->owner_id, 'store_id = ?' => $storeid));

            //UPDATE PHOTO TABLE
            Engine_Api::_()->getDbtable('photos', 'sitestore')->update(array('user_id' => $claiminfo->user_id), array('user_id = ?' => $sitestore->owner_id, 'store_id = ?' => $storeid));

            //UPDATE ALBUM TABLE
            Engine_Api::_()->getDbtable('albums', 'sitestore')->update(array('owner_id' => $claiminfo->user_id), array('owner_id = ?' => $sitestore->owner_id, 'store_id = ?' => $storeid));

            //UPDATE CLAIM TABLE
            $tableClaim->update(array('status' => $status, 'comments' => $comments, 'modified_date' => $modified_date), array('claim_id = ?' => $claimid));

            //SEND EMAIL FOR CLAIM APPROVED
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($claiminfo->email, 'SITESTORE_CLAIM_APPROVED_EMAIL', array(
                'store_title' => $storetitle,
                'store_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true) . '"  >' . $storetitle . ' </a>',
                'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true),
                'comments' => $comments_mail,
                'my_claim_stores_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'my-stores'), 'sitestore_claimstores', true),
                'email' => $email,
                'queue' => true
            ));
          } elseif ($status == 4) {
            //UPDATE CLAIM TABLE
            $tableClaim->update(array('status' => $status, 'comments' => $comments, 'modified_date' => $modified_date), array('claim_id = ?' => $claimid));
            //SEND EMAIL FOR CLAIM HOLD
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($claiminfo->email, 'SITESTORE_CLAIM_HOLDING_EMAIL', array(
                'store_title' => $storetitle,
                'store_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true) . '"  >' . $storetitle . ' </a>',
                'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true),
                'comments' => $comments_mail,
                'email' => $email,
                'queue' => true
            ));
          }
        } else {
          //UPDATE CLAIM TABLE
          $tableClaim->update(array('status' => 2, 'comments' => $comments, 'modified_date' => $modified_date), array('store_id = ?' => $storeid, 'user_id=?' => $claiminfo->user_id));
          //SEND EMAIL FOR CLAIM DECLINED
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($claiminfo->email, 'SITESTORE_CLAIM_DECLINED_EMAIL', array(
              'store_title' => $storetitle,
              'store_title_with_link' => '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true) . '"  >' . $storetitle . ' </a>',
              'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => $store_url), 'sitestore_entry_view', true),
              'comments' => $comments_mail,
              'email' => $email,
              'queue' => true
          ));
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //REDIRECTING
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 300,
          'parentRefresh' => 300,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your action has been submitted and email successfully sent to the claimer.'))
      ));
    }
  }

  //ACTION FOR DELETING THE CLAIM REQUEST OF THE USERS
  public function requestDeleteAction() {

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost()) {
      //GET DB
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //GET CLAIM ITEM
        $sitestoreclaim = Engine_Api::_()->getItem('sitestore_claim', $this->_getParam('claim_id'));
        if ($sitestoreclaim) {
          $sitestoreclaim->delete();
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //REDIRECTING
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 20,
          'parentRefresh' => 20,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
    //OUTPUT
    $this->renderScript('admin-claim/request-delete.tpl');
  }

  //ACTION FOR MULTI DELETING THE CLAIM REQUEST OF THE USERS
  public function multiDeleteRequestAction() {

    //GET CLAIM IDS
    $this->view->ids = $claim_ids = $this->_getParam('ids', null);

    //CONFIRM
    $confirm = $this->_getParam('confirm', false);

    //COUNT MEMBER IDS
    $this->view->count = count(explode(",", $claim_ids));

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $confirm == true) {
      //MAKING CLAIM IDS ARRAY
      $claim_ids_array = explode(",", $claim_ids);

      //DELETE CLAIM
      foreach ($claim_ids_array as $claim_id) {
        $sitestoreclaim = Engine_Api::_()->getItem('sitestore_claim', $claim_id);
        if ($sitestoreclaim) {
          $sitestoreclaim->delete();
        }
      }

      //REDIRECTING
      $this->_helper->redirector->gotoRoute(array('action' => 'processclaim'));
    }
  }

  //ACTION FOR MULTIDELETING THE MEMBER OF CLAIMABLE STORE CREATORS
  public function multiDeleteClaimableMemberAction() {

    //GET MEMEBR IDS
    $this->view->ids = $member_ids = $this->_getParam('ids', null);

    //CONFIRM
    $confirm = $this->_getParam('confirm', false);

    //COUNT MEMBER IDS
    $this->view->count = count(explode(",", $member_ids));

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $confirm == true) {
      //MAKING MEMBER IDS ARRAY
      $member_ids_array = explode(",", $member_ids);

      //DELETE MEMBER
      foreach ($member_ids_array as $member_id) {
        $sitestoreclaimmember = Engine_Api::_()->getItem('sitestore_listmemberclaims', $member_id);
        if ($sitestoreclaimmember) {
          $sitestoreclaimmember->delete();
        }
      }

      //REDIRECTING
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

}

?>