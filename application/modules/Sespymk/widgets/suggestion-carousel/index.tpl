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
<?php if($this->anfheader): ?>
<li class="sesbasic_clearfix">
  <div class="sesadvactivity_peopleyoumayknow">
    <h3><?php echo $this->translate('People You May Know'); ?></h3>
<?php endif; ?>

<?php $randonNumber = $this->identity; ?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<script type="text/javascript" src="<?php echo $baseUrl; ?>application/modules/Sesbasic/externals/scripts/PeriodicalExecuter.js"></script>
<script type="text/javascript" src="<?php echo $baseUrl; ?>application/modules/Sesbasic/externals/scripts/Carousel.js"></script>
<script type="text/javascript" src="<?php echo $baseUrl; ?>application/modules/Sesbasic/externals/scripts/Carousel.Extra.js"></script>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sespymk/externals/styles/styles.css'); ?>

<style>
  #suggestionfriend<?php echo $randonNumber; ?> {
    position: relative;
    height:<?php echo $this->height ?>px;
    overflow: hidden;
  }
</style>
<script type="text/javascript">
  var userWidgetRequestSend_<?php echo $randonNumber ?> = function(action, user_id, notification_id, event) {
  
    event.stopPropagation();
    var url;
    var randonNumber = '<?php echo $randonNumber; ?>';
    if( action == 'confirm' ) {
      url = '<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'friendspymk', 'action' => 'confirm'), 'default', true) ?>';
    } else if( action == 'reject' ) {
      url = '<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'friendspymk', 'action' => 'reject'), 'default', true) ?>';
    } else if( action == 'add' ) {
      url = '<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'friendspymk', 'action' => 'add'), 'default', true) ?>';
    } else {
      return false;
    }
    
    if($('sesbasic_loading_cont_overlay_'+randonNumber))
      $('sesbasic_loading_cont_overlay_'+randonNumber).style.display='block';

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
          if($('sespymk_user_' + notification_id+'_'+randonNumber))
            $('sespymk_user_' + notification_id+'_'+randonNumber).innerHTML = responseJSON.error;
        } else {
          if($('user-widget-request-' + notification_id))
            $('user-widget-request-' + notification_id).innerHTML = responseJSON.message;
          if($('sesbasic_loading_cont_overlay_'+randonNumber))
            $('sesbasic_loading_cont_overlay_'+randonNumber).style.display='none';
          if($('sespymk_user_' + notification_id+'_'+randonNumber))
            $('sespymk_user_' + notification_id+'_'+randonNumber).innerHTML = responseJSON.message;
          sesJqueryObject('.sespymk_user_'+notification_id+'_'+randonNumber).fadeOut("10000", function(){
            setTimeout(function() {
              sesJqueryObject('.sespymk_user_'+notification_id+'_'+randonNumber).remove();
            }, 1000);
          });
        }
      }
    })).send();
  }
</script>
<div class="sespymk_horrizontal_list_more">
  <?php echo $this->htmlLink(array('route' => 'sespymk_general', 'module' => 'sespymk', 'controller' => 'index', 'action' => 'requests'), $this->translate("See All &raquo;")) ?>
