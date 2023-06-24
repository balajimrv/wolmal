<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_PhotoController extends Seaocore_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
            return;

        if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
            return;

        //SET SUBJECT
        if (!Engine_Api::_()->core()->hasSubject()) {

            if (0 != ($photo_id = (int) $this->_getParam('photo_id')) &&
                    null != ($photo = Engine_Api::_()->getItem('sitestoreproduct_photo', $photo_id))) {
                Engine_Api::_()->core()->setSubject($photo);
            } else if (0 != ($product_id = (int) $this->_getParam('product_id')) &&
                    null != ($sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id))) {
                Engine_Api::_()->core()->setSubject($sitestoreproduct);
            }
        }

        $this->_helper->requireUser->addActionRequires(array(
            'upload',
            'upload-photo',
            'edit',
        ));

        $this->_helper->requireSubject->setActionRequireTypes(array(
            'sitestoreproduct' => 'sitestoreproduct_product',
            'upload' => 'sitestoreproduct_product',
            'view' => 'sitestoreproduct_photo',
            'edit' => 'sitestoreproduct_photo',
        ));
    }

    //ACTION FOR UPLOAD PHOTO
    public function uploadAction() {

    
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->_helper->redirector->gotoRoute(array(
                'module' => 'sitestoreproduct',
                'controller' => 'photo',
                'action' => 'upload-mobile',
                'product_id' => $this->_getParam("product_id")
                    ), 'default', true);
        }
    

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET PRODUCT
        $this->view->product_id = $product_id = $this->_getParam('product_id');
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->can_edit = $sitestoreproduct->authorization()->isAllowed($viewer, "edit");

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");

        //AUTHORIZATION CHECK
        $this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($sitestoreproduct, $viewer, "photo");

        if (empty($this->view->allowed_upload_photo)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET SETTINGS
        $this->view->allowed_upload_video = Engine_Api::_()->sitestoreproduct()->allowVideo($sitestoreproduct, $viewer);

        //SELECTED TAB
        $this->view->TabActive = "photo";

        if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
            return $this->_forward('upload-photo', null, null, array('format' => 'json', 'product_id' => (int) $sitestoreproduct->getIdentity()));
        }

        //GET ALBUM
        $album = $sitestoreproduct->getSingletonAlbum();

        //MAKE FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Photo_Upload();
        $form->file->setAttrib('data', array('product_id' => $sitestoreproduct->getIdentity()));
        $this->view->tab_id = $content_id = $this->_getParam('content_id');
        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $table = Engine_Api::_()->getItemTable('sitestoreproduct_photo');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {

            $values = $form->getValues();
            $params = array(
                'product_id' => $sitestoreproduct->getIdentity(),
                'user_id' => $viewer->getIdentity(),
            );

            //ADD ACTION AND ATTACHMENTS
            $count = count($values['file']);
            $api = Engine_Api::_()->getDbtable('actions', 'activity');

            $store = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $isStoreAdmin = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer_id, $store->getIdentity());
            if ($isStoreAdmin && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {

                $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $store, 'sitestoreproduct_photo_upload', null, array('count' => count($values['file']), 'child_id' => $sitestoreproduct->getIdentity()));

                $count = 0;
                foreach ($values['file'] as $photo_id) {
                    $photo = Engine_Api::_()->getItem("sitestoreproduct_photo", $photo_id);

                    if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
                        continue;

                    $photo->collection_id = $album->album_id;
                    $photo->album_id = $album->album_id;
                    $photo->save();

                    if ($sitestoreproduct->photo_id == 0) {
                        $sitestoreproduct->photo_id = $photo->file_id;
                        $sitestoreproduct->save();
                    }

                    if ($action instanceof Activity_Model_Action && $count < 8) {
                        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                    }
                    $count++;
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if ($this->view->can_edit) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'product_id' => $album->product_id), "sitestoreproduct_albumspecific", true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('product_id' => $album->product_id, 'slug' => $sitestoreproduct->getSlug(), 'tab' => $content_id), "sitestoreproduct_entry_view", true);
        }
    }

    //ACTION FOR UPLOAD PHOTO
    public function uploadPhotoAction() {

        //GET SITESTOREPRODUCT
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', (int) $this->_getParam('product_id'));

        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        $values = $this->getRequest()->getPost();
        if (empty($values['Filename'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
            return;
        }

        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }
        $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct');
        $db = $tablePhoto->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $album = $sitestoreproduct->getSingletonAlbum();
            $rows = $tablePhoto->fetchRow($tablePhoto->select()->from($tablePhoto->info('name'), 'order')->order('order DESC')->limit(1));
            $order = 0;
            if (!empty($rows)) {
                $order = $rows->order + 1;
            }
            $params = array(
                'collection_id' => $album->getIdentity(),
                'album_id' => $album->getIdentity(),
                'product_id' => $sitestoreproduct->getIdentity(),
                'user_id' => $viewer->getIdentity(),
                'order' => $order
            );
            $photo_id = Engine_Api::_()->sitestoreproduct()->createPhoto($params, $_FILES['Filedata'])->photo_id;

            if (!$sitestoreproduct->photo_id) {
                $sitestoreproduct->photo_id = $photo_id;
                $sitestoreproduct->save();
            }

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

    //ACTION FOR EDITING OF PHOTOS TITLE AND DISCRIPTION
    public function editAction() {

        //GET PHOTO SUBJECT
        $photo = Engine_Api::_()->core()->getSubject();

        //GET VIEWER
        $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //AUTHORIZATION CHECK
        $this->view->canEdit = 0;
        if (empty($this->view->canEdit) && $photo->user_id == $viewer_id) {
            $this->view->canEdit = 1;
        }

        if (!empty($this->view->canEdit)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //MAKE FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Photo_Edit();

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            $form->populate($photo->toArray());
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $db = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct')->getAdapter();
        $db->beginTransaction();

        try {
            $photo->setFromArray($form->getValues())->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved')),
                    'layout' => 'default-simple',
                    'parentRefresh' => true,
                    'closeSmoothbox' => true,
        ));
    }

    //ACTION FOR PHOTO DELETE
    public function removeAction() {

        //GET PHOTO ID AND ITEM
        $photo_id = (int) $this->_getParam('photo_id');
        $photo = Engine_Api::_()->getItem('sitestoreproduct_photo', $photo_id);

        //GET PRODUCT
        $sitestoreproduct = $photo->getParent('sitestoreproduct_product');

        $isajax = (int) $this->_getParam('isajax');
        if ($isajax) {
            $db = Engine_Api::_()->getDbTable('photos', 'sitestoreproduct')->getAdapter();
            $db->beginTransaction();

            try {
                $photo->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }

        //MAKE FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Photo_Delete();

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            $form->populate($photo->toArray());
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = Engine_Api::_()->getDbTable('photos', 'sitestoreproduct')->getAdapter();
        $db->beginTransaction();

        try {
            $photo->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted')),
                    'layout' => 'default-simple',
                    'parentRedirect' => $sitestoreproduct->getHref(),
                    'closeSmoothbox' => true,
        ));
    }

    //ACTION FOR VIEWING THE PHOTO
    public function viewAction() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET PHOTOS
        $this->view->image = $photo = Engine_Api::_()->core()->getSubject();

        //GET SITESTOREPRODUCT DETAILS
        $this->view->sitestoreproduct = $photo->getCollection();

        // IS STORE ADMINS
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $this->view->sitestoreproduct->product_id);
        $isStoreAdmin = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer->getIdentity(), $sitestoreproduct->store_id);

        //GET SETTINGS
        $this->view->canEdit = 0;
        if ($isStoreAdmin || $photo->user_id == $viewer_id) {
            $this->view->canEdit = 1;
        }

        $this->view->canDelete = 0;
        if ($isStoreAdmin || $photo->user_id == $viewer_id) {
            $this->view->canDelete = 1;
        }

        if (!$viewer || !$viewer_id || $photo->user_id != $viewer->getIdentity()) {
            $photo->view_count = new Zend_Db_Expr('view_count + 1');
            $photo->save();
        }

        $this->view->report = 1;
        $this->view->share = 1;
        $this->view->enablePinit = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.photo.pinit', 0);
    }

    //ACTION FOR UPLOAD PHOTO
    public function uploadMobileAction() {
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET PRODUCT
        $this->view->product_id = $product_id = $this->_getParam('product_id');
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        if (empty($sitestoreproduct)) {
            return $this->_helper->requireSubject->forward();
        }

        $this->view->can_edit = $sitestoreproduct->authorization()->isAllowed($viewer, "edit");

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("sitestoreproduct_main");

        //AUTHORIZATION CHECK
        $this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($sitestoreproduct, $viewer, "photo");

        if (empty($this->view->allowed_upload_photo)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET SETTINGS
        $this->view->allowed_upload_video = Engine_Api::_()->sitestoreproduct()->allowVideo($sitestoreproduct, $viewer);

        //SELECTED TAB
        $this->view->TabActive = "photo";

        //GET ALBUM
        $album = $sitestoreproduct->getSingletonAlbum();

        //MAKE FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Mobile_Photo_Upload();
        $this->view->tab_id = $content_id = $this->_getParam('content_id');
        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $table = Engine_Api::_()->getItemTable('sitestoreproduct_photo');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {

            $values = $form->getValues();
            #### Merge with upload photo
            $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct');
            $rows = $tablePhoto->fetchRow($tablePhoto->select()->from($tablePhoto->info('name'), 'order')->order('order DESC')->limit(1));
            $order = 0;
            if (!empty($rows)) {
                $order = $rows->order + 1;
            }
            $params = array(
                'collection_id' => $album->getIdentity(),
                'album_id' => $album->getIdentity(),
                'product_id' => $sitestoreproduct->getIdentity(),
                'user_id' => $viewer->getIdentity(),
                'order' => $order
            );

            if (!empty($values['photo'])) {
                $photo_id = Engine_Api::_()->sitestoreproduct()->createMobilePhoto($params, $form->photo)->photo_id;
            }

            if (!$sitestoreproduct->photo_id) {
                $sitestoreproduct->photo_id = $photo_id;
                $sitestoreproduct->save();
            }

            ####
            //ADD ACTION AND ATTACHMENTS
            if ($photo_id) {
                $count = 1;
                $api = Engine_Api::_()->getDbtable('actions', 'activity');

                $store = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
                $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                $isStoreAdmin = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer_id, $store->getIdentity());
                if ($isStoreAdmin && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {

                    $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $store, 'sitestoreproduct_photo_upload', null, array('count' => 1, 'child_id' => $sitestoreproduct->getIdentity()));

                    $photo = Engine_Api::_()->getItem("sitestoreproduct_photo", $photo_id);

                    if ($sitestoreproduct->photo_id == 0) {
                        $sitestoreproduct->photo_id = $photo->file_id;
                        $sitestoreproduct->save();
                    }

                    if ($action instanceof Activity_Model_Action && $count < 8) {
                        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
                    }
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if ($this->view->can_edit) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'product_id' => $album->product_id), "sitestoreproduct_albumspecific", true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('product_id' => $album->product_id, 'slug' => $sitestoreproduct->getSlug(), 'tab' => $content_id), "sitestoreproduct_entry_view", true);
        }
    }

}
