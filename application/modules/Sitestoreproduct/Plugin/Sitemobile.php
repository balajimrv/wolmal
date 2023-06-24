<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Plugin_Sitemobile extends Zend_Controller_Plugin_Abstract {

  protected $_pagesTable;
  protected $_contentTable;

  public function onIntegrated($pageTable, $contentTable) {
    $this->_pagesTable = $pageTable;
    $this->_contentTable = $contentTable;

      $this->addBrowseReviewPage();
      $this->addCategroiesPage();
      $this->addBrowseWishlistPage();
      $this->addWishlistProfilePage();
      $this->addDiscussionTopicViewPage();
      $this->addEditorProfilePage();
      $this->addVideoViewPage();
      $this->addmemberProfilePageWidgets();
      $this->addstoreProfilePageWidgets();
      $this->addReviewProfilePage();
      

      $this->addMyWishlistPage();
      $this->addMyOrderPage();
      $this->addStoreLikePage();
      $this->addMyAddressPage();
      $this->addOrderViewPage();
      

      $this->addManageCartPage();
      $this->addStoreCheckoutPage();

      $productTypeApi = Engine_Api::_()->getApi('productTypeSM', 'sitestoreproduct');
      $productTypeApi->defaultCreation($pageTable, $contentTable);


  } 
    
    public function addMyWishlistPage() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreproduct_wishlist_my-wishlists');
        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert($this->_pagesTable, array(
                'name' => 'sitestoreproduct_wishlist_my-wishlists',
                'displayname' => 'Stores - My Wishlists',
                'title' => 'Stores - My Wishlists',
                'description' => 'This page displays wishlists of user.',
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

            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitemobile.sitemobile-navigation',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));
        }
    }
    
    public function addMyOrderPage() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreproduct_product_my-order');
        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert($this->_pagesTable, array(
                'name' => 'sitestoreproduct_product_my-order',
                'displayname' => 'Stores - My Orders',
                'title' => 'Stores - My Orders',
                'description' => 'This page displays orders of user.',
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

            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitemobile.sitemobile-navigation',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));
        }
    }
    
    public function addStoreLikePage() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestore_like_mylikes');
        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert($this->_pagesTable, array(
                'name' => 'sitestore_like_mylikes',
                'displayname' => 'Stores - Stores I Like',
                'title' => 'Stores - Stores I Like',
                'description' => 'This page displays stores which user likes.',
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

            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitemobile.sitemobile-navigation',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));
        }
    }
    
    public function addMyAddressPage() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreproduct_index_manage-address');
        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert($this->_pagesTable, array(
                'name' => 'sitestoreproduct_index_manage-address',
                'displayname' => 'Stores - My Addresses',
                'title' => 'Stores - My Addresses',
                'description' => 'This page displays Addresses of user.',
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

            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitemobile.sitemobile-navigation',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));
        }
    }
    
    
        public function addOrderViewPage() {
        $db = Engine_Db_Table::getDefaultAdapter();

        $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreproduct_index_order-view');
        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert($this->_pagesTable, array(
                'name' => 'sitestoreproduct_index_order-view',
                'displayname' => 'Stores - View Order',
                'title' => 'Stores - View Order',
                'description' => 'This page displays Orders of user.',
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

            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitemobile.sitemobile-navigation',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            // Insert content
            $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));
        }
    }
    
  protected function addBrowseReviewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreproduct_review_browse');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestoreproduct_review_browse',
          'displayname' => 'Stores - Browse Reviews',
          'title' => 'Browse Reviews',
          'description' => 'This is the review browse page.',
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

      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-navigation',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
     // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
      ));
    }
  }

  protected function addCategroiesPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreproduct_index_categories');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestoreproduct_index_categories',
          'displayname' => 'Stores - Categories Home',
          'title' => 'Categories Home',
          'description' => 'This is the categories home page.',
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

        $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitemobile.sitemobile-navigation',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitestoreproduct.categories-home',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
          'params' => '{"producttype_id":"-1"}',
      ));
    }
  }

  protected function addBrowseWishlistPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreproduct_wishlist_browse');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestoreproduct_wishlist_browse',
          'displayname' => 'Stores - Browse Wishlists',
          'title' => 'Browse Wishlists',
          'description' => 'This is the wishlist browse page.',
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

      $db->insert($this->_contentTable, array(
                'type' => 'widget',
                'name' => 'sitemobile.sitemobile-navigation',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
//      // Insert Advance search
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitemobile.sitemobile-advancedsearch',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
          'order' => 2,
      ));
      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitestoreproduct.wishlist-browse',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
          'params' => '{"statisticsWishlist":["entryCount","likeCount","productCount","viewCount","followCount"],"itemCount":"10"}',
      ));
    }
  }
  
  
  protected function addWishlistProfilePage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreproduct_wishlist_profile');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestoreproduct_wishlist_profile',
          'displayname' => 'Stores - Wishlist Profile',
          'title' => 'Wishlist Profile',
          'description' => 'This is the wishlist profile page.',
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

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitestoreproduct.wishlist-profile-items',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 3,
          'params' => '{"followLike":["follow","like"],"shareOptions":["siteShare","friend","report"],"statistics":["likeCount","reviewCount"],"statisticsWishlist":["entryCount","likeCount","viewCount","followCount"]}',
      ));
    }
  }

  public function addDiscussionTopicViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreproduct_topic_view');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestoreproduct_topic_view',
          'displayname' => 'Stores - Topic Discussion View Page',
          'title' => 'Products Discussion Topic View Page',
          'description' => 'This is the review topic view page.',
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

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'core.content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
    }
  }
  
  public function addEditorProfilePage() {
    $db = Engine_Db_Table::getDefaultAdapter();
		//EDITOR PROFILE PAGE
		$page_id = $db->select()
						->from($this->_pagesTable, 'page_id')
						->where('name = ?', "sitestoreproduct_editor_profile")
						->limit(1)
						->query()
						->fetchColumn();

		if (!$page_id) {

			$containerCount = 0;
			$widgetCount = 0;

			$db->insert($this->_pagesTable, array(
					'name' => "sitestoreproduct_editor_profile",
					'displayname' => 'Stores - Editor Profile',
					'title' => 'Editor Profile',
					'description' => 'This is the editor profile page.',
					'custom' => 0,
			));
			$page_id = $db->lastInsertId();

			//MAIN CONTAINER
			$db->insert($this->_contentTable, array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'order' => $containerCount++,
					'params' => '',
			));
			$main_container_id = $db->lastInsertId();

			//MIDDLE CONTAINER  
			$db->insert($this->_contentTable, array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $main_container_id,
					'order' => $containerCount++,
					'params' => '',
			));
			$main_middle_id = $db->lastInsertId();

			$db->insert($this->_contentTable, array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitestoreproduct.editor-photo-sitestoreproduct',
					'parent_content_id' => $main_middle_id,
					'order' => $widgetCount++,
					'params' => '',
			));

			$db->insert($this->_contentTable, array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitemobile.container-tabs-columns',
					'parent_content_id' => $main_middle_id,
					'order' => $widgetCount++,
					'params' => '{"layoutContainer":"tab","title":""}',
			));
			$tab_id = $db->lastInsertId();

			$db->insert($this->_contentTable, array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitestoreproduct.editor-profile-reviews-sitestoreproduct',
					'parent_content_id' => $tab_id,
					'order' => $widgetCount++,
					'params' => '{"title":"Reviews As Editor","type":"editor"}',
			));

			$db->insert($this->_contentTable, array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitestoreproduct.editor-profile-reviews-sitestoreproduct',
					'parent_content_id' => $tab_id,
					'order' => $widgetCount++,
					'params' => '{"title":"Reviews As User","type":"user", "onlyProducttypeEditorProducts":"0"}',
			));

			$db->insert($this->_contentTable, array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitestoreproduct.editor-replies-sitestoreproduct',
					'parent_content_id' => $tab_id,
					'order' => $widgetCount++,
					'params' => '{"title":"Comments", "onlyProducttypeEditor":"0"}',
			));

			$db->insert($this->_contentTable, array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitestoreproduct.editors-sitestoreproduct',
					'parent_content_id' => $tab_id,
					'order' => $widgetCount++,
					'params' => '{"title":"Similar Editors","nomobile":"1"}',
			));
		}
  }

  public function addVideoViewPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreproduct_video_view');
    // insert if it doesn't exist yet
    if (!$page_id) {
      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestoreproduct_video_view',
          'displayname' => 'Stores - Video View Page',
          'title' => 'Products Video View Page',
          'description' => 'This is the review video view page.',
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

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitestoreproduct.video-content',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 1,
      ));
    }
  }
  
  //Create Review Profile Page
  
  public function addReviewProfilePage(){
    //REVIEW PROFILE PAGE
    $pageTable = $this->_pagesTable;
    $contentTable = $this->_contentTable;
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
$page_id = $db->select()
        ->from($pageTable, 'page_id')
        ->where('name = ?', "sitestoreproduct_review_view")
        ->limit(1)
        ->query()
        ->fetchColumn();

    //CREATE PAGE IF NOT EXIST
    if (!$page_id) {

      $containerCount = 0;
      $widgetCount = 0;

      $db->insert($pageTable, array(
          'name' => "sitestoreproduct_review_view",
          'displayname' => 'Stores - Review Profile',
          'title' => 'Review Profile',
          'description' => 'This is the review profile page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      //TOP CONTAINER
      $db->insert($contentTable, array(
          'type' => 'container',
          'name' => 'top',
          'page_id' => $page_id,
          'order' => $containerCount++,
      ));
      $top_container_id = $db->lastInsertId();

      //MAIN CONTAINER
      $db->insert($contentTable, array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => $containerCount++,
      ));
      $main_container_id = $db->lastInsertId();

      //INSERT TOP-MIDDLE
      $db->insert($contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $top_container_id,
          'order' => $containerCount++,
      ));
      $top_middle_id = $db->lastInsertId();


      //MAIN-MIDDLE CONTAINER
      $db->insert($contentTable, array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
      ));
      $main_middle_id = $db->lastInsertId();

 
      $db->insert($contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.profile-review-breadcrumb-sitestoreproduct',
          'parent_content_id' => $top_middle_id,
          'order' => $widgetCount++,
          'params' => '{"nomobile":"1"}',
      ));


      $db->insert($contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.profile-review-sitestoreproduct',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"title":"","titleCount":"true","name":"sitestoreproduct.profile-review-sitestoreproduct"}',
      )); 
    }
    
  }
  
   //MEMBER PROFILE PAGE WIDGETS
  public function addstoreProfilePageWidgets() {
    $pageTable = $this->_pagesTable;
    $contentTable = $this->_contentTable;
    //GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    //MEMBER PROFILE PAGE WIDGETS
    $page_id = $db->select()
            ->from($pageTable, array('page_id'))
            ->where('name =?', 'sitestore_index_view')
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (!empty($page_id)) {

      $tab_id = $db->select()
              ->from($contentTable, array('content_id'))
              ->where('page_id =?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.container-tabs-columns')
              ->limit(1)
              ->query()
              ->fetchColumn();

      if (!empty($tab_id)) {

        $content_id = $db->select()
                ->from($contentTable, array('content_id'))
                ->where('page_id =?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'sitestoreproduct.store-profile-products')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (empty($content_id)) {
           $db->insert($this->_contentTable, array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.store-profile-products',
          'parent_content_id' => $tab_id,
          'order' => 90,
          'params' => '{"title":"Products","titleCount":"true","columnHeight":"225","columnWidth":"165","category_id":"0","postedby":"0","truncationGrid":"32","statistics":[],"name":"sitestoreproduct.store-profile-products"}',
      ));        
        }
      }
    }
  }
  
   //MEMBER PROFILE PAGE WIDGETS
  public function addmemberProfilePageWidgets() {
    $pageTable = $this->_pagesTable;
    $contentTable = $this->_contentTable;
    //GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    //MEMBER PROFILE PAGE WIDGETS
    $page_id = $db->select()
            ->from($pageTable, array('page_id'))
            ->where('name =?', 'user_profile_index')
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (!empty($page_id)) {

      $tab_id = $db->select()
              ->from($contentTable, array('content_id'))
              ->where('page_id =?', $page_id)
              ->where('type = ?', 'widget')
              ->where('name = ?', 'sitemobile.container-tabs-columns')
              ->limit(1)
              ->query()
              ->fetchColumn();

      if (!empty($tab_id)) {

        $content_id = $db->select()
                ->from($contentTable, array('content_id'))
                ->where('page_id =?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'sitestoreproduct.profile-sitestoreproduct')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (empty($content_id)) {
          $db->insert($contentTable, array(
              'page_id' => $page_id,
              'type' => 'widget',
              'name' => 'sitestoreproduct.profile-sitestoreproduct',
              'parent_content_id' => $tab_id,
              'order' => 999,
              'params' => '{"title":"Products","titleCount":"true","statistics":[]}',
          ));
        }
//::- NOT ADD Widget on USER Profile Page at installation
//        $content_id = $db->select()
//                ->from($contentTable, array('content_id'))
//                ->where('page_id =?', $page_id)
//                ->where('type = ?', 'widget')
//                ->where('name = ?', 'sitestoreproduct.editor-profile-reviews-sitestoreproduct')
//                ->limit(1)
//                ->query()
//                ->fetchColumn();
//
//        if (empty($content_id)) {
//
//          $db->insert($contentTable, array(
//              'page_id' => $page_id,
//              'type' => 'widget',
//              'name' => 'sitestoreproduct.editor-profile-reviews-sitestoreproduct',
//              'parent_content_id' => $tab_id,
//              'order' => 999,
//              'params' => '{"title":"Reviews As Editor","type":"editor"}',
//          ));
//
//          $db->insert($contentTable, array(
//              'page_id' => $page_id,
//              'type' => 'widget',
//              'name' => 'sitestoreproduct.editor-profile-reviews-sitestoreproduct',
//              'parent_content_id' => $tab_id,
//              'order' => 999,
//              'params' => '{"title":"Reviews As User","type":"user", "onlyProducttypeEditorProducts":"1"}',
//          ));
//        }
      }
    }
  }
  private function addManageCartPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreproduct_product_cart');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestoreproduct_product_cart',
          'displayname' => 'Stores - Manage Cart',
          'title' => 'Stores - Manage Cart',
          'description' => 'This is the store manage cart page.',
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

      // Insert content
      $db->insert($this->_contentTable, array(
          'type' => 'widget',
          'name' => 'sitestoreproduct.manage-cart',
          'page_id' => $page_id,
          'parent_content_id' => $main_middle_id,
          'order' => 2,
      ));
    }
  }

  private function addStoreCheckoutPage() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $page_id = Engine_Api::_()->getApi('modules', 'sitemobile')->getPageId('sitestoreproduct_index_checkout');
    if (!$page_id) {

      // Insert page
      $db->insert($this->_pagesTable, array(
          'name' => 'sitestoreproduct_index_checkout',
          'displayname' => 'Stores - Checkout',
          'title' => 'Stores - Checkout',
          'description' => 'This is the store manage cart page.',
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

      // Insert content
//      $db->insert($this->_contentTable, array(
//          'type' => 'widget',
//          'name' => 'sitestoreproduct.checkout-process',
//          'page_id' => $page_id,
//          'parent_content_id' => $main_middle_id,
//          'order' => 1,
//      ));
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
}