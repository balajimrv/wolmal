<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if ($this->viewer()->getIdentity() || $this->paginator->count() > 1): ?>
  <div data-role="controlgroup" data-type="horizontal" class="profile-content-top-button">
    <?php if ($this->viewer()->getIdentity()): ?>
      <?php
      echo $this->htmlLink(array(
          'route' => 'event_extended',
          'controller' => 'topic',
          'action' => 'create',
          'subject' => $this->subject()->getGuid(),
              ), $this->translate('Post New Topic'), array(
          //'class' => 'buttonlink icon_event_post_new',
          'data-role' => "button", 'data-icon' => "plus", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
      ));
      ?>
  <?php endif; ?>
  </div>
<?php endif; ?>
  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
	<div class="sm-content-list">	
  	<ul class="event_discussions" data-role="listview" data-inset="false" data-icon="false">
    <?php
    foreach ($this->paginator as $topic):
      $lastpost = $topic->getLastPost();
      $lastposter = $topic->getLastPoster();
      ?>
      <li>
        <a href="<?php echo $topic->getHref(); ?>">
          <h3<?php if ($topic->sticky): ?> class='event_discussions_sticky'<?php endif; ?>>
    <?php echo $topic->getTitle() ?>
          </h3>
          <p class="ui-li-aside"><strong> <?php echo $this->translate(array('%s reply', '%s replies', $topic->post_count - 1), $this->locale()->toNumber($topic->post_count - 1)) ?></strong></p>
          <p><?php echo $this->translate('Last Post') ?> <?php echo $this->translate('by'); ?> <strong><?php echo $lastposter->getTitle() ?></strong></p>
        </a>
      </li>
  <?php endforeach; ?>
  </ul>
	</div>
    <?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('No topics have been posted in this event yet.'); ?>
    </span>
  </div>
<?php endif; ?>