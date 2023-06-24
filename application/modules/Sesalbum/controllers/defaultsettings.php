<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: defaultsettings.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
$db = Zend_Db_Table_Abstract::getDefaultAdapter();
//Member Profile Page
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_content')
        ->where('name = ?', 'album.profile-albums')
        ->where('type = ?', 'widget');
$infoContent = $select->query()->fetch();
if (!empty($infoContent)) {
  $params = array('title' => 'Albums', 'view_type' => 'masonry', 'insideOutside' => 'inside', 'fixHover' => 'fix','insideOutside_profileAlbums' =>'outside','fixHover_profileAlbums'=>'fix', 'show_criteria' => array('like', 'comment', 'rating', 'view', 'title', 'by', 'socialSharing', 'favouriteCount', 'photoCount','downloadCount', 'likeButton', 'favouriteButton'), 'limit_data' => 20, 'pagging' => 'auto_load', 'title_truncation' => '20', 'height' => 250, 'height_masonry' => 280, 'width' => 307, 'search_type' => array('taggedPhoto', 'photoofyou', 'profileAlbums'), 'taggedPhoto_order' => 3, 'photoofyou_order' => 1, 'profileAlbums_order' => 2,'name'=>'sesalbum.profile-albums');
  $db->update('engine4_core_content', array('params' => Zend_Json::encode($params), 'name' => 'sesalbum.profile-albums'), array('name=?' => 'album.profile-albums', 'type=?' => 'widget'));
}
//left widget member photos on member profile page
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_content','content_id')
        ->where('name = ?', 'left')
        ->where('page_id = ?', '5');
$infoId = $select->query()->fetchColumn();
if(!empty($infoId)){
$params = array('title' => '', 'view_type' => 'grid', 'insideOutside' => 'inside', 'fixHover' => 'hover','insideOutside_profileAlbums' =>'inside','fixHover_profileAlbums'=>   'fix', 'show_criteria' => array(), 'limit_data' => 16, 'pagging' => 'pagging', 'title_truncation' => '45', 'height' => 54, 'height_masonry' => 80, 'width' => 54,     'search_type' => array( 'photoofyou'), 'taggedPhoto_order' => 3, 'photoofyou_order' => 1,'show_limited_data'=>'yes', 'profileAlbums_order' => 2,'name'=>'sesalbum.profile-albums');
	$db->insert('engine4_core_content', array(
      'name' => 'sesalbum.profile-albums',
      'page_id' => 5,
			'type'=>'widget',
			'params' => Zend_Json::encode($params), 
      'parent_content_id' => $infoId,
      'order' => 5,
  ));
}
//change album_photo_new type of album for activity 	
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_activity_actiontypes')
        ->where('type = ?', 'album_photo_new');
$infoActivityContent = $select->query()->fetch();
if (!empty($infoActivityContent)) {
  $db->query('UPDATE engine4_activity_actiontypes set module = "sesalbum" WHERE type = "album_photo_new"');
}
//Album Manage Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesalbum_index_manage')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesalbum_index_manage',
      'displayname' => 'SES - Advanced Photos - Manage Albums Page',
      'title' => 'My Albums',
      'description' => 'This page lists a user\'s albums.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
      'order' => 6
  ));
  $top_middle_id = $db->lastInsertId();
  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6,
  ));
  $main_middle_id = $db->lastInsertId();

  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 3,
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.tabbed-manage-widget',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 7,
      'params' => '{"view_type":"masonry","insideOutside":"outside","fixHover":"fix","limit_data":"8","pagging":"auto_load","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"title_truncation":"20","height":"220","height_masonry":"250","width":"238","search_type":["ownalbum","likeAlbum","likePhoto","ratedAlbums","ratedPhotos","favouriteAlbums","favouritePhotos","featuredAlbums","featuredPhotos","sponsoredPhotos","sponsoredAlbums"],"dummy":null,"ownalbum_order":"1","ownalbum_label":"My Albums","dummy1":null,"likeAlbum_order":"2","likeAlbum_label":"Liked Albums","dummy2":null,"likePhoto_order":"3","likePhoto_label":"Liked Photos","dummy3":null,"ratedAlbums_order":"3","ratedAlbums_label":"Rated Albums","dummy4":null,"ratedPhotos_order":"5","ratedPhotos_label":"Rated Photos","dummy5":null,"favouriteAlbums_order":"4","favouriteAlbums_label":"Favourite Albums","dummy6":null,"favouritePhotos_order":"5","favouritePhotos_label":"Favourite Photos","dummy7":null,"featuredAlbums_order":"5","featuredAlbums_label":"Featured Albums","dummy8":null,"featuredPhotos_order":"6","featuredPhotos_label":"Featured Photos","dummy9":null,"sponsoredAlbums_order":"7","sponsoredAlbums_label":"Sponsored Albums","dummy10":null,"sponsoredPhotos_order":"8","sponsoredPhotos_label":"Sponsored Photos","title":"","nomobile":"0","name":"sesalbum.tabbed-manage-widget"}'
  ));
  // Insert search
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-search',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 4,
      'params' => '{"search_for":"album","view_type":"horizontal","search_type":["recentlySPcreated","mostSPviewed","mostSPliked","mostSPcommented","mostSPrated","mostSPfavourite","featured","sponsored"],"default_search_type":"recentlySPcreated","friend_show":"yes","search_title":"yes","browse_by":"yes","categories":"yes","location":"yes","kilometer_miles":"yes","title":"","nomobile":"0","name":"sesalbum.browse-search"}'
  ));
}
//Album Photo View Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesalbum_photo_view')
        ->limit(1)
        ->query()
        ->fetchColumn();

