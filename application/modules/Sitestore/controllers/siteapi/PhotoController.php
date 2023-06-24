<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_PhotoController extends Siteapi_Controller_Action_Standard {
    /*
     * Initialize subject
     */

    public function init() {
        $viewer = Engine_Api::_()->user()->getViewer();

        $store_id = $this->_getParam('store_id');
        if (!empty($store_id)) {
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
            if (!empty($sitestore))
                Engine_Api::_()->core()->setSubject($sitestore);
        }
    }

    /*
     * View album
     * 
     */

    public function browseAlbumAction() {
        $tempResponse = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        else
            $this->respondWithError('no_record');

        $this->validateRequestMethod();

        $albums = Engine_Api::_()->getDbtable('albums', 'sitestore')->getAlbums($subject);

        $response = array();
        $response['totalItemCount'] = $albums->count();

        if ($albums) {
            foreach ($albums as $row => $album) {
                $data = $album->toArray();
                $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album));
                $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album, true));
                $data['photoCount'] = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotosCount(array('album_id' => $album->getIdentity()));
                $data['owner_title'] = $album->getOwner()->getTitle();
                $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'view');
                $data["allow_to_view"] = empty($isManageAdmin) ? 0 : 1;
                $data['photo_count'] = $data['photoCount'];
                $tempResponse[] = $data;
            }
            $response['response'] = $tempResponse;
        }

        $this->respondWithSuccess($response, true);
    }

    /*
     * Add Create Album
     */
