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
<div class="ui-collapsible ui-collapsible-inset ui-collapsible-collapsed" data-role="collapsible">
<?php   if( count($this->siteAdminComments) > 0 ) :?>
  <h3>
    <?php echo $this->translate('Site Administrators Comments') ; ?>
    <span id="comment_count_2"><?php echo ' ('. count($this->siteAdminComments) . ')'; ?></span>
    <input type="hidden" id="total_comment_2" value="<?php echo count($this->siteAdminComments); ?>"/>
  </h3>
  
  <?php 
    echo '<ul data-role="listview" data-inset="false">';  
      foreach($this->siteAdminComments as $comment):
        echo '<li>'.gmdate('M d,Y, g:i A',strtotime($comment->creation_date));
        echo '<p>'. $this->translate("%s", $this->viewMore($comment->comment, 120)) . '</p>';
        echo '</li>';
      endforeach;
  echo '</ul>';
  ?>
<?php endif; ?>
</div>
