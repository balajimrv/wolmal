<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ProductType.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitestoreproduct_Api_ProductType extends Core_Api_Abstract {

  public function defaultCreation() {

    $productTypeApi = Engine_Api::_()->getApi('productType', 'sitestoreproduct');
    $productTypeApi->homePageCreate();
    $productTypeApi->browsePageCreate();
    $productTypeApi->profilePageCreate();
    $productTypeApi->mainNavigationCreate();
    $productTypeApi->gutterNavigationCreate();
    $productTypeApi->addBannedUrls();

    //START: DEFAULT CREATE WIDGETIZED PAGE FOR CATEGORY
    $categoryIds = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategoriesArray(array('cat_dependency' => 0, 'subcat_dependency' => 0));
    $productTypeApi->categoriesPageCreate($categoryIds);   
  }

  public function categoriesPageCreate($categoryIds = array()) {

    //GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    foreach ($categoryIds as $categoryId) {

      $category = Engine_Api::_()->getItem('sitestoreproduct_category', $categoryId);

      if ($category->cat_dependency || $category->subcat_dependency || empty($category)) {
        continue;
      }

      $categoryName = $category->getTitle(true);

      $page_id = $db->select()
              ->from('engine4_core_pages', 'page_id')
              ->where('name = ?', "sitestoreproduct_index_category-home_category_" . $categoryId)
              ->limit(1)
              ->query()
              ->fetchColumn();

      if (empty($page_id)) {

        $containerCount = 0;
        $widgetCount = 0;

        //CREATE PAGE
        $db->insert('engine4_core_pages', array(
            'name' => "sitestoreproduct_index_category-home_category_" . $categoryId,
            'displayname' => "Stores - " . $categoryName . " Home",
            'title' => "Stores - " . $categoryName . " Home",
            'description' => 'This is the Stores / Marketplace - ' . $categoryName . ' home page.',
            'custom' => 0,
        ));
        $page_id = $db->lastInsertId();

        //TOP CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'top',
            'page_id' => $page_id,
            'order' => $containerCount++,
        ));
        $top_container_id = $db->lastInsertId();

        //MAIN CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'main',
            'page_id' => $page_id,
            'order' => $containerCount++,
        ));
        $main_container_id = $db->lastInsertId();

        //INSERT TOP-MIDDLE
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $top_container_id,
            'order' => $containerCount++,
        ));
        $top_middle_id = $db->lastInsertId();

        //LEFT CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'left',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => $containerCount++,
        ));
        $left_container_id = $db->lastInsertId();

        //MAIN-MIDDLE CONTAINER
        $db->insert('engine4_core_content', array(
            'type' => 'container',
            'name' => 'middle',
            'page_id' => $page_id,
            'parent_content_id' => $main_container_id,
            'order' => $containerCount++,
        ));
        $main_middle_id = $db->lastInsertId();

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.navigation-sitestoreproduct',
            'parent_content_id' => $top_middle_id,
            'order' => $widgetCount++,
            'params' => '',
        ));
        
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.categories-home-breadcrumb',
            'parent_content_id' => $top_middle_id,
            'order' => $widgetCount++,
            'params' => '',
        ));        

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.producttypes-categories',
            'parent_content_id' => $left_container_id,
            'order' => $widgetCount++,
            'params' => '{"viewDisplayHR":"0","title":"Categories","nomobile":"0","name":"sitestoreproduct.producttypes-categories"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.tagcloud-sitestoreproduct',
            'parent_content_id' => $left_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"Popular Brands (%s)","titleCount":true,"itemCount":"25","nomobile":"0","name":"sitestoreproduct.tagcloud-sitestoreproduct"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.search-sitestoreproduct',
            'parent_content_id' => $left_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"Browse"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.sponsored-sitestoreproduct',
            'parent_content_id' => $left_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"Most Viewed Products","titleCount":"true","showOptions":["category","rating","review","compare","wishlist"],"ratingType":"rating_avg","fea_spo":"","viewType":"1","blockHeight":"305","blockWidth":"190","category_id":"' . $categoryId . '","subcategory_id":"0","hidden_category_id":"' . $categoryId . '","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","itemCount":"3","popularity":"view_count","featuredIcon":"1","sponsoredIcon":"1","newIcon":"1","interval":"300","truncation":"50","nomobile":"1","defaultWidgetNo":10}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.sponsored-sitestoreproduct',
            'parent_content_id' => $left_container_id,
            'order' => $widgetCount++,
            'params' => '{"title":"Most Liked Products","titleCount":"true","showOptions":["category","rating","review","compare","wishlist"],"ratingType":"rating_avg","fea_spo":"","viewType":"1","blockHeight":"305","blockWidth":"190","category_id":"' . $categoryId . '","subcategory_id":"0","hidden_category_id":"' . $categoryId . '","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","itemCount":"3","popularity":"like_count","featuredIcon":"1","sponsoredIcon":"1","newIcon":"1","interval":"300","truncation":"50","nomobile":"1","defaultWidgetNo":11}',
        ));
        
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.category-name-sitestoreproduct',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '',
        ));        

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.categories-banner-sitestoreproduct',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"Featured Products","titleCount":"true","fea_spo":"featured","statistics":["viewCount","likeCount","commentCount","reviewCount"],"nomobile":"1"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.categories-grid-view',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"columnHeight":216,"columnWidth":234,"defaultWidgetNo":7}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.sponsored-sitestoreproduct',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"Most Rated Products","titleCount":"true","showOptions":["category","rating","review","compare","wishlist"],"ratingType":"rating_avg","fea_spo":"","viewType":"0","blockHeight":"305","blockWidth":"190","category_id":"' . $categoryId . '","subcategory_id":"0","hidden_category_id":"' . $categoryId . '","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","itemCount":"3","popularity":"rating_users","featuredIcon":"1","sponsoredIcon":"1","newIcon":"1","interval":"300","truncation":"50","nomobile":"1","defaultWidgetNo":9}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.recently-popular-random-sitestoreproduct',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"title":"","titleCount":"","statistics":["viewCount"],"layouts_views":["listZZZview","gridZZZview"],"ajaxTabs":["recent","mostZZZreviewed","mostZZZpopular","featured","sponsored","topZZZselling","newZZZarrivals"],"recent_order":"7","reviews_order":"5","popular_order":"1","featured_order":"3","sponsored_order":"4","top_selling_order":"2","new_arrival_order":"6","columnWidth":"165","add_to_cart":"1","in_stock":"1","ratingType":"rating_avg","category_id":"' . $categoryId . '","subcategory_id":"0","hidden_category_id":"' . $categoryId . '","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","defaultOrder":"gridZZZview","columnHeight":"305","postedby":"1","limit":"12","truncationList":"600","truncationGrid":"90","nomobile":"0","name":"sitestoreproduct.recently-popular-random-sitestoreproduct","defaultWidgetNo":8}',
        ));
      } else {
        $PagesTable = Engine_Api::_()->getDbTable('pages', 'core');
        $PagesTable->update(array(
            'displayname' => "Stores - " . $categoryName . " Home",
            'title' => "Stores - " . $categoryName . " Home",
            'description' => 'This is the Stores / Marketplace - ' . $categoryName . ' home page.'
                ), array(
            'name =?' => "sitestoreproduct_index_category-home_category_" . $categoryId,
        ));
      }
    }
  }

  //HOME PAGE WORK
  public function homePageCreate() {

    //GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    //GET PRODUCT DETAILS
    $titleSinUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'));
    $titlePluUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titleplural', 'Products'));
    $titleSinLc = strtolower(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'));
    $titlePluLc = strtolower(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titleplural', 'Products'));

    $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "sitestoreproduct_index_home")
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (empty($page_id)) {

      $containerCount = 0;
      $widgetCount = 0;

      //CREATE PAGE
      $db->insert('engine4_core_pages', array(
          'name' => "sitestoreproduct_index_home",
          'displayname' => 'Stores - ' . $titlePluUc . ' Home',
          'title' => 'Stores - ' . $titlePluUc . ' Home',
          'description' => 'This is the ' . $titleSinLc . ' home page.',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

      //TOP CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'top',
          'page_id' => $page_id,
          'order' => $containerCount++,
      ));
      $top_container_id = $db->lastInsertId();

      //MAIN CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => $containerCount++,
      ));
      $main_container_id = $db->lastInsertId();

      //INSERT TOP-MIDDLE
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $top_container_id,
          'order' => $containerCount++,
      ));
      $top_middle_id = $db->lastInsertId();

      //LEFT CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'left',
          'page_id' => $page_id,
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
      ));
      $left_container_id = $db->lastInsertId();

      //RIGHT CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'right',
          'page_id' => $page_id,
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
      ));
      $right_container_id = $db->lastInsertId();

      //MAIN-MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
      ));
      $main_middle_id = $db->lastInsertId();

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.navigation-sitestoreproduct',
          'parent_content_id' => $top_middle_id,
          'order' => $widgetCount++,
          'params' => '',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.sponsored-sitestoreproduct',
          'parent_content_id' => $top_middle_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Sponsored Products","titleCount":true,"showOptions":["category","rating","review","compare","wishlist"],"ratingType":"rating_both","fea_spo":"sponsored","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","add_to_cart":"1","in_stock":"1","viewType":"0","blockHeight":"305","blockWidth":"200","itemCount":"4","popularity":"product_id","featuredIcon":"1","sponsoredIcon":"1","newIcon":"1","interval":"300","truncation":"50","nomobile":"1","name":"sitestore.sponsored-sitestore","defaultWidgetNo":1}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.zeroproduct-sitestoreproduct',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.slideshow-sitestoreproduct',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Featured Products","titleCount":true,"statistics":["viewCount","likeCount","commentCount","reviewCount"],"category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","ratingType":"rating_avg","fea_spo":"featured","popularity":"product_id","interval":"overall","featuredIcon":"1","sponsoredIcon":"1","newIcon":"1","truncation":"45","count":"10","nomobile":"1","name":"sitestore.slideshow-sitestore"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.categories-middle-sitestoreproduct',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Categories","titleCount":true,"showAllCategories":"1","show2ndlevelCategory":"1","show3rdlevelCategory":"0","showCount":"0","nomobile":"1","name":"sitestore.categories-middle-sitestore"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.category-products-sitestoreproduct',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Popular Products","titleCount":"true","nomobile":"1"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.recently-popular-random-sitestoreproduct',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"title":"","titleCount":"","statistics":["viewCount","likeCount","commentCount","reviewCount"],"layouts_views":["listZZZview","gridZZZview"],"ajaxTabs":["recent","mostZZZreviewed","featured","sponsored","topZZZselling","newZZZarrivals"],"recent_order":"7","reviews_order":"2","popular_order":"6","featured_order":"4","sponsored_order":"5","top_selling_order":"3","new_arrival_order":"1","columnWidth":"158","add_to_cart":"1","in_stock":"1","ratingType":"rating_avg","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","defaultOrder":"gridZZZview","columnHeight":"300","postedby":"0","limit":"9","truncationList":"600","truncationGrid":"32","nomobile":"0","name":"sitestore.recently-popular-random-sitestore","defaultWidgetNo":2}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.store-startup-link',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.search-sitestoreproduct',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.sitestoreproduct-products',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Recently Ordered Products","titleCount":true,"statistics":["likeCount","reviewCount"],"viewType":"gridview","columnWidth":"180","popularity":"last_order_viewer","product_type":"all","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","ratingType":"rating_avg","columnHeight":"328","itemCount":"3","truncation":"16","nomobile":"0","name":"sitestoreproduct.sitestoreproduct-products"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.tagcloud-sitestoreproduct',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title": "Popular Brands (%s)","nomobile":"1"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.producttypes-categories',
          'parent_content_id' => $left_container_id,
          'order' => $widgetCount++,
          'params' => '{"viewDisplayHR":"0","title":"","nomobile":"0","name":"sitestoreproduct.producttypes-categories","nomobile":"1"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.item-sitestoreproduct',
          'parent_content_id' => $left_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"' . $titleSinUc . ' of the Day","titleCount":"true","nomobile":"1"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.products-sitestoreproduct',
          'parent_content_id' => $left_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Top Rated Products","titleCount":true,"statistics":["likeCount","reviewCount"],"viewType":"gridview","columnWidth":"180","add_to_cart":"1","in_stock":"1","ratingType":"rating_avg","fea_spo":"","columnHeight":"328","popularity":"rating_avg","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","itemCount":"3","truncation":"16","nomobile":"1","name":"sitestoreproduct.products-sitestoreproduct"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.recently-viewed-sitestoreproduct',
          'parent_content_id' => $left_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Recently Viewed By Friends","titleCount":true,"statistics":["likeCount","reviewCount"],"ratingType":"rating_avg","fea_spo":"","add_to_cart":"1","in_stock":"1","show":"1","viewType":"gridview","columnWidth":"180","columnHeight":"328","truncation":"32","count":"2","nomobile":"1","name":"sitestore.recently-viewed-sitestore"}',
      ));
    }
  }

