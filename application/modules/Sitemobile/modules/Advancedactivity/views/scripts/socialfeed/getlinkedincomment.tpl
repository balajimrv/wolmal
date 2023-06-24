<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getcomment.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $user = $this->user; ?>

<script>
      $('#count-feedcomments').html(parseInt($('#count-feedcomments').attr('data-message')) + parseInt(1) + ' ' + '<?php echo $this->translate('commments');?>');
       if (typeof $('#activity-item-' + sm4.socialactivity.linkedin.timestamp + '-linkedin').find('.feed_comments span').get(0) == 'undefined') {
         var commentHTML = '<span class="sep">-</span><a href="javascript:void(0);" onclick=\'sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'socialfeed', 'action' => 'view-linkedin-comments', 'post_id' => $this->post_id, 'like_count' => $this->like_count, 'timestamp' => sm4.socialactivity.linkedin.timestamp), 'default', 'true'); ?>", "feedsharepopup")\' class="feed_comments"><span></span></a>';
         
         $('#activity-item-' + sm4.socialactivity.linkedin.timestamp + '-linkedin').find('.feed_item_btm').append(commentHTML);       
         
       }
       $('#activity-item-' + sm4.socialactivity.linkedin.timestamp + '-linkedin').find('.feed_comments span').html(parseInt($('#count-feedcomments').attr('data-message')) + parseInt(1) + ' ' + '<?php echo $this->translate('commments');?>');

</script>
<div class="comments_author_photo">
  
  <?php
  if (isset($user['picture-url'])) {
      $image_url = $user['picture-url'];

  }
  else {
    $image_url = $this->layout()->staticBaseUrl. 'application/modules/User/externals/images/nophoto_user_thumb_icon.png';
  }
  $commenter_photo = '<img src="'.  $image_url. '" alt="" class="thumb_icon item_photo_user" />';
  ?>
  <?php echo $this->htmlLink($user['site-standard-profile-request']['url'], $commenter_photo, array('target' => '_blank')) ?>
</div>
<div class="comments_info">
  <div class='comments_author'>
    <?php $author_name = $user['first-name'] . ' ' . $user['last-name'] ;?>
    <?php echo $this->htmlLink($user['site-standard-profile-request']['url'], $author_name, array('target' => '_blank')); ?>
  </div>
  <div class="comments_body">
    <?php echo $this->viewMore($this->body) ?>
  </div>
  <div class="comments_date">  
 
    <?php echo $this->timestamp(time()); ?>
  </div>
</div>