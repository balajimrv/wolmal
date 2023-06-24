<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage-orders.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js'); ?>

<script type="text/javascript">

    var detail_id;
    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function (order, default_direction) {
        if (order == currentOrder) {
            $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
        }
        else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }
        $('filter_form').submit();
    }

    function findApprovalPendingOrders()
    {
        $('order_status').set('value', 1);
        $('display_only_site_payment_orders').set('value', 1);
        document.getElementById('manage_orders_search_form').submit();
    }



</script>


<h2 class="fleft">
    <?php echo $this->translate('Stores / Marketplace - Ecommerce Plugin'); ?>
</h2>


<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<h3 style="margin-bottom:6px;"><?php echo $this->translate("Manage Orders"); ?></h3>
<p>
    <?php echo $this->translate('Below, you can manage and monitor orders placed by the buyers (users) on your site. All these orders will be fulfilled by their respective store owners.
<br />If buyers have placed their orders by using "By Cheque" payment method, then you need to approve the payment as seller will only be able to take actions on those orders after approval of payments for such orders.') ?>
</p>
<br style="clear:both;" />

<div class="admin_search sitestoreproduct_admin_search">
    <div class="search">
        <form name="manage_orders_search_form" id="manage_orders_search_form" method="post" class="global_form_box" action="">
            <input type="hidden" name="post_search" />
            <input type="hidden" name="display_only_site_payment_orders" value="0" id="display_only_site_payment_orders" /> 
            <div>
                <label>
                    <?php echo $this->translate("Buyer Name") ?>
                </label>
                <?php if (empty($this->username)): ?>
                    <input type="text" name="username" /> 
                <?php else: ?>
                    <input type="text" name="username" value="<?php echo $this->username ?>"/>
                <?php endif; ?>
            </div>

            <div>
                <label>
                    <?php echo $this->translate("Store") ?>
                </label>
                <?php if (empty($this->title)): ?>
                    <input type="text" name="title" /> 
                <?php else: ?>
                    <input type="text" name="username" value="<?php echo $this->title ?>"/>
                <?php endif; ?>
            </div>

            <div>
                <label><?php echo $this->translate("Order Date: ex (2000-12-25)") ?></label>
                <div>
                    <?php if ($this->creation_date_start == ''): ?>
                        <input type="text" name="creation_date_start" placeholder="from" class="input_field_small" /> 
                    <?php else: ?>
                        <input type="text" name="creation_date_start" placeholder="from" value="<?php echo $this->creation_date_start ?>" class="input_field_small" />
                    <?php endif; ?>

                    <?php if ($this->creation_date_end == ''): ?>
                        <input type="text" name="creation_date_end" placeholder="to" class="input_field_small" /> 
                    <?php else: ?>
                        <input type="text" name="creation_date_end" placeholder="to" value="<?php echo $this->creation_date_end ?>" class="input_field_small" />
                    <?php endif; ?>
                </div>   
            </div>

            <div>
                <label><?php echo $this->translate("Billing Name") ?></label>
                <?php if (empty($this->billing_name)): ?>
                    <input type="text" name="billing_name"  /> 
                <?php else: ?>
                    <input type="text" name="billing_name" value="<?php echo $this->billing_name ?>"  />
                <?php endif; ?>
            </div>

            <div>
                <label><?php echo $this->translate("Shipping Name") ?></label>
                <?php if (empty($this->shipping_name)): ?>
                    <input type="text" name="shipping_name"  /> 
                <?php else: ?>
                    <input type="text" name="shipping_name" value="<?php echo $this->shipping_name ?>"  />
                <?php endif; ?>
            </div>

            <div>
                <label><?php echo $this->translate("Order Amount") ?></label>
                <div>
                    <?php if ($this->order_min_amount == ''): ?>
                        <input type="text" name="order_min_amount" placeholder="min" class="input_field_small" /> 
                    <?php else: ?>
                        <input type="text" name="order_min_amount" placeholder="min" value="<?php echo $this->order_min_amount ?>" class="input_field_small" />
                    <?php endif; ?>

                    <?php if ($this->order_max_amount == ''): ?>
                        <input type="text" name="order_max_amount" placeholder="max" class="input_field_small" /> 
                    <?php else: ?>
                        <input type="text" name="order_max_amount" placeholder="max" value="<?php echo $this->order_max_amount ?>" class="input_field_small" />
                    <?php endif; ?>
                </div>   
            </div>

            <div>
                <label><?php echo $this->translate("Commission") ?></label>
                <div>
                    <?php if ($this->commission_min_amount == ''): ?>
                        <input type="text" name="commission_min_amount" placeholder="min" class="input_field_small" /> 
                    <?php else: ?>
                        <input type="text" name="commission_min_amount" placeholder="min" value="<?php echo $this->commission_min_amount ?>" class="input_field_small" />
                    <?php endif; ?>

                    <?php if ($this->commission_max_amount == ''): ?>
                        <input type="text" name="commission_max_amount" placeholder="max" class="input_field_small" /> 
                    <?php else: ?>
                        <input type="text" name="commission_max_amount" placeholder="max" value="<?php echo $this->commission_max_amount ?>" class="input_field_small" />
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label><?php echo $this->translate("Status") ?></label>
                <select id="order_status" name="order_status">
                    <option value="0" ><?php echo $this->translate("Select") ?></option>
                    <?php
                    for ($index = 0; $index < 7;):
                        if ($this->order_status == ($index + 1)):
                            $selected = "selected";
                        else:
                            $selected = "";
                        endif;

                        echo '<option value="' . ($index + 1) . '" ' . $selected . '>' . $this->translate("%s", $this->getOrderStatus($index++)) . '</option>';
                    endfor;
                    ?>

                </select>
            </div>

            <?php $otherOptionsString = $selected = ''; ?>
            <?php if (Engine_Api::_()->hasModuleBootstrap('sitegateway')): ?>
                <?php $enableGateways = Engine_Api::_()->getDbTable('gateways', 'payment')->getEnabledGateways(); ?>
                <?php foreach ($enableGateways as $gateway): ?>
                    <?php if (!strstr($gateway->plugin, 'Sitegateway_')) continue; ?>
                    <?php if ($this->payment_gateway == $gateway->gateway_id): ?>
                        <?php $selected = 'selected' ?>
                    <?php endif; ?>
                    <?php $otherOptionsString .= "<option value='$gateway->gateway_id' $selected>$gateway->title</option>"; ?>

    <?php endforeach; ?>
<?php endif; ?>        

            <div>
                <label><?php echo $this->translate("Payment Gateway") ?>	</label>
                <select id="payment_gateway" name="payment_gateway">
<?php if (empty($this->directPayment)) : ?>
                        <option value="0" ><?php echo $this->translate("Select") ?></option>
                        <option value="1" <?php if ($this->payment_gateway == 1) echo "selected"; ?> ><?php echo $this->translate("2Checkout") ?></option>
                        <option value="2" <?php if ($this->payment_gateway == 2) echo "selected"; ?> ><?php echo $this->translate("PayPal") ?></option>
    <?php echo $otherOptionsString; ?>
                        <option value="3" <?php if ($this->payment_gateway == 3) echo "selected"; ?> ><?php echo $this->translate("By Cheque") ?></option>
                        <option value="4" <?php if ($this->payment_gateway == 4) echo "selected"; ?> ><?php echo $this->translate("Cash on Delivery") ?></option>
                        <option value="5" <?php if ($this->payment_gateway == 5) echo "selected"; ?> ><?php echo $this->translate("Free Order") ?></option>
                    <?php else: ?>
                        <option value="0" ><?php echo $this->translate("Select") ?></option>
                        <option value="2" <?php if ($this->payment_gateway == 2) echo "selected"; ?> ><?php echo $this->translate("PayPal") ?></option>
    <?php echo $otherOptionsString; ?>
                        <option value="3" <?php if ($this->payment_gateway == 3) echo "selected"; ?> ><?php echo $this->translate("By Cheque") ?></option>
                        <option value="4" <?php if ($this->payment_gateway == 4) echo "selected"; ?> ><?php echo $this->translate("Cash on Delivery") ?></option>
                        <option value="5" <?php if ($this->payment_gateway == 5) echo "selected"; ?> ><?php echo $this->translate("Free Order") ?></option>
<?php endif; ?>
                </select>
            </div>

<?php if (!empty($this->isDownPaymentEnable)) : ?>
                <div>
                    <label><?php echo $this->translate("Downpayment") ?>	</label>
                    <select id="downpayment" name="downpayment">
                        <option value="0" ><?php echo $this->translate("Select") ?></option>
                        <option value="1" <?php if ($this->downpayment == 1) echo "selected"; ?> ><?php echo $this->translate("Yes, with downpayment") ?></option>
                        <option value="2" <?php if ($this->downpayment == 2) echo "selected"; ?> ><?php echo $this->translate("Yes, with downpayment and remaining amount payment completed") ?></option>
                        <option value="3" <?php if ($this->downpayment == 3) echo "selected"; ?> ><?php echo $this->translate("Yes, with downpayment and remaining amount payment not completed") ?></option>
                        <option value="4" <?php if ($this->downpayment == 4) echo "selected"; ?> ><?php echo $this->translate("No, without downpayment") ?></option>
                    </select>
                </div>
<?php endif; ?>

            <div>
                <label><?php echo $this->translate("Cheque No") ?></label>
                <input type="text" name="cheque_no" id="cheque_no" value="<?php echo empty($this->cheque_no) ? "" : $this->cheque_no; ?>"/> 
            </div>

            <div style="margin-top:16px;">
                <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
            </div>

        </form>
    </div>
</div>

<div class='admin_search'>
<?php echo $this->formFilter->render($this) ?>
</div>
<br />

<!--<form id="approval_pending_orders" name="approval_pending_orders" action="<?php //echo $this->url(array("module" => "sitestoreproduct", "controller" => "manage", "action" => "manage-orders" ), "admin_default", true)  ?>">
  <input type="hidden" id="order_status" name="order_status" value="1" />
</form>-->

    <?php if (!empty($this->order_approve_count) && empty($this->directPayment)) : ?>
    <h3>
        <?php echo $this->translate("There %s awaiting your approval before %s can be fulfilled by the respective seller.", $this->translate(array('is %s order', 'are %s orders', $this->order_approve_count), '<a href="javascript:void(0); " onclick="findApprovalPendingOrders()"> ' . $this->order_approve_count . '</a>'), $this->translate(array('that', 'they', $this->order_approve_count))); ?>
        <?php //echo  ?>
        <?php //echo $this->translate(" awaiting your approval before  "); ?>
    <?php //echo $this->translate(array('that', 'they', $this->order_approve_count))  ?>
    <?php //echo $this->translate("can be fulfilled by the respective seller."); ?>
    </h3>
<?php endif; ?>


<div class='admin_members_results'>
    <?php
    if (!empty($this->paginator)) {
        $counter = $this->paginator->getTotalItemCount();
    }
    if (!empty($counter)):
        ?>
        <div class="">
        <?php echo $this->translate(array('%s order found.', '%s orders found.', $counter), $this->locale()->toNumber($counter)) ?>
        </div>
            <?php else: ?>
        <div class="tip"><span>
        <?php echo $this->translate("There are no orders available.") ?></span>
        </div>
<?php endif; ?> 
</div>
<br />

<?php
if (!empty($counter)):
    $orderProductTable = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct');
    $order_address_table_obj = Engine_Api::_()->getDbtable('orderaddresses', 'sitestoreproduct');
    $orderProductTable = Engine_Api::_()->getDbtable('orderProducts', 'sitestoreproduct');
    ?>
    <div style="overflow-x:scroll;">
        <table class='admin_table seaocore_admin_table' width="100%">
            <thead>
                <tr>
                    <?php $class = ( $this->order == 'order_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('order_id', 'DESC');"><?php echo $this->translate('Id'); ?></a></th>

                    <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Store Name'); ?></a></th>

    <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate('Buyer'); ?></a></th>

                    <th class='admin_table_short'><?php echo $this->translate("Billing Name") ?></th>
                    <th class='admin_table_short'><?php echo $this->translate("Shipping Name") ?></th>          
                    <th class='admin_table_short'><?php echo $this->translate('Product Qty') ?></th>

                    <?php $class = ( $this->order == 'grand_total' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('grand_total', 'DESC');"><?php echo $this->translate('Order Total'); ?></a></th>

    <?php $class = ( $this->order == 'commission_value' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('commission_value', 'DESC');"><?php echo $this->translate('Commission'); ?></a></th>

                    <th class='admin_table_short'><?php echo $this->translate('Status') ?></th>
                    <th class='admin_table_short'><?php echo $this->translate('Payment') ?></th>
                    <th class='admin_table_short'><?php echo $this->translate('Refund') ?></th>

    <?php $class = ( $this->order == 'delivery_time' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('delivery_time', 'DESC');"><?php echo $this->translate('Delivery Time'); ?></a></th>

                    <th class='admin_table_short'><?php echo $this->translate('Purchased') ?></th>
                    <th class='admin_table_short'><?php echo $this->translate('Options') ?></th>
                </tr>	
            </thead>
                <?php foreach ($this->paginator as $item): ?>
                <tbody>
                <td>
                    <?php
                    $storeItem = $this->item('sitestore_store', $item->store_id);
                    if (empty($storeItem)) :
                        echo "#" . $item->order_id;
                    else:
                        $order_view_url = $this->url(array('action' => 'store', 'store_id' => $item->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-view', 'order_id' => $item->order_id, 'admin_calling' => 1), 'sitestore_store_dashboard', false);
                        echo $this->htmlLink($order_view_url, "#" . $item->order_id, array('target' => '_blank'));
                    endif;
                    ?>          
                </td>
                <?php if (empty($storeItem)): ?>
                    <td><i>Store Deleted</i></td>
                    <?php else: ?>
                    <td><?php echo $this->htmlLink($storeItem->getHref(), $this->string()->truncate($this->string()->stripTags($item->getTitle()), 10), array('title' => $item->getTitle(), 'target' => '_blank')) ?></td>
                    <?php endif; ?>
                <td>
                    <?php
                    // IF BUYER IS LOGGED-OUT USER
                    if (empty($item->buyer_id)) :
                        echo $this->translate('Guest');
                    else :
                        echo $this->htmlLink($item->getOwner()->getHref(), $this->string()->truncate($this->string()->stripTags($item->getOwner()->getTitle()), 10), array('title' => $item->getOwner()->getTitle(), 'target' => '_blank'));
                    endif;

                    $billing_address_obj = $order_address_table_obj->getAddress($item->order_id, false);
                    $shipping_address_obj = $order_address_table_obj->getAddress($item->order_id, true, array('address_type' => 1));

                    if ($item->order_status == 8) :
                        $payment_status = 'marked as non-payment';
                    elseif ($item->payment_status == 'active') :
                        $payment_status = 'Yes';
                    else:
                        $payment_status = 'No';
                    endif;

                    if ($item->order_status == 2 || $item->order_status == 3 || $item->order_status == 4) :
                        $delivery_time = empty($item->delivery_time) ? '-' : $item->delivery_time;
                    else:
                        $delivery_time = '-';
                    endif;
                    ?>
                </td>

                <td><?php echo $billing_address_obj->f_name . ' ' . $billing_address_obj->l_name ?></td>
                <td>
                    <?php if (!empty($shipping_address_obj)) : ?>
            <?php echo $shipping_address_obj->f_name . ' ' . $shipping_address_obj->l_name ?>
        <?php else: ?>
            <?php echo '-'; ?>
        <?php endif; ?>
                </td>
                <td class="admin_table_centered"><?php echo $this->locale()->toNumber($item->item_count); ?></td>
                <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($item->grand_total); ?></td>
                <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrencyAdmin($item->commission_value); ?></td>

                <td>
        <?php if ($item->order_status == 8) : ?>
                        <i>-</i>
                    <?php else: ?>
                        <?php $tempStatus = $this->getOrderStatus($item->order_status, false, true); ?>
                        <div style="min-width:100px">
                            <div class="<?php echo $tempStatus['class'] ?> fleft" id="current_order_status_<?php echo $item->order_id ?>"><i><?php echo $tempStatus['title']; ?></i></div></div>
            <?php if (!empty($storeItem)): ?>
                <?php if (($item->order_status > 1) && ($item->order_status < 5)): ?>
                                <div id="image_link_<?php echo $item->order_id ?>" class="fleft mleft5">
                                    <a id="change_status_title_<?php echo $item->order_id ?>" title="<?php echo $this->translate("Open Status Form") ?>" href="javascript:void(0)" onclick="orderStatus(<?php echo $item->order_id ?>)">
                                        <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/arrow-btm.png" />
                                    </a>
                                </div>
                                <div id="order_status_<?php echo $item->order_id ?>" style="display: none">
                                    <select id="order_<?php echo $item->order_id; ?>_status_change" class="mbot5 mtop5 clr">
                                        <?php
                                        for ($index = 2; $index < 6; $index++):
                                            if ($item->order_status == $index):
                                                $selected = "selected";
                                            else:
                                                $selected = "";
                                            endif;

                                            echo '<option value="' . $index . '" ' . $selected . '>' . $this->getOrderStatus($index) . '</option>';
                                        endfor;
                                        ?>
                                    </select>
                                    <input type="checkbox" checked="checked" id="notify_buyer_<?php echo $item->order_id ?>"/>Notify and Email to Buyer
                                    <input type="checkbox" checked="checked" id="notify_seller_<?php echo $item->order_id ?>"/>Notify and Email to Seller
                                    <a href="javascript:void(0)" onclick="statusChange(<?php echo $item->order_id; ?>)">Change</a>
                                    <div id="loading_image_<?php echo $item->order_id ?>" style="display: none">
                                        <img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitestoreproduct/externals/images/loading.gif" height="15" width="15" />
                                    </div>
                                </div>
                <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td><?php echo $this->translate("%s", $payment_status); ?></td>
                <td><?php echo empty($item->refund_status)?'-':$this->translate("%s", $item->refund_status); ?></td>
                <td title="<?php echo $delivery_time ?>"><?php echo Engine_Api::_()->sitestoreproduct()->truncation($delivery_time, 12) ?></td>
                <td><?php echo $this->locale()->toDateTime($item->creation_date); ?></td>
                <td>
                    <?php if (!empty($storeItem)): ?>
                        <?php if (empty($item->direct_payment) && $item->order_status != 8) : ?>
                            <?php
                            if (($item->gateway_id == 3) && empty($item->order_status)) :
                                echo '<font color="red"><a href="javascript:void(0)" onclick="Smoothbox.open(\'' . $this->url(array('action' => 'payment-approve', 'order_id' => $item->order_id)) . '\')">approve payment</a></font> | ';
                            elseif ($item->order_status == 1) :
                                echo '<font color="red"><a href="javascript:void(0)" onclick="Smoothbox.open(\'' . $this->url(array('action' => 'payment-approve', 'order_id' => $item->order_id, 'payment_pending' => 1)) . '\')">approve payment</a></font> | ';
                            endif;
                            ?>
                        <?php elseif (!empty($item->direct_payment) && ($item->gateway_id == 3 && empty($item->order_status))): ?>
                            <font class="seaocore_txt_light"><?php echo 'seller approval pending |'; ?></font>
                        <?php endif; ?>
                        <?php echo '<a href="javascript:void(0)" onclick="Smoothbox.open(\'' . $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'order-detail', 'order_id' => $item->order_id), 'default', true) . '\')">details</a> '; ?>

                        <?php if (!empty($storeItem)) : ?>
                            <?php echo ' | ' . $this->htmlLink($order_view_url, $this->translate('view'), array('target' => '_blank')) ?> 
                        <?php endif; ?>
                        <?php if ($item->order_status != 8) : ?>
                            <?php if (($item->order_status > 1) && ($item->order_status != 6)): ?>
                                <?php
                                $order_shipment_url = $this->url(array('module' => 'sitestore', 'controller' => 'dashboard', 'action' => 'store', 'store_id' => $item->store_id, 'type' => 'index', 'menuId' => 55, 'method' => 'order-ship', 'order_id' => $item->order_id), 'default', true);
                                ?>
                                <?php $anyOtherProducts = $orderProductTable->checkProductType(array('order_id' => $item->order_id, 'virtual' => true)); ?>
                                <?php $bundleProductShipping = $orderProductTable->checkBundleProductShipping(array('order_id' => $item->order_id)); ?>
                                <?php if (!empty($anyOtherProducts) && empty($bundleProductShipping)) : ?>
                                    <?php if (!empty($storeItem)) : ?>
                                        <?php echo ' | ' . $this->htmlLink($order_shipment_url, $this->translate('shipping details'), array('target' => '_blank')) ?> 
                                    <?php endif; ?>
                                    <?php echo ' | ' . $this->htmlLink($this->url(array('action' => 'print-packing-slip', 'order_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($item->order_id)), 'sitestoreproduct_general', true), $this->translate('print packing slip'), array('target' => '_blank')) ?>
                                <?php endif; ?>
                                <?php echo ' | ' . $this->htmlLink($this->url(array('action' => 'print-invoice', 'order_id' => Engine_Api::_()->sitestoreproduct()->getDecodeToEncode($item->order_id)), 'sitestoreproduct_general', true), $this->translate('print invoice'), array('target' => '_blank')) ?> 
                                | <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('action' => 'send-invoice', 'order_id' => $item->order_id)); ?>')">send invoice</a>
                            <?php endif; ?>
                            <?php if (empty($item->direct_payment) && $item->order_status != 5 && $item->order_status != 6) : ?>
                                <?php $order_cancel_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'order-cancel', 'order_id' => $item->order_id, 'store_id' => $item->store_id, 'admin_calling' => 1), 'default', false); ?>
                                <span id="order_cancel_<?php echo $item->order_id ?>">| <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $order_cancel_url ?>')">cancel</a></span>
                <?php endif; ?>
                    <?php // IF Sitegateway is not enable then follow this condition ?>
                    <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegateway')) : ?>                
                        <?php $status = $item->payoutStatus(); ?>
                        <?php if ($status) : ?>
                                <?php 
                                    switch($status){
                                        case 'both':
                                            echo ' | '.$this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'general', 'action' => 'order-payout', 'store_id' => $item->store_id,'order_id' => $item->order_id,'gateway_id' => $item->gateway_id ), $this->translate('Payout'), array('class' => 'smoothbox'));
                                            echo ' | '.$this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'general', 'action' => 'order-refund', 'store_id' => $item->store_id,'order_id' => $item->order_id,'gateway_id' => $item->gateway_id ), $this->translate('Refund'), array('class' => 'smoothbox'));
                                            break;
                                        case 'payout':
                                            echo ' | '.$this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'general', 'action' => 'order-payout', 'store_id' => $item->store_id,'order_id' => $item->order_id,'gateway_id' => $item->gateway_id ), $this->translate('Payout'), array('class' => 'smoothbox'));
                                            break;
                                        case 'refund':
                                            echo ' | '.$this->htmlLink(array('route' => 'admin_default', 'module' => 'sitestoreproduct', 'controller' => 'general', 'action' => 'order-refund', 'store_id' => $item->store_id,'order_id' => $item->order_id,'gateway_id' => $item->gateway_id ), $this->translate('Refund'), array('class' => 'smoothbox'));
                                            break;
                                    }
                                ?>
                        <?php endif; ?>
                    <?php endif; ?>
            <?php endif; ?>
            <?php else: ?>
                        -
            <?php endif; ?>
                </td>
                </tbody>
        <?php endforeach; ?>
        </table>
    </div>
    <div class="clr mtop10">
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    ));
    ?>
    </div>
