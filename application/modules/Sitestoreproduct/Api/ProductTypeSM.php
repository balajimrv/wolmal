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
class Sitestoreproduct_Api_ProductTypeSM extends Core_Api_Abstract {

    public function defaultCreation($pageTable, $contentTable) {

        $this->productHomePage($pageTable, $contentTable);
        $this->productBrowsePage($pageTable, $contentTable);
        $this->productProfilePage($pageTable, $contentTable); 
    }
    //HOME PAGE WORK
    public function productHomePage($pageTable, $contentTable) {

        //GET DATABASE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //GET PRODUCT DETAILS
        $titleSinUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'));
        $titlePluUc = ucfirst(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titleplural', 'Products'));
        $titleSinLc = strtolower(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product'));
        $titlePluLc = strtolower(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titleplural', 'Products'));

        $page_id = $db->select()
                ->from($pageTable, 'page_id')
                ->where('name = ?', "sitestoreproduct_index_home")
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {

            $containerCount = 0;
            $widgetCount = 0;

            //CREATE PAGE
            $db->insert($pageTable, array(
                'name' => "sitestoreproduct_index_home",
                'displayname' => 'Stores - ' . $titlePluUc . ' Home',
                'title' => 'Stores - ' . $titlePluUc . ' Home',
                'description' => 'This is the ' . $titleSinLc . ' home page.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

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
                'name' => 'sitemobile.sitemobile-navigation',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));
            
            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.slideshow-sitestoreproduct',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":true,"statistics":[],"ratingType":"rating_both","postedby":"0","fea_spo":"featured","in_stock":"1","orderby":"spfesp","itemCount":"20","truncation":"25","nomobile":"0","name":"sitestoreproduct.slideshow-sitestoreproduct"}',
            ));

            //TABED-CONTAINER
            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitemobile.container-tabs-columns',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"layoutContainer":"horizontal","title":"","name":"sitemobile.container-tabs-columns"}'
            ));
            $main_middle_tabed_id = $db->lastInsertId();

//      $db->insert($contentTable, array(
//          'page_id' => $page_id,
//          'type' => 'widget',
//          'name' => 'sitestoreproduct.zeroproduct-sitestoreproduct',
//          'parent_content_id' => $main_middle_id,
//          'order' => $widgetCount++,
//          'params' => '',
//      ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.products-sitestoreproduct',
                'parent_content_id' => $main_middle_tabed_id,
                'order' => $widgetCount++,
                'params' => '{"title":"New Arrival","titleCount":true,"statistics":[],"viewType":"gridview","columnWidth":"200","ratingType":"rating_avg","fea_spo":"newlabel","columnWidth":"165","columnHeight":"225","layouts_views":["1","2"],"showContent":["price","endDate","location","postedDate"],"popularity":"view_count","postedby":"0","itemCount":"9","truncation":"25","name":"sitestoreproduct.products-sitestoreproduct"}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.products-sitestoreproduct',
                'parent_content_id' => $main_middle_tabed_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Most Reviewed","titleCount":true,"statistics":[],"viewType":"gridview","columnWidth":"200","ratingType":"rating_avg","fea_spo":"","columnWidth":"165","columnHeight":"225","layouts_views":["1","2"],"showContent":["price","endDate","location","postedDate"],"popularity":"review_count","postedby":"0","itemCount":"9","truncation":"25","name":"sitestoreproduct.products-sitestoreproduct"}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.products-sitestoreproduct',
                'parent_content_id' => $main_middle_tabed_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Top Selling","titleCount":true,"statistics":[],"viewType":"gridview","columnWidth":"200","ratingType":"rating_avg","fea_spo":"","columnWidth":"165","columnHeight":"225","layouts_views":["1","2"],"showContent":["price","endDate","location","postedDate"],"popularity":"top_selling","postedby":"0","itemCount":"9","truncation":"25","name":"sitestoreproduct.products-sitestoreproduct"}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.products-sitestoreproduct',
                'parent_content_id' => $main_middle_tabed_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Featured","titleCount":true,"statistics":[],"viewType":"gridview","columnWidth":"200","ratingType":"rating_avg","fea_spo":"featured","columnWidth":"165","columnHeight":"225","layouts_views":["1","2"],"showContent":["price","endDate","location","postedDate"],"popularity":"view_count","postedby":"0","itemCount":"9","truncation":"25","name":"sitestoreproduct.products-sitestoreproduct"}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.products-sitestoreproduct',
                'parent_content_id' => $main_middle_tabed_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Sponsered","titleCount":true,"statistics":[],"viewType":"gridview","columnWidth":"200","ratingType":"rating_avg","fea_spo":"sponsored","columnWidth":"165","columnHeight":"225","layouts_views":["listview","gridview"],"showContent":["price","endDate","location","postedDate"],"popularity":"view_count","postedby":"0","itemCount":"9","truncation":"25","name":"sitestoreproduct.products-sitestoreproduct"}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.products-sitestoreproduct',
                'parent_content_id' => $main_middle_tabed_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Recent","titleCount":true,"statistics":[],"viewType":"gridview","columnWidth":"200","ratingType":"rating_avg","fea_spo":"","layouts_views":["1","2"],"columnWidth":"165","columnHeight":"225","showContent":["price","endDate","location","postedDate"],"popularity":"product_id","postedby":"0","itemCount":"9","truncation":"25","name":"sitestoreproduct.products-sitestoreproduct"}'
            ));
            
            //Product of the day 
