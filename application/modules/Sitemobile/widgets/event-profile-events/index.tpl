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
<?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
  <div class="listing events-listing" id ="profile_events">
    <ul> 
    <?php foreach ($this->paginator as $event): ?>   
      <li>
        <a class="list-photo" id="list-photo_<?php echo $event->getIdentity() ?>" href="<?php echo $event->getHref(); ?>">
          <?php
          $url = $this->layout()->staticBaseUrl . 'application/modules/Event/externals/images/nophoto_event_thumb_profile.png';
          $temp_url = $event->getPhotoUrl('thumb.profile');
          if (!empty($temp_url)): $url = $event->getPhotoUrl('thumb.profile');
          endif;
          ?>
          <?php
            $rsvp = null;
            $viewer = Engine_Api::_()->user()->getViewer();
            $row = $event->membership()->getRow($viewer);
            if(!empty($row))
            $rsvp = $row->rsvp;
          ?>
          <span style="background-image: url(<?php echo $url; ?>);"></span>
          <h3 id="tick_<?php echo $event->getIdentity() ?>" class="list-title<?php if ($rsvp == 2): ?> tickmark ui-icon-ok<?php endif;?>">
            <?php echo $event->getTitle() ?> 
          </h3>
        </a>
        <div class="list-info">	
          <span class="datemonth">
            <span class="month"><?php echo $this->locale()->toDateTime($event->starttime, array('format' => 'MMM')); ?></span>
            <span class="date"><?php echo $this->locale()->toDateTime($event->starttime, array('format' => 'd')); ?></span>
          </span>
          <span class="list-stats f_small">
            <?php echo $this->locale()->toTime($event->starttime) ?> 
          </span>
          <span class="list-stats f_small">
            <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), $this->locale()->toNumber($event->membership()->getMemberCount())) ?> 
          </span>
        </div> 
      </li>
    <?php endforeach; ?>
    </ul>
  </div>
<?php else :?>
  <div class="sm-content-list" id="profile_events">
    <ul  data-role="listview" data-icon="arrow-r">
      <?php foreach ($this->paginator as $event): ?>
        <li>
          <a href="<?php echo $event->getHref(); ?>">
            <?php echo $this->itemPhoto($event, 'thumb.icon'); ?>
            <h3><?php echo $event->getTitle() ?></h3>
            <p><strong><?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), $this->locale()->toNumber($event->membership()->getMemberCount())) ?></strong></p>
            <p><?php echo $this->locale()->toDateTime($event->starttime) ?></p>
          </a> 
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
<?php if ($this->paginator->count() > 1): ?>
  <?php
  echo $this->paginationAjaxControl(
          $this->paginator, $this->identity, 'profile_events');
  ?>
<?php endif; ?>