<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorevideo
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/style_sitestore_profile.css')
?>

<?php 
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/hideWidgets.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/hideTabs.js');
?>

<?php
  if(file_exists(APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl'))
    include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?>
<?php
 $this->videoPlayerJs();
?>
<script type="text/javascript" >
function owner(thisobj) {
	var Obj_Url = thisobj.href ;
	Smoothbox.open(Obj_Url);
}
</script>

<?php if (!empty($this->show_content)) : ?>
	<script language="JavaScript">
		function videoclose(id) {
			var video_object="lsit_video_object_"+id;
			var video_thumb="sitestorevideo_video_thumb_"+id;

			$(video_thumb).style.display="block";
			document.getElementById(video_object).style.display="none";
		}
	</script>

	<script type="text/javascript">
var sitestoreVideoSearchText = '<?php echo $this->search ?>';
		var sitestoreVideoStore = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
		function showsearchvideocontent () {

		  var url = en4.core.baseUrl + 'widget/index/mod/sitestorevideo/name/profile-sitestorevideos';
      if(typeof $('sitestorevideo_videos_search_input_text') != 'undefined') {
			$('sitestorevideo_videos_search_input_text').addEvent('keypress', function(e) {
				if( e.key != 'enter' ) return;
				if($('sitestorevideo_videos_search_input_checkbox') && $('sitestorevideo_videos_search_input_checkbox').checked == true) {
					var checkbox_value = 1;
				}
				else {
					var checkbox_value = 0;
				}
				if($('sitestorevideo_search') != null) {
					$('sitestorevideo_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/spinner_temp.gif" /></center>';
				}
					en4.core.request.send(new Request.HTML({
					'url' : url,
					'data' : {
						'format' : 'html',
						'subject' : en4.core.subject.guid,
						'search' : $('sitestorevideo_videos_search_input_text').value,
						'selectbox' : $('sitestorevideo_videos_search_input_selectbox').value,
						'checkbox' : checkbox_value,
						'isajax' : '1',
						'tab' : '<?php echo $this->content_id ?>'
					}, onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
         (function(){
          if(en4.sitevideoview)
           en4.sitevideoview.attachClickEvent(Array('item_photo_sitestorevideo_video','sitestorevideo_thumb_wrapper'));  
          }).delay(100);
          }
				}), {
				'element' : $('id_' + <?php echo $this->content_id ?>)
				});
			});}

		}

		en4.core.runonce.add(function() {
			var url = en4.core.baseUrl + 'widget/index/mod/sitestorevideo/name/profile-sitestorevideos';
      if(typeof $('sitestorevideo_videos_search_input_text') != 'undefined') {
			$('sitestorevideo_videos_search_input_text').addEvent('keypress', function(e) {
				if( e.key != 'enter' ) return;
				if($('sitestorevideo_videos_search_input_checkbox') && $('sitestorevideo_videos_search_input_checkbox').checked == true) {
					var checkbox_value = 1;
				}
				else {
					var checkbox_value = 0;
				}
				if($('sitestorevideo_search') != null) {
					$('sitestorevideo_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/spinner_temp.gif" /></center>';
				}
					en4.core.request.send(new Request.HTML({
					'url' : url,
					'data' : {
						'format' : 'html',
						'subject' : en4.core.subject.guid,
						'search' : $('sitestorevideo_videos_search_input_text').value,
						'selectbox' : $('sitestorevideo_videos_search_input_selectbox').value,
						'checkbox' : checkbox_value,
						'isajax' : '1',
						'tab' : '<?php echo $this->content_id ?>'
					}, onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          (function(){
          if(en4.sitevideoview)
           en4.sitevideoview.attachClickEvent(Array('item_photo_sitestorevideo_video','sitestorevideo_thumb_wrapper'));  
          }).delay(100);
          }
				}), {
				'element' : $('id_' + <?php echo $this->content_id ?>)
				});
			});}
		});


		function Ordervideoselection()
		{
			var sitestoreVideoSearchSelectbox = '<?php echo $this->selectbox ?>';
			var sitestoreVideoStore = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
			var url = en4.core.baseUrl + 'widget/index/mod/sitestorevideo/name/profile-sitestorevideos';
			if($('sitestorevideo_videos_search_input_checkbox') && $('sitestorevideo_videos_search_input_checkbox').checked == true) {
				var checkbox_value = 1;
			}
			else {
				var checkbox_value = 0;
			}
			if($('sitestorevideo_search') != null) {
				$('sitestorevideo_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/spinner_temp.gif" /></center>';
			}
			en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'search' : $('sitestorevideo_videos_search_input_text').value,
					'selectbox' : $('sitestorevideo_videos_search_input_selectbox').value,
					'checkbox' : checkbox_value,
					'isajax' : '1',
					'tab' : '<?php echo $this->content_id ?>'
				}, onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          (function(){
          if(en4.sitevideoview)
           en4.sitevideoview.attachClickEvent(Array('item_photo_sitestorevideo_video','sitestorevideo_thumb_wrapper'));  
          }).delay(100);
          }
			}), {
						'element' : $('id_' + <?php echo $this->content_id ?>)
					});
		}

		function Myvideo() {
			var sitestoreVideoSearchCheckbox = '<?php echo $this->checkbox ?>';

			var sitestoreVideoStore = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
			var url = en4.core.baseUrl + 'widget/index/mod/sitestorevideo/name/profile-sitestorevideos';
			if($('sitestorevideo_videos_search_input_checkbox') && $('sitestorevideo_videos_search_input_checkbox').checked == true) {
				var checkbox_value = 1;
			}
			else {
				var checkbox_value = 0;
			}

			if($('sitestorevideo_search') != null) {
					$('sitestorevideo_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/spinner_temp.gif" /></center>';
			}
				en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'search' : $('sitestorevideo_videos_search_input_text').value,
					'selectbox' : $('sitestorevideo_videos_search_input_selectbox').value,
					'checkbox' : checkbox_value,
					'isajax' : '1',
					'tab' : '<?php echo $this->content_id ?>'
				}, onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
          (function(){
          if(en4.sitevideoview)
           en4.sitevideoview.attachClickEvent(Array('item_photo_sitestorevideo_video','sitestorevideo_thumb_wrapper'));  
          }).delay(100); }
			}), {
				'element' : $('id_' + <?php echo $this->content_id ?>)
			});
		}

		

	</script>
