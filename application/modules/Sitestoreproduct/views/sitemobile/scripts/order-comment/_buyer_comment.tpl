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
<div class="ui-collapsible ui-collapsible-inset ui-collapsible-collapsed" data-role="collapsible">
<?php  if( count($this->buyerComments) > 0 ) :?>
  <h3>
    <?php echo $this->translate('Buyer Comments'); ?>
    <span id="comment_count_0"><?php echo ' ('. count($this->buyerComments) . ')'; ?></span>
    <input type="hidden" id="total_comment_0" value="<?php echo count($this->buyerComments); ?>"/>
  </h3>
    
    <?php echo '<ul data-role="listview" data-inset="false">';   
      foreach($this->buyerComments as $comment):
        echo '<li>'.gmdate('M d,Y, g:i A',strtotime($comment->creation_date));
        echo '<p>'. $this->translate("%s", $this->viewMore($comment->comment, 120)) . '</p>';
        echo '</li>';
      endforeach;
    echo '</ul>';
  ?>
<?php endif;?>
</div>