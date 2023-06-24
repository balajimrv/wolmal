<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: success.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div class='sucess_message_content'>
  <?php $isFalge = false; ?>
  <?php foreach ($this->messages as $message): // Show messages ?>
    <?php if (!empty($message)): ?>
      <?php $isFalge = true; ?>
      <div class="sucess_message">
        <?php echo $message ?>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
  <?php if (!$isFalge): ?>
    <div class="sucess_message">
      <?php echo $this->translate("Succesfully!") ?>
    </div>
  <?php endif; ?>
</div>