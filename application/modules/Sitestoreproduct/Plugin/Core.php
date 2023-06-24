<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function onRenderLayoutDefault($event) {
    $view = $event->getPayload();
    
    $view->headTranslate(array("0 items", "Checkout", "View Cart", "Your cart is empty", "Hide Compare Bar", "Compare", "Show Compare Bar", "Compare All", "Remove All", "Please select more than one product for the comparison." ,"Are you sure you want to delete this?", "Choose Source", "My Computer", "To upload a video from your computer, please use our full uploader.", "Attach", " hrs", "Your Shopping Cart is empty.", "Loading...", " days ", "-- Please Select --", "This shipping method cannot be enabled disabled from here. Please try enable/disable by editing the shipping method."));    
    $view->headScript()
      ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/sitestoreproduct_zoom.js');
    
    $isSitethemeEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetheme');
    $isSitemenuEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemenu');

    $isBuyAllow = Engine_Api::_()->sitestoreproduct()->isBuyAllowed();

    // IF VIEWER HAS PERMISSION TO BUY THE PRODUCTS
    if( empty($isSitethemeEnable) && !empty($isBuyAllow) )
    {
      // CHECK UPDATE CART NOTIFICATION IS ENABLE OR NOT
      $update_cart_notification = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.cart.update', 1);      
      if( !empty($update_cart_notification) && empty ($isSitemenuEnable) )
      {
        $manage_cart = $view->url(array("action" => "cart"), 'sitestoreproduct_product_general', true);
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $cartProductCounts = 0;

        if( empty($viewer_id) )
        {
          $session = new Zend_Session_Namespace('sitestoreproduct_viewer_cart');
          if( !empty($session->sitestoreproduct_guest_user_cart) )
          {
            $tempUserCart = array();
            $tempUserCart = @unserialize($session->sitestoreproduct_guest_user_cart);
            
            if( !empty($tempUserCart) ) {
              foreach( $tempUserCart as $values ) {
                if( isset($values['config']) && is_array($values['config']) )
                  foreach($values['config'] as $quantity) {
                    $cartProductCounts += $quantity['quantity'];
                  }
                else
                  $cartProductCounts += $values['quantity'];
              }
            }
          }
        }
        else
          $cartProductCounts = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getProductCounts();    

        if( empty($cartProductCounts) )
        {
          $getProductCountStr = $view->translate('0 items');
          $UpdateClassName = 'updates_toggle';
        }
        else
        {
          $getProductCountStr = $view->translate(array('%s item', '%s items', $cartProductCounts), $view->locale()->toNumber($cartProductCounts));
          $UpdateClassName = 'new_updates';
        }

        $script = <<<EOF
          var sitestoreproductHandler;
          var tempCartItemRequest = 0;
          en4.core.runonce.add(function() {
            try {
              sitestoreproductHandler = new SitestoreproductCartHandler({
                'PRODUCT_IN_CART_STR' : "{$getProductCountStr}",
                'PRODUCT_IN_CART_INT' : "{$cartProductCounts}",
                'MANAGE_CART' : "{$manage_cart}",
                'VIEWER_ID' : "{$viewer_id}",
                'UPDATES_TAB_CLASS' : "{$UpdateClassName}",
              });

              sitestoreproductHandler.start();
              // sitestoreproductHandler.cartButtonStart();
              window._sitestoreproductHandler = sitestoreproductHandler;
            } catch( e ) {
              //if( \$type(console) ) console.log(e);
            }
          });
EOF;
    
        $view->headScript()
        ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/cart.js')
        ->appendScript($script);
      }
    }
    $view->headScript()
    ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');
  }

  public function onStatistics($event) {

    $table = Engine_Api::_()->getDbTable('products', 'sitestoreproduct');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'product');
  }

  public function onItemDeleteBefore($event) {
    
    $item = $event->getPayload();
    if ($item instanceof Video_Model_Video) {
      Engine_Api::_()->getDbtable('clasfvideos', 'sitestoreproduct')->delete(array('video_id = ?' => $item->getIdentity()));
    }
  }

  public function onUserDeleteBefore($event) {

    //GET VIEWER ID
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $payload = $event->getPayload();
    if ($payload instanceof User_Model_User) {

      //VIDEO TABLE
      $sitestoreproductvideoTable = Engine_Api::_()->getDbtable('videos', 'sitestoreproduct');
      $sitestoreproductvideoSelect = $sitestoreproductvideoTable->select()->where('owner_id = ?', $payload->getIdentity());

      //RATING TABLE
      $ratingTable = Engine_Api::_()->getDbtable('videoratings', 'sitestoreproduct');

      foreach ($sitestoreproductvideoTable->fetchAll($sitestoreproductvideoSelect) as $sitestoreproductvideo) {
        $ratingTable->delete(array('videorating_id = ?' => $sitestoreproductvideo->video_id));
        $sitestoreproductvideo->delete();
      }

      $ratingSelect = $ratingTable->select()->where('user_id = ?', $payload->getIdentity());
      $ratingVideoDatas = $ratingTable->fetchAll($ratingSelect)->toArray();

      if (!empty($ratingVideoDatas)) {
        foreach ($ratingVideoDatas as $ratingvideo) {
          $ratingTable->delete(array('user_id = ?' => $ratingvideo['user_id']));
          $video_id = $ratingvideo['videorating_id'];
          $avg_rating = $ratingTable->rateVideo($ratingvideo['videorating_id']);
          $sitestoreproductvideoTable->update(array('rating' => $avg_rating), array('video_id = ?' => $ratingvideo['videorating_id']));
        }
      }

      //DELETE SITESTOREPRODUCTS
      $sitestoreproductTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
      $sitestoreproductSelect = $sitestoreproductTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach ($sitestoreproductTable->fetchAll($sitestoreproductSelect) as $sitestoreproduct) {
        $sitestoreproduct->delete();
      }

      //DELETE REVIEWS
      $sitestoreproductTable = Engine_Api::_()->getDbtable('reviews', 'sitestoreproduct');
      $sitestoreproductSelect = $sitestoreproductTable->select()->where('owner_id = ?', $payload->getIdentity())->where('type in (?)', array('user', 'visitor'));
      foreach ($sitestoreproductTable->fetchAll($sitestoreproductSelect) as $sitestoreproduct) {
        $sitestoreproduct->delete();
      }

      //LIKE COUNT DREASE FORM PRODUCT TABLE.
      $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
      $likesTableSelect = $likesTable->select()->where('poster_id = ?', $payload->getIdentity())->Where('resource_type = ?', 'sitestoreproduct_product');
      $results = $likesTable->fetchAll($likesTableSelect);
      foreach ($results as $user) {
        $resource = Engine_Api::_()->getItem('sitestoreproduct_product', $user->resource_id);
        $resource->like_count--;
        $resource->save();
      }

      //COMMENT COUNT DECREASE FORM PRODUCT TABLE.
      $commentsTable = Engine_Api::_()->getDbtable('comments', 'core');
      $commentsTableSelect = $commentsTable->select()->where('poster_id = ?', $payload->getIdentity())->Where('resource_type = ?', 'sitestoreproduct_product');
      $results = $commentsTable->fetchAll($commentsTableSelect);
      foreach ($results as $user) {
        $resource = Engine_Api::_()->getItem('sitestoreproduct_product', $user->resource_id);
        $resource->comment_count--;
        $resource->save();
      }

      $commentsTableSelect = $commentsTable->select()->where('poster_id = ?', $payload->getIdentity())->Where('resource_type = ?', 'sitestoreproduct_review');
      $results = $commentsTable->fetchAll($commentsTableSelect);
      foreach ($results as $user) {
        $resource = Engine_Api::_()->getItem('sitestoreproduct_review', $user->resource_id);
        $resource->comment_count--;
        $resource->save();
      }

      //LIKE COUNT DREASE FORM PRODUCT TABLE.
      $likesTable = Engine_Api::_()->getDbtable('likes', 'core');
      $likesTableSelect = $likesTable->select()->where('poster_id = ?', $payload->getIdentity())->Where('resource_type = ?', 'sitestoreproduct_review');
      $results = $likesTable->fetchAll($likesTableSelect);
      foreach ($results as $user) {
        $resource = Engine_Api::_()->getItem('sitestoreproduct_review', $user->resource_id);
        $resource->like_count--;
        $resource->save();
      }

      //GET EDITOR TABLE
      $editorTable = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct');
      $isSuperEditor = $editorTable->getColumnValue($payload->getIdentity(), 'super_editor');

      if ($isSuperEditor) {
        $totalEditors = $editorTable->getEditorsCount(0);

        if ($totalEditors == 2) {
          $editorTable->delete(array('user_id = ?' => $payload->getIdentity()));

          $editor_id = $editorTable->getColumnValue(0, 'editor_id');
          $editor = Engine_Api::_()->getItem('sitestoreproduct_editor', $editor_id);
          $editorTable->update(array('super_editor' => 1), array('user_id = ?' => $editor->user_id));

            //IF EDITOR IS NOT EXIST
            $isExist = $editorTable->isEditor($editor->user_id);
            if (empty($isExist)) {
              $editorNew = $editorTable->createRow();
              $editorNew->user_id = $editor->user_id;
              $editorNew->designation = $editor->designation;
              $editorNew->details = $editor->details;
              $editorNew->about = $editor->about;
              $editorNew->super_editor = 1;
              $editorNew->save();
            }

        } elseif ($totalEditors == 1) {
          $editorTable->delete(array('user_id = ?' => $payload->getIdentity()));

            //IF EDITOR IS NOT EXIST
            $isExist = $editorTable->isEditor($viewer_id);
            if (empty($isExist)) {
              $editorNew = $editorTable->createRow();
              $editorNew->user_id = $viewer_id;
              $editorNew->designation = 'Super Editor';
              $editorNew->details = '';
              $editorNew->about = '';
              $editorNew->super_editor = 1;
              $editorNew->save();
            }
          
        } else {
          $editorTable->delete(array('user_id = ?' => $payload->getIdentity()));
          $editor_id = $editorTable->getHighestLevelEditorId();
          $editor = Engine_Api::_()->getItem('sitestoreproduct_editor', $editor_id);

         
            //IF EDITOR IS NOT EXIST
            $isExist = $editorTable->isEditor($editor->user_id);
            if (empty($isExist)) {
              $editorNew = $editorTable->createRow();
              $editorNew->user_id = $editor->user_id;
              $editorNew->designation = $editor->designation;
              $editorNew->details = $editor->details;
              $editorNew->about = $editor->about;
              $editorNew->super_editor = 1;
              $editorNew->save();
            }
          
        }
      }

      $super_editor_user_id = $editorTable->getSuperEditor('user_id');

      //GET REVIEW TABLE
      $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct');
      $reviewTable->update(array('owner_id' => $super_editor_user_id), array('type = ?' => 'editor', 'owner_id = ?' => $payload->getIdentity()));
      Engine_Api::_()->getDbTable('ratings', 'sitestoreproduct')->update(array('user_id' => $super_editor_user_id), array('user_id = ?' => $payload->getIdentity(), 'type' => 'editor'));
    }
  }

}