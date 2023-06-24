<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/pinboard.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/mooMasonry.js');
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_board.css'); ?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css'); ?>

<?php if ($this->autoload): ?>
  <div id="pinboard_<?php echo $this->identity ?>">
    <?php if(isset ($this->params['defaultLoadingImage']) && $this->params['defaultLoadingImage']): ?>
      <div class="sr_sitestoreproduct_profile_loading_image"></div>
    <?php endif; ?>
  </div>
  <script type="text/javascript">
   var layoutColumn='middle';
   if($("pinboard_<?php echo $this->identity ?>").getParent('.layout_left')){
     layoutColumn='left';
   }else if($("pinboard_<?php echo $this->identity ?>").getParent('.layout_right')){
     layoutColumn='right';
   }
    PinBoardSRObject[layoutColumn].add({
      contentId:'pinboard_<?php echo $this->identity ?>',
      widgetId:'<?php echo $this->identity ?>',
      totalCount:'<?php echo $this->totalCount ?>',
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainerClass :'layout_sitestoreproduct_pinboard_products_sitestoreproduct'
    });

  </script>
<?php else: ?>
  <?php if (!$this->autoload && !$this->is_ajax_load): ?> 
    <div id="pinboard_<?php echo $this->identity ?>"></div>
    <script type="text/javascript">
      en4.core.runonce.add(function(){
        var pinBoardViewMore= new PinBoardSRViewMore({
          contentId:'pinboard_<?php echo $this->identity ?>',
          widgetId:'<?php echo $this->identity ?>',
          totalCount:'<?php echo $this->totalCount ?>',
          viewMoreId:'seaocore_view_more_<?php echo $this->identity ?>',
          loadingId:'seaocore_loading_<?php echo $this->identity ?>',
          requestParams :<?php echo json_encode($this->params) ?>,
          responseContainerClass :'layout_sitestoreproduct_pinboard_products_sitestoreproduct'
        });
        PinBoardSRViewMoreObjects.push(pinBoardViewMore);
      });
    </script>
  <?php endif; ?>

  <?php
    $ratingValue = $this->ratingType;
    $ratingShow = 'small-star';
    if ($this->ratingType == 'rating_editor') {
      $ratingType = 'editor';
    } elseif ($this->ratingType == 'rating_avg') {
      $ratingType = 'overall';
    } else {
      $ratingType = 'user';
    }
  ?>

  <?php $countButton = count($this->show_buttons); ?>
  <?php if($this->totalCount > 0): ?>  
    <?php foreach ($this->products as $sitestoreproduct): ?>

      <?php
      $noOfButtons = $countButton;
      if($this->show_buttons):
        if (in_array('compare', $this->show_buttons)):
          $compareButton = $this->compareButtonSitestoreproduct($sitestoreproduct, $this->identity, 'pinboard-button');
          if(empty ($compareButton)):$noOfButtons--; endif;
        else:
          $compareButton = null;
        endif;

        $alllowComment=(in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $sitestoreproduct->authorization()->isAllowed($this->viewer(), "comment");
        if(in_array('comment', $this->show_buttons) && !$alllowComment){
          $noOfButtons--;
        }
         if(in_array('like', $this->show_buttons) && !$alllowComment){
          $noOfButtons--;
        }
        if (in_array('wishlist', $this->show_buttons)):
           $noOfButtons--;
        endif;
       endif;
      ?>
      <div class="seaocore_list_wrapper" style="width:<?php echo $this->params['itemWidth'] ?>px;">
        <div class="seaocore_board_list b_medium sitestoreproduct_q_v_wrap" style="width:<?php echo $this->params['itemWidth'] - 18 ?>px;">
          <div>
            <?php if ($sitestoreproduct->featured && !empty($this->featuredIcon)): ?>
              <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured') ?></span>
            <?php elseif ($sitestoreproduct->newlabel && !empty($this->newIcon)): ?>
              <i class="seaocore_list_new_label sr_sitestoreproduct_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
            <?php endif; ?>
              <?php $product_id = $sitestoreproduct->product_id; ?>
              <?php $quickViewButton = true; ?>
              <?php include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/scripts/_quickView.tpl'; ?>
            <div class="seaocore_board_list_thumb">
              <a href="<?php echo $sitestoreproduct->getHref() ?>">
                <table>
                  <tr valign="middle">
                    <td>
                               <?php $options=  array('align' => 'center');

                      if(isset ($this->params['withoutStretch']) && $this->params['withoutStretch']):
                     $options['style']='width:auto; max-width:'.($this->params['itemWidth'] - 18).'px;';
                              endif;?>  
        <?php echo $this->itemPhoto($sitestoreproduct, ($this->params['itemWidth']>300)?'thumb.main':'thumb.profile', '', $options); ?>

                      
                    </td> 
                  </tr> 
                </table>
              </a>
            </div>
            
            <div class="seaocore_board_list_btm clr">
              <?php if ($this->postedby): ?>
                <?php echo $this->htmlLink($sitestoreproduct->getOwner()->getHref(), $this->itemPhoto($sitestoreproduct->getOwner(), 'thumb.icon', '', array())) ?>
                <?php endif; ?>  
              <div class="o_hidden seaocore_stats seaocore_txt_light">
                <?php if ($this->postedby): ?>
                  <b><?php echo $this->htmlLink($sitestoreproduct->getOwner()->getHref(), $sitestoreproduct->getOwner()->getTitle()) ?></b><br />
                <?php endif; ?>
                <?php echo $this->translate("in %s", $this->htmlLink($sitestoreproduct->getCategory()->getHref(), $sitestoreproduct->getCategory()->getTitle(true))) ?>
                <?php if (!empty($this->statistics) && in_array('productCreationTime', $this->statistics)): ?>
                    - 
                  <?php echo $this->timestamp(strtotime($sitestoreproduct->creation_date)) ?>
                <?php endif; ?>
              </div>
            </div>
            
            <?php if (!empty($sitestoreproduct->sponsored) && !empty($this->sponsoredIcon)): ?>
              <div class="sr_sitestoreproduct_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.sponsoredcolor', '#FC0505'); ?>">
              <?php echo $this->translate('SPONSORED'); ?>                 
              </div>
            <?php endif; ?>
            
            <div class="seaocore_board_list_cont">
              <div class="seaocore_title">
                <?php echo $this->htmlLink($sitestoreproduct->getHref(), $sitestoreproduct->getTitle()) ?>
              </div>

              <?php if($this->truncationDescription): ?>
                <div class="seaocore_description">
                  <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($sitestoreproduct->getDescription(), $this->truncationDescription) ?>
                </div>  
              <?php endif;  ?>

              <!-- DISPLAY PRODUCTS -->
              <?php 
              // CALLING HELPER FOR GETTING PRODUCT INFORMATIONS
              echo $this->getProductInfo($sitestoreproduct, $this->identity, 'list_view', $this->showAddToCart, $this->showinStock); ?>
              
               <?php if (!empty($this->statistics)): ?>
                <div class="seaocore_stats seaocore_txt_light">
                  <?php
                  if (in_array('viewCount', $this->statistics)) {
                   echo $this->translate(array('%s view', '%s views', $sitestoreproduct->view_count), $this->locale()->toNumber($sitestoreproduct->view_count)) . '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }

                  if (in_array('likeCount', $this->statistics)) {
                    echo '<span class="pin_like_st_' . $sitestoreproduct->getGuid() . '">' . $this->translate(array('%s like', '%s likes', $sitestoreproduct->like_count), $this->locale()->toNumber($sitestoreproduct->like_count)) . '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                  if (in_array('commentCount', $this->statistics)) {
                     echo  '<span id="pin_comment_st_' . $sitestoreproduct->getGuid().'_'.$this->identity . '">' .$this->translate(array('%s comment', '%s comments', $sitestoreproduct->comment_count), $this->locale()->toNumber($sitestoreproduct->comment_count)) . '</span>';
                  }

                  ?>
                  <?php //echo $statistics; ?> 
                </div>
              <?php endif; ?>


              <div class="seaocore_stats seaocore_txt_light mtop5">
                <span class="fright">
                   <?php 
                      if ($this->statistics && in_array('reviewCount', $this->statistics) && (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 2)):

                        echo $this->htmlLink($sitestoreproduct->getHref(), $this->partial(
                                                '_showReview.tpl', 'sitestoreproduct', array('sitestoreproduct' => $sitestoreproduct))) . '';

                      endif;
                  ?>
                </span>
                <?php if (!empty($this->statistics) && in_array('ratingStar', $this->statistics)): ?>
                    <span class="o_hidden">
                      <?php if ($ratingValue == 'rating_both'): ?>
                        <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_editor, 'editor', $ratingShow); ?>
                        <br />
                        <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->rating_users, 'user', $ratingShow); ?>
                      <?php else: ?>
                        <?php echo $this->showRatingStarSitestoreproduct($sitestoreproduct->$ratingValue, $ratingType, $ratingShow); ?>
                      <?php endif; ?>
                    </span> 
                <?php endif; ?>
              </div>
            </div>

            <?php if($this->commentSection): ?>  
              <div class="seaocore_board_list_comments o_hidden">
                <?php echo $this->action("list", "pin-board-comment", "sitestoreproduct", array("type" => $sitestoreproduct->getType(), "id" => $sitestoreproduct->product_id, 'widget_id' => $this->identity)); ?>
              </div>
            <?php endif; ?>
            <?php if (!empty($this->show_buttons)): ?>
              <div class="seaocore_board_list_action_links">
                <?php $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $sitestoreproduct->getHref()); ?>
                <?php if (in_array('wishlist', $this->show_buttons)): ?> 
                  <?php echo $this->addToWishlistSitestoreproduct($sitestoreproduct, array('classIcon' => 'seaocore_board_icon', 'classLink' => 'wishlist_icon', 'text' => $this->translate('')));?>
                <?php endif; ?>
                <?php if ($compareButton): ?>
                  <?php echo $compareButton; ?>
                <?php endif; ?>
                <?php if ((in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $alllowComment): ?>
                  <?php if (in_array('comment', $this->show_buttons)): ?>
                    <a href='javascript:void(0);' onclick="en4.srpinboard.comments.addComment('<?php echo $sitestoreproduct->getGuid() . "_" . $this->identity ?>')" class="seaocore_board_icon comment_icon" title="Comment"><!--<?php echo $this->translate('Comment'); ?>--></a> 
                  <?php endif; ?>
                  <?php if (in_array('like', $this->show_buttons)): ?>
                    <a href="javascript:void(0)" title="Like" class="seaocore_board_icon like_icon <?php echo $sitestoreproduct->getGuid() ?>like_link" id="<?php echo $sitestoreproduct->getType() ?>_<?php echo $sitestoreproduct->getIdentity() ?>like_link" <?php if ($sitestoreproduct->likes()->isLike($this->viewer())): ?>style="display: none;" <?php endif; ?>onclick="en4.srpinboard.likes.like('<?php echo $sitestoreproduct->getType() ?>', '<?php echo $sitestoreproduct->getIdentity() ?>');" ><!--<?php echo $this->translate('Like'); ?>--></a>

                    <a  href="javascript:void(0)" title="Unlike" class="seaocore_board_icon unlike_icon <?php echo $sitestoreproduct->getGuid() ?>unlike_link" id="<?php echo $sitestoreproduct->getType() ?>_<?php echo $sitestoreproduct->getIdentity() ?>unlike_link" <?php if (!$sitestoreproduct->likes()->isLike($this->viewer())): ?>style="display:none;" <?php endif; ?> onclick="en4.srpinboard.likes.unlike('<?php echo $sitestoreproduct->getType() ?>', '<?php echo $sitestoreproduct->getIdentity() ?>');"><!--<?php echo $this->translate('Unlike'); ?>--></a> 
                  <?php endif; ?>
                <?php endif; ?>

                <?php if (in_array('share', $this->show_buttons)): ?>
                  <?php echo $this->htmlLink(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'route' => 'default', 'type' => $sitestoreproduct->getType(), 'id' => $sitestoreproduct->getIdentity(), 'not_parent_refresh' => '1', 'format' => 'smoothbox'), $this->translate(''), array('class' => 'smoothbox seaocore_board_icon share_icon' , 'title' => 'Share')); ?>
                <?php endif; ?>

                <?php if (in_array('facebook', $this->show_buttons)): ?>
                  <?php echo $this->htmlLink('http://www.facebook.com/share.php?u=' . $urlencode . '&t=' . $sitestoreproduct->getTitle(), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon fb_icon' , 'title' => 'Facebook')) ?>
                <?php endif; ?>

                <?php if (in_array('twitter', $this->show_buttons)): ?>
                  <?php echo $this->htmlLink('http://twitter.com/share?url=' . $urlencode . '&text=' . $sitestoreproduct->getTitle(), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon tt_icon' , 'title' => 'Twitter')) ?> 
                <?php endif; ?>

                <?php if (in_array('pinit', $this->show_buttons)): ?>
                  <a href="http://pinterest.com/pin/create/button/?url=<?php echo $urlencode; ?>&media=<?php echo urlencode((!preg_match("~^(?:f|ht)tps?://~i", $sitestoreproduct->getPhotoUrl('thumb.profile')) ? (((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : '') . $sitestoreproduct->getPhotoUrl('thumb.profile')); ?>&description=<?php echo $sitestoreproduct->getTitle(); ?>"  class="pb_ch_wd seaocore_board_icon pin_icon"  title="Pin It" ><!--<?php echo $this->translate('Pin It') ?>--></a>
                <?php endif; ?>

                <?php if (in_array('tellAFriend', $this->show_buttons)): ?>
                  <?php echo $this->htmlLink(array('action' => 'tellafriend', 'route' => 'sitestoreproduct_specific', 'type' => $sitestoreproduct->getType(), 'product_id' => $sitestoreproduct->getIdentity()), $this->translate(''), array('class' => 'smoothbox seaocore_board_icon taf_icon' , 'title' => 'Tell a Friend')); ?>
                <?php endif; ?>

                <?php if (in_array('print', $this->show_buttons)): ?>
                  <?php echo $this->htmlLink(array('action' => 'print', 'route' => 'sitestoreproduct_specific', 'type' => $sitestoreproduct->getType(), 'product_id' => $sitestoreproduct->getIdentity()), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon print_icon' , 'title' => 'Print')); ?> 
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="tip mtop10"> 
      <span> 
        <?php echo $this->translate('No products have been created yet.'); ?>
      </span>
    </div>    
  <?php endif; ?>  

  <?php if (!$this->autoload && !$this->is_ajax_load): ?>
    <div class="seaocore_view_more mtop10 dnone" id="seaocore_view_more_<?php echo $this->identity ?>">
      <a href="javascript:void(0);" id="" class="buttonlink icon_viewmore"><?php echo$this->translate('View More') ?></a>
    </div>
    <div class="seaocore_loading dnone" id="seaocore_loading_<?php echo $this->identity ?>" >
      <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/core/loading.gif" style="margin-right: 5px;">
      <?php echo $this->translate('Loading ...') ?>
    </div>
  <?php endif; ?>
<?php endif; ?>