//BROWSE PAGE WORK
  public function browsePageCreate() {

    //GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "sitestoreproduct_index_index")
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (!$page_id) {

      $containerCount = 0;
      $widgetCount = 0;

      $db->insert('engine4_core_pages', array(
          'name' => "sitestoreproduct_index_index",
          'displayname' => 'Stores - Browse Products',
          'title' => '',
          'description' => '',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId();

//TOP CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'top',
          'page_id' => $page_id,
          'order' => $containerCount++,
      ));
      $top_container_id = $db->lastInsertId();

//MAIN CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'main',
          'page_id' => $page_id,
          'order' => $containerCount++,
      ));
      $main_container_id = $db->lastInsertId();

//INSERT TOP-MIDDLE
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $top_container_id,
          'order' => $containerCount++,
      ));
      $top_middle_id = $db->lastInsertId();

//      //LEFT CONTAINER
//      $db->insert('engine4_core_content', array(
//          'type' => 'container',
//          'name' => 'left',
//          'page_id' => $page_id,
//          'parent_content_id' => $main_container_id,
//          'order' => $containerCount++,
//      ));
//      $left_container_id = $db->lastInsertId();

//RIGHT CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'right',
          'page_id' => $page_id,
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
      ));
      $right_container_id = $db->lastInsertId();

