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

<script type="text/javascript">
  sm4.core.runonce.add(function(){
<?php if (!$this->renderOne): ?>
          sm4.core.Module.getPagination('<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>', '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>', 'profile_groups_events_previous', 'profile_groups_events_next', '<?php echo $this->identity; ?>', 'profile_groups_events', '<?php echo $this->paginator->getCurrentPageNumber(); ?>');
<?php endif; ?>
      });
</script>

<div data-role="controlgroup" data-type="horizontal" class="profile-content-top-button">
  <?php if ($this->canAdd): ?>
    <?php
    echo $this->htmlLink(array(
        'route' => 'event_general',
        'controller' => 'event',
        'action' => 'create',
        'parent_type' => 'group',
        'subject_id' => $this->subject()->getIdentity(),
            ), $this->translate('Add Events'), array(
        // 'class' => 'buttonlink icon_group_photo_new',
        'data-role' => "button", 'data-icon' => "plus", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"
    ))
    ?>
<?php endif; ?>
</div>

  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
	<div class="sm-content-list">	
  	<ul id="profile_groups_events" data-role="listview" data-inset="false" data-icon="false">
        <?php foreach ($this->paginator as $event): ?>
      <li>
        <a href="<?php echo $event->getHref(); ?>">
    <?php echo $this->itemPhoto($event, 'thumb.normal') ?>
          <h3><?php echo $event->getTitle(); ?></h3>
          <p><?php echo $this->translate('By'); ?> <strong><?php echo $event->getOwner()->getTitle(); ?></strong></p>
          <p><?php echo $this->timestamp($event->creation_date) ?></p>
        </a>
      </li>
  <?php endforeach; ?>
  </ul>
	</div>

<?php else: ?>

  <div class="tip">
    <span>
  <?php echo $this->translate('No events have been added to this group yet.'); ?>
    </span>
  </div>

<?php endif; ?>


  <?php if ($this->paginator->count() > 1): ?>

  <div class="ui-grid-a">
  <?php if ($this->paginator->getCurrentPageNumber() > 1): ?>
      <div id="profile_groups_events_previous" class="paginator_previous ui-block-a">
        <button type="button"><?php echo $this->translate('Previous') ?></button>
      </div>
  <?php endif; ?>
  <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
      <div id="profile_groups_events_next" class="paginator_next ui-block-b">
        <button type="button"><?php echo $this->translate('Next') ?></button>
      </div>
  <?php endif; ?>
  </div>

<?php endif; ?>