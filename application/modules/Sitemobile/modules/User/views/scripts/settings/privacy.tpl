<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: privacy.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>


<?php echo $this->form->render($this) ?>

<div id="blockedUserList" style="display:none;">
  <ul>
    <?php foreach ($this->blockedUsers as $user): ?>
      <?php if ($user instanceof User_Model_User && $user->getIdentity()) : ?>
        <li>[
          <?php echo $this->htmlLink(array('controller' => 'block', 'action' => 'remove', 'user_id' => $user->getIdentity()), 'Unblock', array('class' => 'smoothbox')) ?>
          ] <?php echo $user->getTitle() ?></li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
</div>

<script type="text/javascript">

  $(document).bind('ready',function () {
    $('#blockList-element').append($('#blockedUserList ul')[0]);
  });

</script>