<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: update.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<div id='new_notification'>
  <span>
    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
                               $this->translate(array('%s update', '%s updates', $this->notificationCount), $this->locale()->toNumber($this->notificationCount)),
                               array('id' => 'core_menu_mini_menu_updates_count')) ?>
  </span>
  <span id="core_menu_mini_menu_updates_close">
    <a href="javascript:void(0);" onclick="sm4.activity.hideNotifications();">x</a>
  </span>
</div>