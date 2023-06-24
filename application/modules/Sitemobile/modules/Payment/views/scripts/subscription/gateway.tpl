<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: gateway.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>

<?php if ($this->status == 'pending'): // Check for pending status 
  echo $this->translate('Your subscription is pending payment. You will receive an email when the
  payment completes.');
else: ?>
<div class="subscription-form">
  <form method="get" action="<?php echo $this->escape($this->url(array('action' => 'process'))) ?>" class="global_form global_gateway_form" enctype="application/x-www-form-urlencoded" >
    <div>
      <div>
        <h3>
          <?php echo $this->translate('Pay for Access') ?>
        </h3>
        <?php if ($this->package->recurrence): ?>
          <p class="form-description">
            <?php
            echo $this->translate('You have selected an account type that requires ' .
                    'recurring subscription payments. You will be taken to a secure ' .
                    'checkout area where you can setup your subscription. Remember to ' .
                    'continue back to our site after your purchase to sign in to your ' .
                    'account.')
            ?>
          </p>
          <?php endif; ?>
        <p class='bold' style="padding:5px 0;">
          <?php if ($this->package->recurrence): ?>
            <?php echo $this->translate('Please setup your subscription to continue:') ?>
          <?php else: ?>
            <?php echo $this->translate('Please pay a one-time fee to continue:') ?>
  <?php endif; ?>
  <?php echo $this->package->getPackageDescription() ?>
        </p>
        <div class="form-elements">
          <div class="form_code">
           <?php if($this->sitecouponEnable):?>
            <div id="sitecoupan_add_content">
              <p>
  <?php echo $this->translate('Have a coupon code? Enter it to avail discount.') ?>
              </p>
              <input type="text" name="code" id="code_boxid" placeholder="<?php echo $this->translate('Coupon discount') ?>" />
              <ul class="form-errors" style="display: none">
                <li>
  <?php echo $this->translate('This coupon code is invalid.') ?>
                </li></ul>
              <input type="hidden" name="package_type" id="sitecoupon_package_type" value="payment" />
              <a data-role="button" class="sitecoupan_apply"><?php echo $this->translate('Apply Code') ?></a> 
            </div>
            <div id="sitecoupan_show_content">
              <div id="sitecoupan_loading">
  <?php echo $this->translate('Check,Please wait...') ?>
              </div>
            </div>
            <?php endif; ?>
            <div id="buttons-wrapper" class="form-wrapper">
              <div class="form-element">
                <?php
                foreach ($this->gateways as $gatewayInfo):
                  $gateway = $gatewayInfo['gateway'];
                  $plugin = $gatewayInfo['plugin'];
                  $first = (!isset($first) ? true : false );
                  ?>
                    <?php if (!$first): ?>
                    <?php echo $this->translate('or') ?>
                  <?php endif; ?>
                  <button type="submit" <?php echo $this->dataHtmlAttribs('form_button_submit') ?> name="execute" onclick="$.mobile.activePage.find('#gateway_id').attr('value', '<?php echo $gateway->gateway_id ?>')">
      <?php echo $this->translate('Pay with %1$s', $this->translate($gateway->title)) ?>
                  </button>
    <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" name="gateway_id" id="gateway_id" value="" />
  </form>
</div>
  <?php if($this->sitecouponEnable):?>
<script type="text/javascript">
  sm4.core.runonce.add(function() {
      
       if(sm4.core.isApp()){
        $.mobile.activePage.find('.global_gateway_form').on( "submit", function( event ) {
        var params = $( this ).serialize();
        var paypalUrl = $( this ).attr('action')+"?"+$( this ).serialize();   
        event.preventDefault();
        GatewayPaymentApp.open(paypalUrl,params);
        });
        }
      
    $.mobile.activePage.find('.sitecoupan_apply').on('vclick', function() {

      var code = $.mobile.activePage.find('#code_boxid').val(), mobule_type = $('#sitecoupon_package_type').val();
      if (!code)
        return;
      $.mobile.activePage.find("#sitecoupan_show_content").css('display', 'block');
      $.mobile.activePage.find("#sitecoupan_add_content").css('display', 'none');
      $.mobile.activePage.find('#buttons-wrapper').css('display', 'none');
      $.ajax({
        url: sm4.core.baseUrl + 'sitecoupon/index/index',
        dataType: 'json',
        data: {
          format: 'json',
          code: code,
          package_type: mobule_type
        },
        error: function() {
          $.mobile.activePage.find("#sitecoupan_show_content").css('display', 'none');
          $.mobile.activePage.find("#sitecoupan_add_content").css('display', 'block');
          $.mobile.activePage.find('#buttons-wrapper').css('display', 'block');
        },
        success: function(response, textStatus, xhr) {

          if (response.status === true) {
            $.mobile.activePage.find("#sitecoupan_show_content").css('display', 'block').html(response.body);

            $.mobile.activePage.find('.global_gateway_form').attr('action', sm4.core.baseUrl + 'sitecoupon/index/process/package_type/' + mobule_type);
          } else {
            $.mobile.activePage.find("#sitecoupan_show_content").css('display', 'none');
            $.mobile.activePage.find("#sitecoupan_add_content").css('display', 'block');
            $.mobile.activePage.find("#sitecoupan_add_content").find('.form-errors').css('display', 'block');
          }
          $.mobile.activePage.find('#buttons-wrapper').css('display', 'block');
        }
      });

    });
  });
</script>
<style type="text/css">
  #sitecoupan_show_content{
    display: none;
  }
  #sitecoupan_show_content > ul{
    margin: 0;
    padding: 0;
  }
  #sitecoupan_show_content > ul > li{
    background-color: #e9faeb;
    margin: 3px 5px !important;
    border-radius: 3px;
    margin: 7px 5px;
    padding: 5px;
    overflow: hidden;
    border: 1px solid #ccc;
    display: inline-block;
  }
</style>
<?php endif; ?>
<?php endif; ?>
