<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _order_review.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $isAllowCredit = true; ?>
<?php if (!empty($this->isDownPaymentEnable)) : ?>
  <?php $isAllowCredit = false; ?>
<?php endif; ?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Seaocore/externals/styles/style_infotooltip.css');
$settings = Engine_Api::_()->getApi('settings', 'core');
 $precision = $settings->getSetting('sitestoreproduct.rate.precision', 2);
$defaultParams['precision'] = $precision;
$isDownPaymentEnable = $settings->getSetting('sitestorereservation.downpayment', 0);
$directPayment = Engine_Api::_()->sitestoreproduct()->isDirectPaymentEnable();
$isDownPaymentCouponEnable = $settings->getSetting('sitestorereservation.coupon', 0);
$isTermsConditionsEnable = $settings->getSetting('sitestore.terms.conditions', 0);
$isFixedTextEnable = $settings->getSetting('sitestore.fixed.text', 0);
$isVatAllow = $settings->getSetting('sitestoreproduct.vat', 0);
if (!empty($isFixedTextEnable)) {
  $fixedTextValue = $settings->getSetting('sitestore.checkout.fixed.text.value');
}
?>

<?php if (empty($isVatAllow)) : ?>
  <script type="text/javascript">
    var CommentLikesTooltips;
    var show_tool_tip = false;
    var counter_req_pendding = 0;

    en4.core.runonce.add(function() {
      {
        $$('.sea_add_tooltip_link').addEvent('mouseover', function(event) {
          var el = $(event.target);
          ItemTooltips.options.offset.y = el.offsetHeight;
          ItemTooltips.options.showDelay = 0;
          if (!el.hasAttribute("rel")) {
            el = el.parentNode;
          }
          show_tool_tip = true;
          if (!el.retrieve('tip-loaded', false)) {
            counter_req_pendding++;
            var resource = '';
            if (el.hasAttribute("rel"))
              resource = el.rel;
            if (resource == '')
              return;

            el.store('tip-loaded', true);
            el.store('tip:title', '<div class="" style="">' +
                    ' <div class="uiOverlay info_tip" style="width: 300px; top: 0px; ">' +
                    '<div class="info_tip_content_wrapper" ><div class="info_tip_content"><div class="info_tip_content_loader">' +
                    '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" alt="Loading" /><?php echo $this->translate("Loading ...") ?></div>' +
                    '</div></div></div></div>'
                    );
            el.store('tip:text', '');
            // Load the likes
            var url = '<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'show-tooltip-info'), 'default', true) ?>';
            el.addEvent('mouseleave', function() {
              show_tool_tip = false;
            });

            var req = new Request.HTML({
              url: url,
              data: {
                format: 'html',
                'resource': resource,
                tax: this.rel
              },
              evalScripts: true,
              onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                el.store('tip:title', '');
                el.store('tip:text', responseHTML);
                ItemTooltips.options.showDelay = 0;
                ItemTooltips.elementEnter(event, el); // Force it to update the text 
                counter_req_pendding--;
                if (!show_tool_tip || counter_req_pendding > 0) {
                  //ItemTooltips.hide(el);
                  ItemTooltips.elementLeave(event, el);
                }
                var tipEl = ItemTooltips.toElement();
                tipEl.addEvents({
                  'mouseenter': function() {
                    ItemTooltips.options.canHide = false;
                    ItemTooltips.show(el);
                  },
                  'mouseleave': function() {
                    ItemTooltips.options.canHide = true;
                    ItemTooltips.hide(el);
                  }
                });
                Smoothbox.bind($$(".sea_add_tooltip_link_tips"));
              }
            });
            req.send();
          }
        });
        // Add tooltips
        var window_size = window.getSize()
        var ItemTooltips = new SEATips($$('.sea_add_tooltip_link'), {
          fixed: true,
          title: '',
          className: 'sea_add_tooltip_link_tips',
          hideDelay: 200,
          offset: {'x': 0, 'y': 0},
          windowPadding: {'x': 370, 'y': (window_size.y / 2)}
        });
      }
    });
  </script>
<?php endif; ?>

<?php
$this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
$productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
$cart_products_detail = @unserialize($this->cart_products_detail);
$stores_product = @unserialize($this->stores_products);
$checkout_process = @unserialize($this->checkout_process);
$checkout_store_name = @unserialize($this->checkout_store_name);
$store_product_types = @unserialize($this->store_product_types);
$address = @unserialize($this->address);
if (!empty($this->coupon_store_id))
  $coupon_store_id = unserialize($this->coupon_store_id);
$grand_total = $downpayment_grand_total = $remaining_downpayment_grand_total = $you_need_to_pay = 0;

if (!empty($directPayment) && !empty($isDownPaymentEnable)) :
  $product_ids = array();
  foreach ($cart_products_detail as $product_id => $productAttribs) :
    $product_ids[] = $product_id;
  endforeach;
  $product_ids = implode(",", $product_ids);
  $cartProductPaymentType = Engine_Api::_()->sitestoreproduct()->getProductPaymentType($product_ids);
else:
  $cartProductPaymentType = true;
endif;
?>

<?php $isAllowCoupon = true; ?>
<?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType) && empty($isDownPaymentCouponEnable)) : ?>
  <?php $isAllowCoupon = false; ?>
<?php endif; ?>