//            $db->insert($contentTable, array(
//                'page_id' => $page_id,
//                'type' => 'widget',
//                'name' => 'sitestoreproduct.item-sitestoreproduct',
//                'parent_content_id' => $main_middle_tabed_id,
//                'order' => $widgetCount++
//            ));
        }
    }

//BROWSE PAGE WORK
    public function productBrowsePage($pageTable, $contentTable) {

        //GET DATABASE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $page_id = $db->select()
                ->from($pageTable, 'page_id')
                ->where('name = ?', "sitestoreproduct_index_index")
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (!$page_id) {

            $containerCount = 0;
            $widgetCount = 0;

            $db->insert($pageTable, array(
                'name' => "sitestoreproduct_index_index",
                'displayname' => 'Stores - Browse Products',
                'title' => 'Stores - Browse Products',
                'description' => '',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

//MAIN CONTAINER
            $db->insert($contentTable, array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

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
                'name' => 'sitemobile.sitemobile-navigation',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));
            
             // Insert Advance search
            $db->insert($contentTable, array(
                'type' => 'widget',
                'name' => 'sitemobile.sitemobile-advancedsearch',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'params' => '{"search":"2","title":"","nomobile":"0","name":"sitemobile.sitemobile-advancedsearch"}',
                'order' => $widgetCount++,
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.browse-products-sitestoreproduct',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":true,"layouts_views":["1","2"],"layouts_order":1,"statistics":"","columnWidth":"165","truncationGrid":"25","viewType":"gridview","ratingType":"rating_both","columnHeight":"225","in_stock":"1","postedby":"0","orderby":"product_id","itemCount":"10","truncation":"25"}',
            ));
        }
    }

//PROFILE PAGE WORK
    public function productProfilePage($pageTable, $contentTable) {

//GET DATABASE
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $page_id = $db->select()
                ->from($pageTable, 'page_id')
                ->where('name = ?', "sitestoreproduct_index_view")
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {

            $containerCount = 0;
            $widgetCount = 0;

            $db->insert($pageTable, array(
                'name' => "sitestoreproduct_index_view",
                'displayname' => 'Stores - Product Profile',
                'title' => 'Stores - Product Profile',
                'description' => 'This is product profile page.',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId($pageTable);

//MAIN CONTAINER
            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_container_id = $db->lastInsertId($contentTable);

//MIDDLE CONTAINER  
            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_middle_id = $db->lastInsertId($contentTable);

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.list-profile-breadcrumb',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.list-information-profile',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"showContent":["postedDate","postedBy","viewCount","likeCount","commentCount","photo","photosCarousel","tags","location","description","title","compare","wishlist","reviewCreate"]}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitemobile.container-tabs-columns',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"max":"6"}',
            ));
            $tab_id = $db->lastInsertId($contentTable);

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.editor-reviews-sitestoreproduct',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"titleEditor":"Review","titleOverview":"Overview","titleDescription":"Description","titleCount":"","title":"","show_slideshow":"1","slideshow_height":"500","slideshow_width":"800","showCaption":"1","showButtonSlide":"1","mouseEnterEvent":"0","thumbPosition":"bottom","autoPlay":"0","slidesLimit":"20","captionTruncation":"200","showComments":"1","nomobile":"0","name":"sitestoreproduct.editor-reviews-sitestoreproduct"}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.user-sitestoreproduct',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"User Reviews","titleCount":"true"}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.specification-sitestoreproduct',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Specs","titleCount":"true"}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.overview-sitestoreproduct',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Overview","titleCount":"true"}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.photos-sitestoreproduct',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Photos","titleCount":"true"}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.video-sitestoreproduct',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Videos","titleCount":"true"}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitestoreproduct.discussion-sitestoreproduct',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Discussions","titleCount":"true"}'
            ));

            $db->insert($contentTable, array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'sitemobile.profile-links',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Links","titleCount":"true"}'
            ));
        }
    }
}

