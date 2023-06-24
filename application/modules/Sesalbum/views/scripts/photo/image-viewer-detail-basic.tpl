<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: image-viewer-detail-basic.tpl 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<script type="text/javascript">
	function initImage() {
				sesJqueryObject('#gallery-img').apImageZoom({
					  cssWrapperClass: 'custom-wrapper-class'
					, autoCenter: false
					, loadingAnimation: 'throbber'
					, minZoom: 'contain'
					, maxZoom: false
					, maxZoom: 1.0
					, hammerPluginEnabled: false
					, hardwareAcceleration: false
				});
			};
  en4.core.runonce.add(function() {
    var descEls = $$('.albums_viewmedia_info_caption');
    if( descEls.length > 0 ) {
      descEls[0].enableLinks();
    }
    var taggerInstanceSES = window.taggerInstanceSES = new Tagger('media_photo_next_ses', {
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
        'container' : $('media_photo_next_ses')
      },
      'tagListElement' : 'media_tags_ses',
      'existingTags' : <?php echo Zend_Json::encode($this->tags) ?>,
      'suggestProto' : 'request.json',
      'suggestParam' : "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
      'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
      'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
      'enableDelete' : <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>
    });
		// Remove the href attrib if present while tagging
    var nextHref = $('media_photo_next_ses').get('href');
    taggerInstanceSES.addEvents({
      'onBegin' : function() {
			if(sesJqueryObject('#ses_media_lightbox_container').css('display') != '')
				sesJqueryObject('.ses_media_lightbox_options').hide();
        $('media_photo_next_ses').erase('href');
      },
      'onEnd' : function() {
			if(sesJqueryObject('#ses_media_lightbox_container').css('display') != '')
				sesJqueryObject('.ses_media_lightbox_options').show();
        $('media_photo_next_ses').set('href', nextHref);
      }
    });
    var keyupEvent = function(e) {
      if( e.target.get('tag') == 'html' ||
          e.target.get('tag') == 'body' ) {
        if( e.key == 'right' ) {
          $('photo_next').fireEvent('click', e);
          
        } else if( e.key == 'left' ) {
          $('photo_prev').fireEvent('click', e);
         
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
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php if(($this->getAllowRating == 0 && $this->allowShowRating == 1 && $this->total_rating_average != 0 ) || ($this->getAllowRating == 1) ){ ?>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    var pre_rate_viewer = "<?php echo $this->total_rating_average == '' ? '0' : $this->total_rating_average  ;?>";
		<?php if($this->viewer_id == 0){ ?>
			rated_viewer = 0;
		<?php }else if($this->allowShowRating == 1 && $this->allowRating == 0){?>
		var rated_viewer = 3;
		<?php }else if($this->allowRateAgain == 0 && $this->rated){ ?>
		var rated_viewer = 1;
		<?php }else if($this->canRate == 0 && $this->viewer_id != 0){?>
		var rated_viewer = 4;
		<?php }else if(!$this->allowMine){?>
		var rated_viewer = 2;
		<?php }else{ ?>
    var rated_viewer = '90';
		<?php } ?>
    var resource_id_viewer = <?php echo $this->photo->photo_id;?>;
    var total_votes_viewer = <?php echo $this->rating_count;?>;
    var viewer_id = <?php echo $this->viewer_id;?>;
    new_text_viewer = '';

    var rating_over_viewer = window.rating_over_viewer = function(rating) {
      if( rated_viewer == 1 ) {
        $('rating_text_viewer').innerHTML = "<?php echo $this->translate('you have already rated');?>";
				return;
        //set_rating_viewer();
      }
			<?php if(!$this->canRate){ ?>
				else if(rated_viewer == 4){
						 $('rating_text_viewer').innerHTML = "<?php echo $this->translate('rating is not allowed for your member level');?>";
						 return;
				}
			<?php } ?>
			<?php if(!$this->allowMine){ ?>
				else if(rated_viewer == 2){
						 $('rating_text_viewer').innerHTML = "<?php echo $this->translate('rating on own photo not allowed');?>";
						 return;
				}
			<?php } ?>
			<?php if($this->allowShowRating == 1){ ?>
				else if(rated_viewer == 3){
						 $('rating_text_viewer').innerHTML = "<?php echo $this->translate('rating is disabled');?>";
						 return;
				}
			<?php } ?>
			else if( viewer_id == 0 ) {
        $('rating_text_viewer').innerHTML = "<?php echo $this->translate('please login to rate');?>";
				return;
      } else {
        $('rating_text_viewer').innerHTML = "<?php echo $this->translate('click to rate');?>";
        for(var x=1; x<=5; x++) {
          if(x <= rating) {
            $('rate_viewer_'+x).set('class', 'fa fa fa-star');
          } else {
            $('rate_viewer_'+x).set('class', 'fa fa fa-star-o star-disable');
          }
        }
      }
    }
    
    var rating_out_viewer = window.rating_out_viewer = function() {
      if (new_text_viewer != ''){
        $('rating_text_viewer').innerHTML = new_text_viewer;
      }
      else{
        $('rating_text_viewer').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";        
      }
      if (pre_rate_viewer != 0){
        set_rating_viewer();
      }
      else {
        for(var x=1; x<=5; x++) {
          $('rate_viewer_'+x).set('class', 'fa fa fa-star-o star-disable');
        }
      }
			return ;	
    }

     var set_rating_viewer = window.set_rating_viewer = function() {
      var rating_viewert = pre_rate_viewer;
      if (new_text_viewer != ''){
        $('rating_text_viewer').innerHTML = new_text_viewer;
      }
      else{
        $('rating_text_viewer').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
      }
			
      for(var x=1; x<=parseInt(rating_viewert); x++) {
        $('rate_viewer_'+x).set('class', 'fa fa fa-star');
      }
		
      for(var x=parseInt(rating_viewert)+1; x<=5; x++) {
        $('rate_viewer_'+x).set('class', 'fa fa fa-star-o star-disable');
      }

      var remainder = Math.round(rating_viewert)-rating_viewert;
      if (remainder <= 0.5 && remainder !=0){
        var last = parseInt(rating_viewert)+1;
        $('rate_viewer_'+last).set('class', 'fa fa fa-star-half-o');
      }
    }

    var rate_viewer = window.rate_viewer = function(rating) {
      $('rating_text_viewer').innerHTML = "<?php echo $this->translate('Thanks for rating!');?>";
			<?php if($this->allowRateAgain == 0 && !$this->rated){ ?>
						 for(var x=1; x<=5; x++) {
								$('rate_viewer_'+x).set('onclick', '');
							}
					<?php } ?>
     
      (new Request.JSON({
        'format': 'json',
        'url' : '<?php echo $this->url(array('module' => 'sesalbum', 'controller' => 'index', 'action' => 'rate'), 'default', true) ?>',
        'data' : {
          'format' : 'json',
          'rating' : rating,
          'resource_id': resource_id_viewer,
					'resource_type':'<?php echo $this->rating_type; ?>'
        },
        'onSuccess' : function(responseJSON, responseText)
        {
					<?php if($this->allowRateAgain == 0 && !$this->rated){ ?>
							rated_viewer = 1;
					<?php } ?>
					showTooltip(10,10,'<i class="fa fa-star"></i><span>'+("Photo Rated successfully")+'</span>', 'sesbasic_rated_notification');
					total_votes_viewer = responseJSON[0].total;
					var rating_sum = responseJSON[0].rating_sum;
					var totalTxt_viewer = responseJSON[0].totalTxt;
          pre_rate_viewer = rating_sum / total_votes_viewer;
          set_rating_viewer();
          $('rating_text_viewer').innerHTML = responseJSON[0].total+' '+totalTxt_viewer;
          new_text_viewer = responseJSON[0].total+' '+totalTxt_viewer;
        }
      })).send();
    }
    set_rating_viewer();
  });
</script>
<?php } ?>
<div class="ses_media_lightbox_left">
  <div class="ses_media_lightbox_item_wrapper">
    <div class="ses_media_lightbox_item">
      <div id="mainImageContainer">
        <div id="media_photo_next_ses" style="display:inline;">
        <?php 
         $className= '';
         $cssDisplay = 'block';
         if($this->locked){
         			$imageUrl = 'application/modules/Sesalbum/externals/images/locked-album.jpg';
              $className = 'ses-blocked-album';
              $cssDisplay = 'none';
            }
        ?>
        <?php if(isset($this->imagePrivateURL)){
                $imageUrl = $this->imagePrivateURL;
                $className = 'ses-private-image';
              }else if(empty($imageUrl)){
              	$imageUrl = $this->photo->getPhotoUrl('','',$this->stringD);
             	}
          ?>
          <?php echo $this->htmlImage($imageUrl, $this->photo->getTitle(), array(
                'id' => 'gallery-img',
                'class' =>$className,
                'display'=>$cssDisplay,
              )); ?>
        </div>
      </div>
    </div>
  </div>
   <?php if(isset($this->imagePrivateURL)){
          $imageUrl = $this->imagePrivateURL;
         }else
         	$imageUrl =	$this->photo->getPhotoUrl(); 
          ?>
  <span id="image-src-sesalbum-lightbox-hidden" style="display:none;"><?php echo $imageUrl; ?></span>
  <span id="image-main-sesalbum-lightbox-hidden" style="display:none;"><?php echo $this->photo->getPhotoUrl('','','string'); ?></span>
  <div class="ses_media_lightbox_nav_btns">
    <?php 
				if(array_key_exists('user',$this->params) || $this->viewWidget){
        	// photo item for tagged map user
         	$taggedUserMap = true;
       	}else
        	$taggedUserMap = false;
     if($this->toArray){
       if(!$taggedUserMap){
        if(!empty($this->previousPhoto))
        	$previousURL = Engine_Api::_()->sesalbum()->getImageViewerHref($this->previousPhoto[0],array_merge($this->params,$this->extraParamsPrevious));
          if(!empty($previousURL))
          $previousImageURL = $this->previousPhoto->getPhotoUrl('','',$this->stringD);
        }else{
         if(!empty($this->previousPhoto[0]['photo_id']))
        	$previousURL = Engine_Api::_()->sesalbum()->getImageViewerHref(Engine_Api::_()->getItem('photo',$this->previousPhoto[0]['photo_id']),array_merge($this->params,$this->extraParamsPrevious));
          if(!empty($previousURL))
          $previousImageURL = Engine_Api::_()->getItem('photo',$this->previousPhoto[0]['photo_id'])->getPhotoUrl('','',$this->stringD);
        }
       }else{
       		if(!$taggedUserMap){
        if(!empty($this->previousPhoto))
        	$previousURL = Engine_Api::_()->sesalbum()->getImageViewerHref($this->previousPhoto,array_merge($this->params,$this->extraParamsPrevious));
          if(!empty($previousURL))
          $previousImageURL = $this->previousPhoto->getPhotoUrl('','',$this->stringD);
        }else{
         if(!empty($this->previousPhoto->photo_id))
        	$previousURL = Engine_Api::_()->sesalbum()->getImageViewerHref(Engine_Api::_()->getItem('photo',$this->previousPhoto->photo_id),array_merge($this->params,$this->extraParamsPrevious));
          if(!empty($previousURL))
          $previousImageURL = Engine_Api::_()->getItem('photo',$this->previousPhoto->photo_id)->getPhotoUrl('','',$this->stringD);
        }
       }
        if(isset($previousURL)){
        	if(isset($this->imagePrivateURL))
        	$previousImageURL = $this->imagePrivateURL;
        
        if(isset($this->previousPhoto[0]))
          $imagePrevUrl =  Engine_Api::_()->getItem('photo',$this->previousPhoto[0]['photo_id']) ;
        else
        	 $imagePrevUrl = $this->previousPhoto; 
         
      ?>
    <a class="ses_media_lightbox_nav_btn_prev" style="display:block" href="<?php echo $imagePrevUrl->getHref(); ?>" title="<?php echo $this->translate('Previous'); ?>" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $previousImageURL; ?>','<?php echo $previousURL; ?>');return false;" id="nav-btn-prev"></a>
    <?php }     		
    		 if($this->toArray){
          if(!$taggedUserMap){
          if(!empty($this->nextPhoto))
	          $nextURL = Engine_Api::_()->sesalbum()->getImageViewerHref($this->nextPhoto[0],array_merge($this->params,$this->extraParamsNext));
            if(!empty($nextURL))
            $nextImageURL  = $this->nextPhoto->getPhotoUrl('','',$this->stringD);
          }else{
          if(!empty($this->nextPhoto[0]['photo_id']))
  	        $nextURL = Engine_Api::_()->sesalbum()->getImageViewerHref(Engine_Api::_()->getItem('photo',$this->nextPhoto[0]['photo_id']),array_merge($this->params,$this->extraParamsNext));
            if(!empty($nextURL))
            $nextImageURL = Engine_Api::_()->getItem('photo',$this->nextPhoto[0]['photo_id'])->getPhotoUrl('','',$this->stringD);
          }
         }else{
         		 if(!$taggedUserMap){
          if(!empty($this->nextPhoto))
	          $nextURL = Engine_Api::_()->sesalbum()->getImageViewerHref($this->nextPhoto,array_merge($this->params,$this->extraParamsNext));
            if(!empty($nextURL))
            $nextImageURL  = $this->nextPhoto->getPhotoUrl('','',$this->stringD);
          }else{
          if(!empty($this->nextPhoto->photo_id))
  	        $nextURL = Engine_Api::_()->sesalbum()->getImageViewerHref(Engine_Api::_()->getItem('photo',$this->nextPhoto->photo_id),array_merge($this->params,$this->extraParamsNext));
            if(!empty($nextURL))
            $nextImageURL = Engine_Api::_()->getItem('photo',$this->nextPhoto->photo_id)->getPhotoUrl('','',$this->stringD);
          }
         }
        if(!empty($nextURL)){
        	if(isset($this->imagePrivateURL))
        	$nextImageURL = $this->imagePrivateURL;
          
          if(isset($this->nextPhoto[0]))
          	$imageNextUrl =  Engine_Api::_()->getItem('photo',$this->nextPhoto[0]['photo_id']) ;
          else
           	$imageNextUrl =  $this->nextPhoto;
       ?>
       
    <a class="ses_media_lightbox_nav_btn_next" style="display:block" href="<?php $imageNextUrl->getHref(); ?>" title="<?php echo $this->translate('Next'); ?>" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $nextImageURL; ?>','<?php echo $nextURL; ?>');return false;" id="nav-btn-next"></a>
    <?php } ?>
  </div>
  <div class="ses_media_lightbox_options">
    <div class="ses_media_lightbox_options_owner">
    	<?php $albumUserDetails = Engine_Api::_()->user()->getUser($this->photo->owner_id); ?>  
      <?php echo $this->htmlLink($albumUserDetails->getHref(), $this->itemPhoto($albumUserDetails, 'thumb.icon'), array('class' => 'userthumb')); ?>
      <?php echo $this->htmlLink($albumUserDetails->getHref(), $albumUserDetails->getTitle()); ?>&nbsp;&nbsp;&bull;&nbsp;&nbsp;
    </div>
    <div class="ses_media_lightbox_options_name">
      <?php echo $this->translate('In %1$s', $this->htmlLink( Engine_Api::_()->sesalbum()->getHref($this->album->getIdentity()), $this->string()->truncate($this->album->getTitle(),Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.title.truncate',35)))); ?>
    </div>
    <?php if( $this->canEdit && Engine_Api::_()->user()->getViewer()->getIdentity() != 0): ?>
      <div class="ses_media_lightbox_rotate_option">
        <a class="sesalbum_icon_photos_rotate_ccw" id="ses-rotate-90" href="javascript:void(0)" onclick="sesRotate('<?php echo $this->photo->getIdentity() ?>','90')">&nbsp;</a>
        <a class="sesalbum_icon_photos_rotate_cw" id="ses-rotate-270" href="javascript:void(0)" onclick="sesRotate('<?php echo $this->photo->getIdentity() ?>','270')">&nbsp;</a>
        <a class="sesalbum_icon_photos_flip_horizontal" id="ses-rotate-horizontal"  href="javascript:void(0)" onclick="sesRotate('<?php echo $this->photo->getIdentity() ?>','horizontal')">&nbsp;</a>
        <a class="sesalbum_icon_photos_flip_vertical" id="ses-rotate-vertical"  href="javascript:void(0)" onclick="sesRotate('<?php echo $this->photo->getIdentity() ?>','vertical')">&nbsp;</a>
      </div>
    <?php endif ?>
    <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0){ ?>
      <div class="ses_media_lightbox_options_btns">
        <a class="sesbasic_icon_btn sesbasic_icon_msg_btn smoothbox" href="<?php echo $this->url(array('module'=> 'sesalbum', 'controller' => 'index', 'action' => 'message','photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'),'sesalbum_extended',true); ?>" onclick="openURLinSmoothBox(this.href);return false;"><i class="fa fa-envelope"></i></a>     
        <?php if($this->canComment){ ?> 
        <?php $LikeStatus = Engine_Api::_()->sesalbum()->getLikeStatusPhoto($this->photo->photo_id); ?>
        <a href="javascript:void(0);" id="sesLightboxLikeUnlikeButton" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn<?php echo $LikeStatus === true ? ' button_active' : '' ;  ?>"><i class="fa fa-thumbs-up"></i><span id="like_unlike_count"><?php echo $this->photo->like_count; ?></span></a>
        <?php } ?>
        <?php if($this->canFavourite){ ?>
        <?php $albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album_photo','resource_id'=>$this->photo->photo_id)); ?>
        <a href="javascript:;" data-src='<?php echo $this->photo->photo_id; ?>' id="sesalbum_favourite" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesalbum_photoFav<?php echo ($albumFavStatus)>0 ? ' button_active' : '' ; ?>"><i class="fa fa-heart"></i><span><?php echo $this->photo->favourite_count; ?></span></a>
        <?php } ?>
      </div>
    <?php } ?>
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.tags',1) == 1 && $this->canTag){ ?>
      <div class="ses_media_lightbox_options_btn ses_media_lightbox_tag_btn">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Add Tag'), array('onclick'=>'taggerInstanceSES.begin();')).'</div>';
    } ?>
    <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.share',1) == 1){ ?>
      <div class="ses_media_lightbox_options_btn ses_media_lightbox_share_btn">
        <a href="javascript:;" class='smoothbox' onclick="openURLinSmoothBox('<?php echo $this->url(array("action" => "share", "type" => "album_photo", "photo_id" => $this->photo->getIdentity(),"format" => "smoothbox"), 'sesalbum_general', true); ?>')"><?php echo $this->translate('Share'); ?></a>
      </div>
    <?php } ?>
    <div class="ses_media_lightbox_options_btn ses_media_lightbox_more_btn">
      <div class="ses_media_lightbox_options_box">
        <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && $this->canDelete){ ?>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.delete',1) == 1){ ?>
            <a href="javascript:;" class='smoothbox' onclick="openURLinSmoothBox('<?php echo $this->url(array('reset' => false, 'action' => 'delete', 'format' => 'smoothbox')); ?>')"><?php echo $this->translate('Delete'); ?></a>
          <?php } ?>
        <?php } ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.report',1) == 1 && Engine_Api::_()->user()->getViewer()->getIdentity() != 0){ ?>
          <a href="javascript:;" class='smoothbox' onclick="openURLinSmoothBox('<?php echo $this->url(array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'),'default',true); ?>')"><?php echo $this->translate('Report'); ?></a>
        <?php } ?>
        <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.profilepic',1) == 1){ ?>
          <a href="javascript:;" class='smoothbox' onclick="openURLinSmoothBox('<?php echo $this->url(array('route' => 'user_extended', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'),'user_extended',true); ?>')"><?php echo $this->translate('Make Profile Photo'); ?></a>
        <?php }  ?>
        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.add.download',1) == 1  && isset($this->canDownload) && $this->canDownload == 1){ ?>
          <a class="ses-album-photo-download" href="<?php echo $this->url(array('module' => 'sesalbum', 'action' => 'download', 'photo_id' => $this->photo->photo_id,'type'=>'photo'), 'sesalbum_general', true); ?>"><?php echo $this->translate('Download'); ?></a>
        <?php } ?>
         <a href="javascript:;" onclick="slideShow()"><?php echo $this->translate('Slideshow'); ?></a>
         <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && (Engine_Api::_()->user()->getViewer()->level_id == 1 || Engine_Api::_()->user()->getViewer()->level_id == 2)): ?>
            <?php echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "index", "action" => "featured", "route" => "sesalbum_extended", "photo_id" => $this->photo->getIdentity(),"type" =>"photos"),  $this->translate((($this->photo->is_featured == 1) ? "Unmark as Featured" : "Mark Featured")), array("class" => "sesalbum_admin_featured")) ?>          <?php echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "index", "action" => "sponsored", "route" => "sesalbum_extended", "photo_id" => $this->photo->getIdentity(),"type" =>"photos"),  $this->translate((($this->photo->is_sponsored == 1) ? "Unmark as Sponsored" : "Mark Sponsored")), array("class" => "sesalbum_admin_sponsored")) ?>
            <?php if(strtotime($this->photo->endtime) < strtotime(date("Y-m-d")) && $this->photo->offtheday == 1){$itemofftheday=0;}else{$itemofftheday = $this->photo->offtheday;}
                echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "index", "action" => "offtheday", "route" => "sesalbum_extended","id" => $this->photo->photo_id, "type" => "album_photo", "param" => (($itemofftheday == 1) ? 0 : 1)),  $this->translate((($itemofftheday == 1) ? "Edit of the Day" : "Make of the Day")), array("class" => "smoothboxOpen")); ?>
    	<?php endif; ?>
      </div>
      <a href="javascript:void(0);"><?php echo $this->translate('Option'); ?></a>
    </div>
  </div>
  <div class="ses_media_lightbox_fullscreen_btn">
    <a id="fsbutton" onclick="toogle()" href="javascript:;" title="<?php echo $this->translate('Enter Fullscreen'); ?>"><i class="fa fa-expand"></i></a>
  </div>