<form name="order_place" id="order_place" method="post" action="<?php echo $this->url(); ?>" >
  <?php
  foreach ($stores_product as $store_id => $product_ids):
    $store_grand_total = $store_sub_total = $store_tax = $admin_tax = $downPaymentSubTotal = $remainingDownPaymentSubTotal = 0;
    ?>
    <div class="sitestoreproduct_order_review m10 clr">
      <div class="mbot5 bold"><?php echo str_replace("::@::", "'", $checkout_store_name[$store_id]); ?></div>
      <div class="sitestoreproduct_data_table product_detail_table fleft">
        <table>
          <tr class="product_detail_table_head">
            <th><?php echo $this->translate("Product Name") ?></th>
            <th class="txt_right"><?php echo $this->translate("Price") ?></th>
            <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
              <th class="txt_right"><?php echo $this->translate("Downpayment") ?></th>
            <?php endif; ?>
            <th class="txt_center"><?php echo $this->translate("Quantity") ?></th>
            <?php if (!empty($isVatAllow)): ?>
              <th class="txt_right"><?php echo $this->translate("VAT") ?></th>
            <?php else: ?>
              <th class="txt_right"><?php echo $this->translate("Tax") ?></th>
            <?php endif; ?>
            <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
              <th class="txt_right"><?php echo $this->translate("Downpayment Total") ?></th>
              <th class="txt_right"><?php echo $this->translate("Remaining Amount Total") ?></th>
            <?php endif; ?>
            <th class="txt_right"><?php echo $this->translate("Subtotal") ?></th>
          </tr>
          <?php
          $order_product_count = 0;
          $display_store_sub_total = 0;
          foreach ($product_ids as $product_id):
            // CALCULATE TAX
            $store_tax_ids = array();
            $temp_store_tax = array();
            $total_product_tax = 0;

            if (!empty($isVatAllow) && !empty($product_id)):
              $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id);
              $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj);
            endif;

            if (!empty($isVatAllow) && isset($cart_products_detail[$product_id]['vat_amount']) && !empty($cart_products_detail[$product_id]['vat_amount'])) :
              $vatTitle = $cart_products_detail[$product_id]['vat_title'];
              $cart_products_detail[$product_id]['product_tax_title'] = serialize($vatTitle);
              if (isset($cart_products_detail[$product_id]['vat_creator'])) :
                if (empty($cart_products_detail[$product_id]['vat_creator'])) :
                   if ($cart_products_detail[$product_id]['product_type'] != 'configurable' && $cart_products_detail[$product_id]['product_type'] != 'virtual') : 
                    $store_tax += $cart_products_detail[$product_id]['product_tax_amount'] = $total_product_tax = @round($cart_products_detail[$product_id]['vat_amount'] * $cart_products_detail[$product_id]['quantity'], 2);
                  endif;
                else:
                  $admin_tax += $cart_products_detail[$product_id]['product_tax_amount'] = $total_product_tax = @round($cart_products_detail[$product_id]['vat_amount'], 2);
                endif;
              endif;
            elseif( !empty($cart_products_detail[$product_id]['store_tax_id']) ) :
              $store_tax_ids = @implode(',', @unserialize($cart_products_detail[$product_id]['store_tax_id']));
              $enable_tax = Engine_Api::_()->getDbtable('taxes', 'sitestoreproduct')->getCheckoutTaxes($store_tax_ids, $address, $cart_products_detail[$product_id]['product_type']);
        
            if( !empty($enable_tax) ) :
            foreach($enable_tax as $index => $tax):
              if( empty($cart_products_detail[$product_id]['sub_total']) ) :
                continue;
              else:
              $product_tax = empty($tax['handling_type']) ? @round($tax['tax_value'], 2) : @round(($cart_products_detail[$product_id]['sub_total'] * @round($tax['tax_value'], $settings->getSetting('sitestoreproduct.rate.precision', 2)) / 100), 2) ;
              $temp_store_tax[$index]['title'] = $tax['title'];
              $temp_store_tax[$index]['amount'] = $product_tax;
              
              // ADMIN TAX
              if( empty($tax['store_id']) ) :
                $tax_type = $this->translate('Site Administrators');
                $admin_tax += @round($product_tax, 2);
              else :
                $tax_type = $this->translate('Store Administrators');
                $store_tax += @round($product_tax, 2);
              endif;
              
              $temp_store_tax[$index]['handling_type'] = $tax['handling_type'];
              $temp_store_tax[$index]['tax_value'] = $tax['tax_value'];
              $temp_store_tax[$index]['type'] = $tax_type;
              $total_product_tax += @round($product_tax, 2);
              endif;
            endforeach;

                $tax_array = @serialize($temp_store_tax);
                $cart_products_detail[$product_id]['product_tax_title'] = $tax_array;
                $cart_products_detail[$product_id]['product_tax_amount'] = $total_product_tax;
              endif;
            endif;
            if (!empty($isVatAllow) &&  $cart_products_detail[$product_id]['product_type'] != 'configurable' && $cart_products_detail[$product_id]['product_type'] != 'virtual') : 
            $store_sub_total += @round($cart_products_detail[$product_id]['sub_total'], 2);
              if (isset($cart_products_detail[$product_id]['display_sub_total'])):
                $display_store_sub_total += @round($cart_products_detail[$product_id]['display_sub_total'], 2);
              endif;
              elseif (empty($isVatAllow)):
                $store_sub_total += @round($cart_products_detail[$product_id]['sub_total'], 2);
              endif;
            
            ?>
    		<?php $order_product_count += $cart_products_detail[$product_id]['quantity'] ?>
        <?php $title_column = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('title'); ?>
        <?php $productTitle = $productTable->getProductAttribute($title_column, array('product_id' => $product_id))->query()->fetchColumn();
        if(empty ($productTitle)):
          $productTitle = $productTable->getProductAttribute('title', array('product_id' => $product_id))->query()->fetchColumn();
        endif;
        ?>
        <?php if( $cart_products_detail[$product_id]['product_type'] == 'configurable' || $cart_products_detail[$product_id]['product_type'] == 'virtual' ) : ?>
          <?php foreach( $cart_products_detail[$product_id]['config'] as $cartproduct_id ): ?>
             <?php $configuration_price = 0; $total_price_config = 0;?>
             
            <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
              <?php if(!empty($isVatAllow)):?>
              <?php $configProductPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj, null, $cartproduct_id); ?>
              <?php endif;?>
            <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->
             
            <tr>
              <td title="<?php echo $productTitle ?>">
                <?php echo Engine_Api::_()->sitestoreproduct()->truncation($productTitle, 40); ?>
                  
                <!--WORK FOR SHOWING MORE LINK FOR SPECS OF PRODUCTS STARTS-->
                  <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct_show_product_specifications')): ?>&nbsp;
                  <?php
                  $spec_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'show-product-specifications', 'product_id' => $product_id, 'format' => 'smoothbox'), 'default', true);
                  echo '<a href="javascript:void(0)" onClick="Smoothbox.open(\'' . $spec_url . '\');">' . $this->translate("more info..") . '</a>';
                  ?>
              <?php endif; ?>
                <!--WORK FOR SHOWING MORE LINK FOR SPECS OF PRODUCTS ENDS-->
                  <?php
                echo '<br/>';
                if( !empty($this->viewer_id) ) :
                  $productOtherInfo = '';
                  $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $cartproduct_id);
                  if( $cart_products_detail[$product_id]['product_type'] == 'virtual' && !empty($cartProductObject->other_info) ) :
                    $productOtherInfo = unserialize($cartProductObject->other_info);
                    if( !empty($productOtherInfo) ) :
                      echo '<b>' . $this->translate("From:") . '</b>' . ' ' . $this->locale()->toDate($productOtherInfo['starttime']) . '</br>';
                      echo '<b>' . $this->translate("To:") . '</b>' . ' ' . $this->locale()->toDate($productOtherInfo['endtime']) . '</br>';
                    endif;
                  endif;
