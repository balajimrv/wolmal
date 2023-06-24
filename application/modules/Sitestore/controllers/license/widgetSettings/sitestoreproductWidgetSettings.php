<?php

// package-level
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitestoreproductWidgetSettings.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$db = Zend_Db_Table_Abstract::getDefaultAdapter();

$contentTable = Engine_Api::_()->getDbtable('content', 'core');
$contentTableName = $contentTable->info('name');
$pageTable = Engine_Api::_()->getDbtable('pages', 'core');
$pageTableName = $pageTable->info('name');

$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
$db->query("INSERT IGNORE INTO `engine4_sitestoreproduct_editors` (`user_id`, `designation`, `details`, `about`, `badge_id`, `super_editor`) VALUES ($viewer_id,'Super Editor','','',0,1)");

$db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('SITESTOREPRODUCT_EMAIL_FRIEND', 'sitestoreproduct', '[host],[email],[recipient_title],[recipient_link],[review_title],[review_title_with_link],[user_email],[userComment]'),
('SITESTOREPRODUCT_REVIEW_WRITE', 'sitestoreproduct', '[host],[email],[recipient_title],[recipient_link],[review_title],[review_description],[review_link],[product_name],[product_title_with_link],[user_name]'),
('SITESTOREPRODUCT_REVIEW_DISAPPROVED', 'sitestoreproduct', '[host],[email],[recipient_title],[recipient_link],[review_title],[review_description],[review_link]'),
('SITESTOREPRODUCT_REVIEW_APPROVED', 'sitestoreproduct', '[host],[email],[recipient_title],[recipient_link],[review_title],[review_description],[review_link]'),
('sitestoreproduct_product_CREATION_EDITOR', 'sitestoreproduct', '[host],[object_title],[object_link],[object_description]'),
('SITESTOREPRODUCT_EDITOR_EMAIL', 'sitestoreproduct', '[host],[email],[sender],[message]'),
('SITESTOREPRODUCT_EDITOR_ASSIGN_EMAIL', 'sitestoreproduct', '[sender],[editor_page_url]'),
('notify_sitestoreproduct_write_review', 'sitestoreproduct', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_description],[object_parent_link],[object_parent_title],[object_parent_with_link]'),
('SITESTOREPRODUCT_EDITORREVIEW_CREATION', 'sitestoreproduct', '[host],[editor_name],[editor],[object_title],[object_parent_with_link],[object_link], [object_parent_title],[object_description]'),
('notify_sitestoreproduct_approved_review', 'sitestoreproduct', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[object_title],[object_link],[object_description],[object_parent_link],[object_parent_title],[object_parent_with_link],[anonymous_name]'),
('SITESTOREPRODUCT_APPROVED_EMAIL_NOTIFICATION', 'sitestoreproduct', '[host],[email],[subject],[title],[message][object_link]'),
('SITESTOREPRODUCT_TELLAFRIEND_EMAIL', 'sitestoreproduct', '[host],[email],[sender],[message][object_link]'),
('SITESTOREPRODUCT_ASKOPINION_EMAIL', 'sitestoreproduct', '[host],[email],[sender],[message][object_link]');");

$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
  
("comment_sitestoreproduct_photo", "sitestoreproduct", \'{item:$subject} commented on {item:$owner}\'\'s {item:$object:photo}: {body:$body}\', 1, 1, 1, 1, 1, 0),
("comment_sitestoreproduct_video", "sitestoreproduct", \'{item:$subject} commented on {item:$owner}\'\'s {item:$object:video}: {body:$body}\', 1, 1, 1, 1, 1, 0),
("comment_sitestoreproduct_product", "sitestoreproduct", \'{item:$subject} commented on {item:$owner}\'\'s {var:$producttype} product {item:$object:$title}: {body:$body}\', "1", "1", "1", "1", "1", 1),
("comment_sitestoreproduct_review", "sitestoreproduct", \'{item:$subject} commented on {item:$owner}\'\'s review {item:$object:$title}: {body:$body}\', "1", "1", "1", "1", "1", 1),
("nestedcomment_sitestoreproduct_product", "sitestoreproduct", \'{item:$subject} replied to a comment on {item:$owner}\'\'s {var:$producttype} product {item:$object:$title}: {body:$body}\', "1", "1", "1", "1", "1", 1),
("nestedcomment_sitestoreproduct_review", "sitestoreproduct", \'{item:$subject} replied to a comment on {item:$owner}\'\'s review {item:$object:$title}: {body:$body}\', "1", "1", "1", "1", "1", 1),
("follow_sitestoreproduct_wishlist", "sitestoreproduct", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:wishlist}: {body:$body}\', 1, 1, 1, 1, 1, 1);');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitestoreproduct_discussion_reply", "sitestore", \'{item:$subject} has {item:$object:posted} on a {itemParent:$object::product topic} you posted on.\', 0, ""),
("sitestoreproduct_discussion_response", "sitestore", \'{item:$subject} has {item:$object:posted} on a {itemParent:$object::product topic} you created.\', 0, ""),
("sitestoreproduct_video_processed", "sitestore", \'Your {item:$object:product video} is ready to be viewed.\', 0, ""),
("sitestoreproduct_video_processed_failed", "sitestore", \'Your {item:$object:product video} has failed to process.\', 0, ""),
("sitestoreproduct_write_review", "sitestore", \'{item:$subject} has written a {item:$object:review} for the {itemParent:$object::product}.\', "0", ""),
("sitestoreproduct_editorreview", "sitestore", \'{item:$subject} has written a {item:$object:review} for the {itemParent:$object::product}.\', "0", ""),
("sitestoreproduct_wishlist_followers", "sitestore", \'{item:$subject} has added a new {item:$object:product} in {var:$wishlist}.\', "0", ""),
("sitestoreproduct_approved_review", "sitestore", \'{item:$subject} has approved a {item:$object:review} by {var:$anonymous_name} on your {itemParent:$object::product}.\', "0", ""),
("follow_sitestoreproduct_wishlist", "sitestore", \'{item:$subject} is following {item:$object}\', "0", "");');

$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitestoreproduct_order_place_login_viewer", "sitestore", \'{item:$subject} placed an order {var:$order_id} in {item:$page} store.\', 0, ""),
("sitestoreproduct_order_place_logout_viewer", "sitestore", \'{var:$viewer} placed an order {var:$order_id} in {item:$page} store.\', 0, ""),
("sitestoreproduct_order_status_change", "sitestore", \'Your order no {var:$order_id} (purchased from {item:$page}) status has been changed.\', 0, ""),
("sitestoreproduct_order_status_admin_change", "sitestore", \'Admin has changed the order status of order {var:$order_id}\', 0, ""),
("sitestoreproduct_order_comment_from_buyer", "sitestore", \'{item:$subject} has posted a comment on order no {var:$order_no} of {item:$page} store.\', 0, ""),
("sitestoreproduct_order_comment_to_buyer", "sitestore", \'{item:$subject} has posted a comment on order no {var:$order_no} of {item:$page} store.\', 0, ""),
("sitestoreproduct_order_comment_to_store_admin", "sitestore", \'{item:$subject} has posted a comment on order no {var:$order_no} of {item:$page} store.\', 0, ""),
("sitestoreproduct_order_ship", "sitestore", \'Store {item:$subject} has shipped your order no {var:$order_no}.\', 0, "");');


$filesize = (int) ini_get('upload_max_filesize') * 1024;
$db->query('INSERT IGNORE INTO `engine4_authorization_permissions` 
SELECT 
level_id as `level_id`, 
"sitestore_store" as `type`, 
"filesize_main" as `name`, 
3 as `value`, 
' . $filesize . ' as `params` 
FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin", "user");');

$db->query('INSERT IGNORE INTO `engine4_authorization_permissions` 
SELECT 
level_id as `level_id`, 
"sitestore_store" as `type`, 
"filesize_sample" as `name`, 
3 as `value`, 
' . $filesize . ' as `params` 
FROM `engine4_authorization_levels` WHERE `type` IN("moderator", "admin", "user");');

$db->query('
INSERT IGNORE INTO `engine4_sitestoreproduct_startuppages` (`startuppages_id`, `title`, `short_description`, `description`, `status`, `delete`) VALUES
(1, "Get Started", "Open a new store and start selling your products.", \'<div>
<div style="border-bottom-width: 1px; margin-bottom: 20px; padding-bottom: 15px;">
<p style="font-size: 23px; font-weight: bold; margin-bottom: 10px;">Open your New Store</p>
<p style="line-height: 1.15; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 16px;">3 easy steps to getting started here.</span></p>
</div>
<div style="overflow: hidden; border-bottom-width: 1px;">
<div style="float: right; vertical-align: top;"><img alt="" src="./application/modules/Sitestoreproduct/externals/images/pages/get-started-1.png"></div>
<div style="overflow: hidden; display: table-cell; height: 250px; vertical-align: middle; padding: 0px 50px;">
<p style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">1. Choose a Package</p>
<p style="font-size: 16px; line-height: 24px;">Choose a package that best suits your requirements.</p>
</div>
</div>
<div style="overflow: hidden; border-bottom-width: 1px;">
<div style="vertical-align: top; float: left;"><img alt="" src="./application/modules/Sitestoreproduct/externals/images/pages/get-started-2.png"></div>
<div style="overflow: hidden; display: table-cell; height: 250px; vertical-align: middle; padding: 0px 50px;">
<p style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">2. Open a New Store</p>
<p style="font-size: 16px; line-height: 24px;">Configure your store based on the package you have chosen.</p>
</div>
</div>
<div style="margin-bottom: 20px; overflow: hidden;">
<div style="float: right; vertical-align: top;"><img alt="" src="./application/modules/Sitestoreproduct/externals/images/pages/get-started-3.png"></div>
<div style="overflow: hidden; display: table-cell; height: 250px; vertical-align: middle; padding: 0px 50px;">
<p style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">3. Get Started</p>
<p style="font-size: 16px; line-height: 24px;">Tell users about most of your store components.</p>
</div>
</div>
</div>\', 1, 0),

(2, "Basics", "Learn how to attract users and maximize your sales.", \'<div>
<div style="border-bottom-width: 1px; margin-bottom: 20px; padding-bottom: 15px;">
<p style="font-size: 23px; font-weight: bold; margin-bottom: 10px;">Basics Page</p>
<p style="line-height: 1.15; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 16px;">Learn how to attract users and maximize your sales.</span></p>
</div>
<div style="overflow: hidden; border-bottom-width: 1px;">
<div style="float: right; vertical-align: top;"><img src="./application/modules/Sitestoreproduct/externals/images/pages/basics-1.png" alt=""></div>
<div style="overflow: hidden; display: table-cell; height: 250px; vertical-align: middle; padding: 0px 50px;">
<p style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">1. Create Products</p>
<p style="font-size: 16px; line-height: 24px;">Create products with attractive prices and good discounts to attract maximum users.</p>
</div>
</div>
<div style="overflow: hidden; border-bottom-width: 1px;">
<div style="vertical-align: top; float: left;"><img src="./application/modules/Sitestoreproduct/externals/images/pages/basics-2.png" alt=""></div>
<div style="overflow: hidden; display: table-cell; height: 250px; vertical-align: middle; padding: 0px 50px;">
<p style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">2. Upload Attractive Photos</p>
<p style="font-size: 16px; line-height: 24px;">Upload good and attractive photos of your stores and products to draw users" attention.</p>
</div>
</div>
<div style="margin-bottom: 20px; overflow: hidden;">
<div style="float: right; vertical-align: top;"><img src="./application/modules/Sitestoreproduct/externals/images/pages/basics-3.png" alt=""></div>
<div style="overflow: hidden; display: table-cell; height: 250px; vertical-align: middle; padding: 0px 50px;">
<p style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">3. Upload Informative Videos</p>
<p style="font-size: 16px; line-height: 24px;">Good informative videos about your product will bring users faith in handling and using your products.</p>
</div>
</div>
</div>\', 1, 0),


(3, "Success Stories", "See how some stores are doing great here.", \'<div>
<div style="border-bottom-width: 1px; margin-bottom: 20px; padding-bottom: 15px;">
<p style="font-size: 23px; font-weight: bold; margin-bottom: 10px;">Success Stories</p>
</div>
<p style="line-height: 1.15; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 16px;">Showcase success stories of stores on your site here.</span></p>
</div>\', 1, 0),

(4, "Tools", "Tell the users about most of your store.", \'<div>
<div style="border-bottom-width: 1px; margin-bottom: 20px; padding-bottom: 15px;">
<p style="font-size: 23px; font-weight: bold; margin-bottom: 10px;">Tools</p>
<p style="line-height: 1.15; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 16px;">Tell the users about most of your store.</span></p>
</div>
<div style="overflow: hidden; border-bottom-width: 1px;">
<div style="float: right; vertical-align: top;"><img src="./application/modules/Sitestoreproduct/externals/images/pages/tools-1.png" alt=""></div>
<div style="overflow: hidden; display: table-cell; height: 250px; vertical-align: middle; padding: 0px 50px;">
<p style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">1. Products</p>
<p style="font-size: 16px; line-height: 24px;">Create products in your store with attractive pricing, discounts, photos, videos, etc.</p>
</div>
</div>
<div style="overflow: hidden; border-bottom-width: 1px;">
<div style="vertical-align: top; float: left;"><img src="./application/modules/Sitestoreproduct/externals/images/pages/tools-2.png" alt=""></div>
<div style="overflow: hidden; display: table-cell; height: 250px; vertical-align: middle; padding: 0px 50px;">
<p style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">2. Photos</p>
<p style="font-size: 16px; line-height: 24px;">Upload attractive photos to attract more and more customers to your store.</p>
</div>
</div>
<div style="overflow: hidden; border-bottom-width: 1px;">
<div style="float: right; vertical-align: top;"><img src="./application/modules/Sitestoreproduct/externals/images/pages/tools-3.png" alt=""></div>
<div style="overflow: hidden; display: table-cell; height: 250px; vertical-align: middle; padding: 0px 50px;">
<p style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">3. Videos</p>
<p style="font-size: 16px; line-height: 24px;">Uploading videos to share happenings at your store.</p>
</div>
</div>
<div style="overflow: hidden; border-bottom-width: 1px;">
<div style="vertical-align: top; float: left;"><img src="./application/modules/Sitestoreproduct/externals/images/pages/tools-4.png" alt=""></div>
<div style="overflow: hidden; display: table-cell; height: 250px; vertical-align: middle; padding: 0px 50px;">
<p style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">4. Offers</p>
<p style="font-size: 16px; line-height: 24px;">Offer great discounts on your store to attract more users and increase your sales.</p>
</div>
</div>
<div style="margin-bottom: 20px; overflow: hidden;">
<div style="float: right; vertical-align: top;"><img src="./application/modules/Sitestoreproduct/externals/images/pages/tools-5.png" alt=""></div>
<div style="overflow: hidden; display: table-cell; height: 250px; vertical-align: middle; padding: 0px 50px;">
<p style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">5. Form</p>
<p style="font-size: 16px; line-height: 24px;">Gather feedback about your store to know what more your users want from your store, what problems they have, etc.</p>
</div>
</div>
</div>\', 1, 0);
');


//$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
//("sitestoreproductvideo_main_browse", "sitestoreproduct", "Browse Videos", "", \'{"route":"sitestoreproduct_video_general"}\', "sitestoreproductvideo_main", "", 1);');
//
//$db->query('INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
//("sitestoreproductvideo_main", "standard", "Product Video Main Navigation Menu");');

// ANKIT 2nd work
//      $select = new Zend_Db_Select($db);
//      $select_store = $select
//              ->from('engine4_core_pages', 'page_id')
//              ->where('name = ?', 'sitestore_index_view')
//              ->limit(1);
//      $page = $select_store->query()->fetchAll();
//      if (!empty($page)) {
//        $store_id = $page[0]['page_id'];
//
//        //INSERTING THE PRODUCTS WIDGET IN SITESTORE_ADMIN_CONTENT TABLE ALSO.
//        Engine_Api::_()->getDbtable('admincontent', 'sitestore')->setAdminDefaultInfo('sitestoreproduct.store-profile-products', $store_id, 'Products', 'true', '0');
//
//        //INSERTING THE POLL WIDGET IN CORE_CONTENT TABLE ALSO.
//        Engine_Api::_()->getApi('layoutcore', 'sitestore') ->setContentDefaultInfo('sitestoreproduct.store-profile-products', $store_id, 'Products', 'true', '0');
//
//        //INSERTING THE POLL WIDGET IN SITESTORE_CONTENT TABLE ALSO.
//        $select = new Zend_Db_Select($db);
//        $contentstore_ids = $select->from('engine4_sitestore_contentstores', 'contentstore_id')->query()->fetchAll();
//        foreach ($contentstore_ids as $contentstore_id) {
//          if (!empty($contentstore_id)) {
//            Engine_Api::_()->getDbtable('content', 'sitestore')->setDefaultInfo('sitestoreproduct.store-profile-products', $contentstore_id['contentstore_id'], 'Products', 'true', '0');
//          }
//        }
//      }

      // CREATE WIDGETIZED store
      // CREATE STORE HOME store
      $selectPage = $pageTable->select()
              ->from($pageTableName, array('page_id'))
              ->where('name =?', 'sitestore_index_home')
              ->limit(1);
      $fetchPageId = $selectPage->query()->fetchAll();
      if (empty($fetchPageId)) {

        $db->insert('engine4_core_pages', array(
            'name' => 'sitestore_index_home',
            'displayname' => 'Stores - Stores Home',
            'title' => 'Stores - Stores Home',
            'description' => 'This is the store home page.',
            'custom' => 0,
        ));
        $page_id = $db->lastInsertId('engine4_core_pages');

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'container',
            'name' => 'top',
            'parent_content_id' => null,
            'order' => 1,
            'params' => '',
        ));
        $top_id = $db->lastInsertId('engine4_core_content');

        //CONTAINERS
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'container',
            'name' => 'main',
            'parent_content_id' => Null,
            'order' => 2,
            'params' => '',
        ));
        $container_id = $db->lastInsertId('engine4_core_content');

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'container',
            'name' => 'middle',
            'parent_content_id' => $top_id,
            'params' => '',
        ));
        $top_middle_id = $db->lastInsertId('engine4_core_content');

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'container',
            'name' => 'middle',
            'parent_content_id' => $container_id,
            'order' => 2,
            'params' => '',
        ));
        $middle_id = $db->lastInsertId('engine4_core_content');

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.navigation-sitestoreproduct',
            'parent_content_id' => $top_middle_id,
            'order' => 1,
            'params' => '',
        ));

        //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestore.horizontal-searchbox-sitestore',
            'parent_content_id' => $middle_id,
            'order' => 2,
            'params' => '',
        ));
        
        //INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestore.zerostore-sitestore',
            'parent_content_id' => $middle_id,
            'order' => 2,
            'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1","browseredirect":"pinboard"}',
        ));        

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestore.pinboard-stores',
            'parent_content_id' => $middle_id,
            'order' => 3,
            'params' => '{"title":"","statistics":["likeCount","commentCount"],"show_buttons":["like","share","facebook","twitter","pinit","tellAFriend"],"category_id":"0","fea_spo":"","detactLocation":"0","locationmiles":"1000","popularity":"like_count","interval":"overall","postedby":"0","showoptions":["viewCount","likeCount","price","reviewsRatings"],"autoload":"1","itemWidth":"274","withoutStretch":"0","itemCount":"12","noOfTimes":"0","truncationDescription":"100","defaultLoadingImage":"1","nomobile":"0","name":"sitestore.pinboard-stores"}',
        ));
      }

      // CREATE BROWSE STORE
      $selectPage = $pageTable->select()
              ->from($pageTableName, array('page_id'))
              ->where('name =?', 'sitestore_index_index')
              ->limit(1);
      $page_id = $selectPage->query()->fetchAll();
      if (empty($page_id)) {
        $pageCreate = $pageTable->createRow();
        $pageCreate->name = 'sitestore_index_index';
        $pageCreate->displayname = 'Stores - Browse Stores';
        $pageCreate->title = 'Stores - Browse Stores';
        $pageCreate->description = 'This is the store browse page.';
        $pageCreate->custom = 0;
        $pageCreate->save();
        $page_id = $pageCreate->page_id;
        
        //INSERT TOP CONTAINER
        $topContainer = $contentTable->createRow();
        $topContainer->page_id = $page_id;
        $topContainer->type = 'container';
        $topContainer->name = 'top';
        $topContainer->order = 1;
        $topContainer->save();
        $top_id = $topContainer->content_id;         

        //INSERT MAIN CONTAINER
        $mainContainer = $contentTable->createRow();
        $mainContainer->page_id = $page_id;
        $mainContainer->type = 'container';
        $mainContainer->name = 'main';
        $mainContainer->order = 2;
        $mainContainer->save();
        $container_id = $mainContainer->content_id;
        
        //INSERT TOP- MIDDLE CONTAINER
        $topMiddleContainer = $contentTable->createRow();
        $topMiddleContainer->page_id = $page_id;
        $topMiddleContainer->type = 'container';
        $topMiddleContainer->name = 'middle';
        $topMiddleContainer->parent_content_id = $top_id;
        $topMiddleContainer->order = 6;
        $topMiddleContainer->save();
        $top_middle_id = $topMiddleContainer->content_id;        

        //INSERT MAIN - RIGHT CONTAINER
        $mainRightContainer = $contentTable->createRow();
        $mainRightContainer->page_id = $page_id;
        $mainRightContainer->type = 'container';
        $mainRightContainer->name = 'right';
        $mainRightContainer->parent_content_id = $container_id;
        $mainRightContainer->order = 5;
        $mainRightContainer->save();
        $right_id = $mainRightContainer->content_id;
        
        //INSERT MAIN - MIDDLE CONTAINER
        $mainMiddleContainer = $contentTable->createRow();
        $mainMiddleContainer->page_id = $page_id;
        $mainMiddleContainer->type = 'container';
        $mainMiddleContainer->name = 'middle';
        $mainMiddleContainer->parent_content_id = $container_id;
        $mainMiddleContainer->order = 6;
        $mainMiddleContainer->save();
        $middle_id = $mainMiddleContainer->content_id;        

        //INSERT NAVIGATION WIDGET
        $tempOrder = 1;
        Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitestoreproduct.navigation-sitestoreproduct', $top_middle_id, $tempOrder, '', '', '{"title":"","titleCount":true}');

        //INSERT NAVIGATION WIDGET
        Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitestore.alphabeticsearch-sitestore', $top_middle_id, $tempOrder++, '', '', '');

        //INSERT storeS WIDGET
        Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitestore.stores-sitestore', $middle_id, $tempOrder++, '', '', '{"title":"","titleCount":true,"layouts_views":["1","2","3"],"layouts_oder":"2","columnWidth":"195","statistics":["likeCount","followCount","viewCount","reviewCount"],"is_store":"1","columnHeight":"222","turncation":"40","showlikebutton":"1","showfeaturedLable":"1","showsponsoredLable":"1","showlocation":"1","showprice":"0","showpostedBy":"0","showdate":"0","showContactDetails":"0","category_id":"0","nomobile":"0","name":"sitestore.stores-sitestore"}');

        //INSERT "startup - link" WIDGET
        Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitestoreproduct.store-startup-link', $right_id, $tempOrder++, '', '', '', '{"title":"","titleCount":true}');

        //INSERT "Categories" WIDGET
        Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitestore.categories-sitestore', $right_id, $tempOrder++, "Categories", '', '', '{"title":"","titleCount":true}');

        //INSERT SEARCH store WIDGET
        Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitestore.search-sitestore', $right_id, $tempOrder++, '', '', '{"title":"","titleCount":true}');

        //INSERT POPULAR LOCATION WIDGET WIDGET
        Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitestoreproduct.top-selling-store', $right_id, $tempOrder++, '', '', '{"title":"Top Selling Stores","titleCount":true,"autoEdit":true,"interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","statistics":["likeCount","reviewCount"],"truncation":"25","viewType":"gridview","columnWidth":"180","columnHeight":"328","itemCount":"5","display_by":"0","nomobile":"0","name":"sitestore.top-selling-store"}');

        //INSERT POPULAR LOCATION WIDGET WIDGET
        Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitestore.popularlocations-sitestore', $right_id, $tempOrder++, '', '', '{"title":"Popular Locations"}');

        //INSERT TAG CLOUD WIDGET
        Engine_Api::_()->sitestore()->setDefaultDataContentWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitestore.tagcloud-sitestore', $right_id, $tempOrder++, '', '', '');
      }

// ANKIT 1st
//      // INSERT "BEST SELLING PRODUCT" WIDGET AT STORE PROFILE STORE: AT RIGHT SIDE - 3RD POSITION
//      $temp_page_id = $db->query("SELECT `page_id` FROM `engine4_core_pages` WHERE `name` LIKE 'sitestore_index_view' LIMIT 1")->fetch();
//      if (!empty($temp_page_id)) {
//        $page_id = $temp_page_id['page_id'];
//        $isWidgetExist = $db->query("SELECT `content_id` FROM `engine4_core_content` WHERE `page_id` = $page_id AND `name` LIKE 'sitestoreproduct.sitestoreproduct-products' LIMIT 1")->fetch();
//        if (empty($isWidgetExist)) {
//          $temp_content_table = $db->query("SELECT `content_id` FROM `engine4_core_content` WHERE `page_id` = $page_id AND `type` LIKE 'container' AND `name` LIKE 'right' LIMIT 1")->fetch();
//          if (!empty($temp_content_table)) {
//            $getSecondSmallestWidget = $db->query("SELECT * FROM `engine4_core_content` WHERE `parent_content_id` = " . $temp_content_table['content_id'] . " order by `order` ASC limit 1,1;")->fetch();
//            $db->query('INSERT INTO `engine4_core_content` (
//  `page_id` ,
//  `type` ,
//  `name` ,
//  `parent_content_id` ,
//  `order` ,
//  `params` ,
//  `attribs`
//  )
//  VALUES (
//  "' . $page_id . '", "widget", "sitestoreproduct.sitestoreproduct-products", "' . $temp_content_table['content_id'] . '", "' . $getSecondSmallestWidget['order'] . '", \'{"title":"Top Selling Products","titleCount":true,"statistics":"","viewType":"gridview","columnWidth":"180","popularity":"last_order_all","product_type":"all","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","ratingType":"rating_avg","columnHeight":"328","itemCount":"3","truncation":"16","nomobile":"0","name":"sitestoreproduct.sitestoreproduct-products"}\', NULL
//  );
//  ');
//          }
//        }
//      }
    
// Insert default product in Communityad & Suggestion plugins.
$isSuggestionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("suggestion");
$isCommunityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled("communityad");

if (!empty($isSuggestionEnabled)) {
  // Make Table exist conditions.
  $notificationType = "sitestoreproduct_suggestion";
  $isExist = $db->query("SELECT * FROM `engine4_suggestion_module_settings` WHERE `notification_type` LIKE '" . $notificationType . "' LIMIT 1")->fetch();

  $isSuggModTable = $db->query("SHOW TABLES LIKE 'engine4_suggestion_module_settings'")->fetch();
  if (empty($isExist) && !empty($isSuggModTable)) {
    $tempReviewTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.titlesingular', 'Product');
    $tempReviewTitle = strtolower($tempReviewTitle);
    $getReviewTitle = @ucfirst($tempReviewTitle);
    $suggSettingId = array("default" => 1);
    $suggNotificationType = $notificationType;
    $suggNotificationBody = '{item:$subject} has suggested to you a {item:$object:' . $tempReviewTitle . '}.';
    $suggestionModuleTable = Engine_Api::_()->getItemTable('suggestion_modinfo');
    $suggestionModuleTableName = $suggestionModuleTable->info('name');

    // Insert Notification Type in notification table.
    $db->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type` , `module` , `body` , `is_request` ,`handler`) VALUES ('$suggNotificationType', 'suggestion', '$suggNotificationBody', 1, 'suggestion.widget.get-notify')");

    // Insert in Mail Template Table.
    $emailtemType = 'notify_' . $suggNotificationType;
    $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('$emailtemType', 'suggestion', '[suggestion_sender], [suggestion_entity], [email], [link]'
      );");

    // Show "Suggest to Friend" link on "Product Profile Business".
    $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name` , `module` , `label` , `plugin` ,`params`, `menu`, `enabled`, `custom`, `order`) VALUES ("sitestoreproduct_gutter_suggesttofriend", "suggestion", "Suggest to Friends", \'Sitestore_Plugin_Menus::showSitestoreproduct\', \'{"route":"suggest_to_friend_link","class":"buttonlink icon_review_friend_suggestion smoothbox", "type":"popup"}\', "sitestoreproduct_gutter", 1, 0, 999 )');
    // Insert in Language Files.
    $language1 = array('You have a ' . $getReviewTitle . ' suggestion');
    $language2 = array('View all ' . $getReviewTitle . ' suggestions');
    $language3 = array('This ' . $tempReviewTitle . ' was suggested by');

    $temprequestWidgetLan = "sitestoreproduct suggestion";
    $requestTab = array(
        "%s " . $temprequestWidgetLan => array("%s " . strtolower($getReviewTitle) . " suggestion", "%s " . strtolower($getReviewTitle) . " suggestions")
    );


    $languageModTitle = "SITESTOREPRODUCT";
    $makeEmailArray = array(
        "_EMAIL_NOTIFY_" . $languageModTitle . "_SUGGESTION_TITLE" => $getReviewTitle . " Suggestion",
        "_EMAIL_NOTIFY_" . $languageModTitle . "_SUGGESTION_DESCRIPTION" => "This email is sent to the member when someone suggest a " . $getReviewTitle . '.',
        "_EMAIL_NOTIFY_" . $languageModTitle . "_SUGGESTION_SUBJECT" => $getReviewTitle . " Suggestion",
        "_EMAIL_NOTIFY_" . $languageModTitle . "_SUGGESTION_BODY" => "[header]

      [sender_title] has suggested to you a " . $getReviewTitle . ". To view this suggestion please click on: <a href='http://[host][object_link]'>http://[host][object_link]</a>.

      [footer]"
    );
    $userSettingsNotfication = array("ACTIVITY_TYPE_" . $languageModTitle . "_SUGGESTION" => "When I receive a " . strtolower($getReviewTitle) . " suggestion.");
    $userNotification = array($notificationLanguage => $notificationLanguage);

    $this->addPhraseAction($makeEmailArray);
    $this->addPhraseAction($userSettingsNotfication);
    $this->addPhraseAction($userNotification);

    $this->addPhraseAction($language1);
    $this->addPhraseAction($language2);
    $this->addPhraseAction($language3);
    $this->addPhraseAction($requestTab);

    // Insert in Suggestion modules tables.
    $row = $suggestionModuleTable->createRow();
    $row->module = "sitestoreproduct";
    $row->item_type = "sitestoreproduct_product";
    $row->field_name = "product_id";
    $row->owner_field = "owner_id";
    $row->item_title = $getReviewTitle;
    $row->button_title = "View this " . @ucfirst($tempReviewTitle);
    $row->enabled = "1";
    $row->notification_type = $suggNotificationType;
    $row->quality = "1";
    $row->link = "1";
    $row->popup = "1";
    $row->recommendation = "1";
    $row->default = "1";
    $row->settings = @serialize($suggSettingId);
    $row->save();
  }
}

if (!empty($isCommunityadEnabled)) {
  $communityadModuleTable = Engine_Api::_()->getDbTable('modules', 'communityad');
  $communityadModuleTableName = $communityadModuleTable->info('name');
  
  $isAdsExist = $db->query("SELECT * FROM `engine4_communityad_modules` WHERE `table_name` LIKE 'sitestore' LIMIT 1")->fetch();
  if (empty($isAdsExist)) {
      $db->query("INSERT IGNORE INTO `engine4_communityad_modules` (`module_name`, `module_title`, `table_name`, `title_field`, `body_field`, `owner_field`, `is_delete`) VALUES
  ('sitestore', 'Store', 'sitestore_store', 'title', 'body', 'owner_id', 1);");
  }
  
  
  $isAdsExist = $db->query("SELECT * FROM `engine4_communityad_modules` WHERE `table_name` LIKE 'sitestoreproduct' LIMIT 1")->fetch();
  if (empty($isAdsExist)) {
    $db->query("INSERT IGNORE INTO `engine4_communityad_modules` (`module_name`, `module_title`, `table_name`, `title_field`, `body_field`, `owner_field`, `is_delete`) VALUES
  ('sitestoreproduct', 'Product', 'sitestoreproduct_product', 'title', 'body', 'owner_id', 1);");
  }
}



$contentTable = Engine_Api::_()->getDbtable('content', 'core');
$contentTableName = $contentTable->info('name');



$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sitestoreproduct_index_pinboard')
        ->limit(1);

$info = $select->query()->fetch();

if (empty($info)) {
  $tempOrderId = 1;
  $db->insert('engine4_core_pages', array(
      'name' => 'sitestoreproduct_index_pinboard',
      'displayname' => 'Stores - Products Pinboard',
      'title' => 'Stores - Products Pinboard',
      'description' => 'This is the product pinboard page.',
      'custom' => 0
  ));
  $page_id = $db->lastInsertId('engine4_core_pages');

  //top containers
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'top',
      'order' => $tempOrderId++,
      'params' => '',
  ));
  $top_container_id = $db->lastInsertId('engine4_core_content');

  //containers
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'order' => $tempOrderId++,
      'params' => '',
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $top_container_id,
      'order' => $tempOrderId++,
      'params' => '',
  ));
  $top_middle_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => $tempOrderId++,
      'params' => '',
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  //middle column content
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.navigation-sitestoreproduct',
      'parent_content_id' => $top_middle_id,
      'order' => $tempOrderId++,
      'params' => '',
  ));
  
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.search-sitestoreproduct',
      'parent_content_id' => $middle_id,
      'order' => $tempOrderId++,
      'params' => '{"title":"","titleCount":true,"viewType":"horizontal","resultsAction":"pinboard","subcategoryFiltering":"1","priceFieldType":"slider","minPrice":"0","maxPrice":"999","currencySymbolPosition":"left","locationDetection":"0","whatWhereWithinmile":"0","advancedSearch":"0","nomobile":"0","name":"sitestoreproduct.search-sitestoreproduct"}',
  ));  

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.pinboard-browse',
      'parent_content_id' => $middle_id,
      'order' => $tempOrderId++,
      'params' => '{"title":"","statistics":["likeCount","reviewCount"],"show_buttons":["wishlist","compare","like","share","pinit","print"],"add_to_cart":"1","in_stock":"1","ratingType":"rating_avg","postedby":"1","autoload":"1","defaultLoadingImage":"1","itemWidth":"220","withoutStretch":"0","itemCount":"3","noOfTimes":"0","commentSection":"0","truncationDescription":"0","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitestoreproduct.pinboard-browse"}',
  ));
}



//Check if it's already been placed
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_pages')
        ->where('name = ?', 'sitestoreproduct_video_view')
        ->limit(1);

$info = $select->query()->fetch();

if (empty($info)) {
  $db->insert('engine4_core_pages', array(
      'name' => 'sitestoreproduct_video_view',
      'displayname' => 'Stores - Video View Page',
      'title' => 'Stores - Video Profile',
      'description' => 'This is the video view page.',
      'custom' => 0,
      'provides' => 'subject=sitestoreproduct',
  ));
  $page_id = $db->lastInsertId('engine4_core_pages');

  //containers
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'order' => 1,
      'params' => '',
  ));
  $container_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'right',
      'parent_content_id' => $container_id,
      'order' => 1,
      'params' => '',
  ));
  $right_id = $db->lastInsertId('engine4_core_content');

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $container_id,
      'order' => 3,
      'params' => '',
  ));
  $middle_id = $db->lastInsertId('engine4_core_content');

  //middle column content
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.video-content',
      'parent_content_id' => $middle_id,
      'order' => 1,
      'params' => '',
  ));

  //right column
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.show-same-tags',
      'parent_content_id' => $right_id,
      'order' => 1,
      'params' => '{"title":"Similar Videos","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.show-also-liked',
      'parent_content_id' => $right_id,
      'order' => 2,
      'params' => '{"title":"People Also Liked","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.show-same-poster',
      'parent_content_id' => $right_id,
      'order' => 3,
      'params' => '{"title":"Other Videos From Product","nomobile":"1"}',
  ));
}

