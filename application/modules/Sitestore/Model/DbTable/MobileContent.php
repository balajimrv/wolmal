<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_MobileContent extends Engine_Db_Table {

  protected $_serializedColumns = array('params');


  /**
   * Set profile store default widget in user content table without tab
   *
   * @param string $name
   * @param int $store_id
   * @param string $title
   * @param int $titleCount
   * @param int $order
   */
  public function setDefaultInfo($name = null, $contentstore_id, $title = null, $titleCount = null, $order = null, $params = null) {
    $db = Engine_Db_Table::getDefaultAdapter();
    if (!empty($name)) {
      $select = $this->select();
      $select_content = $select
              ->from($this->info('name'))
              ->where('mobilecontentstore_id = ?', $contentstore_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', $name)
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = $this->select();
        $select_container = $select
                ->from($this->info('name'), array('mobilecontent_id'))
                ->where('mobilecontentstore_id = ?', $contentstore_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $select = $this->select();
          $container_id = $container[0]['content_id'];
          $select_middle = $select
                  ->from($this->info('name'))
                  ->where('parent_content_id = ?', $container_id)
                  ->where('type = ?', 'container')
                  ->where('name = ?', 'middle')
                  ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if (!empty($middle)) {
            $select = $this->select();
            $middle_id = $middle[0]['content_id'];
            $select_tab = $select
                    ->from($this->info('name'))
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'sitemobile.container-tabs-columns')
                    ->where('mobilecontentstore_id = ?', $contentstore_id)
                    ->limit(1);
            $tab = $select_tab->query()->fetchAll();
            $tab_id='';
            if (!empty($tab)) {
              $tab_id = $tab[0]['content_id'];
            } else {
              $contentWidget = $this->createRow();
              $contentWidget->mobilecontentstore_id = $contentstore_id;
              $contentWidget->type = 'widget';
              $contentWidget->name = 'sitemobile.container-tabs-columns';
              $contentWidget->parent_content_id = ($tab_id ? $tab_id : $middle_id);
              $contentWidget->order = $order;
              $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showmore', 9);
							$contentWidget->params = "{\"max\":\"$showmaxtab\"}";
              $contentWidget->save();
            }
            if ($name != 'sitestoreintegration.profile-items') {
              $contentWidget = $this->createRow();
              $contentWidget->mobilecontentstore_id = $contentstore_id;
              $contentWidget->type = 'widget';
              $contentWidget->name = $name;
              $contentWidget->parent_content_id = ($tab_id ? $tab_id : $middle_id);
              $contentWidget->order = $order;
							if($params) {
								$contentWidget->params = $params;
							} else {
								$contentWidget->params = '{"title":"' . $title . '","titleCount":' . $titleCount . '}';
							}
              $contentWidget->save();
            } else {
              $select = new Zend_Db_Select($db);
              $select
                      ->from('engine4_core_modules')
                      ->where('name = ?', 'sitereview');
              $check_list = $select->query()->fetchObject();
              if (!empty($check_list)) {
                $results = Engine_Api::_()->getDbtable('mixsettings', 'sitestoreintegration')->getIntegrationItems();

                foreach ($results as $value) {
                   $item_title = $value['item_title'];
                   $resource_type = $value['resource_type']. '_'. $value['listingtype_id'];

                  // Check if it's already been placed
                  $select = new Zend_Db_Select($db);
                  $select
                          ->from('engine4_sitestore_content')
                          ->where('parent_content_id = ?', $tab_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitestoreintegration.profile-items')
                          ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"'.$resource_type.'","nomobile":"0","name":"sitestoreintegration.profile-items"}');
                  $info = $select->query()->fetch();
                  if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_sitestore_content', array(
                        'mobilecontentstore_id' => $contentstore_id,
                        'type' => 'widget',
                        'name' => 'sitestoreintegration.profile-items',
                        'parent_content_id' => $tab_id,
                        'order' => 999,
                        'params' => '{"title":"' . $item_title . '","resource_type":"'.$resource_type.'","nomobile":"0","name":"sitestoreintegration.profile-items"}',
                    ));
                  }
                  //}
                }
              }
              $this->tabstoreintwidgetlayout('document', '{"title":"Documents","resource_type":"document_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $contentstore_id);
              
              $this->tabstoreintwidgetlayout('sitegroup', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $contentstore_id);
              
              $this->tabstoreintwidgetlayout('sitepage', '{"title":"Pages","resource_type":"sitepage_page_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $contentstore_id);
              
              $this->tabstoreintwidgetlayout('list', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $contentstore_id);
              
              $this->tabstoreintwidgetlayout('folder', '{"title":"Folders","resource_type":"folder_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $contentstore_id);
              
              $this->tabstoreintwidgetlayout('quiz', '{"title":"Quiz","resource_type":"quiz_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $contentstore_id);
              
              $this->tabstoreintwidgetlayout('sitebusiness', '{"title":"Businesses","resource_type":"sitebusiness_business_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $contentstore_id);
              
              $this->tabstoreintwidgetlayout('sitefaq', '{"title":"FAQs","resource_type":"sitefaq_faq_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $contentstore_id);

              $this->tabstoreintwidgetlayout('sitetutorial', '{"title":"Tutorials","resource_type":"sitetutorial_tutorial_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $contentstore_id);
            }
          }
        }
      }
    }
  }

  /**
   * Gets content id,parama,name
   *
   * @param int $contentstore_id
   * @return content id,parama,name
   */
  public function getContents($mobilecontentstore_id) {

    $selectStoreAdmin = $this->select()
            ->from($this->info('name'), array('mobilecontent_id', 'params', 'name'))
            ->where('mobilecontentstore_id =?', $mobilecontentstore_id)
            ->where("name IN ('sitestore.overview-sitestore', 'sitestore.photos-sitestore', 'sitestore.discussion-sitestore', 'sitestorenote.profile-sitestorenotes', 'sitestorepoll.profile-sitestorepolls', 'sitestoreevent.profile-sitestoreevents', 'sitestorevideo.profile-sitestorevideos', 'sitestoreoffer.profile-sitestoreoffers', 'sitestorereview.profile-sitestorereviews', 'sitestoredocument.profile-sitestoredocuments', 'sitestoreform.sitestore-viewform','sitestore.info-sitestore', 'seaocore.feed','advancedactivity.home-feeds', 'activity.feed', 'sitestore.location-sitestore', 'core.profile-links', 'sitestoremusic.profile-sitestoremusic', 'sitestoremember.profile-sitestoremembers', 'sitestoreintegration.profile-items','sitestoretwitter.feeds-sitestoretwitter', 'sitestoreproduct.store-profile-products', 'siteevent.contenttype-events', 'sitevideo.contenttype-videos')");
    return $this->fetchAll($selectStoreAdmin);
  }

  /**
   * Gets content_id
   *
   * @param int $contentstore_id
   * @return $params
   */
  public function getContentId($mobilecontentstore_id, $sitestore) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = 0;
    }
    $itemAlbumCount = 10;
    $itemPhotoCount = 100;
    $select = $this->select();
    $select_content = $select
            ->from($this->info('name'))
            ->where('mobilecontentstore_id = ?', $mobilecontentstore_id)
            ->where('type = ?', 'container')
            ->where('name = ?', 'main')
            ->limit(1);
    $content = $select_content->query()->fetchAll();
    if (!empty($content)) {
      $select = $this->select();
      $select_container = $select
              ->from($this->info('name'), array('mobilecontent_id'))
              ->where('mobilecontentstore_id = ?', $mobilecontentstore_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->where("name NOT IN ('	sitestore.title-sitestore', 'seaocore.like-button', 'sitestore.photorecent-sitestore')")
              ->limit(1);
      $container = $select_container->query()->fetchAll();
      if (!empty($container)) {
        $select = $this->select();
        $container_id = $container[0]['mobilecontent_id'];
        $select_middle = $select
                ->from($this->info('name'))
                ->where('parent_content_id = ?', $container_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'core.container-tabs')
                ->where('mobilecontentstore_id = ?', $mobilecontentstore_id)
                ->limit(1);
        $middle = $select_middle->query()->fetchAll();
        if (!empty($middle)) {
          $mobilecontent_id = $middle[0]['mobilecontent_id'];
        } else {
          $mobilecontent_id = $container_id;
        }
      }
    }

    if (!empty($mobilecontent_id)) {
      $select = $this->select();
      $select_middle = $select
              ->from($this->info('name'), array('mobilecontent_id', 'name', 'params'))
              ->where('parent_content_id = ?', $mobilecontent_id)
              ->where('type = ?', 'widget')
              ->where("name NOT IN ('sitestore.title-sitestore', 'seaocore.like-button', 'sitestore.photorecent-sitestore', 'Facebookse.facebookse-sitestoreprofilelike', 'sitestore.thumbphoto-sitestore')")
              ->where('mobilecontentstore_id = ?', $mobilecontentstore_id)
              ->order('order')
      ;

      $select = $this->select();
      $select_photo = $select
              ->from($this->info('name'), array('params'))
              ->where('parent_content_id = ?', $mobilecontent_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitestore.photos-sitestore')->where('contentstore_id = ?', $contentstore_id)
              ->order('mobilecontent_id ASC');

      $middlePhoto = $select_photo->query()->fetchColumn();
      if (!empty($middlePhoto)) {
        $photoParamsDecodedArray = Zend_Json_Decoder::decode($middlePhoto);
        if (isset($photoParamsDecodedArray['itemCount']) && !empty($photoParamsDecodedArray)) {
          $itemAlbumCount = $photoParamsDecodedArray['itemCount'];
        }
        if (isset($photoParamsDecodedArray['itemCount_photo']) && !empty($photoParamsDecodedArray)) {
          $itemPhotoCount = $photoParamsDecodedArray['itemCount_photo'];
        }
      }
      $middle = $select_middle->query()->fetchAll();
      $editpermission = '';
      $isManageAdmin = '';
      $mobilecontent_ids = '';
      $content_names = '';
      $resource_type_integration = 0;
      $ads_display_integration = 0;
      $flag = false;
      foreach ($middle as $value) {
        $content_name = $value['name'];
        switch ($content_name) {
          case 'sitestore.overview-sitestore':
            if (!empty($sitestore)) {
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'overview');
              if (!empty($isManageAdmin)) {
                $editpermission = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'edit');
                if (!empty($editpermission) && empty($sitestore->overview)) {
                  $flag = true;
                } elseif (empty($editpermission) && empty($sitestore->overview)) {
                  $flag = false;
                } elseif (!empty($editpermission) && !empty($sitestore->overview)) {
                  $flag = true;
                } elseif (empty($editpermission) && !empty($sitestore->overview)) {
                  $flag = true;
                }
              }
            }
            break;
          case 'sitestore.location-sitestore':
            if (!empty($sitestore)) {
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'map');
              if (!empty($isManageAdmin)) {
                $value['id'] = $sitestore->getIdentity();
                $location = Engine_Api::_()->getDbtable('locations', 'sitestore')->getLocation($value);
                if (!empty($location)) {
                  $flag = true;
                }
              }
            }
            break;
          case 'core.html-block':
            $flag = true;
            break;
          case 'activity.feed':
            $flag = true;
            break;
          case 'seaocore.feed':
            $flag = true;
            break;
          case 'advancedactivity.home-feeds':
            $flag = true;
            break;
          case 'sitestore.info-sitestore':
            $flag = true;
            break;
          case 'core.profile-links':
            $flag = true;
            break;
          case 'sitestorenote.profile-sitestorenotes':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote')) {
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorenote") == 1) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sncreate');
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
              //TOTAL NOTES
              $noteCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestorenote', 'notes');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sncreate');
              if (!empty($isManageAdmin) || !empty($noteCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitestoreevent.profile-sitestoreevents':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')) {
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreevent") == 1) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'secreate');
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
              //TOTAL EVENTS
              $eventCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestoreevent', 'events');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'secreate');
              if (!empty($isManageAdmin) || !empty($eventCount)) {
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
              //TOTAL EVENTS
							$videoCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitevideo', 'videos');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
              if (!empty($isManageAdmin) || !empty($videoCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
					case 'siteevent.contenttype-events':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreevent") == 1) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'secreate');
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
              //TOTAL EVENTS
							$eventCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'siteevent', 'events');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'secreate');
              if (!empty($isManageAdmin) || !empty($eventCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitestore.discussion-sitestore':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorediscussion") == 1) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'sdicreate');
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
              //TOTAL TOPICS
              $topicCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestore', 'topics');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdicreate');
              if (!empty($isManageAdmin) || !empty($topicCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitestore.photos-sitestore':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorealbum") == 1) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'spcreate');
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
              //TOTAL ALBUMS
              $albumCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestore', 'albums');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'spcreate');
              if (!empty($isManageAdmin) || !empty($albumCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitestoremusic.profile-sitestoremusic':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoremusic") == 1) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'smcreate');
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
              //TOTAL PLAYLISTS
              $musicCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestoremusic', 'playlists');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'smcreate');
              if (!empty($isManageAdmin) || !empty($musicCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;

          case 'sitestoremember.profile-sitestoremembers':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoremember") == 1) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'smecreate');
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
              //TOTAL PLAYLISTS
              $memberCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestorem', 'membership');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'smecreate');
              if (!empty($isManageAdmin) || !empty($memberCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;

          case 'sitestoredocument.profile-sitestoredocuments':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')) {
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
              //TOTAL DOCUMENTS
              $documentCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestoredocument', 'documents');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'sdcreate');
              if (!empty($isManageAdmin) || !empty($documentCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitestorereview.profile-sitestorereviews':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
              //TOTAL REVIEW
              $reviewCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestorereview', 'reviews');
              $level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitestorereview_review', 'create');
              if (!empty($level_allow) || !empty($reviewCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitestorevideo.profile-sitestorevideos':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')) {
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
              //TOTAL VIDEO
              $videoCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestorevideo', 'videos');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'svcreate');
              if (!empty($isManageAdmin) || !empty($videoCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitestorepoll.profile-sitestorepolls':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll')) {
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestorepoll") == 1) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'splcreate');
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
              //TOTAL POLL
              $pollCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestorepoll', 'polls');
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'splcreate');
              if (!empty($isManageAdmin) || !empty($pollCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitestoreoffer.profile-sitestoreoffers':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreoffer") == 1) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'offer');
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
              //TOTAL OFFERS
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
                $can_create_offer = 1;
              }

              //TOTAL OFFER
              $offerCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestoreoffer', 'offers');
              if (!empty($can_create_offer) || !empty($offerCount)) {
                $flag = true;
              } else {
                $flag = false;
              }
            }
            break;
          case 'sitestoreform.sitestore-viewform':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform')) {
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", "sitestoreform") == 1) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, 'form');
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
            }
            break;
          case 'sitestoreintegration.profile-items':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration')) {
              $content_params = $value['params'];
              $paramsDecodedArray = Zend_Json_Decoder::decode($content_params);
              $resource_type_integration = $paramsDecodedArray['resource_type'];
              $ads_display_integration = Engine_Api::_()->getApi('settings', 'core')->getSetting("sitestore.ad.$resource_type_integration", 3);

              //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY START
              if (Engine_Api::_()->sitestore()->hasPackageEnable()) {
                if (Engine_Api::_()->sitestore()->allowPackageContent($sitestore->package_id, "modules", $resource_type_integration)) {
                  $flag = true;
                }
              } else {
                $isStoreOwnerAllow = Engine_Api::_()->sitestore()->isStoreOwnerAllow($sitestore, $resource_type_integration);
                if (!empty($isStoreOwnerAllow)) {
                  $flag = true;
                }
              }
              //PACKAGE BASE AND MEMBER LEVEL SETTINGS PRIYACY END
            }
            break;
          case 'sitestoretwitter.feeds-sitestoretwitter':
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) {
              $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($sitestore, 'twitter');
              if (!empty($isManageAdmin)) {
                $flag = true;
              }
            }
            break;
          case 'sitestoreproduct.store-profile-products':
            $flag = true;
           break;
        }

        if (!empty($flag)) {
          $mobilecontent_ids = $value['mobilecontent_id'];
          $content_names = $value['name'];
          break;
        }
      }
    }

    return array('content_id' => $mobilecontent_ids, 'content_name' => $content_names, 'itemAlbumCount' => $itemAlbumCount, 'itemPhotoCount' => $itemPhotoCount, 'resource_type_integration' => $resource_type_integration, 'ads_display_integration' => $ads_display_integration);
  }

  /**
   * Gets content_id, name
   *
   * @param int $contentstore_id
   * @return content_id, name
   */
  public function getContentInformation($mobilecontentstore_id) {
    $select = $this->select()->from($this->info('name'), array('mobilecontent_id', 'name'))
                    ->where("name IN ('sitestore.info-sitestore', 'seaocore.feed', 'advancedactivity.home-feeds','activity.feed', 'sitestore.location-sitestore', 'core.profile-links', 'core.html-block')")
                    ->where('mobilecontentstore_id = ?', $mobilecontentstore_id)->order('mobilecontent_id ASC');

    return $this->fetchAll($select);
  }

  /**
   * Gets content_id, name
   *
   * @param int $contentstore_id
   * @param int $name 
   * @return content_id, name
   */
  public function getContentByWidgetName($name, $mobilecontentstore_id) {
    $select = $this->select()->from($this->info('name'), array('mobilecontent_id', 'name'))
            ->where('name =?', $name)
            ->where('mobilecontentstore_id = ?', $mobilecontentstore_id)
            ->limit(1)
    ;
    return $this->fetchAll($select)->toarray();
  }

  /**
   * Gets name
   *
   * @param int $tab_main
   * @return name
   */
  public function getCurrentTabName($tab_main = null) {
    if (empty($tab_main)) {
      return;
    }
    $current_tab_name = $this->select()
            ->from($this->info('name'), array('name'))
            ->where('mobilecontent_id = ?', $tab_main)
            ->query()
            ->fetchColumn();
    return $current_tab_name;
  }

  public function tabstoreintwidgetlayout($module_name, $params, $tab_id, $store_id) {

    $db = Engine_Db_Table::getDefaultAdapter();
		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_modules')
						->where('name = ?', $module_name);
		$module_enable = $select->query()->fetchObject();
		
		if (!empty($module_enable)) {
		
			$results = Engine_Api::_()->getDbtable('mixsettings', 'sitestoreintegration')->getIntegrationItems();

			foreach ($results as $value) {

				// Check if it's already been placed
				$select = new Zend_Db_Select($db);
				$select
								->from('engine4_sitestore_mobilecontent')
								->where('parent_content_id = ?', $tab_id)
								->where('type = ?', 'widget')
								->where('name = ?', 'sitestoreintegration.profile-items')
								->where('params = ?', $params);
				$info = $select->query()->fetch();
				if (empty($info)) {

					// tab on profile
					$db->insert('engine4_sitestore_mobilecontent', array(
							'mobilecontentstore_id' => $store_id,
							'type' => 'widget',
							'name' => 'sitestoreintegration.profile-items',
							'parent_content_id' => $tab_id,
							'order' => 999,
							'params' => $params,
					));
				}
			}
		}
  }

}

?>