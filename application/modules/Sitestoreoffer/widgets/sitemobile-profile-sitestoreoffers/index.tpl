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

<?php $viewer_id = $this->viewer->getIdentity(); ?>
<?php if (!empty($viewer_id)): ?>
  <?php $oldTz = date_default_timezone_get(); ?>
  <?php date_default_timezone_set($this->viewer->timezone); ?>
<?php endif; ?>

<?php if($this->paginator->getTotalItemCount() > 0) :?>
	<div class="sm-content-list" id="profile_sitestoreoffers" >
    <?php if ($this->can_create_offer): ?>
			<div data-role="controlgroup" data-type="horizontal">
				<?php
					echo $this->htmlLink(array('route' => 'sitestoreoffer_general', 'action' => 'create', 'store_id' => $this->sitestore->store_id, 'tab' => $this->identity), $this->translate('Add an Coupon'), array(
							'class' => 'buttonlink seaocore_icon_create','data-role'=>"button", 'data-icon'=>"plus", "data-iconpos"=>"left", "data-inset" => 'false', 'data-mini'=>"true",'data-corners'=>"true",'data-shadow'=>"true"
					))
					?>
			</div>
    <?php endif;?>
		<ul data-role="listview" data-icon="arrow-r">
			<?php foreach ($this->paginator as $item): ?>
				<li>
					<a href="<?php echo $item->getHref(); ?>">
            <?php if (!empty($item->photo_id)): ?>
              <?php echo $this->itemPhoto($item, 'thumb.icon') ?>
            <?php else: ?>
              <?php echo  "<img src='" . $this->layout()->staticBaseUrl . "application/modules/Sitestoreoffer/externals/images/offer_thumb.png' alt='' />" ?>
            <?php endif; ?>
						<h3><?php echo $item->getTitle() ?></h3>
						<p>   
							<?php echo $this->translate('Created by') ?>
							<strong><?php echo $item->getOwner()->getTitle(); ?></strong>
						</p>
            <p> 
              <?php if(in_array('start_date', $this->statistics)):?>
            <span><?php echo $this->translate('Start date') . ':'; ?></span>
       <span><?php echo $this->timestamp(strtotime($item->start_time)) ?></span>&nbsp;&nbsp;
          <?php endif;?>
       <?php if(in_array('end_date', $this->statistics)):?>
            <span><?php echo $this->translate('End date') . ":"; ?></span>
       <?php if($item->end_settings == 1):?><span><?php echo $this->timestamp(strtotime($item->end_time)) ?></span>&nbsp;<?php else:?><span><?php echo $this->translate('Never Expires');?></span>&nbsp;<?php endif;?>
          <?php endif;?>
          <br />
          <?php if(in_array('coupon_code', $this->statistics)):?>
            <span><?php echo $this->translate('Coupon Code'). ":"; ?></span>
       <span><?php echo $item->coupon_code;?></span>&nbsp;&nbsp;&nbsp;
          <?php endif;?>
            <?php if(in_array('coupon_url', $this->statistics)):?>
            <?php if(!empty($this->enable_url) && !empty($item->url)):?><?php $this->translate('URL') . ":";?>
							<a href="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $item->url; ?>" target="_blank" title="<?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $item->url ?>"><?php echo (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://" . $item->url ?></a>&nbsp;&nbsp;&nbsp;
            <?php endif;?>
              <?php endif;?>
              <br />
              <?php if(in_array('discount', $this->statistics)):?>
               <?php echo $this->translate('Discount Amount'). ":";?>
                <?php if(!empty($item->discount_type)):
            $priceStr = Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($item->discount_amount);?>
              <span><?php echo $priceStr; ?></span>&nbsp;&nbsp;&nbsp
              <?php else:?>
              <span><?php echo $item->discount_amount . '%';?>&nbsp;&nbsp;&nbsp;
              <?php endif;?>
              <?php endif;?>
              
              <?php if(in_array('min_purchase', $this->statistics) && !empty($item->minimum_purchase)):?>
              <?php echo $this->translate('Minimum Purchase'). ":";?>
              <span><?php echo $item->minimum_purchase;?></span>&nbsp;&nbsp;&nbsp
              <?php endif;?>
              <?php if(in_array('expire', $this->statistics)):?>
              <?php if(!empty($item->end_settings)):?>
            <?php if($item->end_time < $today):?>
            <span>
              	<b><?php echo $this->translate('Expired');?></b>
              </span>
            <?php endif;?>
            <?php endif;?>
            <?php endif;?>
            <?php if(in_array('claim', $this->statistics)):?>
						<?php echo '<span><b>&middot;</b></span><span>' .$item->claimed.' '.$this->translate('Used') . '</span>&nbsp;&nbsp;&nbsp;'; ?>
            <?php if($item->claim_count != -1):?>
            	<span><b>&middot;</b></span>
            	<span>
                <?php echo $this->translate(array('%1$s Left', '%1$s Left', $item->claim_count), $this->locale()->toNumber($item->claim_count)) ?>
							</span>	
            <?php endif;?>
           <?php endif;?>
            </p>
					</a> 
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, 'profile_sitestoreoffers');
			?>
		<?php endif; ?>

	</div>

<?php else:?>
	<div class="tip">
		<span>
			<?php echo $this->translate('No coupons have been created in this Store yet.'); ?>
			<?php if ($this->can_create_offer): ?>
				<?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create', 'store_id' => $this->sitestore->store_id, 'tab' => $this->identity), 'sitestoreoffer_general') . '">', '</a>'); ?>
			<?php endif; ?>
		</span>
	</div>
<?php endif; ?>

<?php if (!empty($viewer_id)): ?>
  <?php date_default_timezone_set($oldTz); ?>
<?php endif; ?>