<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _productAvailability.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php  $temp_allowed_selling = Engine_Api::_()->sitestoreproduct()->getIsAllowedSellingProducts($this->sitestoreproduct->store_id);
$temp_non_selling_product_price = Engine_Api::_()->sitestoreproduct()->getIsAllowedNonSellingProductPrice($this->sitestoreproduct->store_id);
?> 
<?php if( $closed && 
          empty($this->sitestoreproduct->draft) && 
          !empty($this->sitestoreproduct->search) && 
          !empty($this->sitestoreproduct->approved) && 
          ($this->sitestoreproduct->start_date < date('Y-m-d H:i:s')) && 
          ( empty($this->sitestoreproduct->end_date_enable) || $this->sitestoreproduct->end_date > date('Y-m-d H:i:s') ) && ((!empty($temp_allowed_selling) && $this->sitestoreproduct->allow_purchase))
        ) : 
?>
  <div class="sitestoreproduct_pcbox clr">
<!-- IF PRODUCT IS IN STOCK AND AVAILABLE FOR PURCHASING -->
    <?php if(!empty($this->sitestoreproduct->stock_unlimited) || $this->sitestoreproduct->in_stock >= $this->sitestoreproduct->min_order_quantity ) : ?>
      <?php if( empty($this->isQuickView) ) : ?>
        <?php $actionUrl = ''; ?>
      <?php else: ?>
        <?php $actionUrl = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'quick-view'), '', '') ?>
      <?php endif; ?>
          <form method="post" action="<?php echo $actionUrl ?>" onsubmit="return checkQuantity();">
          <div>
          <?php if(!empty($this->productQuantityBox)) : ?>
          <span><?php echo $this->translate('Qty:'); ?></span>
          <input type="text" name="quantity" id="quantity" value="<?php echo $this->sitestoreproduct->min_order_quantity; ?>" style='width:30px;'>
         <?php else : ?>
          <input type="hidden" name="quantity" id="quantity" value="<?php echo $this->sitestoreproduct->min_order_quantity; ?>" style='width:30px;'>
         <?php endif; ?>
          <?php if(!empty($temp_allowed_selling) && $this->sitestoreproduct->allow_purchase):?>
          <button type='submit' name="update_shopping_cart" class="add_to_cart_button mleft10">
            <span>
              <?php echo $this->translate("Add to Cart") ?>
            </span>
          </button>
          <?php endif; ?>
          </div>
        </form>

    <?php else: ?>
      <?php if(!empty($this->out_of_stock_action)) : ?>
      <div id="notify_to_seller" class="mbot10">
          <div>
            <?php echo $this->translate("Notify me when this product is in stock:"); ?>
          </div>
          <span id="notify_to_me_email_error" class="seaocore_txt_red mtop5" style="display:none">
            <?php echo $this->translate("Please enter a valid Email Address") ?>
          </span>
          <div class="clr mtop10">
            <?php echo $this->translate("Email:"); ?>
            <input type="text" id="notify_to_seller_email" value="<?php echo $viewer_email ?>" />
            <button class="notify_btn" type="button" onclick="notifyToSeller(<?php echo $this->sitestoreproduct->product_id ?>)">
              <?php echo $this->translate("Notify Me") ?>
            </button>
            <span id="notify_to_me_loading" style="display: inline-block;"></span>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>

<?php
$isVatAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.vat', 0);
if(!empty($isVatAllow)):
$productPriceArray = Engine_Api::_()->sitestoreproduct()->getPriceOfProductsAfterVAT($this->sitestoreproduct);
if(!empty($productPriceArray) && empty($productPriceArray['show_msg']) && ($this->sitestoreproduct->product_type != 'downloadable')):?>
  <?php if( empty($this->isQuickView) ) : ?>
    <span class="btnlink">
    <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'shipping-methods', 'store_id' => $this->sitestoreproduct->store_id, 'product_id' => $this->sitestoreproduct->product_id, 'isViewerSide' => true), $this->translate("Shipping methods"), array('class' => 'smoothbox buttonlink sitestoreproduct_shipping_link')); ?>
  </span>
<?php else :?>
<span class="btnlink">
    <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'shipping-methods', 'store_id' => $this->sitestoreproduct->store_id, 'product_id' => $this->sitestoreproduct->product_id, 'isViewerSide' => true), $this->translate("Shipping methods"), array('class' => 'buttonlink sitestoreproduct_shipping_link', 'target' => '_blank')); ?>
  </span>
<?php endif; ?>
<?php endif; ?>
<?php elseif(empty($isVatAllow) && ($this->sitestoreproduct->product_type != 'downloadable')):?>
<?php if( empty($this->isQuickView) ) : ?>
  <span class="btnlink">
    <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'shipping-methods', 'store_id' => $this->sitestoreproduct->store_id, 'product_id' => $this->sitestoreproduct->product_id, 'isViewerSide' => true), $this->translate("Shipping methods"), array('class' => 'smoothbox buttonlink sitestoreproduct_shipping_link')); ?>
  </span>
<?php else: ?>
<span class="btnlink">
    <?php echo $this->htmlLink(array('route' => 'sitestoreproduct_general', 'action' => 'shipping-methods', 'store_id' => $this->sitestoreproduct->store_id, 'product_id' => $this->sitestoreproduct->product_id, 'isViewerSide' => true), $this->translate("Shipping methods"), array('class' => 'buttonlink sitestoreproduct_shipping_link', 'target' => '_blank')); ?>
  </span>
  <?php endif; ?>
<?php endif;?>

    <?php if(!empty($compare) || !empty($this->create_review)):?>
      <?php if(!empty($compare)):?>
        <span class="btnlink"> 
          <?php echo $compare ?>
        </span>
      <?php endif; ?>
      <span class="btnlink">
        <?php echo $this->addToWishlistSitestoreproduct($this->sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => ''));?>
      </span>
      <?php if(!empty($this->create_review)):?>
        <span class="btnlink">
          <?php echo $this->content()->renderWidget("sitestoreproduct.review-button", array('product_guid' => $this->sitestoreproduct->getGuid(), 'product_profile_page' => 1, 'identity' => $this->identity, 'isProductProfile' => 1, 'isQuickView' => $this->isQuickView)) ?>
        </span>
      <?php endif; ?>
    <?php endif; ?>

  <!--WORK FOR SHOWING PAYMENT METHODS START-->
  <?php if (!empty($this->payWithString)): ?>
    <div class="mtop10 seaocore_txt_light clr">
      <?php echo $this->translate('Pay with:') ." ".$this->payWithString; $payWithString = '' ?>
    </div>
  <?php endif; ?>
  <!--WORK FOR SHOWING PAYMENT METHODS ENDS -->
  </div>
<?php else: ?>
  <div class="tip mtop10">
    <span class="mbot5">
      <?php echo $this->translate("This product is currently not available for purchase.") ?>
    </span>
  </div>
<?php endif; ?>