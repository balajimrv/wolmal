<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
  	              . 'application/modules/Seaocore/externals/styles/styles.css');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postedby', 0);?>
<script language="javascript" type="text/javascript">
  var urlhome="<?php echo $this->url(array(),"sitestore_homesponsored",true); ?>";
</script>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/slideitmoo-1.1_full_source.js');
?>
<a id="group_profile_members_anchor" style="position:absolute;"></a>
<script language="javascript" type="text/javascript"> 
  var total = '<?php echo $this->totalCount; ?>';

  var forward_link;
  var fwdbck_click  = 1;
 
  var limit='<?php echo $this->limit * 2; ?>';
  var curnt_limit='<?php echo $this->limit; ?>';
  var category_id='<?php echo $this->category_id; ?>';
  var titletruncation='<?php echo $this->titletruncation; ?>';
  var startindex = -1;
  var call_count = 1;
</script>
<script language="javascript" type="text/javascript">
  var slideshow;
  window.addEvents ({
    'domready': function() {
      slideshow = new SlideItMoo({
        overallContainer: 'SlideItMoo_outer',
        elementScrolled: 'SlideItMoo_inner',
        thumbsContainer: 'SlideItMoo_items',
        itemsVisible:curnt_limit,
        elemsSlide:curnt_limit,
        duration:8000,
        itemsSelector: '.SlideItMoo_element',
        itemWidth:132,
        showControls:1,
        startIndex:1,
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) { call_count = 1;
        }
      });

      $('SlideItMoo_back').addEvent('click', function () {slideshow.sendajax(-1,slideshow);
        call_count = 1;

      });

      $('SlideItMoo_forward').addEvent('click', function () { slideshow.sendajax(1,slideshow);
        call_count = 1;
      });

       
      if((total-curnt_limit)<=0){
        // hidding forward button
        document.getElementById('SlideItMoo_forward').style.display= 'none';
        document.getElementById('SlideItMoo_back_dis').style.display= 'none';
      }
    }
  });
  var obj_sliditmoo;
  function custom_sliditmoo (obj, direction) {


    obj_sliditmoo = new SlideItMoo({
      overallContainer: 'SlideItMoo_outer',
      elementScrolled: 'SlideItMoo_inner',
      thumbsContainer: 'SlideItMoo_items',
      itemsVisible:curnt_limit,
      elemsSlide:curnt_limit,
      duration:<?php echo $this->interval; ?>,
      itemsSelector: '.SlideItMoo_element',
      itemWidth:132,
      showControls:1,
      startIndex:1,
      onChange: function(index) { call_count = 1;
      }
    });

    obj_sliditmoo.slide(direction, obj_sliditmoo);

    if(startindex<=0 && direction== -1){
      // hidding back button
      document.getElementById('SlideItMoo_back').style.display= 'none';
      document.getElementById('SlideItMoo_back_dis').style.display= 'block';
    }else{
      // vissible back button
      document.getElementById('SlideItMoo_back').style.display= 'block';
      document.getElementById('SlideItMoo_back_dis').style.display= 'none';

    }

    if(((startindex>(total-limit)|| (startindex>=(total-limit))) && direction== 1) || ((startindex>=(total-curnt_limit)) && direction==  -1)){
      // hidding forward button
      document.getElementById('SlideItMoo_forward').style.display= 'none';
      document.getElementById('SlideItMoo_forward_dis').style.display= 'block';

    }else{
      // vissible forward button
      document.getElementById('SlideItMoo_forward').style.display= 'block';
      document.getElementById('SlideItMoo_forward_dis').style.display= 'none';
    }
    fwdbck_click = 1;
  }
</script>
<ul class="seaocore_sponsored_widget">
  <li>
    <?php $sitestore_advsitestore = Zend_Registry::isRegistered('sitestore_advsitestore') ? Zend_Registry::get('sitestore_advsitestore') : null; ?>
    <div id="SlideItMoo_outer" class="seaocore_sponsored_carousel">
      <div class="seaocore_sponsored_carousel_back" id="SlideItMoo_back" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_carousel_up.png', '', array('align' => '', 'onMouseOver' => 'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/sitestore_carousel_up_hover.png";', 'onMouseOut' => 'this.src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestore/externals/images/sitestore_carousel_up.png";', 'border' => '0')) ?>
      </div>
       <div class="seaocore_sponsored_carousel_back" id="SlideItMoo_back_loding" style="display:none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div class="seaocore_sponsored_carousel_back_dis" id="SlideItMoo_back_dis" style="display:block;">
        <?php if (!empty($sitestore_advsitestore)) {
          echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_carousel_up_dis.png', '', array('align' => '', 'border' => '0'));
        } else {
          return;
        } ?>
      </div>
      <div id="SlideItMoo_inner" class="seaocore_sponsored_carousel_inner">
        <div id="SlideItMoo_items" class="seaocore_sponsored_carousel_item_list">
          <?php foreach ($this->sitestores as $sitestore): ?>
            <div class="SlideItMoo_element seaocore_sponsored_carousel_items">
              <div class="seaocore_sponsored_carousel_items_thumb">
                  <?php echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), $this->itemPhoto($sitestore, 'thumb.icon', $sitestore->getTitle()), array('rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
              </div>
              <div class="seaocore_sponsored_carousel_items_info">
                <div class="seaocore_sponsored_carousel_items_title">
									<?php
									$item_title = Engine_Api::_()->sitestore()->truncation($sitestore->getTitle(), $this->titletruncation);
									echo $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $sitestore->getSlug()), $item_title, array('title' => $sitestore->getTitle()))
									?>
                </div>  
                <?php if($postedBy):?>
                  <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
                    <?php echo $this->translate('posted by'); ?>
                    <?php echo $this->htmlLink($sitestore->getOwner()->getHref(), Engine_Api::_()->sitestore()->truncation($sitestore->getOwner()->getTitle(), 100), array('title' => $sitestore->getOwner()->getTitle())) ?>
                  </div>	
                <?php endif;?>
              </div>
            </div>	                  
          <?php endforeach; ?>
        </div>
      </div>
      <div class="seaocore_sponsored_carousel_forward" id ="SlideItMoo_forward">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_carousel_down.png', '', array('align' => '', 'onMouseOver' => 'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_carousel_down_hover.png";', 'onMouseOut' => 'this.src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_carousel_down.png";', 'border' => '0')) ?>
      </div>

      <div class="seaocore_sponsored_carousel_forward" id="SlideItMoo_forward_loding"  style="display: none;">
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align'=>'', 'border'=>'0'));  ?>
      </div>
      <div class="seaocore_sponsored_carousel_forward_dis" id="SlideItMoo_forward_dis" style="display:none;">
        <?php if (!empty($sitestore_advsitestore)) {
          echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_carousel_down_dis.png', '', array('align' => '', 'border' => '0'));
        } else {
          exit();
        } ?>
      </div>
    </div>
    <div class="clr"></div>
  </li>
</ul>