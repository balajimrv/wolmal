<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteusercoverphoto_Api_Siteapi_Core extends User_Api_Core {
    /*
     * Get the user profile cover photo
     * 
     * @param object user
     * @return string
     */

    public function getCoverPhoto($user) {
        $host = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
//        $baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
//        $baseParentUrl = @trim($baseParentUrl, "/");

        if (isset($user->user_cover) && !empty($user->user_cover)) {
            if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                $photo = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
            } else {
                $photo = Engine_Api::_()->getItem('album_photo', $user->user_cover);
            }
            if (isset($photo) && !empty($photo))
                return $host . $photo->getPhotoUrl();
        }

        // return default image if cover photo not set
        if (!isset($photo) || empty($photo)) {
            $id = $user->level_id;
            return $host . Engine_Api::_()->storage()->get(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$id.id"), 'thumb.cover')->map();
        }


        return;
    }

    /*
     * Get coverphoto menus
     * 
     * @param object user
     * @return array
     */

    public function getCoverPhotoMenu($user) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
        $canEdit = ($can_edit && Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $user, 'upload')) ? true : false;
        if (empty($canEdit))
            return;

        $coverMenu[] = array(
            'label' => $this->_translate('Upload Cover Photo'),
            'name' => 'upload_cover_photo',
            'url' => 'user/profilepage/upload-cover-photo/user_id/' . $user->getIdentity(),
            'urlParams' => array(
            )
        );

        $coverMenu[] = array(
            'label' => $this->_translate('Choose from Albums'),
            'name' => 'choose_from_album',
            'urlParams' => array(
            )
        );

        if (isset($user->user_cover) && !empty($user->user_cover)) {
            if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
                $photo = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
            else
                $photo = Engine_Api::_()->getItem('album_photo', $user->user_cover);

            $host = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();

            if (isset($photo) && !empty($photo))
                $photoUrl = $host . $photo->getPhotoUrl();
            else {
                $id = $user->level_id;
                $photoUrl = $host . Engine_Api::_()->storage()->get(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$id.id"), 'thumb.cover')->map();
            }
            $coverMenu[] = array(
                'label' => $this->_translate('View Cover Photo'),
                'name' => 'view_cover_photo',
                'url' => $photoUrl,
                'urlParams' => array(
                )
            );

            $coverMenu[] = array(
                'label' => $this->_translate('Remove Cover Photo'),
                'name' => 'remove_cover_photo',
                'url' => 'user/profilepage/remove-cover-photo/user_id/' . $user->getIdentity(),
                'urlParams' => array(
                )
            );
        }

        return $coverMenu;
    }

    /*
     * Get mainphoto menus
     * 
     * @param object user
     * @return array
     */

    public function getMainPhotoMenu($user) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $canEdit = $user->authorization()->isAllowed($viewer, 'edit');
        if (empty($canEdit))
            return;

        $mainPhotoMenu[] = array(
            'label' => $this->_translate('Upload Photo'),
            'name' => 'upload_photo',
            'url' => 'user/profilepage/upload-cover-photo/user_id/' . $user->getIdentity(),
            'urlParams' => array(
            )
        );

        $mainPhotoMenu[] = array(
            'label' => $this->_translate('Choose from Albums'),
            'name' => 'choose_from_album',
            'urlParams' => array(
            )
        );

        if (isset($user->photo_id) && !empty($user->photo_id)) {
            $host = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
            $mainPhotoMenu[] = array(
                'label' => $this->_translate('View Profile Photo'),
                'name' => 'view_profile_photo',
                'url' => $host . $user->getPhotoUrl(),
                'urlParams' => array(
                )
            );

            $mainPhotoMenu[] = array(
                'label' => $this->_translate('Remove'),
                'name' => 'remove_photo',
                'url' => 'user/profilepage/remove-cover-photo/user_id/' . $user->getIdentity(),
                'urlParams' => array(
                )
            );
        }

        return $mainPhotoMenu;
    }

    /*
     * Translte the language
     * 
     * @param string str
     * @return string or array
     */

    protected function _translate($str) {
        return Engine_Api::_()->getApi('Core', 'siteapi')->translate($str);
    }

}
