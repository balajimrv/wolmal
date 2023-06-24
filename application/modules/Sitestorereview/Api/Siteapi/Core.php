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
class Sitestorereview_Api_Siteapi_Core extends Core_Api_Abstract {

    /**
     * Returns create a review form 
     *
     * @param array $widgetSettingsReviews
     * @return array
     */
    public function getReviewCreateForm($widgetSettingsReviews) {
        // Get viewer info
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $getItemPage = $widgetSettingsReviews['item'];
        $sitestorereview_proscons = $widgetSettingsReviews['settingsReview']['sitestorereview_proscons'];
        $sitestorereview_limit_proscons = $widgetSettingsReviews['settingsReview']['sitestorereview_limit_proscons'];
        $sitestorereview_recommend = $widgetSettingsReviews['settingsReview']['sitestorereview_recommend'];

        if ($sitestorereview_proscons) {
            if ($sitestorereview_limit_proscons) {
                $createReview[] = array(
                    'type' => 'Textarea',
                    'name' => 'pros',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Pros'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you like about this Page?"),
                    'hasValidator' => 'true'
                );
            } else {
                $createReview[] = array(
                    'type' => 'Textarea',
                    'name' => 'pros',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Pros'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you like about this Page?"),
                    'hasValidator' => 'true',
                );
            }


            if ($sitestorereview_limit_proscons) {
                $createReview[] = array(
                    'type' => 'Textarea',
                    'name' => 'cons',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Cons'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this Page?"),
                    'hasValidator' => 'true',
                );
            } else {
                $createReview[] = array(
                    'type' => 'Textarea',
                    'name' => 'cons',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Cons'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this Page?"),
                    'hasValidator' => 'true',
                );
            }
        }

        $createReview[] = array(
            'type' => 'Textarea',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('One-line summary'),
        );

        $createReview[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Summary'),
        );

        if ($sitestorereview_recommend) {
            $createReview[] = array(
                'type' => 'Radio',
                'name' => 'recommend',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Recommended'),
                'description' => sprintf(Zend_Registry::get('Zend_Translate')->_("Would you recommend this Page to a friend?")),
                'multiOptions' => array(
                    1 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Yes'),
                    0 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('No')
                ),
            );
        }

        $createReview[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit'),
        );
        return $createReview;
    }

    /*
     * Returns review update form 
     *
     * @return array
     */

    public function getReviewUpdateForm() {

        $updateReview[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Summary'),
        );

        $updateReview[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add your Opinion'),
        );
        return $updateReview;
    }

    /*
     * Returns comments on review form 
     *
     * @return array
     */

    public function getcommentForm($type, $id) {
        $commentform = array();
        $commentform[] = array(
            'type' => "text",
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment'),
        );
        return $commentform;
    }

    /*
     *   Adds photo
     *
     *
     */

    public function setPhoto($photo, $subject, $needToUplode = false, $params = array()) {
        try {

            if ($photo instanceof Zend_Form_Element_File) {
                $file = $photo->getFileName();
            } else if (is_array($photo) && !empty($photo['tmp_name'])) {
                $file = $photo['tmp_name'];
            } else if (is_string($photo) && file_exists($photo)) {
                $file = $photo;
            } else {
                throw new Group_Model_Exception('invalid argument passed to setPhoto');
            }
        } catch (Exception $e) {
            
        }

        $imageName = $photo['name'];
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => 'siteevent_event',
            'parent_id' => $subject->getIdentity()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($path . '/m_' . $imageName)
                ->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(200, 400)
                ->write($path . '/p_' . $imageName)
                ->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(140, 160)
                ->write($path . '/in_' . $imageName)
                ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $imageName)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $imageName, $params);
        $iProfile = $storage->create($path . '/p_' . $imageName, $params);
        $iIconNormal = $storage->create($path . '/in_' . $imageName, $params);
        $iSquare = $storage->create($path . '/is_' . $imageName, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $imageName);
        @unlink($path . '/m_' . $imageName);
        @unlink($path . '/in_' . $imageName);
        @unlink($path . '/is_' . $imageName);

        // Update row
        if (empty($needToUplode)) {
            $subject->modified_date = date('Y-m-d H:i:s');
            $subject->save();
        }

        // Add to album
        $viewer = Engine_Api::_()->user()->getViewer();
        $photoTable = Engine_Api::_()->getItemTable('sitepage_photo');
        if (isset($params['album_id']) && !empty($params['album_id'])) {
            $album = Engine_Api::_()->getItem('sitepage_album', $params['album_id']);
            if (!$album->toArray())
                $album = $subject->getSingletonAlbum();
        } else
            $album = $subject->getSingletonAlbum('');
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'event_id' => $subject->getIdentity(),
            'album_id' => $album->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $album->getIdentity()
        ));
        $photoItem->save();

        return $subject;
    }

    /**
     * Review search form
     * 
     * @return array
     */
    public function getReviewSearchForm() {

        $order = 1;
        $reviewForm = array();
        $reviewForm[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => $this->translate('Search'),
        );

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        if ($viewer_id) {
            $reviewForm[] = array(
                'type' => 'Select',
                'name' => 'show',
                'label' => $this->translate('Show'),
                'multiOptions' => array('' => $this->translate("Everyone's Reviews"),
                    'friends_reviews' => $this->translate("My Friends' Reviews"),
                    'self_reviews' => $this->translate("My Reviews"),
                    'featured' => $this->translate("Featured Reviews")),
            );
        }

        $reviewForm[] = array(
            'type' => 'Select',
            'name' => 'type',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Reviews Written By'),
            'multiOptions' => array('' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'), 'editor' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Editors'), 'user' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Users')),
        );


        $reviewForm[] = array(
            'type' => 'Select',
            'name' => 'order',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'recent' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                'rating_highest' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Highest Rating'),
                'rating_lowest' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Lowest Rating'),
                'helpfull_most' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Helpful'),
                'replay_most' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Reply'),
                'view_most' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed')
            ),
        );
        $reviewForm[] = array(
            'type' => 'Select',
            'name' => 'rating',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Ratings'),
            'multiOptions' => array(
                '' => '',
                '5' => sprintf($this->translate('%1s Star'), 5),
                '4' => sprintf($this->translate('%1s Star'), 4),
                '3' => sprintf($this->translate('%1s Star'), 3),
                '2' => sprintf($this->translate('%1s Star'), 2),
                '1' => sprintf($this->translate('%1s Star'), 1),
            ),
        );

        $reviewForm[] = array(
            'type' => 'Checkbox',
            'name' => 'recommend',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Recommended Reviews'),
        );

//        $reviewForm[] = array(
//            'type' => 'Submit',
//            'name' => 'done',
//            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search'),
//        );

        return $reviewForm;
    }

    /*
     * General string translation function
     *
     */

    private function translate($message) {
        return Engine_Api::_()->getApi('Core', 'siteapi')->translate($message);
    }

}
