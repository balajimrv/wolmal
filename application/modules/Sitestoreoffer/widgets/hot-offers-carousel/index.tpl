<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/sitestore_featured_carousel.css');

$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitestoreoffer/externals/styles/style_sitestoreoffer.css');

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/scripts/sitestoreslideitmoo-1.1_full_source.js');
?>

<?php $viewer_id = $this->viewer->getIdentity(); ?>
<?php if (!empty($viewer_id)): ?>
  <?php date_default_timezone_set($this->viewer->timezone); ?>
<?php endif; ?>

<a id="group_profile_members_anchor" style="position:absolute;"></a>
<script language="javascript" type="text/javascript">

  var module = 'Sitestoreoffer';
</script>
<script language="javascript" type="text/javascript">
  var slideshowoffer;
  window.addEvents({
    'domready': function() {
      slideshowoffer = new SocialengineSlideItMoo({
        fwdbck_click: 1,
        slide_element_limit: 1,
        startindex: -1,
        in_one_row:<?php echo $this->inOneRow_offer ?>,
        no_of_row:<?php echo $this->noOfRow_offer ?>,
        curnt_limit:<?php echo $this->totalItemShowoffer; ?>,
        category_id:<?php echo $this->category_id; ?>,
        total:<?php echo $this->totalCount_offer; ?>,
        limit:<?php echo $this->totalItemShowoffer * 2; ?>,
        module: 'Sitestoreoffer',
        call_count: 1,
        foward: 'Sitestoreoffer_SlideItMoo_forward',
        bck: 'Sitestoreoffer_SlideItMoo_back',
        overallContainer: 'Sitestoreoffer_SlideItMoo_outer',
        elementScrolled: 'Sitestoreoffer_SlideItMoo_inner',
        thumbsContainer: 'Sitestoreoffer_SlideItMoo_items',
        slideVertical: <?php echo $this->vertical ?>,
        itemsVisible: 1,
        elemsSlide: 1,
        duration:<?php echo $this->interval; ?>,
        itemsSelector: '.Sitestoreoffer_SlideItMoo_element',
        itemWidth:<?php echo 146 * $this->inOneRow_offer ?>,
        itemHeight:<?php echo 146 * $this->noOfRow_offer ?>,
        showControls: 1,
        startIndex: 1,
        navs: {/* starting this version, you'll need to put your back/forward navigators in your HTML */
          fwd: '.Sitestoreoffer_SlideItMoo_forward', /* forward button CSS selector */
          bk: '.Sitestoreoffer_SlideItMoo_back' /* back button CSS selector */
        },
        transition: Fx.Transitions.linear, /* transition */
        onChange: function(index) {
          slideshowoffer.options.call_count = 1;
        }
      });

      $('Sitestoreoffer_SlideItMoo_back').addEvent('click', function() {
        slideshowoffer.sendajax(-1, slideshowoffer, 'Sitestoreoffer', "<?php echo $this->url(array('module' => 'sitestoreoffer', 'controller' => 'index', 'action' => 'hot-coupons-carousel'), 'default', true); ?>");
        slideshowoffer.options.call_count = 1;

      });

      $('Sitestoreoffer_SlideItMoo_forward').addEvent('click', function() {
        slideshowoffer.sendajax(1, slideshowoffer, 'Sitestoreoffer', "<?php echo $this->url(array('module' => 'sitestoreoffer', 'controller' => 'index', 'action' => 'hot-coupons-carousel'), 'default', true); ?>");
        slideshowoffer.options.call_count = 1;
      });

      if ((slideshowoffer.options.total - slideshowoffer.options.curnt_limit) <= 0) {
        // hidding forward button
        document.getElementById('Sitestoreoffer_SlideItMoo_forward').style.display = 'none';
        document.getElementById('Sitestoreoffer_SlideItMoo_back_disable').style.display = 'none';
      }
    }
  });
