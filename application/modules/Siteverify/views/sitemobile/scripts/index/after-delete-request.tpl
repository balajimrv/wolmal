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
  	<a href='<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'verify', 'resource_id' => $this->resource_id), 'default', true) ?>' class="smoothbox siteverify_buttonlink"><?php echo $this->translate("Verify %s", ucfirst($this->resource_title)); ?></a>
  </div>
</div>

<?php if ($this->verify_count > 0): ?>
  <div class="siteverify_verify_msg_box" id="<?php echo 'user'; ?>_successMessege_<?php echo $this->resource_id; ?>"  style ='display:block;'>
    <div>
      <?php if ($this->verify_count >= $this->verify_limit): ?>
          <i class="ui-icon ui-icon-ok-sign" style="color: rgb(63, 200, 244);"></i>
      <?php endif; ?>
      <span class="o_hidden siteverify_verify_label"><?php echo $this->translate("%s has been verified by", ucfirst($this->resource_title)) . ' ' . $this->translate(array('%s member', '%s members', $this->verify_count), $this->locale()->toNumber($this->verify_count)) . '.'; ?></span>
    </div>
    <a class="fright f_small" href='<?php echo $this->url(array('module' => 'siteverify', 'controller' => 'index', 'action' => 'content-verify-member-list', 'resource_id' => $this->resource_id), 'default', true) ?>'><?php echo $this->translate("View Details") . " &raquo;"; ?></a>
  </div>
<?php endif; ?>






