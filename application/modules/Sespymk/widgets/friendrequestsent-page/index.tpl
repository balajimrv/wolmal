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
  var userWidgetRequestSend = function(action, user_id, notification_id, event) {
  
    event.stopPropagation();
    var url;
    if( action == 'confirm' ) {
      url = '<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'friendspymk', 'action' => 'confirm'), 'default', true) ?>';
    } else if( action == 'reject' ) {
      url = '<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'friendspymk', 'action' => 'reject'), 'default', true) ?>';
    } else if( action == 'add' ) {
      url = '<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'friendspymk', 'action' => 'add'), 'default', true) ?>';
    } else if( action == 'cancel' ) {
      url = '<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'friendspymk', 'action' => 'cancel'), 'default', true) ?>';
    } else {
      return false;
    }
    if($('sesbasic_loading_cont_overlay_' + notification_id))
      $('sesbasic_loading_cont_overlay_' + notification_id).style.display = 'block';
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
            
          if($('sesbasic_loading_cont_overlay_' + notification_id))
						$('sesbasic_loading_cont_overlay_' + notification_id).style.display='none';
						
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

<script type="text/javascript">
  function loadMoreSent() {
  
    if ($('view_more_sent'))
      $('view_more_sent').style.display = "<?php echo ( $this->peopleyoumayknow->count() == $this->peopleyoumayknow->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>";

    if(document.getElementById('view_more_sent'))
      document.getElementById('view_more_sent').style.display = 'none';
    
    if(document.getElementById('loading_image_sent'))
     document.getElementById('loading_image_sent').style.display = '';

    en4.core.request.send(new Request.HTML({
      method: 'post',              
      'url': en4.core.baseUrl + 'widget/index/mod/sespymk/name/friendrequestsent-page',
      'data': {
        format: 'html',
        page: "<?php echo sprintf('%d', $this->peopleyoumayknow->getCurrentPageNumber() + 1) ?>",
        viewmore: 1,
        params: '<?php echo json_encode($this->all_params); ?>',
        
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('sespymk_user_main_sent').innerHTML = document.getElementById('sespymk_user_main_sent').innerHTML + responseHTML;
        
        if(document.getElementById('view_more_sent'))
          document.getElementById('view_more_sent').destroy();
        
        if(document.getElementById('loading_image_sent'))
         document.getElementById('loading_image_sent').destroy();
               if(document.getElementById('loadmore_list_sent'))
         document.getElementById('loadmore_list_sent').destroy();
      }
    }));
    return false;
  }
</script>
<div class="sespymk_requests_container sesbasic_clearfix sesbasic_bxs">
  <?php if (empty($this->viewmore) && $this->linktopage): ?>
    <div class="sespymk_list_more sesbasic_clearfix">
      <a href="findfriends"><?php echo $this->translate("View Received Requests") ?></a>
    </div>
  <?php endif; ?>
<?php if( $this->peopleyoumayknow->getTotalItemCount() > 0 ): ?>  
  <?php if($this->showType):  ?>
  <?php if (empty($this->viewmore)): ?>
    <ul class='sespymk_horrizontal_list sesbasic_bxs sesbasic_clearfix' id="sespymk_user_main_sent">
  <?php endif; ?>
      <?php foreach( $this->peopleyoumayknow as $notification ): ?>
        <?php $user = Engine_Api::_()->getItem('user', $notification->user_id);?>
        <li id="sespymk_user_<?php echo $notification->user_id ?>" class="prelative sespymk_user_<?php echo $notification->user_id ?> sespymk_horrizontal_list_item sesbasic_clearfix"  value="<?php //echo $notification->getIdentity();?>" style="height:<?php echo $this->height ?>px;width:<?php echo $this->horiwidth ?>px;">
          <div class="sespymk_horrizontal_list_item_photo" style="height:<?php echo $this->horiheight ?>px;">
            <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user)) ?>
          </div>
          <div class="sespymk_horrizontal_list_item_cont">
            <!--<a href="javascript:void(0);" class="sespymk_horrizontal_list_remove fa fa-close" onclick='removePeopleYouMayKnow(<?php //echo $user->getIdentity(); ?>)' title="<?php //echo $this->translate('Remove');?>"></a>-->
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
              <button type="submit" onclick='userWidgetRequestSend("cancel", <?php echo $this->string()->escapeJavascript($user->user_id) ?>, <?php echo $user->user_id ?>, event)'><?php echo $this->translate('Cancel Friend Requests');?></button>
            </div>
          </div>
          <div class="sesbasic_loading_cont_overlay" id="sesbasic_loading_cont_overlay_<?php echo $notification->user_id ?>"></div>
        </li>
      <?php endforeach; ?>
      <?php if (!empty($this->peopleyoumayknow) && $this->peopleyoumayknow->count() > 1): ?>
        <?php if ($this->peopleyoumayknow->getCurrentPageNumber() < $this->peopleyoumayknow->count()): ?>
          <div class="clr" id="loadmore_list_sent"></div>
          <div class="sesbasic_view_more sesbasic_load_btn" id="view_more_sent" onclick="loadMoreSent();" style="display: block;">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link_sent', 'class' => 'sesbasic_animation sesbasic_link_btn fa fa-repeat')); ?>
          </div>
          <div class="sesbasic_view_more_loading" id="loading_image_sent" style="display: none;">
            <span class="sesbasic_link_btn"><i class="fa fa-spinner fa-spin"></i></span>
          </div>
        <?php endif; ?>
      <?php endif; ?>
  <?php if (empty($this->viewmore)): ?>
    </ul>
  <?php endif; ?>
  <?php else:  ?>
  <?php if (empty($this->viewmore)): ?>
    <ul class='sespymk_list sesbasic_bxs sesbasic_clearfix' id="sespymk_user_main_sent">
  <?php endif; ?>
    <?php foreach( $this->peopleyoumayknow as $notification ): ?>
      <?php $user = Engine_Api::_()->getItem('user', $notification->user_id);?>
      <li id="sespymk_user_<?php echo $notification->user_id ?>" class="prelative sespymk_user_<?php echo $notification->user_id ?> sespymk_list_item sesbasic_clearfix"  value="<?php //echo $notification->getIdentity();?>">
        <div class="sespymk_list_item_inner">
          <div class="sespymk_list_item_photo">
            <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile')) ?>
          </div>
          <div class="sespymk_list_item_cont">
            <div class="sespymk_list_item_title">
              <a href="<?php echo $user->getHref(); ?>"><?php echo $user->getTitle(); ?></a>
            </div>
            <?php if($this->memberEnable): ?>
              <?php if(in_array('friends', $this->showdetails) && Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible && $cfriends = $user->membership()->getMemberCount($user) && !$this->viewer->isSelf($user)):?>
                <div class="sespymk_list_item_stat">
                  <a href="<?php echo $this->url(array('user_id' => $user->user_id,'action'=>'get-friends','format'=>'smoothbox'), 'sesmember_general', true); ?>" class="opensmoothboxurl"><?php echo  $cfriends. $this->translate(' Friends');?></a>
                </div>
              <?php endif;?>
              <?php if(in_array('mutualfriends', $this->showdetails) && ($this->viewer->getIdentity() && !$this->viewer->isSelf($user)) && $mcount =  Engine_Api::_()->sesmember()->getMutualFriendCount($user, $this->viewer) ): ?> 
                <div class="sespymk_list_item_stat">
                  <a href="<?php echo $this->url(array('user_id' => $user->user_id,'action'=>'get-mutual-friends','format'=>'smoothbox'), 'sesmember_general', true); ?>" class="opensmoothboxurl"><?php echo $mcount. $this->translate(' Mutual Friends'); ?></a>
                </div>
              <?php endif;?>
            <?php endif; ?>
          </div> 
          <div class="sespymk_list_item_btn rightT">
            <button type="submit" onclick='userWidgetRequestSend("add", <?php echo $this->string()->escapeJavascript($user->user_id) ?>, <?php echo $user->user_id ?>, event)'><i class="fa fa-user-plus"></i><?php echo $this->translate('Cancel Friend Requests');?></button>
            
            <!--<a href="javascript:void(0);" class="sesbasic_button" onclick='removePeopleYouMayKnow(<?php //echo $user->getIdentity(); ?>)'><?php //echo $this->translate('Remove');?></a>-->

          </div> 
        </div>
        <div class="sesbasic_loading_cont_overlay" id="sesbasic_loading_cont_overlay_<?php echo $notification->user_id ?>"></div>
      </li>
    <?php endforeach; ?>
    
    
    
    <?php if (!empty($this->peopleyoumayknow) && $this->peopleyoumayknow->count() > 1): ?>
      <?php if ($this->peopleyoumayknow->getCurrentPageNumber() < $this->peopleyoumayknow->count()): ?>
        <div class="clr" id="loadmore_list_sent"></div>
        <div class="sesbasic_view_more sesbasic_load_btn" id="view_more_sent" onclick="loadMoreSent();" style="display: block;">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link_sent', 'class' => 'sesbasic_animation sesbasic_link_btn fa fa-repeat')); ?>
        </div>
        <div class="sesbasic_view_more_loading" id="loading_image_sent" style="display: none;">
          <span class="sesbasic_link_btn"><i class="fa fa-spinner fa-spin"></i></span>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  <?php if (empty($this->viewmore)): ?>
  </ul>
  <?php endif; ?>
  <?php endif; ?>
  <?php else:?>
    <div class="tip"><span style="margin:10px 0;"><?php echo $this->translate('There are no sent requests.');?></span></div>
  <?php endif;?>