</script>
<?php $viewer_id = $this->viewer->getIdentity(); ?>
<?php
$offerSettings = array();
$offerSettings['class'] = 'thumb';
?>
<ul class="Sitestorecontent_featured_slider">
  <li>
    <?php
    $module = 'Sitestoreoffer';
    $extra_width = 0;
    $extra_height = 0;
    if (empty($this->vertical)):
      $typeClass = 'horizontal';
      if ($this->totalCount_offer > $this->totalItemShowoffer):
        $extra_width = 60;
      endif;
      $prev = 'back';
      $next = 'forward';
    else:
      $typeClass = 'vertical';
      if ($this->totalCount_offer > $this->totalItemShowoffer):
        $extra_height = 50;
      endif;
      $prev = 'up';
      $next = 'down';
    endif;
    ?>
    <div id="Sitestoreoffer_SlideItMoo_outer" class="Sitestorecontent_SlideItMoo_outer Sitestorecontent_SlideItMoo_outer_<?php echo $typeClass; ?>" style="height:<?php echo 146 * $this->heightRow + $extra_height; ?>px; width:<?php echo (146 * $this->inOneRow_offer) + $extra_width; ?>px;">
      <div class="Sitestorecontent_SlideItMoo_back" id="Sitestoreoffer_SlideItMoo_back" style="display:none;">
<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$prev.png", '', array('align' => '', 'onMouseOver' => 'this.src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/photo/slider-' . $prev . '-active.png";', 'onMouseOut' => 'this.src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/photo/slider-' . $prev . '.png";', 'border' => '0')) ?>
      </div>
      <div class="Sitestorecontent_SlideItMoo_back" id="Sitestoreoffer_SlideItMoo_back_loding" style="display:none;">
<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align' => '', 'border' => '0', 'class' => 'Sitestorecontent_SlideItMoo_loding')); ?>
      </div>      
      <div class="Sitestorecontent_SlideItMoo_back_disable" id="Sitestoreoffer_SlideItMoo_back_disable" style="display:block;cursor:default;">
