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
if(!$this->is_ajax && !$this->is_related && isset($this->docActive)){
	$imageURL = $this->album->getPhotoUrl();
	if(strpos($this->album->getPhotoUrl(),'http') === false)
          	$imageURL = (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://". $_SERVER['HTTP_HOST'].$this->album->getPhotoUrl() : "http://". $_SERVER['HTTP_HOST'].$this->album->getPhotoUrl();
  $this->doctype('XHTML1_RDFA');
  $this->headMeta()->setProperty('og:url', (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://".$_SERVER['HTTP_HOST'].$this->album->getHref() : "http://".$_SERVER['HTTP_HOST'].$this->album->getHref());
  $this->headMeta()->setProperty('og:title', strip_tags($this->album->getTitle()));
  $this->headMeta()->setProperty('og:description', strip_tags($this->album->getDescription()));
  $this->headMeta()->setProperty('og:image',$imageURL);
  $this->headMeta()->setProperty('twitter:card','summary_large_image');
  $this->headMeta()->setProperty('twitter:site', 'website');
  $this->headMeta()->setProperty('twitter:url', (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://".$_SERVER['HTTP_HOST'].$this->album->getHref() : "http://".$_SERVER['HTTP_HOST'].$this->album->getHref());
  $this->headMeta()->setProperty('twitter:image',$imageURL);
  $this->headMeta()->setProperty('twitter:title', strip_tags($this->album->getTitle()));
  $this->headMeta()->setProperty('twitter:description', strip_tags($this->album->getDescription()));
}
 if(isset($this->identityForWidget) && !empty($this->identityForWidget)){
    $randonNumber = $this->identityForWidget;
 }else{
    $randonNumber = $this->identity; 
 } ?>
<?php if(!$this->is_ajax && !$this->is_related){ ?>
 <div id="album_content" style="display:none">
 <?php 
 }
if(isset($this->canEdit)){
// First, include the Webcam.js JavaScript Library 
  $base_url = $this->layout()->staticBaseUrl;
  $this->headScript()->appendFile($base_url . 'application/modules/Sesbasic/externals/scripts/webcam.js'); 
  }
?>
<?php if(($this->mine || $this->canEdit) && !$this->is_related && !$this->is_ajax){ ?>
<script type="text/javascript">
    var SortablesInstance;
    en4.core.runonce.add(function() {
      $$('.sesalbum_photos_flex_view > li').addClass('sortable');
      SortablesInstance = new Sortables($$('.sesalbum_photos_flex_view'), {
        clone: true,
        constrain: true,
        //handle: 'span',
        onComplete: function(e) {
          var ids = [];
          $$('.sesalbum_photos_flex_view > li').each(function(el) {						
            	ids.push(el.get('id').match(/\d+/)[0]);
          });
					<?php if($this->view_type == 'masonry') { ?>
						sesJqueryObject('#ses-image-view').flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>});
					<?php } ?>
          // Send request
          var url =  en4.core.baseUrl+"albums/order/<?php echo $this->album_id; ?>";
          var request = new Request.JSON({
            'url' : url,
            'data' : {
              format : 'json',
              order : ids
            }
          });
          request.send();
        }
      });
    });			
  </script>
<?php } ?>
<?php 
            $editItem = true;
            if($this->canEditMemberLevelPermission == 1){
              if($this->viewer->getIdentity() == $this->album->owner_id){
                $editItem = true;
              }else{
                $editItem = false;
              }
            }else if($this->canEditMemberLevelPermission == 2){
               $editItem = true;
            }else{
                $editItem = false;
            } 
            $deleteItem = true;
            if($this->canDeleteMemberLevelPermission == 1){
              if($this->viewer->getIdentity() == $this->album->owner_id){
                $deleteItem = true;
              }else{
                $deleteItem = false;
              }
            }else if($this->canDeleteMemberLevelPermission == 2){
               $deleteItem = true;
            }else{
                $deleteItem = false;
            }
             $createItem = true;
            if($this->canCreateMemberLevelPermission == 1){
              if($this->viewer->getIdentity() == $this->album->owner_id){
                $createItem = true;
              }else{
                $createItem = false;
              }
            }else{
                $createItem = false;
            }
          ?>
<?php
 if(!$this->is_ajax && !$this->is_related){
  $this->headTranslate(array(
    'Save', 'Cancel', 'delete',
  ));
?>
<script type="text/javascript">
<?php if(($this->getAllowRating == 0 && $this->allowShowRating == 1 && $this->total_rating_average != 0 ) || ($this->getAllowRating == 1) ){ ?>
  en4.core.runonce.add(function() {
    var pre_rate = "<?php echo $this->total_rating_average == '' ? 0 : $this->total_rating_average  ;?>";
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
    var resource_id = <?php echo $this->album->album_id;?>;
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
						 $('rating_text').innerHTML = "<?php echo $this->translate('rating on own album is not allowed');?>";
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
            $('rate_'+x).set('class', 'fa fa-star');
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
        $('rate_'+x).set('class', 'fa fa-star');
      }

      for(var x=parseInt(rating)+1; x<=5; x++) {
        $('rate_'+x).set('class', 'fa fa fa-star-o star-disable');
      }

      var remainder = Math.round(rating)-rating;
      if (remainder <= 0.5 && remainder !=0){
        var last = parseInt(rating)+1;
        $('rate_'+last).set('class', 'fa fa-star-half-o');
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
					showTooltip(10,10,'<i class="fa fa-star"></i><span>'+("Album Rated successfully")+'</span>', 'sesbasic_rated_notification');
					total_votes = responseJSON[0].total;
					var rating_sum = responseJSON[0].rating_sum;
					var totalTxt = responseJSON[0].totalTxt;
          pre_rate = rating_sum / total_votes;
          set_rating();
          $('rating_text').innerHTML = responseJSON[0].total+' '+totalTxt;
          new_text = responseJSON[0].total+' '+totalTxt;
        }
      })).send();
    }
    set_rating();
  });
