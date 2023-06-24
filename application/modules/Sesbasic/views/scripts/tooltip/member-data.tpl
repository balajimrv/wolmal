<?php $subject = $this->subject; ?>
<?php $viewer = Engine_Api::_()->user()->getViewer();?>
<?php if(!$viewer->getIdentity()):?>
  <?php $levelId = 5;?>
<?php else:?>
  <?php $levelId = $viewer;?>
<?php endif;?>
<?php if(in_array('coverphoto',$this->globalEnableTip) && in_array('coverphoto',$this->moduleEnableTip) && isset($this->subject->cover) && $this->subject->cover != 0 && $this->subject->cover != ''){
  $cover = Engine_Api::_()->storage()->get($this->subject->cover, '')->getPhotoUrl(); 
}else
$cover =''; 
?>
<div class="sesbasic_tooltip sesbasic_clearfix sesbasic_bxs<?php if($cover){?> sesbasic_tooltip_cover_wrap<?php } ?>">
	<?php if($cover){?>
  <div class="sesbasic_tooltip_cover">
    <img src="<?php echo $cover; ?>">
    <div class="sesbasic_tooltip_cover_info">
      <?php if(in_array('title',$this->globalEnableTip) && in_array('title',$this->moduleEnableTip)){ ?>
        <div class="sesbasic_tooltip_info_title">  
          <a href="<?php echo $subject->getHref(); ?>"><?php echo $subject->getTitle(); ?></a></a>
        </div>
      <?php } ?>
      <?php if(in_array('rating',$this->moduleEnableTip) && Engine_Api::_()->getApi('core', 'sesmember')->allowReviewRating()):?>
        <div class="sesmember_list_rating">
          <?php echo $this->partial('_userRating.tpl', 'sesmember', array('rating' => $subject->rating)); ?>
        </div>
      <?php endif;?>
    </div>
  </div>
  <?php } ?>
  <?php if(Engine_Api::_()->sesbasic()->isModuleEnable('sesmember')):?>
    <?php $imageURL = $subject->getPhotoUrl('thumb.profile');?>
  <?php else: ?>
    <?php if($subject->photo_id): ?>
      <?php $imageURL = $subject->getPhotoUrl('thumb.profile');?>
    <?php else: ?>
      <?php $imageURL = 'application/modules/User/externals/images/nophoto_user_thumb_profile.png'; ?>
    <?php endif; ?>
  <?php endif; ?>
  <div class="sesbasic_tooltip_content sesbasic_clearfix">
  <?php if(in_array('mainphoto',$this->globalEnableTip) && in_array('mainphoto',$this->moduleEnableTip)){ ?>
    <div class="sesbasic_tooltip_photo sesbd">
      <a href="<?php echo $subject->getHref(); ?>"><img src="<?php echo $imageURL; ?>"></a>
      <?php
      if(in_array('socialshare',$this->moduleEnableTip)){
        $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $subject->getHref());
        $socialshare = '<div class="sesbasic_tooltip_photo_btns"><a href="http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $subject->getTitle().'" onclick="socialSharingPopUp(this.href,'."'".$this->translate('Facebook')."'".');return false;" class="sesbasic_icon_btn sesbasic_icon_facebook_btn sesbutton_share"><i class="fa fa-facebook"></i></a><a href="https://twitter.com/intent/tweet?url=' . $urlencode . '&title=' . $subject->getTitle().'" onclick="socialSharingPopUp(this.href,'."'". $this->translate('Twitter')."'".');return false;" class="sesbasic_icon_btn sesbasic_icon_twitter_btn sesbutton_share"><i class="fa fa-twitter"></i></a><a href="http://pinterest.com/pin/create/button/?url='.$urlencode.'&media='.urlencode((strpos($imageURL,'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'].$imageURL ) : $imageURL)).'&description='.$subject->getTitle().'" onclick="socialSharingPopUp(this.href,'."'".$this->translate('Pinterest')."'" .');return false;" class="sesbasic_icon_btn sesbasic_icon_pintrest_btn sesbutton_share"><i class="fa fa-pinterest"></i></a></div>';
        echo $socialshare;
      }
      ?>
    </div>
   <?php } ?>
    <div class="sesbasic_tooltip_info">
    	<?php if(!$cover){?>
        <?php if(in_array('title',$this->globalEnableTip) && in_array('title',$this->moduleEnableTip)){ ?>
          <div class="sesbasic_tooltip_info_title">  
            <a href="<?php echo $subject->getHref(); ?>"><?php echo $subject->getTitle(); ?></a></a>
          </div>
        <?php } ?>
        <?php if(in_array('rating',$this->moduleEnableTip) && Engine_Api::_()->getApi('core', 'sesmember')->allowReviewRating()):?>
          <div class="sesmember_list_rating">
            <?php echo $this->partial('_userRating.tpl', 'sesmember', array('rating' => $subject->rating)); ?>
          </div>
        <?php endif;?>
      <?php } ?>
	<?php if(in_array('profileType',$this->moduleEnableTip)):?>
	  <div class="sesbasic_tooltip_stats sesmember_list_membertype "> 
	    <span class="widthfull"><i class="fa fa-user"></i><span><?php echo Engine_Api::_()->sesmember()->getProfileType($subject);?></span></span>
	  </div>
        <?php endif;?>
        <?php if(in_array('age',$this->moduleEnableTip)):?>
	  			<?php echo $this->partial('_userAge.tpl', 'sesmember', array('ageActive' => 1, 'member' => $subject));?>
        <?php endif;?>
        <?php if(in_array('friendCount',$this->moduleEnableTip) && Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible && $cfriends = $subject->membership()->getMemberCount($subject) && !$viewer->isSelf($subject)):?>
          <div class="sesbasic_tooltip_stats">
            <span class="widthfull"><i class="fa fa-users"></i><span><a href="<?php echo $this->url(array('user_id' => $subject->user_id,'action'=>'get-friends','format'=>'smoothbox'), 'sesmember_general', true); ?>" class="opensmoothboxurl"><?php echo  $cfriends. $this->translate(' Friends');?></a></span></span>
          </div>
          <?php endif;?>
          <?php if(in_array('mutualFriendCount',$this->moduleEnableTip) && ($viewer->getIdentity() && !$viewer->isSelf($subject)) && $mcount =  Engine_Api::_()->sesmember()->getMutualFriendCount($subject, $viewer) ): ?> 
	    <div class="sesbasic_tooltip_stats">
	      <span class="widthfull"><i class="fa fa-users"></i><span><a href="<?php echo $this->url(array('user_id' => $subject->user_id,'action'=>'get-mutual-friends','format'=>'smoothbox'), 'sesmember_general', true); ?>" class="opensmoothboxurl"><?php echo $mcount. $this->translate(' Mutual Friends'); ?></a></span></span>
	    </div>
          <?php endif;?>
          <?php if(in_array('email',$this->moduleEnableTip)):?>
	    <div class="sesbasic_tooltip_stats">
	    <span class="widthfull"><i class="fa fa-envelope"></i><span><?php echo $subject->email;?></span></span>
	  </div>
        <?php endif;?>
	<div class="sesbasic_tooltip_stats">
	  <?php if(in_array('like',$this->moduleEnableTip)):?>
	    <span title="<?php echo $this->translate(array('%s like', '%s likes', $subject->like_count), $this->locale()->toNumber($subject->like_count))?>"><i class="fa fa-thumbs-up"></i><?php echo $subject->like_count; ?></span>
	  <?php endif;?>
	  <?php if(in_array('view',$this->moduleEnableTip)):?>
	    <span title="<?php echo $this->translate(array('%s view', '%s views', $subject->view_count), $this->locale()->toNumber($subject->view_count))?>"><i class="fa fa-eye "></i><?php echo $subject->view_count; ?></span>
	  <?php endif;?>
	  <?php if(in_array('rating',$this->moduleEnableTip)):?>
	    <?php if(Engine_Api::_()->getApi('core', 'sesmember')->allowReviewRating() && Engine_Api::_()->authorization()->getPermission($levelId, 'sesmember_review', 'view')):?>
	      <span title="<?php echo $this->translate(array('%s rating', '%s ratings', $subject->rating), $this->locale()->toNumber($subject->rating)) ;?>"><i class="fa fa-star"></i><?php echo round($subject->rating,1).'/5';?></span>  
	    <?php endif;?>
	  <?php endif;?>
	</div>
	<?php if( in_array('location',$this->moduleEnableTip) && isset($subject->location) &&  $subject->location && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmember.enable.location', 1)){ ?>
	  <p class="sesbasic_tooltip_stats sesmember_list_stats sesmember_list_location">
	    <span class="widthfull">
	    <i class="fa fa-map-marker" title="<?php echo $this->translate('location'); ?>"></i>
	    <span>
	      <a href="<?php echo $this->url(array('resource_id' => $subject->user_id,'resource_type'=>'user','action'=>'get-direction'), 'sesbasic_get_direction', true); ?>" class="opensmoothboxurl"><?php echo $subject->location; ?></a></span>
	    </span>
	  </p>
	<?php } ?>
    </div>
	</div>
  <div class="sesbasic_tooltip_footer sesbasic_clearfix sesbm clear">
    <div class="floatR">
      <?php if(in_array('friendButton',$this->moduleEnableTip) && $viewer->getIdentity() != 0):?>
	<?php echo '<span>'.$this->partial('_addfriend.tpl', 'sesbasic', array('subject' => $subject)).'</span>'; ?>
      <?php endif;?>
      <?php if(in_array('likeButton',$this->moduleEnableTip) && $viewer->getIdentity() != 0):?>
	<?php $LikeStatus = Engine_Api::_()->sesbasic()->getLikeStatus($subject->user_id,$subject->getType());?>
	<?php $likeClass = (!$LikeStatus) ? 'fa-thumbs-up' : 'fa-thumbs-down' ;?>
	<?php $likeText = ($LikeStatus) ?  $this->translate('Unlike') : $this->translate('Like') ;?>
	<?php echo "<span><a href='javascript:;' data-url='".$subject->getIdentity()."' class='sesbasic_btn sesmember_add_btn sesmember_button_like_user sesmember_button_like_user_". $subject->user_id."'><i class='fa ".$likeClass."'></i><span><i class='fa fa-caret-down'></i>$likeText</span></a></span>";?>
      <?php endif;?>
      <?php if(in_array('followButton',$this->moduleEnableTip) && $viewer->getIdentity() != 0 &&  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmember.follow.active',1) && !$viewer->isSelf($subject)){
	$FollowUser = Engine_Api::_()->sesmember()->getFollowStatus($subject->user_id);
	$followClass = (!$FollowUser) ? 'fa-check' : 'fa-times' ;
	$followText = ($FollowUser) ?  $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmember.follow.unfollowtext','Unfollow')) : $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmember.follow.followtext','Follow'))  ;
	echo "<span><a href='javascript:;' data-url='".$subject->getIdentity()."' class='sesbasic_btn sesmember_add_btn sesmember_follow_user sesmember_follow_user_".$subject->getIdentity()."'><i class='fa ".$followClass."'  title='$followText'></i> <span><i class='fa fa-caret-down'></i> Follow</span></a></span>"; 
      }
      ?>
      <?php if (in_array('message',$this->moduleEnableTip) && Engine_Api::_()->sesbasic()->hasCheckMessage($subject)): ?>
	<?php $baseUrl = $this->baseUrl();?>
	<?php $messageText = $this->translate('Message');?>
	<?php echo "<span><a href=\"$baseUrl/messages/compose/to/$subject->user_id\" target=\"_parent\" title=\"$messageText\" class=\"opensmoothboxurl sesbasic_btn sesmember_add_btn\"><i class=\"fa fa-commenting-o\"></i><span><i class=\"fa fa-caret-down\"></i>Message</span></a></span>"; ?>
      <?php endif; ?>	       
    </div>
  </div>
</div>
<?php die; ?>