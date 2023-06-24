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
class Sitestore_ProfileController extends Seaocore_Controller_Action_Standard {

  //ACTION FOR SENDING A MESSGE TO STORE OWNER
  public function messageOwnerAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET STORE ID AND STORE OBJECT
    $store_id = $this->_getParam("store_id");
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //STORE OWNER CAN'T SEND MESSAGE TO HIMSELF
    if ($viewer_id == $sitestore->owner_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //FORM GENERATION
    $this->view->form = $form = new Messages_Form_Compose();
    $form->setTitle('Contact Store Owner');
    $form->setDescription('Create your message with the form given below. Your message will be sent to the admins of this Store.');
    $form->removeElement('to');

    //GET ADMINS ID FOR SENDING MESSAGE
    $manageAdminData = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->getManageAdmin($store_id);
    $manageAdminData = $manageAdminData->toArray();
    $ids = '';
    if (!empty($manageAdminData)) {
      foreach ($manageAdminData as $key => $user_ids) {
        $user_id = $user_ids['user_id'];
        if ($viewer_id != $user_id) {
          $ids = $ids . $user_id . ',';
        }
      }
    }
    $ids = trim($ids, ',');
    $form->toValues->setValue($ids);

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      $values = $this->getRequest()->getPost();

      $form->populate($values);

      $is_error = 0;
      if (empty($values['title'])) {
        $is_error = 1;
      }

      //SENDING MESSAGE
      if ($is_error == 1) {
        $error = $this->view->translate('Subject is required field !');
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      $recipients = preg_split('/[,. ]+/', $values['toValues']);

      //LIMIT RECIPIENTS IF IT IS NOT A SPECIAL LIST OF MEMBERS
      $recipients = array_slice($recipients, 0, 1000);

      //CLEAN THE RECIPIENTS FOR REPEATING IDS
      //THIS CAN HAPPEN IF RECIPIENTS IS SELECTED AND THEN A FRIEND LIST IS SELECTED
      $recipients = array_unique($recipients);

      $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);

      $sitestore_title = $sitestore->title;
      $store_title_with_link = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id)), 'sitestore_entry_view') . ">$sitestore_title</a>";

      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
              $viewer, $recipients, $values['title'], $values['body'] . "<br><br>" . $this->view->translate("This message corresponds to the Store:") . $store_title_with_link
      );

      foreach ($recipientsUsers as $user) {
        if ($user->getIdentity() == $viewer->getIdentity()) {
          continue;
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                $user, $viewer, $conversation, 'message_new'
        );
      }

      //INCREMENT MESSAGES COUNTER
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      $db->commit();

      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => false,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'))
              ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR TELL TO THE FRIEND FOR THIS STORE
  public function tellAFriendAction() {

    //DEFAULT LAYOUT   
    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')){
    $this->_helper->layout->setLayout('default-simple');
    }

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewr_id = $viewer->getIdentity();

    //GET STORE ID AND STORE OBJECT
    $store_id = $this->_getParam('store_id', $this->_getParam('id', null));
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (empty($sitestore))
      return $this->_forwardCustom('notfound', 'error', 'core');
    //AUTHORIZATION CHECK FOR TELL A FRIEND
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'tfriend');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //FORM GENERATION
    $this->view->form = $form = new Sitestore_Form_TellAFriend();

    if (!empty($viewr_id)) {
      $value['sender_email'] = $viewer->email;
      $value['sender_name'] = $viewer->displayname;
      $form->populate($value);
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();

      //EDPLODES EMAIL IDS
      $reciver_ids = explode(',', $values['sitestore_reciver_emails']);

      if (!empty($values['sitestore_send_me'])) {
        $reciver_ids[] = $values['sitestore_sender_email'];
      }
      $sender_email = $values['sitestore_sender_email'];

      //CHECK VALID EMAIL ID FORMITE
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);

      if (!$validator->isValid($sender_email)) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
        return;
      }
      foreach ($reciver_ids as $reciver_id) {
        $reciver_id = trim($reciver_id, ' ');
        if (!$validator->isValid($reciver_id)) {
          $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
          return;
        }
      }
      $sender = $values['sitestore_sender_name'];
      $message = $values['sitestore_message'];
      $heading = ucfirst($sitestore->getTitle());
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITESTORE_TELLAFRIEND_EMAIL', array(
          'host' => $_SERVER['HTTP_HOST'],
          'sender_name' => $sender,
          'store_title' => $heading,
          'message' => '<div>' . $message . '</div>',
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()),
          'sender_email' => $sender_email,
          'queue' => true
      ));

      if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
				$this->_forwardCustom('success', 'utility', 'core', array(          
          'parentRedirect' => $sitestore->getHref(),          
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.'))
      ));
      }else{
      $this->_forwardCustom('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => false,
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to your friend has been sent successfully.'))
      ));
      }
    }
  }

  //ACTION FOR PRINTING THE STORE
  public function printAction() {
    $this->_helper->layout->setLayout('default-simple');
    //GET STORE ID AND STORE OBJECT
    $this->view->store_id = $store_id = $this->_getParam('store_id', $this->_getParam('id', null));
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (empty($sitestore))
      return $this->_forwardCustom('notfound', 'error', 'core');
    //AUTHORIZATION CHECK FOR PRINTING
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'print');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    if ($sitestore->category_id != 0)
      $this->view->category = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestore->category_id);

    if ($sitestore->subcategory_id != 0)
      $this->view->subcategory = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestore->subcategory_id);

    if ($sitestore->subsubcategory_id != 0)
      $this->view->subsubcategory = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($sitestore->subsubcategory_id);
    
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestore/View/Helper', 'Sitestore_View_Helper');
     $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitestore);
  }

  //ACTION FOR WRITE BOX AT VIEW PROFILE STORE
  public function displayAction() {

    //GET THE TEXT STRING
    $text = $this->_getParam('text_string');

    //GET STORE ID
    $store_id = $this->_getParam('store_id');

    $writesTable = Engine_Api::_()->getDbtable('writes', 'sitestore')->setWriteContent($store_id, $text);
    exit();
  }

  public function contactDetailAction() {

    //GET STORE ID
    $store_id = $this->_getParam("store_id");

    //GET SITESTORE ITEM    
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //GET PHONE
    $phone = $this->_getParam('phone');

    //GET EMAIL
    $email = $this->_getParam('email');

    //GET WEBSITE
    $website = $this->_getParam('website');

     $phone = $this->view->string()->stripTags($phone);
    $email = $this->view->string()->stripTags($email);
    $website = $this->view->string()->stripTags($website);

    //SAVE DETAILS
    $sitestore->phone = $phone;
    $sitestore->email = $email;
    $sitestore->website = $website;
    $sitestore->save();
  }

  public function getCoverPhotoAction() {

    //GET STORE ID
    $store_id = $this->_getParam("store_id");
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    //START MANAGE-ADMIN CHECK
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    $onlyMemberWithPhoto = $this->_getParam("onlyMemberWithPhoto", 1);
    if (empty($sitestore->store_cover)) {
      $this->view->show_member = $show_member = $this->_getParam("show_member", 0);
      if ($show_member) {
        $this->view->members = $members = Engine_Api::_()->getDbtable('membership', 'sitestore')->getJoinMembers($sitestore->store_id, null,null,$onlyMemberWithPhoto);
        $this->view->membersCount = $members->getTotalItemCount();
        $this->view->membersCountView = $this->_getParam("memberCount", 8);
      }
      return;
    }
    $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitestore');
    $album = $tableAlbum->getSpecialAlbum($sitestore, 'cover');
    //$otherinfo = Engine_Api::_()->getDbtable('otherinfo', 'sitestore')->getOtherinfo($store_id);
    $this->view->photo = $photo = Engine_Api::_()->getItem('sitestore_photo', $sitestore->store_cover);

    $this->view->coverTop = 0;
    $this->view->coverLeft = 0;
    if ($album->cover_params && isset($album->cover_params['top'])) {
      $this->view->coverTop = $album->cover_params['top'];
    }
  }
  
  //ACTION FOR Email Me FOR THIS STORE
  public function emailMeAction() {

    //DEFAULT LAYOUT
    $this->_helper->layout->setLayout('default-simple');

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewr_id = $viewer->getIdentity();

    //GET STORE ID AND STORE OBJECT
    $this->view->store_id  = $store_id = $this->_getParam('store_id', $this->_getParam('id', null));
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    if (empty($sitestore))
      return $this->_forwardCustom('notfound', 'error', 'core');
      
    //AUTHORIZATION CHECK FOR TELL A FRIEND
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'tfriend');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //FORM GENERATION
    $this->view->form = $form = new Sitestore_Form_EmailMe();

    if (!empty($viewr_id)) {
      $value['sender_email'] = $viewer->email;
      $value['sender_name'] = $viewer->displayname;
      $form->populate($value);
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();

      //EDPLODES EMAIL IDS
      $reciver_ids = $sitestore->email; //explode(',', $values['sitestore_reciver_emails']);
      $values['sitestore_sender_email'] = $sitestore->email;
      if (!empty($values['sitestore_send_me'])) {
        $reciver_ids = $values['sitestore_sender_email'];
      }
      $sender_email = $values['sitestore_sender_email'];

      //CHECK VALID EMAIL ID FORMITE
      $validator = new Zend_Validate_EmailAddress();
      $validator->getHostnameValidator()->setValidateTld(false);

      if (!$validator->isValid($sender_email)) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid sender email address value'));
        return;
      }