<?php } ?>
sesJqueryObject(document).click(function(event){
	if(event.target.id != 'sesalbum_dropdown_btn' && event.target.id != 'a_btn' && event.target.id != 'i_btn'){
		sesJqueryObject('#sesalbum_dropdown_btn').find('.sesalbum_option_box1').css('display','none');
		sesJqueryObject('#a_btn').removeClass('active');
	}
	if(event.target.id == 'change_cover_txt' || event.target.id == 'cover_change_btn_i' || event.target.id == 'cover_change_btn'){
		if(sesJqueryObject('#sesalbum_album_change_cover_op').hasClass('active'))
			sesJqueryObject('#sesalbum_album_change_cover_op').removeClass('active')
		else
			sesJqueryObject('#sesalbum_album_change_cover_op').addClass('active')
	}else{
			sesJqueryObject('#sesalbum_album_change_cover_op').removeClass('active')
	}
	if(event.target.id == 'a_btn'){
			if(sesJqueryObject('#a_btn').hasClass('active')){
				sesJqueryObject('#a_btn').removeClass('active');
				sesJqueryObject('.sesalbum_option_box1').css('display','none');
			}
			else{
				sesJqueryObject('#a_btn').addClass('active');
				sesJqueryObject('.sesalbum_option_box1').css('display','block');
			}
		}else if(event.target.id == 'i_btn'){
			if(sesJqueryObject('#a_btn').hasClass('active')){
				sesJqueryObject('#a_btn').removeClass('active');
				sesJqueryObject('.sesalbum_option_box1').css('display','none');
			}
			else{
				sesJqueryObject('#a_btn').addClass('active');
				sesJqueryObject('.sesalbum_option_box1').css('display','block');
			}
	}	
});
</script>
<div class="sesalbum_cover_wrapper sesbasic_bxs" style="height:<?php echo $this->height_cover; ?>px">
  <div class="sesalbum_cover_container" style="height:<?php echo $this->height_cover; ?>px">
    <?php if(isset($this->album->art_cover) && $this->album->art_cover != 0 && $this->album->art_cover != ''){ 
           $albumArtCover =	Engine_Api::_()->storage()->get($this->album->art_cover, '')->getPhotoUrl(); 
     }else
        $albumArtCover =''; 
  ?>
    <div id="sesalbum_cover_default" class="sesalbum_cover_thumbs" style="display:<?php echo $albumArtCover == '' ? 'block' : 'none'; ?>;">
    <ul>
    <?php
       $albumImage = Engine_Api::_()->sesalbum()->getAlbumPhoto($this->album->getIdentity(),0,3); 
       $countTotal = count($albumImage);
       foreach( $albumImage as $photo ){
           $imageURL = $photo->getPhotoUrl('thumb.normalmain','','notcheck');
            if(strpos($imageURL,'http') === false){
              $http_s = (!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://";
              $imageURL = $imageURL;
             }
             $widthPer = $countTotal == 3 ? "33.33" : ($countTotal == 2 ? "50" : '100') ; 
             ?> 
              <li style="height:<?php echo $this->height_cover; ?>px;width:<?php echo $widthPer; ?>%">
                  <span style="background-image:url(<?php echo $imageURL; ?>);"></span> 
              </li>
      <?php } ?>
   </ul>
   </div>
    <span class="sesalbum_cover_image" id="cover_art_work_image" style="background-image:url(<?php echo $albumArtCover; ?>); <?php echo (isset($this->album->position_cover) && !is_null($this->album->position_cover)) ? 'background-position:'.$this->album->position_cover : ''; ?>;height:<?php echo $this->height_cover; ?>px;"></span>
   <div style="display:none;" id="sesalbum-pos-btn" class="sesalbum_cove_positions_btns">
      <a id="saveCoverPosition" href="javascript:;" class="sesalbum_button"><?php echo $this->translate("Save");?></a>
      <a href="javascript:;" id="cancelCoverPosition" class="sesalbum_button"><?php echo $this->translate("Cancel");?></a>
    </div>
    <span class="sesalbum_cover_fade"></span>
    <?php if( $this->mine || $this->canEdit || $editItem): ?>
      <div class="sesalbum_album_coverphoto_op" id="sesalbum_album_change_cover_op">
        <a href="javascript:;" id="cover_change_btn"><i class="fa fa-camera" id="cover_change_btn_i"></i><span id="change_cover_txt"><?php echo $this->translate("Upload Cover Photo"); ?></span></a>
        <div class="sesalbum_album_coverphoto_op_box sesalbum_option_box">
          <i class="sesalbum_album_coverphoto_op_box_arrow"></i>
          <?php if($this->canEdit){ ?>
            <input type="file" id="uploadFileSesalbum" name="art_cover" onchange="uploadCoverArt(this);"  style="display:none" />
            <a id="uploadWebCamPhoto" href="javascript:;"><i class="fa fa-camera"></i><?php echo $this->translate("Take Photo"); ?></a>
            <a id="coverChangeSesalbum" data-src="<?php echo $this->album->art_cover; ?>" href="javascript:;"><i class="fa fa-plus"></i><?php echo (isset($this->album->art_cover) && $this->album->art_cover != 0 && $this->album->art_cover != '') ? $this->translate('Change Cover Photo') : $this->translate('Add Cover Photo');; ?></a>
            <a id="fromExistingAlbum" href="javascript:;"><i class="fa fa-picture-o"></i><?php echo $this->translate("Choose From Existing"); ?></a>
             <a id="coverRemoveSesalbum" style="display:<?php echo (isset($this->album->art_cover) && $this->album->art_cover != 0 && $this->album->art_cover != '') ? 'block' : 'none' ; ?>;" data-src="<?php echo $this->album->art_cover; ?>" href="javascript:;"><i class="fa fa-trash"></i><?php echo $this->translate('Remove Cover Photo'); ?></a>
          <?php } ?>
        </div>
      </div>
    <?php endif;?>
    <div class="sesalbum_cover_inner">
      <div class="sesalbum_cover_album_cont sesbasic_clearfix">
        <div class="sesalbum_cover_album_cont_inner">
          <div class="sesalbum_cover_album_owner_photo">
            <?php $coverAlbumPhoto = $this->album->getPhotoUrl('thumb.icon','status','notcheck'); 
                if($coverAlbumPhoto == ''){
                 $user = Engine_Api::_()->getItem('user',$this->album->owner_id);
                 echo $this->itemPhoto($user, 'thumb.profile');
                }else{
                 $photoCover = Engine_Api::_()->getItem('album_photo',$this->album->photo_id);
                 $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($photoCover); ?>
                <a class="ses-image-viewer" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $photoCover->getPhotoUrl('','','notcheck'); ?>','<?php echo $imageURL	; ?>')" href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($photoCover->getIdentity(),$photoCover->album_id); ?>"> 
                <img src="<?php echo $coverAlbumPhoto; ?>" />	
                </a>
              <?php } ?>
          </div>
          <div class="sesalbum_cover_album_info">
            <h2 class="sesalbum_cover_title">
              <?php echo trim($this->album->getTitle()) ? $this->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>'; ?>
            </h2>
            <div class="sesalbum_cover_date clear sesbasic_clearfix">
              <?php echo  $this->translate('by').' '.$this->album->getOwner()->__toString(); ?>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $this->translate('Added %1$s', $this->timestamp($this->album->creation_date)); ?>
            </div>
            <div class="clear sesbasic_clearfix sesalbum_cover_album_info_btm">
              <?php if(($this->getAllowRating == 0 && $this->allowShowRating == 1 && $this->total_rating_average != 0 ) || ($this->getAllowRating == 1) ){ ?>
                <div id="album_rating" class="sesbasic_rating_star sesalbum_view_album_rating" onmouseout="rating_out();">
                  <span id="rate_1" class="fa fa-star" <?php  if ($this->viewer_id && $this->ratedAgain && $this->allowMine &&  $this->allowRating):?>onclick="rate(1);"<?php  endif; ?> onmouseover="rating_over(1);"></span>
                  <span id="rate_2" class="fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
                  <span id="rate_3" class="fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
                  <span id="rate_4" class="fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
                  <span id="rate_5" class="fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
                  <span id="rating_text" class="sesbasic_rating_text"><?php echo $this->translate('click to rate');?></span>
                </div>
              <?php } ?>
              <div class="sesalbum_cover_stats">
                <div title="<?php echo $this->translate(array('%s photo', '%s photos', $this->album->count()), $this->locale()->toNumber($this->album->count()))?>">
                  <span class="sesalbum_cover_stat_count"><?php echo $this->album->count(); ?></span>
                  <span class="sesalbum_cover_stat_txt"><?php echo str_replace(',','',preg_replace('/[0-9]+/', '', $this->translate(array('%s Photo', '%s Photos', $this->album->count()), $this->locale()->toNumber($this->album->count())))); ?></span>
                </div>
                <div title="<?php echo $this->translate(array('%s view', '%s views', $this->album->view_count), $this->locale()->toNumber($this->album->view_count))?>">
                  <span class="sesalbum_cover_stat_count"><?php echo $this->album->view_count; ?></span>
                  <span class="sesalbum_cover_stat_txt"><?php echo str_replace(',','',preg_replace('/[0-9]+/','',$this->translate(array('%s View', '%s Views', $this->album->view_count), $this->locale()->toNumber($this->album->view_count)))); ?></span>
                </div>
                <div title="<?php echo $this->translate(array('%s like', '%s likes', $this->album->like_count), $this->locale()->toNumber($this->album->like_count))?>">
                  <span class="sesalbum_cover_stat_count"><?php echo $this->album->like_count; ?></span>
                  <span class="sesalbum_cover_stat_txt"><?php echo str_replace(',','',preg_replace('/[0-9]+/', '', $this->translate(array('%s Like', '%s Likes', $this->album->like_count), $this->locale()->toNumber($this->album->like_count)))); ?></span>
                </div>
                <div title="<?php echo $this->translate(array('%s comment', '%s comments',$this->album->comment_count), $this->locale()->toNumber($this->album->comment_count))?>">
                  <span class="sesalbum_cover_stat_count"><?php echo $this->album->comment_count; ?></span>
                  <span class="sesalbum_cover_stat_txt"><?php echo str_replace(',','',preg_replace('/[0-9]+/', '',  $this->translate(array('%s Comment', '%s Comments',$this->album->comment_count), $this->locale()->toNumber($this->album->comment_count)))); ?></span>
                </div>
                <div title="<?php echo $this->translate(array('%s favourite', '%s favourites', $this->album->favourite_count), $this->locale()->toNumber($this->album->favourite_count))?>">
                  <span class="sesalbum_cover_stat_count"><?php echo $this->album->favourite_count; ?></span>
                  <span class="sesalbum_cover_stat_txt"><?php echo str_replace(',','',preg_replace('/[0-9]+/', '', $this->translate(array('%s Favourite', '%s Favourites', $this->album->favourite_count), $this->locale()->toNumber($this->album->favourite_count)))); ?></span>
                </div>
                 <?php if($this->album->count()>0){ ?>
                <div title="<?php echo $this->translate(array('%s download', '%s downloads', $this->album->download_count), $this->locale()->toNumber($this->album->download_count))?>">
                  <span class="sesalbum_cover_stat_count"><?php echo $this->album->download_count; ?></span>
                  <span class="sesalbum_cover_stat_txt"><?php echo str_replace(',','',preg_replace('/[0-9]+/', '', $this->translate(array('%s Download', '%s Downloads', $this->album->download_count), $this->locale()->toNumber($this->album->download_count)))); ?></span>
                </div>
                <?php } ?>
              </div>
            </div>
          </div>          
        </div>
      </div>
      <div class="sesalbum_cover_footer clear sesbasic_clearfix">
      	<div class="sesalbum_cover_footer_inner sesbasic_clearfix">
          <ul id="tab_links_cover" class="sesalbum_cover_tabs sesbasic_clearfix">
            <li data-src="album-info" class="tab_cover <?php echo ($this->relatedAlbumsPaginator->getTotalItemCount() == 0 && $this->paginator->getTotalItemCount() == 0) ? 'sesalbum_cover_tabactive' : "" ; ?>"><a href="javascript:;" ><?php echo $this->translate("Album Info") ; ?></a></li>
            <li class="<?php echo $this->paginator->getTotalItemCount() == 0  ? '' : "sesalbum_cover_tabactive" ; ?> tab_cover" data-src="album-photo" style="display:<?php echo $this->paginator->getTotalItemCount() == 0  ? 'none' : "" ; ?>"><a href="javascript:;"><?php echo $this->translate("Photos") ; ?></a></li>
            <li class="tab_cover <?php echo ($this->relatedAlbumsPaginator->getTotalItemCount() != 0 && $this->paginator->getTotalItemCount() == 0) ? 'sesalbum_cover_tabactive' : "" ;  ?>" data-src="album-related" style="display:<?php if($this->relatedAlbumsPaginator->getTotalItemCount() == 0 && !$this->canEdit){ echo "none"; } ?> "><a href="javascript:;"><?php echo $this->translate("Related Albums") ; ?></a></li>
            <li class="tab_cover" data-src="album-discussion" ><a href="javascript:;"><?php echo $this->translate("Discussion") ; ?></a></li>
          </ul>
          <?php
             $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $this->album->getHref()); ?>
          <div class="sesalbum_cover_user_options sesbasic_clearfix">
            <ul>
              <li><a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $this->album->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_facebook_button"><i class="fa fa-facebook"></i></a></li>
              <li><a href="<?php echo 'https://twitter.com/intent/tweet?url=' . $urlencode . '&text=' . $this->album->getTitle().'%0a'; ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_twitter_button"><i class="fa fa-twitter"></i></a></li>
              <li><a  href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($this->album->getPhotoUrl('thumb.main','','notcheck'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $this->album->getPhotoUrl('thumb.main','','notcheck')) . $this->album->getPhotoUrl('thumb.main','','notcheck')); ?>&description=<?php echo $this->album->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_pintrest_button"><i class="fa fa-pinterest"></i></a></li>
             <?php if($this->viewer->getIdentity() != 0){ ?>
              <li><a title="<?php echo $this->translate('Share'); ?>" href="<?php echo $this->url(array("action" => "share", "type" => "album", "photo_id" => $this->album->getIdentity(),"format" => "smoothbox"), 'sesalbum_general', true); ?>" class="sesalbum_view_share_button smoothbox"><i class="fa fa-share"></i></a></li>
              <li><a title="<?php echo $this->translate('Message'); ?>" href="<?php echo $this->url(array('module'=> 'sesalbum', 'controller' => 'index', 'action' => 'message', 'album_id' => $this->album->getIdentity(), 'format' => 'smoothbox'),'sesalbum_extended',true); ?>" class="sesalbum_view_share_button smoothbox"><i class="fa fa-envelope"></i></a></li>
              <?php if($this->canDownload && $this->album->count()>0){ ?>
                <li><a title="<?php echo $this->translate('Download'); ?>" href="<?php echo $this->url(array('module' => 'sesalbum', 'action' => 'download', 'album_id' => $this->album->album_id), 'sesalbum_general', true); ?>" class="sesalbum_photo_view_download_button"><i class="fa fa-download"></i></a></li>
              <?php } ?>
              <?php if($this->canComment){ ?>
              	<li>
                  <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatus($this->album->album_id); ?>
                  <a  href="javascript:;" data-src='<?php echo $this->album->album_id; ?>' id="sesLikeUnlikeButton" class="sesalbum_view_like_button <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                    <i class="fa fa-thumbs-up"></i>
                  </a>
                </li>
             <?php } ?>
              <li>
                <?php $albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album','resource_id'=>$this->album->album_id)); ?>
                <a href="javascript:;" data-src='<?php echo $this->album->album_id; ?>' class="sesalbum_view_fav_button sesalbum_albumFav <?php echo ($albumFavStatus)>0 ? 'button_active' : '' ; ?>">
                  <i class="fa fa-heart"></i>
                </a>
              </li>        
              <?php } ?>
              <?php if( $this->mine || $this->canEdit || $editItem || $deleteItem || $createItem): ?>
                <li class="sesalbum_cover_user_options_drop_btn" id="sesalbum_dropdown_btn">
                  <a title="<?php echo $this->translate('Options'); ?>" href="javascript:;" id="a_btn">
                    <i class="fa fa-ellipsis-v" id="i_btn"></i>
                  </a>
                  <div class="sesalbum_option_box sesalbum_option_box1">
                    <?php if($createItem){ ?>
                      <a href="<?php echo $this->url(array('module' => 'sesalbum', 'action' => 'create', 'album_id' => $this->album_id), 'sesalbum_general', true); ?>"><i class="fa fa-plus"></i><?php echo $this->translate('Add More Photos'); ?></a>
                    <?php } ?>
                    <?php if($editItem){ ?>
                      <a href="<?php echo $this->url(array('module' => 'sesalbum', 'action' => 'editphotos', 'album_id' => $this->album_id), 'sesalbum_specific', true); ?>"><i class="fa fa-picture-o"></i><?php echo $this->translate('Manage Photos'); ?></a>
                      <a href="<?php echo $this->url(array('module' => 'sesalbum', 'action' => 'edit', 'album_id' => $this->album_id), 'sesalbum_specific', true); ?>"><i class="fa fa-pencil"></i><?php echo $this->translate('Edit Settings'); ?></a>
                    <?php } ?>
                    <?php if($deleteItem){ ?>
                      <a class="smoothbox" href="<?php echo $this->url(array('module' => 'sesalbum', 'action' => 'delete', 'album_id' => $this->album_id, 'format' => 'smoothbox'), 'sesalbum_specific', true); ?>"><i class="fa fa-trash"></i><?php echo $this->translate('Delete Album'); ?></a>
                    <?php } ?>
                     <?php echo $this->htmlLink(Array("module"=> "core", "controller" => "report", "action" => "create", "route" => "default", "subject" => $this->album->getGuid()),'<i class="fa fa-flag"></i>'.$this->translate("Report"), array("class" => "smoothboxOpen")); ?>
                     
                     <?php if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 && (Engine_Api::_()->user()->getViewer()->level_id == 1 || Engine_Api::_()->user()->getViewer()->level_id == 2)): ?>
                     <?php echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "index", "action" => "featured", "route" => "sesalbum_extended", "album_id" => $this->album->getIdentity(),"type" =>"album"),  $this->translate((($this->album->is_featured == 1) ? "Unmark as Featured" : "Mark Featured")), array("class" => "sesalbum_admin_featured fa fa-picture-o")) ?>                  
                     <?php echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "index", "action" => "sponsored", "route" => "sesalbum_extended", "album_id" => $this->album->getIdentity(),"type" =>"album"),  $this->translate((($this->album->is_sponsored == 1) ? "Unmark as Sponsored" : "Mark Sponsored")), array("class" => "sesalbum_admin_sponsored fa fa-picture-o")) ?>
                     <?php if(strtotime($this->album->endtime) < strtotime(date("Y-m-d")) && $this->album->offtheday == 1){$itemofftheday=0;}else{$itemofftheday = $this->album->offtheday;}echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "index", "action" => "offtheday", "route" => "sesalbum_extended","id" => $this->album_id, "type" => "album", "param" => (($itemofftheday == 1) ? 0 : 1)),  $this->translate((($itemofftheday == 1) ? "Edit of the Day" : "Make of the Day")), array("class" => "smoothboxOpen fa fa-picture-o")); ?>
                     <?php endif; ?>                 
                  </div>
                </li>
              <?php endif;?>
          	</ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<div class="clear sesbasic_clearfix sesbasic_bxs sesalbum_album_view_cont" id="scrollHeightDivSes_<?php echo $randonNumber; ?>">
  <ul id="ses-image-view" class="album-photo sesalbum_listings sesalbum_album_photos_listings sesalbum_photos_flex_view sesbasic_clearfix" style="<?php echo $this->paginator->getTotalItemCount() == 0  ? 'none' : "" ; ?>">