<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$prev-disable.png", '', array('align' => '', 'border' => '0')); ?>
      </div>
      <div id="Sitestoreoffer_SlideItMoo_inner" class="Sitestorecontent_SlideItMoo_inner">
        <div id="Sitestoreoffer_SlideItMoo_items" class="Sitestorecontent_SlideItMoo_items" style="height:<?php echo 146 * $this->heightRow; ?>px;">
          <div class="Sitestorecontent_SlideItMoo_element Sitestoreoffer_SlideItMoo_element" style="width:<?php echo 146 * $this->inOneRow_offer; ?>px;">
            <div class="Sitestorecontent_SlideItMoo_contentList">
              <?php $i = 0; ?>
              <?php foreach ($this->hotOffers as $coupon): ?>
                <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $coupon->store_id); ?>
                <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
                $tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $coupon->store_id, $layout);
                ?>
                <div class="featured_thumb_content">
  <?php if (!empty($coupon->photo_id)): ?>
                    <a class="thumb_img" href="<?php echo $coupon->getHref(array('route' => 'sitestoreoffer_view', 'user_id' => $coupon->owner_id, 'offer_id' => $coupon->offer_id, 'tab' => $tab_id, 'slug' => $coupon->getOfferSlug($coupon->title))); ?>">
                      <span style="background-image: url(<?php echo $coupon->getPhotoUrl('thumb.normal'); ?>);"></span>
                    </a>
  <?php else: ?>
                    <a class="thumb_img" href="<?php echo $coupon->getHref(array('store_id' => $coupon->store_id, 'offer_id' => $coupon->offer_id, 'slug' => $coupon->getOfferSlug($coupon->title), 'tab' => $tab_id)); ?>">
                      <span style="background-image: url('<?php echo $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/nophoto_offer_thumb_normal.png" ?>');"></span>
                    </a>
                    <?php endif; ?>
                  <span class="show_content_des">
                    <?php
                    $owner = $coupon->getOwner();
                    echo
                    $this->htmlLink($coupon->getHref(array('tab' => $tab_id)), $this->string()->chunk($coupon->getTitle()), array('title' => $coupon->description));
                    ?>
                    <?php
                    $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
                    $tmpBody = strip_tags($sitestore_object->title);
                    $store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
                    ?>
                    <?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($coupon->store_id, $coupon->owner_id, $coupon->getSlug()), $store_title, array('title' => $sitestore_object->title)) ?>
                    <?php $today = date("Y-m-d H:i:s"); ?>
                    <?php $claim_value = Engine_Api::_()->getDbTable('claims', 'sitestoreoffer')->getClaimValue($this->viewer_id, $coupon->offer_id, $coupon->store_id); ?>
                    <?php //if($coupon->claim_count == -1 && ($coupon->end_time > $today || $coupon->end_settings == 0)):?>
                    <?php //$show_offer_claim = 1;?>
                    <?php //elseif($coupon->claim_count > 0 && ($coupon->end_time > $today || $coupon->end_settings == 0)):?>
                    <?php //$show_offer_claim = 1;?>
                    <?php //else:?>
                    <?php //$show_offer_claim = 0; ?>
                    <?php //endif;?>
                    <!--<div class="sitestore_offer_date seaocore_txt_light" style="margin:3px 0 0;">-->
                    <?php //if(!empty($show_offer_claim) && empty($claim_value)):?>
                    <?php
                    //$request = Zend_Controller_Front::getInstance()->getRequest();
                    //$urlO = $request->getRequestUri();
                    //$request_url = explode('/',$urlO);
                    //$param = 1;
                    //if(empty($request_url['2'])) {
                    //$param = 0;
                    //}
                    //$return_url = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://":"http://";
                    //$currentUrl = urlencode($urlO);
                    ?>
                        <!--<span>-->
                    <?php //if(!empty($this->viewer_id)):?>
                    <?php //echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestoreoffer/externals/images/invite.png" alt="" class="get_offer_icon" />'.$this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'getoffer', 'id' => $coupon->offer_id),$this->translate('Get Coupon'),array('onclick' => 'owner(this);return false'));
                    ?>
                    <?php //else:?>
                    <?php
                    //$offer_tabinformation = $this->url(array( 'action' => 'getoffer', 'id' => $coupon->offer_id,'param' => $param,'request_url'=>$request_url['1']), 'sitestoreoffer_general')."?"."return_url=".$return_url.$_SERVER['HTTP_HOST'].$currentUrl;
                    //$title = $this->translate('Get Coupon');
                    //echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestoreoffer/externals/images/invite.png" alt="" class="get_offer_icon" />'."<a href=$offer_tabinformation>$title</a>";
                    ?>
                    <?php //endif; ?>
                  </span>	
                    <?php //elseif(!empty($claim_value) && !empty($show_offer_claim) || ($coupon->claim_count == 0 && $coupon->end_time > $today && !empty($claim_value))): ?>
                  <span>
  <?php //echo '<img src="'.$this->layout()->staticBaseUrl.'application/modules/Sitestoreoffer/externals/images/invite.png" alt="" class="get_offer_icon" style="margin-top:1px;" />'.$this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'resendoffer', 'id' => $coupon->offer_id),Zend_Registry::get('Zend_Translate')->_('Resend Coupon'),array('onclick' => 'owner(this);return false')); ?>
                    <!--</span>	-->
                    <?php //else: ?>
                    <!--<span>-->
                    <b><?php //echo $this->translate('Expired'); ?></b>
                    <!--</span>	-->
                  <?php //endif;?>
                </div> 
                <div class="sitestore_offer_date">
                  <?php if (in_array('startdate', $this->statistics)): ?>
                    <div class="sitestore_offer_date">
                      <span><?php echo $this->translate('Start date') . ':'; ?></span>
                      <span><?php echo $this->timestamp(strtotime($coupon->start_time)) ?></span>
                    </div>
                  <?php endif; ?>
                  
                  <?php if (in_array('enddate', $this->statistics)): ?>
                    <div class="sitestore_offer_date">
                      <span><?php echo $this->translate('End date') .  ':'; ?></span>
                      <?php if(!empty($coupon->end_settings)):?>
                        <span><?php echo $this->timestamp(strtotime($coupon->end_time)) ?></span>
                      <?php else:?>
                        <span><?php echo $this->translate('Never Expires') ?></span>
                      <?php endif; ?>
                    </div>
                  <?php endif;?>
                  
                  <?php if (in_array('minpurchase', $this->statistics) && !empty($coupon->minimum_purchase)): ?>
                    <div class="sitestore_offer_date">
                      <span><?php echo $this->translate('Minimum Purchase'). ':';?></span>
                      <span><?php echo $coupon->minimum_purchase;?></span>
                    </div>
                  <?php endif; ?>
                  
                  <?php if (in_array('couponurl', $this->statistics)): ?>
                    <?php if (!empty($this->enable_url) && !empty($coupon->url)): ?>
                    	<div class="sitestore_offer_date">
                        <span><?php echo $this->translate('URL'). ':'; ?></span>
                        <span><a href="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $coupon->url; ?>" target="_blank" title="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $coupon->url ?>"><?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $coupon->url ?></a></span>
                      </div>
                    <?php endif; ?>
                  <?php endif; ?>
               	</div>
                
                <div class="sitestore_offer_stats fleft">
                  <?php if (in_array('couponcode', $this->statistics)): ?>
                    <span class="sitestore_offer_stat sitestorecoupon_code sitestorecoupon_tip_wrapper">
                      <span class="sitestorecoupon_tip">
                        <span><?php echo $this->translate('Select and Copy Code to use');?></span>
                        <i></i>
                      </span>
                      <input type="text" value="<?php echo $coupon->coupon_code; ?>" class="sitestorecoupon_code_num" onclick="this.select()" readonly>
                    </span>
                  <?php endif; ?>
                  
                  <span class="sitestore_offer_stat sitestore_offer_discount sitestorecoupon_tip_wrapper"> 
                    <?php if (in_array('discount', $this->statistics)): ?>
                      <span class="sitestorecoupon_tip">
                        <span><?php echo $this->translate('Coupon Discount Value');?></span>
                        <i></i>
                      </span>
                      <?php
                      if (!empty($coupon->discount_type)):
                        $priceStr = Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($coupon->discount_amount);?>
                        <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                      <?php else: ?>
                        <span class="discount_value"><?php echo $coupon->discount_amount . '%'; ?></span>&nbsp;&nbsp;
                      <?php endif; ?>
                    <?php endif; ?>
                  </span>
                </div>
                <?php $today = date("Y-m-d H:i:s"); ?>
                <?php if(in_array('expire', $this->statistics) && !empty($coupon->end_settings) && $coupon->end_time < $today):?>
                      <span class="sitestorecoupon_stat sitestorecoupon_left fright"><b><?php echo 'Expired';?></b></span>
                <?php //endif;?>
                <?php elseif(in_array('claim', $this->statistics)):?>
                  <?php //echo '<span class="sitestorecoupon_stat sitestorecoupon_left fright">' .$coupon->claimed.' '.$this->translate('Used') . '</span>'; ?>
                  <?php if ($coupon->claim_count != -1): ?>
                    <?php $coupon->claim_count  = $coupon->claim_count - $coupon->claimed ;?>
                    <span class="sitestorecoupon_stat sitestorecoupon_left fright">
                      <?php //echo $this->translate(array('%1$s Left', '%1$s Left', $coupon->claim_count), $this->locale()->toNumber($coupon->claim_count)) ?>
                      
                      <?php if($coupon->claim_count == 1) : ?>
                        <?php echo $this->translate(array('%1$s coupon left', '%1$s coupon left', $coupon->claim_count), $this->locale()->toNumber($coupon->claim_count)) ?>
                       <?php else : ?>
                        <?php echo $this->translate(array('%1$s coupons left', '%1$s coupons left', $coupon->claim_count), $this->locale()->toNumber($coupon->claim_count)) ?>
                       <?php endif;?>
                     </span>
                   <?php else : ?>
                     <span class="sitestorecoupon_stat sitestorecoupon_left fright"><?php echo $this->translate('Unlimited Use') ?></span>	
                  <?php endif; ?>
                <?php endif;?>
              </div>
          <?php $i++; ?>
        <?php endforeach; ?>
