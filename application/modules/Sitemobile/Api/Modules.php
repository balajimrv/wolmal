<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Modules.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Api_Modules extends Core_Api_Abstract {

  public $_pagesTable = 'engine4_sitemobile_pages';
  public $_contentTable = 'engine4_sitemobile_content';
  public $_forApp = false;
  public $_onlyForApp = false;

  public function initTables($type) {
    if ($type == 'tablet') {
      $this->_pagesTable = 'engine4_sitemobile_tablet_pages';
      $this->_contentTable = 'engine4_sitemobile_tablet_content';
    } elseif ($type == 'tabletapp') {
      $this->_pagesTable = 'engine4_sitemobileapp_tablet_pages';
      $this->_contentTable = 'engine4_sitemobileapp_tablet_content';
    } elseif ($type == 'mobileapp') {
      $this->_pagesTable = 'engine4_sitemobileapp_pages';
      $this->_contentTable = 'engine4_sitemobileapp_content';
    } else {
      $this->_pagesTable = 'engine4_sitemobile_pages';
      $this->_contentTable = 'engine4_sitemobile_content';
    }
  }

  public function addModules($type) {
    if ($type == 'tablet')
      return;
    $this->addDefault();
    $modulestable = Engine_Api::_()->getDbtable('modules', 'sitemobile');
    $this->_enabledModuleNames = $modulestable->getEnabledModuleNames();
    foreach ($this->_enabledModuleNames as $moduleName) {
      $this->addModuleStart($moduleName);
    }
  }

  public function preAddModule($moduleName) {
    if (in_array($moduleName, array('album', 'video', 'poll', 'user', 'core', 'music', 'forum', 'group', 'event', 'messages', 'activity', 'blog', 'birthday', 'advancedactivity', 'fields', 'payment')))
      return;
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->query("UPDATE `engine4_sitemobile_modules` SET `integrated` = '0' WHERE `name` LIKE  '$moduleName%';");
    $db->query("DELETE FROM `engine4_sitemobile_pages` WHERE `name` LIKE  '%$moduleName%';");
    $db->query("DELETE FROM `engine4_sitemobile_content` WHERE `name` LIKE  '%$moduleName%';");
    $db->query("DELETE FROM `engine4_sitemobile_tablet_pages` WHERE `name` LIKE  '%$moduleName%';");
    $db->query("DELETE FROM `engine4_sitemobile_tablet_content` WHERE `name` LIKE  '%$moduleName%';");
    $db->query("DELETE FROM `engine4_sitemobile_menuitems` WHERE `name` LIKE  '%$moduleName%';");
    $db->query("DELETE FROM `engine4_sitemobile_menus` WHERE `name` LIKE  '%$moduleName%';");
    $db->query("DELETE FROM `engine4_sitemobile_navigation` WHERE `name` LIKE  '%$moduleName%';");
    $db->query("DELETE FROM `engine4_sitemobile_searchform` WHERE `name` LIKE  '%$moduleName%';");
    $hasTable = $db->query('SHOW TABLES LIKE \'engine4_sitemobileapp_content\'')->fetch();
    if (!empty($hasTable)) {
      $db->query("DELETE FROM `engine4_sitemobileapp_pages` WHERE `name` LIKE  '%$moduleName%';");
      $db->query("DELETE FROM `engine4_sitemobileapp_content` WHERE `name` LIKE  '%$moduleName%';");
      $db->query("DELETE FROM `engine4_sitemobileapp_tablet_pages` WHERE `name` LIKE  '%$moduleName%';");
      $db->query("DELETE FROM `engine4_sitemobileapp_tablet_content` WHERE `name` LIKE  '%$moduleName%';");
    }
  }

  public function addModulesOnlyForApp() {
    $this->_onlyForApp = true;
    $this->addDefault();
    $modulestable = Engine_Api::_()->getDbtable('modules', 'sitemobile');
    $this->_enabledModuleNames = $modulestable->getEnabledModuleNames();

    foreach ($this->_enabledModuleNames as $moduleName) {
      if ($moduleName == 'sitemobile') {
        continue;
      }
      $this->addModuleStart($moduleName);
    }
    $this->_onlyForApp = false;
  }

  public function addDefault() {
    if (!$this->_onlyForApp) {
      $this->initTables('mobile');
      $this->addDefaultPage();
      $this->initTables('tablet');
      $this->addDefaultPage();
    }
    if (Engine_Api::_()->hasModuleBootstrap('sitemobileapp')) {
      $this->_forApp = true;
      $this->initTables('mobileapp');
      $this->addDefaultPage();
      $this->initTables('tabletapp');
      $this->addDefaultPage();
      $this->_forApp = false;
    }
  }

  public function addDefaultPage() {
    //pages 
    $this->addMainPages();
    $this->addCorePages();
    $this->addUserPages();
    $this->addSettingsPages();
  }

  public function addModuleStart($moduleName) {

    if (!$this->_onlyForApp) {
      if (!in_array($moduleName, array('core', 'user', 'sitemobile')))
        $this->preAddModule($moduleName);
      $this->initTables('mobile');
      $this->addModule($moduleName);
      $this->initTables('tablet');
      $this->addModule($moduleName);
    }
    if (Engine_Api::_()->hasModuleBootstrap('sitemobileapp')) {
      $this->_forApp = true;
      $this->initTables('mobileapp');
      $this->addModule($moduleName);
      $this->initTables('tabletapp');
      $this->addModule($moduleName);
      $this->_forApp = false;
    }
    $moduleDir = $this->inflictModule($moduleName);

    $sqlFile = APPLICATION_PATH . "/application/modules/$moduleDir/settings/sitemobile/my.sql";
    if (file_exists($sqlFile)) {
      $contents = file_get_contents($sqlFile);
      if ($contents) {
        $db = Engine_Db_Table::getDefaultAdapter();
        foreach (Engine_Package_Utilities::sqlSplit($contents) as $sqlFragment) {
          $db->query($sqlFragment);
        }
      }
    }
  }

  public function inflictModule($moduleName) {
    return str_replace(' ', '', ucwords(str_replace('-', ' ', $moduleName)));
  }

  public function addModule($moduleName) {

    $moduleDir = $this->inflictModule($moduleName);
    $modulestable = Engine_Api::_()->getDbtable('modules', 'sitemobile');
    $file = APPLICATION_PATH . '/application/modules/' . $moduleDir . '/Plugin/Sitemobile.php';
    if (file_exists($file)) {
      try {
        $pluginName = $moduleDir . "_Plugin_Sitemobile";
        $plugin = Engine_Api::_()->loadClass($pluginName);
      } catch (Exception $e) {
        //Silence exceptions
        //continue;
      }
      if (method_exists($plugin, 'onIntegrated')) {
        $plugin->onIntegrated($this->_pagesTable, $this->_contentTable);
      }
    } else {
      $addModuleNamePages = 'add' . $moduleDir . 'Pages';
      if (method_exists($this, $addModuleNamePages)) {
        $this->$addModuleNamePages();
      }
    }

    $existModuleName = $modulestable->select()->from($modulestable->info('name'), array('name'))->where('name =?', $moduleName)->query()->fetchColumn();
    if ($existModuleName) {
      if (Engine_Api::_()->hasModuleBootstrap('sitemobileapp')) {
        $updateModuleArray = array(
            'integrated' => 1,
            'enable_mobile' => 1,
            'enable_tablet' => 1,
            'enable_mobile_app' => 1,
            'enable_tablet_app' => 1);
      } else {
        $updateModuleArray = array(
            'integrated' => 1,
            'enable_mobile' => 1,
            'enable_tablet' => 1);
      }
      $modulestable->update($updateModuleArray, array('name = ?' => $moduleName));
    } else {
      if (Engine_Api::_()->hasModuleBootstrap('sitemobileapp')) {
        $insertModuleArray = array(
            'name' => $moduleName,
            'visibility' => 1,
            'integrated' => 1,
            'enable_mobile' => 1,
            'enable_tablet' => 1,
            'enable_mobile_app' => 1,
            'enable_tablet_app' => 1);
      } else {
        $insertModuleArray = array(
            'name' => $moduleName,
            'visibility' => 1,
            'integrated' => 1,
            'enable_mobile' => 1,
            'enable_tablet' => 1);
      }

      $modulestable->insert($insertModuleArray);
    }
  }

  public function addMainPages() {
    $this->addSiteHeader();
    $this->addSiteFooter();
    $this->addHomePage();
    $this->addUserHomePage();
    $this->addUserProfilePage();
    $this->addDashboardPanelPage();
    $this->addDashboardPage();
    $this->addStartupPage();
  }

  public function addCorePages() {

    //Contact,privacy & terms of services pages.
    $this->addContactPage();
    $this->addPrivacyPage();
    $this->addTermsOfServicePage();
    $this->addMemberBrowsePage();
    $this->addNotificationPage();
    if (method_exists($this, 'addGenericPage')) {
      $this->addGenericPage('core_error_requireuser', 'Sign-in Required', 'Sign-in Required Page', '');
      $this->addGenericPage('core_search_index', 'Search', 'Search Page', '');
    } else {
      $this->_error('Missing addGenericPage method');
    }

    if ($this->_pagesTable == 'engine4_sitemobileapp_pages' || $this->_pagesTable == 'engine4_sitemobileapp_tablet_pages') {
      $this->addFriendsPage();
    }
  }

  public function addUserPages() {
    //Sign up & sign in pages
    $this->addGenericPage('user_auth_login', 'Sign-in', 'Sign-in Page', 'This is the site sign-in page.');
    $this->addGenericPage('user_signup_index', 'Sign-up', 'Sign-up Page', 'This is the site sign-up page.');
    $this->addGenericPage('user_auth_forgot', 'Reset Password', 'Forgot Password Page', 'This is the site forgot password page.');
  }

  public function addSettingsPages() {
    //Setting pages
    $this->addGeneral();
    $this->addPrivacy();
    $this->addNetworks();
    $this->addNotifications();
    $this->addChangePassword();
    $this->addDeleteAccount();
  }

  public function addBlogPages() {
    //Blog pages
    $this->addBlogUserProfileContent();
    $this->addBlogListPage();
    $this->addBlogViewPage();
    $this->addBlogBrowsePage();
    $this->addBlogCreatePage();
    $this->addBlogManagePage();
  }

  public function addAlbumPages() {
    //Album pages
    $this->addAlbumUserProfileContent();
    $this->addAlbumPhotoViewPage();
    $this->addAlbumViewPage();
    $this->addAlbumBrowsePage();
    $this->addAlbumCreatePage();
    $this->addAlbumManagePage();
    $this->addAlbumPhotoPage();
  }

  public function addEventPages() {
    //Events pages
    $this->addEventUserProfileContent();
    $this->addEventPhotoViewPage();
    $this->addEventViewPage();
    $this->addEventBrowsePage();
    $this->addEventCreatePage();
    $this->addEventManagePage();

    //UPDATE - Menus change   
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->query("UPDATE `engine4_sitemobile_menuitems` SET `plugin` = 'Sitemobile_Plugin_eventMenus' WHERE `engine4_sitemobile_menuitems`.`name` = 'event_profile_invite'");
  }

  public function addGroupPages() {
    //Group pages
    $this->addGroupUserProfileContent();
    $this->addGroupPhotoViewPage();
    $this->addGroupViewPage();
    $this->addGroupBrowsePage();
    $this->addGroupCreatePage();
    $this->addGroupManagePage();
  }

  public function addVideoPages() {
    //Videos pages
    $this->addVideoUserProfileContent();
    $this->addVideoViewPage();
    $this->addVideoBrowsePage();
    $this->addVideoCreatePage();
    $this->addVideoManagePage();
  }

  public function addMessagesPages() {
    //Message Pages
    $this->addMessageInboxPage();
    $this->addMessageOutboxPage();
    $this->addMessageComposePage();
    $this->addMessageViewPage();
    $this->addMessageSearchPage();
  }

  public function addBirthdayPages() {
    //Birthday Pages
    $this->addBirthdayPage();

    //UPDATE - order change   
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->query("UPDATE `engine4_sitemobile_menuitems` SET `order` = '10' WHERE `engine4_sitemobile_menuitems`.`name` = 'event_main_birthday'");
  }

  public function addMusicPages() {
    // Music Pages
    $this->addMusicUserProfileContent();
    $this->addMusicBrowsePage();
    $this->addMusicCreatePage();
    $this->addMusicViewPage();
    $this->addMusicManagePage();
  }

  protected function addPollPages() {
    //polls pages
    $this->addPollUserProfileContent();
    $this->addPollBrowsePage();
    $this->addPollViewPage();
    $this->addPollCreatePage();
    $this->addPollManagePage();
  }

  protected function addForumPages() {
    $this->addForumUserProfileContent();
    $this->addForumIndexPage();
    $this->addForumViewPage();

    $this->addGenericPage('forum_topic_view', 'Forum Topic View', 'Forum Topic View Page', 'This is the forum topic view page.');
    $this->addGenericPage('forum_forum_topic-create', 'Post Topic', 'Forum Topic Create Page', 'This is the forum topic create page.');
  }

  //Get page id of pages from "sitemobile_pages" table.
  public function getPageId($page_name) {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $db->select()
            ->from($this->_pagesTable, 'page_id')
            ->where('name = ?', $page_name)
            ->limit(1)
            ->query()
            ->fetchColumn();
    return $page_id;
  }

  public function addGenericPage($page, $title, $displayname, $description) {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId($page);
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => $page,
          'displayname' => $displayname,
          'title' => $title,
          'description' => $description,
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'module' => 'core'
      ));
    }

    return $page_id;
  }

  //Mobile pages.
  public function addSiteHeader() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('header');
    if (!$page_id) {
      $db->insert($this->_pagesTable, array(
          'name' => 'header',
          'displayname' => 'Site Header',
          'title' => 'Site Header',
          'description' => 'This is the site header.',
          'custom' => 0,
          'fragment' => 1
      ));
      $page_id = $db->lastInsertId($this->_pagesTable);

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-notification-request-messages',
          'parent_content_id' => $container_id,
          'order' => 4,
          'params' => '',
          'module' => 'sitemobile'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-headingtitle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
          'module' => 'sitemobile'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-options',
          'parent_content_id' => $container_id,
          'order' => 3,
          'params' => '',
          'module' => 'sitemobile'
      ));
    }
  }

  public function addSiteFooter() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('footer');
    if (!$page_id) {
      $db->insert($this->_pagesTable, array(
          'name' => 'footer',
          'displayname' => 'Site Footer',
          'title' => 'Site Footer',
          'description' => 'This is the site footer.',
          'custom' => 0,
          'fragment' => 1
      ));
      $page_id = $db->lastInsertId($this->_pagesTable);

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      if (!$this->_forApp) {
        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemobile.sitemobile-footer',
            'parent_content_id' => $container_id,
            'order' => 2,
            'params' => '{"shows":["copyright","menusFooter","languageChooser","affiliateCode"]}',
            'module' => 'sitemobile'
        ));
      }
    }
  }

  public function addHomePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('core_index_index');
    if (!$page_id) {
     
       $db->insert($this->_pagesTable, array(
          'name' => 'core_index_index',
          'displayname' => 'Home Page',
          'title' => '',
          'description' => 'This is the  home page.',
          'custom' => 0,
          'layout' => 'default',
      ));
    
      $page_id = $db->lastInsertId($this->_pagesTable);

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.login-or-signup',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '',
          'module' => 'sitemobile'
      ));
    }
  }

  public function addUserHomePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('user_index_home');
    if (!$page_id) {
      $db->insert($this->_pagesTable, array(
          'name' => 'user_index_home',
          'displayname' => 'Member Home Page',
          'title' => 'Member Home Page',
          'description' => 'This is the member homepage.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId($this->_pagesTable);

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId($this->_contentTable);


      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advfeed',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '',
          'module' => 'advancedactivity'
      ));
    }
  }

  public function addUserProfilePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('user_profile_index');
    if (!$page_id) {
      $db->insert($this->_pagesTable, array(
          'name' => 'user_profile_index',
          'displayname' => 'Member Profile',
          'title' => 'Member Profile',
          'description' => 'This is a member profile.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId($this->_pagesTable);

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.profile-photo-status',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '',
          'module' => 'sitemobile'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $middle_id,
          'order' => 5,
          'params' => '{"max":6}',
          'module' => 'sitemobile'
      ));
      $tab_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advfeed',
          'parent_content_id' => $tab_id,
          'order' => 6,
          'params' => '{"title":"Updates"}',
          'module' => 'advancedactivity'
      ));
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.user-profile-info-fields',
          'parent_content_id' => $tab_id,
          'order' => 7,
          'params' => '{"showContent":["profileFields","memberType","networks","profileViews","lastUpdated","joined","enabled"],"title":"Info"}',
          'module' => 'user'
      ));

      $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
      if (empty($direction)) {
        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemobile.user-profile-friends',
            'parent_content_id' => $tab_id,
            'order' => 8,
            'params' => '{"title":"Friends","titleCount":true}',
            'module' => 'user'
        ));
      }

      if (!$direction) {
        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemobile.user-profile-friends-followers',
            'parent_content_id' => $tab_id,
            'order' => 91,
            'params' => '{"title":"Followers","titleCount":true}',
            'module' => 'user'
        ));


        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemobile.user-profile-friends-following',
            'parent_content_id' => $tab_id,
            'order' => 92,
            'params' => '{"title":"Following","titleCount":true}',
            'module' => 'user'
        ));
      }
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.user-profile-friends-common',
          'parent_content_id' => $tab_id,
          'order' => 93,
          'params' => '{"title":"Mutual Friends","titleCount":true}',
          'module' => 'user'
      ));
    }
  }

  //Dashboard panel page
  public function addDashboardPanelPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('dashboard_panel');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'dashboard_panel',
          'displayname' => 'Dashboard Panel Page',
          'title' => 'Dashboard Panel',
          'description' => 'This is the Dashboard Panel Page',
          'provides' => 'no-viewer;no-subject',
          'layout' => 'default-simple',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      if ($this->_forApp) {
        // containers
        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'container',
            'name' => 'top',
            'parent_content_id' => null,
            'order' => 1,
            'params' => '',
        ));
        $top_id = $db->lastInsertId($this->_contentTable);

        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'container',
            'name' => 'middle',
            'parent_content_id' => $top_id,
            'order' => 2,
            'params' => '',
        ));
        $top_middle_id = $db->lastInsertId();

        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitemobile.user-photo-title',
            'page_id' => $page_id,
            'parent_content_id' => $top_middle_id,
            'order' => 1,
            'module' => 'sitemobile'
        ));

        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitemobile.sitemobile-advancedsearch',
            'page_id' => $page_id,
            'parent_content_id' => $top_middle_id,
            'order' => 1,
            'params' => '{"search":"1","module_search":"core"}',
            'module' => 'sitemobile'
        ));
      }
      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $dashboardWidget = array(
          'type' => 'widget',
          'name' => 'sitemobile.dashboard',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      );
      if ($this->_forApp) {
        $dashboardWidget['params'] = '{"showSearch":"0"}';
      }
      $db->insert($this->_contentTable, $dashboardWidget);
      
      if ($this->_forApp) {
        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemobile.sitemobile-footer',
            'parent_content_id' => $middle_id,
            'order' => 2,
            'params' => '{"shows":["languageChooser"]}',
            'module' => 'sitemobile'
        ));
      }
    }
    return $this;
  }

  //Blog pages
  public function addBlogManagePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('blog_index_manage');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'blog_index_manage',
          'displayname' => 'Blog Manage Page',
          'title' => 'My Entries',
          'description' => 'This page lists a user\'s blog entries.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));


      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addBlogCreatePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('blog_index_create');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'blog_index_create',
          'displayname' => 'Blog Create Page',
          'title' => 'Write New Entry',
          'description' => 'This page is the blog create page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addBlogBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('blog_index_index');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'blog_index_index',
          'displayname' => 'Blog Browse Page',
          'title' => 'Blog Browse',
          'description' => 'This page lists blog entries.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addBlogListPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('blog_index_list');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'blog_index_list',
          'displayname' => 'Blog List Page',
          'title' => 'Blog List',
          'description' => 'This page lists a member\'s blog entries.',
          'provides' => 'subject=user',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addBlogViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('blog_index_view');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'blog_index_view',
          'displayname' => 'Blog View Page',
          'title' => 'Blog View',
          'description' => 'This page displays a blog entry.',
          'provides' => 'subject=blog',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-headingtitle',
          'parent_content_id' => $middle_id,
          'order' => 1,
          'params' => '{"title":"","nonloggedin":"1","loggedin":"1","nomobile":"0","notablet":"0","name":"sitemobile.sitemobile-headingtitle"}',
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 3,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addBlogUserProfileContent() {


    // install content areas

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;


    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitemobile.blog-profile-blogs')
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
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
              ->where('page_id = ?', $page_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.blog-profile-blogs',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 10,
          'params' => '{"title":"Blogs","titleCount":true}',
          'module' => 'blog'
      ));
    }
  }

  //Album pages

  public function addAlbumPhotoPage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('album_index_photos');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'album_index_photos',
          'displayname' => 'Album Browse Photo Page',
          'title' => 'Browse Photos',
          'description' => 'This page lists photo entries.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));


      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.album-browse-photos',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }

    return $this;
  }

  public function addAlbumManagePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('album_index_manage');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'album_index_manage',
          'displayname' => 'Album Manage Page',
          'title' => 'My Albums',
          'description' => 'This page lists album a user\'s albums.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }

    return $this;
  }

  public function addAlbumCreatePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('album_index_upload');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'album_index_upload',
          'displayname' => 'Album Create Page',
          'title' => 'Add New Photos',
          'description' => 'This page is the album create page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      //WE WILL NOT ADD THE NAVIGATION TAB ON APP AND TABLET APP.
      if ($this->_pagesTable == 'engine4_sitemobile_pages' || $this->_pagesTable == 'engine4_sitemobile_tablet_pages')
      // Insert menu
        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitemobile.sitemobile-navigation',
            'page_id' => $page_id,
            'parent_content_id' => $main_middle_id,
            'order' => 1,
            'module' => 'sitemobile'
        ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addAlbumPhotoViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('album_photo_view');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'album_photo_view',
          'displayname' => 'Album Photo View Page',
          'title' => 'Album Photo View',
          'description' => 'This page displays an album\'s photo.',
          'provides' => 'subject=album_photo',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }

    return $this;
  }

  public function addAlbumViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('album_album_view');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'album_album_view',
          'displayname' => 'Album View Page',
          'title' => 'Album View',
          'description' => 'This page displays an album\'s photos.',
          'provides' => 'subject=album',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-headingtitle',
          'parent_content_id' => $middle_id,
          'order' => 1,
          'params' => '{"title":"","nonloggedin":"1","loggedin":"1","nomobile":"0","notablet":"0","name":"sitemobile.sitemobile-headingtitle"}',
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 3,
          'module' => 'sitemobile'
      ));
    }

    return $this;
  }

  public function addAlbumBrowsePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('album_index_browse');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'album_index_browse',
          'displayname' => 'Album Browse Page',
          'title' => 'Album Browse',
          'description' => 'This page lists album entries.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

