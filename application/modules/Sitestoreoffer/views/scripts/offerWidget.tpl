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
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/sitestore-tooltip.css');

include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>

<?php $viewer = Engine_Api::_()->user()->getViewer();?>
<?php $viewer_id = $viewer->getIdentity();?>
<?php if(!empty($viewer_id)):?>
  <?php date_default_timezone_set($viewer->timezone);?>
<?php endif;?>

<ul class="sitestore_sidebar_list">
  <?php foreach ($this->recentlyview as $sitestore): ?>
    <li class="generic_list_widget_large_photo">
      <?php $sitestore_object = Engine_Api::_()->getItem('sitestore_store', $sitestore->store_id);?>
      <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.layoutcreate', 0);
            $tab_id = Engine_Api::_()->sitestore()->GetTabIdinfo('sitestoreoffer.profile-sitestoreoffers', $sitestore->store_id, $layout);?>
      <div class="sitestore_offer_photo">
        <?php if(!empty($sitestore->photo_id)):?>
          <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $sitestore->owner_id, 'offer_id' =>  $sitestore->offer_id,'tab' => $tab_id,'slug' => $sitestore->getOfferSlug($sitestore->title)), $this->itemPhoto($sitestore, 'thumb.icon'),array('title' => $sitestore->title)) ?>
        <?php else:?>
          <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $sitestore->owner_id, 'offer_id' =>  $sitestore->offer_id,'tab' => $tab_id,'slug' => $sitestore->getOfferSlug($sitestore->title)), "<img class='thumb_icon' src='". $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/nophoto_offer_thumb_icon.png' alt='' />",array('title' => $sitestore->title)) ?>
        <?php endif;?>
      </div>
      <div class=''>
        <div class='sitestore_sidebar_list_title sitestoreoffer_show_tooltip_wrapper'>
          <?php echo $item_title = $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $sitestore->owner_id, 'offer_id' =>  $sitestore->offer_id,'tab' => $tab_id,'slug' => $sitestore->getOfferSlug($sitestore->title)), $sitestore->title); ?>
          <?php
          $truncation_limit_desc = 500;
          $tmpBody = strip_tags($sitestore->description);
          $item_description = ( Engine_String::strlen($tmpBody) > $truncation_limit_desc ? Engine_String::substr($tmpBody, 0, $truncation_limit_desc) . '..' : $tmpBody );
          ?>
          <div class="sitestoreoffer_show_tooltip">
            <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitestore/externals/images/tooltip_arrow.png" alt="" class="arrow" />
            <?php echo $sitestore->description; ?>
          </div>
        </div>
        <div class='sitestore_sidebar_list_details'>
          <?php
          $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreoffer.truncation.limit', 13);
          $tmpBody = strip_tags($sitestore->sitestore_title);
          $item_sitestore_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
          ?>
          <?php $item = Engine_Api::_()->getItem('sitestore_store', $sitestore->store_id); ?>
          <?php echo $this->translate("in")." ". $this->htmlLink(Engine_Api::_()->sitestore()->getHref($sitestore->store_id, $sitestore->owner_id, $item->getSlug()), $item_sitestore_title, array('title' => $sitestore->sitestore_title)) ?>
        </div>
        
        <div class="sitestore_offer_date"></div>
         <?php $today = date("Y-m-d H:i:s"); ?>
        <div class="sitestore_offer_date"> 
          <?php if(in_array('expire', $this->statistics) && !empty($sitestore->end_settings) && $sitestore->end_time < $today):?>
                <div class="sitestore_offer_date">
                  <span>
                    <b><?php echo $this->translate('Expired');?></b>
                  </span>
                </div>
              <?php //endif;?>
          <?php elseif(in_array('claim', $this->statistics)):?>
            <?php //echo '<span class="sitestorecoupon_stat sitestorecoupon_left ">' .$sitestore->claimed.' '.$this->translate('Used') . '</span>'; ?>
            <?php if($sitestore->claim_count != -1):?>
              <?php $sitestore->claim_count  = $sitestore->claim_count - $sitestore->claimed ;?>
              <div class="sitestore_offer_date">
                <span>
                  <?php //echo $this->translate(array('<b>%1$s</b> Left', '<b>%1$s</b> Left', $sitestore->claim_count), $this->locale()->toNumber($sitestore->claim_count)) ?>
                  
                  <?php if($sitestore->claim_count == 1) : ?>
                  <?php echo $this->translate(array('<b>%1$s</b> coupon left', '<b>%1$s</b> coupon left', $sitestore->claim_count), $this->locale()->toNumber($sitestore->claim_count)) ?>
                 <?php else : ?>
                  <?php echo $this->translate(array('<b>%1$s</b> coupons left', '<b>%1$s</b> coupons left', $sitestore->claim_count), $this->locale()->toNumber($sitestore->claim_count)) ?>
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
              <span><?php echo $this->translate('Start date') . ":"; ?></span>
              <span><?php echo $this->timestamp(strtotime($sitestore->start_time)) ?></span>
            </div>
          <?php endif;?>
          
          <?php if(in_array('enddate', $this->statistics)):?>
            <div class="sitestore_offer_date">
              <span><?php echo $this->translate('End date') . ":"; ?></span>
              <?php if($sitestore->end_settings == 1):?>
                <span>
                  <?php echo $this->timestamp(strtotime($sitestore->end_time)) ?>
                </span>
              <?php else:?>
                <span><?php echo $this->translate('Never Expires');?></span>
              <?php endif;?>
            </div>
          <?php endif;?>
          
          <?php if(in_array('minpurchase', $this->statistics) && !empty($sitestore->minimum_purchase)):?>
            <div class="sitestore_offer_date">
              <span><?php echo $this->translate('Minimum Purchase') . ":";?></span>
              <span><?php echo $sitestore->minimum_purchase;?></span>
            </div>
          <?php endif;?>
          
          <?php if(in_array('couponurl', $this->statistics)):?>
            <?php if(!empty($this->enable_url) && !empty($sitestore->url)):?>
            <div class="sitestore_offer_date">
              <span><?php echo $this->translate('URL') . ":";?></span>
              <span><a href="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $sitestore->url; ?>" target="_blank" title="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $sitestore->url ?>"><?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $sitestore->url ?></a></span>
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
            
            <span class="sitestore_offer_stat sitestore_offer_discount sitestorecoupon_tip_wrapper mtop5">
              <?php if(in_array('discount', $this->statistics)):?>
                <span class="sitestorecoupon_tip">
                  <span><?php echo $this->translate('Coupon Discount Value');?></span>
                  <i></i>
                </span>
                <?php if(!empty($sitestore->discount_type)):
                  $priceStr = Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($sitestore->discount_amount); ?>
                  <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                <?php else:?>
                  <span class="discount_value"><?php echo $sitestore->discount_amount . '%';?></span>&nbsp;&nbsp;
                <?php endif;?>
              <?php endif;?>
            </span>
        <?php 
          $viewer_id = $viewer->getIdentity();
          $claim_value = Engine_Api::_()->getDbTable('claims','sitestoreoffer')->getClaimValue($viewer_id,$sitestore->offer_id,$sitestore->store_id);
        ?>

        <div class="sitestore_sidebar_list_details"> 
          <?php if($this->popularity == 'comment_count'):?>
            <?php echo $this->translate(array('%s comment', '%s comments', $sitestore->comment_count), $this->locale()->toNumber($sitestore->comment_count)) ?>,
            <?php echo $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count)) ?>
          <?php elseif($this->popularity == 'like_count'):?>
            <?php echo $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count)) ?>,
            <?php echo $this->translate(array('%s comment', '%s comments', $sitestore->comment_count), $this->locale()->toNumber($sitestore->comment_count)) ?>
          <?php elseif($this->popularity == 'view_count'):?>
            <?php echo $this->translate(array('%s view', '%s views', $sitestore->view_count), $this->locale()->toNumber($sitestore->view_count)) ?>,
            <?php echo $this->translate(array('%s like', '%s likes', $sitestore->like_count), $this->locale()->toNumber($sitestore->like_count)) ?>
          <?php endif;?>    
        </div>

      