<?php } ?>
	<?php if(!$this->is_related){ ?>
    <?php 
    			$limit = 0;
          $allowRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.photo.rating',1);
					$allowShowPreviousRating = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratephoto.show',1);
          if($allowRating == 0){
          	if($allowShowPreviousRating == 0)
            	$ratingShow = false;
             else
             	$ratingShow = true;
          }else
          	$ratingShow = true; 
          foreach( $this->paginator as $photo ){
           if($this->view_type != 'masonry'){ ?>
            <li id="thumbs-photo-<?php echo $photo->photo_id ?>" class="ses_album_image_viewer sesalbum_list_photo_grid sesalbum_list_grid  sesa-i-<?php echo (isset($this->insideOutside) && $this->insideOutside == 'outside') ? 'outside' : 'inside'; ?> sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
              <?php $imageURL = Engine_Api::_()->sesalbum()->getImageViewerHref($photo); ?>
              <a class="sesalbum_list_grid_img ses-image-viewer" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $photo->getPhotoUrl('','',''); ?>','<?php echo $imageURL	; ?>')" href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($photo->getIdentity(),$photo->album_id); ?>" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;"> 
                <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normalmain','','notcheck'); ?>);"></span>
              </a>
              <?php 
              if(isset($this->socialSharing) || isset($this->likeButton) || isset($this->favouriteButton)){
              //album viewpage link for sharing
                $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $photo->getHref()); ?>
      					<span class="sesalbum_list_grid_btns">
                  <?php if(isset($this->socialSharing)){ ?>
                    <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $photo->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_icon_btn sesalbum_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
                    <a href="<?php echo 'https://twitter.com/intent/tweet?url=' . $urlencode . '&text=' . $photo->getTitle().'%0a'; ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_icon_btn sesalbum_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
                    <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($photo->getPhotoUrl('thumb.main','','notcheck'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $photo->getPhotoUrl('thumb.main','','notcheck')) . $photo->getPhotoUrl('thumb.main','','notcheck')); ?>&description=<?php echo $photo->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
        					<?php } 
                  $canComment =  $photo->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
                  	if(isset($this->likeButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && $canComment){  ?>
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
                    <?php if(isset($this->rating) && $ratingShow) { ?>
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
          $imageURL = $photo->getPhotoUrl('thumb.normalmain','','notcheck');
          if(strpos($imageURL,'http://') === FALSE && strpos($imageURL,'https://') === FALSE)
    					$imageGetSizeURL = $_SERVER['DOCUMENT_ROOT']. DIRECTORY_SEPARATOR . substr($imageURL, 0, strpos($imageURL, "?"));
          else
          	$imageGetSizeURL = $imageURL;
    			$imageHeightWidthData = getimagesize($imageGetSizeURL);
          $width = isset($imageHeightWidthData[0]) ? $imageHeightWidthData[0] : '300';
          $height = isset($imageHeightWidthData[1]) ? $imageHeightWidthData[1] : '200'; ?>
         		<li id="thumbs-photo-<?php echo $photo->photo_id ?>" data-w="<?php echo $width ?>" data-h="<?php echo $height; ?>" class="ses_album_image_viewer sesalbum_list_flex_thumb sesalbum_list_photo_grid sesalbum_list_grid sesa-i-inside sesa-i-<?php echo (isset($this->fixHover) && $this->fixHover == 'fix') ? 'fix' : 'over'; ?>">
              <?php $imageViewerURL = Engine_Api::_()->sesalbum()->getImageViewerHref($photo); ?>
              <a class="sesalbum_list_flex_img ses-image-viewer" onclick="getRequestedAlbumPhotoForImageViewer('<?php echo $photo->getPhotoUrl('','',''); ?>','<?php echo $imageViewerURL	; ?>')" href="<?php echo Engine_Api::_()->sesalbum()->getHrefPhoto($photo->getIdentity(),$photo->album_id); ?>"> 
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
                    <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($photo->getPhotoUrl('thumb.main','','notcheck'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $photo->getPhotoUrl('thumb.main','','notcheck')) . $photo->getPhotoUrl('thumb.main','','notcheck')); ?>&description=<?php echo $photo->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
        					<?php }
                   $canComment =  $photo->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
                  	 if(isset($this->likeButton) && Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && $canComment){  ?>	
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
              
              <?php if(isset($this->like) || isset($this->comment) || isset($this->view) || isset($this->title) || isset($this->rating)  || isset($this->by)){ ?>
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
                   	<?php if(isset($this->rating) && $ratingShow) { ?>
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
         <?php } 
         		 $limit++;
           }
         		 if($this->loadOptionData == 'pagging'){ ?>
             <?php echo $this->paginationControl($this->paginator, null, array("_pagging.tpl", "sesalbum"),array('identityWidget'=>$randonNumber)); ?>
       		  <?php }
         }
          ?>
<?php if(!$this->is_ajax && !$this->is_related) { ?>
  </ul>
  <!--Album Info Tab-->
	<div class="clear sesbasic_clearfix sesalbum_album_info">
  	<div class="sesalbum_album_info_right" id="sesalbum-container-right" style="display:none">
    	<?php foreach($this->defaultOptions as $key=>$defaultOptions){ ?>
      	<?php if($key == 'Like' && $this->paginatorLike->getTotalItemCount() > 0){ ?>
        	<!-- PEOPLE LIKE ALBUM-->
          <div>
            <h3><?php echo $this->translate($defaultOptions); ?></h3>
            <ul class="sesalbum_user_listing sesbasic_clearfix clear">
              <?php foreach( $this->paginatorLike as $item ): ?>
                <li>
                  <?php $user = Engine_Api::_()->getItem('user',$item->poster_id) ?>
                  <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'),array('title'=>$user->getTitle())); ?>
                </li>
              <?php endforeach; ?>
              <?php if($this->paginatorLike->getTotalItemCount() > $this->data_showLike){ ?>
              	<li>
                  <a href="javascript:;" onclick="getLikeData('<?php echo $this->album_id; ?>','<?php echo urlencode($this->translate($defaultOptions)); ?>')" class="sesalbum_user_listing_more">
                   <?php echo '+';echo $this->paginatorLike->getTotalItemCount() - $this->data_showLike ; ?>
                  </a>
              	</li>
            	<?php } ?>
          	</ul>
        	</div>
       	<?php } ?>
        <?php if($key == 'Fav' && $this->paginatorFav->getTotalItemCount() > 0){ ?>
          <!-- PEOPLE FAVOURITE ALBUM-->
          <div>
          	<h3><?php echo $this->translate($defaultOptions); ?></h3>
          	<ul class="sesalbum_user_listing sesbasic_clearfix clear">
            	<?php foreach( $this->paginatorFav as $item ): ?>
              	<li>
                	<?php $user = Engine_Api::_()->getItem('user',$item->user_id) ?>
                	<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'),array('title'=>$user->getTitle())); ?>
              	</li>
            	<?php endforeach; ?>
              <?php if($this->paginatorFav->getTotalItemCount() > $this->data_showFav){ ?>
            		<li>
                  <a href="javascript:;" onclick="getFavouriteData('<?php echo $this->album_id; ?>','<?php echo urlencode($this->translate($defaultOptions)); ?>')" class="sesalbum_user_listing_more">
                   <?php echo '+';echo $this->paginatorFav->getTotalItemCount() - $this->data_showFav ; ?>
                  </a>
            		</li>
           		<?php } ?>
          	</ul>
        	</div>
       	<?php } ?>
       	<?php if($key == 'TaggedUser' && $this->paginatorTaggedUser->getTotalItemCount() > 0){ ?>
          <!-- PEOPLE TAGGED IN ALBUM-->
          <div>
            <h3><?php echo $this->translate($defaultOptions); ?></h3>
            <ul class="sesalbum_user_listing sesbasic_clearfix clear">
              <?php foreach( $this->paginatorTaggedUser as $item ): ?>
                <li>
                  <?php $user = Engine_Api::_()->getItem('user',$item->tag_id) ?>
                  <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'),array('title'=>$user->getTitle())); ?>
                </li>
                <?php endforeach; ?>
                <?php if($this->paginatorTaggedUser->getTotalItemCount() > $this->data_showTagged){ ?>
                  <li>
                    <a href="javascript:;" onclick="getTaggedData('<?php echo $this->album_id; ?>','<?php echo urlencode($this->translate($defaultOptions)); ?>')" class="sesalbum_user_listing_more">
                    <?php echo '+';echo $this->paginatorTaggedUser->getTotalItemCount() - $this->data_showTagged ; ?>
                    </a>
                </li>
              <?php } ?>
            </ul>
          </div>
        <?php } ?>
        <?php if($key == 'RecentAlbum' && $this->paginatorRecentAlbum->getTotalItemCount() > 0){ ?>
          <!-- RECENT  ALBUM OF USER-->
          <div>
            <?php $userName = Engine_Api::_()->getItem('user',$this->album->owner_id) ?>
            <h3><?php echo (str_replace('[USER_NAME]',$userName->getTitle(),$this->translate($defaultOptions))); ?></h3>
            <ul class="sesalbum_user_listing sesbasic_clearfix clear">
              <?php foreach( $this->paginatorRecentAlbum as $item ): ?>
                <li>
                	<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'),array('title'=>$item->getTitle())); ?>
                </li>
                <?php endforeach; ?>
                <?php if($this->paginatorRecentAlbum->getTotalItemCount() > $this->data_showRecentAlbum){ ?>
                <li>
                  <a href="<?php echo $this->url(array('action' => 'browse'), "sesalbum_general").'?user_id='.$this->album->owner_id; ?>"  class="sesalbum_user_listing_more">
                  	<?php echo '+';echo $this->paginatorRecentAlbum->getTotalItemCount() - $this->data_showRecentAlbum ; ?>
                  </a>
                </li>
              <?php } ?>
            </ul>
          </div>
        <?php } ?>
      <?php } ?>
    </div>
    <div class="sesalbum_album_info_left album-info" style="display:<?php echo ($this->relatedAlbumsPaginator->getTotalItemCount() == 0 && $this->paginator->getTotalItemCount() == 0) ? 'block' : "none" ; ?>">
      <?php if( '' != trim($this->album->getDescription()) ): ?>
        <div class="sesalbum_album_info_desc clear"><?php echo nl2br($this->album->getDescription()); ?></div>  
      <?php endif; ?>
      <div class="sesalbum_album_other_info clear sesbasic_clearfix">
      <?php if($this->album->category_id){ ?>
      	<?php $category = Engine_Api::_()->getItem('sesalbum_category',$this->album->category_id); ?>
       <?php if($category){ ?>
      	<div class="sesalbum_album_other_info_field clear sesbasic_clearfix">
        	<span><?php echo $this->translate("Category"); ?></span>
          <span><a href="<?php echo $category->getHref(); ?>"><?php echo $category->category_name; ?></a>
          	<?php $subcategory = Engine_Api::_()->getItem('sesalbum_category',$this->album->subcat_id); ?>
             <?php if($subcategory){ ?>
                &nbsp;&raquo;&nbsp;<a href="<?php echo $subcategory->getHref(); ?>"><?php echo $subcategory->category_name; ?></a>
            <?php } ?>
            <?php $subsubcategory = Engine_Api::_()->getItem('sesalbum_category',$this->album->subsubcat_id); ?>
             <?php if($subsubcategory){ ?>
                &nbsp;&raquo;&nbsp;<a href="<?php echo $subsubcategory->getHref(); ?>"><?php echo $subsubcategory->category_name; ?></a>
            <?php } ?>
          </span>
        </div>
      <?php }          
      	} ?>
        <?php if(count($this->albumTags)>0){ ?>
        <div class="sesalbum_album_other_info_field clear sesbasic_clearfix">
        	<span><?php echo $this->translate("Tags"); ?></span>
            <span>
            <?php $counter = 0;
              foreach($this->albumTags as $tag):
                if($tag->getTag()->text != ''){?>
           		  <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>,"<?php echo $tag->getTag()->text; ?>");'><?php echo $tag->getTag()->text ?></a><?php if((count($this->albumTags) - 1) != $counter ) { echo ",&nbsp;"; } ?>
        <?php	 } 
              $counter++;endforeach;  ?>
            </span>
        </div>
        <?php } ?>
        
       <div class="sesalbum_view_custom_fields">
        <?php
          //custom field data
          echo $this->sesbasicFieldValueLoop($this->album);
        ?>
        </div>
        <?php 
        //Location
        if(!is_null($this->album->location) && $this->album->location != '' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum_enable_location', 1)){ ?>
        	<div class="sesalbum_album_other_info_field clear sesbasic_clearfix">
          	<span><?php echo $this->translate("Location") ?></span>
            <span><?php echo $this->htmlLink(Array("module"=> "sesalbum", "controller" => "album", "action" => "location", "route" => "sesalbum_extended", "type" => "location","album_id" =>$this->album->album_id), $this->album->location, array("class" => "smoothboxOpen")); ?></span>
          </div>
        <?php } ?>
    	</div>
		</div>
   	<div class="sesalbum_album_info_left album-discussion layout_core_comments" style="display:none">
  		<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesadvancedcomment')){ ?>
      <?php echo $this->action("list", "comment", "sesadvancedcomment", array("type" => "album", "id" => $this->album->getIdentity(),'is_ajax_load'=>true)); 
        }else{
         echo $this->action("list", "comment", "core", array("type" => "album", "id" => $this->album->getIdentity()));
        }
          ?>
  	</div>
	 </div>
  <?php } ?>
  <?php if(!$this->is_ajax && !$this->is_related){ ?>
   <div class="clear sesbasic_clearfix album-related" style="display:<?php echo ($this->relatedAlbumsPaginator->getTotalItemCount() != 0 && $this->paginator->getTotalItemCount() == 0) ? 'block' : "none" ;  ?>">
   	<?php if($this->canEdit){ ?>
    <div class="sesalbum_album_view_option clear sesbasic_clearfix">
      <a href="javascript:;" onclick="getRelatedAlbumsData();return false;" class="sesalbum_button">
      	<i class="fa fa-plus sesbasic_text_light"></i>
        <?php echo $this->translate("Add Related Albums"); ?></a>
     </div>
     <?php } ?>
     <ul id="sesalbum_related_<?php echo $randonNumber; ?>">
   <?php } ?>
     <?php  $allowRatingAlbum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.album.rating',1);
					$allowShowPreviousRatingAlbum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.ratealbum.show',1);
          if($allowRatingAlbum == 0){
          	if($allowShowPreviousRatingAlbum == 0)
            	$ratingShowAlbum = false;
             else
             	$ratingShowAlbum = true;
          }else
          	$ratingShowAlbum = true; ?>
        <?php if(isset($this->relatedAlbumsPaginator)){ ?>
     <?php foreach($this->relatedAlbumsPaginator as $albumRelated){
     		$albumRelated = Engine_Api::_()->getItem('album',$albumRelated->album_id)
     ?> 
            <li id="thumbs-photo-<?php echo $albumRelated->photo_id ?>" class="sesalbum_list_grid_thumb sesalbum_list_grid sesa-i-<?php echo (isset($this->insideOutsideRelated) && $this->insideOutsideRelated == 'outside') ? 'outside' : 'inside'; ?> sesa-i-<?php echo (isset($this->fixHoverRelated) && $this->fixHoverRelated == 'fix') ? 'fix' : 'over'; ?> sesbm" style="width:<?php echo is_numeric($this->widthRelated) ? $this->widthRelated.'px' : $this->widthRelated ?>;">  
              <a class="sesalbum_list_grid_img" href="<?php echo Engine_Api::_()->sesalbum()->getHref($albumRelated->getIdentity(),$albumRelated->album_id); ?>" style="height:<?php echo is_numeric($this->heightRelated) ? $this->heightRelated.'px' : $this->heightRelated ?>;">
                <span class="main_image_container" style="background-image: url(<?php echo $albumRelated->getPhotoUrl('thumb.normalmain','','notcheck'); ?>);"></span>
              <div class="ses_image_container" style="display:none;">
                <?php $image = Engine_Api::_()->sesalbum()->getAlbumPhoto($albumRelated->getIdentity(),$albumRelated->photo_id); 
                      foreach($image as $key=>$valuePhoto){ ?>
                       <div class="child_image_container"><?php echo Engine_Api::_()->sesalbum()->photoUrlGet($valuePhoto->photo_id,'thumb.normalmain');  ?></div>
                 <?php  }  ?>  
                 <div class="child_image_container"><?php echo $albumRelated->getPhotoUrl('thumb.normalmain','','notcheck'); ?></div>          
                </div>
              </a>
              <?php  if(isset($this->socialSharingRelated) ||  isset($this->favouriteButtonRelated) || isset($this->likeButtonRelated)){  ?>
      <span class="sesalbum_list_grid_btns">
       <?php if(isset($this->socialSharingRelated)){ 
       	//album viewpage link for sharing
          $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $albumRelated->getHref());
       ?>
        <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $albumRelated->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesalbum_icon_btn sesalbum_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
        <a href="<?php echo 'https://twitter.com/intent/tweet?url=' . $urlencode . '&text=' . $albumRelated->getTitle().'%0a'; ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesalbum_icon_btn sesalbum_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
        <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($albumRelated->getPhotoUrl('thumb.main','','notcheck'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : $albumRelated->getPhotoUrl('thumb.main','','notcheck')) . $albumRelated->getPhotoUrl('thumb.main','','notcheck')); ?>&description=<?php echo $albumRelated->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesalbum_icon_btn sesalbum_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
        <?php }
        $canComment =  $albumRelated->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
        	if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->likeButtonRelated) && $canComment){ ?>
                <!--Album Like Button-->
                <?php $albumLikeStatus = Engine_Api::_()->sesalbum()->getLikeStatus($albumRelated->album_id); ?>
                <a href="javascript:;" data-src='<?php echo $albumRelated->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_like_btn sesalbum_albumlike <?php echo ($albumLikeStatus) ? 'button_active' : '' ; ?>">
                  <i class="fa fa-thumbs-up"></i>
                  <span><?php echo $albumRelated->like_count; ?></span>
                </a>
              <?php } 
              	$canFavourite =  Engine_Api::_()->authorization()->isAllowed('album',Engine_Api::_()->user()->getViewer(), 'favourite_photo');
              	if(Engine_Api::_()->user()->getViewer()->getIdentity() !=0 && isset($this->favouriteButtonRelated) && $canFavourite){
             	 		$albumFavStatus = Engine_Api::_()->getDbtable('favourites', 'sesalbum')->isFavourite(array('resource_type'=>'album','resource_id'=>$albumRelated->album_id)); ?>
              <a href="javascript:;" data-src='<?php echo $albumRelated->album_id; ?>' class="sesalbum_icon_btn sesalbum_icon_fav_btn sesalbum_albumFav <?php echo ($albumFavStatus)>0 ? 'button_active' : '' ; ?>">
                <i class="fa fa-heart"></i>
                <span><?php echo $albumRelated->favourite_count; ?></span>
              </a>
         <?php } ?>
         </span>
         <?php } ?>
          <?php if(isset($this->featuredRelated) || isset($this->sponsoredRelated)){ ?>
          	<span class="sesalbum_labels_container">
              <?php if(isset($this->featuredRelated) && $albumRelated->is_featured == 1){ ?>
                <span class="sesalbum_label_featured"><?php echo $this->translate("Featured"); ?></span>
              <?php } ?>
            <?php if(isset($this->sponsoredRelated)  && $albumRelated->is_sponsored == 1){ ?>
            	<span class="sesalbum_label_sponsored"><?php echo $this->translate("Sonsored"); ?></span>
            <?php } ?>
          </span>
         <?php } ?>
         <?php if(isset($this->likeRelated) || isset($this->commentRelated) || isset($this->viewRelated) || isset($this->titleRelated) || isset($this->ratingRelated) || isset($this->photoCountRelated) || isset($this->favouriteCountRelated) || isset($this->downloadCountRelated)){ ?>
              <p class="sesalbum_list_grid_info sesbasic_clearfix<?php if(!isset($this->photoCountRelated)) { ?> nophotoscount<?php } ?>">
              <?php if(isset($this->titleRelated)) { ?>
                <span class="sesalbum_list_grid_title">
                  <?php echo $this->htmlLink($albumRelated, $this->string()->truncate($albumRelated->getTitle(), $this->title_truncationRelated),array('title'=>$albumRelated->getTitle())) ; ?>
                </span>
              <?php } ?>
              <span class="sesalbum_list_grid_stats">
                <?php if(isset($this->byRelated)) { ?>
                  <span class="sesalbum_list_grid_owner">
                    <?php echo $this->translate('By');?>
                   <?php echo $this->htmlLink($albumRelated->getOwner()->getHref(), $albumRelated->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                  </span>
                <?php }?>
                <?php if(isset($this->ratingRelated) && $ratingShowAlbum) { ?>
                <?php
                          $user_rate = Engine_Api::_()->getDbtable('ratings', 'sesalbum')->getCountUserRate('album',$albumRelated->album_id);
                          $textuserRating = $user_rate == 1 ? 'user' : 'users'; 
                          $textRatingText = $albumRelated->rating == 1 ? 'rating' : 'ratings'; ?>
                       <span class="sesalbum_list_grid_rating" title="<?php echo $albumRelated->rating.' '.$this->translate($textRatingText.' by').' '.$user_rate.' '.$this->translate($textuserRating); ?>">
                    <?php if( $albumRelated->rating > 0 ): ?>
                      <?php for( $x=1; $x<= $albumRelated->rating; $x++ ): ?>
                      	<span class="sesbasic_rating_star_small fa fa-star"></span>
                      <?php endfor; ?>
                      <?php if( (round($albumRelated->rating) - $albumRelated->rating) > 0): ?>
                      	<span class="sesbasic_rating_star_small fa fa-star-half"></span>
                      <?php endif; ?>
                    <?php endif; ?> 
                  </span>
                <?php } ?>
              </span>
              <span class="sesalbum_list_grid_stats sesbasic_text_light">
                <?php if(isset($this->likeRelated)) { ?>
                  <span class="sesalbum_list_grid_likes" title="<?php echo $this->translate(array('%s like', '%s likes', $albumRelated->like_count), $this->locale()->toNumber($albumRelated->like_count))?>">
                    <i class="fa fa-thumbs-up"></i>
                    <?php echo $albumRelated->like_count;?>
                  </span>
                <?php } ?>
                <?php if(isset($this->commentRelated)) { ?>
                  <span class="sesalbum_list_grid_comment" title="<?php echo $this->translate(array('%s comment', '%s comments', $albumRelated->comment_count), $this->locale()->toNumber($albumRelated->comment_count))?>">
                    <i class="fa fa-comment"></i>
                    <?php echo $albumRelated->comment_count;?>
                  </span>
               <?php } ?>
               <?php if(isset($this->viewRelated)) { ?>
                  <span class="sesalbum_list_grid_views" title="<?php echo $this->translate(array('%s view', '%s views', $albumRelated->view_count), $this->locale()->toNumber($albumRelated->view_count))?>">
                    <i class="fa fa-eye"></i>
                    <?php echo $albumRelated->view_count;?>
                  </span>
               <?php } ?>
               <?php if(isset($this->favouriteCountRelated)) { ?>
                  <span class="sesalbum_list_grid_fav" title="<?php echo $this->translate(array('%s favourite', '%s favourites', $albumRelated->favourite_count), $this->locale()->toNumber($albumRelated->favourite_count))?>">
                    <i class="fa fa-heart"></i> 
                    <?php echo $albumRelated->favourite_count;?>            
                  </span>
                <?php } ?>
                <?php if(isset($this->downloadCountRelated)) { ?>
                <span class="sesalbum_list_grid_download" title="<?php echo $this->translate(array('%s download', '%s downloads', $albumRelated->download_count), $this->locale()->toNumber($albumRelated->download_count))?>">
                  <i class="fa fa-download"></i> 
                  <?php echo $albumRelated->download_count;?>            
                </span>
              <?php } ?>
                 <?php if(isset($this->photoCountRelated)) { ?>
               	<span class="sesalbum_list_grid_count" title="<?php echo $this->translate(array('%s photo', '%s photos', $albumRelated->count()), $this->locale()->toNumber($albumRelated->count()))?>" >
                  <i class="fa fa-photo"></i> 
                  <?php echo $albumRelated->count();?>                
               	</span>
                <?php } ?>

                  </span>
              </p>
         <?php } ?>
          <?php if(isset($this->photoCountRelated)) { ?>
              <p class="sesalbum_list_grid_count">
                <?php echo $this->translate(array('%s <span>photo</span>', '%s <span>photos</span>', $albumRelated->count()),$this->locale()->toNumber($albumRelated->count())) ?>
              </p>
              <?php  } ?>
            </li>
          <?php  } 
          }
          if($this->loadOptionDataRelated == 'pagging'){ ?>
             <?php echo $this->paginationControl($this->relatedAlbumsPaginator, null, array("_pagging.tpl", "sesalbum"),array('identityWidget'=>$randonNumber.'PaggingRelated')); ?>
       		  <?php }   ?>
     <?php if(!$this->is_ajax && !$this->is_related){ ?>
      </ul>
			<?php } ?>
     <?php  if( isset($this->relatedAlbumsPaginator) && $this->relatedAlbumsPaginator->getTotalItemCount() == 0){  ?>
            <div class="tip">
              <span>
                <?php echo $this->translate("There are currently no related albums.");?>
                 <?php if( $this->canEdit ): ?>
                  <?php echo $this->translate('Click to %1$screate%2$s one!','<a class="smoothbox" href="'.$this->url(array('action' => 'related-album','album_id'=>$this->album->album_id),'sesalbum_specific',true).'">', '</a>'); ?>
                  <?php endif; ?>
              </span>
            </div>    
    			<?php } ?>
  <?php if(!$this->is_related){ ?>
   </div>
   <?php } ?>  
  <?php if(!$this->is_ajax && !$this->is_related){ ?>
   <?php if($this->loadOptionDataRelated != 'pagging'){ ?>
    <div class="sesbasic_view_more sesbasic_load_btn" id="view_more_related_<?php echo $randonNumber; ?>" onclick="viewMoreRelated_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_related_$randonNumber", 'class' => 'sesbasic_animation sesbasic_link_btn fa fa-repeat')); ?> </div>
    <div class="sesbasic_view_more_loading" id="loading_image_related_<?php echo $randonNumber; ?>" style="display: none;"> <span class="sesbasic_link_btn"><i class="fa fa-spinner fa-spin"></i></span></div>
  <?php } ?>
   <?php if($this->loadOptionData != 'pagging'){ ?>
    <div class="sesbasic_view_more sesbasic_load_btn" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'sesbasic_animation sesbasic_link_btn fa fa-repeat')); ?> </div>
    <div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> <span class="sesbasic_link_btn"><i class="fa fa-spinner fa-spin"></i></span></div>
  <?php } ?>
