<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Plugin_Sitemobile {

  protected $_pagesTable;
  protected $_contentTable;

  public function onIntegrated() {

    $this->_pagesTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable = Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    //Store Plugin Main
    $this->addSitestoreHomeStore();
    $this->addSitestoreBrowseStore();
    $this->addSitestoreProfileStore();
    $this->addSitestoreManageStore();
    $this->addSitestoreManageAdminStore();
    $this->addSitestoreManageLikeStore();
    $this->addSitestoreManageJoinedStore();
    $this->addSitestoreStores();
		include APPLICATION_PATH . "/application/modules/Sitestore/controllers/license/mobileLayoutCreation.php";
  }

  //Store plugin main stores
  public function addSitestoreProfileStore() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // Check if it's already been placed
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'sitestore_index_view')
            ->limit(1);

    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestore_index_view',
          'displayname' => 'Stores - Store Profile',
          'title' => 'Stores / Marketplace Profile',
          'description' => 'This is a stores / marketplace profile.',
          'custom' => 0,
      ));
      $store_id = $db->lastInsertId($this->_pagesTable);

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 2,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId($this->_contentTable);

				$db->insert($this->_contentTable, array(
						'page_id' => $store_id,
						'type' => 'widget',
						'name' => 'sitestore.closestore-sitestore',
						'parent_content_id' => $middle_id,
						'order' => 1,
				));

        $db->insert($this->_contentTable, array(
            'page_id' => $store_id,
            'type' => 'widget',
            'name' => 'sitestore.sitemobile-storecover-photo-information',
            'parent_content_id' => $middle_id,
            'order' => 2,
            'params' => '{"title":"","titleCount":true,"showContent":["mainPhoto","title","sponsored","featured","category","subcategory","subsubcategory","likeButton","followButton","description","phone","email","website","location","tags","price"],"strachPhoto":"0"}',
        ));
    // }

      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $middle_id,
          'order' => 5,
          'params' => '{"max":6}',
      ));
      $tab_id = $db->lastInsertId($this->_contentTable);
      
      //PRODUCTS WIDGET
