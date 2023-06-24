<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _productInfo.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php
$temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($this->sitestoreproduct->store_id);
$temp_non_selling_product_price = Engine_Api::_()->sitestoreproduct()->getIsAllowedNonSellingProductPrice($this->sitestoreproduct->store_id);
$productInfo = $this->productInfo;
$sitestoreproduct = $this->sitestoreproduct;
$view_type = $this->view_type;
$widget_id = $this->widget_id;
$showinStock = $this->showinStock;
$showAddtoCart = $this->showAddtoCart;
$showAddToCartButton = $this->showAddToCartButton;
$priceWithTitle = $this->priceWithTitle;

if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.openclose', 0)) :
  $closed = empty($sitestoreproduct->closed);
else:
  $closed = 1;
endif;
?>
<?php if( (!empty($temp_allowed_selling) && $sitestoreproduct->allow_purchase) || !empty($temp_non_selling_product_price)) : ?>
  <?php if( isset($productInfo['priceRangeBasis']) && !empty($productInfo['priceRangeBasis']) ) : ?>
    <?php $productPriceRangeBasis = ' '.$productInfo['priceRangeBasis']; ?>
  <?php else: ?>
    <?php $productPriceRangeBasis = ''; ?>
  <?php endif; ?>
<?php if( $closed && 
          empty($sitestoreproduct->draft) && 
         !empty($sitestoreproduct->search) && 
         !empty($sitestoreproduct->approved) && 
         ($sitestoreproduct->start_date < date('Y-m-d H:i:s')) && 
         ($sitestoreproduct->end_date > date('Y-m-d H:i:s') || empty($sitestoreproduct->end_date_enable)) ) : ?>
 
  <?php     
    $getPriceOfProductsWithVATInfo = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($sitestoreproduct);
    $showMsg = '';
    if(!empty($getPriceOfProductsWithVATInfo) && isset($getPriceOfProductsWithVATInfo['show_msg']) && !empty($getPriceOfProductsWithVATInfo['show_msg']))
      $showMsg = '*';
  ?>

  <div class="clr sitestoreproduct_list_price_box">
  <!-- IF PRODUCT HAS DISCOUNT -->
    <?php if (!empty($sitestoreproduct->price) && !empty($productInfo['discount']) ): ?>
      <div class="sitestoreproduct_list_price_details">
        <span class="sitestoreproduct_price_sale">
          <?php
          if(!empty($getPriceOfProductsWithVATInfo)):
            echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($getPriceOfProductsWithVATInfo['display_product_price']) . $showMsg . $productPriceRangeBasis;
          else:
            echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($productInfo['discount']['price_after_discount']) . $productPriceRangeBasis;
          endif;
                    
          ?>
        </span>
        <?php if( empty($priceWithTitle) ) : ?>
          <br />
          <span class="sitestoreproduct_price_original">
            <?php
              if(!empty($getPriceOfProductsWithVATInfo)):
               echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($getPriceOfProductsWithVATInfo['origin_price']);
              else:
                echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($sitestoreproduct->price);
              endif;
            ?>
          </span>
          <span class="sitestoreproduct_price_discount">
            (<?php 
                if(!empty($getPriceOfProductsWithVATInfo)):
                  echo $this->translate('%s off', $getPriceOfProductsWithVATInfo['discountPercentage'] . '%'); 
                else:
                  echo $this->translate('%s off', $productInfo['discount']['price'] . '%');
                endif;
              ?>)
          </span>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="sitestoreproduct_list_price_details">
        <span class="sitestoreproduct_price_sale">
          <?php if( $sitestoreproduct->product_type == 'grouped' ) : 
                  echo $this->translate("Starting at: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->getGroupProductStartingPrice($sitestoreproduct->product_id))); 
                else: 
                  if( empty($sitestoreproduct->price) ) : 
                    echo $this->translate("Free"); 
                  else:
                    if(!empty($getPriceOfProductsWithVATInfo)):
                      echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($getPriceOfProductsWithVATInfo['display_product_price']) . $showMsg . $productPriceRangeBasis;
                    else:
                      echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($sitestoreproduct->price) . $productPriceRangeBasis;
                    endif;
                  endif; 
                endif; ?>
        </span>
      </div>
    <?php endif; ?>

    <?php if( !empty($showAddtoCart) ) : ?>
  <?php if(!empty($temp_allowed_selling) && $sitestoreproduct->allow_purchase):?>
      <div class="sitestoreproduct_list_cart_btn fright">
        <?php echo $this->addToCart($sitestoreproduct, $widget_id, $view_type, $showAddToCartButton); ?>
      </div>
  <?php endif; ?>
    <?php endif; ?>
  </div>

  <?php if( !empty($showinStock) && empty($priceWithTitle) ) : ?>
    <?php if(!empty($sitestoreproduct->stock_unlimited) || $sitestoreproduct->in_stock >= $sitestoreproduct->min_order_quantity): ?>
      <div class="sitestoreproduct_list_price_details">                 	
        <span> 
          <b><?php echo $this->translate("In Stock"); ?></b>
          <?php if( empty($product->stock_unlimited) ) : ?>
            <?php $this->translate(array('%s Item left', '%s Items left', $sitestoreproduct->in_stock), $this->locale()->toNumber($sitestoreproduct->in_stock)); ?>
          <?php endif; ?>
        </span>
      </div>
    <?php else: ?>
      <div class="seaocore_txt_red">
        <b><?php echo $this->translate("Out of Stock"); ?></b>
      </div>
    <?php endif; ?>
  <?php endif; ?>
<?php else: ?>
<div class="seaocore_txt_red">
    <span>
      <?php echo $this->translate("This product is currently not available for purchase.") ?>
    </span>
  </div>
<?php endif; ?>
<?php endif; ?>