//MAIN-MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
      ));
      $main_middle_id = $db->lastInsertId();

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.navigation-sitestoreproduct',
          'parent_content_id' => $top_middle_id,
          'order' => $widgetCount++,
          'params' => '',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.browse-breadcrumb-sitestoreproduct',
          'parent_content_id' => $top_middle_id,
          'order' => $widgetCount++,
          'params' => '{"nomobile":"1"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.categories-sidebar-sitestoreproduct',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Categories","titleCount":"true","nomobile":"1"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.search-sitestoreproduct',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.tagcloud-sitestoreproduct',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title": "Popular Brands (%s)","nomobile":"1"}',
      ));
      
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.sitestoreproduct-products',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Top Selling Products","titleCount":true,"statistics":["likeCount","reviewCount"],"viewType":"gridview","columnWidth":"180","popularity":"top_selling","product_type":"all","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","ratingType":"rating_avg","columnHeight":"328","itemCount":"1","truncation":"32","nomobile":"0","name":"sitestore.sitestore-products"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.recently-viewed-sitestoreproduct',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Recently Viewed By You","titleCount":true,"statistics":["likeCount","reviewCount"],"ratingType":"rating_avg","fea_spo":"","add_to_cart":"1","in_stock":"1","show":"0","viewType":"gridview","columnWidth":"180","columnHeight":"328","truncation":"32","count":"1","nomobile":"1","name":"sitestore.recently-viewed-sitestore"}',
      ));   
      
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.searchbox-sitestoreproduct',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"title":"","titleCount":"","categoriesLevel":["category"],"formElements":["textElement","categoryElement"],"textWidth":"580","categoryWidth":"220","nomobile":"0","name":"sitestoreproduct.searchbox-sitestoreproduct"}',
      ));      
      
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.categories-banner-sitestoreproduct',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"nomobile":"1"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.browse-products-sitestoreproduct',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"title":"","titleCount":true,"layouts_views":["1","2"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","reviewCount"],"columnWidth":"165","truncationGrid":"32","ratingType":"rating_both","columnHeight":"315","bottomLine":"1","postedby":"0","add_to_cart":"1","in_stock":"1","orderby":"spfesp","itemCount":"20","truncation":"25","nomobile":"0","name":"sitestore.browse-products-sitestore","defaultWidgetNo":3}',
      ));
    }
  }

