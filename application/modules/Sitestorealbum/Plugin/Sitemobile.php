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
class Sitestorealbum_Plugin_Sitemobile {
  
  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    
    $this->_pagesTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
    //Store Album
    $this->addSitestoreAlbumProfileContent();
    $this->addSitestoreAlbumCreateStore();
    $this->addSitestoreAlbumBrowseStore();
    $this->addSitestoreAlbumViewStore();
    $this->addSitestoreAlbumPhotoViewStore();
  }
  
  //Site store Albums 
  public function addSitestoreAlbumProfileContent() {
    // install content areas
    $db = Engine_Db_Table::getDefaultAdapter();

    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_index_view');


    if(empty($store_id)) 
      return false;

    // album.profile-albums
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $store_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitestore.sitemobile-photos-sitestore')
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
          'name' => 'sitestore.sitemobile-photos-sitestore',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 500,
          'params' => '{"title":"Photos","titleCount":true}',
      ));

      return $this;
    }
  }

  public function addSitestoreAlbumCreateStore() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_photo_upload-album');
    if (!$store_id) {

      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestore_photo_upload-album',
          'displayname' => 'Stores - Album Create Store',
          'title' => 'Create new Stores / Marketplace Album',
          'description' => 'This store is the Stores / Marketplace Album create store.',
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

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
      ));
    }
  }

  public function addSitestoreAlbumBrowseStore() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_album_browse');
    // insert if it doesn't exist yet
    if (!$store_id) {
      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestore_album_browse',
          'displayname' => 'Stores - Album Browse Store',
          'title' => 'Browse Stores / Marketplace Album',
          'description' => 'This store lists stores / marketplace album entries.',
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
      // Insert Advance search
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
          'name' => 'sitestorealbum.sitestore-album',
          'page_id' => $store_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"itemCount":"10"}',
          'order' => 3,
      ));
    }
  }

  public function addSitestoreAlbumViewStore() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_album_view');
    // insert if it doesn't exist yet
    if (!$store_id) {
      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestore_album_view',
          'displayname' => 'Stores - Album View Store',
          'title' => 'View Stores / Marketplace Album',
          'description' => 'This store displays stores / marketplace album entries.',
          'custom' => 0,
      ));
      $store_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $store_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $store_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

//      $db->insert($this->_contentTable, array(
//          'page_id' => $store_id,
//          'type' => 'widget',
//          'name' => 'sitemobile.sitemobile-headingtitle',
//          'parent_content_id' => $middle_id,
//          'order' => 1,
//          'params' => '{"title":"","nonloggedin":"1","loggedin":"1","nomobile":"0","notablet":"0","name":"sitemobile.sitemobile-headingtitle"}',
//          'module' => 'sitemobile'
//      ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitestorealbum.album-content',
          'page_id' => $store_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $store_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
      ));
    }
  }

  public function addSitestoreAlbumPhotoViewStore() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_photo_view');

    // profile store
    if (!$store_id) {
      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestore_photo_view',
          'displayname' => 'Stores - Album Photo View Store',
          'title' => 'Stores / Marketplace Album Photo View',
          'description' => 'This store displays an stores / marketplace album\'s photo.',
          'provides' => 'subject=storealbum_photo',
          'custom' => 0,
      ));
      $store_id = $db->lastInsertId();

      // Insert main
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $store_id,
      ));
      $main_id = $db->lastInsertId();

      // Insert middle
      $db->insert($this->_contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $store_id,
          'parent_content_id' => $main_id,
          'order' => 2,
      ));
      $middle_id = $db->lastInsertId();

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $store_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $store_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
      ));
    }

    return $this;
  }
  
 }