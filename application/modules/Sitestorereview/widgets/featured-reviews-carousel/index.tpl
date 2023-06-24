<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
  $this->headLink()
     ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/styles/sitestore_featured_carousel.css');
  $this->headScript()
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/sitestoreslideitmoo-1.1_full_source.js');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>

<a id="group_profile_members_anchor" style="position:absolute;"></a>
<script language="javascript" type="text/javascript">
  var module = 'Sitestorereview';
</script>
<script language="javascript" type="text/javascript">
  var slideshowreview;
  window.addEvents ({
    'domready': function() {
      slideshowreview = new SocialengineSlideItMoo({
        fwdbck_click:1,
        slide_element_limit:1,
        startindex:-1,
        category_id:<?php echo $this->category_id;?>,
        in_one_row:<?php echo  $this->inOneRow_review?>,
        no_of_row:<?php echo  $this->noOfRow_review?>,
        curnt_limit:<?php echo $this->totalItemShowreview;?>,
        total:<?php echo $this->totalCount_review; ?>,
        limit:<?php echo $this->totalItemShowreview*2;?>,
        module : 'Sitestorereview',
        call_count:1,
        foward:'Sitestorereview_SlideItMoo_forward',
        bck:'Sitestorereview_SlideItMoo_back',
        overallContainer: 'Sitestorereview_SlideItMoo_outer',
        elementScrolled: 'Sitestorereview_SlideItMoo_inner',
        thumbsContainer: 'Sitestorereview_SlideItMoo_items',
        slideVertical: <?php echo $this->vertical?>,
        itemsVisible:1,
        elemsSlide:1,
        duration:<?php echo  $this->interval;?>,
        itemsSelector: '.Sitestorereview_SlideItMoo_element',
        itemWidth:<?php echo 146 * $this->inOneRow_review?>,
        itemHeight:<?php echo 146 * $this->noOfRow_review?>,
        showControls:1,
        startIndex:1,
        navs:{ /* starting this version, you'll need to put your back/forward navigators in your HTML */
				fwd:'.Sitestorereview_SlideItMoo_forward', /* forward button CSS selector */
				bk:'.Sitestorereview_SlideItMoo_back' /* back button CSS selector */
				},
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) { slideshowreview.options.call_count = 1;
        }
      });

      $('Sitestorereview_SlideItMoo_back').addEvent('click', function () {slideshowreview.sendajax(-1,slideshowreview,'Sitestorereview',"<?php echo $this->url(array('module' => 'sitestorereview','controller' => 'index','action'=>'featured-reviews-carousel'),'default',true); ?>");
        slideshowreview.options.call_count = 1;

      });

      $('Sitestorereview_SlideItMoo_forward').addEvent('click', function () { slideshowreview.sendajax(1,slideshowreview,'Sitestorereview',"<?php echo $this->url(array('module' => 'sitestorereview','controller' => 'index','action'=>'featured-reviews-carousel'),'default',true); ?>");
        slideshowreview.options.call_count = 1;
      });
     
      if((slideshowreview.options.total -slideshowreview.options.curnt_limit)<=0){
        // hidding forward button
       document.getElementById('Sitestorereview_SlideItMoo_forward').style.display= 'none';
       document.getElementById('Sitestorereview_SlideItMoo_back_disable').style.display= 'none';
      }
    }
  });
</script>
<?php
$reviewSettings=  array();
$reviewSettings['class'] = 'thumb';

