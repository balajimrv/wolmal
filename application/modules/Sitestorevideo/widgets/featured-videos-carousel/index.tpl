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
     ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/styles/sitestore_featured_carousel.css');

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestorevideo/externals/styles/style_sitestorevideo.css');
     
  $this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/sitestoreslideitmoo-1.1_full_source.js');
?>

<a id="group_profile_members_anchor" style="position:absolute;"></a>
<script language="javascript" type="text/javascript">

  var module = 'Sitestorevideo';
</script>
<script language="javascript" type="text/javascript">
  var slideshowvideo;
  window.addEvents ({
    'domready': function() {
      slideshowvideo = new SocialengineSlideItMoo({
        fwdbck_click:1,
        slide_element_limit:1,
        startindex:-1,
        category_id:<?php echo $this->category_id;?>,
        in_one_row:<?php echo  $this->inOneRow_video?>,
        no_of_row:<?php echo  $this->noOfRow_video?>,
        curnt_limit:<?php echo $this->totalItemShowvideo;?>,
        total:<?php echo $this->totalCount_video; ?>,
        limit:<?php echo $this->totalItemShowvideo*2;?>,
        module : 'Sitestorevideo',
        call_count:1,
        foward:'Sitestorevideo_SlideItMoo_forward',
        bck:'Sitestorevideo_SlideItMoo_back',
        overallContainer: 'Sitestorevideo_SlideItMoo_outer',
        elementScrolled: 'Sitestorevideo_SlideItMoo_inner',
        thumbsContainer: 'Sitestorevideo_SlideItMoo_items',
        slideVertical: <?php echo $this->vertical?>,
        itemsVisible:1,
        elemsSlide:1,
        duration:<?php echo  $this->interval;?>,
        itemsSelector: '.Sitestorevideo_SlideItMoo_element',
        itemWidth:<?php echo 146 * $this->inOneRow_video?>,
        itemHeight:<?php echo 146 * $this->noOfRow_video?>,
        showControls:1,
        startIndex:1,
        navs:{ /* starting this version, you'll need to put your back/forward navigators in your HTML */
				fwd:'.Sitestorevideo_SlideItMoo_forward', /* forward button CSS selector */
				bk:'.Sitestorevideo_SlideItMoo_back' /* back button CSS selector */
				},
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) { slideshowvideo.options.call_count = 1;
        }
      });

      $('Sitestorevideo_SlideItMoo_back').addEvent('click', function () {slideshowvideo.sendajax(-1,slideshowvideo,'Sitestorevideo',"<?php echo $this->url(array('module' => 'sitestorevideo','controller' => 'index','action'=>'featured-videos-carousel'),'default',true); ?>");
        slideshowvideo.options.call_count = 1;

      });

      $('Sitestorevideo_SlideItMoo_forward').addEvent('click', function () { slideshowvideo.sendajax(1,slideshowvideo,'Sitestorevideo',"<?php echo $this->url(array('module' => 'sitestorevideo','controller' => 'index','action'=>'featured-videos-carousel'),'default',true); ?>");
        slideshowvideo.options.call_count = 1;
      });
     
      if((slideshowvideo.options.total -slideshowvideo.options.curnt_limit)<=0){
        // hidding forward button
       document.getElementById('Sitestorevideo_SlideItMoo_forward').style.display= 'none';
       document.getElementById('Sitestorevideo_SlideItMoo_back_disable').style.display= 'none';
      }
    }
  });
</script>
<?php
$videoSettings=  array();
$videoSettings['class'] = 'thumb';

