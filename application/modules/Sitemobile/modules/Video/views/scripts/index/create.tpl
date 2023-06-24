<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: create.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>
<script type="text/javascript">
  var tagsUrl = '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>';
//  var validationUrl = '<?php //echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'validation'), 'default', true) ?>';
  var validationErrorMessage = "<?php echo $this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", "<a class='ui-link' href='javascript://' onclick='sm4.core.Module.video.index.ignoreValidation();'>" . $this->translate("here") . "</a>"); ?>";
  var checkingUrlMessage = '<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...')) ?>';
</script>

<?php if (($this->current_count >= $this->quota) && !empty($this->quota)): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have already uploaded the maximum number of videos allowed.'); ?>
      <?php echo $this->translate('If you would like to upload a new video, please <a href="%1$s">delete</a> an old one first.', $this->url(array('action' => 'manage'), 'video_general')); ?>
    </span>
  </div>
  <br/>
<?php else: ?>
    <?php if($this->error):?>
    <div class="tip">
    <span>
      <?php echo $this->error?>      
    </span>
  </div>
<?php endif; ?>
  <?php echo $this->form->render($this); ?>
<?php endif; ?>

<script type="text/javascript">

  sm4.core.runonce.add(function() {
    sm4.core.Module.autoCompleter.attach("tags", '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {'singletextbox': true, 'limit':10, 'minLength': 1, 'showPhoto' : false, 'search' : 'text'}, 'toValues');  
  });
    
</script>