<?php for ($i; $i < ($this->heightRow * $this->inOneRow_offer); $i++): ?>
              <div class="featured_thumb_content"></div>
    <?php endfor; ?>
          </div>
        </div>
      </div>
    </div>
      <?php $module = 'Sitestoreoffer'; ?>
    <div class="Sitestorecontent_SlideItMoo_forward" id ="Sitestoreoffer_SlideItMoo_forward">
      <?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$next.png", '', array('align' => '', 'onMouseOver' => 'this.src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/photo/slider-' . $next . '-active.png";', 'onMouseOut' => 'this.src="' . $this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/photo/slider-' . $next . '.png";', 'border' => '0')) ?>
    </div>
    <div class="Sitestorecontent_SlideItMoo_forward" id="Sitestoreoffer_SlideItMoo_forward_loding"  style="display: none;">
<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/images/core/loading.gif", '', array('align' => '', 'border' => '0', 'class' => 'Sitestorecontent_SlideItMoo_loding')); ?>
    </div>
    <div class="Sitestorecontent_SlideItMoo_forward_disable" id="Sitestoreoffer_SlideItMoo_forward_disable" style="display:none;cursor:default;">
<?php echo $this->htmlImage($this->layout()->staticBaseUrl . "application/modules/Sitestore/externals/images/photo/slider-$next-disable.png", '', array('align' => '', 'border' => '0')); ?>
    </div>
    </div>
    <div class="clear"></div>
  </li>
</ul>
<?php if (!empty($viewer_id)): ?>
  <?php date_default_timezone_set($oldTz); ?>
<?php endif; ?>
<script type="text/javascript" >
  function owner(thisobj) {
    var Obj_Url = thisobj.href;
    Smoothbox.open(Obj_Url);
  }
</script>
