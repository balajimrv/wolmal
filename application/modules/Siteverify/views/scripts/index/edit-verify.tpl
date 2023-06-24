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
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteverify/externals/styles/style_siteverify.css');
?>
                    
<script type="text/javascript">
  function modifyVerify(verify_id) {
    var comments = $('edit_comments').value;
    document.getElementById('verify_pops_loding_image').style.display = '';
    var request = new Request.HTML({
      url: en4.core.baseUrl + 'siteverify/index/after-edit-request',
      data: {
        format: 'html',
        verify_id: verify_id,
        comments: comments
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        window.parent.$('siteverify').innerHTML = responseHTML;
        parent.Smoothbox.close();
      }
    });
    request.send();
  }

  window.addEvent('domready', function() {
    textCounter($('edit_comments'), 'counter', 300)
  });

  function textCounter(field, field2, maxlimit)
  {
    var countfield = document.getElementById(field2);
    if (field.value.length > maxlimit) {
      field.value = field.value.substring(0, maxlimit);
      return false;
    } else {
      countfield.innerHTML = maxlimit - field.value.length;
    }

  }
</script>

<div class="seaocore_members_popup global_form_popup" id="verify_members_popup">
  <div>
<!--    <div class="top">
      <div class="heading">
        <?php // echo $this->translate("Verify %s", ucfirst($this->resource_title)) ?>
      </div></div>-->
    <div id="verify_popup_content" >
      <div>
        <div class="">
          <?php // echo $this->htmlLink($this->resource->getHref(), $this->itemPhoto($this->resource, 'thumb.icon'), array('class' => 'item_photo', 'target' => '_parent')); ?>

          <div id="siteverify_comment" >
           <?php echo $this->translate("You can modify or add your verification comments for this member here."); ?>
            <br/>            
              <?php 
                if(!empty($this->comments)): 
                  ?><textarea id="edit_comments" class="clr mtop10" name="edit_comments" maxlength="300" onkeyup="textCounter(this, 'counter', 300);" ><?php echo $this->comments; ?></textarea><?php
                else:
                  ?><textarea id="edit_comments" class="clr mtop10" name="edit_comments" placeholder="<?php echo $this->translate("Why are you verifying ") . ucfirst($this->resource_title) . '?'; ?>" maxlength="300" onkeyup="textCounter(this, 'counter', 300);" ></textarea><?php
                endif;
              ?>
            
          </div>
          <div class="seaocore_browse_list_info_date">
            <span value="300" id="counter"> </span>
            <?php echo $this->translate("characters left."); ?>
          </div>
<!--          <div class="mbot10 mtop10"><?php // echo $this->translate("Use below button to modify your verification:"); ?></div>-->
        </div>
      </div>
    </div>
  </div>
</div>

<div class="fright mtop5 clr">
  <div class="fleft mleft5 mtop5" id="verify_pops_loding_image" style="display: none;">
    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' />
    <?php //echo $this->translate("Loading ...")   ?>
  </div>
  <button class="mleft5" id="verifybutton" onclick='modifyVerify("<?php echo $this->verify_id ?>");'><?php echo $this->translate("Modify"); ?></button>
  <?php echo $this->translate(" or "); ?>
  <a href="javascript:void(0);" onclick='javascript:parent.Smoothbox.close();'>
    <?php echo $this->translate("cancel") ?></a>
</div>