//      $db->insert($this->_contentTable, array(
//          'page_id' => $store_id,
//          'type' => 'widget',
//          'name' => 'sitestoreproduct.store-profile-products',
//          'parent_content_id' => $tab_id,
//          'order' => 90,
//          'params' => '{"title":"Products","titleCount":true,"layouts_views":["1","2"],"viewType":"listview","columnHeight":"225","columnWidth";"165","category_id":"0","postedby":"0","truncation":"25","truncationGrid":"32","statistics":[],"name":"sitestoreproduct.store-profile-products"}',
//      ));
      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advfeed',
          'parent_content_id' => $tab_id,
          'order' => 100,
          'params' => '{"title":"Updates"}',
      ));
      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'widget',
          'name' => 'sitestore.sitemobile-info-sitestore',
          'parent_content_id' => $tab_id,
          'order' => 200,
          'params' => '{"title":"Info"}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'widget',
          'name' => 'sitestore.sitemobile-overview-sitestore',
          'parent_content_id' => $tab_id,
          'order' => 300,
          'params' => '{"title":"Overview","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'widget',
          'name' => 'sitestore.sitemobile-location-sitestore',
          'parent_content_id' => $tab_id,
          'order' => 400,
          'params' => '{"title":"Map","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'widget',
          'name' => 'seaocore.sitemobile-people-like',
          'parent_content_id' => $tab_id,
          'order' => 3000,
          'params' => '{"title":"Member Likes","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'widget',
          'name' => 'seaocore.sitemobile-followers',
          'parent_content_id' => $tab_id,
          'order' => 3100,
          'params' => '{"title":"Followers","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'widget',
          'name' => 'sitestore.featuredowner-sitestore',
          'parent_content_id' => $tab_id,
          'order' => 3200,
          'params' => '{"title":"Store Admins","titleCount":true}',
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'widget',
          'name' => 'sitestore.favourite-store',
          'parent_content_id' => $tab_id,
          'order' => 3300,
          'params' => '{"title":"Linked Stores","titleCount":true}',
      ));

      //tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'widget',
          'name' => 'sitemobile.profile-links',
          'parent_content_id' => $tab_id,
          'order' => 3500,
          'params' => '{"title":"Links","titleCount":true}',
      ));
    }
  }

  public function addSitestoreHomeStore() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_index_home');
    // insert if it doesn't exist yet
    if (!$store_id) {
      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestore_index_home',
          'displayname' => 'Stores - Stores Home ',
          'title' => 'Stores / Marketplace Home Store',
          'description' => 'This store stores / marketplace home store.',
          'custom' => 0,
      ));
      $store_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $store_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $store_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
      //Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitestore.categories-sitestore',
          'page_id' => $store_id,
          'params' => '{"title":"Categories","nomobile":"0","name":"sitestore.categories-sitestore"}',
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
      
      $db->insert($this->_contentTable, array(
                'page_id' => $store_id,
                'type' => 'widget',
                'name' => 'sitemobile.container-tabs-columns',
                'parent_content_id' => $main_middle_id,
                'order' => 5,
                'params' => '{"max":6}',
                'module' => 'sitemobile'
            ));
        $tab_id = $db->lastInsertId($this->_contentTable);

        // Insert content
        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitestore.sitemobile-popular-stores',
            'page_id' => $store_id,
            'parent_content_id' => $tab_id,
            'order' => 4,
            'module' => 'sitestore',
            'params' => '{"title":"Recent","titleCount":true,"layouts_views":["1","2"],"columnHeight":"225","category_id":"0","content_display":["date","owner","ratings","likeCount","reviewCount","viewCount"],"name":"sitestore.sitemobile-popular-stores",
            "viewType":"gridview","popularity":"Recently Posted",
            "itemCount":"5","truncation":"16"}',
        ));

        // Insert content
        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitestore.sitemobile-popular-stores',
            'page_id' => $store_id,
            'parent_content_id' => $tab_id,
            'order' => 1,
            'module' => 'sitestore',
            'params' => '{"title":"Most Viewed","titleCount":true,"layouts_views":["1","2"],"columnHeight":"225","category_id":"0","content_display":["date","owner","ratings","likeCount","reviewCount","viewCount"],"name":"sitestore.sitemobile-popular-stores",
            "viewType":"gridview","popularity":"Most Viewed",
            "itemCount":"5","truncation":"16"}',

        ));

        // Insert content
        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitestore.sitemobile-popular-stores',
            'page_id' => $store_id,
            'parent_content_id' => $tab_id,
            'order' => 2,
            'module' => 'sitestore',
            'params' => '{"title":"Featured","titleCount":true,"layouts_views":["1","2"],"columnHeight":"225","category_id":"0","content_display":["date","owner","ratings","likeCount","reviewCount","viewCount"],"name":"sitestore.sitemobile-popular-stores",
            "viewType":"gridview","popularity":"Featured",
            "itemCount":"5","truncation":"16"}',
             ));

        // Insert content
        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitestore.sitemobile-popular-stores',
            'page_id' => $store_id,
            'parent_content_id' => $tab_id,
            'order' => 3,
            'module' => 'sitestore',
            'params' => '{"title":"Sponosred","titleCount":true,"layouts_views":["1","2"],"columnHeight":"225","category_id":"0","content_display":["date","owner","ratings","likeCount","reviewCount","viewCount"],"name":"sitestore.sitemobile-popular-stores",
            "viewType":"gridview","popularity":"Sponosred",
            "itemCount":"5","truncation":"16"}',
             ));

    }
  }

  public function addSitestoreBrowseStore() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_index_index');
    // insert if it doesn't exist yet
    if (!$store_id) {
      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestore_index_index',
          'displayname' => 'Stores - Browse Store',
          'title' => 'Browse Stores / Marketplace',
          'description' => 'This store lists stores / marketplace entries.',
          'custom' => 0,
      ));
      $store_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $store_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $store_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
      //Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      // Insert Alphabetic Filtering
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitestore.alphabeticsearch-sitestore',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
      ));
 
      //Commented code.
      if(false){        
          if ($this->_contentTable == 'engine4_sitemobile_tablet_content') {
            //Tablet Parameters for store listing
            $params = '{"title":"","titleCount":true,"layouts_views":["1","2"],"layouts_oder":"2","columnHeight":"325","category_id":"0","content_display":["featured","sponsored","closed","ratings","date","owner","likeCount","followCount","memberCount","reviewCount","commentCount","viewCount","location","price"],"name":"sitestore.sitemobile-stores-sitestore"}';        
          }else{
            //Mobile Parameters for store listing
            $params = '{"title":"","titleCount":true,"layouts_views":["1","2"],"view_selected":"grid","columnHeight":"325","category_id":"0","content_display":["featured","sponsored","closed","ratings","date","owner","likeCount","followCount","memberCount","reviewCount","commentCount","viewCount","location","price"],"name":"sitestore.sitemobile-stores-sitestore"}';   
          }
      }
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitestore.sitemobile-stores-sitestore',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"title":"","titleCount":true,"layouts_views":["1","2"],"layouts_oder":"2","columnHeight":"240","category_id":"0","content_display":["ratings","likeCount","followCount","memberCount","reviewCount","location","price"],"name":"sitestore.sitemobile-stores-sitestore"}',
          'order' => 3,
      ));
    }
  }

  public function addSitestoreManageStore() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile store
    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_index_manage');
    // insert if it doesn't exist yet
    if (!$store_id) {
      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestore_index_manage',
          'displayname' => 'Stores - Manage Store',
          'title' => 'My Stores / Marketplace',
          'description' => 'This store lists a user\'s Stores / Marketplace\'s.',
          'custom' => 0,
      ));
      $store_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $store_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $store_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
      //Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }

    return $this;
  }

  //Stores i admin
  public function addSitestoreManageAdminStore() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile store
    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_manageadmin_my-stores');
    // insert if it doesn't exist yet
    if (!$store_id) {
      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestore_manageadmin_my-stores',
          'displayname' => 'Stores - Manage Store (Stores I Admin)',
          'title' => 'Stores I Admin',
          'description' => 'This store lists a user\'s Stores / Marketplace\'s of which user\'s is admin.',
          'custom' => 0,
      ));
      $store_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $store_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $store_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }

    return $this;
  }

  //Stores i like
  public function addSitestoreManageLikeStore() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile store
    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_like_mylikes');
    // insert if it doesn't exist yet
    if (!$store_id) {
      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestore_like_mylikes',
          'displayname' => 'Stores - Manage Store (Stores I Like)',
          'title' => 'Stores I Like',
          'description' => 'This store lists a user\'s Stores / Marketplace\'s which user\'s likes.',
          'custom' => 0,
      ));
      $store_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $store_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $store_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }

    return $this;
  }

  //Stores i joined
  public function addSitestoreManageJoinedStore() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile store
    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_like_my-joined');
    // insert if it doesn't exist yet
    if (!$store_id) {
      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestore_like_my-joined',
          'displayname' => 'Stores - Manage Store (Stores I\'ve Joined)',
          'title' => 'Stores i Joined',
          'description' => 'This store lists a user\'s Stores / Marketplace\'s which user\'s have joined.',
          'custom' => 0,
      ));
      $store_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $store_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $store_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }

    return $this;
  }

  public function addSitestoreStores() {
  //  $this->setDefaultWidgetForSitestore('content', 'stores'); //::- NOT ADD Widget on USER Profile Page at installation
  //  $this->setDefaultWidgetForSitestore('tabletcontent', 'tabletstores'); //::- NOT ADD Widget on USER Profile Page at installation
  }

  public function setDefaultWidgetForSitestore($content, $stores) {
    // install content areas

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile store
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $store_id = $select->query()->fetchObject()->page_id;


    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $store_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitestore.profile-sitestore')
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $store_id)
              ->where('type = ?', 'container')
              ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
              ->reset('where')
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.container-tabs-columns')
              ->where('page_id = ?', $store_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $store_id,
          'type' => 'widget',
          'name' => 'sitestore.profile-sitestore',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 11,
          'params' => '{"title":"Stores","titleCount":true}',
          'module' => 'sitestore'
      ));
    }
  }

}