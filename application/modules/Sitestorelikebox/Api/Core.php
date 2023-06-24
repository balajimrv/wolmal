<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorelikebox_Api_Core extends Core_Api_Abstract {

	  /**
   * Check for package is enable or not and store admin check.
   *
   * @param object $sitestore
   * @param string $module
   * @param string $hasPackageEnable
   * @return bool $canDo
   */
  public function allowModule( $sitestore , $module , $hasPackageEnable ) {

    // CHECK FOR PACKAGE IS ENABLE OR NOT.
    if ( $hasPackageEnable ) {
      return (bool) Engine_Api::_()->sitestore()->allowPackageContent( $sitestore->package_id , 'modules' , $module ) ;
    }
    else {
      $levelModules = array ( "offer" => "sitestoreoffer" , "form" => "sitestoreform" , "invite" => "sitestoreinvite" , "sdcreate" => "sitestoredocument" , "sncreate" => "sitestorenote" , "splcreate" => "sitestorepoll" , "secreate" => "sitestoreevent" , "svcreate" => "sitestorevideo" , "spcreate" => "sitestorealbum" , "comment" => "sitestorediscussion" , "smcreate" => "sitestoremusic") ;
      $search_Key = array_search( $module , $levelModules ) ;
      return (bool) Engine_Api::_()->sitestore()->isStoreOwnerAllow( $sitestore , $search_Key ) ;
    }
  }

	  /**
   * Get the widget name and find the order of profile widget show on the store profile store.
   *
   * @return Array $widgetSettingsArray
   */
  public function getWidgteParams() {

		//GET THE STORE LAYOUT.
    $layout = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'sitestore.layoutcreate' ) ;

		//GET THE STORE LAYOUT TYPE.
    $layoutType = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'sitestore.layout.setting' ) ;

    $widgetArray = array ( 'activity.feed' , 'advancedactivity.home-feeds', 'seaocore.feed' , 'sitestore.info-sitestore' , 'sitestore.location-sitestore' , 'sitestore.discussion-sitestore' , 'sitestore.photos-sitestore' , 'sitestoreevent.profile-sitestoreevents' , 'sitestorepoll.profile-sitestorepolls' , 'sitestorenote.profile-sitestorenotes' , 'sitestoreoffer.profile-sitestoreoffers' , 'sitestorevideo.profile-sitestorevideos' , 'sitestoremusic.profile-sitestoremusic' , 'sitestoredocument.profile-sitestoredocuments' , 'sitestorereview.profile-sitestorereviews' ) ;

    $widgetSettingsArray = array () ;
    if ( empty( $layout ) ) {
      if ( !empty( $layoutType ) ) {
        $storesTable = Engine_Api::_()->getDbtable( 'pages' , 'core' ) ;
        $store_id = $storesTable->select()
                ->from( $storesTable->info( 'name' ) , array ( 'page_id' ) )
                ->where( "name = ?" , 'sitestore_index_view' )
                ->query()->fetchColumn() ;

        if ( !empty( $store_id ) ) {
          $contentTable = Engine_Api::_()->getDbtable( 'content' , 'core' ) ;
          $allWidget = $contentTable->select()
                  ->from( $contentTable->info( 'name' ) , array ( 'order' , 'params' , 'name' ) )
                  ->where( "type = ?" , 'widget' )
                  ->where( "page_id = ?" , $store_id ) ;
          $contentTable = $allWidget->query()->fetchAll() ;

          foreach ( $contentTable as $key => $values ) {
            if ( !in_array( $values['name'] , $widgetArray ) ) {
              continue ;
            }
            $getTitleArray = Zend_Json::decode( $values['params'] ) ;
            $getTitle = $values['name'] ;
            if ( !empty( $getTitleArray['title'] ) ) {
              $getTitle = $getTitleArray['title'] ;
            }
            $tempArray = array ( 'name' => $values['name'] , 'title' => $getTitle ) ;
            $widgetSettingsArray[$values['order']] = $tempArray ;
          }
          ksort( $widgetSettingsArray ) ;
          return $widgetSettingsArray ;
        }
      }
      else {
        $storesTable = Engine_Api::_()->getDbtable( 'pages' , 'core' ) ;
        $store_id = $storesTable->select()
                ->from( $storesTable->info( 'name' ) , array ( 'page_id' ) )
                ->where( "name = ?" , 'sitestore_index_view' )
                ->query()->fetchColumn() ;

        if ( !empty( $store_id ) ) {
          $contentTable = Engine_Api::_()->getDbtable( 'content' , 'core' ) ;
          $allWidget = $contentTable->select()
                  ->from( $contentTable->info( 'name' ) , array ( 'order' , 'params' , 'name' ) )
                  ->where( "type = ?" , 'widget' )
                  ->where( "page_id = ?" , $store_id ) ;
          $allWidget = $allWidget->query()->fetchAll() ;

          foreach ( $allWidget as $key => $values ) {
            if ( !in_array( $values['name'] , $widgetArray ) ) {
              continue ;
            }
            $getTitleArray = Zend_Json::decode( $values['params'] ) ;
            $getTitle = $values['name'] ;
            if ( !empty( $getTitleArray['title'] ) ) {
              $getTitle = $getTitleArray['title'] ;
            }
            $tempArray = array ( 'name' => $values['name'] , 'title' => $getTitle ) ;
            $widgetSettingsArray[$values['order']] = $tempArray ;
          }
          ksort( $widgetSettingsArray ) ;
          return $widgetSettingsArray ;
        }
      }
    }
    else {
      if ( !empty( $layoutType ) ) {
        
        $temp_store_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('store_id', 0);
        
        $contentstoresTable = Engine_Api::_()->getDbtable( 'contentstores' , 'sitestore' ) ;
        $contentstore_id = $contentstoresTable->select()
                ->from( $contentstoresTable->info( 'name' ) , array ( 'contentstore_id' ) )
                ->where( "name = ?" , 'sitestore_index_view' )
                ->where("store_id =?", $temp_store_id)
                ->query()->fetchColumn() ;

        if ( !empty( $contentstore_id ) ) {
          $sitestorecontentTable = Engine_Api::_()->getDbtable( 'content' , 'sitestore' ) ;
          $allWidget = $sitestorecontentTable->select()
                  ->from( $sitestorecontentTable->info( 'name' ) , array ( 'order' , 'params' , 'name' ) )
                  ->where( "type = ?" , 'widget' )
                  ->where( "contentstore_id = ?" , $contentstore_id ) ;
          $contentTable = $allWidget->query()->fetchAll() ;

          foreach ( $contentTable as $key => $values ) {
            if ( !in_array( $values['name'] , $widgetArray ) ) {
              continue ;
            }
            $getTitleArray = Zend_Json::decode( $values['params'] ) ;
            $getTitle = $values['name'] ;
            if ( !empty( $getTitleArray['title'] ) ) {
              $getTitle = $getTitleArray['title'] ;
            }
            $tempArray = array ( 'name' => $values['name'] , 'title' => $getTitle ) ;
            $widgetSettingsArray[$values['order']] = $tempArray ;
          }
          ksort( $widgetSettingsArray ) ;
          return $widgetSettingsArray ;
        }
      }
      else {
        $contentstoresTable = Engine_Api::_()->getDbtable( 'contentstores' , 'sitestore' ) ;
        $store_id = $contentstoresTable->select()
                ->from( $contentstoresTable->info( 'name' ) , array ( 'store_id' ) )
                ->where( "name = ?" , 'sitestore_index_view' )
                ->query()->fetchColumn() ;

        if ( !empty( $store_id ) ) {
          $sitestorecontentTable = Engine_Api::_()->getDbtable( 'content' , 'sitestore' ) ;
          $allWidget = $sitestorecontentTable->select()
                  ->from( $sitestorecontentTable->info( 'name' ) , array ( 'order' , 'params' , 'name' ) )
                  ->where( "type = ?" , 'widget' )
                  ->where( "contentstore_id = ?" , $store_id ) ;
          $allWidget = $allWidget->query()->fetchAll() ;

          foreach ( $allWidget as $key => $values ) {
            if ( !in_array( $values['name'] , $widgetArray ) ) {
              continue ;
            }
            $getTitleArray = Zend_Json::decode( $values['params'] ) ;
            $getTitle = $values['name'] ;
            if ( !empty( $getTitleArray['title'] ) ) {
              $getTitle = $getTitleArray['title'] ;
            }
            $tempArray = array ( 'name' => $values['name'] , 'title' => $getTitle ) ;
            $widgetSettingsArray[$values['order']] = $tempArray ;
          }
          ksort( $widgetSettingsArray ) ;
          return $widgetSettingsArray ;
        }
      }
    }
	}
}
?>