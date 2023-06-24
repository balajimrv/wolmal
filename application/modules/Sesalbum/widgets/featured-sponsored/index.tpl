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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesalbum/externals/styles/styles.css'); ?>
<ul class="sesalbum_album_listing sesbasic_bxs">
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
    			foreach( $this->paginator as $photo ): ?>
          <?php if($this->typeWidget == 'photo'){ ?>
            <li id="thumbs-photo-<?php echo $photo->photo_id ?>" class="ses_album_image_viewer sesalbum_list_grid_thumb sesalbum_list_photo_grid sesalbum_list_grid  sesa-i-<?php echo (isset($this->insideOutside) && $this->insideOutside == 'outside') ? 'outside' : 'inside'; ?> sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
              <?php $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($photo,array('status'=>$this->type,'limit'=>$limit,'type'=>$this->specialType)); ?>
              <a class="sesalbum_list_grid_img ses-image-viewer" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $photo->getPhotoUrl(); ?>','<?php echo $imageURL	; ?>')" href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($photo->getIdentity(),$photo->album_id); ?>" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;"> 
                <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normalmain'); ?>);"></span> 
              </a>
              <?php 
        if(isset($this->socialSharing) || isset($this->likeButton) || isset($this->favouriteButton)){
           //album viewpage link for sharing
          $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $photo->getHref()); ?>
      <span class="sesalbum_list_grid_btns">
      <?php if(isset($this->socialSharing)){ ?>
        <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $photo->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_icon_btn sesalbum_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
        <a href="<?php echo 'https://twitter.com/intent/tweet?url=' . $urlencode . '&text=' . $photo->getTitle().'%0a'; ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_icon_btn sesalbum_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
        <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($photo->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $photo->getPhotoUrl('thumb.main')) . $photo->getPhotoUrl('thumb.main')); ?>&description=<?php echo $photo->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
        <?php } 
            $album = Engine_Api::_()->getItem('album',$photo->album_id );
            if($album) {
        		$canComment =  $album->authorization()->isAllowed($this->viewer, 'comment');
        	if(isset($this->likeButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && $canComment){
        ?>
                <!--Album Like Button-->
                <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatusPhoto($photo->photo_id); ?>
                <a href="javascript:;" data-src='<?php echo $photo->photo_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_photolike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                  <i class="fa fa-thumbs-up"></i>
                  <span><?php echo $photo->like_count; ?></span>
                </a>
                  <?php } }
                 if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton)){
                  $albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album_photo','resource_id'=>$photo->photo_id)); ?>
                    <a href="javascript:;" data-src='<?php echo $photo->photo_id; ?>' class="sesalbum_icon_btn sesalbum_icon_fav_btn sesalbum_photoFav <?php echo ($albumFavStatus)>0 ? 'button_active' : '' ; ?>">
                      <i class="fa fa-heart"></i>
                      <span><?php echo $photo->favourite_count; ?></span>
                    </a>
                  <?php } ?>
              </span>
      <?php } ?>
          <?php if(isset($this->featured) || isset($this->sponsored)){ ?>
          	<span class="sesalbum_labels_container">
              <?php if(isset($this->featured) && $photo->is_featured == 1){ ?>
                <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
              <?php } ?>
            <?php if(isset($this->sponsored)  && $photo->is_sponsored == 1){ ?>
            	<span class="sesalbum_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
            <?php } ?>
          </span>
         <?php } ?>
         <?php if(isset($this->like) || isset($this->comment) || isset($this->view) || isset($this->title) || isset($this->rating) || isset($this->favouriteCount) || isset($this->downloadCount) || isset($this->by)){ ?>
            <p class="sesalbum_list_grid_info sesbasic_clearfix">
            <?php if(isset($this->title)) { ?>
              <span class="sesalbum_list_grid_title">
                <?php echo $this->htmlLink($photo, $this->string()->truncate($photo->getTitle(), $this->title_truncation),array('title'=>$photo->getTitle())) ; ?>
              </span>
            <?php } ?>
            <span class="sesalbum_list_grid_stats">
              <?php if(isset($this->by)) { ?>
                <span class="sesalbum_list_grid_owner">
                  <?php echo $this->translate('By');?>
                 <?php echo $this->htmlLink($photo->getOwner()->getHref(), $photo->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                </span>
              <?php }?>
             	<?php if(isset($this->rating) && $ratingShowPhoto) { ?>
              <?php
              	$user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album_photo',$photo->photo_id);
                $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                $textRatingText = $photo->rating == 1 ? 'rating' : 'ratings'; ?>
               	<span class="sesalbum_list_grid_rating" title="<?php echo $photo->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                  <?php if( $photo->rating > 0 ): ?>
                    <?php for( $x=1; $x<= $photo->rating; $x++ ): ?>
                      <span class="sesbasic_rating_star_small fa fa-star"></span>
                    <?php endfor; ?>
                    <?php if( (round($photo->rating) - $photo->rating) > 0): ?>
                      <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                    <?php endif; ?>
              		<?php endif; ?> 
                </span>
              <?php } ?>
            </span>
            <span class="sesalbum_list_grid_stats sesbasic_text_light">
              <?php if(isset($this->like)) { ?>
                <span class="sesalbum_list_grid_likes" title="<?php echo $this->translate(array('%s like', '%s likes', $photo->like_count), $this->locale()->toNumber($photo->like_count))?>">
                  <i class="fa fa-thumbs-up"></i>
                  <?php echo $photo->like_count;?>
                </span>
              <?php } ?>
              <?php if(isset($this->comment)) { ?>
                <span class="sesalbum_list_grid_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $photo->comment_count), $this->locale()->toNumber($photo->comment_count))?>">
                  <i class="fa fa-comment"></i>
                  <?php echo $photo->comment_count;?>
                </span>
             <?php } ?>
             <?php if(isset($this->view)) { ?>
                <span class="sesalbum_list_grid_views" title="<?php echo $this->translate(array('%s view', '%s views', $photo->view_count), $this->locale()->toNumber($photo->view_count))?>">
                  <i class="fa fa-eye"></i>
                  <?php echo $photo->view_count;?>
                </span>
             <?php } ?>
             <?php if(isset($this->favouriteCount)) { ?>
                <span class="sesalbum_list_grid_fav" title="<?php echo $this->translate(array('%s favourite', '%s favourites', $photo->favourite_count), $this->locale()->toNumber($photo->favourite_count))?>">
                  <i class="fa fa-heart"></i> 
                  <?php echo $photo->favourite_count;?>            
                </span>
              <?php } ?>
              <?php if(isset($this->downloadCount)) { ?>
                <span class="sesalbum_list_grid_download" title="<?php echo $this->translate(array('%s download', '%s downloads', $photo->download_count), $this->locale()->toNumber($photo->download_count))?>">
                  <i class="fa fa-download"></i> 
                  <?php echo $photo->download_count;?>            
                </span>
              <?php } ?>
            	</span>
            </p>
         <?php } ?>   
            </li>
          <?php }else{    
          	$canComment =  $photo->authorization()->isAllowed($this->viewer, 'comment');
          ?> 
            <li id="thumbs-photo-<?php echo $photo->photo_id ?>" class="sesalbum_list_grid_thumb sesalbum_list_grid sesa-i-<?php echo (isset($this->insideOutside) && $this->insideOutside == 'outside') ? 'outside' : 'inside'; ?> sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">  
              <?php $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($photo,array('status'=>$this->type,'limit'=>$limit)); ?>
              <a class="sesalbum_list_grid_img" href="<?php echo Engine_Api::_()->sesalbum()->getHref($photo->getIdentity(),$photo->album_id); ?>" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;">
                <span class="main_image_container" style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normalmain'); ?>);"></span>
              <div class="ses_image_container" style="display:none;">
                <?php $image = Engine_Api::_()->sesalbum()->getAlbumPhoto($photo->getIdentity(),$photo->photo_id); 
                      foreach($image as $key=>$valuePhoto){?>
                       <div class="child_image_container"><?php echo Engine_Api::_()->sesalbum()->photoUrlGet($valuePhoto->photo_id,'thumb.normalmain');  ?></div>
                 <?php  }  ?>  
                 <div class="child_image_container"><?php echo $photo->getPhotoUrl('thumb.normalmain'); ?></div>          
                </div>
              </a>
              <?php  if(isset($this->socialSharing) || isset($this->likeButton) || isset($this->favouriteButton)){  ?>
                <span class="sesalbum_list_grid_btns">
                	<?php if(isset($this->socialSharing)){ 
                  //album viewpage link for sharing
                    $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $photo->getHref());
                 ?>
                  <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $photo->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_icon_btn sesalbum_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
                  <a href="<?php echo 'https://twitter.com/intent/tweet?url=' . $urlencode . '&text=' . $photo->getTitle().'%0a'; ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_icon_btn sesalbum_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
                  <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($photo->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $photo->getPhotoUrl('thumb.main')) . $photo->getPhotoUrl('thumb.main')); ?>&description=<?php echo $photo->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
        				  <?php }
        					if(isset($this->likeButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && $canComment){  ?>
                	<!--Album Like Button-->
                		<?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatus($photo->album_id); ?>
                		<a href="javascript:;" data-src='<?php echo $photo->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_albumlike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                  	<i class="fa fa-thumbs-up"></i>
                  	<span><?php echo $photo->like_count; ?></span>
                	</a>
                <?php } 
               if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton)){
                      $albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album','resource_id'=>$photo->album_id)); ?>
                  <a href="javascript:;" data-src='<?php echo $photo->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_fav_btn sesalbum_albumFav <?php echo ($albumFavStatus)>0 ? 'button_active' : '' ; ?>">
                    <i class="fa fa-heart"></i>
                    <span><?php echo $photo->favourite_count; ?></span>
                  </a>
              <?php } ?>
              </span>
         		<?php } ?>
          <?php if(isset($this->featured) || isset($this->sponsored)){ ?>
          	<span class="sesalbum_labels_container">
              <?php if(isset($this->featured) && $photo->is_featured == 1){ ?>
                <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
              <?php } ?>
            <?php if(isset($this->sponsored)  && $photo->is_sponsored == 1){ ?>
            	<span class="sesalbum_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
            <?php } ?>
          </span>
          <?php } ?>
          <?php if(isset($this->like) || isset($this->comment) || isset($this->view) || isset($this->title) || isset($this->rating) || isset($this->photoCount) || isset($this->favouriteCount) || isset($this->downloadCount)  || isset($this->by)){ ?>
            <p class="sesalbum_list_grid_info sesbasic_clearfix<?php if(!isset($this->photoCount)) { ?> nophotoscount<?php } ?>">
            <?php if(isset($this->title)) { ?>
              <span class="sesalbum_list_grid_title">
                <?php echo $this->htmlLink($photo, $this->string()->truncate($photo->getTitle(), $this->title_truncation),array('title'=>$photo->getTitle())) ; ?>
              </span>
            <?php } ?>
            <span class="sesalbum_list_grid_stats">
              <?php if(isset($this->by)) { ?>
                <span class="sesalbum_list_grid_owner">
                  <?php echo $this->translate('By');?>
                 <?php echo $this->htmlLink($photo->getOwner()->getHref(), $photo->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                </span>
              <?php }?>
             	<?php if(isset($this->rating) && $ratingShowAlbum) { ?>
              <?php
              	$user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album',$photo->album_id);
                $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                $textRatingText = $photo->rating == 1 ? 'rating' : 'ratings'; ?>
               	<span class="sesalbum_list_grid_rating" title="<?php echo $photo->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                  <?php if( $photo->rating > 0 ): ?>
                    <?php for( $x=1; $x<= $photo->rating; $x++ ): ?>
                      <span class="sesbasic_rating_star_small fa fa-star"></span>
                    <?php endfor; ?>
                    <?php if( (round($photo->rating) - $photo->rating) > 0): ?>
                      <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                    <?php endif; ?>
                  <?php endif; ?> 
              	</span>
            	<?php } ?>
            </span>
            <span class="sesalbum_list_grid_stats sesbasic_text_light">
              <?php if(isset($this->like)) { ?>
                <span class="sesalbum_list_grid_likes" title="<?php echo $this->translate(array('%s like', '%s likes', $photo->like_count), $this->locale()->toNumber($photo->like_count))?>">
                  <i class="fa fa-thumbs-up"></i>
                  <?php echo $photo->like_count;?>
                </span>
              <?php } ?>
              <?php if(isset($this->comment)) { ?>
                <span class="sesalbum_list_grid_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $photo->comment_count), $this->locale()->toNumber($photo->comment_count))?>">
                  <i class="fa fa-comment"></i>
                  <?php echo $photo->comment_count;?>
                </span>
             <?php } ?>
             <?php if(isset($this->view)) { ?>
                <span class="sesalbum_list_grid_views" title="<?php echo $this->translate(array('%s view', '%s views', $photo->view_count), $this->locale()->toNumber($photo->view_count))?>">
                  <i class="fa fa-eye"></i>
                  <?php echo $photo->view_count;?>
                </span>
             <?php } ?>
             <?php if(isset($this->favouriteCount)) { ?>
                <span class="sesalbum_list_grid_fav" title="<?php echo $this->translate(array('%s favourite', '%s favourites', $photo->favourite_count), $this->locale()->toNumber($photo->favourite_count))?>">
                  <i class="fa fa-heart"></i> 
                  <?php echo $photo->favourite_count;?>            
                </span>
              <?php } ?>
              <?php if(isset($this->downloadCount)) { ?>
                <span class="sesalbum_list_grid_download" title="<?php echo $this->translate(array('%s download', '%s downloads', $photo->download_count), $this->locale()->toNumber($photo->download_count))?>">
                  <i class="fa fa-download"></i> 
                  <?php echo $photo->download_count;?>            
                </span>
              <?php } ?>
               <?php if(isset($this->photoCount)) { ?>
              <span class="sesalbum_list_grid_count" title="<?php echo $this->translate(array('%s photo', '%s photos', $photo->count()), $this->locale()->toNumber($photo->count()))?>" >
                <i class="fa fa-photo"></i> 
                <?php echo $photo->count();?>                
              </span>
              <?php } ?>
            	</span>
            </p>
          <?php } ?>
          <?php if(isset($this->photoCount)) { ?>
            <p class="sesalbum_list_grid_count">
              <?php echo $this->translate(array('%s <span>photo</span>', '%s <span>photos</span>', $photo->count()),$this->locale()->toNumber($photo->count())); ?>
            </p>
            <?php } ?>
            </li>
          <?php  } ?>
    <?php $limit++;
    			endforeach;?>
  </ul>