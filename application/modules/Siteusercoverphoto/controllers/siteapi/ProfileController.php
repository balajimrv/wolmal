<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    ProfileController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_ProfileController extends Siteapi_Controller_Action_Standard {
    /*
     * Upload the cover photo OR profile phot of the user
     */

    public function uploadCoverPhotoAction() {
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $viewer = Engine_Api::_()->user()->getViewer();
        $cover_photo_preview = $level_id = 0;
        $user_id = $this->getRequestParam('user_id');
        $special = $this->getRequestParam('special', 'cover');
        $user = Engine_Api::_()->getItem('user', $user_id);
        Engine_Api::_()->getApi('Core', 'siteapi')->setView();
        Engine_Api::_()->getApi('Core', 'siteapi')->setLocal();

        Zend_Registry::get('Zend_Translate')->_('The settings for the Advanced Lightbox Viewer have been moved to the SocialEngineAddOns Core Plugin. Please %1svisit here%2s to see and configure these settings.');
        
        // Set the translations for zend library.
//        if (!Zend_Registry::isRegistered('Zend_Translate'))
//            Engine_Api::_()->getApi('Core', 'siteapi')->setTranslate();

        if ($viewer->getIdentity() && $viewer->level_id == 1 && $user->getOwner()->isSelf($viewer)) {
            $cover_photo_preview = $this->getRequestParam("cover_photo_preview", 0);
            $level_id = $this->getRequestParam("level_id", 0);
        }

        if ($special == 'cover') {
            if (!$cover_photo_preview) {
                $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
                if ($can_edit && Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $user, 'upload')) {
                    $can_edit = 1;
                } else {
                    $can_edit = 0;
                }

                if (!$can_edit) {
                    $this->respondWithError('unauthorized');
                }
            }
        }

        if (empty($cover_photo_preview)) {
            $file = '';
            $notNeedToCreate = false;
            $photo_id = $this->getRequestParam('photo_id');
            if ($photo_id) {
                if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                    $photo = Engine_Api::_()->getItem('advalbum_photo', $photo_id);
                    $album = Engine_Api::_()->getItem('advalbum_album', $photo->album_id);
                } else {
                    $photo = Engine_Api::_()->getItem('album_photo', $photo_id);
                    $album = Engine_Api::_()->getItem('album', $photo->album_id);
                }

                if ($album && ($album->type == 'cover' || $album->type == 'profile')) {
                    $notNeedToCreate = true;
                }
                if ($photo->file_id && !$notNeedToCreate)
                    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo->file_id);
            }

            if (empty($photo_id) || empty($photo)) {
                if (!$this->getRequest()->isPost()) {
                    $this->respondWithError('unauthorized');
                }
            }

            // Upload Photo
            if ($_FILES['photo'] !== null || $photo || ($notNeedToCreate && $file)) {
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    // Create Photo
                    if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                        $tablePhoto = Engine_Api::_()->getDbtable('photos', 'advalbum');
                    } else {
                        $tablePhoto = Engine_Api::_()->getDbtable('photos', 'album');
                    }

                    if (!$notNeedToCreate) {
                        $photo = $tablePhoto->createRow();
                        $photo->setFromArray(array(
                            'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                            'owner_type' => 'user'
                        ));
                        $photo->save();

                        if ($file) {
                            if ($special == 'cover') {
                                $this->_setCoverPhoto($file, $photo, $cover_photo_preview);
                            } else {
                                $this->_setMainPhoto($file, $photo);
                            }
                        } else {
                            if ($special == 'cover') {
                                $this->_setCoverPhoto($_FILES['photo'], $photo, $cover_photo_preview);
                            } else {
                                $this->_setMainPhoto($_FILES['photo'], $photo);
                            }
                        }

                        if ($special == 'cover') {
                            $tableAlbum = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto');
                            $album = $tableAlbum->getSpecialAlbumCover($user, $special);
                        } else {
                            if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                                $tableAlbum = Engine_Api::_()->getDbtable('albums', 'advalbum');
                            } else {
                                $tableAlbum = Engine_Api::_()->getDbtable('albums', 'album');
                            }
                            $album = $tableAlbum->getSpecialAlbum($user, 'profile');
                        }
                        $photo->album_id = $album->album_id;
                        $photo->save();
                    }

                    $album->cover_params = Zend_Json_Encoder::encode($this->getRequestParam('position', array('top' => '0', 'left' => 0)));
                    $album->save();
                    if (!$album->photo_id) {
                        $album->photo_id = $photo->photo_id;
                        $album->save();
                    }
                    if ($special == 'cover') {
                        $user->user_cover = $photo->photo_id;
                    } else {
                        $user->photo_id = $photo->file_id;
                    }

                    $user->save();

