<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct_dashboard.css'); ?>
<?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); ?>

<!--IF VIEWER CART IS EMPTY-->
<?php if( empty($this->cartProductsCount) ) : ?>
  <div class="tip" style="margin:0;">
    <span style="margin:0;">
      <?php echo $this->translate("You have no items in your shopping cart."); ?>
    </span>
  </div>
<?php return; endif;?>

<?php if(COUNT($this->getCartProducts)): ?>
<ul id="sitestoreproduct_cart_menu" class="seaocore_sidebar_list sitestoreproduct_mycart_block">
  <?php if( !empty($this->cartProductsCount) ) : ?>
    <li class="top" style="padding:0;">
      <?php 
        echo $this->translate(array('There is %s in your shopping cart.', 'There are %s in your shopping cart.', $this->cartProductsCount), $this->locale()->toNumber($this->cartProductsCount));
        echo $this->htmlLink($this->url(array('action' => 'cart'), 'sitestoreproduct_product_general', true), $this->translate(array('%s item', '%s items', $this->cartProductsCount), $this->locale()->toNumber($this->cartProductsCount)));         
      ?>
      <h4><?php echo $this->translate("Cart SubTotal: %s", Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->cartSubTotal)) ?></h4>
    </li>
  <?php endif;?>
  <?php
    $cartTotal = 0;
    $product_count = 1;
    foreach ($this->getCartProducts as $cart_product) :
      if( $this->limit < $product_count++ ) :
        break;
      endif;
      if( empty($this->viewer_id) ):
        $item_id = $cart_product->product_id;
      
        // IF CONFIGURABLE PRODUUCT
        if( isset($this->product_quantity[$item_id]['config']) && is_array($this->product_quantity[$item_id]['config']) ) : 
          $storeObj = Engine_Api::_()->getItem('sitestore_store', $cart_product->store_id);
          foreach( $this->product_quantity[$item_id]['config'] as $index => $item ) : 
            $quantity = $item;
  ?>
      <li id="sitestoreproduct_cart_product_<?php echo $cart_product->product_id ?>">
        <?php echo $this->htmlLink($cart_product->getHref(), $this->itemPhoto($cart_product, 'thumb.icon')); ?>
        <div class="seaocore_sidebar_list_info">
          <div class="seaocore_sidebar_list_title">
            <?php echo '<a class="seaocore_remove fright" href="javascript:void(0)" onclick="confirmRemoveProduct('.$cart_product->product_id.')" style="margin-top: 4px;"></a>'; ?>
            <?php echo $this->htmlLink($cart_product->getHref(), Engine_Api::_()->sitestoreproduct()->truncation($cart_product->getTitle(), '25'), array('title' => $cart_product->getTitle())); ?>
            <?php if( isset($this->viewerCartConfig[$cart_product->product_id]) && !empty($this->viewerCartConfig[$cart_product->product_id]) ) : 

            ?>
          </div>
          <div class="sitestoreproduct_product_stats seaocore_sidebar_list_details sitestoreproduct_product_cong">
            <?php  
                $configuration = $this->viewerCartConfig[$getProduct->product_id][$index];
                $makeFieldValueArray = Engine_Api::_()->sitestoreproduct()->makeFieldValueArray($configuration);
                foreach($makeFieldValueArray as $key => $makeFieldValue) :
                  echo "$key  $makeFieldValue<br/>";
                endforeach;
              endif;
            ?>
          </div>
          <div class="seaocore_sidebar_list_details">
            <?php echo $this->translate('Store: %s', $this->htmlLink($storeObj->getHref(), $storeObj->getTitle())); ?>
          </div>  

          <div class="seaocore_sidebar_list_details">
            <?php echo $quantity . ' x ' . '<strong class="sitestoreproduct_price_sale">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($cart_product->price) . '</strong>' ?>
          </div>
        </div>
      </li>
       
      <li id="sitestoreproduct_delete_cart_product_<?php echo $item_id ?>" style="display: none">
        <div id="confirm_delete_message_<?php echo $item_id ?>">
          <?php echo $this->translate('Do you really want to delete this product from your shopping cart?').'<br />'; ?>
          &nbsp;&nbsp;<input type="radio" value="1" onclick="deleteProduct(this.value, <?php echo $item_id ?>)" name="delete_product_<?php echo $item_id ?>" style="margin-left:5px;" />Yes <br/>
          &nbsp;&nbsp;<input type="radio" value="0" onclick="deleteProduct(this.value, <?php echo $item_id ?>)" name="delete_product_<?php echo $item_id ?>" style="margin-left:5px;" />No
          <input type="hidden" id="config_index_id" value="<?php echo $index ?>" />
          <input type="hidden" id="config_is_array" value="1" />
        </div>
        <div id="deleted_tip_message_<?php echo $item_id ?>" style="display: none">
          <?php echo $this->translate('Product deleted successfully.') ?>
        </div>
      </li>

