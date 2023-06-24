<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreoffer
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if(!empty($this->sitestoreofferDetail)): ?>
<div class="sitestore_admin_popup" style="margin:10px 10px 0 10px;">
	<?php foreach ($this->sitestoreofferDetail as $item) ?>
	<div>
		<h3><?php echo $this->translate('Store Coupon Details'); ?></h3>
		<br />
		<table>
			<tr>
				<tr valign="top">
					<td width="120"><b><?php echo $this->translate('Title:'); ?></b></td>
					<td>
						<?php echo $this->sitestoreofferDetail->title; ?>&nbsp;&nbsp;
					</td>
				</tr>
        <tr valign="top">
					<td width="120"><b><?php echo $this->translate('Coupon Code:'); ?></b></td>
					<td>
						<?php echo $this->sitestoreofferDetail->coupon_code; ?>&nbsp;&nbsp;
					</td>
				</tr> 
        <tr valign="top">
					<td width="120"><b><?php echo $this->translate('Start Date:'); ?></b></td>
					<td>
						<?php echo $this->sitestoreofferDetail->start_time; ?>&nbsp;&nbsp;
					</td>
				</tr>
         <tr valign="top">
					<td width="120"><b><?php echo $this->translate('End Date:'); ?></b></td>
					<td>
            <?php if(!empty($this->sitestoreofferDetail->end_settings)):?>
						<?php echo $this->sitestoreofferDetail->end_time; ?>&nbsp;&nbsp;
            <?php else:?>
            <?php echo $this->translate('Never Expires'); ?>
            <?php endif;?>
   				</td>
				</tr>
        
        <tr valign="top">
					<td width="120"><b><?php echo $this->translate('Discount:'); ?></b></td>
					<td>
						<?php if (!empty($this->sitestoreofferDetail->discount_type)):

                      $priceStr = Engine_Api::_()->sitestoreoffer()->getCurrencySymbolPrice($this->sitestoreofferDetail->discount_amount);
                      ?>
                   <span><?php echo $priceStr; ?></span>&nbsp;&nbsp;
                    <?php else: ?>
                      <span><?php echo $this->sitestoreofferDetail->discount_amount . '%'; ?></span>&nbsp;&nbsp;
                    <?php endif; ?>
					</td>
				</tr>
        <tr valign="top">
					<td width="120"><b><?php echo $this->translate('Minimum Purchase:'); ?></b></td>
					<td>
            <?php if(empty($this->sitestoreofferDetail->minimum_purchase)):?>
              <?php echo $this->translate('<i>Unlimited</i>'); ?>&nbsp;&nbsp;
            <?php else:?>
              <?php echo $this->translate($this->sitestoreofferDetail->minimum_purchase); ?>&nbsp;&nbsp;
            <?php endif;?>
					</td>
				</tr>
        <tr valign="top">
					<td width="120"><b><?php echo $this->translate('Minimum Product Quantity:'); ?></b></td>
					<td>
            <?php if(empty($this->sitestoreofferDetail->min_product_quantity)):?>
              <?php echo $this->translate('<i>Unlimited</i>'); ?>&nbsp;&nbsp;
            <?php else:?>
              <?php echo $this->translate($this->sitestoreofferDetail->min_product_quantity); ?>&nbsp;&nbsp;
            <?php endif;?>
					</td>
				</tr>
        <?php if(!empty($this->sitestoreofferDetail->product_ids)):?>
        <tr valign="top">
					<td width="120"><b><?php echo $this->translate('Selected Products:'); ?></b></td>
          <?php $selected_product_ids = explode(',' , $this->sitestoreofferDetail->product_ids);?>
          <?php foreach($selected_product_ids as $product_id):?>
                          <?php $productTitle = Engine_Api::_()->getDbTable('products', 'sitestoreproduct')->getProductTitle($product_id);?>
					<td>
              <?php echo $this->translate($productTitle); ?>&nbsp;&nbsp;
					</td>
        <?php endforeach;?>
				</tr>
        <?php endif;?>
        <tr valign="top">
					<td width="120"><b><?php echo $this->translate('Status:'); ?></b></td>
					<td>
            <?php if(empty($this->sitestoreofferDetail->status)):?>
              <?php echo $this->translate('Disabled'); ?>&nbsp;&nbsp;
            <?php else:?>
              <?php echo $this->translate('Enabled'); ?>&nbsp;&nbsp;
            <?php endif;?>
					</td>
				</tr>
        <tr valign="top">
					<td width="120"><b><?php echo $this->translate('Approved:'); ?></b></td>
					<td>
            <?php if(empty($this->sitestoreofferDetail->approved)):?>
              <?php echo $this->translate('Dis-Approved'); ?>&nbsp;&nbsp;
            <?php else:?>
              <?php echo $this->translate('Approved'); ?>&nbsp;&nbsp;
            <?php endif;?>
					</td>
				</tr><tr valign="top">
					<td width="120"><b><?php echo $this->translate('Uses Per Coupon:'); ?></b></td>
					<td>
            <?php if($this->sitestoreofferDetail->claim_count == -1):?>
              <?php echo $this->translate('<i>Unlimited</i>'); ?>&nbsp;&nbsp;
            <?php else:?>
              <?php echo $this->sitestoreofferDetail->claim_count; ?>&nbsp;&nbsp;
            <?php endif;?>
					</td>
				</tr>
        <tr valign="top">
					<td width="120"><b><?php echo $this->translate('User Per Buyer:'); ?></b></td>
					<td>
            <?php if(empty($this->sitestoreofferDetail->claim_user_count)):?>
              <?php echo $this->translate('<i>Unlimited</i>'); ?>&nbsp;&nbsp;
            <?php else:?>
              <?php echo $this->sitestoreofferDetail->claim_user_count; ?>&nbsp;&nbsp;
            <?php endif;?>
					</td>
				</tr>
				<tr valign="top">
					<td width="120"><b><?php echo $this->translate('Description:'); ?></b></td>
					<td>
						<?php echo $this->sitestoreofferDetail->description; ?>&nbsp;&nbsp;
					</td>
				</tr>
				<tr>
					<td></td>
					<td><br /><button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close')  ?></button></td>
				</tr>
			</tr>
		</table>
		<?php if (@$this->closeSmoothbox): ?>
			<script type="text/javascript">
				TB_close();
			</script>
		<?php endif; ?>
		<style type="text/css">
		td{padding:3px; }
		td b{font-weight:bold;}
		</style>
	</div>
</div>
<?php endif;?>