<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: success.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div class="sucess_message_content">
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