// Add activity
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                    if ($special == 'cover') {
                        $action = $activityApi->addActivity($viewer, $photo, 'user_cover_update');
                        if ($action) {
                            if ($photo)
                                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                        }
                    }
                    else {
                        $iMain = Engine_Api::_()->getItem('storage_file', $user->photo_id);

// Insert activity
                        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update', '{item:$subject} added a new profile photo.');

// Hooks to enable album to work
                        if ($action)
                            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $photo);
                    }

                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                }
            }
        } else {
//CHECK FORM VALIDATION
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }
            if ($form->Filedata->getValue() !== null) {
                $values = $form->getValues();
                $siteusercoverphoto_setdefaultcoverphoto = $values['siteusercoverphoto_setdefaultcoverphoto'];
                $this->_setCoverPhoto($form->Filedata, null, $cover_photo_preview, $level_id, $siteusercoverphoto_setdefaultcoverphoto);
                $this->view->status = true;
                $this->view->siteusercoverphoto_setdefaultcoverphoto = $siteusercoverphoto_setdefaultcoverphoto;
            }
        }

        $this->successResponseNoContent('no_content');
    }

    /*
     * Remove the cover photo OR profile photo of the user.
     */

    public function removeCoverPhotoAction() {

//CHECK USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            $this->respondWithError('unauthorized');

        $cover_photo_preview = 0;
        $level_id = 0;
        $special = $this->getRequestParam('special', 'cover');
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $this->getRequestParam('user_id');
        $user = Engine_Api::_()->getItem('user', $user_id);
        if ($viewer->getIdentity() && $viewer->level_id == 1 && $user->getOwner()->isSelf($viewer)) {
            $cover_photo_preview = $this->getRequestParam("cover_photo_preview", 0);
            $level_id = $this->getRequestParam("level_id", 0);
        }

        if ($special == 'cover' && empty($cover_photo_preview)) {
            $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
            if ($can_edit && Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $user, 'upload')) {
                $can_edit = 1;
            } else {
                $can_edit = 0;
            }
            if (!$can_edit) {
                $this->respondWithError('unauthorized');
            }
        }

        $coreSettingsApi = Engine_Api::_()->getApi("settings", "core");
        $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
        $preview_id = $coreSettingsApi->getSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id");
        $count = 0;
        foreach ($level_ids as $key => $value) {
            $public_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
            if ($public_level_id == $key)
                continue;
            if ($coreSettingsApi->getSetting("siteusercoverphoto.cover.photo.preview.level.$key.id") == $preview_id) {
                $count++;
            }
        }

        if ($this->getRequest()->isPost()) {
            if ($special == 'cover') {
                if (empty($cover_photo_preview)) {
                    $user->user_cover = 0;
                    $tableAlbum = Engine_Api::_()->getDbtable('albums', 'siteusercoverphoto');
                    $album = $tableAlbum->getSpecialAlbumCover($user, $special);
                    $album->cover_params = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
                    $album->save();
                } else {
                    if ($count > 1) {
                        $siteusercoverphoto_removedefaultcoverphoto = $_POST['siteusercoverphoto_removedefaultcoverphoto'];
                        if ($siteusercoverphoto_removedefaultcoverphoto) {
                            $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
                            foreach ($level_ids as $key => $value) {
                                $public_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
                                if ($public_level_id == $key)
                                    continue;
                                $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$key.id", 0);
                                $postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
                                $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$key.params", $postionParams);
                            }
                            $file = Engine_Api::_()->getItem('storage_file', $preview_id);
                            if ($file)
                                $file->delete();
                        } else {
                            $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id", 0);
                            $postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
                            $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.params", $postionParams);
                        }
                    } else {
                        $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id", 0);
                        $postionParams = Zend_Json_Encoder::encode(array('top' => '0', 'left' => 0));
                        $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.params", $postionParams);
                        $file = Engine_Api::_()->getItem('storage_file', $preview_id);
                        if ($file)
                            $file->delete();
                    }
                }
            } else {
                $user->photo_id = 0;
            }
            $user->save();

            $this->successResponseNoContent('no_content');
        }
    }

    /**
     * Set a photo
     *
     * @param array photo
     * @param object photoObject
     * @param int cover_photo_preview
     * @param int level_id
     * @param int siteusercoverphoto_setdefault
     * @return photo object
     */
    private function _setCoverPhoto($photo, $photoObject, $cover_photo_preview, $level_id = null, $siteusercoverphoto_setdefault = 0) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        if (!$fileName) {
            $fileName = $file;
        }

        $name = basename($file);
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $mainHeight = $coreSettings->getSetting('main.photo.height', 1600);
        $mainWidth = $coreSettings->getSetting('main.photo.width', 1600);

// Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();

// Add autorotation for uploded images. It will work only for SocialEngine-4.8.9 Or more then.
        $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
        if (!empty($hasVersion)) {
            $image->open($file)
                    ->resize($mainWidth, $mainHeight)
                    ->write($mainPath)
                    ->destroy();
        } else {
            $image->open($file)
                    ->autoRotate()
                    ->resize($mainWidth, $mainHeight)
                    ->write($mainPath)
                    ->destroy();
        }

        $normalHeight = $coreSettings->getSetting('normal.photo.height', 375);
        $normalWidth = $coreSettings->getSetting('normal.photo.width', 375);
// Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

        $image = Engine_Image::factory();
        if (!empty($hasVersion)) {
            $image->open($file)
                    ->resize($normalWidth, $normalHeight)
                    ->write($normalPath)
                    ->destroy();
        } else {
            $image->open($file)
                    ->autoRotate()
                    ->resize($normalWidth, $normalHeight)
                    ->write($normalPath)
                    ->destroy();
        }

        $coverPath = $path . DIRECTORY_SEPARATOR . $base . '_c.' . $extension;
        $image = Engine_Image::factory();
        if (!empty($hasVersion)) {
            $image->open($file)
                    ->resize(1500, 1500)
                    ->write($coverPath)
                    ->destroy();
        } else {
            $image->open($file)
                    ->autoRotate()
                    ->resize(1500, 1500)
                    ->write($coverPath)
                    ->destroy();
        }

        if (empty($cover_photo_preview)) {
            $params = array(
                'parent_type' => $photoObject->getType(),
                'parent_id' => $photoObject->getIdentity(),
                'user_id' => $photoObject->owner_id,
                'name' => basename($fileName),
            );

            try {
                $iMain = $filesTable->createFile($mainPath, $params);
                $iIconNormal = $filesTable->createFile($normalPath, $params);
                $iMain->bridge($iIconNormal, 'thumb.normal');
                $iCover = $filesTable->createFile($coverPath, $params);
                $iMain->bridge($iCover, 'thumb.cover');
            } catch (Exception $e) {
                @unlink($mainPath);
                @unlink($normalPath);
                @unlink($coverPath);
            }
            @unlink($mainPath);
            @unlink($normalPath);
            @unlink($coverPath);
            $photoObject->modified_date = date('Y-m-d H:i:s');
            $photoObject->file_id = $iMain->file_id;
            $photoObject->save();
            if (!empty($tmpRow)) {
                $tmpRow->delete();
            }

            return $photoObject;
        } else {
            try {
                $iMain = $filesTable->createSystemFile($mainPath);
                $iIconNormal = $filesTable->createSystemFile($normalPath);
                $iMain->bridge($iIconNormal, 'thumb.normal');
                $iCover = $filesTable->createSystemFile($coverPath);
                $iMain->bridge($iCover, 'thumb.cover');
            } catch (Exception $e) {
                @unlink($mainPath);
                @unlink($normalPath);
                @unlink($coverPath);
                if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
                    throw new Album_Model_Exception($e->getMessage(), $e->getCode());
                } else {
                    throw $e;
                }
            }
            $coreSettingsApi = Engine_Api::_()->getApi("settings", "core");
            if ($siteusercoverphoto_setdefault) {
                $level_ids = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
                foreach ($level_ids as $key => $value) {
                    $public_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
                    if ($public_level_id == $key)
                        continue;
                    $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$key.id", $iMain->file_id);
                }
            } else {
                $coreSettingsApi->setSetting("siteusercoverphoto.cover.photo.preview.level.$level_id.id", $iMain->file_id);
            }
        }

        return;
    }

    /**
     * Set a photo
     *
     * @param array photo
     * @param object photoObject
     * @return photo object
     */
    private function _setMainPhoto($photo, $photoObject) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        if (!$fileName) {
            $fileName = $file;
        }

        $name = basename($file);
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => $photoObject->getType(),
            'parent_id' => $photoObject->getIdentity(),
            'user_id' => $photoObject->owner_id,
            'name' => basename($fileName),
        );

// Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $mainHeight = $coreSettings->getSetting('main.photo.height', 1600);
        $mainWidth = $coreSettings->getSetting('main.photo.width', 1600);
        $hasVersion = Engine_Api::_()->seaocore()->usingLessVersion('core', '4.8.9');
// Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        if (!empty($hasVersion)) {
            $image->open($file)
                    ->resize($mainWidth, $mainHeight)
                    ->write($mainPath)
                    ->destroy();
        } else {
            $image->open($file)
                    ->autoRotate()
                    ->resize($mainWidth, $mainHeight)
                    ->write($mainPath)
                    ->destroy();
        }

        $normalHeight = $coreSettings->getSetting('normal.photo.height', 375);
        $normalWidth = $coreSettings->getSetting('normal.photo.width', 375);
// Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;

        $image = Engine_Image::factory();
        if (!empty($hasVersion)) {
            $image->open($file)
                    ->resize($normalWidth, $normalHeight)
                    ->write($normalPath)
                    ->destroy();
        } else {
            $image->open($file)
                    ->autoRotate()
                    ->resize($normalWidth, $normalHeight)
                    ->write($normalPath)
                    ->destroy();
        }

        $normalLargeHeight = $coreSettings->getSetting('normallarge.photo.height', 720);
        $normalLargeWidth = $coreSettings->getSetting('normallarge.photo.width', 720);
// Resize image (normal)
        $normalLargePath = $path . DIRECTORY_SEPARATOR . $base . '_inl.' . $extension;

        $image = Engine_Image::factory();
        if (!empty($hasVersion)) {
            $image->open($file)
                    ->resize($normalLargeWidth, $normalLargeHeight)
                    ->write($normalLargePath)
                    ->destroy();
        } else {
            $image->open($file)
                    ->autoRotate()
                    ->resize($normalLargeWidth, $normalLargeHeight)
                    ->write($normalLargePath)
                    ->destroy();
        }
// Resize image (icon)
        $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($squarePath)
                ->destroy();
// Store
        try {
            $iMain = $filesTable->createFile($mainPath, $params);
            $iIconNormal = $filesTable->createFile($normalPath, $params);
            $iMain->bridge($iIconNormal, 'thumb.normal');
            $iIconNormalLarge = $filesTable->createFile($normalLargePath, $params);
            $iMain->bridge($iIconNormalLarge, 'thumb.large');
            $iSquare = $filesTable->createFile($squarePath, $params);
            $iMain->bridge($iSquare, 'thumb.icon');
        } catch (Exception $e) {
// Remove temp files
            @unlink($mainPath);
            @unlink($normalPath);
            @unlink($normalLargePath);
            @unlink($squarePath);
// Throw
            if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
                throw new Album_Model_Exception($e->getMessage(), $e->getCode());
            } else {
                throw $e;
            }
        }

        $photoObject->modified_date = date('Y-m-d H:i:s');
        $photoObject->file_id = $iMain->file_id;
        $photoObject->save();
        if (!empty($tmpRow)) {
            $tmpRow->delete();
        }
        return $photoObject;
    }

}