if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesalbum_photo_view',
      'displayname' => 'SES - Advanced Photos - Photo View Page',
      'title' => 'Album Photo View Page',
      'description' => 'This page displays an album\'s photo.',
      'provides' => 'subject=album_photo',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2
  ));
  $main_id = $db->lastInsertId();
  // Insert middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6,
  ));
  $middle_id = $db->lastInsertId();
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.breadcrumb-photo-view',
      'page_id' => $page_id,
      'parent_content_id' => $middle_id,
      'order' => 3,
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.photo-view-page',
      'page_id' => $page_id,
      'parent_content_id' => $middle_id,
      'order' => 4,
      'params' => '{"criteria":["like","favourite","tagged","slideshowPhoto"],"maxHeight":"550","view_more_like":"17","view_more_favourite":"10","view_more_tagged":"10","title":"","nomobile":"0","name":"sesalbum.photo-view-page"}'
  ));
}
//Album View Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesalbum_album_view')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesalbum_album_view',
      'displayname' => 'SES - Advanced Photos - Album View Page',
      'title' => 'Album View Page',
      'description' => 'This page displays an album.',
      'provides' => 'subject=album',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6,
  ));
  $main_middle_id = $db->lastInsertId();
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.breadcrumb-album-view',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 3,
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.album-view-page',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 4,
      'params' => '{"view_type":"masonry","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","featured","sponsored","likeButton","favouriteButton"],"limit_data":"20","pagging":"auto_load","title_truncation":"20","height":"350","width":"250","dummy1":null,"insideOutsideRelated":"outside","fixHoverRelated":"fix","show_criteriaRelated":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","featured","sponsored","likeButton","favouriteButton"],"limit_dataRelated":"15","paggingRelated":"auto_load","title_truncationRelated":"45","heightRelated":"240","widthRelated":"294","dummy2":null,"search_type":["RecentAlbum","Like","TaggedUser","Fav"],"dummy":null,"RecentAlbum_order":"1","RecentAlbum_label":"[USER_NAME]\'s Recent Albums","RecentAlbum_limitdata":"17","dummy4":null,"Like_order":"2","Like_label":"People Who Like This","Like_limitdata":"17","dummy5":null,"TaggedUser_order":"3","TaggedUser_label":"People Who Are Tagged In This Album","TaggedUser_limitdata":"17","dummy6":null,"Fav_order":"4","Fav_label":"People Who Added This As Favourite","Fav_limitdata":"17","title":"","nomobile":"0","name":"sesalbum.album-view-page"}'
  ));
}
//Photo Browse Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesalbum_index_browse-photo')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesalbum_index_browse-photo',
      'displayname' => 'SES - Advanced Photos - Browse Photos Page',
      'title' => 'Browse Photos Page',
      'description' => 'This page is the browse photos page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
      'order' => 6,
  ));
  $top_middle_id = $db->lastInsertId();
  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6,
  ));
  $main_middle_id = $db->lastInsertId();
  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 3,
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-search',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 4,
      'params' => '{"search_type":["recentlySPcreated","mostSPviewed","mostSPliked","mostSPcommented","mostSPrated","mostSPfavourite","featured","sponsored"],"search_for":"photo","view_type":"horizontal","title":"","nomobile":"0","name":"sesalbum.browse-search"}'
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.album-home-error',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 9,
      'params' => '{"itemType":"photo","title":"","nomobile":"0","name":"sesalbum.album-home-error"}'
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.tabbed-widget',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 7,
      'params' => '{"photo_album":"photo","tab_option":"filter","view_type":"masonry","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"limit_data":"50","show_limited_data":"no","pagging":"auto_load","title_truncation":"40","height":"350","width":"400","search_type":["mostSPliked"],"dummy1":null,"recentlySPcreated_order":"1","recentlySPcreated_label":"Recently Created","dummy2":null,"mostSPviewed_order":"2","mostSPviewed_label":"Most Viewed","dummy3":null,"mostSPfavourite_order":"2","mostSPfavourite_label":"Most Favourite","dummy4":null,"mostSPdownloaded_order":"2","mostSPdownloaded_label":"Most Downloaded","dummy5":null,"mostSPliked_order":"3","mostSPliked_label":"Most Liked","dummy6":null,"mostSPcommented_order":"4","mostSPcommented_label":"Most Commented","dummy7":null,"mostSPrated_order":"5","mostSPrated_label":"Most Rated","dummy8":null,"featured_order":"6","featured_label":"Featured","dummy9":null,"sponsored_order":"7","sponsored_label":"Sponsored","title":"","nomobile":"0","name":"sesalbum.tabbed-widget"}'
  ));
}
//Photo home page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesalbum_index_photo-home')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesalbum_index_photo-home',
      'displayname' => 'SES - Advanced Photos - Photos Home Page',
      'title' => 'Photos Home Page',
      'description' => 'This page is the photos home page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1
  ));
  $top_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2
  ));
  $main_id = $db->lastInsertId();
  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
      'order' => 6
  ));
  $top_middle_id = $db->lastInsertId();
  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6
  ));
  $main_middle_id = $db->lastInsertId();
  // Insert main-right
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 5,
  ));
  $main_right_id = $db->lastInsertId();
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 3,
  ));
	$db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.slideshows',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 4,
			'params'=>'{"featured_sponsored_carosel":"1","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"height_container":"350","num_rows":"2","info":"recently_created","title_truncation":"45","limit_data":"37","title":"Featured Photos Slideshow","nomobile":"0","name":"sesalbum.slideshows"}'
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.album-home-error',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 9,
      'params' => '{"itemType":"photo","title":"","nomobile":"0","name":"sesalbum.album-home-error"}'
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.featured-sponsored-carosel',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 7,
      'params' => '{"featured_sponsored_carosel":"3","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"duration":"250","height":"250","width":"306","info":"most_liked","title_truncation":"18","limit_data":"10","aliganment_of_widget":"1","title":"Sponsored Photos","nomobile":"0","name":"sesalbum.featured-sponsored-carosel"}'
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.tabbed-widget',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 8,
      'params' => '{"photo_album":"photo","tab_option":"advance","view_type":"grid","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"limit_data":"12","show_limited_data":"no","pagging":"pagging","title_truncation":"45","height":"190","width":"228","search_type":["mostSPviewed","mostSPfavourite","mostSPliked","mostSPcommented","mostSPrated","mostSPdownloaded"],"dummy1":null,"recentlySPcreated_order":"7","recentlySPcreated_label":"Recently Created","dummy2":null,"mostSPviewed_order":"6","mostSPviewed_label":"Most Viewed","dummy3":null,"mostSPfavourite_order":"3","mostSPfavourite_label":"Most Favourite","dummy4":null,"mostSPdownloaded_order":"4","mostSPdownloaded_label":"Most Downloaded","dummy5":null,"mostSPliked_order":"2","mostSPliked_label":"Most Liked","dummy6":null,"mostSPcommented_order":"5","mostSPcommented_label":"Most Commented","dummy7":null,"mostSPrated_order":"1","mostSPrated_label":"Most Rated","dummy8":null,"featured_order":"6","featured_label":"Featured","dummy9":null,"sponsored_order":"7","sponsored_label":"Sponsored","title":"Popular Photos","nomobile":"0","name":"sesalbum.tabbed-widget"}'
  ));
	$db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 9,
      'params' => '{"tableName":"photo","criteria":"5","info":"recently_created","insideOutside":"inside","fixHover":"hover","show_criteria":["by","likeButton","favouriteButton"],"view_type":"1","title_truncation":"20","height":"111","width":"111","limit_data":"32","title":"Most Recent Photos","nomobile":"0","name":"sesalbum.featured-sponsored"}'
  ));
	//Insert offthe day albums
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' =>'sesalbum.of-the-day',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => 11,
      'params' => '{"ofTheDayType":"photos","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"title_truncation":"20","height":"262","width":"235","title":"Photo Of The Day","nomobile":"0","name":"sesalbum.of-the-day"}'
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-search',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => 12,
      'params' => '{"search_for":"photo","view_type":"vertical","search_type":["recentlySPcreated","mostSPviewed","mostSPliked","mostSPcommented","mostSPrated","mostSPfavourite","featured","sponsored"],"friend_show":"no","search_title":"yes","browse_by":"yes","categories":"yes","location":"yes","kilometer_miles":"no","title":"Search Photos","nomobile":"0","name":"sesalbum.browse-search"}'
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => 13,
      'params' => '{"tableName":"photo","criteria":"5","info":"recently_created","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"view_type":"1","title_truncation":"18","height":"219","width":"200","limit_data":"2","title":"Top Rated Photos","nomobile":"0","name":"sesalbum.featured-sponsored"}'
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => 14,
      'params' => '{"category":"photo","criteria":"on_site","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"title_truncation":"18","height":"180","width":"180","limit_data":"2","title":"Recently Viewed Photos","nomobile":"0","name":"sesalbum.recently-viewed-item"}'
  ));
}
//Album Create Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesalbum_index_create')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesalbum_index_create',
      'displayname' => 'SES - Advanced Photos - Album Create Page',
      'title' => 'Add New Photos',
      'description' => 'This page is the album create page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
// Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
      'order' => 6
  ));
  $top_middle_id = $db->lastInsertId();
  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6
  ));
  $main_middle_id = $db->lastInsertId();
  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 3,
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'core.content',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 4,
  ));
}
//Album Tag Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesalbum_index_tags')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesalbum_index_tags',
      'displayname' => 'SES - Advanced Photos - Browse Tags Page',
      'title' => 'Album Browse Tags Page',
      'description' => 'This page is the browse albums tag page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
      'order' => 6
  ));
  $top_middle_id = $db->lastInsertId();
  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6
  ));
  $main_middle_id = $db->lastInsertId();
  // Insert main-right
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 7,
  ));
  $main_right_id = $db->lastInsertId();
  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 3,
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.tag-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 4,
  ));
}
//Album welcome page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesalbum_index_welcome')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesalbum_index_welcome',
      'displayname' => 'SES - Advanced Photos - Albums Welcome Page',
      'title' => 'Albums Welcome Page',
      'description' => 'This page is the albums welcome page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
      'order' => 6
  ));
  $top_middle_id = $db->lastInsertId();
  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6
  ));
  $main_middle_id = $db->lastInsertId();
  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 3,
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.welcome',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 4,
      'params' => '{"criteria_slide":"allincludedfeaturedsponsored","slide_to_show":"most_liked","height_slideshow":"480","limit_data_slide":"8","slide_title":"Share your Stories with Photos!","slide_descrition":"Let your photos do the talking for you. After all, they\'re worth a million words.","enable_search":"yes","search_criteria":"photos","show_album_under":"yes","show_statistics":"yes","criteria_slide_album":"sponsored","album_criteria":"most_liked","limit_data_album":"3","title_truncation":"45","title":"","nomobile":"0","name":"sesalbum.welcome"}',
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.album-home-error',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 6,
      'params' => '{"itemType":"photo","title":"","nomobile":"0","name":"sesalbum.album-home-error"}'
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.tabbed-widget',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 7,
      'params' => '{"photo_album":"photo","tab_option":"filter","view_type":"masonry","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"limit_data":"12","show_limited_data":"yes","pagging":"pagging","title_truncation":"45","height":"500","width":"140","search_type":["featured"],"dummy1":null,"recentlySPcreated_order":"1","recentlySPcreated_label":"Recently Created","dummy2":null,"mostSPviewed_order":"2","mostSPviewed_label":"Most Viewed","dummy3":null,"mostSPfavourite_order":"2","mostSPfavourite_label":"Most Favourite","dummy4":null,"mostSPdownloaded_order":"2","mostSPdownloaded_label":"Most Downloaded","dummy5":null,"mostSPliked_order":"3","mostSPliked_label":"Most Liked","dummy6":null,"mostSPcommented_order":"4","mostSPcommented_label":"Most Commented","dummy7":null,"mostSPrated_order":"5","mostSPrated_label":"Most Rated","dummy8":null,"featured_order":"6","featured_label":"Featured","dummy9":null,"sponsored_order":"7","sponsored_label":"Sponsored","title":"","nomobile":"0","name":"sesalbum.tabbed-widget"}',
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesbasic.simple-html-block',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 5,
      'params' => '{"bodysimple":"<div class=\"sesalbum_welcome_html_block\">\r\n<h2>Upload your photos and share them with the World.<\/h2>\r\n<p>Share your photos with your family & friends in an effective way. Let the world know you!<\/p>\r\n<p><a href=\"javascript:;\" onclick=\"browsePhotoURL();returnfalse;\">Browse All Photos<\/a><\/p>\r\n<\/div>","show_content":"1","title":"","nomobile":"0","name":"sesbasic.simple-html-block"}',
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-categories',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 8,
      'params' => '{"type":"photo","show_category_has_count":"no","show_count":"no","allign":"1","limit_data":"12","title":"Browse More Photos by Categories","nomobile":"0","name":"sesalbum.browse-categories"}',
  ));
}
//Album Home Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesalbum_index_home')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesalbum_index_home',
      'displayname' => 'SES - Advanced Photos - Albums Home Page',
      'title' => 'Albums Home Page',
      'description' => 'This page is the albums home page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1
  ));
  $top_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2
  ));
  $main_id = $db->lastInsertId();
  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
      'order' => 6
  ));
  $top_middle_id = $db->lastInsertId();
  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6
  ));
  $main_middle_id = $db->lastInsertId();
  // Insert main-left
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'left',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 4,
  ));
  $main_left_id = $db->lastInsertId();
  // Insert main-right
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'right',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 5,
  ));
  $main_right_id = $db->lastInsertId();
  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 3
  ));
	$db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.featured-sponsored-carosel',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 4,
			'params'=>'{"featured_sponsored_carosel":"2","insideOutside":"inside","fixHover":"fix","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"duration":"300","height":"270","width":"293","info":"most_favourite","title_truncation":"20","limit_data":"20","aliganment_of_widget":"1","title":"Featured Albums","nomobile":"0","name":"sesalbum.featured-sponsored-carosel"}'
  ));
  //Insert popular albums widget
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.of-the-day',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => 7,
      'params' => '{"ofTheDayType":"albums","insideOutside":"outside","fixHover":"fix","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"title_truncation":"15","height":"250","width":"200","title":"Album Of The Day","nomobile":"0","name":"sesalbum.of-the-day"}'
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => 8,
      'params' => '{"tableName":"album","criteria":"5","info":"most_download","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"view_type":"2","title_truncation":"20","height":"160","width":"180","limit_data":"3","title":"Most Downloaded Albums","nomobile":"0","name":"sesalbum.featured-sponsored"}',
  ));
	$db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => 9,
      'params' => '{"tableName":"album","criteria":"5","info":"most_liked","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"view_type":"2","title_truncation":"20","height":"160","width":"180","limit_data":"3","title":"Most Liked Albums","nomobile":"0","name":"sesalbum.featured-sponsored"}',
  ));
	$db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_left_id,
      'order' => 10,
      'params' => '{"tableName":"album","criteria":"5","info":"recently_liked","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"view_type":"2","title_truncation":"20","height":"162","width":"180","limit_data":"3","title":"Most Viewed Albums","nomobile":"0","name":"sesalbum.featured-sponsored"}',
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-search',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 12,
      'params' => '{"search_for":"album","view_type":"horizontal","search_type":"","friend_show":"no","search_title":"yes","browse_by":"no","categories":"yes","location":"yes","kilometer_miles":"yes","title":"","nomobile":"0","name":"sesalbum.browse-search"}',
  ));
	$db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.album-category',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 13,
      'params' => '{"height":"110","width":"160","criteria":"admin_order","show_criteria":["title","icon"],"title":"Popular Categories","nomobile":"0","name":"sesalbum.album-category"}',
  ));
	$db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 14,
      'params' => '{"tableName":"album","criteria":"5","info":"most_rated","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"view_type":"1","title_truncation":"20","height":"200","width":"220","limit_data":"3","title":"Top Rated Albums","nomobile":"0","name":"sesalbum.featured-sponsored"}',
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.album-home-error',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 15,
      'params' => '{"itemType":"album","title":"","nomobile":"0","name":"sesalbum.album-home-error"}',
  ));
