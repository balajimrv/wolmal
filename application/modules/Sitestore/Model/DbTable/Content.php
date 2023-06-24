<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Model_DbTable_Content extends Engine_Db_Table {

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
              ->where('contentstore_id = ?', $contentstore_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', $name)
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = $this->select();
        $select_container = $select
                ->from($this->info('name'), array('content_id'))
                ->where('contentstore_id = ?', $contentstore_id)
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
                    ->where('name = ?', 'core.container-tabs')
                    ->where('contentstore_id = ?', $contentstore_id)
                    ->limit(1);
            $tab = $select_tab->query()->fetchAll();
            $tab_id='';
            if (!empty($tab)) {
              $tab_id = $tab[0]['content_id'];
            } else {
              $contentWidget = $this->createRow();
              $contentWidget->contentstore_id = $contentstore_id;
              $contentWidget->type = 'widget';
              $contentWidget->name = 'core.container-tabs';
              $contentWidget->parent_content_id = ($tab_id ? $tab_id : $middle_id);
              $contentWidget->order = $order;
              $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showmore', 9);
							$contentWidget->params = "{\"max\":\"$showmaxtab\"}";
              $contentWidget->save();
            }
            if ($name != 'sitestoreintegration.profile-items') {
              $contentWidget = $this->createRow();
              $contentWidget->contentstore_id = $contentstore_id;
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
                        'contentstore_id' => $contentstore_id,
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
   * Set profile store default widget in user content table with tab
   *
   * @param string $name
   * @param int $store_id
   * @param string $title
   * @param int $titleCount
   * @param int $order
   */
  public function setContentDefaultInfoWithoutTab($name = null, $contentstore_id, $title = null, $titleCount = null, $order = null) {
    $db = Engine_Db_Table::getDefaultAdapter();
    if (!empty($name)) {
      $select = $this->select();
      $select_content = $select
              ->from($this->info('name'))
              ->where('contentstore_id = ?', $contentstore_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', $name)
              ->limit(1);
      $content = $select_content->query()->fetchAll();
      if (empty($content)) {
        $select = $this->select();
        $select_container = $select
                ->from($this->info('name'), array('content_id'))
                ->where('contentstore_id = ?', $contentstore_id)
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
            $middle_id = $middle[0]['content_id'];

            if ($name != 'sitestoreintegration.profile-items') {
              $contentWidget = $this->createRow();
              $contentWidget->contentstore_id = $contentstore_id;
              $contentWidget->type = 'widget';
              $contentWidget->name = $name;
              $contentWidget->parent_content_id = ($middle_id);
              $contentWidget->order = $order;
              $contentWidget->params = '{"title":"' . $title . '" , "titleCount":"' . $titleCount . '"}';
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
                          ->where('parent_content_id = ?', $middle_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitestoreintegration.profile-items')
                          ->where('params = ?', '{"title":"' . $item_title . '","resource_type":"'.$resource_type.'","nomobile":"0","name":"sitestoreintegration.profile-items"}');
                  $info = $select->query()->fetch();
                  if (empty($info)) {

                    // tab on profile
                    $db->insert('engine4_sitestore_content', array(
                        'contentstore_id' => $contentstore_id,
                        'type' => 'widget',
                        'name' => 'sitestoreintegration.profile-items',
                        'parent_content_id' => $middle_id,
                        'order' => 999,
                        'params' => '{"title":"' . $item_title . '","resource_type":"'.$resource_type.'","nomobile":"0","name":"sitestoreintegration.profile-items"}',
                    ));
                  }
                  // }
                }
              }
                            
              $this->tabstoreintwidgetlayout('document', '{"title":"Docuemts","resource_type":"document_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              
              $this->tabstoreintwidgetlayout('sitegroup', '{"title":"Groups","resource_type":"sitegroup_group_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              
              $this->tabstoreintwidgetlayout('sitepage', '{"title":"Pages","resource_type":"sitepage_page_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              
              $this->tabstoreintwidgetlayout('list', '{"title":"Listings","resource_type":"list_listing_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              $this->tabstoreintwidgetlayout('folder', '{"title":"Folders","resource_type":"folder_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              $this->tabstoreintwidgetlayout('quiz', '{"title":"Quiz","resource_type":"quiz_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
              
              $this->tabstoreintwidgetlayout('sitefaq', '{"title":"FAQs","resource_type":"sitefaq_faq_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);
               
              $this->tabstoreintwidgetlayout('sitetutorial', '{"title":"Tutorials","resource_type":"sitetutorial_tutorial_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_ids);
              
              $this->tabstoreintwidgetlayout('sitebusiness', '{"title":"Businesses","resource_type":"sitebusiness_business_0","nomobile":"0","name":"sitestoreintegration.profile-items"}', $middle_id, $store_id);

            }
          }
        }
      }
    }
  }

  /**
   * Set default widget in content stores table
   *
   * @param object $table
   * @param string $tablename
   * @param int $store_id
   * @param string $type
   * @param string $widgetname
   * @param int $middle_id 
   * @param int $order 
   * @param string $title 
   * @param int $titlecount    
   */
    public function setDefaultDataUserWidget($table, $tablename, $contentstore_id, $type, $widgetname, $middle_id, $order, $title = null, $titlecount = null,$advanced_activity_params = null) {
    $selectWidgetId = $this->select()
            ->where('contentstore_id =?', $contentstore_id)
            ->where('type = ?', $type)
            ->where('name = ?', $widgetname)
            ->where('parent_content_id = ?', $middle_id)
            ->limit(1);
    $fetchWidgetContentId = $selectWidgetId->query()->fetchAll();
    if (empty($fetchWidgetContentId)) {
      $contentWidget = $this->createRow();
      $contentWidget->contentstore_id = $contentstore_id;
      $contentWidget->type = $type;
      $contentWidget->name = $widgetname;
      $contentWidget->parent_content_id = $middle_id;
      $contentWidget->order = $order;
      if(empty($advanced_activity_params)) {
        $contentWidget->params = "{\"title\":\"$title\",\"titleCount\":$titlecount}";
      } else {
        $contentWidget->params = "$advanced_activity_params";
      }
      $contentWidget->save();
    }
  }  
  
  /**
   * Set default widget in content stores table without tab
   *
   * @param int $store_id
   */  
  public function setWithoutTabLayout($store_id, $sitestore_layout_cover_photo=1) {
  	
  	// GET CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('content', 'sitestore');
    
    // GET CONTENT TABLE NAME
    $contentTableName = $this->info('name');
    
    //INSERTING MAIN CONTAINER
    $mainContainer = $this->createRow();
    $mainContainer->contentstore_id = $store_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

    //INSERTING MAIN-MIDDLE CONTAINER
    $mainMiddleContainer = $this->createRow();
    $mainMiddleContainer->contentstore_id = $store_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

    //INSERTING MAIN-LEFT CONTAINER
    $mainLeftContainer = $this->createRow();
    $mainLeftContainer->contentstore_id = $store_id;
    $mainLeftContainer->type = 'container';
    $mainLeftContainer->name = 'right';
    $mainLeftContainer->parent_content_id = $container_id;
    $mainLeftContainer->order = 4;
    $mainLeftContainer->save();
    $left_id = $mainLeftContainer->content_id;

    if(empty($sitestore_layout_cover_photo)) {
    
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');
    
			//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoremember.storecover-photo-sitestoremembers', $middle_id, 2, '', 'true');
      }

			//INSERTING TITLE WIDGET
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.title-sitestore', $middle_id, 3, '', 'true');

			//INSERTING LIKE WIDGET
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.like-button', $middle_id, 4, '', 'true');

			//INSERTING FACEBOOK LIKE WIDGET
			if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'Facebookse.facebookse-sitestoreprofilelike', $middle_id, 5, '', 'true');
			}

			//INSERTING MAIN PHOTO WIDGET
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.mainphoto-sitestore', $left_id, 10, '', 'true');

    } else {
			//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');
			
			//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-cover-information-sitestore', $middle_id, 2, '', 'true');
    }   
    
    //INSERTING CONTACT DETAIL WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.contactdetails-sitestore', $middle_id, 5, '', 'true');
    
//     //INSERTING PHOTO STRIP WIDGET
//     if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
//       $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.photorecent-sitestore', $middle_id, 6, '', 'true');
//     }
    
			if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct')) {
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreproduct.store-profile-products', $middle_id, 5, 'Products', 'true', '{"columnHeight":325,"columnWidth":165,"defaultWidgetNo":13}');
			}

    //INSERTING ACTIVITY FEED WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
      $advanced_activity_params =   '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'advancedactivity.home-feeds', $middle_id, 6, 'Updates', 'true',$advanced_activity_params);
    } else {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.feed', $middle_id, 6, 'Updates', 'true');
    }
   
    //INSERTING INFORAMTION WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.info-sitestore', $middle_id, 7, 'Info', 'true');
    
    //INSERTING OVERVIEW WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.overview-sitestore', $middle_id, 8, 'Overview', 'true');
   
    //INSERTING LOCATION WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.location-sitestore', $middle_id, 9, 'Map', 'true');
    		  
    //INSERTING LINKS WIDGET  
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'core.profile-links', $middle_id, 125, 'Links', 'true');
		
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreproduct.sitestoreproduct-products', $left_id, 11, '', 'true', '{"title":"Top Selling Products","titleCount":true,"statistics":"","viewType":"gridview","columnWidth":"180","popularity":"last_order_all","product_type":"all","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","ratingType":"rating_avg","columnHeight":"328","itemCount":"3","truncation":"16","nomobile":"0","name":"sitestoreproduct.sitestoreproduct-products"}');

		//INSERTING WIDGET LINK WIDGET 
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.widgetlinks-sitestore', $left_id, 12, '', 'true');
		
		//INSERTING OPTIONS WIDGET 
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.options-sitestore', $left_id, 12, '', 'true');
				
		//INSERTING WRITE SOMETHING ABOUT WIDGET 
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.write-store', $left_id, 13, '', 'true');

		//INSERTING WRITE SOMETHING ABOUT WIDGET 
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.information-sitestore', $left_id, 10, 'Information', 'true');
			
		//INSERTING LIKE WIDGET 
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');
				
		//INSERTING RATING WIDGET 
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.ratings-sitestorereviews', $left_id, 16, 'Ratings', 'true');
		}

		//INSERTING BADGE WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge')) {
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorebadge.badge-sitestorebadge', $left_id, 17, 'Badge', 'true');
    }
    
	  $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitestore.socialshare-sitestore"}';
	  
    //INSERTING SOCIAL SHARE WIDGET 
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.socialshare-sitestore', $left_id, 19, 'Social Share', 'true', $social_share_default_code);
		
//		//INSERTING FOUR SQUARE WIDGET 
//		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.foursquare-sitestore', $left_id, 20, '', 'true');

		//INSERTING INSIGHTS WIDGET 
// 		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.insights-sitestore', $left_id, 22, 'Insights',  'true');

		//INSERTING FEATURED OWNER WIDGET 
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.featuredowner-sitestore', $left_id, 23, 'Owners', 'true');
		
		//INSERTING ALBUM WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.albums-sitestore', $left_id, 24, 'Albums', 'true');
      $this->setContentDefaultInfoWithoutTab('sitestore.photos-sitestore', $store_id, 'Photos', 'true', '110');
    }
    
	  //INSERTING STORE PROFILE PLAYER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
	    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoremusic.profile-player', $left_id, 25, '', 'true');
	  }    
	     
	  //INSERTING LINKED STORE WIDGET
 	  $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.favourite-store', $left_id, 26, 'Linked Stores', 'true');
 	  
    //INSERTING VIDEO WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')) {
      $this->setContentDefaultInfoWithoutTab('sitestorevideo.profile-sitestorevideos', $store_id, 'Videos', 'true', '111');
    }
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
      $this->setContentDefaultInfoWithoutTab('sitevideo.contenttype-videos', $store_id, 'Videos', 'true', '117');
    }
    //INSERTING NOTE WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote')) {
      $this->setContentDefaultInfoWithoutTab('sitestorenote.profile-sitestorenotes', $store_id, 'Notes', 'true', '112');
    }

    //INSERTING REVIEW WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
      $this->setContentDefaultInfoWithoutTab('sitestorereview.profile-sitestorereviews', $store_id, 'Reviews', 'true', '113');
    }
    
    //INSERTING FORM WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform')) {
      $this->setContentDefaultInfoWithoutTab('sitestoreform.sitestore-viewform', $store_id, 'Form', 'false', '114');
    }
    
    //INSERTING DOCUMENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')) {
      $this->setContentDefaultInfoWithoutTab('sitestoredocument.profile-sitestoredocuments', $store_id, 'Documents', 'true', '115');
    }

    //INSERTING OFFER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
      $this->setContentDefaultInfoWithoutTab('sitestoreoffer.profile-sitestoreoffers', $store_id, 'Coupons', 'true', '116');
    }

    //INSERTING EVENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')) {
      $this->setContentDefaultInfoWithoutTab('sitestoreevent.profile-sitestoreevents', $store_id, 'Events', 'true', '117');
    }

    //INSERTING EVENT WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
      $this->setContentDefaultInfoWithoutTab('siteevent.contenttype-events', $store_id, 'Events', 'true', '117');
    }

    //INSERTING POLL WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll')) {
      $this->setContentDefaultInfoWithoutTab('sitestorepoll.profile-sitestorepolls', $store_id, 'Polls', 'true', '118');
    }

    //INSERTING DISCUSSION WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
      $this->setContentDefaultInfoWithoutTab('sitestore.discussion-sitestore', $store_id, 'Discussions', 'true', '119');
    }
    
    //INSERTING NOTE WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
      $this->setContentDefaultInfoWithoutTab('sitestoremusic.profile-sitestoremusic', $store_id, 'Music', 'true', '120');
    }
    //INSERTING TWITTER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) {
      $this->setContentDefaultInfoWithoutTab('sitestoretwitter.feeds-sitestoretwitter', $store_id, 'Twitter', 'true', '121');
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
      $this->setContentDefaultInfoWithoutTab('sitestoremember.profile-sitestoremembers', $store_id, 'Members', 'true', '122');
      $this->setContentDefaultInfoWithoutTab('sitestoremember.profile-sitestoremembers-announcements', $store_id, 'Announcements', 'true', '123');
    }
    
    //INSERTING STORE INTEGRATION WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration')) {
      $this->setContentDefaultInfoWithoutTab('sitestoreintegration.profile-items', $store_id, '', '', 999);
    }

  } 
 
  public function setTabbedLayout($store_id, $sitestore_layout_cover_photo = 1) {
  	
  	//SHOW HOW MANY TAB SHOULD BE SHOW IN THE STORE PROFILE STORE BEFORE MORE LINK
  	$showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showmore', 9);
  	
  	// GET CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('content', 'sitestore');
    
    // GET CONTENT TABLE NAME    
    $contentTableName = $this->info('name');
    
    //INSERTING MAIN CONTAINER
    $mainContainer = $this->createRow();
    $mainContainer->contentstore_id = $store_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

    //INSERTING MAIN-MIDDLE CONTAINER
    $mainMiddleContainer = $this->createRow();
    $mainMiddleContainer->contentstore_id = $store_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

    //INSERTING MAIN-LEFT CONTAINER
    $mainLeftContainer = $this->createRow();
    $mainLeftContainer->contentstore_id = $store_id;
    $mainLeftContainer->type = 'container';
    $mainLeftContainer->name = 'right';
    $mainLeftContainer->parent_content_id = $container_id;
    $mainLeftContainer->order = 4;
    $mainLeftContainer->save();
    $left_id = $mainLeftContainer->content_id;

    //INSERTING MAIN-MIDDLE-TAB CONTAINER
    $middleTabContainer = $this->createRow();
    $middleTabContainer->contentstore_id = $store_id;
    $middleTabContainer->type = 'widget';
    $middleTabContainer->name = 'core.container-tabs';
    $middleTabContainer->parent_content_id = $middle_id;
    $middleTabContainer->order = 10;
    $middleTabContainer->params = "{\"max\":\"$showmaxtab\"}";
    $middleTabContainer->save();
    $middle_tab = $middleTabContainer->content_id;
			
		//INSERTING THUMB PHOTO WIDGET
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.thumbphoto-sitestore', $middle_id, 3, '', 'true');

    if(empty($sitestore_layout_cover_photo)) {
    
      //INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');

			//INSERTING THUMB PHOTO WIDGET
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoremember.storecover-photo-sitestoremembers', $middle_id, 2, '', 'true');
      }

			//INSERTING TITLE WIDGET
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.title-sitestore', $middle_id, 4, '', 'true');

			//INSERTING LIKE WIDGET
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.like-button', $middle_id, 5, '', 'true');

			//INSERTING FACEBOOK LIKE WIDGET
			if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'Facebookse.facebookse-sitestoreprofilelike', $middle_id, 6, '', 'true');
			}

			//INSERTING MAIN PHOTO WIDGET
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.mainphoto-sitestore', $left_id, 10, '', 'true');

    } else {
    
      //INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');

			//INSERTING THUMB PHOTO WIDGET
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-cover-information-sitestore', $middle_id, 2, '', 'true');
    }
    
    //INSERTING CONTACT DETAIL WIDGET
    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.contactdetails-sitestore', $middle_id, 7, '', 'true');
  					
