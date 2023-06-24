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
<div class="sm-content-list">
  <ul class="ui-member-list" data-role="listview" data-icon="none" id="user_profile_friends">
    <?php
    foreach ($this->friends as $membership):
      if (!isset($this->friendUsers[$membership->resource_id]))
        continue;
      $member = $this->friendUsers[$membership->resource_id];
      ?>
      <li>
        <?php if ($this->userFriendshipSM($member)) : ?>
          <div class="ui-item-member-action">
            <?php echo $this->userFriendshipSM($member) ?>
          </div>
        <?php endif; ?>
        <a href="<?php echo $member->getHref() ?>">
          <?php echo $this->itemPhoto($member, 'thumb.icon') ?>
          <div class="ui-list-content">
            <h3><?php echo $member->getTitle() ?></h3>
          </div>	
        </a>
      </li>
    <?php endforeach ?>    
  </ul>
  <?php if ($this->friends->count() > 1): ?>
    <?php
    echo $this->paginationAjaxControl(
            $this->friends, $this->identity, 'user_profile_friends');
    ?>
  <?php endif; ?>
</div>	