</div>
<?php if($this->is_fullwidth){ ?>
<script type="application/javascript">
sesJqueryObject(document).ready(function(){
	var htmlElement = document.getElementsByTagName("body")[0];
  	htmlElement.addClass('sesalbum_album_cover_full');
		sesJqueryObject('#global_content').css('padding-top',0);
		sesJqueryObject('#global_wrapper').css('padding-top',0);	
});
</script>
<?php } ?>
<script type="text/javascript">
<?php if(!$this->is_ajax && $this->canEdit){ ?>
sesJqueryObject('<div class="sesalbum_photo_update_popup sesbasic_bxs" id="sesalbum_popup_cam_upload" style="display:none"><div class="sesalbum_photo_update_popup_overlay"></div><div class="sesalbum_photo_update_popup_container sesalbum_photo_update_webcam_container"><div class="sesalbum_photo_update_popup_header"><?php echo $this->translate("Click to Take Cover Photo") ?><a class="fa fa-close" href="javascript:;" onclick="hideProfilePhotoUpload()" title="<?php echo $this->translate("Close") ?>"></a></div><div class="sesalbum_photo_update_popup_webcam_options"><div id="sesalbum_camera" style="background-color:#ccc;"></div><div class="centerT sesalbum_photo_update_popup_btns">   <button onclick="take_snapshot()" style="margin-right:3px;" ><?php echo $this->translate("Take Cover Photo") ?></button><button onclick="hideProfilePhotoUpload()" ><?php echo $this->translate("Cancel") ?></button></div></div></div></div><div class="sesalbum_photo_update_popup sesbasic_bxs" id="sesalbum_popup_existing_upload" style="display:none"><div class="sesalbum_photo_update_popup_overlay"></div><div class="sesalbum_photo_update_popup_container" id="sesalbum_popup_container_existing"><div class="sesalbum_photo_update_popup_header"><?php echo $this->translate("Select a cover photo") ?><a class="fa fa-close" href="javascript:;" onclick="hideProfilePhotoUpload()" title="<?php echo $this->translate("Close") ?>"></a></div><div class="sesalbum_photo_update_popup_content"><div id="sesalbum_album_existing_data"></div><div id="sesalbum_profile_existing_img" style="display:none;text-align:center;"><img src="application/modules/Sesbasic/externals/images/loading.gif" alt="<?php echo $this->translate("Loading"); ?>" style="margin-top:10px;"  /></div></div></div></div>').appendTo('body');
var canPaginatePageNumber = 1;
function existingPhotosGet(){
	sesJqueryObject('#sesalbum_profile_existing_img').show();
	var URL = en4.core.staticBaseUrl+'albums/index/existing-photos/';
	(new Request.HTML({
      method: 'post',
      'url': URL ,
      'data': {
        format: 'html',
        page: canPaginatePageNumber,
        is_ajax: 1
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				document.getElementById('sesalbum_album_existing_data').innerHTML = document.getElementById('sesalbum_album_existing_data').innerHTML + responseHTML;
      	sesJqueryObject('#sesalbum_album_existing_data').slimscroll({
					 height: 'auto',
					 alwaysVisible :true,
					 color :'#000',
					 railOpacity :'0.5',
					 disableFadeOut :true,					 
					});
					sesJqueryObject('#sesalbum_album_existing_data').slimScroll().bind('slimscroll', function(event, pos){
					 if(canPaginateExistingPhotos == '1' && pos == 'bottom' && sesJqueryObject('#sesalbum_profile_existing_img').css('display') != 'block'){
						 	sesJqueryObject('#sesalbum_profile_existing_img').css('position','absolute').css('width','100%').css('bottom','5px');
							existingPhotosGet();
					 }
					});
					sesJqueryObject('#sesalbum_profile_existing_img').hide();
		}
    })).send();	
}
sesJqueryObject(document).on('click','a[id^="sesalbum_profile_upload_existing_photos_"]',function(event){
	event.preventDefault();
	var id = sesJqueryObject(this).attr('id').match(/\d+/)[0];
	if(!id)
		return;
	sesJqueryObject('.sesalbum_cover_container').append('<div id="sesalbum_cover_loading" class="sesbasic_loading_cont_overlay"></div>');
	hideProfilePhotoUpload();
	var URL = en4.core.staticBaseUrl+'albums/index/upload-existingcover/';
	(new Request.HTML({
      method: 'post',
      'url': URL ,
      'data': {
        format: 'json',
        id: id,
				album_id:'<?php echo $this->album_id; ?>',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				response = sesJqueryObject.parseJSON(responseHTML);
				sesJqueryObject('#sesalbum_cover_loading').remove();
				sesJqueryObject('.sesalbum_cover_image').css('background-image', 'url(' + response.file + ')');
				sesJqueryObject('#sesalbum_cover_default').hide();
				sesJqueryObject('#coverChangeSesalbum').html('<i class="fa fa-plus"></i>'+en4.core.language.translate('Change Cover Photo'));
				sesJqueryObject('#coverRemoveSesalbum').css('display','block');
			}
		 }
    )).send();	
});
sesJqueryObject(document).on('click','a[id^="sesalbum_existing_album_see_more_"]',function(event){
	event.preventDefault();
	var thatObject = this;
	sesJqueryObject(thatObject).parent().hide();
	var id = sesJqueryObject(this).attr('id').match(/\d+/)[0];
	var pageNum = parseInt(sesJqueryObject(this).attr('data-src'),10);
	sesJqueryObject('#sesalbum_existing_album_see_more_loading_'+id).show();
	if(pageNum == 0){
		sesJqueryObject('#sesalbum_existing_album_see_more_page_'+id).remove();
		return;
	}
	var URL = en4.core.staticBaseUrl+'albums/index/existing-albumphotos/';
	(new Request.HTML({
      method: 'post',
      'url': URL ,
      'data': {
        format: 'html',
        page: pageNum+1,
        id: id,
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				document.getElementById('sesalbum_photo_content_'+id).innerHTML = document.getElementById('sesalbum_photo_content_'+id).innerHTML + responseHTML;
				var dataSrc = sesJqueryObject('#sesalbum_existing_album_see_more_page_'+id).html();
      	sesJqueryObject('#sesalbum_existing_album_see_more_'+id).attr('data-src',dataSrc);
				sesJqueryObject('#sesalbum_existing_album_see_more_page_'+id).remove();
				if(dataSrc == 0)
					sesJqueryObject('#sesalbum_existing_album_see_more_'+id).parent().remove();
				else
					sesJqueryObject(thatObject).parent().show();
				sesJqueryObject('#sesalbum_existing_album_see_more_loading_'+id).hide();
		}
    })).send();	
});
sesJqueryObject(document).on('click','#fromExistingAlbum',function(){
	sesJqueryObject('#sesalbum_popup_existing_upload').show();
	existingPhotosGet();
});
sesJqueryObject(document).on('click','#uploadWebCamPhoto',function(){
	sesJqueryObject('#sesalbum_popup_cam_upload').show();
	<!-- Configure a few settings and attach camera -->
	Webcam.set({
		width: 320,
		height: 240,
		image_format:'jpeg',
		jpeg_quality: 90
	});
	Webcam.attach('#sesalbum_camera');
});
<!-- Code to handle taking the snapshot and displaying it locally -->
function take_snapshot() {
	// take snapshot and get image data
	Webcam.snap(function(data_uri) {
		Webcam.reset();
		sesJqueryObject('#sesalbum_popup_cam_upload').hide();
		// upload results
		sesJqueryObject('.sesalbum_cover_container').append('<div id="sesalbum_cover_loading" class="sesbasic_loading_cont_overlay"></div>');
		 Webcam.upload( data_uri, en4.core.staticBaseUrl+'albums/index/upload-cover/album_id/<?php echo $this->album_id ?>' , function(code, text) {
				response = sesJqueryObject.parseJSON(text);
				sesJqueryObject('#sesalbum_cover_loading').remove();
				sesJqueryObject('.sesalbum_cover_image').css('background-image', 'url(' + response.file + ')');
				sesJqueryObject('#sesalbum_cover_default').hide();
				sesJqueryObject('#coverChangeSesalbum').html('<i class="fa fa-plus"></i>'+en4.core.language.translate('Change Cover Photo'));
				sesJqueryObject('#coverRemoveSesalbum').css('display','block');
			} );
	});
}
function hideProfilePhotoUpload(){
	if(typeof Webcam != 'undefined')
	 Webcam.reset();
	canPaginatePageNumber = 1;
	sesJqueryObject('#sesalbum_popup_cam_upload').hide();
	sesJqueryObject('#sesalbum_popup_existing_upload').hide();
	if(typeof Webcam != 'undefined'){
		sesJqueryObject('.slimScrollDiv').remove();
		sesJqueryObject('.sesalbum_photo_update_popup_content').html('<div id="sesalbum_album_existing_data"></div><div id="sesalbum_profile_existing_img" style="display:none;text-align:center;"><img src="application/modules/Sesbasic/externals/images/loading.gif" alt="Loading" style="margin-top:10px;"  /></div>');
	}
}

sesJqueryObject(document).on('click','#coverChangeSesalbum',function(){
	document.getElementById('uploadFileSesalbum').click();	
});
function uploadCoverArt(input){
	 var url = input.value;
    var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
    if (input.files && input.files[0] && (ext == "png" || ext == "jpeg" || ext == "jpg" || ext == 'PNG' || ext == 'JPEG' || ext == 'JPG' || ext == 'gif' || ext == 'GIF')){
				uploadFileToServer(input.files[0]);
    }else{
				//Silence
		}
}
sesJqueryObject('#coverRemoveSesalbum').click(function(){
		sesJqueryObject(this).css('display','none');
		sesJqueryObject('.sesalbum_cover_image').css('background-image', 'url()');
		sesJqueryObject('#sesalbum_cover_default').show();
		var album_id = '<?php echo $this->album->album_id; ?>';
		uploadURL = en4.core.staticBaseUrl+'albums/index/remove-cover/album_id/'+album_id;
		var jqXHR=sesJqueryObject.ajax({
			url: uploadURL,
			type: "POST",
			contentType:false,
			processData: false,
			cache: false,
			success: function(response){
				sesJqueryObject('#coverChangeSesalbum').html('<i class="fa fa-plus"></i>'+en4.core.language.translate('Add Cover Photo'));
				//silence
			 }
			}); 
});
sesJqueryObject('#changePositionOfCoverPhoto').click(function(){
		sesJqueryObject('.sesalbum_cover_fade').css('display','none');
		sesJqueryObject('.sesalbum_cover_inner').css('display','none');
		sesJqueryObject('#sesalbum-pos-btn').css('display','inline-block');
});
sesJqueryObject(document).on('click','#cancelCoverPosition',function(){
	sesJqueryObject('.sesalbum_cover_fade').css('display','block');
	sesJqueryObject('.sesalbum_cover_inner').css('display','block');
	sesJqueryObject('#sesalbum-pos-btn').css('display','none');
});
sesJqueryObject('#saveCoverPosition').click(function(){
	var album_id = '<?php echo $this->album->album_id; ?>';
	var bgPosition = sesJqueryObject('#cover_art_work_image').css('background-position');
	sesJqueryObject('.sesalbum_cover_fade').css('display','block');
	sesJqueryObject('.sesalbum_cover_inner').css('display','block');
	sesJqueryObject('#sesalbum-pos-btn').css('display','none');
	var URL = en4.core.staticBaseUrl+'albums/index/change-position/album_id/'+album_id;
	(new Request.HTML({
		method: 'post',
		'url':URL,
		'data': {
			format: 'html',
			position: bgPosition,    
			album_id:'<?php echo $this->album_id; ?>',
		},
		onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
			//silence
		}
	})).send();
});
function uploadFileToServer(files){
	sesJqueryObject('.sesalbum_cover_container').append('<div id="sesalbum_cover_loading" class="sesbasic_loading_cont_overlay"></div>');
	var formData = new FormData();
	formData.append('Filedata', files);
	var album_id = '<?php echo $this->album->album_id; ?>';
	uploadURL = en4.core.staticBaseUrl+'albums/index/upload-cover/album_id/'+album_id;
	var jqXHR=sesJqueryObject.ajax({
    url: uploadURL,
    type: "POST",
    contentType:false,
    processData: false,
		cache: false,
		data: formData,
		success: function(response){
			response = sesJqueryObject.parseJSON(response);
			sesJqueryObject('#sesalbum_cover_loading').remove();
			sesJqueryObject('.sesalbum_cover_image').css('background-image', 'url(' + response.file + ')');
				sesJqueryObject('#sesalbum_cover_default').hide();
			sesJqueryObject('#coverChangeSesalbum').html('<i class="fa fa-plus"></i>'+en4.core.language.translate('Change Cover Photo'));
			sesJqueryObject('#coverRemoveSesalbum').css('display','block');
     }
    }); 
}
<?php } ?>
var tagAction = window.tagAction = function(tag,value){
	var url = "<?php echo $this->url(array('module' => 'sesalbum','action'=>'browse'), 'sesalbum_general', true) ?>?tag_id="+tag+'&tag_name='+value;
 window.location.href = url;
}
function getRelatedAlbumsData(){
	openURLinSmoothBox("<?php echo $this->url(array('action' => 'related-album','album_id'=>$this->album->album_id), "sesalbum_specific",true); ?>");
	return;
}
function getLikeData(value,title){
	if(value){
		url = en4.core.staticBaseUrl+'albums/index/like-album/album_id/'+value+'/title/'+title;
		openURLinSmoothBox(url);	
		return;
	}
}
function getTaggedData(value,title){
	if(value){
		url = en4.core.staticBaseUrl+'albums/index/tagged-album/album_id/'+value+'/title/'+title;
		openURLinSmoothBox(url);	
		return;
	}
}
function getFavouriteData(value,title){
	if(value){
		url = en4.core.staticBaseUrl+'albums/index/fav-album/album_id/'+value+'/title/'+title;
		openURLinSmoothBox(url);	
		return;
	}
}
<?php if($this->loadOptionData == 'auto_load'){ ?>
		window.addEvent('domready', function() {
		 sesJqueryObject(window).scroll( function() {
			 if(!$('loading_image_<?php echo $randonNumber; ?>'))
			 	return false;
			  var heightOfContentDiv_<?php echo $randonNumber; ?> = sesJqueryObject('#scrollHeightDivSes_<?php echo $randonNumber; ?>').offset().top;
        var fromtop_<?php echo $randonNumber; ?> = sesJqueryObject(this).scrollTop();
        if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDiv_<?php echo $randonNumber; ?> - 100 && sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display') == 'block' ){
						document.getElementById('feed_viewmore_link_<?php echo $randonNumber; ?>').click();
				}
     });
	});
