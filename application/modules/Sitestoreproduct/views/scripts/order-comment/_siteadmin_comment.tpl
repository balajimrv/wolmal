<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _siteadmin_comment.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="sitestoreproduct_manage_account clr">
  <h3>
    <a href="javascript:void(0)" onclick="commentToggle('site_admin_comment')"><?php echo $this->translate('Site Administrators Comments') ; ?></a>
    <span id="comment_count_2"><?php echo ' ('. count($this->siteAdminComments) . ')'; ?></span>
    <input type="hidden" id="total_comment_2" value="<?php echo count($this->siteAdminComments); ?>"/>
  </h3>

  <?php if( !empty($this->admin_calling) ): ?>
  <a class="buttonlink activity_icon_comment_sitestoreproduct_product mbot5" href="javascript:void(0)" onclick="commentBox(2)"><?php echo $this->translate('Write a comment')?></a>
  <div id="order_comment_2" style="display: none">
    <form id="order_comment_form_2" name="order_comment_form_2">
      <div class="mbot10"><textarea id="sitestoreproduct_order_comment_box_2" name="sitestoreproduct_order_comment_box_2"></textarea></div>
      <div class="mbot5"><input type="hidden" name="user_type" value="2" />
      <input type="checkbox" id="notify_buyer" name="notify_buyer" /><?php echo empty($this->orderObj->buyer_id) ? $this->translate('Email to buyer') : $this->translate('Notify, email & show to buyer'); ?></div>
  <?php if( !empty($this->is_siteadmin_owner) ) : ?>    
        <input type="hidden" id="is_siteadmin_owner" name="is_siteadmin_owner" value="1" />
  <?php endif; ?>    
      <div class="mbot10">
        <input type="checkbox" id="notify_store_admin" name="notify_store_admin" /><?php echo $this->translate("Notify, email & show to seller") ?>
      </div>
      
      <div class='buttons mbot10'>
        <button id="form_submit_2" type='submit' name="continue" ><?php echo $this->translate("Post Comment") ?></button> or <a href="javascript:void(0)" onclick="commentBox(2)"><?php echo $this->translate("Cancel") ?></a>
        <div id="comment_loading_image_2" class="mleft5" style="display: inline-block;"></div>
      </div>
    </form>
  </div>
  <?php endif; ?>
  
  <?php 
    echo '<div id="site_admin_comment" class="sitestoreproduct_dashboard_comments"><ul>';
    if( count($this->siteAdminComments) > 0 ) :
      foreach($this->siteAdminComments as $comment):
        echo '<li class="mbot10"><div class="seaocore_txt_light mbot5">'.gmdate('M d,Y, g:i A',strtotime($comment->creation_date)) . '</div>';
        echo '<p class="pleft10">'. $this->translate("%s", $this->viewMore($comment->comment, 120)) . '</p>';
        echo '</li>';
      endforeach;
    else :
      echo '<div id="tip_message_2" class="tip"><span>'.$this->translate("There are no comments.").'</span></div>';
    endif;
  echo '</ul></div>';
  ?>
</div>
  
<script type="text/javascript">
  en4.core.runonce.add(function() {
    <?php if( empty($this->admin_calling) ): ?>
    commentToggle('site_admin_comment');
    <?php endif; ?>
  });
</script>
