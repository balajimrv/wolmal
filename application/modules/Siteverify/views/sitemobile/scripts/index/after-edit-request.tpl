<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: after-edit-request.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="siteverify_verify_msg_box">
<?php
if (!empty($this->admin_approve)) {
  $successMessege_show = "display:block;";
  $adminMessege_show = "display:none;";
  $siteverify_ownerprofile = "display:block;";
} else {
  $successMessege_show = "display:block;";
  $adminMessege_show = "display:block;";
  $siteverify_ownerprofile = "display:none;";
}

if ($this->verify_count > 0):
  ?>
  <div id="<?php echo 'user'; ?>_successMessege_<?php echo $this->resource_id; ?>" class="o_hidden"  style ='<?php echo $successMessege_show; ?>'>
    <div class="o_hidden">
      <?php if ($this->verify_count >= $this->verify_limit): ?>
        <i class="ui-icon ui-icon-ok-sign" style="color: rgb(63, 200, 244);"></i>
      <?php endif; ?>
      <span class="o_hidden siteverify_verify_label"><?php echo $this->translate("%s has been verified by", ucfirst($this->resource_title)) . ' ' . $this->translate(array('%s member', '%s members', $this->verify_count), $this->locale()->toNumber($this->verify_count)) . '.'; ?></span>
    </div>
     <a class="fright f_small" href='<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'content-verify-member-list', 'resource_id' => $this->resource_id), 'default', true) ?>'><?php echo $this->translate("View Details") . " &raquo;"; ?></a>
    <div id="<?php echo 'user'; ?>_successMessege_<?php echo $this->resource_id; ?>" class="o_hidden clr"  style ='<?php echo $siteverify_ownerprofile; ?>'>
      <div><?php echo $this->translate('You have verified this member.'); ?></div>
      <div class="fright f_small">
      <?php if (!empty($this->is_comment)) : ?>
        <a class="smoothbox" href='<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'edit-verify', 'verify_id' => $this->verify_id), 'default', true) ?>'><?php echo $this->translate("Edit"); ?></a><?php endif; ?>
      <?php
      if (!empty($this->is_comment) && !empty($this->allow_unverify))
        echo $this->translate("&nbsp;|&nbsp;");
      ?>
      <?php if (!empty($this->allow_unverify)) : ?>
        <a class="smoothbox" href='<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'delete-verify', 'verify_id' => $this->verify_id), 'default', true) ?>'><?php echo $this->translate("Cancel Verification"); ?></a> <?php endif; ?>
      </div>  
   </div>
  </div>
<?php endif; ?>

<div id="<?php echo 'user'; ?>_adminMessege_<?php echo $this->resource_id; ?>"  class="o_hidden clr" style ='<?php echo $adminMessege_show; ?>'>
  <div>
    <?php
    echo $this->translate("Your verification to %s will be approved by administrator.", ucfirst($this->resource_title)); ?>
    <div class="fright f_small">
      <?php if (!empty($this->is_comment)) : ?>
        <a class="smoothbox" href='<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'edit-verify', 'verify_id' => $this->verify_id), 'default', true) ?>'><?php echo $this->translate("Edit Request"); ?></a><?php endif; ?>
      <?php if (!empty($this->is_comment) && !empty($this->allow_unverify))
        echo $this->translate("&nbsp;|&nbsp;");
      ?>
      <?php if (!empty($this->allow_unverify)) : ?>
        <a class="smoothbox" href='<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'delete-verify', 'verify_id' => $this->verify_id), 'default', true) ?>'><?php echo $this->translate("Cancel Request"); ?></a> <?php endif; ?>
    </div>
  </div>
</div>
</div>