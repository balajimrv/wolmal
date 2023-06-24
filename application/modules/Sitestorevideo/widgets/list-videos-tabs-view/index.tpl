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
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestorevideo/externals/styles/style_sitestorevideo.css');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php if(empty($this->is_ajax)): ?>
<div class="layout_core_container_tabs">
<div class="tabs_alt tabs_parent">
  <ul id="main_tabs">
    <?php foreach ($this->tabs as $tab): ?>
    <?php $class = $tab->name == $this->activTab->name ? 'active' : '' ?>
      <li class = '<?php echo $class ?>'  id = '<?php echo 'sitestorevideo_' . $tab->name.'_tab' ?>'>
        <a href='javascript:void(0);'  onclick="tabSwitchSitestorevideo('<?php echo$tab->name; ?>');"><?php echo $this->translate($tab->getTitle()) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<div id="hideResponse_div" style="display: none;"></div>
<div id="sitestorelbum_videos_tabs">   
   <?php endif; ?>
   <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <?php if($this->is_ajax !=2): ?>
     <ul class="sitestorevideos_browse" id ="sitestorevideo_list_tab_video_content">
       <?php endif; ?>
      <?php foreach( $this->paginator as $video ): ?>
        <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorevideo.profile-sitestorevideos', $video->store_id, $layout);?>
        <li style="margin-left:<?php echo $this->marginPhoto ?>px;margin-right:<?php echo $this->marginPhoto ?>px;">
					<a href="<?php echo $this->url(array('user_id' => $video->owner_id, 'video_id' =>  $video->video_id,'tab' => $tab_id,'slug' => $video->getSlug()),'sitestorevideo_view', true)?>">
						<div class="sitestore_video_thumb_wrapper">
							<?php if ($video->duration): ?>
								<span class="sitestore_video_length">
								<?php
								if ($video->duration > 360)
								$duration = gmdate("H:i:s", $video->duration); else
								$duration = gmdate("i:s", $video->duration);
								if ($duration[0] == '0')
								$duration = substr($duration, 1); echo $duration;
								?>
								</span>
							<?php endif; ?>
							<?php  if ($video->photo_id): ?>
								<?php echo   $this->itemPhoto($video, 'thumb.normal'); ?>
							<?php else: ?>
								<img src= "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestorevideo/externals/images/video.png" class="thumb_normal item_photo_video  thumb_normal" />
							<?php endif;?>
						</div>
					</a>
					<span class="video_title">
          	<?php echo $this->htmlLink($video->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($video->getTitle(), 45), 10),array('title' => $video->getTitle())) ?>
					</span>
					<span class="video_stats">
						<?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $video->store_id);?>
						<?php
							$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
							$tmpBody = strip_tags($sitestore_object->title);
							$store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
						?>
						<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($video->store_id, $video->owner_id, $video->getSlug()),  $store_title,array('title' => $sitestore_object->title)) ?>      
            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0)):?>   
							<?php echo $this->translate('by %1$s',$this->htmlLink($video->getOwner()->getHref(), $video->getOwner()->getTitle(), array('title'=> $video->getOwner()->getTitle(),'class' => 'thumbs_author'))) ?>
            <?php endif;?>
	          <?php if( $this->activTab->name == 'viewed_storevideos' ): ?> <br />
	            <?php echo $this->translate(array('%s view', '%s views', $video->view_count), $this->locale()->toNumber($video->view_count)) ?>
	          <?php elseif( $this->activTab->name == 'commented_storevideos' ): ?> <br />
	            <?php echo $this->translate(array('%s comment', '%s comments', $video->comment_count), $this->locale()->toNumber($video->comment_count)) ?>
	          <?php elseif( $this->activTab->name == 'liked_storevideos' ): ?> <br />
	            <?php echo $this->translate(array('%s like', '%s likes', $video->like_count), $this->locale()->toNumber($video->like_count)) ?>
	          <?php endif; ?>
          </span>
        </li>
      <?php endforeach;?>
       <?php if($this->is_ajax !=2): ?>  
    </ul>  
      <?php endif; ?>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No videos have been created yet.');?>
      </span>
    </div>
  <?php endif; ?>   
<?php if(empty($this->is_ajax)): ?>    
</div>
<?php if (!empty($this->showViewMore)): ?>
<div class="seaocore_view_more" id="sitestorevideo_videos_tabs_view_more" onclick="viewMoreTabVideo()">
  <?php
  echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
      'id' => 'feed_viewmore_link',
      'class' => 'buttonlink icon_viewmore'
  ))
  ?>
