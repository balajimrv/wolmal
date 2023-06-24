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
  <div class="place-host-info">
    <ul data-inset="false" data-role="listview" class="ui-listview">
      <li class="ui-first-child ui-last-child">
          <span class="icon"><i class="ui-icon-time"> </i></span>
          <?php
        // Convert the dates for the viewer
        $startDateObject = new Zend_Date(strtotime($this->subject->starttime));
        $endDateObject = new Zend_Date(strtotime($this->subject->endtime));
        if ($this->viewer() && $this->viewer()->getIdentity()) {
          $tz = $this->viewer()->timezone;
          $startDateObject->setTimezone($tz);
          $endDateObject->setTimezone($tz);
        }
        ?>
        <?php if ($this->subject->starttime == $this->subject->endtime): ?>
          <p class="details seevent-details">
              <?php echo $this->locale()->toDate($startDateObject,array('format' => 'MMM-d-yy')).$this->translate(" at ").$this->locale()->toTime($startDateObject); ?>    
          </p>
        <?php elseif ($startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd')): ?>         
          <p class="details seevent-details">
            <?php echo $this->locale()->toDate($startDateObject,array('format' => 'MMM-d-yy')).$this->translate(" at ").$this->locale()->toTime($startDateObject).(" to "). $this->locale()->toTime($endDateObject)?>  
          </p>
        <?php else: ?>  
          <p class="details seevent-details"> 
          <?php echo $this->translate($this->locale()->toDate($startDateObject,array('format' => 'd MMMM')));?>
            -
          <?php echo $this->translate($this->locale()->toDate($endDateObject,array('format' => 'd MMMM')));?>
          <span>
          <?php echo $this->translate($this->locale()->toDate($startDateObject,array('format' => 'd MMMM'))).$this->translate(' at ').$this->locale()->toTime($startDateObject).$this->translate(" to ").$this->translate($this->locale()->toDate($endDateObject,array('format' => 'd MMMM'))).$this->translate(' at ').$this->locale()->toTime($endDateObject);?>
          </span>
        </p>
        <?php endif ?>
      </li>
       <?php if (!empty($this->subject->location)): ?>
          <li class="ui-first-child">
            <span class="icon"><i class="ui-icon-map-marker"> </i></span>
            <p class="details"><?php echo $this->subject->location; ?> <?php echo $this->htmlLink('https://maps.google.com/?q=' . urlencode($this->subject->location), $this->translate('[Map]'), array('target' => 'blank')) ?></p>
          </li>
        <?php endif; ?>
      <?php if (!empty($this->subject->host)): ?>
      <li class="ui-first-child">
          <span class="icon"><i class="ui-icon-user"> </i></span>         
          <p class="details"><?php echo $this->translate('Led by') ?> <?php echo $this->subject->getParent()->__toString() ?></p>
      </li>
      <?php endif; ?>
    </ul>
 </div>
<?php 
if ($this->rsvp == '2'): ?>
<!--<div class="tickmark ui-icon-ok"><?php //echo $this->translate('Going'); ?></div>-->
<?php endif;?>
  <div class="attending-probability">
    <div>
      <ul>
        <li>
          <span><?php echo $this->locale()->toNumber($this->subject->getAttendingCount()) ?></span>
          <span><?php echo $this->translate('attending'); ?></span>
        </li>
        <li>
          <span><?php echo $this->locale()->toNumber($this->subject->getMaybeCount()) ?></span>
          <span><?php echo $this->translate('maybe </br>attending'); ?></span>
        </li>
        <li>
          <span><?php echo $this->locale()->toNumber($this->subject->getNotAttendingCount()) ?></span>
          <span><?php echo $this->translate('not </br>attending'); ?></span>
        </li>
        <li>
          <span><?php echo $this->locale()->toNumber($this->subject->getAwaitingReplyCount()) ?></span>
          <span><?php echo $this->translate('awaiting </br>reply'); ?></span>
        </li>
      </ul>
    </div>
  </div>
  <div class="ui-page-content sm-widget-block content_cover_profile_fields profile-details">
    <h4><?php echo $this->translate('Details'); ?></h4>
    <ul>
      <?php if (!empty($this->subject->description)): ?>
        <li class="t_light"><?php echo nl2br($this->subject->description) ?></li>
      <?php endif ?>

       <?php if (!empty($this->subject->category_id)): ?>
          <li>
            <span class="t_light"><?php echo $this->translate('Category:') ?></span>
            <span>
              <?php
              echo $this->htmlLink(array(
                  'route' => 'event_general',
                  'action' => 'browse',
                  'category_id' => $this->subject->category_id,
                      ), $this->translate((string) $this->subject->categoryName()))
              ?>
            </span>
          </li>
        <?php endif ?>
      </ul>
  </div>
<?php else :?>
<?php if (!empty($this->event_info_collapsible)) : ?>
  <div class="sm_ui_item_profile_details" data-role="collapsible" <?php if(!empty($this->event_info_collapsible_default)):?> data-collapsed='false' <?php else:?> data-collapsed='true' <?php endif;?> id="collapsibles" data-mini="true">
    <h3><?php echo $this->translate('Event Details'); ?></h3>
  <?php else: ?>
    <div class="sm_ui_item_profile_details">
    <?php endif; ?>
    <table>
      <tbody>
        <?php if (!empty($this->subject->description)): ?>
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Details') ?></div></td>
            <td><?php echo nl2br($this->subject->description) ?></td>
          </tr>
        <?php endif ?>
        <?php
        // Convert the dates for the viewer
        $startDateObject = new Zend_Date(strtotime($this->subject->starttime));
        $endDateObject = new Zend_Date(strtotime($this->subject->endtime));
        if ($this->viewer() && $this->viewer()->getIdentity()) {
          $tz = $this->viewer()->timezone;
          $startDateObject->setTimezone($tz);
          $endDateObject->setTimezone($tz);
        }
        ?>
        <?php if ($this->subject->starttime == $this->subject->endtime): ?>
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Date') ?></div></td>
            <td>
              <?php echo $this->locale()->toDate($startDateObject) ?>
            </td>
          </tr> 
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Time') ?></div></td>
            <td>
              <?php echo $this->locale()->toTime($startDateObject) ?>
            </td>
          </tr> 
        <?php elseif ($startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd')): ?>
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Date') ?></div></td>
            <td>
              <?php echo $this->locale()->toDate($startDateObject) ?>
            </td>
          </tr> 
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Time') ?></div></td>
            <td>
              <?php echo $this->locale()->toTime($startDateObject) ?>
              -
              <?php echo $this->locale()->toTime($endDateObject) ?>
            </td>
          </tr>
        <?php else: ?>  
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('When') ?></div></td>
            <td>
              <div class="event_stats_content">
                <?php
                echo $this->translate('%1$s at %2$s', $this->locale()->toDate($startDateObject), $this->locale()->toTime($startDateObject)
                )
                ?>
                - 
                <?php
                echo $this->translate('%1$s at %2$s', $this->locale()->toDate($endDateObject), $this->locale()->toTime($endDateObject)
                )
                ?>
              </div>
            </td>
          </tr>
        <?php endif ?>
        <?php if (!empty($this->subject->location)): ?>
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Where') ?></div></td>
            <td><?php echo $this->subject->location; ?> <?php echo $this->htmlLink('https://maps.google.com/?q=' . urlencode($this->subject->location), $this->translate('[Map]'), array('target' => 'blank')) ?></td>
          </tr>
        <?php endif ?>

        <?php if (!empty($this->subject->host)): ?>
          <?php if ($this->subject->host != $this->subject->getParent()->getTitle()): ?>
            <tr valign="top">
              <td class="label"><div><?php echo $this->translate('Host') ?></div></td>
              <td><?php echo $this->subject->host ?></td>
            </tr>
          <?php endif ?>
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Led by') ?></div></td>
            <td><?php echo $this->subject->getParent()->__toString() ?></td>
          </tr>
        <?php endif ?>

        <?php if (!empty($this->subject->category_id)): ?>
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Category') ?></div></td>
            <td>
              <?php
              echo $this->htmlLink(array(
                  'route' => 'event_general',
                  'action' => 'browse',
                  'category_id' => $this->subject->category_id,
                      ), $this->translate((string) $this->subject->categoryName()))
              ?>
            </td>
          </tr>
<?php endif ?>
        <tr valign="top">
          <td class="label"><div><?php echo $this->translate('RSVPs'); ?></div></td>
          <td>
            <ul>
              <li>
                <strong><?php echo $this->locale()->toNumber($this->subject->getAttendingCount()) ?></strong>
                <span><?php echo $this->translate('attending'); ?></span>
              </li>
              <li>
                <strong><?php echo $this->locale()->toNumber($this->subject->getMaybeCount()) ?></strong>
                <span><?php echo $this->translate('maybe attending'); ?></span>
              </li>
              <li>
                <strong><?php echo $this->locale()->toNumber($this->subject->getNotAttendingCount()) ?></strong>
                <span><?php echo $this->translate('not attending'); ?></span>
              </li>
              <li>
                <strong><?php echo $this->locale()->toNumber($this->subject->getAwaitingReplyCount()) ?></strong>
                <span><?php echo $this->translate('awaiting reply'); ?></span>
              </li>
            </ul>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
<?php endif; ?>