<!--        <div class="sitestore_sidebar_list_details">  
          <?php //echo $sitestore->claimed.' '.$this->translate('claimed') ?>
        </div>
        <?php //if($sitestore->claim_count != -1):?>
          <div class="sitestore_sidebar_list_details">  
            <?php //echo $sitestore->claim_count.' '.$this->translate(array('claim left', 'claims left', $sitestore->claim_count ), $this->locale()->toNumber($sitestore->claim_count)) ?>
          </div>  
        <?php //endif;?>-->
      </div>
    </li>
  <?php endforeach; ?>
  <li class="sitestore_sidebar_list_seeall">
    <?php if($this->popularity == 'comment_count'):?>
      <a href='<?php echo $this->url(array('orderby'=> 'comment'), 'sitestoreoffer_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
    <?php elseif($this->popularity == 'like_count'):?>
      <a href='<?php echo $this->url(array('orderby'=> 'like'), 'sitestoreoffer_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
    <?php elseif($this->popularity == 'view_count'):?>
      <a href='<?php echo $this->url(array('orderby'=> 'view'), 'sitestoreoffer_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
    <?php elseif($this->popularity == 'popular'):?>
      <a href='<?php echo $this->url(array('orderby'=> 'popular'), 'sitestoreoffer_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
    <?php else:?>
      <a href='<?php echo $this->url(array('hotoffer'=> $this->hotOffer), 'sitestoreoffer_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
    <?php endif;?>
  </li>
</ul>