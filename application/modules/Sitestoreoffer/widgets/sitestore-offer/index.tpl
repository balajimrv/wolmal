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
<?php if($this->paginator->getTotalItemCount()):?>
  <form id='filter_form_store' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitestoreoffer_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="store" name="store"  value=""/>
  </form>
	<div class='layout_middle'>
		<ul class="seaocore_browse_list">
			<?php foreach ($this->paginator as $sitestore): ?>
				<li>
					<div class="seaocore_browse_list_photo"> 
          <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $sitestore->store_id);?>
					<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
									$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $sitestore->store_id, $layout);?>
          <?php if(!empty($sitestore->photo_id)):?>
						<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $sitestore->owner_id, 'offer_id' =>  $sitestore->offer_id,'tab' => $tab_id,'slug' => $sitestore->getOfferSlug($sitestore->title)), $this->itemPhoto($sitestore, 'thumb.normal'),array('title' => $sitestore->getTitle())) ?>
					<?php else:?>
						<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $sitestore->owner_id, 'offer_id' =>  $sitestore->offer_id,'tab' => $tab_id,'slug' => $sitestore->getOfferSlug($sitestore->title)), "<img class='thumb_normal' src='". $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/nophoto_offer_thumb_normal.png' alt='' />",array('title' => $sitestore->getTitle())) ?>
					<?php endif;?>
						</div>
					<div class='seaocore_browse_list_info'>
						<div>
            <div class="seaocore_title">
              <h3>
                <span>
                  <?php if (!empty($sitestore->hotoffer)):?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/icons/hot-offer.png', '', array('class' => 'icon', 'title' => $this->translate('Hot Coupon'))) ?>
                  <?php endif;?>
                  <?php if (($sitestore->price>0)): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                  <?php endif; ?>
                  <?php if ($sitestore->sticky == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/sitestore_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                  <?php endif; ?>
                </span>
              	<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $sitestore->owner_id, 'offer_id' =>  $sitestore->offer_id,'tab' => $tab_id,'slug' => $sitestore->getOfferSlug($sitestore->title)), $sitestore->title,array('title' => $sitestore->description)); ?></h3>
              </div>
						</div>
						<div class="seaocore_browse_list_info_date">
							<?php $item = Engine_Api::_()->getItem('sitestore_store', $sitestore->store_id); ?>
							<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $item->getSlug()),  $sitestore->sitestore_title) ?>
						</div>

						<div class="sitestore_offer_date">
							<?php if(in_array('startdate', $this->statistics)):?>
                <div class="sitestore_offer_date">
                  <span><?php echo $this->translate('Start date') . ":"; ?></span>
                  <span><?php echo $this->timestamp(strtotime($sitestore->start_time)) ?></span>
                </div>
          		<?php endif;?>
              
       				<?php if(in_array('enddate', $this->statistics)):?>
                <div class="sitestore_offer_date">
                  <span><?php echo $this->translate('End date'). ":"; ?></span>
                  <?php if($sitestore->end_settings == 1):?>
                    <span><?php echo $this->timestamp(strtotime($sitestore->end_time)) ?></span>
                  <?php else:?>
                    <span><?php echo $this->translate('Never Expires');?></span>
                  <?php endif;?>
                </div>
          		<?php endif;?>
          		
              <?php if(in_array('minpurchase', $this->statistics) && !empty($sitestore->minimu_purchase)):?>
                <div class="sitestore_offer_date">
                  <span><?php echo $this->translate('Minimum Purchase') . ":";?></span>
                  <span><?php echo $sitestore->minimu_purchase;?></span>
                </div>
              <?php endif;?>
              
              <?php if(in_array('couponurl', $this->statistics)):?>
                <?php if(!empty($this->enable_url) && !empty($sitestore->url)):?>
                <div class="sitestore_offer_date">
                  <span><?php echo $this->translate('URL') . ":";?></span>
                  <span><a href="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $sitestore->url; ?>" target="_blank" title="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $sitestore->url ?>"><?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $sitestore->url ?></a>
                  </span>
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
                  <input type="text" value="<?php echo $sitestore->coupon_code;?>" class="sitestorecoupon_code_num" onclick="this.select()" readonly>
                </span>
              <?php endif;?>
              
            <span class="sitestore_offer_stat sitestore_offer_discount sitestorecoupon_tip_wrapper">
              <?php if(in_array('discount', $this->statistics)):?>
                <span class="sitestorecoupon_tip">
                  <span><?php echo $this->translate('Coupon Discount Value');?></span>
                  <i></i>
                </span>
                <?php if(!empty($sitestore->discount_type)):
                $priceStr = Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($sitestore->discount_amount);?>
                  <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                <?php else:?>
                <span class="discount_value"><?php echo $sitestore->discount_amount . '%';?></span>&nbsp;&nbsp;
              <?php endif;?>
              <?php endif;?>
            </span>
          </div>
  
						<div class="sitestore_offer_date "> 
              <?php $today = date("Y-m-d H:i:s"); ?>
              <?php if(in_array('expire', $this->statistics) && !empty($sitestore->end_settings) && $sitestore->end_time < $today):?>
                    <span class="sitestorecoupon_stat sitestorecoupon_left fright"><b><?php echo $this->translate('Expired');?></b></span>
                  <?php //endif;?>
              <?php elseif(in_array('claim', $this->statistics)):?>
								<?php //echo ' <span class="sitestorecoupon_stat sitestorecoupon_left fright">' .$sitestore->claimed.' '.$this->translate('Used') . '</span>'; ?>
                <?php if($sitestore->claim_count != -1):?>
                  <?php $sitestore->claim_count  = $sitestore->claim_count - $sitestore->claimed ;?>  
                  <span class="sitestorecoupon_stat sitestorecoupon_left fright">
                    <?php //echo $this->translate(array('%1$s Left', '%1$s Left', $sitestore->claim_count), $this->locale()->toNumber($sitestore->claim_count)) ?>
                    
                    <?php if($sitestore->claim_count == 1) : ?>
                      <?php echo $this->translate(array('%1$s coupon left', '%1$s coupon left', $sitestore->claim_count), $this->locale()->toNumber($sitestore->claim_count)) ?>
                    <?php else : ?>
                     <?php echo $this->translate(array('%1$s coupons left', '%1$s coupons left', $sitestore->claim_count), $this->locale()->toNumber($sitestore->claim_count)) ?>
                    <?php endif;?>
                   </span>
                 <?php else : ?>
                   <span class="sitestorecoupon_stat sitestorecoupon_left fright"><?php echo $this->translate('Unlimited Use') ?></span>
                <?php endif;?>
            	<?php endif;?>
						</div> 
            <?php $description = strip_tags($sitestore->description);?>
            <?php   if (!empty($description)):?>
							<?php $truncate_description = ( Engine_String::strlen($description) > 190 ? Engine_String::substr($description, 0, 190) . '...' : $description );?>
              <?php if(Engine_String::strlen($description) > 190):?>
								<?php $truncate_description .= $this->htmlLink($sitestore->getHref(array('tab' => $tab_id)), $this->translate('More &raquo;'));?>
              <?php endif;?>
              <?php echo $truncate_description;?>
            <?php endif;?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitestoreoffer"), array("orderby" => $this->orderby)); ?>
	</div>
<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There are no search results to display.');?>
		</span>
	</div>
<?php endif;?>
<?php if(!empty($viewer_id)):?>
	<?php date_default_timezone_set($oldTz);?>
<?php endif;?>

<script type="text/javascript">
  var storeAction = function(store){
     var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_store')){
				form=$('filter_form_store');
			}
    form.elements['store'].value = store;
    
		form.submit();
  } 
</script>