//CREATE BROWSE, HOME AND PROFILE PAGE FOR THIS PRODUCT
Engine_Api::_()->getApi('productType', 'sitestoreproduct')->defaultCreation();

//WISHLIST PROFILE PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "sitestoreproduct_wishlist_profile")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {

  $containerCount = 0;
  $widgetCount = 0;

  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_wishlist_profile",
      'displayname' => 'Stores - Wishlist Profile',
      'title' => 'Stores - Wishlist Profile',
      'description' => 'This is the wishlist profile page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  //MAIN CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => $containerCount++,
  ));
  $main_container_id = $db->lastInsertId();

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
        'name' => 'sitestoreproduct.wishlist-profile-items',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '{"followLike":["follow","like"],"shareOptions":["siteShare","friend","report","print","socialShare"],"viewTypes":["list","pin"],"statistics":["likeCount","reviewCount"],"statisticsWishlist":["productCount","likeCount","viewCount","followCount"],"show_buttons":["wishlist","comment","like","share","facebook","pinit"],"itemWidth":235,"defaultWidgetNo":12}',
  ));
}

//WISHLIST HOME PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "sitestoreproduct_wishlist_browse")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {

  $containerCount = 0;
  $widgetCount = 0;

  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_wishlist_browse",
      'displayname' => 'Stores - Browse Wishlists',
      'title' => 'Stores - Browse Wishlists',
      'description' => 'This is the wishlist browse page.',
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
      'name' => 'sitestoreproduct.wishlist-browse-search',
      'parent_content_id' => $top_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.wishlist-creation-link',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.wishlist-products',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Friends\' Wishlists","type":"friends","statisticsWishlist":["productCount","followCount"],"nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.wishlist-products',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Wishlists with Most Products","orderby":"total_item","statisticsWishlist":["productCount","followCount"],"nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.wishlist-products',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Most Followed Wishlists","orderby":"follow_count","statisticsWishlist":["productCount","followCount"],"nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.wishlist-products',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Most Liked Wishlists","orderby":"like_count","statisticsWishlist":["likeCount","productCount"],"nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.wishlist-products',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Most Viewed Wishlists","orderby":"view_count","statisticsWishlist":["viewCount","productCount"],"nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.wishlist-browse',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '{"followLike":["follow","like"],"viewTypes":["list","grid"],"statisticsWishlist":["productCount","likeCount","viewCount","followCount"],"viewTypeDefault":"grid","listThumbsCount":"4","itemCount":"20"}',
  ));
}

