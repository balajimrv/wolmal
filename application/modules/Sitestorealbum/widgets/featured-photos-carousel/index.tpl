<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorealbum
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
  $this->headLink()
     ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/styles/sitestore_featured_carousel.css');

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestorealbum/externals/styles/style_sitestorealbum.css');
     
  $this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/sitestoreslideitmoo-1.1_full_source.js');
?>

<a id="group_profile_members_anchor1" style="position:absolute;"></a>
<script language="javascript" type="text/javascript">
  var module = 'Sitestorealbum';
</script>
<script language="javascript" type="text/javascript">
  var slideshowalbum;
  window.addEvents ({
    'domready': function() { 
      slideshowalbum = new SocialengineSlideItMoo({
        module : 'Sitestorealbum',
        fwdbck_click:1,
        slide_element_limit:1,
        startindex:-1,
        category_id : <?php echo $this->category_id;?>,
        in_one_row:<?php echo  $this->inOneRow_photo?>,
        no_of_row:<?php echo  $this->noOfRow_photo?>,
        curnt_limit:<?php echo $this->totalItemShow_photo;?>,
        limit:<?php echo $this->totalItemShow_photo*2;?>,
        total:<?php echo $this->totalCount_photo; ?>,
        overallContainer: 'Sitestorealbum_SlideItMoo_outer',
        elementScrolled: 'Sitestorealbum_SlideItMoo_inner',
        thumbsContainer: 'Sitestorealbum_SlideItMoo_items',
        slideVertical: <?php echo $this->vertical?>,
        itemsVisible:1,
        elemsSlide:1,
        call_count:1,
        duration:<?php echo  $this->interval;?>,
        foward:'Sitestorealbum_SlideItMoo_forward',
        bck:'Sitestorealbum_SlideItMoo_back',
        itemsSelector: '.' +'Sitestorealbum_SlideItMoo_element',
        itemWidth:<?php echo 146 * $this->inOneRow_photo?>,
        itemHeight:<?php echo 146 * $this->noOfRow_photo?>,
        showControls:1,
        navs:{ /* starting this version, you'll need to put your back/forward navigators in your HTML */
				fwd:'.Sitestorealbum_SlideItMoo_forward', /* forward button CSS selector */
				bk:'.Sitestorealbum_SlideItMoo_back' /* back button CSS selector */
				},
        startIndex:1,
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) { slideshowalbum.options.call_count = 1;
        }
      });

      $('Sitestorealbum_SlideItMoo_back').addEvent('click', function () {slideshowalbum.sendajax(-1,slideshowalbum,'Sitestorealbum',"<?php echo $this->url(array('action'=>'featured-photos-carousel'),"sitestorealbum_extended",true); ?>");
        slideshowalbum.options.call_count = 1;

      });

      $('Sitestorealbum_SlideItMoo_forward').addEvent('click', function () { slideshowalbum.sendajax(1,slideshowalbum,'Sitestorealbum',"<?php echo $this->url(array('action'=>'featured-photos-carousel'),"sitestorealbum_extended",true); ?>");
        slideshowalbum.options.call_count = 1;
      });
     
      if((slideshowalbum.options.total -slideshowalbum.options.curnt_limit)<=0){
        // hidding forward button
       document.getElementById('Sitestorealbum_SlideItMoo_forward').style.display= 'none';
       document.getElementById('Sitestorealbum_SlideItMoo_back_disable').style.display= 'none';
      }
    }
  });
</script>
<?php
$module = 'Sitestorealbum';
$photoSettings=  array();
$photoSettings['class'] = 'thumb';

