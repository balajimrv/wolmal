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

<?php $viewer_id = $this->viewer->getIdentity();?>
<?php if(!empty($viewer_id)):?>
<?php $oldTz = date_default_timezone_get();?>
	<?php date_default_timezone_set($this->viewer->timezone);?>
<?php endif;?>

<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<?php if(empty($this->is_ajax)): ?>
<div class="layout_core_container_tabs">
<div class="tabs_alt tabs_parent">
  <ul id="main_tabs">
    <?php foreach ($this->tabs as $tab): ?>
    <?php $class = $tab->name == $this->activTab->name ? 'active' : '' ?>
      <li class = '<?php echo $class ?>'  id = '<?php echo 'sitestoreoffer_' . $tab->name.'_tab' ?>'>
        <a href='javascript:void(0);'  onclick="tabSwitchSitestoreoffer('<?php echo $tab->name; ?>');"><?php echo $this->translate($tab->getTitle()) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<div id="hideResponse_div" style="display: none;"></div>
<div id="sitestorelbum_offers_tabs">   
   <?php endif; ?>
   <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <?php if($this->is_ajax !=2): ?>
     <ul id="sitestoreoffer_list_tab_offer_content">
       <?php endif; ?>
      <?php foreach( $this->paginator as $coupon ): ?>

        <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $coupon->store_id);?>
        <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $coupon->store_id, $layout);?>
        <li class="sitestore_offer_block">
					<div class="seaocore_browse_list_photo">
						<?php if(!empty($coupon->photo_id)):?>
							<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $coupon->owner_id, 'offer_id' =>  $coupon->offer_id,'tab' => $tab_id,'slug' => $coupon->getOfferSlug($coupon->title)), $this->itemPhoto($coupon, 'thumb.normal'),array('title' => $coupon->getTitle())) ?>
						<?php else:?>
							<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $coupon->owner_id, 'offer_id' =>  $coupon->offer_id,'tab' => $tab_id,'slug' => $coupon->getOfferSlug($coupon->title)), "<img class='thumb_normal' src='". $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/nophoto_offer_thumb_normal.png' alt='' />",array('title' => $coupon->getTitle())) ?>
						<?php endif;?>
					</div>          
					<div class="seaocore_browse_list_info">
						<!--<div class="seaocore_browse_list_info_title">-->
							<div class="seaocore_title">
								<h3><?php echo $this->htmlLink($coupon->getHref(array('tab' => $tab_id)), $this->string()->chunk($coupon->getTitle()),array('title' => $coupon->description));?></h3>
							</div>
            <!--</div>-->
						<div class="seaocore_browse_list_info_date">
							<?php
							$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
							$tmpBody = strip_tags($sitestore_object->title);
							$store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
							?>
							<?php echo $this->translate("in")." ". $this->htmlLink(Engine_Api::_()->sitestore()->getHref($coupon->store_id, $coupon->owner_id, $coupon->getSlug()),  $store_title,array('title' => $sitestore_object->title)) ?>      
						</div>
						<div class="seaocore_browse_list_info_date">
							<?php if( $this->activTab->name == 'viewed_storeoffers' ): ?>
								<?php echo $this->translate(array('%s view', '%s views', $coupon->view_count), $this->locale()->toNumber($coupon->view_count)) ?>
							<?php elseif( $this->activTab->name == 'commented_storeoffers' ): ?>
								<?php echo $this->translate(array('%s comment', '%s comments', $coupon->comment_count), $this->locale()->toNumber($coupon->comment_count)) ?>
							<?php elseif( $this->activTab->name == 'liked_storeoffers' ): ?>
								<?php echo $this->translate(array('%s like', '%s likes', $coupon->like_count), $this->locale()->toNumber($coupon->like_count)) ?>
							<?php endif; ?>
						</div>            

						<div class="sitestore_offer_date">
							<?php if(in_array('startdate', $this->statistics)):?>
                <div class="sitestore_offer_date">
                  <span><?php echo $this->translate('Start date') . ':'; ?></span>
                  <span><?php echo $this->timestamp(strtotime($coupon->start_time)) ?></span>
                </div>
              <?php endif;?>
              
              <?php if(in_array('enddate', $this->statistics)):?>
                <div class="sitestore_offer_date">
                  <span><?php echo $this->translate('End date'). ':'; ?></span>
                  <?php if($coupon->end_settings == 1):?><span><?php echo $this->timestamp(strtotime($coupon->end_time)) ?></span>		<?php else:?><span><?php echo $this->translate('Never Expires');?></span><?php endif;?>
                </div>
              <?php endif;?>
                
          		<?php if(in_array('minpurchase', $this->statistics) && !empty($coupon->minimum_purchase)):?>
                <div class="sitestore_offer_date">
                  <span><?php echo $this->translate('Minimum Purchase'). ':';?></span>
                  <span><?php echo $coupon->minimum_purchase;?></span>
                </div>
              <?php endif;?>
              
              <?php if(in_array('couponurl', $this->statistics)):?>
                <?php if(!empty($this->enable_url) && !empty($coupon->url)):?>
                  <div class="sitestore_offer_date">
                    <span><?php echo $this->translate('URL:');?></span>
                    <?php $tempUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $coupon->url; ?>
                    <span><a href="<?php echo $tempUrl; ?>" target="_blank" title="<?php echo $tempUrl; ?>"><?php echo $tempUrl; ?></a></span>
                  </div>
                <?php endif;?>
              <?php endif;?>
            </div>
            
            <div class="sitestore_offer_stats fleft">
              <?php if(in_array('couponcode', $this->statistics)):?>
                <span class="sitestore_offer_stat sitestorecoupon_code sitestorecoupon_tip_wrapper">
                  <span class="sitestorecoupon_tip">
                    <span><?php echo $this->translate('Select and Copy Code to use');?></span>
                    <i></i>
                  </span>
                  <input type="text" value="<?php echo $coupon->coupon_code;?>" class="sitestorecoupon_code_num" onclick="this.select()" readonly>
                </span>
              <?php endif;?>
              
              <span class="sitestore_offer_stat sitestore_offer_discount sitestorecoupon_tip_wrapper"> 
                <?php if(in_array('discount', $this->statistics)):?>
                	<span class="sitestorecoupon_tip">
                    <span><?php echo $this->translate('Coupon Discount Value');?></span>
                    <i></i>
                  </span>
                  <?php if(!empty($coupon->discount_type)):
                    $priceStr = Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($coupon->discount_amount);?>
                    <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                  <?php else:?>
                  <span class="discount_value"><?php echo $coupon->discount_amount . '%';?></span>&nbsp;&nbsp;
                <?php endif;?>
                <?php endif;?>
              </span>
            </div>
            <?php $today = date("Y-m-d H:i:s"); ?>
            <?php if(in_array('expire', $this->statistics) && !empty($coupon->end_settings) && $coupon->end_time < $today):?>
                  <span class="sitestorecoupon_stat sitestorecoupon_left fright"><b><?php echo $this->tranlsate('Expired');?></b></span>
                <?php// endif;?>
            <?php elseif(in_array('claim', $this->statistics)):?>
             <?php //echo '<span class="sitestorecoupon_stat sitestorecoupon_left fright">' .$coupon->claimed.' '.$this->translate('Used') . '</span>'; ?>
              <?php if($coupon->claim_count != -1):?> 
                <?php $coupon->claim_count  = $coupon->claim_count - $coupon->claimed ;?>
                <span class="sitestorecoupon_stat sitestorecoupon_left fright">
                  <?php //echo $this->translate(array('%1$s Left', '%1$s Left', $coupon->claim_count), $this->locale()->toNumber($coupon->claim_count)) ?>
                  
                  <?php if($coupon->claim_count == 1) : ?>
                   <?php echo $this->translate('%1$s coupon left', $this->locale()->toNumber($coupon->claim_count)) ?>
                  <?php else : ?>
                   <?php echo $this->translate('%1$s coupons left', $this->locale()->toNumber($coupon->claim_count)) ?>
                  <?php endif;?>
                </span>
              <?php else : ?>
                <span class="sitestorecoupon_stat sitestorecoupon_left fright"><?php echo $this->translate('Unlimited Use') ?></span>
              <?php endif;?>
            <?php endif;?> 
            
            <div class="sitestore_offer_stats">
            	<?php $description = strip_tags($coupon->description);?>
              <?php   if (!empty($description)):?>
                <?php $truncate_description = ( Engine_String::strlen($description) > 110 ? Engine_String::substr($description, 0, 110) . '...' : $description );?>
                <?php if(Engine_String::strlen($description) > 110):?>
                  <?php $truncate_description .= $this->htmlLink($coupon->getHref(array('tab' => $tab_id)), $this->translate('More &raquo;'));?>
                <?php endif;?>
                <?php echo $truncate_description;?>
              <?php endif;?>
             </div>
						</div> 
            </li>
      <?php endforeach;?>
            </ul>
       <?php if($this->is_ajax !=2): ?>
  </div>
      <?php endif; ?>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No coupons have been created yet.');?>
      </span>
    </div>
  <?php endif; ?>   
