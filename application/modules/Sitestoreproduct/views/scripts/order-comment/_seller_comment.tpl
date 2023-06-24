<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _seller_comment.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="sitestoreproduct_manage_account">
  <h3>
    <a href="javascript:void(0)" onclick="commentToggle('seller_comment')"><?php echo $this->translate('Seller Comments'); ?></a>
    <span id="comment_count_1"><?php echo ' ('. count($this->sellerComments) . ')'; ?></span>
    <input type="hidden" id="total_comment_1" value="<?php echo count($this->sellerComments); ?>"/>
  </h3>

  <?php if( !empty($this->page_owner) || !empty($this->page_admin) ): ?>
    <a class="buttonlink activity_icon_comment_sitestoreproduct_product mbot5" href="javascript:void(0)" onclick="commentBox(1)"><?php echo $this->translate('Write a comment') ?></a>
    <div id="order_comment_1" style="display: none">
      <form id="order_comment_form_1" name="order_comment_form_1" >
        <div class="mbot10"><textarea id="sitestoreproduct_order_comment_box_1" name="sitestoreproduct_order_comment_box_1"></textarea></div>
        <div class="mbot10"><input type="hidden" name="user_type" value="1" />
        <input type="checkbox" id="notify_buyer" name="notify_buyer" /><?php echo empty($this->orderObj->buyer_id) ? $this->translate('Email to buyer') : $this->translate('Notify, email & show to buyer'); ?></div>
        <div class='buttons mbot10'>
          <button id="form_submit_1" type='submit' name="continue"><?php echo $this->translate("Post Comment") ?></button> or <a href="javascript:void(0)" onclick="commentBox(1)"><?php echo $this->translate("Cancel") ?></a>
          <div id="comment_loading_image_1" class="mleft5" style="display: inline-block;"></div>
        </div>
      </form>
    </div>
    <?php endif; ?>
    
    <?php echo '<div id="seller_comment" class="sitestoreproduct_dashboard_comments"><ul>';
    if( count($this->sellerComments) > 0 ) :
      foreach($this->sellerComments as $comment):
        echo '<li class="mbot10"><div class="seaocore_txt_light mbot5">'.gmdate('M d,Y, g:i A',strtotime($comment->creation_date)) . '</div>';
        echo '<p class="pleft10">'. $this->translate("%s", $this->viewMore($comment->comment, 120)) . '</p>';
        echo '</li>';
      endforeach;
      echo '</ul></div>';
    else :
      echo '<div id="tip_message_1" class="tip"><span>'.$this->translate("There are no comments.").'</span></div>';
    endif;
    echo '</ul></div>';
  ?>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    <?php if( empty($this->page_owner) && empty($this->page_admin) ): ?>
    commentToggle('seller_comment');
    <?php endif; ?>
  });
</script>