//PROFILE PAGE WORK
  public function profilePageCreate() {

//GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "sitestoreproduct_index_view")
            ->query()
            ->fetchColumn();

    if (empty($page_id)) {

      $containerCount = 0;
      $widgetCount = 0;

      $db->insert('engine4_core_pages', array(
          'name' => "sitestoreproduct_index_view",
          'displayname' => 'Stores - Product Profile',
          'title' => 'Stores - Product Profile',
          'description' => 'This is product profile page.',
          'custom' => 0
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

//TOP CONTAINER
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'top',
          'page_id' => $page_id,
          'order' => $containerCount++,
      ));
      $top_container_id = $db->lastInsertId();

//INSERT TOP-MIDDLE
      $db->insert('engine4_core_content', array(
          'type' => 'container',
          'name' => 'middle',
          'page_id' => $page_id,
          'parent_content_id' => $top_container_id,
          'order' => $containerCount++,
      ));
      $top_middle_id = $db->lastInsertId();

//MAIN CONTAINER
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'main',
          'order' => $containerCount++,
          'params' => '',
      ));
      $main_container_id = $db->lastInsertId('engine4_core_content');

//RIGHT CONTAINER
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'right',
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
          'params' => '',
      ));
      $right_container_id = $db->lastInsertId('engine4_core_content');

