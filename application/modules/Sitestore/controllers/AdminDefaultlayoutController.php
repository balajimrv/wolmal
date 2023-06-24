<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminDefaultlayoutController.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_AdminDefaultlayoutController extends Core_Controller_Action_Admin {

  //ACTION FOR SETTING THE DEFAULT LAYOUT
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitestore_admin_main', array(), 'sitestore_admin_main_layoutdefault');

    //FORM   
    $this->view->form = $form = new Sitestore_Form_Admin_Layoutdefault();

    //FORM   
//    if(!Engine_Api::_()->getApi("settings", "core")->getSetting('sitestore.layout.coverphotoenabled', 0)) {
//			$this->view->coverForm = $coverForm = new Sitestore_Form_Admin_CoverPhotoLayout();
//    }

    //GET STORE PROFILE STORE INFO
    $selectStore = Engine_Api::_()->sitestore()->getWidgetizedStore();
    if (!empty($selectStore)) {
      $this->view->store_id = $selectStore->page_id;
    }

    if (Engine_Api::_()->sitestore()->getMobileWidgetizedStore()) {
      $this->view->mobile_store_id = Engine_Api::_()->sitestore()->getMobileWidgetizedStore()->page_id;
    }


    //FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) || isset($_GET['store_reload'])) {
      //GET FORM VALUES
      $values = $form->getValues();

      //GET ADMIN CONTENT TABLE
      $contentTable = Engine_Api::_()->getDbtable('admincontent', 'sitestore');

      //GET ADMIN CONTENT TABLE NAME
      $contentTableName = $contentTable->info('name');

      //GET SITESTORE CONTENT TABLE
      $sitestorecontentTable = Engine_Api::_()->getDbtable('content', 'sitestore');

      //CORE CONTENT TABLE
      $corecontentTable = Engine_Api::_()->getDbtable('content', 'core');

      //GET SITESTORE CONTENT STORES TABLE
      $sitestorestoreTable = Engine_Api::_()->getDbtable('contentstores', 'sitestore');

      //GET HIDE PROFILE WODGET TABLE   
      $hideprofilewidgetsTable = Engine_Api::_()->getDbtable('hideprofilewidgets', 'sitestore');

      //DELETING THE OLD ENTRIES
      $hideprofilewidgets = $hideprofilewidgetsTable->select()->query()->fetchAll();
      if (!empty($hideprofilewidgets)) {
        foreach ($hideprofilewidgets as $data) {
          $hideprofilewidgetsTable->delete(array('hideprofilewidgets_id =?' => $data['hideprofilewidgets_id']));
        }
      }
      $totalStores = $sitestorestoreTable->select()
                      ->from($sitestorestoreTable->info('name'), array('count(*) as count'))
                      ->where('name =?', 'sitestore_index_view')->query()->fetchColumn();
      if (isset($_POST['sitestore_sitestore_layout_setting'])) {
        $layout_option = $_POST['sitestore_sitestore_layout_setting'];
      } else {
        $layout_option = $_GET['sitestore_sitestore_layout_setting'];
      }
      $limit = 300;
      $sitestore_layout_cover_photo = 1;
      if(isset($_POST['sitestore_layout_cover_photo'])) {
        $sitestore_layout_cover_photo = $_POST['sitestore_layout_cover_photo'];
      }
      
      $reload_count = round($totalStores / $limit);
      $store_reload = $this->_getParam('store_reload', 1);
      $offset = ($store_reload - 1) * $limit;

      $selectsitestoreStore = $sitestorestoreTable->select()
              ->from($sitestorestoreTable->info('name'), array('contentstore_id'))
              ->where('name =?', 'sitestore_index_view')
              ->limit($limit, $offset);
      $contentstores_id = $selectsitestoreStore->query()->fetchAll();
      foreach ($contentstores_id as $key => $value) {
        $sitestorecontentTable->delete(array('contentstore_id =?' => $value['contentstore_id']));
        if (empty($layout_option)) {
          Engine_Api::_()->getDbtable('content', 'sitestore')->setWithoutTabLayout($value['contentstore_id'], $sitestore_layout_cover_photo);
        } else {
          Engine_Api::_()->getDbtable('content', 'sitestore')->setTabbedLayout($value['contentstore_id'], $sitestore_layout_cover_photo);
        }
      }

      if ($store_reload == 1) {
        if (!empty($layout_option)) {
          Engine_Api::_()->getApi("settings", "core")->setSetting('sitestore.layout.setting', $layout_option);
          include_once APPLICATION_PATH . '/application/modules/Sitestore/controllers/AdminviewstorewidgetController.php';
          if (!empty($selectStore)) {
            $store_id = $selectStore->page_id;
            $contentTable->delete(array('store_id =?' => $store_id));
            Engine_Api::_()->getApi('layoutcore', 'sitestore')->setTabbedLayoutContent($store_id, $sitestore_layout_cover_photo);
          }

          if (!empty($store_id)) {
            //INSERT MAIN CONTAINER
            $mainContainer = $contentTable->createRow();
            $mainContainer->store_id = $store_id;
            $mainContainer->type = 'container';
            $mainContainer->name = 'main';
            $mainContainer->order = 2;
            $mainContainer->save();
            $container_id = $mainContainer->admincontent_id;

            //INSERT MAIN-MIDDLE CONTAINER
            $mainMiddleContainer = $contentTable->createRow();
            $mainMiddleContainer->store_id = $store_id;
            $mainMiddleContainer->type = 'container';
            $mainMiddleContainer->name = 'middle';
            $mainMiddleContainer->parent_content_id = $container_id;
            $mainMiddleContainer->order = 6;
            $mainMiddleContainer->save();
            $middle_id = $mainMiddleContainer->admincontent_id;

            //INSERT MAIN-LEFT CONTAINER
            $mainLeftContainer = $contentTable->createRow();
            $mainLeftContainer->store_id = $store_id;
            $mainLeftContainer->type = 'container';
            $mainLeftContainer->name = 'right';
            $mainLeftContainer->parent_content_id = $container_id;
            $mainLeftContainer->order = 4;
            $mainLeftContainer->save();
            $left_id = $mainLeftContainer->admincontent_id;
            $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showmore', 9);

            //INSERT MAIN-MIDDLE TAB CONTAINER
            $middleTabContainer = $contentTable->createRow();
            $middleTabContainer->store_id = $store_id;
            $middleTabContainer->type = 'widget';
            $middleTabContainer->name = 'core.container-tabs';
            $middleTabContainer->parent_content_id = $middle_id;
            $middleTabContainer->order = 10;
            $middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
            $middleTabContainer->save();
            $middle_tab = $middleTabContainer->admincontent_id;
						
            if(empty($sitestore_layout_cover_photo)) {
              Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');
              
							//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
              if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')){
								Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoremember.storecover-photo-sitestoremembers', $middle_id, 2, '', 'true');
              }

							//INSERTING TITLE WIDGET
							Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.title-sitestore', $middle_id, 4, '', 'true');
													
							//INSERTING LIKE WIDGET
							Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.like-button', $middle_id, 5, '', 'true');
            
							//INSERTING FOLLOW WIDGET
							Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.seaocore-follow', $middle_id, 6,'','true');

							//INSERTING FACEBOOK LIKE WIDGET
							if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
								Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'Facebookse.facebookse-sitestoreprofilelike', $middle_id, 7, '', 'true');
							}

							//INSERTING MAIN PHOTO WIDGET 
							Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.mainphoto-sitestore', $left_id, 10, '', 'true');

            } else {
							//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
							Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');
							
							Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-cover-information-sitestore', $middle_id, 2, '', 'true');
            }

            //INSERTING CONTACT DETAIL WIDGET
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.contactdetails-sitestore', $middle_id, 8, '', 'true');

						Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreproduct.sitestoreproduct-products', $left_id, 11, '', 'true', '{"title":"Top Selling Products","titleCount":true,"statistics":"","viewType":"gridview","columnWidth":"180","popularity":"last_order_all","product_type":"all","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","ratingType":"rating_avg","columnHeight":"328","itemCount":"3","truncation":"16","nomobile":"0","name":"sitestoreproduct.sitestoreproduct-products"}');

            //INSERTING OPTIONS WIDGET
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.options-sitestore', $left_id, 12, '', 'true');

            //INSERTING INFORMATION WIDGET 
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.information-sitestore', $left_id, 10, 'Information', 'true');

            //INSERTING WRITE SOMETHING ABOUT WIDGET 
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

            //INSERTING RATING WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
              Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.ratings-sitestorereviews', $left_id, 16, 'Ratings', 'true');
            }
						
            //INSERTING BADGE WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge')) {
              Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorebadge.badge-sitestorebadge', $left_id, 17, 'Badge', 'true');
            }

            //INSERTING YOU MAY ALSO LIKE WIDGET 
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.suggestedstore-sitestore', $left_id, 18, 'You May Also Like', 'true');

            $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitestore.socialshare-sitestore"}';

            //INSERTING SOCIAL SHARE WIDGET 
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.socialshare-sitestore', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