<?php endif; ?>

<?php if (empty($this->isajax)) : ?>
  <div id="id_<?php echo $this->content_id; ?>">
<?php endif;?>

<?php if (!empty($this->show_content)) : ?>
	<?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_video">
			<?php echo $this->translate($this->sitestore->getTitle());?><?php echo $this->translate("'s Videos");?>
		</div>
	<?php endif;?>
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideowidget', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)):?>
		<div class="layout_right" id="communityad_video">
				<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideowidget', 3),"loaded_by_ajax"=>0,'widgetId'=>"store_video"))?>		
		</div>
		<div class="layout_middle">
  <?php endif;?>

  <?php if($this->can_create):?>
		<div class="seaocore_add ">
			<a href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id,'tab' => $this->identity_temp),'sitestorevideo_create', true) ?>' class='buttonlink icon_type_sitestorevideo_new'><?php echo $this->translate('Add a Video');?></a>
		</div>
	<?php endif; ?>
	<?php if( $this->paginator->count() <= 0 && (empty($this->search) && empty($this->checkbox) &&
    empty($this->selectbox))):?>
		<div class="sitestore_list_filters" style="display:none;">
	<?php else: ?>
	  <div class="sitestore_list_filters">
  <?php endif; ?>

	<?php if(!empty($this->viewer_id)):?>
		<div class="sitestore_list_filter_first">
			<?php if($this->checkbox != 1): ?>
				<input id="sitestorevideo_videos_search_input_checkbox" type="checkbox" value="1" onclick="Myvideo();" /><?php echo $this->translate("Show my videos"); ?>
			<?php else: ?>
				<input id="sitestorevideo_videos_search_input_checkbox" type="checkbox" value="2"  checked = "checked" onclick="Myvideo();" /><?php echo $this->translate("Show my videos");?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="sitestore_list_filter_field">
		<?php echo $this->translate("Search:");?>
		<input id="sitestorevideo_videos_search_input_text" type="text" value="<?php echo $this->search; ?>" />
	</div>

	<div class="sitestore_list_filter_field">
		<?php echo $this->translate('Browse by:');?>
		<select name="default_visibility" id="sitestorevideo_videos_search_input_selectbox" onChange= "Ordervideoselection();">
      <option value=""></option>
			<?php if($this->selectbox == 'creation_date'): ?>
				<option value="creation_date" selected='selected'><?php echo $this->translate("Most Recent"); ?></option>
			<?php else:?>
				<option value="creation_date"><?php echo $this->translate("Most Recent"); ?></option>
			<?php endif;?>
      <?php if($this->selectbox == 'like_count'): ?>
			<option value="like_count" selected='selected'><?php echo $this->translate("Most Liked"); ?></option>
		  <?php else:?>
			  <option value="like_count"><?php echo $this->translate("Most Liked"); ?></option>
		  <?php endif;?>
			<?php if($this->selectbox == 'comment_count'): ?>
				<option value="comment_count" selected='selected'><?php echo $this->translate("Most Commented"); ?></option>
			<?php else:?>
				<option value="comment_count"><?php echo $this->translate("Most Commented"); ?></option>
			<?php endif;?>
			<?php if($this->selectbox == 'view_count'): ?>
				<option value="view_count" selected='selected'><?php echo $this->translate("Most Viewed"); ?></option>
			<?php else:?>
				<option value="view_count"><?php echo $this->translate("Most Viewed"); ?></option>
			<?php endif;?>
			<?php if($this->selectbox == 'rating'): ?>
				<option value="rating" selected='selected'><?php echo $this->translate("Most Rated"); ?></option>
			<?php else:?>
				<option value="rating"><?php echo $this->translate("Most Rated"); ?></option>
			<?php endif;?>
      <?php if($this->selectbox == 'featured'): ?>
				<option value="featured" selected='selected'><?php echo $this->translate("Featured"); ?></option>
			<?php else:?>
				<option value="featured"><?php echo $this->translate("Featured"); ?></option>
			<?php endif;?>
      <?php if($this->selectbox == 'highlighted'): ?>
				<option value="highlighted" selected='selected'><?php echo $this->translate("Highlighted"); ?></option>
			<?php else:?>
				<option value="highlighted"><?php echo $this->translate("Highlighted"); ?></option>
			<?php endif;?>
		</select>
	</div>
