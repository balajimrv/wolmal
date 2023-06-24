<?php ?>
<?php $randonNumber = 'sesadvancedactivity'; ?>
<?php if(empty($this->is_ajax)) { ?>
  <div class="sesact_insagram_data sesbasic_clearfix sesbasic_bxs">
    <ul class="sesbasic_clearfix sesact_insagram_list" id="sesactin-content-data">
<?php } ?>
<?php foreach($this->gallerydata['data'] as $photo) {
//     if($photo['type'] == 'video') {
//       continue;
//     }
   ?>
  <li class="sesact_insagram_list_item">
    <div class="sesact_insagram_list_item_inner">
    	<a href="<?php echo $photo['link']; ?>" target="_blank">
        <?php if($photo['type'] == 'image'): ?>
          <div class="sesact_insagram_list_item_img">
            <img src="<?php echo $photo['images']['standard_resolution']['url']; ?>" />
          </div>
        <?php elseif($photo['type'] == 'video'): ?>
          <div class="sesact_insagram_list_item_img">
            <img src="<?php echo $photo['images']['standard_resolution']['url']; ?>" />
          </div>
          <i class="sesact_int_iconvideo fa fa-video-camera"></i>
        <?php endif; ?>
        <div class="sesact_insagram_list_item_overlay sesbasic_animation">
          <div class="sesact_insagram_list_item_stats">
            <span><i class="fa fa-heart" title="<?php echo $this->translate("%s likes", $photo['likes']['count']); ?>"></i><?php echo $photo['likes']['count']; ?></span>
            <span title="<?php echo $this->translate("%s comments", $photo['comments']['count']); ?>"><i class="fa fa-comment"></i><?php echo $photo['comments']['count']; ?></span>
          </div>
          <div class="sesact_insagram_list_item_info">
            <?php if($photo['caption']): ?>
              <?php if($photo['caption']['text']): ?>
                <span class="sesact_insagram_list_item_info_caption"><?php echo $photo['caption']['text']; ?></span>
              <?php endif; ?>
            <?php endif; ?>
            
            <?php if($photo['location']): ?>
              <?php if($photo['location']['name']): ?>
                <span class="sesact_insagram_list_item_location fa fa-map-marker"><?php echo $photo['location']['name']; ?></span>
              <?php endif; ?>
            <?php endif; ?>
        	</div>
        </div>
			</a>
    </div>	
  </li>
<?php } ?>
<?php if(empty($this->is_ajax)) { ?>    
  </ul>
  <div class="sesbasic_view_more sesbasic_load_btn" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'sesbasic_animation sesbasic_link_btn fa fa-repeat')); ?></div>
  <div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> <span class="sesbasic_link_btn"><i class="fa fa-spinner fa-spin"></i></span></div>
  </div>
  <script type="application/javascript">
  function getInAlbums(param) {
    document.getElementById("instagram_album").innerHTML = '<div class="sesbasic_loading_container" id="fb-spinner"></div>';
    sesJqueryObject('.hidefb').hide();
    //Makes An AJAX Request On Load which retrieves the albums
    sesJqueryObject.ajax({
      type: 'post',
      url:  en4.core.baseUrl+"sesadvancedactivity/index/load-instagram-gallery",
      data: {
          extra_params: param
      },
      success: function( data ) {
        //Hide The Spinner
        sesJqueryObject('.hidefb').show();
          document.getElementById("fb-spinner").style.display = "none";
          //Put the Data in the Div
          sesJqueryObject('#instagram_album').html(data);
      }
    });
  }
  </script>
<?php } ?>

<script type="application/javascript">

  function viewMoreHide_<?php echo $randonNumber; ?>() {
    if ($('view_more_<?php echo $randonNumber; ?>'))
      $('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo (isset($this->gallerydata['pagination']['next_url']) ? 'block' : 'none'); ?>";
    if(sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').css('display') == 'none'){
      sesJqueryObject('#view_more_<?php echo $randonNumber; ?>').remove();
      sesJqueryObject('#loading_image_<?php echo $randonNumber; ?>').remove();
    }
  }
  
  viewMoreHide_<?php echo $randonNumber; ?>();
  function viewMore_<?php echo $randonNumber; ?> () {
    document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'none';
    document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = '';    
    (new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl+'<?php echo "sesadvancedactivity/index/load-instagram-gallery"; ?>',
      'data': {
        format: 'html',
				is_ajax : 1,
				after: "<?php echo isset($this->gallerydata['pagination']['next_max_id']) ? $this->gallerydata['pagination']['next_max_id'] : '';  ?>"
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        sesJqueryObject('#sesactin-content-data').append(responseHTML);
				if($('loading_image_<?php echo $randonNumber; ?>'))
					document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
      }
    })).send();
    return false;
  }
</script>
<?php die; ?>