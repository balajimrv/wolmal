<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AlbumController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_AlbumController extends Seaocore_Controller_Action_Standard {

  public function init() {

    //HERE WE CHECKING THE SITESTORE ALBUM IS ENABLED OR NOT
    $sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
    if (!$sitestorealbumEnabled) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();
    $store_id = $this->_getParam('store_id', $this->_getParam('id', null));

    //PACKAGE BASE PRIYACY START
    if (!empty($store_id)) {
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
      if ($sitestore) {
        Engine_Api::_()->core()->setSubject($sitestore);      
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

  //ACTION FOR EDIT THE ALBUM
  public function editAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK REQUERIED SUBJECT IS THERE OR NOT
    if (!$this->_helper->requireSubject('sitestore_store')->isValid())
      return;

    //GET ALBUM ID
    $album_id = $this->_getParam('album_id');

    //GET STORE ID
    $store_id = $this->_getParam('store_id');

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
    $ownerList = $sitestore->getStoreOwnerList();

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitestore_album', $album_id);
    if (!Engine_Api::_()->core()->hasSubject('sitestore_store')) {
      Engine_Api::_()->core()->setSubject($album);
    }

    //MAKE FORM
    $this->view->form = $form = new Sitestore_Form_Album_Edit();

    //START PHOTO PRIVACY WORK
    $sitestorealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
    if ($sitestorealbumEnabled) {
      $auth = Engine_Api::_()->authorization()->context;
     // $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
      	$sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
			if (!empty($sitestorememberEnabled)) {
				$roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
			} else {
				$roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 	'registered', 'everyone');
			}
//       foreach ($roles as $role) {
//         if ($form->auth_tag) {
//           if (1 == $auth->isAllowed($album, $role, 'tag')) {
//             $form->auth_tag->setValue($role);
//           }
//         }
//       }

			foreach ($roles as $roleString) {
				$role = $roleString;
				if ($role === 'like_member') {
					$role = $ownerList;
				}
				if ($form->auth_tag) {
					if (1 == $auth->isAllowed($album, $role, 'tag')) {
						$form->auth_tag->setValue($roleString);
					}
				}
			}
    }
    //END PHOTO PRIVACY WORK
    //COMMENT PRIVACY
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    $commentMax = array_search("everyone", $roles);
    foreach ($roles as $i => $role) {
      $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
    }
    //END PHOTO PRIVACY	WORK
    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $form->populate($album->toArray());
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    //PROCESS
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //GET FORM VALUES
      $values = $form->getValues();
      $album->setFromArray($values);
      $album->save();

      //CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      //$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');
      $sitestorememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
			if (!empty($sitestorememberEnabled)) {
				$roles = array('owner', 'member', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
			} else {
				$roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 	'registered', 'everyone');
			}

      //REBUILD PRIVACY
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($album) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      //START TAG PRIVACY
      if (empty($values['auth_tag'])) {
        $values['auth_tag'] = key($form->auth_tag->options);
        if (empty($values['auth_tag'])) {
          $values['auth_tag'] = 'registered';
        }
      }
      $tagMax = array_search($values['auth_tag'], $roles);
			foreach ($roles as $i => $role) {
				if ($role === 'like_member') {
					$role = $ownerList;
				}
				$auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
			}

//       foreach ($roles as $i => $role) {
//         $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
//       }
      //END TAG PRIVACY
      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_forwardCustom('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved.')),
        'smoothboxClose' => 300,
        'parentRefresh' => 300,
    ));
  }

  //ACTION FOR VIEW THE ALBUM
  public function viewAction() {

   //GET STORE ID
    $store_id = $this->_getParam('store_id');

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    
    //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        if(!Zend_Registry::isRegistered('sitemobileNavigationName')){
        Zend_Registry::set('sitemobileNavigationName','setNoRender');
        }
    }
    
    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }

  }

  //ACTION FOR DELETE THE ALBUM
  public function deleteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK REQUERIED SUBJECT IS THERE OR NOT
    if (!$this->_helper->requireSubject('sitestore_store')->isValid())
      return;

    //GET STORE ID
    $store_id = $this->_getParam('store_id', null);

    //GET SITESTORE ITEM
    $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET ALBUM ID
    $album_id = $this->_getParam('album_id', $this->_getParam('album_id', null));

    //GET ALBUM ITEM
    $album = Engine_Api::_()->getItem('sitestore_album', $album_id);

    //GET DELETE FORM
    $this->view->form = $form = new Sitestore_Form_Album_Delete();

    //CHECK ALBUM EXIST OR NOT TO DELETE
    if (!$album) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Album doesn't exist or not authorized to delete.");
      return;
    }

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //GET DB
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();
    try {

      //DELETE ALBUM
      $album->delete();

      //COMMIT
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('store_url' => Engine_Api::_()->sitestore()->getStoreUrl($store_id), 'tab' => $this->_getParam('tab')), 'sitestore_entry_view'),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => Zend_Registry::get('Zend_Translate')->_("Album has been deleted.")
    ));
  }

  //ACTION FOR EDIT PHOTOS TO THE ALBUM
  public function editPhotosAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //CHECK REQUERIED SUBJECT IS THERE OR NOT
    if (!$this->_helper->requireSubject('sitestore_store')->isValid())
      return;

    //GET SITESTORE ITEM
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET ALBUM ID
    $album_id = $this->view->album_id = $this->_getParam('album_id');

    //GET STORE ID
    $store_id = $this->view->store_id = $this->_getParam('store_id');

    //GET REQUEST ISAJAX OR NOT
    $isajax = $this->_getParam('is_ajax');

    //GET ITEM ALBUM
    $this->view->album = $album = $sitestore->getSingletonAlbum($album_id);

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitestoreproduct_main');

    //SEND CURRENT STORE NUMBER TO THE TPL
    $this->view->currentStoreNumbers = $currentStoreNumbers = $this->_getParam('stores', 1);

    //SEND PHOTOS PER STORE TO THE TPL
    $this->view->photos_per_store = $photos_per_store = 20;

    //SET STORE PHOTO PARAMS
    $paramsPhoto = array();
    $paramsPhoto['store_id'] = $store_id;
    $paramsPhoto['album_id'] = $album_id;
    $paramsPhoto['order'] = 'order ASC';
    $paramsPhoto['viewStore'] = 1;

    //GET TOTAL PHOTOS
    $total_photo = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotosCount($paramsPhoto);
    
    $store_vars = Engine_Api::_()->sitestore()->makeStore($total_photo, $photos_per_store, $currentStoreNumbers);
    $store_array = Array();
    for ($x = 0; $x <= $store_vars[2] - 1; $x++) {
      if ($x + 1 == $store_vars[1]) {
        $link = "1";
      } else {
        $link = "0";
      }
      $store_array[$x] = Array('store' => $x + 1,
          'link' => $link);
    }
    $this->view->storearray = $store_array;
    $this->view->maxstore = $store_vars[2];
    $this->view->pstart = 1;
    $this->view->total_images = $total_photo;

    //SET LIMIT PARAMS
    $paramsPhoto['start'] = $photos_per_store;
    $paramsPhoto['end'] = $store_vars[0];
    $paramsPhoto['viewStore'] = 1;

    //GETTING THE PHOTOS ACCORDING TO LIMIT
    $this->view->photos = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotos($paramsPhoto);

    //MAKE EDIT PHOTOS FORM
    $this->view->form = $form = new Sitestore_Form_Album_Photos();
    foreach ($this->view->photos as $photo) {
      $subform = new Sitestore_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->file_id, $photo->file_id);