<?php } ?>
<?php if($this->loadOptionDataRelated == 'auto_load'){ ?>
		window.addEvent('domready', function() {
		 sesJqueryObject(window).scroll( function() {
			 if(!$('loading_image_related_<?php echo $randonNumber; ?>'))
			 	return false;
			  var heightOfContentDivRelated_<?php echo $randonNumber; ?> = sesJqueryObject('#sesalbum_related_<?php echo $randonNumber; ?>').offset().top;
        var fromtop_<?php echo $randonNumber; ?> = sesJqueryObject(this).scrollTop();
        if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDivRelated_<?php echo $randonNumber; ?> - 100 && sesJqueryObject('#view_more_related_<?php echo $randonNumber; ?>').css('display') == 'block' ){
						document.getElementById('feed_viewmore_link_related_<?php echo $randonNumber; ?>').click();
				}
     });
	});
<?php } ?>
</script>
<?php } ?>
<script type="text/javascript">
<?php if(!$this->is_ajax && !$this->is_related){ ?>
		sesJqueryObject(document).on('click','#tab_links_cover > li',function(){
			var elemLength = sesJqueryObject('#tab_links_cover').children();	
			for(i=0;i<elemLength.length;i++){
					sesJqueryObject(elemLength[i].removeClass('sesalbum_cover_tabactive'));
					sesJqueryObject('.'+sesJqueryObject(elemLength[i]).attr('data-src')).css('display','none');
			}
				sesJqueryObject(this).addClass('sesalbum_cover_tabactive');
				sesJqueryObject('.'+sesJqueryObject(this).attr('data-src')).css('display','block');
				if("<?php echo $this->view_type ; ?>" == 'masonry'){
					sesJqueryObject("#ses-image-view").flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>});
				}
				if(sesJqueryObject(this).attr('data-src') == 'album-photo'){
					sesJqueryObject('#sesalbum-container-right').css('display','none');
					if(sesJqueryObject('#view_more_<?php echo $randonNumber; ?>'))
						sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display','block');
					if(sesJqueryObject('#view_more_<?php echo $randonNumber; ?>'))
						sesJqueryObject('#loading_image_<?php echo $randonNumber; ?>').css('display','none');					
					if(sesJqueryObject('#view_more_related_<?php echo $randonNumber; ?>'))							
							sesJqueryObject('#view_more_related_<?php echo $randonNumber; ?>').css('display','none');						
						if(sesJqueryObject('#view_more_related<?php echo $randonNumber; ?>'))
							sesJqueryObject('#loading_image_related_<?php echo $randonNumber; ?>').css('display','none');
				}else if(sesJqueryObject(this).attr('data-src') == 'album-related'){
						sesJqueryObject('#sesalbum-container-right').css('display','none');
						if(sesJqueryObject('#view_more_related_<?php echo $randonNumber; ?>'))							
							sesJqueryObject('#view_more_related_<?php echo $randonNumber; ?>').css('display','block');						
						if(sesJqueryObject('#view_more_related<?php echo $randonNumber; ?>'))
							sesJqueryObject('#loading_image_related_<?php echo $randonNumber; ?>').css('display','none');
						if(sesJqueryObject('#view_more_<?php echo $randonNumber; ?>'))							
							sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display','none');						
						if(sesJqueryObject('#view_more_<?php echo $randonNumber; ?>'))
							sesJqueryObject('#loading_image_<?php echo $randonNumber; ?>').css('display','none');
				}else{
					sesJqueryObject('#sesalbum-container-right').css('display','block');
						if(sesJqueryObject('#view_more_<?php echo $randonNumber; ?>'))							
							sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display','none');						
						if(sesJqueryObject('#view_more_<?php echo $randonNumber; ?>'))
							sesJqueryObject('#loading_image_<?php echo $randonNumber; ?>').css('display','none');
						if(sesJqueryObject('#view_more_related_<?php echo $randonNumber; ?>'))							
							sesJqueryObject('#view_more_related_<?php echo $randonNumber; ?>').css('display','none');						
						if(sesJqueryObject('#view_more_related<?php echo $randonNumber; ?>'))
							sesJqueryObject('#loading_image_related_<?php echo $randonNumber; ?>').css('display','none');
				}
		});
	 var divPosition = sesJqueryObject('.sesalbum_cover_inner').offset();
	 sesJqueryObject('html, body').animate({scrollTop: divPosition.top}, "slow");
	 if("<?php echo $this->view_type ; ?>" == 'masonry'){
		sesJqueryObject("#ses-image-view").flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>});
	 }
