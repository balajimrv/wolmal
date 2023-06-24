<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DashboardController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_DashboardController extends Core_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {
        if (!$this->_helper->requireAuth()->setAuthParams('sitestore_store', null, "view")->isValid())
            return;

        if (!$this->_helper->requireAuth()->setAuthParams('sitestoreproduct_product', null, "view")->isValid())
            return;
    }

    //ACTION FOR CONTACT INFORMATION
    public function contactAction() {

        //ONLY LOGGED IN USER CAN ADD OVERVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET PRODUCT ID AND OBJECT
        $product_id = $this->_getParam('product_id');

        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

        //GET PRODUCT ID
        $tempSitestoreproductContactDetails = @unserialize(Engine_Api::_()->getApi('settings', 'core')->getSetting('temp.sitestoreproduct.contactdetail', array()));
        if (!($tempSitestoreproductContactDetails)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$sitestoreproduct->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "contact")) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->sitestores_view_menu = 6;

        //SET FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Contactinfo();
        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct');

        //POPULATE FORM
        $row = $tableOtherinfo->getOtherinfo($product_id);
        $value['email'] = $row->email;
        $value['phone'] = $row->phone;
        $value['website'] = $row->website;

        $form->populate($value);

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET FORM VALUES
            $values = $form->getValues();
            if (isset($values['email'])) {
                $email_id = $values['email'];

                //CHECK EMAIL VALIDATION
                $validator = new Zend_Validate_EmailAddress();
                $validator->getHostnameValidator()->setValidateTld(false);
                if (!empty($email_id)) {
                    if (!$validator->isValid($email_id)) {
                        $form->addError(Zend_Registry::get('Zend_Translate')->_('Please enter a valid email address.'));
                        return;
                    } else {
                        $tableOtherinfo->update(array('email' => $email_id), array('product_id = ?' => $product_id));
                    }
                } else {
                    $tableOtherinfo->update(array('email' => $email_id), array('product_id = ?' => $product_id));
                }
            }

            //CHECK PHONE OPTION IS THERE OR NOT
            if (isset($values['phone'])) {
                $tableOtherinfo->update(array('phone' => $values['phone']), array('product_id = ?' => $product_id));
            }

            //CHECK WEBSITE OPTION IS THERE OR NOT
            if (isset($values['website'])) {
                $tableOtherinfo->update(array('website' => $values['website']), array('product_id = ?' => $product_id));
            }

            //SHOW SUCCESS MESSAGE
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
    }

    //ACTION FOR CHANING THE PHOTO
    public function changePhotoAction() {

      
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            return $this->_helper->redirector->gotoRoute(array(
                        'module' => 'sitestoreproduct',
                        'controller' => 'dashboard',
                        'action' => 'change-photo-mobile',
                        'product_id' => $this->_getParam("product_id")
                            ), 'default', true);
        }
      

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PRODUCT ID
        $this->view->product_id = $product_id = $this->_getParam('product_id');

        $viewer = Engine_Api::_()->user()->getViewer();

        //GET PRODUCT ITEM
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

        //IF THERE IS NO SITESTOREPRODUCT.
        if (empty($sitestoreproduct)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->sitestores_view_menu = 5;

        //CAN EDIT OR NOT
        if (!$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, $viewer, "edit")->isValid()) {
            return;
        }

        //AUTHORIZATION CHECK
        $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($sitestoreproduct, $viewer, "photo");
        if (empty($allowed_upload_photo)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET FORM
        $this->view->form = $form = new Sitestoreproduct_Form_ChangePhoto();

        //CHECK FORM VALIDATION
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //CHECK FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //UPLOAD PHOTO
        if ($form->Filedata->getValue() !== null) {
            //GET DB
            $db = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getAdapter();
            $db->beginTransaction();
            //PROCESS
            try {
                //SET PHOTO
                $sitestoreproduct->setPhoto($form->Filedata);
                $db->commit();
            } catch (Engine_Image_Adapter_Exception $e) {
                $db->rollBack();
                $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } else if ($form->getValue('coordinates') !== '') {
            $storage = Engine_Api::_()->storage();
            $iProfile = $storage->get($sitestoreproduct->photo_id, 'thumb.profile');
            $iSquare = $storage->get($sitestoreproduct->photo_id, 'thumb.icon');
            $pName = $iProfile->getStorageService()->temporary($iProfile);
            $iName = dirname($pName) . '/nis_' . basename($pName);
            list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));
            $image = Engine_Image::factory();
            $image->open($pName)
                    ->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
                    ->write($iName)
                    ->destroy();
            $iSquare->store($iName);
            @unlink($iName);
        }

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitestoreproduct, 'sitestoreproduct_change_photo');

        $file_id = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct')->getPhotoId($product_id, $sitestoreproduct->photo_id);

        $photo = Engine_Api::_()->getItem('sitestoreproduct_photo', $file_id);

        if ($action != null) {
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
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

        return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'product_id' => $product_id), "sitestoreproduct_dashboard", true);
    }

    //ACTION FOR REMOVE THE PHOTO
    public function removePhotoAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PRODUCT ID
        $product_id = $this->_getParam('product_id');

        //GET PRODUCT ITEM
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $viewer = Engine_Api::_()->user()->getViewer();

        //CAN EDIT OR NOT
        if (!$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, $viewer, "edit")->isValid()) {
            return;
        }

        //GET FILE ID
        $file_id = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct')->getPhotoId($product_id, $sitestoreproduct->photo_id);

        //DELETE PHOTO
        if (!empty($file_id)) {
            $photo = Engine_Api::_()->getItem('sitestoreproduct_photo', $file_id);
            $photo->delete();
        }

        //SET PHOTO ID TO ZERO
        $sitestoreproduct->photo_id = 0;
        $sitestoreproduct->save();

        return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'product_id' => $product_id), "sitestoreproduct_dashboard", true);
    }

    //ACTION FOR CONTACT INFORMATION
    public function metaDetailAction() {

        //ONLY LOGGED IN USER CAN ADD OVERVIEW
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET PRODUCT ID AND OBJECT
        $product_id = $this->_getParam('product_id');

        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

        if (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.metakeyword', 1))) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!$sitestoreproduct->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        if (!Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "metakeyword")) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->sitestores_view_menu = 10;

        //SET FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Metainfo();

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct');

        //POPULATE FORM
        $value['keywords'] = $tableOtherinfo->getColumnValue($product_id, 'keywords');

        $form->populate($value);

        //CHECK FORM VALIDATION
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //GET FORM VALUES
            $values = $form->getValues();
            $tableOtherinfo->update(array('keywords' => $values['keywords']), array('product_id = ?' => $product_id));

            //SHOW SUCCESS MESSAGE
            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
    }

    public function productHistoryAction() {

        //ONLY LOGGED IN USER CAN VIEW PRODUCT HISTORY
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET PRODUCT ID AND OBJECT
        $product_id = $this->_getParam('product_id');

        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

        if (!$sitestoreproduct->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //SELECTED TAB
        $this->view->sitestores_view_menu = 13;

        $orderProductTable = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct');
        $orderProductTableName = $orderProductTable->info('name');
        $orderAddressTableName = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->info('name');
        $orderTableName = Engine_Api::_()->getDbtable('orders', 'sitestoreproduct')->info('name');
        $userTableName = Engine_Api::_()->getDbtable('users', 'user')->info('name');
        $select = $orderProductTable->select()
                ->setIntegrityCheck(false)
                ->from($orderProductTable)
                ->joinLeft($orderAddressTableName, "$orderProductTableName.order_id = $orderAddressTableName.order_id", array('f_name as billing_fname', 'l_name as billing_lname', 'owner_id as order_owner_id'))
                ->joinLeft($orderTableName, "$orderProductTableName.order_id = $orderTableName.order_id", array('creation_date as order_creation_date', 'store_id', 'order_status'))
                ->joinLeft($userTableName, "$orderAddressTableName.owner_id = $userTableName.user_id", array('displayname', 'username'))
                ->where("$orderProductTableName.product_id = ?", $product_id)
                ->where("$orderTableName.order_status = ?", 5)
                ->group("$orderProductTableName.order_id")
                ->order("$orderProductTableName.order_id DESC");

        $temp_result = $orderProductTable->fetchAll($select);
        $this->view->ordersobj = $temp_result;

        $this->view->currencySymbol = $currencySymbol = Zend_Registry::isRegistered('sitestoreproduct.currency.symbol') ? Zend_Registry::get('sitestoreproduct.currency.symbol') : null;

        if (empty($currencySymbol)) {
            $this->view->currencySymbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
        }
    }

    public function productDocumentAction() {

        //ONLY LOGGED IN USER CAN VIEW PRODUCT DOCUMENT
        if (!$this->_helper->requireUser()->isValid())
            return;

        $allowDocument = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.document.enable', 0);
        if (empty($allowDocument)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET PRODUCT ID AND OBJECT
        $this->view->product_id = $product_id = $this->_getParam('product_id');

        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

        $this->view->canEdit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');

        if (!$sitestoreproduct->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //SELECTED TAB
        $this->view->sitestores_view_menu = 9;

        $params = array();
        $params['product_id'] = $product_id;
        $params['page'] = $this->_getParam('page', 1);
        $params['limit'] = 20;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('documents', 'sitestoreproduct')->getDocumentsPaginator($params);
    }

    public function createDocumentAction() {
        //ONLY LOGGED IN USER CAN CREATE DOCUMENT
        if (!$this->_helper->requireUser()->isValid())
            return;
        $allowDocument = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.document.enable', 0);
        if (empty($allowDocument)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $level_id = $viewer->level_id;

        //GET PRODUCT ID AND OBJECT
        $this->view->product_id = $product_id = $this->_getParam('product_id');
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

        if (!$sitestoreproduct->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //SELECTED TAB
        $this->view->TabActive = "document";

        $this->view->form = $form = new Sitestoreproduct_Form_Document_AddDocument();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        if (!empty($_POST)) {

            $filesize = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'filesize');
            $filesize = 2024;
            $filesize = $filesize * 1024;
            if ($filesize < 0) {
                $filesize = (int) ini_get('upload_max_filesize') * 1024 * 1024;
            }
            if ($_FILES['filename']['size'] > $filesize) {
                $error = $this->view->translate('File size can not be exceed from ') . ($filesize / 1024) . $this->view->translate(' KB for this user level');
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            //FILE EXTENSION SHOULD NOT DIFFER FROM ALLOWED TYPE
            $ext = str_replace(".", "", strrchr($_FILES['filename']['name'], "."));
            if (!in_array($ext, array('pdf', 'txt', 'ps', 'rtf', 'epub', 'odt', 'odp', 'ods', 'odg', 'odf', 'sxw', 'sxc', 'sxi', 'sxd', 'doc', 'ppt', 'pps', 'xls', 'docx', 'pptx', 'ppsx', 'xlsx', 'tif', 'tiff'))) {
                $error = $this->view->translate("Invalid file extension. Allowed extensions are :'pdf', 'txt', 'ps', 'rtf', 'epub', 'odt', 'odp', 'ods', 'odg', 'odf', 'sxw', 'sxc', 'sxi', 'sxd', 'doc', 'ppt', 'pps', 'xls', 'docx', 'pptx', 'ppsx', 'xlsx', 'tif', 'tiff'");
                $error = Zend_Registry::get('Zend_Translate')->_($error);

                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }
        }
        if (!empty($_FILES))
            $uploadedDocument = Engine_Api::_()->getApi('core', 'sitestoreproduct')->createDocument($_FILES['filename'], array('product_id' => $product_id));
        $values = $form->getValues();
        $values['product_id'] = $product_id;
        $values['store_id'] = $sitestoreproduct->store_id;
        $values['file_id'] = $uploadedDocument->getIdentity();
        $approve = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.document.auto', 1);
        $values['approve'] = $approve;
        $privacy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.document.privacy');
        if (!isset($values['privacy']) && empty($privacy))
            $values['privacy'] = 1;


        $documents = Engine_Api::_()->getDbtable('documents', 'sitestoreproduct');
        $db = $documents->getAdapter();
        $db->beginTransaction();
        try {

            $row = $documents->createRow();
            $row->setFromArray($values);
            $row->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }



        return $this->_helper->redirector->gotoRoute(array('action' => 'product-document', 'product_id' => $product_id), "sitestoreproduct_dashboard", true);
    }

    public function productDocumentEnableAction() {
        $document_id = $this->_getParam('doc_id', null);
        $productDocumentItem = Engine_Api::_()->getItem('sitestoreproduct_document', $document_id);

        // CHANGING ACTIVE TO COMPLEMENT OF PRESENT ACTIVE VALUE
        $productDocumentItem->status = !$productDocumentItem->status;
        $productDocumentItem->save();
        $this->view->activeFlag = $productDocumentItem->status;
    }

    public function productDocumentApproveAction() {
        $document_id = $this->_getParam('doc_id', null);

        $productDocumentItem = Engine_Api::_()->getItem('sitestoreproduct_document', $document_id);

        // CHANGING ACTIVE TO COMPLEMENT OF PRESENT ACTIVE VALUE
        $productDocumentItem->approve = !$productDocumentItem->approve;
        $productDocumentItem->save();
        $this->view->activeFlag = $productDocumentItem->approve;
    }

    public function multideleteDocumentsAction() {
        $values = $this->getRequest()->getPost();
        foreach ($values['doc_id'] as $document_id) {
            $productDocumentItem = Engine_Api::_()->getItem('sitestoreproduct_document', $document_id);
            $file_id = $productDocumentItem->file_id;
            $tmpRow = Engine_Api::_()->getItem('storage_file', $file_id);
            if (!empty($tmpRow))
                $tmpRow->remove();

            Engine_Api::_()->getDbtable('documents', 'sitestoreproduct')->delete(array('document_id 	 = ?' => $document_id));
        }
        $this->view->success = 1;
    }

    public function editDocumentAction() {

        //ONLY LOGGED IN USER CAN VIEW PRODUCT HISTORY
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        //GET PRODUCT ID AND OBJECT
        $this->view->product_id = $product_id = $this->_getParam('product_id');
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
        $doc_id = $this->_getParam('doc_id', null);
        if (!$sitestoreproduct->authorization()->isAllowed($viewer, 'edit')) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        //SELECTED TAB
        $this->view->TabActive = "document";
        $this->view->form = $form = new Sitestoreproduct_Form_Document_EditDocument();
        $form->removeElement('filename');

        $value = array();
        $productDocumentItem = Engine_Api::_()->getItem('sitestoreproduct_document', $doc_id);
        $productDocumentItemArray = $productDocumentItem->toArray();
        $value['title'] = $productDocumentItemArray['title'];
        $value['body'] = $productDocumentItemArray['body'];
        $value['status'] = $productDocumentItemArray['status'];
        $value['privacy'] = $productDocumentItemArray['privacy'];
        $form->populate($value);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        $productDocumentItem = Engine_Api::_()->getItem('sitestoreproduct_document', $doc_id);
        $productDocumentItem->title = $values['title'];
        $productDocumentItem->body = $values['body'];
        $productDocumentItem->filename = $values['filename'];
        $productDocumentItem->status = $values['status'];
        $productDocumentItem->privacy = $values['privacy'];
        $productDocumentItem->save();
        return $this->_helper->redirector->gotoRoute(array('action' => 'product-document', 'product_id' => $product_id), "sitestoreproduct_dashboard", true);
    }

    public function deleteDocumentAction() {
        //ONLY LOGGED IN USER 
        if (!$this->_helper->requireUser()->isValid())
            return;

        $this->view->document_id = $doc_id = $this->_getParam('doc_id');


        if ($this->getRequest()->isPost()) {
            $productDocumentItem = Engine_Api::_()->getItem('sitestoreproduct_document', $doc_id);

            //IF TABLE OBJECT NOT EMPTY THEN DELETE ROW
            if (!empty($productDocumentItem)) {
                $file_id = $productDocumentItem->file_id;
                $tmpRow = Engine_Api::_()->getItem('storage_file', $file_id);
                if (!empty($tmpRow))
                    $tmpRow->remove();
//         $documentHrefArray = explode('/public/', $tmpRow->getHref());
//         $path = APPLICATION_PATH . '/public/' . end($documentHrefArray);
//              if (is_file($path)) {
//            @chmod($path, 0777);
//            @unlink($path);
//        }
                $productDocumentItem->delete();
                Engine_Api::_()->getDbtable('documents', 'sitestoreproduct')->delete(array('document_id 	 = ?' => $doc_id));
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Product document deleted successfully.'))
            ));
        }
    }

    public function downloadDocumentAction() {

        $doc_id = $this->_getParam('doc_id', null);
        $file_id = $this->_getParam('file_id', null);
        $tmpRow = Engine_Api::_()->getItem('storage_file', $file_id);
        $documentHrefArray = explode('/public/', $tmpRow->getHref());
        $path = APPLICATION_PATH . '/public/' . end($documentHrefArray);

        if (file_exists($path) && is_file($path)) {
            @chmod($path, 0777);
            // zend's ob
            $isGZIPEnabled = false;
            if (ob_get_level()) {
                $isGZIPEnabled = true;
                @ob_end_clean();
            }

            header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
            header("Content-Transfer-Encoding: Binary", true);
            header("Content-Type: application/x-tar", true);
            header("Content-Type: application/octet-stream", true);
            header("Content-Type: application/download", true);
            header("Content-Description: File Transfer", true);
            if (empty($isGZIPEnabled))
                header("Content-Length: " . filesize($path), true);

            readfile($path);
        }
        exit();
    }

    //ACTION FOR EDIT THE LOCATION
    public function editlocationAction() {

        //GET EVENT ID AND OBJECT
        $this->view->product_id = $product_id = $this->_getParam('product_id');
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

        //IF LOCATION SETTING IS ENABLED
        if (!Engine_Api::_()->sitestoreproduct()->enableLocation()) {
            return $this->_forwardCustom('requireauth', 'error', 'core');
        }

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, $viewer, "edit")->isValid()) {
            return;
        }

        //SELECTED TAB
        $this->view->sitestores_view_menu = 15;

        //GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestoreproduct');

        //MAKE VALUE ARRAY
        $values = array();
        $value['id'] = $sitestoreproduct->product_id;

        //GET LOCATION
        $this->view->location = $location = $locationTable->getLocation($value);

        if (!empty($location)) {

            //MAKE FORM
            $this->view->form = $form = new Sitestoreproduct_Form_Location(array(
                'item' => $sitestoreproduct,
                'location' => $location->location
            ));

            //CHECK POST
            if (!$this->getRequest()->isPost()) {
                $form->populate($location->toarray());
                return;
            }

            //FORM VALIDATION
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }

            //GET FORM VALUES
            $values = $form->getValues();
            unset($values['submit']);
            unset($values['location']);

            //UPDATE LOCATION
            $locationTable->update($values, array('product_id = ?' => $product_id));

            $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved successfully.'));
        }
        $this->view->location = $locationTable->getLocation($value);
    }

    //ACTION FOR EDIT THE EVENT ADDRESS
    public function editaddressAction() {

        //GET EVENT ID AND OBJECT
        $product_id = $this->_getParam('product_id');
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $viewer = Engine_Api::_()->user()->getViewer();


        //IF SITEEVENT IS NOT EXIST
        if (empty($sitestoreproduct)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }

        //MAKE FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Address(array('item' => $sitestoreproduct));
        $this->view->locationId = 0;
        if (empty($sitestoreproduct->location)) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);
            $tableLocation = Engine_Api::_()->getDbTable('locations', 'sitestore');
            $this->view->locationId = $tableLocation->select()->from($tableLocation->info('name'), 'location_id')->where('store_id = ?', $sitestoreproduct->store_id)->where("location = ?", $sitestore->location)->query()->fetchColumn();
            if (!empty($this->view->locationId)) {
                $this->view->locationDetails = Engine_Api::_()->getItem('sitestore_location', $this->view->locationId);
            }
        }

        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            $form->populate($sitestoreproduct->toArray());
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $oldProductLocation = $sitestoreproduct->location;
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            $location = $_POST['location'];
            $sitestoreproduct->location = $location;
            $sitestoreproduct->save();

            //GET LOCATION TABLE
            $locationTable = Engine_Api::_()->getDbtable('locations', 'sitestoreproduct');
            if (!empty($location)) {
                $sitestoreproduct->setLocation();
                $locationTable->update(array('location' => $location), array('product_id = ?' => $product_id));
            } else {
                $locationTable->delete(array('product_id = ?' => $product_id));
            }

            $db->commit();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 500,
                'parentRefresh' => 500,
                'messages' => array('Your product location has been modified successfully.')
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    //ACTION FOR CHANING THE PHOTO
    public function changePhotoMobileAction() {

        //CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        //GET PRODUCT ID
        $this->view->product_id = $product_id = $this->_getParam('product_id');

        $viewer = Engine_Api::_()->user()->getViewer();

        //GET PRODUCT ITEM
        $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
        $this->view->sitestore = $sitestore = Engine_Api::_()->getItem('sitestore_store', $sitestoreproduct->store_id);

        //IF THERE IS NO SITESTOREPRODUCT.
        if (empty($sitestoreproduct)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //SELECTED TAB
        $this->view->sitestores_view_menu = 5;

        //CAN EDIT OR NOT
        if (!$this->_helper->requireAuth()->setAuthParams($sitestoreproduct, $viewer, "edit")->isValid()) {
            return;
        }

        //AUTHORIZATION CHECK
        $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($sitestoreproduct, $viewer, "photo");
        if (empty($allowed_upload_photo)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        //GET FORM
        $this->view->form = $form = new Sitestoreproduct_Form_Mobile_ChangePhoto();

        //CHECK FORM VALIDATION
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //CHECK FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //UPLOAD PHOTO
        if ($form->Filedata->getValue() !== null) {
            //GET DB
            $db = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getAdapter();
            $db->beginTransaction();
            //PROCESS
            try {
                //SET PHOTO
                $sitestoreproduct->setPhoto($form->Filedata);
                $db->commit();
            } catch (Engine_Image_Adapter_Exception $e) {
                $db->rollBack();
                $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } else if ($form->getValue('coordinates') !== '') {
            $storage = Engine_Api::_()->storage();
            $iProfile = $storage->get($sitestoreproduct->photo_id, 'thumb.profile');
            $iSquare = $storage->get($sitestoreproduct->photo_id, 'thumb.icon');
            $pName = $iProfile->getStorageService()->temporary($iProfile);
            $iName = dirname($pName) . '/nis_' . basename($pName);
            list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));
            $image = Engine_Image::factory();
            $image->open($pName)
                    ->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
                    ->write($iName)
                    ->destroy();
            $iSquare->store($iName);
            @unlink($iName);
        }

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitestoreproduct, 'sitestoreproduct_change_photo');

        $file_id = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct')->getPhotoId($product_id, $sitestoreproduct->photo_id);

        $photo = Engine_Api::_()->getItem('sitestoreproduct_photo', $file_id);

        if ($action != null) {
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
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

        return $this->_helper->redirector->gotoRoute(array('action' => 'change-photo', 'product_id' => $product_id), "sitestoreproduct_dashboard", true);
    }

}
