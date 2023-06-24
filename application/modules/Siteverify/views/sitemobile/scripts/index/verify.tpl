<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: verify.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">

  function proceedToVerify(resource_id) {
    var comments = '';
    if ($('#comments'))
      comments = $('#comments').val();
    $('#verify_pops_loding_image').css("display", "none");
         $.ajax({
   url: sm4.core.baseUrl + 'siteverify/index/proceed-to-verify',
   data: {
        format: 'html',
        resource_id: resource_id,
        comments: comments
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
<div class="seaocore_members_popup global_form_popup" id="verify_members_popup">

  <h3>
    <?php echo $this->translate("Verify %s ?", ucfirst($this->resource_title)) ?>
  </h3>
  <div class="" id="verify_popup_content" >
    <div class="o_hidden">
      <div>
        <?php echo $this->translate("Are you sure you want to verify %s?", ucfirst($this->resource_title)); ?>
        <?php if (!empty($this->is_comment)): ?>
          <?php echo $this->translate(" You can also add your comment below for this."); ?>
          <div id="siteverify_comment" class="clr" style="display:block;">
            <textarea id="comments" maxlength="300" placeholder="<?php echo $this->translate("Why are you verifying ") . ucfirst($this->resource_title) . '?'; ?>"></textarea>
            </br>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<div class="clr">
  <div id="verify_pops_loding_image" style="display: none;">
<!--    <img src='<?php //echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' />-->
  </div>
  <button class="widthfull" id="verifybutton" onclick='proceedToVerify("<?php echo $this->resource_id ?>");'><?php echo $this->translate("Verify"); ?></button>
  <center><?php echo $this->translate(" or "); ?></center>
  <a onclick="" href="javascript:void(0);" type="button" id="cancel" name="cancel" class="ui-link ui-btn ui-btn-d ui-shadow ui-corner-all" data-rel="back"><?php echo $this->translate('cancel'); ?></a>
</div>