//                    $temp_store_tax[$index]['handling_type'] = $tax['handling_type'];
//                    $temp_store_tax[$index]['tax_value'] = $tax['tax_value'];
//                    $temp_store_tax[$index]['type'] = $tax_type;
//                    $total_product_tax += @round($product_tax, 2);
                  
                  unset($cartProductObject->other_info);
                  $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($cartProductObject);
                      
                  /* WORK FOR CONFIGURATION PRICE STARTS */
                  $values = Engine_Api::_()->fields()->getFieldsValues($cartProductObject);
                  $valueRows = $values->getRowsMatching(array(
                                 'item_id' => $cartProductObject->getIdentity(),
                                ));
                  $configuration_info = Engine_Api::_()->sitestoreproduct()->getConfigurationDetails($valueRows, $product_id);
                  $configuration_price = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($product_id, array('price', 'price_increment'), $valueRows);
                  if(!empty($configuration_price)):
                    $total_price_config = $cart_products_detail[$product_id]['price'] + $configuration_price;
                   $cart_products_detail[$product_id]['configuration_price']  += ($cartProductObject->quantity * $configuration_price);
                   else:
                     $total_price_config = $cart_products_detail[$product_id]['price'];
                  endif;
                  /* END WORK FOR CONFIGURATION PRICE */
                      
                  $otherDetails = $this->storeFieldValueLoop($cartProductObject, $fieldStructure);
                  if (!empty($otherDetails)) :
                    $configDetails = Zend_Json::decode($otherDetails);
                    if (!empty($configDetails)) :
                      foreach ($configDetails as $key => $makeFieldValue) :
                        echo "$key: <b>$makeFieldValue</b><br/>";
                      endforeach;
                    endif;
                  endif;
                   //$categoryAttributeDetails = Engine_Api::_()->sitestoreproduct()->makeCategoryFieldArray($cartProductObject);
//                   if (!empty($categoryAttributeDetails)) :
//                    foreach ($categoryAttributeDetails as $config_key => $makeFieldValue) :
//                      echo "$config_key: <b>$makeFieldValue</b><br/>";
//                    endforeach;
//                   endif;
//                  if(!empty($categoryAttributeDetails)):
//                    $otherDetails = Zend_Json::decode($otherDetails);
//                    $otherDetails = array_merge($otherDetails, $categoryAttributeDetails);
//                    $otherDetails = Zend_Json::encode($otherDetails);
//                  endif;
                  
                  $cart_products_detail[$product_id]['calendar_date'][] = $productOtherInfo;
                  $tempOtherDetails  = str_replace('\"', '', $otherDetails);
                  $tempOtherDetails = str_replace('\u', '*u', $tempOtherDetails); 
                  $cart_products_detail[$product_id]['config_name'][] = $tempOtherDetails;
                  $cart_products_detail[$product_id]['config_quantity'][] = $config_product_quantity = $cartProductObject->quantity;
                  $cart_products_detail[$product_id]['config_info'][] = $configuration_info;
                  if ($isVatAllow):
                    $cart_products_detail[$product_id]['config_price'][] = $configProductPricesArray['product_price_after_discount'];
                    $cart_products_detail[$product_id]['config_downpayment'][] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $configProductPricesArray['product_price_after_discount']));
                  else:
                    $cart_products_detail[$product_id]['config_price'][] = $total_price_config;
                    $cart_products_detail[$product_id]['config_downpayment'][] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $total_price_config));
                  endif; 
                else:
                  $tempCalendarDate = '';
                  if( $cart_products_detail[$product_id]['product_type'] == 'virtual' && (!empty($cartproduct_id['starttime']) && !empty($cartproduct_id['endtime'])) ) :
                    $tempCalendarDate = array('starttime' => $cartproduct_id['starttime'], 'endtime' => $cartproduct_id['endtime']);
                    echo '<b>' . $this->translate("From:") . '</b>' . ' ' . $this->locale()->toDate($cartproduct_id['starttime']) . '</br>';
                    echo '<b>' . $this->translate("To:") . '</b>' . ' ' . $this->locale()->toDate($cartproduct_id['endtime']) . '</br>';
                  endif;
                  
                  /* WORK FOR CONFIGURATION PRICE STARTS*/
                  $configuration_info = Engine_Api::_()->sitestoreproduct()->getConfigurationDetails($cartproduct_id, $product_id);
                  $configuration_price = Engine_Api::_()->sitestoreproduct()->getConfigurationPrice($product_id, array('price', 'price_increment'), $cartproduct_id, 0, 1);
                  if(!empty($configuration_price)):
                    $total_price_config = $cart_products_detail[$product_id]['price'] + $configuration_price;
                    $cart_products_detail[$product_id]['configuration_price']  += ($cartproduct_id['quantity'] * $configuration_price);
                    
                    else:
                        $total_price_config = $cart_products_detail[$product_id]['price'];
                  endif;
                  /* END WORK FOR CONFIGURATION PRICE */
                  
                  $makeFieldValueArray = Engine_Api::_()->sitestoreproduct()->makeFieldValueArray($cartproduct_id);
                  if( !empty($makeFieldValueArray) ) : ?>
                  	<div class="f_small seaocore_txt_light"
                      <?php foreach($makeFieldValueArray as $key => $makeFieldValue) : ?>
                        <span><?php echo "$key: <b>$makeFieldValue</b><br/>"; ?></span>
                      <?php endforeach; ?>
                    </div>
                  <?php endif;
                  $otherDetails = Zend_Json::encode($makeFieldValueArray);
                  $tempOtherDetails = str_replace('\"', '', $otherDetails);
                  $tempOtherDetails = str_replace('\u', '*u', $tempOtherDetails); 
                  $cart_products_detail[$product_id]['calendar_date'][] = $tempCalendarDate;
                  $cart_products_detail[$product_id]['config_name'][] = $tempOtherDetails;
                  $cart_products_detail[$product_id]['config_quantity'][] = $config_product_quantity = $cartproduct_id['quantity']; 
                  $cart_products_detail[$product_id]['config_info'][] = $configuration_info;