<?php if(empty($this->is_ajax)): ?>    
</div>
<?php if (!empty($this->showViewMore)): ?>
<div class="seaocore_view_more" id="sitestoreoffer_offers_tabs_view_more" onclick="viewMoreTabOffer()">
  <?php
  echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
      'id' => 'feed_viewmore_link',
      'class' => 'buttonlink icon_viewmore'
  ))
  ?>
</div>
<div class="seaocore_loading" id="sitestoreoffer_offers_tabs_loding_image" style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="" />
  <?php echo $this->translate("Loading ...") ?>
</div>
<?php endif; ?>
<?php endif; ?>
<?php if(!empty($viewer_id)):?>
	<?php date_default_timezone_set($oldTz);?>
<?php endif;?>
<?php if(empty($this->is_ajax)): ?>
<script type="text/javascript">
  
  var tabSwitchSitestoreoffer = function (tabName) {
 <?php foreach ($this->tabs as $tab): ?>
  if($('<?php echo 'sitestoreoffer_'.$tab->name.'_tab' ?>'))
        $('<?php echo 'sitestoreoffer_' .$tab->name.'_tab' ?>').erase('class');
  <?php  endforeach; ?>

 if($('sitestoreoffer_'+tabName+'_tab'))
        $('sitestoreoffer_'+tabName+'_tab').set('class', 'active');
   if($('sitestorelbum_offers_tabs')) {
      $('sitestorelbum_offers_tabs').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/loader.gif" class="sitestore_tabs_loader_img" /></center>';
    }   
    if($('sitestoreoffer_offers_tabs_view_more'))
    $('sitestoreoffer_offers_tabs_view_more').style.display =  'none';
    var request = new Request.HTML({
     method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitestoreoffer/name/list-offers-tabs-view',
      'data' : {
        format : 'html',
        isajax : 1,
        category_id : '<?php echo $this->category_id?>',
        tabName: tabName,
        margin_photo : '<?php echo $this->marginPhoto ?>'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('sitestorelbum_offers_tabs').innerHTML = responseHTML;
            <?php if(!empty ($this->showViewMore)): ?>
              hideViewMoreLinkSitestoreOfferOffer();
             <?php endif; ?> 
      }
    });

    request.send();
  }
