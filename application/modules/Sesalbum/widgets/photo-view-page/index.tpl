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
<?php
if(!$this->is_ajax && isset($this->docActive)){
	$imageURL = $this->photo->getPhotoUrl();
	if(strpos($this->photo->getPhotoUrl(),'http') === false)
          	$imageURL = (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://". $_SERVER['HTTP_HOST'].$this->photo->getPhotoUrl() : "http://". $_SERVER['HTTP_HOST'].$this->photo->getPhotoUrl();
  $this->doctype('XHTML1_RDFA');
  $this->headMeta()->setProperty('og:title', $this->photo->getTitle());
  $this->headMeta()->setProperty('og:url', (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://".$_SERVER['HTTP_HOST'].$this->photo->getHref() : "http://".$_SERVER['HTTP_HOST'].$this->photo->getHref());
  $this->headMeta()->setProperty('og:description', $this->photo->getDescription());
  $this->headMeta()->setProperty('og:image',$imageURL);
  $this->headMeta()->setProperty('twitter:card','summary_large_image');
  $this->headMeta()->setProperty('twitter:site', 'website');
  $this->headMeta()->setProperty('twitter:image',$imageURL);
  $this->headMeta()->setProperty('twitter:url', (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://".$_SERVER['HTTP_HOST'].$this->photo->getHref() : "http://".$_SERVER['HTTP_HOST'].$this->photo->getHref());
  $this->headMeta()->setProperty('twitter:title', $this->photo->getTitle());
    $this->headMeta()->setProperty('twitter:description', $this->photo->getDescription());
}
?>
<?php
  $this->headTranslate(array(
    'Save', 'Cancel', 'delete',
  ));
?>
<?php if(!$this->is_ajax){ ?>
 <div id="photo_content" style="display:none">
 <?php } ?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php if(($this->getAllowRating == 0 && $this->allowShowRating == 1 && $this->total_rating_average != 0 ) || ($this->getAllowRating == 1) ){ ?>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    var pre_rate = "<?php echo $this->total_rating_average == '' ? '0' : $this->total_rating_average  ;?>";
		<?php if($this->viewer_id == 0){ ?>
			rated = 0;
		<?php }else if($this->allowShowRating == 1 && $this->allowRating == 0){?>
		var rated = 3;
		<?php }else if($this->allowRateAgain == 0 && $this->rated){ ?>
		var rated = 1;
		<?php }else if($this->canRate == 0 && $this->viewer_id != 0){?>
		var rated = 4;
		<?php }else if(!$this->allowMine){?>
		var rated = 2;
		<?php }else{ ?>
    var rated = '90';
		<?php } ?>
    var resource_id = <?php echo $this->photo->photo_id;?>;
    var total_votes = <?php echo $this->rating_count;?>;
    var viewer = <?php echo $this->viewer_id;?>;
    new_text = '';

    var rating_over = window.rating_over = function(rating) {
      if( rated == 1 ) {
        $('rating_text').innerHTML = "<?php echo $this->translate('you have already rated');?>";
				return;
        //set_rating();
      }
			<?php if(!$this->canRate){ ?>
				else if(rated == 4){
						 $('rating_text').innerHTML = "<?php echo $this->translate('rating is not allowed for your member level');?>";
						 return;
				}
			<?php } ?>
			<?php if(!$this->allowMine){ ?>
				else if(rated == 2){
						 $('rating_text').innerHTML = "<?php echo $this->translate('rating on own photo not allowed');?>";
						 return;
				}
			<?php } ?>
			<?php if($this->allowShowRating == 1){ ?>
				else if(rated == 3){
						 $('rating_text').innerHTML = "<?php echo $this->translate('rating is disabled');?>";
						 return;
				}
			<?php } ?>
			else if( viewer == 0 ) {
        $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate');?>";
				return;
      } else {
        $('rating_text').innerHTML = "<?php echo $this->translate('click to rate');?>";
        for(var x=1; x<=5; x++) {
          if(x <= rating) {
            $('rate_'+x).set('class', 'fa fa fa-star');
          } else {
            $('rate_'+x).set('class', 'fa fa fa-star-o star-disable');
          }
        }
      }
    }
    
    var rating_out = window.rating_out = function() {
      if (new_text != ''){
        $('rating_text').innerHTML = new_text;
      }
      else{
        $('rating_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";        
      }
      if (pre_rate != 0){
        set_rating();
      }
      else {
        for(var x=1; x<=5; x++) {
          $('rate_'+x).set('class', 'fa fa fa-star-o star-disable');
        }
      }
    }

    var set_rating = window.set_rating = function() {
      var rating = pre_rate;
      if (new_text != ''){
        $('rating_text').innerHTML = new_text;
      }
      else{
        $('rating_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
      }
      for(var x=1; x<=parseInt(rating); x++) {
        $('rate_'+x).set('class', 'fa fa fa-star');
      }

      for(var x=parseInt(rating)+1; x<=5; x++) {
        $('rate_'+x).set('class', 'fa fa fa-star-o star-disable');
      }

      var remainder = Math.round(rating)-rating;
      if (remainder <= 0.5 && remainder !=0){
        var last = parseInt(rating)+1;
        $('rate_'+last).set('class', 'fa fa fa-star-half-o');
      }
    }

    var rate = window.rate = function(rating) {
      $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!');?>";
			<?php if($this->allowRateAgain == 0 && !$this->rated){ ?>
						 for(var x=1; x<=5; x++) {
								$('rate_'+x).set('onclick', '');
							}
					<?php } ?>
     
      (new Request.JSON({
        'format': 'json',
        'url' : '<?php echo $this->url(array('module' => 'sesalbum', 'controller' => 'index', 'action' => 'rate'), 'default', true) ?>',
        'data' : {
          'format' : 'json',
          'rating' : rating,
          'resource_id': resource_id,
					'resource_type':'<?php echo $this->rating_type; ?>'
        },
        'onSuccess' : function(responseJSON, responseText)
        {
					<?php if($this->allowRateAgain == 0 && !$this->rated){ ?>
							rated = 1;
					<?php } ?>
					showTooltip(10,10,'<i class="fa fa-star"></i><span>'+("Photo Rated successfully")+'</span>', 'sesbasic_rated_notification');
					var total = responseJSON[0].total;
					var totalTxt = responseJSON[0].totalTxt;
					var rating_sum = responseJSON[0].rating_sum;
          pre_rate = rating_sum / total;
          set_rating();
          $('rating_text').innerHTML = total+' '+totalTxt;
          new_text = total+' '+totalTxt;
        }
      })).send();
    }
    set_rating();
  });
					
</script>
<?php } ?>
<script type="text/javascript">
var maxHeight = <?php echo $this->maxHeight; ?>;
function doResizeForButton(){
	<?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0){ ?>	
	var topPositionOfParentDiv =  sesJqueryObject(".sesalbum_photo_view_option_btn").offset().top + 35;
	topPositionOfParentDiv = topPositionOfParentDiv+'px';
	var leftPositionOfParentDiv =  sesJqueryObject(".sesalbum_photo_view_option_btn").offset().left - 115;
	leftPositionOfParentDiv = leftPositionOfParentDiv+'px';
	sesJqueryObject('.sesalbum_option_box').css('top',topPositionOfParentDiv);
	sesJqueryObject('.sesalbum_option_box').css('left',leftPositionOfParentDiv);
	<?php } ?>
	 var width = sesJqueryObject('.sesalbum_view_photo_container_wrapper').width();
  sesJqueryObject('#media_photo').css('max-width',width+'px');
	sesJqueryObject('#media_photo').css('max-height',maxHeight+'px');
}

<?php //if(Engine_Api::_()->user()->getViewer()->getIdentity() != ''){ ?>
window.addEvent('load',function(){
	doResizeForButton();
});
  en4.core.runonce.add(function() {
    var descEls = $$('.sesalbum_view_photo_des');
    if( descEls.length > 0 ) {
      descEls[0].enableLinks();
    }
    var taggerInstance = window.taggerInstance = new Tagger('media_photo_next', {
      'title' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Tag'));?>',
      'description' : '<?php echo $this->string()->escapeJavascript($this->translate('Type a tag or select a name from the list.'));?>',
      'createRequestOptions' : {
        'url' : '<?php echo $this->url(array('module' => 'sesalbum', 'controller' => 'photo', 'action' => 'add'), 'default', true) ?>',
        'data' : {
          'subject' : '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'deleteRequestOptions' : {
        'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
        'data' : {
          'subject' : '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'cropOptions' : {
        'container' : $('media_photo_next')
      },
      'tagListElement' : 'media_tags',
      'existingTags' : <?php echo Zend_Json::encode($this->tags) ?>,
      'suggestProto' : 'request.json',
      'suggestParam' : "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
      'guid' : <?php echo ( $this->viewer->getIdentity() ? "'".$this->viewer->getGuid()."'" : 'false' ) ?>,
      'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
      'enableDelete' : <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>
    });
    // Remove the href attrib while tagging
    var nextHref = $('media_photo_next').get('href');
    taggerInstance.addEvents({
      'onBegin' : function() {
				sesJqueryObject('.sesalbum_photo_view_btns').hide();
        $('media_photo_next').erase('href');
      },
      'onEnd' : function() {
				sesJqueryObject('.sesalbum_photo_view_btns').show();
        $('media_photo_next').set('href', nextHref);
      }
    });
    var keyupEvent = function(e) {
      if( e.target.get('tag') == 'html' ||
          e.target.get('tag') == 'body' ) {
        if( e.key == 'right' ) {
          $('photo_next').fireEvent('click', e);
          //window.location.href = "<?php echo ( $this->nextPhoto ? $this->nextPhoto->getHref() : 'window.location.href' ) ?>";
        } else if( e.key == 'left' ) {
          $('photo_prev').fireEvent('click', e);
          //window.location.href = "<?php echo ( $this->previousPhoto ? $this->previousPhoto->getHref() : 'window.location.href' ) ?>";
        }
      }
    }
    window.addEvent('keyup', keyupEvent);
    // Add shutdown handler
    en4.core.shutdown.add(function() {
      window.removeEvent('keyup', keyupEvent);
    });
  });
</script>
<div class='sesalbum_view_photo sesbasic_bxs sesbasic_clearfix'>
  <div class='sesalbum_view_photo_container_wrapper sesbasic_clearfix'>
    <?php if( $this->album->count() > 1 ): ?>
      <div class="sesalbum_view_photo_nav_btns">
        <?php
        $photoPreviousData = Engine_Api::_()->sesalbum()->getPreviousPhoto($this->album->album_id ,$this->photo->order ) ?  Engine_Api::_()->sesalbum()->getPreviousPhoto($this->album->album_id,$this->photo->order) : null;
        echo $this->htmlLink((isset($photoPreviousData->album_id) ?  Engine_Api::_()->sesalbum()->getHrefPhoto($photoPreviousData->photo_id,$photoPreviousData->album_id) : null ), '<i class="fa fa-angle-left"></i>', array('id' => 'photo_prev','data-url'=>$photoPreviousData->photo_id, 'class' => 'sesalbum_view_photo_nav_prev_btn'));
        $photoNextData = Engine_Api::_()->sesalbum()->getNextPhoto($this->album->album_id  ,$this->photo->order ) ?  Engine_Api::_()->sesalbum()->getNextPhoto($this->album->album_id  ,$this->photo->order ) : null;
         ?>
        <?php echo $this->htmlLink(( isset($photoNextData->album_id) ?  Engine_Api::_()->sesalbum()->getHrefPhoto($photoNextData->photo_id,$photoNextData->album_id) : null ), '<i class="fa fa-angle-right"></i>', array('id' => 'photo_next','data-url'=>$photoNextData->photo_id, 'class' => 'sesalbum_view_photo_nav_nxt_btn')) ?>
      </div>
    <?php endif ?>
    <div class='sesalbum_view_photo_container' id='media_photo_div'>
      <?php 
        $imageViewerURL = Engine_Api::_()->sesalbum()->getImageViewerHref($this->photo);
        if($imageViewerURL != ''){
      ?>
        <a href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($this->photo->photo_id,$this->photo->album_id); ?>" title="Open image in image viewer" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $this->photo->getPhotoUrl(); ?>','<?php echo $imageViewerURL; ?>');return false;" class="sesalbum_view_photo_expend"><i class="fa fa-expand"></i></a>
      <?php } ?>
      <div id="media_photo_next">
      	<a id="photo_main_next" href="javascript:;">
        <?php echo $this->htmlImage($this->photo->getPhotoUrl('','','string'), $this->photo->getTitle(), array(
          'id' => 'media_photo',
          'onload'=>'doResizeForButton()'
        )); ?>
        </a>
      </div>
    </div>
    <?php if( $this->canEdit ): ?>
      <div class="sesalbum_view_photo_rotate_btns">          
         <a class="sesalbum_icon_photos_rotate_ccw" id="ses-rotate-90" href="javascript:void(0)" onclick="sesRotate('<?php echo $this->photo->getIdentity() ?>','90')">&nbsp;</a>
      <a class="sesalbum_icon_photos_rotate_cw" id="ses-rotate-270" href="javascript:void(0)" onclick="sesRotate('<?php echo $this->photo->getIdentity() ?>','270')">&nbsp;</a>
      <a class="sesalbum_icon_photos_flip_horizontal" id="ses-rotate-horizontal"  href="javascript:void(0)" onclick="sesRotate('<?php echo $this->photo->getIdentity() ?>','horizontal')">&nbsp;</a>
      <a class="sesalbum_icon_photos_flip_vertical" id="ses-rotate-vertical"  href="javascript:void(0)" onclick="sesRotate('<?php echo $this->photo->getIdentity() ?>','vertical')">&nbsp;</a>          
      </div>
    <?php endif ?>
    <?php
    if($this->canCommentMemberLevelPermission == 0){
    		$canComment = false;
    }else{
    		$canComment = true;
    } ?>
    <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != ''){ 
    	$urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $this->photo->getHref());
    ?>
      <ul class="sesbasic_clearfix sesalbum_photo_view_btns">
        <li><a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $this->photo->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_facebook_button"><i class="fa fa-facebook"></i></a></li>
        <li><a href="<?php echo 'https://twitter.com/intent/tweet?url=' . $urlencode . '&text=' . $this->photo->getTitle().'%0a'; ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_twitter_button"><i class="fa fa-twitter"></i></a></li>
        <li><a  href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($this->photo->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $this->album->getPhotoUrl('thumb.main')) . $this->album->getPhotoUrl('thumb.main')); ?>&description=<?php echo $this->photo->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_pintrest_button"><i class="fa fa-pinterest"></i></a></li>
        <li><a title="<?php echo $this->translate('Share'); ?>" href="<?php echo $this->url(array("action" => "share", "type" => "album_photo", "photo_id" => $this->photo->getIdentity(),"format" => "smoothbox"), 'sesalbum_general', true); ?>" onclick="openURLinSmoothBox(this.href);return false;" class="sesalbum_photo_view_share_button smoothbox"><i class="fa fa-share"></i></a></li>       
        <li><a  title="<?php echo $this->translate('Message'); ?>" href="<?php echo $this->url(array('module'=> 'sesalbum', 'controller' => 'index', 'action' => 'message','photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'),'sesalbum_extended',true); ?>" onclick="openURLinSmoothBox(this.href);return false;" class="sesalbum_photo_view_msg_button smoothbox"><i class="fa fa-envelope"></i></a></li>
        <?php if(isset($this->canDownload) && $this->canDownload){ ?>
        <li><a title="<?php echo $this->translate('Download'); ?>" href="<?php echo $this->url(array('module' => 'sesalbum', 'action' => 'download', 'photo_id' => $this->photo->photo_id,'type'=>'photo'), 'sesalbum_general', true); ?>" class="sesalbum_photo_view_download_button"><i class="fa fa-download"></i></a></li>
        <?php } ?>
       <?php if($this->canComment){ ?>
     	 <?php $LikeStatus = Engine_Api::_()->sesalbum()->getLikeStatusPhoto($this->photo->photo_id); ?>
        <li><a href="javascript:void(0);" id="sesLikeUnlikeButton" class="sesalbum_view_like_button <?php echo $LikeStatus === true ? 'button_active' : '' ;  ?>"><i class="fa fa-thumbs-up"></i></a></li>
        <?php } ?>
      <?php $albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album_photo','resource_id'=>$this->photo->photo_id)); ?>
        <li><a href="javascript:;" data-src='<?php echo $this->photo->photo_id; ?>' class="sesalbum_view_fav_button sesalbum_photoFav <?php echo ($albumFavStatus)>0 ? 'button_active' : '' ; ?>"><i class="fa fa-heart"></i></a></li>
      <?php if($canComment){ ?>
        <li><a title="<?php echo $this->translate('Comment'); ?>" href="javascript:void(0);" id="sescomment_button" class="sesalbum_view_comment_button"><i class="fa fa-comment"></i></a></ii>
      <?php } ?>
        <?php if($this->canTag){ ?>
        <li><a title="<?php echo $this->translate('Tag'); ?>" href="javascript:void(0);" onclick="taggerInstance.begin();" class="sesalbum_view_tag_button"><i class="fa fa-tag"></i></a></li>
        <?php } ?>
        <li class="sesalbum_photo_view_option_btn">
          <a title="<?php echo $this->translate('Options'); ?>" href="javascript:;" id="parent_container_option"><i id="fa-ellipsis-v" class="fa fa-ellipsis-v"></i></a>
        </li>  
      </ul>
    <?php } ?>
  </div>
  <div class="sesalbum_view_photo_count">
    <?php echo $this->translate('Photo %1$s of %2$s',
        $this->locale()->toNumber($this->photo->getPhotoIndex() + 1),
        $this->locale()->toNumber($this->album->count())) ?>
  </div>
	<div class="sesalbum_photo_view_bottom_right">
    <?php if(isset($this->status_slideshowPhoto)){ ?>
      <!-- Corresponding photo as per album id -->
      <div class="layout_sesalbum_photo_strip">
        <div class="sesalbum_photos_strip_slider sesbasic_clearfix clear">
          <a id="prevSlide" class="sesalbum_photos_strip_slider_btn btn-prev"><i class="fa fa-angle-left"></i></a>
          <div class="sesalbum_photos_strip_slider_content">
            <div id="sesalbum_corresponding_photo" style="width:257px;">
            <?php if(!$this->is_ajax){ ?>
              <img id="sesalbum_corresponding_photo_image" src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sesbasic/externals/images/loading.gif" alt="" style="margin-top:23px;" />
             <?php } ?>
            </div>
          </div>
          <a id="nextSlide" class="sesalbum_photos_strip_slider_btn btn-nxt"><i class="fa fa-angle-right"></i></a>
        </div>
      </div>
    <?php } ?>
    <?php if(isset($this->paginator_like) && isset($this->status_like) && $this->paginator_like->getTotalItemCount() >0){ ?>
      <!--People  Like photo code -->
      <div class="layout_sesalbum_people_like_photo">
        <h3><?php echo $this->translate("People Who Like This");?></h3>
        <ul id="like-status-<?php echo $this->identity; ?>" class="sesalbum_user_listing sesbasic_clearfix clear">
          <?php foreach( $this->paginator_like as $item ): ?>
            <li>
              <?php $user = Engine_Api::_()->getItem('user',$item->poster_id); ?>
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'),array('title'=>$user->getTitle())); ?>
            </li>
          <?php endforeach; ?>
          <?php if($this->paginator_like->getTotalItemCount() > $this->data_show_like){ ?>
            <li>
              <a href="javascript:;" onclick="getLikeData('<?php echo $this->photo_id; ?>')" class="sesalbum_user_listing_more">
                <?php echo '+';echo $this->paginator_like->getTotalItemCount() - $this->data_show_like ; ?>
              </a>
            </li>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>
    <?php if(isset($this->paginator_favourite) && isset($this->status_favourite) && $this->paginator_favourite->getTotalItemCount() >0){ ?>
      <!--People  Like photo code -->
      <div class="layout_sesalbum_people_favourite_photo">
        <h3><?php echo $this->translate("People Who Added This As Favourite");?></h3>
        <ul id="like-status-<?php echo $this->identity; ?>" class="sesalbum_user_listing sesbasic_clearfix clear">
          <?php foreach( $this->paginator_favourite as $item ): ?>
            <li>
              <?php $user = Engine_Api::_()->getItem('user',$item->user_id); ?>
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'),array('title'=>$user->getTitle())); ?>
            </li>
          <?php endforeach; ?>
          <?php if($this->paginator_favourite->getTotalItemCount() > $this->data_show_favourite){ ?>
            <li>
              <a href="javascript:;" onclick="getFavouriteData('<?php echo $this->photo_id; ?>')" class="sesalbum_user_listing_more">
                <?php echo '+';echo $this->paginator_favourite->getTotalItemCount() - $this->data_show_favourite ; ?>
              </a>
            </li>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>
    <?php if(isset($this->paginator_tagged) && isset($this->status_tagged) && $this->paginator_tagged->getTotalItemCount()>0){ ?>
      <!-- People tagged in photo code-->
      <div class="layout_sesalbum_people_tagged_photo">
        <h3><?php echo $this->translate("People Who Are Tagged In This Photo"); ?></h3>
        <ul class="sesalbum_user_listing sesbasic_clearfix clear">
          <?php foreach( $this->paginator_tagged as $item ): ?>
            <li>
              <?php $user = Engine_Api::_()->getItem('user',$item->tag_id); ?>
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'),array('title'=>$user->getTitle())); ?>
            </li>
          <?php endforeach; ?>
          <?php if($this->paginator_tagged->getTotalItemCount() > $this->data_show_tagged){ ?>
          <li>
            <a href="javascript:;" onclick="getTagData('<?php echo $this->photo_id; ?>')" class="sesalbum_user_listing_more">
             <?php echo '+';echo $this->paginator_tagged->getTotalItemCount() - $this->data_show_tagged ; ?>
            </a>
          </li>
         <?php } ?>
        </ul>
      </div>
		<?php } ?>
  </div>
	<div class="sesalbum_photo_view_bottom_middle sesbasic_clearfix">
    <?php if( $this->photo->getTitle() ): ?>
      <div class="sesalbum_view_photo_title">
        <?php echo $this->photo->getTitle(); ?>
      </div>
    <?php endif; ?>
    <div class="sesalbum_view_photo_middle_box clear sesbasic_clearfix">
      <div class="sesalbum_view_photo_owner_info sesbasic_clearfix">
        <div class="sesalbum_view_photo_owner_photo">
          <?php $albumOwnerDetails = Engine_Api::_()->user()->getUser($this->album->owner_id); ?>
          <?php echo $this->htmlLink($albumOwnerDetails->getHref(), $this->itemPhoto($albumOwnerDetails, 'thumb.icon')); ?>  
        </div>
        <div class="sesalbum_view_photo_owner_details">
          <span class="sesalbum_view_photo_owner_name sesbasic_text_light">
            by <?php echo $this->htmlLink($albumOwnerDetails->getHref(), $albumOwnerDetails->getTitle()); ?>
          </span>
          <span class="sesbasic_text_light sesalbum_view_photo_date">
            <?php echo $this->translate('in %1$s', $this->htmlLink( Engine_Api::_()->sesalbum()->getHref($this->album->getIdentity()), $this->album->getTitle())); ?>
            on <?php echo date('F j',strtotime($this->photo->creation_date)); ?>
          </span>
        </div>
    	</div>
      <div class="sesalbum_view_photo_photo_stats">
        <?php if(($this->getAllowRating == 0 && $this->allowShowRating == 1 && $this->total_rating_average != 0 ) || ($this->getAllowRating == 1) ){ ?>
          <div id="album_rating" class="sesbasic_rating_star sesalbum_view_photo_rating" onmouseout="rating_out();">
            <span id="rate_1" class="fa fa fa-star" <?php  if ($this->viewer_id && $this->ratedAgain && $this->allowMine &&  $this->allowRating):?>onclick="rate(1);"<?php  endif; ?> onmouseover="rating_over(1);"></span>
            <span id="rate_2" class="fa fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
            <span id="rate_3" class="fa fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
            <span id="rate_4" class="fa fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
            <span id="rate_5" class="fa fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
            <span id="rating_text" class="sesbasic_rating_text"><?php echo $this->translate('click to rate');?></span>
          </div>
        <?php } ?>  
        <div class="sesalbum_list_stats sesbasic_clearfix sesbasic_text_light clear">
          <span title="<?php echo $this->translate(array('%s Like', '%s Likes', $this->photo->like_count), $this->locale()->toNumber($this->photo->like_count))?>"><i class="fa fa-thumbs-up" ></i><?php echo $this->translate(array('%s Like', '%s Likes', $this->photo->like_count), $this->locale()->toNumber($this->photo->like_count))?></span>
          <span title="<?php echo $this->translate(array('%s Comment', '%s Comments', $this->photo->comment_count), $this->locale()->toNumber($this->photo->comment_count))?>"><i class="fa fa-comment" ></i><?php echo $this->translate(array('%s Comment', '%s Comments', $this->photo->comment_count), $this->locale()->toNumber($this->photo->comment_count))?></span>
          <span title="<?php echo $this->translate(array('%s View', '%s Views', $this->photo->view_count), $this->locale()->toNumber($this->photo->view_count))?>"><i class="fa fa-eye" ></i><?php echo $this->translate(array('%s View', '%s Views', $this->photo->view_count), $this->locale()->toNumber($this->photo->view_count))?></span>
          <span title="<?php echo $this->translate(array('%s Favourite', '%s Favourites', $this->photo->favourite_count), $this->locale()->toNumber($this->photo->favourite_count))?>"><i class="fa fa-heart" ></i><?php echo $this->translate(array('%s Favourite', '%s Favourites', $this->photo->favourite_count), $this->locale()->toNumber($this->photo->favourite_count))?></span>
          <span title="<?php echo $this->translate(array('%s Download', '%s Downloads', $this->photo->download_count), $this->locale()->toNumber($this->photo->download_count))?>"><i class="fa fa-download" ></i><?php echo $this->translate(array('%s Download', '%s Downloads', $this->photo->download_count), $this->locale()->toNumber($this->photo->download_count))?></span>
        </div>
      </div>
    </div>
    <div class="sesalbum_view_photo_info_left">
      <?php if( $this->photo->getDescription() ): ?>
        <div class="sesalbum_view_photo_des">
          <b>Description</b>
          <?php echo nl2br($this->photo->getDescription()) ?>
        </div>
      <?php endif; ?>
      <div class="sesalbum_view_photo_tags" id="media_tags" style="display: none;">
        <b><?php echo $this->translate('Tagged') ?></b>
      </div>
      <?php if(!is_null($this->photo->location) && $this->photo->location != ''){ ?>
        <div class="sesalbum_view_photo_location"><i class="fa fa-map-marker sesbasic_text_light"></i>
        <?php echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "photo", "action" => "location", "route" => "sesalbum_extended", "type" => "location","album_id" =>$this->photo->album_id, "photo_id" => $this->photo->getIdentity()), $this->photo->location, array("class" => "smoothboxOpen")); ?>
        </div>
      <?php } ?>
    </div>
    <!-- comment code-->
    <div class="sesalbum_photo_view_bottom_comments layout_core_comments">
      <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedcomment')){ ?>
      <?php echo $this->action("list", "comment", "sesadvancedcomment", array("type" => "album_photo", "id" => $this->photo->getIdentity(),'is_ajax_load'=>true)); 
        }else{
          echo $this->action("list", "comment", "core", array("type" => "album_photo", "id" => $this->photo->getIdentity())); 
        }
      ?> 
    </div>
  </div>
</div>
<script type="text/javascript">
var optionDataForButton;
optionDataForButton = '<div class="sesalbum_option_box"><?php if ($this->viewer()->getIdentity()):?><?php if( $this->canEdit ): ?><?php echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "photo", "action" => "location", "route" => "sesalbum_extended", "type" => "album_photo","album_id" =>$this->photo->album_id, "photo_id" => $this->photo->getIdentity()), $this->translate("Edit Location"), array("class" => "smoothboxOpen fa fa-map-marker")); ?><?php echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "photo", "action" => "edit", "route" => "sesalbum_extended","album_id" =>$this->photo->album_id, "photo_id" => $this->photo->getIdentity()), $this->translate("Edit"), array("class" => "smoothboxOpen fa fa-pencil")) ?><?php endif; ?><?php if( $this->canDelete ): ?><?php echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "photo", "action" => "delete", "route" => "sesalbum_extended","album_id" =>$this->photo->album_id, "photo_id" => $this->photo->getIdentity()), $this->translate("Delete"), array("class" => "smoothboxOpen fa fa-trash")) ?><?php endif; ?><?php if( !$this->message_view ):?>  <?php echo $this->htmlLink($this->url(array("action" => "share", "type" => "album_photo", "photo_id" => $this->photo->getIdentity(),"format" => "smoothbox"), "sesalbum_general"	, true), $this->translate("Share"), array("class" => "smoothboxOpen fa fa-share")); ?><?php echo $this->htmlLink(Array("module"=> "core", "controller" => "report", "action" => "create", "route" => "default", "subject" => $this->photo->getGuid()), $this->translate("Report"), array("class" => "smoothboxOpen  fa fa-flag")); ?><?php echo $this->htmlLink(array("route" => "user_extended", "controller" => "edit", "action" => "external-photo", "photo" => $this->photo->getGuid()), $this->translate("Make Profile Photo"), array("class" => "smoothboxOpen  fa fa-picture-o")) ?><?php endif;?><?php endif ?><?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && (Engine_Api::_()->user()->getViewer()->level_id == 1 || Engine_Api::_()->user()->getViewer()->level_id == 2)): ?><?php echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "index", "action" => "featured", "route" => "sesalbum_extended", "photo_id" => $this->photo->getIdentity(),"type" =>"photos"),  $this->translate((($this->photo->is_featured == 1) ? "Unmark as Featured" : "Mark Featured")), array("class" => "sesalbum_admin_featured fa fa-picture-o")) ?><?php echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "index", "action" => "sponsored", "route" => "sesalbum_extended", "photo_id" => $this->photo->getIdentity(),"type" =>"photos"),  $this->translate((($this->photo->is_sponsored == 1) ? "Unmark as Sponsored" : "Mark Sponsored")), array("class" => "sesalbum_admin_sponsored fa fa-picture-o")) ?><?php if(strtotime($this->photo->endtime) < strtotime(date("Y-m-d")) && $this->photo->offtheday == 1){$itemofftheday=0;}else{$itemofftheday = $this->photo->offtheday;}echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "index", "action" => "offtheday", "route" => "sesalbum_extended","id" => $this->photo->photo_id, "type" => "album_photo", "param" => (($itemofftheday == 1) ? 0 : 1)),  $this->translate((($itemofftheday == 1) ? "Edit of the Day" : "Make of the Day")), array("class" => "smoothboxOpen fa fa-picture-o")); ?><?php endif; ?></div>';
sesJqueryObject(optionDataForButton).appendTo('body');
<?php if(!$this->is_ajax){ ?>
	sesJqueryObject(document).click(function(event){
		if(event.target.id == 'parent_container_option' || event.target.id == 'fa-ellipsis-v'){
			if(sesJqueryObject('#parent_container_option').hasClass('active')){
				sesJqueryObject('#parent_container_option').removeClass('active');
				sesJqueryObject('.sesalbum_option_box').hide();	
			}else{
				sesJqueryObject('#parent_container_option').addClass('active');
				sesJqueryObject('.sesalbum_option_box').show();	
		  }
		}else{
			sesJqueryObject('#parent_container_option').removeClass('active');
			sesJqueryObject('.sesalbum_option_box').hide();	
		}
	});
	// on window resize work
	sesJqueryObject(window).resize(function(){
			doResizeForButton();
	});
<?php } ?>
  //Set Width On Image
<?php //} ?>
<?php if(!$this->is_ajax){ ?>
		function changeResizeSesalbum(){
			sesJqueryObject('#locked_content').remove();
			sesJqueryObject('#photo_content').show();
			doResizeForButton();	
		}
	/*change next previous button click event*/
		sesJqueryObject(document).on('click','#photo_prev',function(){
			changeNextPrevious(this);	
			return false;
		});
		sesJqueryObject(document).on('click','#photo_next',function(){
			changeNextPrevious(this);	
			return false;
		});
	 function changeNextPrevious(thisObject){
			history.pushState(null, null, sesJqueryObject(thisObject).attr('href'));
			var height = sesJqueryObject('#media_photo_div').height();
			var width = sesJqueryObject('#media_photo_div').width();
			sesJqueryObject('#media_photo_div').html('<div class="clear sesbasic_loading_container"></div>');
			sesJqueryObject('.sesbasic_loading_container').css('height',height) ;
			sesJqueryObject('.sesbasic_loading_container').css('width',width) ;
			var correspondingImageData = sesJqueryObject('#sesalbum_corresponding_photo').html();
			var photo_id = sesJqueryObject(thisObject).attr('data-url');
			(new Request.HTML({
      method: 'post',
      'url':en4.core.baseUrl + 'widget/index/mod/sesalbum/name/photo-view-page/',
      'data': {
        format: 'html',
				 photo_id : photo_id,
				params :'<?php echo json_encode($this->params); ?>', 
				is_ajax : 1,
				maxHeight:maxHeight
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				if(sesJqueryObject('.sesalbum_option_box').length >0)
					sesJqueryObject('.sesalbum_option_box').remove();
				<?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != ''){ ?>
					sesJqueryObject(optionDataForButton).appendTo('body');
				<?php } ?>
					sesJqueryObject('.layout_sesalbum_photo_view_page').html(responseHTML);
					changeResizeSesalbum();
					var width = sesJqueryObject('.sesalbum_view_photo_container_wrapper').width();
					sesJqueryObject('#media_photo').css('max-width',width+'px');
					sesJqueryObject('#media_photo').css('max-height',maxHeight+'px');
					sesJqueryObject('#sesalbum_corresponding_photo').html(correspondingImageData);
					sesJqueryObject('#sesalbum_corresponding_photo > a').each(function(index){
					sesJqueryObject(this).removeClass('slideuptovisible');
					if(sesJqueryObject(this).attr('data-url') == photo_id)
						sesJqueryObject(this).addClass('active');
					else
						sesJqueryObject(this).removeClass('active');
						countSlide++;
					});
					sesJqueryObject('#sesalbum_corresponding_photo > a').eq(3).addClass('slideuptovisible');
					sesJqueryObject('#sesalbum_corresponding_photo').css('width',(countSlide*64)+'px');
      }
    })).send();
    return false;
	}
<?php } ?>
 <?php if(isset($this->status_slideshowPhoto) && !$this->is_ajax){ ?>
sesJqueryObject(document).on('click','.sesalbum_corresponding_image_album',function(e){
		e.preventDefault();
		if(!sesJqueryObject(this).hasClass('active'))
			changeNextPrevious(this);
});
var countSlide = 0;
function getCorrespondingImg(){
	(new Request.HTML({
      method: 'post',
      'url':en4.core.baseUrl + 'sesalbum/index/corresponding-image/album_id/<?php echo $this->album->album_id; ?>',
      'data': {
        format: 'html',
				is_ajax : 1,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				if(responseHTML){
					sesJqueryObject('#sesalbum_corresponding_photo').html(responseHTML);
					sesJqueryObject('#sesalbum_corresponding_photo > a').each(function(index){
						sesJqueryObject(this).removeClass('slideuptovisible');
						if(sesJqueryObject(this).attr('data-url') == "<?php echo $this->photo->photo_id; ?>"){
							sesJqueryObject(this).addClass('active');	
						}
						countSlide++;	
					});
					
					sesJqueryObject('#sesalbum_corresponding_photo > a').eq(3).addClass('slideuptovisible');
					sesJqueryObject('#sesalbum_corresponding_photo').css('width',(countSlide*64)+'px');
				}
      }
    })).send();	
}
<?php } ?>
<?php if(!$this->is_ajax && isset($this->status_slideshowPhoto)){ ?>
sesJqueryObject(document).on('mouseover','#prevSlide',function(e){
	var indexCurrent = 	sesJqueryObject('#sesalbum_corresponding_photo > a.slideuptovisible').index();
	if(indexCurrent<4 || indexCurrent == '-1')
		sesJqueryObject('#prevSlide').css('cursor','not-allowed');
	else
		sesJqueryObject('#prevSlide').css('cursor','pointer');
});
sesJqueryObject(document).on('mouseover','#nextSlide',function(e){
	var indexCurrent = 	sesJqueryObject('#sesalbum_corresponding_photo > a.slideuptovisible').index();
	if(indexCurrent == (countSlide-1) || indexCurrent == '-1')
		sesJqueryObject('#nextSlide').css('cursor','not-allowed');
	else
		sesJqueryObject('#nextSlide').css('cursor','pointer');
});
sesJqueryObject(document).on('click','#nextSlide',function(){
	var indexCurrent = 	sesJqueryObject('#sesalbum_corresponding_photo > a.slideuptovisible').index();
	if((countSlide-1) == indexCurrent || indexCurrent == '-1'){
		// last slide is visible
	}else{
		var slideLeft = (countSlide-1) - indexCurrent;
		var leftAttr = sesJqueryObject('#sesalbum_corresponding_photo').css('left').replace('px','');
		leftAttr = leftAttr.replace('-','');		
		if(slideLeft>3){
			leftAttr = parseInt(leftAttr,10);
			sesJqueryObject('#sesalbum_corresponding_photo').css('left','-'+(leftAttr+(64*4))+'px');
			sesJqueryObject('#sesalbum_corresponding_photo > a').eq(indexCurrent).removeClass('slideuptovisible');
			sesJqueryObject('#sesalbum_corresponding_photo > a').eq((indexCurrent+4)).addClass('slideuptovisible');
		}else{
			leftAttr = parseInt(64*slideLeft,10)+parseInt(leftAttr,10);
			sesJqueryObject('#sesalbum_corresponding_photo').css('left','-'+leftAttr+'px');
			sesJqueryObject('#sesalbum_corresponding_photo > a').eq(indexCurrent).removeClass('slideuptovisible');
			sesJqueryObject('#sesalbum_corresponding_photo > a').eq((indexCurrent+slideLeft)).addClass('slideuptovisible');
		}
	}
});
sesJqueryObject(document).on('click','#prevSlide',function(){
	var indexCurrent = 	sesJqueryObject('#sesalbum_corresponding_photo > a.slideuptovisible').index();
	var leftAttr = sesJqueryObject('#sesalbum_corresponding_photo').css('left').replace('px','');
	leftAttr = leftAttr.replace('-','');
	leftAttr = parseInt(leftAttr,10);
 if(leftAttr == 0 || countSlide < 4 || indexCurrent == '-1'){
	 //first slide
 }else{
	var type = indexCurrent - 3;
	if(typeof type == 'number' && type > 3 ){
		sesJqueryObject('#sesalbum_corresponding_photo').css('left','-'+(leftAttr-(64*4))+'px');
		sesJqueryObject('#sesalbum_corresponding_photo > a').eq(indexCurrent).removeClass('slideuptovisible');
		sesJqueryObject('#sesalbum_corresponding_photo > a').eq((indexCurrent-4)).addClass('slideuptovisible');
	}else{
		var slideLeft = (countSlide-1)-((countSlide-1) - indexCurrent)
		leftAttr = parseInt(leftAttr,10) -  parseInt(64*type,10);
		if(countSlide-1 > 3 || countSlide-1 == 3 || indexCurrent-type < 4)
			var selectedindex = 3;
		else
			var selectedindex = indexCurrent-type;
		sesJqueryObject('#sesalbum_corresponding_photo').css('left',leftAttr+'px');
		sesJqueryObject('#sesalbum_corresponding_photo > a').eq(indexCurrent).removeClass('slideuptovisible');
		sesJqueryObject('#sesalbum_corresponding_photo > a').eq(selectedindex).addClass('slideuptovisible');
	}
 }
	return false;
});
<?php } ?>
 <?php if(isset($this->status_slideshowPhoto) && !$this->is_ajax){ ?>
sesJqueryObject(document).ready(function(){
	getCorrespondingImg();	
});
<?php } ?>
function getTagData(value){
	if(value){
		url = en4.core.staticBaseUrl+'albums/index/tag-photo/photo_id/'+value;
		openURLinSmoothBox(url);	
		return;
	}
}
<?php if($this->viewer->getIdentity() !=0){ ?>
	sesJqueryObject(document).on('keyup', function (e) {
		if(sesJqueryObject('#'+e.target.id).prop('tagName') == 'INPUT' || sesJqueryObject('#'+e.target.id).prop('tagName') == 'TEXTAREA')
				return true;
		if(sesJqueryObject('#ses_media_lightbox_container').css('display') == 'none'){
			// like code
			if (e.keyCode === 76) {
				if(sesJqueryObject('#sesLikeUnlikeButton').length > 0)
				 sesJqueryObject('#sesLikeUnlikeButton').trigger('click');
			}
			// favourite code
			if (e.keyCode === 70) {
				if(sesJqueryObject('.sesalbum_photoFav').length > 0)
					sesJqueryObject('.sesalbum_photoFav').trigger('click');
			}
			// open photo in lightbox code
			if(e.keyCode === 77){
				if(sesJqueryObject('.sesalbum_view_photo_expend').length>0)	
					sesJqueryObject('.sesalbum_view_photo_expend').trigger('click');
			}
		}
	});
<?php } ?>
function getLikeData(value){
	if(value){
		url = en4.core.staticBaseUrl+'albums/index/like-photo/photo_id/'+value;
		openURLinSmoothBox(url);	
		return;
	}
}
</script>
<?php if(!$this->is_ajax ){ ?>
</div>
<div id="locked_content" style="display:none" class="sesbasic_locked_msg sesbasic_clearfix sesbasic_bxs">
	<div class="sesbasic_locked_msg_img"><i class="fa fa-lock"></i></div>
  <div class="sesbasic_locked_msg_cont">
    <h1><?php echo $this->translate('Locked Photo'); ?></h1>
    <p><?php echo $this->translate('Seems you enter wrong password'); ?> <a href="javascript:;" onClick="window.location.reload();"><?php echo $this->translate('click here'); ?></a> <?php echo $this->translate('to enter password again.'); ?></p>
	</div>    
</div>
<?php if($this->locked){ ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customAlert/sweetalert.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customAlert/sweetalert.js'); ?>
<script type="application/javascript">
 function promptPasswordCheck(){
	 sesJqueryObject('#photo_content').hide();
	 sesJqueryObject('#locked_content').show();
	//var password = prompt("Enter the password ?");
	swal({   
			title: "",   
			text: "<?php echo $this->translate('Enter Password %s', $this->album->getTitle()); ?>",   
			type: "input",   
			showCancelButton: true,   
			closeOnConfirm: false,   
			animation: "slide-from-top",   
			inputPlaceholder: "<?php echo $this->translate('Enter Password'); ?>"
		}, function(inputValue){   
			if (inputValue === false) {
				sesJqueryObject('#photo_content').remove();
				sesJqueryObject('#locked_content').show();
			 return false;
			}
			if (inputValue === "") {    
			 swal.showInputError("<?php echo $this->translate('You need to write something!');  ?>");     
			 return false   
		}
			if(inputValue.toLowerCase() == '<?php echo strtolower($this->password); ?>'){
					sesJqueryObject('#locked_content').remove();
					sesJqueryObject('#photo_content').show();
					setCookieSesalbum('<?php echo $this->album->album_id; ?>');
					doResizeForButton();
					swal.close();
			}else{
			 	swal("Wrong Password", "You wrote: " + inputValue, "error");
				sesJqueryObject('#photo_content').remove();
				sesJqueryObject('#locked_content').show();
			}
			doResizeForButton();
	});
 }
 promptPasswordCheck();
</script>
<?php }else{ ?>
<script type="application/javascript">
 sesJqueryObject(document).ready(function(){
	sesJqueryObject('#locked_content').remove();
	sesJqueryObject('#photo_content').show();
	doResizeForButton();
});
</script>
<?php } ?>
<?php } ?>