</div>
<div class="seaocore_loading" id="sitestorevideo_videos_tabs_loding_image" style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="" />
  <?php echo $this->translate("Loading ...") ?>
</div>
<?php endif; ?>
</div>
<?php endif; ?>

<?php if(empty($this->is_ajax)): ?>
<script type="text/javascript">
  
  var tabSwitchSitestorevideo = function (tabName) {    
  //addWidgetParentClassBusniess(tabName);
 <?php foreach ($this->tabs as $tab): ?>
  if($('<?php echo 'sitestorevideo_'.$tab->name.'_tab' ?>'))
        $('<?php echo 'sitestorevideo_' .$tab->name.'_tab' ?>').erase('class');
  <?php  endforeach; ?>

 if($('sitestorevideo_'+tabName+'_tab'))
        $('sitestorevideo_'+tabName+'_tab').set('class', 'active');
   if($('sitestorelbum_videos_tabs')) {
      $('sitestorelbum_videos_tabs').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loader.gif" class="sitestore_tabs_loader_img" /></center>';
    }   
    if($('sitestorevideo_videos_tabs_view_more'))
    $('sitestorevideo_videos_tabs_view_more').style.display =  'none';
    var request = new Request.HTML({
     method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitestorevideo/name/list-videos-tabs-view',
      'data' : {
        format : 'html',
        isajax : 1,
        tabName: tabName,
        category_id : '<?php echo $this->category_id?>',
        margin_photo : '<?php echo $this->marginPhoto ?>'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('sitestorelbum_videos_tabs').innerHTML = responseHTML;
            <?php if(!empty ($this->showViewMore)): ?>
              hideViewMoreLinkSitestoreVideoVideo();
             <?php endif; ?>
             if(en4.sitevideoview)
           en4.sitevideoview.attachClickEvent(Array('item_photo_sitestorevideo_video','sitestorevideo_thumb_wrapper'));
      }
    });

    request.send();
  }
//   var addWidgetParentClassBusniess= function(tabName){
//  var parentEl=$$(".layout_sitestorevideo_list_videos_tabs_view");
//   parentEl.erase('class');
//   parentEl.set('class', 'generic_layout_container layout_sitestorevideo_list_videos_tabs_view layout_sitestorevideo_list_videos_tabs_'+tabName);
//  } 
//  en4.core.runonce.add(function() {
//    addWidgetParentClassBusniess('<?php echo $this->activTab->name ?>');
//  });
</script>
<?php endif; ?>
<?php if(!empty ($this->showViewMore)): ?>
<script type="text/javascript">
    en4.core.runonce.add(function() {
    hideViewMoreLinkSitestoreVideoVideo();  
    });
    function getNextStoreSitestoreVideoVideo(){
      return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }
    function hideViewMoreLinkSitestoreVideoVideo(){
      if($('sitestorevideo_videos_tabs_view_more'))
        $('sitestorevideo_videos_tabs_view_more').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
    }
        
    function viewMoreTabVideo()
  {
    $('sitestorevideo_videos_tabs_view_more').style.display ='none';
    $('sitestorevideo_videos_tabs_loding_image').style.display ='';
    en4.core.request.send(new Request.HTML({
      method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitestorevideo/name/list-videos-tabs-view',
      'data' : {
        format : 'html', 
        isajax : 2,
        category_id : '<?php echo $this->category_id?>',
        tabName : '<?php echo $this->activTab->name ?>',
        margin_photo : '<?php echo $this->marginPhoto ?>',
        store: getNextStoreSitestoreVideoVideo()
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {    
        $('hideResponse_div').innerHTML=responseHTML;      
        var photocontainer = $('hideResponse_div').getElement('.layout_sitestorevideo_list_videos_tabs_view').innerHTML;
        $('sitestorevideo_list_tab_video_content').innerHTML = $('sitestorevideo_list_tab_video_content').innerHTML + photocontainer;
        $('sitestorevideo_videos_tabs_loding_image').style.display ='none';
        $('hideResponse_div').innerHTML="";  
        if(en4.sitevideoview)
         en4.sitevideoview.attachClickEvent(Array('item_photo_sitestorevideo_video','sitestorevideo_thumb_wrapper'));
      }
    }));

    return false;

  }  
</script>
<?php endif; ?>