?>
<ul class="Sitestorecontent_featured_slider">
  <li>
		<?php
    $module = 'Sitestorereview';
    $extra_width=0;
    $extra_height=0;    
        if (empty($this->vertical)):
        $typeClass='horizontal';
         if ($this->totalCount_review > $this->totalItemShowreview):
          $extra_width = 60;
          endif;
          $prev='back';
          $next='forward';
        else:
        	$typeClass='vertical';
        if ($this->totalCount_review > $this->totalItemShowreview):
          $extra_height=50;
          endif;
          $prev='up';
          $next='down';
        endif;
     ?>
    <div id="Sitestorereview_SlideItMoo_outer" class="Sitestorecontent_SlideItMoo_outer Sitestorecontent_SlideItMoo_outer_<?php echo $typeClass;?>" style="height:<?php echo 146*$this->heightRow+$extra_height;?>px; width:<?php echo (146*$this->inOneRow_review)+$extra_width;?>px;">
      <div class="Sitestorecontent_SlideItMoo_back" id="Sitestorereview_SlideItMoo_back" style="display:none;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$prev.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/photo/slider-'.$prev.'-active.png";','onMouseOut'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/photo/slider-'.$prev.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitestorecontent_SlideItMoo_back" id="Sitestorereview_SlideItMoo_back_loding" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitestorecontent_SlideItMoo_loding'));  ?>
      </div>      
       <div class="Sitestorecontent_SlideItMoo_back_disable" id="Sitestorereview_SlideItMoo_back_disable" style="display:block;cursor:default;">
				<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$prev-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div id="Sitestorereview_SlideItMoo_inner" class="Sitestorecontent_SlideItMoo_inner">
        <div id="Sitestorereview_SlideItMoo_items" class="Sitestorecontent_SlideItMoo_items" style="height:<?php echo 146*$this->heightRow;?>px;">
          <div class="Sitestorecontent_SlideItMoo_element Sitestorereview_SlideItMoo_element" style="width:<?php echo 146*$this->inOneRow_review;?>px;">
              <div class="Sitestorecontent_SlideItMoo_contentList">
               <?php  $i=0; ?>
                  <?php $photo_review = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.photo', 1);?>
                  <?php foreach ($this->featuredReviews as $review):?>
                       <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $review->store_id);?>
                       <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
												$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestorereview.profile-sitestorereviews', $review->store_id, $layout);?>
                        <div class="featured_thumb_content">
													<a class="thumb_img" href="<?php echo Engine_Api::_()->sitestore()->getHref($sitestore_object->store_id, $sitestore_object->owner_id, $sitestore_object->getSlug()); ?>">
                           
														<?php if(!empty($photo_review)):?>
                              <span>
																<?php $user = Engine_Api::_()->getItem('user', $review->owner_id);
																echo $this->itemPhoto($user, 'thumb.profile');
																?>
                              </span>
														<?php else:?>
                              <span>
															<?php echo $this->itemPhoto($sitestore_object, 'thumb.normaml', $sitestore_object->getTitle()) ?></span>
														<?php endif;?>
													</a>
                          <span class="show_content_des">
                            <?php
								              $owner = $review->getOwner();
								              echo $this->htmlLink($review->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($review->getTitle(), 45), 10),array('title' => $review->getTitle()));
														?>
														<?php
														$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
														$tmpBody = strip_tags($sitestore_object->title);
														$store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
														?>
														<?php echo $this->translate("on ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($review->store_id, $review->owner_id, $review->getSlug()),  $store_title,array('title' => $sitestore_object->title,'class' => 'bold')) ?> 
                            <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0)):?>      
                            <?php echo $this->translate('by ').
								                  $this->htmlLink($owner->getHref(), $this->string()->truncate($owner->getTitle(),25),array('title' => $owner->getTitle()));?>
                            <?php endif;?>
                          </span>
                      </div>
                   <?php  $i++; ?>
                  <?php endforeach; ?>
                <?php for($i; $i<($this->heightRow *$this->inOneRow_review);$i++):?>
                <div class="featured_thumb_content"></div>
                <?php endfor; ?>
              </div>
           </div>
        </div>
      </div>
      <?php $module = 'Sitestorereview';?>
      <div class="Sitestorecontent_SlideItMoo_forward" id ="Sitestorereview_SlideItMoo_forward">
      	<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$next.png", '', array('align'=>'', 'onMouseOver'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/photo/slider-'.$next.'-active.png";','onMouseOut'=>'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/photo/slider-'.$next.'.png";', 'border'=>'0')) ?>
      </div>
      <div class="Sitestorecontent_SlideItMoo_forward" id="Sitestorereview_SlideItMoo_forward_loding"  style="display: none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0','class'=>'Sitestorecontent_SlideItMoo_loding'));  ?>
      </div>
      <div class="Sitestorecontent_SlideItMoo_forward_disable" id="Sitestorereview_SlideItMoo_forward_disable" style="display:none;cursor:default;">
      	<?php  echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$next-disable.png", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
    </div>
    <div class="clear"></div>
  </li>
</ul>