//     //INSERTING PHOTO STRIP WIDGET
// 	  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
// 	    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.photorecent-sitestore', $middle_id, 8, '', 'true');
// 	  }

	  //INSERTING MAIN PHOTO WIDGET 
		//$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.mainphoto-sitestore', $left_id, 10, '', 'true');
	
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreproduct.sitestoreproduct-products', $left_id, 11, '', 'true', '{"title":"Top Selling Products","titleCount":true,"statistics":"","viewType":"gridview","columnWidth":"180","popularity":"last_order_all","product_type":"all","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","ratingType":"rating_avg","columnHeight":"328","itemCount":"3","truncation":"16","nomobile":"0","name":"sitestoreproduct.sitestoreproduct-products"}');

		//INSERTING OPTIONS WIDGET
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.options-sitestore', $left_id, 12, '', 'true');
	
	  //INSERTING INFORMATION WIDGET 
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.information-sitestore', $left_id, 10, 'Information', 'true');
				
		//INSERTING WRITE SOMETHING ABOUT WIDGET 
	  $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');
	
    //INSERTING RATING WIDGET 
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.ratings-sitestorereviews', $left_id, 16, 'Ratings', 'true');
		}

	  //INSERTING BADGE WIDGET 
	  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge')) {
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorebadge.badge-sitestorebadge', $left_id, 17, 'Badge',  'true');
	  }
	  
	  //INSERTING YOU MAY ALSO LIKE WIDGET 
	  $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.suggestedstore-sitestore', $left_id, 18, 'You May Also Like', 'true');
	  
		$social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitestore.socialshare-sitestore"}';
		
	  //INSERTING SOCIAL SHARE WIDGET 
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.socialshare-sitestore', $left_id, 19, 'Social Share' , 'true', $social_share_default_code);
		
