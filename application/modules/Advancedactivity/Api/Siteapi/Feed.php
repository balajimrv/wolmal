<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Feed.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_Api_Siteapi_Feed extends Core_Api_Abstract {

    /**
     * Make an array for activity feeds
     *
     * @return array
     */
    public function getFeeds($actions = null, array $data = array()) {
        if (null == $actions || (!is_array($actions) && !($actions instanceof Zend_Db_Table_Rowset_Abstract)))
            return '';

        $allowEdit = 0;
        $activity_moderate = "";
        $privacyDropdownList = null;
        $is_owner = $add_saved_feed = $allowEditCategory = false;
        $viewer = Engine_Api::_()->user()->getViewer();

        if ($viewer->getIdentity()) {
            $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
            if (Engine_Api::_()->core()->hasSubject() && $viewer->isSelf(Engine_Api::_()->core()->getSubject())) {
                $allowEdit = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.post.canedit', 1);
//        if ( $allowEdit )
//          $privacyDropdownList = $this->getPrivacyDropdownList();

                if (Engine_Api::_()->hasModuleBootstrap('advancedactivitypost')) {
                    $tableCategories = Engine_Api::_()->getDbtable('categories', 'advancedactivitypost');
                    $categoriesList = $tableCategories->getCategories();
                    $allowEditCategory = count($categoriesList);
                }
            }

            if (!Engine_Api::_()->core()->hasSubject()) {
                $add_saved_feed_row = Engine_Api::_()->getDbtable('contents', 'advancedactivity')->getContentList(array('content_tab' => 1, 'filter_type' => 'user_saved'));
                $add_saved_feed = !empty($add_saved_feed_row) ? true : false;
            } else {
                if (Engine_Api::_()->core()->hasSubject())
                    $subject = Engine_Api::_()->core()->getSubject();

                if (empty($subject))
                    return;

                if ($subject->getType() == 'siteevent_event' && ($subject->getParent()->getType() == 'sitepage_page' || $subject->getParent()->getType() == 'sitbusiness_business' || $subject->getParent()->getType() == 'sitegroup_group' || $subject->getParent()->getType() == 'sitestore_store')) {
                    $subject = Engine_Api::_()->getItem($subject->getParent()->getType(), $subject->getParent()->getIdentity());
                }
                switch ($subject->getType()) {
                    case 'user':
                        $is_owner = $viewer->isSelf($subject);
                        break;
                    case 'sitepage_page':
                    case 'sitebusiness_business':
                    case 'sitegroup_group':
                    case 'sitestore_store':
                        $is_owner = $subject->isOwner($viewer);
                        break;
                    case 'sitepageevent_event':
                    case 'sitebusinessevent_event':
                    case 'sitegroupevent_event':
                    case 'sitestorevent_event':
                        $is_owner = $viewer->isSelf($subject);
                        if (empty($is_owner)) {
                            $is_owner = $subject->getParent()->isOwner($viewer);
                        }
                        break;
                    default :
                        $is_owner = $viewer->isSelf($subject->getOwner());
                        break;
                }
            }
        }

        $composerOptions = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("emotions", "withtags"));

        // Prepare response
        $data = array_merge($data, array(
            'actions' => $actions,
            'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
            'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
            'commentShowBottomPost' => Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.comment.show.bottom.post', 1),
            'isMobile' => 1, //Engine_Api::_()->advancedactivity()->isMobile(),
            'activity_moderate' => $activity_moderate,
            'allowEdit' => $allowEdit,
            'allowEditCategory' => $allowEditCategory,
            'privacyDropdownList' => $privacyDropdownList,
            'allowEmotionsIcon' => in_array("emotions", $composerOptions),
            'allowSaveFeed' => $add_saved_feed,
            'is_owner' => $is_owner,
            'showLargePhoto' => Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.largephoto.enable', 1)
        ));
        $_activityText = $this->_activityText($data);
        return $_activityText;
    }

    /**
     * Get a helper
     * 
     * @param string $name
     * @return Activity_Model_Helper_Abstract
     */
    public function getHelper($name) {
        $name = $this->_normalizeHelperName($name);
        if (!isset($this->_helpers[$name])) {
            $helper = $this->getPluginLoader()->load($name);
            $this->_helpers[$name] = new $helper;
        }

        return $this->_helpers[$name];
    }

    protected $_flagBodyIndex;

    /**
     * Activity template parsing
     * 
     * @param string $body
     * @param array $params
     * @return string
     */
    public function assemble($body, array $params = array()) {
        $body = $this->getHelper('translate')->direct($body);

        // By pass for un supported modules.
//        $getDefaultAPPModules = DEFAULT_APP_MODULES;
//        if (!empty($getDefaultAPPModules)) {
//            $getDefaultAPPModuleArray = @explode(",", DEFAULT_APP_MODULES);
//            if (!empty($params['object']) && is_object($params['object'])) {
//                $moduleName = $params['object']->getModuleName();
//                $moduleName = strtolower($moduleName);
//                if (!in_array($moduleName, $getDefaultAPPModuleArray))
//                    return $body;
//            }
//        }
        // Do other stuff
        preg_match_all('~\{([^{}]+)\}~', $body, $matches, PREG_SET_ORDER);
        $this->_flagBodyIndex = 0;
        $feedParams = array();
        $isBodyParamSet = null;

        foreach ($matches as $match) {
            $tag = $match[0];
            $args = explode(':', $match[1]);
            $helper = array_shift($args);

            $tempParams = $helperArgs = array();
            foreach ($args as $arg) {
                if (substr($arg, 0, 1) === '$') {
                    $arg = substr($arg, 1);
                    $helperArgs[] = ( isset($params[$arg]) ? $params[$arg] : null );
                } else {
                    $helperArgs[] = $arg;
                }
            }

            if ($tag == '{item:$listing}' && is_array($helperArgs) && $helperArgs[0][0] == 'sitereview_listing') {
                $helperArgs[0] = Engine_Api::_()->getItem($helperArgs[0][0], $helperArgs[0][1]);
            }

            if ($tag == '{body:$body}') {
                $this->_idBodyContentAvailable = true;
                $action = $params['actionObj'];
                $getAttachment = $action->getFirstAttachment();
                foreach ($getAttachment as $attachment) {
                    if (isset($attachment->type) && isset($attachment->id)) {
                        $getObj = Engine_Api::_()->getItem($attachment->type, $attachment->id);

                        if (isset($getObj->body)) {
                            $tempBodyArray['search'] = $tag;
                            $tempBodyArray['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($getObj->body);

                            if (isset($tempBodyArray['label']))
                                $tempBodyArray['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate(@trim($tempBodyArray['label']));

                            if (isset($this->_flagBodyIndex) && !empty($this->_flagBodyIndex))
                                $feedParams[$this->_flagBodyIndex] = $tempBodyArray;
                            else
                                $feedParams[] = $tempBodyArray;

                            continue;
                        }
                    }else {
                        $tempBodyArray['search'] = $tag;
                        $tempBodyArray['label'] = "";
                        $getElementArray = array_keys($feedParams);
                        $this->_flagBodyIndex = end($getElementArray);
                        $feedParams[++$this->_flagBodyIndex] = $tempBodyArray;
                        continue;
                    }
                }
            }

            if (isset($params['flag']) && !empty($params['flag'])) { // Make a feed type body params for dynamic Feed Title                               
                if (isset($helperArgs[0]) && !empty($helperArgs[0])) {
                    if (strstr($tag, 'siteEvent')) {
                        $tag = str_replace("siteEvent", "siteevent", $tag);
                    }

                    if (strstr($tag, '{itemSeaoChild:$object:siteevent_diary:$child_id}')) {
                        $tag = str_replace("siteevent", "siteEvent", $tag);
                    }

                    if (is_object($helperArgs[0])) {
                        $tempParams['search'] = $tag;
                        $tempParams['label'] = (isset($helperArgs[1]) && !empty($helperArgs[1]) && is_string($helperArgs[1]) && ($tag != '{item:$object:topic}')) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($helperArgs[1]) : Engine_Api::_()->getApi('Core', 'siteapi')->translate($helperArgs[0]->getTitle());

                        // @Todo: Bypass in case of this Advanced Event. We will make it, whenever work on Adv Events API.
                        if (($tag == '{itemSeaoChild:$object:siteEvent_topic:$child_id}') || ($tag == '{itemSeaoChild:$object:siteevent_topic:$child_id}'))
                            $tempParams['label'] = '';

                        $tempParams['type'] = $helperArgs[0]->getType();
                        $tempParams['id'] = $helperArgs[0]->getIdentity();
                        if ((strstr($tag, 'siteEvent_diary')) || (strstr($tag, 'siteevent_diary'))) {
                            $tempParams['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate(str_replace("siteEvent_diary", "", $tempParams['label']));
                        }
                        if ($tag == '{item:$object:topic}') {
                            $tempParams['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('topic');
                            $tempParams['slug'] = $helperArgs[0]->getSlug();
                        }

                        if ($tag == '{itemParent:$object:forum}') {
                            $tempParams['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($helperArgs[0]->getParent()->getTitle());
                            $tempParams['type'] = $helperArgs[0]->getParent()->getType();
                            $tempParams['id'] = $helperArgs[0]->getParent()->getIdentity();
                            $tempParams['slug'] = $helperArgs[0]->getParent()->getSlug();
                        }

                        // Add URL in case, if feed not related to app modules. So that we can open webview for that feed.
                        if (!empty($helperArgs[0])) {
                            $getFeedBodyParamURL = $this->addURLInFeedBodyParam($helperArgs[0]);
                            if (!empty($getFeedBodyParamURL))
                                $tempParams['url'] = $getFeedBodyParamURL;
                        }

                        if (isset($helperArgs[1]) && is_object($helperArgs[1]) && strstr($tag, '{actors:$subject:$object}')) {
                            $tempParams['object']['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($helperArgs[1]->getTitle());
                            $tempParams['object']['type'] = $helperArgs[1]->getType();
                            $tempParams['object']['id'] = $helperArgs[1]->getIdentity();

                            // Add URL in case, if feed not related to app modules. So that we can open webview for that feed.
                            if (!empty($helperArgs[1])) {
                                $getFeedBodyParamURL = $this->addURLInFeedBodyParam($helperArgs[1]);
                                if (!empty($getFeedBodyParamURL))
                                    $tempParams['object']['url'] = $getFeedBodyParamURL;
                            }
                        }
                    } else {
                        $tempParams['search'] = $tag;
                        $tempParams['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate(preg_replace('/<\/?a[^>]*>/', '', $helperArgs[0]));

                        // In case of GUID, create object and send respective array to client.
                        if (isset($helperArgs[0]) && !empty($helperArgs[0]) && is_string($helperArgs[0]) && strstr($helperArgs[0], '_')) {
                            $explodeItemTypes = @explode("_", $helperArgs[0]);
                            $id = @end($explodeItemTypes);
                            array_pop($explodeItemTypes);
                            $type = @implode("_", $explodeItemTypes);
                            if (!empty($type) && !empty($id)) {
                                try {
                                    $getObj = Engine_Api::_()->getItem($type, $id);
                                    if (!empty($getObj)) {
                                        $tempParams['search'] = $tag;
                                        $tempParams['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($getObj->getTitle());
                                        $tempParams['type'] = $getObj->getType();
                                        $tempParams['id'] = $getObj->getIdentity();

                                        // Add URL in case, if feed not related to app modules. So that we can open webview for
                                        $getFeedBodyParamURL = $this->addURLInFeedBodyParam($getObj);
                                        if (!empty($getFeedBodyParamURL))
                                            $tempParams['url'] = $getFeedBodyParamURL;
                                    }
                                } catch (Exception $ex) {
                                    // Blank Exception
                                }
                            }
                        }
                    }

                    if (isset($tempParams['label']))
                        $tempParams['label'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate(@trim($tempParams['label']));

                    // @Todo: Need to remove in future.
                    if (isset($tempParams['search']) && $tempParams['search'] == '{itemChild:$object:siteFicha_album:$child_id}')
                        $tempParams['label'] = '';

                    if (isset($tempParams['search']) && !empty($tempParams['search']))
                        $tempParams['search'] = @strtolower($tempParams['search']);

                    $feedParams[] = $tempParams;
                }
            } else { // Make a Feed Title
                try {
                    $helper = $this->getHelper($helper);
                    $r = new ReflectionMethod($helper, 'direct');
                    $content = $r->invokeArgs($helper, $helperArgs);
                    $content = preg_replace('/\$(\d)/', '\\\\$\1', $content);
                    $body = preg_replace("/" . preg_quote($tag) . "/", $content, $body, 1);
                } catch (Exception $ex) {
                    return $body;
                }
            }
        }

        if (isset($params['flag']) && !empty($params['flag'])) {
            return $feedParams;
        } else {
            $body = strip_tags($body);
            return $body;
        }
    }

    /*
     * Get the URL of content for the modules.
     * 
     * @param $obj content object
     * @return string OR false
     */

    private function addURLInFeedBodyParam($obj) {
        try {
            if (!empty($obj)) {
                $tempHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
                $getModuleName = $obj->getModuleName();
                $getModuleName = (!empty($getModuleName)) ? strtolower($getModuleName) : '';
                $defaultModules = @explode(",", DEFAULT_APP_MODULES);
                if (!in_array($getModuleName, $defaultModules) && $obj->getHref()) {
                    $getHref = $obj->getHref();
                    return (!strstr($getHref, 'http')) ? $tempHost . $obj->getHref() : $obj->getHref();
                }
            }
        } catch (Exception $ex) {
            // blank exception
        }

        return;
    }

    /**
     * Prepare activity feeds array
     *
     * @return array
     */
    private function _activityText($data) {
        $sharesTable = Engine_Api::_()->getDbtable('shares', 'advancedactivity');
        if (empty($data['actions']))
            return "The action you are looking for does not exist.";
        $actions = $data['actions'];
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.static.baseurl', null);
        $tempHost = $serverHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();

        $getDefaultStorageId = Engine_Api::_()->getDbtable('services', 'storage')->getDefaultServiceIdentity();
        $getDefaultStorageType = Engine_Api::_()->getDbtable('services', 'storage')->getService($getDefaultStorageId)->getType();
        $getHost = $getPhotoHost = '';
        if ($getDefaultStorageType == 'local')
            $getPhotoHost = $getHost = !empty($staticBaseUrl) ? $staticBaseUrl : $serverHost;

        $advancedactivityCoreApi = Engine_Api::_()->advancedactivity();
        $advancedactivitySaveFeed = Engine_Api::_()->getDbtable('saveFeeds', 'advancedactivity');
        if (Engine_Api::_()->core()->hasSubject())
            $getSubject = Engine_Api::_()->core()->getSubject();

        // Manage feeds
        foreach ($actions as $action) {
            try { // prevents a bad feed item from destroying the entire page
                // Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
                if (!$action->getTypeInfo()->enabled)
                    continue;
                if (!$action->getSubject() || !$action->getSubject()->getIdentity())
                    continue;
                if (!$action->getObject() || !$action->getObject()->getIdentity())
                    continue;

                try {
                    $objectUrl = $action->getObject()->getHref();
                } catch (Exception $ex) {
                    continue;
                }


                $activityMenu = $activityFooterMenus = $activityFeedArray = array();
                $getFeedTypeInfo = $action->getTypeInfo()->toArray();

                $item = $itemPhoto = (isset($action->getTypeInfo()->is_object_thumb) && !empty($action->getTypeInfo()->is_object_thumb)) ? $action->getObject() : $action->getSubject();

                $itemPhoto = (isset($action->getTypeInfo()->is_object_thumb) && $action->getTypeInfo()->is_object_thumb === 2) ? $action->getObject()->getParent() : $itemPhoto;

                // Prepare the feed array
                $activityFeedArray['feed'] = $action->toArray();

                // hashtag work
                if (Engine_Api::_()->hasModuleBootstrap('sitehashtag')) {
                    $hashtags = $this->getHashtagNames($action);
                    if (!empty($hashtags))
                        $activityFeedArray['hashtags'] = $hashtags;
                }

                $activityFeedArray['feed']['time_value'] = $action->getTimeValue();

                // Set feed subject information on request
                if (isset($data['subject_info']) && !empty($data['subject_info'])) {
                    $activityFeedArray['feed']['subject'] = $action->getSubject()->toArray();
                    $getSubjectModName = $action->getSubject()->getModuleName();
                    $activityFeedArray['feed']['subject']['name'] = (!empty($getSubjectModName)) ? strtolower($getSubjectModName) : '';
                    $activityFeedArray['feed']['subject']["url"] = $tempHost . $action->getSubject()->getHref();

                    if ((strstr($action->getSubject()->getPhotoUrl, 'http://')) || (strstr($action->getSubject()->getPhotoUrl, 'http://'))) {
                        $getHost = '';
                    }

                    $activityFeedArray['feed']['subject']["image"] = ($action->getSubject()->getPhotoUrl('thumb.main')) ? $getHost . $action->getSubject()->getPhotoUrl('thumb.main') : '';
                    $activityFeedArray['feed']['subject']["image_icon"] = ($action->getSubject()->getPhotoUrl('thumb.icon')) ? $getHost . $action->getSubject()->getPhotoUrl('thumb.icon') : '';
                    $activityFeedArray['feed']['subject']["image_profile"] = ($action->getSubject()->getPhotoUrl('thumb.profile')) ? $getHost . $action->getSubject()->getPhotoUrl('thumb.profile') : '';
                    $activityFeedArray['feed']['subject']["image_normal"] = ($action->getSubject()->getPhotoUrl('thumb.normal')) ? $getHost . $action->getSubject()->getPhotoUrl('thumb.normal') : '';

                    //assign getHost the correct value
                    $getHost = $getPhotoHost;

                    $activityFeedArray['feed']['subject']["owner_url"] = ($action->getSubject()->getOwner()->getHref()) ? $getHost . $action->getSubject()->getOwner()->getHref() : '';
                    $activityFeedArray['feed']['subject']["owner_title"] = $action->getSubject()->getOwner()->getTitle();

                    if (isset($activityFeedArray['feed']['subject']['creation_ip']))
                        unset($activityFeedArray['feed']['subject']['creation_ip']);

                    if (isset($activityFeedArray['feed']['subject']['lastlogin_ip']))
                        unset($activityFeedArray['feed']['subject']['lastlogin_ip']);
                }

                // Set feed object information on request
                if (isset($data['object_info']) && !empty($data['object_info'])) {
                    $activityFeedArray['feed']['object'] = $action->getObject()->toArray();
                    $getObjectModName = $action->getObject()->getModuleName();
                    $activityFeedArray['feed']['object']['name'] = (!empty($getObjectModName)) ? strtolower($getObjectModName) : '';
                    $activityFeedArray['feed']['object']["url"] = $tempHost . $action->getObject()->getHref();

                    if ((strstr($action->getObject()->getPhotoUrl(), 'http://')) || (strstr($action->getObject()->getPhotoUrl(), 'https://'))) {
                        $getHost = '';
                    }

                    // Add images
                    $getContentImages = Engine_Api::_()->getApi('Core', 'siteapi')->getContentImage($action->getObject());
                    if (isset($getContentImages) && !empty($getContentImages))
                        $activityFeedArray['feed']['object'] = array_merge($activityFeedArray['feed']['object'], $getContentImages);

//                    $activityFeedArray['feed']['object']["image"] = ($action->getObject()->getPhotoUrl('thumb.main')) ? $getHost . '/' . $action->getObject()->getPhotoUrl('thumb.main') : '';
//                    $activityFeedArray['feed']['object']["image_icon"] = ($action->getObject()->getPhotoUrl('thumb.icon')) ? $getHost . $action->getObject()->getPhotoUrl('thumb.icon') : '';
//                    $activityFeedArray['feed']['object']["image_profile"] = ($action->getObject()->getPhotoUrl('thumb.profile')) ? $getHost . $action->getObject()->getPhotoUrl('thumb.profile') : '';
//                    $activityFeedArray['feed']['object']["image_normal"] = ($action->getObject()->getPhotoUrl('thumb.normal')) ? $getHost . $action->getObject()->getPhotoUrl('thumb.normal') : '';
                    //assign getHost the correct value
                    $getHost = $getPhotoHost;

                    $activityFeedArray['feed']['object']["owner_url"] = ($action->getObject()->getOwner()->getHref()) ? $getHost . $action->getObject()->getOwner()->getHref() : '';
                    $activityFeedArray['feed']['object']["owner_title"] = $action->getObject()->getOwner()->getTitle();


                    if (isset($activityFeedArray['feed']['object']['creation_ip']))
                        unset($activityFeedArray['feed']['object']['creation_ip']);

                    if (isset($activityFeedArray['feed']['object']['lastlogin_ip']))
                        unset($activityFeedArray['feed']['object']['lastlogin_ip']);
                }


                // Set feed like count
                $activityFeedArray['feed']['like_count'] = $action->likes()->getLikePaginator()->getTotalItemCount();

                // Set feed comment count
                $activityFeedArray['feed']['comment_count'] = $action->comments()->getCommentCount();

                if ((strstr($itemPhoto->getPhotoUrl(), 'http://')) || (strstr($itemPhoto->getPhotoUrl(), 'https://'))) {
                    $getHost = '';
                }

                // Set feed icon
                $activityFeedArray['feed']['feed_icon'] = ($itemPhoto->getPhotoUrl('thumb.icon')) ? $getHost . $itemPhoto->getPhotoUrl('thumb.icon') : '';

                $getHost = $getPhotoHost;

                $privacy_titile = $privacy_icon_class = null;
                $privacy_titile_array = array();

                // Get the tag information
                $getTags = Engine_Api::_()->advancedactivity()->getTag($action);
                if (!empty($getTags)) {
                    foreach ($getTags as $tagFriend) {
                        $tempTag = $tagFriend->toArray();
                        $getTagedObj = Engine_Api::_()->getItem($tagFriend->tag_type, $tagFriend->tag_id);
                        if (!empty($getTagedObj)) {
                            $tempTag['tag_obj'] = $getTagedObj->toArray();
                            $tempTag['tag_obj']["image_icon"] = ($getTagedObj->getPhotoUrl('thumb.icon')) ? $getHost . $getTagedObj->getPhotoUrl('thumb.icon') : '';
                        }

                        if (isset($tempTag['tag_obj']['lastlogin_ip']) && !empty($tempTag['tag_obj']['lastlogin_ip']))
                            unset($tempTag['tag_obj']['lastlogin_ip']);

                        if (isset($tempTag['tag_obj']['creation_ip']) && !empty($tempTag['tag_obj']['creation_ip']))
                            unset($tempTag['tag_obj']['creation_ip']);


                        $activityFeedArray['feed']['tags'][] = $tempTag;
                    }
                }

                /* Start Attachement Work */
                if ($action->getTypeInfo()->attachable && $action->attachment_count > 0) {
                    if (false && $action->getAttachments()) {
                        // @TODO: IN CASE OF 1 ATTACHMENT OR GETRICHCONTENT CASE, WE ARE NOT USING GETRICHCONTENT AND USING THE DEFAULT ATTACHEMENT MENTHODS.
                    } else {
                        $attachmentArray = array();
                        $attachedImageCount = 0;

                        foreach ($action->getAttachments() as $attachment) {
                            $tempAttachmentArray = array();
                            if ($attachment->meta->mode == 0) {
                                
                            } elseif (($attachment->meta->mode == 1) || ($attachment->meta->mode == 2)) {

                                // In case of mode-1 set the attachment title and description.
                                if ($attachment->meta->mode == 1) {
                                    $tempAttachmentArray['title'] = $attachment->item->getTitle();
                                    //$tempAttachmentArray['body'] = $attachment->item->getDescription();
                                    $tempAttachmentBody = $attachment->item->getDescription();
                                    $tempAttachmentArray['body'] = (isset($activityFeedArray['feed']['body']) && !empty($activityFeedArray['feed']['body']) && ($activityFeedArray['feed']['body'] === $tempAttachmentBody)) ? '' : $tempAttachmentBody;
                                }

                                $tempAttachmentArray["attachment_type"] = $attachment->item->getType();
                                $activityFeedArray['feed']['attachment_content_type'] = $attachment->item->getType();
                                if (isset($activityFeedArray['feed']['type']) && ($activityFeedArray['feed']['type'] == 'share')) {
                                    $activityFeedArray['feed']['share_params_type'] = $attachment->item->getType();
                                    $activityFeedArray['feed']['share_params_id'] = $attachment->item->getIdentity();

                                    if (strstr($activityFeedArray['feed']['share_params_type'], 'sitereview')) {
                                        if (isset($activityFeedArray['feed']['share_params_id']) && $activityFeedArray['feed']['share_params_type']) {
                                            if ($activityFeedArray['feed']['share_params_type'] == 'sitereview_listing')
                                                $sitereviewObj = Engine_Api::_()->getItem('sitereview_listing', $activityFeedArray['feed']['share_params_id']);
                                            else {
                                                $tempObj = Engine_Api::_()->getItem($activityFeedArray['feed']['share_params_type'], $activityFeedArray['feed']['share_params_id']);
                                                if (isset($tempObj) && !empty($tempObj))
                                                    $sitereviewObj = $tempObj->getParent();
                                            }

                                            if (isset($sitereviewObj) && !empty($sitereviewObj) && isset($sitereviewObj->listingtype_id)) {
                                                if (isset($tempAttachmentArray) && !empty($tempAttachmentArray))
                                                    $tempAttachmentArray['listingtype_id'] = $sitereviewObj->listingtype_id;
                                                $tempAttachmentArray['listing_id'] = $sitereviewObj->listing_id;
                                            }
                                        }
                                    }
                                }

                                if ($tempAttachmentArray["attachment_type"] == 'music_playlist_song')
                                    $tempAttachmentArray["playlist_id"] = $attachment->item->playlist_id;

                                //@todo code need to be updated for all types of attachment[for now working for status update only]
                                if ($tempAttachmentArray["attachment_type"] == 'activity_action') {

                                    $tempAttachmentArray["attachment_id"] = $attachment->item->getIdentity();
                                    $attachedActionId = $attachment->meta->id;
                                    $attachedActionObject = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($attachedActionId);

                                    if (isset($attachedActionObject) &&
                                            !empty($attachedActionObject) &&
                                            $attachedActionObject->type == 'status' &&
                                            $attachedActionObject->object_type == 'user'
                                    ) {
                                        $oldcode = 'href="/profile';
                                        if (strstr($tempAttachmentArray['body'], $oldcode)) {
                                            $newcode = 'href="' . $tempHost . '/profile';
                                            $tempAttachmentArray['body'] = str_replace($oldcode, $newcode, $tempAttachmentArray['body']);
                                        }
                                    }
                                }

                                if (!empty($attachment->item))
                                    $tempAttachmentArray["attachment_id"] = $attachment->item->getIdentity();

                                if (isset($activityFeedArray['feed']['attachment_content_type']) && !empty($activityFeedArray['feed']['attachment_content_type']) && (strstr($activityFeedArray['feed']['attachment_content_type'], "sitereview_wishlist") || strstr($activityFeedArray['feed']['attachment_content_type'], "sitereview_review") || strstr($activityFeedArray['feed']['attachment_content_type'], "sitereview_listing"))) {
                                    $sitereviewInfo = $this->_getSitereviewInfo($tempAttachmentArray["attachment_type"], $tempAttachmentArray["attachment_id"]);
                                    if (isset($sitereviewInfo) && !empty($sitereviewInfo)) {
                                        $tempAttachmentArray = array_merge($tempAttachmentArray, $sitereviewInfo);
                                    }
                                }

                                // Siteevent title for Review in Advanced event
                                if (isset($activityFeedArray['feed']['attachment_content_type']) && !empty($activityFeedArray['feed']['attachment_content_type']) && (strstr($activityFeedArray['feed']['attachment_content_type'], "siteevent_review")) && isset($tempAttachmentArray['attachment_type']) && !empty($tempAttachmentArray['attachment_type']) && isset($tempAttachmentArray['attachment_id']) && !empty($tempAttachmentArray['attachment_id'])) {
                                    $getEventReviewItem = Engine_Api::_()->getItem('siteevent_review', $tempAttachmentArray['attachment_id']);
                                    if (isset($getEventReviewItem) && !empty($getEventReviewItem) && !empty($getEventReviewItem->resource_id))
                                        $tempAttachmentArray['event_id'] = $getEventReviewItem->resource_id;
                                }



                                if ($tempAttachmentArray["attachment_type"] == 'core_link')
                                    $tempAttachmentArray["uri"] = $attachment->item->uri;

                                if ($tempAttachmentArray["attachment_type"] == 'sitestoreproduct_product')
                                    $tempAttachmentArray["uri"] = $tempHost . $attachment->item->getHref();

                                if (strstr($tempAttachmentArray["attachment_type"], 'video')) {
                                    $tempAttachmentArray['attachment_video_type'] = $attachment->item->type;
                                    try {
                                        $tempAttachmentArray['attachment_video_url'] = Engine_Api::_()->getApi('Siteapi_Core', 'video')->getVideoURL($attachment->item);
                                    } catch (Exception $ex) {
                                        $tempAttachmentArray['attachment_video_url'] = "";
                                    }
                                }

                                // If attachment type related to photo then set the respective photo like and comment count information because it will be required in Photo Lightbox
                                if (strpos($attachment->meta->type, '_photo')) {
                                    $getAttachmentItem = $attachment->item;
                                    $tempAttachmentArray["attachment_id"] = (isset($getAttachmentItem->album_id)) ? $getAttachmentItem->album_id : $attachment->item->getAlbum()->getIdentity();
                                    $tempAttachmentArray["album_id"] = (isset($getAttachmentItem->album_id)) ? $getAttachmentItem->album_id : $attachment->item->getAlbum()->getIdentity();
                                    $tempAttachmentArray["photo_id"] = ($getAttachmentItem->getIdentity()) ? $getAttachmentItem->getIdentity() : 0;

                                    $tempAttachmentArray['likes_count'] = $attachment->item->likes()->getLikeCount();

                                    $tempAttachmentArray['comment_count'] = $attachment->item->comments()->getCommentCount();
                                    $tempAttachmentArray['is_like'] = ($attachment->item->likes()->isLike($viewer)) ? 1 : 0;
                                    if ($viewer->getIdentity() && isset($tempAttachmentArray["album_id"]) && !empty($tempAttachmentArray["album_id"]) && isset($tempAttachmentArray["photo_id"]) && !empty($tempAttachmentArray["photo_id"])) {
                                        $photo = $getAttachmentItem;
                                        $tempAttachmentArray['menu'] = $this->_getPhotoMenuUrl($photo, $tempAttachmentArray);
                                    }
                                }

                                if (isset($activityFeedArray['feed']['attachment_content_type']) && !empty($activityFeedArray['feed']['attachment_content_type']) && (strstr($activityFeedArray['feed']['attachment_content_type'], "sitereview_photo") && isset($tempAttachmentArray["attachment_type"]) && !empty($tempAttachmentArray["attachment_type"]) && isset($tempAttachmentArray["attachment_id"]) && !empty($tempAttachmentArray["attachment_id"]) && isset($tempAttachmentArray["photo_id"]) && !empty($tempAttachmentArray["photo_id"]))) {
                                    $sitereviewInfo = $this->_getSitereviewInfo($tempAttachmentArray["attachment_type"], $tempAttachmentArray["attachment_id"], $tempAttachmentArray["photo_id"]);
                                    if (isset($sitereviewInfo) && !empty($sitereviewInfo)) {
                                        $tempAttachmentArray = array_merge($tempAttachmentArray, $sitereviewInfo);
                                    }
                                }

                                // Set the feed image in case of feed item if image.
                                if (empty($_GET['getAttachedImageDimention'])) {
                                    if ($attachment->item->getPhotoUrl()) {
                                        $attachedImageCount++;
                                        $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.main');
                                        $getimagesize = @getimagesize($imageUrl);
                                        if (!empty($getimagesize)) {
                                            $tempAttachmentArray["image_main"] = array(
                                                "src" => $imageUrl,
                                                "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
                                            );
                                        }

                                        $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.icon');
                                        $getimagesize = @getimagesize($imageUrl);
                                        if (!empty($getimagesize)) {
                                            $tempAttachmentArray["image_icon"] = array(
                                                "src" => $imageUrl,
                                                "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
                                            );
                                        }

                                        $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.profile');
                                        $getimagesize = @getimagesize($imageUrl);
                                        if (!empty($getimagesize)) {
                                            $tempAttachmentArray["image_profile"] = array(
                                                "src" => $imageUrl,
                                                "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
                                            );
                                        }

                                        $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.normal');
                                        $getimagesize = @getimagesize($imageUrl);
                                        if (!empty($getimagesize)) {
                                            $tempAttachmentArray["image_normal"] = array(
                                                "src" => $imageUrl,
                                                "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
                                            );
                                        }

                                        $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.medium');
                                        $getimagesize = @getimagesize($imageUrl);
                                        if (!empty($getimagesize)) {
                                            $tempAttachmentArray["image_medium"] = array(
                                                "src" => $imageUrl,
                                                "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
                                            );
                                        }
                                    }
                                } else {
                                    if ($attachment->item->getPhotoUrl()) {
                                        $attachedImageCount++;

                                        if ((strstr($attachment->item->getPhotoUrl(), 'http://')) || (strstr($attachment->item->getPhotoUrl(), 'https://'))) {
                                            $getHost = '';
                                        }

                                        $tempAttachmentArray["image_main"] = $getHost . $attachment->item->getPhotoUrl('thumb.main');
//                                        $getimagesize = @getimagesize($imageUrl);
//                                        if (!empty($getimagesize)) {
//                                            $tempAttachmentArray["image_main"] = array(
//                                                "src" => $imageUrl,
//                                                "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
//                                            );
//                                        }
//                                        $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.icon');
//                                        $getimagesize = @getimagesize($imageUrl);
//                                        if (!empty($getimagesize)) {
//                                            $tempAttachmentArray["image_icon"] = array(
//                                                "src" => $imageUrl,
//                                                "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
//                                            );
//                                        }
//                                        $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.profile');
//                                        $getimagesize = @getimagesize($imageUrl);
//                                        if (!empty($getimagesize)) {
//                                            $tempAttachmentArray["image_profile"] = array(
//                                                "src" => $imageUrl,
//                                                "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
//                                            );
//                                        }
//
//                                        $imageUrl = $getHost . $attachment->item->getPhotoUrl('thumb.normal');
//                                        $getimagesize = @getimagesize($imageUrl);
//                                        if (!empty($getimagesize)) {
//                                            $tempAttachmentArray["image_normal"] = array(
//                                                "src" => $imageUrl,
//                                                "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
//                                            );
//                                        }

                                        $tempAttachmentArray["image_medium"] = $getHost . $attachment->item->getPhotoUrl('thumb.medium');
//                                        $getimagesize = @getimagesize($imageUrl);
//                                        if (!empty($getimagesize)) {
//                                            $tempAttachmentArray["image_medium"] = array(
//                                                "src" => $imageUrl,
//                                                "size" => array("width" => $getimagesize[0], "height" => $getimagesize[1])
//                                            );
//                                        }
                                        $getHost = $getPhotoHost;
                                    }
                                }
                            } elseif ($attachment->meta->mode == 3) { // Description Type Only
                                $tempAttachmentArray["description"] = $attachment->item->getDescription();
                            } else if ($attachment->meta->mode == 4) {
                                
                            }
                            $tempAttachmentArray['mode'] = $attachment->meta->mode;

                            if (($attachment->meta->mode == 1) && ($activityFeedArray['feed']['type'] == 'share' || $activityFeedArray['feed']['type'] == 'react') && $tempAttachmentArray['attachment_type'] = 'activity_action') {
                                try {
                                    // Add body in case of share feed
                                    if (isset($attachment->item->body) && !empty($attachment->item->body))
                                        $tempAttachmentArray['body'] = $attachment->item->body;

                                    // Add uri in case of share feed
                                    if ($attachment->item->getHref())
                                        $tempAttachmentArray['uri'] = $tempHost . $attachment->item->getHref();
                                } catch (Exception $ex) {
                                    // Blank Exception
                                }
                            }

                            // Following code added to bypass iOS app crashing issue, when click on photo's
                            // @Todo: Need to remove in next release
                            if (_CLIENT_TYPE && (_CLIENT_TYPE == 'ios')) {
                                try {
                                    if (isset($tempAttachmentArray['attachment_type']) && !empty($tempAttachmentArray['attachment_type']) && isset($tempAttachmentArray['attachment_id']) || !empty($tempAttachmentArray['attachment_id'])) {
                                        if ($tempAttachmentArray['attachment_type'] == 'sitepage_photo') {
                                            $attachmentTempObj = Engine_Api::_()->getItem($tempAttachmentArray['attachment_type'], $tempAttachmentArray['attachment_id']);
                                            if (!empty($attachmentTempObj)) {
                                                if (@method_exists($attachmentTempObj, 'getParent')) {
                                                    $tempAttachmentArray['attachment_type'] = $attachmentTempObj->getParent()->getType();
                                                    $tempAttachmentArray['attachment_id'] = $attachmentTempObj->getParent()->getIdentity();
                                                    if (@method_exists($attachmentTempObj->getParent(), 'getParent')) {
                                                        $tempAttachmentArray['attachment_type'] = $attachmentTempObj->getParent()->getParent()->getType();
                                                        $tempAttachmentArray['attachment_id'] = $attachmentTempObj->getParent()->getParent()->getIdentity();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } catch (Exception $ex) {
                                    // Blank Exception
                                }
                            }

                            if (!isset($tempAttachmentArray['title']) || empty($tempAttachmentArray['title'])) {
                                $tempAttachmentArray['title'] = "";
                            }

                            $attachmentArray[] = $tempAttachmentArray;
                        }
                        // Set the attachements
                        $activityFeedArray['feed']['attachment'] = $attachmentArray;
                        $activityFeedArray['feed']['photo_attachment_count'] = !empty($attachedImageCount) ? $attachedImageCount : 0;
                    }
                }
                /* End Attachement Work */

                // Set the feed comment allow permission.
                $canComment = ($action->getTypeInfo()->commentable && $action->commentable &&
                        $viewer->getIdentity() &&
                        Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment'));
                $activityFeedArray['can_comment'] = $canLike = !empty($canComment) ? 1 : 0;

                // Set the feed like allow permission.
                $isLike = $action->likes()->isLike($viewer);
                $activityFeedArray['is_like'] = !empty($isLike) ? 1 : 0;

                //Sitereaction Plugin work start here
                if ($this->isSitereactionPluginLive()) {
                    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereaction') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereaction.reaction.active', 1)) {
                        $popularity = Engine_Api::_()->getApi('core', 'sitereaction')->getLikesReactionPopularity($action);
                        $feedReactionIcons = Engine_Api::_()->getApi('Siteapi_Core', 'sitereaction')->getLikesReactionIcons($popularity, 1);
                        $activityFeedArray['feed_reactions'] = $feedReactionIcons;

                        if (isset($viewer_id) && !empty($viewer_id)) {
                            $myReaction = $action->likes()->getLike($viewer);
                            if (isset($myReaction) && !empty($myReaction) && isset($myReaction->reaction) && !empty($myReaction->reaction)) {
                                $myReactionIcon = Engine_Api::_()->getApi('Siteapi_Core', 'sitereaction')->getIcons($myReaction->reaction, 1);
                                $activityFeedArray['my_feed_reaction'] = $myReactionIcon;
                            }
                        }
                    }
                }
                //Sitereaction Plugin work end here

                $isShareable = ($action->getTypeInfo()->shareable && $action->shareable && $viewer->getIdentity()) ? 1 : 0;

                /* ------------ START FEED MENU WORK ---------------- */
                if (!empty($canLike)) {
                    if (empty($isLike)) {
                        $activityFooterMenus["like"]["name"] = "like";
                        $activityFooterMenus["like"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Like");
                        $activityFooterMenus["like"]["url"] = "like";
                        $activityFooterMenus["like"]['urlParams'] = array(
                            "action_id" => $action->action_id
                        );
                    } else {
                        $activityFooterMenus["like"]["name"] = "unlike";
                        $activityFooterMenus["like"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Unlike");
                        $activityFooterMenus["like"]["url"] = "unlike";
                        $activityFooterMenus["like"]['urlParams'] = array(
                            "action_id" => $action->action_id
                        );
                    }
                }

                $activityFeedArray['can_share'] = 0;
                if ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment())) {
                    $activityFeedArray['can_share'] = $isShareable;
                    $activityFooterMenus["share"]["name"] = "share";
                    $activityFooterMenus["share"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Share");
                    $activityFooterMenus["share"]["url"] = "activity/share";
                    $activityFooterMenus["share"]['urlParams'] = array(
                        "type" => $attachment->item->getType(),
                        "id" => $attachment->item->getIdentity()
                    );
                } else if ($action->getTypeInfo()->shareable == 2) {
                    $activityFeedArray['can_share'] = $isShareable;
                    $activityFooterMenus["share"]["name"] = "share";
                    $activityFooterMenus["share"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Share");
                    $activityFooterMenus["share"]["url"] = "activity/share";
                    $activityFooterMenus["share"]['urlParams'] = array(
                        "type" => $subject->getType(),
                        "id" => $subject->getIdentity()
                    );
                } elseif ($action->getTypeInfo()->shareable == 3) {
                    $activityFeedArray['can_share'] = $isShareable;
                    $activityFooterMenus["share"]["name"] = "share";
                    $activityFooterMenus["share"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Share");
                    $activityFooterMenus["share"]["url"] = "activity/share";
                    $activityFooterMenus["share"]['urlParams'] = array(
                        "type" => $object->getType(),
                        "id" => $object->getIdentity()
                    );
                } else if ($action->getTypeInfo()->shareable == 4) {
                    $activityFeedArray['can_share'] = $isShareable;
                    $activityFooterMenus["share"]["name"] = "share";
                    $activityFooterMenus["share"]["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Share");
                    $activityFooterMenus["share"]["url"] = "activity/share";
                    $activityFooterMenus["share"]['urlParams'] = array(
                        "type" => $action->getType(),
                        "id" => $action->getIdentity()
                    );
                }

                // Edit menu Work
                if (($action->getSubject()->getOwner()->getIdentity() == $viewer_id) && !empty($action->body) && (((_CLIENT_TYPE == 'android') && (_ANDROID_VERSION >= '1.6.2' )) || ((_CLIENT_TYPE == 'ios') && (_IOS_VERSION >= '1.4.3')))) {
                    $tempActivityMenu = array();
                    $tempActivityMenu["name"] = "edit_feed";
                    $tempActivityMenu["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Edit Feed');
                    $tempActivityMenu["url"] = "advancedactivity/edit-feed";
                    $tempActivityMenu['urlParams'] = array(
                        "action_id" => $action->action_id
                    );

                    $activityMenu[] = $tempActivityMenu;
                }

                if (empty($getSubject) && !empty($viewer_id) && $action->getTypeInfo()->type != 'birthday_post' && (!$viewer->isSelf($action->getSubject()))) {
                    if (!Engine_Api::_()->core()->hasSubject()) {

                        $add_saved_feed_row = Engine_Api::_()->getDbtable('contents', 'advancedactivity')->getContentList(array('content_tab' => 1, 'filter_type' => 'user_saved'));
                        if (!empty($add_saved_feed_row)) {
                            $tempActivityMenu = array();
                            $tempActivityMenu["name"] = "update_save_feed";
                            $tempActivityMenu["label"] = ($advancedactivitySaveFeed->getSaveFeed($viewer, $action->action_id)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate('Unsaved Feed') : Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Feed');
                            $tempActivityMenu["url"] = "advancedactivity/update-save-feed";
                            $tempActivityMenu['urlParams'] = array(
                                "action_id" => $action->getIdentity()
                            );
                            $activityMenu[] = $tempActivityMenu;
                        }

                        $tempActivityMenu = array();
                        $tempActivityMenu["name"] = "hide";
                        $tempActivityMenu["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Hide");
                        $tempActivityMenu["url"] = "advancedactivity/feeds/hide-item";
                        $tempActivityMenu['urlParams'] = array(
                            "type" => $action->getType(),
                            "id" => $action->getIdentity()
                        );
                        $activityMenu[] = $tempActivityMenu;
                    }

                    $tempActivityMenu = array();
                    $tempActivityMenu["name"] = "report_feed";
                    $tempActivityMenu["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Report Feed");
                    $tempActivityMenu["url"] = "advancedactivity/feeds/hide-item";
                    $tempActivityMenu['urlParams'] = array(
                        "type" => $action->getType(),
                        "id" => $action->getIdentity(),
                        "hide_report" => 1
                    );
                    $activityMenu[] = $tempActivityMenu;

                    // Remove Hide all menu due to UI issues in app
//                    if (!Engine_Api::_()->core()->hasSubject()) {
//                        $item = (isset($action->getTypeInfo()->is_object_thumb) && !empty($action->getTypeInfo()->is_object_thumb)) ? $action->getObject() : $action->getSubject();
//                        $tempActivityMenu = array();
//                        $tempActivityMenu["name"] = "hits_feed";
//                        $tempActivityMenu["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Hide all by ') . $item->getTitle();
//                        $tempActivityMenu["url"] = "advancedactivity/feeds/hide-item";
//                        $tempActivityMenu['urlParams'] = array(
//                            "type" => $item->getType(),
//                            "id" => $item->getIdentity()
//                        );
//                        $activityMenu[] = $tempActivityMenu;
//                    }

                    if ($viewer_id && (
                            $data['activity_moderate'] || $data['is_owner'] || (
                            $data['allow_delete'] && (
                            ('user' == $action->subject_type && $viewer_id == $action->subject_id) ||
                            ('user' == $action->object_type && $viewer_id == $action->object_id)
                            )
                            )
                            )) {

                        $tempActivityMenu = array();
                        $tempActivityMenu["name"] = "delete_feed";
                        $tempActivityMenu["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Delete Feed');
                        $tempActivityMenu["url"] = "advancedactivity/delete";
                        $tempActivityMenu['urlParams'] = array(
                            "action_id" => $action->action_id
                        );


                        $activityMenu[] = $tempActivityMenu;


                        if ($action->getTypeInfo()->commentable) {
                            // Disable Comment
                            $tempActivityMenu = array();
                            $tempActivityMenu["name"] = "disable_comment";
                            $tempActivityMenu["label"] = ($action->commentable) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate('Disable Comments') : Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enable Comments');
                            $tempActivityMenu["url"] = "advancedactivity/update-commentable";
                            $tempActivityMenu['urlParams'] = array(
                                "action_id" => $action->action_id
                            );
                            $activityMenu[] = $tempActivityMenu;
                        }

                        if ($action->getTypeInfo()->shareable > 1 || ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()))) {
                            // Lock this Feed
                            $tempActivityMenu = array();
                            $tempActivityMenu["name"] = "lock_this_feed";
                            $tempActivityMenu["label"] = ($action->shareable) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate('Lock this Feed') : Engine_Api::_()->getApi('Core', 'siteapi')->translate('Unlock this Feed');
                            $tempActivityMenu["url"] = "advancedactivity/update-shareable";
                            $tempActivityMenu['urlParams'] = array(
                                "action_id" => $action->action_id
                            );
                            $activityMenu[] = $tempActivityMenu;
                        }
                    }
                } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.post.canedit', 1) && !empty($action->privacy) && in_array($action->getTypeInfo()->type, array("post", "post_self", "status", 'sitetagcheckin_add_to_map', 'sitetagcheckin_content', 'sitetagcheckin_status', 'sitetagcheckin_post_self', 'sitetagcheckin_post', 'sitetagcheckin_checkin', 'sitetagcheckin_lct_add_to_map', 'post_self_photo', 'post_self_video', 'post_self_music', 'post_self_link')) && $viewer->getIdentity() && (('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id))) {
                    if (!empty($data['allowSaveFeed']) && $viewer_id) {
                        $tempActivityMenu = array();
                        $tempActivityMenu["name"] = "update_save_feed";
                        $tempActivityMenu["label"] = ($advancedactivitySaveFeed->getSaveFeed($viewer, $action->action_id)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate('Unsaved Feed') : Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Feed');
                        $tempActivityMenu["url"] = "advancedactivity/update-save-feed";
                        $tempActivityMenu['urlParams'] = array(
                            "action_id" => $action->getIdentity()
                        );
                        $activityMenu[] = $tempActivityMenu;
                    }

                    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
                    $allowToDelete = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete');


                    if (Engine_Api::_()->core()->hasSubject()) {
                        $subject = Engine_Api::_()->core()->getSubject();
                        $is_owner = $viewer->isSelf($subject);
                    }

                    if ($activity_moderate || $allowToDelete || $is_owner) {
                        $tempActivityMenu = array();
                        $tempActivityMenu["name"] = "delete_feed";
                        $tempActivityMenu["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Delete Feed');
                        $tempActivityMenu["url"] = "advancedactivity/delete";
                        $tempActivityMenu['urlParams'] = array(
                            "action_id" => $action->action_id
                        );
                        $activityMenu[] = $tempActivityMenu;



                        if ($action->getTypeInfo()->commentable) {
                            // Disable Comment
                            $tempActivityMenu = array();
                            $tempActivityMenu["name"] = "disable_comment";
                            $tempActivityMenu["label"] = ($action->commentable) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate('Disable Comments') : Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enable Comments');
                            $tempActivityMenu["url"] = "advancedactivity/update-commentable";
                            $tempActivityMenu['urlParams'] = array(
                                "action_id" => $action->action_id
                            );
                            $activityMenu[] = $tempActivityMenu;
                        }

                        if ($action->getTypeInfo()->shareable > 1 || ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()))) {
                            // Lock this Feed
                            $tempActivityMenu = array();
                            $tempActivityMenu["name"] = "lock_this_feed";
                            $tempActivityMenu["label"] = ($action->shareable) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate('Lock this Feed') : Engine_Api::_()->getApi('Core', 'siteapi')->translate('Unlock this Feed');
                            $tempActivityMenu["url"] = "advancedactivity/update-shareable";
                            $tempActivityMenu['urlParams'] = array(
                                "action_id" => $action->action_id
                            );
                            $activityMenu[] = $tempActivityMenu;
                        }
                    }
                } else {
                    if (!empty($data['allowSaveFeed']) && $viewer_id) {
                        $tempActivityMenu = array();
                        $tempActivityMenu["name"] = "update_save_feed";
                        $tempActivityMenu["label"] = ($advancedactivitySaveFeed->getSaveFeed($viewer, $action->action_id)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate('Unsaved Feed') : Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Feed');
                        $tempActivityMenu["url"] = "advancedactivity/update-save-feed";
                        $tempActivityMenu['urlParams'] = array(
                            "action_id" => $action->getIdentity()
                        );
                        $activityMenu[] = $tempActivityMenu;
                    }


                    if (Engine_Api::_()->core()->hasSubject()) {
                        $subject = Engine_Api::_()->core()->getSubject();
                        $is_owner = $viewer->isSelf($subject);
                    }

                    if (isset($viewer) && !empty($viewer) && isset($action) && !empty($action))
                        $is_owner = $viewer->isSelf($action->getSubject());

                    if (!empty($is_owner) || (isset($viewer->level_id) && ($viewer->level_id == 1))) {
                        $tempActivityMenu = array();
                        $tempActivityMenu["name"] = "delete_feed";
                        $tempActivityMenu["label"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Delete Feed');
                        $tempActivityMenu["url"] = "advancedactivity/delete";
                        $tempActivityMenu['urlParams'] = array(
                            "action_id" => $action->action_id
                        );
                        $activityMenu[] = $tempActivityMenu;


                        if ($action->getTypeInfo()->commentable) {
                            // Disable Comment
                            $tempActivityMenu = array();
                            $tempActivityMenu["name"] = "disable_comment";
                            $tempActivityMenu["label"] = ($action->commentable) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate('Disable Comments') : Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enable Comments');
                            $tempActivityMenu["url"] = "advancedactivity/update-commentable";
                            $tempActivityMenu['urlParams'] = array(
                                "action_id" => $action->action_id
                            );
                            $activityMenu[] = $tempActivityMenu;
                        }

                        if ($action->getTypeInfo()->shareable > 1 || ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()))) {
                            // Lock this Feed
                            $tempActivityMenu = array();
                            $tempActivityMenu["name"] = "lock_this_feed";
                            $tempActivityMenu["label"] = ($action->shareable) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate('Lock this Feed') : Engine_Api::_()->getApi('Core', 'siteapi')->translate('Unlock this Feed');
                            $tempActivityMenu["url"] = "advancedactivity/update-shareable";
                            $tempActivityMenu['urlParams'] = array(
                                "action_id" => $action->action_id
                            );
                            $activityMenu[] = $tempActivityMenu;
                        }
                    }
                }


                if (!empty($viewer_id)) {
                    $activityFeedArray['feed_menus'] = $activityMenu;
                    $activityFeedArray['feed_footer_menus'] = $activityFooterMenus;
                }

                // Set Feed Title
                $activityFeedArray['feed']['feed_title'] = $this->getContent($action);

                // Set activity feed type - body and these params array. So that Feed Title could be create at dynamically APP side.
                $activityFeedArray['feed']['action_type_body'] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($getFeedTypeInfo['body']);
                $activityFeedArray['feed']['action_type_body'] = @strtolower($activityFeedArray['feed']['action_type_body']);
                $this->_idBodyContentAvailable = false;
                $activityFeedArray['feed']['action_type_body_params'] = $this->getContent($action, 1);

                // @Todo: Following code should be modified and move in "getContent()" method.
                if (!empty($this->_idBodyContentAvailable)) {
                    $tempBodyArray = $finalArray = array();
                    $getActionTypeBodyParams = $activityFeedArray['feed']['action_type_body_params'];
                    $getDefaultKey = false;
                    foreach ($getActionTypeBodyParams as $key => $paramArray) {
                        if ($paramArray['search'] == '{body:$body}') {
                            // Create temporary body array.
                            $tempBodyArray[] = $paramArray;
//                            if (!empty($getDefaultKey))
//                                $finalArray[$getDefaultKey] = $paramArray;
//                            else
//                                $finalArray[] = $paramArray;
//
//                            $getDefaultKey = $key;
                        } else {
                            if (isset($paramArray['label']) && !empty($paramArray['label']) && !strstr($paramArray['label'], " ") && strstr($paramArray['label'], "_")) {
                                try {
                                    if (isset($paramArray['type']) && isset($paramArray['id']) && !empty($paramArray['type']) && !empty($paramArray['id'])) {
                                        $getTempObj = Engine_Api::_()->getItem($paramArray['type'], $paramArray['id']);
                                        if ($getTempObj->getTitle())
                                            $paramArray['label'] = $getTempObj->getTitle();
                                    }else {
                                        $paramArray['label'] = "";
                                    }
                                } catch (Exception $ex) {
                                    $paramArray['label'] = "";
                                }
                            }

                            $finalArray[] = $paramArray;
                        }
                    }

                    // Set last value of temporary array to final array
                    if (!empty($tempBodyArray))
                        $finalArray[] = @end($tempBodyArray);

                    $activityFeedArray['feed']['action_type_body_params'] = $finalArray;
                }

                /* ------------ END FEED MENU WORK ---------------- */
            } catch (Exception $e) {
                
            }
            if (!empty($activityFeedArray))
                $activityFeed[] = $activityFeedArray;
        }

        return $activityFeed;
    }

    protected $_idBodyContentAvailable = false;

    /**
     * Feed Title
     *
     * @return array
     */
    private function getContent($action, $flag = false) {
        $params = array_merge(
                $action->toArray(), (array) $action->params, array(
            'subject' => $action->getSubject(),
            'object' => $action->getObject()
                )
        );

        $params['actionObj'] = $action;
        $params['flag'] = $flag;

        $content = $this->assemble($action->getTypeInfo()->body, $params);
        return $content;
    }

    protected $_pluginLoader;

    /**
     * Feed Title - Load the Plugins
     *
     * @return array
     */
    private function getPluginLoader() {
        if (null === $this->_pluginLoader) {
            $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR
                    . 'modules' . DIRECTORY_SEPARATOR
                    . 'Activity';
            $this->_pluginLoader = new Zend_Loader_PluginLoader(array(
                'Activity_Model_Helper_' => $path . '/Model/Helper/'
            ));
        }

        return $this->_pluginLoader;
    }

    /**
     * Normalize helper name
     * 
     * @param string $name
     * @return string
     */
    private function _normalizeHelperName($name) {
        $name = preg_replace('/[^A-Za-z0-9]/', '', $name);
        $name = ucfirst($name);
        return $name;
    }

    /*
     *  Returns a multidimentional array with hashtags separated for separate action_id
     * 
     * @param $action object
     * @return array
     */

    private function getHashtagNames($action) {

        $hashTagMapTable = Engine_Api::_()->getDbtable('tagmaps', 'sitehashtag');
        $hashtagNames = array();
        preg_match_all("/\B#\w*[a-zA-Z]+\w*/", $action->body, $hashtags);
        $hashtagName = array();
        $hashtagmaps = $hashTagMapTable->getActionTagMaps($action->action_id);
        foreach ($hashtagmaps as $hashtagmap) {
            $tag = Engine_Api::_()->getItem('sitehashtag_tag', $hashtagmap->tag_id);
            if ($tag && !in_array($tag->text, $hashtags[0])) {
                $hashtagName[] = $tag->text;
            }
        }
        return $hashtagName;
    }

    /*
     * Get review information
     * 
     * @param $attachementType string
     * @param $attachmentId in
     * @return array
     * 
     */

    private function _getSitereviewInfo($attachementType, $attachmentId, $photo_id = 0) {
        if (empty($attachementType) || empty($attachmentId))
            return;

        if (strstr($attachementType, 'sitereview_wishlist')) {
            $wishlist = Engine_Api::_()->getItem('sitereview_wishlist', $attachmentId);

            if (isset($wishlist) && !empty($wishlist)) {
                $wishlistListing = Engine_Api::_()->getDbTable('wishlistmaps', 'sitereview')->wishlistListings($wishlist->wishlist_id);
                $sitereviewInfo['count'] = $wishlistListing->getTotalItemCount();
            }
        } else if (strstr($attachementType, 'sitereview_review')) {
            $review = Engine_Api::_()->getItem('sitereview_review', $attachmentId);
            $sitereview = $review->getParent();
            $listing_id = $sitereview->getIdentity();
            if (isset($review) && !empty($review) && !empty($listing_id)) {
                $reviewParams = array();
                $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitereview');
                $reviewParams['resource_id'] = $sitereview->getIdentity();
                $reviewParams['resource_type'] = 'sitereview_listing';
                $reviewParams['type'] = 'user';
                $sitereviewInfo['listing_title'] = $sitereview->getTitle();
                $sitereviewInfo['listing_id'] = $listing_id;
                $sitereviewInfo['count'] = $reviewTable->totalReviews($reviewParams);
                $sitereviewInfo['listingtype_id'] = $sitereview->listingtype_id;
            }
        } else if (strstr($attachementType, 'sitereview_listing')) {
            $sitereview = Engine_Api::_()->getItem('sitereview_listing', $attachmentId);

            if (isset($sitereview) && !empty($sitereview))
                $sitereviewInfo['listingtype_id'] = $sitereview->listingtype_id;
        }else if (strstr($attachementType, 'sitereview_photo') && isset($photo_id) && !empty($photo_id)) {
            $sitereviewPhotoObj = Engine_Api::_()->getItem('sitereview_photo', $photo_id);
            if (isset($sitereviewPhotoObj) && !empty($sitereviewPhotoObj))
                $sitereviewId = $sitereviewPhotoObj->listing_id;
            if (isset($sitereviewId) && !empty($sitereviewId))
                $sitereview = Engine_Api::_()->getItem('sitereview_listing', $sitereviewId);
            if (isset($sitereview) && !empty($sitereview)) {
                $listing_id = $sitereview->getIdentity();
                $sitereviewInfo['listing_id'] = $listing_id;
                $sitereviewInfo['listingtype_id'] = $sitereview->listingtype_id;
            }
        }

        if (isset($sitereviewInfo) && !empty($sitereviewInfo))
            return $sitereviewInfo;
    }

    /*
     * Get attached image menu according to module information
     * 
     * @param $subject string
     * @param $tempAttachmentArray array
     * @return array
     * 
     */

    private function _getPhotoMenuUrl($subject, $tempAttachmentArray) {
        $menu = array();
        $type = $subject->getType();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

//        if ($type == 'sitepage_photo') {
//            $parentObject = $subject->getParent();
//            $page_id = $parentObject->getIdentity();
//
//            if (!empty($parentObject) && !empty($page_id))
//                $canEdit = Engine_Api::_()->sitepage()->isManageAdmin($parentObject, 'edit');
//
//            if (!empty($canEdit)) {
//                $menu[] = array(
//                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Edit'),
//                    'name' => 'edit',
//                    'url' => 'sitepage/photos/editphoto/' . $page_id . "/" . $tempAttachmentArray["album_id"] . "/" . $tempAttachmentArray["photo_id"],
//                );
//
//                $menu[] = array(
//                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Delete'),
//                    'name' => 'delete',
//                    'url' => 'sitepage/photos/deletephoto/' . $page_id . "/" . $tempAttachmentArray["album_id"] . "/" . $tempAttachmentArray["photo_id"],
//                );
//            }
//        } else if ($type == 'sitegroup_photo') {
//            $parentObject = $subject->getParent();
//            $group_id = $parentObject->getIdentity();
//
//            if (!empty($parentObject) && !empty($group_id))
//                $canEdit = Engine_Api::_()->sitegroup()->isManageAdmin($parentObject, 'edit');
//
//            if (!empty($canEdit)) {
//                $menu[] = array(
//                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Edit'),
//                    'name' => 'edit',
//                    'url' => 'advancedgroups/photos/editphoto/' . $group_id . "/" . $tempAttachmentArray["album_id"] . "/" . $tempAttachmentArray["photo_id"],
//                );
//
//                $menu[] = array(
//                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Delete'),
//                    'name' => 'delete',
//                    'url' => 'advancedgroups/photos/deletephoto/' . $group_id . "/" . $tempAttachmentArray["album_id"] . "/" . $tempAttachmentArray["photo_id"],
//                );
//            }
//        } elseif ($type == 'siteevent_photo') {
//            $canEdit = $subject->canEdit($viewer);
//            if (!empty($canEdit)) {
//                $menu[] = array(
//                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Edit'),
//                    'name' => 'edit',
//                    'url' => 'advancedevents/photo/edit/' . $tempAttachmentArray["photo_id"],
//                );
//
//                $menu[] = array(
//                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Delete'),
//                    'name' => 'delete',
//                    'url' => 'advancedevents/photo/delete/' . $tempAttachmentArray["photo_id"],
//                );
//            }
//        } elseif ($type == 'sitereview_photo') {
//            $parentObject = $subject->getParent();
//            if (isset($subject->user_id) && !empty($subject->user_id) && !empty($parentObject) && $subject->user_id == $viewer_id) {
//                $menu[] = array(
//                    'label' => $this->translate('Edit'),
//                    'name' => 'edit',
//                    'url' => 'listings/photo/edit/' . $parentObject->getIdentity(),
//                    'urlParams' => array(
//                        "photo_id" => $tempAttachmentArray["photo_id"]
//                    )
//                );
//
//                $menu[] = array(
//                    'label' => $this->translate('Delete'),
//                    'name' => 'delete',
//                    'url' => 'listings/photo/delete/' . $parentObject->getIdentity(),
//                    'urlParams' => array(
//                        "photo_id" => $tempAttachmentArray["photo_id"]
//                    )
//                );
//            }
//        } else {
//            try {
//                $canEdit = $subject->authorization()->isAllowed($viewer, 'edit');
//            } catch (Exception $ex) {
//                $canEdit = 0;
//            }
//
//            if (!empty($canEdit)) {
//                $attachmentType = $tempAttachmentArray['attachment_type'];
//                $attachmentArrayName = explode("_", $attachmentType);
//                $moduleName = $attachmentArrayName[0] . 's';
//
//                if (!empty($moduleName)) {
//                    $menu[] = array(
//                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Edit'),
//                        'name' => 'edit',
//                        'url' => $moduleName . '/photo/edit',
//                        'urlParams' => array(
//                            "album_id" => $tempAttachmentArray["album_id"],
//                            "photo_id" => $tempAttachmentArray["photo_id"]
//                        )
//                    );
//
//                    $menu[] = array(
//                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Delete'),
//                        'name' => 'delete',
//                        'url' => $moduleName . '/photo/delete',
//                        'urlParams' => array(
//                            "album_id" => $tempAttachmentArray["album_id"],
//                            "photo_id" => $tempAttachmentArray["photo_id"]
//                        )
//                    );
//                }
//            }
//        }

        $menu[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Share'),
            'name' => 'share',
            'url' => 'activity/share',
            'urlParams' => array(
                "type" => $subject->getType(),
                "id" => $tempAttachmentArray["photo_id"]
            )
        );

        $menu[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Report'),
            'name' => 'report',
            'url' => 'report/create/subject/' . $subject->getGuid(),
            'urlParams' => array(
                "type" => $subject->getType(),
                "id" => $tempAttachmentArray["photo_id"]
            )
        );

        $menu[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Make Profile Photo'),
            'name' => 'make_profile_photo',
            'url' => 'members/edit/external-photo',
            'urlParams' => array(
                "photo" => $subject->getGuid()
            )
        );

        return $menu;
    }

    public function isSitereactionPluginLive() {
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereaction')) {
            $reactionModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitereaction');
            $reactionModule = $reactionModule->version;
            if ($reactionModule >= '4.8.12p4') {

                if ((_ANDROID_VERSION && _ANDROID_VERSION >= '1.7.5') || (_IOS_VERSION && _IOS_VERSION >= '1.6.4')) {
                    return true;
                }
            }
        }
        return false;
    }

    public function isSitestickerPluginLive() {
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereaction')) {
            $reactionModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitereaction');
            $reactionModule = $reactionModule->version;
            if ($reactionModule >= '4.8.12p4') {

                if ((_ANDROID_VERSION && _ANDROID_VERSION >= '1.7.6') || (_IOS_VERSION && _IOS_VERSION > '1.7')) {
                    return true;
                }
            }
        }
        return false;
    }

}
