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
            
    			//foreach( $this->paginator as $photo ):?>
          <?php if($this->typeWidget == 'photos'){ ?>
          	<?php $itemPhoto = Engine_Api::_()->getItem('photo',$this->paginator->photo_id); 
           ?>
            <li id="thumbs-photo-<?php echo $itemPhoto->photo_id ?>" class="ses_album_image_viewer sesalbum_list_grid_thumb sesalbum_list_photo_grid sesalbum_list_grid  sesa-i-<?php echo (isset($this->insideOutside) && $this->insideOutside == 'outside') ? 'outside' : 'inside'; ?> sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
              <?php $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($itemPhoto,array('status'=>'offtheday','limit'=>$limit)); ?>
              <a class="sesalbum_list_grid_img ses-image-viewer" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $itemPhoto->getPhotoUrl(); ?>','<?php echo $imageURL	; ?>')" href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($itemPhoto->getIdentity(),$itemPhoto->album_id); ?>" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;"> 
                <span style="background-image: url(<?php echo $itemPhoto->getPhotoUrl('thumb.normalmain'); ?>);"></span> 
              </a>
              <?php 
        if(isset($this->socialSharing) || isset($this->likeButton) || isset($this->favouriteButton)){
           //album viewpage link for sharing
          $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $itemPhoto->getHref()); ?>
      <span class="sesalbum_list_grid_btns">
      <?php if(isset($this->socialSharing)){ ?>
        <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $itemPhoto->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_icon_btn sesalbum_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
        <a href="<?php echo 'https://twitter.com/intent/tweet?url=' . $urlencode . '&text=' . $itemPhoto->getTitle().'%0a'; ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_icon_btn sesalbum_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
        <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($itemPhoto->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $itemPhoto->getPhotoUrl('thumb.main')) . $itemPhoto->getPhotoUrl('thumb.main')); ?>&description=<?php echo $itemPhoto->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
        <?php } 
        $canComment =  Engine_Api::_()->getItem('album',$itemPhoto->album_id )->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
        	if(isset($this->likeButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && $canComment){
        ?>
                <!--Album Like Button-->
                <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatusPhoto($itemPhoto->photo_id); ?>
                <a href="javascript:;" data-src='<?php echo $itemPhoto->photo_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_photolike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                  <i class="fa fa-thumbs-up"></i>
                  <span><?php echo $itemPhoto->like_count; ?></span>
                </a>
                  <?php } 
                  $canFavourite =  Engine_Api::_()->authorization()->isAllowed('album',Engine_Api::_()->user()->getViewer(), 'favourite_photo');
                  if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton) && $canFavourite){
                  $albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album_photo','resource_id'=>$itemPhoto->photo_id)); ?>
                    <a href="javascript:;" data-src='<?php echo $itemPhoto->photo_id; ?>' class="sesalbum_icon_btn sesalbum_icon_fav_btn sesalbum_photoFav <?php echo ($albumFavStatus)>0 ? 'button_active' : '' ; ?>">
                      <i class="fa fa-heart"></i>
                      <span><?php echo $itemPhoto->favourite_count; ?></span>
                    </a>
                  <?php } ?>
              </span>
            <?php } ?>
              <?php if(isset($this->featured) || isset($this->sponsored)){ ?>
                <span class="sesalbum_labels_container">
                  <?php if(isset($this->featured) && $itemPhoto->is_featured == 1){ ?>
                    <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
                  <?php } ?>
                  <?php if(isset($this->sponsored)  && $itemPhoto->is_sponsored == 1){ ?>
                    <span class="sesalbum_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
                  <?php } ?>
                </span>
              <?php } ?>
              <?php if(isset($this->like) || isset($this->comment) || isset($this->view) || isset($this->title) || isset($this->rating) || isset($this->favouriteCount) || isset($this->downloadCount)  || isset($this->by)){ ?>
                <p class="sesalbum_list_grid_info sesbasic_clearfix">
                  <?php if(isset($this->title)) { ?>
                    <span class="sesalbum_list_grid_title">
                      <?php echo $this->htmlLink($itemPhoto, $this->htmlLink($itemPhoto, $this->string()->truncate($itemPhoto->getTitle(), $this->title_truncation), array('title'=>$itemPhoto->getTitle()))) ?>
                    </span>
                  <?php } ?>
                  <span class="sesalbum_list_grid_stats">
                    <?php if(isset($this->by)) { ?>
                      <span class="sesalbum_list_grid_owner">
                        <?php echo $this->translate('By');?>
                        <?php echo $this->htmlLink($itemPhoto->getOwner()->getHref(), $itemPhoto->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                      </span>
                    <?php }?>
                    <?php if(isset($this->rating) && $ratingShowPhoto) { ?>
                     <?php
              	$user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album_photo',$itemPhoto->photo_id);
                $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                $textRatingText = $itemPhoto->rating == 1 ? 'rating' : 'ratings'; ?>
               	<span class="sesalbum_list_grid_rating" title="<?php echo $itemPhoto->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                        <?php if( $itemPhoto->rating > 0 ): ?>
                          <?php for( $x=1; $x<= $itemPhoto->rating; $x++ ): ?>
                            <span class="sesbasic_rating_star_small fa fa-star"></span>
                          <?php endfor; ?>
                          <?php if( (round($itemPhoto->rating) - $itemPhoto->rating) > 0): ?>
                            <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                          <?php endif; ?>
                      	<?php endif; ?> 
                    	</span>
                    <?php } ?>
                  </span>
                  <span class="sesalbum_list_grid_stats sesbasic_text_light">
                    <?php if(isset($this->like)) { ?>
                      <span class="sesalbum_list_grid_likes" title="<?php echo $this->translate(array('%s like', '%s likes', $itemPhoto->like_count), $this->locale()->toNumber($itemPhoto->like_count))?>">
                        <i class="fa fa-thumbs-up"></i>
                        <?php echo $itemPhoto->like_count;?>
                      </span>
                    <?php } ?>
                  <?php if(isset($this->comment)) { ?>
                    <span class="sesalbum_list_grid_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $itemPhoto->comment_count), $this->locale()->toNumber($itemPhoto->comment_count))?>">
                      <i class="fa fa-comment"></i>
                      <?php echo $itemPhoto->comment_count;?>
                    </span>
                 <?php } ?>
                 <?php if(isset($this->view)) { ?>
                  <span class="sesalbum_list_grid_views" title="<?php echo $this->translate(array('%s view', '%s views', $itemPhoto->view_count), $this->locale()->toNumber($itemPhoto->view_count))?>">
                    <i class="fa fa-eye"></i>
                    <?php echo $itemPhoto->view_count;?>
                  </span>
                 <?php } ?>
                 <?php if(isset($this->favouriteCount)) { ?>
                    <span class="sesalbum_list_grid_fav" title="<?php echo $this->translate(array('%s favourite', '%s favourites', $itemPhoto->favourite_count), $this->locale()->toNumber($itemPhoto->favourite_count))?>">
                      <i class="fa fa-heart"></i> 
                      <?php echo $itemPhoto->favourite_count;?>            
                    </span>
                  <?php } ?>
                  <?php if(isset($this->downloadCount)) { ?>
                <span class="sesalbum_list_grid_download" title="<?php echo $this->translate(array('%s download', '%s downloads', $itemPhoto->download_count), $this->locale()->toNumber($itemPhoto->download_count))?>">
                  <i class="fa fa-download"></i> 
                  <?php echo $itemPhoto->download_count;?>            
                </span>
              <?php } ?>
              </span>
                </p>         
              <?php } ?>   
            </li>
          <?php }else{ 
             			 $itemAlbum = Engine_Api::_()->getItem('album',$this->paginator->album_id);
                   if($itemAlbum){
          ?> 
            <li id="thumbs-photo-<?php echo $itemAlbum->photo_id ?>" class="sesalbum_list_grid_thumb sesalbum_list_grid sesa-i-<?php echo (isset($this->insideOutside) && $this->insideOutside == 'outside') ? 'outside' : 'inside'; ?> sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">  
              <?php $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($itemAlbum,array('status'=>$this->type,'limit'=>$limit)); ?>
              <a class="sesalbum_list_grid_img" href="<?php echo Engine_Api::_()->sesalbum()->getHref($itemAlbum->getIdentity(),$itemAlbum->album_id); ?>" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;">
                <span class="main_image_container" style="background-image: url(<?php echo $itemAlbum->getPhotoUrl('thumb.normalmain'); ?>);"></span>
              <div class="ses_image_container" style="display:none;">
                <?php $image = Engine_Api::_()->sesalbum()->getAlbumPhoto($itemAlbum->getIdentity(),$itemAlbum->photo_id); 
                      foreach($image as $key=>$valuePhoto){?>
                       <div class="child_image_container"><?php echo Engine_Api::_()->sesalbum()->photoUrlGet($valuePhoto->photo_id,'thumb.normalmain');  ?></div>
                 <?php  }  ?>  
                 <div class="child_image_container"><?php echo $itemAlbum->getPhotoUrl('thumb.normalmain'); ?></div>          
                </div>
              </a>
              <?php  if(isset($this->socialSharing) || isset($this->likeButton) || isset($this->favouriteButton)){  ?>
      <span class="sesalbum_list_grid_btns">
       <?php if(isset($this->socialSharing)){ 
       	//album viewpage link for sharing
          $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $itemAlbum->getHref());
       ?>
        <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $itemAlbum->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_icon_btn sesalbum_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
        <a href="<?php echo 'https://twitter.com/intent/tweet?url=' . $urlencode . '&text=' . $itemAlbum->getTitle().'%0a'; ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_icon_btn sesalbum_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
        <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($itemAlbum->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $itemAlbum->getPhotoUrl('thumb.main')) . $itemAlbum->getPhotoUrl('thumb.main')); ?>&description=<?php echo $itemAlbum->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
        <?php }
        $canComment =  $itemAlbum->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
        	if(isset($this->likeButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && $canComment){  ?>
                <!--Album Like Button-->
                <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatus($itemAlbum->album_id); ?>
                <a href="javascript:;" data-src='<?php echo $itemAlbum->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_albumlike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                  <i class="fa fa-thumbs-up"></i>
                  <span><?php echo $itemAlbum->like_count; ?></span>
                </a>
              <?php } 
              $canFavourite =  Engine_Api::_()->authorization()->isAllowed('album',Engine_Api::_()->user()->getViewer(), 'favourite_album');
              if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton) && $canFavourite){
                 $albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album','resource_id'=>$itemAlbum->album_id)); ?>
                  <a href="javascript:;" data-src='<?php echo $itemAlbum->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_fav_btn sesalbum_albumFav <?php echo ($albumFavStatus)>0 ? 'button_active' : '' ; ?>">
                    <i class="fa fa-heart"></i>
                    <span><?php echo $itemAlbum->favourite_count; ?></span>
                  </a>
              <?php } ?>
              </span>
         <?php } ?>
          <?php if(isset($this->featured) || isset($this->sponsored)){ ?>
          	<span class="sesalbum_labels_container">
              <?php if(isset($this->featured) && $itemAlbum->is_featured == 1){ ?>
                <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
              <?php } ?>
            <?php if(isset($this->sponsored)  && $itemAlbum->is_sponsored == 1){ ?>
            	<span class="sesalbum_label_sponsored"><?php echo $this->translate("Sponsored"); ?></span>
            <?php } ?>
          </span>
         <?php } ?>
         <?php if(isset($this->like) || isset($this->comment) || isset($this->view) || isset($this->title) || isset($this->rating) || isset($this->photoCount) || isset($this->favouriteCount) || isset($this->downloadCount)  || isset($this->by)){ ?>
            <p class="sesalbum_list_grid_info sesbasic_clearfix<?php if(!isset($this->photoCount)) { ?> nophotoscount<?php } ?>">
            <?php if(isset($this->title)) { ?>
              <span class="sesalbum_list_grid_title">
                <?php echo $this->htmlLink($itemAlbum, $this->string()->truncate($itemAlbum->getTitle(), $this->title_truncation),array('title'=>$itemAlbum->getTitle())) ; ?>
              </span>
            <?php } ?>
            <span class="sesalbum_list_grid_stats">
              <?php if(isset($this->by)) { ?>
                <span class="sesalbum_list_grid_owner">
                  <?php echo $this->translate('By');?>
                 <?php echo $this->htmlLink($itemAlbum->getOwner()->getHref(), $itemAlbum->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                </span>
              <?php }?>
             	<?php if(isset($this->rating) && $ratingShowAlbum) { ?>
              	 <?php
              	$user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album',$itemAlbum->album_id);
                $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                $textRatingText = $itemAlbum->rating == 1 ? 'rating' : 'ratings'; ?>
               	<span class="sesalbum_list_grid_rating" title="<?php echo $itemAlbum->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                  <?php if( $itemAlbum->rating > 0 ): ?>
                    <?php for( $x=1; $x<= $itemAlbum->rating; $x++ ): ?>
                      <span class="sesbasic_rating_star_small fa fa-star"></span>
                    <?php endfor; ?>
                    <?php if( (round($itemAlbum->rating) - $itemAlbum->rating) > 0): ?>
                      <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                    <?php endif; ?>
                  <?php endif; ?> 
                </span>
            	<?php } ?>
            </span>
            <span class="sesalbum_list_grid_stats sesbasic_text_light">
              <?php if(isset($this->like)) { ?>
                <span class="sesalbum_list_grid_likes" title="<?php echo $this->translate(array('%s like', '%s likes', $itemAlbum->like_count), $this->locale()->toNumber($itemAlbum->like_count))?>">
                  <i class="fa fa-thumbs-up"></i>
                  <?php echo $itemAlbum->like_count;?>
                </span>
              <?php } ?>
              <?php if(isset($this->comment)) { ?>
                <span class="sesalbum_list_grid_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $itemAlbum->comment_count), $this->locale()->toNumber($itemAlbum->comment_count))?>">
                  <i class="fa fa-comment"></i>
                  <?php echo $itemAlbum->comment_count;?>
                </span>
             <?php } ?>
             <?php if(isset($this->view)) { ?>
                <span class="sesalbum_list_grid_views" title="<?php echo $this->translate(array('%s view', '%s views', $itemAlbum->view_count), $this->locale()->toNumber($itemAlbum->view_count))?>">
                  <i class="fa fa-eye"></i>
                  <?php echo $itemAlbum->view_count;?>
                </span>
             <?php } ?>
             <?php if(isset($this->favouriteCount)) { ?>
                <span class="sesalbum_list_grid_fav" title="<?php echo $this->translate(array('%s favourite', '%s favourites', $itemAlbum->favourite_count), $this->locale()->toNumber($itemAlbum->favourite_count))?>">
                  <i class="fa fa-heart"></i> 
                  <?php echo $itemAlbum->favourite_count;?>            
                </span>
              <?php } ?>
               <?php if(isset($this->downloadCount)) { ?>
                <span class="sesalbum_list_grid_download" title="<?php echo $this->translate(array('%s download', '%s downloads', $itemAlbum->download_count), $this->locale()->toNumber($itemAlbum->download_count))?>">
                  <i class="fa fa-download"></i> 
                  <?php echo $itemAlbum->download_count;?>            
                </span>
              <?php } ?>
               <?php if(isset($this->photoCount)) { ?>
              <span class="sesalbum_list_grid_count" title="<?php echo $this->translate(array('%s photo', '%s photos', $itemAlbum->count()), $this->locale()->toNumber($itemAlbum->count()))?>" >
                <i class="fa fa-photo"></i> 
                <?php echo $itemAlbum->count();?>                
              </span>
              <?php } ?>

              </span>
            </p>
         <?php } ?>
         		<?php if(isset($this->photoCount)) { ?>
              <p class="sesalbum_list_grid_count">
                <?php echo $this->translate(array('%s <span>photo</span>', '%s <span>photos</span>', $itemAlbum->count()),$this->locale()->toNumber($itemAlbum->count())); ?>
              </p>
              <?php } ?>
            </li>
          <?php  }} ?>
    <?php $limit++;
    			//endforeach;?>
  </ul>