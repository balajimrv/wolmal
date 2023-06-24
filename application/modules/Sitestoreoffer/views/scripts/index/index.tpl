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
  include APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/Adintegration.tpl';
?>

<?php 
	$this->headLink()
	  ->appendStylesheet($this->layout()->staticBaseUrl
	    . 'application/modules/Sitestoreoffer/externals/styles/style_sitestoreoffer.css');
?>

<?php $viewer_id = $this->viewer->getIdentity();?>
<?php if(!empty($viewer_id)):?>
<?php $oldTz = date_default_timezone_get();?>
	<?php date_default_timezone_set($this->viewer->timezone);?>
<?php endif;?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/payment_navigation_views.tpl'; ?>

<div class="sitestore_viewstores_head">
	<?php echo $this->htmlLink($this->sitestore->getHref(), $this->itemPhoto($this->sitestore, 'thumb.icon', '', array('align' => 'left'))) ?>
  <div class="fright">
		<a href='<?php echo $this->url(array('store_id' => $this->sitestore->store_id), 'sitestore_edit', true) ?>' class='buttonlink icon_sitestores_dashboard'><?php echo $this->translate('Dashboard');?></a>
  </div>
	<h2>	
	  <?php echo $this->sitestore->__toString() ?>	
	  <?php echo $this->translate('&raquo; ');?>
			<?php echo $this->htmlLink($this->sitestore->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Coupons')) ?>
  </h2>
</div>

<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adofferstore', 3) && $store_communityad_integration && Engine_Api::_()->sitestore()->showAdWithPackage($this->sitestore)):?>
	<div class="layout_right" id="communityad_offerindex">
		<?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.adofferstore', 3),"loaded_by_ajax"=>0,'widgetId'=>"store_offerindex"))?>
	</div>
<?php endif;?>

<div class="layout_middle">
	<!--<div class="global_form">-->
  <div>
		<div>
			<div>
				<div class="mbot10"><h3><?php echo $this->translate('Coupons') ?></h3></div>
			  <?php if($this->can_create_offer): ?>
					<?php echo $this->translate('You can add attractive coupons for your store and Page by clicking on the "Add an Coupon" link. These coupons will appear on your Page profile. You can also select a featured coupon for your Page below, which will be shown alongside your Page\'s entry in the listing of all Pages of this community, and will also be shown on top of all your coupons.') ?>
					<br />
					<div class="seaocore_add" style="margin-top:20px;">
			  		<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'create','store_id'=>$this->sitestore->store_id,'store_offer'=> '1', 'tab' => $this->tab_selected_id), $this->translate('Add Coupon'), array(
						'class' => 'buttonlink seaocore_icon_create',
						)) ?>
					</div>	
			  <?php endif;?>
			   
				<ul class="sitestoreoffer_list">
					<?php if(!empty($this->count)): ?>
						<?php foreach ($this->paginator as $item): ?>
							<?php if($item->sticky == 1):?>
								<li class="sitestoreoffer_show">
							<?php else: ?>
								<li>
							<?php endif;?>
							<div class="sitestoreoffer_list_photo">
								<?php if(!empty($item->photo_id)):?>
									<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $item->owner_id, 'offer_id' =>  $item->offer_id,'tab' => $this->identity_temp,'slug' => $item->getOfferSlug($item->title)), $this->itemPhoto($item, 'thumb.normal')) ?>
								<?php else:?>
									<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $item->owner_id, 'offer_id' =>  $item->offer_id,'tab' => $this->identity_temp,'slug' => $item->getOfferSlug($item->title)), "<img class='thumb_normal' src='". $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/nophoto_offer_thumb_normal.png' alt='' />") ?>
								<?php endif;?>
               </div>
								<?php if($this->can_create_offer): ?>
									<div class='sitestoreoffer_list_options'>
				  					<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'edit','store_id'=>$this->sitestore->store_id,'offer_id'=>$item->offer_id,'offer_store'=> '1', 'tab' => $this->tab_selected_id), $this->translate('Edit Coupon'), array(
										'class' => 'smoothbox buttonlink seaocore_icon_edit'
										)) ?>	
				  					<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'delete','store_id'=>$this->sitestore->store_id,'offer_id'=>$item->offer_id,'offer_store'=> '1', 'tab' => $this->tab_selected_id), $this->translate('Delete Coupon'), array(
										'class' => 'smoothbox buttonlink seaocore_icon_delete',
										)) ?>
								  
										<?php if($item->sticky == 1):?>
											<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general','action' => 'sticky', 'offer_id' => $item->offer_id,'store_id'=>$item->store_id,'offer_store'=>'1', 'tab' => $this->tab_selected_id), $this->translate('Remove as Featured'), array('class'=>'smoothbox buttonlink seaocore_icon_unfeatured')) ?>
										<?php else: ?>
											<?php echo $this->htmlLink(array('route' => 'sitestoreoffer_general','action' => 'sticky', 'offer_id' => $item->offer_id,'store_id'=>$item->store_id,'offer_store'=>'1', 'tab' => $this->tab_selected_id), $this->translate('Make Featured'), array('class'=>'smoothbox buttonlink seaocore_icon_featured')) ?>
										<?php endif; ?>
                    
                    <?php if (!empty($item->status )): ?>
                    <a class='buttonlink seaocore_icon_disapproved' href='javascript:void(0)' onclick='Smoothbox.open("<?php echo $this->url(array('module' => 'sitestoreoffer', 'controller' => 'index','action' => 'enable-disable', 'offer_id' => $item->offer_id,'store_id' => $item->store_id, 'status' => $item->status), 'default', true); ?>")'><?php echo $this->translate('Disable Coupon '); ?></a>											
										<?php else: ?>
                    <a class='buttonlink seaocore_icon_approved' href='javascript:void(0)' onclick='Smoothbox.open("<?php echo $this->url(array('module' => 'sitestoreoffer', 'controller' => 'index','action' => 'enable-disable', 'offer_id' => $item->offer_id,'store_id' => $item->store_id, 'status' => $item->status), 'default', true); ?>")'><?php echo $this->translate('Enable Coupon '); ?></a>	
										<?php endif; ?>
									</div>
								<?php endif;?>
							  <div class='sitestoreoffer_list_info'>
					        <div class='sitestoreoffer_list_info_title'>
					        	<?php if (!empty($item->hotoffer)):?>
											<span>
												<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/images/icons/hot-offer.png', '', array('class' => 'icon', 'title' => $this->translate('Hot Coupon'))) ?>
											</span>
										<?php endif; ?>
					          <h3><?php echo $this->htmlLink(array('route' => 'sitestoreoffer_view', 'user_id' => $item->owner_id, 'offer_id' =>  $item->offer_id,'tab' => $this->identity_temp,'slug' => $item->getOfferSlug($item->title)), $item->title) ?></h3>
			            </div>
                  
                  <div class="sitestore_offer_date">
                    <div class="sitestore_offer_date">
                      <span><?php echo $this->translate('Start date') . ":"; ?></span>
                      <span><?php echo $this->timestamp(strtotime($item->start_time)) ?></span>
                    </div>
                    <div class="sitestore_offer_date">
                      <span><?php echo $this->translate('End date:'); ?></span>
                      <?php if($item->end_settings == 1):?>
                        <span><?php echo $this->timestamp(strtotime($item->end_time)) ?></span>
                      <?php else:?>
                        <span><?php echo $this->translate('Never Expires') ?></span>
                      <?php endif;?>
                    </div>
                    <?php if (!empty($item->minimum_purchase)): ?>
                      <div class="sitestore_offer_date">
                        <span><?php echo $this->translate('Minimum Purchase') . ":"; ?></span>
                        <span><?php echo $item->minimum_purchase; ?></span>
                      </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($this->enable_url) && !empty($item->url)):?>
                    <div class="sitestore_offer_date">
                      <span><?php echo $this->translate('URL:');?></span>
										  <span><a href="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $item->url; ?>" target="_blank" title="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $item->url ?>"><?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $item->url ?></a></span>
                    </div>
										<?php endif;?>
                  </div> 
                  
                  
                  <div class="sitestore_offer_stats fleft">
                    <?php if(!empty($item->coupon_code)):?>
                      <span class="sitestore_offer_stat sitestorecoupon_code sitestorecoupon_tip_wrapper">
                        <span class="sitestorecoupon_tip">
                          <span><?php echo $this->translate('Select and Copy Code to use');?></span>
                          <i></i>
                        </span>
                        <input type="text" value="<?php echo $item->coupon_code; ?>" class="sitestorecoupon_code_num" onclick="this.select()" readonly>
                      </span>
                    <?php endif; ?>
                    
                    <span class="sitestore_offer_stat sitestore_offer_discount sitestorecoupon_tip_wrapper">
                        <?php
                        if (!empty($item->discount_type)):
                          $priceStr = Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($item->discount_amount);?>
                          <span class="sitestorecoupon_tip">
                            <span><?php echo $this->translate('Coupon Discount Value');?></span>
                            <i></i>
                          </span>
                          <span class="discount_value"><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                        <?php else: ?>
                        	<span class="sitestorecoupon_tip">
                            <span><?php echo $this->translate('Coupon Discount Value');?></span>
                            <i></i>
                          </span>
                          <span class="discount_value"><?php echo $item->discount_amount . '%'; ?></span>&nbsp;&nbsp;
                        <?php endif; ?>
                    </span>
                  </div>
 
                  <div class="sitestore_offer_date">
										<?php $today = date("Y-m-d H:i:s"); ?>
                    <?php if( !empty($item->end_settings) && $item->end_time < $today):?>
                      <span class="sitestorecoupon_stat sitestorecoupon_left fright">
                        <b><?php echo $this->translate('Expired'); ?></b>
                      </span>
                    <?php endif; ?>
									<!--<span>   
                    //<?php if($item->claim_count == -1 && $item->end_time > $today || $item->end_settings == 0):?>
