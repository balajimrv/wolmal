<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<?php if (count($this->paginator) > 0): ?>
  <?php if (Engine_Api::_()->sitemobile()->isApp()): ?>
<?php if(!$this->autoContentLoad) : ?>
    <div class="listing events-listing">
      <ul id='manageevent_ul'> 
        <?php endif;?>
        <?php foreach ($this->paginator as $event): ?>
        <!--TO SHOW TICKMARK FOR ATTENDING--> 
        <?php $viewer = Engine_Api::_()->user()->getViewer();
        if($viewer->getIdentity()):
        $row = $event->membership()->getRow($viewer); 
        $rsvp = $row->rsvp;
        endif;?>
        <!--END TICKMARK-->
          <li>
            <a class="list-photo" id="list-photo_<?php echo $event->getIdentity() ?>" href="<?php echo $event->getHref(); ?>">
              <?php $url= $this->layout()->staticBaseUrl . 'application/modules/Event/externals/images/nophoto_event_thumb_profile.png'; $temp_url=$event->getPhotoUrl('thumb.profile'); if(!empty($temp_url)): $url=$event->getPhotoUrl('thumb.profile'); endif;?>
              <span style="background-image: url(<?php echo $url; ?>);"></span>
              <h3 id="tick_<?php echo $event->getIdentity() ?>" class="list-title <?php if($rsvp == 2):?> tickmark ui-icon-ok <?php endif;?>">
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
                <?php if(!empty($event->location))echo $this->translate(" at ").$event->location;  ?>
              </span>
              <span class="list-stats f_small">
              <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), $this->locale()->toNumber($event->membership()->getMemberCount())) ?> |    
              <?php echo $this->translate('led by') ?>
              <a href="<?php echo $event->getOwner()->getHref(); ?>"><strong><?php echo $event->getOwner()->getTitle(); ?></strong></a>
              </span>  
              <a href="#event_popup_<?php echo $event->getIdentity() ?>" data-rel="popup" data-transition="pop" class="righticon ui-icon-ellipsis-vertical"></a>
            </div>
            <div data-role="popup" id="event_popup_<?php echo $event->getIdentity() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
              <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
              <?php if ($this->viewer() && $event->isOwner($this->viewer())): ?>
                <?php
                echo $this->htmlLink(array('route' => 'event_specific', 'action' => 'edit', 'event_id' => $event->getIdentity()), $this->translate('Edit Event'), array(
                    'class' => 'ui-btn-default ui-btn-action'
                ))
                ?>
                <?php
                echo $this->htmlLink(array('route' => 'default', 'module' => 'event', 'controller' => 'event', 'action' => 'delete', 'event_id' => $event->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Event'), array(
                    'class' => 'smoothbox ui-btn-default ui-btn-danger'
                ));
                ?>
              <?php endif; ?>
              <?php if ($this->viewer() && !$event->membership()->isMember($this->viewer(), null)): ?>
                <?php
                echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'join', 'event_id' => $event->getIdentity()), $this->translate('Join Event'), array(
                    'class' => 'ui-btn-default ui-btn-action smoothbox'
                ))
                ?>
                <?php elseif ($this->viewer() && $event->membership()->isMember($this->viewer()) && !$event->isOwner($this->viewer())): ?>
                  <?php
                  echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'leave', 'event_id' => $event->getIdentity()), $this->translate('Leave Event'), array(
                      'class' => 'ui-btn-default ui-btn-danger smoothbox'
                  ))
                  ?>
        <?php endif; ?>
                   <div class="ui-btn-default dblock chnage-rsvp">
                    <?php echo $this->translate('Change RSVP')?>
                    <form class="event_rsvp_form" action="<?php echo $this->url() ?>" method="post"  onsubmit="return false;">                        <fieldset data-role="controlgroup" data-mini="true" class="events_rsvp" id="rsvp_options_<?php echo $event->getIdentity();?>" data-eventurl='<?php echo $this->url(array('module' => 'event', 'controller' => 'widget', 'action' => 'profile-rsvp','subject' => $event->getGuid()), 'default', true); ?>' data-eventid="<?php echo $event->getIdentity() ?>" >
                        <input type="radio" name="rsvp_options" id="rsvp_option_2" value="2" <?php if ($rsvp == 2): ?> checked="true" <?php endif; ?> />
                        <label for="rsvp_option_2"><?php echo $this->translate('Attending'); ?></label>	
                        <input type="radio"  class="rsvp_options" name="rsvp_options" id="rsvp_option_1" value="1" <?php if ($rsvp == 1): ?> checked="true" <?php endif; ?> />
                        <label for="rsvp_option_1"><?php echo $this->translate('Maybe Attending'); ?></label>
                        <input type="radio"  class="rsvp_options" name="rsvp_options" id="rsvp_option_0" value="0" <?php if ($rsvp == 0): ?> checked="true" <?php endif; ?> />
                        <label for="rsvp_option_0"><?php echo $this->translate('Not Attending'); ?></label>	
                      </fieldset>
                    </form>
                  </div>
              </div>
            </div>
          </li>
        <?php endforeach; ?>  
          <?php if(!$this->autoContentLoad) : ?>
      </ul>
    </div>
