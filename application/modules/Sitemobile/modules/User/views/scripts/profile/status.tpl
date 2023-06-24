<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: status.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<div id='profile_status'>
  <h2>
    <?php echo $this->subject()->getTitle() ?>
  </h2>
  <?php if ($this->auth): ?>
    <span>
      <?php echo $this->viewMore($this->subject()->status) ?>
    </span>
  <?php endif; ?>
</div>

<?php if (!$this->auth): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('This profile is private - only friends of this member may view it.'); ?>
    </span>
  </div>
  <br />
<?php endif; ?>