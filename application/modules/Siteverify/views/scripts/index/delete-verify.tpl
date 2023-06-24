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
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteverify/externals/styles/style_siteverify.css');
?>

<script type="text/javascript">

  function unVerify(verify_id) {
    document.getElementById('verify_pops_loding_image').style.display = '';
    var request = new Request.HTML({
      url: en4.core.baseUrl + 'siteverify/index/after-delete-request',
      data: {
        format: 'html',
        verify_id: verify_id
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        window.parent.$('siteverify').innerHTML = responseHTML;
        parent.Smoothbox.close();
      }
    });
    request.send();
  }
</script>


<div class="global_form_popup">
  <div class="mtop10">
    <p><?php echo $this->translate("Are you sure you want to cancel your verification for this member?"); ?></p>
  </div>
  <div class="fright mtop10 clr">
    <div class=" fleft mtop10" id="verify_pops_loding_image" style="display: none;">
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' />
    </div>
    <button class="mleft5" id="verifybutton" onclick='unVerify("<?php echo $this->verify_id ?>");'><?php echo $this->translate("Cancel Verification"); ?></button>
    <?php echo $this->translate(" or "); ?>
    <a href="javascript:void(0);" onclick='javascript:parent.Smoothbox.close();'><?php echo $this->translate("cancel") ?></a>
  </div>
</div>