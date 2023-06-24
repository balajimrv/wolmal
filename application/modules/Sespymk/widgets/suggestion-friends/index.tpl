<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sespymk
 * @package    Sespymk
 * @copyright  Copyright 2016-2017 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2017-03-03 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sespymk/externals/styles/styles.css'); ?>
<script type="text/javascript">
  var userWidgetRequestSend = function(action, user_id, notification_id, event)
  {
    event.stopPropagation();
    var url;
    if( action == 'confirm' ) {
      url = '<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'friendspymk', 'action' => 'confirm'), 'default', true) ?>';
    } else if( action == 'reject' ) {
      url = '<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'friendspymk', 'action' => 'reject'), 'default', true) ?>';
    } else if( action == 'add' ) {
      url = '<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'friendspymk', 'action' => 'add'), 'default', true) ?>';
    } else {
      return false;
    }

    (new Request.JSON({
      'url' : url,
      'data' : {
        'user_id' : user_id,
        'format' : 'json',
        'token' : '<?php echo $this->token() ?>'
      },
      'onSuccess' : function(responseJSON) {
        if( !responseJSON.status ) {
          if($('user-widget-request-' + notification_id))
            $('user-widget-request-' + notification_id).innerHTML = responseJSON.error;
          if($('sespymk_user_' + notification_id))
            $('sespymk_user_' + notification_id).innerHTML = responseJSON.error;
        } else {
          if($('user-widget-request-' + notification_id))
            $('user-widget-request-' + notification_id).innerHTML = responseJSON.message;
          if($('sespymk_user_' + notification_id))
            $('sespymk_user_' + notification_id).innerHTML = responseJSON.message;
          sesJqueryObject('.sespymk_user_'+notification_id).fadeOut("slow", function(){
            setTimeout(function() {
              sesJqueryObject('.sespymk_user_'+notification_id).remove();
            }, 1000);
          });
        }
      }
    })).send();
  }
