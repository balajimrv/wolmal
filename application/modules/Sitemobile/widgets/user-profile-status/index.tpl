<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
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
    <br>
  </h2>
  <?php if ($this->auth): ?>
    <span class="profile_status_text" id="user_profile_status_container">
      <?php echo $this->viewMore($this->subject()->status) ?>
      <?php if (!empty($this->subject()->status) && $this->subject()->isSelf($this->viewer())): ?>
        <a class="profile_status_clear" href="javascript:void(0);" onclick="sm4.user.clearStatus();">(<?php echo $this->translate('clear') ?>)</a>
      <?php endif; ?>
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