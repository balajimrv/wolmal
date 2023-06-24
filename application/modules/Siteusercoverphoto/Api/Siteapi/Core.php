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
        try {
            if (isset($user->user_cover) && !empty($user->user_cover)) {
                if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {
                    $photo = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
                } else {
                    $photo = Engine_Api::_()->getItem('album_photo', $user->user_cover);
                }

                if (!empty($photo)) {
                    $getPhotoURL = $photo->getPhotoUrl();

                    $finalPhotoURL = (strstr($getPhotoURL, 'http')) ? $getPhotoURL : $host . $getPhotoURL;
                    return $finalPhotoURL;
                }
            }
        } catch (Exception $ex) {
            // Blank Exception
        }

        if (isset($user->level_id) && !empty($user->level_id)) {
            if (Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user->level_id.id")) {
                $getPhotoURL = Engine_Api::_()->storage()->get(Engine_Api::_()->getApi("settings", "core")->getSetting("siteusercoverphoto.cover.photo.preview.level.$user->level_id.id"), 'thumb.cover')->map();
                $finalPhotoURL = (strstr($getPhotoURL, 'http') || strstr($getPhotoURL, 'https')) ? $getPhotoURL : $host . $getPhotoURL;
                return $finalPhotoURL;
            }
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
        $viewer_id = $viewer->getIdentity();
        $can_edit = $user->authorization()->isAllowed($viewer, 'edit');
        $canEdit = ($can_edit && Engine_Api::_()->authorization()->isAllowed('siteusercoverphoto', $user, 'upload')) ? true : false;
        if (empty($canEdit))
            return;

        if ($viewer->getIdentity() != $user->getIdentity())
            return;

        if ($user->user_id == $viewer_id) {
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
        }

        if (isset($user->user_cover) && !empty($user->user_cover)) {
            if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
                $photo = Engine_Api::_()->getItem('advalbum_photo', $user->user_cover);
            else
                $photo = Engine_Api::_()->getItem('album_photo', $user->user_cover);

            if (isset($photo) && !empty($photo)) {
                $tempArray['label'] = $this->_translate('View Cover Photo');
                $tempArray['name'] = 'view_cover_photo';
                $tempArray['urlParams'] = array();
                $getPhotoURL = $photo->getPhotoUrl();
                $finalPhotoURL = (strstr($getPhotoURL, 'http')) ? $getPhotoURL : Engine_Api::_()->getApi('Core', 'siteapi')->getHost() . $getPhotoURL;
                $tempArray['url'] = $finalPhotoURL;

                $coverMenu[] = $tempArray;

                if ($user->user_id == $viewer_id) {
                    $coverMenu[] = array(
                        'label' => $this->_translate('Remove Cover Photo'),
                        'name' => 'remove_cover_photo',
                        'url' => 'user/profilepage/remove-cover-photo/user_id/' . $user->getIdentity(),
                        'urlParams' => array(
                        )
                    );
                }
            }
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

        if ($viewer->getIdentity() != $user->getIdentity())
            return;

        $mainPhotoMenu[] = array(
            'label' => $this->_translate('Upload Photo'),
            'name' => 'upload_photo',
            'url' => 'user/profilepage/upload-cover-photo/user_id/' . $user->getIdentity() . '/special/profile',
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
            $getPhotoURL = $user->getPhotoUrl();
            $finalPhotoURL = (strstr($getPhotoURL, 'http')) ? $getPhotoURL : $host . $getPhotoURL;
            $tempInfo = array(
                'label' => $this->_translate('View Profile Photo'),
                'name' => 'view_profile_photo',
                'url' => $finalPhotoURL,
                'urlParams' => array(
                )
            );

            $mainPhotoMenu[] = $tempInfo;

            $mainPhotoMenu[] = array(
                'label' => $this->_translate('Remove'),
                'name' => 'remove_photo',
                'url' => 'user/profilepage/remove-cover-photo/user_id/' . $user->getIdentity() . '/special/profile',
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