?>
<ul class="Sitestorecontent_featured_slider">
  <li>
		<?php
    $module = 'Sitestorevideo';
    $extra_width=0;
    $extra_height=0;    
        if (empty($this->vertical)):
        $typeClass='horizontal';
         if ($this->totalCount_video > $this->totalItemShowvideo):
          $extra_width = 60;
          endif;
          $prev='back';
          $next='forward';
        else:
        	$typeClass='vertical';
        if ($this->totalCount_video > $this->totalItemShowvideo):
          $extra_height=50;
          endif;
          $prev='up';
          $next='down';
        endif;
     ?>
    <div id="Sitestorevideo_SlideItMoo_outer" class="Sitestorecontent_SlideItMoo_outer Sitestorecontent_SlideItMoo_outer_<?php echo $typeClass;?>" style="height:<?php echo 146*$this->heightRow+$extra_height;?>px; width:<?php echo (146*$this->inOneRow_video)+$extra_width;?>px;">
      <div class="Sitestorecontent_SlideItMoo_back" id="Sitestorevideo_SlideItMoo_back" style="display:none;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$prev.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/photo/slider-'.$prev.'-active.png";','onMouseOut'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/photo/slider-'.$prev.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitestorecontent_SlideItMoo_back" id="Sitestorevideo_SlideItMoo_back_loding" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitestorecontent_SlideItMoo_loding'));  ?>
      </div>      
       <div class="Sitestorecontent_SlideItMoo_back_disable" id="Sitestorevideo_SlideItMoo_back_disable" style="display:block;cursor:default;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$prev-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div id="Sitestorevideo_SlideItMoo_inner" class="Sitestorecontent_SlideItMoo_inner">
        <div id="Sitestorevideo_SlideItMoo_items" class="Sitestorecontent_SlideItMoo_items" style="height:<?php echo 146*$this->heightRow;?>px;">
          <div class="Sitestorecontent_SlideItMoo_element Sitestorevideo_SlideItMoo_element" style="width:<?php echo 146*$this->inOneRow_video;?>px;">
              <div class="Sitestorecontent_SlideItMoo_contentList">
               <?php  $i=0; ?>
                  <?php foreach ($this->featuredVideos as $video):?>
                       <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
												$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorevideo.profile-sitestorevideos', $video->store_id, $layout);?>
                       <div class="featured_thumb_content">
                          <a href="<?php echo $this->url(array('user_id' => $video->owner_id, 'video_id' =>  $video->video_id,'tab' => $tab_id,'slug' => $video->getSlug()),'sitestorevideo_view', true)?>" class="thumb_video">
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
                          <span class="show_content_des">
                            <?php
								              $owner = $video->getOwner();
								              echo $this->htmlLink($video->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($video->getTitle(), 45), 10),array('title' => $video->getTitle(),'class'=>'sitestorevideo_title'));
														?>
														<?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $video->store_id);?>
														<?php
														$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
														$tmpBody = strip_tags($sitestore_object->title);
														$store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
														?>
														<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($video->store_id, $video->owner_id, $video->getSlug()),  $store_title,array('title' => $sitestore_object->title)) ?> 
                            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0)):?>      
                            <?php echo $this->translate('by ').
								                  $this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title' => $owner->getTitle()));?>
                            <?php endif;?>
                          </span>
                      </div>
                   <?php  $i++; ?>
                  <?php endforeach; ?>
                <?php for($i; $i<($this->heightRow *$this->inOneRow_video);$i++):?>
                <div class="featured_thumb_content"></div>
                <?php endfor; ?>
              </div>
           </div>
        </div>
      </div>
      <?php $module = 'Sitestorevideo';?>
      <div class="Sitestorecontent_SlideItMoo_forward" id ="Sitestorevideo_SlideItMoo_forward">
      	<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$next.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/photo/slider-'.$next.'-active.png";','onMouseOut'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/photo/slider-'.$next.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitestorecontent_SlideItMoo_forward" id="Sitestorevideo_SlideItMoo_forward_loding"  style="display: none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitestorecontent_SlideItMoo_loding'));  ?>
      </div>
      <div class="Sitestorecontent_SlideItMoo_forward_disable" id="Sitestorevideo_SlideItMoo_forward_disable" style="display:none;cursor:default;">
      	<?php  echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$next-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
    </div>
    <div class="clear"></div>
  </li>
</ul>