//MIDDLE CONTAINER  
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $main_container_id,
          'order' => $containerCount++,
          'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.list-profile-breadcrumb',
          'parent_content_id' => $top_middle_id,
          'order' => $widgetCount++,
          'params' => '',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.overall-ratings',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"","show_rating":"both"}'
      ));    

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.quick-specification-sitestoreproduct',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Quick Specifications","titleCount":"true","nomobile":"1"}'
      ));
      
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.write-sitestoreproduct',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"nomobile":"1"}'
      ));
//      
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.information-sitestoreproduct',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Information","showContent":["modifiedDate","viewCount","likeCount","commentCount","tags"],"nomobile":"1"}'
      ));       
      
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.about-editor-sitestoreproduct',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"About Editor","titleCount":"true","nomobile":"1"}'
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.share',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Share and Report","titleCount":"true","options":["siteShare","friend","report","print","socialShare"],"nomobile":"1"}'
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.sitestoreproduct-products',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Recently Sold Products","itemCount":"2"}'
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.similar-items-sitestoreproduct',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Best Alternatives","statistics":["likeCount","reviewCount","commentCount"],"itemCount":"2","nomobile":"1"}'
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.related-products-view-sitestoreproduct',
          'parent_content_id' => $right_container_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Related Products","related":"tags","titleCount":"true","statistics":["likeCount","reviewCount","commentCount"],"itemCount":"2","nomobile":"1"}',
      ));
//
//      $db->insert('engine4_core_content', array(
//          'page_id' => $page_id,
//          'type' => 'widget',
//          'name' => 'sitestoreproduct.userproduct-sitestoreproduct',
//          'parent_content_id' => $right_container_id,
//          'order' => $widgetCount++,
//          'params' => '{"statistics":["likeCount","reviewCount","commentCount"],"title":"%s\'s Products","count":"2","nomobile":"1"}'
//      ));

//      $db->insert('engine4_core_content', array(
//          'page_id' => $page_id,
//          'type' => 'widget',
//          'name' => 'seaocore.people-like',
//          'parent_content_id' => $right_container_id,
//          'order' => $widgetCount++,
//          'params' => '{"nomobile":"1"}'
//      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'seaocore.scroll-top',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.full-width-list-information-profile',
          'parent_content_id' => $top_middle_id,
          'order' => $widgetCount++,
          'params' => '{"showContent":["postedDate","postedBy","viewCount","likeCount","commentCount","photo","photosCarousel","tags","description"]}'
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'core.container-tabs',
          'parent_content_id' => $main_middle_id,
          'order' => $widgetCount++,
          'params' => '{"max":"6"}',
      ));
      $tab_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.editor-reviews-sitestoreproduct',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"titleEditor":"Review","titleOverview":"Overview","titleDescription":"Description","titleCount":"","loaded_by_ajax":"1","title":"","show_slideshow":"1","slideshow_height":"500","slideshow_width":"800","showCaption":"1","showButtonSlide":"1","mouseEnterEvent":"0","thumbPosition":"bottom","autoPlay":"0","slidesLimit":"20","captionTruncation":"200","showComments":"1","nomobile":"0","name":"sitestoreproduct.editor-reviews-sitestoreproduct"}'
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.user-sitestoreproduct',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"User Reviews","titleCount":"true","loaded_by_ajax":"1"}'
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.specification-sitestoreproduct',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Specs","titleCount":"true","loaded_by_ajax":"1"}'
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.overview-sitestoreproduct',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Overview","titleCount":"true","loaded_by_ajax":"1"}'
      ));
      
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.location-sitestoreproduct',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Map","titleCount":"true"}'
      ));      

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.photos-sitestoreproduct',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Photos","titleCount":"true","loaded_by_ajax":"1"}'
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.video-sitestoreproduct',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Videos","titleCount":"true","loaded_by_ajax":"1"}'
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.discussion-sitestoreproduct',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Discussions","titleCount":"true","loaded_by_ajax":"1"}'
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'core.profile-links',
          'parent_content_id' => $tab_id,
          'order' => $widgetCount++,
          'params' => '{"title":"Links","titleCount":"true"}'
      ));

