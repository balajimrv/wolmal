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
<?php if(isset($this->albumPhotoOption) && $this->albumPhotoOption == 'photo' && $this->view_type  == 'pinboard' && !$this->is_ajax){
	 $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/pinboard.css'); 
   $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/wookmark.min.js');
   $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/imagesloaded.pkgd.js');
   $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/pinboardcomment.js');
 } ?>
<?php if(isset($this->identityForWidget) && !empty($this->identityForWidget)){
				$randonNumber = $this->identityForWidget;
      }else{
      	$randonNumber = $this->identity; 
       }?>
<?php if(!$this->is_ajax){ ?>
<!--Default Tabs-->
<?php if($this->tab_option == 'default'){ ?>
<div class="layout_core_container_tabs">
<div class="tabs_alt tabs_parent" id="sesalbum_tabbed_widget_container_<?php echo $randonNumber; ?>">
<?php } ?>
<!--Advance Tabs-->
<?php if($this->tab_option == 'advance'){ ?>
<div class="sesbasic_tabs_container sesbasic_clearfix sesbasic_bxs">
  <div class="sesbasic_tabs sesbasic_clearfix" id="sesalbum_tabbed_widget_container_<?php echo $randonNumber; ?>">
 <?php } ?>
<!--Filter Tabs-->
<?php if($this->tab_option == 'filter'){ ?>
<div class="sesbasic_filter_tabs_container sesbasic_clearfix sesbasic_bxs">
  <div class="sesbasic_filter_tabs sesbasic_clearfix" id="sesalbum_tabbed_widget_container_<?php echo $randonNumber; ?>">
<?php } ?>
    <ul id="tab-widget-sesalbum-<?php echo $randonNumber; ?>">
      <?php 
      				$defaultOptionArray = array();
            	foreach($this->defaultOptions as $key=>$valueOptions){
            	$defaultOptionArray[] = $key;
             ?>
        <li <?php if($this->defaultOpenTab == $key){ ?>class="active"<?php } ?> id="sesTabContainer_<?php echo $randonNumber; ?>_<?php echo $key; ?>">
          <a href="javascript:;" data-src="<?php echo $key; ?>" onclick="changeTabSes_<?php echo $randonNumber; ?>('<?php echo $key; ?>')">
		  <?php echo $this->translate($valueOptions); ?></a>
        </li>
      <?php } ?>
    </ul>
  </div>
  
  
  <div class="sesbasic_tabs_content sesbasic_clearfix sesbasic_bxs">
<?php  if(count($this->defaultOptions) == 1){ ?>
<script type="application/javascript">
	sesJqueryObject('#sesalbum_tabbed_widget_container_<?php echo $randonNumber; ?>').css('display','none');
</script>
<?php } ?>
  <div id="scrollHeightDivSes_<?php echo $randonNumber; ?>" class="sesbasic_clearfix">
    <ul class="sesalbum_listings sesalbum_tabbed_listings sesalbum_photos_flex_view sesbasic_bxs sesbasic_clearfix" id="tabbed-widget_<?php echo $randonNumber; ?>">
<?php } ?>
          <?php $limit = $this->limit;
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
          <?php if($this->albumPhotoOption == 'photo' && $this->view_type != 'pinboard'){ ?>
         <?php if($this->view_type != 'masonry'){ ?>
            <li id="thumbs-photo-<?php echo $photo->photo_id ?>" class="ses_album_image_viewer sesalbum_list_grid_thumb sesalbum_list_photo_grid sesalbum_list_grid  sesa-i-<?php echo (isset($this->insideOutside) && $this->insideOutside == 'outside') ? 'outside' : 'inside'; ?> sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
              <?php $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($photo,array('status'=>$this->type,'limit'=>$limit)); ?>
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
              $canComment =  Engine_Api::_()->getItem('album',$photo->album_id )->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
              if(isset($this->likeButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && $canComment){  ?>
                <!--Album Like Button-->
                <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatusPhoto($photo->photo_id); ?>
                <a href="javascript:;" data-src='<?php echo $photo->photo_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_photolike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                  <i class="fa fa-thumbs-up"></i>
                  <span><?php echo $photo->like_count; ?></span>
                </a>
                  <?php } 
                  $canFavourite =  Engine_Api::_()->authorization()->isAllowed('album',Engine_Api::_()->user()->getViewer(), 'favourite_photo');
                   if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton) && $canFavourite){
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
              <?php if(isset($this->like) || isset($this->comment) || isset($this->view) || isset($this->title) || isset($this->rating) || isset($this->favouriteCount) || isset($this->downloadCount)  || isset($this->by)){ ?>
                <p class="sesalbum_list_grid_info sesbasic_clearfix">
                  <?php if(isset($this->title)) { ?>
                    <span class="sesalbum_list_grid_title">
                      <?php echo $this->htmlLink($photo, $this->htmlLink($photo, $this->string()->truncate($photo->getTitle(), $this->title_truncation), array('title'=>$photo->getTitle()))) ?>
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
         <?php }else{ ?>
          <?php
    			$imageURL = $photo->getPhotoUrl('thumb.normalmain');
          if(strpos($imageURL,'http://') === FALSE && strpos($imageURL,'https://') === FALSE)
    					$imageGetSizeURL = $_SERVER['DOCUMENT_ROOT']. DIRECTORY_SEPARATOR . substr($imageURL, 0, strpos($imageURL, "?"));
          else
          	$imageGetSizeURL =$imageURL;
    			$imageHeightWidthData = getimagesize($imageGetSizeURL);           
          $width = isset($imageHeightWidthData[0]) ? $imageHeightWidthData[0] : '300';
          $height = isset($imageHeightWidthData[1]) ? $imageHeightWidthData[1] : '200'; 
          	$imageURL = $photo->getPhotoUrl();?>
         		<li id="thumbs-photo-<?php echo $photo->photo_id ?>" data-w="<?php echo $width ?>" data-h="<?php echo $height; ?>" class="ses_album_image_viewer sesalbum_list_flex_thumb sesalbum_list_photo_grid sesalbum_list_grid sesa-i-inside sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?>">
              <?php $imageViewerURL = Engine_Api::_()->sesalbum()->getImageViewerHref($photo,array('status'=>$this->type,'limit'=>$limit)); ?>
              <a class="sesalbum_list_flex_img ses-image-viewer" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $photo->getPhotoUrl(); ?>','<?php echo $imageViewerURL	; ?>')" href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($photo->getIdentity(),$photo->album_id); ?>"> 
                <img data-src="<?php echo $imageURL; ?>" src="<?php $this->layout()->staticBaseUrl; ?>application/modules/Sesalbum/externals/images/blank-img.gif" /> 
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
              	$canComment =  Engine_Api::_()->getItem('album',$photo->album_id )->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
              	if(isset($this->likeButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && $canComment){  ?>
                <!--Album Like Button-->
                <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatusPhoto($photo->photo_id); ?>
                <a href="javascript:;" data-src='<?php echo $photo->photo_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_photolike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                  <i class="fa fa-thumbs-up"></i>
                  <span><?php echo $photo->like_count; ?></span>
                </a>
                  <?php } 
                   $canFavourite =  Engine_Api::_()->authorization()->isAllowed('album',Engine_Api::_()->user()->getViewer(), 'favourite_photo');
                  if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton) && $canFavourite){
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
              <?php if(isset($this->like) || isset($this->comment) || isset($this->view) || isset($this->title) || isset($this->rating) || isset($this->favouriteCount) || isset($this->downloadCount)  || isset($this->by)){ ?>
                <p class="sesalbum_list_grid_info sesbasic_clearfix">
                  <?php if(isset($this->title)) { ?>
                    <span class="sesalbum_list_grid_title">
                      <?php echo $this->htmlLink($photo, $this->htmlLink($photo, $this->string()->truncate($photo->getTitle(), $this->title_truncation), array('title'=>$photo->getTitle()))) ?>
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
         <?php } ?>
          <?php }else if($this->albumPhotoOption == 'photo' && $this->view_type  == 'pinboard'){ ?>
        		  <li class="sesbasic_bxs sesbasic_pinboard_list_item_wrap new_image_pinboard_<?php echo $randonNumber; ?>" style="display:none;">
              	<div class="sesbasic_pinboard_list_item sesbasic_bm">
                	<div class="sesbasic_pinboard_list_item_top">
                  	<div class="sesbasic_pinboard_list_item_thumb">
                     <?php $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($photo,array('status'=>$this->type,'limit'=>$limit)); ?>
                  		<a href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($photo->getIdentity(),$photo->album_id); ?>" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $photo->getPhotoUrl(); ?>','<?php echo $imageURL	; ?>')" class="sesJqueryObject_thumb_img ses-image-viewer">
                      	<img src="<?php echo $photo->getPhotoUrl('thumb.normalmain'); ?>">
                        	<span style="background-image:url(<?php echo $photo->getPhotoUrl('thumb.normalmain'); ?>);display:none;"></span>
                      </a>
                    </div>            
                    <?php if(isset($this->featuredLabel) || isset($this->sponsoredLabel)){ ?>
                      <div class="sesbasic_pinboard_list_label">
                      <?php if(isset($this->featured) && $photo->is_featured == 1){ ?>
                        <span class="sesJqueryObject_label_featured">FEATURED</span>
                      <?php } ?>
                      <?php if(isset($this->sponsored)  && $photo->is_sponsored == 1){ ?>
                        <span class="sesJqueryObject_label_sponsored">SPONSORED</span>
                      <?php } ?>
                      </div>
                     <?php } ?>                    
                  <?php   if(isset($this->socialSharing) || isset($this->likeButton) || isset($this->favouriteButton)){
                    $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $photo->getHref()); ?>
                     <div class="sesbasic_pinboard_list_btns sesalbum_list_grid_btns"> 
                   <?php if(isset($this->socialSharing)){ ?>
                <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $photo->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_icon_btn sesalbum_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
                <a href="<?php echo 'https://twitter.com/intent/tweet?url=' . $urlencode . '&text=' . $photo->getTitle().'%0a'; ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_icon_btn sesalbum_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
                <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($photo->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $photo->getPhotoUrl('thumb.main')) . $photo->getPhotoUrl('thumb.main')); ?>&description=<?php echo $photo->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
              <?php }  
                          $canComment =  Engine_Api::_()->getItem('album',$photo->album_id )->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
                           if(isset($this->likeButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && $canComment){  
                        ?>
                      <!--Like Button-->
                     <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatusPhoto($photo->photo_id); ?>
                        <a href="javascript:;" data-src='<?php echo $photo->photo_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_photolike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                  <i class="fa fa-thumbs-up"></i>
                  <span><?php echo $photo->like_count; ?></span>
               					 </a>
                        <?php } 
                         $canFavourite =  Engine_Api::_()->authorization()->isAllowed('album',Engine_Api::_()->user()->getViewer(), 'favourite_photo');
                        if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton) && $canFavourite){
                        $albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album_photo','resource_id'=>$photo->photo_id));?>
                        <a href="javascript:;" data-src='<?php echo $photo->photo_id; ?>' class="sesalbum_icon_btn sesalbum_icon_fav_btn sesalbum_photoFav <?php echo ($albumFavStatus)>0 ? 'button_active' : '' ; ?>">
                      <i class="fa fa-heart"></i>
                      <span><?php echo $photo->favourite_count; ?></span>
                    </a>
                      
                    <?php  } ?>
                  </div>
                  <?php } ?>
                  </div>
               <?php if($photo->getTitle() || $photo->description){ ?>
                  <div class="sesbasic_pinboard_list_item_cont sesbasic_clearfix">
                  <?php if(isset($this->title)) { ?>
              			<div class="sesbasic_pinboard_list_item_title">
                    	<?php echo $this->htmlLink($photo, $this->htmlLink($photo, $this->string()->truncate($photo->getTitle(), $this->title_truncation), array('title'=>$photo->getTitle()))) ?>
                    </div>
                   <?php } ?>
                    <?php if(isset($this->description)){ ?>
                      <?php if(strlen($photo->description) > $this->description_truncation){ 
                          $description = mb_substr($photo->description,0,$this->description_truncation).'...';
                          echo $title = nl2br($description);
                         }else{ ?>
                  <?php  echo nl2br($photo->description);?>
                  <?php } ?>
                		<?php } ?>
                  </div>
                  <?php } ?>
                  <div class="sesbasic_pinboard_list_item_btm sesbasic_bm sesbasic_clearfix" style="margin-top:0px;">
                  	<?php if(isset($this->by)){ ?>    
                      <div class="sesbasic_pinboard_list_item_poster sesbasic_text_light sesbasic_clearfix">
                        <?php $owner = $photo->getOwner(); ?>
                        <div class="sesbasic_pinboard_list_item_poster_thumb">
                        	<?php echo $this->htmlLink($photo->getOwner()->getParent(), $this->itemPhoto($photo->getOwner()->getParent(), 'thumb.icon')); ?>
                        </div>
                        <div class="sesbasic_pinboard_list_item_poster_info">
                          <span class="sesbasic_pinboard_list_item_poster_info_title"><?php echo $this->htmlLink($owner->getHref(),$owner->getTitle() ) ?></span>
                            <div class="sesalbum_list_stats sesbasic_text_light">
                            <?php if(isset($this->like) && isset($photo->like_count)) { ?>
                              <span title="<?php echo $this->translate(array('%s like', '%s likes', $photo->like_count), $this->locale()->toNumber($photo->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $photo->like_count; ?></span>
                            <?php } ?>
                            <?php if(isset($this->comment) && isset($photo->comment_count)) { ?>
                              <span title="<?php echo $this->translate(array('%s comment', '%s comments', $photo->comment_count), $this->locale()->toNumber($photo->comment_count))?>"><i class="fa fa-comment"></i><?php echo $photo->comment_count;?></span>
                            <?php } ?>
                             <?php if(isset($this->favourite) && isset($photo->favourite_count)) { ?>
                                  <span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $photo->favourite_count), $this->locale()->toNumber($photo->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $photo->favourite_count;?></span>
                                <?php } ?>                          
                            <?php if(isset($this->view) && isset($photo->view_count)) { ?>
                              <span title="<?php echo $this->translate(array('%s view', '%s views', $photo->view_count), $this->locale()->toNumber($photo->view_count))?>"><i class="fa fa-eye"></i><?php echo $photo->view_count; ?></span>
                            <?php } ?>
                             <?php if(isset($this->downloadCount)) { ?>
                             <span title="<?php echo $this->translate(array('%s download', '%s downloads', $photo->download_count), $this->locale()->toNumber($photo->download_count))?>"><i class="fa fa-download"></i><?php echo $photo->download_count; ?></span>
                            <?php } ?>
                    		  </div>
                      <?php if(isset($this->rating) && $ratingShowPhoto) { ?>
                       <?php
                            $user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album_photo',$photo->photo_id);
                            $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                            $textRatingText = $photo->rating == 1 ? 'rating' : 'ratings'; ?>
                            <div class="sesJqueryObject_grid_date sesbasic_text_light clear" title="<?php echo $photo->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                               <?php if( $photo->rating > 0 ): ?>
                                <?php for( $x=1; $x<= $photo->rating; $x++ ): ?>
                                  <span class="sesbasic_rating_star_small fa fa-star"></span>
                                <?php endfor; ?>
                                <?php if( (round($photo->rating) - $photo->rating) > 0): ?>
                                  <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                                <?php endif; ?>
                              <?php endif; ?> 
                            </div>
                        <?php } ?>
                          </div>
                        </div>
                      <?php } ?>
                    <div class="sesbasic_pinboard_list_comments sesbasic_clearfix">
                    <?php echo !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedcomment') ? $this->action('list', 'comment', 'sesadvancedcomment', array('type' => $photo->getType(), 'id' => $photo->getIdentity(),'page'=>'')) : $this->action("list", "comment", "sesbasic", array("item_type" => $photo->getType(), "item_id" => $photo->getIdentity(),"widget_identity"=>$randonNumber)); ?>
                    </div>
                  </div>
              	</div>
              </li>
        <?php
        }else{ ?> 
            <li id="thumbs-photo-<?php echo $photo->photo_id ?>" class="sesalbum_list_grid_thumb sesalbum_list_grid sesa-i-<?php echo (isset($this->insideOutside) && $this->insideOutside == 'outside') ? 'outside' : 'inside'; ?> sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">  
              <a class="sesalbum_list_grid_img" href="<?php echo Engine_Api::_()->sesalbum()->getHref($photo->getIdentity(),$photo->album_id); ?>" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;">
                <span class="main_image_container" style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normalmain'); ?>);"></span>
              <div class="ses_image_container" style="display:none;">
                <?php $image = Engine_Api::_()->sesalbum()->getAlbumPhoto($photo->getIdentity(),$photo->photo_id); 
                      foreach($image as $key=>$valuePhoto){ ?>
                       <div class="child_image_container"><?php echo Engine_Api::_()->sesalbum()->photoUrlGet($valuePhoto->photo_id,'thumb.normalmain');  ?></div>
                 <?php  }  ?>  
                 <div class="child_image_container"><?php echo $photo->getPhotoUrl('thumb.normalmain'); ?></div>          
                </div>
              </a>
              <?php  if(isset($this->socialSharing) ||  isset($this->favouriteButton) || isset($this->likeButton)){  ?>
      <span class="sesalbum_list_grid_btns">
       <?php if(isset($this->socialSharing)){ 
       	//album viewpage link for sharing
          $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $photo->getHref());
       ?>
        <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $photo->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_icon_btn sesalbum_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
        <a href="<?php echo 'https://twitter.com/intent/tweet?url=' . $urlencode . '&text=' . $photo->getTitle().'%0a'; ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_icon_btn sesalbum_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
        <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($photo->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $photo->getPhotoUrl('thumb.main')) . $photo->getPhotoUrl('thumb.main')); ?>&description=<?php echo $photo->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
        <?php }
        $canComment =  $photo->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
        	if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->likeButton) && $canComment){ ?>
                <!--Album Like Button-->
                <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatus($photo->album_id); ?>
                <a href="javascript:;" data-src='<?php echo $photo->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_albumlike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                  <i class="fa fa-thumbs-up"></i>
                  <span><?php echo $photo->like_count; ?></span>
                </a>
              <?php } 
              $canFavourite =  Engine_Api::_()->authorization()->isAllowed('album',Engine_Api::_()->user()->getViewer(), 'favourite_album');
              	if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButton) && $canFavourite){
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
                <?php echo $this->translate(array('%s <span>photo</span>', '%s <span>photos</span>', $photo->count()),$this->locale()->toNumber($photo->count())) ?>
              </p>
              <?php  } ?>
            </li>
          <?php  } ?>
    <?php $limit++;
    			endforeach;
           if($this->loadOptionData == 'pagging' && $this->show_limited_data == 'no'){ ?>
             <?php echo $this->paginationControl($this->paginator, null, array("_pagging.tpl", "sesalbum"),array('identityWidget'=>$randonNumber)); ?>
         <?php } ?>
          <?php  if($this->paginator->getTotalItemCount() == 0){  ?>
            <div class="tip">
              <span>
                <?php echo $this->translate("There are currently no ".ucfirst($this->albumPhotoOption)."s.");?>
                 <?php if( $this->canCreate ): ?>
                  <?php echo $this->translate('Be the first to %1$screate%2$s one!', 
                     '<a href="'.$this->url(array('action' => 'create','controller'=>'index'),'sesalbum_general',true).'">', '</a>'); 
                  ?>
                  <?php endif; ?>
              </span>
            </div>    
    			<?php } ?>
    <?php if(!$this->is_ajax){ ?>
  </ul>
   <?php if($this->loadOptionData != 'pagging' && $this->show_limited_data == 'no'){ ?>
  <div class="sesbasic_view_more sesbasic_load_btn" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'sesbasic_animation sesbasic_link_btn fa fa-repeat')); ?> </div>
  <div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> <span class="sesbasic_link_btn"><i class="fa fa-spinner fa-spin"></i></span> </div>
  <?php } ?>
