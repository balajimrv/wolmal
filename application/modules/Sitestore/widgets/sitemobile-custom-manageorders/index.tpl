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
try {
	?>
	<?php
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js');
	$order_address_table_obj = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct');
	$orderProductTable = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct');
	$paginationCount = @count($this->paginator);
	?>
	<?php if(empty($this->call_same_action)) : ?>
	<div class="sitestoreproduct_manage_store">

		<h3><?php echo $this->translate('Manage Orders') ?></h3>
		<p class="mbot10"><?php echo $this->translate("Below you can manage all the orders placed from your Store."); ?></p>
		<!--
		<div class="seaocore_searchform_criteria seaocore_searchform_criteria_horizontal">
		  <form method="post" class="field_search_criteria" id="filter_form">
			<div>
			  <ul>
				<li>
				  <span><label> <?php echo $this->translate("Order Id (#)") ?></label></span>
				  <input type="text" name="order_id" id="order_id" /> 
				</li>
				<li>
				  <span><label><?php echo  $this->translate("Buyer Name") ?></label></span>
				  <input type="text" name="username" id="username"/> 
				</li>      
				<li id="integer-wrapper">
				  <span><label><?php echo  $this->translate("Order Date : ex (2000-12-25)") ?></label></span>
				  <div class="form-element"> 
					<input type="text" name="creation_date_start" id="creation_date_start" placeholder="<?php echo $this->translate("from"); ?>"/> 
				  </div>
				  <div class="form-element"> 
					<input type="text" name="creation_date_end" id="creation_date_end" placeholder="<?php echo $this->translate("to"); ?>"/> 
				  </div>
				</li>
				<li>
				  <span><label><?php echo  $this->translate("Billing Name") ?></label></span>
				  <input type="text" name="billing_name" id="billing_name" />
				</li>
				<li>
				  <span><label><?php echo  $this->translate("Shipping Name") ?></label> </span>
				  <input type="text" name="shipping_name" id="shipping_name" />
				</li>
				<li id="integer-wrapper">
				  <label><?php echo  $this->translate("Order Total") ?></label>
				  <div class="form-element">
					<input type="text" name="order_min_amount" id="order_min_amount" placeholder="min"/>
				  </div>
				  <div class="form-element">
					<input type="text" name="order_max_amount" id="order_max_amount" placeholder="max"/> 	      
				  </div>
				</li>
				<li id="integer-wrapper">
				  <label><?php echo  $this->translate("Commission") ?></label>
				  <div class="form-element">
					<input type="text" name="commission_min_amount" id="commission_min_amount" placeholder="min"/>
				  </div>
				  <div class="form-element">
					<input type="text" name="commission_max_amount" id="commission_max_amount" placeholder="max"/> 	      
				  </div>
				</li>
				<li>
				  <span><label><?php echo  $this->translate("Delivery Time (In Days)") ?></label></span>
				  <input type="text" name="delivery_time" id="delivery_time" />
				</li>
				<li>
				  <span><label><?php echo  $this->translate("Status") ?>	</label></span>
				  <select id="order_status" name="order_status" >
					<option value="0" ></option>
					<?php for( $index = 0; $index < 7; $index++ ):
			echo '<option value="' . ($index+1) . '">' . $this->translate("%s", $this->getOrderStatus($index)) . '</option>';
		endfor; ?>
				   </select>
				</li>
				<?php if( empty($this->isPaymentToSiteEnable) && !empty($this->isDownPaymentEnable) ) : ?>
				  <li>
					<span><label><?php echo  $this->translate("Downpayment") ?>	</label></span>
					<select id="downpayment" name="downpayment" >
					  <option value="0" ></option>
					  <option value="1" <?php if( $this->downpayment == 1) echo "selected";?> >
						<?php echo $this->translate("Yes, with downpayment") ?>
					  </option>
					  <option value="2" <?php if( $this->downpayment == 2) echo "selected";?> >
						<?php echo $this->translate("Yes, with downpayment and remaining amount payment completed") ?>
					  </option>
					  <option value="3" <?php if( $this->downpayment == 3) echo "selected";?> >
						<?php echo $this->translate("Yes, with downpayment and remaining amount payment not completed") ?>
					  </option>
					  <option value="4" <?php if( $this->downpayment == 4) echo "selected";?> >
						<?php echo $this->translate("No, without downpayment") ?>
					  </option>
					</select>
				  </li>
				<?php endif; ?>
				<li class="clear mtop10">
				  <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>        
				</li>
				<li>
				  <span id="search_spinner"></span>
				</li>
			  </ul>
				</div>
		  </form>
		</div>
		-->

		<div id="manage_order_pagination">  <?php endif; ?>
			<?php if($paginationCount): ?>
				<div class="mbot5">
					<?php echo $this->translate('%s order(s) found.', $this->total_item) ?>
				</div>
			<?php endif; ?>
			<div id="manage_order_tab">
				<?php if($paginationCount): ?>
				<div class="product_detail_table sitestoreproduct_data_table fleft mbot10">
					<table>
						<tr class="product_detail_table_head">
							<th class="txt_center"><?php echo $this->translate('Order Id') ?></th>
							<th><?php echo $this->translate('Buyer') ?></th>
							<th><?php echo $this->translate('Billing Name') ?></th>
							<th><?php echo $this->translate('Shipping Name') ?></th>
							<!--<th><?php echo $this->translate('Order Date') ?></th>-->
							<th class="txt_center"><?php echo $this->translate('Qty') ?></th>
							<th class="txt_right"><?php echo $this->translate('Order Total') ?></th>
							<!--<th class="txt_right"><?php echo $this->translate('Commision') ?></th>-->
							<th><?php echo $this->translate('Status') ?></th>
							<th class="txt_center"><?php echo $this->translate('Payment') ?></th>
							<!--<th><?php echo $this->translate('Delivery Time') ?></th>-->
							<!--<th><?php echo $this->translate('Options') ?></th>-->
						</tr>
						<?php foreach( $this->paginator as $item ): ?>
							<?php $billing_address_obj = $order_address_table_obj->getAddress($item->order_id);
							$shipping_address_obj = $order_address_table_obj->getAddress($item->order_id, true, array('address_type' => 1));

							if( $item->order_status == 8 ) :
								$payment_status = $this->translate('marked as non-payment');
							elseif( $item->payment_status == 'active' ) :
								$payment_status = 'Yes';
							else:
								$payment_status = 'No';
							endif;

							if( $item->order_status == 2 || $item->order_status == 3 || $item->order_status == 4  ) :
								$delivery_time = empty ($item->delivery_time) ? '-' : $item->delivery_time;
							else:
								$delivery_time = '-';
							endif;
							?>
							<tr>
								<td><a href="javascript:void(0)" onclick="window.location.href='<?php echo $this->url(array("module" => "sitestoreproduct", "controller" => "index", "action" => "order-view", "order_id" => $item->order_id, "store_id" => $this->store_id),"default", true);?>';return false;"><?php echo "#" . $item->order_id; ?></a></td>
								<td><?php echo empty($item->buyer_id) ? $this->translate('Guest') : ($this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($item->getOwner()->getTitle(), 10), array('title' => $item->getOwner()->getTitle(), 'target' => '_blank'))); ?></td>
								<td><?php echo $billing_address_obj->f_name . ' ' . $billing_address_obj->l_name ?></td>
								<td>
									<?php if( !empty($shipping_address_obj) ) : ?>
										<?php echo $shipping_address_obj->f_name . ' ' . $shipping_address_obj->l_name ?>
									<?php else: ?>
										<?php echo '-'; ?>
									<?php endif; ?>
								</td>
								<!--<td><?php echo $this->locale()->toDateTime($item->creation_date); ?></td>-->
								<td class="txt_center"><?php echo $this->locale()->toNumber($item->item_count); ?></td>
								<td class="txt_right"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->grand_total); ?></td>
								<!--<td class="txt_right"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->commission_value); ?></td>-->
								<td>
									<?php $tempStatus = $this->getOrderStatus($item->order_status, true); ?>
									<?php if( !empty($this->canEdit) ) : ?>
										<?php if( $item->order_status == 8 ) : ?>
											<i>-</i>
										<?php else: ?>
											<div style="min-width:100px">
												<div class="<?php echo $tempStatus['class'] ?> fleft" id="current_order_status_<?php echo $item->order_id ?>">
													<i><?php echo $tempStatus['title']; ?></i>
												</div>
												<?php //if( ($item->order_status >1 ) && ($item->order_status < 5)): ?>
												<div id="image_link_<?php echo $item->order_id ?>" class="fleft pleft10">
													<a id="change_status_title_<?php echo $item->order_id ?>" title="<?php echo $this->translate("Open Status Form") ?>" href="javascript:void(0)" onclick="orderStatus(<?php echo $item->order_id ?>)">
														<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/arrow-btm.png" />
													</a>
												</div>
												<div id="order_status_<?php echo $item->order_id ?>" style="display:none" class="clr">
													<select id="order_<?php echo $item->order_id; ?>_status_change" class="mbot5 mtop5 clr">
														<?php for($index = 2; $index < 6; $index++):
															$selected = ($item->order_status == $index) ? "selected" : "";
															echo '<option value="'.$index.'" '.$selected.'>'.$this->getOrderStatus($index).'</option>';
														endfor; ?>
													</select>
													<div>
														<input type="checkbox" checked="checked" id="notify_buyer_<?php echo $item->order_id ?>"/><?php echo $this->translate("Notify and Email to Buyer") ?>
														<a href="javascript:void(0)" onclick="statusChange(<?php echo $item->order_id; ?>)"><?php echo $this->translate("Change") ?></a>
													</div>
													<div id="loading_image_<?php echo $item->order_id ?>" style="display: none">
														<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" height="15" width="15" />
													</div>
												</div>
												<?php //endif; ?>
											</div>
										<?php endif; ?>
									<?php else: ?>
										<?php if( $item->order_status == 8 ) : ?>
											<i>-</i>
										<?php else: ?>
											<div class="<?php echo $tempStatus['class'] ?> fleft">
												<i><?php echo $tempStatus['title']; ?></i>
											</div>
										<?php endif; ?>
									<?php endif; ?>
								</td>
								<td class="txt_center"><?php echo $this->translate($payment_status); ?></td>
								<!--<td title="<?php echo $delivery_time ?>"><?php echo Engine_Api::_()->sitestoreproduct()->truncation($delivery_time, 12); ?></td>-->
								<!--
				  <td>
					<?php if( empty($this->isPaymentToSiteEnable) && !empty($this->canEdit)) : ?>
					  <?php if( $item->non_payment_admin_reason != 1 ) : ?>
						<?php if( !empty($item->direct_payment) && (($item->gateway_id == 3 && empty($item->order_status)) || (($item->gateway_id == 4 || $item->gateway_id == 2) && $item->order_status == 1) ) ) : ?>
						  <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'payment-approve', 'order_id' => $item->order_id, 'gateway_id' => $item->gateway_id), 'default', true) ?>');">
							<?php echo $this->translate("approve payment") ?>
						  </a> |
						<?php elseif( !empty($item->direct_payment) && !empty($this->isDownPaymentEnable) && !empty($item->direct_payment) && $item->is_downpayment == 1 && ( $item->gateway_id == 4 || ( ($item->gateway_id == 2 || $item->gateway_id == 3) && $item->payment_status == 'active') ) ): ?>
						  <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'approve-remaining-amount-payment', 'order_id' => $item->order_id, 'gateway_id' => $item->gateway_id), 'default', true) ?>');">
							<?php echo $this->translate("approve remaining amount payment") ?>
						  </a> |
						<?php endif;?>
						<?php if( !empty($item->direct_payment) && ($item->order_status < 2) ) : ?>
						  <?php if( empty($item->non_payment_seller_reason) ) : ?>
							<a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'non-payment-order', 'order_id' => $item->order_id), 'default', true) ?>');"><?php echo $this->translate("mark as non-payment") ?></a> |
						  <?php endif; ?>
						<?php endif;?>
					  <?php endif; ?>
					<?php endif; ?>
					
					<a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'order-detail', 'order_id' => $item->order_id), 'default', true) ?>')"><?php echo $this->translate("details") ?></a> | 
					<a href="javascript:void(0)" onclick="manage_store_dashboard(55, 'order-view/order_id/<?php echo $item->order_id; ?>', 'index')"><?php echo $this->translate("view") ?></a> 
					<?php if( ($item->order_status > 1) && ($item->order_status != 6) && ($item->order_status != 8) ): ?>
					  <?php $anyOtherProducts = $orderProductTable->checkProductType(array('order_id' => $item->order_id, 'virtual' => true)); ?>
					  <?php $bundleProductShipping = $orderProductTable->checkBundleProductShipping(array('order_id' => $item->order_id)); ?>
					  <?php if( !empty($anyOtherProducts) && empty($bundleProductShipping) ) : ?>
						| <a href="javascript:void(0)" onclick="manage_store_dashboard(55, 'order-ship/order_id/<?php echo $item->order_id; ?>', 'index')"><?php echo $this->translate("shipping details") ?></a>
						| <a href="sitestoreproduct/index/print-packing-slip/order_id/<?php echo Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($item->order_id);?>" target="_blank"><?php echo $this->translate("print packing slip") ?></a>
					  <?php endif; ?>
					  | <a href="sitestoreproduct/index/print-invoice/order_id/<?php echo Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($item->order_id);?>" target="_blank"><?php echo $this->translate("print invoice") ?></a>                  
					<?php endif; ?>
					<?php /*//if( !empty($item->direct_payment) && $item->order_status != 5 && $item->order_status != 6 && $item->order_status != 8 && !empty($this->canEdit)) : ?>
						<span id="order_cancel_<?php //echo $item->order_id ?>">| <a href="javascript:void(0)" onclick="Smoothbox.open('<?php //echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'order-cancel', 'order_id' => $item->order_id, 'store_id' => $this->store_id), 'default', true) ?>')"><?php //echo $this->translate("cancel") ?></a></span>
					<?php //endif;*/ ?>
				  </td>
				  -->
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
			</div>
			<div class="clr dblock sitestoreproduct_data_paging">
				<?php echo $this->paginationControl($this->paginator, null); ?>
				<?php else: ?>
					<div class="tip"><span>
		  <?php echo $this->translate('There are no orders placed in this store yet.') ?>
			</span></div>
				<?php endif; ?>
			</div>
			<?php if(empty($this->call_same_action)) : ?>
		</div>
	</div>