<?php endif; ?>

<script type="text/javascript">
    function orderStatus(order_id)
    {
        document.getElementById("order_status_" + order_id).toggle();

        if (document.getElementById("order_status_" + order_id).style.display === 'block')
            $('change_status_title_' + order_id).setProperties({title: 'Close Status Form'});
        else
            $('change_status_title_' + order_id).setProperties({title: 'Open Status Form'});
    }

    function statusChange(order_id)
    {
        document.getElementById('notify_buyer_' + order_id).checked;
        $('loading_image_' + order_id).style.display = 'block';
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'sitestoreproduct/product/change-order-status',
            'data': {
                'format': 'json',
                'order_id': order_id,
                'status': status,
                'status' : $('order_' + order_id + '_status_change').value,
                        'notify_buyer': document.getElementById('notify_buyer_' + order_id).checked,
                'notify_seller': document.getElementById('notify_seller_' + order_id).checked
            },
            onSuccess: function (responseJSON)
            {
                if (responseJSON.order_status_no == 5)
                    document.getElementById('image_link_' + order_id).style.display = 'none';
                document.getElementById('current_order_status_' + order_id).style.display = 'block';
                document.getElementById('current_order_status_' + order_id).set('class', responseJSON.status_class + ' fleft');
                document.getElementById('current_order_status_' + order_id).innerHTML = responseJSON.status;
                document.getElementById('order_status_' + order_id).style.display = 'none';
                document.getElementById('order_' + order_id + '_status_change').value = responseJSON.order_status_no;
                document.getElementById('loading_image_' + order_id).style.display = 'none';
                document.getElementById('change_status_title_' + order_id).setProperties({title: '<?php echo $this->translate("Open Status Form") ?>'});
                if (responseJSON.order_status_no == 5)
                    document.getElementById('order_cancel_' + order_id).style.display = 'none';
            }
        })
                );
    }
</script>