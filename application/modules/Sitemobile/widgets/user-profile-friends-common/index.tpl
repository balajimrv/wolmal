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
  <ul class="ui-member-list" data-role="listview" data-icon="none">
    <?php foreach ($this->paginator as $user): ?>
      <li>
        <a href="<?php echo $user->getHref() ?>">
          <?php echo $this->itemPhoto($user, 'thumb.icon', $user->getTitle()) ?>
          <div class="ui-list-content">
            <h3><?php echo $user->getTitle() ?></h3>
          </div>	
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>