$db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.tabbed-widget',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 16,
      'params' => '{"photo_album":"album","tab_option":"default","view_type":"grid","insideOutside":"inside","fixHover":"fix","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"limit_data":"4","show_limited_data":"no","pagging":"pagging","title_truncation":"20","height":"200","width":"334","search_type":["recentlySPcreated"],"dummy1":null,"recentlySPcreated_order":"1","recentlySPcreated_label":"Recently Created","dummy2":null,"mostSPviewed_order":"2","mostSPviewed_label":"Most Viewed","dummy3":null,"mostSPfavourite_order":"2","mostSPfavourite_label":"Most Favourite","dummy4":null,"mostSPdownloaded_order":"2","mostSPdownloaded_label":"Most Downloaded","dummy5":null,"mostSPliked_order":"3","mostSPliked_label":"Most Liked","dummy6":null,"mostSPcommented_order":"4","mostSPcommented_label":"Most Commented","dummy7":null,"mostSPrated_order":"5","mostSPrated_label":"Most Rated","dummy8":null,"featured_order":"6","featured_label":"Featured","dummy9":null,"sponsored_order":"7","sponsored_label":"Sponsored","title":"Recent Albums","nomobile":"0","name":"sesalbum.tabbed-widget"}',
  ));
  // Insert slide show featured photo
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.tabbed-widget',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 17,
      'params' => '{"photo_album":"album","tab_option":"filter","view_type":"grid","insideOutside":"inside","fixHover":"fix","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","featured","sponsored","likeButton","favouriteButton"],"limit_data":"6","show_limited_data":"no","pagging":"button","title_truncation":"20","height":"210","width":"334","search_type":["mostSPfavourite","mostSPliked","mostSPrated","mostSPdownloaded","featured","sponsored"],"dummy1":null,"recentlySPcreated_order":"9","recentlySPcreated_label":"Recently Created","dummy2":null,"mostSPviewed_order":"8","mostSPviewed_label":"Most Viewed","dummy3":null,"mostSPfavourite_order":"6","mostSPfavourite_label":"Most Favourite","dummy4":null,"mostSPdownloaded_order":"5","mostSPdownloaded_label":"Most Downloaded","dummy5":null,"mostSPliked_order":"4","mostSPliked_label":"Most Liked","dummy6":null,"mostSPcommented_order":"7","mostSPcommented_label":"Most Commented","dummy7":null,"mostSPrated_order":"3","mostSPrated_label":"Most Rated","dummy8":null,"featured_order":"1","featured_label":"Featured","dummy9":null,"sponsored_order":"2","sponsored_label":"Sponsored","title":"Popular Albums","nomobile":"0","name":"sesalbum.tabbed-widget"}'
  ));
  // Insert categories
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.featured-sponsored-carosel',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => 19,
      'params' => '{"featured_sponsored_carosel":"4","insideOutside":"inside","fixHover":"fix","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"duration":"250","height":"275","width":"200","info":"recently_created","title_truncation":"18","limit_data":"10","aliganment_of_widget":"2","title":"Sponsored Albums","nomobile":"0","name":"sesalbum.featured-sponsored-carosel"}'
  ));
  // Insert search
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.tag-cloud-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => 20,
			'params'=>'{"color":"#000000","text_height":"15","height":"150","itemCountPerPage":"25","title":"Popular Tags","nomobile":"0","name":"sesalbum.tag-cloud-albums"}'
  ));
  // Insert search
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => 21,
      'params' => '{"tableName":"album","criteria":"5","info":"most_favourite","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"view_type":"2","title_truncation":"20","height":"155","width":"180","limit_data":"3","title":"Most Favourite Album","nomobile":"0","name":"sesalbum.featured-sponsored"}'
  ));
  // Insert browse menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.featured-sponsored',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => 22,
      'params' => '{"tableName":"album","criteria":"5","info":"most_commented","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"view_type":"2","title_truncation":"20","height":"151","width":"180","limit_data":"3","title":"Most Commented Albums","nomobile":"0","name":"sesalbum.featured-sponsored"}',
  ));
	$db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.recently-viewed-item',
      'page_id' => $page_id,
      'parent_content_id' => $main_right_id,
      'order' => 23,
      'params' => '{"category":"album","criteria":"on_site","insideOutside":"inside","fixHover":"hover","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","likeButton","favouriteButton"],"title_truncation":"20","height":"150","width":"180","limit_data":"2","title":"Recently Viewed Albums","nomobile":"0","name":"sesalbum.recently-viewed-item"}',
  ));
}
//Album Category Browse Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesalbum_category_browse')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesalbum_category_browse',
      'displayname' => 'SES - Advanced Photos - Browse Categories Page',
      'title' => 'Browse Categories Page',
      'description' => 'This page is the browse albums categories page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
      'order' => 6
  ));
  $top_middle_id = $db->lastInsertId();
  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6
  ));
  $main_middle_id = $db->lastInsertId();
  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 3,
  ));
  $PathFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Sesalbum' . DIRECTORY_SEPARATOR . "externals" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR;
  if (is_file($PathFile . "banners" . DIRECTORY_SEPARATOR . 'category_banner.jpg')) {
    if (!file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/admin')) {
      mkdir(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/admin', 0777, true);
    }
    copy($PathFile . "banners" . DIRECTORY_SEPARATOR . 'category_banner.jpg', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/admin/category_banner.jpg');
    $category_banner = 'public/admin/category_banner.jpg';
  } else {
    $category_banner = '';
  }
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.banner-category',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 6,
      'params' => '{"description":"Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\r\n\r\n Pellentesque lacinia hendrerit leo, nec hendrerit magna porttitor at. Vestibulum pellentesque erat orci, non mollis purus ornare a. Ut a blandit dolor. Quisque ac pharetra ex. Aliquam pretium pharetra elementum. Phasellus nec mollis metus, non pellentesque purus. Vivamus in sem facilisis, dictum ex suscipit, imperdiet tortor. Sed varius massa ex, quis porta elit interdum non. Mauris at dictum nisi. Maecenas malesuada diam sit amet turpis porttitor, ut aliquam nibh facilisis. Ut sit amet ligula lacus.\r\n\r\nIn hac habitasse platea dictumst. Cras mollis sagittis feugiat. Nunc ac velit eu turpis congue lobortis. Pellentesque quam diam, feugiat vitae ipsum sit amet, aliquet vestibulum ligula. Sed nulla risus, malesuada blandit egestas vel, semper a risus. Pellentesque et tincidunt mauris. Nunc sodales diam dictum, sollicitudin leo nec, dapibus sapien. Suspendisse a fringilla urna. Quisque luctus neque tristique, cursus nulla ac, egestas felis. Proin dapibus condimentum posuere. Aenean lacinia volutpat convallis. In gravida, elit eu imperdiet venenatis, lacus risus venenatis quam, at consectetur tortor tortor malesuada enim. Nam hendrerit ipsum vel odio molestie rutrum. Vivamus vitae risus eget est vehicula consequat. In varius nec dolor eu aliquet. ","sesalbum_categorycover_photo":"' . $category_banner . '","title":"Categories","nomobile":"0","name":"sesalbum.banner-category"}'
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.album-category',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 7,
      'params' => '{"height":"155","width":"250","allign_content":"center","criteria":"admin_order","show_criteria":["title","icon","countAlbums"],"title":"All Categories","nomobile":"0","name":"sesalbum.album-category"}'
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.category-associate-album',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 8,
      'params' => '{"show_criteria":["like","comment","rating","view","title","description","favourite","by","featuredLabel","sponsoredLabel","albumPhoto","photoCounts","photoThumbnail","albumCount"],"popularity_album":"most_liked","pagging":"auto_load","count_album":"0","criteria":"most_album","category_limit":"5","album_limit":"10","photo_limit":"22","seemore_text":"+ See All [category_name]","allignment_seeall":"left","title_truncation":"150","description_truncation":"120","height":"80","width":"80","title":"","nomobile":"0","name":"sesalbum.category-associate-album"}'
  ));
}
//Album Category View Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesalbum_category_index')
        ->limit(1)
        ->query()
        ->fetchColumn();