//            //INSERTING FOUR SQUARE WIDGET 
//            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.foursquare-sitestore', $left_id, 20, '', 'true');

            //INSERTING INSIGHTS WIDGET 
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.insights-sitestore', $left_id, 21, 'Insights', 'true');

            //INSERTING FEATURED OWNER WIDGET 
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.featuredowner-sitestore', $left_id, 22, 'Owners', 'true');

            //INSERTING ALBUM WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
              Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.albums-sitestore', $left_id, 23, 'Albums', 'true');
            }

            //INSERTING STORE PROFILE PLAYER WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
              Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoremusic.profile-player', $left_id, 24, '', 'true');
            }

            //INSERTING LINKED STORES WIDGET
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.favourite-store', $left_id, 25, 'Linked Stores', 'true');

						if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct')) {
							Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreproduct.store-profile-products', $middle_tab, 1, 'Products', 'true', '{"columnHeight":325,"columnWidth":165,"defaultWidgetNo":13}');
						}

            //INSERTING ACTIVITY FEED WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
              $advanced_activity_params =
                      '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
              Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true', $advanced_activity_params);
            } else {
              Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'activity.feed', $middle_tab, 2, 'Updates', 'true');
            }

            //INSERTING INFORAMTION WIDGET
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.info-sitestore', $middle_tab, 3, 'Info', 'true');

            //INSERTING OVERVIEW WIDGET
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.overview-sitestore', $middle_tab, 4, 'Overview', 'true');

            //INSERTING LOCATION WIDGET
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.location-sitestore', $middle_tab, 5, 'Map', 'true');

            //INSERTING LINKS WIDGET
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');

            //INSERTING ALBUM WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestore.photos-sitestore', $store_id, 'Photos', 'true', '110');
            }

            //INSERTING VIDEO WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestorevideo.profile-sitestorevideos', $store_id, 'Videos', 'true', '111');
            }
            
             if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
                Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitevideo.contenttype-videos', $store_id, 'Videos', 'true', '117');
            }

            //INSERTING NOTE WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestorenote.profile-sitestorenotes', $store_id, 'Notes', 'true', '112');
            }

            //INSERTING REVIEW WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestorereview.profile-sitestorereviews', $store_id, 'Reviews', 'true', '113');
            }

            //INSERTING FORM WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestoreform.sitestore-viewform', $store_id, 'Form', 'false', '114');
            }

            //INSERTING DOCUMENT WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestoredocument.profile-sitestoredocuments', $store_id, 'Documents', 'true', '115');
            }
            
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
                Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('document.contenttype-documents', $store_id, 'Documents', 'true', '115');
            }

            //INSERTING OFFER WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestoreoffer.profile-sitestoreoffers', $store_id, 'Coupons', 'true', '116');
            }

            //INSERTING EVENT WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestoreevent.profile-sitestoreevents', $store_id, 'Events', 'true', '117');
            }

						//INSERTING EVENT WIDGET 
						if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
							Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('siteevent.contenttype-events', $store_id, 'Events', 'true', '117');
						}

            //INSERTING POLL WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestorepoll.profile-sitestorepolls', $store_id, 'Polls', 'true', '118');
            }

            //INSERTING DISCUSSION WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestore.discussion-sitestore', $store_id, 'Discussions', 'true', '119');
            }

            //INSERTING MUSIC WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestoremusic.profile-sitestoremusic', $store_id, 'Music', 'true', '120');
            }

            //INSERTING TWITTER WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestoretwitter.feeds-sitestoretwitter', $store_id, 'Twitter', 'true', '121');
            }

						if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
							Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestoremember.profile-sitestoremembers', $store_id, 'Members', 'true', '122');
							Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestoremember.profile-sitestoremembers-announcements', $store_id, 'Announcements', 'true', '123');
						}

            //INSERTING INTEGRATION WIDGET 
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration')) {
              Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestoreintegration.profile-items', $store_id, '', '', '999');
            }

          }
        } else {
          //IF EMPTY THEN MAKE STORE PROFILE STORE
            $storeTable = Engine_Api::_()->getDbtable('pages', 'core');
          if (empty($selectStore)) {
            $storeCreate = $storeTable->createRow();
            $storeCreate->name = 'sitestore_index_view';
            $storeCreate->displayname = 'Stores - Store Profile';
            $storeCreate->title = 'Store Profile';
            $storeCreate->description = 'This is the store view page.';
            $storeCreate->custom = 0;
            $current_store_id = $storeCreate->save();
          } else {
            $current_store_id = $selectStore->page_id;
          }

          if (!empty($current_store_id)) {
            $corecontentTable->delete(array('page_id =?' => $current_store_id));
            Engine_Api::_()->getApi('layoutcore', 'sitestore')->setWithoutTabContent($current_store_id, $sitestore_layout_cover_photo);
          }

          if (!empty($selectStore)) {
            $store_id = $selectStore->page_id;
            $contentTable->delete(array('store_id =?' => $store_id));
          }
          //INSERT MAIN CONTAINER
          $mainContainer = $contentTable->createRow();
          $mainContainer->store_id = $store_id;
          $mainContainer->type = 'container';
          $mainContainer->name = 'main';
          $mainContainer->order = 2;
          $mainContainer->save();
          $container_id = $mainContainer->admincontent_id;

          //INSERT MAIN-MIDDLE CONTAINER.
          $mainMiddleContainer = $contentTable->createRow();
          $mainMiddleContainer->store_id = $store_id;
          $mainMiddleContainer->type = 'container';
          $mainMiddleContainer->name = 'middle';
          $mainMiddleContainer->parent_content_id = $container_id;
          $mainMiddleContainer->order = 6;
          $mainMiddleContainer->save();
          $middle_id = $mainMiddleContainer->admincontent_id;

          //INSERT MAIN-LEFT CONTAINER.
          $mainLeftContainer = $contentTable->createRow();
          $mainLeftContainer->store_id = $store_id;
          $mainLeftContainer->type = 'container';
          $mainLeftContainer->name = 'right';
          $mainLeftContainer->parent_content_id = $container_id;
          $mainLeftContainer->order = 4;
          $mainLeftContainer->save();
          $left_id = $mainLeftContainer->admincontent_id;

          //INSERTING TITLE WIDGET
          if(empty($sitestore_layout_cover_photo)) {
          
							Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');
             
						//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
							Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoremember.storecover-photo-sitestoremembers', $middle_id, 2, '', 'true');
            }
						Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.title-sitestore', $middle_id, 3, '', 'true');

						//INSERTING LIKE WIDGET 
						Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.like-button', $middle_id, 4, '', 'true');

						//INSERTING FACEBOOK LIKE WIDGET
						if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
							Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'Facebookse.facebookse-sitestoreprofilelike', $middle_id, 5, '', 'true');
						} 

						//INSERTING MAIN PHOTO WIDGET 
						Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.mainphoto-sitestore', $left_id, 10, '', 'true');

          } else {
          
						Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');
          
						//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
						Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-cover-information-sitestore', $middle_id, 2, '', 'true');
          }

          //INSERTING CONTACT DETAIL WIDGET
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.contactdetails-sitestore', $middle_id, 5, '', 'true');

          //INSERTING ACTIVITY FEED WIDGET
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
            $advanced_activity_params =
                    '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'advancedactivity.home-feeds', $middle_id, 6, 'Updates', 'true', $advanced_activity_params);
          } else {
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'activity.feed', $middle_id, 6, 'Updates', 'true');
          }

          //INSERTING INFORAMTION WIDGET
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.info-sitestore', $middle_id, 7, 'Info', 'true');

          //INSERTING OVERVIEW WIDGET
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.overview-sitestore', $middle_id, 8, 'Overview', 'true');

          //INSERTING LOCATION WIDGET
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.location-sitestore', $middle_id, 9, 'Map', 'true');

          //INSERTING LINKS WIDGET  
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'core.profile-links', $middle_id, 125, 'Links', 'true');

          //INSERTING WIDGET LINK WIDGET 
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.widgetlinks-sitestore', $left_id, 11, '', 'true');

          //INSERTING OPTIONS WIDGET 
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.options-sitestore', $left_id, 12, '', 'true');

          //INSERTING WRITE SOMETHING ABOUT WIDGET 
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.write-store', $left_id, 13, '', 'true');

          //INSERTING INFORMATION WIDGET 
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.information-sitestore', $left_id, 10, 'Information', 'true');

          //INSERTING LIKE WIDGET 
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');

          //INSERTING RATING WIDGET 	
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.ratings-sitestorereviews', $left_id, 16, 'Ratings', 'true');
          }

          //INSERTING BADGE WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge')) {
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorebadge.badge-sitestorebadge', $left_id, 17, 'Badge', 'true');
          }

          $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitestore.socialshare-sitestore"}';

          //INSERTING SOCIAL SHARE WIDGET 
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.socialshare-sitestore', $left_id, 19, 'Social Share', 'true', $social_share_default_code);