//      
      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }

    return $this;
  }

  public function addAlbumUserProfileContent() {
    // install content areas
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;


    // album.profile-albums
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitemobile.album-profile-albums')
    ;

    $info = $select->query()->fetch();

    if (empty($info)) {
      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
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
              ->where('page_id = ?', $page_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.album-profile-albums',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 9,
          'params' => '{"title":"Albums","titleCount":true}',
          'module' => 'album'
      ));

      return $this;
    }
  }

  //Event pages
  public function addEventManagePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('event_index_manage');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'event_index_manage',
          'displayname' => 'Event Manage Page',
          'title' => 'My Events',
          'description' => 'This page lists a user\'s events.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));


      // Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addEventCreatePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('event_index_create');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'event_index_create',
          'displayname' => 'Event Create Page',
          'title' => 'Event Create',
          'description' => 'This page allows users to create events.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addEventViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'event')
            ->limit(1);
    ;
    $event_module = $select->query()->fetch();

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'event_profile_index')
            ->limit(1);
    ;
    $info = $select->query()->fetch();

    if (empty($info) && !empty($event_module)) {
      $db->insert($this->_pagesTable, array(
          'name' => 'event_profile_index',
          'displayname' => 'Event Profile',
          'title' => 'Event Profile',
          'description' => 'This is the mobile verison of an event profile.',
          'custom' => 0
      ));
      $page_id = $db->lastInsertId($this->_pagesTable);

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.profile-photo-status',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '',
          'module' => 'sitemobile'
      ));


      if ($this->_pagesTable == 'engine4_sitemobileapp_pages' || $this->_pagesTable == 'engine4_sitemobileapp_tablet_pages') {
        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemobile.event-profile-options',
            'parent_content_id' => $middle_id,
            'order' => 4,
            'params' => '',
            'module' => 'event'
        ));
      }

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.event-profile-info',
          'parent_content_id' => $middle_id,
          'order' => 5,
          'params' => '',
          'module' => 'event'
      ));
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.event-profile-rsvp',
          'parent_content_id' => $middle_id,
          'order' => 6,
          'params' => '',
          'module' => 'event'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $middle_id,
          'order' => 7,
          'params' => '{"max":6}',
          'module' => 'sitemobile'
      ));
      $tab_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advfeed',
          'parent_content_id' => $tab_id,
          'order' => 8,
          'params' => '{"title":"Updates"}',
          'module' => 'advancedactivity'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.event-profile-members',
          'parent_content_id' => $tab_id,
          'order' => 10,
          'params' => '{"title":"Guests","titleCount":true}',
          'module' => 'event'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.event-profile-photos',
          'parent_content_id' => $tab_id,
          'order' => 11,
          'params' => '{"title":"Photos","titleCount":true}',
          'module' => 'event'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.event-profile-discussions',
          'parent_content_id' => $tab_id,
          'order' => 11,
          'params' => '{"title":"Discussions","titleCount":true}',
          'module' => 'event'
      ));
    }
  }

  public function addEventPhotoViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('event_photo_view');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'event_photo_view',
          'displayname' => 'Event Photo View Page',
          'title' => 'Event Photo View',
          'description' => 'This page displays an event\'s photo.',
          'provides' => 'subject=event_photo',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }

    return $this;
  }

  public function addEventBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('event_index_browse');

    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'event_index_browse',
          'displayname' => 'Event Browse Page',
          'title' => 'Event Browse',
          'description' => 'This page lists events.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addEventUserProfileContent() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    $page_id = $this->getPageId('user_profile_index');

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $hasProfileEvents = $select
            ->from($this->_contentTable, new Zend_Db_Expr('TRUE'))
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitemobile.event-profile-events')
            ->query()
            ->fetchColumn()
    ;

    // Add it
    if (!$hasProfileEvents) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $container_id = $select
              ->from($this->_contentTable, 'content_id')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'container')
              ->limit(1)
              ->query()
              ->fetchColumn()
      ;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $middle_id = $select
              ->from($this->_contentTable, 'content_id')
              ->where('parent_content_id = ?', $container_id)
              ->where('type = ?', 'container')
              ->where('name = ?', 'middle')
              ->limit(1)
              ->query()
              ->fetchColumn()
      ;

      // tab_id (tab container) may not always be there
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable, 'content_id')
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.container-tabs-columns')
              ->where('page_id = ?', $page_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = $middle_id;
      }

      // insert
      if ($tab_id) {
        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemobile.event-profile-events',
            'parent_content_id' => $tab_id,
            'order' => 14,
            'params' => '{"title":"Events","titleCount":true}',
            'module' => 'event'
        ));
      }
    }
  }

  public function addBirthdayPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('birthday_index_view');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'birthday_index_view',
          'displayname' => 'Birthday Page',
          'title' => 'Birthdays',
          'description' => 'This page lists friends birthday.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  //Group pages
  public function addGroupManagePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('group_index_manage');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'group_index_manage',
          'displayname' => 'Group Manage Page',
          'title' => 'My Groups',
          'description' => 'This page lists a user\'s groups.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));


      // Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addGroupCreatePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('group_index_create');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'group_index_create',
          'displayname' => 'Group Create Page',
          'title' => 'Group Create',
          'description' => 'This page allows users to create groups.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      //WE WILL NOT ADD THE NAVIGATION TAB ON APP AND TABLET APP.
      if ($this->_pagesTable == 'engine4_sitemobile_pages' || $this->_pagesTable == 'engine4_sitemobile_tablet_pages')
      // Insert menu
        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitemobile.sitemobile-navigation',
            'page_id' => $page_id,
            'parent_content_id' => $main_middle_id,
            'order' => 1,
            'module' => 'sitemobile'
        ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addGroupUserProfileContent() {
    //
    // install content areas
    //
  $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;

    // sitemobile.group-profile-groups
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitemobile.group-profile-groups')
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
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
              ->where('page_id = ?', $page_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = $middle_id;
      }

      // tab on profile
      if ($tab_id) {
        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemobile.group-profile-groups',
            'parent_content_id' => $tab_id,
            'order' => 12,
            'params' => '{"title":"Groups","titleCount":true}',
            'module' => 'group'
        ));
      }
    }
  }

  public function addGroupPhotoViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('group_photo_view');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'group_photo_view',
          'displayname' => 'Group Photo View Page',
          'title' => 'Group Photo View',
          'description' => 'This page displays an group\'s photo.',
          'provides' => 'subject=group_photo',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }

    return $this;
  }

  public function addGroupViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'group')
            ->limit(1);
    ;
    $group_module = $select->query()->fetch();

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'group_profile_index')
            ->limit(1);
    ;
    $info = $select->query()->fetch();

    if (empty($info) && !empty($group_module)) {
      $db->insert($this->_pagesTable, array(
          'name' => 'group_profile_index',
          'displayname' => 'Group Profile',
          'title' => 'Group Profile',
          'description' => 'This is the mobile verison of a group profile.',
          'custom' => 0
      ));
      $page_id = $db->lastInsertId($this->_pagesTable);

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.profile-photo-status',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '',
          'module' => 'sitemobile'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.group-profile-options',
          'parent_content_id' => $middle_id,
          'order' => 4,
          'params' => '',
          'module' => 'group'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.group-profile-info',
          'parent_content_id' => $middle_id,
          'order' => 5,
          'params' => '',
          'module' => 'group'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.container-tabs-columns',
          'parent_content_id' => $middle_id,
          'order' => 6,
          'params' => '{"max":6}',
          'module' => 'sitemobile'
      ));
      $tab_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advfeed',
          'parent_content_id' => $tab_id,
          'order' => 7,
          'params' => '{"title":"Updates"}',
          'module' => 'advancedactivity'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.group-profile-members',
          'parent_content_id' => $tab_id,
          'order' => 8,
          'params' => '{"title":"Members","titleCount":true}',
          'module' => 'group'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.group-profile-photos',
          'parent_content_id' => $tab_id,
          'order' => 9,
          'params' => '{"title":"Photos","titleCount":true}',
          'module' => 'group'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.group-profile-discussions',
          'parent_content_id' => $tab_id,
          'order' => 10,
          'params' => '{"title":"Discussions","titleCount":true}',
          'module' => 'group'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.group-profile-events',
          'parent_content_id' => $tab_id,
          'order' => 11,
          'params' => '{"title":"Events","titleCount":true}',
          'module' => 'group'
      ));
    }
  }

  public function addGroupBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('group_index_browse');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'group_index_browse',
          'displayname' => 'Group Browse Page',
          'title' => 'Group Browse',
          'description' => 'This page lists groups.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));


      // Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }
  }

  //Videos pages
  public function addVideoManagePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('video_index_manage');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'video_index_manage',
          'displayname' => 'Video Manage Page',
          'title' => 'My Videos',
          'description' => 'This page lists a user\'s videos.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addVideoCreatePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // create page
    $page_id = $this->getPageId('video_index_create');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'video_index_create',
          'displayname' => 'Video Create Page',
          'title' => 'Video Create',
          'description' => 'This page allows video to be added.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addVideoBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('video_index_browse');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'video_index_browse',
          'displayname' => 'Video Browse Page',
          'title' => 'Video Browse',
          'description' => 'This page lists videos.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();


      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addVideoUserProfileContent() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);


    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;

    if ($page_id) {
      // video.profile-videos
      // Check if it's already been placed
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.video-profile-videos')
      ;
      $info = $select->query()->fetch();

      if (empty($info)) {

        // container_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
                ->from($this->_contentTable)
                ->where('page_id = ?', $page_id)
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
                ->where('page_id = ?', $page_id)
                ->limit(1);
        $tab_id = $select->query()->fetchObject();
        if ($tab_id && @$tab_id->content_id) {
          $tab_id = $tab_id->content_id;
        } else {
          $tab_id = null;
        }

        // tab on profile
        $db->insert($this->_contentTable, array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitemobile.video-profile-videos',
            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
            'order' => 18,
            'params' => '{"title":"Videos","titleCount":true}',
            'module' => 'video'
        ));
      }
    }
  }

  public function addVideoViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'video_index_view')
            ->limit(1);
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert($this->_pagesTable, array(
          'name' => 'video_index_view',
          'displayname' => 'Video View Page',
          'title' => 'View Video',
          'description' => 'This is the view page for a video.',
          'custom' => 0,
          'provides' => 'subject=video',
      ));
      $page_id = $db->lastInsertId($this->_pagesTable);

      // containers
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $container_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 3,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId($this->_contentTable);

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-headingtitle',
          'parent_content_id' => $middle_id,
          'order' => 1,
          'params' => '{"title":"","nonloggedin":"1","loggedin":"1","nomobile":"0","notablet":"0","name":"sitemobile.sitemobile-headingtitle"}',
          'module' => 'sitemobile'
      ));

      // middle column content
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'core.content',
          'parent_content_id' => $middle_id,
          'order' => 1,
          'params' => '',
          'module' => 'sitemobile'
      ));

      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'parent_content_id' => $middle_id,
          'order' => 2,
          'params' => '',
          'module' => 'sitemobile'
      ));
    }
  }

  //Message Page
  public function addMessageInboxPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page = 'messages_messages_inbox';
    $displayname = 'Messages Inbox Page';
    $title = 'Inbox';
    $description = '';

    // check page
    $page_id = $this->getPageId($page);
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => $page,
          'displayname' => $displayname,
          'title' => $title,
          'description' => $description,
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));

      // Extra
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addMessageOutboxPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page = 'messages_messages_outbox';
    $displayname = 'Messages Outbox Page';
    $title = 'Inbox';
    $description = '';

    // check page
    $page_id = $this->getPageId($page);
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => $page,
          'displayname' => $displayname,
          'title' => $title,
          'description' => $description,
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));

      // Extra
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addMessageViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page = 'messages_messages_view';
    $displayname = 'Messages View Page';
    $title = 'My Message';
    $description = '';

    // check page
    $page_id = $db->select()
            ->from($this->_pagesTable, 'page_id')
            ->where('name = ?', $page)
            ->limit(1)
            ->query()
            ->fetchColumn();

    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => $page,
          'displayname' => $displayname,
          'title' => $title,
          'description' => $description,
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));

      // Extra
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
    }
  }

  protected function addMessageSearchPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page = 'messages_messages_search';
    $displayname = 'Messages Search Page';
    $title = 'Search';
    $description = '';

    // check page
    $page_id = $db->select()
            ->from($this->_pagesTable, 'page_id')
            ->where('name = ?', $page)
            ->limit(1)
            ->query()
            ->fetchColumn();

    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => $page,
          'displayname' => $displayname,
          'title' => $title,
          'description' => $description,
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));

      // Extra
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addMessageComposePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page = 'messages_messages_compose';
    $displayname = 'Messages Compose Page';
    $title = 'Compose';
    $description = '';

    // check page
    $page_id = $db->select()
            ->from($this->_pagesTable, 'page_id')
            ->where('name = ?', $page)
            ->limit(1)
            ->query()
            ->fetchColumn();

    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => $page,
          'displayname' => $displayname,
          'title' => $title,
          'description' => $description,
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
//WE WILL NOT ADD THE NAVIGATION TAB ON APP AND TABLET APP.
      if ($this->_pagesTable == 'engine4_sitemobile_pages' || $this->_pagesTable == 'engine4_sitemobile_tablet_pages') {
        // Extra
        $db->insert($this->_contentTable, array(
            'type' => 'widget',
            'name' => 'sitemobile.sitemobile-navigation',
            'page_id' => $page_id,
            'parent_content_id' => $middle_id,
            'order' => 1,
            'module' => 'sitemobile'
        ));
      }
    }
  }

  //Privacy Page 
  public function addPrivacyPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('core_help_privacy');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'core_help_privacy',
          'displayname' => 'Privacy Page',
          'title' => 'Privacy Policy',
          'description' => 'This is the privacy policy page',
          'provides' => 'no-viewer;no-subject',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
    }

    return $this;
  }

  //Terms of service page
  public function addTermsOfServicePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('core_help_terms');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'core_help_terms',
          'displayname' => 'Terms of Service Page',
          'title' => 'Terms of Service',
          'description' => 'This is the terms of service page',
          'provides' => 'no-viewer;no-subject',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
    }

    return $this;
  }

  //Contact page
  public function addContactPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page

    $page_id = $this->getPageId('core_help_contact');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'core_help_contact',
          'displayname' => 'Contact Page',
          'title' => 'Contact Us',
          'description' => 'This is the contact page',
          'provides' => 'no-viewer;no-subject',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
    }
    return $this;
  }

  //Startup Page
  public function addStartupPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page

    $page_id = $this->getPageId('sitemobile_browse_startup');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitemobile_browse_startup',
          'displayname' => 'Startup Page',
          'title' => 'Startup Page',
          'description' => 'This is the Startup page',
          'provides' => 'no-viewer;no-subject',
          'layout' => 'default-simple',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.startup',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
    }
    return $this;
  }

  //Dashboard page
  public function addDashboardPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page

    $page_id = $this->getPageId('sitemobile_browse_browse');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitemobile_browse_browse',
          'displayname' => 'Dashboard Page',
          'title' => 'Dashboard',
          'description' => 'This is the Dashboard Page',
          'provides' => 'no-viewer;no-subject',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
    }
    return $this;
  }

  //Member page
  public function addMemberBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page

    $page_id = $this->getPageId('user_index_browse');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'user_index_browse',
          'displayname' => 'Member Browse Page',
          'title' => 'Member Browse',
          'description' => 'This is the Member Page',
          'provides' => 'no-viewer;no-subject',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 3,
          'module' => 'sitemobile'
      ));
    }
    return $this;
  }

  //Member page
  public function addFriendsPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page

    $page_id = $this->getPageId('user_index_friends');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'user_index_friends',
          'displayname' => 'Friends Page',
          'title' => 'Friends',
          'description' => 'This is the Friends Page',
          'provides' => 'no-subject',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 3,
          'module' => 'sitemobile'
      ));
    }
    return $this;
  }

  //Notification & Request page
  public function addNotificationPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page

    $page_id = $this->getPageId('activity_notifications_index');
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'activity_notifications_index',
          'displayname' => 'Notification Page',
          'title' => 'Notification',
          'description' => 'This is the Notification Page',
          'provides' => 'no-viewer;no-subject',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
    }
    return $this;
  }

  //Setting pages
  public function addGeneral() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page

    $page_id = $this->getPageId('user_settings_general');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'user_settings_general',
          'displayname' => 'User General Settings Page',
          'title' => 'General',
          'description' => 'This page is the user general settings page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addPrivacy() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page

    $page_id = $this->getPageId('user_settings_privacy');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'user_settings_privacy',
          'displayname' => 'User Privacy Settings Page',
          'title' => 'Privacy',
          'description' => 'This page is the user privacy settings page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addNetworks() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page

    $page_id = $this->getPageId('user_settings_network');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'user_settings_network',
          'displayname' => 'User Networks Settings Page',
          'title' => 'Networks',
          'description' => 'This page is the user networks settings page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addNotifications() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page

    $page_id = $this->getPageId('user_settings_notifications');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'user_settings_notifications',
          'displayname' => 'User Notifications Settings Page',
          'title' => 'Notifications',
          'description' => 'This page is the user notification settings page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addChangePassword() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $this->getPageId('user_settings_password');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'user_settings_password',
          'displayname' => 'User Change Password Settings Page',
          'title' => 'Change Password',
          'description' => 'This page is the change password page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addDeleteAccount() {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page

    $page_id = $this->getPageId('user_settings_delete');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'user_settings_delete',
          'displayname' => 'User Delete Account Settings Page',
          'title' => 'Delete Account',
          'description' => 'This page is the delete accout page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  //page music
  public function addMusicUserProfileContent() {

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitemobile.music-profile-music')
    ;

    $info = $select->query()->fetch();

    if (empty($info)) {
      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
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
              ->where('page_id = ?', $page_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.music-profile-music',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 16,
          'params' => '{"title":"Music","titleCount":true}',
          'module' => 'music'
      ));

      return $this;
    }
  }

  public function addMusicViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('music_playlist_view');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'music_playlist_view',
          'displayname' => 'Music Playlist View Page',
          'title' => 'View Music',
          'description' => 'This page displays a music entry.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));

      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 3,
      ));
    }
  }

  //Music pages
  public function addMusicManagePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('music_index_manage');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'music_index_manage',
          'displayname' => 'Music Manage Page',
          'title' => 'My Music',
          'description' => 'This page lists a user\'s music entries.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));


      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addMusicBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('music_index_browse');

    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'music_index_browse',
          'displayname' => 'Music Browse Page',
          'title' => 'Browse Music',
          'description' => 'This page displays music entries.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 3,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addMusicCreatePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('music_index_create');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'music_index_create',
          'displayname' => 'Music Create Page',
          'title' => 'Create new Music',
          'description' => 'This page is the music create page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
      ));
    }
  }

  //Poll Pages
  public function addPollUserProfileContent() {

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitemobile.poll-profile-polls')
    ;

    $info = $select->query()->fetch();

    if (empty($info)) {
      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
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
              ->where('page_id = ?', $page_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.poll-profile-polls',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 17,
          'params' => '{"title":"Poll","titleCount":true}',
          'module' => 'poll'
      ));

      return $this;
    }
  }

  public function addPollViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('poll_poll_view');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'poll_poll_view',
          'displayname' => 'Poll View Page',
          'title' => 'View Poll',
          'description' => 'This page displays a poll entry.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 3,
      ));
    }
  }

  //poll
  public function addPollBrowsePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('poll_index_browse');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'poll_index_browse',
          'displayname' => 'Poll Browse Page',
          'title' => 'Browse Poll',
          'description' => 'This page displays poll entries.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 3,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addPollCreatePage() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('poll_index_create');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'poll_index_create',
          'displayname' => 'Poll Create Page',
          'title' => 'Create new Poll',
          'description' => 'This page is the poll create page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
          'module' => 'sitemobile'
      ));
    }
  }

  public function addPollManagePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('poll_index_manage');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'poll_index_manage',
          'displayname' => 'Poll Manage Page',
          'title' => 'My Polls',
          'description' => 'This page lists a user\'s poll entries.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => 1,
      ));
      $main_id = $db->lastInsertId();

      // Insert main-middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $main_middle_id = $db->lastInsertId();

      // Insert menu
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
          'module' => 'sitemobile'
      ));

      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }
  }

  //Forum pages
  protected function addForumIndexPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('forum_index_index');

    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'forum_index_index',
          'displayname' => 'Forum Main Page',
          'title' => 'Forum Main',
          'description' => 'This is the main forum page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $middle_id = $db->lastInsertId();

      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 3,
          'module' => 'sitemobile'
      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
          'order' => 4,
          'module' => 'sitemobile'
      ));
    }
  }

  protected function addForumViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = $this->getPageId('forum_forum_view');

    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'forum_forum_view',
          'displayname' => 'Forum View Page',
          'title' => 'Forum View',
          'description' => 'This is the view forum page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_id,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $middle_id,
      ));
    }
  }

  public function addForumUserProfileContent() {

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
            ->from($this->_pagesTable)
            ->where('name = ?', 'user_profile_index')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitemobile.profile-forum-posts')
    ;

    $info = $select->query()->fetch();

    if (empty($info)) {
      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from($this->_contentTable)
              ->where('page_id = ?', $page_id)
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
              ->where('page_id = ?', $page_id)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitemobile.profile-forum-posts',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 15,
          'params' => '{"title":"Forum Posts","titleCount":true}',
          'module' => 'poll'
      ));

      return $this;
    }
  }

}