<?php   endforeach;
      continue; 
    endif;
  
    $quantity = $this->product_quantity[$item_id];
  else:
    $item_id = $cart_product->cartproduct_id;
    $quantity = $cart_product->quantity;
  endif;
  $storeObj = Engine_Api::_()->getItem('sitestore_store', $cart_product->store_id);
  ?>

      <li id="sitestoreproduct_cart_product_<?php echo $item_id ?>">
        <?php echo $this->htmlLink($cart_product->getHref(), $this->itemPhoto($cart_product, 'thumb.icon')); ?>
        <div class="seaocore_sidebar_list_info">
          <div class="seaocore_sidebar_list_title">
            <?php echo '<a class="seaocore_remove fright" href="javascript:void(0)" onclick="confirmRemoveProduct('.$item_id.')" style="margin-top: 4px;"></a>'; ?>
            <?php echo $this->htmlLink($cart_product->getHref(), $cart_product->getTitle()); ?>
          </div>
          <div class="sitestoreproduct_product_stats seaocore_sidebar_list_details sitestoreproduct_product_cong">
            <?php if($cart_product->product_type == 'configurable' || $cart_product->product_type == 'virtual' ) :
                    $cartProductObject = Engine_Api::_()->getItem('sitestoreproduct_cartproduct', $item_id); 
                      $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($cartProductObject);
                      $otherDetails = $this->fieldValueLoop($cartProductObject, $fieldStructure);    
                      echo htmlspecialchars_decode($otherDetails);
            endif; ?>
          </div>
          <div class="seaocore_sidebar_list_details">
            <?php echo $this->translate('Store: %s', $this->htmlLink($storeObj->getHref(), $storeObj->getTitle())); ?>
          </div>
          <div class="seaocore_sidebar_list_details">
            <?php echo $quantity . ' x ' . '<strong class="sitestoreproduct_price_sale">' . Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($this->productPrice[$cart_product->product_id]) . '</strong>' ?>
          </div>
        </div>
      </li>
      
      <li id="sitestoreproduct_delete_cart_product_<?php echo $item_id ?>" style="display: none">
        <div id="confirm_delete_message_<?php echo $item_id ?>">
          <?php echo $this->translate('Do you really want to delete this product from your shopping cart?').'<br />'; ?>
          <input type="hidden" id="product_id" value="<?php echo $item_id ?>" />
          <input type="radio" id="confirm_yes" value="1" onclick="deleteProduct(this.value, <?php echo $item_id ?>)" name="delete_product_<?php echo $item_id ?>" style="margin-left:5px;" />Yes <br/>
          <input type="radio" id="confirm_no" value="0" onclick="deleteProduct(this.value, <?php echo $item_id ?>)" name="delete_product_<?php echo $item_id ?>" style="margin-left:5px;" />No
        </div>
        <div id="deleted_tip_message_<?php echo $item_id ?>" style="display: none">
          <?php echo $this->translate('Product deleted successfully.') ?>
        </div>

    </li>
<?php endforeach; ?>
</ul>

<?php endif; ?>
