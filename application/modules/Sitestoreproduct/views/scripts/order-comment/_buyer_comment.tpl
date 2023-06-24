<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _buyer_comment.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div class="sitestoreproduct_manage_account">
  <h3>
    <a href="javascript:void(0)" onclick="commentToggle('buyer_comment')"><?php echo $this->translate('Buyer Comments'); ?></a>
    <span id="comment_count_0"><?php echo ' ('. count($this->buyerComments) . ')'; ?></span>
    <input type="hidden" id="total_comment_0" value="<?php echo count($this->buyerComments); ?>"/>
  </h3>

  <?php if( !empty($this->page_user) ): ?>
  	<a class="buttonlink activity_icon_comment_sitestoreproduct_product mbot5" href="javascript:void(0)" onclick="commentBox(0)"><?php echo $this->translate('Write a comment') ?></a>
    <div id="order_comment_0" style="display: none">
      <form id="order_comment_form_0" name="order_comment_form_0">
        <div class="mbot10"><textarea name="sitestoreproduct_order_comment_box_0" id="sitestoreproduct_order_comment_box_0"></textarea></div>
        <div class="mbot10"><input type="hidden" name="user_type" value="0" /></div>
        <div class='buttons mbot10'>
          <button id="form_submit_0" type='submit' name="continue" ><?php echo $this->translate("Post Comment") ?></button> or <a href="javascript:void(0)" onclick="commentBox(0)"><?php echo $this->translate("Cancel") ?></a>
          <div id="comment_loading_image_0" class="mleft5" style="display: inline-block;"></div>
        </div>
      </form>
    </div>
    <?php endif; ?>
    
    <?php echo '<div id="buyer_comment" class="sitestoreproduct_dashboard_comments"><ul>';
    if( count($this->buyerComments) > 0 ) :
      foreach($this->buyerComments as $comment):
        echo '<li class="mbot10"><div class="seaocore_txt_light mbot5">'.gmdate('M d,Y, g:i A',strtotime($comment->creation_date)) . '</div>';
        echo '<p class="pleft10">'. $this->translate("%s", $this->viewMore($comment->comment, 120)) . '</p>';
        echo '</li>';
      endforeach;
    else :
      echo '<div id="tip_message_0" class="tip"><span>'.$this->translate("There are no comments.").'</span></div>';
    endif;
    echo '</ul></div>';
  ?>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    <?php if( empty($this->page_user) ): ?>
      commentToggle('buyer_comment');
    <?php endif; ?>
  });
</script>