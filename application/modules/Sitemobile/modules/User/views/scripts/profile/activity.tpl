<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: profile.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php
echo $this->action('feed', 'widget', 'activity', array(
    'action_id' => $this->action_id,
    'show_comments' => (bool) $this->action_id,
    'show_likes' => (bool) $this->viewAllLikes,
));
return;
?>

<form class="activity">
  <div>
    <input type="text" value="<?php echo $this->translate('Post something...'); ?>" />
    <p>
      <?php
      echo $this->htmlLink(array('route' => 'group_general', 'action' => 'create'), $this->translate('Post'), array(
          'class' => 'buttonlink icon_activity_post'
      ))
      ?>
    </p>
  </div>
</form>

<br />
<br />

<!--
<?php
if ($this->showPost):
  echo $this->form->setAttrib("class", "global_form_box")->render($this);
endif;
?>
-->


<?php // See application/modules/activity/views/scripts/_activity*.tpl  ?>
<ul class='feed'>
<?php echo $this->activityLoop($this->activity) ?>
</ul>