</div>
<?php if($this->paginationType == 1): ?>
  <script type="text/javascript">    
     //Take refrences from: http://mootools-users.660466.n2.nabble.com/Fixing-an-element-on-page-scroll-td1100601.html
    //Take refrences from: http://davidwalsh.name/mootools-scrollspy-load
    en4.core.runonce.add(function() {
      var paginatorCount = '<?php echo $this->peopleyoumayknow->count(); ?>';
      var paginatorCurrentPageNumber = '<?php echo $this->peopleyoumayknow->getCurrentPageNumber(); ?>';
      function ScrollLoaderSent() { 
        var scrollTopSent = document.documentElement.scrollTop || document.body.scrollTop;
        if($('loadmore_list_sent')) {
          if (scrollTopSent > 40)
            loadMoreSent();
        }
      }
      window.addEvent('scroll', function() {
        ScrollLoaderSent(); 
      });
    });    
  </script>
<?php endif; ?>

<script type="text/javascript">
  
  function removePeopleYouMayKnow(id) {
    //event.stopPropagation();
    sesJqueryObject('.sespymk_user_'+id).fadeOut("slow", function(){
      sesJqueryObject('.sespymk_user_'+id).remove();
    });
    if (document.getElementById('sespymk_user_main_sent').getChildren().length == 0) {
      document.getElementById('sespymk_user_main_sent').innerHTML = "<div class='tip' id=''><span><?php echo $this->translate('There are no more members.');?> </span></div>";
    }
  }
</script>