//		//INSERTING FOUR SQUARE WIDGET 
//		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.foursquare-sitestore', $left_id, 20, '', 'true');
	
		//INSERTING INSIGHTS WIDGET 
// 		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.insights-sitestore', $left_id, 21, 'Insights',  'true');

		//INSERTING FEATURED OWNER WIDGET 
		$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.featuredowner-sitestore', $left_id, 22, 'Owners', 'true');
	
		//INSERTING ALBUM WIDGET 
	  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
	    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.albums-sitestore', $left_id, 23, 'Albums', 'true');
	  }	
	  
	  //INSERTING STORE PROFILE PLAYER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
	    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoremusic.profile-player', $left_id, 24, '', 'true');
	  }  	 
	   	
	  //INSERTING LINKED STORE WIDGET
 	  $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.favourite-store', $left_id, 25, 'Linked Stores', 'true');
 	  	  	
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct')) {
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreproduct.store-profile-products', $middle_tab, 1, 'Products', 'true', '{"columnHeight":325,"columnWidth":165,"defaultWidgetNo":13}');
		}
  
	  //INSERTING ACTIVITY FEED WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
      $advanced_activity_params =  '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 2, 'Updates', 'true',$advanced_activity_params);
    } else {
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.feed', $middle_tab, 2, 'Updates', 'true');
    }
	 
	  //INSERTING INFORAMTION WIDGET
	  $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.info-sitestore', $middle_tab, 3, 'Info', 'true');
	  
	  //INSERTING OVERVIEW WIDGET
	  $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.overview-sitestore', $middle_tab, 4, 'Overview', 'true');
	 
	  //INSERTING LOCATION WIDGET
	  $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.location-sitestore', $middle_tab, 5, 'Map', 'true');
	  		    
	  //INSERTING LINKS WIDGET
	  $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');

    //INSERTING ALBUM WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
      $this->setDefaultInfo('sitestore.photos-sitestore', $store_id, 'Photos', 'true', '110');
    }

    //INSERTING VIDEO WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')) {
      $this->setDefaultInfo('sitestorevideo.profile-sitestorevideos', $store_id, 'Videos', 'true', '111');
    }
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')) {
      $this->setDefaultInfo('sitevideo.contenttype-videos', $store_id, 'Videos', 'true', '117');
    }
    //INSERTING NOTE WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote')) {
     $this->setDefaultInfo('sitestorenote.profile-sitestorenotes', $store_id, 'Notes', 'true', '112');
    }

    //INSERTING REVIEW WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
      $this->setDefaultInfo('sitestorereview.profile-sitestorereviews', $store_id, 'Reviews', 'true', '113');
    }

    //INSERTING FORM WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform')) {
      $this->setDefaultInfo('sitestoreform.sitestore-viewform', $store_id, 'Form', 'false', '114');
    }
    
    //INSERTING DOCUMENT WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')) {
      $this->setDefaultInfo('sitestoredocument.profile-sitestoredocuments', $store_id, 'Documents', 'true', '115');
    }
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
      $this->setDefaultInfo('document.contenttype-documents', $store_id, 'Documents', 'true', '117');
    }

    //INSERTING OFFER WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
      $this->setDefaultInfo('sitestoreoffer.profile-sitestoreoffers', $store_id, 'Coupons', 'true', '116');
    }

    //INSERTING EVENT WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')) {
      $this->setDefaultInfo('sitestoreevent.profile-sitestoreevents', $store_id, 'Events', 'true', '117');
    }

    //INSERTING EVENT WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
      $this->setDefaultInfo('siteevent.contenttype-events', $store_id, 'Events', 'true', '117');
    }

    //INSERTING POLL WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll')) {
      $this->setDefaultInfo('sitestorepoll.profile-sitestorepolls', $store_id, 'Polls', 'true', '118');
    }

    //INSERTING DISCUSSION WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
      $this->setDefaultInfo('sitestore.discussion-sitestore', $store_id, 'Discussions', 'true', '119');
    }

    //INSERTING MUSIC WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
      $this->setDefaultInfo('sitestoremusic.profile-sitestoremusic', $store_id, 'Music', 'true', '120');
    }  
    //INSERTING TWITTER WIDGET 
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) {
      $this->setDefaultInfo('sitestoretwitter.feeds-sitestoretwitter', $store_id, 'Twitter', 'true', '121');
    }

		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
			$this->setDefaultInfo('sitestoremember.profile-sitestoremembers', $store_id, 'Members', 'true', '122');
			$this->setDefaultInfo('sitestoremember.profile-sitestoremembers-announcements', $store_id, 'Announcements', 'true', '123');
		}
		
    //INSERTING MEMBER WIDGET
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration')) {
      $this->setDefaultInfo('sitestoreintegration.profile-items', $store_id, '', '', 999);
    }
  } 
  
 	public function setContentDefault($store_id, $sitestore_layout_cover_photo = 1) {

    $storelayout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layout.setting', 1);
    $showmaxtab = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.showmore', 9);
   
    if ($storelayout) {
    	//GET CONTENT TABLE
      $contentTable = Engine_Api::_()->getDbtable('content', 'sitestore');
      
      //GET CONTENT TABLE NAME
      $contentTableName = $this->info('name');
      
      //INSERTING MAIN CONTAINER
      $mainContainer = $this->createRow();
      $mainContainer->contentstore_id = $store_id;
      $mainContainer->type = 'container';
      $mainContainer->name = 'main';
      $mainContainer->order = 2;
      $mainContainer->save();
      $container_id = $mainContainer->content_id;

      //INSERTING MAIN-MIDDLE CONTAINER
      $mainMiddleContainer = $this->createRow();
      $mainMiddleContainer->contentstore_id = $store_id;
      $mainMiddleContainer->type = 'container';
      $mainMiddleContainer->name = 'middle';
      $mainMiddleContainer->parent_content_id = $container_id;
      $mainMiddleContainer->order = 6;
      $mainMiddleContainer->save();
      $middle_id = $mainMiddleContainer->content_id;

      //INSERTING MAIN-LEFT CONTAINER
      $mainLeftContainer = $this->createRow();
      $mainLeftContainer->contentstore_id = $store_id;
      $mainLeftContainer->type = 'container';
      $mainLeftContainer->name = 'right';
      $mainLeftContainer->parent_content_id = $container_id;
      $mainLeftContainer->order = 4;
      $mainLeftContainer->save();
      $left_id = $mainLeftContainer->content_id;

      //INSERTING MAIN-MIDDLE TAB CONTAINER
      $middleTabContainer = $this->createRow();
      $middleTabContainer->contentstore_id = $store_id;
      $middleTabContainer->type = 'widget';
      $middleTabContainer->name = 'core.container-tabs';
      $middleTabContainer->parent_content_id = $middle_id;
      $middleTabContainer->order = 10;
      $middleTabContainer->params =  "{\"max\":\"$showmaxtab\"}";
      $middleTabContainer->save();
      $middle_tab = $middleTabContainer->content_id;

      //INSERTING THUMB PHOTO WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-cover-information-sitestore', $middle_id, 1, '', 'true');

      if(empty($sitestore_layout_cover_photo)) {

				//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');

				//INSERTING THUMB PHOTO WIDGET
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
					$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoremember.storecover-info-sitestoremembers', $middle_id, 2, '', 'true');
        }

				//INSERTING TITLE WIDGET
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.title-sitestore', $middle_id, 4, '', 'true');

				//INSERTING LIKE WIDGET
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.like-button', $middle_id, 5, '', 'true');

				//INSERTING FACEBOOK LIKE WIDGET
				if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse')) {
					$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'Facebookse.facebookse-sitestoreprofilelike', $middle_id, 6, '', 'true');
				}

				//INSERTING MAIN PHOTO WIDGET
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.mainphoto-sitestore', $left_id, 10, '', 'true');

      } else {
				//INSERTING STORE PROFILE STORE COVER PHOTO WIDGET
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-profile-breadcrumb', $middle_id, 1, '', 'true');

				//INSERTING THUMB PHOTO WIDGET
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.store-cover-information-sitestore', $middle_id, 2, '', 'true');
      }
      
      //INSERTING CONTACT DETAIL WIDGET
      $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.contactdetails-sitestore', $middle_id, 7, '', 'true');	
      
