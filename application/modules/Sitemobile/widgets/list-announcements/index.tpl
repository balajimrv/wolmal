<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Announcement
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Announcement
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<ul id="announcements" data-role="listview" data-icon="arrow-r">
  <?php foreach ($this->announcements as $item): ?>
    <li>
      <a href="<?php echo $item->getOwner()->getHref(); ?>">
        <h3><?php echo $item->getTitle() ?></h3>
        <p>
          <?php echo $this->translate('Posted by'); ?>
          <strong><?php echo $item->getOwner()->getTitle(); ?></strong>
        </p>
        <p><?php echo $this->timestamp($item->creation_date) ?></p>
        <p><?php echo $item->body ?></p>
      </a> 
    </li>
  <?php endforeach; ?>
</ul>