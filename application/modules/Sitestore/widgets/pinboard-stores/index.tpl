<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/pinboard.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/mooMasonry.js');
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_board.css'); ?>

 <?php $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.price.field', 0); ?>
  <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.locationfield', 1); ?>
<?php if ($this->autoload): ?>
  <div id="pinboard_<?php echo $this->identity ?>">
    <?php if(isset ($this->params['defaultLoadingImage']) && $this->params['defaultLoadingImage']): ?>
      <div class="seaocore_loading_image"></div>
    <?php endif; ?>    
  </div>
  <script type="text/javascript">
   var layoutColumn='middle';
   if($("pinboard_<?php echo $this->identity ?>").getParent('.layout_left')){
     layoutColumn='left';
   }else if($("pinboard_<?php echo $this->identity ?>").getParent('.layout_right')){
     layoutColumn='right';
   }
    PinBoardSeaoObject[layoutColumn].add({
      contentId:'pinboard_<?php echo $this->identity ?>',
      widgetId:'<?php echo $this->identity ?>',
      totalCount:'<?php echo $this->totalCount ?>',
      detactLocation : <?php echo $this->detactLocation; ?>,
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainerClass :'layout_sitestore_pinboard_stores'
    });

  </script>
<?php else: ?>
  <?php if (!$this->autoload && !$this->is_ajax_load): ?> 
    <div id="pinboard_<?php echo $this->identity ?>"></div>
    <script type="text/javascript">
      en4.core.runonce.add(function(){
        var pinBoardViewMore= new PinBoardSeaoViewMore({
          contentId:'pinboard_<?php echo $this->identity ?>',
          widgetId:'<?php echo $this->identity ?>',
          totalCount:'<?php echo $this->totalCount ?>',
          viewMoreId:'seaocore_view_more_<?php echo $this->identity ?>',
          loadingId:'seaocore_loading_<?php echo $this->identity ?>',
          requestParams :<?php echo json_encode($this->params) ?>,
          responseContainerClass :'layout_sitestore_pinboard_stores'
        });
        PinBoardSeaoViewMoreObjects.push(pinBoardViewMore);
      });
    </script>
  <?php endif; ?>

    <?php if ($this->is_ajax_load && $this->currentpage == $this->params['noOfTimes']): ?>
        <script type="text/javascript">
          var layoutColumn='middle';
          if($("pinboard_wrapper_<?php echo $this->identity ?>").getParent('.layout_left')){
            layoutColumn='left';
          }else if($("pinboard_wrapper_<?php echo $this->identity ?>").getParent('.layout_right')){
            layoutColumn='right';
          }
          PinBoardSeaoObject[layoutColumn].currentIndex++
        </script>
      <?php endif; ?>

  <?php $countButton = count($this->show_buttons); ?>
  <?php foreach ($this->sitestores as $store): ?>
  
    <?php
    $noOfButtons = $countButton;
    if($this->show_buttons):
      $alllowComment=(in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $store->authorization()->isAllowed($this->viewer(), "comment");
      if(in_array('comment', $this->show_buttons) && !$alllowComment){
        $noOfButtons--;
      }
       if(in_array('like', $this->show_buttons) && !$alllowComment){
        $noOfButtons--;
      }
     
     endif;
    ?>
    <div class="seaocore_list_wrapper" style="width:<?php echo $this->params['itemWidth'] ?>px;">
      <div class="seaocore_board_list b_medium" style="width:<?php echo $this->params['itemWidth'] - 18 ?>px;">
        <div>
          <?php if ($store->featured): ?>
            <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"><?php echo $this->translate('Featured')?></span>
          <?php endif; ?>
          <div class="seaocore_board_list_thumb">
          	<a href="<?php echo $store->getHref() ?>" class="seaocore_thumb">
	            <table>
	              <tr valign="middle">
	                <td>
	                             <?php $options=  array('align' => 'center');
	                  
	                  if(isset ($this->params['withoutStretch']) && $this->params['withoutStretch']):
	                 $options['style']='width:auto; max-width:'.($this->params['itemWidth'] - 18).'px;';
	                          endif;?>  
	    <?php echo $this->itemPhoto($store, ($this->params['itemWidth'] > 200)?'thumb.main':'thumb.profile', '', $options); ?>
	                  
			             
	                </td> 
	              </tr> 
	            </table>
            </a>
          </div>
          
          <div class="seaocore_board_list_btm">
            <?php if ($this->postedby): ?>
              <?php echo $this->htmlLink($store->getOwner()->getHref(), $this->itemPhoto($store->getOwner(), 'thumb.icon', '', array())) ?>
              <?php endif; ?>  
            <div class="o_hidden seaocore_stats seaocore_txt_light">
              <?php if ($this->postedby): ?>
                <b><?php echo $this->htmlLink($store->getOwner()->getHref(), $store->getOwner()->getTitle()) ?></b><br />
              <?php endif; ?>
              <?php if($store->category_id):?>  
              <?php echo $this->translate("in %s", $this->htmlLink($store->getCategory()->getHref(), $this->translate($store->getCategory()->getTitle(true)))) ?> - <?php endif; ?> 
    <?php echo $this->timestamp(strtotime($store->creation_date)) ?>
            </div>
          </div>
          
           <?php if (!empty($store->sponsored)): ?>
            <div class="seaocore_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.sponsored.color', '#FC0505'); ?>">
            <?php echo $this->translate('SPONSORED'); ?>                 
            </div>
          <?php endif; ?>
          
          <div class="seaocore_board_list_cont">
            <div class="seaocore_title">
              <?php echo $this->htmlLink($store->getHref(), $store->getTitle()) ?>
            </div>
            
            <?php if($this->truncationDescription): ?>
              <div class="seaocore_description">
                <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($store->getDescription(), $this->truncationDescription) ?>
              </div>  
            <?php endif;  ?>
              
             <?php if (in_array('price', $this->showOptions) && $store->price && $enablePrice): ?>
                <div class="seaocore_stats seaocore_txt_light mtop5">
                   <span><?php echo $this->translate('Price:'); ?></span>
                   <span><?php echo Engine_Api::_()->sitestore()->getPriceWithCurrency($store->price);?></span>
                </div>
             <?php endif; ?>
             <?php if($this->detactLocation && isset ($store->locationName) && $store->locationName):
              $location = $store->locationName;
              elseif($store->location):
              $location = $store->location;
            endif;
            ?>
            <?php if(in_array('location', $this->showOptions) && $location && $enableLocation):?>
            <div class="seaocore_stats seaocore_txt_light mtop5">
              <span><?php echo $this->translate('Location:'); ?></span>
              <span><?php echo $location ?>&nbsp; - 
              <b>
                <?php $location_id = Engine_Api::_()->getDbTable('locations', 'sitestore')->getLocationId($store->store_id, $location);
                echo  $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $store->store_id, 'resouce_type' => 'sitestore_store', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('class' => 'smoothbox')) ; ?>
              </b>
              </span>
            </div>
          <?php endif; ?>
           <?php if (in_array('viewCount', $this->showOptions) || in_array('likeCount', $this->showOptions) || in_array('commentCount', $this->showOptions) || in_array('followCount', $this->showOptions) || (in_array('commentCount', $this->showOptions) && $this->membersEnabled)): ?>
              <div class="seaocore_stats seaocore_txt_light">
                <?php
                if (in_array('viewCount', $this->showOptions)) {
                 echo $this->translate(array('%s view', '%s views', $store->view_count), $this->locale()->toNumber($store->view_count)) . '&nbsp;&nbsp;&nbsp;&nbsp;';
                }

                if (in_array('likeCount', $this->showOptions)) {
                  echo '<span class="pin_like_st_' . $store->getGuid() . '">' . $this->translate(array('%s like', '%s likes', $store->like_count), $this->locale()->toNumber($store->like_count)) . '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                
                if (in_array('followCount', $this->showOptions)) {
                   echo  '<span id="pin_followt_st_' . $store->getGuid().'_'.$this->identity . '">' .$this->translate(array('%s follower', '%s followers', $store->follow_count), $this->locale()->toNumber($store->follow_count)) . '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                if (in_array('commentCount', $this->showOptions)) {
                   echo  '<span id="pin_comment_st_' . $store->getGuid().'_'.$this->identity . '">' .$this->translate(array('%s comment', '%s comments', $store->comment_count), $this->locale()->toNumber($store->comment_count)) . '</span>' . '&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                
                      if(in_array('memberCount', $this->showOptions) && $this->membersEnabled){
                  echo $this->translate(array('%s member', '%s members', $store->member_count), $this->locale()->toNumber($store->member_count));
                } 
             
                ?>
                <?php //echo $statistics; ?> 
              </div>
            
            <?php endif; ?>

						<?php if (!empty($this->showOptions) && in_array('reviewsRatings', $this->showOptions) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview') && $store->rating > 0): ?>
							<?php
								$currentRatingValue = $store->rating;
								$difference = $currentRatingValue- (int)$currentRatingValue;
								if($difference < .5) {
									$finalRatingValue = (int)$currentRatingValue;
								}
								else {
									$finalRatingValue = (int)$currentRatingValue + .5;
								}
							?>

							<div class='seaocore_browse_list_info_date'>          
								<?php 
								if (in_array('reviewsRatings', $this->showOptions) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestorereview')) {
									echo '<span id="pin_review_st_' . $store->getGuid() . '_' . $this->identity . '">' . $this->translate(array('%s review', '%s reviews', $store->review_count), $this->locale()->toNumber($store->review_count)) . '</span>&nbsp;&nbsp;';
								}
								?>
								<span class="sitestore_rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
									<span class="clr">
										<?php for ($x = 1; $x <= $store->rating; $x++): ?>
										<span class="rating_star_generic rating_star" ></span>
										<?php endfor; ?>
										<?php if ((round($store->rating) - $store->rating) > 0): ?>
										<span class="rating_star_generic rating_star_half" ></span>
										<?php endif; ?>
									</span>
								</span>
							</div>
				 
						<?php endif; ?>

            
          </div>
          
          <div class="seaocore_board_list_comments o_hidden">
            <?php echo $this->action("list", "pin-board-comment", "seaocore", array("type" => $store->getType(), "id" => $store->getIdentity(), 'widget_id' => $this->identity)); ?>
          </div>
          <?php if (!empty($this->show_buttons)): ?>
            <div class="seaocore_board_list_action_links">
              <?php $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $store->getHref()); ?>
             
              <?php if ((in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $alllowComment): ?>
                <?php if (in_array('comment', $this->show_buttons)): ?>
                  <a href='javascript:void(0);' onclick="en4.seaocorepinboard.comments.addComment('<?php echo $store->getGuid() . "_" . $this->identity ?>')" class="seaocore_board_icon comment_icon" title="Comment"><!--<?php echo $this->translate('Comment'); ?>--></a> 
                <?php endif; ?>
                <?php if (in_array('like', $this->show_buttons)): ?>
                  <a href="javascript:void(0)" title="Like" class="seaocore_board_icon like_icon <?php echo $store->getGuid() ?>like_link" id="<?php echo $store->getType() ?>_<?php echo $store->getIdentity() ?>like_link" <?php if ($store->likes()->isLike($this->viewer())): ?>style="display: none;" <?php endif; ?>onclick="en4.seaocorepinboard.likes.like('<?php echo $store->getType() ?>', '<?php echo $store->getIdentity() ?>');" ><!--<?php echo $this->translate('Like'); ?>--></a>

                  <a  href="javascript:void(0)" title="Unlike" class="seaocore_board_icon unlike_icon <?php echo $store->getGuid() ?>unlike_link" id="<?php echo $store->getType() ?>_<?php echo $store->getIdentity() ?>unlike_link" <?php if (!$store->likes()->isLike($this->viewer())): ?>style="display:none;" <?php endif; ?> onclick="en4.seaocorepinboard.likes.unlike('<?php echo $store->getType() ?>', '<?php echo $store->getIdentity() ?>');"><!--<?php echo $this->translate('Unlike'); ?>--></a> 
                <?php endif; ?>
              <?php endif; ?>
                  
              <?php if (in_array('share', $this->show_buttons)): ?>
                <?php echo $this->htmlLink(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'route' => 'default', 'type' => $store->getType(), 'id' => $store->getIdentity(), 'not_parent_refresh' => '1', 'format' => 'smoothbox'), $this->translate(''), array('class' => 'smoothbox seaocore_board_icon share_icon' , 'title' => 'Share')); ?>
              <?php endif; ?>
                  
              <?php if (in_array('facebook', $this->show_buttons)): ?>
                <?php echo $this->htmlLink('http://www.facebook.com/share.php?u=' . $urlencode . '&t=' . $store->getTitle(), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon fb_icon' , 'title' => 'Facebook')) ?>
              <?php endif; ?>
                  
              <?php if (in_array('twitter', $this->show_buttons)): ?>
                <?php echo $this->htmlLink('http://twitter.com/share?url=' . $urlencode . '&text=' . $store->getTitle(), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon tt_icon' , 'title' => 'Twitter')) ?> 
              <?php endif; ?>
                  
              <?php if (in_array('pinit', $this->show_buttons)): ?>
                <a href="http://pinterest.com/pin/create/button/?url=<?php echo $urlencode; ?>&media=<?php echo urlencode((!preg_match("~^(?:f|ht)tps?://~i", $store->getPhotoUrl('thumb.profile')) ? (((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : '') . $store->getPhotoUrl('thumb.profile')); ?>&description=<?php echo $store->getTitle(); ?>"  class="pb_ch_wd seaocore_board_icon pin_icon"  title="Pin It" ><!--<?php echo $this->translate('Pin It') ?>--></a>
              <?php endif; ?>
                
              <?php if (in_array('tellAFriend', $this->show_buttons)): ?>
                <?php echo $this->htmlLink(array('action' => 'tell-a-friend', 'route' => 'sitestore_profilestore' , 'id' => $store->getIdentity()), $this->translate(''), array('class' => 'smoothbox seaocore_board_icon taf_icon' , 'title' => 'Tell a Friend')); ?>
              <?php endif; ?>
                
              <?php if (in_array('print', $this->show_buttons)): ?>
                <?php echo $this->htmlLink(array('action' => 'print', 'route' => 'sitestore_profilestore' , 'type' => $store->getType(), 'store_id' => $store->getIdentity()), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon print_icon' , 'title' => 'Print')); ?> 
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

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