</script>
<?php endif; ?>
<?php if(!empty ($this->showViewMore)): ?>
<script type="text/javascript">
    en4.core.runonce.add(function() {
    hideViewMoreLinkSitestoreOfferOffer();  
    });
    function getNextPageSitestoreOfferOffer(){
      return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }
    function hideViewMoreLinkSitestoreOfferOffer(){
      if($('sitestoreoffer_offers_tabs_view_more'))
        $('sitestoreoffer_offers_tabs_view_more').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
    }
        
    function viewMoreTabOffer()
  {
    $('sitestoreoffer_offers_tabs_view_more').style.display ='none';
    $('sitestoreoffer_offers_tabs_loding_image').style.display ='';
    en4.core.request.send(new Request.HTML({
      method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitestoreoffer/name/list-coupons-tabs-view',
      'data' : {
        format : 'html', 
        isajax : 2,
        category_id : '<?php echo $this->category_id?>',
        tabName : '<?php echo $this->activTab->name ?>',
        margin_photo : '<?php echo $this->marginPhoto ?>',
        store: getNextPageSitestoreOfferOffer()
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {   
        $('hideResponse_div').innerHTML=responseHTML;      
        var photocontainer = $('hideResponse_div').getElement('.layout_sitestoreoffer_list_offers_tabs_view').innerHTML;
        $('sitestoreoffer_list_tab_offer_content').innerHTML = $('sitestoreoffer_list_tab_offer_content').innerHTML + photocontainer;
        $('sitestoreoffer_offers_tabs_loding_image').style.display ='none';
        $('hideResponse_div').innerHTML="";        
      }
    }));

    return false;

  }  
</script>
<?php endif; ?>

<script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>
