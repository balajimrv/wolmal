<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Api_Core extends Core_Api_Abstract {

    const IMAGE_WIDTH = 1600;
    const IMAGE_HEIGHT = 1600;
    const THUMB_WIDTH = 140;
    const THUMB_HEIGHT = 160;
    const THUMB_LARGE_WIDTH = 250;
    const THUMB_LARGE_HEIGHT = 250;

    // handle document upload
    public function createDocument($file, $tempParams = array()) {
        if (is_array($file)) {
            if (!is_uploaded_file($file['tmp_name'])) {

                throw new Engine_Exception('Invalid upload or file too large');
            }
            $filename = $file['name'];
        } else if (is_string($file)) {
            $filename = $file;
        } else {
            throw new Engine_Exception('Invalid upload or file too large');
        }
        // upload to storage system
        $params = array_merge(array(
            'type' => 'document',
            'name' => $filename,
            'parent_type' => 'sitestoreproduct_document',
            'parent_id' => $tempParams['product_id'],
            'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
            'extension' => substr($filename, strrpos($filename, '.') + 1),
        ));

        $document = Engine_Api::_()->storage()->create($file, $params);

        return $document;
    }

    public function createPhoto($params, $file) {

        if ($file instanceof Storage_Model_File) {
            $params['file_id'] = $file->getIdentity();
        } else {
            // CTSTYLE-46
            Engine_Api::_()->sitestoreproduct()->checkPhotoOrientation($file['tmp_name']);
            //GET IMAGE INFO AND RESIZE
            $name = basename($file['tmp_name']);
            $path = dirname($file['tmp_name']);
            $extension = ltrim(strrchr($file['name'], '.'), '.');

            $mainName = $path . '/m_' . $name . '.' . $extension;
            $thumbName = $path . '/t_' . $name . '.' . $extension;
            $thumbLargeName = $path . '/t_l_' . $name . '.' . $extension;

            $image = Engine_Image::factory();
            $image->open($file['tmp_name'])
                    ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
                    ->write($mainName)
                    ->destroy();

            $image = Engine_Image::factory();
            $image->open($file['tmp_name'])
                    ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
                    ->write($thumbName)
                    ->destroy();
            $image = Engine_Image::factory();
            $image->open($file['tmp_name'])
                    ->resize(self::THUMB_LARGE_WIDTH, self::THUMB_LARGE_HEIGHT)
                    ->write($thumbLargeName)
                    ->destroy();

            //RESIZE IMAGE (ICON)
            $iSquarePath = $path . '/is_' . $name . '.' . $extension;
            $image = Engine_Image::factory();
            $image->open($file['tmp_name']);

            $size = min($image->height, $image->width);
            $x = ($image->width - $size) / 2;
            $y = ($image->height - $size) / 2;

            $image->resample($x, $y, $size, $size, 48, 48)
                    ->write($iSquarePath)
                    ->destroy();

            //STORE PHOTO
            $photo_params = array(
                'parent_id' => $params['product_id'],
                'parent_type' => 'sitestoreproduct_product',
            );

            $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
            $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
            $photoFile->bridge($thumbFile, 'thumb.normal');

            $thumbLargeFile = Engine_Api::_()->storage()->create($thumbLargeName, $photo_params);
            $photoFile->bridge($thumbLargeFile, 'thumb.large');

            $iSquare = Engine_Api::_()->storage()->create($iSquarePath, $photo_params);
            $photoFile->bridge($iSquare, 'thumb.icon');
            $params['file_id'] = $photoFile->file_id;
            $params['photo_id'] = $photoFile->file_id;

            //REMOVE TEMP FILES
            @unlink($mainName);
            @unlink($thumbName);
            @unlink($thumbLargeName);
            @unlink($iSquarePath);
        }

        $row = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct')->createRow();
        $row->setFromArray($params);
        $row->save();

        return $row;
    }

    // CTSTYLE-26
    public function createMobilePhoto($params, $photo) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Engine_Exception('invalid argument passed to setPhoto');
        }
        // CTSTYLE-46
        Engine_Api::_()->sitestoreproduct()->checkPhotoOrientation($file);

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $mainName = $path . '/m_' . $name;
        $thumbName = $path . '/t_' . $name;
        $thumbLargeName = $path . '/t_l_' . $name;

        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
                ->write($mainName)
                ->destroy();

        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
                ->write($thumbName)
                ->destroy();
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(self::THUMB_LARGE_WIDTH, self::THUMB_LARGE_HEIGHT)
                ->write($thumbLargeName)
                ->destroy();

        //RESIZE IMAGE (ICON)
        $iSquarePath = $path . '/is_' . $name;
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($iSquarePath)
                ->destroy();

        //STORE PHOTO
        $photo_params = array(
            'parent_id' => $params['product_id'],
            'parent_type' => 'sitestoreproduct_product',
        );

        $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
        $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
        $photoFile->bridge($thumbFile, 'thumb.normal');

        $thumbLargeFile = Engine_Api::_()->storage()->create($thumbLargeName, $photo_params);
        $photoFile->bridge($thumbLargeFile, 'thumb.large');

        $iSquare = Engine_Api::_()->storage()->create($iSquarePath, $photo_params);
        $photoFile->bridge($iSquare, 'thumb.icon');
        $params['file_id'] = $photoFile->file_id;
        $params['photo_id'] = $photoFile->file_id;

        //REMOVE TEMP FILES
        @unlink($mainName);
        @unlink($thumbName);
        @unlink($thumbLargeName);
        @unlink($iSquarePath);

        $row = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct')->createRow();
        $row->setFromArray($params);
        $row->save();

        return $row;
    }

    //FUNCTION FOR SHOWING 'LIKED LINK'
    public function check_availability($resourceType, $resourceId) {

        $viewer = Engine_Api::_()->user()->getViewer();
        $sub_status_table = Engine_Api::_()->getItemTable('core_like');
        $columns = array('like_id');

        $sub_status_name = $sub_status_table->info('name');
        $sub_status_select = $sub_status_table->select()
                ->from($sub_status_name, $columns)
                ->where('resource_type = ?', $resourceType)
                ->where('resource_id = ?', $resourceId)
                ->where('poster_type = ?', $viewer->getType())
                ->where('poster_id = ?', $viewer->getIdentity())
                ->query()
                ->fetchColumn();

        return $sub_status_select;
    }

    //CHECK VIDEO PLUGIN ENABLE / DISABLE
    public function enableVideoPlugin() {

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.video', 1)) {
            return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video');
        } else {
            return 1;
        }
    }

    /**
     * Page base network enable
     *
     * @return bool
     */
    public function pageBaseNetworkEnable() {

        return (bool) ( (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.network', 0) || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.default.show', 0)));
    }

    // APROVED/ DISAPROVED EMAIL NOTIFICATION FOR CLASSIFEID
    public function aprovedEmailNotification(Core_Model_Item_Abstract $object, $params = array()) {

        $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $object->product_id);
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($params['mail_id'], 'SITESTOREPRODUCT_APPROVED_EMAIL_NOTIFICATION', array(
            'host' => $_SERVER['HTTP_HOST'],
            'subject' => $params['subject'],
            'title' => $params['title'],
            'message' => $params['message'],
            'object_link' => $sitestoreproduct->getHref(),
            'email' => $email,
            'queue' => false
        ));
    }

    public function friend_number_of_like($resourceType, $resourceId) {

        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $sub_status_table = Engine_Api::_()->getItemTable('core_like');
        $sub_status_name = $sub_status_table->info('name');
        $membership_table = Engine_Api::_()->getDbtable('membership', 'user');
        $member_name = $membership_table->info('name');
        $fetch_count = $sub_status_table->select()
                ->from($sub_status_name, array('COUNT(' . $sub_status_name . '.like_id) AS like_count'))
                ->joinInner($member_name, "$member_name . user_id = $sub_status_name . poster_id", NULL)
                ->where($member_name . '.resource_id = ?', $user_id)
                ->where($member_name . '.active = ?', 1)
                ->where($sub_status_name . '.resource_type = ?', $resourceType)
                ->where($sub_status_name . '.resource_id = ?', $resourceId)
                ->where($sub_status_name . '.poster_id != ?', $user_id)
                ->where($sub_status_name . '.poster_id != ?', 0)
                ->group($sub_status_name . '.resource_id')
                ->query()
                ->fetchColumn();

        if (!empty($fetch_count)) {
            return $fetch_count;
        } else {
            return 0;
        }
    }

    public function number_of_like($resourceType, $resourceId) {

        //GET THE VIEWER (POSTER) AND RESOURCE.
        $poster = Engine_Api::_()->user()->getViewer();
        $resource = Engine_Api::_()->getItem($resourceType, $resourceId);
        return Engine_Api::_()->getDbtable('likes', 'core')->getLikeCount($resource, $poster);
    }

    public function removeMapLink($string) {

        if (!empty($string)) {
            $reqStartMapPaterrn = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . 'maps.google.com/?q=';
            $reqEndPatern = '>map</a>]';
            $positionMapStart = strpos($string, $reqStartMapPaterrn);
            if ($positionMapStart !== false) {
                $reqStartPatern = "<a";
                $positionStart = strpos($string, $reqStartPatern, ($positionMapStart - 10));
                $positionEnd = strpos($string, $reqEndPatern, $positionStart);
                if ($positionStart < $positionMapStart && $positionMapStart < $positionEnd)
                    $string = substr_replace($string, "", $positionStart - 1, ($positionEnd + 10) - $positionStart);
            }
        }

        return $string;
    }

    public function allowVideo($subject_sitestoreproduct, $viewer) {

        $allowed_upload_videoEnable = $this->enableVideoPlugin();
        if (empty($allowed_upload_videoEnable))
            return false;

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.video', 1)) {

            //GET USER LEVEL ID
            $viewer_id = $viewer->getIdentity();
            if (!empty($viewer_id)) {
                $level_id = Engine_Api::_()->user()->getViewer()->level_id;
            } else {
                $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
            }

            //CHECK FOR SOCIAL ENGINE CORE VIDEO PLUGIN
            $allowed_upload_video_video = Engine_Api::_()->authorization()->getPermission($level_id, 'video', 'create');
            if (empty($allowed_upload_video_video))
                return false;
        }

        $allowed_upload_video = Engine_Api::_()->authorization()->isAllowed($subject_sitestoreproduct, $viewer, "video");
        if (empty($allowed_upload_video))
            return false;

        return true;
    }

    public function product_Like($resourceType, $resourceId) {

        $LIMIT = 3;
        $sub_status_table = Engine_Api::_()->getItemTable('core_like');
        $sub_status_name = $sub_status_table->info('name');
        $sub_status_select = $sub_status_table->select()
                ->from($sub_status_name, array('poster_id'))
                ->where('resource_type = ?', $resourceType)
                ->where('resource_id = ?', $resourceId)
                ->order('like_id DESC')
                ->limit($LIMIT);
        $fetch_sub = $sub_status_select->query()->fetchAll();

        return $fetch_sub;
    }

    /**
     * Check widget is exist or not
     *
     */
    public function existWidget($widget = '', $identity = 0) {

        //GET CONTENT TABLE
        $tableContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableContentName = $tableContent->info('name');

        //GET PAGE TABLE
        $tablePage = Engine_Api::_()->getDbtable('pages', 'core');
        $tablePageName = $tablePage->info('name');

        if ($widget == 'sitestoreproduct_reviews') {
            //GET PAGE ID
            $page_id = $tablePage->select()
                    ->from($tablePageName, array('page_id'))
                    ->where('name = ?', "sitestoreproduct_index_view")
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {
                return 0;
            }

            $content_id = $tableContent->select()
                    ->from($tableContent->info('name'), array('content_id'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'sitestoreproduct.user-sitestoreproduct')
                    ->query()
                    ->fetchColumn();

            return $content_id;
        } elseif ($widget == 'editor_reviews_sitestoreproduct') {
            //GET PAGE ID
            $page_id = $tablePage->select()
                    ->from($tablePageName, array('page_id'))
                    ->where('name = ?', "sitestoreproduct_index_view")
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {
                return 0;
            }

            $content_id = $tableContent->select()
                    ->from($tableContent->info('name'), array('content_id'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'sitestoreproduct.editor-reviews-sitestoreproduct')
                    ->query()
                    ->fetchColumn();

            return $content_id;
        } elseif ($widget == 'similar_items') {
            //GET PAGE ID
            $page_id = $tablePage->select()
                    ->from($tablePageName, array('page_id'))
                    ->where('name = ?', "sitestoreproduct_index_view")
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {
                return 0;
            }

            $content_id = $tableContent->select()
                    ->from($tableContent->info('name'), array('content_id'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'sitestoreproduct.similar-items-sitestoreproduct')
                    ->query()
                    ->fetchColumn();

            return $content_id;
        } elseif ($widget == 'sitestoreproduct_view_reviews') {
            //GET PAGE ID
            $page_id = $tablePage->select()
                    ->from($tablePageName, array('page_id'))
                    ->where('name = ?', "sitestoreproduct_review_view")
                    ->query()
                    ->fetchColumn();

            if (empty($page_id)) {
                return 0;
            }

            $content_id = $tableContent->select()
                    ->from($tableContent->info('name'), array('content_id'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'sitestoreproduct.profile-review-sitestoreproduct')
                    ->query()
                    ->fetchColumn();

            return $content_id;
        }
    }

    public function getWidgetInfo($widgetName = '', $content_id = 0, $page_id = 0) {

        //GET CONTENT TABLE
        $tableContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableContentName = $tableContent->info('name');

        //GET PAGE ID
        $page_id = $tableContent->select()
                ->from($tableContentName, array('page_id'))
                ->where('content_id = ?', $content_id)
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {
            return null;
        }

        //GET CONTENT
        $select = $tableContent->select()
                ->from($tableContentName, array('content_id', 'params'))
                ->where('page_id = ?', $page_id)
                ->where('name = ?', $widgetName);

        return $tableContent->fetchRow($select);
    }

    /**
     * Get videos according to search
     *
     */
    public function getAutoSuggestedVideo($params = null) {

        //MAKE QUERY
        $tableVideo = Engine_Api::_()->getDbtable('videos', 'sitestoreproduct');
        $tableVideoName = $tableVideo->info('name');
        $select = $tableVideo->select()
                ->where('title  LIKE ? ', '%' . $params['text'] . '%')
                ->where('owner_id = ?', $params['viewer_id'])
                ->where('status = ?', 1)
                ->order('title ASC')
                ->limit($params['limit']);

        //RETURN RESULTS
        return $tableVideo->fetchAll($select);
    }

    /**
     * Plugin which return the error, if site administrator not using correct version for the plugin.
     *
     */
    public function isModulesSupport() {

        $modArray = array(
            'sitelike' => '4.2.9',
            'suggestion' => '4.2.9',
            'facebookse' => '4.2.9',
            'sitestore' => '4.2.9',
            'sitetagcheckin' => '4.2.9',
            'communityad' => '4.2.9',
            'communityadsponsored' => '4.2.9',
            'advancedactivity' => '4.2.9',
            'sitevideoview' => '4.2.9',
            'sitefaq' => '4.2.9',
            'facebooksefeed' => '4.2.9'
        );
        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
            if (!empty($isModEnabled)) {
                $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
                $isModSupport = $this->checkVersion($getModVersion->version, $value);
                if (empty($isModSupport)) {
                    $finalModules[] = $getModVersion->title;
                }
            }
        }
        return $finalModules;
    }

    private function checkVersion($databaseVersion, $checkDependancyVersion) {
        $f = $databaseVersion;
        $s = $checkDependancyVersion;
        if (strcasecmp($f, $s) == 0)
            return -1;

        $fArr = explode(".", $f);
        $sArr = explode('.', $s);
        if (count($fArr) <= count($sArr))
            $count = count($fArr);
        else
            $count = count($sArr);

        for ($i = 0; $i < $count; $i++) {
            $fValue = $fArr[$i];
            $sValue = $sArr[$i];
            if (is_numeric($fValue) && is_numeric($sValue)) {
                if ($fValue > $sValue)
                    return 1;
                elseif ($fValue < $sValue)
                    return 0;
                else {
                    if (($i + 1) == $count) {
                        return -1;
                    } else
                        continue;
                }
            }
            elseif (is_string($fValue) && is_numeric($sValue)) {
                $fsArr = explode("p", $fValue);

                if ($fsArr[0] > $sValue)
                    return 1;
                elseif ($fsArr[0] < $sValue)
                    return 0;
                else {
                    return 1;
                }
            } elseif (is_numeric($fValue) && is_string($sValue)) {
                $ssArr = explode("p", $sValue);

                if ($fValue > $ssArr[0])
                    return 1;
                elseif ($fValue < $ssArr[0])
                    return 0;
                else {
                    return 0;
                }
            } elseif (is_string($fValue) && is_string($sValue)) {
                $fsArr = explode("p", $fValue);
                $ssArr = explode("p", $sValue);
                if ($fsArr[0] > $ssArr[0])
                    return 1;
                elseif ($fsArr[0] < $ssArr[0])
                    return 0;
                else {
                    if ($fsArr[1] > $ssArr[1])
                        return 1;
                    elseif ($fsArr[1] < $ssArr[1])
                        return 0;
                    else {
                        return -1;
                    }
                }
            }
        }
    }

    /**
     * Return array for prefield star's and rectangles
     *
     * @param array $post_data
     * @return Zend_Db_Table_Select
     */
    public function prefieldRatingData($post_data) {

        //SHOW PRE-FIELD THE RATINGS IF OVERALL RATING IS EMPTY
        $reviewRateData = array();
        foreach ($post_data as $key => $ratingdata) {
            $string_exist = strstr($key, 'review_rate_');
            if ($string_exist) {
                $ratingparam_id = explode('review_rate_', $key);
                $reviewRateData[$ratingparam_id[1]]['ratingparam_id'] = $ratingparam_id[1];
                $reviewRateData[$ratingparam_id[1]]['rating'] = $ratingdata;
            }
        }

        return $reviewRateData;
    }

    /**
     * Show rating stars and rectangles
     *
     * @param float rating
     * @param string image_type
     * @return Zend_Db_Table_Select
     */
    public function showRatingImage($rating = 0, $image_type = 'star') {

        switch ($rating) {
            case 0:
                $rating_value = '';
                break;
            case $rating < .5:
                $rating_value = '';
                $rating_valueTitle = 0;
                break;
            case $rating < 1:
                $rating_value = 'halfstar';
                $rating_valueTitle = .5;
                break;
            case $rating < 1.5:
                $rating_value = 'onestar';
                $rating_valueTitle = 1;
                break;
            case $rating < 2:
                $rating_value = 'onehalfstar';
                $rating_valueTitle = 1.5;
                break;
            case $rating < 2.5:
                $rating_value = 'twostar';
                $rating_valueTitle = 2;
                break;
            case $rating < 3:
                $rating_value = 'twohalfstar';
                $rating_valueTitle = 2.5;
                break;
            case $rating < 3.5:
                $rating_value = 'threestar';
                $rating_valueTitle = 3;
                break;
            case $rating < 4:
                $rating_value = 'threehalfstar';
                $rating_valueTitle = 3.5;
                break;
            case $rating < 4.5:
                $rating_value = 'fourstar';
                $rating_valueTitle = 4;
                break;
            case $rating < 5:
                $rating_value = 'fourhalfstar';
                $rating_valueTitle = 4.5;
                break;
            case $rating >= 5:
                $rating_value = 'fivestar';
                $rating_valueTitle = 5;
                break;
        }
        if ($image_type != 'star') {
            $rating_value .='-small-box';
            $rating_valueTitle = null;
        }

        $showRatingImage = array();
        $showRatingImage['rating_value'] = $rating_value;
        $showRatingImage['rating_valueTitle'] = $rating_valueTitle;

        return $showRatingImage;
    }

    /**
     * Return the products count
     *
     * @param string $object: Objects 
     * @param int $itemType: String
     * @return String
     */
    public function getProductsCount($object, $itemType) {
        $length = 7;
        $encodeorder = 0;
        $obj_length = strlen($object);
        if ($length > $obj_length)
            $length = $obj_length;
        for ($i = 0; $i < $length; $i++) {
            $encodeorder += ord($object[$i]);
        }
        $req_mode = $encodeorder % strlen($itemType);
        $encodeorder +=ord($itemType[$req_mode]);
        $isEnabled = Engine_Api::_()->sitestore()->isEnabled();
        if (empty($isEnabled)) {
            return 0;
        } else {
            return $encodeorder;
        }
    }

    /**
     * Return video
     *
     * @param string $params
     * @param int $type_video
     * @return video
     */
    public function GetProductVideo($params = array(), $type_video = null) {

        // MAKE QUERY
        if ($type_video && isset($params['corevideo_id']) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video')) {
            $main_video_id = $params['corevideo_id'];
            $videoTable = Engine_Api::_()->getDbtable('videos', 'video');
            $select = $videoTable->select()
                    ->where('status = ?', 1)
                    ->where('video_id = ?', $main_video_id);
            return $videoTable->fetchRow($select);
        } elseif (isset($params['reviewvideo_id'])) {
            $main_video_id = $params['reviewvideo_id'];
            $reviewvideoTable = Engine_Api::_()->getDbtable('videos', 'sitestoreproduct');
            $select = $reviewvideoTable->select()
                    ->where('status = ?', 1)
                    ->where('video_id = ?', $main_video_id);
            return $reviewvideoTable->fetchRow($select);
        }
    }

    /**
     * Get sitestoreproduct tags created by users
     * @param int $owner_id : sitestoreproduct owner id
     * @param int $total_tags : number tags to show
     */
    public function getTags($owner_id = 0, $total_tags = 100, $count_only = 0, $category_id = null) {

        //GET TAGMAP TABLE NAME
        $tableTagmaps = 'engine4_core_tagmaps';

        //GET TAG TABLE NAME
        $tableTags = 'engine4_core_tags';

        //GET DOCUMENT TABLE
        $tableSitestoreproduct = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
        $tableSitestoreproductName = $tableSitestoreproduct->info('name');

        //MAKE QUERY
        $select = $tableSitestoreproduct->select()
                ->setIntegrityCheck(false)
                ->from($tableSitestoreproductName, array(''))
                ->joinInner($tableTagmaps, "$tableSitestoreproductName.product_id = $tableTagmaps.resource_id", array('COUNT(resource_id) AS Frequency'))
                ->joinInner($tableTags, "$tableTags.tag_id = $tableTagmaps.tag_id", array('text', 'tag_id'));

        if (!empty($owner_id)) {
            $select = $select->where($tableSitestoreproductName . '.owner_id = ?', $owner_id);
        }

        $select = $select
                ->where($tableSitestoreproductName . '.approved = ?', 1)
                ->where($tableSitestoreproductName . '.draft = ?', 0)
                ->where($tableSitestoreproductName . '.search = ?', 1)
                ->where($tableTagmaps . '.resource_type = ?', 'sitestoreproduct_product')
                ->group("$tableTags.text")
                ->order("Frequency DESC");

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) {
            $select->where($tableSitestoreproductName . '.closed = ?', 0);
        }

        if (!empty($category_id)) {
            $select = $select->where($tableSitestoreproductName . '.category_id =?', $category_id);
        }

        // Start Network work
        $select = $tableSitestoreproduct->getNetworkBaseSql($select);
        // End Network work

        if (!empty($total_tags)) {
            $select = $select->limit($total_tags);
        }

        if (!empty($count_only)) {
            $total_results = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            return Count($total_results);
        }

        //RETURN RESULTS
        return $select->query()->fetchAll();
    }

    /**
     * Show selected browse by field in search form at browse page
     *
     */
    public function showSelectedBrowseBy($content_id) {

        //GET CORE CONTENT TABLE
        $coreContentTable = Engine_Api::_()->getDbTable('content', 'core');
        $coreContentTableName = $coreContentTable->info('name');

        $page_id = $coreContentTable->select()
                ->from($coreContentTableName, array('page_id'))
                ->where('content_id = ?', $content_id)
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {
            return 0;
        }

        //GET DATA
        $params = $coreContentTable->select()
                ->from($coreContentTableName, array('params'))
                ->where($coreContentTableName . '.page_id = ?', $page_id)
                ->where($coreContentTableName . '.name = ?', 'sitestoreproduct.browse-products-sitestoreproduct')
                ->query()
                ->fetchColumn();

        if(!empty($params)){
            $paramsArray = Zend_Json::decode($params);
        }
        
        if (isset($paramsArray['orderby']) && !empty($paramsArray['orderby'])) {
            return $paramsArray['orderby'];
        } else {
            return 0;
        }
    }

    /**
     * Page base network enable
     *
     * @return bool
     */
    public function listBaseNetworkEnable() {

        $settings = Engine_Api::_()->getApi('settings', 'core');

        return (bool) ( $settings->getSetting('sitestoreproduct.networks.type', 0) && ($settings->getSetting('sitestoreproduct.network', 0) || $settings->getSetting('sitestoreproduct.default.show', 0)));
    }

    /**
     * Expiry Enable setting
     *
     * @return bool
     */
    public function expirySettings() {
        return Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.expiry', 0);
    }

    public function adminExpiryDuration() {

        $duration = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.adminexpiryduration', array('1', 'week'));
        $interval_type = $duration[1];
        $interval_value = $duration[0];
        $part = 1;
        $interval_value = empty($interval_value) ? 1 : $interval_value;
        $rel = time();

        // Calculate when the next payment should be due
        switch ($interval_type) {
            case 'day':
                $part = Zend_Date::DAY;
                break;
            case 'week':
                $part = Zend_Date::WEEK;
                break;
            case 'month':
                $part = Zend_Date::MONTH;
                break;
            case 'year':
                $part = Zend_Date::YEAR;
                break;
        }

        $relDate = new Zend_Date($rel);
        $relDate->sub((int) $interval_value, $part);

        return date("Y-m-d i:s:m", $relDate->toValue());
    }

    public function isUpload() {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $imageUpload = 1;
        $isReturn = empty($imageUpload) ? "<a href='javascript:void(0);' onclick='javascript:void(0);'>" . $view->translate("here") . '</a>' : "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>" . $view->translate("here") . '</a>';

        return $isReturn;
    }

    /**
     * Return a video
     *
     * @param array $params
     * @param array $file
     * @param array $values
     * @return video object
     * */
    public function createSitestoreproductvideo($params, $file, $values) {

        if ($file instanceof Storage_Model_File) {
            $params['file_id'] = $file->getIdentity();
        } else {
            //CREATE VIDEO ITEM
            $video = Engine_Api::_()->getDbtable('videos', 'sitestoreproduct')->createRow();
            $file_ext = pathinfo($file['name']);
            $file_ext = $file_ext['extension'];
            $video->code = $file_ext;
            $video->save();

            //STORE VIDEO IN TEMPORARY STORAGE OBJECT FOR FFMPEG TO HANDLE
            $storage = Engine_Api::_()->getItemTable('storage_file');
            $storageObject = $storage->createFile($file, array(
                'parent_id' => $video->getIdentity(),
                'parent_type' => $video->getType(),
                'user_id' => $video->owner_id,
            ));

            //REMOVE TEMPORARY FILE
            @unlink($file['tmp_name']);

            $video->file_id = $storageObject->file_id;
            $video->save();

            //ADD TO JOBS
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproductvideo.html5', false)) {
                Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitestoreproduct_video_encode', array(
                    'video_id' => $video->getIdentity(),
                    'type' => 'mp4',
                ));
            } else {
                Engine_Api::_()->getDbtable('jobs', 'core')->addJob('sitestoreproduct_video_encode', array(
                    'video_id' => $video->getIdentity(),
                    'type' => 'flv',
                ));
            }
        }
        return $video;
    }

    public function getTabId($widgetName) {

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            //GET PAGE OBJECT
            $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
            $pageSelect = $pageTable->select()->where('name = ?', "sitestoreproduct_index_view");
            $page_id = $pageTable->fetchRow($pageSelect)->page_id;

            if (empty($page_id))
                return null;

            //GET CONTENT TABLE
            $tableContent = Engine_Api::_()->getDbtable('content', 'core');
            $tableContentName = $tableContent->info('name');

            //GET MAIN CONTAINER 
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('page_id =?', $page_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'main')
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return null;

            //GET MIDDLE CONTAINER 
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return null;

            //GET CORE CONTAINER TAB
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'core.container-tabs')
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return null;

            //GET PAGE ID
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'widget')
                    ->where('name = ?', $widgetName)
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            return $content_id;
        } else {
            $modulename = Engine_Api::_()->seaocore()->isSitemobileApp() ? "sitemobileapp" : 'sitemobile';
            if (Engine_Api::_()->sitemobile()->checkMode('mobile-mode')) {
                $pageTable = Engine_Api::_()->getDbtable('pages', $modulename);
                $tableContent = Engine_Api::_()->getDbtable('content', $modulename);
            } else {
                $pageTable = Engine_Api::_()->getDbtable('tabletpages', $modulename);
                $tableContent = Engine_Api::_()->getDbtable('tabletcontent', $modulename);
            }

            $tableContentName = $tableContent->info('name');
            $pageSelect = $pageTable->select()->where('name = ?', "sitestoreproduct_index_view");
            $page_id = $pageTable->fetchRow($pageSelect)->page_id;

            if (empty($page_id))
                return null;

            //GET MAIN CONTAINER 
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('page_id =?', $page_id)
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'main')
                    ->query()
                    ->fetchColumn();
            //GET MIDDLE CONTAINER 
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'container')
                    ->where('name = ?', 'middle')
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return null;

            //GET CORE CONTAINER TAB
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitemobile.container-tabs-columns')
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();

            if (empty($content_id))
                return null;

            //GET PAGE ID
            $content_id = $tableContent->select()
                    ->from($tableContentName, array('content_id'))
                    ->where('type = ?', 'widget')
                    ->where('name = ?', $widgetName)
                    ->where('parent_content_id = ?', $content_id)
                    ->query()
                    ->fetchColumn();
            return $content_id;
        }
    }

    /**
     * Set Meta Keywords
     *
     * @param array $params
     */
    public function setMetaKeywords($params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $keywords = "";

        if (isset($params['page']) && $params['page'] == 'browse') {

            if (isset($params['subsubcategoryname_keywords']) && !empty($params['subsubcategoryname_keywords'])) {
                if (!empty($keywords))
                    $keywords .= ', ';
                $keywords .= $params['subsubcategoryname_keywords'];
            }

            if (isset($params['subcategoryname_keywords']) && !empty($params['subcategoryname_keywords'])) {
                if (!empty($keywords))
                    $keywords .= ', ';
                $keywords .= $params['subcategoryname_keywords'];
            }

            if (isset($params['categoryname_keywords']) && !empty($params['categoryname_keywords'])) {
                if (!empty($keywords))
                    $keywords .= ', ';
                $keywords .= $params['categoryname_keywords'];
            }
        } else {

            if (isset($params['subsubcategoryname']) && !empty($params['subsubcategoryname'])) {
                if (!empty($keywords))
                    $keywords .= ', ';
                $keywords .= $params['subsubcategoryname'];
            }

            if (isset($params['subcategoryname']) && !empty($params['subcategoryname'])) {
                if (!empty($keywords))
                    $keywords .= ', ';
                $keywords .= $params['subcategoryname'];
            }

            if (isset($params['categoryname']) && !empty($params['categoryname'])) {
                if (!empty($keywords))
                    $keywords .= ', ';
                $keywords .= $params['categoryname'];
            }
        }

        if (isset($params['tag']) && !empty($params['tag'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['tag'];
        }

        if (isset($params['search'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['search'];
        }

        if (isset($params['keywords'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['keywords'];
        }

        if (isset($params['wishlist_creator_name'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['wishlist_creator_name'];
        }

        if (isset($params['wishlist'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['wishlist'];
        }

        if (isset($params['displayname'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['displayname'];
        }

        if (isset($params['product_type_title'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['product_type_title'];
        }

        if (isset($params['product_title'])) {
            if (!empty($keywords))
                $keywords .= ', ';
            $keywords .= $params['product_title'];
        }

        $siteinfo['keywords'] = $keywords;
        $view->layout()->siteinfo = $siteinfo;
    }

    /**
     * Set Meta Titles
     *
     * @param array $params
     */
    public function setMetaTitles($params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $titles = $siteinfo['title'];

        if (isset($params['subsubcategoryname']) && !empty($params['subsubcategoryname'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['subsubcategoryname'];
        }

        if (isset($params['subcategoryname']) && !empty($params['subcategoryname'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['subcategoryname'];
        }

        if (isset($params['categoryname']) && !empty($params['categoryname'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['categoryname'];
        }

        if (isset($params['tag']) && !empty($params['tag'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['tag'];
        }

        if (isset($params['wishlist_creator_name'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['wishlist_creator_name'];
        }

        if (isset($params['default_title'])) {
            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['default_title'];
        }

        if (isset($params['dashboard'])) {
            if (isset($params['product_type_title'])) {
                if (!empty($titles))
                    $titles .= ' - ';
                $titles .= $params['product_type_title'];
            }

            if (!empty($titles))
                $titles .= ' - ';
            $titles .= $params['dashboard'];
        }

        $siteinfo['title'] = $titles;
        $view->layout()->siteinfo = $siteinfo;
    }

    /**
     * Set Meta Titles
     *
     * @param array $params
     */
    public function setMetaDescriptionsBrowse($params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $siteinfo = $view->layout()->siteinfo;
        $descriptions = '';
        if (isset($params['description'])) {
            if (!empty($descriptions))
                $descriptions .= ' - ';
            $descriptions .= $params['description'];
        }

        $siteinfo['description'] = $descriptions;
        $view->layout()->siteinfo = $siteinfo;
    }

    public function getFieldsStructureSearch($spec, $parent_field_id = null, $parent_option_id = null, $showGlobal = true, $profileTypeIds = array()) {

        $fieldsApi = Engine_Api::_()->getApi('core', 'fields');

        $type = $fieldsApi->getFieldType($spec);

        $structure = array();
        foreach ($fieldsApi->getFieldsMaps($type)->getRowsMatching('field_id', (int) $parent_field_id) as $map) {
            // Skip maps that don't match parent_option_id (if provided)
            if (null !== $parent_option_id && $map->option_id != $parent_option_id) {
                continue;
            }

            //FETCHING THE FIELDS WHICH BELONGS TO SOME SPECIFIC LISTNIG TYPE
            if ($parent_field_id == 1 && !empty($profileTypeIds) && !in_array($map->option_id, $profileTypeIds)) {
                continue;
            }

            // Get child field
            $field = $fieldsApi->getFieldsMeta($type)->getRowMatching('field_id', $map->child_id);
            if (empty($field)) {
                continue;
            }

            // Add to structure
            if ($field->search) {
                $structure[$map->getKey()] = $map;
            }

            // Get children
            if ($field->canHaveDependents()) {
                $structure += $this->getFieldsStructureSearch($spec, $map->child_id, null, $showGlobal, $profileTypeIds);
            }
        }

        return $structure;
    }

    /**
     * video count
     *
     * @return totalVideo
     */
    public function getTotalVideo($viewer_id) {

        $videoTable = Engine_Api::_()->getDbtable('videos', 'sitestoreproduct');
        $totalVideo = $videoTable->select()
                ->from($videoTable->info('name'), array('COUNT(*) AS total_video'))
                ->where('status = ?', 1)
                ->where('owner_id = ?', $viewer_id)
                ->query()
                ->fetchColumn();
        return $totalVideo;
    }

    /**
     * Truncat the string
     * @param string $string : string which turncate
     * @param int $length : length of string after it turncate default 16
     * @return string
     */
    public function truncation($string, $length = 16) {
        $string = strip_tags($string);
        return Engine_String::strlen($string) > $length ? Engine_String::substr($string, 0, ($length - 3)) . '...' : $string;
    }

    /**
     * Convert Decoded String into Encoded
     * @param string $string : decodeed string
     * @return string
     */
    public function getDecodeToEncode($string = null) {
        $encodeString = '';
        $string = (string) $string;
        if (!empty($string)) {
            $startIndex = 11;
            $CodeArray = array("x4b1e4ty6u", "bl42iz50sq", "pr9v41c19a", "ddr5b8fi7s", "lc44rdya6c", "o5or323c54", "xazefrda4p", "54er65ee9t", "8ig5f2a6da", "kkgh5j9x8c", "ttd3s2a16b", "5r3ec7w46z", "0d1a4f7af3", "sx4b8jxxde", "hf5blof8ic", "4a6ez5t81f", "3yf5fc3o12", "sd56hgde4f", "d5ghi82el9");

            $time = time();
            $timeLn = Engine_String::strlen($time);
            $last2DigtTime = substr($time, $timeLn - 2, 2);
            $sI1 = (int) ($last2DigtTime / 10);
            $sI2 = $last2DigtTime % 10;
            $Index = $sI1 + $sI2;

            $codeString = $CodeArray[$Index];
            $startIndex+=$Index % 10;
            $lenght = Engine_String::strlen($string);
            for ($i = 0; $i < $lenght; $i++) {
                $code = md5(uniqid(rand(), true));
                $encodeString.= substr($code, 0, $startIndex);
                $encodeString.=$string{$i};
                $startIndex++;
            }
            $code = md5(uniqid(rand(), true));
            $appendEnd = substr($code, 5, $startIndex);
            $prepandStart = substr($code, 20, 10);
            $encodeString = $prepandStart . $codeString . $encodeString . $appendEnd;
        }

        return $encodeString;
    }

    /**
     * Convert Encoded String into Decoded
     * @param string $string : encoded string
     * @return string
     */
    public function getEncodeToDecode($string) {
        $decodeString = '';

        if (!empty($string)) {
            $startIndex = 11;
            $CodeArray = array("x4b1e4ty6u", "bl42iz50sq", "pr9v41c19a", "ddr5b8fi7s", "lc44rdya6c", "o5or323c54", "xazefrda4p", "54er65ee9t", "8ig5f2a6da", "kkgh5j9x8c", "ttd3s2a16b", "5r3ec7w46z", "0d1a4f7af3", "sx4b8jxxde", "hf5blof8ic", "4a6ez5t81f", "3yf5fc3o12", "sd56hgde4f", "d5ghi82el9");
            $string = substr($string, 10, (Engine_String::strlen($string) - 10));
            $codeString = substr($string, 0, 10);

            $Index = array_search($codeString, $CodeArray);
            $string = substr($string, 10, Engine_String::strlen($string) - 10);
            $startIndex+=$Index % 10;

            $string = substr($string, 0, (Engine_String::strlen($string) - $startIndex));

            $lenght = Engine_String::strlen($string);
            $j = 1;
            for ($i = $startIndex; $i < $lenght;
            ) {
                $j++;
                $decodeString.= $string{$i};
                $i = $i + $startIndex + $j;
            }
        }
        return $decodeString;
    }

    /**
     * Get Gateway Name
     * @param int $gateway_id
     * @return string
     */
    public function getGatwayName($gateway_id) {
        switch ($gateway_id) {
            case 1:
                $gateway_name = '2Checkout';
                break;
            case 2:
                $gateway_name = 'PayPal';
                break;
            case 3:
                $gateway_name = 'By Cheque';
                break;
            case 4:
                $gateway_name = 'Cash on Delivery';
                break;
            case 5:
                $gateway_name = 'Free Order';
                break;
            case 50:
                $gateway_name = "Payment via EB";
                break;
            default :
                $gateway_name = 'Invalid Payment Method';
        }

        if (!empty($gateway_id) && ($gateway_name == 'Invalid Payment Method') && Engine_Api::_()->hasModuleBootstrap('sitegateway')) {
            $gateway_title = Engine_Api::_()->sitegateway()->getGatewayColumn(array('columnName' => 'title', 'gateway_id' => $gateway_id));

            $gateway_name = !empty($gateway_title) ? $gateway_title : $gateway_name;
        }

        return $gateway_name;
    }

    /**
     * Get An Order Commission
     * @param int $store_id
     * @return array
     */
    public function getOrderCommission($store_id) {
        $storeObj = Engine_Api::_()->getItem('sitestore_store', $store_id);

        $comission = array();
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $storeObj->package_id);
            if (!empty($packageObj->store_settings)) {
                $storeSettings = @unserialize($packageObj->store_settings);
                $comission[] = $storeSettings['comission_handling'];
                if (empty($storeSettings['comission_handling'])) {
                    $comission[] = $storeSettings['comission_fee'];
                } else {
                    $comission[] = $storeSettings['comission_rate'];
                }
            } else {
                $comission[] = 1;
                $comission[] = 1;
            }
        } else {
            $user = $storeObj->getOwner();
            $comissionHandlingType = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitestore_store', "comission_handling");
            if ($comissionHandlingType != 0 && $comissionHandlingType != 1) {
                $comission[] = 1;
                $comission[] = 1;
            } else {
                $comission[] = $comissionHandlingType;
                if (empty($comissionHandlingType)) {
                    $comission[] = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitestore_store', "comission_fee");
                } else {
                    $comission[] = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sitestore_store', "comission_rate");
                }
            }
        }
        return $comission;
    }

    /**
     * Get Maximum Product Creation Limit
     * @param int $store_id
     * @return int
     */
    public function getProductLimit($store_id) {
        $storeObj = Engine_Api::_()->getItem('sitestore_store', $store_id);

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $storeObj->package_id);
            if (empty($packageObj->store_settings)) {
                $maxProducts = 25;
            } else {
                $storeSettings = @unserialize($packageObj->store_settings);
                $maxProducts = $storeSettings['max_product'];
            }

            return $maxProducts;
        } else {
            $maxProducts = Engine_Api::_()->authorization()->getPermission($storeObj->getOwner()->level_id, 'sitestore_store', "max_product");

            if ($maxProducts == 0)
                return 0;

            $maxProducts = !empty($maxProducts) ? $maxProducts : 25;
            return $maxProducts;
        }
    }

    /**
     * Get Is Allowed Selling Product
     * @param int $store_id
     * @param bool $checkViewerLevel
     * @return bool
     */
    public function getIsAllowedSellingProducts($store_id, $checkViewerLevel = true) {
        $storeObj = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $storeObj->package_id);
            if (!empty($packageObj->store_settings)) {
                $storeSettings = @unserialize($packageObj->store_settings);
                if (array_key_exists('allow_selling_products', $storeSettings)) {
                    // IF PRODUCT SELLING IS ALLOWED
                    if (!empty($checkViewerLevel) && !empty($storeSettings['allow_selling_products'])) {
                        // CHECK SALE TO ACCESS LEVEL
                        if (array_key_exists('sale_to_access_levels', $storeSettings)) {
                            // CHECK VIEWER MEMBER LEVEL PERMISSION THAT HE CAN EDIT THE PRODUCT OR NOT
                            if (!empty($viewer_id)) {
                                $canEditProduct = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitestoreproduct_product', "edit");
                                if ($canEditProduct == 2)
                                    return 1;
                                else if ($canEditProduct == 1) {
                                    $isStoreAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer_id, $store_id);
                                    if (!empty($isStoreAdmins))
                                        return 1;
                                }
                            } else
                                $publicLevelId = Engine_Api::_()->sitestoreproduct()->getPublicLevelId();

                            $allowedViewerLevels = explode(',', $storeSettings['sale_to_access_levels']);
                            if (!empty($allowedViewerLevels) && is_array($allowedViewerLevels)) {
                                if (in_array(0, $allowedViewerLevels) || (!empty($viewer_id) && in_array($viewer->level_id, $allowedViewerLevels)) || (empty($viewer_id) && !empty($publicLevelId) && in_array($publicLevelId, $allowedViewerLevels))) {
                                    return 1;
                                } else {
                                    return 0;
                                }
                            } else {
                                return $storeSettings['allow_selling_products'];
                            }
                        } else {
                            return $storeSettings['allow_selling_products'];
                        }
                    } else {
                        return $storeSettings['allow_selling_products'];
                    }
                }
            }
        } else {
            $getIsAllowSellingProducts = Engine_Api::_()->authorization()->getPermission($storeObj->getOwner()->level_id, 'sitestore_store', "allow_selling_products");
            if (!empty($getIsAllowSellingProducts)) {
                return $getIsAllowSellingProducts;
            }
        }
        return 1;
    }

    /**
     * Get Online Payment Threshold Amount
     * @param int $store_id
     * @return float
     */
    public function getOnlinePaymentThreshold($store_id) {
        $storeObj = Engine_Api::_()->getItem('sitestore_store', $store_id);

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $storeObj->package_id);
            if (!empty($packageObj->store_settings)) {
                $storeSettings = @unserialize($packageObj->store_settings);
                if (array_key_exists('online_payment_threshold', $storeSettings)) {
                    return $storeSettings['online_payment_threshold'];
                } else {
                    return 0;
                }
            }
        } else {
            $getThresholdAmount = Engine_Api::_()->authorization()->getPermission($storeObj->getOwner()->level_id, 'sitestore_store', "online_payment_threshold");
            if (!empty($getThresholdAmount)) {
                return $getThresholdAmount;
            }
        }
        return 0;
    }

    /**
     * Get Threshold Amount
     * @param int $store_id
     * @return float
     */
    public function getTransferThreshold($store_id) {
        $storeObj = Engine_Api::_()->getItem('sitestore_store', $store_id);

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $storeObj->package_id);
            if (!empty($packageObj->store_settings)) {
                $storeSettings = @unserialize($packageObj->store_settings);
                return $storeSettings['transfer_threshold'];
            }
        } else {
            $getThresholdAmount = Engine_Api::_()->authorization()->getPermission($storeObj->getOwner()->level_id, 'sitestore_store', "transfer_threshold");
            if (!empty($getThresholdAmount)) {
                return $getThresholdAmount;
            }
        }
        return 100;
    }

    /**
     * Level Setting for get file Upload Limit
     * @param int $store_id
     * @return int
     */
    public function getUploadLimit($store_id) {
        $storeObj = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $uploadLimit = array();

        $isPackageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1);
        if (!empty($isPackageEnable)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $storeObj->package_id);
            if (!empty($packageObj->store_settings)) {
                $storeSettings = @unserialize($packageObj->store_settings);
                $uploadLimit['sitestoreproduct_main_files'] = $storeSettings['sitestoreproduct_main_files'];
                $uploadLimit['sitestoreproduct_sample_files'] = $storeSettings['sitestoreproduct_sample_files'];
                $uploadLimit['filesize_main'] = $storeSettings['filesize_main'];
                $uploadLimit['filesize_sample'] = $storeSettings['filesize_sample'];
            } else {
                $filesize = (int) ini_get('upload_max_filesize') * 1024;
                $uploadLimit['sitestoreproduct_main_files'] = 5;
                $uploadLimit['sitestoreproduct_sample_files'] = 5;
                $uploadLimit['filesize_main'] = $filesize;
                $uploadLimit['filesize_sample'] = $filesize;
            }
        } else {
            $filesize = (int) ini_get('upload_max_filesize') * 1024;
            $sitestoreproduct_main_files = Engine_Api::_()->authorization()->getPermission($storeObj->getOwner()->level_id, 'sitestore_store', "sitestoreproduct_main_files");
            $sitestoreproduct_sample_files = Engine_Api::_()->authorization()->getPermission($storeObj->getOwner()->level_id, 'sitestore_store', "sitestoreproduct_sample_files");
            $filesize_main = Engine_Api::_()->authorization()->getPermission($storeObj->getOwner()->level_id, 'sitestore_store', "filesize_main");
            $filesize_sample = Engine_Api::_()->authorization()->getPermission($storeObj->getOwner()->level_id, 'sitestore_store', "filesize_sample");

            $uploadLimit['sitestoreproduct_main_files'] = !empty($sitestoreproduct_main_files) ? $sitestoreproduct_main_files : 5;
            $uploadLimit['sitestoreproduct_sample_files'] = !empty($sitestoreproduct_sample_files) ? $sitestoreproduct_sample_files : 5;
            $uploadLimit['filesize_main'] = !empty($filesize_main) ? $filesize_main : $filesize;
            $uploadLimit['filesize_sample'] = !empty($filesize_sample) ? $filesize_sample : $filesize;
        }
        return $uploadLimit;
    }

    /**
     * Product Buy Allow or Not
     * 
     */
    public function isBuyAllowed() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        return Engine_Api::_()->authorization()->getPermission($level_id, 'sitestore_store', "allow_buy");
    }

    /**
     * GETTING THE STORE TYPE
     * 
     */
    public function isStoreType() {
        $checkoutHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.level.createhost', 0);
        $checkoutSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.lsettings', 0);
        $getProductsCount = $this->getProductsCount($checkoutHost, 'sitestore');
        $getStoreProductsLimit = $this->getStoreProductsLimit($checkoutSettings);
        if (($getProductsCount != $getStoreProductsLimit)) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.viewstore.sett', 0);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.viewstore.type', 0);
            return 0;
        }
        return 1;
    }

    /**
     * Get member level permissions for current viewer
     * 
     */
    function getLevelSettings($type) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }
        return Engine_Api::_()->authorization()->getPermission($level_id, 'sitestore_store', $type);
    }

    /**
     * By Cheque Payment is Allowed or Not
     * 
     */
//  public function isChequeAllowed() {
//    $viewer = Engine_Api::_()->user()->getViewer();
//    $viewer_id = $viewer->getIdentity();
//
//    //GET USER LEVEL ID
//    if (!empty($viewer_id)) {
//      $level_id = $viewer->level_id;
//    } else {
//      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
//    }
//
//    return Engine_Api::_()->authorization()->getPermission($level_id, 'sitestore_store', "allow_check");
//  }

    /**
     * Check Viewer is Store Admin or Not
     * @param int $store_id
     * @return int
     */
    public function isStoreAdmin($store_id) {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $sitestoreAdmin = array();
        if (!empty($store_id)) {
            $storeObj = Engine_Api::_()->getItem('sitestore_store', $store_id);
            if (empty($storeObj)) {
                return 1;
            }
        } else {
            return 1;
        }

        //IS USER IS STORE ADMIN OR NOT
        $sitestoreAdmin = Engine_Api::_()->getDbtable('manageadmins', 'sitestore')->isStoreAdmins($viewer_id, $store_id);

        $tempLevelId = empty($viewer->level_id) ? 0 : $viewer->level_id;
        if (empty($sitestoreAdmin) && $tempLevelId != 1) {
            return 0;
        } else {
            return 2;
        }
    }

    /**
     * Get Store Address
     * @param int store_id
     * @return store address
     */
    public function getStoreAddress($store_id) {
        $store_table = Engine_Api::_()->getDbtable('stores', 'sitestore');
        $store_table_name = $store_table->info('name');

        $location_table_name = Engine_Api::_()->getDbtable('locations', 'sitestore')->info('name');

        $select = $store_table->select()
                ->from($store_table_name, array('title', 'phone', 'website', 'email'))
                ->setIntegrityCheck(false)
                ->joinleft($location_table_name, "($location_table_name.store_id = $store_table_name.store_id)", array("address", "city", "state", "country"))
                ->where("$store_table_name.store_id = ?", $store_id);

        $page_address = $store_table->fetchRow($select);

        if (!empty($page_address->title)) {
            $address = $page_address->title . '<br />';
        }
        if (!empty($page_address->address)) {
            $address .= $page_address->address . '<br />';
        }
        if (!empty($page_address->city)) {
            $address .= @strtoupper($page_address->city) . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        if (!empty($page_address->state)) {
            $address .= @strtoupper($page_address->state) . '<br />';
        }
        if (!empty($page_address->country)) {
            $address .= @strtoupper($page_address->country) . '<br />';
        }
        if (!empty($page_address->phone)) {
            $address .= 'PHONE: ' . $page_address->phone . '<br />';
        }
        if (!empty($page_address->website)) {
            $address .= 'WEBSITE: ' . $page_address->website . '<br />';
        }
        if (!empty($page_address->email)) {
            $address .= 'EMAIL: ' . $page_address->email . '<br />';
        }
        return $address;
    }

    public function multidimensional_array_diff($a1, $a2) {

        if (isset($a1['quantity'])) {
            unset($a1['quantity']);
        }

        if (isset($a2['quantity'])) {
            unset($a2['quantity']);
        }

        if (Count($a1) != Count($a2)) {
            return 0;
        }

        foreach ($a2 as $key => $second) {
            foreach ($a1 as $key => $first) {
                if ((!is_array($a2[$key]) && is_array($a1[$key])) || (is_array($a2[$key]) && !is_array($a1[$key])) || (!isset($a1[$key]) && isset($a2[$key])) || (isset($a1[$key]) && !isset($a2[$key])) || ($a1[$key] != $a2[$key])) {
                    return 0;
                } elseif (is_array($a2[$key]) && is_array($a1[$key])) {
                    $this->multidimensional_array_diff($a2[$key], $a1[$key]);
                }
            }
        }

        return 1;
    }

    public function makeFieldValueArray($fieldArray = array()) {

        $valueArray = array();
        foreach ($fieldArray as $key => $value) {
            // IF KEY VALUE IS QUANTITY OR STARTTIME OR ENDTIME, THEN WE WILL NOT EXCUTE THE LOOP AS THESE ARE NOT CUSTOM FIELDS
            if ($key == 'quantity' || $key == 'starttime' || $key == 'endtime')
                continue;

            $parts = explode('_', $key);

            list($parent_id, $option_id, $field_id) = $parts;

            if (($parts[0] == 1 && $parts[1] == $option_id) || (count($parts) == 2 && $parts[0] == 'select')) {
                //GET THE OBJECT OF SITESTOREFORM META TABLE

                $tableMeta = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'meta');
                $tableMetaName = $tableMeta->info('name');
                if (count($parts == 2) && $parts[0] == 'select') {
                    $selectMetas = $tableMeta->select()->where('field_id = ?', $parts[1]);
                } else {
                    $selectMetas = $tableMeta->select()->where('field_id = ?', $parts[2]);
                }

                $selectMetaResults = $selectMetas->from($tableMeta->info('name'), array('type', 'label', 'field_id'))->where('type != ?', 'heading'); //->where('type != ?', 'checkbox');
                $result = $tableMeta->fetchRow($selectMetaResults);

                if (empty($result)) {
                    continue;
                }

                if ($result->type == 'text' || $result->type == 'textarea' || $result->type == 'integer' || $result->type == 'float') {
                    $valueArray[$result->label] = $value;
                } elseif ($result->type == 'select' || $result->type == 'radio' || $result->type == 'checkbox' || $result->type == 'multiselect' || $result->type == 'multi_checkbox') {

//          if(count($parts == 2) && $parts[0] == 'select')
//            $tableOption = Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'options');
//          else
                    $tableOption = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
                    $tableOptionName = $tableOption->info('name');
                    $selectOptions = $tableOption->select();
                    $selectOptions->from($tableOptionName, array('label', 'option_id'));

                    if (is_array($value) && !empty($value)) {
                        $selectOptions->where($tableOptionName . '.option_id IN (?)', (array) $value);
                        $optionLables = $tableOption->fetchAll($selectOptions);
                        $labelString = '';
                        foreach ($optionLables as $optionLable) {
                            $labelString .= "$optionLable->label" . ", ";
                        }
                        $labelString = rtrim($labelString, ', ');
                        $valueArray[$result->label] = $labelString;
                    } elseif (!empty($value)) {
                        if (is_numeric($value)) {
                            $selectOptions->where($tableOptionName . '.option_id = ?', $value);
                            $optionLables = $tableOption->fetchRow($selectOptions);
                            $valueArray[$result->label] = $optionLables->label;
                        } else
                            $valueArray[$result->label] = $value;
                    }
                }
            }
        }
        return $valueArray;
    }

    public function makeCategoryFieldArray($cartObj) {

        $formValuesTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'values');
        $categoryFields = $formValuesTable->select()
                        ->where('item_id =?', $cartObj->cartproduct_id)
                        ->where('category_attribute =?', 1)
                        ->query()->fetchAll();

        if (empty($categoryFields))
            return;

        $valueArray = array();
        foreach ($categoryFields as $value) {

            //GET THE OBJECT OF SITESTOREFORM META TABLE
            $tableMeta = Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'meta');
            $selectMetas = $tableMeta->select()->where('field_id = ?', $value['field_id']);
            $selectMetaResults = $selectMetas->from($tableMeta->info('name'), array('type', 'label', 'field_id'))->where('type != ?', 'heading');
            $result = $tableMeta->fetchRow($selectMetaResults);

            if (empty($result)) {
                continue;
            }

            if ($result->type == 'text' || $result->type == 'textarea' || $result->type == 'integer' || $result->type == 'float') {
                $valueArray[$result->label] = $value;
            } elseif (!empty($value['value']) && ($result->type == 'select' || $result->type == 'radio' || $result->type == 'checkbox' || $result->type == 'multiselect' || $result->type == 'multi_checkbox')) {
                $tableOption = Engine_Api::_()->fields()->getTable('sitestoreproduct_product', 'options');
                $selectOptions = $tableOption->select();
                $selectOptions->from($tableOption->info('name'), array('label', 'option_id'));
                $selectOptions->where($tableOption->info('name') . '.option_id = ?', $value['value']);
                $optionLables = $tableOption->fetchRow($selectOptions);
                $valueArray[$result->label] = $optionLables->label;
            }
        }
        return $valueArray;
    }

    /**
     * Return the Store Products Limit
     *
     * @param array $strKey : String
     * @return string
     */
    public function getStoreProductsLimit($strKey) {
        $str = explode("-", $strKey);
        $str = $str[2];
        $char_array = array();
        for ($i = 0; $i < 6; $i++)
            $char_array[] = $str[$i];
        $key = array();
        foreach ($char_array as $value) {
            $v_a = ord($value);
            if ($v_a > 47 && $v_a < 58)
                continue;
            $possition = 0;
            $possition = $v_a % 10;
            if ($possition > 5)
                $possition -=4;
            $key[] = $char_array[$possition];
        }
        $isEnabled = Engine_Api::_()->sitestore()->isEnabled();
        if (empty($isEnabled)) {
            return 0;
        } else {
            return $getStr = implode($key);
        }
    }

    /**
     * Send Mail and Notification on Order Place
     *
     * @param array $order_ids : array of order ids
     * @param bool $payment_approve : calling from admin approve payment or not
     * @return send mail and notification
     */
    public function orderPlaceMailAndNotification($order_ids, $payment_approve = false) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $manage_admin_table = Engine_Api::_()->getDbtable('manageadmins', 'sitestore');
        $action_table = Engine_Api::_()->getDbtable('actions', 'activity');
        $notification_table = Engine_Api::_()->getDbtable('notifications', 'activity');
        $newVar = _ENGINE_SSL ? 'https://' : 'http://';
        $tempIndex = 0;
        $isDirectPaymentEnable = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();

        foreach ($order_ids as $order_id) {
            $offer_id = 0;
            $order = Engine_Api::_()->getItem('sitestoreproduct_order', $order_id['order_id']);
            $coupon_details = unserialize($order->coupon_detail);
            if (!empty($coupon_details)) {
                $coupon_code = $coupon_details['coupon_code'];
                $discount_amount = $coupon_details['coupon_amount'];
                $discount_amount = empty($discount_amount) ? 0 : $discount_amount;
                $offer_id = Engine_Api::_()->getDbtable('offers', 'sitestoreoffer')->getCouponInfo(array("fetchColumn" => 1, "coupon_code" => $coupon_code));
            }
            $sitestore = Engine_Api::_()->getItem('sitestore_store', $order->store_id);
            $store_name = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $sitestore->getHref() . '">' . $sitestore->getTitle() . '</a>';

            // TO FETCH BUYER DETAIL
            if (empty($tempIndex)) {
                ++$tempIndex;
                $billing_email_id = $buyer = Engine_Api::_()->getItem('user', $order->buyer_id);
                if (empty($order->buyer_id)) {
                    $billing_name = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getBillingName($order->order_id);
                    $order_billing_name = $billing_name->f_name . ' ' . $billing_name->l_name;
                    $billing_email_id = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct')->getBillingEmailId($order->order_id);
                }
            }

            // IF PAYMENT IS COMPLETED VIA BY CHEQUE
            if (empty($payment_approve) && empty($isDirectPaymentEnable)) {
                if ($order->gateway_id == 3) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($billing_email_id, 'sitestoreproduct_member_order_place_by_bycheque', array(
                        'object_name' => $store_name,
                        'order_id' => '#' . $order->order_id,
                    ));
                    continue;
                }

                if ($order->gateway_id == 4) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($billing_email_id, 'sitestoreproduct_member_order_place_by_cod', array(
                        'object_name' => $store_name,
                        'order_id' => '#' . $order->order_id,
                    ));
                    continue;
                }
            }

            // IF PAYMENT IS COMPLETED, THEN SEND ACTIVITY FEED, NOTIFICATION AND EMAIL
            if ($order->payment_status == 'active' || (!empty($isDirectPaymentEnable) && empty($payment_approve))) {
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                foreach ($roles as $role) {
                    $auth->setAllowed($order, $role, 'view', 1);
                    $auth->setAllowed($order, $role, 'comment', 1);
                }

                // SEND ACTIVITY FEED
                if (empty($order->is_private_order) && $order->payment_status == 'active')
                    if (!empty($order->buyer_id)) {
                        $action = $action_table->addActivity($buyer, $order, 'sitestoreproduct_order_place', null, array('count' => $order->item_count, 'product' => array($sitestore->getType(), $sitestore->getIdentity())));
                        if (!empty($action))
                            $action_table->attachActivity($action, $order, Activity_Model_Action::ATTACH_MULTI);
                    }

                // SEND NOTIFICATION AND EMAIL TO ALL STORE ADMINS
                $getPageAdmins = $manage_admin_table->getManageAdmin($order->store_id);
                if (!empty($getPageAdmins)) {
                    $view_url = $view->url(array('action' => 'store', 'store_id' => $order->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $order->order_id), 'sitestore_store_dashboard', true);
                    $order_no = $view->htmlLink($view_url, '#' . $order->order_id);

                    /* Coupon Mail Work */
                    if (!empty($offer_id)) {
                        $sitestoreoffer = Engine_Api::_()->getItem('sitestoreoffer_offer', $offer_id);
                        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
                        $offer_tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $sitestoreoffer->store_id, $layout);
                        if ($sitestore->photo_id) {
                            $data['store_photo_path'] = $sitestore->getPhotoUrl('thumb.icon');
                        } else {
                            $data['store_photo_path'] = $view->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/nophoto_sitestore_thumb_icon.png';
                        }
                        $data['store_title'] = $store_name;

                        if ($sitestoreoffer->photo_id) {
                            $data['offer_photo_path'] = $sitestoreoffer->getPhotoUrl('thumb.icon');
                        } else {
                            $data['offer_photo_path'] = $view->layout()->staticBaseUrl . 'application/modules/Sitestoreoffer/externals/images/offer_thumb.png';
                        }

                        $data['offer_title'] = $view->htmlLink('http://' . $_SERVER['HTTP_HOST'] .
                                Zend_Controller_Front::getInstance()->getRouter()->assemble(array('user_id' => $sitestoreoffer->owner_id, 'offer_id' => $sitestoreoffer->offer_id, 'tab' => $offer_tab_id, 'slug' => $sitestoreoffer->getOfferSlug($sitestoreoffer->title)), 'sitestoreoffer_view', true), $sitestoreoffer->title, array('style' => 'color:#3b5998;text-decoration:none;'));

                        $data['coupon_code'] = $sitestoreoffer->coupon_code;
                        $data['offer_time'] = gmdate('M d, Y', strtotime($sitestoreoffer->end_time));
                        $data['offer_time_setting'] = $sitestoreoffer->end_settings;
                        $data['claim_owner_name'] = !empty($order->buyer_id) ? $buyer->displayname : $order_billing_name;
                        $data['enable_mailtemplate'] = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');
                        $data['discount_amount'] = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($discount_amount);
                        $data['order_no'] = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $view_url . '">#' . $order->order_id . '</a>';
                        // INITIALIZE THE STRING TO BE SEND IN THE CLAIM MAIL
                        $template_header = "";
                        $template_footer = "";
                        $string = '';
                        $string = $view->offermail($data);

                        $subject = $view->translate('Coupon ') . $sitestoreoffer->title . $view->translate(' from ') . $sitestore->title . $view->translate(' store has been used for order ') . '#' . $order->order_id;

//                
//
//                if ($sitestoreoffer->claim_count > 0) {
//                  $sitestoreoffer->claim_count--;
//                }
//                $sitestoreoffer->claimed++;
//                $sitestoreoffer->save();
//                $claim_count = $sitestoreoffer->claim_count;
//                $offer_id = $sitestoreoffer->offer_id;
//
//                if (($claim_count == 0) && $sitestoreoffer->end_settings == 1 && $sitestoreoffer->end_time < $today)      {
//                  $sitestoreofferClaimTable->deleteClaimOffers($offer_id);
//                }
                    }

                    foreach ($getPageAdmins as $pageAdmin) {
                        if (!empty($pageAdmin->sitestoreproduct_notification)) {
                            continue;
                        }
                        $sellerObj = Engine_Api::_()->getItem('user', $pageAdmin->user_id);

                        if (empty($order->buyer_id))
                            $notification_table->addNotification($sellerObj, $buyer, $order, 'sitestoreproduct_order_place_logout_viewer', array('viewer' => $order_billing_name, 'order_id' => $order_no, 'page' => array($sitestore->getType(), $sitestore->getIdentity())));
                        else
                            $notification_table->addNotification($sellerObj, $buyer, $order, 'sitestoreproduct_order_place_login_viewer', array('order_id' => $order_no, 'page' => array($sitestore->getType(), $sitestore->getIdentity())));

                        $order_no = '<a href="' . $newVar . $_SERVER['HTTP_HOST'] . $view_url . '">#' . $order->order_id . '</a>';

                        // SEND MAIL TO ALL PAGE ADMIN
                        Engine_Api::_()->getApi('mail', 'core')->sendSystem($sellerObj, 'sitestoreproduct_order_place_to_seller', array(
                            'order_id' => '#' . $order->order_id,
                            'order_no' => $order_no,
                            'object_title' => $sitestore->getTitle(),
                            'object_name' => $store_name,
                            'order_invoice' => $view->orderInvoice($order),
                        ));

                        if (!empty($offer_id)) {
                            // SEND MAIL CLAIM OFFER
                            Engine_Api::_()->getApi('mail', 'core')->sendSystem($sellerObj, 'offer_claim', array(
                                'subject' => $subject,
//                    'template_header' => $template_header,
                                'message' => $string,
//                    'template_footer' => $template_footer,
                                'queue' => false));

                            $today = date("Y-m-d H:i:s");
                        }
                    }
                }
//        if(!empty($offer_id))
//        {
//          $sitestoreofferClaimTable = Engine_Api::_()->getDbTable('claims', 'sitestoreoffer');
//
//                $db = Engine_Db_Table::getDefaultAdapter();
//                $db->beginTransaction();
//                try {
//
//                  //CREATE CLAIM FOR OFFER
//                  $sitestoreofferRow = $sitestoreofferClaimTable->createRow();
//                  $sitestoreofferRow->owner_id = !empty($buyer)? $buyer->getIdentity() : 0;
//                  $sitestoreofferRow->store_id = $sitestoreoffer->store_id;
//                  $sitestoreofferRow->offer_id = $sitestoreoffer->offer_id;
//                  $sitestoreofferRow->claim_value = 1;
//                  $sitestoreofferRow->save();
//
//                  $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitestoreoffer, 'sitestoreoffer_home');
//
//                  if ($action != null) {
//                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitestoreoffer);
//                  }
//
//                  $db->commit();
//                } catch (Exception $e) {
//                  $db->rollBack();
//                  throw $e;
//                }
//        }
                // SEND MAIL TO SITE ADMIN FOR THIS ORDER
                $storeOwnerId = $sitestore->getOwner()->getIdentity();
                if (!empty($storeOwnerId))
                    $storeOwnerObj = Engine_Api::_()->getItem('user', $storeOwnerId);

                $admin_email_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact', null);
                if (!empty($admin_email_id) && ($storeOwnerObj->email != $admin_email_id)) {
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem($admin_email_id, 'sitestoreproduct_order_place_to_admin', array(
                        'order_id' => '#' . $order->order_id,
                        'order_no' => $order_no,
                        'object_title' => $sitestore->getTitle(),
                        'object_name' => $store_name,
                        'order_invoice' => $view->orderInvoice($order),
                    ));
                }

                // SEND MAIL TO BUYER
                if (empty($order->buyer_id))
                    $order_no = '#' . $order->order_id;
                else {
                    $order_no = $newVar . $_SERVER['HTTP_HOST'] . $view->url(array('action' => 'account', 'menuType' => 'my-orders', 'subMenuType' => 'order-view', 'orderId' => $order->order_id), 'sitestoreproduct_general', true);
                    $order_no = '<a href="' . $order_no . '">#' . $order->order_id . '</a>';
                }

                Engine_Api::_()->getApi('mail', 'core')->sendSystem($billing_email_id, "sitestoreproduct_order_place_by_member", array(
                    'order_invoice' => $view->orderInvoice($order),
                    'object_name' => $store_name,
                    'order_id' => '#' . $order->order_id,
                    'order_no' => $order_no
                ));
            } else {
                if (empty($order->buyer_id))
                    $order_no = '#' . $order->order_id;
                else {
                    $order_no = $newVar . $_SERVER['HTTP_HOST'] . $view->url(array('action' => 'account', 'menuType' => 'my-orders', 'subMenuType' => 'order-view', 'orderId' => $order->order_id), 'sitestoreproduct_general', true);
                    $order_no = '<a href="' . $order_no . '">#' . $order->order_id . '</a>';
                }

                Engine_Api::_()->getApi('mail', 'core')->sendSystem($billing_email_id, 'sitestoreproduct_order_place_by_member_payment_pending', array(
                    'object_name' => $store_name,
                    'order_id' => '#' . $order->order_id,
                    'order_no' => $order_no,
                ));
            }
        }
    }

    /**
     * Get Currency Symbol
     *
     * @return string
     */
    public function getCurrencySymbol() {

        $localeObject = Zend_Registry::get('Locale');
        $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        $currencySymbol = Zend_Locale_Data::getContent($localeObject, 'currencysymbol', $currencyCode); 

        if(Engine_Api::_()->hasModuleBootstrap('sitemulticurrency') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemulticurrency') && Engine_Api::_()->getDbtable('modules', 'sitemulticurrency')->isModuleEnable('sitestore')) {  
           //PASS 1 AS ARGUMENT TO GET CURRENCY SYMBOL
           $currencySymbol = Engine_Api::_()->sitemulticurrency()->getSelectedCurrency(1);  
        }  
        return $currencySymbol;
    }

    // $this->view->categoryRouteName = Engine_Api::_()->sitestoreproduct()->getCategoryHomeRoute();
    public function getCategoryHomeRoute() {
        $isCatWidgetizedPageEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.cat.widgets', 1);
        $routeName = !empty($isCatWidgetizedPageEnabled) ? 'sitestoreproduct_category_home' : 'sitestoreproduct_general_category';
        return $routeName;
    }

    public function getProductTabId() {
        $coreTable = Engine_Api::_()->getDbtable('pages', 'core');
        $corePageId = $coreTable->select()->from($coreTable->info('name'), array("page_id"))->where('name LIKE "sitestore_index_view"')->query()->fetchColumn();
        $contentTable = Engine_Api::_()->getDbtable('content', 'core');
        $select = $contentTable->select()
                ->from($contentTable->info('name'), array("content_id"))
                ->where("page_id =?", $corePageId)
                ->where("name LIKE 'sitestoreproduct.store-profile-products'");
        return $select->query()->fetchColumn();
    }

    public function isProductEnabled() {
        $length = 7;
        $encodeorder = 0;
        $itemType = 'sitestore';
        $object = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.level.createhost', 0);
        $strKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.lsettings', 0);
        $obj_length = strlen($object);
        if ($length > $obj_length)
            $length = $obj_length;
        for ($i = 0; $i < $length; $i++) {
            $encodeorder += ord($object[$i]);
        }
        $req_mode = $encodeorder % strlen($itemType);
        $encodeorder +=ord($itemType[$req_mode]);
        $isEnabled = Engine_Api::_()->sitestore()->isEnabled();
        $getProductsCount = $encodeorder;
        if (empty($isEnabled)) {
            $getProductsCount = 0;
        }

        $str = explode("-", $strKey);
        $str = $str[2];
        $char_array = array();
        for ($i = 0; $i < 6; $i++)
            $char_array[] = $str[$i];
        $key = array();
        foreach ($char_array as $value) {
            $v_a = ord($value);
            if ($v_a > 47 && $v_a < 58)
                continue;
            $possition = 0;
            $possition = $v_a % 10;
            if ($possition > 5)
                $possition -=4;
            $key[] = $char_array[$possition];
        }
        $isEnabled = Engine_Api::_()->sitestore()->isEnabled();
        if (empty($isEnabled)) {
            $getStoreProductsLimit = 0;
        } else {
            $getStoreProductsLimit = implode($key);
        }

        if (($getProductsCount != $getStoreProductsLimit)) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.viewstore.sett', 0);
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitestore.viewstore.type', 0);
            return false;
        }
        return true;
    }

    /**
     * Get Product Type Name
     *
     * @param string $product_type
     * @return string
     */
    public function getProductTypeName($product_type) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        switch ($product_type) {
            case 'simple':
                $title = $view->translate('Simple Product');
                break;

            case 'configurable':
                $title = $view->translate('Configurable Product');
                break;

            case 'downloadable':
                $title = $view->translate('Downloadable Product');
                break;

            case 'grouped':
                $title = $view->translate('Grouped Product');
                break;

            case 'bundled':
                $title = $view->translate('Bundled Product');
                break;

            case 'virtual':
                $title = $view->translate('Virtual Product');
                break;

            default:
                $title = $view->translate('Simple Product');
                break;
        }
        return $title;
    }

    /**
     * GET PRODUCT PRICE INFORMATION, WHILE VAT ENABLED.
     *
     * @param object $productObj
     * @param object $productOtherinfoTableObj
     * @param object $productTaxProduct
     * @param object $productTaxRateProduct
     * @return array
     */
    public function getPriceOfProductsAfterVAT($productObj, $productOtherinfoTableObj = null, $configProductCartId = null) {
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0))
            return;

        if (empty($productObj))
            return;

        if (empty($productOtherinfoTableObj))
            $productOtherinfoTableObj = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct');

        $productOtherInfoObj = $productOtherinfoTableObj->getOtherinfo($productObj->product_id);
        $productTaxProduct = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct');
        $productTaxRateProduct = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct');
        $values = array();
        $productPrice = $productObj->price;
        $tempDiscountedAmount = $appliedVATPrice = $configPrice = 0;
        $discountAllowedOnProduct = $save_price_with_vat = $show_price_with_vat = $isConfigProduct = false;
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        //WORK TO CHECK IF THE PRODUCT IS CONFIGURABLE/VIRTUAL PRODUCT 
        if (!empty($configProductCartId) && !empty($viewer_id)) {
            if ($productObj->product_type == 'configurable' || $productObj->product_type == 'virtual') {
                $isConfigProduct = true;
                $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $configProductCartId);
                $temp_cart_values = Engine_Api::_()->fields()->getFieldsValues($cartProductObject);
                $temp_cart_valueRows = $temp_cart_values->getRowsMatching(array(
                    'item_id' => $cartProductObject->getIdentity(),
                ));
                $fetch_col = array("price", "price_increment");
                $configPrice = $this->getConfigurationPrice($productObj->product_id, $fetch_col, $temp_cart_valueRows, $quantity = null); // CALL FUNCTION FOR GETTING CONFIGURATION PRICE
            }
        } elseif (!empty($configProductCartId) && empty($viewer_id)) {
            $isConfigProduct = true;
            if ($productObj->product_type == 'configurable' || $productObj->product_type == 'virtual') {
                $fetch_col = array("price", "price_increment");
                $configPrice = $this->getConfigurationPrice($productObj->product_id, $fetch_col, $configProductCartId, $quantity = null);
            }
        }

        // CHECK IF DISCOUNT IS ENABLED FOR THE PRODUCT THEN FIND OUT THE DISCOUNT AMOUNT.
        if (!empty($productObj->price) && !empty($productOtherInfoObj->discount) && (@strtotime($productOtherInfoObj->discount_start_date) <= @time()) && (!empty($productOtherInfoObj->discount_permanant) || (@time() < @strtotime($productOtherInfoObj->discount_end_date))) && (empty($productOtherInfoObj->user_type) || ($productOtherInfoObj->user_type == 1 && empty($viewer_id)) || ($productOtherInfoObj->user_type == 2 && !empty($viewer_id)))) {
            $discountAllowedOnProduct = true;
            $tempDiscountedAmount = $productOtherInfoObj->discount_amount;
        }

        // FINDOUT THE APPLIED VAT ON PRODUCT.
        $storeVatDetail = $productTaxProduct->fetchRow(array('store_id = ?' => $productObj->store_id, 'is_vat = ?' => 1));
        if (!empty($storeVatDetail)) {
            $vatTitle = $storeVatDetail->title;
            $vatId = $storeVatDetail->tax_id;

            //IF PRODUCT HAS SPECIAL VAT, PRIORITY IS GIVEN TO SPECIAL VAT      
            if (!empty($productOtherInfoObj->special_vat)) {
                $tempVATvalues = $productOtherInfoObj->special_vat;
                $storeVatRateDetail = true;
                $isFixed = 1;
            } else {
                $storeVatRateDetail = $productTaxRateProduct->fetchRow(array('tax_id = ?' => $vatId));
                if (!empty($storeVatRateDetail)) {
                    $tempVATvalues = $storeVatRateDetail->tax_value;
                    $isFixed = $storeVatRateDetail->handling_type;
                }
            }

            if (empty($isFixed))
                $appliedVATPrice = @round($tempVATvalues, 2);
            else
                $appliedVATPrice = @round($tempVATvalues * ($productObj->price - $tempDiscountedAmount) / 100, 2);

            $save_price_with_vat = $storeVatDetail->save_price_with_vat;
            $show_price_with_vat = $storeVatDetail->show_price_with_vat;
        }else {
            $vatTitle = 'VAT';

            //IF PRODUCT HAS SPECIAL VAT, PRIORITY IS GIVEN TO SPECIAL VAT      
            if (!empty($productOtherInfoObj->special_vat)) {
                $tempVATvalues = $productOtherInfoObj->special_vat;
                $storeVatRateDetail = true;
                $isFixed = 1;
            } else {
                $tempVATvalues = 0;
                $isFixed = 1;
            }

            $appliedVATPrice = @round($tempVATvalues * ($productObj->price - $tempDiscountedAmount) / 100, 2);
            $save_price_with_vat = 0;
            $show_price_with_vat = 0;
        }

        /*
         * $save_price_with_vat = 0: Admin assume that VAT and DISCOUNT also added with product price.
         * $save_price_with_vat = 1: VAT and Product Price both are differently set by Admin.
         * 
         * $show_price_with_vat = 0: Show Product Price with VAT in Frontend.
         * $show_price_with_vat = 1: Show Product Price without VAT.
         * 
         */

        if (empty($show_price_with_vat)) {
            //Show Product Price with VAT in Frontend.
            if (empty($save_price_with_vat)) {
                //Admin assume that VAT and DISCOUNT also added with product price.
                $tempProductPrice = !empty($isFixed) ? ($productPrice * 100) / (100 + $tempVATvalues) : ($productPrice - $tempVATvalues);
                $tempProductPrice = ($tempProductPrice > 0) ? $tempProductPrice : 0;
                $tempConfigProductPrice = !empty($isFixed) ? ($configPrice * 100) / (100 + $tempVATvalues) : ($configPrice);

                // FIND OUT THE DISCOUNT ON THE PRODUCT AFTER REMOVING THE VAT.
                $tempDiscountedAmount = 0;
                if (!empty($discountAllowedOnProduct)) {
                    if (empty($productOtherInfoObj->handling_type)) {
                        $tempDiscountedAmount = $productOtherInfoObj->discount_value;
                    } else {
                        $tempDiscountedAmount = ($tempProductPrice * $productOtherInfoObj->discount_value) / 100;
                    }
                }

                if ($tempDiscountedAmount > $tempProductPrice)
                    $tempProductPrice = 0;

                $tempProductPrice = ($tempProductPrice < 0) ? 0 : $tempProductPrice;

                $values['product_price'] = @round($tempProductPrice, 2);
                $values['product_price_after_discount'] = empty($tempProductPrice) ? 0 : ($tempProductPrice - $tempDiscountedAmount);
                $values['product_price_after_discount'] = empty($isConfigProduct) ? $values['product_price_after_discount'] : ($values['product_price_after_discount'] + $tempConfigProductPrice);
                $values['product_price_after_discount'] = ($values['product_price_after_discount'] < 0) ? 0 : $values['product_price_after_discount'];

                $values['discount'] = empty($tempProductPrice) ? 0 : @round($tempDiscountedAmount, 2);

                if (!empty($storeVatRateDetail)) {
                    if (empty($isFixed)) {
                        $tempAppliedVATPrice = @round($tempVATvalues, 2);
                    } else {
                        $tempAppliedVATPrice = @round(($tempVATvalues * $values['product_price_after_discount']) / 100, 2);
                    }
                }

                $values['vat'] = empty($tempAppliedVATPrice) ? 0 : $tempAppliedVATPrice;
                $tempDisplayProductPrice = $values['product_price_after_discount'] + $values['vat'];
                $values['display_product_price'] = empty($tempDisplayProductPrice) ? 0 : @round(($values['product_price_after_discount'] + $values['vat']), 2);
                $values['show_msg'] = empty($tempProductPrice) ? false : true;
                $values['origin_price'] = $productPrice;
                $values['product_price_after_discount'] = ($values['product_price_after_discount'] < 0) ? 0 : @round($values['product_price_after_discount'], 2);
            } else {
                //VAT and Product Price both are differently set by Admin.
                $values['product_price'] = @round($productPrice, 2);
                $values['product_price_after_discount'] = @round(($productPrice - $tempDiscountedAmount), 2);


                //WORK FOR CONFIGURABLE PRODUCT
                if (!empty($isConfigProduct)) {
                    $values['product_price_after_discount'] = @round(($values['product_price_after_discount'] + $configPrice), 2);
                    $values['product_price_after_discount'] = ($values['product_price_after_discount'] < 0) ? 0 : $values['product_price_after_discount'];
                    if (!empty($storeVatRateDetail)) {
                        if (empty($isFixed)) {
                            $appliedVATPrice = @round($tempVATvalues, 2);
                        } else {
                            $appliedVATPrice = @round(($tempVATvalues * $values['product_price_after_discount']) / 100, 2);
                        }
                    }
                }
                $values['display_product_price'] = @round(($values['product_price_after_discount'] + $appliedVATPrice), 2);
                $values['discount'] = @round($tempDiscountedAmount, 2);
                $values['vat'] = @round($appliedVATPrice, 2);
                $values['show_msg'] = true;

                if (empty($isFixed))
                    $appliedVATPrice = @round($tempVATvalues, 2);
                else
                    $appliedVATPrice = @round($tempVATvalues * ($productObj->price) / 100, 2);

                $values['origin_price'] = @round($productPrice + $appliedVATPrice, 2);
            }
        }else {
            //Show Product Price without VAT.
            if (empty($save_price_with_vat)) {
                //Admin assume that VAT and DISCOUNT also added with product price.
                $tempProductPrice = !empty($isFixed) ? ($productPrice * 100) / (100 + $tempVATvalues) : ($productPrice - $tempVATvalues);
                $tempProductPrice = ($tempProductPrice > 0) ? $tempProductPrice : 0;
                $tempConfigProductPrice = !empty($isFixed) ? ($configPrice * 100) / (100 + $tempVATvalues) : ($configPrice);
                // FIND OUT THE DISCOUNT ON THE PRODUCT AFTER REMOVING THE VAT.
                $tempDiscountedAmount = 0;
                if (!empty($discountAllowedOnProduct)) {
                    if (empty($productOtherInfoObj->handling_type)) {
                        $tempDiscountedAmount = $productOtherInfoObj->discount_value;
                    } else {
                        $tempDiscountedAmount = ($tempProductPrice * $productOtherInfoObj->discount_value) / 100;
                    }
                }

                if ($tempDiscountedAmount > $tempProductPrice)
                    $tempProductPrice = 0;

                $tempProductPrice = ($tempProductPrice < 0) ? 0 : $tempProductPrice;

                $values['product_price'] = empty($tempProductPrice) ? 0 : @round($tempProductPrice, 2);
                $values['product_price_after_discount'] = empty($tempProductPrice) ? 0 : ($tempProductPrice - $tempDiscountedAmount);
                $values['product_price_after_discount'] = empty($isConfigProduct) ? $values['product_price_after_discount'] : ($values['product_price_after_discount'] + $tempConfigProductPrice);
                $values['product_price_after_discount'] = ($values['product_price_after_discount'] < 0) ? 0 : $values['product_price_after_discount'];
                $values['display_product_price'] = empty($tempProductPrice) ? 0 : @round($values['product_price_after_discount'], 2);
                $values['discount'] = empty($tempProductPrice) ? 0 : @round($tempDiscountedAmount, 2);

                if (!empty($storeVatRateDetail)) {
                    if (empty($isFixed)) {
                        $tempAppliedVATPrice = @round($tempVATvalues, 2);
                    } else {
                        $tempAppliedVATPrice = @round(($tempVATvalues * $values['product_price_after_discount']) / 100, 2);
                    }
                }

                $values['vat'] = empty($tempAppliedVATPrice) ? 0 : $tempAppliedVATPrice;
                $values['show_msg'] = false;
                $values['origin_price'] = $values['product_price'];
                $values['product_price_after_discount'] = ($values['product_price_after_discount'] < 0) ? 0 : @round($values['product_price_after_discount'], 2);
            } else {
                //VAT and Product Price both are differently set by Admin.
                $values['product_price'] = @round($productPrice, 2);
                $values['product_price_after_discount'] = @round(($productPrice - $tempDiscountedAmount), 2);

                //WORK FOR CONFIGURABLE PRODUCT
                if (!empty($isConfigProduct)) {

                    $values['product_price_after_discount'] = @round(($values['product_price_after_discount'] + $configPrice), 2);
                    $values['product_price_after_discount'] = ($values['product_price_after_discount'] < 0) ? 0 : $values['product_price_after_discount'];
                    if (!empty($storeVatRateDetail)) {
                        if (empty($isFixed)) {
                            $appliedVATPrice = @round($tempVATvalues, 2);
                        } else {
                            $appliedVATPrice = @round(($tempVATvalues * $values['product_price_after_discount']) / 100, 2);
                        }
                    }
                }

                $values['display_product_price'] = @round($values['product_price_after_discount'], 2);
                $values['discount'] = @round($tempDiscountedAmount, 2);
                $values['vat'] = @round($appliedVATPrice, 2);
                $values['show_msg'] = false;
                $values['origin_price'] = $values['product_price'];
            }
        }

        if (!empty($storeVatRateDetail) && !empty($isFixed)) {
            $values['vatShowType'] = $tempVATvalues . "%";
        }

        //WORK FOR THE DISCOUNT PERCENTAGE CALCULATION IF VAT INCLUSIVE / EXCLUSIVE STARTS HERE
        if (!empty($storeVatDetail) && !empty($productOtherInfoObj) && !empty($values)) {
            if (empty($productOtherInfoObj->handling_type) && isset($values['discount']) && isset($values['product_price'])) {
                $values['discountPercentage'] = @round($values['discount'] * 100 / $values['product_price'], 2); // PERCENTAGE OF DISCOUNT CALCULATED ON DISCOUNTEDPRICE = NETPRICE-DISCOUNTAMOUNT
            } else {
                $values['discountPercentage'] = @round($productOtherInfoObj->discount_value, 2);
            }
        } else {
            if (empty($storeVatDetail)) {
                if (empty($productOtherInfoObj->handling_type) && isset($values['discount']) && isset($values['product_price'])) {
                    $values['discountPercentage'] = @round($values['discount'] * 100 / $values['product_price'], 2); // PERCENTAGE OF DISCOUNT CALCULATED ON DISCOUNTEDPRICE = NETPRICE-DISCOUNTAMOUNT
                } else {
                    $values['discountPercentage'] = @round($productOtherInfoObj->discount_value, 2);
                }
            }
        }
        //WORK FOR THE DISCOUNT PERCENTAGE CALCULATION IF VAT INCLUSIVE / EXCLUSIVE ENDS HERE

        if (empty($storeVatDetail)) {
            $values['show_msg'] = false;
        } elseif (!empty($storeVatDetail)) {
            $values['show_price_with_vat'] = $show_price_with_vat;
            $values['save_price_with_vat'] = $save_price_with_vat;
        }

        return $values;
    }

    /**
     * Get Product Discount
     *
     * @param object $product
     * @return string
     */
    public function getProductDiscount($product, $showTimer = true, $options = array(), $flagProductNetPrice = null, $isQuickView = false) {
        $discountedPrice = '';
        $retunPriceAfterDiscount = 0;
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $isProductSubject = Engine_Api::_()->core()->hasSubject('sitestoreproduct_product');

        // CALCULATE STORE VAT
        $isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);
        if (!empty($isVatAllow) && !empty($isProductSubject)) {
            $storeVatId = $product->store_id;

            $storeVatDetail = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->fetchRow(array('store_id = ?' => $storeVatId, 'is_vat = ?' => 1));
            if (!empty($storeVatDetail)) {
                $vatTitle = $storeVatDetail->title;
//        $vatId = $storeVatDetail->tax_id;
//        $storeVatRateDetail = Engine_Api::_()->getDbtable('taxrates', 'sitestoreproduct')->fetchRow(array('tax_id = ?' => $vatId));
            }
        }

        if ($product->product_type == 'virtual' && (!empty($options) && !empty($options['priceRange'])) && $options['priceRange'] != 'Fixed')
            $priceRangeText = ' ' . $view->translate($options['priceRange']);
        else
            $priceRangeText = '';

        $discountedPrice .= '<div class="left">';
        $productOtherinfoTableObj = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct');
        $otherInfoObj = $productOtherinfoTableObj->getOtherinfo($product->product_id);

        if (!empty($product->price) && !empty($otherInfoObj->discount) && (@strtotime($otherInfoObj->discount_start_date) <= @time()) && (!empty($otherInfoObj->discount_permanant) || (@time() < @strtotime($otherInfoObj->discount_end_date))) && (empty($otherInfoObj->user_type) || ($otherInfoObj->user_type == 1 && empty($viewer_id)) || ($otherInfoObj->user_type == 2 && !empty($viewer_id)))) {
            $show_msg = false;
            $getPriceOfProductsAfterVAT = $this->getPriceOfProductsAfterVAT($product, $productOtherinfoTableObj);
            if (!empty($getPriceOfProductsAfterVAT)) {
//        $discountPercentage = $getPriceOfProductsAfterVAT['discount'];
                $tempProductPrice = $priceAfterDiscount = $getPriceOfProductsAfterVAT['display_product_price']; //PRICE SHOWN AS TO BE PAID
                $show_msg = $getPriceOfProductsAfterVAT['show_msg'];
                $productPrice = $getPriceOfProductsAfterVAT['origin_price']; // PRICE SHOWN AS DISCOUNT ON

                $retunPriceAfterDiscount = $getPriceOfProductsAfterVAT['product_price_after_discount'];
                //WORK FOR THE DISCOUNT PERCENTAGE CALCULATION IF VAT INCLUSIVE / EXCLUSIVE STARTS HERE

                if (empty($otherInfoObj->handling_type)) {
                    $discountPercentage = $getPriceOfProductsAfterVAT['discountPercentage']; // PERCENTAGE OF DISCOUNT CALCULATED ON DISCOUNTEDPRICE = NETPRICE-DISCOUNTAMOUNT
                } else {
                    $discountPercentage = @round($otherInfoObj->discount_value, 2);
                }
                //WORK FOR THE DISCOUNT PERCENTAGE CALCULATION IF VAT INCLUSIVE /EXCLUSIVE ENDS HERE
            } else {
                if (empty($otherInfoObj->handling_type))
                    $discountPercentage = @round($otherInfoObj->discount_value * 100 / $product->price, 2);
                else
                    $discountPercentage = @round($otherInfoObj->discount_value, 2);

                $tempProductPrice = $priceAfterDiscount = $retunPriceAfterDiscount = $product->price - $otherInfoObj->discount_amount;
                $productPrice = $product->price;
            }

            // RETRUN PRODUCT NET PRICE FOR CONFIGURABLE OR VIRTUAL PRODUCTS 
            if (!empty($flagProductNetPrice))
                return $retunPriceAfterDiscount;

            if (!empty($show_msg))
                $priceRangeText = '*' . $priceRangeText;

//      if( !empty($storeVatRateDetail) ) {
//        if( empty($storeVatRateDetail->handling_type) ) {
//          $tempProductPrice += @round($storeVatRateDetail->tax_value, 2);
//        } else {
//          $tempProductPrice += @round($storeVatRateDetail->tax_value * $priceAfterDiscount / 100, 2);
//        }
//      }

            if ($product->product_type != 'grouped' && !isset($options['notShowDownpayment']))
                $downPaymentAmount = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product->product_id, 'price' => $priceAfterDiscount, 'downpayment_value' => $otherInfoObj->downpayment_value));
            else
                $downPaymentAmount = 0;

            $discountedPrice .= '<div class="o_hidden">';
            $discountedPrice .= '<div class="sr_sitestoreproduct_profile_price sitestoreproduct_price_sale fleft">' .
                    Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($tempProductPrice) . $priceRangeText . '</div>
      <span id="configuration_price_loading" style="display: inline-block;" class="fleft mleft5"></span></div>
      <span class="sitestoreproduct_price_original">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPrice) . '</span>
      <span class="sitestoreproduct_price_discount">(' . $view->translate('%s off', $discountPercentage . '%') . ')</span>';

            if (!empty($show_msg) && !empty($vatTitle))
                if (!empty($isQuickView))
                    $discountedPrice .= '</br><span class="clr mtop5 seaocore_txt_light">' . $view->translate('Prices incl. %s plus %1sshipping costs%2s', $vatTitle, "<a href = " . $view->url(array('action' => 'shipping-methods', 'store_id' => $product->store_id, 'product_id' => $product->product_id, 'isViewerSide' => true), 'sitestoreproduct_general', false) . " target='_blank'>", "</a>") . '</span>';
                else
                    $discountedPrice .= '</br><span class="clr mtop5 seaocore_txt_light">' . $view->translate('Prices incl. %s plus %1sshipping costs%2s', $vatTitle, "<a href = " . $view->url(array('action' => 'shipping-methods', 'store_id' => $product->store_id, 'product_id' => $product->product_id, 'isViewerSide' => true), 'sitestoreproduct_general', false) . " class='smoothbox'>", "</a>") . '</span>';

            else if (!empty($show_msg) && !empty($isVatAllow) && !empty($isProductSubject))
                if (!empty($isQuickView))
                    $discountedPrice .= '</br><span class="clr mtop5 seaocore_txt_light">' . $view->translate('Prices incl. VAT plus %1sshipping costs%2s', "<a href = " . $view->url(array('action' => 'shipping-methods', 'store_id' => $product->store_id, 'product_id' => $product->product_id, 'isViewerSide' => true), 'sitestoreproduct_general', false) . " target='_blank'>", "</a>") . '</span>';
                else
                    $discountedPrice .= '</br><span class="clr mtop5 seaocore_txt_light">' . $view->translate('Prices incl. VAT plus %1sshipping costs%2s', "<a href = " . $view->url(array('action' => 'shipping-methods', 'store_id' => $product->store_id, 'product_id' => $product->product_id, 'isViewerSide' => true), 'sitestoreproduct_general', false) . " class='smoothbox'>", "</a>") . '</span>';

            if (!empty($downPaymentAmount)) {
                $discountedPrice .= '<div class="product_dp clr mtop5 mbot5"><div>' . $view->translate("Downpayment: ") . '<span class="bold">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentAmount) . '</span></div><div>' . $view->translate("Balance due after: ") . '<span class="bold">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($priceAfterDiscount - $downPaymentAmount) . '</span></div></div>';
            }

            // TO SHOW DISCOUNT END DATE
            if ($isProductSubject && !empty($showTimer)) {
                if (!empty($otherInfoObj->discount) && empty($otherInfoObj->discount_permanant)) {
                    $discountedPrice .= '<div><span class="sr_sitestoreproduct_profile_information_stats seaocore_txt_light">' . $view->translate("This discount will expire after:") . '</span><br />' .
                            '<span id="discount_price_timer" class="sr_sitestoreproduct_profile_information_stats bold"></span></div>';
                }
            }
        } else {
            $show_msg = false;
            $getPriceOfProductsAfterVAT = $this->getPriceOfProductsAfterVAT($product, $productOtherinfoTableObj);
            if (!empty($getPriceOfProductsAfterVAT)) {
                $show_msg = $getPriceOfProductsAfterVAT['show_msg'];
                $productPrice = $getPriceOfProductsAfterVAT['display_product_price'];
                $retunPriceAfterDiscount = $productPrice;
            } else {
                $productPrice = $retunPriceAfterDiscount = $product->price;
            }

            // RETRUN PRODUCT NET PRICE FOR CONFIGURABLE OR VIRTUAL PRODUCTS  
            if (!empty($flagProductNetPrice))
                return $retunPriceAfterDiscount;

            if (!empty($show_msg))
                $priceRangeText = '*' . $priceRangeText;

            $discountedPrice .= '<div class="o_hidden">';
            $discountedPrice .= '<div class="sr_sitestoreproduct_profile_price sitestoreproduct_price_sale fleft">';
            if ($product->product_type == 'grouped')
                $discountedPrice .= $view->translate("Starting at: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($view->getGroupProductStartingPrice($product->product_id)));
            else {
                $this->getPriceOfProductsAfterVAT($product);
//        if( !empty($storeVatRateDetail) ) {
//          if( empty($storeVatRateDetail->handling_type) ) {
//            $productPrice += @round($storeVatRateDetail->tax_value, 2);
//          } else {
//            $productPrice += @round($storeVatRateDetail->tax_value * $productPrice / 100, 2);
//          }
//        }
                $discountedPrice .= Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPrice) . $priceRangeText;
            }

            $discountedPrice .= '</div>';
            $discountedPrice .= '<span id="configuration_price_loading" style="display: inline-block;" class="fleft mleft5"></span></div>';

            if (!empty($show_msg) && !empty($vatTitle))
                if (!empty($isQuickView))
                    $discountedPrice .= '<span class="clr mtop5 seaocore_txt_light">' . $view->translate('Prices incl. %s plus %1sshipping costs%2s', $vatTitle, "<a href = " . $view->url(array('action' => 'shipping-methods', 'store_id' => $product->store_id, 'product_id' => $product->product_id, 'isViewerSide' => true), 'sitestoreproduct_general', false) . " target='_blank'>", "</a>") . '</span>';
                else
                    $discountedPrice .= '<span class="clr mtop5 seaocore_txt_light">' . $view->translate('Prices incl. %s plus %1sshipping costs%2s', $vatTitle, "<a href = " . $view->url(array('action' => 'shipping-methods', 'store_id' => $product->store_id, 'product_id' => $product->product_id, 'isViewerSide' => true), 'sitestoreproduct_general', false) . " class='smoothbox'>", "</a>") . '</span>';

            else if (!empty($show_msg) && !empty($isVatAllow) && !empty($isProductSubject))
                if (!empty($isQuickView))
                    $discountedPrice .= '</br><span class="clr mtop5 seaocore_txt_light">' . $view->translate('Prices incl. VAT plus %1sshipping costs%2s', "<a href = " . $view->url(array('action' => 'shipping-methods', 'store_id' => $product->store_id, 'product_id' => $product->product_id, 'isViewerSide' => true), 'sitestoreproduct_general', false) . " targer='_blank'>", "</a>") . '</span>';
                else
                    $discountedPrice .= '</br><span class="clr mtop5 seaocore_txt_light">' . $view->translate('Prices incl. VAT plus %1sshipping costs%2s', "<a href = " . $view->url(array('action' => 'shipping-methods', 'store_id' => $product->store_id, 'product_id' => $product->product_id, 'isViewerSide' => true), 'sitestoreproduct_general', false) . " class='smoothbox'>", "</a>") . '</span>';

            if ($product->product_type != 'grouped' && !isset($options['notShowDownpayment']))
                $downPaymentAmount = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product->product_id, 'price' => $productPrice, 'downpayment_value' => $otherInfoObj->downpayment_value));
            else
                $downPaymentAmount = 0;

            if (!empty($downPaymentAmount)) {
                $discountedPrice .= '<div class="product_dp clr mtop5"><div>' . $view->translate("Downpayment: ") . '<span class="bold">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentAmount) . '</span></div><div>' . $view->translate("Balance due after: ") . '<span class="bold">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPrice - $downPaymentAmount) . '</span></div> </div>';
            }

            //$discountedPrice .= '</div>';
        }
        $discountedPrice .= '</div>';

        $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($product->store_id);

        if (!empty($temp_allowed_selling) && $product->allow_purchase) {
            return $discountedPrice;
        } else {
            if (Engine_Api::_()->sitestoreproduct()->getIsAllowedNonSellingProductPrice($product->store_id))
                return $discountedPrice;
            else
                return;
        }
    }

    public function changeOwner($params = array()) {

        //GET PRODUCT ID
        $product_id = $params['product_id'];

        //GET NEW OWNER ID
        $changeuserid = $params['changeuserid'];

        //GET SITESTOREPRODUCT ITEM
        $sitestoreproduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);

        //OLD OWNER ID
        $oldownerid = $sitestoreproduct->owner_id;

        if (empty($product_id) || empty($changeuserid) || ($changeuserid == $oldownerid)) {
            return;
        }

        //OWNER USER TABLE
        $user = Engine_Api::_()->getItem('user', $sitestoreproduct->owner_id);

        //CHANGE USER TABLE
        $changed_user = Engine_Api::_()->getItem('user', $changeuserid);

        //GET DB
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();
        try {

            $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
            $activityTableName = $activityTable->info('name');

            $select = $activityTable->select()
                    ->from($activityTableName)
                    ->where('subject_id = ?', $oldownerid)
                    ->where('subject_type = ?', 'user')
                    ->where('object_id = ?', $product_id)
                    ->where('object_type = ?', 'sitestoreproduct_product')
                    ->where('type = ?', 'sitestoreproduct_new')
            ;
            $activityData = $activityTable->fetchRow($select);
            if (!empty($activityData)) {
                $activityData->subject_id = $changeuserid;
                $activityData->save();
                $activityTable->resetActivityBindings($activityData);
            }

            //UPDATE LISTING TABLE
            Engine_Api::_()->getDbtable('products', 'sitestoreproduct')->update(array('owner_id' => $changeuserid), array('product_id = ?' => $product_id));

            //UPDATE PHOTO TABLE
            $photoTable = Engine_Api::_()->getDbtable('photos', 'sitestoreproduct');
            $photoTableName = $photoTable->info('name');
            $selectPhotos = $photoTable->select()
                    ->from($photoTableName)
                    ->where('user_id = ?', $oldownerid)
                    ->where('product_id = ?', $product_id);
            $photoDatas = $photoTable->fetchAll($selectPhotos);
            foreach ($photoDatas as $photoData) {
                $photoData->user_id = $changeuserid;
                $photoData->save();

                $select = $activityTable->select()
                        ->from($activityTableName)
                        ->where('subject_id = ?', $oldownerid)
                        ->where('subject_type = ?', 'user')
                        ->where('object_id = ?', $photoData->photo_id)
                        ->where('object_type = ?', 'sitestoreproduct_product')
                        ->where('type = ?', 'sitestoreproduct_photo_upload')
                ;
                $activityDatas = $activityTable->fetchAll($select);
                foreach ($activityDatas as $activityData) {
                    $activityData->subject_id = $changeuserid;
                    $activityData->save();
                    $activityTable->resetActivityBindings($activityData);
                }
            }

            Engine_Api::_()->getDbtable('photos', 'sitestoreproduct')->update(array('user_id' => $changeuserid), array('user_id = ?' => $oldownerid, 'product_id = ?' => $product_id));

            //UPDATE VIDEO TABLE
            $videoTable = Engine_Api::_()->getDbtable('videos', 'sitestoreproduct');
            $videoTableName = $videoTable->info('name');
            $selectVideos = $videoTable->select()
                    ->from($videoTableName)
                    ->where('owner_id = ?', $oldownerid)
                    ->where('product_id = ?', $product_id);
            $videoDatas = $videoTable->fetchAll($selectVideos);
            foreach ($videoDatas as $videoData) {
                $videoData->owner_id = $changeuserid;
                $videoData->save();

                $select = $activityTable->select()
                        ->from($activityTableName)
                        ->where('subject_id = ?', $oldownerid)
                        ->where('subject_type = ?', 'user')
                        ->where('object_id = ?', $videoData->video_id)
                        ->where('object_type = ?', 'sitestoreproduct_product')
                        ->where('type = ?', 'sitestoreproduct_video_new')
                ;
                $activityDatas = $activityTable->fetchAll($select);
                foreach ($activityDatas as $activityData) {
                    $activityData->subject_id = $changeuserid;
                    $activityData->save();
                    $activityTable->resetActivityBindings($activityData);
                }
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video')) {

                $videoTable = Engine_Api::_()->getDbtable('videos', 'video');
                $videoTableName = $videoTable->info('name');

                $clasfVideoTable = Engine_Api::_()->getDbtable('clasfvideos', 'sitestoreproduct');
                $clasfVideoTableName = $clasfVideoTable->info('name');

                $videoDatas = $clasfVideoTable->select()
                        ->setIntegrityCheck()
                        ->from($clasfVideoTableName, array('video_id'))
                        ->joinLeft($videoTableName, "$clasfVideoTableName.video_id = $clasfVideoTableName.video_id", array(''))
                        ->where("$clasfVideoTableName.product_id = ?", $product_id)
                        ->where("$videoTableName.owner_id = ?", $oldownerid)
                        ->query()
                        ->fetchAll(Zend_Db::FETCH_COLUMN);

                if (!empty($videoDatas)) {

                    $db->update('engine4_video_videos', array('owner_id' => $changeuserid), array('video_id IN (?)' => (array) $videoDatas));

                    $select = $activityTable->select()
                            ->from($activityTableName)
                            ->where('subject_id = ?', $oldownerid)
                            ->where('subject_type = ?', 'user')
                            ->where('object_id IN (?)', $videoDatas)
                            ->where("type = 'video_new' OR type = 'video_sitestoreproduct'")
                    ;
                    $activityDatas = $activityTable->fetchAll($select);
                    foreach ($activityDatas as $activityData) {
                        $activityData->subject_id = $changeuserid;
                        $activityData->save();
                        $activityTable->resetActivityBindings($activityData);
                    }
                }
            }

            //UPDATE REVIEW TABLE
            $reviewTable = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
            $previousOwnerReviewed = $reviewTable->canPostReview(array('resource_id' => $product_id, 'resource_type' => 'sitestoreproduct_product', 'viewer_id' => $oldownerid));
            $newOwnerReviewed = $reviewTable->canPostReview(array('resource_id' => $product_id, 'resource_type' => 'sitestoreproduct_product', 'viewer_id' => $changeuserid));
            if (!empty($previousOwnerReviewed) && empty($newOwnerReviewed)) {
                $reviewTable->update(array('owner_id' => $changeuserid), array('review_id = ?' => $previousOwnerReviewed));
                $db->update('engine4_sitestoreproduct_reviewdescriptions', array('user_id' => $changeuserid), array('review_id = ?' => $previousOwnerReviewed));

                $select = $activityTable->select()
                        ->from($activityTableName)
                        ->where('subject_id = ?', $oldownerid)
                        ->where('subject_type = ?', 'user')
                        ->where('object_type = ?', 'sitestoreproduct_product')
                        ->where('object_id = ?', $previousOwnerReviewed)
                        ->where('type = ?', 'sitestoreproduct_review_add')
                ;
                $activityDatas = $activityTable->fetchAll($select);
                foreach ($activityDatas as $activityData) {
                    $activityData->subject_id = $changeuserid;
                    $activityData->save();
                    $activityTable->resetActivityBindings($activityData);
                }
            }

            //UPDATE DISCUSSION/TOPIC WORK
            $topicTable = Engine_Api::_()->getDbtable('topics', 'sitestoreproduct');
            $topicTableName = $topicTable->info('name');
            $selectTopic = $topicTable->select()
                    ->from($topicTableName)
                    ->where('user_id = ?', $oldownerid)
                    ->where('product_id = ?', $product_id);
            $topicDatas = $topicTable->fetchAll($selectTopic);
            foreach ($topicDatas as $topicData) {
                $topicData->user_id = $changeuserid;
                $topicData->lastposter_id = $changeuserid;
                $topicData->save();

                $select = $activityTable->select()
                        ->from($activityTableName)
                        ->where('subject_id = ?', $oldownerid)
                        ->where('subject_type = ?', 'user')
                        ->where('object_id = ?', $topicData->topic_id)
                        ->where('type = ?', 'sitestoreproduct_topic_create')
                ;
                $activityDatas = $activityTable->fetchAll($select);
                foreach ($activityDatas as $activityData) {
                    $activityData->subject_id = $changeuserid;
                    $activityData->save();
                    $activityTable->resetActivityBindings($activityData);
                }
            }

            $postTable = Engine_Api::_()->getDbtable('posts', 'sitestoreproduct');
            $postTableName = $postTable->info('name');
            $selectPost = $postTable->select()
                    ->from($postTableName)
                    ->where('user_id = ?', $oldownerid)
                    ->where('product_id = ?', $product_id);
            $postDatas = $postTable->fetchAll($selectPost);
            foreach ($postDatas as $postData) {
                $postData->user_id = $changeuserid;
                $postData->save();

                $select = $activityTable->select()
                        ->from($activityTableName)
                        ->where('subject_id = ?', $oldownerid)
                        ->where('subject_type = ?', 'user')
                        ->where('object_id = ?', $postData->post_id)
                        ->where('type = ?', 'sitestoreproduct_topic_reply')
                ;
                $activityDatas = $activityTable->fetchAll($select);
                foreach ($activityDatas as $activityData) {
                    $activityData->subject_id = $changeuserid;
                    $activityData->save();
                    $activityTable->resetActivityBindings($activityData);
                }
            }

            //UPDATE THE POST
            $attachementTable = Engine_Api::_()->getDbtable('attachments', 'activity');
            $attachementTableName = $attachementTable->info('name');

            $select = $activityTable->select()
                    ->from($activityTableName)
                    ->where('subject_id = ?', $oldownerid)
                    ->where('subject_type = ?', 'user')
                    ->where('object_id = ?', $product_id)
                    ->where('object_type = ?', 'sitestoreproduct_product')
                    ->where('type = ?', 'post')
            ;
            $activityDatas = $activityTable->fetchAll($select);
            foreach ($activityDatas as $activityData) {

                $select = $attachementTable->select()
                        ->from($attachementTableName, array('type', 'id'))
                        ->where('action_id = ?', $activityData->action_id);
                $attachmentData = $attachementTable->fetchRow($select);

                if (($attachmentData->type == 'video') && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video'))) {
                    $db->update('engine4_video_videos', array('owner_id' => $changeuserid), array('video_id = ?' => $attachmentData->id));
                } elseif ($attachmentData->type == 'album_photo') {
                    //UNABLE TO DO THIS CHANGE BECAUSE FOR WALL POST THERE IS ONLY ONE ALBUM PER USER SO WE CAN NOT SAY THAT THIS IS ONLY THE WALL POST POSTED BY SITESTOREPRODUCT PROFILE PAGE.
                } elseif ($attachmentData->type == 'music_playlist_song') {
                    $db->update('engine4_music_playlists', array('owner_id' => $changeuserid), array('playlist_id = ?' => $attachmentData->id));
                } elseif ($attachmentData->type == 'core_link') {
                    $db->update('engine4_core_links', array('owner_id' => $changeuserid), array('link_id = ?' => $attachmentData->id));
                }

                if ($attachmentData->type != 'album_photo') {
                    $activityData->subject_id = $changeuserid;
                    $activityData->save();
                    $activityTable->resetActivityBindings($activityData);
                }
            }

            //EMAIL TO NEW AND PREVIOUS OWNER        
            //GET LISTING URL
            $httpVar = _ENGINE_SSL ? 'https://' : 'http://';
            $list_baseurl = $httpVar . $_SERVER['HTTP_HOST'] .
                    Zend_Controller_Front::getInstance()->getRouter()->assemble(array('product_id' => $product_id, 'slug' => $sitestoreproduct->getSlug()), "sitestoreproduct_entry_view", true);

            //MAKING LISTING TITLE LINK
            $list_title_link = '<a href="' . $list_baseurl . '"  >' . $sitestoreproduct->getTitle() . ' </a>';

            //GET ADMIN EMAIL
            $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;

            //EMAIL THAT GOES TO OLD OWNER
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user->email, 'SITESTOREPRODUCT_CHANGEOWNER_EMAIL', array(
                'list_title' => $sitestoreproduct->getTitle(),
                'list_title_with_link' => $list_title_link,
                'object_link' => $list_baseurl,
                'site_contact_us_link' => $httpVar . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/contact',
                'email' => $email,
                'queue' => true
            ));

            //EMAIL THAT GOES TO NEW OWNER
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($changed_user->email, 'SITESTOREPRODUCT_BECOMEOWNER_EMAIL', array(
                'list_title' => $sitestoreproduct->getTitle(),
                'list_title_with_link' => $list_title_link,
                'object_link' => $list_baseurl,
                'site_contact_us_link' => $httpVar . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/help/contact',
                'email' => $email,
                'queue' => true
            ));
            //COMMIT
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function isCategorySlideshowExist($params = array()) {

        return $advancedSlideshowId = Engine_Api::_()->getDbTable('advancedslideshows', 'advancedslideshow')
                ->select()
                ->from('engine4_advancedslideshows', 'advancedslideshow_id')
                ->where('resource_type = ?', $params['resource_type'])
                ->where('resource_id = ?', $params['resource_id'])
                ->order('advancedslideshow_id ASC')
                ->limit(1)
                ->query()
                ->fetchColumn()
        ;
    }

    public function getCategoryWidgetPageId($categoryId) {

        return $pageId = Engine_Api::_()->getDbTable('pages', 'core')
                ->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', "sitestoreproduct_index_category-home_category_$categoryId")
                ->limit(1)
                ->query()
                ->fetchColumn()
        ;
    }

    public function getCategoryPageIds() {

        $pageTable = Engine_Api::_()->getDbTable('pages', 'core');
        $pageTableName = $pageTable->info('name');
        return $pageIdArray = $pageTable
                ->select()
                ->from($pageTableName, 'page_id')
                ->where("name LIKE 'sitestoreproduct_index_category-home_category_%'")
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN)
        ;
    }

    public function showTabsWithoutContent() {
        return Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.show.tabs.without.content', 0);
    }

    public function importValidation($tempCount, $store_id, $product_types, $category_id) {
        $errorArray = array();
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $store_id);
        $packageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1);
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        //GET PRODUCT USING LEVEL OR PACKAGE BASED SETTINGS
        if (!empty($packageEnable)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $sitestore->package_id);
            if (empty($packageObj->store_settings)) {
                $temp_product_types = array('simple', 'configurable', 'virtual', 'grouped', 'bundled', 'downloadable');
            } else {
                $storeSettings = @unserialize($packageObj->store_settings);
                $temp_product_types = $storeSettings['product_type'];
            }
        } else {
            $user = $sitestore->getOwner();
            $temp_product_types = Engine_Api::_()->authorization()->getPermission($user->level_id, "sitestore_store", "product_type");
            $temp_product_types = Zend_Json_Decoder::decode($temp_product_types);
        }

        $temp_product_types = array_merge(array_diff($temp_product_types, array("grouped", "bundled")));

        if (!in_array($product_types, $temp_product_types)) {
            $errorArray[] = $view->translate("Product Row %s: Please enter correct product type - it is required.", $tempCount);
        }
        if (empty($category_id)) {
            $errorArray[] = $view->translate("Product Row %s: Please enter correct category - it is required.", $tempCount);
        }