</div>
</div>
</div>
<script type="text/javascript">
var valueTabData ;
var params<?php echo $randonNumber; ?> = '<?php echo json_encode($this->params); ?>';
var identity<?php echo $randonNumber; ?>  = '<?php echo $randonNumber; ?>';
var searchParams<?php echo $randonNumber; ?>;
<?php if($this->hide_row){ ?>
	var truncateRow = true;
<?php }else{ ?>
  var truncateRow = false;
<?php } ?>
if("<?php echo $this->albumPhotoOption; ?>" == 'photo' && "<?php echo $this->view_type ; ?>" == 'masonry'){
		sesJqueryObject("#tabbed-widget_<?php echo $randonNumber; ?>").flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>,truncate:truncateRow});
	}
	function paggingNumber<?php echo $randonNumber; ?>(pageNum){
		 sesJqueryObject ('.overlay_<?php echo $randonNumber ?>').css('display','block');
		 var valueTab ;
		 sesJqueryObject('#tab-widget-sesalbum-<?php echo $randonNumber; ?> > li').each(function(index){
					if(sesJqueryObject(this).hasClass('active')){
					  valueTab = sesJqueryObject(this).find('a').attr('data-src');
					}
		 });
		 if(typeof valueTab == 'undefined')
		 	return false;
			(new Request.HTML({
				method: 'post',
				'url': en4.core.baseUrl + 'widget/index/mod/sesalbum/name/tabbed-widget/openTab/' + valueTab,
				'data': {
					format: 'html',
					page: pageNum,   
					searchParams : searchParams<?php echo $randonNumber; ?>, 
					params :params<?php echo $randonNumber; ?> , 
					is_ajax : 1,
					identity : identity<?php echo $randonNumber; ?>,
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					sesJqueryObject ('.overlay_<?php echo $randonNumber ?>').css('display','none');
					 if($('loadingimgsesalbum-wrapper'))
						sesJqueryObject('#loadingimgsesalbum-wrapper').hide();
					document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML =  responseHTML;
					if("<?php echo $this->view_type ; ?>" == 'pinboard'){
							pinboardLayout_<?php echo $randonNumber ?>();
					}
					if("<?php echo $this->albumPhotoOption; ?>" == 'photo' && "<?php echo $this->view_type ; ?>" == 'masonry'){
							sesJqueryObject("#tabbed-widget_<?php echo $randonNumber; ?>").flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>,truncate:truncateRow});
					}
				}
			})).send();
			return false;
	}
