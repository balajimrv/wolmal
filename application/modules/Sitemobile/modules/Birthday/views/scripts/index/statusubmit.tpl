<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: statussubmit.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $action = $this->action; ?>
<?php $object = $action->getObject();
$remove_patern = ' &rarr; ' . $object->toString(array('class' => 'feed_item_username')) ?>
<li class="clr View_More_Birthday_Feed_<?php echo $action->action_id ?>" id="activity-item-<?php echo $action->action_id ?>">

  <div id="main-feed-<?php echo $action->action_id ?>">
    <div class="feed_item_header"> 
      <div class='feed_item_photo <?php echo 'Hide_' . $action->getSubject()->getType() . "_" . $action->getSubject()->getIdentity() ?>'>    
        <?php echo $this->htmlLink($action->getSubject()->getHref(), $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle())) ?>
      </div>
      <div class="feed_item_status">
        <div class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_posted' ) ?>">
          <?php echo str_replace($remove_patern, "", $action->getContent()); ?>
        </div>
      </div>
    </div>    

    <div class="feed_item_body">

      <?php // Icon, time since, action links ?>
      <?php
      $canComment = ( $action->getTypeInfo()->commentable &&
              $this->viewer()->getIdentity() &&
              Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'));
      ?>

    </div>
    <div class="feed_item_btm">
      <span class="feed_item_date">
<?php echo $this->timestamp($action->getTimeValue()) ?>
      </span>

    </div>

    <div class="feed_item_option">
<?php if ($canComment): ?>
        <div data-role="navbar" data-inset="false">
          <ul>
            <?php if ($canComment): ?>
    <?php if ($action->likes()->isLike($this->viewer())): ?>
                <li>
                  <a href="javascript:void(0);" onclick="javascript:sm4.activity.unlike('<?php echo $action->action_id ?>');">
                    <i class="ui-icon ui-icon-thumbs-down"></i>
                    <span><?php echo $this->translate('Unlike') ?></span>
                  </a>
                </li>
    <?php else: ?>
                <li> 
                  <a href="javascript:void(0);" onclick="javascript:sm4.activity.like('<?php echo $action->action_id ?>');">
                    <i class="ui-icon ui-icon-thumbs-up"></i>
                    <span><?php echo $this->translate('Like') ?></span>
                  </a>
                </li>
              <?php endif; ?>
    <?php if (Engine_Api::_()->getApi('settings', 'core')->core_spam_comment): // Comments - likes   ?>
                <li>
                  <a href="<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity(), 'format' => 'smoothbox'), 'default', 'true'); ?>">
                    <i class="ui-icon ui-icon-comment"></i>
                    <span><?php echo $this->translate('Comment'); ?></span>
                  </a>
                </li>
    <?php else: ?>
                <li>
                  <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity()), 'default', 'true'); ?>" , "feedsharepopup")'>
                    <i class="ui-icon ui-icon-comment"></i>
                    <span><?php echo $this->translate('Comment'); ?></span>
                  </a>
                </li>
              <?php endif; ?>
  <?php endif; ?>	           
          </ul>
        </div>
<?php endif; ?>
    </div> 
  </div>


</li>