//REVIEW PROFILE PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "sitestoreproduct_review_view")
        ->limit(1)
        ->query()
        ->fetchColumn();

//CREATE PAGE IF NOT EXIST
if (!$page_id) {

  $containerCount = 0;
  $widgetCount = 0;

  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_review_view",
      'displayname' => 'Stores - Review Profile',
      'title' => 'Stores - Review Profile',
      'description' => 'This is the review profile page.',
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
      'name' => 'seaocore.scroll-top',
      'parent_content_id' => $top_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.profile-review-breadcrumb-sitestoreproduct',
      'parent_content_id' => $top_middle_id,
      'order' => $widgetCount++,
      'params' => '{"nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.quick-specification-sitestoreproduct',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Quick Specifications","titleCount":true,"nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.socialshare-sitestoreproduct',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Social Share","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.related-products-view-sitestoreproduct',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Related Products","statistics":["likeCount","reviewCount"],"nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.ownerreviews-sitestoreproduct',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"statistics":["likeCount","replyCount","commentCount"],"nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.profile-review-sitestoreproduct',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"","titleCount":true,"loaded_by_ajax":"1","name":"sitestoreproduct.profile-review-sitestoreproduct"}',
  ));
}

//CATEGORIES HOME PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "sitestoreproduct_index_categories")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {

  $containerCount = 0;
  $widgetCount = 0;

  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_index_categories",
      'displayname' => 'Stores - Categories Home',
      'title' => 'Stores - Categories Home',
      'description' => 'This is the categories home page.',
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
  $main_left_id = $db->lastInsertId();  

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
      'name' => 'sitestoreproduct.producttypes-categories',
      'parent_content_id' => $main_left_id,
      'order' => $widgetCount++,
      'params' => '{"viewDisplayHR":"0","title":"All Categories","nomobile":"0","name":"sitestore.producttypes-categories"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.products-sitestoreproduct',
      'parent_content_id' => $main_left_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Most Liked Products","titleCount":true,"statistics":"","viewType":"gridview","columnWidth":"180","add_to_cart":"1","in_stock":"1","ratingType":"rating_avg","fea_spo":"","columnHeight":"328","popularity":"like_count","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","itemCount":"2","truncation":"32","nomobile":"0","name":"sitestore.products-sitestore"}'
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.recently-viewed-sitestoreproduct',
      'parent_content_id' => $main_left_id,
      'order' => $widgetCount++,
      'params' => '{"title":"You Recently Viewed","titleCount":true,"statistics":"","ratingType":"rating_avg","fea_spo":"","add_to_cart":"1","in_stock":"1","show":"0","viewType":"gridview","columnWidth":"180","columnHeight":"328","truncation":"32","count":"2","nomobile":"0","name":"sitestore.recently-viewed-sitestore"}'
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.recently-viewed-sitestoreproduct',
      'parent_content_id' => $main_left_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Your Frined Recently Viewed","titleCount":true,"statistics":"","ratingType":"rating_avg","fea_spo":"","add_to_cart":"1","in_stock":"1","show":"1","viewType":"gridview","columnWidth":"180","columnHeight":"328","truncation":"32","count":"2","nomobile":"0","name":"sitestore.recently-viewed-sitestore"}'
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.categories-grid-view',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Categories","titleCount":true,"columnHeight":216,"columnWidth":234,"defaultWidgetNo":5}'
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.recently-popular-random-sitestoreproduct',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"","titleCount":"","statistics":["viewCount","likeCount","commentCount","reviewCount"],"layouts_views":["listZZZview","gridZZZview"],"ajaxTabs":["recent","mostZZZreviewed","mostZZZpopular","featured","sponsored","topZZZselling","newZZZarrival"],"recent_order":"7","reviews_order":"1","popular_order":"2","featured_order":"3","sponsored_order":"4","top_selling_order":"5","new_arrival_order":"6","columnWidth":"165","add_to_cart":"1","in_stock":"1","ratingType":"rating_avg","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","defaultOrder":"gridZZZview","columnHeight":"305","postedby":"1","limit":"12","truncationList":"600","truncationGrid":"32","nomobile":"0","name":"sitestore.recently-popular-random-sitestore","defaultWidgetNo":6}'
  ));
}

