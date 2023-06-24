<?php $subject = $this->subject; ?>
<?php $viewer = Engine_Api::_()->user()->getViewer();?>
<div class="sesbasic_tooltip sesbasic_clearfix sesbasic_bxs">
  <?php if(!empty($subject->photo_id)): ?>
    <?php $imageURL = $subject->getPhotoUrl('thumb.profile');?>
  <?php else: ?>
    <?php $imageURL = 'application/modules/User/externals/images/nophoto_user_thumb_profile.png'; ?>
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
  <?php if(in_array('title',$this->globalEnableTip) && in_array('title',$this->moduleEnableTip)){ ?>
   <div class="sesbasic_tooltip_info_title">  
     <a href="<?php echo $subject->getHref(); ?>"><?php echo $subject->getTitle(); ?></a></a>
   </div>
  <?php } ?>
	<div class="sesbasic_tooltip_stats">
	  <?php if(in_array('like',$this->moduleEnableTip) && !empty($subject->like_count)):?>
	    <span title="<?php echo $this->translate(array('%s like', '%s likes', $subject->like_count), $this->locale()->toNumber($subject->like_count))?>"><i class="fa fa-thumbs-up"></i><?php echo $subject->like_count; ?></span>
	  <?php endif;?>
	  <?php if(in_array('view',$this->moduleEnableTip) && !empty($subject->view_count)):?>
	    <span title="<?php echo $this->translate(array('%s view', '%s views', $subject->view_count), $this->locale()->toNumber($subject->view_count))?>"><i class="fa fa-eye "></i><?php echo $subject->view_count; ?></span>
	  <?php endif;?>
    <?php if(in_array('comment',$this->moduleEnableTip) && !empty($subject->comment_count)):?>
	    <span title="<?php echo $this->translate(array('%s comment', '%s comments', $subject->comment_count), $this->locale()->toNumber($subject->comment_count))?>"><i class="fa fa-eye "></i><?php echo $subject->comment_count; ?></span>
	  <?php endif;?>
	</div>
 </div>
	</div>
</div>
<?php die; ?>