<?php endif;?>
<?php else: ?>
  <div class="sm-content-list">
    <ul data-role="listview" data-inset="false" id='manageevent_ul'>
      <?php foreach ($this->paginator as $event): ?>
        <li data-icon="cog" data-inset="true">
          <a href="<?php echo $event->getHref(); ?>">
            <?php echo $this->itemPhoto($event, 'thumb.icon'); ?>
            <h3><?php echo $event->getTitle() ?></h3>
            <p>
              <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), $this->locale()->toNumber($event->membership()->getMemberCount())) ?>   
              <?php echo $this->translate('led by') ?>
              <strong><?php echo $event->getOwner()->getTitle(); ?></strong>
            </p>
            <p><?php echo $this->locale()->toDateTime($event->starttime) ?></p>
          </a>
          <a href="#manage_<?php echo $event->getGuid() ?>" data-rel="popup" data-transition="pop"></a>
          <div data-role="popup" id="manage_<?php echo $event->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
            <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
              <h3><?php echo $event->getTitle() ?></h3>
              <?php if ($this->viewer() && $event->isOwner($this->viewer())): ?>
                <?php
                echo $this->htmlLink(array('route' => 'event_specific', 'action' => 'edit', 'event_id' => $event->getIdentity()), $this->translate('Edit Event'), array(
                    'class' => 'ui-btn-default ui-btn-action'
                ))
                ?>
                <?php
                echo $this->htmlLink(array('route' => 'default', 'module' => 'event', 'controller' => 'event', 'action' => 'delete', 'event_id' => $event->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Event'), array(
                    'class' => 'smoothbox ui-btn-default ui-btn-danger'
                ));
                ?>
              <?php endif; ?>
              <?php if ($this->viewer() && !$event->membership()->isMember($this->viewer(), null)): ?>
                <?php
                echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'join', 'event_id' => $event->getIdentity()), $this->translate('Join Event'), array(
                    'class' => 'ui-btn-default ui-btn-action smoothbox'
                ))
                ?>
                <?php elseif ($this->viewer() && $event->membership()->isMember($this->viewer()) && !$event->isOwner($this->viewer())): ?>
                  <?php
                  echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'leave', 'event_id' => $event->getIdentity()), $this->translate('Leave Event'), array(
                      'class' => 'ui-btn-default ui-btn-danger smoothbox'
                  ))
                  ?>
        <?php endif; ?>
              <a href="#" data-rel="back" class="ui-btn-default">
                <?php echo $this->translate('Cancel'); ?>
              </a>
            </div>
          </div>
        </li>
  <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  <?php if ($this->paginator->count() > 1 && !Engine_Api::_()->sitemobile()->isApp()): ?>
        <?php
        echo $this->paginationControl($this->paginator, null, null, array(
            'query' => array('view' => $this->view, 'text' => $this->text)
        ));
        ?>
      <?php endif; ?>	
  
 <?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have not joined any events yet.') ?>
        <?php if ($this->canCreate && !Engine_Api::_()->sitemobile()->isApp()): ?>
          <?php echo $this->translate('Why don\'t you %1$screate one%2$s?', '<a href="' . $this->url(array('action' => 'create'), 'event_general') . '">', '</a>') ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>
<script type="text/javascript">
<?php if (Engine_Api::_()->sitemobile()->isApp()) { ?>
      
         sm4.core.runonce.add(function() {   
     var ul_id = 'manageevent_ul';
      //call function to change rsvp - bind using click
      
       sm4app.core.Module.event.changeRsvpApp(ul_id);
              var activepage_id = sm4.activity.activityUpdateHandler.getIndexId();
              sm4.core.Module.core.activeParams[activepage_id] = {'currentPage' : '<?php echo sprintf('%d', $this->page) ?>', 'totalPages' : '<?php echo sprintf('%d', $this->totalPages) ?>', 'formValues' : <?php echo json_encode($this->formValues);?>, 'contentUrl' : '<?php echo $this->url(array('action' => 'manage'));?>', 'activeRequest' : false, 'container' : 'manageevent_ul' }; 
          });
          
         
   <?php } ?>    
</script>