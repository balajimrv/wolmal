<?php
class Sitestoreproduct_Api_Template extends Core_Api_Abstract {
  public function template1ProductProfile($templateType) {
    $this->_deleteExistingInformation();
    $this->_profilePageCreate($templateType);
  }
  
  private function _deleteExistingInformation(){
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', "sitestoreproduct_index_view")
            ->query()
            ->fetchColumn();
    
    $db->query('DELETE FROM `engine4_core_pages` WHERE `engine4_core_pages`.`page_id` = ' . $page_id . ' LIMIT 1');
    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = ' . $page_id . ' LIMIT 1');
    return;
  }
  
//PROFILE PAGE WORK
  private function _profilePageCreate($templateType) {

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
      
      if(!empty($templateType) && ($templateType == 'template2')) {
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.full-width-list-information-profile',
            'parent_content_id' => $top_middle_id,
            'order' => $widgetCount++,
            'params' => '{"showContent":["postedDate","postedBy","viewCount","likeCount","commentCount","photo","photosCarousel","tags","description"]}'
        ));
      }

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

      if(!empty($templateType) && ($templateType == 'default')) {
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitestoreproduct.list-information-profile',
            'parent_content_id' => $main_middle_id,
            'order' => $widgetCount++,
            'params' => '{"showContent":["postedDate","postedBy","viewCount","likeCount","commentCount","photo","photosCarousel","tags","description"]}'
        ));
      }

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
}
