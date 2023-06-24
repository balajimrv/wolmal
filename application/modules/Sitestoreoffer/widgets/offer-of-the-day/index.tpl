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

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestoreoffer/externals/styles/style_sitestoreoffer.css')
?>
<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $this->offerOfDay->store_id, $layout);?>
<ul class="generic_list_widget generic_list_widget_large_photo sitestore_sidebar_list">
	<li>
    <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $this->offerOfDay->store_id);?>
		<div class="photo">
			<?php if(!empty($this->offerOfDay->photo_id)):?>
				<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $this->offerOfDay->owner_id, 'offer_id' =>  $this->offerOfDay->offer_id,'tab' => $tab_id,'slug' => $this->offerOfDay->getOfferSlug($this->offerOfDay->title)), $this->itemPhoto($this->offerOfDay, 'thumb.profile'),array('title' => $this->offerOfDay->getTitle())) ?>
			<?php else:?>
				<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $this->offerOfDay->owner_id, 'offer_id' =>  $this->offerOfDay->offer_id,'tab' => $tab_id,'slug' => $this->offerOfDay->getOfferSlug($this->offerOfDay->title)), "<img class='thumb_profile' src='". $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/nophoto_offer_thumb_profile.png' alt='' />",array('title' => $this->offerOfDay->getTitle())) ?>
			<?php endif;?>
		</div>
		<div class="info">
			<div class="title">
			  <?php echo $this->htmlLink($this->offerOfDay->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->offerOfDay->getTitle()),array('title' => $this->offerOfDay->description)) ?>  
			</div>
	    <div class="owner seaocore_txt_light">
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.title.truncation', 18);
				$tmpBody = strip_tags($sitestore_object->title);
				$store_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>	
				<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitestore()->getHref($this->offerOfDay->store_id, $this->offerOfDay->owner_id, $this->offerOfDay->getSlug()),  $store_title,array('title' => $sitestore_object->title)) ?>
      </div>
      
      <div class="sitestore_offer_date"></div>
      
      <div class="sitestore_offer_date">
        <?php $today = date("Y-m-d H:i:s"); ?>
        <?php if(in_array('expire', $this->statistics) && !empty($this->offerOfDay->end_settings) && $this->offerOfDay->end_time < $today):?>
              <div class="sitestore_offer_date">
                <span>
                  <b><?php echo $this->translate('Expired');?></b>
                </span>
              </div>
            <?php //endif;?>
        <?php elseif(in_array('claim', $this->statistics)):?>
          <?php //echo '<span class="sitestorecoupon_stat sitestorecoupon_left ">' .$this->offerOfDay->claimed.' '.$this->translate('Used') . '</span>'; ?>
          <?php if($this->offerOfDay->claim_count != -1):?>
          <?php $this->offerOfDay->claim_count  = $this->offerOfDay->claim_count - $this->offerOfDay->claimed ;?>
            <div class="sitestore_offer_date">
              <span>
                <?php //echo $this->translate(array('<b>%1$s</b> Left', '<b>%1$s</b> Left', $this->offerOfDay->claim_count), $this->locale()->toNumber($this->offerOfDay->claim_count)) ?>
                
                <?php if($this->offerOfDay->claim_count == 1) : ?>
                 <?php echo $this->translate(array('<b>%1$s</b> coupon left', '<b>%1$s</b> coupon left', $this->offerOfDay->claim_count), $this->locale()->toNumber($this->offerOfDay->claim_count)) ?>
                <?php else : ?>
                 <?php echo $this->translate(array('<b>%1$s</b> coupons left', '<b>%1$s</b> coupons left', $this->offerOfDay->claim_count), $this->locale()->toNumber($this->offerOfDay->claim_count)) ?>
                <?php endif;?>
              </span>
            </div>
          <?php else : ?>
            <div class="sitestore_offer_date">
              <span><?php echo $this->translate('Unlimited Use') ?></span>
            </div>
          <?php endif;?>
        <?php endif;?>
      
        <?php if(in_array('startdate', $this->statistics)):?>
          <div class="sitestore_offer_date">
            <span><?php echo $this->translate('Start date') . ':'; ?></span>
            <span><?php echo $this->timestamp(strtotime($this->offerOfDay->start_time)) ?></span>
          </div>
        <?php endif;?>
        
      	<?php if(in_array('enddate', $this->statistics)):?>
          <div class="sitestore_offer_date">
            <span><?php echo $this->translate('End date') . ':'; ?></span>
            <?php if($this->offerOfDay->end_settings == 1):?>
              <span><?php echo $this->timestamp(strtotime($this->offerOfDay->end_time)) ?></span>
            <?php else:?>
              <span><?php echo $this->translate('Never Expires');?></span>
            <?php endif;?>
          </div>
        <?php endif;?>
        
        <?php if(in_array('minpurchase', $this->statistics) && !empty($this->offerOfDay->minimum_purchase)):?>
          <div class="sitestore_offer_date">
            <span><?php echo $this->translate('Minimum Purchase'). ':';?></span>
            <span><?php echo $this->offerOfDay->minimum_purchase;?></span>
          </div>
        <?php endif;?>
        
        <?php if(in_array('couponurl', $this->statistics)):?>
          <?php if(!empty($this->offerOfDay->enable_url) && !empty($this->offerOfDay->url)):?>
          <div class="sitestore_offer_date">
            <span><?php echo $this->translate('URL') . ":";?></span>
            <span><a href="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $this->offerOfDay->url; ?>" target="_blank" title="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $this->offerOfDay->url ?>"><?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $this->offerOfDay->url ?></a></span> 
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
            <input type="text" value="<?php echo $this->offerOfDay->coupon_code;?>" class="sitestorecoupon_code_num" onclick="this.select()" readonly>
          </span>
        <?php endif;?>
        
        <span class="sitestore_offer_stat sitestore_offer_discount sitestorecoupon_tip_wrapper mtop5">
        	<?php if(in_array('discount', $this->statistics)):?>
          <span class="sitestorecoupon_tip">
            <span><?php echo $this->translate('Coupon Discount Value');?></span>
            <i></i>
          </span>
          <?php if(!empty($this->offerOfDay->discount_type)):
            $priceStr = Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($this->offerOfDay->discount_amount);?>
            <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
          <?php else:?>
            <span class="discount_value"><?php echo $this->offerOfDay->discount_amount . '%';?></span>&nbsp;&nbsp;
          <?php endif;?>
        	<?php endif;?>
        </span>
        </div>                
		</div>
	</li>
</ul>		
<?php if(!empty($viewer_id)):?>
	<?php date_default_timezone_set($oldTz);?>
<?php endif;?>