//                  $cart_products_detail[$product_id]['config_price'][] = $total_price_config;
                  if ($isVatAllow):
                    $cart_products_detail[$product_id]['config_price'][] = $configProductPricesArray['product_price_after_discount'];
                    $cart_products_detail[$product_id]['config_downpayment'][] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $configProductPricesArray['product_price_after_discount']));
                  else:
                    $cart_products_detail[$product_id]['config_price'][] = $total_price_config;
                    $cart_products_detail[$product_id]['config_downpayment'][] = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $total_price_config));
                  endif;
                endif;
                    ?>
              </td>
                  <td class="txt_right">
                    
                    <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                    <?php if(!empty($isVatAllow)):?>
                    <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($configProductPricesArray['display_product_price']); ?>
                    <?php else: ?>
                     <?php if(!empty($total_price_config)) : ?>
                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_price_config); ?>
                     <?php else: ?>
                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['price']); ?>
                    <?php endif; ?>
                    <?php endif;?>
                    <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->
                    
                    <?php if ($cart_products_detail[$product_id]['product_type'] == 'virtual' && !empty($cart_products_detail[$product_id]['price_range_text'])) : ?>
                      <?php echo $this->translate($cart_products_detail[$product_id]['price_range_text']) ?>
                    <?php endif; ?>
                  </td>
                  <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
                  <td class="txt_right">
                  <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                    <?php if (!empty($isVatAllow)): ?>
                      <?php $downPaymentAmount = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $configProductPricesArray['product_price_after_discount'])); ?>               
                      <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentAmount) ?>
                    <?php else:?>
                      <?php if(!empty($total_price_config)) : ?>
                        <?php $downPaymentAmount = Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $total_price_config)); ?>
                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentAmount) ?>
                      <?php else :?>
                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['downpayment']) ?>
                     <?php endif; ?>
                    <?php endif; ?>
                  <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->
                  </td>
                  <?php endif; ?>
                  <td class="txt_center"><?php echo $config_product_quantity; ?></td>
                  
                  <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS-->
                  <td class="txt_right" <?php if (!empty($vatTitle)) : echo "title = $vatTitle"; endif; ?>>
                    <?php if (!empty($isVatAllow) && !empty($configProductPricesArray)) : ?>
                      <?php if (isset($productPricesArray['vatShowType'])) : ?>
                        <?php echo $productPricesArray['vatShowType']; ?>
                      <?php else: ?>
                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($configProductPricesArray['vat'] * $config_product_quantity); ?>
                      <?php endif; ?>
                    <?php else: ?>
                      <?php echo empty($total_product_tax) ? Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_product_tax) : $this->htmlLink('javascript:void(0);', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_product_tax), array('class' => 'sea_add_tooltip_link', 'rel' => $tax_array)); ?>
                    <?php endif; ?>
                  </td>
                  <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
                    <td class="txt_right">
                      
                      <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                      <?php if(!empty($isVatAllow)):?>
                        <?php $downPaymentAmount = round(Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $configProductPricesArray['product_price_after_discount'])) * $config_product_quantity, 2); ?>
                      <?php else:?>
                      <?php if(!empty($total_price_config)) : ?>
                        <?php $downPaymentAmount = round(Engine_Api::_()->sitestoreproduct()->getDownpaymentAmount(array('product_id' => $product_id, 'price' => $total_price_config)) * $config_product_quantity, 2); ?>

                      <?php else :?>
                        <?php $downPaymentAmount = @round($cart_products_detail[$product_id]['downpayment'] * $config_product_quantity, 2); ?>
                     <?php endif; ?>
                      <?php endif;?>
                      <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->

                      <?php $downPaymentSubTotal += $downPaymentAmount; ?>
                      <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentAmount); ?>
                    </td>
                    <td class="txt_right">
                      
                      <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                      <?php if(!empty($isVatAllow)):?>
                        <?php $remainingDownPaymentAmount = round($configProductPricesArray['product_price_after_discount'] * $config_product_quantity - $downPaymentAmount, 2) ?>
                      <?php else:?>
                      <?php if(!empty($total_price_config)) :?>
                        <?php $remainingDownPaymentAmount = round($total_price_config * $config_product_quantity - $downPaymentAmount, 2) ?>
                      <?php else: ?>
                        <?php $remainingDownPaymentAmount = round($cart_products_detail[$product_id]['price'] * $config_product_quantity - $downPaymentAmount, 2) ?>
                      <?php endif; ?>
                      <?php endif;?>
                      <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->

                      <?php $remainingDownPaymentSubTotal += $remainingDownPaymentAmount; ?>
                      <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remainingDownPaymentAmount); ?>
                    </td>
                  <?php endif; ?>
                    
                  <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                  <?php if(!empty($isVatAllow)):?>
                    <td class="txt_right">
                      <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($configProductPricesArray['display_product_price'] * $config_product_quantity); ?>
                      <?php $display_store_sub_total += $configProductPricesArray['display_product_price'] * $config_product_quantity; ?>
                    </td>
                  <?php else:?>
                    <?php if(!empty($total_price_config)) : ?>
                       <td class="txt_right"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_price_config * $config_product_quantity);?></td>
                    <?php else :?>
                        <td class="txt_right"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['price'] * $config_product_quantity);?></td>
                    <?php endif; ?>
                  <?php endif;?>
                  <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->
                </tr>
                
                <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS STARTS-->
                <?php if (!empty($isVatAllow)): ?>
                  <?php $store_sub_total += @round($configProductPricesArray['product_price_after_discount'] * $config_product_quantity, 2); ?>
                  <?php if (isset($cart_products_detail[$product_id]['display_sub_total'])):  ?>
                    <?php // $display_store_sub_total += @round($configProductPricesArray['display_product_price']*$config_product_quantity, 2); ?>
                  <?php endif;?>
                <?php endif; ?>

                <?php if (!empty($isVatAllow) && isset($cart_products_detail[$product_id]['vat_amount']) && !empty($cart_products_detail[$product_id]['vat_amount'])) : ?>
                  <?php $store_tax += $cart_products_detail[$product_id]['product_tax_amount'] = $total_product_tax = @round($configProductPricesArray['vat'] * $config_product_quantity, 2); ?>
                <?php endif;?>
                <!--WORK FOR VAT IN CONFIGUREABLE PRODUCTS ENDS-->
                
              <?php endforeach; ?>
    <?php else: ?>
        <tr>
          <td title="<?php echo $productTitle ?>">
            <?php echo Engine_Api::_()->sitestoreproduct()->truncation($productTitle, 40); ?>
            
            <!--WORK FOR SHOWING MORE LINK FOR SPECS OF PRODUCTS STARTS-->
              <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct_show_product_specifications')): ?>&nbsp;
                  <?php
                  $spec_url = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'show-product-specifications', 'product_id' => $product_id, 'format' => 'smoothbox'), 'default', true);
                  echo '<a href="javascript:void(0)" onClick="Smoothbox.open(\'' . $spec_url . '\');">' . $this->translate("more info..") . '</a>';
                  ?>
              <?php endif; ?>
            <!--WORK FOR SHOWING MORE LINK FOR SPECS OF PRODUCTS ENDS-->
            
            
          </td>
          <td class="txt_right">           
            <!--WORK FOR SHOWING PRICE ACC. TO THE VAT SETTING-->
            <?php if (!empty($isVatAllow) && !empty($productPricesArray) && isset($productPricesArray['display_product_price'])): ?>
              <?php // $product_obj = Engine_Api::_()->getItem('sitestoreproduct_product', $product_id); ?>
              <?php // $productPricesArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($product_obj); ?>

              <?php // if (!empty($productPricesArray) && isset($productPricesArray['display_product_price'])): ?>
                <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPricesArray['display_product_price']); ?>
            <?php // else: ?>
              <?php // echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['price']); ?>
              <?php // endif; ?>
            <?php else: ?>
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['price']); ?>
            <?php endif; ?>
          </td>
          <?php if( !empty($isDownPaymentEnable) && !empty($cartProductPaymentType) ) : ?>
            <td class="txt_right">
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['downpayment']); ?>
            </td>
          <?php endif; ?>
          <td class="txt_center"><?php echo $cart_products_detail[$product_id]['quantity']; ?></td>
          <td class="txt_right" <?php if( !empty($vatTitle) ) : echo "title = $vatTitle"; endif; ?>>
            <?php if( !empty($isVatAllow) ) : ?>
              <?php // echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_product_tax) ?>

                      <?php if (isset($productPricesArray['vatShowType'])) : ?>
                        <?php echo $productPricesArray['vatShowType']; ?>
                      <?php else: ?>
                        <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productPricesArray['vat'] * $cart_products_detail[$product_id]['quantity']); ?>
                      <?php endif; ?>
            
            <?php else: ?>
              <?php echo empty($total_product_tax) ? Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_product_tax) : $this->htmlLink('javascript:void(0);', Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($total_product_tax), array('class'=>'sea_add_tooltip_link', 'rel'=>$tax_array)); ?>
            <?php endif; ?>
          </td>
          <?php if( !empty($isDownPaymentEnable) && !empty($cartProductPaymentType) ) : ?>
            <td class="txt_right">
              <?php $downPaymentSubTotal += $cart_products_detail[$product_id]['downpayment_subtotal']; ?>
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['downpayment_subtotal']);?>
            </td>
            <td class="txt_right">
              <?php $remainingDownPaymentAmount = round($cart_products_detail[$product_id]['sub_total'] - $cart_products_detail[$product_id]['downpayment_subtotal'], 2) ?>
              <?php $remainingDownPaymentSubTotal += $remainingDownPaymentAmount; ?>
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remainingDownPaymentAmount);?>
            </td>
          <?php endif; ?>
          <td class="txt_right">
            <?php if (!empty($isVatAllow) && isset($cart_products_detail[$product_id]['display_sub_total'])): ?>
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['display_sub_total']);?>
            <?php //$display_store_sub_total += ($cart_products_detail[$product_id]['display_sub_total']); ?>
            <?php else:?>
              <?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_products_detail[$product_id]['sub_total']);?>
            <?php endif;?>
          </td>
        </tr>
      <?php endif; ?>
      <?php if(empty($isVatAllow) && !empty($cart_products_detail[$product_id]['configuration_price'])) : ?>
          <?php $store_sub_total += $cart_products_detail[$product_id]['configuration_price']; ?>
      <?php endif; ?>
  <?php endforeach; ?>
          <tr class="product_detail_table_head">
              <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
              <td colspan="8" class="bold txt_right">
                <?php echo $this->translate("Downpayment: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentSubTotal)); ?>&nbsp&nbsp|&nbsp&nbsp
              <?php echo $this->translate("Remaining Amount: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remainingDownPaymentSubTotal)); ?>&nbsp&nbsp|&nbsp&nbsp
              <?php echo $this->translate("Subtotal: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_sub_total)); ?>
              </td>
                <?php else: ?>
              <td colspan="5">
                <span class="fright bold">
                  <?php if (!empty($isVatAllow)): ?>
                    <?php echo $this->translate("Subtotal: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($display_store_sub_total)); ?>
                  <?php else:  ?>
      <?php echo $this->translate("Subtotal: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_sub_total)); ?>
              <?php endif; ?>
                </span>
              </td>
  <?php endif; ?>
          </tr>
        </table>
      </div>
      <div class="fleft">
          <?php if (!empty($isTermsConditionsEnable)) : ?>
          <section>
    <?php $termsConditionsUrl = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'index', 'action' => 'terms-conditions-details', 'store_id' => $store_id), 'default', true); ?>
            <div class="mbot5"><?php echo $this->translate("Terms and Conditions") ?><span class="seaocore_txt_red">*</span></div>
            <input id="terms_conditions_agree_<?php echo $store_id ?>" type="checkbox" /><?php echo $this->translate('I agree with the %1$sterms and conditions%2$s.', '<a href="javascript:void(0);" onclick="Smoothbox.open(\'' . $termsConditionsUrl . '\')">', '</a>') ?>
            <span id="terms_conditions_error_<?php echo $store_id ?>" style="display: none" class="seaocore_txt_red">
          <?php echo $this->translate("Please agree with terms & conditions."); ?>
            </span>
          </section>
  <?php endif; ?>

        <section class="fleft mtop10">
          <a href="javascript:void(0)" onclick="orderNote(<?php echo $store_id; ?>)" class="buttonlink seaocore_icon_edit mbot5"><?php echo $this->translate("Write a note for your order from this store.") ?></a>
          <article id="note_<?php echo $store_id; ?>" style="display: none" class="mbot10" >
            <textarea id="order_note_<?php echo $store_id; ?>" name="order_note[<?php echo $store_id; ?>]"></textarea>
          </article>
        </section>
      </div>
      <div class="fright">
        <div >
          <div class="invoice_ttlamt_box_wrap mbot10 fright" style="width: 300px;">
            <div class="invoice_ttlamt_box">
              <?php
              if (empty($store_product_types[$store_id])) :
                $checkout_process['shipping_methods'][$store_id]['delivery_time'] = 0;
                $checkout_process['shipping_methods'][$store_id]['price'] = 0;
                $checkout_process['shipping_methods'][$store_id]['title'] = '';
                $shipping_price = 0;
              else : $shipping_price = $checkout_process['shipping_methods'][$store_id]['price'];
                ?>
                <div class="clr">
                  <div class="invoice_order_info fleft"><?php echo $checkout_process['shipping_methods'][$store_id]['title']; ?></div>
                  <div class="fright"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($shipping_price) . '<br/>'; ?></div>
                </div>
  <?php endif; ?>
                  <?php if (!empty($isVatAllow)): ?>
                <div class="clr">  
                  <div class="invoice_order_info fleft">
    <?php echo $this->translate('Total Products (net):'); ?>
                  </div>
                  <div class="fright"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_sub_total) . '<br/>'; ?> </div>
                </div>
                  <?php endif; ?>
              <div class="clr">  
                <div class="invoice_order_info fleft">
                  <?php if (!empty($isVatAllow)): ?>
                    <?php echo $this->translate('Total VAT'); ?>
                  <?php else: ?>
    <?php echo $this->translate('Tax'); ?>
  <?php endif; ?>
                </div>
                <div class="fright">
                  <?php $tempTotalTax = $store_tax + $admin_tax; 
                  if(empty($tempTotalTax)):
                    $tempTotalTax = (float) $tempTotalTax;
                    $priceStr = Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($tempTotalTax);
                    echo $priceStr. '<br/>';
                  else:
                      echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_tax + $admin_tax) . '<br/>';
                  endif;
                  ?>
                  </div>
              </div>

              <?php if (!empty($isAllowCoupon) && !empty($coupon_store_id) && in_array($store_id, $coupon_store_id)) : ?>
                <?php $couponAmount = $this->couponDetail[$store_id]['coupon_amount']; ?>
                <?php $couponCode = $this->couponDetail[$store_id]['coupon_name']; ?>
                <?php if (!empty($couponAmount) && !empty($couponCode)) : ?>
                  <?php $couponDetail = serialize(array('coupon_code' => $couponCode, 'coupon_amount' => $couponAmount)); ?>
                  <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
                    <?php if ($downPaymentSubTotal > $couponAmount) : ?>
                      <?php $downPaymentSubTotal -= $couponAmount; ?>
                    <?php else: ?>
                      <?php $downPaymentSubTotal = 0; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                  <?php if ($store_sub_total > $couponAmount) : ?>
                  <?php $store_sub_total -= $couponAmount; ?>
                  <?php else: ?>
        <?php $store_sub_total = 0; ?>
      <?php endif; ?> 
                  <div class="clr">  
                    <div class="invoice_order_info fleft"><?php echo $couponCode; ?></div>
                    <div class="fright"><?php echo '-' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($couponAmount); ?> </div>
                  </div>
                <?php endif; ?>
              <?php endif; ?>


<?php if (!empty($isAllowCredit) && !empty($this->creditAllow) && in_array($store_id, $this->creditDetail['store_id'])) : ?>
                <?php $creditAmount = $this->creditDetail['store_deduct'][$store_id]; ?>
                <?php $creditCode = $this->creditDetail['Store_credit'][$store_id]; ?>
                <?php if (!empty($creditAmount) && !empty($creditCode)) : ?>
                  <?php $creditDetail = serialize(array('credit_code' => $creditCode, 'credit_amount' => $creditAmount)); ?>
                  <?php if ($store_sub_total > $creditAmount) : ?>
                  <?php $store_sub_total -= $creditAmount; ?>
                  <?php else: ?>
        <?php $store_sub_total = 0; ?>
      <?php endif; ?> 
                  <div class="clr">  
                    <div class="invoice_order_info fleft"><?php echo $creditCode; ?> Credit Points </div>
                    <div class="fright"><?php echo '-' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($creditAmount); ?> </div>
                  </div>
                <?php endif; ?>
              <?php endif; ?>
  <?php $store_total = @round($store_sub_total, 2) + @round($store_tax, 2) + @round($shipping_price, 2) + @round($admin_tax, 2); ?>

              <div class="clr">
                <div class="invoice_order_info fleft"><b><?php echo $this->translate('Total'); ?></b></div>
                <div class="fright"><b><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($store_total) . '<br/>'; ?></b></div>
              </div>
  <?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
    <?php $you_need_to_pay += @round($downPaymentSubTotal, 2) + @round($shipping_price, 2) + @round($admin_tax, 2) + @round($store_tax, 2); ?>
                <div class="clr">  
                  <div class="invoice_order_info fleft"><b><?php echo $this->translate('Payable Amount'); ?></b></div>
                  <div class="fright">
                    <b><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downPaymentSubTotal + $store_tax + $admin_tax + $shipping_price) . '<br/>'; ?></b>
                  </div>
                </div>
  <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div> 

    <?php
    // COMMISSION
    $commission = Engine_Api::_()->sitestoreproduct()->getOrderCommission($store_id);
    $commission_type = $commission[0];
    $commission_rate = $commission[1];

    // IF COMMISSION VALUE IS FIX.
    if ($commission_type == 0) :
      $commission_value = $commission_rate;
    else :
      $commission_value = (@round($store_sub_total, 2) * $commission_rate) / 100;
    endif;

    // SAVE ALL FIELDS VALUE FOR A STORE
    $checkout_process[$store_id]['item_count'] = $order_product_count;
    $checkout_process['payment'][$store_id]['sub_total'] = @round($store_sub_total, 2);
    $checkout_process['payment'][$store_id]['store_tax'] = @round($store_tax, 2);
    $checkout_process['payment'][$store_id]['admin_tax'] = @round($admin_tax, 2);
    $checkout_process['payment'][$store_id]['commission_type'] = $commission_type;
    $checkout_process['payment'][$store_id]['commission_rate'] = @round($commission_rate, 2);
    $checkout_process['payment'][$store_id]['commission_value'] = @round($commission_value, 2);
    $checkout_process['payment'][$store_id]['grand_total'] = @round($store_total, 2);
    $checkout_process['payment'][$store_id]['credit_points'] = @round($creditCode, 2);
    $checkout_process['payment'][$store_id]['credit_amount'] = @round($creditAmount, 2);
    $grand_total += @round($store_total, 2);
    if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) :
      $checkout_process['payment'][$store_id]['downpayment_total'] = @round($downPaymentSubTotal, 2);
      $downpayment_grand_total += @round($downPaymentSubTotal, 2);
      $remaining_downpayment_grand_total += @round($remainingDownPaymentSubTotal, 2);
    endif;
    if (!empty($couponDetail)) :
      $checkout_process[$store_id]['coupon_detail'] = $couponDetail;
    endif;
  endforeach;
  $checkout_process['grand_total'] = @round($grand_total, 2);
  ?>