?>
<ul class="Sitestorecontent_featured_slider">
  <li>
		<?php
    $extra_width=0;
    $extra_height=0;    
        if (empty($this->vertical)):
        $typeClass='horizontal';
         if ($this->totalCount_photo > $this->totalItemShow_photo):
          $extra_width = 60;
          endif;
          $prev='back';
          $next='forward';
        else:
        	$typeClass='vertical';
        if ($this->totalCount_photo > $this->totalItemShow_photo):
          $extra_height=50;
          endif;
          $prev='up';
          $next='down';
        endif;
     ?>
    <div id="Sitestorealbum_SlideItMoo_outer" class="Sitestorecontent_SlideItMoo_outer Sitestorecontent_SlideItMoo_outer_<?php echo $typeClass;?>" style="height:<?php echo 146*$this->heightRow+$extra_height;?>px; width:<?php echo (146*$this->inOneRow_photo)+$extra_width;?>px;">
      <div class="Sitestorecontent_SlideItMoo_back" id="Sitestorealbum_SlideItMoo_back" style="display:none;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$prev.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/photo/slider-'.$prev.'-active.png";','onMouseOut'=>'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/photo/slider-'.$prev.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitestorecontent_SlideItMoo_back" id="Sitestorealbum_SlideItMoo_back_loding" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitestorecontent_SlideItMoo_loding'));  ?>
      </div>      
       <div class="Sitestorecontent_SlideItMoo_back_disable" id="Sitestorealbum_SlideItMoo_back_disable" style="display:block;cursor:default;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$prev-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div id="Sitestorealbum_SlideItMoo_inner" class="Sitestorecontent_SlideItMoo_inner">
        <div id="Sitestorealbum_SlideItMoo_items" class="Sitestorecontent_SlideItMoo_items" style="height:<?php echo 146*$this->heightRow;?>px;">
          <div class="Sitestorecontent_SlideItMoo_element Sitestorealbum_SlideItMoo_element" style="width:<?php echo 146*$this->inOneRow_photo;?>px;">
              <div class="Sitestorecontent_SlideItMoo_contentList">
               <?php  $i=0; ?>
                  <?php foreach ($this->featuredPhotos as $photo):?>
                       <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
												$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestore.photos-sitestore', $photo->store_id, $layout);?>
                       <div class="featured_thumb_content">
                          <a href="<?php echo $photo->getHref() ?>"  <?php if ($this->showLightBox): ?> onclick="openSeaocoreLightBox('<?php echo $photo->getHref()?>');return false;" <?php endif; ?> title="<?php echo $photo->title; ?>" class="thumb_img">
                          	<span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
                          </a>
                          <span class="show_content_des">
                            <?php
								              $owner = $photo->getOwner();
								              $parent = Engine_Api::_()->getItem('sitestore_album', $photo->album_id);
								              echo $this->translate('in ').
								                  $this->htmlLink($parent->getHref(), $this->string()->truncate($parent->getTitle(),25),array('title' => $parent->getTitle()));
														?>
														<?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $photo->store_id);?>
														<?php
														$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
														$tmpBody = strip_tags($sitestore_object->title);
														$store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
														?>
														<?php echo $this->translate("of ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($photo->store_id, $photo->user_id, $photo->getSlug()),  $store_title,array('title' => $sitestore_object->title)) ?> 
                            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0)):?>     
															<?php echo $this->translate('by ').
																		$this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title' => $owner->getTitle()));?>
                            <?php endif;?>
                          </span>
                      </div>
                   <?php  $i++; ?>
                  <?php endforeach; ?>
                <?php for($i; $i<($this->heightRow *$this->inOneRow_photo);$i++):?>
                <div class="featured_thumb_content"></div>
                <?php endfor; ?>
              </div>
           </div>
        </div>
      </div>
      <div class="Sitestorecontent_SlideItMoo_forward" id ="Sitestorealbum_SlideItMoo_forward">
      	<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$next.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'. $this->layout()->staticBaseUrl        . 'application/modules/Sitestore/externals/images/photo/slider-'.$next.'-active.png";','onMouseOut'=>'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/photo/slider-'.$next.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitestorecontent_SlideItMoo_forward" id="Sitestorealbum_SlideItMoo_forward_loding"  style="display: none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitestorecontent_SlideItMoo_loding'));  ?>
      </div>
      <div class="Sitestorecontent_SlideItMoo_forward_disable" id="Sitestorealbum_SlideItMoo_forward_disable" style="display:none;cursor:default;">
      	<?php  echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$next-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
    </div>
    <div class="clear"></div>
  </li>
</ul>
