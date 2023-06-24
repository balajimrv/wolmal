<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestores
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Layoutcore.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Api_LayoutCore extends Core_Api_Abstract {

  /**
   * Sets the without tab widgets information in core content table
   *
   * @param int $store_id
   */
  public function setWithoutTabContent($store_id, $sitestore_layout_cover_photo) {

    //GET CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');

    //GET CONTENT TABLE NAME
    $contentTableName = $contentTable->info('name');

    //INSERTING MAIN CONTAINER
    $mainContainer = $contentTable->createRow();
    $mainContainer->page_id = $store_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

    //INSERTING MAIN-MIDDLE CONTAINER
    $mainMiddleContainer = $contentTable->createRow();
    $mainMiddleContainer->page_id = $store_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

    //INSERTING MAIN-LEFT CONTAINER
    $mainLeftContainer = $contentTable->createRow();
    $mainLeftContainer->page_id = $store_id;
    $mainLeftContainer->type = 'container';
    $mainLeftContainer->name = 'right';
    $mainLeftContainer->parent_content_id = $container_id;
    $mainLeftContainer->order = 4;
    $mainLeftContainer->save();
    $left_id = $mainLeftContainer->content_id;
    
    //INSERTING TITLE WIDGET
		if(empty($sitestore_layout_cover_photo)) {
		
      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');
      
			//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
				Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoremember.storecover-photo-sitestoremembers', $middle_id, 2, '', 'true');
      }

			Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.title-sitestore', $middle_id, 3,'','true');

			//INSERTING LIKE WIDGET 
			Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.like-button', $middle_id, 4,'','true');

			//INSERTING FACEBOOK LIKE WIDGET
			if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
				Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'Facebookse.facebookse-sitestoreprofilelike', $middle_id, 5,'','true');
			}

			//INSERTING MAIN PHOTO WIDGET 
			Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.mainphoto-sitestore', $left_id, 10,'','true');

    } else {
      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');
			//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
			Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-cover-information-sitestore', $middle_id, 1, '', 'true');
    }

    //INSERTING CONTACT DETAIL WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.contactdetails-sitestore', $middle_id, 5,'','true');

//     //INSERTING PHOTO STRIP WIDGET   
//     if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
//       Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.photorecent-sitestore', $middle_id, 6,'','true');
//     }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct')) {
			Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreproduct.store-profile-products', $middle_id, 5, 'Products', 'true', '{"columnHeight":325,"columnWidth":165,"defaultWidgetNo":13}');
    }

    //INSERTING ACTIVITY FEED WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
      $advanced_activity_params =  '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'advancedactivity.home-feeds', $middle_id, 6, 'Updates', 'true',$advanced_activity_params);
    } else {
      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'activity.feed', $middle_id, 6, 'Updates', 'true');
    }

    //INSERTING INFORAMTION WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.info-sitestore', $middle_id, 7, 'Info', 'true');

    //INSERTING OVERVIEW WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.overview-sitestore', $middle_id, 8, 'Overview', 'true');

    //INSERTING LOCATION WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.location-sitestore', $middle_id, 9, 'Map', 'true');

    //INSERTING LINKS WIDGET  
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'core.profile-links', $middle_id, 125, 'Links', 'true');

    //INSERTING MAIN PHOTO WIDGET 
   // Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.mainphoto-sitestore', $left_id, 10,'','true');

		Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreproduct.sitestoreproduct-products', $left_id, 11, '', 'true', '{"title":"Top Selling Products","titleCount":true,"statistics":"","viewType":"gridview","columnWidth":"180","popularity":"last_order_all","product_type":"all","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","ratingType":"rating_avg","columnHeight":"328","itemCount":"3","truncation":"16","nomobile":"0","name":"sitestoreproduct.sitestoreproduct-products"}');

    //INSERTING WIDGET LINK WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.widgetlinks-sitestore', $left_id, 12,'','true');

    //INSERTING OPTIONS WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.options-sitestore', $left_id, 12,'','true');

    //INSERTING WRITE SOMETHING ABOUT WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.write-store', $left_id, 13,'','true');

    //INSERTING INFORMATION WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.information-sitestore', $left_id, 10, 'Information', 'true');

    //INSERTING LIKE WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.people-like', $left_id, 15,'','true');

    //INSERTING RATING WIDGET 	
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.ratings-sitestorereviews', $left_id, 16, 'Ratings', 'true');
    }

    //INSERTING BADGE WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge')) {
      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorebadge.badge-sitestorebadge', $left_id, 17, 'Badge','true');
    }
    
	  $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitestore.socialshare-sitestore"}';
	  
    //INSERTING SOCIAL SHARE WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.socialshare-sitestore', $left_id, 19, 'Social Share','true', $social_share_default_code);