<?php if (!empty($isDownPaymentEnable) && !empty($cartProductPaymentType)) : ?>
    <!--  <div class="fright m10 clr" style="margin-top:0;">
          <div class="invoice_ttlamt_box_wrap mbot10 o_hidden" style="width:300px;">
            <div class="invoice_ttlamt_box">
              <div class="clr">  
                <div class="invoice_order_info fleft"><?php echo $this->translate("Downpayment Grand Total:") ?></div>
                <div class="fright"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($downpayment_grand_total) ?><br> </div>
              </div>
              <div class="clr">
                <div class="invoice_order_info fleft"><?php echo $this->translate("Remaining Amount Grand Total:") ?></div>
                <div class="fright"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remaining_downpayment_grand_total) ?></div>
              </div>
              <div class="clr">
                <div class="invoice_order_info fleft"><?php echo $this->translate("Amount Need to pay:") ?></div>
                <div class="fright"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($remaining_downpayment_grand_total) ?></div>
              </div>
              <div class="clr">
                <div class="invoice_order_info fleft bold"><?php echo $this->translate("Grand Total:") ?></div>
                <div class="fright bold"><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($grand_total) ?></div>
              </div>
            </div>
          </div>
        </div>-->
    <h3 class="clr fright pright10 mright10">
    <?php echo $this->translate("You need to pay: ") . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($you_need_to_pay); ?>
    </h3>
    <?php endif; ?>
    <?php //else:  ?>
  <h3 class="clr fright pright10 mright10">
  <?php echo $this->translate("Grand Total: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($grand_total)); ?>
  </h3>
<?php //endif;   ?>

  <!--<input type="hidden" id ="sitestoreproduct_address" name="sitestoreproduct_address" />-->
  <script type="text/javascript">
      // We have moved this code to "_billing_address.tpl" file.
      
//  en4.core.runonce.add(function() {
//    $("sitestoreproduct_address").set('value', sitestoreproduct_address);
//  });
  </script>
  <div class="clr">

    <div id="checkout_place_order_error"></div>

    <div class='buttons'>
        <?php if (!empty($fixedTextValue)) : ?>
        <div class="m10">
        <?php echo $fixedTextValue; ?>
        </div>
<?php endif; ?>
      <div class="fright m10">  
        <button type="button" name="place_order" onclick="placeOrder()" class="fright"><?php echo $this->translate("Place Order") ?></button>
        <div id="loading_image_5" class="fright m10" style="display: inline-block;"></div>
      </div>
      <div class="o_hidden m10">
        <?php echo $this->htmlLink(array("action" => "cart", "route" => "sitestoreproduct_product_general"), $this->translate("Edit your cart"), array('class' => 'buttonlink sitestore_icon_edit_cart')); ?>
        <?php
        $isPlaceOrderActivityEnabled = Engine_Api::_()->getDbTable('actionTypes', 'activity')->getActionType("sitestoreproduct_order_place");

        if (!empty($isPlaceOrderActivityEnabled) && !empty($isPlaceOrderActivityEnabled->enabled)):
          ?>
          <span class="clr dblock mtop5" style="display:none" >
            <input type="checkbox" id="isPrivateOrder" name="isPrivateOrder" checked="checked"><label for="isPrivateOrder"><?php echo $this->translate("Make my purchase private.") ?></label>
          </span>
<?php endif;
?>

      </div> 
    </div>
  </div>
</form>

<script type="text/javascript">

  var is_showing = 0;
  function orderNote(id)
  {
    if(is_showing == 0) {
      $('note_' + id).style.display = 'block';
      is_showing = 1;
    }else if(is_showing == 1) {
      $('note_' + id).style.display = 'none';
      is_showing = 0;
    }
  }

//  function orderNote(id)
//  {
//    $('note_' + id).toggle();
//  }

  function placeOrder()
  {
    var index = 0;
    var order_note = new Array(<?php echo count($stores_product) * 2; ?>);
    var agreeTermsConditions = true;

<?php foreach ($stores_product as $store_id => $value): ?>
      order_note[index++] = <?php echo $store_id ?>;
      order_note[index++] = $('order_note_' +<?php echo $store_id ?>).value;
  <?php if (!empty($isTermsConditionsEnable)) : ?>
        $("terms_conditions_error_<?php echo $store_id ?>").style.display = 'none';
        if (!$("terms_conditions_agree_<?php echo $store_id ?>").checked) {
          $("terms_conditions_error_<?php echo $store_id ?>").style.display = 'block';
          agreeTermsConditions = false;
        }
  <?php endif; ?>
<?php endforeach; ?>

    if (!agreeTermsConditions)
      return;

    var isPrivateOrder = 0;
    if ($('isPrivateOrder') && $('isPrivateOrder').checked)
      isPrivateOrder = 1;
    else if (!$('isPrivateOrder'))
      isPrivateOrder = 1;

    var placeOrderUrl;
<?php if (!empty($this->checkoutStoreId)) : ?>
      placeOrderUrl = "sitestoreproduct/index/place-order/store_id/<?php echo $this->checkoutStoreId ?>";
<?php else: ?>
      placeOrderUrl = 'sitestoreproduct/index/place-order';
<?php endif; ?>

    en4.core.request.send(new Request.JSON({
      url: en4.core.baseUrl + placeOrderUrl,
      method: 'POST',
      onRequest: function() {
        $('loading_image_5').innerHTML = '<img src=' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15>';
      },
      data: {
        format: 'json',
        checkout_process: '<?php echo serialize($checkout_process); ?>',
        order_note: order_note.join('::@::'),
        cart_products_detail: '<?php echo @serialize($cart_products_detail); ?>',
        cart_products_exist: '<?php echo $this->paymentRequest; ?>',
        sitestoreproduct_address: $('sitestoreproduct_address').value,
        sitestoreproduct_downloadable_product: '<?php echo $this->sitestoreproduct_downloadable_product; ?>',
        cartProductPaymentType: '<?php echo $cartProductPaymentType ?>',
        isPrivateOrder: isPrivateOrder
      },
      onSuccess: function(responseJSON)
      {
        $('loading_image_5').innerHTML = '';
        if (responseJSON.return_sitestoreproduct_manage_cart)
        {
          window.location = '<?php echo $this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true) ?>';
        }
        if (responseJSON.checkout_place_order_error)
        {
          $('checkout_place_order_error').innerHTML = responseJSON.checkout_place_order_error;
          return;
        }
        if (responseJSON.parent_id)
        {
          if (responseJSON.gateway_id == 1 || responseJSON.gateway_id == 2 || otherPaymentGateways.contains(responseJSON.gateway_id))
          {
<?php if (!empty($this->checkoutStoreId)) : ?>
  <?php $payment_url = $this->url(array('action' => 'payment', 'store_id' => $this->checkoutStoreId), 'sitestoreproduct_general', true) ?>
<?php else: ?>
  <?php $payment_url = $this->url(array('action' => 'payment'), 'sitestoreproduct_general', true) ?>
<?php endif; ?>
            window.location = '<?php echo $payment_url ?>/gateway_id/' + responseJSON.gateway_id + '/order_id/' + responseJSON.parent_id;
          }
          else
          {
<?php $success_url = $this->url(array('action' => 'success'), 'sitestoreproduct_general', true) ?>
            window.location = '<?php echo $success_url ?>/success_id/' + responseJSON.parent_id;
          }
        }
      }
    })
            );
  }

</script>