//                      <?php $show_offer_claim = 1;?>
//                    <?php elseif($item->claim_count > 0 && ($item->end_time > $today || $item->end_settings == 0)):?>
//                      <?php $show_offer_claim = 1;?>
//                    <?php else:?>
//                      <?php $show_offer_claim = 0;?>
//                    <?php endif;?>  
                    <?php //echo $item->claimed.' '.$this->translate('Used') ?>
                  </span>-->
                  
									<?php if($item->claim_count != -1):?>
									<?php $item->claim_count  = $item->claim_count - $item->claimed ;?>	
										<span class="sitestorecoupon_stat sitestorecoupon_left fright">
                    	<?php //echo $this->translate(array('%1$s Left', '%1$s Left', $item->claim_count), $this->locale()->toNumber($item->claim_count)) ?>
                     	<?php if($coupon->claim_count == 1) : ?>
                      	<?php echo $this->translate(array('%1$s coupon left', '%1$s coupon left', $item->claim_count), $this->locale()->toNumber($item->claim_count)) ?>
                     	<?php else : ?>
                      	<?php echo $this->translate(array('%1$s coupons left', '%1$s coupons left', $item->claim_count), $this->locale()->toNumber($item->claim_count)) ?>
                     	<?php endif;?>
                    </span>
                  <?php else : ?>
                   	<span class="sitestorecoupon_stat sitestorecoupon_left fright"><?php echo $this->translate('Unlimited Use') ?></span>	
									<?php endif;?>
									</div>
                  
			            <div class='sitestoreoffer_list_info_blurb'>
			              <?php echo nl2br($item->description); ?>
			  					</div>
			  					
                  <?php if($item->end_settings == 1 && ($item->end_time < $today)):?><br />
										<div class="tip" id='sitestorenoffer_search'>
											<span>
												<?php echo $this->translate('This coupon has expired.');?>
                        <?php if($this->can_create_offer): ?>
													<?php echo $this->translate('If you want this coupon to be displayed again, then please %1$sedit it%2$s to change its expiry date.', '<a href="'.$this->url(array('action' => 'edit','store_id' => $this->sitestore->store_id, 'offer_id'=>$item->offer_id,'tab' => $this->tab_selected_id)).'" class="smoothbox ">', '</a>'); ?>
                        <?php endif;?>
											</span>
										</div> 
                  <?php endif;?>
			  				</div>
			    		</li>
						<?php  endforeach; ?>
					<?php else:?>
						<div class="tip" id='sitestorenoffer_search'>
							<span>
								<?php echo $this->translate('No coupons have been added in this Page yet.'); ?>
								<?php if($this->can_create_offer): ?>
									<?php echo $this->translate('Click %1$shere%2$s to create the first coupon of this store.', '<a href="'.$this->url(array( 'action' => 'create','store_id' => $this->sitestore->store_id,'offer_store'=> '1', 'tab' => $this->tab_selected_id)).'" class="smoothbox ">', '</a>'); ?>
									<?php endif;?>
							</span>
						</div>	
		    <?php endif;?>
				</ul>
				<?php  if(!empty($paginator)) { echo $this->paginationControl($this->paginator);} ?>
			</div>
		</div>
	</div>
</div>	
<?php if(!empty($viewer_id)):?>
	<?php date_default_timezone_set($oldTz);?>
<?php endif;?>