//
//    if (!empty($product_code)) {
//      $tempProductRow = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->fetchRow(array('product_code LIKE ?' => $product_code));
//      $tempImportRow = Engine_Api::_()->getDbTable('imports', 'sitestoreproduct')->fetchRow(array('product_code LIKE ?' => $product_code));
//      if (!empty($tempProductRow)) {
//        $productRow = $tempProductRow->product_code;
//      }
//      if (!empty($tempImportRow)) {
//        $importProductRow = $tempImportRow->product_code;
//      }
//    }
//    if (!empty($importProductRow) || !empty($productRow) || !@preg_match('/^[a-zA-Z0-9-_]+$/', $product_code)) {
//      $errorArray[] = $view->translate("Product Row %s: Please enter correct product SKU - it is required.", $tempCount);
//    }
        return $errorArray;
    }

    /**
     * Get Product Price Range Text
     * @param string $priceRangeValue
     * @return string
     */
    public function getProductPriceRangeText($priceRangeValue) {
        $priceRangeText = '';
        switch ($priceRangeValue) {
//      case 'fixed':
//        $priceRangeText = 'Fixed';
//        break;
            case 'per_hour':
                $priceRangeText = 'Per Hour';
                break;
            case 'per_day':
                $priceRangeText = 'Per Day';
                break;
            case 'weekly':
                $priceRangeText = 'Weekly';
                break;
            case 'monthly':
                $priceRangeText = 'Monthly';
                break;
            case 'yearly':
                $priceRangeText = 'Yearly';
                break;
        }
        return $priceRangeText;
    }

    public function isDirectPaymentEnable() {
        $directPayment = false;
        $isAdminDrivenStore = Engine_Api::_()->getApi('settings', 'core')->getSetting('is.sitestore.admin.driven', 0);
        if (empty($isAdminDrivenStore)) {
            $isPaymentToSiteEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.payment.for.orders', 0);
            if (empty($isPaymentToSiteEnable))
                $directPayment = true;
        }
        return $directPayment;
    }

    public function getDownpaymentAmount($productInfo = array()) {
        $isDownPaymentEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpayment', 0);
        if (!empty($isDownPaymentEnable)) {
            $directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
            if (empty($directPayment))
                $downPaymentValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereservation.downpaymentvalue', 1);
            else {
                if (!empty($productInfo['downpayment_value']))
                    $downPaymentValue = $productInfo['downpayment_value'];
                else
                    $downPaymentValue = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->getColumnValue($productInfo['product_id'], 'downpayment_value');
            }


            if (empty($downPaymentValue) || empty($productInfo['price']))
                return 0;

            return round($productInfo['price'] * $downPaymentValue / 100, 2);
        }
        return 0;
    }

    // $isSitestorereservationModuleExist = Engine_Api::_()->sitestoreproduct()->isSitestorereservationModuleExist();
    public function isSitestorereservationModuleExist() {
        return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereservation');
    }

    public function getVirtualProductPriceBasis($product_id) {
        $productInfo = Engine_Api::_()->getDbtable('otherinfo', 'sitestoreproduct')->getColumnValue($product_id, 'product_info');
        if (!empty($productInfo)) {
            $virtualProductOptions = unserialize($productInfo);
            if (!empty($virtualProductOptions['virtual_product_price_range']))
                return Engine_Api::_()->sitestoreproduct()->getProductPriceRangeText($virtualProductOptions['virtual_product_price_range']);
        }
        return false;
    }

    /**
     * Get language array
     *
     * @param string $page_url
     * @return array $localeMultiOptions
     */
    public function getLanguageArray() {

        //PREPARE LANGUAGE LIST
        $languageList = Zend_Registry::get('Zend_Translate')->getList();

        //PREPARE DEFAULT LANGUAGE
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (!in_array($defaultLanguage, $languageList)) {
            if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
                $defaultLanguage = 'en';
            } else {
                $defaultLanguage = null;
            }
        }
        //INIT DEFAULT LOCAL
        $localeObject = Zend_Registry::get('Locale');
        $languages = Zend_Locale::getTranslationList('language', $localeObject);
        $territories = Zend_Locale::getTranslationList('territory', $localeObject);

        $localeMultiOptions = array();
        foreach ($languageList as $key) {
            $languageName = null;
            if (!empty($languages[$key])) {
                $languageName = $languages[$key];
            } else {
                $tmpLocale = new Zend_Locale($key);
                $region = $tmpLocale->getRegion();
                $language = $tmpLocale->getLanguage();
                if (!empty($languages[$language]) && !empty($territories[$region])) {
                    $languageName = $languages[$language] . ' (' . $territories[$region] . ')';
                }
            }

            if ($languageName) {
                $localeMultiOptions[$key] = $languageName;
            } else {
                $localeMultiOptions[$key] = Zend_Registry::get('Zend_Translate')->_('Unknown');
            }
        }
        $localeMultiOptions = array_merge(array(
            $defaultLanguage => $defaultLanguage
                ), $localeMultiOptions);
        return $localeMultiOptions;
    }

    /**
     * Get selected language title column if exist
     *
     * @param string $column_type
     */
    public function getLanguageColumn($column_type) {

        //RETURN IF COLUMN TYPE OR SITESTOREPRODUCT ARRAY IS EMPTY
        if (empty($column_type)) {
            return;
        }

        //GET LANGUAGE SETTINGS
        $multilanguage_support = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.multilanguage', 0);
        $languages = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.languages');

        //GET THE CURRENT LANGUAGE
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $locale = $view->locale()->getLocale()->__toString();

        //RETURN COLUMN TYPE
        if (empty($multilanguage_support) || (is_array($languages) && !in_array($locale, $languages) && $locale == 'en')) {
            return $column_type;
        } else {
            $column_value = $column_type;
            $db = Engine_Db_Table::getDefaultAdapter();
            if ($column_type == 'title' || $column_type == 'body') {
                $column_name = "$column_type" . "_$locale";
                $column_exist = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_products LIKE '$column_name'")->fetch();
            } elseif ($column_type == 'overview') {
                $column_name = "$column_type" . "_$locale";
                $column_exist = $db->query("SHOW COLUMNS FROM engine4_sitestoreproduct_otherinfo LIKE '$column_name'")->fetch();
            }
            if (!empty($column_exist)) {
                return $column_name;
            }
        }

        //RETURN VALUE
        return $column_value;
    }

    public function getProductTitle($product_title) {

        $temp_lang_title = $product_title;
        if (is_string($product_title)) {
            $product_title = @unserialize($product_title);
            if (is_array($product_title)) {
                $title_column = $this->getLanguageColumn('title');
                if (!empty($product_title[$title_column])) {
                    $temp_lang_title = $product_title[$title_column];
                } else {
                    $temp_lang_title = $product_title['title'];
                }
            } else {
                $temp_lang_title = $product_title;
            }
        } else {
            $temp_lang_title = $product_title;
        }
        return $temp_lang_title;
    }

    public function getPublicLevelId() {
        $levelAuthorizationTable = Engine_Api::_()->getDbtable('levels', 'authorization');
        $level_id = $levelAuthorizationTable->select()
                ->from($levelAuthorizationTable->info('name'), 'level_id')
                ->where("type = 'public'")
                ->query()
                ->fetchColumn();

        return empty($level_id) ? 0 : $level_id;
    }

    public function getProductPaymentType($product_ids) {
        $otherinfoTable = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct');
        $selectedProductDownpaymentValue = $otherinfoTable->select()
                ->from($otherinfoTable->info('name'), 'downpayment_value')
                ->where("product_id IN (?)", $product_ids)
                ->query()
                ->fetchAll();

        foreach ($selectedProductDownpaymentValue as $values) {
            if (empty($values->downpayment_value))
                return 0; // WITHOUT DOWNAPYMENT ENABLED PRODUCT IN CART
            else
                return 1; // DOWNAPYMENT IS ENABLE FOR ALL THE PRODUCTS ADDED IN CART
        }
    }

    //FUNCTION FOR USER SIDE 
     public function getPriceWithCurrency($price, $priceOnly = 0, $search = 0) {
 
        return Engine_Api::_()->sitestore()->getPriceWithCurrency($price, $priceOnly, $search);
    }

    //FUNCTION FOR ADMIN SIDE ONLY
    public function getPriceWithCurrencyAdmin($price) { 
 
        if (empty($price)) {
          return $price;
        } 
          $defaultParams = array();
          $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (empty($viewer_id)) {
          $defaultParams['locale'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'auto');
        } 
          $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
          $defaultParams['precision'] = 2;
          $price = (float) $price;
          $priceStr = Zend_Registry::get('Zend_View')->locale()->toCurrency($price, $currency, $defaultParams); 

          return $priceStr;
    }

    public function getConfigurationPrice($product_id, $fetch_col, $fieldArray, $quantity = null, $var = null) {
        if (empty($product_id) || empty($fieldArray) || empty($fetch_col))
            return;

        $combination_attribute_ids = array();
        $configuration_price = 0;
        $error = false;
        $formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        $quantity_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.quantity', 0);
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        foreach ($fieldArray as $field_key => $value) {

            if (!empty($value)) {
                // IN CASE OF LOGGED-OUT USER
                if (is_array($value)) {
                    $config_qunatity = $value['quantity'];
                    foreach ($value as $key => $singleValue) {

                        // CONTINUE IF KEY IS NOT FOR CONFIGURATION
                        if (empty($viewer_id) && (!isset($var) && empty($var))) {
                            $parts = explode('_', $key);
                            if (count($parts) != 3 && $parts[0] != 'select')
                                continue;
                        }

                        if (is_array($singleValue)) {
                            foreach ($singleValue as $option_id) {
                                $formOptionSelect = $formOptionTable->select()->from($formOptionTable->info('name'), $fetch_col)->where('option_id =?', $option_id);
                                $optionData = $formOptionTable->fetchRow($formOptionSelect);

                                if ($optionData) {
                                    if (!empty($quantity) && !empty($quantity_check)) {
                                        if (empty($optionData->quantity_unlimited) && empty($optionData->quantity))
                                            $error = Zend_Registry::get('Zend_Translate')->_("The selected configuration is currently not available for purchase.");
                                        elseif (empty($optionData->quantity_unlimited) && $config_qunatity > $optionData->quantity) {
                                            if ($optionData->quantity == 1)
                                                $error = Zend_Registry::get('Zend_Translate')->_("Only 1 quantity of the selected configuration is available in stock. Please enter the quantity as 1.");
                                            else
                                                $error = Zend_Registry::get('Zend_Translate')->_("Only %s quantities of the selected configuration are available in stock. Please enter the quantity less than or equal to %s.", $optionData->quantity, $optionData->quantity);
                                        }
                                    }else {
                                        if ($optionData) {
                                            if (!empty($optionData->price_increment))
                                                $configuration_price += $optionData->price;
                                            else
                                                $configuration_price -= $optionData->price;
                                        }
                                    }
                                }
                            }
                        }
                        else {
                            if (count($parts) == 2 && $parts[0] == 'select') {
                                if (empty($quantity)) {
                                    $formOptionSelect = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), array('price', 'price_increment'))
                                            ->where('field_id =?', $parts[1])
                                            ->where('combination_attribute_id =?', $singleValue)
                                            ->where('product_id =?', $product_id);
                                    $optionData = $combinationAttributesTable->fetchRow($formOptionSelect);
                                } else {
                                    $attribute_id = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')->where('field_id =?', $parts[1])->where('combination_attribute_id =?', $singleValue)->where('product_id =?', $product_id)->query()->fetchColumn();
                                    $combination_attribute_ids[] = $attribute_id;
                                }
                            } else {

                                $formOptionSelect = $formOptionTable->select()->from($formOptionTable->info('name'), $fetch_col)->where('option_id =?', $singleValue);
                                $optionData = $formOptionTable->fetchRow($formOptionSelect);
                            }

                            if (!empty($optionData) || !empty($combination_attribute_ids)) {
                                $quantity = $config_qunatity;
                                if (isset($quantity) && !empty($quantity) && !empty($quantity_check)) {
                                    if (empty($optionData->quantity_unlimited) && empty($optionData->quantity))
                                        $error = Zend_Registry::get('Zend_Translate')->_("The selected configuration is currently not available for purchase.");
                                    elseif (empty($optionData->quantity_unlimited) && $config_qunatity > $optionData->quantity) {
                                        if ($optionData->quantity == 1)
                                            $error = Zend_Registry::get('Zend_Translate')->_("Only 1 quantity of the selected configuration is available in stock. Please enter the quantity as 1.");
                                        else
                                            $error = sprintf(Zend_Registry::get('Zend_Translate')->_("Only %s quantities of the selected configuration are available in stock. Please enter the quantity less than or equal to %s."), $optionData->quantity, $optionData->quantity);
                                    }
                                }
                                elseif (empty($quantity) && !empty($optionData)) {
                                    if (!empty($optionData->price_increment))
                                        $configuration_price += $optionData->price;
                                    else
                                        $configuration_price -= $optionData->price;
                                }
                            }
                        }
                    }
                }
                else {
                    if (!empty($viewer_id)) {
                        if (!empty($value->category_attribute)) {
                            if (empty($quantity)) {
                                $formOptionSelect = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), array('price', 'price_increment'))
                                        ->where('field_id =?', $value->field_id)
                                        ->where('combination_attribute_id =?', $value->value)
                                        ->where('product_id =?', $product_id);
                                $optionData = $combinationAttributesTable->fetchRow($formOptionSelect);
                            } else {
                                $attribute_id = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')->where('field_id =?', $value->field_id)->where('combination_attribute_id =?', $value->value)->where('product_id =?', $product_id)->query()->fetchColumn();
                                $combination_attribute_ids[] = $attribute_id;
                            }
                        } else {
                            $formOptionSelect = $formOptionTable->select()->from($formOptionTable->info('name'), $fetch_col)->where('option_id =?', $value->value);
                            $optionData = $formOptionTable->fetchRow($formOptionSelect);
                        }
                    } else {
                        $parts = @explode('_', $field_key);
                        if (count($parts) == 2 && $parts[0] == 'select') {
                            if (empty($quantity)) {
                                $formOptionSelect = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), array('price', 'price_increment'))
                                        ->where('field_id =?', $parts[1])
                                        ->where('combination_attribute_id =?', $value)
                                        ->where('product_id =?', $product_id);
                                $optionData = $combinationAttributesTable->fetchRow($formOptionSelect);
                            } else {
                                $attribute_id = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')->where('field_id =?', $parts[1])->where('combination_attribute_id =?', $value)->where('product_id =?', $product_id)->query()->fetchColumn();
                                $combination_attribute_ids[] = $attribute_id;
                            }
                        } else {
                            $formOptionSelect = $formOptionTable->select()->from($formOptionTable->info('name'), $fetch_col)->where('option_id =?', $value);
                            $optionData = $formOptionTable->fetchRow($formOptionSelect);
                        }
                    }

                    if (!empty($optionData) || !empty($combination_attribute_ids)) {
                        if (isset($quantity) && !empty($quantity) && !empty($quantity_check)) {
                            if (!empty($optionData)) {
                                if (empty($optionData->quantity_unlimited) && empty($optionData->quantity))
                                    $error = Zend_Registry::get('Zend_Translate')->_("The selected configuration is currently not available for purchase.");
                                elseif (empty($optionData->quantity_unlimited) && $quantity > $optionData->quantity) {
                                    if ($optionData->quantity == 1)
                                        $error = Zend_Registry::get('Zend_Translate')->_("Only 1 quantity of the selected configuration is available in stock. Please enter the quantity as 1.");
                                    else
                                        $error = sprintf(Zend_Registry::get('Zend_Translate')->_("Only %s quantities of the selected configuration are available in stock. Please enter the quantity less than or equal to %s.", $optionData->quantity, $optionData->quantity));
                                }
                            }
                        }
                        elseif (!empty($optionData) && empty($quantity)) {
                            if (!empty($optionData->price_increment))
                                $configuration_price += $optionData->price;
                            else
                                $configuration_price -= $optionData->price;
                        }
                    }
                }
            }
            if (empty($viewer_id)) {
                if (!empty($quantity) && count($combination_attribute_ids) != 0) {
                    $combination_quantity = Engine_Api::_()->sitestoreproduct()->getCombinationQuantity($combination_attribute_ids);
                    if (empty($combination_quantity))
                        $error = Zend_Registry::get('Zend_Translate')->_("The selected configuration is currently not available for purchase.");
                    elseif ($combination_quantity < $quantity)
                        $error = sprintf(Zend_Registry::get('Zend_Translate')->_("Only %s quantities of the selected configuration are available in stock. Please enter the quantity less than or equal to %s."), $combination_quantity, $combination_quantity);
                }
            }
        }

        if (!empty($viewer_id)) {
            if (!empty($quantity) && count($combination_attribute_ids) != 0) {
                $combination_quantity = Engine_Api::_()->sitestoreproduct()->getCombinationQuantity($combination_attribute_ids);
                if (empty($combination_quantity))
                    $error = Zend_Registry::get('Zend_Translate')->_("The selected configuration is currently not available for purchase.");
                elseif ($combination_quantity < $quantity)
                    $error = sprintf(Zend_Registry::get('Zend_Translate')->_("Only %s quantities of the selected configuration are available in stock. Please enter the quantity less than or equal to %s."), $combination_quantity, $combination_quantity);
            }
        }


        if (!empty($error) && empty($configuration_price))
            return $error;
        elseif (empty($error) && !empty($configuration_price))
            return $configuration_price;

        return;
    }

    public function getConfigurationDetails($fieldArray, $product_id) {
        if (empty($fieldArray))
            return;

        $fields_info = array();
        $combination_attribute_ids = array();
        $formOptionTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'options');
        $formMetaTable = Engine_Api::_()->fields()->getTable('sitestoreproduct_cartproduct', 'meta');
        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        foreach ($fieldArray as $key => $value) {
            $optionData = '';
            if (empty($viewer_id)) {
                $parts = @explode('_', $key);
                if (count($parts) != 3 && $parts[0] != 'select')
                    continue;
            }
            if (is_array($value)) {
                $optionData = '';
                foreach ($value as $option_id) {
                    $formOptionSelect = $formOptionTable->select()->from($formOptionTable->info('name'), array('price', 'price_increment', 'quantity', 'quantity_unlimited'))->where('option_id =?', $option_id);

                    $label = $formMetaTable->select()->from($formMetaTable->info('name'), 'label')->where('field_id =?', $parts[2])->query()->fetchColumn();

                    $optionData = $formOptionTable->fetchRow($formOptionSelect);

                    if (!empty($optionData->price_increment))
                        $optionData->price = '+' . $optionData->price;
                    else
                        $optionData->price = '-' . $optionData->price;

                    $fields_info[$option_id] = array('price' => $optionData->price, 'qty' => $optionData->quantity, 'qty_unlimited' => $optionData->quantity_unlimited, 'label' => $label);
                }
            }
            else {
                if (!empty($viewer_id)) {
                    if (!empty($value->category_attribute)) {
                        $attribute_id = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')->where('field_id =?', $value->field_id)->where('combination_attribute_id =?', $value->value)->where('product_id =?', $product_id)->query()->fetchColumn();
                        $combination_attribute_ids[] = $attribute_id;
                    } else {
                        $formOptionSelect = $formOptionTable->select()->from($formOptionTable->info('name'), array('price', 'price_increment', 'quantity', 'quantity_unlimited'))->where('option_id =?', $value->value);

                        $label = $formMetaTable->select()->from($formMetaTable->info('name'), 'label')->where('field_id =?', $value->field_id)->query()->fetchColumn();
                        $optionData = $formOptionTable->fetchRow($formOptionSelect);
                    }
                } else {
                    if (count($parts) == 2 && $parts[0] == 'select') {
                        $attribute_id = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')->where('field_id =?', $parts[1])->where('product_id =?', $product_id)->where('combination_attribute_id =?', $value)->query()->fetchColumn();
                        $combination_attribute_ids[] = $attribute_id;
                    } else {
                        $formOptionSelect = $formOptionTable->select()->from($formOptionTable->info('name'), array('price', 'price_increment', 'quantity', 'quantity_unlimited'))->where('option_id =?', $value);

                        $label = $formMetaTable->select()->from($formMetaTable->info('name'), 'label')->where('field_id =?', $parts[2])->query()->fetchColumn();
                        $optionData = $formOptionTable->fetchRow($formOptionSelect);
                    }
                }

                if (!empty($optionData)) {
                    if (!empty($optionData->price_increment))
                        $optionData->price = '+' . $optionData->price;
                    else
                        $optionData->price = '-' . $optionData->price;

                    if (!empty($viewer_id))
                        $fields_info[$value->value] = array('price' => $optionData->price, 'qty' => $optionData->quantity, 'qty_unlimited' => $optionData->quantity_unlimited, 'label' => $label);
                    else
                        $fields_info[$value] = array('price' => $optionData->price, 'qty' => $optionData->quantity, 'qty_unlimited' => $optionData->quantity_unlimited, 'label' => $label);
                }
            }
        }
        if (count($combination_attribute_ids)) {
            $combinationQuantity = Engine_Api::_()->sitestoreproduct()->getCombinationQuantity($combination_attribute_ids, 1);
            if (count($combinationQuantity)) {
                foreach ($combinationQuantity as $combination_id => $quantity) {
                    $fields_info[$combination_id] = array('combination_quantity' => $quantity);
                }
            }
        }
        return $fields_info;
    }

    public function getProductCategoryFields($productObj) {
        if ((!$productObj) || empty($productObj->category_id))
            return;

        $parentCategoryDropDown = $subCategoryDropDown = $subSubCategoryDropDown = array();

        $category = Engine_Api::_()->getItem('sitestoreproduct_category', $productObj->category_id);

        if (empty($category))
            return;

        if (!empty($category->profile_type)) {
            $parentCategoryDropDown = $this->getCategoryFields($category->profile_type);
        }

        if (!empty($productObj->subcategory_id)) {
            $category = Engine_Api::_()->getItem('sitestoreproduct_category', $productObj->subcategory_id);
            if (!empty($category->profile_type))
                $subCategoryDropDown = $this->getCategoryFields($category->profile_type);
        }

        if (!empty($productObj->subsubcategory_id)) {
            $category = Engine_Api::_()->getItem('sitestoreproduct_category', $productObj->subsubcategory_id);
            if (!empty($category->profile_type))
                $subSubCategoryDropDown = $this->getCategoryFields($category->profile_type);
        }

        return array_merge($parentCategoryDropDown, $subCategoryDropDown, $subSubCategoryDropDown);
    }

    public function getCategoryFields($category_profile_id) {
        $dropDownFields = array();
        $fieldMetaTable = Engine_Api::_()->getDbTable('metas', 'sitestoreproduct');
        $fieldOptionsTable = Engine_Api::_()->getDbTable('options', 'sitestoreproduct');
        foreach ($fieldMetaTable->getProfileFields($category_profile_id) as $field_id => $field) {
            if ($field['type'] == 'select') {
                $fieldOptions = $fieldOptionsTable->getOptions($field_id);
                if (!empty($fieldOptions)) {
                    $field['multioptions'] = $fieldOptions;
                    $dropDownFields[$field_id] = $field;
                }
            }
        }
        return $dropDownFields;
    }

    public function getCombinationOptions($product_id, $productProfile = null, $isUserEnd = true) {

        if (!$product_id)
            return;

        $attributeOptions = array();
        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('combinationAttributeMap', 'sitestoreproduct');
        $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');
        $combinationAttributesTableName = $combinationAttributesTable->info('name');
        $combinationAttributeMapsTableName = $combinationAttributeMapsTable->info('name');
        $combinationTableName = $combinationsTable->info('name');

        if (empty($productProfile)) {
            $combinationOptions = $combinationAttributesTable->select()
                            ->where('product_id =?', $product_id)
                            ->order('order ASC')
                            ->query()->fetchAll();
        } else {

            $combinationOptions = $combinationAttributesTable->select()
                            ->setIntegrityCheck(false)
                            ->from($combinationAttributesTableName)
                            ->join($combinationAttributeMapsTableName, "$combinationAttributesTableName.attribute_id = $combinationAttributeMapsTableName.attribute_id")
                            ->join($combinationTableName, "$combinationAttributeMapsTableName.combination_id = $combinationTableName.combination_id")
                            ->where("$combinationAttributesTableName.product_id =?", $product_id)
                            ->where("$combinationTableName.status =?", 1)
                            ->where("$combinationTableName.quantity > ?", 0)
                            ->order("$combinationAttributesTableName.order ASC")
                            ->query()->fetchAll();
        }

        if (empty($combinationOptions))
            return;
        $count = count($combinationOptions);

        foreach ($combinationOptions as $option) {

            //WORK FOR THE VAT IN CONFIGURABLE PRODUCT
            if ($isUserEnd) {
                $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
                $productPricesArray = $this->getPriceOfProductsAfterVAT($product_obj);

                if (!empty($productPricesArray) && isset($productPricesArray['vatShowType'])) {
                    if (isset($productPricesArray['show_price_with_vat']) && !empty($productPricesArray['show_price_with_vat']) && isset($productPricesArray['save_price_with_vat']) && empty($productPricesArray['save_price_with_vat'])) {
                        $vat = explode("%", $productPricesArray['vatShowType']);
                        $option['price'] = @round(($option['price'] * 100) / (100 + $vat[0]), 2);
                    }


                    if (isset($productPricesArray['show_price_with_vat']) && empty($productPricesArray['show_price_with_vat']) && isset($productPricesArray['save_price_with_vat']) && !empty($productPricesArray['save_price_with_vat'])) {
                        $vat = explode("%", $productPricesArray['vatShowType']);
                        $option['price'] = @round(((($option['price'] * $vat[0]) / 100) + ($option['price'])), 2);
                    }
                }
            }
            //WORK FOR THE VAT IN CONFIGURABLE PRODUCT

            if (!empty($productProfile)) {
                $attributeOptions[$option['field_id']]['multioptions'][0] = '-- Please Select --';
                $attributeOptions[$option['field_id']]['label'] = Engine_Api::_()->getDbTable('cartproductFieldMeta', 'sitestoreproduct')->getFieldLabel($option['field_id']);

                if (!empty($option['price_increment']))
                    $option_label = Engine_Api::_()->getDbTable('cartproductFieldOptions', 'sitestoreproduct')->getOptionLabel($option['field_id'], $option['combination_attribute_id']) . '   ' . '(+' . $option['price'] . ')';
                else
                    $option_label = Engine_Api::_()->getDbTable('cartproductFieldOptions', 'sitestoreproduct')->getOptionLabel($option['field_id'], $option['combination_attribute_id']) . '   ' . '(-' . $option['price'] . ')';

                $attributeOptions[$option['field_id']]['multioptions'][$option['combination_attribute_id']] = $option_label;
                $attributeOptions[$option['field_id']]['price_array'][$option['combination_attribute_id']] = !empty($option['price_increment']) ? '+' . $option['price'] : '-' . $option['price'];
                $attributeOptions[$option['field_id']]['order'] = $option['order'];
                $attributeOptions[$option['field_id']]['max_order'] = $combinationOptions[$count - 1]['order'];
            }else {
                $attributeOptions[$option['field_id']][] = array('option_id' => $option['combination_attribute_id'], 'price' => $option['price'], 'price_inc' => $option['price_increment']);
            }
        }

        return $attributeOptions;
    }

    public function getCombinations($product_id) {

        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('combinationAttributeMap', 'sitestoreproduct');
        $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');

        $combinationAttributesTableName = $combinationAttributesTable->info('name');
        $combinationAttributeMapsTableName = $combinationAttributeMapsTable->info('name');
        $combinationTableName = $combinationsTable->info('name');
        $groupedCombinations = array();

        $productCombinations = $combinationsTable->select()
                        ->setIntegrityCheck(false)
                        ->from($combinationTableName, array('combination_id', 'status', 'quantity'))
                        ->join($combinationAttributeMapsTableName, "$combinationTableName.combination_id = $combinationAttributeMapsTableName.combination_id")
                        ->join($combinationAttributesTableName, "$combinationAttributesTableName.attribute_id = $combinationAttributeMapsTableName.attribute_id")
                        ->where("$combinationAttributesTableName.product_id =?", $product_id)
                        ->query()->fetchAll();

        $combination_id = 0;
        foreach ($productCombinations as $combination) {

            $attributes[Engine_Api::_()->getDbTable('cartproductFieldMeta', 'sitestoreproduct')->getFieldLabel($combination['field_id'])] = Engine_Api::_()->getDbTable('cartproductFieldOptions', 'sitestoreproduct')->getOptionLabel($combination['field_id'], $combination['combination_attribute_id']);
            if ($combination['combination_id'] != $combination_id) {
                $combination_id = $combination['combination_id'];
                $configurationName = Engine_Api::_()->getDbTable('cartproductFieldOptions', 'sitestoreproduct')->getOptionLabel($combination['field_id'], $combination['combination_attribute_id']);
                $price = !empty($combination['price_increment']) ? '+' . $combination['price'] : '-' . $combination['price'];
                $quantity = $combination['quantity'];
                $status = $combination['status'];
            } else {
                $configurationName .= ' ' . '-' . ' ' . Engine_Api::_()->getDbTable('cartproductFieldOptions', 'sitestoreproduct')->getOptionLabel($combination['field_id'], $combination['combination_attribute_id']);
                $price +=!empty($combination['price_increment']) ? '+' . $combination['price'] : '-' . $combination['price'];
            }
            $groupedCombinations[$combination_id] = array('name' => $configurationName, 'price' => $price, 'quantity' => $quantity, 'status' => $status, 'attributes' => $attributes);
        }

        return $groupedCombinations;
    }

    public function getCombinationQuantity($attributeIds, $returnArray = null) {

        if (empty($attributeIds))
            return;

        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('combinationAttributeMap', 'sitestoreproduct');
        $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');

        $combinationIds = $combinationAttributeMapsTable->select()->from($combinationAttributeMapsTable->info('name'), 'combination_id')->where("attribute_id IN (?)", (array) $attributeIds)
                        ->query()->fetchAll();
        $combinationCount = array();

        foreach ($combinationIds as $value) {
            if (isset($combinationCount[$value['combination_id']]))
                $combinationCount[$value['combination_id']] ++;
            else
                $combinationCount[$value['combination_id']] = 1;
        }
        foreach ($combinationCount as $combination_id => $count) {
            if ($count == count($attributeIds))
                $returnCombinationId = $combination_id;
        }
        if (!empty($returnCombinationId)) {
            $combinationQuantity = $combinationsTable->select()->from($combinationsTable->info('name'), 'quantity')
                            ->where('combination_id =?', $returnCombinationId)
                            ->query()->fetchColumn();
        }

        if (isset($returnArray) && !empty($returnArray)) {
            return array($returnCombinationId => $combinationQuantity);
        } else {
            return $combinationQuantity;
        }
    }

    public function deleteCombinations($fieldId) {

        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');
        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('combinationAttributeMap', 'sitestoreproduct');
        if (is_array($fieldId))
            $attribute_ids = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')
                            ->where("field_id IN (?)", (array) $fieldId)
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        else
            $attribute_ids = $combinationAttributesTable->select()->from($combinationAttributesTable->info('name'), 'attribute_id')
                            ->where('field_id =?', $fieldId)
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

        if (count($attribute_ids) != 0) {
            $combination_ids = $combinationAttributeMapsTable->select()->from($combinationAttributeMapsTable->info('name'), 'combination_id')
                            ->where("attribute_id IN (?)", (array) $attribute_ids)
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            if (!empty($combination_ids)) {
                $combinationAttributeMapsTable->delete(array('combination_id IN (?)' => (array) $combination_ids));
                $combinationsTable->delete(array('combination_id IN (?)' => (array) $combination_ids));
            }
            (is_array($fieldId)) ? $combinationAttributesTable->delete(array('field_id IN (?)' => $fieldId)) : $combinationAttributesTable->delete(array('field_id = ?' => $fieldId));
        }
    }

    public function getProductAttributes($productObj) {

        if ((!$productObj) || empty($productObj->product_id))
            return;

        $option_id = Engine_Api::_()->getDbTable('productfields', 'sitestoreproduct')->getOptionId($productObj->product_id);

        if (empty($option_id))
            return;

        $fieldMetaTable = Engine_Api::_()->getDbTable('cartproductFieldMeta', 'sitestoreproduct');
        $fieldOptionsTable = Engine_Api::_()->getDbTable('cartproductFieldOptions', 'sitestoreproduct');
        foreach ($fieldMetaTable->getProfileFields($option_id) as $field_id => $field) {
            $fieldOptions = $fieldOptionsTable->getOptions($field_id);
            if (!empty($fieldOptions)) {
                $field['multioptions'] = $fieldOptions;
                $dropDownFields[$field_id] = $field;
            }
        }
        return $dropDownFields;
    }

    public function getProductCombinationQuantity($product_id) {

        if (empty($product_id))
            return;

        $combinationAttributesTable = Engine_Api::_()->getDbTable('combinationAttributes', 'sitestoreproduct');
        $combinationAttributeMapsTable = Engine_Api::_()->getDbTable('combinationAttributeMap', 'sitestoreproduct');
        $combinationsTable = Engine_Api::_()->getDbTable('combinations', 'sitestoreproduct');

        $combinationAttributesTableName = $combinationAttributesTable->info('name');
        $combinationAttributeMapsTableName = $combinationAttributeMapsTable->info('name');
        $combinationTableName = $combinationsTable->info('name');

        $combinationsQuantity = $combinationsTable->select()
                        ->setIntegrityCheck(false)
                        ->from($combinationTableName, array('quantity', 'combination_id'))
                        ->join($combinationAttributeMapsTableName, "$combinationTableName.combination_id = $combinationAttributeMapsTableName.combination_id", array(''))
                        ->join($combinationAttributesTableName, "$combinationAttributesTableName.attribute_id = $combinationAttributeMapsTableName.attribute_id", array(''))
                        ->where("$combinationAttributesTableName.product_id =?", $product_id)
                        ->group("$combinationTableName.combination_id")
                        ->query()->fetchAll();

        $totalQuantity = 0;
        if (!empty($combinationsQuantity)) {
            foreach ($combinationsQuantity as $combination) {
                if (!empty($combination['combination_id']))
                    $totalQuantity += $combination['quantity'];
            }
        }

        return $totalQuantity;
    }

    public function enableLocation() {

        return Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.locationfield', 0);
    }

    public function getLanguages() {

        $multilanguage_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.multilanguage', 0);
        if (!empty($multilanguage_allow)) {

            $languages = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.languages', null);
            $total_allowed_languages = Count($languages);

            if ($total_allowed_languages > 1)
                return $languages;
            else
                return false;
        }else {
            return false;
        }
    }

    /**
     * Get Online Payment Threshold Amount
     * @param int $store_id
     * @return float
     */
    public function getIsAllowedNonSellingProductPrice($store_id) {
        $storeObj = Engine_Api::_()->getItem('sitestore_store', $store_id);

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.package.enable', 1)) {
            $packageObj = Engine_Api::_()->getItem('sitestore_package', $storeObj->package_id);
            if (!empty($packageObj->store_settings)) {
                $storeSettings = @unserialize($packageObj->store_settings);
                if (array_key_exists('allow_non_selling_product_price', $storeSettings)) {
                    return $storeSettings['allow_non_selling_product_price'];
                } else {
                    return 0;
                }
            }
        } else {
            $getIsAllowedAddToCart = Engine_Api::_()->authorization()->getPermission($storeObj->getOwner()->level_id, 'sitestore_store', "allow_non_selling_product_price");
            if (!empty($getIsAllowedAddToCart)) {
                return $getIsAllowedAddToCart;
            }
        }
        return 1;
    }

    /**
     * 	check & rotate image to correct orientation
     * 	@category	CTSTYLE-46
     * 	@return 	boolean
     */
    public function checkPhotoOrientation($file) {
        
         if( !function_exists('exif_read_data')) {
            return 0;
        }
        
        $exif = @exif_read_data($file);
        $angle = 0;
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 8 :
                    $angle = 90;
                    break;
                case 3 :
                    $angle = 180;
                    break;
                case 6 :
                    $angle = -90;
                    break;
            }
        };

        if ($angle != 0) {
            switch (@exif_imagetype($file)) {
                case IMAGETYPE_JPEG:
                    $im = imagecreatefromjpeg($file);
                    break;
                case IMAGETYPE_PNG:
                    $im = imagecreatefrompng($file);
                    break;
                default:
                    return false;
            }

            // Rotate
            $rotate = imagerotate($im, $angle, 0);

            // Output & Free the memory
            switch (@exif_imagetype($file)) {
                case IMAGETYPE_JPEG:
                    imagejpeg($rotate, $file);
                    imagedestroy($rotate);
                    return $angle;
                case IMAGETYPE_PNG:
                    imagepng($rotate, $file);
                    imagedestroy($rotate);
                    return $angle;
                default:
                    return false;
            }
        }

        return $angle;
    }
    
    public function getFBTotal() {
		$bridgeTable = Engine_Api::_()->getDbTable('bridges', 'sesbasic');
		$selectTable = $bridgeTable->select()
    	               ->from($bridgeTable->info('name'), array("SUM(buyer_bb) as total_full_bb","SUM(buyer_cb) as total_full_cb","SUM(buyer_db) as total_full_db", "creation_date"))
    	               ->where("DATE_FORMAT(creation_date,'%Y') >= ?", 2017) 
    	               ->where("DATE_FORMAT(creation_date,'%Y') <= ?", (date('Y'))) 
    	               ->where('buyer_user_id =?', Engine_Api::_()->user()->getViewer()->getIdentity())
    	               ->group("YEAR(creation_date)")
    	               ->group("MONTH(creation_date)");
    	$this->view->full_bridges = $bridgeTable->fetchAll($selectTable);
    	
    	$bArray = array();
    	foreach($this->view->full_bridges as $bridge) {
	        $bArray[] = array("total_full_bb"=>$bridge['total_full_bb'],"total_full_cb"=>$bridge['total_full_cb'],"total_full_db"=>$bridge['total_full_db'],"creation_date"=>$bridge['creation_date']);
    	}
    	
		foreach($bArray as $x => $value) {
			$previous_bb = $bArray[$x]['total_full_bb'];
			$previous_cb = $bArray[$x]['total_full_cb'];
			$previous_db = $bArray[$x]['total_full_db'];
			$creation_date = $bArray[$x]['creation_date'];
			
			$monthYear = date('m-Y', strtotime($creation_date));
			$dateMonth = date('Y-m', strtotime($creation_date));
			
			$valueRs = Engine_Api::_()->sescustomize()->getValue($monthYear);
			$bridges_value = $valueRs['value'];
			
			$Bank =  Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->expend(array('month'=>$dateMonth,'type'=>'bank'));
			$Redeem =  Engine_Api::_()->getDbtable('fbvalues', 'sescustomize')->expend(array('month'=>$dateMonth,'type'=>'redeem'));
		  
		  	$EB_value = 0;
			if($previous_bb > 0){
				//$EB_value = (($previous_bb*$bridges_value) + ($previous_cb*$bridges_value) + ($previous_db*$bridges_value));
				$FB_value = ($bridges_value*($previous_bb + $previous_cb + $previous_db))+$EB_value;
			}else{
				$FB_value = 0;
				$EB_value = 0;
			}
			
			$RD_value = ($Bank+$Redeem);
			$totalEarn = $totalEarn + ($FB_value - $RD_value);
		}

			return $totalEarn;
    }

}