<?php } ?>
<?php if(!($this->is_related)){ ?>
viewMoreHide_<?php echo $randonNumber; ?>();
  function viewMoreHide_<?php echo $randonNumber; ?>() {
    if ($('view_more_<?php echo $randonNumber; ?>'))
      $('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginator->count() == 0 ? 'none' : ($this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' )) ?>";
			if(sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display') == 'none'){
				sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').remove();
				sesJqueryObject('#loading_image_<?php echo $randonNumber; ?>').remove();
			}	
  }
	 function viewMore_<?php echo $randonNumber; ?> (){
    document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'none';
    document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = '';    
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'widget/index/mod/sesalbum/name/album-view-page/',
      'data': {
        format: 'html',
        page: <?php echo $this->page ; ?>,    
				params :'<?php echo json_encode($this->params); ?>', 
				is_ajax : 1,
				album_id:'<?php echo $this->album_id; ?>',
				identity : '<?php echo $randonNumber; ?>',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('ses-image-view').innerHTML = document.getElementById('ses-image-view').innerHTML + responseHTML;
				if("<?php echo $this->view_type ; ?>" == 'masonry'){
							sesJqueryObject("#ses-image-view").flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>});
				}
				<?php if(($this->mine || $this->canEdit)){ ?>
					$$('.sesalbum_photos_flex_view > li').addClass('sortable');
					SortablesInstance = new Sortables($$('.sesalbum_photos_flex_view'), {
						clone: true,
						constrain: true,
						//handle: 'span',
						onComplete: function(e) {
							var ids = [];
							$$('.sesalbum_photos_flex_view > li').each(function(el) {						
									ids.push(el.get('id').match(/\d+/)[0]);
							});
							<?php if($this->view_type == 'masonry') { ?>
								sesJqueryObject('#ses-image-view').flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>});
							<?php } ?>
							// Send request
							var url =  en4.core.baseUrl+"albums/order/<?php echo $this->album_id; ?>";
							var request = new Request.JSON({
								'url' : url,
								'data' : {
									format : 'json',
									order : ids
								}
							});
							request.send();
						}
					});
					<?php } ?>
				if($('loading_image_<?php echo $randonNumber; ?>'))
					document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
      }
    })).send();
    return false;
  }