//MEMBER PROFILE PAGE WIDGETS
$page_id = $db->select()
        ->from('engine4_core_pages', array('page_id'))
        ->where('name =?', 'user_profile_index')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!empty($page_id)) {

  $tab_id = $db->select()
          ->from('engine4_core_content', array('content_id'))
          ->where('page_id =?', $page_id)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'core.container-tabs')
          ->limit(1)
          ->query()
          ->fetchColumn();

  if (!empty($tab_id)) {

    $content_id = $db->select()
            ->from('engine4_core_content', array('content_id'))
            ->where('page_id =?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitestoreproduct.profile-sitestoreproduct')
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (empty($content_id)) {
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.profile-sitestoreproduct',
          'parent_content_id' => $tab_id,
          'order' => 999,
          'params' => '{"title":"Products","titleCount":"true","statistics":["viewCount","likeCount","commentCount","reviewCount"],"columnHeight":325,"columnWidth":165,"defaultWidgetNo":14}',
      ));
    }

    $content_id = $db->select()
            ->from('engine4_core_content', array('content_id'))
            ->where('page_id =?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitestoreproduct.editor-profile-reviews-sitestoreproduct')
            ->limit(1)
            ->query()
            ->fetchColumn();

    if (empty($content_id)) {

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.editor-profile-reviews-sitestoreproduct',
          'parent_content_id' => $tab_id,
          'order' => 999,
          'params' => '{"title":"Reviews As Editor","type":"editor"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitestoreproduct.editor-profile-reviews-sitestoreproduct',
          'parent_content_id' => $tab_id,
          'order' => 999,
          'params' => '{"title":"Reviews As User","type":"user"}',
      ));
    }
  }
}