//          //INSERTING FOUR SQUARE WIDGET 
//          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.foursquare-sitestore', $left_id, 20, '', 'true');

          //INSERTING INSIGHTS WIDGET 
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.insights-sitestore', $left_id, 22, 'Insights', 'true');

          //INSERTING FEATURED OWNER WIDGET 
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.featuredowner-sitestore', $left_id, 23, 'Owners', 'true');

          //INSERTING ALBUM WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.albums-sitestore', $left_id, 24, 'Albums');
            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestore.photos-sitestore', $store_id, 'Photos', 'true', '110');
          }

          //INSERTING STORE PROFILE PLAYER WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
            Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoremusic.profile-player', $left_id, 25, '', 'true');
          }

          //INSERTING LINKED STORES WIDGET   
          Engine_Api::_()->sitestore()->setDefaultDataWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.favourite-store', $left_id, 26, 'Linked Stores', 'true');

          //INSERTING VIDEO WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestorevideo.profile-sitestorevideos', $store_id, 'Videos', 'true', '111');
          }
          
                                  
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
                Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitevideo.contenttype-videos', $store_id, 'Videos', 'true', '117');
            }

          //INSERTING NOTE WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestorenote.profile-sitestorenotes', $store_id, 'Notes', 'true', '112');
          }

          //INSERTING REVIEW WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestorereview.profile-sitestorereviews', $store_id, 'Reviews', 'true', '113');
          }

          //INSERTING FORM WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestoreform.sitestore-viewform', $store_id, 'Form', 'false', '114');
          }

          //INSERTING DOCUMENT WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestoredocument.profile-sitestoredocuments', $store_id, 'Documents', 'true', '115');
          }

          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
                Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('document.contenttype-documents', $store_id, 'Documents', 'true', '115');
            }
            
          //INSERTING OFFER WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestoreoffer.profile-sitestoreoffers', $store_id, 'Coupons', 'true', '116');
          }

          //INSERTING EVENT WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestoreevent.profile-sitestoreevents', $store_id, 'Events', 'true', '117');
          }

					//INSERTING EVENT WIDGET 
					if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
						Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('siteevent.contenttype-events', $store_id, 'Events', 'true', '117');
					}

          //INSERTING POLL WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestorepoll.profile-sitestorepolls', $store_id, 'Polls', 'true', '118');
          }

          //INSERTING DISCUSSION WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestore.discussion-sitestore', $store_id, 'Discussions', 'true', '119');
          }

          //INSERTING MUSIC WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestoremusic.profile-sitestoremusic', $store_id, 'Music', 'true', '120');
          }
          //INSERTING TWITTER WIDGET 
          if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) {
            Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestoretwitter.feeds-sitestoretwitter', $store_id, 'Twitter', 'true', '121');
          }

					if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
						Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestoremember.profile-sitestoremembers', $store_id, 'Members', 'true', '122');
						Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestoremember.profile-sitestoremembers-announcements', $store_id, 'Announcements', 'true', '123');
					}

					//INSERTING INTEGRATION WIDGET 
					if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration')) {
						Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminContentDefaultInfoWithoutTab('sitestoreintegration.profile-items', $store_id, '', '', '999');
					}
        }
      }
      Engine_Api::_()->getApi("settings", "core")->setSetting('sitestore.layout.setting', $layout_option);
      Engine_Api::_()->getApi("settings", "core")->setSetting('sitestore.layout.coverphotoenabled', 1);

      Engine_Api::_()->getApi("settings", "core")->setSetting('sitestore.layout.cover.photo', $sitestore_layout_cover_photo);
      if ($store_reload < $reload_count) {
        $store_reload++;
        $this->_redirect("admin/sitestore/defaultlayout/index?store_reload=$store_reload&sitestore_sitestore_layout_setting=$layout_option");
      }
      
      $view = Zend_Registry::get('Zend_View');
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => false,
          'redirect' => $this->view->url(array('module' => 'sitestore', 'controller' => 'defaultlayout'), 'admin_default', true),
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('<div class="tip" style="margin:10px auto;width:750px;"><span>Please do not close this store or navigate to another store till you see a default layout changes completion or error message.</span></div><div>
					<center><img src="'.$view->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/layout/uploading.gif" alt="" /></center>
				</div>'))
      ));
    }
  }

  //ACTION FOR SAVING THE LAYOUT
  public function savelayoutAction() {

    $this->_helper->layout->setLayout('admin-simple');
  }

  public function saveCoverStoreLayoutAction() {
   

    if(Engine_Api::_()->getApi("settings", "core")->getSetting('sitestore.layout.coverphotoenabled', 0) && $this->getRequest()->isPost())
      return;
      $db = Engine_Db_Table::getDefaultAdapter();
   	$coreContentTable = Engine_Api::_()->getDbtable('content', 'core');
		$coreContentTableName = $coreContentTable->info('name');
   	$corePagesTable = Engine_Api::_()->getDbtable('pages', 'core');
		$corePagesTableName = $corePagesTable->info('name');
		$adminContentTable = Engine_Api::_()->getDbtable('admincontent', 'sitestore');
		$adminContentTableName = $adminContentTable->info('name');
		$storeContentTable = Engine_Api::_()->getDbtable('content', 'sitestore');
		$storeContentTableName = $storeContentTable->info('name');
		$coreContentStoreID = $corePagesTable->select()->from($corePagesTableName, array('page_id'))->where('name =?', 'sitestore_index_view')->query()->fetchColumn();
		$adminContentId = $adminContentTable->select()->from($adminContentTableName, array('name'))->where('store_id =?', $coreContentStoreID)->where('name =?', 'left')->where('name =?', 'right')->query()->fetchColumn();
    $sitestoreCoverPhoto = $_POST['sitestore_sitestore_layout_coverphoto'];
		if($_POST['sitestore_sitestore_layout_coverphoto']  == 1) {
			$current='right';
			$prev='left';
		} else {
			$current='left';
			$prev='right';
		}
		if(empty($adminContentId) ) {
		$adminContentCurrentColumn = $adminContentTable->select()->from($adminContentTableName, array('name'))->where('name =?', $prev)->where('store_id =?', $coreContentStoreID)->query()->fetchColumn();
			if($adminContentCurrentColumn) {
				$adminContentTable->update(array('name' =>  $current), array('name=?' =>  $prev, 'store_id =?'=> $coreContentStoreID));
			}

			$adminContentTable->delete(array('name =?' => 'sitestore.mainphoto-sitestore'));
			$adminContentTable->delete(array('name =?' => 'sitestoremember.profile-sitestoremembers-announcements'));
			$adminContentTable->delete(array('name =?' => 'seaocore.like-button'));
			$adminContentTable->delete(array('name =?' => 'seaocore.seaocore-follow'));		
			$adminContentTable->delete(array('name =?' => 'facebookse.facebookse-commonlike'));	
			$adminContentTable->delete(array('name =?' => 'sitestore.title-sitestore'));	
      $adminContentTable->delete(array('name =?' => 'sitestore.photorecent-sitestore'));
		}

		$storeContentId = $storeContentTable->select()->from($storeContentTableName, array('name'))->where('name =?', 'left')->where('name =?', 'right')->query()->fetchColumn();
		
		if(empty($storeContentId) ) {
			$storeContentCurrentColumn = $storeContentTable->select()->from($storeContentTableName, array('name'))->where('name =?', $prev)->query()->fetchColumn();
			if($storeContentCurrentColumn) {
				$storeContentTable->update(array('name' =>  $current), array('name=?' =>  $prev));
			}

			$storeContentTable->delete(array('name =?' => 'sitestore.mainphoto-sitestore'));
      $storeContentTable->delete(array('name =?' => 'sitestore.photorecent-sitestore'));
			$storeContentTable->delete(array('name =?' => 'sitestoremember.profile-sitestoremembers-announcements'));
			$storeContentTable->delete(array('name =?' => 'seaocore.like-button'));
			$storeContentTable->delete(array('name =?' => 'seaocore.seaocore-follow'));		
			$storeContentTable->delete(array('name =?' => 'facebookse.facebookse-commonlike'));	
			$storeContentTable->delete(array('name =?' => 'sitestore.title-sitestore'));	
		}
		
    $coreContentId = $coreContentTable->select()->from($coreContentTableName, array('name'))->where('page_id =?', $coreContentStoreID)->where('name =?', 'left')->where('name =?', 'right')->query()->fetchColumn();
		
		if(empty($coreContentId) ) {
		$coreContentCurrentColumn = $coreContentTable->select()->from($coreContentTableName, array('name'))->where('page_id =?', $coreContentStoreID)->where('name =?', $prev)->where('name =?', $prev)->where('page_id =?', $coreContentStoreID)->query()->fetchColumn();
			if($coreContentCurrentColumn) {
				$coreContentTable->update(array('name' =>  $current), array('name=?' =>  $prev, 'page_id =?'=> $coreContentStoreID));
			}

			$coreContentTable->delete(array('name =?' => 'sitestore.mainphoto-sitestore'));
      $coreContentTable->delete(array('name =?' => 'sitestore.photorecent-sitestore'));
			$coreContentTable->delete(array('name =?' => 'sitestoremember.profile-sitestoremembers-announcements'));
			$coreContentTable->delete(array('name =?' => 'seaocore.like-button'));
			$coreContentTable->delete(array('name =?' => 'seaocore.seaocore-follow'));		
			$coreContentTable->delete(array('name =?' => 'facebookse.facebookse-commonlike'));	
			$coreContentTable->delete(array('name =?' => 'sitestore.title-sitestore'));	
		}

    Engine_Api::_()->getApi("settings", "core")->setSetting('sitestore.layout.coverphotoenabled', 1);
    Engine_Api::_()->getApi("settings", "core")->setSetting('sitestore.layout.coverphoto', $sitestoreCoverPhoto);
    
    		$select = new Zend_Db_Select($db);
		$select_store = $select
								->from('engine4_core_pages', 'page_id')
								->where('name = ?', 'sitestore_index_view')
								->limit(1);
		$store = $select_store->query()->fetchAll();
		
		if(!empty($store)) {
			$store_id = $store[0]['page_id'];

			//INSERTING THE MEMBER WIDGET IN SITESTORE_CONTENT TABLE ALSO.
			$select = new Zend_Db_Select($db);
			$contentstore_ids = $select->from('engine4_sitestore_contentstores', 'contentstore_id')->query()->fetchAll();
			foreach ($contentstore_ids as $contentstore_id) {
				if(!empty($contentstore_id)) {

						$select = new Zend_Db_Select($db);
						$select_content = $select
												->from('engine4_sitestore_content')
												->where('contentstore_id = ?', $contentstore_id['contentstore_id'])
												->where('type = ?', 'widget')
												->where('name = ?', 'sitestore.store-cover-information-sitestore')
												->limit(1);
						$content = $select_content->query()->fetchAll();
						if(empty($content)) {
							$select = new Zend_Db_Select($db);
							$select_container = $select
													->from('engine4_sitestore_content', 'content_id')
													->where('contentstore_id = ?', $contentstore_id['contentstore_id'])
													->where('type = ?', 'container')
													->limit(1);
							$container = $select_container->query()->fetchAll();
							if(!empty($container)) {
								$container_id = $container[0]['content_id'];
								$select = new Zend_Db_Select($db);
								$select_left = $select
													->from('engine4_sitestore_content')
													->where('parent_content_id = ?', $container_id)
													->where('type = ?', 'container')
													->where('name = ?', 'middle')
													->limit(1);
								$middle = $select_left->query()->fetchAll();
								if(!empty($middle)) {
									$middle_id = $middle[0]['content_id'];
									$db->insert('engine4_sitestore_content', array(
									'contentstore_id' => $contentstore_id['contentstore_id'],
									'type' => 'widget',
									'name' => 'sitestore.store-cover-information-sitestore',
									'parent_content_id' => $middle_id,
									'order' => 1,
									'params' => '{"title":""}',
									));
								}
							}
						}
				}
			}

			$select = new Zend_Db_Select($db);
			$select_content = $select
									->from('engine4_sitestore_admincontent')
									->where('store_id = ?', $store_id)
									->where('type = ?', 'widget')
									->where('name = ?', 'sitestore.store-cover-information-sitestore')
									->limit(1);
			$content = $select_content->query()->fetchAll();
			if(empty($content)) {
				$select = new Zend_Db_Select($db);
				$select_container = $select
										->from('engine4_sitestore_admincontent', 'admincontent_id')
										->where('store_id = ?', $store_id)
										->where('type = ?', 'container')
										->limit(1);
				$container = $select_container->query()->fetchAll();
				if(!empty($container)) {
					$container_id = $container[0]['admincontent_id'];
					$select = new Zend_Db_Select($db);
					$select_left = $select
										->from('engine4_sitestore_admincontent')
										->where('parent_content_id = ?', $container_id)
										->where('type = ?', 'container')
										->where('name = ?', 'middle')
										->limit(1);
					$middle = $select_left->query()->fetchAll();
					if(!empty($middle)) {
						$middle_id = $middle[0]['admincontent_id'];
						$db->insert('engine4_sitestore_admincontent', array(
						'store_id' => $store_id,
						'type' => 'widget',
						'name' => 'sitestore.store-cover-information-sitestore',
						'parent_content_id' => $middle_id,
						'order' => 1,
						'params' => '{"title":""}',
						));
					}
				}
			} 
			
				$select = new Zend_Db_Select($db);
				$select_content = $select
										->from('engine4_core_content')
										->where('page_id = ?', $store_id)
										->where('type = ?', 'widget')
										->where('name = ?', 'sitestore.store-cover-information-sitestore')
										->limit(1);
				$content = $select_content->query()->fetchAll();
				if(empty($content)) {
					$select = new Zend_Db_Select($db);
					$select_container = $select
											->from('engine4_core_content', 'content_id')
											->where('page_id = ?', $store_id)
											->where('type = ?', 'container')
											->limit(1);
					$container = $select_container->query()->fetchAll();
					if(!empty($container)) {
						$container_id = $container[0]['content_id'];
						$select = new Zend_Db_Select($db);
						$select_left = $select
											->from('engine4_core_content')
											->where('parent_content_id = ?', $container_id)
											->where('type = ?', 'container')
											->where('name = ?', 'middle')
											->limit(1);
						$middle = $select_left->query()->fetchAll();
						if(!empty($middle)) {
							$middle_id = $middle[0]['content_id'];
							$db->insert('engine4_core_content', array(
							'page_id' => $store_id,
							'type' => 'widget',
							'name' => 'sitestore.store-cover-information-sitestore',
							'parent_content_id' => $middle_id,
							'order' => 1,
							'params' => '{"title":""}',
							));
						}
					}
				}
		}

    $this->_redirect("admin/sitestore/defaultlayout/index");

	}

  //ACTION FOR SAVING THE LAYOUT
  public function setCoverStoreLayoutAction() {

    $this->_helper->layout->setLayout('admin-simple');
  }

}

?>