</div>
<div class="sesbasic_bxs slide sespymk_carousel_wrapper clearfix <?php if($this->viewType == 'horizontal'): ?> sespymk_carousel_h_wrapper <?php else: ?> sespymk_carousel_v_wrapper <?php endif; ?>">
  <div id="suggestionfriend<?php echo $randonNumber; ?>">
    <?php foreach( $this->peopleyoumayknow as $item ):  ?>
      <?php $user = Engine_Api::_()->getItem('user', $item->user_id);?>
      <div id="sespymk_user_<?php echo $item->user_id ?>_<?php echo $randonNumber; ?>" class="prelative sespymk_user_<?php echo $item->user_id ?>_<?php echo $randonNumber; ?> sespymk_horrizontal_list_item sesbasic_clearfix" value="<?php echo $item->getIdentity();?>" style="height:<?php echo $this->height ?>px;width:<?php echo $this->width ?>px;">
        <div class="sespymk_horrizontal_list_item_photo sesbasic_clearfix" style="height:<?php echo $this->heightphoto ?>px;">
          <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user)) ?>
        </div>
        <div class="sespymk_horrizontal_list_item_cont">
        	<a href="javascript:void(0);" class="sespymk_horrizontal_list_remove fa fa-close" onclick='removePeopleYouMayKnow_<?php echo $randonNumber; ?>(<?php echo $user->getIdentity(); ?>)' title="<?php echo $this->translate('Remove');?>"></a>
          <div class="sespymk_horrizontal_list_item_title">
          	<a href="<?php echo $user->getHref(); ?>"><?php echo $user->getTitle(); ?></a>
          </div>
          
          <?php if($this->memberEnable): ?>
          	<div class="sespymk_horrizontal_list_item_stats">
              <?php if(in_array('friends', $this->showdetails) && Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible && $cfriends = $user->membership()->getMemberCount($user) && !$this->viewer->isSelf($user)):?>
                <div class="sespymk_horrizontal_list_item_stat">
                  <a href="<?php echo $this->url(array('user_id' => $user->user_id,'action'=>'get-friends','format'=>'smoothbox'), 'sesmember_general', true); ?>" class="opensmoothboxurl"><?php echo  $cfriends. $this->translate(' Friends');?></a>
                </div>
              <?php endif;?>
              <?php if(in_array('mutualfriends', $this->showdetails) && ($this->viewer->getIdentity() && !$this->viewer->isSelf($user)) && $mcount =  Engine_Api::_()->sesmember()->getMutualFriendCount($user, $this->viewer) ): ?> 
                <div class="sespymk_horrizontal_list_item_stat">
                  <a href="<?php echo $this->url(array('user_id' => $user->user_id,'action'=>'get-mutual-friends','format'=>'smoothbox'), 'sesmember_general', true); ?>" class="opensmoothboxurl"><?php echo $mcount. $this->translate(' Mutual Friends'); ?></a>
                </div>
              <?php endif;?>
            </div>
          <?php endif; ?>
        	<div class="sespymk_horrizontal_list_item_btn">
          	<button type="submit" onclick='userWidgetRequestSend_<?php echo $randonNumber ?>("add", <?php echo $this->string()->escapeJavascript($user->user_id) ?>, <?php echo $user->user_id ?>, event)'><?php echo $this->translate('Add Friend');?></button>
        	</div>
        </div>
        
        <div class="sesbasic_loading_cont_overlay" id="sesbasic_loading_cont_overlay_<?php echo $randonNumber; ?>"></div>  
      </div>
    <?php endforeach; ?>
  </div>
  <?php if($this->viewType == 'horizontal'): ?>
    <div class="tabs_<?php echo $randonNumber; ?> sespymk_carousel_nav">
      <a class="sespymk_carousel_nav_pre sesbasic_animation" href="#page-p"><i class="fa fa-angle-left"></i></a>
      <a class="sespymk_carousel_nav_nxt sesbasic_animation" href="#page-p"><i class="fa fa-angle-right"></i></a>
    </div>  
  <?php else: ?>
    <div class="tabs_<?php echo $randonNumber; ?> sespymk_carousel_nav">
      <a class="sespymk_carousel_nav_pre" href="#page-p"><i class="fa fa-angle-up"></i></a>
      <a class="sespymk_carousel_nav_nxt" href="#page-p"><i class="fa fa-angle-down"></i></a>
    </div>  
  <?php endif; ?>

</div>
<script type="text/javascript">
window.addEvent('domready', function() {
  var duration = 50,
  div = document.getElement('div.tabs_<?php echo $randonNumber; ?>');
  links = div.getElements('a'),
  carousel = new Carousel.Extra({
    activeClass: 'selected',
    container: 'suggestionfriend<?php echo $randonNumber; ?>',
    circular: true,
    current: 1,
    previous: links.shift(),
    next: links.pop(),
    tabs: links,
    mode: '<?php echo $this->viewType; ?>',
    fx: {
      duration: duration
    }
  })
});
</script>
<script type="text/javascript">
  
  function removePeopleYouMayKnow_<?php echo $randonNumber; ?>(id) {
    event.stopPropagation();
    var randonNumber = '<?php echo $randonNumber; ?>';
    sesJqueryObject('.sespymk_user_'+id+'_'+randonNumber).fadeOut("slow", function(){
      sesJqueryObject('.sespymk_user_'+id+'_'+randonNumber).remove();
    });
    if (document.getElementById('sespymk_user_main').getChildren().length == 0) {
      document.getElementById('sespymk_user_main').innerHTML = "<div class='tip' id=''><span><?php echo $this->translate('There are no more members.');?> </span></div>";
    }
  }
</script>
<?php if($this->anfheader): ?>
</div>
  </li>
<?php endif; ?>