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
          ( empty($this->sitestoreproduct->end_date_enable) || $this->sitestoreproduct->end_date > date('Y-m-d H:i:s') ) && ((!empty($temp_allowed_selling) && $this->sitestoreproduct->allow_purchase) || !empty($temp_non_selling_product_price))
        ) : 
?>
  <div class="sitestoreproduct_pcbox clr">
    <!-- IF PRODUCT IS IN STOCK AND AVAILABLE FOR PURCHASING -->
    <?php // if (!empty($this->sitestoreproduct->stock_unlimited) || $this->sitestoreproduct->in_stock >= $this->sitestoreproduct->min_order_quantity) : ?>
    <?php if(!empty($temp_allowed_selling) && $this->sitestoreproduct->allow_purchase):?>    
    <!--Changes done here-->
        <?php $actionUrl = $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'quick-view'), '', '') ?>
      <form method="post" action="<?php echo $actionUrl ?>">
        <button type='submit' data-theme="b" name="update_shopping_cart" class="add_to_cart_button">
    <?php echo $this->translate("Add to Cart") ?>
        </button>
      </form>
    <?php else: ?>
    <?php if (!empty($this->out_of_stock_action)) : ?>
        <div id="notify_to_seller" class="mbot10">
          <div>
      <?php echo $this->translate("Notify me when this product is in stock:"); ?>
          </div>
          <span id="notify_to_me_email_error" class="r_text mtop5" style="display:none">
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
    <span class="btnlink">
    <?php echo $this->addToWishlistSitestoreproduct($this->sitestoreproduct, array('classIcon' => 'sr_sitestoreproduct_wishlist_href_link', 'classLink' => '')); ?>
    </span>
      <?php if (!empty($this->create_review)): ?>
      <span class="btnlink">
      <?php echo $this->content()->renderWidget("sitestoreproduct.review-button", array('product_guid' => $this->sitestoreproduct->getGuid(), 'product_profile_page' => 1, 'identity' => $this->identity, 'isProductProfile' => 1, 'isQuickView' => $this->isQuickView)) ?>
      </span>
  <?php endif; ?>
  </div>
<?php else: ?>
  <div class="tip mtop10">
    <span class="mbot5">
  <?php echo $this->translate("This product is currently not available for purchase.") ?>
    </span>
  </div>
<?php endif; ?>