//      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
//        $db->insert('engine4_core_content', array(
//            'page_id' => $page_id,
//            'type' => 'widget',
//            'name' => 'advancedactivity.home-feeds',
//            'parent_content_id' => $tab_id,
//            'order' => $widgetCount++,
//            'params' => '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0"}'
//        ));
//      } else {
//        $db->insert('engine4_core_content', array(
//            'page_id' => $page_id,
//            'type' => 'widget',
//            'name' => 'activity.feed',
//            'parent_content_id' => $tab_id,
//            'order' => $widgetCount++,
//            'params' => '{"title":"Updates"}'
//        ));
//      }
    }
  }

//MAIN NAVIGATION WORK
  public function mainNavigationCreate() {

//GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

//GET CORE MENUITEMS TABLE
    $menuItemsTable = Engine_Api::_()->getDbTable('MenuItems', 'core');
    $menuItemsTableName = $menuItemsTable->info('name');

    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "core_main_sitestoreproduct")
            ->query()
            ->fetchColumn();
    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "core_main_sitestoreproduct",
          'module' => 'sitestoreproduct',
          'label' => "Stores",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::canViewSitestoreproducts',
          'params' => '{"route":"sitestoreproduct_general' . '","action":"home"}',
          'menu' => "core_main",
          'submenu' => '',
          'order' => 999,
      ));
    }

    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "mobi_browse_sitestoreproduct")
            ->query()
            ->fetchColumn();
    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "mobi_browse_sitestoreproduct",
          'module' => 'sitestoreproduct',
          'label' => "Products",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::canViewSitestoreproducts',
          'params' => '{"route":"sitestoreproduct_general' . '","action":"home"}',
          'menu' => "mobi_browse",
          'submenu' => '',
          'order' => 999,
      ));
    }
  }