//    public function createAlbumAction()
//    {
//        $tempResponse = array();
//        $viewer = Engine_Api::_()->user()->getViewer();
//
//        // Require user
//        if (!$this->_helper->requireUser()->isValid())
//            $this->respondWithError('unauthorized');
//
//        if (Engine_Api::_()->core()->hasSubject('sitestore_store'))
//            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
//        else
//            $this->respondWithError('no_record');
//        
//    }

    /**
     * Returns the contents of the album (photos)
     * 
     * 
     */
    public function viewAlbumAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $response = array();

        // Get sitepage and album
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitestore_store') {
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        } else {
            $sitestore = $subject = Engine_Api::_()->core()->getSubject()->getParent();
        }

        $album_id = $this->_getParam('album_id', 0);

        if (!$album_id)
            $this->respondWithValidationError("parameter_missing", "album_id missing");

        $album = Engine_Api::_()->getItem('sitestore_album', $album_id);

        // Albums order
        $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorealbum.albumsorder', 1);

        $photoCreate = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'view');

        if (!$isManageAdmin)
            $this->respondWithError('unauthorized');

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit');

        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }
        $response['canEdit'] = $canEdit;
        $response['canUpload'] = $photoCreate ? 1 : 0;
        // if (empty($photoCreate) && empty($canEdit)) {
        //     $this->respondWithError('unauthorized');
        // }
        $photos_per_page = $this->_getParam('itemCount_photo', 100);

        $paramsPhoto = array();
        $paramsPhoto['page_id'] = $subject->getIdentity();
        $paramsPhoto['album_id'] = $album_id;
        $total_photo = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotosCount($paramsPhoto);
        $currentPageNumbers = $this->_getParam('page', 1);

        // Start photos pagination
        $page_vars = Engine_Api::_()->sitepage()->makePage($total_photo, $photos_per_page, $currentPageNumbers);
        $page_array = Array();
        for ($x = 0; $x <= $page_vars[2] - 1; $x++) {
            if ($x + 1 == $page_vars[1]) {
                $link = "1";
            } else {
                $link = "0";
            }
            $page_array[$x] = Array('page' => $x + 1,
                'link' => $link);
        }
        $paramsPhoto['start'] = $photos_per_page;
        $paramsPhoto['end'] = $page_vars[0];

        if (empty($albums_order)) {
            $paramsPhoto['photosorder'] = 'album_id ASC';
        } else {
            $paramsPhoto['photosorder'] = 'album_id DESC';
        }

        $paginators = Engine_Api::_()->getDbtable('photos', 'sitestore')->getPhotos($paramsPhoto);
        $photos = array();
        $paginatorArray = $paginators->toArray();
        if (!empty($paginatorArray)) {
            foreach ($paginators as $photo) {
                $data = $photo->toArray();
                $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo, false));
                $data['menu'] = $this->getPhotoGutterMenu($photo);
                $photos[] = $data;
            }
        } else
            $photos = null;
        $response['totalPhotoCount'] = $total_photo;
        $response['album'] = $album->toArray();
        $response['album'] = array_merge($response['album'], Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album, false));
        $response['album'] = array_merge($response['album'], Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($album, true));
        $response['album']['is_like'] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($album);
        $response['album']['owner_title'] = $album->getOwner()->getTitle();
        $response['albumPhotos'] = $photos;
        $response['gutterMenus'] = $this->_albumGutterMenus();
        $this->respondWithSuccess($response, false);
    }

    public function getPhotoGutterMenu($photo) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $menu = array();

        // Get sitepage and album
        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitestore_store') {
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        } else {
            $sitestore = $subject = Engine_Api::_()->core()->getSubject()->getParent();
        }

        $store_id = $sitestore->getIdentity();

        $album_id = $this->_getParam('album_id', 0);

        $album = Engine_Api::_()->getItem('sitestore_album', $album_id);

        $photo_id = $photo->getIdentity();

        $photoCreate = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit');

        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }

        $menu[] = array(
            'label' => $this->translate("Share"),
            'name' => 'share',
            'url' => 'activity/share',
            'urlParams' => array(
                'type' => 'album_photo',
                'id' => $photo_id,
            ),
        );

        $menu[] = array(
            'label' => $this->translate("Report"),
            'name' => 'report',
            'url' => 'report/create/subject/album_photo_' . $photo_id,
            'urlParams' => array(
                'type' => "album_photo",
                'id' => $photo_id,
            ),
        );

        $menu[] = array(
            'label' => $this->translate("Make Profile Photo"),
            'name' => 'make_profile_photo',
            'url' => 'members/edit/external-photo',
            'urlParams' => array(
                'photo' => 'album_photo_' . $photo_id,
            ),
        );


        if ($canEdit) {
            $menu[] = array(
                'label' => $this->translate("Edit Photo"),
                'name' => 'edit',
                'url' => 'sitestore/photos/edit-photo/' . $store_id,
                'urlParams' => array(
                    'photo_id' => $photo_id,
                ),
            );
            $menu[] = array(
                'label' => $this->translate("Delete Photo"),
                'name' => 'delete',
                'url' => 'sitestore/photos/delete-photo/' . $store_id,
                'urlParams' => array(
                    'photo_id' => $photo_id,
                ),
            );
        }

        return $menu;
    }

    /**
     * Delete album
     *
     * @return array
     */
    public function deleteAlbumAction() {
        // Validate request methods
        $this->validateRequestMethod('DELETE');

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (empty($viewer_id))
            $this->respondWithError('unauthorized');

        $album_id = $this->_getParam("album_id");
        if (!Engine_Api::_()->authorization()->isAllowed('sitestore_album', $viewer, 'delete'))
            $this->respondWithError('unauthorized');

        $album = Engine_Api::_()->getItem('sitestore_album', $album_id);

        if (!$album)
            $this->respondWithError('no_record');

        $db = $album->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $album->delete();
            $db->commit();
            $this->successResponseNoContent('no_content', true);
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    public function albumFeaturedAction() {
        $album_id = $this->_getParam("album_id");
        $album = Engine_Api::_()->getItem('sitestore_album', $album_id);

        if (!$album)
            $this->respondWithError('no_record');

        $album->featured = !$album->featured;
        $album->save();
        $this->successResponseNoContent('no_content', true);
    }

    /*
     * Adding album of the day
     *
     *
     */

    public function addAlbumOfDayAction() {

        // Form generation
        $album_id = $this->_getParam('album_id');

        // Check post
        if ($this->getRequest()->isPost()) {

            // Get form values
            $values = $this->_getAllParams();

            $validators = Engine_Api::_()->getApi('Siteapi_FormValidators', 'sitestore')->albumOfDayValidators();
            $values['validators'] = $validators;
            $validationMessage = $this->isValid($values);

            // Response validation error
            if (!empty($validationMessage) && @is_array($validationMessage)) {
                $this->respondWithValidationError('validation_fail', $validationMessage);
            }

            // Begin transaction
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                // Get item of the day table
                $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitestore');

                // Fetch result for resource_id
                $select = $dayItemTime->select()->where('resource_id = ?', $album_id)->where('resource_type = ?', 'sitestore_album');

                $row = $dayItemTime->fetchRow($select);

                if (empty($row)) {
                    $row = $dayItemTime->createRow();
                    $row->resource_id = $album_id;
                }
                $row->start_date = $values["startdate"];
                $row->end_date = $values["enddate"];
                $row->resource_type = 'sitestore_album';
                $row->save();
                $db->commit();
                $this->successResponseNoContent('no_content', true);
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        } else if ($this->getRequest()->isGet()) {

            $responseform = array();
            $responseform[] = array(
                'type' => 'date',
                'name' => 'startdate',
                'title' => $this->translate("Start Date"),
                'description' => $this->translate(" example : 2016-04-27 "),
                'required' => 'true'
            );
            $responseform[] = array(
                'type' => 'date',
                'name' => 'enddate',
                'title' => $this->translate("End Date"),
                'description' => $this->translate(" example : 2016-04-27 "),
                'required' => 'true'
            );
            $responseform[] = array(
                'type' => "submit",
                'name' => "submit",
            );
            $responseData = array();
            $responseData['form'] = $responseform;
            $this->respondWithSuccess($responseData, false);
        }
    }

    /*
     * Returns photo with detail
     *
     */

    public function viewPhotoAction() {

        // Getting viewer and page and photo
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitestore_store') {
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        } else {
            $sitestore = $subject = Engine_Api::_()->core()->getSubject()->getParent();
        }
        $photo_id = $this->_getParam('photo_id');

        if (!$photo_id)
            $this->respondWithValidationError("parameter_missing", "photo_id missing");

        $photo = Engine_Api::_()->getItem('sitestore_photo', $photo_id);

        if (!$photo)
            $this->respondWithError("no_record");

        // Checking for permissions 
        $photoCreate = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit');

        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }
        $data = array();
        $data = $photo->toArray();
        $data = array_merge($data, Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($photo, false));
        $data['is_like'] = Engine_Api::_()->getApi('Core', 'siteapi')->isLike($photo);
        $data['menu'] = $this->getPhotoGutterMenu($photo);
        $response['totalPhotoCount'] = 1;
        $response['canUpload'] = $photoCreate ? 1 : 0;
        $response['photos'][] = $data;
        $this->respondWithSuccess($response, false);
    }

    /*
     * Edit title and description of a particular photo
     *
     */

    public function editPhotoAction() {

        // Getting viewer and page and photo
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitestore_store') {
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        } else {
            $sitestore = $subject = Engine_Api::_()->core()->getSubject()->getParent();
        }
        $photo_id = $this->_getParam('photo_id');

        if (!$photo_id)
            $this->respondWithValidationError("parameter_missing", "photo_id missing");

        $photo = Engine_Api::_()->getItem('sitestore_photo', $photo_id);

        // Checking for permissions 
        $photoCreate = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit');
        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }

        if (empty($photoCreate) && empty($canEdit)) {
            $this->respondWithError('unauthorized');
        }

        if ($this->getRequest()->isGet()) {

            $editForm = array();
            $editForm[] = array(
                'title' => $this->translate('Title'),
                'name' => 'title',
                'type' => 'text',
            );

            $editForm[] = array(
                'title' => $this->translate('Description'),
                'name' => 'description',
                'type' => 'text',
            );

            $editForm[] = array(
                'type' => 'submit',
                'title' => $this->translate('submit'),
                'name' => 'submit'
            );

            $this->respondWithSuccess($editForm, false);
        } elseif ($this->getRequest()->isPost()) {
            $values = $this->_getAllParams();

            $db = $photo->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                if (isset($values['title']) && !empty($values['title']))
                    $photo->title = $values['title'];

                if (isset($values['description']) && !empty($values['description']))
                    $photo->description = $values['description'];

                $photo->save();
                $db->commit();
                $this->successResponseNoContent('no_content');
            } catch (Exception $e) {
                $db->rollBack();
                $this->respondWithValidationError('internal_server_error', $e->getMessage());
            }
        }
    }

    /*
     * Deletes a photo
     */

    public function deletephotoAction() {

        // Validate request method
        $this->validateRequestMethod('DELETE');

        // Getting viewer and page and photo
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->core()->getSubject()->getType() == 'sitestore_store') {
            $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
        } else {
            $sitestore = $subject = Engine_Api::_()->core()->getSubject()->getParent();
        }
        $photo_id = $this->_getParam('photo_id');

        if (!$photo_id)
            $this->respondWithValidationError("parameter_missing", "photo_id missing");

        $photo = Engine_Api::_()->getItem('sitestore_photo', $photo_id);

        // Checking for permissions 
        $photoCreate = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'spcreate');

        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'edit');
        if (empty($isManageAdmin)) {
            $canEdit = 0;
        } else {
            $canEdit = 1;
        }

        if (empty($photoCreate) && empty($canEdit)) {
            $this->respondWithError('unauthorized');
        }

        $db = $photo->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $photo->delete();
            $db->commit();
            $this->successResponseNoContent('no_content');
        } catch (Exception $e) {
            $db->rollBack();
            $this->respondWithValidationError('internal_server_error', $e->getMessage());
        }
    }

    /*
     * Add photo to album
     *
     */

    public function addphotoAction() {

        $this->validateRequestMethod('POST');

        // Getting viewer and page and photo
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (isset($_FILES) && $this->getRequest()->isPost()) {

            if (empty($viewer_id))
                $this->respondWithError('unauthorized');

            $params = $this->_getAllParams();

            if (Engine_Api::_()->core()->getSubject()->getType() == 'sitestore_store') {
                $sitestore = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');
            } else {
                $sitestore = $subject = Engine_Api::_()->core()->getSubject()->getParent();
            }

            foreach ($_FILES as $value) {
                Engine_Api::_()->getApi('Siteapi_Core', 'sitestore')->setPhoto($value, $sitepage, 1, $params);
            }
            $this->successResponseNoContent('no_content');
        }
    }

    /*
     *   Returns menus of the album
     *
     * @return array
     */

    private function _albumGutterMenus() {
        $album_id = $this->_getParam('album_id', 0);
        $store_id = $this->_getParam('store_id', 0);
        $album = Engine_Api::_()->getItem('sitestore_album', $album_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $gutterMenus = array();


        // Delete an album
        if (Engine_Api::_()->authorization()->isAllowed('sitestore_album', $viewer, 'delete')) {
            $gutterMenus[] = array(
                'title' => $this->translate("Delete Album"),
                'url' => 'sitestore/photos/deletealbum/' . $store_id . '/' . $album_id,
                'name' => 'delete'
            );
        }

        // edit an album
        if (Engine_Api::_()->authorization()->isAllowed('sitestore_album', $viewer, 'edit')) {
            $gutterMenus[] = array(
                'title' => $this->translate("Edit Album"),
                'url' => 'sitestore/photos/editalbum/' . $store_id . '/' . $album_id,
                'name' => 'edit'
            );
        }

        if ($album->featured) {
            $gutterMenus[] = array(
                'title' => $this->translate("Make Album non Featured"),
                'url' => 'sitestore/photos/albumfeatured/' . $store_id . '/' . $album_id,
                'name' => 'unfeatured'
            );
        } else {
            $gutterMenus[] = array(
                'title' => $this->translate("Make Featured"),
                'url' => 'sitestore/photos/albumfeatured/' . $store_id . '/' . $album_id,
                'name' => 'featured'
            );
        }

        $gutterMenus[] = array(
            'title' => $this->translate("Make Album of the Day"),
            'url' => 'sitestore/photos/addalbumofday/' . $store_id . '/' . $album_id,
            'name' => 'albumofday'
        );
        $gutterMenus[] = array(
            'title' => $this->translate("Add Photo"),
            'url' => 'sitestore/photos/addphoto/' . $store_id . '/' . $album_id,
            'name' => 'addphoto'
        );
        $gutterMenus[] = array(
            'title' => $this->translate("View Photo"),
            'url' => 'sitestore/photos/viewphoto/' . $store_id . '/' . $album_id . '/photo_id',
            'name' => 'viewphoto'
        );

        return $gutterMenus;
    }

}