function paggingNumber<?php echo $randonNumber; ?>(pageNum){
		 sesJqueryObject ('.overlay_<?php echo $randonNumber ?>').css('display','block');
			(new Request.HTML({
				method: 'post',
				'url': en4.core.baseUrl + 'widget/index/mod/sesalbum/name/album-view-page/',
				'data': {
					format: 'html',
					page: pageNum,    
					params :'<?php echo json_encode($this->params); ?>', 
					is_ajax : 1,
					identity : '<?php echo $randonNumber; ?>',
					album_id:'<?php echo $this->album_id; ?>',
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					sesJqueryObject ('.overlay_<?php echo $randonNumber ?>').css('display','none');
					document.getElementById('ses-image-view').innerHTML =  responseHTML;
					sesJqueryObject("#ses-image-view").flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>});
					<?php if(($this->mine || $this->canEdit)){ ?>
					$$('.sesalbum_photos_flex_view > li').addClass('sortable');
					SortablesInstance = new Sortables($$('.sesalbum_photos_flex_view'), {
						clone: true,
						constrain: true,
						//handle: 'span',
						onComplete: function(e) {
							var ids = [];
							$$('.sesalbum_photos_flex_view > li').each(function(el) {						
									ids.push(el.get('id').match(/\d+/)[0]);
							});
							<?php if($this->view_type == 'masonry') { ?>
								sesJqueryObject('#ses-image-view').flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>});
							<?php } ?>
							// Send request
							var url =  en4.core.baseUrl+"albums/order/<?php echo $this->album_id; ?>";
							var request = new Request.JSON({
								'url' : url,
								'data' : {
									format : 'json',
									order : ids
								}
							});
							request.send();
						}
					});
					<?php } ?>
				}
			})).send();
			return false;
	}
