<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _login_member.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="m10 o_hidden">
  <?php if( empty($this->sitestoreproduct_downloadable_product) && !empty($this->loggedoutViewerCheckout) ) : $checked = "";?>
  <div class="mbot5 clr">
    <input id="checkout_as_guest" type="radio" name="login_member" value="0" checked />
    <label for="checkout_as_guest"><?php echo $this->translate("Checkout as Guest") ?></label>
  </div>
  <?php else:
    $checked = "checked = checked";
  endif; ?>
  <div class="mbot5 clr">
    <?php if( !empty($this->sitestoreproduct_downloadable_product) ) : ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have downloadable products in your cart. Please login or signup to continue checkout.'); ?>
      </span>
    </div>
    <?php endif; ?>
    <input id="registered_member" type="radio" name="login_member" <?php echo $checked ?> value="1" /> 
    <label for="registered_member"><?php echo $this->translate("Login or Signup to checkout") ?></label>
  </div>
</div>

<div>
  <span id="login_error" class="seaocore_txt_red f_small"></span>
</div>

<div class='buttons'>
  <button type='button' name="continue" onclick="loginMethod()" class="m10 fright"><?php echo $this->translate("Continue") ?></button>
  <div id="loading_image_1" class="fright mtop10 ptop10" style="display: inline-block;"></div>
</div>

<script type="text/javascript">
function loginMethod()
{
  var login_method = $$('input[name=login_member]:checked').get('value');
  
  if( login_method.length == 0 )
  {
    $('login_error').innerHTML = '<?php echo $this->translate("Select a login method for checkout process.") ?>';
    $('login_error').style.display = 'block';
  }
  else
  {
    $('login_error').style.display = 'none';
    if( login_method == 0 )
    {
      $('loading_image_1').innerHTML = '<img src='+en4.core.staticBaseUrl+'application/modules/Sitestoreproduct/externals/images/loading.gif height=15 width=15>';
      checkout(2);
    }  
    else
    {
      window.location = en4.core.baseUrl + 'sitestoreproduct/index/checkout/placeOrder/login'
    }
  }
}
</script>