//COMPARE PRODUCTS PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "sitestoreproduct_compare_compare")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (empty($page_id)) {

  $containerCount = 0;
  $widgetCount = 0;

  $db->insert('engine4_core_pages', array(
      'name' => 'sitestoreproduct_compare_compare',
      'displayname' => 'Stores - Products Comparison',
      'title' => 'Stores - Products Comparison',
      'description' => 'This is the products comparison page.',
      'custom' => 0
  ));
  $page_id = $db->lastInsertId('engine4_core_pages');

  //MAIN CONTAINER
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'order' => $containerCount++,
      'params' => '',
  ));
  $main_container_id = $db->lastInsertId('engine4_core_content');

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
      'name' => 'seaocore.scroll-top',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'core.content',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));
}

//REVIEW BROWSE PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "sitestoreproduct_review_browse")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (empty($page_id)) {

  $containerCount = 0;
  $widgetCount = 0;

  $db->insert('engine4_core_pages', array(
      'name' => 'sitestoreproduct_review_browse',
      'displayname' => 'Stores - Browse Reviews',
      'title' => 'Stores - Browse Reviews',
      'description' => 'This is the review browse page.',
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
      'name' => 'sitestoreproduct.navigation-sitestoreproduct',
      'parent_content_id' => $top_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'seaocore.scroll-top',
      'parent_content_id' => $top_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.review-of-the-day',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Review of the Day","titleCount":"true","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.review-browse-search',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.reviews-statistics',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Reviews Statistics","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'core.content',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));
}

//EDITOR HOME PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "sitestoreproduct_editor_home")
        ->limit(1)
        ->query()
        ->fetchColumn();

//CREATE PAGE IF NOT EXIST
if (!$page_id) {

  $containerCount = 0;
  $widgetCount = 0;

  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_editor_home",
      'displayname' => 'Stores - Editors Home',
      'title' => 'Stores - Editors Home',
      'description' => 'This is the editors home page.',
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

  //RIGHT CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_container_id,
      'order' => $containerCount++,
  ));
  $right_container_id = $db->lastInsertId();

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
      'name' => 'sitestoreproduct.popular-reviews-sitestoreproduct',
      'parent_content_id' => $left_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Most Recent Reviews","groupby":"0","type":"editor","popularity":"review_id","titleCount":"true","itemCount":"5","statistics":"","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.popular-reviews-sitestoreproduct',
      'parent_content_id' => $left_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Most Viewed Reviews","groupby":"0","type":"editor","popularity":"view_count","titleCount":"true","itemCount":"5","statistics":"","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.editor-featured-sitestoreproduct',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Featured Editor","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.editors-home-statistics-sitestoreproduct',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Statistics","titleCount":"true","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.top-reviewers-sitestoreproduct',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Top Reviewers","type":"editor","titleCount":"true","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.editors-home',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Review Editors","titleCount":"true"}',
  ));
}

