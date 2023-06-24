<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteverify
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: after-delete-request.tpl 2014-09-11 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteverify/externals/styles/style_siteverify.css');
?>

          
<div class="siteverify_button_box mbot10" id="<?php echo 'user'; ?>_verify_<?php echo $this->resource_id; ?>" style ='display:block;' >
  <div>
  	<a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'verify', 'resource_id' => $this->resource_id), 'default', true) ?>');" class="siteverify_buttonlink"><?php echo $this->translate("Verify %s", ucfirst($this->resource_title)); ?></a>
  </div>
</div>

<?php if ($this->verify_count > 0): ?>
  <div class="siteverify_verify_msg_box" id="<?php echo 'user'; ?>_successMessege_<?php echo $this->resource_id; ?>"  style ='display:block;'>
    <div class="seaocore_txt_light">
      <?php if ($this->verify_count >= $this->verify_limit): ?>
          <div class="fleft siteverify_tick_image">
            <span class="siteverify_tip"><?php echo $this->translate('Verified Member'); ?><i></i></span>
          </div>
      <?php endif; ?>
      <span class="o_hidden siteverify_verify_label"><?php echo $this->translate("%s has been verified by", ucfirst($this->resource_title)) . ' ' . $this->translate(array('%s member', '%s members', $this->verify_count), $this->locale()->toNumber($this->verify_count)) . '.'; ?></span>
    </div>
    <a class="siteverify_links f_small" href="javascript:void(0)" onclick="showSmoothBox('<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'content-verify-member-list', 'resource_id' => $this->resource_id), 'default', true) ?>');"><?php echo $this->translate("View Details") . " &raquo;"; ?></a>
  </div>
<?php endif; ?>