// globally define available tab array
	var availableTabs_<?php echo $randonNumber; ?>;
	var requestTab_<?php echo $randonNumber; ?>;
  availableTabs_<?php echo $randonNumber; ?> = <?php echo json_encode($defaultOptionArray); ?>;
<?php if($this->loadOptionData == 'auto_load' && $this->show_limited_data == 'no'){ ?>
		window.addEvent('load', function() {
		 sesJqueryObject(window).scroll( function() {
			  var heightOfContentDiv_<?php echo $randonNumber; ?> = sesJqueryObject('#scrollHeightDivSes_<?php echo $randonNumber; ?>').offset().top;
        var fromtop_<?php echo $randonNumber; ?> = sesJqueryObject(this).scrollTop();
        if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDiv_<?php echo $randonNumber; ?> - 100 && sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display') == 'block' ){
						document.getElementById('feed_viewmore_link_<?php echo $randonNumber; ?>').click();
				}
     });
	});
<?php } ?>
</script>
<?php } ?>
<?php if(!$this->is_ajax && $this->view_type == 'pinboard' && $this->albumPhotoOption  == 'photo'){ ?>
<script type="application/javascript">
//Code for Pinboard View
var wookmark<?php echo $randonNumber ?>;;
function pinboardLayout_<?php echo $randonNumber ?>(force,checkEnablePinboard){
		sesJqueryObject('.new_image_pinboard_<?php echo $randonNumber; ?>').css('display','none');
		var imgLoad = imagesLoaded('#tabbed-widget_<?php echo $randonNumber; ?>');
		imgLoad.on('progress',function(instance,image){
			sesJqueryObject(image.img).parent().parent().parent().parent().parent().show();
			sesJqueryObject(image.img).parent().parent().parent().parent().parent().removeClass('new_image_pinboard_<?php echo $randonNumber; ?>');
			imageLoadedAll<?php echo $randonNumber ?>(force,checkEnablePinboard);
		});
  }
	function imageLoadedAll<?php echo $randonNumber ?>(force,checkEnablePinboard){
		sesJqueryObject('#tabbed-widget_<?php echo $randonNumber; ?>').addClass('sesbasic_pinboard_<?php echo $randonNumber; ?>');
		if (typeof wookmark<?php echo $randonNumber ?> == 'undefined' || typeof force != 'undefined') {
				(function() {
		function getWindowWidth_<?php echo $randonNumber; ?>() {
			return Math.max(document.documentElement.clientWidth, window.innerWidth || 0)
		}				
		wookmark<?php echo $randonNumber ?> = new Wookmark('#tabbed-widget_<?php echo $randonNumber;?>', {
			itemWidth:  <?php echo isset($this->pinboard_width) ? str_replace(array('px','%'),array(''),$this->pinboard_width) : '300'; ?>, // Optional min width of a grid item
			outerOffset: 0, // Optional the distance from grid to parent
			align:'left',
			flexibleWidth: function () {
				// Return a maximum width depending on the viewport
				return getWindowWidth_<?php echo $randonNumber; ?>() < 1024 ? '100%' : '40%';
			}
			});
		})();
			}else {
				wookmark<?php echo $randonNumber ?>.initItems();
				wookmark<?php echo $randonNumber ?>.layout(true);
			}	
	}
sesJqueryObject(document).ready(function(){
	pinboardLayout_<?php echo $randonNumber ?>();
});
 sesJqueryObject(window).resize(function(e){
  pinboardLayout_<?php echo $randonNumber ?>('',true);
 });
</script>
<?php } ?>
<script type="text/javascript">
	function changeTabSes_<?php echo $randonNumber; ?>(valueTab){
			if(sesJqueryObject("#sesTabContainer_<?php echo $randonNumber ?>_"+valueTab).hasClass('active'))
				return;
			var id = '_<?php echo $randonNumber; ?>';
			var length = availableTabs_<?php echo $randonNumber; ?>.length;
			for (var i = 0; i < length; i++) {
					if(availableTabs_<?php echo $randonNumber; ?>[i] == valueTab)
						document.getElementById('sesTabContainer'+id+'_'+availableTabs_<?php echo $randonNumber; ?>[i]).addClass('active');
					else
						document.getElementById('sesTabContainer'+id+'_'+availableTabs_<?php echo $randonNumber; ?>[i]).removeClass('active');
			}
		if(valueTab){
				document.getElementById("tabbed-widget_<?php echo $randonNumber; ?>").innerHTML = "<div class='clear sesbasic_loading_container'></div>";
				if(document.getElementById("view_more_<?php echo $randonNumber; ?>"))
				document.getElementById("view_more_<?php echo $randonNumber; ?>").style.display = 'none';
			 if (typeof(requestTab_<?php echo $randonNumber; ?>) != 'undefined') {
				 requestTab_<?php echo $randonNumber; ?>.cancel();
 			 }
			 requestTab_<?php echo $randonNumber; ?> = new Request.HTML({
				method: 'post',
				'url': en4.core.baseUrl + 'widget/index/mod/sesalbum/name/tabbed-widget/openTab/' + valueTab,
				'data': {
					format: 'html',
					page:  1,    
					params :'<?php echo json_encode($this->params); ?>', 
					is_ajax : 1,
					identity : '<?php echo $randonNumber; ?>',
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = '';
					document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
					if("<?php echo $this->view_type ; ?>" == 'pinboard'){
							pinboardLayout_<?php echo $randonNumber ?>();
					}
					if("<?php echo $this->albumPhotoOption; ?>" == 'photo' && "<?php echo $this->view_type ; ?>" == 'masonry'){
							sesJqueryObject("#tabbed-widget_<?php echo $randonNumber; ?>").flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>,truncate:truncateRow});
					}
				}
    	});
		requestTab_<?php echo $randonNumber; ?>.send();
    return false;			
		}
	}