//EDITOR PROFILE PAGE
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', "sitestoreproduct_editor_profile")
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {

  $containerCount = 0;
  $widgetCount = 0;

  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_editor_profile",
      'displayname' => 'Stores - Editor Profile',
      'title' => 'Stores - Editor Profile',
      'description' => 'This is the editor profile page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

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
      'name' => 'left',
      'parent_content_id' => $main_container_id,
      'order' => $containerCount++,
      'params' => '',
  ));
  $left_container_id = $db->lastInsertId('engine4_core_content');

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
      'name' => 'sitestoreproduct.editor-photo-sitestoreproduct',
      'parent_content_id' => $left_container_id,
      'order' => $widgetCount++,
      'params' => '',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.editor-profile-info',
      'parent_content_id' => $left_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"About Editor","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.editor-profile-statistics',
      'parent_content_id' => $left_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Statistics","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.socialshare-sitestoreproduct',
      'parent_content_id' => $left_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Social Share","nomobile":"1"}',
  ));

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
      'name' => 'sitestoreproduct.editor-profile-title',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
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
      'name' => 'sitestoreproduct.editor-profile-reviews-sitestoreproduct',
      'parent_content_id' => $tab_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Reviews As Editor","type":"editor"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.editor-profile-reviews-sitestoreproduct',
      'parent_content_id' => $tab_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Reviews As User","type":"user"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.editor-replies-sitestoreproduct',
      'parent_content_id' => $tab_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Comments"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.editors-sitestoreproduct',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Similar Editors","nomobile":"1"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'core.content',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));
}


