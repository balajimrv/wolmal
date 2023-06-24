<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-cart.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php
//IF VIEWER CART IS EMPTY
if( !empty($this->sitestoreproduct_viewer_cart_empty) ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There is no product in your shopping cart for delete.');?>
    </span>
  </div> 
<?php return;
  endif; 
?> 

<form method="post" class="global_form_popup">
  <div>
    <?php if( empty($this->clear_shopping_cart) ): ?>
      <h3><?php echo $this->translate("Delete product from shopping cart?") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to delete this product from your shopping cart? Product will not be recoverable after being deleted.") ?>
      </p>
      <?php else : ?>
      <h3><?php echo $this->translate("Delete product’s from shopping cart?") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to clear your shopping cart? Product’s will not be recoverable after being deleted.") ?>
      </p>
      <?php endif; ?>
      <br />

      <p>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo $this->translate(" or ") ?> 
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
  </div>
</form>