//      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
//				$form->store_cover->addMultiOption($photo->file_id, $photo->file_id);
//      }
    }

    //CHECK FORM VALIDATION
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //CHECK FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      //return;
    }

    //GET DB
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //GET FORM VALUES
      $values = $form->getValues();
      if (!empty($values['cover'])) {
        $album->photo_id = $values['cover'];
        $album->save();
      }

//			if (isset($values['store_cover'])) {
//				Engine_Api::_()->getDbtable('stores', 'sitestore')->update(array('store_cover' => $values['store_cover']), array('store_id =?' => $store_id));
//			}

      //PROCESS
      foreach ($this->view->photos as $photo) { 
        $subform = $form->getSubForm($photo->getGuid());
        $values = $subform->getValues();
        $values = $values[$photo->getGuid()];

        //UNSET TEH PHOTO ID
        unset($values['photo_id']);

        if (isset($values['delete']) && $values['delete'] == '1') {
          $photo->delete();

          //FETCHING ALL PHOTOS
          $count = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotosCount($paramsPhoto);
          if (empty($count)) {
            Engine_Api::_()->getItemTable('sitestore_album')->update(array('photo_id' => 0,), array('store_id =?' => $store_id, 'album_id =?' => $album_id));
          }
        } else { 
          $photo->setFromArray($values);
          $photo->save();
        }
      }
      

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    if (!$isajax) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'store_id' => $store_id, 'album_id' => $album_id, 'slug' => $album->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitestore_albumphoto_general', true);
    }
  }

  public function viewAlbumAction() {

    //GET STORE ID
    $store_id = $this->_getParam('store_id', null);

    //SET SITESTORE ITEM
    $this->view->sitestore = $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');

    //SEND TAB ID TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['store_id'] = $store_id;
    $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.albumsorder', 1);
    if($albums_order) {
			$paramsAlbum['orderby'] = 'album_id DESC';
    } else {
      $paramsAlbum['orderby'] = 'album_id ASC';
    }

    //FETCH ALBUMS
    $this->view->album = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbums($paramsAlbum);
  }

  //ACTION FOR CHANGE THE ORDER OF THE PHOTOS
  public function orderAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET ALBUM ID
    $album_id = $this->_getParam('album_id');

    //GET STORE ID
    $store_id = $this->_getParam('store_id');

    //GET ORDER
    $order = $this->_getParam('order');
    if (!$order) {
      $this->view->status = false;
      return;
    }

    //GET CURRENT ORDER
    $currentOrder = Engine_Api::_()->getDbtable('photos', 'sitestore')->getStorePhotosOrder($album_id, $store_id);

    //FIND THE STARTING POINT?
    $start = null;
    $end = null;
    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if (in_array($currentOrder[$i], $order)) {
        $start = $i;
        $end = $i + count($order);
        break;
      }
    }

    if (null === $start || null === $end) {
      $this->view->status = false;
      return;
    }

    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if ($i >= $start && $i <= $end) {
        $photo_id = $order[$i - $start];
      } else {
        $photo_id = $currentOrder[$i];
      }
      Engine_Api::_()->getItemTable('sitestore_photo')->update(array('order' => $i), array('photo_id = ?' => $photo_id));
    }
    $this->view->status = true;
  }

  //ACTION FOR CHANGE THE ORDER OF THE ALBUMS
  public function albumOrderAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET STORE ID
    $store_id = $this->_getParam('store_id');

    //GET ORDER
    $order = $this->_getParam('order');
    if (!$order) {
      $this->view->status = false;
      return;
    }

    //GET CURRENT ORDER OF ALBUM
    $currentOrder = Engine_Api::_()->getDbtable('albums', 'sitestore')->getStoreAlbumsOrder($store_id);

    //FIND THE STARTING POINT?
    $start = null;
    $end = null;
    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if (in_array($currentOrder[$i], $order)) {
        $start = $i;
        $end = $i + count($order);
        break;
      }
    }

    if (null === $start || null === $end) {
      $this->view->status = false;
      return;
    }

    for ($i = 0, $l = count($currentOrder); $i < $l; $i++) {
      if ($i >= $start && $i <= $end) {
        $photo_id = $order[$i - $start];
      } else {
        $photo_id = $currentOrder[$i];
      }
      Engine_Api::_()->getItemTable('sitestore_album')->update(array('order' => $i), array('photo_id = ?' => $photo_id));
    }
    $this->view->status = true;
  }

  public function composeUploadAction() {
    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $this->_redirect('login');
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    if (empty($_FILES['Filedata'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Get album
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('albums', 'sitestore');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $type = $this->_getParam('type', 'wall');

      if (empty($type))
        $type = 'wall';
      $store_id = $this->_getParam('store_id', $this->_getParam('id', null));

      //PACKAGE BASE PRIYACY START
      $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);

      $album = $table->getSpecialAlbum($sitestore, $type);

      $photoTable = Engine_Api::_()->getDbtable('photos', 'sitestore');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
          'store_id' => $store_id,
          'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
      ));
      $photo->save();
      $photo->setPhoto($_FILES['Filedata']);

      if ($type == 'message') {
        $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
      }

      $photo->album_id = $album->album_id;
      $photo->collection_id = $album->album_id;
      $photo->save();

      if (!$album->photo_id) {
        $album->photo_id = $photo->file_id;
        $album->save();
      }

      if ($type != 'message') {
        // Authorizations
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view', true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
      }

      $db->commit();

      $this->view->status = true;
      $this->view->photo_id = $photo->photo_id;
      $this->view->album_id = $album->album_id;
      $this->view->src = $photo->getPhotoUrl();
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Photo saved successfully');
      

      $requesttype = $this->_getParam('feedphoto', false);
      if ($requesttype) {
      	echo '<img src="'. $photo->getPhotoUrl() . '" id="compose-photo-preview-image" class="compose-preview-image"><div id="advfeed-photo"><input type="hidden" name="attachment[photo_id]" value="'.$photo->photo_id.'"><input type="hidden" name="attachment[type]" value="sitestorephoto"></div>';
      	exit();
      }
    } catch (Exception $e) {
      $db->rollBack();
      //throw $e;
      $this->view->status = false;
    }
  }

  public function browseAction() {
 
    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->isValid())
      return;

     //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else { 
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }

  }

  public function homeAction() {
 
    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, 'view')->isValid())
      return;

     //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else { 
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }

  }

  public function featuredAction() {
    
    $this->view->album = $album = Engine_Api::_()->getItem('sitestore_album', $this->_getParam('album_id', $this->_getParam('album_id', null)));
    $album->featured = !$album->featured;
    $album->save();
    exit(0);
  }

  //ACTION FOR ADDING ALBUM OF THE DAY
  public function addAlbumOfDayAction() {
    //FORM GENERATION
    $form = $this->view->form = new Sitestorealbum_Form_ItemOfDayday();
    $album_id = $this->_getParam('album_id');
   // $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore');

				//FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $album_id)->where('resource_type = ?', 'sitestore_album');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $album_id;
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitestore_album';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  //'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Album of the Day has been added successfully.'))
              ));
    }
  }

}
?>