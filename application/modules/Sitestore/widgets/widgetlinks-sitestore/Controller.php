<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Widget_WidgetlinksSitestoreController extends Engine_Content_Widget_Abstract {

    //ACTION FOR GETTING THE LINKS OF THE WIDGETS ON STORE PROFILE STORE (MEANS WITHOUT TAB LINKS)
    public function indexAction() {

        //DON'T RENDER IF SUNJECT IS NOT THERE
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        //GET SITESTORE SUBJECT
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sitestore_store');

        //GET STORE ID 
        $store_id = $subject->store_id;

        //START MANAGE-ADMIN CHECK
        $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($subject, 'view');
        if (empty($isManageAdmin)) {
            return $this->setNoRender();
        }
        //END MANAGE-ADMIN CHECK
        //GET CORE CONTENT TABLE
        $contentTable = Engine_Api::_()->getDbtable('content', 'core');

        //GET CORE STORES TABLE
        $storeTable = Engine_Api::_()->getDbtable('pages', 'core');

        //SELECT STORE
        $selectStore = $storeTable->select()->from($storeTable->info('name'), array('page_id'))->where('name =?', 'sitestore_index_view')->limit(1);

        //GET STORE INFO
        $storeInfo = $storeTable->fetchRow($selectStore);

        //HOW MAY LINK SHOULD BE SHOW IN THE WIDGET LINK BEFORE MORE LINK
        $this->view->linklimit = $linklimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showmore', 9);

        if (!empty($storeInfo)) {
            $selectContent = $contentTable->select()->from($contentTable->info('name'), array('page_id'))->where('name =?', 'core.container-tabs')->where('page_id =?', $storeInfo->page_id)->limit(1);
            $contentinfo = $contentTable->fetchRow($selectContent);
            if (!empty($contentinfo) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0)) {
                return $this->setNoRender();
            }
        }
        $resource_type = "";
        //GET TAB ID
        $this->view->tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0)) {
            $storeAdminContentTable = Engine_Api::_()->getDbtable('content', 'core');
            $selectStoreAdmin = $storeAdminContentTable->select()
                    ->from($storeAdminContentTable->info('name'), array('content_id', 'params', 'name'))
                    ->where('page_id =?', $storeInfo->page_id)
                    ->where("name IN ('sitestore.overview-sitestore', 'sitestore.photos-sitestore', 'sitestore.discussion-sitestore', 'sitestorenote.profile-sitestorenotes', 'sitestorepoll.profile-sitestorepolls', 'sitestoreevent.profile-sitestoreevents', 'sitestorevideo.profile-sitestorevideos', 'sitestoreoffer.profile-sitestoreoffers', 'sitestorereview.profile-sitestorereviews', 'sitestoredocument.profile-sitestoredocuments', 'sitestoreform.sitestore-viewform','sitestore.info-sitestore', 'seaocore.feed', 'activity.feed','sitestore.location-sitestore', 'core.profile-links', 'sitestoremusic.profile-sitestoremusic', 'sitestoreintegration.profile-items','sitestoretwitter.feeds-sitestoretwitter', 'sitestoremember.profile-sitestoremembers', 'advancedactivity.home-feeds', 'siteevent-contenttype-events', 'sitevideo.contenttype-videos')");
            $storeAdminresult = $storeAdminContentTable->fetchAll($selectStoreAdmin);
            $contentWigentLinks = array();
            if (!empty($storeAdminresult)) {
                foreach ($storeAdminresult as $key => $value) {
                    if (isset($value->params['resource_type'])) {
                        $resource_type = $value->params['resource_type'];

                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration') && strstr($resource_type, 'sitereview_listing') && !empty($resource_type)) {
                            $pieces = explode("_", $resource_type);
                            $listingTypeId = $pieces[2];
                            $count = Engine_Api::_()->getDbtable('contents', 'sitestoreintegration')->getCountResults('sitereview_listing', $listingTypeId);
                            if ($count == 0)
                                continue;
                        }
                    }
                    $content = $this->getContentName($value->name, $value->params['title'], $resource_type);
                    if (!empty($content)) {
                        $contentWigentLinks[$key]['content_id'] = $value->content_id;
                        $contentWigentLinks[$key]['content_name'] = $content[0];
                        $contentWigentLinks[$key]['content_class'] = $content[1];
                        if (isset($content[2]))
                            $contentWigentLinks[$key]['content_resource'] = $content[2];
                    }
                }

                $this->view->contentWigentLinks = $contentWigentLinks;
            }
        } else {
            $row = Engine_Api::_()->getDbtable('contentstores', 'sitestore')->getContentStoreId($store_id);
            if (!empty($row)) {
                $contentstore_id = $row->contentstore_id;
                $storeAdminresult = Engine_Api::_()->getDbtable('content', 'sitestore')->getContents($contentstore_id);
            } else {
                $contentstore_id = Engine_Api::_()->sitestore()->getWidgetizedStore()->page_id;
                $storeAdminresult = Engine_Api::_()->getDbtable('admincontent', 'sitestore')->getContents($contentstore_id);
            }
            $contentWigentLinks = array();
            if (!empty($storeAdminresult)) {
                foreach ($storeAdminresult as $key => $value) {
                    if (isset($value->params['resource_type'])) {
                        $resource_type = $value->params['resource_type'];
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration') && strstr($resource_type, 'sitereview_listing') && !empty($resource_type)) {
                            $pieces = explode("_", $resource_type);
                            $listingTypeId = $pieces[2];
                            $count = Engine_Api::_()->getDbtable('contents', 'sitestoreintegration')->getCountResults('sitereview_listing', $listingTypeId);
                            if ($count == 0)
                                continue;
                        }
                    }

                    $content = $this->getContentName($value->name, $value->params['title'], $resource_type);
                    if (!empty($content)) {
                        if (isset($value->content_id)) {
                            $contentWigentLinks[$key]['content_id'] = $value->content_id;
                        } else {
                            $contentWigentLinks[$key]['content_id'] = $value->admincontent_id;
                        }
                        $contentWigentLinks[$key]['content_name'] = $content[0];
                        $contentWigentLinks[$key]['content_class'] = $content[1];
                        if (isset($content[2]))
                            $contentWigentLinks[$key]['content_resource'] = $content[2];
                    }
                }
                $this->view->contentWigentLinks = $contentWigentLinks;
            }
        }
    }

    public function getContentName($name, $widgettitle, $resource_type) {
        $sitestore = Engine_Api::_()->core()->getSubject('sitestore_store');
        $store_id = $sitestore->store_id;
        $can_edit = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
        $content_array = array();

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        if (!empty($viewer_id)) {
            $level_id = $viewer->level_id;
        } else {
            $level_id = 0;
        }
        switch ($name) {
            case 'advancedactivity.home-feeds':
                $content_array = array($widgettitle, 'icon_sitestore_update');
                break;
            case 'seaocore.feed':
                $content_array = array($widgettitle, 'icon_sitestore_update');
                break;
            case 'activity.feed':
                $content_array = array($widgettitle, 'icon_sitestore_update');
                break;
            case 'sitestore.info-sitestore':
                $content_array = array($widgettitle, 'icon_sitestore_info');
                break;
            case 'core.profile-links':
                $linkTable = Engine_Api::_()->getDbtable('links', 'core');
                $linkTableresult = $linkTable->fetchAll($linkTable->select()->where('parent_id =?', $store_id))->toarray();
                if (!empty($linkTableresult)) {
                    $content_array = array($widgettitle, 'icon_sitestore_store_link');
                }
                break;
            case 'sitestore.photos-sitestore':
                $enable_albums = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
                if ($enable_albums) {

                    //TOTAL ALBUMS
                    $albumCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestore', 'albums');
                    $photoCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'spcreate');
                    if (empty($photoCreate) && empty($albumCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorealbum")) {
                            break;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'spcreate');
                        if (empty($isStoreOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitestore_photo_view');
                }
                break;
            case 'sitestore.discussion-sitestore':
                $enable_discussions = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion');
                if ($enable_discussions) {

                    //TOTAL TOPICS
                    $discussionsCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestore', 'topics');
                    $topicComment = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdicreate');
                    if (empty($topicComment) && empty($discussionsCount)) {
                        break;
                    }

                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorediscussion")) {
                            break;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sdicreate');
                        if (empty($isStoreOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitestores_discussion');
                }
                break;
            case 'sitestorenote.profile-sitestorenotes':
                $enable_notes = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote');
                if ($enable_notes) {
                    //TOTAL NOTES
                    $notesCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestorenote', 'notes');
                    $noteCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sncreate');
                    if (empty($noteCreate) && empty($notesCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorenote")) {
                            break;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sncreate');
                        if (empty($isStoreOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitestorenote_note');
                }
                break;
            case 'sitestoreevent.profile-sitestoreevents':
                $enable_events = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent');
                if ($enable_events) {
                    //TOTAL EVENTS
                    $eventCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestoreevent', 'events');
                    $eventCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'secreate');
                    if (empty($eventCreate) && empty($eventCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreevent")) {
                            break;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'secreate');
                        if (empty($isStoreOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitestoreevent');
                }
                break;
            case 'siteevent.contenttype-events':
                $enable_events = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
                if ($enable_events) {
                    $eventCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'siteevent', 'events');
                    $eventCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'secreate');
                    if (empty($eventCreate) && empty($eventCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreevent")) {
                            break;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'secreate');
                        if (empty($isStoreOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitestoreevent');
                }
                break;
            case 'sitevideo.contenttype-videos':
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo") == 1) {
                            $flag = true;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
                        if (!empty($isStoreOwnerAllow)) {
                            $flag = true;
                        }
                    }
                    $videoCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitevideo', 'videos');
                    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
                    if (!empty($isManageAdmin) || !empty($videoCount)) {
                        $flag = true;
                    } else {
                        $flag = false;
                    }
                }
                break;
            case 'document.contenttype-documents':
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoredocument") == 1) {
                            $flag = true;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sdcreate');
                        if (!empty($isStoreOwnerAllow)) {
                            $flag = true;
                        }
                    }
                    $documentCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'document', 'documents');
                    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdcreate');
                    if (!empty($isManageAdmin) || !empty($documentCount)) {
                        $flag = true;
                    } else {
                        $flag = false;
                    }
                }
                break;
            case 'sitestorepoll.profile-sitestorepolls':
                $enable_polls = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll');
                if ($enable_polls) {
                    //TOTAL POLLS
                    $pollCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestorepoll', 'polls');
                    $pollCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'splcreate');
                    if (empty($pollCreate) && empty($pollCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorepoll")) {
                            break;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'splcreate');
                        if (empty($isStoreOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'item_icon_sitestorepoll');
                }
                break;
            case 'sitestorevideo.profile-sitestorevideos':
                $enable_videos = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo');
                if ($enable_videos) {
                    //TOTAL VIDEOS
                    $videoCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestorevideo', 'videos');
                    $videoCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
                    if (empty($videoCreate) && empty($videoCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorevideo")) {
                            break;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'svcreate');
                        if (empty($isStoreOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_type_sitestorevideo');
                }
                break;
            case 'sitestoredocument.profile-sitestoredocuments':
                $enable_documents = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument');
                if ($enable_documents) {
                    //TOTAL DOCUMENTS
                    $documentCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestoredocument', 'documents');
                    $documentCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdcreate');
                    if (empty($documentCreate) && empty($documentCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoredocument")) {
                            break;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sdcreate');
                        if (empty($isStoreOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'item_icon_sitestoredocument_detail');
                }
                break;
            case 'sitestorereview.profile-sitestorereviews':
                $enable_reviews = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview');
                if ($enable_reviews) {
                    //TOTAL REVIEW
                    $reviewCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestorereview', 'reviews');
                    $level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestorereview_review', 'create');
                    if (empty($level_allow) && empty($reviewCount)) {
                        break;
                    }
                    $content_array = array($widgettitle, 'icon_sitestores_review');
                }
                break;
            case 'sitestore.location-sitestore':
                $check_location = Engine_Api::_()->sitestore()->enableLocation();
                $value['id'] = $store_id;
                $location = Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocation($value);
                $isManageAdmin = 0;
                $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'map');
                if ($check_location && $location && !empty($isManageAdmin)) {
                    $content_array = array($widgettitle, 'icon_sitestores_map');
                }
                break;
            case 'sitestore.overview-sitestore':
                $isManageAdmin = 0;
                $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'overview');
                if (empty($isManageAdmin) || empty($can_edit) && empty($sitestore->overview))
                    break;
                $content_array = array($widgettitle, 'icon_sitestores_overview');
                break;
            case 'sitestoreform.sitestore-viewform':
                $enable_forms = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform');
                if ($enable_forms) {
                    $quetion = Engine_Api::_()->getDbtable('storequetions', 'sitestoreform');
                    $result_quetion = $quetion->fetchRow($quetion->select()->where('store_id = ?', $store_id));
                    $option_id = $result_quetion->option_id;
                    $itestoreforms_table = Engine_Api::_()->getDbtable('sitestoreforms', 'sitestoreform');
                    $select_sitestoreform_result = $itestoreforms_table->fetchRow($itestoreforms_table->select()->where('store_id = ?', $store_id));
                    if (!empty($option_id)) {
                        if ($select_sitestoreform_result->status == 0 || $select_sitestoreform_result->storeformactive == 0) {
                            break;
                        }
                        //PACKAGE BASE PRIYACY START
                        if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                            if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreform")) {
                                break;
                            }
                        } else {
                            $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'form');
                            if (empty($isStoreOwnerAllow)) {
                                break;
                            }
                        }
                        //PACKAGE BASE PRIYACY END
                        $content_array = array($widgettitle, 'icon_sitestore_form');
                    }
                }
                break;
            case 'sitestoreoffer.profile-sitestoreoffers':
                $enable_offers = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer');
                if ($enable_offers) {

                    $can_edit = 1;
                    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
                    if (empty($isManageAdmin)) {
                        $can_edit = 0;
                    }

                    $can_offer = 1;
                    $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'offer');

                    if (empty($isManageAdmin)) {
                        $can_offer = 0;
                    }
                    $can_create_offer = '';

                    //OFFER CREATION AUTHENTICATION CHECK
                    if ($can_edit == 1 && $can_offer == 1) {
                        $this->view->can_create_offer = $can_create_offer = 1;
                    }
                    //TOTAL OFFER
                    $offerCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestoreoffer', 'offers');
                    if (empty($can_create_offer) && empty($offerCount)) {
                        break;
                    }

                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreoffer")) {
                            break;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'offer');
                        if (empty($isStoreOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'sitestoreoffer_type_offer');
                }
                break;
            case 'sitestoremusic.profile-sitestoremusic':
                $enable_musics = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic');
                if ($enable_musics) {
                    //TOTAL MUSIC
                    $musicCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestoremusic', 'playlists');
                    $musicCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'smcreate');
                    if (empty($musicCreate) && empty($musicCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoremusic")) {
                            break;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'smcreate');
                        if (empty($isStoreOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitestoremusic_music');
                }
                break;
            case 'sitestoremember.profile-sitestoremembers':
                $enable_member = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember');
                if ($enable_member) {
                    //TOTAL MEMBER
                    $memberCount = Engine_Api::_()->sitestore()->getTotalCount($store_id, 'sitestore', 'membership');
                    $memberCreate = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'smecreate');
                    if (empty($memberCreate) && empty($memberCount)) {
                        break;
                    }
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoremember")) {
                            break;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'smecreate');
                        if (empty($isStoreOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END
                    $content_array = array($widgettitle, 'icon_sitestore_member');
                }
                break;
            case 'sitestoreintegration.profile-items':
                $pieces = explode("_", $resource_type);
                $resourceType = $pieces[0] . '_' . $pieces[1];
                $listingTypeId = $pieces[2];
                $enable_sitestoreintegration = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
                if ($enable_sitestoreintegration) {
                    //PACKAGE BASE PRIYACY START
                    if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                        if (!Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", $resourceType)) {
                            break;
                        }
                    } else {
                        $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, $resourceType);
                        if (empty($isStoreOwnerAllow)) {
                            break;
                        }
                    }
                    //PACKAGE BASE PRIYACY END          
                    //PACKAGE BASE PRIYACY END
                    if ($resourceType == 'list_listing') {
                        $content_array = array($widgettitle, "item_icon_list", $resourceType);
                    } elseif ($resourceType == 'sitepage_page') {
                        $content_array = array($widgettitle, "item_icon_sitepage", $resourceType);
                    } else {
                        $content_array = array($widgettitle, "item_icon_sitereview_listtype_$listingTypeId", $resourceType);
                    }
                }
                break;

//         case 'sitestoretwitter.feeds-sitestoretwitter':
//         
// 					$enable_sitestoretwitter = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter');
// 					if ($enable_sitestoretwitter) {
// 						$isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'twitter');
// 					if (empty($isManageAdmin)) {
// 							break;
// 						}
// 						$content_array = array($widgettitle, 'icon_sitestoremusic_music');
// 					}
        }
        return $content_array;
    }

}

?>