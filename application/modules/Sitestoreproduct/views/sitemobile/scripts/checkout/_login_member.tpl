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
  <?php if (empty($this->sitestoreproduct_downloadable_product)) : $checked = ""; ?>
    <div class="mbot5 clr">
      <input id="checkout_as_guest" type="radio" name="login_member" value="0" checked />
      <label for="checkout_as_guest"><?php echo $this->translate("Checkout as Guest") ?></label>
    </div>
  <?php
  else:
    $checked = "checked = checked";
  endif;
  ?>
  <div class="mbot5 clr">
        <?php if (!empty($checked)) : ?>
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

<div id="login_error" style="display: none">
  <span class="r_text f_small">
    <?php echo $this->translate("Select a login method for checkout process.") ?>
  </span>
</div>

<div class='buttons'>
  <button type='button' data-theme="b" name="continue" onclick="loginMethod()" class="m10 fright"><?php echo $this->translate("Continue") ?></button>
  <div id="loading_image_1" class="t_center clr"></div>
</div>

<script type="text/javascript">
    function loginMethod()
    {
      var login_method = $.mobile.activePage.find('input[name=login_member]:checked');

      if (login_method.length == 0)
      {
       $.mobile.activePage.find('#login_error').css("display", 'block');
      }
      else
      {
        $.mobile.activePage.find('#login_error').css("display", 'none');
        if (login_method.attr('value') == 0)
        {
          $.mobile.activePage.find('#loading_image_1').html('<img src=' + sm4.core.staticBaseUrl + 'application/modules/Sitemobile/modules/Core/externals/images/loading.gif />');
          checkout(2);
        }
        else
        {
         $.mobile.changePage(sm4.core.baseUrl + 'sitestoreproduct/index/checkout/placeOrder/login');
        }
      }
    }
</script>