<?php } ?>
<?php if(!($this->is_ajax)){ ?>
	viewMoreHideRelated_<?php echo $randonNumber; ?>();
  function viewMoreHideRelated_<?php echo $randonNumber; ?>() {
    if ($('view_more_related_<?php echo $randonNumber; ?>'))
      $('view_more_related_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->relatedAlbumsPaginator->count() == 0 ? 'none' : ($this->relatedAlbumsPaginator->count() == $this->relatedAlbumsPaginator->getCurrentPageNumber() ? 'none' : '' )) ?>";
			if(sesJqueryObject('#view_more_related_<?php echo $randonNumber; ?>').css('display') == 'none'){
				sesJqueryObject('#view_more_related_<?php echo $randonNumber; ?>').remove();
				sesJqueryObject('#loading_image_related_<?php echo $randonNumber; ?>').remove();
			}	
  }
<?php if(!$this->is_related){ ?>
	if($('view_more_related_<?php echo $randonNumber; ?>'))
	 $('view_more_related_<?php echo $randonNumber; ?>').style.display = 'none';
<?php } ?>
	function viewMoreRelated_<?php echo $randonNumber; ?> (){
    document.getElementById('view_more_related_<?php echo $randonNumber; ?>').style.display = 'none';
    document.getElementById('loading_image_related_<?php echo $randonNumber; ?>').style.display = '';    
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'widget/index/mod/sesalbum/name/album-view-page/',
      'data': {
        format: 'html',
        pageRelated: <?php echo $this->pageRelated ; ?>,    
				paramsRelated :'<?php echo json_encode($this->paramsRelated); ?>', 
				is_related:1,
				album_id:'<?php echo $this->album_id; ?>',
				identity : '<?php echo $randonNumber; ?>',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('sesalbum_related_<?php echo $randonNumber ?>').innerHTML = document.getElementById('sesalbum_related_<?php echo $randonNumber ?>').innerHTML + responseHTML;
				if($('loading_image_related_<?php echo $randonNumber; ?>'))
					document.getElementById('loading_image_related_<?php echo $randonNumber; ?>').style.display = 'none';
      }
    })).send();
    return false;
  }
function paggingNumber<?php echo $randonNumber; ?>PaggingRelated(pageNum){
		 sesJqueryObject ('.overlay_<?php echo $randonNumber ?>PaggingRelated').css('display','block');
			(new Request.HTML({
				method: 'post',
				'url': en4.core.baseUrl + 'widget/index/mod/sesalbum/name/album-view-page/',
				'data': {
					format: 'html',
					pageRelated: pageNum,
					paramsRelated :'<?php echo json_encode($this->paramsRelated); ?>',
					is_related:1,
					identity : '<?php echo $randonNumber; ?>',
					album_id:'<?php echo $this->album_id; ?>',
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					sesJqueryObject ('.overlay_<?php echo $randonNumber ?>PaggingRelated').css('display','none');
					document.getElementById('sesalbum_related_<?php echo $randonNumber ?>').innerHTML = responseHTML;
				if($('loading_image_related_<?php echo $randonNumber; ?>'))
					document.getElementById('loading_image_related_<?php echo $randonNumber; ?>').style.display = 'none';
			return false;
				}
			})).send();
	}
<?php } ?>
<?php if(!$this->is_ajax && !$this->is_related){ ?>
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
				if(sesJqueryObject('.sesalbum_albumFav').length > 0)
					sesJqueryObject('.sesalbum_albumFav').trigger('click');
			}
		}
	});
<?php } ?>
<?php } ?>
</script>
<?php if(!$this->is_ajax && !$this->is_related){ ?>
</div>
<div id="locked_content" style="display:none" class="sesbasic_locked_msg sesbasic_clearfix sesbasic_bxs">
  <div class="sesbasic_locked_msg_img"><i class="fa fa-lock"></i></div>
    <div class="sesbasic_locked_msg_cont">
  	<h1><?php echo $this->translate('Locked Album'); ?></h1>
    <p>
    	<?php echo $this->translate('Seems you enter wrong password'); ?>
      <a href="javascript:;" onClick="window.location.reload();"><?php echo $this->translate('click here'); ?></a>
    	<?php echo $this->translate('to enter password again.'); ?>
  	</p>
  </div>
</div>
<?php if($this->locked){ ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customAlert/sweetalert.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customAlert/sweetalert.js'); ?>
<script type="application/javascript">
 function promptPasswordCheck(){
	 sesJqueryObject('#album_content').hide();
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
				sesJqueryObject('#album_content').remove();
				sesJqueryObject('#locked_content').show();
			 return false;
			}
			if (inputValue === "") {    
			 swal.showInputError("<?php echo $this->translate('You need to write something!');  ?>");     
			 return false   
		}
			if(inputValue.toLowerCase() == '<?php echo strtolower($this->password); ?>'){
					sesJqueryObject('#locked_content').remove();
					sesJqueryObject('#album_content').show();
					sesJqueryObject('#ses-image-view').flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>});
					setCookieSesalbum('<?php echo $this->album->album_id; ?>');
					swal.close();
			}else{
			 	swal("Wrong Password", "You wrote: " + inputValue, "error");
				sesJqueryObject('#album_content').remove();
				sesJqueryObject('#locked_content').show();
			}
	});
 }
 promptPasswordCheck();
</script>
<?php }else{ ?>
<script type="application/javascript">
 sesJqueryObject(document).ready(function(){
		sesJqueryObject('#locked_content').remove();
		sesJqueryObject('#album_content').show();
		sesJqueryObject('#ses-image-view').flexImages({rowHeight: <?php echo str_replace('px','',$this->height); ?>});
	 });
</script>
<?php } ?>
<?php } ?>