<?php endif; ?>

	<script type="text/javascript">
		function orderStatus(order_id)
		{
			if(document.getElementById("order_status_"+order_id).style.display == 'block'){
				document.getElementById("order_status_"+order_id).style.display = 'none';
			} else {
				document.getElementById("order_status_"+order_id).style.display = 'block';
			}

			// if( document.getElementById("order_status_"+order_id).style.display === 'block' )
			// $('change_status_title_'+order_id).setProperties({title: '<?php echo $this->translate("Close Status Form") ?>'});
			// else
			// $('change_status_title_'+order_id).setProperties({title: '<?php echo $this->translate("Open Status Form") ?>'});
		}

		function statusChange(order_id)
		{
			document.getElementById('loading_image_'+order_id).style.display = 'block';
			$.ajax({
				dataType: "json",
				url : '<?php echo $this->layout()->staticBaseUrl;?>' + 'sitestoreproduct/product/change-order-status',
				data : {
					'format' : 'json',
					'order_id' : order_id,
					'status' : document.getElementById('order_'+order_id+'_status_change').value,
					'notify_buyer' : document.getElementById('notify_buyer_'+order_id).checked
				},
				success: function(responseJSON)
				{
					if( responseJSON.order_status_no == 5  )
						document.getElementById('image_link_'+order_id).style.display = 'none';
					document.getElementById('current_order_status_'+order_id).style.display = 'block';
					document.getElementById('current_order_status_'+order_id).className = responseJSON.status_class + ' fleft';
					document.getElementById('current_order_status_'+order_id).innerHTML = '<i>' + responseJSON.status + '</i>';
					document.getElementById('order_status_'+order_id).style.display = 'none';
					document.getElementById('order_'+order_id+'_status_change').value = responseJSON.order_status_no;
					document.getElementById('loading_image_'+order_id).style.display = 'none';
				}
			});
		}
	</script>

	<?php
} catch(Exception $e){
	// var_dump($e);die;
	throw $e;
}
?>