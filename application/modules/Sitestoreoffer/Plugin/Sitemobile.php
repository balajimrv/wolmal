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
class Sitestoreoffer_Plugin_Sitemobile {
  
  protected $_pagesTable;
  protected $_contentTable;
  
  public function onIntegrated() {
    
    $this->_pagesTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_pagesTable;
    $this->_contentTable =  Engine_Api::_()->getApi('modules', 'sitemobile')->_contentTable;
   //Store offers
    $this->addSitestoreOffersProfileContent();
    $this->addSitestoreOffersViewStore();
    $this->addSitestoreOffersBrowseStore();
    $this->addSitestoreOffersCreateStore();
  }
  
  //Offers view store

  public function addSitestoreOffersProfileContent() {


    // install content areas

    $db = Engine_Db_Table::getDefaultAdapter();
    $select = new Zend_Db_Select($db);

    // profile store
    $store_id =  Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_index_view');

    // sitemobile.blog-profile-blogs
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from($this->_contentTable)
            ->where('page_id = ?', $store_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitestoreoffer.sitemobile-profile-sitestoreoffers')
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
          'name' => 'sitestoreoffer.sitemobile-profile-sitestoreoffers',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 1400,
          'params' => '{"title":"Offers","titleCount":true}',
      ));
    }
  }

  public function addSitestoreOffersViewStore() {
    $db = Engine_Db_Table::getDefaultAdapter();


    $store_id =  Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreoffer_index_view');
    // insert if it doesn't exist yet
    if (!$store_id) {
      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestoreoffer_index_view',
          'displayname' => 'Stores - Offers View ',
          'title' => 'View Stores / Marketplace Offer',
          'description' => 'This store displays a stores / marketplace Offer entry.',
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
          'name' => 'sitestoreoffer.offer-content',
          'page_id' => $store_id,
          'parent_content_id' => $middle_id,
          'order' => 2,
      ));
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.comments',
          'page_id' => $store_id,
          'parent_content_id' => $middle_id,
          'order' => 3,
      ));
    }
  }

  //Offers view store
  public function addSitestoreOffersBrowseStore() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreoffer_index_browse');
    // insert if it doesn't exist yet
    if (!$store_id) {
      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestoreoffer_index_browse',
          'displayname' => 'Stores - Browse Offers',
          'title' => 'Browse Stores / Marketplace Offers',
          'description' => 'This store displays  stores / marketplace Offers entries.',
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

      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $store_id,
          'parent_content_id' => $middle_id,
          'order' => 1,
      ));
//      
      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $store_id,
          'parent_content_id' => $middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
          'module' => 'sitemobile'
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitestoreoffer.hot-offers-slideshow',
          'page_id' => $store_id,
          'parent_content_id' => $middle_id,
          'params' => '{"itemCount":"10"}',
          'order' => 3,
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitestoreoffer.sitestore-offer',
          'page_id' => $store_id,
          'parent_content_id' => $middle_id,
          'params' => '{"itemCount":"10"}',
          'order' => 4,
      ));     
    }
  }

  public function addSitestoreOffersCreateStore() {

    $db = Engine_Db_Table::getDefaultAdapter();

    $store_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreoffer_index_create');
    if (!$store_id) {

      // Insert store
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestoreoffer_index_create',
          'displayname' => 'Stores - Offers Create',
          'title' => 'Create new Stores / Marketplace Offers',
          'description' => 'This store is the stores / marketplace offers create store.',
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

}