$db->query('
  
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES

("sitestoreproduct_admin_submain_general_tab", "sitestoreproduct", "Video Settings", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"settings","action":"show-video"}\', "sitestoreproduct_admin_submain", "", 1, 0, 1),

("sitestoreproduct_admin_reviewmain_general", "sitestoreproduct", "Review Settings", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"review"}\', "sitestoreproduct_admin_reviewmain", "", 1, 0, 1),

("sitestoreproduct_admin_reviewmain_manage", "sitestoreproduct", "Manage Reviews & Ratings", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"review", "action":"manage"}\', "sitestoreproduct_admin_reviewmain", "", 1, 0, 2),

("sitestoreproduct_admin_reviewmain_fields", "sitestoreproduct", "Review Profile Fields", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"fields-review"}\', "sitestoreproduct_admin_reviewmain", "", 1, 0, 3),

("sitestoreproduct_admin_reviewmain_profilemaps", "sitestoreproduct", "Category-Review Profile Fields Mapping", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"profilemaps-review","action":"manage"}\', "sitestoreproduct_admin_reviewmain", "", 1, 0, 4),

("sitestoreproduct_admin_reviewmain_ratingparams", "sitestoreproduct", "Rating Parameters", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"ratingparameters","action":"manage"}\', "sitestoreproduct_admin_reviewmain", "", 1, 0, 5),

("sitestoreproduct_admin_reviewmain_editors", "sitestoreproduct", "Editors", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"editors","action":"manage"}\', "sitestoreproduct_admin_reviewmain", "", 1, 0, 6),

("sitestoreproduct_admin_submain_manage_tab", "sitestoreproduct", "Manage Review Videos", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"video","action": "manage"}\', "sitestoreproduct_admin_submain", "", 1, 0, 2),

("sitestoreproduct_admin_submain_utilities_tab", "sitestoreproduct", "Review Video Utilities", "", \'{"route":"admin_default","module":"sitestoreproduct","controller":"video", "action": "utility"}\', "sitestoreproduct_admin_submain", "", 1, 0, 3)
 ');

$db->query("UPDATE `engine4_sitestoreproduct_categories` SET `apply_compare` = '1' WHERE `engine4_sitestoreproduct_categories`.`cat_dependency` = 0");

$db->query("UPDATE `engine4_activity_actiontypes` SET `enabled` = '0' WHERE `engine4_activity_actiontypes`.`type` = 'video_sitestoreproduct' ");

$selectPage = $pageTable->select()
        ->from($pageTableName, array('page_id'))
        ->where('name =?', 'sitestoreproduct_product_cart')
        ->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (empty($page_id)) {

  $containerCount = 0;
  $widgetCount = 0;

  //CREATE PAGE
  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_product_cart",
      'displayname' => 'Stores - Manage Cart',
      'title' => 'Stores - Manage Cart',
      'description' => 'This is the store manage cart page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  //INSERT TOP CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => $containerCount++,
  ));
  $top_container_id = $db->lastInsertId();

  //INSERT MAIN CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => $containerCount++,
  ));
  $main_container_id = $db->lastInsertId();

  //INSERT TOP- MIDDLE CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_container_id,
      'order' => $containerCount++,
  ));
  $top_middle_id = $db->lastInsertId();

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

  //INSERT NAVIGATION WIDGET
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.navigation-sitestoreproduct',
      'parent_content_id' => $top_middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"","titleCount":true}',
  ));

  //INSERT "MANAGE CART" WIDGET
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.manage-cart',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));

  //INSERT "MANAGE CART" WIDGET
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.products-sitestoreproduct',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Most Viewed Products","titleCount":true,"statistics":"","viewType":"listview","columnWidth":"180","add_to_cart":"1","in_stock":"1","ratingType":"rating_avg","fea_spo":"","columnHeight":"328","popularity":"view_count","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","itemCount":"3","truncation":"16","nomobile":"0","name":"sitestore.products-sitestore"}',
  ));
}

$selectPage = $pageTable->select()
        ->from($pageTableName, array('page_id'))
        ->where('name =?', 'sitestoreproduct_index_checkout')
        ->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (empty($page_id)) {

  $containerCount = 0;
  $widgetCount = 0;

  //CREATE PAGE
  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_index_checkout",
      'displayname' => 'Stores - Checkout',
      'title' => 'Stores - Checkout',
      'description' => 'This is the store checkout page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  //INSERT TOP CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => $containerCount++,
  ));
  $top_container_id = $db->lastInsertId();

  //INSERT MAIN CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => $containerCount++,
  ));
  $main_container_id = $db->lastInsertId();

  //INSERT TOP- MIDDLE CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_container_id,
      'order' => $containerCount++,
  ));
  $top_middle_id = $db->lastInsertId();

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

  //INSERT NAVIGATION WIDGET
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.navigation-sitestoreproduct',
      'parent_content_id' => $top_middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"","titleCount":true}',
  ));

  //INSERT "CHECKOUT PROCESS" WIDGET
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'core.content',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));

  //INSERT "CHECKOUT MAIN" WIDGET
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.checkout-process',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '',
  ));
}


