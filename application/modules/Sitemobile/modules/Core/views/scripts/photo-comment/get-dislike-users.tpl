<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-dislike-users.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
    include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/_activitySettings.tpl';
    if($showAsLike) {
       $showLikeWithoutIcon=1;
    }
?>
<?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemobile/modules/User/View/Helper', 'User_View_Helper'); ?>
<?php if ($this->likes->getTotalItemCount() > 0): // COMMENTS -------  ?>
  <?php $viewer = Engine_Api::_()->user()->getViewer(); ?>

  <?php if ($this->page_current == 1): ?>
    <div class="sm-ui-popup-top ui-header ui-bar-a">
      <a data-iconpos="notext" data-role="button" data-icon="chevron-left" data-corners="true" data-shadow="true" class="ui-btn-left " onclick="sm4.core.photocomments.toggleCommentDisLikeList('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>');"><?php //echo $this->translate('back');?></a>
    <a href="javascript:void(0);" data-iconpos="notext" data-role="button" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" onclick=" sm4.core.closefunctions.trigger();" class="ps-close-popup close-feedsharepopup ui-btn-right" ></a>
    
    <?php if($showLikeWithoutIcon != 3):?>
            <h2 class="ui-title"><?php echo $this->translate('People who dislike this'); ?></h2>
    <?php else:?>
        <h2 class="ui-title"><?php echo $this->translate('People who down voted this'); ?></h2>
    <?php endif;?>
  </div>

  <div class="sm-ui-popup-container sm-ui-popup-likes sm-content-list">
    <ul id="dislikes-wrp-ul" class="ui-member-list" data-role="listview" data-icon="none">
   <?php endif; ?>
    <?php foreach ($this->likes as $like): ?>
      <?php $user = $this->item($like->poster_type, $like->poster_id); ?>
      <?php
      $table = Engine_Api::_()->getDbtable('block', 'user');
      $select = $table->select()
              ->where('user_id = ?', $user->getIdentity())
              ->where('blocked_user_id = ?', $viewer->getIdentity())
              ->limit(1);
      $row = $table->fetchRow($select);
      ?>
      <li>
        <?php if ($row == NULL && $this->viewer()->getIdentity() && $this->userFriendshipSM($user)): ?>
          <div class="ui-item-member-action">
            <?php echo $this->userFriendshipSM($user) ?>
          </div>
        <?php endif; ?>
        <a href="<?php echo $user->getHref() ?>">
          <?php echo $this->itemPhoto($user, 'thumb.icon') ?>
          <div class="ui-list-content">
            <h3><?php echo $user->getTitle() ?></h3>
          </div>
        </a>
      </li>
    <?php endforeach; ?>
    <?php if ($this->page_current > 1 && $this->likes->count() > $this->page_current): ?> 
      <div class="dislike_viewmore feed_viewmore" style="display: none;">
      <?php
      echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
          'id' => 'dislike_viewmore_link',
          'class' => 'ui-btn-default icon_viewmore',
          'onclick' => 'sm4.core.photocomments.showDisLikesUsers("'.$this->subject()->getType().'","'.$this->subject()->getIdentity().'",'.($this->page_current+1).')'
      ))
      ?>
    </div>
      <?php endif; ?>
      <?php if ($this->page_current == 1): ?>
        <?php if ( $this->likes->count() >1): ?> 
          </ul>
          <div class="dislike_viewmore feed_viewmore" >
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                'id' => 'dislike_viewmore_link',
                'class' => 'ui-btn-default icon_viewmore',
                'onclick' => 'sm4.core.photocomments.showLikesUsers("' . $this->subject()->getType() . '","' . $this->subject()->getIdentity() . '",' . ($this->page_current + 1) . ')'
            ))
            ?>
          </div>
        <?php endif; ?>
        <div class="feeds_loading dnone" id="dislikes_viewmore_loading">
          <i class="icon_loading"></i>
        </div>
  </div>	
<?php endif; ?>
<?php endif; ?>