if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesalbum_category_index',
      'displayname' => 'SES - Advanced Photos - Category View Page',
      'title' => 'Category View Page',
      'description' => 'This page is the category view page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6
  ));
  $main_middle_id = $db->lastInsertId();
  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 3,
  ));
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.category-view',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 4,
      'params' => '{"show_subcat":"1","show_subcatcriteria":["title","icon","countAlbums"],"heightSubcat":"170","widthSubcat":"355","dummy1":null,"show_criteria":["featuredLabel","sponsoredLabel","like","comment","rating","view","title","by","favourite","photo"],"pagging":"button","album_limit":"19","height":"260","width":"400","title":"","nomobile":"0","name":"sesalbum.category-view"}'
  ));
}
//Album Browse Page
$page_id = $db->select()
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'sesalbum_index_browse')
        ->limit(1)
        ->query()
        ->fetchColumn();
// insert if it doesn't exist yet
if (!$page_id) {
  // Insert page
  $db->insert('engine4_core_pages', array(
      'name' => 'sesalbum_index_browse',
      'displayname' => 'SES - Advanced Photos - Browse Albums Page',
      'title' => 'Browse Albums Page',
      'description' => 'This page is the browse albums page.',
      'custom' => 0,
  ));
  $page_id = $db->lastInsertId();
  // Insert top
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'top',
      'page_id' => $page_id,
      'order' => 1,
  ));
  $top_id = $db->lastInsertId();
  // Insert main
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'main',
      'page_id' => $page_id,
      'order' => 2,
  ));
  $main_id = $db->lastInsertId();
  // Insert top-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $top_id,
      'order' => 6
  ));
  $top_middle_id = $db->lastInsertId();
  // Insert main-middle
  $db->insert('engine4_core_content', array(
      'type' => 'container',
      'name' => 'middle',
      'page_id' => $page_id,
      'parent_content_id' => $main_id,
      'order' => 6
  ));
  $main_middle_id = $db->lastInsertId();
  // Insert menu
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-menu',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 3,
  ));
	// Insert search
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-search',
      'page_id' => $page_id,
      'parent_content_id' => $top_middle_id,
      'order' => 4,
      'params' => '{"search_for":"album","view_type":"horizontal","search_type":["recentlySPcreated","mostSPviewed","mostSPliked","mostSPcommented","mostSPrated","mostSPfavourite","featured","sponsored"],"default_search_type":"mostSPliked","friend_show":"yes","search_title":"yes","browse_by":"yes","categories":"yes","location":"yes","kilometer_miles":"yes","title":"","nomobile":"0","name":"sesalbum.browse-search"}'
  ));
  // Insert content
  $db->insert('engine4_core_content', array(
      'type' => 'widget',
      'name' => 'sesalbum.browse-albums',
      'page_id' => $page_id,
      'parent_content_id' => $main_middle_id,
      'order' => 7,
      'params' => '{"load_content":"auto_load","sort":"most_liked","view_type":"2","insideOutside":"inside","fixHover":"fix","show_criteria":["like","comment","rating","view","title","by","socialSharing","favouriteCount","downloadCount","photoCount","featured","sponsored","likeButton","favouriteButton"],"title_truncation":"30","limit_data":"21","height":"240","width":"395","title":"","nomobile":"0","name":"sesalbum.browse-albums"}'
  ));
}
$db->query("UPDATE `engine4_core_menuitems` SET `label` = 'Custom Fields' WHERE `engine4_core_menuitems`.`name` = 'sesalbum_admin_main_subfields';");
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES ("sesalbum_admin_main_integrateothermodule", "sesalbum", "Integrate Plugins", "", \'{"route":"admin_default","module":"sesalbum","controller":"integrateothermodule","action":"index"}\', "sesalbum_admin_main", "", 995);');

$album_table_exist = $db->query('SHOW TABLES LIKE \'engine4_album_albums\'')->fetch();
if($album_table_exist) {
  $is_locked = $db->query('SHOW COLUMNS FROM engine4_users LIKE \'is_locked\'')->fetch();
  if (empty($is_locked)) {
    $db->query("ALTER TABLE `engine4_album_albums` ADD `is_locked` TINYINT(1) NOT NULL DEFAULT '0';");
  }
  $password = $db->query('SHOW COLUMNS FROM engine4_users LIKE \'password\'')->fetch();
  if (empty($password)) {
    $db->query("ALTER TABLE `engine4_album_albums` ADD `password` VARCHAR(255) NULL;");
  }
  $adult = $db->query('SHOW COLUMNS FROM engine4_users LIKE \'adult\'')->fetch();
  if (empty($adult)) {
    $db->query("ALTER TABLE  `engine4_album_albums` ADD  `adult` TINYINT( 1 ) NOT NULL DEFAULT  '0';");
  }
}
$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'album' as `type`,
    'locked' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");
$db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'album' as `type`,
    'sesalbum_locked' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');");
$db->query('ALTER TABLE `engine4_album_albums` ADD `draft` TINYINT(1) NOT NULL DEFAULT "0";');