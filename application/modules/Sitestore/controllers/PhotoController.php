<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_PhotoController extends Seaocore_Controller_Action_Standard {

  public function init() {
    //HERE WE CHECKING THE SITESTORE ALBUM IS ENABLED OR NOT
    $sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
    if (!$sitestorealbumEnabled) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //CHECK SUBJECT IS EXIST OR NOT IF NOT EXIST THEN SET ACCORDING TO THE STORE ID AND PHOTO ID
    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
              null !== ($photo = Engine_Api::_()->getItem('sitestore_photo', $photo_id))) {
        Engine_Api::_()->core()->setSubject($photo);
      } else if (0 !== ($store_id = (int) $this->_getParam('store_id')) &&
              null !== ($sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id))) {
        Engine_Api::_()->core()->setSubject($sitestore);
      }
    }

    //GET STORE ID
    $store_id = $this->_getParam('store_id');

    //PACKAGE BASE PRIYACY START    
    if (!empty($store_id)) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      if (!empty($sitestore)) {
        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorealbum")) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
          }
        } else {
          $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'spcreate');
          if (empty($isStoreOwnerAllow)) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
          }
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if (Engine_Api::_()->core()->hasSubject() != null) {
        $photo = Engine_Api::_()->core()->getSubject();
        $album = $photo->getCollection();
        $store_id = $album->store_id;
      }
    }
  }

  //ACTION FOR UPLOADING THE ALBUM
  public function uploadAlbumAction() {

    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			//GET THE FILEDATA IF FILEDATA IS THERE THEN CALL THE UPLOAD PHOTO ACTION
			if (isset($_GET['ul']) || isset($_FILES['Filedata']))
				return $this->_forwardCustom('upload-photo', null, null, array('format' => 'json'));

			//GET VIEWER ID 
			$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

			//GET STORE ID 
			$store_id = $this->_getParam('store_id');

			//GET NAVIGATION
			$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

			//GET SITESTORE ITEM
			$this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

			//START MANAGE-ADMIN CHECK
			$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
			if (empty($isManageAdmin)) {
				$this->view->can_edit = $can_edit = 0;
			} else {
				$this->view->can_edit = $can_edit = 1;
			}

			$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'spcreate');
			if (empty($isManageAdmin) && empty($can_edit)) {
				return $this->_forwardCustom('requireauth', 'error', 'core');
			}
			//END MANAGE-ADMIN CHECK
			//SEND TAB ID TO THE TPL 
			$this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

			//GET FORM
			$this->view->form = $form = new Sitestore_Form_Photo_Album();

			//SEND ALBUM ID TO THE TPL
			$this->view->album_id = $this->_getParam('album_id');

			if (!$can_edit) {
				$form->removeElement('album');
				$form->removeElement('auth_tag');
				$form->removeElement('title');
				$form->removeElement('search');
				if (isset($_POST['default_album_id']))
					$this->view->album_id = $album_id = $_POST['default_album_id'];
			}

			//SET STORE ID INTO THE FORM
			$form->store_id->setValue($store_id);

			//CHECK FORM VALIDATION
			if (!$this->getRequest()->isPost()) {
				if (null !== ($album_id = $this->_getParam('album_id'))) {
					$form->populate(array(
							'album' => $album_id
					));
				}
				return;
			}


			//CHECK FORM VALIDATION
			if (!$form->isValid($this->getRequest()->getPost())) {
				return;
			}

			//GET DB
			$db = Engine_Api::_()->getItemTable('sitestore_album')->getAdapter();
			$db->beginTransaction($db);
			try {
				//SAVE VALUES
				$values = $album = $form->saveValues();

				//UPDATE VALUES
				Engine_Api::_()->getDbtable('photos', 'sitestore')->update(array('album_id' => $album->getIdentity(), 'store_id' => $store_id, 'collection_id' => $album->getIdentity(),), array('store_id =?' => $store_id, 'album_id =?' => 0, 'collection_id =?' => 0, 'user_id =?' => $viewer_id));

				//COMMIT
				$db->commit();
			} catch (Exception $e) {
				$db->rollBack();
				throw $e;
			}

			//REDIRECTING
			if ($viewer_id == $sitestore->owner_id) {
				$this->_helper->redirector->gotoRoute(array('action' => 'edit-photos', 'store_id' => $store_id, 'album_id' => $album->getIdentity(), 'slug' => $album->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitestore_albumphoto_general', true);
			} else {
				$this->_helper->redirector->gotoRoute(array('action' => 'view', 'store_id' => $store_id, 'album_id' => $album->getIdentity(), 'slug' => $album->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitestore_albumphoto_general', true);
			}

    } 
    else {
			//CHECK THE VERSION OF THE CORE MODULE
			$coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
			$coreversion = $coremodule->version;
			Engine_API::_()->sitemobile()->setContentStorage();
			if ($coreversion < '4.1.0') {
				$this->_helper->content->render();
			} else {
				$this->_helper->content
							//->setNoRender()
							->setEnabled()
				;
			}
			
			//GET VIEWER ID 
			$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

			//GET STORE ID 
			$store_id = $this->_getParam('store_id');
			
			$this->view->album_id = $this->_getParam('album_id');
			$this->view->tab_selected_id = $this->_getParam('tab');
			
			//GET NAVIGATION
			$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

			//GET SITESTORE ITEM
			$this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

			//START MANAGE-ADMIN CHECK

			$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
			if (empty($isManageAdmin)) {
				$this->view->can_edit = $can_edit = 0;
			} else {
				$this->view->can_edit = $can_edit = 1;
			}

			$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'spcreate');
			if (empty($isManageAdmin) && empty($can_edit)) {
				return $this->_forwardCustom('requireauth', 'error', 'core');
			}
			//END MANAGE-ADMIN CHECK
			//
			//GET FORM
			$this->view->form = $form = new Sitestore_Form_SitemobilePhoto_Album();

			if (!$can_edit) {
				$form->removeElement('album');
				$form->removeElement('auth_tag');
				$form->removeElement('title');
				$form->removeElement('search');
				if (isset($_POST['default_album_id']))
					$this->view->album_id = $album_id = $_POST['default_album_id'];
			}

			//SET STORE ID INTO THE FORM
			$form->store_id->setValue($store_id);

			//CHECK FORM VALIDATION
			if (!$this->getRequest()->isPost()) {
				$this->view->status = false;
				$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
				if (null !== ($album_id = $this->_getParam('album_id'))) {
					$form->populate(array(
							'album' => $album_id
					));
				}
				return;
			}
		


			//CHECK FORM VALIDATION
			if (!$form->isValid($this->getRequest()->getPost())) {
				return;
			}
			

			//GET SITESTORE ITEM
			$sitestore = Engine_Api::_()->getItem('sitestore_store', (int) $this->_getParam('store_id'));

			$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
			if (empty($isManageAdmin)) {
				$can_edit = 0;
			} else {
				$can_edit = 1;
			}

			//START MANAGE-ADMIN CHECK
			if ($viewer_id != $sitestore->owner_id && empty($can_edit)) {
				$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'spcreate');
				if (empty($isManageAdmin)) {
					return $this->_forwardCustom('requireauth', 'error', 'core');
				}
			}
			//END MANAGE-ADMIN CHECK


			//CHECK MAX FILE SIZE
			if (!$this->_helper->requireUser()->checkRequire()) {
				$this->view->status = false;
				$this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
				return;
			}

			//GET FORM VALUES
			$values = $this->getRequest()->getPost();



			//GET DB
			$tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitestore');
			$db = $tablePhoto->getAdapter();
			$db->beginTransaction();

			//COUNT NO. OF PHOTOS (CHECK ATLEAST SINGLE PHOTO UPLOAD).
			$count = 0;
			foreach ($_FILES['Filedata']['name'] as $data) {
				if (!empty($data)) {
					$count = 1;
					break;
				}
			}
	//order of photos 
			$rows = $tablePhoto->fetchRow($tablePhoto->select()->from($tablePhoto->info('name'), 'order')->order('order DESC')->limit(1));
			$order = 0;
			if (!empty($rows)) {
				$order = $rows->order + 1;
			}
			try {
				if (!isset($_FILES['Filedata']) || !isset($_FILES['Filedata']['name']) || $count == 0) {
					$this->view->status = false;
					$form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
					return;
				}
        $values['file'] = array();
				foreach ($_FILES['Filedata']['name'] as $key => $uploadFile) {
					$params = array(
							'collection_id' => 0,
							'album_id' => 0,
							'store_id' => $sitestore->store_id,
							'user_id' => $viewer_id,
							'order' => $order,
					);

					$file = array('name' => $_FILES['Filedata']['name'][$key], 'tmp_name' => $_FILES['Filedata']['tmp_name'][$key], 'type' => $_FILES['Filedata']['type'][$key], 'size' => $_FILES['Filedata']['size'][$key], 'error' => $_FILES['Filedata']['error'][$key]);

					if (!is_uploaded_file($file['tmp_name'])) {
						continue;
					}
					$photoObj = $tablePhoto->createPhoto($params, $file);
					$photoObj ? $photo_id = $photoObj->photo_id : $photo_id = 0;
					$this->view->status = true;
					$this->view->name = $_FILES['Filedata']['name'][$key];
					$this->view->photo_id = $photo_id;
					$db->commit();
					$order++;
          $values['file'][] = $photoObj->photo_id;
				}
			} catch (Exception $e) {
				$db->rollBack();
				$this->view->status = false;
				$this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
				return;
			}


			//Album action
			//GET DB
			$db = Engine_Api::_()->getItemTable('sitestore_album')->getAdapter();
			$db->beginTransaction($db);
			try {
				//SAVE VALUES
				$values = $album = $form->saveValues($values);

				//UPDATE VALUES
				Engine_Api::_()->getDbtable('photos', 'sitestore')->update(array('album_id' => $album->getIdentity(), 'store_id' => $store_id, 'collection_id' => $album->getIdentity(),), array('store_id =?' => $store_id, 'album_id =?' => 0, 'collection_id =?' => 0, 'user_id =?' => $viewer_id));

				//COMMIT
				$db->commit();
			} catch (Exception $e) {
				$db->rollBack();
				throw $e;
			}

      $this->_helper->redirector->gotoRoute(array('action' => 'view', 'store_id' => $store_id, 'album_id' => $album->getIdentity(), 'slug' => $album->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitestore_albumphoto_general', true);
    }
  }

  //ACTION FOR UPLOADING THE PHOTO
  public function uploadPhotoAction() {


    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', (int) $this->_getParam('store_id'));

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    //START MANAGE-ADMIN CHECK
    if ($viewer_id != $sitestore->owner_id && empty($can_edit)) {
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'spcreate');
      if (empty($isManageAdmin)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //END MANAGE-ADMIN CHECK


    //CHECK MAX FILE SIZE
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    //CHECK FORM VALIDAION
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //GET FORM VALUES
    $values = $this->getRequest()->getPost();
    if (empty($values['Filename'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    //CHECK UPLOAD
    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

   

    //GET DB
    $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitestore');
    $db = $tablePhoto->getAdapter();
    $db->beginTransaction();
    
    $rows = $tablePhoto->fetchRow($tablePhoto->select()->from($tablePhoto->info('name'), 'order')->order('order DESC')->limit(1));
    $order = 0;
    if (!empty($rows)) {
      $order = $rows->order + 1;
    }
    try {
      $params = array(
          'collection_id' => 0,
          'album_id' => 0,
          'store_id' => $sitestore->store_id,
          'user_id' => $viewer_id,
          'order' => $order,
      );
      $photoObj = $tablePhoto->createPhoto($params, $_FILES['Filedata']);
      $photoObj ? $photo_id = $photoObj->photo_id : $photo_id = 0;
      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo_id;
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      return;
    }
  }

  //ACTION FOR EDIT THE PHOTOS TITLE AND DISCRIPTION
  public function photoEditAction() {

    //GET PHOTO SUBJECT
    $photo = Engine_Api::_()->core()->getSubject();

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET STORE ID
    $store_id = (int) $this->_getParam('store_id');

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //PHOTO OWNER, STORE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id != $photo->user_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET PHOTO ID
    $photo_id = (int) $this->_getParam('photo_id');

    //GET ALBUM ID
    $album_id = (int) $this->_getParam('album_id');

    //EDIT PHOTO FORM
    $this->view->form = $form = new Sitestore_Form_Photo_Photoedit();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $form->populate($photo->toArray());
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $this->view->tab_selected_id = $this->_getParam('tab');

    //PROCESS
    $db = Engine_Api::_()->getDbtable('photos', 'sitestore')->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE VALUES
      $photo->setFromArray($form->getValues())->save();

      //GET FORM VALUES
      $values = $form->getValues();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved.')),
                'smoothboxClose' => 2,
                'parentRedirect' => $this->_helper->url->url(array('action' => 'view',
                    'photo_id' => $photo_id, 'store_id' => $store_id, 'album_id' => $album_id, 'tab' => $this->view->tab_selected_id
                        ), 'sitestore_imagephoto_specific', true),
                'parentRedirectTime' => '2',
            ));
  }

  //ACTION FOR DELETE THE PHOTOS
  public function removeAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET PHOTO ID
    $photo_id = (int) $this->_getParam('photo_id');

    //GET STORE ID
    $store_id = (int) $this->_getParam('store_id');

    //GET ALBUM ID
    $album_id = (int) $this->_getParam('album_id');

    //GET TAB ID
    $tab_selected_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $store_id, Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0));

    //GET PHOTO ITEM
    $photo = Engine_Api::_()->getItem('sitestore_photo', $photo_id);

    //GET COLLECTION OF ALBUM
    $album = $photo->getCollection();

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //PHOTO OWNER, STORE OWNER AND SUPER-ADMIN CAN DELETE PHOTO
    if ($viewer_id != $photo->user_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET REQUEST ISAJAX OR NOT
    $isajax = (int) $this->_getParam('isajax');
    if ($isajax) {
      $photo->delete();
    }

    //GET PHOTO DELETE FORM
    $this->view->form = $form = new Sitestore_Form_Photo_Delete();

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $form->populate($photo->toArray());
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET DB
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //DELETE PHOTO
      $photo->delete();

      //SET STORE PHOTO PARAMS
      $paramsPhoto = array();
      $paramsPhoto['album_id'] = $album_id;
      $paramsPhoto['order'] = 'order ASC';

      //COUNT PHOTOS
      $count = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotosCount($paramsPhoto);

      if (empty($count)) {
        Engine_Api::_()->getItemTable('sitestore_album')->update(array('photo_id' => 0), array('album_id = ?' => $album_id, 'default_value=?' => 1));
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted.')),
                'smoothboxClose' => 2,
                'parentRedirect' => $this->_helper->url->url(array('action' => 'view', 'store_id' => $store_id, 'album_id' => $album_id, 'slug' => $album->getSlug(), 'tab' => $tab_selected_id), 'sitestore_albumphoto_general', true),
                'parentRedirectTime' => '2',
            ));
  }

  //ACTION FOR VIEWS THE PHOTOS
  public function viewAction() {

    //GET REQUEST ISAJAX OR NOT
    $this->view->isajax = (int) $this->_getParam('isajax', 0);
    $this->view->sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');

    //SEND ALBUM ID TO THE TPL
    $this->view->album_id = (int) $this->_getParam('album_id');

    //SEND ALBUM ID TO THE TPL
    $photo_id = (int) $this->_getParam('photo_id');

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

    //CHECK SUBJECT IS THERE OR NOT
    if (Engine_Api::_()->core()->hasSubject() == null) {
      return $this->_forwardCustom('notfound', 'error', 'core');
    }

    //GET PHOTO SUBJECT
    $this->view->image = $photo = Engine_Api::_()->core()->getSubject();

    //GET ALBUM INFORMATION
    $this->view->album = $album = $photo->getCollection();

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET SITESTORE ITEM
    if (!empty($album)) {
      $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $album->store_id);
    }

    //START MANAGE-ADMIN CHECK
    if (!empty($sitestore)) {
      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
      if (empty($isManageAdmin)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }

      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'comment');
      if (empty($isManageAdmin)) {
        $this->view->can_comment = 0;
      } else {
        $this->view->can_comment = 1;
      }

      $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
      if (empty($isManageAdmin)) {
        $can_edit = 0;
      } else {
        $can_edit = 1;
      }
    }
    //END MANAGE-ADMIN CHECK

    if ($can_edit) {
      $this->view->canTag = 1;
      $this->view->canUntagGlobal = 1;
    } else {
      $this->view->canTag = $album->authorization()->isAllowed($viewer, 'tag');
      $this->view->canUntagGlobal = $album->isOwner($viewer);
    }

    //PHOTO OWNER, STORE OWNER AND SUPER-ADMIN CAN EDIT AND DELETE PHOTO
    if ($viewer_id == $photo->user_id || $can_edit == 1) {
      $this->view->canDelete = 1;
      $this->view->canEdit = 1;
    } else {
      $this->view->canDelete = 0;
      $this->view->canEdit = 0;
    }

    if (!empty($viewer_id) && $viewer_id != $photo->user_id) {
      $this->view->report = $report = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.report', 1);
    }

    if (!empty($viewer_id)) {
      $this->view->share = $share = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.share', 1);
    }

    //INCREMENT VIEWS
    if (!$photo->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
      $photo->view_count++;
      ;
    }

		$this->view->allowFeatured = false;
		if (!empty($viewer_id) && $viewer->level_id == 1) {
			$auth = Engine_Api::_()->authorization()->context;
			$this->view->allowFeatured = $auth->isAllowed($sitestore, 'everyone', 'view') === 1 ? true : false || $auth->isAllowed($sitestore, 'registered', 'view') === 1 ? true : false;
		}
    //SAVE
    $photo->save();

    $this->view->showLightBox = Engine_Api::_()->sitestore()->canShowPhotoLightBox();
    $this->view->enablePinit = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.pinit', 0);
  }

  //ACTION FOR EDIT THE DESCRIPTION OF THE PHOTOS
  public function editDescriptionAction() {

    //GET TEXT
    $text = $this->_getParam('text_string');

    //GET PHOTO ITEM
    $photo = Engine_Api::_()->getItem('sitestore_photo', $this->_getParam('photo_id'));

    //GET DB
    $db = Engine_Api::_()->getDbtable('photos', 'sitestore')->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE VALUE
      $photo->description = $text;
      $photo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    exit();
  }

  //ACTION FOR ROTATE THE PHOTOS
  public function rotateAction() {

    //CHECK PHOTO SUBJECT IS OR NOT
    if (!$this->_helper->requireSubject('sitestore_photo')->isValid())
      return;

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    //GET PHOTO ITEM
    $photo = Engine_Api::_()->core()->getSubject('sitestore_photo');

    //GET ANGLE
    $angle = (int) $this->_getParam('angle', 90);
    if (!$angle || !($angle % 360)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid angle, must not be empty');
      return;
    }
    if (!in_array((int) $angle, array(90, 270))) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid angle, must be 90 or 270');
      return;
    }

    //GET FILE
    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }

    $tmpFile = $file->temporary();
    $image = Engine_Image::factory();
    $image->open($tmpFile)
            ->rotate($angle)
            ->write()
            ->destroy();

    //GET DB
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $photo->setPhoto($tmpFile);
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }

  //ACTION FOR FLIP THE PHOTOS
  public function flipAction() {

    //CHECK PHOTO SUBJECT IS OR NOT
    if (!$this->_helper->requireSubject('sitestore_photo')->isValid())
      return;

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    //GET PHOTO ITEM
    $photo = Engine_Api::_()->core()->getSubject('sitestore_photo');

    //GET DIRECTION
    $direction = $this->_getParam('direction');
    if (!in_array($direction, array('vertical', 'horizontal'))) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid direction');
      return;
    }

    //GET FILE
    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }

    $tmpFile = $file->temporary();
    $image = Engine_Image::factory();
    $image->open($tmpFile)
            ->flip($direction != 'vertical')
            ->write()
            ->destroy();

    //GET DB
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $photo->setPhoto($tmpFile);
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }

  //ACTION FOR DOWNLOAD THE PHOTOS
  public function downloadAction() {

    //GET PATH
    $path = urldecode($_GET['path']);
    $path = preg_replace('/\.{2,}/', '.', $path);
    $path = preg_replace('/[\/\\\\]+/', '/', $path);
    $path = trim($path, './\\');

    if (!Engine_Api::_()->seaocore()->isCdn()) {
      $pathArray = explode('?', $path);
      $path = $pathArray['0'];
      $pathRemoveArray = explode('/', $path);

      if ($pathRemoveArray['0'] != 'public') {
        unset($pathRemoveArray['0']);
      }
      $path = implode('/', $pathRemoveArray);
      $path = APPLICATION_PATH . '/' . $path;
    }
    $explodePath = explode("?", $path);
    $path = $explodePath['0'];
    if (ob_get_level()) {
      while (@ob_end_clean());
    }
    header("Content-Disposition: attachment; filename=" . @urlencode(basename($path)), true);
    header("Content-Transfer-Encoding: Binary", true);
    header("Content-Type: application/force-download", true);
    header("Content-Type: application/octet-stream", true);
    header("Content-Type: application/download", true);
    header("Content-Description: File Transfer", true);
    header("Content-Length: " . @filesize($path), true);
    flush();

    $fp = @fopen($path, "r");
    while (!@feof($fp)) {
      echo @fread($fp, 65536);
      flush();
    }
    @fclose($fp);

    exit();
  }

  public function makeStoreProfilePhotoAction() {
    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET STORE ID
    $store_id = $this->_getParam('store_id');

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET PHOTO
    $photo = Engine_Api::_()->getItemByGuid($this->_getParam('photo'));

    if (!$photo || !($photo instanceof Core_Model_Item_Collectible) || empty($photo->photo_id)) {
      $this->_forwardCustom('requiresubject', 'error', 'core');
      return;
    }

    //MAKE FORM
    $this->view->photo = $photo;

    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
      //PROCESS
      $table = Engine_Api::_()->getItemTable('sitestore_store');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try {

        if ($sitestore->photo_id != $photo->file_id) {

          //ENSURE THUMB.ICON AND THUMB.PROFILE EXIST
          $newStorageFile = Engine_Api::_()->getItem('storage_file', $photo->file_id);
          $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
          if ($photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.profile')) {
            try {
              $tmpFile = $newStorageFile->temporary();
              $image = Engine_Image::factory();
              $image->open($tmpFile)
                      ->resize(200, 400)
                      ->write($tmpFile)
                      ->destroy();
              $iProfile = $filesTable->createFile($tmpFile, array(
                  'parent_type' => 'sitestore_store',
                  'parent_id' => $store_id,
                  'user_id' => $sitestore->owner_id,
                  'name' => basename($tmpFile),
                      ));
              $newStorageFile->bridge($iProfile, 'thumb.profile');
              @unlink($tmpFile);
            } catch (Exception $e) {
              echo $e;
              die();
            }
          }
          if ($photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.icon')) {
            try {
              $tmpFile = $newStorageFile->temporary();
              $image = Engine_Image::factory();
              $image->open($tmpFile);
              $size = min($image->height, $image->width);
              $x = ($image->width - $size) / 2;
              $y = ($image->height - $size) / 2;
              $image->resample($x, $y, $size, $size, 48, 48)
                      ->write($tmpFile)
                      ->destroy();
              $iSquare = $filesTable->createFile($tmpFile, array(
                  'parent_type' => 'sitestore_store',
                  'parent_id' => $store_id,
                  'user_id' => $sitestore->owner_id,
                  'name' => basename($tmpFile),
                      ));
              $newStorageFile->bridge($iSquare, 'thumb.icon');
              @unlink($tmpFile);
            } catch (Exception $e) {
              echo $e;
              die();
            }
          }

          //Set it
          $sitestore->photo_id = $photo->file_id;
          $sitestore->save();
          $db->commit();

          //INSERT ACTIVITY
          //@TODO MAYBE IT SHOULD READ "CHANGED THEIR PROFILE PHOTO" ?
          $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
          $activityFeedType = null;
          if (Engine_Api::_()->sitestore()->isStoreOwner($sitestore) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
            $activityFeedType = 'sitestore_admin_profile_photo';
          elseif ($sitestore->all_post || Engine_Api::_()->sitestore()->isStoreOwner($sitestore))
            $activityFeedType = 'sitestore_profile_photo_update';


          if ($activityFeedType) {
            $action = $activityApi->addActivity(Engine_Api::_()->user()->getViewer(), $sitestore, $activityFeedType);
            Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
          }

          if ($action) {
            //WE HAVE TO ATTACH THE USER HIMSELF W/O ALBUM PLUGIN
            $activityApi
                    ->attachActivity($action, $photo);
          }
        }
      }

      //OTHERWISE IT'S PROBABLY A PROBLEM WITH THE DATABASE OR THE STORAGE SYSTEM (JUST THROW IT)
      catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your store profile photo has been successfully changed.')),
                  'parentRedirect' => $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id)), 'sitestore_entry_view', true),
                  'smoothboxClose' => true,
              ));
    }
  }

  public function featuredAction() {

    $this->view->photo = $photo = Engine_Api::_()->getItem('sitestore_photo', $this->_getParam('photo_id', $this->_getParam('photo_id', null)));
    $photo->featured = !$photo->featured;
    $photo->save();
    exit(0);
  }

  //ACTION FOR ADDING STORE OF THE DAY
  public function addPhotoOfDayAction() {


    //FORM GENERATION
    //$photo = Engine_Api::_()->core()->getSubject();
    $photo_id = $this->_getParam('photo_id');
    $form = $this->view->form = new Sitestorealbum_Form_ItemOfDayday();
    $form->setTitle('Make this Photo of the Day')
            ->setDescription('Select a start date and end date below.This photo will be displayed as "Photo of the Day" for this duration.If more than one photos of the day are found for a date then randomly one will be displayed.');

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();
      //$values["resource_id"] = $photo->getIdentity();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore');

        //FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $photo_id)->where('resource_type = ?', 'sitestore_photo');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $photo_id;
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
        $row->resource_type = 'sitestore_photo';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
                  'layout' => 'default-simple',
                  'smoothboxClose' => true,
              ));
    }
  }

  // ACTION FOR FEATURED PHOTOS CAROUSEL AFTER CLICK ON BUTTON 
  public function featuredPhotosCarouselAction() {
    //RETRIVE THE VALUE OF ITEM VISIBLE
    $this->view->itemsVisible = $limit = (int) $_GET['itemsVisible'];

    //RETRIVE THE VALUE OF NUMBER OF ROW
    $this->view->noOfRow = (int) $_GET['noOfRow'];
    //RETRIVE THE VALUE OF ITEM VISIBLE IN ONE ROW
    $this->view->inOneRow = (int) $_GET['inOneRow'];

    // Total Count Featured Photos
    $totalCount = (int) $_GET['totalItem'];

    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'] * $limit;

    if ($startindex > $totalCount) {
      $startindex = $totalCount - $limit;
    }
    if ($startindex < 0)
      $startindex = 0;

    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $direction = $_GET['direction'];
    $this->view->offset = $values['start_index'] = $startindex;
    $values['category_id'] = $_GET['category_id'];
    //GET Featured Photos with limit * 2
    $this->view->totalItemsInSlide = $values['limit'] = $limit * 2;
    $this->view->featuredPhotos = $featuredPhotos = Engine_Api::_()->sitestorealbum()->getFeaturedPhotos($values);

    //Pass the total number of result in tpl file
    $this->view->count = count($featuredPhotos);

    //Pass the direction of button in tpl file
    $this->view->direction = $direction;
    $this->view->showLightBox = Engine_Api::_()->sitestore()->canShowPhotoLightBox();
    if ($this->view->showLightBox) {
      $this->view->params = $params = array('type' => 'featured', 'count' => $totalCount);
    }
  }
  
  public function suggestAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      $data = null;
    } else {
    
      $store_id =  $this->_getParam('store_id');
      $values['store_id'] = $store_id;
      
      $data = array();
      if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))) ) {
        $values['search'] = $text;
      }
      
      $select = Engine_Api::_()->getDbtable('membership', 'sitestore')->getsitestoremembersSelect($values);

      foreach( $select->getTable()->fetchAll($select) as $friend ) {
        $data[] = array(
          'type'  => 'user',
          'id'    => $friend->getIdentity(),
          'guid'  => $friend->getGuid(),
          'label' => $friend->getTitle(),
          'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
          'url'   => $friend->getHref(),
        );
      }
    }

    if( $this->_getParam('sendNow', true) ) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }
}

?>