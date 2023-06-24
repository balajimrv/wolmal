<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-Verify.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<script type="text/javascript">
  function unVerify(verify_id) {
    $('#verify_pops_loding_image').css("display", "none");
      $.ajax({
   url: sm4.core.baseUrl + 'siteverify/index/after-delete-request',
   data: {
        format: 'html',
        verify_id: verify_id
      },
   success: function(data) {
       $.mobile.changePage($.mobile.navigate.history.getActive().url, {
      reloadPage: true,
      showLoadMsg: true
    });
   },
});
  }
</script>


<div class="global_form_popup">
  <div class="mtop10">
    <p><?php echo $this->translate("Are you sure you want to cancel your verification for this member?"); ?></p>
  </div>
  <div class="clr">
    <div id="verify_pops_loding_image" style="display: none;">
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' />
    </div>
    <button class="widthfull" id="verifybutton" onclick='unVerify("<?php echo $this->verify_id ?>");'><?php echo $this->translate("Cancel Verification"); ?></button>
    <center><?php echo $this->translate(" or "); ?></center>
    <a onclick="" href="javascript:void(0);" type="button" id="cancel" name="cancel" class="ui-link ui-btn ui-btn-d ui-shadow ui-corner-all" data-rel="back"><?php echo $this->translate('cancel'); ?></a>
  </div>
</div>