//       foreach ($reciver_ids as $reciver_id) {
//         $reciver_id = trim($reciver_id, ' ');
//         if (!$validator->isValid($reciver_id)) {
//           $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter correct email address of the receiver(s).'));
//           return;
//         }
//       }
      $sender = $values['sitestore_sender_name'];
      $message = $values['sitestore_message'];
      $heading = ucfirst($sitestore->getTitle());
      Engine_Api::_()->getApi('mail', 'core')->sendSystem($reciver_ids, 'SITESTORE_EMAILME_EMAIL', array(
          'host' => $_SERVER['HTTP_HOST'],
          'sender_name' => $sender,
          'store_title' => $heading,
          'message' => '<div>' . $message . '</div>',
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] . Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()),
          'sender_email' => $sender_email,
          'queue' => true
      ));

      $this->_forwardCustom('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => false,
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message to store owner has been sent successfully.'))
      ));
    }
  }

  public function uploadCoverPhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //LAYOUT
    $this->_helper->layout->setLayout('default-simple');
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    //STORE ID
    $store_id = $this->_getParam('store_id');

    $special = $this->_getParam('special', 'cover');
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //CHECK FORM VALIDATION
    $file='';
    $notNeedToCreate=false;
    $photo_id = $this->_getParam('photo_id');
    if ($photo_id) {
      $photo = Engine_Api::_()->getItem('sitestore_photo', $photo_id);
      $album = Engine_Api::_()->getItem('sitestore_album', $photo->album_id);
      if ($album && $album->type == 'cover') {
        $notNeedToCreate = true;
      }
      if ($photo->file_id && !$notNeedToCreate)
        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo->file_id);
    }


		//PROCESS
		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();
		try {
			//CREATE PHOTO
			$tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitestore');
			if (!$notNeedToCreate) {
				$photo = $tablePhoto->createRow();
				$photo->setFromArray(array(
						'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
						'store_id' => $store_id
				));
				$photo->save();
				if ($file) {
					$photo->setPhoto($file);
				} else {
					$photo->setPhoto($_FILES['Filedata'],true);
				}


				$tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitestore');
				$album = $tableAlbum->getSpecialAlbum($sitestore, $special);

				$tablePhotoName = $tablePhoto->info('name');
				$photoSelect = $tablePhoto->select()->from($tablePhotoName, 'order')->where('album_id = ?', $album->album_id)->order('order DESC')->limit(1);
				$photo_rowinfo = $tablePhoto->fetchRow($photoSelect);
				$photo->collection_id = $album->album_id;
				$photo->album_id = $album->album_id;
				$order = 0;
				if (!empty($photo_rowinfo)) {
					$order = $photo_rowinfo->order + 1;
				}
				$photo->order = $order;
				$photo->save();
			}

			$album->cover_params = $this->_getParam('position', array('top' => '0', 'left' => 0));
			$album->save();
			if (!$album->photo_id) {
				$album->photo_id = $photo->file_id;
				$album->save();
			}
			$sitestore->store_cover = $photo->photo_id;
			$sitestore->save();
			//ADD ACTIVITY
			$viewer = Engine_Api::_()->user()->getViewer();
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$activityFeedType = null;
			if (Engine_Api::_()->sitestore()->isStoreOwner($sitestore) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
				$activityFeedType = 'sitestore_admin_cover_update';
			elseif ($sitestore->all_post || Engine_Api::_()->sitestore()->isStoreOwner($sitestore))
				$activityFeedType = 'sitestore_cover_update';


			if ($activityFeedType) {
				$action = $activityApi->addActivity($viewer, $sitestore, $activityFeedType);
			}
			if ($action) {
				Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
				if ($photo)
					Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
			}

			$this->view->status = true;
			$db->commit();
      return $this->_redirectCustom($sitestore->getHref());
		} catch (Exception $e) {
			$db->rollBack();
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
			return;
		}
  }

  public function removeCoverPhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    $store_id = $this->_getParam('store_id');
    if ($this->getRequest()->isPost()) {
      $special = $this->_getParam('special', 'cover');
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      $sitestore->store_cover = 0;
      $sitestore->save();
      $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitestore');
      $album = $tableAlbum->getSpecialAlbum($sitestore, $special);
      $album->cover_params = array('top' => '0', 'left' => 0);
      $album->save();
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
  }

  public function getAlbumsPhotosAction() {
    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;
    $sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
    if (!$sitestorealbumEnabled) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //GET STORE ID
    $store_id = $this->_getParam("store_id");
    $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    ////START MANAGE-ADMIN CHECK
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($can_edit))
      return;
    //FETCH ALBUMS
    $this->view->recentAdded = $recentAdded = $this->_getParam("recent", false);
    $this->view->album_id = $album_id = $this->_getParam("album_id");
    if ($album_id) {
      $this->view->album = $album = Engine_Api::_()->getItem('sitestore_album', $album_id);
      $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
      $paginator->setItemCountPerPage(10000);
    } elseif ($recentAdded) {
      $paginator = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotos(array('store_id' => $store_id, 'orderby' => 'photo_id DESC', 'start' => 0, 'end' => 100));
    } else {
      $paramsAlbum['store_id'] = $store_id;
      $paginator = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbums($paramsAlbum);
    }
    $this->view->paginator = $paginator;
  }

}