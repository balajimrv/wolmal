<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php $randonNumber = $this->identity; ?>
 <?php $this->headScript()->appendFile($baseUrl . 'application/modules/Sesbasic/externals/scripts/PeriodicalExecuter.js')
 													->appendFile($baseUrl . 'application/modules/Sesbasic/externals/scripts/Carousel.js')
                          ->appendFile($baseUrl . 'application/modules/Sesbasic/externals/scripts/Carousel.Extra.js'); 
       $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sesalbum/externals/styles/carousel.css'); 
       $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesalbum/externals/styles/styles.css');
 ?>
<?php 
	$height = (isset($this->insideOutside) && $this->insideOutside == 'outside') ? '72' : '0'; 
	$height = $height + $this->height;
?>
<style type="text/css">
#sesalbum_slider_<?php echo $randonNumber; ?> {
	position: relative;
	height:<?php echo $height; ?>px;
	overflow: hidden;
}
</style>
<div class="slide sesalbum_carousel_wrapper clearfix <?php echo $this->align == 'vertical' ? 'sesalbum_carousel_v_wrapper' : 'sesalbum_carousel_h_wrapper'; ?> <?php echo $this->mouseover == '1' ? 'hidenav' : ''; ?> ">
  <div id="sesalbum_slider_<?php echo $randonNumber; ?>" class="sesalbum_album_listing sesbasic_bxs">
    <?php $limit = 0;
     			$allowRatingAlbum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.album.rating',1);
					$allowShowPreviousRatingAlbum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratealbum.show',1);
          if($allowRatingAlbum == 0){
          	if($allowShowPreviousRatingAlbum == 0)
            	$ratingShowAlbum = false;
             else
             	$ratingShowAlbum = true;
          }else
          	$ratingShowAlbum = true;
          $allowRatingPhoto = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.photo.rating',1);
					$allowShowPreviousRatingPhoto = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratephoto.show',1);
          if($allowRatingPhoto == 0){
          	if($allowShowPreviousRatingPhoto == 0)
            	$ratingShowPhoto= false;
             else
             	$ratingShowPhoto = true;
          }else
          	$ratingShowPhoto = true;
    	foreach( $this->paginator as $item ){
      $photoURL = $item->getPhotoUrl('thumb.normalmain'); ?>
      <?php if($this->typeWidget == 1 || $this->typeWidget == 3) { ?>
        <div class="ses_album_image_viewer sesalbum_list_grid_thumb sesalbum_list_photo_grid sesalbum_list_grid sesa-i-<?php echo (isset($this->insideOutside) && $this->insideOutside == 'outside') ? 'outside' : 'inside'; ?> sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
          <?php 
          $photoURL=$item->getPhotoUrl();
          $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($item,array('limit'=>$limit,'status'=>$this->type,'order'=>$this->order)); ?>
          <a class="sesalbum_list_grid_img ses-image-viewer" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $item->getPhotoUrl(); ?>','<?php echo $imageURL	; ?>')" href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($item->getIdentity(),$item->album_id); ?>" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;"> 
            <span style="background-image: url(<?php echo $item->getPhotoUrl('thumb.normalmain'); ?>);"></span> 
          </a>
          <?php 
            if(isset($this->socialSharing) || isset($this->likeButton) || isset($this->favouriteButton)){
            //album viewpage link for sharing
            $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
            <span class="sesalbum_list_grid_btns">
              <?php if(isset($this->socialSharing)){ ?>
                <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $item->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_icon_btn sesalbum_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
                <a href="<?php echo 'https://twitter.com/intent/tweet?url=' . $urlencode . '&text=' . $item->getTitle().'%0a'; ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_icon_btn sesalbum_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
                <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($item->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $item->getPhotoUrl('thumb.main')) . $item->getPhotoUrl('thumb.main')); ?>&description=<?php echo $item->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
                <?php } 
                	$canComment =  Engine_Api::_()->getItem('album',$item->album_id )->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
                  if(isset($this->likeButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && $canComment){
                ?>
                <!--Photo Like Button-->
                <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatusPhoto($item->photo_id); ?>
                  <a href="javascript:;" data-src='<?php echo $item->photo_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_photolike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                    <i class="fa fa-thumbs-up"></i>
                    <span><?php echo $item->like_count; ?></span>
                  </a>
                  <?php } 
                  $canFavourite =  Engine_Api::_()->authorization()->isAllowed('album',Engine_Api::_()->user()->getViewer(), 'favourite_photo');
                  if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton) && $canFavourite){
                  $albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album_photo','resource_id'=>$item->photo_id)); ?>
                    <a href="javascript:;" data-src='<?php echo $item->photo_id; ?>' class="sesalbum_icon_btn sesalbum_icon_fav_btn sesalbum_photoFav <?php echo ($albumFavStatus)>0 ? 'button_active' : '' ; ?>">
                      <i class="fa fa-heart"></i>
                      <span><?php echo $item->favourite_count; ?></span>
                    </a>
                  <?php } ?>
            </span>
          <?php } ?>
              <?php if(isset($this->featured) || isset($this->sponsored)){ ?>
                <span class="sesalbum_labels_container">
                  <?php if(isset($this->featured) && $item->is_featured == 1){ ?>
                    <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
                  <?php } ?>
                  <?php if(isset($this->sponsored)  && $item->is_sponsored == 1){ ?>
                    <span class="sesalbum_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
                  <?php } ?>
                </span>
              <?php } ?>
              
              <?php if(isset($this->like) || isset($this->comment) || isset($this->view) || isset($this->title) || isset($this->rating) || isset($this->favouriteCount) || isset($this->downloadCount)  || isset($this->by)){ ?>
                <p class="sesalbum_list_grid_info sesbasic_clearfix">
                  <?php if(isset($this->title)) { ?>
                    <span class="sesalbum_list_grid_title">
                      <?php echo $this->htmlLink($item, $this->htmlLink($item, $this->string()->truncate($item->getTitle(), $this->title_truncation), array('title'=>$item->getTitle()))) ?>
                    </span>
                  <?php } ?>
                  <span class="sesalbum_list_grid_stats">
                    <?php if(isset($this->by)) { ?>
                      <span class="sesalbum_list_grid_owner">
                        <?php echo $this->translate('By');?>
                        <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                      </span>
                    <?php }?>
                    <?php if(isset($this->rating) && $ratingShowPhoto) { ?>
                     <?php
                      $user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album_photo',$item->photo_id);
                      $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                      $textRatingText = $item->rating == 1 ? 'rating' : 'ratings'; ?>
                      <span class="sesalbum_list_grid_rating" title="<?php echo $item->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                     		<?php if( $item->rating > 0 ): ?>
                          <?php for( $x=1; $x<= $item->rating; $x++ ): ?>
                            <span class="sesbasic_rating_star_small fa fa-star"></span>
                          <?php endfor; ?>
                          <?php if( (round($item->rating) - $item->rating) > 0): ?>
                            <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                          <?php endif; ?>
                        <?php endif; ?> 
                      </span>
                  	<?php } ?>
                  </span>
                  <span class="sesalbum_list_grid_stats sesbasic_text_light">
                    <?php if(isset($this->like)) { ?>
                      <span class="sesalbum_list_grid_likes" title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count))?>">
                        <i class="fa fa-thumbs-up"></i>
                        <?php echo $item->like_count;?>
                      </span>
                    <?php } ?>
                  <?php if(isset($this->comment)) { ?>
                    <span class="sesalbum_list_grid_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>">
                      <i class="fa fa-comment"></i>
                      <?php echo $item->comment_count;?>
                    </span>
                 <?php } ?>
                 <?php if(isset($this->view)) { ?>
                  <span class="sesalbum_list_grid_views" title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>">
                    <i class="fa fa-eye"></i>
                    <?php echo $item->view_count;?>
                  </span>
                 <?php } ?>
                 <?php if(isset($this->favouriteCount)) { ?>
                    <span class="sesalbum_list_grid_fav" title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>">
                      <i class="fa fa-heart"></i> 
                      <?php echo $item->favourite_count;?>            
                    </span>
                  <?php } ?>
                  <?php if(isset($this->downloadCount)) { ?>
                <span class="sesalbum_list_grid_download" title="<?php echo $this->translate(array('%s download', '%s downloads', $item->download_count), $this->locale()->toNumber($item->download_count))?>">
                  <i class="fa fa-download"></i> 
                  <?php echo $item->download_count;?>            
                </span>
              <?php } ?>

                    </span>
                </p>         
              <?php } ?>
        </div>
      <?php }else{ ?>
        <div class="sesalbum_list_grid_thumb sesalbum_list_grid sesa-i-<?php echo (isset($this->insideOutside) && $this->insideOutside == 'outside') ? 'outside' : 'inside'; ?> sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
          <a class="sesalbum_list_grid_img" href="<?php echo Engine_Api::_()->sesalbum()->getHref($item->getIdentity(),$item->album_id); ?>" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;">
            <span class="main_image_container" style="background-image: url(<?php echo $item->getPhotoUrl('thumb.normalmain'); ?>);"></span>
            <div class="ses_image_container" style="display:none;">
              <?php $image = Engine_Api::_()->sesalbum()->getAlbumPhoto($item->getIdentity(),$item->photo_id); 
                    foreach($image as $key=>$valuePhoto){?>
                     <div class="child_image_container"><?php echo Engine_Api::_()->sesalbum()->photoUrlGet($valuePhoto->photo_id,'thumb.normalmain');  ?></div>
              <?php  }  ?>  
              <div class="child_image_container"><?php echo $item->getPhotoUrl('thumb.normalmain'); ?></div>          
            </div>
          </a>
          <?php if(isset($this->socialSharing) || isset($this->likeButton) || isset($this->favouriteButton)){  ?>
            <span class="sesalbum_list_grid_btns">
             <?php if(isset($this->socialSharing)){ 
              //album viewpage link for sharing
                $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref());
             ?>
              <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $item->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_icon_btn sesalbum_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
              <a href="<?php echo 'https://twitter.com/intent/tweet?url=' . $urlencode . '&text=' . $item->getTitle().'%0a'; ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_icon_btn sesalbum_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
              <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($item->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $item->getPhotoUrl('thumb.main')) . $item->getPhotoUrl('thumb.main')); ?>&description=<?php echo $item->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
              <?php }
              $canComment =  $item->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
        if(isset($this->likeButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && $canComment){  ?>
              <!--Album Like Button-->
              <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatus($item->album_id); ?>
                <a href="javascript:;" data-src='<?php echo $item->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_albumlike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                  <i class="fa fa-thumbs-up"></i>
                  <span><?php echo $item->like_count; ?></span>
                </a>
              <?php } 
              	$canFavourite =  Engine_Api::_()->authorization()->isAllowed('album',Engine_Api::_()->user()->getViewer(), 'favourite_album');
              if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton) && $canFavourite){
                    $albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album','resource_id'=>$item->album_id)); ?>
                <a href="javascript:;" data-src='<?php echo $item->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_fav_btn sesalbum_albumFav <?php echo ($albumFavStatus)>0 ? 'button_active' : '' ; ?>">
                  <i class="fa fa-heart"></i>
                  <span><?php echo $item->favourite_count; ?></span>
                </a>
            <?php } ?>
            </span>
          <?php } ?>
          <?php if(isset($this->featured) || isset($this->sponsored)){ ?>
          	<span class="sesalbum_labels_container">
              <?php if(isset($this->featured) && $item->is_featured == 1){ ?>
                <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
              <?php } ?>
            <?php if(isset($this->sponsored)  && $item->is_sponsored == 1){ ?>
            	<span class="sesalbum_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
            <?php } ?>
          </span>
         <?php } ?>
         <?php if(isset($this->like) || isset($this->comment) || isset($this->view) || isset($this->title) || isset($this->rating) || isset($this->photoCount) || isset($this->favouriteCount) || isset($this->downloadCount)  || isset($this->by)){ ?>
              <p class="sesalbum_list_grid_info sesbasic_clearfix<?php if(!isset($this->photoCount)) { ?> nophotoscount<?php } ?>">
              <?php if(isset($this->title)) { ?>
                <span class="sesalbum_list_grid_title">
                  <?php echo $this->htmlLink($item, $this->string()->truncate($item->getTitle(), $this->title_truncation),array('title'=>$item->getTitle())) ; ?>
                </span>
              <?php } ?>
              <span class="sesalbum_list_grid_stats">
                <?php if(isset($this->by)) { ?>
                  <span class="sesalbum_list_grid_owner">
                    <?php echo $this->translate('By');?>
                   <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                  </span>
                <?php }?>
                <?php if(isset($this->rating) && $ratingShowAlbum) { ?>
                   <?php
              	$user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album',$item->album_id);
                $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                $textRatingText = $item->rating == 1 ? 'rating' : 'ratings'; ?>
               	<span class="sesalbum_list_grid_rating" title="<?php echo $item->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                    <?php if( $item->rating > 0 ): ?>
                      <?php for( $x=1; $x<= $item->rating; $x++ ): ?>
                      	<span class="sesbasic_rating_star_small fa fa-star"></span>
                      <?php endfor; ?>
                      <?php if( (round($item->rating) - $item->rating) > 0): ?>
                      	<span class="sesbasic_rating_star_small fa fa-star-half"></span>
                      <?php endif; ?>
                    <?php endif; ?> 
                  </span>
                <?php } ?>
              </span>
              <span class="sesalbum_list_grid_stats sesbasic_text_light">
                <?php if(isset($this->like)) { ?>
                  <span class="sesalbum_list_grid_likes" title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count))?>">
                    <i class="fa fa-thumbs-up"></i>
                    <?php echo $item->like_count;?>
                  </span>
                <?php } ?>
                <?php if(isset($this->comment)) { ?>
                  <span class="sesalbum_list_grid_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count))?>">
                    <i class="fa fa-comment"></i>
                    <?php echo $item->comment_count;?>
                  </span>
               <?php } ?>
               <?php if(isset($this->view)) { ?>
                  <span class="sesalbum_list_grid_views" title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>">
                    <i class="fa fa-eye"></i>
                    <?php echo $item->view_count;?>
                  </span>
               <?php } ?>
               <?php if(isset($this->favouriteCount)) { ?>
                  <span class="sesalbum_list_grid_fav" title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>">
                    <i class="fa fa-heart"></i> 
                    <?php echo $item->favourite_count;?>            
                  </span>
                <?php } ?>
                <?php if(isset($this->downloadCount)) { ?>
                <span class="sesalbum_list_grid_download" title="<?php echo $this->translate(array('%s download', '%s downloads', $item->download_count), $this->locale()->toNumber($item->download_count))?>">
                  <i class="fa fa-download"></i> 
                  <?php echo $item->download_count;?>            
                </span>
              <?php } ?>
                 <?php if(isset($this->photoCount)) { ?>
               	<span class="sesalbum_list_grid_count" title="<?php echo $this->translate(array('%s photo', '%s photos', $item->count()), $this->locale()->toNumber($item->count()))?>" >
                  <i class="fa fa-photo"></i> 
                  <?php echo $item->count();?>                
               	</span>
                <?php } ?>
                </span>
              </p>
         <?php } ?>
         		<?php if(isset($this->photoCount)) { ?>
              <p class="sesalbum_list_grid_count">
                <?php echo $this->translate(array('%s <span>photo</span>', '%s <span>photos</span>', $item->count()),$this->locale()->toNumber($item->count())); ?>
              </p>
              <?php } ?>
        </div>
      <?php } ?>
    <?php $limit++;
    			} ?>
  </div>
  <?php if($this->align == 'horizontal'): ?>
    <div class="tabs_<?php echo $randonNumber; ?> sesalbum_carousel_nav">
      <a class="sesalbum_carousel_nav_pre" href="#page-p"><i class="fa fa-caret-left"></i></a>
      <a class="sesalbum_carousel_nav_nxt" href="#page-p"><i class="fa fa-caret-right"></i></a>
    </div>  
  <?php else: ?>
    <div class="tabs_<?php echo $randonNumber; ?> sesalbum_carousel_nav">
      <a class="sesalbum_carousel_nav_pre" href="#page-p"><i class="fa fa-caret-up"></i></a>
      <a class="sesalbum_carousel_nav_nxt" href="#page-p"><i class="fa fa-caret-down"></i></a>
    </div>  
  <?php endif; ?>

</div>
<script type="text/javascript">
	window.addEvent('domready', function () {
		var duration = <?php echo $this->duration; ?>,
			div = document.getElement('div.tabs_<?php echo $randonNumber; ?>');
			links = div.getElements('a'),
			carousel = new Carousel.Extra({
				activeClass: 'selected',
				container: 'sesalbum_slider_<?php echo $randonNumber; ?>',
				circular: true,
				current: 1,
				previous: links.shift(),
				next: links.pop(),
				tabs: links,
			  mode: '<?php echo $this->align; ?>',
				fx: {
					duration: duration
				}
			})
	})
</script>