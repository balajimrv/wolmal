<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php $this->headLink()
					->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteverify/externals/styles/style_siteverify.css') ?>
          
<?php
if(empty($this->viewer_id)){
    $verify_show = "display:none;";
    $successMessege_show = "display:block;";
    $siteverify_ownerprofile = "display:none;";
    $adminMessege_show = "display:none;";
}
elseif ($this->viewer_id != $this->resource_id) {
  if (empty($this->hasVerified)) {
    $verify_show = "display:block;";
    $successMessege_show = "display:block;";
    $siteverify_ownerprofile = "display:none;";
    $adminMessege_show = "display:none;";
  } else {
    if (!empty($this->admin_approve)) {
      $verify_show = "display:none;";
      $successMessege_show = "display:block;";
      $siteverify_ownerprofile = "display:block;";
      $adminMessege_show = "display:none;";
    } else {
      $verify_show = "display:none;";
      $successMessege_show = "display:block;";
      $siteverify_ownerprofile = "display:none;";
      $adminMessege_show = "display:block;";
    }
  }
} else {
  $verify_show = "display:none;";
  $successMessege_show = "display:block;";
  $siteverify_ownerprofile = "display:none;";
  $adminMessege_show = "display:none;";
}
?>

<div id="siteverify" class="clr">
  <div class="siteverify_button_box mbot10" id="<?php echo 'user'; ?>_verify_<?php echo $this->resource_id; ?>" style ='<?php echo $verify_show; ?>' >
	  <div>
    	<a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'verify', 'resource_id' => $this->resource_id,), 'default', true) ?>');" class="siteverify_buttonlink"><?php echo $this->translate("Verify %s", ucfirst($this->resource_title)); ?></a>
    </div>
  </div>
  
  <?php if ($this->verify_count > 0): ?>
      <div class="siteverify_verify_msg_box">
        <div id="<?php echo 'user'; ?>_successMessege_<?php echo $this->resource_id; ?>"  style ='<?php echo $successMessege_show; ?>'>
          <div class="seaocore_txt_light o_hidden">
            <?php if ($this->verify_count >= $this->verify_limit): ?> 
                <div class="fleft siteverify_tick_image">
                    <span class="siteverify_tip"><?php echo $this->translate('Verified Member'); ?><i></i></span>
                </div>
            <?php endif; ?>
            <span class="o_hidden siteverify_verify_label"><?php echo $this->translate("%s has been verified by", ucfirst($this->resource_title)) . ' ' . $this->translate(array('%s member', '%s members', $this->verify_count), $this->locale()->toNumber($this->verify_count)) . '.'; ?>
            </span>  
          </div>
          <a class="siteverify_links mbot10 f_small" href="javascript:void(0)" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'content-verify-member-list', 'resource_id' => $this->resource_id), 'default', true) ?>');"><?php echo $this->translate("View Details") . " &raquo;"; ?></a>
          <div id="<?php echo 'user'; ?>_successMessege_<?php echo $this->resource_id; ?>" class="clr o_hidden"  style ="<?php echo $siteverify_ownerprofile; ?>" >
            <div><?php echo $this->translate('You have verified this member.'); ?></div>
            <div class="siteverify_links f_small">
              <?php if (!empty($this->is_comment)) :
                ?>
                <a href="javascript:void(0)" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'edit-verify', 'verify_id' => $this->verify_id), 'default', true) ?>');"><?php echo $this->translate("Edit"); ?></a><?php endif; ?>
              <?php
              if (!empty($this->is_comment) && !empty($this->allow_unverify))
                echo $this->translate("|");
              ?>
              <?php if (!empty($this->allow_unverify)) : ?>
    
                <a href="javascript:void(0)" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'delete-verify', 'verify_id' => $this->verify_id), 'default', true) ?>');"><?php echo $this->translate("Cancel Verification"); ?></a> <?php endif; ?> 
            </div>
          </div>
        </div>
      </div>
  <?php endif; ?>
  <div class="siteverify_verify_msg_box" style ='<?php echo $adminMessege_show; ?>'>
      <div id="<?php echo 'user'; ?>_adminMessege_<?php echo $this->resource_id; ?>" class="o_hidden clr" style ='<?php echo $adminMessege_show; ?>'>
        <div class="seaocore_txt_light">
          <?php echo $this->translate("Your verification to %s will be approved by administrator.", ucfirst($this->resource_title)); ?>
        </div>
        <div class="siteverify_links">
          <?php if (!empty($this->is_comment)) : ?>
            <a href="javascript:void(0)" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'edit-verify', 'verify_id' => $this->verify_id), 'default', true) ?>');"><?php echo $this->translate("Edit Request"); ?></a><?php endif; ?>
          <?php
          if (!empty($this->is_comment) && !empty($this->allow_unverify))
            echo $this->translate("|");
          ?>
          <?php if (!empty($this->allow_unverify)) : ?>
            <a href="javascript:void(0)" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'delete-verify', 'verify_id' => $this->verify_id), 'default', true) ?>');"><?php echo $this->translate("Cancel Request"); ?></a> <?php endif; ?>
        </div>
      </div>
  </div>
</div>

<script type="text/javascript">

      function showSmoothBox(url) {
        Smoothbox.open(url);
        parent.Smoothbox.close;
      }
</script>