</div>
<div class="ses_media_lightbox_information">
<div id="heightOfImageViewerContent">
  <div id="flexcroll" >
    <div class="ses_media_lightbox_media_info" id="ses_media_lightbox_media_info">
      <div class="ses_media_lightbox_information_top sesbasic_clearfix">
        <?php $albumUserDetails = Engine_Api::_()->user()->getUser($this->photo->owner_id); ?>
        <div class="ses_media_lightbox_author_photo">  
          <?php echo $this->htmlLink($albumUserDetails->getHref(), $this->itemPhoto($albumUserDetails, 'thumb.icon')); ?>
        </div>
        <div class="ses_media_lightbox_author_info">
          <span class="ses_media_lightbox_author_name">
            <?php echo $this->htmlLink($albumUserDetails->getHref(), $albumUserDetails->getTitle()); ?>
          </span>
          <span class="ses_media_lightbox_posted_date sesbasic_text_light">
            <?php echo date('F j',strtotime($this->photo->creation_date)); ?>
          </span>
        </div>
      </div>
      <div class="ses_media_lightbox_item_title" id="ses_title_get"> <?php echo $this->photo->getTitle(); ?></div>
      <div class="ses_media_lightbox_item_description" id="ses_title_description"><?php echo nl2br($this->photo->getDescription()) ?></div>
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum_enable_location', 1)){ ?>
      <div class="ses_media_lightbox_item_location">
        <span class="sesbasic_text_light" id="seslocationIn"><?php if($this->photo->location != '') echo $this->translate("In"); ?></span>
        <span>
            <a id="ses_location_data" href="<?php echo $this->url(array("module"=> "sesalbum", "controller" => "photo", "action" => "location", "type" => "location","album_id" =>$this->photo->album_id, "photo_id" => $this->photo->getIdentity()),'default',true); ?>" onclick="openURLinSmoothBox(this.href);return false;"><?php echo $this->photo->location; ?></a>
         </span>
      </div>
      <?php } ?>
      <div class="ses_media_lightbox_item_tags sesbasic_text_light" id="media_tags_ses" style="display: none;">
        <?php echo $this->translate('Tagged:') ?>
      </div>
      <?php if(($this->getAllowRating == 0 && $this->allowShowRating == 1 && $this->total_rating_average != 0 ) || ($this->getAllowRating == 1) ){ ?>
        <div id="album_rating" class="sesbasic_rating_star ses_media_lightbox_item_rating" onmouseout="rating_out_viewer();">
          <span id="rate_viewer_1" class="fa fa fa-star" <?php  if ($this->viewer_id && $this->ratedAgain && $this->allowMine &&  $this->allowRating):?>onclick="rate_viewer(1);"<?php  endif; ?> onmouseover="rating_over_viewer(1);"></span>
          <span id="rate_viewer_2" class="fa fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate_viewer(2);"<?php endif; ?> onmouseover="rating_over_viewer(2);"></span>
          <span id="rate_viewer_3" class="fa fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate_viewer(3);"<?php endif; ?> onmouseover="rating_over_viewer(3);"></span>
          <span id="rate_viewer_4" class="fa fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate_viewer(4);"<?php endif; ?> onmouseover="rating_over_viewer(4);"></span>
          <span id="rate_viewer_5" class="fa fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate_viewer(5);"<?php endif; ?> onmouseover="rating_over_viewer(5);"></span>
          <span id="rating_text_viewer" class="sesbasic_rating_text"><?php echo $this->translate('click to rate');?></span>
        </div>
      <?php } ?>
      <?php if($this->viewer()->getIdentity() == $this->photo->owner_id){ ?>
        <div class="ses_media_lightbox_item_edit_link">
          <a id="editDetailsLink" href="javascript:void(0)" class="sesbasic_button">
            <i class="fa fa-pencil sesbasic_text_light"></i>  
            <?php echo $this->translate('Edit Details'); ?>
          </a>
        </div>
      <?php } ?>
    </div>
  <?php if($this->canEdit){ ?>
    <div class="ses_media_lightbox_edit_form" id="editDetailsForm" style="display:none;">
      <form id="changePhotoDetails">
        <input  name="title" id="titleSes" type="text" placeholder="<?php echo $this->translate('Title'); ?>" />
        <textarea id="descriptionSes" name="description" value="" placeholder="<?php echo $this->translate('Description'); ?>"></textarea>
        <input type="hidden" id="photo_id_ses" name="photo_id" value="<?php echo $this->photo->photo_id; ?>" />
        <input type="hidden" id="album_id_ses" name="album_id" value="<?php echo $this->photo->album_id; ?>" />
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum_enable_location', 1)){ ?>
        <input id="locationSes" name="location"  type="text" placeholder="<?php echo $this->translate('Location'); ?>">
        <input type="hidden" id="latSes" name="lat" value="" />
        <input type="hidden" id="lngSes" name="lng" value="" />
        <div id="map-canvas" style="height:200px; margin-bottom:10px;"></div>
      <?php } ?>
        <button id="saveDetailsSes"><?php echo $this->translate('Save Changes'); ?></button>
        <button id="cancelDetailsSes"><?php echo $this->translate('Cancel'); ?></button>
      </form>
    </div>
  <?php } ?>
    <div class="ses_media_lightbox_comments clear">
      <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedcomment')){ ?>
      <?php echo $this->action("list", "comment", "sesadvancedcomment", array("type" => "album_photo", "id" => $this->photo->getIdentity())); 
        }else{
          echo $this->action("list", "comment", "core", array("type" => "album_photo", "id" => $this->photo->getIdentity())); 
        }    
    ?> </div>
  </div>
  </div>