//GUTTER NAVIGATION MENU WORK
  public function gutterNavigationCreate() {

//GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

//GET CORE MENUITEMS TABLE
    $menuItemsTable = Engine_Api::_()->getDbTable('MenuItems', 'core');
    $menuItemsTableName = $menuItemsTable->info('name');

    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_wishlist")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_wishlist",
          'module' => 'sitestoreproduct',
          'label' => "Add to Wishlist",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterWishlist',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'order' => 1,
          'enabled' => 0,
      ));
    }

    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_messageowner")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_messageowner",
          'module' => 'sitestoreproduct',
          'label' => "Message Owner",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterMessageowner',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'order' => 2,
      ));
    }

    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_print")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_print",
          'module' => 'sitestoreproduct',
          'label' => "Print",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterPrint',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'order' => 3,
      ));
    }


    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_share")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_share",
          'module' => 'sitestoreproduct',
          'label' => "Share",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterShare',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'order' => 4,
      ));
    }

    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_tfriend")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_tfriend",
          'module' => 'sitestoreproduct',
          'label' => "Tell a Friend",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterTfriend',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'order' => 5,
      ));
    }

    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_report")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_report",
          'module' => 'sitestoreproduct',
          'label' => "Report",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterReport',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'order' => 6,
      ));
    }
    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_edit")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_edit",
          'module' => 'sitestoreproduct',
          'label' => "Edit Details",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterEdit',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'order' => 7,
      ));
    }

    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_editoverview")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_editoverview",
          'module' => 'sitestoreproduct',
          'label' => 'Edit Overview',
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterEditoverview',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'enabled' => 0,
          'order' => 8,
      ));
    }

    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_editstyle")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_editstyle",
          'module' => 'sitestoreproduct',
          'label' => "Edit Style",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterEditstyle',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'enabled' => 0,
          'order' => 9,
      ));
    }

    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_close")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_close",
          'module' => 'sitestoreproduct',
          'label' => "Open / Close",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterClose',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'order' => 10,
      ));
    }
    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_publish")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_publish",
          'module' => 'sitestoreproduct',
          'label' => "Publish",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterPublish',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'order' => 11,
      ));
    }

    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_delete")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_delete",
          'module' => 'sitestoreproduct',
          'label' => "Delete",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterDelete',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'order' => 12,
      ));
    }
    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_editorpick")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_editorpick",
          'module' => 'sitestoreproduct',
          'label' => 'Add Best Alternatives',
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterEditorPick',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'order' => 13,
      ));
    }

    $menuItemsId = $menuItemsTable->select()
            ->from($menuItemsTableName, array('id'))
            ->where('name = ? ', "sitestoreproduct_gutter_review")
            ->query()
            ->fetchColumn();

    if (empty($menuItemsId)) {
      $menuItemsTable->insert(array(
          'name' => "sitestoreproduct_gutter_review",
          'module' => 'sitestoreproduct',
          'label' => "Write / Edit a Editor Review",
          'plugin' => 'Sitestoreproduct_Plugin_Menus::sitestoreproductGutterReview',
          'menu' => "sitestoreproduct_gutter",
          'submenu' => '',
          'order' => 14,
      ));
    }
  }

  public function mainNavigationEdit() {

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

//GET PRODUCT DETAILS
    $titleSinUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'));
    $titlePluUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titleplural', 'Products'));
    $titleSinLc = strtolower(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'));
    $titlePluLc = strtolower(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titleplural', 'Products'));

//GET CORE MENUITEMS TABLE
    $menuItemsTable = Engine_Api::_()->getDbTable('MenuItems', 'core');
    $menuItemsTableName = $menuItemsTable->info('name');

    $menuItemsTable->update(array('label' => "$titlePluUc"), array(
        'name = ?' => "mobi_browse_sitestoreproduct"
    ));

//    $db->query("UPDATE `engine4_core_menus` SET `title` = '$titlePluUc Main Navigation Menu' WHERE `name`='sitestoreproduct_main'");

    $menuItemsTable->update(array('label' => "$titlePluUc Home"), array(
        'name = ?' => "sitestoreproduct_main_home"
    ));

    $menuItemsTable->update(array('label' => "Browse $titlePluUc"), array(
        'name = ?' => "sitestoreproduct_main_browse"
    ));

    $menuItemsTable->update(array('label' => "My $titlePluUc"), array(
        'name = ?' => "sitestoreproduct_main_manage",
    ));
  }

  public function gutterNavigationEdit() {

//GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

//GET PRODUCT DETAILS
    $titlePluUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titleplural', 'Products'));

    $db->query("UPDATE `engine4_core_menus` SET `title` = '$titlePluUc Profile Page Options Menu' WHERE `name` = 
'sitestoreproduct_gutter'");
  }

  public function widgetizedPagesEdit($pageName, $previousTitleSin, $previousTitlePlu) {

//GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

//GET PRODUCT DETAILS
    $titleSinUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'));
    $titlePluUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titleplural', 'Products'));

//GET PAGE TABLE
    $pageTable = Engine_Api::_()->getDbTable('pages', 'core');
    $pageTableName = $pageTable->info('name');

//DELETE HOME PAGE
    $page_id = $pageTable->select()
            ->from($pageTableName, 'page_id')
            ->where('name = ?', "sitestoreproduct_index_$pageName")
            ->query()
            ->fetchColumn();

    if (!empty($page_id)) {

      $db->query("UPDATE `engine4_core_pages` SET `displayname` = REPLACE(displayname, '$previousTitlePlu', '$titlePluUc') WHERE `page_id` = $page_id AND `displayname` Like '%$previousTitlePlu%'");

      $db->query("UPDATE `engine4_core_pages` SET `title` = REPLACE(title, '$previousTitlePlu', '$titlePluUc'), `description` = REPLACE(description, '$previousTitlePlu', '$titlePluUc') WHERE `page_id` = $page_id AND `title` Like '%$previousTitlePlu%'");

      $db->query("UPDATE `engine4_core_pages` SET `description` = REPLACE(description, '$previousTitlePlu', '$titlePluUc') WHERE `page_id` = $page_id AND `description` Like '%$previousTitlePlu%'");

      $db->query("UPDATE `engine4_core_pages` SET `displayname` = REPLACE(displayname, '$previousTitleSin', '$titleSinUc') WHERE `page_id` = $page_id AND `displayname` Like '%$previousTitleSin%'");

      $db->query("UPDATE `engine4_core_pages` SET `title` = REPLACE(title, '$previousTitleSin', '$titleSinUc') WHERE `page_id` = $page_id AND `title` Like '%$previousTitleSin%'");

      $db->query("UPDATE `engine4_core_pages` SET `description` = REPLACE(description, '$previousTitleSin', '$titleSinUc') WHERE `page_id` = $page_id AND `description` Like '%$previousTitleSin%'");

      $db->query("UPDATE `engine4_core_content` SET `params` = REPLACE(params, '$previousTitlePlu', '$titlePluUc') WHERE `page_id` = $page_id AND `params` Like '%$previousTitlePlu%'");

      $db->query("UPDATE `engine4_core_content` SET `params` = REPLACE(params, '$previousTitleSin', '$titleSinUc') WHERE `page_id` = $page_id AND `params` Like '%$previousTitleSin%'");
    }
  }

//SEARCH FORM SETTING WORK
  public function activityFeedQueryEdit($previousTitleSin, $previousTitlePlu) {

//GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

//GET PRODUCT DETAILS
    $titlePluUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titleplural', 'Products'));
    $titleSinUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'));

    $db->query("UPDATE `engine4_activity_actiontypes` SET `body` = REPLACE(body, '$previousTitleSin', '$titleSinUc') WHERE `type` LIKE '%' AND `module` = 'sitestoreproduct' AND  `body` LIKE '%$previousTitleSin%'");
    $db->query("UPDATE `engine4_activity_actiontypes` SET `body` = REPLACE(body, '$previousTitlePlu', '$titleSinUc') WHERE `type` LIKE '%' AND `module` = 'sitestoreproduct' AND  `body` LIKE '%$previousTitlePlu%'");
  }

  public function searchFormSettingEdit($previousTitleSin, $previousTitlePlu) {

//GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

//GET PRODUCT DETAILS
    $titlePluUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titleplural', 'Products'));
    $titleSinUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'));

    $db->query("UPDATE `engine4_seaocore_searchformsetting` SET `label` = REPLACE(label, '$previousTitleSin', '$titleSinUc') WHERE `module` = 
'sitestoreproduct' AND `label` LIKE '%$previousTitleSin%'");

    $db->query("UPDATE `engine4_seaocore_searchformsetting` SET `label` = REPLACE(label, '$previousTitlePlu', '$titlePluUc') WHERE `module` = 
'sitestoreproduct' AND `label` LIKE '%$previousTitlePlu%'");
  }

  public function addBannedUrls() {

//GET DATABASE
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $seocoreBannedUrlsTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_bannedpageurls\'')->fetch();
    if (!empty($seocoreBannedUrlsTable)) {

      $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
      $bannedPageurlsTableName = $bannedPageurlsTable->info('name');

      $db = $bannedPageurlsTable->getAdapter();
      $db->beginTransaction();

      try {

        $urls = array(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.slugplural', 'products'), Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.slugsingular', 'product'));

        $data = $bannedPageurlsTable->select()
                ->from($bannedPageurlsTableName, 'word')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);        
        
        foreach ($urls as $url) {

          $bannedWordsNew = preg_split('/\s*[,\n]+\s*/', $url);

          $words = array_map('strtolower', array_filter(array_values($bannedWordsNew)));

          if (in_array($words[0], $data)) {
            continue;
          }
          $bannedPageurlsTable->setWords($bannedWordsNew);
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    }
  }
  
  public function categoryWidgetizedPagesDelete($categoryId = 0) {

    //GET PAGE TABLE
    $pageTable = Engine_Api::_()->getDbTable('pages', 'core');
    $pageTableName = $pageTable->info('name');

    //DELETE CATEGORY PAGE
    $page_id = $pageTable->select()
            ->from($pageTableName, 'page_id')
            ->where('name = ?', "sitestoreproduct_index_category-home_category_" . $categoryId)
            ->query()
            ->fetchColumn();

    if (!empty($page_id)) {
      Engine_Api::_()->getDbTable('content', 'core')->delete(array('page_id = ?' => $page_id));
      $pageTable->delete(array('page_id = ?' => $page_id));
    }
  }  

}