</div>
<div id='sitestorevideo_search'>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class='sitestorevideo_profile'>
    <?php foreach( $this->paginator as $item ): ?>
      <?php if($item->highlighted == 1): ?>
				<li id="sitestorevideo-item-<?php echo $item->video_id ?>" class="sitestore_list_highlight">
			<?php else: ?>
				<li id="sitestorevideo-item-<?php echo $item->video_id ?>">
			<?php endif; ?>
        <?php if($item->status == 1 && !$this->sitevideoviewEnable):?>
				<a id="sitestorevideo_video_thumb_<?php echo $item->video_id; ?>" style="" href="javascript:void(0);" onclick="javascript:var myElement = 	$(this);myElement.style.display='none';var next = myElement.getNext(); next.style.display='block'; ">
        <?php else:?>
          <a href="<?php echo $this->url(array('user_id' => $item->owner_id, 'video_id' =>  $item->video_id,'tab' => $this->identity_temp,'slug' => $item->getSlug()),'sitestorevideo_view', true)?>">
        <?php endif;?>
					<div class="sitestore_video_thumb_wrapper">
						<?php if ($item->duration): ?>
							<span class="sitestore_video_length">
								<?php
									if ($item->duration > 360)
										$duration = gmdate("H:i:s", $item->duration); else
										$duration = gmdate("i:s", $item->duration);
									if ($duration[0] == '0')
										$duration = substr($duration, 1); echo $duration;
								?>
							</span>
						<?php endif; ?>
						<?php  if ($item->photo_id): ?>
							<?php echo   $this->itemPhoto($item, 'thumb.normal'); ?>
						<?php else: ?>
							<img src= "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestorevideo/externals/images/video.png" class="thumb_normal item_photo_video thumb_normal" />
						<?php endif;?>
					</div>
				</a>
         <?php if($item->status == 1 && !$this->sitevideoviewEnable):?>
			<?php if($item->type == 1): ?>
         <?php    $videoEmbedded = $item->compileYouTube($item->video_id, $item->code, false); ?>
      <?php  elseif($item->type == 2) :?>
         <?php   $videoEmbedded = $item->compileVimeo($item->video_id, $item->code, false); ?>
         <?php  elseif($item->type == 3) :?>
            <?php  $video_location = Engine_Api::_()->storage()->get($item->file_id, $item->getType())->getHref();
                    //$videoEmbedded = $item->compileFlowPlayer($video_location, false);?>
         <?php endif ?>

         <div id="lsit_video_object_<?php echo $item->video_id; ?>" style="display: none;" class="sitestorevideo_play">
							<?php if($item->type == 3): ?>
							<div id='video_Frame_<?php  echo $item->video_id ?>'></div>
			<?php
                                                        $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;

       $flowplayerSwf = $coreVersion < '4.8.10' ? 'flowplayer-3.1.5.swf' : 'flowplayer-3.2.18.swf';?>					<script type='text/javascript'>
                  (function() {
									flashembed("video_Frame_<?php  echo $item->video_id ?>",
									{
										src: "<?php echo Zend_Registry::get('StaticBaseUrl') ?>externals/flowplayer/<?php echo $flowplayerSwf;?>",
										width: 420,
										height: 326,
										wmode: 'opaque'
									},
									{
										config: {
											clip: {
												url: "<?php echo $video_location;?>",
												autoPlay: false,
												duration: "<?php echo $item->duration ?>",
												autoBuffering: true
											},
											plugins: {
												controls: {
													background: '#000000',
													bufferColor: '#333333',
													progressColor: '#444444',
													buttonColor: '#444444',
													buttonOverColor: '#666666'
												}
											},
											canvas: {
												backgroundColor:'#000000'
											}
										}
									});
 }).delay(1000);
								</script>
					<?php else: ?>
              <?php echo $videoEmbedded ?>
					<?php endif; ?>
                <div onclick=" videoclose('<?php echo $item->video_id ?>')" class="sitestorevideo_close" title="Close"></div>
         </div>
         <?php endif; ?>
				<div class="sitestorevideo_profile_info">
					<div class="sitestorevideo_profile_title">
            <span>
							<?php if($item->featured == 1): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
							<?php endif;?>
					  </span>
						<?php echo $this->htmlLink(array('route' => 'sitestorevideo_view', 'user_id' => $item->owner_id, 'video_id' =>  $item->video_id,'tab' => $this->identity_temp,'slug' => $item->getSlug()), $item->title) ?>
					</div>
					<div class="sitestorevideo_profile_info_date">
						<?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
						<span class="video_views">
							<?php echo $this->translate('Added');?> <?php echo $this->timestamp(strtotime($item->creation_date)) ?> - <?php echo $this->translate(array('%s comment', '%s comments', $item->comments()->getCommentCount()),$this->locale()->toNumber($item->comments()->getCommentCount())) ?> - <?php echo $this->translate(array('%s like', '%s likes', $item->likes()->getLikeCount()),$this->locale()->toNumber($item->likes()->getLikeCount())) ?> - <?php echo $this->translate(array('%s view', '%s views', $item->view_count),$this->locale()->toNumber($item->view_count)) ?>
						</span>
						<span class="fright">
						<span class="video_star"></span><span class="video_star"></span><span class="video_star"></span><span class="video_star"></span><span class="video_star_half"></span>
						<?php if($item->rating>0):?>

								<?php
									$currentRatingValue = $item->rating;
									$difference = $currentRatingValue- (int)$currentRatingValue;
									if($difference < .5) {
										$finalRatingValue = (int)$currentRatingValue;
									}
									else {
										$finalRatingValue = (int)$currentRatingValue + .5;
									}
								?>

							<?php for($x=1; $x<=$item->rating; $x++): ?><span class="rating_star_generic rating_star" title= "<?php echo $finalRatingValue.$this->translate(' rating');?>" ></span><?php endfor; ?><?php if((round($item->rating)-$item->rating)>0):?><span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue ?> rating"></span><?php endif; ?>
						<?php endif; ?>
						</span>
					</div>

					<div class="sitestorevideo_profile_info_desc">
						<?php echo substr(strip_tags($item->description), 0, 350); if (strlen($item->description)>349) echo "...";?>
					</div>

					<?php if($item->status == 0):?>
						<div class="tip">
							<span>
								<?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.')?>
							</span>
						</div>
					<?php elseif($item->status == 2):?>
						<div class="tip">
							<span>
								<?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.')?>
							</span>
						</div>
					<?php elseif($item->status == 3):?>
						<div class="tip">
							<span>
								<?php echo $this->translate('Video conversion failed. Please try %1$suploading again%2$s.', '<a href="'.$this->url(array('store_id' => $this->sitestore->store_id,'tab' => $this->identity_temp,'type'=>3),'sitestorevideo_create', true).'">', '</a>'); ?>
							</span>
						</div>
					<?php elseif($item->status == 4):?>
						<div class="tip">
							<span>
								<?php echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('store_id' => $this->sitestore->store_id,'tab' => $this->identity_temp,'type'=>3),'sitestorevideo_create', true).'">', '</a>'); ?>
							</span>
						</div>
					<?php elseif($item->status == 5):?>
						<div class="tip">
							<span>
								<?php echo $this->translate('Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('store_id' => $this->sitestore->store_id,'tab' => $this->identity_temp,'type'=>3),'sitestorevideo_create', true).'">', '</a>'); ?>
							</span>
						</div>
					<?php elseif($item->status == 7):?>
						<div class="tip">
							<span>
								<?php echo $this->translate('Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.', '<a href="'.$this->url(array('store_id' => $this->sitestore->store_id,'tab' => $this->identity_temp,'type'=>3),'sitestorevideo_create', true).'">', '</a>'); ?>
							</span>
						</div>
					<?php endif;?>
					<div class='sitestorevideo_profile_options'>
						<?php echo $this->htmlLink(array('route' => 'sitestorevideo_view', 'user_id' => $item->owner_id, 'video_id' =>  $item->video_id,'tab' => $this->identity_temp,'slug' => $item->getSlug()), $this->translate('View Video'), array(
								'class' => 'buttonlink icon_type_sitestorevideo'
						)) ?>

						<?php if($item->owner_id == $this->viewer_id || $this->can_edit == 1): ?>
							<?php echo $this->htmlLink(array('route' => 'sitestorevideo_edit', 'video_id' => $item->video_id,'store_id'=>$item->store_id,'tab'=>$this->identity_temp), $this->translate('Edit Video'), array(
									'class' => 'buttonlink icon_type_sitestorevideo_edit'
							)) ?>

							<?php  echo $this->htmlLink(array('route' => 'sitestorevideo_delete', 'video_id' => $item->video_id,'store_id'=>$item->store_id,'tab'=>$this->identity_temp), $this->translate('Delete Video'), array(
											'class' => 'buttonlink icon_type_sitestorevideo_delete'
									));?>
						<?php endif; ?>

            <?php if (!empty($this->viewer_id)): ?>
							<a class="buttonlink icon_sitestorevideo_comment" href="<?php echo $item->getHref();?>#comments_sitestorevideo_video_<?php echo $item->video_id?>"><?php echo $this->translate('Comment on Video'); ?></a>
            <?php endif; ?>

            <?php if(($this->allowView)):?>
							<?php if($item->featured == 1) echo $this->htmlLink(array('route' => 'sitestorevideo_featured', 'video_id' => $item->video_id,'tab'=>$this->identity_temp), $this->translate('Make Un-featured'), array(
								'onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_unfeatured')) ?>
							<?php if($item->featured == 0) echo $this->htmlLink(array('route' => 'sitestorevideo_featured', 'video_id' => $item->video_id,'tab'=>$this->identity_temp), $this->translate('Make Featured'), array(
								'onclick' => 'owner(this);return false',' class' => 'buttonlink seaocore_icon_featured')) ?>
						<?php endif;?>
            <?php if(!empty($this->can_edit) && $this->canMakeHighlighted):?>
							<?php if($item->highlighted == 1) echo $this->htmlLink(array('route' => 'sitestorevideo_highlighted', 'video_id' => $item->video_id,'tab'=>$this->identity_temp), $this->translate('Make Un-highlighted'), array(
								'onclick' => 'owner(this);return false', ' class' => 'buttonlink icon_sitestore_unhighlighted')) ?>
							<?php if($item->highlighted == 0) echo $this->htmlLink(array('route' => 'sitestorevideo_highlighted', 'video_id' => $item->video_id,'tab'=>$this->identity_temp), $this->translate('Make Highlighted'), array(
								'onclick' => 'owner(this);return false',' class' => 'buttonlink icon_sitestore_highlighted')) ?>
						<?php endif;?>
					</div>
				</div>
			</li>
    <?php endforeach; ?>

		<?php if( $this->paginator->count() > 1 ): ?>
			<div>
				<?php if( $this->paginator->getCurrentPageNumber() > 1 ): ?>
					<div id="user_sitestore_members_previous" class="paginator_previous">
						<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
							'onclick' => 'paginateSitestoreVideos(sitestoreVideoStore - 1)',
							'class' => 'buttonlink icon_previous'
						)); ?>
					</div>
				<?php endif; ?>
				<?php if( $this->paginator->getCurrentPageNumber() < $this->paginator->count() ): ?>
					<div id="user_sitestore_members_next" class="paginator_next">
						<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
							'onclick' => 'paginateSitestoreVideos(sitestoreVideoStore + 1)',
							'class' => 'buttonlink_right icon_next'
						)); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
  </ul>
	<?php elseif($this->paginator->count() <= 0 && ($this->search != '' || $this->checkbox == 1 || $this->selectbox == 'view_count' ||  $this->selectbox == 'comment_count' || $this->selectbox == 'like_count' || $this->selectbox == 'rating' || $this->selectbox == 'creation_date')):?>
			<div class="tip" id='sitestorevideo_search'>
				<span>
					<?php echo $this->translate('No videos were found matching your search criteria.');?>
				</span>
		</div>
