<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-verify.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
    function modifyVerify(verify_id) {
        var comments = $('#edit_comments').val();
        $.ajax({
            url: sm4.core.baseUrl + 'siteverify/index/after-edit-request',
            data: {
                format: 'html',
                verify_id: verify_id,
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
    <div>
        <!--    <div class="top">
              <div class="heading">
        <?php // echo $this->translate("Verify %s", ucfirst($this->resource_title))  ?>
              </div></div>-->
        <div id="verify_popup_content" >
            <div>
                <div class="">
                    <?php // echo $this->htmlLink($this->resource->getHref(), $this->itemPhoto($this->resource, 'thumb.icon'), array('class' => 'item_photo', 'target' => '_parent'));  ?>

                    <div id="siteverify_comment" >
                        <?php echo $this->translate("You can modify or add your verification comments for this member here."); ?>
                        <br/>            
                        <?php
                        if (!empty($this->comments)):
                            ?><textarea id="edit_comments" class="clr mtop10" name="edit_comments" maxlength="300" ><?php echo $this->comments; ?></textarea><?php
                        else:
                            ?><textarea id="edit_comments" class="clr mtop10" name="edit_comments" placeholder="<?php echo $this->translate("Why are you verifying ") . ucfirst($this->resource_title) . '?'; ?>" maxlength="300" ></textarea><?php
                        endif;
                        ?>
           </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="clr">
  <div id="verify_pops_loding_image" style="display: none;">
  </div>
  <button class="widthfull" id="verifybutton" onclick='modifyVerify("<?php echo $this->verify_id ?>");'><?php echo $this->translate("Modify"); ?></button>
    <center><?php echo $this->translate(" or "); ?></center>
 <a onclick="" href="javascript:void(0);" type="button" id="cancel" name="cancel" class="ui-link ui-btn ui-btn-d ui-shadow ui-corner-all" data-rel="back"><?php echo $this->translate('cancel'); ?></a>
</div>