$selectPage = $pageTable->select()
        ->from($pageTableName, array('page_id'))
        ->where('name =?', 'sitestoreproduct_product_store-dashboard')
        ->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (empty($page_id)) {

  $containerCount = 0;
  $widgetCount = 0;

  //CREATE PAGE
  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_product_store-dashboard",
      'displayname' => 'Stores - Store Statistics',
      'title' => 'Stores - Store Statistics',
      'description' => 'This is the store dashboard page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  //INSERT MAIN CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => $containerCount++,
  ));
  $main_container_id = $db->lastInsertId();

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

  //INSERT "STATISTICS BOX" WIDGET
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.statistics-box',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));

  //INSERT "LATEST ORDER" WIDGET
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.latest-orders',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '{"itemCount":"5","title":"Recent Orders"}',
  ));

  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.store-overview',
      'parent_content_id' => $right_container_id,
      'order' => $widgetCount++,
      'params' => '{"title":"Store Statistics"}',
  ));
}

//$selectPage = $pageTable->select()
//        ->from($pageTableName, array('page_id'))
//        ->where('name =?', 'sitestoreproduct_product_manage')
//        ->limit(1);
//$page_id = $selectPage->query()->fetchAll();
//if (empty($page_id)) {
//
//  $containerCount = 0;
//  $widgetCount = 0;
//
//  //CREATE PAGE
//  $db->insert('engine4_core_pages', array(
//      'name' => "sitestoreproduct_product_manage",
//      'displayname' => 'Stores - Manage Products',
//      'title' => 'Stores - Manage Products',
//      'description' => 'This is the store dashboard manage products page.',
//      'custom' => 0,
//  ));
//  $page_id = $db->lastInsertId();
//
//  //INSERT MAIN CONTAINER
//  $db->insert('engine4_core_content', array(
//      'type' => 'container',
//      'name' => 'main',
//      'page_id' => $page_id,
//      'order' => $containerCount++,
//  ));
//  $main_container_id = $db->lastInsertId();
//
//  //MAIN-MIDDLE CONTAINER
//  $db->insert('engine4_core_content', array(
//      'type' => 'container',
//      'name' => 'middle',
//      'page_id' => $page_id,
//      'parent_content_id' => $main_container_id,
//      'order' => $containerCount++,
//  ));
//  $main_middle_id = $db->lastInsertId();
//
//  //INSERT "STATISTICS BOX" WIDGET
//  $db->insert('engine4_core_content', array(
//      'page_id' => $page_id,
//      'type' => 'widget',
//      'name' => 'sitestoreproduct.store-profile-products',
//      'parent_content_id' => $main_middle_id,
//      'order' => $widgetCount++,
//      'params' => '{"title":"Products","layouts_views":["1","2"],"layouts_order":"2","statistics":["viewCount","likeCount","commentCount","reviewCount"],"columnWidth":"180","truncationGrid":"90","ratingType":"rating_avg","columnHeight":"328","add_to_cart":"1","in_stock":"1","orderby":"sponsored","itemCount":"10","truncation":"25","nomobile":"0","name":"sitestoreproduct.page-profile-products"}',
//  ));
//}


$selectPage = $pageTable->select()
        ->from($pageTableName, array('page_id'))
        ->where('name =?', 'sitestoreproduct_index_startup')
        ->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (empty($page_id)) {

  $containerCount = 0;
  $widgetCount = 0;

  //CREATE PAGE
  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_index_startup",
      'displayname' => 'Stores - Startup Page',
      'title' => 'Stores - Startup Page',
      'description' => 'This is the store startup page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  //INSERT MAIN CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => $containerCount++,
  ));
  $top_container_id = $db->lastInsertId();

  //INSERT MAIN CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => $containerCount++,
  ));
  $main_container_id = $db->lastInsertId();

  //MAIN-MIDDLE CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_container_id,
      'order' => $containerCount++,
  ));
  $main_middle_id = $db->lastInsertId();

  //MAIN-MIDDLE CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_container_id,
      'order' => $containerCount++,
  ));
  $top_middle_id = $db->lastInsertId();

  //INSERT "NAVIGATION" WIDGET
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.navigation-sitestoreproduct',
      'parent_content_id' => $top_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));

  //INSERT "STATISTICS BOX" WIDGET
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'core.content',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '',
  ));
}


// STORE GET STARTED PAGE
$selectPage = $pageTable->select()
        ->from($pageTableName, array('page_id'))
        ->where('name =?', 'sitestoreproduct_index_get-started')
        ->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (empty($page_id)) {

  $containerCount = 0;
  $widgetCount = 0;

  //CREATE PAGE
  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_index_get-started",
      'displayname' => 'Stores - Get Started',
      'title' => 'Stores - Get Started',
      'description' => 'This is the store startup page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  //INSERT MAIN CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => $containerCount++,
  ));
  $main_container_id = $db->lastInsertId();

  //MAIN-MIDDLE CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_container_id,
      'order' => $containerCount++,
  ));
  $main_middle_id = $db->lastInsertId();

  //INSERT "NAVIGATION" WIDGET
  $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'sitestoreproduct.store-startuppage',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"","titleCount":true,"page_id":"1","nomobile":"0","name":"sitestoreproduct.store-startuppage"}'
  ));
}

// STORE BASIC PAGE
$selectPage = $pageTable->select()
        ->from($pageTableName, array('page_id'))
        ->where('name =?', 'sitestoreproduct_index_basic')
        ->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (empty($page_id)) {

  $containerCount = 0;
  $widgetCount = 0;

  //CREATE PAGE
  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_index_basic",
      'displayname' => 'Stores - Basics',
      'title' => 'Stores - Basics',
      'description' => 'This is the store startup page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  //INSERT MAIN CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => $containerCount++,
  ));
  $main_container_id = $db->lastInsertId();

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
      'name' => 'sitestoreproduct.store-startuppage',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"","titleCount":true,"page_id":"2","nomobile":"0","name":"sitestoreproduct.store-startuppage"}'
  ));
}


// STORE STORIES PAGE
$selectPage = $pageTable->select()
        ->from($pageTableName, array('page_id'))
        ->where('name =?', 'sitestoreproduct_index_stories')
        ->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (empty($page_id)) {

  $containerCount = 0;
  $widgetCount = 0;

  //CREATE PAGE
  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_index_stories",
      'displayname' => 'Stores - Success Stories',
      'title' => 'Stores - Success Stories',
      'description' => 'This is the store startup page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  //INSERT MAIN CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => $containerCount++,
  ));
  $main_container_id = $db->lastInsertId();

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
      'name' => 'sitestoreproduct.store-startuppage',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"","titleCount":true,"page_id":"3","nomobile":"0","name":"sitestoreproduct.store-startuppage"}'
  ));
}


// STORE TOOLS PAGE
$selectPage = $pageTable->select()
        ->from($pageTableName, array('page_id'))
        ->where('name =?', 'sitestoreproduct_index_tools')
        ->limit(1);
$page_id = $selectPage->query()->fetchAll();
if (empty($page_id)) {

  $containerCount = 0;
  $widgetCount = 0;

  //CREATE PAGE
  $db->insert('engine4_core_pages', array(
      'name' => "sitestoreproduct_index_tools",
      'displayname' => 'Stores - Tools',
      'title' => 'Stores - Tools',
      'description' => 'This is the store startup page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();

  //INSERT MAIN CONTAINER
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => $containerCount++,
  ));
  $main_container_id = $db->lastInsertId();

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
      'name' => 'sitestoreproduct.store-startuppage',
      'parent_content_id' => $main_middle_id,
      'order' => $widgetCount++,
      'params' => '{"title":"","titleCount":true,"page_id":"4","nomobile":"0","name":"sitestoreproduct.store-startuppage"}'
  ));
}
// Work for advancedactivity feed plugin(feed widget place by default).
$aafModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
//Quary for update widget of seaocore activity feed.
if (!empty($aafModuleEnabled)) {
  $db->query("INSERT IGNORE INTO `engine4_advancedactivity_contents` ( `module_name`, `filter_type`, `resource_title`, `content_tab`, `order`, `default`) VALUES ('sitestoreproduct', 'sitestoreproduct', 'Store Products', '1', '999', '1')");
}
