<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: order-view.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
 ?>
   
<?php if( !empty($this->sitestoreproduct_view_no_permission) ) : ?>
  <span class="tip">
    <span>
      <?php echo $this->translate("Order not available for view or you are not permitted to view the order.") ?>
    </span>
  </span>
<?php return; endif; ?>
  <div class="o_hidden">
    <b class="fleft"><?php echo $this->translate('Order Id: #%s', $this->order_id) ?></b>
    <b class="fright"><?php echo $this->translate(' [ Total: %s ]', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->grand_total)) ?></b>
  </div>
  <div class="profile_fields sm-widget-block myorder-details">
    <!-- Billing Information -->
    <h4><?php echo $this->translate('Name & Billing Address') ?></h4>
    <ul> 
      <li>
        <?php echo $this->billing_address->f_name . ' ' . $this->billing_address->l_name; ?>
      </li>
      <li>
        <?php echo $this->billing_address->address; ?>
      </li>
      <li>
        <?php echo @strtoupper($this->billing_address->city) . ' - ' . $this->billing_address->zip ; ?>
      </li>
      <li>
        <?php echo @strtoupper($this->billing_region_name); ?>
      </li>
      <li>
        <?php echo @strtoupper(Zend_Locale::getTranslation($this->billing_address->country, 'country')); ?>
      </li>
      <li>
        <?php echo $this->translate("Ph: %s", $this->billing_address->phone); ?>
      </li>
    </ul>
  </div>
  <div class="profile_fields sm-widget-block myorder-details">
    <!-- Shipping Information -->
    <?php if( !empty($this->orderObj->shipping_title) ) : ?>
      <h4><?php echo $this->translate('Name & Shipping Address') ?></h4>   
      <ul>
        <li>
          <?php echo $this->shipping_address->f_name . ' ' . $this->shipping_address->l_name;; ?>
        </li>
        <li>
          <?php echo $this->shipping_address->address; ?>
        </li>
        <li>
          <?php echo @strtoupper($this->shipping_address->city) . ' - ' . $this->shipping_address->zip ; ?>
        </li>
        <li>
          <?php echo @strtoupper($this->shipping_region_name); ?>
        </li>
        <li>
          <?php echo @strtoupper(Zend_Locale::getTranslation($this->shipping_address->country, 'country')); ?>
        </li>
        <li>
          <?php echo $this->translate("Ph: %s", $this->shipping_address->phone); ?>
        </li>
      </ul>
    <?php endif; ?>
  </div>

  <div class="profile_fields sm-widget-block myorder-details">
    <!-- Order Information -->
    <h4><?php echo $this->translate('Order Information') ?></h4>
    <ul class="o_hidden">
      <li>
        <span><?php echo $this->translate('Order Date') ?></span>
        <span><?php echo gmdate('M d,Y, g:i A',strtotime($this->orderObj->creation_date)); ?></span>
      </li>
      <li>
        <span><?php echo $this->translate('Order Status') ?></span>
        <span><?php echo $this->getOrderStatus($this->orderObj->order_status); ?></span>
      </li>
      <?php if (empty($this->page_user)) : ?>
        <li>
          <span><?php echo $this->translate('Commission Type') ?></span>
          <span><?php echo empty($this->orderObj->commission_type) ? $this->translate('Fixed') : $this->translate('Percentage'); ?></span>
        </li>
        <?php if (!empty($this->orderObj->commission_type)) : ?>
        <li>
          <span><?php echo $this->translate('Commission Rate') ?></span>
          <span><?php echo number_format($this->orderObj->commission_rate, 2).' %'; ?></span>
        </li>
        <?php endif; ?>
        <li>
          <span><?php echo $this->translate('Commission Amount') ?></span>
          <span><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->commission_value); ?></span>
        </li>
        <li>
          <span><?php echo $this->translate('Store Tax Amount') ?></span>
          <span colspan='2'><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->store_tax); ?></span>
        </li>
        <li>
          <span><?php echo $this->translate('Admin Tax Amount') ?></span>
          <span><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->admin_tax); ?></span>
        </li>
      <?php else : ?>
        <li>
          <span><?php echo $this->translate('Tax Amount') ?></span>
          <span><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($this->orderObj->store_tax + $this->orderObj->admin_tax)); ?></span>
        </li>
      <?php endif; ?>
      <?php if( !empty($this->orderObj->shipping_title) ) : ?>
        <li>
          <span><?php echo $this->translate('Shipping Amount') ?></span>
          <span><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->shipping_price); ?></span>
        </li>
        <li>
          <span><?php echo $this->translate('Delivery Time') ?></span>
          <span><?php echo empty($this->orderObj->delivery_time) || $this->orderObj->order_status < 2 ? '-' : $this->orderObj->delivery_time; ?></span>
        </li>
      <?php endif; ?>
      <?php if (empty($this->page_user)) : ?>
        <li>
          <span><?php echo $this->translate('User Type') ?></span>
          <span>
            <?php echo empty($this->orderObj->buyer_id) ? $this->translate('Guest') : $this->translate('Site Member'); ?>
          </span>
        </li>
      
      <li>
        <span><?php echo $this->translate('IP Address') ?></span>
        <span><?php $ipObj = new Engine_IP($this->orderObj->ip_address); echo $ipObj->toString();?></span>
      </li>
      <?php endif; ?>
    </ul>
  </div>
    
  <div class="profile_fields sm-widget-block myorder-details">
    <!-- Payment Information -->
    <h4><?php echo $this->translate('Payment Information') ?></h4>
    <ul>
      <li>
        <span><?php echo $this->translate('Payment Method') ?></span>
        <span><?php echo $this->translate(Engine_Api::_()->sitestoreproduct()->getGatwayName($this->orderObj->gateway_id)); ?></span>
      </li>
      <?php if($this->orderObj->gateway_id == 3): ?>
        <li>
          <span><?php echo $this->translate('Cheque No') ?></span>
          <span><?php echo $this->cheque_info['cheque_no'] ?></span>
        </li>
        <li>
          <span><?php echo $this->translate('Account Holder Name') ?></span>
          <span><?php echo $this->cheque_info['customer_signature'] ?></span>
        </li>
        <li>
          <span><?php echo $this->translate('Account Number') ?></span>
          <span><?php echo $this->cheque_info['account_number'] ?></span>
        </li>
        <li>
          <span><?php echo $this->translate('Bank Rounting Number') ?></span>
          <span><?php echo $this->cheque_info['bank_routing_number'] ?></span>
        </li>
      <?php endif; ?>
    </ul>
  </div>
  <?php if( !empty($this->orderObj->shipping_title) ) : ?>
    <div class="profile_fields sm-widget-block myorder-details">
      <h4><?php echo $this->translate('Shipping Information') ?></h4>
      <ul>
        <li>
          <span><?php echo $this->translate('Shipping Method') ?></span>
          <span><?php echo $this->orderObj->shipping_title; ?></span>
        </li>
      </ul>
    </div>
  <?php endif; ?>

  <div class="clr t_l"></div>
 
  <b><?php echo $this->translate("Order Details") ?></b>
  <div class="profile_fields sm-widget-block myorder-details">
    <?php foreach( $this->orderProducts as $item ): ?>
      <?php $temp_lang_title = Engine_Api::_()->sitestoreproduct()->getProductTitle($item->product_title); ?>
      <h4  title="<?php echo $temp_lang_title ?>">
        <?php echo Engine_Api::_()->sitestoreproduct()->truncation($temp_lang_title, 40) ?>
      </h4>
      <ul>
        <?php if( !empty($item->configuration) ): 
          $configuration = Zend_Json::decode($item->configuration);
          foreach($configuration as $config_name => $config_value):
            echo "<li><span>".$config_name."</span><span>$config_value</span></li>";
          endforeach;
        endif; ?>
          
        <li>
          <span><?php echo $this->translate('SKU') ?></span>
          <span><?php echo empty($item->product_sku) ? '-' : $item->product_sku; ?></span>
        </li>
        
        <li>
          <span><?php echo $this->translate('Price') ?></span>
          <span><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->price); ?></span>
        </li>
        
        <li>
          <span><?php echo $this->translate("Quantity"); ?></span>
          <span><?php echo $item->quantity; ?></span>
        </li>
        
        <li>
          <span><?php echo $this->translate('Tax Amount') ?></span>
          <span> 
            <?php 
              if( empty($item->tax_amount) ):
                echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($item->tax_amount));
              else:
                echo $this->htmlLink('javascript:void(0);', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($item->tax_amount), array('class'=>'sea_add_tooltip_link', 'rel'=>$item->tax_title));        
              endif;
            ?>
          </span>
        </li>
        
        <li>
          <span><?php echo $this->translate('Subtotal') ?></span>
          <span><b><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($item->price * $item->quantity)); ?></b></span>
        </li>
      </ul>
    <?php endforeach; ?> 
  </div>
  
  
  <div class="clr t_l"></div>
  <b><?php echo $this->translate("Order Summary") ?></b>
  <div class="clr sm-widget-block m-cart-items-total">
    <div class="ui-grid-a">
      <div class="ui-block-a">
        <?php echo $this->translate('Subtotal'); ?>
      </div>
      <div class="ui-block-b">
        <?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->sub_total);?>
      </div>
    </div>
      
    <div class="ui-grid-a">
      <div class="ui-block-a">
        <?php echo $this->translate('Shipping cost'); ?>
      </div>
      <div class="ui-block-b">
        <?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->shipping_price);?>
      </div>
    </div>
      
    <div class="ui-grid-a">
      <div class="ui-block-a">
        <?php echo $this->translate('Taxes'); ?>
      </div>
      <div class="ui-block-b">
        <?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency(($this->orderObj->admin_tax + $this->orderObj->store_tax));?>
      </div>
    </div>
    <div class="cont-sep b_medium"></div>
    <div class="ui-grid-a">
      <div class="ui-block-a">
        <strong><?php echo $this->translate('Grand Total'); ?></strong>
      </div>
      <div class="ui-block-b">
        <strong><?php  echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->orderObj->grand_total);?></strong>
      </div>
    </div>
  </div>
  
  <?php if(!empty($this->orderObj->order_note)): ?>
    <div class="clr t_l"></div>
    <b><?php echo $this->translate('order note'); ?></b>
    <div class="clr sm-widget-block m-cart-items-total">
      <div class="ui-grid-a">
          <?php echo Engine_Api::_()->sitestoreproduct()->truncation($this->orderObj->order_note, 310); ?>
      </div>
    </div>
  <?php endif; ?>

<span class="ui-collapsible-set" data-content-theme="d" data-role="collapsible-set">
<?php 
if( $this->orderObj->order_status != 6 ) :
  if( !empty($this->admin_calling) ):
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/order-comment/_siteadmin_comment.tpl';
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/order-comment/_seller_comment.tpl';
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/order-comment/_buyer_comment.tpl';
  elseif( empty($this->page_user) ):
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/order-comment/_seller_comment.tpl';
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/order-comment/_siteadmin_comment.tpl';
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/order-comment/_buyer_comment.tpl';
  else:
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/order-comment/_buyer_comment.tpl';
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/order-comment/_seller_comment.tpl';
    include APPLICATION_PATH . '/application/modules/Sitestoreproduct/views/sitemobile/scripts/order-comment/_siteadmin_comment.tpl';
  endif;
endif;
?>
</span>