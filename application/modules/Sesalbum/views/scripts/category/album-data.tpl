<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: album-data.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
		 <?php if(empty($this->resultArray['album_data'][0])){ die($this->translate('No albums found with this criteria.'));}
   			  $allowRatingAlbum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.album.rating',1);
					$allowShowPreviousRatingAlbum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratealbum.show',1);
          if($allowRatingAlbum == 0){
          	if($allowShowPreviousRatingAlbum == 0)
            	$ratingShowAlbum = false;
             else
             	$ratingShowAlbum = true;
          }else
          	$ratingShowAlbum = true;
      $albumData = $this->resultArray['album_data'][0];?>
      <div class="sesalbum_categories_albums_item sesbasic_clearfix clear">
        <?php if(isset($this->albumPhotoActive)){ ?>
          <div class="sesalbum_categories_albums_items_photo floatL">
          	<a class="sesalbum_thumb_img" href="<?php echo $albumData->getHref(); ?>">
            	<span style="background-image:url(<?php echo $albumData->getPhotoUrl('thumb.normalmain'); ?>);"></span>
            </a>
            
          </div>
        <?php } ?>
          <div class="sesalbum_categories_albums_items_cont">
            <div class="sesalbum_categories_albums_items_title">
            <?php if(isset($this->titleActive)){ ?>
            	<?php 
              		if(strlen($albumData->title)>$this->title_truncation)
                  	$titleChanel = mb_substr($albumData->title,0,$this->title_truncation).'...';
                  else
              			$titleChanel = $albumData->title; 
              ?>
            	<a href="<?php echo $albumData->getHref(); ?>"><?php echo $titleChanel ?></a>
             <?php } ?>
            </div>
            <div class="sesalbum_categories_albums_item_stat sesalbum_list_stats sesbasic_clearfix sesbasic_text_light"> 
            	<?php if(isset($this->byActive)){ ?>
                <span>
                  <?php 
                  $owner = $albumData->getOwner();
                  echo $this->translate('Posted by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
                  ?>
                </span>
              <?php } ?>
              <?php if(isset($this->albumCountActive)){ ?>
                <span title="<?php echo $this->translate(array('%s photo', '%s photos', $albumData->total_photos), $this->locale()->toNumber($albumData->total_photos)); ?>">
                  <i class="fa fa-photo"></i> <?php echo $albumData->total_photos ; ?>
                </span>
              <?php } ?>
              <?php if(isset($this->likeActive)) { ?>
                <span title="<?php echo $this->translate(array('%s like', '%s likes', $albumData->like_count), $this->locale()->toNumber($albumData->like_count)); ?>">
                  <i class="fa fa-thumbs-up"></i>
                  <?php echo $albumData->like_count;?>
                </span>
              <?php } ?>
              <?php if(isset($this->commentActive)) { ?>
                <span title="<?php echo $this->translate(array('%s comment', '%s comments', $albumData->comment_count), $this->locale()->toNumber($albumData->comment_count)); ?>">
                  <i class="fa fa-comment"></i>
                  <?php echo $albumData->comment_count;?>
                </span>
             <?php } ?>
             <?php if(isset($this->viewActive)) { ?>
                <span title="<?php echo $this->translate(array('%s view', '%s views', $albumData->view_count), $this->locale()->toNumber($albumData->view_count)); ?>">
                  <i class="fa fa-eye"></i>
                  <?php echo $albumData->view_count;?>
                </span>
              <?php } ?>
              <?php if(isset($this->ratingActive) && $ratingShowAlbum) { 
               $user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album',$albumData->album_id);
                  $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                  $textRatingText = $albumData->rating == 1 ? 'rating' : 'ratings'; ?>
                <span title="<?php echo $albumData->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                  <?php if( $albumData->rating > 0 ): ?>
                    <?php for( $x=1; $x <= $albumData->rating; $x++ ): ?>
                      <span class="sesbasic_rating_star_small fa fa-star"></span>
                    <?php endfor; ?>
                    <?php if( (round($albumData->rating) - $albumData->rating) > 0): ?>
                      <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                    <?php endif; ?>
                  <?php endif; ?> 
                </span>
             	<?php } ?>
            </div>
						<div class="sesalbum_categories_albums_item_cont_btm sesbasic_custom_scroll">
              <?php if(isset($this->descriptionActive)){ ?>
                <div class="sesalbum_list_des clear">
                <?php if(strlen($albumData->description) > $this->description_truncation){
                          $description = mb_substr($albumData->description,0,$this->description_truncation).'...';
                         }else{ ?>
                  <?php $description = $albumData->description; ?>
                    <?php } ?>
                    <?php echo $description; ?>
                </div>
              <?php } ?>
             	<?php if(isset($this->resultArray['photos']) && !$albumData->adult){ ?>
                <div class="sesalbum_categories_albums_photos clear sesbasic_clearfix">
                 <?php foreach($this->resultArray['photos'] as $photoData){ ?>
                 <?php
                   	$imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($photoData);
                  ?>
                  <div class="sesalbum_thumb" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;"> 
                    <a href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($photoData->getIdentity(),$photoData->album_id); ?>" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $photoData->getPhotoUrl(); ?>','<?php echo $imageURL	; ?>')" class="sesalbum_thumb_img ses-image-viewer"> 
                    <span style="background-image:url(<?php echo $photoData->getPhotoUrl('thumb.normalmain'); ?>);"></span> </a> 
                  </div>
                 <?php } ?>
            		</div>
          		<?php } ?>
						</div>
          </div>
        </div>