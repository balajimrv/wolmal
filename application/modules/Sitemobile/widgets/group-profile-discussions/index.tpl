<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php if ($this->canPost || $this->paginator->count() > 1): ?>
  <div data-role="controlgroup" data-type="horizontal" class="profile-content-top-button">
    <?php if ($this->canPost): ?>
      <?php
      echo $this->htmlLink(array(
          'route' => 'group_extended',
          'controller' => 'topic',
          'action' => 'create',
          'subject' => $this->subject()->getGuid(),
              ), $this->translate('Post New Topic'), array(
          //'class' => 'buttonlink icon_event_post_new',
          'data-role' => "button", 'data-icon' => "plus", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
      ))
      ?>
  <?php endif; ?>
  </div>
<?php endif; ?>

  <?php $empty = true;
  if ($this->paginator->getTotalItemCount() > 0): ?>
	<div class="sm-content-list">
		<ul class="group_discussions"  data-role="listview" data-inset="false" data-icon="false">
			<?php
			foreach ($this->paginator as $topic):
				if (empty($topic->lastposter_id)) {
					continue;
				}
				$lastpost = $topic->getLastPost();
				if (!$lastpost) {
					continue;
				}
				$lastposter = $topic->getLastPoster();
				$empty = false;
				?>
				<li>
					<a href="<?php echo $topic->getHref(); ?>">
						<h3<?php if ($topic->sticky): ?> class='group_discussions_sticky'<?php endif; ?>>
			<?php echo $topic->getTitle() ?>
						</h3>
						<p class="ui-li-aside"><strong> <?php echo $this->translate(array('%s reply', '%s replies', $topic->post_count - 1), $this->locale()->toNumber($topic->post_count - 1)) ?></strong></p>
						<p><?php echo $this->translate('Last Post') ?> <?php echo $this->translate('by'); ?> <strong><?php echo $lastposter->getTitle() ?></strong></p>
					</a>
				</li>
		<?php endforeach; ?>
		</ul>
	</div>
    <?php endif; ?>
    <?php if ($empty): ?>
  <?php if ($this->viewer()->getIdentity())
     ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('No topics have been posted in this group yet.'); ?>
    </span>
  </div>
<?php endif; ?>