</div>
<a href="javascript:;" class="cross ses_media_lightbox_close_btn exit_lightbox"><i class="fa fa-close sesbasic_text_light"></i></a>
<a href="javascript:;" class="ses_media_lightbox_close_btn exit_fullscreen" title="<?php echo $this->translate('Exit Fullscreen'); ?>" onclick="toogle()"><i class="fa fa-close sesbasic_text_light"></i></a>
<script type="application/javascript">
function sespromptPasswordCheck(){
	var password = prompt("Enter the password for album '<?php echo $this->album->getTitle(); ?>'");
	if(typeof password != 'object' && password.toLowerCase() == '<?php echo strtolower($this->password); ?>'){
			sesJqueryObject('.ses_media_lightbox_information').show();
			sesJqueryObject('#gallery-img').attr('src',sesJqueryObject('#image-main-sesalbum-lightbox-hidden').html());
			setCookieSesalbum('<?php echo $this->album->album_id; ?>');
	}else{
		sesJqueryObject('.ses_media_lightbox_options_btns').hide();
		sesJqueryObject('.ses_media_lightbox_tag_btn').hide();
		sesJqueryObject('.ses_media_lightbox_share_btn').hide();
		sesJqueryObject('.ses_media_lightbox_more_btn').hide();
	}
 }
</script>