// 	    //INSERTING PHOTO STRIP WIDGET
// 		  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
// 		    $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.photorecent-sitestore', $middle_id, 8, '', 'true');
// 		  }

			//INSERTING OPTIONS WIDGET
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.options-sitestore', $left_id, 11, '', 'true');
					
			//INSERTING WRITE SOMETHING ABOUT WIDGET 
		  $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'seaocore.people-like', $left_id, 15, '', 'true');
		
	    //INSERTING RATING WIDGET 
			if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorereview.ratings-sitestorereviews', $left_id, 16, 'Ratings', 'true');
			}

		  //INSERTING BADGE WIDGET 
		  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorebadge')) {
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestorebadge.badge-sitestorebadge', $left_id, 17, 'Badge', 'true');
		  }
		  		  
			$social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitestore.socialshare-sitestore"}';
			
		  //INSERTING YOU MAY ALSO LIKE WIDGET 
		  $this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.suggestedstore-sitestore', $left_id, 18, 'You May Also Like', 'true', $social_share_default_code);

			
		  //INSERTING SOCIAL SHARE WIDGET 
			$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.socialshare-sitestore', $left_id, 19, 'Social Share', 'true');
			
//			//INSERTING FOUR SQUARE WIDGET 
//			Engine_Api::_()->getDbTable('content', 'sitestore')->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.foursquare-sitestore', $left_id, 20, '', 'true');
		
			//INSERTING INSIGHTS WIDGET 