//    //INSERTING FOUR SQUARE WIDGET 
//    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.foursquare-sitestore', $left_id, 20, '','true');

    //INSERTING INSIGHTS WIDGET 
//     Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.insights-sitestore', $left_id, 22, 'Insights','true');

    //INSERTING FEATURED OWNER WIDGET 
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.featuredowner-sitestore', $left_id, 23, 'Owners','true');

    //INSERTING ALBUM WIDGET 
    $sitestoreAlbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum');
    if ($sitestoreAlbumEnabled) {
      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.albums-sitestore', $left_id, 24, 'Albums','true');
      $this->setDefaultInfoWithoutTab('sitestore.photos-sitestore', $store_id, 'Photos', 'true', '110');
    }

    //INSERTING LINKED STORES WIDGET
    Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.favourite-store', $left_id, 26, 'Linked Stores','true');
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
      Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.profile-player', $left_id, 25, '','true');
    }
    
    //INSERTING VIDEO WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')) {
      $this->setDefaultInfoWithoutTab('sitestorevideo.profile-sitestorevideos', $store_id, 'Videos', 'true', '111');
    }
    
    //INSERTING EVENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
      $this->setDefaultInfoWithoutTab('sitevideo.contenttype-videos', $store_id, 'Videos', 'true', '111');
    }    

    //INSERTING NOTE WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote')) {
      $this->setDefaultInfoWithoutTab('sitestorenote.profile-sitestorenotes', $store_id, 'Notes', 'true', '112');
    }

    //INSERTING REVIEW WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
      $this->setDefaultInfoWithoutTab('sitestorereview.profile-sitestorereviews', $store_id, 'Reviews', 'true', '113');
    }

    //INSERTING FORM WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform')) {
      $this->setDefaultInfoWithoutTab('sitestoreform.sitestore-viewform', $store_id, 'Form', 'false', '114');
    }

    //INSERTING DOCUMENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')) {
      $this->setDefaultInfoWithoutTab('sitestoredocument.profile-sitestoredocuments', $store_id, 'Documents', 'true', '115');
    }
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
      $this->setDefaultInfoWithoutTab('document.contenttype-documents', $store_id, 'Documents', 'true', '115');
    }

    //INSERTING OFFER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
      $this->setDefaultInfoWithoutTab('sitestoreoffer.profile-sitestoreoffers', $store_id, 'Coupons', 'true', '116');
    }

    //INSERTING EVENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')) {
      $this->setDefaultInfoWithoutTab('sitestoreevent.profile-sitestoreevents', $store_id, 'Events', 'true', '117');
    }

    //INSERTING EVENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
      $this->setDefaultInfoWithoutTab('siteevent.contenttype-events', $store_id, 'Events', 'true', '117');
    }

    //INSERTING POLL WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll')) {
      $this->setDefaultInfoWithoutTab('sitestorepoll.profile-sitestorepolls', $store_id, 'Polls', 'true', '118');
    }

    //INSERTING DISCUSSION WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
      $this->setDefaultInfoWithoutTab('sitestore.discussion-sitestore', $store_id, 'Discussions', 'true', '119');
    }

    //INSERTING MUSIC WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
      $this->setDefaultInfoWithoutTab('sitestoremusic.profile-sitestoremusic', $store_id, 'Music', 'true', '120');
    }
    //INSERTING TWITTER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) {
      $this->setDefaultInfoWithoutTab('sitestoretwitter.feeds-sitestoretwitter', $store_id, 'Twitter', 'true', '121');
    }
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
      $this->setDefaultInfoWithoutTab('sitestoremember.profile-sitestoremembers', $store_id, 'Member', 'true', '122');
      $this->setDefaultInfoWithoutTab('sitestoremember.profile-sitestoremembers-announcements', $store_id, 'Announcements', 'true', '123');
    }
		//INSERTING MEMBER WIDGET
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration')) {
			$this->setDefaultInfoWithoutTab('sitestoreintegration.profile-items', $store_id, '', '', 999);
			
		}
  }

  /**
   * Sets the tab widgets information in core content table
   *
   * @param int $store_id
   */
  public function setTabbedLayoutContent($store_id) {

    //NOW INSERTING DEFUALT INFO OF OTHER SUB PLUGINS WHICH ARE DEPENDENTS ON THIS SITESTORE PLUGIN ARE ENABLED.
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
      $this->setContentDefaultInfo('sitestore.photos-sitestore', $store_id, 'Photos', 'true', '110');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')) {
      $this->setContentDefaultInfo('sitestorevideo.profile-sitestorevideos', $store_id, 'Videos', 'true', '111');
    }
    
        //INSERTING EVENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
      $this->setContentDefaultInfo('sitevideo.contenttype-videos', $store_id, 'Videos', 'true', '111');
    }   

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote')) {
      $this->setContentDefaultInfo('sitestorenote.profile-sitestorenotes', $store_id, 'Notes', 'true', '112');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
      $this->setContentDefaultInfo('sitestorereview.profile-sitestorereviews', $store_id, 'Reviews', 'true', '113');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform')) {
      $this->setContentDefaultInfo('sitestoreform.sitestore-viewform', $store_id, 'Form', 'false', '114');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')) {
      $this->setContentDefaultInfo('sitestoredocument.profile-sitestoredocuments', $store_id, 'Documents', 'true', '115');
    }
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
      $this->setContentDefaultInfo('document.contenttype-documents', $store_id, 'Documents', 'true', '115');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
      $this->setContentDefaultInfo('sitestoreoffer.profile-sitestoreoffers', $store_id, 'Coupons', 'true', '116');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')) {
      $this->setContentDefaultInfo('sitestoreevent.profile-sitestoreevents', $store_id, 'Events', 'true', '117');
    }

    //INSERTING EVENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
      $this->setContentDefaultInfo('siteevent.contenttype-events', $store_id, 'Events', 'true', '117');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll')) {
      $this->setContentDefaultInfo('sitestorepoll.profile-sitestorepolls', $store_id, 'Polls', 'true', '118');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
      $this->setContentDefaultInfo('sitestore.discussion-sitestore', $store_id, 'Discussions', 'true', '119');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
      $this->setContentDefaultInfo('sitestoremusic.profile-sitestoremusic', $store_id, 'Music', 'true', '120');
    }
    //INSERTING TWITTER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) {
      $this->setContentDefaultInfo('sitestoretwitter.feeds-sitestoretwitter', $store_id, 'Twitter', 'true', '121');
    }
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
      $this->setContentDefaultInfo('sitestoremember.profile-sitestoremembers', $store_id, 'Member', 'true', '122');
      $this->setContentDefaultInfo('sitestoremember.profile-sitestoremembers-announcements', $store_id, 'Announcements', 'true', '123');
    }
		//INSERTING MEMBER WIDGET
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration')) {
			$this->setContentDefaultInfo('sitestoreintegration.profile-items', $store_id, '', '', 999);
		}
  }

  /**
   * Sets the tab widgets information in admin content and also content table for user layout
   *
   * @param int $store_id
   */
  public function setContentDefaultLayout($store_id) {

    //GET ADMIN CONTENT TABLE
    $admincontentTable = Engine_Api::_()->getDbtable('admincontent', 'sitestore');

    //GET CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('content', 'sitestore');

    //FETCH
    $corestoresinfo = Engine_Api::_()->sitestore()->getWidgetizedStore();

    //SELECT ADMIN CONTENT
    $admincontentselected = $admincontentTable->select()->where('store_id =?', $corestoresinfo->page_id)->where('type =?', 'container');

    //FETCH
    $admintableinfo = $admincontentTable->fetchAll($admincontentselected);

    //DEFINE OLD CONTAINER   
    $oldContener = array();

    //CREATING A ROW
    foreach ($admintableinfo as $value) {
      $mainContainer = $contentTable->createRow();
      $mainContainer->contentstore_id = $store_id;
      $mainContainer->type = 'container';
      $mainContainer->name = $value->name;
      $mainContainer->order = $value->order;
      $mainContainer->params = $value->params;
      if (isset($oldContener[$value->parent_content_id]))
        $mainContainer->parent_content_id = $oldContener[$value->parent_content_id];
      $mainContainer->save();
      $container_id = $mainContainer->content_id;
      $oldContener[$value->admincontent_id] = $container_id;
    }

    //SELECT ADMIN CONTENT
    $admincontentselected = $admincontentTable->select()->where('store_id =?', $corestoresinfo->page_id)->where('type =?', 'widget')->where('name =?', 'core.container-tabs');

    //FETCH
    $admintableinfo = $admincontentTable->fetchAll($admincontentselected);

    //CREATING A ROW
    foreach ($admintableinfo as $values) {
      $mainWidgets = $contentTable->createRow();
      $mainWidgets->contentstore_id = $store_id;
      $mainWidgets->type = 'widget';
      $mainWidgets->name = $values->name;
      $mainWidgets->order = $values->order;
      $mainWidgets->params = $values->params;
      if (isset($oldContener[$values->parent_content_id]))
        $mainWidgets->parent_content_id = $oldContener[$values->parent_content_id];
      $mainWidgets->save();
      $container_id = $mainWidgets->content_id;
      $oldContener[$values->admincontent_id] = $container_id;
    }

    //SELECT ADMIN CONTENT
    $admincontentselected = $admincontentTable->select()->where('store_id =?', $corestoresinfo->page_id)->where('type =?', 'widget')->where('name <>?', 'core.container-tabs');

    //FETCH
    $admintableinfo = $admincontentTable->fetchAll($admincontentselected);

    //CREATING A ROW
    foreach ($admintableinfo as $values) {
      $mainWidgets = $contentTable->createRow();
      $mainWidgets->contentstore_id = $store_id;
      $mainWidgets->type = 'widget';
      $mainWidgets->name = $values->name;
      $mainWidgets->order = $values->order;
      $mainWidgets->params = $values->params;
      if (isset($oldContener[$values->parent_content_id]))
        $mainWidgets->parent_content_id = $oldContener[$values->parent_content_id];
      $mainWidgets->save();
    }
  }

  /**
   * Set profile store default widget in core content table with tab
   *
   * @param string $name
   * @param int $store_id
   * @param string $title
   * @param int $titleCount
   * @param int $order
   */
  public function setContentDefaultInfo($name = null, $store_id, $title = null, $titleCount = null, $order = null, $params = null) {
    $db = Engine_Db_Table::getDefaultAdapter();
    if (!empty($name)) {
      $contentTable = Engine_Api::_()->getDbtable('content', 'core');
      $contentTableName = $contentTable->info('name');
      $select = $contentTable->select();
      $select_content = $select
              ->from($contentTableName)
              ->where('page_id = ?', $store_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', $name)
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = $contentTable->select();
        $select_container = $select
                ->from($contentTableName, array('content_id'))
                ->where('page_id = ?', $store_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = $contentTable->select();
          $select_middle = $select
                  ->from($contentTableName)
                  ->where('parent_content_id = ?', $container_id)
                  ->where('type = ?', 'container')
                  ->where('name = ?', 'middle')
                  ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if (!empty($middle)) {
            $middle_id = $middle[0]['content_id'];
            $select = $contentTable->select();
            $select_tab = $select
                    ->from($contentTableName)
                    ->where('type = ?', 'widget')
                    ->where('name = ?', 'core.container-tabs')
                    ->where('page_id = ?', $store_id)
                    ->limit(1);
            $tab = $select_tab->query()->fetchAll();
            $tab_id='';
            if (!empty($tab)) {
              $tab_id = $tab[0]['content_id'];
            } else {
							$contentWidget = $contentTable->createRow();
							$contentWidget->page_id = $store_id;
							$contentWidget->type = 'widget';
							$contentWidget->name = 'core.container-tabs';
							$contentWidget->parent_content_id = $middle_id;
							$contentWidget->order = $order;
              $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showmore', 9);
							$contentWidget->params = "{\"max\":\"$showmaxtab\"}";
							$tab_id = $contentWidget->save();
            }

            if($name != 'sitestoreintegration.profile-items') {
							$contentWidget = $contentTable->createRow();
							$contentWidget->page_id = $store_id;
							$contentWidget->type = 'widget';
							$contentWidget->name = $name;
							$contentWidget->parent_content_id = ($tab_id ? $tab_id : $middle_id);
							$contentWidget->order = $order;
							if($params) {
								$contentWidget->params = $params;
							} else {
								$contentWidget->params = '{"title":"' . $title . '" , "titleCount":' . $titleCount . '}';
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
                          ->from('engine4_core_content')
                          ->where('parent_content_id = ?', $tab_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitestoreintegration.profile-items')
                          ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"'.$resource_type.'","nomobile":"0","name":"sitestoreintegration.profile-items"}');
                  $info = $select->query()->fetch();
                  if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_core_content', array(
                        'page_id' => $store_id,
                        'type' => 'widget',
                        'name' => 'sitestoreintegration.profile-items',
                        'parent_content_id' => $tab_id,
                        'order' => 999,
                        'params' => '{"title":"' . $item_title . '","resource_type":"'.$resource_type.'","nomobile":"0","name":"sitestoreintegration.profile-items"}',
                    ));
                  }
                }
              }

              $this->setstoresintwidgetTab('document', '{"title":"Documents","resource_type":"document_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $store_id);

              $this->setstoresintwidgetTab('sitegroup', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $store_id);

              $this->setstoresintwidgetTab('sitepage', '{"title":"Pages","resource_type":"sitepage_page_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $store_id);

              $this->setstoresintwidgetTab('sitebusiness', '{"title":"Businesses","resource_type":"sitebusiness_business_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $store_id);
              
              $this->setstoresintwidgetTab('sitefaq', '{"title":"FAQs","resource_type":"sitefaq_faq_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $store_id);
              
              $this->setstoresintwidgetTab('sitetutorial', '{"title":"Tutorials","resource_type":"sitetutorial_tutorial_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $store_id);

              $this->setstoresintwidgetTab('list', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $store_id);
              
              $this->setstoresintwidgetTab('quiz', '{"title":"Quiz","resource_type":"quiz_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $store_id);
              
              $this->setstoresintwidgetTab('folder', '{"title":"Folder","resource_type":"folder_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $tab_id, $store_id);

            }
          }
        }
      }
    }
  }

  /**
   * Set profile store default widget in core content table without tab
   *
   * @param string $name
   * @param int $store_id
   * @param string $title
   * @param int $titleCount
   * @param int $order
   */
  public function setDefaultInfoWithoutTab($name = null, $store_id, $title = null, $titleCount = null, $order = null) {
    $db = Engine_Db_Table::getDefaultAdapter();
    if (!empty($name)) {
      $contentTable = Engine_Api::_()->getDbtable('content', 'core');
      $contentTableName = $contentTable->info('name');
      $select = $contentTable->select();
      $select_content = $select
              ->from($contentTableName)
              ->where('page_id = ?', $store_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', $name)
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = $contentTable->select();
        $select_container = $select
                ->from($contentTableName, array('content_id'))
                ->where('page_id = ?', $store_id)
                ->where('type = ?', 'container')
                ->limit(1);
        $container = $select_container->query()->fetchAll();
        if (!empty($container)) {
          $container_id = $container[0]['content_id'];
          $select = $contentTable->select();
          $select_middle = $select
                  ->from($contentTableName)
                  ->where('parent_content_id = ?', $container_id)
                  ->where('type = ?', 'container')
                  ->where('name = ?', 'middle')
                  ->limit(1);
          $middle = $select_middle->query()->fetchAll();
          if (!empty($middle)) {
            $middle_id = $middle[0]['content_id'];

            if($name != 'sitestoreintegration.profile-items') {
							$contentWidget = $contentTable->createRow();
							$contentWidget->page_id = $store_id;
							$contentWidget->type = 'widget';
							$contentWidget->name = $name;
							$contentWidget->parent_content_id = ($middle_id);
							$contentWidget->order = $order;
							$contentWidget->params = '{"title":"' . $title . '" , "titleCount":' . $titleCount . '}';
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
                          ->from('engine4_core_content')
                          ->where('parent_content_id = ?', $middle_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitestoreintegration.profile-items')
                          ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"'.$resource_type.'","nomobile":"0","name":"sitestoreintegration.profile-items"}');
                  $info = $select->query()->fetch();
                  if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_core_content', array(
                        'page_id' => $store_id,
                        'type' => 'widget',
                        'name' => 'sitestoreintegration.profile-items',
                        'parent_content_id' => $middle_id,
                        'order' => 999,
                        'params' => '{"title":"' . $item_title . '","resource_type":"'.$resource_type.'","nomobile":"0","name":"sitestoreintegration.profile-items"}',
                    ));
                  }
                  //}
                }
              }

              
              $this->setstoresintwidgetTab('document', '{"title":"Documents","resource_type":"document_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              
              $this->setstoresintwidgetTab('quiz', '{"title":"Quiz","resource_type":"quiz_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              
              $this->setstoresintwidgetTab('folder', '{"title":"Folder","resource_type":"folder_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              
              $this->setstoresintwidgetTab('sitefaq', '{"title":"FAQs","resource_type":"sitefaq_faq_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              
              $this->setstoresintwidgetTab('sitetutorial', '{"title":"Tutorials","resource_type":"sitetutorial_tutorial_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);

              $this->setstoresintwidgetTab('sitegroup', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              
              $this->setstoresintwidgetTab('sitepage', '{"title":"Pages","resource_type":"sitepage_page_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              
              $this->setstoresintwidgetTab('sitebusiness', '{"title":"Businesses","resource_type":"sitebusiness_business_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              
              $this->setstoresintwidgetTab('list', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
            }
          }
        }
      }
    }
  }
  
  public function setstoresintwidgetTab($module_name, $params, $tab_id, $store_id) {

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
						->from('engine4_core_content')
						->where('parent_content_id = ?', $tab_id)
						->where('type = ?', 'widget')
						->where('name = ?', 'sitestoreintegration.profile-items')
						->where('params = ?', $params);
				$info = $select->query()->fetch();
				if (empty($info)) {
					// tab on profile
					$db->insert('engine4_core_content', array(
							'page_id' => $store_id,
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
