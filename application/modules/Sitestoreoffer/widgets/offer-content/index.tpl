<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $posted =  $this->timestamp(strtotime($this->offer->creation_date));  ?>
<?php $viewer_id = $this->viewer->getIdentity();?>
<?php if(!empty($viewer_id)):?>
	<?php $oldTz = date_default_timezone_get();?>
	<?php date_default_timezone_set($this->viewer->timezone);?>
<?php endif;?>
<?php 
  //include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitestoreoffer/externals/styles/style_sitestoreoffer.css')
?>
<div class="sitestore_viewstores_head">
	<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>
	  <?php echo $this->sitestore->__toString() ?>
	  <?php echo $this->translate('&raquo;');?>
     <?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Coupons')) ?>
	  <?php echo $this->translate('&raquo;');?>
	  <?php echo $this->offer->title ?>
	</h2>
</div>

<div class="sitestoreoffer_view">
  <!--FACEBOOK LIKE BUTTON START HERE-->
	<?php  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
	 if (!empty ($fbmodule)) :
	  $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse'); 
	  if (!empty ($enable_facebookse) && !empty($fbmodule->version)) :
	    $fbversion = $fbmodule->version; 
	    if (!empty($fbversion) && ($fbversion >= '4.1.5')) { ?>
	       <div class="sitestoreoffer_fb_like">
	          <script type="text/javascript">
	              var fblike_moduletype = 'sitestoreoffer_offer';
	              var fblike_moduletype_id = '<?php echo $this->offer->offer_id ?>';
	           </script>
	          <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
	        </div>
	    <?php } ?>
   	<?php endif; ?>
   <?php endif; ?>
   
	<div class="sitestore_offer_block" style="margin-bottom:10px;margin-top:10px;">
		<div class="sitestore_offer_photo">
			<?php if(!empty($this->offer->photo_id)):?>
				<?php echo $this->itemPhoto($this->offer, 'thumb.normal'); ?>
			<?php else:?>
				<?php echo "<img class='thumb_normal' src='". $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/nophoto_offer_thumb_normal.png' alt='' />" ?>
			<?php endif;?>
		</div>
		<div class="sitestore_offer_details">
			<div class="sitestore_offer_title">
				<h3><?php echo $this->offer->title;?></h3>
			</div>
			
		  <div class="sitestore_offer_date">
        <?php if(in_array('startdate', $this->statistics)):?>
          <div class="sitestore_offer_date">
            <span><?php echo $this->translate('Start date') . ':'; ?></span>
            <span><?php echo $this->timestamp(strtotime($this->offer->start_time)) ?></span>
          </div>
        <?php endif;?>
        
        <?php if(in_array('enddate', $this->statistics)):?>
          <div class="sitestore_offer_date">
            <span><?php echo $this->translate('End date') . ':'; ?></span>
            <?php if($this->offer->end_settings == 1):?>
              <span><?php echo $this->timestamp(strtotime($this->offer->end_time)) ?></span>
            <?php else:?>
              <span><?php echo $this->translate('Never Expires');?></span>
            <?php endif;?>
          </div>
        <?php endif;?>
        
        <?php if(in_array('minpurchase', $this->statistics) && !empty($this->offer->minimum_purchase)):?>
          <div class="sitestore_offer_date">
            <span><?php echo $this->translate('Minimum Purchase') . ':';?></span>
            <span><?php echo $this->offer->minimum_purchase;?></span>
          </div>
        <?php endif;?>
        
        <?php $today = date("Y-m-d H:i:s"); ?>
        <?php $claim_value = Engine_Api::_()->getDbTable('claims','sitestoreoffer')->getClaimValue($this->viewer_id,$this->offer->offer_id,$this->sitestore->store_id);?>
        <?php if(!empty($this->offer->product_name)):?>
          <div class="sitestore_offer_date">
            <span><?php echo $this->translate('Mapped Products:');?></span>
            <span><?php echo $this->offer->product_name;?></span>
          </div>
        <?php endif;?>
        
        <?php if(in_array('couponurl', $this->statistics)):?>
          <?php if(!empty($this->enable_url) && !empty($this->offer->url)):?>
          <div class="sitestore_offer_date">
            <span><?php echo $this->translate('URL') . ':';?></span>
            <span><a href="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $this->offer->url; ?>" target="_blank" title="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $this->offer->url ?>"><?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $this->offer->url ?></a></span>
            </div>
          <?php endif;?>
        <?php endif;?>	
			</div>
          
    	 <div class="sitestore_offer_stats">
     		<?php if(in_array('couponcode', $this->statistics)):?>
					<span class="sitestore_offer_stat sitestorecoupon_code sitestorecoupon_tip_wrapper">
						<span class="sitestorecoupon_tip">
							<span><?php echo $this->translate('Select and Copy Code to use');?></span>
							<i></i>
						</span>
						<input type="text" value="<?php echo $this->offer->coupon_code;?>" class="sitestorecoupon_code_num" onclick="this.select()" readonly>
					</span>
				<?php endif;?>
        
          <?php if(in_array('discount', $this->statistics)):?>
         <span class="sitestore_offer_stat sitestore_offer_discount sitestorecoupon_tip_wrapper">
          <span class="sitestorecoupon_tip">
            <span><?php echo $this->translate('Coupon Discount Value');?></span>
            <i></i>
          </span>
          <?php if(!empty($this->offer->discount_type)):
            $priceStr = Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($this->offer->discount_amount);?>
            <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
          <?php else:?>
            <span class="discount_value"><?php echo $this->offer->discount_amount . '%';?></span>&nbsp;&nbsp;
          <?php endif;?>
            </span>	
          <?php endif;?>
        <?php $today = date("Y-m-d H:i:s"); ?>     
        <?php if(in_array('expire', $this->statistics) && !empty($this->offer->end_settings) && $this->offer->end_time < $today):?>
              <span class="sitestorecoupon_stat sitestorecoupon_left fright">
                <b><?php echo $this->translate('Expired');?></b>&nbsp;&nbsp;&nbsp;
              </span>
            <?php //endif;?>
        <?php elseif(in_array('claim', $this->statistics)):?>
        	<?php //echo '<span class="sitestorecoupon_stat sitestorecoupon_left fright">' .$this->offer->claimed.' '.$this->translate('Used'). '</span>'; ?>
          <?php if($this->offer->claim_count != -1):?>
            <?php $this->offer->claim_count  = $this->offer->claim_count - $this->offer->claimed ;?>
            <span class="sitestorecoupon_stat sitestorecoupon_left fright">
              <?php //echo $this->translate(array('%1$s Left', '%1$s Left', $this->offer->claim_count), $this->locale()->toNumber($this->offer->claim_count)) ?>
              
              <?php if($this->offer->claim_count == 1) : ?>
               <?php echo $this->translate(array('%1$s coupon left', '%1$s coupon left', $this->offer->claim_count), $this->locale()->toNumber($this->offer->claim_count)) ?>
              <?php else : ?>
               <?php echo $this->translate(array('%1$s coupons left', '%1$s coupons left', $this->offer->claim_count), $this->locale()->toNumber($this->offer->claim_count)) ?>
              <?php endif;?>
            </span>
          <?php else : ?>
            <span class="sitestorecoupon_stat sitestorecoupon_left fright"><?php echo $this->translate('Unlimited Use') ?></span>
          <?php endif;?>
     		<?php endif;?>
		  	</div>
     
		  <div class="sitestore_offer_stats">
		    <?php echo nl2br($this->offer->description);?>
		  </div>

			<?php //if($this->offer->claim_count == -1 && ($this->offer->end_time > $today || $this->offer->end_settings == 0)):?>
				<?php //$show_offer_claim = 1;?>
			<?php //elseif($this->offer->claim_count > 0 && ($this->offer->end_time > $today || $this->offer->end_settings == 0)):?>
				<?php //$show_offer_claim = 1;?>
			<?php //else:?>
				<?php //$show_offer_claim = 0;?>
			<?php //endif;?>		  
		</div>
	</div>  
  <div class="sitestoreoffer_view_stat seaocore_txt_light">
    <?php echo $this->translate('Posted');?> <?php echo $posted;  ?>
     <span class="offer_views">- <?php echo $this->translate(array('%s comment', '%s ', $this->offer->comments()->getCommentCount()),$this->locale()->toNumber($this->offer->comments()->getCommentCount())) ?>	-  
			<?php echo $this->translate(array('%s view', '%s views', $this->offer->view_count ), $this->locale()->toNumber($this->offer->view_count )) ?>
     - <?php echo $this->translate(array('%s like', '%s likes', $this->offer->likes()->getLikeCount()),$this->locale()->toNumber($this->offer->likes()->getLikeCount())) ?>
     </span>
  </div>
  
  <div class='sitestoreoffer_view_options' style="margin-bottom:15px;">
		<!--  Start: Suggest to Friend link show work -->
		<?php if( !empty($this->offerSuggLink)): ?>				
			<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $this->offer->offer_id, 'sugg_type' => 'store_offer'), $this->translate('Suggest to Friends'), array(
			'class'=>'buttonlink icon_suggestion smoothbox')); ?> &nbsp; | &nbsp;			
		<?php endif; ?>					
		<!--  End: Suggest to Friend link show work -->
		
  	<?php if($this->can_create_offer):?>
				<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'create','store_id'=>$this->sitestore->store_id, 'tab'=>$this->tab_selected_id), $this->translate('Add a Coupon'), array(
						'class' => 'buttonlink seaocore_icon_create',
			)) ?>&nbsp; | &nbsp;
        <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general','action' => 'edit', 'offer_id' => $this->offer->offer_id,'store_id'=>$this->sitestore->store_id,'tab'=>$this->tab_selected_id), $this->translate('Edit Coupon'), array(
						'class' => 'buttonlink seaocore_icon_edit'
					)) ?>&nbsp; | &nbsp;
       <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'delete','store_id'=>$this->sitestore->store_id,'offer_id'=>$this->offer->offer_id, 'tab'=>$this->identity_temp), $this->translate('Delete Coupon'), array(
						'class' => 'buttonlink seaocore_icon_delete',
						)) ?>&nbsp; | &nbsp;
			<?php if($this->offer->sticky == 1):?>
				<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general','action' => 'sticky', 'offer_id' => $this->offer->offer_id,'store_id'=>$this->offer->store_id, 'tab'=>$this->tab_selected_id), $this->translate('Remove as Featured'),array('class' => 'smoothbox buttonlink seaocore_icon_unfeatured')) ?>
			<?php else: ?>
				<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general','action' => 'sticky', 'offer_id' => $this->offer->offer_id,'store_id'=>$this->offer->store_id, 'tab'=>$this->tab_selected_id), $this->translate('Make Featured'), array('class' => 'smoothbox buttonlink seaocore_icon_featured')
				) ?>
			<?php endif; ?>&nbsp; | &nbsp;
		<?php endif; ?>   

		<?php if($this->allowView ): ?>    
			<?php echo $this->htmlLink(array('route' => 'default','module'=> 'sitestoreoffer', 'controller'=>'index','action' => 'add-offer-of-day', 'offer_id' => $this->offer->offer_id, 'format' => 'smoothbox'), $this->translate('Make Coupon of the Day'), array(
			'class' => 'buttonlink smoothbox item_icon_sitestoreoffer_offer'
		)) ?>
      &nbsp; | &nbsp;
		<?php endif;?>

    <?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general','action' => 'print', 'offer_id' => $this->offer->offer_id,'store_id'=>$this->offer->store_id), $this->translate('Print Coupon'), array('target' => '_blank',' class' => 'buttonlink icon_sitestores_print')) ?>&nbsp; | &nbsp;

    <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'sitestoreoffer_offer', 'id' => $this->offer->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox buttonlink seaocore_icon_share')); ?>&nbsp; | &nbsp;

   <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' =>  $this->offer->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox buttonlink seaocore_icon_report')); ?>&nbsp; | &nbsp;
   
   <?php if($this->can_create_offer):?>
   <?php if (!empty($this->offer->status)): ?>
   <a class='buttonlink seaocore_icon_disapproved' href='javascript:void(0)' onclick='Smoothbox.open("<?php echo $this->url(array('module' => 'sitestoreoffer', 'controller' => 'index','action' => 'enable-disable', 'offer_id' => $this->offer->offer_id,'store_id' => $this->offer->store_id, 'status' => $this->offer->status), 'default', true); ?>")'><?php echo $this->translate('Disable Coupon '); ?></a>	
                  <?php else: ?>
   <a class='buttonlink seaocore_icon_approved' href='javascript:void(0)' onclick='Smoothbox.open("<?php echo $this->url(array('module' => 'sitestoreoffer', 'controller' => 'index','action' => 'enable-disable', 'offer_id' => $this->offer->offer_id,'store_id' => $this->offer->store_id, 'status' => $this->offer->status), 'default', true); ?>")'><?php echo $this->translate('Enable Coupon '); ?></a>
                  <?php endif; ?>&nbsp; | &nbsp;
    <?php endif;?>
    <?php if(!empty($viewer_id)) : ?>
    <?php echo   $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'resendoffer', 'id' => $this->offer->offer_id, 'format' => 'smoothbox'),Zend_Registry::get('Zend_Translate')->_('Email Me '),array(
						'class' => 'smoothbox buttonlink sitestore_offer_invite',
			)); ?>
   <?php endif; ?>
  </div>

	<?php 
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl';
    ?>

</div>
<?php if(!empty($viewer_id)):?>
	<?php date_default_timezone_set($oldTz);?>
<?php endif;?>
<style type="text/css">
.fb_edge_widget_with_comment {
    position: relative !important;
}
</style>
<script type="text/javascript">

 var share_offer =  '<?php echo $this->share_offer;?>';
 var offer_id =  '<?php echo $this->offer_id;?>';

 if(share_offer != '') {
  var url = en4.core.baseUrl + 'activity/index/share/type/sitestoreoffer_offer/id/'+ offer_id +'/format/smoothbox';
  Smoothbox.open(url);
 }

</script>