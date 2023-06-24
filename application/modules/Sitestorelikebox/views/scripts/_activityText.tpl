<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorelikebox
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _activityText.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if( empty($this->actions) ) {
  echo $this->translate("The action you are looking for does not exist.");
  return;
	} else {
   $actions = $this->actions;
	} ?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Activity/externals/scripts/core.js');
$this->videoPlayerJs();?>

<ul class='splb_feed' id="activity-feed">
  
	<?php
		foreach( $actions as $action ): // (goes to the end of the file)
			try { // prevents a bad feed item from destroying the entire store
			// Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
			if( !$action->getTypeInfo()->enabled ) continue;
			if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
			if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;
			ob_start();
	?>
	  <?php if( !$this->noList ): ?><li id="activity-item-<?php echo $action->action_id ?>"><?php endif; ?>
	    <?php // User's profile photo ?>
	    <div class='splb_feed_item_photo'>
	    	<?php echo $this->htmlLink($action->getSubject()->getHref(),
	      $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle()), array('target' => '_blank')) ?>
	    </div>
    	<div class='splb_feed_item_body'>
	      <?php // Main Content ?>
	      <span class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'splb_feed_item_posted' : 'splb_feed_item_generated' ) ?>">
	        <?php echo str_replace('<a ','<a target="_blank" ',$action->getContent()); ?>
	      </span>

      <?php // Attachments ?>
      <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
        <div class='splb_feed_item_attachments'>
          <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>

              <?php foreach( $action->getAttachments() as $attachment ): ?>
                <span class='splb_feed_attachment_<?php echo $attachment->meta->type ?>'>
                <?php if( $attachment->meta->mode == 0 ): // Silence ?>
                <?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
                  <div>
                    <?php if( $attachment->item->getPhotoUrl() ): ?>
                      <?php 

                          $attribs = Array('target'=>'_blank');

                      ?>
                      <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs ) ?>
                    <?php endif; ?>
                    <div>
                      <div class='splb_feed_item_link_title'>
                        <?php

                            $attribs = Array('target'=>'_blank');

                          echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs, array('target' => '_blank'));
                        ?>
                      </div>
                      <div class='splb_feed_item_link_desc'>
                        <?php echo $this->viewMore($attachment->item->getDescription()) ?>
                      </div>
                    </div>
                  </div>
                <?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?>
                  <div class="feed_attachment_photo">
                    <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), array('class' => 'feed_item_thumb','target'=>'_blank')) ?>
                  </div>
                <?php //elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
                  <?php //echo $this->viewMore($attachment->item->getDescription()); ?>
                <?php //elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@todo) ?>
                <?php endif; ?>
                </span>
              <?php endforeach; ?>
         
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php // Icon, time since, action links ?>
      <?php
//         $icon_type = 'activity_icon_'.$action->type;
//         list($attachment) = $action->getAttachments();
//         if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item ):
//           $icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
//         endif;

      ?>
      <!--<div class='splb_item_date'>
        <?php //echo $this->timestamp($action->getTimeValue()) ?>
      </div>-->
    </div>
  <?php if( !$this->noList ): ?></li><?php endif; ?>

<?php
      ob_end_flush();
    } catch (Exception $e) {
      ob_end_clean();
      if( APPLICATION_ENV === 'development' ) {
        echo $e->__toString();
      }
    };
  endforeach;
?>

</ul>