<?php else: ?>
  <div class="tip" id='sitestorevideo_search'>
		<span>
			<?php echo $this->translate('No videos have been added in this Store yet.'); ?>
			<?php if ($this->can_create):  ?>
				<?php echo $this->translate('Be the first to %1$sadd%2$s one!', '<a href="'.$this->url(array('store_id' => $this->sitestore->store_id,'tab'=>$this->identity_temp), 'sitestorevideo_create').'">', '</a>'); ?>
			<?php endif; ?>
		</span>
	</div>
<?php endif;?>
</div>
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideowidget', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)):?>
		</div>
<?php endif;?>
<?php endif;?>

<?php if (empty($this->isajax)) : ?>
	</div>
<?php endif;?>

<script type="text/javascript">
  var adwithoutpackage = '<?php echo Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore) ?>';
  var video_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.advideowidget', 3);?>';
	var is_ajax_divhide = '<?php echo $this->isajax;?>';
  var execute_Request_Video = '<?php echo $this->show_content;?>';
  var store_communityad_integration = '<?php echo $store_communityad_integration; ?>';
  //window.addEvent('domready', function () {
 	var show_widgets = '<?php echo $this->widgets ?>';
    var VideotabId = '<?php echo $this->module_tabid;?>';
    var VideoTabIdCurrent = '<?php echo $this->identity_temp; ?>';

    if (VideoTabIdCurrent == VideotabId) {
    	if(store_showtitle != 0) {
    		if($('profile_status') && show_widgets == 1) {
				  $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitestore->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Videos');?></h2>";
    		}
    		if($('layout_video')) {
				  $('layout_video').style.display = 'block';
				}
    	}
      hideWidgetsForModule('sitestorevideo');
			prev_tab_id = '<?php echo $this->content_id; ?>';
			prev_tab_class = 'layout_sitestorevideo_profile_sitestorevideos';
			execute_Request_Video = true;
			hideLeftContainer (video_ads_display, store_communityad_integration, adwithoutpackage);
    }
    else if (is_ajax_divhide != 1) {
  		if($('global_content').getElement('.layout_sitestorevideo_profile_sitestorevideos')) {
				$('global_content').getElement('.layout_sitestorevideo_profile_sitestorevideos').style.display = 'none';
		  }
		}

  // });

    $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
    	$('global_content').getElement('.layout_sitestorevideo_profile_sitestorevideos').style.display = 'block';
    	if(store_showtitle != 0) {
    		if($('profile_status') && show_widgets == 1) {
				  $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitestore->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Videos');?></h2>";
    		}
    	}
      hideWidgetsForModule('sitestorevideo');
		 	$('id_' + <?php echo $this->content_id ?>).style.display = "block";
	    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
	      $$('.'+ prev_tab_class).setStyle('display', 'none');
	    }
    	if (prev_tab_id != '<?php echo $this->content_id; ?>') {
    		execute_Request_Video = false;
    		prev_tab_id = '<?php echo $this->content_id; ?>';
    		prev_tab_class = 'layout_sitestorevideo_profile_sitestorevideos';
    	}
    	if(execute_Request_Video == false) {
    		ShowContent('<?php echo $this->content_id; ?>', execute_Request_Video, '<?php echo $this->identity_temp?>', 'video', 'sitestorevideo', 'profile-sitestorevideos', store_showtitle, 'null', video_ads_display, store_communityad_integration, adwithoutpackage);
    		execute_Request_Video = true;
    	}
			if('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1);?>' && video_ads_display == 0)
{setLeftLayoutForStore();		    }
   });



   var paginateSitestoreVideos = function(store) {
			var url = en4.core.baseUrl + 'widget/index/mod/sitestorevideo/name/profile-sitestorevideos';
			if($('sitestorevideo_videos_search_input_checkbox') && $('sitestorevideo_videos_search_input_checkbox').checked == true) {
				var checkbox_value = 1;
			}
			else {
				var checkbox_value = 0;
			}
      if($type($('sitestorevideo_videos_search_input_selectbox')) && typeof $('sitestorevideo_videos_search_input_selectbox') != 'undefined') {
var select_value = $('sitestorevideo_videos_search_input_selectbox').value;
      }
			en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'search' : sitestoreVideoSearchText,
					'selectbox' : select_value,
					'checkbox' : checkbox_value,
					'store' : store,
					'isajax' : '1',
					'tab' : '<?php echo $this->content_id ?>'
				}, onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
         (function(){
          if(en4.sitevideoview)
           en4.sitevideoview.attachClickEvent(Array('item_photo_sitestorevideo_video','sitestorevideo_thumb_wrapper'));  
          }).delay(100);
        }
			}), {
				'element' : $('id_' + <?php echo $this->content_id ?>)
			});
		}

</script>