// 			Engine_Api::_()->getDbTable('content', 'sitestore')->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.insights-sitestore', $left_id, 21, 'Insights', 'true');
	
			//INSERTING FEATURED OWNER WIDGET 
			Engine_Api::_()->getDbTable('content', 'sitestore')->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.featuredowner-sitestore', $left_id, 22, 'Owners', 'true');
		
			//INSERTING ALBUM WIDGET 
		  if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
		    Engine_Api::_()->getDbTable('content', 'sitestore')->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.albums-sitestore', $left_id, 23, 'Albums', 'true');
		  }		
		  
		  //INSERTING STORE PROFILE PLAYER WIDGET 
	    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
		    Engine_Api::_()->getDbTable('content', 'sitestore')->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoremusic.profile-player', $left_id, 24, '', 'true');
		  }		  	
		  
		  //INSERTING LINKED STORE WIDGET
	 	  Engine_Api::_()->getDbTable('content', 'sitestore')->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.favourite-store', $left_id, 25, 'Linked Stores', 'true');
		  

			if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct')) {
				$this->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestoreproduct.store-profile-products', $middle_tab, 1, 'Products', 'true', '{"columnHeight":325,"columnWidth":165,"defaultWidgetNo":13}');
			}

		  //INSERTING ACTIVITY FEED WIDGET
			if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
				$advanced_activity_params =   '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0","name":"advancedactivity.home-feeds"}';
				Engine_Api::_()->getDbTable('content', 'sitestore')->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'advancedactivity.home-feeds', $middle_tab, 1, 'Updates', 'true',$advanced_activity_params);
			} else {
				Engine_Api::_()->getDbTable('content', 'sitestore')->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'activity.feed', $middle_tab, 1, 'Updates', 'true');
			}
			
		  //INSERTING INFORAMTION WIDGET
		  Engine_Api::_()->getDbTable('content', 'sitestore')->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.info-sitestore', $middle_tab, 2, 'Info', 'true');
		  
		  //INSERTING OVERVIEW WIDGET
		  Engine_Api::_()->getDbTable('content', 'sitestore')->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.overview-sitestore', $middle_tab, 3, 'Overview', 'true');
		 
		  //INSERTING LOCATION WIDGET
		  Engine_Api::_()->getDbTable('content', 'sitestore')->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'sitestore.location-sitestore', $middle_tab, 4, 'Map', 'true');
		  		    
		  //INSERTING LINKS WIDGET
		  Engine_Api::_()->getDbTable('content', 'sitestore')->setDefaultDataUserWidget($contentTable, $contentTableName, $store_id, 'widget', 'core.profile-links', $middle_tab, 125, 'Links', 'true');

	    //INSERTING ALBUM WIDGET 
	    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorealbum')) {
	      Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestore.photos-sitestore', $store_id, 'Photos', 'true', '110');
	    }
	
	    //INSERTING VIDEO WIDGET
	    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorevideo')) {
	      Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestorevideo.profile-sitestorevideos', $store_id, 'Videos', 'true', '111');
	    }
	
	    //INSERTING NOTE WIDGET 
	    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorenote')) {
	      Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestorenote.profile-sitestorenotes', $store_id, 'Notes', 'true', '112');
	    }
	
	    //INSERTING REVIEW WIDGET
	    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
	      Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestorereview.profile-sitestorereviews', $store_id, 'Reviews', 'true', '113');
	    }
      
	    //INSERTING FORM WIDGET 
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreform')) {
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestoreform.sitestore-viewform', $store_id, 'Form', 'false', '114');
      }

      //INSERTING DOCUMENT WIDGET
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoredocument')) {
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestoredocument.profile-sitestoredocuments', $store_id, 'Documents', 'true', '115');
      }
      
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document')) {
      Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('document.contenttype-documents', $store_id, 'Documents', 'true', '115');
    }

      //INSERTING OFFER WIDGET
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreoffer')) {
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestoreoffer.profile-sitestoreoffers', $store_id, 'Coupons', 'true', '116');
      }

      //INSERTING EVENT WIDGET
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreevent')) {
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestoreevent.profile-sitestoreevents', $store_id, 'Events', 'true', '117');
      }

			//INSERTING EVENT WIDGET
			if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')) {
				Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('siteevent.contenttype-events', $store_id, 'Events', 'true', '117');
			}

      //INSERTING POLL WIDGET
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorepoll')) {
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestorepoll.profile-sitestorepolls', $store_id, 'Polls', 'true', '118');
      }

      //INSERTING DISCUSSION WIDGET
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorediscussion')) {
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestore.discussion-sitestore', $store_id, 'Discussions', 'true', '119');
      }
      
      //INSERTING NOTE WIDGET 
	    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremusic')) {
	      Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestoremusic.profile-sitestoremusic', $store_id, 'Music', 'true', '120');
	    }
      //INSERTING TWITTER WIDGET 
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoretwitter')) {
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestoretwitter.feeds-sitestoretwitter', $store_id, 'Twitter', 'true', '121');
      }

      //INSERTING MEMBER WIDGET
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoremember')) {
				Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestoremember.profile-sitestoremembers', $store_id, 'Members', 'true', '122');
				Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestoremember.profile-sitestoremembers-announcements', $store_id, 'Announcements', 'true', '123');
      }
      
      //INSERTING MEMBER WIDGET
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration')) {
        Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestoreintegration.profile-items', $store_id, '', '', 999);
      }
    } else {
      Engine_Api::_()->getDbtable('content', 'sitestore')->setWithoutTabLayout($store_id, $sitestore_layout_cover_photo);
    }
  }

  /**
   * Gets content id,parama,name
   *
   * @param int $contentstore_id
   * @return content id,parama,name
   */  
  public function getContents($contentstore_id) {
  	
    $selectStoreAdmin = $this->select()
											               ->from($this->info('name'), array('content_id', 'params', 'name'))
											               ->where('contentstore_id =?', $contentstore_id)
											               ->where("name IN ('sitestore.overview-sitestore', 'sitestore.photos-sitestore', 'sitestore.discussion-sitestore', 'sitestorenote.profile-sitestorenotes', 'sitestorepoll.profile-sitestorepolls', 'sitestoreevent.profile-sitestoreevents', 'sitestorevideo.profile-sitestorevideos', 'sitestoreoffer.profile-sitestoreoffers', 'sitestorereview.profile-sitestorereviews', 'sitestoredocument.profile-sitestoredocuments', 'sitestoreform.sitestore-viewform','sitestore.info-sitestore', 'seaocore.feed', 'activity.feed', 'sitestore.location-sitestore', 'core.profile-links', 'sitestoremusic.profile-sitestoremusic', 'sitestoreintegration.profile-items','sitestoretwitter.feeds-sitestoretwitter', 'sitestoremember.profile-sitestoremembers', 'sitestoreproduct.store-profile-products', 'siteevent-contenttype-events', 'sitevideo-contenttype-videos', 'document-contenttype-documents')");
    return $this->fetchAll($selectStoreAdmin);
  }
  
  /**
   * Gets content_id
   *
   * @param int $contentstore_id
   * @return $params
   */      
  public function getContentId($contentstore_id, $sitestore) {
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
            ->where('contentstore_id = ?', $contentstore_id)
            ->where('type = ?', 'container')
            ->where('name = ?', 'main')
            ->limit(1);
    $content = $select_content->query()->fetchAll();
    if (!empty($content)) {
    	$select = $this->select();
      $select_container = $select
              ->from($this->info('name'), array('content_id'))
              ->where('contentstore_id = ?', $contentstore_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->where("name NOT IN ('	sitestore.title-sitestore', 'seaocore.like-button', 'sitestore.photorecent-sitestore')")
              ->limit(1);
      $container = $select_container->query()->fetchAll();
      if (!empty($container)) {
      	$select = $this->select();
        $container_id = $container[0]['content_id'];          
        $select_middle = $select
                ->from($this->info('name'))
                ->where('parent_content_id = ?', $container_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'core.container-tabs')
                ->where('contentstore_id = ?', $contentstore_id)
                ->limit(1);
        $middle = $select_middle->query()->fetchAll();
        if (!empty($middle)) {
          $content_id = $middle[0]['content_id'];
        } else {
        	$content_id = $container_id;
        }
      }
    } 

    if (!empty($content_id)) {
      $select = $this->select();
      $select_middle = $select
              ->from($this->info('name'), array('content_id', 'name', 'params'))
              ->where('parent_content_id = ?', $content_id)
              ->where('type = ?', 'widget')
              ->where("name NOT IN ('sitestore.title-sitestore', 'seaocore.like-button', 'sitestore.photorecent-sitestore', 'Facebookse.facebookse-commonlike', 'sitestore.thumbphoto-sitestore')")
              ->where('contentstore_id = ?', $contentstore_id)
              ->order('order')
      ;

      $select = $this->select();
      $select_photo = $select
              ->from($this->info('name'), array('params'))
              ->where('parent_content_id = ?', $content_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitestore.photos-sitestore')->where('contentstore_id = ?', $contentstore_id)
              ->order('content_id ASC');

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
      $content_ids = '';
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
          case 'advancedactivity.home-feeds':
            $flag = true;
            break;
          case 'activity.feed':
            $flag = true;
            break;
          case 'seaocore.feed':
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
              $memberCount = Engine_Api::_()->sitestore()->getTotalCount($sitestore->store_id, 'sitestoremember', 'membership');
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

							if (empty($sitestore->approved) || !empty($sitestore->closed) || empty($sitestore->search) || empty($sitestore->draft) || !empty($sitestore->declined)) {
								$flag = false;
							} else {
								$flag = true;
							}
	
							//PACKAGE BASE PRIYACY START
							if (!Engine_Api::_()->sitestore()->hasPackageEnable() && !empty($flag)) {
								$canStoreCreate = Engine_Api::_()->sitestoreproduct()->getLevelSettings("allow_store_create");
								if (empty($canStoreCreate)) {
									$flag = false;
								} else {
									$flag = true;
								}
							} 
           break;
        }
        if (!empty($flag)) {
          $content_ids = $value['content_id'];
          $content_names = $value['name'];
          break;
        }
      }
    }

    return array('content_id' => $content_ids, 'content_name' => $content_names, 'itemAlbumCount' => $itemAlbumCount, 'itemPhotoCount' => $itemPhotoCount, 'resource_type_integration' => $resource_type_integration, 'ads_display_integration' => $ads_display_integration);
  }
  
  /**
   * Gets content_id, name
   *
   * @param int $contentstore_id
   * @return content_id, name
   */       
  public function getContentInformation($contentstore_id) {
    $select = $this->select()->from($this->info('name'), array('content_id', 'name'))
		          ->where("name IN ('sitestore.info-sitestore', 'seaocore.feed', 'advancedactivity.home-feeds','activity.feed', 'sitestore.location-sitestore', 'core.profile-links', 'core.html-block')")
                   ->where('contentstore_id = ?', $contentstore_id)->order('content_id ASC');
    
    return $this->fetchAll($select);
  }
  
  /**
   * Gets content_id, name
   *
   * @param int $contentstore_id
   * @param int $name 
   * @return content_id, name
   */   
  public function getContentByWidgetName($name, $contentstore_id) {
   $select = $this->select()->from($this->info('name'), array('content_id', 'name'))
            ->where('name =?', $name)
            ->where('contentstore_id = ?', $contentstore_id)
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
                    ->where('content_id = ?', $tab_main)
                    ->query()
                    ->fetchColumn();
    return $current_tab_name;
  }
  
  public function checkWidgetExist($store_id = 0, $widgetName) {
  
		$params = $this->select()
						->from($this->info('name'),'params')
						->where('contentstore_id = ?', $store_id)
						->where('name = ?', $widgetName)
						->where('type = ?', 'widget')
						->query()->fetchColumn();
		return $params;
  
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
								->from('engine4_sitestore_content')
								->where('parent_content_id = ?', $tab_id)
								->where('type = ?', 'widget')
								->where('name = ?', 'sitestoreintegration.profile-items')
								->where('params = ?', $params);
				$info = $select->query()->fetch();
				if (empty($info)) {

					// tab on profile
					$db->insert('engine4_sitestore_content', array(
							'contentstore_id' => $store_id,
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