<?php if($this->loadOptionData != 'pagging'){ ?>
  viewMoreHide_<?php echo $randonNumber; ?>();
  function viewMoreHide_<?php echo $randonNumber; ?>() {
    if ($('view_more_<?php echo $randonNumber; ?>'))
      $('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginator->count() == 0 ? 'none' : ($this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' )) ?>";
  }
	var page<?php echo $randonNumber; ?> = '<?php echo $this->page + 1; ?>';
  function viewMore_<?php echo $randonNumber; ?> (){
    var openTab_<?php echo $randonNumber; ?> = '<?php echo $this->defaultOpenTab; ?>';
    document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'none';
    document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = '';    
    if (typeof(requestTab_<?php echo $randonNumber; ?>) != 'undefined') {
				 requestTab_<?php echo $randonNumber; ?>.cancel();
 			 }
		requestTab_<?php echo $randonNumber; ?> = 
		(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'widget/index/mod/sesalbum/name/tabbed-widget/openTab/' + openTab_<?php echo $randonNumber; ?>,
      'data': {
        format: 'html',
        page: page<?php echo $randonNumber; ?>,    
				params :params<?php echo $randonNumber; ?> , 
				is_ajax : 1,
				searchParams : searchParams<?php echo $randonNumber; ?>,
				identity : identity<?php echo $randonNumber; ?>,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
				 if($('loadingimgsesalbum-wrapper'))
					sesJqueryObject('#loadingimgsesalbum-wrapper').hide();
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
				if("<?php echo $this->albumPhotoOption; ?>" == 'photo' && "<?php echo $this->view_type ; ?>" == 'pinboard'){
							pinboardLayout_<?php echo $randonNumber ?>();
					}
				if("<?php echo $this->albumPhotoOption; ?>" == 'photo' && "<?php echo $this->view_type ; ?>" == 'masonry'){
							sesJqueryObject("#tabbed-widget_<?php echo $randonNumber; ?>").flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>,truncate:truncateRow});
				}
      }
    })).send();
    return false;
  }
<?php } ?>
</script>