</script>
<?php if($this->peopleyoumayknow): ?>
	<?php if($this->showType):?>
  	<div class="sespymk_horrizontal_list_more">
    	<?php echo $this->htmlLink(array('route' => 'sespymk_general', 'module' => 'sespymk', 'controller' => 'index', 'action' => 'requests'), $this->translate("See All &raquo;")) ?>
    </div>
  	<ul class='sespymk_horrizontal_list sesbasic_bxs sesbasic_clearfix' id="sespymk_user_main">
      <?php foreach( $this->peopleyoumayknow as $notification ): ?>
      <?php $user = Engine_Api::_()->getItem('user', $notification->user_id);?>
        <li id="sespymk_user_<?php echo $notification->user_id ?>" class="sespymk_user_<?php echo $notification->user_id ?> sespymk_horrizontal_list_item sesbasic_clearfix"  value="<?php echo $notification->getIdentity();?>" style="width:<?php echo $this->horiwidth ?>px;">
          <div class="sespymk_horrizontal_list_item_photo" style="height:<?php echo $this->horiheight ?>px;">
            <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, '')) ?>
          </div>
          <div class="sespymk_horrizontal_list_item_cont">
            <a href="javascript:void(0);" class="sespymk_horrizontal_list_remove fa fa-close" onclick='removePeopleYouMayKnow(<?php echo $user->getIdentity(); ?>)' title="<?php echo $this->translate('Remove');?>"></a>
            <div class="sespymk_horrizontal_list_item_title">
              <a href="<?php echo $user->getHref(); ?>"><?php echo $user->getTitle(); ?></a>
            </div>
            <?php if($this->memberEnable): ?>
              <div class="sespymk_horrizontal_list_item_stats">	
                <?php if(in_array('mutualfriends', $this->showdetails) && ($this->viewer->getIdentity() && !$this->viewer->isSelf($user)) && $mcount =  Engine_Api::_()->sesmember()->getMutualFriendCount($user, $this->viewer) ): ?> 
                  <div class="sespymk_horrizontal_list_item_stat">
                    <a href="<?php echo $this->url(array('user_id' => $user->user_id,'action'=>'get-mutual-friends','format'=>'smoothbox'), 'sesmember_general', true); ?>" class="opensmoothboxurl"><?php echo $mcount. $this->translate(' Mutual Friends'); ?></a>
                  </div>
                <?php endif;?>
                <?php if(in_array('friends', $this->showdetails) && Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible && $cfriends = $user->membership()->getMemberCount($user) && !$this->viewer->isSelf($user)):?>
                  <div class="sespymk_horrizontal_list_item_stat">
                    <a href="<?php echo $this->url(array('user_id' => $user->user_id,'action'=>'get-friends','format'=>'smoothbox'), 'sesmember_general', true); ?>" class="opensmoothboxurl"><?php echo  $cfriends. $this->translate(' Friends');?></a>
                  </div>
                <?php endif;?>
              </div>
            <?php endif; ?>
            <div class="sespymk_horrizontal_list_item_btn">
              <button type="submit" onclick='userWidgetRequestSend("add", <?php echo $this->string()->escapeJavascript($user->user_id) ?>, <?php echo $user->user_id ?>, event)'><?php echo $this->translate('Add Friend');?></button>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
  	<ul class='sespymk_sidebar_list sesbasic_bxs sesbasic_clearfix sesbasic_sidebar_block' id="sespymk_user_main">
      <?php foreach( $this->peopleyoumayknow as $notification ): ?>
        <?php $user = Engine_Api::_()->getItem('user', $notification->user_id);?>
        <li id="sespymk_user_<?php echo $notification->user_id ?>" class="sespymk_user_<?php echo $notification->user_id ?> sespymk_sidebar_list_item sesbasic_clearfix"  value="<?php echo $notification->getIdentity();?>">
          <div class="sespymk_sidebar_list_item_photo">
            <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
          </div>
          <div class="sespymk_sidebar_list_item_cont">
            <a href="javascript:void(0);" class="sespymk_remove fa fa-close" onclick='removePeopleYouMayKnow(<?php echo $user->getIdentity(); ?>)' title="<?php echo $this->translate('Remove');?>"></a>
            <div class="sespymk_sidebar_list_item_title">
              <a href="<?php echo $user->getHref(); ?>"><?php echo $user->getTitle(); ?></a>
            </div>
            <?php if($this->memberEnable): ?>
              <?php if(in_array('friends', $this->showdetails) && Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible && $cfriends = $user->membership()->getMemberCount($user) && !$this->viewer->isSelf($user)):?>
                <div class="sespymk_sidebar_list_item_stat">
                  <a href="<?php echo $this->url(array('user_id' => $user->user_id,'action'=>'get-friends','format'=>'smoothbox'), 'sesmember_general', true); ?>" class="opensmoothboxurl"><?php echo  $cfriends. $this->translate(' Friends');?></a>
                </div>
              <?php endif;?>
              <?php if(in_array('mutualfriends', $this->showdetails) && ($this->viewer->getIdentity() && !$this->viewer->isSelf($user)) && $mcount =  Engine_Api::_()->sesmember()->getMutualFriendCount($user, $this->viewer) ): ?> 
                <div class="sespymk_sidebar_list_item_stat">
                  <a href="<?php echo $this->url(array('user_id' => $user->user_id,'action'=>'get-mutual-friends','format'=>'smoothbox'), 'sesmember_general', true); ?>" class="opensmoothboxurl"><?php echo $mcount. $this->translate(' Mutual Friends'); ?></a>
                </div>
              <?php endif;?>
            <?php endif; ?>
            <div class="sespymk_sidebar_list_item_btn">
              <button type="submit" onclick='userWidgetRequestSend("add", <?php echo $this->string()->escapeJavascript($user->user_id) ?>, <?php echo $user->user_id ?>, event)'><?php echo $this->translate('Add Friend');?></button>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
      <li class="sespymk_sidebar_list_more">
      	<?php echo $this->htmlLink(array('route' => 'sespymk_general', 'module' => 'sespymk', 'controller' => 'index', 'action' => 'requests'), $this->translate("See All &raquo;")) ?>
      </li>
    </ul>
      
	<?php endif; ?>

<?php endif; ?>
<script type="text/javascript">
  
  function removePeopleYouMayKnow(id) {
    event.stopPropagation();
    sesJqueryObject('.sespymk_user_'+id).fadeOut("slow", function(){
      sesJqueryObject('.sespymk_user_'+id).remove();
    });
//     if($('sespymk_user_' +id)) {
//       $('sespymk_user_'+id).destroy();
//     }
    if (document.getElementById('sespymk_user_main').getChildren().length == 0) {
      document.getElementById('sespymk_user_main').innerHTML = "<div class='tip' id=''><span><?php echo $this->translate('There are no more members.');?> </span></div>";
    }
  }
</script>