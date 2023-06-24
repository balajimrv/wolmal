<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: GetContent.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_View_Helper_GetRichContentSM extends Zend_View_Helper_Abstract {

  /**
   * Assembles action string
   * 
   * @return string
   */
  public function getRichContentSM($item) {
    if (!$item)
      return;
    switch ($item->getType()) {
      //Work done for only music type items, for poll or video or other item have to add switch case for them.
      case 'music_playlist_song':
        $view = false;
        $params = array();
        $playlist = $item->getParent();
        $videoEmbedded = '';

        // $view == false means that this rich content is requested from the activity feed
        if ($view == false) {
          $desc = strip_tags($playlist->description);
          $desc = "<div class='music_desc'>" . (Engine_String::strlen($desc) > 255 ? Engine_String::substr($desc, 0, 255) . '...' : $desc) . "</div>";
          $zview = Zend_Registry::get('Zend_View');
          $zview->playlist = $playlist;
          $zview->songs = array($item);
          $zview->short_player = true;         
          $videoEmbedded = $desc . $zview->render('application/modules/Sitemobile/modules/Music/views/scripts/_Player.tpl');
        }

        return $videoEmbedded;
        break;
      case 'poll': 
        $view = Zend_Registry::get('Zend_View');
        $view = clone $view;
        $view->clearVars();
        $view->addScriptPath('application/modules/Sitemobile/modules/Poll/views/scripts/');

        $content = '';
        $content .= '
					<div class="feed_poll_rich_content">
						<div class="feed_item_link_title">
							' . $view->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'sea_add_tooltip_link', 'rel' => $item->getType() . ' ' . $item->getIdentity())) . '
						</div>
						<div class="feed_item_link_desc">
							' . $view->viewMore($item->getDescription()) . '
						</div>
				';

        // Render the thingy
        $view->poll = $item;
        $view->owner = $owner = $item->getOwner();
        $view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $view->pollOptions = $item->getOptions();
        $view->hasVoted = $item->viewerVoted();
        $view->showPieChart = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.showpiechart', false);
        $view->canVote = $item->authorization()->isAllowed(null, 'vote');
        $view->canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('poll.canchangevote', false);
        $view->hideLinks = true;
        $view->hideStats = true;

        if(Engine_Api::_()->sitemobile()->isApp()){
        $content .= $view->render('application/modules/Sitemobile/modules/Poll/views/scripts/_poll.tpl');
        }
        /* $content .= '
          <div class="poll_stats">
          '; */

        $content .= '
					</div>
				';
        break;
      
      case 'sitestoreproduct_product':
         $richStr = '';
        $product_id = $item->product_id;
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $currency_symbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();
    $getProduct = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
    $RESOURCE_TYPE = 'sitestoreproduct_product';
    $sub_title = str_replace("'", '"', $getProduct->getTitle());
    $title = '<span class="sitestoreproduct_feed_title dblock">' . $view->htmlLink($getProduct->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($getProduct->getTitle(), 100), array('title' => $sub_title, 'class' => 'sea_add_tooltip_link', 'rel' => $RESOURCE_TYPE . ' ' . $product_id)) . '</span>';
    $price = Engine_Api::_()->sitestoreproduct()->getProductDiscount($getProduct);
    
    $photoURL = $getProduct->getPhotoUrl('thumb.profile');
    $photoURL = !empty($photoURL) ? $photoURL : 'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_profile.png';
    $image = "<a href='" . $getProduct->getHref() . "'  rel = '$RESOURCE_TYPE " . $product_id . "'>" . '<span class="sitestoreproduct_feed_img" style="background-image:url(' . $photoURL . ');"></span>' . '</a>';      
//    $strFlag = "<span class='fright cartbtn'>" . $view->addToCart($getProduct, 1, '') . "</span>";
    $product = '<span class="sitestoreproduct_product_feed b_medium">' . $image . $title . '<span class="price_info">' . $price . '</span>' . '</span>';      
    $richStr .= $product;

    $richStr = rtrim($richStr, ", ");

    return $richStr;
    break;
  
      case 'sitestoreproduct_order':
        $id = $item->order_id;
       $richStr = '';
      // $flag = 1;
       $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

       $currency_symbol = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();

       $product_ids = Engine_Api::_()->getDbtable("orderProducts", 'sitestoreproduct')->getOrderProductsDetail($id);
       $productsCount = @COUNT($product_ids);
       foreach ($product_ids as $productId) {
         $productId['price'] = $currency_symbol.number_format($productId['price'], 2);
         if ($flag > 2)
           break;

         $getProduct = Engine_Api::_()->getItem('sitestoreproduct_product', $productId['product_id']);

         if( empty($getProduct) )
           continue;

         $RESOURCE_TYPE = 'sitestoreproduct_product';
         $order_id = $productId['order_id'];
         $sub_title = str_replace("'", '"', $getProduct->getTitle());
         $title = '<span class="sitestoreproduct_feed_title dblock">' . $view->htmlLink($getProduct->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($getProduct->getTitle(), 100), array('title' => $sub_title, 'class' => 'sea_add_tooltip_link', 'rel' => $RESOURCE_TYPE . ' ' . $productId['product_id'])) . '</span>';
         if( !empty($productId['configuration']) ) {
           $configuration = Zend_Json::decode($productId['configuration']);
             $tempConfigCount = 0;
             foreach($configuration as $config_name => $config_value):
               if( !empty($tempConfigCount) ) :
                 $title .= ', ';
               endif;
               $title .= "<span class='sitestoreproduct_stats'><b>$config_name:</b> $config_value </span>";
               $tempConfigCount++;
             endforeach;
         }
         $price = "<span class='sitestoreproduct_feed_stats'>" . $productId['quantity'] . ' x <strong class="sitestoreproduct_price_sale">' . $productId['price'] . "</strong></span>";

         $photoURL = $getProduct->getPhotoUrl('thumb.profile');
         $photoURL = !empty($photoURL) ? $photoURL : 'application/modules/Sitestoreproduct/externals/images/nophoto_product_thumb_profile.png';
         $image = "<a href='" . $getProduct->getHref() . "' class = 'sea_add_tooltip_link' rel = '$RESOURCE_TYPE " . $productId['product_id'] . "'>" . '<span class="sitestoreproduct_feed_img" style="background-image:url(' . $photoURL . ');"></span>' . '</a>';     
         $product = '<span class="sitestoreproduct_product_feed">' . $image . $title . '<br/><span class="price_info">' .$price . "</span>" . '</span>';      
         $richStr .= $product;
         $flag++;
       }

       if( !empty($richStr) )
       {
         $richStr = rtrim($richStr, ", ");
         return $richStr;
       }
       return false;
        break;
      
      default:
        $content = $item->getRichContent();
    }
    return $content;
  }

}
