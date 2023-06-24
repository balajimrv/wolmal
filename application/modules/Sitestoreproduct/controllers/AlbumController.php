<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AlbumController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_AlbumController extends Core_Controller_Action_Standard {

    //ACTION FOR EDIT PHOTO
    public function editphotosAction() {

    
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            return $this->_helper->redirector->gotoRoute(array(
                        'module' => 'sitestoreproduct',
                        'controller' => 'album',
                        'action' => 'editphotos-mobile',
                        'product_id' => $this->_getParam("product_id")
                            ), 'default', true);
        }
    

        //LOGGEND IN USER CAN EDIT PHOTO
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PRODUCT ID AND OBJECT
        $this->view->product_id = $product_id = $this->_getParam('product_id');
        $change_url = $this->_getParam('change_url', 0);
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
            return;

        //IF SITESTOREPRODUCT IS NOT EXIST
        if (empty($sitestoreproduct)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //SET PRODUCT SUBJECT
        Engine_Api::_()->core()->setSubject($sitestoreproduct);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        $this->view->allowed_upload_video = Engine_Api::_()->sitestoreproduct()->allowVideo($sitestoreproduct, $viewer);
        if (!$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, $viewer, "edit")->isValid()) {
            return;
        }

        //AUTHORIZATION CHECK
        $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($sitestoreproduct, $viewer, "photo");
        if (empty($allowed_upload_photo)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->sitestores_view_menu = 7;

        //PREPARE DATA
        $this->view->album = $album = $sitestoreproduct->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());
        $this->view->count = count($paginator);
        $this->view->slideShowEnanle = $slideShowEnanle = $slideShowEnable = $this->slideShowEnable();

        //MAKE FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Album_Photos();
        $this->view->enableVideoPlugin = $slideShowEnanle ? Engine_Api::_()->sitestoreproduct()->allowVideo($sitestoreproduct, $viewer) : 0;
        if ($this->view->enableVideoPlugin) {
            $form->addElement('Radio', 'video_snapshot_id', array(
                'label' => 'Video Snapshot',
            ));
        }
        foreach ($paginator as $photo) {
            $subform = new Sitestoreproduct_Form_Photo_SubEdit(array('elementsBelongTo' => $photo->getGuid()));
            if (empty($slideShowEnable)) {
                $subform->removeElement('show_slidishow');
            }
            $subform->populate($photo->toArray());
            $form->addSubForm($subform, $photo->getGuid());
            $form->cover->addMultiOption($photo->file_id, $photo->file_id);
            if ($this->view->enableVideoPlugin) {
                $form->video_snapshot_id->addMultiOption($photo->photo_id, $photo->photo_id);
            }
        }

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = Engine_Api::_()->getDbTable('albums', 'sitestoreproduct');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $values = $form->getValues();
            if (!empty($values['cover']) && $sitestoreproduct->photo_id != $values['cover']) {

                $album->photo_id = $values['cover'];
                $album->save();

                $sitestoreproduct->photo_id = $values['cover'];
                $sitestoreproduct->save();
                $sitestoreproduct->updateAllCoverPhotos();
            }

            if (!empty($values['video_snapshot_id'])) {
                $sitestoreproduct->video_snapshot_id = $values['video_snapshot_id'];
                $sitestoreproduct->save();
            }

            //PROCESS
            foreach ($paginator as $photo) {

                $subform = $form->getSubForm($photo->getGuid());
                $values = $subform->getValues();
                $values = $values[$photo->getGuid()];

                if (isset($values['delete']) && $values['delete'] == '1') {
                    $photo->delete();
                } else {
                    $photo->setFromArray($values);
                    $photo->save();
                }
            }

            if (!empty($sitestoreproduct->photo_id)) {
                $photoTable = Engine_Api::_()->getItemTable('sitestoreproduct_photo');
                $order = $photoTable->select()
                        ->from($photoTable->info('name'), array('order'))
                        ->where('product_id = ?', $sitestoreproduct->product_id)
                        ->group('photo_id')
                        ->order('order ASC')
                        ->limit(1)
                        ->query()
                        ->fetchColumn();

                $photoTable->update(array('order' => $order - 1), array('file_id = ?' => $sitestoreproduct->photo_id));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if (empty($change_url)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'product_id' => $album->product_id), "sitestoreproduct_albumspecific", true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'product_id' => $album->product_id), "sitestoreproduct_dashboard", true);
        }
    }

    public function orderAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!$this->_helper->requireSubject('sitestoreproduct_product')->isValid())
            return;

        $subject = Engine_Api::_()->core()->getSubject();

        $order = $this->_getParam('order');
        if (!$order) {
            $this->view->status = false;
            return;
        }

        // Get a list of all photos in this album, by order
        $photoTable = Engine_Api::_()->getItemTable('sitestoreproduct_photo');
        $currentOrder = $photoTable->select()
                ->from($photoTable, 'photo_id')
                ->where('product_id = ?', $subject->getIdentity())
                ->order('order ASC')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN)
        ;

        // Find the starting point?
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
            $photoTable->update(array(
                'order' => $i,
                    ), array(
                'photo_id = ?' => $photo_id,
            ));
        }

        $this->view->status = true;
    }

    public function slideShowEnable() {

        //GET CONTENT TABLE
        $tableContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableContentName = $tableContent->info('name');

        //GET PAGE TABLE
        $tablePage = Engine_Api::_()->getDbtable('pages', 'core');
        $tablePageName = $tablePage->info('name');
        //GET PAGE ID
        $page_id = $tablePage->select()
                ->from($tablePageName, array('page_id'))
                ->where('name = ?', "sitestoreproduct_index_view")
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {
            return false;
        }

        $content_id = $tableContent->select()
                ->from($tableContent->info('name'), array('content_id'))
                ->where('page_id = ?', $page_id)
                ->where('name = ?', 'sitestoreproduct.slideshow-list-photo')
                ->query()
                ->fetchColumn();

        if ($content_id)
            return true;

        $params = $tableContent->select()
                ->from($tableContent->info('name'), array('params'))
                ->where('page_id = ?', $page_id)
                ->where('name = ?', 'sitestoreproduct.editor-reviews-sitestoreproduct')
                ->query()
                ->fetchColumn();
        if ($params) {
            $params = Zend_Json::decode($params);
            if (!isset($params['show_slideshow']) || $params['show_slideshow']) {
                return true;
            }
            return false;
        }
    }

    //ACTION FOR EDIT PHOTO
    public function editphotosMobileAction() {

        //LOGGEND IN USER CAN EDIT PHOTO
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PRODUCT ID AND OBJECT
        $this->view->product_id = $product_id = $this->_getParam('product_id');
        $change_url = $this->_getParam('change_url', 0);
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
            return;

        //IF SITESTOREPRODUCT IS NOT EXIST
        if (empty($sitestoreproduct)) {
            return $this->_forward('notfound', 'error', 'core');
        }

        //SET PRODUCT SUBJECT
        Engine_Api::_()->core()->setSubject($sitestoreproduct);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //AUTHORIZATION CHECK
        $this->view->allowed_upload_video = Engine_Api::_()->sitestoreproduct()->allowVideo($sitestoreproduct, $viewer);
        if (!$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, $viewer, "edit")->isValid()) {
            return;
        }

        //AUTHORIZATION CHECK
        $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($sitestoreproduct, $viewer, "photo");
        if (empty($allowed_upload_photo)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->sitestores_view_menu = 7;

        //PREPARE DATA
        $this->view->album = $album = $sitestoreproduct->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());
        $this->view->count = count($paginator);
        $this->view->slideShowEnanle = $slideShowEnanle = $slideShowEnable = $this->slideShowEnable();

        //MAKE FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Album_Photos();
        $this->view->enableVideoPlugin = $slideShowEnanle ? Engine_Api::_()->sitestoreproduct()->allowVideo($sitestoreproduct, $viewer) : 0;
        if ($this->view->enableVideoPlugin) {
            $form->addElement('Radio', 'video_snapshot_id', array(
                'label' => 'Video Snapshot',
            ));
        }
        foreach ($paginator as $photo) {
            $subform = new Sitestoreproduct_Form_Photo_SubEdit(array('elementsBelongTo' => $photo->getGuid()));
            if (empty($slideShowEnable)) {
                $subform->removeElement('show_slidishow');
            }
            $subform->populate($photo->toArray());
            $form->addSubForm($subform, $photo->getGuid());
            $form->cover->addMultiOption($photo->file_id, $photo->file_id);
            if ($this->view->enableVideoPlugin) {
                $form->video_snapshot_id->addMultiOption($photo->photo_id, $photo->photo_id);
            }
        }

        //CHECK METHOD
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = Engine_Api::_()->getDbTable('albums', 'sitestoreproduct');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $values = $form->getValues();
            if (!empty($values['cover']) && $sitestoreproduct->photo_id != $values['cover']) {

                $album->photo_id = $values['cover'];
                $album->save();

                $sitestoreproduct->photo_id = $values['cover'];
                $sitestoreproduct->save();
                $sitestoreproduct->updateAllCoverPhotos();
            }

            if (!empty($values['video_snapshot_id'])) {
                $sitestoreproduct->video_snapshot_id = $values['video_snapshot_id'];
                $sitestoreproduct->save();
            }

            //PROCESS
            foreach ($paginator as $photo) {

                $subform = $form->getSubForm($photo->getGuid());
                $values = $subform->getValues();
                $values = $values[$photo->getGuid()];

                if (isset($values['delete']) && $values['delete'] == '1') {
                    $photo->delete();
                } else {
                    $photo->setFromArray($values);
                    $photo->save();
                }
            }

            if (!empty($sitestoreproduct->photo_id)) {
                $photoTable = Engine_Api::_()->getItemTable('sitestoreproduct_photo');
                $order = $photoTable->select()
                        ->from($photoTable->info('name'), array('order'))
                        ->where('product_id = ?', $sitestoreproduct->product_id)
                        ->group('photo_id')
                        ->order('order ASC')
                        ->limit(1)
                        ->query()
                        ->fetchColumn();

                $photoTable->update(array('order' => $order - 1), array('file_id = ?' => $sitestoreproduct->photo_id));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if (empty($change_url)) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'editphotos', 'product_id' => $album->product_id), "sitestoreproduct_albumspecific", true);
        } else {
            return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'product_id' => $album->product_id), "sitestoreproduct_dashboard", true);
        }
    }

}
